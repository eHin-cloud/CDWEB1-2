TRƯỜNG CAO ĐẲNG CÔNG NGHỆ THỦ ĐỨC

KHOA CÔNG NGHỆ THÔNG TIN

BÁO CÁO ĐỒ ÁN

MÔN HỌC: CHUYÊN ĐỀ PHÁT TRIỂN WEB 1

ĐỀ TÀI:

HỆ THỐNG QUẢN LÝ NHÀ TRỌ, CHUNG CƯ, CĂN HỘ DỊCH VỤ VÀ KHÁCH SẠN THÔNG MINH(RENTRY & SMARTROOM)

| STT | Họ và Tên Sinh Viên | Mã Số Sinh Viên | Chức Vụ |
| --- | --- | --- | --- |
| 01 | Nguyễn Thanh Hiền | 23211TT4102 | Nhóm Trưởng |
| 02 | Nguyễn Anh Quý | 23211TT4188 | Nhóm Phó |
| 03 | Huỳnh Văn Vĩnh Em | 23211TT4256 | Thành Viên |

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
| 01 | Nguyễn Thanh Hiền | 23211TT4102 | Nhóm Trưởng |
| 02 | Nguyễn Anh Quý | 23211TT4188 | Nhóm Phó |
| 03 | Huỳnh Văn Vĩnh Em | 23211TT4256 | Thành Viên |

GIẢNG VIÊN HƯỚNG DẪN: PHAN THANH NHUẦN

Thành phố Hồ Chí Minh, Năm 2026

MỤC LỤC

DANH MỤC HÌNH ẢNH3

DANH MỤC BẢNG SỐ LIỆU4

DANH MỤC TỪ VIẾT TẮT5

LỜI MỞ ĐẦU5

I. KẾ HOẠCH LÀM VIỆC NHÓM7

1. Bảng phân chia công việc (Bảng 2)7

2. Bảng báo cáo phiên họp nhóm (Bảng 3)11

II. GIỚI THIỆU ĐỀ TÀI VÀ MÔ TẢ CHỨC NĂNG13

1. Giới thiệu đề tài13

a. Hiện trạng và vấn đề13

b. Mục tiêu của đề tài13

c. Công nghệ sử dụng14

2. Bảng danh mục chức năng và Endpoint API hệ thống (Bảng 4)14

III. DATABASE VÀ MÔ HÌNH ERD25

1. Mô hình ERD (Entity Relationship Diagram)25

2. Từ điển dữ liệu (Data Dictionary - 10 Thực thể cốt lõi)26

a. Bảng Users & Roles (Tài khoản và Vai trò)26

b. Bảng Properties / Buildings (Cơ sở lưu trú: Nhà trọ, Chung cư, Tòa nhà, Khách sạn)

c. Bảng Rooms & Condos (Phòng trọ, Căn hộ chung cư, Phòng khách sạn & Minibar)

d. Bảng Residents & Guests (Cư dân thuê trọ & Khách lưu trú khách sạn)

e. Bảng Contracts & Bookings (Hợp đồng thuê dài hạn & Đặt phòng khách sạn)

f. Bảng Utility & Services (Chốt Điện - Nước & Dịch vụ Khách sạn)

g. Bảng Bills & Transactions (Hóa đơn thu tiền, Bảng kê Folio & Sổ quỹ)

h. Bảng Tickets & RoomReports (Sự cố kỹ thuật, Dịch vụ phòng & Khiếu nại)

i. Bảng LandlordProfiles & VerificationRequests (Hồ sơ & Yêu cầu duyệt chủ trọ)36

j. Bảng AdminActivityLogs & AuditLogs (Nhật ký truy vết & Kiểm toán bất biến)37

IV. THIẾT KẾ GIAO DIỆN DEMO VÀ KỊCH BẢN XỬ LÝ LỖI (UI/UX)39

1. Trang Đăng nhập & Đăng ký (WebAuthn Passkey + Mật khẩu)39

2. Trang Dashboard Chủ trọ / Admin (Thống kê & AI Insight)40

3. Trang Quản lý Danh sách phòng & Sơ đồ Ma trận phòng lưu trú (Room Matrix)

4. Trang Quản lý Hợp đồng thuê & Phiếu đặt phòng (Bookings) trực tuyến

5. Trang Ghi chỉ số Điện - Nước định kỳ & AI OCR Camera42

6. Trang Quản lý Thanh toán, Xuất hóa đơn VietQR & Bảng kê Folio Khách sạn (PDF)

7. Trang Cổng thông tin Cư dân & Khách lưu trú (Guest Portal) & Mẫu tạm trú CT01

8. Trang Trợ lý ảo AI & Chatbot tư vấn thuê phòng Renty44

9. Trang Kiểm duyệt Hồ sơ Định danh Chủ trọ (Admin Verification)44

10. Trang Quản lý Tài sản - Trang thiết bị & Minibar (Asset & Minibar Management)

TÀI LIỆU THAM KHẢO46


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


## 1. Bảng phân chia công việc (Bảng 2)

| Họ và tên | STT | Công việc | Ngày bắt đầu | Hạn hoàn thành | Sinh viên đánh giá | Đánh giá của GV |
| --- | --- | --- | --- | --- | --- | --- |
| Nguyễn Thanh Hiền(Nhóm Trưởng) | 1 | Khởi tạo dự án & Database Migrations | 07/09/2026 | 09/09/2026 | 1 |  |
|  | 2 | Phân quyền người dùng & Multi-tenancy | 07/09/2026 | 11/09/2026 | 0.75 |  |
|  | 3 | Đăng ký, Đăng nhập & Rate Limiting | 07/09/2026 | 13/09/2026 | 0.75 |  |
|  | 4 | Đăng nhập sinh trắc học Passkey | 07/09/2026 | 15/09/2026 | 1.5 |  |
|  | 5 | Quản lý Phòng trọ (CRUD Room) | 07/09/2026 | 17/09/2026 | 1.5 |  |
|  | 6 | Trang Chi tiết phòng (Room Detail) | 07/09/2026 | 19/09/2026 | 0.75 |  |
|  | 7 | Quản lý Hợp đồng & Tiền cọc | 07/09/2026 | 22/09/2026 | 1 |  |
|  | 8 | Ký số hợp đồng online & OTP | 07/09/2026 | 24/09/2026 | 0.75 |  |
|  | 9 | AI Soạn thảo điều khoản hợp đồng | 07/09/2026 | 26/09/2026 | 0.75 |  |
|  | 10 | Xác thực định danh & Tích Xanh KYC | 07/09/2026 | 28/09/2026 | 0.5 |  |
|  | 11 | Onboarding đăng ký nhanh Chủ trọ | 07/09/2026 | 30/09/2026 | 0.25 |  |
|  | 12 | Duyệt hồ sơ Chủ trọ (Admin) | 07/09/2026 | 02/10/2026 | 0.5 |  |
| Nguyễn Anh Quý(Nhóm Phó) | 1 | Quản lý Tòa nhà & Cơ sở lưu trú | 07/09/2026 | 09/09/2026 | 0.75 |  |
|  | 2 | Sơ đồ ma trận phòng (Room Matrix) | 07/09/2026 | 12/09/2026 | 0.75 |  |
|  | 3 | Chốt số Điện - Nước định kỳ | 07/09/2026 | 14/09/2026 | 1 |  |
|  | 4 | AI Quét số công tơ điện nước (OCR) | 07/09/2026 | 16/09/2026 | 0.75 |  |
|  | 5 | Tính tiền phòng & Hóa đơn tự động | 07/09/2026 | 18/09/2026 | 2 |  |
|  | 6 | Xuất Hóa đơn PDF & Mã VietQR | 07/09/2026 | 21/09/2026 | 0.75 |  |
|  | 7 | Nhắc nợ tự động qua Zalo/SMS | 07/09/2026 | 23/09/2026 | 1.5 |  |
|  | 8 | Quản lý Trang thiết bị & Tài sản | 07/09/2026 | 25/09/2026 | 0.75 |  |
|  | 9 | Sổ quỹ thu - chi tài chính | 07/09/2026 | 27/09/2026 | 0.75 |  |
|  | 10 | AI Viết bài mô tả phòng chuẩn SEO | 07/09/2026 | 30/09/2026 | 0.5 |  |
|  | 11 | Nhật ký kiểm toán (Audit Logs) | 07/09/2026 | 02/10/2026 | 0.5 |  |
| Huỳnh Văn Vĩnh Em(Thành Viên) | 1 | Cổng tìm kiếm & Đặt phòng Renty | 07/09/2026 | 09/09/2026 | 1 |  |
|  | 2 | Bộ lọc tìm kiếm phòng thông minh | 07/09/2026 | 12/09/2026 | 0.75 |  |
|  | 3 | So sánh phòng trực quan (Compare) | 07/09/2026 | 14/09/2026 | 0.75 |  |
|  | 4 | Mã hóa bảo mật dữ liệu cá nhân | 07/09/2026 | 16/09/2026 | 0.75 |  |
|  | 5 | Đánh giá phòng & Báo cáo sai phạm | 07/09/2026 | 18/09/2026 | 0.5 |  |
|  | 6 | Trợ lý ảo AI Renty Chatbot | 07/09/2026 | 21/09/2026 | 2 |  |
|  | 7 | Cổng dịch vụ Cư dân (Guest Portal) | 07/09/2026 | 23/09/2026 | 1 |  |
|  | 8 | Xử lý báo hỏng & Dọn phòng (Tickets) | 07/09/2026 | 25/09/2026 | 0.75 |  |
|  | 9 | Quản lý Cư dân & Người ở cùng | 07/09/2026 | 27/09/2026 | 0.75 |  |
|  | 10 | Xuất tờ khai tạm trú Mẫu CT01 | 07/09/2026 | 30/09/2026 | 1 |  |
|  | 11 | Chuông báo phòng trống (Room Alert) | 07/09/2026 | 02/10/2026 | 0.75 |  |


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


