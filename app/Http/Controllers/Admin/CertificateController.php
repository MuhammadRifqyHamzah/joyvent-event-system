<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Certificate;
use Illuminate\Support\Facades\File;
 
class CertificateController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DISPLAY CERTIFICATES GENERATOR PANEL
    |--------------------------------------------------------------------------
    */
 
    public function index(Request $request)
    {
        // Only load events configured with has_certificate = 1
        $events = Event::where('has_certificate', 1)
            ->orderBy('name', 'asc')
            ->get();
            
        $eventId = $request->query('event_id');
        
        $event = null;
        $certificates = collect();
        $candidates = collect();
        $templateUrl = null;
 
        if ($eventId) {
            $event = Event::findOrFail($eventId);
            
            // Get already generated certificates
            $certificates = Certificate::with(['registration.user', 'registration.ticketCategory'])
                ->whereHas('registration', function($query) use ($eventId) {
                    $query->where('event_id', $eventId);
                })
                ->orderBy('created_at', 'desc')
                ->get();
                
            // Get checked-in participants who do NOT have a certificate issued yet
            $issuedRegistrationIds = Certificate::pluck('registration_id');
            $candidates = Registration::with('user')
                ->where('event_id', $eventId)
                ->where('is_checked_in', 1)
                ->whereNotIn('id', $issuedRegistrationIds)
                ->get();
 
            // Check if there is an uploaded template background image
            $templateDirectory = public_path('storage/certificates/templates');
            if (File::exists($templateDirectory)) {
                $files = File::files($templateDirectory);
                foreach ($files as $file) {
                    $filename = $file->getFilename();
                    if (str_starts_with($filename, 'template_' . $eventId . '.')) {
                        $templateUrl = asset('storage/certificates/templates/' . $filename);
                        break;
                    }
                }
            }
        }
 
        return view('admin.certificates.index', compact(
            'events',
            'eventId',
            'event',
            'certificates',
            'candidates',
            'templateUrl'
        ));
    }
 
    /*
    |--------------------------------------------------------------------------
    | AUTOMATED BULK GENERATOR WITH TEMPLATE UPLOADER
    |--------------------------------------------------------------------------
    */
 
    public function generate(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'template' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
        ]);
 
        $eventId = $request->event_id;
        $event = Event::findOrFail($eventId);
 
        // Handle Template Background upload if present
        if ($request->hasFile('template')) {
            $file = $request->file('template');
            
            // Create directory if it doesn't exist
            $templateDirectory = public_path('storage/certificates/templates');
            if (!File::exists($templateDirectory)) {
                File::makeDirectory($templateDirectory, 0755, true);
            }
 
            // Clean up any old template extensions first
            $files = File::files($templateDirectory);
            foreach ($files as $f) {
                if (str_starts_with($f->getFilename(), 'template_' . $eventId . '.')) {
                    File::delete($f->getRealPath());
                }
            }
 
            // Save new template file
            $extension = $file->getClientOriginalExtension();
            $filename = 'template_' . $eventId . '.' . $extension;
            $file->move($templateDirectory, $filename);
        }
 
        // Get all checked-in participants without certificates
        $issuedRegistrationIds = Certificate::pluck('registration_id');
        $candidates = Registration::where('event_id', $eventId)
            ->where('is_checked_in', 1)
            ->whereNotIn('id', $issuedRegistrationIds)
            ->get();
 
        if ($candidates->isEmpty()) {
            return redirect()
                ->back()
                ->with('success', '💡 Semua peserta yang hadir sudah memiliki sertifikat!');
        }
 
        $totalIssued = 0;
        foreach ($candidates as $candidate) {
            Certificate::create([
                'registration_id' => $candidate->id,
                'certificate_code' => 'JV-' . strtoupper($eventId . substr(uniqid(), -6)),
                'is_valid' => true,
            ]);
            $totalIssued++;
        }
 
        return redirect()
            ->back()
            ->with('success', "🎉 Berhasil menerbitkan {$totalIssued} sertifikat secara massal! 🚀");
    }
 
    /*
    |--------------------------------------------------------------------------
    | TOGGLE CERTIFICATE VALIDATION STATUS
    |--------------------------------------------------------------------------
    */
 
    public function toggleValid($id)
    {
        $certificate = Certificate::findOrFail($id);
        $certificate->is_valid = !$certificate->is_valid;
        $certificate->save();
 
        $statusMessage = $certificate->is_valid 
            ? "Sertifikat {$certificate->certificate_code} berhasil di-aktifkan! 🟢"
            : "Sertifikat {$certificate->certificate_code} berhasil di-nonaktifkan! 🔴";
 
        return redirect()
            ->back()
            ->with('success', $statusMessage);
    }
}
