# -*- coding: utf-8 -*-
import sys
import os
import docx
from docx.shared import Pt, RGBColor
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls

sys.stdout.reconfigure(encoding='utf-8')

doc_path = 'BaoCao_GiaoDien_Message_30ChucNang (1).docx'
if not os.path.exists(doc_path):
    doc_path = 'BaoCao/BaoCao_GiaoDien_Message_30ChucNang (1).docx'
print(f'Đang mở file: {doc_path}')
doc = docx.Document(doc_path)

def set_cell_text(cell, text, bold=False, italic=False, color=None, font_size=11):
    cell.text = ''
    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(2)
    p.paragraph_format.line_spacing = 1.15
    run = p.add_run(text)
    run.font.name = 'Times New Roman'
    run.font.size = Pt(font_size)
    run.bold = bold
    run.italic = italic
    if color:
        run.font.color.rgb = color
    rPr = run._r.get_or_add_rPr()
    rFonts = parse_xml(f'<w:rFonts {nsdecls("w")} w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>')
    rPr.append(rFonts)

def set_para_text(p, text, bold=False, italic=False, font_size=13):
    p.text = ''
    run = p.add_run(text)
    run.font.name = 'Times New Roman'
    run.font.size = Pt(font_size)
    run.bold = bold
    run.italic = italic
    rPr = run._r.get_or_add_rPr()
    rFonts = parse_xml(f'<w:rFonts {nsdecls("w")} w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>')
    rPr.append(rFonts)

# ==================== CẬP NHẬT TÍNH NĂNG 14 ====================
print('Đang cập nhật Tính năng 14 (AI Hỗ trợ)...')
p_map_14 = {
    436: 'TÍNH NĂNG 14: HỆ SINH THÁI TRỢ LÝ AI HỖ TRỢ TOÀN DIỆN (TRA CỨU PHÒNG, CỐ VẤN CHỦ TRỌ & XỬ LÝ SỰ CỐ) (FEAT_14_AI_ASSISTANT_HUB)',
    441: 'Khách tìm phòng (Public Guest) được tự do tra cứu phòng thông minh; Chủ trọ cần đăng nhập để sử dụng Cố vấn kinh doanh & Viết bài chuẩn SEO; Cư dân cần đăng nhập hợp đồng để báo sự cố kỹ thuật.',
    443: '• Câu hỏi / Yêu cầu người dùng (prompt): Tối thiểu 5 ký tự và tối đa 2.000 ký tự. • Chế độ tác vụ (agent_mode): Phải thuộc danh mục [room_finder, landlord_advisor, tenant_incident]. • Tần suất: Tối đa 20 yêu cầu/phút/người dùng để chống nghẽn và quá tải token API.',
    445: 'Regex kiểm tra ký tự điều khiển: /^[^<>]+$/u',
    447: '1. Người dùng mở giao diện "Trợ lý AI Hỗ trợ toàn diện" và chọn tab nghiệp vụ mong muốn (Khách tìm phòng / Cố vấn chủ trọ & Viết bài SEO / Cư dân báo sự cố).',
    448: '2. Nhập câu hỏi hoặc yêu cầu cần trợ giúp vào khung chat (hoặc chọn tiện ích phòng, đính kèm ảnh sự cố).',
    449: '3. Bấm nút "Gửi yêu cầu AI" (hoặc bấm phím Enter).',
    450: '4. Hệ thống kiểm tra: Nếu câu hỏi để trống hoặc dưới 5 ký tự, viền đỏ khung nhập và thông báo lỗi.',
    451: '5. Nếu hợp lệ, hệ thống định tuyến (orchestration) đến Agent chuyên biệt: Tra cứu cơ sở dữ liệu phòng trống theo ngữ nghĩa; hoặc sinh bài viết SEO / phân tích hợp đồng; hoặc phân loại sự cố khẩn cấp và đề xuất giải pháp sơ cứu an toàn tức thời. AI phản hồi với hiệu ứng gõ chữ trực quan (typewriter effect) kèm cụm nút thao tác nhanh (Sao chép bài viết, Đặt lịch hẹn xem phòng, Xác nhận tạo ticket kỹ thuật).',
    457: 'Hình 14.1: Giao diện Hệ sinh thái Trợ lý AI Hỗ trợ toàn diện (SmartRoom & Renty AI Assistant Hub)',
    458: 'Mô tả chức năng: Trung tâm trợ lý ảo đa tác nhân hỗ trợ tra cứu phòng thông minh theo ngôn ngữ tự nhiên, cố vấn kinh doanh & viết bài quảng cáo SEO cho chủ trọ, và tiếp nhận phân loại sự cố khẩn cấp của cư dân.'
}

