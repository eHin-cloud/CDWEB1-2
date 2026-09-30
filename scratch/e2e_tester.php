<?php

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = '/tmp/smartroom_test_cookies.txt';

function makeRequest($method, $path, $data = [], $useCookie = true, $followRedirect = false) {
    global $baseUrl, $cookieFile;
    $url = $baseUrl . $path;
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    if ($useCookie) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }
    
    if ($followRedirect) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    }
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if (is_array($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
        }
    }
    
    $start = microtime(true);
    $response = curl_exec($ch);
    $dur = round((microtime(true) - $start) * 1000, 1);
    
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    curl_close($ch);
    return ['status' => $statusCode, 'header' => $header, 'body' => $body, 'dur' => $dur];
}

function extractCsrf($html) {
    if (preg_match('/<input[^>]*name="_token"[^>]*value="([^"]+)"/i', $html, $matches)) {
        return $matches[1];
    }
    if (preg_match('/name="csrf-token"\s+content="([^"]+)"/i', $html, $matches)) {
        return $matches[1];
    }
    return '';
}

function clearCookies() {
    global $cookieFile;
    if (file_exists($cookieFile)) {
        unlink($cookieFile);
    }
}

echo "========================================================================================\n";
echo "       KIEM THU HE THONG SMARTROOM & RENTY REVIEW (END-TO-END HTTP INTEGRATION)\n";
echo "========================================================================================\n\n";

$results = [];

function record($category, $name, $status, $expected, $dur, $notes = '') {
    global $results;
    $passed = in_array($status, (array)$expected, true);
    $results[] = [
        'category' => $category,
        'name' => $name,
        'status' => $status,
        'passed' => $passed,
        'dur' => $dur,
        'notes' => $notes
    ];
    $tag = $passed ? "[PASS]" : "[FAIL]";
    $expStr = is_array($expected) ? implode('/', $expected) : $expected;
    echo sprintf("%s [%3d] %-30s | %-38s (%sms) %s\n", $tag, $status, $category, $name, $dur, $notes);
}

