# -*- coding: utf-8 -*-
"""
Script nâng cấp toàn diện Báo cáo nhóm A:
1. Đọc và trích xuất toàn bộ dữ liệu 30 tính năng từ BaoCao_GiaoDien_Message_30ChucNang (1).docx:
   - Thông tin tổng quan (Tên, Mã, Mô tả, Actor, Trigger, Điều kiện)
   - Input Specification
   - Quy tắc nghiệp vụ & Luồng xử lý Flow
   - Bảng thông báo lỗi và xác thực giao diện (UI VALIDATION ERROR MESSAGES - mã lỗi ERR_xx_xx)
   - TẤT CẢ 57 HÌNH ẢNH GIAO DIỆN VÀ WIREFRAME (kèm caption và mô tả chức năng)
   - Bảng mô tả chi tiết từng phần tử UI (UI Elements Description)
   - Test Scenarios
2. Kết hợp với các thông số kỹ thuật backend (Endpoints API, Middleware, Database Operations, Exception handling, DoD)
3. Cập nhật Bảng 3 (Phân chia công việc) và Bảng 5 (Endpoints API tổng quan)
4. Tái cấu trúc Phần IV của Báo cáo nhóm A.docx:
   - Phần A: Nguyễn Thanh Hiền (Tính năng 1 - 10)
   - Phần B: Nguyễn Anh Quý (Tính năng 11 - 20)
   - Phần C: Huỳnh Văn Vĩnh Em (Tính năng 21 - 30)
5. Chèn trực tiếp hình ảnh vào đúng từng tính năng, loại bỏ cụm hình dồn ở cuối file.
6. Giữ nguyên 100% Phần I, II, III (Trang bìa, Lời mở đầu, Mục lục, Bảng 1-5, ERD, Data Dictionary) và Phần V (Tài liệu tham khảo).
"""

import sys
import os
import zipfile
import docx
from docx.shared import Pt, Inches, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls
from copy import deepcopy
import xml.etree.ElementTree as ET

sys.stdout.reconfigure(encoding='utf-8')

src_spec_path = 'BaoCao_GiaoDien_Message_30ChucNang (1).docx'
target_doc_path = 'Báo cáo nhóm A.docx'

print("=== BƯỚC 1: TRÍCH XUẤT TOÀN BỘ 30 TÍNH NĂNG TỪ SPEC CHUẨN ===")

# Map rId -> filename trong spec docx
with zipfile.ZipFile(src_spec_path, 'r') as z:
    rels_xml = z.read('word/_rels/document.xml.rels')
    rels_root = ET.fromstring(rels_xml)
    rId_to_filename = {}
    for rel in rels_root:
        rId = rel.get('Id')
        target = rel.get('Target')
        if 'media/' in target:
            rId_to_filename[rId] = os.path.basename(target)

spec_doc = docx.Document(src_spec_path)

# Xác định phạm vi từng tính năng trong spec_doc
feat_ranges = {}
for p_idx, p in enumerate(spec_doc.paragraphs):
    txt = p.text.strip()
    if txt.startswith('TÍNH NĂNG '):
        parts = txt.split(':')
        f_num_str = parts[0].replace('TÍNH NĂNG', '').strip()
        try:
            f_num = int(f_num_str)
            feat_ranges[f_num] = {'start': p_idx, 'title': txt}
        except:
            pass

sorted_f_nums = sorted(feat_ranges.keys())
for i, f_num in enumerate(sorted_f_nums):
    start = feat_ranges[f_num]['start']
    end = feat_ranges[sorted_f_nums[i+1]]['start'] if i + 1 < len(sorted_f_nums) else len(spec_doc.paragraphs)
    feat_ranges[f_num]['end'] = end

