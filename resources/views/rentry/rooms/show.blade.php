@php
    $images = $room['image_angles'] ?? collect($room['image_urls'] ?? [])->map(fn ($url, $index) => [
        'url' => $url,
        'label' => 'Ảnh thực tế ' . ($index + 1),
    ])->all();
    $fullDescription = collect([
        $room['location_description'] ?? null,
        $room['scenery_description'] ?? null,
        $room['space_description'] ?? null,
        'Tiện ích nổi bật: ' . (($room['loft_txt'] ?? 'Không') === 'Có' ? 'có gác lửng' : 'không gác lửng') . ', ' . (($room['balcony_txt'] ?? 'Không') === 'Có' ? 'có ban công' : 'không ban công') . ', ' . (($room['pets_txt'] ?? 'Không') === 'Có' ? 'có thể nuôi thú cưng' : 'không nuôi thú cưng') . '.',
    ])->filter()->join(' ');
    $reviewCount = count($room['reviews'] ?? []);
    $initialCost = ($room['price'] * 2) + 350000 + (2 * 20000) + 150000;
@endphp
<!-- DESIGN READ: Premium, bento-inspired dark mode detail page calibrated with Emerald / Teal accent hues, tactile interactive inputs, micro-glows, dynamic asymmetrical media grids, and optimized for smooth viewport-height fluidity without slop. -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $room['title'] }} - chi tiết hình ảnh, video tour, chi phí và đánh giá thực tế.">
    <title>{{ $room['title'] }} - Renty Review</title>
    <script>
        if (localStorage.getItem('renty_theme_mode') === 'light') {
            document.documentElement.classList.add('theme-light');
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif']
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    <style>
        /* Glassmorphism custom styling for premium feel */
        .premium-card {
            background: linear-gradient(145deg, rgba(30, 41, 59, 0.45), rgba(15, 23, 42, 0.35));
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(148, 163, 184, 0.08);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03), 0 20px 50px rgba(2, 6, 23, 0.15);
        }
        .theme-light .premium-card {
            background: rgba(255, 255, 255, 0.85);
            border-color: rgba(203, 213, 225, 0.7);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.05);
        }
        .hover-lift {
            transition: transform 0.25s cubic-bezier(0.2, 1, 0.3, 1), border-color 0.25s ease, box-shadow 0.25s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            border-color: rgba(16, 185, 129, 0.3);
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.25);
        }
        /* Custom scrollbar for gallery list */
        .custom-scroll::-webkit-scrollbar {
            height: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.4);
            border-radius: 99px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.2);
            border-radius: 99px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(16, 185, 129, 0.4);
        }

        /* 3D Tilt Glare Elements */
        .tilt-element {
            transform-style: preserve-3d;
            will-change: transform;
            transition: transform 0.2s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.3s ease, border-color 0.3s ease;
            position: relative;
        }

        .card-3d-glare {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(circle at var(--glare-x, 50%) var(--glare-y, 50%), rgba(255, 255, 255, 0.1) 0%, transparent 60%);
            z-index: 5;
            opacity: 0;
            transition: opacity 0.3s ease;
            mix-blend-mode: overlay;
            border-radius: inherit;
        }
        .tilt-element:hover .card-3d-glare {
            opacity: 1;
        }

        @keyframes starWarningPulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); border-color: rgba(245, 158, 11, 0.3); }
            50% { transform: scale(1.02); box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.4); border-color: #f59e0b; }
        }
        .star-warning-animate {
            animation: starWarningPulse 0.4s ease-in-out 3;
            border-color: #f59e0b !important;
        }
        .is-invalid {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 1px #ef4444 !important;
        }

        /* AI Summary Glowing Pulsing Box */
        #review-summary-box {
            position: relative;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(6, 182, 212, 0.05) 100%) !important;
            border: 1px solid rgba(16, 185, 129, 0.25) !important;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.05) !important;
            animation: summary-pulse 3s infinite alternate;
        }
        @keyframes summary-pulse {
            0% {
                border-color: rgba(16, 185, 129, 0.25);
                box-shadow: 0 10px 30px rgba(16, 185, 129, 0.05);
            }
            100% {
                border-color: rgba(6, 182, 212, 0.4);
                box-shadow: 0 10px 35px rgba(6, 182, 212, 0.12);
            }
        }

        /* =========================================================
           ROOM DETAIL: LANDLORD OWNER CARD & BOOKING SIDEBAR (LIGHT THEME)
           ========================================================= */
        html.theme-light .booking-sidebar-card,
        body.theme-light .booking-sidebar-card {
            background: #ffffff !important;
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
        }

        html.theme-light .landlord-owner-title,
        body.theme-light .landlord-owner-title {
            color: #0f172a !important;
        }

        html.theme-light .landlord-owner-pill,
        body.theme-light .landlord-owner-pill {
            background-color: #eff6ff !important;
            border-color: #bfdbfe !important;
            color: #2563eb !important;
            font-weight: 700 !important;
        }

        html.theme-light .landlord-owner-card,
        body.theme-light .landlord-owner-card {
            background: linear-gradient(145deg, #ffffff 0%, #f5f3ff 100%) !important;
            background-color: #ffffff !important;
            border: 1.5px solid #c7d2fe !important;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.12), 0 4px 6px -2px rgba(99, 102, 241, 0.05) !important;
        }

        html.theme-light .landlord-owner-badge,
        body.theme-light .landlord-owner-badge {
            background-color: #ede9fe !important;
            border-color: #c4b5fd !important;
            color: #4338ca !important;
            font-weight: 800 !important;
        }

        html.theme-light .landlord-owner-sub,
        body.theme-light .landlord-owner-sub {
            color: #64748b !important;
            font-weight: 600 !important;
        }

        html.theme-light .landlord-owner-text,
        body.theme-light .landlord-owner-text {
            color: #1e293b !important;
            font-weight: 500 !important;
            line-height: 1.6 !important;
        }

        html.theme-light .landlord-owner-name,
        body.theme-light .landlord-owner-name {
            color: #4338ca !important;
            font-weight: 800 !important;
        }

        html.theme-light .landlord-owner-divider,
        body.theme-light .landlord-owner-divider {
            border-top-color: #e0e7ff !important;
        }

        html.theme-light .landlord-owner-primary-btn,
        body.theme-light .landlord-owner-primary-btn {
            background: linear-gradient(135deg, #6366f1 0%, #0d9488 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.35) !important;
        }
        html.theme-light .landlord-owner-primary-btn:hover,
        body.theme-light .landlord-owner-primary-btn:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #0f766e 100%) !important;
            box-shadow: 0 6px 20px 0 rgba(99, 102, 241, 0.45) !important;
        }

        html.theme-light .landlord-owner-secondary-btn,
        body.theme-light .landlord-owner-secondary-btn {
            background: #ffffff !important;
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #1e293b !important;
            font-weight: 700 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
        }
        html.theme-light .landlord-owner-secondary-btn:hover,
        body.theme-light .landlord-owner-secondary-btn:hover {
            background-color: #f8fafc !important;
            border-color: #6366f1 !important;
            color: #4338ca !important;
        }
        html.theme-light .landlord-owner-secondary-btn i,
        body.theme-light .landlord-owner-secondary-btn i {
            color: #4f46e5 !important;
        }

        /* Superadmin card in Light Mode */
        html.theme-light .admin-auditor-title,
        body.theme-light .admin-auditor-title {
            color: #0f172a !important;
        }
        html.theme-light .admin-auditor-pill,
        body.theme-light .admin-auditor-pill {
            background-color: #fef3c7 !important;
            border-color: #fde68a !important;
            color: #b45309 !important;
        }
        html.theme-light .admin-auditor-card,
        body.theme-light .admin-auditor-card {
            background: linear-gradient(145deg, #ffffff 0%, #fffbeb 100%) !important;
            border: 1.5px solid #fde68a !important;
            box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.1) !important;
        }
        html.theme-light .admin-auditor-badge,
        body.theme-light .admin-auditor-badge {
            background-color: #fef3c7 !important;
            border-color: #fde68a !important;
            color: #92400e !important;
        }
        html.theme-light .admin-auditor-sub,
        body.theme-light .admin-auditor-sub {
            color: #64748b !important;
        }
        html.theme-light .admin-auditor-text,
        body.theme-light .admin-auditor-text {
            color: #1e293b !important;
        }
        html.theme-light .admin-auditor-name,
        body.theme-light .admin-auditor-name {
            color: #b45309 !important;
        }
        html.theme-light .admin-auditor-box,
        body.theme-light .admin-auditor-box {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }
        html.theme-light .admin-auditor-box .text-slate-400,
        body.theme-light .admin-auditor-box .text-slate-400 {
            color: #475569 !important;
        }
        html.theme-light .admin-auditor-box .text-slate-200,
        body.theme-light .admin-auditor-box .text-slate-200 {
            color: #0f172a !important;
        }
        html.theme-light .admin-auditor-secondary-btn,
        body.theme-light .admin-auditor-secondary-btn {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #1e293b !important;
        }
        html.theme-light .admin-auditor-toggle-btn,
        body.theme-light .admin-auditor-toggle-btn {
            background-color: #f1f5f9 !important;
            border-color: #e2e8f0 !important;
            color: #475569 !important;
        }

        /* Booking Guest Form in Light Mode */
        html.theme-light .booking-sidebar-card h2,
        body.theme-light .booking-sidebar-card h2 {
            color: #0f172a !important;
        }
        html.theme-light .booking-sidebar-card label,
        body.theme-light .booking-sidebar-card label {
            color: #334155 !important;
        }
        html.theme-light .booking-sidebar-card input:not([type="submit"]):not([type="button"]),
        body.theme-light .booking-sidebar-card input:not([type="submit"]):not([type="button"]),
        html.theme-light .booking-sidebar-card textarea,
        body.theme-light .booking-sidebar-card textarea {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        html.theme-light .booking-sidebar-card input::placeholder,
        body.theme-light .booking-sidebar-card input::placeholder,
        html.theme-light .booking-sidebar-card textarea::placeholder,
        body.theme-light .booking-sidebar-card textarea::placeholder {
            color: #94a3b8 !important;
        }
        html.theme-light .booking-sidebar-card input[type="datetime-local"],
        body.theme-light .booking-sidebar-card input[type="datetime-local"] {
            color-scheme: light !important;
        }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen overflow-x-hidden selection:bg-emerald-500 selection:text-white">
    <!-- Toast container cho FEAT_25 -->
    <div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

    @include('header.header')

    <main class="container mx-auto px-6 py-8 max-w-6xl">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-semibold flex items-center justify-between animate-fade-in">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @if(session('code'))
                    <span class="text-xs font-mono bg-emerald-500/20 px-2 py-0.5 rounded">{{ session('code') }}</span>
                @endif
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-semibold flex items-center justify-between animate-fade-in">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
                @if(session('error_code'))
                    <span class="text-xs font-mono bg-rose-500/20 px-2 py-0.5 rounded">{{ session('error_code') }}</span>
                @endif
            </div>
        @endif
        <!-- Back navigation link -->
        <div class="mb-6">
            <a href="{{ route('renty.user') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900/60 border border-slate-800 hover:border-emerald-500/30 text-slate-350 hover:text-emerald-300 text-xs font-bold transition-all active:scale-[0.98]">
                <i class="fa-solid fa-chevron-left text-[10px]"></i> Quay lại danh sách
            </a>
        </div>

        <!-- Room Header Details -->
        <section class="mb-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                <div>
                    <!-- Trust badges row & Trạng thái phòng -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        @if(($room['status'] ?? 'empty') !== 'empty')
                            <span class="px-2.5 py-1 rounded-lg bg-slate-700/80 border border-slate-600 text-[10px] font-black text-slate-200 uppercase tracking-widest flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-lock text-[9px] text-slate-400"></i>
                                <span>ĐÃ THUÊ</span>
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-[9px] font-extrabold text-emerald-300 uppercase tracking-widest">CÒN TRỐNG</span>
                        @endif
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-[9px] font-extrabold text-emerald-300 uppercase tracking-widest">{{ $room['media_source_label'] }}</span>
                        <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-[9px] font-extrabold text-slate-300 uppercase tracking-widest">Phòng {{ $room['room_number'] }}</span>
                        <span class="px-2.5 py-1 rounded-lg border text-[9px] font-extrabold uppercase tracking-widest flex items-center gap-1.5 shadow-sm {{ $room['trust_badge']['class'] }}">
                            <i class="fa-solid {{ $room['trust_badge']['icon'] }}"></i>
                            <span>{{ $room['trust_badge']['label'] }}</span>
                        </span>
                    </div>
                    <h1 class="text-2xl md:text-4xl font-extrabold tracking-tight text-slate-100">{{ $room['title'] }}</h1>
                    <p class="mt-3 text-xs md:text-sm text-slate-400 flex items-start gap-2 max-w-2xl leading-relaxed">
                        <i class="fa-solid fa-location-dot text-emerald-500/80 mt-0.5 shrink-0 text-sm"></i>
                        <span>{{ $room['address'] }}</span>
                    </p>
                </div>
                <div class="text-left md:text-right shrink-0">
                    <div id="lbl_price" name="lbl_price" data-field-name="lbl_price" class="text-3xl md:text-4xl font-black text-emerald-400 drop-shadow-[0_0_15px_rgba(52,211,153,0.1)]">
                        {{ number_format($room['price'], 0, ',', '.') }} đ/tháng
                    </div>
                    <div class="text-[11px] font-bold text-slate-500 mt-1.5">Giá thuê niêm yết · {{ $room['rating'] }} ⭐ · {{ $reviewCount }} đánh giá</div>
                    <div class="mt-3.5 flex items-center gap-2 justify-start md:justify-end flex-wrap">
                        <!-- Nút sao chép liên kết phòng (ERR_06_04) -->
                        <button type="button" id="btn-copy-room-link" onclick="copyRoomLink()" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 text-slate-200 hover:text-white text-xs font-bold transition-all active:scale-95 cursor-pointer shadow-sm">
                            <i class="fa-solid fa-share-nodes text-[11px] text-emerald-400"></i>
                            <span>Chia sẻ phòng</span>
                        </button>
                        <button type="button" id="btnOpenReportModal" onclick="openDetailReportModal()" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-350 hover:text-rose-200 text-xs font-bold transition-colors active:scale-95 cursor-pointer">
                            <i class="fa-solid fa-flag text-[10px]"></i>
                            <span>Báo cáo tin</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Workspace: Left content, Right sticky cards -->
        <section class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-8 items-start">
            <div class="space-y-6">
                <!-- Bento Gallery Grid (Airbnb Style - img_slider) -->
                <div id="img_slider" name="img_slider" data-field-name="img_slider" class="relative rounded-2xl overflow-hidden border border-slate-800 bg-slate-950 shadow-2xl shadow-slate-950/50">
                    @php
                        $imgCount = count($images);
                    @endphp
                    
                    <!-- Ảnh chính tràn toàn bộ khung -->
                    <button type="button" onclick="openZoomWithIndex(activeImageIndex)" class="tilt-element relative block w-full h-[320px] md:h-[480px] rounded-xl overflow-hidden group text-left">
                        <img id="main-image" src="{{ $images[0]['url'] ?? $room['cover_image'] }}" alt="{{ $images[0]['label'] ?? 'Ảnh phòng' }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.015]" onerror="handleImageError(this)">
                        <span class="media-overlay-label absolute left-4 bottom-4 z-10">
                            <i class="fa-solid fa-camera text-emerald-300 mr-1.5"></i><span id="main-image-label">{{ $images[0]['label'] ?? 'View toàn phòng' }}</span>
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 to-transparent z-0"></div>
                        <span class="absolute right-4 bottom-4 w-10 h-10 rounded-xl bg-slate-950/75 border border-white/10 text-emerald-300 flex items-center justify-center backdrop-blur z-10">
                            <i class="fa-solid fa-expand"></i>
                        </span>
                    </button>


                    <!-- Nút xem đầy đủ ở góc -->
                    <button type="button" onclick="openZoom()" class="renty-zoom-btn absolute right-4 bottom-4 px-3.5 py-2 rounded-xl bg-slate-950/80 border border-white/10 text-emerald-355 hover:text-white flex items-center gap-2 text-xs font-bold backdrop-blur transition-all active:scale-95 shadow-md">
                        <i class="fa-solid fa-images text-emerald-400"></i>
                        <span>Xem tất cả {{ $imgCount }} ảnh</span>
                    </button>
                </div>

                <!-- Thư viện ảnh dạng danh sách Thumbnail trượt chọn -->
                <div class="flex gap-2.5 overflow-x-auto py-1.5 px-0.5 custom-scroll">
                    @foreach($images as $index => $image)
                        <button type="button" onclick="selectImage({{ $index }})" title="{{ $image['label'] }}" class="thumb-button shrink-0 relative w-28 h-20 rounded-xl overflow-hidden border {{ $index === 0 ? 'border-emerald-400' : 'border-slate-800' }} hover:border-emerald-500/70 bg-slate-950 transition-all">
                            <img src="{{ $image['url'] }}" alt="{{ $image['label'] }}" class="w-full h-full object-cover" loading="lazy" onerror="handleImageError(this)">
                        </button>
                    @endforeach
                </div>

                <p class="renty-media-note rounded-2xl border border-slate-850 bg-slate-900/25 px-4 py-3.5 text-[11px] leading-relaxed text-slate-400">
                    <i class="fa-solid fa-circle-info text-emerald-400 mr-2 text-xs shrink-0"></i>{{ $room['media_source_note'] }}
                </p>

                <!-- Video / Virtual Tour Section -->
                @if($room['video_url'])
                    <section class="p-5 rounded-2xl bg-slate-900/20 border border-slate-800/80 shadow-[inset_0_1px_0_rgba(255,255,255,0.02)]">
                        <h2 class="text-sm font-extrabold text-slate-100 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-video text-rose-400"></i> Video / Virtual Tour thực tế
                        </h2>
                        <div class="relative rounded-xl overflow-hidden border border-slate-800 bg-black max-h-[420px] shadow-lg">
                            <video class="w-full h-full max-h-[420px]" src="{{ $room['video_url'] }}" controls preload="metadata"></video>
                        </div>
                    </section>
                @else
                    <section class="p-5 rounded-2xl bg-slate-900/10 border border-dashed border-slate-800/80 text-xs text-slate-400 flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-mobile-screen-button text-slate-500"></i>
                        </div>
                        <div class="space-y-1">
                            <strong class="text-slate-300 block font-bold">Chưa có video tour cho phòng này</strong>
                            <p class="leading-relaxed">Bạn nên yêu cầu chủ trọ gửi video quay cận cảnh (không cắt ghép) từ cửa vào phòng trước khi đặt cọc để kiểm tra chính xác hiện trạng.</p>
                        </div>
                    </section>
                @endif

                <!-- Mô tả chi tiết -->
                <section class="p-6 rounded-2xl bg-slate-900/20 border border-slate-800/80 shadow-[inset_0_1px_0_rgba(255,255,255,0.02)]">
                    <h2 class="text-sm font-extrabold text-slate-100 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-align-left text-emerald-400"></i> Mô tả không gian sống
                    </h2>
                    <p class="text-xs md:text-sm text-slate-350 leading-relaxed whitespace-pre-line">{{ $fullDescription }}</p>
                </section>

                <!-- Đánh giá thực tế từ người dùng -->
                <section class="p-6 rounded-2xl bg-slate-900/20 border border-slate-800/80 shadow-[inset_0_1px_0_rgba(255,255,255,0.02)]">
                    <div class="flex items-center justify-between gap-4 mb-5 pb-3 border-b border-slate-800/50">
                        <h2 class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-comments text-amber-400"></i> Đánh giá khách quan
                        </h2>
                        <button type="button" onclick="loadDetailReviewSummary(this)" data-room-id="{{ $room['id'] }}" class="px-3 py-2 rounded-xl bg-emerald-600/90 hover:bg-emerald-500 text-white text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1.5 shadow-md shadow-emerald-600/10 active:scale-95 transition-all">
                            <i class="fa-solid fa-wand-magic-sparkles text-[11px]"></i>
                            <span>AI Tóm tắt</span>
                        </button>
                    </div>

                    <!-- AI Summary Box container -->
                    <div id="review-summary-box" class="hidden mb-5 rounded-2xl bg-slate-950/60 border border-emerald-500/20 p-5 text-xs text-slate-300 shadow-lg relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl pointer-events-none"></div>
                    </div>

                    <!-- Reviews list -->
                    <div class="space-y-4">
                        @forelse($room['reviews'] as $review)
                            @php
                                $firstLetter = mb_substr($review['author_name'] ?? 'U', 0, 1, 'UTF-8');
                                $bgColors = [
                                    'bg-emerald-500/10 text-emerald-355 border-emerald-500/20',
                                    'bg-cyan-500/10 text-cyan-355 border-cyan-500/20',
                                    'bg-teal-500/10 text-teal-355 border-teal-500/20',
                                    'bg-indigo-500/10 text-indigo-355 border-indigo-500/20'
                                ];
                                $colorIdx = (ord(strtolower($firstLetter)) % count($bgColors));
                                $avatarClass = $bgColors[$colorIdx];
                            @endphp
                            <article class="tilt-element p-4 rounded-xl bg-slate-900/30 border border-slate-800/50 hover:border-slate-800 transition-all relative group shadow-[inset_0_1px_0_rgba(255,255,255,0.01)]">
                                <!-- Background Quote Decors -->
                                <i class="fa-solid fa-quote-right absolute right-4 top-4 text-slate-800/10 text-3xl pointer-events-none group-hover:text-slate-800/20 transition-colors"></i>
                                
                                <div class="flex justify-between items-start gap-3 text-xs relative z-10">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs border {{ $avatarClass }}">
                                            {{ mb_strtoupper($firstLetter, 'UTF-8') }}
                                        </div>
                                        <div>
                                            <strong class="text-slate-200 text-xs font-bold block">{{ $review['author_name'] }}</strong>
                                            <small class="text-[9px] text-slate-500 block mt-0.5">{{ $review['created_at'] }}</small>
                                        </div>
                                    </div>
                                    <span class="text-amber-400 text-[10px] bg-amber-500/5 px-2.5 py-1 rounded-lg border border-amber-500/10 font-bold shrink-0">
                                        {{ str_repeat('★', (int) $review['rating']) }}{{ str_repeat('☆', 5 - (int) $review['rating']) }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 leading-relaxed mt-3 pl-11 relative z-10 select-all">{{ $review['comment'] }}</p>
                            </article>
                        @empty
                            <div class="py-6 text-center text-xs text-slate-500 italic rounded-xl bg-slate-900/10 border border-dashed border-slate-800/60">
                                <i class="fa-solid fa-comments text-slate-650 block text-lg mb-2"></i>
                                Chưa có đánh giá thực tế nào cho phòng này.
                            </div>
                        @endforelse
                    </div>

                    <!-- Gửi đánh giá form (FEAT_25_REVIEW_REPORT) -->
                    <form id="formRoomReview" action="{{ route('renty.room.review.store', $room['id']) }}" method="POST" class="mt-8 pt-6 border-t border-slate-800/60 space-y-4">
                        @csrf
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-pen-to-square text-indigo-400"></i> Chia sẻ trải nghiệm trọ thực tế
                            </h3>
                            @guest
                                <a href="{{ route('login') }}" class="text-[11px] text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-arrow-right-to-bracket text-[10px]"></i> Đăng nhập để đánh giá
                                </a>
                            @endguest
                        </div>

                        <!-- STT 1: Star Rating Bar (star_picker) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                                Chấm điểm sao <span class="text-rose-500">*</span>
                            </label>
                            <div id="star_picker" class="star_picker flex items-center justify-between p-3.5 rounded-xl bg-slate-950 border border-slate-800 transition-all">
                                <input type="hidden" name="rating" id="rating_input" value="">
                                <div class="stars-group flex items-center gap-2 cursor-pointer text-2xl text-slate-600 select-none">
                                    <i class="fa-solid fa-star star-item transition-all hover:scale-110" data-value="1" title="1 sao - Rất kém"></i>
                                    <i class="fa-solid fa-star star-item transition-all hover:scale-110" data-value="2" title="2 sao - Kém"></i>
                                    <i class="fa-solid fa-star star-item transition-all hover:scale-110" data-value="3" title="3 sao - Trung bình"></i>
                                    <i class="fa-solid fa-star star-item transition-all hover:scale-110" data-value="4" title="4 sao - Tốt"></i>
                                    <i class="fa-solid fa-star star-item transition-all hover:scale-110" data-value="5" title="5 sao - Xuất sắc"></i>
                                </div>
                                <span id="star_rating_text" class="text-xs font-semibold text-slate-400">★ ★ ★ ★ ★ (Chưa chọn sao)</span>
                            </div>
                            <p id="err_rating" class="text-xs text-rose-500 mt-1.5 hidden flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                <span class="err-text"></span>
                            </p>
                        </div>

                        <!-- STT 2: Textarea comment -->
                        <div>
                            <label for="comment" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                                Nội dung nhận xét thực tế <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="comment" name="comment" rows="3.5" 
                                placeholder="Phòng sạch sẽ, chủ nhà thân thiện..." 
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none placeholder-slate-600 transition-colors resize-none"></textarea>
                            <p id="err_comment" class="text-xs text-rose-500 mt-1.5 hidden flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                <span class="err-text"></span>
                            </p>
                        </div>

                        <!-- STT 3: Button btnSubmitReview & STT 4: Report Link lnkReport -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                            <button type="submit" id="btnSubmitReview" 
                                class="px-6 py-2.5 rounded-xl text-xs font-extrabold uppercase tracking-wider text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20 active:scale-95 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-paper-plane text-[11px]"></i>
                                <span>Gửi đánh giá</span>
                            </button>

                            <a href="javascript:void(0)" id="lnkReport" onclick="openDetailReportModal()" 
                                class="text-xs font-semibold text-rose-400/90 hover:text-rose-300 transition-colors flex items-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-triangle-exclamation text-[11px]"></i>
                                <span>Báo cáo tin này có sai phạm</span>
                            </a>
                        </div>
                    </form>
                </section>
            </div>

            <!-- Right Sidebar Columns (Sticky cards) -->
            <aside class="lg:sticky lg:top-24 space-y-5">

                <!-- Bento box: Thông số phòng -->
                <section class="tilt-element p-6 rounded-2xl bg-slate-900/30 border border-slate-800/80 backdrop-blur-md relative overflow-hidden shadow-[inset_0_1px_0_rgba(255,255,255,0.03)]">
                    <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-emerald-500/30 to-transparent"></div>
                    
                    <h2 class="text-sm font-extrabold text-slate-100 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-cubes text-emerald-400"></i> Thông số phòng trọ
                    </h2>
                    
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="rounded-xl bg-slate-950/40 border border-slate-800/70 p-3 hover:border-emerald-500/20 transition-colors flex flex-col justify-between min-h-[64px]">
                            <small class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Diện tích</small>
                            <div class="flex items-center justify-between gap-1.5 mt-1.5">
                                <strong class="text-slate-200">{{ $room['area_text'] }}</strong>
                                <i class="fa-solid fa-ruler-combined text-emerald-400/80 text-[10px]"></i>
                            </div>
                        </div>
                        
                        <div class="rounded-xl bg-slate-950/40 border border-slate-800/70 p-3 hover:border-emerald-500/20 transition-colors flex flex-col justify-between min-h-[64px]">
                            <small class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Khu vực</small>
                            <div class="flex items-center justify-between gap-1.5 mt-1.5">
                                <strong class="text-slate-200 truncate max-w-[80px]" title="{{ $room['area_name'] }}">{{ $room['area_name'] }}</strong>
                                <i class="fa-solid fa-map-pin text-emerald-400/80 text-[10px]"></i>
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-950/40 border border-slate-800/70 p-3 hover:border-emerald-500/20 transition-colors flex flex-col justify-between min-h-[64px]">
                            <small class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Gác lửng</small>
                            <div class="flex items-center justify-between gap-1.5 mt-1.5">
                                <strong class="text-slate-200">{{ $room['loft_txt'] }}</strong>
                                <i class="fa-solid fa-stairs text-emerald-400/80 text-[10px]"></i>
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-950/40 border border-slate-800/70 p-3 hover:border-emerald-500/20 transition-colors flex flex-col justify-between min-h-[64px]">
                            <small class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Ban công</small>
                            <div class="flex items-center justify-between gap-1.5 mt-1.5">
                                <strong class="text-slate-200">{{ $room['balcony_txt'] }}</strong>
                                <i class="fa-solid fa-door-open text-emerald-400/80 text-[10px]"></i>
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-950/40 border border-slate-800/70 p-3 hover:border-emerald-500/20 transition-colors flex flex-col justify-between min-h-[64px]">
                            <small class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Thú cưng</small>
                            <div class="flex items-center justify-between gap-1.5 mt-1.5">
                                <strong class="text-slate-200">{{ $room['pets_txt'] }}</strong>
                                <i class="fa-solid fa-paw text-emerald-400/80 text-[10px]"></i>
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-950/40 border border-slate-800/70 p-3 hover:border-emerald-500/20 transition-colors flex flex-col justify-between min-h-[64px]">
                            <small class="block text-slate-500 font-bold uppercase tracking-wider text-[9px]">Tiền cọc</small>
                            <div class="flex items-center justify-between gap-1.5 mt-1.5">
                                <strong class="text-slate-200">1 tháng</strong>
                                <i class="fa-solid fa-wallet text-emerald-400/80 text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Warning box if available -->
                @if($room['price_warning'])
                    <section class="p-4.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-200 shadow-sm relative overflow-hidden">
                        <strong class="block text-amber-300 mb-1 font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation text-amber-400"></i>
                            <span>{{ $room['price_warning']['label'] }}</span>
                        </strong>
                        <p class="leading-relaxed text-[11px] text-amber-305">{{ $room['price_warning']['message'] }}</p>
                    </section>
                @endif

                <!-- Glassmorphic Receipt Box: Chi phí dự kiến -->
                <section class="tilt-element p-6 rounded-2xl bg-slate-900/30 border border-slate-800/80 backdrop-blur-md relative overflow-hidden shadow-[inset_0_1px_0_rgba(255,255,255,0.03)]">
                    <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-cyan-500/30 to-transparent"></div>
                    
                    <h2 class="text-sm font-extrabold text-slate-100 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-file-invoice-dollar text-cyan-400"></i> Chi phí ước tính
                    </h2>
                    
                    <div class="space-y-3.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Tiền phòng</span>
                            <span class="flex-grow border-b border-dashed border-slate-800/80 mx-2 mt-1"></span>
                            <strong class="text-slate-200">{{ number_format($room['price'], 0, ',', '.') }}đ</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Tiền cọc phòng</span>
                            <span class="flex-grow border-b border-dashed border-slate-800/80 mx-2 mt-1"></span>
                            <strong class="text-slate-200">{{ number_format($room['price'], 0, ',', '.') }}đ</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Điện (ước lượng)</span>
                            <span class="flex-grow border-b border-dashed border-slate-800/80 mx-2 mt-1"></span>
                            <strong class="text-slate-200">350.000đ</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Nước (2 người)</span>
                            <span class="flex-grow border-b border-dashed border-slate-800/80 mx-2 mt-1"></span>
                            <strong class="text-slate-200">40.000đ</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Dịch vụ & Gửi xe</span>
                            <span class="flex-grow border-b border-dashed border-slate-800/80 mx-2 mt-1"></span>
                            <strong class="text-slate-200">150.000đ</strong>
                        </div>
                        
                        <!-- Divider receipt -->
                        <div class="border-t border-dashed border-slate-800 pt-4 mt-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-slate-300">Tổng ban đầu</span>
                                <strong class="text-lg font-black text-emerald-400 drop-shadow-[0_0_12px_rgba(52,211,153,0.15)]">{{ number_format($initialCost, 0, ',', '.') }}đ</strong>
                            </div>
                            <span class="block text-[9px] text-slate-500 mt-2 leading-relaxed text-right">
                                * Ước lượng 1 tháng thuê đầu tiên kèm cọc & các dịch vụ cơ bản.
                            </span>
                        </div>
                    </div>
                </section>

                <!-- Landlord Card: Thông tin chủ nhà (Spec UI STT 3 - landlord_info) -->
                <section id="landlord_info" name="landlord_info" data-field-name="landlord_info" class="tilt-element p-5 rounded-2xl bg-slate-900/40 border border-slate-800/80 backdrop-blur-md relative overflow-hidden shadow-sm">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 font-extrabold text-lg shrink-0 shadow-inner overflow-hidden">
                            @if(!empty($room['landlord_avatar']))
                                <img src="{{ $room['landlord_avatar'] }}" alt="{{ $room['landlord_name'] }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-user-tie"></i>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-xs font-extrabold text-slate-100 truncate" id="landlord-name">{{ $room['landlord_name'] ?? 'Chủ nhà trọ Renty' }}</h3>
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-sky-500/10 text-sky-300 border border-sky-500/25 shrink-0 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-[8px]"></i> KYC
                                </span>
                            </div>
                            @if(!empty($room['tenant_name']) && $room['tenant_name'] !== $room['landlord_name'])
                                <p class="text-[10px] text-slate-400 truncate mt-0.5 flex items-center gap-1">
                                    <i class="fa-solid fa-building text-[9px] text-indigo-400"></i>
                                    <span>{{ $room['tenant_name'] }}</span>
                                </p>
                            @endif
                            <p class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-phone text-[10px] text-emerald-400"></i>
                                <span id="landlord-phone">{{ $room['landlord_phone'] ?? '0396519196' }}</span>
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Liên hệ & Hẹn lịch xem trọ (Spec UI STT 4 - Action Buttons: btnBooking) -->
                <section class="booking-sidebar-card tilt-element p-6 rounded-2xl bg-slate-900/30 border border-slate-800/80 backdrop-blur-md relative overflow-hidden shadow-[inset_0_1px_0_rgba(255,255,255,0.03)]">
                    <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-teal-500/30 to-transparent"></div>
                    
                    @php
                        $currentUser = Auth::user();
                        $isSuperAdmin = $currentUser && $currentUser->isAdmin();
                        $isRoomOwner = $currentUser && $currentUser->canAccessLandlordDashboard() && $currentUser->tenant_id && $currentUser->tenant_id == ($room['tenant_id'] ?? null);
                        $defaultName = old('name', old('customer_name', (!$isRoomOwner && !$isSuperAdmin && $currentUser && !$currentUser->canAccessLandlordDashboard()) ? $currentUser->name : ''));
                        $defaultPhone = old('phone', old('customer_phone', (!$isRoomOwner && !$isSuperAdmin && $currentUser && !$currentUser->canAccessLandlordDashboard()) ? $currentUser->phone : ''));
                    @endphp

                    @if($isSuperAdmin)
                        <!-- DÀNH RIÊNG CHO SUPERADMIN: Khung Kiểm Duyệt Quản Trị Viên Sàn -->
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="admin-auditor-title text-sm font-extrabold text-slate-100 flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved text-amber-400"></i> Quản trị viên sàn Renty
                            </h2>
                            <span class="admin-auditor-pill text-[10px] text-amber-400 font-extrabold px-2 py-0.5 rounded-md bg-amber-500/10 border border-amber-500/25">Superadmin</span>
                        </div>

                        <div class="admin-auditor-card p-5 rounded-2xl bg-gradient-to-br from-amber-950/30 via-slate-900/60 to-purple-950/30 border border-amber-500/30 text-xs text-slate-300 space-y-3.5 animate-fade-in shadow-2xl">
                            <div class="flex items-center justify-between">
                                <span class="admin-auditor-badge inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-500/15 text-amber-300 font-extrabold uppercase text-[10px] tracking-wider border border-amber-500/30">
                                    <i class="fa-solid fa-user-shield text-[10px] text-amber-400"></i> Chế độ kiểm duyệt
                                </span>
                                <span class="admin-auditor-sub text-[10px] text-slate-400 font-medium">Toàn sàn</span>
                            </div>

                            <p class="admin-auditor-text text-slate-300 text-xs leading-relaxed">
                                Bạn đang xem tin đăng với tư cách <span class="admin-auditor-name text-amber-300 font-bold">{{ $currentUser->name }}</span> (Quản trị viên tối cao).
                            </p>

                            <!-- Đối soát thông tin phòng & chủ trọ -->
                            <div class="admin-auditor-box p-3.5 rounded-xl bg-slate-950/70 border border-slate-800 space-y-2 text-[11px]">
                                <div class="flex justify-between items-center text-slate-400">
                                    <span>Cơ sở sở hữu:</span>
                                    <span class="text-slate-200 font-bold truncate max-w-[150px]">{{ $room['tenant_name'] ?? 'Hệ thống SmartRoom' }}</span>
                                </div>
                                <div class="flex justify-between items-center text-slate-400">
                                    <span>Mã cơ sở (Tenant ID):</span>
                                    <span class="text-amber-400 font-mono font-bold">#{{ $room['tenant_id'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between items-center text-slate-400">
                                    <span>Chủ trọ phụ trách:</span>
                                    <span class="text-slate-200 font-semibold">{{ $room['landlord_name'] ?? 'Chủ nhà trọ' }}</span>
                                </div>
                                <div class="flex justify-between items-center text-slate-400">
                                    <span>Hotline liên hệ:</span>
                                    <span class="text-emerald-400 font-mono font-semibold">{{ $room['landlord_phone'] ?? '0396519196' }}</span>
                                </div>
                            </div>

                            <!-- Nút chức năng dành cho Superadmin -->
                            <div class="pt-2 border-t border-amber-500/20 flex flex-col gap-2">
                                <a href="{{ route('user.list') }}" class="w-full py-2.5 px-3 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-center flex items-center justify-center gap-2 transition-all shadow-lg shadow-amber-600/20 active:scale-95 text-xs">
                                    <i class="fa-solid fa-users-gear"></i> Quản lý tài khoản sàn
                                </a>
                                <a href="{{ route('admin.verifications.index') }}" class="admin-auditor-secondary-btn w-full py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 font-semibold text-center flex items-center justify-center gap-2 transition-all border border-slate-700/80 active:scale-95 text-xs">
                                    <i class="fa-solid fa-id-card-clip text-amber-400"></i> Xét duyệt KYC chủ trọ
                                </a>
                                <button type="button" onclick="document.getElementById('superadmin-guest-preview')?.classList.toggle('hidden');" class="admin-auditor-toggle-btn w-full py-2 px-3 rounded-xl bg-slate-800/60 hover:bg-slate-800 text-slate-400 hover:text-slate-200 text-center flex items-center justify-center gap-1.5 transition-all border border-slate-800 text-[11px] cursor-pointer">
                                    <i class="fa-solid fa-eye text-[10px]"></i> Bật / Tắt mô phỏng Khách thuê
                                </button>
                            </div>
                        </div>

                        <!-- Khối xem thử dành cho Superadmin khi bấm toggle -->
                        <div id="superadmin-guest-preview" class="hidden mt-4 p-3.5 rounded-2xl bg-slate-950/50 border border-slate-800 space-y-3">
                            <p class="text-[10px] text-amber-400/80 italic text-center">Giao diện tương tác mà Khách thuê nhìn thấy:</p>
                            <div class="flex gap-2 mb-2">
                                <span class="flex-1 py-2 rounded-xl bg-emerald-600/40 text-emerald-300 text-xs font-bold text-center flex items-center justify-center gap-1 border border-emerald-500/30">
                                    <i class="fa-solid fa-phone"></i> Gọi điện
                                </span>
                                <span class="flex-1 py-2 rounded-xl bg-blue-600/40 text-blue-300 text-xs font-bold text-center flex items-center justify-center gap-1 border border-blue-500/30">
                                    <i class="fa-solid fa-comments"></i> Nhắn Zalo
                                </span>
                            </div>
                        </div>
                    @elseif($isRoomOwner)
                        <!-- Dành riêng cho Chủ trọ sở hữu phòng này -->
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="landlord-owner-title text-sm font-extrabold text-slate-100 flex items-center gap-2">
                                <i class="fa-solid fa-crown text-amber-400"></i> Quản lý phòng của bạn
                            </h2>
                            <span class="landlord-owner-pill text-[10px] text-indigo-400 font-semibold px-2 py-0.5 rounded-md bg-indigo-500/10 border border-indigo-500/20">Chủ sở hữu</span>
                        </div>

                        <div class="landlord-owner-card p-5 rounded-2xl bg-indigo-950/60 border border-indigo-500/30 text-xs text-slate-300 space-y-3.5 animate-fade-in shadow-xl">
                            <div class="flex items-center justify-between">
                                <span class="landlord-owner-badge inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-300 font-extrabold uppercase text-[10px] tracking-wider border border-indigo-500/30">
                                    <i class="fa-solid fa-user-check text-[10px] text-amber-400"></i> Bạn là chủ phòng này
                                </span>
                                <span class="landlord-owner-sub text-[10px] text-slate-400 font-medium">Bảng điều khiển</span>
                            </div>
                            <p class="landlord-owner-text text-slate-300 text-xs leading-relaxed">
                                Bạn đang xem tin đăng phòng của chính mình với tư cách <span class="landlord-owner-name text-indigo-200 font-bold">{{ $currentUser->name }}</span>. Khách tìm phòng trên Renty sẽ liên hệ qua form này khi họ truy cập.
                            </p>
                            <div class="landlord-owner-divider pt-2 border-t border-indigo-500/20 flex flex-col gap-2.5">
                                <a href="{{ route('admin.rooms.edit', $room['id']) }}" class="landlord-owner-primary-btn w-full py-3 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-center flex items-center justify-center gap-2 transition-all shadow-lg shadow-indigo-600/30 active:scale-95 text-xs">
                                    <i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa phòng này
                                </a>
                                <a href="{{ route('smartroom.admin', ['tab' => 'contact-section']) }}" class="landlord-owner-secondary-btn w-full py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 font-semibold text-center flex items-center justify-center gap-2 transition-all border border-slate-700/80 active:scale-95 text-xs">
                                    <i class="fa-solid fa-clipboard-list text-indigo-400"></i> Xem danh sách khách đặt lịch
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Dành cho Khách thuê / Người xem bên ngoài -->
                        <h2 class="text-sm font-extrabold text-slate-100 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-phone-volume text-teal-400"></i> Hẹn xem phòng / Liên hệ
                        </h2>
                        
                        @if(($room['status'] ?? 'empty') !== 'empty')
                            <!-- Thông báo phòng đã có người thuê (ERR_06_02) -->
                            <div class="mb-4 p-3.5 rounded-xl bg-slate-800/80 border border-slate-700 text-xs text-slate-300 space-y-1">
                                <div class="flex items-center gap-2 text-rose-400 font-extrabold">
                                    <i class="fa-solid fa-lock"></i>
                                    <span>Phòng đã có người thuê</span>
                                </div>
                                <p class="text-[11px] text-slate-400 leading-relaxed">
                                    Phòng lưu trú này hiện đã có người thuê, tạm ngừng nhận đăng ký.
                                </p>
                            </div>
                        @endif

                        <div class="flex gap-2.5 mb-4">
                            <a href="{{ (($room['status'] ?? 'empty') === 'empty') ? 'tel:' . ($room['landlord_phone'] ?? '0396519196') : 'javascript:void(0)' }}" 
                               class="flex-1 py-2.5 rounded-xl {{ (($room['status'] ?? 'empty') === 'empty') ? 'bg-emerald-600 hover:bg-emerald-500 hover:shadow-lg hover:shadow-emerald-600/15' : 'bg-slate-800 opacity-50 cursor-not-allowed' }} text-white text-xs font-bold text-center flex items-center justify-center gap-1.5 transition-all active:scale-[0.98]">
                                <i class="fa-solid fa-phone"></i>
                                <span>Gọi điện</span>
                            </a>
                            <a href="{{ (($room['status'] ?? 'empty') === 'empty') ? 'https://zalo.me/' . ($room['landlord_phone'] ?? '0396519196') : 'javascript:void(0)' }}" 
                               target="{{ (($room['status'] ?? 'empty') === 'empty') ? '_blank' : '_self' }}" 
                               class="flex-1 py-2.5 rounded-xl {{ (($room['status'] ?? 'empty') === 'empty') ? 'bg-blue-600 hover:bg-blue-500 hover:shadow-lg hover:shadow-blue-600/15' : 'bg-slate-800 opacity-50 cursor-not-allowed' }} text-white text-xs font-bold text-center flex items-center justify-center gap-1.5 transition-all active:scale-[0.98]">
                                <i class="fa-solid fa-comments"></i>
                                <span>Nhắn Zalo</span>
                            </a>
                        </div>
                        <form action="{{ route('renty.contact_request.store') }}" method="POST" id="formContactRequest" novalidate class="space-y-3">
                            @csrf
                            <input type="hidden" name="room_id" value="{{ $room['id'] }}">
                            <div>
                                <label for="customer_name" class="block text-[11px] font-bold text-slate-300 mb-1">
                                    Họ và tên của bạn <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" id="customer_name" value="{{ $defaultName }}" placeholder="Nguyễn Văn B" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border {{ $errors->has('name') || $errors->has('customer_name') ? 'border-rose-500 ring-2 ring-rose-500/20 bg-rose-950/20' : 'border-slate-800' }} text-slate-200 text-xs focus:border-emerald-500 focus:outline-none placeholder-slate-655 transition-colors" {{ (($room['status'] ?? 'empty') !== 'empty') ? 'disabled' : '' }}>
                                <p id="err_customer_name" class="text-[11px] text-rose-400 mt-1.5 flex items-center gap-1.5 font-medium {{ $errors->has('name') || $errors->has('customer_name') ? '' : 'hidden' }}">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                    <span>{{ $errors->first('name') ?: $errors->first('customer_name') ?: 'Vui lòng nhập họ và tên để chủ trọ tiện xưng hô liên hệ.' }}</span>
                                </p>
                            </div>
                            <div>
                                <label for="customer_phone" class="block text-[11px] font-bold text-slate-300 mb-1">
                                    Số điện thoại liên hệ <span class="text-rose-500">*</span>
                                </label>
                                <input type="tel" name="phone" id="customer_phone" value="{{ $defaultPhone }}" placeholder="0987654321" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border {{ $errors->has('phone') || $errors->has('customer_phone') ? 'border-rose-500 ring-2 ring-rose-500/20 bg-rose-950/20' : 'border-slate-800' }} text-slate-200 text-xs focus:border-emerald-500 focus:outline-none placeholder-slate-655 transition-colors" {{ (($room['status'] ?? 'empty') !== 'empty') ? 'disabled' : '' }}>
                                <p id="err_customer_phone" class="text-[11px] text-rose-400 mt-1.5 flex items-center gap-1.5 font-medium {{ $errors->has('phone') || $errors->has('customer_phone') ? '' : 'hidden' }}">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                    <span>{{ $errors->first('phone') ?: $errors->first('customer_phone') ?: 'Vui lòng nhập số điện thoại để nhận cuộc gọi tư vấn.' }}</span>
                                </p>
                            </div>
                            <div>
                                <label for="appointment_time" class="block text-[11px] font-bold text-slate-300 mb-1">
                                    Ngày & Giờ xem phòng <span class="text-rose-500">*</span>
                                </label>
                                <input type="datetime-local" 
                                       name="appointment_time" 
                                       id="appointment_time" 
                                       value="{{ old('appointment_time') }}" 
                                       min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                                       onclick="this.showPicker ? this.showPicker() : null"
                                       style="color-scheme: dark;" 
                                       placeholder="Chọn ngày giờ..."
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border {{ $errors->has('appointment_time') ? 'border-rose-500 ring-2 ring-rose-500/20 bg-rose-950/20' : 'border-slate-800' }} text-slate-200 text-xs focus:border-emerald-500 focus:outline-none transition-colors cursor-pointer" 
                                       {{ (($room['status'] ?? 'empty') !== 'empty') ? 'disabled' : '' }}>
                                <p id="err_appointment_time" class="text-[11px] text-rose-400 mt-1.5 flex items-center gap-1.5 font-medium {{ $errors->has('appointment_time') ? '' : 'hidden' }}">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                    <span>{{ $errors->first('appointment_time') ?: 'Vui lòng chọn ngày và giờ bạn muốn đến xem phòng.' }}</span>
                                </p>
                            </div>
                            <div>
                                <label for="note" class="block text-[11px] font-bold text-slate-300 mb-1">
                                    Lời nhắn / Ghi chú <span class="text-slate-500 font-normal text-[10px]">(Tối đa 255 ký tự)</span>
                                </label>
                                <textarea name="message" id="note" rows="2.5" maxlength="255" placeholder="Ghi chú thời gian muốn hẹn xem phòng..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none placeholder-slate-655 transition-colors resize-none" {{ (($room['status'] ?? 'empty') !== 'empty') ? 'disabled' : '' }}>{{ old('message', old('note')) }}</textarea>
                            </div>
                            <button type="submit" id="btnSubmitRequest" name="btnBooking" data-field-name="btnBooking" 
                                class="w-full py-3 rounded-xl {{ (($room['status'] ?? 'empty') === 'empty') ? 'bg-blue-600 hover:bg-blue-500 cursor-pointer shadow-lg shadow-blue-600/25 hover:shadow-blue-600/35' : 'bg-slate-800 text-slate-500 cursor-not-allowed border border-slate-700' }} text-white text-xs font-extrabold uppercase tracking-wider transition-all active:scale-[0.98] mt-1.5"
                                {{ (($room['status'] ?? 'empty') !== 'empty') ? 'disabled' : '' }}>
                                {{ (($room['status'] ?? 'empty') === 'empty') ? 'Gửi yêu cầu hẹn xem' : 'Phòng Đã Thuê (Ngừng Nhận Đặt)' }}
                            </button>
                        </form>
                    @endif
                </section>
            </aside>
        </section>
    </main>

    <!-- Overlay Zoom Modal (Full screen image browser) -->
    <div id="zoom-modal" class="fixed inset-0 z-50 hidden bg-[#02040a]/95 backdrop-blur-md p-4 items-center justify-center">
        <button type="button" onclick="closeZoom()" class="absolute top-5 right-5 w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 hover:text-white flex items-center justify-center transition-all shadow-lg active:scale-95"><i class="fa-solid fa-xmark"></i></button>
        <button type="button" onclick="changeZoom(-1)" class="absolute left-4 md:left-8 w-12 h-12 rounded-full bg-slate-900/80 border border-slate-800 text-slate-200 hover:text-white flex items-center justify-center transition-all shadow-md active:scale-90"><i class="fa-solid fa-chevron-left"></i></button>
        <img id="zoom-image" src="" alt="Ảnh phòng phóng to" class="max-w-full max-h-[82vh] object-contain rounded-2xl border border-slate-800 shadow-2xl">
        <button type="button" onclick="changeZoom(1)" class="absolute right-4 md:right-8 w-12 h-12 rounded-full bg-slate-900/80 border border-slate-800 text-slate-200 hover:text-white flex items-center justify-center transition-all shadow-md active:scale-90"><i class="fa-solid fa-chevron-right"></i></button>
        <div class="absolute left-1/2 -translate-x-1/2 bottom-5 px-4.5 py-2.5 rounded-xl bg-slate-950/85 border border-white/10 text-xs font-bold text-slate-200 flex items-center gap-2 shadow-lg backdrop-blur">
            <span id="zoom-label">{{ $images[0]['label'] ?? 'View toàn phòng' }}</span>
            <span class="text-slate-650 font-normal">|</span>
            <span id="zoom-count" class="text-emerald-400 tabular-nums">1/{{ count($images) }}</span>
        </div>
    </div>

    <!-- Overlay Report Modal -->
    <div id="room-report-modal" onclick="if(event.target === this) closeReportModal()" class="fixed inset-0 z-[60] hidden bg-[#02040a]/80 backdrop-blur-md p-4 items-center justify-center">
        <div class="w-full max-w-lg rounded-3xl bg-[#0a0f1d] border border-slate-800 p-5 md:p-6 shadow-2xl animate-fade-in relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-rose-500/30 to-transparent"></div>
            
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-350 text-[10px] font-extrabold uppercase tracking-widest shadow-sm">
                        <i class="fa-solid fa-shield-halved text-[9px]"></i> Báo cáo phòng
                    </span>
                    <h2 class="mt-3 text-xl font-extrabold text-slate-100">Báo cáo tin đăng đáng ngờ</h2>
                    <p id="report-room-title" class="mt-1 text-xs text-slate-500 truncate max-w-[320px]">{{ $room['title'] }}</p>
                </div>
                <button type="button" onclick="closeReportModal()" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 text-slate-355 hover:text-white flex items-center justify-center transition-all active:scale-95 shadow-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formRoomReport" method="POST" action="{{ route('renty.room.report.store', $room['id']) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="text" name="reporter_name" value="{{ Auth::user()->name ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none placeholder-slate-600 transition-colors" placeholder="Họ tên của bạn">
                    <input type="tel" name="reporter_phone" value="{{ Auth::user()->phone ?? '' }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none placeholder-slate-600 transition-colors" placeholder="Số điện thoại của bạn">
                </div>
                
                <!-- Lý do vi phạm (report_reason) -->
                <div>
                    <label for="report_reason" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Lý do báo cáo sai phạm <span class="text-rose-500">*</span>
                    </label>
                    <select id="report_reason" name="report_reason" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none transition-colors">
                        <option value="">-- Chọn lý do vi phạm --</option>
                        <option value="Lừa cọc / yêu cầu cọc bất thường">Lừa cọc / yêu cầu cọc bất thường</option>
                        <option value="Ảnh sai thực tế phòng">Ảnh sai thực tế phòng</option>
                        <option value="Giá ảo / phụ phí phát sinh sai lệch">Giá ảo / phụ phí phát sinh sai lệch</option>
                        <option value="Vấn đề an toàn / an ninh khu trọ">Vấn đề an toàn / an ninh khu trọ</option>
                        <option value="Tin spam / đã hết phòng từ lâu">Tin spam / đã hết phòng từ lâu</option>
                        <option value="Lý do khác">Lý do khác</option>
                    </select>
                    <p id="err_report_reason" class="text-xs text-rose-500 mt-1.5 hidden flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                        <span class="err-text"></span>
                    </p>
                </div>

                <div>
                    <label for="report_description" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Mô tả chi tiết vi phạm
                    </label>
                    <textarea id="report_description" name="description" rows="3.5" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none placeholder-slate-600 transition-colors resize-none" placeholder="Vui lòng mô tả chi tiết điều bạn thấy bất thường để quản trị viên xác minh..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeReportModal()" class="px-4.5 py-2.5 rounded-xl border border-slate-800 bg-slate-900 hover:bg-slate-850 hover:text-slate-100 text-slate-355 text-xs font-bold transition-all active:scale-95">Hủy</button>
                    <button type="submit" id="btnSubmitReport" class="px-5.5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold uppercase tracking-wider transition-all active:scale-95 shadow-lg shadow-rose-600/10 hover:shadow-rose-600/20">
                        Gửi báo cáo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const roomImages = @json($images);
        let activeImageIndex = 0;

        function applyThemeMode(mode) {
            const isLight = mode === 'light';
            document.documentElement.classList.remove('theme-fire', 'theme-ice');
            document.body?.classList.remove('theme-fire', 'theme-ice');
            document.documentElement.classList.toggle('theme-light', isLight);
            document.body.classList.toggle('theme-light', isLight);
            document.querySelectorAll('#theme-toggle-icon').forEach(icon => {
                icon.classList.toggle('fa-sun', isLight);
                icon.classList.toggle('fa-moon', !isLight);
            });
        }

        function toggleThemeMode() {
            const nextMode = document.body.classList.contains('theme-light') ? 'dark' : 'light';
            localStorage.setItem('renty_theme_mode', nextMode);
            localStorage.setItem('smartroom_theme', nextMode);
            applyThemeMode(nextMode);
        }

        let savedTheme = localStorage.getItem('renty_theme_mode') || localStorage.getItem('smartroom_theme') || 'dark';
        if (savedTheme === 'fire' || savedTheme === 'ice') savedTheme = 'dark';
        applyThemeMode(savedTheme);

        function selectImage(index) {
            activeImageIndex = Math.max(0, Math.min(roomImages.length - 1, index));
            const image = roomImages[activeImageIndex];
            const mainImg = document.getElementById('main-image');
            if (mainImg) {
                mainImg.src = image.url;
                mainImg.alt = image.label;
            }
            const labelEl = document.getElementById('main-image-label');
            if (labelEl) {
                labelEl.textContent = image.label;
            }
            document.querySelectorAll('.thumb-button').forEach((button, buttonIndex) => {
                button.classList.toggle('border-emerald-400', buttonIndex === activeImageIndex);
                button.classList.toggle('border-slate-800', buttonIndex !== activeImageIndex);
            });
        }

        function openZoom() {
            document.getElementById('zoom-modal').classList.remove('hidden');
            document.getElementById('zoom-modal').classList.add('flex');
            renderZoom();
        }

        function openZoomWithIndex(index) {
            activeImageIndex = Math.max(0, Math.min(roomImages.length - 1, index));
            selectImage(activeImageIndex);
            openZoom();
        }

        function renderZoom() {
            const image = roomImages[activeImageIndex];
            document.getElementById('zoom-image').src = image.url;
            document.getElementById('zoom-label').textContent = image.label;
            document.getElementById('zoom-count').textContent = `${activeImageIndex + 1}/${roomImages.length}`;
        }

        function changeZoom(delta) {
            activeImageIndex = (activeImageIndex + delta + roomImages.length) % roomImages.length;
            selectImage(activeImageIndex);
            renderZoom();
        }

        function closeZoom() {
            document.getElementById('zoom-modal').classList.add('hidden');
            document.getElementById('zoom-modal').classList.remove('flex');
        }

        function openDetailReportModal(roomId, title) {
            const modal = document.getElementById('room-report-modal');
            if (!modal) return;
            const targetRoomId = roomId || window.currentDetailRoomId || '{{ $room['id'] }}';
            const form = document.getElementById('formRoomReport');
            if (form) {
                form.action = `/renty/room/${targetRoomId}/report`;
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeReportModal() {
            const modal = document.getElementById('room-report-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            document.body.classList.remove('overflow-hidden');
        }

        window.openDetailReportModal = openDetailReportModal;
        window.openReportModal = openDetailReportModal;
        window.closeReportModal = closeReportModal;

        window.currentDetailRoomId = {{ (int) $room['id'] }};

        function loadDetailReviewSummary(button) {
            const box = document.getElementById('review-summary-box');
            if (!box) return;
            const original = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1"></i> Đang tóm tắt...';
            box.classList.remove('hidden');
            box.innerHTML = `
                <div class="flex items-center gap-2 text-slate-400">
                    <i class="fa-solid fa-circle-notch animate-spin text-emerald-400 text-xs"></i>
                    <span>AI đang phân tích các đánh giá thực tế...</span>
                </div>
            `;

            const roomId = button?.dataset?.roomId || window.currentDetailRoomId || '{{ $room['id'] }}';

            fetch(`/api/renty/rooms/${roomId}/reviews/summary`, {
                headers: { 'Accept': 'application/json' }
            })
                .then(response => response.json())
                .then(data => {
                    button.disabled = false;
                    button.innerHTML = original;
                    if (!data || !data.success) {
                        box.innerHTML = '<span class="text-rose-450"><i class="fa-solid fa-circle-exclamation mr-1"></i> ' + escapeHtml(data?.message || 'Không thể tóm tắt lúc này.') + '</span>';
                        return;
                    }
                    const summary = data.summary || {};
                    const prosList = summary.pros || [];
                    const consList = summary.cons || [];
                    const pros = prosList.map(item => `
                        <li class="flex items-start gap-2 text-slate-300">
                            <i class="fa-solid fa-circle-check text-emerald-400 mt-1 shrink-0 text-[10px]"></i>
                            <span>${escapeHtml(item)}</span>
                        </li>
                    `).join('');
                    const cons = consList.map(item => `
                        <li class="flex items-start gap-2 text-slate-300">
                            <i class="fa-solid fa-circle-exclamation text-amber-400 mt-1 shrink-0 text-[10px]"></i>
                            <span>${escapeHtml(item)}</span>
                        </li>
                    `).join('');
                    
                    box.innerHTML = `
                        <div class="space-y-4">
                            <div class="font-bold text-slate-200 text-xs border-b border-slate-800 pb-2 flex items-center gap-2">
                                <i class="fa-solid fa-wand-magic-sparkles text-emerald-400"></i>
                                <span>AI PHÂN TÍCH REVIEW</span>
                            </div>
                            <p class="text-slate-350 leading-relaxed text-xs">${escapeHtml(summary.summary || 'Chưa có đủ nội dung đánh giá để tổng hợp.')}</p>
                            
                            ${(pros || cons) ? `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-900/60">
                                ${pros ? `
                                    <div>
                                        <div class="text-emerald-400 font-extrabold text-[10px] uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <i class="fa-solid fa-thumbs-up"></i> Ưu điểm
                                        </div>
                                        <ul class="space-y-2 text-[11px]">${pros}</ul>
                                    </div>
                                ` : ''}
                                ${cons ? `
                                    <div>
                                        <div class="text-amber-400 font-extrabold text-[10px] uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Cần lưu ý
                                        </div>
                                        <ul class="space-y-2 text-[11px]">${cons}</ul>
                                    </div>
                                ` : ''}
                            </div>
                            ` : ''}
                        </div>
                    `;
                })
                .catch(() => {
                    button.disabled = false;
                    button.innerHTML = original;
                    box.innerHTML = '<span class="text-rose-450"><i class="fa-solid fa-circle-exclamation mr-1"></i> Lỗi kết nối máy chủ AI. Vui lòng thử lại.</span>';
                });
        }

        window.loadDetailReviewSummary = loadDetailReviewSummary;
        window.loadReviewSummary = loadDetailReviewSummary;

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        (function saveViewedRoom() {
            try {
                const roomId = String({{ $room['id'] }});
                const viewedIds = JSON.parse(localStorage.getItem('renty_viewed_rooms') || '[]').filter(id => id !== roomId);
                viewedIds.unshift(roomId);
                localStorage.setItem('renty_viewed_rooms', JSON.stringify(viewedIds.slice(0, 6)));
            } catch (error) {
                // Local storage is optional
            }
        })();

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeZoom();
                closeReportModal();
            }
            if (!document.getElementById('zoom-modal').classList.contains('hidden')) {
                if (event.key === 'ArrowLeft') changeZoom(-1);
                if (event.key === 'ArrowRight') changeZoom(1);
            }
        });

        // Initialize 3D Card Tilt effects on detail page cards
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.tilt-element').forEach(el => {
                // Prepend glare sheen dynamically
                if (!el.querySelector('.card-3d-glare')) {
                    const glare = document.createElement('div');
                    glare.className = 'card-3d-glare';
                    el.appendChild(glare);
                }
                
                el.addEventListener('mousemove', (e) => {
                    const rect = el.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const xc = rect.width / 2;
                    const yc = rect.height / 2;
                    const dx = x - xc;
                    const dy = y - yc;

                    const rotateX = -(dy / yc) * 6;
                    const rotateY = (dx / xc) * 6;

                    el.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.015, 1.015, 1.015)`;
                    el.style.setProperty('--glare-x', `${(x / rect.width) * 100}%`);
                    el.style.setProperty('--glare-y', `${(y / rect.height) * 100}%`);
                });

                el.addEventListener('mouseleave', () => {
                    el.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
                });
            });
        });

        // ==========================================
        // FEAT_25_REVIEW_REPORT: XỬ LÝ ĐÁNH GIÁ & BÁO CÁO VI PHẠM
        // ==========================================
        const starRatingLabels = {
            1: '1 sao - Rất kém',
            2: '2 sao - Kém',
            3: '3 sao - Trung bình',
            4: '4 sao - Tốt',
            5: '5 sao - Xuất sắc'
        };

        // Khởi tạo tương tác Star Picker
        const starItems = document.querySelectorAll('#star_picker .star-item');
        const ratingInput = document.getElementById('rating_input');
        const starRatingText = document.getElementById('star_rating_text');
        const starPickerBox = document.getElementById('star_picker');

        function updateStarUI(rating) {
            starItems.forEach(star => {
                const val = parseInt(star.getAttribute('data-value'), 10);
                if (val <= rating) {
                    star.classList.remove('text-slate-600');
                    star.classList.add('text-amber-400');
                } else {
                    star.classList.remove('text-amber-400');
                    star.classList.add('text-slate-600');
                }
            });
            if (rating > 0) {
                starRatingText.innerText = starRatingLabels[rating] || (rating + ' sao');
                starRatingText.classList.remove('text-slate-400');
                starRatingText.classList.add('text-amber-400');
            } else {
                starRatingText.innerText = '★ ★ ★ ★ ★ (Chưa chọn sao)';
                starRatingText.classList.remove('text-amber-400');
                starRatingText.classList.add('text-slate-400');
            }
        }

        if (starItems.length > 0) {
            starItems.forEach(star => {
                star.addEventListener('mouseenter', () => {
                    const hoverVal = parseInt(star.getAttribute('data-value'), 10);
                    updateStarUI(hoverVal);
                });
                star.addEventListener('click', () => {
                    const clickVal = parseInt(star.getAttribute('data-value'), 10);
                    ratingInput.value = clickVal;
                    updateStarUI(clickVal);
                    // Xóa lỗi nếu có
                    starPickerBox?.classList.remove('star-warning-animate');
                    document.getElementById('err_rating')?.classList.add('hidden');
                });
            });

            document.querySelector('#star_picker .stars-group')?.addEventListener('mouseleave', () => {
                const currentRating = parseInt(ratingInput.value, 10) || 0;
                updateStarUI(currentRating);
            });
        }

        // Toast thông báo (Chỉ hiển thị tên lỗi/thông báo theo user rule)
        function showToast(message, isSuccess = true, code = '') {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const toast = document.createElement('div');
            toast.className = `p-4 rounded-2xl border text-sm font-semibold flex items-center justify-between gap-4 shadow-2xl transition-all duration-300 pointer-events-auto animate-fade-in ${
                isSuccess 
                ? 'bg-emerald-950/95 border-emerald-500 text-emerald-300 shadow-emerald-900/30' 
                : 'bg-rose-950/95 border-rose-500 text-rose-300 shadow-rose-900/30'
            }`;
            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fa-solid ${isSuccess ? 'fa-circle-check text-emerald-400' : 'fa-circle-exclamation text-rose-400'} text-lg"></i>
                    <span>${message}</span>
                </div>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Bấm sao chép liên kết phòng (ERR_06_04)
        function copyRoomLink() {
            const currentUrl = window.location.href;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(currentUrl).then(() => {
                    showToast('Đã sao chép liên kết chia sẻ phòng vào bộ nhớ tạm!', true);
                }).catch(() => {
                    fallbackCopyUrl(currentUrl);
                });
            } else {
                fallbackCopyUrl(currentUrl);
            }
        }

        function fallbackCopyUrl(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                showToast('Đã sao chép liên kết chia sẻ phòng vào bộ nhớ tạm!', true);
            } catch (err) {
                showToast('Không thể sao chép liên kết. Bạn có thể sao chép trực tiếp từ thanh địa chỉ.', false);
            }
            document.body.removeChild(textArea);
        }

        // Lỗi tải ảnh trong thư viện Gallery -> Tự động nạp ảnh thế mạng (Placeholder Image) mặc định (ERR_06_03)
        let hasShownImageErrorToast = false;
        function handleImageError(img) {
            if (!img) return;
            img.onerror = null;
            img.src = 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';
            if (!hasShownImageErrorToast) {
                hasShownImageErrorToast = true;
                showToast('Không thể tải một số ảnh độ phân giải cao của phòng. Đã sử dụng ảnh thay thế.', false);
            }
        }

        // Xử lý gửi Form Đánh giá phòng (Review)
        const formRoomReview = document.getElementById('formRoomReview');
        if (formRoomReview) {
            formRoomReview.addEventListener('submit', async function(e) {
                e.preventDefault();

                const ratingVal = ratingInput.value;
                const commentEl = document.getElementById('comment');
                const commentVal = commentEl.value.trim();

                let hasError = false;

                // 1. Kiểm tra chưa chọn số sao (ERR_25_01)
                if (!ratingVal || parseInt(ratingVal, 10) < 1 || parseInt(ratingVal, 10) > 5) {
                    starPickerBox?.classList.remove('star-warning-animate');
                    void starPickerBox?.offsetWidth; // trigger reflow
                    starPickerBox?.classList.add('star-warning-animate');
                    
                    const errRating = document.getElementById('err_rating');
                    if (errRating) {
                        errRating.querySelector('.err-text').innerText = 'Vui lòng chọn số sao xếp hạng (từ 1 đến 5 sao).';
                        errRating.classList.remove('hidden');
                    }
                    hasError = true;
                } else {
                    document.getElementById('err_rating')?.classList.add('hidden');
                    starPickerBox?.classList.remove('star-warning-animate');
                }

                // 2. Kiểm tra nhận xét quá ngắn (ERR_25_02: < 10 ký tự)
                if (!commentVal || commentVal.length < 10) {
                    commentEl.classList.add('is-invalid');
                    commentEl.classList.add('border-red-500');
                    const errComment = document.getElementById('err_comment');
                    if (errComment) {
                        errComment.querySelector('.err-text').innerText = 'Nội dung nhận xét đánh giá phải có độ dài tối thiểu 10 ký tự.';
                        errComment.classList.remove('hidden');
                    }
                    hasError = true;
                } else {
                    commentEl.classList.remove('is-invalid');
                    commentEl.classList.remove('border-red-500');
                    document.getElementById('err_comment')?.classList.add('hidden');
                }

                if (hasError) return;

                // Submit qua Ajax / Fetch
                const btn = document.getElementById('btnSubmitReview');
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-[11px]"></i> <span>Đang gửi...</span>`;

                try {
                    const response = await fetch(formRoomReview.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            rating: parseInt(ratingVal, 10),
                            comment: commentVal
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (response.status === 401) {
                            showToast(data.message || 'Vui lòng đăng nhập tài khoản để thực hiện đánh giá.', false, 'AUTH_REQUIRED');
                        } else if (response.status === 403) {
                            showToast(data.message || 'Chỉ người có hợp đồng thuê phòng mới được quyền gửi đánh giá thực tế.', false, 'FORBIDDEN');
                        } else {
                            if (data.errors) {
                                if (data.errors.rating) {
                                    starPickerBox?.classList.add('star-warning-animate');
                                    const errRating = document.getElementById('err_rating');
                                    if (errRating) {
                                        errRating.querySelector('.err-text').innerText = data.errors.rating[0];
                                        errRating.classList.remove('hidden');
                                    }
                                }
                                if (data.errors.comment) {
                                    commentEl.classList.add('is-invalid', 'border-red-500');
                                    const errComment = document.getElementById('err_comment');
                                    if (errComment) {
                                        errComment.querySelector('.err-text').innerText = data.errors.comment[0];
                                        errComment.classList.remove('hidden');
                                    }
                                }
                            }
                            showToast(data.message || 'Dữ liệu đánh giá không hợp lệ.', false, data.code || 'ERR');
                        }
                    } else {
                        showToast(data.message || 'Cảm ơn bạn đã gửi đánh giá thực tế!', true, 'SUCCESS');
                        commentEl.value = '';
                        ratingInput.value = '';
                        updateStarUI(0);
                        setTimeout(() => window.location.reload(), 1200);
                    }
                } catch (err) {
                    showToast('Đã xảy ra lỗi kết nối. Vui lòng thử lại!', false, 'NETWORK_ERROR');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });

            // Khi người dùng gõ vào textarea thì xóa viền đỏ lỗi
            document.getElementById('comment')?.addEventListener('input', function() {
                if (this.value.trim().length >= 10) {
                    this.classList.remove('is-invalid', 'border-red-500');
                    document.getElementById('err_comment')?.classList.add('hidden');
                }
            });
        }

        // Xử lý gửi Form Báo cáo vi phạm (Report)
        const formRoomReport = document.getElementById('formRoomReport');
        if (formRoomReport) {
            formRoomReport.addEventListener('submit', async function(e) {
                e.preventDefault();

                const reasonSelect = document.getElementById('report_reason');
                const reasonVal = reasonSelect.value.trim();
                const descVal = document.getElementById('report_description')?.value.trim() || '';

                // Kiểm tra chưa chọn lý do vi phạm (ERR_25_03)
                if (!reasonVal) {
                    reasonSelect.classList.add('is-invalid', 'border-red-500');
                    const errReport = document.getElementById('err_report_reason');
                    if (errReport) {
                        errReport.querySelector('.err-text').innerText = 'Vui lòng chọn lý do vi phạm (Lừa cọc, Ảnh sai thực tế, Giá ảo...).';
                        errReport.classList.remove('hidden');
                    }
                    return;
                } else {
                    reasonSelect.classList.remove('is-invalid', 'border-red-500');
                    document.getElementById('err_report_reason')?.classList.add('hidden');
                }

                const btn = document.getElementById('btnSubmitReport');
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>Đang gửi...</span>`;

                try {
                    const response = await fetch(formRoomReport.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            report_reason: reasonVal,
                            description: descVal,
                            reporter_name: formRoomReport.querySelector('[name="reporter_name"]')?.value?.trim() || '',
                            reporter_phone: formRoomReport.querySelector('[name="reporter_phone"]')?.value?.trim() || ''
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        showToast(data.message || 'Không thể gửi báo cáo.', false, data.code || 'ERR');
                    } else {
                        // Thành công: ERR_25_04
                        closeReportModal();
                        showToast(data.message || 'Đã tiếp nhận báo cáo vi phạm. Quản trị viên sẽ kiểm duyệt tin này trong 12h.', true, data.code || 'ERR_25_04');
                        formRoomReport.reset();
                    }
                } catch (err) {
                    showToast('Đã xảy ra lỗi kết nối. Vui lòng thử lại!', false, 'NETWORK_ERROR');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });

            document.getElementById('report_reason')?.addEventListener('change', function() {
                if (this.value) {
                    this.classList.remove('is-invalid', 'border-red-500');
                    document.getElementById('err_report_reason')?.classList.add('hidden');
                }
            });
        }

        // FEAT_07_CONTACT: Validation giao diện bôi đỏ viền & thông báo lỗi chuẩn Table 61
        const formContact = document.getElementById('formContactRequest');
        if (formContact) {
            const inputName = document.getElementById('customer_name');
            const inputPhone = document.getElementById('customer_phone');
            const inputTime = document.getElementById('appointment_time');

            const errName = document.getElementById('err_customer_name');
            const errPhone = document.getElementById('err_customer_phone');
            const errTime = document.getElementById('err_appointment_time');

            function showFieldError(input, errorEl, msg) {
                if (!input) return;
                input.classList.remove('border-slate-800');
                input.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/30', 'bg-rose-950/20');
                if (errorEl) {
                    errorEl.innerHTML = `<i class="fa-solid fa-circle-exclamation text-[10px]"></i> <span>${msg}</span>`;
                    errorEl.classList.remove('hidden');
                }
            }

            function clearFieldError(input, errorEl) {
                if (!input) return;
                input.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/30', 'bg-rose-950/20');
                input.classList.add('border-slate-800');
                if (errorEl) {
                    errorEl.classList.add('hidden');
                }
            }

            inputName?.addEventListener('input', () => clearFieldError(inputName, errName));
            inputPhone?.addEventListener('input', () => clearFieldError(inputPhone, errPhone));
            inputTime?.addEventListener('input', () => clearFieldError(inputTime, errTime));
            inputTime?.addEventListener('change', () => clearFieldError(inputTime, errTime));

            formContact.addEventListener('submit', function(e) {
                let hasErr = false;

                // 1. Khách để trống họ và tên (ERR_07_01)
                const nameVal = (inputName?.value || '').trim();
                if (!nameVal) {
                    showFieldError(inputName, errName, 'Vui lòng nhập họ và tên để chủ trọ tiện xưng hô liên hệ.');
                    if (!hasErr) inputName?.focus();
                    hasErr = true;
                } else if (nameVal.length < 2 || nameVal.length > 100) {
                    showFieldError(inputName, errName, 'Họ và tên phải từ 2 đến 100 ký tự.');
                    if (!hasErr) inputName?.focus();
                    hasErr = true;
                } else {
                    clearFieldError(inputName, errName);
                }

                // 2. Khách để trống số điện thoại (ERR_07_02 & ERR_07_03)
                const phoneVal = (inputPhone?.value || '').trim();
                const cleanPhone = phoneVal.replace(/[^0-9]/g, '');
                if (!phoneVal) {
                    showFieldError(inputPhone, errPhone, 'Vui lòng nhập số điện thoại để nhận cuộc gọi tư vấn.');
                    if (!hasErr) inputPhone?.focus();
                    hasErr = true;
                } else if (cleanPhone.length !== 10 || !/^(0[3|5|7|8|9])[0-9]{8}$/.test(cleanPhone)) {
                    showFieldError(inputPhone, errPhone, 'Số điện thoại liên hệ phải có chính xác 10 chữ số.');
                    if (!hasErr) inputPhone?.focus();
                    hasErr = true;
                } else {
                    clearFieldError(inputPhone, errPhone);
                }

                // 3. Kiểm tra bắt buộc chọn ngày hẹn xem phòng & không ở quá khứ (ERR_07_04)
                const timeVal = (inputTime?.value || '').trim();
                if (!timeVal) {
                    showFieldError(inputTime, errTime, 'Vui lòng chọn ngày và giờ bạn muốn đến xem phòng.');
                    if (!hasErr) inputTime?.focus();
                    hasErr = true;
                } else {
                    const selectedDate = new Date(timeVal);
                    const now = new Date();
                    const hours = selectedDate.getHours();
                    const minutes = selectedDate.getMinutes();

                    if (selectedDate <= now) {
                        showFieldError(inputTime, errTime, 'Ngày hẹn đến xem phòng không thể là ngày trong quá khứ.');
                        if (!hasErr) inputTime?.focus();
                        hasErr = true;
                    } else if (hours < 7 || hours > 21 || (hours === 21 && minutes > 0)) {
                        showFieldError(inputTime, errTime, 'Thời gian hẹn xem phòng phải nằm trong khung giờ 07:00 - 21:00 hàng ngày.');
                        if (!hasErr) inputTime?.focus();
                        hasErr = true;
                    } else {
                        clearFieldError(inputTime, errTime);
                    }
                }

                if (hasErr) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });
        }
    </script>
    @if(session('success') && str_contains(session('success'), 'Gửi yêu cầu hẹn xem phòng thành công'))
    <!-- Modal Popup Cảm Ơn & Xác Nhận Đặt Lịch (Table 61 - ERR_07_05) -->
    <div id="contact-success-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 animate-fade-in">
        <div class="w-full max-w-md rounded-3xl bg-[#0a0f1d] border border-emerald-500/30 p-6 md:p-8 shadow-2xl text-center relative overflow-hidden">
            <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 mx-auto flex items-center justify-center text-3xl mb-4 shadow-lg shadow-emerald-500/10 animate-bounce">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-extrabold uppercase tracking-widest mb-2">
                ERR_07_05: Đặt lịch thành công
            </span>
            <h3 class="text-xl font-extrabold text-white mt-1">Đã gửi yêu cầu hẹn xem phòng!</h3>
            <p class="text-xs text-slate-400 mt-2.5 leading-relaxed">
                Cảm ơn bạn đã quan tâm. Chủ trọ đã nhận được thông tin và sẽ sớm gọi điện tư vấn, sắp xếp lịch đón bạn đến xem phòng thực tế.
            </p>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('contact-success-modal').remove()" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-lg shadow-emerald-600/20 active:scale-95 cursor-pointer">
                    Tôi đã hiểu
                </button>
            </div>
        </div>
    </div>
    @endif
</body>
</html>