for p_idx, text in p_map_14.items():
    if p_idx < len(doc.paragraphs):
        is_heading = (p_idx == 436 or p_idx == 457)
        set_para_text(doc.paragraphs[p_idx], text, bold=is_heading, font_size=13 if p_idx == 436 else 12)

# Table 65: Tổng quan tính năng 14
t65 = doc.tables[65]
set_cell_text(t65.rows[0].cells[1], 'Hệ sinh thái Trợ lý AI Hỗ trợ toàn diện (SmartRoom & Renty AI Assistant Hub)', bold=True)
set_cell_text(t65.rows[1].cells[1], 'FEAT_14_AI_ASSISTANT_HUB')
set_cell_text(t65.rows[2].cells[1], 'Hệ sinh thái AI đa tác nhân tích hợp 3 chức năng cốt lõi: Tra cứu phòng thông minh cho khách thuê (Semantic Search & RAG), Cố vấn kinh doanh & viết bài quảng cáo chuẩn SEO cho chủ trọ, và Tiếp nhận phân loại sự cố kỹ thuật tự động cho cư dân.')
set_cell_text(t65.rows[3].cells[1], 'Khách tìm phòng (Guest), Chủ trọ/Quản lý cơ sở (Landlord), Cư dân đang thuê phòng (Resident).')
set_cell_text(t65.rows[4].cells[1], 'Nhấp biểu tượng "Trợ lý ảo AI" tại thanh điều hướng hoặc nút "Nhờ AI hỗ trợ" tại các phân hệ tương ứng.')
set_cell_text(t65.rows[5].cells[1], 'Có kết nối Internet, máy chủ kích hoạt Google Gemini 2.5 Flash / OpenAI API.')

# Table 66: Bảng Input 14
t66 = doc.tables[66]
while len(t66.rows) > 1:
    tr = t66.rows[-1]._tr
    tr.getparent().remove(tr)

data_t66 = [
    ('agent_mode', 'Chế độ tác vụ AI', 'enum', 'Có', 'room_finder (Tìm phòng), landlord_advisor (Chủ trọ), tenant_incident (Cư dân)'),
    ('prompt', 'Câu hỏi / Yêu cầu người dùng', 'string', 'Có', 'Tối thiểu 5 ký tự, tối đa 2.000 ký tự'),
    ('context_id', 'ID ngữ cảnh (Phòng/Hợp đồng)', 'integer', 'Không', 'ID phòng hoặc mã hợp đồng liên quan'),
    ('tone', 'Văn phong phản hồi', 'enum', 'Không', 'Thân thiện, Chuyên nghiệp, Khẩn cấp, Sáng tạo SEO')
]
for row_vals in data_t66:
    row = t66.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v)

# Table 67: Bảng lỗi UI Messages 14
t67 = doc.tables[67]
while len(t67.rows) > 1:
    tr = t67.rows[-1]._tr
    tr.getparent().remove(tr)

