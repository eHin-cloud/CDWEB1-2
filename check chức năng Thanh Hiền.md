# BẢNG ĐỐI CHIẾU & KIỂM TRA CHỨC NĂNG - NGUYỄN THANH HIỀN
> **Đề tài**: Hệ thống Quản lý Nhà trọ, Chung cư, Căn hộ dịch vụ và Khách sạn Thông minh (Renty & SmartRoom)  
> **Thành viên phụ trách**: **Nguyễn Thanh Hiền** (MSSV: 24211TT3646 - Nhóm Trưởng)  
> **Phân hệ**: Sàn phòng trọ Renty, Định danh KYC, Hợp đồng & Xác thực SmartRoom (Tính năng 01 đến Tính năng 10)  
> **Ngày cập nhật kiểm tra**: 12/10/2026  
> **Nguyên tắc thực hiện**: Giữ nguyên đặc tả báo cáo; kiểm tra code theo đặc tả; nếu code đã có y chang hoặc có thêm tính năng mở rộng ("thừa") thì giữ nguyên code ổn định.

---

## TỔNG HỢP TRẠNG THÁI 10 CHỨC NĂNG

| STT | Mã Chức Năng | Tên Tính Năng | Trạng Thái Đối Soát | Đánh Giá Chi Tiết |
| :---: | :--- | :--- | :---: | :--- |
| **01** | `FEAT_01_AUTH` | Đăng ký & Đăng nhập hệ thống (Xác thực OTP & Mật khẩu) | **Đã giống với spec** | Đầy đủ luồng Web & REST API, Rate Limiting chống Brute-force, OTP 6 số (TTL 5p), mã hóa HMAC Blind Index SĐT. |
| **02** | `FEAT_02_PROFILE` | Quản lý hồ sơ chủ trọ & Cài đặt chủ trọ | **Đã giống với spec** | Cập nhật hồ sơ cá nhân, CCCD, thông tin ngân hàng VietQR Napas247, đồng bộ 2 chiều `users` và `landlord_profiles`. |
| **03** | `FEAT_03_ROOM_MGMT` | Quản lý danh mục phòng trọ & Cấu hình phòng (CRUD Room) | **Đã giống với spec** | Đầy đủ CRUD, Multi-tenancy, bảo vệ BOLA, kiểm tra điều kiện duyệt hồ sơ trước khi đăng tin, khóa mềm `SoftDeletes`. |
| **04** | `FEAT_04_LANDING` | Trang chủ sàn phòng trọ Renty (Landing Page) | **Đã giống với spec** | Giao diện Glassmorphism, Huy hiệu Tín nhiệm Trust Badge (Tích xanh), thuật toán cảnh báo giá lệch mặt bằng thông minh. |
| **05** | `FEAT_05_SMART_MAP` | Bản đồ phòng trọ thông minh (Smart Map) | **Đã giống với spec** | Tích hợp Google Maps chia đôi màn hình (Split View), Marker tương tác 2 chiều mượt mà, chuyển lớp Roadmap / Vệ tinh. |
| **06** | `FEAT_06_ROOM_DETAIL` | Trang chi tiết phòng trọ (Room Detail) | **Đã giống với spec** | Thư viện ảnh đa góc độ, thông số tiện nghi khép kín, đánh giá thực tế kèm AI tóm tắt ưu/nhược điểm khách quan. |
| **07** | `FEAT_07_CONTACT` | Tiếp nhận & Quản lý yêu cầu hẹn xem phòng | **Đã giống với spec** | Form đặt lịch xem phòng trực tuyến, chuông thông báo đẩy đến chủ trọ theo thời gian thực, nhật ký kiểm toán Audit Log. |
| **08** | `FEAT_08_CONTRACT` | Hợp đồng thuê phòng online (Ký số Canvas & Xuất PDF) | **Đã giống với spec** | Ký số Canvas 2 bên (Chủ trọ & Cư dân) kèm xác thực OTP bảo chứng pháp lý, kết xuất file PDF hợp đồng chuẩn in ấn A4. |
| **09** | `FEAT_09_KYC` | Xác thực định danh & Đăng ký tích xanh KYC chủ trọ | **Đã giống với spec** | Tải ảnh CCCD 2 mặt & chứng từ ngân hàng, lưu trữ Zero Trust, che mờ CCCD nhạy cảm, băm toàn vẹn dữ liệu SHA-256. |
| **10** | `FEAT_10_ADMIN_KYC` | Kiểm duyệt hồ sơ chủ trọ & Cấp tích xanh (Admin Verification) | **Đã giống với spec** | Bảng thẩm định hồ sơ Superadmin, xem ảnh qua Signed URL (TTL 300s), xác thực Passkey WebAuthn, duyệt cấp Tích Xanh. |

