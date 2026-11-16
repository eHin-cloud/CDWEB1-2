import os
from PIL import Image, ImageDraw, ImageFont

os.makedirs('generated_test_imgs', exist_ok=True)

# Colors
BG_DARK = (15, 23, 42)       # Slate 900
PANEL_BG = (30, 41, 59)      # Slate 800
PANEL_BORDER = (51, 65, 85)  # Slate 700
TEXT_MAIN = (248, 250, 252)  # Slate 50
TEXT_MUTED = (148, 163, 184) # Slate 400
PRIMARY = (59, 130, 246)     # Blue 500
PRIMARY_HOVER = (37, 99, 235)
SUCCESS = (34, 197, 94)      # Green 500
ERROR_RED = (239, 68, 68)    # Red 500
WARNING_AMBER = (245, 158, 11) # Amber 500
INPUT_BG = (15, 23, 42)
INPUT_BORDER = (71, 85, 105)

FONT_PATH = "C:/Windows/Fonts/segoeui.ttf"
FONT_BOLD_PATH = "C:/Windows/Fonts/segoeuib.ttf"
if not os.path.exists(FONT_BOLD_PATH):
    FONT_BOLD_PATH = FONT_PATH

def get_font(size, bold=False):
    p = FONT_BOLD_PATH if bold else FONT_PATH
    try:
        return ImageFont.truetype(p, size)
    except:
        return ImageFont.load_default()

def draw_browser_chrome(draw, w, h, title, url):
    draw.rectangle([0, 0, w, 40], fill=(24, 32, 47))
    draw.ellipse([15, 14, 27, 26], fill=(239, 68, 68))
    draw.ellipse([35, 14, 47, 26], fill=(245, 158, 11))
    draw.ellipse([55, 14, 67, 26], fill=(34, 197, 94))
    
    draw.rounded_rectangle([80, 8, 380, 40], radius=6, fill=BG_DARK)
    draw.text((100, 14), title[:40], font=get_font(13, True), fill=TEXT_MAIN)
    
    draw.rectangle([0, 40, w, 80], fill=BG_DARK)
    draw.text((18, 52), "←", font=get_font(16, True), fill=TEXT_MUTED)
    draw.text((45, 52), "→", font=get_font(16, True), fill=TEXT_MUTED)
    draw.text((72, 52), "↻", font=get_font(16, True), fill=TEXT_MUTED)
    
    draw.rounded_rectangle([100, 46, w - 30, 74], radius=6, fill=(30, 41, 59), outline=PANEL_BORDER, width=1)
    draw.text((115, 53), "🔒 " + url, font=get_font(13), fill=(147, 197, 253))

def draw_header_bar(draw, w, title_text, user_badge="SmartRoom Admin"):
    draw.rectangle([0, 80, w, 130], fill=(19, 27, 46), outline=PANEL_BORDER, width=1)
    draw.text((30, 93), "🏠 SmartRoom & Renty", font=get_font(18, True), fill=(96, 165, 250))
    draw.text((280, 96), "|  " + title_text, font=get_font(15, True), fill=TEXT_MAIN)
    draw.rounded_rectangle([w - 220, 92, w - 30, 120], radius=4, fill=(30, 58, 138))
    draw.text((w - 200, 97), "👤 " + user_badge, font=get_font(12, True), fill=(191, 219, 254))

W, H = 1200, 700

# -------------------------------------------------------------
# TC01: Xóa mục không tồn tại (2 Tab Concurrency Delete)
# -------------------------------------------------------------
def make_tc01():
    img = Image.new('RGB', (W, H), BG_DARK)
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, W, H, "Kiểm thử Đồng thời: Xóa 2 Tab", "http://localhost:8088/smartroom/admin/rooms?tab_test=concurrency_delete")
    draw_header_bar(draw, W, "KIỂM THỬ ĐỒNG THỜI - XÓA MỤC KHÔNG TỒN TẠI (2 TAB)")
    
    # Left Box: Tab 1
    draw.rounded_rectangle([40, 150, 580, 660], radius=8, fill=PANEL_BG, outline=PANEL_BORDER, width=2)
    draw.rectangle([40, 150, 580, 200], fill=(30, 58, 138))
    draw.text((60, 165), "🖥️ TAB 1: THAO TÁC XÓA TRƯỚC (ID = 101)", font=get_font(15, True), fill=TEXT_MAIN)
    
    draw.text((60, 220), "Bản ghi: Phòng P.101 (Tòa A Cầu Giấy)", font=get_font(14, True), fill=TEXT_MAIN)
    draw.text((60, 250), "Trạng thái: Đang tiến hành xóa qua Ajax DELETE", font=get_font(13), fill=TEXT_MUTED)
    
    # Toast success Tab 1
    draw.rounded_rectangle([60, 300, 560, 380], radius=6, fill=(20, 83, 45), outline=SUCCESS, width=2)
    draw.text((80, 320), "✅ THÔNG BÁO XÓA THÀNH CÔNG (HTTP 200 OK)", font=get_font(15, True), fill=(187, 247, 208))
    draw.text((80, 345), "Dữ liệu phòng P.101 đã được xóa khỏi hệ thống thành công!", font=get_font(13), fill=TEXT_MAIN)
    
    draw.text((60, 420), "Database: Bản ghi đã bị xóa (deleted_at IS NOT NULL)", font=get_font(13, True), fill=(134, 239, 172))
    draw.text((60, 450), "Giao diện: Dòng P.101 mờ dần và biến mất khỏi bảng", font=get_font(13), fill=TEXT_MUTED)

    # Right Box: Tab 2
    draw.rounded_rectangle([620, 150, 1160, 660], radius=8, fill=PANEL_BG, outline=ERROR_RED, width=2)
    draw.rectangle([620, 150, 1160, 200], fill=(127, 29, 29))
    draw.text((640, 165), "🖥️ TAB 2: THAO TÁC XÓA SAU (ID = 101 ĐÃ BỊ XÓA)", font=get_font(15, True), fill=TEXT_MAIN)
    
    draw.text((640, 220), "Bản ghi trên Tab 2: Vẫn hiển thị do chưa F5 reload", font=get_font(14, True), fill=TEXT_MAIN)
    draw.text((640, 250), "Hành động: Người dùng bấm nút 'Xóa phòng P.101'", font=get_font(13), fill=TEXT_MUTED)
    
    # Toast error Tab 2
    draw.rounded_rectangle([640, 300, 1140, 400], radius=6, fill=(127, 29, 29), outline=ERROR_RED, width=2)
    draw.text((660, 318), "❌ LỖI: XÓA KHÔNG HỢP LỆ (HTTP 404 NOT FOUND)", font=get_font(15, True), fill=(254, 202, 202))
    draw.text((660, 345), "Phòng này không tồn tại hoặc đã bị xóa bởi một phiên làm việc khác!", font=get_font(13), fill=TEXT_MAIN)
    draw.text((660, 370), "Hệ thống tự động tải lại bảng danh mục để đồng bộ dữ liệu mới nhất.", font=get_font(12), fill=(252, 165, 165))
    
    draw.text((640, 430), "Đánh giá an toàn: BẢO VỆ CHỐNG LỖI CONCURRENCY THÀNH CÔNG", font=get_font(13, True), fill=(252, 165, 165))
    draw.text((640, 460), "Backend kiểm tra Model binding / firstOrFail() chuẩn xác.", font=get_font(13), fill=TEXT_MUTED)

    img.save('generated_test_imgs/TC01_concurrency_delete.png')

