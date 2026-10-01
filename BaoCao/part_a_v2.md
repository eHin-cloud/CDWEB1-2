## A. PHÂN HỆ DO NGUYỄN THANH HIỀN (NHÓM TRƯỞNG) PHỤ TRÁCH

### 1. ĐẶC TẢ KỸ THUẬT: KHỞI TẠO KIẾN TRÚC DỰ ÁN, DOCKER & HỆ THỐNG CƠ SỞ DỮ LIỆU

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-HIEN-01` |
| **Loại tác vụ (Type)** | Infrastructure / Database Architecture / CI-CD Pipeline |
| **Nhánh Git (Branches)** | `main`, `ci-cd`, `feature/env-setup-migrations` |
| **Người thực hiện (Assignee)** | Nguyễn Thanh Hiền (Nhóm Trưởng) |
| **Package / Standards** | Docker Compose v2, GitHub Actions, Laravel Pint, PHPUnit / Pest 3.x, MySQL 8.0 DDL |

#### 1. Phạm vi & Endpoints API / CLI Commands
| Method / CLI | Command / Path | Middlewares / Runners | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| CLI | `docker compose up -d` | Docker Daemon | Khởi chạy 4 services: app (PHP 8.3-FPM), web (Nginx), db (MySQL 8.0), redis (Redis 7.2) |
| CLI | `php artisan migrate:fresh --seed` | Artisan Console | Tái thiết lập cấu trúc 48 bảng CSDL và nạp dữ liệu mẫu ban đầu |
| CI | `.github/workflows/ci.yml` | GitHub Ubuntu Runner | Tự động kiểm tra chất lượng code (Pint) và chạy 64/64 Unit & Feature Tests |
| GET | `/` (Port 8088 / 8000) | Web Pipeline | Trang chào mừng hệ thống Renty xác nhận ứng dụng boot thành công |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
```text
[Developer / Git Push]
         |
         v
[GitHub Actions Runner]
         |---> 1. Setup PHP 8.3 + Composer Dependencies
         |---> 2. Run Laravel Pint: `vendor/bin/pint --test` (Linting Check)
         |---> 3. Khởi tạo Service Containers: MySQL 8.0 + Redis 7.2
         |---> 4. Exec Migrations: 48 Database Migration Files (Thứ tự bảng cha -> bảng con)
         |---> 5. Execute Test Suite: `php artisan test` (64/64 Tests Pass)
         |
    [100% Passed] ---> Cho phép tạo Pull Request vào nhánh `main` (Branch Protection)
```
- **Thứ tự thực thi Migration:** Bảng cha tạo trước bảng con (`tenants` $\to$ `roles` $\to$ `users` $\to$ `buildings` $\to$ `rooms` $\to$ `residents` $\to$ `contracts` $\to$ `bills` $\to$ `utility_records` $\to$ `equipment` $\to$ `iot_devices` $\to$ `iot_meter_telemetries`...).
- **Ràng buộc toàn vẹn:** 100% Foreign Keys có indexing đầy đủ, thiết lập `onDelete('cascade')` hoặc `onDelete('restrict')` theo đúng logic nghiệp vụ.

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Khởi động Hệ thống Thành công
- **HTTP Status Code:** `200 OK`
- **Response Format:** Giao diện Web Renty Portal hoặc JSON Healthcheck:
```json
{
  "status": "healthy",
  "app_env": "production",
  "database": "connected",
  "redis": "connected",
  "migrations_count": 48
}
```
##### 3.2. Quy tắc Xác thực Cấu hình Môi trường (.env Configuration Rules)
| Tên Biến (KEY) | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `APP_ENV` | String | Có | `in:local,production,testing` | Môi trường thực thi APP_ENV không hợp lệ. |
| `DB_CONNECTION` | String | Có | `in:mysql,sqlite` | Driver cơ sở dữ liệu không được hỗ trợ. |
| `DB_HOST` | String | Có | `required\|string` | Địa chỉ máy chủ MySQL (DB_HOST) không được để trống. |
| `PII_ENCRYPTION_KEY` | String | Có | `size:44\|starts_with:base64:` | Khóa mã hóa AES-256-GCM không đúng chuẩn Base64 256-bit. |
| `BLIND_INDEX_KEY` | String | Có | `required\|string\|min:32` | Khóa HMAC Blind Index phải có độ dài tối thiểu 32 ký tự. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Tạo lập Schema:** Khởi tạo 48 bảng với Engine `InnoDB`, Charset `utf8mb4`, Collation `utf8mb4_unicode_ci`.
- **Ràng buộc DDL:** Khóa ngoại `tenant_id` tham chiếu `tenants(id)` bắt buộc có B-Tree Index trên tất cả bảng nghiệp vụ.
- **Transaction Gotcha:** MySQL tự động commit (Implicit Commit) với câu lệnh DDL (`CREATE/ALTER TABLE`), do đó không thể rollback migration dở dang nếu xảy ra lỗi giữa chừng. Bắt buộc dùng `php artisan migrate:fresh` khi sửa schema.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Cổng 8088 bị xung đột do phần mềm khác đang chiếm dụng | Trình duyệt báo lỗi "Không thể kết nối máy chủ" (`ERR_CONNECTION_REFUSED`). Hiển thị hướng dẫn kỹ thuật kiểm tra cổng và đổi cổng ánh xạ sang 8000 trong `docker-compose.yml`. |
| Container MySQL đang khởi động chưa sẵn sàng khi chạy migrate | Script CLI báo lỗi `PDOException: [2002] Connection refused`. Script entrypoint tự động chờ qua lệnh `nc -z db 3306` hiển thị thông báo tiến độ xoay tròn trước khi chạy migrate. |
| Truy cập ứng dụng khi Nginx container chưa sẵn sàng | Trình duyệt trả về lỗi `502 Bad Gateway`. Hiển thị trang chờ dịch vụ đang khởi động kèm nút "Tải lại trang". |
| CI Pipeline Linting bị fail do sai định dạng code | GitHub Actions đánh dấu đỏ X, hiển thị log vị trí file vi phạm và khóa nút Merge Pull Request vào nhánh `main`. |
| Môi trường cục bộ thiếu tệp cấu hình `.env` | Giao diện hiển thị trang cảnh báo "No application encryption key has been specified", cung cấp nút tự động sinh khóa `php artisan key:generate`. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Container MySQL chưa khởi động xong khi chạy migrate | 500 / CLI | `DB_CONNECTION_REFUSED` | Ném `PDOException: [2002] Connection refused`. Script entrypoint chờ MySQL ready qua lệnh `nc -z db 3306` trước khi migrate. |
| Sai thứ tự khóa ngoại (Bảng con tạo trước bảng cha) | CLI | `MIGRATION_FK_VIOLATION` | Ném `ForeignKeyConstraintViolationException`. Đổi lại timestamp trên tên file migration để bảng cha chạy trước. |
| Thiếu biến môi trường mã hóa PII | 500 | `CONFIG_PII_KEY_MISSING` | Ném ngoại lệ `InvalidConfigurationException` ngay khi boot Service Container: "Khóa PII_ENCRYPTION_KEY không hợp lệ!". |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Container `docker compose up -d` khởi chạy thành công 4 services không bị restart loop.
- [ ] Lệnh `php artisan migrate:fresh` tạo đủ 48 bảng kèm khóa ngoại không phát sinh lỗi.
- [ ] Pipeline GitHub Actions chạy xanh 100% (Pint pass + 64/64 test cases pass).
- [ ] Truy cập `http://localhost:8088` trả về HTTP Status 200 OK.