### d. Kiến trúc Đa mô hình lưu trú & Động cơ Tính tiền (Billing Engine)

Hệ thống được thiết kế theo kiến trúc lai (Hybrid Hospitality & Rental PMS) nhằm đáp ứng đồng thời cả 2 luồng vận hành đặc thù:

1. **Mô hình Thuê dài hạn theo tháng (Nhà trọ truyền thống & Chung cư mini)**:
   - **Đối tượng**: Cư dân (Resident) thuê dài hạn từ 6 - 12 tháng, ký kết hợp đồng điện tử xác thực OTP và chữ ký vẽ tay Canvas.
   - **Động cơ tính tiền định kỳ**: Vào cuối tháng, hệ thống tổng hợp: `Tiền phòng/tháng + (Điện mới - Điện cũ) * Giá điện + (Nước mới - Nước cũ) * Giá nước + Phí dịch vụ cố định (Thang máy, rác, wifi, gửi xe)`. Hóa đơn được xuất tự động kèm mã VietQR và gửi tin nhắn nhắc nợ qua Zalo. Đồng thời, hệ thống hỗ trợ xuất dữ liệu khai báo cư trú Mẫu CT01 theo quy chuẩn Bộ Công An.

2. **Mô hình Thuê ngắn hạn theo ngày / giờ (Khách sạn & Homestay nghỉ dưỡng)**:
   - **Đối tượng**: Khách lưu trú (Guest) thuê theo block giờ (2 giờ đầu cố định + đơn giá giờ phụ trội) hoặc thuê theo ngày/đêm (Check-in 14:00, Check-out 12:00 hôm sau).
   - **Động cơ tính tiền tức thời (Folio Engine)**: Tại thời điểm Check-out, hệ thống tự động tính toán: `(Số ngày/đêm * Giá ngày) hoặc [Giá block 2h đầu + (Số giờ thêm * Đơn giá giờ thêm)] + Phụ thu nhận phòng sớm / trả phòng trễ + Chi phí Minibar (đồ uống, thức ăn nhẹ tiêu thụ) - Tiền đặt cọc`. Hóa đơn Bảng kê Folio tích hợp mã VietQR động được in ngay tại quầy lễ tân.
   - **Quy trình Xoay vòng buồng phòng thời gian thực (Realtime Room Flow)**: Khi khách Check-out, phòng tự động chuyển sang trạng thái **Cần dọn dẹp** (`cleaning` / `dirty`). Nhân viên Buồng phòng (Housekeeper) nhận danh sách trên thiết bị di động, sau khi vệ sinh xong bấm **"Đã dọn xong"** ➔ Phòng lập tức chuyển sang trạng thái **Sạch** (`clean` / `empty`), phát tín hiệu Realtime qua Server-Sent Events (SSE) để Sơ đồ ma trận phòng của Lễ tân đổi màu xanh đón khách mới ngay lập tức.


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
| Lễ tân | Check-in nhận phòng nhanh | POST | /smartroom/admin/hotel/check-in | Tiếp nhận thông tin khách lưu trú ngắn hạn (ngày/giờ), đổi phòng sang Đang ở (occupied) |
| Lễ tân | Ghi nhận sử dụng Minibar | POST | /smartroom/admin/hotel/folio/{id}/items | Tích chọn đồ uống minibar và dịch vụ phòng khách đã tiêu thụ vào hóa đơn phòng |
| Lễ tân | Check-out trả phòng tức thì | POST | /smartroom/admin/hotel/check-out/{id} | Tính tiền giờ/ngày + phụ thu + minibar, chuyển trạng thái phòng sang Cần dọn (cleaning) |
| Lễ tân | Xuất Bảng kê Folio VietQR | GET | /smartroom/admin/hotel/folio/{id} | Xuất hóa đơn Folio chi tiết tích hợp mã VietQR động để khách thanh toán tại quầy |
| Buồng phòng | Danh sách phòng chờ dọn | GET | /smartroom/housekeeping | Giao diện tối ưu di động hiển thị danh sách các phòng bẩn khách vừa trả |
| Buồng phòng | Cập nhật trạng thái vệ sinh | POST | /smartroom/housekeeping/{id}/status | Buồng phòng bấm Đã dọn xong, chuyển phòng sang Sạch (clean) và bắn tín hiệu Realtime |
| Admin | Danh sách duyệt xác minh | GET | /admin/verifications | Xem danh sách các hồ sơ KYC và Premium đang chờ phê duyệt |
| Admin | Phê duyệt hồ sơ xác minh | POST | /admin/verifications/{id}/approve | Duyệt hồ sơ: thăng cấp quyền chủ trọ, cấp Tích Xanh, mở cổng VietQR |
| Admin | Từ chối hồ sơ xác minh | POST | /admin/verifications/{id}/reject | Từ chối hồ sơ kèm lý do phản hồi cho chủ trọ bổ sung lại |
| Admin | Xem tài liệu pháp lý bảo mật | GET | /admin/verification-documents/{id} | Lấy URL ký có thời hạn (TTL 5 phút) để xem ảnh CCCD/PCCC có watermark |
| Admin | Mở khóa tài liệu nhạy cảm | POST | /admin/verification-documents/{id}/unlock | Yêu cầu mở khóa xem hồ sơ sau duyệt kèm lý do nghiệp vụ và Passkey |
| Admin | Nhật ký kiểm toán Audit Log | GET | /admin/audit-logs | Xem lịch sử truy cập dữ liệu nhạy cảm bất biến (chống sửa xóa) |
| Admin | Nhật ký hoạt động Admin | GET | /smartroom/admin/activity-logs | Theo dõi toàn bộ lịch sử thao tác đăng nhập, tạo sửa xóa của hệ thống |
| Admin | Quản lý người dùng hệ thống | GET | /list | Xem danh sách toàn bộ tài khoản người dùng trên hệ thống |
| Admin | Phân quyền vai trò | POST | /users/role | Cập nhật vai trò quản trị (Admin, Landlord, Manager, Receptionist, Housekeeper, Resident, Guest) |
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
| role | VARCHAR(50) | Tên vai trò: admin, landlord, unverified_landlord, manager, receptionist, housekeeper, resident, guest |
| created_at | TIMESTAMP, NULL | Thời điểm tạo tài khoản |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |

Bảng 6: Mô tả cấu trúc bảng Roles (Vai trò và phân quyền 8 Roles)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| name | VARCHAR(100) | Tên hiển thị vai trò (Ví dụ: Chủ trọ, Quản lý, Lễ tân, Buồng phòng, Cư dân) |
| slug | VARCHAR(50), UNIQUE | Mã định danh vai trò: admin, landlord, unverified_landlord, manager, receptionist, housekeeper, resident, guest |
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
| description | TEXT, NULL | Thông tin mô tả đặc điểm, vị trí và dịch vụ của cơ sở |
| property_type | VARCHAR(30), DEFAULT 'boarding' | Phân loại mô hình cơ sở lưu trú: boarding (nhà trọ), apartment (chung cư mini), hotel (khách sạn) |
| checkin_time | TIME, DEFAULT '14:00:00' | Giờ quy chuẩn nhận phòng tiêu chuẩn khách sạn |
| checkout_time | TIME, DEFAULT '12:00:00' | Giờ quy chuẩn trả phòng tiêu chuẩn khách sạn |
| phone | VARCHAR(50), NULL | Số điện thoại hotline / liên hệ quản lý cơ sở lưu trú |
| status | VARCHAR(30), DEFAULT 'active' | Trạng thái hoạt động: active (hoạt động), maintenance (bảo trì), inactive (tạm ngưng) |
| image | VARCHAR(255), NULL | Đường dẫn ảnh đại diện tòa nhà / cơ sở lưu trú |
| amenities | JSON, NULL | Mảng JSON lưu các tiện ích chung: thang máy, camera, bảo vệ 24/7, hầm để xe, PCCC... |
| created_at | TIMESTAMP, NULL | Thời điểm tạo bản ghi |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |
| deleted_at | TIMESTAMP, NULL | Thời điểm xóa mềm cơ sở lưu trú (phục vụ SoftDeletes) |


### c. Bảng Rooms & Condos (Phòng trọ, Căn hộ chung cư, Phòng khách sạn & Minibar)

Quản lý chi tiết từng căn phòng trọ hoặc căn hộ chung cư: số phòng/mã căn (P.101, Căn 12A.03), phân loại phòng (Studio, 1PN, 2PN, 3PN, Deluxe, VIP), hình thức thuê linh hoạt (theo tháng cho trọ/chung cư, theo ngày hoặc theo giờ cho khách sạn), đa khung giá (giá tháng, giá ngày/đêm, giá giờ đầu và giờ phụ trội), trạng thái phòng (trống, đang ở, nợ cước, đang dọn dẹp, bảo trì), trạng thái vệ sinh buồng phòng (clean, dirty, cleaning, inspected), danh mục tiện ích (WC, ban công, thang máy, thẻ từ) và danh mục tài sản/minibar bàn giao.

