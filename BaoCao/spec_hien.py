# -*- coding: utf-8 -*-
"""
Đặc tả chi tiết 13 chức năng của Nguyễn Thanh Hiền (Nhóm trưởng)
Chuẩn hóa: 10 người đọc đều code giống nhau, coder đọc vô code được ngay.
"""

SPEC_HIEN = """
## A. CÁC MODULE & ĐẶC TẢ KỸ THUẬT DO NGUYỄN THANH HIỀN (NHÓM TRƯỞNG) PHỤ TRÁCH

---

### [FEAT-HIEN-01] Khởi tạo kiến trúc dự án Laravel 11, Docker, CI/CD GitHub Actions & 35 Migrations
- **Git Branch**: `main`, `CI/CD`, `CauHinh`
- **Thành viên phụ trách**: Nguyễn Thanh Hiền
- **Phạm vi mã nguồn**: `docker-compose.yml`, `.github/workflows/laravel-ci.yml`, `.github/workflows/docker-ci.yml`, `database/migrations/*`
- **Môi trường**: PHP 8.3-fpm, Nginx Alpine, MySQL 8.0, Redis 7.2 Alpine

#### 1. Input Specification (Môi trường & Biến cấu hình .env)
| Biến Cấu Hình | Kiểu Dữ Liệu | Bắt Buộc | Giá Trị Mặc Định / Mẫu | Mục Đích Sử Dụng |
|---|---|---|---|---|
| `APP_ENV` | String | Có | `local` / `production` | Môi trường thực thi ứng dụng |
| `DB_CONNECTION` | String | Có | `mysql` | Driver cơ sở dữ liệu chính |
| `DB_HOST` | String | Có | `127.0.0.1` / `db` | Host kết nối MySQL container |
| `DB_PORT` | Integer | Có | `3306` | Cổng kết nối CSDL |
| `DB_DATABASE` | String | Có | `cdweb1_db` | Tên CSDL ứng dụng |
| `DB_USERNAME` | String | Có | `root` / `sail` | Tài khoản đăng nhập MySQL |
| `DB_PASSWORD` | String | Có | `password` | Mật khẩu truy cập MySQL |
| `PII_ENCRYPTION_KEY` | String (Base64) | Có | `base64:32bytes...` | Khóa mã hóa đối xứng AES-256-GCM |
| `BLIND_INDEX_KEY` | String (Hex/Base64) | Có | `sha256:32bytes...` | Khóa HMAC-SHA256 băm chỉ mục tra cứu |
| `GEMINI_API_KEY` | String | Có | `AIzaSy...` | Khóa API truy cập Google Gemini AI |

#### 2. Business Logic Flow
1. **Thiết lập hạ tầng Docker**: Khởi động 4 dịch vụ độc lập (`app`, `web`, `db`, `redis`) qua file `docker-compose.yml`, mount volume mã nguồn và thư mục `storage`.
2. **Quy trình CI/CD GitHub Actions**:
   - Chạy linter định dạng mã nguồn chuẩn hóa bằng `vendor/bin/pint --test`.
   - Khởi tạo service container MySQL, chạy toàn bộ `php artisan migrate --force`.
   - Thực thi bộ kiểm thử tự động `php artisan test` (đảm bảo 100% 64/64 Unit & Feature tests pass).
   - Biên dịch tài nguyên giao diện `npm run build` (Vite + Tailwind CSS).
3. **Thực thi 35 Migrations**: Thứ tự chạy migration từ bảng cha sang bảng con, kích hoạt Foreign Key constraints, Unique Indexes và Composite Indexes cho các bảng lõi.

#### 3. Database Operation
- **Thực thi**: Tạo lập 35 bảng hệ thống bao gồm `tenants`, `roles`, `users`, `buildings`, `rooms`, `equipment`, `room_equipment`, `residents`, `resident_relatives`, `contracts`, `utility_records`, `bills`, `transactions`, `tickets`, `room_reports`, `landlord_profiles`, `audit_logs`...
- **Ràng buộc**: Khóa ngoại `ON DELETE RESTRICT` cho các quan hệ bảo vệ toàn vẹn tài chính, `ON DELETE CASCADE` cho các bảng chi tiết phụ thuộc.

#### 4. Output Specification
- **Thành công**: Pipeline GitHub Actions trả về trạng thái `All checks have passed` (Xanh lá), ứng dụng lắng nghe tại `http://localhost:8000` hoặc cổng Docker `http://localhost:8088`.
- **Thất bại**: CI dừng khẩn cấp và gửi cảnh báo đỏ về Telegram/Email nếu có bất kỳ test case nào fail hoặc migration bị lỗi Foreign Key.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Lỗi kết nối CSDL khi chạy Migration | Terminal bôi đỏ: "Lỗi kết nối CSDL: SQLSTATE[HY000] [2002] Connection refused. Vui lòng kiểm tra file .env và container MySQL". |
| Trùng lặp bảng hoặc khóa ngoại sai thứ tự | Dừng tiến trình, hiển thị mã lỗi ForeignKeyConstraintViolationException và tự động rollback giao dịch. |

---

### [FEAT-HIEN-02] Phân quyền truy cập đa tầng RBAC & Multi-tenancy phân lập theo Tenant
- **Git Branch**: `Hien/PhanQuyen`
- **Thành viên phụ trách**: Nguyễn Thanh Hiền
- **Controller & Method**: `App\Http\Controllers\CrudUserController@listUser`, `updateRole`
- **Middleware**: `App\Http\Middleware\RoleMiddleware`, Global Scope `App\Models\Scopes\TenantScope`
- **Endpoint & Method**: `GET /list`, `POST /users/role`
- **Middleware kiểm soát**: `auth`, `role:admin`

#### 1. Input Specification (Request Validation POST /users/role)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `user_id` | Integer | Có | `required\|integer\|exists:users,id` | "Tài khoản người dùng không tồn tại trên hệ thống." |
| `role` | String | Có | `required\|string\|in:admin,landlord,unverified_landlord,manager,resident,guest` | "Vai trò được gán không hợp lệ." |

#### 2. Business Logic Flow
1. **Kiểm tra quyền quản trị viên**: Middleware `role:admin` kiểm tra người thực hiện có quyền `admin`. Nếu không, trả về HTTP 403 Forbidden.
2. **Cơ chế Multi-tenancy Scoping**:
   - Mọi truy vấn trên các Model nghiệp vụ (`Building`, `Room`, `Resident`, `Bill`, `Contract`...) tự động áp dụng `TenantScope`: `builder->where('tenant_id', auth()->user()->tenant_id)`.
   - Nếu người dùng có role `admin` (Superadmin), bỏ qua Scope qua hàm `withoutGlobalScope(TenantScope::class)`.
3. **Thực thi phân quyền**:
   - Tìm kiếm người dùng theo `user_id`. Chặn không cho phép Admin tự hạ quyền chính mình nếu là admin duy nhất.
   - Cập nhật trường `role` và `role_id` tương ứng trong bảng `users`.
   - Ghi bản ghi Audit Log truy vết hành vi thay đổi quyền hạn.
4. **Trả về phản hồi**: Redirect về danh sách kèm Flash message thành công.

#### 3. Database Operation
- **Bảng tác động**: `users` (cột `role`, `role_id`), `audit_logs` (ghi log phân quyền).

#### 4. Output Specification
- **Thành công (HTTP 302)**: Redirect về route `user.list` kèm `session('success', 'Cập nhật vai trò người dùng thành công!')`.
- **Thất bại (HTTP 403 / 422)**:
  - 403: View `errors.403` "Bạn không có quyền hạn thực hiện thao tác này".
  - 422: Redirect back kèm `$errors->withInput()`.

#### 5. UI/UX Specification & Xử lý lỗi
- **Component**: View `resources/views/list.blade.php`.
- **Badge vai trò**: `admin` (bg-red-500/20 text-red-400), `landlord` (bg-sky-500/20 text-sky-400), `resident` (bg-emerald-500/20 text-emerald-400).
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Truy cập URL vượt quyền hạn (VD: Landlord vào /admin/audit-logs) | Hệ thống chặn ngay lập tức, hiển thị trang 403 Forbidden kèm nút quay lại trang chủ quản trị an toàn. |
| Cố tình sửa tham số tenant_id hoặc property_id trên URL | Hệ thống trả về mã lỗi 404 Not Found do TenantScope tự động loại bỏ bản ghi không thuộc quyền quản lý. |

---

### [FEAT-HIEN-03] Đăng ký & Đăng nhập truyền thống kèm Rate Limiting chống Brute-force
- **Git Branch**: `Hien/Login_Sign`
- **Thành viên phụ trách**: Nguyễn Thanh Hiền
- **Controller & Method**: `App\Http\Controllers\CrudUserController@login`, `authUser`, `createUser`, `postUser`
- **Endpoint & Method**: `GET /login`, `POST /login`, `GET /create`, `POST /create`
- **Middleware**: `guest`, `throttle:30,1` (Login), `throttle:10,1` (Register)

#### 1. Input Specification (Request Validation POST /login)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `login` | String | Có | `required\|string` | "Vui lòng nhập tên đăng nhập hoặc số điện thoại." |
| `password` | String | Có | `required\|string\|min:6` | "Mật khẩu phải có độ dài từ 6 ký tự trở lên." |

#### 2. Business Logic Flow
1. **Kiểm tra Rate Limiting**: Nếu IP gửi quá 30 request/phút hoặc tài khoản nhập sai quá 5 lần liên tiếp trong 60 giây, chặn ngay và trả về HTTP 429 Too Many Requests.
2. **Nhận diện phương thức đăng nhập**:
   - Kiểm tra `login` có phải định dạng số điện thoại Việt Nam (`preg_match('/^(0[3|5|7|8|9])[0-9]{8}$/')`).
   - Nếu là SĐT: Tính giá trị băm `phone_blind_index = hash_hmac('sha256', $phone, env('BLIND_INDEX_KEY'))` và truy vấn `User::where('phone_blind_index', $blindIndex)->first()`.
   - Nếu là username: Truy vấn `User::where('username', $login)->first()`.
3. **Xác thực Mật khẩu**: Dùng `Hash::check($password, $user->password)`.
   - Nếu không khớp: Tăng biến đếm rate limit, trả về lỗi "Thông tin đăng nhập hoặc mật khẩu không chính xác".
   - Nếu khớp: `Auth::login($user, $remember = true)`. Xóa biến đếm rate limit.
4. **Phân luồng điều hướng**:
   - Role `admin`: Redirect `/smartroom/admin`.
   - Role `landlord`: Redirect `/smartroom/admin`.
   - Role `resident`: Redirect `/smartroom/resident`.
   - Role `guest`: Redirect `/renty`.

#### 3. Database Operation
- **Đọc**: `users` (kiểm tra username hoặc `phone_blind_index`).
- **Ghi**: Cập nhật `last_login_at`, lưu session vào bảng `sessions`.

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng đến dashboard tương ứng vai trò.
- **Thất bại (HTTP 422 / 429)**: Redirect back với input `login`, flash message lỗi đỏ.

#### 5. UI/UX Specification & Xử lý lỗi
- **Form element**: `#login-form`, input `#login-field`, `#password-field`, nút submit `#btn-login`.
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống cả Tên đăng nhập và Mật khẩu | Viền 2 ô input đổi sang màu đỏ `border-red-500`. Hiển thị: "Vui lòng nhập tên đăng nhập hoặc số điện thoại" và "Vui lòng nhập mật khẩu". |
| Nhập sai thông tin đăng nhập | Hiển thị thông báo trên đầu form: "Thông tin đăng nhập hoặc mật khẩu không chính xác. Vui lòng kiểm tra lại!". |
| Nhập sai mật khẩu liên tiếp quá 5 lần | Khóa form 60 giây (HTTP 429): "Bạn đã thao tác sai quá nhiều lần. Vui lòng đợi sau 60 giây". |

---

### [FEAT-HIEN-04] Xác thực không mật khẩu WebAuthn / FIDO2 Passkey sinh trắc học
- **Git Branch**: `Hien/Login_Sign`
- **Thành viên phụ trách**: Nguyễn Thanh Hiền
- **Controller & Method**: `Laragear\WebAuthn\Http\Controllers\WebAuthnLoginController`, `WebAuthnRegisterController`
- **Endpoint**: `POST /webauthn/login/options`, `POST /webauthn/login`, `POST /webauthn/register/options`, `POST /webauthn/register`
- **Middleware**: `web`, `throttle:10,1`

#### 1. Input Specification (Request Verification Assertion)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `id` | String | Có | `required\|string` | "Định danh credential Passkey không hợp lệ." |
| `rawId` | String (Base64) | Có | `required\|string` | "Mã khóa gốc Passkey không hợp lệ." |
| `response.clientDataJSON` | String (Base64) | Có | `required\|string` | "Thiếu dữ liệu xác thực clientDataJSON." |
| `response.authenticatorData`| String (Base64) | Có | `required\|string` | "Thiếu chữ ký phần cứng authenticatorData." |
| `response.signature` | String (Base64) | Có | `required\|string` | "Chữ ký sinh trắc học không hợp lệ." |

#### 2. Business Logic Flow
1. **Khởi tạo Challenge**: Client gọi `POST /webauthn/login/options`. Server sinh chuỗi ngẫu nhiên 32 bytes cryptographically secure, lưu vào session `webauthn.challenge`.
2. **Kích hoạt phần cứng WebAuthn**: Trình duyệt gọi `navigator.credentials.get({publicKey: options})`, kích hoạt cảm biến vân tay/FaceID/Windows Hello của thiết bị.
3. **Xác thực chữ ký công khai (Public Key Assertion)**:
   - Server nhận Assertion response từ client.
   - Kiểm tra Challenge khớp với session, kiểm tra `origin` khớp với domain hệ thống, kiểm tra `userPresence` và `userVerification`.
   - Tìm kiếm khóa công khai trong bảng `webauthn_credentials` theo `credential_id`.
   - Xác thực chữ ký `signature` bằng Public Key.
4. **Đăng nhập người dùng**: Lấy `user_id` liên kết với credential và thực hiện `Auth::loginUsingId($credential->user_id)`.

#### 3. Database Operation
- **Bảng tác động**: `webauthn_credentials` (đọc Public Key, cập nhật `counter` chống replay attack).

#### 4. Output Specification
- **Thành công (HTTP 204 / JSON)**: `{"status": "ok", "redirect": "/smartroom/admin"}`.
- **Thất bại (HTTP 422)**: `{"error": "Xác thực sinh trắc học thất bại hoặc khóa không tồn tại."}`.

#### 5. UI/UX Specification & Xử lý lỗi
- **Nút bấm**: `#btn-webauthn-login` với icon vân tay phát sáng `text-sky-400`.
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Thiết bị hoặc trình duyệt không hỗ trợ WebAuthn | Ẩn nút Passkey hoặc hiển thị popup: "Trình duyệt chưa hỗ trợ WebAuthn. Vui lòng đăng nhập bằng mật khẩu". |
| Người dùng bấm Hủy (Cancel) trên popup sinh trắc | Toast thông báo vàng: "Thao tác xác thực vân tay/Passkey đã bị hủy. Bạn có thể thử lại". |

---

### [FEAT-HIEN-05] Mã hóa bảo mật dữ liệu cá nhân PII bằng AES-256-GCM & HMAC Blind Index
- **Git Branch**: `Hien/PhanQuyen`
- **Thành viên phụ trách**: Nguyễn Thanh Hiền
- **Controller & Service**: `App\Services\SecureDocumentService`, `App\Http\Controllers\Api\SensitiveDataController`
- **Phạm vi bảo vệ**: Các trường `phone`, `cccd`, `bank_account_number` trên toàn hệ thống

#### 1. Input Specification (Tra cứu dữ liệu nhạy cảm)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `search_term` | String | Có | `required\|string\|min:3` | "Chuỗi tìm kiếm phải từ 3 ký tự trở lên." |
| `field_type` | String | Có | `required\|in:phone,cccd` | "Loại trường tìm kiếm không hợp lệ." |

#### 2. Business Logic Flow
1. **Quy trình Mã hóa (Ghi dữ liệu)**:
   - Dữ liệu thô (Plaintext) như số CCCD được mã hóa bằng AES-256-GCM với IV (Initialization Vector) 96-bit ngẫu nhiên và Authentication Tag 128-bit:
     `ciphertext = encrypt_aes_256_gcm(plaintext, key, iv, tag)`.
   - Đồng thời sinh mã chỉ mục mù (Blind Index) phục vụ tra cứu chính xác:
     `blind_index = hash_hmac('sha256', strtolower(trim(plaintext)), env('BLIND_INDEX_KEY'))`.
   - Lưu vào cơ sở dữ liệu: Cột `cccd` lưu `ciphertext:iv:tag` (dạng chuỗi base64), cột `cccd_blind_index` lưu chuỗi hex 64 ký tự.
2. **Quy trình Tra cứu**: Không bao giờ giải mã toàn bộ DB. Tính hash của giá trị tìm kiếm và truy vấn: `where('cccd_blind_index', $targetBlindIndex)`.
3. **Quy trình Giải mã hiển thị**: Chỉ người dùng có thẩm quyền kèm lý do nghiệp vụ mới được giải mã. Kết quả hiển thị được mặt nạ hóa mặc định (ví dụ: `0912****89`, `07920100****`).

#### 3. Database Operation
- **Bảng tác động**: `users`, `residents`, `resident_relatives`, `landlord_profiles`.
- **Cột**: `phone`, `phone_blind_index`, `cccd`, `cccd_blind_index`.

#### 4. Output Specification
- **Thành công**: Trả về dữ liệu đã giải mã kèm ghi nhận 1 bản ghi vào `audit_logs`.
- **Thất bại**: Trả về chuỗi lỗi `[DECRYPTION_ERROR]` và kích hoạt cảnh báo an ninh.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Nhập sai mật khẩu cấp 2 khi xem số CCCD | Ô mật khẩu bôi đỏ: "Mật khẩu xác thực cấp 2 không chính xác. Quyền xem thông tin bị từ chối". |
| Khóa mã hóa hệ thống PII_ENCRYPTION_KEY bị thay đổi | Dữ liệu hiển thị mặt nạ [DECRYPTION_ERROR], hệ thống tự động kích hoạt cảnh báo an ninh gửi Superadmin. |

---

### [FEAT-HIEN-06] Quản lý Hợp đồng thuê phòng & Tiền cọc (Contracts)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Nguyễn Thanh Hiền
- **Controller & Method**: `App\Http\Controllers\AdminDashboardController@storeContract`, `deleteContract`, `renewContract`
- **Endpoint & Method**: `POST /smartroom/admin/contract`, `DELETE /smartroom/admin/contract/{id}`, `POST /smartroom/admin/contract/{id}/renew`
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation POST /contract)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `room_id` | Integer | Có | `required\|integer\|exists:rooms,id` | "Phòng được chọn không tồn tại." |
| `resident_id` | Integer | Có | `required\|integer\|exists:residents,id` | "Thông tin cư dân đại diện không hợp lệ." |
| `deposit` | Numeric | Có | `required\|numeric\|min:0` | "Tiền đặt cọc giữ phòng phải lớn hơn hoặc bằng 0 VNĐ." |
| `start_date` | Date | Có | `required\|date` | "Ngày bắt đầu hợp đồng không hợp lệ." |
| `end_date` | Date | Có | `required\|date\|after:start_date` | "Ngày kết thúc hợp đồng phải sau ngày bắt đầu." |
| `terms` | String | Không | `nullable\|string` | "Điều khoản hợp đồng không hợp lệ." |

#### 2. Business Logic Flow
1. **Kiểm tra trạng thái phòng**: Phòng được chọn phải đang ở trạng thái `empty` hoặc `maintenance`. Nếu phòng đang có hợp đồng `active`, chặn thao tác (HTTP 422).
2. **Sinh mã hợp đồng chuẩn**: Tạo chuỗi duy nhất định dạng `HD-{YYYY}-{room_number}-{random4}` (Ví dụ: `HD-2026-P101-A9B2`).
3. **Mở DB Transaction**:
   - Tạo bản ghi mới trong bảng `contracts` với `status = 'draft'`.
   - Tự động sinh biên lai tiền cọc vào bảng `bills` (nếu `deposit > 0`).
4. **Commit & Phản hồi**: Trả về redirect kèm thông báo thành công và đường dẫn ký số trực tuyến.

#### 3. Database Operation
- **Bảng tác động**: `contracts` (INSERT bản ghi mới), `rooms` (chuẩn bị trạng thái), `bills` (sinh phiếu cọc).

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về tab Hợp đồng kèm toast xanh: "Tạo hợp đồng thuê phòng thành công. Hãy gửi liên kết ký số cho cư dân."
- **Thất bại (HTTP 422)**: Redirect back với input và `$errors`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Ngày kết thúc sớm hơn hoặc trùng ngày bắt đầu | Ô ngày kết thúc bôi đỏ `border-red-500`: "Ngày kết thúc hợp đồng phải sau ngày bắt đầu ít nhất 1 tháng". |
| Nhập tiền đặt cọc âm | Ô tiền cọc bôi đỏ: "Tiền cọc phòng phải lớn hơn hoặc bằng 0 VNĐ". |

---

### [FEAT-HIEN-07] Ký số hợp đồng online bằng HTML5 Canvas Signature Pad & Xác thực OTP
- **Git Branch**: `main`
- **Thành viên phụ trách**: Nguyễn Thanh Hiền
- **Controller & Method**: `AdminDashboardController@signContractView`, `signContract`, `sendOtpForContract`, `printContractPdf`
- **Endpoint**: `GET /smartroom/contract/{id}/sign`, `POST /smartroom/contract/{id}/sign`, `POST /smartroom/contract/{id}/send-otp`, `GET /smartroom/contract/{id}/pdf`
- **Middleware**: `web`

#### 1. Input Specification (Request Validation POST /sign)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `signature` | String (Base64) | Có | `required\|string\|starts_with:data:image/png;base64,` | "Vui lòng vẽ chữ ký tay của bạn vào khung ký trước khi xác nhận." |
| `otp_code` | String | Có | `required\|digits:6` | "Mã xác thực OTP phải gồm đúng 6 chữ số." |

#### 2. Business Logic Flow
1. **Kiểm tra trạng thái hợp đồng**: `Contract::findOrFail($id)`. Nếu `status == 'active'` hoặc đã có chữ ký, chặn thao tác và báo lỗi "Hợp đồng đã hoàn tất ký số trước đó".
2. **Xác minh OTP**: Tra cứu `OtpCode` theo SĐT người thuê, mã `code`, kiểm tra thời hạn hiệu lực (5 phút). Nếu sai hoặc hết hạn, từ chối giao dịch.
3. **Mở DB Transaction**:
   - Lưu chuỗi Base64 chữ ký vào trường `signature` của bảng `contracts`.
   - Cập nhật `signed_at = now()`, chuyển trạng thái hợp đồng `status = 'active'`.
   - Chuyển trạng thái phòng liên quan trong bảng `rooms` sang `status = 'occupied'` (Đang ở).
   - Đánh dấu OTP đã sử dụng.
4. **Commit & Xuất PDF**: Hợp đồng có hiệu lực pháp lý, kích hoạt link tải file PDF có gắn ảnh chữ ký hai bên.

#### 3. Database Operation
- **Bảng tác động**: `contracts` (cột `signature`, `signed_at`, `status`), `rooms` (cột `status`), `otp_codes`.

#### 4. Output Specification
- **Thành công (HTTP 200)**: `{"success": true, "message": "Ký số hợp đồng thành công!", "pdf_url": "/smartroom/contract/12/pdf"}`.
- **Thất bại (HTTP 422)**: `{"success": false, "message": "Mã OTP không chính xác hoặc đã hết thời gian hiệu lực."}`.

#### 5. UI/UX Specification & Xử lý lỗi
- **Khung Canvas**: Thẻ `<canvas id="signature-pad" class="border rounded-xl bg-white w-full h-48"></canvas>`, nút xóa chữ ký `#btn-clear-sig`.
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Chưa vẽ chữ ký vào Canvas mà bấm xác nhận | Khung Canvas rung nhẹ (animation shake), đổi viền sang đỏ: "Vui lòng vẽ chữ ký tay của bạn vào khung trước khi xác nhận ký kết". |
| Nhập sai mã OTP xác thực hoặc mã OTP hết hạn | Ô nhập OTP bôi đỏ: "Mã xác thực OTP không chính xác hoặc đã hết thời gian hiệu lực. Vui lòng bấm gửi lại mã mới". |

---

### [FEAT-HIEN-08] Quy trình Xác thực định danh chủ trọ lũy tiến (KYC CCCD & Premium Tích xanh PCCC/ANTT)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Nguyễn Thanh Hiền
- **Controller & Method**: `App\Http\Controllers\LandlordVerificationController@submitKyc`, `submitPremium`
- **Endpoint & Method**: `POST /smartroom/admin/verification/kyc`, `POST /smartroom/admin/verification/premium`
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation POST /kyc)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `landlord_name` | String | Có | `required\|string\|max:255` | "Vui lòng nhập họ và tên chủ trọ." |
| `cccd_number` | String | Có | `required\|regex:/^[0-9]{12}$/` | "Số CCCD phải gồm đúng 12 chữ số hợp lệ." |
| `bank_name` | String | Có | `required\|string\|max:100` | "Vui lòng chọn ngân hàng thụ hưởng." |
| `bank_account_number` | String | Có | `required\|string\|max:50` | "Số tài khoản ngân hàng không được để trống." |
| `cccd_front_image` | File | Có | `required\|image\|mimes:jpg,jpeg,png,webp\|max:10240` | "Ảnh mặt trước CCCD không quá 10MB." |
| `cccd_back_image` | File | Có | `required\|image\|mimes:jpg,jpeg,png,webp\|max:10240` | "Ảnh mặt sau CCCD không quá 10MB." |

#### 2. Business Logic Flow
1. **Lưu trữ tài liệu bảo mật**: Upload ảnh CCCD vào đĩa lưu trữ riêng biệt `storage/app/secure_documents/` (thư mục không public ra ngoài web).
2. **Mã hóa dữ liệu nhạy cảm**: Số CCCD và STK ngân hàng được mã hóa AES-256-GCM trước khi lưu vào bảng `landlord_profiles`.
3. **Tạo yêu cầu thẩm định**: Tạo bản ghi trong `landlord_verification_requests` với `type = 'kyc'`, `status = 'pending'`.
4. **Phản hồi**: Thông báo hồ sơ đang chờ Superadmin xét duyệt trong 24 giờ làm việc.

#### 3. Database Operation
- **Bảng tác động**: `landlord_profiles`, `landlord_verification_requests`, `landlord_verification_documents`.

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về màn hình trạng thái KYC kèm toast xanh: "Hồ sơ định danh KYC đã được gửi lên Ban Quản Trị thành công."

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Chưa tải đủ cả 2 mặt CCCD khi bấm gửi duyệt | Vùng upload bôi đỏ: "Vui lòng tải lên đầy đủ ảnh chụp cả hai mặt trước và sau của CCCD". |
| Tải file tài liệu dung lượng vượt quá giới hạn (> 10MB) | Thông báo lỗi: "Dung lượng file tải lên quá lớn (tối đa 10MB). Vui lòng nén file trước khi gửi". |

---

### [FEAT-HIEN-09] Bảng điều khiển kiểm duyệt hồ sơ định danh chủ trọ (Superadmin Verification)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Nguyễn Thanh Hiền
- **Controller & Method**: `App\Http\Controllers\AdminVerificationController@index`, `approve`, `reject`
- **Endpoint**: `GET /admin/verifications`, `POST /admin/verifications/{verification}/approve`, `POST /admin/verifications/{verification}/reject`
- **Middleware**: `auth`, `role:admin`

#### 1. Input Specification (Request Validation POST /reject)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `rejection_reason` | String | Có | `required\|string\|min:10\|max:1000` | "Vui lòng nhập lý do từ chối cụ thể (tối thiểu 10 ký tự)." |

#### 2. Business Logic Flow
1. **Kiểm tra quyền Superadmin**: Chỉ tài khoản có `role = 'admin'` mới được truy cập.
2. **Xử lý Phê duyệt (Approve)**:
   - Cập nhật trạng thái yêu cầu sang `approved`, ghi nhận `reviewed_by = auth()->id()`, `reviewed_at = now()`.
   - Nếu là hồ sơ KYC: Nâng cấp tài khoản chủ trọ từ `unverified_landlord` thành `landlord`, mở khóa chức năng nhận tiền VietQR.
   - Nếu là hồ sơ Premium: Cấp cờ Tích Xanh thẩm định uy tín (`listing_badge = 'premium_verified'`).
3. **Xử lý Từ chối (Reject)**:
   - Cập nhật `status = 'rejected'`, lưu `rejection_reason`.
   - Gửi thông báo hệ thống đến chủ trọ nêu rõ nguyên nhân để nộp lại giấy tờ.
4. **Ghi Audit Log**: Mọi thao tác duyệt/từ chối đều được ghi vào `audit_logs`.

#### 3. Database Operation
- **Bảng tác động**: `landlord_verification_requests`, `users`, `tenants`, `audit_logs`.

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về danh sách xét duyệt kèm flash message: "Đã phê duyệt hồ sơ định danh thành công!".
- **Thất bại (HTTP 422)**: Báo lỗi nếu thiếu lý do từ chối.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Admin bấm Từ chối nhưng bỏ trống lý do | Ô lý do từ chối bôi đỏ viền: "Vui lòng nhập lý do từ chối để chủ cơ sở biết và bổ sung lại giấy tờ hợp lệ". |
| Tài khoản không phải admin cố tình vào URL | Hệ thống chặn và trả về trang lỗi 403: "Truy cập bị từ chối. Bạn không có quyền hạn quản trị viên". |

---

### [FEAT-HIEN-10] Xem tài liệu pháp lý bảo mật bằng Signed URL (TTL 5 phút) & Đóng dấu Watermark
- **Git Branch**: `main`
- **Thành viên phụ trách**: Nguyễn Thanh Hiền
- **Controller & Method**: `App\Http\Controllers\VerificationDocumentController@show`, `stream`, `unlock`
- **Endpoint**: `GET /admin/verification-documents/{document}`, `GET /admin/verification-documents/{document}/stream`, `POST /admin/verification-documents/{document}/unlock`
- **Middleware**: `auth`, `role:admin`, `signed` (đối với route stream)

#### 1. Input Specification (Mở khóa tài liệu sau duyệt POST /unlock)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `reason` | String | Có | `required\|string\|min:10` | "Bắt buộc phải nhập lý do nghiệp vụ khi mở khóa xem tài liệu nhạy cảm." |
| `passkey_verified` | Boolean | Có | `accepted` | "Vui lòng xác thực Passkey sinh trắc học trước khi mở khóa." |

#### 2. Business Logic Flow
1. **Bảo vệ tài liệu thô**: File CCCD/PCCC được đặt ngoài `public`. Mọi yêu cầu xem file phải sinh đường dẫn có chữ ký thời hạn tạm thời (Signed URL) thông qua `URL::temporarySignedRoute('admin.verification-documents.stream', now()->addMinutes(5), ['document' => $id])`.
2. **Xác thực Signed URL**: Middleware `signed` kiểm tra tham số băm `signature` và thời hạn `expires`. Nếu hết hạn (> 5 phút), trả về HTTP 403 Forbidden.
3. **Đóng dấu bản quyền động (Dynamic Watermarking)**: Khi stream file ảnh ra response, ứng dụng tự động đóng dấu chìm: `"CHỈ DÙNG KIỂM DUYỆT - ADMIN: {name} - IP: {ip} - TIME: {now}"` chéo qua bức ảnh nhằm chống chụp màn hình tuồn ra ngoài.
4. **Ghi vết truy cập**: Ghi nhận hành vi xem tài liệu vào `admin_access_logs`.

#### 3. Database Operation
- **Bảng tác động**: `admin_access_logs`, `audit_logs`. Không sửa đổi file gốc.

#### 4. Output Specification
- **Thành công**: Stream nhị phân hình ảnh định dạng `image/jpeg` kèm header `Cache-Control: no-store, private`.
- **Thất bại (HTTP 403)**: "Liên kết xem tài liệu đã hết hạn vì lý do bảo mật. Vui lòng bấm làm mới trang."

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Truy cập tài liệu khi link ký đã quá thời gian 5 phút | Trang hiển thị lỗi 403: "Liên kết xem tài liệu đã hết hạn vì lý do an toàn bảo mật. Vui lòng bấm làm mới trang để nhận liên kết mới". |
| Mở khóa tài liệu nhạy cảm mà không nhập lý do | Ô lý do bôi đỏ: "Quy định bảo mật: Bạn bắt buộc phải ghi rõ lý do nghiệp vụ để lưu vào nhật ký kiểm toán Audit Log trước khi mở khóa tài liệu". |

---

### [FEAT-HIEN-11] Hệ thống Nhật ký kiểm toán bất biến (Immutable Audit Logs)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Nguyễn Thanh Hiền
- **Controller & Service**: `App\Http\Controllers\AdminVerificationController@auditLogs`, `App\Services\AuditLogService`
- **Endpoint**: `GET /admin/audit-logs`
- **Middleware**: `auth`, `role:admin`

#### 1. Input Specification (Bộ lọc tra cứu Audit Logs)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `action` | String | Không | `nullable\|string` | "Hành động lọc không hợp lệ." |
| `date_from` | Date | Không | `nullable\|date` | "Ngày bắt đầu lọc không hợp lệ." |
| `date_to` | Date | Không | `nullable\|date\|after_or_equal:date_from` | "Ngày kết thúc lọc phải sau hoặc bằng ngày bắt đầu." |

#### 2. Business Logic Flow
1. **Kiến trúc Bất biến (Immutable Append-only)**:
   - Bảng `audit_logs` được thiết lập Database Trigger cấm triệt để lệnh `UPDATE` và `DELETE`. Bất kỳ thao tác can thiệp sửa/xóa nào đều bị CSDL ném lỗi `SIGNAL SQLSTATE '45000'`.
2. **Cơ chế chuỗi băm xác thực toàn vẹn (Cryptographic Hash Chaining)**:
   - Mỗi bản ghi log mới được tính toán:
     `row_hash = sha256(prev_hash + user_id + action + target_model + target_id + reason + timestamp)`.
   - Tạo nên một chuỗi khối liên hoàn không thể bị chèn bản ghi giả mạo hoặc thay đổi dữ liệu cũ.
3. **Hiển thị giao diện**: Superadmin tra cứu danh sách log, hiển thị huy hiệu xác thực tính toàn vẹn (Integrity Verified: Xanh lá).

#### 3. Database Operation
- **Bảng tác động**: `audit_logs` (Chỉ cho phép `INSERT` và `SELECT`).

#### 4. Output Specification
- **Thành công (HTTP 200)**: Render view `admin.audit_logs` hiển thị danh sách dòng thời gian truy vết.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Người dùng hoặc script can thiệp API để sửa/xóa log | Hệ thống chặn lập tức với mã lỗi 405 Method Not Allowed: "Nhật ký kiểm toán là bất biến, nghiêm cấm mọi hành vi sửa/xóa dữ liệu!". |

---

### [FEAT-HIEN-12] AI Google Gemini tự động phân tích và sinh điều khoản hợp đồng thuê phòng
- **Git Branch**: `main`
- **Thành viên phụ trách**: Nguyễn Thanh Hiền
- **Controller & Method**: `App\Http\Controllers\AdminDashboardController@aiContractTerms`
- **Endpoint & Method**: `POST /smartroom/admin/ai/contract-terms`
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `requirements` | String | Có | `required\|string\|min:5\|max:1000` | "Vui lòng nhập ít nhất một yêu cầu quy định sinh hoạt (5 - 1000 ký tự)." |
| `room_type` | String | Không | `nullable\|string` | "Hạng phòng không hợp lệ." |

#### 2. Business Logic Flow
1. **Tiếp nhận tiêu chí quản lý**: Chủ trọ nhập các ý tưởng thực tế (ví dụ: *"cho nuôi mèo, không được dẫn người lạ qua đêm, xe điện phải sạc ban ngày"*).
2. **Tạo Prompt Pháp lý chuyên sâu**:
   - Gửi yêu cầu đến mô hình `gemini-2.5-flash` kèm luật tham chiếu: Luật Nhà ở Việt Nam và Bộ luật Dân sự.
   - Yêu cầu AI sinh văn bản điều khoản pháp lý chuẩn xác gồm 3 phần: Quyền hạn, Nghĩa vụ và Chế tài phạt khi vi phạm.
3. **Trả về kết quả**: Trả về văn bản đã chuẩn hóa định dạng Markdown/HTML để chèn tự động vào ô soạn thảo hợp đồng.

#### 3. Database Operation
- Không ghi CSDL trực tiếp tại bước này.

#### 4. Output Specification
- **Thành công (HTTP 200)**: `{"success": true, "terms": "ĐIỀU KHOẢN VỀ AN NINH VÀ SINH HOẠT CHUNG: 1. Bên thuê được phép nuôi thú cưng (mèo)..."}`.
- **Thất bại (HTTP 500)**: Trả về mẫu điều khoản mặc định dự phòng nếu mất kết nối AI.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống yêu cầu khi gọi AI sinh điều khoản | Ô nhập bôi đỏ viền: "Vui lòng nhập ít nhất một yêu cầu quy định sinh hoạt để AI phân tích". |
| Mất kết nối mạng đến Google Gemini API | Toast cảnh báo: "Không thể kết nối đến máy chủ AI. Hệ thống tạm thời nạp mẫu điều khoản tiêu chuẩn có sẵn". |

---

### [FEAT-HIEN-13] Quy trình Onboarding Step-Wizard đăng ký nhanh cho chủ trọ mới
- **Git Branch**: `Hien/Menu`
- **Thành viên phụ trách**: Nguyễn Thanh Hiền
- **Controller & Method**: `App\Http\Controllers\LandlordOnboardingController@create`, `store`, `verifyOtp`
- **Endpoint**: `GET /landlord/register`, `POST /landlord/register`, `POST /landlord/verify-otp`
- **Middleware**: `guest`

#### 1. Input Specification (Request Validation POST /landlord/register)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `name` | String | Có | `required\|string\|max:255` | "Vui lòng nhập họ và tên chủ cơ sở." |
| `phone` | String | Có | `required\|regex:/^(0[3\|5\|7\|8\|9])[0-9]{8}$/` | "Số điện thoại di động Việt Nam không hợp lệ." |
| `password` | String | Có | `required\|string\|min:6` | "Mật khẩu tối thiểu 6 ký tự." |
| `facility_name` | String | Có | `required\|string\|max:255` | "Vui lòng nhập tên cơ sở lưu trú ban đầu." |
| `facility_address`| String | Có | `required\|string\|max:255` | "Địa chỉ cơ sở lưu trú không được để trống." |
| `total_floors` | Integer | Có | `required\|integer\|min:1\|max:100` | "Số tầng phải từ 1 đến 100 tầng." |

#### 2. Business Logic Flow
1. **Kiểm tra trùng lặp SĐT**: Tính `phone_blind_index` và kiểm tra trong bảng `users`. Nếu tồn tại, trả về lỗi: *"Số điện thoại này đã được đăng ký tài khoản"*.
2. **Khởi tạo tài khoản & Tenant**:
   - Tạo bản ghi mới trong bảng `tenants` đại diện cho doanh nghiệp lưu trú của chủ trọ.
   - Tạo tài khoản `users` với `role = 'unverified_landlord'`, liên kết `tenant_id`.
   - Tạo cơ sở lưu trú ban đầu trong bảng `buildings`.
3. **Xác thực OTP kích hoạt**: Gửi mã OTP kích hoạt qua SMS/Zalo. Sau khi xác thực thành công, đăng nhập tự động và chuyển đến bảng điều khiển Overview.

#### 3. Database Operation
- **Bảng tác động**: `tenants`, `users`, `buildings`, `otp_codes`.

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng đến trang xác thực OTP hoặc trang quản trị `/smartroom/admin`.

#### 5. UI/UX Specification & Xử lý lỗi
- **Giao diện**: Thanh chỉ báo tiến trình Step Wizard (Bước 1: Tài khoản -> Bước 2: Cơ sở lưu trú -> Bước 3: Kích hoạt).
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống Tên cơ sở hoặc Địa chỉ tòa nhà | Các ô input bắt buộc bôi đỏ: "Vui lòng nhập tên cơ sở lưu trú" và "Địa chỉ không được để trống". |
| Số tầng hoặc số phòng dự kiến nhập số ≤ 0 | Báo lỗi: "Số tầng và số phòng dự kiến phải là số nguyên dương lớn hơn 0". |
"""
