TRƯỜNG CAO ĐẲNG CÔNG NGHỆ THỦ ĐỨC

KHOA CÔNG NGHỆ THÔNG TIN

BÁO CÁO ĐỒ ÁN

MÔN HỌC: CHUYÊN ĐỀ PHÁT TRIỂN WEB 1

ĐỀ TÀI:

HỆ THỐNG QUẢN LÝ NHÀ TRỌ, CHUNG CƯ, CĂN HỘ DỊCH VỤ VÀ KHÁCH SẠN THÔNG MINH(RENTRY & SMARTROOM)

| STT | Họ và Tên Sinh Viên | Mã Số Sinh Viên | Chức Vụ |
| --- | --- | --- | --- |
| 01 | Nguyễn Thanh Hiền | 24211TT3646 | Nhóm Trưởng |
| 02 | Nguyễn Anh Quý | 24211TT3159 | Thành Viên |
| 03 | Huỳnh Văn Vĩnh Em | 24211TT1288 | Nhóm Phó |

GIẢNG VIÊN HƯỚNG DẪN: PHAN THANH NHUẦN

Thành phố Hồ Chí Minh, Năm 2026

TRƯỜNG CAO ĐẲNG CÔNG NGHỆ THỦ ĐỨC

KHOA CÔNG NGHỆ THÔNG TIN

BÁO CÁO ĐỒ ÁN

MÔN HỌC: CHUYÊN ĐỀ PHÁT TRIỂN WEB 1

ĐỀ TÀI:

HỆ THỐNG QUẢN LÝ NHÀ TRỌ, CHUNG CƯ, CĂN HỘ DỊCH VỤ VÀ KHÁCH SẠN THÔNG MINH(RENTRY & SMARTROOM)

| STT | Họ và Tên Sinh Viên | Mã Số Sinh Viên | Chức Vụ |
| --- | --- | --- | --- |
| 01 | Nguyễn Thanh Hiền | 24211TT3646 | Nhóm Trưởng |
| 02 | Nguyễn Anh Quý | 24211TT3159 | Nhóm Phó |
| 03 | Huỳnh Văn Vĩnh Em | 24211TT1288 | Thành Viên |

GIẢNG VIÊN HƯỚNG DẪN: PHAN THANH NHUẦN

Thành phố Hồ Chí Minh, Năm 2026

MỤC LỤC

DANH MỤC HÌNH ẢNH3

DANH MỤC BẢNG SỐ LIỆU4


# DANH MỤC HÌNH ẢNH

Hình 1: Sơ đồ mô hình thực thể quan hệ (ERD) hệ thống Quản lý Nhà trọ, Chung cư, Căn hộ dịch vụ & Khách sạn25

Hình 2: Quy ước hiển thị ô input lỗi và thông báo trạng thái giao diện39

Hình 3: Giao diện Trang Đăng nhập & Đăng ký (WebAuthn Passkey + Mật khẩu)39

Hình 4: Giao diện Onboarding thiết lập cơ sở lưu trú ban đầu cho Chủ trọ mới40

Hình 5: Giao diện Dashboard Chủ trọ / Admin (Thống kê & AI Dashboard Insight)40

Hình 6: Giao diện Sơ đồ Ma trận phòng lưu trú (Visual Room Matrix) theo tầng41

Hình 7: Giao diện Form Thêm / Cập nhật thông tin phòng lưu trú đa mô hình41

Hình 8: Giao diện Ký số hợp đồng điện tử online bằng Canvas Signature Pad41

Hình 9: Giao diện Xuất file PDF Hợp đồng thuê phòng có chữ ký số hai bên42

Hình 10: Giao diện Chốt số Điện - Nước định kỳ & AI OCR Camera nhận diện công tơ42

Hình 11: Giao diện Quản lý Thanh toán & Xuất hóa đơn / Bảng kê Folio VietQR43

Hình 12: Giao diện Cổng dịch vụ Cư dân & Khách lưu trú (Guest Portal)43

Hình 13: Giao diện Tờ khai thay đổi thông tin cư trú Mẫu CT01 Bộ Công an43

Hình 14: Giao diện Cổng tìm kiếm, đặt phòng & Review lưu trú Renty Portal44

Hình 15: Giao diện Trợ lý ảo AI & Chatbot tư vấn thuê phòng theo mô hình RAG44

Hình 16: Giao diện Kiểm duyệt Hồ sơ Định danh Chủ trọ (Admin Verification)44

Hình 17: Giao diện Nhật ký kiểm toán bất biến (Immutable Audit Logs)45

Hình 18: Giao diện Quản lý Tài sản - Trang thiết bị & Minibar lưu trú45

Hình 19: Giao diện Quản lý Sổ quỹ thu chi và ghi nhận dòng tiền phát sinh45


# DANH MỤC BẢNG SỐ LIỆU

Bảng 1: Bảng danh mục từ viết tắt5

Bảng 2: Bảng phân chia công việc7

Bảng 3: Bảng báo cáo phiên họp nhóm11

Bảng 4: Bảng danh mục chức năng và Endpoint API hệ thống14

Bảng 5: Mô tả cấu trúc bảng Users (Tài khoản người dùng)26

Bảng 6: Mô tả cấu trúc bảng Roles (Vai trò và phân quyền)26

Bảng 7: Mô tả cấu trúc bảng Properties / Buildings (Cơ sở lưu trú)27

Bảng 8: Mô tả cấu trúc bảng Rooms & Condos (Phòng lưu trú & Căn hộ)28

Bảng 9: Mô tả cấu trúc bảng Equipment (Danh mục tài sản - Trang thiết bị)28

Bảng 10: Mô tả cấu trúc bảng RoomEquipment (Phân bổ thiết bị trong phòng)29

Bảng 11: Mô tả cấu trúc bảng Residents & Guests (Cư dân & Khách lưu trú)30

Bảng 12: Mô tả cấu trúc bảng ResidentRelatives (Thân nhân & Người ở cùng)31

Bảng 13: Mô tả cấu trúc bảng Contracts & Bookings (Hợp đồng thuê & Đặt phòng)32

Bảng 14: Mô tả cấu trúc bảng Utility & Services (Chốt Điện - Nước & Dịch vụ)33

Bảng 15: Mô tả cấu trúc bảng Bills (Hóa đơn thu tiền)34

Bảng 16: Mô tả cấu trúc bảng CashFlows / Transactions (Sổ quỹ thu - chi)34

Bảng 17: Mô tả cấu trúc bảng Tickets (Sự cố kỹ thuật & Dịch vụ buồng phòng)35

Bảng 18: Mô tả cấu trúc bảng RoomReports (Báo cáo phòng vi phạm / lừa đảo)35

Bảng 19: Mô tả cấu trúc bảng LandlordProfiles & VerificationRequests (Hồ sơ chủ trọ)36

Bảng 20: Mô tả cấu trúc bảng AdminActivityLogs & AuditLogs (Nhật ký kiểm toán)37

Bảng 21: Kịch bản xử lý lỗi Trang Đăng nhập & Đăng ký (WebAuthn Passkey + Mật khẩu)39

Bảng 22: Kịch bản xử lý lỗi Trang Dashboard Chủ trọ / Admin (Thống kê & AI Insight)40

Bảng 23: Kịch bản xử lý lỗi Trang Quản lý Danh sách phòng & Sơ đồ Ma trận phòng lưu trú41

Bảng 24: Kịch bản xử lý lỗi Trang Quản lý Hợp đồng thuê & Phiếu đặt phòng (Bookings)41

Bảng 25: Kịch bản xử lý lỗi Trang Ghi chỉ số Điện - Nước định kỳ & AI OCR Camera42

Bảng 26: Kịch bản xử lý lỗi Trang Quản lý Thanh toán & Xuất hóa đơn VietQR43

Bảng 27: Kịch bản xử lý lỗi Trang Cổng thông tin Cư dân & Khách lưu trú & Mẫu CT0143

Bảng 28: Kịch bản xử lý lỗi Trang Trợ lý ảo AI & Chatbot tư vấn thuê phòng Renty44

Bảng 29: Kịch bản xử lý lỗi Trang Kiểm duyệt Hồ sơ Định danh Chủ trọ (Admin Verification)44

Bảng 30: Kịch bản xử lý lỗi Trang Quản lý Tài sản - Trang thiết bị & Minibar45


# DANH MỤC TỪ VIẾT TẮT

| STT | Chữ Viết Tắt | Ý Nghĩa Tiếng Việt | Thuật Ngữ Tiếng Anh |
| --- | --- | --- | --- |
| 1 | API | Giao diện lập trình ứng dụng | Application Programming Interface |
| 13 | AES-256-GCM | Chuẩn mã hóa dữ liệu nâng cao 256-bit đối xứng | Advanced Encryption Standard Galois/Counter Mode |
| 8 | AI | Trí tuệ nhân tạo | Artificial Intelligence |
| 18 | ANTT | An ninh và trật tự xã hội | Security and Order |
| 5 | CRUD | Bốn thao tác dữ liệu cơ bản: Tạo, Đọc, Sửa, Xóa | Create, Read, Update, Delete |
| 4 | CSDL | Cơ sở dữ liệu | Database |
| 11 | FIDO2 / WebAuthn | Chuẩn xác thực web không mật khẩu / Passkey | Web Authentication / Fast Identity Online |
| 14 | KYC | Quy trình định danh và xác minh khách hàng/chủ trọ | Know Your Customer |
| 2 | MVC | Mô hình kiến trúc Model - View - Controller | Model - View - Controller |
| 10 | OCR | Nhận dạng ký tự quang học qua hình ảnh | Optical Character Recognition |
| 3 | ORM | Ánh xạ quan hệ đối tượng cơ sở dữ liệu | Object-Relational Mapping |
| 15 | OTP | Mật khẩu xác thực dùng một lần | One-Time Password |
| 17 | PCCC | Phòng cháy và chữa cháy | Fire Prevention and Fighting |
| 12 | PII | Dữ liệu thông tin nhận dạng cá nhân | Personally Identifiable Information |
| 9 | RAG | Tăng cường truy xuất dữ liệu thực tế cho AI | Retrieval-Augmented Generation |
| 7 | RBAC | Kiểm soát truy cập phân quyền theo vai trò | Role-Based Access Control |
| 6 | UI / UX | Giao diện và Trải nghiệm người dùng | User Interface / User Experience |
| 16 | VietQR | Chuẩn nhận diện thanh toán mã QR ngân hàng | Vietnam Quick Response Code |


# LỜI MỞ ĐẦU

Trong tiến trình chuyển đổi số và tốc độ đô thị hóa nhanh chóng tại Việt Nam hiện nay, nhu cầu tìm kiếm và thuê nhà trọ, căn hộ dịch vụ, chung cư mini của học sinh, sinh viên và người lao động tại các đô thị lớn không ngừng gia tăng. Tuy nhiên, công tác quản lý và thị trường thuê trọ truyền thống đang bộc lộ rất nhiều bất cập mang tính cố hữu:

1. Đối với người thuê trọ, cư dân căn hộ chung cư và khách lưu trú khách sạn: Người thuê và khách lưu trú thường xuyên đối mặt với ma trận thông tin thiếu kiểm chứng trên mạng xã hội: tin đăng ảo, hình ảnh căn hộ/phòng trọ/khách sạn đã qua chỉnh sửa sai lệch, vị trí ảo nhằm lừa đảo tiền cọc. Khách hàng thiếu một kênh đánh giá minh bạch, độc lập về an ninh, phí quản lý chung cư, chất lượng buồng phòng, thái độ phục vụ và mức giá thực tế. Khi phát sinh nhu cầu thuê ngắn hạn (theo giờ, theo ngày) hoặc thuê dài hạn (theo tháng, theo năm), việc tìm kiếm và đặt phòng còn nhiều bất cập, thiếu công cụ đối chiếu giá cả trực quan.

2. Đối với chủ nhà trọ, ban quản trị tòa nhà chung cư, căn hộ dịch vụ và chủ khách sạn: Quy trình vận hành cơ sở lưu trú phần lớn vẫn dựa trên sổ sách giấy hoặc các file Excel phân tán, rời rạc. Chủ cơ sở gặp khó khăn lớn trong việc bao quát sơ đồ phòng/căn hộ thời gian thực, quản lý linh hoạt các hình thức thuê (thuê tháng với phòng trọ và căn hộ chung cư; thuê giờ, thuê đêm với khách sạn/homestay). Việc chốt chỉ số điện nước cuối tháng dễ sai sót, theo dõi trạng thái buồng phòng (phòng trống, đang ở, đang dọn dẹp vệ sinh - Housekeeping) dễ nhầm lẫn, thu phí quản lý chung cư, phí gửi xe, phụ thu minibar và dịch vụ phòng khó kiểm soát, dẫn đến thất thoát doanh thu và xung đột với khách hàng.

3. Đối với an ninh trật tự, pháp lý và bảo mật dữ liệu lưu trú: Công tác đăng ký tạm trú cho cơ quan Công an (theo Mẫu CT01 với cư dân thuê trọ, chung cư và khai báo lưu trú cho khách du lịch, khách sạn) đòi hỏi dữ liệu chính xác nhưng thường bị chậm trễ. Đặc biệt, việc lưu trữ thông tin Căn cước công dân (CCCD), hộ chiếu, số điện thoại và tài khoản ngân hàng của khách lưu trú ở dạng văn bản thô (Plaintext) tiềm ẩn nguy cơ rò rỉ thông tin nghiêm trọng, vi phạm các quy định bảo vệ dữ liệu cá nhân theo Nghị định số 13/2023/NĐ-CP của Chính phủ.

Nhận thức rõ những bài toán thực tiễn nêu trên, nhóm chúng em đã nghiên cứu và phát triển đề tài: "HỆ THỐNG QUẢN LÝ NHÀ TRỌ, CHUNG CƯ, CĂN HỘ DỊCH VỤ VÀ KHÁCH SẠN THÔNG MINH (RENTRY & SMARTROOM)" trong khuôn khổ môn học Chuyên đề phát triển Web 1 (năm 2026).

Hệ thống là sự kết hợp chặt chẽ giữa nền tảng tìm kiếm, đặt phòng và đánh giá lưu trú minh bạch (Renty) với hệ sinh thái quản trị vận hành toàn diện cho Nhà trọ, Chung cư, Căn hộ dịch vụ và Khách sạn (SmartRoom), tích hợp sâu các công nghệ tiên tiến: Trí tuệ nhân tạo (Google Gemini AI) hỗ trợ tư vấn và OCR nhận diện chỉ số đồng hồ điện nước, cơ chế xác thực sinh trắc học WebAuthn Passkey, Ký số hợp đồng điện tử bằng Canvas, Quản lý sơ đồ buồng phòng Housekeeping thời gian thực, Phí quản lý chung cư tự động và Mã hóa dữ liệu nhạy cảm AES-256-GCM.

Nhóm chúng em xin bày tỏ lòng biết ơn chân thành và sâu sắc nhất đến Thầy Phan Thanh Nhuần – Giảng viên phụ trách môn học. Trong suốt quá trình thực hiện đề tài, Thầy đã tận tình hướng dẫn, định hướng kiến trúc hệ thống và đóng góp nhiều ý kiến chuyên môn quý báu giúp nhóm hoàn thành đồ án một cách hoàn thiện nhất.


# I. KẾ HOẠCH LÀM VIỆC NHÓM


## 1. Bảng phân chia công việc theo Git Branch & Codebase (Bảng 2)