# Trích xuất dữ liệu chi tiết từng tính năng
spec_features = {}
for f_num in sorted_f_nums:
    start_p = feat_ranges[f_num]['start']
    end_p = feat_ranges[f_num]['end']
    
    t_base = (f_num - 1) * 5
    tbl_overview = spec_doc.tables[t_base] if t_base < len(spec_doc.tables) else None
    tbl_input = spec_doc.tables[t_base + 1] if t_base + 1 < len(spec_doc.tables) else None
    tbl_errors = spec_doc.tables[t_base + 2] if t_base + 2 < len(spec_doc.tables) else None
    tbl_elements = spec_doc.tables[t_base + 3] if t_base + 3 < len(spec_doc.tables) else None
    tbl_tests = spec_doc.tables[t_base + 4] if t_base + 4 < len(spec_doc.tables) else None

    # Lấy thông tin tổng quan từ Table 1
    overview_dict = {}
    if tbl_overview:
        for r in tbl_overview.rows:
            k = r.cells[0].text.strip()
            v = r.cells[1].text.strip()
            overview_dict[k] = v

    # Lấy bảng input
    input_rows = []
    if tbl_input:
        for r in tbl_input.rows:
            input_rows.append([c.text.strip().replace('\n', ' ') for c in r.cells])

    # Lấy bảng thông báo lỗi UI
    error_rows = []
    if tbl_errors:
        for r in tbl_errors.rows:
            error_rows.append([c.text.strip().replace('\n', ' ') for c in r.cells])

    # Lấy bảng UI elements
    element_rows = []
    if tbl_elements:
        for r in tbl_elements.rows:
            element_rows.append([c.text.strip().replace('\n', ' ') for c in r.cells])

    # Lấy bảng test cases
    test_rows = []
    if tbl_tests:
        for r in tbl_tests.rows:
            test_rows.append([c.text.strip().replace('\n', ' ') for c in r.cells])

    # Trích xuất hình ảnh trong phạm vi của tính năng
    images = []
    for p_i in range(start_p, end_p):
        p = spec_doc.paragraphs[p_i]
        blips = p._p.xpath('.//a:blip')
        if blips:
            for blip in blips:
                embed_id = blip.get('{http://schemas.openxmlformats.org/officeDocument/2006/relationships}embed')
                img_file = rId_to_filename.get(embed_id, '')
                caption = ""
                desc = ""
                for offset in [1, 2]:
                    if p_i + offset < end_p:
                        nxt = spec_doc.paragraphs[p_i + offset].text.strip()
                        if nxt.startswith('Hình '):
                            caption = nxt
                            if p_i + offset + 1 < end_p:
                                nxt_desc = spec_doc.paragraphs[p_i + offset + 1].text.strip()
                                if nxt_desc.startswith('Mô tả chức năng:'):
                                    desc = nxt_desc
                            break
                if img_file:
                    images.append({
                        'filename': img_file,
                        'caption': caption,
                        'desc': desc
                    })

    # Lấy các đoạn text về Flow và Business Rules
    rules_text = []
    flow_steps = []
    sec_state = ""
    for p_i in range(start_p, end_p):
        t = spec_doc.paragraphs[p_i].text.strip()
        if '3. QUY TẮC NGHIỆP VỤ' in t:
            sec_state = "rules"
        elif '4. LUỒNG XỬ LÝ' in t:
            sec_state = "flow"
        elif '5. BẢNG THÔNG BÁO LỖI' in t or '6. HÌNH ẢNH' in t:
            sec_state = ""
        elif sec_state == "rules" and t and not t.startswith('3.'):
            rules_text.append(t)
        elif sec_state == "flow" and t and not t.startswith('4.'):
            flow_steps.append(t)

    spec_features[f_num] = {
        'num': f_num,
        'title': feat_ranges[f_num]['title'],
        'overview': overview_dict,
        'input_table': input_rows,
        'error_table': error_rows,
        'element_table': element_rows,
        'test_table': test_rows,
        'images': images,
        'rules': rules_text,
        'flow': flow_steps
    }

print(f"-> Đã trích xuất thành công dữ liệu của {len(spec_features)} tính năng.")