---

## CHI TIẾT ĐỐI SOÁT TỪNG TÍNH NĂNG

### 1. Tính năng 01: Đăng ký & Đăng nhập hệ thống (Xác thực OTP & Mật khẩu) (`FEAT_01_AUTH`)
* **Mã nguồn thực tế**:
  * Web Controller: `app/Http/Controllers/CrudUserController.php` (`login`, `authUser`, `createUser`, `postUser`, `showVerifyOtp`, `verifyGuestOtp`, `sendGuestOtp`)
  * API Controller: `app/Http/Controllers/Api/AuthController.php` (`login`, `register`, `profile`, `logout`, `checkAvailability`)
  * Onboarding Controller: `app/Http/Controllers/LandlordOnboardingController.php` (`create`, `store`, `showVerifyForm`, `sendOtp`, `verifyOtp`)
  * Services: `app/Services/OtpService.php`, `app/Support/SensitiveData.php`
  * Models: `app/Models/User.php`, `app/Models/Role.php`, `app/Models/NotificationLog.php`
  * Routes:
    * Web: `GET /login`, `POST /login`, `GET /create`, `POST /create`, `GET /guest/verify`, `POST /guest/verify-otp`, `POST /guest/send-otp`, `GET /landlord/register`, `POST /landlord/register`, `GET /signout`
    * API: `POST /api/auth/register`, `POST /api/auth/login`, `GET /api/auth/profile`, `POST /api/auth/logout`
  * Views: `resources/views/login/login.blade.php`, `resources/views/landlord/register.blade.php`, `resources/views/guest/verify_otp.blade.php`
* **Đối soát với đặc tả**:
  * ✅ Endpoints & Middleware: Cung cấp đầy đủ cả Web Route lẫn REST API `/api/auth/login`, `/api/auth/register`, áp dụng Rate Limiting `throttle:5,1` và `throttle:30,1` chống tấn công dò mật khẩu (Brute-force).
  * ✅ Điều hướng phân quyền thông minh: Sau khi đăng nhập thành công, tự động điều hướng đúng phân hệ: Khách thuê vào `/renty`, Chủ trọ vào `/smartroom/admin`, Cư dân vào `/smartroom/resident`, Superadmin vào `/dashboard` hoặc `/list`.
  * ✅ Luồng xác thực OTP đa kênh: Hỗ trợ tạo và gửi mã xác thực OTP ngẫu nhiên 6 chữ số có thời hạn (TTL 5 phút) qua Email và SMS/Zalo, lưu vết kiểm toán vào bảng `notification_logs`.
  * ✅ Bảo mật dữ liệu & HMAC Blind Index: Mật khẩu mã hóa chuẩn một chiều `bcrypt`/`Hash::make`; số điện thoại được mã hóa và đối soát bảo mật thông qua hàm Blind Index `SensitiveData::blindIndex($phone)` chống lộ dữ liệu nhạy cảm.
  * ✅ Kịch bản xử lý lỗi (`ERR_01_01` -> `ERR_01_12`): Xử lý trọn vẹn lỗi bỏ trống trường, SĐT sai định dạng regex `^(0[3|5|7|8|9])[0-9]{8}$`, mật khẩu dưới 6 ký tự, email không hợp lệ, tài khoản bị khóa `locked` (HTTP 403), mã OTP sai hoặc hết hạn.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 2. Tính năng 02: Quản lý hồ sơ chủ trọ & Cài đặt chủ trọ (`FEAT_02_PROFILE`)
* **Mã nguồn thực tế**:
  * Controller: `app/Http/Controllers/AdminDashboardController.php` (`updateProfile`, `index`), `app/Http/Controllers/Api/TenantAdminController.php` (`dashboard`)
  * Models: `app/Models/LandlordProfile.php`, `app/Models/User.php`, `app/Models/Tenant.php`
  * Routes:
    * Web: `POST /smartroom/admin/profile`, `GET /smartroom/admin` (Tab `#profile-section`)
    * API: `GET /api/auth/profile`, `POST /api/tenant/bank-account/reveal`
  * Views: `resources/views/admin/admin.blade.php` (Giao diện quản lý hồ sơ và cấu hình chủ trọ)
