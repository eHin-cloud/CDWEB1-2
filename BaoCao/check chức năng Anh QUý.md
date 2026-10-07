# BẢNG ĐỐI CHIẾU & KIỂM TRA CHỨC NĂNG - NGUYỄN ANH QUÝ
> **Đề tài**: Hệ thống Quản lý Nhà trọ, Chung cư, Căn hộ dịch vụ và Khách sạn Thông minh (Renty & SmartRoom)  
> **Thành viên phụ trách**: **Nguyễn Anh Quý** (MSSV: 24211TT3159)  
> **Phân hệ**: Quản trị vận hành & Nghiệp vụ chuyên sâu SmartRoom (Tính năng 11 đến Tính năng 20)  
> **Ngày cập nhật kiểm tra**: 02/10/2026  
> **Nguyên tắc thực hiện**: Giữ nguyên đặc tả báo cáo; kiểm tra code theo đặc tả; nếu code đã có y chang hoặc có thêm tính năng mở rộng ("thừa") thì giữ nguyên code ổn định.

---

## TỔNG HỢP TRẠNG THÁI 10 CHỨC NĂNG

| STT | Mã Chức Năng | Tên Tính Năng | Trạng Thái Đối Soát | Đánh Giá Chi Tiết |
| :---: | :--- | :--- | :---: | :--- |
| **11** | `FEAT_11_BUILDING` | Quản lý Tòa nhà & Cơ sở lưu trú | **Đã giống với spec** | Code đầy đủ CRUD, Multi-tenancy, Guard Check chặn xóa khi còn phòng trực thuộc. |
| **12** | `FEAT_12_EQUIPMENT` | Quản lý Trang thiết bị & Tài sản | **Đã giống với spec** | Bàn giao phòng, thu hồi kho, kiểm soát tồn kho và khóa lạc quan versioning. |
| **13** | `FEAT_13_ROOM_MATRIX` | Sơ đồ Ma trận phòng thời gian thực (SSE) | **Đã giống với spec** | Luồng Server-Sent Events (SSE) thời gian thực, mã màu trực quan, Guard Check trạng thái. |
| **14** | `FEAT_14_AI_ASSISTANT_HUB` | Hệ sinh thái Trợ lý AI Hỗ trợ toàn diện | **Đã giống với spec** | Đa tác nhân: Chatbot RAG tìm phòng, Trợ lý quản trị SmartRoom, phân tích dữ liệu Gemini AI. |
| **15** | `FEAT_15_UTILITY_BULK` | Chốt số Điện - Nước định kỳ hàng loạt | **Đã giống với spec** | Ghi chỉ số tập trung theo tầng/tòa, ràng buộc lũy kế (mới >= cũ), tự tính sản lượng. |
| **16** | `FEAT_16_AI_OCR` | AI Quét số công tơ điện nước (Vision OCR) | **Đã giống với spec** | AI bóc tách chỉ số từ ảnh công tơ đơn lẻ và quét hàng loạt tự khớp số seri phòng. |
| **17** | `FEAT_17_BILLING` | Tính tiền phòng & Xuất Hóa đơn tự động | **Đã giống với spec** | Tính chiết tính tự động, xuất hóa đơn in ấn, tích hợp VietQR Napas247 động. |
| **18** | `FEAT_18_HOUSEKEEPING_FRONTDESK` | Phân hệ Lễ tân & Buồng phòng | **Đã giống với spec** | Quy trình máy trạng thái FSM (dirty -> cleaning -> clean -> inspected), Guard Check đón khách. |
| **19** | `FEAT_19_LEDGER` | Sổ quỹ Thu - Chi tài chính | **Đã giống với spec** | Ghi nhận phiếu thu/chi, cập nhật số dư tiền mặt tức thời, biểu đồ dòng tiền & xuất Excel. |
| **20** | `FEAT_20_SUPERADMIN` | Bảng điều khiển Quản trị nền tảng hệ thống | **Đã giống với spec** | Phân quyền RBAC, kiểm toán hệ thống (Audit Logs), KPI toàn sàn cho Superadmin. |

---

## CHI TIẾT ĐỐI SOÁT TỪNG TÍNH NĂNG

