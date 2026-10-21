@php $editing = $page === 'update'; @endphp
<div class="register-card-header">
    <div>
        <p class="login-form-kicker">{{ $editing ? 'Cập nhật hồ sơ' : (Auth::check() ? 'Thêm thành viên mới' : 'Đăng ký tài khoản khách') }}</p>
        <h1>{{ $editing ? 'Chỉnh Sửa Tài Khoản' : (Auth::check() ? 'Thêm Thành Viên' : 'Đăng Ký Tài Khoản Khách') }}</h1>
        <p>{{ $editing ? 'Cập nhật thông tin quản trị viên #' . $user->id : (Auth::check() ? 'Tạo tài khoản mới và gán vai trò quản trị.' : 'Đăng ký tài khoản khách để tìm phòng và gửi liên hệ.') }}</p>
    </div>
    <div class="register-mini-badge" aria-hidden="true">
        <i class="fa-solid fa-user-tag"></i>
        <span>{{ $editing ? 'Admin' : (Auth::check() ? 'Thêm Mới' : 'Khách') }}</span>
    </div>
</div>
<form action="{{ $editing ? route('user.postUpdateUser') : route('user.postUser') }}" method="POST" class="register-form-grid">
    @csrf
    @if($editing)
        <input type="hidden" name="id" value="{{ $user->id }}">
    @endif
    <div class="register-field">
        <label class="block text-xs font-semibold text-slate-300 mb-2" for="name">Họ và Tên</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-user"></i></span>
            <input type="text" name="name" id="name" value="{{ old('name', $editing ? $user->name : '') }}" required class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all" placeholder="Nguyễn Văn A">
        </div>
    </div>
    <div class="register-field">
        <label class="block text-xs font-semibold text-slate-300 mb-2" for="username">Tên đăng nhập</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-user-tag"></i></span>
            <input type="text" name="username" id="username" value="{{ old('username', $editing ? $user->username : '') }}" required class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all" placeholder="username_vi_viet">
        </div>
    </div>
    <div class="register-field">
        <label class="block text-xs font-semibold text-slate-300 mb-2" for="phone">Số điện thoại</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-phone"></i></span>
            <input type="tel" name="phone" id="phone" value="{{ old('phone', $editing ? $user->phone : '') }}" required maxlength="10" pattern="^0[0-9]{9}$" inputmode="numeric" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)" class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all @error('phone') border-rose-500 ring-1 ring-rose-500/40 @enderror" placeholder="0901234567">
        </div>
        @error('phone')
            <p class="text-xs text-rose-400 font-semibold mt-1.5 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
            </p>
        @enderror
    </div>
    <div class="register-field">
        <label class="block text-xs font-semibold text-slate-300 mb-2" for="email">Địa chỉ Email</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" name="email" id="email" value="{{ old('email', $editing ? $user->email : '') }}" class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all" placeholder="admin@example.com">
        </div>
    </div>
    <div class="register-field register-field-wide">
        <label class="block text-xs font-semibold text-slate-300 mb-2" for="like">Ghi chú / Mô tả</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-heart"></i></span>
            <input type="text" name="like" id="like" value="{{ old('like', $editing ? $user->like : '') }}" class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all" placeholder="{{ Auth::check() ? 'Ghi chú công việc hoặc vai trò' : 'Ví dụ: Tìm phòng trọ khu vực Quận 10' }}">
        </div>
    </div>
    @if($editing)
        <div class="register-field">
            <label class="block text-xs font-semibold text-slate-300 mb-2" for="status">Tình trạng tài khoản</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-signal"></i></span>
                <select name="status" id="status" class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all">
                    <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="locked" {{ old('status', $user->status) === 'locked' ? 'selected' : '' }}>Đã khóa</option>
                    <option value="pending" {{ old('status', $user->status) === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                </select>
            </div>
        </div>
        @if(isset($tenants))
            <div class="register-field">
                <label class="block text-xs font-semibold text-slate-300 mb-2" for="tenant_id">Cơ sở lưu trú / Tenant</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-building"></i></span>
                    <select name="tenant_id" id="tenant_id" class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all">
                        <option value="">Không gán cơ sở (Toàn hệ thống)</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}" {{ old('tenant_id', $user->tenant_id) == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
    @endif
    <div class="register-field register-field-wide">
        <label class="block text-xs font-semibold text-slate-300 mb-2" for="password">{{ $editing ? 'Mật khẩu mới (Tùy chọn)' : 'Mật khẩu' }}</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="password" id="password" {{ $editing ? '' : 'required' }} class="login-input-control w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all" placeholder="{{ $editing ? 'Để trống nếu giữ nguyên mật khẩu hiện tại' : '•••••••• (tối thiểu 6 ký tự)' }}">
        </div>
    </div>
    <div class="register-actions">
        <a href="{{ $editing ? route('user.list') : route('login') }}">{{ $editing ? 'Hủy' : 'Đăng Nhập Ngay' }}</a>
        <button type="submit">{{ $editing ? 'Cập Nhật' : 'Đăng Ký Tài Khoản' }}</button>
    </div>
</form>
