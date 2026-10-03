# DANH SÁCH TOÀN BỘ CÔNG VIỆC & CHỨC NĂNG DỰ ÁN (SMARTROOM & RENTY REVIEW)

Bản quét chi tiết toàn bộ các module, chức năng nghiệp vụ và công việc đã được triển khai trong hệ sinh thái SmartRoom & Renty Review (Website Quản lý nhà trọ thông minh kết hợp Sàn tìm kiếm phòng trọ trực tuyến).

---

## 🌐 PHẦN 1: PHÂN HỆ SÀN TÌM KIẾM PHÒNG TRỌ (RENTY PORTAL - DÀNH CHO KHÁCH THUÊ)

- [x] **1. Tìm kiếm thông minh & Bộ lọc nâng cao (Smart Search & Filtering)**
  - Tích hợp tìm kiếm đa điều kiện: từ khóa số phòng/tòa nhà, khu vực (quận/huyện), khoảng giá thuê, diện tích phòng, khoảng cách đến tiện ích gần nhất.
  - Bộ lọc tiện ích đặc thù: có gác lửng, ban công/cửa sổ thoáng, WC khép kín, cho phép nuôi thú cưng.
  - Gợi ý từ khóa tìm kiếm tự động (Smart suggestions).

- [x] **2. Cảnh báo giá bất thường & Phân tích thị trường (Price Anomaly Warning)**
  - Tự động so sánh giá phòng với các phòng lân cận cùng phân khúc và diện tích (trong biên độ ±5m²).
  - Gắn nhãn cảnh báo trực quan:
    - *Giá thấp bất thường:* Cảnh báo nghi vấn tin ảo/lừa đảo cọc khi thấp hơn từ 25% so với mặt bằng chung.
    - *Giá cao hơn mặt bằng:* Cảnh báo khi giá cao hơn từ 25% so với mặt bằng để khách hàng cân nhắc thêm tiện ích.

- [x] **3. Trải nghiệm không gian phòng 3D thực tế ảo (3D Room Interactive Model)**
  - Tích hợp thư viện Three.js dựng mô hình không gian phòng 3D tương tác trực quan (`room-3d-model.js`).
  - Cho phép xoay 360°, phóng to/thu nhỏ, xem chi tiết bố trí gác lửng, ban công, giường ngủ, bàn làm việc trước khi đến xem phòng thực tế.
  - Tối ưu hiệu năng rendering WebGL mượt mà trên cả trình duyệt máy tính và điện thoại.

- [x] **4. So sánh phòng trọ đa tiêu chí (Room Compare & Multi-factor Scoring)**
  - Cho phép người dùng chọn tối đa 3 phòng trọ để hiển thị bảng so sánh đối đầu (Side-by-side).
  - Thuật toán chấm điểm tự động theo thang điểm 10 trên 4 trục tiêu chí:
    - Điểm giá cả (Price Score)
    - Điểm khoảng cách (Distance Score)
    - Điểm an ninh khu vực (Security Score)
    - Điểm uy tín chủ nhà (Owner Rating Score)

- [x] **5. Hệ thống Đánh giá & Báo cáo sai phạm (Reviews & Scam Reports)**
  - **Đánh giá thực tế (Reviews):** Khách thuê gửi đánh giá xếp hạng sao (1 - 5 sao) và nhận xét thực tế về môi trường sống, chủ nhà, an ninh.
  - **Báo cáo vi phạm (Room Reports):** Người dùng có thể gắn cờ báo cáo tin đăng có dấu hiệu lừa đảo (scam), ảnh giả mạo, sai giá, không an toàn hoặc tin rác kèm mô tả chi tiết để Superadmin kiểm duyệt.

- [x] **6. Đăng ký xem phòng & Tư vấn trực tuyến (Contact Requests)**
  - Form liên hệ nhanh ngay tại trang chi tiết phòng: Khách thuê để lại Họ tên, Số điện thoại và Lời nhắn.
  - Tự động đẩy thông báo và lưu vào hàng đợi "Yêu cầu tư vấn" trên Admin Dashboard của đúng chủ trọ quản lý phòng đó.