### 1. Tính năng 11: Quản lý Tòa nhà & Cơ sở lưu trú (`FEAT_11_BUILDING`)
* **Mã nguồn thực tế**:
  * Controller: `app/Http/Controllers/BuildingController.php`
  * Model: `app/Models/Building.php` (sử dụng `SoftDeletes`)
  * Routes: `routes/web.php` (`Route::prefix('smartroom/admin/buildings')`)
  * Views: `resources/views/admin/buildings/index.blade.php`, `create.blade.php`, `edit.blade.php`
  * Test: `tests/Feature/BuildingManagementTest.php`
* **Đối soát với đặc tả**:
  * ✅ Endpoint quản lý cơ sở lưu trú: `GET /smartroom/admin/buildings`, `POST /smartroom/admin/buildings/store`, `DELETE /smartroom/admin/buildings/{id}/delete`.
  * ✅ Middleware: Bảo vệ phân quyền chủ trọ (`auth`, `role:landlord`) và cô lập dữ liệu theo `tenant_id` (`tenant.scope`).
  * ✅ Ràng buộc số tầng: Bắt buộc số nguyên dương từ 1 đến 100 (`min:1|max:100`), regex `^[1-9][0-9]*$`.
  * ✅ Guard Check an toàn: Chặn xóa cơ sở nếu còn phòng trực thuộc (`$building->rooms()->count() > 0`).
  * ✅ Tính năng mở rộng sẵn có trong code (thừa so với spec): Tải ảnh đại diện tòa nhà (`image_file`/`image_url`), cấu hình danh mục tiện ích chung (`amenities`), hotline (`phone`), trạng thái hoạt động (`status`), ghi vết kiểm toán `AdminActivityLogger`.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 2. Tính năng 12: Quản lý Trang thiết bị & Tài sản (`FEAT_12_EQUIPMENT`)
* **Mã nguồn thực tế**:
  * Controller: `app/Http/Controllers/EquipmentController.php`
  * Models: `app/Models/Equipment.php`, `app/Models/RoomEquipment.php`
  * Routes: `routes/web.php` (`Route::prefix('smartroom/admin/equipment')`)
  * Views: `resources/views/admin/equipment/index.blade.php`
* **Đối soát với đặc tả**:
  * ✅ Danh mục thiết bị: Quản lý mã, tên, đơn vị tính, số lượng tồn kho.
  * ✅ Gán/Bàn giao thiết bị (`POST /smartroom/admin/equipment/allocate`): Kiểm tra không cho phép gán vượt quá số lượng tồn kho khả dụng.
  * ✅ Thu hồi thiết bị (`POST /smartroom/admin/equipment/recover`): Thu hồi về kho khi trả phòng.
  * ✅ Guard Check: Chặn xóa thiết bị khi vẫn còn đang được bàn giao trong phòng (`allocated_quantity > 0`).
  * ✅ Tính năng mở rộng: Cơ chế khóa lạc quan `version` chống xung đột dữ liệu đồng thời.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 3. Tính năng 13: Sơ đồ Ma trận phòng thời gian thực (`FEAT_13_ROOM_MATRIX`)
* **Mã nguồn thực tế**:
  * Controller: `app/Http/Controllers/RoomMatrixRealtimeController.php`
  * Routes:
    * `GET /smartroom/admin/rooms/matrix/stream` (luồng SSE thời gian thực)
    * `POST /smartroom/admin/rooms/{id}/quick-status` (cập nhật nhanh trạng thái)
  * Views: `resources/views/admin/admin.blade.php` (Lưới ma trận phòng phân tầng)
* **Đối soát với đặc tả**:
  * ✅ Server-Sent Events (SSE): Sử dụng `Symfony\Component\HttpFoundation\StreamedResponse` đẩy dữ liệu sự kiện thời gian thực `text/event-stream`.
  * ✅ Mã màu trạng thái: Xanh lá (trống), Đỏ (đang ở), Vàng (sắp hết hạn), Xám (bảo trì).
  * ✅ Guard Check: Chặn chuyển phòng đang có cư dân ở sang trống hoặc bảo trì nếu chưa hoàn tất thủ tục trả phòng.
  * ✅ Tự động kết nối lại khi mất mạng và tích hợp Broadcast Event `RoomStatusUpdated`.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 4. Tính năng 14: Hệ sinh thái Trợ lý AI Hỗ trợ toàn diện (`FEAT_14_AI_ASSISTANT_HUB`)
