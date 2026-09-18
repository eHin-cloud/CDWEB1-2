# Hướng Dẫn Sử Dụng Bộ Công Cụ Đọc & Tìm Kiếm Báo Cáo (.docx)

Thư mục này chứa các file script Python chạy độc lập bằng **thư viện chuẩn Python** (không cần cài thêm bất kỳ package bên ngoài như `python-docx`).

---

## 1. Các tệp tin trong thư mục

- `read_docx.py`: Trích xuất toàn bộ nội dung từ file `.docx` (bao gồm văn bản, tiêu đề cấp độ, bảng biểu) và xuất ra màn hình hoặc file Markdown/Text.
- `search_docx.py`: Tìm kiếm nhanh từ khóa bất kỳ trong file `.docx` (tìm theo đoạn văn, tiêu đề, hàng trong bảng biểu).
- `baocaomoi.md`: Bản xuất định dạng Markdown hoàn chỉnh được trích xuất từ `baocaomoi.docx`.
- `baocaomoi.docx`: Tệp báo cáo gốc.

---

## 2. Cách sử dụng

### Đọc và xuất nội dung:
```powershell
# In trực tiếp toàn bộ nội dung ra màn hình console
python read_docx.py

# Xuất ra file Markdown (.md) để đọc dễ dàng
python read_docx.py baocaomoi.docx -o baocaomoi.md

# Chỉ xem dàn ý / mục lục các tiêu đề (Headings)
python read_docx.py --headings-only

# Đọc một file .docx bất kỳ khác
python read_docx.py duong_dan_den_file_khac.docx -o ketqua.md
```

### Tìm kiếm từ khóa:
```powershell
# Tìm kiếm từ khóa bất kỳ trong baocaomoi.docx
python search_docx.py "VietQR"
python search_docx.py "WebAuthn"
python search_docx.py "Database"

# Tìm kiếm từ khóa trong file .docx khác
python search_docx.py "Từ khóa" file_khac.docx
```
