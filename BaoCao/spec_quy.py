# -*- coding: utf-8 -*-
"""
Đặc tả chi tiết 13 chức năng của Nguyễn Anh Quý (Nhóm phó)
Chuẩn hóa: 10 người đọc đều code giống nhau, coder đọc vô code được ngay.
"""

SPEC_QUY = """
## B. CÁC MODULE & ĐẶC TẢ KỸ THUẬT DO NGUYỄN ANH QUÝ (NHÓM PHÓ) PHỤ TRÁCH

---

### [FEAT-AQ-01] CRUD Quản lý Cơ sở lưu trú (Properties/Hotels) & Guard Check an toàn
- **Git Branch**: `AnhQuy/quan-ly-co-so-luu-tru` (`AnhQuy-quan-ly-co-so-luu-tru`)
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\BuildingController@index`, `create`, `store`, `edit`, `update`, `destroy`
- **Endpoint**:
  - `GET /smartroom/admin/buildings` (`admin.buildings.index`)
  - `GET /smartroom/admin/buildings/create` (`admin.buildings.create`)
  - `POST /smartroom/admin/buildings/store` (`admin.buildings.store`)
  - `GET /smartroom/admin/buildings/{id}/edit` (`admin.buildings.edit`)
  - `POST /smartroom/admin/buildings/{id}/update` (`admin.buildings.update`)
  - `DELETE /smartroom/admin/buildings/{id}/delete` (`admin.buildings.destroy`)
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation POST /store & POST /{id}/update)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `name` | String | Có | `required\|string\|max:255` | "Tên cơ sở lưu trú không được để trống." |
| `address` | String | Có | `required\|string\|max:255` | "Địa chỉ cơ sở lưu trú không được để trống." |
| `total_floors` | Integer | Có | `required\|integer\|min:1\|max:100` | "Số tầng tối thiểu là 1 và tối đa là 100." |
| `phone` | String | Không | `nullable\|regex:/^(0[3\|5\|7\|8\|9])[0-9]{8}$/` | "Số điện thoại hotline cơ sở không hợp lệ." |
| `status` | String | Có | `required\|in:active,maintenance,inactive` | "Trạng thái hoạt động không hợp lệ." |
| `image` | File | Không | `nullable\|image\|mimes:jpeg,png,jpg,webp\|max:5120` | "File tải lên phải là hình ảnh (jpg, png, webp) và dung lượng không quá 5MB." |
| `amenities` | Array | Không | `nullable\|array` | "Danh sách tiện ích chung không hợp lệ." |
| `property_type`| String | Không | `nullable\|in:boarding,apartment,hotel` | "Loại hình cơ sở lưu trú không hợp lệ." |
| `checkin_time` | String | Không | `nullable\|date_format:H:i` | "Giờ check-in tiêu chuẩn không đúng định dạng HH:mm." |
| `checkout_time`| String | Không | `nullable\|date_format:H:i` | "Giờ check-out tiêu chuẩn không đúng định dạng HH:mm." |

#### 2. Business Logic Flow
1. **Kiểm tra quyền Multi-tenancy**: Mọi thao tác truy xuất hoặc cập nhật phải đảm bảo `building->tenant_id === auth()->user()->tenant_id`. Nếu không khớp, trả về HTTP 403.
2. **Logic Thêm mới (Store) & Cập nhật (Update)**:
   - Xử lý upload file ảnh: Nếu có file `image`, lưu trữ vào `storage/app/public/buildings/` với tên file tạo ngẫu nhiên UUID kèm đuôi tệp gốc; cập nhật đường dẫn vào cột `image`.
   - Tiện ích chung `amenities` được encode thành JSON array.
   - Kiểm tra logic giờ giấc (nếu là khách sạn): Nếu có cả `checkin_time` và `checkout_time`, kiểm tra `checkin_time > checkout_time` (check-in sau check-out).
3. **Cơ chế Guard Check an toàn khi Xóa (Destroy)**:
   - Đếm số lượng phòng trực thuộc: `$roomCount = Room::where('building_id', $id)->count()`.
   - **Guard Check**: Nếu `$roomCount > 0`, **CHẶN TUYỆT ĐỐI** hành vi xóa; trả về phản hồi lỗi kèm số lượng phòng còn tồn tại.
   - Nếu `$roomCount === 0`: Thực thi SoftDelete (`building->delete()`).
4. **Phản hồi**: Redirect về danh sách kèm Flash Session Toast.

#### 3. Database Operation
- **Bảng tác động**: `buildings` (hoặc `properties`).
- **Cột**: `tenant_id`, `name`, `address`, `total_floors`, `phone`, `status`, `image`, `amenities`, `deleted_at`.
- **Cơ chế**: SoftDeletes (`deleted_at` timestamp).

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về `admin.buildings.index` kèm `session('success', 'Lưu thông tin cơ sở lưu trú thành công!')`.
- **Thất bại khi Xóa (HTTP 422)**: Redirect back kèm `session('error', 'Không thể xóa cơ sở vì vẫn còn 8 phòng trực thuộc. Vui lòng chuyển hoặc xóa các phòng trước!')`.

#### 5. UI/UX Specification & Xử lý lỗi
- **Form element**: `#building-form`, input `#building-name`, `#total-floors`, vùng upload `#building-image-dropzone`.
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống tên cơ sở lưu trú khi tạo mới | Ô tên cơ sở bôi đỏ `border-red-500`: "Tên cơ sở lưu trú không được để trống". |
| Số tầng nhập không hợp lệ (nhỏ hơn 1 hoặc lớn hơn 100) | Ô số tầng bôi đỏ: "Số tầng tối thiểu là 1 và tối đa 100". |
| Tải lên ảnh sai định dạng hoặc vượt quá 5MB | Vùng upload bôi đỏ: "File tải lên phải là hình ảnh hợp lệ (jpg, png, webp) và dung lượng không quá 5MB". |
| Xóa cơ sở lưu trú khi vẫn còn phòng trực thuộc (Guard Check) | Modal cảnh báo chặn thao tác: "Không thể xóa cơ sở vì vẫn còn X phòng trọ trực thuộc. Vui lòng chuyển hoặc xóa các phòng trước". |

---

### [FEAT-AQ-02] CRUD Quản lý Phòng lưu trú (Rooms - đa mô hình) & Cấu hình Serial công tơ
- **Git Branch**: `AnhQuy/quan-ly-phong`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\RoomController@index`, `create`, `store`, `edit`, `update`, `destroy`
- **Endpoint**:
  - `GET /smartroom/admin/rooms` (`admin.rooms.index`)
  - `GET /smartroom/admin/rooms/create` (`admin.rooms.create`)
  - `POST /smartroom/admin/rooms/store` (`admin.rooms.store`)
  - `GET /smartroom/admin/rooms/{id}/edit` (`admin.rooms.edit`)
  - `POST /smartroom/admin/rooms/{id}/update` (`admin.rooms.update`)
  - `DELETE /smartroom/admin/rooms/{id}/delete` (`admin.rooms.destroy`)
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation POST /store & POST /{id}/update)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `building_id` | Integer | Có | `required\|integer\|exists:buildings,id` | "Vui lòng chọn cơ sở lưu trú hợp lệ." |
| `room_number` | String | Có | `required\|string\|max:50` | "Số phòng không được để trống." |
| `floor` | Integer | Có | `required\|integer\|min:0\|max:100` | "Tầng phòng tọa lạc phải từ 0 (tầng trệt) đến 100." |
| `price` | Numeric | Có | `required\|numeric\|min:0` | "Giá thuê phòng phải là số dương lớn hơn hoặc bằng 0." |
| `area` | Integer | Có | `required\|integer\|min:5\|max:500` | "Diện tích phòng phải từ 5m² đến 500m²." |
| `deposit` | Numeric | Có | `required\|numeric\|min:0` | "Tiền đặt cọc giữ phòng không được là số âm." |
| `room_type` | String | Có | `required\|in:standard,deluxe,vip,studio` | "Hạng phòng phải là standard, deluxe, vip hoặc studio." |
| `rental_type`| String | Có | `required\|in:month,day,hour` | "Hình thức cho thuê phải là theo tháng, theo ngày hoặc theo giờ." |
| `status` | String | Có | `required\|in:empty,occupied,overdue,maintenance` | "Trạng thái phòng không hợp lệ." |
| `electric_meter_serial`| String | Không | `nullable\|string\|max:100` | "Số SX công tơ điện tối đa 100 ký tự." |
| `water_meter_serial` | String | Không | `nullable\|string\|max:100` | "Số SX đồng hồ nước tối đa 100 ký tự." |
| `amenities` | Array | Không | `nullable\|array` | "Danh sách tiện ích phòng không hợp lệ." |
| `image` | File | Không | `nullable\|image\|mimes:jpeg,png,jpg,webp\|max:5120` | "Ảnh đại diện phòng tối đa 5MB." |
| `images` | Array | Không | `nullable\|array\|max:10` | "Chỉ được tải lên tối đa 10 ảnh thực tế của phòng." |
| `images.*` | File | Không | `image\|mimes:jpeg,png,jpg,webp\|max:5120` | "Mỗi ảnh thực tế tối đa 5MB." |
| `video` | File | Không | `nullable\|file\|mimes:mp4,mov,webm\|max:30720` | "Video không gian phòng phải có định dạng MP4/MOV/WebM và dung lượng tối đa 30MB." |
| `version` | Integer | Không | `nullable\|integer` | "Mã phiên bản đồng bộ không hợp lệ." |

#### 2. Business Logic Flow
1. **Kiểm tra trùng lặp số phòng trong cùng tòa nhà**:
   Query kiểm tra: `Room::where('building_id', $buildingId)->where('room_number', $roomNumber)->where('id', '!=', $currentId)->exists()`. Nếu trùng lặp, trả về HTTP 422: *"Số phòng này đã tồn tại trong tòa nhà"*.
2. **Khóa lạc quan (Optimistic Locking)**:
   Khi `update`: So sánh giá trị `version` gửi lên với `room->version` trong CSDL.
   - Nếu `version_request != room->version`: Ném ngoại lệ xung đột dữ liệu (HTTP 409 Conflict): *"Dữ liệu phòng vừa được cập nhật bởi một người dùng khác. Vui lòng tải lại trang"*.
   - Nếu khớp: Thực hiện cập nhật và tăng `version = version + 1`.
3. **Cấu hình Serial công tơ**: Lưu trữ `electric_meter_serial` và `water_meter_serial` phục vụ thuật toán AI Vision Bulk Match.
4. **Xử lý tệp tin Media**:
   - Lưu ảnh đại diện và mảng ảnh thực tế vào `storage/app/public/rooms/photos/`.
   - Lưu video vào `storage/app/public/rooms/videos/`.
5. **Guard Check khi Xóa phòng**: Nếu phòng có `status == 'occupied'` hoặc còn cư dân đang ở, cấm xóa (HTTP 422).

#### 3. Database Operation
- **Bảng tác động**: `rooms`.
- **Cột**: `tenant_id`, `building_id`, `room_number`, `floor`, `price`, `area`, `deposit`, `room_type`, `rental_type`, `status`, `electric_meter_serial`, `water_meter_serial`, `amenities`, `image`, `images`, `video`, `version`.

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về `admin.rooms.index` kèm `session('success', 'Lưu thông tin phòng thành công!')`.
- **Thất bại (HTTP 422 / 409)**: Redirect back với input và thông báo lỗi tương ứng.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống Số phòng, Diện tích hoặc Giá thuê | Các ô input bôi đỏ `border-red-500`: "Số phòng không được để trống", "Vui lòng nhập giá phòng hợp lệ". |
| Số phòng bị trùng lặp trong cùng tòa nhà | Ô số phòng bôi đỏ: "Số phòng P.101 đã tồn tại trong tòa nhà này. Vui lòng chọn số phòng khác". |
| Tải file ảnh/video vượt quá dung lượng (Ảnh > 5MB, Video > 30MB) | Vùng upload bôi đỏ: "Dung lượng tệp vượt quá kích thước cho phép. Vui lòng nén file hoặc chọn tệp nhỏ hơn". |
| Xóa phòng đang có người ở (status = occupied) | Modal cảnh báo đỏ: "Không thể xóa phòng đang có người ở! Bạn phải làm thủ tục trả phòng cho cư dân trước khi xóa". |

---

### [FEAT-AQ-03] Sơ đồ Ma trận phòng trực quan theo tầng (Visual Room Matrix) & Real-time Housekeeping
- **Git Branch**: `AnhQuy/ma-tran-phong`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\RoomMatrixRealtimeController@updateStatus`, `stream`, `poll`, `App\Http\Controllers\HousekeepingController@index`, `updateStatus`
- **Endpoint**:
  - `POST /smartroom/admin/rooms/{id}/quick-status` (`admin.rooms.quick_status`)
  - `GET /smartroom/admin/rooms/matrix/stream` (SSE Server-Sent Events)
  - `GET /smartroom/admin/rooms/matrix/poll` (Long-polling fallback)
  - `GET /smartroom/housekeeping` (`admin.housekeeping.index`)
  - `POST /smartroom/housekeeping/{roomId}/status` (`admin.housekeeping.update`)
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Request Validation POST /quick-status)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `status` | String | Có | `required\|in:empty,occupied,cleaning,overdue,maintenance` | "Trạng thái phòng cập nhật không hợp lệ." |

#### 2. Business Logic Flow
1. **Kiểm tra logic chuyển trạng thái**:
   - Không cho phép chuyển thủ công sang `occupied` nếu phòng chưa có Hợp đồng hoặc Phiếu đặt phòng có hiệu lực.
   - Cho phép nhân viên buồng phòng đổi giữa `cleaning` (Cần dọn) và `empty` (Đã dọn xong - Sẵn sàng đón khách) chỉ với 1 chạm.
2. **Cập nhật CSDL**: Cập nhật `rooms.status = :status` và tăng `version = version + 1`.
3. **Phát sóng thời gian thực (Real-time Broadcast)**:
   - Đẩy sự kiện qua SSE (`stream`) hoặc Laravel Reverb WebSocket tới toàn bộ các tab trình duyệt của nhân viên lễ tân và quản lý đang mở.
4. **Phản hồi**: Trả về JSON trạng thái mới.

#### 3. Database Operation
- **Bảng tác động**: `rooms` (cập nhật cột `status`, `version`, `updated_at`).

#### 4. Output Specification
- **Thành công (HTTP 200)**: `{"success": true, "room_id": 15, "new_status": "empty", "status_label": "Còn trống", "color_class": "bg-emerald-500"}`.
- **Thất bại (HTTP 422)**: `{"success": false, "message": "Không thể chuyển trạng thái thủ công sang Đang ở. Vui lòng tạo Hợp đồng trước."}`.

#### 5. UI/UX Specification & Xử lý lỗi
- **Ma trận phòng**: Chia theo tầng (Tầng 1, Tầng 2...), mỗi phòng là 1 thẻ Card bo tròn với mã màu động:
  - `empty`: Viền xanh lá, nền `bg-emerald-500/10 text-emerald-400`
  - `occupied`: Viền đỏ, nền `bg-rose-500/10 text-rose-400`
  - `cleaning`: Viền cam, nền `bg-amber-500/10 text-amber-400` (Icon chổi quét)
  - `overdue`: Viền vàng cảnh báo nợ, nền `bg-yellow-500/10 text-yellow-400`
  - `maintenance`: Viền xám, nền `bg-slate-500/10 text-slate-400`
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Chuyển trạng thái sang Đang ở khi chưa lập hợp đồng | Toast cảnh báo vàng: "Không thể chuyển trạng thái thủ công sang Đang ở. Vui lòng tạo Hợp đồng hoặc Phiếu booking trước". |
| Mất kết nối SSE thời gian thực | Hệ thống tự động chuyển sang cơ chế Polling ngầm mỗi 5 giây mà không làm gián đoạn người dùng. |

---

### [FEAT-AQ-04] Chốt số Điện - Nước định kỳ hàng tháng (Đơn lẻ & Hàng loạt)
- **Git Branch**: `AnhQuy/chot-so-dien-nuoc-ai-ocr`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\AdminDashboardController@storeUtility`, `storeUtilityBulk`, `payUtility`
- **Endpoint**:
  - `POST /smartroom/admin/utility` (`smartroom.admin.utility.store`)
  - `POST /smartroom/admin/utility/bulk` (`smartroom.admin.utility.bulk_store`)
  - `POST /smartroom/admin/utility/{id}/pay` (`smartroom.admin.utility.pay`)
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Request Validation POST /utility)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `room_id` | Integer | Có | `required\|integer\|exists:rooms,id` | "Phòng được chốt số không tồn tại." |
| `billing_month`| String | Có | `required\|regex:/^[0-9]{4}-(0[1-9]\|1[0-2])$/` | "Tháng tính tiền phải có định dạng YYYY-MM (Ví dụ: 2026-06)." |
| `new_electricity`| Integer | Có | `required\|integer\|min:0` | "Chỉ số điện mới phải là số nguyên dương." |
| `new_water` | Integer | Có | `required\|integer\|min:0` | "Chỉ số nước mới phải là số nguyên dương." |
| `electricity_price`| Numeric | Có | `required\|numeric\|min:0` | "Đơn giá tiền điện không hợp lệ." |
| `water_price` | Numeric | Có | `required\|numeric\|min:0` | "Đơn giá tiền nước không hợp lệ." |

#### 2. Business Logic Flow
1. **Lấy chỉ số cũ**: Truy vấn bản ghi tháng trước liền kề của phòng. Nếu là tháng đầu tiên, `old_electricity` và `old_water` lấy từ chỉ số bàn giao ban đầu.
2. **Kiểm tra ràng buộc logic**:
   - `new_electricity >= old_electricity`: Nếu nhỏ hơn, báo lỗi HTTP 422: *"Chỉ số điện mới không được nhỏ hơn chỉ số cũ tháng trước"*.
   - `new_water >= old_water`: Tương tự với chỉ số nước.
   - Cảnh báo tiêu thụ bất thường: Nếu `(new_electricity - old_electricity) > 1000 kWh`, đánh dấu cờ cảnh báo rò rỉ điện.
3. **Tính toán chi phí**:
   - `electric_cost = (new_electricity - old_electricity) * electricity_price`.
   - `water_cost = (new_water - old_water) * water_price`.
   - `total_utility_cost = electric_cost + water_cost`.
4. **Lưu CSDL**: Ghi vào bảng `utility_records` với `status = 'draft'`. Tự động đồng bộ sang bảng `bills` (Hóa đơn tổng hợp).

#### 3. Database Operation
- **Bảng tác động**: `utility_records`, `bills`.

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về màn hình Điện nước kèm toast xanh: "Chốt số điện nước thành công!".
- **Thất bại (HTTP 422)**: Báo lỗi viền đỏ ô nhập liệu.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Chỉ số mới nhỏ hơn chỉ số cũ | Ô chỉ số mới bôi đỏ: "Chỉ số mới không được nhỏ hơn chỉ số cũ tháng trước (Chỉ số cũ: 150 kWh)". |
| Mức tiêu thụ tăng đột biến (> 1000 kWh) | Toast cảnh báo vàng: "Lượng điện tiêu thụ tăng đột biến (+1200 kWh). Vui lòng kiểm tra lại công tơ xem có bị nhầm số không!". |
| Phòng đã được chốt số trong tháng đó rồi | Thông báo: "Hóa đơn điện nước tháng này của phòng đã được lập. Bạn chỉ có thể cập nhật chỉnh sửa lại bản ghi cũ". |

---

### [FEAT-AQ-05] AI Vision OCR Nhận diện mặt công tơ đơn lẻ & Quét hàng loạt khớp Serial phòng tự động
- **Git Branch**: `AnhQuy/chot-so-dien-nuoc-ai-ocr`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\AdminDashboardController@aiOcrMeter`, `aiOcrMeterBulk`
- **Endpoint**:
  - `POST /smartroom/admin/ai/ocr-meter` (`smartroom.admin.ai.ocr_meter`)
  - `POST /smartroom/admin/ai/ocr-meter-bulk` (`smartroom.admin.ai.ocr_meter_bulk`)
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation POST /ocr-meter-bulk)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `building_id` | Integer | Có | `required\|integer\|exists:buildings,id` | "Vui lòng chọn cơ sở lưu trú hợp lệ." |
| `meter_type` | String | Có | `required\|in:electricity,water` | "Loại công tơ phải là điện (electricity) hoặc nước (water)." |
| `images` | Array | Có | `required\|array\|min:1\|max:30` | "Số lượng ảnh công tơ tải lên từ 1 đến tối đa 30 ảnh." |
| `images.*` | File | Có | `image\|mimes:jpeg,png,jpg,webp\|max:10240` | "Ảnh công tơ phải có định dạng hợp lệ và dung lượng không quá 10MB." |

#### 2. Business Logic Flow
1. **Nạp danh mục Serial của cơ sở**: Truy vấn danh sách phòng thuộc `building_id`, lấy ra danh sách cặp `[room_id, room_number, electric_meter_serial, water_meter_serial]`.
2. **Gọi Google Gemini Vision API**:
   - Duyệt qua từng file ảnh, convert sang chuỗi `base64`.
   - Gửi yêu cầu phân tích thị giác AI kèm Structured Output Schema:
     `{"meter_reading": <int>, "serial_number": "<string>", "confidence": <float>}`.
3. **Thuật toán Khớp số Serial (Serial Matching Engine)**:
   - So sánh chuỗi `serial_number` AI đọc được với `electric_meter_serial` (hoặc `water_meter_serial`) của các phòng trong CSDL.
   - Nếu trùng khớp: Gán `matched = true`, `room_id = room.id`, `room_number = room.room_number`.
   - Nếu không khớp: Gán `matched = false`, gắn nhãn `"Cần gán phòng thủ công"`.
4. **Phản hồi**: Trả về danh sách đối soát JSON để hiển thị trực quan lên Modal chốt số hàng loạt.

#### 3. Database Operation
- Không ghi CSDL tại bước này (chỉ phân tích và gợi ý dữ liệu). Dữ liệu được ghi khi người dùng bấm Lưu qua `storeUtilityBulk`.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Trả về JSON kết quả gồm `total_processed`, `matched_count`, `unmatched_count` và mảng chi tiết từng phòng kèm chỉ số cũ, chỉ số mới, lượng tiêu thụ.
- **Thất bại (HTTP 422 / 500)**: Báo lỗi định dạng ảnh hoặc lỗi timeout từ Google Gemini API.

#### 5. UI/UX Specification & Xử lý lỗi
- **Modal Quét hàng loạt**: `#bulk-meter-ocr-modal`, hiệu ứng Laser Scan xanh lướt qua thumbnail các bức ảnh.
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Ảnh chụp đồng hồ quá mờ hoặc lóa sáng | Toast cảnh báo: "AI không nhận diện rõ số trên mặt đồng hồ do ảnh mờ/thiếu sáng. Vui lòng chụp lại rõ nét hoặc tự nhập tay". |
| Không tìm thấy Số SX trùng khớp với phòng nào | Dòng kết quả hiển thị nhãn vàng 'Cần gán phòng' kèm dropdown danh sách phòng để chủ trọ chọn nhanh trước khi lưu. |
| Tải vượt quá 30 ảnh trong 1 lượt | Modal hiển thị cảnh báo: "Hệ thống hỗ trợ quét tối đa 30 ảnh công tơ trong một lượt và chỉ chấp nhận định dạng ảnh JPG, PNG, WEBP". |

---

### [FEAT-AQ-06] Động cơ tính cước tự động (BillingEngine) & Bảng phân bổ doanh thu
- **Git Branch**: `AnhQuy/tinh-cuoc-hoa-don-vietqr`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Service & Endpoint**: `App\Services\BillingEngine`, `GET /api/revenue-breakdown`
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Tính toán hóa đơn phòng)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `room_id` | Integer | Có | `required\|integer\|exists:rooms,id` | "Phòng tính cước không hợp lệ." |
| `billing_month`| String | Có | `required\|regex:/^[0-9]{4}-(0[1-9]\|1[0-2])$/` | "Tháng tính cước không hợp lệ." |

#### 2. Business Logic Flow
1. **Tổng hợp đa thành phần chi phí**:
   - `room_fee`: Tiền thuê phòng cơ sở (từ bảng `rooms.price`).
   - `electric_fee`: Tiền điện tiêu thụ trong kỳ tính theo đơn giá.
   - `water_fee`: Tiền nước tiêu thụ trong kỳ.
   - `management_fee`: Phí quản lý chung cư (nếu là căn hộ: tính theo diện tích `area * management_fee_rate`).
   - `minibar_fee`: Phụ phí đồ uống, dịch vụ buồng phòng phát sinh (từ bảng `hotel_folio_items` nếu có).
   - `discount`: Khấu trừ khuyến mãi / giảm giá (nếu có).
2. **Công thức tính tổng tiền**:
   `total_amount = room_fee + electric_fee + water_fee + management_fee + minibar_fee - discount`.
3. **Cập nhật hoặc Tạo bản ghi Hóa đơn**: Ghi vào bảng `bills` kèm mã hóa đơn duy nhất `BILL-{YYYYMM}-{ROOM_NUMBER}` và hạn thanh toán `due_date = ngày 5 tháng kế tiếp`.
4. **Phân tích cơ cấu doanh thu**: API `/api/revenue-breakdown` trả về tỷ trọng phần trăm từng nguồn thu phục vụ vẽ biểu đồ Donut Chart (Chart.js).

#### 3. Database Operation
- **Bảng tác động**: `bills`, `utility_records`, `hotel_folio_items`.

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: Trả về `total`, cấu trúc `breakdown` (room, electric, water, service) và tỷ trọng `percentages`.

#### 5. UI/UX Specification & Xử lý lỗi
- **Biểu đồ doanh thu**: Thẻ `<canvas id="revenueDoughnutChart"></canvas>` hiển thị 4 màu: Tiền phòng (Xanh dương `#38bdf8`), Điện (Vàng `#facc15`), Nước (Xanh lơ `#06b6d4`), Dịch vụ (Tím `#a855f7`).

---

### [FEAT-AQ-07] Xuất Hóa đơn / Bảng kê Folio PDF chuẩn in kèm Mã VietQR động NAPAS247
- **Git Branch**: `AnhQuy/tinh-cuoc-hoa-don-vietqr`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `AdminDashboardController@printUtility`, `HotelReceptionController@printFolio`, `PaymentController@index`
- **Endpoint**:
  - `GET /smartroom/admin/utility/{id}/print` (`smartroom.admin.utility.print`)
  - `GET /smartroom/admin/hotel/folio/{bookingId}` (`admin.hotel.folio`)
  - `GET /smartroom/admin/payments` (`admin.payments.index`)
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Tham số xuất in)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `id` | Integer (Route) | Có | `exists:utility_records,id` | "Bản ghi hóa đơn không tồn tại." |

#### 2. Business Logic Flow
1. **Kiểm tra thông tin tài khoản ngân hàng chủ trọ**:
   Lấy `bank_name`, `bank_account_number`, `landlord_name` từ hồ sơ chủ trọ đã xác minh KYC.
   - **Guard Check**: Nếu chủ trọ chưa cập nhật STK ngân hàng, chặn xuất VietQR và hiển thị cảnh báo yêu cầu cài đặt.
2. **Sinh mã VietQR động chuẩn NAPAS247**:
   - Sử dụng định dạng QuickLink chuẩn VietQR:
     `https://img.vietqr.io/image/{BANK_ID}-{ACCOUNT_NO}-compact2.png?amount={TOTAL}&addInfo={BILL_CODE}&accountName={LANDLORD_NAME}`.
   - Mã QR chứa sẵn: STK ngân hàng thụ hưởng, chính xác số tiền cần nộp đến từng đồng, và nội dung chuyển khoản là mã hóa đơn.
3. **Kết xuất file PDF chuẩn in (DomPDF)**:
   - Sử dụng `Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.utility_invoice', $data)`.
   - Thiết lập khổ giấy A4 hoặc A5 đứng, nhúng font tiếng Việt hỗ trợ Unicode, hiển thị mã VietQR nổi bật ở góc dưới.
4. **Phản hồi**: Stream file PDF trực tiếp trên trình duyệt hoặc tải về máy.

#### 3. Database Operation
- **Đọc**: `utility_records`, `rooms`, `residents`, `tenants`, `landlord_profiles`. Không làm biến đổi dữ liệu.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Trả về PDF stream MIME `application/pdf` sẵn sàng bấm Ctrl+P để in hóa đơn nhiệt/A4.
- **Thất bại (HTTP 422)**: Báo lỗi chưa cấu hình tài khoản nhận tiền.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Chủ trọ chưa hoàn tất xác minh KYC hoặc chưa có STK | Modal chặn: "Bạn cần hoàn tất xác minh định danh KYC (CCCD & STK Ngân hàng) trước khi sử dụng tính năng nhận tiền online qua VietQR". |
| Lỗi mạng khi gọi API cổng VietQR | Hiển thị khung thông báo dự phòng: "Không tải được ảnh mã QR từ cổng VietQR. Vui lòng chuyển khoản thủ công theo thông tin STK bên dưới". |

---

### [FEAT-AQ-08] Quét nợ tự động và gửi tin nhắn nhắc tiền phòng Zalo/SMS/Telegram kèm link VietQR
- **Git Branch**: `AnhQuy/nhac-no-zalo-sms`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Service**: `AdminDashboardController@autoRemindUtilities`, `notifyUtility`, `App\Services\SmsZaloService`, `NotificationService`
- **Endpoint**: `POST /smartroom/admin/utility/auto-remind`, `POST /smartroom/admin/utility/{id}/notify`
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation POST /auto-remind)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `channel` | String | Không | `nullable\|in:all,zalo,sms,telegram` | "Kênh gửi tin nhắn không hợp lệ." |

#### 2. Business Logic Flow
1. **Quét danh sách hóa đơn trễ hạn**: Truy vấn các hóa đơn trong `utility_records` có `status = 'draft'` hoặc `'sent'` và ngày hiện tại đã quá hạn thanh toán (`due_date < now()`).
2. **Xây dựng nội dung tin nhắn cá nhân hóa**:
   - Template mẫu: *"Kính gửi [Tên_Cư_Dân] phòng [Số_Phòng], Ban Quản Lý xin gửi hóa đơn tiền phòng tháng [Tháng]. Tổng thanh toán: [Số_Tiền]đ. Quý khách vui lòng bấm vào liên kết sau để quét mã VietQR thanh toán nhanh: [Short_Link]. Xin cảm ơn!"*.
3. **Phân phối tin nhắn đa kênh (Background Dispatch)**:
   - Gửi tin Zalo ZNS / SMS Brandname qua `SmsZaloService`.
   - Gửi tin nhắn bot Telegram đến nhóm quản trị viên.
   - Ghi lịch sử gửi vào bảng `notification_logs` để chặn việc gửi spam nhiều lần trong cùng một ngày.
4. **Cập nhật trạng thái**: Chuyển trạng thái hóa đơn sang `status = 'sent'`.

#### 3. Database Operation
- **Bảng tác động**: `utility_records` (cập nhật status), `notification_logs` (lưu vết gửi tin).

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: `{"success": true, "reminded_count": 8, "message": "Đã gửi tin nhắn nhắc tiền phòng thành công đến 8 phòng trễ hạn!"}`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Gửi tin nhắc nợ cho phòng chưa có số điện thoại | Báo lỗi trên danh sách: "Không thể gửi tin nhắc nợ đến Phòng 201: Cư dân chưa cập nhật số điện thoại liên lạc". |
| Gửi tin nhắc nợ nhiều lần liên tục trong ngày | Popup cảnh báo: "Phòng này đã được gửi tin nhắc nợ hôm nay lúc 08:30. Bạn có chắc chắn muốn gửi tiếp?". |

---

### [FEAT-AQ-09] Quản lý Danh mục Trang thiết bị - Tài sản kho & Phân bổ phòng (Equipment & RoomEquipment)
- **Git Branch**: `AnhQuy/quan-ly-trang-thiet-bi`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\EquipmentController@index`, `store`, `update`, `allocate`, `recover`, `destroy`
- **Endpoint**:
  - `GET /smartroom/admin/equipment` (`admin.equipment.index`)
  - `POST /smartroom/admin/equipment/store` (`admin.equipment.store`)
  - `POST /smartroom/admin/equipment/{id}/update` (`admin.equipment.update`)
  - `POST /smartroom/admin/equipment/allocate` (`admin.equipment.allocate`)
  - `POST /smartroom/admin/equipment/recover` (`admin.equipment.recover`)
  - `DELETE /smartroom/admin/equipment/{id}/delete` (`admin.equipment.destroy`)
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Bàn giao thiết bị vào phòng POST /allocate)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `equipment_id` | Integer | Có | `required\|integer\|exists:equipment,id` | "Trang thiết bị không tồn tại." |
| `room_id` | Integer | Có | `required\|integer\|exists:rooms,id` | "Phòng tiếp nhận thiết bị không hợp lệ." |
| `quantity` | Integer | Có | `required\|integer\|min:1` | "Số lượng bàn giao tối thiểu là 1." |
| `condition` | String | Có | `required\|string\|max:100` | "Vui lòng ghi rõ tình trạng thiết bị (VD: Mới 100%, Hoạt động tốt)." |

#### 2. Business Logic Flow
1. **Kiểm tra tồn kho thực tế**: Truy vấn `equipment = Equipment::findOrFail($equipment_id)`.
   - **Guard Check**: Nếu `equipment->quantity < $request->quantity`, ném lỗi HTTP 422: *"Số lượng thiết bị trong kho không đủ để bàn giao"*.
2. **Mở DB Transaction**:
   - Trừ số lượng tồn kho trong bảng `equipment`: `quantity = quantity - $request->quantity`.
   - Thêm hoặc cập nhật bản ghi trong bảng trung gian `room_equipment`: gán `room_id`, `equipment_id`, số lượng bàn giao, tình trạng và `assigned_date = now()`.
3. **Quy trình Thu hồi về kho (/recover)**:
   - Khi cư dân trả phòng hoặc thiết bị hỏng cần sửa: Xóa/giảm bản ghi trong `room_equipment` và cộng hoàn trả số lượng vào kho `equipment`.
4. **Guard Check khi Xóa danh mục thiết bị**: Nếu thiết bị đang được phân bổ trong bất kỳ phòng nào, cấm xóa danh mục thiết bị khỏi hệ thống.

#### 3. Database Operation
- **Bảng tác động**: `equipment`, `room_equipment`.

#### 4. Output Specification
- **Thành công (HTTP 302)**: Redirect về `admin.equipment.index` kèm `session('success', 'Bàn giao trang thiết bị vào phòng thành công!')`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống tên hoặc mã thiết bị khi thêm mới | Các ô input bôi đỏ: "Vui lòng nhập tên trang thiết bị" và "Mã thiết bị không được để trống". |
| Bàn giao số lượng vượt quá tồn kho thực tế | Ô số lượng bôi đỏ: "Số lượng thiết bị trong kho không đủ để bàn giao (Tồn kho hiện tại: 2 cái, yêu cầu: 5 cái)". |
| Xóa danh mục thiết bị đang được sử dụng trong phòng | Modal chặn: "Không thể xóa trang thiết bị này! Thiết bị đang được phân bổ trong các phòng trọ. Bạn phải thu hồi về kho trước khi xóa". |

---

### [FEAT-AQ-10] Sổ quỹ thu - chi và ghi nhận dòng tiền phát sinh ngoài tiền phòng (Transactions)
- **Git Branch**: `AnhQuy/so-quy-thu-chi`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\ReportController@index`, `storeTransaction`
- **Endpoint**: `GET /smartroom/admin/reports` (`admin.reports.index`), `POST /smartroom/admin/reports/transactions` (`admin.reports.transaction.store`)
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation POST /transactions)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `type` | String | Có | `required\|in:income,expense` | "Loại phiếu phải là Thu (income) hoặc Chi (expense)." |
| `category` | String | Có | `required\|string\|max:100` | "Khoản mục thu chi không được để trống." |
| `amount` | Numeric | Có | `required\|numeric\|min:1000` | "Số tiền giao dịch tối thiểu là 1.000 VNĐ." |
| `description` | String | Có | `required\|string\|max:500` | "Vui lòng nhập lý do / diễn giải khoản thu chi." |
| `transaction_date`| Date | Có | `required\|date\|before_or_equal:today` | "Ngày ghi nhận không thể là ngày trong tương lai." |

#### 2. Business Logic Flow
1. **Gán Tenant Scoping**: Tự động gán `tenant_id = auth()->user()->tenant_id`.
2. **Lưu bản ghi dòng tiền**: Tạo bản ghi mới trong bảng `transactions` (hoặc `cash_flows`).
3. **Tính toán số dư lũy kế**:
   - `total_income = sum(amount) where type = 'income'`.
   - `total_expense = sum(amount) where type = 'expense'`.
   - `net_cash_flow = total_income - total_expense`.
4. **Phản hồi**: Redirect về báo cáo tài chính hiển thị thẻ KPI dòng tiền thuần thời gian thực.

#### 3. Database Operation
- **Bảng tác động**: `transactions`.

#### 4. Output Specification
- **Thành công (HTTP 302)**: Điều hướng về `admin.reports.index` kèm `session('success', 'Ghi nhận phiếu thu/chi thành công!')`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Bỏ trống số tiền hoặc hạng mục chi khi lập phiếu | Viền đỏ các ô input: "Vui lòng nhập lý do phát sinh" và "Số tiền phải lớn hơn 0 đ". |
| Chọn ngày lập phiếu lớn hơn ngày hiện tại | Báo lỗi: "Ngày ghi nhận phiếu thu/chi không thể là ngày trong tương lai". |

---

### [FEAT-AQ-11] AI Google Gemini tự động viết bài mô tả phòng chuẩn SEO thu hút khách thuê
- **Git Branch**: `AnhQuy/ai-viet-mo-ta-phong`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\RoomController@generateDescription`
- **Endpoint & Method**: `POST /smartroom/admin/rooms/description/ai` (`admin.rooms.description.ai`)
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Request Validation)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `room_number` | String | Có | `required\|string` | "Số phòng không được để trống." |
| `area` | Numeric | Có | `required\|numeric` | "Diện tích phòng không hợp lệ." |
| `price` | Numeric | Có | `required\|numeric` | "Giá thuê phòng không hợp lệ." |
| `amenities` | Array | Không | `nullable\|array` | "Danh sách tiện ích không hợp lệ." |
| `highlights` | String | Không | `nullable\|string\|max:500` | "Ghi chú đặc điểm nổi bật tối đa 500 ký tự." |

#### 2. Business Logic Flow
1. **Thiết lập Prompt Marketing chuyên nghiệp**:
   Gửi yêu cầu đến Google Gemini API (`gemini-2.5-flash`):
   - Đóng vai chuyên gia Copywriter Bất động sản.
   - Tổng hợp các tham số: Diện tích, giá, tiện ích (máy lạnh, ban công, gác lửng), vị trí khu vực.
   - Yêu cầu cấu trúc bài đăng: Tiêu đề giật tít hấp dẫn, nội dung mô tả tiện nghi sinh động, bảng giá minh bạch, lời kêu gọi hành động (Call To Action - CTA) đặt lịch xem phòng ngay.
2. **Tối ưu SEO**: Tự động chèn các từ khóa tìm kiếm phổ biến (ví dụ: *phòng trọ giá rẻ, căn hộ mini đầy đủ nội thất, giờ giấc tự do, an ninh 24/7*).
3. **Phản hồi**: Trả về chuỗi văn bản mô tả để chèn vào textarea `description` của form phòng.

#### 3. Database Operation
- Không ghi CSDL tại bước này.

#### 4. Output Specification
- **Thành công (HTTP 200)**: `{"success": true, "description": "🌟 SIÊU PHẨM PHÒNG TRỌ BAN CÔNG THOÁNG MÁT... Giá chỉ 3.5tr/tháng..."}`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Chưa nhập thông số cơ bản của phòng | Viền đỏ: "Vui lòng nhập ít nhất một vài đặc điểm nổi bật của phòng trọ để AI xử lý". |
| Mất kết nối API | Toast cảnh báo lỗi kết nối và gợi ý nhập mô tả thủ công. |

---

### [FEAT-AQ-12] Nhật ký thao tác quản trị viên hệ thống (AdminActivityLog)
- **Git Branch**: `AnhQuy/nhat-ky-kiem-toan`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Service**: `App\Http\Controllers\AdminActivityLogController@index`, `App\Services\AdminActivityLogger`
- **Endpoint**: `GET /smartroom/admin/activity-logs` (`admin.activity_logs.index`)
- **Middleware**: `auth`, `admin`, `role:landlord`

#### 1. Input Specification (Bộ lọc tìm kiếm nhật ký)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `action` | String | Không | `nullable\|string` | "Loại thao tác lọc không hợp lệ." |
| `user_id` | Integer | Không | `nullable\|integer\|exists:users,id` | "Người thực hiện không hợp lệ." |

#### 2. Business Logic Flow
1. **Lắng nghe sự kiện hệ thống (Model Observers & Middleware)**:
   - Tự động bắt các thao tác CRUD trên các bảng trọng yếu (`Room::created`, `Room::updated`, `Contract::deleted`...).
2. **Ghi nhật ký chi tiết**:
   - Lưu trữ: `user_id`, `action` (login, create, update, delete), `model_type`, `model_id`, `description`, `ip_address`, `user_agent`, mảng `old_values` và `new_values` (JSON).
3. **Phân quyền hiển thị**: Chủ trọ chỉ xem nhật ký hoạt động thuộc cơ sở của mình; Superadmin xem toàn bộ hệ thống.

#### 3. Database Operation
- **Bảng tác động**: `admin_activity_logs`.

#### 4. Output Specification
- **Thành công (HTTP 200)**: Render view `admin.activity_logs.index` với bảng phân trang 20 dòng/trang.

#### 5. UI/UX Specification & Xử lý lỗi
- **Bảng Timeline**: Hiển thị nhãn màu theo action: `create` (Xanh lá), `update` (Xanh dương), `delete` (Đỏ), `login` (Tím).

---

### [FEAT-AQ-13] Phân hệ Khách sạn / Lễ tân: Check-in, Check-out & Folio chi tiêu minibar
- **Git Branch**: `AnhQuy/PhanQuyen`
- **Thành viên phụ trách**: Nguyễn Anh Quý
- **Controller & Method**: `App\Http\Controllers\HotelReceptionController@checkIn`, `checkOut`, `addFolioItem`, `printFolio`
- **Endpoint**:
  - `POST /smartroom/admin/hotel/check-in` (`admin.hotel.checkin`)
  - `POST /smartroom/admin/hotel/folio/{bookingId}/items` (`admin.hotel.folio.add_item`)
  - `POST /smartroom/admin/hotel/check-out/{bookingId}` (`admin.hotel.checkout`)
  - `GET /smartroom/admin/hotel/folio/{bookingId}` (`admin.hotel.folio`)
- **Middleware**: `auth`, `admin`

#### 1. Input Specification (Check-in khách sạn POST /check-in)
| Tên Field | Kiểu | Bắt buộc | Validation Rules | Message Lỗi Cụ Thể |
|---|---|---|---|---|
| `room_id` | Integer | Có | `required\|integer\|exists:rooms,id` | "Phòng khách sạn không hợp lệ." |
| `guest_name` | String | Có | `required\|string\|max:255` | "Họ tên khách lưu trú không được để trống." |
| `guest_phone` | String | Có | `required\|regex:/^[0-9]{10}$/` | "Số điện thoại khách lưu trú không đúng 10 số." |
| `guest_cccd` | String | Không | `nullable\|string\|max:20` | "Số CCCD/Hộ chiếu không hợp lệ." |
| `rental_type` | String | Có | `required\|in:hour,day` | "Hình thức thuê phải là theo giờ hoặc theo ngày." |
| `checkin_time` | DateTime | Có | `required\|date` | "Thời điểm nhận phòng không hợp lệ." |

#### 2. Business Logic Flow
1. **Kiểm tra trạng thái phòng**: Phòng phải có `status == 'empty'`. Nếu đang `occupied` hoặc `cleaning`, ném lỗi chặn Check-in.
2. **Quy trình Check-in**:
   - Tạo bản ghi mới trong bảng `hotel_bookings` với `status = 'active'`.
   - Cập nhật phòng sang `status = 'occupied'`.
3. **Thêm phụ phí tiêu dùng minibar (addFolioItem)**:
   - Lễ tân ghi nhận nước ngọt, bia, giặt ủi vào bảng kê `hotel_folio_items` liên kết với `booking_id`.
4. **Quy trình Check-out**:
   - Tính tổng tiền phòng (theo số giờ hoặc số đêm thực tế) + tổng tiền minibar folio.
   - Chuyển `booking->status = 'completed'`, chuyển phòng sang `status = 'cleaning'` (Cần dọn vệ sinh buồng phòng).
   - Tự động xuất phiếu thanh toán Bảng kê Folio PDF có nhúng mã VietQR thanh toán.

#### 3. Database Operation
- **Bảng tác động**: `hotel_bookings`, `hotel_folio_items`, `rooms`, `cash_flows`.

#### 4. Output Specification
- **Thành công (HTTP 200 / JSON)**: `{"success": true, "booking_id": 45, "folio_url": "/smartroom/admin/hotel/folio/45"}`.

#### 5. UI/UX Specification & Xử lý lỗi
| Nguyên Nhân Phát Sinh Lỗi | Message Lỗi / Trạng Thái Giao Diện Hiển Thị Phản Hồi |
|---|---|
| Check-in vào phòng đang có khách hoặc chưa dọn dẹp | Modal chặn: "Phòng đang ở trạng thái Cần dọn dẹp vệ sinh. Vui lòng hoàn tất dọn phòng trước khi nhận khách mới". |
| Bỏ trống họ tên hoặc số điện thoại khách | Viền đỏ ô input bắt buộc. |
"""