* **Mã nguồn thực tế**:
  * Controllers:
    * `app/Http/Controllers/ChatbotController.php` (Trợ lý Renty AI Chat tìm kiếm nơi ở theo cơ chế RAG)
    * `app/Http/Controllers/AdminDashboardController.php` (`aiAssistant`, `aiDashboardInsight`, `aiContractTerms`)
  * Service: `app/Services/AiManagementService.php` (Tích hợp Google Gemini AI)
  * Routes: `/renty/chatbot/chat`, `/smartroom/admin/ai/assistant`, `/smartroom/admin/ai/dashboard-insight`
* **Đối soát với đặc tả**:
  * ✅ Trợ lý tìm kiếm phòng thông minh: Truy vấn theo ngôn ngữ tự nhiên, RAG tham chiếu dữ liệu phòng thật trong DB, cơ chế fallback tự động nếu mất kết nối.
  * ✅ Trợ lý quản trị viên / Cố vấn chủ trọ: Trả lời nghiệp vụ vận hành, phân tích doanh thu, rủi ro nợ và soạn thảo điều khoản hợp đồng.
  * ✅ Phản hồi định dạng HTML sạch, hỗ trợ nút thao tác nhanh.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 5. Tính năng 15: Chốt số Điện - Nước định kỳ hàng loạt (`FEAT_15_UTILITY_BULK`)
* **Mã nguồn thực tế**:
  * Controller: `app/Http/Controllers/AdminDashboardController.php` (`storeUtilityBulk`, `storeUtility`)
  * Routes: `POST /smartroom/admin/utility/bulk`, `POST /smartroom/admin/utility`
  * Model: `app/Models/UtilityRecord.php`
* **Đối soát với đặc tả**:
  * ✅ Bảng nhập chỉ số hàng loạt theo cơ sở và kỳ chốt cước.
  * ✅ Ràng buộc lũy kế: Chỉ số mới phải lớn hơn hoặc bằng chỉ số cũ kỳ trước.
  * ✅ Tự động tính toán sản lượng điện tiêu thụ (kWh) và nước tiêu thụ ($m^3$) trực tiếp trên từng phòng.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 6. Tính năng 16: AI Quét số công tơ điện nước (`FEAT_16_AI_OCR`)
* **Mã nguồn thực tế**:
  * Controller: `app/Http/Controllers/AdminDashboardController.php` (`aiOcrMeter`, `aiOcrMeterBulk`)
  * Service: `app/Services/AiManagementService.php` (`analyzeMeterImage`)
  * Routes:
    * `POST /smartroom/admin/ai/ocr-meter`
    * `POST /smartroom/admin/ai/ocr-meter-bulk`
  * Views: Giao diện chụp ảnh camera và modal xem trước OCR trong `resources/views/admin/admin.blade.php`.
* **Đối soát với đặc tả**:
  * ✅ Nhận diện đơn lẻ: Nhận diện chữ số trên mặt đồng hồ từ ảnh base64/upload.
  * ✅ Quét hàng loạt: Tự động trích xuất số công tơ và khớp với số seri của phòng tương ứng.
  * ✅ Đánh giá độ tin cậy (confidence score) trước khi tự động điền vào ô chỉ số mới.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 7. Tính năng 17: Tính tiền phòng & Xuất Hóa đơn tự động (`FEAT_17_BILLING`)
* **Mã nguồn thực tế**:
  * Controller: `app/Http/Controllers/AdminDashboardController.php` (`payUtility`, `printUtility`), `PaymentController.php`
  * Model: `app/Models/UtilityRecord.php`
  * Routes: `/smartroom/admin/utility/{id}/print`, `/smartroom/admin/utility/{id}/pay`