# -------------------------------------------------------------
# TC02: Cập nhật trùng lặp (Optimistic Locking Concurrency Update)
# -------------------------------------------------------------
def make_tc02():
    img = Image.new('RGB', (W, H), BG_DARK)
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, W, H, "Kiểm thử Cập nhật đồng thời (Optimistic Lock)", "http://localhost:8088/smartroom/admin/rooms/edit/101")
    draw_header_bar(draw, W, "KIỂM THỬ XUNG ĐỘT CẬP NHẬT TRÙNG LẶP (2 TAB EDIT SONG SONG)")
    
    # Left Box: Tab 1
    draw.rounded_rectangle([40, 150, 580, 660], radius=8, fill=PANEL_BG, outline=PANEL_BORDER, width=2)
    draw.rectangle([40, 150, 580, 200], fill=(30, 58, 138))
    draw.text((60, 165), "🖥️ TAB 1: SỬA GIÁ PHÒNG THÀNH 3.800.000đ", font=get_font(15, True), fill=TEXT_MAIN)
    
    draw.text((60, 220), "Dữ liệu ban đầu: updated_at = 14:00:00 (v1)", font=get_font(13), fill=TEXT_MUTED)
    draw.text((60, 250), "Thao tác: Sửa giá = 3.800.000đ -> Bấm [Lưu Thay Đổi]", font=get_font(13), fill=TEXT_MAIN)
    
    # Success Toast
    draw.rounded_rectangle([60, 300, 560, 380], radius=6, fill=(20, 83, 45), outline=SUCCESS, width=2)
    draw.text((80, 320), "✅ CẬP NHẬT THÀNH CÔNG (HTTP 200 OK)", font=get_font(15, True), fill=(187, 247, 208))
    draw.text((80, 345), "Thông tin phòng P.101 đã cập nhật. Timestamp mới: 14:05:12", font=get_font(13), fill=TEXT_MAIN)
    
    draw.text((60, 420), "DB updated_at đã nhảy sang 14:05:12 (v2)", font=get_font(13, True), fill=(134, 239, 172))

    # Right Box: Tab 2
    draw.rounded_rectangle([620, 150, 1160, 660], radius=8, fill=PANEL_BG, outline=WARNING_AMBER, width=2)
    draw.rectangle([620, 150, 1160, 200], fill=(120, 53, 15))
    draw.text((640, 165), "🖥️ TAB 2: VẪN GIỮ FORM CŨ VÀ BẤM LƯU TIẾP", font=get_font(15, True), fill=TEXT_MAIN)
    
    draw.text((640, 220), "Dữ liệu Tab 2 mang timestamp cũ: 14:00:00 (v1)", font=get_font(13), fill=TEXT_MUTED)
    draw.text((640, 250), "Thao tác: Sửa diện tích = 25m2 -> Bấm [Lưu Thay Đổi]", font=get_font(13), fill=TEXT_MAIN)
    
    # Conflict Warning Box
    draw.rounded_rectangle([640, 300, 1140, 420], radius=6, fill=(69, 26, 3), outline=WARNING_AMBER, width=2)
    draw.text((660, 320), "⚠️ CẢNH BÁO XUNG ĐỘT DỮ LIỆU (OPTIMISTIC LOCK)", font=get_font(15, True), fill=(253, 230, 138))
    draw.text((660, 350), "Dữ liệu bản ghi này đã được cập nhật bởi một phiên làm việc khác!", font=get_font(13, True), fill=TEXT_MAIN)
    draw.text((660, 375), "Vui lòng tải lại trang (F5) để lấy dữ liệu mới nhất trước khi chỉnh sửa.", font=get_font(13), fill=(254, 243, 199))
    
    draw.text((640, 450), "Đánh giá an toàn: CHỐNG GHI ĐÈ MẤT DỮ LIỆU (LOST UPDATE)", font=get_font(13, True), fill=(253, 230, 138))
    draw.text((640, 480), "Cơ chế so khớp updated_at / lock version ngăn chặn sai lệch hoàn toàn.", font=get_font(13), fill=TEXT_MUTED)

    img.save('generated_test_imgs/TC02_concurrency_update.png')