- [x] **7. Trợ lý Chatbot AI tư vấn tìm phòng (AI RAG Chatbot)**
  - Tích hợp mô hình ngôn ngữ lớn Google Gemini AI kết hợp cơ chế RAG (Retrieval-Augmented Generation).
  - Tự động quét cơ sở dữ liệu các phòng còn trống thực tế để đối chiếu và trả lời thông minh theo nhu cầu của khách (ngân sách, khu vực, tiện ích nuôi thú cưng, gác lửng).
  - Có cơ chế Fallback tự động tư vấn bằng dữ liệu database thật kể cả khi chưa cấu hình API Key.

---

## 🏢 PHẦN 2: PHÂN HỆ QUẢN LÝ NHÀ TRỌ & VẬN HÀNH (SMARTROOM ADMIN / CHỦ TRỌ)

- [x] **1. Quản lý phòng & Ma trận phòng trực quan (Room Management & Room Matrix)**
  - CRUD phòng trọ: Quản lý số phòng, tầng, giá thuê, tiền cọc, diện tích, tiện ích, ảnh góc chụp đa chiều, video giới thiệu phòng.
  - **AI Description Generator:** Tự động sinh mô tả tin đăng phòng chuẩn SEO, hấp dẫn khách thuê dựa trên thông số phòng thực tế.
  - **Sơ đồ ma trận phòng trực quan (Room Matrix):** Phân chia theo tầng, đổi màu trạng thái theo thời gian thực (Trống, Đã thuê, Nợ cước, Bảo trì).
  - Cập nhật trạng thái nhanh (Quick Status) hỗ trợ Polling / Server-Sent Events (SSE Stream) đồng bộ trạng thái tức thì.
  - Drawer trượt hiển thị chi tiết thông tin cư dân, hợp đồng và hóa đơn của từng phòng.

- [x] **2. Quản lý trang thiết bị & Tài sản (Equipment Management)**
  - Quản lý danh mục thiết bị: Tên tài sản, loại thiết bị (điện tử, nội thất, vệ sinh), số lượng tồn kho, giá trị tài sản.
  - Bàn giao thiết bị (Allocate): Gán trang thiết bị vào từng phòng trọ kèm ghi nhận tình trạng ban đầu (mới, đang dùng tốt).
  - Thu hồi thiết bị (Recover): Thu hồi tài sản khi khách trả phòng, cập nhật trạng thái hỏng hóc hoặc thanh lý.
  - Cảnh báo tồn kho thiết bị sắp hết hoặc thiết bị đang báo hỏng.

- [x] **3. Quản lý cư dân & Thân nhân (Resident & Relatives Management)**
  - Hồ sơ cư dân chi tiết: Họ tên, số điện thoại, số CCCD, ngày sinh, quê quán, nghề nghiệp, ngày bắt đầu thuê, ảnh chân dung, ảnh CCCD 2 mặt.
  - Liên kết phòng tự động: Tự động đổi trạng thái phòng sang "Đã thuê" khi thêm cư dân và chuyển về "Trống" khi cư dân trả phòng.
  - Quản lý người ở cùng (Resident Relatives): Thêm danh sách thân nhân, bạn cùng phòng (Họ tên, quan hệ, CCCD, SĐT).
  - **Xuất biểu mẫu CT01:** Tự động điền thông tin cư dân và thân nhân vào tờ khai thay đổi thông tin cư trú (Mẫu CT01) theo chuẩn Bộ Công an để làm thủ tục đăng ký tạm trú.

- [x] **4. Hợp đồng điện tử & Ký số trực tuyến (E-Contract & Digital Signature)**
  - Soạn thảo hợp đồng thuê phòng trực quan: Chọn phòng, cư dân đại diện, thời hạn thuê, tiền thuê, tiền cọc và tùy chỉnh các điều khoản pháp lý.
  - **AI Contract Terms Analysis:** Tích hợp AI phân tích điều khoản hợp đồng, cảnh báo rủi ro pháp lý cho chủ nhà và khách thuê.
  - **Ký số online (Signature Pad):** Khung canvas ký tên bằng tay hỗ trợ cảm ứng trên điện thoại và chuột máy tính, mã hóa chữ ký base64 lưu vào hệ thống.
  - **Xác thực OTP:** Gửi mã OTP xác nhận trước khi ký kết để đảm bảo tính pháp lý.
  - Ký số 2 bên: Cư dân ký trước, Chủ nhà ký duyệt (Lessor Sign).
  - Xuất hợp đồng dạng PDF chuẩn định dạng có đầy đủ chữ ký số điện tử của 2 bên.
  - Quản lý yêu cầu gia hạn hợp đồng gửi từ cư dân (phê duyệt hoặc từ chối).

