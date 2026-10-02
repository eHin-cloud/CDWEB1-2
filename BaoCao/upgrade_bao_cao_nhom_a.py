# -*- coding: utf-8 -*-
import sys
import os
import docx
from docx.shared import Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls

sys.stdout.reconfigure(encoding='utf-8')

doc_path = 'Báo cáo nhóm A.docx'
if not os.path.exists(doc_path):
    doc_path = 'BaoCao/Báo cáo nhóm A.docx'

print(f"Bắt đầu nâng cấp Báo cáo nhóm A: {doc_path}")
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

def set_cell_text(cell, text, bold=False, italic=False, color=None, font_size=11, align=WD_ALIGN_PARAGRAPH.LEFT):
    cell.text = ''
    p = cell.paragraphs[0]
    p.alignment = align
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(2)
    p.paragraph_format.line_spacing = 1.15
    run = p.add_run(text)
    format_run(run, font_name="Times New Roman", size_pt=font_size, bold=bold, italic=italic, color=color)

def set_para_text(p, text, bold=False, italic=False, font_size=13, align=WD_ALIGN_PARAGRAPH.LEFT, space_before=2, space_after=3):
    p.text = ''
    p.alignment = align
    p.paragraph_format.space_before = Pt(space_before)
    p.paragraph_format.space_after = Pt(space_after)
    p.paragraph_format.line_spacing = 1.15
    run = p.add_run(text)
    format_run(run, font_name="Times New Roman", size_pt=font_size, bold=bold, italic=italic)

# ==================== 1. CẬP NHẬT BẢNG 3: PHÂN CHIA CÔNG VIỆC ====================
print("1. Đang cập nhật Bảng 3 (Phân chia công việc)...")
t3 = doc.tables[3]
# Row 14: Công việc 4 của Quý (AI Viết bài SEO -> AI Hỗ trợ toàn diện)
row14_cells = t3.rows[14].cells
set_cell_text(row14_cells[2], "Hệ sinh thái Trợ lý AI Hỗ trợ toàn diện (Tra cứu phòng, Cố vấn chủ trọ & Tiếp nhận sự cố)")

# Row 18: Công việc 8 của Quý (Nhắc nợ Zalo -> Lễ tân dọn phòng)
row18_cells = t3.rows[18].cells
set_cell_text(row18_cells[2], "Phân hệ Lễ tân & Quản lý buồng phòng, dọn phòng (Housekeeping & Front Desk)")
print("-> Đã cập nhật Bảng 3 thành công!")

# ==================== 2. CẬP NHẬT BẢNG 5: ENDPOINT API ====================
print("2. Đang cập nhật Bảng 5 (Danh mục API Endpoint)...")
t5 = doc.tables[5]
# Row 28: AI Viết mô tả phòng
r28 = t5.rows[28].cells
set_cell_text(r28[0], "Đa phân hệ")
set_cell_text(r28[1], "Hệ sinh thái Trợ lý AI Hỗ trợ toàn diện (Tra cứu, Cố vấn & Sự cố)")
set_cell_text(r28[2], "POST")
set_cell_text(r28[3], "/api/ai/assistant-hub")
set_cell_text(r28[4], "Hệ sinh thái AI đa tác nhân: Tra cứu phòng cho khách thuê, Cố vấn kinh doanh & viết bài SEO cho chủ trọ, Tiếp nhận & phân loại sự cố khẩn cấp cho cư dân")

# Row 40: Nhắc nợ Zalo
r40 = t5.rows[40].cells
set_cell_text(r40[0], "Lễ tân / Chủ trọ")
set_cell_text(r40[1], "Phân hệ Lễ tân & Buồng phòng (Dọn phòng)")
set_cell_text(r40[2], "POST")
set_cell_text(r40[3], "/smartroom/admin/housekeeping/status")
set_cell_text(r40[4], "Quản lý vòng đời buồng phòng theo máy trạng thái FSM (Clean, Dirty, Cleaning, Inspected), điều phối nhân viên dọn phòng và check-in/out khách")
print("-> Đã cập nhật Bảng 5 thành công!")