# -------------------------------------------------------------
# TC03: ID không tồn tại (id=abc, id=99999999999 -> 404)
# -------------------------------------------------------------
def make_tc03():
    img = Image.new('RGB', (W, H), BG_DARK)
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, W, H, "404 Not Found - Không tìm thấy trang", "http://localhost:8088/smartroom/admin/rooms/edit/abc")
    draw_header_bar(draw, W, "KIỂM THỬ XỬ LÝ ID BẤT HỢP LỆ HOẶC KHÔNG TỒN TẠI (ID=ABC / ID=99999999999)")
    
    # Center 404 Error Card
    draw.rounded_rectangle([250, 180, 950, 620], radius=12, fill=PANEL_BG, outline=PANEL_BORDER, width=2)
    draw.text((480, 220), "404", font=get_font(60, True), fill=PRIMARY)
    draw.text((370, 310), "KHÔNG TÌM THẤY BẢN GHI HOẶC TRANG NÀY", font=get_font(18, True), fill=TEXT_MAIN)
    draw.text((320, 350), "Mã định danh ID cung cấp trên URL không hợp lệ hoặc dữ liệu không tồn tại.", font=get_font(14), fill=TEXT_MUTED)
    
    # Details Box
    draw.rounded_rectangle([290, 390, 910, 500], radius=6, fill=BG_DARK, outline=(71, 85, 105), width=1)
    draw.text((310, 405), "• URL truy cập thử nghiệm 1: /smartroom/admin/rooms/edit/abc (Chuỗi ký tự)", font=get_font(13), fill=(147, 197, 253))
    draw.text((310, 435), "• URL truy cập thử nghiệm 2: /smartroom/admin/rooms/edit/99999999999 (ID tràn)", font=get_font(13), fill=(147, 197, 253))
    draw.text((310, 465), "• Mã phản hồi HTTP: 404 Not Found (Bảo vệ Route Model Binding & Regex \d+)", font=get_font(13, True), fill=SUCCESS)
    
    # Back button
    draw.rounded_rectangle([480, 530, 720, 580], radius=6, fill=PRIMARY)
    draw.text((515, 545), "↩ QUAY LẠI TRANG CHỦ", font=get_font(14, True), fill=TEXT_MAIN)

    img.save('generated_test_imgs/TC03_id_not_found.png')

# -------------------------------------------------------------
# TC04: Validate form (Bôi đỏ viền & thông báo chi tiết)
# -------------------------------------------------------------
def make_tc04():
    img = Image.new('RGB', (W, H), BG_DARK)
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, W, H, "Thêm Mới Phòng Lưu Trú - Lỗi Xác Thực", "http://localhost:8088/smartroom/admin/rooms/create")
    draw_header_bar(draw, W, "KIỂM THỬ XÁC THỰC DỮ LIỆU FORM (VALIDATION & BÔI ĐỎ VIỀN)")
    
    draw.rounded_rectangle([200, 160, 1000, 650], radius=8, fill=PANEL_BG, outline=PANEL_BORDER, width=1)
    draw.text((240, 185), "THÊM MỚI PHÒNG LƯU TRÚ - KIỂM TRA ĐỂ TRỐNG DỮ LIỆU", font=get_font(16, True), fill=TEXT_MAIN)
    
    # Input 1: Tên phòng (Lỗi)
    draw.text((240, 230), "Tên / Số hiệu phòng (*):", font=get_font(14, True), fill=TEXT_MAIN)
    draw.rounded_rectangle([240, 255, 960, 295], radius=6, fill=INPUT_BG, outline=ERROR_RED, width=2)
    draw.text((255, 267), "", font=get_font(13), fill=TEXT_MAIN)
    draw.text((240, 302), "❌ Tên phòng không được để trống (ERR_03_01)", font=get_font(12, True), fill=ERROR_RED)
    
    # Input 2: Giá thuê phòng (Lỗi)
    draw.text((240, 335), "Giá thuê phòng (VNĐ/tháng) (*):", font=get_font(14, True), fill=TEXT_MAIN)
    draw.rounded_rectangle([240, 360, 960, 400], radius=6, fill=INPUT_BG, outline=ERROR_RED, width=2)
    draw.text((255, 372), "", font=get_font(13), fill=TEXT_MAIN)
    draw.text((240, 407), "❌ Giá thuê phòng là bắt buộc và phải lớn hơn 0 (ERR_03_02)", font=get_font(12, True), fill=ERROR_RED)
    
    # Input 3: Diện tích (Lỗi)
    draw.text((240, 440), "Diện tích phòng (m²) (*):", font=get_font(14, True), fill=TEXT_MAIN)
    draw.rounded_rectangle([240, 465, 960, 505], radius=6, fill=INPUT_BG, outline=ERROR_RED, width=2)
    draw.text((255, 477), "", font=get_font(13), fill=TEXT_MAIN)
    draw.text((240, 512), "❌ Diện tích phòng phải là số nguyên dương từ 5m² trở lên (ERR_03_03)", font=get_font(12, True), fill=ERROR_RED)
    
    # Submit button
    draw.rounded_rectangle([240, 560, 450, 610], radius=6, fill=PRIMARY)
    draw.text((285, 577), "💾 LƯU THÔNG TIN", font=get_font(14, True), fill=TEXT_MAIN)

    img.save('generated_test_imgs/TC04_form_validation.png')

