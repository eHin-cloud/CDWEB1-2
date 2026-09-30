# -*- coding: utf-8 -*-
"""
Script lắp ráp hoàn chỉnh tài liệu baocaomoi.md
Cập nhật Bảng 2 phân chia công việc theo Git Branch và bổ sung toàn bộ Spec chuẩn hóa Phần IV.
"""

import sys
from pathlib import Path
from spec_hien import SPEC_HIEN
from spec_quy import SPEC_QUY
from spec_vinhem import SPEC_VINHEM

# Đảm bảo UTF-8
if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

baocao_dir = Path(__file__).resolve().parent
baocao_md = baocao_dir / "baocaomoi.md"

content = baocao_md.read_text(encoding="utf-8")

# 1. BẢNG 2 MỚI: Phân chia công việc khớp 100% với Git Branch và Codebase
TABLE_2_NEW = """## 1. Bảng phân chia công việc theo Git Branch & Codebase (Bảng 2)

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
| | 12 | FEAT-VEM-12 | Tiện ích Đăng ký nhận chuông báo khi phòng trống | `main` | 07/09/2026 | 04/10/2026 | 100% |"""

# Thay thế Bảng 2 trong nội dung
marker_table2_start = "## 1. Bảng phân chia công việc (Bảng 2)"
marker_table2_end = "## 2. Bảng báo cáo phiên họp nhóm (Bảng 3)"

pos_t2_start = content.find(marker_table2_start)
pos_t2_end = content.find(marker_table2_end)

if pos_t2_start != -1 and pos_t2_end != -1:
    content = content[:pos_t2_start] + TABLE_2_NEW + "\n\n\n" + content[pos_t2_end:]
    print("Đã cập nhật Bảng 2 thành công.")
else:
    print("Cảnh báo: Không tìm thấy mốc Bảng 2.")

# 2. XÂY DỰNG PHẦN IV MỚI: ĐẶC TẢ KỸ THUẬT CHI TIẾT
PART4_HEADER = """# IV. ĐẶC TẢ KỸ THUẬT HỆ THỐNG VÀ KỊCH BẢN XỬ LÝ LỖI (TECHNICAL SPECIFICATIONS & UI/UX)

Để đảm bảo nguyên tắc **"10 người đọc cả 10 người code đều giống nhau"** và **"một lập trình viên khi đọc vào spec phải code được ngay mà không cần suy đoán"**, toàn bộ các chức năng của hệ thống được đặc tả nghiêm ngặt theo chuẩn công nghiệp với cấu trúc 5 thành phần bắt buộc cho mỗi chức năng:
1. **Input Specification**: Bảng quy tắc xác thực dữ liệu đầu vào (Validation Rules, kiểu dữ liệu, thông báo lỗi cụ thể khi fail).
2. **Business Logic Flow**: Thuật toán xử lý tuần tự từng bước (kiểm tra Multi-tenancy, Guard check, DB Transaction, Event).
3. **Database Operation**: Chi tiết các bảng, cột bị tác động, cơ chế khóa lạc quan (Optimistic Locking) và toàn vẹn dữ liệu.
4. **Output Specification**: Hợp đồng dữ liệu đầu ra (Response JSON thành công / thất bại hoặc View Redirect kèm Flash Toast).
5. **UI/UX Specification & Kịch bản lỗi**: Giao diện, Class CSS Tailwind, Element IDs, và bảng xử lý chi tiết mọi trường hợp ngoại lệ.

Đồng thời, tuân thủ nguyên tắc cốt lõi: **"Trong báo cáo có gì thì trong code phải có cái đó và ngược lại"**, mọi chức năng đều được ánh xạ trực tiếp từ các nhánh Git, Controller, Model và Migration thực tế trong kho mã nguồn dự án.
"""

full_part4 = PART4_HEADER + "\n" + SPEC_HIEN + "\n" + SPEC_QUY + "\n" + SPEC_VINHEM + "\n\n"

# Thay thế toàn bộ Phần IV cũ
part4_old_marker = "# IV. THIẾT KẾ GIAO DIỆN DEMO VÀ KỊCH BẢN XỬ LÝ LỖI (UI/UX)"
part_ref_marker = "# TÀI LIỆU THAM KHẢO"

pos_p4_start = content.find(part4_old_marker)
pos_ref = content.find(part_ref_marker)

if pos_p4_start != -1 and pos_ref != -1:
    final_content = content[:pos_p4_start] + full_part4 + content[pos_ref:]
    baocao_md.write_text(final_content, encoding="utf-8")
    print(f"THÀNH CÔNG: Đã xuất bản nội dung mới vào {baocao_md}!")
    print(f"Tổng số ký tự: {len(final_content)}, Số dòng: {final_content.count(chr(10)) + 1}")
else:
    print("Lỗi: Không tìm thấy mốc Phần IV hoặc Tài liệu tham khảo.")
    sys.exit(1)