Bảng 8: Mô tả cấu trúc bảng Rooms & Condos (Phòng lưu trú, Căn hộ chung cư & Minibar)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| tenant_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng tenants(id) |
| building_id | BIGINT UNSIGNED, NULL | Khóa ngoại tham chiếu bảng buildings(id) |
| room_number | VARCHAR(50) | Mã hoặc số phòng (Ví dụ: P.101, Phòng Deluxe 202, VIP Suite) |
| floor | INT | Tầng mà phòng trọ đang tọa lạc |
| price | DECIMAL(12,2) | Giá thuê phòng theo tháng (VNĐ) |
| price_per_day | DECIMAL(12,2), NULL | Giá thuê phòng theo ngày / đêm khách sạn (VNĐ) |
| price_per_hour | DECIMAL(12,2), NULL | Giá thuê phòng block 2 giờ đầu khách sạn (VNĐ) |
| price_extra_hour | DECIMAL(12,2), NULL | Giá phụ trội mỗi giờ tiếp theo khi thuê theo giờ (VNĐ) |
| area | INT | Diện tích sử dụng của phòng (m2) |
| status | VARCHAR(30) | Trạng thái phòng: Trống (empty), Đang ở (occupied), Nợ cước (overdue), Đang dọn dẹp (cleaning), Bảo trì (maintenance) |
| cleaning_status | VARCHAR(30) | Trạng thái vệ sinh buồng phòng: Sạch (clean), Bẩn cần dọn (dirty), Đang dọn (cleaning), Đã kiểm tra (inspected) |
| amenities | JSON, NULL | Mảng JSON lưu các tiện ích: WC khép kín, ban công, gác lửng, minibar, khóa từ, thú cưng |
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

Bảng 10b: Mô tả cấu trúc bảng HotelBookings (Phiếu đặt phòng & Lưu trú khách sạn ngắn hạn)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| tenant_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng tenants(id) |
| room_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng rooms(id) |
| booking_code | VARCHAR(30), UNIQUE | Mã phiếu đặt phòng duy nhất (Ví dụ: HB-RENTY01) |
| guest_name | VARCHAR(255) | Họ và tên khách lưu trú |
| guest_phone | TEXT, NULL | Số điện thoại liên hệ (Mã hóa AES-256-GCM) |
| guest_cccd | TEXT, NULL | Số Căn cước công dân khách (Mã hóa AES-256-GCM) |
| rental_type | VARCHAR(20), DEFAULT 'day' | Hình thức thuê phòng: day (theo ngày/đêm), hour (theo giờ) |
| check_in_at | TIMESTAMP | Thời điểm nhận phòng thực tế |
| expected_check_out_at | TIMESTAMP, NULL | Thời điểm dự kiến trả phòng |
| actual_check_out_at | TIMESTAMP, NULL | Thời điểm thực tế trả phòng (Check-out) |
| unit_rate | DECIMAL(12,2) | Đơn giá phòng áp dụng theo giờ hoặc ngày |
| room_amount | DECIMAL(12,2) | Tổng tiền phòng sau tính toán thời gian lưu trú |
| service_amount | DECIMAL(12,2) | Tổng tiền dịch vụ minibar và tiện ích phòng |
| surcharge_amount | DECIMAL(12,2) | Tiền phụ thu trả phòng muộn hoặc nhận phòng sớm |
| deposit_amount | DECIMAL(12,2) | Tiền đặt cọc giữ phòng của khách |
| total_amount | DECIMAL(12,2) | Tổng số tiền thanh toán cuối cùng trên hóa đơn Folio |
| payment_status | VARCHAR(20), DEFAULT 'unpaid' | Trạng thái thanh toán: unpaid (chưa thanh toán), paid (đã thanh toán) |
| payment_method | VARCHAR(30), NULL | Phương thức thanh toán: cash (tiền mặt), vietqr, transfer |
| status | VARCHAR(20), DEFAULT 'checked_in' | Trạng thái lượt ở: checked_in (đang ở), checked_out (đã trả phòng), cancelled |
| note | TEXT, NULL | Ghi chú yêu cầu đặc biệt của khách |
| created_at | TIMESTAMP, NULL | Thời điểm tạo bản ghi |
| updated_at | TIMESTAMP, NULL | Thời điểm cập nhật gần nhất |

Bảng 10c: Mô tả cấu trúc bảng HotelFolioItems (Chi tiết Bảng kê Dịch vụ & Minibar Khách sạn)

| Tên Trường | Kiểu Dữ Liệu | Mô Tả |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Khóa chính, tự động tăng |
| booking_id | BIGINT UNSIGNED | Khóa ngoại tham chiếu bảng hotel_bookings(id) |
| item_name | VARCHAR(255) | Tên sản phẩm/dịch vụ (Bia Heineken, Nước suối, Snack, Giặt ủi...) |
| item_type | VARCHAR(30), DEFAULT 'minibar' | Phân loại: minibar, service (dịch vụ), surcharge (phụ thu) |
| quantity | INT, DEFAULT 1 | Số lượng tiêu thụ |
| unit_price | DECIMAL(12,2) | Đơn giá niêm yết của dịch vụ / sản phẩm minibar (VNĐ) |
| subtotal | DECIMAL(12,2) | Thành tiền chi tiết = Số lượng * Đơn giá (VNĐ) |
| created_at | TIMESTAMP, NULL | Thời điểm thêm dịch vụ |
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
| category | ENUM | Danh mục phân loại: electric, water, furniture, maintenance, housekeeping, other |
| specific_location | VARCHAR(255), NULL | Vị trí chi tiết hỏng hóc trong phòng (VD: bồn rửa mặt, ban công, góc bếp) |
| image_path | VARCHAR(255), NULL | Đường dẫn ảnh chụp hiện trạng đính kèm (giới hạn tối đa 10MB) |
| priority | ENUM | Mức độ khẩn cấp (AI phân tích): low, medium, high |
| suggestion | TEXT, NULL | Gợi ý biện pháp khắc phục nhanh an toàn từ AI |
| assigned_to | VARCHAR(255), NULL | Nhân viên kỹ thuật / thợ sửa chữa được phân công phụ trách |
| status | ENUM | Trạng thái xử lý: pending (Chờ tiếp nhận), processing (Đang sửa), resolved (Đã xong) |
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


# IV. THIẾT KẾ GIAO DIỆN DEMO VÀ KỊCH BẢN XỬ LÝ LỖI (UI/UX)

Quy ước chung khi thiết kế giao diện và xử lý lỗi người dùng:

Note: Đối với những input khi lỗi input sẽ bôi đỏ viền ô input (border-red-500) và xuất hiện thông báo lỗi chi tiết màu đỏ ngay phía dưới mỗi ô input. Khi thao tác thành công, hệ thống hiển thị Toast/Alert thông báo xanh (bg-emerald-500) tự động ẩn sau 3 giây.

A. CÁC MODULE & CHỨC NĂNG DO NGUYỄN THANH HIỀN (NHÓM TRƯỞNG) PHỤ TRÁCH

1. Khởi tạo kiến trúc dự án Laravel 11, thiết lập Docker, Git và thiết kế 35 bản ghi Database Migrations

Mô tả chi tiết chức năng: Xây dựng kiến trúc dự án trên framework Laravel 11, cấu hình môi trường Docker (Nginx, PHP 8.3, MySQL 8.0, Redis Cache) và quy trình Git workflow. Thiết kế và thực thi 35 bản ghi migration định nghĩa toàn bộ mô hình dữ liệu lõi cho hệ sinh thái quản lý lưu trú đa mô hình (Properties, Rooms, Users, Contracts, UtilityReadings, Bills, Tickets, AuditLogs...).

Bảng: Kịch bản xử lý lỗi Khởi tạo hệ thống và Database Migrations

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Lỗi kết nối CSDL khi chạy Migration | Màn hình bôi đỏ: "Lỗi kết nối CSDL: SQLSTATE[HY000] [2002] Connection refused. Vui lòng kiểm tra file .env và container MySQL". |
| Trùng lặp bảng hoặc khóa ngoại sai thứ tự | Dừng tiến trình, hiển thị mã lỗi ForeignKeyConstraintViolationException và tự động rollback giao dịch. |

2. Xây dựng Middleware phân quyền truy cập đa tầng RBAC & Multi-tenancy phân lập dữ liệu

Mô tả chi tiết chức năng: Thiết lập Middleware kiểm soát an ninh đa tầng theo vai trò RBAC (Superadmin, Landlord, Resident, Guest). Tích hợp cơ chế Multi-tenancy tự động lọc phạm vi truy vấn dữ liệu theo landlord_id và property_id, ngăn chặn triệt để nguy cơ rò rỉ dữ liệu chéo giữa các chủ cơ sở lưu trú khác nhau.

Bảng: Kịch bản xử lý lỗi Phân quyền truy cập đa tầng và Multi-tenancy

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Truy cập URL vượt quyền hạn (VD: Landlord vào /admin/audit-logs) | Hệ thống chặn ngay lập tức, hiển thị trang 403 Forbidden kèm nút quay lại trang chủ quản trị an toàn. |
| Cố tình sửa tham số property_id trên URL để xem cơ sở khác | Hệ thống trả về mã lỗi 404 Not Found hoặc thông báo "Cơ sở lưu trú này không thuộc quyền quản lý của bạn". |

3. Lập trình module Đăng ký & Đăng nhập truyền thống kèm cơ chế Rate Limiting chống brute-force

Mô tả chi tiết chức năng: Cung cấp giao diện đăng nhập bằng Username/Số điện thoại và Mật khẩu. Tích hợp cơ chế Rate Limiting tự động khóa form sau 5 lần nhập sai liên tiếp trong 60 giây nhằm vô hiệu hóa các cuộc tấn công brute-force tự động dò mật khẩu.

Hình 2: Quy ước hiển thị ô input lỗi và thông báo trạng thái giao diện

