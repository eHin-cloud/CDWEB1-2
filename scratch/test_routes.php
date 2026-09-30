<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$endpoints = [
    // 1. Phân hệ Public / Renty Portal
    ['GET', '/renty', 'Trang chu Renty Portal', 200],
    ['GET', '/api/renty/rooms', 'API danh sach phong', 200],
    ['GET', '/api/renty/rooms/smart-search?keyword=phong', 'API tim kiem thong minh SmartSearch', 200],
    ['GET', '/renty/room-3d', 'Xem phong 3D truc quan', 200],
    ['GET', '/renty/room/1', 'Chi tiet phong luu tru', 200],
    
    // 2. Phân hệ Xác thực & Onboarding
    ['GET', '/login', 'Trang dang nhap', 200],
    ['GET', '/create', 'Trang dang ky nguoi dung', 200],
    ['GET', '/landlord/register', 'Quy trinh Onboarding Chu tro moi', 200],
    
    // 3. Phân hệ Cổng cư dân & Khách lưu trú
    ['GET', '/smartroom/resident', 'Cong dich vu Cu dan (Resident Portal)', 200],
    ['GET', '/smartroom/contract/1/sign', 'Giao dien ky so HD online Canvas', 200],
    
    // 4. Phân hệ Buồng phòng & Khách sạn (Chưa đăng nhập -> redirect 302 login)
    ['GET', '/smartroom/housekeeping', 'Giao dien Buong phong di dong (Housekeeping)', 302],
    
    // 5. Phân hệ Quản trị Chủ trọ (Chưa đăng nhập -> redirect 302 login)
    ['GET', '/smartroom/admin', 'Dashboard Chu tro (Overview)', 302],
    ['GET', '/smartroom/admin/buildings', 'Quan ly Co so luu tru (Buildings)', 302],
    ['GET', '/smartroom/admin/rooms', 'Quan ly Phong va Ma tran phong', 302],
    ['GET', '/smartroom/admin/equipment', 'Quan ly Trang thiet bi', 302],
    ['GET', '/smartroom/admin/reports', 'So quy thu chi (Reports)', 302],
    ['GET', '/smartroom/admin/payments', 'Quan ly Thanh toan va VietQR', 302],
    
    // 6. Phân hệ Superadmin (Chưa đăng nhập -> redirect 302 login)
    ['GET', '/admin/verifications', 'Kiem duyet ho so KYC/Premium', 302],
    ['GET', '/admin/audit-logs', 'Nhat ky kiem toan Audit Logs', 302],
    ['GET', '/list', 'Quan ly nguoi dung va Phan quyen RBAC', 302]
];

echo "========================================================================================\n";
echo "           KIEM TRA HOAT DONG CAC ROUTE NGHIEP VU (UNAUTHENTICATED GUEST)\n";
echo "========================================================================================\n";

$passCount = 0;
$failCount = 0;

foreach ($endpoints as $item) {
    list($method, $uri, $name, $expectedStatus) = $item;
    $req = Illuminate\Http\Request::create($uri, $method);
    $start = microtime(true);
    try {
        $resp = $kernel->handle($req);
        $status = $resp->getStatusCode();
        $dur = round((microtime(true) - $start) * 1000, 1);
        $isOk = ($status === $expectedStatus);
        if ($isOk) {
            $passCount++;
            $tag = "[PASS]";
        } else {
            $failCount++;
            $tag = "[FAIL]";
        }
        echo sprintf("%s [%3d] %-6s %-45s (%s - %sms)\n", $tag, $status, $method, $uri, $name, $dur);
    } catch (\Throwable $e) {
        $failCount++;
        echo sprintf("[ERR ] [---] %-6s %-45s (%s: %s)\n", $method, $uri, $name, $e->getMessage());
    }
}

echo "----------------------------------------------------------------------------------------\n";
echo "TONG KET UNATHENTICATED: $passCount PASS / " . ($passCount + $failCount) . " TOTAL\n\n";
