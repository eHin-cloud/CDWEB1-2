<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "========================================================================================\n";
echo "        CHUONG TRINH KIEM THU TOAN DIEN HE THONG SMARTROOM & RENTY PORTAL\n";
echo "========================================================================================\n\n";

// 1. KIEM TRA CO SO DU LIEU
echo "--- 1. KIEM TRA TRANG THAI CO SO DU LIEU ---\n";
try {
    $dbName = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
    $tenantsCount = \App\Models\Tenant::count();
    $usersCount = \App\Models\User::count();
    $buildingsCount = \App\Models\Building::count();
    $roomsCount = \App\Models\Room::count();
    $residentsCount = \App\Models\Resident::count();
    $contractsCount = \App\Models\Contract::count();
    $utilitiesCount = \App\Models\UtilityRecord::count();
    $reviewsCount = \App\Models\Review::count();
    $equipmentCount = \App\Models\Equipment::count();
    $ticketsCount = \App\Models\Ticket::count();

    echo "[PASS] Ket noi CSDL thanh cong: Database [$dbName]\n";
    echo "  - Tenants (Don vi kinh doanh): $tenantsCount\n";
    echo "  - Users (Tai khoan he thong):  $usersCount\n";
    echo "  - Buildings (Toa nha/Co so):   $buildingsCount\n";
    echo "  - Rooms (Phong luu tru):       $roomsCount\n";
    echo "  - Residents (Cu dan/Khach o):  $residentsCount\n";
    echo "  - Contracts (Hop dong):        $contractsCount\n";
    echo "  - UtilityRecords (Dien nuoc):  $utilitiesCount\n";
    echo "  - Reviews (Danh gia phong):    $reviewsCount\n";
    echo "  - Equipment (Trang thiet bi):  $equipmentCount\n";
    echo "  - Tickets (Su co ky thuat):    $ticketsCount\n";
} catch (\Throwable $e) {
    echo "[FAIL] Loi ket noi CSDL: " . $e->getMessage() . "\n";
    exit(1);
}

// Ham helper de chay request voi user da dang nhap
function testRequest($kernel, $method, $uri, $user = null, $data = [], $expectedCode = 200, $desc = '') {
    $session = app('session.store');
    $req = \Illuminate\Http\Request::create($uri, $method, $data);
    $req->setLaravelSession($session);

    if ($user) {
        $session->put(auth()->getName(), $user->getAuthIdentifier());
        $req->setUserResolver(fn() => $user);
        \Illuminate\Support\Facades\Auth::setUser($user);
    } else {
        $session->flush();
        $req->setUserResolver(fn() => null);
        \Illuminate\Support\Facades\Auth::logout();
    }
    
    if ($method === 'POST' || $method === 'PUT' || $method === 'DELETE') {
        $req->headers->set('Accept', 'application/json');
    }
    
    $start = microtime(true);
    try {
        $resp = $kernel->handle($req);
        $status = $resp->getStatusCode();
        $dur = round((microtime(true) - $start) * 1000, 1);
        $pass = ($status === $expectedCode);
        $tag = $pass ? "[PASS]" : "[FAIL]";
        echo sprintf("%s [%3d] %-6s %-45s (%s - %sms)\n", $tag, $status, $method, $uri, $desc, $dur);
        $kernel->terminate($req, $resp);
        return ['pass' => $pass, 'status' => $status, 'response' => $resp];
    } catch (\Throwable $e) {
        echo sprintf("[ERR ] [---] %-6s %-45s (%s: %s)\n", $method, $uri, $desc, $e->getMessage());
        return ['pass' => false, 'status' => 500, 'error' => $e->getMessage()];
    }
}

// 2. KIEM TRA PHAN HE SUPERADMIN
echo "\n--- 2. KIEM TRA PHAN HE SUPERADMIN ---\n";
$superadmin = \App\Models\User::where('role', 'admin')->orWhereHas('roleRecord', fn($q)=>$q->where('slug', 'admin'))->first();
if ($superadmin) {
    echo "Dang nhap voi tai khoan Superadmin: {$superadmin->username} (ID: {$superadmin->id})\n";
    testRequest($kernel, 'GET', '/list', $superadmin, [], 200, 'Quan ly nguoi dung & Phan quyen');
    testRequest($kernel, 'GET', '/admin/verifications', $superadmin, [], 200, 'Kiem duyet ho so KYC & Premium');
    testRequest($kernel, 'GET', '/admin/audit-logs', $superadmin, [], 200, 'Nhat ky kiem toan Audit Logs');
} else {
    echo "[WARN] Khong tim thay tai khoan Superadmin trong CSDL.\n";
}