Hình 3: Giao diện Trang Đăng nhập & Đăng ký (WebAuthn Passkey + Mật khẩu)

Bảng 21: Kịch bản xử lý lỗi Trang Đăng nhập & Đăng ký (WebAuthn Passkey + Mật khẩu)

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Bỏ trống cả Tên đăng nhập và Mật khẩu | Viền 2 ô input đổi sang màu đỏ. Hiển thị thông báo: "Vui lòng nhập tên đăng nhập hoặc số điện thoại" và "Vui lòng nhập mật khẩu". |
| Số điện thoại không đúng định dạng (< 10 số hoặc chứa chữ) | Ô SĐT bôi đỏ. Hiển thị: "Số điện thoại không hợp lệ, vui lòng nhập đúng 10 số di động Việt Nam". |
| Sai thông tin đăng nhập (Username hoặc Password không khớp) | Hiển thị thông báo lỗi trên cùng form: "Thông tin đăng nhập hoặc mật khẩu không chính xác. Vui lòng kiểm tra lại!". |
| Tài khoản bị khóa hoặc vô hiệu hóa bởi Admin | Thông báo lỗi màu đỏ: "Tài khoản của bạn đã bị tạm khóa do vi phạm tiêu chuẩn cộng đồng. Vui lòng liên hệ hỗ trợ". |
| Nhập sai mật khẩu liên tiếp quá 5 lần | Khóa tạm thời form đăng nhập 60 giây: "Bạn đã thao tác sai quá nhiều lần. Vui lòng đợi sau 60 giây". |
| Mất kết nối mạng / Lỗi máy chủ (HTTP 500) | Hiển thị Toast lỗi hệ thống: "Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối internet và thử lại sau ít phút". |

4. Tích hợp chuẩn xác thực không mật khẩu WebAuthn / FIDO2 Passkey sinh trắc học

Mô tả chi tiết chức năng: Cho phép người dùng tạo cặp khóa bất đối xứng và xác thực không mật khẩu (Passkey) thông qua vân tay, FaceID hoặc mã PIN thiết bị (Windows Hello). Giúp nâng cao trải nghiệm đăng nhập siêu tốc chỉ với một chạm và bảo mật tuyệt đối trước các thủ đoạn lừa đảo Phishing.

Bảng: Kịch bản xử lý lỗi Xác thực sinh trắc học WebAuthn Passkey

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Thiết bị hoặc trình duyệt chưa hỗ trợ WebAuthn | Popup cảnh báo: "Không tìm thấy thiết bị xác thực WebAuthn hoặc trình duyệt chưa hỗ trợ. Vui lòng sử dụng mật khẩu". |
| Người dùng bấm Hủy (Cancel) trên popup sinh trắc học | Toast vàng nhẹ: "Thao tác quét vân tay/Passkey đã bị hủy. Bạn có thể thử lại hoặc nhập mật khẩu truyền thống". |

5. Hiện thực giải pháp mã hóa dữ liệu nhạy cảm PII bằng AES-256-GCM kết hợp HMAC Blind Index

Mô tả chi tiết chức năng: Bảo vệ tuyệt đối thông tin định danh cá nhân PII của cư dân và chủ cơ sở (CCCD, Số tài khoản ngân hàng, Số điện thoại) bằng thuật toán mã hóa đối xứng quân sự AES-256-GCM. Xây dựng chỉ mục ẩn HMAC-SHA256 Blind Index phục vụ tìm kiếm chính xác mà không cần giải mã toàn bộ cơ sở dữ liệu.

Bảng: Kịch bản xử lý lỗi Bảo mật dữ liệu cá nhân PII và Giải mã

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Nhập sai mật khẩu cấp 2 khi bấm mở xem số CCCD | Ô mật khẩu bôi đỏ: "Mật khẩu xác thực cấp 2 không chính xác. Quyền xem thông tin bị từ chối". |
| Lỗi giải mã do khóa mã hóa hệ thống bị thay đổi | Dữ liệu hiển thị dạng mặt nạ lỗi [DECRYPTION_ERROR] và tự động kích hoạt cảnh báo an ninh gửi Superadmin. |

6 & 7. Quản lý Hợp đồng thuê phòng & Ký số điện tử online bằng HTML5 Canvas Signature Pad

Mô tả chi tiết chức năng: Quản lý toàn diện vòng đời hợp đồng: từ hợp đồng thuê dài hạn theo tháng (nhà trọ, căn hộ) đến các phiếu đặt phòng ngắn hạn theo ngày/giờ (khách sạn). Khách thuê truy cập link ký số, đọc toàn văn điều khoản, xác thực mã OTP bảo mật và ký tay trực tiếp lên khung cảm ứng HTML5 Canvas. Chữ ký được nhúng thẳng vào file PDF hợp đồng có giá trị chứng thực.

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Ký số hợp đồng trực tuyến HTML5 Canvas Pad

Hình 8: Giao diện Ký số hợp đồng điện tử online bằng Canvas Signature Pad

Hình 9: Giao diện Xuất file PDF Hợp đồng thuê phòng có chữ ký số hai bên

Bảng 24: Kịch bản xử lý lỗi Trang Quản lý Hợp đồng thuê & Ký số Canvas

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Ngày kết thúc hợp đồng sớm hơn hoặc trùng với ngày bắt đầu | Ô ngày kết thúc bôi đỏ. Thông báo: "Ngày kết thúc hợp đồng phải sau ngày bắt đầu ít nhất 1 tháng". |
| Số tiền đặt cọc giữ phòng nhập giá trị âm | Ô tiền cọc bôi đỏ. Hiển thị: "Tiền cọc phòng phải lớn hơn hoặc bằng 0 VNĐ". |
| Cư dân bấm Xác nhận ký khi chưa vẽ chữ ký vào khung Canvas | Khung viền Canvas rung nhẹ và đổi màu đỏ. Thông báo: "Vui lòng vẽ chữ ký tay của bạn vào khung trước khi xác nhận ký kết". |
| Nhập sai mã OTP xác thực ký số hoặc mã OTP đã hết hạn 5 phút | Ô nhập OTP bôi đỏ. Hiển thị: "Mã xác thực OTP không chính xác hoặc đã hết thời gian hiệu lực. Vui lòng bấm gửi lại mã mới". |
| Hợp đồng đã được ký trước đó nhưng người dùng bấm nút Ký lại | Thông báo: "Hợp đồng này đã được hoàn tất ký số trước đó. Bạn không thể thực hiện ký lại". |

8. Xây dựng quy trình Xác thực định danh chủ trọ lũy tiến (KYC CCCD & Premium Tích xanh PCCC/ANTT)

Mô tả chi tiết chức năng: Quy trình xác thực lũy tiến nâng cao uy tín cho cơ sở lưu trú: Cấp 1 (Chủ trọ gửi ảnh CCCD 2 mặt để kích hoạt nhận tiền qua cổng VietQR) và Cấp 2 (Nộp Giấy phép phòng cháy chữa cháy PCCC và Cam kết an ninh trật tự để nhận tích xanh kiểm định uy tín hiển thị nổi bật trên bản đồ tìm phòng).

Bảng: Kịch bản xử lý lỗi Quy trình Nộp hồ sơ định danh KYC

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Chưa tải đủ cả 2 mặt CCCD khi bấm gửi duyệt | Vùng upload bôi đỏ: "Vui lòng tải lên đầy đủ ảnh chụp cả hai mặt trước và sau của CCCD". |
| Tải file tài liệu vượt quá giới hạn dung lượng (> 10MB) | Thông báo lỗi: "Dung lượng file tải lên quá lớn (tối đa 10MB). Vui lòng nén file trước khi gửi". |

9. Xây dựng Bảng điều khiển kiểm duyệt hồ sơ chủ trọ (Superadmin)

Mô tả chi tiết chức năng: Giao diện kiểm duyệt bảo mật dành cho Superadmin: xem tài liệu định danh của chủ trọ qua đường dẫn tạm thời Signed URL (hết hạn sau 5 phút kèm đóng dấu Watermark chống rò rỉ). Nút Phê duyệt và nút Từ chối (bắt buộc nhập lý do chi tiết để thông báo cho chủ cơ sở chỉnh sửa).

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Bảng điều khiển kiểm duyệt hồ sơ Chủ trọ (Admin Verification)

Hình 16: Giao diện Kiểm duyệt Hồ sơ Định danh Chủ trọ (Admin Verification)

Bảng 29: Kịch bản xử lý lỗi Trang Kiểm duyệt Hồ sơ Định danh Chủ trọ (Admin Verification)

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Admin bấm nút Từ chối hồ sơ xác minh nhưng bỏ trống lý do từ chối | Ô lý do từ chối bôi đỏ viền. Thông báo: "Vui lòng nhập lý do từ chối để chủ cơ sở biết và bổ sung lại giấy tờ hợp lệ". |
| Truy cập tài liệu bảo mật khi link ký đã quá thời gian hiệu lực (TTL > 5 phút) | Trang hiển thị lỗi 403 Forbidden: "Liên kết xem tài liệu đã hết hạn vì lý do an toàn bảo mật. Vui lòng bấm làm mới trang để nhận liên kết mới". |
| Tài khoản không có quyền admin cố tình truy cập vào URL /admin/verifications | Hệ thống chặn và trả về lỗi 403: "Truy cập bị từ chối. Bạn không có quyền hạn quản trị viên để thực hiện thao tác này". |
| Mở khóa tài liệu nhạy cảm sau khi đã duyệt mà không nhập lý do nghiệp vụ | Ô lý do bôi đỏ. Hiển thị: "Quy định bảo mật: Bạn bắt buộc phải ghi rõ lý do nghiệp vụ để lưu vào nhật ký kiểm toán Audit Log trước khi mở khóa tài liệu". |