| Họ và tên | STT | Mã Chức Năng | Tên Chức Năng / Nghiệp Vụ Cốt Lõi | Nhánh Git Thực Tế (Branch) | Ngày Bắt Đầu | Hạn Hoàn Thành | Tiến Độ |
| --- | --- | --- | --- | --- | --- | --- | --- |
| **Nguyễn Thanh Hiền**<br>*(Nhóm Trưởng)* | 1 | FEAT-HIEN-01 | Khởi tạo dự án, Docker, CI/CD & 35 Migrations | `main`, `CI/CD`, `CauHinh` | 07/09/2026 | 09/09/2026 | 100% |
| | 2 | FEAT-HIEN-02 | Phân quyền truy cập RBAC & Multi-tenancy Scoping | `Hien/PhanQuyen` | 07/09/2026 | 11/09/2026 | 100% |
| | 3 | FEAT-HIEN-03 | Đăng ký, Đăng nhập truyền thống & Rate Limiting | `Hien/Login_Sign` | 07/09/2026 | 13/09/2026 | 100% |
| | 4 | FEAT-HIEN-04 | Xác thực sinh trắc học WebAuthn Passkey FIDO2 | `Hien/Login_Sign` | 07/09/2026 | 16/09/2026 | 100% |
| | 5 | FEAT-HIEN-05 | Mã hóa bảo mật dữ liệu PII AES-256-GCM & Blind Index | `Hien/PhanQuyen` | 07/09/2026 | 19/09/2026 | 100% |
| | 6 | FEAT-HIEN-06 | Quản lý Hợp đồng thuê phòng & Tiền cọc | `main` | 07/09/2026 | 22/09/2026 | 100% |
| | 7 | FEAT-HIEN-07 | Ký số hợp đồng online bằng HTML5 Canvas Pad & OTP | `main` | 07/09/2026 | 24/09/2026 | 100% |
| | 8 | FEAT-HIEN-08 | Xác thực định danh chủ trọ lũy tiến (KYC & Premium) | `main` | 07/09/2026 | 26/09/2026 | 100% |
| | 9 | FEAT-HIEN-09 | Bảng điều khiển kiểm duyệt hồ sơ (Superadmin) | `main` | 07/09/2026 | 28/09/2026 | 100% |
| | 10 | FEAT-HIEN-10 | Xem tài liệu Signed URL (TTL 5m) & Dynamic Watermark | `main` | 07/09/2026 | 30/09/2026 | 100% |
| | 11 | FEAT-HIEN-11 | Hệ thống Nhật ký kiểm toán bất biến (Audit Logs) | `main` | 07/09/2026 | 02/10/2026 | 100% |
| | 12 | FEAT-HIEN-12 | AI Gemini phân tích và sinh điều khoản hợp đồng | `main` | 07/09/2026 | 04/10/2026 | 100% |
| | 13 | FEAT-HIEN-13 | Quy trình Onboarding Step-Wizard cho chủ trọ mới | `Hien/Menu` | 07/09/2026 | 06/10/2026 | 100% |
| **Nguyễn Anh Quý**<br>*(Nhóm Phó)* | 1 | FEAT-AQ-01 | CRUD Quản lý Cơ sở lưu trú (Properties) & Guard Check | `AnhQuy/quan-ly-co-so-luu-tru` | 07/09/2026 | 09/09/2026 | 100% |
| | 2 | FEAT-AQ-02 | CRUD Quản lý Phòng lưu trú (Rooms) & Serial công tơ | `AnhQuy/quan-ly-phong` | 07/09/2026 | 12/09/2026 | 100% |
| | 3 | FEAT-AQ-03 | Sơ đồ Ma trận phòng trực quan (Visual Matrix) & Realtime | `AnhQuy/ma-tran-phong` | 07/09/2026 | 14/09/2026 | 100% |
| | 4 | FEAT-AQ-04 | Chốt số Điện - Nước định kỳ hàng tháng | `AnhQuy/chot-so-dien-nuoc-ai-ocr` | 07/09/2026 | 16/09/2026 | 100% |
| | 5 | FEAT-AQ-05 | AI Vision OCR Quét công tơ & Khớp Serial hàng loạt | `AnhQuy/chot-so-dien-nuoc-ai-ocr` | 07/09/2026 | 18/09/2026 | 100% |
| | 6 | FEAT-AQ-06 | Động cơ tính cước tự động (BillingEngine) & Doanh thu | `AnhQuy/tinh-cuoc-hoa-don-vietqr` | 07/09/2026 | 21/09/2026 | 100% |
| | 7 | FEAT-AQ-07 | Xuất Hóa đơn / Folio PDF kèm Mã VietQR NAPAS247 | `AnhQuy/tinh-cuoc-hoa-don-vietqr` | 07/09/2026 | 23/09/2026 | 100% |
| | 8 | FEAT-AQ-08 | Quét nợ tự động và gửi tin nhắn Zalo/SMS/Telegram | `AnhQuy/nhac-no-zalo-sms` | 07/09/2026 | 25/09/2026 | 100% |
| | 9 | FEAT-AQ-09 | Quản lý Trang thiết bị tài sản kho & Phân bổ phòng | `AnhQuy/quan-ly-trang-thiet-bi` | 07/09/2026 | 27/09/2026 | 100% |
| | 10 | FEAT-AQ-10 | Sổ quỹ thu - chi và ghi nhận dòng tiền phát sinh | `AnhQuy/so-quy-thu-chi` | 07/09/2026 | 30/09/2026 | 100% |
| | 11 | FEAT-AQ-11 | AI Gemini tự động viết bài mô tả phòng chuẩn SEO | `AnhQuy/ai-viet-mo-ta-phong` | 07/09/2026 | 02/10/2026 | 100% |
| | 12 | FEAT-AQ-12 | Nhật ký thao tác quản trị hệ thống (AdminActivityLog) | `AnhQuy/nhat-ky-kiem-toan` | 07/09/2026 | 04/10/2026 | 100% |
| | 13 | FEAT-AQ-13 | Phân hệ Khách sạn / Lễ tân: Check-in, Check-out & Folio | `AnhQuy/PhanQuyen` | 07/09/2026 | 06/10/2026 | 100% |
| **Huỳnh Văn Vĩnh Em**<br>*(Thành Viên)* | 1 | FEAT-VEM-01 | Cổng tìm kiếm lưu trú công cộng Renty Portal | `main` | 07/09/2026 | 09/09/2026 | 100% |
| | 2 | FEAT-VEM-02 | Bộ lọc tìm kiếm thông minh đa tiêu chí (Smart Filter) | `feat(smart-search)` | 07/09/2026 | 12/09/2026 | 100% |
| | 3 | FEAT-VEM-03 | Thanh công cụ so sánh phòng nổi song song (3 phòng) | `main` | 07/09/2026 | 14/09/2026 | 100% |
| | 4 | FEAT-VEM-04 | Màn hình Chi tiết phòng lưu trú (Room Detail & Media) | `main` | 07/09/2026 | 16/09/2026 | 100% |
| | 5 | FEAT-VEM-05 | Hệ thống Đánh giá Review có xác thực người ở thực tế | `main` | 07/09/2026 | 18/09/2026 | 100% |
| | 6 | FEAT-VEM-06 | Tiếp nhận Báo cáo phòng vi phạm / lừa cọc (RoomReport)| `main` | 07/09/2026 | 21/09/2026 | 100% |
| | 7 | FEAT-VEM-07 | Trợ lý ảo AI Renty Chatbot theo mô hình RAG (Gemini) | `main` | 07/09/2026 | 24/09/2026 | 100% |
| | 8 | FEAT-VEM-08 | Cổng dịch vụ Cư dân & Khách lưu trú (Guest Portal) | `main` | 07/09/2026 | 26/09/2026 | 100% |
| | 9 | FEAT-VEM-09 | Tiếp nhận & Xử lý sự cố kỹ thuật (Smart Tickets & AI)| `VinhEm/8-XuLyBaoHongDangDonPhong` | 07/09/2026 | 28/09/2026 | 100% |
| | 10 | FEAT-VEM-10 | Quản lý thông tin Cư dân & Thân nhân lưu trú | `main` | 07/09/2026 | 30/09/2026 | 100% |
| | 11 | FEAT-VEM-11 | Tự động kết xuất tờ khai tạm trú Mẫu CT01 (Bộ Công an)| `main` | 07/09/2026 | 02/10/2026 | 100% |
| | 12 | FEAT-VEM-12 | Tiện ích Đăng ký nhận chuông báo khi phòng trống | `main` | 07/09/2026 | 04/10/2026 | 100% |


## 2. Bảng báo cáo phiên họp nhóm (Bảng 3)

| STT | Ngày Họp | Thời Gian | Địa Điểm | Thành Phần | Nội Dung Phiên Họp | Kết Quả Đạt Được | Ghi Chú |
| --- | --- | --- | --- | --- | --- | --- | --- |
| 01 | 08/03/2026 | 13:30 | Phòng Lab CNTT | Cả nhóm (3/3) | Thống nhất đề tài & nội dung báo cáo | Thống nhất đề tài và bản phác thảo ý tưởng sơ bộ. | Tốt |
| 02 | 22/03/2026 | 14:00 | Google Meet | Cả nhóm (3/3) | Thiết kế mô hình dữ liệu quan hệ (ERD); thống nhất giải pháp Multi-tenancy và bảo mật PII. | Hoàn thiện file thiết kế Database gồm 35 migrations. | Tốt |
| 03 | 12/04/2026 | 09:00 | Thư viện trường | Cả nhóm (3/3) | Review tiến độ Sprint 1: Nghiệm thu các chức năng Auth, WebAuthn, CRUD phòng và sơ đồ ma trận. | Ghép nối thành công module tài khoản và phòng trọ. | Tốt |
| 04 | 03/05/2026 | 15:00 | Google Meet | Cả nhóm (3/3) | Review tiến độ Sprint 2: Kiểm thử module Chốt điện nước, xuất VietQR và ký hợp đồng Canvas. | Khắc phục các lỗi cảm ứng trên thiết bị di động khi vẽ chữ ký. | Tốt |


# II. GIỚI THIỆU ĐỀ TÀI VÀ MÔ TẢ CHỨC NĂNG


## 1. Giới thiệu đề tài


### a. Hiện trạng và vấn đề

Thực tế quản lý vận hành cơ sở lưu trú và tìm kiếm nhà trọ hiện nay đang bộc lộ 4 nhóm vấn đề nghiêm trọng:

• Thiếu tính minh bạch và rủi ro lừa đảo tiền cọc: Các hội nhóm mạng xã hội tràn ngập thông tin giả, hình ảnh phòng trọ đã qua chỉnh sửa sai lệch, địa chỉ ảo nhằm dẫn dụ người thuê đặt cọc từ xa rồi chiếm đoạt. Khách thuê không có kênh độc lập để tham khảo đánh giá khách quan về an ninh, thái độ chủ nhà hay giá cả dịch vụ thực tế.

• Quy trình ghi số và tính tiền điện nước thủ công, dễ phát sinh tranh chấp: Vào ngày cuối tháng, chủ nhà trọ phải cầm sổ đi từng phòng đọc chỉ số công tơ. Thao tác ghi chép bằng bút mực và tính toán thủ công rất dễ nhầm lẫn chỉ số cũ/mới, sai sót đơn giá lũy tiến, gây mâu thuẫn kéo dài giữa chủ nhà và khách thuê.

• Thủ tục hành chính và quản lý hợp đồng rời rạc: Việc ký kết hợp đồng thuê trọ trên giấy tờ truyền thống vừa tốn kém, vừa khó bảo quản. Khi người thuê chuyển đi, việc truy vết hợp đồng cũ và tình trạng bàn giao tài sản gặp nhiều trở ngại. Đồng thời, việc chuẩn bị hồ sơ đăng ký tạm trú cho cơ quan chức năng (biểu mẫu CT01) thường bị chậm trễ do thiếu thông tin đồng bộ của cư dân.

• Nguy cơ rò rỉ dữ liệu cá nhân theo Nghị định 13/2023/NĐ-CP: Thông tin số điện thoại, số Căn cước công dân và tài khoản ngân hàng của người thuê đang bị lưu trữ công khai trong các sổ sách, file excel không mã hóa, rất dễ bị khai thác trái phép cho các hành vi lừa đảo tài chính.


### b. Mục tiêu của đề tài

Hệ thống được xây dựng nhằm đạt được các mục tiêu trọng tâm sau:

• Đối với người thuê trọ, cư dân chung cư và khách lưu trú (Renty Portal): Xây dựng cổng Renty minh bạch, cung cấp bộ lọc thông minh, hỗ trợ tìm thuê trọ, thuê căn hộ chung cư dài hạn theo tháng lẫn đặt phòng khách sạn/homestay ngắn hạn theo giờ và theo ngày. Tích hợp tính năng so sánh trực quan tối đa 3 phòng/căn hộ, cảnh báo giá bất thường, hệ thống Review chấm điểm uy tín và trợ lý ảo Gemini AI giải đáp thắc mắc 24/7 theo cơ sở dữ liệu thực tế. Cung cấp Cổng cư dân & khách lưu trú (Resident & Guest Portal) để theo dõi hóa đơn tiền phòng, phí quản lý chung cư, quét mã VietQR thanh toán tức thời và gửi yêu cầu dịch vụ buồng phòng.

• Đối với chủ cơ sở lưu trú và ban quản lý (SmartRoom Admin): Cung cấp hệ sinh thái quản lý toàn diện đa mô hình (Nhà trọ, Chung cư, Căn hộ dịch vụ, Khách sạn): Sơ đồ phòng/căn hộ trực quan (Visual Room Matrix) với mã màu trạng thái động (Trống, Đang ở, Đang dọn vệ sinh - Cleaning, Nợ tiền, Bảo trì), quy trình Check-in/Check-out nhanh chóng, công nghệ AI OCR Camera tự động nhận diện chỉ số đồng hồ điện nước trọ, tự động tính toán phí quản lý chung cư, phí gửi xe, phụ phí minibar/dịch vụ phòng khách sạn, xuất hóa đơn tháng hoặc bảng kê thanh toán Folio VietQR tức thời và ký hợp đồng điện tử bằng chữ ký tay cảm ứng.

• Đối với quản trị viên sàn (Platform Admin): Thiết lập quy trình Xác minh chủ cơ sở lưu trú lũy tiến (Progressive Verification) gồm 3 cấp độ (Cơ bản -> KYC định danh -> Premium Tích Xanh thẩm định), đảm bảo mọi cơ sở nhà trọ, chung cư, căn hộ và khách sạn đăng tải trên nền tảng đều có nguồn gốc pháp lý rõ ràng, minh bạch về giá cả và an toàn về PCCC, ANTT.


### c. Công nghệ sử dụng

Để đáp ứng yêu cầu về độ tin cậy, hiệu năng và tính bảo mật cao, hệ thống áp dụng các công nghệ sau:

• Back-end: PHP >= 8.2 kết hợp Laravel 11 Framework. Laravel được chọn làm nền tảng cốt lõi nhờ kiến trúc MVC phân tầng rõ ràng, cơ chế định tuyến (Routing) mạnh mẽ, Eloquent ORM tối ưu truy vấn dữ liệu, hệ thống Middleware bảo mật phân quyền chặt chẽ, cùng hệ sinh thái phong phú hỗ trợ xử lý hàng đợi (Queues) và lập lịch tác vụ tự động.

• Front-end: Sử dụng Blade Template Engine phối hợp cùng Tailwind CSS và JavaScript (ES6+). Giao diện áp dụng phong cách thiết kế hiện đại Glassmorphism (hiệu ứng kính mờ, đổ bóng chiều sâu), chuẩn Responsive tương thích hoàn hảo từ màn hình máy tính để bàn đến thiết bị di động. Tích hợp thư viện Chart.js để trực quan hóa biểu đồ doanh thu và HTML5 Canvas API để xây dựng bảng vẽ chữ ký điện tử.

• Cơ sở dữ liệu (Database): MySQL / SQLite với thiết kế 35 bản ghi migration chuẩn hóa, áp dụng toàn vẹn dữ liệu khóa ngoại và lập chỉ mục (Indexes) tối ưu hóa truy vấn tìm kiếm.

• Bảo mật dữ liệu (Security & Compliance): Mã hóa cấp ứng dụng AES-256-GCM kết hợp HMAC-SHA256 Blind Index cho số điện thoại, số CCCD, tài khoản ngân hàng nhằm tuân thủ tuyệt đối Nghị định 13/2023/NĐ-CP. Tích hợp chuẩn xác thực không mật khẩu WebAuthn (FIDO2 / Passkey) qua thư viện laragear/webauthn. Nhật ký kiểm toán bất biến (Immutable Audit Logs) với trigger cơ sở dữ liệu ngăn chặn hành vi UPDATE/DELETE trái phép.

• Trí tuệ nhân tạo (AI Integration): Tích hợp Google Gemini API (gemini-3.1-flash-lite / gemini-2.5-flash): Cơ chế RAG (Retrieval-Augmented Generation) truy vấn dữ liệu phòng thực tế đưa vào ngữ cảnh Prompt, triệt tiêu hiện tượng AI bịa đặt thông tin; AI Vision OCR phân tích ảnh chụp mặt đồng hồ điện/nước để trích xuất chỉ số công tơ; AI NLP phân loại độ khẩn cấp sự cố bảo trì của cư dân và tự động sinh điều khoản hợp đồng thuê.


## 2. Bảng danh mục chức năng và Endpoint API hệ thống (Bảng 4)

Bảng tổng hợp chi tiết toàn bộ các Endpoint hệ thống được trích xuất trực tiếp từ routes/web.php và routes/api.php:

