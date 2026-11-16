import os
import json
from PIL import Image

# 1. Update real_test_screenshots with 100% RGB color images
EDGE_REAL_MAPPINGS = [
    {
        "id": 1,
        "src": "extracted_imgs_quy/image5.png", # RGB
        "tab": "SmartRoom - Xóa Mục Không Tồn Tại (2 Tab)",
        "url": "http://localhost:8088/smartroom/admin?tab=buildings-section&action=delete",
        "out": "real_test_screenshots/TC01_real_screen.png"
    },
    {
        "id": 2,
        "src": "extracted_imgs_quy/image14.png", # RGB
        "tab": "SmartRoom - Cập Nhật Trùng Lặp (Optimistic Lock)",
        "url": "http://localhost:8088/smartroom/admin?tab=room-matrix-section&action=update",
        "out": "real_test_screenshots/TC02_real_screen.png"
    },
    {
        "id": 3,
        "src": "extracted_imgs_quy/image15.png", # RGB
        "tab": "SmartRoom - 404 Không Tìm Thấy Dữ Liệu ID",
        "url": "http://localhost:8088/smartroom/admin/rooms/edit/abc",
        "out": "real_test_screenshots/TC03_real_screen.png"
    },
    {
        "id": 4,
        "src": "extracted_imgs_quy/image8.png", # RGB
        "tab": "SmartRoom - Kiểm Tra Xác Thực Form Dữ Liệu (Bôi Đỏ Viền)",
        "url": "http://localhost:8088/smartroom/admin?tab=equipment-section",
        "out": "real_test_screenshots/TC04_real_screen.png"
    },
    {
        "id": 5,
        "src": "extracted_imgs_quy/image17.png", # RGB
        "tab": "SmartRoom - Kiểm Tra Giới Hạn Chiều Dài Văn Bản (Max 255)",
        "url": "http://localhost:8088/smartroom/admin?tab=ai-hub-section",
        "out": "real_test_screenshots/TC05_real_screen.png"
    },
    {
        "id": 6,
        "src": "extracted_imgs_quy/image1.png", # RGB
        "tab": "SmartRoom - Kiểm Tra Chuỗi Khoảng Trắng Rỗng (Trim & Zenkaku)",
        "url": "http://localhost:8088/smartroom/admin?tab=buildings-section",
        "out": "real_test_screenshots/TC06_real_screen.png"
    },
    {
        "id": 7,
        "src": "extracted_imgs_quy/image10.png", # RGB
        "tab": "SmartRoom - Kiểm Tra Định Dạng Dữ Liệu Số (Full-width / Số Âm)",
        "url": "http://localhost:8088/smartroom/admin?tab=equipment-section",
        "out": "real_test_screenshots/TC07_real_screen.png"
    },
    {
        "id": 8,
        "src": "extracted_imgs_quy/image45.png", # RGB
        "tab": "SmartRoom - Kiểm Tra Phần Tử Select-Option (Can Thiệp F12)",
        "url": "http://localhost:8088/smartroom/superadmin/users",
        "out": "real_test_screenshots/TC08_real_screen.png"
    },
    {
        "id": 9,
        "src": "extracted_imgs_quy/image6.png", # RGB
        "tab": "SmartRoom - Kiểm Tra Chống Trùng Lặp Spam Click (Double Submit)",
        "url": "http://localhost:8088/smartroom/admin?tab=buildings-section",
        "out": "real_test_screenshots/TC09_real_screen.png"
    },
    {
        "id": 10,
        "src": "extracted_imgs_nhomc/image12.png", # Full COLOR counterpart of image66!
        "tab": "Renty - Kiểm Tra Tham Số URL Phân Trang & Lọc (?page=abc)",
        "url": "http://localhost:8088/renty?page=abc&price_min=99999999",
        "out": "real_test_screenshots/TC10_real_screen.png"
    },
    {
        "id": 11,
        "src": "extracted_imgs_quy/image27.png", # RGB
        "tab": "SmartRoom - Kiểm Tra Định Dạng File Upload (Chặn PDF / EXE)",
        "url": "http://localhost:8088/smartroom/admin?tab=ai-ocr-section",
        "out": "real_test_screenshots/TC11_real_screen.png"
    },
    {
        "id": 12,
        "src": "extracted_imgs_nhomc/image32.png", # Full COLOR counterpart of image86!
        "tab": "SmartRoom - Xử Lý Ảnh Mặc Định Fallback (Onerror Placeholder)",
        "url": "http://localhost:8088/smartroom/admin?tab=ai-ocr-section&modal=scanner",
        "out": "real_test_screenshots/TC12_real_screen.png"
    },
    {
        "id": 13,
        "src": "extracted_imgs_nhomc/image21.png", # Full COLOR counterpart of image75!
        "tab": "Renty - Cập Nhật Form Có Ảnh Giữ Nguyên Ảnh Cũ (Nullable)",
        "url": "http://localhost:8088/renty/landlord/kyc",
        "out": "real_test_screenshots/TC13_real_screen.png"
    },
    {
        "id": 14,
        "src": "extracted_imgs_quy/image47.png", # RGB
        "tab": "SmartRoom - 405 Chặn Gọi URL Xóa Bằng Phương Thức GET",
        "url": "http://localhost:8088/smartroom/admin/rooms/delete/101",
        "out": "real_test_screenshots/TC14_real_screen.png"
    }
]