data_t67 = [
    ('Để trống câu hỏi khi gửi AI', 'ERR_14_01', 'Vui lòng nhập câu hỏi hoặc yêu cầu cần trợ lý AI hỗ trợ.', 'Bôi đỏ viền khung chat, rung nhẹ (shake animation)'),
    ('Nội dung yêu cầu dưới 5 ký tự', 'ERR_14_02', 'Nội dung yêu cầu quá ngắn (tối thiểu 5 ký tự) để AI có đủ ngữ cảnh xử lý.', 'Bôi đỏ viền ô input và hiển thị cảnh báo đỏ bên dưới'),
    ('Kết nối máy chủ Gemini AI bị Timeout', 'ERR_14_03', 'Kết nối máy chủ AI quá thời gian chờ (6s), tự động kích hoạt câu trả lời từ kho tri thức dự phòng.', 'Hiển thị bài viết/lời khuyên từ kho mẫu kèm Toast vàng'),
    ('Vượt quá giới hạn tần suất truy vấn', 'ERR_14_04', 'Bạn đã gửi quá nhiều yêu cầu trong thời gian ngắn (tối đa 20 req/phút). Vui lòng đợi 30 giây!', 'Khóa nút gửi, hiển thị đồng hồ đếm ngược Countdown'),
    ('Sao chép nội dung bài viết AI thành công', 'ERR_14_05', 'Đã sao chép nội dung bài viết marketing chuẩn SEO vào bộ nhớ tạm thành công!', 'Hiển thị Toast thông báo xanh lá góc phải màn hình'),
    ('AI phân loại sự cố khẩn cấp và tạo ticket', 'ERR_14_06', 'Sự cố kỹ thuật khẩn cấp đã được AI ghi nhận và chuyển ngay đến đội ngũ bảo trì tòa nhà!', 'Hiển thị Modal thông báo màu đỏ cam ưu tiên xử lý')
]
for row_vals in data_t67:
    row = t67.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v, bold=(c_i==1))

# Table 68: Bảng UI Elements 14
t68 = doc.tables[68]
while len(t68.rows) > 1:
    tr = t68.rows[-1]._tr
    tr.getparent().remove(tr)

data_t68 = [
    ('1', 'Tab Selector', 'tabMode', 'Chế độ hỗ trợ', '—', 'Thanh điều hướng 3 tab: Tìm phòng, Cố vấn chủ trọ & SEO, Báo sự cố kỹ thuật'),
    ('2', 'Textarea', 'txtPrompt', 'Câu hỏi / Yêu cầu *', 'Bạn cần AI hỗ trợ điều gì? Nhập yêu cầu tại đây...', 'Khung soạn thảo linh hoạt, hỗ trợ phím Enter để gửi'),
    ('3', 'Select', 'cboTone', 'Phong cách phản hồi', 'Chuyên nghiệp, thu hút', 'Dropdown lựa chọn phong cách trả lời của AI'),
    ('4', 'Button', 'btnSendAI', 'Gửi yêu cầu đến AI', '—', 'Nền tím Gradient hiện đại kèm icon tia sét phát sáng'),
    ('5', 'Chat Stream', 'responseBox', 'Phản hồi của Trợ lý AI', '—', 'Khung chat dạng thẻ tin nhắn kèm hiệu ứng gõ chữ thời gian thực'),
    ('6', 'Action Buttons', 'btnQuickAction', 'Thao tác nhanh', 'Sao chép / Đặt lịch / Tạo Ticket', 'Cụm nút hành động nổi bật bên dưới câu trả lời của AI')
]
for row_vals in data_t68:
    row = t68.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v)

# Table 69: Test cases 14
t69 = doc.tables[69]
while len(t69.rows) > 1:
    tr = t69.rows[-1]._tr
    tr.getparent().remove(tr)

