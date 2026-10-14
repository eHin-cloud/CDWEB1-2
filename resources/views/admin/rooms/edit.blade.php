<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Chỉnh sửa thông tin phòng trọ - SmartRoom.">
    <title>Chỉnh Sửa Phòng Trọ - SmartRoom</title>
    @include('admin.partials.theme-head-script')
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
            background: rgba(13, 18, 31, 0.45);
            backdrop-filter: blur(16px);
            border: 1px border-slate-800/80;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white overflow-hidden">

    <!-- Decorative glows -->
    <div class="absolute top-[-10%] right-[-10%] w-[400px] h-[400px] rounded-full bg-indigo-600/5 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[400px] h-[400px] rounded-full bg-emerald-600/5 blur-[100px] pointer-events-none"></div>

    <!-- SIDEBAR -->
    @include('admin.partials.sidebar')

    <!-- MAIN APP WRAPPER -->
    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        
        <header class="h-16 border-b border-slate-900 bg-[#080b11]/80 backdrop-blur-md flex items-center justify-between px-8 sticky top-0 z-20">
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold text-slate-100">Chỉnh Sửa Thông Tin Phòng Trọ</h2>
            </div>
            <div class="flex items-center gap-3">
                @include('admin.partials.accent-picker')
                <button type="button" onclick="toggleThemeMode()" class="theme-toggle-button" aria-label="Chuyển chế độ sáng tối">
                    <i class="fa-solid fa-moon" data-theme-icon></i>
                </button>
            </div>
        </header>

        <main class="p-8 flex-grow overflow-y-auto">

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-semibold">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Container -->
            <div class="glass-card rounded-2xl p-8 border border-slate-800/40 relative max-w-3xl mx-auto">
                <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                
                <h3 class="text-lg font-bold text-slate-200 mb-6 flex items-center gap-2">
                    <i class="fa-regular fa-pen-to-square text-indigo-400"></i> Chỉnh Sửa Phòng Trọ P.{{ $room->room_number }}
                </h3>

                <form id="edit-room-form" action="{{ route('admin.rooms.update', $room->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- Container lưu trữ danh sách ảnh bị xóa -->
                    <div id="deleted-images-container"></div>

                    <!-- OPTIMISTIC LOCKING: Trường ẩn version -->
                    <input type="hidden" name="version" value="{{ $room->version }}">

                    <!-- Tòa nhà và số phòng -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tòa Nhà <span class="text-rose-500">*</span></label>
                            <select name="building_id" id="building_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                                @foreach($buildings as $building)
                                    <option value="{{ $building->id }}" {{ $room->building_id == $building->id ? 'selected' : '' }}>{{ $building->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-xs text-rose-400 mt-1 hidden" id="err-building"></span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số Phòng <span class="text-rose-500">*</span></label>
                            <input type="text" name="room_number" id="room_number" required 
                                   value="{{ old('room_number', $room->room_number) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" 
                                   placeholder="Ví dụ: 101, 202" onblur="validateRoomNumber()">
                            <span class="text-xs text-rose-400 mt-1 hidden" id="err-room_number"></span>
                        </div>
                    </div>

                    <!-- Tầng, Diện tích, Trạng thái, Loại phòng -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tầng <span class="text-rose-500">*</span></label>
                            <input type="text" name="floor" id="floor" required 
                                   value="{{ old('floor', $room->floor) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" 
                                   placeholder="Ví dụ: 1, 2" oninput="sanitizeNumberInput(this)" onblur="validateFloor()">
                            <span class="text-xs text-rose-400 mt-1 hidden" id="err-floor"></span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Diện Tích (m²) <span class="text-rose-500">*</span></label>
                            <input type="text" name="area" id="area" required 
                                   value="{{ old('area', $room->area) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" 
                                   placeholder="Ví dụ: 25" oninput="sanitizeNumberInput(this)" onblur="validateArea()">
                            <span class="text-xs text-rose-400 mt-1 hidden" id="err-area"></span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Trạng Thái <span class="text-rose-500">*</span></label>
                            <select name="status" id="status" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                                <option value="empty" {{ old('status', $room->status) === 'empty' ? 'selected' : '' }}>Trống</option>
                                <option value="occupied" {{ old('status', $room->status) === 'occupied' ? 'selected' : '' }}>Đầy (Đang thuê)</option>
                                <option value="maintenance" {{ old('status', $room->status) === 'maintenance' ? 'selected' : '' }}>Đang sửa chữa</option>
                                <option value="overdue" {{ old('status', $room->status) === 'overdue' ? 'selected' : '' }}>Nợ tiền</option>
                            </select>
                        </div>
                        <div>
                            @php
                                $currentType = old('room_type', $room->room_type);
                                if ($currentType === 'normal') $currentType = 'standard';
                            @endphp
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hạng Phòng <span class="text-rose-500">*</span></label>
                            <select name="room_type" id="room_type" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                                <option value="standard" {{ $currentType === 'standard' ? 'selected' : '' }}>Standard</option>
                                <option value="deluxe" {{ $currentType === 'deluxe' ? 'selected' : '' }}>Deluxe</option>
                                <option value="vip" {{ $currentType === 'vip' ? 'selected' : '' }}>VIP</option>
                                <option value="studio" {{ $currentType === 'studio' ? 'selected' : '' }}>Studio</option>
                            </select>
                        </div>
                    </div>

                    <!-- Hình thức thuê, Giá thuê, Tiền cọc -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            @php
                                $currentRentalType = old('rental_type', $room->rental_type ?? 'month');
                            @endphp
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hình Thức Thuê <span class="text-rose-500">*</span></label>
                            <select name="rental_type" id="rental_type" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none">
                                <option value="month" {{ $currentRentalType === 'month' ? 'selected' : '' }}>Theo tháng</option>
                                <option value="day" {{ $currentRentalType === 'day' ? 'selected' : '' }}>Theo ngày</option>
                                <option value="hour" {{ $currentRentalType === 'hour' ? 'selected' : '' }}>Theo giờ</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Giá Thuê (VND) <span class="text-rose-500">*</span></label>
                            <input type="text" name="price" id="price" required 
                                   value="{{ old('price', $room->price) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" 
                                   placeholder="Ví dụ: 3000000" oninput="sanitizeNumberInput(this)" onblur="validatePrice()">
                            <span class="text-xs text-rose-400 mt-1 hidden" id="err-price"></span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tiền Cọc (VND)</label>
                            <input type="text" name="deposit" id="deposit" 
                                   value="{{ old('deposit', $room->deposit ?? 0) }}"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" 
                                   placeholder="Ví dụ: 1000000" oninput="sanitizeNumberInput(this)" onblur="validateDeposit()">
                            <span class="text-xs text-rose-400 mt-1 hidden" id="err-deposit"></span>
                        </div>
                    </div>

                    <!-- Hình ảnh và Video -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Quản lý hình ảnh -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Hình Ảnh Minh Họa (Mỗi ảnh ≤ 5MB, tối đa 10 ảnh)</label>
                            </div>
                            <input type="file" name="images[]" id="images" accept="image/jpeg,image/png,image/webp" multiple
                                   class="w-full px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none"
                                   onchange="previewImages(this)">
                            <span class="text-xs text-rose-400 mt-1 hidden" id="err-image"></span>
                            
                            <!-- Hiển thị ảnh hiện tại -->
                            <div class="mt-3" id="current-images-box">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="block text-[11px] font-semibold text-slate-400">Ảnh hiện tại của phòng:</span>
                                    <span class="text-[10px] text-slate-500">Bấm icon thùng rác để xóa ảnh</span>
                                </div>
                                @php
                                    $roomImages = is_array($room->images) ? $room->images : [];
                                    if ($room->image && !in_array($room->image, $roomImages, true)) {
                                        array_unshift($roomImages, $room->image);
                                    }
                                    $roomImages = array_values(array_filter($roomImages));
                                @endphp
                                <div id="current-images-list" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @forelse($roomImages as $roomImage)
                                        @php
                                            $imgSrc = (str_starts_with($roomImage, 'http://') || str_starts_with($roomImage, 'https://'))
                                                ? $roomImage
                                                : asset('storage/' . ltrim($roomImage, '/'));
                                        @endphp
                                        <div class="relative group rounded-lg overflow-hidden border border-slate-800 bg-slate-950 aspect-video flex items-center justify-center transition-all duration-200" data-image-item data-image-path="{{ $roomImage }}">
                                            <img src="{{ $imgSrc }}" class="h-full w-full object-cover"
                                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=600&q=80';">
                                            <button type="button" 
                                                    onclick="markImageDeleted(this, '{{ addslashes($roomImage) }}')" 
                                                    class="absolute top-1.5 right-1.5 w-7 h-7 rounded-lg bg-rose-600/90 hover:bg-rose-600 text-white flex items-center justify-center shadow-lg transition-all hover:scale-110 active:scale-95 cursor-pointer z-10" 
                                                    title="Xóa ảnh này">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </div>
                                    @empty
                                        <div class="col-span-full py-4 text-center rounded-lg border border-dashed border-slate-800 text-slate-500 text-xs" id="no-current-images">
                                            <i class="fa-regular fa-image text-lg mb-1 block"></i>
                                            Chưa có ảnh nào được lưu cho phòng này.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Khối xem trước ảnh mới tải lên -->
                            <div class="mt-3 hidden" id="new-images-box">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="block text-[11px] font-semibold text-emerald-400"><i class="fa-solid fa-cloud-arrow-up mr-1"></i> Ảnh mới đã chọn (sẽ thêm vào khi lưu):</span>
                                    <button type="button" onclick="clearNewImages()" class="text-[10px] text-rose-400 hover:text-rose-300 underline font-semibold cursor-pointer">Hủy chọn ảnh mới</button>
                                </div>
                                <div id="new-preview-list" class="grid grid-cols-2 sm:grid-cols-3 gap-3"></div>
                            </div>
                        </div>

                        <!-- Quản lý video -->
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Video Giới Thiệu Phòng (≤ 30MB)</label>
                            <input type="file" name="video" id="video" accept="video/mp4,video/webm,video/quicktime"
                                   class="w-full px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none"
                                   onchange="previewVideo(this)">
                            <span class="text-xs text-rose-400 mt-1 hidden" id="err-video"></span>

                            <!-- Hiển thị video hiện tại hoặc preview video mới -->
                            <div class="mt-3 {{ $room->video ? '' : 'hidden' }}" id="video-preview-box">
                                <div class="flex items-center justify-between mb-1.5" id="video-header-bar">
                                    <span class="text-[11px] text-slate-400 font-semibold" id="video-status-label">
                                        <i class="fa-solid fa-video mr-1 text-indigo-400"></i> Video giới thiệu phòng:
                                    </span>
                                    <button type="button" id="btn-delete-video" onclick="markVideoDeleted()"
                                            class="px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-semibold flex items-center gap-1.5 transition-colors border border-rose-500/30 cursor-pointer">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i> Xóa video này
                                    </button>
                                </div>

                                <video id="preview-video" class="w-full max-h-60 rounded-lg border border-slate-800 bg-black" controls
                                       @if($room->video) src="{{ asset('storage/' . $room->video) }}" @endif></video>

                                <!-- Cảnh báo khi người dùng bấm xóa video hiện tại -->
                                <div id="video-deleted-alert" class="hidden p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-center justify-between mt-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                        <span>Video sẽ bị gỡ bỏ khi bấm <strong>Lưu Thay Đổi</strong>.</span>
                                    </div>
                                    <button type="button" onclick="undoDeleteVideo()" class="text-indigo-400 underline font-bold hover:text-indigo-300 cursor-pointer">Hoàn tác</button>
                                </div>
                            </div>
                            <input type="hidden" name="delete_video" id="delete_video" value="0">
                        </div>
                    </div>

                    <!-- Thông tin Số Sản Xuất Công Tơ (Phục vụ AI Quét Hàng Loạt) -->
                    <div class="rounded-2xl p-5 border border-indigo-500/20 bg-indigo-950/10 space-y-4">
                        <div class="flex items-center gap-2 text-indigo-400 font-bold text-xs uppercase tracking-wider">
                            <i class="fa-solid fa-barcode"></i> Định Danh Công Tơ Đo Lường (Hỗ trợ AI Quét Hàng Loạt)
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 mb-1.5">Số Sản Xuất (Số SX) Công Tơ Điện</label>
                                <div class="relative">
                                    <input type="text" name="electric_meter_serial" id="electric_meter_serial" value="{{ old('electric_meter_serial', $room->electric_meter_serial) }}"
                                           class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none font-mono" 
                                           placeholder="Ví dụ: 16258817">
                                    <span class="absolute left-3.5 top-3 text-amber-400 text-xs"><i class="fa-solid fa-bolt"></i></span>
                                </div>
                                <span class="text-[11px] text-slate-500 mt-1 block">Dập trên mặt đồng hồ GELEX EMIC hoặc công tơ điện tử.</span>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 mb-1.5">Số Sản Xuất (Số SX) Đồng Hồ Nước</label>
                                <div class="relative">
                                    <input type="text" name="water_meter_serial" id="water_meter_serial" value="{{ old('water_meter_serial', $room->water_meter_serial) }}"
                                           class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none font-mono" 
                                           placeholder="Ví dụ: DHN-88219">
                                    <span class="absolute left-3.5 top-3 text-cyan-400 text-xs"><i class="fa-solid fa-droplet"></i></span>
                                </div>
                                <span class="text-[11px] text-slate-500 mt-1 block">Mã số dập trên mặt kính hoặc vành đồng hồ nước.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tiện ích (Checkbox) -->
                    @php
                        $roomAmenities = is_array($room->amenities) ? $room->amenities : [];
                    @endphp
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Tiện Ích Đi Kèm</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="Máy lạnh" {{ in_array('Máy lạnh', $roomAmenities) || in_array('điều hòa', $roomAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-indigo-600 bg-slate-900 focus:ring-indigo-500">
                                <span><i class="fa-solid fa-snowflake text-cyan-400 mr-1"></i> Máy lạnh</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="Nóng lạnh" {{ in_array('Nóng lạnh', $roomAmenities) || in_array('nước nóng', $roomAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-indigo-600 bg-slate-900 focus:ring-indigo-500">
                                <span><i class="fa-solid fa-fire text-amber-400 mr-1"></i> Nóng lạnh</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="Minibar" {{ in_array('Minibar', $roomAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-indigo-600 bg-slate-900 focus:ring-indigo-500">
                                <span><i class="fa-solid fa-wine-glass text-rose-400 mr-1"></i> Minibar</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="SmartLock" {{ in_array('SmartLock', $roomAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-indigo-600 bg-slate-900 focus:ring-indigo-500">
                                <span><i class="fa-solid fa-key text-emerald-400 mr-1"></i> SmartLock</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="Gác lửng" {{ in_array('Gác lửng', $roomAmenities) || in_array('gác lửng', $roomAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-indigo-600 bg-slate-900 focus:ring-indigo-500">
                                <span>Gác lửng</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="Máy giặt" {{ in_array('Máy giặt', $roomAmenities) || in_array('máy giặt', $roomAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-indigo-600 bg-slate-900 focus:ring-indigo-500">
                                <span>Máy giặt</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="Tủ lạnh" {{ in_array('Tủ lạnh', $roomAmenities) || in_array('tủ lạnh', $roomAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-indigo-600 bg-slate-900 focus:ring-indigo-500">
                                <span>Tủ lạnh</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="Ban công" {{ in_array('Ban công', $roomAmenities) || in_array('ban công', $roomAmenities) ? 'checked' : '' }} class="rounded border-slate-800 text-indigo-600 bg-slate-900 focus:ring-indigo-500">
                                <span>Ban công</span>
                            </label>
                        </div>
                    </div>

                    <!-- Mô tả -->
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Mô Tả Chi Tiết (Không chứa mã HTML)</label>
                            <button type="button" onclick="generateRoomDescriptionWithAi(this)" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> AI viết mô tả
                            </button>
                        </div>
                        <textarea name="description" id="description" rows="3" 
                                  class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-850 text-slate-200 text-sm focus:border-indigo-500 focus:outline-none" 
                                  placeholder="Nhập thông tin mô tả chi tiết phòng trọ..." onblur="validateDescription()">{{ old('description', $room->description) }}</textarea>
                        <span class="text-xs text-rose-400 mt-1 hidden" id="err-description"></span>
                    </div>

                    <!-- Buttons -->
                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-900">
                        <a href="{{ route('admin.rooms.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                            Hủy bỏ
                        </a>
                        <button type="submit" id="submit-btn" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-save"></i> Cập Nhật Phòng
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- JS VALIDATION, CHỐNG F12 & CHỐNG SPAM CLICK -->
    <script>
        function cleanString(str) {
            if (!str) return '';
            let cleaned = str.replace(/　/g, ' ');
            const fullWidth = ['０', '１', '２', '３', '４', '５', '６', '７', '８', '９'];
            const halfWidth = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            for (let i = 0; i < 10; i++) {
                cleaned = cleaned.replace(new RegExp(fullWidth[i], 'g'), halfWidth[i]);
            }
            return cleaned.trim();
        }

        function sanitizeNumberInput(input) {
            input.value = cleanString(input.value).replace(/[^0-9]/g, '');
        }

        // [ERR_03_01 & ERR_03_02]: Số phòng
        function validateRoomNumber() {
            const input = document.getElementById('room_number');
            input.value = cleanString(input.value);
            const errSpan = document.getElementById('err-room_number');
            const buildingSelect = document.getElementById('building_id');

            // [ERR_03_01]: Để trống số phòng
            if (input.value === '') {
                errSpan.textContent = 'Số phòng không được để trống.';
                errSpan.classList.remove('hidden');
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                return false;
            }

            // [ERR_03_02]: Số phòng bị trùng trong cùng tòa (loại trừ chính phòng hiện tại)
            const buildingId = buildingSelect ? buildingSelect.value : '';
            if (buildingId && input.value !== '') {
                fetch(`{{ route('admin.rooms.check_number') }}?building_id=${buildingId}&room_number=${encodeURIComponent(input.value)}&exclude_id={{ $room->id }}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.exists) {
                            errSpan.textContent = 'Số phòng này đã tồn tại trong cơ sở lưu trú, vui lòng chọn số khác.';
                            errSpan.classList.remove('hidden');
                            input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                        } else {
                            errSpan.classList.add('hidden');
                            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
                        }
                    })
                    .catch(() => {});
            }

            errSpan.classList.add('hidden');
            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
            return true;
        }

        // [ERR_03_07]: Để trống tầng hoặc tầng <= 0
        function validateFloor() {
            const input = document.getElementById('floor');
            input.value = cleanString(input.value);
            const errSpan = document.getElementById('err-floor');
            if (input.value === '' || isNaN(input.value) || parseInt(input.value) <= 0) {
                errSpan.textContent = 'Vui lòng nhập tầng bố trí phòng.';
                errSpan.classList.remove('hidden');
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                return false;
            }
            errSpan.classList.add('hidden');
            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
            return true;
        }

        // [ERR_03_05 & ERR_03_06]: Diện tích phòng
        function validateArea() {
            const input = document.getElementById('area');
            input.value = cleanString(input.value);
            const errSpan = document.getElementById('err-area');
            if (input.value === '') {
                errSpan.textContent = 'Vui lòng nhập diện tích phòng lưu trú.';
                errSpan.classList.remove('hidden');
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                return false;
            }
            if (isNaN(input.value) || parseFloat(input.value) <= 0) {
                errSpan.textContent = 'Diện tích phòng phải lớn hơn 0 mét vuông.';
                errSpan.classList.remove('hidden');
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                return false;
            }
            errSpan.classList.add('hidden');
            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
            return true;
        }

        // [ERR_03_03 & ERR_03_04]: Giá thuê phòng
        function validatePrice() {
            const input = document.getElementById('price');
            input.value = cleanString(input.value);
            const errSpan = document.getElementById('err-price');
            if (input.value === '') {
                errSpan.textContent = 'Giá thuê phòng hàng tháng là bắt buộc.';
                errSpan.classList.remove('hidden');
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                return false;
            }
            if (isNaN(input.value) || parseInt(input.value) <= 0) {
                errSpan.textContent = 'Giá thuê phòng phải là số nguyên dương lớn hơn 0 đồng.';
                errSpan.classList.remove('hidden');
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                return false;
            }
            errSpan.classList.add('hidden');
            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
            return true;
        }

        function validateDeposit() {
            const input = document.getElementById('deposit');
            if (!input) return true;
            input.value = cleanString(input.value);
            const errSpan = document.getElementById('err-deposit');
            if (input.value !== '' && (isNaN(input.value) || parseInt(input.value) < 0)) {
                errSpan.textContent = 'Tiền cọc không được là số âm.';
                errSpan.classList.remove('hidden');
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                return false;
            }
            errSpan.classList.add('hidden');
            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
            return true;
        }

        function validateDescription() {
            const input = document.getElementById('description');
            const errSpan = document.getElementById('err-description');
            if (input.value.length > 1000) {
                errSpan.textContent = 'Mô tả không được vượt quá 1000 ký tự.';
                errSpan.classList.remove('hidden');
                return false;
            }
            input.value = input.value.replace(/<\/?[^>]+(>|$)/g, "");
            errSpan.classList.add('hidden');
            return true;
        }

        // Quản lý xóa ảnh hiện tại
        function markImageDeleted(btn, imagePath) {
            if (!confirm('Bạn có chắc chắn muốn xóa ảnh này khỏi phòng trọ không?')) {
                return;
            }
            const item = btn.closest('[data-image-item]');
            if (item) {
                item.style.transition = 'all 0.25s ease';
                item.style.opacity = '0';
                item.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    item.remove();
                    const list = document.getElementById('current-images-list');
                    if (list && list.querySelectorAll('[data-image-item]').length === 0) {
                        list.innerHTML = `
                            <div class="col-span-full py-4 text-center rounded-lg border border-dashed border-slate-800 text-slate-500 text-xs" id="no-current-images">
                                <i class="fa-regular fa-image text-lg mb-1 block"></i>
                                Đã xóa hết ảnh cũ. Hãy chọn ảnh mới ở trên nếu muốn bổ sung ảnh cho phòng.
                            </div>
                        `;
                    }
                }, 250);
            }

            const container = document.getElementById('deleted-images-container');
            if (container) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_images[]';
                input.value = imagePath;
                container.appendChild(input);
            }
        }

        // Hủy chọn các ảnh mới
        function clearNewImages() {
            const input = document.getElementById('images');
            if (input) {
                input.value = '';
            }
            const newBox = document.getElementById('new-images-box');
            const newList = document.getElementById('new-preview-list');
            if (newBox) newBox.classList.add('hidden');
            if (newList) newList.innerHTML = '';
        }

        // Preview ảnh mới và validate file ảnh (ERR_03_08 & ERR_03_09)
        function previewImages(input) {
            const errSpan = document.getElementById('err-image');
            const newBox = document.getElementById('new-images-box');
            const newList = document.getElementById('new-preview-list');
            const files = Array.from(input.files || []);

            if (files.length === 0) {
                if (newBox) newBox.classList.add('hidden');
                if (newList) newList.innerHTML = '';
                return;
            }

            if (files.length > 10) {
                errSpan.textContent = 'Chỉ được chọn tối đa 10 hình ảnh mỗi lần.';
                errSpan.classList.remove('hidden');
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                input.value = '';
                if (newBox) newBox.classList.add('hidden');
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            for (const file of files) {
                // [ERR_03_08]: Tải tệp ảnh sai định dạng
                if (!allowedTypes.includes(file.type)) {
                    errSpan.textContent = 'Tệp ảnh phòng phải có định dạng hợp lệ (JPG, JPEG, PNG, WEBP).';
                    errSpan.classList.remove('hidden');
                    input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                    input.value = '';
                    if (newBox) newBox.classList.add('hidden');
                    return;
                }

                // [ERR_03_09]: Dung lượng ảnh vượt quá 5MB
                if (file.size > 5 * 1024 * 1024) {
                    errSpan.textContent = 'Ảnh tải lên vượt quá dung lượng tối đa cho phép (5MB).';
                    errSpan.classList.remove('hidden');
                    input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                    input.value = '';
                    if (newBox) newBox.classList.add('hidden');
                    return;
                }
            }

            errSpan.classList.add('hidden');
            input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
            if (newList) newList.innerHTML = '';
            if (newBox) newBox.classList.remove('hidden');

            files.forEach((file) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const wrap = document.createElement('div');
                    wrap.className = 'relative rounded-lg overflow-hidden border border-emerald-500/40 bg-slate-950 aspect-video';
                    wrap.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover">`;
                    newList.appendChild(wrap);
                };
                reader.readAsDataURL(file);
            });
        }

        // Quản lý xóa và hoàn tác video hiện tại
        function markVideoDeleted() {
            const delInput = document.getElementById('delete_video');
            const video = document.getElementById('preview-video');
            const btnDel = document.getElementById('btn-delete-video');
            const alertBox = document.getElementById('video-deleted-alert');
            const statusLabel = document.getElementById('video-status-label');

            if (delInput) delInput.value = '1';
            if (video) video.classList.add('hidden');
            if (btnDel) btnDel.classList.add('hidden');
            if (alertBox) alertBox.classList.remove('hidden');
            if (statusLabel) statusLabel.innerHTML = '<i class="fa-solid fa-video-slash mr-1 text-rose-400"></i> Video hiện tại (đã đánh dấu xóa):';
        }

        function undoDeleteVideo() {
            const delInput = document.getElementById('delete_video');
            const video = document.getElementById('preview-video');
            const btnDel = document.getElementById('btn-delete-video');
            const alertBox = document.getElementById('video-deleted-alert');
            const statusLabel = document.getElementById('video-status-label');

            if (delInput) delInput.value = '0';
            if (video) video.classList.remove('hidden');
            if (btnDel) btnDel.classList.remove('hidden');
            if (alertBox) alertBox.classList.add('hidden');
            if (statusLabel) statusLabel.innerHTML = '<i class="fa-solid fa-video mr-1 text-indigo-400"></i> Video giới thiệu phòng:';
        }

        function previewVideo(input) {
            const file = input.files[0];
            const errSpan = document.getElementById('err-video');
            const previewBox = document.getElementById('video-preview-box');
            const previewVideo = document.getElementById('preview-video');
            const delInput = document.getElementById('delete_video');
            const btnDel = document.getElementById('btn-delete-video');
            const alertBox = document.getElementById('video-deleted-alert');
            const statusLabel = document.getElementById('video-status-label');

            if (!file) {
                return;
            }

            const allowedTypes = ['video/mp4', 'video/webm', 'video/quicktime'];
            if (!allowedTypes.includes(file.type)) {
                errSpan.textContent = 'Video phải có định dạng mp4, webm hoặc mov.';
                errSpan.classList.remove('hidden');
                input.value = '';
                return;
            }

            if (file.size > 30 * 1024 * 1024) {
                errSpan.textContent = 'Dung lượng video không được vượt quá 30MB.';
                errSpan.classList.remove('hidden');
                input.value = '';
                return;
            }

            errSpan.classList.add('hidden');
            if (delInput) delInput.value = '0';
            if (alertBox) alertBox.classList.add('hidden');
            if (previewVideo) {
                previewVideo.src = URL.createObjectURL(file);
                previewVideo.classList.remove('hidden');
            }
            if (btnDel) {
                btnDel.classList.remove('hidden');
                btnDel.innerHTML = '<i class="fa-solid fa-xmark text-xs"></i> Hủy video mới';
                btnDel.onclick = function () {
                    input.value = '';
                    const origVideo = @json($room->video);
                    if (origVideo) {
                        previewVideo.src = "{{ asset('storage/' . $room->video) }}";
                        btnDel.innerHTML = '<i class="fa-solid fa-trash-can text-[11px]"></i> Xóa video này';
                        btnDel.onclick = markVideoDeleted;
                        if (statusLabel) statusLabel.innerHTML = '<i class="fa-solid fa-video mr-1 text-indigo-400"></i> Video giới thiệu phòng:';
                    } else {
                        previewBox.classList.add('hidden');
                    }
                };
            }
            if (statusLabel) {
                statusLabel.innerHTML = '<i class="fa-solid fa-video mr-1 text-emerald-400"></i> Video mới chuẩn bị tải lên:';
            }
            previewBox.classList.remove('hidden');
        }

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function selectedAmenities() {
            return Array.from(document.querySelectorAll('input[name="amenities[]"]:checked')).map(input => input.value);
        }

        function generateRoomDescriptionWithAi(btn) {
            const price = document.getElementById('price').value;
            const area = document.getElementById('area').value;
            const description = document.getElementById('description');

            if (!price || !area) {
                alert('Vui lòng nhập giá thuê và diện tích trước khi dùng AI.');
                return;
            }

            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang viết...';

            fetch("{{ route('admin.rooms.description.ai') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: JSON.stringify({
                    room_number: document.getElementById('room_number').value,
                    floor: Number(document.getElementById('floor').value || 0),
                    room_type: document.getElementById('room_type').value,
                    rental_type: document.getElementById('rental_type')?.value || 'month',
                    price: Number(price),
                    deposit: Number(document.getElementById('deposit')?.value || 0),
                    area: Number(area),
                    status: document.getElementById('status').value,
                    amenities: selectedAmenities()
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = original;

                if (!data.success) {
                    alert('Không thể tạo mô tả bằng AI.');
                    return;
                }

                description.value = data.description.description || description.value;
                validateDescription();
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = original;
                alert('Không thể kết nối AI để tạo mô tả.');
            });
        }

        let isFormSubmitting = false;
        const form = document.getElementById('edit-room-form');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            
            const isOk = validateRoomNumber() && validateFloor() && validateArea() && validatePrice() && validateDeposit() && validateDescription();
            if (!isOk) {
                alert('Vui lòng kiểm tra lại thông tin form.');
                return false;
            }

            isFormSubmitting = true;
            if (typeof observer !== 'undefined' && observer) {
                observer.disconnect();
            }

            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang lưu...';
            
            form.submit();
        });

        // 2. Chặn các phím tắt mở DevTools
        document.addEventListener('keydown', function (e) {
            if (e.key === 'F12' || e.keyCode === 123) {
                e.preventDefault();
                alert('Hành động can thiệp hệ thống bị chặn!');
                return false;
            }
            if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C' || e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 67)) {
                e.preventDefault();
                alert('Hành động can thiệp hệ thống bị chặn!');
                return false;
            }
            if (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.keyCode === 85)) {
                e.preventDefault();
                alert('Hành động can thiệp hệ thống bị chặn!');
                return false;
            }
        });

        // 3. Chặn chuột phải trên toàn trang
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
            alert('Chuột phải đã bị vô hiệu hóa để bảo mật!');
            return false;
        });

        // 4. MutationObserver giám sát việc hack sửa thuộc tính DOM
        const targetBtn = document.getElementById('submit-btn');
        let observer = null;
        if (targetBtn) {
            observer = new MutationObserver((mutationsList) => {
                if (isFormSubmitting) return;
                for (let mutation of mutationsList) {
                    if (mutation.type === 'attributes') {
                        alert('Phát hiện hành vi can thiệp hệ thống!');
                        window.location.reload();
                    }
                }
            });
            observer.observe(targetBtn, { attributes: true });
        }
    </script>
    <script src="{{ asset('js/admin-sidebar.js') }}"></script>
</body>
</html>