- [x] **5. Quản lý điện nước & Dịch vụ (Utilities & Automated Billing)**
  - Ghi chỉ số điện nước thủ công theo từng phòng hoặc nhập hàng loạt (Bulk Input).
  - **AI OCR quét số công tơ:** Tự động nhận diện chỉ số điện/nước từ ảnh chụp công tơ bằng AI Vision (hỗ trợ đọc cả ảnh đơn lẻ và tải hàng loạt).
  - Tự động tính toán lượng điện nước tiêu thụ và thành tiền theo đơn giá cài đặt + phí dịch vụ cố định (rác, internet, vệ sinh).
  - **Tạo mã VietQR động:** Tự sinh mã QR chuẩn VietQR (ngân hàng thụ hưởng, số tài khoản, số tiền chính xác, nội dung chuyển khoản tự động).
  - **Tự động nhắc nợ:** Gửi tin nhắn thông báo hóa đơn và nhắc nợ tiền trọ tự động qua Zalo / SMS kèm đường dẫn VietQR.
  - In phiếu tính tiền, xuất hóa đơn chi tiết dạng PDF chuyên nghiệp.

- [x] **6. IoT Đo đếm thông minh & Giám sát thời gian thực (IoT Smart Metering & Telemetry)**
  - **IoT Ingestion API:** Tiếp nhận luồng dữ liệu chỉ số công tơ điện nước gửi từ thiết bị phần cứng IoT theo chu kỳ.
  - Giám sát công suất tức thời, phát hiện bất thường: cảnh báo rò rỉ nước liên tục hoặc dòng điện tăng đột biến chống nguy cơ chập cháy.
  - Công cụ giả lập chỉ số IoT (Simulate) phục vụ thử nghiệm và kiểm thử hệ thống.
  - Đồng bộ tự động số liệu IoT vào kỳ tính cước hóa đơn (Sync Billing).

- [x] **7. Tiếp nhận & Xử lý sự cố (Tickets & Maintenance)**
  - Tiếp nhận các phiếu báo hỏng thiết bị, sự cố điện nước từ cư dân gửi lên.
  - Phân loại mức độ khẩn cấp bằng AI, gán kỹ thuật viên xử lý.
  - Cập nhật tiến độ xử lý sự cố (Chờ xử lý, Đang xử lý, Đã hoàn thành) và lưu lại lịch sử chi phí sửa chữa.

- [x] **8. Quản lý thanh toán & Đối soát (Payment Management & Webhooks)**
  - Bảng quản lý giao dịch thanh toán: Theo dõi phương thức thanh toán (Tiền mặt, Chuyển khoản VietQR, Cổng thanh toán).
  - Cập nhật trạng thái thanh toán (Chờ thanh toán, Đã thanh toán, Quá hạn).
  - Payment Webhook: Tiếp nhận thông báo biến động số dư ngân hàng/cổng thanh toán để tự động gạch nợ hóa đơn.
  - Xuất dữ liệu đối soát giao dịch ra file bảng tính.

- [x] **9. Báo cáo tài chính & Thống kê doanh thu (Financial Reports & Analytics)**
  - Thống kê doanh thu theo thời gian (theo tháng, quý, năm) với biểu đồ trực quan (Chart.js).
  - Phân tích cơ cấu nguồn thu: Tỷ trọng tiền phòng, tiền điện, tiền nước, phí dịch vụ.
  - Quản lý sổ thu chi nội bộ (Transactions): Ghi nhận thu tiền phòng và chi phí vận hành (sửa chữa, bảo trì tòa nhà).
  - Thống kê tỷ lệ lấp đầy phòng (Occupancy Rate) và số phòng nợ cước.
  - **AI Dashboard Insights:** Phân tích toàn diện bức tranh tài chính, đưa ra các cảnh báo rủi ro dòng tiền và gợi ý chiến lược lấp đầy phòng trống.

- [x] **10. Quản lý cơ sở lưu trú & Tòa nhà (Building Management)**
  - Quản lý danh mục nhiều tòa nhà / chi nhánh (dành cho chủ trọ có chuỗi nhà trọ): Tên tòa nhà, địa chỉ, số tầng, số lượng phòng, mô tả cơ sở.
  - Phân quyền quản lý phòng và cư dân theo từng cơ sở lưu trú.

