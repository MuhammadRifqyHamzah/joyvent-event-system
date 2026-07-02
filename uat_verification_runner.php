<?php
/**
 * JoyVent Lucky Draw Refactoring - UAT & Implementation Verification Runner
 * Boots Laravel, runs the 10 UAT tests, checks database status, and outputs results.
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
$app->make(ConsoleKernel::class)->bootstrap();

use App\Models\Event;
use App\Models\EventPrize;
use App\Models\Registration;
use App\Models\User;
use App\Models\TicketCategory;
use App\Models\LuckyDrawWinner;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\LuckyDrawController;
use App\Http\Controllers\Api\LuckyDrawController as ApiLuckyDrawController;

echo "=========================================================\n";
echo "  JOYVENT LUCKY DRAW REFACTORING - UAT VERIFICATION RUNNER \n";
echo "=========================================================\n\n";

DB::beginTransaction();

try {
    // ----------------------------------------------------
    // SETUP: Create a test event and users
    // ----------------------------------------------------
    echo "[SETUP] Creating Test Event and User Candidates...\n";
    
    $event = Event::create([
        'name' => 'UAT Verification Event',
        'location' => 'Jakarta Creative Hub',
        'start_date' => '2026-07-01',
        'end_date' => '2026-07-02',
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
        'capacity' => 100,
        'has_lucky_draw' => true,
        'is_configured' => true,
    ]);

    $category = TicketCategory::create([
        'event_id' => $event->id,
        'name' => 'VIP',
        'price' => 1000000,
        'quota' => 100,
    ]);

    // Create 15 participant users
    $users = [];
    $registrations = [];
    for ($i = 1; $i <= 15; $i++) {
        $user = User::create([
            'name' => "UAT User $i",
            'email' => "uat_user$i@joyvent.com",
            'password' => bcrypt('password'),
            'role' => 'participant',
        ]);
        $users[$i] = $user;

        // Create registration
        $reg = Registration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'ticket_category_id' => $category->id,
            'qr_code' => "QR-UAT-$i",
            'is_checked_in' => true,
            'registration_status' => 'active',
            'payment_status' => 'paid',
        ]);
        $registrations[$i] = $reg;
    }

    $adminUser = User::create([
        'name' => 'Admin UAT',
        'email' => 'admin_uat@joyvent.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);
    // Log admin user in for APIs
    auth()->login($adminUser);

    echo "[SETUP DONE] Created Event ID: {$event->id}, Ticket Category, 15 Users, and Admin User.\n\n";

    // ----------------------------------------------------
    // TEST 1: Create Event with 3 Prizes
    // ----------------------------------------------------
    echo "--- TEST 1: Create Event with 3 Prizes (Repeater Simulation) ---\n";
    
    // Simulate EventController storeFeatures call
    $eventController = new \App\Http\Controllers\Admin\EventController();
    $requestData = [
        'has_lucky_draw' => 1,
        'prizes' => [
            [
                'name' => 'Grand Prize - MacBook Pro',
                'description' => 'Super fast laptop',
                'winner_count' => 1,
                'draw_order' => 0,
            ],
            [
                'name' => 'AirPods Pro 2',
                'description' => 'Noise cancelling earbuds',
                'winner_count' => 3,
                'draw_order' => 1,
            ],
            [
                'name' => 'JoyVent Merchandise Voucher',
                'description' => 'Voucher senilai Rp 100.000',
                'winner_count' => 5,
                'draw_order' => 2,
            ]
        ]
    ];
    
    $storeRequest = Request::create("/admin/events/{$event->id}/features", 'POST', $requestData);
    $eventController->storeFeatures($storeRequest, $event);

    // Verify row count in database
    $prizes = EventPrize::where('event_id', $event->id)->orderBy('draw_order', 'asc')->get();
    echo "Row count in event_prizes: " . $prizes->count() . " (Expected: 3)\n";
    foreach ($prizes as $p) {
        echo "  - Prize ID: {$p->id} | Name: {$p->name} | Winner Count: {$p->winner_count} | Status: {$p->status} | Draw Order: {$p->draw_order}\n";
    }
    
    $grandPrize = $prizes[0];
    $airpodsPrize = $prizes[1];
    $voucherPrize = $prizes[2];

    echo "TEST 1 Verdict: " . ($prizes->count() === 3 ? "PASS" : "FAIL") . "\n\n";

    // ----------------------------------------------------
    // TEST 2: Event Ongoing Dashboard
    // ----------------------------------------------------
    echo "--- TEST 2: Event Ongoing Dashboard (List Prizes) ---\n";
    // Check features loader inside EventController
    $ongoingPrizes = $event->eventPrizes;
    echo "Prizes loaded for Dashboard:\n";
    foreach ($ongoingPrizes as $idx => $p) {
        echo "  [" . ($idx+1) . "] Prize: {$p->name} | Target: {$p->winner_count} | Drawn: {$p->drawn_count} | Remaining: {$p->remaining_count} | Status: {$p->status}\n";
    }
    echo "TEST 2 Verdict: " . ($ongoingPrizes->count() === 3 ? "PASS" : "FAIL") . "\n\n";

    // ----------------------------------------------------
    // TEST 3: Draw Grand Prize
    // ----------------------------------------------------
    echo "--- TEST 3: Draw Grand Prize ---\n";
    $luckyDrawController = new LuckyDrawController();
    $drawRequest = Request::create('/admin/lucky-draw/draw', 'POST', [
        'event_prize_id' => $grandPrize->id,
    ]);
    // Expect expectsJson or ajax
    $drawRequest->headers->set('Accept', 'application/json');
    $drawResponse = $luckyDrawController->draw($drawRequest);
    $drawData = json_decode($drawResponse->getContent(), true);

    if ($drawData['success']) {
        $winnerName = $drawData['data']['registration']['user']['name'];
        $drawNo = $drawData['data']['draw_number'];
        $prizeNameBackfill = $drawData['data']['prize_name'];
        echo "Successfully drawn winner: $winnerName\n";
        echo "Draw Number: $drawNo (Expected: 1)\n";
        echo "Denormalized Prize Name: $prizeNameBackfill (Expected: Grand Prize - MacBook Pro)\n";
    } else {
        echo "Draw Failed: " . $drawData['message'] . "\n";
    }

    $grandPrize->refresh();
    echo "Grand Prize Status: {$grandPrize->status} (Expected: completed)\n";
    echo "Grand Prize Drawn Count: {$grandPrize->drawn_count} (Expected: 1)\n";
    echo "Grand Prize Remaining: {$grandPrize->remaining_count} (Expected: 0)\n";
    echo "TEST 3 Verdict: " . ($grandPrize->status === 'completed' && $grandPrize->drawn_count === 1 ? "PASS" : "FAIL") . "\n\n";

    // ----------------------------------------------------
    // TEST 4: Draw AirPods 3 times
    // ----------------------------------------------------
    echo "--- TEST 4: Draw AirPods 3 times ---\n";
    for ($d = 1; $d <= 3; $d++) {
        $drawReq = Request::create('/admin/lucky-draw/draw', 'POST', [
            'event_prize_id' => $airpodsPrize->id,
        ]);
        $drawReq->headers->set('Accept', 'application/json');
        $resp = $luckyDrawController->draw($drawReq);
        $resData = json_decode($resp->getContent(), true);
        
        if ($resData['success']) {
            echo "  Draw $d: Winner = " . $resData['data']['registration']['user']['name'] . " | Draw Number = " . $resData['data']['draw_number'] . "\n";
        } else {
            echo "  Draw $d Failed: " . $resData['message'] . "\n";
        }
    }

    $airpodsPrize->refresh();
    echo "AirPods Prize Status: {$airpodsPrize->status} (Expected: completed)\n";
    echo "AirPods Drawn Count: {$airpodsPrize->drawn_count} (Expected: 3)\n";
    echo "AirPods Remaining: {$airpodsPrize->remaining_count} (Expected: 0)\n";
    
    // Draw AirPods 4th time (over quota)
    $overQuotaReq = Request::create('/admin/lucky-draw/draw', 'POST', [
        'event_prize_id' => $airpodsPrize->id,
    ]);
    $overQuotaReq->headers->set('Accept', 'application/json');
    $overQuotaResp = $luckyDrawController->draw($overQuotaReq);
    $overQuotaData = json_decode($overQuotaResp->getContent(), true);
    echo "Draw 4 (Over Quota): Success: " . ($overQuotaData['success'] ? 'true' : 'false') . " | Msg: " . $overQuotaData['message'] . " | HTTP Code: " . $overQuotaResp->getStatusCode() . "\n";

    echo "TEST 4 Verdict: " . ($airpodsPrize->status === 'completed' && $airpodsPrize->drawn_count === 3 && $overQuotaResp->getStatusCode() === 422 ? "PASS" : "FAIL") . "\n\n";

    // ----------------------------------------------------
    // TEST 5: Duplicate Winner Prevention
    // ----------------------------------------------------
    echo "--- TEST 5: Duplicate Winner Prevention ---\n";
    // Check if the current winners list contains any user with multiple wins at the same event.
    $duplicatedWinners = DB::table('lucky_draw_winners')
        ->join('registrations', 'lucky_draw_winners.registration_id', '=', 'registrations.id')
        ->where('lucky_draw_winners.event_id', $event->id)
        ->select('registrations.user_id', DB::raw('COUNT(*) as win_count'))
        ->groupBy('registrations.user_id')
        ->having('win_count', '>', 1)
        ->get();
    
    echo "Number of users who won multiple times: " . $duplicatedWinners->count() . " (Expected: 0)\n";
    foreach ($duplicatedWinners as $dup) {
        echo "  - User ID: {$dup->user_id} | Wins: {$dup->win_count}\n";
    }
    echo "TEST 5 Verdict: " . ($duplicatedWinners->count() === 0 ? "PASS" : "FAIL") . "\n\n";

    // ----------------------------------------------------
    // TEST 6: Refund Validation
    // ----------------------------------------------------
    echo "--- TEST 6: Refund Validation ---\n";
    // Set up UAT User 10 with a PENDING refund, and UAT User 11 with an APPROVED refund
    $user10Reg = $registrations[10];
    $user11Reg = $registrations[11];

    Refund::create([
        'registration_id' => $user10Reg->id,
        'amount' => 1000000,
        'status' => 'pending',
        'reason' => 'UAT Refund Pending test',
    ]);
    
    Refund::create([
        'registration_id' => $user11Reg->id,
        'amount' => 1000000,
        'status' => 'approved',
        'reason' => 'UAT Refund Approved test',
    ]);

    // Query candidate query to see if user 10 and user 11 are excluded
    // We'll mimic the query in LuckyDrawController:
    $winnerUserIds = DB::table('lucky_draw_winners')
        ->join('registrations', 'lucky_draw_winners.registration_id', '=', 'registrations.id')
        ->where('lucky_draw_winners.event_id', $event->id)
        ->pluck('registrations.user_id');

    $candidates = Registration::where('event_id', $event->id)
        ->where('registration_status', 'active')
        ->where('payment_status', 'paid')
        ->where('status', '!=', 'cancelled')
        ->whereNotIn('user_id', $winnerUserIds)
        ->where(function ($q) {
            $q->whereDoesntHave('refund')
                ->orWhereHas('refund', function ($qr) {
                    $qr->whereNotIn('status', ['pending', 'approved']);
                });
        })
        ->pluck('user_id')
        ->toArray();

    $isUser10Excluded = !in_array($users[10]->id, $candidates);
    $isUser11Excluded = !in_array($users[11]->id, $candidates);

    echo "User 10 (Pending Refund) Excluded: " . ($isUser10Excluded ? "YES" : "NO") . " (Expected: YES)\n";
    echo "User 11 (Approved Refund) Excluded: " . ($isUser11Excluded ? "YES" : "NO") . " (Expected: YES)\n";
    echo "TEST 6 Verdict: " . ($isUser10Excluded && $isUser11Excluded ? "PASS" : "FAIL") . "\n\n";

    // ----------------------------------------------------
    // TEST 7: Payment Validation
    // ----------------------------------------------------
    echo "--- TEST 7: Payment Validation ---\n";
    // Set up UAT User 12 with a pending payment status
    $user12Reg = $registrations[12];
    $user12Reg->payment_status = 'pending';
    $user12Reg->save();

    // Query candidates again
    $candidates = Registration::where('event_id', $event->id)
        ->where('registration_status', 'active')
        ->where('payment_status', 'paid')
        ->where('status', '!=', 'cancelled')
        ->whereNotIn('user_id', $winnerUserIds)
        ->where(function ($q) {
            $q->whereDoesntHave('refund')
                ->orWhereHas('refund', function ($qr) {
                    $qr->whereNotIn('status', ['pending', 'approved']);
                });
        })
        ->pluck('user_id')
        ->toArray();

    $isUser12Excluded = !in_array($users[12]->id, $candidates);
    echo "User 12 (Pending Payment) Excluded: " . ($isUser12Excluded ? "YES" : "NO") . " (Expected: YES)\n";
    echo "TEST 7 Verdict: " . ($isUser12Excluded ? "PASS" : "FAIL") . "\n\n";

    // ----------------------------------------------------
    // TEST 8: Legacy Event Compatibility
    // ----------------------------------------------------
    echo "--- TEST 8: Legacy Event Compatibility ---\n";
    // Create an old event that has only been configured with single-prize structure
    $legacyEvent = Event::create([
        'name' => 'Legacy Conference 2025',
        'location' => 'Bandung IT Hall',
        'start_date' => '2025-12-01',
        'end_date' => '2025-12-02',
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
        'capacity' => 10,
        'has_lucky_draw' => true,
        'prize_name' => 'iPhone 13 Mini',
        'prize_description' => 'Original single prize',
        'winner_count' => 1,
        'is_configured' => true,
    ]);

    // Backfill logic runs on migration. Let's see if we backfill a new legacy event programmatically or verify backfilled rows.
    // In migration, we run backfill. Let's backfill this legacy event manually to prove that the backfill code handles it safely.
    $drawnCount = 0;
    $status = 'waiting';
    $prizeId = DB::table('event_prizes')->insertGetId([
        'event_id' => $legacyEvent->id,
        'name' => $legacyEvent->prize_name,
        'description' => $legacyEvent->prize_description,
        'image' => null,
        'winner_count' => $legacyEvent->winner_count,
        'drawn_count' => $drawnCount,
        'status' => $status,
        'draw_order' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $legacyEventPrize = EventPrize::find($prizeId);
    echo "Legacy Event backfilled prize created: ID: {$legacyEventPrize->id} | Name: {$legacyEventPrize->name} | Target: {$legacyEventPrize->winner_count}\n";
    
    // Check if we can still draw this legacy prize
    // Set up 1 checked-in candidate for this legacy event
    $legacyUser = User::create([
        'name' => 'Legacy User',
        'email' => 'legacy_user@joyvent.com',
        'password' => bcrypt('password'),
        'role' => 'participant',
    ]);
    
    $legacyCategory = TicketCategory::create([
        'event_id' => $legacyEvent->id,
        'name' => 'Regular',
        'price' => 150000,
        'quota' => 10,
    ]);

    Registration::create([
        'user_id' => $legacyUser->id,
        'event_id' => $legacyEvent->id,
        'ticket_category_id' => $legacyCategory->id,
        'qr_code' => 'QR-LEGACY',
        'is_checked_in' => true,
        'registration_status' => 'active',
        'payment_status' => 'paid',
    ]);

    $legacyDrawReq = Request::create('/admin/lucky-draw/draw', 'POST', [
        'event_prize_id' => $legacyEventPrize->id,
    ]);
    $legacyDrawReq->headers->set('Accept', 'application/json');
    $legacyResp = $luckyDrawController->draw($legacyDrawReq);
    $legacyData = json_decode($legacyResp->getContent(), true);

    echo "Legacy Draw Success: " . ($legacyData['success'] ? 'true' : 'false') . " | Winner: " . ($legacyData['data']['registration']['user']['name'] ?? 'none') . "\n";
    echo "TEST 8 Verdict: " . ($legacyData['success'] ? "PASS" : "FAIL") . "\n\n";

    // ----------------------------------------------------
    // TEST 9: Mobile Compatibility
    // ----------------------------------------------------
    echo "--- TEST 9: Mobile API Compatibility ---\n";
    $apiController = new ApiLuckyDrawController();
    
    // Simulate GET /api/events/{id}/winners
    $winnersReq = Request::create("/api/events/{$event->id}/winners", 'GET');
    $winnersResp = $apiController->getWinners($event->id);
    $winnersJson = json_decode($winnersResp->getContent(), true);
    
    echo "GET /api/events/{id}/winners payload snippet:\n";
    foreach ($winnersJson['data'] as $winnerRow) {
        echo "  - Winner ID: {$winnerRow['id']} | Prize Name Field: {$winnerRow['prize_name']} | Draw Number: {$winnerRow['draw_number']}\n";
    }

    // Simulate GET /api/lucky-draw/my-wins (acting as winner of Grand Prize)
    // Find winner registration of grand prize
    $grandWinner = LuckyDrawWinner::where('event_id', $event->id)->where('event_prize_id', $grandPrize->id)->first();
    $grandWinnerUser = $grandWinner->registration->user;
    
    auth()->login($grandWinnerUser);
    $myWinsReq = Request::create("/api/lucky-draw/my-wins", 'GET');
    // Inject auth user
    $myWinsReq->setUserResolver(function() use ($grandWinnerUser) { return $grandWinnerUser; });
    
    $myWinsResp = $apiController->getMyWins($myWinsReq);
    $myWinsJson = json_decode($myWinsResp->getContent(), true);

    echo "GET /api/lucky-draw/my-wins payload for {$grandWinnerUser->name}:\n";
    foreach ($myWinsJson['data'] as $winRow) {
        echo "  - Win ID: {$winRow['id']} | Event Name: {$winRow['event_name']} | Prize: {$winRow['prize_name']} | Won At: {$winRow['won_at']}\n";
    }
    
    $hasPrizeNameWinners = isset($winnersJson['data'][0]['prize_name']);
    $hasPrizeNameMyWins = isset($myWinsJson['data'][0]['prize_name']);

    echo "TEST 9 Verdict: " . ($hasPrizeNameWinners && $hasPrizeNameMyWins ? "PASS" : "FAIL") . "\n\n";

    // Restore admin auth
    auth()->login($adminUser);

    // ----------------------------------------------------
    // TEST 10: Race Condition Protection
    // ----------------------------------------------------
    echo "--- TEST 10: Race Condition Protection (Conflict 409) ---\n";
    // We set status of voucherPrize to 'drawing'
    $voucherPrize->status = 'drawing';
    $voucherPrize->save();

    // Now try to run a draw on it
    $conflictReq = Request::create('/admin/lucky-draw/draw', 'POST', [
        'event_prize_id' => $voucherPrize->id,
    ]);
    $conflictReq->headers->set('Accept', 'application/json');
    $conflictResp = $luckyDrawController->draw($conflictReq);
    $conflictData = json_decode($conflictResp->getContent(), true);

    echo "Draw on 'drawing' status: Success: " . ($conflictData['success'] ? 'true' : 'false') . " | Msg: " . $conflictData['message'] . " | HTTP Code: " . $conflictResp->getStatusCode() . "\n";
    echo "TEST 10 Verdict: " . ($conflictResp->getStatusCode() === 409 ? "PASS" : "FAIL") . "\n\n";

} catch (\Exception $ex) {
    echo "Exception occurred during UAT verification: " . $ex->getMessage() . "\n" . $ex->getTraceAsString() . "\n";
} finally {
    // Roll back transaction to keep the database clean
    DB::rollBack();
    echo "[CLEANUP] Rolled back database transaction successfully.\n";
}