// -------------------------------------------------------------
// 1. PHAN HE PUBLIC / RENTY REVIEW
// -------------------------------------------------------------
clearCookies();
$r = makeRequest('GET', '/renty');
record('Public Portal', 'Trang chu /renty', $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/api/renty/rooms');
record('Public Portal', 'API danh sach phong /api/renty/rooms', $r['status'], 200, $r['dur']);
$roomsData = json_decode($r['body'], true);
$firstRoomId = $roomsData['data'][0]['id'] ?? 1;

$r = makeRequest('GET', '/api/renty/rooms/smart-search?keyword=Cầu+Giấy');
record('Public Portal', 'API SmartSearch theo khu vuc', $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/renty/room/' . $firstRoomId);
record('Public Portal', 'Chi tiet phong #' . $firstRoomId, $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/renty/room-3d');
record('Public Portal', 'Xem phong 3D truc quan', $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/login');
record('Auth Guest', 'Trang dang nhap /login', $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/landlord/register');
record('Auth Guest', 'Trang Onboarding Chu tro moi', $r['status'], 200, $r['dur']);

// -------------------------------------------------------------
// 2. PHAN HE SUPERADMIN
// -------------------------------------------------------------
echo "\n--- Dang nhap voi tai khoan Superadmin (superadmin) ---\n";
clearCookies();
$loginPage = makeRequest('GET', '/login');
$csrf = extractCsrf($loginPage['body']);

$loginResp = makeRequest('POST', '/login', [
    '_token' => $csrf,
    'username' => 'superadmin',
    'password' => 'password'
]);
record('Auth Superadmin', 'Xac thuc dang nhap Superadmin', $loginResp['status'], [302, 200], $loginResp['dur']);

$r = makeRequest('GET', '/list');
record('Superadmin', 'Quan ly nguoi dung & Phan quyen /list', $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/admin/verifications');
record('Superadmin', 'Kiem duyet ho so KYC /admin/verifications', $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/admin/audit-logs');
record('Superadmin', 'Nhat ky kiem toan /admin/audit-logs', $r['status'], 200, $r['dur']);

// -------------------------------------------------------------
// 3. PHAN HE CHU TRO (SMARTROOM ADMIN)
// -------------------------------------------------------------
echo "\n--- Dang nhap voi tai khoan Chu tro (demo-landlord-1) ---\n";
clearCookies();
$loginPage = makeRequest('GET', '/login');
$csrf = extractCsrf($loginPage['body']);

$loginResp = makeRequest('POST', '/login', [
    '_token' => $csrf,
    'username' => 'demo-landlord-1',
    'password' => 'password'
]);
record('Auth Landlord', 'Xac thuc dang nhap Chu tro', $loginResp['status'], [302, 200], $loginResp['dur']);

$r = makeRequest('GET', '/smartroom/admin');
record('Landlord Admin', 'Dashboard Overview /smartroom/admin', $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/smartroom/admin/rooms');
record('Landlord Admin', 'Quan ly Phong & Ma tran phong', $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/smartroom/admin/buildings');
record('Landlord Admin', 'Quan ly Co so luu tru (Buildings)', $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/smartroom/admin/equipment');
record('Landlord Admin', 'Quan ly Trang thiet bi /equipment', $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/smartroom/admin/reports');
record('Landlord Admin', 'So quy thu chi /reports', $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/smartroom/admin/payments');
record('Landlord Admin', 'Quan ly Thanh toan & VietQR /payments', $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/smartroom/admin/activity-logs');
record('Landlord Admin', 'Nhat ky thao tac quan tri', $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/smartroom/admin/iot/summary');
record('Landlord Admin', 'Tong quan IoT Smart Metering', $r['status'], 200, $r['dur']);

$r = makeRequest('GET', '/api/revenue-breakdown');
record('Landlord Admin', 'API Co cau Doanh thu /revenue-breakdown', $r['status'], 200, $r['dur']);

// -------------------------------------------------------------
// 4. PHAN HE CU DAN (RESIDENT PORTAL)
// -------------------------------------------------------------
echo "\n--- Dang nhap voi tai khoan Cu dan (demo-resident-101-1) ---\n";
clearCookies();
$loginPage = makeRequest('GET', '/login');
$csrf = extractCsrf($loginPage['body']);

$loginResp = makeRequest('POST', '/login', [
    '_token' => $csrf,
    'username' => 'demo-resident-101-1',
    'password' => 'password'
]);
record('Auth Resident', 'Xac thuc dang nhap Cu dan', $loginResp['status'], [302, 200], $loginResp['dur']);

$r = makeRequest('GET', '/smartroom/resident');
record('Resident Portal', 'Cong dich vu Cu dan /smartroom/resident', $r['status'], 200, $r['dur']);

// -------------------------------------------------------------
// 5. PHAN HE BUONG PHONG & HOUSEKEEPING
// -------------------------------------------------------------
echo "\n--- Kiem tra phan he Buong phong (Housekeeping) ---\n";
$r = makeRequest('GET', '/smartroom/housekeeping');
record('Housekeeping', 'Giao dien Buong phong di dong', $r['status'], 200, $r['dur']);

// -------------------------------------------------------------
// 6. IOT SMART METERING TELEMETRY
// -------------------------------------------------------------
echo "\n--- Kiem tra Nap du lieu IoT Telemetry ---\n";
$iotJson = json_encode([
    'meter_serial' => 'CT-ELEC-TEST-01',
    'meter_type' => 'electricity',
    'reading_kwh' => 156.8,
    'current_power_w' => 1250,
    'voltage_v' => 220.5
]);
$r = makeRequest('POST', '/api/v1/iot/telemetry', $iotJson, false);
record('IoT Telemetry', 'API nap xung dien /api/v1/iot/telemetry', $r['status'], 200, $r['dur']);

// -------------------------------------------------------------
// TONG KET
// -------------------------------------------------------------
echo "\n========================================================================================\n";
$total = count($results);
$passed = count(array_filter($results, fn($i) => $i['passed']));
$failed = $total - $passed;
$pct = round(($passed / $total) * 100, 1);
echo sprintf("TONG KET KET QUA KIEM THU: %d/%d PASS (%.1f%%) | THAT BAI: %d\n", $passed, $total, $pct, $failed);
echo "========================================================================================\n";