# ==================== 3. CẬP NHẬT MỤC 7 CỦA QUÝ (BỘ MÁY TÍNH CƯỚC & VIETQR) ====================
print("3. Đang cập nhật Mục 7 (Bộ máy tính cước & VietQR NAPAS247)...")
# Paragraph 843: Tiêu đề
set_para_text(doc.paragraphs[843], "7. ĐẶC TẢ KỸ THUẬT: BỘ MÁY TÍNH CƯỚC TỰ ĐỘNG, HÓA ĐƠN ĐIỆN TỬ & MÃ THANH TOÁN VIETQR NAPAS247 ĐỘNG", bold=True, font_size=13)

# Table 157: Thông tin chung
t157 = doc.tables[157]
set_cell_text(t157.rows[1].cells[1], "FEAT-QUY-07")
set_cell_text(t157.rows[2].cells[1], "Financial Billing Engine / NAPAS247 Dynamic QR / Automated Invoicing")
set_cell_text(t157.rows[3].cells[1], "main, feature/billing-engine-vietqr-invoicing")
set_cell_text(t157.rows[4].cells[1], "Nguyễn Anh Quý (Thành Viên)")
set_cell_text(t157.rows[5].cells[1], "BillingEngine, VietQR Image API, NAPAS247 Standard, DomPDF Invoicing")

# Table 158: Endpoints API
t158 = doc.tables[158]
while len(t158.rows) > 1:
    tr = t158.rows[-1]._tr
    tr.getparent().remove(tr)

data_t158 = [
    ("POST", "/smartroom/admin/utility/calculate", "auth, tenant.scope", "Tính toán tổng cước phí hóa đơn theo công thức động đa dịch vụ"),
    ("GET", "/smartroom/admin/utility/{id}/vietqr", "auth, tenant.scope", "Xuất mã QR thanh toán động VietQR NAPAS247 chuẩn ngân hàng"),
    ("POST", "/smartroom/admin/utility/{id}/invoice-pdf", "auth, tenant.scope", "Kết xuất hóa đơn điện tử PDF chuẩn in kèm bảng kê chi tiết và mã QR thanh toán")
]
for row_vals in data_t158:
    row = t158.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v)

# Table 159: Input Request Validation
t159 = doc.tables[159]
while len(t159.rows) > 1:
    tr = t159.rows[-1]._tr
    tr.getparent().remove(tr)

data_t159 = [
    ("room_id", "Integer", "Có", "required|integer|exists:rooms,id", "Phòng lưu trú cần lập hóa đơn không tồn tại trong hệ thống."),
    ("billing_month", "String", "Có", "required|date_format:Y-m", "Kỳ tính tiền hóa đơn không đúng định dạng chuẩn YYYY-MM."),
    ("electricity_reading", "Numeric", "Có", "required|numeric|gte:0", "Chỉ số điện mới phải là số thực lớn hơn hoặc bằng 0."),
    ("water_reading", "Numeric", "Có", "required|numeric|gte:0", "Chỉ số nước mới phải là số thực lớn hơn hoặc bằng 0."),
    ("additional_fees", "Array", "Không", "nullable|array", "Danh sách chi phí phụ thu phát sinh không hợp lệ.")
]
for row_vals in data_t159:
    row = t159.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v)

# Cập nhật đoạn văn P[853]-P[856] (Loại bỏ nhắc nợ Zalo)
set_para_text(doc.paragraphs[853], "• **Cơ chế Khóa Hóa Đơn & Bảo Toàn Dữ liệu:**", bold=True, font_size=12)
set_para_text(doc.paragraphs[854], "• Khi hóa đơn được tạo ở trạng thái 'sent', hệ thống tự động chốt số dư và đóng băng các chỉ số điện nước để ngăn chặn chỉnh sửa trái phép.", font_size=11)
set_para_text(doc.paragraphs[855], "• Mã VietQR động NAPAS247 được sinh trực tiếp chứa đầy đủ số tiền chính xác, tên chủ tài khoản và mã hóa đơn.", font_size=11)
set_para_text(doc.paragraphs[856], "• Hỗ trợ xuất file PDF hóa đơn tiền phòng chuẩn mẫu hóa đơn điện tử với mã vạch QR đối soát tức thời.", font_size=11)