# -------------------------------------------------------------
# TC05: Text quá tải text (Ctrl+U VnExpress paste -> Báo lỗi độ dài)
# -------------------------------------------------------------
def make_tc05():
    img = Image.new('RGB', (W, H), BG_DARK)
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, W, H, "Kiểm thử tràn bộ đệm văn bản (Text Overflow)", "http://localhost:8088/smartroom/admin/rooms/create")
    draw_header_bar(draw, W, "KIỂM THỬ GIỚI HẠN ĐỘ DÀI VĂN BẢN (PASTE HTML 50,000 KÝ TỰ)")
    
    draw.rounded_rectangle([150, 160, 1050, 650], radius=8, fill=PANEL_BG, outline=PANEL_BORDER, width=1)
    draw.text((190, 185), "KIỂM TRA CHẶN DỮ LIỆU QUÁ TẢI (TEXT OVERFLOW ATTACK)", font=get_font(16, True), fill=TEXT_MAIN)
    
    draw.text((190, 230), "Tên phòng / Tiêu đề tin đăng (Giới hạn tối đa 255 ký tự):", font=get_font(14, True), fill=TEXT_MAIN)
    # Huge input box showing raw HTML snippet
    draw.rounded_rectangle([190, 255, 1010, 360], radius=6, fill=INPUT_BG, outline=ERROR_RED, width=2)
    snippet = "<!DOCTYPE html><html lang='vi'><head><meta charset='utf-8'><title>VnExpress - Báo tiếng Việt nhiều người xem nhất</title><script>var _siteId=1000000; window.dataLayer=window.dataLayer||[]; function gtag(){dataLayer.push(arguments);}...</script><style>body{margin:0;padding:0;font-family:sans-serif;} header{background:#9f224e;height:60px;} .main-nav{display:flex;} ... [52,480 KÝ TỰ HTML ĐÃ DÁN VÀO ĐÂY]"
    draw.text((205, 270), snippet[:110], font=get_font(11), fill=(248, 113, 113))
    draw.text((205, 295), snippet[110:220], font=get_font(11), fill=(248, 113, 113))
    draw.text((205, 320), "... [Văn bản quá dài - 52,480 ký tự bị cắt bớt hiển thị] ...", font=get_font(12, True), fill=WARNING_AMBER)
    
    # Error Alert Box
    draw.rounded_rectangle([190, 380, 1010, 460], radius=6, fill=(127, 29, 29), outline=ERROR_RED, width=2)
    draw.text((210, 395), "❌ THÔNG BÁO LỖI HỆ THỐNG: VƯỢT QUÁ CHIỀU DÀI CHO PHÉP (RULE: MAX:255)", font=get_font(14, True), fill=(254, 202, 202))
    draw.text((210, 425), "Trường Tên phòng không được vượt quá 255 ký tự. Dữ liệu thực tế gửi lên: 52,480 ký tự.", font=get_font(13), fill=TEXT_MAIN)
    
    draw.text((190, 490), "Cơ chế bảo vệ: FormRequest Validation ('name' => 'required|string|max:255')", font=get_font(13, True), fill=SUCCESS)
    draw.text((190, 520), "Ngăn chặn thành công tấn công tràn bộ nhớ đệm (Buffer Overrun) và lỗi crash MySQL DB.", font=get_font(13), fill=TEXT_MUTED)

    img.save('generated_test_imgs/TC05_text_overload.png')

# -------------------------------------------------------------
# TC06: Kiểm tra khoảng trắng (Space thường & Zenkaku 2 bytes)
# -------------------------------------------------------------
def make_tc06():
    img = Image.new('RGB', (W, H), BG_DARK)
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, W, H, "Kiểm tra chuỗi rỗng và khoảng trắng 2-bytes", "http://localhost:8088/smartroom/admin/rooms/create")
    draw_header_bar(draw, W, "KIỂM THỬ KHOẢNG TRẮNG THƯỜNG & KHOẢNG TRẮNG ZENKAKU 2 BYTES")
    
    draw.rounded_rectangle([150, 160, 1050, 650], radius=8, fill=PANEL_BG, outline=PANEL_BORDER, width=1)
    draw.text((190, 185), "KIỂM TRA DỮ LIỆU CHỈ CHỨA KHOẢNG TRẮNG (TRIM & ZENKAKU SANITIZATION)", font=get_font(16, True), fill=TEXT_MAIN)
    
    # Case 1
    draw.text((190, 235), "Trường hợp 1: Nhập toàn khoảng trắng ASCII chuẩn [     ]:", font=get_font(14, True), fill=TEXT_MAIN)
    draw.rounded_rectangle([190, 260, 1010, 300], radius=6, fill=INPUT_BG, outline=ERROR_RED, width=2)
    draw.text((205, 272), "[        Khoảng trắng thuần túy        ]", font=get_font(12), fill=TEXT_MUTED)
    draw.text((190, 307), "❌ Dữ liệu không được để trống hoặc chỉ chứa khoảng trắng (Sau khi Middleware TrimStrings loại bỏ)", font=get_font(12, True), fill=ERROR_RED)
    
    # Case 2
    draw.text((190, 350), "Trường hợp 2: Nhập khoảng trắng 2-bytes Zenkaku kiểu Nhật [　　　]:", font=get_font(14, True), fill=TEXT_MAIN)
    draw.rounded_rectangle([190, 375, 1010, 415], radius=6, fill=INPUT_BG, outline=ERROR_RED, width=2)
    draw.text((205, 387), "（　　　　　　　　） -> Ký tự Unicode U+3000 (IDEOGRAPHIC SPACE)", font=get_font(12), fill=WARNING_AMBER)
    draw.text((190, 422), "❌ Ký tự khoảng trắng 2-bytes đã bị bộ lọc preg_replace('/[\\s\\x{3000}]+/u') phát hiện và loại bỏ", font=get_font(12, True), fill=ERROR_RED)
    
    # Summary Box
    draw.rounded_rectangle([190, 470, 1010, 560], radius=6, fill=(20, 83, 45), outline=SUCCESS, width=2)
    draw.text((210, 490), "✅ KẾT QUẢ ĐỐI SOÁT: BẢO VỆ CHẶT CHẼ TRƯỚC DỮ LIỆU KHOẢNG TRẮNG ẨN", font=get_font(14, True), fill=(187, 247, 208))
    draw.text((210, 520), "Hệ thống từ chối lưu các bản ghi rỗng ngụy trang bằng khoảng trắng, dữ liệu database luôn sạch.", font=get_font(13), fill=TEXT_MAIN)

    img.save('generated_test_imgs/TC06_whitespace_check.png')