---

### 2. ĐẶC TẢ KỸ THUẬT: KIẾN TRÚC MULTI-TENANCY DỮ LIỆU & PHÂN QUYỀN ĐA TẦNG RBAC

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-HIEN-02` |
| **Loại tác vụ (Type)** | Security Architecture / Access Control / Multi-tenancy |
| **Nhánh Git (Branches)** | `main`, `feature/multi-tenancy-rbac` |
| **Người thực hiện (Assignee)** | Nguyễn Thanh Hiền (Nhóm Trưởng) |
| **Package / Standards** | Laravel Eloquent Global Scopes, Custom Middleware, OWASP Top 10 Broken Access Control |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| ALL | `/smartroom/admin/*` | `auth`, `role:landlord,manager` | Phân hệ quản trị cơ sở lưu trú dành riêng cho Chủ trọ |
| ALL | `/smartroom/resident/*` | `auth`, `role:resident` | Phân hệ cổng thông tin dành riêng cho Cư dân thuê phòng |
| ALL | `/admin/*` | `auth`, `role:admin` | Phân hệ kiểm soát toàn sàn dành riêng cho Superadmin |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
```text
Client Request
      |
      v
[Authenticate Middleware] ---> Chưa đăng nhập: Redirect /login hoặc HTTP 401
      |
      v
[RoleMiddleware] ------------> auth()->user()->role không khớp: HTTP 403 Forbidden
      |
      v
[Eloquent Model Query]
      |
      +---> User KHÔNG PHẢI Superadmin:
      |        Global Scope tự động thêm: `WHERE tenant_id = auth()->user()->tenant_id`
      |        => Triệt tiêu 100% rủi ro rò rỉ dữ liệu chéo giữa các chủ cơ sở
      |
      +---> User LÀ Superadmin:
               Bỏ qua Global Scope => Xem được toàn bộ danh sách phòng toàn sàn
```

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Bị từ chối Quyền truy cập
- **HTTP Status Code:** `403 Forbidden`
```json
{
  "success": false,
  "error_code": "AUTH_FORBIDDEN_ACCESS",
  "message": "Truy cập bị từ chối. Bạn không có quyền thực hiện thao tác nghiệp vụ này!",
  "required_roles": ["landlord", "admin"]
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (Tenant Context)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `tenant_id` | Integer | Có | `required\|integer\|exists:tenants,id` | Đơn vị lưu trú không tồn tại trên hệ thống. |
| `role` | String | Có | `required\|in:admin,landlord,manager,resident,guest` | Vai trò người dùng không hợp lệ. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Trait `BelongsToTenant`:** Gắn vào 12 models chính (`Building`, `Room`, `Resident`, `Contract`, `Bill`, `UtilityRecord`, `Equipment`, `IotDevice`...).
- **Queue Jobs Execution:** Với Job xử lý ngầm (tính cước, IoT sync), gọi `TenantScope::withoutGlobalScope()` và truyền tường minh `tenant_id` trong câu lệnh query.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Chủ trọ cố tình gõ URL truy cập phòng của chủ trọ khác | Hệ thống chặn hiển thị dữ liệu, trả về HTTP 404 hoặc 403. Giao diện hiển thị Toast cảnh báo: "Bạn không có quyền truy cập dữ liệu cơ sở này!" và tự động điều hướng về Dashboard. |
| Nhân viên buồng phòng cố tình truy cập menu Cấu hình giá | Ẩn hoàn toàn mục menu trên thanh Sidebar; nếu cố tình nhập URL trực tiếp sẽ chuyển hướng về trang 403 Forbidden với thông báo: "Khu vực giới hạn quyền quản trị." |
| Phiên làm việc hết hạn (Session Expired) khi đang thao tác form | Mở Modal pop-up yêu cầu nhập lại mật khẩu xác thực tại chỗ mà không tải lại trang, giữ nguyên 100% dữ liệu đang nhập trên form. |
| Người dùng chưa đăng nhập bấm vào đường link nội bộ | Chuyển hướng ngay lập tức về trang `/login` kèm tham số `?redirect_to=` để tự động quay lại đúng trang sau khi đăng nhập thành công. |
| Tài khoản chủ trọ mới chưa có bất kỳ dữ liệu cơ sở nào | Màn hình Dashboard hiển thị trạng thái trống (Empty State) với banner hướng dẫn thân thiện: "Chào mừng bạn! Hãy bấm vào đây để tạo cơ sở kinh doanh đầu tiên." |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Chủ trọ A cố truy vấn ID phòng của Chủ trọ B | 404 | `MODEL_NOT_FOUND` | Global Scope thêm `WHERE tenant_id = A`, câu truy vấn không tìm thấy bản ghi. Trả về HTTP 404 sạch sẽ, tuyệt đối không lộ 403 để che giấu ID tồn tại. |
| Request API thiếu token xác thực Sanctum | 401 | `AUTH_UNAUTHENTICATED` | Trả về JSON: `{"success": false, "error_code": "AUTH_UNAUTHENTICATED", "message": "Yêu cầu phiên đăng nhập hợp lệ."}` |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] 100% câu lệnh truy vấn nghiệp vụ của chủ trọ tự động bổ sung điều kiện `tenant_id = ?`.
- [ ] Viết Unit Test chứng minh Chủ trọ A không thể xem, sửa hoặc xóa phòng của Chủ trọ B.
- [ ] Superadmin đăng nhập truy cập xem được toàn bộ danh sách phòng toàn sàn.
- [ ] Người dùng truy cập sai phân hệ bị chặn ngay tại Middleware với HTTP 403.

---

### 3. ĐẶC TẢ KỸ THUẬT: XÁC THỰC ĐĂNG KÝ, ĐĂNG NHẬP & CHỐNG TẤN CÔNG BRUTE-FORCE

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-HIEN-03` |
| **Loại tác vụ (Type)** | Core Security / Authentication / Rate Limiting |
| **Nhánh Git (Branches)** | `main`, `feature/auth-rate-limiting` |
| **Người thực hiện (Assignee)** | Nguyễn Thanh Hiền (Nhóm Trưởng) |
| **Package / Standards** | Laravel Fortify / Custom Auth, Redis Rate Limiter, Bcrypt (rounds = 12), HMAC Blind Index |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| POST | `/api/auth/register` | `api`, `guest`, `throttle:10,1` | Đăng ký tài khoản người dùng mới (Chủ trọ hoặc Cư dân) |
| POST | `/login` | `web`, `guest`, `throttle:30,1` | Đăng nhập hệ thống bằng Username hoặc Số điện thoại di động |
| POST | `/logout` | `web`, `auth` | Đăng xuất phiên làm việc, hủy Cookie và Session bảo mật |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
```text
User Input: (Username/SĐT + Password)
                 |
                 v
     [Kiểm tra định dạng SĐT Việt Nam: /^(0[3|5|7|8|9])[0-9]{8}$/]
                 |
        +--------+--------+
        | Khớp            | Không khớp
        v                 v
[Tính Blind Index HMAC]  [Query theo cột username]
        |                 |
        +--------+--------+
                 |
                 v
      [Tìm thấy User trong CSDL?]
        |                        |
      Không                     Có
        |                        |
        v                        v
[Băm giả lập chống Timing Attack] [Tài khoản status == 'inactive'?]
        |                                |
        v                         +------+------+
[Ném lỗi: Sai thông tin]          | Có          | Không
                                  v             v
                         [HTTP 403 Khóa]  [Hash::check($password, $user->password)]
                                                |
                                       +--------+--------+
                                       | Sai             | Đúng
                                       v                 v
                              [Rate Limiter +1]   [Reset Limiter]
                              [>= 5 lần: 429]    [Auth::login($user)]
                                                 [Redirect theo Role]
```

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Đăng nhập Thành công
- **HTTP Status Code:** `200 OK` (hoặc `Redirect 302` đối với Web Request)
- **Response Format:**
```json
{
  "success": true,
  "message": "Đăng nhập thành công!",
  "user": {
    "id": 15,
    "name": "Nguyễn Văn A",
    "role": "landlord",
    "redirect_url": "/smartroom/admin"
  }
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (Request Validation Rules)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `username` | String | Có | `required\|string\|max:100` | Tên đăng nhập hoặc số điện thoại không được để trống. |
| `password` | String | Có | `required\|string\|min:6` | Mật khẩu bắt buộc phải có tối thiểu 6 ký tự. |
| `remember` | Boolean | Không | `nullable\|boolean` | Giá trị ghi nhớ đăng nhập không hợp lệ. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Truy vấn bảng `users`:** Tra cứu theo `username` hoặc `phone_blind_index`.
- **Ghi nhận Rate Limiter:** Khóa Redis `login_attempts:{ip}:{username}` với thời gian sống TTL 60 giây.
- **Tạo Tenant mới:** Nếu đăng ký `role === 'landlord'`, tạo bản ghi `tenants` trong cùng `DB::transaction` và gán `user.tenant_id`.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Nhập sai mật khẩu liên tiếp quá 5 lần | Nút "Đăng nhập" bị khóa (disabled), xuất hiện đồng hồ đếm ngược 60 giây và dòng chữ đỏ: "Bạn đã nhập sai 5 lần. Vui lòng thử lại sau {sec} giây." |
| Để trống trường Email hoặc Mật khẩu khi bấm Đăng nhập | Viền đỏ ô input tức thì, hiển thị thông báo lỗi ngay dưới trường dữ liệu: "Vui lòng nhập đầy đủ thông tin tài khoản." |
| Nhập email sai định dạng (ví dụ: `abc@`) | Ô input chuyển viền cam kèm tooltip nhắc nhở: "Định dạng email chưa hợp lệ (ví dụ: user@renty.vn)." |
| Người dùng bấm nút Đăng ký nhiều lần liên tiếp do mạng chậm | Nút bấm chuyển sang trạng thái Loading (Spinner xoay tròn), hiển thị text "Đang xử lý..." và vô hiệu hóa click để chống tạo trùng tài khoản. |
| Đăng nhập tài khoản trên thiết bị lạ | Hiển thị Modal thông báo phát hiện đăng nhập mới kèm cảnh báo gửi về email đăng ký của người dùng. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Thử sai mật khẩu lần thứ 6 liên tiếp | 429 | `AUTH_RATE_LIMIT_EXCEEDED` | Trả về Header `Retry-After: 60`. JSON: `{"success": false, "error_code": "AUTH_RATE_LIMIT_EXCEEDED", "retry_after": 60}`. |
| Tài khoản bị khóa vi phạm tiêu chuẩn | 403 | `AUTH_ACCOUNT_LOCKED` | Ngắt phiên, trả về mã lỗi `AUTH_ACCOUNT_LOCKED` kèm thông báo liên hệ Quản trị viên. |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Nhập sai mật khẩu 5 lần liên tiếp sẽ bị khóa form trong 60 giây.
- [ ] Đăng nhập thành công bằng cả Username và Số điện thoại di động Việt Nam.
- [ ] Mật khẩu được băm an toàn bằng thuật toán Bcrypt với `rounds = 12`.
- [ ] Điều hướng chính xác theo vai trò: Admin $\to$ `/list`, Landlord $\to$ `/smartroom/admin`, Resident $\to$ `/smartroom/resident`.

---

### 4. ĐẶC TẢ KỸ THUẬT: XÁC THỰC KHÔNG MẬT KHẨU BẰNG WEBAUTHN / FIDO2 PASSKEY (LOGIN)

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-HIEN-04` |
| **Loại tác vụ (Type)** | Security / Authentication Feature |
| **Nhánh Git (Branches)** | `main`, `feature/webauthn-passkey-login` |
| **Người thực hiện (Assignee)** | Nguyễn Thanh Hiền (Nhóm Trưởng) |
| **Package / Standards** | `laragear/webauthn` (Laravel 11), W3C WebAuthn Level 3 / FIDO2 |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| POST | `/webauthn/login/options` | `web`, `throttle:30,1` | Tạo PublicKeyCredentialRequestOptions (Challenge ngẫu nhiên 32 bytes) |
| POST | `/webauthn/login` | `web`, `throttle:10,1` | Tiếp nhận và xác thực chữ ký số sinh trắc học từ Client |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
```text
Browser (Client)                                  Laravel Server (Laragear\WebAuthn)
      |                                                             |
      |-- 1. POST /webauthn/login/options ------------------------->|
      |                                                             |-- Sinh ngẫu nhiên Challenge 32 bytes
      |                                                             |-- Lưu Challenge + User Intent vào Session
      |<-- 2. HTTP 200 OK (PublicKeyCredentialRequestOptions) ------|
      |
      |-- 3. Gọi navigator.credentials.get({ publicKey: options })
      |      (Hiển thị prompt: Vân tay / FaceID / Windows Hello)
      |-- 4. Trình xác thực (Authenticator) ký Challenge bằng Private Key
      |
      |-- 5. POST /webauthn/login (Assertion Response) ------------>|
      |                                                             |-- 1. Kiểm tra Origin (RP ID) & Session Challenge
      |                                                             |-- 2. Tìm Public Key trong `webauthn_credentials`
      |                                                             |-- 3. Verify Asymmetric Signature
      |                                                             |-- 4. Check Counter: chống Replay / Cloning Attack
      |                                                             |-- 5. Cập nhật Counter mới & Auth::login($user)
      |<-- 6. HTTP 200 OK {'status': 'ok', 'redirect': '...'} ------|
```

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output của POST /webauthn/login khi thành công
- **HTTP Status Code:** `200 OK`
- **Response Body:**
```json
{
  "status": "success",
  "message": "Xác thực Passkey thành công.",
  "redirect": "/smartroom/admin"
}
```
##### 3.2. Quy tắc xác thực dữ liệu đầu vào (Request Validation Rules) cho POST /webauthn/login
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `id` | String | Có | `required\|string` | ID định danh Passkey không hợp lệ. |
| `rawId` | String | Có | `required\|string` | Mã khóa gốc không hợp lệ. |
| `type` | String | Có | `required\|in:public-key` | Loại thông tin xác thực phải là public-key. |
| `response.clientDataJSON` | String | Có | `required\|string` | Thiếu clientDataJSON. |
| `response.authenticatorData` | String | Có | `required\|string` | Thiếu dữ liệu trạng thái bộ xác thực. |
| `response.signature` | String | Có | `required\|string` | Chữ ký điện tử sinh trắc học không hợp lệ. |
| `response.userHandle` | String | Không | `nullable\|string` | User Handle không hợp lệ. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Bảng `webauthn_credentials`:**
  - Truy vấn theo `id` (credential ID) để lấy `public_key`, `counter`, `user_id`.
  - Ghi nhận: Cập nhật `counter = $incomingCounter`, `updated_at = NOW()`.
- **Bảng `users`:** Lấy thông tin user tương ứng qua `user_id` để thiết lập session đăng nhập (`Auth::login($user)`).

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Thiết bị hoặc trình duyệt không hỗ trợ WebAuthn sinh trắc học | Nút "Đăng nhập bằng Passkey" tự động ẩn hoặc bị mờ kèm tooltip giải thích: "Thiết bị chưa hỗ trợ Windows Hello / Touch ID." |
| Người dùng bấm nút "Hủy" trên hộp thoại quét vân tay / FaceID | Bắt lỗi `NotAllowedError` từ Web API, hiển thị Toast cảnh báo nhẹ nhàng: "Xác thực sinh trắc học bị hủy. Bạn có thể chọn đăng nhập bằng mật khẩu." |
| Thao tác quét vân tay bị quá hạn (Timeout > 60 giây) | Hộp thoại thông báo: "Quá thời gian chờ phản hồi sinh trắc học. Vui lòng bấm để thử lại!" kèm nút kích hoạt lại. |
| Người dùng mở trang web ở chế độ duyệt web ẩn danh (Incognito) | Hiển thị thông báo lưu ý: "Chế độ ẩn danh có thể không truy cập được khóa bảo mật phần cứng TPM." |
| Nhập tên tài khoản chưa từng đăng ký Passkey trên thiết bị này | Hiển thị gợi ý: "Tài khoản chưa kích hoạt Passkey trên máy này. Vui lòng đăng nhập mật khẩu trước để tạo khóa." |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Nhập sai mật khẩu tài khoản người yêu cầu | 403 | `PII_INVALID_PASSWORD` | Ném lỗi HTTP 403: "Mật khẩu xác thực không chính xác. Thao tác xem PII đã bị hủy!" |
| Tag GCM không khớp (Dữ liệu bị can thiệp) | 500 | `PII_TAMPER_DETECTED` | Ném `DecryptionException`: "Dữ liệu mã hóa đã bị thay đổi trái phép. Tính toàn vẹn bị vi phạm!" |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Dữ liệu CCCD, SĐT trong tệp Database Dump chỉ hiển thị chuỗi Base64 mã hóa.
- [ ] Tìm kiếm người dùng bằng Số điện thoại thực tế qua hàm Blind Index với tốc độ truy vấn $< 5\text{ ms}$.
- [ ] Gọi API Secure Reveal bắt buộc ghi nhận 1 dòng kiểm toán vào `audit_logs`.
- [ ] Cố tình sửa 1 byte trong ciphertext sẽ bị hàm giải mã ném lỗi bảo vệ toàn vẹn Tag tức thì.

---

---

### 5. ĐẶC TẢ KỸ THUẬT: BẢO MẬT DỮ LIỆU ĐỊNH DANH PII & API GIẢI MÃ AN TOÀN (SECURE REVEAL)

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-HIEN-05` |
| **Loại tác vụ (Type)** | Data Security / AES-256-GCM / Blind Index / PII Protection |
| **Nhánh Git (Branches)** | `main`, `feature/pii-aes-encryption-blind-index` |
| **Người thực hiện (Assignee)** | Nguyễn Thanh Hiền (Nhóm Trưởng) |
| **Package / Standards** | OpenSSL AES-256-GCM, Hash HMAC-SHA256, Nghị định số 13/2023/NĐ-CP |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| POST | `/smartroom/admin/pii/reveal` | `auth`, `tenant.scope`, `throttle:5,1` | API giải mã an toàn số CCCD/STK hiển thị tạm thời có xác thực |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
- **Mã hóa Dữ liệu Nhạy cảm (AES-256-GCM):**
  - Mọi trường dữ liệu định danh (Số CCCD, Số tài khoản ngân hàng) được mã hóa bằng chuẩn AES-256-GCM trước khi ghi vào CSDL.
  - Mỗi bản ghi sinh ngẫu nhiên một Vector khởi tạo (IV - 12 bytes) và Authentication Tag (16 bytes) để chống can thiệp sửa đổi dữ liệu.
- **Tìm kiếm Không Cần Giải Mã (HMAC-SHA256 Blind Index):**
  - Tính chỉ mục ẩn Blind Index: $BIndex = \text{HMAC-SHA256}(Value, K_{\text{bindex}})$.
  - Khi chủ trọ tìm kiếm số CCCD: Hệ thống băm giá trị tìm kiếm và truy vấn theo cột `id_card_number_bindex` với độ phức tạp $O(1)$, không cần quét toàn bảng và không cần giải mã.

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Giải Mã Thành Công
- **HTTP Status Code:** `200 OK`
```json
{
  "success": true,
  "revealed_value": "079098012345",
  "expires_in_seconds": 30,
  "message": "Dữ liệu PII đã được giải mã an toàn. Thông tin sẽ tự động ẩn sau 30 giây."
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /smartroom/admin/pii/reveal`)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `entity_type` | String | Có | `required|in:tenant,landlord` | Thực thể cần giải mã không hợp lệ. |
| `entity_id` | Integer | Có | `required|integer` | ID đối tượng không được để trống. |
| `field_name` | String | Có | `required|in:id_card_number,bank_account` | Trường dữ liệu yêu cầu giải mã không hợp lệ. |
| `auth_password` | String | Có | `required|string` | Mật khẩu xác thực bắt buộc để giải mã dữ liệu PII. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Bảng tác động:** `tenants` (cột `id_card_number_encrypted`, `id_card_number_bindex`), `audit_logs` (ghi nhận sự kiện giải mã PII).

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Số CCCD hiển thị trên giao diện quản trị mặc định | Tự động che định dạng `079******123`, tuyệt đối không để lộ 12 số thực trên màn hình chung. |
| Bấm biểu tượng "Con mắt" để xem số CCCD gốc | Hiển thị Modal yêu cầu nhập mật khẩu cấp 2 hoặc mã PIN xác thực trước khi kích hoạt API giải mã an toàn. |
| Sau 30 giây hiển thị số CCCD thực tế | Giao diện tự động làm mờ và ẩn lại số CCCD bằng dấu sao `*` để chống nhìn trộm khi rời màn hình. |
| API giải mã gặp sự cố hoặc khóa mã hóa bị lỗi | Hiển thị nhãn bảo mật xám: "[Dữ liệu mã hóa không khả dụng]" thay vì hiển thị chữ `null` hoặc làm sập giao diện. |
| Thao tác in trang web hoặc chụp ảnh màn hình | Áp dụng CSS `@media print` tự động che đen toàn bộ các trường thông tin nhận dạng cá nhân PII. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Nhập sai mật khẩu xác thực giải mã PII | 401 | `PII_AUTH_FAILED` | Ném lỗi: "Mật khẩu xác thực giải mã PII không chính xác." |
| Bấm giải mã PII quá 5 lần trong 1 phút | 429 | `PII_RATE_LIMIT_EXCEEDED` | Throttling `throttle:5,1` khóa truy cập tạm thời 60 giây và ghi log cảnh báo an ninh. |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Dữ liệu CCCD được lưu dưới dạng bản mã AES-256-GCM trong CSDL, không thể đọc trộm khi dump database.
- [ ] Tìm kiếm cư dân theo CCCD hoạt động chuẩn xác qua Blind Index $O(1)$.
- [ ] API Reveal giải mã an toàn, tự động ẩn sau 30 giây trên giao diện.

### 6. ĐẶC TẢ KỸ THUẬT: QUẢN LÝ HỢP ĐỒNG THUÊ ĐIỆN TỬ & KÝ SỐ CANVAS PAD + OTP

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-HIEN-06` |
| **Loại tác vụ (Type)** | Core Business / Digital Contract / Digital Signature & OTP |
| **Nhánh Git (Branches)** | `main`, `feature/e-contract-canvas-otp` |
| **Người thực hiện (Assignee)** | Nguyễn Thanh Hiền (Nhóm Trưởng) |
| **Package / Standards** | HTML5 Canvas API, Twilio / Zalo ZNS SMS Gateway, Barryvdh DomPDF |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| POST | `/smartroom/admin/contract` | `auth`, `role:landlord` | Khởi tạo hợp đồng thuê mới ở trạng thái `draft` |
| POST | `/smartroom/contract/{id}/send-otp` | `web`, `throttle:5,1` | Gửi mã OTP 6 số xác thực về số điện thoại cư dân |
| POST | `/smartroom/contract/{id}/sign` | `web`, `throttle:10,1` | Tiếp nhận ảnh chữ ký Canvas và mã OTP, kích hoạt hợp đồng |
| GET | `/smartroom/contract/{id}/pdf` | `auth` | Xuất và tải tệp hợp đồng PDF có đầy đủ chữ ký số |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
```text
Cư dân xem hợp đồng online -> Bấm "Gửi mã xác thực"
             |
             v
[POST /smartroom/contract/{id}/send-otp]
             |---> Sinh mã OTP ngẫu nhiên 6 chữ số: mt_rand(100000, 999999)
             |---> contracts.otp_code = Hash::make($otp), otp_expires_at = now() + 5 min
             |---> Gửi SMS/Zalo ZNS đến SĐT cư dân
             |
Cư dân vẽ chữ ký tay trên Canvas + Nhập mã OTP 6 số
             |
             v
[POST /smartroom/contract/{id}/sign]
             |---> 1. Kiểm tra: contract.is_signed == false
             |---> 2. Kiểm tra: Hash::check($otp, contract.otp_code) && !otp_expires_at.isPast()
             |---> 3. DB::transaction:
             |          contracts.signature = $signature_base64
             |          contracts.is_signed = true
             |          contracts.status = 'active'
             |          contracts.signed_at = now()
             |          contracts.signer_ip = request()->ip()
             |---> 4. Dispatch Job: GenerateContractPdfJob (Queue: documents)
             |<--- Trả về HTTP 200 {status: "success", pdf_url: "..."}
```

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Ký Hợp đồng Thành công
- **HTTP Status Code:** `200 OK`
```json
{
  "success": true,
  "message": "Ký hợp đồng thuê phòng điện tử thành công!",
  "contract": {
    "id": 85,
    "status": "active",
    "is_signed": true,
    "signed_at": "2026-10-01 14:00:00",
    "pdf_download_url": "/smartroom/contract/85/pdf"
  }
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /smartroom/contract/{id}/sign`)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `signature` | String | Có | `required\|string\|starts_with:data:image/png;base64,` | Chữ ký vẽ tay bắt buộc phải là định dạng ảnh Base64 PNG. |
| `otp_code` | String | Có | `required\|string\|size:6` | Mã OTP xác thực phải gồm đúng 6 chữ số. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Bảng `contracts`:** Cập nhật `signature`, `is_signed`, `signed_at`, `status = 'active'`, `signer_ip`. Hủy `otp_code = null`.
- **Bảng `rooms`:** Chuyển trạng thái phòng sang `status = 'occupied'`.
- **Hàng đợi Redis:** Đẩy tác vụ `GenerateContractPdfJob` vào hàng đợi `documents` để xử lý xuất PDF bất đồng bộ.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Bấm nút "Hoàn tất hợp đồng" khi chưa ký vào khung Canvas | Viền đỏ khung ký, rung lắc nhẹ kèm thông báo: "Vui lòng ký tên vào khung cảm ứng trước khi xác nhận!" |
| Nhập mã OTP 6 số sai hoặc mã đã hết hạn 5 phút | Ô nhập OTP báo lỗi màu đỏ, xóa sạch các ô nhập và hiển thị nút bấm: "Gửi lại mã OTP mới qua SMS/Zalo". |
| Bấm nút "Tải tệp PDF hợp đồng" khi máy chủ đang render | Hiển thị thanh tiến trình (ProgressBar) "Đang kết xuất tệp PDF có chữ ký số...", tránh người dùng bấm liên tục. |
| Ký hợp đồng trên màn hình điện thoại xoay dọc | Giao diện tự động gợi ý xoay ngang màn hình (Landscape Mode) để mở rộng vùng vẽ chữ ký thoải mái. |
| Trình duyệt không hỗ trợ mở xem trực tiếp file PDF | Tự động kích hoạt cơ chế tải về tệp tin `.pdf` trực tiếp vào thư mục Downloads của thiết bị. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Cố ký hợp đồng đã hoàn tất trước đó | 400 | `CONTRACT_ALREADY_SIGNED` | Chặn lại: "Hợp đồng này đã được ký số hoàn tất trước đó. Không thể ký lại!" |
| Nhập sai mã OTP | 422 | `CONTRACT_OTP_INVALID` | Tăng bộ đếm sai OTP. Quá 3 lần: Hủy mã OTP cũ, bắt buộc yêu cầu cấp lại mã mới. |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Nhập đúng mã OTP và vẽ chữ ký kích hoạt hợp đồng sang `active`.
- [ ] Mã OTP tự động hết hiệu lực sau đúng 300 giây (5 phút).
- [ ] Tệp Hợp đồng PDF kết xuất chứa đầy đủ điều khoản pháp lý, thông tin 2 bên và chữ ký số rõ nét.
- [ ] Ghi nhận địa chỉ IP và dấu thời gian ký tên làm bằng chứng pháp lý trong CSDL.

---

### 7. ĐẶC TẢ KỸ THUẬT: THẨM ĐỊNH KYC CHỦ TRỌ, SIGNED URL WATERMARK & AUDIT LOGS BẤT BIẾN

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-HIEN-07` |
| **Loại tác vụ (Type)** | Admin Moderation / Document Security / Immutable Audit Logs |
| **Nhánh Git (Branches)** | `main`, `feature/kyc-signed-url-audit-log` |
| **Người thực hiện (Assignee)** | Nguyễn Thanh Hiền (Nhóm Trưởng) |
| **Package / Standards** | Laravel Signed URLs, PHP GD Watermarking, MySQL SHA-256 Hash Chain Triggers |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| POST | `/admin/verifications/{id}/approve` | `auth`, `role:admin` | Phê duyệt hồ sơ KYC, cấp Tích Xanh thẩm định và mở cổng VietQR |
| POST | `/admin/verifications/{id}/reject` | `auth`, `role:admin` | Từ chối hồ sơ kèm lý do chi tiết yêu cầu bổ sung |
| GET | `/admin/verification-documents/{id}/stream` | `auth`, `signed` | Xem tài liệu định danh bảo mật có đóng dấu Watermark chéo 45 độ |
| GET | `/admin/audit-logs` | `auth`, `role:admin` | Xem nhật ký kiểm toán chuỗi băm bất biến (Immutable Hash Chain) |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
```text
Admin yêu cầu xem ảnh CCCD KYC
               |
               v
[Xác thực Middleware `signed` (HMAC SHA-256 trên URL, TTL 300s)]
        |                                    |
      Hết hạn / Sai URL                     Hợp lệ
        |                                    |
        v                                    v
 [HTTP 403 Forbidden]               [Đọc ảnh từ Storage an toàn]
                                             |
                                             v
                           [PHP GD: Áp dấu Watermark chéo 45 độ]
                           "[XEM BỞI: ADMIN_{ID} - {IP} - {DATETIME}]"
                                             |
                                             v
                           [Ghi nhận Hash Chain vào bảng audit_logs]
                           row_hash = SHA256(id + model + action + prev_hash)
                                             |
                                             v
                           [Stream ảnh nhị phân trực tiếp về Trình duyệt]
```

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Phê duyệt KYC Thành công
- **HTTP Status Code:** `200 OK`
```json
{
  "success": true,
  "message": "Đã phê duyệt hồ sơ định danh KYC thành công. Chủ cơ sở đã được cấp Tích Xanh thẩm định!",
  "badge": "verified",
  "can_receive_vietqr": true
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /admin/verifications/{id}/reject`)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `reason` | String | Có | `required\|string\|min:10\|max:500` | Lý do từ chối hồ sơ KYC bắt buộc phải có từ 10 đến 500 ký tự. |
| `reject_fields` | Array | Có | `required\|array\|min:1` | Danh sách giấy tờ không đạt chuẩn bắt buộc phải có ít nhất 1 mục. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Bảng `landlord_profiles`:** `status = 'approved'`.
- **Bảng `tenants`:** `verification_status = 'kyc_verified'`, `listing_badge = 'verified'`.
- **Database Triggers trên `audit_logs`:**
  ```sql
  CREATE TRIGGER trg_audit_logs_no_update BEFORE UPDATE ON audit_logs FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Audit log is immutable!';
  CREATE TRIGGER trg_audit_logs_no_delete BEFORE DELETE ON audit_logs FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Audit log is immutable!';
  ```

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Bảng danh sách hồ sơ KYC đang tải từ máy chủ | Hiển thị hiệu ứng Skeleton loading 5 dòng nhấp nháy xám thay vì để bảng trắng trơn gây hiểu lầm. |
| Quản trị viên bấm "Từ chối hồ sơ" nhưng không nhập lý do | Khóa nút gửi, bôi đỏ trường nhập lý do và yêu cầu bắt buộc nhập tối thiểu 10 ký tự giải thích lý do từ chối. |
| Ảnh CCCD chụp bị xoay ngang hoặc xoay ngược | Cung cấp công cụ xoay ảnh (Rotate 90/180/270 độ) và phóng to (Zoom) trực tiếp trên Modal kiểm duyệt. |
| Chọn ngày bắt đầu lớn hơn ngày kết thúc trên bộ lọc Audit Log | Báo lỗi ngay lập tức dưới bộ chọn ngày: "Ngày bắt đầu lọc không được lớn hơn ngày kết thúc." |
| Bảng nhật ký Audit Log có trên 1.000 bản ghi | Tự động kích hoạt phân trang kết hợp cuộn ảo (Virtual Scrolling) đảm bảo giao diện cuộn mượt mà 60fps. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Signed URL bị can thiệp tham số | 403 | `INVALID_SIGNATURE` | Trả về lỗi: "Chữ ký xác thực URL không hợp lệ hoặc liên kết đã quá hạn." |
| Cố tình chạy lệnh UPDATE hoặc DELETE trên `audit_logs` | SQLSTATE 45000 | `DB_TRIGGER_IMMUTABLE` | Trigger chặn đứng câu lệnh và ném exception: "Audit log is immutable!". Không thể xóa log ngay cả khi chiếm quyền admin. |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Thử thực hiện lệnh `DELETE FROM audit_logs` trong MySQL Console, hệ thống báo lỗi SQLSTATE 45000 và giữ nguyên dữ liệu.
- [ ] Truy cập đường dẫn xem ảnh KYC không có chữ ký số hợp lệ bị chặn với HTTP 403.
- [ ] Tệp ảnh tài liệu mở qua Signed URL có dòng Watermark chéo hiển thị rõ thông tin Admin ID và IP.
- [ ] Phê duyệt KYC thành công cập nhật trạng thái hồ sơ và hiển thị Tích Xanh trên Cổng Renty.

---

### 8. ĐẶC TẢ KỸ THUẬT: TRỢ LÝ AI TỰ ĐỘNG PHÂN TÍCH RỦI RO & SINH ĐIỀU KHOẢN HỢP ĐỒNG

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-HIEN-08` |
| **Loại tác vụ (Type)** | Generative AI / Legal Automation / Prompt Engineering |
| **Nhánh Git (Branches)** | `main`, `feature/ai-contract-legal-clauses` |
| **Người thực hiện (Assignee)** | Nguyễn Thanh Hiền (Nhóm Trưởng) |
| **Package / Standards** | Google Gemini 2.5 Flash API, Laravel HTTP Client, JSON Schema Parser, Civil Code 2015 |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| POST | `/smartroom/admin/ai/generate-clauses` | `auth`, `role:landlord`, `throttle:15,1` | Phân tích quy định riêng của chủ trọ và sinh điều khoản pháp lý chuẩn |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
- **Bước 1:** Chủ cơ sở nhập quy chế riêng (ví dụ: Giờ giới nghiêm 23h, cấm nuôi chó mèo, phạt trễ tiền phòng 50k/ngày).
- **Bước 2:** Hệ thống gửi Prompt đến Google Gemini 2.5 Flash kèm System Instructions chuyên gia luật hợp đồng:
  - So khớp với Bộ luật Dân sự 2015 và Luật Nhà ở.
  - Chuyển đổi ngôn ngữ đời thường thành các điều khoản pháp lý chặt chẽ.
  - Đánh giá chỉ số rủi ro (Risk Score: 1-10) và gắn cảnh báo nếu có điều khoản trái pháp luật.
- **Bước 3:** Nếu Gemini API timeout quá 6 giây: Tự động kích hoạt cơ chế Fallback nạp bộ điều khoản mẫu chuẩn hóa sẵn có trong CSDL.

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Sinh Điều khoản Thành công
- **HTTP Status Code:** `200 OK`
```json
{
  "success": true,
  "risk_score": 2,
  "risk_level": "low",
  "clauses": [
    {
      "clause_number": "Điều 5.1",
      "title": "Thời gian ra vào và an ninh trật tự",
      "content": "Bên B có trách nhiệm bảo đảm trật tự chung sau 23h00 hàng ngày; trường hợp có khách lưu trú qua đêm phải đăng ký trước với Bên A theo quy định tạm trú."
    }
  ],
  "warnings": [],
  "used_ai": true
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /smartroom/admin/ai/generate-clauses`)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `house_rules` | String | Có | `required\|string\|min:10\|max:1500` | Nội dung quy chế riêng bắt buộc từ 10 đến 1500 ký tự. |
| `rental_type` | String | Có | `required\|in:month,day,hour` | Hình thức cho thuê không hợp lệ. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Lưu trữ CSDL:** Khi chủ trọ chấp nhận điều khoản, lưu chuỗi JSON điều khoản vào cột `contracts.custom_clauses`.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Quá trình AI phân tích kéo dài 3-5 giây | Hiển thị hiệu ứng quét Laser và dòng trạng thái: "Gemini AI đang rà soát 24 điều khoản pháp lý...". |
| API Gemini bị quá tải hoặc phản hồi chậm (> 10s) | Tự động kích hoạt bộ quy chuẩn pháp lý dự phòng ngoại tuyến, thông báo Toast: "Đã phân tích theo bộ quy tắc chuẩn." |
| Hợp đồng không phát hiện điều khoản rủi ro nào | Hiển thị huy hiệu xanh lá cây: "Hợp đồng an toàn - Không phát hiện điều khoản bất lợi cho người thuê." |
| Phát hiện điều khoản phạt cọc vi phạm pháp luật | Bôi đỏ đoạn văn bản vi phạm và hiển thị thẻ cảnh báo ghi rõ căn cứ pháp luật theo Bộ luật Dân sự 2015. |
| Nhấn nút "Sao chép khuyến nghị chỉnh sửa" | Hiển thị Toast thông báo màu xanh: "Đã sao chép nội dung sửa đổi vào bộ nhớ tạm!" trong 2 giây. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Mất mạng hoặc Gemini API timeout > 6s | 200 | `FALLBACK_TEMPLATE_USED` | Bắt lỗi `ConnectionException`. Kích hoạt Fallback nạp mẫu điều khoản chuẩn. Trả về cờ `"used_ai": false`. Tuyệt đối không ném lỗi 500 ra client. |
| Phát hiện điều khoản vi phạm pháp luật dân sự | 200 | `LEGAL_RISK_WARNING` | Gắn nhãn cảnh báo đỏ: "Điều khoản tự ý tịch thu đồ đạc vi phạm pháp luật dân sự. AI đã tự động điều chỉnh thành cơ chế niêm phong có biên bản." |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Sinh các điều khoản pháp lý hoàn chỉnh từ văn bản đời thường trong dưới 4 giây.
- [ ] Khi ngắt kết nối mạng của Gemini, hệ thống tự động fallback sang mẫu chuẩn không ném lỗi 500.
- [ ] Chèn các điều khoản đã sinh vào mẫu hợp đồng chính thức chỉ với 1 click.

---

### 9. ĐẶC TẢ KỸ THUẬT: QUY TRÌNH ONBOARDING THIẾT LẬP NHANH CƠ SỞ LƯU TRÚ BAN ĐẦU

| Thông Tin Chung | Chi Tiết |
| :--- | :--- |
| **Mã Chức Năng (Feature Code)** | `FEAT-HIEN-09` |
| **Loại tác vụ (Type)** | User Onboarding / Wizard Setup / Bulk Room Generation |
| **Nhánh Git (Branches)** | `main`, `feature/landlord-onboarding-wizard` |
| **Người thực hiện (Assignee)** | Nguyễn Thanh Hiền (Nhóm Trưởng) |
| **Package / Standards** | Multi-step Blade Wizard, Alpine.js, Laravel Database Transactions |

#### 1. Phạm vi & Endpoints API
| Method | Endpoint URL | Middlewares | Mô Tả Chức Năng Nghiệp Vụ |
| :---: | :--- | :--- | :--- |
| GET | `/landlord/register` | `auth` | Hiển thị giao diện Wizard 3 bước thiết lập nhanh cơ sở lưu trú |
| POST | `/landlord/onboarding/complete` | `auth` | Xử lý hoàn tất Wizard, sinh hàng loạt tòa nhà, phòng và bảng giá |

#### 2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)
- **Bước 1 (Tòa nhà):** Nhập tên cơ sở, địa chỉ, số tầng.
- **Bước 2 (Sinh phòng hàng loạt):** Nhập số phòng mỗi tầng và giá mặc định $\to$ Sinh tự động mã phòng theo công thức `Tầng + STT` (P.101, P.102...).
- **Bước 3 (Đơn giá dịch vụ):** Nhập giá điện/kWh, giá nước/$m^3$.
- Bọc toàn bộ quy trình tạo bản ghi `buildings`, `rooms`, `utility_pricing` trong `DB::transaction()`. Nếu có bất kỳ lỗi nào, rollback 100% dữ liệu.

#### 3. Hợp đồng Dữ liệu (API Contract)
##### 3.1. Output khi Hoàn tất Onboarding
- **HTTP Status Code:** `201 Created`
```json
{
  "success": true,
  "message": "Thiết lập cơ sở lưu trú ban đầu thành công!",
  "total_rooms_created": 20,
  "redirect_url": "/smartroom/admin"
}
```
##### 3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /landlord/onboarding/complete`)
| Tên Field | Kiểu Dữ Liệu | Bắt Buộc | Validation Rules (Laravel) | Thông Báo Lỗi Nghiệp Vụ (Message) |
| :--- | :--- | :---: | :--- | :--- |
| `building_name` | String | Có | `required\|string\|max:255` | Tên cơ sở lưu trú không được để trống. |
| `address` | String | Có | `required\|string\|max:255` | Địa chỉ tòa nhà không được để trống. |
| `floors` | Integer | Có | `required\|integer\|min:1\|max:20` | Số tầng cơ sở phải từ 1 đến 20 tầng. |
| `rooms_per_floor` | Integer | Có | `required\|integer\|min:1\|max:50` | Số phòng mỗi tầng tối đa 50 phòng. |
| `default_rent_price` | Integer | Có | `required\|integer\|min:500000` | Mức giá thuê mặc định tối thiểu từ 500.000 VNĐ. |
| `electricity_rate` | Integer | Có | `required\|integer\|min:1000` | Đơn giá điện sinh hoạt tối thiểu 1.000 VNĐ/kWh. |
| `water_rate` | Integer | Có | `required\|integer\|min:5000` | Đơn giá nước sinh hoạt tối thiểu 5.000 VNĐ/m³. |

#### 4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)
- **Tạo `buildings`:** Lưu thông tin tòa nhà của tenant hiện tại.
- **Tạo hàng loạt `rooms`:** Vòng lặp sinh tự động `rooms` với `status = 'empty'`, `cleaning_status = 'clean'`.
- **Cập nhật `users`:** Chuyển vai trò từ `unverified_landlord` sang `landlord`.

#### 5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)
##### A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)
| Nguyên Nhân / Tình Huống Thao Tác | Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX |
| :--- | :--- |
| Bấm nút "Tiếp tục" ở Bước 1 khi chưa nhập Tên tòa nhà | Chặn chuyển bước, tự động cuộn đến trường Tên cơ sở và viền đỏ báo lỗi: "Vui lòng nhập tên tòa nhà." |
| Tải ảnh đại diện cơ sở vượt quá kích thước 5MB | Client kiểm tra dung lượng ngay khi chọn tệp, báo lỗi: "Dung lượng ảnh tối đa 5MB. Vui lòng chọn tệp khác!" |
| Người dùng vô tình bấm F5 tải lại trang khi đang ở Bước 2 | Dữ liệu các bước trước đã được lưu tạm trong `localStorage`, form tự động khôi phục dữ liệu không bị mất. |
| Nhập số tầng hoặc số phòng là số âm hoặc chữ | Input mask tự động chặn ký tự chữ, hiển thị cảnh báo: "Vui lòng nhập số nguyên dương hợp lệ." |
| Hoàn tất thành công Bước 3 của Onboarding | Hiển thị hiệu ứng pháo hoa chúc mừng (Confetti) và tự động chuyển hướng vào Sơ đồ ma trận phòng. |

##### B. Tầng Máy chủ Backend (Server-side / HTTP Response)
| Tình Huống Phát Sinh Lỗi | Mã HTTP | Error Code | Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas |
| :--- | :---: | :--- | :--- |
| Lỗi gián đoạn mạng giữa chừng khi đang tạo phòng | 500 | `ONBOARDING_TX_FAILED` | `DB::rollBack()` tự động thu hồi toàn bộ bản ghi đã tạo dở dang, không để lại dữ liệu rác mồ côi. |

#### 6. Tiêu chí Nghiệm thu (Definition of Done - DoD)
- [ ] Hoàn thành Wizard 3 bước tạo thành công 1 tòa nhà và 20 phòng hoàn chỉnh trong CSDL.
- [ ] Tự động gán bảng giá điện nước vào cấu hình Tenant.
- [ ] Chuyển trạng thái người dùng từ `unverified_landlord` sang `landlord` và điều hướng về Dashboard.
