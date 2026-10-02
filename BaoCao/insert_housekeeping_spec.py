# -*- coding: utf-8 -*-
import sys
import os
import docx
from docx.shared import Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls
from copy import deepcopy

sys.stdout.reconfigure(encoding='utf-8')

doc_path = 'Báo cáo nhóm A.docx'
if not os.path.exists(doc_path):
    doc_path = 'BaoCao/Báo cáo nhóm A.docx'

print(f"Đang mở file: {doc_path}")
doc = docx.Document(doc_path)

def set_cell_shading(cell, color_hex="F1F5F9"):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{color_hex}"/>')
    tcPr.append(shd)

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

def set_cell_width(cell, width_dxa):
    tcPr = cell._tc.get_or_add_tcPr()
    tcW = parse_xml(f'<w:tcW {nsdecls("w")} w:w="{width_dxa}" w:type="dxa"/>')
    tcPr.append(tcW)

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

# Tìm đoạn P bắt đầu mục 8 cũ (QUẢN LÝ THIẾT BỊ KHO)
target_p_idx = -1
for i, p in enumerate(doc.paragraphs):
    if '8. ĐẶC TẢ KỸ THUẬT: QUẢN LÝ THIẾT BỊ KHO' in p.text:
        target_p_idx = i
        break

if target_p_idx == -1:
    print("Không tìm thấy vị trí mục 8 cũ!")
    sys.exit(1)

print(f"Tìm thấy vị trí mục 8 cũ tại P[{target_p_idx}]: {doc.paragraphs[target_p_idx].text}")
target_p = doc.paragraphs[target_p_idx]
target_elem = target_p._p

# Tạo helper doc để tạo các phần tử mới
helper = docx.Document()

def create_p(text, bold=False, italic=False, font_size=11, align=WD_ALIGN_PARAGRAPH.LEFT, space_before=2, space_after=3):
    p = helper.add_paragraph()
    p.alignment = align
    p.paragraph_format.space_before = Pt(space_before)
    p.paragraph_format.space_after = Pt(space_after)
    p.paragraph_format.line_spacing = 1.15
    run = p.add_run(text)
    format_run(run, size_pt=font_size, bold=bold, italic=italic)
    return p

def create_table(headers, data_rows, col_widths=None):
    num_cols = len(headers)
    tbl = helper.add_table(rows=len(data_rows) + 1, cols=num_cols)
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
        set_cell_margins(c, top=120, bottom=120, left=140, right=140)
        for p in c.paragraphs:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after = Pt(2)
            for r in p.runs:
                format_run(r, size_pt=11.5, bold=True, color=RGBColor(15, 23, 42))

    # Data rows
    for r_i, d_row in enumerate(data_rows):
        row = tbl.rows[r_i + 1]
        bg_color = "FFFFFF" if r_i % 2 == 0 else "F8FAFC"
        for c_i in range(num_cols):
            c = row.cells[c_i]
            val = d_row[c_i] if c_i < len(d_row) else ""
            c.text = val
            set_cell_width(c, col_widths[c_i] if c_i < len(col_widths) else 1500)
            set_cell_shading(c, bg_color)
            set_cell_margins(c, top=100, bottom=100, left=140, right=140)
            for p in c.paragraphs:
                p.paragraph_format.space_before = Pt(2)
                p.paragraph_format.space_after = Pt(2)
                for r in p.runs:
                    format_run(r, size_pt=11)
    return tbl

# Chuẩn bị danh sách phần tử cần chèn trước target_elem
elements_to_insert = []

# 1. Heading Mục 8 mới
p_head = create_p("8. ĐẶC TẢ KỸ THUẬT: PHÂN HỆ LỄ TÂN & BUỒNG PHÒNG (DỌN PHÒNG - HOUSEKEEPING & FRONT DESK MANAGEMENT THEO MÁY TRẠNG THÁI FSM)", bold=True, font_size=13, space_before=12, space_after=4)
elements_to_insert.append(p_head._p)