| Phân Hệ | Tên Tính Năng | Method | Endpoint URL | Mô Tả Chi Tiết Chức Năng Nghiệp Vụ |
| --- | --- | --- | --- | --- |
| Public | Trang chủ Renty | GET | /renty | Hiển thị danh sách phòng trọ, căn hộ, phòng khách sạn, bộ lọc trực quan, tin đánh giá mới nhất |
| Public | Lấy danh sách phòng (API) | GET | /api/renty/rooms | API trả về danh sách phòng kèm khoảng cách, giá, tiện ích |
| Public | Bản đồ phòng trọ (API) | GET | /api/renty/rooms/map | Trả về tọa độ địa lý và vị trí các phòng trọ phục vụ bản đồ |
| Public | Chi tiết phòng trọ | GET | /renty/room/{id} | Xem chi tiết phòng, giá theo tháng/ngày/giờ, bộ sưu tập ảnh thực tế, tiện ích minibar/khóa từ |
| Public | So sánh phòng trọ (Web) | POST | /api/renty/rooms/compare | So sánh đối chiếu trực tiếp tối đa 3 phòng theo giá, diện tích, an ninh |
| Public | Gửi đánh giá phòng trọ | POST | /renty/room/{id}/review | Khách gửi chấm điểm sao và bình luận thực tế về phòng trọ |
| Public | Báo cáo phòng sai phạm | POST | /renty/room/{id}/report | Báo cáo phòng lừa đảo, sai giá, hình ảnh ảo hoặc mất an ninh |
| Public | Trợ lý ảo Renty AI Chat | POST | /renty/chatbot/chat | Chatbot Gemini tư vấn phòng theo ngôn ngữ tự nhiên (cơ chế RAG) |
| Public | Gửi yêu cầu xem phòng | POST | /renty/contact-request | Khách để lại họ tên, SĐT đặt lịch hẹn xem phòng trực tiếp với chủ trọ |
| Public | Đăng ký tài khoản khách | POST | /api/auth/register | Đăng ký tài khoản người dùng mới vào hệ thống |
| Public | Đăng nhập hệ thống | POST | /login | Xác thực người dùng bằng username/SĐT và mật khẩu hoặc Passkey |
| Public | Đăng ký Onboarding Chủ trọ | GET/POST | /landlord/register | Cổng đăng ký tài khoản nhanh và khởi tạo cơ sở cho chủ trọ mới |
| Chủ trọ | Bảng điều khiển Overview | GET | /smartroom/admin | Thống kê số phòng, tỷ lệ lấp đầy, phòng nợ phí, biểu đồ doanh thu |
| Chủ trọ | AI Phân tích Dashboard | POST | /smartroom/admin/ai/dashboard-insight | Gemini AI phân tích doanh thu, rủi ro nợ và đề xuất giải pháp tối ưu |
| Chủ trọ | Trợ lý Quản lý SmartRoom | POST | /smartroom/admin/ai/assistant | Hỏi đáp tự nhiên với AI về tình trạng hợp đồng, phòng trống, hóa đơn |
| Chủ trọ | Danh sách cơ sở lưu trú | GET | /smartroom/admin/buildings | Quản lý danh sách các cơ sở lưu trú (nhà trọ, chung cư, khách sạn), tìm kiếm và lọc trạng thái |
| Chủ trọ | Form thêm cơ sở lưu trú | GET | /smartroom/admin/buildings/create | Giao diện nhập thông tin tòa nhà, số tầng, tải ảnh đại diện và chọn tiện ích chung |
| Chủ trọ | Lưu mới cơ sở lưu trú | POST | /smartroom/admin/buildings/store | Tiếp nhận dữ liệu, upload ảnh đại diện và lưu cấu hình tiện ích cơ sở lưu trú |
| Chủ trọ | Form sửa cơ sở lưu trú | GET | /smartroom/admin/buildings/{id}/edit | Giao diện chỉnh sửa thông tin tòa nhà, cập nhật số tầng, trạng thái và tiện ích |
| Chủ trọ | Cập nhật cơ sở lưu trú | POST | /smartroom/admin/buildings/{id}/update | Lưu cập nhật thông tin tòa nhà, thay đổi ảnh đại diện và trạng thái hoạt động |
| Chủ trọ | Xóa cơ sở lưu trú | DELETE | /smartroom/admin/buildings/{id}/delete | Xóa mềm cơ sở lưu trú (Guard Check: chặn xóa nếu tòa nhà còn phòng trực thuộc) |
| Chủ trọ | Danh sách phòng trọ | GET | /smartroom/admin/rooms | Quản lý danh sách phòng đa mô hình (Trọ, Căn hộ, Khách sạn) và sơ đồ ma trận phòng trực quan |
| Chủ trọ | Form thêm phòng trọ | GET | /smartroom/admin/rooms/create | Giao diện nhập thông tin phòng mới, diện tích, giá thuê, tiện ích |
| Chủ trọ | Lưu phòng trọ mới | POST | /smartroom/admin/rooms/store | Tiếp nhận dữ liệu tạo phòng, upload ảnh/video thực tế lên hệ thống |
| Chủ trọ | Form sửa phòng trọ | GET | /smartroom/admin/rooms/{id}/edit | Giao diện chỉnh sửa thông tin phòng, hạng phòng, hình thức thuê, tiền cọc và media |
| Chủ trọ | Cập nhật phòng trọ | POST | /smartroom/admin/rooms/{id}/update | Sửa đổi giá thuê, trạng thái phòng, cập nhật hình ảnh tiện ích |
| Chủ trọ | Xóa phòng trọ | DELETE | /smartroom/admin/rooms/{id}/delete | Xóa phòng khỏi cơ sở dữ liệu (chỉ áp dụng cho phòng đang trống) |
| Chủ trọ | AI Viết mô tả phòng | POST | /smartroom/admin/rooms/description/ai | AI tự động sinh văn bản mô tả phòng hấp dẫn dựa trên thông số nhập |
| Chủ trọ | Thêm mới cư dân | POST | /smartroom/admin/resident | Tiếp nhận thông tin cư dân thuê phòng, tự động chuyển phòng sang Đã thuê |
| Chủ trọ | Cập nhật thông tin cư dân | PUT | /smartroom/admin/resident/{id} | Cập nhật CCCD, số điện thoại, quê quán của cư dân đang ở |
| Chủ trọ | Trả phòng / Xóa cư dân | DELETE | /smartroom/admin/resident/{id} | Làm thủ tục trả phòng, tự động cập nhật trạng thái phòng về Còn trống |
| Chủ trọ | Xuất tờ khai CT01 tạm trú | GET | /smartroom/admin/resident/{id}/export-ct01 | Xuất file dữ liệu khai báo thay đổi thông tin cư trú Mẫu CT01 |
| Chủ trọ | Quản lý người ở cùng phòng | GET/POST | /smartroom/admin/resident/{id}/relatives | Quản lý danh sách thân nhân, bạn cùng phòng của cư dân chính |
| Chủ trọ | Chốt số Điện - Nước | POST | /smartroom/admin/utility | Nhập chỉ số điện nước cuối tháng, tự động tính tiền theo đơn giá |
| Chủ trọ | Chốt điện nước hàng loạt | POST | /smartroom/admin/utility/bulk | Lưu dữ liệu chỉ số công tơ của toàn bộ các phòng trong một thao tác |
| Chủ trọ | AI OCR Quét số công tơ | POST | /smartroom/admin/ai/ocr-meter | Nhận ảnh chụp mặt đồng hồ từ điện thoại, AI Vision đọc ra chỉ số |
| Chủ trọ | AI Quét công tơ hàng loạt | POST | /smartroom/admin/ai/ocr-meter-bulk | Tải lên cùng lúc nhiều ảnh công tơ, AI Gemini bóc tách Số SX và Chỉ số để tự động khớp và điền vào từng phòng |
| Chủ trọ | In Hóa đơn PDF & VietQR | GET | /smartroom/admin/utility/{id}/print | Xuất bản in phiếu thanh toán điện nước có nhúng mã VietQR động |
| Chủ trọ | Xác nhận đã đóng tiền | POST | /smartroom/admin/utility/{id}/pay | Chuyển trạng thái hóa đơn sang Đã thanh toán, ghi nhận dòng tiền |
| Chủ trọ | Nhắc nợ tự động qua Zalo/SMS | POST | /smartroom/admin/utility/auto-remind | Quét các phòng chưa nộp tiền và gửi tin nhắn Zalo kèm link quét VietQR |
| Chủ trọ | Tạo Hợp đồng thuê mới | POST | /smartroom/admin/contract | Lập hợp đồng thuê phòng, thiết lập tiền cọc, thời hạn và quy định |
| Chủ trọ | AI Soạn thảo điều khoản | POST | /smartroom/admin/ai/contract-terms | Trợ lý AI gợi ý các điều khoản pháp lý phù hợp với đặc thù nhà trọ |
| Chủ trọ | Gia hạn hợp đồng | POST | /smartroom/admin/contract/{id}/renew | Kéo dài thời hạn hợp đồng khi cư dân gửi yêu cầu gia hạn |
| Chủ trọ | Xóa / Hủy hợp đồng | DELETE | /smartroom/admin/contract/{id} | Hủy hợp đồng thuê khi hai bên thanh lý sớm |
| Chủ trọ | Chủ trọ ký số hợp đồng | POST | /smartroom/contract/{id}/lessor-sign | Lưu trữ chữ ký vẽ tay điện tử của bên cho thuê vào hợp đồng |
| Chủ trọ | Danh mục trang thiết bị | GET | /smartroom/admin/equipment | Xem thống kê danh mục thiết bị tài sản (điều hòa, nóng lạnh, quạt) |
| Chủ trọ | Bàn giao thiết bị vào phòng | POST | /smartroom/admin/equipment/allocate | Gán tài sản, trang thiết bị vào một phòng trọ cụ thể |
| Chủ trọ | Thu hồi thiết bị về kho | POST | /smartroom/admin/equipment/recover | Thu hồi thiết bị từ phòng về kho khi hỏng hóc hoặc trả phòng |
| Chủ trọ | Sổ thu chi phát sinh | GET | /smartroom/admin/reports | Quản lý các khoản thu chi ngoài tiền phòng (bảo trì, mua sắm vật tư) |
| Chủ trọ | Nộp hồ sơ xác minh KYC | POST | /smartroom/admin/verification/kyc | Tải ảnh 2 mặt CCCD và thông tin tài khoản ngân hàng gửi lên Admin |
| Chủ trọ | Nộp hồ sơ Tích xanh | POST | /smartroom/admin/verification/premium | Nộp giấy phép ĐKKD, giấy chứng nhận PCCC, ANTT để xin duyệt Tích xanh |
| Cư dân | Trang chủ Cổng cư dân | GET | /smartroom/resident | Xem phòng đang thuê, danh sách bạn cùng phòng, bảng hóa đơn |
| Cư dân | Xem mã VietQR thanh toán | GET | /smartroom/resident/bills/{id}/qr | Lấy mã QR chuyển khoản điền sẵn STK chủ trọ, số tiền và cú pháp |
| Cư dân | Gửi yêu cầu sửa chữa | POST | /smartroom/resident/tickets | Chụp ảnh, mô tả sự cố hỏng hóc thiết bị gửi đến ban quản lý |
| Cư dân | AI Phân tích sự cố ticket | POST | /smartroom/resident/tickets/analyze | AI phân loại sự cố (Điện/Nước), đánh giá độ khẩn và gợi ý xử lý |
| Cư dân | Gửi yêu cầu gia hạn HĐ | POST | /smartroom/resident/contract/{id}/request-renewal | Gửi phiếu xin kéo dài hợp đồng thuê khi sắp hết thời hạn |
| Cư dân | Giao diện ký HĐ online | GET | /smartroom/contract/{id}/sign | Xem toàn văn điều khoản hợp đồng và khung ký tên cảm ứng |
| Cư dân | Gửi mã OTP xác thực ký | POST | /smartroom/contract/{id}/send-otp | Gửi mã OTP về số điện thoại cư dân trước khi cho phép ký số |
| Cư dân | Cư dân xác nhận ký hợp đồng | POST | /smartroom/contract/{id}/sign | Lưu chữ ký Base64 và mã OTP xác nhận hợp đồng có hiệu lực |
| Cư dân | Tải file PDF Hợp đồng | GET | /smartroom/contract/{id}/pdf | Tải bản PDF hợp đồng có chữ ký số của cả hai bên để lưu trữ |
| Admin | Danh sách duyệt xác minh | GET | /admin/verifications | Xem danh sách các hồ sơ KYC và Premium đang chờ phê duyệt |
| Admin | Phê duyệt hồ sơ xác minh | POST | /admin/verifications/{id}/approve | Duyệt hồ sơ: thăng cấp quyền chủ trọ, cấp Tích Xanh, mở cổng VietQR |
| Admin | Từ chối hồ sơ xác minh | POST | /admin/verifications/{id}/reject | Từ chối hồ sơ kèm lý do phản hồi cho chủ trọ bổ sung lại |
| Admin | Xem tài liệu pháp lý bảo mật | GET | /admin/verification-documents/{id} | Lấy URL ký có thời hạn (TTL 5 phút) để xem ảnh CCCD/PCCC có watermark |
| Admin | Mở khóa tài liệu nhạy cảm | POST | /admin/verification-documents/{id}/unlock | Yêu cầu mở khóa xem hồ sơ sau duyệt kèm lý do nghiệp vụ và Passkey |
| Admin | Nhật ký kiểm toán Audit Log | GET | /admin/audit-logs | Xem lịch sử truy cập dữ liệu nhạy cảm bất biến (chống sửa xóa) |
| Admin | Nhật ký hoạt động Admin | GET | /smartroom/admin/activity-logs | Theo dõi toàn bộ lịch sử thao tác đăng nhập, tạo sửa xóa của hệ thống |
| Admin | Quản lý người dùng hệ thống | GET | /list | Xem danh sách toàn bộ tài khoản người dùng trên hệ thống |
| Admin | Phân quyền vai trò | POST | /users/role | Cập nhật vai trò quản trị (Admin, Landlord, Manager, Resident, Guest) |
| Admin | Khóa / Xóa tài khoản | DELETE | /delete/{id} | Vô hiệu hóa hoặc xóa người dùng vi phạm quy chế hoạt động |


# III. DATABASE VÀ MÔ HÌNH ERD


## 1. Mô hình ERD (Entity Relationship Diagram)

Hệ thống Renty & SmartRoom được thiết kế theo kiến trúc Đa chủ trọ (Multi-tenancy) với mô hình quan hệ chặt chẽ giữa các thực thể cốt lõi:

• Quan hệ 1 - Nhiều giữa Tenant và các thực thể dữ liệu: Một đơn vị kinh doanh phòng trọ (Tenant) quản lý nhiều Tòa nhà (Buildings), nhiều Phòng trọ (Rooms), nhiều Hợp đồng (Contracts), nhiều Bản ghi điện nước (UtilityRecords) và Danh mục tài sản (Equipment).

• Quan hệ giữa Tòa nhà (Buildings) và Phòng trọ (Rooms): Một tòa nhà bao gồm nhiều tầng và nhiều phòng trọ khác nhau (1-n). Mỗi phòng trọ thuộc về duy nhất một tòa nhà.

• Quan hệ giữa Phòng trọ (Rooms) và Cư dân (Residents): Một phòng trọ tại một thời điểm có thể có một Cư dân đại diện đứng tên hợp đồng và nhiều Cư dân ở ghép (ResidentRelatives) cùng sinh sống (1-n).

• Quan hệ giữa Phòng trọ và Hợp đồng thuê (Contracts): Một phòng trọ trải qua nhiều chu kỳ thuê theo thời gian, mỗi chu kỳ ứng với một Hợp đồng thuê độc lập liên kết giữa Phòng trọ và Cư dân (1-n).

• Quan hệ giữa Phòng trọ và Chỉ số Điện Nước (UtilityRecords): Mỗi tháng, phòng trọ phát sinh một bản ghi chỉ số tiêu thụ điện nước và hóa đơn dịch vụ tương ứng (1-n).

• Quan hệ giữa Phòng trọ và Trang thiết bị (RoomEquipment): Quan hệ Nhiều - Nhiều (n-n) giữa Phòng trọ (Rooms) và Danh mục tài sản (Equipment) thông qua bảng trung gian room_equipment để theo dõi số lượng và tình trạng hao mòn.

• Quan hệ Cộng đồng (Reviews, RoomReports, Tickets): Khách thuê viết đánh giá Reviews cho phòng trọ; Cư dân gửi Tickets phản ánh sự cố kỹ thuật; Khách vãng lai gửi RoomReports khiếu nại phòng sai phạm.

Hình 1: Sơ đồ mô hình thực thể quan hệ (ERD) hệ thống Quản lý Nhà trọ, Chung cư, Căn hộ dịch vụ & Khách sạn (Renty - SmartRoom)


## 2. Từ điển dữ liệu (Data Dictionary - 10 Thực thể cốt lõi)


### a. Bảng Users & Roles (Tài khoản và Vai trò)

Bảng users lưu trữ thông tin đăng nhập, xác thực WebAuthn Passkey và phân quyền. Trường phone được mã hóa AES-256-GCM kết hợp Blind Index.

Bảng 5: Mô tả cấu trúc bảng Users (Tài khoản người dùng)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| tenant_id | BIGINT UNSIGNED, NULL | Khóa ngoại tham chiếu bảng tenants(id) |
| role_id | BIGINT UNSIGNED, NULL | Khóa ngoại tham chiếu bảng roles(id) |
| name | VARCHAR(255) | Họ và tên người dùng |
| username | VARCHAR(100), UNIQUE | Tên đăng nhập hệ thống |
| phone | TEXT, NULL | Số điện thoại đăng nhập (Mã hóa AES-256-GCM) |
| phone_blind_index | VARCHAR(64), NULL | Chỉ mục mù HMAC-SHA256 phục vụ tra cứu số điện thoại |
| email | VARCHAR(255), NULL | Địa chỉ thư điện tử người dùng |
| password | VARCHAR(255) | Mật khẩu đã được băm (Bcrypt hash) |
| role | VARCHAR(50) | Tên vai trò: admin, landlord, unverified_landlord, manager, resident, guest |
| created_at | TIMESTAMP, NULL | Thời điểm tạo tài khoản |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |

Bảng 6: Mô tả cấu trúc bảng Roles (Vai trò và phân quyền)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| name | VARCHAR(100) | Tên hiển thị vai trò (Ví dụ: Chủ trọ, Quản lý, Cư dân) |
| slug | VARCHAR(50), UNIQUE | Mã định danh vai trò: admin, landlord, manager, resident, guest |
| description | VARCHAR(255), NULL | Mô tả phạm vi quyền hạn của vai trò |
| created_at | TIMESTAMP, NULL | Thời điểm tạo vai trò |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |


### b. Bảng Properties / Buildings (Cơ sở lưu trú: Nhà trọ, Chung cư, Tòa nhà, Khách sạn)

Bảng properties (buildings) đại diện cho các cơ sở bất động sản lưu trú trực thuộc quyền quản lý của một Tenant. Hỗ trợ phân loại loại hình cơ sở kinh doanh (property_type: nhà trọ truyền thống, chung cư / căn hộ mini, tòa nhà căn hộ dịch vụ, hoặc khách sạn/homestay), quản lý số tầng, tổng số căn hộ/phòng, cấu hình phí quản lý chung cư (management_fee_rate), quy chuẩn giờ nhận/trả phòng khách sạn (check-in/check-out) và biểu giá điện nước.

Bảng 7: Mô tả cấu trúc bảng Properties / Buildings (Cơ sở lưu trú: Nhà trọ, Chung cư, Khách sạn)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| tenant_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng tenants(id) |
| name | VARCHAR(255) | Tên cơ sở lưu trú (Ví dụ: Dãy trọ A, Khách sạn Renty Star, Căn hộ dịch vụ Landmark) |
| address | VARCHAR(255) | Địa chỉ cụ thể của tòa nhà |
| total_floors | INT UNSIGNED, DEFAULT 1 | Tổng số tầng của cơ sở lưu trú |
| description | TEXT, NULL | Loại hình cơ sở (property_type: boarding, apartment, hotel), giờ check-in/out, tiện ích chung |
| phone | VARCHAR(50), NULL | Số điện thoại hotline / liên hệ quản lý cơ sở lưu trú |
| status | VARCHAR(30), DEFAULT 'active' | Trạng thái hoạt động: active (hoạt động), maintenance (bảo trì), inactive (tạm ngưng) |
| image | VARCHAR(255), NULL | Đường dẫn ảnh đại diện tòa nhà / cơ sở lưu trú |
| amenities | JSON, NULL | Mảng JSON lưu các tiện ích chung: thang máy, camera, bảo vệ 24/7, hầm để xe, PCCC... |
| created_at | TIMESTAMP, NULL | Thời điểm tạo bản ghi |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |
| deleted_at | TIMESTAMP, NULL | Thời điểm xóa mềm cơ sở lưu trú (phục vụ SoftDeletes) |


### c. Bảng Rooms & Condos (Phòng trọ, Căn hộ chung cư, Phòng khách sạn & Minibar)

Quản lý chi tiết từng căn phòng trọ hoặc căn hộ chung cư: số phòng/mã căn (P.101, Căn 12A.03), phân loại phòng (Studio, 1PN, 2PN, 3PN, Deluxe, VIP), hình thức thuê linh hoạt (theo tháng cho trọ/chung cư, theo ngày hoặc theo giờ cho khách sạn), đa khung giá (giá tháng, giá đêm, giá giờ), trạng thái phòng (trống, đang ở, đang dọn dẹp vệ sinh - Housekeeping, bảo trì), danh mục tiện ích (WC, ban công, thang máy, thẻ từ) và danh mục tài sản/minibar bàn giao.

Bảng 8: Mô tả cấu trúc bảng Rooms & Condos (Phòng lưu trú, Căn hộ chung cư & Minibar)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| tenant_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng tenants(id) |
| building_id | BIGINT UNSIGNED, NULL | Khóa ngoại tham chiếu bảng buildings(id) |
| room_number | VARCHAR(50) | Mã hoặc số phòng (Ví dụ: P.101, Phòng Deluxe 202, VIP Suite) |
| floor | INT | Tầng mà phòng trọ đang tọa lạc |
| price | DECIMAL(12,2) | Giá thuê phòng (Theo tháng với trọ/căn hộ, hoặc theo ngày/giờ với khách sạn) |
| area | INT | Diện tích sử dụng của phòng (m2) |
| electric_meter_serial | VARCHAR(100), NULL | Số sản xuất (Số SX) dập trên mặt công tơ điện (hỗ trợ AI Vision Bulk OCR quét và khớp phòng tự động) |
| water_meter_serial | VARCHAR(100), NULL | Số sản xuất (Số SX) dập trên mặt đồng hồ nước (hỗ trợ AI Vision Bulk OCR quét và khớp phòng tự động) |
| status | ENUM | Trạng thái phòng: Trống (empty), Đang ở (occupied), Nợ cước (overdue), Bảo trì (maintenance) |
| amenities | JSON, NULL | Mảng JSON lưu các tiện ích: WC khép kín, ban công, gác lửng, thú cưng |
| image | VARCHAR(255), NULL | Đường dẫn ảnh đại diện phòng |
| images | JSON, NULL | Mảng JSON danh sách ảnh thực tế các góc chụp trong phòng |
| video | VARCHAR(255), NULL | Đường dẫn video thực tế không gian phòng |
| room_type | VARCHAR(50), DEFAULT 'standard' | Hạng phòng chuẩn hóa: standard (tiêu chuẩn), deluxe, vip, studio |
| rental_type | VARCHAR(20), DEFAULT 'month' | Hình thức cho thuê linh hoạt: month (theo tháng), day (theo ngày), hour (theo giờ) |
| deposit | INT UNSIGNED, DEFAULT 0 | Tiền đặt cọc giữ phòng (VNĐ) |
| description | TEXT, NULL | Văn bản mô tả đặc điểm, quy định và tiện nghi của phòng |
| version | INT UNSIGNED, DEFAULT 1 | Phiên bản phục vụ Optimistic Locking (chống xung đột ghi đè khi đặt phòng) |
| created_at | TIMESTAMP, NULL | Thời điểm tạo phòng |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |

Bảng 9: Mô tả cấu trúc bảng Equipment (Danh mục tài sản - Trang thiết bị)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| tenant_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng tenants(id) |
| name | VARCHAR(255) | Tên trang thiết bị (Điều hòa, Bình nóng lạnh, Tủ lạnh, Quạt trần) |
| code | VARCHAR(50), UNIQUE | Mã quản lý thiết bị trong kho |
| quantity | INT, DEFAULT 0 | Tổng số lượng tồn trong kho |
| price | DECIMAL(12,2), NULL | Giá trị tài sản ước tính (VNĐ) |
| created_at | TIMESTAMP, NULL | Thời điểm tạo thiết bị |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |

Bảng 10: Mô tả cấu trúc bảng RoomEquipment (Phân bổ trang thiết bị trong phòng)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| room_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng rooms(id) |
| equipment_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng equipment(id) |
| quantity | INT, DEFAULT 1 | Số lượng thiết bị bàn giao trong phòng |
| condition | VARCHAR(100) | Tình trạng thiết bị: Mới 100%, Hoạt động tốt, Cần bảo trì |
| assigned_date | DATE, NULL | Ngày bàn giao thiết bị vào phòng |
| created_at | TIMESTAMP, NULL | Thời điểm tạo bản ghi |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |


### d. Bảng Residents & Guests (Cư dân thuê trọ & Khách lưu trú khách sạn)

Quản lý thông tin nhân thân của cư dân thuê trọ dài hạn và khách lưu trú khách sạn/homestay ngắn hạn. Phân loại đối tượng (resident, hotel_guest), quản lý người đi cùng / ở ghép, phục vụ xuất biểu mẫu đăng ký tạm trú CT01 và khai báo lưu trú du lịch. Toàn bộ thông tin CCCD, hộ chiếu và SĐT được mã hóa chuẩn ứng dụng AES-256-GCM.

Bảng 11: Mô tả cấu trúc bảng Residents & Guests (Cư dân thuê trọ & Khách lưu trú)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| tenant_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng tenants(id) |
| room_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng rooms(id) |
| user_id | BIGINT UNSIGNED, NULL | Khóa ngoại liên kết tài khoản bảng users(id) |
| name | VARCHAR(255) | Họ và tên cư dân đại diện thuê phòng |
| phone | TEXT, NULL | Số điện thoại liên lạc (Mã hóa AES-256-GCM) |
| phone_blind_index | VARCHAR(64), NULL | Chỉ mục tra cứu số điện thoại |
| cccd | TEXT, NULL | Số Căn cước công dân (Mã hóa AES-256-GCM) |
| cccd_blind_index | VARCHAR(64), NULL | Chỉ mục tra cứu số CCCD |
| dob | DATE, NULL | Ngày tháng năm sinh của cư dân |
| gender | VARCHAR(10), NULL | Giới tính (Nam / Nữ / Khác) |
| hometown | VARCHAR(255), NULL | Quê quán / Nơi đăng ký thường trú |
| start_date | DATE | Ngày bắt đầu dọn vào ở |
| created_at | TIMESTAMP, NULL | Thời điểm thêm cư dân |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |

Bảng 12: Mô tả cấu trúc bảng ResidentRelatives (Thân nhân & Người ở cùng phòng)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| resident_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng residents(id) |
| name | VARCHAR(255) | Họ và tên người ở cùng phòng |
| phone | TEXT, NULL | Số điện thoại người ở cùng (Mã hóa AES-256-GCM) |
| cccd | TEXT, NULL | Số CCCD người ở cùng (Mã hóa AES-256-GCM) |
| relationship | VARCHAR(100) | Mối quan hệ với cư dân chính (Bạn bè, Vợ/Chồng, Anh em) |
| dob | DATE, NULL | Ngày sinh người ở cùng |
| created_at | TIMESTAMP, NULL | Thời điểm thêm bản ghi |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |


### e. Bảng Contracts & Bookings (Hợp đồng thuê dài hạn & Đặt phòng khách sạn)

Lưu trữ thông tin giao dịch lưu trú: Hợp đồng thuê trọ dài hạn có tiền cọc, chu kỳ thu và chuỗi Base64 chữ ký vẽ tay điện tử của hai bên; hoặc Phiếu đặt phòng khách sạn (Booking) với mã đặt phòng, thời điểm check-in/check-out chi tiết theo giờ, tổng cước phòng và trạng thái thanh toán.

Bảng 13: Mô tả cấu trúc bảng Contracts & Bookings (Hợp đồng thuê & Đặt phòng)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| tenant_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng tenants(id) |
| room_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng rooms(id) |
| resident_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng residents(id) |
| contract_code | VARCHAR(50), UNIQUE | Mã số hợp đồng duy nhất (Ví dụ: HD-2026-P101) |
| deposit | DECIMAL(12,2) | Số tiền đặt cọc giữ phòng (VNĐ) |
| start_date | DATE | Ngày bắt đầu có hiệu lực của hợp đồng |
| end_date | DATE | Ngày kết thúc / hết hạn hợp đồng |
| terms | TEXT, NULL | Nội dung các điều khoản thỏa thuận (AI hỗ trợ soạn) |
| signature | LONGTEXT, NULL | Ảnh chữ ký số vẽ tay của Bên thuê (Dạng chuỗi Base64) |
| lessor_signature | LONGTEXT, NULL | Ảnh chữ ký số vẽ tay của Bên cho thuê (Chuỗi Base64) |
| signed_at | TIMESTAMP, NULL | Thời điểm hoàn tất việc ký kết |
| status | ENUM | Trạng thái hợp đồng: draft (Chờ ký), active (Đang hiệu lực), expired, terminated |
| created_at | TIMESTAMP, NULL | Thời điểm khởi tạo hợp đồng |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |


### f. Bảng Utility & Services (Chốt Điện - Nước & Dịch vụ Khách sạn)

Ghi nhận chỉ số tiêu thụ điện nước hàng tháng bằng AI OCR (cho trọ/căn hộ) và theo dõi các chi phí dịch vụ buồng phòng, tiêu thụ đồ uống/đồ ăn vặt minibar, giặt ủi và cước phòng theo giờ/ngày (cho khách sạn).

Bảng 14: Mô tả cấu trúc bảng Utility & Services (Chốt Điện - Nước & Dịch vụ Khách sạn)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| tenant_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng tenants(id) |
| room_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng rooms(id) |
| billing_month | VARCHAR(7) | Tháng tính tiền hóa đơn (Định dạng: YYYY-MM, ví dụ: 2026-06) |
| old_electricity | INT | Chỉ số điện cũ tháng trước (kWh) |
| new_electricity | INT | Chỉ số điện mới chốt kỳ này (kWh) |
| old_water | INT | Chỉ số nước cũ tháng trước (m3) |
| new_water | INT | Chỉ số nước mới chốt kỳ này (m3) |
| electricity_price | DECIMAL(10,2) | Đơn giá tiền điện áp dụng (VNĐ/kWh) |
| water_price | DECIMAL(10,2) | Đơn giá tiền nước áp dụng (VNĐ/m3) |
| status | ENUM | Trạng thái thanh toán: draft (Bản nháp), sent (Đã gửi nhắc), paid (Đã nộp) |
| payment_method | VARCHAR(50), NULL | Hình thức thanh toán: vietqr, cash, bank_transfer |
| paid_at | TIMESTAMP, NULL | Thời điểm xác nhận thanh toán thành công |
| created_at | TIMESTAMP, NULL | Thời điểm chốt số |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |


### g. Bảng Bills & Transactions (Hóa đơn thu tiền, Bảng kê Folio & Sổ quỹ)

Quản lý toàn bộ hóa đơn tiền phòng định kỳ hàng tháng của nhà trọ và bảng kê thanh toán trả phòng (Hotel Folio) của khách sạn. Tự động sinh mã VietQR thanh toán chuẩn NAPAS247 và ghi nhận dòng tiền đối soát sổ quỹ thu chi.

Bảng 15: Mô tả cấu trúc bảng Bills (Hóa đơn thu tiền lưu trú)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| tenant_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng tenants(id) |
| room_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng rooms(id) |
| code | VARCHAR(50), UNIQUE | Mã hóa đơn duy nhất (Ví dụ: BILL-202606-P101) |
| amount | DECIMAL(12,2) | Tổng số tiền cần phải thanh toán (VNĐ) |
| due_date | DATE | Hạn chót phải hoàn thành thanh toán tiền trọ |
| status | ENUM | Trạng thái: pending (Chờ đóng), paid (Đã nộp), overdue (Trễ hạn) |
| created_at | TIMESTAMP, NULL | Thời điểm xuất hóa đơn |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |

Bảng 16: Mô tả cấu trúc bảng CashFlows / Transactions (Sổ quỹ thu - chi và dòng tiền)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| tenant_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng tenants(id) |
| type | ENUM | Loại giao dịch dòng tiền: income (Khoản thu), expense (Khoản chi) |
| category | VARCHAR(100) | Khoản mục: Sửa chữa điện nước, Mua vật tư, Tiền phòng, Tiếp khách |
| amount | DECIMAL(12,2) | Số tiền giao dịch thực tế (VNĐ) |
| description | TEXT, NULL | Ghi chú giải trình lý do phát sinh khoản chi/thu |
| created_at | TIMESTAMP, NULL | Thời điểm ghi sổ kế toán |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |


### h. Bảng Tickets & RoomReports (Sự cố kỹ thuật, Dịch vụ phòng & Khiếu nại)

Tiếp nhận và xử lý yêu cầu báo hỏng thiết bị từ cư dân trọ, đồng thời tiếp nhận các yêu cầu dịch vụ phòng (Housekeeping, dọn phòng, tiếp nước, đổi khăn) từ khách lưu trú khách sạn có AI phân loại mức độ khẩn cấp.

Bảng 17: Mô tả cấu trúc bảng Tickets (Sự cố kỹ thuật & Dịch vụ buồng phòng)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| tenant_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng tenants(id) |
| room_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng rooms(id) |
| resident_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng residents(id) |
| title | VARCHAR(255) | Tiêu đề ngắn gọn phản ánh sự cố |
| description | TEXT | Mô tả chi tiết tình trạng hư hỏng thiết bị |
| category | ENUM | Danh mục phân loại: electric, water, furniture, maintenance, other |
| priority | ENUM | Mức độ khẩn cấp (AI phân tích): low, medium, high |
| suggestion | TEXT, NULL | Gợi ý biện pháp khắc phục nhanh từ AI |
| status | ENUM | Trạng thái xử lý: pending (Tiếp nhận), in_progress (Đang sửa), resolved (Đã xong) |
| created_at | TIMESTAMP, NULL | Thời điểm gửi phiếu sự cố |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |

Bảng 18: Mô tả cấu trúc bảng RoomReports (Báo cáo phòng vi phạm / lừa đảo)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| room_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng rooms(id) |
| reporter_name | VARCHAR(255), NULL | Họ tên người gửi báo cáo sai phạm |
| reporter_phone | VARCHAR(50), NULL | Số điện thoại người gửi báo cáo |
| reason | ENUM | Lý do: scam (Lừa cọc), fake_images (Ảnh ảo), wrong_price, unsafe, other |
| description | TEXT | Nội dung phản ánh bằng chứng sai phạm của phòng trọ |
| status | ENUM | Trạng thái kiểm duyệt: pending, reviewed, resolved, rejected |
| created_at | TIMESTAMP, NULL | Thời điểm gửi báo cáo |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |


### i. Bảng LandlordProfiles & VerificationRequests (Hồ sơ & Yêu cầu duyệt chủ trọ)

Phục vụ luồng xác minh chủ trọ lũy tiến (Progressive Verification) và lưu trữ chứng chỉ PCCC, ANTT, ĐKKD.

Bảng 19: Mô tả cấu trúc bảng LandlordProfiles & VerificationRequests (Hồ sơ duyệt chủ trọ)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| tenant_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng tenants(id) |
| landlord_name | VARCHAR(255) | Họ tên chủ cơ sở kinh doanh nhà trọ |
| type | ENUM | Loại xác thực: kyc (Căn cước & Ngân hàng), premium (PCCC & ĐKKD) |
| cccd_number | TEXT, NULL | Số CCCD chủ trọ (Mã hóa AES-256-GCM) |
| business_license_no | VARCHAR(100), NULL | Số giấy phép đăng ký kinh doanh |
| status | ENUM | Trạng thái duyệt: pending (Chờ duyệt), approved (Đã duyệt), rejected |
| rejection_reason | TEXT, NULL | Lý do từ chối phản hồi cho chủ trọ |
| reviewed_by | BIGINT UNSIGNED, NULL | Khóa ngoại ID Admin đã duyệt hồ sơ |
| reviewed_at | TIMESTAMP, NULL | Thời điểm phê duyệt hoặc từ chối |
| created_at | TIMESTAMP, NULL | Thời điểm gửi hồ sơ |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |


### j. Bảng AdminActivityLogs & AuditLogs (Nhật ký truy vết & Kiểm toán bất biến)

Bảo đảm tuân thủ Nghị định 13: Ghi nhận mọi thao tác truy cập dữ liệu cá nhân nhạy cảm, chống sửa xóa bằng trigger CSDL.

Bảng 20: Mô tả cấu trúc bảng AdminActivityLogs & AuditLogs (Nhật ký kiểm toán bất biến)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| admin_user_id | BIGINT UNSIGNED, NULL | Khóa ngoại ID tài khoản Admin thực hiện thao tác |
| target_model | VARCHAR(100) | Tên bảng/thực thể bị truy cập (Ví dụ: Resident, CCCD, BankAccount) |
| target_id | BIGINT UNSIGNED, NULL | ID bản ghi cụ thể bị xem hoặc thay đổi |
| action | VARCHAR(50) | Thao tác: reveal_sensitive, unlock_document, approve_kyc |
| reason | TEXT | Lý do nghiệp vụ bắt buộc phải nhập khi xem dữ liệu nhạy cảm |
| ip_address | VARCHAR(45), NULL | Địa chỉ IP của thiết bị truy cập |
| user_agent | TEXT, NULL | Trình duyệt và thiết bị của người thao tác |
| prev_hash | VARCHAR(64), NULL | Mã băm SHA-256 của bản ghi liền trước (Tạo chuỗi băm Blockchain) |
| row_hash | VARCHAR(64) | Mã băm SHA-256 xác thực tính toàn vẹn của bản ghi hiện tại |
| created_at | TIMESTAMP | Thời điểm phát sinh hành vi kiểm toán |


# IV. ĐẶC TẢ KỸ THUẬT HỆ THỐNG VÀ KỊCH BẢN XỬ LÝ LỖI (TECHNICAL SPECIFICATIONS & UI/UX)

Để đảm bảo nguyên tắc **"10 người đọc cả 10 người code đều giống nhau"** và **"một lập trình viên khi đọc vào spec phải code được ngay mà không cần suy đoán"**, toàn bộ các chức năng của hệ thống được đặc tả nghiêm ngặt theo chuẩn công nghiệp với cấu trúc 5 thành phần bắt buộc cho mỗi chức năng:
1. **Input Specification**: Bảng quy tắc xác thực dữ liệu đầu vào (Validation Rules, kiểu dữ liệu, thông báo lỗi cụ thể khi fail).
2. **Business Logic Flow**: Thuật toán xử lý tuần tự từng bước (kiểm tra Multi-tenancy, Guard check, DB Transaction, Event).
3. **Database Operation**: Chi tiết các bảng, cột bị tác động, cơ chế khóa lạc quan (Optimistic Locking) và toàn vẹn dữ liệu.
4. **Output Specification**: Hợp đồng dữ liệu đầu ra (Response JSON thành công / thất bại hoặc View Redirect kèm Flash Toast).
5. **UI/UX Specification & Kịch bản lỗi**: Giao diện, Class CSS Tailwind, Element IDs, và bảng xử lý chi tiết mọi trường hợp ngoại lệ.

Đồng thời, tuân thủ nguyên tắc cốt lõi: **"Trong báo cáo có gì thì trong code phải có cái đó và ngược lại"**, mọi chức năng đều được ánh xạ trực tiếp từ các nhánh Git, Controller, Model và Migration thực tế trong kho mã nguồn dự án.


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


## B. CÁC MODULE & ĐẶC TẢ KỸ THUẬT DO NGUYỄN ANH QUÝ (NHÓM PHÓ) PHỤ TRÁCH

---

