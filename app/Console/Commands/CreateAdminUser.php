<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {--email= : Email quản trị viên} {--password= : Mật khẩu} {--name= : Họ và tên} {--username= : Tên đăng nhập} {--phone= : Số điện thoại}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo tài khoản Quản trị viên hệ thống (Admin) an toàn qua terminal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("==========================================");
        $this->info("  KHỞI TẠO TÀI KHOẢN ADMIN HỆ THỐNG");
        $this->info("==========================================");

        $name = $this->option('name') ?: $this->ask('Nhập họ và tên Admin', 'Quản Trị Viên Hệ Thống');
        $username = $this->option('username') ?: $this->ask('Nhập tên đăng nhập', 'admin_' . time());

        // Kiểm tra username đã tồn tại chưa
        if (User::where('username', $username)->exists()) {
            $this->error("Tên đăng nhập '{$username}' đã tồn tại!");
            return Command::FAILURE;
        }

        $email = $this->option('email') ?: $this->ask('Nhập email quản trị viên');
        while (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error("Email không hợp lệ. Vui lòng nhập lại!");
            $email = $this->ask('Nhập email quản trị viên');
        }

        if (User::where('email', $email)->exists()) {
            $this->error("Email '{$email}' đã tồn tại trong hệ thống!");
            return Command::FAILURE;
        }

        $password = $this->option('password') ?: $this->secret('Nhập mật khẩu (tối thiểu 6 ký tự)');
        while (strlen($password) < 6) {
            $this->error("Mật khẩu phải có ít nhất 6 ký tự!");
            $password = $this->secret('Nhập lại mật khẩu');
        }

        $phone = $this->option('phone') ?: $this->ask('Nhập số điện thoại (tùy chọn)', '0901234567');

        $adminRole = Role::where('slug', 'admin')->first();
        $adminRoleId = $adminRole ? $adminRole->id : null;

        $user = User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password),
            'role' => 'admin',
            'role_id' => $adminRoleId,
            'status' => 'active',
        ]);

        $this->newLine();
        $this->info("✅ Đã tạo tài khoản Admin thành công!");
        $this->line(" - ID: <comment>{$user->id}</comment>");
        $this->line(" - Tên: <comment>{$user->name}</comment>");
        $this->line(" - Username: <comment>{$user->username}</comment>");
        $this->line(" - Email: <comment>{$user->email}</comment>");
        $this->line(" - Vai trò: <fg=green>admin</>");
        $this->line(" - Trạng thái: <fg=green>active</>");
        $this->info("Bạn có thể dùng tài khoản này để đăng nhập vào /admin/login.html");

        return Command::SUCCESS;
    }
}