# Bảng Thông tin chung
tbl_info = create_table(
    ["Thông Tin Chung", "Chi Tiết"],
    [
        ["Mã Chức Năng (Feature Code)", "FEAT-QUY-08 / FEAT_18_HOUSEKEEPING_FRONTDESK"],
        ["Loại tác vụ (Type)", "Front Desk & Housekeeping / Finite State Machine / Staff Task Dispatching"],
        ["Nhánh Git (Branches)", "main, feature/housekeeping-fsm-workflow"],
        ["Người thực hiện (Assignee)", "Nguyễn Anh Quý (Thành Viên)"],
        ["Package / Standards", "Finite State Machine (FSM), Mobile-first Housekeeping UI, Reverb WebSockets, RFID Room Keycard"]
    ],
    col_widths=[2800, 6560]
)
elements_to_insert.append(tbl_info._tbl)

# 2. Phần 1: Phạm vi & Endpoints API
p_sec1 = create_p("1. Phạm vi & Endpoints API", bold=True, font_size=12, space_before=6)
elements_to_insert.append(p_sec1._p)

tbl_endpoints = create_table(
    ["Method", "Endpoint URL", "Middlewares", "Mô Tả Chức Năng Nghiệp Vụ"],
    [
        ["GET", "/smartroom/admin/housekeeping/matrix", "auth, tenant.scope", "Sơ đồ buồng phòng thời gian thực, hiển thị trực quan mã màu FSM dọn phòng"],
        ["POST", "/smartroom/admin/housekeeping/assign", "auth, tenant.scope", "Điều phối và phân công nhân viên buồng phòng dọn dẹp theo ca hoặc theo phòng"],
        ["POST", "/smartroom/admin/housekeeping/status", "auth, tenant.scope", "Cập nhật tiến độ dọn phòng (Bắt đầu dọn -> Hoàn thành dọn sạch sẽ)"],
        ["POST", "/smartroom/admin/housekeeping/inspect", "auth, tenant.scope", "Lễ tân/Trưởng ca nghiệm thu buồng phòng đạt chuẩn đón khách lưu trú mới"],
        ["POST", "/smartroom/admin/frontdesk/checkin", "auth, tenant.scope", "Tiếp nhận Check-in khách (Cơ chế Guard Check chặn tuyệt đối phòng Dirty)"],
        ["POST", "/smartroom/admin/frontdesk/checkout", "auth, tenant.scope", "Quyết toán Check-out, tự động chuyển phòng sang Dirty và đối soát trừ kho minibar"]
    ],
    col_widths=[1200, 2760, 2200, 3200]
)
elements_to_insert.append(tbl_endpoints._tbl)

# 3. Phần 2: Thuật toán & Luồng xử lý nghiệp vụ
p_sec2 = create_p("2. Thuật toán & Luồng xử lý nghiệp vụ (Business Logic Flow)", bold=True, font_size=12, space_before=6)
elements_to_insert.append(p_sec2._p)