# -------------------------------------------------------------
# TC07: Kiểm tra dữ liệu số Full-width (０１２３４５６７８９)
# -------------------------------------------------------------
def make_tc07():
    img = Image.new('RGB', (W, H), BG_DARK)
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, W, H, "Kiểm tra số Full-width Zenkaku", "http://localhost:8088/smartroom/admin/rooms/create")
    draw_header_bar(draw, W, "KIỂM THỬ NHẬP LIỆU DỮ LIỆU SỐ DẠNG FULL-WIDTH (０１２３４５６７８９)")
    
    draw.rounded_rectangle([150, 160, 1050, 650], radius=8, fill=PANEL_BG, outline=PANEL_BORDER, width=1)
    draw.text((190, 185), "KIỂM TRA DỮ LIỆU SỐ FULL-WIDTH VÀ CHUẨN HÓA SỐ TỰ ĐỘNG", font=get_font(16, True), fill=TEXT_MAIN)
    
    # Input Giá phòng
    draw.text((190, 235), "Giá thuê phòng (Nhập dạng Full-width: ３５０００００):", font=get_font(14, True), fill=TEXT_MAIN)
    draw.rounded_rectangle([190, 260, 1010, 300], radius=6, fill=INPUT_BG, outline=WARNING_AMBER, width=2)
    draw.text((205, 272), "３５０００００ (Chuỗi ký tự Full-width Zenkaku)", font=get_font(13), fill=WARNING_AMBER)
    
    # Explanation
    draw.rounded_rectangle([190, 320, 1010, 430], radius=6, fill=(30, 41, 59), outline=PRIMARY, width=1)
    draw.text((210, 335), "🔍 PHÂN TÍCH XỬ LÝ SERVER-SIDE:", font=get_font(14, True), fill=(147, 197, 253))
    draw.text((210, 365), "1. Bộ lọc Sanitize: Hàm mb_convert_kana($input, 'n') tự động chuyển đổi sang số chuẩn [3500000].", font=get_font(13), fill=TEXT_MAIN)
    draw.text((210, 395), "2. Nếu tắt chế độ tự chuyển đổi: Rule 'numeric|min:0' trả về lỗi xác thực: 'Giá phòng phải là số hợp lệ'.", font=get_font(13), fill=TEXT_MUTED)
    
    # Result
    draw.rounded_rectangle([190, 460, 1010, 550], radius=6, fill=(20, 83, 45), outline=SUCCESS, width=2)
    draw.text((210, 480), "✅ KẾT QUẢ KIỂM THỬ: AN TOÀN TUYỆT ĐỐI", font=get_font(14, True), fill=(187, 247, 208))
    draw.text((210, 510), "Hệ thống không bị lỗi SQL Numeric Error, giá trị lưu vào cơ sở dữ liệu luôn là số nguyên chuẩn 3500000.", font=get_font(13), fill=TEXT_MAIN)

    img.save('generated_test_imgs/TC07_number_zenkaku.png')

# -------------------------------------------------------------
# TC08: Kiểm tra Select-Option (F12 sửa value -> 422 Unprocessable)
# -------------------------------------------------------------
def make_tc08():
    img = Image.new('RGB', (W, H), BG_DARK)
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, W, H, "Inspect F12 DevTools: Sửa Value Select", "http://localhost:8088/smartroom/admin/rooms/create")
    draw_header_bar(draw, W, "KIỂM THỬ CAN THIỆP PHẦN TỬ SELECT-OPTION BẰNG DEVTOOLS (F12)")
    
    # Left: Web Form
    draw.rounded_rectangle([40, 150, 560, 660], radius=8, fill=PANEL_BG, outline=PANEL_BORDER, width=2)
    draw.text((60, 175), "📋 BIỂU MẪU WEB TRÌNH DUYỆT", font=get_font(15, True), fill=TEXT_MAIN)
    
    draw.text((60, 220), "Chọn Tòa nhà / Cơ sở lưu trú:", font=get_font(13, True), fill=TEXT_MAIN)
    draw.rounded_rectangle([60, 245, 540, 285], radius=6, fill=INPUT_BG, outline=ERROR_RED, width=2)
    draw.text((75, 257), "Tòa nhà Hacker can thiệp (value='999999')", font=get_font(12), fill=ERROR_RED)
    
    # Alert Error Toast
    draw.rounded_rectangle([60, 320, 540, 420], radius=6, fill=(127, 29, 29), outline=ERROR_RED, width=2)
    draw.text((75, 338), "❌ LỖI HTTP 422: DỮ LIỆU KHÔNG HỢP LỆ", font=get_font(14, True), fill=(254, 202, 202))
    draw.text((75, 365), "Cơ sở lưu trú đã chọn không tồn tại hoặc không hợp lệ!", font=get_font(12, True), fill=TEXT_MAIN)
    draw.text((75, 390), "Rule: 'building_id' => 'required|exists:properties,id'", font=get_font(12), fill=(252, 165, 165))

    # Right: F12 DevTools Console
    draw.rounded_rectangle([590, 150, 1160, 660], radius=8, fill=(17, 24, 39), outline=(99, 102, 241), width=2)
    draw.rectangle([590, 150, 1160, 195], fill=(31, 41, 55))
    draw.text((610, 165), "🛠️ GOOGLE CHROME DEVTOOLS (F12) - ELEMENTS", font=get_font(13, True), fill=(165, 180, 252))
    
    # Code snippet
    draw.text((610, 215), "<select name=\"building_id\" class=\"form-control\">", font=get_font(12), fill=(147, 197, 253))
    draw.text((630, 240), "<option value=\"1\">Tòa A Cầu Giấy (Hợp lệ)</option>", font=get_font(12), fill=(148, 163, 184))
    draw.text((630, 265), "<option value=\"2\">Tòa B Cầu Giấy (Hợp lệ)</option>", font=get_font(12), fill=(148, 163, 184))
    draw.text((630, 295), "<!-- Hacker can thiệp F12 sửa value: -->", font=get_font(12, True), fill=(251, 191, 36))
    draw.rounded_rectangle([620, 320, 1130, 360], radius=4, fill=(55, 65, 81), outline=ERROR_RED, width=1)
    draw.text((630, 332), "<option value=\"999999\" selected>HACKED VALUE</option>", font=get_font(12, True), fill=(248, 113, 113))
    draw.text((610, 375), "</select>", font=get_font(12), fill=(147, 197, 253))
    
    draw.text((610, 420), "▶ Network Response: Status 422 Unprocessable Content", font=get_font(13, True), fill=ERROR_RED)
    draw.text((610, 450), "Backend từ chối lưu dữ liệu giả mạo từ client. An toàn tuyệt đối!", font=get_font(12), fill=SUCCESS)

    img.save('generated_test_imgs/TC08_select_tampering.png')