data_t69 = [
    ('1', 'Bấm gửi khi khung chat để trống', 'Báo lỗi viền đỏ "Vui lòng nhập câu hỏi hoặc yêu cầu cần trợ lý AI hỗ trợ"', 'Kiểm tra Empty Validation'),
    ('2', 'Chọn tab "Cố vấn chủ trọ" và nhập "Viết bài phòng 101"', 'AI sinh bài đăng chuẩn SEO đầy đủ tiêu đề, tiện ích và lời kêu gọi hành động CTA', 'Happy path Landlord Copywriting'),
    ('3', 'Chọn tab "Báo sự cố" nhập "Chập điện bốc khói"', 'AI phân loại khẩn cấp (Emergency), hướng dẫn cúp cầu dao và tự động tạo ticket bảo trì', 'Happy path Incident Triage')
]
for row_vals in data_t69:
    row = t69.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v)

print('-> Đã cập nhật xong Tính năng 14!')

# ==================== CẬP NHẬT TÍNH NĂNG 18 ====================
print('Đang cập nhật Tính năng 18 (Lễ tân dọn phòng)...')
p_map_18 = {
    567: 'TÍNH NĂNG 18: PHÂN HỆ LỄ TÂN & BUỒNG PHÒNG (DỌN PHÒNG) (FEAT_18_HOUSEKEEPING_FRONTDESK)',
    572: 'Tài khoản Nhân viên Lễ tân, Nhân viên Buồng phòng hoặc Quản lý cơ sở đã đăng nhập.',
    574: '• Trạng thái buồng phòng (housekeeping_status): Bắt buộc thuộc tập hợp [dirty, cleaning, clean, inspected, out_of_service]. • Chuyển trạng thái tuân thủ nghiêm ngặt máy trạng thái FSM: Phòng chưa Inspected/Clean tuyệt đối không cho phép Check-in đón khách mới. • Nhân viên phụ trách: Phải là nhân sự còn hoạt động thuộc cơ sở lưu trú.',
    576: 'Regex mã phòng: /^[A-Z0-9\\.\\-]+$/i',
    578: '1. Khách trả phòng hoặc bắt đầu ca làm việc: Lễ tân mở giao diện "Sơ đồ Buồng phòng & Lễ tân".',
    579: '2. Hệ thống hiển thị trực quan các thẻ phòng với màu sắc phân định trạng thái dọn dẹp (Màu đỏ: Cần dọn, Màu vàng: Đang dọn, Màu xanh lục: Đã dọn xong, Màu xanh ngọc: Đã kiểm tra sẵn sàng đón khách).',
    580: '3. Lễ tân chọn phòng cần dọn, chọn nhân viên buồng phòng phụ trách và đặt mức độ ưu tiên dọn dẹp (Khẩn cấp nếu chuẩn bị có khách mới đến).',
    581: '4. Nhân viên buồng phòng nhận thông báo trên thiết bị di động, bấm "Bắt đầu dọn" (chuyển sang cleaning).',
    582: '5. Hoàn tất vệ sinh: Nhân viên kiểm đếm minibar, nhập vật tư tiêu hao (nếu có) và bấm "Báo dọn xong" (chuyển sang clean). Lễ tân hoặc Trưởng ca kiểm tra thực tế, bấm "Nghiệm thu đạt chuẩn", phòng chính thức chuyển sang inspected và sẵn sàng bàn giao chìa khóa cho khách mới.',
    588: 'Hình 18.1: Giao diện Sơ đồ Ma trận Quản lý Buồng phòng & Điều phối Lễ tân dọn phòng',
    589: 'Mô tả chức năng: Bảng điều khiển buồng phòng thời gian thực cho phép lễ tân theo dõi trạng thái vệ sinh từng phòng, phân công ca dọn dẹp cho nhân viên và kiểm soát quy trình nghiệm thu phòng sẵn sàng đón khách.'
}

for p_idx, text in p_map_18.items():
    if p_idx < len(doc.paragraphs):
        is_heading = (p_idx == 567 or p_idx == 588)
        set_para_text(doc.paragraphs[p_idx], text, bold=is_heading, font_size=13 if p_idx == 567 else 12)