10. Xây dựng hệ thống Nhật ký kiểm toán bất biến (Immutable Audit Logs)

Mô tả chi tiết chức năng: Cơ chế ghi nhật ký hệ thống tự động ghi nhận mọi thao tác nhạy cảm (xem số CCCD, sửa đổi giá thuê phòng, duyệt hồ sơ KYC, xóa dữ liệu). Bảng log áp dụng chính sách ghi một lần (Append-only) và cấm triệt để quyền UPDATE/DELETE để đảm bảo tính toàn vẹn phục vụ công tác thanh tra.

Hình 17: Giao diện Nhật ký kiểm toán bất biến (Immutable Audit Logs)

Bảng: Kịch bản xử lý lỗi Nhật ký kiểm toán bất biến Audit Logs

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Người dùng can thiệp gọi API để sửa hoặc xóa bản ghi log | Hệ thống chặn lập tức với mã lỗi 405 Method Not Allowed: "Nhật ký kiểm toán là bất biến, nghiêm cấm mọi hành vi sửa/xóa dữ liệu!". |

11. Tích hợp Google Gemini AI tự động phân tích và sinh điều khoản hợp đồng thuê phòng chuẩn pháp lý

Mô tả chi tiết chức năng: Ứng dụng mô hình ngôn ngữ lớn Google Gemini AI để hỗ trợ chủ cơ sở: chỉ cần nhập các ý tưởng hoặc yêu cầu quản lý thực tế (cho nuôi thú cưng, giữ xe điện, giờ đóng cổng), AI tự động tạo văn bản điều khoản pháp lý chặt chẽ, đúng quy chuẩn pháp luật thuê nhà tại Việt Nam.

Bảng: Kịch bản xử lý lỗi Trợ lý AI sinh điều khoản hợp đồng

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Bỏ trống yêu cầu khi gọi AI sinh điều khoản | Ô nhập bôi đỏ: "Vui lòng nhập ít nhất một yêu cầu quy định sinh hoạt để AI phân tích". |
| Mất kết nối mạng đến Google Gemini API | Toast cảnh báo: "Không thể kết nối đến máy chủ AI. Hệ thống tạm thời nạp mẫu điều khoản tiêu chuẩn có sẵn". |

12. Xây dựng quy trình Onboarding đăng ký nhanh cho chủ trọ mới

Mô tả chi tiết chức năng: Quy trình hướng dẫn từng bước (Step Wizard) giúp chủ trọ mới thiết lập cơ sở lưu trú chỉ trong 3 phút: Bước 1 (Tên cơ sở, địa chỉ, loại hình), Bước 2 (Số lượng tầng, cấu hình biểu giá điện nước và dịch vụ), Bước 3 (Khởi tạo phòng tự động và bàn giao quyền quản trị).

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Thiết lập cơ sở lưu trú ban đầu (Step Wizard)

Hình 4: Giao diện Onboarding thiết lập cơ sở lưu trú ban đầu cho Chủ trọ mới

Bảng: Kịch bản xử lý lỗi Quy trình Onboarding Chủ trọ mới

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Bỏ trống Tên cơ sở hoặc Địa chỉ tòa nhà | Các ô input bắt buộc bôi đỏ: "Vui lòng nhập tên cơ sở lưu trú" và "Địa chỉ không được để trống". |
| Số tầng hoặc số phòng dự kiến nhập số ≤ 0 | Báo lỗi: "Số tầng và số phòng dự kiến phải là số nguyên dương lớn hơn 0". |

B. CÁC MODULE & CHỨC NĂNG DO NGUYỄN ANH QUÝ (NHÓM PHÓ) PHỤ TRÁCH

1. Lập trình module CRUD Quản lý Cơ sở lưu trú (Properties/Hotels)

Mô tả chi tiết chức năng: Module cho phép chủ cơ sở và quản trị viên thêm mới, cập nhật thông tin tòa nhà, khách sạn; quản lý số điện thoại liên hệ, tải lên ảnh đại diện tòa nhà, chọn danh mục tiện ích chung (thang máy, bảo vệ 24/7, camera an ninh, hầm giữ xe, hệ thống PCCC...), thiết lập trạng thái hoạt động (Hoạt động - active, Bảo trì - maintenance, Tạm ngưng - inactive), cài đặt số tầng (total_floors), mốc giờ Check-in/Check-out tiêu chuẩn và cấu hình bảng giá điện, nước, phí quản lý cơ sở. Áp dụng cơ chế SoftDeletes (xóa mềm) kết hợp Guard Check an toàn tuyệt đối: tự động kiểm tra và ngăn chặn hành vi xóa cơ sở lưu trú nếu vẫn còn phòng trọ trực thuộc.

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Quản lý và thiết lập Cơ sở lưu trú (Properties/Hotels)

Bảng: Kịch bản xử lý lỗi Quản lý Cơ sở lưu trú (Properties/Hotels)

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Bỏ trống tên cơ sở lưu trú khi tạo mới | Ô tên cơ sở bôi đỏ: "Tên cơ sở lưu trú không được để trống". |
| Giờ Check-in sớm hơn hoặc trùng với giờ Check-out | Báo lỗi logic: "Giờ Check-in (14:00) phải sau giờ Check-out tiêu chuẩn (12:00)". |
| Đơn giá điện hoặc nước nhập số âm | Ô đơn giá bôi đỏ: "Đơn giá dịch vụ phải lớn hơn hoặc bằng 0 VNĐ". |
| Số tầng nhập không hợp lệ (nhỏ hơn 1 hoặc lớn hơn 100) | Ô số tầng bôi đỏ: "Số tầng tối thiểu là 1 và tối đa 100". |
| Tải lên ảnh đại diện sai định dạng hoặc vượt quá dung lượng cho phép (> 5MB) | Vùng upload ảnh bôi đỏ: "File tải lên phải là hình ảnh hợp lệ (jpg, png, webp) và dung lượng không quá 5MB". |
| Xóa cơ sở lưu trú khi vẫn còn phòng trực thuộc (Guard Check an toàn) | Modal cảnh báo chặn thao tác: "Không thể xóa cơ sở vì vẫn còn X phòng trọ trực thuộc. Vui lòng chuyển hoặc xóa các phòng trước". |

2. Lập trình module CRUD Quản lý Phòng lưu trú (Rooms)

Mô tả chi tiết chức năng: Quản lý danh sách chi tiết các phòng trong cơ sở: phân loại theo tầng, phân hạng phòng chuẩn hóa (Standard, Deluxe, VIP, Studio), hình thức cho thuê linh hoạt (Theo tháng - month, Theo ngày - day, Theo giờ - hour), cấu hình diện tích, đơn giá thuê, thiết lập tiền đặt cọc giữ phòng (deposit), mô tả chi tiết phòng, cơ chế khóa lạc quan (Optimistic Locking với trường version) và danh mục tiện ích phòng (Máy lạnh, Nóng lạnh, Minibar, SmartLock, Ban công). Hỗ trợ tải lên cùng lúc tối đa 10 ảnh thực tế và 1 video không gian phòng.

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Thêm mới và cập nhật thông tin phòng lưu trú

Hình 7: Giao diện Form Thêm / Cập nhật thông tin phòng lưu trú đa mô hình

Bảng 23: Kịch bản xử lý lỗi Trang Quản lý Danh sách phòng & Form Phòng

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Bỏ trống Số phòng hoặc Diện tích hoặc Giá thuê | Các ô input bắt buộc bôi đỏ viền. Dưới mỗi ô hiện: "Số phòng không được để trống", "Vui lòng nhập giá phòng hợp lệ". |
| Nhập giá phòng hoặc diện tích là số âm hoặc bằng 0 | Ô input bôi đỏ. Hiển thị thông báo: "Giá thuê và diện tích phải là số nguyên dương lớn hơn 0". |
| Số phòng bị trùng lặp trong cùng một tòa nhà (Ví dụ đã có P.101) | Ô số phòng bôi đỏ. Thông báo lỗi: "Số phòng P.101 đã tồn tại trong tòa nhà này. Vui lòng chọn số phòng khác". |
| Tải lên tệp tin hình ảnh sai định dạng (.exe, .pdf thay vì .jpg, .png) | Vùng upload ảnh bôi đỏ. Hiển thị: "Định dạng tệp tin không hợp lệ. Hệ thống chỉ chấp nhận ảnh .jpg, .jpeg, .png, .webp". |
| Dung lượng file ảnh hoặc video vượt quá giới hạn (Ảnh > 5MB, Video > 30MB) | Thông báo lỗi: "Dung lượng tệp vượt quá kích thước cho phép. Vui lòng nén file hoặc chọn tệp nhỏ hơn". |
| Chưa chọn hoặc chọn sai Hạng phòng (room_type) | Ô chọn hạng phòng bôi đỏ viền: "Hạng phòng phải là Standard, Deluxe, VIP hoặc Studio". |
| Chưa chọn hoặc chọn sai Hình thức cho thuê (rental_type) | Ô chọn hình thức thuê bôi đỏ viền: "Hình thức cho thuê phải là theo tháng, theo ngày hoặc theo giờ". |
| Nhập tiền đặt cọc giữ phòng là số âm (deposit < 0) | Ô tiền cọc bôi đỏ viền: "Tiền cọc không được là số âm (tối thiểu 0 VNĐ)". |

3. Thiết kế & phát triển Sơ đồ ma trận phòng trực quan (Visual Room Matrix) theo tầng

