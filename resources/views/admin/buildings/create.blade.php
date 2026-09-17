<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Thêm Cơ sở Lưu trú mới - SmartRoom.">
    <title>Thêm Cơ Sở Lưu Trú Mới - SmartRoom</title>
    
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
    
    <style>
        .glass-card {
            background: rgba(13, 18, 31, 0.55);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(30, 41, 59, 0.7);
        }
    </style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white overflow-hidden">

    <!-- Decorative glows -->
    <div class="absolute top-[-10%] right-[-10%] w-[400px] h-[400px] rounded-full bg-indigo-600/10 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[400px] h-[400px] rounded-full bg-emerald-600/10 blur-[120px] pointer-events-none"></div>

    <!-- SIDEBAR -->
    @include('admin.partials.sidebar')

    <!-- MAIN APP WRAPPER -->
    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        
        <!-- TOP NAVBAR -->
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.buildings.index') }}" class="p-2 rounded-xl border border-slate-800 bg-slate-900/50 text-slate-400 hover:text-slate-200 hover:bg-slate-800 transition" title="Quay lại">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h2 class="text-base font-bold text-slate-100">Thêm Cơ Sở Lưu Trú Mới</h2>
                    <p class="text-xs text-slate-400">Khởi tạo tòa nhà / khu trọ mới vào hệ thống quản lý</p>
                </div>
            </div>
            
            <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button p-2.5 rounded-xl border border-slate-800 bg-slate-900/50 text-slate-400 hover:text-slate-200 hover:bg-slate-800 transition" aria-label="Chuyển chế độ sáng tối">
                <i class="fa-solid fa-moon" data-theme-icon></i>
            </button>
        </header>

        <!-- CONTENT PANEL -->
        <main class="p-8 flex-grow overflow-y-auto max-w-5xl mx-auto w-full">

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm shadow-lg shadow-rose-500/5">
                    <div class="font-bold flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>Vui lòng kiểm tra lại các trường thông tin:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-xs text-rose-400">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.buildings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Basic Information Card -->
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="text-sm font-bold text-indigo-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-info-circle"></i>
                        <span>Thông tin cơ bản</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">
                                Tên Cơ Sở / Tòa Nhà <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Tòa Nhà SmartRoom Tây Sơn" required class="w-full px-4 py-2.5 bg-slate-900/60 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">
                                Địa Chỉ Chi Tiết <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" name="address" value="{{ old('address') }}" placeholder="Ví dụ: Số 12 Ngõ 165 Cầu Giấy, Phường Dịch Vọng, Hà Nội" required class="w-full px-4 py-2.5 bg-slate-900/60 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">
                                Số Hotline / Quản Lý Cơ Sở
                            </label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Ví dụ: 0987 654 321" class="w-full px-4 py-2.5 bg-slate-900/60 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">
                                Tổng Số Tầng <span class="text-rose-400">*</span>
                            </label>
                            <input type="number" name="total_floors" value="{{ old('total_floors', 1) }}" min="1" max="100" required class="w-full px-4 py-2.5 bg-slate-900/60 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">
                                Trạng Thái Hoạt Động <span class="text-rose-400">*</span>
                            </label>
                            <select name="status" class="w-full px-4 py-2.5 bg-slate-900/60 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Đang bảo trì / nâng cấp</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Tạm ngưng đón khách</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Amenities & Facilities Card -->
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="text-sm font-bold text-emerald-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Tiện ích & Dịch vụ chung của cơ sở</span>
                    </h3>

                    @php
                        $presetAmenities = [
                            'Thang máy',
                            'Hầm để xe rộng rãi',
                            'Bảo vệ 24/7',
                            'Khóa cửa vân tay / FaceID',
                            'Camera an ninh các tầng',
                            'Hệ thống PCCC tự động',
                            'Máy giặt chung',
                            'Sân phơi thoáng mát',
                            'Dọn vệ sinh hàng tuần',
                            'Internet cáp quang tốc độ cao',
                            'Giờ giấc tự do, không chung chủ',
                            'Trạm sạc xe điện'
                        ];
                        $selectedAmenities = old('amenities', []);
                    @endphp

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($presetAmenities as $amenity)
                            <label class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-900/40 border border-slate-800 hover:border-indigo-500/40 cursor-pointer transition">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity }}" {{ in_array($amenity, $selectedAmenities) ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600 bg-slate-800 border-slate-700 focus:ring-0">
                                <span class="text-xs text-slate-300 font-medium">{{ $amenity }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Media & Description Card -->
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="text-sm font-bold text-sky-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-image"></i>
                        <span>Hình ảnh & Giới thiệu cơ sở</span>
                    </h3>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-300 mb-1.5">
                                    Tải ảnh đại diện (File)
                                </label>
                                <input type="file" name="image_file" accept="image/*" class="w-full px-3 py-2 bg-slate-900/60 border border-slate-800 rounded-xl text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer focus:outline-none">
                                <p class="text-[11px] text-slate-500 mt-1">Định dạng JPG, PNG, WEBP tối đa 5MB</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-300 mb-1.5">
                                    Hoặc dán URL ảnh trực tiếp
                                </label>
                                <input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://example.com/banner.jpg" class="w-full px-4 py-2.5 bg-slate-900/60 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">
                                Mô Tả Chi Tiết Cơ Sở
                            </label>
                            <textarea name="description" rows="4" placeholder="Giới thiệu vị trí giao thông, không gian tòa nhà, tiện ích ngoại khu..." class="w-full px-4 py-2.5 bg-slate-900/60 border border-slate-800 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-indigo-500 transition">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Submit Actions -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.buildings.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-800 bg-slate-900/50 hover:bg-slate-800 text-slate-300 text-sm font-semibold transition">
                        Hủy bỏ
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/25 transition">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Lưu Cơ Sở Mới</span>
                    </button>
                </div>
            </form>

        </main>
    </div>
</body>
</html>