- [x] **11. Lịch sử vận hành & Nhật ký hoạt động (Activity Logs & Audit Trail)**
  - Ghi nhận chi tiết từng hành động can thiệp dữ liệu: Ai đã sửa chỉ số điện nước, ai thêm cư dân, ai cập nhật trạng thái phòng, thời gian thực hiện.
  - Hỗ trợ chủ trọ và ban quản lý dễ dàng tra cứu, quy trách nhiệm khi xảy ra sai sót.

---

## 🛎️ PHẦN 3: PHÂN HỆ LỄ TÂN & BUỒNG PHÒNG KHÁCH SẠN (HOSPITALITY MODULE)

- [x] **1. Phân hệ Lễ tân Khách sạn (Hotel Reception & Front Desk)**
  - Quản lý đặt phòng ngắn ngày / căn hộ dịch vụ (Hotel Booking).
  - Check-in nhanh cho khách lưu trú.
  - Quản lý Folio (các dịch vụ gia tăng, đồ uống, giặt là phát sinh trong quá trình lưu trú).
  - Check-out và in phiếu thanh toán tổng hợp Folio.

- [x] **2. Phân hệ Buồng phòng tối ưu di động (Housekeeping Mobile Dashboard)**
  - Giao diện thân thiện, tối ưu riêng cho màn hình điện thoại thông minh của nhân viên dọn phòng.
  - Danh sách phòng cần dọn, phân loại trạng thái: Phòng sạch (Clean), Phòng bẩn cần dọn (Dirty), Đang dọn (Cleaning), Cần kiểm tra (Inspecting).
  - Cập nhật trạng thái dọn phòng thời gian thực, đồng bộ ngay lập tức sang màn hình Lễ tân và Ma trận phòng của Quản lý.

---

## 📱 PHẦN 4: PHÂN HỆ CỔNG CƯ DÂN (RESIDENT PORTAL)

- [x] **1. Dashboard Thông tin phòng thuê**
  - Cư dân tra cứu thông tin phòng đang ở, giá thuê, ngày đến hạn hợp đồng, danh sách tài sản bàn giao trong phòng.
  - Đăng ký sở thích, nhu cầu cá nhân (Tenant Preferences).

- [x] **2. Tra cứu hóa đơn & Thanh toán VietQR tức thì**
  - Xem chi tiết hóa đơn tiền phòng từng tháng: tiền phòng, số điện cũ - mới, số nước cũ - mới, đơn giá và phí dịch vụ.
  - Tích hợp mã VietQR động trực tiếp trên màn hình điện thoại để cư dân mở app ngân hàng quét mã thanh toán ngay không cần nhập số tài khoản.

- [x] **3. Báo hỏng thiết bị & Sự cố kèm AI phân tích (Ticket Reporting)**
  - Cư dân gửi yêu cầu sửa chữa hỏng hóc kèm hình ảnh thực tế.
  - **AI Ticket Analysis:** AI tự động đọc mô tả và hình ảnh để nhận diện loại sự cố (điện, nước, khóa cửa), phân loại mức độ khẩn cấp để báo lên chủ trọ.
  - Theo dõi tiến độ thợ sửa chữa và lịch sử xử lý sự cố của phòng mình.

- [x] **4. Yêu cầu gia hạn hợp đồng trực tuyến**
  - Cư dân chủ động gửi yêu cầu gia hạn thời gian thuê phòng trực tiếp trên portal mà không cần gặp mặt trực tiếp.

---

## 🛡️ PHẦN 5: QUẢN TRỊ NỀN TẢNG & BẢO MẬT HỆ THỐNG (SUPERADMIN & SECURITY)

- [x] **1. Quản lý tài khoản & Phân quyền RBAC (Role-Based Access Control)**
  - Hệ thống đa người dùng và đa phân quyền:
    - `Superadmin`: Quản trị toàn bộ nền tảng, duyệt hồ sơ KYC, kiểm duyệt tin phòng vi phạm.
    - `Landlord`: Chủ trọ quản lý các tòa nhà, phòng, cư dân, hóa đơn của riêng cơ sở mình (Multi-tenant isolation).
    - `Resident`: Cư dân chỉ có quyền xem thông tin phòng của mình.
    - `Staff`: Nhân viên buồng phòng, lễ tân.
    - `Guest`: Khách vãng lai tìm kiếm phòng.