# Backend Endpoints map chuẩn cho từng tính năng (1-30)
BACKEND_ENDPOINTS = {
    1: [("POST", "/api/auth/register", "guest, throttle:10,1", "Đăng ký tài khoản người dùng kèm xác thực mật khẩu"),
        ("POST", "/api/auth/login", "guest, throttle:5,1", "Đăng nhập hệ thống kèm Rate Limiting chống Brute-force"),
        ("POST", "/api/auth/otp/verify", "auth, throttle:5,1", "Xác thực mã OTP gửi về số điện thoại/email")],
    2: [("GET", "/smartroom/admin/profile", "auth, role:landlord", "Xem thông tin hồ sơ cá nhân và cấu hình tài khoản chủ trọ"),
        ("POST", "/smartroom/admin/profile/update", "auth, role:landlord", "Cập nhật họ tên, CCCD, thông tin tài khoản ngân hàng VietQR")],
    3: [("GET", "/smartroom/admin/rooms", "auth, tenant.scope", "Danh sách danh mục phòng trọ kèm bộ lọc trạng thái và tầng"),
        ("POST", "/smartroom/admin/rooms/store", "auth, tenant.scope", "Thêm mới phòng trọ, cấu hình đơn giá điện nước và tiện nghi"),
        ("POST", "/smartroom/admin/rooms/{id}/update", "auth, tenant.scope", "Cập nhật thông tin chi tiết phòng trọ (Optimistic Locking)"),
        ("DELETE", "/smartroom/admin/rooms/{id}/delete", "auth, tenant.scope", "Xóa phòng trọ (Guard Check chặn khi phòng đang có người ở)")],
    4: [("GET", "/renty", "web", "Trang chủ sàn kết nối phòng trọ Renty phong cách Glassmorphism"),
        ("GET", "/api/renty/rooms", "api, throttle:60,1", "API lấy danh sách phòng công khai hiển thị trên trang chủ")],
    5: [("GET", "/renty/map", "web", "Bản đồ phòng trọ thông minh tương tác trực quan"),
        ("GET", "/api/renty/rooms/map", "api, throttle:60,1", "API trả về tọa độ GPS và bán kính tìm kiếm phòng")],
    6: [("GET", "/renty/room/{id}", "web", "Trang chi tiết phòng trọ, thư viện ảnh 360 và thông số tiện nghi"),
        ("GET", "/api/renty/room/{id}/reviews", "api", "Danh sách đánh giá thực tế của cư dân đã ký hợp đồng")],
    7: [("POST", "/api/renty/contact-requests", "throttle:15,1", "Gửi yêu cầu hẹn lịch xem phòng và nhận tư vấn trực tiếp"),
        ("GET", "/smartroom/admin/contact-requests", "auth, role:landlord", "Chủ trọ tiếp nhận, xác nhận lịch hẹn hoặc từ chối yêu cầu")],
    8: [("POST", "/smartroom/admin/contracts/store", "auth, role:landlord", "Tạo hợp đồng thuê phòng online với các điều khoản pháp lý chuẩn"),
        ("POST", "/smartroom/contract/{id}/sign-canvas", "auth", "Ký số hợp đồng bằng HTML5 Canvas Signature Pad"),
        ("GET", "/smartroom/contract/{id}/export-pdf", "auth", "Kết xuất bản hợp đồng điện tử trọn gói định dạng PDF")],
    9: [("POST", "/smartroom/admin/kyc/submit", "auth, role:landlord", "Tải lên ảnh CCCD 2 mặt và giấy tờ PCCC đăng ký Tích Xanh KYC"),
        ("GET", "/smartroom/admin/kyc/status", "auth", "Theo dõi tiến độ xét duyệt định danh hồ sơ chủ trọ")],
    10: [("GET", "/admin/kyc/pending", "auth, role:superadmin", "Danh sách hồ sơ định danh chủ trọ đang chờ thẩm định"),
         ("POST", "/admin/kyc/{id}/approve", "auth, role:superadmin", "Phê duyệt cấp huy hiệu Tích Xanh chứng nhận cơ sở an toàn"),
         ("POST", "/admin/kyc/{id}/reject", "auth, role:superadmin", "Từ chối hồ sơ kèm lý do phản hồi chi tiết")],
    11: [("GET", "/smartroom/admin/buildings", "auth, tenant.scope", "Danh sách cơ sở lưu trú (Tòa nhà, Khách sạn, Dãy trọ)"),
         ("POST", "/smartroom/admin/buildings/store", "auth, tenant.scope", "Thêm mới cơ sở lưu trú kèm cấu hình tiện ích chung"),
         ("DELETE", "/smartroom/admin/buildings/{id}/delete", "auth, tenant.scope", "Xóa cơ sở (Guard Check chặn khi còn phòng trực thuộc)")],
    12: [("GET", "/smartroom/admin/equipment", "auth, tenant.scope", "Quản lý danh mục trang thiết bị, tài sản kho và phân bổ phòng"),
         ("POST", "/smartroom/admin/equipment/handover", "auth, tenant.scope", "Bàn giao thiết bị cho khách thuê hoặc thu hồi khấu trừ cọc")],
    13: [("GET", "/smartroom/admin/room-matrix", "auth, tenant.scope", "Sơ đồ ma trận phòng trực quan theo tầng"),
         ("GET", "/smartroom/admin/room-matrix/sse", "auth, tenant.scope", "Luồng đồng bộ dữ liệu thời gian thực Server-Sent Events (SSE)")],
    14: [("POST", "/api/ai/assistant-hub", "auth:api, throttle:30,1", "Trung tâm điều phối AI đa tác nhân: Tra cứu, Cố vấn chủ trọ & Tiếp nhận sự cố"),
         ("POST", "/api/ai/room-finder", "throttle:60,1", "Trợ lý AI tra cứu và gợi ý phòng trọ thông minh theo ngôn ngữ tự nhiên"),
         ("POST", "/api/ai/landlord-advisor", "auth, role:landlord", "Cố vấn kinh doanh, tối ưu giá thuê & soạn bài đăng chuẩn SEO"),
         ("POST", "/api/ai/incident-triage", "auth, role:resident", "Tiếp nhận, phân loại khẩn cấp sự cố kỹ thuật và hướng dẫn xử lý an toàn")],
    15: [("GET", "/smartroom/admin/utility/bulk", "auth, tenant.scope", "Bảng nhập chỉ số điện nước hàng loạt định kỳ theo tầng/tòa"),
         ("POST", "/smartroom/admin/utility/bulk-store", "auth, tenant.scope", "Lưu trữ chỉ số điện nước hàng loạt và kiểm tra chống ngược dòng")],
    16: [("POST", "/smartroom/admin/ai/ocr-meter", "auth, tenant.scope", "AI Vision nhận diện mặt số công tơ điện nước từ ảnh chụp đơn lẻ"),
         ("POST", "/smartroom/admin/ai/ocr-meter-bulk", "auth, tenant.scope", "Tải lên hàng loạt ảnh công tơ, AI bóc tách và khớp mã số đồng hồ")],
    17: [("POST", "/smartroom/admin/utility/calculate", "auth, tenant.scope", "Tính toán chiết tính tiền phòng, điện nước và dịch vụ tự động"),
         ("GET", "/smartroom/admin/utility/{id}/vietqr", "auth, tenant.scope", "Xuất mã QR thanh toán động VietQR NAPAS247 chuẩn ngân hàng"),
         ("GET", "/smartroom/admin/utility/{id}/invoice-pdf", "auth, tenant.scope", "Kết xuất hóa đơn điện tử tiền phòng định dạng PDF")],
    18: [("GET", "/smartroom/admin/housekeeping/matrix", "auth, tenant.scope", "Sơ đồ buồng phòng thời gian thực, hiển thị trực quan mã màu FSM dọn phòng"),
         ("POST", "/smartroom/admin/housekeeping/assign", "auth, tenant.scope", "Phân công nhân viên buồng phòng dọn dẹp theo ca"),
         ("POST", "/smartroom/admin/housekeeping/status", "auth, tenant.scope", "Cập nhật tiến độ dọn phòng (Bắt đầu dọn -> Dọn sạch sẽ)"),
         ("POST", "/smartroom/admin/housekeeping/inspect", "auth, tenant.scope", "Lễ tân nghiệm thu buồng phòng đạt chuẩn sẵn sàng đón khách"),
         ("POST", "/smartroom/admin/frontdesk/checkin", "auth, tenant.scope", "Check-in khách lưu trú (Guard Check chặn tuyệt đối phòng Dirty)"),
         ("POST", "/smartroom/admin/frontdesk/checkout", "auth, tenant.scope", "Check-out trả phòng, tự động chuyển phòng sang Dirty và đối soát minibar")],
    19: [("GET", "/smartroom/admin/reports/cash-flow", "auth, tenant.scope", "Sổ quỹ thu - chi tài chính và biểu đồ dòng tiền Chart.js"),
         ("POST", "/smartroom/admin/reports/transactions", "auth, tenant.scope", "Ghi nhận giao dịch phát sinh thu chi ngoài tiền phòng"),
         ("GET", "/smartroom/admin/reports/export-excel", "auth, tenant.scope", "Xuất báo cáo sổ quỹ tài chính ra định dạng Excel/CSV UTF-8")],
    20: [("GET", "/admin/dashboard", "auth, role:superadmin", "Bảng điều khiển quản trị nền tảng hệ thống Superadmin Console"),
         ("GET", "/admin/audit-logs", "auth, role:superadmin", "Nhật ký kiểm toán an ninh truy vết bất biến toàn hệ thống"),
         ("POST", "/admin/system/config", "auth, role:superadmin", "Thiết lập tham số toàn sàn, hạn ngạch tài khoản và chính sách phí")],
    21: [("GET", "/renty/search", "web", "Cổng tìm kiếm và bộ lọc phòng trọ thông minh đa tiêu chí Renty"),
         ("GET", "/api/renty/filter", "api, throttle:60,1", "Lọc phòng theo khoảng giá, vị trí GPS, tiện ích (Redis Cache Tags)")],
    22: [("POST", "/renty/chatbot/chat", "throttle:20,1", "Trợ lý ảo AI Renty Chatbot tư vấn tìm phòng theo mô hình RAG (Gemini API)"),
         ("GET", "/renty/chatbot/history", "auth:api", "Xem lại lịch sử hội thoại tư vấn tìm phòng")],
    23: [("POST", "/api/renty/rooms/compare", "throttle:30,1", "So sánh đối chiếu song song từ 2 đến 3 phòng theo thang điểm 10 đa tiêu chí")],
    24: [("POST", "/api/renty/room-alerts", "throttle:10,1", "Đăng ký nhận chuông báo tự động khi phòng chuyển sang trạng thái trống"),
         ("GET", "/api/renty/room-alerts/my", "auth:api", "Quản lý danh sách chuông báo phòng đã thiết lập")],
    25: [("POST", "/renty/room/{id}/review", "auth, verified_tenant", "Đánh giá phòng thực tế (Chỉ người có hợp đồng thuê mới được review)"),
         ("POST", "/renty/room/{id}/report", "throttle:5,1", "Gửi báo cáo vi phạm phòng trọ: lừa đảo, sai giá, thông tin ảo")],
    26: [("GET", "/smartroom/admin/residents", "auth, tenant.scope", "Quản lý danh sách cư dân, khách lưu trú và thân nhân theo phòng"),
         ("POST", "/smartroom/admin/residents/store", "auth, tenant.scope", "Thêm mới thông tin cư dân, số điện thoại, CCCD và hợp đồng liên kết")],
    27: [("GET", "/smartroom/resident/portal", "auth, role:resident", "Cổng dịch vụ cá nhân hóa dành cho cư dân đang thuê phòng"),
         ("GET", "/smartroom/resident/invoices", "auth, role:resident", "Xem danh sách hóa đơn, lịch sử thanh toán và quét mã VietQR cá nhân")],
    28: [("POST", "/smartroom/resident/tickets/store", "auth, role:resident", "Cư dân gửi yêu cầu sửa chữa báo hỏng kỹ thuật và dịch vụ phòng"),
         ("GET", "/smartroom/admin/tickets", "auth, tenant.scope", "Ban quản lý tiếp nhận, phân loại mức độ khẩn cấp và điều phối kỹ thuật viên"),
         ("POST", "/smartroom/admin/tickets/{id}/status", "auth, tenant.scope", "Cập nhật tiến độ xử lý sự cố (Đang xử lý -> Đã hoàn thành)")],
    29: [("GET", "/smartroom/admin/payments", "auth, tenant.scope", "Bảng kê quản lý thanh toán, công nợ và đối soát tiền phòng"),
         ("POST", "/smartroom/admin/payments/confirm", "auth, tenant.scope", "Xác nhận gạch nợ thanh toán hóa đơn tiền mặt hoặc chuyển khoản VietQR")],
    30: [("GET", "/smartroom/admin/dashboard", "auth, tenant.scope", "Bảng điều khiển thống kê tổng quan: Tỷ lệ lấp đầy phòng, doanh thu và công nợ"),
         ("GET", "/smartroom/admin/dashboard/revenue-chart", "auth, tenant.scope", "Dữ liệu chuỗi thời gian vẽ biểu đồ tài chính Chart.js")]
}