* **Đối soát với đặc tả**:
  * ✅ Endpoint quản lý hồ sơ: `POST /smartroom/admin/profile`, bảo vệ bằng middleware `admin` và kiểm tra phân quyền chủ trọ.
  * ✅ Quản lý thông tin định danh & Tài chính: Cập nhật họ tên chủ cơ sở, số điện thoại chính thức (regex 10 chữ số bắt đầu bằng số 0), số CCCD/MST, địa chỉ thường trú, tên ngân hàng và số tài khoản ngân hàng thụ hưởng (phục vụ tự động sinh mã VietQR).
  * ✅ Đổi mật khẩu tài khoản trực tiếp: Cho phép đổi mật khẩu mới (tối thiểu 6 ký tự) ngay trên form cập nhật hồ sơ với cơ chế băm `Hash::make`.
  * ✅ Đồng bộ cơ sở dữ liệu 2 chiều: Tự động đồng bộ và cập nhật thông tin đồng thời trên cả 2 bảng `users` và `landlord_profiles` theo `tenant_id`.
  * ✅ Kịch bản xử lý lỗi (`ERR_02_01` -> `ERR_02_08`): Bắt buộc các trường họ tên, số điện thoại, kiểm tra số tài khoản ngân hàng, thông báo thành công hoặc trả lỗi chi tiết dưới từng ô nhập liệu.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 3. Tính năng 03: Quản lý danh mục phòng trọ & Cấu hình phòng (`FEAT_03_ROOM_MGMT`)
* **Mã nguồn thực tế**:
  * Controller: `app/Http/Controllers/RoomController.php` (`index`, `create`, `store`, `edit`, `update`, `destroy`, `generateDescription`)
  * API Controller: `app/Http/Controllers/Api/TenantAdminController.php` (`listRooms`, `storeRoom`, `updateRoom`, `deleteRoom`)
  * Model: `app/Models/Room.php` (sử dụng `SoftDeletes`, quan hệ `building`, `tenant`, `residents`, `equipments`, `reviews`)
  * Routes:
    * Web: `Route::prefix('smartroom/admin/rooms')` (`GET /`, `GET /create`, `POST /store`, `GET /{id}/edit`, `POST /{id}/update`, `DELETE /{id}/delete`, `POST /description/ai`)
    * API: `GET /api/admin/rooms`, `POST /api/admin/rooms`, `PUT /api/admin/rooms/{id}`, `DELETE /api/admin/rooms/{id}`
  * Views: `resources/views/admin/rooms/index.blade.php`, `resources/views/admin/rooms/create.blade.php`, `resources/views/admin/rooms/edit.blade.php`
  * Tests: `tests/Feature/RoomManagementCrudTest.php`
* **Đối soát với đặc tả**:
  * ✅ Quản lý toàn diện vòng đời phòng trọ: Quản lý mã/số phòng, tầng, diện tích ($m^2$), giá thuê tháng, tiền đặt cọc, loại phòng (`standard`, `deluxe`, `vip`, `studio`), hình thức cho thuê, trạng thái phòng (`empty`, `occupied`, `overdue`, `maintenance`).
  * ✅ Kiểm tra điều kiện tiên quyết đăng tin (Guard Condition): Bắt buộc chủ trọ phải hoàn thiện hồ sơ pháp lý (CCCD/MST) và được duyệt trước khi được phép tạo phòng mới.
  * ✅ Bộ lọc & Phân trang an toàn: Tìm kiếm theo số phòng, lọc trạng thái, tầng, khoảng giá; tự động xử lý và ép trang an toàn nếu tham số `page` vượt ngưỡng.
  * ✅ Bảo vệ an ninh BOLA & Ngăn chặn xóa qua URL: Chặn xóa phòng bằng phương thức HTTP GET trực tiếp; kiểm tra quyền sở hữu `tenant_id` chống xóa phòng cơ sở khác; tự động xóa sạch file media trên Disk Storage khi xóa phòng.
  * ✅ Tính năng mở rộng phong phú: Hỗ trợ tải lên nhiều ảnh kèm gán nhãn góc chụp thực tế; tích hợp Trợ lý AI Gemini tự sinh nội dung mô tả phòng thu hút (`POST /smartroom/admin/rooms/description/ai`).
  * ✅ Kịch bản xử lý lỗi (`ERR_03_01` -> `ERR_03_10`): Bắt trùng lặp số phòng trong cùng tòa nhà, kiểm tra giá thuê > 0, diện tích > 0, xử lý xung đột đồng thời khi xóa.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 4. Tính năng 04: Trang chủ sàn phòng trọ Renty (Landing Page) (`FEAT_04_LANDING`)