- [x] **2. Xác thực danh tính chủ trọ KYC & Cấp huy hiệu uy tín (Trust Badges)**
  - Chủ trọ gửi hồ sơ xác minh: Tải lên căn cước công dân (CCCD), giấy phép kinh doanh / quyền sở hữu bất động sản.
  - Superadmin xem xét hồ sơ, phê duyệt hoặc từ chối yêu cầu xác thực.
  - Hệ thống cấp huy hiệu uy tín hiển thị công khai trên tin đăng phòng Renty:
    - *Tích xanh (Verified / Premium Verified):* Chủ trọ uy tín đã xác thực toàn diện, ưu tiên hiển thị đầu trang tìm kiếm (Boost score).
    - *Đã xác minh (KYC Verified):* Đã đối chiếu thông tin định danh cá nhân.
    - *Chưa xác minh (Unverified):* Nhắc nhở người thuê cẩn trọng khi giao dịch.

- [x] **3. Bảo mật tài liệu xác minh & Khóa WebAuthn / Passkey**
  - Trình xem tài liệu bảo mật (`VerificationDocumentController`): Luồng truyền dữ liệu có ký số URL (Signed URL), chống lộ link công khai.
  - Mở khóa tài liệu nhạy cảm bằng xác thực sinh trắc học **WebAuthn / Passkey** (Vân tay, FaceID hoặc khóa phần cứng FIDO2) dành riêng cho Superadmin.

- [x] **4. Mã hóa bảo vệ dữ liệu nhạy cảm & Kiểm toán (Security & Audit Logs)**
  - Mã hóa các trường dữ liệu nhạy cảm: Số CCCD, số tài khoản ngân hàng của chủ trọ và cư dân trong CSDL.
  - Cơ chế tìm kiếm bảo mật bằng Blind Index (cho phép tìm kiếm số điện thoại đã mã hóa mà không làm lộ dữ liệu thô).
  - Hệ thống nhật ký kiểm toán (Audit Logs) và nhật ký truy cập (Admin Access Logs): Giám sát mọi lần truy cập hoặc mở khóa dữ liệu nhạy cảm.

- [x] **5. Cơ chế xác thực OTP & Onboarding**
  - Xác thực qua mã OTP (SMS/Email OTP) trong quy trình đăng ký tài khoản khách, đăng ký chủ trọ và quy trình ký kết hợp đồng điện tử.

---

## ⚙️ PHẦN 6: CƠ SỞ DỮ LIỆU, KIẾN TRÚC HỆ THỐNG & TÍCH HỢP (ARCH & APIS)

- [x] **1. Kiến trúc CSDL Multi-tenant hoàn chỉnh (33+ Models & Migrations)**
  - Đầy đủ các thực thể cốt lõi: `Users`, `Roles`, `Tenants`, `Buildings`, `Rooms`, `Residents`, `ResidentRelatives`, `Contracts`, `UtilityRecords`, `Bills`, `Payments`, `Tickets`, `Equipment`, `RoomEquipment`, `Reviews`, `RoomReports`, `ContactRequests`, `HotelBookings`, `HotelFolioItems`, `IotDevices`, `IotMeterTelemetries`, `LandlordVerificationRequests`, `AuditLogs`, `AdminActivityLogs`, `OtpCodes`...

- [x] **2. Dữ liệu mẫu phong phú (Seeders đa kịch bản)**
  - Khởi tạo đầy đủ tài khoản Superadmin, Chủ trọ mẫu, Cư dân mẫu, danh sách phòng trọ đa tầng, dữ liệu điện nước lịch sử nhiều tháng, trang thiết bị và đánh giá thực tế.

- [x] **3. Hệ thống RESTful APIs & Webhook**
  - **Public APIs:** Tìm kiếm phòng, gợi ý thông minh, bản đồ vị trí phòng, API so sánh, API đánh giá.
  - **Authenticated APIs (Laravel Sanctum):** Đầy đủ các API quản lý phòng, tòa nhà, cư dân, hợp đồng, hóa đơn dành cho ứng dụng di động.
  - **IoT Telemetry Ingestion API:** API nhận dữ liệu thời gian thực từ cảm biến IoT.
  - **Payment Webhook:** Tiếp nhận thông báo nạp/chuyển tiền tự động từ cổng thanh toán.
