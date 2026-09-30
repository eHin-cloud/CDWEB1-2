<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Test trực tiếp logic controller authUser
$controller = $app->make(\App\Http\Controllers\CrudUserController::class);

$credentials = [
    'superadmin' => 'superadmin',
    'landlord'   => 'demo-landlord-1',
    'resident'   => 'demo-resident-101-1',
];

echo "========================================================================================\n";
echo "       KIEM TRA XAC THUC NGUOI DUNG & DIEU HUONG DASHBOARD (AUTHENTICATION LOGIC)\n";
echo "========================================================================================\n\n";

foreach ($credentials as $roleName => $username) {
    $user = \App\Models\User::where('username', $username)->first();
    if (!$user) {
        echo "[FAIL] Khong tim thay user [$username] trong CSDL\n";
        continue;
    }
    
    // Kiểm tra mật khẩu Bcrypt
    $checkPass = \Illuminate\Support\Facades\Hash::check('password', $user->password);
    echo sprintf("[AUTH] User: %-20s | Role: %-15s | Password Hash Check: %s\n", 
        $username, 
        $user->roleSlug(), 
        $checkPass ? "PASS" : "FAIL"
    );
    
    // Test logic điều hướng homeRouteFor sau đăng nhập
    $route = match (true) {
        $user->isAdmin() => 'user.list (URL: /list)',
        $user->isHousekeeper() => 'admin.housekeeping.index (URL: /smartroom/housekeeping)',
        $user->canAccessLandlordDashboard() => 'smartroom.admin (URL: /smartroom/admin)',
        $user->isResident() => 'smartroom.resident (URL: /smartroom/resident)',
        default => 'renty.user (URL: /renty)'
    };
    echo "       -> Dieu huong sau dang nhap: $route\n";
}

echo "\n========================================================================================\n";