* **Mã nguồn thực tế**:
  * Web Routes: `routes/web.php` (Closure `$rentyRooms`, `$rentyPage`, `GET /renty`, `GET /renty/search`)
  * API Controller: `app/Http/Controllers/Api/VisitorController.php` (`index`, `filter`), `SmartSearchController.php`
  * Views: `resources/views/rentry/rentry.blade.php`, `resources/views/rentry/partials/room_card.blade.php`, `resources/views/header/header.blade.php`, `resources/views/footer/footer.blade.php`
  * Assets: `resources/css/app.css`, `resources/js/app.js`
* **Đối soát với đặc tả**:
  * ✅ Giao diện hiện đại & Thẩm mỹ cao cấp: Thiết kế Glassmorphism trong suốt sang trọng, hỗ trợ chế độ màu tối ưu, trình bày danh sách phòng dạng lưới Grid 2D trực quan.
  * ✅ Huy hiệu Tín nhiệm (Trust Badge): Tự động hiển thị huy hiệu "Tích xanh" / "Đã xác minh" cho các phòng trọ thuộc chủ cơ sở đã hoàn tất xét duyệt định danh KYC, tự động cộng điểm ưu tiên đề xuất `boost_score`.
  * ✅ Thuật toán cảnh báo giá thông minh: Tự động tính toán giá trung bình của các phòng cùng phân khúc diện tích trong khu vực lân cận; đưa ra huy hiệu cảnh báo "Giá thấp bất thường" (khi thấp hơn >= 25%) hoặc "Giá cao hơn mặt bằng" (khi cao hơn >= 25%) giúp người thuê phòng tránh bẫy lừa đảo.
  * ✅ Thanh thông báo thời gian thực: Tích hợp endpoint `/renty/notifications` hiển thị chuông thông báo cá nhân hóa theo từng vai trò (Khách vãng lai, Cư dân, Chủ trọ, Superadmin).
  * ✅ Kịch bản xử lý lỗi (`ERR_04_01` -> `ERR_04_04`): Xử lý từ khóa tìm kiếm rỗng, làm sạch ký tự đặc biệt, hiển thị khung trạng thái trống (Empty State) thân thiện khi không tìm thấy kết quả.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 5. Tính năng 05: Bản đồ phòng trọ thông minh (Smart Map) (`FEAT_05_SMART_MAP`)
* **Mã nguồn thực tế**:
  * Web Routes: `routes/web.php` (`GET /renty?view=map`)
  * API Controller: `app/Http/Controllers/Api/VisitorController.php` (`map`)
  * Endpoint API: `GET /api/renty/rooms/map`
  * Client Script: `public/js/google-maps-renty.js`
  * Views: `resources/views/rentry/rentry.blade.php` (`#renty-interactive-map`, thanh điều khiển lớp bản đồ Roadmap/Satellite, nút bật/tắt toàn màn hình, `#gps-permission-modal`, `#gm-tiles-error-banner`, `#gm-coord-error-toast`, `#range_radius`, `#room_list_panel`)
* **Đối soát với đặc tả**:
  * ✅ Bản đồ tương tác trực quan: Hiển thị bản đồ Google Maps chia đôi màn hình (Split-view: 50% bản đồ bên trái, 50% danh sách thẻ phòng bên phải).
  * ✅ Tương tác 2 chiều mượt mà: Nhấp vào Marker trên bản đồ sẽ tự động kích hoạt InfoWindow, làm nổi bật và cuộn danh sách đến thẻ phòng tương ứng; rê chuột vào thẻ phòng sẽ nhấp nháy vị trí Marker trên bản đồ.
  * ✅ Chuyển đổi lớp bản đồ linh hoạt: Cung cấp nút chuyển đổi nhanh giữa bản đồ giao thông đường phố (`roadmap`) và bản đồ không gian vệ tinh (`satellite`).
  * ✅ API trả dữ liệu tọa độ địa lý: API `/api/renty/rooms/map` trả về danh sách các phòng còn trống (`empty`) kèm tọa độ vĩ độ (`latitude`), kinh độ (`longitude`), tên cơ sở và địa chỉ chi tiết.
  * ✅ Kịch bản xử lý lỗi (`ERR_05_01` -> `ERR_05_04`): Hướng dẫn bật GPS trình duyệt, xử lý trường hợp không tìm thấy phòng trong bán kính quét, tự động điều hướng tâm bản đồ về khu vực trung tâm mặc định.