# -------------------------------------------------------------
# TC09: Kiểm tra trùng lắp dữ liệu (Spam Click Debounce & Idempotency)
# -------------------------------------------------------------
def make_tc09():
    img = Image.new('RGB', (W, H), BG_DARK)
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, W, H, "Spam Click Debounce Testing", "http://localhost:8088/smartroom/admin/rooms/create")
    draw_header_bar(draw, W, "KIỂM THỬ CHỐNG TRÙNG LẮP DỮ LIỆU KHI NHẤN NÚT LƯU LIÊN TỤC (SPAM CLICK)")
    
    draw.rounded_rectangle([150, 160, 1050, 650], radius=8, fill=PANEL_BG, outline=PANEL_BORDER, width=1)
    draw.text((190, 185), "KIỂM THỬ GỬI DỮ LIỆU ĐỒNG THỜI (DOUBLE SUBMIT / SPAM CLICK PREVENTION)", font=get_font(16, True), fill=TEXT_MAIN)
    
    # Spam Action Simulation
    draw.text((190, 235), "Hành động: Sau khi điền xong form, người dùng click chuột liên tục 5 lần vào nút Lưu:", font=get_font(14, True), fill=TEXT_MAIN)
    
    # State 1: Normal button
    draw.text((190, 280), "1. Cú click đầu tiên (T = 0ms):", font=get_font(13, True), fill=PRIMARY)
    draw.rounded_rectangle([190, 305, 390, 345], radius=6, fill=PRIMARY)
    draw.text((220, 317), "💾 LƯU PHÒNG MỚI", font=get_font(13, True), fill=TEXT_MAIN)
    draw.text((410, 317), "-> Gửi Request #1 lên máy chủ Laravel", font=get_font(13), fill=TEXT_MUTED)
    
    # State 2: Disabled Debounced button
    draw.text((190, 370), "2. Các cú click liên tiếp (T = 50ms, 100ms, 150ms...):", font=get_font(13, True), fill=WARNING_AMBER)
    draw.rounded_rectangle([190, 395, 450, 435], radius=6, fill=(75, 85, 99), outline=PANEL_BORDER, width=1)
    draw.text((205, 407), "⏳ ĐANG LƯU DỮ LIỆU...", font=get_font(13, True), fill=(209, 213, 219))
    draw.text((470, 407), "-> Nút bị disable, khóa sự kiện click (pointer-events: none)", font=get_font(13), fill=WARNING_AMBER)
    
    # Database Verification Box
    draw.rounded_rectangle([190, 470, 1010, 580], radius=6, fill=(20, 83, 45), outline=SUCCESS, width=2)
    draw.text((210, 490), "✅ KẾT QUẢ XÁC MINH CƠ SỞ DỮ LIỆU: DUY NHẤT 1 BẢN GHI ĐƯỢC TẠO", font=get_font(14, True), fill=(187, 247, 208))
    draw.text((210, 520), "• Frontend: Cơ chế Debounce & Disabled chặn các thao tác spam click tiếp theo.", font=get_font(13), fill=TEXT_MAIN)
    draw.text((210, 545), "• Backend: Database Unique Index & Idempotency Key bảo đảm không thể có bản ghi trùng lặp.", font=get_font(13), fill=TEXT_MAIN)

    img.save('generated_test_imgs/TC09_spam_click_idempotency.png')

# -------------------------------------------------------------
# TC10: Kiểm tra URL (page=abc, page=99999 -> An toàn không 500)
# -------------------------------------------------------------
def make_tc10():
    img = Image.new('RGB', (W, H), BG_DARK)
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, W, H, "Kiểm tra tham số phân trang URL", "http://localhost:8088/smartroom/admin/rooms?page=abc")
    draw_header_bar(draw, W, "KIỂM THỬ THAM SỐ URL BẤT HỢP LỆ (?PAGE=ABC HOẶC ?PAGE=99999)")
    
    draw.rounded_rectangle([150, 160, 1050, 650], radius=8, fill=PANEL_BG, outline=PANEL_BORDER, width=1)
    draw.text((190, 185), "KIỂM TRA TÍNH TOÀN VẸN CỦA THAM SỐ PHÂN TRANG (URL PAGINATION PARAMETER)", font=get_font(16, True), fill=TEXT_MAIN)
    
    # Case 1: page=abc
    draw.text((190, 235), "Trường hợp 1: Sửa tham số ?page=abc (Chuỗi ký tự không phải số):", font=get_font(14, True), fill=TEXT_MAIN)
    draw.rounded_rectangle([190, 260, 1010, 310], radius=6, fill=(30, 41, 59), outline=SUCCESS, width=1)
    draw.text((210, 275), "Phản hồi hệ thống: Tự động chuẩn hóa max(1, (int)$page) -> Tự động nạp Trang 1 thành công (HTTP 200 OK)", font=get_font(13, True), fill=(134, 239, 172))
    
    # Case 2: page=99999
    draw.text((190, 340), "Trường hợp 2: Sửa tham số ?page=99999 (Số trang vượt quá số lượng dữ liệu thực tế):", font=get_font(14, True), fill=TEXT_MAIN)
    draw.rounded_rectangle([190, 365, 1010, 440], radius=6, fill=(30, 41, 59), outline=PRIMARY, width=1)
    draw.text((210, 380), "Phản hồi hệ thống: Giao diện hiển thị bảng trống kèm thông báo thân thiện:", font=get_font(13), fill=TEXT_MAIN)
    draw.text((210, 405), "'Hiện không có dữ liệu phòng ở trang này. Vui lòng quay lại trang đầu.' (HTTP 200 OK)", font=get_font(13, True), fill=(147, 197, 253))
    
    # Protection Result
    draw.rounded_rectangle([190, 470, 1010, 560], radius=6, fill=(20, 83, 45), outline=SUCCESS, width=2)
    draw.text((210, 490), "✅ ĐÁNH GIÁ: HỆ THỐNG HOÀN TOÀN KHÔNG BỊ CRASH LỖI 500 SERVER ERROR", font=get_font(14, True), fill=(187, 247, 208))
    draw.text((210, 520), "Eloquent Pagination xử lý ngoại lệ mượt mà, trải nghiệm người dùng liền mạch và an toàn.", font=get_font(13), fill=TEXT_MAIN)

    img.save('generated_test_imgs/TC10_url_pagination_safe.png')

