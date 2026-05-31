<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use App\Models\Event;
use App\Models\Registration;
use App\Models\LuckyDrawWinner;
use App\Models\Certificate;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'source_key',
        'type',
        'title',
        'message',
        'is_read',
        'event_id',
        'target_tab',
        'created_at',
    ];

    /**
     * Ensure table exists, and then sync database activities into notifications
     */
    public static function checkTableAndSync()
    {
        // 1. Ensure table exists
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function ($table) {
                $table->id();
                $table->string('source_key')->nullable()->unique();
                $table->string('type');
                $table->string('title');
                $table->text('message')->nullable();
                $table->boolean('is_read')->default(false);
                $table->unsignedBigInteger('event_id')->nullable();
                $table->string('target_tab')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        } else {
            if (!Schema::hasColumn('notifications', 'event_id')) {
                Schema::table('notifications', function ($table) {
                    $table->unsignedBigInteger('event_id')->nullable();
                });
            }
            if (!Schema::hasColumn('notifications', 'target_tab')) {
                Schema::table('notifications', function ($table) {
                    $table->string('target_tab')->nullable();
                });
            }
        }

        // 2. Perform sync
        self::syncActivities();

        // 3. Backfill historic/NULL event_id records
        self::backfillEventIds();
    }

    /**
     * Helper to firstOrCreate and backfill event_id and target_tab if they are null
     */
    private static function helperFirstOrCreate($key, $attributes)
    {
        $notif = self::withTrashed()->firstOrCreate([
            'source_key' => $key
        ], $attributes);

        // Backfill logic for existing records
        $dirty = false;
        if (is_null($notif->event_id) && isset($attributes['event_id'])) {
            $notif->event_id = $attributes['event_id'];
            $dirty = true;
        }
        if (is_null($notif->target_tab) && isset($attributes['target_tab'])) {
            $notif->target_tab = $attributes['target_tab'];
            $dirty = true;
        }
        if ($dirty) {
            $notif->save();
        }

        return $notif;
    }

    private static function syncActivities()
    {
        // Sync Events
        $events = Event::where('is_configured', 1)->get();
        $now = now();
        foreach ($events as $event) {
            // Event created
            $key = "event_created_" . $event->id;
            self::helperFirstOrCreate($key, [
                'type' => 'events',
                'title' => 'Event baru berhasil dibuat',
                'message' => "Event {$event->name} berhasil dibuat dan dikonfigurasi.",
                'is_read' => false,
                'event_id' => $event->id,
                'target_tab' => null,
                'created_at' => $event->created_at,
            ]);

            // Event finished
            $endDate = \Carbon\Carbon::parse($event->end_date . ' ' . $event->end_time);
            if ($now->greaterThan($endDate)) {
                $keyFinished = "event_finished_" . $event->id;
                self::helperFirstOrCreate($keyFinished, [
                    'type' => 'events',
                    'title' => 'Event telah selesai',
                    'message' => "Event {$event->name} telah selesai dilaksanakan.",
                    'is_read' => false,
                    'event_id' => $event->id,
                    'target_tab' => null,
                    'created_at' => $endDate,
                ]);
            }

            // Event starting tomorrow
            $startDate = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time);
            if ($now->diffInDays($startDate, false) === 1 || ($now->copy()->addDay()->toDateString() === $event->start_date)) {
                $keyTomorrow = "event_tomorrow_" . $event->id;
                self::helperFirstOrCreate($keyTomorrow, [
                    'type' => 'events',
                    'title' => "Event {$event->name} dimulai besok",
                    'message' => "Event {$event->name} akan dimulai besok.",
                    'is_read' => false,
                    'event_id' => $event->id,
                    'target_tab' => null,
                    'created_at' => now(),
                ]);
            }
        }

        // Sync Registrations
        $registrations = Registration::with(['user', 'event', 'ticketCategory'])->get();
        foreach ($registrations as $reg) {
            if (!$reg->user || !$reg->event) continue;

            // Participant registration
            $keyReg = "registration_created_" . $reg->id;
            self::helperFirstOrCreate($keyReg, [
                'type' => 'participants',
                'title' => "{$reg->user->name} mendaftar ke {$reg->event->name}",
                'message' => "{$reg->user->name} mendaftar ke event {$reg->event->name}.",
                'is_read' => false,
                'event_id' => $reg->event_id,
                'target_tab' => 'participants',
                'created_at' => $reg->created_at,
            ]);

            // Participant ticket details (Budi membeli tiket VIP)
            $keyTicket = "registration_ticket_" . $reg->id;
            self::helperFirstOrCreate($keyTicket, [
                'type' => 'participants',
                'title' => "{$reg->user->name} membeli tiket " . ($reg->ticketCategory->name ?? 'Regular'),
                'message' => "Pembelian tiket category " . ($reg->ticketCategory->name ?? 'Regular') . " berhasil diproses.",
                'is_read' => false,
                'event_id' => $reg->event_id,
                'target_tab' => 'participants',
                'created_at' => $reg->created_at,
            ]);

            // Check-in
            if ($reg->is_checked_in && $reg->checked_in_at) {
                $keyCheckin = "registration_checked_" . $reg->id;
                self::helperFirstOrCreate($keyCheckin, [
                    'type' => 'check_in',
                    'title' => "{$reg->user->name} berhasil check-in",
                    'message' => "Peserta {$reg->user->name} berhasil check-in di event {$reg->event->name}.",
                    'is_read' => false,
                    'event_id' => $reg->event_id,
                    'target_tab' => 'participants',
                    'created_at' => $reg->checked_in_at,
                ]);
            }

        }

        // Sync Refunds from database
        if (Schema::hasTable('refunds')) {
            $dbRefunds = \App\Models\Refund::with(['registration.user', 'registration.event'])->get();
            foreach ($dbRefunds as $refund) {
                if (!$refund->registration || !$refund->registration->user || !$refund->registration->event) continue;

                $reg = $refund->registration;

                // Refund request notification
                $keyRefundReq = "refund_request_" . $refund->id;
                self::helperFirstOrCreate($keyRefundReq, [
                    'type' => 'refunds',
                    'title' => "Permintaan refund baru dari {$reg->user->name}",
                    'message' => "Permintaan pengembalian dana tiket event {$reg->event->name} diajukan.",
                    'is_read' => false,
                    'event_id' => $reg->event_id,
                    'target_tab' => 'refunds',
                    'created_at' => $refund->created_at,
                ]);

                if ($refund->status === 'approved') {
                    $keyRefundApp = "refund_approved_" . $refund->id;
                    self::helperFirstOrCreate($keyRefundApp, [
                        'type' => 'refunds',
                        'title' => "Refund disetujui untuk {$reg->user->name}",
                        'message' => "Refund tiket event {$reg->event->name} disetujui.",
                        'is_read' => false,
                        'event_id' => $reg->event_id,
                        'target_tab' => 'refunds',
                        'created_at' => $refund->updated_at,
                    ]);
                } elseif ($refund->status === 'rejected') {
                    $keyRefundRej = "refund_rejected_" . $refund->id;
                    self::helperFirstOrCreate($keyRefundRej, [
                        'type' => 'refunds',
                        'title' => "Refund ditolak untuk {$reg->user->name}",
                        'message' => "Permintaan refund tiket event {$reg->event->name} ditolak.",
                        'is_read' => false,
                        'event_id' => $reg->event_id,
                        'target_tab' => 'refunds',
                        'created_at' => $refund->updated_at,
                    ]);
                }
            }
        }

        // Sync Lucky Draw Winners
        $winners = LuckyDrawWinner::with(['registration.user', 'event'])->get();
        foreach ($winners as $winner) {
            if (!$winner->event) continue;

            $keyDraw = "lucky_draw_event_" . $winner->event_id;
            self::helperFirstOrCreate($keyDraw, [
                'type' => 'lucky_draw',
                'title' => 'Lucky Draw telah dijalankan',
                'message' => "Lucky draw diselenggarakan untuk event {$winner->event->name}.",
                'is_read' => false,
                'event_id' => $winner->event_id,
                'target_tab' => 'lucky_draw',
                'created_at' => $winner->won_at ?? $winner->created_at,
            ]);

            $keyWinner = "lucky_draw_winner_" . $winner->id;
            self::helperFirstOrCreate($keyWinner, [
                'type' => 'lucky_draw',
                'title' => 'Pemenang Lucky Draw berhasil dipilih',
                'message' => "Peserta " . ($winner->registration->user->name ?? 'Guest') . " memenangkan hadiah {$winner->prize_name}.",
                'is_read' => false,
                'event_id' => $winner->event_id,
                'target_tab' => 'lucky_draw',
                'created_at' => $winner->won_at ?? $winner->created_at,
            ]);
        }

        // Sync Certificates
        $certificates = Certificate::with(['registration.user', 'registration.event'])->get();
        foreach ($certificates as $cert) {
            if (!$cert->registration || !$cert->registration->event) continue;

            $keyCertGen = "certificate_gen_" . $cert->id;
            self::helperFirstOrCreate($keyCertGen, [
                'type' => 'certificates',
                'title' => "Sertifikat berhasil digenerate untuk " . ($cert->registration->user->name ?? 'Guest'),
                'message' => "Sertifikat diterbitkan untuk event {$cert->registration->event->name}.",
                'is_read' => false,
                'event_id' => $cert->registration->event_id,
                'target_tab' => 'certificates',
                'created_at' => $cert->created_at,
            ]);

            $keyCertReady = "certificate_ready_" . $cert->registration->event_id;
            self::helperFirstOrCreate($keyCertReady, [
                'type' => 'certificates',
                'title' => "Certificate untuk {$cert->registration->event->name} siap didownload",
                'message' => "Sertifikat kelayakan peserta siap diunduh.",
                'is_read' => false,
                'event_id' => $cert->registration->event_id,
                'target_tab' => 'certificates',
                'created_at' => $cert->created_at,
            ]);
        }
    }

    public static function backfillEventIds()
    {
        $nullNotifs = self::whereNull('event_id')->get();
        foreach ($nullNotifs as $notif) {
            $key = $notif->source_key;
            if (!$key) continue;

            $eventId = null;

            if (preg_match('/^event_(created|finished|tomorrow)_(\d+)$/', $key, $matches)) {
                $eventId = (int)$matches[2];
            } elseif (preg_match('/^lucky_draw_event_(\d+)$/', $key, $matches)) {
                $eventId = (int)$matches[1];
            } elseif (preg_match('/^certificate_ready_(\d+)$/', $key, $matches)) {
                $eventId = (int)$matches[1];
            } elseif (preg_match('/^(registration_created|registration_ticket|registration_checked)_(\d+)$/', $key, $matches)) {
                $regId = (int)$matches[2];
                $reg = Registration::find($regId);
                if ($reg) {
                    $eventId = $reg->event_id;
                }
            } elseif (preg_match('/^(refund_request|refund_approved|refund_rejected)_(\d+)$/', $key, $matches)) {
                $refId = (int)$matches[2];
                $refund = \App\Models\Refund::with('registration')->find($refId);
                if ($refund && $refund->registration) {
                    $eventId = $refund->registration->event_id;
                }
            } elseif (preg_match('/^lucky_draw_winner_(\d+)$/', $key, $matches)) {
                $winnerId = (int)$matches[1];
                $winner = LuckyDrawWinner::find($winnerId);
                if ($winner) {
                    $eventId = $winner->event_id;
                }
            } elseif (preg_match('/^certificate_gen_(\d+)$/', $key, $matches)) {
                $certId = (int)$matches[1];
                $cert = Certificate::with('registration')->find($certId);
                if ($cert && $cert->registration) {
                    $eventId = $cert->registration->event_id;
                }
            }

            if ($eventId) {
                $notif->event_id = $eventId;
                $notif->save();
            }
        }
    }
}
