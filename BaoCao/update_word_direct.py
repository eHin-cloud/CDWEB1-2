# -*- coding: utf-8 -*-
"""
Script chỉnh sửa trực tiếp vào file Word: BaoCao_NhomA (1).docx
1. Cập nhật Bảng 2 phân chia công việc theo 38 nhánh Git và task thực tế.
2. Nâng cấp 28 module chức năng trong Mục IV thành Đặc Tả Kỹ Thuật Chuẩn Hóa:
   - Header kỹ thuật: Mã chức năng, Git Branch, Controller & Method, Endpoint & Middleware.
   - Bảng Input Specification (Validation Rules chi tiết từng field).
   - Business Logic Flow (Thuật toán các bước tuần tự).
   - Database Operation (Models, Tables, Columns, Locking).
   - Output Specification (Data Contract JSON / Redirect Toast).
   - Giữ nguyên 100% hình ảnh minh họa, wireframe và các bảng kịch bản lỗi hiện có.
"""

import sys
from pathlib import Path
import docx
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from data_specs import TABLE_2_ROWS

if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

docx_file = Path("BaoCao_NhomA (1).docx").resolve()
doc = docx.Document(str(docx_file))

print("1. Đang cập nhật Bảng 2 (Phân chia công việc theo Git Branch)...")
t2 = doc.tables[3]

# Xóa các dòng cũ trừ header
while len(t2.rows) > 1:
    tr = t2.rows[-1]._tr
    tr.getparent().remove(tr)

# Cập nhật header nếu cần
header_cells = t2.rows[0].cells
header_titles = ["Họ và tên", "STT", "Nội Dung Công Việc & Mã CN [Git Branch]", "Ngày Bắt Đầu", "Hạn Hoàn Thành", "Tiến Độ", "Ghi Chú"]
for i, title in enumerate(header_titles):
    header_cells[i].text = title

# Thêm 38 dòng mới
for row_data in TABLE_2_ROWS:
    new_tr = t2.add_row()
    for col_idx, text_val in enumerate(row_data):
        new_tr.cells[col_idx].text = text_val

print(f"-> Đã cập nhật Bảng 2 thành công với {len(t2.rows)} dòng (1 header + 38 công việc)!")

print("2. Đang chuẩn bị dữ liệu đặc tả kỹ thuật cho 28 module trong Mục IV...")

