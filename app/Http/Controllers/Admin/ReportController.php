<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Show the reports main page with finished events.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $nowString = now()->toDateTimeString();

        // Get only events where status is finished dynamically based on date & time
        $finishedEvents = Event::where('is_configured', 1)
            ->finished()
            ->orderBy('start_date', 'desc')
            ->get();

        return view('admin.reports.index', compact('finishedEvents'));
    }

    /**
     * Fetch reports data for a specific finished event.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Event $event)
    {
        // Double check event is finished
        $now = now();
        $endDate = Carbon::parse($event->end_date . ' ' . $event->end_time);
        
        if ($now->lessThanOrEqualTo($endDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan hanya tersedia untuk event yang sudah selesai.'
            ], 400);
        }

        // Fetch registrations with user and category
        $registrations = Registration::where('event_id', $event->id)
            ->with(['user', 'ticketCategory'])
            ->get();

        // 1. Participant Summary calculations
        $totalRegistrations = $registrations->count();
        $totalCheckIn = $registrations->where('is_checked_in', 1)->count();
        $totalNotCheckIn = $totalRegistrations - $totalCheckIn;
        $attendancePercentage = $totalRegistrations > 0 ? ($totalCheckIn / $totalRegistrations) * 100 : 0;

        // 2. Ticket Summary calculations
        $categories = $event->ticketCategories;
        $ticketCategoriesData = $categories->map(function ($cat) use ($registrations) {
            $sold = $registrations->where('ticket_category_id', $cat->id)
                                  ->where('status', 'confirmed')
                                  ->count();
            return [
                'name' => $cat->name,
                'sold' => $sold,
            ];
        });
        $totalTicketsSold = $registrations->where('status', 'confirmed')->count();

        // 3. Revenue Summary calculations
        $totalRevenue = (float)$registrations->where('status', 'confirmed')->sum(function ($reg) {
            return $reg->ticketCategory->price ?? 0;
        });
        $averageTicketPrice = $totalTicketsSold > 0 ? ($totalRevenue / $totalTicketsSold) : 0;

        // 4. Participant Report Table rows mapping
        $participants = $registrations->map(function ($reg) {
            return [
                'name' => $reg->user->name ?? 'Guest',
                'email' => $reg->user->email ?? '-',
                'ticket_category' => $reg->ticketCategory->name ?? '-',
                'is_checked_in' => $reg->is_checked_in ? 'Hadir' : 'Belum Hadir',
            ];
        });

        return response()->json([
            'success' => true,
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'category' => $event->category,
                'start_date' => Carbon::parse($event->start_date)->format('d M Y'),
                'location' => $event->location,
                'status' => 'Finished',
            ],
            'participant_summary' => [
                'total_registrations' => $totalRegistrations,
                'total_check_in' => $totalCheckIn,
                'total_not_check_in' => $totalNotCheckIn,
                'attendance_percentage' => round($attendancePercentage, 2),
            ],
            'ticket_summary' => [
                'categories' => $ticketCategoriesData,
                'total_tickets_sold' => $totalTicketsSold,
            ],
            'revenue_summary' => [
                'total_revenue' => $totalRevenue,
                'average_ticket_price' => round($averageTicketPrice, 2),
                'tickets_sold' => $totalTicketsSold,
            ],
            'participants' => $participants,
        ]);
    }
}