#### Bảng Thông Báo Lỗi Và Xác Thực Giao Diện (UI Validation Error Messages)
| Nguyên nhân / Kịch bản lỗi | Mã lỗi | Message cụ thể (nguyên văn) | Giao diện & Hiệu ứng phản hồi |
| :--- | :---: | :--- | :--- |
| Từ chối cấp quyền định vị GPS | **`ERR_05_01`** | `Vui lòng cho phép truy cập vị trí trên trình duyệt để tìm phòng gần bạn nhất.` | Hiển thị Popup Modal thông báo hướng dẫn 3 bước bật GPS trên trình duyệt kèm nút Thử lại định vị |
| Không có phòng trong bán kính chọn | **`ERR_05_02`** | `Không tìm thấy phòng trọ nào trong bán kính đã chọn.` | Hiển thị vòng quét trên bản đồ đổi sang viền đỏ nét đứt cảnh báo và gợi ý nút tăng bán kính (5km, 10km, tất cả) |
| Tọa độ địa lý không hợp lệ | **`ERR_05_03`** | `Tọa độ tìm kiếm nằm ngoài phạm vi bản đồ hỗ trợ.` | Tự động chuyển tâm bản đồ về khu vực trung tâm mặc định kèm Toast thông báo viền đỏ |
| Lỗi kết nối dịch vụ bản đồ số | **`ERR_05_04`** | `Không thể tải lớp bản đồ số (Tiles Server timeout).` | Hiển thị Banner thông báo lỗi viền đỏ góc trên kèm tùy chọn nút "Chuyển sang chế độ danh sách" |

#### Hình Ảnh Giao Diện & Phác Thảo UI (UI Wireframe)
* **Hình 5.1: Giao diện Bản đồ phòng trọ thông minh (Smart Map)**
  * *Mô tả chức năng*: Bản đồ tương tác trực quan hiển thị vị trí các phòng trọ kèm giá thuê, hỗ trợ tìm kiếm phòng quanh trường học và cơ quan.

#### Bảng Mô Tả Chi Tiết Từng Phần Tử UI (UI Elements Description)
| STT | Loại phần tử | Tên field | Label hiển thị | Placeholder | Vị trí trên form & Quy cách |
| :---: | :--- | :--- | :--- | :--- | :--- |
| **1** | Map Canvas | `map_view` | Bản đồ số | — | Chiếm toàn màn hình hoặc 50%-70% bên trái/phải (`#renty-interactive-map`) |
| **2** | Side Panel | `room_list_panel` | Danh sách phòng theo khung nhìn | — | Thanh trượt danh sách phòng tương ứng với vùng bản đồ đang hiển thị (`#room_list_panel`) |
| **3** | Radius Slider | `range_radius` | Bán kính quét | 5 km | Thanh kéo điều chỉnh bán kính (`#range_radius`), bước nhảy 0.5km, min 0, max 10km |

#### Kịch bản Kiểm thử & Tiêu chí Nghiệm thu (Definition of Done - DoD)
| STT | Input kiểm thử | Kết quả mong đợi | Ghi chú phân loại |
| :---: | :--- | :--- | :--- |
| **1** | Mở trang bản đồ và cho phép quyền truy cập vị trí | Bản đồ di chuyển tâm về vị trí hiện tại và tải các Marker lân cận | Kiểm tra GPS |
| **2** | Kéo thanh bán kính về 0km | Báo lỗi 'Bán kính tối thiểu phải từ 1km' kèm viền đỏ nổi bật | Kiểm tra Range validation |
* ☑ Giao diện hiển thị đúng chuẩn Responsive, thân thiện trên Desktop và Mobile.
* ☑ Toàn bộ các thông điệp lỗi (validation error) xuất hiện đúng vị trí và viền đỏ theo kịch bản.
* ☑ Dữ liệu được lưu trữ toàn vẹn vào Cơ sở dữ liệu và bảo toàn phân quyền Multi-tenancy.

* **Kết luận**: **Chức năng đã giống với spec.**

---

### 6. Tính năng 06: Trang chi tiết phòng trọ (Room Detail) (`FEAT_06_ROOM_DETAIL`)
* **Mã nguồn thực tế**:
  * Web Route: `routes/web.php` (`GET /renty/room/{id}`)
  * API Controller: `app/Http/Controllers/Api/VisitorController.php` (`reviews`, `reviewSummary`)
  * Views: `resources/views/rentry/rooms/show.blade.php`, `resources/views/rentry/room_3d.blade.php`
  * Models: `app/Models/Room.php`, `app/Models/Building.php`, `app/Models/Review.php`, `app/Models/RoomReport.php`