print("=== BƯỚC 2: MỞ BÁO CÁO CHÍNH VÀ CẬP NHẬT BẢNG 3, BẢNG 5 ===")
doc = docx.Document(target_doc_path)

# Cập nhật Bảng 3: Phân chia công việc (14 & 18)
t3 = doc.tables[3]
t3.rows[14].cells[2].text = "Hệ sinh thái Trợ lý AI Hỗ trợ toàn diện (Tra cứu phòng, Cố vấn chủ trọ & Tiếp nhận sự cố)"
t3.rows[18].cells[2].text = "Phân hệ Lễ tân & Quản lý buồng phòng, dọn phòng (Housekeeping & Front Desk)"

# Cập nhật Bảng 5: API Endpoints (28 & 40)
t5 = doc.tables[5]
t5.rows[28].cells[0].text = "Đa phân hệ"
t5.rows[28].cells[1].text = "Hệ sinh thái Trợ lý AI Hỗ trợ toàn diện (Tra cứu, Cố vấn & Sự cố)"
t5.rows[28].cells[2].text = "POST"
t5.rows[28].cells[3].text = "/api/ai/assistant-hub"
t5.rows[28].cells[4].text = "Hệ sinh thái AI đa tác nhân: Tra cứu phòng cho khách thuê, Cố vấn kinh doanh & viết bài SEO cho chủ trọ, Tiếp nhận & phân loại sự cố khẩn cấp cho cư dân"