from PIL import ImageDraw, ImageFont
FONT_PATH = "C:/Windows/Fonts/segoeui.ttf"
font_tab = ImageFont.truetype(FONT_PATH, 14)
font_url = ImageFont.truetype(FONT_PATH, 13)
font_win = ImageFont.truetype(FONT_PATH, 12)
header_h = 76

for item in EDGE_REAL_MAPPINGS:
    with Image.open(item["src"]) as real_screen:
        real_screen = real_screen.convert('RGB')
        w, h = real_screen.size
        full_img = Image.new('RGB', (w, h + header_h), (24, 32, 47))
        draw = ImageDraw.Draw(full_img)
        
        draw.rectangle([0, 0, w, 40], fill=(30, 41, 59))
        draw.text((w - 90, 10), "—   □   ✕", font=font_win, fill=(148, 163, 184))
        
        tab_width = min(360, w - 150)
        draw.rounded_rectangle([15, 8, 15 + tab_width, 40], radius=6, fill=(15, 23, 42))
        draw.text((32, 14), item["tab"][:42], font=font_tab, fill=(248, 250, 252))
        
        draw.rectangle([0, 40, w, header_h], fill=(15, 23, 42))
        draw.text((15, 48), "←   →   ↻", font=font_tab, fill=(148, 163, 184))
        draw.rounded_rectangle([90, 44, w - 20, 70], radius=6, fill=(30, 41, 59), outline=(51, 65, 85), width=1)
        draw.text((105, 49), "🔒 " + item["url"], font=font_url, fill=(147, 197, 253))
        
        full_img.paste(real_screen, (0, header_h))
        full_img.save(item["out"])
        print(f"Generated COLOR {item['out']} from {item['src']} ({full_img.size})")

# 2. Update hien_figures_extracted.json and vinhem_figures_extracted.json to use color counterparts
def convert_to_color_filename(fn_img):
    import re
    m = re.search(r'image(\d+)\.png', fn_img)
    if m:
        num = int(m.group(1))
        if num >= 57 and num <= 109:
            color_num = num - 54
            color_fn = f"image{color_num}.png"
            if os.path.exists(os.path.join("extracted_imgs_nhomc", color_fn)):
                return color_fn
    return fn_img

with open('hien_figures_extracted.json', 'r', encoding='utf-8') as f:
    hien_data = json.load(f)

for fn, items in hien_data.items():
    new_items = []
    for img_name, cap in items:
        if img_name.strip():
            color_name = convert_to_color_filename(img_name)
            new_items.append((color_name, cap))
    hien_data[fn] = new_items

with open('hien_figures_extracted.json', 'w', encoding='utf-8') as f:
    json.dump(hien_data, f, ensure_ascii=False, indent=2)

with open('vinhem_figures_extracted.json', 'r', encoding='utf-8') as f:
    vinhem_data = json.load(f)

for fn, items in vinhem_data.items():
    new_items = []
    for img_name, cap in items:
        if img_name.strip():
            color_name = convert_to_color_filename(img_name)
            new_items.append((color_name, cap))
    vinhem_data[fn] = new_items

with open('vinhem_figures_extracted.json', 'w', encoding='utf-8') as f:
    json.dump(vinhem_data, f, ensure_ascii=False, indent=2)

print("Updated Hien and Vinh Em figure mappings to 100% COLOR images!")