* **Đối soát với đặc tả**:
  * ✅ Trình bày thông tin chuyên sâu: Thư viện ảnh chi tiết từng góc chụp (toàn cảnh, ban công, bếp, WC), diện tích, giá thuê, bảng giá điện - nước - dịch vụ minh bạch, tiện ích phòng khép kín.
  * ✅ Đánh giá thực tế từ cư dân: Hiển thị danh sách đánh giá sao, bình luận từ những người đã từng thuê phòng tại cơ sở.
  * ✅ Phân tích không gian & Vị trí: Mô tả cảnh quan xung quanh, độ thoáng mát của ban công/cửa sổ, khoảng cách ước tính tới các tiện ích dân sinh trọng điểm.
  * ✅ Tính năng mở rộng phong phú: Tích hợp phòng trải nghiệm ảo 3D/360° (`/renty/room/{id}/3d`), form báo cáo tin đăng vi phạm/sai giá (`POST /renty/room/{id}/report`), form gửi đánh giá thực tế (`POST /renty/room/{id}/review`).
  * ✅ Kịch bản xử lý lỗi (`ERR_06_01` -> `ERR_06_04`): Xử lý phòng không tồn tại (trả mã HTTP 404), gắn nhãn phòng đã cho thuê, cơ chế nạp ảnh thế mạng (Fallback Placeholder) tự động khi ảnh gốc bị lỗi.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 7. Tính năng 07: Tiếp nhận & Quản lý yêu cầu hẹn xem phòng (`FEAT_07_CONTACT`)
* **Mã nguồn thực tế**:
  * Controller: `app/Http/Controllers/AdminDashboardController.php` (`storeContactRequest`, `updateContactRequestStatus`, `deleteContactRequest`)
  * Model: `app/Models/ContactRequest.php`
  * Routes:
    * `POST /renty/contact-request` (Khách gửi yêu cầu từ Renty)
    * `POST /smartroom/admin/contact-request/{id}/status` (Chủ trọ cập nhật trạng thái `pending` -> `processed`)
    * `DELETE /smartroom/admin/contact-request/{id}` (Chủ trọ xóa yêu cầu)
  * Views: Form modal xem phòng trên `resources/views/rentry/rooms/show.blade.php`, bảng tiếp nhận yêu cầu trong `resources/views/admin/admin.blade.php` (`#contact-section`)
  * Test: `tests/Feature/RentyFormSubmissionTest.php`
* **Đối soát với đặc tả**:
  * ✅ Đăng ký hẹn xem phòng tiện lợi: Khách thuê chỉ cần nhập họ tên, số điện thoại liên hệ và lời nhắn mong muốn; form tự động liên kết với mã phòng đang xem.
  * ✅ Bảng quản lý tiếp nhận tập trung: Chủ cơ sở xem danh sách khách hẹn theo thời gian thực, đánh dấu "Đã xử lý" sau khi gọi điện tư vấn, hoặc xóa yêu cầu nếu khách hủy hẹn.
  * ✅ Thông báo đẩy chủ trọ: Chuông thông báo trên thanh điều khiển của chủ trọ tự động hiển thị thẻ nhắc nhở kèm số điện thoại của khách vừa gửi yêu cầu.
  * ✅ Ghi vết kiểm toán (Audit Logs): Mọi thao tác cập nhật trạng thái và xóa yêu cầu hẹn xem phòng đều được ghi lại qua `AdminActivityLogger`.
  * ✅ Kịch bản xử lý lỗi (`ERR_07_01` -> `ERR_07_06`): Bắt buộc nhập họ tên, kiểm tra số điện thoại đúng chuẩn 10 chữ số, chống spam yêu cầu qua Rate Limiting `throttle:15,1`.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 8. Tính năng 08: Hợp đồng thuê phòng online (Ký số Canvas & Xuất PDF) (`FEAT_08_CONTRACT`)
* **Mã nguồn thực tế**:
  * Controller: `app/Http/Controllers/AdminDashboardController.php` (`storeContract`, `deleteContract`, `signContractView`, `printContractPdf`, `sendOtpForContract`, `signContract`, `signLessorContract`, `renewContract`, `declineRenewal`)
  * API Controller: `app/Http/Controllers/Api/TenantAdminController.php` (`listContracts`, `storeContract`, `deleteContract`)
  * Model: `app/Models/Contract.php`, `app/Models/Resident.php`, `app/Models/Room.php`
  * Routes:
    * Web:
      * `POST /smartroom/admin/contract`
      * `GET /smartroom/contract/{id}/sign`
      * `GET /smartroom/contract/{id}/pdf`
      * `POST /smartroom/contract/{id}/send-otp`
      * `POST /smartroom/contract/{id}/sign`
      * `POST /smartroom/contract/{id}/lessor-sign`
      * `POST /smartroom/admin/contract/{id}/renew`
    * API: `GET /api/admin/contracts`, `POST /api/admin/contracts`, `DELETE /api/admin/contracts/{id}`
  * Views: `resources/views/admin/sign_contract.blade.php` (Giao diện Ký số Canvas + Nhập OTP), `resources/views/admin/pdf_contract.blade.php` (Bản in PDF)
