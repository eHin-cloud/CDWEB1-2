# BÁO CÁO NHẬT KÝ KIỂM THỬ ỨNG DỤNG WEB (TEST LOG REPORT)
**Dự án:** Hệ thống Quản trị Nhà trọ & Sàn tìm phòng thông minh (SmartRoom & Renty - CDWEB1-2)  
**Tiêu chuẩn kiểm thử:** Bộ 14 Tình huống Kiểm thử Ứng dụng Web Tiêu chuẩn (Khoa CNTT)  
**Người thực hiện (Tester/QA):** Lead QA Engineer & Giảng viên Đánh giá Độc lập  
**Ngày thực hiện kiểm thử:** 16/11/2026  
**Môi trường:** PHP 8.2 (XAMPP), MySQL 8.0, Laravel 11, Google Chrome 124, Apache Solr 9.x  

---

## I. MỤC TIÊU & PHẠM VI KIỂM THỬ
Thực hiện rà soát, kiểm thử chức năng (Functional Testing), kiểm thử ngoại lệ (Edge Cases / Negative Testing), kiểm thử đồng thời (Concurrency Testing) và bảo mật dữ liệu (Security Testing) trên toàn bộ 10 tính năng phân hệ A do sinh viên **Nguyễn Thanh Hiền** phụ trách, kết hợp các phân hệ của **Nguyễn Anh Quý** và **Huỳnh Văn Vĩnh Em**.

---

## II. KẾT QUẢ THỰC THI CHI TIẾT 14 TÌNH HUỐNG KIỂM THỬ

### TH_01: Xóa mục không tồn tại (Concurrency Delete)
* **Kịch bản:** Trên trình duyệt, tại màn hình danh sách (Quản lý phòng trọ / Hợp đồng / Cư dân), mở 2 tab cùng hiển thị bản ghi `id = 1`:
  * *Tab 1:* Nhấn nút Xóa phần tử `id = 1`.
  * *Tab 2:* Nhấn nút Xóa phần tử `id = 1` vừa bị xóa ở Tab 1.
* **Kết quả mong đợi:**
  * Tab 1: Xóa thành công, hiển thị Toast "Xóa thành công".
  * Tab 2: Báo lỗi "Mục này đã bị xóa trước đó hoặc không tồn tại", không được gây lỗi sập trang (HTTP 500 Crash).
* **Kết quả thực tế trong Code (`RoomController.php` - dòng `destroy`):**
  ```php
  $room = Room::where('id', $id)->first();
  if (!$room) {
      return redirect()->route('admin.rooms.index')->with('error', 'Phòng trọ này đã bị xóa trước đó hoặc không tồn tại.');
  }
  ```
  Hệ thống bắt `!$room` trước khi gọi delete, trả về thông báo lỗi thân thiện.
* **Đánh giá:** ✅ **PASS (Đạt)**

---

### TH_02: Cập nhật trùng lặp (Concurrency Update / Optimistic Locking)
* **Kịch bản:** Mở màn hình Sửa phòng trọ (`id = 1`) thành 2 Tab riêng biệt trên cùng trình duyệt:
  * *Tab 1:* Chỉnh sửa giá phòng thành `3.500.000đ` -> Bấm Lưu.
  * *Tab 2:* Chỉnh sửa giá phòng thành `3.800.000đ` (trên nền dữ liệu cũ chưa tải lại) -> Bấm Lưu.
* **Kết quả mong đợi:**
  * Tab 1: Cập nhật OK.
  * Tab 2: Chặn lưu đè dữ liệu cũ, hiển thị cảnh báo: *"Dữ liệu phòng trọ này đã được cập nhật bởi một phiên làm việc khác. Vui lòng tải lại trang trước khi lưu."*
* **Kết quả thực tế trong Code (`RoomController.php` - dòng `update`):**
  ```php
  $submittedVersion = $request->input('version');
  if ($submittedVersion != $room->version) {
      return back()->withInput()->with('error', 'Dữ liệu phòng trọ này đã được cập nhật bởi một phiên làm việc khác. Vui lòng tải lại trang trước khi lưu.');
  }
  ```
  Phân hệ Room đã cài đặt cơ chế **Khóa lạc quan (Optimistic Locking)** với trường `version`. Tab 2 bị chặn lưu thành công.  
  *(Lưu ý: Phân hệ Cư dân và Thiết bị chưa cài trường version, vẫn dùng Last-write-wins)*.
* **Đánh giá:** ⚠️ **PARTIAL PASS (Đạt ở module Phòng trọ, Cần bổ sung ở module Cư dân)**