# Table 160: Kịch bản lỗi UI/UX
t160 = doc.tables[160]
while len(t160.rows) > 1:
    tr = t160.rows[-1]._tr
    tr.getparent().remove(tr)

data_t160 = [
    ("Nhấn nút Tính tiền khi chưa nhập chỉ số điện nước", "Hiển thị viền đỏ tại các ô chỉ số còn thiếu và cảnh báo: 'Vui lòng nhập đầy đủ chỉ số điện nước mới.'"),
    ("Ảnh mã VietQR NAPAS247 tải chậm do mạng", "Hiển thị khung hình vuông Skeleton kích thước chuẩn QR tránh xô lệch khung hóa đơn."),
    ("Quét mã VietQR trên App ngân hàng di động", "Hiển thị chính xác số tiền, số tài khoản thụ hưởng và đúng cú pháp nội dung chuyển khoản tự động."),
    ("Chỉ số điện/nước mới nhỏ hơn chỉ số cũ", "Báo lỗi viền đỏ: 'Chỉ số mới không được nhỏ hơn chỉ số cũ (trừ trường hợp đã xác nhận thay đồng hồ).'"),
    ("Số tiền cước phí bị lẻ số thập phân", "Tự động làm tròn số tiền về số nguyên trước khi sinh mã QR và ghi nhận công nợ.")
]
for row_vals in data_t160:
    row = t160.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v)

# Table 161: Mã lỗi Backend
t161 = doc.tables[161]
while len(t161.rows) > 1:
    tr = t161.rows[-1]._tr
    tr.getparent().remove(tr)

data_t161 = [
    ("Cấu hình tài khoản ngân hàng chủ trọ bị thiếu BIN/STK", "422", "VIETQR_CONFIG_MISSING", "Báo lỗi: 'Chưa cấu hình Số tài khoản hoặc Mã ngân hàng thụ hưởng trong hồ sơ chủ trọ.'"),
    ("Kỳ hóa đơn của phòng đã được tạo trước đó", "422", "BILLING_PERIOD_ALREADY_EXISTS", "Báo lỗi: 'Hóa đơn tiền phòng kỳ tháng {MM/YYYY} đã tồn tại. Vui lòng kiểm tra lại!'"),
    ("Chỉ số điện nước không hợp lệ", "422", "INVALID_METER_READINGS", "Báo lỗi: 'Chỉ số tiêu thụ mới không hợp lệ hoặc nhỏ hơn chỉ số kỳ trước.'")
]
for row_vals in data_t161:
    row = t161.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v)

# Tiêu chí DoD mục 7
set_para_text(doc.paragraphs[869], "☐ Hóa đơn tính chuẩn xác từng đồng theo công thức và bảng giá dịch vụ.", font_size=11)
set_para_text(doc.paragraphs[870], "☐ Mã VietQR quét thành công trên App ngân hàng thực tế, hiển thị đúng thông tin nhận tiền.", font_size=11)
set_para_text(doc.paragraphs[871], "☐ Kết xuất hóa đơn PDF tải về mượt mà, định dạng rõ ràng, chuyên nghiệp.", font_size=11)
print("-> Đã nâng cấp Mục 7 thành công!")

# ==================== 4. CẬP NHẬT MỤC 10 CỦA QUÝ (AI HỖ TRỢ TOÀN DIỆN) ====================
print("4. Đang cập nhật Mục 10 (Hệ sinh thái Trợ lý AI Hỗ trợ toàn diện)...")
# Paragraph 926: Tiêu đề
set_para_text(doc.paragraphs[926], "10. ĐẶC TẢ KỸ THUẬT: HỆ SINH THÁI TRỢ LÝ AI HỖ TRỢ TOÀN DIỆN (MULTI-AGENT AI ASSISTANT HUB: TRA CỨU PHÒNG, CỐ VẤN KINH DOANH CHỦ TRỌ & XỬ LÝ SỰ CỐ CƯ DÂN)", bold=True, font_size=13)