Mô tả chi tiết chức năng: Giao diện sơ đồ buồng phòng ma trận chia theo tầng chuẩn công nghiệp khách sạn: mỗi phòng là một thẻ trực quan với mã màu trạng thái thời gian thực: Xanh lá (Phòng trống), Đỏ (Đang ở), Cam (Cần dọn dẹp - Cleaning/Housekeeping), Vàng (Đang nợ cước), Xám (Đang bảo trì). Hỗ trợ đổi trạng thái dọn buồng một chạm.

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Sơ đồ Ma trận phòng trực quan theo tầng (Visual Room Matrix)

Hình 6: Giao diện Sơ đồ Ma trận phòng lưu trú (Visual Room Matrix) theo tầng

Bảng: Kịch bản xử lý lỗi Sơ đồ Ma trận phòng trực quan

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Bấm nút Xóa phòng đang có cư dân thuê (Status = occupied) | Modal cảnh báo nguy hiểm: "Không thể xóa phòng đang có người ở! Bạn phải làm thủ tục trả phòng cho cư dân trước khi xóa". |
| Bấm Xóa phòng trống (Hành động hợp lệ) | Modal Popup xác nhận: "Bạn có chắc chắn muốn xóa vĩnh viễn phòng này? Thao tác này không thể khôi phục!" (Nút Xác nhận đỏ / Hủy bỏ). |
| Chuyển trạng thái sang Đang ở khi chưa lập Hợp đồng | Toast cảnh báo: "Không thể chuyển trạng thái thủ công sang Đang ở. Vui lòng tạo Hợp đồng hoặc Phiếu booking trước". |

4 & 5. Chốt số Điện - Nước định kỳ hàng tháng & AI Vision OCR nhận diện công tơ từ camera

Mô tả chi tiết chức năng: Bảng chốt số tiện ích tập trung cuối tháng: tự động nạp chỉ số cũ của tháng trước (khóa không cho sửa), ô nhập chỉ số mới của tháng này, tự động tính chênh lệch sản lượng tiêu thụ và thành tiền tương ứng. Tích hợp AI thị giác: người ghi số chụp ảnh đồng hồ điện/nước, Google Gemini Vision API tự động phân tích và điền chỉ số vào ô tương ứng.

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Chốt số Điện - Nước định kỳ & AI Vision OCR Camera

Hình 10: Giao diện Chốt số Điện - Nước định kỳ & AI OCR Camera nhận diện công tơ

Bảng 25: Kịch bản xử lý lỗi Trang Ghi chỉ số Điện - Nước định kỳ & AI OCR Camera

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Nhập chỉ số điện/nước mới nhỏ hơn chỉ số cũ tháng trước | Ô chỉ số mới bôi đỏ. Thông báo lỗi: "Chỉ số mới không được nhỏ hơn chỉ số cũ tháng trước (Chỉ số cũ: 150 kWh)". |
| Nhập ký tự chữ cái hoặc ký hiệu đặc biệt vào ô chỉ số | Ô input bôi đỏ. Hiển thị: "Chỉ số công tơ phải là số nguyên dương hợp lệ". |
| Mức tiêu thụ tăng đột biến bất thường (Ví dụ dùng hơn 1000 số điện/tháng) | Hiển thị cảnh báo vàng (Warning): "Lượng điện tiêu thụ tăng đột biến (+1200 kWh). Vui lòng kiểm tra lại công tơ xem có bị nhầm số không!". |
| Ảnh chụp đồng hồ quá mờ hoặc bị lóa sáng khiến AI không đọc được số | Toast thông báo: "AI không nhận diện rõ số trên mặt đồng hồ do ảnh mờ/thiếu sáng. Vui lòng chụp lại rõ nét hoặc tự nhập tay". |
| Lưu bản ghi chốt số của phòng đã được chốt trong tháng đó rồi | Thông báo: "Hóa đơn điện nước tháng này của phòng đã được lập. Bạn chỉ có thể cập nhật chỉnh sửa lại bản ghi cũ". |

6 & 7. Bộ máy tính toán cước tự động & Xuất hóa đơn tháng / Bảng kê Folio PDF kèm mã VietQR Check-out

Mô tả chi tiết chức năng: Động cơ tính toán tài chính tự động tổng hợp tiền phòng, tiền điện nước (từ chốt số AI), phí dịch vụ chung và phụ thu tiêu hao đồ uống minibar khách sạn. Sử dụng DomPDF kết xuất phiếu tính tiền chuẩn khổ giấy A4/A5, tự động sinh mã VietQR động NAPAS chứa chính xác số tiền cần thu và nội dung chuyển khoản để khách thanh toán tức thời.

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Bảng kê thanh toán Folio & Hóa đơn VietQR

Hình 11: Giao diện Quản lý Thanh toán & Xuất hóa đơn / Bảng kê Folio VietQR

Bảng 26: Kịch bản xử lý lỗi Trang Quản lý Thanh toán, Xuất hóa đơn VietQR & Bảng kê Folio

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Bấm xuất mã VietQR khi Chủ trọ chưa hoàn tất xác minh KYC | Modal chặn: "Bạn cần hoàn tất xác minh định danh KYC (CCCD & STK Ngân hàng) trước khi sử dụng tính năng nhận tiền online qua VietQR". |
| Chủ trọ chưa cập nhật thông tin Số tài khoản hoặc Tên ngân hàng nhận tiền | Thông báo lỗi: "Chưa cấu hình tài khoản ngân hàng thụ hưởng. Vui lòng vào Cài đặt để bổ sung thông tin thanh toán". |
| Lỗi mạng khi gọi API VietQR sinh ảnh mã QR | Hiển thị khung thông báo dự phòng: "Không tải được ảnh mã QR từ cổng VietQR. Vui lòng chuyển khoản theo thông tin STK bên dưới". |

8. Quét nợ tự động và gửi tin nhắn nhắc tiền phòng kèm link VietQR qua Zalo/SMS

Mô tả chi tiết chức năng: Hệ thống tự động theo dõi thời hạn thanh toán sau chu kỳ thu tiền, lọc danh sách các phòng trễ hẹn nợ cước và hỗ trợ chức năng gửi tin nhắn nhắc nợ một chạm kèm link mở hóa đơn VietQR thanh toán nhanh qua mạng xã hội Zalo hoặc SMS.

Bảng: Kịch bản xử lý lỗi Quét nợ và Gửi nhắc nợ Zalo/SMS

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Gửi tin nhắc nợ tự động cho phòng không có số điện thoại cư dân | Báo lỗi trên danh sách: "Không thể gửi tin nhắc nợ đến Phòng 201: Cư dân chưa cập nhật số điện thoại liên lạc". |
| Gửi tin nhắc nợ nhiều lần liên tục trong ngày | Popup cảnh báo: "Phòng này đã được gửi tin nhắc nợ hôm nay lúc 08:30. Bạn có chắc chắn muốn gửi tiếp?". |

9. Lập trình module Quản lý Trang thiết bị - Tài sản phòng trọ (Equipment)

Mô tả chi tiết chức năng: Theo dõi và quản lý danh mục tài sản, trang thiết bị vật tư của cơ sở lưu trú (Máy lạnh, Giường, Tủ, Tivi, Nệm, Tủ lạnh mini): thống kê số lượng tồn kho, quy trình bàn giao vào phòng và lập biên bản ghi nhận hỏng hóc để khấu trừ tiền cọc khi trả phòng.

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Quản lý Trang thiết bị & Hàng hóa Minibar

Hình 18: Giao diện Quản lý Tài sản - Trang thiết bị & Minibar lưu trú

Bảng 30: Kịch bản xử lý lỗi Trang Quản lý Tài sản - Trang thiết bị & Minibar (Asset & Minibar Management)

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Bỏ trống Tên thiết bị hoặc Mã thiết bị khi thêm mới | Các ô bắt buộc bôi đỏ. Hiển thị: "Vui lòng nhập tên trang thiết bị" và "Mã thiết bị không được để trống". |
| Nhập mã thiết bị đã tồn tại trong kho của cùng chủ trọ | Ô mã thiết bị bôi đỏ: "Mã thiết bị này đã tồn tại trong danh mục. Vui lòng chọn mã khác". |
| Bàn giao thiết bị vào phòng với số lượng vượt quá tồn kho thực tế | Ô số lượng bôi đỏ. Thông báo: "Số lượng thiết bị trong kho không đủ để bàn giao (Tồn kho hiện tại: 2 cái, yêu cầu: 5 cái)". |
| Xóa danh mục thiết bị đang được gán sử dụng trong các phòng trọ | Modal chặn: "Không thể xóa trang thiết bị này! Thiết bị đang được phân bổ trong các phòng trọ. Bạn phải thu hồi về kho trước khi xóa". |

10. Xây dựng module Sổ quỹ thu - chi và ghi nhận dòng tiền phát sinh ngoài tiền phòng

Mô tả chi tiết chức năng: Giúp chủ trọ kiểm soát dòng tiền thực tế thông qua việc lập phiếu thu phát sinh (tiền cọc, thanh lý đồ cũ) và phiếu chi vận hành (mua bóng đèn, sửa ống nước, vệ sinh bể nước, thuê bảo vệ, tiền rác). Báo cáo trực quan doanh thu thuần và lợi nhuận ròng.

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Sổ quỹ thu - chi cơ sở lưu trú

Hình 19: Giao diện Quản lý Sổ quỹ thu chi và ghi nhận dòng tiền phát sinh

