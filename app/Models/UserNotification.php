<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Registration;
use App\Models\LuckyDrawWinner;

class UserNotification extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'user_notifications';

    protected $fillable = [
        'source_key',
        'user_id',
        'event_id',
        'title',
        'message',
        'type',
        'action_url',
        'data',
        'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Generate notifications dynamically for a specific user based on database records.
     */
    public static function generateForUser($userId)
    {
        $now = now();

        // 1. Fetch user's registrations (active and historical)
        $registrations = Registration::where('user_id', $userId)
            ->with(['event', 'refund', 'certificate'])
            ->get();

        foreach ($registrations as $reg) {
            $event = $reg->event;
            if (!$event) continue;

            // A. TIKET BERHASIL DIPESAN
            $keyOrdered = "ticket_ordered_" . $reg->id;
            self::withTrashed()->firstOrCreate([
                'source_key' => $keyOrdered
            ], [
                'user_id' => $userId,
                'event_id' => $reg->event_id,
                'title' => 'Tiket Berhasil Dipesan',
                'message' => "Pemesanan tiket Anda untuk event {$event->name} berhasil dibuat. Silakan selesaikan pembayaran.",
                'type' => 'payment',
                'action_url' => "ticket?registrationId={$reg->id}",
                'data' => [
                    'registration_id' => $reg->id,
                    'event_id' => $reg->event_id
                ],
                'is_read' => false,
                'created_at' => $reg->created_at,
            ]);

            // B. PEMBAYARAN BERHASIL
            if ($reg->payment_status === 'paid') {
                // Hapus notifikasi menunggu/ditolak jika pembayaran disetujui
                self::where('user_id', $userId)->whereIn('source_key', [
                    "payment_waiting_verification_" . $reg->id,
                    "payment_rejected_" . $reg->id
                ])->delete();

                $keyPaid = "payment_success_" . $reg->id;
                self::withTrashed()->firstOrCreate([
                    'source_key' => $keyPaid
                ], [
                    'user_id' => $userId,
                    'event_id' => $reg->event_id,
                    'title' => 'Pembayaran Berhasil',
                    'message' => "Pembayaran Anda untuk event {$event->name} telah terverifikasi. Tiket Anda sudah aktif.",
                    'type' => 'payment',
                    'action_url' => "ticket?registrationId={$reg->id}",
                    'data' => [
                        'registration_id' => $reg->id,
                        'event_id' => $reg->event_id
                    ],
                    'is_read' => false,
                    'created_at' => $reg->paid_at ?? $reg->updated_at,
                ]);
            }

            // B.01 PEMBAYARAN MENUNGGU VERIFIKASI
            if ($reg->payment_status === 'waiting_verification') {
                self::where('user_id', $userId)->where('source_key', "payment_rejected_" . $reg->id)->delete();

                $keyWaitingVerif = "payment_waiting_verification_" . $reg->id;
                $notif = self::withTrashed()->where('source_key', $keyWaitingVerif)->first();
                if ($notif) {
                    $notif->update([
                        'is_read' => false,
                        'created_at' => $reg->payment_proof_uploaded_at ?? $reg->updated_at ?? now(),
                        'deleted_at' => null
                    ]);
                } else {
                    self::create([
                        'source_key' => $keyWaitingVerif,
                        'user_id' => $userId,
                        'event_id' => $reg->event_id,
                        'title' => 'Pembayaran Menunggu Verifikasi',
                        'message' => "Bukti pembayaran Anda untuk event {$event->name} telah dikirim dan sedang diverifikasi oleh admin.",
                        'type' => 'payment',
                        'action_url' => "ticket?registrationId={$reg->id}",
                        'data' => [
                            'registration_id' => $reg->id,
                            'event_id' => $reg->event_id
                        ],
                        'is_read' => false,
                        'created_at' => $reg->payment_proof_uploaded_at ?? $reg->updated_at ?? now(),
                    ]);
                }
            }

            // B.02 PEMBAYARAN DITOLAK
            if ($reg->payment_status === 'rejected') {
                self::where('user_id', $userId)->where('source_key', "payment_waiting_verification_" . $reg->id)->delete();

                $keyRejected = "payment_rejected_" . $reg->id;
                $notif = self::withTrashed()->where('source_key', $keyRejected)->first();
                if ($notif) {
                    $notif->update([
                        'is_read' => false,
                        'message' => "Bukti pembayaran Anda untuk event {$event->name} ditolak. Alasan: " . ($reg->payment_rejection_reason ?? 'Berkas tidak terbaca atau nominal salah.') . " Silakan unggah bukti yang valid.",
                        'data' => [
                            'registration_id' => $reg->id,
                            'event_id' => $reg->event_id,
                            'rejection_reason' => $reg->payment_rejection_reason
                        ],
                        'created_at' => $reg->payment_rejected_at ?? $reg->updated_at ?? now(),
                        'deleted_at' => null
                    ]);
                } else {
                    self::create([
                        'source_key' => $keyRejected,
                        'user_id' => $userId,
                        'event_id' => $reg->event_id,
                        'title' => 'Pembayaran Ditolak',
                        'message' => "Bukti pembayaran Anda untuk event {$event->name} ditolak. Alasan: " . ($reg->payment_rejection_reason ?? 'Berkas tidak terbaca atau nominal salah.') . " Silakan unggah bukti yang valid.",
                        'type' => 'payment',
                        'action_url' => "ticket?registrationId={$reg->id}",
                        'data' => [
                            'registration_id' => $reg->id,
                            'event_id' => $reg->event_id,
                            'rejection_reason' => $reg->payment_rejection_reason
                        ],
                        'is_read' => false,
                        'created_at' => $reg->payment_rejected_at ?? $reg->updated_at ?? now(),
                    ]);
                }
            }

            // B.1 PEMBAYARAN KEDALUWARSA
            if (in_array($reg->payment_status, ['failed', 'expired']) && $reg->registration_status === 'cancelled') {
                // Hapus notifikasi menunggu/ditolak jika expired
                self::where('user_id', $userId)->whereIn('source_key', [
                    "payment_waiting_verification_" . $reg->id,
                    "payment_rejected_" . $reg->id
                ])->delete();

                $keyExpired = "payment_expired_" . $reg->id;
                self::withTrashed()->firstOrCreate([
                    'source_key' => $keyExpired
                ], [
                    'user_id' => $userId,
                    'event_id' => $reg->event_id,
                    'title' => 'Pembayaran Kedaluwarsa',
                    'message' => "Pembayaran untuk event {$event->name} telah melewati batas waktu. Tiket otomatis dibatalkan.",
                    'type' => 'payment',
                    'action_url' => "ticket?registrationId={$reg->id}",
                    'data' => [
                        'registration_id' => $reg->id,
                        'event_id' => $reg->event_id
                    ],
                    'is_read' => false,
                    'created_at' => $reg->updated_at,
                ]);
            }

            // C. REFUND UPDATES
            if ($reg->refund) {
                $refund = $reg->refund;

                // Refund Diajukan
                $keyRefundReq = "refund_submitted_" . $refund->id;
                self::withTrashed()->firstOrCreate([
                    'source_key' => $keyRefundReq
                ], [
                    'user_id' => $userId,
                    'event_id' => $reg->event_id,
                    'title' => 'Refund Diajukan',
                    'message' => "Permintaan pengembalian dana tiket event {$event->name} sedang diproses.",
                    'type' => 'refund',
                    'action_url' => "ticket?registrationId={$reg->id}",
                    'data' => [
                        'registration_id' => $reg->id,
                        'refund_id' => $refund->id,
                        'event_id' => $reg->event_id
                    ],
                    'is_read' => false,
                    'created_at' => $refund->created_at,
                ]);

                // Refund Disetujui
                if ($refund->status === 'approved') {
                    $keyRefundApp = "refund_approved_" . $refund->id;
                    self::withTrashed()->firstOrCreate([
                        'source_key' => $keyRefundApp
                    ], [
                        'user_id' => $userId,
                        'event_id' => $reg->event_id,
                        'title' => 'Refund Disetujui',
                        'message' => "Pengajuan refund untuk event {$event->name} telah disetujui.",
                        'type' => 'refund',
                        'action_url' => "ticket?registrationId={$reg->id}",
                        'data' => [
                            'registration_id' => $reg->id,
                            'refund_id' => $refund->id,
                            'event_id' => $reg->event_id
                        ],
                        'is_read' => false,
                        'created_at' => $refund->updated_at,
                    ]);
                }

                // Refund Ditolak
                if ($refund->status === 'rejected') {
                    $keyRefundRej = "refund_rejected_" . $refund->id;
                    self::withTrashed()->firstOrCreate([
                        'source_key' => $keyRefundRej
                    ], [
                        'user_id' => $userId,
                        'event_id' => $reg->event_id,
                        'title' => 'Refund Ditolak',
                        'message' => "Pengajuan refund untuk event {$event->name} telah ditolak.",
                        'type' => 'refund',
                        'action_url' => "ticket?registrationId={$reg->id}",
                        'data' => [
                            'registration_id' => $reg->id,
                            'refund_id' => $refund->id,
                            'event_id' => $reg->event_id
                        ],
                        'is_read' => false,
                        'created_at' => $refund->updated_at,
                    ]);
                }
            }

            // D. SERTIFIKAT TERSEDIA
            if ($reg->certificate) {
                $cert = $reg->certificate;
                $keyCert = "certificate_ready_" . $cert->id;
                self::withTrashed()->firstOrCreate([
                    'source_key' => $keyCert
                ], [
                    'user_id' => $userId,
                    'event_id' => $reg->event_id,
                    'title' => 'Sertifikat Tersedia',
                    'message' => "Sertifikat untuk event {$event->name} telah terbit. Silakan unduh sertifikat kelayakan Anda.",
                    'type' => 'certificate',
                    'action_url' => "certificate-detail?registrationId={$reg->id}",
                    'data' => [
                        'registration_id' => $reg->id,
                        'certificate_id' => $cert->id,
                        'event_id' => $reg->event_id
                    ],
                    'is_read' => false,
                    'created_at' => $cert->created_at,
                ]);
            }

            // E. LUCKY DRAW WINNER
            $winner = LuckyDrawWinner::where('registration_id', $reg->id)->first();
            if ($winner) {
                $keyWinner = "lucky_draw_winner_" . $winner->id;
                self::withTrashed()->firstOrCreate([
                    'source_key' => $keyWinner
                ], [
                    'user_id' => $userId,
                    'event_id' => $reg->event_id,
                    'title' => 'Selamat! Anda Menang Lucky Draw',
                    'message' => "Anda memenangkan hadiah {$winner->prize_name} pada event {$event->name}!",
                    'type' => 'lucky_draw',
                    'action_url' => "lucky-draw",
                    'data' => [
                        'registration_id' => $reg->id,
                        'winner_id' => $winner->id,
                        'prize_name' => $winner->prize_name,
                        'event_id' => $reg->event_id
                    ],
                    'is_read' => false,
                    'created_at' => $winner->won_at ?? $winner->created_at,
                ]);
            }

            // F. EVENT DIMULAI BESOK
            $startDate = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time);
            if ($now->diffInDays($startDate, false) === 1 || ($now->copy()->addDay()->toDateString() === $event->start_date)) {
                $keyTomorrow = "event_tomorrow_" . $event->id;
                self::withTrashed()->firstOrCreate([
                    'source_key' => $keyTomorrow
                ], [
                    'user_id' => $userId,
                    'event_id' => $reg->event_id,
                    'title' => 'Event Dimulai Besok',
                    'message' => "Event {$event->name} akan dimulai besok. Persiapkan tiket Anda.",
                    'type' => 'event',
                    'action_url' => "ticket?registrationId={$reg->id}",
                    'data' => [
                        'registration_id' => $reg->id,
                        'event_id' => $reg->event_id
                    ],
                    'is_read' => false,
                    'created_at' => now(),
                ]);
            }
        }
    }
}