# Table 172: Thông tin chung
t172 = doc.tables[172]
set_cell_text(t172.rows[1].cells[1], "FEAT-QUY-10 / FEAT_14_AI_ASSISTANT_HUB")
set_cell_text(t172.rows[2].cells[1], "Multi-Agent Generative AI / Semantic Search RAG / Landlord Advisor / Incident Triage")
set_cell_text(t172.rows[3].cells[1], "main, feature/ai-assistant-multi-agent-hub")
set_cell_text(t172.rows[4].cells[1], "Nguyễn Anh Quý (Thành Viên)")
set_cell_text(t172.rows[5].cells[1], "Google Gemini 2.5 Flash, Multi-Agent Orchestration, Semantic Search RAG, NLP Triage, HTML5 Clipboard API")

# Table 173: Endpoints API
t173 = doc.tables[173]
while len(t173.rows) > 1:
    tr = t173.rows[-1]._tr
    tr.getparent().remove(tr)

data_t173 = [
    ("POST", "/api/ai/assistant-hub", "auth:api, throttle:30,1", "Endpoint trung tâm định tuyến đa tác nhân (Orchestration Hub)"),
    ("POST", "/api/ai/room-finder", "throttle:60,1", "Trợ lý tra cứu và gợi ý phòng trọ thông minh theo ngôn ngữ tự nhiên"),
    ("POST", "/api/ai/landlord-advisor", "auth, role:landlord, throttle:20,1", "Cố vấn kinh doanh, tối ưu giá thuê & sáng tạo bài viết marketing chuẩn SEO"),
    ("POST", "/api/ai/incident-triage", "auth, role:resident, throttle:20,1", "Tiếp nhận, phân loại khẩn cấp sự cố kỹ thuật & hướng dẫn sơ cứu an toàn")
]
for row_vals in data_t173:
    row = t173.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v)

# Cập nhật đoạn văn P[929]-P[934] (Kiến trúc 3 Agent)
set_para_text(doc.paragraphs[929], "• **Kiến trúc Hệ sinh thái AI Đa tác nhân (Multi-Agent Architecture):**", bold=True, font_size=12)
set_para_text(doc.paragraphs[930], "1. Bộ định tuyến Router (Intent Classifier): Phân tích ngữ nghĩa yêu cầu người dùng để chuyển giao chính xác đến 1 trong 3 Agent chuyên trách.", font_size=11)
set_para_text(doc.paragraphs[931], "2. RoomFinderAgent (Tra cứu phòng): Trích xuất thực thể (giá, quận/huyện, tiện ích), kết hợp Semantic Search & RAG truy vấn CSDL phòng trống và đối sánh gợi ý tối ưu.", font_size=11)
set_para_text(doc.paragraphs[932], "3. LandlordAdvisorAgent (Cố vấn chủ trọ): Tư vấn chiến lược giá thuê cạnh tranh theo thị trường, sáng tạo bài viết marketing chuẩn SEO đa kênh (Facebook, Chợ Tốt) kèm CTA hấp dẫn và phân tích điều khoản hợp đồng.", font_size=11)
set_para_text(doc.paragraphs[933], "4. TenantIncidentAgent (Xử lý sự cố): Phân tích triệu chứng kỹ thuật, xếp loại độ khẩn (Emergency/Normal), hướng dẫn biện pháp sơ cứu an toàn tức thời và tự động phát sinh ticket bảo trì.", font_size=11)
set_para_text(doc.paragraphs[934], "5. Trả về kết quả với hiệu ứng Typewriter streaming kèm cụm nút tương tác nhanh (Sao chép, Đặt lịch hẹn, Tạo ticket).", font_size=11)

# Table 174: Input Request Validation
t174 = doc.tables[174]
while len(t174.rows) > 1:
    tr = t174.rows[-1]._tr
    tr.getparent().remove(tr)

data_t174 = [
    ("agent_mode", "Enum", "Có", "required|in:room_finder,landlord_advisor,tenant_incident", "Chế độ tác vụ AI không hợp lệ."),
    ("prompt", "String", "Có", "required|string|min:5|max:2000", "Câu hỏi/yêu cầu tối thiểu 5 ký tự và không quá 2.000 ký tự."),
    ("context_id", "Integer", "Không", "nullable|integer", "ID đối tượng ngữ cảnh (phòng/hợp đồng) không hợp lệ."),
    ("tone", "Enum", "Không", "nullable|in:friendly,professional,urgent,creative_seo", "Văn phong phản hồi không hợp lệ."),
    ("incident_image", "File", "Không", "nullable|image|mimes:jpeg,png,webp|max:5120", "Ảnh chụp sự cố tối đa 5MB.")
]
for row_vals in data_t174:
    row = t174.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v)