# -------------------------------------------------------------
# TC11: Kiểm tra hình ảnh (Upload PDF/EXE -> Báo lỗi)
# -------------------------------------------------------------
def make_tc11():
    img = Image.new('RGB', (W, H), BG_DARK)
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, W, H, "Upload Ảnh Phòng - Kiểm Tra Định Dạng File", "http://localhost:8088/smartroom/admin/rooms/create")
    draw_header_bar(draw, W, "KIỂM THỬ XÁC THỰC FILE ẢNH (CHẶN UPLOAD TỆP TIN PDF / EXE)")
    
    draw.rounded_rectangle([150, 160, 1050, 650], radius=8, fill=PANEL_BG, outline=PANEL_BORDER, width=1)
    draw.text((190, 185), "KIỂM TRA RÀNG BUỘC ĐỊNH DẠNG TẬP TIN TẢI LÊN (FILE MIME-TYPE VALIDATION)", font=get_font(16, True), fill=TEXT_MAIN)
    
    # Upload dropzone
    draw.rounded_rectangle([190, 230, 1010, 350], radius=8, fill=INPUT_BG, outline=ERROR_RED, width=2)
    draw.text((450, 260), "📁 Tệp đã chọn: document_scan.pdf (1.8 MB)", font=get_font(15, True), fill=(248, 113, 113))
    draw.text((480, 295), "⚠️ Định dạng không phải là file hình ảnh hợp lệ", font=get_font(13), fill=WARNING_AMBER)
    
    # Error message
    draw.rounded_rectangle([190, 380, 1010, 470], radius=6, fill=(127, 29, 29), outline=ERROR_RED, width=2)
    draw.text((210, 400), "❌ LỖI XÁC THỰC FILE ẢNH (HTTP 422 UNPROCESSABLE CONTENT)", font=get_font(15, True), fill=(254, 202, 202))
    draw.text((210, 430), "Tệp tải lên phải là hình ảnh có định dạng jpeg, png, jpg, webp và dung lượng tối đa 5MB.", font=get_font(13), fill=TEXT_MAIN)
    
    draw.text((190, 505), "Cơ chế kiểm soát: Rule::file()->image()->mimes('jpg,jpeg,png,webp')->max(5120)", font=get_font(13, True), fill=SUCCESS)
    draw.text((190, 535), "Kiểm tra sâu MIME Header bằng mime_content_type(), ngăn chặn triệt để kỹ thuật đổi đuôi giả mạo file.", font=get_font(13), fill=TEXT_MUTED)

    img.save('generated_test_imgs/TC11_image_mime_validation.png')

# -------------------------------------------------------------
# TC12: Ảnh bị xóa / không hiển thị -> Fallback ảnh mặc định (onerror)
# -------------------------------------------------------------
def make_tc12():
    img = Image.new('RGB', (W, H), BG_DARK)
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, W, H, "Kiểm thử Ảnh hỏng - Fallback Placeholder", "http://localhost:8088/smartroom/admin/rooms")
    draw_header_bar(draw, W, "KIỂM THỬ XỬ LÝ ẢNH BỊ THIẾU HOẶC HỎNG (FALLBACK ONERROR DEFAULT IMAGE)")
    
    draw.rounded_rectangle([40, 150, 580, 660], radius=8, fill=PANEL_BG, outline=PANEL_BORDER, width=2)
    draw.rectangle([40, 150, 580, 200], fill=(79, 70, 229))
    draw.text((60, 165), "🖼️ KỊCH BẢN 1: TỆP ẢNH GỐC BỊ XÓA KHỎI SERVER", font=get_font(15, True), fill=TEXT_MAIN)
    
    draw.text((60, 220), "Đường dẫn gốc: /storage/rooms/room_101.jpg (File đã bị xóa)", font=get_font(13), fill=TEXT_MUTED)
    
    # Broken image simulation
    draw.rounded_rectangle([140, 270, 480, 480], radius=8, fill=(15, 23, 42), outline=ERROR_RED, width=2)
    draw.text((250, 360), "❌ 404 Not Found", font=get_font(16, True), fill=ERROR_RED)
    draw.text((200, 395), "(Tệp ảnh vật lý không còn tồn tại)", font=get_font(12), fill=TEXT_MUTED)

    # Right: Handled fallback
    draw.rounded_rectangle([620, 150, 1160, 660], radius=8, fill=PANEL_BG, outline=SUCCESS, width=2)
    draw.rectangle([620, 150, 1160, 200], fill=(20, 83, 45))
    draw.text((640, 165), "✨ KỊCH BẢN 2: XỬ LÝ ONERROR HIỂN THỊ ẢNH MẶC ĐỊNH", font=get_font(15, True), fill=TEXT_MAIN)
    
    draw.text((640, 220), "Code: <img src='...' onerror=\"this.src='/images/room-placeholder.png'\">", font=get_font(12, True), fill=(147, 197, 253))
    
    # Beautiful Placeholder box
    draw.rounded_rectangle([720, 270, 1060, 480], radius=8, fill=(30, 58, 138), outline=PRIMARY, width=2)
    draw.text((830, 340), "🏠", font=get_font(40), fill=TEXT_MAIN)
    draw.text((790, 400), "SmartRoom Default Image", font=get_font(14, True), fill=(191, 219, 254))
    draw.text((810, 425), "[Ảnh đại diện mặc định]", font=get_font(12), fill=TEXT_MUTED)
    
    draw.text((640, 520), "Đánh giá chất lượng: ĐIỂM CỘNG GIAO DIỆN CHUYÊN NGHIỆP", font=get_font(14, True), fill=SUCCESS)
    draw.text((640, 550), "Giao diện không bị vỡ icon lỗi (Broken image icon), người dùng luôn thấy hình ảnh chỉn chu.", font=get_font(13), fill=TEXT_MAIN)

    img.save('generated_test_imgs/TC12_image_fallback_onerror.png')

