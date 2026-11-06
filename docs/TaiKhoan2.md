# HỆ THỐNG RENTY & SMARTROOM - DANH SÁCH TÀI KHOẢN MỚI TOÀN DIỆN (4 THÀNH PHỐ)

> **LƯU Ý QUAN TRỌNG VỀ ĐĂNG NHẬP:**
> - ⭐ **Mật khẩu chung cho tất cả tài khoản:** `123456`
> - ⭐ **Đăng nhập linh hoạt:** Tất cả tài khoản đều có thể đăng nhập bằng **Tên đăng nhập (Username)**, **Số điện thoại** hoặc **Email**.
> - ⭐ **Cổng đăng nhập chính:** `http://localhost:8000/login` (hoặc cổng cấu hình hiện tại)
> - ⭐ **Cổng quản trị Superadmin & Chủ trọ:** `http://localhost:8000/admin`
> - ⭐ **Cổng Resident Portal cho cư dân:** `http://localhost:8000/smartroom/resident`
> - ⭐ **Bản đồ thông minh tìm phòng (Smart Map):** `http://localhost:8000/renty` hoặc `http://localhost:8000/renty/map`

---

## 1. TÀI KHOẢN ADMIN HỆ THỐNG (SUPERADMIN) - [4 TÀI KHOẢN]

* **Quyền hạn (Role `admin`):** Toàn quyền kiểm soát toàn bộ nền tảng, xét duyệt hồ sơ KYC chủ trọ (phê duyệt/từ chối/thu hồi tích xanh), quản lý người dùng, phân quyền vai trò, báo cáo doanh thu tài chính toàn sàn, nhật ký kiểm toán hệ thống.

| STT | Username | Tên hiển thị | Số điện thoại | Email | Mật khẩu | Ghi chú |
| :---: | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `admin1` | Admin Hệ Thống 1 (Superadmin) | 0999000001 | admin1@smartroom.local | `123456` | Tài khoản quản trị chính 1 |
| 2 | `admin2` | Admin Hệ Thống 2 (Superadmin) | 0999000002 | admin2@smartroom.local | `123456` | Tài khoản quản trị chính 2 |
| 3 | `superadmin` | Superadmin Hệ Thống | 0999999999 | superadmin@smartroom.local | `123456` | Tài khoản superadmin gốc |
| 4 | `admin` | Admin Mặc Định | 0987654321 | admin@smartroom.com | `admin123` / `123456` | Tài khoản quản trị mặc định |

---

## 2. TÀI KHOẢN CHỦ TRỌ (LANDLORD) - [40 CHỦ TRỌ / 4 THÀNH PHỐ LỚN]

Hệ thống Seeder mới phân bổ đều cho 4 thành phố trọng điểm: **Hà Nội**, **TP. Hồ Chí Minh**, **Đà Nẵng**, **Đà Lạt** (mỗi tỉnh/thành phố đúng 10 chủ trọ).

* **Mật khẩu chung:** `123456`
* **Hỗ trợ 2 định dạng Username:**
  - Dạng số thứ tự: `chutro1` đến `chutro40`
  - Dạng vùng miền gợi nhớ: `chutro_hn_1..10`, `chutro_hcm_1..10`, `chutro_dn_1..10`, `chutro_dl_1..10`
* **Phân loại KYC xác minh:**
  - **32 Chủ trọ ĐÃ XÁC MINH KYC (Tích xanh uy tín `kyc_verified`):** Đầy đủ quyền đăng tin, phòng có huy hiệu xác thực, tích xanh nổi bật trên bản đồ.
  - **4 Chủ trọ CHỜ DUYỆT KYC (`pending`):** Đang gửi hồ sơ CCCD/GPKD, dùng để kiểm thử tính năng duyệt KYC từ phía Admin.
  - **4 Chủ trọ CHƯA XÁC MINH (`unverified`):** Chưa nộp hồ sơ, dùng để kiểm thử các hạn chế phân quyền.

### 2.1 KHU VỰC: Hà Nội (Tenant 1 - 10)