# Table 85: Tổng quan tính năng 18
t85 = doc.tables[85]
set_cell_text(t85.rows[0].cells[1], 'Phân hệ Lễ tân & Buồng phòng (Dọn phòng - Housekeeping & Front Desk Management)', bold=True)
set_cell_text(t85.rows[1].cells[1], 'FEAT_18_HOUSEKEEPING_FRONTDESK')
set_cell_text(t85.rows[2].cells[1], 'Quản lý toàn diện quy trình tiếp đón khách, check-in, check-out và vòng đời dọn dẹp vệ sinh buồng phòng theo máy trạng thái hữu hạn FSM: Sạch (Clean), Cần dọn (Dirty), Đang dọn (Cleaning), Đã kiểm tra nghiệm thu (Inspected), Tạm khóa bảo trì (Out of Service). Hỗ trợ điều phối nhân viên dọn phòng, giám sát tiến độ và đối soát vật tư minibar tiêu hao.')
set_cell_text(t85.rows[3].cells[1], 'Nhân viên Lễ tân (Receptionist), Nhân viên Buồng phòng (Housekeeper), Chủ cơ sở/Quản lý tòa nhà (Landlord/Manager).')
set_cell_text(t85.rows[4].cells[1], 'Khách trả phòng (Check-out tự động chuyển phòng sang Dirty), yêu cầu dọn phòng định kỳ theo ca, hoặc cư dân gửi yêu cầu dọn phòng đột xuất.')
set_cell_text(t85.rows[5].cells[1], 'Danh mục phòng và tòa nhà đã được cấu hình trong hệ thống, nhân viên được cấp tài khoản phân quyền buồng phòng.')

# Table 86: Bảng Input 18
t86 = doc.tables[86]
while len(t86.rows) > 1:
    tr = t86.rows[-1]._tr
    tr.getparent().remove(tr)

data_t86 = [
    ('room_id', 'Phòng lưu trú', 'integer', 'Có', 'ID phòng tồn tại và thuộc quyền quản lý của cơ sở'),
    ('housekeeping_status', 'Trạng thái buồng phòng', 'enum', 'Có', 'dirty, cleaning, clean, inspected, out_of_service'),
    ('assigned_staff_id', 'Nhân viên buồng phòng', 'integer', 'Không', 'ID tài khoản nhân viên buồng phòng phụ trách'),
    ('priority', 'Mức độ ưu tiên', 'enum', 'Có', 'urgent (Khẩn cấp), high (Cao), normal (Bình thường), low (Thấp)'),
    ('inspection_notes', 'Ghi chú kiểm phòng', 'string', 'Không', 'Tối đa 255 ký tự ghi nhận tình trạng trang thiết bị')
]
for row_vals in data_t86:
    row = t86.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v)

# Table 87: Bảng lỗi UI Messages 18
t87 = doc.tables[87]
while len(t87.rows) > 1:
    tr = t87.rows[-1]._tr
    tr.getparent().remove(tr)