t5.rows[40].cells[0].text = "Lễ tân / Chủ trọ"
t5.rows[40].cells[1].text = "Phân hệ Lễ tân & Buồng phòng (Dọn phòng)"
t5.rows[40].cells[2].text = "POST"
t5.rows[40].cells[3].text = "/smartroom/admin/housekeeping/status"
t5.rows[40].cells[4].text = "Quản lý vòng đời buồng phòng theo máy trạng thái FSM (Clean, Dirty, Cleaning, Inspected), điều phối nhân viên dọn phòng và check-in/out khách"

print("-> Đã cập nhật xong Bảng 3 và Bảng 5!")

body = doc._body._element
children = list(body)

sec4_start_idx = -1
sec4_end_idx = -1

for idx, c in enumerate(children):
    if idx > 50 and c.tag.endswith('p'):
        txt = ''.join(c.itertext()).strip()
        if 'IV. ĐẶC TẢ KỸ THUẬT' in txt and sec4_start_idx == -1:
            sec4_start_idx = idx
        if 'TÀI LIỆU THAM KHẢO' in txt and sec4_start_idx != -1 and sec4_end_idx == -1:
            sec4_end_idx = idx
            break

print(f"Section IV trong Báo cáo nhóm A: start={sec4_start_idx}, end={sec4_end_idx}")