p_fsm1 = create_p("• **Mô hình Máy Trạng thái Hữu hạn Buồng phòng (Housekeeping FSM Finite State Machine):**", bold=True, font_size=11)
elements_to_insert.append(p_fsm1._p)
p_fsm2 = create_p("$$\\text{dirty (Cần dọn)} \\xrightarrow{\\text{Nhận việc/Bắt đầu}} \\text{cleaning (Đang dọn)} \\xrightarrow{\\text{Báo dọn xong}} \\text{clean (Sạch)} \\xrightarrow{\\text{Nghiệm thu}} \\text{inspected (Sẵn sàng)}$$", font_size=11)
elements_to_insert.append(p_fsm2._p)
p_fsm3 = create_p("$$\\text{inspected / clean} \\xrightarrow{\\text{Check-in đón khách}} \\text{occupied (Có người ở)} \\xrightarrow{\\text{Check-out trả phòng}} \\text{dirty (Cần dọn)}$$", font_size=11)
elements_to_insert.append(p_fsm3._p)
p_fsm4 = create_p("• **Cơ chế Guard Check an toàn tuyệt đối:** Hệ thống kích hoạt Barrier chặn đứng mọi thao tác Check-in nếu trạng thái buồng phòng chưa đạt 'clean' hoặc 'inspected'. Khi khách trả phòng (Check-out), hệ thống tự động sinh Task buồng phòng và phát bản tin WebSockets cảnh báo đỏ trên sơ đồ ma trận.", font_size=11)
elements_to_insert.append(p_fsm4._p)
p_fsm5 = create_p("• **Quản lý Tiêu hao Minibar & Bàn giao:** Khi dọn phòng, nhân viên ghi nhận số lượng nước ngọt/vật tư khách đã tiêu thụ, tự động đẩy dữ liệu sang Bảng kê Folio khách hàng để khấu trừ tiền cọc hoặc thanh toán phát sinh.", font_size=11)
elements_to_insert.append(p_fsm5._p)

# 4. Phần 3: Hợp đồng Dữ liệu (API Contract)
p_sec3 = create_p("3. Hợp đồng Dữ liệu (API Contract)", bold=True, font_size=12, space_before=6)
elements_to_insert.append(p_sec3._p)

p_out_title = create_p("3.1. Output khi Cập nhật Trạng thái Buồng phòng Thành công", bold=True, font_size=11)
elements_to_insert.append(p_out_title._p)
p_out_code = create_p("• **HTTP Status Code:** 200 OK\n{\n  \"success\": true,\n  \"room_id\": 204,\n  \"room_number\": \"P.204\",\n  \"housekeeping_status\": \"inspected\",\n  \"inspector\": \"Nguyễn Văn Lễ Tân\",\n  \"ready_for_checkin\": true,\n  \"message\": \"Phòng P.204 đã được nghiệm thu đạt chuẩn và sẵn sàng đón khách mới!\"\n}", font_size=10)
elements_to_insert.append(p_out_code._p)

p_in_title = create_p("3.2. Quy tắc Xác thực Dữ liệu Đầu vào (`POST /smartroom/admin/housekeeping/status`)", bold=True, font_size=11)
elements_to_insert.append(p_in_title._p)

tbl_input = create_table(
    ["Tên Field", "Kiểu Dữ Liệu", "Bắt Buộc", "Validation Rules (Laravel)", "Thông Báo Lỗi Nghiệp Vụ (Message)"],
    [
        ["room_id", "Integer", "Có", "required|integer|exists:rooms,id", "Phòng lưu trú cần thao tác buồng phòng không tồn tại trong hệ thống."],
        ["housekeeping_status", "String", "Có", "required|in:dirty,cleaning,clean,inspected,out_of_service", "Trạng thái vệ sinh buồng phòng không hợp lệ theo chuẩn FSM."],
        ["assigned_staff_id", "Integer", "Không", "nullable|integer|exists:users,id", "Nhân viên buồng phòng được phân công không tồn tại."],
        ["priority", "String", "Có", "required|in:urgent,high,normal,low", "Mức độ ưu tiên dọn dẹp buồng phòng không hợp lệ."],
        ["consumed_items", "Array", "Không", "nullable|array", "Danh sách vật tư tiêu hao minibar không đúng định dạng mảng."],
        ["inspection_notes", "String", "Không", "nullable|string|max:500", "Ghi chú kiểm tra tình trạng buồng phòng tối đa 500 ký tự."]
    ],
    col_widths=[1700, 1400, 1160, 2550, 2550]
)
elements_to_insert.append(tbl_input._tbl)