#### Chủ trọ 1: Hệ thống Chung cư mini SmartRoom Cầu Giấy
- **Tên đăng nhập:** `chutro1` HOẶC `chutro_hn_1`
- **Tên chủ trọ:** Trần Văn Hoàng
- **Số điện thoại:** `0988000001`
- **Email:** `contact@smartroom-caugiay.vn`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** MB - `999988880001` (TRAN VAN HOANG)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Chung cư mini SmartRoom Cầu Giấy - Tòa A [Mã: `HN01A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 12 Ngõ 105 Xuân Thủy, Phường Dịch Vọng Hậu, Quận Cầu Giấy, Hà Nội
  + Chung cư mini SmartRoom Cầu Giấy - Tòa B [Mã: `HN01B`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 36 Ngõ 86 Chùa Hà, Phường Quan Hoa, Quận Cầu Giấy, Hà Nội

#### Chủ trọ 2: Chuỗi Trọ & Chung cư mini Rentry Home Thanh Xuân
- **Tên đăng nhập:** `chutro2` HOẶC `chutro_hn_2`
- **Tên chủ trọ:** Lê Quản Lý Thanh Xuân
- **Số điện thoại:** `0988000002`
- **Email:** `chutro.thanhxuan@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** VCB - `999988880002` (LE QUAN LY THANH XUAN)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Khu Trọ Cao Cấp Rentry Vũ Tông Phan [Mã: `HN02A`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 85 Vũ Tông Phan, Phường Khương Trung, Quận Thanh Xuân, Hà Nội
  + Chung cư mini Rentry Khương Đình [Mã: `HN02B`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 120 Khương Đình, Phường Hạ Đình, Quận Thanh Xuân, Hà Nội

#### Chủ trọ 3: Căn hộ Dịch vụ Sen Vàng Đống Đa
- **Tên đăng nhập:** `chutro3` HOẶC `chutro_hn_3`
- **Tên chủ trọ:** Phạm Văn Đống Đa
- **Số điện thoại:** `0988000003`
- **Email:** `chutro.dongda@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** MB - `999988880003` (PHAM VAN DONG DA)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Chung cư mini Sen Vàng Xã Đàn [Mã: `HN03A`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 18 Ngõ 198 Xã Đàn, Phường Phương Liên, Quận Đống Đa, Hà Nội
  + Ký túc xá & Trọ Sinh viên Chùa Bộc [Mã: `HN03B`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 45 Chùa Bộc, Phường Quang Trung, Quận Đống Đa, Hà Nội

#### Chủ trọ 4: Hồ Tây Panorama Hotel & Luxury Suites
- **Tên đăng nhập:** `chutro4` HOẶC `chutro_hn_4`
- **Tên chủ trọ:** Hoàng Văn Tây Hồ
- **Số điện thoại:** `0988000004`
- **Email:** `chutro.tayho@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** Agribank - `999988880004` (HOANG VAN TAY HO)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Panorama Boutique Hotel Tây Hồ [Mã: `HN04A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 210 Lạc Long Quân, Phường Bưởi, Quận Tây Hồ, Hà Nội
  + Hồ Tây Studio View Lake [Mã: `HN04B`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 99 Trích Sài, Phường Thụy Khuê, Quận Tây Hồ, Hà Nội

#### Chủ trọ 5: Hệ thống Phòng trọ & Studio Times Light Hai Bà Trưng
- **Tên đăng nhập:** `chutro5` HOẶC `chutro_hn_5`
- **Tên chủ trọ:** Vũ Thị Hai Bà Trưng
- **Số điện thoại:** `0988000005`
- **Email:** `chutro.hbt@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** VCB - `999988880005` (VU THI HAI BA TRUNG)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Chung cư mini Times Light Bạch Mai [Mã: `HN05A`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 250 Bạch Mai, Phường Cầu Dền, Quận Hai Bà Trưng, Hà Nội

#### Chủ trọ 6: Khách sạn Mini & Căn hộ Liễu Giai Riverside Ba Đình
- **Tên đăng nhập:** `chutro6` HOẶC `chutro_hn_6`
- **Tên chủ trọ:** Trần Văn Ba Đình
- **Số điện thoại:** `0988000006`
- **Email:** `chutro.badinh@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** BIDV - `999988880006` (TRAN VAN BA DINH)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Liễu Giai Riverside Suites & Hotel [Mã: `HN06A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 35 Ngõ 12 Liễu Giai, Phường Cống Vị, Quận Ba Đình, Hà Nội

#### Chủ trọ 7: Tổ hợp Căn hộ & KTX Mỹ Đình Star Nam Từ Liêm
- **Tên đăng nhập:** `chutro7` HOẶC `chutro_hn_7`
- **Tên chủ trọ:** Bùi Tiến Đạt
- **Số điện thoại:** `0988000007`
- **Email:** `chutro.namtuliem@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** Techcombank - `999988880007` (BUI TIEN DAT)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Chung cư mini Mỹ Đình Star Đình Thôn [Mã: `HN07A`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 16 Ngõ 39 Đình Thôn, Phường Mỹ Đình 1, Quận Nam Từ Liêm, Hà Nội
  + Nhà trọ Sinh viên & Người đi làm Lê Đức Thọ [Mã: `HN07B`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 48 Ngõ 63 Lê Đức Thọ, Phường Mỹ Đình 2, Quận Nam Từ Liêm, Hà Nội

#### Chủ trọ 8: Chung cư mini & Khách sạn Eco Linh Đàm Hoàng Mai
- **Tên đăng nhập:** `chutro8` HOẶC `chutro_hn_8`
- **Tên chủ trọ:** Hoàng Văn Hùng
- **Số điện thoại:** `0988000008`
- **Email:** `chutro.hoangmai@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** MB - `999988880008` (HOANG VAN HUNG)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Eco Linh Đàm Hotel & Suites [Mã: `HN08A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 15 BT2 Bán đảo Linh Đàm, Phường Hoàng Liệt, Quận Hoàng Mai, Hà Nội

#### Chủ trọ 9: Cổ Nhuế Green House & KTX Học Viện Bắc Từ Liêm
- **Tên đăng nhập:** `chutro9` HOẶC `chutro_hn_9`
- **Tên chủ trọ:** Nguyễn Thị Mai
- **Số điện thoại:** `0988000009`
- **Email:** `chutro.bactuliem@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ⏳ CHỜ DUYỆT KYC (Pending hồ sơ)
- **Tài khoản ngân hàng:** VietinBank - `999988880009` (NGUYEN THI MAI)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Nhà trọ Sinh viên Cổ Nhuế Green House [Mã: `HN09A`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 72 Ngõ 145 Cổ Nhuế, Phường Cổ Nhuế 2, Quận Bắc Từ Liêm, Hà Nội

#### Chủ trọ 10: Tòa nhà Căn hộ Dịch vụ Hà Đông Central
- **Tên đăng nhập:** `chutro10` HOẶC `chutro_hn_10`
- **Tên chủ trọ:** Trịnh Văn Lâm
- **Số điện thoại:** `0988000010`
- **Email:** `chutro.hadong@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ⚠️ CHƯA XÁC MINH (Unverified)
- **Tài khoản ngân hàng:** VPBank - `999988880010` (TRINH VAN LAM)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Chung cư mini Hà Đông Central [Mã: `HN10A`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 68 Đường Chiến Thắng, Phường Văn Quán, Quận Hà Đông, Hà Nội

### 2.2 KHU VỰC: TP. Hồ Chí Minh (Tenant 11 - 20)

#### Chủ trọ 11: Saigon Central Boutique Hotel & Studio Q1
- **Tên đăng nhập:** `chutro11` HOẶC `chutro_hcm_1`
- **Tên chủ trọ:** Huỳnh Minh Trí
- **Số điện thoại:** `0938000011`
- **Email:** `chutro.saigonq1@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** VCB - `999988880011` (HUYNH MINH TRI)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Saigon Central Boutique Hotel Q1 [Mã: `HCM01A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 128 Lê Lai, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh
  + Căn hộ Studio Vintage Cống Quỳnh [Mã: `HCM01B`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 84 Cống Quỳnh, Phường Phạm Ngũ Lão, Quận 1, TP. Hồ Chí Minh

#### Chủ trọ 12: Hệ thống Căn hộ & KTX Sinh viên Renty Bình Thạnh
- **Tên đăng nhập:** `chutro12` HOẶC `chutro_hcm_2`
- **Tên chủ trọ:** Nguyễn Hoàng Phúc
- **Số điện thoại:** `0938000012`
- **Email:** `chutro.binhthanh@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** ACB - `999988880012` (NGUYEN HOANG PHUC)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Chung cư mini Renty HUTECH Điện Biên Phủ [Mã: `HCM02A`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 475A Điện Biên Phủ, Phường 25, Quận Bình Thạnh, TP. Hồ Chí Minh
  + Ký túc xá Cao cấp Xô Viết Nghệ Tĩnh [Mã: `HCM02B`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 280 Xô Viết Nghệ Tĩnh, Phường 21, Quận Bình Thạnh, TP. Hồ Chí Minh

#### Chủ trọ 13: Làng Đại học Mega Dorm & Căn hộ Công nghệ cao Thủ Đức
- **Tên đăng nhập:** `chutro13` HOẶC `chutro_hcm_3`
- **Tên chủ trọ:** Đặng Thanh Tùng
- **Số điện thoại:** `0938000013`
- **Email:** `chutro.thuduc@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** MB - `999988880013` (DANG THANH TUNG)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Thủ Đức University Dorm Võ Văn Ngân [Mã: `HCM03A`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 18 Võ Văn Ngân, Phường Linh Chiểu, TP. Thủ Đức, TP. Hồ Chí Minh
  + Căn hộ Studio Grand Park Thủ Đức [Mã: `HCM03B`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Đường Nguyễn Xiển, Phường Long Thạnh Mỹ, TP. Thủ Đức, TP. Hồ Chí Minh

#### Chủ trọ 14: The Garden Suites Hotel & Studio Quận 3
- **Tên đăng nhập:** `chutro14` HOẶC `chutro_hcm_4`
- **Tên chủ trọ:** Phan Thúy Hằng
- **Số điện thoại:** `0938000014`
- **Email:** `chutro.quan3@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** Techcombank - `999988880014` (PHAN THUY HANG)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + The Garden Suites Hotel Quận 3 [Mã: `HCM04A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 214 Nam Kỳ Khởi Nghĩa, Phường Võ Thị Sáu, Quận 3, TP. Hồ Chí Minh

#### Chủ trọ 15: Căn hộ Bách Khoa & Trọ Sinh viên Thành Thái Q10
- **Tên đăng nhập:** `chutro15` HOẶC `chutro_hcm_5`
- **Tên chủ trọ:** Lê Quốc Anh
- **Số điện thoại:** `0938000015`
- **Email:** `chutro.quan10@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** VCB - `999988880015` (LE QUOC ANH)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Bách Khoa Studio Home Thành Thái [Mã: `HCM05A`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 42 Thành Thái, Phường 12, Quận 10, TP. Hồ Chí Minh
  + KTX Sinh viên Lý Thường Kiệt [Mã: `HCM05B`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 268 Lý Thường Kiệt, Phường 14, Quận 10, TP. Hồ Chí Minh

#### Chủ trọ 16: Phú Mỹ Hưng Serviced Apartments & Hotel Q7
- **Tên đăng nhập:** `chutro16` HOẶC `chutro_hcm_6`
- **Tên chủ trọ:** Võ Minh Hải
- **Số điện thoại:** `0938000016`
- **Email:** `chutro.quan7@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** BIDV - `999988880016` (VO MINH HAI)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Phú Mỹ Hưng Green Suites Q7 [Mã: `HCM06A`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 55 Đường số 7, Phường Tân Phong, Quận 7, TP. Hồ Chí Minh
  + Sunrise View Hotel Nguyễn Thị Thập Q7 [Mã: `HCM06B`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 18 Nguyễn Thị Thập, Phường Tân Hưng, Quận 7, TP. Hồ Chí Minh

#### Chủ trọ 17: Phan Xích Long Smart Living & Studio Phú Nhuận
- **Tên đăng nhập:** `chutro17` HOẶC `chutro_hcm_7`
- **Tên chủ trọ:** Trần Bảo Ngọc
- **Số điện thoại:** `0938000017`
- **Email:** `chutro.phunhuan@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** Sacombank - `999988880017` (TRAN BAO NGOC)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Phan Xích Long Smart Living [Mã: `HCM07A`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 88 Phan Xích Long, Phường 2, Quận Phú Nhuận, TP. Hồ Chí Minh

#### Chủ trọ 18: Airport Transit Hotel & Nhà trọ Sân Bay Tân Bình
- **Tên đăng nhập:** `chutro18` HOẶC `chutro_hcm_8`
- **Tên chủ trọ:** Đỗ Quang Minh
- **Số điện thoại:** `0938000018`
- **Email:** `chutro.tanbinh@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** TPBank - `999988880018` (DO QUANG MINH)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Airport Transit Hotel Tân Sơn Nhất [Mã: `HCM08A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 32 Bạch Đằng, Phường 2, Quận Tân Bình, TP. Hồ Chí Minh
  + Nhà trọ Sinh viên Hoàng Hoa Thám [Mã: `HCM08B`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 154 Hoàng Hoa Thám, Phường 12, Quận Tân Bình, TP. Hồ Chí Minh

#### Chủ trọ 19: Nhà trọ & Chung cư mini Quang Trung Gò Vấp
- **Tên đăng nhập:** `chutro19` HOẶC `chutro_hcm_9`
- **Tên chủ trọ:** Nguyễn Thị Kim Ngân
- **Số điện thoại:** `0938000019`
- **Email:** `chutro.govap@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ⏳ CHỜ DUYỆT KYC (Pending hồ sơ)
- **Tài khoản ngân hàng:** MB - `999988880019` (NGUYEN THI KIM NGAN)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Quang Trung Modern House Gò Vấp [Mã: `HCM09A`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 520 Quang Trung, Phường 11, Quận Gò Vấp, TP. Hồ Chí Minh

#### Chủ trọ 20: Chợ Lớn Heritage House & Trọ Y Dược Quận 5
- **Tên đăng nhập:** `chutro20` HOẶC `chutro_hcm_10`
- **Tên chủ trọ:** Vương Quốc Hào
- **Số điện thoại:** `0938000020`
- **Email:** `chutro.quan5@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ⚠️ CHƯA XÁC MINH (Unverified)
- **Tài khoản ngân hàng:** VCB - `999988880020` (VUONG QUOC HAO)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Chợ Lớn Studio & Hotel Quận 5 [Mã: `HCM10A`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 86 Nguyễn Trãi, Phường 3, Quận 5, TP. Hồ Chí Minh

### 2.3 KHU VỰC: Đà Nẵng (Tenant 21 - 30)

#### Chủ trọ 21: Sông Hàn Riverside Hotel & Luxury Apartment Đà Nẵng
- **Tên đăng nhập:** `chutro21` HOẶC `chutro_dn_1`
- **Tên chủ trọ:** Trần Đình Nam
- **Số điện thoại:** `0905000021`
- **Email:** `chutro.dananghc@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** VCB - `999988880021` (TRAN DINH NAM)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Han River Riverside Hotel Bạch Đằng [Mã: `DN01A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 74 Bạch Đằng, Phường Hải Châu 1, Quận Hải Châu, TP. Đà Nẵng
  + Căn hộ Cao cấp Nguyễn Văn Linh [Mã: `DN01B`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 150 Nguyễn Văn Linh, Phường Nam Dương, Quận Hải Châu, TP. Đà Nẵng

#### Chủ trọ 22: Biển Xanh Mỹ Khê Beach Hotel & Resort Suites Sơn Trà
- **Tên đăng nhập:** `chutro22` HOẶC `chutro_dn_2`
- **Tên chủ trọ:** Lê Thị Thu Hương
- **Số điện thoại:** `0905000022`
- **Email:** `chutro.danangst@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** MB - `999988880022` (LE THI THU HUONG)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + My Khe Blue Ocean Hotel [Mã: `DN02A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 258 Võ Nguyên Giáp, Phường Phước Mỹ, Quận Sơn Trà, TP. Đà Nẵng
  + Biển Đông Studio Apartment [Mã: `DN02B`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 19 Dương Đình Nghệ, Phường An Hải Bắc, Quận Sơn Trà, TP. Đà Nẵng

#### Chủ trọ 23: Non Nước Luxury Boutique & Trọ Kinh Tế Ngũ Hành Sơn
- **Tên đăng nhập:** `chutro23` HOẶC `chutro_dn_3`
- **Tên chủ trọ:** Nguyễn Văn Danh
- **Số điện thoại:** `0905000023`
- **Email:** `chutro.danangnhs@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** BIDV - `999988880023` (NGUYEN VAN DANH)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Non Nước Boutique Suites Trường Sa [Mã: `DN03A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 36 Trường Sa, Phường Hòa Hải, Quận Ngũ Hành Sơn, TP. Đà Nẵng
  + Trọ Sinh viên ĐH Kinh Tế Đà Nẵng [Mã: `DN03B`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 71 Ngũ Hành Sơn, Phường Mỹ An, Quận Ngũ Hành Sơn, TP. Đà Nẵng

#### Chủ trọ 24: Vịnh Đà Nẵng Studio & Nhà trọ Thanh Khê
- **Tên đăng nhập:** `chutro24` HOẶC `chutro_dn_4`
- **Tên chủ trọ:** Mai Văn Thành
- **Số điện thoại:** `0905000024`
- **Email:** `chutro.danangtk@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** Techcombank - `999988880024` (MAI VAN THANH)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Vịnh Đà Nẵng View Home Nguyễn Tất Thành [Mã: `DN04A`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 312 Nguyễn Tất Thành, Phường Tam Thuận, Quận Thanh Khê, TP. Đà Nẵng
  + Trọ Sinh viên Điện Biên Phủ Thanh Khê [Mã: `DN04B`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 420 Điện Biên Phủ, Phường Thanh Khê Đông, Quận Thanh Khê, TP. Đà Nẵng

#### Chủ trọ 25: Ký túc xá & Phòng trọ Sinh viên Bách Khoa Liên Chiểu
- **Tên đăng nhập:** `chutro25` HOẶC `chutro_dn_5`
- **Tên chủ trọ:** Phạm Quốc Trung
- **Số điện thoại:** `0905000025`
- **Email:** `chutro.dananglc@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** VCB - `999988880025` (PHAM QUOC TRUNG)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Bách Khoa Smart Dorm Nguyễn Lương Bằng [Mã: `DN05A`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 54 Nguyễn Lương Bằng, Phường Hòa Khánh Bắc, Quận Liên Chiểu, TP. Đà Nẵng
  + Căn hộ Khu Công nghệ Cao Đà Nẵng Tôn Đức Thắng [Mã: `DN05B`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 102 Tôn Đức Thắng, Phường Hòa Minh, Quận Liên Chiểu, TP. Đà Nẵng

#### Chủ trọ 26: Cẩm Lệ Green Living & Chung cư mini Khuê Trung
- **Tên đăng nhập:** `chutro26` HOẶC `chutro_dn_6`
- **Tên chủ trọ:** Đặng Thị Lan
- **Số điện thoại:** `0905000026`
- **Email:** `chutro.danangcl@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** Agribank - `999988880026` (DANG THI LAN)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Cẩm Lệ Central Apartment Cách Mạng Tháng 8 [Mã: `DN06A`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 89 Cách Mạng Tháng 8, Phường Khuê Trung, Quận Cẩm Lệ, TP. Đà Nẵng

#### Chủ trọ 27: Bán đảo Sơn Trà Nature Homestay & Suites
- **Tên đăng nhập:** `chutro27` HOẶC `chutro_dn_7`
- **Tên chủ trọ:** Trương Quang Khải
- **Số điện thoại:** `0905000027`
- **Email:** `chutro.danangst2@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** VPBank - `999988880027` (TRUONG QUANG KHAI)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Son Tra Nature Lodge Hoàng Sa [Mã: `DN07A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 15 Hoàng Sa, Phường Thọ Quang, Quận Sơn Trà, TP. Đà Nẵng

#### Chủ trọ 28: Dragon Bridge Studio & Suites 2 Tháng 9 Hải Châu
- **Tên đăng nhập:** `chutro28` HOẶC `chutro_dn_8`
- **Tên chủ trọ:** Bùi Hồng Sơn
- **Số điện thoại:** `0905000028`
- **Email:** `chutro.dananghc2@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** MB - `999988880028` (BUI HONG SON)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Dragon Bridge Studio & Suites [Mã: `DN08A`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 210 Đường 2 Tháng 9, Phường Hòa Cường Bắc, Quận Hải Châu, TP. Đà Nẵng

#### Chủ trọ 29: Khu nhà trọ Công nhân Hòa Khánh - Hòa Vang
- **Tên đăng nhập:** `chutro29` HOẶC `chutro_dn_9`
- **Tên chủ trọ:** Nguyễn Đức Hạnh
- **Số điện thoại:** `0905000029`
- **Email:** `chutro.dananghv@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ⏳ CHỜ DUYỆT KYC (Pending hồ sơ)
- **Tài khoản ngân hàng:** Agribank - `999988880029` (NGUYEN DUC HANH)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Hòa Vang Eco Boarding House [Mã: `DN09A`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Tuyến QL14B, Xã Hòa Nhơn, Huyện Hòa Vang, TP. Đà Nẵng

#### Chủ trọ 30: Phố Tây An Thượng Hotel & Apartment Ngũ Hành Sơn
- **Tên đăng nhập:** `chutro30` HOẶC `chutro_dn_10`
- **Tên chủ trọ:** Lâm Hoài Nam
- **Số điện thoại:** `0905000030`
- **Email:** `chutro.dananganthuong@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ⚠️ CHƯA XÁC MINH (Unverified)
- **Tài khoản ngân hàng:** VCB - `999988880030` (LAM HOAI NAM)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + An Thuong Western Hotel & Suites [Mã: `DN10A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 12 An Thượng 2, Phường Mỹ An, Quận Ngũ Hành Sơn, TP. Đà Nẵng

### 2.4 KHU VỰC: Đà Lạt (Lâm Đồng) (Tenant 31 - 40)

#### Chủ trọ 31: Hồ Xuân Hương Panorama Hotel & Căn hộ Trung tâm Đà Lạt
- **Tên đăng nhập:** `chutro31` HOẶC `chutro_dl_1`
- **Tên chủ trọ:** Đoàn Minh Thông
- **Số điện thoại:** `0963000031`
- **Email:** `chutro.dalatp1@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** VCB - `999988880031` (DOAN MINH THONG)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Xuan Huong Lakeview Hotel [Mã: `DL01A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 10 Nguyễn Chí Thanh, Phường 1, TP. Đà Lạt, Tỉnh Lâm Đồng
  + Căn hộ Phố Đi Bộ Lê Thị Hồng Gấm [Mã: `DL01B`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 35 Lê Thị Hồng Gấm, Phường 1, TP. Đà Lạt, Tỉnh Lâm Đồng

#### Chủ trọ 32: Ngàn Hoa Boutique Hotel & Trọ Sinh viên Đà Lạt Phường 2
- **Tên đăng nhập:** `chutro32` HOẶC `chutro_dl_2`
- **Tên chủ trọ:** Phan Cẩm Ly
- **Số điện thoại:** `0963000032`
- **Email:** `chutro.dalatp2@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** MB - `999988880032` (PHAN CAM LY)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Ngan Hoa Boutique Hotel Bùi Thị Xuân [Mã: `DL02A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 72 Bùi Thị Xuân, Phường 2, TP. Đà Lạt, Tỉnh Lâm Đồng
  + Nhà trọ Sinh viên Phan Đình Phùng [Mã: `DL02B`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 145 Phan Đình Phùng, Phường 2, TP. Đà Lạt, Tỉnh Lâm Đồng

#### Chủ trọ 33: Đồi Robin Pine View Villa & Suites Phường 3
- **Tên đăng nhập:** `chutro33` HOẶC `chutro_dl_3`
- **Tên chủ trọ:** Lâm Thanh Tuấn
- **Số điện thoại:** `0963000033`
- **Email:** `chutro.dalatp3@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** Sacombank - `999988880033` (LAM THANH TUAN)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Robin Hill Pine Hotel Đống Đa [Mã: `DL03A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 01 Đống Đa, Phường 3, TP. Đà Lạt, Tỉnh Lâm Đồng
  + Homestay Căn hộ Rừng Thông Ba Tháng Tư [Mã: `DL03B`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 22 Đường Ba Tháng Tư, Phường 3, TP. Đà Lạt, Tỉnh Lâm Đồng

#### Chủ trọ 34: Vạn Thành Valley Homestay & Nhà trọ Phường 4
- **Tên đăng nhập:** `chutro34` HOẶC `chutro_dl_4`
- **Tên chủ trọ:** Nguyễn Kim Yến
- **Số điện thoại:** `0963000034`
- **Email:** `chutro.dalatp4@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** VietinBank - `999988880034` (NGUYEN KIM YEN)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Vạn Thành Valley Lodge Vạn Hạnh [Mã: `DL04A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 40 Vạn Hạnh, Phường 4, TP. Đà Lạt, Tỉnh Lâm Đồng
  + Nhà trọ Công nhân Vạn Thành Hoàng Văn Thụ [Mã: `DL04B`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 88 Hoàng Văn Thụ, Phường 4, TP. Đà Lạt, Tỉnh Lâm Đồng

#### Chủ trọ 35: Ký túc xá & Trọ Sinh viên Đại học Đà Lạt Phường 8
- **Tên đăng nhập:** `chutro35` HOẶC `chutro_dl_5`
- **Tên chủ trọ:** Vũ Đình Trọng
- **Số điện thoại:** `0963000035`
- **Email:** `chutro.dalatp8@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** VCB - `999988880035` (VU DINH TRONG)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + ĐH Đà Lạt Student Dorm Phù Đổng Thiên Vương [Mã: `DL05A`, Loại: Nhà trọ sinh viên, ~20 phòng] - Đ/c: Số 1 Phù Đổng Thiên Vương, Phường 8, TP. Đà Lạt, Tỉnh Lâm Đồng
  + Căn hộ Studio Thung Lũng Tình Yêu Mai Anh Đào [Mã: `DL05B`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 24 Mai Anh Đào, Phường 8, TP. Đà Lạt, Tỉnh Lâm Đồng

#### Chủ trọ 36: Ga Đà Lạt Heritage Hotel & Căn hộ Dịch vụ Phường 10
- **Tên đăng nhập:** `chutro36` HOẶC `chutro_dl_6`
- **Tên chủ trọ:** Hoàng Gia Long
- **Số điện thoại:** `0963000036`
- **Email:** `chutro.dalatp10@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** BIDV - `999988880036` (HOANG GIA LONG)
- **Quy mô:** 2 cơ sở, tổng cộng ~40 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Old Railway Heritage Hotel Quang Trung [Mã: `DL06A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 01 Quang Trung, Phường 10, TP. Đà Lạt, Tỉnh Lâm Đồng
  + Lam Vien Square Studio Yersin [Mã: `DL06B`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 18 Yersin, Phường 10, TP. Đà Lạt, Tỉnh Lâm Đồng

#### Chủ trọ 37: Trại Mát Cloud Villa & Nhà trọ Phường 11 Đà Lạt
- **Tên đăng nhập:** `chutro37` HOẶC `chutro_dl_7`
- **Tên chủ trọ:** Lê Trọng Nhân
- **Số điện thoại:** `0963000037`
- **Email:** `chutro.dalatp11@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** Agribank - `999988880037` (LE TRONG NHAN)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Trai Mat Cloud Villa & Homestay [Mã: `DL07A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 120 Tự Phước, Phường 11, TP. Đà Lạt, Tỉnh Lâm Đồng

#### Chủ trọ 38: Hùng Vương Royal Garden Villa & Căn hộ Phường 9
- **Tên đăng nhập:** `chutro38` HOẶC `chutro_dl_8`
- **Tên chủ trọ:** Đỗ Thu Hà
- **Số điện thoại:** `0963000038`
- **Email:** `chutro.dalatp9@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ✅ ĐÃ XÁC MINH KYC (Tích Xanh)
- **Tài khoản ngân hàng:** Techcombank - `999988880038` (DO THU HA)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Royal Garden Suites Da Lat Hùng Vương [Mã: `DL08A`, Loại: Chung cư mini, ~20 phòng] - Đ/c: Số 42 Hùng Vương, Phường 9, TP. Đà Lạt, Tỉnh Lâm Đồng

#### Chủ trọ 39: Thái Phiên Thung Lũng Ánh Sáng Homestay Phường 12
- **Tên đăng nhập:** `chutro39` HOẶC `chutro_dl_9`
- **Tên chủ trọ:** Bùi Vĩnh Phát
- **Số điện thoại:** `0963000039`
- **Email:** `chutro.dalatp12@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ⏳ CHỜ DUYỆT KYC (Pending hồ sơ)
- **Tài khoản ngân hàng:** MB - `999988880039` (BUI VINH PHAT)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Light Valley Farmstay & Suites Ngô Gia Tự [Mã: `DL09A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Số 55 Ngô Gia Tự, Phường 12, TP. Đà Lạt, Tỉnh Lâm Đồng

#### Chủ trọ 40: Tuyền Lâm Sunset Eco Resort & Lakeview Villa
- **Tên đăng nhập:** `chutro40` HOẶC `chutro_dl_10`
- **Tên chủ trọ:** Nguyễn Thảo Vy
- **Số điện thoại:** `0963000040`
- **Email:** `chutro.dalattuyenlam@smartroom.local`
- **Mật khẩu:** `123456`
- **Trạng thái KYC:** ⚠️ CHƯA XÁC MINH (Unverified)
- **Tài khoản ngân hàng:** VCB - `999988880040` (NGUYEN THAO VY)
- **Quy mô:** 1 cơ sở, tổng cộng ~20 phòng trọ
- **Danh sách cơ sở/tòa nhà:**
  + Tuyen Lam Sunset Eco Resort [Mã: `DL10A`, Loại: Khách sạn, ~20 phòng] - Đ/c: Khu du lịch Quốc gia Hồ Tuyền Lâm, Phường 3, TP. Đà Lạt, Tỉnh Lâm Đồng

---

## 3. TÀI KHOẢN NHÂN VIÊN QUẢN LÝ (MANAGER) - [40 TÀI KHOẢN]

* **Quyền hạn (Role `manager`):** Vận hành cơ sở lưu trú, quản lý sơ đồ phòng, theo dõi hợp đồng thuê, quản lý chỉ số điện nước IoT, lập hóa đơn thanh toán và xử lý sự cố báo hỏng.
* **Mật khẩu chung:** `123456`
* **Quy tắc Username:** `quanly1` đến `quanly40` tương ứng trực tiếp với Tenant 1 đến Tenant 40.

| Tenant ID | Khu vực | Username | Email | Số điện thoại | Cơ sở phụ trách |
| :---: | :--- | :--- | :--- | :--- | :--- |
| 1 | Hà Nội | `quanly1` | quanly1@smartroom.local | `0970000001` | Hệ thống Chung cư mini SmartRoom Cầu Giấy |
| 2 | Hà Nội | `quanly2` | quanly2@smartroom.local | `0970000002` | Chuỗi Trọ & Chung cư mini Rentry Home Thanh Xuân |
| 3 | Hà Nội | `quanly3` | quanly3@smartroom.local | `0970000003` | Căn hộ Dịch vụ Sen Vàng Đống Đa |
| 4 | Hà Nội | `quanly4` | quanly4@smartroom.local | `0970000004` | Hồ Tây Panorama Hotel & Luxury Suites |
| 5 | Hà Nội | `quanly5` | quanly5@smartroom.local | `0970000005` | Hệ thống Phòng trọ & Studio Times Light Hai Bà Trưng |
| 6 | Hà Nội | `quanly6` | quanly6@smartroom.local | `0970000006` | Khách sạn Mini & Căn hộ Liễu Giai Riverside Ba Đình |
| 7 | Hà Nội | `quanly7` | quanly7@smartroom.local | `0970000007` | Tổ hợp Căn hộ & KTX Mỹ Đình Star Nam Từ Liêm |
| 8 | Hà Nội | `quanly8` | quanly8@smartroom.local | `0970000008` | Chung cư mini & Khách sạn Eco Linh Đàm Hoàng Mai |
| 9 | Hà Nội | `quanly9` | quanly9@smartroom.local | `0970000009` | Cổ Nhuế Green House & KTX Học Viện Bắc Từ Liêm |
| 10 | Hà Nội | `quanly10` | quanly10@smartroom.local | `0970000010` | Tòa nhà Căn hộ Dịch vụ Hà Đông Central |
| 11 | Hồ Chí Minh | `quanly11` | quanly11@smartroom.local | `0970000011` | Saigon Central Boutique Hotel & Studio Q1 |
| 12 | Hồ Chí Minh | `quanly12` | quanly12@smartroom.local | `0970000012` | Hệ thống Căn hộ & KTX Sinh viên Renty Bình Thạnh |
| 13 | Hồ Chí Minh | `quanly13` | quanly13@smartroom.local | `0970000013` | Làng Đại học Mega Dorm & Căn hộ Công nghệ cao Thủ Đức |
| 14 | Hồ Chí Minh | `quanly14` | quanly14@smartroom.local | `0970000014` | The Garden Suites Hotel & Studio Quận 3 |
| 15 | Hồ Chí Minh | `quanly15` | quanly15@smartroom.local | `0970000015` | Căn hộ Bách Khoa & Trọ Sinh viên Thành Thái Q10 |
| 16 | Hồ Chí Minh | `quanly16` | quanly16@smartroom.local | `0970000016` | Phú Mỹ Hưng Serviced Apartments & Hotel Q7 |
| 17 | Hồ Chí Minh | `quanly17` | quanly17@smartroom.local | `0970000017` | Phan Xích Long Smart Living & Studio Phú Nhuận |
| 18 | Hồ Chí Minh | `quanly18` | quanly18@smartroom.local | `0970000018` | Airport Transit Hotel & Nhà trọ Sân Bay Tân Bình |
| 19 | Hồ Chí Minh | `quanly19` | quanly19@smartroom.local | `0970000019` | Nhà trọ & Chung cư mini Quang Trung Gò Vấp |
| 20 | Hồ Chí Minh | `quanly20` | quanly20@smartroom.local | `0970000020` | Chợ Lớn Heritage House & Trọ Y Dược Quận 5 |
| 21 | Đà Nẵng | `quanly21` | quanly21@smartroom.local | `0970000021` | Sông Hàn Riverside Hotel & Luxury Apartment Đà Nẵng |
| 22 | Đà Nẵng | `quanly22` | quanly22@smartroom.local | `0970000022` | Biển Xanh Mỹ Khê Beach Hotel & Resort Suites Sơn Trà |
| 23 | Đà Nẵng | `quanly23` | quanly23@smartroom.local | `0970000023` | Non Nước Luxury Boutique & Trọ Kinh Tế Ngũ Hành Sơn |
| 24 | Đà Nẵng | `quanly24` | quanly24@smartroom.local | `0970000024` | Vịnh Đà Nẵng Studio & Nhà trọ Thanh Khê |
| 25 | Đà Nẵng | `quanly25` | quanly25@smartroom.local | `0970000025` | Ký túc xá & Phòng trọ Sinh viên Bách Khoa Liên Chiểu |
| 26 | Đà Nẵng | `quanly26` | quanly26@smartroom.local | `0970000026` | Cẩm Lệ Green Living & Chung cư mini Khuê Trung |
| 27 | Đà Nẵng | `quanly27` | quanly27@smartroom.local | `0970000027` | Bán đảo Sơn Trà Nature Homestay & Suites |
| 28 | Đà Nẵng | `quanly28` | quanly28@smartroom.local | `0970000028` | Dragon Bridge Studio & Suites 2 Tháng 9 Hải Châu |
| 29 | Đà Nẵng | `quanly29` | quanly29@smartroom.local | `0970000029` | Khu nhà trọ Công nhân Hòa Khánh - Hòa Vang |
| 30 | Đà Nẵng | `quanly30` | quanly30@smartroom.local | `0970000030` | Phố Tây An Thượng Hotel & Apartment Ngũ Hành Sơn |
| 31 | Đà Lạt | `quanly31` | quanly31@smartroom.local | `0970000031` | Hồ Xuân Hương Panorama Hotel & Căn hộ Trung tâm Đà Lạt |
| 32 | Đà Lạt | `quanly32` | quanly32@smartroom.local | `0970000032` | Ngàn Hoa Boutique Hotel & Trọ Sinh viên Đà Lạt Phường 2 |
| 33 | Đà Lạt | `quanly33` | quanly33@smartroom.local | `0970000033` | Đồi Robin Pine View Villa & Suites Phường 3 |
| 34 | Đà Lạt | `quanly34` | quanly34@smartroom.local | `0970000034` | Vạn Thành Valley Homestay & Nhà trọ Phường 4 |
| 35 | Đà Lạt | `quanly35` | quanly35@smartroom.local | `0970000035` | Ký túc xá & Trọ Sinh viên Đại học Đà Lạt Phường 8 |
| 36 | Đà Lạt | `quanly36` | quanly36@smartroom.local | `0970000036` | Ga Đà Lạt Heritage Hotel & Căn hộ Dịch vụ Phường 10 |
| 37 | Đà Lạt | `quanly37` | quanly37@smartroom.local | `0970000037` | Trại Mát Cloud Villa & Nhà trọ Phường 11 Đà Lạt |
| 38 | Đà Lạt | `quanly38` | quanly38@smartroom.local | `0970000038` | Hùng Vương Royal Garden Villa & Căn hộ Phường 9 |
| 39 | Đà Lạt | `quanly39` | quanly39@smartroom.local | `0970000039` | Thái Phiên Thung Lũng Ánh Sáng Homestay Phường 12 |
| 40 | Đà Lạt | `quanly40` | quanly40@smartroom.local | `0970000040` | Tuyền Lâm Sunset Eco Resort & Lakeview Villa |

---

## 4. TÀI KHOẢN NHÂN VIÊN LỄ TÂN (RECEPTIONIST) - [40 TÀI KHOẢN]

* **Quyền hạn (Role `receptionist`):** Check-in đón khách, Check-out trả phòng, ghi nhận dịch vụ phát sinh (Folio), quản lý chìa khóa/thẻ từ, hỗ trợ khách lưu trú hàng ngày.
* **Mật khẩu chung:** `123456`
* **Quy tắc Username:** `letan1` đến `letan40` tương ứng trực tiếp với Tenant 1 đến Tenant 40.

| Tenant ID | Khu vực | Username | Email | Số điện thoại | Cơ sở phụ trách |
| :---: | :--- | :--- | :--- | :--- | :--- |
| 1 | Hà Nội | `letan1` | letan1@smartroom.local | `0960000001` | Hệ thống Chung cư mini SmartRoom Cầu Giấy |
| 2 | Hà Nội | `letan2` | letan2@smartroom.local | `0960000002` | Chuỗi Trọ & Chung cư mini Rentry Home Thanh Xuân |
| 3 | Hà Nội | `letan3` | letan3@smartroom.local | `0960000003` | Căn hộ Dịch vụ Sen Vàng Đống Đa |
| 4 | Hà Nội | `letan4` | letan4@smartroom.local | `0960000004` | Hồ Tây Panorama Hotel & Luxury Suites |
| 5 | Hà Nội | `letan5` | letan5@smartroom.local | `0960000005` | Hệ thống Phòng trọ & Studio Times Light Hai Bà Trưng |
| 6 | Hà Nội | `letan6` | letan6@smartroom.local | `0960000006` | Khách sạn Mini & Căn hộ Liễu Giai Riverside Ba Đình |
| 7 | Hà Nội | `letan7` | letan7@smartroom.local | `0960000007` | Tổ hợp Căn hộ & KTX Mỹ Đình Star Nam Từ Liêm |
| 8 | Hà Nội | `letan8` | letan8@smartroom.local | `0960000008` | Chung cư mini & Khách sạn Eco Linh Đàm Hoàng Mai |
| 9 | Hà Nội | `letan9` | letan9@smartroom.local | `0960000009` | Cổ Nhuế Green House & KTX Học Viện Bắc Từ Liêm |
| 10 | Hà Nội | `letan10` | letan10@smartroom.local | `0960000010` | Tòa nhà Căn hộ Dịch vụ Hà Đông Central |
| 11 | Hồ Chí Minh | `letan11` | letan11@smartroom.local | `0960000011` | Saigon Central Boutique Hotel & Studio Q1 |
| 12 | Hồ Chí Minh | `letan12` | letan12@smartroom.local | `0960000012` | Hệ thống Căn hộ & KTX Sinh viên Renty Bình Thạnh |
| 13 | Hồ Chí Minh | `letan13` | letan13@smartroom.local | `0960000013` | Làng Đại học Mega Dorm & Căn hộ Công nghệ cao Thủ Đức |
| 14 | Hồ Chí Minh | `letan14` | letan14@smartroom.local | `0960000014` | The Garden Suites Hotel & Studio Quận 3 |
| 15 | Hồ Chí Minh | `letan15` | letan15@smartroom.local | `0960000015` | Căn hộ Bách Khoa & Trọ Sinh viên Thành Thái Q10 |
| 16 | Hồ Chí Minh | `letan16` | letan16@smartroom.local | `0960000016` | Phú Mỹ Hưng Serviced Apartments & Hotel Q7 |
| 17 | Hồ Chí Minh | `letan17` | letan17@smartroom.local | `0960000017` | Phan Xích Long Smart Living & Studio Phú Nhuận |
| 18 | Hồ Chí Minh | `letan18` | letan18@smartroom.local | `0960000018` | Airport Transit Hotel & Nhà trọ Sân Bay Tân Bình |
| 19 | Hồ Chí Minh | `letan19` | letan19@smartroom.local | `0960000019` | Nhà trọ & Chung cư mini Quang Trung Gò Vấp |
| 20 | Hồ Chí Minh | `letan20` | letan20@smartroom.local | `0960000020` | Chợ Lớn Heritage House & Trọ Y Dược Quận 5 |
| 21 | Đà Nẵng | `letan21` | letan21@smartroom.local | `0960000021` | Sông Hàn Riverside Hotel & Luxury Apartment Đà Nẵng |
| 22 | Đà Nẵng | `letan22` | letan22@smartroom.local | `0960000022` | Biển Xanh Mỹ Khê Beach Hotel & Resort Suites Sơn Trà |
| 23 | Đà Nẵng | `letan23` | letan23@smartroom.local | `0960000023` | Non Nước Luxury Boutique & Trọ Kinh Tế Ngũ Hành Sơn |
| 24 | Đà Nẵng | `letan24` | letan24@smartroom.local | `0960000024` | Vịnh Đà Nẵng Studio & Nhà trọ Thanh Khê |
| 25 | Đà Nẵng | `letan25` | letan25@smartroom.local | `0960000025` | Ký túc xá & Phòng trọ Sinh viên Bách Khoa Liên Chiểu |
| 26 | Đà Nẵng | `letan26` | letan26@smartroom.local | `0960000026` | Cẩm Lệ Green Living & Chung cư mini Khuê Trung |
| 27 | Đà Nẵng | `letan27` | letan27@smartroom.local | `0960000027` | Bán đảo Sơn Trà Nature Homestay & Suites |
| 28 | Đà Nẵng | `letan28` | letan28@smartroom.local | `0960000028` | Dragon Bridge Studio & Suites 2 Tháng 9 Hải Châu |
| 29 | Đà Nẵng | `letan29` | letan29@smartroom.local | `0960000029` | Khu nhà trọ Công nhân Hòa Khánh - Hòa Vang |
| 30 | Đà Nẵng | `letan30` | letan30@smartroom.local | `0960000030` | Phố Tây An Thượng Hotel & Apartment Ngũ Hành Sơn |
| 31 | Đà Lạt | `letan31` | letan31@smartroom.local | `0960000031` | Hồ Xuân Hương Panorama Hotel & Căn hộ Trung tâm Đà Lạt |
| 32 | Đà Lạt | `letan32` | letan32@smartroom.local | `0960000032` | Ngàn Hoa Boutique Hotel & Trọ Sinh viên Đà Lạt Phường 2 |
| 33 | Đà Lạt | `letan33` | letan33@smartroom.local | `0960000033` | Đồi Robin Pine View Villa & Suites Phường 3 |
| 34 | Đà Lạt | `letan34` | letan34@smartroom.local | `0960000034` | Vạn Thành Valley Homestay & Nhà trọ Phường 4 |
| 35 | Đà Lạt | `letan35` | letan35@smartroom.local | `0960000035` | Ký túc xá & Trọ Sinh viên Đại học Đà Lạt Phường 8 |
| 36 | Đà Lạt | `letan36` | letan36@smartroom.local | `0960000036` | Ga Đà Lạt Heritage Hotel & Căn hộ Dịch vụ Phường 10 |
| 37 | Đà Lạt | `letan37` | letan37@smartroom.local | `0960000037` | Trại Mát Cloud Villa & Nhà trọ Phường 11 Đà Lạt |
| 38 | Đà Lạt | `letan38` | letan38@smartroom.local | `0960000038` | Hùng Vương Royal Garden Villa & Căn hộ Phường 9 |
| 39 | Đà Lạt | `letan39` | letan39@smartroom.local | `0960000039` | Thái Phiên Thung Lũng Ánh Sáng Homestay Phường 12 |
| 40 | Đà Lạt | `letan40` | letan40@smartroom.local | `0960000040` | Tuyền Lâm Sunset Eco Resort & Lakeview Villa |

---

## 5. TÀI KHOẢN NHÂN VIÊN BUỒNG PHÒNG (HOUSEKEEPER) - [40 TÀI KHOẢN]

* **Quyền hạn (Role `housekeeper`):** Giao diện mobile chuyên dụng dọn phòng: Cập nhật trạng thái buồng phòng thời gian thực (Phòng sạch, Phòng bẩn, Đang dọn, Sẵn sàng đón khách), ghi chú vật tư tiêu hao.
* **Mật khẩu chung:** `123456`
* **Quy tắc Username:** `buongphong1` đến `buongphong40` tương ứng trực tiếp với Tenant 1 đến Tenant 40.

| Tenant ID | Khu vực | Username | Email | Số điện thoại | Cơ sở phụ trách |
| :---: | :--- | :--- | :--- | :--- | :--- |
| 1 | Hà Nội | `buongphong1` | buongphong1@smartroom.local | `0950000001` | Hệ thống Chung cư mini SmartRoom Cầu Giấy |
| 2 | Hà Nội | `buongphong2` | buongphong2@smartroom.local | `0950000002` | Chuỗi Trọ & Chung cư mini Rentry Home Thanh Xuân |
| 3 | Hà Nội | `buongphong3` | buongphong3@smartroom.local | `0950000003` | Căn hộ Dịch vụ Sen Vàng Đống Đa |
| 4 | Hà Nội | `buongphong4` | buongphong4@smartroom.local | `0950000004` | Hồ Tây Panorama Hotel & Luxury Suites |
| 5 | Hà Nội | `buongphong5` | buongphong5@smartroom.local | `0950000005` | Hệ thống Phòng trọ & Studio Times Light Hai Bà Trưng |
| 6 | Hà Nội | `buongphong6` | buongphong6@smartroom.local | `0950000006` | Khách sạn Mini & Căn hộ Liễu Giai Riverside Ba Đình |
| 7 | Hà Nội | `buongphong7` | buongphong7@smartroom.local | `0950000007` | Tổ hợp Căn hộ & KTX Mỹ Đình Star Nam Từ Liêm |
| 8 | Hà Nội | `buongphong8` | buongphong8@smartroom.local | `0950000008` | Chung cư mini & Khách sạn Eco Linh Đàm Hoàng Mai |
| 9 | Hà Nội | `buongphong9` | buongphong9@smartroom.local | `0950000009` | Cổ Nhuế Green House & KTX Học Viện Bắc Từ Liêm |
| 10 | Hà Nội | `buongphong10` | buongphong10@smartroom.local | `0950000010` | Tòa nhà Căn hộ Dịch vụ Hà Đông Central |
| 11 | Hồ Chí Minh | `buongphong11` | buongphong11@smartroom.local | `0950000011` | Saigon Central Boutique Hotel & Studio Q1 |
| 12 | Hồ Chí Minh | `buongphong12` | buongphong12@smartroom.local | `0950000012` | Hệ thống Căn hộ & KTX Sinh viên Renty Bình Thạnh |
| 13 | Hồ Chí Minh | `buongphong13` | buongphong13@smartroom.local | `0950000013` | Làng Đại học Mega Dorm & Căn hộ Công nghệ cao Thủ Đức |
| 14 | Hồ Chí Minh | `buongphong14` | buongphong14@smartroom.local | `0950000014` | The Garden Suites Hotel & Studio Quận 3 |
| 15 | Hồ Chí Minh | `buongphong15` | buongphong15@smartroom.local | `0950000015` | Căn hộ Bách Khoa & Trọ Sinh viên Thành Thái Q10 |
| 16 | Hồ Chí Minh | `buongphong16` | buongphong16@smartroom.local | `0950000016` | Phú Mỹ Hưng Serviced Apartments & Hotel Q7 |
| 17 | Hồ Chí Minh | `buongphong17` | buongphong17@smartroom.local | `0950000017` | Phan Xích Long Smart Living & Studio Phú Nhuận |
| 18 | Hồ Chí Minh | `buongphong18` | buongphong18@smartroom.local | `0950000018` | Airport Transit Hotel & Nhà trọ Sân Bay Tân Bình |
| 19 | Hồ Chí Minh | `buongphong19` | buongphong19@smartroom.local | `0950000019` | Nhà trọ & Chung cư mini Quang Trung Gò Vấp |
| 20 | Hồ Chí Minh | `buongphong20` | buongphong20@smartroom.local | `0950000020` | Chợ Lớn Heritage House & Trọ Y Dược Quận 5 |
| 21 | Đà Nẵng | `buongphong21` | buongphong21@smartroom.local | `0950000021` | Sông Hàn Riverside Hotel & Luxury Apartment Đà Nẵng |
| 22 | Đà Nẵng | `buongphong22` | buongphong22@smartroom.local | `0950000022` | Biển Xanh Mỹ Khê Beach Hotel & Resort Suites Sơn Trà |
| 23 | Đà Nẵng | `buongphong23` | buongphong23@smartroom.local | `0950000023` | Non Nước Luxury Boutique & Trọ Kinh Tế Ngũ Hành Sơn |
| 24 | Đà Nẵng | `buongphong24` | buongphong24@smartroom.local | `0950000024` | Vịnh Đà Nẵng Studio & Nhà trọ Thanh Khê |
| 25 | Đà Nẵng | `buongphong25` | buongphong25@smartroom.local | `0950000025` | Ký túc xá & Phòng trọ Sinh viên Bách Khoa Liên Chiểu |
| 26 | Đà Nẵng | `buongphong26` | buongphong26@smartroom.local | `0950000026` | Cẩm Lệ Green Living & Chung cư mini Khuê Trung |
| 27 | Đà Nẵng | `buongphong27` | buongphong27@smartroom.local | `0950000027` | Bán đảo Sơn Trà Nature Homestay & Suites |
| 28 | Đà Nẵng | `buongphong28` | buongphong28@smartroom.local | `0950000028` | Dragon Bridge Studio & Suites 2 Tháng 9 Hải Châu |
| 29 | Đà Nẵng | `buongphong29` | buongphong29@smartroom.local | `0950000029` | Khu nhà trọ Công nhân Hòa Khánh - Hòa Vang |
| 30 | Đà Nẵng | `buongphong30` | buongphong30@smartroom.local | `0950000030` | Phố Tây An Thượng Hotel & Apartment Ngũ Hành Sơn |
| 31 | Đà Lạt | `buongphong31` | buongphong31@smartroom.local | `0950000031` | Hồ Xuân Hương Panorama Hotel & Căn hộ Trung tâm Đà Lạt |
| 32 | Đà Lạt | `buongphong32` | buongphong32@smartroom.local | `0950000032` | Ngàn Hoa Boutique Hotel & Trọ Sinh viên Đà Lạt Phường 2 |
| 33 | Đà Lạt | `buongphong33` | buongphong33@smartroom.local | `0950000033` | Đồi Robin Pine View Villa & Suites Phường 3 |
| 34 | Đà Lạt | `buongphong34` | buongphong34@smartroom.local | `0950000034` | Vạn Thành Valley Homestay & Nhà trọ Phường 4 |
| 35 | Đà Lạt | `buongphong35` | buongphong35@smartroom.local | `0950000035` | Ký túc xá & Trọ Sinh viên Đại học Đà Lạt Phường 8 |
| 36 | Đà Lạt | `buongphong36` | buongphong36@smartroom.local | `0950000036` | Ga Đà Lạt Heritage Hotel & Căn hộ Dịch vụ Phường 10 |
| 37 | Đà Lạt | `buongphong37` | buongphong37@smartroom.local | `0950000037` | Trại Mát Cloud Villa & Nhà trọ Phường 11 Đà Lạt |
| 38 | Đà Lạt | `buongphong38` | buongphong38@smartroom.local | `0950000038` | Hùng Vương Royal Garden Villa & Căn hộ Phường 9 |
| 39 | Đà Lạt | `buongphong39` | buongphong39@smartroom.local | `0950000039` | Thái Phiên Thung Lũng Ánh Sáng Homestay Phường 12 |
| 40 | Đà Lạt | `buongphong40` | buongphong40@smartroom.local | `0950000040` | Tuyền Lâm Sunset Eco Resort & Lakeview Villa |

---

## 6. TÀI KHOẢN KHÁCH TÌM PHÒNG TỰ DO (GUEST) - [14 TÀI KHOẢN]

* **Quyền hạn (Role `guest`):** Người dùng tự do khám phá sàn Renty: Tìm kiếm phòng thông minh trên Google Maps 60 FPS, lọc theo bán kính trường đại học, so sánh phòng, trò chuyện với trợ lý AI, gửi form hẹn xem phòng trực tiếp.
* **Mật khẩu chung:** `123456`

### 6.1 Tài khoản Khách đại diện 4 Thành phố lớn:
| STT | Username | Tên hiển thị | Số điện thoại | Email | Khu vực đại diện |
| :---: | :--- | :--- | :--- | :--- | :--- |
| 1 | `khachhanoi` | Khách Tìm Phòng Hà Nội | 0931000001 | khach.hn@smartroom.local | 🏛️ Hà Nội (khám phá Cầu Giấy, Đống Đa...) |
| 2 | `khachhcm` | Khách Tìm Phòng TP.HCM | 0931000002 | khach.hcm@smartroom.local | 🏙️ TP.HCM (khám phá Thủ Đức, Bình Thạnh, Q.1...) |
| 3 | `khachdanang` | Khách Tìm Phòng Đà Nẵng | 0931000003 | khach.dn@smartroom.local | 🌊 Đà Nẵng (khám phá Hải Châu, Sơn Trà...) |
| 4 | `khachdalat` | Khách Tìm Phòng Đà Lạt | 0931000004 | khach.dl@smartroom.local | 🌲 Đà Lạt (khám phá Hồ Xuân Hương, Phường 1-12...) |

### 6.2 Tài khoản Khách vãng lai demo (10 tài khoản):
| STT | Username | Tên hiển thị | Số điện thoại | Email | Mật khẩu |
| :---: | :--- | :--- | :--- | :--- | :--- |
| 1 | `khach1` | Khách Vãng Lai 1 | 0938000001 | khach1@smartroom.local | `123456` |
| 2 | `khach2` | Khách Vãng Lai 2 | 0938000002 | khach2@smartroom.local | `123456` |
| 3 | `khach3` | Khách Vãng Lai 3 | 0938000003 | khach3@smartroom.local | `123456` |
| 4 | `khach4` | Khách Vãng Lai 4 | 0938000004 | khach4@smartroom.local | `123456` |
| 5 | `khach5` | Khách Vãng Lai 5 | 0938000005 | khach5@smartroom.local | `123456` |
| 6 | `khach6` | Khách Vãng Lai 6 | 0938000006 | khach6@smartroom.local | `123456` |
| 7 | `khach7` | Khách Vãng Lai 7 | 0938000007 | khach7@smartroom.local | `123456` |
| 8 | `khach8` | Khách Vãng Lai 8 | 0938000008 | khach8@smartroom.local | `123456` |
| 9 | `khach9` | Khách Vãng Lai 9 | 0938000009 | khach9@smartroom.local | `123456` |
| 10 | `khach10` | Khách Vãng Lai 10 | 0938000010 | khach10@smartroom.local | `123456` |

---

## 7. TÀI KHOẢN CƯ DÂN THUÊ PHÒNG (RESIDENT) & DEMO CHUNG

* **Quyền hạn (Role `resident` / `user`):** Đăng nhập Resident Portal (`/smartroom/resident`): Xem chi tiết phòng đang thuê, tra cứu hợp đồng điện tử, theo dõi chỉ số công tơ điện nước IoT thời gian thực, quét mã VietQR tự động thanh toán tiền nhà, gửi phiếu báo hỏng/khiếu nại trực tuyến.
* **Mật khẩu chung:** `123456` (hoặc `password` cho tài khoản demo chung)

### 7.1 Tài khoản Cư dân Demo Chung:
- **Tên đăng nhập:** `tenant`
- **Tên hiển thị:** Khách thuê demo chung
- **Số điện thoại:** `0940000000`
- **Email:** `tenant@smartroom.local`
- **Mật khẩu:** `password` (hoặc `123456`)

### 7.2 Quy ước Tài khoản Cư dân theo từng Phòng thuê:
Trong hệ thống seeder, các phòng có trạng thái đã thuê đều được liên kết tự động với tài khoản cư dân tương ứng:
- **Quy tắc Username:** `cudan_{room_id}` (Ví dụ: `cudan_101`, `cudan_102`, `cudan_201`...)
- **Quy tắc Email:** `cudan_{room_id}@smartroom.local`
- **Quy tắc SĐT:** `094` + 7 chữ số mã phòng (Ví dụ: `0940000101`, `0940000102`...)
- **Mật khẩu:** `123456`

#### Một số tài khoản cư dân tiêu biểu theo 4 khu vực để test ngay:
- **Hà Nội (SmartRoom Cầu Giấy - Tenant 1):**
  + `cudan_101` | SĐT: `0940000101` | Email: `cudan_101@smartroom.local` | Phòng 101
  + `cudan_102` | SĐT: `0940000102` | Email: `cudan_102@smartroom.local` | Phòng 102
  + `cudan_103` | SĐT: `0940000103` | Email: `cudan_103@smartroom.local` | Phòng 103
- **TP. Hồ Chí Minh (Saigon Central Hotel Q1 - Tenant 11):**
  + `cudan_1101` | SĐT: `0940001101` | Email: `cudan_1101@smartroom.local` | Phòng 1101
  + `cudan_1102` | SĐT: `0940001102` | Email: `cudan_1102@smartroom.local` | Phòng 1102
- **Đà Nẵng (Han River Hotel Hải Châu - Tenant 21):**
  + `cudan_2101` | SĐT: `0940002101` | Email: `cudan_2101@smartroom.local` | Phòng 2101
  + `cudan_2102` | SĐT: `0940002102` | Email: `cudan_2102@smartroom.local` | Phòng 2102
- **Đà Lạt (Xuan Huong Lakeview Hotel - Tenant 31):**
  + `cudan_3101` | SĐT: `0940003101` | Email: `cudan_3101@smartroom.local` | Phòng 3101
  + `cudan_3102` | SĐT: `0940003102` | Email: `cudan_3102@smartroom.local` | Phòng 3102

---

## 8. TỔNG HỢP QUY MÔ DỮ LIỆU SEEDER HỆ THỐNG MỚI

| Chỉ số hệ thống | Số lượng | Chi tiết phân bổ |
| :--- | :---: | :--- |
| **Thị trường trọng điểm** | **4** | Hà Nội, TP. Hồ Chí Minh, Đà Nẵng, Đà Lạt |
| **Tổng số Chủ trọ (Tenants)** | **40** | Mỗi tỉnh/thành phố 10 chủ trọ (32 KYC Verified, 4 Pending, 4 Unverified) |
| **Tổng số Cơ sở / Tòa nhà** | **62** | 15 Hà Nội, 16 TP.HCM, 15 Đà Nẵng, 16 Đà Lạt (Trọ, Chung cư, Khách sạn) |
| **Tổng số Phòng trọ** | **1.240** | 20 phòng / Tòa nhà (khoảng 60% trống, 40% đang thuê) |
| **Thiết bị IoT đo đếm thông minh** | **2.480** | 1.240 công tơ điện tử + 1.240 đồng hồ nước thông minh |
| **Tài khoản Admin / Superadmin** | **4** | `admin1`, `admin2`, `superadmin`, `admin` |
| **Tài khoản Chủ trọ (Landlord)** | **40** (80 username) | `chutro1..40` và bí danh vùng miền `chutro_hn_x`, `chutro_hcm_x`, `chutro_dn_x`, `chutro_dl_x` |
| **Tài khoản Quản lý (Manager)** | **40** | `quanly1` đến `quanly40` |
| **Tài khoản Lễ tân (Receptionist)**| **40** | `letan1` đến `letan40` |
| **Tài khoản Buồng phòng (Housekeeper)**| **40** | `buongphong1` đến `buongphong40` |
| **Tài khoản Khách tìm phòng (Guest)** | **14** | 4 khách thành phố + 10 khách vãng lai demo |
| **Tài khoản Cư dân (Resident)** | **800+** | Đầy đủ hợp đồng thuê, công tơ IoT, hóa đơn VietQR |

================================================================================
TẤT CẢ TÀI KHOẢN ĐÃ SẴN SÀNG KIỂM THỬ - MẬT KHẨU: 123456
ĐĂNG NHẬP TRỰC TIẾP TẠI: http://localhost:8000/login
================================================================================