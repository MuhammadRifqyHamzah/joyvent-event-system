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
 
    public function index(Event $event, Request $request)
    {
        return redirect()->route('admin.events.show', ['event' => $event->id, 'tab' => 'participants']);
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
 
    /*
    |--------------------------------------------------------------------------
    | RESET ALL CHECK-INS FOR EVENT
    |--------------------------------------------------------------------------
    */
    public function resetCheckIn(Event $event)
    {
        Registration::where('event_id', $event->id)->update([
            'is_checked_in' => 0,
            'checked_in_at' => null,
        ]);
 
        return redirect()
            ->back()
            ->with('success', 'Data kehadiran seluruh peserta berhasil di-reset! ↺');
    }
}