---

### TH_03: ID không tồn tại (ID Parameter Tampering)
* **Kịch bản:** Trên thanh địa chỉ URL của màn hình xem chi tiết hoặc sửa (ví dụ: `/rooms/{id}/edit` hoặc `/rooms/{id}`):
  * *Case 1:* Chỉnh sửa `id = abc` (ký tự chữ).
  * *Case 2:* Chỉnh sửa `id = 99999999999` (ID vượt ngưỡng hoặc không tồn tại trong DB).
* **Kết quả mong đợi:** Hệ thống chặn ngay, trả về trang lỗi thân thiện HTTP 404 (Không tìm thấy trang), không để lộ Stack Trace cơ sở dữ liệu.
* **Kết quả thực tế trong Code:**
  ```php
  if (!is_numeric($id) || intval($id) <= 0) {
      abort(404, 'Phòng trọ không tồn tại.');
  }
  $room = Room::where('id', $id)->first();
  if (!$room) { abort(404, 'Phòng trọ không tồn tại.'); }
  ```
  Laravel bắt chuẩn xác `is_numeric` và trả về `abort(404)`. Giao diện 404 tùy biến của Renty hiển thị đẹp mắt.
* **Đánh giá:** ✅ **PASS (Đạt)**

---

### TH_04: Kiểm tra tính hợp lệ của Form (Form Validation)
* **Kịch bản:** Tại các form Thêm mới / Cập nhật (Phòng trọ, Profile, Hẹn xem phòng, Hợp đồng, KYC):
  * Cố tình để trống các ô bắt buộc (Họ tên, SĐT, Số phòng, Giá tiền).
  * Nhập sai định dạng Email, nhập SĐT không đúng 10 số.
* **Kết quả mong đợi:** Chặn gửi Form, viền đỏ ô nhập liệu vi phạm, hiển thị thông điệp hướng dẫn cụ thể bên dưới ô.
* **Kết quả thực tế trong Code:**
  * Toàn bộ Form Request (`RoomRequest`, `KycRequest`, `ContactRequest`) đều có rules: `required`, `numeric`, `min:0`, `regex:/^(0[3|5|7|8|9])[0-9]{8}$/`.
  * Giao diện Blade sử dụng class `@error('field') border-rose-500 @enderror` bôi đỏ viền sắc nét.
* **Đánh giá:** ✅ **PASS (Đạt)**

---

### TH_05: Text quá tải text (Payload HTML / Max Length Testing)
* **Kịch bản:** 
  * Mở trang báo điện tử (VnExpress / Tuổi Trẻ), nhấn `Ctrl + U` copy toàn bộ mã nguồn HTML (hàng chục nghìn ký tự).
  * Dán toàn bộ mã nguồn vào các ô Text ngắn (Tên phòng, Số phòng, Họ tên, Mã CCCD, Tiêu đề).
  * Nhấn nút Lưu.
* **Kết quả mong đợi:** Hệ thống không bị treo hoặc lỗi tràn bộ nhớ (DB Buffer Overflow); hiển thị lỗi độ dài vượt quá quy định (ví dụ: *Tối đa 255 ký tự*).
* **Kết quả thực tế trong Code:**
  * Các trường ngắn đều có rule `max:255` hoặc `max:100`.
  * Nếu người dùng cố tình chèn script `<script>alert('XSS')</script>`, Laravel Blade sử dụng hàm `e()` và `{{ }}` tự động escape chống XSS tuyệt đối.
  * *Điểm trừ:* Ô "Mô tả chi tiết phòng trọ" cho phép nhập nội dung dài nhưng chưa giới hạn `max:10000`, nếu dán HTML quá nặng có thể gây tốn dung lượng DB.
* **Đánh giá:** ⚠️ **PASS CÓ GHI CHÚ (Đạt 90%)**

---

### TH_06: Kiểm tra khoảng trắng & Khoảng trắng 2 byte (Whitespace & Full-width Space)
* **Kịch bản:** Tại ô nhập liệu văn bản:
  * *Case 1:* Nhập toàn bộ là dấu cách khoảng trắng thường (`   `).
  * *Case 2:* Nhập khoảng trắng 2 byte tiếng Nhật / Unicode (`\u3000` - Full-width ideographic space: `(　)a b`).