* **Đối soát với đặc tả**:
  * ✅ Soạn thảo hợp đồng điện tử chuẩn: Tự sinh mã hợp đồng, gắn kết thông tin pháp lý bên cho thuê (Bên A), cư dân đại diện (Bên B), số phòng, kỳ hạn thuê, giá thuê, tiền cọc và điều khoản pháp lý mẫu.
  * ✅ Ký số 2 bên bằng HTML5 Canvas Signature Pad: Cư dân vẽ chữ ký trực tiếp trên màn hình cảm ứng / chuột máy tính; tích hợp bước xác thực mã OTP gửi về số điện thoại trước khi lưu chữ ký số nhằm đảm bảo giá trị pháp lý.
  * ✅ Bên cho thuê ký duyệt online: Chủ trọ thực hiện ký duyệt điện tử trực tiếp trên hệ thống qua endpoint `signLessorContract`.
  * ✅ Xuất file PDF trọn vẹn: Sử dụng thư viện `Barryvdh\DomPDF\Facade\Pdf` kết xuất bản hợp đồng định dạng PDF chuẩn khổ A4, đầy đủ điều khoản, thông tin 2 bên và hình ảnh chữ ký số.
  * ✅ Quản lý gia hạn hợp đồng: Hỗ trợ tạo đề xuất gia hạn hợp đồng và phê duyệt trực tuyến giữa chủ trọ và khách thuê.
  * ✅ Kịch bản xử lý lỗi (`ERR_08_01` -> `ERR_08_08`): Bắt buộc chọn phòng và cư dân, kiểm tra ngày kết thúc phải sau ngày bắt đầu, kiểm tra chữ ký Canvas không được để trống, mã OTP ký số sai hoặc hết hạn.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 9. Tính năng 09: Xác thực định danh & Đăng ký tích xanh KYC chủ trọ (`FEAT_09_KYC`)
* **Mã nguồn thực tế**:
  * Controller: `app/Http/Controllers/LandlordVerificationController.php` (`submitKyc`, `submitPremium`)
  * Services: `app/Services/SecureDocumentService.php`, `app/Support/SensitiveData.php`
  * Models: `app/Models/LandlordVerificationRequest.php`, `app/Models/LandlordVerificationDocument.php`, `app/Models/Tenant.php`
  * Routes:
    * `POST /smartroom/admin/verification/kyc`
    * `POST /smartroom/admin/verification/premium`
  * Views: Modal nộp hồ sơ KYC và giao diện theo dõi tiến độ trong `resources/views/admin/admin.blade.php` (`#kyc-modal`, `#verification-section`)
* **Đối soát với đặc tả**:
  * ✅ Quy trình nộp hồ sơ KYC minh bạch: Chủ cơ sở nhập số CCCD (9 - 20 ký tự), tên ngân hàng, số tài khoản và tên chủ tài khoản ngân hàng thụ hưởng.
  * ✅ Tải lên hồ sơ minh chứng: Bắt buộc đính kèm ảnh chụp mặt trước CCCD, mặt sau CCCD và ảnh chụp minh chứng tài khoản ngân hàng / giấy phép PCCC (dung lượng tối đa 4MB, định dạng jpg, jpeg, png, webp).
  * ✅ Kiến trúc lưu trữ bảo mật Zero Trust: Toàn bộ tài liệu nhạy cảm được lưu vào thư mục cô lập `landlord-verifications/{tenant_id}`, tính toán mã băm toàn vẹn SHA-256 (`sha256_checksum`), che mờ số CCCD bằng hàm `SensitiveData::maskNationalId`.
  * ✅ Quản lý vòng đời trạng thái hồ sơ: Tự động chuyển đổi mượt mà giữa các trạng thái: Chưa xác minh (`unverified`) -> Chờ duyệt KYC (`kyc_pending`) -> Đã duyệt KYC (`kyc_verified`) -> Chờ duyệt Tích xanh (`premium_pending`) -> Đã cấp Tích xanh (`premium_verified`).
  * ✅ Kịch bản xử lý lỗi (`ERR_09_01` -> `ERR_09_07`): Kiểm tra định dạng số CCCD, chặn nộp trùng khi hồ sơ đang chờ duyệt, kiểm tra định dạng và kích thước file tải lên, bắt buộc đồng ý điều khoản xét duyệt `admin_review_consent`.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 10. Tính năng 10: Kiểm duyệt hồ sơ chủ trọ & Cấp tích xanh (Admin Verification) (`FEAT_10_ADMIN_KYC`)