### [FEAT-AQ-01] CRUD Quản lý Cơ sở lưu trú (Properties/Hotels) & Guard Check an toàn
- **Git Branch**: `AnhQuy/quan-ly-co-so-luu-tru` (`AnhQuy-quan-ly-co-so-luu-tru`)
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\BuildingController@index`, `create`, `store`, `edit`, `update`, `destroy`
- **Endpoint**:
  - `GET /smartroom/admin/buildings` (`admin.buildings.index`)
  - `GET /smartroom/admin/buildings/create` (`admin.buildings.create`)
  - `POST /smartroom/admin/buildings/store` (`admin.buildings.store`)
  - `GET /smartroom/admin/buildings/{id}/edit` (`admin.buildings.edit`)
  - `POST /smartroom/admin/buildings/{id}/update` (`admin.buildings.update`)
  - `DELETE /smartroom/admin/buildings/{id}/delete` (`admin.buildings.destroy`)
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation POST /store & POST /{id}/update)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `name` | String | Có | `required\|string\|max:255` | "Tên cơ sở lưu trú không được để trống." |
| `address` | String | Có | `required\|string\|max:255` | "Địa chỉ cơ sở lưu trú không được để trống." |
| `total_floors` | Integer | Có | `required\|integer\|min:1\|max:100` | "Số tầng tối thiểu là 1 và tối đa là 100." |
| `phone` | String | Không | `nullable\|regex:/^(0[3\|5\|7\|8\|9])[0-9]{8}$/` | "Số điện thoại hotline cơ sở không hợp lệ." |
| `status` | String | Có | `required\|in:active,maintenance,inactive` | "Trạng thái hoạt động không hợp lệ." |
| `image` | File | Không | `nullable\|image\|mimes:jpeg,png,jpg,webp\|max:5120` | "File tải lên phải là hình ảnh (jpg, png, webp) và dung lượng không quá 5MB." |
| `amenities` | Array | Không | `nullable\|array` | "Danh sách tiện ích chung không hợp lệ." |
| `property_type`| String | Không | `nullable\|in:boarding,apartment,hotel` | "Loại hình cơ sở lưu trú không hợp lệ." |
| `checkin_time` | String | Không | `nullable\|date_format:H:i` | "Giờ check-in tiêu chuẩn không đúng định dạng HH:mm." |
| `checkout_time`| String | Không | `nullable\|date_format:H:i` | "Giờ check-out tiêu chuẩn không đúng định dạng HH:mm." |

#### 2. Business Logic Flow
1. **Kiểm tra quyền Multi-tenancy**: Mọi thao tác truy xuất hoặc cập nhật phải đảm bảo `building->tenant_id === auth()->user()->tenant_id`. Nếu không khớp, trả về HTTP 403.
2. **Logic Thêm mới (Store) & Cập nhật (Update)**:
   - Xử lý upload file ảnh: Nếu có file `image`, lưu trữ vào `storage/app/public/buildings/` với tên file tạo ngẫu nhiên UUID kèm đuôi tệp gốc; cập nhật đường dẫn vào cột `image`.
   - Tiện ích chung `amenities` được encode thành JSON array.
   - Kiểm tra logic giờ giấc (nếu là khách sạn): Nếu có cả `checkin_time` và `checkout_time`, kiểm tra `checkin_time > checkout_time` (check-in sau check-out).
3. **Cơ chế Guard Check an toàn khi Xóa (Destroy)**:
   - Đếm số lượng phòng trực thuộc: `$roomCount = Room::where('building_id', $id)->count()`.
   - **Guard Check**: Nếu `$roomCount > 0`, **CHẶN TUYỆT ĐỐI** hành vi xóa; trả về phản hồi lỗi kèm số lượng phòng còn tồn tại.
   - Nếu `$roomCount === 0`: Thực thi SoftDelete (`building->delete()`).
4. **Phản hồi**: Redirect về danh sách kèm Flash Session Toast.

#### 3. Database Operation
- **Bảng tác động**: `buildings` (hoặc `properties`).
- **Cột**: `tenant_id`, `name`, `address`, `total_floors`, `phone`, `status`, `image`, `amenities`, `deleted_at`.
- **Cơ chế**: SoftDeletes (`deleted_at` timestamp).

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về `admin.buildings.index` kèm `session('success', 'Lưu thông tin cơ sở lưu trú thành công!')`.
- **Thất bại khi Xóa (HTTP 422)**: Redirect back kèm `session('error', 'Không thể xóa cơ sở vì vẫn còn 8 phòng trực thuộc. Vui lòng chuyển hoặc xóa các phòng trước!')`.

#### 5. UI/UX Specification & Xử lý lỗi
- **Form element**: `#building-form`, input `#building-name`, `#total-floors`, vùng upload `#building-image-dropzone`.
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống tên cơ sở lưu trú khi tạo mới | Ô tên cơ sở bôi đỏ `border-red-500`: "Tên cơ sở lưu trú không được để trống". |
| Số tầng nhập không hợp lệ (nhỏ hơn 1 hoặc lớn hơn 100) | Ô số tầng bôi đỏ: "Số tầng tối thiểu là 1 và tối đa 100". |
| Tải lên ảnh sai định dạng hoặc vượt quá 5MB | Vùng upload bôi đỏ: "File tải lên phải là hình ảnh hợp lệ (jpg, png, webp) và dung lượng không quá 5MB". |
| Xóa cơ sở lưu trú khi vẫn còn phòng trực thuộc (Guard Check) | Modal cảnh báo chặn thao tác: "Không thể xóa cơ sở vì vẫn còn X phòng trọ trực thuộc. Vui lòng chuyển hoặc xóa các phòng trước". |

---

### [FEAT-AQ-02] CRUD Quản lý Phòng lưu trú (Rooms - đa mô hình) & Cấu hình Serial công tơ
- **Git Branch**: `AnhQuy/quan-ly-phong`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\RoomController@index`, `create`, `store`, `edit`, `update`, `destroy`
- **Endpoint**:
  - `GET /smartroom/admin/rooms` (`admin.rooms.index`)
  - `GET /smartroom/admin/rooms/create` (`admin.rooms.create`)
  - `POST /smartroom/admin/rooms/store` (`admin.rooms.store`)
  - `GET /smartroom/admin/rooms/{id}/edit` (`admin.rooms.edit`)
  - `POST /smartroom/admin/rooms/{id}/update` (`admin.rooms.update`)
  - `DELETE /smartroom/admin/rooms/{id}/delete` (`admin.rooms.destroy`)
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation POST /store & POST /{id}/update)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `building_id` | Integer | Có | `required\|integer\|exists:buildings,id` | "Vui lòng chọn cơ sở lưu trú hợp lệ." |
| `room_number` | String | Có | `required\|string\|max:50` | "Số phòng không được để trống." |
| `floor` | Integer | Có | `required\|integer\|min:0\|max:100` | "Tầng phòng tọa lạc phải từ 0 (tầng trệt) đến 100." |
| `price` | Numeric | Có | `required\|numeric\|min:0` | "Giá thuê phòng phải là số dương lớn hơn hoặc bằng 0." |
| `area` | Integer | Có | `required\|integer\|min:5\|max:500` | "Diện tích phòng phải từ 5m² đến 500m²." |
| `deposit` | Numeric | Có | `required\|numeric\|min:0` | "Tiền đặt cọc giữ phòng không được là số âm." |
| `room_type` | String | Có | `required\|in:standard,deluxe,vip,studio` | "Hạng phòng phải là standard, deluxe, vip hoặc studio." |
| `rental_type`| String | Có | `required\|in:month,day,hour` | "Hình thức cho thuê phải là theo tháng, theo ngày hoặc theo giờ." |
| `status` | String | Có | `required\|in:empty,occupied,overdue,maintenance` | "Trạng thái phòng không hợp lệ." |
| `electric_meter_serial`| String | Không | `nullable\|string\|max:100` | "Số SX công tơ điện tối đa 100 ký tự." |
| `water_meter_serial` | String | Không | `nullable\|string\|max:100` | "Số SX đồng hồ nước tối đa 100 ký tự." |
| `amenities` | Array | Không | `nullable\|array` | "Danh sách tiện ích phòng không hợp lệ." |
| `image` | File | Không | `nullable\|image\|mimes:jpeg,png,jpg,webp\|max:5120` | "Ảnh đại diện phòng tối đa 5MB." |
| `images` | Array | Không | `nullable\|array\|max:10` | "Chỉ được tải lên tối đa 10 ảnh thực tế của phòng." |
| `images.*` | File | Không | `image\|mimes:jpeg,png,jpg,webp\|max:5120` | "Mỗi ảnh thực tế tối đa 5MB." |
| `video` | File | Không | `nullable\|file\|mimes:mp4,mov,webm\|max:30720` | "Video không gian phòng phải có định dạng MP4/MOV/WebM và dung lượng tối đa 30MB." |
| `version` | Integer | Không | `nullable\|integer` | "Mã phiên bản đồng bộ không hợp lệ." |

#### 2. Business Logic Flow
1. **Kiểm tra trùng lặp số phòng trong cùng tòa nhà**:
   Query kiểm tra: `Room::where('building_id', $buildingId)->where('room_number', $roomNumber)->where('id', '!=', $currentId)->exists()`. Nếu trùng lặp, trả về HTTP 422: *"Số phòng này đã tồn tại trong tòa nhà"*.
2. **Khóa lạc quan (Optimistic Locking)**:
   Khi `update`: So sánh giá trị `version` gửi lên với `room->version` trong CSDL.
   - Nếu `version_request != room->version`: Ném ngoại lệ xung đột dữ liệu (HTTP 409 Conflict): *"Dữ liệu phòng vừa được cập nhật bởi một người dùng khác. Vui lòng tải lại trang"*.
   - Nếu khớp: Thực hiện cập nhật và tăng `version = version + 1`.
3. **Cấu hình Serial công tơ**: Lưu trữ `electric_meter_serial` và `water_meter_serial` phục vụ thuật toán AI Vision Bulk Match.
4. **Xử lý tệp tin Media**:
   - Lưu ảnh đại diện và mảng ảnh thực tế vào `storage/app/public/rooms/photos/`.
   - Lưu video vào `storage/app/public/rooms/videos/`.
5. **Guard Check khi Xóa phòng**: Nếu phòng có `status == 'occupied'` hoặc còn cư dân đang ở, cấm xóa (HTTP 422).

#### 3. Database Operation
- **Bảng tác động**: `rooms`.
- **Cột**: `tenant_id`, `building_id`, `room_number`, `floor`, `price`, `area`, `deposit`, `room_type`, `rental_type`, `status`, `electric_meter_serial`, `water_meter_serial`, `amenities`, `image`, `images`, `video`, `version`.

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về `admin.rooms.index` kèm `session('success', 'Lưu thông tin phòng thành công!')`.
- **Thất bại (HTTP 422 / 409)**: Redirect back với input và thông báo lỗi tương ứng.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống Số phòng, Diện tích hoặc Giá thuê | Các ô input bôi đỏ `border-red-500`: "Số phòng không được để trống", "Vui lòng nhập giá phòng hợp lệ". |
| Số phòng bị trùng lặp trong cùng tòa nhà | Ô số phòng bôi đỏ: "Số phòng P.101 đã tồn tại trong tòa nhà này. Vui lòng chọn số phòng khác". |
| Tải file ảnh/video vượt quá dung lượng (Ảnh > 5MB, Video > 30MB) | Vùng upload bôi đỏ: "Dung lượng tệp vượt quá kích thước cho phép. Vui lòng nén file hoặc chọn tệp nhỏ hơn". |
| Xóa phòng đang có người ở (status = occupied) | Modal cảnh báo đỏ: "Không thể xóa phòng đang có người ở! Bạn phải làm thủ tục trả phòng cho cư dân trước khi xóa". |

---

### [FEAT-AQ-03] Sơ đồ Ma trận phòng trực quan theo tầng (Visual Room Matrix) & Real-time Housekeeping
- **Git Branch**: `AnhQuy/ma-tran-phong`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\RoomMatrixRealtimeController@updateStatus`, `stream`, `poll`, `App\Http\Controllers\HousekeepingController@index`, `updateStatus`
- **Endpoint**:
  - `POST /smartroom/admin/rooms/{id}/quick-status` (`admin.rooms.quick_status`)
  - `GET /smartroom/admin/rooms/matrix/stream` (SSE Server-Sent Events)
  - `GET /smartroom/admin/rooms/matrix/poll` (Long-polling fallback)
  - `GET /smartroom/housekeeping` (`admin.housekeeping.index`)
  - `POST /smartroom/housekeeping/{roomId}/status` (`admin.housekeeping.update`)
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Request Validation POST /quick-status)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `status` | String | Có | `required\|in:empty,occupied,cleaning,overdue,maintenance` | "Trạng thái phòng cập nhật không hợp lệ." |

#### 2. Business Logic Flow
1. **Kiểm tra logic chuyển trạng thái**:
   - Không cho phép chuyển thủ công sang `occupied` nếu phòng chưa có Hợp đồng hoặc Phiếu đặt phòng có hiệu lực.
   - Cho phép nhân viên buồng phòng đổi giữa `cleaning` (Cần dọn) và `empty` (Đã dọn xong - Sẵn sàng đón khách) chỉ với 1 chạm.
2. **Cập nhật CSDL**: Cập nhật `rooms.status = :status` và tăng `version = version + 1`.
3. **Phát sóng thời gian thực (Real-time Broadcast)**:
   - Đẩy sự kiện qua SSE (`stream`) hoặc Laravel Reverb WebSocket tới toàn bộ các tab trình duyệt của nhân viên lễ tân và quản lý đang mở.
4. **Phản hồi**: Trả về JSON trạng thái mới.

#### 3. Database Operation
- **Bảng tác động**: `rooms` (cập nhật cột `status`, `version`, `updated_at`).

#### 4. Output Specification
- **Thành công (HTTP 200)**: `{"success": true, "room_id": 15, "new_status": "empty", "status_label": "Còn trống", "color_class": "bg-emerald-500"}`.
- **Thất bại (HTTP 422)**: `{"success": false, "message": "Không thể chuyển trạng thái thủ công sang Đang ở. Vui lòng tạo Hợp đồng trước."}`.

#### 5. UI/UX Specification & Xử lý lỗi
- **Ma trận phòng**: Chia theo tầng (Tầng 1, Tầng 2...), mỗi phòng là 1 thẻ Card bo tròn với mã màu động:
  - `empty`: Viền xanh lá, nền `bg-emerald-500/10 text-emerald-400`
  - `occupied`: Viền đỏ, nền `bg-rose-500/10 text-rose-400`
  - `cleaning`: Viền cam, nền `bg-amber-500/10 text-amber-400` (Icon chổi quét)
  - `overdue`: Viền vàng cảnh báo nợ, nền `bg-yellow-500/10 text-yellow-400`
  - `maintenance`: Viền xám, nền `bg-slate-500/10 text-slate-400`
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Chuyển trạng thái sang Đang ở khi chưa lập hợp đồng | Toast cảnh báo vàng: "Không thể chuyển trạng thái thủ công sang Đang ở. Vui lòng tạo Hợp đồng hoặc Phiếu booking trước". |
| Mất kết nối SSE thời gian thực | Hệ thống tự động chuyển sang cơ chế Polling ngầm mỗi 5 giây mà không làm gián đoạn người dùng. |

---

### [FEAT-AQ-04] Chốt số Điện - Nước định kỳ hàng tháng (Đơn lẻ & Hàng loạt)
- **Git Branch**: `AnhQuy/chot-so-dien-nuoc-ai-ocr`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\AdminDashboardController@storeUtility`, `storeUtilityBulk`, `payUtility`
- **Endpoint**:
  - `POST /smartroom/admin/utility` (`smartroom.admin.utility.store`)
  - `POST /smartroom/admin/utility/bulk` (`smartroom.admin.utility.bulk_store`)
  - `POST /smartroom/admin/utility/{id}/pay` (`smartroom.admin.utility.pay`)
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Request Validation POST /utility)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `room_id` | Integer | Có | `required\|integer\|exists:rooms,id` | "Phòng được chốt số không tồn tại." |
| `billing_month`| String | Có | `required\|regex:/^[0-9]{4}-(0[1-9]\|1[0-2])$/` | "Tháng tính tiền phải có định dạng YYYY-MM (Ví dụ: 2026-06)." |
| `new_electricity`| Integer | Có | `required\|integer\|min:0` | "Chỉ số điện mới phải là số nguyên dương." |
| `new_water` | Integer | Có | `required\|integer\|min:0` | "Chỉ số nước mới phải là số nguyên dương." |
| `electricity_price`| Numeric | Có | `required\|numeric\|min:0` | "Đơn giá tiền điện không hợp lệ." |
| `water_price` | Numeric | Có | `required\|numeric\|min:0` | "Đơn giá tiền nước không hợp lệ." |

#### 2. Business Logic Flow
1. **Lấy chỉ số cũ**: Truy vấn bản ghi tháng trước liền kề của phòng. Nếu là tháng đầu tiên, `old_electricity` và `old_water` lấy từ chỉ số bàn giao ban đầu.
2. **Kiểm tra ràng buộc logic**:
   - `new_electricity >= old_electricity`: Nếu nhỏ hơn, báo lỗi HTTP 422: *"Chỉ số điện mới không được nhỏ hơn chỉ số cũ tháng trước"*.
   - `new_water >= old_water`: Tương tự với chỉ số nước.
   - Cảnh báo tiêu thụ bất thường: Nếu `(new_electricity - old_electricity) > 1000 kWh`, đánh dấu cờ cảnh báo rò rỉ điện.
3. **Tính toán chi phí**:
   - `electric_cost = (new_electricity - old_electricity) * electricity_price`.
   - `water_cost = (new_water - old_water) * water_price`.
   - `total_utility_cost = electric_cost + water_cost`.
4. **Lưu CSDL**: Ghi vào bảng `utility_records` với `status = 'draft'`. Tự động đồng bộ sang bảng `bills` (Hóa đơn tổng hợp).

#### 3. Database Operation
- **Bảng tác động**: `utility_records`, `bills`.

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về màn hình Điện nước kèm toast xanh: "Chốt số điện nước thành công!".
- **Thất bại (HTTP 422)**: Báo lỗi viền đỏ ô nhập liệu.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Chỉ số mới nhỏ hơn chỉ số cũ | Ô chỉ số mới bôi đỏ: "Chỉ số mới không được nhỏ hơn chỉ số cũ tháng trước (Chỉ số cũ: 150 kWh)". |
| Mức tiêu thụ tăng đột biến (> 1000 kWh) | Toast cảnh báo vàng: "Lượng điện tiêu thụ tăng đột biến (+1200 kWh). Vui lòng kiểm tra lại công tơ xem có bị nhầm số không!". |
| Phòng đã được chốt số trong tháng đó rồi | Thông báo: "Hóa đơn điện nước tháng này của phòng đã được lập. Bạn chỉ có thể cập nhật chỉnh sửa lại bản ghi cũ". |

---

### [FEAT-AQ-05] AI Vision OCR Nhận diện mặt công tơ đơn lẻ & Quét hàng loạt khớp Serial phòng tự động
- **Git Branch**: `AnhQuy/chot-so-dien-nuoc-ai-ocr`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\AdminDashboardController@aiOcrMeter`, `aiOcrMeterBulk`
- **Endpoint**:
  - `POST /smartroom/admin/ai/ocr-meter` (`smartroom.admin.ai.ocr_meter`)
  - `POST /smartroom/admin/ai/ocr-meter-bulk` (`smartroom.admin.ai.ocr_meter_bulk`)
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation POST /ocr-meter-bulk)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `building_id` | Integer | Có | `required\|integer\|exists:buildings,id` | "Vui lòng chọn cơ sở lưu trú hợp lệ." |
| `meter_type` | String | Có | `required\|in:electricity,water` | "Loại công tơ phải là điện (electricity) hoặc nước (water)." |
| `images` | Array | Có | `required\|array\|min:1\|max:30` | "Số lượng ảnh công tơ tải lên từ 1 đến tối đa 30 ảnh." |
| `images.*` | File | Có | `image\|mimes:jpeg,png,jpg,webp\|max:10240` | "Ảnh công tơ phải có định dạng hợp lệ và dung lượng không quá 10MB." |