# 5. Phần 4: Tương tác Cơ sở Dữ liệu
p_sec4 = create_p("4. Tương tác Cơ sở Dữ liệu & Quản lý Trạng thái (Database Interactions)", bold=True, font_size=12, space_before=6)
elements_to_insert.append(p_sec4._p)
p_db1 = create_p("• **Bảng tác động:** `rooms` (cập nhật cột `housekeeping_status`, `inspected_at`), `housekeeping_tasks` (lưu vết phân công và thời gian dọn dẹp), `minibar_consumptions` (ghi nhận trừ kho vật tư).", font_size=11)
elements_to_insert.append(p_db1._p)
p_db2 = create_p("• **Realtime Dispatching:** Phát sự kiện `HousekeepingStatusUpdatedEvent` qua Laravel Reverb / SSE để cập nhật tức thời màu sắc thẻ phòng trên màn hình Lễ tân mà không cần tải lại trang.", font_size=11)
elements_to_insert.append(p_db2._p)

# 6. Phần 5: Ma trận Xử lý Ngoại lệ
p_sec5 = create_p("5. Ma trận Xử lý Ngoại lệ (Exception & Error Handling)", bold=True, font_size=12, space_before=6)
elements_to_insert.append(p_sec5._p)

p_ui_err_title = create_p("A. Kịch bản Ngoại lệ & Kiểm thử Giao diện (UI/UX Edge Cases & Error States)", bold=True, font_size=11)
elements_to_insert.append(p_ui_err_title._p)

tbl_ui_err = create_table(
    ["Nguyên Nhân / Tình Huống Thao Tác", "Hiện Tượng Lỗi & Hướng Khắc Phục UI/UX"],
    [
        ["Chưa chọn phòng cần phân công dọn (ERR_18_01)", "Bôi đỏ viền thẻ phòng trên ma trận, rung nhẹ kèm thông báo: 'Vui lòng chọn ít nhất một phòng cần phân công dọn dẹp.'"],
        ["Cố tình Check-in vào phòng đang Dirty (ERR_18_02)", "Modal cảnh báo đỏ chặn nút Check-in: 'Phòng đang ở trạng thái Cần dọn. Không thể Check-in khách!'"],
        ["Chưa phân công nhân viên dọn phòng (ERR_18_03)", "Bôi đỏ dropdown chọn Nhân viên phụ trách và hiển thị cảnh báo yêu cầu gán nhân sự."],
        ["Chuyển trạng thái FSM buồng phòng sai quy trình (ERR_18_04)", "Hiển thị Toast cảnh báo đỏ góc màn hình: 'Chuyển trạng thái không hợp lệ (Phòng phải qua bước Sạch trước khi Nghiệm thu).'"],
        ["Số lượng vật tư tiêu hao minibar âm (ERR_18_05)", "Bôi đỏ ô nhập số lượng vật tư minibar: 'Số lượng vật tư tiêu hao minibar phải là số nguyên không âm.'"],
        ["Nghiệm thu buồng phòng thành công (ERR_18_06)", "Hiển thị Toast xanh lá góc phải: 'Đã nghiệm thu buồng phòng thành công! Phòng đã sẵn sàng đón khách lưu trú mới.'"]
    ],
    col_widths=[3300, 6060]
)
elements_to_insert.append(tbl_ui_err._tbl)

p_be_err_title = create_p("B. Tầng Máy chủ Backend (Server-side / HTTP Response)", bold=True, font_size=11)
elements_to_insert.append(p_be_err_title._p)

tbl_be_err = create_table(
    ["Tình Huống Phát Sinh Lỗi", "Mã HTTP", "Error Code", "Xử Lý Kỹ Thuật & Thông Báo Lỗi / Gotchas"],
    [
        ["Cố tình Check-in vào phòng chưa sạch", "422", "ROOM_NOT_READY_FOR_CHECKIN", "Chặn đứng Check-in và trả về mã lỗi: 'Phòng chưa được nghiệm thu buồng phòng đạt chuẩn.'"],
        ["Nhảy cóc trạng thái FSM trái quy trình", "400", "INVALID_FSM_TRANSITION", "Báo lỗi: 'Quy trình chuyển đổi trạng thái buồng phòng không hợp lệ theo máy trạng thái FSM.'"],
        ["Nhân viên được phân công không thuộc cơ sở", "403", "STAFF_TENANT_MISMATCH", "Phản hồi HTTP 403 Forbidden chặn nhân sự ngoài tòa nhà/cơ sở lưu trú."]
    ],
    col_widths=[2800, 1160, 2400, 3000]
)
elements_to_insert.append(tbl_be_err._tbl)