# Danh sách dữ liệu đặc tả cho 28 module
MODULE_SPECS = [
    # --- HIỀN ---
    {
        "search_key": "1. Khởi tạo kiến trúc dự án Laravel 11",
        "code": "FEAT-HIEN-01",
        "branch": "main, CI/CD, CauHinh",
        "assignee": "Nguyễn Thanh Hiền",
        "controller": "Phạm vi hạ tầng: docker-compose.yml, .github/workflows/*, database/migrations/*",
        "endpoint": "Môi trường Docker: PHP 8.3-fpm, Nginx, MySQL 8.0, Redis 7.2",
        "middleware": "N/A",
        "inputs": [
            ("APP_ENV", "String", "Có", "in:local,production", "Môi trường thực thi không hợp lệ."),
            ("DB_CONNECTION", "String", "Có", "in:mysql,sqlite", "Driver CSDL không được hỗ trợ."),
            ("DB_HOST", "String", "Có", "string", "Host MySQL không được để trống."),
            ("PII_ENCRYPTION_KEY", "String", "Có", "size:44|starts_with:base64:", "Khóa mã hóa PII AES-256-GCM không đúng chuẩn 256-bit."),
            ("BLIND_INDEX_KEY", "String", "Có", "string", "Khóa HMAC Blind Index không được để trống.")
        ],
        "logic": [
            "Khởi tạo hệ thống container độc lập (app, web, db, redis) qua docker-compose.",
            "Pipeline GitHub Actions tự động kiểm tra code style bằng Laravel Pint, chạy toàn bộ 64/64 Unit & Feature Tests qua PHPUnit.",
            "Thực thi tự động 35 bản ghi Migration thiết lập cấu trúc 35 bảng hệ thống với đầy đủ Foreign Keys và Composite Indexes."
        ],
        "db": "Tạo lập 35 bảng: tenants, roles, users, buildings, rooms, equipment, residents, contracts, bills...",
        "output": "Ứng dụng sẵn sàng tại http://localhost:8000 hoặc container port 8088; CI/CD trả về All checks passed (Xanh lá)."
    },
    {
        "search_key": "2. Xây dựng Middleware phân quyền truy cập đa tầng RBAC",
        "code": "FEAT-HIEN-02",
        "branch": "Hien/PhanQuyen",
        "assignee": "Nguyễn Thanh Hiền",
        "controller": "App\\Http\\Controllers\\CrudUserController@listUser, updateRole",
        "endpoint": "GET /list, POST /users/role",
        "middleware": "auth, role:admin, TenantScope",
        "inputs": [
            ("user_id", "Integer", "Có", "required|integer|exists:users,id", "Tài khoản người dùng không tồn tại."),
            ("role", "String", "Có", "required|in:admin,landlord,unverified_landlord,manager,resident,guest", "Vai trò được gán không hợp lệ.")
        ],
        "logic": [
            "Middleware RoleMiddleware chặn mọi truy cập trái phép cấp quyền; trả về 403 Forbidden nếu không đủ thẩm quyền.",
            "Global Scope TenantScope tự động áp dụng điều kiện where('tenant_id', auth()->user()->tenant_id) cho mọi query nghiệp vụ, ngăn chặn rò rỉ dữ liệu chéo.",
            "Admin cập nhật vai trò, ghi nhật ký truy vết vào bảng audit_logs; cấm admin duy nhất tự hạ quyền chính mình."
        ],
        "db": "users (cập nhật role, role_id), audit_logs (lưu vết phân quyền).",
        "output": "Redirect về user.list kèm session('success', 'Cập nhật vai trò người dùng thành công!')."
    },
    {
        "search_key": "3. Lập trình module Đăng ký & Đăng nhập truyền thống",
        "code": "FEAT-HIEN-03",
        "branch": "Hien/Login_Sign",
        "assignee": "Nguyễn Thanh Hiền",
        "controller": "App\\Http\\Controllers\\CrudUserController@login, authUser",
        "endpoint": "GET /login, POST /login",
        "middleware": "guest, throttle:30,1",
        "inputs": [
            ("login", "String", "Có", "required|string", "Vui lòng nhập tên đăng nhập hoặc số điện thoại."),
            ("password", "String", "Có", "required|string|min:6", "Mật khẩu phải từ 6 ký tự trở lên.")
        ],
        "logic": [
            "Áp dụng Rate Limiting tối đa 30 req/phút; tự động khóa form 60 giây nếu nhập sai mật khẩu quá 5 lần liên tiếp.",
            "Tự động phát hiện SĐT (Regex 10 số): tính toán phone_blind_index = hash_hmac('sha256', phone, key) để tra cứu không cần giải mã DB.",
            "Xác thực mật khẩu bằng Hash::check(); phân luồng điều hướng: admin/landlord -> /smartroom/admin, resident -> /smartroom/resident."
        ],
        "db": "users (đọc username/phone_blind_index, cập nhật last_login_at), sessions.",
        "output": "Redirect về Dashboard tương ứng vai trò; nếu sai trả về HTTP 422 kèm lỗi hiển thị trên form."
    },
    {
        "search_key": "4. Tích hợp chuẩn xác thực không mật khẩu WebAuthn",
        "code": "FEAT-HIEN-04",
        "branch": "Hien/Login_Sign",
        "assignee": "Nguyễn Thanh Hiền",
        "controller": "Laragear\\WebAuthn\\Http\\Controllers\\WebAuthnLoginController, WebAuthnRegisterController",
        "endpoint": "POST /webauthn/login/options, POST /webauthn/login",
        "middleware": "web, throttle:10,1",
        "inputs": [
            ("id", "String", "Có", "required|string", "Credential ID không hợp lệ."),
            ("rawId", "String", "Có", "required|string", "Mã khóa gốc không hợp lệ."),
            ("response.clientDataJSON", "String", "Có", "required|string", "Thiếu clientDataJSON."),
            ("response.signature", "String", "Có", "required|string", "Chữ ký sinh trắc học không hợp lệ.")
        ],
        "logic": [
            "Server sinh Challenge ngẫu nhiên 32 bytes cryptographically secure, lưu vào session.",
            "Client gọi navigator.credentials.get(), kích hoạt cảm biến vân tay / FaceID / Windows Hello.",
            "Server xác thực chữ ký bất đối xứng bằng Public Key lưu trong webauthn_credentials, kiểm tra counter chống replay attack, đăng nhập tự động."
        ],
        "db": "webauthn_credentials (đọc Public Key, tăng counter), users.",
        "output": "HTTP 204 JSON {'status': 'ok', 'redirect': '/smartroom/admin'}."
    },
    {
        "search_key": "5. Hiện thực giải pháp mã hóa dữ liệu nhạy cảm PII",
        "code": "FEAT-HIEN-05",
        "branch": "Hien/PhanQuyen",
        "assignee": "Nguyễn Thanh Hiền",
        "controller": "App\\Services\\SecureDocumentService, App\\Http\\Controllers\\Api\\SensitiveDataController",
        "endpoint": "Áp dụng toàn hệ thống cho các trường phone, cccd, bank_account_number",
        "middleware": "auth",
        "inputs": [
            ("search_term", "String", "Có", "required|string|min:3", "Chuỗi tra cứu tối thiểu 3 ký tự."),
            ("field_type", "String", "Có", "required|in:phone,cccd", "Loại trường tra cứu không hợp lệ.")
        ],
        "logic": [
            "Dữ liệu gốc được mã hóa đối xứng quân sự AES-256-GCM với IV 96-bit ngẫu nhiên và Auth Tag 128-bit.",
            "Sinh Blind Index: HMAC-SHA256(lowercase(plaintext), BLIND_INDEX_KEY), lưu vào cột *_blind_index để tra cứu SQL chính xác mà không cần giải mã CSDL.",
            "Mặt nạ hóa dữ liệu hiển thị mặc định (0912****89, 07920100****); chỉ giải mã khi có xác thực cấp 2 và ghi nhật ký Audit Log."
        ],
        "db": "users, residents, resident_relatives, landlord_profiles (cột phone, cccd, *_blind_index).",
        "output": "Dữ liệu được bảo vệ tuyệt đối tuân thủ Nghị định 13/2023/NĐ-CP."
    },
    {
        "search_key": "6 & 7. Quản lý Hợp đồng thuê phòng & Ký số điện tử online",
        "code": "FEAT-HIEN-06 & FEAT-HIEN-07",
        "branch": "main",
        "assignee": "Nguyễn Thanh Hiền",
        "controller": "App\\Http\\Controllers\\AdminDashboardController@storeContract, signContract, sendOtpForContract, printContractPdf",
        "endpoint": "POST /smartroom/admin/contract, POST /smartroom/contract/{id}/sign, GET /smartroom/contract/{id}/pdf",
        "middleware": "auth (storeContract), web (signContract)",
        "inputs": [
            ("room_id", "Integer", "Có", "required|integer|exists:rooms,id", "Phòng được chọn không tồn tại."),
            ("deposit", "Numeric", "Có", "required|numeric|min:0", "Tiền cọc phải lớn hơn hoặc bằng 0 VNĐ."),
            ("start_date", "Date", "Có", "required|date", "Ngày bắt đầu không hợp lệ."),
            ("end_date", "Date", "Có", "required|date|after:start_date", "Ngày kết thúc phải sau ngày bắt đầu."),
            ("signature", "String", "Có (khi ký)", "required|starts_with:data:image/png;base64,", "Vui lòng vẽ chữ ký tay vào khung trước khi ký."),
            ("otp_code", "String", "Có (khi ký)", "required|digits:6", "Mã xác thực OTP gồm đúng 6 chữ số.")
        ],
        "logic": [
            "Khởi tạo hợp đồng trạng thái draft; tự động sinh mã hợp đồng duy nhất HD-{YYYY}-{room_number}-{random4}.",
            "Người thuê mở link ký số, đọc toàn văn điều khoản, bấm gửi mã OTP về SĐT.",
            "Cư dân vẽ chữ ký tay lên HTML5 Canvas, nhập OTP. Hệ thống lưu chữ ký Base64, chuyển hợp đồng sang active, chuyển phòng sang occupied (Đang ở).",
            "Xuất bản file PDF hợp đồng có đầy đủ chữ ký 2 bên qua DomPDF."
        ],
        "db": "contracts (signature, status, signed_at), rooms (chuyển status = occupied), otp_codes, bills (phiếu cọc).",
        "output": "JSON {'success': true, 'pdf_url': '/smartroom/contract/12/pdf'}; phòng chuyển sang Đang ở."
    },
    {
        "search_key": "8. Xây dựng quy trình Xác thực định danh chủ trọ lũy tiến",
        "code": "FEAT-HIEN-08",
        "branch": "main",
        "assignee": "Nguyễn Thanh Hiền",
        "controller": "App\\Http\\Controllers\\LandlordVerificationController@submitKyc, submitPremium",
        "endpoint": "POST /smartroom/admin/verification/kyc, POST /smartroom/admin/verification/premium",
        "middleware": "auth, admin, role:landlord",
        "inputs": [
            ("landlord_name", "String", "Có", "required|string|max:255", "Vui lòng nhập họ tên chủ cơ sở."),
            ("cccd_number", "String", "Có", "required|regex:/^[0-9]{12}$/", "Số CCCD phải gồm đúng 12 chữ số."),
            ("bank_name", "String", "Có", "required|string|max:100", "Vui lòng chọn ngân hàng thụ hưởng."),
            ("bank_account_number", "String", "Có", "required|string|max:50", "Số tài khoản ngân hàng không được để trống."),
            ("cccd_front_image", "File", "Có", "required|image|mimes:jpg,jpeg,png,webp|max:10240", "Ảnh mặt trước CCCD tối đa 10MB."),
            ("cccd_back_image", "File", "Có", "required|image|mimes:jpg,jpeg,png,webp|max:10240", "Ảnh mặt sau CCCD tối đa 10MB.")
        ],
        "logic": [
            "Lưu trữ ảnh CCCD và giấy phép PCCC vào thư mục bảo mật storage/app/secure_documents/ (không public).",
            "Mã hóa số CCCD và STK ngân hàng bằng AES-256-GCM trước khi lưu vào landlord_profiles.",
            "Tạo bản ghi yêu cầu thẩm định trong landlord_verification_requests (status = pending) gửi Admin."
        ],
        "db": "landlord_profiles, landlord_verification_requests, landlord_verification_documents.",
        "output": "Redirect về màn hình trạng thái KYC kèm toast xanh: 'Hồ sơ định danh đã được gửi phê duyệt thành công'."
    },
    {
        "search_key": "9. Xây dựng Bảng điều khiển kiểm duyệt hồ sơ chủ trọ",
        "code": "FEAT-HIEN-09 & FEAT-HIEN-10",
        "branch": "main",
        "assignee": "Nguyễn Thanh Hiền",
        "controller": "App\\Http\\Controllers\\AdminVerificationController@index, approve, reject, VerificationDocumentController@stream",
        "endpoint": "GET /admin/verifications, POST /admin/verifications/{id}/approve, POST /admin/verifications/{id}/reject",
        "middleware": "auth, role:admin",
        "inputs": [
            ("rejection_reason", "String", "Có (khi reject)", "required|string|min:10|max:1000", "Vui lòng nhập lý do từ chối cụ thể (tối thiểu 10 ký tự).")
        ],
        "logic": [
            "Superadmin xem tài liệu pháp lý qua Signed URL có thời hạn TTL 5 phút; ảnh stream ra được đóng dấu Watermark chìm chống rò rỉ.",
            "Phê duyệt (Approve): Nâng quyền chủ trọ lên landlord, mở cổng nhận tiền VietQR; nếu hồ sơ Premium thì cấp huy hiệu Tích Xanh (premium_verified).",
            "Từ chối (Reject): Lưu lý do từ chối, gửi thông báo hệ thống yêu cầu chủ trọ bổ sung lại giấy tờ hợp lệ."
        ],
        "db": "landlord_verification_requests, users (cập nhật role), tenants (cập nhật listing_badge), audit_logs.",
        "output": "Redirect về danh sách duyệt hồ sơ kèm flash message thông báo kết quả."
    },
    {
        "search_key": "10. Xây dựng hệ thống Nhật ký kiểm toán bất biến",
        "code": "FEAT-HIEN-11",
        "branch": "main",
        "assignee": "Nguyễn Thanh Hiền",
        "controller": "App\\Http\\Controllers\\AdminVerificationController@auditLogs, App\\Services\\AuditLogService",
        "endpoint": "GET /admin/audit-logs",
        "middleware": "auth, role:admin",
        "inputs": [
            ("action", "String", "Không", "nullable|string", "Hành động lọc không hợp lệ."),
            ("date_from", "Date", "Không", "nullable|date", "Ngày bắt đầu lọc không hợp lệ.")
        ],
        "logic": [
            "Bảng audit_logs được cài đặt Trigger CSDL chặn triệt để lệnh UPDATE và DELETE (Append-only).",
            "Mỗi bản ghi được băm liên hoàn chuỗi khối: row_hash = sha256(prev_hash + data), chống sửa xóa dữ liệu lịch sử.",
            "Hiển thị giao diện kiểm toán truy vết mọi hành vi truy cập dữ liệu cá nhân nhạy cảm."
        ],
        "db": "audit_logs (chỉ cho phép INSERT và SELECT, cấm UPDATE/DELETE).",
        "output": "View danh sách Audit Log toàn hệ thống kèm huy hiệu xác thực tính toàn vẹn (Integrity Verified)."
    },
    {
        "search_key": "11. Tích hợp Google Gemini AI tự động phân tích và sinh điều khoản",
        "code": "FEAT-HIEN-12",
        "branch": "main",
        "assignee": "Nguyễn Thanh Hiền",
        "controller": "App\\Http\\Controllers\\AdminDashboardController@aiContractTerms",
        "endpoint": "POST /smartroom/admin/ai/contract-terms",
        "middleware": "auth, admin, role:landlord",
        "inputs": [
            ("requirements", "String", "Có", "required|string|min:5|max:1000", "Vui lòng nhập ít nhất một yêu cầu quy định sinh hoạt (5 - 1000 ký tự).")
        ],
        "logic": [
            "Tiếp nhận yêu cầu sinh hoạt thực tế của chủ trọ (cho nuôi thú cưng, giữ xe điện, giờ đóng cổng...).",
            "Gửi Prompt pháp lý chuyên sâu đến mô hình gemini-2.5-flash tham chiếu Luật Nhà ở và Bộ luật Dân sự Việt Nam.",
            "Trả về văn bản điều khoản pháp lý hoàn chỉnh có cấu trúc: Quyền hạn, Nghĩa vụ và Chế tài vi phạm."
        ],
        "db": "Không ghi CSDL trực tiếp (kết quả được điền vào form tạo hợp đồng).",
        "output": "JSON {'success': true, 'terms': 'ĐIỀU KHOẢN VỀ AN NINH VÀ SINH HOẠT CHUNG...'}"
    },
    {
        "search_key": "12. Xây dựng quy trình Onboarding đăng ký nhanh cho chủ trọ",
        "code": "FEAT-HIEN-13",
        "branch": "Hien/Menu",
        "assignee": "Nguyễn Thanh Hiền",
        "controller": "App\\Http\\Controllers\\LandlordOnboardingController@create, store, verifyOtp",
        "endpoint": "GET /landlord/register, POST /landlord/register, POST /landlord/verify-otp",
        "middleware": "guest",
        "inputs": [
            ("name", "String", "Có", "required|string|max:255", "Vui lòng nhập họ tên chủ cơ sở."),
            ("phone", "String", "Có", "required|regex:/^(0[3|5|7|8|9])[0-9]{8}$/", "Số điện thoại di động Việt Nam không hợp lệ."),
            ("password", "String", "Có", "required|string|min:6", "Mật khẩu tối thiểu 6 ký tự."),
            ("facility_name", "String", "Có", "required|string|max:255", "Tên cơ sở lưu trú không được để trống."),
            ("facility_address", "String", "Có", "required|string|max:255", "Địa chỉ cơ sở lưu trú không được để trống."),
            ("total_floors", "Integer", "Có", "required|integer|min:1|max:100", "Số tầng phải từ 1 đến 100.")
        ],
        "logic": [
            "Quy trình Step-Wizard 3 bước: 1. Đăng ký tài khoản -> 2. Thiết lập cơ sở ban đầu -> 3. Kích hoạt OTP.",
            "Tự động tạo bản ghi trong tenants, users (role unverified_landlord), và buildings.",
            "Gửi mã OTP qua SMS/Zalo; xác thực thành công tự động đăng nhập và bàn giao quyền quản trị."
        ],
        "db": "tenants, users, buildings, otp_codes.",
        "output": "Redirect về /smartroom/admin sau khi hoàn tất thiết lập ban đầu chỉ trong 3 phút."
    },

    # --- ANH QUÝ ---
    {
        "search_key": "1. Lập trình module CRUD Quản lý Cơ sở lưu trú (Properties/Hotels)",
        "code": "FEAT-AQ-01",
        "branch": "AnhQuy/quan-ly-co-so-luu-tru",
        "assignee": "Nguyễn Anh Quý",
        "controller": "App\\Http\\Controllers\\BuildingController@index, create, store, edit, update, destroy",
        "endpoint": "GET /smartroom/admin/buildings, POST /smartroom/admin/buildings/store, POST /smartroom/admin/buildings/{id}/update, DELETE /smartroom/admin/buildings/{id}/delete",
        "middleware": "auth, admin, role:landlord",
        "inputs": [
            ("name", "String", "Có", "required|string|max:255", "Tên cơ sở lưu trú không được để trống."),
            ("address", "String", "Có", "required|string|max:255", "Địa chỉ cơ sở lưu trú không được để trống."),
            ("total_floors", "Integer", "Có", "required|integer|min:1|max:100", "Số tầng tối thiểu 1 và tối đa 100."),
            ("status", "String", "Có", "required|in:active,maintenance,inactive", "Trạng thái hoạt động không hợp lệ."),
            ("image", "File", "Không", "nullable|image|mimes:jpeg,png,jpg,webp|max:5120", "Ảnh đại diện tối đa 5MB.")
        ],
        "logic": [
            "Kiểm tra quyền Multi-tenancy: building->tenant_id === auth()->user()->tenant_id.",
            "Upload và lưu trữ ảnh đại diện tòa nhà vào storage/app/public/buildings/.",
            "Guard Check an toàn tuyệt đối khi Xóa: Nếu đếm Room::where('building_id', $id)->count() > 0 thì CHẶN XÓA (HTTP 422); chỉ cho phép SoftDelete khi không còn phòng trực thuộc."
        ],
        "db": "buildings (tenant_id, name, address, total_floors, status, image, amenities, deleted_at SoftDeletes).",
        "output": "Redirect về admin.buildings.index kèm session('success', 'Lưu thông tin cơ sở lưu trú thành công!')."
    },
    {
        "search_key": "2. Lập trình module CRUD Quản lý Phòng lưu trú (Rooms)",
        "code": "FEAT-AQ-02",
        "branch": "AnhQuy/quan-ly-phong",
        "assignee": "Nguyễn Anh Quý",
        "controller": "App\\Http\\Controllers\\RoomController@index, create, store, edit, update, destroy",
        "endpoint": "GET /smartroom/admin/rooms, POST /smartroom/admin/rooms/store, POST /smartroom/admin/rooms/{id}/update, DELETE /smartroom/admin/rooms/{id}/delete",
        "middleware": "auth, admin, role:landlord",
        "inputs": [
            ("building_id", "Integer", "Có", "required|integer|exists:buildings,id", "Vui lòng chọn cơ sở lưu trú hợp lệ."),
            ("room_number", "String", "Có", "required|string|max:50", "Số phòng không được để trống."),
            ("price", "Numeric", "Có", "required|numeric|min:0", "Giá thuê phòng phải lớn hơn hoặc bằng 0."),
            ("area", "Integer", "Có", "required|integer|min:5|max:500", "Diện tích phòng từ 5m² đến 500m²."),
            ("deposit", "Numeric", "Có", "required|numeric|min:0", "Tiền cọc giữ phòng không được là số âm."),
            ("room_type", "String", "Có", "required|in:standard,deluxe,vip,studio", "Hạng phòng phải là standard, deluxe, vip hoặc studio."),
            ("rental_type", "String", "Có", "required|in:month,day,hour", "Hình thức thuê phải là month, day hoặc hour."),
            ("electric_meter_serial", "String", "Không", "nullable|string|max:100", "Số SX công tơ điện tối đa 100 ký tự."),
            ("water_meter_serial", "String", "Không", "nullable|string|max:100", "Số SX đồng hồ nước tối đa 100 ký tự."),
            ("video", "File", "Không", "nullable|file|mimes:mp4,mov,webm|max:30720", "Video không gian phòng tối đa 30MB.")
        ],
        "logic": [
            "Kiểm tra trùng lặp số phòng trong cùng một cơ sở lưu trú.",
            "Cơ chế Khóa lạc quan (Optimistic Locking): So sánh version gửi lên với version trong DB; nếu lệch ném HTTP 409 Conflict chống ghi đè dữ liệu đồng thời.",
            "Cấu hình Số SX công tơ điện nước phục vụ thuật toán AI Vision Bulk Match; upload tối đa 10 ảnh và 1 video phòng.",
            "Guard Check khi Xóa: Cấm xóa phòng đang có người ở (status = occupied)."
        ],
        "db": "rooms (tenant_id, building_id, room_number, price, area, deposit, room_type, rental_type, electric_meter_serial, water_meter_serial, version...).",
        "output": "Redirect về admin.rooms.index kèm toast thông báo thành công."
    },
    {
        "search_key": "3. Thiết kế & phát triển Sơ đồ ma trận phòng trực quan (Visual Room Matrix)",
        "code": "FEAT-AQ-03",
        "branch": "AnhQuy/ma-tran-phong",
        "assignee": "Nguyễn Anh Quý",
        "controller": "App\\Http\\Controllers\\RoomMatrixRealtimeController@updateStatus, stream, poll, HousekeepingController@updateStatus",
        "endpoint": "POST /smartroom/admin/rooms/{id}/quick-status, GET /smartroom/admin/rooms/matrix/stream",
        "middleware": "auth, admin",
        "inputs": [
            ("status", "String", "Có", "required|in:empty,occupied,cleaning,overdue,maintenance", "Trạng thái phòng cập nhật không hợp lệ.")
        ],
        "logic": [
            "Sơ đồ buồng phòng ma trận phân tầng trực quan theo mã màu chuẩn khách sạn: Xanh lá (Trống), Đỏ (Đang ở), Cam (Cần dọn - Cleaning), Vàng (Nợ cước), Xám (Bảo trì).",
            "Chặn chuyển thủ công sang occupied nếu chưa có Hợp đồng / Booking có hiệu lực.",
            "Phát sóng trạng thái thời gian thực qua Server-Sent Events (SSE) hoặc Laravel Reverb đồng bộ toàn bộ tab làm việc của lễ tân và buồng phòng."
        ],
        "db": "rooms (cập nhật status, tăng version).",
        "output": "JSON {'success': true, 'room_id': 15, 'new_status': 'empty', 'color_class': 'bg-emerald-500'}."
    },
    {
        "search_key": "4 & 5. Chốt số Điện - Nước định kỳ hàng tháng & AI Vision OCR",
        "code": "FEAT-AQ-04 & FEAT-AQ-05",
        "branch": "AnhQuy/chot-so-dien-nuoc-ai-ocr",
        "assignee": "Nguyễn Anh Quý",
        "controller": "App\\Http\\Controllers\\AdminDashboardController@storeUtility, storeUtilityBulk, aiOcrMeter, aiOcrMeterBulk",
        "endpoint": "POST /smartroom/admin/utility, POST /smartroom/admin/utility/bulk, POST /smartroom/admin/ai/ocr-meter-bulk",
        "middleware": "auth, admin, role:landlord",
        "inputs": [
            ("building_id", "Integer", "Có", "required|integer|exists:buildings,id", "Vui lòng chọn cơ sở lưu trú hợp lệ."),
            ("billing_month", "String", "Có", "required|regex:/^[0-9]{4}-(0[1-9]|1[0-2])$/", "Tháng tính tiền định dạng YYYY-MM."),
            ("new_electricity", "Integer", "Có", "required|integer|min:0", "Chỉ số điện mới phải là số nguyên dương."),
            ("new_water", "Integer", "Có", "required|integer|min:0", "Chỉ số nước mới phải là số nguyên dương."),
            ("images", "Array", "Có (khi quét bulk)", "required|array|min:1|max:30", "Số lượng ảnh công tơ tải lên từ 1 đến 30 ảnh.")
        ],
        "logic": [
            "Kiểm tra logic: new_electricity >= old_electricity; cảnh báo vàng nếu tiêu thụ tăng đột biến (> 1000 kWh).",
            "AI Vision Bulk Match: Google Gemini Vision API bóc tách đồng thời Số SX và Chỉ số tiêu thụ từ hàng loạt ảnh công tơ, tự động khớp với serial phòng trong CSDL.",
            "Tự động tính thành tiền điện, nước và đồng bộ sang hóa đơn tháng bills."
        ],
        "db": "utility_records, bills.",
        "output": "JSON kết quả đối soát khớp phòng tự động; lưu toàn bộ chỉ số các phòng chỉ với 1 click."
    },
    {
        "search_key": "6 & 7. Bộ máy tính toán cước tự động & Xuất hóa đơn tháng / Bảng kê Folio PDF",
        "code": "FEAT-AQ-06, FEAT-AQ-07 & FEAT-AQ-13",
        "branch": "AnhQuy/tinh-cuoc-hoa-don-vietqr, AnhQuy/PhanQuyen",
        "assignee": "Nguyễn Anh Quý",
        "controller": "App\\Services\\BillingEngine, AdminDashboardController@printUtility, HotelReceptionController@printFolio",
        "endpoint": "GET /smartroom/admin/utility/{id}/print, GET /smartroom/admin/hotel/folio/{bookingId}, GET /api/revenue-breakdown",
        "middleware": "auth, admin",
        "inputs": [
            ("id", "Integer (Route)", "Có", "exists:utility_records,id", "Bản ghi hóa đơn không tồn tại.")
        ],
        "logic": [
            "BillingEngine tự động tổng hợp: Tiền phòng + Tiền điện nước + Phí quản lý chung cư + Phụ phí tiêu thụ đồ uống minibar khách sạn.",
            "Tự động sinh mã VietQR động NAPAS247 chứa chính xác số tiền, STK ngân hàng chủ trọ và cú pháp chuyển khoản.",
            "Kết xuất phiếu tính tiền và Bảng kê Hotel Folio PDF chuẩn A4/A5 đứng qua DomPDF sẵn sàng in ấn."
        ],
        "db": "bills, utility_records, hotel_folio_items.",
        "output": "Stream file PDF MIME application/pdf có mã VietQR thanh toán nhanh."
    },
    {
        "search_key": "8. Quét nợ tự động và gửi tin nhắn nhắc tiền phòng",
        "code": "FEAT-AQ-08",
        "branch": "AnhQuy/nhac-no-zalo-sms",
        "assignee": "Nguyễn Anh Quý",
        "controller": "AdminDashboardController@autoRemindUtilities, App\\Services\\SmsZaloService",
        "endpoint": "POST /smartroom/admin/utility/auto-remind",
        "middleware": "auth, admin, role:landlord",
        "inputs": [
            ("channel", "String", "Không", "nullable|in:all,zalo,sms,telegram", "Kênh gửi tin nhắn không hợp lệ.")
        ],
        "logic": [
            "Quét toàn bộ hóa đơn trễ hạn thanh toán (due_date < now()).",
            "Tạo nội dung tin nhắn cá nhân hóa kèm đường link mở hóa đơn VietQR thanh toán nhanh.",
            "Gửi tự động qua Zalo ZNS / SMS Brandname / Bot Telegram; ghi nhật ký notification_logs chặn gửi lặp nhiều lần trong ngày."
        ],
        "db": "utility_records (status = sent), notification_logs.",
        "output": "JSON {'success': true, 'reminded_count': 8, 'message': 'Đã gửi tin nhắc nợ thành công!'}"
    },
    {
        "search_key": "9. Lập trình module Quản lý Trang thiết bị - Tài sản phòng trọ (Equipment)",
        "code": "FEAT-AQ-09",
        "branch": "AnhQuy/quan-ly-trang-thiet-bi",
        "assignee": "Nguyễn Anh Quý",
        "controller": "App\\Http\\Controllers\\EquipmentController@index, store, update, allocate, recover, destroy",
        "endpoint": "GET /smartroom/admin/equipment, POST /smartroom/admin/equipment/allocate, POST /smartroom/admin/equipment/recover",
        "middleware": "auth, admin",
        "inputs": [
            ("equipment_id", "Integer", "Có", "required|integer|exists:equipment,id", "Trang thiết bị không tồn tại."),
            ("room_id", "Integer", "Có", "required|integer|exists:rooms,id", "Phòng tiếp nhận thiết bị không hợp lệ."),
            ("quantity", "Integer", "Có", "required|integer|min:1", "Số lượng bàn giao tối thiểu là 1."),
            ("condition", "String", "Có", "required|string|max:100", "Vui lòng ghi rõ tình trạng thiết bị.")
        ],
        "logic": [
            "Kiểm tra tồn kho: Nếu số lượng yêu cầu bàn giao > equipment.quantity trong kho thì ném HTTP 422.",
            "Mở DB Transaction: Trừ tồn kho trang thiết bị, thêm bản ghi phân bổ vào bảng trung gian room_equipment.",
            "Quy trình thu hồi về kho khi trả phòng; cấm xóa danh mục thiết bị nếu đang được phân bổ trong các phòng."
        ],
        "db": "equipment (cập nhật quantity tồn kho), room_equipment (quản lý phân bổ).",
        "output": "Redirect về admin.equipment.index kèm toast thông báo thành công."
    },
    {
        "search_key": "10. Xây dựng module Sổ quỹ thu - chi và ghi nhận dòng tiền",
        "code": "FEAT-AQ-10",
        "branch": "AnhQuy/so-quy-thu-chi",
        "assignee": "Nguyễn Anh Quý",
        "controller": "App\\Http\\Controllers\\ReportController@index, storeTransaction",
        "endpoint": "GET /smartroom/admin/reports, POST /smartroom/admin/reports/transactions",
        "middleware": "auth, admin, role:landlord",
        "inputs": [
            ("type", "String", "Có", "required|in:income,expense", "Loại phiếu phải là Thu (income) hoặc Chi (expense)."),
            ("category", "String", "Có", "required|string|max:100", "Khoản mục thu chi không được để trống."),
            ("amount", "Numeric", "Có", "required|numeric|min:1000", "Số tiền giao dịch tối thiểu là 1.000 VNĐ."),
            ("description", "String", "Có", "required|string|max:500", "Vui lòng nhập diễn giải khoản thu chi."),
            ("transaction_date", "Date", "Có", "required|date|before_or_equal:today", "Ngày ghi nhận không thể là ngày tương lai.")
        ],
        "logic": [
            "Ghi nhận dòng tiền phát sinh ngoài tiền phòng (bảo trì, mua sắm vật tư, thanh lý đồ cũ...).",
            "Tính toán số dư quỹ thuần: net_cash_flow = total_income - total_expense.",
            "Trực quan hóa thẻ KPI doanh thu và lợi nhuận ròng của cơ sở lưu trú."
        ],
        "db": "transactions (tenant_id, type, category, amount, description, transaction_date).",
        "output": "Redirect về admin.reports.index kèm toast xanh ghi nhận phiếu thành công."
    },
    {
        "search_key": "11. Tích hợp AI tự động viết bài đăng mô tả phòng trọ",
        "code": "FEAT-AQ-11 & FEAT-AQ-12",
        "branch": "AnhQuy/ai-viet-mo-ta-phong, AnhQuy/nhat-ky-kiem-toan",
        "assignee": "Nguyễn Anh Quý",
        "controller": "App\\Http\\Controllers\\RoomController@generateDescription, AdminActivityLogController@index",
        "endpoint": "POST /smartroom/admin/rooms/description/ai, GET /smartroom/admin/activity-logs",
        "middleware": "auth, admin, role:landlord",
        "inputs": [
            ("room_number", "String", "Có", "required|string", "Số phòng không được để trống."),
            ("area", "Numeric", "Có", "required|numeric", "Diện tích phòng không hợp lệ."),
            ("price", "Numeric", "Có", "required|numeric", "Giá thuê phòng không hợp lệ.")
        ],
        "logic": [
            "AI Copywriter: Gemini API tổng hợp diện tích, giá thuê, tiện ích phòng để tạo bài đăng hấp dẫn, tối ưu từ khóa SEO tìm kiếm.",
            "Tự động ghi nhận mọi thao tác tạo/sửa/xóa của quản trị viên vào nhật ký admin_activity_logs (lưu vết IP, User Agent, old/new values)."
        ],
        "db": "admin_activity_logs.",
        "output": "JSON {'success': true, 'description': '🌟 SIÊU PHẨM PHÒNG TRỌ BAN CÔNG THOÁNG MÁT...'}"
    },

    # --- VĨNH EM ---
    {
        "search_key": "1, 2 & 3. Cổng tìm kiếm Renty Portal, Bộ lọc thông minh trực",
        "code": "FEAT-VEM-01, FEAT-VEM-02 & FEAT-VEM-03",
        "branch": "main, feat(smart-search)",
        "assignee": "Huỳnh Văn Vĩnh Em",
        "controller": "App\\Services\\SmartSearchService, App\\Http\\Controllers\\Api\\SmartSearchController",
        "endpoint": "GET /renty, GET /api/renty/rooms, POST /api/renty/rooms/compare",
        "middleware": "web",
        "inputs": [
            ("price_min", "Numeric", "Không", "nullable|numeric|min:0", "Giá tối thiểu phải >= 0."),
            ("price_max", "Numeric", "Không", "nullable|numeric|gte:price_min", "Giá tối đa phải >= giá tối thiểu."),
            ("rental_type", "String", "Không", "nullable|in:month,day,hour", "Hình thức thuê phải là month, day hoặc hour."),
            ("room_ids", "Array", "Có (khi so sánh)", "required|array|min:2|max:3", "Vui lòng chọn từ 2 đến tối đa 3 phòng để so sánh.")
        ],
        "logic": [
            "Cổng tìm kiếm phong cách Glassmorphism (backdrop-blur-md) tương thích responsive hoàn hảo mọi thiết bị di động.",
            "Smart Search Filter lọc đa tiêu chí (khoảng giá, tiện ích WC khép kín, gác lửng, ban công, thú cưng, trạng thái phòng trống).",
            "Thanh so sánh phòng nổi song song (tối đa 3 phòng) đối chiếu giá thuê, cọc, diện tích, tiện ích và điểm sao đánh giá."
        ],
        "db": "rooms, buildings, tenants, reviews.",
        "output": "HTML trang chủ Renty và JSON API danh sách phòng lọc / đối chiếu."
    },
    {
        "search_key": "4 & 5. Chi tiết phòng trọ (Room Detail), Review có xác thực",
        "code": "FEAT-VEM-04, FEAT-VEM-05 & FEAT-VEM-06",
        "branch": "main",
        "assignee": "Huỳnh Văn Vĩnh Em",
        "controller": "Route GET /renty/room/{id}, POST /renty/room/{id}/review, POST /renty/room/{id}/report",
        "endpoint": "GET /renty/room/{id}, POST /renty/room/{id}/review, POST /renty/room/{id}/report",
        "middleware": "web (detail, report), auth (review)",
        "inputs": [
            ("rating", "Integer", "Có (khi review)", "required|integer|min:1|max:5", "Điểm đánh giá phải từ 1 đến 5 sao."),
            ("comment", "String", "Có (khi review)", "required|string|min:10|max:1000", "Nhận xét phải từ 10 đến 1000 ký tự."),
            ("reason", "String", "Có (khi report)", "required|in:scam,fake_images,wrong_price,unsafe,other", "Lý do báo cáo vi phạm không hợp lệ."),
            ("description", "String", "Có (khi report)", "required|string|min:10|max:1000", "Mô tả bằng chứng sai phạm từ 10 - 1000 ký tự.")
        ],
        "logic": [
            "Trang Room Detail hiển thị slide ảnh/video, vị trí bản đồ GIS và thuật toán cảnh báo nếu giá phòng dị biệt bất thường.",
            "Cơ chế Verified Tenant Guard: Chỉ người dùng đã từng có Hợp đồng hoặc Booking thuê phòng thực tế mới được phép gửi đánh giá Review.",
            "Tiếp nhận báo cáo lừa đảo (RoomReport); tự động gắn cờ cảnh báo vàng nếu phòng nhận quá 3 báo cáo lừa cọc."
        ],
        "db": "reviews (gắn nhãn Verified Tenant), room_reports, rooms.",
        "output": "Toast xác nhận gửi review/report thành công; ngăn chặn triệt để seeding đánh giá ảo."
    },
    {
        "search_key": "6. Tích hợp Trợ lý ảo AI Renty Chatbot theo mô hình RAG",
        "code": "FEAT-VEM-07",
        "branch": "main",
        "assignee": "Huỳnh Văn Vĩnh Em",
        "controller": "App\\Http\\Controllers\\ChatbotController@chat",
        "endpoint": "POST /renty/chatbot/chat",
        "middleware": "web, throttle:60,1",
        "inputs": [
            ("message", "String", "Có", "required|string|min:2|max:300", "Câu hỏi phải từ 2 đến tối đa 300 ký tự.")
        ],
        "logic": [
            "Mô hình RAG (Retrieval-Augmented Generation): Phân tích ngữ nghĩa câu hỏi, truy vấn CSDL lấy tối đa 5 phòng trống phù hợp nhất nạp vào Prompt.",
            "Google Gemini API (gemini-2.5-flash) trả lời tự nhiên dựa trên dữ liệu phòng thực tế, triệt tiêu hiện tượng AI bịa đặt phòng.",
            "Trả về câu trả lời kèm thẻ Card xem nhanh phòng (ảnh, giá, địa chỉ) trực tiếp trong khung chat."
        ],
        "db": "rooms, buildings (chỉ đọc phòng trống status = empty).",
        "output": "JSON {'success': true, 'reply': '...', 'suggested_rooms': [...]}."
    },
    {
        "search_key": "7. Xây dựng Cổng thông tin Cư dân & Khách lưu trú (Guest Portal)",
        "code": "FEAT-VEM-08",
        "branch": "main",
        "assignee": "Huỳnh Văn Vĩnh Em",
        "controller": "App\\Http\\Controllers\\ResidentPortalController@index, billQr, requestRenewal",
        "endpoint": "GET /smartroom/resident, GET /smartroom/resident/bills/{id}/qr, POST /smartroom/resident/contract/{id}/request-renewal",
        "middleware": "auth",
        "inputs": [
            ("extension_months", "Integer", "Có (khi gia hạn)", "required|integer|min:1|max:36", "Thời gian gia hạn từ 1 đến 36 tháng.")
        ],
        "logic": [
            "Cổng thông tin riêng cho cư dân: theo dõi thời hạn hợp đồng, danh sách người ở cùng phòng và bảng hóa đơn chưa nộp.",
            "Hiển thị mã VietQR chuyển khoản chứa sẵn STK chủ trọ, số tiền và cú pháp hóa đơn để quét thanh toán tức thì.",
            "Gửi phiếu đề nghị gia hạn hợp đồng trực tuyến đến chủ cơ sở."
        ],
        "db": "residents, rooms, contracts, bills, resident_relatives.",
        "output": "Dashboard cư dân tối ưu trên điện thoại di động."
    },
    {
        "search_key": "8. Lập trình module Tiếp nhận & Xử lý sự cố kỹ thuật",
        "code": "FEAT-VEM-09",
        "branch": "VinhEm/8-XuLyBaoHongDangDonPhong",
        "assignee": "Huỳnh Văn Vĩnh Em",
        "controller": "App\\Http\\Controllers\\ResidentPortalController@storeTicket, analyzeTicket",
        "endpoint": "POST /smartroom/resident/tickets, POST /smartroom/resident/tickets/analyze",
        "middleware": "auth",
        "inputs": [
            ("title", "String", "Có", "required|string|max:255", "Tiêu đề sự cố không được để trống."),
            ("description", "String", "Có", "required|string|min:5|max:1000", "Mô tả sự cố hỏng hóc từ 5 - 1000 ký tự."),
            ("category", "String", "Có", "required|in:electric,water,furniture,maintenance,other", "Danh mục sự cố không hợp lệ."),
            ("image", "File", "Không", "nullable|image|mimes:jpeg,png,jpg,webp|max:10240", "Ảnh chụp sự cố tối đa 10MB.")
        ],
        "logic": [
            "AI NLP phân tích nội dung mô tả: tự động đánh giá độ khẩn cấp (priority: low, medium, high) và gợi ý biện pháp xử lý tạm thời cho cư dân.",
            "Tạo phiếu sự cố trong tickets (status = pending) và tự động thông báo đến bộ phận kỹ thuật / dọn phòng để điều phối xử lý."
        ],
        "db": "tickets (tenant_id, room_id, resident_id, title, description, category, priority, status).",
        "output": "Redirect về danh sách ticket kèm toast thông báo tiếp nhận thành công."
    },
    {
        "search_key": "9. Lập trình module Quản lý thông tin Cư dân & Thân nhân lưu trú",
        "code": "FEAT-VEM-10",
        "branch": "main",
        "assignee": "Huỳnh Văn Vĩnh Em",
        "controller": "App\\Http\\Controllers\\AdminDashboardController@storeResident, getRelatives, storeRelative",
        "endpoint": "POST /smartroom/admin/resident, GET /smartroom/admin/resident/{id}/relatives, POST /smartroom/admin/resident/{id}/relative",
        "middleware": "auth, admin",
        "inputs": [
            ("name", "String", "Có", "required|string|max:255", "Vui lòng nhập họ tên người ở cùng."),
            ("cccd", "String", "Có", "required|regex:/^[0-9]{12}$/", "Số CCCD người ở cùng phải đủ 12 chữ số."),
            ("relationship", "String", "Có", "required|string|max:100", "Vui lòng ghi rõ quan hệ nhân thân.")
        ],
        "logic": [
            "Kiểm tra sức chứa của phòng; chặn thêm người ở cùng nếu vượt quá quy định tối đa của phòng.",
            "Mã hóa AES-256-GCM số CCCD và SĐT của người ở cùng trước khi lưu vào resident_relatives."
        ],
        "db": "residents, resident_relatives.",
        "output": "JSON danh sách thân nhân được cập nhật thành công."
    },
    {
        "search_key": "10. Lập trình chức năng Tự động trích xuất và kết xuất tờ khai đăng ký tạm trú",
        "code": "FEAT-VEM-11",
        "branch": "main",
        "assignee": "Huỳnh Văn Vĩnh Em",
        "controller": "App\\Http\\Controllers\\AdminDashboardController@exportCt01",
        "endpoint": "GET /smartroom/admin/resident/{id}/export-ct01",
        "middleware": "auth, admin",
        "inputs": [
            ("id", "Integer (Route)", "Có", "exists:residents,id", "Hồ sơ cư dân không tồn tại.")
        ],
        "logic": [
            "Tổng hợp dữ liệu nhân thân: Giải mã CCCD, ngày sinh, quê quán của cư dân và người ở cùng.",
            "Kiểm tra tính đầy đủ của hồ sơ; tự động điền vào biểu mẫu Mẫu CT01 (Bộ Công an) qua DomPDF xuất file in nộp công an phường."
        ],
        "db": "residents, resident_relatives, buildings, landlord_profiles.",
        "output": "Tải về file PDF Mau_CT01_TamTru_{TenCuDan}.pdf."
    },
    {
        "search_key": "11. Xây dựng tiện ích Đăng ký nhận chuông báo khi phòng đang thuê chuyển sang trạng thái trống",
        "code": "FEAT-VEM-12",
        "branch": "main",
        "assignee": "Huỳnh Văn Vĩnh Em",
        "controller": "App\\Http\\Controllers\\AdminDashboardController@storeContactRequest, updateContactRequestStatus",
        "endpoint": "POST /renty/contact-request",
        "middleware": "web, throttle:5,1",
        "inputs": [
            ("room_id", "Integer", "Có", "required|integer|exists:rooms,id", "Phòng đăng ký không tồn tại."),
            ("name", "String", "Có", "required|string|max:255", "Vui lòng nhập họ tên của bạn."),
            ("phone", "String", "Có", "required|regex:/^(0[3|5|7|8|9])[0-9]{8}$/", "Số điện thoại nhận tin không hợp lệ.")
        ],
        "logic": [
            "Khách thuê nhấn Đăng ký nhận chuông báo khi phòng đang có người ở.",
            "Hệ thống lưu contact_requests; khi hợp đồng kết thúc và phòng chuyển về empty, hệ thống tự động bắn tin nhắn Zalo/SMS cho khách."
        ],
        "db": "contact_requests, notification_logs.",
        "output": "JSON {'success': true, 'message': 'Đăng ký nhận chuông báo thành công!'}"
    }
]