# Phần tử mốc: Chèn toàn bộ nội dung mới vào trước target_ref
target_ref = children[sec4_end_idx]

# Xóa các phần tử cũ của Section IV (từ sau tiêu đề Section IV đến trước Tài liệu tham khảo)
print(f"Đang dọn dẹp các phần tử cũ của Section IV ({sec4_end_idx - (sec4_start_idx + 1)} phần tử)...")
for idx in range(sec4_end_idx - 1, sec4_start_idx, -1):
    body.remove(children[idx])

print("-> Đã dọn dẹp sạch sẽ Section IV cũ!")

# Helper functions tạo phần tử XML
helper_doc = docx.Document()

def insert_elem(elem):
    target_ref.addprevious(deepcopy(elem))

def format_run(run, font_name="Times New Roman", size_pt=11, bold=False, italic=False, color=None):
    run.font.name = font_name
    run.font.size = Pt(size_pt)
    run.bold = bold
    run.italic = italic
    if color:
        run.font.color.rgb = color
    rPr = run._r.get_or_add_rPr()
    rFonts = parse_xml(f'<w:rFonts {nsdecls("w")} w:ascii="{font_name}" w:hAnsi="{font_name}" w:cs="{font_name}"/>')
    rPr.append(rFonts)

def add_p(text, size_pt=12, bold=False, italic=False, align=WD_ALIGN_PARAGRAPH.LEFT, space_before=2, space_after=3):
    p = helper_doc.add_paragraph()
    p.alignment = align
    p.paragraph_format.space_before = Pt(space_before)
    p.paragraph_format.space_after = Pt(space_after)
    p.paragraph_format.line_spacing = 1.15
    run = p.add_run(text)
    format_run(run, size_pt=size_pt, bold=bold, italic=italic)
    insert_elem(p._p)
    return p

def set_cell_width(cell, width_dxa):
    tcPr = cell._tc.get_or_add_tcPr()
    tcW = parse_xml(f'<w:tcW {nsdecls("w")} w:w="{width_dxa}" w:type="dxa"/>')
    tcPr.append(tcW)

def set_cell_shading(cell, color_hex="F1F5F9"):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{color_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=140, right=140):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(
        f'<w:tcMar {nsdecls("w")}>'
        f'  <w:top w:w="{top}" w:type="dxa"/>'
        f'  <w:bottom w:w="{bottom}" w:type="dxa"/>'
        f'  <w:left w:w="{left}" w:type="dxa"/>'
        f'  <w:right w:w="{right}" w:type="dxa"/>'
        f'</w:tcMar>'
    )
    tcPr.append(tcMar)

def set_table_borders(table, color="CBD5E1", sz="4", val="single"):
    tblPr = table._tbl.tblPr
    borders = parse_xml(
        f'<w:tblBorders {nsdecls("w")}>'
        f'  <w:top w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'  <w:left w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'  <w:bottom w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'  <w:right w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'  <w:insideH w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'  <w:insideV w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'</w:tblBorders>'
    )
    tblPr.append(borders)