Bảng: Kịch bản xử lý lỗi Sổ quỹ thu - chi và Dòng tiền

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Bỏ trống số tiền hoặc hạng mục chi khi lập phiếu | Viền đỏ các ô input: "Vui lòng nhập lý do phát sinh" và "Số tiền phải lớn hơn 0 đ". |
| Chọn ngày lập phiếu lớn hơn ngày hiện tại | Báo lỗi: "Ngày ghi nhận phiếu thu/chi không thể là ngày trong tương lai". |

11. Tích hợp AI tự động viết bài đăng mô tả phòng trọ chuẩn SEO thu hút khách thuê

Mô tả chi tiết chức năng: Tính năng ứng dụng AI hỗ trợ marketing: chủ trọ chỉ cần chọn vài tiện ích chính (ban công, thang máy, gần trường ĐH), AI tự động tạo bài viết giới thiệu phòng trọ hấp dẫn, tối ưu từ khóa SEO để đăng lên cổng Renty tìm khách thuê phòng siêu tốc.

Bảng: Kịch bản xử lý lỗi AI Soạn bài viết mô tả phòng trọ

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Chưa nhập đặc điểm phòng khi bấm tạo bài đăng | Viền đỏ ô nhập: "Vui lòng nhập ít nhất một vài đặc điểm nổi bật của phòng trọ để AI xử lý". |

C. CÁC MODULE & CHỨC NĂNG DO HUỲNH VĂN VĨNH EM (THÀNH VIÊN) PHỤ TRÁCH

1, 2 & 3. Cổng tìm kiếm Renty Portal, Bộ lọc thông minh trực quan & Thanh so sánh phòng nổi

Mô tả chi tiết chức năng: Cổng công cộng phong cách Glassmorphism đáp ứng mọi thiết bị di động (Responsive). Tích hợp bộ lọc tiện ích trực quan (WC khép kín, Thú cưng, Ban công, Thang máy, Máy giặt) và công tắc 'Chỉ hiển thị phòng còn trống'. Thanh công cụ so sánh nổi cố định ở cạnh đáy màn hình cho phép đối chiếu song song tối đa 3 phòng.

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Cổng tìm kiếm phòng & Thanh so sánh nổi Renty Portal

Hình 14: Giao diện Cổng tìm kiếm, đặt phòng & Review lưu trú Renty Portal

Bảng: Kịch bản xử lý lỗi Cổng tìm kiếm, Bộ lọc và So sánh phòng Renty

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Nhập khoảng giá tối thiểu lớn hơn giá tối đa | Ô giá bôi đỏ: "Khoảng giá tìm kiếm không hợp lệ (Giá tối thiểu phải nhỏ hơn giá tối đa)". |
| Người dùng chọn thêm phòng thứ 4 vào thanh so sánh | Toast cảnh báo vàng: "Bạn chỉ có thể so sánh tối đa 3 phòng cùng một lúc". |
| Bấm nút So sánh ngay khi mới chỉ chọn 1 phòng duy nhất | Toast thông báo: "Vui lòng chọn ít nhất 2 phòng để tiến hành so sánh đối chiếu". |

4 & 5. Chi tiết phòng trọ (Room Detail), Review có xác thực & Báo cáo phòng lừa đảo

Mô tả chi tiết chức năng: Màn hình chi tiết phòng: slide trình chiếu ảnh/video, bản đồ vị trí, popover thống kê chỉ số an ninh và vệ sinh từ cư dân cũ, kèm thuật toán phát hiện cảnh báo nếu giá phòng dị biệt. Cơ chế bảo vệ minh bạch: chỉ người có hợp đồng ở thực tế mới được viết review; đồng thời cung cấp form báo cáo phòng sai phạm (ảnh ảo, cọc lừa đảo).

Bảng: Kịch bản xử lý lỗi Chi tiết phòng, Gửi đánh giá và Báo cáo sai phạm

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Người dùng chưa đăng nhập bấm Đặt phòng ngay | Hệ thống tự động hiển thị Modal đăng nhập/đăng ký kèm thông báo: "Vui lòng đăng nhập tài khoản để đặt phòng". |
| Người dùng chưa từng thuê phòng cố tình gửi đánh giá | Hệ thống chặn gửi đánh giá: "Bạn chỉ có thể đánh giá phòng này sau khi đã hoàn tất hợp đồng thuê tại đây". |
| Gửi báo cáo lừa đảo nhưng bỏ trống phần mô tả chi tiết | Ô mô tả bôi đỏ viền: "Vui lòng nhập mô tả chi tiết hành vi sai phạm để ban quản trị đối soát xử lý". |

6. Tích hợp Trợ lý ảo AI Renty Chatbot theo mô hình RAG với Google Gemini API

Mô tả chi tiết chức năng: Hộp thoại chat nổi góc phải màn hình Cổng Renty: khách thuê trò chuyện bằng tiếng Việt tự nhiên; hệ thống áp dụng kỹ thuật RAG (Retrieval-Augmented Generation) truy xuất trực tiếp dữ liệu phòng trống thực tế trong cơ sở dữ liệu và phản hồi kèm thẻ preview phòng trực quan có giá, địa chỉ và tiện ích.

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Trợ lý ảo AI & Chatbot tư vấn thuê phòng theo mô hình RAG

Hình 15: Giao diện Trợ lý ảo AI & Chatbot tư vấn thuê phòng theo mô hình RAG

Bảng 28: Kịch bản xử lý lỗi Trang Trợ lý ảo AI & Chatbot tư vấn thuê phòng Renty

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Gửi tin nhắn rỗng hoặc toàn khoảng trắng vào ô chat | Nút gửi bị vô hiệu hóa (disabled). Nếu cố tình gửi hiển thị: "Nội dung tin nhắn không được để trống". |
| Nhập câu hỏi quá dài vượt quá giới hạn xử lý (> 300 ký tự) | Ô chat bôi đỏ viền. Thông báo: "Câu hỏi quá dài (tối đa 300 ký tự). Vui lòng rút ngắn tiêu chí tìm kiếm của bạn". |
| Tìm kiếm với từ khóa không tồn tại trong hệ thống (Ví dụ: phòng trọ dưới 500k tại Quận 1) | Chatbot trả lời thân thiện: "Renty chưa tìm thấy phòng trọ nào phù hợp với yêu cầu của bạn trong hệ thống. Bạn có thể thử tăng khoảng giá hoặc chọn khu vực lân cận xem sao nhé!". |
| Người dùng gửi tin nhắn spam quá nhanh (Vượt quá giới hạn Rate Limiting 60 req/phút) | Hệ thống chặn tạm thời: "Bạn đang thao tác quá nhanh! Vui lòng chờ 30 giây trước khi gửi câu hỏi tiếp theo". |

7. Xây dựng Cổng thông tin Cư dân & Khách lưu trú (Guest Portal)

Mô tả chi tiết chức năng: Trang thông tin trực tuyến dành riêng cho khách thuê phòng: theo dõi hạn hợp đồng, kiểm tra chi tiết hóa đơn tháng (tiền phòng, điện nước, dịch vụ), nút mở mã VietQR thanh toán nhanh, danh sách người ở cùng phòng và các tiện ích dịch vụ buồng phòng.

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Cổng thông tin Cư dân & Khách lưu trú (Guest Portal)

Hình 12: Giao diện Cổng dịch vụ Cư dân & Khách lưu trú (Guest Portal)

Bảng 27: Kịch bản xử lý lỗi Trang Cổng thông tin Cư dân & Khách lưu trú (Guest Portal) & Mẫu tạm trú CT01

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Bấm mở VietQR khi hóa đơn tháng đã được thanh toán hoàn tất | Toast xanh thông báo: "Hóa đơn tháng này của bạn đã được thanh toán đầy đủ. Cảm ơn bạn!". |
| Gửi yêu cầu gia hạn hợp đồng khi hợp đồng hiện tại vẫn còn hạn trên 60 ngày | Toast thông báo: "Hợp đồng của bạn vẫn còn thời hạn dài (> 60 ngày). Hệ thống chỉ mở tính năng xin gia hạn trước khi hết hạn 30 ngày". |

8. Lập trình module Tiếp nhận & Xử lý sự cố kỹ thuật, Dịch vụ buồng phòng và Cơ chế Tự động đồng bộ thời gian thực (Smart Tickets & Real-time Auto-Reload)

Mô tả chi tiết chức năng: Hệ thống cung cấp kênh tương tác phản ánh sự cố hai chiều hoàn chỉnh giữa Khách thuê / Cư dân và Ban quản lý / Chủ trọ với độ trễ cực thấp:
- **Phía Khách thuê & Cư dân (Resident Portal):** Khách thuê truy cập Cổng cư dân để gửi báo hỏng thiết bị (chập cháy điện, rò rỉ nước, gãy khóa, tắc cống...) hoặc đặt lịch dịch vụ buồng phòng (Housekeeping). Biểu mẫu hỗ trợ phân loại danh mục đa dạng (Điện, Nước, Nội thất, Bảo trì, Dọn phòng, Khác), cho phép chỉ định chính xác vị trí cụ thể trong phòng (`specific_location` như: ban công, bồn rửa mặt, góc bếp...), đính kèm ảnh chụp hiện trạng (hỗ trợ định dạng jpeg/png/webp, dung lượng tối đa 10MB). Tích hợp Google Gemini AI tự động phân tích độ khẩn cấp (Low, Medium, High) và đưa ra lời khuyên an toàn tạm thời cho cư dân trong lúc chờ thợ.
- **Cơ chế Đồng bộ Kép thời gian thực (Dual Real-time Engine):** 
  + *Kênh chính (WebSocket Reverb):* Khi phiếu sự cố được khởi tạo thành công, hệ thống lập tức phát sóng sự kiện `TicketCreated` (kế thừa `ShouldBroadcastNow`) lên kênh private/tenant qua Laravel Echo và WebSocket Reverb server (`tenant.{tenant_id}.dashboard`).
  + *Kênh dự phòng thông minh (Smart Polling Fallback 2.5s):* Thiết lập endpoint chuyên dụng `GET /smartroom/admin/tickets/poll` định kỳ kiểm tra sự cố mới theo `tenant_id` và `last_id` mỗi 2.5 giây, đảm bảo 100% không bao giờ bị trượt hoặc mất thông tin báo cáo kể cả trong điều kiện mạng chập chờn.