# 7. Phần 6: Tiêu chí Nghiệm thu
p_sec6 = create_p("6. Tiêu chí Nghiệm thu (Definition of Done - DoD)", bold=True, font_size=12, space_before=6)
elements_to_insert.append(p_sec6._p)
p_dod1 = create_p("☐ Cơ chế Guard Check chặn đứng 100% các hành vi Check-in vào phòng bẩn (Dirty) hoặc đang dọn (Cleaning).", font_size=11)
elements_to_insert.append(p_dod1._p)
p_dod2 = create_p("☐ Quy trình luân chuyển buồng phòng tuân thủ nghiêm ngặt máy trạng thái FSM, thời gian cập nhật thẻ phòng dưới 300ms.", font_size=11)
elements_to_insert.append(p_dod2._p)
p_dod3 = create_p("☐ Ghi nhận tiêu hao vật tư minibar chuẩn xác từng món và đồng bộ trực tiếp vào hóa đơn Check-out của khách.", font_size=11, space_after=12)
elements_to_insert.append(p_dod3._p)

# Chèn toàn bộ các phần tử vào trước target_elem
print(f"Đang chèn {len(elements_to_insert)} phần tử mới của Mục 8 Lễ tân buồng phòng...")
for elem in elements_to_insert:
    target_elem.addprevious(deepcopy(elem))

print("-> Đã chèn thành công Mục 8 mới!")

# Đổi lại số thứ tự các mục phía sau:
# Mục 8 cũ (QUẢN LÝ THIẾT BỊ KHO) -> Mục 9
doc.paragraphs[target_p_idx].text = doc.paragraphs[target_p_idx].text.replace("8. ĐẶC TẢ KỸ THUẬT: QUẢN LÝ THIẾT BỊ KHO", "9. ĐẶC TẢ KỸ THUẬT: QUẢN LÝ THIẾT BỊ KHO")

# Mục 9 cũ (SỔ QUỸ THU - CHI) -> Mục 10
for i in range(target_p_idx, len(doc.paragraphs)):
    if '9. ĐẶC TẢ KỸ THUẬT: SỔ QUỸ THU - CHI' in doc.paragraphs[i].text:
        doc.paragraphs[i].text = doc.paragraphs[i].text.replace("9. ĐẶC TẢ KỸ THUẬT: SỔ QUỸ THU - CHI", "10. ĐẶC TẢ KỸ THUẬT: SỔ QUỸ THU - CHI")
        print(f"-> Đã đổi Mục 9 cũ thành Mục 10 tại P[{i}]")
        break

# Mục 10 cũ (AI HỖ TRỢ TOÀN DIỆN) -> Mục 11
for i in range(target_p_idx, len(doc.paragraphs)):
    if '10. ĐẶC TẢ KỸ THUẬT: HỆ SINH THÁI TRỢ LÝ AI' in doc.paragraphs[i].text:
        doc.paragraphs[i].text = doc.paragraphs[i].text.replace("10. ĐẶC TẢ KỸ THUẬT: HỆ SINH THÁI TRỢ LÝ AI", "11. ĐẶC TẢ KỸ THUẬT: HỆ SINH THÁI TRỢ LÝ AI")
        print(f"-> Đã đổi Mục 10 cũ thành Mục 11 tại P[{i}]")
        break

# Lưu file kết quả
doc.save(doc_path)
print(f"==> HOÀN TẤT CHÈN MỤC LỄ TÂN BUỒNG PHÒNG VÀO: {doc_path}")
