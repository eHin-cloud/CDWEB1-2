/**
 * Script seed-admin.js: Tạo tài khoản Admin hệ thống thủ công qua Terminal
 * Cách chạy: node seed-admin.js
 */

const readline = require('readline');
const { execSync } = require('child_process');

const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

function question(query) {
    return new Promise(resolve => rl.question(query, resolve));
}

async function main() {
    console.log("=================================================");
    console.log("   KHỞI TẠO TÀI KHOẢN ADMIN HỆ THỐNG (TERMINAL)  ");
    console.log("=================================================");

    try {
        const name = (await question("Nhập họ và tên Admin [System Administrator]: ")).trim() || "System Administrator";
        const email = (await question("Nhập email Admin: ")).trim();

        if (!email || !email.includes('@')) {
            console.error("❌ Email không hợp lệ!");
            rl.close();
            process.exit(1);
        }

        const username = (await question(`Nhập tên đăng nhập [admin_${Date.now().toString().slice(-4)}]: `)).trim() || `admin_${Date.now().toString().slice(-4)}`;
        const password = (await question("Nhập mật khẩu (tối thiểu 6 ký tự): ")).trim();

        if (!password || password.length < 6) {
            console.error("❌ Mật khẩu phải có ít nhất 6 ký tự!");
            rl.close();
            process.exit(1);
        }

        const phone = (await question("Nhập số điện thoại [0909999999]: ")).trim() || "0909999999";

        console.log("\n⏳ Đang tiến hành tạo tài khoản admin...");

        // Gọi lệnh artisan make:admin được bảo mật và mã hóa chuẩn bcrypt
        const cmd = `php artisan make:admin --name="${name.replace(/"/g, '\\"')}" --username="${username}" --email="${email}" --password="${password.replace(/"/g, '\\"')}" --phone="${phone}"`;
        const output = execSync(cmd, { encoding: 'utf-8' });

        console.log(output);
        console.log("🎉 Hoàn tất tạo tài khoản quản trị viên!");
        console.log("👉 Đăng nhập tại: http://127.0.0.1:8000/admin/login.html");
    } catch (error) {
        console.error("❌ Lỗi khi tạo admin:", error.message);
    } finally {
        rl.close();
    }
}

main();