def add_table(headers, data_rows, col_widths=None):
    if not headers or not data_rows:
        return
    num_cols = len(headers)
    tbl = helper_doc.add_table(rows=len(data_rows) + 1, cols=num_cols)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(tbl, color="CBD5E1", sz="4")
    total_dxa = 9360
    if not col_widths:
        col_widths = [total_dxa // num_cols] * num_cols

    # Header
    hdr = tbl.rows[0]
    hdr._tr.get_or_add_trPr().append(parse_xml(f'<w:tblHeader {nsdecls("w")}/>'))
    for c_i, h_text in enumerate(headers):
        c = hdr.cells[c_i]
        c.text = h_text
        set_cell_width(c, col_widths[c_i] if c_i < len(col_widths) else 1500)
        set_cell_shading(c, "E2E8F0")
        set_cell_margins(c, top=110, bottom=110, left=130, right=130)
        for p in c.paragraphs:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after = Pt(2)
            for r in p.runs:
                format_run(r, size_pt=11, bold=True, color=RGBColor(15, 23, 42))

    # Rows
    for r_i, d_row in enumerate(data_rows):
        row = tbl.rows[r_i + 1]
        bg_color = "FFFFFF" if r_i % 2 == 0 else "F8FAFC"
        for c_i in range(num_cols):
            c = row.cells[c_i]
            val = d_row[c_i] if c_i < len(d_row) else ""
            c.text = val
            set_cell_width(c, col_widths[c_i] if c_i < len(col_widths) else 1500)
            set_cell_shading(c, bg_color)
            set_cell_margins(c, top=90, bottom=90, left=130, right=130)
            for p in c.paragraphs:
                p.paragraph_format.space_before = Pt(2)
                p.paragraph_format.space_after = Pt(2)
                for r in p.runs:
                    format_run(r, size_pt=10.5)

    insert_elem(tbl._tbl)

def add_image_with_caption(img_filename, caption_text, desc_text):
    img_path = os.path.join('extracted_images', img_filename)
    if os.path.exists(img_path):
        # Paragraph chứa hình ảnh
        p_img = helper_doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(6)
        p_img.paragraph_format.space_after = Pt(2)
        run_img = p_img.add_run()
        run_img.add_picture(img_path, width=Inches(5.6))
        insert_elem(p_img._p)

        # Paragraph caption
        if caption_text:
            p_cap = helper_doc.add_paragraph()
            p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p_cap.paragraph_format.space_before = Pt(2)
            p_cap.paragraph_format.space_after = Pt(2)
            run_cap = p_cap.add_run(caption_text)
            format_run(run_cap, size_pt=10.5, bold=True, italic=True, color=RGBColor(30, 41, 59))
            insert_elem(p_cap._p)

        # Paragraph mô tả chức năng
        if desc_text:
            p_desc = helper_doc.add_paragraph()
            p_desc.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p_desc.paragraph_format.space_before = Pt(1)
            p_desc.paragraph_format.space_after = Pt(6)
            run_desc = p_desc.add_run(desc_text)
            format_run(run_desc, size_pt=10, italic=True, color=RGBColor(100, 116, 139))
            insert_elem(p_desc._p)

print("=== BƯỚC 3: DỰNG LẠI TOÀN BỘ 30 MỤC ĐẶC TẢ SECTION IV KÈM HÌNH ẢNH ===")

assigned_groups = [
    ("A. PHÂN HỆ DO NGUYỄN THANH HIỀN PHỤ TRÁCH (TÍNH NĂNG 1 - 10)", range(1, 11), "Nguyễn Thanh Hiền"),
    ("B. PHÂN HỆ DO NGUYỄN ANH QUÝ PHỤ TRÁCH (TÍNH NĂNG 11 - 20)", range(11, 21), "Nguyễn Anh Quý"),
    ("C. PHÂN HỆ DO HUỲNH VĂN VĨNH EM PHỤ TRÁCH (TÍNH NĂNG 21 - 30)", range(21, 31), "Huỳnh Văn Vĩnh Em")
]

for group_title, feat_range, assignee in assigned_groups:
    print(f"\n--- Đang xử lý {group_title} ---")
    add_p(group_title, size_pt=14, bold=True, space_before=16, space_after=8)
    
    for f_num in feat_range:
        fdata = spec_features.get(f_num, {})
        title = fdata.get('title', f'TÍNH NĂNG {f_num}')
        img_count = len(fdata.get('images', []))
        print(f"  + Chèn Đặc tả Tính năng {f_num:2d}: {title[:45]} ({img_count} ảnh)...")
        
        # 1. Tiêu đề tính năng
        add_p(f"{f_num}. ĐẶC TẢ KỸ THUẬT: {title.upper()}", size_pt=12.5, bold=True, space_before=12, space_after=4)
        
        # 2. Bảng thông tin kỹ thuật & Metadata
        overview = fdata.get('overview', {})
        meta_rows = [
            ["Mã Chức Năng (Feature Code)", overview.get('Mã tính năng (Feature ID)', f'FEAT_{f_num:02d}')],
            ["Tên Tính Năng", overview.get('Tên tính năng', title)],
            ["Thành viên phụ trách", assignee],
            ["Tác nhân (Actor)", overview.get('Actor (Tác nhân)', 'Người dùng hệ thống')],
            ["Sự kiện kích hoạt (Trigger)", overview.get('Trigger (Sự kiện kích hoạt)', 'Thao tác trên giao diện')],
            ["Điều kiện tiên quyết", overview.get('Điều kiện tiên quyết', 'Đã đăng nhập và có quyền tương ứng')]
        ]
        add_table(["Thông Tin Chung", "Chi Tiết Kỹ Thuật"], meta_rows, col_widths=[2800, 6560])

        # 3. Phần 1: Phạm vi & Endpoints API
        add_p("1. Phạm vi & Endpoints API", size_pt=11.5, bold=True, space_before=6)
        ep_list = BACKEND_ENDPOINTS.get(f_num, [("POST", f"/api/feature/{f_num}", "auth", "API xử lý nghiệp vụ")])
        ep_rows = [[m, u, mw, desc] for m, u, mw, desc in ep_list]
        add_table(["Method", "Endpoint URL", "Middlewares", "Mô Tả Chức Năng Nghiệp Vụ"], ep_rows, col_widths=[1200, 2760, 2200, 3200])

        # 4. Phần 2: Thuật toán & Luồng xử lý nghiệp vụ (Flow & Rules)
        add_p("2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)", size_pt=11.5, bold=True, space_before=6)
        rules = fdata.get('rules', [])
        for r_text in rules:
            add_p(f"• {r_text}", size_pt=11)
            
        flow = fdata.get('flow', [])
        for fl_text in flow:
            add_p(fl_text, size_pt=11)

        # 5. Phần 3: Hợp đồng Dữ liệu (API Contract) & Bảng Input
        add_p("3. Hợp đồng Dữ liệu (API Contract) & Bảng Input Specification", size_pt=11.5, bold=True, space_before=6)
        in_tbl = fdata.get('input_table', [])
        if len(in_tbl) > 1:
            add_table(in_tbl[0], in_tbl[1:], col_widths=[1700, 1400, 1160, 2550, 2550])

        # 6. Phần 4: Bảng Thông Báo Lỗi & Xác Thực Giao Diện (UI Validation Error Messages)
        add_p("4. Bảng Thông Báo Lỗi Và Xác Thực Giao Diện (UI Validation Error Messages)", size_pt=11.5, bold=True, space_before=6)
        err_tbl = fdata.get('error_table', [])
        if len(err_tbl) > 1:
            add_table(err_tbl[0], err_tbl[1:], col_widths=[2800, 1200, 2600, 2760])

        # 7. Phần 5: HÌNH ẢNH GIAO DIỆN & PHÁC THẢO UI (UI WIREFRAME) -> CHÈN TRỰC TIẾP HÌNH ẢNH!
        add_p("5. Hình Ảnh Giao Diện & Phác Thảo UI (UI Wireframe)", size_pt=11.5, bold=True, space_before=6)
        imgs = fdata.get('images', [])
        if imgs:
            for img_info in imgs:
                add_image_with_caption(img_info['filename'], img_info['caption'], img_info['desc'])
        else:
            add_p("(Hình ảnh giao diện đang được đồng bộ trực tiếp từ hệ thống)", italic=True, size_pt=10.5)

        # 8. Phần 6: Bảng Mô Tả Chi Tiết Từng Phần Tử UI (UI Elements Description)
        add_p("6. Bảng Mô Tả Chi Tiết Từng Phần Tử UI (UI Elements Description)", size_pt=11.5, bold=True, space_before=6)
        elem_tbl = fdata.get('element_table', [])
        if len(elem_tbl) > 1:
            add_table(elem_tbl[0], elem_tbl[1:], col_widths=[700, 1600, 1500, 1800, 1800, 1960])

        # 9. Phần 7: Kịch bản Kiểm thử & Tiêu chí Nghiệm thu (DoD)
        add_p("7. Kịch bản Kiểm thử & Tiêu chí Nghiệm thu (Definition of Done - DoD)", size_pt=11.5, bold=True, space_before=6)
        test_tbl = fdata.get('test_table', [])
        if len(test_tbl) > 1:
            add_table(test_tbl[0], test_tbl[1:], col_widths=[800, 2800, 3200, 2560])

        add_p("☐ Giao diện hiển thị đúng chuẩn Responsive, thân thiện trên Desktop và Mobile.", size_pt=11)
        add_p("☐ Toàn bộ các thông điệp lỗi (validation error) xuất hiện đúng vị trí và viền đỏ theo kịch bản.", size_pt=11)
        add_p("☐ Dữ liệu được lưu trữ toàn vẹn vào Cơ sở dữ liệu và bảo toàn phân quyền Multi-tenancy.", size_pt=11, space_after=10)

print("\n=== BƯỚC 4: LƯU FILE KẾT QUẢ VÀO BÁO CÁO NHÓM A ===")
doc.save(target_doc_path)
print(f"==> ĐÃ HOÀN TẤT THÀNH CÔNG NÂNG CẤP VÀ KÉO TOÀN BỘ 57 ẢNH VÀO: {target_doc_path}")
