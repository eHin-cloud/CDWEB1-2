# -*- coding: utf-8 -*-
"""
Dữ liệu đặc tả kỹ thuật chi tiết của 28 module chức năng trong file Word.
Chuẩn hóa: 10 người đọc đều code giống nhau, coder đọc vô code được ngay.
"""

# Dữ liệu Bảng 2 (Bảng phân chia công việc)
TABLE_2_ROWS = [
    # Nguyễn Thanh Hiền
    ("Nguyễn Thanh Hiền\n(Nhóm Trưởng)", "1", "Khởi tạo dự án, Docker, CI/CD & 35 Migrations\n[FEAT-HIEN-01 | Branch: main, CI/CD, CauHinh]", "07/09/2026", "09/09/2026", "100%", ""),
    ("", "2", "Phân quyền truy cập RBAC & Multi-tenancy Scoping\n[FEAT-HIEN-02 | Branch: Hien/PhanQuyen]", "07/09/2026", "11/09/2026", "100%", ""),
    ("", "3", "Đăng ký, Đăng nhập truyền thống & Rate Limiting\n[FEAT-HIEN-03 | Branch: Hien/Login_Sign]", "07/09/2026", "13/09/2026", "100%", ""),
    ("", "4", "Xác thực sinh trắc học WebAuthn Passkey FIDO2\n[FEAT-HIEN-04 | Branch: Hien/Login_Sign]", "07/09/2026", "16/09/2026", "100%", ""),
    ("", "5", "Mã hóa bảo mật PII AES-256-GCM & Blind Index\n[FEAT-HIEN-05 | Branch: Hien/PhanQuyen]", "07/09/2026", "19/09/2026", "100%", ""),
    ("", "6", "Quản lý Hợp đồng thuê phòng & Tiền cọc\n[FEAT-HIEN-06 | Branch: main]", "07/09/2026", "22/09/2026", "100%", ""),
    ("", "7", "Ký số hợp đồng online bằng Canvas Pad & OTP\n[FEAT-HIEN-07 | Branch: main]", "07/09/2026", "24/09/2026", "100%", ""),
    ("", "8", "Xác thực định danh chủ trọ lũy tiến KYC & Tích Xanh\n[FEAT-HIEN-08 | Branch: main]", "07/09/2026", "26/09/2026", "100%", ""),
    ("", "9", "Bảng điều khiển kiểm duyệt hồ sơ (Superadmin)\n[FEAT-HIEN-09 | Branch: main]", "07/09/2026", "28/09/2026", "100%", ""),
    ("", "10", "Xem tài liệu Signed URL (TTL 5m) & Dynamic Watermark\n[FEAT-HIEN-10 | Branch: main]", "07/09/2026", "30/09/2026", "100%", ""),
    ("", "11", "Hệ thống Nhật ký kiểm toán bất biến (Audit Logs)\n[FEAT-HIEN-11 | Branch: main]", "07/09/2026", "02/10/2026", "100%", ""),
    ("", "12", "AI Gemini phân tích và sinh điều khoản hợp đồng\n[FEAT-HIEN-12 | Branch: main]", "07/09/2026", "04/10/2026", "100%", ""),
    ("", "13", "Quy trình Onboarding Step-Wizard cho chủ trọ mới\n[FEAT-HIEN-13 | Branch: Hien/Menu]", "07/09/2026", "06/10/2026", "100%", ""),

    # Nguyễn Anh Quý
    ("Nguyễn Anh Quý\n(Nhóm Phó)", "1", "CRUD Quản lý Cơ sở lưu trú (Properties) & Guard Check\n[FEAT-AQ-01 | Branch: AnhQuy/quan-ly-co-so-luu-tru]", "07/09/2026", "09/09/2026", "100%", ""),
    ("", "2", "CRUD Quản lý Phòng lưu trú (Rooms) & Serial công tơ\n[FEAT-AQ-02 | Branch: AnhQuy/quan-ly-phong]", "07/09/2026", "12/09/2026", "100%", ""),
    ("", "3", "Sơ đồ Ma trận phòng trực quan (Visual Matrix) & Realtime\n[FEAT-AQ-03 | Branch: AnhQuy/ma-tran-phong]", "07/09/2026", "14/09/2026", "100%", ""),
    ("", "4", "Chốt số Điện - Nước định kỳ hàng tháng\n[FEAT-AQ-04 | Branch: AnhQuy/chot-so-dien-nuoc-ai-ocr]", "07/09/2026", "16/09/2026", "100%", ""),
    ("", "5", "AI Vision OCR Quét công tơ & Khớp Serial hàng loạt\n[FEAT-AQ-05 | Branch: AnhQuy/chot-so-dien-nuoc-ai-ocr]", "07/09/2026", "18/09/2026", "100%", ""),
    ("", "6", "Động cơ tính cước tự động (BillingEngine) & Doanh thu\n[FEAT-AQ-06 | Branch: AnhQuy/tinh-cuoc-hoa-don-vietqr]", "07/09/2026", "21/09/2026", "100%", ""),
    ("", "7", "Xuất Hóa đơn / Folio PDF kèm Mã VietQR NAPAS247\n[FEAT-AQ-07 | Branch: AnhQuy/tinh-cuoc-hoa-don-vietqr]", "07/09/2026", "23/09/2026", "100%", ""),
    ("", "8", "Quét nợ tự động và gửi tin nhắn Zalo/SMS/Telegram\n[FEAT-AQ-08 | Branch: AnhQuy/nhac-no-zalo-sms]", "07/09/2026", "25/09/2026", "100%", ""),
    ("", "9", "Quản lý Trang thiết bị tài sản kho & Phân bổ phòng\n[FEAT-AQ-09 | Branch: AnhQuy/quan-ly-trang-thiet-bi]", "07/09/2026", "27/09/2026", "100%", ""),
    ("", "10", "Sổ quỹ thu - chi và ghi nhận dòng tiền phát sinh\n[FEAT-AQ-10 | Branch: AnhQuy/so-quy-thu-chi]", "07/09/2026", "30/09/2026", "100%", ""),
    ("", "11", "AI Gemini tự động viết bài mô tả phòng chuẩn SEO\n[FEAT-AQ-11 | Branch: AnhQuy/ai-viet-mo-ta-phong]", "07/09/2026", "02/10/2026", "100%", ""),
    ("", "12", "Nhật ký thao tác quản trị hệ thống (AdminActivityLog)\n[FEAT-AQ-12 | Branch: AnhQuy/nhat-ky-kiem-toan]", "07/09/2026", "04/10/2026", "100%", ""),
    ("", "13", "Phân hệ Khách sạn / Lễ tân: Check-in, Check-out & Folio\n[FEAT-AQ-13 | Branch: AnhQuy/PhanQuyen]", "07/09/2026", "06/10/2026", "100%", ""),

    # Huỳnh Văn Vĩnh Em
    ("Huỳnh Văn Vĩnh Em\n(Thành Viên)", "1", "Cổng tìm kiếm lưu trú công cộng Renty Portal\n[FEAT-VEM-01 | Branch: main]", "07/09/2026", "09/09/2026", "100%", ""),
    ("", "2", "Bộ lọc tìm kiếm thông minh đa tiêu chí (Smart Filter)\n[FEAT-VEM-02 | Branch: feat(smart-search)]", "07/09/2026", "12/09/2026", "100%", ""),
    ("", "3", "Thanh công cụ so sánh phòng nổi song song (3 phòng)\n[FEAT-VEM-03 | Branch: main]", "07/09/2026", "14/09/2026", "100%", ""),
    ("", "4", "Màn hình Chi tiết phòng lưu trú (Room Detail & Media)\n[FEAT-VEM-04 | Branch: main]", "07/09/2026", "16/09/2026", "100%", ""),
    ("", "5", "Hệ thống Đánh giá Review có xác thực người ở thực tế\n[FEAT-VEM-05 | Branch: main]", "07/09/2026", "18/09/2026", "100%", ""),
    ("", "6", "Tiếp nhận Báo cáo phòng vi phạm / lừa cọc (RoomReport)\n[FEAT-VEM-06 | Branch: main]", "07/09/2026", "21/09/2026", "100%", ""),
    ("", "7", "Trợ lý ảo AI Renty Chatbot theo mô hình RAG (Gemini)\n[FEAT-VEM-07 | Branch: main]", "07/09/2026", "24/09/2026", "100%", ""),
    ("", "8", "Cổng dịch vụ Cư dân & Khách lưu trú (Guest Portal)\n[FEAT-VEM-08 | Branch: main]", "07/09/2026", "26/09/2026", "100%", ""),
    ("", "9", "Tiếp nhận & Xử lý sự cố kỹ thuật (Smart Tickets & AI)\n[FEAT-VEM-09 | Branch: VinhEm/8-XuLyBaoHongDangDonPhong]", "07/09/2026", "28/09/2026", "100%", ""),
    ("", "10", "Quản lý thông tin Cư dân & Thân nhân lưu trú\n[FEAT-VEM-10 | Branch: main]", "07/09/2026", "30/09/2026", "100%", ""),
    ("", "11", "Tự động kết xuất tờ khai tạm trú Mẫu CT01 (Bộ Công an)\n[FEAT-VEM-11 | Branch: main]", "07/09/2026", "02/10/2026", "100%", ""),
    ("", "12", "Tiện ích Đăng ký nhận chuông báo khi phòng trống\n[FEAT-VEM-12 | Branch: main]", "07/09/2026", "04/10/2026", "100%", "")
]