#### 2. Business Logic Flow
1. **Nạp danh mục Serial của cơ sở**: Truy vấn danh sách phòng thuộc `building_id`, lấy ra danh sách cặp `[room_id, room_number, electric_meter_serial, water_meter_serial]`.
2. **Gọi Google Gemini Vision API**:
   - Duyệt qua từng file ảnh, convert sang chuỗi `base64`.
   - Gửi yêu cầu phân tích thị giác AI kèm Structured Output Schema:
     `{"meter_reading": <int>, "serial_number": "<string>", "confidence": <float>}`.
3. **Thuật toán Khớp số Serial (Serial Matching Engine)**:
   - So sánh chuỗi `serial_number` AI đọc được với `electric_meter_serial` (hoặc `water_meter_serial`) của các phòng trong CSDL.
   - Nếu trùng khớp: Gán `matched = true`, `room_id = room.id`, `room_number = room.room_number`.
   - Nếu không khớp: Gán `matched = false`, gắn nhãn `"Cần gán phòng thủ công"`.
4. **Phản hồi**: Trả về danh sách đối soát JSON để hiển thị trực quan lên Modal chốt số hàng loạt.

#### 3. Database Operation
- Không ghi CSDL tại bước này (chỉ phân tích và gợi ý dữ liệu). Dữ liệu được ghi khi người dùng bấm Lưu qua `storeUtilityBulk`.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Trả về JSON kết quả gồm `total_processed`, `matched_count`, `unmatched_count` và mảng chi tiết từng phòng kèm chỉ số cũ, chỉ số mới, lượng tiêu thụ.
- **Thất bại (HTTP 422 / 500)**: Báo lỗi định dạng ảnh hoặc lỗi timeout từ Google Gemini API.

#### 5. UI/UX Specification & Xử lý lỗi
- **Modal Quét hàng loạt**: `#bulk-meter-ocr-modal`, hiệu ứng Laser Scan xanh lướt qua thumbnail các bức ảnh.
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Ảnh chụp đồng hồ quá mờ hoặc lóa sáng | Toast cảnh báo: "AI không nhận diện rõ số trên mặt đồng hồ do ảnh mờ/thiếu sáng. Vui lòng chụp lại rõ nét hoặc tự nhập tay". |
| Không tìm thấy Số SX trùng khớp với phòng nào | Dòng kết quả hiển thị nhãn vàng 'Cần gán phòng' kèm dropdown danh sách phòng để chủ trọ chọn nhanh trước khi lưu. |
| Tải vượt quá 30 ảnh trong 1 lượt | Modal hiển thị cảnh báo: "Hệ thống hỗ trợ quét tối đa 30 ảnh công tơ trong một lượt và chỉ chấp nhận định dạng ảnh JPG, PNG, WEBP". |

---

### [FEAT-AQ-06] Động cơ tính cước tự động (BillingEngine) & Bảng phân bổ doanh thu
- **Git Branch**: `AnhQuy/tinh-cuoc-hoa-don-vietqr`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Service & Endpoint**: `App\Services\BillingEngine`, `GET /api/revenue-breakdown`
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Tính toán hóa đơn phòng)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `room_id` | Integer | Có | `required\|integer\|exists:rooms,id` | "Phòng tính cước không hợp lệ." |
| `billing_month`| String | Có | `required\|regex:/^[0-9]{4}-(0[1-9]\|1[0-2])$/` | "Tháng tính cước không hợp lệ." |

#### 2. Business Logic Flow
1. **Tổng hợp đa thành phần chi phí**:
   - `room_fee`: Tiền thuê phòng cơ sở (từ bảng `rooms.price`).
   - `electric_fee`: Tiền điện tiêu thụ trong kỳ tính theo đơn giá.
   - `water_fee`: Tiền nước tiêu thụ trong kỳ.
   - `management_fee`: Phí quản lý chung cư (nếu là căn hộ: tính theo diện tích `area * management_fee_rate`).
   - `minibar_fee`: Phụ phí đồ uống, dịch vụ buồng phòng phát sinh (từ bảng `hotel_folio_items` nếu có).
   - `discount`: Khấu trừ khuyến mãi / giảm giá (nếu có).
2. **Công thức tính tổng tiền**:
   `total_amount = room_fee + electric_fee + water_fee + management_fee + minibar_fee - discount`.
3. **Cập nhật hoặc Tạo bản ghi Hóa đơn**: Ghi vào bảng `bills` kèm mã hóa đơn duy nhất `BILL-{YYYYMM}-{ROOM_NUMBER}` và hạn thanh toán `due_date = ngày 5 tháng kế tiếp`.
4. **Phân tích cơ cấu doanh thu**: API `/api/revenue-breakdown` trả về tỷ trọng phần trăm từng nguồn thu phục vụ vẽ biểu đồ Donut Chart (Chart.js).

#### 3. Database Operation
- **Bảng tác động**: `bills`, `utility_records`, `hotel_folio_items`.

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: Trả về `total`, cấu trúc `breakdown` (room, electric, water, service) và tỷ trọng `percentages`.

#### 5. UI/UX Specification & Xử lý lỗi
- **Biểu đồ doanh thu**: Thẻ `<canvas id="revenueDoughnutChart"></canvas>` hiển thị 4 màu: Tiền phòng (Xanh dương `#38bdf8`), Điện (Vàng `#facc15`), Nước (Xanh lơ `#06b6d4`), Dịch vụ (Tím `#a855f7`).

---

### [FEAT-AQ-07] Xuất Hóa đơn / Bảng kê Folio PDF chuẩn in kèm Mã VietQR động NAPAS247
- **Git Branch**: `AnhQuy/tinh-cuoc-hoa-don-vietqr`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `AdminDashboardController@printUtility`, `HotelReceptionController@printFolio`, `PaymentController@index`
- **Endpoint**:
  - `GET /smartroom/admin/utility/{id}/print` (`smartroom.admin.utility.print`)
  - `GET /smartroom/admin/hotel/folio/{bookingId}` (`admin.hotel.folio`)
  - `GET /smartroom/admin/payments` (`admin.payments.index`)
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Tham số xuất in)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `id` | Integer (Route) | Có | `exists:utility_records,id` | "Bản ghi hóa đơn không tồn tại." |

#### 2. Business Logic Flow
1. **Kiểm tra thông tin tài khoản ngân hàng chủ trọ**:
   Lấy `bank_name`, `bank_account_number`, `landlord_name` từ hồ sơ chủ trọ đã xác minh KYC.
   - **Guard Check**: Nếu chủ trọ chưa cập nhật STK ngân hàng, chặn xuất VietQR và hiển thị cảnh báo yêu cầu cài đặt.
2. **Sinh mã VietQR động chuẩn NAPAS247**:
   - Sử dụng định dạng QuickLink chuẩn VietQR:
     `https://img.vietqr.io/image/{BANK_ID}-{ACCOUNT_NO}-compact2.png?amount={TOTAL}&addInfo={BILL_CODE}&accountName={LANDLORD_NAME}`.
   - Mã QR chứa sẵn: STK ngân hàng thụ hưởng, chính xác số tiền cần nộp đến từng đồng, và nội dung chuyển khoản là mã hóa đơn.
3. **Kết xuất file PDF chuẩn in (DomPDF)**:
   - Sử dụng `Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.utility_invoice', $data)`.
   - Thiết lập khổ giấy A4 hoặc A5 đứng, nhúng font tiếng Việt hỗ trợ Unicode, hiển thị mã VietQR nổi bật ở góc dưới.
4. **Phản hồi**: Stream file PDF trực tiếp trên trình duyệt hoặc tải về máy.

#### 3. Database Operation
- **Đọc**: `utility_records`, `rooms`, `residents`, `tenants`, `landlord_profiles`. Không làm biến đổi dữ liệu.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Trả về PDF stream MIME `application/pdf` sẵn sàng bấm Ctrl+P để in hóa đơn nhiệt/A4.
- **Thất bại (HTTP 422)**: Báo lỗi chưa cấu hình tài khoản nhận tiền.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Chủ trọ chưa hoàn tất xác minh KYC hoặc chưa có STK | Modal chặn: "Bạn cần hoàn tất xác minh định danh KYC (CCCD & STK Ngân hàng) trước khi sử dụng tính năng nhận tiền online qua VietQR". |
| Lỗi mạng khi gọi API cổng VietQR | Hiển thị khung thông báo dự phòng: "Không tải được ảnh mã QR từ cổng VietQR. Vui lòng chuyển khoản thủ công theo thông tin STK bên dưới". |

---

### [FEAT-AQ-08] Quét nợ tự động và gửi tin nhắn nhắc tiền phòng Zalo/SMS/Telegram kèm link VietQR
- **Git Branch**: `AnhQuy/nhac-no-zalo-sms`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Service**: `AdminDashboardController@autoRemindUtilities`, `notifyUtility`, `App\Services\SmsZaloService`, `NotificationService`
- **Endpoint**: `POST /smartroom/admin/utility/auto-remind`, `POST /smartroom/admin/utility/{id}/notify`
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation POST /auto-remind)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `channel` | String | Không | `nullable\|in:all,zalo,sms,telegram` | "Kênh gửi tin nhắn không hợp lệ." |

#### 2. Business Logic Flow
1. **Quét danh sách hóa đơn trễ hạn**: Truy vấn các hóa đơn trong `utility_records` có `status = 'draft'` hoặc `'sent'` và ngày hiện tại đã quá hạn thanh toán (`due_date < now()`).
2. **Xây dựng nội dung tin nhắn cá nhân hóa**:
   - Template mẫu: *"Kính gửi [Tên_Cư_Dân] phòng [Số_Phòng], Ban Quản Lý xin gửi hóa đơn tiền phòng tháng [Tháng]. Tổng thanh toán: [Số_Tiền]đ. Quý khách vui lòng bấm vào liên kết sau để quét mã VietQR thanh toán nhanh: [Short_Link]. Xin cảm ơn!"*.
3. **Phân phối tin nhắn đa kênh (Background Dispatch)**:
   - Gửi tin Zalo ZNS / SMS Brandname qua `SmsZaloService`.
   - Gửi tin nhắn bot Telegram đến nhóm quản trị viên.
   - Ghi lịch sử gửi vào bảng `notification_logs` để chặn việc gửi spam nhiều lần trong cùng một ngày.
4. **Cập nhật trạng thái**: Chuyển trạng thái hóa đơn sang `status = 'sent'`.

#### 3. Database Operation
- **Bảng tác động**: `utility_records` (cập nhật status), `notification_logs` (lưu vết gửi tin).

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: `{"success": true, "reminded_count": 8, "message": "Đã gửi tin nhắn nhắc tiền phòng thành công đến 8 phòng trễ hạn!"}`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Gửi tin nhắc nợ cho phòng chưa có số điện thoại | Báo lỗi trên danh sách: "Không thể gửi tin nhắc nợ đến Phòng 201: Cư dân chưa cập nhật số điện thoại liên lạc". |
| Gửi tin nhắc nợ nhiều lần liên tục trong ngày | Popup cảnh báo: "Phòng này đã được gửi tin nhắc nợ hôm nay lúc 08:30. Bạn có chắc chắn muốn gửi tiếp?". |

---

### [FEAT-AQ-09] Quản lý Danh mục Trang thiết bị - Tài sản kho & Phân bổ phòng (Equipment & RoomEquipment)
- **Git Branch**: `AnhQuy/quan-ly-trang-thiet-bi`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\EquipmentController@index`, `store`, `update`, `allocate`, `recover`, `destroy`
- **Endpoint**:
  - `GET /smartroom/admin/equipment` (`admin.equipment.index`)
  - `POST /smartroom/admin/equipment/store` (`admin.equipment.store`)
  - `POST /smartroom/admin/equipment/{id}/update` (`admin.equipment.update`)
  - `POST /smartroom/admin/equipment/allocate` (`admin.equipment.allocate`)
  - `POST /smartroom/admin/equipment/recover` (`admin.equipment.recover`)
  - `DELETE /smartroom/admin/equipment/{id}/delete` (`admin.equipment.destroy`)
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Bàn giao thiết bị vào phòng POST /allocate)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `equipment_id` | Integer | Có | `required\|integer\|exists:equipment,id` | "Trang thiết bị không tồn tại." |
| `room_id` | Integer | Có | `required\|integer\|exists:rooms,id` | "Phòng tiếp nhận thiết bị không hợp lệ." |
| `quantity` | Integer | Có | `required\|integer\|min:1` | "Số lượng bàn giao tối thiểu là 1." |
| `condition` | String | Có | `required\|string\|max:100` | "Vui lòng ghi rõ tình trạng thiết bị (VD: Mới 100%, Hoạt động tốt)." |

#### 2. Business Logic Flow
1. **Kiểm tra tồn kho thực tế**: Truy vấn `equipment = Equipment::findOrFail($equipment_id)`.
   - **Guard Check**: Nếu `equipment->quantity < $request->quantity`, ném lỗi HTTP 422: *"Số lượng thiết bị trong kho không đủ để bàn giao"*.
2. **Mở DB Transaction**:
   - Trừ số lượng tồn kho trong bảng `equipment`: `quantity = quantity - $request->quantity`.
   - Thêm hoặc cập nhật bản ghi trong bảng trung gian `room_equipment`: gán `room_id`, `equipment_id`, số lượng bàn giao, tình trạng và `assigned_date = now()`.
3. **Quy trình Thu hồi về kho (/recover)**:
   - Khi cư dân trả phòng hoặc thiết bị hỏng cần sửa: Xóa/giảm bản ghi trong `room_equipment` và cộng hoàn trả số lượng vào kho `equipment`.
4. **Guard Check khi Xóa danh mục thiết bị**: Nếu thiết bị đang được phân bổ trong bất kỳ phòng nào, cấm xóa danh mục thiết bị khỏi hệ thống.

#### 3. Database Operation
- **Bảng tác động**: `equipment`, `room_equipment`.

#### 4. Output Specification
- **Thành công (HTTP 302)**: Redirect về `admin.equipment.index` kèm `session('success', 'Bàn giao trang thiết bị vào phòng thành công!')`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống tên hoặc mã thiết bị khi thêm mới | Các ô input bôi đỏ: "Vui lòng nhập tên trang thiết bị" và "Mã thiết bị không được để trống". |
| Bàn giao số lượng vượt quá tồn kho thực tế | Ô số lượng bôi đỏ: "Số lượng thiết bị trong kho không đủ để bàn giao (Tồn kho hiện tại: 2 cái, yêu cầu: 5 cái)". |
| Xóa danh mục thiết bị đang được sử dụng trong phòng | Modal chặn: "Không thể xóa trang thiết bị này! Thiết bị đang được phân bổ trong các phòng trọ. Bạn phải thu hồi về kho trước khi xóa". |

---

### [FEAT-AQ-10] Sổ quỹ thu - chi và ghi nhận dòng tiền phát sinh ngoài tiền phòng (Transactions)
- **Git Branch**: `AnhQuy/so-quy-thu-chi`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\ReportController@index`, `storeTransaction`
- **Endpoint**: `GET /smartroom/admin/reports` (`admin.reports.index`), `POST /smartroom/admin/reports/transactions` (`admin.reports.transaction.store`)
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation POST /transactions)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `type` | String | Có | `required\|in:income,expense` | "Loại phiếu phải là Thu (income) hoặc Chi (expense)." |
| `category` | String | Có | `required\|string\|max:100` | "Khoản mục thu chi không được để trống." |
| `amount` | Numeric | Có | `required\|numeric\|min:1000` | "Số tiền giao dịch tối thiểu là 1.000 VNĐ." |
| `description` | String | Có | `required\|string\|max:500` | "Vui lòng nhập lý do / diễn giải khoản thu chi." |
| `transaction_date`| Date | Có | `required\|date\|before_or_equal:today` | "Ngày ghi nhận không thể là ngày trong tương lai." |

#### 2. Business Logic Flow
1. **Gán Tenant Scoping**: Tự động gán `tenant_id = auth()->user()->tenant_id`.
2. **Lưu bản ghi dòng tiền**: Tạo bản ghi mới trong bảng `transactions` (hoặc `cash_flows`).
3. **Tính toán số dư lũy kế**:
   - `total_income = sum(amount) where type = 'income'`.
   - `total_expense = sum(amount) where type = 'expense'`.
   - `net_cash_flow = total_income - total_expense`.
4. **Phản hồi**: Redirect về báo cáo tài chính hiển thị thẻ KPI dòng tiền thuần thời gian thực.

#### 3. Database Operation
- **Bảng tác động**: `transactions`.

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về `admin.reports.index` kèm `session('success', 'Ghi nhận phiếu thu/chi thành công!')`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống số tiền hoặc hạng mục chi khi lập phiếu | Viền đỏ các ô input: "Vui lòng nhập lý do phát sinh" và "Số tiền phải lớn hơn 0 đ". |
| Chọn ngày lập phiếu lớn hơn ngày hiện tại | Báo lỗi: "Ngày ghi nhận phiếu thu/chi không thể là ngày trong tương lai". |

---