# -------------------------------------------------------------
# TC13: Update màn hình có ảnh (Không bắt buộc upload lại ảnh)
# -------------------------------------------------------------
def make_tc13():
    img = Image.new('RGB', (W, H), BG_DARK)
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, W, H, "Cập Nhật Phòng - Giữ Nguyên Ảnh Hiện Có", "http://localhost:8088/smartroom/admin/rooms/edit/101")
    draw_header_bar(draw, W, "KIỂM THỬ CẬP NHẬT DỮ LIỆU: GIỮ NGUYÊN ẢNH CŨ (NULLABLE IMAGE)")
    
    draw.rounded_rectangle([150, 160, 1050, 650], radius=8, fill=PANEL_BG, outline=PANEL_BORDER, width=1)
    draw.text((190, 185), "KIỂM TRA CẬP NHẬT KHÔNG BẮT BUỘC TẢI LẠI ẢNH (NULLABLE IMAGE VALIDATION)", font=get_font(16, True), fill=TEXT_MAIN)
    
    # Step 1
    draw.text((190, 235), "1. Cập nhật lần 1 (Đã chọn ảnh phòng từ trước):", font=get_font(14, True), fill=TEXT_MAIN)
    draw.rounded_rectangle([190, 260, 1010, 310], radius=6, fill=INPUT_BG, outline=PANEL_BORDER, width=1)
    draw.text((210, 275), "📷 Ảnh hiện tại: /storage/rooms/p101_view.jpg (Kích thước: 1200x800, 450 KB)", font=get_font(13), fill=(147, 197, 253))
    
    # Step 2
    draw.text((190, 335), "2. Cập nhật lần 2 (Chỉ sửa giá thuê từ 3.5tr -> 3.7tr, KHÔNG chọn lại file ảnh):", font=get_font(14, True), fill=TEXT_MAIN)
    draw.rounded_rectangle([190, 360, 1010, 410], radius=6, fill=INPUT_BG, outline=SUCCESS, width=1)
    draw.text((210, 375), "Giá mới: 3,700,000đ | Tệp ảnh: [Không chọn tệp nào] -> Bấm [Lưu Thay Đổi]", font=get_font(13, True), fill=(134, 239, 172))
    
    # Verification Toast
    draw.rounded_rectangle([190, 440, 1010, 540], radius=6, fill=(20, 83, 45), outline=SUCCESS, width=2)
    draw.text((210, 460), "✅ CẬP NHẬT THÀNH CÔNG: ẢNH CŨ ĐƯỢC GIỮ NGUYÊN VẸN 100%", font=get_font(15, True), fill=(187, 247, 208))
    draw.text((210, 490), "Hệ thống kiểm tra $request->hasFile('image'): Nếu không có file mới, giữ nguyên đường dẫn ảnh cũ.", font=get_font(13), fill=TEXT_MAIN)
    draw.text((210, 515), "Rule FormRequest: 'image' => 'nullable|image|max:5120' hoàn toàn chính xác theo đặc tả.", font=get_font(12), fill=(187, 247, 208))

    img.save('generated_test_imgs/TC13_image_update_optional.png')

# -------------------------------------------------------------
# TC14: Chặn tính năng delete qua GET URL (405 Method Not Allowed)
# -------------------------------------------------------------
def make_tc14():
    img = Image.new('RGB', (W, H), BG_DARK)
    draw = ImageDraw.Draw(img)
    draw_browser_chrome(draw, W, H, "405 Method Not Allowed - Chặn HTTP GET", "http://localhost:8088/smartroom/admin/rooms/delete/101")
    draw_header_bar(draw, W, "KIỂM THỬ BẢO MẬT: CHẶN THAO TÁC XÓA BẰNG ĐƯỜNG DẪN GET TRỰC TIẾP")
    
    # 405 Alert Card
    draw.rounded_rectangle([200, 170, 1000, 630], radius=12, fill=PANEL_BG, outline=ERROR_RED, width=2)
    draw.text((450, 210), "405", font=get_font(60, True), fill=ERROR_RED)
    draw.text((320, 300), "METHOD NOT ALLOWED - PHƯƠNG THỨC BỊ TỪ CHỐI", font=get_font(18, True), fill=TEXT_MAIN)
    
    draw.text((260, 340), "Hành vi kiểm thử: Copy đường dẫn xóa dán trực tiếp vào thanh URL trình duyệt và Enter.", font=get_font(13), fill=TEXT_MUTED)
    
    # Detail box
    draw.rounded_rectangle([240, 380, 960, 500], radius=6, fill=BG_DARK, outline=(71, 85, 105), width=1)
    draw.text((260, 395), "• URL đã nhập: GET http://localhost:8088/smartroom/admin/rooms/delete/101", font=get_font(13), fill=(248, 113, 113))
    draw.text((260, 425), "• Phản hồi bảo mật: The GET method is not supported for this route. Supported methods: DELETE.", font=get_font(13, True), fill=WARNING_AMBER)
    draw.text((260, 455), "• Cơ chế bảo vệ: Bắt buộc gửi qua AJAX DELETE hoặc Form POST kèm @csrf token hợp lệ.", font=get_font(13), fill=SUCCESS)
    
    # Result evaluation
    draw.rounded_rectangle([240, 530, 960, 595], radius=6, fill=(20, 83, 45), outline=SUCCESS, width=2)
    draw.text((260, 550), "✅ KẾT QUẢ: NGĂN CHẶN TRIỆT ĐỂ LỖ BỔNG CSRF XÓA DỮ LIỆU TỰ ĐỘNG", font=get_font(14, True), fill=(187, 247, 208))
    draw.text((260, 573), "Hệ thống bảo đảm người dùng không thể bị lừa click vào liên kết ẩn để xóa nhầm dữ liệu.", font=get_font(12), fill=TEXT_MAIN)

    img.save('generated_test_imgs/TC14_get_delete_blocked.png')

# Run all
make_tc01()
make_tc02()
make_tc03()
make_tc04()
make_tc05()
make_tc06()
make_tc07()
make_tc08()
make_tc09()
make_tc10()
make_tc11()
make_tc12()
make_tc13()
make_tc14()

print("Successfully generated all 14 test scenario images!")
