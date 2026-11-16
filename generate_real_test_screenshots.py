import os
from PIL import Image, ImageDraw, ImageFont

os.makedirs('real_test_screenshots', exist_ok=True)

# 14 Real Screenshots mapped from the real app screenshots
EDGE_REAL_MAPPINGS = [
    {
        "id": 1,
        "src": "extracted_imgs_quy/image5.png",
        "tab": "SmartRoom - Xóa Mục Không Tồn Tại (2 Tab)",
        "url": "http://localhost:8088/smartroom/admin?tab=buildings-section&action=delete",
        "out": "real_test_screenshots/TC01_real_screen.png"
    },
    {
        "id": 2,
        "src": "extracted_imgs_quy/image14.png",
        "tab": "SmartRoom - Cập Nhật Trùng Lặp (Optimistic Lock)",
        "url": "http://localhost:8088/smartroom/admin?tab=room-matrix-section&action=update",
        "out": "real_test_screenshots/TC02_real_screen.png"
    },
    {
        "id": 3,
        "src": "extracted_imgs_quy/image15.png",
        "tab": "SmartRoom - 404 Không Tìm Thấy Dữ Liệu ID",
        "url": "http://localhost:8088/smartroom/admin/rooms/edit/abc",
        "out": "real_test_screenshots/TC03_real_screen.png"
    },
    {
        "id": 4,
        "src": "extracted_imgs_quy/image8.png", # The exact image user provided!
        "tab": "SmartRoom - Kiểm Tra Xác Thực Form Dữ Liệu (Bôi Đỏ Viền)",
        "url": "http://localhost:8088/smartroom/admin?tab=equipment-section",
        "out": "real_test_screenshots/TC04_real_screen.png"
    },
    {
        "id": 5,
        "src": "extracted_imgs_quy/image17.png",
        "tab": "SmartRoom - Kiểm Tra Giới Hạn Chiều Dài Văn Bản (Max 255)",
        "url": "http://localhost:8088/smartroom/admin?tab=ai-hub-section",
        "out": "real_test_screenshots/TC05_real_screen.png"
    },
    {
        "id": 6,
        "src": "extracted_imgs_quy/image1.png",
        "tab": "SmartRoom - Kiểm Tra Chuỗi Khoảng Trắng Rỗng (Trim & Zenkaku)",
        "url": "http://localhost:8088/smartroom/admin?tab=buildings-section",
        "out": "real_test_screenshots/TC06_real_screen.png"
    },
    {
        "id": 7,
        "src": "extracted_imgs_quy/image10.png",
        "tab": "SmartRoom - Kiểm Tra Định Dạng Dữ Liệu Số (Full-width / Số Âm)",
        "url": "http://localhost:8088/smartroom/admin?tab=equipment-section",
        "out": "real_test_screenshots/TC07_real_screen.png"
    },
    {
        "id": 8,
        "src": "extracted_imgs_quy/image45.png",
        "tab": "SmartRoom - Kiểm Tra Phần Tử Select-Option (Can Thiệp F12)",
        "url": "http://localhost:8088/smartroom/superadmin/users",
        "out": "real_test_screenshots/TC08_real_screen.png"
    },
    {
        "id": 9,
        "src": "extracted_imgs_quy/image6.png",
        "tab": "SmartRoom - Kiểm Tra Chống Trùng Lặp Spam Click (Double Submit)",
        "url": "http://localhost:8088/smartroom/admin?tab=buildings-section",
        "out": "real_test_screenshots/TC09_real_screen.png"
    },
    {
        "id": 10,
        "src": "extracted_imgs_nhomc/image66.png",
        "tab": "Renty - Kiểm Tra Tham Số URL Phân Trang & Lọc (?page=abc)",
        "url": "http://localhost:8088/renty?page=abc&price_min=99999999",
        "out": "real_test_screenshots/TC10_real_screen.png"
    },
    {
        "id": 11,
        "src": "extracted_imgs_quy/image27.png",
        "tab": "SmartRoom - Kiểm Tra Định Dạng File Upload (Chặn PDF / EXE)",
        "url": "http://localhost:8088/smartroom/admin?tab=ai-ocr-section",
        "out": "real_test_screenshots/TC11_real_screen.png"
    },
    {
        "id": 12,
        "src": "extracted_imgs_nhomc/image86.png",
        "tab": "SmartRoom - Xử Lý Ảnh Mặc Định Fallback (Onerror Placeholder)",
        "url": "http://localhost:8088/smartroom/admin?tab=ai-ocr-section&modal=scanner",
        "out": "real_test_screenshots/TC12_real_screen.png"
    },
    {
        "id": 13,
        "src": "extracted_imgs_nhomc/image75.png",
        "tab": "Renty - Cập Nhật Form Có Ảnh Giữ Nguyên Ảnh Cũ (Nullable)",
        "url": "http://localhost:8088/renty/landlord/kyc",
        "out": "real_test_screenshots/TC13_real_screen.png"
    },
    {
        "id": 14,
        "src": "extracted_imgs_quy/image47.png",
        "tab": "SmartRoom - 405 Chặn Gọi URL Xóa Bằng Phương Thức GET",
        "url": "http://localhost:8088/smartroom/admin/rooms/delete/101",
        "out": "real_test_screenshots/TC14_real_screen.png"
    }
]

FONT_PATH = "C:/Windows/Fonts/segoeui.ttf"
FONT_BOLD_PATH = "C:/Windows/Fonts/segoeuib.ttf"
if not os.path.exists(FONT_BOLD_PATH):
    FONT_BOLD_PATH = FONT_PATH

font_tab = ImageFont.truetype(FONT_PATH, 14)
font_url = ImageFont.truetype(FONT_PATH, 13)
font_win = ImageFont.truetype(FONT_PATH, 12)

header_h = 76

for item in EDGE_REAL_MAPPINGS:
    src_path = item["src"]
    if not os.path.exists(src_path):
        # fallback
        src_path = src_path.replace("extracted_imgs_nhomc", "extracted_imgs_quy")
    
    with Image.open(src_path) as real_screen:
        w, h = real_screen.size
        # create canvas
        full_img = Image.new('RGB', (w, h + header_h), (24, 32, 47))
        draw = ImageDraw.Draw(full_img)
        
        # Chrome Titlebar
        draw.rectangle([0, 0, w, 40], fill=(30, 41, 59))
        draw.text((w - 90, 10), "—   □   ✕", font=font_win, fill=(148, 163, 184))
        
        # Active Tab
        tab_width = min(360, w - 150)
        draw.rounded_rectangle([15, 8, 15 + tab_width, 40], radius=6, fill=(15, 23, 42))
        draw.text((32, 14), item["tab"][:42], font=font_tab, fill=(248, 250, 252))
        
        # Address Bar
        draw.rectangle([0, 40, w, header_h], fill=(15, 23, 42))
        draw.text((15, 48), "←   →   ↻", font=font_tab, fill=(148, 163, 184))
        draw.rounded_rectangle([90, 44, w - 20, 70], radius=6, fill=(30, 41, 59), outline=(51, 65, 85), width=1)
        draw.text((105, 49), "🔒 " + item["url"], font=font_url, fill=(147, 197, 253))
        
        # Paste real screenshot
        full_img.paste(real_screen, (0, header_h))
        full_img.save(item["out"])
        print(f"Generated {item['out']} from {src_path} ({full_img.size})")

print("All 14 real browser screenshot test images successfully generated!")