* **Mã nguồn thực tế**:
  * Controller: `app/Http/Controllers/AdminVerificationController.php` (`index`, `approve`, `reject`, `hasPasskey`, `auditLogs`, `analytics`), `VerificationDocumentController.php` (`show`, `unlock`, `stream`)
  * API Controller: `app/Http/Controllers/Api/SystemAdminController.php` (`getLandlords`, `verifyLandlord`)
  * Models: `app/Models/LandlordVerificationRequest.php`, `app/Models/LandlordVerificationDocument.php`, `app/Models/Tenant.php`, `app/Models/User.php`
  * Middleware: `role:admin`, `auth`, `signed`
  * Routes:
    * Web:
      * `GET /admin/verifications`
      * `POST /admin/verifications/{verification}/approve`
      * `POST /admin/verifications/{verification}/reject`
      * `GET /admin/verification-documents/{document}`
      * `POST /admin/verification-documents/{document}/unlock`
      * `GET /admin/verification-documents/{document}/stream`
    * API: `PATCH /api/admin/landlords/{id}/verify`, `POST /api/platform-admin/verification-documents/{document}/unlock`
  * Views: `resources/views/admin/verifications/index.blade.php` (Bảng điều khiển thẩm định hồ sơ của Superadmin)
* **Đối soát với đặc tả**:
  * ✅ Bảng điều khiển thẩm định hồ sơ tập trung: Dành riêng cho Superadmin với bảng thống kê số lượng hồ sơ (Tổng cộng, Chờ duyệt, Đã duyệt, Đã từ chối), phân trang và tìm kiếm nhanh.
  * ✅ Cơ chế xem ảnh tài liệu an toàn tuyệt đối (Signed Route): Sử dụng đường dẫn có chữ ký số với thời gian sống giới hạn (TTL 300 giây) để xem tài liệu; tích hợp bảo mật mở khóa bằng khóa vân tay / phần cứng `Passkey WebAuthn (FIDO2)` trước khi giải mã dữ liệu nhạy cảm.
  * ✅ Phê duyệt cấp chứng nhận (`approve`): Nâng cấp tự động vai trò chủ trọ từ `unverified_landlord` thành `landlord` chính thức, gắn huy hiệu `kyc_verified` hoặc `premium_verified`, tự động cộng điểm ưu tiên đề xuất `boost_score` lên tối thiểu 100 điểm trên sàn Renty.
  * ✅ Từ chối hồ sơ kèm phản hồi chi tiết (`reject`): Bắt buộc Superadmin nhập lý do từ chối rõ ràng (`reject_reason` từ 10 đến 500 ký tự) để chủ trọ nắm rõ lý do và tiến hành bổ sung hồ sơ.
  * ✅ Kịch bản xử lý lỗi (`ERR_10_01` -> `ERR_10_04`): Bắt buộc nhập lý do từ chối tối thiểu 10 ký tự, xử lý phiên xem ảnh hết hạn (HTTP 403), chặn phê duyệt lại các hồ sơ đã có kết quả.
* **Kết luận**: **Chức năng đã giống với spec.**

---

## TỔNG KẾT HÀNH ĐỘNG
1. **Đặc tả**: Giữ nguyên toàn bộ nội dung trong báo cáo Word / Markdown.
2. **Mã nguồn**: Cả 10 chức năng thuộc phân hệ của **Nguyễn Thanh Hiền (Tính năng 01 đến Tính năng 10)** **đều đã được lập trình hoàn chỉnh, chuẩn xác theo đặc tả**, đồng thời đã có sẵn các cơ chế bảo mật cao cấp (Rate Limiting, HMAC Blind Index, Zero Trust Storage, FIDO2/WebAuthn Passkey, Signed Routes, FSM KYC) và các tính năng mở rộng phong phú (AI gợi ý mô tả phòng, phòng ảo 3D, cảnh báo giá thông minh). Do đó **tuân thủ đúng nguyên tắc: không thay đổi code khi code đã có y chang hoặc thừa/tốt hơn spec**.