* **Kết quả mong đợi:** Hệ thống tự động cắt bỏ khoảng trắng đầu/cuối (trim), không lưu bản ghi rác toàn khoảng trắng.
* **Kết quả thực tế trong Code (`RoomController.php`):**
  ```php
  private function cleanWhitespace($string) {
      if (is_null($string)) return '';
      $cleaned = str_replace('　', ' ', $string); // Thay khoảng trắng 2 byte thành khoảng trắng 1 byte
      return trim($cleaned); // Trim sạch đầu cuối
  }
  ```
  Code đã được lập trình sẵn hàm `cleanWhitespace` loại bỏ khoảng trắng toàn sừng 2 bytes.
* **Đánh giá:** ✅ **PASS (Đạt - Xử lý rất chỉn chu)**

---

### TH_07: Kiểm tra dữ liệu số dạng Full-width (Full-width Numbers)
* **Kịch bản:** Tại các ô nhập số (Giá tiền, Tiền cọc, Diện tích, Số tầng, Chỉ số điện nước):
  * Nhập dãy số dạng Full-width Unicode: `０１２３４５６７８９`.
* **Kết quả mong đợi:** Hệ thống tự động chuyển đổi sang dạng số Half-width chuẩn `0-9` để lưu DB, hoặc báo lỗi định dạng số.
* **Kết quả thực tế trong Code (`RoomController.php`):**
  ```php
  private function toHalfWidth($string) {
      $fullWidth = ['０', '１', '２', '３', '４', '５', '６', '７', '８', '９'];
      $halfWidth = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
      return str_replace($fullWidth, $halfWidth, $string);
  }
  ```
  Hàm `toHalfWidth` tự động chuẩn hóa số Unicode thành số nguyên an toàn trước khi lưu.
* **Đánh giá:** ✅ **PASS (Đạt - Điểm cộng nghiệp vụ)**

---

### TH_08: Kiểm tra trường lựa chọn (Select-Option Tampering)
* **Kịch bản:**
  * *Case 1:* Không chọn giá trị trong dropdown bắt buộc (để mặc định "Chọn tòa nhà", "Chọn loại phòng").
  * *Case 2:* Dùng F12 (Inspect Element) sửa giá trị `<option value="999999">` hoặc sửa sang `tenant_id` của chủ trọ khác rồi submit.
* **Kết quả mong đợi:** Báo lỗi chọn danh mục; Backend chặn lưu do không thỏa mãn rule `exists` và bảo toàn phân quyền Multi-tenancy.
* **Kết quả thực tế trong Code:**
  * Request có rule `exists:buildings,id`.
  * Có kiểm tra `building->tenant_id === Auth::user()->tenant_id` (chống lỗi BOLA / IDOR).
* **Đánh giá:** ✅ **PASS (Đạt)**

---

### TH_09: Kiểm tra trùng lặp dữ liệu do nhấn nút Lưu liên tục (Spam Submit / Double Click)
* **Kịch bản:** Tại form Thêm phòng trọ mới hoặc Đăng ký lịch hẹn:
  * Điền đầy đủ thông tin hợp lệ.
  * Nhấp chuột liên tục thật nhanh (Double-click / Spam click 4-5 lần vào nút "Lưu dữ liệu").
* **Kết quả mong đợi:** Chỉ có duy nhất 1 bản ghi được tạo trong CSDL, nút bấm phải bị `disabled` hoặc hiện hiệu ứng Spinner loading ngay sau nhấp đầu tiên.
* **Kết quả thực tế:**
  * Ở form Hợp đồng và form Phòng trọ: Nút bấm có hiệu ứng disable bằng JavaScript (`this.disabled = true; this.innerHTML = 'Đang xử lý...'`).
  * *Điểm trừ:* Ở form Hẹn xem phòng của khách vãng lai (Contact Request Modal) chưa khóa nút triệt để trên một số thiết bị chậm, nếu bấm cực nhanh có thể tạo ra 2 bản ghi trùng thời gian.
* **Đánh giá:** ⚠️ **PASS (Minor Bug ở modal hẹn phòng)**

---

### TH_10: Kiểm tra tham số URL Phân trang (URL Parameter Boundary)
* **Kịch bản:** Trên các URL có tham số phân trang hoặc lọc (Ví dụ: `/rooms?page=1`):
  * *Case 1:* Sửa thành `?page=abc`.
  * *Case 2:* Sửa thành `?page=99999` hoặc `?page=-5`.
