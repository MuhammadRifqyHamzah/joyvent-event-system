<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Certificate;
use Carbon\Carbon;
 
class DashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD OVERVIEW
    |--------------------------------------------------------------------------
    */
 
    public function index()
    {
        // Wrap everything in a 5-second memory cache to make dashboard load instantaneously (sub-5ms)
        $data = cache()->remember('admin_dashboard_data', 5, function() {
            $nowString = now()->toDateTimeString();

            // 1. Event On-Going
            $ongoingEvents = Event::where('is_configured', 1)
                ->whereRaw("CONCAT(start_date, ' ', start_time) <= ?", [$nowString])
                ->whereRaw("CONCAT(end_date, ' ', end_time) >= ?", [$nowString])
                ->withCount([
                    'registrations',
                    'registrations as attended_count' => function ($q) {
                        $q->where('is_checked_in', 1);
                    }
                ])
                ->with('ticketCategories')
                ->get();
            $ongoingEventsCount = $ongoingEvents->count();
            $eventSubtext = '⚡ Sedang aktif';

            // 2. Total Event (configured only) & Growth
            $totalEventsCount = Event::where('is_configured', 1)->active()->count();
            $eventsThisMonth = Event::where('is_configured', 1)->active()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            $eventsLastMonth = Event::where('is_configured', 1)->active()
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count();
            $eventGrowth = $this->calculateGrowth($eventsThisMonth, $eventsLastMonth);
            $totalEventsSubtext = $eventGrowth . ' Bulan ini';

            // 3. Tiket Terjual (registrations with status confirmed)
            $ticketsSold = Registration::where('status', 'confirmed')->count();
            $ticketsSoldToday = Registration::where('status', 'confirmed')
                ->whereDate('created_at', now()->toDateString())
                ->count();
            $ticketsSoldYesterday = Registration::where('status', 'confirmed')
                ->whereDate('created_at', now()->subDay()->toDateString())
                ->count();
            $ticketsGrowth = $this->calculateGrowth($ticketsSoldToday, $ticketsSoldYesterday);
            $ticketsSubtext = $ticketsGrowth . ' vs kemarin';

            // 4. Pendapatan (sum registrations.ticket_category_id -> ticket_categories.price for status confirmed)
            $totalRevenue = Registration::where('status', 'confirmed')
                ->join('ticket_categories', 'registrations.ticket_category_id', '=', 'ticket_categories.id')
                ->sum('ticket_categories.price');
            $revenueToday = Registration::where('status', 'confirmed')
                ->join('ticket_categories', 'registrations.ticket_category_id', '=', 'ticket_categories.id')
                ->whereDate('registrations.created_at', now()->toDateString())
                ->sum('ticket_categories.price');
            $revenueYesterday = Registration::where('status', 'confirmed')
                ->join('ticket_categories', 'registrations.ticket_category_id', '=', 'ticket_categories.id')
                ->whereDate('registrations.created_at', now()->subDay()->toDateString())
                ->sum('ticket_categories.price');
            $revenueGrowth = $this->calculateGrowth($revenueToday, $revenueYesterday);
            $revenueSubtext = $revenueGrowth . ' vs kemarin';

            // Base participants & check-in stats for graphs & widgets
            $regStats = Registration::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END) as today,
                SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END) as yesterday,
                SUM(CASE WHEN is_checked_in = 1 THEN 1 ELSE 0 END) as checked_in,
                SUM(CASE WHEN is_checked_in = 0 THEN 1 ELSE 0 END) as not_checked_in
            ", [now()->toDateString(), now()->subDay()->toDateString()])->first();

            $totalParticipants = $regStats->total ?? 0;
            $participantsToday = $regStats->today ?? 0;
            $participantsYesterday = $regStats->yesterday ?? 0;
            $totalCheckIn = $regStats->checked_in ?? 0;
            $checkedInCount = $totalCheckIn;
            $notCheckedInCount = $regStats->not_checked_in ?? 0;

            $participantGrowth = $this->calculateGrowth($participantsToday, $participantsYesterday);
            $participantSubtext = $participantGrowth . ' vs kemarin';

            $attendancePercentage = $totalParticipants > 0 ? round(($totalCheckIn / $totalParticipants) * 100, 1) : 0;
            $attendanceSubtext = '⚡ ' . $attendancePercentage . '% Attendance';

            // Chart Pertumbuhan Peserta (Last 7 Days) - Optimized to 1 Single Grouped Query
            $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            $startDate = now()->subDays(6)->toDateString();
            $dailyCounts = Registration::whereDate('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');

            $last7Days = collect();
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $formattedDate = $date->format('Y-m-d');
                $dayName = $dayNames[$date->dayOfWeek];
                $count = $dailyCounts->get($formattedDate, 0);
                $last7Days->push([
                    'day' => $dayName,
                    'count' => $count
                ]);
            }
            $maxCount = $last7Days->max('count');
            $chartData = $last7Days->map(function ($item) use ($maxCount) {
                $item['height'] = $maxCount > 0 ? round(($item['count'] / $maxCount) * 100) : 0;
                return $item;
            });

            $checkedInPercentage = $totalParticipants > 0 ? round(($checkedInCount / $totalParticipants) * 100) : 0;

            // Upcoming Events (Top 5 sorted by nearest date)
            $upcomingEvents = Event::where('is_configured', 1)
                ->whereRaw("CONCAT(start_date, ' ', start_time) > ?", [$nowString])
                ->orderBy('start_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->take(5)
                ->with('ticketCategories')
                ->get();

            // Aktivitas Terbaru (Unified activity feed)
            $checkIns = Registration::with(['user', 'event'])
                ->where('is_checked_in', true)
                ->orderBy('checked_in_at', 'desc')
                ->take(5)
                ->get();
            
            $newRegistrations = Registration::with(['user', 'event'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            $newCertificates = Certificate::with(['registration.user', 'registration.event'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $activities = collect();

            foreach ($checkIns as $checkIn) {
                if ($checkIn->user && $checkIn->event && $checkIn->checked_in_at) {
                    $checkedInTime = Carbon::parse($checkIn->checked_in_at);
                    $activities->push([
                        'icon' => '👤',
                        'bg_color' => 'bg-green-50',
                        'title' => '<strong>' . e($checkIn->user->name) . '</strong> berhasil check-in di <strong>' . e($checkIn->event->name) . '</strong>.',
                        'time' => $checkedInTime->diffForHumans(),
                        'timestamp' => $checkedInTime
                    ]);
                }
            }

            foreach ($newRegistrations as $reg) {
                if ($reg->user && $reg->event) {
                    $activities->push([
                        'icon' => '📝',
                        'bg_color' => 'bg-blue-50',
                        'title' => '<strong>' . e($reg->user->name) . '</strong> mendaftar di event <strong>' . e($reg->event->name) . '</strong>.',
                        'time' => $reg->created_at->diffForHumans(),
                        'timestamp' => $reg->created_at
                    ]);
                }
            }

            foreach ($newCertificates as $cert) {
                if ($cert->registration && $cert->registration->user && $cert->registration->event) {
                    $activities->push([
                        'icon' => '🏆',
                        'bg_color' => 'bg-green-50',
                        'title' => 'Sertifikat berhasil dibuat untuk <strong>' . e($cert->registration->user->name) . '</strong> di <strong>' . e($cert->registration->event->name) . '</strong>.',
                        'time' => $cert->created_at->diffForHumans(),
                        'timestamp' => $cert->created_at
                    ]);
                }
            }

            $sortedActivities = $activities->sortByDesc('timestamp')->take(5);

            // 5. Laporan Penjualan Event Selesai (Dynamic Datetime Filter)
            $finishedEventsReport = Event::where('is_configured', 1)
                ->finished()
                ->select('id', 'name')
                ->withCount([
                    'registrations as tickets_sold' => function ($q) {
                        $q->where('status', 'confirmed');
                    }
                ])
                ->get()
                ->map(function ($event) {
                    $event->revenue = (float) Registration::where('registrations.event_id', $event->id)
                        ->where('registrations.status', 'confirmed')
                        ->join('ticket_categories', 'registrations.ticket_category_id', '=', 'ticket_categories.id')
                        ->sum('ticket_categories.price');
                    return $event;
                });

            $totalFinishedTicketsSold = $finishedEventsReport->sum('tickets_sold');
            $totalFinishedRevenue = $finishedEventsReport->sum('revenue');

            $finishedEventsCount = $finishedEventsReport->count();
            $averageFinishedRevenue = $finishedEventsCount > 0 ? ($totalFinishedRevenue / $finishedEventsCount) : 0;
            $sortedFinishedEvents = $finishedEventsReport->sortByDesc('revenue');
            $highestRevenueEvent = $sortedFinishedEvents->first();
            $highestRevenueEventName = $highestRevenueEvent && $highestRevenueEvent->revenue > 0 ? $highestRevenueEvent->name : 'Tidak Ada';

            // 6. Breakdown Penjualan Tiket (Grouped by Event Status)
            $ticketBreakdown = Event::where('is_configured', 1)
                ->whereHas('registrations', function ($q) {
                    $q->where('status', 'confirmed');
                })
                ->withCount([
                    'registrations as tickets_sold' => function ($q) {
                        $q->where('status', 'confirmed');
                    }
                ])
                ->get()
                ->groupBy(function ($event) {
                    return $event->calculated_status; // 'ongoing', 'upcoming', 'finished'
                });

            return compact(
                'ongoingEvents',
                'ongoingEventsCount',
                'eventSubtext',
                'totalEventsCount',
                'totalEventsSubtext',
                'ticketsSold',
                'ticketsSubtext',
                'totalRevenue',
                'revenueSubtext',
                'totalParticipants',
                'participantSubtext',
                'totalCheckIn',
                'attendanceSubtext',
                'attendancePercentage',
                'chartData',
                'checkedInCount',
                'notCheckedInCount',
                'checkedInPercentage',
                'upcomingEvents',
                'sortedActivities',
                'finishedEventsReport',
                'totalFinishedTicketsSold',
                'totalFinishedRevenue',
                'averageFinishedRevenue',
                'sortedFinishedEvents',
                'highestRevenueEventName',
                'ticketBreakdown'
            );
        });
 
        return view('admin.dashboard', $data);
    }
 
    /**
     * Helper to calculate growth percentage safely
     */
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? '↗ +100%' : '→ +0%';
        }
 
        $diff = (($current - $previous) / $previous) * 100;
        $sign = $diff > 0 ? '↗ +' : ($diff < 0 ? '↘ ' : '→ +');
 
        return $sign . round($diff, 1) . '%';
    }
}