- **Phía Ban quản trị & Chủ trọ (Admin Portal - `tab=ticket-section`):**
  + *Tự động tải lại trang (Auto-Reload Page):* Ngay khi nhận được tín hiệu báo cáo sự cố mới từ cư dân, hệ thống lập tức kích hoạt chuông cảnh báo âm thanh *"Ding-dong!"* bằng Web Audio API Synthesizer (tần số 587Hz -> 880Hz), đồng thời hiển thị Toast thông báo khẩn màu đỏ góc trên màn hình: `🚨 Sự cố mới: P.[Số phòng] • [Vị trí]`. Sau 0.5 giây, trang quản trị tự động tải lại (Reload) đưa thẳng về tab Quản lý Sự Cố & Báo Hỏng, hiển thị ngay phiếu báo hỏng mới nhất lên đầu bảng.
  + *Cơ chế chống lặp tải trang (Anti-loop Logic):* Quản lý trạng thái thông qua biến định danh `adminMaxTicketId`, đảm bảo trang chỉ tự động reload đúng 1 lần duy nhất khi có ID sự cố mới phát sinh từ khách hàng.
  + *Quản lý & Phân công kỹ thuật viên:* Bảng danh sách hiển thị đầy đủ thông tin: Mã phiếu, tag `Vừa gửi`, số phòng & tầng, vị trí hư hỏng, họ tên & số điện thoại cư dân, ảnh chụp đính kèm (hỗ trợ popup xem ảnh phóng to). Chủ trọ có thể mở modal cập nhật trạng thái (`pending` -> `processing` -> `resolved`), nhập tên thợ phụ trách (`assigned_to`), và tự động kích hoạt thông báo tiến độ về Telegram Bot của ban quản lý.
  + *Chỉ số thống kê động:* Các thẻ đếm chỉ số (Tổng số sự cố, Chờ xử lý, Đang khắc phục, Đã hoàn thành) và huy hiệu số lượng sự cố màu đỏ trên Sidebar menu (`sidebar-ticket-badge`) tự động nhảy số theo thời gian thực.
- **Kiểm thử tự động hoàn chỉnh (Automated Feature Tests):** Toàn bộ module được kiểm chứng tự động qua bộ test suite `TicketManagementTest.php` với 7/7 ca kiểm thử (100% PASS), bao gồm kiểm thử gửi kèm vị trí cụ thể, hiển thị tại cổng cư dân, phân công kỹ thuật viên & trạng thái, dịch vụ buồng phòng, xác thực rỗng mô tả, chặn ảnh vượt quá 10MB, và endpoint polling thời gian thực.

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Tiếp nhận sự cố kỹ thuật & buồng phòng (Smart Ticket & Real-time Sync)

Bảng: Kịch bản xử lý lỗi Tiếp nhận sự cố, Dịch vụ buồng phòng và Đồng bộ thời gian thực

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Cư dân gửi ticket báo hỏng nhưng để trống phần mô tả chi tiết sự cố | Ô mô tả bôi đỏ viền. Hiển thị: "Vui lòng nhập mô tả sự cố để ban quản lý nắm được nguyên nhân hư hỏng". |
| Tải ảnh chụp sự cố bị lỗi vượt dung lượng cho phép (> 10MB) | Thông báo lỗi: "Kích thước ảnh chụp sự cố quá lớn. Vui lòng chọn ảnh dung lượng dưới 10MB". |
| Tải lên tệp không đúng định dạng hình ảnh (PDF, DOCX, EXE...) | Thông báo lỗi: "Hình ảnh chỉ chấp nhận định dạng jpeg, jpg, png hoặc webp". |
| Chọn danh mục dịch vụ không tồn tại trong hệ thống | Báo lỗi validation: "Danh mục sự cố hoặc dịch vụ không hợp lệ". |
| Kết nối mạng hoặc WebSocket gián đoạn giữa chừng | Cơ chế Smart Polling 2.5s tự động kích hoạt ngầm, tiếp tục đồng bộ và phát chuông báo hiệu khi có sự cố mới mà không làm gián đoạn trải nghiệm người dùng. |
| Trang admin nhận nhiều sự kiện liên tiếp của cùng một sự cố | Cơ chế Deduplication & Anti-loop tự động lọc ID, loại bỏ xử lý trùng lặp và ngăn chặn tình trạng reload trang lặp vô tận. |

9. Lập trình module Quản lý thông tin Cư dân & Thân nhân lưu trú theo phòng

Mô tả chi tiết chức năng: Quản lý hồ sơ cư dân đại diện ký hợp đồng và danh sách những người ở cùng phòng (Họ tên, CCCD, Số điện thoại, Quan hệ nhân thân). Giúp chủ cơ sở nắm rõ số lượng người cư trú thực tế phục vụ công tác khai báo an ninh trật tự khu phố.

Bảng: Kịch bản xử lý lỗi Quản lý Cư dân và Thân nhân

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Thêm người ở cùng nhưng bỏ trống số CCCD hoặc Họ tên | Các ô bắt buộc bôi đỏ: "Vui lòng điền họ tên và số CCCD hợp lệ của người ở cùng". |
| Số lượng người ở cùng vượt quá sức chứa tối đa của phòng | Cảnh báo quá tải: "Phòng này chỉ có sức chứa tối đa 2 người. Vui lòng kiểm tra lại quy định phòng". |

10. Lập trình chức năng Tự động trích xuất và kết xuất tờ khai đăng ký tạm trú Mẫu CT01 (Bộ Công an)

Mô tả chi tiết chức năng: Tính năng hành chính công tự động: hệ thống tổng hợp thông tin cá nhân của cư dân, chủ cơ sở và địa chỉ cơ sở lưu trú, tự động điền vào biểu mẫu chuẩn Mẫu CT01 (Tờ khai thay đổi thông tin cư trú của Bộ Công an) và xuất file PDF để nộp công an phường.

Hình phác thảo: Phác thảo sơ bộ (Low-fidelity Wireframe) - Tờ khai thay đổi thông tin cư trú Mẫu CT01 (Bộ Công an)

Hình 13: Giao diện Tờ khai thay đổi thông tin cư trú Mẫu CT01 Bộ Công an

Bảng: Kịch bản xử lý lỗi Kết xuất tờ khai tạm trú Mẫu CT01

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Xuất Mẫu CT01 khi cư dân chưa bổ sung thông tin quê quán và ngày sinh | Modal yêu cầu: "Hồ sơ cư trú chưa đầy đủ thông tin (thiếu Quê quán / Ngày sinh). Vui lòng cập nhật đầy đủ trước khi xuất mẫu CT01". |

11. Xây dựng tiện ích Đăng ký nhận chuông báo khi phòng đang thuê chuyển sang trạng thái trống (Empty Room Alert)

Mô tả chi tiết chức năng: Tính năng tiện ích dành cho khách thuê: khi ưng ý một phòng đang có người ở, khách nhấn 'Đăng ký nhận chuông báo'. Khi hợp đồng cũ kết thúc và phòng chuyển trạng thái sang 'Trống', hệ thống tự động gửi tin nhắn thông báo qua Zalo/SMS cho khách.

Bảng: Kịch bản xử lý lỗi Đăng ký nhận chuông báo phòng trống

| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
| --- | --- |
| Bỏ trống số điện thoại nhận thông báo chuông báo | Viền đỏ ô SĐT: "Vui lòng nhập số điện thoại để hệ thống gửi thông báo". |
| Số điện thoại đã đăng ký nhận thông báo phòng này trước đó | Toast thông báo: "Bạn đã đăng ký nhận chuông báo cho phòng này rồi. Hệ thống sẽ nhắn tin ngay khi phòng trống!". |


# TÀI LIỆU THAM KHẢO

1. Laravel Documentation (v11.x): The PHP Framework for Web Artisans. Truy cập tại: https://laravel.com/docs/11.x

2. Google Cloud AI Documentation: Gemini Models and OpenAI-compatible REST API Reference. Truy cập tại: https://ai.google.dev/docs

3. Chính phủ nước CHXHCN Việt Nam: Nghị định số 13/2023/NĐ-CP ngày 17/04/2023 về Bảo vệ dữ liệu cá nhân. Cổng thông tin điện tử Chính phủ.

4. Công ty Cổ phần Thanh toán Quốc gia Việt Nam (NAPAS): Tiêu chuẩn kỹ thuật định dạng thanh toán VietQR cho chuyển khoản liên ngân hàng. Truy cập tại: https://vietqr.net/

5. World Wide Web Consortium (W3C): Web Authentication: An API for accessing Public Key Credentials Level 2 (WebAuthn / FIDO2). Truy cập tại: https://www.w3.org/TR/webauthn-2/

6. Tailwind CSS Documentation: A utility-first CSS framework for rapid UI development. Truy cập tại: https://tailwindcss.com/docs

7. Chart.js Documentation: Simple yet flexible JavaScript charting for designers & developers. Truy cập tại: https://www.chartjs.org/docs/

8. Barryvdh Laravel-DomPDF: A DOMPDF Wrapper for Laravel. Truy cập tại: https://github.com/barryvdh/laravel-dompdf