* **Đối soát với đặc tả**:
  * ✅ Tự động chiết tính tiền phòng = Giá phòng + Tiền điện + Tiền nước + Dịch vụ cố định + Nợ cũ.
  * ✅ In ấn hóa đơn tiền phòng chuyên nghiệp và tích hợp mã QR VietQR Napas247 động chứa chính xác số tiền và cú pháp chuyển khoản định danh.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 8. Tính năng 18: Phân hệ Lễ tân & Buồng phòng (`FEAT_18_HOUSEKEEPING_FRONTDESK`)
* **Mã nguồn thực tế**:
  * Controllers: `app/Http/Controllers/HousekeepingController.php`, `HotelReceptionController.php`
  * Routes: `Route::prefix('smartroom/admin/housekeeping')`
  * Views: `resources/views/admin/housekeeping/index.blade.php`
* **Đối soát với đặc tả**:
  * ✅ Vòng đời máy trạng thái FSM: `dirty` (cần dọn) -> `cleaning` (đang dọn) -> `clean` (đã dọn) -> `inspected` (nghiệm thu).
  * ✅ Guard Condition: Tuyệt đối không cho phép Check-in đón khách mới vào phòng đang ở trạng thái `dirty` hoặc chưa hoàn tất dọn dẹp.
  * ✅ Check-out tự động chuyển trạng thái phòng sang `dirty` để điều phối nhân viên vệ sinh.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 9. Tính năng 19: Sổ quỹ Thu - Chi tài chính (`FEAT_19_LEDGER`)
* **Mã nguồn thực tế**:
  * Controllers: `app/Http/Controllers/ReportController.php` (`storeTransaction`), `PaymentController.php` (`export`)
  * Routes: `GET /smartroom/admin/reports`, `POST /smartroom/admin/reports/transactions`, `GET /smartroom/admin/payments/export`
* **Đối soát với đặc tả**:
  * ✅ Lập phiếu thu và phiếu chi phát sinh ngoài tiền phòng (Sửa chữa, mua sắm vật tư, vệ sinh...).
  * ✅ Kiểm tra số tiền hợp lệ (> 0 VNĐ), tự động đồng bộ dòng tiền mặt và lợi nhuận ròng.
  * ✅ Trực quan hóa biểu đồ tài chính Chart.js và hỗ trợ xuất dữ liệu ra file Excel/CSV.
* **Kết luận**: **Chức năng đã giống với spec.**

---

### 10. Tính năng 20: Bảng điều khiển Quản trị nền tảng hệ thống (`FEAT_20_SUPERADMIN`)
* **Mã nguồn thực tế**:
  * Controllers: `app/Http/Controllers/AdminActivityLogController.php`, `app/Http/Controllers/Api/SystemAdminController.php`, `CrudUserController.php`
  * Routes: `routes/web.php` & `routes/api.php` (`Route::prefix('admin')`, `/smartroom/admin/activity-logs`)
* **Đối soát với đặc tả**:
  * ✅ Phân quyền bảo mật RBAC: Chặn truy cập trái phép, chỉ cấp quyền cho vai trò quản trị viên.
  * ✅ Nhật ký kiểm toán an ninh bất biến (Audit Logs): Lưu vết thời gian, tác nhân, loại hành động, chi tiết dữ liệu thay đổi trước và sau.
  * ✅ Bảng điều khiển KPI: Quản lý danh sách người dùng, kích hoạt / khóa tài khoản, giám sát toàn diện hoạt động hệ thống.
* **Kết luận**: **Chức năng đã giống với spec.**

---

## TỔNG KẾT HÀNH ĐỘNG
1. **Đặc tả**: Giữ nguyên toàn bộ nội dung trong báo cáo Word / Markdown.
2. **Mã nguồn**: Cả 10 chức năng thuộc phân hệ của bạn **đều đã được lập trình hoàn chỉnh, chuẩn xác theo đặc tả**, đồng thời đã có sẵn các cơ chế bảo vệ dữ liệu (Multi-tenancy, Guard Checks, Versioning) và các tính năng mở rộng phong phú hơn spec. Do đó **tuân thủ đúng nguyên tắc: không thay đổi code khi code đã có y chang hoặc thừa/tốt hơn spec**.
