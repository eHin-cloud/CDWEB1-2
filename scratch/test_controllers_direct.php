<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$request = \Illuminate\Http\Request::create('/');
app()->instance('request', $request);
\Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag());

echo "========================================================================================\n";
echo "      KIEM TRA TRUC TIEP CONTROLLER & VIEW RENDERING CHO CAC VAI TRO (RBAC)\n";
echo "========================================================================================\n\n";

$superadmin = \App\Models\User::where('username', 'superadmin')->first();
$landlord = \App\Models\User::where('username', 'demo-landlord-1')->first();
$residentUser = \App\Models\User::where('username', 'demo-resident-101-1')->first();

function testControllerAction($name, $user, $callback) {
    echo sprintf("[TEST] %-55s ... ", $name);
    if ($user) {
        \Illuminate\Support\Facades\Auth::setUser($user);
    } else {
        \Illuminate\Support\Facades\Auth::logout();
    }
    
    $start = microtime(true);
    try {
        $result = $callback($user);
        $dur = round((microtime(true) - $start) * 1000, 1);
        if ($result instanceof \Illuminate\View\View) {
            $rendered = $result->render();
            echo sprintf("PASS (View rendered: %d bytes - %sms)\n", strlen($rendered), $dur);
            return true;
        } elseif ($result instanceof \Illuminate\Http\JsonResponse) {
            echo sprintf("PASS (JSON response - %sms)\n", $dur);
            return true;
        } elseif ($result instanceof \Illuminate\Http\RedirectResponse) {
            echo sprintf("PASS (Redirect: %s - %sms)\n", $result->getTargetUrl(), $dur);
            return true;
        } else {
            echo sprintf("PASS (%sms)\n", $dur);
            return true;
        }
    } catch (\Throwable $e) {
        $dur = round((microtime(true) - $start) * 1000, 1);
        echo sprintf("FAIL (%sms)\n       -> Loi: %s (Tai %s:%d)\n", $dur, $e->getMessage(), basename($e->getFile()), $e->getLine());
        return false;
    }
}

// 1. Phân hệ Superadmin
echo "--- 1. PHAN HE SUPERADMIN ---\n";
testControllerAction('Superadmin: Quan ly nguoi dung (listUser)', $superadmin, function($u) {
    $ctrl = app(\App\Http\Controllers\CrudUserController::class);
    return $ctrl->listUser();
});

testControllerAction('Superadmin: Kiem duyet ho so KYC (verifications)', $superadmin, function($u) {
    $ctrl = app(\App\Http\Controllers\AdminVerificationController::class);
    return $ctrl->index(new \Illuminate\Http\Request());
});

testControllerAction('Superadmin: Nhat ky kiem toan (auditLogs)', $superadmin, function($u) {
    $ctrl = app(\App\Http\Controllers\AdminVerificationController::class);
    return $ctrl->auditLogs(new \Illuminate\Http\Request());
});

// 2. Phân hệ Chủ trọ (SmartRoom Admin)
echo "\n--- 2. PHAN HE CHU TRO (SMARTROOM ADMIN) ---\n";
testControllerAction('Chu tro: Dashboard Overview (AdminDashboardController)', $landlord, function($u) {
    $ctrl = app(\App\Http\Controllers\AdminDashboardController::class);
    return $ctrl->index(new \Illuminate\Http\Request());
});

testControllerAction('Chu tro: Danh sach co so luu tru (BuildingController)', $landlord, function($u) {
    $ctrl = app(\App\Http\Controllers\BuildingController::class);
    return $ctrl->index(new \Illuminate\Http\Request());
});

testControllerAction('Chu tro: Danh sach phong & Ma tran (RoomController)', $landlord, function($u) {
    $ctrl = app(\App\Http\Controllers\RoomController::class);
    return $ctrl->index(new \Illuminate\Http\Request());
});

testControllerAction('Chu tro: Danh muc trang thiet bi (EquipmentController)', $landlord, function($u) {
    $ctrl = app(\App\Http\Controllers\EquipmentController::class);
    return $ctrl->index(new \Illuminate\Http\Request());
});

testControllerAction('Chu tro: So quy thu chi (ReportController)', $landlord, function($u) {
    $ctrl = app(\App\Http\Controllers\ReportController::class);
    return $ctrl->index(new \Illuminate\Http\Request());
});

testControllerAction('Chu tro: Quan ly thanh toan & VietQR (PaymentController)', $landlord, function($u) {
    $ctrl = app(\App\Http\Controllers\PaymentController::class);
    return $ctrl->index(new \Illuminate\Http\Request());
});

testControllerAction('Chu tro: Nhat ky thao tac quan tri (AdminActivityLog)', $landlord, function($u) {
    $ctrl = app(\App\Http\Controllers\AdminActivityLogController::class);
    return $ctrl->index(new \Illuminate\Http\Request());
});

testControllerAction('Chu tro: IoT Smart Metering Facility Summary', $landlord, function($u) {
    $ctrl = app(\App\Http\Controllers\IotMeteringController::class);
    return $ctrl->facilitySummary(new \Illuminate\Http\Request());
});

// 3. Phân hệ Cư dân
echo "\n--- 3. PHAN HE CU DAN (RESIDENT PORTAL) ---\n";
testControllerAction('Cu dan: Cong thong tin cu dan (ResidentPortalController)', $residentUser, function($u) {
    $ctrl = app(\App\Http\Controllers\ResidentPortalController::class);
    return $ctrl->index();
});

// 4. Phân hệ Buồng phòng (Housekeeping)
echo "\n--- 4. PHAN HE BUONG PHONG (HOUSEKEEPING) ---\n";
testControllerAction('Buong phong: Giao dien di dong (HousekeepingController)', $landlord, function($u) {
    $ctrl = app(\App\Http\Controllers\HousekeepingController::class);
    return $ctrl->index(new \Illuminate\Http\Request());
});

// 5. Trợ lý ảo AI Chatbot (RAG)
echo "\n--- 5. TRO LY AO AI CHATBOT (RAG / GEMINI) ---\n";
testControllerAction('AI Chatbot: Hoi dap thue phong tu nhien', null, function($u) {
    $ctrl = app(\App\Http\Controllers\ChatbotController::class);
    $req = new \Illuminate\Http\Request();
    $req->replace(['prompt' => 'Có phòng nào ở Cầu Giấy dưới 3 triệu không?']);
    return $ctrl->chat($req);
});

// 6. AI Dashboard Insight
testControllerAction('AI SmartRoom: Phan tich Dashboard Insight', $landlord, function($u) {
    $ctrl = app(\App\Http\Controllers\AdminDashboardController::class);
    $service = app(\App\Services\AiManagementService::class);
    return $ctrl->aiDashboardInsight($service);
});

echo "\n========================================================================================\n";
echo "              HOAN TAT KIEM TRA TRUC TIEP CONTROLLERS & VIEWS\n";
echo "========================================================================================\n";