print(f"-> Đã chuẩn bị đặc tả kỹ thuật cho {len(MODULE_SPECS)} module.")

print("3. Đang tiến hành cập nhật trực tiếp vào từng module trong Word...")

# Ánh xạ và cập nhật
count_updated = 0
for spec in MODULE_SPECS:
    key = spec["search_key"]
    found = False
    for i in range(188, len(doc.paragraphs)):
        p_title = doc.paragraphs[i]
        if key in p_title.text:
            found = True
            # Tìm đoạn mô tả chi tiết ngay sau đó
            desc_p = None
            for j in range(i + 1, min(i + 5, len(doc.paragraphs))):
                if doc.paragraphs[j].text.strip().startswith("Mô tả chi tiết chức năng:"):
                    desc_p = doc.paragraphs[j]
                    break
            
            if desc_p:
                # 1. Soạn văn bản đặc tả kỹ thuật chi tiết
                full_spec_text = (
                    f"ĐẶC TẢ KỸ THUẬT CHI TIẾT (TECHNICAL SPECIFICATION):\n"
                    f"• Mã Chức Năng: {spec['code']}  |  Nhánh Git Thực Tế (Branch): {spec['branch']}\n"
                    f"• Thành viên phụ trách: {spec['assignee']}\n"
                    f"• Controller & Action Method: {spec['controller']}\n"
                    f"• Endpoint & HTTP Method: {spec['endpoint']}  |  Middleware: {spec['middleware']}\n\n"
                    f"1. Thuật toán và Luồng xử lý nghiệp vụ (Business Logic Flow):\n"
                )
                for step_idx, step_txt in enumerate(spec["logic"], 1):
                    full_spec_text += f"   {step_idx}. {step_txt}\n"
                
                full_spec_text += (
                    f"\n2. Tương tác Cơ sở dữ liệu (Database Operation):\n"
                    f"   • {spec['db']}\n\n"
                    f"3. Hợp đồng dữ liệu đầu ra (Output Specification):\n"
                    f"   • {spec['output']}\n\n"
                    f"4. Quy tắc xác thực dữ liệu đầu vào (Request Validation Rules):"
                )
                
                # Ghi vào đoạn văn
                desc_p.text = full_spec_text
                
                # 2. Tạo bảng Validation Rules và chèn ngay sau đoạn desc_p
                inputs = spec["inputs"]
                if inputs:
                    val_table = doc.add_table(rows=len(inputs) + 1, cols=5)
                    val_table.style = 'Table Grid'
                    
                    headers = ["Tên Field", "Kiểu", "Bắt Buộc", "Validation Rules (Laravel)", "Thông Báo Lỗi Khi Fail"]
                    for c_idx, h_text in enumerate(headers):
                        cell = val_table.rows[0].cells[c_idx]
                        cell.text = h_text
                    
                    for r_idx, inp_row in enumerate(inputs, 1):
                        row_cells = val_table.rows[r_idx].cells
                        for c_idx, val_txt in enumerate(inp_row):
                            row_cells[c_idx].text = val_txt
                    
                    # Chèn bảng vào sau đoạn desc_p trong DOM XML
                    desc_p._p.addnext(val_table._tbl)
                
                count_updated += 1
                print(f"  [+] Đã cập nhật spec & bảng validation cho: {spec['code']}")
            break

print(f"\n-> Hoàn thành cập nhật {count_updated}/{len(MODULE_SPECS)} module chức năng trong Word!")

# Lưu tệp
out_file = Path("BaoCao_NhomA (1).docx").resolve()
doc.save(str(out_file))
print(f"\n[THÀNH CÔNG] Đã lưu cập nhật trực tiếp vào: {out_file}")

# Đồng thời lưu một bản sang baocaomoi.docx để đồng bộ
doc.save(str(Path("baocaomoi.docx").resolve()))
print(f"[ĐỒNG BỘ] Đã lưu bản sao đồng bộ tại: baocaomoi.docx")
