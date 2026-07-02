<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | EVENTS PAGE
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = Event::where('is_configured', 1)->latest();

        if ($status) {
            $nowString = now()->toDateTimeString();
            if ($status === 'open' || $status === 'ongoing') {
                $query->whereRaw("CONCAT(start_date, ' ', start_time) <= ?", [$nowString])
                      ->whereRaw("CONCAT(end_date, ' ', end_time) >= ?", [$nowString]);
            } elseif ($status === 'upcoming') {
                $query->whereRaw("CONCAT(start_date, ' ', start_time) > ?", [$nowString]);
            } elseif ($status === 'finished') {
                $query->whereRaw("CONCAT(end_date, ' ', end_time) < ?", [$nowString]);
            }
        }

        $events = $query->withCount([
            'registrations',
            'registrations as attended_count' => function ($q) {
                $q->where('is_checked_in', 1);
            }
        ])
        ->with('ticketCategories')
        ->get();

        return view('admin.events.index', compact('events'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE EVENT PAGE
    |--------------------------------------------------------------------------
    |
    */

    public function create()
    {
        return view('admin.events.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE EVENT
    |--------------------------------------------------------------------------
    |
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'name' => 'required|max:255',
            'description' => 'required',
            'category' => 'required|in:Entertainment,Education,Sports,Business,Community',

            'location' => 'required',
            'google_maps_url' => [
                'nullable',
                'url',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $pattern = '/^(https?:\/\/)?(www\.)?(maps\.google\.com|google\.com\/maps|maps\.app\.goo\.gl)/i';
                    if (!preg_match($pattern, $value)) {
                        $fail('Format URL harus berupa tautan resmi Google Maps.');
                    }
                }
            ],

            'start_date' => 'required|date',
            'end_date' => 'required|date',

            'start_time' => 'required',
            'end_time' => 'required',

            'capacity' => 'required|integer',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',

        ]);

        /*
        |--------------------------------------------------------------------------
        | CREATE EVENT
        |--------------------------------------------------------------------------
        */

        $event = Event::create([

            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,

            'location' => $request->location,
            'google_maps_url' => $request->google_maps_url,

            'start_date' => $request->start_date,
            'end_date' => $request->end_date,

            'start_time' => $request->start_time,
            'end_time' => $request->end_time,

            'capacity' => $request->capacity,

            'has_certificate' => $request->has_certificate ? 1 : 0,
            'has_seat_layout' => $request->has_seat_layout ? 1 : 0,
            'has_lucky_draw' => $request->has_lucky_draw ? 1 : 0,

        ]);

        // Handle Banner Upload
        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $bannerDir = public_path('storage/banners');
            if (!\Illuminate\Support\Facades\File::exists($bannerDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($bannerDir, 0755, true);
            }

            $filename = 'banner_image_' . $event->id . '.' . $file->getClientOriginalExtension();
            $file->move($bannerDir, $filename);
        }

        /*
        |--------------------------------------------------------------------------
        | REDIRECT TO TICKET CATEGORY
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.tickets.index', $event->id)
            ->with('success', 'Event berhasil dibuat! 🎉');
    }

    /*
    |--------------------------------------------------------------------------
    | EVENT DETAIL PAGE
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | EVENT DETAIL PAGE (Central Router)
    |--------------------------------------------------------------------------
    */

    public function show(Event $event)
    {
        if (!$event->is_configured) {
            return redirect()
                ->route('admin.tickets.index', $event->id)
                ->with('info', 'Harap selesaikan pembuatan event terlebih dahulu.');
        }

        // Determine Status and redirect
        $now = now();
        $startDate = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time);
        $endDate = \Carbon\Carbon::parse($event->end_date . ' ' . $event->end_time);

        $queryParams = request()->query();

        if ($now->greaterThan($endDate)) {
            return redirect()->route('admin.events.finished', array_merge(['event' => $event->id], $queryParams));
        } elseif ($now->lessThan($startDate)) {
            return redirect()->route('admin.events.upcoming', array_merge(['event' => $event->id], $queryParams));
        } else {
            return redirect()->route('admin.events.ongoing', array_merge(['event' => $event->id], $queryParams));
        }
    }

    /**
     * Helper to compute base event details & statistics
     */
    private function getEventBaseData(Event $event)
    {
        // Eager load relations
        $event->load([
            'ticketCategories',
            'registrations' => function ($q) {
                $q->with(['user', 'ticketCategory', 'verifiedBy', 'rejectedBy'])->latest();
            }
        ]);

        // Calculate statistics
        $capacity = $event->ticketCategories->sum('quota');
        $ticketsSold = $event->registrations->count();
        $remainingSeats = $ticketsSold === 0 ? $capacity : max(0, $capacity - $ticketsSold);

        // Sum price of confirmed sold tickets only
        $totalRevenue = $event->registrations->where('status', 'confirmed')->sum(function ($registration) {
            return $registration->ticketCategory->price ?? 0;
        });

        // Ticket Sales progress percentage
        $soldPercentage = $capacity > 0 ? round(($ticketsSold / $capacity) * 100, 1) : 0;

        // Group and count registrations by ticket category
        $soldCounts = \App\Models\Registration::where('event_id', $event->id)
            ->groupBy('ticket_category_id')
            ->selectRaw('ticket_category_id, COUNT(*) as count')
            ->pluck('count', 'ticket_category_id');

        return compact(
            'event',
            'capacity',
            'ticketsSold',
            'remainingSeats',
            'totalRevenue',
            'soldPercentage',
            'soldCounts'
        );
    }

    /**
     * Helper to compute full event details, statistics, lists, and features
     */
    private function getEventDetailsData(Event $event)
    {
        $baseData = $this->getEventBaseData($event);

        // Attendance stats
        $totalParticipants = $baseData['ticketsSold'];
        $attended_count = $event->registrations->where('is_checked_in', 1)->count();
        $notAttendedCount = max(0, $totalParticipants - $attended_count);
        $attendancePercentage = $totalParticipants > 0 ? round(($attended_count / $totalParticipants) * 100, 1) : 0;

        // Seat Layout (if active)
        $groupedSeats = collect();
        $seatBookings = collect();
        if ($event->has_seat_layout) {
            $seats = \App\Models\Seat::where('event_id', $event->id)
                ->orderBy('row', 'asc')
                ->orderBy('column', 'asc')
                ->get();

            $groupedSeats = $seats->groupBy(function($seat) {
                return is_numeric($seat->row) && $seat->row >= 1 && $seat->row <= 26 
                    ? chr($seat->row + 64) 
                    : $seat->row;
            });

            $seatBookings = \App\Models\Registration::with('user')
                ->where('event_id', $event->id)
                ->whereNotNull('seat_number')
                ->get()
                ->keyBy('seat_number');
        }

        // Lucky Draw (if active)
        $winners = collect();
        $candidates = collect();
        $eventPrizes = collect();
        if ($event->has_lucky_draw) {
            $winners = \App\Models\LuckyDrawWinner::with(['registration.user', 'eventPrize'])
                ->where('event_id', $event->id)
                ->orderBy('won_at', 'desc')
                ->get();

            $winnerRegistrationIds = $winners->pluck('registration_id');
            $winnerUserIds = $winners->pluck('registration.user_id')->filter()->toArray();

            $candidates = \App\Models\Registration::with('user')
                ->where('event_id', $event->id)
                ->where('is_checked_in', 1)
                ->where('registration_status', 'active')
                ->where('payment_status', 'paid')
                ->where('status', '!=', 'cancelled')
                ->whereNotIn('id', $winnerRegistrationIds)
                ->whereNotIn('user_id', $winnerUserIds)
                ->where(function ($q) {
                    $q->whereDoesntHave('refund')
                        ->orWhereHas('refund', function ($qr) {
                            $qr->whereNotIn('status', ['pending', 'approved']);
                        });
                })
                ->get();

            $eventPrizes = $event->eventPrizes;
        }

        // Certificate (if active)
        $certificates = collect();
        $certCandidates = collect();
        $templateUrl = null;
        if ($event->has_certificate) {
            $certificates = \App\Models\Certificate::with(['registration.user', 'registration.ticketCategory'])
                ->whereHas('registration', function($query) use ($event) {
                    $query->where('event_id', $event->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $issuedRegistrationIds = \App\Models\Certificate::whereNotNull('registration_id')->pluck('registration_id');
            $certCandidates = \App\Models\Registration::with('user')
                ->where('event_id', $event->id)
                ->where('is_checked_in', 1)
                ->whereNotIn('id', $issuedRegistrationIds)
                ->get();

            // Temporary debug to audit the candidates query and variables
            \Illuminate\Support\Facades\Log::debug('Certificate Generation Audit:', [
                'event_id' => $event->id,
                'has_certificate' => $event->has_certificate,
                'issuedRegistrationIds' => $issuedRegistrationIds->toArray(),
                'certCandidates_count' => $certCandidates->count(),
                'certCandidates_ids' => $certCandidates->pluck('id')->toArray(),
            ]);

            $templateDirectory = public_path('storage/certificates/templates');
            if (\Illuminate\Support\Facades\File::exists($templateDirectory)) {
                $files = \Illuminate\Support\Facades\File::files($templateDirectory);
                foreach ($files as $file) {
                    $filename = $file->getFilename();
                    if (str_starts_with($filename, 'template_' . $event->id . '.')) {
                        $templateUrl = asset('storage/certificates/templates/' . $filename);
                        break;
                    }
                }
            }
        }

        // Combined Recent Activity for this event
        $recentActivities = $this->getEventRecentActivity($event);

        // Refunds
        $refunds = \App\Models\Refund::whereHas('registration', function ($query) use ($event) {
            $query->where('event_id', $event->id);
        })->with(['registration.user', 'registration.ticketCategory'])->latest()->get();

        $pendingRefundsCount = $refunds->where('status', 'pending')->count();
        $approvedRefundsCount = $refunds->where('status', 'approved')->count();
        $rejectedRefundsCount = $refunds->where('status', 'rejected')->count();

        return array_merge($baseData, compact(
            'totalParticipants',
            'attended_count',
            'notAttendedCount',
            'attendancePercentage',
            'groupedSeats',
            'seatBookings',
            'winners',
            'candidates',
            'eventPrizes',
            'certificates',
            'certCandidates',
            'templateUrl',
            'recentActivities',
            'refunds',
            'pendingRefundsCount',
            'approvedRefundsCount',
            'rejectedRefundsCount'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPCOMING EVENT DETAIL
    |--------------------------------------------------------------------------
    */
    public function showUpcoming(Event $event)
    {
        // Redirect if status changed (e.g. now ongoing or finished)
        $now = now();
        $startDate = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time);
        $endDate = \Carbon\Carbon::parse($event->end_date . ' ' . $event->end_time);
        if ($now->greaterThan($endDate)) {
            return redirect()->route('admin.events.finished', array_merge(['event' => $event->id], request()->query()));
        } elseif ($now->greaterThanOrEqualTo($startDate)) {
            return redirect()->route('admin.events.ongoing', array_merge(['event' => $event->id], request()->query()));
        }

        $data = $this->getEventDetailsData($event);
        return view('admin.events.upcoming', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | ON-GOING EVENT DETAIL (Realtime Monitoring Dashboard)
    |--------------------------------------------------------------------------
    */
    public function showOngoing(Event $event)
    {
        // Redirect if status changed (e.g. not ongoing anymore)
        $now = now();
        $startDate = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time);
        $endDate = \Carbon\Carbon::parse($event->end_date . ' ' . $event->end_time);
        if ($now->lessThan($startDate)) {
            return redirect()->route('admin.events.upcoming', array_merge(['event' => $event->id], request()->query()));
        } elseif ($now->greaterThan($endDate)) {
            return redirect()->route('admin.events.finished', array_merge(['event' => $event->id], request()->query()));
        }

        $data = $this->getEventDetailsData($event);
        return view('admin.events.ongoing', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | FINISHED EVENT DETAIL
    |--------------------------------------------------------------------------
    */
    public function showFinished(Event $event)
    {
        // Redirect if status changed (e.g. not finished yet)
        $now = now();
        $startDate = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time);
        $endDate = \Carbon\Carbon::parse($event->end_date . ' ' . $event->end_time);
        if ($now->lessThan($startDate)) {
            return redirect()->route('admin.events.upcoming', array_merge(['event' => $event->id], request()->query()));
        } elseif ($now->lessThanOrEqualTo($endDate)) {
            return redirect()->route('admin.events.ongoing', array_merge(['event' => $event->id], request()->query()));
        }

        $data = $this->getEventDetailsData($event);
        return view('admin.events.finished', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | REALTIME POLL STATS API
    |--------------------------------------------------------------------------
    */
    public function ongoingStats(Event $event)
    {
        $event->load(['registrations.user', 'registrations.ticketCategory']);
        $totalParticipants = $event->registrations->count();
        $attended_count = $event->registrations->where('is_checked_in', 1)->count();
        $notAttendedCount = max(0, $totalParticipants - $attended_count);
        $attendancePercentage = $totalParticipants > 0 ? round(($attended_count / $totalParticipants) * 100, 1) : 0;

        // Base metrics
        $capacity = $event->ticketCategories->sum('quota');
        $remainingSeats = $totalParticipants === 0 ? $capacity : max(0, $capacity - $totalParticipants);

        // Winners count
        $winnerRegistrationIds = \App\Models\LuckyDrawWinner::where('event_id', $event->id)->pluck('registration_id');
        $candidatesCount = \App\Models\Registration::where('event_id', $event->id)
            ->where('is_checked_in', 1)
            ->whereNotIn('id', $winnerRegistrationIds)
            ->count();

        // Recent Activity
        $recentActivities = $this->getEventRecentActivity($event);

        // Winners List
        $winnersList = \App\Models\LuckyDrawWinner::with('registration.user')
            ->where('event_id', $event->id)
            ->orderBy('won_at', 'desc')
            ->get()
            ->map(function ($w) {
                return [
                    'id' => $w->id,
                    'name' => $w->registration->user->name ?? 'Guest',
                    'user_id' => $w->registration->user_id ?? null,
                    'prize_name' => $w->prize_name,
                    'destroy_url' => route('admin.lucky_draw.destroy', $w->id)
                ];
            });

        // Participants minimal list
        $participants = $event->registrations->map(function ($reg) {
            return [
                'id' => $reg->id,
                'name' => $reg->user->name ?? 'Guest',
                'email' => $reg->user->email ?? '-',
                'ticket_class' => $reg->ticketCategory->name ?? '-',
                'is_checked_in' => $reg->is_checked_in,
                'checked_in_at' => $reg->checked_in_at ? \Carbon\Carbon::parse($reg->checked_in_at)->format('d M Y, H:i') : null,
                'toggle_url' => route('admin.participants.check_in', $reg->id)
            ];
        });

        // Certificate stats if active
        $pendingCertificates = 0;
        if ($event->has_certificate) {
            $pendingCertificates = \App\Models\Registration::where('event_id', $event->id)
                ->where('is_checked_in', true)
                ->whereDoesntHave('certificate')
                ->count();
        }

        // Refunds stats
        $refunds = \App\Models\Refund::whereHas('registration', function ($query) use ($event) {
            $query->where('event_id', $event->id);
        })->with(['registration.user', 'registration.ticketCategory'])->latest()->get();

        $pendingRefunds = $refunds->where('status', 'pending')->count();
        $approvedRefunds = $refunds->where('status', 'approved')->count();
        $rejectedRefunds = $refunds->where('status', 'rejected')->count();

        $refundsList = $refunds->map(function ($refund) {
            return [
                'id' => $refund->id,
                'participant_name' => $refund->registration->user->name ?? 'Guest',
                'participant_email' => $refund->registration->user->email ?? '-',
                'ticket_type' => $refund->registration->ticketCategory->name ?? '-',
                'ticket_price' => $refund->registration->ticketCategory->price ?? 0,
                'reason' => $refund->reason,
                'additional_notes' => $refund->additional_notes ?? '-',
                'request_date' => $refund->created_at->format('d M Y, H:i'),
                'purchase_date' => $refund->registration->created_at->format('d M Y, H:i'),
                'status' => $refund->status,
                'approve_url' => route('admin.refunds.approve', $refund->id),
                'reject_url' => route('admin.refunds.reject', $refund->id),
            ];
        });

        return response()->json([
            'success' => true,
            'ticketsSold' => $totalParticipants,
            'capacity' => $capacity,
            'remainingSeats' => $remainingSeats,
            'attended_count' => $attended_count,
            'notAttendedCount' => $notAttendedCount,
            'attendancePercentage' => $attendancePercentage,
            'candidatesCount' => $candidatesCount,
            'pendingCertificates' => $pendingCertificates,
            'recentActivities' => $recentActivities,
            'winners' => $winnersList,
            'participants' => $participants,
            'pendingRefundsCount' => $pendingRefunds,
            'approvedRefundsCount' => $approvedRefunds,
            'rejectedRefundsCount' => $rejectedRefunds,
            'refunds' => $refundsList
        ]);
    }

    public function checkInQr(Request $request, Event $event)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $registration = \App\Models\Registration::with('refund')
            ->where('event_id', $event->id)
            ->where('qr_code', $request->qr_code)
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak terdaftar untuk event ini.'
            ], 404);
        }

        // Production Hardening Validation
        if ($registration->payment_status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Check-in gagal: tiket belum dibayar.'
            ], 400);
        }

        if ($registration->registration_status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Check-in gagal: tiket sudah tidak aktif.'
            ], 400);
        }

        if ($registration->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Check-in gagal: tiket telah dibatalkan.'
            ], 400);
        }

        if (
            $registration->refund &&
            $registration->refund->status === 'pending'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Check-in gagal: tiket sedang dalam proses refund.'
            ], 400);
        }

        if ($registration->is_checked_in) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta ' . $registration->user->name . ' sudah check-in sebelumnya.'
            ], 400);
        }

        $registration->update([
            'is_checked_in' => true,
            'checked_in_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in ' . $registration->user->name . ' berhasil! 🏆',
            'registration' => [
                'id' => $registration->id,
                'name' => $registration->user->name ?? 'Guest',
                'email' => $registration->user->email ?? '-',
            ]
        ]);
    }

    /**
     * Helper to get sorted combined recent activities for a specific event
     */
    private function getEventRecentActivity(Event $event)
    {
        $checkIns = \App\Models\Registration::with(['user'])
            ->where('event_id', $event->id)
            ->where('is_checked_in', true)
            ->orderBy('checked_in_at', 'desc')
            ->take(5)
            ->get();

        $newRegistrations = \App\Models\Registration::with(['user'])
            ->where('event_id', $event->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $newCertificates = \App\Models\Certificate::with(['registration.user'])
            ->whereHas('registration', function ($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $activities = collect();

        foreach ($checkIns as $checkIn) {
            if ($checkIn->user && $checkIn->checked_in_at) {
                $checkedInTime = \Carbon\Carbon::parse($checkIn->checked_in_at);
                $activities->push([
                    'icon' => '👤',
                    'bg_color' => 'bg-green-50',
                    'text_color' => 'text-green-600',
                    'title' => '<strong>' . e($checkIn->user->name) . '</strong> berhasil check-in.',
                    'time' => $checkedInTime->diffForHumans(),
                    'timestamp' => $checkedInTime->timestamp
                ]);
            }
        }

        foreach ($newRegistrations as $reg) {
            if ($reg->user) {
                $activities->push([
                    'icon' => '📝',
                    'bg_color' => 'bg-blue-50',
                    'text_color' => 'text-blue-600',
                    'title' => '<strong>' . e($reg->user->name) . '</strong> mendaftar di event.',
                    'time' => $reg->created_at->diffForHumans(),
                    'timestamp' => $reg->created_at->timestamp
                ]);
            }
        }

        foreach ($newCertificates as $cert) {
            if ($cert->registration && $cert->registration->user) {
                $activities->push([
                    'icon' => '🏆',
                    'bg_color' => 'bg-green-50',
                    'text_color' => 'text-green-600',
                    'title' => 'Sertifikat berhasil dibuat untuk <strong>' . e($cert->registration->user->name) . '</strong>.',
                    'time' => $cert->created_at->diffForHumans(),
                    'timestamp' => $cert->created_at->timestamp
                ]);
            }
        }

        return $activities->sortByDesc('timestamp')->take(5)->values()->toArray();
    }
 
    /*
    |--------------------------------------------------------------------------
    | EDIT EVENT PAGE
    |--------------------------------------------------------------------------
    */
 
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }
 
    /*
    |--------------------------------------------------------------------------
    | UPDATE EVENT
    |--------------------------------------------------------------------------
    */
 
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'category' => 'required|in:Entertainment,Education,Sports,Business,Community',
            'location' => 'required',
            'google_maps_url' => [
                'nullable',
                'url',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $pattern = '/^(https?:\/\/)?(www\.)?(maps\.google\.com|google\.com\/maps|maps\.app\.goo\.gl)/i';
                    if (!preg_match($pattern, $value)) {
                        $fail('Format URL harus berupa tautan resmi Google Maps.');
                    }
                }
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'capacity' => 'required|integer',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
        ]);
 
        $event->update([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'location' => $request->location,
            'google_maps_url' => $request->google_maps_url,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'capacity' => $request->capacity,
            'has_certificate' => $request->has_certificate ? 1 : 0,
            'has_seat_layout' => $request->has_seat_layout ? 1 : 0,
            'has_lucky_draw' => $request->has_lucky_draw ? 1 : 0,
        ]);

        // Handle Banner Upload
        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $bannerDir = public_path('storage/banners');
            if (!\Illuminate\Support\Facades\File::exists($bannerDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($bannerDir, 0755, true);
            }

            // Delete old banners
            $files = \Illuminate\Support\Facades\File::files($bannerDir);
            foreach ($files as $f) {
                if (str_starts_with($f->getFilename(), 'banner_image_' . $event->id . '.')) {
                    \Illuminate\Support\Facades\File::delete($f->getRealPath());
                }
            }

            $filename = 'banner_image_' . $event->id . '.' . $file->getClientOriginalExtension();
            $file->move($bannerDir, $filename);
        }
 
        return redirect()
            ->route('admin.events')
            ->with('success', 'Event berhasil diperbarui! 🎉');
    }
 
    /*
    |--------------------------------------------------------------------------
    | DELETE EVENT
    |--------------------------------------------------------------------------
    */
 
    public function destroy(Event $event)
    {
        // Delete banner image if exists
        $bannerDir = public_path('storage/banners');
        if (\Illuminate\Support\Facades\File::exists($bannerDir)) {
            $files = \Illuminate\Support\Facades\File::files($bannerDir);
            foreach ($files as $f) {
                if (str_starts_with($f->getFilename(), 'banner_image_' . $event->id . '.')) {
                    \Illuminate\Support\Facades\File::delete($f->getRealPath());
                }
            }
        }

        $event->delete();
 
        return redirect()
            ->route('admin.events')
            ->with('success', 'Event berhasil dihapus! 🗑');
    }

    /*
    |--------------------------------------------------------------------------
    | EVENT FEATURE SETUP
    |--------------------------------------------------------------------------
    */

    public function showFeatures(Event $event)
    {
        // Check if at least one feature is enabled
        $hasFeatures = $event->has_certificate || $event->has_seat_layout || $event->has_lucky_draw;
        if (!$hasFeatures) {
            $event->is_configured = true;
            $event->save();
            return redirect()
                ->route('admin.events')
                ->with('success', 'Event berhasil dibuat. 🎉');
        }

        $tickets = $event->ticketCategories;
        // Parse current layout if stored
        $seatLayouts = json_decode($event->seat_layout ?? '{}', true);

        return view('admin.events.features', compact('event', 'tickets', 'seatLayouts'));
    }

    public function storeFeatures(Request $request, Event $event)
    {
        $validationRules = [];

        if ($event->has_certificate) {
            $validationRules = array_merge($validationRules, [
                'certificate_title' => 'required|max:255',
                'organizer_name' => 'required|max:255',
                'certificate_template' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
                'signature_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
        }

        if ($event->has_seat_layout && $request->has('use_legacy_layout')) {
            $validationRules = array_merge($validationRules, [
                'seat_layout' => 'required|array',
                'seat_layout.*' => 'required|string',
            ]);
        }

        if ($event->has_lucky_draw) {
            $validationRules = array_merge($validationRules, [
                'prizes' => 'required|array|min:1',
                'prizes.*.id' => 'nullable|integer',
                'prizes.*.name' => 'required|string|max:255',
                'prizes.*.description' => 'nullable|string',
                'prizes.*.winner_count' => 'required|integer|min:1',
                'prizes.*.draw_order' => 'required|integer|min:0',
            ]);
        }

        $request->validate($validationRules);

        // 1. Certificate config
        if ($event->has_certificate) {
            $event->certificate_title = $request->certificate_title;
            $event->organizer_name = $request->organizer_name;

            // Handle Template
            if ($request->hasFile('certificate_template')) {
                $file = $request->file('certificate_template');
                $templateDir = public_path('storage/certificates/templates');
                if (!\Illuminate\Support\Facades\File::exists($templateDir)) {
                    \Illuminate\Support\Facades\File::makeDirectory($templateDir, 0755, true);
                }

                // Delete old templates
                $files = \Illuminate\Support\Facades\File::files($templateDir);
                foreach ($files as $f) {
                    if (str_starts_with($f->getFilename(), 'template_' . $event->id . '.')) {
                        \Illuminate\Support\Facades\File::delete($f->getRealPath());
                    }
                }

                $filename = 'template_' . $event->id . '.' . $file->getClientOriginalExtension();
                $file->move($templateDir, $filename);
                $event->certificate_template = $filename;
            }

            // Handle Signature
            if ($request->hasFile('signature_image')) {
                $file = $request->file('signature_image');
                $signatureDir = public_path('storage/certificates/signatures');
                if (!\Illuminate\Support\Facades\File::exists($signatureDir)) {
                    \Illuminate\Support\Facades\File::makeDirectory($signatureDir, 0755, true);
                }

                // Delete old signatures
                $files = \Illuminate\Support\Facades\File::files($signatureDir);
                foreach ($files as $f) {
                    if (str_starts_with($f->getFilename(), 'signature_' . $event->id . '.')) {
                        \Illuminate\Support\Facades\File::delete($f->getRealPath());
                    }
                }

                $filename = 'signature_' . $event->id . '.' . $file->getClientOriginalExtension();
                $file->move($signatureDir, $filename);
                $event->signature_image = $filename;
            }
        }

        $isLegacyUsed = $event->has_seat_layout && $request->has('use_legacy_layout');

        // 2. Seat Layout Config (Legacy Fallback)
        if ($isLegacyUsed) {
            $layouts = $request->input('seat_layout', []);
            $event->seat_layout = json_encode($layouts);

            // Re-generate seats table
            \App\Models\Seat::where('event_id', $event->id)->delete();

            foreach ($layouts as $ticketCategoryId => $layoutString) {
                $ranges = array_filter(array_map('trim', explode(',', $layoutString)));
                foreach ($ranges as $range) {
                    if (preg_match('/^([A-Z]+)(\d+)-([A-Z]*?)(\d+)$/i', $range, $matches)) {
                        $row = strtoupper($matches[1]);
                        $startCol = intval($matches[2]);
                        $endCol = intval($matches[4]);

                        $minCol = min($startCol, $endCol);
                        $maxCol = max($startCol, $endCol);

                        $rowInt = is_numeric($row) ? intval($row) : (ord(strtoupper($row)) - 64);
                        for ($col = $minCol; $col <= $maxCol; $col++) {
                            $seatNumber = $row . $col;
                            \App\Models\Seat::create([
                                'event_id' => $event->id,
                                'seat_number' => $seatNumber,
                                'row' => $rowInt,
                                'column' => $col,
                                'status' => 'available',
                            ]);
                        }
                    }
                }
            }
        }

        // 3. Lucky Draw Config
        if ($event->has_lucky_draw) {
            $prizesData = $request->input('prizes', []);
            if (!empty($prizesData)) {
                $event->prize_name = $prizesData[0]['name'];
                $event->prize_description = $prizesData[0]['description'] ?? '';
                $event->winner_count = $prizesData[0]['winner_count'];
            }

            // Sync prizes: delete those not in request, update/create others
            $keepPrizeIds = collect($prizesData)->pluck('id')->filter()->toArray();
            
            // Delete removed prizes
            $event->eventPrizes()->whereNotIn('id', $keepPrizeIds)->delete();

            // Create/Update prizes
            foreach ($prizesData as $prizeItem) {
                if (isset($prizeItem['id']) && $prizeItem['id']) {
                    $prize = $event->eventPrizes()->find($prizeItem['id']);
                    if ($prize) {
                        $prize->update([
                            'name' => $prizeItem['name'],
                            'description' => $prizeItem['description'] ?? null,
                            'winner_count' => $prizeItem['winner_count'],
                            'draw_order' => $prizeItem['draw_order'] ?? 0,
                            'status' => ($prize->drawn_count >= $prizeItem['winner_count']) ? 'completed' : ($prizeItem['status'] ?? $prize->status),
                        ]);
                    }
                } else {
                    $event->eventPrizes()->create([
                        'name' => $prizeItem['name'],
                        'description' => $prizeItem['description'] ?? null,
                        'winner_count' => $prizeItem['winner_count'],
                        'drawn_count' => 0,
                        'status' => $prizeItem['status'] ?? 'waiting',
                        'draw_order' => $prizeItem['draw_order'] ?? 0,
                    ]);
                }
            }
        }

        // Direct to Visual Builder if using visual flow
        if ($event->has_seat_layout && !$request->has('use_legacy_layout')) {
            $event->save();
            return redirect()
                ->route('admin.seats.builder', $event->id)
                ->with('info', 'Lanjutkan konfigurasi tata letak kursi secara visual. 🪑');
        }

        $event->is_configured = true;
        $event->save();

        return redirect()
            ->route('admin.events')
            ->with('success', 'Event successfully configured. 🎉');
    }

    public function finishSetup(Event $event)
    {
        $event->is_configured = true;
        $event->save();

        return redirect()
            ->route('admin.events')
            ->with('success', 'Event berhasil dibuat. 🎉');
    }
}