### [FEAT-AQ-11] AI Google Gemini tự động viết bài mô tả phòng chuẩn SEO thu hút khách thuê
- **Git Branch**: `AnhQuy/ai-viet-mo-ta-phong`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\RoomController@generateDescription`
- **Endpoint & Method**: `POST /smartroom/admin/rooms/description/ai` (`admin.rooms.description.ai`)
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `room_number` | String | Có | `required\|string` | "Số phòng không được để trống." |
| `area` | Numeric | Có | `required\|numeric` | "Diện tích phòng không hợp lệ." |
| `price` | Numeric | Có | `required\|numeric` | "Giá thuê phòng không hợp lệ." |
| `amenities` | Array | Không | `nullable\|array` | "Danh sách tiện ích không hợp lệ." |
| `highlights` | String | Không | `nullable\|string\|max:500` | "Ghi chú đặc điểm nổi bật tối đa 500 ký tự." |

#### 2. Business Logic Flow
1. **Thiết lập Prompt Marketing chuyên nghiệp**:
   Gửi yêu cầu đến Google Gemini API (`gemini-2.5-flash`):
   - Đóng vai chuyên gia Copywriter Bất động sản.
   - Tổng hợp các tham số: Diện tích, giá, tiện ích (máy lạnh, ban công, gác lửng), vị trí khu vực.
   - Yêu cầu cấu trúc bài đăng: Tiêu đề giật tít hấp dẫn, nội dung mô tả tiện nghi sinh động, bảng giá minh bạch, lời kêu gọi hành động (Call To Action - CTA) đặt lịch xem phòng ngay.
2. **Tối ưu SEO**: Tự động chèn các từ khóa tìm kiếm phổ biến (ví dụ: *phòng trọ giá rẻ, căn hộ mini đầy đủ nội thất, giờ giấc tự do, an ninh 24/7*).
3. **Phản hồi**: Trả về chuỗi văn bản mô tả để chèn vào textarea `description` của form phòng.

#### 3. Database Operation
- Không ghi CSDL tại bước này.

#### 4. Output Specification
- **Thành công (HTTP 200)**: `{"success": true, "description": "🌟 SIÊU PHẨM PHÒNG TRỌ BAN CÔNG THOÁNG MÁT... Giá chỉ 3.5tr/tháng..."}`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Chưa nhập thông số cơ bản của phòng | Viền đỏ: "Vui lòng nhập ít nhất một vài đặc điểm nổi bật của phòng trọ để AI xử lý". |
| Mất kết nối API | Toast cảnh báo lỗi kết nối và gợi ý nhập mô tả thủ công. |

---

### [FEAT-AQ-12] Nhật ký thao tác quản trị viên hệ thống (AdminActivityLog)
- **Git Branch**: `AnhQuy/nhat-ky-kiem-toan`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Service**: `App\Http\Controllers\AdminActivityLogController@index`, `App\Services\AdminActivityLogger`
- **Endpoint**: `GET /smartroom/admin/activity-logs` (`admin.activity_logs.index`)
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Bộ lọc tìm kiếm nhật ký)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `action` | String | Không | `nullable\|string` | "Loại thao tác lọc không hợp lệ." |
| `user_id` | Integer | Không | `nullable\|integer\|exists:users,id` | "Người thực hiện không hợp lệ." |

#### 2. Business Logic Flow
1. **Lắng nghe sự kiện hệ thống (Model Observers & Middleware)**:
   - Tự động bắt các thao tác CRUD trên các bảng trọng yếu (`Room::created`, `Room::updated`, `Contract::deleted`...).
2. **Ghi nhật ký chi tiết**:
   - Lưu trữ: `user_id`, `action` (login, create, update, delete), `model_type`, `model_id`, `description`, `ip_address`, `user_agent`, mảng `old_values` và `new_values` (JSON).
3. **Phân quyền hiển thị**: Chủ trọ chỉ xem nhật ký hoạt động thuộc cơ sở của mình; Superadmin xem toàn bộ hệ thống.

#### 3. Database Operation
- **Bảng tác động**: `admin_activity_logs`.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Render view `admin.activity_logs.index` với bảng phân trang 20 dòng/trang.

#### 5. UI/UX Specification & Xử lý lỗi
- **Bảng Timeline**: Hiển thị nhãn màu theo action: `create` (Xanh lá), `update` (Xanh dương), `delete` (Đỏ), `login` (Tím).

---

### [FEAT-AQ-13] Phân hệ Khách sạn / Lễ tân: Check-in, Check-out & Folio chi tiêu minibar
- **Git Branch**: `AnhQuy/PhanQuyen`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\HotelReceptionController@checkIn`, `checkOut`, `addFolioItem`, `printFolio`
- **Endpoint**:
  - `POST /smartroom/admin/hotel/check-in` (`admin.hotel.checkin`)
  - `POST /smartroom/admin/hotel/folio/{bookingId}/items` (`admin.hotel.folio.add_item`)
  - `POST /smartroom/admin/hotel/check-out/{bookingId}` (`admin.hotel.checkout`)
  - `GET /smartroom/admin/hotel/folio/{bookingId}` (`admin.hotel.folio`)
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Check-in khách sạn POST /check-in)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `room_id` | Integer | Có | `required\|integer\|exists:rooms,id` | "Phòng khách sạn không hợp lệ." |
| `guest_name` | String | Có | `required\|string\|max:255` | "Họ tên khách lưu trú không được để trống." |
| `guest_phone` | String | Có | `required\|regex:/^[0-9]{10}$/` | "Số điện thoại khách lưu trú không đúng 10 số." |
| `guest_cccd` | String | Không | `nullable\|string\|max:20` | "Số CCCD/Hộ chiếu không hợp lệ." |
| `rental_type` | String | Có | `required\|in:hour,day` | "Hình thức thuê phải là theo giờ hoặc theo ngày." |
| `checkin_time` | DateTime | Có | `required\|date` | "Thời điểm nhận phòng không hợp lệ." |

#### 2. Business Logic Flow
1. **Kiểm tra trạng thái phòng**: Phòng phải có `status == 'empty'`. Nếu đang `occupied` hoặc `cleaning`, ném lỗi chặn Check-in.
2. **Quy trình Check-in**:
   - Tạo bản ghi mới trong bảng `hotel_bookings` với `status = 'active'`.
   - Cập nhật phòng sang `status = 'occupied'`.
3. **Thêm phụ phí tiêu dùng minibar (addFolioItem)**:
   - Lễ tân ghi nhận nước ngọt, bia, giặt ủi vào bảng kê `hotel_folio_items` liên kết với `booking_id`.
4. **Quy trình Check-out**:
   - Tính tổng tiền phòng (theo số giờ hoặc số đêm thực tế) + tổng tiền minibar folio.
   - Chuyển `booking->status = 'completed'`, chuyển phòng sang `status = 'cleaning'` (Cần dọn vệ sinh buồng phòng).
   - Tự động xuất phiếu thanh toán Bảng kê Folio PDF có nhúng mã VietQR thanh toán.

#### 3. Database Operation
- **Bảng tác động**: `hotel_bookings`, `hotel_folio_items`, `rooms`, `cash_flows`.

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: `{"success": true, "booking_id": 45, "folio_url": "/smartroom/admin/hotel/folio/45"}`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Check-in vào phòng đang có khách hoặc chưa dọn dẹp | Modal chặn: "Phòng đang ở trạng thái Cần dọn dẹp vệ sinh. Vui lòng hoàn tất dọn phòng trước khi nhận khách mới". |
| Bỏ trống họ tên hoặc số điện thoại khách | Viền đỏ ô input bắt buộc. |


## C. CÁC MODULE & ĐẶC TẢ KỸ THUẬT DO HUỲNH VĂN VĨNH EM (THÀNH VIÊN) PHỤ TRÁCH

---

### [FEAT-VEM-01] Cổng tìm kiếm lưu trú công cộng Renty Portal (Glassmorphism & Responsive)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Route & View**: `GET /renty` (hoặc `/home`, `/`), View `resources/views/renty/index.blade.php`
- **Middleware**: `web`

#### 1. Input Specification (Tham số Query String)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `q` | String | Không | `nullable\|string\|max:100` | "Từ khóa tìm kiếm tối đa 100 ký tự." |
| `city` | String | Không | `nullable\|string` | "Tỉnh/Thành phố không hợp lệ." |
| `district` | String | Không | `nullable\|string` | "Quận/Huyện không hợp lệ." |
| `sort` | String | Không | `nullable\|in:price_asc,price_desc,rating_desc,newest` | "Tiêu chí sắp xếp không hợp lệ." |

#### 2. Business Logic Flow
1. **Truy vấn danh sách phòng**:
   - Truy vấn `Room::with(['building', 'tenant', 'reviews'])` với điều kiện mặc định `status = 'empty'`.
   - Tính toán điểm đánh giá sao trung bình (`rating = reviews->avg('rating') ?? 4.5`).
   - Lấy huy hiệu uy tín chủ trọ (`trustBadge`):
     - `premium_verified` / `verified`: Huy hiệu "Tích xanh" uy tín (Xanh ngọc `bg-sky-500/10 text-sky-300`).
     - `kyc_verified`: Huy hiệu "Đã xác minh KYC" (Xanh lá `bg-emerald-500/10 text-emerald-300`).
     - `unverified`: Chưa xác minh (Xám).
2. **Khởi tạo dữ liệu giao diện**: Nạp danh sách các cơ sở, ảnh đại diện, khoảng cách tiện ích, mức giá theo tháng/ngày/giờ.
3. **Render View**: Kết xuất giao diện Glassmorphism với hiệu ứng nền mờ `backdrop-blur-md bg-slate-900/80 border border-white/10`.

#### 3. Database Operation
- **Đọc**: `rooms`, `buildings`, `tenants`, `reviews`. Không sửa đổi dữ liệu.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Render HTML trang chủ Renty hoàn chỉnh kèm danh sách thẻ phòng dạng Grid (1 cột trên di động, 2 cột trên tablet, 3 cột trên desktop).

#### 5. UI/UX Specification & Xử lý lỗi
- **Layout**: Header với logo Renty phát sáng, thanh tìm kiếm lớn ở Banner Hero, nút chuyển chế độ xem Bản đồ / Danh sách.

---

### [FEAT-VEM-02] Bộ lọc tìm kiếm thông minh đa tiêu chí (Smart Search Filter)
- **Git Branch**: `feat(smart-search)`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Service & Controller**: `App\Services\SmartSearchService`, `App\Http\Controllers\Api\SmartSearchController`
- **Endpoint**: `GET /api/renty/rooms`
- **Middleware**: `web`

#### 1. Input Specification (Request Query Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `price_min` | Numeric | Không | `nullable\|numeric\|min:0` | "Giá tối thiểu phải lớn hơn hoặc bằng 0." |
| `price_max` | Numeric | Không | `nullable\|numeric\|gte:price_min` | "Giá tối đa phải lớn hơn hoặc bằng giá tối thiểu." |
| `rental_type` | String | Không | `nullable\|in:month,day,hour` | "Hình thức thuê phải là month, day hoặc hour." |
| `room_type` | String | Không | `nullable\|in:standard,deluxe,vip,studio` | "Hạng phòng không hợp lệ." |
| `amenities` | Array | Không | `nullable\|array` | "Danh sách tiện ích lọc không hợp lệ." |
| `only_empty` | Boolean | Không | `nullable\|boolean` | "Trạng thái chỉ phòng trống phải là true/false." |

#### 2. Business Logic Flow
1. **Xây dựng truy vấn động (Dynamic Query Builder)**:
   - Áp dụng các điều kiện lọc: `whereBetween('price', [$min, $max])`.
   - Nếu `only_empty == true`: Thêm điều kiện `where('status', 'empty')`.
   - Lọc theo tiện ích (JSON column): Duyệt qua mảng `amenities` (ví dụ: `wc_rieng`, `gac_lung`, `ban_cong`, `thu_cung`, `thang_may`):
     `whereJsonContains('amenities', $amenity)`.
2. **Sắp xếp & Phân trang**: Mặc định sắp xếp theo ngày đăng mới nhất hoặc theo giá tăng/giảm dần; phân trang 12 phòng/trang.
3. **Phản hồi**: Trả về dữ liệu JSON kèm metadata phân trang để giao diện cập nhật AJAX không cần tải lại trang.

#### 3. Database Operation
- **Đọc**: `rooms`, `buildings`, `reviews`. Lập chỉ mục trên các cột `price`, `status`, `room_type` để truy vấn dưới 50ms.

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: `{"success": true, "data": [...], "current_page": 1, "total_rooms": 28}`.
- **Thất bại (HTTP 422)**: Báo lỗi nếu khoảng giá `price_min > price_max`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Nhập khoảng giá tối thiểu lớn hơn giá tối đa | Ô giá bôi đỏ viền: "Khoảng giá tìm kiếm không hợp lệ (Giá tối thiểu phải nhỏ hơn giá tối đa)". |
| Không tìm thấy phòng nào phù hợp | Hiển thị Empty State với hình minh họa: "Không tìm thấy phòng phù hợp với tiêu chí của bạn. Hãy thử nới lỏng bộ lọc!". |

---

### [FEAT-VEM-03] Thanh công cụ so sánh phòng nổi song song (Compare tối đa 3 phòng)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Endpoint**: `POST /api/renty/rooms/compare`
- **Middleware**: `web`

#### 1. Input Specification (Request Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `room_ids` | Array | Có | `required\|array\|min:2\|max:3` | "Vui lòng chọn từ 2 đến tối đa 3 phòng để thực hiện so sánh." |
| `room_ids.*`| Integer | Có | `integer\|exists:rooms,id` | "Mã phòng so sánh không tồn tại." |

#### 2. Business Logic Flow
1. **Kiểm tra số lượng phòng**:
   - Nếu `count(room_ids) < 2`: Báo lỗi yêu cầu chọn ít nhất 2 phòng.
   - Nếu `count(room_ids) > 3`: Chặn và thông báo chỉ được so sánh tối đa 3 phòng.
2. **Nạp & Đối chiếu thông số kỹ thuật song song**:
   - Truy vấn chi tiết các phòng được chọn kèm thông tin tòa nhà.
   - Chuẩn hóa ma trận đối chiếu gồm các tiêu chí: Giá thuê theo tháng, tiền đặt cọc, diện tích sử dụng, đơn giá điện/nước, danh mục tiện ích có/không (Checkmark xanh / Dấu x đỏ), khoảng cách tiện ích và điểm sao đánh giá uy tín.
3. **Phản hồi**: Trả về cấu trúc bảng so sánh chi tiết dạng JSON hoặc render Partial Blade View.

#### 3. Database Operation
- **Đọc**: `rooms`, `buildings`, `utility_records`.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Trả về ma trận so sánh song song 3 cột để hiển thị trên Modal đối chiếu.

#### 5. UI/UX Specification & Xử lý lỗi
- **Thanh so sánh nổi (Sticky Floating Bar)**: Cố định ở đáy màn hình `fixed bottom-4 left-1/2 -translate-x-1/2 z-40 bg-slate-900/90 border border-sky-500/30 rounded-2xl px-6 py-3 shadow-2xl`, hiển thị thumbnail các phòng đã chọn kèm nút "So sánh ngay (2/3)".
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Chọn thêm phòng thứ 4 vào thanh so sánh | Toast cảnh báo vàng: "Bạn chỉ có thể so sánh tối đa 3 phòng cùng một lúc". |
| Bấm So sánh khi chỉ chọn 1 phòng | Toast thông báo: "Vui lòng chọn ít nhất 2 phòng để tiến hành so sánh đối chiếu". |

---

### [FEAT-VEM-04] Màn hình Chi tiết phòng lưu trú (Room Detail & Media Gallery)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Route & View**: `GET /renty/room/{id}`, View `resources/views/renty/detail.blade.php`
- **Middleware**: `web`

#### 1. Input Specification (Tham số Route)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `id` | Integer (Route) | Có | `exists:rooms,id` | "Phòng lưu trú không tồn tại hoặc đã ngừng cho thuê." |

#### 2. Business Logic Flow
1. **Truy vấn thông tin chi tiết**: `Room::with(['building', 'tenant', 'reviews', 'equipment'])->findOrFail($id)`.
2. **Tổng hợp đa phương tiện (Media Showcase)**:
   - Slide ảnh chất lượng cao (Lightbox xem ảnh phóng to).
   - Trình phát Video thực tế không gian phòng (hỗ trợ MP4, WebM).
3. **Tọa độ địa lý & Bản đồ GIS**: Nạp tọa độ GPS của tòa nhà để hiển thị vị trí trên OpenStreetMap / Google Maps.
4. **Phát hiện giá dị biệt (Price Anomaly Detection)**:
   - So sánh đơn giá phòng với mức giá trung bình của các phòng cùng khu vực. Nếu giá rẻ hơn hoặc cao hơn 30% bất thường, hiển thị nhãn cảnh báo để khách hàng lưu ý.

#### 3. Database Operation
- **Đọc**: `rooms`, `buildings`, `reviews`, `equipment`.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Render HTML trang chi tiết phòng đầy đủ tiện ích và form liên hệ đặt hẹn xem phòng.

#### 5. UI/UX Specification & Xử lý lỗi
- **Gallery**: Carousel trình chiếu ảnh mượt mà, nút "Đặt phòng ngay", nút "Đăng ký xem phòng", nút "Gửi báo cáo vi phạm".

---

### [FEAT-VEM-05] Hệ thống Đánh giá Review có xác thực hợp đồng lưu trú thực tế
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: Route `POST /renty/room/{id}/review`
- **Middleware**: `auth`

#### 1. Input Specification (Request Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `rating` | Integer | Có | `required\|integer\|min:1\|max:5` | "Điểm đánh giá phải từ 1 đến 5 sao." |
| `comment` | String | Có | `required\|string\|min:10\|max:1000` | "Nội dung nhận xét phải từ 10 đến 1000 ký tự." |

#### 2. Business Logic Flow
1. **Cơ chế Xác thực Khách thuê Thực tế (Verified Tenant Guard)**:
   - Kiểm tra xem người dùng đang đăng nhập (`auth()->id()`) đã từng có Hợp đồng thuê (`Contract`) hoặc Phiếu đặt phòng (`HotelBooking`) hợp lệ đối với căn phòng này chưa.
   - **Guard Check**: Nếu chưa từng thuê, **CHẶN ĐÁNH GIÁ** (HTTP 403): *"Bạn chỉ có thể đánh giá phòng này sau khi đã ký hợp đồng hoặc lưu trú thực tế tại đây"*.
2. **Lưu đánh giá**:
   - Tạo bản ghi mới trong bảng `reviews` với `user_id = auth()->id()`, `room_id = :id`, số sao và bình luận.
   - Gắn nhãn chứng thực `"Đã xác thực cư dân thuê trọ"`.
3. **Phản hồi**: Redirect back kèm thông báo cảm ơn đã gửi đánh giá.

#### 3. Database Operation
- **Bảng tác động**: `reviews` (INSERT bản ghi mới).

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về trang chi tiết phòng kèm toast xanh: "Cảm ơn bạn đã gửi đánh giá trải nghiệm lưu trú!".

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Người dùng chưa từng thuê phòng cố tình gửi review | Chặn gửi đánh giá kèm thông báo đỏ: "Bạn chỉ có thể đánh giá phòng này sau khi đã hoàn tất hợp đồng thuê tại đây". |
| Bỏ trống nội dung hoặc nhận xét dưới 10 ký tự | Ô nhận xét bôi đỏ viền: "Vui lòng nhập nội dung đánh giá chi tiết (tối thiểu 10 ký tự)". |

---

### [FEAT-VEM-06] Tiếp nhận Báo cáo phòng vi phạm / lừa cọc (RoomReport)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: Route `POST /renty/room/{id}/report`
- **Middleware**: `web`, `throttle:5,1`