# Table 175: Kịch bản lỗi UI/UX
t175 = doc.tables[175]
while len(t175.rows) > 1:
    tr = t175.rows[-1]._tr
    tr.getparent().remove(tr)

data_t175 = [
    ("Để trống câu hỏi khi gửi AI (ERR_14_01)", "Bôi đỏ viền khung chat, rung nhẹ và hiển thị: 'Vui lòng nhập câu hỏi hoặc yêu cầu cần trợ lý AI hỗ trợ.'"),
    ("Nội dung yêu cầu dưới 5 ký tự (ERR_14_02)", "Bôi đỏ ô input và báo lỗi: 'Nội dung yêu cầu quá ngắn (tối thiểu 5 ký tự) để AI có đủ ngữ cảnh xử lý.'"),
    ("Kết nối Google Gemini AI bị Timeout (ERR_14_03)", "Tự động kích hoạt câu trả lời từ kho tri thức mẫu dự phòng kèm Toast cảnh báo vàng (6 giây)."),
    ("Vượt quá giới hạn 20 truy vấn/phút (ERR_14_04)", "Khóa nút gửi, hiển thị đồng hồ đếm ngược Countdown 30 giây tránh quá tải hệ thống."),
    ("Nhấn nút Sao chép bài viết (ERR_14_05)", "Hiển thị Toast thông báo xanh lá góc phải: 'Đã sao chép nội dung bài viết marketing chuẩn SEO thành công!'"),
    ("AI phân loại sự cố khẩn cấp (ERR_14_06)", "Hiển thị Modal đỏ cam: 'Sự cố khẩn cấp đã được AI ghi nhận và chuyển ngay đến đội ngũ bảo trì tòa nhà!'")
]
for row_vals in data_t175:
    row = t175.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v)

# Table 176: Mã lỗi Backend
t176 = doc.tables[176]
while len(t176.rows) > 1:
    tr = t176.rows[-1]._tr
    tr.getparent().remove(tr)

data_t176 = [
    ("Quá hạn phản hồi API Gemini (> 6s)", "200 (Fallback)", "GEMINI_TIMEOUT_FALLBACK", "Kích hoạt kho tri thức đệm (Knowledge Cache) để phục vụ người dùng không gián đoạn."),
    ("Yêu cầu không thuộc quyền hạn phân quyền", "403", "AI_AGENT_ACCESS_DENIED", "Báo lỗi: 'Bạn không có quyền truy cập vào chế độ Trợ lý AI này.'"),
    ("Vượt hạn ngạch Rate Limit (20 req/min)", "429", "AI_RATE_LIMIT_EXCEEDED", "Phản hồi lỗi 429 kèm header Retry-After: 30.")
]
for row_vals in data_t176:
    row = t176.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v)

# Tiêu chí DoD mục 10
set_para_text(doc.paragraphs[947], "☐ Phản hồi câu trả lời tự nhiên, chính xác trong dưới 3 giây qua mô hình Gemini 2.5 Flash.", font_size=11)
set_para_text(doc.paragraphs[948], "☐ Định tuyến chuẩn xác 100% giữa 3 chế độ: Tra cứu phòng, Cố vấn chủ trọ và Phân loại sự cố kỹ thuật.", font_size=11)
set_para_text(doc.paragraphs[949], "☐ Thao tác 1-chạm sao chép bài viết SEO và tự động chuyển đổi sự cố khẩn cấp thành ticket thành công.", font_size=11)
print("-> Đã nâng cấp Mục 10 thành công!")

# Lưu file kết quả
doc.save(doc_path)
print(f"==> ĐÃ NÂNG CẤP VÀ LƯU THÀNH CÔNG BÁO CÁO NHÓM A: {doc_path}")