// 3. KIEM TRA PHAN HE CHU TRO (SMARTROOM ADMIN)
echo "\n--- 3. KIEM TRA PHAN HE CHU TRO (SMARTROOM ADMIN) ---\n";
$landlord = \App\Models\User::where('role', 'landlord')
    ->orWhereHas('roleRecord', fn($q)=>$q->where('slug', 'landlord'))
    ->first();

if ($landlord) {
    echo "Dang nhap voi tai khoan Chu tro: {$landlord->username} (ID: {$landlord->id}, Tenant: {$landlord->tenant_id})\n";
    testRequest($kernel, 'GET', '/smartroom/admin', $landlord, [], 200, 'Overview Dashboard Chu tro');
    testRequest($kernel, 'GET', '/smartroom/admin/buildings', $landlord, [], 200, 'Quan ly Co so luu tru');
    testRequest($kernel, 'GET', '/smartroom/admin/rooms', $landlord, [], 200, 'Quan ly Phong & Ma tran phong');
    testRequest($kernel, 'GET', '/smartroom/admin/equipment', $landlord, [], 200, 'Quan ly Danh muc Trang thiet bi');
    testRequest($kernel, 'GET', '/smartroom/admin/reports', $landlord, [], 200, 'So quy thu chi phat sinh');
    testRequest($kernel, 'GET', '/smartroom/admin/payments', $landlord, [], 200, 'Quan ly Thanh toan & Hoa don');
    testRequest($kernel, 'GET', '/smartroom/admin/activity-logs', $landlord, [], 200, 'Nhat ky thao tac quan tri');
    testRequest($kernel, 'GET', '/smartroom/admin/iot/summary', $landlord, [], 200, 'Tong quan IoT Smart Metering');
} else {
    echo "[WARN] Khong tim thay tai khoan Chu tro trong CSDL.\n";
}

// 4. KIEM TRA PHAN HE CU DAN (RESIDENT PORTAL)
echo "\n--- 4. KIEM TRA PHAN HE CU DAN (RESIDENT PORTAL) ---\n";
$residentUser = \App\Models\User::where('role', 'resident')
    ->orWhere('role', 'user')
    ->orWhereHas('roleRecord', fn($q)=>$q->where('slug', 'resident'))
    ->has('resident')
    ->first();

if (!$residentUser) {
    $residentUser = \App\Models\User::whereHas('resident')->first();
}

if ($residentUser) {
    echo "Dang nhap voi tai khoan Cu dan: {$residentUser->username} (ID: {$residentUser->id})\n";
    testRequest($kernel, 'GET', '/smartroom/resident', $residentUser, [], 200, 'Trang chu Cong Cu dan');
} else {
    echo "[WARN] Khong tim thay tai khoan Cu dan co lien ket phong.\n";
}

// 5. KIEM TRA PHAN HE PUBLIC (RENTY REVIEW & PORTAL)
echo "\n--- 5. KIEM TRA PHAN HE PUBLIC (RENTY PORTAL) ---\n";
testRequest($kernel, 'GET', '/renty', null, [], 200, 'Trang chu Renty Portal');
testRequest($kernel, 'GET', '/api/renty/rooms', null, [], 200, 'API lay danh sach phong');
testRequest($kernel, 'GET', '/api/renty/rooms/smart-search?keyword=Hà+Nội', null, [], 200, 'API SmartSearch theo khu vuc');
$firstRoom = \App\Models\Room::first();
if ($firstRoom) {
    testRequest($kernel, 'GET', '/renty/room/' . $firstRoom->id, null, [], 200, 'Chi tiet phong ID: ' . $firstRoom->id);
}
testRequest($kernel, 'GET', '/renty/room-3d', null, [], 200, 'Xem phong 3D truc quan');

// 6. KIEM TRA CAC API CHUYEN BIET
echo "\n--- 6. KIEM TRA API NGHIEP VU CHUYEN BIET (IOT, REVENUE, AI) ---\n";
// API Revenue breakdown
testRequest($kernel, 'GET', '/api/revenue-breakdown', $landlord, [], 200, 'API co cau doanh thu dien/nuoc/phong');

// API IoT Ingestion
$iotPayload = [
    'meter_serial' => 'CT-ELEC-TEST-01',
    'meter_type' => 'electricity',
    'reading_kwh' => 156.8,
    'current_power_w' => 1250,
    'voltage_v' => 220.5
];
testRequest($kernel, 'POST', '/api/v1/iot/telemetry', null, $iotPayload, 200, 'API nap du lieu xung IoT dien');

echo "\n========================================================================================\n";
echo "                        HOAN TAT KIEM TRA TOAN DIEN HE THONG\n";
echo "========================================================================================\n";