#### 1. Input Specification (Request Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `reason` | String | Có | `required\|in:scam,fake_images,wrong_price,unsafe,other` | "Lý do báo cáo vi phạm không hợp lệ." |
| `description` | String | Có | `required\|string\|min:10\|max:1000` | "Vui lòng cung cấp mô tả chi tiết bằng chứng sai phạm (10 - 1000 ký tự)." |
| `reporter_name`| String | Không | `nullable\|string\|max:255` | "Họ tên người báo cáo tối đa 255 ký tự." |
| `reporter_phone`| String | Không | `nullable\|regex:/^[0-9]{10}$/` | "Số điện thoại liên lạc phải gồm đúng 10 chữ số." |

#### 2. Business Logic Flow
1. **Tiếp nhận khiếu nại**: Ghi nhận báo cáo vào bảng `room_reports` với `status = 'pending'`.
2. **Cơ chế Cảnh báo Tự động (Auto-flagging)**:
   - Đếm số lượng báo cáo `pending` của phòng đó.
   - Nếu một phòng nhận quá 3 báo cáo lừa đảo (`scam`), hệ thống tự động gắn cờ cảnh báo màu vàng `"Phòng đang bị người dùng báo cáo sai phạm"` trên cổng tìm kiếm Renty để bảo vệ người thuê khác.
3. **Phản hồi**: Thông báo đã tiếp nhận và sẽ đối soát xử lý trong vòng 12 giờ.

#### 3. Database Operation
- **Bảng tác động**: `room_reports`.

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: `{"success": true, "message": "Báo cáo của bạn đã được gửi đến Ban Quản Trị để xử lý."}`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống phần mô tả chi tiết vi phạm | Ô mô tả bôi đỏ viền: "Vui lòng nhập mô tả chi tiết hành vi sai phạm để ban quản trị đối soát xử lý". |

---

### [FEAT-VEM-07] Trợ lý ảo AI Renty Chatbot tư vấn thuê phòng theo mô hình RAG (Google Gemini API)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: `App\Http\Controllers\ChatbotController@chat`
- **Endpoint & Method**: `POST /renty/chatbot/chat`
- **Middleware**: `web`, `throttle:60,1`

#### 1. Input Specification (Request Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `message` | String | Có | `required\|string\|min:2\|max:300` | "Câu hỏi của bạn phải từ 2 đến tối đa 300 ký tự." |
| `conversation_history`| Array | Không | `nullable\|array\|max:10` | "Lịch sử hội thoại không hợp lệ." |

#### 2. Business Logic Flow
1. **Giai đoạn Truy xuất dữ liệu (Retrieval Phase - RAG)**:
   - Phân tích ngữ nghĩa câu hỏi để trích xuất các thực thể: Khu vực (Quận/Đường), Mức giá tối đa, Tiện ích yêu cầu (máy lạnh, gác lửng, thú cưng).
   - Truy vấn CSDL bảng `rooms` kết hợp `buildings` lấy tối đa 5 phòng trống (`status = 'empty'`) phù hợp nhất.
   - Nạp thông tin phòng vào đoạn văn bản ngữ cảnh Context.
2. **Giai đoạn Tăng cường & Sinh câu trả lời (Augmented Generation Phase)**:
   - Gửi Context phòng và câu hỏi người dùng đến Google Gemini API (`gemini-2.5-flash`).
   - Ràng buộc AI tuân thủ nguyên tắc: *"Chỉ trả lời dựa trên danh sách phòng trong Context. Không tự bịa đặt phòng không có thật."*
3. **Phản hồi**: Nhận văn bản trả lời từ AI và bóc tách danh sách phòng đính kèm (Card Preview) hiển thị trực tiếp trong khung chat.

#### 3. Database Operation
- **Đọc**: `rooms`, `buildings` (lọc phòng trống).

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: `{"success": true, "reply": "...", "suggested_rooms": [{"id": 8, "name": "...", "price": "...", "image": "..."}]}`.

#### 5. UI/UX Specification & Xử lý lỗi
- **Khung chat**: Cửa sổ nổi góc phải `#renty-chatbot-modal`, bong bóng chat người dùng và AI, hiệu ứng gõ phím 3 chấm nhảy.
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Gửi tin nhắn rỗng hoặc toàn khoảng trắng | Nút gửi bị vô hiệu hóa (disabled). Nếu cố tình gửi hiển thị: "Nội dung tin nhắn không được để trống". |
| Nhập câu hỏi quá dài (> 300 ký tự) | Ô chat bôi đỏ viền: "Câu hỏi quá dài (tối đa 300 ký tự). Vui lòng rút ngắn tiêu chí tìm kiếm của bạn". |
| Không tìm thấy phòng phù hợp trong CSDL | Chatbot trả lời thân thiện: "Renty chưa tìm thấy phòng trọ nào phù hợp với yêu cầu của bạn. Bạn thử nới rộng khoảng giá hoặc chọn khu vực lân cận nhé!". |

---

### [FEAT-VEM-08] Cổng dịch vụ Cư dân & Khách lưu trú (Guest Portal: Hóa đơn & Mã VietQR)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: `App\Http\Controllers\ResidentPortalController@index`, `billQr`, `requestRenewal`
- **Endpoint**:
  - `GET /smartroom/resident` (`smartroom.resident`)
  - `GET /smartroom/resident/bills/{id}/qr` (`smartroom.resident.bills.qr`)
  - `POST /smartroom/resident/contract/{id}/request-renewal` (`smartroom.resident.contract.request_renewal`)
- **Middleware**: `auth`

#### 1. Input Specification (Xin gia hạn hợp đồng POST /request-renewal)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `extension_months`| Integer | Có | `required\|integer\|min:1\|max:36` | "Thời gian xin gia hạn phải từ 1 đến 36 tháng." |
| `note` | String | Không | `nullable\|string\|max:500` | "Ghi chú gia hạn tối đa 500 ký tự." |

#### 2. Business Logic Flow
1. **Xác thực quyền Cư dân**: Lấy thông tin cư dân liên kết với tài khoản người dùng (`Resident::where('user_id', auth()->id())->first()`).
2. **Tổng hợp dữ liệu Dashboard Cư dân**:
   - Phòng đang thuê, thời hạn hợp đồng, danh sách người ở cùng phòng.
   - Danh sách hóa đơn chưa thanh toán kèm nút "Quét mã VietQR thanh toán nhanh".
3. **Hiển thị mã VietQR chuyển khoản**:
   - Khi bấm xem mã QR hóa đơn: Hệ thống sinh mã VietQR chứa đúng số tiền và cú pháp chuyển tiền để cư dân mở app ngân hàng quét thanh toán tức thì.

#### 3. Database Operation
- **Đọc**: `residents`, `rooms`, `contracts`, `bills`, `resident_relatives`.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Render view `resident.dashboard` hoặc trả về ảnh mã VietQR.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bấm mở VietQR khi hóa đơn đã được thanh toán | Toast xanh: "Hóa đơn tháng này của bạn đã được thanh toán đầy đủ. Cảm ơn bạn!". |
| Gửi xin gia hạn khi hợp đồng còn hạn trên 60 ngày | Toast cảnh báo: "Hợp đồng của bạn vẫn còn thời hạn dài (> 60 ngày). Hệ thống chỉ mở tính năng xin gia hạn trước khi hết hạn 30 ngày". |

---

### [FEAT-VEM-09] Tiếp nhận & Xử lý sự cố kỹ thuật và buồng phòng (Smart Tickets & AI NLP)
- **Git Branch**: `VinhEm/8-XuLyBaoHongDangDonPhong`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: `App\Http\Controllers\ResidentPortalController@storeTicket`, `analyzeTicket`
- **Endpoint**:
  - `POST /smartroom/resident/tickets` (`smartroom.resident.tickets.store`)
  - `POST /smartroom/resident/tickets/analyze` (`smartroom.resident.tickets.analyze`)
- **Middleware**: `auth`

#### 1. Input Specification (Gửi báo hỏng POST /tickets)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `title` | String | Có | `required\|string\|max:255` | "Tiêu đề sự cố không được để trống." |
| `description` | String | Có | `required\|string\|min:5\|max:1000` | "Vui lòng mô tả chi tiết sự cố hỏng hóc (5 - 1000 ký tự)." |
| `category` | String | Có | `required\|in:electric,water,furniture,maintenance,other` | "Danh mục phân loại sự cố không hợp lệ." |
| `image` | File | Không | `nullable\|image\|mimes:jpeg,png,jpg,webp\|max:10240` | "Ảnh chụp hiện trạng sự cố tối đa 10MB." |

#### 2. Business Logic Flow
1. **AI NLP Phân tích độ khẩn cấp (analyzeTicket)**:
   - Sử dụng Google Gemini AI phân tích nội dung mô tả: Tự động đánh giá mức độ nghiêm trọng (`priority`: `low`, `medium`, `high`) và gợi ý biện pháp xử lý tạm thời cho cư dân (ví dụ: *"Khóa van nước tổng ngay lập tức để tránh ngập phòng"*).
2. **Lưu phiếu sự cố**: Tạo bản ghi trong bảng `tickets` với `status = 'pending'`, gán `resident_id`, `room_id`, `priority` từ AI.
3. **Thông báo Ban Quản Lý**: Tự động gửi thông báo đến chủ trọ / nhân viên kỹ thuật để điều phối sửa chữa.

#### 3. Database Operation
- **Bảng tác động**: `tickets`.

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về danh sách ticket kèm toast: "Đã gửi phiếu báo hỏng thành công. Kỹ thuật viên sẽ xử lý sớm nhất!".

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Cư dân gửi ticket nhưng để trống mô tả | Ô mô tả bôi đỏ: "Vui lòng nhập mô tả sự cố để ban quản lý nắm được nguyên nhân hư hỏng". |
| Tải ảnh sự cố vượt quá 10MB | Thông báo lỗi: "Kích thước ảnh chụp sự cố quá lớn. Vui lòng chọn ảnh dung lượng dưới 10MB". |

---

### [FEAT-VEM-10] Quản lý thông tin Cư dân & Thân nhân lưu trú theo phòng (Residents & Relatives)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: `AdminDashboardController@storeResident`, `updateResident`, `deleteResident`, `getRelatives`, `storeRelative`, `updateRelative`, `deleteRelative`
- **Endpoint**:
  - `POST /smartroom/admin/resident` (`smartroom.admin.resident.store`)
  - `PUT /smartroom/admin/resident/{id}` (`smartroom.admin.resident.update`)
  - `DELETE /smartroom/admin/resident/{id}` (`smartroom.admin.resident.delete`)
  - `GET /smartroom/admin/resident/{residentId}/relatives` (`smartroom.admin.resident.relatives`)
  - `POST /smartroom/admin/resident/{residentId}/relative` (`smartroom.admin.resident.relative.store`)
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Thêm người ở cùng POST /relative)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `name` | String | Có | `required\|string\|max:255` | "Vui lòng nhập họ và tên người ở cùng." |
| `phone` | String | Không | `nullable\|regex:/^[0-9]{10}$/` | "Số điện thoại người ở cùng không hợp lệ." |
| `cccd` | String | Có | `required\|regex:/^[0-9]{12}$/` | "Số CCCD người ở cùng phải đủ 12 chữ số." |
| `relationship` | String | Có | `required\|string\|max:100` | "Vui lòng ghi rõ quan hệ nhân thân (Bạn bè, Vợ/Chồng, Anh em)." |

#### 2. Business Logic Flow
1. **Kiểm tra sức chứa của phòng**:
   - Đếm tổng số người đang ở phòng đó (1 cư dân đại diện + số thân nhân hiện tại).
   - Nếu vượt quá sức chứa tối đa quy định của phòng, ném lỗi HTTP 422: *"Số lượng người ở cùng vượt quá sức chứa tối đa của phòng"*.
2. **Mã hóa PII**: Dữ liệu SĐT và CCCD của người ở cùng được mã hóa AES-256-GCM trước khi lưu vào bảng `resident_relatives`.
3. **Phản hồi**: Trả về danh sách thân nhân cập nhật dưới dạng JSON.

#### 3. Database Operation
- **Bảng tác động**: `residents`, `resident_relatives`.

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: `{"success": true, "message": "Thêm người ở cùng phòng thành công!"}`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Thêm người ở cùng nhưng bỏ trống CCCD | Ô CCCD bôi đỏ: "Vui lòng điền họ tên và số CCCD hợp lệ của người ở cùng". |
| Số người ở cùng vượt quá sức chứa phòng | Cảnh báo quá tải: "Phòng này chỉ có sức chứa tối đa 2 người. Vui lòng kiểm tra lại quy định phòng". |

---

### [FEAT-VEM-11] Tự động kết xuất tờ khai đăng ký tạm trú Mẫu CT01 (Bộ Công an)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: `App\Http\Controllers\AdminDashboardController@exportCt01`
- **Endpoint**: `GET /smartroom/admin/resident/{id}/export-ct01` (`smartroom.admin.resident.export_ct01`)
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Tham số Route)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `id` | Integer (Route) | Có | `exists:residents,id` | "Hồ sơ cư dân không tồn tại trên hệ thống." |

#### 2. Business Logic Flow
1. **Tổng hợp dữ liệu nhân thân**:
   - Giải mã số CCCD, SĐT của cư dân đại diện và danh sách người ở cùng phòng từ bảng `residents` và `resident_relatives`.
   - Lấy địa chỉ cơ sở lưu trú từ bảng `buildings`, thông tin chủ hộ/chủ trọ từ bảng `landlord_profiles`.
2. **Kiểm tra tính đầy đủ của hồ sơ (Completeness Guard)**:
   - Nếu cư dân còn thiếu Quê quán (`hometown`), Ngày sinh (`dob`) hoặc Số CCCD: Chặn xuất và yêu cầu cập nhật hồ sơ trước.
3. **Điền biểu mẫu Mẫu CT01 tự động (DomPDF)**:
   - Tự động điền đúng các mục hành chính theo chuẩn Bộ Công an: Họ tên, Ngày tháng năm sinh, Giới tính, Số định danh cá nhân CCCD, Nơi thường trú, Nơi tạm trú, Ý kiến của chủ hộ/chủ cơ sở lưu trú.
4. **Xuất file**: Kết xuất file PDF Mẫu CT01 sẵn sàng in để nộp Công an phường/xã.

#### 3. Database Operation
- **Đọc**: `residents`, `resident_relatives`, `buildings`, `landlord_profiles`. Không ghi CSDL.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Tải về file PDF tên `Mau_CT01_TamTru_{TenCuDan}.pdf`.
- **Thất bại (HTTP 422)**: Báo lỗi hồ sơ cư trú chưa đầy đủ thông tin.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Xuất CT01 khi thiếu thông tin quê quán / ngày sinh | Modal cảnh báo: "Hồ sơ cư trú chưa đầy đủ thông tin (thiếu Quê quán / Ngày sinh). Vui lòng cập nhật đầy đủ trước khi xuất mẫu CT01". |

---

### [FEAT-VEM-12] Tiện ích Đăng ký nhận chuông báo khi phòng chuyển sang trống (Empty Room Alert)
- **Git Branch**: `main`
- **Thành viên phụ trách**: Huỳnh Văn Vĩnh Em
- **Controller & Method**: `AdminDashboardController@storeContactRequest`, `updateContactRequestStatus`
- **Endpoint**: `POST /renty/contact-request` (`renty.contact_request.store`)
- **Middleware**: `web`, `throttle:5,1`

#### 1. Input Specification (Request Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `room_id` | Integer | Có | `required\|integer\|exists:rooms,id` | "Phòng đăng ký theo dõi không tồn tại." |
| `name` | String | Có | `required\|string\|max:255` | "Vui lòng nhập họ và tên của bạn." |
| `phone` | String | Có | `required\|regex:/^(0[3\|5\|7\|8\|9])[0-9]{8}$/` | "Số điện thoại nhận chuông báo không hợp lệ." |
| `note` | String | Không | `nullable\|string\|max:500` | "Ghi chú tối đa 500 ký tự." |

#### 2. Business Logic Flow
1. **Kiểm tra trùng lặp đăng ký**:
   Kiểm tra trong bảng `contact_requests` xem SĐT này đã đăng ký theo dõi phòng này trong 30 ngày qua chưa. Nếu đã có, trả về thông báo đã ghi nhận.
2. **Lưu phiếu đăng ký**: Tạo bản ghi trong `contact_requests` với `type = 'empty_room_alert'`, `status = 'pending'`.
3. **Cơ chế kích hoạt chuông báo tự động**:
   Khi một hợp đồng thuê kết thúc và phòng chuyển trạng thái từ `occupied` sang `empty` (hoặc nhân viên dọn phòng xong), hệ thống tự động quét danh sách `contact_requests` của phòng đó và gửi tin nhắn SMS/Zalo thông báo cho khách: *"Phòng [Số_Phòng] tại [Địa_Chỉ] bạn đang theo dõi hiện đã trống! Bấm vào đây để đặt phòng ngay: [Link]"*.

#### 3. Database Operation
- **Bảng tác động**: `contact_requests`, `notification_logs`.

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: `{"success": true, "message": "Đăng ký nhận chuông báo thành công! Renty sẽ nhắn tin ngay khi phòng này có người trả."}`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống số điện thoại nhận thông báo | Viền đỏ ô SĐT: "Vui lòng nhập số điện thoại để hệ thống gửi thông báo". |
| Số điện thoại đã đăng ký nhận thông báo phòng này | Toast thông báo: "Bạn đã đăng ký nhận chuông báo cho phòng này rồi. Hệ thống sẽ nhắn tin ngay khi phòng trống!". |


# TÀI LIỆU THAM KHẢO

1. Laravel Documentation (v11.x): The PHP Framework for Web Artisans. Truy cập tại: https://laravel.com/docs/11.x

2. Google Cloud AI Documentation: Gemini Models and OpenAI-compatible REST API Reference. Truy cập tại: https://ai.google.dev/docs

3. Chính phủ nước CHXHCN Việt Nam: Nghị định số 13/2023/NĐ-CP ngày 17/04/2023 về Bảo vệ dữ liệu cá nhân. Cổng thông tin điện tử Chính phủ.

4. Công ty Cổ phần Thanh toán Quốc gia Việt Nam (NAPAS): Tiêu chuẩn kỹ thuật định dạng thanh toán VietQR cho chuyển khoản liên ngân hàng. Truy cập tại: https://vietqr.net/

5. World Wide Web Consortium (W3C): Web Authentication: An API for accessing Public Key Credentials Level 2 (WebAuthn / FIDO2). Truy cập tại: https://www.w3.org/TR/webauthn-2/

6. Tailwind CSS Documentation: A utility-first CSS framework for rapid UI development. Truy cập tại: https://tailwindcss.com/docs

7. Chart.js Documentation: Simple yet flexible JavaScript charting for designers & developers. Truy cập tại: https://www.chartjs.org/docs/

8. Barryvdh Laravel-DomPDF: A DOMPDF Wrapper for Laravel. Truy cập tại: https://github.com/barryvdh/laravel-dompdf