* **Kết quả mong đợi:** Không bị văng lỗi màn hình trắng HTTP 500; Laravel Paginator tự động đưa về trang 1 hoặc hiển thị danh sách rỗng kèm thông báo *"Không có dữ liệu phù hợp"*.
* **Kết quả thực tế:**
  * Laravel Paginator mặc định chuyển `page=abc` về integer `0` -> tự động tải trang 1.
  * Trang `page=99999` trả về danh sách rỗng, giao diện hiển thị Empty State minh họa đẹp mắt.
* **Đánh giá:** ✅ **PASS (Đạt)**

---

### TH_11: Kiểm tra upload tệp sai định dạng (File Type Validation)
* **Kịch bản:** Tại phần tử tải tệp (Upload ảnh phòng, ảnh CCCD KYC, ảnh đại diện):
  * Cố tình chọn tải lên tệp văn bản `.pdf`, tệp nén `.zip`, hoặc tệp thực thi `.exe`, `.php`.
* **Kết quả mong đợi:** Chặn tải lên, hiển thị thông báo lỗi: *"Tệp tải lên phải là hình ảnh (jpg, jpeg, png, webp)"*.
* **Kết quả thực tế trong Code:**
  * Request validation sử dụng rule: `'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240'`.
  * Backend kiểm tra MIME type thực tế của file (không chỉ dựa vào đuôi file).
* **Đánh giá:** ✅ **PASS (Đạt chuẩn bảo mật)**

---

### TH_12: Kiểm tra hình ảnh không thể hiển thị (Broken Image Fallback)
* **Kịch bản:**
  * Tải ảnh phòng lên thành công.
  * Vào thư mục lưu trữ `storage/app/public/...` xóa vật lý file ảnh đó (giả lập trường hợp mất ảnh trên máy chủ hoặc link ảnh hỏng).
  * F5 tải lại trang danh sách phòng và chi tiết phòng.
* **Kết quả mong đợi:** Không hiển thị icon ảnh vỡ xấu xí (`broken image icon`); hệ thống tự động hiển thị ảnh giữ chỗ mặc định (`placeholder.png`).
* **Kết quả thực tế trong Code:**
  * Thẻ `<img>` trong Blade template đều được trang bị thuộc tính:
    ```html
    onerror="this.onerror=null; this.src='{{ asset('images/room-placeholder.jpg') }}';"
    ```
  * Khi ảnh chính bị lỗi, giao diện tự động thay thế bằng ảnh phòng mẫu chất lượng cao. Đây là điểm cộng lớn theo đúng yêu cầu kiểm thử!
* **Đánh giá:** 🌟 **PASS CÓ ĐIỂM CỘNG (+)**

---

### TH_13: Kiểm tra cập nhật Form có tệp hình ảnh (Update without re-uploading file)
* **Kịch bản:** 
  * *Lần 1:* Tạo phòng và upload ảnh bìa thành công.
  * *Lần 2:* Bấm Sửa phòng, chỉ sửa giá tiền từ `3.000.000đ` thành `3.200.000đ` và **không chọn tệp ảnh mới**, nhấn Lưu.
* **Kết quả mong đợi:** Ảnh của lần 1 vẫn được giữ nguyên vẹn; hệ thống KHÔNG ĐƯỢC bắt buộc người dùng phải chọn lại ảnh ở lần sửa thứ 2.
* **Kết quả thực tế trong Code (`RoomController.php` - `update`):**
  ```php
  if ($request->hasFile('images')) {
      // Chỉ khi người dùng chọn ảnh mới thì mới xử lý thay thế
      $this->uploadNewImages($room, $request->file('images'));
  }
  // Nếu không chọn ảnh mới, các bản ghi room_images cũ được bảo toàn 100%
  ```
  Rule validation khi update là `nullable|image`, không bắt buộc `required`.
* **Đánh giá:** ✅ **PASS (Đạt)**

---

### TH_14: Kiểm tra bảo mật tính năng Xóa qua URL (Direct GET Delete Protection)
* **Kịch bản:** 
  * Tại nút xóa một phòng trọ (`id = 5`), copy đường dẫn xóa (ví dụ: `http://localhost:8088/smartroom/admin/rooms/5/delete`).
  * Mở một tab trình duyệt khác hoặc dán trực tiếp đường dẫn đó lên thanh Address Bar và nhấn Enter (Gửi HTTP GET Request).
