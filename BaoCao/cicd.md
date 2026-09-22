# Báo Cáo Thiết Lập Hệ Thống CI/CD (GitHub Actions)

## 1. Mục tiêu
Thiết lập hệ thống **Tự động hóa Tích hợp liên tục và Kiểm thử chất lượng mã nguồn (CI/CD)** chuẩn công nghiệp cho đề tài **SmartRoom & Renty (Chuyên đề phát triển Web 1)**.

---

## 2. Các quy trình CI/CD đã được cấu hình

### A. Pipeline Laravel Core (`.github/workflows/laravel-ci.yml`)
Kích hoạt tự động khi có `push` hoặc `pull_request` vào `main`, `CI/CD`, hoặc các nhánh thành viên (`AnhQuy/**`, `Hien/**`, `VinhEm/**`).
1. **Job 1: 🎨 Kiểm tra chuẩn Code (Laravel Pint)**
   - Kiểm tra định dạng code PHP theo chuẩn PSR-12.
   - Cảnh báo các file cần format mà không chặn gián đoạn tiến độ kiểm thử logic.
2. **Job 2: 🧪 Chạy bộ kiểm thử tự động (PHPUnit Suite)**
   - Cài đặt môi trường PHP 8.3 với đầy đủ extensions (`pdo_sqlite`, `bcmath`, `intl`, `gd`, `zip`, v.v.).
   - Cache tự động thư mục `vendor/` theo hash của `composer.lock`.
   - Chạy toàn bộ **64/64 bài kiểm thử tự động** (Auth, IoT Smart Metering, Room Matrix, Billing VietQR, AI OCR, Hospitality Folio, v.v.) trên SQLite in-memory siêu tốc.
3. **Job 3: ⚡ Biên dịch tài nguyên giao diện (Vite & Tailwind)**
   - Cài đặt môi trường Node.js v22 và cache `npm`.
   - Biên dịch tự động toàn bộ CSS và JavaScript qua `npm run build`.
   - Xác nhận file bundle `manifest.json` được sinh ra an toàn.

### B. Pipeline Docker Container (`.github/workflows/docker-ci.yml`)
Kích hoạt tự động khi có thay đổi trong `Dockerfile`, thư mục `docker/`, hoặc `docker-compose.yml`.
1. **Job: 🐳 Build & Kiểm tra Docker Container**
   - Thiết lập Docker Buildx trên runner Ubuntu.
   - Tự động chạy `docker build` kiểm tra toàn bộ các layer và dependencies trong container.
   - Khởi chạy thử container và xác nhận các dịch vụ PHP CLI và Node.js chạy bình thường.

---

## 3. Tối ưu bộ kiểm thử Unit/Feature Tests
- Đã chuẩn hóa class [`SmartSearchApiTest.php`](file:///d:/smartgit/CDWEB1-2/tests/Feature/SmartSearchApiTest.php) bổ sung `use RefreshDatabase;` và hàm `setUp()` tạo dữ liệu mẫu, đảm bảo chạy độc lập hoàn hảo trên SQLite in-memory.
- Cập nhật [`ExampleTest.php`](file:///d:/smartgit/CDWEB1-2/tests/Feature/ExampleTest.php) kiểm tra chính xác hành vi redirect 302 về trang chủ `/renty`.
- **Kết quả nghiệm thu tại máy local:** `Tests: 64, Assertions: 290, 100% Passed` chỉ trong 6.9 giây.

---

## 4. Trạng thái nhánh Git
- Nhánh: **`CI/CD`**
- Đã commit và đẩy thành công lên GitHub Remote (`origin/CI/CD`).
- GitHub Actions đã sẵn sàng tự động bắt đầu các job kiểm thử trên GitHub.
