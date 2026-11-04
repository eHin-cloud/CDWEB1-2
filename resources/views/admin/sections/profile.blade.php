            <!-- SECTION PROFILE: HỒ SƠ & XÁC MINH -->
            <section id="profile-section" class="tab-content space-y-8 animate-fade-in hidden">
                <div class="glass-card rounded-3xl p-8 border border-slate-800">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 border-b border-slate-800/80 pb-4">
                        <div>
                            <h2 class="text-xl font-bold text-slate-100 flex items-center gap-2">
                                <i class="fa-solid fa-address-card text-indigo-400"></i> Quản Lý Hồ Sơ &amp; Thông Tin Chủ Trọ
                            </h2>
                            <p class="text-xs text-slate-400 mt-1">Cập nhật thông tin định danh, liên hệ và tài khoản ngân hàng nhận tiền</p>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs font-bold self-start">
                            <i class="fa-solid fa-user-check mr-1"></i> Bảng Dữ Liệu landlord_profiles
                        </span>
                    </div>

                    <!-- FORM CẬP NHẬT HỒ SƠ CHỦ TRỌ (FEAT_02_PROFILE) -->
                    <form id="profile-form" method="POST" action="{{ route('smartroom.admin.profile.update', [], false) }}" enctype="multipart/form-data" class="mb-10 rounded-2xl border border-slate-800/80 bg-slate-950/50 p-6 space-y-6">
                        @csrf

                        @if ($errors->any())
                            <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-start gap-2.5">
                                <i class="fa-solid fa-triangle-exclamation text-rose-400 text-sm mt-0.5"></i>
                                <div>
                                    <div class="font-bold text-rose-400 mb-1">Cập nhật hồ sơ không thành công! Vui lòng kiểm tra lại:</div>
                                    <ul class="list-disc list-inside space-y-0.5 text-slate-300">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <!-- STT 1: Avatar Upload (96px circle) -->
                        <div class="flex items-center gap-5 border-b border-slate-800/60 pb-5">
                            <div class="relative w-24 h-24 rounded-full border-2 border-indigo-500/40 bg-slate-900 flex items-center justify-center overflow-hidden shadow-lg shadow-indigo-500/10 group cursor-pointer" onclick="document.getElementById('avatar-input').click()">
                                <img id="avatar-preview" src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=4f46e5&color=fff' }}" alt="Avatar" class="w-full h-full object-cover group-hover:opacity-75 transition-all" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=4f46e5&color=fff';">
                                <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all text-white text-[10px] font-bold">
                                    <i class="fa-solid fa-camera text-base mb-1"></i>
                                    <span>Đổi ảnh</span>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                                    <i class="fa-solid fa-camera text-indigo-400"></i> Ảnh Đại Diện Chủ Cơ Sở
                                </h4>
                                <p class="text-[11px] text-slate-400 mt-1 mb-2">Định dạng PNG, JPG hoặc WebP (tối đa 2MB)</p>
                                <input type="file" name="avatar" id="avatar-input" accept="image/png,image/jpeg,image/webp" class="hidden" onchange="previewLandlordAvatar(this)">
                                <button type="button" onclick="document.getElementById('avatar-input').click()" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 flex items-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-arrow-up-from-bracket text-[10px]"></i>
                                    <span>Chọn ảnh từ máy tính</span>
                                </button>
                                <span id="err-avatar" class="text-[11px] text-rose-400 mt-1 @error('avatar') block @else hidden @enderror">@error('avatar') {{ $message }} @enderror</span>
                            </div>
                        </div>

                        <!-- 1. Thông tin cơ bản & Tài khoản -->
                        <div class="border-b border-slate-800/60 pb-3 flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-400 flex items-center gap-2">
                                <i class="fa-solid fa-user-gear"></i> Thông tin cơ bản &amp; Đăng nhập
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1.5 flex items-center justify-between">
                                    <span>Tài khoản đăng nhập</span>
                                    <span class="text-[10px] text-slate-500 font-semibold flex items-center gap-1 normal-case" title="Tài khoản đăng nhập là định danh cố định">
                                        <i class="fa-solid fa-lock text-[9px] text-amber-500/80"></i> Cố định
                                    </span>
                                </label>
                                <div class="relative" title="Tài khoản đăng nhập là định danh cố định, không thể thay đổi">
                                    <i class="fa-solid fa-circle-user absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                    <input type="text" name="username" id="username" readonly onkeydown="return false;" value="{{ old('username', $landlordProfile->username ?? Auth::user()->username) }}" class="w-full pl-9 pr-9 py-2.5 rounded-xl border border-slate-800/80 bg-slate-950/70 text-xs text-slate-400 font-mono outline-none cursor-not-allowed select-none transition-all" tabindex="-1">
                                    <i class="fa-solid fa-lock absolute right-3.5 top-3 text-slate-500 text-xs"></i>
                                </div>
                                <span id="err-username" class="text-[11px] text-rose-400 mt-1 @error('username') block @else hidden @enderror">@error('username') {{ $message }} @enderror</span>
                            </div>

                            <!-- STT 2: Họ và tên -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">Họ và tên *</label>
                                <div class="relative">
                                    <i class="fa-solid fa-user-tie absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                    <input type="text" name="full_name" id="full_name" required value="{{ old('full_name', $landlordProfile->full_name ?? Auth::user()->name) }}" placeholder="Nguyễn Văn A" class="w-full pl-9 pr-3 py-2.5 rounded-xl border @error('full_name') border-rose-500 ring-2 ring-rose-500 @else border-slate-800 @enderror bg-slate-900 text-xs text-slate-100 outline-none focus:border-indigo-500 transition-all" onblur="validateLandlordFullName()" oninput="validateLandlordFullName()">
                                </div>
                                <span id="err-full_name" class="text-[11px] text-rose-400 mt-1 @error('full_name') block @else hidden @enderror">@error('full_name') {{ $message }} @enderror</span>
                            </div>

                            <!-- STT 3: Số điện thoại liên hệ -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">Số điện thoại liên hệ *</label>
                                <div class="relative">
                                    <i class="fa-solid fa-phone absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                    <input type="tel" name="phone" id="phone" required value="{{ old('phone', $landlordProfile->phone ?? Auth::user()->phone) }}" placeholder="0912345678" class="w-full pl-9 pr-3 py-2.5 rounded-xl border @error('phone') border-rose-500 ring-2 ring-rose-500 @else border-slate-800 @enderror bg-slate-900 text-xs text-slate-100 outline-none focus:border-indigo-500 transition-all" onblur="validateLandlordPhone()" oninput="validateLandlordPhone()">
                                </div>
                                <span id="err-phone" class="text-[11px] text-rose-400 mt-1 @error('phone') block @else hidden @enderror">@error('phone') {{ $message }} @enderror</span>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">Địa chỉ Email</label>
                                <div class="relative">
                                    <i class="fa-solid fa-envelope absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                    <input type="email" name="email" id="email" value="{{ old('email', $landlordProfile->email ?? Auth::user()->email) }}" placeholder="chutro@example.com" class="w-full pl-9 pr-3 py-2.5 rounded-xl border @error('email') border-rose-500 ring-2 ring-rose-500 @else border-slate-800 @enderror bg-slate-900 text-xs text-slate-100 outline-none focus:border-indigo-500 transition-all" onblur="validateLandlordEmail()" oninput="validateLandlordEmail()">
                                </div>
                                <span id="err-email" class="text-[11px] text-rose-400 mt-1 @error('email') block @else hidden @enderror">@error('email') {{ $message }} @enderror</span>
                            </div>
                        </div>

                        <!-- 2. Thông tin cơ sở lưu trú & Kinh doanh -->
                        <div class="border-b border-slate-800/60 pt-2 pb-3 flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400 flex items-center gap-2">
                                <i class="fa-solid fa-hotel"></i> Thông tin cơ sở lưu trú &amp; Khu trọ
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Tên cơ sở lưu trú (ERR_02_03) -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">Tên cơ sở lưu trú / Khu trọ *</label>
                                <div class="relative">
                                    <i class="fa-solid fa-building absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                    <input type="text" name="property_name" id="property_name" value="{{ old('property_name', $landlordProfile->property_name ?? $tenant->name ?? '') }}" placeholder="Khu trọ Bình An, Tòa nhà Diamond..." class="w-full pl-9 pr-3 py-2.5 rounded-xl border @error('property_name') border-rose-500 ring-2 ring-rose-500 @else border-slate-800 @enderror bg-slate-900 text-xs text-slate-100 outline-none focus:border-amber-500 transition-all" onblur="validateLandlordPropertyName()" oninput="validateLandlordPropertyName()">
                                </div>
                                <span id="err-property_name" class="text-[11px] text-rose-400 mt-1 @error('property_name') block @else hidden @enderror">@error('property_name') {{ $message }} @enderror</span>
                            </div>

                            <!-- Địa chỉ cơ sở lưu trú (ERR_02_04) -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">Địa chỉ cơ sở lưu trú *</label>
                                <div class="relative">
                                    <i class="fa-solid fa-location-dot absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                    <input type="text" name="property_address" id="property_address" value="{{ old('property_address', $landlordProfile->property_address ?? $tenant->address ?? '') }}" placeholder="Số 123 Đường Số 1, Phường Linh Trung, TP. Thủ Đức" class="w-full pl-9 pr-3 py-2.5 rounded-xl border @error('property_address') border-rose-500 ring-2 ring-rose-500 @else border-slate-800 @enderror bg-slate-900 text-xs text-slate-100 outline-none focus:border-amber-500 transition-all" onblur="validateLandlordPropertyAddress()" oninput="validateLandlordPropertyAddress()">
                                </div>
                                <span id="err-property_address" class="text-[11px] text-rose-400 mt-1 @error('property_address') block @else hidden @enderror">@error('property_address') {{ $message }} @enderror</span>
                            </div>
                        </div>

                        <!-- 3. Thông tin pháp lý & Tài khoản ngân hàng VietQR -->
                        <div class="border-b border-slate-800/60 pt-2 pb-3 flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved"></i> Thông tin pháp lý &amp; Tài khoản nhận tiền VietQR
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- CCCD/CMND -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">CCCD / CMND *</label>
                                <div class="relative">
                                    <i class="fa-solid fa-id-card absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                    <input type="text" name="national_id" id="national_id" value="{{ old('national_id', $landlordProfile->national_id ?? '') }}" placeholder="001203040506" class="w-full pl-9 pr-3 py-2.5 rounded-xl border @error('national_id') border-rose-500 ring-2 ring-rose-500 @else border-slate-800 @enderror bg-slate-900 text-xs text-slate-100 outline-none focus:border-emerald-500 transition-all" onblur="validateLandlordNationalId()" oninput="validateLandlordNationalId()">
                                </div>
                                <span id="err-national_id" class="text-[11px] text-rose-400 mt-1 @error('national_id') block @else hidden @enderror">@error('national_id') {{ $message }} @enderror</span>
                            </div>

                            <!-- Địa chỉ thường trú -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">Địa chỉ thường trú *</label>
                                <div class="relative">
                                    <i class="fa-solid fa-house-user absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                    <input type="text" name="permanent_address" id="permanent_address" value="{{ old('permanent_address', $landlordProfile->permanent_address ?? '') }}" placeholder="Địa chỉ theo CCCD" class="w-full pl-9 pr-3 py-2.5 rounded-xl border @error('permanent_address') border-rose-500 ring-2 ring-rose-500 @else border-slate-800 @enderror bg-slate-900 text-xs text-slate-100 outline-none focus:border-emerald-500 transition-all" onblur="validateLandlordPermanentAddress()" oninput="validateLandlordPermanentAddress()">
                                </div>
                                <span id="err-permanent_address" class="text-[11px] text-rose-400 mt-1 @error('permanent_address') block @else hidden @enderror">@error('permanent_address') {{ $message }} @enderror</span>
                            </div>

                            <!-- Giấy phép kinh doanh -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">Giấy phép kinh doanh / Mã số thuế *</label>
                                <div class="relative">
                                    <i class="fa-solid fa-file-invoice absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                    <input type="text" name="business_license" id="business_license" value="{{ old('business_license', $landlordProfile->business_license ?? '') }}" placeholder="Số ĐKKD hoặc Mã số thuế" class="w-full pl-9 pr-3 py-2.5 rounded-xl border @error('business_license') border-rose-500 ring-2 ring-rose-500 @else border-slate-800 @enderror bg-slate-900 text-xs text-slate-100 outline-none focus:border-emerald-500 transition-all" onblur="validateLandlordBusinessLicense()" oninput="validateLandlordBusinessLicense()">
                                </div>
                                <span id="err-business_license" class="text-[11px] text-rose-400 mt-1 @error('business_license') block @else hidden @enderror">@error('business_license') {{ $message }} @enderror</span>
                            </div>

                            <!-- Tên ngân hàng (ERR_02_06) -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">Tên ngân hàng thụ hưởng</label>
                                <div class="relative">
                                    <i class="fa-solid fa-building-columns absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                    <input type="text" name="bank_name" id="bank_name" list="bank-list-admin" value="{{ old('bank_name', $landlordProfile->bank_name ?? $tenant->bank_name ?? '') }}" placeholder="MB Bank, Vietcombank..." class="w-full pl-9 pr-3 py-2.5 rounded-xl border @error('bank_name') border-rose-500 ring-2 ring-rose-500 @else border-slate-800 @enderror bg-slate-900 text-xs text-slate-100 outline-none focus:border-emerald-500 transition-all" onblur="validateLandlordBank()" oninput="validateLandlordBank()">
                                    <datalist id="bank-list-admin">
                                        <option value="MB Bank (Quân Đội)">
                                        <option value="Vietcombank">
                                        <option value="Techcombank">
                                        <option value="BIDV">
                                        <option value="VietinBank">
                                        <option value="Agribank">
                                        <option value="ACB">
                                        <option value="VPBank">
                                        <option value="TPBank">
                                    </datalist>
                                </div>
                                <span id="err-bank_name" class="text-[11px] text-rose-400 mt-1 @error('bank_name') block @else hidden @enderror">@error('bank_name') {{ $message }} @enderror</span>
                            </div>

                            <!-- Số tài khoản ngân hàng (ERR_02_05 & ERR_02_07) -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">Số tài khoản ngân hàng</label>
                                <div class="relative">
                                    <i class="fa-solid fa-credit-card absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                    <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', $landlordProfile->bank_account_number ?? $tenant->bank_account_no ?? '') }}" placeholder="Số tài khoản ngân hàng" class="w-full pl-9 pr-3 py-2.5 rounded-xl border @error('bank_account_number') border-rose-500 ring-2 ring-rose-500 @else border-slate-800 @enderror bg-slate-900 text-xs text-slate-100 font-mono outline-none focus:border-emerald-500 transition-all" onblur="validateLandlordBankAccount()" oninput="validateLandlordBankAccount()">
                                </div>
                                <span id="err-bank_account_number" class="text-[11px] text-rose-400 mt-1 @error('bank_account_number') block @else hidden @enderror">@error('bank_account_number') {{ $message }} @enderror</span>
                            </div>

                            <!-- Đổi mật khẩu mới -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">Đổi mật khẩu mới</label>
                                <div class="relative">
                                    <i class="fa-solid fa-lock absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                    <input type="password" name="password" id="password" minlength="6" placeholder="Để trống nếu không đổi" class="w-full pl-9 pr-3 py-2.5 rounded-xl border @error('password') border-rose-500 ring-2 ring-rose-500 @else border-slate-800 @enderror bg-slate-900 text-xs text-slate-100 outline-none focus:border-indigo-500 transition-all" onblur="validateLandlordPassword()" oninput="validateLandlordPassword()">
                                </div>
                                <span id="err-password" class="text-[11px] text-rose-400 mt-1 @error('password') block @else hidden @enderror">@error('password') {{ $message }} @enderror</span>
                            </div>
                        </div>

                        <!-- STT 4: Nút Submit (btnSave) Nền xanh Indigo -->
                        <div class="flex items-center justify-end gap-3 pt-3">
                            <button type="submit" id="btnSave" name="btnSave" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/25 transition-all flex items-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-floppy-disk text-xs"></i>
                                <span>Lưu thay đổi</span>
                            </button>
                        </div>
                    </form>

                    <script>
                        // Xem trước ảnh đại diện (Preview tạm thời trên giao diện, chỉ lưu khi bấm nút Lưu Thay Đổi)
                        function previewLandlordAvatar(input) {
                            const file = input.files[0];
                            const preview = document.getElementById('avatar-preview');
                            const err = document.getElementById('err-avatar');
                            if (!file) return;

                            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                            if (!allowedTypes.includes(file.type)) {
                                if (err) {
                                    err.textContent = 'Ảnh đại diện phải là tệp hình ảnh hợp lệ (PNG, JPG, WebP).';
                                    err.classList.remove('hidden');
                                    err.classList.add('block');
                                }
                                input.value = '';
                                return;
                            }

                            if (file.size > 2 * 1024 * 1024) {
                                if (err) {
                                    err.textContent = 'Ảnh tải lên vượt quá dung lượng tối đa cho phép (2MB).';
                                    err.classList.remove('hidden');
                                    err.classList.add('block');
                                }
                                input.value = '';
                                return;
                            }

                            if (err) {
                                err.classList.add('hidden');
                                err.classList.remove('block');
                            }

                            // Xem trước ảnh trên Client (FileReader) - CHƯA gửi lên máy chủ
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                if (preview) preview.src = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        }

                        // Client-side Validation cho Form Hồ Sơ Chủ Trọ (FEAT_02_PROFILE)
                        function validateLandlordFullName() {
                            const input = document.getElementById('full_name');
                            const err = document.getElementById('err-full_name');
                            if (!input || !err) return true;
                            if (input.value.trim() === '') {
                                err.textContent = 'Họ và tên chủ cơ sở không được để trống.';
                                err.classList.remove('hidden');
                                err.classList.add('block');
                                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                return false;
                            }
                            err.classList.add('hidden');
                            err.classList.remove('block');
                            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
                            return true;
                        }

                        function validateLandlordPhone() {
                            const input = document.getElementById('phone');
                            const err = document.getElementById('err-phone');
                            if (!input || !err) return true;
                            const regex = /^0[0-9]{9}$/;
                            if (input.value.trim() === '' || !regex.test(input.value.trim())) {
                                err.textContent = 'Vui lòng nhập số điện thoại liên hệ chính của chủ trọ.';
                                err.classList.remove('hidden');
                                err.classList.add('block');
                                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                return false;
                            }
                            err.classList.add('hidden');
                            err.classList.remove('block');
                            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
                            return true;
                        }

                        function validateLandlordEmail() {
                            const input = document.getElementById('email');
                            const err = document.getElementById('err-email');
                            if (!input || !err) return true;
                            const val = input.value.trim();
                            if (val !== '') {
                                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                                if (!emailRegex.test(val)) {
                                    err.textContent = 'Địa chỉ email không đúng định dạng hợp lệ.';
                                    err.classList.remove('hidden');
                                    err.classList.add('block');
                                    input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                    return false;
                                }
                            }
                            err.classList.add('hidden');
                            err.classList.remove('block');
                            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
                            return true;
                        }

                        function validateLandlordPropertyName() {
                            const input = document.getElementById('property_name');
                            const err = document.getElementById('err-property_name');
                            if (!input || !err) return true;
                            if (input.value.trim() === '') {
                                err.textContent = 'Tên cơ sở lưu trú / khu trọ không được để trống.';
                                err.classList.remove('hidden');
                                err.classList.add('block');
                                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                return false;
                            }
                            err.classList.add('hidden');
                            err.classList.remove('block');
                            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
                            return true;
                        }

                        function validateLandlordPropertyAddress() {
                            const input = document.getElementById('property_address');
                            const err = document.getElementById('err-property_address');
                            if (!input || !err) return true;
                            if (input.value.trim() === '') {
                                err.textContent = 'Địa chỉ cơ sở lưu trú là bắt buộc để khách hàng định vị.';
                                err.classList.remove('hidden');
                                err.classList.add('block');
                                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                return false;
                            }
                            err.classList.add('hidden');
                            err.classList.remove('block');
                            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
                            return true;
                        }

                        function validateLandlordNationalId() {
                            const input = document.getElementById('national_id');
                            const err = document.getElementById('err-national_id');
                            if (!input || !err) return true;
                            const val = input.value.trim();
                            if (val === '') {
                                err.textContent = 'Vui lòng nhập số Căn cước công dân của chủ cơ sở.';
                                err.classList.remove('hidden');
                                err.classList.add('block');
                                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                return false;
                            }
                            if (!/^[0-9]+$/.test(val) || val.length < 9 || val.length > 12) {
                                err.textContent = 'Số CCCD / CMND phải gồm 9 đến 12 chữ số.';
                                err.classList.remove('hidden');
                                err.classList.add('block');
                                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                return false;
                            }
                            err.classList.add('hidden');
                            err.classList.remove('block');
                            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
                            return true;
                        }

                        function validateLandlordPermanentAddress() {
                            const input = document.getElementById('permanent_address');
                            const err = document.getElementById('err-permanent_address');
                            if (!input || !err) return true;
                            const val = input.value.trim();
                            if (val === '') {
                                err.textContent = 'Vui lòng nhập địa chỉ thường trú theo CCCD.';
                                err.classList.remove('hidden');
                                err.classList.add('block');
                                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                return false;
                            }
                            err.classList.add('hidden');
                            err.classList.remove('block');
                            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
                            return true;
                        }

                        function validateLandlordBusinessLicense() {
                            const input = document.getElementById('business_license');
                            const err = document.getElementById('err-business_license');
                            if (!input || !err) return true;
                            const val = input.value.trim();
                            if (val === '') {
                                err.textContent = 'Vui lòng nhập số Giấy phép kinh doanh hoặc Mã số thuế.';
                                err.classList.remove('hidden');
                                err.classList.add('block');
                                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                return false;
                            }
                            err.classList.add('hidden');
                            err.classList.remove('block');
                            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
                            return true;
                        }

                        function validateLandlordBankAccount() {
                            const input = document.getElementById('bank_account_number');
                            const bankInput = document.getElementById('bank_name');
                            const err = document.getElementById('err-bank_account_number');
                            if (!input || !err) return true;
                            const val = input.value.trim();
                            const bankVal = bankInput ? bankInput.value.trim() : '';

                            // ERR_02_05: Có tên ngân hàng mà để trống số tài khoản
                            if (bankVal !== '' && val === '') {
                                err.textContent = 'Số tài khoản ngân hàng không được để trống để tạo mã VietQR.';
                                err.classList.remove('hidden');
                                err.classList.add('block');
                                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                return false;
                            }

                            // ERR_02_07: Số tài khoản chứa ký tự không phải số
                            if (val !== '' && !/^[0-9]+$/.test(val)) {
                                err.textContent = 'Số tài khoản ngân hàng chỉ được bao gồm các chữ số nguyên.';
                                err.classList.remove('hidden');
                                err.classList.add('block');
                                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                return false;
                            }

                            err.classList.add('hidden');
                            err.classList.remove('block');
                            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
                            return true;
                        }

                        function validateLandlordBank() {
                            const bankInput = document.getElementById('bank_name');
                            const accInput = document.getElementById('bank_account_number');
                            const errBank = document.getElementById('err-bank_name');
                            if (!bankInput || !errBank) return true;
                            const bankVal = bankInput.value.trim();
                            const accVal = accInput ? accInput.value.trim() : '';
                            
                            // ERR_02_06: Có số tài khoản mà chưa chọn ngân hàng
                            if (accVal !== '' && bankVal === '') {
                                errBank.textContent = 'Vui lòng chọn ngân hàng thụ hưởng nhận tiền thuê phòng.';
                                errBank.classList.remove('hidden');
                                errBank.classList.add('block');
                                bankInput.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                return false;
                            }
                            errBank.classList.add('hidden');
                            errBank.classList.remove('block');
                            bankInput.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
                            return true;
                        }

                        function validateLandlordPassword() {
                            const input = document.getElementById('password');
                            const err = document.getElementById('err-password');
                            if (!input || !err) return true;
                            const val = input.value;
                            if (val !== '' && val.length < 6) {
                                err.textContent = 'Mật khẩu mới phải có tối thiểu 6 ký tự.';
                                err.classList.remove('hidden');
                                err.classList.add('block');
                                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                return false;
                            }
                            err.classList.add('hidden');
                            err.classList.remove('block');
                            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
                            return true;
                        }

                        document.getElementById('profile-form')?.addEventListener('submit', function(e) {
                            const okName = validateLandlordFullName();
                            const okPhone = validateLandlordPhone();
                            const okEmail = validateLandlordEmail();
                            const okPropName = validateLandlordPropertyName();
                            const okPropAddr = validateLandlordPropertyAddress();
                            const okNationalId = validateLandlordNationalId();
                            const okPermanent = validateLandlordPermanentAddress();
                            const okLicense = validateLandlordBusinessLicense();
                            const okAcc = validateLandlordBankAccount();
                            const okBank = validateLandlordBank();
                            const okPass = validateLandlordPassword();

                            if (!okName || !okPhone || !okEmail || !okPropName || !okPropAddr || !okNationalId || !okPermanent || !okLicense || !okAcc || !okBank || !okPass) {
                                e.preventDefault();
                                const firstErr = document.querySelector('#profile-form .ring-rose-500, #profile-form .border-rose-500');
                                if (firstErr) {
                                    firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    firstErr.focus();
                                }
                                return false;
                            }
                        });
                    </script>

                    <div id="verification-section" class="border-t border-slate-800 pt-6">
                        <h3 class="text-sm font-bold text-slate-200 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-sky-400"></i> Tiến Trình Xác Minh &amp; Nhận Tiền
                        </h3>
                    </div>

                    @if(($tenant->verification_status ?? 'unverified') === 'kyc_verified')
                        <div class="mb-6 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-emerald-100 text-sm font-bold flex items-center gap-3">
                            <i class="fa-solid fa-shield-halved text-emerald-300"></i>
                            Hồ sơ nhận tiền đã được xác minh. Bạn có thể dùng chuyển khoản/VietQR và tiếp tục nâng cấp tích xanh khi sẵn sàng.
                        </div>
                    @endif

                    @if(($tenant->verification_status ?? 'unverified') === 'premium_verified')
                        <div class="mb-6 rounded-2xl border border-sky-500/25 bg-sky-500/10 p-5 text-sky-50 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-11 h-11 rounded-xl bg-sky-500/10 border border-sky-400/30 text-sky-200 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-sky-100">Nhà trọ đã có Tích xanh</h3>
                                    <p class="mt-1 text-xs leading-6 text-sky-100/75">Hồ sơ ĐKKD, PCCC và ANTT đã được xác minh. Tin đăng được gắn huy hiệu Tích xanh và ưu tiên hiển thị trên Renty.</p>
                                </div>
                            </div>
                            <span class="px-3 py-1.5 rounded-lg bg-sky-400/10 border border-sky-400/25 text-sky-100 text-[10px] font-black">Tăng tốc hiển thị: đang bật</span>
                        </div>
                    @endif

                    @if(($tenant->verification_status ?? 'unverified') === 'premium_pending')
                        <div class="mb-6 rounded-2xl border border-sky-500/20 bg-sky-500/10 p-5 text-sky-50">
                            <div class="flex items-start gap-3">
                                <div class="w-11 h-11 rounded-xl bg-sky-500/10 border border-sky-400/30 text-sky-200 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-sky-100">Hồ sơ Tích xanh đang duyệt</h3>
                                    <p class="mt-1 text-xs leading-6 text-sky-100/75">Mã hồ sơ: #{{ $premiumRequest->id ?? 'N/A' }}. Trong lúc chờ duyệt, bạn vẫn giữ quyền nhận tiền/VietQR từ hồ sơ KYC đã xác minh.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(in_array(($tenant->verification_status ?? 'unverified'), ['unverified', 'kyc_pending'], true))
                        <div class="mb-6 rounded-2xl border border-amber-500/20 bg-amber-500/10 p-5 text-amber-50">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-seedling"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-black text-amber-100">Hồ sơ đang ở mức khởi đầu</h3>
                                        <p class="mt-1 text-xs leading-6 text-amber-100/80">
                                            Bạn có thể thêm phòng và quản lý nhà trọ ngay. Khi muốn nhận tiền tự động hoặc rút tiền, hãy bổ sung CCCD và tài khoản ngân hàng để mở khóa thanh toán.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 text-[10px] font-bold">
                                    <span class="px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-200">Tạo nhà trọ: xong</span>
                                    <span class="px-3 py-1.5 rounded-lg bg-slate-950/40 border border-amber-500/20 text-amber-100">KYC nhận tiền: {{ ($tenant->verification_status ?? 'unverified') === 'kyc_pending' ? 'đang duyệt' : 'khi cần' }}</span>
                                    <span class="px-3 py-1.5 rounded-lg bg-slate-950/40 border border-sky-500/20 text-sky-100">Tích xanh: tùy chọn</span>
                                </div>
                            </div>

                            @if(($tenant->verification_status ?? 'unverified') === 'kyc_pending')
                                <div class="mt-4 rounded-2xl border border-amber-500/40 bg-amber-500/15 p-4 text-xs text-amber-100 shadow-lg shadow-amber-500/5">
                                    <div class="flex items-center gap-2 font-bold text-amber-200">
                                        <i class="fa-solid fa-clock"></i>
                                        Hồ sơ xác minh KYC đã được gửi thành công! Vui lòng chờ Admin duyệt.
                                    </div>
                                    <p class="mt-2 text-slate-300">Mã hồ sơ: #{{ $kycRequest->id ?? 'N/A' }}. Ban quản trị SmartRoom đang thẩm định CCCD và phê duyệt cấp Tích Xanh cho cơ sở của bạn.</p>
                                </div>
                            @else
                                <form id="kyc-form" method="POST" action="{{ route('smartroom.admin.kyc.submit', [], false) }}" enctype="multipart/form-data" class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-5 rounded-2xl border border-slate-800 bg-slate-950/40 p-5">
                                    @csrf

                                    <!-- STT 1: Input Số CCCD 12 số (id_card_number) -->
                                    <div class="lg:col-span-3">
                                        <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">
                                            Số CCCD 12 số *
                                        </label>
                                        <div class="relative">
                                            <i class="fa-solid fa-id-card absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                            <input type="text" name="id_card_number" id="id_card_number" maxlength="12" placeholder="07920100xxxx" value="{{ old('id_card_number', old('cccd_number', '')) }}" class="w-full pl-9 pr-3 py-2.5 rounded-xl border @if($errors->has('id_card_number') || $errors->has('cccd_number')) border-rose-500 ring-2 ring-rose-500 @else border-slate-800 focus:border-emerald-500 @enderror bg-slate-900 text-xs text-slate-100 outline-none transition-all font-mono" onblur="validateKycCccd()" oninput="validateKycCccd()">
                                        </div>
                                        <span id="err-id_card_number" class="text-[11px] text-rose-400 mt-1.5 @if($errors->has('id_card_number') || $errors->has('cccd_number')) block @else hidden @endif font-medium">
                                            {{ $errors->first('id_card_number') ?: $errors->first('cccd_number') }}
                                        </span>
                                    </div>

                                    <!-- STT 2: Upload Box Ảnh mặt trước * (file_front) -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">
                                            Ảnh mặt trước *
                                        </label>
                                        <div id="box-front" class="relative border-2 border-dashed @if($errors->has('file_front') || $errors->has('front_image') || $errors->has('cccd_front')) border-rose-500 ring-2 ring-rose-500/50 @else border-slate-800 hover:border-emerald-500/60 @enderror rounded-2xl p-4 text-center bg-slate-900/60 transition-all cursor-pointer group flex flex-col items-center justify-center min-h-[140px]" onclick="document.getElementById('file_front').click()">
                                            <input type="file" name="file_front" id="file_front" accept="image/jpeg,image/png,image/webp,application/pdf" class="hidden" onchange="handleKycFileSelect(this, 'preview-front', 'placeholder-front', 'err-file_front', 'box-front')">
                                            
                                            <!-- Placeholder hướng dẫn -->
                                            <div id="placeholder-front" class="flex flex-col items-center justify-center space-y-1.5">
                                                <div class="w-10 h-10 rounded-xl bg-slate-800/80 border border-slate-700/60 flex items-center justify-center text-slate-400 group-hover:text-emerald-400 transition-colors">
                                                    <i class="fa-solid fa-cloud-arrow-up text-base"></i>
                                                </div>
                                                <p class="text-xs font-semibold text-slate-200">Chọn file JPG/PNG...</p>
                                                <p class="text-[10px] text-slate-500">Kéo thả hoặc bấm để tải lên (tối đa 10MB)</p>
                                            </div>

                                            <!-- Thumbnail xem trước -->
                                            <div id="preview-front" class="hidden relative w-full h-28 rounded-xl overflow-hidden border border-slate-700/80">
                                                <img src="" alt="CCCD Mặt trước" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity text-white text-[10px] font-bold">
                                                    <i class="fa-solid fa-arrows-rotate mr-1"></i> Đổi ảnh khác
                                                </div>
                                            </div>
                                        </div>
                                        <span id="err-file_front" class="text-[11px] text-rose-400 mt-1.5 @if($errors->has('file_front') || $errors->has('front_image') || $errors->has('cccd_front')) block @else hidden @endif font-medium">
                                            {{ $errors->first('file_front') ?: ($errors->first('front_image') ?: $errors->first('cccd_front')) }}
                                        </span>
                                    </div>

                                    <!-- STT 3: Upload Box Ảnh mặt sau * (file_back) -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">
                                            Ảnh mặt sau *
                                        </label>
                                        <div id="box-back" class="relative border-2 border-dashed @if($errors->has('file_back') || $errors->has('back_image') || $errors->has('cccd_back')) border-rose-500 ring-2 ring-rose-500/50 @else border-slate-800 hover:border-emerald-500/60 @enderror rounded-2xl p-4 text-center bg-slate-900/60 transition-all cursor-pointer group flex flex-col items-center justify-center min-h-[140px]" onclick="document.getElementById('file_back').click()">
                                            <input type="file" name="file_back" id="file_back" accept="image/jpeg,image/png,image/webp,application/pdf" class="hidden" onchange="handleKycFileSelect(this, 'preview-back', 'placeholder-back', 'err-file_back', 'box-back')">
                                            
                                            <div id="placeholder-back" class="flex flex-col items-center justify-center space-y-1.5">
                                                <div class="w-10 h-10 rounded-xl bg-slate-800/80 border border-slate-700/60 flex items-center justify-center text-slate-400 group-hover:text-emerald-400 transition-colors">
                                                    <i class="fa-solid fa-cloud-arrow-up text-base"></i>
                                                </div>
                                                <p class="text-xs font-semibold text-slate-200">Chọn file JPG/PNG...</p>
                                                <p class="text-[10px] text-slate-500">Kéo thả hoặc bấm để tải lên (tối đa 10MB)</p>
                                            </div>

                                            <div id="preview-back" class="hidden relative w-full h-28 rounded-xl overflow-hidden border border-slate-700/80">
                                                <img src="" alt="CCCD Mặt sau" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity text-white text-[10px] font-bold">
                                                    <i class="fa-solid fa-arrows-rotate mr-1"></i> Đổi ảnh khác
                                                </div>
                                            </div>
                                        </div>
                                        <span id="err-file_back" class="text-[11px] text-rose-400 mt-1.5 @if($errors->has('file_back') || $errors->has('back_image') || $errors->has('cccd_back')) block @else hidden @endif font-medium">
                                            {{ $errors->first('file_back') ?: ($errors->first('back_image') ?: $errors->first('cccd_back')) }}
                                        </span>
                                    </div>

                                    <!-- Bảng 3: Ảnh chụp chân dung cầm CCCD / Giấy tờ bổ sung (holding_image) -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1.5">
                                            Ảnh chân dung cầm CCCD (Tùy chọn)
                                        </label>
                                        <div id="box-holding" class="relative border-2 border-dashed @if($errors->has('holding_image') || $errors->has('bank_account_proof')) border-rose-500 ring-2 ring-rose-500/50 @else border-slate-800 hover:border-emerald-500/60 @enderror rounded-2xl p-4 text-center bg-slate-900/60 transition-all cursor-pointer group flex flex-col items-center justify-center min-h-[140px]" onclick="document.getElementById('holding_image').click()">
                                            <input type="file" name="holding_image" id="holding_image" accept="image/jpeg,image/png,image/webp,application/pdf" class="hidden" onchange="handleKycFileSelect(this, 'preview-holding', 'placeholder-holding', 'err-holding_image', 'box-holding')">
                                            
                                            <div id="placeholder-holding" class="flex flex-col items-center justify-center space-y-1.5">
                                                <div class="w-10 h-10 rounded-xl bg-slate-800/80 border border-slate-700/60 flex items-center justify-center text-slate-400 group-hover:text-emerald-400 transition-colors">
                                                    <i class="fa-solid fa-user-shield text-base"></i>
                                                </div>
                                                <p class="text-xs font-semibold text-slate-200">Chân dung hoặc PCCC/NH</p>
                                                <p class="text-[10px] text-slate-500">Giúp duyệt Tích Xanh siêu tốc</p>
                                            </div>

                                            <div id="preview-holding" class="hidden relative w-full h-28 rounded-xl overflow-hidden border border-slate-700/80">
                                                <img src="" alt="Ảnh chân dung" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity text-white text-[10px] font-bold">
                                                    <i class="fa-solid fa-arrows-rotate mr-1"></i> Đổi ảnh khác
                                                </div>
                                            </div>
                                        </div>
                                        <span id="err-holding_image" class="text-[11px] text-rose-400 mt-1.5 hidden font-medium"></span>
                                    </div>

                                    <!-- Bản Cam Kết Pháp Lý (Hình 9.1 Spec) -->
                                    <label class="lg:col-span-3 flex items-start gap-3 rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-3.5 text-xs leading-5 text-emerald-100 cursor-pointer">
                                        <input type="checkbox" name="admin_review_consent" value="1" required checked class="mt-0.5 rounded border-slate-700 bg-slate-950 text-emerald-500 focus:ring-emerald-500 cursor-pointer">
                                        <span>Tôi cam kết các thông tin định danh và hình ảnh CCCD cung cấp là chính xác, chính chủ, tuân thủ theo <a href="javascript:void(0)" onclick="openKycTermsModal()" class="text-emerald-300 underline underline-offset-2 hover:text-emerald-200 font-bold transition-colors">Điều khoản Bảo mật & Xác thực KYC</a> của SmartRoom.</span>
                                    </label>

                                    <!-- STT 4: Nút Submit (btnSubmitKYC) Nền xanh lá/xanh dương đậm -->
                                    <div class="lg:col-span-3 flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
                                        <p class="text-[11px] leading-relaxed text-slate-400">
                                            <i class="fa-solid fa-lock text-emerald-400 mr-1"></i> Thông tin được mã hóa bảo mật Zero Trust SHA-256 và chỉ phục vụ xét duyệt cấp Tích Xanh uy tín.
                                        </p>
                                        <button type="submit" id="btnSubmitKYC" class="shrink-0 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-6 py-3 text-xs font-bold text-white shadow-lg shadow-emerald-600/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                            <i class="fa-solid fa-shield-check text-xs"></i>
                                            <span>Gửi hồ sơ phê duyệt</span>
                                        </button>
                                    </div>
                                </form>

                                <script>
                                    // Xử lý xem trước ảnh (Preview) và Drag & Drop cho Upload Box
                                    function handleKycFileSelect(input, previewId, placeholderId, errorId, boxId) {
                                        const file = input.files[0];
                                        const previewEl = document.getElementById(previewId);
                                        const placeholderEl = document.getElementById(placeholderId);
                                        const errEl = document.getElementById(errorId);
                                        const boxEl = document.getElementById(boxId);

                                        if (!file) return;

                                        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
                                        // ERR_09_05: Tài liệu xác minh chỉ chấp nhận định dạng JPG, PNG hoặc PDF.
                                        if (!allowedTypes.includes(file.type)) {
                                            if (errEl) {
                                                errEl.textContent = 'Tài liệu xác minh chỉ chấp nhận định dạng JPG, PNG hoặc PDF.';
                                                errEl.classList.remove('hidden');
                                                errEl.classList.add('block');
                                            }
                                            if (boxEl) boxEl.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/50');
                                            input.value = '';
                                            return;
                                        }

                                        // ERR_09_06: Dung lượng tệp tài liệu vượt quá giới hạn cho phép (10MB).
                                        if (file.size > 10 * 1024 * 1024) {
                                            if (errEl) {
                                                errEl.textContent = 'Dung lượng tệp tài liệu vượt quá giới hạn cho phép (10MB).';
                                                errEl.classList.remove('hidden');
                                                errEl.classList.add('block');
                                            }
                                            if (boxEl) boxEl.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/50');
                                            input.value = '';
                                            return;
                                        }

                                        if (errEl) {
                                            errEl.classList.add('hidden');
                                            errEl.classList.remove('block');
                                        }
                                        if (boxEl) {
                                            boxEl.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/50');
                                            boxEl.classList.add('border-emerald-500');
                                        }

                                        if (file.type === 'application/pdf') {
                                            if (placeholderEl) {
                                                placeholderEl.innerHTML = `
                                                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center mx-auto mb-1">
                                                        <i class="fa-solid fa-file-pdf text-xl"></i>
                                                    </div>
                                                    <p class="text-xs font-bold text-emerald-300 truncate max-w-[180px]">${file.name}</p>
                                                    <p class="text-[10px] text-slate-400">Đã chọn tệp PDF</p>
                                                `;
                                            }
                                            if (previewEl) previewEl.classList.add('hidden');
                                        } else {
                                            const reader = new FileReader();
                                            reader.onload = function(e) {
                                                if (previewEl) {
                                                    const img = previewEl.querySelector('img');
                                                    if (img) img.src = e.target.result;
                                                    previewEl.classList.remove('hidden');
                                                }
                                                if (placeholderEl) placeholderEl.classList.add('hidden');
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    }

                                    // Client-side Validation cho Form KYC (FEAT_09_KYC)
                                    function validateKycCccd() {
                                        const input = document.getElementById('id_card_number');
                                        const err = document.getElementById('err-id_card_number');
                                        if (!input || !err) return true;
                                        const val = input.value.trim();

                                        // ERR_09_01: Để trống số CCCD
                                        if (val === '') {
                                            err.textContent = 'Vui lòng nhập số Căn cước công dân của chủ cơ sở.';
                                            err.classList.remove('hidden');
                                            err.classList.add('block');
                                            input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                            return false;
                                        }

                                        // ERR_09_02: Số CCCD không đúng 12 chữ số
                                        if (!/^[0-9]{12}$/.test(val)) {
                                            err.textContent = 'Số Căn cước công dân phải gồm chính xác 12 chữ số.';
                                            err.classList.remove('hidden');
                                            err.classList.add('block');
                                            input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                                            return false;
                                        }

                                        err.classList.add('hidden');
                                        err.classList.remove('block');
                                        input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
                                        return true;
                                    }

                                    function validateKycFront() {
                                        const input = document.getElementById('file_front');
                                        const err = document.getElementById('err-file_front');
                                        const box = document.getElementById('box-front');
                                        if (!input || !err) return true;

                                        // ERR_09_03: Chưa tải ảnh mặt trước CCCD
                                        if (!input.files || input.files.length === 0) {
                                            err.textContent = 'Vui lòng tải lên hình ảnh chụp mặt trước Căn cước công dân.';
                                            err.classList.remove('hidden');
                                            err.classList.add('block');
                                            if (box) box.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/50');
                                            return false;
                                        }

                                        err.classList.add('hidden');
                                        err.classList.remove('block');
                                        if (box) box.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/50');
                                        return true;
                                    }

                                    function validateKycBack() {
                                        const input = document.getElementById('file_back');
                                        const err = document.getElementById('err-file_back');
                                        const box = document.getElementById('box-back');
                                        if (!input || !err) return true;

                                        // ERR_09_04: Chưa tải ảnh mặt sau CCCD
                                        if (!input.files || input.files.length === 0) {
                                            err.textContent = 'Vui lòng tải lên hình ảnh chụp mặt sau Căn cước công dân.';
                                            err.classList.remove('hidden');
                                            err.classList.add('block');
                                            if (box) box.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/50');
                                            return false;
                                        }

                                        err.classList.add('hidden');
                                        err.classList.remove('block');
                                        if (box) box.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/50');
                                        return true;
                                    }

                                    // Lắng nghe sự kiện Submit Form KYC
                                    document.getElementById('kyc-form')?.addEventListener('submit', function(e) {
                                        const okCccd = validateKycCccd();
                                        const okFront = validateKycFront();
                                        const okBack = validateKycBack();

                                        if (!okCccd || !okFront || !okBack) {
                                            e.preventDefault();
                                            const firstErr = document.querySelector('#kyc-form .ring-rose-500, #kyc-form .border-rose-500');
                                            if (firstErr) {
                                                firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                                if (firstErr.tagName === 'INPUT') firstErr.focus();
                                            }
                                            return false;
                                        }
                                    });
                                </script>
                            @endif
                        </div>
                    @endif

                    @if(($tenant->verification_status ?? 'unverified') === 'kyc_verified')
                        <div class="mb-6 rounded-2xl border border-sky-500/20 bg-sky-500/10 p-5 text-sky-50">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-sky-500/10 border border-sky-400/30 text-sky-200 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-award"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-black text-sky-100">Lấy Tích xanh cho nhà trọ</h3>
                                        <p class="mt-1 text-xs leading-6 text-sky-100/75">Đây là bước tùy chọn. Hoàn tất ĐKKD, PCCC và ANTT để tin đăng có huy hiệu Tích xanh và được ưu tiên hiển thị trên Renty.</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 text-[10px] font-bold">
                                    <span class="px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-200">KYC nhận tiền: xong</span>
                                    <span class="px-3 py-1.5 rounded-lg bg-sky-500/10 border border-sky-500/25 text-sky-100">Tích xanh: tùy chọn</span>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('smartroom.admin.verification.premium', [], false) }}" enctype="multipart/form-data" class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-4 rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
                                @csrf
                                <label class="block">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Giấy phép ĐKKD</span>
                                    <input type="file" name="business_registration_certificate" accept=".pdf,image/*" required class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-300">
                                </label>
                                <label class="block">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Giấy chứng nhận PCCC</span>
                                    <input type="file" name="fire_safety_certificate" accept=".pdf,image/*" required class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-300">
                                </label>
                                <label class="block">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Giấy chứng nhận ANTT</span>
                                    <input type="file" name="security_order_certificate" accept=".pdf,image/*" required class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-300">
                                </label>

                                <label class="lg:col-span-3 flex items-start gap-3 rounded-xl border border-sky-500/20 bg-sky-500/5 p-3 text-[11px] leading-5 text-sky-50">
                                    <input type="checkbox" name="admin_review_consent" value="1" required class="mt-1 rounded border-slate-700 bg-slate-950 text-sky-400 focus:ring-sky-400">
                                    <span>Tôi đồng ý với <a href="javascript:void(0)" onclick="openKycTermsModal()" class="text-sky-300 underline underline-offset-2 hover:text-sky-200 font-bold transition-colors">Điều khoản Bảo mật Thông tin & Xác thực Tài khoản (KYC)</a> của SmartRoom.</span>
                                </label>

                                <div class="lg:col-span-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <p class="text-[11px] leading-5 text-slate-500">Nếu chưa có đủ giấy tờ, có thể bỏ qua bước này. Hệ thống chỉ dùng hồ sơ này để xét duyệt huy hiệu tin cậy và ưu tiên hiển thị.</p>
                                    <button type="submit" class="shrink-0 rounded-xl bg-sky-400 px-4 py-2 text-xs font-black text-slate-950 hover:bg-sky-300">
                                        Gửi hồ sơ Tích xanh
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </section>