* **Kết quả mong đợi:** Hệ thống từ chối xóa, phản hồi lỗi `405 Method Not Allowed` hoặc `404 Not Found`. Hành động xóa bắt buộc phải đi qua HTTP `DELETE` hoặc `POST` có kèm mã xác thực Token `@csrf`.
* **Kết quả thực tế trong Code:**
  * Route web được đăng ký: `Route::delete('/smartroom/admin/rooms/{id}', ...)`.
  * Trong Controller có lớp phòng thủ kép:
    ```php
    if (!$request->isMethod('delete') && !$request->isMethod('post')) {
        abort(405, 'Phương thức không hợp lệ. Hành động xóa chỉ được thực thi thông qua yêu cầu POST/DELETE an toàn.');
    }
    ```
  * Thao tác Enter trên URL sẽ bị chặn đứng bằng mã lỗi HTTP 405.
* **Đánh giá:** ✅ **PASS (Đạt chuẩn bảo mật OWASP)**

---

## III. BẢNG TỔNG HỢP ĐIỂM CHẤM THẬT THEO TIÊU CHÍ TEST (10 TÍNH NĂNG)

Dựa trên kết quả thực tế của 14 tình huống kiểm thử trên kết hợp với mức độ hoàn thiện so với bản Đặc tả kỹ thuật gốc (Spec), điểm số thực tế được chốt như sau:

| STT | Tính năng phụ trách (Nguyễn Thanh Hiền) | Điểm tối đa | Điểm Test thực tế | Ghi chú đánh giá của Tester |
| :---: | :--- | :---: | :---: | :--- |
| **01** | Đăng ký, Đăng nhập (Xác thực OTP) | 1.0 | **0.85** | Vượt qua TH_04, TH_06. Trừ điểm do thiếu kênh OTP SMS theo Spec. |
| **02** | Quản lý Hồ sơ cá nhân & Cài đặt Chủ trọ | 1.0 | **0.90** | Vượt qua TH_04, TH_06, TH_13. Có VietQR và mã hóa Blind Index. |
| **03** | Quản lý danh mục Phòng trọ (CRUD Room) | 1.0 | **0.95** | Vượt qua xuất sắc TH_01, TH_02, TH_03, TH_06, TH_07, TH_11, TH_12, TH_13, TH_14. |
| **04** | Trang chủ tìm kiếm phòng (Landing Page) | 1.0 | **0.85** | Giao diện đẹp, chuẩn responsive. Trừ điểm do bỏ thanh search tĩnh của Spec gốc. |
| **05** | Bản đồ phòng trọ thông minh (Smart Map) | 1.0 | **0.85** | Tính khoảng cách động tốt. Đã vá lỗi kéo thanh bán kính về 0km gây mất phòng. |
| **06** | Trang Chi tiết phòng (Room Detail) | 1.0 | **0.90** | Vượt qua TH_03, TH_12 (Broken Image Fallback tốt). Đã bổ sung nút chuông báo. |
| **07** | Tiếp nhận & Quản lý yêu cầu hẹn xem phòng | 1.0 | **0.85** | Form gửi tốt, có badge thông báo. Trừ điểm do chưa có rate limit 3 lần/10 phút. |
| **08** | Hợp đồng online (Ký số Canvas & Xuất PDF) | 1.0 | **0.95** | Tính năng xuất sắc nhất: Ký 2 bên, OTP, Audit Trail, xuất PDF. |
| **09** | Xác thực định danh & Đăng ký Tích Xanh KYC | 1.0 | **0.90** | Vượt qua TH_04, TH_11, TH_13. Mở rộng 9-20 số, file tối đa 10MB. |
| **10**| Kiểm duyệt hồ sơ Chủ trọ (Admin Verification) | 1.0 | **0.85** | Phân quyền tốt, duyệt/từ chối chuẩn. Đã vá lỗi hiển thị theme sidebar trắng/đen. |
| **TỔNG** | **ĐÁNH GIÁ CHUNG NGUYỄN THANH HIỀN** | **10.0** | **`8.85 / 10`** | **Xếp loại: Giỏi (B+ / A-) — Điểm số kiểm thử thực tế rất vững chắc** |

---

### ĐÁNH GIÁ 2 BẠN CÙNG NHÓM THEO 14 TIÊU CHÍ TEST:
* **Nguyễn Anh Quý (Vận hành & Kế toán):** **`8.9 / 10`** (Có điểm cộng lớn ở TH_02, TH_07 và tính năng AI Vision OCR đọc đồng hồ điện nước; cần lưu ý chặn số âm ở Bulk Input).
* **Huỳnh Văn Vĩnh Em (Cư dân & Tìm kiếm):** **`8.9 / 10`** (Chatbot AI NLP xử lý tiếng lóng địa phương xuất sắc; TH_10 phân trang và TH_04 bộ lọc hoạt động rất nhạy).