data_t87 = [
    ('Chưa chọn phòng cần phân công dọn', 'ERR_18_01', 'Vui lòng chọn ít nhất một phòng cần phân công dọn dẹp vệ sinh.', 'Bôi đỏ viền thẻ phòng trên ma trận, rung nhẹ'),
    ('Cố tình Check-in vào phòng chưa dọn', 'ERR_18_02', 'Phòng [Số phòng] đang ở trạng thái Cần dọn (Dirty). Không thể thực hiện Check-in đón khách!', 'Modal cảnh báo đỏ chặn nút Check-in, yêu cầu dọn phòng trước'),
    ('Chưa phân công nhân viên dọn phòng', 'ERR_18_03', 'Vui lòng chọn nhân viên buồng phòng phụ trách thực hiện ca dọn dẹp này.', 'Bôi đỏ dropdown chọn Nhân viên phụ trách'),
    ('Chuyển trạng thái FSM buồng phòng sai quy trình', 'ERR_18_04', 'Chuyển đổi trạng thái buồng phòng không hợp lệ theo quy trình FSM (Phòng phải qua bước Sạch trước khi Nghiệm thu).', 'Hiển thị Toast cảnh báo màu đỏ góc màn hình'),
    ('Số lượng vật tư tiêu hao minibar không hợp lệ', 'ERR_18_05', 'Số lượng vật tư tiêu hao minibar phải là số nguyên dương lớn hơn hoặc bằng 0.', 'Bôi đỏ ô nhập số lượng vật tư minibar'),
    ('Nghiệm thu buồng phòng thành công', 'ERR_18_06', 'Đã nghiệm thu buồng phòng thành công! Phòng đã sẵn sàng đón khách lưu trú mới.', 'Hiển thị Toast thông báo thành công màu xanh lá')
]
for row_vals in data_t87:
    row = t87.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v, bold=(c_i==1))

# Table 88: Bảng UI Elements 18
t88 = doc.tables[88]
while len(t88.rows) > 1:
    tr = t88.rows[-1]._tr
    tr.getparent().remove(tr)

data_t88 = [
    ('1', 'Filter Buttons', 'btnStatusFilter', 'Lọc trạng thái phòng', 'Tất cả / Cần dọn / Đang dọn / Đã xong', 'Thanh công cụ lọc phía trên ma trận phòng'),
    ('2', 'Room Matrix Grid', 'gridHousekeeping', 'Sơ đồ buồng phòng', '—', 'Khung hiển thị lưới các phòng kèm mã màu FSM trực quan'),
    ('3', 'Select', 'cboStaff', 'Nhân viên dọn phòng *', 'Chọn nhân viên phụ trách...', 'Dropdown danh sách nhân sự buồng phòng'),
    ('4', 'Select', 'cboPriority', 'Mức độ ưu tiên', 'Khẩn cấp đón khách / Bình thường', 'Dropdown chọn mức độ ưu tiên ca dọn'),
    ('5', 'Button', 'btnStartClean', 'Bắt đầu dọn phòng', '—', 'Nền vàng cam kèm icon chổi dọn dẹp'),
    ('6', 'Button', 'btnInspectPass', 'Nghiệm thu phòng đạt chuẩn', '—', 'Nền xanh ngọc (#0D9488) kèm icon tích xanh hoàn tất')
]
for row_vals in data_t88:
    row = t88.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v)

# Table 89: Test cases 18
t89 = doc.tables[89]
while len(t89.rows) > 1:
    tr = t89.rows[-1]._tr
    tr.getparent().remove(tr)

data_t89 = [
    ('1', 'Bấm phân công khi chưa chọn nhân viên buồng phòng', 'Báo lỗi đỏ "Vui lòng chọn nhân viên buồng phòng phụ trách thực hiện ca dọn dẹp này"', 'Kiểm tra Assigned Staff Required'),
    ('2', 'Bấm Check-in khi phòng đang ở trạng thái Dirty', 'Hiển thị modal đỏ chặn thao tác: "Phòng đang ở trạng thái Cần dọn. Không thể Check-in!"', 'Kiểm tra FSM Guard Condition'),
    ('3', 'Nhân viên bấm Báo dọn xong -> Lễ tân bấm Nghiệm thu', 'Phòng chuyển trạng thái sang Inspected (Xanh ngọc), sẵn sàng bàn giao chìa khóa', 'Happy path Housekeeping Lifecycle')
]
for row_vals in data_t89:
    row = t89.add_row()
    for c_i, v in enumerate(row_vals):
        set_cell_text(row.cells[c_i], v)

print('-> Đã cập nhật xong Tính năng 18!')

# Lưu file spec chuẩn
doc.save(doc_path)
print(f'==> ĐÃ LƯU THÀNH CÔNG FILE SPEC CHUẨN: {doc_path}')
