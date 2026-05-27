<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\Event;
 
class ParticipantController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST PARTICIPANTS
    |--------------------------------------------------------------------------
    */
 
    public function index(Request $request)
    {
        $events = Event::orderBy('name', 'asc')->get();
        $eventId = $request->query('event_id');
 
        // Build query
        $query = Registration::with(['user', 'event', 'ticketCategory'])
            ->orderBy('created_at', 'desc');
 
        if ($eventId) {
            $query->where('event_id', $eventId);
        }
 
        $registrations = $query->get();
 
        // Calculate statistics
        $totalRegistrations = $registrations->count();
        $totalAttended = $registrations->where('is_checked_in', 1)->count();
        $attendanceRate = $totalRegistrations > 0 
            ? round(($totalAttended / $totalRegistrations) * 100, 1) 
            : 0;
 
        return view('admin.participants.index', compact(
            'registrations',
            'events',
            'eventId',
            'totalRegistrations',
            'totalAttended',
            'attendanceRate'
        ));
    }
 
    /*
    |--------------------------------------------------------------------------
    | TOGGLE MANUAL CHECK-IN
    |--------------------------------------------------------------------------
    */
 
    public function toggleCheckIn(Registration $registration)
    {
        if ($registration->is_checked_in) {
            $registration->is_checked_in = 0;
            $registration->checked_in_at = null;
            $message = 'Presensi peserta ' . $registration->user->name . ' berhasil dibatalkan! ⏳';
        } else {
            $registration->is_checked_in = 1;
            $registration->checked_in_at = now();
            $message = 'Check-in peserta ' . $registration->user->name . ' berhasil! 🏆';
        }
 
        $registration->save();
 
        return redirect()
            ->back()
            ->with('success', $message);
    }
}
