<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$users = ['admin', 'superadmin', 'chutro1', 'buongphong1_2'];
foreach ($users as $u) {
    $user = User::where('username', $u)->first();
    if ($user) {
        $check123 = Hash::check('123456', $user->password) ? 'YES' : 'NO';
        $checkPass = Hash::check('password', $user->password) ? 'YES' : 'NO';
        $checkAdmin = Hash::check('admin123', $user->password) ? 'YES' : 'NO';
        echo "User: $u | 123456: $check123 | password: $checkPass | admin123: $checkAdmin\n";
    } else {
        echo "User: $u NOT FOUND\n";
    }
}
