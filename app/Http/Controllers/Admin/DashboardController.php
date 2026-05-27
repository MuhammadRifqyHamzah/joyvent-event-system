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
            // 1. Total Event & Growth
            $totalEvents = Event::count();
            $eventsThisMonth = Event::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            $eventsLastMonth = Event::whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count();
            $eventGrowth = $this->calculateGrowth($eventsThisMonth, $eventsLastMonth);
            $eventSubtext = $eventGrowth . ' Bulan ini';
 
            // 2. Total Peserta, Growth & Check-In - Optimized to a single raw query
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
 
            // 4. Total Certificates & Pending
            $totalCertificates = Certificate::count();
            $pendingCertificates = Registration::where('is_checked_in', true)
                ->whereHas('event', function ($q) {
                    $q->where('has_certificate', true);
                })
                ->whereDoesntHave('certificate')
                ->count();
            $certificateSubtext = '⏱ ' . $pendingCertificates . ' Pending';
 
            // 5. Chart Pertumbuhan Peserta (Last 7 Days) - Optimized to 1 Single Grouped Query
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
 
            // 7. Event Terbaru (Top 5)
            $latestEvents = Event::withCount('registrations')
                ->latest()
                ->take(5)
                ->get();
 
            // 8. Aktivitas Terbaru (Unified activity feed)
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
 
            return compact(
                'totalEvents',
                'eventSubtext',
                'totalParticipants',
                'participantSubtext',
                'totalCheckIn',
                'attendanceSubtext',
                'attendancePercentage',
                'totalCertificates',
                'certificateSubtext',
                'pendingCertificates',
                'chartData',
                'checkedInCount',
                'notCheckedInCount',
                'checkedInPercentage',
                'latestEvents',
                'sortedActivities'
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
