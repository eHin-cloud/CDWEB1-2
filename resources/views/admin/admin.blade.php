<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Hệ thống quản lý SmartRoom - Trang quản trị nhà trọ.">
    <title>SmartRoom - Quản Trị Nhà Trọ Cao Cấp</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white overflow-hidden">
    @php
        $isLandlord = Auth::user()?->isLandlord();
    @endphp

    <!-- Decorative glows -->
    <div class="absolute top-[-10%] right-[-10%] w-[400px] h-[400px] rounded-full bg-indigo-600/5 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[400px] h-[400px] rounded-full bg-emerald-600/5 blur-[100px] pointer-events-none"></div>

    <!-- SIDEBAR -->
    @include('admin.partials.sidebar')

    <!-- MAIN APP WRAPPER -->
    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        
        <!-- TOP NAVBAR -->
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-20">
            <div class="flex items-center gap-2">
                <h2 id="section-title" class="text-lg font-bold text-slate-100">Tổng Quan Hệ Thống</h2>
            </div>
            
            <div class="flex items-center gap-6">
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" data-theme-icon></i>
                </button>
                <!-- Notifications -->
                <div class="relative">
                    <button class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                        <i class="fa-regular fa-bell"></i>
                        <span class="absolute top-2.5 right-2.5 w-2 h-2 rounded-full bg-indigo-500"></span>
                    </button>
                </div>
                
                <!-- Quick Date -->
                <div class="text-sm font-semibold text-slate-400 bg-slate-900 border border-slate-800 px-4 py-2 rounded-xl flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-indigo-400"></i>
                    <span>Tháng 06 / 2026</span>
                </div>
            </div>
        </header>

        <!-- CONTENT PANEL -->
        <main class="p-8 flex-grow overflow-y-auto">

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-semibold flex items-center gap-2 animate-fade-in">
                    <i class="fa-solid fa-circle-check text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-semibold flex items-center gap-2 animate-fade-in">
                    <i class="fa-solid fa-circle-exclamation text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- SECTION PROFILE: HỒ SƠ & XÁC MINH -->
            <section id="profile-section" class="tab-content space-y-8 animate-fade-in hidden">
                <div class="glass-card rounded-3xl p-8 border border-slate-800">
                    <h2 class="text-xl font-bold mb-6 text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-address-card text-indigo-400"></i> Hồ Sơ & Xác Minh Chủ Trọ
                    </h2>

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
                                <div class="mt-4 rounded-2xl border border-slate-800 bg-slate-950/40 p-4 text-xs text-slate-300">
                                    <div class="flex items-center gap-2 font-bold text-amber-100">
                                        <i class="fa-solid fa-clock"></i>
                                        Hồ sơ KYC đã gửi, đang chờ quản trị viên hệ thống duyệt.
                                    </div>
                                    <p class="mt-2 text-slate-500">Mã hồ sơ: #{{ $kycRequest->id ?? 'N/A' }}. Bạn vẫn có thể tiếp tục thêm phòng và quản lý nhà trọ trong lúc chờ xét duyệt.</p>
                                </div>
                            @else
                                <form method="POST" action="{{ route('smartroom.admin.verification.kyc') }}" enctype="multipart/form-data" class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-4 rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
                                    @csrf
                                    <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                                        <label class="block">
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Số CCCD</span>
                                            <input name="cccd_number" required class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-100 outline-none focus:border-amber-400">
                                        </label>
                                        <label class="block">
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Ngân hàng</span>
                                            <input name="bank_name" required value="{{ old('bank_name', $tenant->bank_name ?? 'MB') }}" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-100 outline-none focus:border-amber-400">
                                        </label>
                                        <label class="block">
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Số tài khoản</span>
                                            <input name="bank_account_no" required value="{{ old('bank_account_no', $tenant->bank_account_no ?? '') }}" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-100 outline-none focus:border-amber-400">
                                        </label>
                                        <label class="block">
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Tên chủ tài khoản</span>
                                            <input name="bank_account_name" required value="{{ old('bank_account_name', $tenant->bank_account_name ?? '') }}" class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-100 outline-none focus:border-amber-400">
                                        </label>
                                    </div>

                                    <label class="block">
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1">CCCD mặt trước</span>
                                        <input type="file" name="cccd_front" accept="image/*" required class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-300">
                                    </label>
                                    <label class="block">
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1">CCCD mặt sau</span>
                                        <input type="file" name="cccd_back" accept="image/*" required class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-300">
                                    </label>
                                    <label class="block">
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Ảnh/xác nhận tài khoản NH</span>
                                        <input type="file" name="bank_account_proof" accept="image/*" required class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-300">
                                    </label>

                                    <label class="lg:col-span-3 flex items-start gap-3 rounded-xl border border-amber-500/20 bg-amber-500/5 p-3 text-[11px] leading-5 text-amber-50">
                                        <input type="checkbox" name="admin_review_consent" value="1" required class="mt-1 rounded border-slate-700 bg-slate-950 text-amber-400 focus:ring-amber-400">
                                        <span>Tôi đồng ý với <a href="javascript:void(0)" onclick="openKycTermsModal()" class="text-amber-300 underline underline-offset-2 hover:text-amber-200 font-bold transition-colors">Điều khoản Bảo mật Thông tin & Xác thực Tài khoản (KYC)</a> của SmartRoom.</span>
                                    </label>

                                    <div class="lg:col-span-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <p class="text-[11px] leading-5 text-slate-500">Thông tin này chỉ dùng để mở khóa nhận tiền/chuyển khoản. Giấy phép PCCC và ANTT sẽ là bước tích xanh riêng, không bắt buộc lúc này.</p>
                                        <button type="submit" class="shrink-0 rounded-xl bg-amber-500 px-4 py-2 text-xs font-black text-slate-950 hover:bg-amber-400">
                                            Gửi KYC nhận tiền
                                        </button>
                                    </div>
                                </form>
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

                            <form method="POST" action="{{ route('smartroom.admin.verification.premium') }}" enctype="multipart/form-data" class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-4 rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
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

            <!-- SECTION 1: DASHBOARD OVERVIEW -->
            <section id="dashboard-section" class="tab-content space-y-8 animate-fade-in">
                <!-- Stats ribbon -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Rooms -->
                    <div class="glass-card rounded-2xl p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-500/5 rounded-full blur-xl"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tổng số phòng</span>
                            <h3 class="text-3xl font-extrabold text-slate-100 mt-2">{{ $totalRooms }}</h3>
                            <span class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1 mt-1">
                                <i class="fa-solid fa-arrow-up"></i> 100% Khai thác
                            </span>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center">
                            <i class="fa-solid fa-door-open text-xl"></i>
                        </div>
                    </div>
                    <!-- Occupied Rooms -->
                    <div class="glass-card rounded-2xl p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-red-500/5 rounded-full blur-xl"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Đang thuê</span>
                            <h3 class="text-3xl font-extrabold text-red-400 mt-2">{{ $occupiedRooms }}</h3>
                            <span class="text-[10px] text-slate-400 font-semibold flex items-center gap-1 mt-1">
                                Tỉ lệ lấp đầy: {{ $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0 }}%
                            </span>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center justify-center">
                            <i class="fa-solid fa-user-check text-xl"></i>
                        </div>
                    </div>
                    <!-- Empty Rooms -->
                    <div class="glass-card rounded-2xl p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Phòng trống</span>
                            <h3 class="text-3xl font-extrabold text-emerald-400 mt-2">{{ $emptyRooms }}</h3>
                            <span class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1 mt-1">
                                Sẵn sàng đón khách
                            </span>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                            <i class="fa-solid fa-circle-plus text-xl"></i>
                        </div>
                    </div>
                    <!-- Overdue Rooms -->
                    <div class="glass-card rounded-2xl p-6 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-amber-500/5 rounded-full blur-xl"></div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Chưa đóng tiền</span>
                            <h3 class="text-3xl font-extrabold text-amber-400 mt-2">{{ $overdueRooms }}</h3>
                            <span class="text-[10px] text-amber-400 font-semibold flex items-center gap-1 mt-1">
                                Cần nhắc nhở đóng phí
                            </span>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                        </div>
                    </div>
                </div>

                @if($isLandlord)
                <!-- AI insights and assistant -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <div class="glass-card rounded-2xl p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-200 flex items-center gap-2">
                                    <i class="fa-solid fa-wand-magic-sparkles text-indigo-400"></i> Nhận xét AI
                                </h3>
                                <p class="text-xs text-slate-500 mt-1">Phân tích nhanh doanh thu, công nợ, phòng trống và rủi ro vận hành.</p>
                            </div>
                            <button type="button" onclick="loadAiDashboardInsight(this)" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold flex items-center gap-2">
                                <i class="fa-solid fa-rotate"></i> Tạo nhận xét
                            </button>
                        </div>
                        <div id="ai-dashboard-insight" class="mt-5 rounded-xl bg-slate-950/40 border border-slate-800 p-4 text-sm text-slate-300 leading-relaxed">
                            Bấm "Tạo nhận xét" để AI phân tích dữ liệu dashboard hiện tại.
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-comments text-emerald-400"></i> Trợ lý AI nhà trọ
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">Hỏi bằng tiếng Việt, ví dụ: "Phòng nào chưa đóng tiền tháng này?" hoặc "Ai sắp hết hợp đồng?".</p>
                        </div>
                        <div class="mt-5 flex gap-2">
                            <input id="ai-assistant-question" type="text" class="min-w-0 flex-1 px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-200 focus:outline-none focus:border-emerald-500" placeholder="Nhập câu hỏi quản lý nhà trọ...">
                            <button type="button" onclick="askAiAssistant(this)" class="px-4 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i> Hỏi
                            </button>
                        </div>
                        <div id="ai-assistant-answer" class="mt-4 rounded-xl bg-slate-950/40 border border-slate-800 p-4 text-sm text-slate-300 leading-relaxed min-h-20">
                            Trợ lý chỉ đọc dữ liệu thuộc tài khoản chủ trọ đang đăng nhập.
                        </div>
                    </div>
                </div>
                @endif

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Revenue Line & Doughnut Split Chart -->
                    <div class="glass-card rounded-2xl p-6 lg:col-span-2 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-4 border-b border-slate-900/60 pb-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-200">Doanh Thu 3 Tháng Gần Nhất</h3>
                                <p class="text-xs text-slate-500">Báo cáo xu hướng cột và cơ cấu nguồn thu từ điện, nước, phòng</p>
                            </div>
                            <span class="text-[10px] px-2.5 py-1 rounded bg-indigo-500/10 text-indigo-400 font-bold border border-indigo-500/20 uppercase tracking-wider">Doanh thu tháng 06</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                            <!-- Left: Trend Bar Chart -->
                            <div class="md:col-span-3 flex flex-col justify-between">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Xu hướng 3 tháng</span>
                                <div class="h-56 w-full">
                                    <canvas id="revenueChart"></canvas>
                                </div>
                            </div>
                            
                            <!-- Right: Doughnut Breakdown Chart -->
                            <div class="md:col-span-2 flex flex-col justify-between border-t md:border-t-0 md:border-l border-slate-800/50 pt-4 md:pt-0 md:pl-6">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Cơ Cấu Nguồn Thu</span>
                                <div class="h-36 w-full flex items-center justify-center relative">
                                    <canvas id="revenueBreakdownChart"></canvas>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-2">
                                        <span class="text-[8px] text-slate-500 font-bold uppercase tracking-wider">Tổng thu</span>
                                        <span class="text-xs font-black text-indigo-400" id="breakdown-total-txt">--M</span>
                                    </div>
                                </div>
                                <div class="space-y-1.5 mt-3 text-[10px]">
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-slate-450"><span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>Tiền phòng</span>
                                        <strong class="text-slate-200" id="breakdown-room-pct">--%</strong>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-slate-450"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Tiền điện</span>
                                        <strong class="text-slate-200" id="breakdown-elec-pct">--%</strong>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-slate-450"><span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>Tiền nước</span>
                                        <strong class="text-slate-200" id="breakdown-water-pct">--%</strong>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 text-slate-450"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Dịch vụ</span>
                                        <strong class="text-slate-200" id="breakdown-service-pct">--%</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Doughnut Room Status Chart -->
                    <div class="glass-card rounded-2xl p-6 flex flex-col justify-between">
                        <div class="mb-4">
                            <h3 class="text-base font-bold text-slate-200">Tỷ Lệ Phòng Trống / Đầy</h3>
                            <p class="text-xs text-slate-500">Tổng quan nhanh tình trạng khai thác phòng</p>
                        </div>
                        <div class="h-48 w-full flex items-center justify-center relative">
                            <canvas id="statusChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Tổng phòng</span>
                                <strong class="text-2xl font-black text-slate-100">{{ $totalRooms }}</strong>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                            <div class="p-2 rounded-xl bg-emerald-500/5 border border-emerald-500/10">
                                <span class="block text-[10px] text-slate-500 font-bold uppercase">Trống</span>
                                <strong class="text-sm text-emerald-400">{{ $totalRooms > 0 ? round(($emptyRooms / $totalRooms) * 100, 1) : 0 }}%</strong>
                            </div>
                            <div class="p-2 rounded-xl bg-red-500/5 border border-red-500/10">
                                <span class="block text-[10px] text-slate-500 font-bold uppercase">Thuê</span>
                                <strong class="text-sm text-red-400">{{ $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0 }}%</strong>
                            </div>
                            <div class="p-2 rounded-xl bg-amber-500/5 border border-amber-500/10">
                                <span class="block text-[10px] text-slate-500 font-bold uppercase">Nợ</span>
                                <strong class="text-sm text-amber-400">{{ $totalRooms > 0 ? round(($overdueRooms / $totalRooms) * 100, 1) : 0 }}%</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Smart Alerts Dashboard -->
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-brain text-indigo-400"></i>
                                Dashboard cảnh báo thông minh
                            </h3>
                            <p class="text-xs text-slate-500">Tự động quét hợp đồng, hóa đơn, phòng trống và thiết bị cần xử lý</p>
                        </div>
                        <div class="px-4 py-2 rounded-xl bg-slate-900/70 border border-slate-800 text-xs font-bold text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-bell text-amber-400"></i>
                            {{ $smartAlertTotal }} cảnh báo cần chú ý
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                        @foreach($smartAlertGroups as $group)
                            @php
                                $alertColorClasses = [
                                    'indigo' => [
                                        'box' => 'bg-indigo-500/5 border-indigo-500/20',
                                        'icon' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
                                        'count' => 'text-indigo-400',
                                        'badge' => 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20',
                                    ],
                                    'amber' => [
                                        'box' => 'bg-amber-500/5 border-amber-500/20',
                                        'icon' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                        'count' => 'text-amber-400',
                                        'badge' => 'bg-amber-500/10 text-amber-300 border-amber-500/20',
                                    ],
                                    'emerald' => [
                                        'box' => 'bg-emerald-500/5 border-emerald-500/20',
                                        'icon' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'count' => 'text-emerald-400',
                                        'badge' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20',
                                    ],
                                    'rose' => [
                                        'box' => 'bg-rose-500/5 border-rose-500/20',
                                        'icon' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                                        'count' => 'text-rose-400',
                                        'badge' => 'bg-rose-500/10 text-rose-300 border-rose-500/20',
                                    ],
                                ][$group['color']];
                            @endphp

                            <div class="rounded-2xl border {{ $alertColorClasses['box'] }} p-4 flex flex-col min-h-[320px]">
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-xl border {{ $alertColorClasses['icon'] }} flex items-center justify-center shrink-0">
                                            <i class="fa-solid {{ $group['icon'] }}"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-extrabold text-slate-200 leading-snug">{{ $group['label'] }}</h4>
                                            <p class="text-[10px] text-slate-500 mt-0.5">Cập nhật theo dữ liệu hiện tại</p>
                                        </div>
                                    </div>
                                    <span class="text-2xl font-black {{ $alertColorClasses['count'] }}">{{ $group['count'] }}</span>
                                </div>

                                <div class="space-y-2 flex-1">
                                    @forelse($group['items'] as $item)
                                        <div class="rounded-xl bg-slate-950/40 border border-slate-800/70 p-3">
                                            <div class="flex items-start justify-between gap-2">
                                                <strong class="text-xs text-slate-200 leading-snug">{{ $item['title'] }}</strong>
                                                <span class="shrink-0 text-[9px] font-bold px-2 py-0.5 rounded-full border {{ $alertColorClasses['badge'] }}">{{ $item['meta'] }}</span>
                                            </div>
                                            <p class="text-[10px] text-slate-500 mt-1 leading-relaxed">{{ $item['detail'] }}</p>
                                        </div>
                                    @empty
                                        <div class="h-full min-h-[170px] rounded-xl bg-slate-950/30 border border-slate-800/50 flex flex-col items-center justify-center text-center p-4">
                                            <i class="fa-solid fa-circle-check text-emerald-400 mb-2"></i>
                                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ $group['empty'] }}</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($isLandlord)
                <!-- Notification Center -->
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-6 mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-paper-plane text-cyan-400"></i>
                                Notification Center
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">Gui gia lap Email, Zalo, SMS va luu log tat ca thong bao.</p>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="px-4 py-3 rounded-xl bg-slate-950/50 border border-slate-800">
                                <div class="text-lg font-black text-emerald-300">{{ $notificationSummary['sent'] }}</div>
                                <div class="text-[10px] text-slate-500 font-bold uppercase">Sent</div>
                            </div>
                            <div class="px-4 py-3 rounded-xl bg-slate-950/50 border border-slate-800">
                                <div class="text-lg font-black text-amber-300">{{ $notificationSummary['skipped'] }}</div>
                                <div class="text-[10px] text-slate-500 font-bold uppercase">Skipped</div>
                            </div>
                            <div class="px-4 py-3 rounded-xl bg-slate-950/50 border border-slate-800">
                                <div class="text-lg font-black text-indigo-300">{{ $notificationSummary['today'] }}</div>
                                <div class="text-[10px] text-slate-500 font-bold uppercase">Today</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-3 mb-6">
                        <form method="POST" action="{{ route('smartroom.admin.notifications.run_all') }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold flex items-center justify-center gap-2">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Run all
                            </button>
                        </form>
                        <button type="button" onclick="triggerAutoRemind(this)" class="w-full px-4 py-3 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold flex items-center justify-center gap-2">
                            <i class="fa-solid fa-money-bill-wave"></i> Payment
                        </button>
                        <form method="POST" action="{{ route('smartroom.admin.notifications.contracts') }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold flex items-center justify-center gap-2">
                                <i class="fa-solid fa-file-signature"></i> Contracts
                            </button>
                        </form>
                        <form method="POST" action="{{ route('smartroom.admin.notifications.maintenance') }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center justify-center gap-2">
                                <i class="fa-solid fa-screwdriver-wrench"></i> Maintenance
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-900">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/70 border-b border-slate-900">
                                <tr>
                                    <th class="px-4 py-3 font-bold">Time</th>
                                    <th class="px-4 py-3 font-bold">Type</th>
                                    <th class="px-4 py-3 font-bold">Channel</th>
                                    <th class="px-4 py-3 font-bold">Recipient</th>
                                    <th class="px-4 py-3 font-bold">Subject</th>
                                    <th class="px-4 py-3 font-bold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900 bg-slate-950/20">
                                @forelse($notificationLogs as $log)
                                    <tr class="hover:bg-slate-900/30 transition-all">
                                        <td class="px-4 py-3 text-xs text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-3 text-xs font-bold text-slate-200">{{ str_replace('_', ' ', $log->type) }}</td>
                                        <td class="px-4 py-3 text-xs font-bold text-cyan-300 uppercase">{{ $log->channel }}</td>
                                        <td class="px-4 py-3 text-xs text-slate-400">
                                            <div class="font-bold text-slate-300">{{ $log->recipient_name ?? '-' }}</div>
                                            <div class="text-[10px] text-slate-500">{{ $log->recipient_contact ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-slate-400 max-w-[320px] truncate" title="{{ $log->message }}">{{ $log->subject }}</td>
                                        <td class="px-4 py-3">
                                            @if($log->status === 'sent')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">sent</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-300 border border-amber-500/20">{{ $log->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-xs text-slate-500">No notification logs yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Recent Events Table -->
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200">Hoạt Động Gần Đây</h3>
                            <p class="text-xs text-slate-500">Các giao dịch và cập nhật mới nhất trong tháng</p>
                        </div>
                        <button class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1">
                            Xem tất cả <i class="fa-solid fa-angle-right"></i>
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/50 border-b border-slate-900">
                                <tr>
                                    <th class="px-6 py-4 font-bold">Cư dân / Phòng</th>
                                    <th class="px-6 py-4 font-bold">Loại giao dịch</th>
                                    <th class="px-6 py-4 font-bold">Số tiền</th>
                                    <th class="px-6 py-4 font-bold">Thời gian</th>
                                    <th class="px-6 py-4 font-bold">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900">
                                @foreach($rooms->where('status', '!=', 'empty')->take(3) as $r)
                                @php
                                    $latestRecord = $r->utilityRecords->first();
                                    $resident = $r->residents->first();
                                @endphp
                                @if($resident)
                                <tr class="hover:bg-slate-900/30 transition-all">
                                    <td class="px-6 py-4 flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400 font-bold text-xs">
                                            {{ $r->room_number }}
                                        </div>
                                        <div>
                                            <strong class="text-slate-200 text-xs block">{{ $resident->name }}</strong>
                                            <span class="text-[10px] text-slate-500">Phòng {{ $r->room_number }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-xs text-slate-400">
                                        {{ $r->status === 'overdue' ? 'Gửi hóa đơn tháng ' . ($latestRecord ? explode('-', $latestRecord->billing_month)[1] : '06') : 'Đã đóng hóa đơn tháng ' . ($latestRecord ? explode('-', $latestRecord->billing_month)[1] : '05') }}
                                    </td>
                                    <td class="px-6 py-4 {{ $r->status === 'overdue' ? 'text-amber-400' : 'text-emerald-400' }} font-bold text-xs">
                                        @if($latestRecord)
                                            {{ number_format($r->price + ($latestRecord->new_electricity - $latestRecord->old_electricity) * $latestRecord->electricity_price + ($latestRecord->new_water - $latestRecord->old_water) * $latestRecord->water_price + 150000) }}đ
                                        @else
                                            {{ number_format($r->price + 150000) }}đ
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">
                                        {{ $latestRecord ? $latestRecord->updated_at->diffForHumans() : 'Hôm nay' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($r->status === 'overdue')
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Chưa đóng</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Hoàn tất</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- SECTION 2: VISUAL ROOM MAP -->
            <section id="room-map-section" class="tab-content hidden space-y-8 animate-fade-in">
                <!-- Filter buttons and color legend -->
                <div class="flex flex-wrap items-center justify-between gap-4 bg-slate-900/40 border border-slate-800/80 p-4 rounded-2xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <button data-filter="all" onclick="filterRooms('all', this)" class="room-filter-btn px-3.5 py-2 text-xs font-bold rounded-xl bg-indigo-600 text-white transition-all">
                            Tất cả (<span id="filter-count-all">{{ $totalRooms }}</span>)
                        </button>
                        <button data-filter="empty" onclick="filterRooms('empty', this)" class="room-filter-btn px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Trống (<span id="filter-count-empty">{{ $emptyRooms }}</span>)
                        </button>
                        <button data-filter="occupied" onclick="filterRooms('occupied', this)" class="room-filter-btn px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Đã thuê (<span id="filter-count-occupied">{{ $occupiedRooms }}</span>)
                        </button>
                        <button data-filter="overdue" onclick="filterRooms('overdue', this)" class="room-filter-btn px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Nợ phí (<span id="filter-count-overdue">{{ $overdueRooms }}</span>)
                        </button>
                        <button data-filter="cleaning" onclick="filterRooms('cleaning', this)" class="room-filter-btn px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Cần dọn (<span id="filter-count-cleaning">{{ $cleaningRooms ?? 0 }}</span>)
                        </button>
                        <button data-filter="maintenance" onclick="filterRooms('maintenance', this)" class="room-filter-btn px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Bảo trì (<span id="filter-count-maintenance">{{ $maintenanceRooms ?? 0 }}</span>)
                        </button>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-4 sm:gap-6 text-xs font-bold text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-md bg-emerald-500 border border-emerald-400/30"></span>
                            <span>Trống</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-md bg-red-500 border border-red-400/30"></span>
                            <span>Đã thuê</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-md bg-amber-500 border border-amber-400/30"></span>
                            <span>Nợ tiền</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-md bg-orange-500 border border-orange-400/30 animate-pulse"></span>
                            <span>Dọn dẹp</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-md bg-slate-500 border border-slate-400/30"></span>
                            <span>Bảo trì</span>
                        </div>
                    </div>
                </div>

                <!-- Floors and room grid -->
                <div class="space-y-8" id="room-matrix-container">
                    @foreach($roomsByFloor as $floor => $floorRooms)
                    <div class="floor-group" data-floor="{{ $floor }}">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-indigo-400"></i> Tầng {{ $floor }}
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            @foreach($floorRooms as $room)
                            @php
                                $resident = $room->residents->first();
                                $latestBill = $room->utilityRecords->first();
                                $elecUsed = $latestBill ? ($latestBill->new_electricity - $latestBill->old_electricity) : 0;
                                $waterUsed = $latestBill ? ($latestBill->new_water - $latestBill->old_water) : 0;
                                $statusLabel = $room->status_label;
                                $statusClass = $room->status_class;
                                $badgeClass = $room->badge_class;
                                $totalBill = $latestBill ? ($room->price + ($elecUsed * $latestBill->electricity_price) + ($waterUsed * $latestBill->water_price) + 150000) : 0;
                            @endphp
                            <div id="room-card-{{ $room->id }}"
                                 data-room-id="{{ $room->id }}"
                                 data-room-number="{{ $room->room_number }}"
                                 data-room-status="{{ $room->status }}"
                                 data-resident-name="{{ $resident ? $resident->name : '' }}"
                                 data-resident-phone="{{ $resident ? $resident->phone : '' }}"
                                 data-price="{{ number_format($room->price) }}đ"
                                 data-elec-used="{{ $elecUsed }} kWh"
                                 data-water-used="{{ $waterUsed }} m3"
                                 data-total-bill="{{ number_format($totalBill) }}đ"
                                 data-latest-bill-id="{{ $latestBill ? $latestBill->id : '' }}"
                                 onclick="openRoomDetailById({{ $room->id }})" 
                                 class="room-card {{ $statusClass }} glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/10">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-lg font-extrabold text-slate-200">P. {{ $room->room_number }}</span>
                                    <span class="room-badge px-2 py-0.5 rounded text-[10px] font-extrabold border {{ $badgeClass }}">{{ $statusLabel }}</span>
                                </div>
                                @if($resident && in_array($room->status, ['occupied', 'overdue']))
                                    <h4 class="room-resident text-xs font-bold text-slate-400 truncate mb-1">Cư dân: {{ $resident->name }}</h4>
                                @else
                                    <h4 class="room-resident text-xs font-bold text-slate-500 italic mb-1">Chưa có cư dân</h4>
                                @endif
                                <p class="text-[10px] text-slate-500">Giá phòng: {{ number_format($room->price) }}đ</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- ROOM DETAIL MODAL (CENTERED & FITS CONTENT) -->
                <div id="room-detail-modal" onclick="closeRoomDetail()" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-md hidden flex items-center justify-center p-4 sm:p-6 transition-all duration-200">
                    <div onclick="event.stopPropagation()" class="w-full max-w-md bg-[#0a0f1d] border border-slate-800/80 rounded-3xl p-6 sm:p-7 shadow-2xl relative my-auto max-h-[90vh] overflow-y-auto space-y-4 animate-fade-in border-t border-t-indigo-500/30">
                        <button type="button" onclick="closeRoomDetail()" class="absolute top-5 right-5 w-8 h-8 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all shadow-sm">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                        
                        <div class="space-y-4">
                            <!-- Room Head -->
                            <div class="pr-8">
                                <span class="text-xs px-2.5 py-1 rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 font-bold uppercase" id="modal-room-status-badge">Đã thuê</span>
                                <h2 class="text-2xl font-extrabold text-slate-100 mt-2" id="modal-room-title">Phòng 202</h2>
                            </div>

                            <!-- Housekeeping Quick Action Selector -->
                            <div class="p-3.5 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-2.5">
                                <div class="flex items-center justify-between text-[11px] font-bold text-slate-400">
                                    <span class="flex items-center gap-1.5 uppercase tracking-wider text-slate-300">
                                        <i class="fa-solid fa-broom text-amber-400"></i> Housekeeping / Dọn buồng
                                    </span>
                                    <span id="quick-status-loading" class="text-indigo-400 hidden">
                                        <i class="fa-solid fa-circle-notch fa-spin"></i> Đang lưu...
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-xs">
                                    <button type="button" onclick="setQuickRoomStatus('empty')" id="btn-quick-empty" class="py-2 px-2 rounded-xl font-bold border transition-all text-center flex flex-col items-center gap-1 hover:border-emerald-500 hover:bg-emerald-500/10 text-emerald-400 border-slate-800 bg-slate-900/50">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/50"></span>
                                        <span class="text-[11px]">Đã dọn xong</span>
                                    </button>
                                    <button type="button" onclick="setQuickRoomStatus('cleaning')" id="btn-quick-cleaning" class="py-2 px-2 rounded-xl font-bold border transition-all text-center flex flex-col items-center gap-1 hover:border-orange-500 hover:bg-orange-500/10 text-orange-400 border-slate-800 bg-slate-900/50">
                                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500 shadow-sm shadow-orange-500/50"></span>
                                        <span class="text-[11px]">Cần dọn dẹp</span>
                                    </button>
                                    <button type="button" onclick="setQuickRoomStatus('maintenance')" id="btn-quick-maintenance" class="py-2 px-2 rounded-xl font-bold border transition-all text-center flex flex-col items-center gap-1 hover:border-slate-500 hover:bg-slate-500/10 text-slate-400 border-slate-800 bg-slate-900/50">
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-500 shadow-sm shadow-slate-500/50"></span>
                                        <span class="text-[11px]">Bảo trì</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Resident details (chỉ hiện khi phòng có người ở) -->
                            <div class="space-y-2 hidden" id="modal-resident-details">
                                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Thông tin cư dân</h3>
                                <div class="p-3.5 rounded-2xl bg-slate-900/50 border border-slate-800/60 space-y-2">
                                    <div class="flex justify-between text-xs"><span class="text-slate-500">Họ tên:</span> <strong class="text-slate-200" id="modal-resident-name">-</strong></div>
                                    <div class="flex justify-between text-xs"><span class="text-slate-500">Số điện thoại:</span> <strong class="text-slate-200" id="modal-resident-phone">-</strong></div>
                                    <div class="flex justify-between text-xs"><span class="text-slate-500">Bắt đầu ở:</span> <strong class="text-slate-200">01/03/2025</strong></div>
                                </div>
                            </div>

                            <!-- Billing summary (chỉ hiện khi phòng có hóa đơn) -->
                            <div class="space-y-2 hidden" id="modal-billing-details">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hóa đơn tháng gần nhất</h3>
                                    <span class="text-[10px] text-amber-400 font-bold" id="modal-bill-status">Chưa thanh toán</span>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-slate-900/50 border border-slate-800/60 space-y-2">
                                    <div class="flex justify-between text-xs"><span class="text-slate-500">Tiền thuê phòng:</span> <strong class="text-slate-200" id="modal-bill-rent">0đ</strong></div>
                                    <div class="flex justify-between text-xs"><span class="text-slate-500">Tiền điện:</span> <strong class="text-slate-200" id="modal-bill-electric">0đ</strong></div>
                                    <div class="flex justify-between text-xs"><span class="text-slate-500">Tiền nước:</span> <strong class="text-slate-200" id="modal-bill-water">0đ</strong></div>
                                    <div class="flex justify-between text-xs"><span class="text-slate-500">Dịch vụ (Mạng, vệ sinh):</span> <strong class="text-slate-200">150.000đ</strong></div>
                                    <hr class="border-slate-800/60 my-2">
                                    <div class="flex justify-between text-sm"><strong class="text-indigo-400">Tổng thanh toán:</strong> <strong class="text-indigo-400 font-extrabold" id="modal-bill-total">0đ</strong></div>
                                </div>
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="space-y-2.5 pt-3 border-t border-slate-800/60">
                            <form id="modal-pay-form" action="" method="POST" class="hidden">
                                @csrf
                            </form>
                            <form id="modal-notify-form" action="" method="POST" class="hidden">
                                @csrf
                            </form>
                            <button id="modal-btn-pay" onclick="submitModalPay()" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-lg shadow-emerald-600/30 transition-all hidden">
                                <i class="fa-solid fa-circle-check"></i> Xác nhận đã đóng tiền
                            </button>
                            <button id="modal-btn-action" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 transition-all hidden">
                                <i class="fa-solid fa-bell-slash"></i> Gửi nhắc nợ qua Zalo/Mail
                            </button>
                            <button id="modal-btn-qr" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-semibold text-slate-300 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all hidden">
                                <i class="fa-solid fa-qrcode text-indigo-400"></i> Xem mã VietQR hóa đơn
                            </button>
                            <button id="modal-btn-print" onclick="printModalInvoice()" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-semibold text-slate-300 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all hidden">
                                <i class="fa-solid fa-print text-indigo-400"></i> In hóa đơn / Xuất PDF
                            </button>
                            <button type="button" onclick="closeRoomDetail()" class="w-full flex items-center justify-center gap-2 py-2 px-4 rounded-xl text-xs font-semibold text-slate-400 bg-slate-900/40 hover:bg-slate-900 border border-slate-800/60 hover:border-slate-700 transition-all">
                                Đóng lại
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 3: UTILITY RECORD INPUT -->
            <section id="utility-section" class="tab-content hidden space-y-8 animate-fade-in">
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200">Chốt Điện Nước Cuối Tháng</h3>
                            <p class="text-xs text-slate-500">Nhập chỉ số điện nước tháng 06/2026. Đơn giá: Điện 3.500đ/kWh, Nước 15.000đ/m3.</p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <button type="button" onclick="openIotDashboardModal()" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:opacity-90 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-tower-broadcast animate-pulse"></i> 📡 IoT Smart Metering (Realtime)
                            </button>
                            <button type="button" onclick="triggerIotAutoSync(this)" class="px-4 py-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 hover:opacity-90 text-white rounded-xl text-xs font-bold shadow-lg shadow-cyan-600/20 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-bolt-lightning"></i> ⚡ Chốt Số Tự Động Từ IoT
                            </button>
                            <button type="button" onclick="triggerAutoRemind(this)" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-rose-600/20 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-bell animate-bounce"></i> Nhắc Nợ Zalo Hàng Loạt
                            </button>
                            <button type="button" onclick="openBulkOcrModal('electricity')" class="px-4 py-2.5 bg-gradient-to-r from-amber-500 via-orange-500 to-rose-500 hover:opacity-90 text-white rounded-xl text-xs font-bold shadow-lg shadow-orange-500/25 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-camera"></i> ⚡ AI Quét Hàng Loạt
                            </button>
                            <button type="submit" form="bulk-utility-form" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/20 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-check-double"></i> Lưu & Xuất Hóa Đơn
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <form id="bulk-utility-form" action="{{ route('smartroom.admin.utility.bulk_store') }}" method="POST">
                            @csrf
                            <table class="w-full text-left text-sm text-slate-300">
                                <thead class="text-xs text-slate-500 uppercase bg-slate-900/50 border-b border-slate-900">
                                    <tr>
                                        <th class="px-6 py-4 font-bold">Phòng & Số SX Công Tơ</th>
                                        <th class="px-6 py-4 font-bold">Điện cũ</th>
                                        <th class="px-6 py-4 font-bold">Điện mới (kWh)</th>
                                        <th class="px-6 py-4 font-bold">Nước cũ</th>
                                        <th class="px-6 py-4 font-bold">Nước mới (m3)</th>
                                        <th class="px-6 py-4 font-bold">Số lượng xài</th>
                                        <th class="px-6 py-4 font-bold">Thành tiền tạm tính</th>
                                        <th class="px-6 py-4 font-bold text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-900" id="utility-table-body">
                                    @foreach($utilityRooms as $room)
                                    @php
                                        $resident = $room->residents->first();
                                        $latestBill = $room->utilityRecords->first();
                                        $currentMonth = \Carbon\Carbon::now()->format('Y-m');
                                        
                                        if ($latestBill && $latestBill->billing_month === $currentMonth) {
                                            $oldElec = $latestBill->old_electricity;
                                            $oldWater = $latestBill->old_water;
                                            $newElec = $latestBill->new_electricity;
                                            $newWater = $latestBill->new_water;
                                        } else {
                                            $oldElec = $latestBill ? $latestBill->new_electricity : 0;
                                            $oldWater = $latestBill ? $latestBill->new_water : 0;
                                            $newElec = '';
                                            $newWater = '';
                                        }
                                        $statusColor = $room->status === 'overdue' ? 'bg-amber-500' : ($room->status === 'empty' ? 'bg-emerald-500' : 'bg-red-500');
                                    @endphp
                                    <tr class="hover:bg-slate-900/10 transition-all" data-room-id="{{ $room->id }}" data-room="{{ $room->room_number }}" data-price="{{ $room->price }}" data-electric-serial="{{ $room->electric_meter_serial ?? '' }}" data-water-serial="{{ $room->water_meter_serial ?? '' }}">
                                        <td class="px-6 py-4 text-slate-200">
                                            <div class="flex items-center gap-2 font-bold">
                                                <span class="w-2.5 h-2.5 rounded-full {{ $statusColor }}"></span> 
                                                <span>{{ $room->room_number }} ({{ $resident ? $resident->name : 'N/A' }})</span>
                                                @if($room->latestElectricTelemetry || $room->latestWaterTelemetry)
                                                    <button type="button" onclick="viewRoomIotChart('{{ $room->id }}', '{{ $room->room_number }}')" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-500/10 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/20 text-[9px] font-semibold transition-all shadow-sm" title="Công tơ thông minh IoT chu kỳ 15 phút. Bấm để xem đồ thị phụ tải">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> IoT Online
                                                    </button>
                                                @endif
                                            </div>
                                            <div class="text-[10px] text-slate-400 flex items-center gap-1.5 mt-1 font-mono flex-wrap">
                                                @if($room->electric_meter_serial)
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20" title="Số SX Công tơ Điện: {{ $room->electric_meter_serial }}">
                                                        <i class="fa-solid fa-bolt"></i> {{ $room->electric_meter_serial }}
                                                    </span>
                                                @endif
                                                @if($room->water_meter_serial)
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20" title="Số SX Đồng hồ Nước: {{ $room->water_meter_serial }}">
                                                        <i class="fa-solid fa-droplet"></i> {{ $room->water_meter_serial }}
                                                    </span>
                                                @endif
                                                @if(!$room->electric_meter_serial && !$room->water_meter_serial)
                                                    <a href="{{ route('admin.rooms.edit', $room->id) }}" class="text-[9px] text-indigo-400 hover:underline italic" title="Bấm vào đây để cài đặt Số SX công tơ cho phòng">+ Cài Số SX</a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-500" data-field="old-elec">{{ $oldElec }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-1.5">
                                                <input type="number" name="utilities[{{ $room->id }}][new_electricity]" value="{{ $newElec }}" oninput="calculateRowCost(this)" class="new-elec-input w-24 px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none transition-colors" placeholder="Số mới">
                                                @if($room->latestElectricTelemetry)
                                                    <button type="button" onclick="applyIotReadingToInput(this, '{{ (int) round($room->latestElectricTelemetry->reading) }}')" class="w-7 h-7 flex items-center justify-center rounded-lg bg-emerald-500/10 hover:bg-emerald-600 text-emerald-400 hover:text-white border border-emerald-500/20 text-xs transition-all shadow-sm" title="Lấy số điện từ IoT Smart Meter: {{ $room->latestElectricTelemetry->reading }} kWh">
                                                        <i class="fa-solid fa-bolt-lightning"></i>
                                                    </button>
                                                @endif
                                                <button type="button" onclick="openMeterOcrModal('{{ $room->id }}', '{{ $room->room_number }}', 'electricity', this)" class="w-7 h-7 flex items-center justify-center rounded-lg bg-indigo-500/10 hover:bg-indigo-600 text-indigo-400 hover:text-white border border-indigo-500/20 text-xs transition-all shadow-sm" title="Quét số điện bằng AI OCR Camera">
                                                    <i class="fa-solid fa-camera"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-500" data-field="old-water">{{ $oldWater }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-1.5">
                                                <input type="number" name="utilities[{{ $room->id }}][new_water]" value="{{ $newWater }}" oninput="calculateRowCost(this)" class="new-water-input w-24 px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none transition-colors" placeholder="Số mới">
                                                @if($room->latestWaterTelemetry)
                                                    <button type="button" onclick="applyIotReadingToInput(this, '{{ (int) round($room->latestWaterTelemetry->reading) }}')" class="w-7 h-7 flex items-center justify-center rounded-lg bg-cyan-500/10 hover:bg-cyan-600 text-cyan-400 hover:text-white border border-cyan-500/20 text-xs transition-all shadow-sm" title="Lấy số nước từ IoT Smart Meter: {{ $room->latestWaterTelemetry->reading }} m3">
                                                        <i class="fa-solid fa-droplet"></i>
                                                    </button>
                                                @endif
                                                <button type="button" onclick="openMeterOcrModal('{{ $room->id }}', '{{ $room->room_number }}', 'water', this)" class="w-7 h-7 flex items-center justify-center rounded-lg bg-cyan-500/10 hover:bg-cyan-600 text-cyan-400 hover:text-white border border-cyan-500/20 text-xs transition-all shadow-sm" title="Quét số nước bằng AI OCR Camera">
                                                    <i class="fa-solid fa-camera"></i>
                                                </button>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-xs text-slate-400">
                                            <div class="flex items-center gap-1.5">
                                                <span>⚡ Điện: <strong data-field="used-elec">0</strong> kWh</span>
                                                <span class="abnormal-elec-warning hidden text-amber-400 font-bold text-[10px] items-center gap-1 bg-amber-500/10 border border-amber-500/20 px-1.5 py-0.5 rounded" title="Lượng điện tiêu thụ tăng đột biến (> 1000 kWh)!">
                                                    <i class="fa-solid fa-triangle-exclamation animate-bounce"></i> Bất thường
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span>💧 Nước: <strong data-field="used-water">0</strong> m3</span>
                                                <span class="abnormal-water-warning hidden text-amber-400 font-bold text-[10px] items-center gap-1 bg-amber-500/10 border border-amber-500/20 px-1.5 py-0.5 rounded" title="Lượng nước tiêu thụ tăng đột biến (> 100 m3)!">
                                                    <i class="fa-solid fa-triangle-exclamation animate-bounce"></i> Bất thường
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-indigo-400 font-bold text-xs" data-field="cost-total">0đ</td>
                                        <td class="px-6 py-4 text-center">
                                             <div class="flex items-center justify-center gap-2">
                                                 @if($room->status === 'overdue' && $latestBill && $latestBill->status !== 'paid')
                                                     <form action="{{ route('smartroom.admin.utility.pay', $latestBill->id) }}" method="POST" class="inline">
                                                         @csrf
                                                         <button type="submit" class="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white rounded-lg text-xs font-bold border border-emerald-500/20 transition-all flex items-center gap-1">
                                                             <i class="fa-solid fa-check"></i> Xác nhận đóng
                                                         </button>
                                                     </form>
                                                     <a href="{{ route('smartroom.admin.utility.print', $latestBill->id) }}" target="_blank" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-bold border border-slate-700 transition-all flex items-center gap-1" title="In hóa đơn">
                                                         <i class="fa-solid fa-print"></i> In
                                                     </a>
                                                 @else
                                                     <button type="button" onclick="saveSingleUtility('{{ $room->id }}', this)" class="px-3 py-1.5 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-400 hover:text-white rounded-lg text-xs font-bold border border-indigo-500/20 transition-all">
                                                         <i class="fa-solid fa-save"></i> Lưu số
                                                     </button>
                                                 @endif
                                             </div>
                                         </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>

                <!-- MODAL IOT SMART METERING & REALTIME DASHBOARD -->
                <div id="iot-metering-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/85 backdrop-blur-md p-4 overflow-y-auto">
                    <div class="relative w-full max-w-5xl bg-slate-900/95 border border-slate-800 rounded-3xl p-6 shadow-2xl space-y-6 my-8 text-slate-200">
                        <!-- Header -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-5">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 via-teal-500 to-cyan-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/25">
                                    <i class="fa-solid fa-tower-broadcast text-xl animate-pulse"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-base font-bold text-slate-100">IoT Smart Metering & Giám Sát Thời Gian Thực</h3>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Chu kỳ 15 phút/lần
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">Tích hợp công tơ điện tử thông minh truyền không dây qua LoRaWAN / ESP32 WiFi / Modbus RS485 / Zigbee</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="loadIotSummary()" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold border border-slate-700 transition-all flex items-center gap-1.5" title="Tải lại dữ liệu">
                                    <i class="fa-solid fa-arrows-rotate"></i> Làm mới
                                </button>
                                <button type="button" onclick="closeIotDashboardModal()" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-slate-200 border border-slate-700 flex items-center justify-center transition-all">
                                    <i class="fa-solid fa-xmark text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- KPI Summary Cards -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5">
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                                <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                                    <span>Công Suất Tức Thời</span>
                                    <i class="fa-solid fa-bolt text-amber-400"></i>
                                </div>
                                <div class="text-2xl font-black text-amber-300 font-mono" id="iot-kpi-power">0.00 <span class="text-xs font-bold text-slate-400">kW</span></div>
                                <div class="text-[10px] text-slate-500 mt-1">Toàn bộ phòng trọ</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                                <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                                    <span>Lưu Lượng Nước</span>
                                    <i class="fa-solid fa-droplet text-cyan-400"></i>
                                </div>
                                <div class="text-2xl font-black text-cyan-300 font-mono" id="iot-kpi-water">0.00 <span class="text-xs font-bold text-slate-400">m3/h</span></div>
                                <div class="text-[10px] text-slate-500 mt-1">Lưu lượng tức thời</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                                <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                                    <span>Công Tơ Đang Kết Nối</span>
                                    <i class="fa-solid fa-wifi text-emerald-400"></i>
                                </div>
                                <div class="text-2xl font-black text-emerald-300 font-mono" id="iot-kpi-devices">0 / 0</div>
                                <div class="text-[10px] text-emerald-400/80 mt-1" id="iot-kpi-status-hint">Trạng thái Online</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                                <div class="flex items-center justify-between text-slate-400 text-xs mb-1">
                                    <span>Cảnh Báo Dị Thường</span>
                                    <i class="fa-solid fa-triangle-exclamation text-rose-400"></i>
                                </div>
                                <div class="text-2xl font-black text-rose-400 font-mono" id="iot-kpi-warnings">0</div>
                                <div class="text-[10px] text-slate-500 mt-1">Quá tải / Rò rỉ nước</div>
                            </div>
                        </div>

                        <!-- Tab Navigation -->
                        <div class="flex items-center gap-2 border-b border-slate-800 pb-1">
                            <button type="button" onclick="switchIotTab('realtime')" id="iot-tab-btn-realtime" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-emerald-500/10 text-emerald-300 border border-emerald-500/30">
                                <i class="fa-solid fa-chart-line"></i> Biểu Đồ Phụ Tải 24h
                            </button>
                            <button type="button" onclick="switchIotTab('devices')" id="iot-tab-btn-devices" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-slate-200 border border-transparent">
                                <i class="fa-solid fa-microchip"></i> Danh Sách Công Tơ IoT
                            </button>
                            <button type="button" onclick="switchIotTab('simulator')" id="iot-tab-btn-simulator" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-slate-200 border border-transparent">
                                <i class="fa-solid fa-flask-vial"></i> Trình Giả Lập Phát Sóng (Simulator)
                            </button>
                        </div>

                        <!-- Tab 1: Realtime Load Curves -->
                        <div id="iot-tab-realtime" class="space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-950/50 p-3 rounded-2xl border border-slate-800/80">
                                <div class="flex items-center gap-2">
                                    <label class="text-xs text-slate-400 font-semibold">Chọn phòng:</label>
                                    <select id="iot-room-filter-select" onchange="onIotRoomFilterChange(this.value)" class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none">
                                        <option value="">-- Chọn phòng xem biểu đồ --</option>
                                        @foreach($utilityRooms as $r)
                                            <option value="{{ $r->id }}">Phòng {{ $r->room_number }} ({{ $r->residents->first()?->name ?? 'Trống' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-400">Khung thời gian:</span>
                                    <button type="button" onclick="changeIotChartRange('24h')" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-800 text-slate-200 hover:bg-slate-700 border border-slate-700">24 Giờ</button>
                                    <button type="button" onclick="changeIotChartRange('7d')" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-900 text-slate-400 hover:bg-slate-800 border border-slate-800">7 Ngày</button>
                                </div>
                            </div>

                            <!-- Interactive Chart Canvas -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                        <h4 class="text-xs font-bold text-slate-300" id="iot-chart-title">Đường Cong Phụ Tải Điện (W) & Lưu Lượng Nước (L/phút) Theo Chu Kỳ 15 Phút</h4>
                                    </div>
                                    <div class="flex items-center gap-3 text-[11px]">
                                        <span class="inline-flex items-center gap-1 text-amber-400"><span class="w-2.5 h-0.5 bg-amber-400"></span> Công suất điện (W)</span>
                                        <span class="inline-flex items-center gap-1 text-cyan-400"><span class="w-2.5 h-0.5 bg-cyan-400"></span> Nước (L/m)</span>
                                    </div>
                                </div>

                                <div class="relative w-full h-64 bg-slate-900/40 rounded-xl border border-slate-800/50 p-2 flex items-center justify-center">
                                    <canvas id="iot-realtime-chart-canvas" class="w-full h-full"></canvas>
                                    <div id="iot-chart-empty-state" class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-slate-500 text-xs">
                                        <i class="fa-solid fa-chart-area text-3xl opacity-40"></i>
                                        <span>Vui lòng chọn 1 phòng bên trên hoặc dùng Trình Giả Lập để phát sóng dữ liệu mẫu</span>
                                    </div>
                                </div>

                                <!-- Current Room Stats Summary -->
                                <div id="iot-room-stats-banner" class="hidden grid grid-cols-3 gap-3 pt-2 text-xs">
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 block text-[10px]">Chỉ số điện tích lũy:</span>
                                        <strong id="iot-stat-elec-val" class="text-emerald-400 font-mono text-sm">0 kWh</strong>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 block text-[10px]">Chỉ số nước tích lũy:</span>
                                        <strong id="iot-stat-water-val" class="text-cyan-400 font-mono text-sm">0 m3</strong>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 block text-[10px]">Ước tính tiêu thụ hôm nay:</span>
                                        <strong id="iot-stat-cost-val" class="text-indigo-400 font-mono text-sm">0đ</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Devices Fleet -->
                        <div id="iot-tab-devices" class="hidden space-y-4">
                            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                                <table class="w-full text-left text-xs text-slate-300">
                                    <thead class="bg-slate-950/70 text-[11px] text-slate-400 uppercase border-b border-slate-800">
                                        <tr>
                                            <th class="px-4 py-3">Mã Thiết Bị</th>
                                            <th class="px-4 py-3">Giao Thức</th>
                                            <th class="px-4 py-3">Phòng Gán</th>
                                            <th class="px-4 py-3">Số SX Công Tơ</th>
                                            <th class="px-4 py-3">Loại</th>
                                            <th class="px-4 py-3">Chỉ Số Gần Nhất</th>
                                            <th class="px-4 py-3">Trạng Thái</th>
                                            <th class="px-4 py-3">Lần Cuối Nhận Tin</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60" id="iot-devices-table-body">
                                        <tr>
                                            <td colspan="8" class="text-center py-6 text-slate-500">Đang tải danh sách thiết bị...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 3: Hardware Simulator -->
                        <div id="iot-tab-simulator" class="hidden space-y-4">
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-4">
                                <div class="flex items-center gap-2 text-xs font-bold text-amber-300">
                                    <i class="fa-solid fa-flask text-sm"></i>
                                    <span>Bộ Giả Lập Phát Sóng Gói Tin IoT (Hardware Telemetry Simulator)</span>
                                </div>
                                <p class="text-xs text-slate-400">Tính năng này cho phép bạn giả lập một vi điều khiển (ESP32 / LoRaWAN Node) gửi gói tin đo đạc về máy chủ theo chu kỳ 15 phút để kiểm thử hệ thống ngay trên trình duyệt mà không cần phần cứng thật.</p>

                                <form id="iot-simulator-form" onsubmit="sendIotSimulation(event)" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                    <div>
                                        <label class="block text-slate-400 mb-1 font-semibold">Phòng nhận dữ liệu <span class="text-rose-400">*</span></label>
                                        <select name="room_id" id="sim-room-id" required class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 focus:border-emerald-500 focus:outline-none">
                                            @foreach($utilityRooms as $r)
                                                <option value="{{ $r->id }}">Phòng {{ $r->room_number }} (Số SX Điện: {{ $r->electric_meter_serial ?: 'Chưa có' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 mb-1 font-semibold">Loại công tơ</label>
                                        <select name="meter_type" id="sim-meter-type" onchange="onSimMeterTypeChange(this.value)" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 focus:border-emerald-500 focus:outline-none">
                                            <option value="electricity">⚡ Điện (PZEM-004T / Modbus)</option>
                                            <option value="water">💧 Nước (Pulse Counter / Ultrasonic)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 mb-1 font-semibold">Giao thức truyền thông</label>
                                        <select name="protocol" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 focus:border-emerald-500 focus:outline-none">
                                            <option value="esp32_wifi">ESP32 (WiFi / HTTP Webhook)</option>
                                            <option value="lorawan">LoRaWAN Long-Range 868MHz</option>
                                            <option value="modbus_rs485">Modbus RS485 Bus RTU</option>
                                            <option value="zigbee">Zigbee 3.0 Mesh</option>
                                            <option value="mqtt">MQTT Broker Ingestion</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-slate-400 mb-1 font-semibold">Chỉ số tích lũy mới (kWh hoặc m3)</label>
                                        <input type="number" step="0.01" name="reading" id="sim-reading" value="1420.5" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 focus:border-emerald-500 focus:outline-none font-mono" placeholder="Chỉ số công tơ">
                                    </div>
                                    <div id="sim-elec-inputs" class="contents">
                                        <div>
                                            <label class="block text-slate-400 mb-1 font-semibold">Điện áp tức thời (V)</label>
                                            <input type="number" step="0.1" name="voltage" value="221.8" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 focus:border-emerald-500 focus:outline-none font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-400 mb-1 font-semibold">Công suất tức thời (W)</label>
                                            <input type="number" step="1" name="power" value="780" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 focus:border-emerald-500 focus:outline-none font-mono">
                                        </div>
                                    </div>
                                    <div id="sim-water-inputs" class="hidden">
                                        <label class="block text-slate-400 mb-1 font-semibold">Lưu lượng nước tức thời (L/phút)</label>
                                        <input type="number" step="0.1" name="flow_rate" value="1.8" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 focus:border-cyan-500 focus:outline-none font-mono">
                                    </div>

                                    <div class="md:col-span-3 flex justify-end pt-2">
                                        <button type="submit" id="btn-send-sim" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:opacity-90 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-600/25 transition-all flex items-center gap-2">
                                            <i class="fa-solid fa-satellite-dish"></i> 🚀 Phát Tín Hiệu Telemetry Lên Server
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-slate-800">
                            <span class="text-xs text-slate-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-400"></i> Máy chủ Ingestion sẵn sàng nhận gói tin tại: <code class="bg-slate-950 px-2 py-0.5 rounded font-mono text-[11px] text-emerald-300">/api/v1/iot/telemetry</code>
                            </span>
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <button type="button" onclick="closeIotDashboardModal()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all">
                                    Đóng lại
                                </button>
                                <button type="button" onclick="triggerIotAutoSync(this)" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:opacity-90 text-white text-xs font-bold shadow-lg shadow-cyan-600/20 transition-all flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-bolt-lightning"></i> ⚡ Chốt Số Tự Động Sang Hóa Đơn
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL AI OCR CAMERA QUÉT CÔNG TƠ ĐIỆN NƯỚC -->

                <div id="meter-ocr-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-md hidden flex items-center justify-center p-4 transition-all duration-300">
                    <div class="glass-card w-full max-w-lg rounded-3xl border border-slate-800 p-6 space-y-5 shadow-2xl relative animate-fade-in bg-[#0a0f1d]/95">
                        <!-- Header -->
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500/20 via-purple-500/20 to-pink-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 shadow-inner">
                                    <i class="fa-solid fa-wand-magic-sparkles text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">
                                        AI OCR Quét Công Tơ
                                        <span id="ocr-meter-type-badge" class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Điện</span>
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Phòng <strong id="ocr-target-room-text" class="text-slate-200">101</strong> — Google Gemini Vision AI bóc tách chỉ số</p>
                                </div>
                            </div>
                            <button type="button" onclick="closeMeterOcrModal()" class="w-8 h-8 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Source Switch Tabs -->
                        <div class="grid grid-cols-2 gap-2 p-1 rounded-xl bg-slate-950 border border-slate-900">
                            <button type="button" id="tab-btn-camera" onclick="switchOcrSource('camera')" class="py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 bg-indigo-600 text-white shadow-sm">
                                <i class="fa-solid fa-camera"></i> Camera Trực Tiếp
                            </button>
                            <button type="button" id="tab-btn-upload" onclick="switchOcrSource('upload')" class="py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 text-slate-400 hover:text-slate-200">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Tải Ảnh Có Sẵn
                            </button>
                        </div>

                        <!-- Viewport Box: Camera Stream & Image Preview -->
                        <div class="relative w-full rounded-2xl bg-slate-950 border border-slate-800/80 overflow-hidden flex items-center justify-center min-h-[220px]">
                            <!-- Video Stream -->
                            <video id="ocr-camera-video" autoplay playsinline class="w-full h-56 object-cover hidden"></video>

                            <!-- Image Preview -->
                            <img id="ocr-image-preview" src="" alt="Meter Preview" class="w-full h-56 object-contain hidden">

                            <!-- Upload Zone -->
                            <div id="ocr-upload-zone" onclick="document.getElementById('ocr-file-input').click()" class="p-8 text-center cursor-pointer hover:bg-slate-900/30 transition-all flex flex-col items-center justify-center gap-2 hidden w-full h-56">
                                <div class="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-image text-xl"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-300">Bấm vào đây để chọn ảnh chụp công tơ</p>
                                <p class="text-[10px] text-slate-500">Hỗ trợ định dạng JPG, PNG, WEBP (Tối đa 5MB)</p>
                            </div>

                            <!-- Laser Scanning Animation -->
                            <div id="ocr-laser-line" class="hidden absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-cyan-400 to-transparent shadow-[0_0_20px_#22d3ee] animate-pulse"></div>

                            <!-- AI Processing Overlay -->
                            <div id="ocr-loading-overlay" class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm hidden flex flex-col items-center justify-center gap-3">
                                <div class="w-10 h-10 border-3 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                                <span class="text-xs font-bold text-slate-200">Gemini Vision AI đang đọc chỉ số công tơ...</span>
                                <span class="text-[10px] text-indigo-400">Quét số nguyên từ mặt đồng hồ</span>
                            </div>

                            <!-- Camera Permission / Error Warning -->
                            <div id="ocr-camera-error" class="p-6 text-center hidden flex flex-col items-center justify-center gap-2">
                                <i class="fa-solid fa-video-slash text-2xl text-rose-400 mb-1"></i>
                                <p class="text-xs font-bold text-slate-300">Không thể mở Camera thiết bị</p>
                                <p class="text-[10px] text-slate-500 max-w-xs">Vui lòng cấp quyền truy cập camera trên trình duyệt hoặc chuyển sang tab <strong>Tải Ảnh Có Sẵn</strong>.</p>
                            </div>
                        </div>

                        <!-- Hidden Form Elements -->
                        <canvas id="ocr-capture-canvas" class="hidden"></canvas>
                        <input type="file" id="ocr-file-input" accept="image/*" class="hidden" onchange="handleOcrFileSelected(this)">

                        <!-- Intermediate Actions: Capture & Retake -->
                        <div class="flex gap-2">
                            <button type="button" id="btn-ocr-capture" onclick="captureOcrSnapshot()" class="flex-1 py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/25">
                                <i class="fa-solid fa-camera"></i> Chụp Ảnh Mặt Đồng Hồ
                            </button>
                            <button type="button" id="btn-ocr-retake" onclick="resetOcrCapture()" class="hidden py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 text-xs font-bold transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-rotate-left"></i> Chụp Lại
                            </button>
                            <button type="button" id="btn-ocr-analyze" onclick="submitOcrAnalysis()" class="hidden flex-1 py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:opacity-90 text-white text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-purple-600/25">
                                <i class="fa-solid fa-sparkles"></i> Phân Tích Với AI Gemini
                            </button>
                        </div>

                        <!-- Result Display Box -->
                        <div id="ocr-result-box" class="hidden p-4 rounded-2xl bg-slate-950/80 border border-emerald-500/30 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Kết Quả Phân Tích</span>
                                <span id="ocr-confidence-badge" class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    Độ tin cậy: 95%
                                </span>
                            </div>
                            <div class="flex items-baseline justify-between">
                                <span class="text-xs text-slate-500">Chỉ số công tơ đọc được:</span>
                                <div class="flex items-baseline gap-1">
                                    <span id="ocr-result-value" class="text-3xl font-extrabold text-emerald-400 font-mono tracking-tight">0</span>
                                    <span id="ocr-result-unit" class="text-xs font-bold text-slate-400">kWh</span>
                                </div>
                            </div>
                            <div id="ocr-fallback-hint" class="hidden p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-[10px] text-amber-300">
                                <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                                <span>AI nhận diện độ nét chưa tối ưu. Vui lòng kiểm tra lại ảnh chụp hoặc chỉnh tay nếu cần.</span>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex gap-2 pt-2 border-t border-slate-900">
                            <button type="button" onclick="closeMeterOcrModal()" class="w-1/3 py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 text-xs font-bold transition-all">
                                Đóng lại
                            </button>
                            <button type="button" id="btn-ocr-apply" onclick="applyOcrResultToInput()" disabled class="flex-1 py-2.5 px-4 rounded-xl bg-emerald-600 disabled:bg-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed hover:bg-emerald-500 text-white text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/20">
                                <i class="fa-solid fa-check"></i> Áp Dụng Chỉ Số Vào Bảng
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal: AI Bulk OCR Scanning (Quét Hàng Loạt & Khớp Phòng) -->
                <div id="bulk-meter-ocr-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-md p-4 overflow-y-auto">
                    <div class="relative w-full max-w-4xl bg-slate-900/95 border border-slate-800 rounded-3xl p-6 shadow-2xl space-y-5 my-8">
                        <!-- Header -->
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 flex items-center justify-center text-white shadow-lg shadow-orange-500/25">
                                    <i class="fa-solid fa-bolt-lightning text-lg"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-bold text-slate-100">AI Quét Hàng Loạt & Khớp Phòng Tự Động</h3>
                                        <span id="bulk-ocr-type-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">Điện (kWh)</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">Tải lên nhiều ảnh công tơ một lượt. Gemini Vision AI sẽ bóc tách Số SX và Chỉ số để tự động điền vào từng phòng.</p>
                                </div>
                            </div>
                            <button type="button" onclick="closeBulkOcrModal()" class="w-8 h-8 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Loại công tơ: Điện hoặc Nước -->
                        <div class="flex items-center justify-between gap-4 p-3 rounded-2xl bg-slate-950 border border-slate-800/60">
                            <div class="text-xs font-semibold text-slate-300 flex items-center gap-2">
                                <i class="fa-solid fa-filter text-indigo-400"></i> Loại công tơ đang quét:
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" id="bulk-type-btn-electricity" onclick="setBulkOcrType('electricity')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-sm">
                                    <i class="fa-solid fa-bolt"></i> Công Tơ Điện
                                </button>
                                <button type="button" id="bulk-type-btn-water" onclick="setBulkOcrType('water')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-slate-900 text-slate-400 border border-slate-800 hover:text-slate-200">
                                    <i class="fa-solid fa-droplet"></i> Đồng Hồ Nước
                                </button>
                            </div>
                        </div>

                        <!-- Khu vực tải nhiều ảnh (Dropzone) -->
                        <input type="file" id="bulk-ocr-file-input" multiple accept="image/*" class="hidden" onchange="handleBulkOcrFilesSelected(this)">
                        
                        <div id="bulk-ocr-dropzone" onclick="document.getElementById('bulk-ocr-file-input').click()" class="border-2 border-dashed border-slate-700 hover:border-indigo-500/60 rounded-2xl p-6 text-center cursor-pointer transition-all bg-slate-950/50 hover:bg-slate-950/80 group">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 group-hover:bg-indigo-500/20 border border-indigo-500/20 flex items-center justify-center text-indigo-400 transition-all shadow-inner">
                                    <i class="fa-solid fa-cloud-arrow-up text-2xl group-hover:scale-110 transition-transform"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-200">Nhấn vào đây hoặc kéo thả nhiều ảnh công tơ vào đây</p>
                                <p class="text-xs text-slate-400 max-w-md">Hỗ trợ định dạng JPG, PNG, WEBP (chụp từ điện thoại, tối đa 30 ảnh một lượt). AI sẽ tự xoay, phóng to mặt số và trích xuất.</p>
                            </div>
                        </div>

                        <!-- Trạng thái ảnh đã chọn & Nút kích hoạt AI -->
                        <div id="bulk-ocr-files-bar" class="hidden flex items-center justify-between p-3 rounded-2xl bg-slate-950 border border-slate-800">
                            <div class="flex items-center gap-2 text-xs">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                <span class="text-slate-300 font-semibold">Đã nạp <strong id="bulk-files-count" class="text-emerald-400 font-mono text-sm">0</strong> ảnh công tơ</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="clearBulkOcrFiles()" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 text-xs font-semibold transition-all">
                                    <i class="fa-solid fa-trash-can mr-1"></i> Chọn lại
                                </button>
                                <button type="button" id="btn-start-bulk-ocr" onclick="startBulkOcrAnalysis()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:opacity-90 text-white text-xs font-bold transition-all shadow-lg shadow-purple-600/25 flex items-center gap-1.5">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Bắt Đầu Quét AI Tự Động
                                </button>
                            </div>
                        </div>

                        <!-- Hiệu ứng quét và thanh tiến trình -->
                        <div id="bulk-ocr-progress-box" class="hidden p-4 rounded-2xl bg-slate-950 border border-indigo-500/30 space-y-3">
                            <div class="flex items-center justify-between text-xs font-bold">
                                <span class="text-indigo-300 flex items-center gap-2">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i> Đang phân tích qua Google Gemini Vision AI...
                                </span>
                                <span id="bulk-ocr-progress-text" class="text-slate-400 font-mono">0%</span>
                            </div>
                            <div class="w-full bg-slate-900 rounded-full h-2.5 overflow-hidden border border-slate-800">
                                <div id="bulk-ocr-progress-bar" class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p class="text-[11px] text-slate-400 italic">Hệ thống đang bóc tách Số SX và Chỉ số từng ảnh, đồng thời đối chiếu với các phòng trong nhà trọ của bạn...</p>
                        </div>

                        <!-- Bảng kết quả phân tích & đối chiếu (Review Table) -->
                        <div id="bulk-ocr-results-wrapper" class="hidden space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-200 uppercase tracking-wider">Kết Quả Khớp Dữ Liệu</span>
                                    <span id="bulk-match-stats-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Khớp thành công 0/0
                                    </span>
                                </div>
                                <span class="text-[11px] text-slate-400">Bạn có thể chỉnh sửa lại phòng hoặc chỉ số trước khi chốt</span>
                            </div>

                            <div class="max-h-72 overflow-y-auto rounded-2xl border border-slate-800 bg-slate-950/60 divide-y divide-slate-800/60">
                                <table class="w-full text-left text-xs text-slate-300">
                                    <thead class="text-[11px] text-slate-400 uppercase bg-slate-900/80 sticky top-0 backdrop-blur-sm z-10 border-b border-slate-800">
                                        <tr>
                                            <th class="px-4 py-3">Ảnh</th>
                                            <th class="px-4 py-3">Số SX AI Đọc</th>
                                            <th class="px-4 py-3">Phòng Khớp Được</th>
                                            <th class="px-4 py-3">Chỉ Số Mới</th>
                                            <th class="px-4 py-3">Trạng Thái</th>
                                            <th class="px-4 py-3 text-center">Bỏ qua</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bulk-ocr-results-body" class="divide-y divide-slate-900">
                                        <!-- Rows rendered dynamically by JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex items-center justify-between pt-3 border-t border-slate-800/80">
                            <button type="button" onclick="closeBulkOcrModal()" class="py-2.5 px-5 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 text-xs font-bold transition-all">
                                Đóng lại
                            </button>
                            <button type="button" id="btn-bulk-ocr-apply" onclick="applyBulkOcrToTable()" disabled class="py-2.5 px-6 rounded-xl bg-emerald-600 disabled:bg-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed hover:bg-emerald-500 text-white text-xs font-bold transition-all flex items-center gap-2 shadow-lg shadow-emerald-600/20">
                                <i class="fa-solid fa-check-double"></i> Áp Dụng Tất Cả Vào Bảng Chốt Số
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 4: RESIDENT MANAGEMENT (Quản lý khách trọ) -->
            <section id="resident-section" class="tab-content hidden space-y-8 animate-fade-in">
                <!-- Stat Cards cho Trạng thái tạm trú -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(99,102,241,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Tổng cư dân</p>
                                <h3 class="text-2xl font-extrabold text-white mt-1">{{ $residentStats->count() }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400"><i class="fa-solid fa-users"></i></div>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(16,185,129,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Đã đăng ký tạm trú</p>
                                <h3 class="text-2xl font-extrabold text-emerald-400 mt-1">{{ $residentStats->where('temporary_residence_status', 'registered')->count() }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400"><i class="fa-solid fa-clipboard-check"></i></div>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(245,158,11,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-amber-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Tạm vắng</p>
                                <h3 class="text-2xl font-extrabold text-amber-400 mt-1">{{ $residentStats->where('temporary_residence_status', 'absent')->count() }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400"><i class="fa-solid fa-user-clock"></i></div>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(239,68,68,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-rose-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Chưa đăng ký</p>
                                <h3 class="text-2xl font-extrabold text-rose-400 mt-1">{{ $residentStats->where('temporary_residence_status', 'none')->count() }}</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-400"><i class="fa-solid fa-user-xmark"></i></div>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-200">Quản Lý Danh Sách Cư Dân</h3>
                            <p class="text-xs text-slate-500">Lưu trữ thông tin cá nhân, đăng ký tạm trú, quản lý người thân tạm trú.</p>
                        </div>
                        <div class="flex gap-2">
                            <form method="GET" action="{{ route('smartroom.admin') }}" class="flex gap-2">
                                <input type="hidden" name="tab" value="resident-section">
                                <input type="search" name="resident_q" value="{{ $residentFilters['q'] }}" class="px-4 py-2 text-xs rounded-xl bg-slate-900 border border-slate-800 text-slate-200 placeholder-slate-500 focus:border-indigo-500 focus:outline-none" placeholder="Ten / SDT / CCCD">
                                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 rounded-xl text-xs font-bold transition-all">
                                    <i class="fa-solid fa-filter"></i>
                                </button>
                                @if($residentFilters['q'] !== '')
                                    <a href="{{ route('smartroom.admin', ['tab' => 'resident-section']) }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 rounded-xl text-xs font-bold transition-all">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                @endif
                            </form>
                            <button onclick="toggleAddResidentModal(true)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                                <i class="fa-solid fa-plus"></i> Thêm Cư Dân
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/50 border-b border-slate-900">
                                <tr>
                                    <th class="px-4 py-4 font-bold">Họ tên</th>
                                    <th class="px-4 py-4 font-bold">Phòng</th>
                                    <th class="px-4 py-4 font-bold">SĐT</th>
                                    <th class="px-4 py-4 font-bold">CCCD</th>
                                    <th class="px-4 py-4 font-bold">Quê quán</th>
                                    <th class="px-4 py-4 font-bold">Tạm trú</th>
                                    <th class="px-4 py-4 font-bold">Ngày vào ở</th>
                                    <th class="px-4 py-4 font-bold text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900" id="resident-table-body">
                                @foreach($residents as $resident)
                                <tr class="hover:bg-slate-900/30 transition-all">
                                    <td class="px-4 py-4 font-bold text-slate-200">{{ $resident->name }}</td>
                                    <td class="px-4 py-4 text-xs font-semibold text-indigo-400">P. {{ $resident->room ? $resident->room->room_number : 'N/A' }}</td>
                                    <td class="px-4 py-4 text-xs text-slate-400 font-mono">{{ $resident->phone }}</td>
                                    <td class="px-4 py-4 text-xs text-slate-400 font-mono">{{ $resident->cccd ?? '—' }}</td>
                                    <td class="px-4 py-4 text-xs text-slate-500 max-w-[120px] truncate" title="{{ $resident->hometown }}">{{ $resident->hometown ?? '—' }}</td>
                                    <td class="px-4 py-4">
                                        @if($resident->temporary_residence_status === 'registered')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Đã đăng ký
                                            </span>
                                        @elseif($resident->temporary_residence_status === 'absent')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Tạm vắng
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Chưa ĐK
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-xs text-slate-500">{{ \Carbon\Carbon::parse($resident->start_date)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex gap-1.5 justify-center flex-wrap">
                                            {{-- Nút Xem chi tiết --}}
                                            <button onclick="openViewResidentModal({{ $resident->id }}, '{{ addslashes($resident->name) }}', '{{ $resident->phone }}', '{{ $resident->email }}', '{{ $resident->room ? $resident->room->room_number : 'N/A' }}', '{{ \Carbon\Carbon::parse($resident->start_date)->format('d/m/Y') }}', '{{ $resident->status === 'active' ? 'Đang hoạt động' : 'Tạm ngưng' }}', '{{ $resident->dob }}', '{{ $resident->cccd }}', '{{ addslashes($resident->hometown) }}', '{{ $resident->temporary_residence_status }}')" class="px-2 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-[10px] font-bold text-emerald-400 transition-all" title="Xem chi tiết">
                                                <i class="fa-regular fa-eye"></i>
                                            </button>
                                            {{-- Nút Sửa thông tin --}}
                                            <button onclick="openEditResidentModal('{{ $resident->id }}', '{{ addslashes($resident->name) }}', '{{ $resident->phone }}', '{{ $resident->room_id }}', '{{ $resident->start_date }}', '{{ $resident->dob }}', '{{ $resident->cccd }}', '{{ addslashes($resident->hometown) }}', '{{ $resident->temporary_residence_status }}', '{{ $resident->version }}')" class="px-2 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-[10px] font-bold text-indigo-400 transition-all" title="Sửa thông tin">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </button>
                                            {{-- Nút Quản lý người thân --}}
                                            <button onclick="openRelativesModal({{ $resident->id }}, '{{ addslashes($resident->name) }}')" class="px-2 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-[10px] font-bold text-cyan-400 transition-all" title="Quản lý người thân tạm trú">
                                                <i class="fa-solid fa-people-roof"></i>
                                            </button>
                                            @if($isLandlord)
                                                {{-- Nút Xóa cư dân - có chống spam click (disabled sau click) --}}
                                                <form action="{{ route('smartroom.admin.resident.delete', $resident->id) }}" method="POST" onsubmit="return confirmAndDisable(this, 'Bạn có chắc chắn muốn xóa cư dân này ra khỏi phòng trọ?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="anti-spam-btn px-2 py-1.5 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 rounded-lg text-[10px] font-bold text-rose-400 transition-all" title="Xóa cư dân">
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($residents->hasPages())
                        <div class="mt-6 flex justify-center pb-2">
                            {{ $residents->appends(array_merge(request()->query(), ['tab' => 'resident-section']))->links() }}
                        </div>
                    @endif
                </div>
            </section>

            <!-- SECTION 5: CONTRACT MANAGEMENT -->
            <section id="contract-section" class="tab-content hidden space-y-8 animate-fade-in">
                <!-- Premium Stat Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(99,102,241,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Tổng số hợp đồng</p>
                                <h3 class="text-3xl font-extrabold text-white mt-2 tracking-tight">{{ $contractStats['total'] }}</h3>
                                <span class="text-[10px] text-indigo-400 font-semibold mt-1 block">Tất cả bản ghi</span>
                            </div>
                            <span class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-file-contract text-lg"></i>
                            </span>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(16,185,129,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Hợp đồng hiệu lực</p>
                                <h3 class="text-3xl font-extrabold text-white mt-2 tracking-tight">{{ $contractStats['active'] }}</h3>
                                <span class="text-[10px] text-emerald-400 font-semibold mt-1 block">Đã có chữ ký</span>
                            </div>
                            <span class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-circle-check text-lg"></i>
                            </span>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(245,158,11,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-amber-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Chờ cư dân ký</p>
                                <h3 class="text-3xl font-extrabold text-white mt-2 tracking-tight">{{ $contractStats['pending'] }}</h3>
                                <span class="text-[10px] text-amber-400 font-semibold mt-1 block">Yêu cầu chữ ký</span>
                            </div>
                            <span class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-signature text-lg"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Contract List Glass Card -->
                <div class="glass-card rounded-2xl p-6 border border-slate-800/40 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-file-invoice-dollar text-indigo-400"></i> Danh Sách Hợp Đồng Online
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Khởi tạo hợp đồng thuê nhà điện tử và theo dõi trạng thái ký trực tuyến.</p>
                        </div>
                        <div>
                            <button onclick="toggleAddContractModal(true)" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-lg shadow-indigo-600/25 hover:-translate-y-0.5">
                                <i class="fa-solid fa-plus-circle"></i> Tạo Hợp Đồng Mới
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-900">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-900/80 border-b border-slate-900">
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider">Mã Hợp Đồng</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Phòng Trọ</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Cư Dân Đại Diện</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Tiền Cọc</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Thời Hạn Thuê</th>
                                    <th class="px-6 py-4 font-bold text-center tracking-wider">Trạng Thái</th>
                                    <th class="px-6 py-4 font-bold text-center tracking-wider">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900 bg-slate-950/20">
                                @forelse($contracts as $c)
                                <tr class="hover:bg-slate-900/40 transition-all group">
                                    <td class="px-6 py-4 font-bold text-slate-200 group-hover:text-indigo-400 transition-colors">{{ $c->contract_code }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded bg-indigo-500/10 text-indigo-400 text-xs font-bold border border-indigo-500/20">
                                            Phòng {{ $c->room ? $c->room->room_number : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-200 text-xs">{{ $c->resident ? $c->resident->name : 'N/A' }}</div>
                                        <div class="text-[10px] text-slate-500 mt-0.5"><i class="fa-solid fa-phone text-[8px] mr-1"></i>{{ $c->resident ? $c->resident->phone : '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-bold text-emerald-400">{{ number_format($c->deposit) }}đ</td>
                                    <td class="px-6 py-4 text-xs text-slate-400">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-calendar-alt text-[10px] text-slate-500"></i>
                                            <span>{{ date('d/m/Y', strtotime($c->start_date)) }} - {{ date('d/m/Y', strtotime($c->end_date)) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($c->status === 'active')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                                Đã ký hiệu lực
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                Chờ chữ ký
                                            </span>
                                        @endif

                                        @if($c->renewal_status === 'requested')
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 animate-pulse" title="Ghi chú cư dân: {{ $c->renewal_note }}">
                                                    Cần gia hạn ({{ $c->renewal_months }} thg)
                                                </span>
                                            </div>
                                        @elseif($c->renewal_status === 'approved')
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                                                    Đã duyệt gia hạn
                                                </span>
                                            </div>
                                        @elseif($c->renewal_status === 'declined')
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-500/10 text-rose-300 border border-rose-500/20">
                                                    Đã từ chối gia hạn
                                                </span>
                                            </div>
                                        @elseif($c->renewal_status === 'renewed')
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                                    Đã gia hạn/tái ký
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2 flex-wrap">
                                            @if($c->status === 'pending')
                                                <button onclick="copySignLink('{{ route('smartroom.contract.sign_view', $c->id, false) }}', this)" class="px-3 py-2 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-400 hover:text-white rounded-xl text-xs font-bold border border-indigo-500/20 transition-all flex items-center gap-1.5">
                                                    <i class="fa-solid fa-link"></i> Link ký
                                                </button>
                                                <button onclick="openSendMsgModal('{{ $c->resident ? $c->resident->phone : '' }}', '{{ $c->resident ? $c->resident->name : '' }}', 'Kính gửi anh/chị {{ $c->resident ? $c->resident->name : '' }}, vui lòng truy cập đường link sau để hoàn tất ký kết hợp đồng thuê phòng {{ $c->room ? $c->room->room_number : '' }}: ' + window.location.origin + '{{ route('smartroom.contract.sign_view', $c->id, false) }}', 'contract')" class="px-3 py-2 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white rounded-xl text-xs font-bold border border-emerald-500/20 transition-all flex items-center gap-1.5">
                                                    <i class="fa-solid fa-paper-plane"></i> Gửi Zalo/SMS
                                                </button>
                                            @endif
                                            
                                            @if($c->status === 'active' || $c->renewal_status === 'requested')
                                                <button onclick="openRenewContractModal({{ $c->toJson() }}, {{ $c->resident ? $c->resident->toJson() : 'null' }}, {{ $c->room ? $c->room->toJson() : 'null' }})" class="px-3 py-2 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white rounded-xl text-xs font-bold border border-emerald-500/20 transition-all flex items-center gap-1.5">
                                                    <i class="fa-solid fa-clock-rotate-left"></i> Gia hạn / Tái ký
                                                </button>
                                            @endif

                                            <a href="{{ route('smartroom.contract.sign_view', $c->id) }}" target="_blank" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold border border-slate-750 transition-all flex items-center gap-1.5">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem HĐ
                                            </a>
                                            <a href="{{ route('smartroom.contract.pdf', $c->id) }}" target="_blank" class="px-3 py-2 bg-cyan-600/20 hover:bg-cyan-600 text-cyan-300 hover:text-white rounded-xl text-xs font-bold border border-cyan-500/25 transition-all flex items-center gap-1.5">
                                                <i class="fa-solid fa-file-pdf"></i> In/PDF
                                            </a>
                                            @if($isLandlord)
                                                <form action="{{ route('smartroom.admin.contract.delete', $c->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hợp đồng này không?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-2 bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white rounded-xl text-xs font-bold border border-rose-500/25 transition-all">
                                                        <i class="fa-solid fa-trash-can"></i> Xóa
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-xs text-slate-500">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <i class="fa-solid fa-folder-open text-2xl text-slate-700"></i>
                                            <span>Không tìm thấy hợp đồng nào. Hãy tạo hợp đồng mới!</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($contracts->hasPages())
                        <div class="mt-6 flex justify-center pb-2">
                            {{ $contracts->appends(array_merge(request()->query(), ['tab' => 'contract-section']))->links() }}
                        </div>
                    @endif
                </div>
            </section>

            <!-- SECTION 6: CONTACT REQUESTS -->
            <section id="contact-section" class="tab-content hidden space-y-8 animate-fade-in">
                <!-- Stat Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(16,185,129,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Tổng số yêu cầu</p>
                                <h3 class="text-3xl font-extrabold text-white mt-2 tracking-tight">{{ $contactRequestStats['total'] }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                                <i class="fa-solid fa-phone-volume text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(245,158,11,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-amber-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Chưa xử lý</p>
                                <h3 class="text-3xl font-extrabold text-amber-400 mt-2 tracking-tight">{{ $contactRequestStats['pending'] }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400">
                                <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:shadow-[0_0_30px_rgba(99,102,241,0.1)] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-600/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Đã liên hệ</p>
                                <h3 class="text-3xl font-extrabold text-indigo-400 mt-2 tracking-tight">{{ $contactRequestStats['processed'] }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                                <i class="fa-solid fa-square-check text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Consultation Requests Table -->
                <div class="glass-card rounded-3xl border border-slate-900 overflow-hidden shadow-2xl">
                    <div class="p-6 border-b border-slate-900 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-200">Danh sách đăng ký tư vấn</h2>
                            <p class="text-xs text-slate-500 mt-1">Các yêu cầu xem phòng và đăng ký tư vấn từ khách thuê trên Renty Hub</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-900">
                                <tr>
                                    <th class="px-6 py-4">Khách hàng</th>
                                    <th class="px-6 py-4">Số điện thoại</th>
                                    <th class="px-6 py-4">Phòng quan tâm</th>
                                    <th class="px-6 py-4">Lời nhắn</th>
                                    <th class="px-6 py-4">Ngày đăng ký</th>
                                    <th class="px-6 py-4">Trạng thái</th>
                                    <th class="px-6 py-4 text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900">
                                @forelse($contactRequests as $req)
                                <tr class="hover:bg-slate-900/40 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-slate-250">{{ $req->name }}</td>
                                    <td class="px-6 py-4 font-mono text-xs text-indigo-300">{{ $req->phone }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-900 border border-slate-800 text-slate-300">
                                            Phòng {{ $req->room->room_number }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs max-w-xs truncate" title="{{ $req->message }}">
                                        {{ $req->message ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4">
                                        @if($req->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                Chờ xử lý
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                Đã liên hệ
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="tel:{{ $req->phone }}" class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all text-xs" title="Gọi điện">
                                                <i class="fa-solid fa-phone"></i>
                                            </a>
                                            <a href="https://zalo.me/{{ $req->phone }}" target="_blank" class="w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all text-xs" title="Chat Zalo">
                                                <i class="fa-solid fa-comment-sms"></i>
                                            </a>
                                            <form action="{{ route('smartroom.admin.contact_request.status', $req->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $req->status === 'pending' ? 'processed' : 'pending' }}">
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center hover:bg-indigo-500 hover:text-white transition-all text-xs" title="Đổi trạng thái">
                                                    <i class="fa-solid {{ $req->status === 'pending' ? 'fa-check' : 'fa-rotate-left' }}"></i>
                                                </button>
                                            </form>
                                            @if($isLandlord)
                                                <form action="{{ route('smartroom.admin.contact_request.delete', $req->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa yêu cầu tư vấn này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-8 h-8 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all text-xs" title="Xóa">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-xs text-slate-500">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <i class="fa-solid fa-phone-slash text-2xl text-slate-700"></i>
                                            <span>Chưa có yêu cầu tư vấn hay đăng ký xem phòng nào.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($contactRequests->hasPages())
                        <div class="mt-6 flex justify-center pb-2">
                            {{ $contactRequests->appends(array_merge(request()->query(), ['tab' => 'contact-section']))->links() }}
                        </div>
                    @endif
                </div>
            </section>

        </main>
    </div>

    <!-- ADD RESIDENT MODAL (POPUP) - Mở rộng thêm thông tin cá nhân & tạm trú -->
    <div id="add-resident-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-2xl bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[90vh] overflow-y-auto">
            <button onclick="toggleAddResidentModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-indigo-400"></i> Thêm Cư Dân Mới
            </h2>
            <!-- Chống spam click: form submit sẽ disable nút sau lần click đầu tiên -->
            <form action="{{ route('smartroom.admin.resident.store') }}" method="POST" class="space-y-4" onsubmit="return antiSpamSubmit(this)">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Họ và tên *</label>
                        <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="Nguyễn Văn A">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số điện thoại *</label>
                        <input type="tel" name="phone" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="09xxxxxxxx">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày sinh</label>
                        <input type="date" name="dob" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số CCCD</label>
                        <input type="text" name="cccd" maxlength="12" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="0xxxxxxxxxx">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Quê quán</label>
                        <input type="text" name="hometown" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="TP. Hồ Chí Minh">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Chọn phòng *</label>
                        <select name="room_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            @foreach($emptyRoomsList as $room)
                                <option value="{{ $room->id }}">P. {{ $room->room_number }} (Trống)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày vào ở *</label>
                        <input type="date" name="start_date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tạm trú *</label>
                        <select name="temporary_residence_status" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="none">Chưa đăng ký</option>
                            <option value="registered">Đã đăng ký</option>
                            <option value="absent">Tạm vắng</option>
                        </select>
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleAddResidentModal(false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                        Hủy bỏ
                    </button>
                    <button type="submit" class="anti-spam-btn px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        Xác Nhận Thêm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT RESIDENT MODAL - Có Optimistic Locking (version) để chặn ghi đè xung đột -->
    <div id="edit-resident-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-2xl bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[90vh] overflow-y-auto">
            <button onclick="toggleEditResidentModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-indigo-400"></i> Chỉnh Sửa Thông Tin Cư Dân
            </h2>
            <form id="edit-resident-form" action="" method="POST" class="space-y-4" onsubmit="return antiSpamSubmit(this)">
                @csrf
                @method('PUT')
                <!-- Optimistic Locking: trường version ẩn để server so sánh phiên bản trước khi cập nhật -->
                <input type="hidden" name="version" id="edit-version" value="1">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Họ và tên *</label>
                        <input type="text" name="name" id="edit-name" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số điện thoại *</label>
                        <input type="tel" name="phone" id="edit-phone" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày sinh</label>
                        <input type="date" name="dob" id="edit-dob" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số CCCD</label>
                        <input type="text" name="cccd" id="edit-cccd" maxlength="12" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Quê quán</label>
                        <input type="text" name="hometown" id="edit-hometown" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Phòng trọ *</label>
                        <select name="room_id" id="edit-room-id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            @foreach($rooms as $r)
                                <option value="{{ $r->id }}">P. {{ $r->room_number }} ({{ $r->status === 'empty' ? 'Trống' : 'Đang thuê' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày vào ở *</label>
                        <input type="date" name="start_date" id="edit-start-date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tạm trú *</label>
                        <select name="temporary_residence_status" id="edit-temp-status" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="none">Chưa đăng ký</option>
                            <option value="registered">Đã đăng ký</option>
                            <option value="absent">Tạm vắng</option>
                        </select>
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleEditResidentModal(false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                        Hủy bỏ
                    </button>
                    <button type="submit" class="anti-spam-btn px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        Lưu Thay Đổi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- VIEW RESIDENT MODAL (POPUP) - Hiển thị chi tiết cư dân & danh sách người thân đi cùng -->
    <div id="view-resident-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-2xl bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[90vh] overflow-y-auto">
            <button onclick="toggleViewResidentModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-address-card text-indigo-400"></i> Thông Tin Chi Tiết Cư Dân
            </h2>
            <div class="space-y-6">
                <!-- Thông tin cá nhân -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-user text-indigo-400 text-[10px]"></i> Thông tin cá nhân
                    </h3>
                    <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-slate-900/50 border border-slate-800/40">
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Họ và tên:</span>
                                <strong class="text-slate-200" id="view-name">Nguyễn Văn A</strong>
                            </div>
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Số điện thoại:</span>
                                <strong class="text-slate-200 font-mono" id="view-phone">09xxxxxxxx</strong>
                            </div>
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Email liên hệ:</span>
                                <strong class="text-slate-200" id="view-email">email@gmail.com</strong>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Ngày sinh:</span>
                                <strong class="text-slate-200 font-mono" id="view-dob">—</strong>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Phòng thuê:</span>
                                <strong class="text-indigo-400 font-bold" id="view-room">Phòng 101</strong>
                            </div>
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Số CCCD:</span>
                                <strong class="text-slate-200 font-mono" id="view-cccd">—</strong>
                            </div>
                            <div class="flex justify-between text-xs border-b border-slate-800/60 pb-1.5">
                                <span class="text-slate-500">Quê quán:</span>
                                <strong class="text-slate-200" id="view-hometown">—</strong>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Đăng ký tạm trú:</span>
                                <span id="view-temp-status" class="px-2 py-0.5 rounded text-[10px] font-bold">Chưa ĐK</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danh sách người thân -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-people-group text-cyan-400 text-[10px]"></i> Người thân tạm trú cùng
                        </h3>
                        <button id="view-manage-relatives-btn" onclick="" class="text-[10px] font-bold text-cyan-400 hover:text-cyan-300 transition-colors flex items-center gap-1">
                            <i class="fa-solid fa-cog"></i> Quản lý
                        </button>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-900/50 border border-slate-800/40 min-h-[80px]">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-300 hidden" id="view-relatives-table">
                                <thead class="text-slate-500 uppercase font-bold text-[10px] border-b border-slate-800">
                                    <tr>
                                        <th class="pb-2">Họ tên</th>
                                        <th class="pb-2">Quan hệ</th>
                                        <th class="pb-2">CCCD</th>
                                        <th class="pb-2">Trạng thái tạm trú</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/40" id="view-relatives-tbody">
                                    <!-- AJAX insert relatives here -->
                                </tbody>
                            </table>
                            <div class="text-slate-500 text-center py-4" id="view-relatives-empty">
                                <i class="fa-solid fa-folder-open text-lg mb-1 block"></i>
                                Không có thông tin người thân tạm trú.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="toggleViewResidentModal(false)" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MANAGE RELATIVES MODAL (POPUP) - Quản lý người thân tạm trú qua AJAX -->
    <div id="relatives-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-3xl bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[90vh] overflow-y-auto">
            <button onclick="toggleRelativesModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-2 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-people-roof text-cyan-400"></i> Quản Lý Người Thân Tạm Trú
            </h2>
            <p class="text-xs text-slate-500 mb-6">Của cư dân: <strong class="text-slate-300 font-bold" id="relatives-modal-resident-name">—</strong></p>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Danh sách người thân hiện có -->
                <div class="lg:col-span-3 space-y-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                        <i class="fa-solid fa-list text-cyan-400"></i> Danh sách người thân trọ cùng
                    </h3>
                    <div class="p-4 rounded-xl bg-slate-900/40 border border-slate-800/60 min-h-[200px] max-h-[350px] overflow-y-auto space-y-2" id="relatives-list-container">
                        <!-- AJAX generated list here -->
                    </div>
                </div>

                <!-- Form Thêm / Sửa người thân -->
                <div class="lg:col-span-2 space-y-4 border-t lg:border-t-0 lg:border-l border-slate-800/80 pt-4 lg:pt-0 lg:pl-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1" id="relative-form-title">
                        <i class="fa-solid fa-user-plus text-indigo-400"></i> Thêm Người Thân Mới
                    </h3>
                    
                    <form id="relative-ajax-form" onsubmit="saveRelative(event)" class="space-y-3">
                        <input type="hidden" id="relative-id" value="">
                        <!-- version của relative cho Optimistic Locking -->
                        <input type="hidden" id="relative-version" value="1">
                        
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Họ và tên *</label>
                            <input type="text" id="relative-name" required class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Nguyễn Văn B">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Mối quan hệ *</label>
                                <input type="text" id="relative-relationship" required class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Bố, mẹ, vợ, con...">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Ngày sinh</label>
                                <input type="date" id="relative-dob" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Số CCCD</label>
                            <input type="text" id="relative-cccd" maxlength="12" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="12 số">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Quê quán</label>
                            <input type="text" id="relative-hometown" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Địa chỉ quê quán">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Trạng thái đăng ký tạm trú *</label>
                            <select id="relative-temp-status" required class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none">
                                <option value="none">Chưa đăng ký</option>
                                <option value="registered">Đã đăng ký</option>
                                <option value="absent">Tạm vắng</option>
                            </select>
                        </div>
                        <div class="pt-2 flex gap-2">
                            <button type="button" id="relative-reset-btn" onclick="resetRelativeForm()" class="hidden w-1/2 py-2 rounded-lg text-xs font-bold text-slate-400 bg-transparent hover:bg-slate-900 border border-slate-800 transition-all">
                                Hủy sửa
                            </button>
                            <button type="submit" id="relative-submit-btn" class="w-full py-2 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-save"></i> <span>Lưu Lại</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="pt-6 mt-6 border-t border-slate-900 flex justify-end">
                <button type="button" onclick="toggleRelativesModal(false)" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-300 bg-slate-900 border border-slate-800 hover:border-slate-700 transition-all">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <!-- ADD CONTRACT MODAL (POPUP) -->
    <div id="add-contract-modal" class="fixed inset-0 z-50 bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center transition-opacity duration-300">
        <div class="w-full max-w-5xl bg-[#0a0f1d] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in mx-4 max-h-[92vh] overflow-y-auto">
            <button onclick="toggleAddContractModal(false)" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-indigo-400"></i> Tạo Hợp Đồng Thuê Nhà Mới
            </h2>
            <form action="{{ route('smartroom.admin.contract.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nơi ký</label>
                        <input type="text" name="contract_place" value="Hà Nội" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày</label>
                        <input type="number" name="sign_day" min="1" max="31" value="{{ date('d') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tháng</label>
                        <input type="number" name="sign_month" min="1" max="12" value="{{ date('m') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Năm</label>
                        <input type="number" name="sign_year" min="1900" max="2100" value="{{ date('Y') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Chọn Phòng Trọ</label>
                        <div class="relative">
                            <div class="flex items-center gap-2 rounded-xl bg-slate-900 border border-slate-800 focus-within:border-indigo-500 px-4">
                                <i class="fa-solid fa-magnifying-glass text-slate-500 text-xs"></i>
                                <input type="text" id="contract-room-search" oninput="filterContractRooms(this.value)" onfocus="filterContractRooms(this.value)" placeholder="Tìm số phòng, tên tòa, địa chỉ..." autocomplete="off" class="w-full py-2.5 bg-transparent text-slate-200 text-sm focus:outline-none">
                            </div>
                            <input type="hidden" name="room_id" id="contract-room-id" required>
                            <div id="contract-room-results" class="hidden absolute left-0 right-0 top-[calc(100%+8px)] z-50 max-h-72 overflow-y-auto rounded-2xl border border-slate-800 bg-slate-950 shadow-2xl shadow-black/40 p-1">
                                @foreach($rooms as $r)
                                    @if($r->status !== 'empty')
                                        @php
                                            $buildingName = $r->building?->name ?? 'Chưa rõ tòa';
                                            $buildingAddress = $r->building?->address ?? '';
                                            $roomLabel = 'Phòng ' . $r->room_number . ' - ' . $buildingName;
                                            $roomSearchText = $roomLabel . ' Phòng' . $r->room_number . ' ' . $buildingAddress;
                                        @endphp
                                        <button type="button" onclick="selectContractRoom(this)" data-id="{{ $r->id }}" data-price="{{ $r->price }}" data-area="{{ $r->area }}" data-room="{{ $r->room_number }}" data-address="{{ $buildingAddress }}" data-label="{{ $roomLabel }}" data-search="{{ mb_strtolower($roomSearchText) }}" class="contract-room-option w-full text-left px-3 py-2.5 rounded-xl hover:bg-indigo-600/20 transition-all">
                                            <span class="block text-sm font-bold text-slate-100">Phòng {{ $r->room_number }}</span>
                                            <span class="block text-[11px] text-slate-500">{{ $buildingName }}</span>
                                        </button>
                                    @endif
                                @endforeach
                                <div id="contract-room-empty" class="hidden px-3 py-4 text-center text-xs font-semibold text-slate-500">
                                    Không tìm thấy phòng phù hợp
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-950/30 p-4">
                    <h3 class="text-sm font-extrabold text-slate-200 mb-3">I. Bên cho thuê nhà (Bên A)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="lessor_name" required value="{{ Auth::user()->name ?? 'Chủ trọ SmartRoom' }}" placeholder="Ông/Bà bên A" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                        <input type="text" name="lessor_id_number" placeholder="CMTND/CCCD bên A" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                        <input type="text" name="lessor_phone" value="{{ Auth::user()->phone ?? '' }}" placeholder="Điện thoại bên A" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                        <input type="text" name="lessor_address" placeholder="HKTT/Chỗ ở hiện tại bên A" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-950/30 p-4">
                    <h3 class="text-sm font-extrabold text-slate-200 mb-3">II. Bên thuê nhà ở (Bên B)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tên bên thuê</label>
                            <input type="text" name="lessee_name" id="lessee-name" required placeholder="Nhập họ tên bên thuê" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                            <input type="hidden" name="resident_id" id="contract-resident-id">
                        </div>
                        <input type="text" name="lessee_id_number" id="lessee-id-number" placeholder="CMND/CCCD bên B" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                        <input type="text" name="lessee_phone" id="lessee-phone" placeholder="Điện thoại bên B" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                        <input type="text" name="lessee_permanent_address" id="lessee-permanent-address" placeholder="HKTT bên B" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                        <input type="text" name="lessee_current_address" placeholder="Chỗ ở hiện tại bên B" class="md:col-span-2 w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày Bắt Đầu</label>
                        <input type="date" name="start_date" id="contract-start-date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ngày Kết Thúc</label>
                        <input type="date" name="end_date" id="contract-end-date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tiền Đặt Cọc (VNĐ)</label>
                        <input type="number" name="deposit" id="contract-deposit" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" placeholder="3000000">
                    </div>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-950/30 p-4 space-y-4">
                    <h3 class="text-sm font-extrabold text-slate-200">Nội dung thuê và thanh toán</h3>
                    <input type="text" name="rental_address" id="rental-address" required placeholder="Địa chỉ nhà/phòng cho thuê" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    <textarea name="rental_area_description" id="rental-area-description" rows="2" placeholder="Diện tích cho thuê" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none resize-none"></textarea>
                    <textarea name="equipment_list" rows="4" placeholder="Trang thiết bị kèm theo..." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none resize-none"></textarea>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="text" name="rental_purpose" value="ở" placeholder="Mục đích thuê" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                        <input type="number" name="occupant_count" min="1" max="20" value="1" placeholder="Số người ở" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                        <input type="number" name="rent_price" id="contract-rent-price" required placeholder="Giá thuê/tháng" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                        <input type="number" name="payment_cycle_months" min="1" max="12" value="3" placeholder="Chu kỳ thanh toán tháng" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="first_payment_date" placeholder="Thời điểm thanh toán lần đầu" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                        <input type="text" name="payment_method" placeholder="Hình thức thanh toán" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" onclick="toggleAddContractModal(false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                        Hủy Bỏ
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        Tạo Hợp Đồng
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS Logic -->
    <script>
        const currentUserIsLandlord = @json($isLandlord);
        const canReceiveOnlinePayments = {{ in_array(($tenant->verification_status ?? 'unverified'), ['kyc_verified', 'premium_pending', 'premium_verified'], true) ? 'true' : 'false' }};

        function requireKycForOnlinePayments() {
            alert('Can hoan tat KYC nhan tien truoc khi hien thi VietQR/chuyen khoan. Vui long gui CCCD va tai khoan ngan hang tren the xac minh o dau trang.');
            return false;
        }

        // Setup custom toast notification override for alert()
        (function() {
            const toastStyle = document.createElement('style');
            toastStyle.innerHTML = `
                .custom-toast-container {
                    position: fixed;
                    top: 24px;
                    right: 24px;
                    z-index: 9999;
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                    pointer-events: none;
                }
                .custom-toast {
                    min-width: 320px;
                    max-width: 450px;
                    background: rgba(15, 23, 42, 0.9);
                    backdrop-filter: blur(12px);
                    -webkit-backdrop-filter: blur(12px);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    border-radius: 16px;
                    padding: 16px 20px;
                    color: #f1f5f9;
                    box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3), 0 0 1px 1px rgba(255, 255, 255, 0.05);
                    display: flex;
                    align-items: flex-start;
                    gap: 14px;
                    pointer-events: auto;
                    transform: translateX(120%);
                    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                }
                .custom-toast.show {
                    transform: translateX(0);
                }
                .custom-toast.hide {
                    transform: translateX(120%);
                    opacity: 0;
                    margin-top: -60px;
                }
                .custom-toast-icon {
                    flex-shrink: 0;
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 13px;
                }
                .custom-toast-success .custom-toast-icon {
                    background: rgba(16, 185, 129, 0.15);
                    color: #10b981;
                    border: 1px solid rgba(16, 185, 129, 0.2);
                }
                .custom-toast-warning .custom-toast-icon {
                    background: rgba(245, 158, 11, 0.15);
                    color: #f59e0b;
                    border: 1px solid rgba(245, 158, 11, 0.2);
                }
                .custom-toast-error .custom-toast-icon {
                    background: rgba(239, 68, 68, 0.15);
                    color: #ef4444;
                    border: 1px solid rgba(239, 68, 68, 0.2);
                }
                .custom-toast-info .custom-toast-icon {
                    background: rgba(59, 130, 246, 0.15);
                    color: #3b82f6;
                    border: 1px solid rgba(59, 130, 246, 0.2);
                }
                .custom-toast-content {
                    flex-grow: 1;
                }
                .custom-toast-title {
                    font-size: 13px;
                    font-weight: 700;
                    margin-bottom: 3px;
                    letter-spacing: 0.3px;
                }
                .custom-toast-message {
                    font-size: 12px;
                    color: #94a3b8;
                    line-height: 1.5;
                    white-space: pre-wrap;
                }
                .custom-toast-close {
                    color: #64748b;
                    cursor: pointer;
                    font-size: 14px;
                    transition: color 0.2s;
                    margin-top: 1px;
                }
                .custom-toast-close:hover {
                    color: #94a3b8;
                }
            `;
            document.head.appendChild(toastStyle);

            window.alert = function(message) {
                let type = 'success';
                let title = 'Thông Báo';
                
                const lowerMsg = message.toLowerCase();
                if (lowerMsg.includes('lỗi') || 
                    lowerMsg.includes('không thể') || 
                    lowerMsg.includes('thất bại') || 
                    lowerMsg.includes('chưa') || 
                    lowerMsg.includes('không được') || 
                    lowerMsg.includes('chỉ được') || 
                    lowerMsg.includes('nhỏ hơn') ||
                    lowerMsg.includes('vui lòng')) {
                    type = 'warning';
                    title = 'Cảnh Báo';
                } else if (lowerMsg.includes('thành công') || 
                           lowerMsg.includes('tuyệt vời') || 
                           lowerMsg.includes('đã') || 
                           lowerMsg.includes('sao chép')) {
                    type = 'success';
                    title = 'Thành Công';
                } else {
                    type = 'info';
                    title = 'Thông Tin';
                }
                
                let container = document.querySelector('.custom-toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'custom-toast-container';
                    document.body.appendChild(container);
                }
                
                const toast = document.createElement('div');
                toast.className = `custom-toast custom-toast-${type}`;
                
                let iconHtml = '';
                if (type === 'success') iconHtml = '<i class="fa-solid fa-check"></i>';
                else if (type === 'warning') iconHtml = '<i class="fa-solid fa-triangle-exclamation"></i>';
                else if (type === 'error') iconHtml = '<i class="fa-solid fa-circle-xmark"></i>';
                else iconHtml = '<i class="fa-solid fa-info"></i>';
                
                toast.innerHTML = `
                    <div class="custom-toast-icon">${iconHtml}</div>
                    <div class="custom-toast-content">
                        <div class="custom-toast-title">${title}</div>
                        <div class="custom-toast-message">${message}</div>
                    </div>
                    <div class="custom-toast-close" onclick="this.parentElement.classList.add('hide'); setTimeout(() => this.parentElement.remove(), 400);"><i class="fa-solid fa-xmark"></i></div>
                `;
                
                container.appendChild(toast);
                
                setTimeout(() => toast.classList.add('show'), 10);
                
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.classList.remove('show');
                        toast.classList.add('hide');
                        setTimeout(() => toast.remove(), 400);
                    }
                }, 4500);
            };
        })();

        // Tab switching
        function switchTab(tabId, btn = null) {
            // Hide all sections
            document.querySelectorAll('.tab-content').forEach(section => {
                section.classList.add('hidden');
            });
            // Show active section
            const targetSection = document.getElementById(tabId);
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }
            
            // Remove active style from all sidebar nav links
            document.querySelectorAll('.sidebar-nav-link').forEach(button => {
                button.classList.remove('text-indigo-400', 'bg-indigo-500/10', 'border-indigo-500/10');
                button.classList.add('text-slate-400', 'hover:text-slate-100', 'hover:bg-slate-800/50', 'border-transparent', 'hover:border-slate-800');
            });
            
            // Find active button
            let activeBtn = btn;
            if (!activeBtn) {
                activeBtn = document.querySelector(`.sidebar-nav-link[data-section="${tabId}"]`);
            }
            
            // Add active style to current button
            if (activeBtn) {
                activeBtn.classList.remove('text-slate-400', 'hover:text-slate-100', 'hover:bg-slate-800/50', 'border-transparent', 'hover:border-slate-800');
                activeBtn.classList.add('text-indigo-400', 'bg-indigo-500/10', 'border-indigo-500/10');
            }
            
            // Set header title
            let title = "SmartRoom Dashboard";
            if(tabId === 'dashboard-section') title = "Tổng Quan Hệ Thống";
            else if(tabId === 'profile-section') title = "Hồ Sơ & Xác Minh";
            else if(tabId === 'room-map-section') title = "Sơ Đồ Phòng Trực Quan";
            else if(tabId === 'utility-section') title = "Chốt Chỉ Số Điện Nước";
            else if(tabId === 'resident-section') title = "Quản Lý Cư Dân";
            else if(tabId === 'contract-section') title = "Quản Lý Hợp Đồng Online";
            else if(tabId === 'contact-section') title = "Yêu Cầu Tư Vấn & Xem Phòng";
            
            const titleEl = document.getElementById('section-title');
            if (titleEl) {
                titleEl.textContent = title;
            }
            
            // Update URL without page reload
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') !== tabId) {
                urlParams.set('tab', tabId);
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + urlParams.toString();
                window.history.pushState({ tab: tabId }, '', newUrl);
            }
        }

        // Add event listener to sidebar-nav-link links on DOMContentLoaded to switch tabs dynamically without page reload
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.sidebar-nav-link[data-section]').forEach(link => {
                link.addEventListener('click', function(e) {
                    const sectionId = this.getAttribute('data-section');
                    if (sectionId) {
                        e.preventDefault();
                        switchTab(sectionId, this);
                    }
                });
            });

            // Initial switch tab based on URL query parameter
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab') || 'dashboard-section';
            switchTab(tabParam);

            // Sync with browser back/forward buttons
            window.addEventListener('popstate', function(event) {
                const params = new URLSearchParams(window.location.search);
                const currentTab = params.get('tab') || 'dashboard-section';
                switchTab(currentTab);
            });
        });

        // Room filter state & functions
        let currentRoomFilter = 'all';

        function applyCurrentFilterToCard(card) {
            if (!card) return;
            const status = card.getAttribute('data-room-status') || '';
            if (currentRoomFilter === 'all' || currentRoomFilter === status) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        }

        function updateRoomFilterCounts() {
            const cards = document.querySelectorAll('.room-card');
            const counts = {
                all: cards.length,
                empty: 0,
                occupied: 0,
                overdue: 0,
                cleaning: 0,
                maintenance: 0
            };

            cards.forEach(card => {
                const st = card.getAttribute('data-room-status');
                if (counts.hasOwnProperty(st)) {
                    counts[st]++;
                }
            });

            for (const [key, val] of Object.entries(counts)) {
                const el = document.getElementById('filter-count-' + key);
                if (el) el.textContent = val;
            }
        }

        function filterRooms(status, btnElement = null) {
            currentRoomFilter = status;

            // Set active button style
            document.querySelectorAll('.room-filter-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'text-white');
                btn.classList.add('bg-slate-900', 'text-slate-400', 'hover:text-slate-200', 'hover:bg-slate-800');
                if (btn.getAttribute('data-filter') === status) {
                    btn.classList.remove('bg-slate-900', 'text-slate-400', 'hover:text-slate-200', 'hover:bg-slate-800');
                    btn.classList.add('bg-indigo-600', 'text-white');
                }
            });

            // Show/hide cards
            document.querySelectorAll('.room-card').forEach(card => {
                applyCurrentFilterToCard(card);
            });
        }

        function toggleAddContractModal(show) {
            const modal = document.getElementById('add-contract-modal');
            if (show) {
                resetContractDraftFields();
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
                closeContractRoomResults();
            }
        }

        function resetContractDraftFields() {
            const valuesToClear = [
                'contract-room-search',
                'contract-room-id',
                'lessee-name',
                'lessee-id-number',
                'lessee-phone',
                'lessee-permanent-address',
                'rental-address',
                'rental-area-description',
                'contract-rent-price',
                'contract-deposit',
            ];

            valuesToClear.forEach(id => {
                const input = document.getElementById(id);
                if (input) input.value = '';
            });

            const currentAddress = document.querySelector('[name="lessee_current_address"]');
            if (currentAddress) currentAddress.value = '';

            const equipmentList = document.querySelector('[name="equipment_list"]');
            if (equipmentList) equipmentList.value = '';

            const residentSelect = document.getElementById('contract-resident-id');
            if (residentSelect) residentSelect.value = '';

            closeContractRoomResults();
        }

        function fillContractResident(select) {
            const option = select?.selectedOptions?.[0];
            if (!option) return;

            const fields = {
                'lessee-name': option.dataset.name || '',
                'lessee-id-number': option.dataset.cccd || '',
                'lessee-phone': option.dataset.phone || '',
                'lessee-permanent-address': option.dataset.hometown || '',
            };

            Object.entries(fields).forEach(([id, value]) => {
                const input = document.getElementById(id);
                if (input) {
                    input.value = value;
                }
            });
        }

        function filterContractRooms(keyword = '') {
            const results = document.getElementById('contract-room-results');
            const empty = document.getElementById('contract-room-empty');
            if (!results) return;

            const normalize = (value) => (value || '')
                .toString()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^\w\s]/g, ' ')
                .replace(/\s+/g, ' ')
                .trim()
                .toLowerCase();
            const normalizedKeyword = normalize(keyword);
            let visibleCount = 0;

            document.querySelectorAll('.contract-room-option').forEach(option => {
                const haystack = normalize(`${option.dataset.search || ''} ${option.dataset.label || ''}`);
                const isVisible = !normalizedKeyword || haystack.includes(normalizedKeyword);
                option.classList.toggle('hidden', !isVisible);
                if (isVisible) visibleCount++;
            });

            empty?.classList.toggle('hidden', visibleCount > 0);
            results.classList.remove('hidden');
        }

        function closeContractRoomResults() {
            document.getElementById('contract-room-results')?.classList.add('hidden');
        }

        function selectContractRoom(option) {
            if (!option) return;

            const hiddenInput = document.getElementById('contract-room-id');
            const searchInput = document.getElementById('contract-room-search');

            if (hiddenInput) hiddenInput.value = option.dataset.id || '';
            if (searchInput) searchInput.value = option.dataset.label || '';
            fillContractRoom(option);
            closeContractRoomResults();
        }

        function fillContractRoom(option) {
            if (!option) return;

            const roomNumber = option.dataset.room || '';
            const area = option.dataset.area || '';
            const address = option.dataset.address || '';
            const price = option.dataset.price || '';

            const rentalAddress = document.getElementById('rental-address');
            const areaDescription = document.getElementById('rental-area-description');
            const rentPrice = document.getElementById('contract-rent-price');
            const deposit = document.getElementById('contract-deposit');

            if (rentalAddress) {
                rentalAddress.value = [address, roomNumber ? `Phòng ${roomNumber}` : ''].filter(Boolean).join(' - ');
            }
            if (areaDescription) {
                areaDescription.value = roomNumber
                    ? `Phòng ${roomNumber}${area ? `, diện tích ${area} m²` : ''}.`
                    : '';
            }
            if (rentPrice) {
                rentPrice.value = price;
            }
            if (deposit && price) {
                deposit.value = price;
            }
        }

        document.addEventListener('click', (event) => {
            const picker = document.getElementById('contract-room-results');
            const search = document.getElementById('contract-room-search');

            if (picker && search && !picker.contains(event.target) && event.target !== search) {
                closeContractRoomResults();
            }
        });

        function copySignLink(url, btn) {
            const fullUrl = url.startsWith('http') ? url : (window.location.origin + url);
            navigator.clipboard.writeText(fullUrl).then(() => {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Đã sao chép!';
                btn.classList.remove('text-indigo-400', 'bg-indigo-600/20');
                btn.classList.add('text-emerald-400', 'bg-emerald-600/20');
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('text-emerald-400', 'bg-emerald-600/20');
                    btn.classList.add('text-indigo-400', 'bg-indigo-600/20');
                }, 2000);
            }).catch(err => {
                console.error('Lỗi khi sao chép: ', err);
            });
        }

        let currentBillId = null;

        function printModalInvoice() {
            if (currentBillId) {
                window.open(`/smartroom/admin/utility/${currentBillId}/print`, '_blank');
            } else {
                alert('Không tìm thấy hóa đơn hợp lệ để in!');
            }
        }

        // Biến lưu thông tin phòng đang mở trong modal
        let currentActiveRoomId = null;
        let currentActiveRoomStatus = null;
        let currentActiveRoomNumber = null;

        function openRoomDetailById(roomId) {
            const card = document.getElementById('room-card-' + roomId);
            if (!card) return;

            const roomNum = card.getAttribute('data-room-number') || '';
            const status = card.getAttribute('data-room-status') || 'empty';
            const name = card.getAttribute('data-resident-name') || '';
            const phone = card.getAttribute('data-resident-phone') || '';
            const rent = card.getAttribute('data-price') || '0đ';
            const elec = card.getAttribute('data-elec-used') || '0 kWh';
            const water = card.getAttribute('data-water-used') || '0 m3';
            const total = card.getAttribute('data-total-bill') || '0đ';
            const latestBillId = card.getAttribute('data-latest-bill-id') || null;

            openRoomDetail(roomId, roomNum, status, name, phone, rent, elec, water, total, latestBillId);
        }

        // Room Detail modal triggers
        function openRoomDetail(roomId, roomNum, status, name, phone, rent, elec, water, total, latestBillId) {
            // Hỗ trợ trường hợp gọi cũ (9 tham số mà không có roomId ở đầu)
            if (arguments.length === 9 && typeof roomId === 'string' && isNaN(roomId)) {
                latestBillId = total;
                total = water;
                water = elec;
                elec = rent;
                rent = phone;
                phone = name;
                name = status;
                status = roomNum;
                roomNum = roomId;
                roomId = null;
            }

            currentActiveRoomId = roomId;
            currentActiveRoomStatus = status;
            currentActiveRoomNumber = roomNum;

            const modal = document.getElementById('room-detail-modal');
            const title = document.getElementById('modal-room-title');
            const badge = document.getElementById('modal-room-status-badge');
            
            title.textContent = "Phòng " + roomNum;
            
            // Set nhãn trạng thái và badge
            const statusLabels = {
                'empty': 'Trống',
                'occupied': 'Đã thuê',
                'overdue': 'Nợ phí',
                'cleaning': 'Cần dọn dẹp',
                'maintenance': 'Bảo trì'
            };
            const badgeClasses = {
                'empty': 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                'occupied': 'bg-red-500/10 text-red-400 border-red-500/20',
                'overdue': 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                'cleaning': 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                'maintenance': 'bg-slate-500/10 text-slate-400 border-slate-500/20'
            };

            badge.textContent = statusLabels[status] || 'Không xác định';
            badge.className = "text-xs px-2.5 py-1 rounded-md font-bold uppercase border " + (badgeClasses[status] || 'bg-slate-500/10 text-slate-400 border-slate-500/20');

            // Cập nhật trạng thái active cho các nút Housekeeping
            updateQuickStatusButtons(status);

            // If empty, cleaning or maintenance, hide resident and billing details
            const resDetails = document.getElementById('modal-resident-details');
            const billDetails = document.getElementById('modal-billing-details');
            const actionBtn = document.getElementById('modal-btn-action');
            const payBtn = document.getElementById('modal-btn-pay');
            const printBtn = document.getElementById('modal-btn-print');
            const qrBtn = document.getElementById('modal-btn-qr');
            
            if(status === 'empty' || status === 'cleaning' || status === 'maintenance') {
                resDetails.classList.add('hidden');
                billDetails.classList.add('hidden');
                actionBtn.classList.add('hidden');
                payBtn.classList.add('hidden');
                printBtn.classList.add('hidden');
                if (qrBtn) qrBtn.classList.add('hidden');
                currentBillId = null;
            } else {
                resDetails.classList.remove('hidden');
                billDetails.classList.remove('hidden');
                actionBtn.classList.remove('hidden');
                if (qrBtn) qrBtn.classList.remove('hidden');
                
                document.getElementById('modal-resident-name').textContent = name;
                document.getElementById('modal-resident-phone').textContent = phone;
                document.getElementById('modal-bill-rent').textContent = rent;
                document.getElementById('modal-bill-electric').textContent = elec;
                document.getElementById('modal-bill-water').textContent = water;
                document.getElementById('modal-bill-total').textContent = total || rent;
                
                const billStatusBadge = document.getElementById('modal-bill-status');
                const rawAmount = (total || rent).replace(/\D/g, '');
                
                if (qrBtn) {
                    qrBtn.onclick = function() {
                        if (latestBillId && latestBillId !== 'null') {
                            showVietQR(latestBillId);
                        } else {
                            showVietQRFallback(roomNum, rawAmount);
                        }
                    };
                }

                if (latestBillId && latestBillId !== 'null') {
                    currentBillId = latestBillId;
                    printBtn.classList.remove('hidden');
                } else {
                    currentBillId = null;
                    printBtn.classList.add('hidden');
                }

                if(status === 'overdue') {
                    billStatusBadge.textContent = 'Chưa thanh toán';
                    billStatusBadge.className = 'text-[10px] text-amber-400 font-bold px-2 py-0.5 bg-amber-500/10 border border-amber-500/20 rounded';
                    actionBtn.innerHTML = '<i class="fa-solid fa-bell"></i> Gửi nhắc nợ Zalo & SMS';
                    actionBtn.className = "w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-white bg-amber-600 hover:bg-amber-500 shadow-lg shadow-amber-600/30 transition-all cursor-pointer";
                    
                    if (latestBillId && latestBillId !== 'null') {
                        payBtn.classList.remove('hidden');
                        document.getElementById('modal-pay-form').action = `/smartroom/admin/utility/${latestBillId}/pay`;
                        document.getElementById('modal-notify-form').action = `/smartroom/admin/utility/${latestBillId}/notify`;
                        actionBtn.onclick = function() {
                            openSendMsgModal(phone, name, `Kính gửi anh/chị ${name}, ban quản lý thông báo hóa đơn dịch vụ phòng ${roomNum} chưa được thanh toán với tổng số tiền là ${total}. Vui lòng thanh toán sớm nhất có thể. Trân trọng!`, 'debt');
                        };
                    } else {
                        payBtn.classList.add('hidden');
                        actionBtn.onclick = null;
                    }
                } else {
                    billStatusBadge.textContent = 'Đã thanh toán';
                    billStatusBadge.className = 'text-[10px] text-emerald-400 font-bold px-2 py-0.5 bg-emerald-500/10 border border-emerald-500/20 rounded';
                    actionBtn.innerHTML = '<i class="fa-solid fa-check"></i> Đã đóng tiền tháng này';
                    actionBtn.className = "w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-slate-500 bg-slate-900 border border-slate-800 cursor-not-allowed";
                    payBtn.classList.add('hidden');
                    actionBtn.onclick = null;
                }
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRoomDetail() {
            const modal = document.getElementById('room-detail-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }


        function updateQuickStatusButtons(status) {
            const btnEmpty = document.getElementById('btn-quick-empty');
            const btnCleaning = document.getElementById('btn-quick-cleaning');
            const btnMaintenance = document.getElementById('btn-quick-maintenance');
            
            if (!btnEmpty || !btnCleaning || !btnMaintenance) return;

            // Reset classes
            [btnEmpty, btnCleaning, btnMaintenance].forEach(btn => {
                btn.classList.remove('ring-2', 'ring-offset-1', 'ring-offset-slate-950', 'border-emerald-500', 'border-orange-500', 'border-slate-400', 'bg-emerald-500/20', 'bg-orange-500/20', 'bg-slate-500/20');
            });

            if (status === 'empty') {
                btnEmpty.classList.add('border-emerald-500', 'bg-emerald-500/20', 'ring-2', 'ring-emerald-400');
            } else if (status === 'cleaning') {
                btnCleaning.classList.add('border-orange-500', 'bg-orange-500/20', 'ring-2', 'ring-orange-400');
            } else if (status === 'maintenance') {
                btnMaintenance.classList.add('border-slate-400', 'bg-slate-500/20', 'ring-2', 'ring-slate-400');
            }
        }

        // Cập nhật nhanh trạng thái phòng (Housekeeping) qua AJAX
        async function setQuickRoomStatus(newStatus) {
            if (!currentActiveRoomId) {
                alert('Không xác định được phòng cần cập nhật!');
                return;
            }

            const loadingSpinner = document.getElementById('quick-status-loading');
            if (loadingSpinner) loadingSpinner.classList.remove('hidden');

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                    || '{{ csrf_token() }}';

                const response = await fetch(`/smartroom/admin/rooms/${currentActiveRoomId}/quick-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    alert(data.message || 'Có lỗi xảy ra khi cập nhật trạng thái phòng.');
                    return;
                }

                // Cập nhật trực tiếp trên thẻ phòng trên Ma Trận
                applyRoomCardUpdate(data.room);

                // Cập nhật lại trạng thái hiển thị trong Drawer
                currentActiveRoomStatus = newStatus;
                const badge = document.getElementById('modal-room-status-badge');
                if (badge) {
                    badge.textContent = data.room.status_label;
                    badge.className = "text-xs px-2.5 py-1 rounded-md font-bold uppercase border " + data.room.badge_class;
                }
                updateQuickStatusButtons(newStatus);

                showRealtimeToast(`Đã cập nhật P.${data.room.room_number}: ${data.room.status_label}`, 'success');
            } catch (err) {
                console.error('Lỗi setQuickRoomStatus: ', err);
                alert('Không thể kết nối đến máy chủ.');
            } finally {
                if (loadingSpinner) loadingSpinner.classList.add('hidden');
            }
        }


        function syncInputs(btn) {
            const row = btn.closest('tr');
            const newElecInput = row.querySelector('.new-elec-input');
            const newWaterInput = row.querySelector('.new-water-input');
            const newElecVal = newElecInput.value;
            const newWaterVal = newWaterInput.value;
            
            const oldElec = parseInt(row.querySelector('[data-field="old-elec"]').textContent);
            const oldWater = parseInt(row.querySelector('[data-field="old-water"]').textContent);
            
            if (!newElecVal || !newWaterVal) {
                alert('Vui lòng nhập đầy đủ số điện và nước mới!');
                event.preventDefault();
                return;
            }
            
            if (parseInt(newElecVal) < oldElec || parseInt(newWaterVal) < oldWater) {
                alert('Số mới không được nhỏ hơn số cũ!');
                event.preventDefault();
                return;
            }
            
            row.querySelector('.form-new-elec').value = newElecVal;
            row.querySelector('.form-new-water').value = newWaterVal;
        }

        function closeRoomDetail() {
            document.getElementById('room-detail-modal').classList.add('hidden');
        }

        function submitModalPay() {
            if (confirm('Xác nhận cư dân đã thanh toán hóa đơn này?')) {
                document.getElementById('modal-pay-form').submit();
            }
        }

        // Live calculation for electricity & water utility row with anomaly detection & validation
        function calculateRowCost(input) {
            const row = input.closest('tr');
            const roomPrice = parseInt(row.getAttribute('data-price')) || 0;
            
            const oldElecEl = row.querySelector('[data-field="old-elec"]');
            const oldWaterEl = row.querySelector('[data-field="old-water"]');
            const oldElec = oldElecEl ? parseInt(oldElecEl.textContent) || 0 : 0;
            const oldWater = oldWaterEl ? parseInt(oldWaterEl.textContent) || 0 : 0;

            const newElecInputEl = row.querySelector('.new-elec-input');
            const newWaterInputEl = row.querySelector('.new-water-input');
            const newElecVal = newElecInputEl ? newElecInputEl.value.trim() : '';
            const newWaterVal = newWaterInputEl ? newWaterInputEl.value.trim() : '';

            const newElec = newElecVal !== '' ? parseInt(newElecVal) : oldElec;
            const newWater = newWaterVal !== '' ? parseInt(newWaterVal) : oldWater;

            // Kiểm tra số mới nhỏ hơn số cũ (Bảng 25)
            if (newElecInputEl) {
                if (newElecVal !== '' && parseInt(newElecVal) < oldElec) {
                    newElecInputEl.classList.add('border-rose-500', 'text-rose-400', 'bg-rose-500/10');
                    newElecInputEl.title = 'Chỉ số mới không được nhỏ hơn chỉ số cũ tháng trước (' + oldElec + ' kWh)';
                } else {
                    newElecInputEl.classList.remove('border-rose-500', 'text-rose-400', 'bg-rose-500/10');
                    newElecInputEl.removeAttribute('title');
                }
            }

            if (newWaterInputEl) {
                if (newWaterVal !== '' && parseInt(newWaterVal) < oldWater) {
                    newWaterInputEl.classList.add('border-rose-500', 'text-rose-400', 'bg-rose-500/10');
                    newWaterInputEl.title = 'Chỉ số mới không được nhỏ hơn chỉ số cũ tháng trước (' + oldWater + ' m3)';
                } else {
                    newWaterInputEl.classList.remove('border-rose-500', 'text-rose-400', 'bg-rose-500/10');
                    newWaterInputEl.removeAttribute('title');
                }
            }

            let elecUsed = 0;
            let waterUsed = 0;
            
            if (newElec >= oldElec) elecUsed = newElec - oldElec;
            if (newWater >= oldWater) waterUsed = newWater - oldWater;

            const usedElecEl = row.querySelector('[data-field="used-elec"]');
            const usedWaterEl = row.querySelector('[data-field="used-water"]');
            if (usedElecEl) usedElecEl.textContent = elecUsed;
            if (usedWaterEl) usedWaterEl.textContent = waterUsed;

            // Phát hiện tiêu thụ tăng đột biến bất thường (Bảng 25 Kịch bản lỗi)
            const abnormalElecWarn = row.querySelector('.abnormal-elec-warning');
            const abnormalWaterWarn = row.querySelector('.abnormal-water-warning');
            if (abnormalElecWarn) {
                if (elecUsed > 1000) {
                    abnormalElecWarn.classList.remove('hidden');
                    abnormalElecWarn.classList.add('inline-flex');
                } else {
                    abnormalElecWarn.classList.add('hidden');
                    abnormalElecWarn.classList.remove('inline-flex');
                }
            }
            if (abnormalWaterWarn) {
                if (waterUsed > 100) {
                    abnormalWaterWarn.classList.remove('hidden');
                    abnormalWaterWarn.classList.add('inline-flex');
                } else {
                    abnormalWaterWarn.classList.add('hidden');
                    abnormalWaterWarn.classList.remove('inline-flex');
                }
            }

            // Compute costs
            const elecCost = elecUsed * 3500;
            const waterCost = waterUsed * 15000;
            const serviceCost = 150000; // default service fee
            const total = roomPrice + elecCost + waterCost + serviceCost;

            const totalEl = row.querySelector('[data-field="cost-total"]');
            if (totalEl) totalEl.textContent = total.toLocaleString('vi-VN') + "đ";
        }

        function saveSingleUtility(roomId, btn) {
            const row = btn.closest('tr');
            const newElecInput = row.querySelector('.new-elec-input');
            const newWaterInput = row.querySelector('.new-water-input');
            const newElecVal = newElecInput.value;
            const newWaterVal = newWaterInput.value;
            
            const oldElec = parseInt(row.querySelector('[data-field="old-elec"]').textContent);
            const oldWater = parseInt(row.querySelector('[data-field="old-water"]').textContent);
            
            if (!newElecVal || !newWaterVal) {
                alert('Vui lòng nhập đầy đủ số điện và nước mới!');
                return;
            }
            
            if (parseInt(newElecVal) < oldElec || parseInt(newWaterVal) < oldWater) {
                alert('Số mới không được nhỏ hơn số cũ!');
                return;
            }
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('smartroom.admin.utility.store') }}";
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = "{{ csrf_token() }}";
            form.appendChild(csrf);
            
            const roomInp = document.createElement('input');
            roomInp.type = 'hidden';
            roomInp.name = 'room_id';
            roomInp.value = roomId;
            form.appendChild(roomInp);
            
            const elecInp = document.createElement('input');
            elecInp.type = 'hidden';
            elecInp.name = 'new_electricity';
            elecInp.value = newElecVal;
            form.appendChild(elecInp);
            
            const waterInp = document.createElement('input');
            waterInp.type = 'hidden';
            waterInp.name = 'new_water';
            waterInp.value = newWaterVal;
            form.appendChild(waterInp);
            
            document.body.appendChild(form);
            form.submit();
        }

        // =======================================================
        // 2. AI VISION OCR CAMERA QUÉT CÔNG TƠ ĐIỆN NƯỚC (GEMINI AI)
        // =======================================================
        const meterOcrState = {
            roomId: null,
            roomNumber: null,
            type: 'electricity',
            targetInput: null,
            stream: null,
            capturedBase64: null,
            recognizedValue: null,
            activeSource: 'camera'
        };

        function openMeterOcrModal(roomId, roomNumber, type, triggerBtn) {
            meterOcrState.roomId = roomId;
            meterOcrState.roomNumber = roomNumber;
            meterOcrState.type = type;
            
            const row = triggerBtn.closest('tr');
            meterOcrState.targetInput = row.querySelector('.new-' + (type === 'electricity' ? 'elec' : 'water') + '-input');
            
            // Cập nhật text UI
            document.getElementById('ocr-target-room-text').textContent = roomNumber;
            const badge = document.getElementById('ocr-meter-type-badge');
            const unit = document.getElementById('ocr-result-unit');
            if (type === 'electricity') {
                badge.textContent = 'Điện (kWh)';
                badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30';
                unit.textContent = 'kWh';
            } else {
                badge.textContent = 'Nước (m3)';
                badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-cyan-500/20 text-cyan-300 border border-cyan-500/30';
                unit.textContent = 'm3';
            }

            resetOcrModalState();
            document.getElementById('meter-ocr-modal').classList.remove('hidden');

            // Mặc định khởi động camera
            switchOcrSource('camera');
        }

        function closeMeterOcrModal() {
            stopOcrCamera();
            document.getElementById('meter-ocr-modal').classList.add('hidden');
            resetOcrModalState();
        }

        function resetOcrModalState() {
            meterOcrState.capturedBase64 = null;
            meterOcrState.recognizedValue = null;
            
            document.getElementById('ocr-image-preview').classList.add('hidden');
            document.getElementById('ocr-image-preview').src = '';
            document.getElementById('ocr-result-box').classList.add('hidden');
            document.getElementById('ocr-laser-line').classList.add('hidden');
            document.getElementById('ocr-loading-overlay').classList.add('hidden');
            document.getElementById('ocr-fallback-hint').classList.add('hidden');
            
            document.getElementById('btn-ocr-capture').classList.remove('hidden');
            document.getElementById('btn-ocr-retake').classList.add('hidden');
            document.getElementById('btn-ocr-analyze').classList.add('hidden');
            document.getElementById('btn-ocr-apply').disabled = true;
            
            const fileInp = document.getElementById('ocr-file-input');
            if (fileInp) fileInp.value = '';
        }

        function switchOcrSource(mode) {
            meterOcrState.activeSource = mode;
            const tabCamera = document.getElementById('tab-btn-camera');
            const tabUpload = document.getElementById('tab-btn-upload');
            const videoEl = document.getElementById('ocr-camera-video');
            const uploadZone = document.getElementById('ocr-upload-zone');
            const previewImg = document.getElementById('ocr-image-preview');
            const cameraError = document.getElementById('ocr-camera-error');
            const btnCapture = document.getElementById('btn-ocr-capture');

            cameraError.classList.add('hidden');

            if (mode === 'camera') {
                tabCamera.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 bg-indigo-600 text-white shadow-sm';
                tabUpload.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 text-slate-400 hover:text-slate-200';
                
                uploadZone.classList.add('hidden');
                previewImg.classList.add('hidden');
                btnCapture.classList.remove('hidden');
                document.getElementById('btn-ocr-retake').classList.add('hidden');
                document.getElementById('btn-ocr-analyze').classList.add('hidden');

                startOcrCamera();
            } else {
                stopOcrCamera();
                tabUpload.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 bg-indigo-600 text-white shadow-sm';
                tabCamera.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 text-slate-400 hover:text-slate-200';
                
                videoEl.classList.add('hidden');
                btnCapture.classList.add('hidden');
                
                if (meterOcrState.capturedBase64) {
                    previewImg.classList.remove('hidden');
                    uploadZone.classList.add('hidden');
                    document.getElementById('btn-ocr-retake').classList.remove('hidden');
                    document.getElementById('btn-ocr-analyze').classList.remove('hidden');
                } else {
                    uploadZone.classList.remove('hidden');
                    previewImg.classList.add('hidden');
                }
            }
        }

        async function startOcrCamera() {
            const videoEl = document.getElementById('ocr-camera-video');
            const cameraError = document.getElementById('ocr-camera-error');
            videoEl.classList.add('hidden');
            cameraError.classList.add('hidden');

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                cameraError.classList.remove('hidden');
                return;
            }

            try {
                if (meterOcrState.stream) {
                    stopOcrCamera();
                }
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } },
                    audio: false
                });
                meterOcrState.stream = stream;
                videoEl.srcObject = stream;
                videoEl.classList.remove('hidden');
            } catch (err) {
                console.warn('Camera access error:', err);
                cameraError.classList.remove('hidden');
            }
        }

        function stopOcrCamera() {
            if (meterOcrState.stream) {
                meterOcrState.stream.getTracks().forEach(track => track.stop());
                meterOcrState.stream = null;
            }
            const videoEl = document.getElementById('ocr-camera-video');
            if (videoEl) {
                videoEl.srcObject = null;
                videoEl.classList.add('hidden');
            }
        }

        function captureOcrSnapshot() {
            const videoEl = document.getElementById('ocr-camera-video');
            const canvas = document.getElementById('ocr-capture-canvas');
            const previewImg = document.getElementById('ocr-image-preview');

            if (!videoEl || videoEl.videoWidth === 0) {
                alert('Camera chưa sẵn sàng để chụp!');
                return;
            }

            canvas.width = videoEl.videoWidth;
            canvas.height = videoEl.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);

            const base64 = canvas.toDataURL('image/jpeg', 0.9);
            meterOcrState.capturedBase64 = base64;

            // Dừng camera và hiển thị preview
            stopOcrCamera();
            previewImg.src = base64;
            previewImg.classList.remove('hidden');

            document.getElementById('btn-ocr-capture').classList.add('hidden');
            document.getElementById('btn-ocr-retake').classList.remove('hidden');
            document.getElementById('btn-ocr-analyze').classList.remove('hidden');
            
            // Tự động kích hoạt phân tích ngay
            submitOcrAnalysis();
        }

        function resetOcrCapture() {
            meterOcrState.capturedBase64 = null;
            meterOcrState.recognizedValue = null;
            document.getElementById('ocr-image-preview').classList.add('hidden');
            document.getElementById('ocr-result-box').classList.add('hidden');
            document.getElementById('btn-ocr-apply').disabled = true;

            if (meterOcrState.activeSource === 'camera') {
                document.getElementById('btn-ocr-capture').classList.remove('hidden');
                document.getElementById('btn-ocr-retake').classList.add('hidden');
                document.getElementById('btn-ocr-analyze').classList.add('hidden');
                startOcrCamera();
            } else {
                document.getElementById('ocr-upload-zone').classList.remove('hidden');
                document.getElementById('btn-ocr-retake').classList.add('hidden');
                document.getElementById('btn-ocr-analyze').classList.add('hidden');
                document.getElementById('ocr-file-input').value = '';
            }
        }

        function handleOcrFileSelected(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ảnh không được vượt quá 5MB!');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    meterOcrState.capturedBase64 = e.target.result;
                    const previewImg = document.getElementById('ocr-image-preview');
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                    document.getElementById('ocr-upload-zone').classList.add('hidden');
                    document.getElementById('btn-ocr-retake').classList.remove('hidden');
                    document.getElementById('btn-ocr-analyze').classList.remove('hidden');
                    
                    // Tự động phân tích
                    submitOcrAnalysis();
                };
                reader.readAsDataURL(file);
            }
        }

        async function submitOcrAnalysis() {
            if (!meterOcrState.capturedBase64) {
                alert('Vui lòng chụp ảnh hoặc tải ảnh công tơ trước!');
                return;
            }

            const loadingOverlay = document.getElementById('ocr-loading-overlay');
            const laserLine = document.getElementById('ocr-laser-line');
            const resultBox = document.getElementById('ocr-result-box');
            const btnAnalyze = document.getElementById('btn-ocr-analyze');
            const btnApply = document.getElementById('btn-ocr-apply');
            const fallbackHint = document.getElementById('ocr-fallback-hint');

            loadingOverlay.classList.remove('hidden');
            loadingOverlay.classList.add('flex');
            laserLine.classList.remove('hidden');
            resultBox.classList.add('hidden');
            fallbackHint.classList.add('hidden');
            btnAnalyze.disabled = true;

            try {
                const response = await fetch("{{ route('smartroom.admin.ai.ocr_meter') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        image: meterOcrState.capturedBase64,
                        type: meterOcrState.type
                    })
                });

                const data = await response.json();
                if (data.success && data.result) {
                    const result = data.result;
                    meterOcrState.recognizedValue = result.value;

                    document.getElementById('ocr-result-value').textContent = result.value.toLocaleString();
                    
                    const confPercent = Math.round((result.confidence || 0.95) * 100);
                    const confBadge = document.getElementById('ocr-confidence-badge');
                    confBadge.textContent = 'Độ tin cậy: ' + confPercent + '%';

                    if (confPercent >= 85) {
                        confBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                    } else if (confPercent >= 60) {
                        confBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/20';
                        fallbackHint.classList.remove('hidden');
                    } else {
                        confBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500/10 text-rose-400 border border-rose-500/20';
                        fallbackHint.classList.remove('hidden');
                    }

                    if (!result.used_ai) {
                        fallbackHint.classList.remove('hidden');
                    }

                    resultBox.classList.remove('hidden');
                    btnApply.disabled = false;
                } else {
                    alert('Không thể nhận diện chỉ số từ ảnh. Vui lòng chụp rõ nét hơn hoặc nhập tay!');
                }
            } catch (err) {
                console.error('OCR Error:', err);
                alert('Có lỗi xảy ra trong quá trình gửi ảnh tới AI. Vui lòng thử lại hoặc tự nhập tay!');
            } finally {
                loadingOverlay.classList.add('hidden');
                loadingOverlay.classList.remove('flex');
                laserLine.classList.add('hidden');
                btnAnalyze.disabled = false;
            }
        }

        function applyOcrResultToInput() {
            if (meterOcrState.targetInput && meterOcrState.recognizedValue !== null) {
                meterOcrState.targetInput.value = meterOcrState.recognizedValue;
                calculateRowCost(meterOcrState.targetInput);
                
                // Hiệu ứng nháy xanh xác nhận ô vừa được điền
                meterOcrState.targetInput.classList.add('ring-2', 'ring-emerald-500');
                setTimeout(() => {
                    meterOcrState.targetInput.classList.remove('ring-2', 'ring-emerald-500');
                }, 1500);

                closeMeterOcrModal();
            }
        }

        // ==========================================
        // AI BULK METER OCR (QUÉT HÀNG LOẠT NHIỀU CÔNG TƠ)
        // ==========================================
        let bulkOcrState = {
            type: 'electricity',
            files: [], // Array of base64 strings
            results: [], // { index, image, serial_number, room_id, room_number, value, confidence, is_matched, reason }
            allRooms: []
        };

        function openBulkOcrModal(type = 'electricity') {
            setBulkOcrType(type);
            clearBulkOcrFiles();
            const modal = document.getElementById('bulk-meter-ocr-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeBulkOcrModal() {
            const modal = document.getElementById('bulk-meter-ocr-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        function setBulkOcrType(type) {
            bulkOcrState.type = type;
            const badge = document.getElementById('bulk-ocr-type-badge');
            const btnElec = document.getElementById('bulk-type-btn-electricity');
            const btnWater = document.getElementById('bulk-type-btn-water');

            if (type === 'electricity') {
                if (badge) {
                    badge.textContent = 'Điện (kWh)';
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30';
                }
                if (btnElec) {
                    btnElec.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-sm';
                }
                if (btnWater) {
                    btnWater.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-slate-900 text-slate-400 border border-slate-800 hover:text-slate-200';
                }
            } else {
                if (badge) {
                    badge.textContent = 'Nước (m3)';
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-cyan-500/20 text-cyan-300 border border-cyan-500/30';
                }
                if (btnWater) {
                    btnWater.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shadow-sm';
                }
                if (btnElec) {
                    btnElec.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-slate-900 text-slate-400 border border-slate-800 hover:text-slate-200';
                }
            }
        }

        function handleBulkOcrFilesSelected(input) {
            if (!input.files || input.files.length === 0) return;
            loadBulkOcrFilesList(input.files);
        }

        function loadBulkOcrFilesList(fileList) {
            const files = Array.from(fileList).slice(0, 30);
            bulkOcrState.files = [];
            bulkOcrState.results = [];
            document.getElementById('bulk-ocr-results-wrapper')?.classList.add('hidden');
            const btnApply = document.getElementById('btn-bulk-ocr-apply');
            if (btnApply) btnApply.disabled = true;

            let loaded = 0;
            files.forEach(file => {
                if (!file.type.startsWith('image/')) {
                    loaded++;
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    bulkOcrState.files.push(e.target.result);
                    loaded++;
                    if (loaded === files.length) {
                        const countEl = document.getElementById('bulk-files-count');
                        if (countEl) countEl.textContent = bulkOcrState.files.length;
                        document.getElementById('bulk-ocr-files-bar')?.classList.remove('hidden');
                        const btnStart = document.getElementById('btn-start-bulk-ocr');
                        if (btnStart) btnStart.disabled = false;
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        function clearBulkOcrFiles() {
            bulkOcrState.files = [];
            bulkOcrState.results = [];
            const fileInput = document.getElementById('bulk-ocr-file-input');
            if (fileInput) fileInput.value = '';
            document.getElementById('bulk-ocr-files-bar')?.classList.add('hidden');
            document.getElementById('bulk-ocr-progress-box')?.classList.add('hidden');
            document.getElementById('bulk-ocr-results-wrapper')?.classList.add('hidden');
            const btnApply = document.getElementById('btn-bulk-ocr-apply');
            if (btnApply) btnApply.disabled = true;
        }

        async function startBulkOcrAnalysis() {
            if (!bulkOcrState.files || bulkOcrState.files.length === 0) {
                alert('Vui lòng chọn ít nhất 1 ảnh công tơ!');
                return;
            }

            const progressBox = document.getElementById('bulk-ocr-progress-box');
            const progressBar = document.getElementById('bulk-ocr-progress-bar');
            const progressText = document.getElementById('bulk-ocr-progress-text');
            const btnStart = document.getElementById('btn-start-bulk-ocr');

            progressBox.classList.remove('hidden');
            if (btnStart) btnStart.disabled = true;
            if (progressBar) progressBar.style.width = '30%';
            if (progressText) progressText.textContent = '30%';

            try {
                const response = await fetch("{{ route('smartroom.admin.ai.ocr_meter_bulk') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        images: bulkOcrState.files,
                        type: bulkOcrState.type
                    })
                });

                if (progressBar) progressBar.style.width = '75%';
                if (progressText) progressText.textContent = '75%';

                const data = await response.json();
                if (data.success) {
                    if (progressBar) progressBar.style.width = '100%';
                    if (progressText) progressText.textContent = '100%';
                    bulkOcrState.allRooms = data.all_rooms || [];
                    
                    bulkOcrState.results = [];
                    (data.matched || []).forEach(item => {
                        bulkOcrState.results.push({
                            index: item.index,
                            image: bulkOcrState.files[item.index],
                            serial_number: item.serial_number,
                            room_id: item.room_id,
                            room_number: item.room_number,
                            value: item.value,
                            confidence: item.confidence,
                            is_matched: true,
                            reason: ''
                        });
                    });
                    (data.unmatched || []).forEach(item => {
                        bulkOcrState.results.push({
                            index: item.index,
                            image: bulkOcrState.files[item.index],
                            serial_number: item.serial_number,
                            room_id: null,
                            room_number: null,
                            value: item.value,
                            confidence: item.confidence,
                            is_matched: false,
                            reason: item.reason || 'Chưa khớp phòng'
                        });
                    });

                    renderBulkOcrResults();
                } else {
                    alert('Lỗi phân tích: ' + (data.message || 'Không thể quét ảnh hàng loạt.'));
                }
            } catch (err) {
                console.error('Bulk OCR Error:', err);
                alert('Có lỗi xảy ra khi gửi dữ liệu tới AI. Vui lòng thử lại!');
            } finally {
                if (btnStart) btnStart.disabled = false;
                setTimeout(() => {
                    progressBox.classList.add('hidden');
                }, 600);
            }
        }

        function renderBulkOcrResults() {
            const tbody = document.getElementById('bulk-ocr-results-body');
            if (!tbody) return;
            tbody.innerHTML = '';

            let matchedCount = 0;
            bulkOcrState.results.forEach((item, idx) => {
                if (item.is_matched && item.room_id) matchedCount++;

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-900/40 transition-colors border-b border-slate-900';
                
                let roomOptionsHtml = `<option value="">-- Chọn phòng gán tay --</option>`;
                bulkOcrState.allRooms.forEach(r => {
                    const selected = item.room_id == r.id ? 'selected' : '';
                    roomOptionsHtml += `<option value="${r.id}" ${selected}>Phòng ${r.room_number}</option>`;
                });

                const serialDisplay = item.serial_number 
                    ? `<span class="font-mono font-bold text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded text-[11px]">${item.serial_number}</span>`
                    : `<span class="text-slate-500 italic text-[10px]">Không tìm thấy Số SX</span>`;

                const statusBadge = item.is_matched && item.room_id
                    ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><i class="fa-solid fa-circle-check"></i> Khớp tự động</span>`
                    : `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20" title="${item.reason}"><i class="fa-solid fa-circle-exclamation"></i> Cần gán phòng</span>`;

                tr.innerHTML = `
                    <td class="px-4 py-3">
                        <img src="${item.image}" class="w-10 h-10 object-cover rounded-lg border border-slate-800 cursor-pointer hover:scale-110 transition-transform shadow-sm" onclick="window.open('${item.image}')" title="Bấm xem ảnh gốc">
                    </td>
                    <td class="px-4 py-3 font-semibold">${serialDisplay}</td>
                    <td class="px-4 py-3">
                        <select onchange="updateBulkItemRoom(${idx}, this.value)" class="w-36 px-2 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:border-indigo-500 focus:outline-none transition-colors">
                            ${roomOptionsHtml}
                        </select>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1">
                            <input type="number" value="${item.value ?? 0}" onchange="updateBulkItemValue(${idx}, this.value)" class="w-20 px-2 py-1 rounded-lg bg-slate-900 border border-slate-800 text-xs font-mono font-bold text-emerald-400 focus:border-indigo-500 focus:outline-none">
                            <span class="text-[10px] text-slate-400">${bulkOcrState.type === 'electricity' ? 'kWh' : 'm3'}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">${statusBadge}</td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" onclick="removeBulkItem(${idx})" class="w-7 h-7 rounded-lg bg-slate-900 hover:bg-rose-500/20 text-slate-500 hover:text-rose-400 text-xs transition-all flex items-center justify-center mx-auto" title="Bỏ qua ảnh này">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            const statsBadge = document.getElementById('bulk-match-stats-badge');
            if (statsBadge) {
                statsBadge.textContent = `Khớp thành công ${matchedCount}/${bulkOcrState.results.length}`;
            }

            document.getElementById('bulk-ocr-results-wrapper')?.classList.remove('hidden');
            const btnApply = document.getElementById('btn-bulk-ocr-apply');
            if (btnApply) btnApply.disabled = (matchedCount === 0);
        }

        function updateBulkItemRoom(idx, roomId) {
            if (bulkOcrState.results[idx]) {
                bulkOcrState.results[idx].room_id = roomId ? parseInt(roomId) : null;
                const room = bulkOcrState.allRooms.find(r => r.id == roomId);
                bulkOcrState.results[idx].room_number = room ? room.room_number : null;
                bulkOcrState.results[idx].is_matched = !!roomId;
                
                const matchedCount = bulkOcrState.results.filter(r => r.room_id).length;
                const statsBadge = document.getElementById('bulk-match-stats-badge');
                if (statsBadge) {
                    statsBadge.textContent = `Khớp thành công ${matchedCount}/${bulkOcrState.results.length}`;
                }
                const btnApply = document.getElementById('btn-bulk-ocr-apply');
                if (btnApply) btnApply.disabled = (matchedCount === 0);
            }
        }

        function updateBulkItemValue(idx, val) {
            if (bulkOcrState.results[idx]) {
                bulkOcrState.results[idx].value = parseInt(val) || 0;
            }
        }

        function removeBulkItem(idx) {
            bulkOcrState.results.splice(idx, 1);
            renderBulkOcrResults();
        }

        function applyBulkOcrToTable() {
            const validItems = bulkOcrState.results.filter(item => item.room_id && item.value !== null);
            if (validItems.length === 0) {
                alert('Không có phòng nào được chọn để áp dụng!');
                return;
            }

            let appliedCount = 0;
            validItems.forEach(item => {
                const tr = document.querySelector(`tr[data-room-id="${item.room_id}"]`);
                if (tr) {
                    let input = null;
                    if (bulkOcrState.type === 'electricity') {
                        input = tr.querySelector('.new-elec-input');
                    } else {
                        input = tr.querySelector('.new-water-input');
                    }

                    if (input) {
                        input.value = item.value;
                        calculateRowCost(input);
                        
                        const ringClass = bulkOcrState.type === 'electricity' ? 'ring-emerald-500' : 'ring-cyan-500';
                        input.classList.add('ring-2', ringClass);
                        setTimeout(() => {
                            input.classList.remove('ring-2', ringClass);
                        }, 2500);

                        appliedCount++;
                    }
                }
            });

            closeBulkOcrModal();
            alert(`🎉 Đã áp dụng thành công chỉ số cho ${appliedCount} phòng trên bảng chốt số!`);
        }

        // Kéo thả Dropzone listener
        document.addEventListener('DOMContentLoaded', function() {
            const dropzone = document.getElementById('bulk-ocr-dropzone');
            if (dropzone) {
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.add('border-indigo-500', 'bg-indigo-500/10');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.remove('border-indigo-500', 'bg-indigo-500/10');
                    }, false);
                });

                dropzone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    if (files && files.length > 0) {
                        loadBulkOcrFilesList(files);
                    }
                }, false);
            }
        });

        // =======================================================
        // 3. IOT SMART METERING & REALTIME MONITORING (LORAWAN / ESP32 / MODBUS)
        // =======================================================
        const iotState = {
            currentTab: 'realtime',
            currentRoomId: null,
            currentRange: '24h',
            chartData: { electric: [], water: [] }
        };

        function openIotDashboardModal() {
            const modal = document.getElementById('iot-metering-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
                loadIotSummary();
                const roomSelect = document.getElementById('iot-room-filter-select');
                if (roomSelect && roomSelect.options.length > 1 && !roomSelect.value) {
                    roomSelect.selectedIndex = 1;
                    onIotRoomFilterChange(roomSelect.value);
                }
            }
        }

        function closeIotDashboardModal() {
            const modal = document.getElementById('iot-metering-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        function switchIotTab(tab) {
            iotState.currentTab = tab;
            ['realtime', 'devices', 'simulator'].forEach(t => {
                const el = document.getElementById(`iot-tab-${t}`);
                const btn = document.getElementById(`iot-tab-btn-${t}`);
                if (el) {
                    if (t === tab) {
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                }
                if (btn) {
                    if (t === tab) {
                        btn.className = 'px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-emerald-500/10 text-emerald-300 border border-emerald-500/30';
                    } else {
                        btn.className = 'px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-slate-200 border border-transparent';
                    }
                }
            });

            if (tab === 'devices') {
                loadIotSummary();
            }
        }

        async function loadIotSummary() {
            try {
                const response = await fetch("{{ route('smartroom.admin.iot.summary') }}", {
                    headers: { 'Accept': 'application/json' }
                });
                const res = await response.json();
                if (res.success && res.data) {
                    const d = res.data;
                    document.getElementById('iot-kpi-power').innerHTML = `${d.total_power_kw} <span class="text-xs font-bold text-slate-400">kW</span>`;
                    document.getElementById('iot-kpi-water').innerHTML = `${d.total_water_flow} <span class="text-xs font-bold text-slate-400">m3/h</span>`;
                    document.getElementById('iot-kpi-devices').textContent = `${d.online_devices} / ${d.total_devices}`;
                    document.getElementById('iot-kpi-status-hint').textContent = `${d.offline_devices} thiết bị Offline`;
                    document.getElementById('iot-kpi-warnings').textContent = d.warning_devices;

                    const tbody = document.getElementById('iot-devices-table-body');
                    if (tbody && d.devices) {
                        if (d.devices.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-6 text-slate-500">Chưa có thiết bị công tơ nào được kích hoạt. Hãy dùng Bộ Giả Lập để phát gói tin thử nghiệm!</td></tr>';
                        } else {
                            tbody.innerHTML = d.devices.map(dev => {
                                const statusBadge = dev.is_online
                                    ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><i class="fa-solid fa-circle text-[6px] mr-1"></i>Online</span>'
                                    : '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20"><i class="fa-solid fa-circle text-[6px] mr-1"></i>Offline</span>';
                                const typeIcon = dev.meter_type === 'electricity' ? '<i class="fa-solid fa-bolt text-amber-400"></i> Điện' : '<i class="fa-solid fa-droplet text-cyan-400"></i> Nước';
                                const unit = dev.meter_type === 'electricity' ? 'kWh' : 'm3';

                                return `
                                    <tr class="hover:bg-slate-900/30 transition-all font-mono">
                                        <td class="px-4 py-3 font-bold text-slate-200">${dev.device_code}</td>
                                        <td class="px-4 py-3 text-slate-400 font-sans">${dev.protocol}</td>
                                        <td class="px-4 py-3 font-sans font-bold text-emerald-400">P.${dev.room_number}</td>
                                        <td class="px-4 py-3 text-slate-400">${dev.meter_serial}</td>
                                        <td class="px-4 py-3 font-sans">${typeIcon}</td>
                                        <td class="px-4 py-3 font-bold text-slate-200">${dev.last_reading} ${unit}</td>
                                        <td class="px-4 py-3 font-sans">${statusBadge}</td>
                                        <td class="px-4 py-3 text-slate-500 font-sans">${dev.last_seen}</td>
                                    </tr>
                                `;
                            }).join('');
                        }
                    }
                }
            } catch (err) {
                console.error('Lỗi khi tải IoT Summary:', err);
            }
        }

        function viewRoomIotChart(roomId, roomNumber) {
            openIotDashboardModal();
            switchIotTab('realtime');
            const roomSelect = document.getElementById('iot-room-filter-select');
            if (roomSelect) {
                roomSelect.value = roomId;
            }
            onIotRoomFilterChange(roomId);
        }

        async function onIotRoomFilterChange(roomId) {
            if (!roomId) {
                document.getElementById('iot-chart-empty-state')?.classList.remove('hidden');
                document.getElementById('iot-room-stats-banner')?.classList.add('hidden');
                clearIotCanvas();
                return;
            }

            iotState.currentRoomId = roomId;
            try {
                const res = await fetch(`/smartroom/admin/iot/rooms/${roomId}/realtime?range=${iotState.currentRange}`);
                const json = await res.json();
                if (json.success && json.data) {
                    const data = json.data;
                    document.getElementById('iot-chart-empty-state')?.classList.add('hidden');
                    document.getElementById('iot-room-stats-banner')?.classList.remove('hidden');
                    document.getElementById('iot-chart-title').textContent = `Phụ Tải Phòng ${data.room.room_number} (Số SX Điện: ${data.room.electric_serial || 'N/A'})`;

                    document.getElementById('iot-stat-elec-val').textContent = (data.latest.electric_reading !== null ? data.latest.electric_reading : 'N/A') + ' kWh';
                    document.getElementById('iot-stat-water-val').textContent = (data.latest.water_reading !== null ? data.latest.water_reading : 'N/A') + ' m3';
                    document.getElementById('iot-stat-cost-val').textContent = (data.consumption_today.estimated_cost || 0).toLocaleString('vi-VN') + 'đ';

                    iotState.chartData = data.series;
                    renderIotCanvasChart(data.series.electricity || [], data.series.water || []);
                }
            } catch (e) {
                console.error('Lỗi tải dữ liệu phòng:', e);
            }
        }

        function changeIotChartRange(range) {
            iotState.currentRange = range;
            if (iotState.currentRoomId) {
                onIotRoomFilterChange(iotState.currentRoomId);
            }
        }

        function clearIotCanvas() {
            const canvas = document.getElementById('iot-realtime-chart-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function renderIotCanvasChart(electricData, waterData) {
            const canvas = document.getElementById('iot-realtime-chart-canvas');
            if (!canvas) return;

            const rect = canvas.getBoundingClientRect();
            canvas.width = rect.width * (window.devicePixelRatio || 1) || 600;
            canvas.height = rect.height * (window.devicePixelRatio || 1) || 240;
            const ctx = canvas.getContext('2d');
            ctx.scale(window.devicePixelRatio || 1, window.devicePixelRatio || 1);

            const width = rect.width;
            const height = rect.height;
            const padding = { top: 25, right: 30, bottom: 35, left: 45 };

            ctx.clearRect(0, 0, width, height);

            ctx.strokeStyle = '#1e293b';
            ctx.lineWidth = 1;
            for (let i = 0; i < 5; i++) {
                const y = padding.top + ((height - padding.top - padding.bottom) / 4) * i;
                ctx.beginPath();
                ctx.moveTo(padding.left, y);
                ctx.lineTo(width - padding.right, y);
                ctx.stroke();
            }

            if (electricData.length === 0 && waterData.length === 0) {
                ctx.fillStyle = '#64748b';
                ctx.font = '12px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('Chưa có chuỗi đo đạc 15 phút nào trong khung thời gian này', width / 2, height / 2);
                return;
            }

            if (electricData.length > 0) {
                const powers = electricData.map(p => p.power || 0);
                const maxPower = Math.max(...powers, 1500);
                const chartH = height - padding.top - padding.bottom;
                const chartW = width - padding.left - padding.right;

                ctx.strokeStyle = '#fbbf24';
                ctx.lineWidth = 2.5;
                ctx.beginPath();

                electricData.forEach((pt, idx) => {
                    const x = padding.left + (chartW / Math.max(1, electricData.length - 1)) * idx;
                    const y = height - padding.bottom - (pt.power / maxPower) * chartH;
                    if (idx === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                });
                ctx.stroke();

                electricData.forEach((pt, idx) => {
                    const x = padding.left + (chartW / Math.max(1, electricData.length - 1)) * idx;
                    const y = height - padding.bottom - (pt.power / maxPower) * chartH;
                    ctx.fillStyle = '#f59e0b';
                    ctx.beginPath();
                    ctx.arc(x, y, 3.5, 0, Math.PI * 2);
                    ctx.fill();
                });

                ctx.fillStyle = '#fbbf24';
                ctx.font = '10px monospace';
                ctx.textAlign = 'right';
                ctx.fillText(`${Math.round(maxPower)}W`, padding.left - 5, padding.top + 5);
                ctx.fillText('0W', padding.left - 5, height - padding.bottom);
            }

            if (waterData.length > 0) {
                const flows = waterData.map(p => p.flow_rate || 0);
                const maxFlow = Math.max(...flows, 5);
                const chartH = height - padding.top - padding.bottom;
                const chartW = width - padding.left - padding.right;

                ctx.strokeStyle = '#06b6d4';
                ctx.lineWidth = 2;
                ctx.setLineDash([4, 3]);
                ctx.beginPath();

                waterData.forEach((pt, idx) => {
                    const x = padding.left + (chartW / Math.max(1, waterData.length - 1)) * idx;
                    const y = height - padding.bottom - (pt.flow_rate / maxFlow) * chartH;
                    if (idx === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                });
                ctx.stroke();
                ctx.setLineDash([]);
            }
        }

        function onSimMeterTypeChange(val) {
            const elecInputs = document.getElementById('sim-elec-inputs');
            const waterInputs = document.getElementById('sim-water-inputs');
            const readingInp = document.getElementById('sim-reading');

            if (val === 'electricity') {
                elecInputs?.classList.remove('hidden');
                waterInputs?.classList.add('hidden');
                if (readingInp) readingInp.value = 1420.5;
            } else {
                elecInputs?.classList.add('hidden');
                waterInputs?.classList.remove('hidden');
                if (readingInp) readingInp.value = 48.2;
            }
        }

        async function sendIotSimulation(event) {
            event.preventDefault();
            const btn = document.getElementById('btn-send-sim');
            const form = document.getElementById('iot-simulator-form');
            if (!form) return;

            const formData = new FormData(form);
            const payload = Object.fromEntries(formData.entries());

            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang truyền telemetry...';

            try {
                const response = await fetch("{{ route('smartroom.admin.iot.simulate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const res = await response.json();
                if (res.success) {
                    alert('🎉 ' + res.message);
                    loadIotSummary();
                    if (payload.room_id) {
                        const roomSelect = document.getElementById('iot-room-filter-select');
                        if (roomSelect) roomSelect.value = payload.room_id;
                        onIotRoomFilterChange(payload.room_id);
                    }
                } else {
                    alert('Lỗi: ' + (res.message || 'Không thể gửi gói tin'));
                }
            } catch (err) {
                console.error(err);
                alert('Có lỗi xảy ra khi kết nối máy chủ IoT!');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        async function triggerIotAutoSync(btn) {
            if (!confirm('Bạn có chắc chắn muốn CHỐT SỐ TỰ ĐỘNG TỪ IOT cho toàn bộ các phòng vào hóa đơn tháng này?')) {
                return;
            }

            const originalHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang đồng bộ...';
            }

            try {
                const response = await fetch("{{ route('smartroom.admin.iot.sync_billing') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const res = await response.json();
                if (res.success) {
                    alert(`⚡ ĐỒNG BỘ THÀNH CÔNG!\n${res.message}`);
                    window.location.href = "{{ route('smartroom.admin', ['tab' => 'utility-section']) }}";
                } else {
                    alert('Lỗi: ' + (res.message || 'Không thể đồng bộ chốt số'));
                }
            } catch (e) {
                console.error(e);
                alert('Có lỗi xảy ra khi gọi API chốt số IoT!');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            }
        }

        function applyIotReadingToInput(btn, value) {
            const row = btn.closest('tr');
            if (!row) return;
            const input = btn.previousElementSibling;
            if (input && input.tagName === 'INPUT') {
                input.value = value;
                calculateRowCost(input);
                input.classList.add('ring-2', 'ring-emerald-400');
                setTimeout(() => input.classList.remove('ring-2', 'ring-emerald-400'), 2000);
            }
        }

        // Resident search
        function searchResidentTable() {

            const query = document.querySelector('input[name="resident_q"]')?.value.toLowerCase() || '';
            const rows = document.getElementById('resident-table-body').querySelectorAll('tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if(text.includes(query)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        }

        // Resident delete simulation
        function deleteRow(btn) {
            if(confirm('Bạn có chắc chắn muốn xóa cư dân này ra khỏi phòng trọ?')) {
                btn.closest('tr').remove();
            }
        }

        // Resident add modal triggers
        function toggleAddResidentModal(show) {
            const modal = document.getElementById('add-resident-modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function submitAddResident(e) {
            e.preventDefault();
            const name = document.getElementById('add-res-name').value;
            const phone = document.getElementById('add-res-phone').value;
            const room = document.getElementById('add-res-room').value;
            const dateStr = document.getElementById('add-res-date').value;
            
            const date = new Date(dateStr);
            const formattedDate = date.getDate().toString().padStart(2, '0') + '/' + (date.getMonth() + 1).toString().padStart(2, '0') + '/' + date.getFullYear();

            // Insert row to resident table
            const tbody = document.getElementById('resident-table-body');
            const tr = document.createElement('tr');
            tr.className = "hover:bg-slate-900/30 transition-all";
            tr.innerHTML = `
                <td class="px-6 py-4 font-bold text-slate-200">${name}</td>
                <td class="px-6 py-4 text-xs font-semibold text-indigo-400">P. ${room}</td>
                <td class="px-6 py-4 text-xs text-slate-400">${phone}</td>
                <td class="px-6 py-4 text-xs text-slate-500">${formattedDate}</td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Đang hoạt động</span>
                </td>
                <td class="px-6 py-4 flex gap-2 justify-center">
                    <button class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-xs font-bold text-indigo-400 transition-all"><i class="fa-regular fa-pen-to-square"></i> Sửa</button>
                    <button onclick="deleteRow(this)" class="px-2.5 py-1.5 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 rounded-lg text-xs font-bold text-rose-400 transition-all"><i class="fa-regular fa-trash-can"></i> Xóa</button>
                </td>
            `;
            tbody.appendChild(tr);
            
            // Update the room card status in Sơ đồ phòng
            const roomCards = document.querySelectorAll('.room-card');
            roomCards.forEach(card => {
                if (card.querySelector('span').textContent.trim() === 'P. ' + room) {
                    // Update card design
                    card.classList.remove('room-empty');
                    card.classList.add('room-occupied');
                    
                    const badge = card.querySelector('span:nth-child(2)');
                    badge.textContent = 'Đã thuê';
                    badge.className = 'px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-500/10 text-red-400 border border-red-500/20';
                    
                    card.querySelector('h4').textContent = 'Cư dân: ' + name;
                    card.querySelector('h4').className = 'text-xs font-bold text-slate-400 truncate mb-1';
                    
                    // Attach click handler with new details
                    card.onclick = function() {
                        openRoomDetail(room, 'occupied', name, phone, '4.000.000đ', '0 kWh', '0 m3');
                    };
                }
            });

            // Close modal
            toggleAddResidentModal(false);
            alert(`Đã thêm thành công cư dân ${name} vào phòng ${room}!`);
            
            // Clear inputs
            document.getElementById('add-res-name').value = '';
            document.getElementById('add-res-phone').value = '';
            document.getElementById('add-res-date').value = '';
        }

        // ==========================================
        // 1. CƠ CHẾ KHÓA ĐỒNG THỜI (OPTIMISTIC LOCKING)
        // Giải thích: Server và Client truyền tay nhau thuộc tính 'version' (phiên bản dữ liệu).
        // Khi Landlord sửa dữ liệu cư dân, giá trị version hiện tại sẽ được gửi kèm lên Server.
        // Server kiểm tra xem version đó có khớp với database không. Nếu một Landlord khác đã lưu trước đó,
        // version trong database sẽ tăng lên, dẫn đến xung đột phiên bản và Server sẽ chặn cập nhật (HTTP 409).
        // Dưới client, nếu nhận được HTTP 409 hoặc lỗi 422, ta sẽ hiển thị cảnh báo cho người dùng reload lại trang.
        // ==========================================

        // Edit resident modal triggers
        function toggleEditResidentModal(show) {
            const modal = document.getElementById('edit-resident-modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function openEditResidentModal(id, name, phone, roomId, startDate, dob, cccd, hometown, tempStatus, version) {
            const form = document.getElementById('edit-resident-form');
            form.action = `/smartroom/admin/resident/${id}`;
            
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-phone').value = phone;
            document.getElementById('edit-room-id').value = roomId;
            document.getElementById('edit-start-date').value = startDate;
            
            // Điền thông tin cá nhân mở rộng
            document.getElementById('edit-dob').value = dob || '';
            document.getElementById('edit-cccd').value = cccd || '';
            document.getElementById('edit-hometown').value = hometown || '';
            document.getElementById('edit-temp-status').value = tempStatus || 'none';
            
            // Optimistic Locking: Gán version hiện tại của bản ghi
            document.getElementById('edit-version').value = version || 1;
            
            toggleEditResidentModal(true);
        }

        // View resident modal triggers
        function toggleViewResidentModal(show) {
            const modal = document.getElementById('view-resident-modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function openViewResidentModal(id, name, phone, email, room, startDate, status, dob, cccd, hometown, tempStatus) {
            document.getElementById('view-name').textContent = name;
            document.getElementById('view-phone').textContent = phone;
            document.getElementById('view-email').textContent = email || 'Chưa cung cấp';
            document.getElementById('view-room').textContent = 'Phòng ' + room;
            
            // Gán dữ liệu mở rộng
            document.getElementById('view-dob').textContent = dob ? formatDateString(dob) : 'Chưa cập nhật';
            document.getElementById('view-cccd').textContent = cccd || 'Chưa cập nhật';
            document.getElementById('view-hometown').textContent = hometown || 'Chưa cập nhật';
            
            const tempBadge = document.getElementById('view-temp-status');
            if (tempStatus === 'registered') {
                tempBadge.textContent = 'Đã đăng ký';
                tempBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
            } else if (tempStatus === 'absent') {
                tempBadge.textContent = 'Tạm vắng';
                tempBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20';
            } else {
                tempBadge.textContent = 'Chưa ĐK';
                tempBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20';
            }

            // Gán nút cấu hình người thân đi cùng
            const manageBtn = document.getElementById('view-manage-relatives-btn');
            manageBtn.onclick = function() {
                toggleViewResidentModal(false);
                openRelativesModal(id, name);
            };

            // Tải danh sách người thân trọ cùng qua AJAX
            loadRelativesForView(id);
            
            toggleViewResidentModal(true);
        }

        function formatDateString(dateStr) {
            if (!dateStr) return '—';
            try {
                const date = new Date(dateStr);
                if (isNaN(date.getTime())) return dateStr;
                return date.getDate().toString().padStart(2, '0') + '/' + (date.getMonth() + 1).toString().padStart(2, '0') + '/' + date.getFullYear();
            } catch(e) {
                return dateStr;
            }
        }

        // Tải danh sách người thân chỉ để hiển thị trong View Modal
        function loadRelativesForView(residentId) {
            const table = document.getElementById('view-relatives-table');
            const tbody = document.getElementById('view-relatives-tbody');
            const emptyDiv = document.getElementById('view-relatives-empty');

            tbody.innerHTML = '';
            table.classList.add('hidden');
            emptyDiv.classList.remove('hidden');

            fetch(`/smartroom/admin/resident/${residentId}/relatives`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.relatives.length > 0) {
                        data.relatives.forEach(relative => {
                            let tempBadgeHTML = '';
                            if (relative.temporary_residence_status === 'registered') {
                                tempBadgeHTML = '<span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400">Đã ĐK</span>';
                            } else if (relative.temporary_residence_status === 'absent') {
                                tempBadgeHTML = '<span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/10 text-amber-400">Tạm vắng</span>';
                            } else {
                                tempBadgeHTML = '<span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-500/10 text-rose-400">Chưa ĐK</span>';
                            }

                            const tr = document.createElement('tr');
                            tr.className = "hover:bg-slate-900/10 transition-all border-b border-slate-800 last:border-0";
                            tr.innerHTML = `
                                <td class="py-2 text-slate-200 font-semibold text-xs">${relative.name}</td>
                                <td class="py-2 text-slate-400 text-xs">${relative.relationship}</td>
                                <td class="py-2 text-slate-400 text-xs font-mono">${relative.cccd || '—'}</td>
                                <td class="py-2">${tempBadgeHTML}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                        table.classList.remove('hidden');
                        emptyDiv.classList.add('hidden');
                    }
                })
                .catch(err => console.error("Error fetching relatives for view:", err));
        }

        // ==========================================
        // RELATIVES AJAX CRUD LOGIC (Quản lý người thân tạm trú)
        // ==========================================
        let currentResidentId = null;

        function toggleRelativesModal(show) {
            const modal = document.getElementById('relatives-modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
                resetRelativeForm();
            }
        }

        function openRelativesModal(residentId, residentName) {
            currentResidentId = residentId;
            document.getElementById('relatives-modal-resident-name').textContent = residentName;
            
            // Load danh sách người thân hiện tại
            loadRelativesList();
            toggleRelativesModal(true);
        }

        function loadRelativesList() {
            const container = document.getElementById('relatives-list-container');
            container.innerHTML = `
                <div class="flex items-center justify-center py-10">
                    <div class="w-6 h-6 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            `;

            fetch(`/smartroom/admin/resident/${currentResidentId}/relatives`)
                .then(res => res.json())
                .then(data => {
                    container.innerHTML = '';
                    if (data.success && data.relatives.length > 0) {
                        data.relatives.forEach(rel => {
                            let statusText = 'Chưa ĐK';
                            let statusClass = 'bg-rose-500/10 text-rose-400 border border-rose-500/20';
                            if (rel.temporary_residence_status === 'registered') {
                                statusText = 'Đã ĐK';
                                statusClass = 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                            } else if (rel.temporary_residence_status === 'absent') {
                                statusText = 'Tạm vắng';
                                statusClass = 'bg-amber-500/10 text-amber-400 border border-amber-500/20';
                            }

                            const card = document.createElement('div');
                            card.className = "flex items-center justify-between p-3 rounded-xl bg-slate-950/40 border border-slate-900 hover:border-slate-800 transition-all";
                            
                            // Tránh lỗi ký tự đặc biệt khi parse JSON trong inline onclick
                            const relEscaped = JSON.stringify(rel).replace(/'/g, "\\'").replace(/"/g, "&quot;");
                            
                            const deleteButton = currentUserIsLandlord ? `
                                    <button onclick="deleteRelative(${rel.id})" class="w-7 h-7 rounded-lg bg-slate-900 hover:bg-rose-950/20 border border-slate-800 hover:border-rose-900/50 flex items-center justify-center text-rose-400 transition-all" title="Xoa">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    </button>
                            ` : '';

                            card.innerHTML = `
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-xs font-bold text-slate-200">${rel.name}</h4>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-500/10 text-indigo-400 font-semibold">${rel.relationship}</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded ${statusClass} font-bold">${statusText}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 mt-1 flex gap-3">
                                        <span>CCCD: <strong class="font-mono text-slate-400">${rel.cccd || '—'}</strong></span>
                                        <span>Sinh: <strong class="font-mono text-slate-400">${rel.dob ? formatDateString(rel.dob) : '—'}</strong></span>
                                        <span class="truncate max-w-[150px]" title="${rel.hometown}">Quê: <strong class="text-slate-400">${rel.hometown || '—'}</strong></span>
                                    </div>
                                </div>
                                <div class="flex gap-1.5">
                                    <button onclick="editRelative(JSON.parse('${JSON.stringify(rel).replace(/'/g, "\\'")}'))" class="w-7 h-7 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 flex items-center justify-center text-cyan-400 transition-all" title="Sửa">
                                        <i class="fa-solid fa-pencil text-[10px]"></i>
                                    </button>
                                    ${deleteButton}
                                </div>
                            `;
                            container.appendChild(card);
                        });
                    } else {
                        container.innerHTML = `
                            <div class="flex flex-col items-center justify-center py-12 text-slate-500 text-xs">
                                <i class="fa-solid fa-people-arrows text-2xl mb-2 text-slate-600"></i>
                                <span>Chưa có người thân tạm trú nào đăng ký cùng.</span>
                            </div>
                        `;
                    }
                })
                .catch(err => {
                    container.innerHTML = `<p class="text-xs text-rose-400 py-4 text-center">Không thể tải danh sách người thân!</p>`;
                    console.error(err);
                });
        }

        function resetRelativeForm() {
            document.getElementById('relative-id').value = '';
            document.getElementById('relative-version').value = '1';
            document.getElementById('relative-name').value = '';
            document.getElementById('relative-relationship').value = '';
            document.getElementById('relative-dob').value = '';
            document.getElementById('relative-cccd').value = '';
            document.getElementById('relative-hometown').value = '';
            document.getElementById('relative-temp-status').value = 'none';

            document.getElementById('relative-form-title').innerHTML = '<i class="fa-solid fa-user-plus text-indigo-400"></i> Thêm Người Thân Mới';
            document.getElementById('relative-submit-btn').innerHTML = '<i class="fa-solid fa-save"></i> <span>Lưu Lại</span>';
            document.getElementById('relative-reset-btn').classList.add('hidden');
            document.getElementById('relative-submit-btn').className = "w-full py-2 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all flex items-center justify-center gap-1.5";
        }

        function editRelative(rel) {
            document.getElementById('relative-id').value = rel.id;
            document.getElementById('relative-version').value = rel.version;
            document.getElementById('relative-name').value = rel.name;
            document.getElementById('relative-relationship').value = rel.relationship;
            document.getElementById('relative-dob').value = rel.dob || '';
            document.getElementById('relative-cccd').value = rel.cccd || '';
            document.getElementById('relative-hometown').value = rel.hometown || '';
            document.getElementById('relative-temp-status').value = rel.temporary_residence_status || 'none';

            document.getElementById('relative-form-title').innerHTML = '<i class="fa-solid fa-user-pen text-cyan-400"></i> Cập Nhật Người Thân';
            document.getElementById('relative-submit-btn').innerHTML = '<i class="fa-solid fa-save"></i> <span>Cập Nhật</span>';
            document.getElementById('relative-reset-btn').classList.remove('hidden');
            document.getElementById('relative-submit-btn').className = "w-1/2 py-2 rounded-lg text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 shadow-md shadow-cyan-600/20 transition-all flex items-center justify-center gap-1.5";
        }

        function saveRelative(e) {
            e.preventDefault();
            const relativeId = document.getElementById('relative-id').value;
            const submitBtn = document.getElementById('relative-submit-btn');

            const payload = {
                name: document.getElementById('relative-name').value,
                relationship: document.getElementById('relative-relationship').value,
                dob: document.getElementById('relative-dob').value,
                cccd: document.getElementById('relative-cccd').value,
                hometown: document.getElementById('relative-hometown').value,
                temporary_residence_status: document.getElementById('relative-temp-status').value,
                version: document.getElementById('relative-version').value
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const originalHTML = submitBtn.innerHTML;
            
            // Chống click liên tiếp
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i>';

            let url = `/smartroom/admin/resident/${currentResidentId}/relative`;
            let method = 'POST';

            if (relativeId) {
                url = `/smartroom/admin/relative/${relativeId}`;
                method = 'PUT';
            }

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            })
            .then(async res => {
                const isJson = res.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await res.json() : null;

                if (res.status === 409) {
                    // Xử lý xung đột phiên bản dữ liệu (Optimistic Locking)
                    alert("❌ Lỗi Xung Đột Phiên Bản (Optimistic Locking):\nDữ liệu của người thân này đã bị thay đổi bởi một quản trị viên khác. Vui lòng đóng và mở lại danh sách để nhận dữ liệu mới nhất.");
                    return false;
                }

                if (!res.ok) {
                    throw new Error(data?.message || "Lỗi xử lý API!");
                }

                return data;
            })
            .then(data => {
                if (data) {
                    alert(data.message || "Đã lưu thông tin người thân thành công!");
                    resetRelativeForm();
                    loadRelativesList();
                }
            })
            .catch(err => {
                alert("Lỗi: " + err.message);
                console.error(err);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
            });
        }

        function deleteRelative(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa thông tin người thân này?')) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            fetch(`/smartroom/admin/relative/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || "Đã xóa thành công!");
                    loadRelativesList();
                } else {
                    alert("Có lỗi xảy ra: " + (data.message || "Không thể xóa!"));
                }
            })
            .catch(err => {
                alert("Không thể kết nối máy chủ để xóa!");
                console.error(err);
            });
        }

        // ==========================================
        // 2. CHỐNG SPAM CLICK (ANTI-SPAM frontend)
        // Giải thích: Vô hiệu hóa nút nhấn hoặc form submit ngay sau click đầu tiên,
        // ngăn chặn việc gửi trùng lặp nhiều request cùng lúc khi mạng chậm.
        // ==========================================
        function antiSpamSubmit(form) {
            const btn = form.querySelector('.anti-spam-btn') || form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                const origHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang xử lý...';
            }
            return true;
        }

        function confirmAndDisable(form, message) {
            if (confirm(message)) {
                const btn = form.querySelector('.anti-spam-btn') || form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i>';
                }
                return true;
            }
            return false;
        }

        // ==========================================
        // 3. CHỐNG MỞ F12 & CÔNG CỤ NHÀ PHÁT TRIỂN (ANTI-F12 / DEVTOOLS)
        // Giải thích: Hạn chế người dùng hoặc hacker táy máy thay đổi DOM, xem mã nguồn,
        // hoặc bắt các request API nhạy cảm bằng cách chặn phím tắt và theo dõi DevTools.
        // ==========================================
        (function() {
            // Chặn phím tắt mở Developer Tools
            window.addEventListener('keydown', function(e) {
                // F12
                if (e.keyCode === 123) {
                    e.preventDefault();
                    showDevToolsWarning();
                    return false;
                }
                // Ctrl+Shift+I
                if (e.ctrlKey && e.shiftKey && e.keyCode === 73) {
                    e.preventDefault();
                    showDevToolsWarning();
                    return false;
                }
                // Ctrl+Shift+J
                if (e.ctrlKey && e.shiftKey && e.keyCode === 74) {
                    e.preventDefault();
                    showDevToolsWarning();
                    return false;
                }
                // Ctrl+U (Xem nguồn trang)
                if (e.ctrlKey && e.keyCode === 85) {
                    e.preventDefault();
                    showDevToolsWarning();
                    return false;
                }
            });

            // Chặn chuột phải
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                showDevToolsWarning();
                return false;
            });

            // Phát hiện DevTools mở bằng sự khác biệt về kích thước màn hình
            const devtools = {
                isOpen: false,
                orientation: undefined
            };
            const threshold = 160;
            
            setInterval(function() {
                const widthThreshold = window.outerWidth - window.innerWidth > threshold;
                const heightThreshold = window.outerHeight - window.innerHeight > threshold;
                
                if (widthThreshold || heightThreshold) {
                    if (!devtools.isOpen) {
                        devtools.isOpen = true;
                        console.warn("Cảnh báo: Phát hiện bảng điều khiển DevTools!");
                    }
                } else {
                    devtools.isOpen = false;
                }
            }, 1000);

            function showDevToolsWarning() {
                console.warn("🔐 Chức năng F12 và chuột phải đã bị khóa để bảo mật trang Quản trị.");
            }
        })();

        // CHARTS INITIALIZATION
        window.addEventListener('DOMContentLoaded', () => {
            // Revenue Chart
            const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
            const gradRev = ctxRevenue.createLinearGradient(0, 0, 0, 300);
            gradRev.addColorStop(0, '#6366f1');
            gradRev.addColorStop(1, '#4f46e5');

            new Chart(ctxRevenue, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartMonths) !!},
                    datasets: [{
                        label: 'Doanh thu (VND)',
                        data: {!! json_encode($chartRevenue) !!},
                        backgroundColor: gradRev,
                        hoverBackgroundColor: '#818cf8',
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 16
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            grid: { color: 'rgba(255, 255, 255, 0.03)' },
                            ticks: {
                                color: '#64748b',
                                font: { size: 10, weight: 'bold' },
                                callback: function(value) { return (value / 1000000) + 'M'; }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#64748b',
                                font: { size: 10, weight: 'bold' }
                            }
                        }
                    }
                }
            });

            // Revenue Breakdown Doughnut Chart
            const ctxBreakdown = document.getElementById('revenueBreakdownChart').getContext('2d');
            let breakdownChart = new Chart(ctxBreakdown, {
                type: 'doughnut',
                data: {
                    labels: ['Tiền phòng', 'Tiền điện', 'Tiền nước', 'Dịch vụ'],
                    datasets: [{
                        data: [0, 0, 0, 0],
                        backgroundColor: [
                            '#6366f1',
                            '#f59e0b',
                            '#06b6d4',
                            '#10b981'
                        ],
                        borderWidth: 2,
                        borderColor: '#0a0f1d',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed !== null) {
                                        label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            fetch('/api/revenue-breakdown')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const b = data.breakdown;
                        const p = data.percentages;
                        breakdownChart.data.datasets[0].data = [b.room, b.electric, b.water, b.service];
                        breakdownChart.update();
                        
                        document.getElementById('breakdown-total-txt').textContent = (data.total / 1000000).toFixed(1) + 'M';
                        document.getElementById('breakdown-room-pct').textContent = p.room + '%';
                        document.getElementById('breakdown-elec-pct').textContent = p.electric + '%';
                        document.getElementById('breakdown-water-pct').textContent = p.water + '%';
                        document.getElementById('breakdown-service-pct').textContent = p.service + '%';
                    }
                })
                .catch(err => console.error("Error loading revenue breakdown:", err));

            // Status Pie Chart
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Trống', 'Đang thuê', 'Nợ tiền'],
                    datasets: [{
                        data: [{{ $emptyRooms }}, {{ $occupiedRooms }}, {{ $overdueRooms }}],
                        backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                        borderColor: '#0a0f1d',
                        borderWidth: 4,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((sum, item) => sum + item, 0);
                                    const value = context.parsed || 0;
                                    const percent = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${context.label}: ${value} phòng (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Trigger calculation for any pre-populated utility inputs
            document.querySelectorAll('.new-elec-input').forEach(input => {
                if (input.value) {
                    calculateRowCost(input);
                }
            });

            // Auto-switch to tab from query param if provided (e.g. ?tab=utility-section)
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam) {
                const targetLink = document.querySelector(`.sidebar-nav-link[data-section="${tabParam}"]`);
                if (targetLink) {
                    switchTab(tabParam, targetLink);
                }
                // Clean the URL parameter without reloading the page
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({ path: newUrl }, '', newUrl);
            }
        });

        function showVietQR(billId) {
            if (!canReceiveOnlinePayments) {
                return requireKycForOnlinePayments();
            }

            const qrModal = document.getElementById('vietqr-modal');
            document.getElementById('qr-modal-loading').classList.remove('hidden');
            document.getElementById('qr-modal-content').classList.add('hidden');
            qrModal.classList.remove('hidden');

            fetch(`/api/utility-bill/${billId}/qr`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('qr-modal-room').textContent = `Phòng ${data.room_number}`;
                        document.getElementById('qr-modal-tenant').textContent = data.resident_name;
                        document.getElementById('qr-modal-amount').textContent = data.amount.toLocaleString('vi-VN') + "đ";
                        document.getElementById('qr-modal-bank').textContent = `${data.bank_id} - ${data.account_no}`;
                        document.getElementById('qr-modal-name').textContent = data.account_name;
                        document.getElementById('qr-modal-desc').textContent = decodeURIComponent(data.description);
                        document.getElementById('qr-modal-image').src = data.qr_url;
                        
                        document.getElementById('qr-modal-download').href = data.qr_url;
                        
                        document.getElementById('qr-modal-loading').classList.add('hidden');
                        document.getElementById('qr-modal-content').classList.remove('hidden');
                    } else {
                        alert("Không thể tải mã QR: " + (data.error || "Lỗi không xác định"));
                        qrModal.classList.add('hidden');
                    }
                })
                .catch(err => {
                    alert("Có lỗi xảy ra khi kết nối API!");
                    console.error(err);
                    qrModal.classList.add('hidden');
                });
        }

        function showVietQRFallback(roomNum, amount) {
            if (!canReceiveOnlinePayments) {
                return requireKycForOnlinePayments();
            }

            const qrModal = document.getElementById('vietqr-modal');
            document.getElementById('qr-modal-loading').classList.add('hidden');
            document.getElementById('qr-modal-content').classList.remove('hidden');
            qrModal.classList.remove('hidden');

            const amt = parseInt(amount) || 0;
            const bankId = 'MB';
            const accountNo = '9999888889999';
            const accountName = 'NGUYEN VAN CHU NHA';
            const desc = `Thanh toan Phong ${roomNum} coc hoac tien phong`;
            const qrUrl = `https://img.vietqr.io/image/${bankId}-${accountNo}-compact.png?amount=${amt}&addInfo=${encodeURIComponent(desc)}&accountName=${encodeURIComponent(accountName)}`;

            document.getElementById('qr-modal-room').textContent = `Phòng ${roomNum}`;
            document.getElementById('qr-modal-tenant').textContent = 'Khách mới / Cư dân';
            document.getElementById('qr-modal-amount').textContent = amt.toLocaleString('vi-VN') + "đ";
            document.getElementById('qr-modal-bank').textContent = `${bankId} - ${accountNo}`;
            document.getElementById('qr-modal-name').textContent = accountName;
            document.getElementById('qr-modal-desc').textContent = desc;
            document.getElementById('qr-modal-image').src = qrUrl;
            document.getElementById('qr-modal-download').href = qrUrl;
        }

        let activeMessageType = 'zalo';
        let currentRecipientPhone = '';
        let currentRecipientName = '';
        let currentMsgType = 'contract'; // 'contract' or 'debt'
        
        function openSendMsgModal(phone, name, messageText, msgType) {
            currentRecipientPhone = phone;
            currentRecipientName = name;
            currentMsgType = msgType;
            activeMessageType = 'zalo'; 
            
            document.getElementById('msg-phone-input').value = phone;
            document.getElementById('msg-text-input').value = messageText;
            document.getElementById('phone-screen-title').textContent = name || 'Khách thuê';
            
            updateMessagePreview();
            switchMsgTypeTab('zalo');
            
            document.getElementById('msg-success-overlay').classList.add('hidden');
            document.getElementById('msg-send-btn').disabled = false;
            document.getElementById('msg-send-btn').innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Gửi Tin Ngay';
            
            document.getElementById('send-msg-modal').classList.remove('hidden');
        }
        
        function closeSendMsgModal() {
            document.getElementById('send-msg-modal').classList.add('hidden');
        }
        
        function switchMsgTypeTab(type) {
            activeMessageType = type;
            const tabZalo = document.getElementById('msg-tab-zalo');
            const tabSms = document.getElementById('msg-tab-sms');
            const previewContainer = document.getElementById('msg-preview-container');
            const phoneHeader = document.getElementById('phone-header');
            
            if (type === 'zalo') {
                tabZalo.className = "flex-1 py-2 text-center text-xs font-bold text-indigo-400 bg-indigo-500/10 border-b-2 border-indigo-500 transition-all cursor-pointer";
                tabSms.className = "flex-1 py-2 text-center text-xs font-medium text-slate-500 hover:text-slate-300 transition-all cursor-pointer";
                previewContainer.className = "flex-1 p-4 overflow-y-auto space-y-3 bg-[#0f172a] rounded-2xl border border-slate-800/80 flex flex-col justify-end";
                phoneHeader.className = "flex items-center justify-between px-4 py-3 bg-[#1e293b] border-b border-slate-800 rounded-t-3xl text-slate-200";
                document.getElementById('phone-app-name').textContent = "Zalo Messenger";
                document.getElementById('preview-bubble').className = "max-w-[85%] rounded-2xl px-4 py-2 text-xs bg-indigo-600 text-white self-end ml-auto shadow-md";
            } else {
                tabSms.className = "flex-1 py-2 text-center text-xs font-bold text-emerald-400 bg-emerald-500/10 border-b-2 border-emerald-500 transition-all cursor-pointer";
                tabZalo.className = "flex-1 py-2 text-center text-xs font-medium text-slate-500 hover:text-slate-300 transition-all cursor-pointer";
                previewContainer.className = "flex-1 p-4 overflow-y-auto space-y-3 bg-[#0b0f19] rounded-2xl border border-slate-800/80 flex flex-col justify-end";
                phoneHeader.className = "flex items-center justify-between px-4 py-3 bg-[#111827] border-b border-slate-800 rounded-t-3xl text-slate-200";
                document.getElementById('phone-app-name').textContent = "Tin nhắn SMS";
                document.getElementById('preview-bubble').className = "max-w-[85%] rounded-2xl px-4 py-2 text-xs bg-emerald-600 text-white self-end ml-auto shadow-md";
            }
        }
        
        function updateMessagePreview() {
            const val = document.getElementById('msg-text-input').value;
            document.getElementById('preview-bubble-text').textContent = val || "(Trống)";
        }
        
        function selectMsgTemplate(templateIndex) {
            let msg = '';
            if (currentMsgType === 'contract') {
                if (templateIndex === 1) {
                    msg = `Kính gửi anh/chị ${currentRecipientName}, hợp đồng thuê phòng của anh/chị đã được ban quản lý khởi tạo. Vui lòng ký trực tuyến tại đây: [Link ký]`;
                } else if (templateIndex === 2) {
                    msg = `Chào ${currentRecipientName}, vui lòng kiểm tra và thực hiện ký hợp đồng online sớm nhất để hoàn tất thủ tục nhận phòng nhé.`;
                } else {
                    msg = `Thông báo: Link ký hợp đồng thuê phòng của anh/chị đã sẵn sàng. Truy cập ngay: [Link ký]`;
                }
            } else {
                if (templateIndex === 1) {
                    msg = `Kính gửi anh/chị ${currentRecipientName}, ban quản lý thông báo tiền phòng tháng này chưa thanh toán. Vui lòng nộp trước ngày 10. Trân trọng!`;
                } else if (templateIndex === 2) {
                    msg = `Nhắc nhở: Phòng của anh/chị còn dư nợ hóa đơn dịch vụ điện nước. Vui lòng thanh toán qua ứng dụng SmartRoom.`;
                } else {
                    msg = `Ban quản lý nhà trọ thông báo nhắc nợ tiền phòng tháng này đối với anh/chị ${currentRecipientName}. Liên hệ chủ nhà để biết thêm chi tiết.`;
                }
            }
            
            const originalVal = document.getElementById('msg-text-input').value;
            const linkMatch = originalVal.match(/https?:\/\/[^\s]+/);
            if (linkMatch && linkMatch[0]) {
                msg = msg.replace('[Link ký]', linkMatch[0]);
            }
            
            document.getElementById('msg-text-input').value = msg;
            updateMessagePreview();
        }
        
        function triggerSendMessage() {
            const phone = document.getElementById('msg-phone-input').value;
            const messageText = document.getElementById('msg-text-input').value;
            const btn = document.getElementById('msg-send-btn');
            
            if (!phone || !messageText) {
                alert("Vui lòng điền đầy đủ số điện thoại và nội dung tin nhắn!");
                return;
            }
            
            btn.disabled = true;
            btn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';
            
            fetch('/api/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    phone: phone,
                    message: messageText,
                    type: activeMessageType
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('msg-success-overlay').classList.remove('hidden');
                    
                    if (navigator.vibrate) {
                        navigator.vibrate([100, 50, 100]);
                    }
                    
                    setTimeout(() => {
                        closeSendMsgModal();
                        alert(`Đã gửi tin nhắn nhắc nhở đến ${data.phone} qua kênh ${data.type.toUpperCase()} thành công!`);
                    }, 2200);
                } else {
                    alert("Có lỗi xảy ra: " + (data.error || "Gửi tin thất bại"));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Gửi Tin Ngay';
                }
            })
            .catch(err => {
                alert("Không thể kết nối đến máy chủ!");
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Gửi Tin Ngay';
                console.error(err);
            });
        }

        function closeVietQRModal() {
            document.getElementById('vietqr-modal').classList.add('hidden');
        }

        function copyBankAccount() {
            navigator.clipboard.writeText('9999888889999').then(() => {
                const btn = document.getElementById('qr-modal-copy');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check text-emerald-450"></i> Đã sao chép!';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            });
        }

        function triggerAutoRemind(btn) {
            if (!confirm('Hệ thống sẽ tự động quét các hóa đơn chưa đóng trong tháng này và gửi tin nhắn nhắc nợ qua Zalo hàng loạt. Bạn có chắc chắn muốn thực hiện?')) {
                return;
            }

            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang tự động gửi...';

            fetch("{{ route('smartroom.admin.utility.auto_remind') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;

                if (data.success) {
                    if (data.sent_count > 0) {
                        let roomList = data.sent_rooms.map(r => `Phòng ${r.room_number} (${r.resident_name}): ${r.total_amount_formatted}`).join('\n');
                        alert(`🚀 Tự động nhắc nợ thành công!\nĐã gửi tin nhắn nhắc nợ Zalo tới ${data.sent_count} phòng chưa đóng tiền:\n\n${roomList}`);
                    } else {
                        alert(`✨ Tuyệt vời! Tất cả các phòng đã hoàn thành đóng tiền trọ tháng này.`);
                    }
                } else {
                    alert("Có lỗi xảy ra khi gửi nhắc nợ tự động.");
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                alert("Không thể kết nối đến máy chủ để thực hiện gửi nhắc nợ hàng loạt!");
                console.error(err);
            });
        }

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderAiList(title, items, iconClass) {
            if (!items || items.length === 0) {
                return '';
            }

            return `
                <div class="mt-3">
                    <div class="text-[11px] uppercase tracking-wider font-bold text-slate-500 flex items-center gap-2">
                        <i class="${iconClass}"></i> ${escapeHtml(title)}
                    </div>
                    <ul class="mt-2 space-y-1.5 text-xs text-slate-300">
                        ${items.map(item => `<li class="flex gap-2"><span class="text-indigo-400">•</span><span>${escapeHtml(item)}</span></li>`).join('')}
                    </ul>
                </div>
            `;
        }

        function loadAiDashboardInsight(btn) {
            const target = document.getElementById('ai-dashboard-insight');
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang phân tích...';
            target.innerHTML = '<span class="text-slate-500">AI đang đọc dữ liệu dashboard...</span>';

            fetch("{{ route('smartroom.admin.ai.dashboard_insight') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;

                if (!data.success) {
                    target.innerHTML = '<span class="text-rose-400">Không thể tạo nhận xét AI.</span>';
                    return;
                }

                const insight = data.insight;
                const badge = insight.used_ai
                    ? '<span class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">AI</span>'
                    : '<span class="text-[10px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20">Fallback</span>';

                target.innerHTML = `
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-sm text-slate-200 font-semibold">${escapeHtml(insight.summary || '')}</p>
                        ${badge}
                    </div>
                    ${renderAiList('Điểm đáng chú ý', insight.highlights, 'fa-solid fa-chart-line text-emerald-400')}
                    ${renderAiList('Rủi ro', insight.risks, 'fa-solid fa-triangle-exclamation text-amber-400')}
                    ${renderAiList('Gợi ý', insight.suggestions, 'fa-solid fa-lightbulb text-indigo-400')}
                `;
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                target.innerHTML = '<span class="text-rose-400">Không thể kết nối để tạo nhận xét AI.</span>';
                console.error(err);
            });
        }

        function askAiAssistant(btn) {
            const input = document.getElementById('ai-assistant-question');
            const target = document.getElementById('ai-assistant-answer');
            const question = input.value.trim();

            if (question.length < 3) {
                target.innerHTML = '<span class="text-amber-400">Vui lòng nhập câu hỏi rõ hơn.</span>';
                return;
            }

            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang hỏi...';
            target.innerHTML = '<span class="text-slate-500">Trợ lý đang đọc dữ liệu nhà trọ...</span>';

            fetch("{{ route('smartroom.admin.ai.assistant') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: JSON.stringify({ question })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;

                if (!data.success) {
                    target.innerHTML = '<span class="text-rose-400">Không thể hỏi trợ lý AI.</span>';
                    return;
                }

                const answer = data.answer;
                const badge = answer.used_ai
                    ? '<span class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">AI</span>'
                    : '<span class="text-[10px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20">Fallback</span>';

                target.innerHTML = `
                    <div class="flex items-start justify-between gap-3">
                        <div class="whitespace-pre-line">${escapeHtml(answer.answer || '')}</div>
                        ${badge}
                    </div>
                `;
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                target.innerHTML = '<span class="text-rose-400">Không thể kết nối trợ lý AI.</span>';
                console.error(err);
            });
        }

        function generateContractTermsWithAi(btn) {
            const roomId = document.getElementById('contract-room-id')?.value;
            const residentId = document.getElementById('contract-resident-id')?.value;
            const startDate = document.getElementById('contract-start-date')?.value;
            const endDate = document.getElementById('contract-end-date')?.value;
            const deposit = document.getElementById('contract-deposit')?.value;
            const terms = document.getElementById('contract-terms');

            if (!roomId || !residentId || !startDate || !endDate || !deposit) {
                alert('Vui lòng nhập đủ phòng, cư dân, thời hạn và tiền cọc trước khi dùng AI.');
                return;
            }

            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang soạn...';

            fetch("{{ route('smartroom.admin.ai.contract_terms') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: JSON.stringify({
                    room_id: roomId,
                    resident_id: residentId,
                    start_date: startDate,
                    end_date: endDate,
                    deposit: Number(deposit)
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = original;

                if (!data.success) {
                    alert('Không thể tạo điều khoản bằng AI.');
                    return;
                }

                terms.value = data.terms.terms || terms.value;
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = original;
                alert('Không thể kết nối AI để tạo điều khoản.');
                console.error(err);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const aiQuestionInput = document.getElementById('ai-assistant-question');
            if (aiQuestionInput) {
                aiQuestionInput.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        const button = aiQuestionInput.parentElement.querySelector('button');
                        if (button) {
                            askAiAssistant(button);
                        }
                    }
                });
            }
        });
    </script>

    <!-- VIETQR POPUP MODAL -->
    <div id="vietqr-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 shadow-2xl relative animate-fade-in">
            <button onclick="closeVietQRModal()" class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            
            <div class="text-center mb-4">
                <span class="text-xs px-2.5 py-1 rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 font-bold uppercase tracking-wider">Mã VietQR Hóa Đơn</span>
                <h3 class="text-lg font-bold text-slate-100 mt-2" id="qr-modal-room">Phòng 101</h3>
            </div>

            <!-- Loading Spinner -->
            <div id="qr-modal-loading" class="flex flex-col items-center justify-center py-12 gap-3">
                <div class="w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs text-slate-400 font-semibold">Đang tạo mã thanh toán...</span>
            </div>

            <!-- Content -->
            <div id="qr-modal-content" class="space-y-4 hidden">
                <!-- QR Image Display -->
                <div class="flex justify-center p-4 bg-white rounded-2xl relative overflow-hidden group">
                    <img id="qr-modal-image" class="w-60 h-60 object-contain transition-transform group-hover:scale-105 duration-300" src="" alt="Mã VietQR">
                </div>

                <!-- Payment Details -->
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 space-y-2.5 text-xs">
                    <div class="flex justify-between"><span class="text-slate-500">Khách thuê:</span> <strong class="text-slate-200" id="qr-modal-tenant">N/A</strong></div>
                    <div class="flex justify-between"><span class="text-slate-500">Số tiền cần đóng:</span> <strong class="text-emerald-400 font-bold text-sm" id="qr-modal-amount">0đ</strong></div>
                    <div class="flex justify-between"><span class="text-slate-500">Ngân hàng:</span> <strong class="text-slate-200" id="qr-modal-bank">MB - 9999888889999</strong></div>
                    <div class="flex justify-between"><span class="text-slate-500">Chủ tài khoản:</span> <strong class="text-slate-200" id="qr-modal-name">NGUYEN VAN CHU NHA</strong></div>
                    <div class="flex justify-between items-start"><span class="text-slate-500">Nội dung chuyển:</span> <strong class="text-slate-200 text-right shrink-0 max-w-[180px] break-words" id="qr-modal-desc">N/A</strong></div>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button id="qr-modal-copy" onclick="copyBankAccount()" class="w-full flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-all">
                        <i class="fa-solid fa-copy text-indigo-400"></i> Sao chép STK
                    </button>
                    <a id="qr-modal-download" download="VietQR_Payment.png" target="_blank" class="w-full flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all">
                        <i class="fa-solid fa-download"></i> Tải ảnh QR
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ZALO / SMS SEND MESSAGE PHONE MODAL -->
    <div id="send-msg-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-3xl bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 md:p-8 shadow-2xl relative animate-fade-in">
            <button onclick="closeSendMsgModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all z-30">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="flex flex-col md:flex-row gap-8 items-stretch justify-center">
                <!-- LEFT SIDE: PHONE SIMULATOR -->
                <div class="flex justify-center items-center shrink-0">
                    <div class="w-[280px] h-[480px] bg-slate-950 rounded-[36px] border-[5px] border-slate-800 relative shadow-2xl flex flex-col overflow-hidden">
                        <!-- iPhone Notch & Status Bar -->
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-28 h-4 bg-slate-950 rounded-b-xl z-20 flex justify-between items-center px-4 text-[7px] text-slate-500 font-bold">
                            <span>16:40</span>
                            <div class="w-8 h-1 bg-slate-800 rounded-full"></div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-wifi"></i>
                                <i class="fa-solid fa-battery-three-quarters"></i>
                            </div>
                        </div>

                        <!-- Chat Header -->
                        <div id="phone-header" class="flex items-center justify-between px-3 py-2 bg-[#1e293b] border-b border-slate-800 rounded-t-2xl text-slate-200 mt-3 shrink-0">
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 rounded-full bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-[10px] text-indigo-400 font-bold">
                                    <i class="fa-solid fa-user text-[9px]"></i>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold" id="phone-screen-title">Khách thuê</div>
                                    <div class="text-[7px] text-emerald-400 font-bold" id="phone-app-name">Zalo Messenger</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-ellipsis-vertical text-[10px] text-slate-500"></i>
                        </div>

                        <!-- Chat Area -->
                        <div id="msg-preview-container" class="flex-1 p-3 overflow-y-auto space-y-3 bg-[#0f172a] flex flex-col justify-end">
                            <div class="max-w-[85%] rounded-2xl px-3 py-1.5 text-[10px] bg-slate-900 text-slate-400 border border-slate-800 self-start">
                                Xin chào ban quản lý. Tôi cần nhận thông báo phòng.
                            </div>
                            <!-- Live Preview Bubble -->
                            <div id="preview-bubble" class="max-w-[85%] rounded-2xl px-3 py-1.5 text-[10px] bg-indigo-600 text-white self-end ml-auto shadow-md">
                                <p id="preview-bubble-text" class="break-words leading-relaxed">(Trống)</p>
                            </div>
                        </div>

                        <!-- Message Input Mock -->
                        <div class="p-2 bg-slate-900/60 border-t border-slate-800 shrink-0 flex items-center gap-2">
                            <div class="flex-1 bg-slate-950 border border-slate-800 rounded-full px-3 py-1 text-[8px] text-slate-500">
                                Tin nhắn...
                            </div>
                            <div class="w-5 h-5 rounded-full bg-indigo-600 flex items-center justify-center text-white text-[8px]">
                                <i class="fa-solid fa-microphone"></i>
                            </div>
                        </div>

                        <!-- Success Broadcast Overlay -->
                        <div id="msg-success-overlay" class="absolute inset-0 bg-slate-950/95 flex flex-col items-center justify-center text-center space-y-4 p-4 z-10 hidden">
                            <div class="relative flex items-center justify-center">
                                <div class="absolute w-16 h-16 bg-emerald-500/10 rounded-full animate-ping"></div>
                                <div class="absolute w-24 h-24 bg-emerald-500/5 rounded-full animate-pulse"></div>
                                <div class="w-12 h-12 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-lg">
                                    <i class="fa-solid fa-paper-plane animate-bounce"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-200">ĐANG TRUYỀN TIN</h4>
                                <p class="text-[9px] text-slate-500 mt-1">Kết nối cổng Zalo & SMS API thành công...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE: MESSAGE EDITING & CONTROL -->
                <div class="flex-1 flex flex-col justify-between space-y-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-envelope-open-text text-indigo-400 animate-pulse"></i> Trình Gửi Tin Nhắn Điện Tử
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Hệ thống tích hợp cổng API Zalo ZNS và SMS Brandname để tự động gửi thông báo đến cư dân.</p>
                    </div>

                    <!-- Channel Switch Tabs -->
                    <div class="flex border-b border-slate-800">
                        <div id="msg-tab-zalo" onclick="switchMsgTypeTab('zalo')" class="flex-1 py-2 text-center text-xs font-bold text-indigo-400 bg-indigo-500/10 border-b-2 border-indigo-500 transition-all cursor-pointer">
                            <i class="fa-solid fa-message mr-1.5"></i> Cổng Zalo ZNS
                        </div>
                        <div id="msg-tab-sms" onclick="switchMsgTypeTab('sms')" class="flex-1 py-2 text-center text-xs font-medium text-slate-500 hover:text-slate-300 transition-all cursor-pointer">
                            <i class="fa-solid fa-comment-sms mr-1.5"></i> Cổng SMS Brandname
                        </div>
                    </div>

                    <!-- Recipient details -->
                    <div class="grid grid-cols-1 gap-2">
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Số điện thoại nhận tin</label>
                        <input type="text" id="msg-phone-input" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-bold" placeholder="Nhập số điện thoại...">
                    </div>

                    <!-- Template Selectors -->
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Mẫu tin nhắn nhanh</label>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="selectMsgTemplate(1)" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[10px] text-slate-300 hover:text-slate-100 hover:border-slate-700 transition-all">
                                <i class="fa-solid fa-paste text-slate-500 mr-1"></i> Mẫu 1
                            </button>
                            <button onclick="selectMsgTemplate(2)" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[10px] text-slate-300 hover:text-slate-100 hover:border-slate-700 transition-all">
                                <i class="fa-solid fa-paste text-slate-500 mr-1"></i> Mẫu 2
                            </button>
                            <button onclick="selectMsgTemplate(3)" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[10px] text-slate-300 hover:text-slate-100 hover:border-slate-700 transition-all">
                                <i class="fa-solid fa-paste text-slate-500 mr-1"></i> Mẫu 3
                            </button>
                        </div>
                    </div>

                    <!-- Message text input -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Nội dung tin nhắn</label>
                        <textarea id="msg-text-input" oninput="updateMessagePreview()" rows="4" class="w-full bg-slate-900 border border-slate-800 rounded-xl p-3 text-xs text-slate-300 focus:outline-none focus:border-indigo-500 resize-none leading-relaxed" placeholder="Soạn nội dung tin nhắn..."></textarea>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex items-center gap-3 pt-2">
                        <button onclick="closeSendMsgModal()" class="flex-1 py-3 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 rounded-xl text-xs font-bold transition-all">
                            Hủy bỏ
                        </button>
                        <button id="msg-send-btn" onclick="triggerSendMessage()" class="flex-1 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/25 transition-all flex items-center justify-center">
                            <i class="fa-solid fa-paper-plane mr-2"></i> Gửi Tin Ngay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- KYC TERMS MODAL -->
    <div id="kyc-terms-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeKycTermsModal()"></div>
        <!-- Modal Content -->
        <div class="relative w-full max-w-3xl max-h-[85vh] mx-4 bg-[#0f1422] border border-slate-700/60 rounded-3xl shadow-2xl shadow-black/50 flex flex-col animate-fade-in">
            <!-- Header -->
            <div class="flex items-center justify-between px-8 py-5 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/15 border border-indigo-500/25 flex items-center justify-center">
                        <i class="fa-solid fa-shield-halved text-indigo-400"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-100">Điều Khoản Bảo Mật & KYC</h3>
                        <p class="text-[10px] text-slate-500 mt-0.5">Vui lòng đọc kỹ trước khi đồng ý</p>
                    </div>
                </div>
                <button onclick="closeKycTermsModal()" class="w-9 h-9 rounded-xl border border-slate-800 hover:border-slate-600 bg-slate-900/50 hover:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <!-- Body -->
            <div class="overflow-y-auto flex-1 px-8 py-6 space-y-6 text-[12px] leading-relaxed text-slate-300 custom-scrollbar">
                <div class="rounded-2xl border border-indigo-500/20 bg-indigo-500/5 p-4">
                    <h4 class="text-base font-extrabold text-indigo-300 mb-2">ĐIỀU KHOẢN BẢO MẬT THÔNG TIN VÀ XÁC THỰC TÀI KHOẢN (KYC)</h4>
                    <p class="text-slate-400 text-[11px] leading-6">Bằng việc tích chọn đồng ý, Quý khách (sau đây gọi là <strong class="text-slate-200">"Chủ trọ"</strong>) chấp thuận các điều khoản nghiêm ngặt dưới đây từ Ban quản trị hệ thống SmartRoom (sau đây gọi là <strong class="text-slate-200">"SmartRoom"</strong>) liên quan đến việc thu thập, xử lý và bảo vệ dữ liệu cá nhân.</p>
                </div>

                <!-- Section I -->
                <div>
                    <h5 class="text-sm font-extrabold text-emerald-300 mb-3 flex items-center gap-2"><i class="fa-solid fa-scale-balanced text-emerald-400/70"></i> I. Căn Cứ Pháp Lý (Luật Việt Nam Bản Địa)</h5>
                    <p class="mb-3 text-slate-400">SmartRoom cam kết hoạt động tuân thủ nghiêm ngặt hệ thống pháp luật Việt Nam về an toàn thông tin và bảo vệ dữ liệu cá nhân, bao gồm nhưng không giới hạn ở:</p>
                    <ul class="space-y-2 text-slate-300">
                        <li class="flex items-start gap-2"><i class="fa-solid fa-check-circle text-emerald-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Nghị định 13/2023/NĐ-CP</strong> về Bảo vệ dữ liệu cá nhân (Đặc biệt là quy định về Dữ liệu cá nhân cơ bản và Dữ liệu cá nhân nhạy cảm).</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-check-circle text-emerald-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Luật An toàn thông tin mạng 2015</strong> về trách nhiệm bảo vệ thông tin cá nhân trên mạng của tổ chức, cá nhân.</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-check-circle text-emerald-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Bộ luật Dân sự 2015 (Điều 38)</strong> về Quyền bí mật đời tư.</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-check-circle text-emerald-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Luật An ninh mạng 2018</strong> về phòng ngừa, xử lý hành vi xâm phạm an ninh mạng.</span></li>
                    </ul>
                </div>

                <!-- Section II -->
                <div>
                    <h5 class="text-sm font-extrabold text-sky-300 mb-3 flex items-center gap-2"><i class="fa-solid fa-bullseye text-sky-400/70"></i> II. Mục Đích Sử Dụng Dữ Liệu Duy Nhất</h5>
                    <ul class="space-y-2 text-slate-300">
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-dot text-sky-400/60 mt-0.5 shrink-0 text-[9px]"></i><span><strong class="text-slate-200">Phạm vi thu thập:</strong> Hình ảnh Căn cước công dân (CCCD) hai mặt, số tài khoản ngân hàng, tên chủ tài khoản và ảnh xác minh tài khoản.</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle-dot text-sky-400/60 mt-0.5 shrink-0 text-[9px]"></i><span><strong class="text-slate-200">Mục đích:</strong> Chỉ sử dụng duy nhất cho việc xác thực danh tính chủ sở hữu (KYC) để kích hoạt tính năng nhận tiền tự động/rút tiền trên hệ thống và phòng chống các hành vi gian lận tài chính.</span></li>
                        <li class="flex items-start gap-2 rounded-xl border border-rose-500/20 bg-rose-500/5 p-3"><i class="fa-solid fa-ban text-rose-400 mt-0.5 shrink-0"></i><span><strong class="text-rose-300">Tuyệt đối nghiêm cấm:</strong> SmartRoom không được phép sử dụng dữ liệu này cho mục đích quảng cáo, chia sẻ, bán hoặc chuyển giao cho bất kỳ bên thứ ba nào khác khi chưa có sự đồng ý bằng văn bản của Chủ trọ.</span></li>
                    </ul>
                </div>

                <!-- Section III -->
                <div>
                    <h5 class="text-sm font-extrabold text-amber-300 mb-3 flex items-center gap-2"><i class="fa-solid fa-lock text-amber-400/70"></i> III. Quy Trình Giới Hạn Quyền Truy Cập Và Xóa Dữ Liệu</h5>
                    <ul class="space-y-2 text-slate-300">
                        <li class="flex items-start gap-2"><i class="fa-solid fa-hourglass-half text-amber-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Giới hạn quyền xem tạm thời:</strong> Quyền xem hình ảnh CCCD và tài liệu ngân hàng gốc chỉ được cấp cho nhân sự kiểm duyệt được chỉ định của SmartRoom trong thời gian hồ sơ đang chờ duyệt (tối đa không quá <strong class="text-amber-200">24 giờ làm việc</strong> kể từ khi gửi).</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-key text-amber-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Mã hóa và Thu hồi quyền:</strong> Ngay sau khi hồ sơ được "Duyệt" hoặc "Từ chối", hệ thống tự động thu hồi hoàn toàn quyền tiếp cận của nhân sự kiểm duyệt.</span></li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-trash-can text-amber-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Cam kết xóa dữ liệu (Hard Delete):</strong> Toàn bộ tệp ảnh gốc (CCCD, Xác nhận tài khoản) sẽ được xóa vĩnh viễn khỏi bộ nhớ đệm của nhân sự trong vòng <strong class="text-amber-200">24 giờ</strong>. Hệ thống chỉ lưu trữ chuỗi hash đã được mã hóa theo tiêu chuẩn quân đội <strong class="text-amber-200">AES-256</strong> trên máy chủ để phục vụ đối chiếu lịch sử khi có yêu cầu từ cơ quan chức năng.</span></li>
                    </ul>
                </div>

                <!-- Section IV -->
                <div>
                    <h5 class="text-sm font-extrabold text-rose-300 mb-3 flex items-center gap-2"><i class="fa-solid fa-gavel text-rose-400/70"></i> IV. Cam Kết Trách Nhiệm Chi Tiết Và Chế Tài Xử Phạt Khi Lộ Thông Tin</h5>
                    <p class="mb-3 text-slate-400"><strong class="text-slate-200">Tuyên bố chịu trách nhiệm:</strong> SmartRoom khẳng định việc bảo vệ dữ liệu của Chủ trọ là nghĩa vụ pháp lý tối cao. Nếu xảy ra bất kỳ sự cố rò rỉ, phát tán hoặc làm lộ thông tin cá nhân, thông tin tài khoản của Chủ trọ xuất phát từ lỗi hệ thống, lỗ hổng bảo mật hoặc do hành vi cố ý/vô ý của nhân viên SmartRoom, chúng tôi cam kết chịu trách nhiệm như sau:</p>
                    <ul class="space-y-3 text-slate-300">
                        <li class="flex items-start gap-2"><i class="fa-solid fa-shield-halved text-rose-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Trách nhiệm hình sự và hành chính:</strong> SmartRoom hoàn toàn chịu trách nhiệm trước pháp luật Việt Nam, phối hợp với Cục An ninh mạng và phòng, chống tội phạm sử dụng công nghệ cao <strong class="text-rose-200">(A05)</strong> để điều tra và xử lý cá nhân vi phạm theo quy định của pháp luật.</span></li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-coins text-rose-400/60 mt-0.5 shrink-0"></i>
                            <div>
                                <p><strong class="text-slate-200">Trách nhiệm dân sự và bồi thường thiệt hại:</strong></p>
                                <ul class="mt-2 space-y-2 pl-4">
                                    <li class="flex items-start gap-2"><span class="text-rose-400 mt-0.5 shrink-0">•</span><span>SmartRoom cam kết bồi thường toàn bộ thiệt hại về vật chất và tinh thần thực tế phát sinh cho Chủ trọ nếu thông tin bị lộ dẫn đến việc Chủ trọ bị lợi dụng danh tính (Ví dụ: bị kẻ xấu dùng CCCD vay tín dụng đen, lừa đảo chiếm đoạt tài sản...).</span></li>
                                    <li class="flex items-start gap-2"><span class="text-rose-400 mt-0.5 shrink-0">•</span><span>Mức bồi thường tối thiểu cho một sự cố lộ thông tin được ấn định sẵn là <strong class="text-rose-200 bg-rose-500/10 px-2 py-0.5 rounded-lg">50.000.000 VNĐ</strong> cho một tài khoản bị ảnh hưởng, chưa bao gồm các chi phí khắc phục thiệt hại phát sinh thực tế được chứng minh bằng hóa đơn, chứng từ hoặc phán quyết của Tòa án.</span></li>
                                </ul>
                            </div>
                        </li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-bell text-rose-400/60 mt-0.5 shrink-0"></i><span><strong class="text-slate-200">Khắc phục sự cố:</strong> Trong vòng <strong class="text-rose-200">02 giờ</strong> kể từ khi phát hiện sự cố, SmartRoom có trách nhiệm thông báo bằng văn bản/email cho Chủ trọ, đồng thời áp dụng mọi biện pháp kỹ thuật để ngăn chặn và giảm thiểu tối đa thiệt hại.</span></li>
                    </ul>
                </div>

                <!-- Section V -->
                <div>
                    <h5 class="text-sm font-extrabold text-violet-300 mb-3 flex items-center gap-2"><i class="fa-solid fa-handshake text-violet-400/70"></i> V. Phương Thức Giải Quyết Tranh Chấp</h5>
                    <div class="rounded-xl border border-violet-500/20 bg-violet-500/5 p-4 text-slate-300">
                        <p>Mọi tranh chấp phát sinh từ hoặc liên quan đến việc bảo mật thông tin này sẽ được ưu tiên giải quyết bằng <strong class="text-violet-200">thương lượng</strong>. Trong trường hợp không đạt được thỏa thuận, vụ việc sẽ được đưa ra giải quyết tại <strong class="text-violet-200">Tòa án nhân dân có thẩm quyền tại Việt Nam</strong> theo quy định của pháp luật.</p>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <div class="px-8 py-4 border-t border-slate-800 flex items-center justify-between">
                <p class="text-[10px] text-slate-500"><i class="fa-solid fa-info-circle mr-1"></i> Cập nhật lần cuối: Tháng 06/2026</p>
                <button onclick="closeKycTermsModal()" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-lg shadow-indigo-600/25">
                    <i class="fa-solid fa-check mr-1.5"></i> Đã đọc và hiểu
                </button>
            </div>
        </div>
    </div>

    <!-- RENEW CONTRACT MODAL -->
    <div id="renew-contract-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-3xl bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 md:p-8 shadow-2xl relative animate-fade-in overflow-y-auto max-h-[90vh]">
            <button onclick="closeRenewContractModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all z-30">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-lg">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">
                        Xử Lý Gia Hạn & Tái Ký Hợp Đồng
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Duyệt gia hạn trực tiếp thời hạn thuê hoặc khởi tạo hợp đồng tái ký mới.</p>
                </div>
            </div>

            <!-- Resident Renewal Request Info -->
            <div id="renew-request-info-box" class="hidden rounded-2xl border border-indigo-500/20 bg-indigo-500/5 p-4 mb-6">
                <h4 class="text-xs font-bold text-indigo-400 mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info"></i> Yêu cầu từ Cư dân
                </h4>
                <p class="text-xs text-slate-300 leading-relaxed whitespace-pre-line" id="renew-request-info-text"></p>
            </div>

            <form id="renew-contract-form" method="POST" action="" class="space-y-6" onsubmit="return disableSubmit(this)">
                @csrf
                <!-- Hidden fields containing old contract details -->
                <input type="hidden" name="room_id" id="renew-room-id">
                <input type="hidden" name="resident_id" id="renew-resident-id">
                <input type="hidden" name="lessor_name" id="renew-lessor-name">
                <input type="hidden" name="lessor_phone" id="renew-lessor-phone">
                <input type="hidden" name="lessor_id_number" id="renew-lessor-id-number">
                <input type="hidden" name="lessor_address" id="renew-lessor-address">
                <input type="hidden" name="lessee_name" id="renew-lessee-name">
                <input type="hidden" name="lessee_phone" id="renew-lessee-phone">
                <input type="hidden" name="lessee_id_number" id="renew-lessee-id-number">
                <input type="hidden" name="lessee_permanent_address" id="renew-lessee-permanent-address">
                <input type="hidden" name="lessee_current_address" id="renew-lessee-current-address">
                <input type="hidden" name="rental_address" id="renew-rental-address">
                <input type="hidden" name="rental_area_description" id="renew-rental-area-description">
                <input type="hidden" name="equipment_list" id="renew-equipment-list">
                <input type="hidden" name="rental_purpose" id="renew-rental-purpose">
                <input type="hidden" name="occupant_count" id="renew-occupant-count">
                <input type="hidden" name="first_payment_date" id="renew-first-payment-date">
                <input type="hidden" name="payment_method" id="renew-payment-method">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Phòng trọ</label>
                        <input type="text" id="renew-room-display" readonly class="w-full bg-slate-900/50 border border-slate-800/80 rounded-xl px-4 py-2.5 text-xs text-slate-400 font-bold focus:outline-none">
                    </div>
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Cư dân đại diện</label>
                        <input type="text" id="renew-resident-display" readonly class="w-full bg-slate-900/50 border border-slate-800/80 rounded-xl px-4 py-2.5 text-xs text-slate-400 font-bold focus:outline-none">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Hình thức xử lý</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex flex-col p-4 rounded-2xl bg-slate-950 border border-slate-850 hover:border-slate-700 cursor-pointer select-none group transition-all">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-200">Gia hạn trực tiếp</span>
                                <input type="radio" name="renewal_mode" value="extend" checked onchange="toggleRenewalModeFields('extend')" class="accent-indigo-600">
                            </div>
                            <span class="text-[10px] text-slate-500 mt-2 leading-relaxed">Cập nhật trực tiếp ngày hết hạn của hợp đồng hiện tại. Không cần cư dân ký lại chữ ký số.</span>
                        </label>
                        <label class="relative flex flex-col p-4 rounded-2xl bg-slate-950 border border-slate-850 hover:border-slate-700 cursor-pointer select-none group transition-all">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-200">Tái ký hợp đồng mới</span>
                                <input type="radio" name="renewal_mode" value="new_contract" onchange="toggleRenewalModeFields('new_contract')" class="accent-indigo-600">
                            </div>
                            <span class="text-[10px] text-slate-500 mt-2 leading-relaxed">Kết thúc hợp đồng cũ và tạo một bản hợp đồng điện tử hoàn toàn mới. Cư dân cần ký kết online để kích hoạt.</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div id="renew-start-date-group" class="hidden">
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Ngày bắt đầu mới</label>
                        <input type="date" name="start_date" id="renew-start-date" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-bold">
                    </div>
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2" id="renew-end-date-label">Ngày kết thúc gia hạn mới</label>
                        <input type="date" name="end_date" id="renew-end-date" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Giá thuê mới (đ/tháng)</label>
                        <input type="number" name="rent_price" id="renew-rent-price" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-bold">
                    </div>
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Tiền cọc mới (đ)</label>
                        <input type="number" name="deposit" id="renew-deposit" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-bold">
                    </div>
                    <div>
                        <label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2">Chu kỳ đóng tiền (tháng)</label>
                        <select name="payment_cycle_months" id="renew-payment-cycle-months" required class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 font-bold">
                            <option value="1">1 tháng</option>
                            <option value="2">2 tháng</option>
                            <option value="3">3 tháng</option>
                            <option value="6">6 tháng</option>
                            <option value="12">12 tháng</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 pt-4 border-t border-slate-850">
                    <button type="button" id="renew-decline-btn" class="w-full sm:w-auto px-5 py-3 bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white rounded-xl text-xs font-bold border border-rose-500/20 transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-ban"></i> Từ Chối Yêu Cầu
                    </button>
                    <div class="flex-1"></div>
                    <button type="button" onclick="closeRenewContractModal()" class="w-full sm:w-auto px-5 py-3 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 rounded-xl text-xs font-bold transition-all">
                        Hủy bỏ
                    </button>
                    <button type="submit" class="submit-btn w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-extrabold shadow-lg shadow-emerald-600/25 transition-all flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-check"></i> Xác Nhận Duyệt
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openKycTermsModal() {
            const modal = document.getElementById('kyc-terms-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeKycTermsModal() {
            const modal = document.getElementById('kyc-terms-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
        
        function openRenewContractModal(contract, resident, room) {
            const modal = document.getElementById('renew-contract-modal');
            if (!modal) return;

            const form = document.getElementById('renew-contract-form');
            form.action = `/smartroom/admin/contract/${contract.id}/renew`;

            document.getElementById('renew-room-display').value = 'Phòng ' + (room ? room.room_number : 'N/A');
            document.getElementById('renew-resident-display').value = resident ? resident.name : 'N/A';

            document.getElementById('renew-room-id').value = contract.room_id || '';
            document.getElementById('renew-resident-id').value = contract.resident_id || '';
            
            if (resident) {
                document.getElementById('renew-lessee-name').value = resident.name || '';
                document.getElementById('renew-lessee-phone').value = resident.phone || '';
                document.getElementById('renew-lessee-id-number').value = resident.cccd || '';
                document.getElementById('renew-lessee-permanent-address').value = resident.hometown || '';
                document.getElementById('renew-lessee-current-address').value = resident.hometown || '';
            }

            document.getElementById('renew-lessor-name').value = '{{ $tenant->name ?? "" }}';
            document.getElementById('renew-lessor-phone').value = '{{ $tenant->phone ?? "" }}';
            document.getElementById('renew-lessor-id-number').value = '';
            document.getElementById('renew-lessor-address').value = '{{ $tenant->address ?? "" }}';
            document.getElementById('renew-rental-address').value = (room && room.building) ? room.building.address : 'SmartRoom Apartment';
            document.getElementById('renew-rental-area-description').value = room ? `Phòng trọ số ${room.room_number}` : '';
            document.getElementById('renew-equipment-list').value = room ? (room.equipments || '') : '';
            document.getElementById('renew-rental-purpose').value = 'Để ở';
            document.getElementById('renew-occupant-count').value = '1';
            document.getElementById('renew-first-payment-date').value = '';
            document.getElementById('renew-payment-method').value = 'Chuyển khoản / Tiền mặt';

            const infoBox = document.getElementById('renew-request-info-box');
            const infoText = document.getElementById('renew-request-info-text');
            if (contract.renewal_status === 'requested') {
                infoBox.classList.remove('hidden');
                infoText.textContent = `Cư dân đã gửi yêu cầu gia hạn thêm ${contract.renewal_months} tháng.\nGhi chú của cư dân: "${contract.renewal_note || 'Không có ghi chú'}"`;
            } else {
                infoBox.classList.add('hidden');
            }

            document.getElementById('renew-rent-price').value = room ? room.price : (contract.rent_price || 0);
            document.getElementById('renew-deposit').value = contract.deposit || (room ? room.price : 0);
            document.getElementById('renew-payment-cycle-months').value = contract.payment_cycle_months || '3';

            if (contract.end_date) {
                const oldEndDate = new Date(contract.end_date);
                const nextDay = new Date(oldEndDate);
                nextDay.setDate(oldEndDate.getDate() + 1);
                
                const yyyy = nextDay.getFullYear();
                const mm = String(nextDay.getMonth() + 1).padStart(2, '0');
                const dd = String(nextDay.getDate()).padStart(2, '0');
                document.getElementById('renew-start-date').value = `${yyyy}-${mm}-${dd}`;

                const durationMonths = contract.renewal_status === 'requested' ? parseInt(contract.renewal_months) : 6;
                const newEndDate = new Date(oldEndDate);
                newEndDate.setMonth(oldEndDate.getMonth() + durationMonths);
                
                const ey = newEndDate.getFullYear();
                const em = String(newEndDate.getMonth() + 1).padStart(2, '0');
                const ed = String(newEndDate.getDate()).padStart(2, '0');
                document.getElementById('renew-end-date').value = `${ey}-${em}-${ed}`;
            }

            const declineBtn = document.getElementById('renew-decline-btn');
            if (contract.renewal_status === 'requested') {
                declineBtn.classList.remove('hidden');
                declineBtn.onclick = function() {
                    if (confirm('Bạn có chắc chắn muốn từ chối yêu cầu gia hạn này không?')) {
                        const declineForm = document.createElement('form');
                        declineForm.method = 'POST';
                        declineForm.action = `/smartroom/admin/contract/${contract.id}/decline-renewal`;
                        
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = '{{ csrf_token() }}';
                        declineForm.appendChild(csrfInput);
                        
                        document.body.appendChild(declineForm);
                        declineForm.submit();
                    }
                };
            } else {
                declineBtn.classList.add('hidden');
            }

            // Reset radio
            document.querySelector('input[name="renewal_mode"][value="extend"]').checked = true;
            toggleRenewalModeFields('extend');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeRenewContractModal() {
            const modal = document.getElementById('renew-contract-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        function toggleRenewalModeFields(mode) {
            const startDateGroup = document.getElementById('renew-start-date-group');
            const endDateLabel = document.getElementById('renew-end-date-label');
            
            if (mode === 'extend') {
                startDateGroup.classList.add('hidden');
                endDateLabel.textContent = 'Ngày kết thúc gia hạn mới';
            } else {
                startDateGroup.classList.remove('hidden');
                endDateLabel.textContent = 'Ngày kết thúc hợp đồng mới';
            }
        }

        // ==========================================
        // REALTIME ROOM MATRIX & HOUSEKEEPING
        // ==========================================
        let lastEventTimestamp = Math.floor(Date.now() / 1000);

        function applyRoomCardUpdate(roomData) {
            const card = document.getElementById('room-card-' + roomData.id);
            if (!card) return;

            // 1. Cập nhật thuộc tính trong dataset
            card.setAttribute('data-room-status', roomData.status);
            if (roomData.room_number) {
                card.setAttribute('data-room-number', roomData.room_number);
            }

            // 2. Cập nhật thông tin cư dân trên thẻ & dataset
            const resH4 = card.querySelector('.room-resident');
            if (roomData.has_resident && roomData.resident_name && (roomData.status === 'occupied' || roomData.status === 'overdue')) {
                card.setAttribute('data-resident-name', roomData.resident_name);
                if (roomData.resident_phone) card.setAttribute('data-resident-phone', roomData.resident_phone);
                if (resH4) {
                    resH4.textContent = 'Cư dân: ' + roomData.resident_name;
                    resH4.className = 'room-resident text-xs font-bold text-slate-400 truncate mb-1';
                }
            } else if (['empty', 'cleaning', 'maintenance'].includes(roomData.status)) {
                card.setAttribute('data-resident-name', '');
                card.setAttribute('data-resident-phone', '');
                if (resH4) {
                    resH4.textContent = 'Chưa có cư dân';
                    resH4.className = 'room-resident text-xs font-bold text-slate-500 italic mb-1';
                }
            }

            // 3. Cập nhật class viền/nền CSS
            card.className = card.className.replace(/room-(empty|occupied|overdue|cleaning|maintenance)/g, '');
            card.className = card.className.replace(/border-(emerald|red|amber|orange|slate)-500\/20/g, '');
            
            const newStatusClass = roomData.status_class || ('room-' + roomData.status);
            card.classList.add(...newStatusClass.split(' ').filter(c => c));

            // 4. Cập nhật Badge trên card
            const badge = card.querySelector('.room-badge');
            if (badge) {
                badge.textContent = roomData.status_label;
                badge.className = "room-badge px-2 py-0.5 rounded text-[10px] font-extrabold border " + (roomData.badge_class || 'border-slate-700 text-slate-400');
            }

            // 5. Ẩn/hiện card ngay theo tab bộ lọc hiện tại (nếu đang chọn tab)
            applyCurrentFilterToCard(card);

            // 6. Cập nhật số đếm tất cả các nút filter
            updateRoomFilterCounts();

            // 7. Đồng bộ trực tiếp nếu Modal của phòng này đang mở
            if (currentActiveRoomId == roomData.id) {
                currentActiveRoomStatus = roomData.status;
                const modalBadge = document.getElementById('modal-room-status-badge');
                if (modalBadge) {
                    modalBadge.textContent = roomData.status_label;
                    modalBadge.className = "text-xs px-2.5 py-1 rounded-md font-bold uppercase border " + (roomData.badge_class || 'bg-slate-500/10 text-slate-400 border-slate-500/20');
                }
                updateQuickStatusButtons(roomData.status);

                const resDetails = document.getElementById('modal-resident-details');
                const billDetails = document.getElementById('modal-billing-details');
                const actionBtn = document.getElementById('modal-btn-action');
                const payBtn = document.getElementById('modal-btn-pay');
                const printBtn = document.getElementById('modal-btn-print');
                const qrBtn = document.getElementById('modal-btn-qr');

                if (['empty', 'cleaning', 'maintenance'].includes(roomData.status)) {
                    if (resDetails) resDetails.classList.add('hidden');
                    if (billDetails) billDetails.classList.add('hidden');
                    if (actionBtn) actionBtn.classList.add('hidden');
                    if (payBtn) payBtn.classList.add('hidden');
                    if (printBtn) printBtn.classList.add('hidden');
                    if (qrBtn) qrBtn.classList.add('hidden');
                } else if (roomData.has_resident) {
                    if (resDetails) resDetails.classList.remove('hidden');
                    const resNameEl = document.getElementById('modal-resident-name');
                    if (resNameEl && roomData.resident_name) {
                        resNameEl.textContent = roomData.resident_name;
                    }
                    const resPhoneEl = document.getElementById('modal-resident-phone');
                    if (resPhoneEl && roomData.resident_phone) {
                        resPhoneEl.textContent = roomData.resident_phone;
                    }
                }
            }

            // 8. Hiệu ứng Pulse Flash Realtime
            card.classList.add('ring-4', 'ring-emerald-400', 'scale-[1.03]');
            setTimeout(() => {
                card.classList.remove('ring-4', 'ring-emerald-400', 'scale-[1.03]');
            }, 1800);
        }

        function showRealtimeToast(title, subtitle = '', type = 'room') {
            let container = document.getElementById('realtime-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'realtime-toast-container';
                container.className = 'fixed top-20 right-8 z-50 flex flex-col gap-3 pointer-events-none';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            const isTicket = type === 'ticket';
            const iconBg = isTicket ? 'bg-rose-500/20 text-rose-400' : 'bg-emerald-500/20 text-emerald-400';
            const icon = isTicket ? 'fa-triangle-exclamation' : 'fa-bolt';
            const borderColor = isTicket ? 'border-rose-500/40' : 'border-emerald-500/40';
            const tagLabel = isTicket ? 'Sự cố cư dân (Realtime Echo)' : 'Sơ đồ phòng (Realtime Reverb)';

            toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl bg-slate-900/95 border ${borderColor} text-slate-100 shadow-2xl shadow-indigo-500/20 backdrop-blur-md text-xs font-semibold animate-slide-in transition-all duration-300`;
            toast.innerHTML = `
                <div class="w-9 h-9 rounded-xl ${iconBg} flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid ${icon} animate-bounce"></i>
                </div>
                <div class="min-w-[180px]">
                    <div class="text-[10px] ${isTicket ? 'text-rose-400' : 'text-emerald-400'} font-bold uppercase tracking-wider">${tagLabel}</div>
                    <div class="text-slate-100 font-bold mt-0.5">${title}</div>
                    ${subtitle ? `<div class="text-[11px] text-slate-400 mt-0.5 leading-snug">${subtitle}</div>` : ''}
                </div>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-8');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        @php
            $activeTenantId = $tenant->id ?? (Auth::user()?->tenant_id ?? ($rooms->first()?->tenant_id ?? 1));
        @endphp
        const tenantId = {{ $activeTenantId }};
        let lastProcessedEventKey = '';

        function handleIncomingRoomUpdate(data) {
            if (!data || !data.id) return;
            const eventKey = `${data.id}_${data.status}_${data.updated_at || ''}`;
            if (eventKey === lastProcessedEventKey) {
                return; // Tránh xử lý trùng lặp từ 2 channel
            }
            lastProcessedEventKey = eventKey;
            lastEventTimestamp = Math.floor(Date.now() / 1000);
            applyRoomCardUpdate(data);
            showRealtimeToast(
                `P.${data.room_number}: ${data.status_label}`,
                'Đồng bộ trạng thái phòng tức thời qua WebSocket Reverb!',
                'room'
            );
        }

        function initRoomMatrixRealtime() {
            // 1. Kết nối chính thức qua Laravel Echo + Reverb (WebSocket)
            if (window.Echo) {
                try {
                    // Lắng nghe trên kênh tenant cụ thể
                    window.Echo.channel(`tenant.${tenantId}.room-matrix`)
                        .listen('.room.status.updated', (data) => handleIncomingRoomUpdate(data));

                    // Lắng nghe trên kênh toàn cục để các tab Admin luôn nhận được tức thì
                    window.Echo.channel('room-matrix')
                        .listen('.room.status.updated', (data) => handleIncomingRoomUpdate(data));

                    // Lắng nghe báo hỏng sự cố tức thời từ cư dân
                    window.Echo.channel(`tenant.${tenantId}.dashboard`)
                        .listen('.ticket.created', (data) => {
                            if (data && data.id) {
                                showRealtimeToast(
                                    `🚨 Sự cố mới: P.${data.room_number}`,
                                    `[${data.category}] ${data.title}`,
                                    'ticket'
                                );
                                const bellBadge = document.querySelector('.fa-bell + span');
                                if (bellBadge) bellBadge.classList.add('animate-ping');
                            }
                        });
                } catch (err) {
                    console.warn('Echo Reverb subscription error: ', err);
                }
            }

            // 2. Thăm dò phụ (Polling fallback nhẹ nhàng) mỗi 25s khi tab đang hiển thị
            setInterval(async () => {
                if (document.hidden) return; // Không poll khi tab ẩn để tiết kiệm tài nguyên
                try {
                    const res = await fetch("{{ route('admin.rooms.matrix.poll') }}?since=" + lastEventTimestamp);
                    if (res.ok) {
                        const json = await res.json();
                        if (json.has_update && json.event) {
                            lastEventTimestamp = json.event.updated_at;
                            applyRoomCardUpdate(json.event);
                        }
                    }
                } catch (e) {
                    // im lặng bỏ qua lỗi mạng tạm thời
                }
            }, 25000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initRoomMatrixRealtime, 250);
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRoomDetail();
            }
        });
    </script>

    <script src="{{ asset('js/admin-sidebar.js') }}"></script>
</body>
</html>
