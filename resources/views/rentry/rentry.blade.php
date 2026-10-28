<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Renty Review - Nền tảng tìm kiếm và đánh giá phòng trọ chân thực.">
    <title>Renty Review - Tìm Phòng Trọ & Đánh Giá Không Gian Sống</title>
    <script>
        (function() {
            try {
                var initialSavedTheme = localStorage.getItem('renty_theme_mode') || localStorage.getItem('smartroom_theme') || 'dark';
                var isLight = (initialSavedTheme === 'light');
                document.documentElement.classList.remove('theme-fire', 'theme-ice');
                document.documentElement.classList.toggle('theme-light', isLight);
                
                var savedDuo = localStorage.getItem('smartroom_accent_duo') || 'violet-mint';
                document.documentElement.setAttribute('data-duo', savedDuo);
            } catch (e) {}
        })();
    </script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" crossorigin="anonymous">
    
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js" integrity="sha384-Emf5ZVIUiarAdhkgtqK+5/Z5qIvM6QoVrS+qAVtfyZVuD5lIHrac0me485+Antp0" crossorigin="anonymous"></script>

    <!-- Pass Laravel variables to Global JS context (Must precede google-maps-renty.js) -->
    <script>
        window.rentyRoomsData = {!! json_encode($rooms->keyBy('id')) !!};
        window.rentyIsAuthenticated = @json(auth()->check());
        window.rentySessionSuccess = {!! json_encode(session('success')) !!};
        window.rentySessionError = {!! json_encode(session('error')) !!};
    </script>

    <!-- Leaflet Map Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="{{ asset('js/google-maps-renty.js') }}?v={{ time() }}"></script>

    <!-- Custom CSS -->
    @vite(['resources/css/app.css', 'resources/css/style.css'])

    <style>
    /* ============================================================ */
    /* ÁNH LỬA XANH CHÁY NHẸ BẬP BÙNG QUANH VIỀN THẺ PHÒNG HÌNH CHỮ NHẬT */
    /* ============================================================ */
    .room-item-card.verified-flame-card,
    .verified-flame-card,
    .verified-flame-banner {
        position: relative !important;
        /* Ánh lửa xanh nhẹ bập bùng ôm trọn viền hình chữ nhật của thẻ */
        border: 1.5px solid rgba(56, 189, 248, 0.75) !important;
        animation: blueFlameNaturalFlicker 2.2s ease-in-out infinite !important;
        transition: transform var(--transition-normal, 0.25s), box-shadow 0.25s ease, border-color 0.25s ease !important;
    }

    @keyframes blueFlameNaturalFlicker {
        0% {
            box-shadow: 
                0 0 5px rgba(56, 189, 248, 0.65),
                0 0 11px rgba(14, 165, 233, 0.35),
                0 0 18px rgba(2, 132, 199, 0.15);
            border-color: rgba(56, 189, 248, 0.7);
        }
        18% {
            box-shadow: 
                0 0 8px rgba(56, 189, 248, 0.85),
                0 0 16px rgba(14, 165, 233, 0.45),
                0 0 24px rgba(2, 132, 199, 0.22);
            border-color: rgba(125, 211, 252, 0.9);
        }
        35% {
            box-shadow: 
                0 0 4px rgba(56, 189, 248, 0.55),
                0 0 9px rgba(14, 165, 233, 0.28),
                0 0 16px rgba(2, 132, 199, 0.12);
            border-color: rgba(56, 189, 248, 0.6);
        }
        52% {
            box-shadow: 
                0 0 9px rgba(56, 189, 248, 0.95),
                0 0 18px rgba(14, 165, 233, 0.5),
                0 0 28px rgba(2, 132, 199, 0.25);
            border-color: rgba(186, 230, 253, 1);
        }
        70% {
            box-shadow: 
                0 0 6px rgba(56, 189, 248, 0.7),
                0 0 13px rgba(14, 165, 233, 0.38),
                0 0 20px rgba(2, 132, 199, 0.18);
            border-color: rgba(56, 189, 248, 0.75);
        }
        85% {
            box-shadow: 
                0 0 8px rgba(56, 189, 248, 0.8),
                0 0 15px rgba(14, 165, 233, 0.42),
                0 0 23px rgba(2, 132, 199, 0.2);
            border-color: rgba(125, 211, 252, 0.85);
        }
        100% {
            box-shadow: 
                0 0 5px rgba(56, 189, 248, 0.65),
                0 0 11px rgba(14, 165, 233, 0.35),
                0 0 18px rgba(2, 132, 199, 0.15);
            border-color: rgba(56, 189, 248, 0.7);
        }
    }

    /* Khi rê chuột: ngọn lửa bùng sáng nhẹ hơn */
    .room-item-card.verified-flame-card:hover,
    .room-item-card:hover .verified-flame-banner {
        box-shadow: 
            0 0 12px rgba(56, 189, 248, 1),
            0 0 24px rgba(14, 165, 233, 0.6),
            0 0 36px rgba(2, 132, 199, 0.3) !important;
        border-color: #38bdf8 !important;
        transition: box-shadow 0.25s ease, border-color 0.25s ease;
    }

    /* Huy hiệu Tích Xanh ánh lửa rực rỡ */
    .verified-flame-badge {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.25), rgba(56, 189, 248, 0.2)) !important;
        border: 1px solid rgba(56, 189, 248, 0.75) !important;
        color: #e0f2fe !important;
        box-shadow: 0 0 8px rgba(56, 189, 248, 0.5), 0 0 14px rgba(14, 165, 233, 0.25) !important;
        animation: blueFlameBadgeGlow 2.5s ease-in-out infinite alternate !important;
    }
    .verified-flame-badge i {
        color: #38bdf8 !important;
        filter: drop-shadow(0 0 4px #00f2fe) !important;
    }

    @keyframes blueFlameBadgeGlow {
        0% {
            box-shadow: 0 0 5px rgba(56, 189, 248, 0.35);
        }
        100% {
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.7), 0 0 18px rgba(14, 165, 233, 0.35);
        }
    }
    </style>

    <script>
        window.toggleThemeMode = window.toggleThemeMode || function() {
            const isLight = document.body.classList.contains('theme-light') || document.documentElement.classList.contains('theme-light');
            const nextMode = isLight ? 'dark' : 'light';
            localStorage.setItem('renty_theme_mode', nextMode);
            localStorage.setItem('smartroom_theme', nextMode);
            document.documentElement.classList.toggle('theme-light', !isLight);
            document.body.classList.toggle('theme-light', !isLight);
            document.querySelectorAll('[data-theme-switch]').forEach(btn => {
                btn.classList.toggle('is-light', !isLight);
                btn.setAttribute('aria-pressed', !isLight ? 'true' : 'false');
            });
        };

        window.setViewMode = window.setViewMode || function(mode) {
            const mapBtn = document.getElementById('view-mode-map-btn');
            const gridBtn = document.getElementById('view-mode-grid-btn');
            if (mode === 'map') {
                document.body.classList.add('renty-map-mode');
                if (mapBtn) mapBtn.classList.add('active');
                if (gridBtn) gridBtn.classList.remove('active');
                localStorage.setItem('rentry_view_mode', 'map');
                if (typeof window.initRentyMap === 'function') window.initRentyMap();
                [50, 150, 300, 500, 800].forEach(delay => {
                    setTimeout(() => {
                        if (typeof window.initRentyMap === 'function') window.initRentyMap();
                        const m = (window.rentyGoogleMap && window.rentyGoogleMap.map) || (typeof rentyMap !== 'undefined' ? rentyMap : null);
                        if (m && typeof m.invalidateSize === 'function') {
                            m.invalidateSize();
                        }
                    }, delay);
                });
            } else {
                document.body.classList.remove('renty-map-mode');
                document.body.classList.remove('renty-map-fullscreen');
                const fsIcon = document.getElementById('gm-fullscreen-icon');
                if (fsIcon) fsIcon.className = 'fa-solid fa-expand';
                const fsBtn = document.getElementById('gm-fullscreen-btn');
                if (fsBtn) {
                    fsBtn.title = 'Toàn màn hình (Esc)';
                    fsBtn.classList.remove('border-amber-500/60', 'text-amber-400');
                }
                if (mapBtn) mapBtn.classList.remove('active');
                if (gridBtn) gridBtn.classList.add('active');
                localStorage.setItem('rentry_view_mode', 'grid');
            }
        };

        // Mặc định luôn ở giao diện 2D (Lưới danh sách). Chỉ mở bản đồ khi URL có chỉ định view=map
        document.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            if (params.get('view') === 'map') {
                if (typeof window.setViewMode === 'function') {
                    window.setViewMode('map');
                }
            } else {
                localStorage.setItem('rentry_view_mode', 'grid');
                if (typeof window.setViewMode === 'function') {
                    window.setViewMode('grid');
                }
            }
        });
    </script>

    <style>
        .compare-checkbox {
            accent-color: #10b981 !important;
            cursor: pointer;
        }
        .compare-checkbox:checked {
            background-color: #10b981 !important;
            border-color: #10b981 !important;
        }
        .theme-light #compare-radar-wrap {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }
        .theme-light #compare-radar-wrap h4 {
            color: #0f172a !important;
        }

        /* Đồng bộ giao diện Thanh Tìm Kiếm Hero & Filter Tags khi đổi Light Mode (theme-light) */
        .theme-light #hero-search-box-container,
        html.theme-light #hero-search-box-container,
        body.theme-light #hero-search-box-container {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.08), 0 2px 6px -1px rgba(15, 23, 42, 0.04) !important;
        }

        .theme-light #hero-search-box-container:focus-within,
        html.theme-light #hero-search-box-container:focus-within,
        body.theme-light #hero-search-box-container:focus-within {
            border-color: #10b981 !important;
            box-shadow: 0 4px 20px -2px rgba(16, 185, 129, 0.2), 0 0 0 2px rgba(16, 185, 129, 0.25) !important;
        }

        .theme-light #hero-search-box-container .fa-location-dot,
        html.theme-light #hero-search-box-container .fa-location-dot,
        body.theme-light #hero-search-box-container .fa-location-dot {
            color: #059669 !important;
        }

        .theme-light #hero-search-input,
        html.theme-light #hero-search-input,
        body.theme-light #hero-search-input {
            background-color: transparent !important;
            background: transparent !important;
            color: #0f172a !important;
            border: none !important;
            box-shadow: none !important;
        }

        .theme-light #hero-search-input::placeholder,
        html.theme-light #hero-search-input::placeholder,
        body.theme-light #hero-search-input::placeholder {
            color: #94a3b8 !important;
        }

        .theme-light #hero-search-clear-btn,
        html.theme-light #hero-search-clear-btn,
        body.theme-light #hero-search-clear-btn {
            background-color: #f1f5f9 !important;
            color: #64748b !important;
        }

        .theme-light #hero-search-clear-btn:hover,
        html.theme-light #hero-search-clear-btn:hover,
        body.theme-light #hero-search-clear-btn:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }

        /* Nút tìm kiếm Glassmorphism xanh lá dịu mắt, đồng bộ toàn diện với giao diện */
        .hero-search-btn-glass,
        #btn-hero-search-submit {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.9), rgba(13, 148, 136, 0.95)) !important;
            border: 1px solid rgba(255, 255, 255, 0.22) !important;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35), inset 0 1px 1px rgba(255, 255, 255, 0.3) !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            color: #ffffff !important;
        }

        .hero-search-btn-glass:hover,
        #btn-hero-search-submit:hover {
            background: linear-gradient(135deg, rgba(16, 185, 129, 1), rgba(20, 184, 166, 1)) !important;
            border-color: rgba(255, 255, 255, 0.38) !important;
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45), inset 0 1px 2px rgba(255, 255, 255, 0.45) !important;
            transform: translateY(-0.5px);
        }

        .theme-light .hero-search-btn-glass,
        html.theme-light #btn-hero-search-submit,
        body.theme-light #btn-hero-search-submit {
            background: linear-gradient(135deg, #059669, #0d9488) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.28) !important;
            color: #ffffff !important;
        }

        .theme-light .hero-search-btn-glass:hover,
        html.theme-light #btn-hero-search-submit:hover,
        body.theme-light #btn-hero-search-submit:hover {
            background: linear-gradient(135deg, #047857, #0f766e) !important;
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.38) !important;
        }

        .theme-light #hero-quick-tags span,
        html.theme-light #hero-quick-tags span,
        body.theme-light #hero-quick-tags span {
            color: #64748b !important;
        }

        .theme-light #hero-quick-tags .quick-filter-tag,
        html.theme-light #hero-quick-tags .quick-filter-tag,
        body.theme-light #hero-quick-tags .quick-filter-tag {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }

        .theme-light #hero-quick-tags .quick-filter-tag:hover,
        html.theme-light #hero-quick-tags .quick-filter-tag:hover,
        body.theme-light #hero-quick-tags .quick-filter-tag:hover {
            background-color: #ecfdf5 !important;
            border-color: #10b981 !important;
            color: #059669 !important;
        }

        @keyframes heroTextGlow {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }
        .hero-title-accent {
            background: linear-gradient(90deg, #10b981 0%, #14b8a6 35%, #06b6d4 70%, #3b82f6 100%) !important;
            background-size: 250% auto !important;
            -webkit-background-clip: text !important;
            background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            color: transparent !important;
            animation: heroTextGlow 8s ease-in-out infinite alternate !important;
            transition: background 0.5s ease;
        }
        .theme-light .hero-title-accent,
        html.theme-light .hero-title-accent,
        body.theme-light .hero-title-accent {
            background: linear-gradient(90deg, #059669 0%, #0d9488 35%, #0284c7 70%, #4f46e5 100%) !important;
            background-size: 250% auto !important;
            -webkit-background-clip: text !important;
            background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            color: transparent !important;
            animation: heroTextGlow 8s ease-in-out infinite alternate !important;
            filter: none !important;
        }

        /* Đồng bộ giao diện màu sắc Light Mode cho Hero Search */
        html.theme-light #renty-hero-section .group\/search > div:nth-child(2),
        body.theme-light #renty-hero-section .group\/search > div:nth-child(2) {
            background-color: transparent !important;
            border-color: transparent !important;
        }
        html.theme-light #hero-search-box-container,
        body.theme-light #hero-search-box-container {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.08), 0 2px 6px -1px rgba(15, 23, 42, 0.04) !important;
        }
        html.theme-light #hero-search-box-container:focus-within,
        body.theme-light #hero-search-box-container:focus-within {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2), 0 10px 25px -5px rgba(15, 23, 42, 0.1) !important;
        }
        html.theme-light #hero-search-box-container .fa-location-dot,
        body.theme-light #hero-search-box-container .fa-location-dot {
            color: #059669 !important;
        }
        html.theme-light #hero-search-input,
        body.theme-light #hero-search-input {
            background: transparent !important;
            background-color: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            color: #0f172a !important;
        }
        html.theme-light #hero-search-input::placeholder,
        body.theme-light #hero-search-input::placeholder {
            color: #94a3b8 !important;
        }
        html.theme-light #hero-search-clear-btn,
        body.theme-light #hero-search-clear-btn {
            background-color: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            color: #64748b !important;
        }
        html.theme-light #hero-search-clear-btn:hover,
        body.theme-light #hero-search-clear-btn:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }
        html.theme-light #btn-hero-search-submit,
        body.theme-light #btn-hero-search-submit {
            background: linear-gradient(135deg, #059669 0%, #0d9488 100%) !important;
            background-color: #059669 !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3) !important;
        }
        html.theme-light #btn-hero-search-submit:hover,
        body.theme-light #btn-hero-search-submit:hover {
            background: linear-gradient(135deg, #047857 0%, #0f766e 100%) !important;
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.4) !important;
        }
        html.theme-light #btn-hero-search-submit i,
        html.theme-light #btn-hero-search-submit span,
        body.theme-light #btn-hero-search-submit i,
        body.theme-light #btn-hero-search-submit span {
            color: #ffffff !important;
        }

        /* Đồng bộ Empty State trong Light Mode */
        html.theme-light #smart-search-empty-state,
        body.theme-light #smart-search-empty-state {
            background: rgba(255, 255, 255, 0.92) !important;
            border-color: rgba(226, 232, 240, 0.95) !important;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.06) !important;
        }
        html.theme-light #smart-search-empty-state > div:first-child,
        body.theme-light #smart-search-empty-state > div:first-child {
            background-color: #ecfdf5 !important;
            border-color: #a7f3d0 !important;
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.15) !important;
        }
        html.theme-light #smart-search-empty-state > div:first-child i,
        body.theme-light #smart-search-empty-state > div:first-child i {
            color: #059669 !important;
        }
        html.theme-light #smart-search-empty-title,
        body.theme-light #smart-search-empty-title {
            color: #0f172a !important;
        }
        html.theme-light #smart-search-empty-code,
        html.theme-light #smart-search-empty-state p,
        body.theme-light #smart-search-empty-code,
        body.theme-light #smart-search-empty-state p {
            color: #475569 !important;
        }
        html.theme-light #smart-search-empty-did-you-mean,
        body.theme-light #smart-search-empty-did-you-mean {
            background-color: #ecfdf5 !important;
            border-color: #6ee7b7 !important;
            color: #065f46 !important;
        }
        html.theme-light #smart-search-empty-did-you-mean strong,
        body.theme-light #smart-search-empty-did-you-mean strong {
            color: #047857 !important;
        }
        html.theme-light #btnExploreEmpty,
        body.theme-light #btnExploreEmpty {
            background: linear-gradient(135deg, #059669 0%, #0d9488 100%) !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3) !important;
        }
        html.theme-light #btnExploreEmpty i,
        body.theme-light #btnExploreEmpty i {
            color: #ffffff !important;
        }
        html.theme-light #btnResetFilterEmpty,
        body.theme-light #btnResetFilterEmpty {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05) !important;
        }
        html.theme-light #btnResetFilterEmpty:hover,
        body.theme-light #btnResetFilterEmpty:hover {
            background-color: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }
        html.theme-light #btnResetFilterEmpty i,
        body.theme-light #btnResetFilterEmpty i {
            color: #64748b !important;
        }

        /* Badge phòng đã xem */
        .viewed-room-strip {
            display: none !important;
            align-items: center;
            gap: 0.35rem;
            width: fit-content;
            max-width: 100%;
            margin-bottom: 0.45rem;
            padding: 0.2rem 0.55rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, rgba(20, 184, 166, 0.16), rgba(15, 23, 42, 0.8));
            border: 1px solid rgba(45, 212, 191, 0.35);
            color: #5eead4;
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            line-height: 1;
            box-shadow: 0 4px 12px rgba(20, 184, 166, 0.15);
        }
        .room-card-viewed .viewed-room-strip {
            display: inline-flex !important;
        }
        .viewed-room-strip-icon {
            position: relative;
            display: inline-grid;
            place-items: center;
            width: 1.15rem;
            height: 1.15rem;
            border-radius: 9999px;
            color: #2dd4bf;
            background: rgba(15, 23, 42, 0.8);
            font-size: 0.65rem;
        }
        .viewed-room-strip-icon .fa-check {
            position: absolute;
            right: -0.15rem;
            bottom: -0.1rem;
            width: 0.65rem;
            height: 0.65rem;
            display: grid;
            place-items: center;
            border-radius: 9999px;
            background: #14b8a6;
            color: #022c22;
            font-size: 0.42rem;
        }
        .room-card-viewed {
            border-color: rgba(45, 212, 191, 0.45) !important;
        }
        .theme-light .viewed-room-strip {
            background: linear-gradient(135deg, rgba(13, 148, 136, 0.12), rgba(241, 245, 249, 0.95));
            border-color: rgba(13, 148, 136, 0.35);
            color: #0f766e;
        }

        /* Custom Range Slider Styles chuẩn theo Hình 21.2 & 21.3 */
        .custom-range-slider {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 4px;
            background: transparent;
            outline: none;
            cursor: pointer;
            margin: 8px 0;
            display: block;
        }
        .custom-range-slider::-webkit-slider-runnable-track {
            width: 100%;
            height: 4px;
            border-radius: 99px;
            background: linear-gradient(
                to right, 
                #ffffff 0%, 
                #ffffff var(--range-progress, 100%), 
                #334155 var(--range-progress, 100%), 
                #334155 100%
            );
            border: none;
        }
        .custom-range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #ffffff;
            margin-top: -6px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.85);
            transition: transform 0.15s ease;
        }
        .custom-range-slider::-webkit-slider-thumb:hover {
            transform: scale(1.2);
        }
        .custom-range-slider::-moz-range-track {
            width: 100%;
            height: 4px;
            border-radius: 99px;
            background: #334155;
            border: none;
        }
        .custom-range-slider::-moz-range-progress {
            height: 4px;
            border-radius: 99px;
            background: #ffffff;
        }
        .custom-range-slider::-moz-range-thumb {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #ffffff;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.85);
        }
        /* Ẩn spinner cho input number */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] { 
            -moz-appearance: textfield; 
        }

        /* =========================================================
           LIGHT THEME: GOOGLE MAPS PRO HUD BAR ENHANCEMENTS
           ========================================================= */
        html.theme-light .gm-pro-hud,
        body.theme-light .gm-pro-hud {
            background: rgba(255, 255, 255, 0.96) !important;
            background-color: #ffffff !important;
            backdrop-filter: blur(24px) saturate(180%) !important;
            -webkit-backdrop-filter: blur(24px) saturate(180%) !important;
            border: 1.5px solid #cbd5e1 !important;
            border-top: 2px solid #ffffff !important;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;
        }

        html.theme-light .gm-pro-hud .gm-city-switcher,
        body.theme-light .gm-pro-hud .gm-city-switcher,
        html.theme-light .gm-pro-hud div[class*="bg-slate-900"],
        body.theme-light .gm-pro-hud div[class*="bg-slate-900"] {
            background: #f1f5f9 !important;
            background-color: #f1f5f9 !important;
            border-color: #e2e8f0 !important;
        }

        html.theme-light .gm-pro-hud .gm-city-tab:not(.active),
        body.theme-light .gm-pro-hud .gm-city-tab:not(.active) {
            color: #1e293b !important;
            font-weight: 700 !important;
            background: transparent !important;
        }
        html.theme-light .gm-pro-hud .gm-city-tab:not(.active):hover,
        body.theme-light .gm-pro-hud .gm-city-tab:not(.active):hover {
            color: #0f172a !important;
            background-color: #e2e8f0 !important;
        }

        html.theme-light #gm-search-input,
        body.theme-light #gm-search-input {
            background: #ffffff !important;
            background-color: #ffffff !important;
            color: #0f172a !important;
            border: 1.5px solid #cbd5e1 !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04) !important;
        }
        html.theme-light #gm-search-input:focus,
        body.theme-light #gm-search-input:focus {
            border-color: #0284c7 !important;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15) !important;
        }
        html.theme-light #gm-search-input::placeholder,
        body.theme-light #gm-search-input::placeholder {
            color: #64748b !important;
        }
        html.theme-light #gm-search-engine-wrapper .fa-magnifying-glass,
        body.theme-light #gm-search-engine-wrapper .fa-magnifying-glass {
            color: #0284c7 !important;
        }

        html.theme-light #gm-search-autocomplete,
        body.theme-light #gm-search-autocomplete {
            background: #ffffff !important;
            background-color: #ffffff !important;
            color: #0f172a !important;
            border-color: #38bdf8 !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
        }

        html.theme-light .gm-pro-hud .gm-layer-container,
        body.theme-light .gm-pro-hud .gm-layer-container {
            background: #f1f5f9 !important;
            background-color: #f1f5f9 !important;
            border-color: #e2e8f0 !important;
        }

        html.theme-light .gm-pro-hud button[onclick*="setRentyMapLayer"]:not(#gm-layer-roadmap),
        body.theme-light .gm-pro-hud button[onclick*="setRentyMapLayer"]:not(#gm-layer-roadmap) {
            color: #475569 !important;
            background: transparent !important;
        }
        html.theme-light .gm-pro-hud button[onclick*="setRentyMapLayer"]:not(#gm-layer-roadmap):hover,
        body.theme-light .gm-pro-hud button[onclick*="setRentyMapLayer"]:not(#gm-layer-roadmap):hover {
            color: #0f172a !important;
            background: #e2e8f0 !important;
        }

        html.theme-light .gm-pro-hud .gm-action-btn,
        body.theme-light .gm-pro-hud .gm-action-btn,
        html.theme-light .gm-pro-hud button[onclick*="locateUserRentyMap"],
        body.theme-light .gm-pro-hud button[onclick*="locateUserRentyMap"],
        html.theme-light .gm-pro-hud button[onclick*="resetRentyMapView"],
        body.theme-light .gm-pro-hud button[onclick*="resetRentyMapView"],
        html.theme-light .gm-pro-hud #gm-fullscreen-btn,
        body.theme-light .gm-pro-hud #gm-fullscreen-btn,
        html.theme-light .gm-pro-hud #gm-hud-toggle-btn,
        body.theme-light .gm-pro-hud #gm-hud-toggle-btn {
            background: #ffffff !important;
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #1e293b !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08) !important;
        }
        html.theme-light .gm-pro-hud .gm-action-btn:hover,
        body.theme-light .gm-pro-hud .gm-action-btn:hover,
        html.theme-light .gm-pro-hud button[onclick*="locateUserRentyMap"]:hover,
        body.theme-light .gm-pro-hud button[onclick*="locateUserRentyMap"]:hover,
        html.theme-light .gm-pro-hud button[onclick*="resetRentyMapView"]:hover,
        body.theme-light .gm-pro-hud button[onclick*="resetRentyMapView"]:hover,
        html.theme-light .gm-pro-hud #gm-fullscreen-btn:hover,
        body.theme-light .gm-pro-hud #gm-fullscreen-btn:hover,
        html.theme-light .gm-pro-hud #gm-hud-toggle-btn:hover,
        body.theme-light .gm-pro-hud #gm-hud-toggle-btn:hover {
            background: #f8fafc !important;
            background-color: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }
        html.theme-light .gm-pro-hud .gm-action-btn i,
        body.theme-light .gm-pro-hud .gm-action-btn i,
        html.theme-light .gm-pro-hud button[onclick*="locateUserRentyMap"] i,
        body.theme-light .gm-pro-hud button[onclick*="locateUserRentyMap"] i,
        html.theme-light .gm-pro-hud button[onclick*="resetRentyMapView"] i,
        body.theme-light .gm-pro-hud button[onclick*="resetRentyMapView"] i,
        html.theme-light .gm-pro-hud #gm-fullscreen-btn i,
        body.theme-light .gm-pro-hud #gm-fullscreen-btn i,
        html.theme-light .gm-pro-hud #gm-hud-toggle-btn i,
        body.theme-light .gm-pro-hud #gm-hud-toggle-btn i {
            color: #334155 !important;
        }

        html.theme-light .gm-pro-hud .gm-campus-label,
        body.theme-light .gm-pro-hud .gm-campus-label {
            color: #0284c7 !important;
            font-weight: 800 !important;
        }

        html.theme-light .gm-pro-hud .gm-scroll-btn,
        body.theme-light .gm-pro-hud .gm-scroll-btn,
        html.theme-light .gm-pro-hud button[onclick*="scrollCampusBar"],
        body.theme-light .gm-pro-hud button[onclick*="scrollCampusBar"] {
            background: #ffffff !important;
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #1e293b !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06) !important;
        }
        html.theme-light .gm-pro-hud .gm-scroll-btn:hover,
        body.theme-light .gm-pro-hud .gm-scroll-btn:hover,
        html.theme-light .gm-pro-hud button[onclick*="scrollCampusBar"]:hover,
        body.theme-light .gm-pro-hud button[onclick*="scrollCampusBar"]:hover {
            background: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }

        html.theme-light .gm-pro-hud .gm-campus-chip:not(.active),
        body.theme-light .gm-pro-hud .gm-campus-chip:not(.active) {
            background: #ffffff !important;
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #1e293b !important;
            font-weight: 600 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
        }
        html.theme-light .gm-pro-hud .gm-campus-chip:not(.active):hover,
        body.theme-light .gm-pro-hud .gm-campus-chip:not(.active):hover {
            background: #f8fafc !important;
            border-color: #0284c7 !important;
            color: #0284c7 !important;
        }

        html.theme-light .gm-pro-hud #radius_slider_wrapper,
        body.theme-light .gm-pro-hud #radius_slider_wrapper {
            background: #ffffff !important;
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06) !important;
        }
        html.theme-light .gm-pro-hud #radius_slider_wrapper label,
        body.theme-light .gm-pro-hud #radius_slider_wrapper label {
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        html.theme-light .gm-pro-hud #radius_slider_wrapper label i,
        body.theme-light .gm-pro-hud #radius_slider_wrapper label i {
            color: #059669 !important;
        }
        html.theme-light .gm-pro-hud #range_radius,
        body.theme-light .gm-pro-hud #range_radius {
            background-color: #e2e8f0 !important;
        }
        html.theme-light .gm-pro-hud #range_radius_val,
        body.theme-light .gm-pro-hud #range_radius_val {
            color: #059669 !important;
            font-weight: 800 !important;
        }
        html.theme-light .gm-pro-hud .gm-radius-chip:not(.active),
        body.theme-light .gm-pro-hud .gm-radius-chip:not(.active) {
            color: #475569 !important;
            background: #f1f5f9 !important;
        }
        html.theme-light .gm-pro-hud .gm-radius-chip:not(.active):hover,
        body.theme-light .gm-pro-hud .gm-radius-chip:not(.active):hover {
            color: #0f172a !important;
            background: #e2e8f0 !important;
        }

        html.theme-light .gm-pro-hud .border-white\/10,
        body.theme-light .gm-pro-hud .border-white\/10 {
            border-color: #e2e8f0 !important;
        }
    </style>
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen flex flex-col justify-between overflow-x-hidden selection:bg-emerald-500 selection:text-white">
    <script>
        (function() {
            try {
                var theme = localStorage.getItem('renty_theme_mode') || 'ice';
                document.body.classList.add(theme === 'fire' ? 'theme-fire' : 'theme-ice');
            } catch (e) {}
        })();
    </script>
    <div id="theme-flip-wash" class="theme-flip-wash" aria-hidden="true"></div>

    <!-- Decorative glows -->
    <div class="absolute top-[-15%] left-[-10%] w-[500px] h-[500px] rounded-full bg-emerald-600/5 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-0 right-[-10%] w-[500px] h-[500px] rounded-full bg-indigo-600/5 blur-[120px] pointer-events-none"></div>

    @include('header.header')

    @if(session('error'))
        <div class="container mx-auto px-6 pt-4 max-w-6xl">
            <div class="p-4 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs font-bold flex items-center justify-between gap-3 animate-shake shadow-lg">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-base shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white px-2 py-1"><i class="fa-solid fa-xmark"></i></button>
            </div>
        </div>
    @endif
    @if(session('success'))
        <div class="container mx-auto px-6 pt-4 max-w-6xl">
            <div class="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs font-bold flex items-center justify-between gap-3 animate-fade-in shadow-lg">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-base shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white px-2 py-1"><i class="fa-solid fa-xmark"></i></button>
            </div>
        </div>
    @endif

    <!-- HERO BENTO GRID & ADVANCED FILTERS -->
    <section id="renty-hero-section" class="container mx-auto px-6 pt-8 pb-8 max-w-6xl relative z-10 overflow-hidden rounded-3xl">
        <!-- Ambient floating particles background -->
        <canvas id="renty-ambient-particles" class="absolute inset-0 w-full h-full pointer-events-none z-0" style="mix-blend-mode: screen;"></canvas>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative z-10">
            <!-- Bento Box 1: Main Search & Visual Filters (Col span 2) - Cho phép Dropdown nổi ra ngoài mà không bị cắt xén (overflow-visible z-20) -->
            <div class="lg:col-span-2 bg-gradient-to-br from-slate-900/40 to-slate-950/40 border border-slate-800/80 rounded-3xl p-6 md:p-8 backdrop-blur-md flex flex-col justify-between relative overflow-visible z-20 group">
                <!-- Decorative absolute glow inside card -->
                <div class="absolute -top-24 -left-24 w-48 h-48 rounded-full bg-emerald-500/10 blur-[80px] pointer-events-none"></div>
                
                <div class="relative z-50 w-full">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Review phòng trọ thực tế
                    </span>
                    
                    <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight mb-4 leading-tight text-slate-100">
                        Tìm Trọ Đúng Nghĩa - <br class="hidden md:inline"><span class="hero-title-accent bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400 bg-clip-text text-transparent">Xem Review Thật</span>
                    </h1>
                    
                    <p class="text-slate-400 text-xs md:text-sm max-w-xl mb-6 leading-relaxed">
                        Tránh bẫy "ảnh mạng một đằng thực tế một nẻo". Xem đánh giá điểm số chủ nhà, an ninh, điện nước trước khi cọc.
                    </p>
                    
                    <!-- Integrated Search Bar (FEAT_04_LANDING - STT 1: search_box) -->
                    <div class="relative w-full max-w-2xl mb-2 z-50" id="hero-search-wrapper">
                        <label for="hero-search-input" class="sr-only">Khu vực tìm kiếm</label>
                        
                        <!-- Wrapper định vị chuẩn cho Search Input kèm Quầng sáng ôm sát form -->
                        <div class="relative w-full z-50 group/search-box">
                            <!-- Quầng sáng xanh dịu nhẹ ôm sát form tìm kiếm, không lan ra ngoài -->
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-emerald-500/30 via-teal-500/25 to-emerald-400/25 rounded-2xl opacity-20 group-hover/search-box:opacity-50 blur-sm transition duration-300 pointer-events-none"></div>

                            <form onsubmit="submitHeroSearch(event)" class="relative flex items-center bg-slate-950/90 border border-slate-800/80 hover:border-emerald-500/40 focus-within:border-emerald-400/70 rounded-2xl overflow-hidden backdrop-blur-xl transition-all shadow-lg shadow-black/30" id="hero-search-box-container">
                                <i class="fa-solid fa-location-dot pl-4 text-emerald-400/90 text-sm"></i>
                                <input 
                                    type="text" 
                                    id="hero-search-input" 
                                    name="search_box" 
                                    data-field-name="search_box"
                                    maxlength="100"
                                    oninput="handleSearchInput(event)" 
                                    onkeyup="handleSearchInput(event)" 
                                    class="w-full pl-3 pr-20 py-3.5 bg-transparent text-slate-200 placeholder-slate-500 focus:outline-none text-xs md:text-sm font-semibold" 
                                    placeholder="Nhập quận, huyện, tên đường..."
                                >
                                <button type="button" id="hero-search-clear-btn" onclick="clearGlobalSearch()" class="hidden mr-2 text-slate-400 hover:text-white transition-colors w-6 h-6 flex items-center justify-center rounded-full bg-slate-800/80 hover:bg-slate-700 text-xs shrink-0 cursor-pointer" title="Xóa tìm kiếm">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                                <button type="submit" id="btn-hero-search-submit" class="mr-2 px-3.5 py-2 rounded-xl text-white font-extrabold text-xs active:scale-95 transition-all flex items-center gap-1.5 shrink-0 cursor-pointer hero-search-btn-glass" title="Tìm kiếm">
                                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                    <span class="hidden sm:inline">Tìm</span>
                                </button>
                            </form>
                        </div>

                        <!-- Thông báo lỗi khi bấm tìm kiếm mà từ khóa rỗng [ERR_04_01] -->
                        <div id="search-empty-error" class="hidden mt-2 p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-xs text-amber-300 flex items-center justify-between gap-2 animate-shake">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-exclamation text-amber-400 shrink-0"></i>
                                <span id="search-empty-error-text">Vui lòng nhập khu vực, tên đường hoặc trường đại học cần tìm phòng.</span>
                            </div>
                        </div>

                        <!-- Thông báo lỗi từ khóa chứa ký tự không hợp lệ [ERR_04_02] -->
                        <div id="search-invalid-error" class="hidden mt-2 p-2.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-xs text-rose-300 flex items-center justify-between gap-2 animate-shake">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-triangle-exclamation text-rose-400 shrink-0"></i>
                                <span id="search-invalid-error-text">Từ khóa tìm kiếm chứa ký tự không hợp lệ. Hệ thống đã tự động làm sạch.</span>
                            </div>
                        </div>

                        <!-- AI / Smart Search Spell Correction (Did You Mean) Banner -->
                        <div id="smart-search-suggestion" class="hidden mt-2 p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-300 flex items-center justify-between gap-2 animate-fade-in">
                            <div class="flex items-center gap-2 min-w-0">
                                <i class="fa-solid fa-wand-magic-sparkles text-emerald-400 shrink-0"></i>
                                <span class="truncate">Có phải bạn muốn tìm: <button type="button" id="did-you-mean-btn" onclick="applySuggestedQuery()" class="font-extrabold underline hover:text-white decoration-emerald-400"></button>?</span>
                            </div>
                            <button type="button" onclick="closeSuggestion()" class="text-emerald-400 hover:text-white text-xs px-1 shrink-0">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Filter Chips: Tag nhanh (FEAT_04_LANDING - STT 3: tags) -->
                        <div class="flex items-center gap-1.5 flex-wrap mt-3" id="hero-quick-tags" name="tags" data-field-name="tags">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mr-0.5 flex items-center gap-1">
                                <i class="fa-solid fa-tags text-teal-400 text-[10px]"></i> Tag nhanh:
                            </span>
                            <button type="button" onclick="applyQuickTag('Gần ĐH Quốc Gia')" class="quick-filter-tag px-2.5 py-1 rounded-xl bg-slate-900/90 hover:bg-emerald-500/10 border border-slate-800 hover:border-emerald-500/40 text-slate-300 hover:text-emerald-300 text-[11px] font-semibold transition-all cursor-pointer">
                                🎓 Gần ĐH Quốc Gia
                            </button>
                            <button type="button" onclick="applyQuickTag('Studio')" class="quick-filter-tag px-2.5 py-1 rounded-xl bg-slate-900/90 hover:bg-emerald-500/10 border border-slate-800 hover:border-emerald-500/40 text-slate-300 hover:text-emerald-300 text-[11px] font-semibold transition-all cursor-pointer">
                                🏢 Studio
                            </button>
                            <button type="button" onclick="applyQuickTag('Giờ tự do')" class="quick-filter-tag px-2.5 py-1 rounded-xl bg-slate-900/90 hover:bg-emerald-500/10 border border-slate-800 hover:border-emerald-500/40 text-slate-300 hover:text-emerald-300 text-[11px] font-semibold transition-all cursor-pointer">
                                🕒 Giờ tự do
                            </button>
                            <button type="button" onclick="applyQuickTag('Cầu Giấy')" class="quick-filter-tag px-2.5 py-1 rounded-xl bg-slate-900/90 hover:bg-emerald-500/10 border border-slate-800 hover:border-emerald-500/40 text-slate-300 hover:text-emerald-300 text-[11px] font-semibold transition-all cursor-pointer">
                                📍 Cầu Giấy
                            </button>
                            <button type="button" onclick="applyQuickTag('Bách Khoa')" class="quick-filter-tag px-2.5 py-1 rounded-xl bg-slate-900/90 hover:bg-emerald-500/10 border border-slate-800 hover:border-emerald-500/40 text-slate-300 hover:text-emerald-300 text-[11px] font-semibold transition-all cursor-pointer">
                                🏛️ Bách Khoa
                            </button>
                            <button type="button" onclick="applyQuickTag('Thủ Đức')" class="quick-filter-tag px-2.5 py-1 rounded-xl bg-slate-900/90 hover:bg-emerald-500/10 border border-slate-800 hover:border-emerald-500/40 text-slate-300 hover:text-emerald-300 text-[11px] font-semibold transition-all cursor-pointer">
                                📍 Thủ Đức
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Visual Filters Inner Row - Đặt z-0 để không bao giờ đè lên Dropdown Search -->
                <div class="relative z-0 pt-4 border-t border-slate-800/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4" style="position: relative; z-index: 0;">
                    <div>
                        <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                            <i class="fa-solid fa-wand-magic-sparkles text-teal-400"></i>
                            Bộ lọc nhanh trực quan
                        </span>
                        <span class="block text-[9px] text-slate-500 mt-0.5">Click nhanh để lọc nhanh phòng trọ phù hợp</span>
                    </div>
                    
                    <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto" style="position: relative; z-index: 0;">
                        <!-- Nuôi thú cưng -->
                        <button type="button" id="vbtn-pets" onclick="toggleVisualFilter('pets')" class="vfilter-btn flex items-center gap-2 px-3 py-2 bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-350 hover:text-slate-100 rounded-xl transition-all duration-300 text-[10px] font-bold" style="position: relative; z-index: 0;">
                            <i class="fa-solid fa-cat text-teal-450"></i>
                            <span>Thú cưng</span>
                        </button>
                        
                        <!-- WC khép kín -->
                        <button type="button" id="vbtn-wc" onclick="toggleVisualFilter('wc')" class="vfilter-btn flex items-center gap-2 px-3 py-2 bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-350 hover:text-slate-100 rounded-xl transition-all duration-300 text-[10px] font-bold" style="position: relative; z-index: 0;">
                            <i class="fa-solid fa-door-closed text-teal-450"></i>
                            <span>WC khép kín</span>
                        </button>
                        
                        <!-- Có ban công -->
                        <button type="button" id="vbtn-balcony" onclick="toggleVisualFilter('balcony')" class="vfilter-btn flex items-center gap-2 px-3 py-2 bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-350 hover:text-slate-100 rounded-xl transition-all duration-300 text-[10px] font-bold" style="position: relative; z-index: 0;">
                            <i class="fa-solid fa-cloud-sun text-teal-450"></i>
                            <span>Có ban công</span>
                        </button>

                        <button type="button" onclick="toggleFilterDrawer()" class="px-3 py-2 bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-350 hover:text-slate-100 rounded-xl text-[10px] font-extrabold flex items-center gap-1.5 transition-all" style="position: relative; z-index: 0;">
                            <i class="fa-solid fa-sliders text-emerald-450"></i> Lọc nâng cao
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bento Box 2: Community Insights & Live Stats (Col span 1) -->
            <div class="bg-gradient-to-br from-slate-900/40 to-slate-950/40 border border-slate-800/80 rounded-3xl p-6 backdrop-blur-md flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute -bottom-24 -right-24 w-48 h-48 rounded-full bg-teal-500/10 blur-[80px] pointer-events-none"></div>
                
                <div class="relative z-10">
                    <span class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-4">
                        <i class="fa-solid fa-chart-line text-emerald-400 mr-1.5"></i>
                        Thống kê cộng đồng
                    </span>
                    
                    <div class="space-y-4">
                        <!-- Stat 1 -->
                        <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-slate-950/40 border border-slate-900/60 transition-all hover:border-slate-800">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/15 flex items-center justify-center font-bold text-xs shrink-0 animate-pulse">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <span class="block text-xs font-extrabold text-slate-200">100% Xác thực</span>
                                <span class="block text-[9px] text-slate-500">Mọi đánh giá đều từ sinh viên ở thực tế</span>
                            </div>
                        </div>

                        <!-- Stat 2 -->
                        <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-slate-950/40 border border-slate-900/60 transition-all hover:border-slate-800">
                            <div class="w-9 h-9 rounded-xl bg-teal-500/10 text-teal-400 border border-teal-500/15 flex items-center justify-center font-bold text-xs shrink-0">
                                <i class="fa-solid fa-house-circle-check"></i>
                            </div>
                            <div>
                                <span class="block text-xs font-extrabold text-slate-200">Tìm kiếm tối ưu</span>
                                <span class="block text-[9px] text-slate-500">Lọc theo khoảng cách & tiện ích trực quan</span>
                            </div>
                        </div>

                        <!-- Stat 3 -->
                        <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-slate-950/40 border border-slate-900/60 transition-all hover:border-slate-800">
                            <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/15 flex items-center justify-center font-bold text-xs shrink-0">
                                <i class="fa-solid fa-user-secret"></i>
                            </div>
                            <div>
                                <span class="block text-xs font-extrabold text-slate-200">Ẩn danh tuyệt đối</span>
                                <span class="block text-[9px] text-slate-500">Đăng review và hỏi đáp không lo lộ danh tính</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 pt-4 border-t border-slate-800/50 flex items-center justify-between text-[9px] text-slate-550 font-bold uppercase tracking-wider">
                    <span>Cập nhật trực tiếp</span>
                    <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Realtime</span>
                </div>
            </div>
        </div>

        <!-- Advanced Filters Dropdown (Expandable Filters) -->
        <div id="filter-drawer" class="hidden mt-6 bg-[#0a0e17]/90 border border-slate-800/80 p-6 rounded-3xl backdrop-blur-md animate-fade-in shadow-2xl">
            <div class="flex items-center justify-between pb-3 mb-5 border-b border-slate-800/60">
                <span class="text-xs font-black text-slate-200 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-emerald-400"></i> BỘ LỌC CHI TIẾT
                </span>
                <div class="flex items-center gap-3">
                    <button type="button" id="btnApplyFilter" onclick="filterItems()" class="px-4 py-1.5 bg-emerald-700 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-emerald-900/30 flex items-center gap-1.5">
                        <i class="fa-solid fa-filter text-[10px]"></i> Áp dụng bộ lọc
                    </button>
                    <button type="button" onclick="resetAllFilters()" class="text-xs font-semibold text-slate-400 hover:text-rose-400 flex items-center gap-1.5 px-2 py-1 rounded-lg hover:bg-rose-500/10 transition-all">
                        <i class="fa-solid fa-rotate-left"></i> Đặt lại bộ lọc
                    </button>
                </div>
            </div>

            <!-- Hàng 1: 4 Cột theo Spec FEAT_21_SMART_SEARCH (Khoảng giá - Diện tích - Loại phòng & Đánh giá - Khoảng cách) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
                <!-- Cột 1: KHOẢNG GIÁ (VNĐ) - Spec STT 1: range_price -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">KHOẢNG GIÁ (VNĐ)</label>
                        <select id="filter-price" onchange="handlePricePresetChange(this.value)" class="px-2 py-1 bg-[#0a0e17] border border-slate-800 hover:border-slate-700 rounded-lg text-slate-300 text-[11px] focus:border-emerald-500 focus:outline-none transition-colors">
                            <option value="all">Mức giá gợi ý ⌵</option>
                            <option value="3000000">Dưới 3 triệu</option>
                            <option value="4000000">Dưới 4 triệu</option>
                            <option value="5000000">Dưới 5 triệu</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="text" inputmode="numeric" id="filter-price-min" name="min_price" data-field="range_price" oninput="debouncedFilterItems()" placeholder="0 đ" class="w-1/2 px-3 py-2 bg-[#0a0e17] border border-slate-800 rounded-xl text-slate-200 text-xs focus:border-emerald-500 focus:outline-none transition-all placeholder-slate-500 font-medium">
                        <span class="text-slate-600 text-sm font-bold">-</span>
                        <input type="text" inputmode="numeric" id="filter-price-max" name="max_price" data-field="range_price" oninput="debouncedFilterItems()" placeholder="10.000.000 đ" class="w-1/2 px-3 py-2 bg-[#0a0e17] border border-slate-800 rounded-xl text-slate-200 text-xs focus:border-emerald-500 focus:outline-none transition-all placeholder-slate-500 font-medium">
                    </div>
                    <!-- Lỗi viền đỏ và text lỗi chuẩn ERR_21_01 & ERR_21_03 -->
                    <div id="filter-price-error" class="hidden text-xs text-rose-400 font-bold mt-2 flex items-center gap-1.5 animate-shake">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400"></i>
                        <span id="filter-price-error-text">Giá tối thiểu không thể lớn hơn giá tối đa.</span>
                    </div>
                </div>

                <!-- Cột 2: DIỆN TÍCH (m²) - Spec ERR_21_02 & ERR_21_03 -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">DIỆN TÍCH (m²)</label>
                        <select id="filter-area-preset" onchange="handleAreaPresetChange(this.value)" class="px-2 py-1 bg-[#0a0e17] border border-slate-800 hover:border-slate-700 rounded-lg text-slate-300 text-[11px] focus:border-emerald-500 focus:outline-none transition-colors">
                            <option value="all">Mức diện tích ⌵</option>
                            <option value="20">Dưới 20m²</option>
                            <option value="30">Dưới 30m²</option>
                            <option value="50">Dưới 50m²</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="text" inputmode="numeric" id="filter-area-min" name="min_area" data-field="range_area" oninput="debouncedFilterItems()" placeholder="0 m²" class="w-1/2 px-3 py-2 bg-[#0a0e17] border border-slate-800 rounded-xl text-slate-200 text-xs focus:border-emerald-500 focus:outline-none transition-all placeholder-slate-500 font-medium">
                        <span class="text-slate-600 text-sm font-bold">-</span>
                        <input type="text" inputmode="numeric" id="filter-area-max" name="max_area" data-field="range_area" oninput="debouncedFilterItems()" placeholder="100 m²" class="w-1/2 px-3 py-2 bg-[#0a0e17] border border-slate-800 rounded-xl text-slate-200 text-xs focus:border-emerald-500 focus:outline-none transition-all placeholder-slate-500 font-medium">
                    </div>
                    <!-- Lỗi viền đỏ diện tích ERR_21_02 & ERR_21_03 -->
                    <div id="filter-area-error" class="hidden text-xs text-rose-400 font-bold mt-2 flex items-center gap-1.5 animate-shake">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400"></i>
                        <span id="filter-area-error-text">Diện tích tối thiểu không thể lớn hơn diện tích tối đa.</span>
                    </div>
                </div>
                
                <!-- Cột 3: LOẠI PHÒNG & ĐIỂM ĐÁNH GIÁ - Spec: room_type -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">LOẠI PHÒNG & ĐÁNH GIÁ</label>
                    <div class="flex items-center gap-2">
                        <select id="filter-room-type" name="room_type" onchange="filterItems()" class="w-1/2 px-2.5 py-2 bg-[#0a0e17] border border-slate-800 hover:border-slate-700 rounded-xl text-slate-200 text-xs focus:border-emerald-500 focus:outline-none transition-colors font-medium">
                            <option value="">Tất cả loại phòng</option>
                            <option value="Studio">Studio</option>
                            <option value="KTX">KTX</option>
                            <option value="Phòng trọ giá rẻ">Phòng giá rẻ</option>
                            <option value="Chung cư mini">Chung cư mini</option>
                        </select>
                        <select id="filter-rating" onchange="filterItems()" class="w-1/2 px-2.5 py-2 bg-[#0a0e17] border border-slate-800 hover:border-slate-700 rounded-xl text-slate-200 text-xs focus:border-emerald-500 focus:outline-none transition-colors font-medium">
                            <option value="all">Mọi điểm số</option>
                            <option value="4.5">Từ 4.5⭐</option>
                            <option value="4.0">Từ 4.0⭐</option>
                            <option value="3.5">Từ 3.5⭐</option>
                        </select>
                    </div>
                </div>

                <!-- Cột 4: KHOẢNG CÁCH ĐẾN TRƯỜNG/TIỆN ÍCH -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">KHOẢNG CÁCH (0 - 3KM)</label>
                    <div class="slider-container relative mt-1 mb-1 px-1">
                        <input 
                            type="range" 
                            id="distance-slider" 
                            min="0" 
                            max="3" 
                            step="0.1" 
                            value="3.0" 
                            oninput="updateDistanceSlider(this.value)"
                            class="custom-range-slider"
                            style="--range-progress: 100%;"
                        >
                        <!-- 4 Mốc km -->
                        <div class="slider-ticks flex justify-between px-0.5 mt-1 text-[11px] text-slate-400 font-medium select-none">
                            <span class="tick-mark cursor-pointer hover:text-white transition-colors" onclick="setSliderValue(0)" data-value="0">0km</span>
                            <span class="tick-mark cursor-pointer hover:text-white transition-colors" onclick="setSliderValue(1)" data-value="1">1km</span>
                            <span class="tick-mark cursor-pointer hover:text-white transition-colors" onclick="setSliderValue(2)" data-value="2">2km</span>
                            <span class="tick-mark cursor-pointer hover:text-white transition-colors" onclick="setSliderValue(3)" data-value="3">3km</span>
                        </div>
                    </div>
                    <!-- Feedback Box -->
                    <div class="feedback-box mt-2 px-2.5 py-1.5 bg-[#0a0e17] border border-slate-800 rounded-xl flex items-center gap-1.5 text-slate-300 text-[11px]">
                        <i class="fa-solid fa-location-dot text-slate-400 text-xs shrink-0"></i>
                        <span id="distance-feedback" class="text-[11px] text-slate-300 font-medium truncate">
                            Dưới 3km từ ĐH Bách Khoa
                        </span>
                    </div>
                </div>
            </div>

            <!-- Hàng 2: TIỆN ÍCH ĐẶC BIỆT & SẮP XẾP -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-4 border-t border-slate-800/60">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">TIỆN ÍCH</label>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <label class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#0a0e17] border border-slate-800 text-xs font-semibold text-slate-300 cursor-pointer hover:border-slate-700 transition-colors">
                            <input type="checkbox" id="tag-pets" name="chkAmenities[]" value="pets" onchange="syncFromCheckbox('pets')" class="rounded border-slate-700 text-emerald-500 focus:ring-0 focus:ring-offset-0 bg-slate-900 w-4 h-4">
                            <span>Nuôi thú cưng</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#0a0e17] border border-slate-800 text-xs font-semibold text-slate-300 cursor-pointer hover:border-slate-700 transition-colors">
                            <input type="checkbox" id="tag-loft" name="chkAmenities[]" value="loft" onchange="filterItems()" class="rounded border-slate-700 text-emerald-500 focus:ring-0 focus:ring-offset-0 bg-slate-900 w-4 h-4">
                            <span>Có gác lửng</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#0a0e17] border border-slate-800 text-xs font-semibold text-slate-300 cursor-pointer hover:border-slate-700 transition-colors">
                            <input type="checkbox" id="tag-balcony" name="chkAmenities[]" value="balcony" onchange="syncFromCheckbox('balcony')" class="rounded border-slate-700 text-emerald-500 focus:ring-0 focus:ring-offset-0 bg-slate-900 w-4 h-4">
                            <span>Ban công</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#0a0e17] border border-slate-800 text-xs font-semibold text-slate-300 cursor-pointer hover:border-slate-700 transition-colors">
                            <input type="checkbox" id="tag-wc" name="chkAmenities[]" value="wc" onchange="syncFromCheckbox('wc')" class="rounded border-slate-700 text-emerald-500 focus:ring-0 focus:ring-offset-0 bg-slate-900 w-4 h-4">
                            <span>WC khép kín</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#0a0e17] border border-slate-800 text-xs font-semibold text-slate-300 cursor-pointer hover:border-slate-700 transition-colors">
                            <input type="checkbox" id="tag-ac" name="chkAmenities[]" value="air_conditioner" onchange="filterItems()" class="rounded border-slate-700 text-emerald-500 focus:ring-0 focus:ring-offset-0 bg-slate-900 w-4 h-4">
                            <span>Máy lạnh</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#0a0e17] border border-slate-800 text-xs font-semibold text-slate-300 cursor-pointer hover:border-slate-700 transition-colors">
                            <input type="checkbox" id="tag-washer" name="chkAmenities[]" value="washing_machine" onchange="filterItems()" class="rounded border-slate-700 text-emerald-500 focus:ring-0 focus:ring-offset-0 bg-slate-900 w-4 h-4">
                            <span>Máy giặt</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#0a0e17] border border-slate-800 text-xs font-semibold text-slate-300 cursor-pointer hover:border-slate-700 transition-colors">
                            <input type="checkbox" id="tag-fridge" name="chkAmenities[]" value="fridge" onchange="filterItems()" class="rounded border-slate-700 text-emerald-500 focus:ring-0 focus:ring-offset-0 bg-slate-900 w-4 h-4">
                            <span>Tủ lạnh</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#0a0e17] border border-slate-800 text-xs font-semibold text-slate-300 cursor-pointer hover:border-slate-700 transition-colors">
                            <input type="checkbox" id="tag-wifi" name="chkAmenities[]" value="wifi" onchange="filterItems()" class="rounded border-slate-700 text-emerald-500 focus:ring-0 focus:ring-offset-0 bg-slate-900 w-4 h-4">
                            <span>Wifi</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#0a0e17] border border-slate-800 text-xs font-semibold text-slate-300 cursor-pointer hover:border-slate-700 transition-colors">
                            <input type="checkbox" id="tag-elevator" name="chkAmenities[]" value="elevator" onchange="filterItems()" class="rounded border-slate-700 text-emerald-500 focus:ring-0 focus:ring-offset-0 bg-slate-900 w-4 h-4">
                            <span>Thang máy</span>
                        </label>
                    </div>
                </div>

                <!-- Dropdown sắp xếp góc phải theo Spec STT 3 -->
                <div class="shrink-0 self-start md:self-auto">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2" for="sort_by">SẮP XẾP THEO</label>
                    <select id="sort_by" onchange="filterItems()" class="px-3 py-1.5 bg-[#0a0e17] border border-slate-800 hover:border-slate-700 rounded-xl text-slate-200 text-xs focus:border-emerald-500 focus:outline-none transition-colors font-medium">
                        <option value="default">Mặc định</option>
                        <option value="price_asc">Giá tăng dần</option>
                        <option value="price_desc">Giá giảm dần</option>
                        <option value="newest">Mới nhất</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN WORKSPACE -->
    <main class="container mx-auto px-6 py-6 max-w-6xl flex-grow flex flex-col relative z-10">
        <!-- Interactive Map Pane (Left 50% in map mode) - STT 1: map_view (Bản đồ số) -->
        <div class="renty-split-left relative h-full rounded-3xl overflow-hidden border border-slate-800/80 shadow-2xl" data-field-name="map_view">
            <!-- Map Viewport (Fills 100% of left pane) -->
            <div id="renty-interactive-map" name="map_view" data-field-name="map_view" class="w-full h-full"></div>

            <!-- Toast Thông báo Tọa độ ngoài phạm vi [ERR_05_03] -->
            <div id="gm-coord-error-toast" class="hidden absolute top-24 left-1/2 -translate-x-1/2 z-[2000] px-4 py-2 rounded-2xl bg-slate-950/95 border-2 border-rose-500 text-rose-200 text-xs font-bold shadow-2xl flex items-center gap-2.5 transition-all animate-bounce">
                <i class="fa-solid fa-circle-exclamation text-rose-400 text-base"></i>
                <span>Tọa độ tìm kiếm nằm ngoài phạm vi bản đồ hỗ trợ.</span>
            </div>

            <!-- Banner Lỗi Tiles Server [ERR_05_04] -->
            <div id="gm-tiles-error-banner" class="hidden absolute top-28 left-4 right-4 z-[2000] p-4 rounded-2xl bg-slate-950/95 border-2 border-rose-500 shadow-2xl text-slate-100 flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-extrabold text-white">Lỗi kết nối dịch vụ bản đồ số</span>
                        </div>
                        <div class="text-[11px] text-rose-200 mt-0.5">Không thể tải lớp bản đồ số (Tiles Server timeout).</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="/renty?view=grid" class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-table-cells"></i> Chuyển sang chế độ danh sách
                    </a>
                    <button type="button" onclick="window.location.reload()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition-all">
                        <i class="fa-solid fa-rotate-right"></i> Thử lại
                    </button>
                </div>
            </div>

            <!-- Floating Google Maps Pro HUD Control Bar (Hiệu ứng Liquid Glass lỏng trong suốt, Search Box giữ bình thường) -->
            <div class="gm-pro-hud absolute top-3 left-3 right-3 z-[1000] rounded-2xl p-2.5 flex flex-col gap-2 transition-all duration-300">
                
                <!-- Row 1: City Tabs + Smart Search Engine + Compact Map Controls (Khóa cứng 1 dòng, không bao giờ nhảy xuống) -->
                <div class="flex items-center gap-2 w-full flex-nowrap">
                    <!-- City Switcher Tabs -->
                    <div class="gm-city-switcher flex items-center gap-1 bg-slate-900/95 p-0.5 rounded-xl border border-slate-800 text-[11px] font-bold shrink-0">
                        <button type="button" onclick="switchMapCity('all', this)" id="city-tab-all" class="gm-city-tab active px-2 py-1 rounded-lg bg-blue-600 text-white font-bold transition-all shadow-sm">
                            🌐 Toàn quốc
                        </button>
                        <button type="button" onclick="switchMapCity('hcm', this)" id="city-tab-hcm" class="gm-city-tab px-2 py-1 rounded-lg text-slate-300 hover:text-white font-semibold transition-all">
                            🏙️ TP.HCM
                        </button>
                        <button type="button" onclick="switchMapCity('hanoi', this)" id="city-tab-hanoi" class="gm-city-tab px-2 py-1 rounded-lg text-slate-300 hover:text-white font-semibold transition-all">
                            🏛️ Hà Nội
                        </button>
                    </div>

                    <!-- Live Search Engine Input (Rõ nét, màu bình thường đậm đặc) -->
                    <div class="relative flex-grow min-w-0" id="gm-search-engine-wrapper">
                        <form onsubmit="onMapSearchSubmit(event)" class="w-full flex items-center relative">
                            <input type="text" id="gm-search-input" autocomplete="off"
                                placeholder="Tìm mọi địa điểm du lịch, đường phố, trường học, trọ..." 
                                class="w-full pl-8 pr-12 py-1.5 rounded-xl bg-slate-900 border border-slate-700 text-xs text-white placeholder-slate-400 focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-400/40 transition-all font-medium truncate shadow-inner">
                            <i class="fa-solid fa-magnifying-glass absolute left-2.5 text-sky-400 text-[11px]"></i>
                            
                            <!-- Nút xóa nhanh nếu có text -->
                            <button type="button" id="gm-search-clear-btn" onclick="clearMapSearchInput()" class="hidden absolute right-6 w-4 h-4 rounded-full bg-slate-800 text-slate-300 hover:text-white flex items-center justify-center text-[9px] transition-all" title="Xóa">
                                <i class="fa-solid fa-xmark"></i>
                            </button>

                            <button type="submit" class="absolute right-1.5 w-4 h-4 rounded-md bg-sky-500 text-white flex items-center justify-center text-[9px] transition-all" title="Tìm">
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </form>

                        <!-- Dropdown Autocomplete: ĐẶC 100%, KHÔNG TRONG SUỐT, NỔI LÊN MỌI THỨ -->
                        <div id="gm-search-autocomplete" style="background-color: #0b0f19 !important; background: #0b0f19 !important; opacity: 1 !important; z-index: 999999 !important; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.95), 0 0 0 1.5px #38bdf8 !important;" class="hidden absolute left-0 right-0 top-full mt-2 border border-sky-500/50 rounded-2xl overflow-hidden max-h-84 overflow-y-auto text-xs divide-y divide-slate-800">
                            <!-- Kết quả tìm kiếm tự động đổ vào đây -->
                        </div>
                    </div>

                    <!-- Right Controls: Layer + GPS + Compass + Collapse -->
                    <div class="flex items-center gap-1 shrink-0">
                        <!-- Layer Buttons -->
                        <div class="gm-layer-container flex items-center gap-0.5 bg-slate-900/95 p-0.5 rounded-xl border border-slate-800 text-[10px] font-bold">
                            <button type="button" onclick="setRentyMapLayer('roadmap')" id="gm-layer-roadmap" class="w-6 h-6 rounded-lg bg-blue-600 text-white font-bold transition-all flex items-center justify-center" title="Đường phố">
                                <i class="fa-solid fa-map"></i>
                            </button>
                            <button type="button" onclick="setRentyMapLayer('satellite')" id="gm-layer-satellite" class="w-6 h-6 rounded-lg text-slate-400 hover:text-slate-200 transition-all flex items-center justify-center" title="Vệ tinh">
                                <i class="fa-solid fa-satellite"></i>
                            </button>
                            <button type="button" onclick="setRentyMapLayer('terrain')" id="gm-layer-terrain" class="w-6 h-6 rounded-lg text-slate-400 hover:text-slate-200 transition-all flex items-center justify-center" title="Địa hình">
                                <i class="fa-solid fa-mountain"></i>
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <button type="button" onclick="locateUserRentyMap()" title="Định vị GPS" class="gm-action-btn w-6 h-6 rounded-xl bg-slate-900/95 border border-slate-800 hover:border-emerald-500/50 text-slate-300 hover:text-emerald-400 flex items-center justify-center text-[10px] transition-all">
                            <i class="fa-solid fa-crosshairs"></i>
                        </button>
                        <button type="button" onclick="resetRentyMapView()" title="Về trung tâm" class="gm-action-btn w-6 h-6 rounded-xl bg-slate-900/95 border border-slate-800 hover:border-blue-500/50 text-slate-300 hover:text-blue-400 flex items-center justify-center text-[10px] transition-all">
                            <i class="fa-solid fa-compass"></i>
                        </button>
                        <button type="button" onclick="toggleRentyMapFullscreen()" id="gm-fullscreen-btn" title="Toàn màn hình (Esc)" class="gm-action-btn w-6 h-6 rounded-xl bg-slate-900/95 border border-slate-800 hover:border-amber-500/50 text-slate-300 hover:text-amber-400 flex items-center justify-center text-[10px] transition-all">
                            <i class="fa-solid fa-expand" id="gm-fullscreen-icon"></i>
                        </button>
                        <button type="button" onclick="toggleGnHudDetails()" id="gm-hud-toggle-btn" title="Tiện ích & Lọc" class="gm-action-btn w-6 h-6 rounded-xl bg-slate-900/95 border border-slate-800 hover:border-teal-500/50 text-slate-300 hover:text-teal-400 flex items-center justify-center text-[10px] transition-all">
                            <i class="fa-solid fa-chevron-down transition-transform duration-200" id="gm-hud-toggle-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Row 2: Campus Quick Chips + Navigation Arrows + Radius Selector -->
                <div class="flex items-center justify-between gap-1.5 pt-1 border-t border-white/10">
                    <span class="gm-campus-label text-[10px] font-bold text-sky-400 flex items-center gap-1 shrink-0 mr-0.5">
                        <i class="fa-solid fa-graduation-cap"></i> Trường:
                    </span>

                    <!-- Nút trượt trái -->
                    <button type="button" onclick="scrollCampusBar(-160)" class="gm-scroll-btn w-5 h-5 rounded-md bg-slate-900/90 hover:bg-slate-800 text-slate-300 hover:text-white flex items-center justify-center text-[9px] shrink-0 transition-all border border-slate-700/60 shadow-sm" title="Cuộn sang trái">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <!-- Campus Quick Chips (Lướt ngang, Kéo chuột hoặc lăn chuột mượt mà) -->
                    <div class="gm-campus-bar flex items-center gap-1.5 overflow-x-auto py-0.5 no-scrollbar flex-grow min-w-0 select-none cursor-grab" style="scrollbar-width: none; -ms-overflow-style: none;">
                        <button type="button" onclick="selectCampusFilter('all', this)" id="campus-chip-all" class="gm-campus-chip active px-2.5 py-0.5 rounded-lg bg-blue-600 text-white text-[11px] font-bold transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🌐 Tất cả</span>
                        </button>
                        <button type="button" onclick="selectCampusFilter('hcmut', this)" class="gm-campus-chip px-2.5 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🎓 Bách Khoa TP.HCM</span>
                        </button>
                        <button type="button" onclick="selectCampusFilter('vnuhcm', this)" class="gm-campus-chip px-2.5 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🎓 Làng ĐH Thủ Đức</span>
                        </button>
                        <button type="button" onclick="selectCampusFilter('spkt', this)" class="gm-campus-chip px-2.5 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🎓 ĐH SPKT</span>
                        </button>
                        <button type="button" onclick="selectCampusFilter('ueh', this)" class="gm-campus-chip px-2.5 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🎓 ĐH Kinh Tế UEH</span>
                        </button>
                        <button type="button" onclick="selectCampusFilter('hutech', this)" class="gm-campus-chip px-2.5 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🎓 ĐH HUTECH</span>
                        </button>
                        <button type="button" onclick="selectCampusFilter('tdtu', this)" class="gm-campus-chip px-2.5 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🎓 ĐH Tôn Đức Thắng</span>
                        </button>
                        <button type="button" onclick="selectCampusFilter('rmit', this)" class="gm-campus-chip px-2.5 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🎓 ĐH RMIT Q.7</span>
                        </button>
                        <button type="button" onclick="selectCampusFilter('iuh', this)" class="gm-campus-chip px-2.5 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🎓 ĐH Công Nghiệp IUH</span>
                        </button>
                        <button type="button" onclick="selectCampusFilter('vanlang', this)" class="gm-campus-chip px-2.5 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🎓 ĐH Văn Lang</span>
                        </button>
                        <button type="button" onclick="selectCampusFilter('vnu_hn', this)" class="gm-campus-chip px-2.5 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🎓 ĐHQG Hà Nội</span>
                        </button>
                        <button type="button" onclick="selectCampusFilter('hust', this)" class="gm-campus-chip px-2.5 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🎓 Bách Khoa HN</span>
                        </button>
                        <button type="button" onclick="selectCampusFilter('neu', this)" class="gm-campus-chip px-2.5 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🎓 ĐH KT Quốc Dân</span>
                        </button>
                        <button type="button" onclick="selectCampusFilter('ftu', this)" class="gm-campus-chip px-2.5 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🎓 ĐH Ngoại Thương</span>
                        </button>
                        <button type="button" onclick="selectCampusFilter('ptit', this)" class="gm-campus-chip px-2.5 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                            <span>🎓 Học viện PTIT</span>
                        </button>
                    </div>

                    <!-- Nút trượt phải -->
                    <button type="button" onclick="scrollCampusBar(160)" class="gm-scroll-btn w-5 h-5 rounded-md bg-slate-900/90 hover:bg-slate-800 text-slate-300 hover:text-white flex items-center justify-center text-[9px] shrink-0 transition-all border border-slate-700/60 shadow-sm" title="Cuộn sang phải">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                    <!-- Commute Radius Slider & Selector (STT 3: range_radius - Bán kính quét) -->
                    <div class="relative flex items-center gap-1.5 bg-slate-900/95 px-2 py-0.5 rounded-xl border border-slate-800 text-[10px] font-bold shrink-0 shadow-sm" id="radius_slider_wrapper">
                        <label for="range_radius" class="text-slate-300 font-bold shrink-0 flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-arrows-to-circle text-emerald-400"></i> Bán kính:
                        </label>
                        <input type="range" 
                               id="range_radius" 
                               name="range_radius" 
                               min="0" 
                               max="10" 
                               step="0.5" 
                               value="3.5" 
                               placeholder="5 km"
                               oninput="onRadiusSliderChange(this.value)" 
                               onchange="onRadiusSliderChange(this.value)" 
                               class="w-16 sm:w-20 h-1.5 bg-slate-800 rounded-lg appearance-none cursor-pointer accent-emerald-500 transition-all border border-transparent">
                        <span id="range_radius_val" class="text-emerald-400 font-extrabold min-w-[28px] text-center">3.5km</span>

                        <!-- Preset Radius Chips -->
                        <div class="hidden sm:flex items-center gap-0.5 border-l border-slate-700/60 pl-1">
                            <button type="button" onclick="applyPresetRadius(1.5)" id="radius-chip-1500" class="gm-radius-chip px-1.5 py-0.2 rounded text-[9px] text-slate-400 hover:text-white transition-all">1.5km</button>
                            <button type="button" onclick="applyPresetRadius(3.5)" id="radius-chip-3500" class="gm-radius-chip active px-1.5 py-0.2 rounded text-[9px] bg-emerald-600 text-white font-bold transition-all shadow-sm">3.5km</button>
                            <button type="button" onclick="applyPresetRadius(5.0)" id="radius-chip-5000" class="gm-radius-chip px-1.5 py-0.2 rounded text-[9px] text-slate-400 hover:text-white transition-all">5km</button>
                        </div>

                        <!-- Khung thông báo lỗi viền đỏ theo kịch bản DoD: Bán kính tối thiểu phải từ 1km -->
                        <div id="range_radius_error" class="hidden absolute -bottom-7 right-0 z-50 px-2 py-0.5 rounded-lg bg-rose-950/95 border border-rose-500 text-rose-300 text-[10px] font-bold shadow-xl whitespace-nowrap animate-bounce flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-rose-400"></i> Bán kính tối thiểu phải từ 1km
                        </div>
                    </div>
                </div>

                <!-- Collapsible filter section (Mặc định ẩn gọn) -->
                <div id="gm-hud-collapsible" class="hidden flex flex-col gap-2 transition-all duration-300 pt-2 border-t border-slate-800/80">
                    <!-- Nearby Places Amenities Scanner -->
                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] uppercase font-bold text-slate-400 shrink-0 flex items-center gap-1.5">
                                <i class="fa-solid fa-layer-group text-sky-400"></i> Quét tiện ích:
                            </span>
                        </div>

                        <!-- Nearby Places Filter Chips (Đầy đủ 8 tiện ích thiết thực) -->
                        <div class="flex items-center gap-1.5 overflow-x-auto py-0.5 no-scrollbar flex-grow">
                            <button type="button" onclick="toggleNearbyPlace('supermarket', this)" class="gm-nearby-chip px-2.5 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 text-[10px] font-bold hover:text-white transition-all shrink-0">
                                🛒 Siêu thị & Tiện lợi
                            </button>
                            <button type="button" onclick="toggleNearbyPlace('hospital', this)" class="gm-nearby-chip px-2.5 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 text-[10px] font-bold hover:text-white transition-all shrink-0">
                                🏥 Bệnh viện & Nhà thuốc
                            </button>
                            <button type="button" onclick="toggleNearbyPlace('bus', this)" class="gm-nearby-chip px-2.5 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 text-[10px] font-bold hover:text-white transition-all shrink-0">
                                🚌 Xe buýt & Metro
                            </button>
                            <button type="button" onclick="toggleNearbyPlace('food', this)" class="gm-nearby-chip px-2.5 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 text-[10px] font-bold hover:text-white transition-all shrink-0">
                                🍜 Quán ăn sinh viên
                            </button>
                            <button type="button" onclick="toggleNearbyPlace('cafe', this)" class="gm-nearby-chip px-2.5 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 text-[10px] font-bold hover:text-white transition-all shrink-0">
                                ☕ Cà phê học bài
                            </button>
                            <button type="button" onclick="toggleNearbyPlace('atm', this)" class="gm-nearby-chip px-2.5 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 text-[10px] font-bold hover:text-white transition-all shrink-0">
                                🏧 Cây ATM & Ngân hàng
                            </button>
                            <button type="button" onclick="toggleNearbyPlace('laundry', this)" class="gm-nearby-chip px-2.5 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 text-[10px] font-bold hover:text-white transition-all shrink-0">
                                🧺 Giặt sấy 24/7
                            </button>
                            <button type="button" onclick="toggleNearbyPlace('gym', this)" class="gm-nearby-chip px-2.5 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 text-[10px] font-bold hover:text-white transition-all shrink-0">
                                🏋️ Phòng Gym
                            </button>
                        </div>
                    </div>

                    <!-- Distance Matrix Result Banner (shown when university selected) -->
                    <div id="gm-commute-box" class="hidden p-2 rounded-xl bg-blue-950/60 border border-blue-500/30"></div>
                </div>
            </div>
        </div>

        <!-- Room Cards List Pane (Right 50% in map mode) [UI Element: room_list_panel] -->
        <div class="renty-split-right" name="room_list_panel" data-field-name="room_list_panel" id="room_list_panel">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b border-slate-900 pb-4 renty-split-header">
            <div class="flex items-center gap-3.5 flex-wrap">
                <h2 class="text-lg font-bold text-slate-200" id="results-count">Tìm thấy {{ count($rooms) }} phòng</h2>
                @if(auth()->check() && auth()->user()->canAccessLandlordDashboard() && auth()->user()->tenant_id && !auth()->user()->isAdmin())
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 flex items-center gap-1.5">
                        <i class="fa-solid fa-building-user text-indigo-400"></i> Cơ sở: {{ auth()->user()->tenant?->name ?? 'Khu vực quản lý' }}
                    </span>
                    @if(request()->boolean('all_tenants'))
                        <a href="{{ route('renty.user') }}" class="text-xs text-indigo-400 hover:text-indigo-300 hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-filter"></i> Chỉ xem trọ của tôi
                        </a>
                    @else
                        <a href="{{ route('renty.user', ['all_tenants' => 1]) }}" class="text-xs text-slate-400 hover:text-slate-200 hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-globe"></i> Xem tất cả trọ hệ thống
                        </a>
                    @endif
                @endif
                <div id="live-activity-pill" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-[10px] font-extrabold text-emerald-400 select-none shadow-sm transition-all duration-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span id="live-activity-text">24 người đang xem khu vực Đống Đa</span>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 self-start sm:self-auto">
                <!-- View Mode Toggle (Map vs Grid) -->
                <div class="view-mode-toggle">
                    <button type="button" onclick="setViewMode('map')" id="view-mode-map-btn">
                        <i class="fa-solid fa-map-location-dot"></i> <span class="hidden sm:inline">Bản đồ</span>
                    </button>
                    <button type="button" onclick="setViewMode('grid')" id="view-mode-grid-btn" class="active">
                        <i class="fa-solid fa-table-cells-large"></i> <span class="hidden sm:inline">Lưới</span>
                    </button>
                </div>

                <!-- Search Engine Selector (AI Hybrid vs Laravel Scout) -->
                <div class="flex items-center gap-2 bg-slate-950/45 px-3 py-1.5 rounded-2xl border border-slate-900/60 backdrop-blur-sm">
                    <span class="text-[11px] text-slate-400 font-bold flex items-center gap-1">
                        <i class="fa-solid fa-bolt text-amber-400"></i> <span class="hidden md:inline">Engine:</span>
                    </span>
                    <select id="search-engine-select" onchange="filterItems()" class="bg-slate-900/90 text-xs text-emerald-400 border border-slate-800 hover:border-emerald-500/40 rounded-xl px-2.5 py-1 outline-none font-semibold cursor-pointer transition-colors">
                        <option value="hybrid">AI Hybrid (RRF)</option>
                        <option value="scout">Laravel Scout</option>
                    </select>
                </div>

                <div class="flex items-center gap-3 bg-slate-950/45 px-4 py-2 rounded-2xl border border-slate-900/60 backdrop-blur-sm">
                    <label for="hide-rented-toggle" class="text-xs text-slate-400 font-bold select-none cursor-pointer hover:text-slate-200 transition-colors">Ẩn phòng đã thuê</label>
                    <label class="ios-switch cursor-pointer">
                        <input type="checkbox" id="hide-rented-toggle" onchange="filterItems()">
                        <span class="ios-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- SMART SEARCH DID YOU MEAN BANNER -->
        <div id="smart-search-did-you-mean-banner" class="hidden mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-between gap-3 text-xs backdrop-blur-md">
            <div class="flex items-center gap-2.5 text-emerald-300">
                <i class="fa-solid fa-wand-magic-sparkles text-sm text-emerald-400 animate-pulse"></i>
                <span>Có phải bạn muốn tìm: <strong id="smart-search-did-you-mean-text" onclick="applyDidYouMeanSearch()" class="text-white cursor-pointer underline underline-offset-4 font-bold hover:text-emerald-300 transition-colors"></strong>?</span>
            </div>
            <button type="button" onclick="applyDidYouMeanSearch()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-[11px] shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-check"></i> Áp dụng ngay
            </button>
        </div>

        <!-- NETWORK DISCONNECT ERROR BANNER (FEAT_04_LANDING - ERR_04_04) -->
        <div id="renty-network-error-banner" class="hidden mb-6 p-4 rounded-2xl bg-rose-950/80 border border-rose-500/40 text-xs text-rose-200 flex items-center justify-between gap-4 backdrop-blur-md animate-shake shadow-xl">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-wifi"></i>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <strong class="text-white text-xs">Mất kết nối máy chủ dữ liệu phòng</strong>
                    </div>
                    <p class="text-[11px] text-rose-300 mt-0.5">Không thể nạp danh sách phòng lúc này. Vui lòng kiểm tra lại kết nối.</p>
                </div>
            </div>
            <button type="button" onclick="window.location.reload()" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-extrabold text-xs shadow-md transition-all shrink-0 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-rotate-right"></i> Bấm để tải lại
            </button>
        </div>

        <!-- SMART SEARCH EMPTY STATE (FEAT_04_LANDING - ERR_04_03) -->
        <div id="smart-search-empty-state" class="hidden mb-16 py-16 px-4 text-center rounded-3xl bg-slate-900/30 border border-slate-800/60 backdrop-blur-md">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-center text-slate-500 text-2xl shadow-xl">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <h3 class="text-base font-bold text-slate-200 mb-1" id="smart-search-empty-title">Không tìm thấy phòng trọ nào khớp với từ khóa tìm kiếm của bạn.</h3>
            <p class="text-[11px] text-slate-400 font-medium mb-3 tracking-wide flex items-center justify-center gap-1.5" id="smart-search-empty-code">
                <span id="smart-search-empty-code-text">Không tìm thấy kết quả phù hợp với yêu cầu tìm kiếm</span>
            </p>
            <!-- Gợi ý từ gõ sai ngay trong khung báo lỗi Empty State -->
            <div id="smart-search-empty-did-you-mean" class="hidden mb-4 p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-xs text-emerald-300 inline-flex items-center justify-center gap-2 max-w-md mx-auto animate-fade-in">
                <i class="fa-solid fa-wand-magic-sparkles text-amber-400 shrink-0"></i>
                <span>Có phải bạn muốn tìm: <strong id="smart-search-empty-did-you-mean-text" onclick="applyDidYouMeanSearch()" class="cursor-pointer underline underline-offset-2 font-bold text-white hover:text-emerald-300"></strong>?</span>
                <button type="button" onclick="applyDidYouMeanSearch()" class="ml-2 px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[10px] font-bold shadow-md transition-all shrink-0">
                    <i class="fa-solid fa-magnifying-glass text-[9px] mr-1"></i>Tìm ngay
                </button>
            </div>
            <p class="text-xs text-slate-400 max-w-md mx-auto mb-6 leading-relaxed">Thử nới rộng khoảng giá, bỏ bớt tiêu chí tiện ích hoặc tìm kiếm theo khu vực lân cận xem sao nhé.</p>
            <div class="flex items-center justify-center gap-3">
                <button type="button" onclick="handleExploreClick(event)" id="btnExploreEmpty" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-extrabold rounded-xl transition-all shadow-lg shadow-emerald-600/25 inline-flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-compass"></i> Khám phá
                </button>
                <button type="button" onclick="resetAllFilters()" id="btnResetFilterEmpty" class="px-4 py-2.5 bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white text-xs font-bold rounded-xl transition-all inline-flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-rotate-left"></i> Xóa bộ lọc
                </button>
            </div>
        </div>

        <!-- Card Grid: Danh Sách Phòng (FEAT_04_LANDING - STT 2: room_cards) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16" id="rooms-grid" name="room_cards" data-field-name="room_cards">
            @foreach($rooms as $room)
                @include('rentry.partials.room_card', ['room' => $room])
            @endforeach
        </div>

        <!-- Pagination Controls -->
        <div id="renty-pagination" class="flex justify-center items-center gap-2 mb-12 flex-wrap">
            @php
                $totalRoomsCount = count($rooms);
                $totalPagesSSR = (int) ceil($totalRoomsCount / 9);
            @endphp
            @if($totalPagesSSR > 1)
                <button type="button" 
                        onclick="changeRentyPage(1)" 
                        class="px-4 py-2 rounded-xl bg-slate-900/40 border border-slate-800/85 hover:border-emerald-500/40 hover:text-emerald-400 transition-all font-bold text-xs flex items-center justify-center gap-1.5 opacity-40 cursor-not-allowed" 
                        disabled>
                    <i class="fa-solid fa-chevron-left text-[10px]"></i> Trước
                </button>
                @for($p = 1; $p <= min(7, $totalPagesSSR); $p++)
                    <button type="button" 
                            onclick="changeRentyPage({{ $p }})" 
                            class="w-9 h-9 rounded-xl font-extrabold text-xs transition-all border {{ $p === 1 ? 'bg-gradient-to-tr from-emerald-600 to-teal-500 text-white border-transparent shadow-lg shadow-emerald-500/20' : 'bg-slate-900/40 border-slate-800/80 text-slate-400 hover:border-emerald-500/30 hover:text-slate-200' }}">
                        {{ $p }}
                    </button>
                @endfor
                @if($totalPagesSSR > 7)
                    <div class="relative inline-block pagination-ellipsis-container">
                        <button type="button" onclick="makePaginationInput(this, {{ $totalPagesSSR }})" class="w-9 h-9 rounded-xl font-extrabold text-xs transition-all border bg-slate-900/40 border-slate-800/80 text-slate-400 hover:border-emerald-500/30 hover:text-slate-200 flex items-center justify-center cursor-pointer" title="Click để nhập số trang nhanh">
                            ...
                        </button>
                    </div>
                    <button type="button" onclick="changeRentyPage({{ $totalPagesSSR }})" class="w-9 h-9 rounded-xl font-extrabold text-xs transition-all border bg-slate-900/40 border-slate-800/80 text-slate-400 hover:border-emerald-500/30 hover:text-slate-200">
                        {{ $totalPagesSSR }}
                    </button>
                @endif
                <button type="button" 
                        onclick="changeRentyPage(2)" 
                        class="px-4 py-2 rounded-xl bg-slate-900/40 border border-slate-800/85 hover:border-emerald-500/40 hover:text-emerald-400 transition-all font-bold text-xs flex items-center justify-center gap-1.5">
                    Sau <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
            @endif
        </div>

        <!-- FOOTER -->
        @include('footer.footer')
        </div> <!-- End of .renty-split-right -->
    </main>

    <!-- QUICK ROOM PREVIEW -->
    <div id="quick-room-preview" class="quick-preview-overlay hidden" aria-hidden="true">
        <div class="quick-preview-panel" role="dialog" aria-modal="true" aria-labelledby="quick-preview-title">
            <button type="button" onclick="closeQuickRoomPreview()" class="quick-preview-close" aria-label="Đóng xem nhanh">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="quick-preview-media">
                <img id="quick-preview-image" src="" alt="Ảnh phòng xem nhanh" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                <span id="quick-preview-media-label">Ảnh thực tế</span>
            </div>

            <div class="quick-preview-content">
                <span class="quick-preview-brand">Renty Review</span>
                <h2 id="quick-preview-title">Phòng trọ</h2>
                <div class="quick-preview-price-row">
                    <strong id="quick-preview-price">0đ</strong>
                    <span id="quick-preview-rating">4.0 ⭐</span>
                </div>

                <div class="quick-preview-facts">
                    <div>
                        <i class="fa-solid fa-ruler-combined"></i>
                        <span id="quick-preview-area">25 m²</span>
                    </div>
                    <div>
                        <i class="fa-solid fa-location-dot"></i>
                        <span id="quick-preview-location">Cầu Giấy</span>
                    </div>
                    <div>
                        <i class="fa-solid fa-video"></i>
                        <span id="quick-preview-video">Có video tour</span>
                    </div>
                </div>

                <div class="quick-preview-tags" id="quick-preview-tags"></div>

                <div class="quick-preview-actions">
                    <a href="tel:0396519196" class="quick-preview-call">
                        <i class="fa-solid fa-phone"></i> Gọi ngay
                    </a>
                    <a href="#" id="quick-preview-detail" class="quick-preview-detail">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ROOM REPORT MODAL -->
    <div id="room-report-modal" class="fixed inset-0 z-[75] bg-[#02040a]/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-3xl bg-[#0a0f1d] border border-slate-800 p-5 md:p-6 shadow-2xl animate-fade-in">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-300 text-[10px] font-extrabold uppercase tracking-wider">
                        <i class="fa-solid fa-shield-halved"></i> Báo cáo phòng
                    </span>
                    <h2 class="mt-3 text-xl font-extrabold text-slate-100">Nghi ngờ thông tin không an toàn?</h2>
                    <p class="mt-1 text-xs text-slate-500" id="report-room-title">Renty Review sẽ kiểm tra báo cáo này.</p>
                </div>
                <button type="button" onclick="closeReportModal()" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="room-report-form" method="POST" action="" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tên của bạn</label>
                        <input type="text" name="reporter_name" value="{{ Auth::user()->name ?? '' }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none" placeholder="Có thể bỏ trống">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Số điện thoại</label>
                        <input type="tel" name="reporter_phone" value="{{ Auth::user()->phone ?? '' }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none" placeholder="Để liên hệ xác minh">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Lý do báo cáo</label>
                    <select name="reason" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none">
                        <option value="scam">Nghi lừa đảo / yêu cầu cọc bất thường</option>
                        <option value="fake_images">Ảnh không đúng thực tế</option>
                        <option value="wrong_price">Giá hoặc phí phát sinh sai</option>
                        <option value="unsafe">Vấn đề an toàn / an ninh</option>
                        <option value="spam">Tin đăng spam / trùng lặp</option>
                        <option value="other">Khác</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Mô tả chi tiết</label>
                    <textarea name="description" required minlength="10" rows="4" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-500 focus:outline-none" placeholder="Ví dụ: chủ trọ yêu cầu chuyển cọc trước khi xem phòng, ảnh không giống lúc đến xem..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeReportModal()" class="px-4 py-2 rounded-xl border border-slate-800 bg-slate-900 text-slate-300 text-xs font-bold">Hủy</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-extrabold shadow-lg shadow-rose-600/20">
                        <i class="fa-solid fa-flag mr-1.5"></i> Gửi báo cáo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ROOM DETAIL & REVIEWS MODAL -->
    <div id="room-detail-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="room-detail-panel w-full max-w-3xl bg-[#0a0f1d] border border-slate-800 rounded-3xl p-5 md:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto animate-fade-in">
            <button onclick="closeRoomDetailModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-xl font-bold mb-2 text-slate-100" id="detail-room-title">SmartRoom Cầu Giấy - Phòng 101</h2>
            <p class="text-xs text-slate-500 mb-6 flex items-center gap-1">
                <i class="fa-solid fa-location-dot text-slate-600"></i> <span id="detail-room-address">Số 12 Ngõ 105 Xuân Thủy, Cầu Giấy</span>
            </p>

            <div class="mb-6">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-images text-emerald-400"></i>
                            Nội dung hình ảnh
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1" id="detail-media-note">Ưu tiên ảnh thật theo từng góc, xem rõ trước khi liên hệ đặt lịch.</p>
                    </div>
                    <button type="button" onclick="openImageZoom()" class="shrink-0 px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-emerald-500/70 text-[11px] font-extrabold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-expand"></i> Zoom
                    </button>
                </div>
                <div class="relative h-72 rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 group">
                    <button type="button" onclick="openImageZoom()" class="absolute inset-0 z-10" aria-label="Phóng to ảnh phòng"></button>
                    <img id="detail-main-image" src="" alt="Ảnh phòng trọ" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.02]" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent pointer-events-none"></div>
                    <div class="absolute left-4 bottom-4">
                        <span class="px-3 py-1.5 rounded-lg bg-slate-950/70 border border-white/10 backdrop-blur text-xs font-bold text-slate-100">
                            <i class="fa-solid fa-camera text-emerald-300 mr-1.5"></i><span id="detail-image-count">Ảnh phòng</span>
                        </span>
                    </div>
                    <div class="absolute right-4 bottom-4 z-20">
                        <span id="detail-image-angle" class="px-3 py-1.5 rounded-lg bg-emerald-500/15 border border-emerald-400/20 backdrop-blur text-xs font-extrabold text-emerald-100">View toàn phòng</span>
                    </div>
                </div>
                <div id="detail-image-thumbs" class="mt-3 grid grid-cols-3 gap-3">
                </div>
                <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2 text-[10px] font-bold text-slate-400">
                    <span class="rounded-xl border border-slate-800 bg-slate-900/50 px-3 py-2"><i class="fa-solid fa-border-all text-emerald-400 mr-1.5"></i> Toàn phòng</span>
                    <span class="rounded-xl border border-slate-800 bg-slate-900/50 px-3 py-2"><i class="fa-solid fa-shower text-cyan-300 mr-1.5"></i> Nhà vệ sinh</span>
                    <span class="rounded-xl border border-slate-800 bg-slate-900/50 px-3 py-2"><i class="fa-solid fa-kitchen-set text-amber-300 mr-1.5"></i> Bếp</span>
                    <span class="rounded-xl border border-slate-800 bg-slate-900/50 px-3 py-2"><i class="fa-solid fa-sun text-sky-300 mr-1.5"></i> Ban công/cửa sổ</span>
                </div>
            </div>

            <div id="detail-video-section" class="hidden mb-6 p-4 rounded-2xl bg-[#070b13] border border-slate-800/70">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-video text-rose-300"></i>
                            Video / Virtual Tour
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1">Video quay nhanh từ cửa vào phòng giúp cảm nhận diện tích và luồng di chuyển thực tế.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-rose-500/10 border border-rose-500/20 text-[9px] font-extrabold text-rose-200 uppercase tracking-wider">Điểm cộng</span>
                </div>
                <video id="detail-room-video" class="w-full max-h-80 rounded-xl border border-slate-800 bg-black" controls preload="metadata"></video>
            </div>

            <div id="detail-video-empty" class="mb-6 p-4 rounded-2xl bg-slate-900/35 border border-dashed border-slate-800 text-xs text-slate-400 flex items-start gap-3">
                <i class="fa-solid fa-mobile-screen-button text-slate-500 mt-0.5"></i>
                <span>Chưa có video tour cho phòng này. Khi chủ trọ thêm video quay từ cửa vào phòng, mục này sẽ hiển thị ngay tại đây.</span>
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h3 class="text-sm font-extrabold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-table-cells-large text-emerald-400"></i>
                        Thông tin phòng
                    </h3>
                    <strong class="text-amber-400 text-xs" id="detail-room-rating">4.5 sao</strong>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="detail-info-tile">
                        <span><i class="fa-solid fa-tag"></i></span>
                        <small>Giá</small>
                        <strong id="detail-room-price">3.200.000đ/tháng</strong>
                    </div>
                    <div class="detail-info-tile">
                        <span><i class="fa-solid fa-ruler-combined"></i></span>
                        <small>Diện tích</small>
                        <strong id="detail-room-area">0 m²</strong>
                    </div>
                    <div class="detail-info-tile">
                        <span><i class="fa-solid fa-location-dot"></i></span>
                        <small>Địa chỉ</small>
                        <strong id="detail-room-area-name">Khu vực trung tâm</strong>
                    </div>
                    <div class="detail-info-tile">
                        <span><i class="fa-solid fa-shield-halved"></i></span>
                        <small>Cọc</small>
                        <strong>1 tháng</strong>
                    </div>
                    <div class="detail-info-tile">
                        <span><i class="fa-solid fa-bolt"></i></span>
                        <small>Điện</small>
                        <strong>3.5k/kWh</strong>
                    </div>
                    <div class="detail-info-tile">
                        <span><i class="fa-solid fa-droplet"></i></span>
                        <small>Nước</small>
                        <strong>20k/người</strong>
                    </div>
                </div>
            </div>

            <div id="detail-price-warning" class="hidden mb-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-100 text-xs leading-relaxed">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-300 mt-0.5"></i>
                    <div>
                        <strong class="block text-amber-200 mb-1" id="detail-price-warning-title">Cảnh báo giá</strong>
                        <span id="detail-price-warning-message"></span>
                    </div>
                </div>
            </div>

            <div class="mb-6 p-5 rounded-2xl bg-slate-900/45 border border-slate-800/60">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h3 class="text-sm font-extrabold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-align-left text-emerald-400"></i>
                        Mô tả chi tiết
                    </h3>
                    <div class="hidden">
                        <span id="detail-room-owner"></span>
                        <span id="detail-room-sec"></span>
                        <span id="detail-room-pets"></span>
                        <span id="detail-room-loft"></span>
                        <span id="detail-room-balcony"></span>
                    </div>
                </div>
                <p class="detail-description detail-description-clamped text-xs text-slate-300 leading-relaxed" id="detail-full-description"></p>
                <button type="button" onclick="toggleDetailDescription(this)" class="mt-3 text-xs font-extrabold text-emerald-400 hover:text-emerald-300">
                    Xem thêm
                </button>
            </div>

            <div class="mb-6 p-5 rounded-2xl bg-[#070b13] border border-slate-800/70">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-calculator text-cyan-300"></i>
                            Chi phí dự kiến khi vào ở
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1">Ước tính nhanh, có thể điều chỉnh theo số người và số xe.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                    <div class="cost-stepper">
                        <span>Số người ở</span>
                        <div>
                            <button type="button" id="cost-people-minus" onclick="updateMoveInCost('people', -1)">-</button>
                            <strong id="cost-people">2</strong>
                            <button type="button" id="cost-people-plus" onclick="updateMoveInCost('people', 1)">+</button>
                        </div>
                    </div>
                    <div class="cost-stepper">
                        <span>Số lượng xe</span>
                        <div>
                            <button type="button" onclick="updateMoveInCost('vehicles', -1)">-</button>
                            <strong id="cost-vehicles">1</strong>
                            <button type="button" onclick="updateMoveInCost('vehicles', 1)">+</button>
                        </div>
                    </div>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="cost-row"><span>Tiền phòng</span><strong id="cost-room">3.200.000đ</strong></div>
                    <div class="cost-row"><span>Cọc</span><strong id="cost-deposit">3.200.000đ</strong></div>
                    <div class="cost-row"><span>Điện dự kiến</span><strong id="cost-electric">350.000đ</strong></div>
                    <div class="cost-row"><span>Nước</span><strong id="cost-water">40.000đ</strong></div>
                    <div class="cost-row"><span>Phí dịch vụ / xe</span><strong id="cost-service">150.000đ</strong></div>
                    <div class="cost-row cost-total"><span>Tổng ban đầu</span><strong id="cost-total">6.940.000đ</strong></div>
                </div>
            </div>

            <!-- Contact landlord and request consultation -->
            <div class="border-t border-slate-800/60 pt-6 mb-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-300 mb-3"><i class="fa-solid fa-phone-volume text-emerald-400 mr-1.5"></i>Liên hệ Chủ nhà</h3>
                    <div class="p-4 rounded-xl bg-[#070b13] border border-slate-800/60 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400 font-bold">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-200 block">Chủ trọ SmartRoom</span>
                                <span class="text-[10px] text-slate-400">Hỗ trợ tư vấn, xem phòng trực tiếp</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="tel:0396519196" class="flex-1 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold text-center flex items-center justify-center gap-1.5 transition-all">
                                <i class="fa-solid fa-phone"></i> Gọi điện
                            </a>
                            <a href="https://zalo.me/0396519196" target="_blank" class="flex-1 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold text-center flex items-center justify-center gap-1.5 transition-all">
                                <i class="fa-solid fa-comments"></i> Chat Zalo
                            </a>
                        </div>
                    </div>

                    <!-- Consultation Form -->
                    <h3 class="text-sm font-bold text-slate-300 mt-6 mb-3"><i class="fa-solid fa-calendar-check text-indigo-400 mr-1.5"></i>Đăng ký Tư vấn & Xem phòng</h3>
                    <form action="{{ route('renty.contact_request.store', [], false) }}" method="POST" class="p-4 rounded-xl bg-[#070b13] border border-slate-800/60 space-y-3.5">
                        @csrf
                        <input type="hidden" name="room_id" id="contact-room-id" value="">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Họ tên của bạn</label>
                            @auth
                                <input type="text" name="name" value="{{ Auth::user()->name }}" readonly class="w-full px-3 py-2 rounded-lg bg-[#0c1222] border border-slate-850 text-slate-400 text-xs cursor-not-allowed">
                            @else
                                <input type="text" name="name" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Nguyễn Văn A">
                            @endauth
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Số điện thoại liên hệ</label>
                            @auth
                                <input type="tel" name="phone" value="{{ Auth::user()->phone }}" readonly class="w-full px-3 py-2 rounded-lg bg-[#0c1222] border border-slate-855 text-slate-400 text-xs cursor-not-allowed">
                            @else
                                <input type="tel" name="phone" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="0901234567">
                            @endauth
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Lời nhắn (Ví dụ: Thời gian muốn xem phòng...)</label>
                            <textarea name="message" rows="2" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none" placeholder="Tôi muốn xem phòng vào tối nay lúc 19h..."></textarea>
                        </div>
                        <button type="submit" class="w-full py-2.5 rounded-lg bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-bold transition-all shadow-md shadow-indigo-500/10">
                            Gửi Yêu Cầu Tư Vấn
                        </button>
                    </form>
                </div>

            </div>

            <!-- Reviews: summary, list, and write form in one place -->
            <div class="reviews-end-section border-t border-slate-800/60 pt-6">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h3 class="text-sm font-bold text-slate-300"><i class="fa-solid fa-comments text-amber-400 mr-1.5"></i>Đánh giá thực tế</h3>
                    <button type="button" id="review-summary-btn" onclick="loadReviewSummary(this)" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> AI tóm tắt
                    </button>
                </div>
                <div id="review-summary-box" class="hidden mb-3 rounded-xl bg-slate-900/70 border border-slate-800 p-3 text-xs text-slate-300"></div>
                <div class="p-4 rounded-xl bg-[#070b13] border border-slate-800/60 mb-4">
                    <div class="flex flex-col md:flex-row md:items-start gap-4 mb-4">
                        <div class="min-w-20">
                            <strong class="block text-4xl leading-none text-slate-100" id="review-average-score">4.8</strong>
                            <span class="block text-amber-400 text-xs mt-1" id="review-average-stars">★★★★★</span>
                            <small class="block text-[10px] text-slate-500 mt-1" id="review-count-label">24 đánh giá</small>
                        </div>
                        <div class="flex-1 space-y-2" id="review-score-bars"></div>
                    </div>
                    <a href="https://zalo.me/0396519196" target="_blank" class="w-full min-h-10 rounded-lg bg-blue-600/20 border border-blue-500/30 text-blue-200 text-xs font-extrabold flex items-center justify-center gap-2 hover:bg-blue-600/30 transition-all">
                        <i class="fa-solid fa-comments"></i> Chat Zalo để hỏi phòng
                    </a>
                </div>
                <div class="space-y-3" id="detail-reviews-container">
                    <!-- Dynamic list of reviews -->
                </div>
                <button type="button" id="show-all-reviews-btn" onclick="toggleAllReviews()" class="hidden mt-3 w-full min-h-11 rounded-xl border border-slate-800 bg-slate-900/50 text-slate-200 text-xs font-extrabold hover:border-slate-700">
                    Xem tất cả đánh giá
                </button>

                <div class="mt-6 pt-6 border-t border-slate-800/60">
                    <h3 class="text-sm font-bold text-slate-300 mb-3">Gửi đánh giá của bạn</h3>
                    <form id="write-review-form" action="" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Tên của bạn</label>
                                @auth
                                    <input type="text" name="author_name" value="{{ Auth::user()->name }}" readonly class="w-full px-4 py-2.5 rounded-xl bg-[#0a0e17] border border-slate-800 text-slate-400 text-xs cursor-not-allowed">
                                @else
                                    <input type="text" name="author_name" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none" placeholder="Nguyễn Văn A">
                                @endauth
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Điểm đánh giá (1-5)</label>
                                <select name="rating" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none">
                                    <option value="5">5 ⭐⭐⭐⭐⭐ (Xuất sắc)</option>
                                    <option value="4">4 ⭐⭐⭐⭐ (Tốt)</option>
                                    <option value="3">3 ⭐⭐⭐ (Trung bình)</option>
                                    <option value="2">2 ⭐⭐ (Kém)</option>
                                    <option value="1">1 ⭐ (Rất kém)</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Bình luận trải nghiệm thực tế</label>
                            <textarea name="comment" required rows="3" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-xs focus:border-emerald-500 focus:outline-none" placeholder="Hãy chia sẻ trải nghiệm về chủ nhà, an ninh, phòng ốc..."></textarea>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="closeRoomDetailModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 bg-transparent hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all">
                                Hủy bỏ
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-lg shadow-emerald-600/20 transition-all">
                                Gửi Đánh Giá
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="sticky-contact-bar">
                <div class="sticky-price">
                    <span>Giá thuê</span>
                    <strong id="sticky-room-price">3.2tr/tháng</strong>
                </div>
                <div class="sticky-actions">
                    <a href="tel:0396519196" class="sticky-call-button"><i class="fa-solid fa-phone"></i> Gọi điện</a>
                    <a href="https://zalo.me/0396519196" target="_blank" class="sticky-zalo-button"><i class="fa-solid fa-comments"></i> Chat Zalo</a>
                </div>
            </div>
        </div>
    </div>

    <!-- IMAGE ZOOM MODAL -->
    <div id="image-zoom-modal" class="fixed inset-0 z-[60] bg-[#02040a]/95 backdrop-blur-md hidden flex items-center justify-center p-4">
        <button type="button" onclick="closeImageZoom()" class="absolute top-5 right-5 w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-300 hover:text-white">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <button type="button" onclick="changeZoomImage(-1)" class="absolute left-4 md:left-8 w-11 h-11 rounded-full bg-slate-900/80 border border-slate-800 hover:border-emerald-500/70 text-slate-200 flex items-center justify-center">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <img id="zoom-main-image" src="" alt="Ảnh phòng phóng to" class="max-w-full max-h-[84vh] rounded-2xl object-contain border border-slate-800 shadow-2xl">
        <button type="button" onclick="changeZoomImage(1)" class="absolute right-4 md:right-8 w-11 h-11 rounded-full bg-slate-900/80 border border-slate-800 hover:border-emerald-500/70 text-slate-200 flex items-center justify-center">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
        <div class="absolute left-1/2 -translate-x-1/2 bottom-5 px-4 py-2 rounded-xl bg-slate-950/80 border border-white/10 text-xs font-bold text-slate-200">
            <span id="zoom-image-label">View toàn phòng</span>
            <span class="text-slate-500 mx-2">·</span>
            <span id="zoom-image-count">1/1</span>
        </div>
    </div>

    <!-- JS LOGIC EXTRACTED TO resources/js/rentry.js -->
    <script src="{{ asset('js/rentry.js') }}?v={{ time() }}"></script>

    <!-- HOT AREAS MODAL -->
    <div id="hot-areas-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-2xl bg-[#0a0f1d] border border-slate-800 rounded-3xl p-8 shadow-2xl relative max-h-[85vh] overflow-y-auto animate-fade-in">
            <button onclick="closeHotAreasModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-2 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-fire text-amber-500 animate-pulse"></i> Khu Vực Tìm Kiếm Hot
            </h2>
            <p class="text-xs text-slate-500 mb-6 font-semibold">Chọn nhanh khu vực để lọc danh sách phòng trọ tốt nhất quanh các khu đại học lớn.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Area 1 -->
                <div onclick="selectHotArea('Sư Phạm')" class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-all hover:-translate-y-0.5 group relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition-all"></div>
                    <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-colors">ĐH Sư Phạm Hà Nội</h3>
                    <p class="text-[11px] text-slate-505 mt-1 leading-relaxed">Khu vực Xuân Thủy đông đúc sinh viên, đi bộ ra trạm tàu điện Nhổn - Cát Linh rất gần.</p>
                    <div class="mt-4 flex justify-between items-center text-[10px] text-slate-400 font-bold">
                        <span>Bán kính: ~0.5 km</span>
                        <span class="text-emerald-400 flex items-center gap-1">Lọc nhanh <i class="fa-solid fa-arrow-right text-[8px]"></i></span>
                    </div>
                </div>
                
                <!-- Area 2 -->
                <div onclick="selectHotArea('Quốc Gia')" class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-all hover:-translate-y-0.5 group relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition-all"></div>
                    <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-colors">ĐH Quốc Gia Hà Nội</h3>
                    <p class="text-[11px] text-slate-505 mt-1 leading-relaxed">Trung tâm Cầu Giấy, các ngõ rộng thoáng khí, an ninh an toàn và nhiều phòng trọ cao cấp.</p>
                    <div class="mt-4 flex justify-between items-center text-[10px] text-slate-400 font-bold">
                        <span>Bán kính: ~0.8 km</span>
                        <span class="text-emerald-400 flex items-center gap-1">Lọc nhanh <i class="fa-solid fa-arrow-right text-[8px]"></i></span>
                    </div>
                </div>

                <!-- Area 3 -->
                <div onclick="selectHotArea('Cầu Giấy')" class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-all hover:-translate-y-0.5 group relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition-all"></div>
                    <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-colors">Quận Cầu Giấy</h3>
                    <p class="text-[11px] text-slate-505 mt-1 leading-relaxed">Khu vực trung tâm đắc địa tập trung nhiều trường đại học lớn, di chuyển cực kì nhanh chóng.</p>
                    <div class="mt-4 flex justify-between items-center text-[10px] text-slate-400 font-bold">
                        <span>Bán kính: Mọi khoảng cách</span>
                        <span class="text-emerald-400 flex items-center gap-1">Lọc nhanh <i class="fa-solid fa-arrow-right text-[8px]"></i></span>
                    </div>
                </div>

                <!-- Area 4 -->
                <div onclick="selectHotArea('Xuân Thủy')" class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-all hover:-translate-y-0.5 group relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition-all"></div>
                    <h3 class="font-bold text-slate-200 text-sm group-hover:text-emerald-400 transition-colors">Đường Xuân Thủy</h3>
                    <p class="text-[11px] text-slate-505 mt-1 leading-relaxed">Trục đường chính sầm uất, di chuyển thuận tiện tới các trường đại học xung quanh.</p>
                    <div class="mt-4 flex justify-between items-center text-[10px] text-slate-400 font-bold">
                        <span>Bán kính: ~0.2 km</span>
                        <span class="text-emerald-400 flex items-center gap-1">Lọc nhanh <i class="fa-solid fa-arrow-right text-[8px]"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NEW REVIEWS MODAL -->
    <div id="new-reviews-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-xl bg-[#0a0f1d] border border-slate-800 rounded-3xl p-8 shadow-2xl relative max-h-[85vh] overflow-y-auto animate-fade-in">
            <button onclick="closeNewReviewsModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-2 text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-comments text-emerald-400 animate-pulse"></i> Đánh Giá Mới Nhất
            </h2>
            <p class="text-xs text-slate-500 mb-6 font-semibold">Tổng hợp 5 bình luận đánh giá không gian sống thực tế mới được cập nhật trên hệ thống.</p>
            
            <div class="space-y-4">
                @forelse($recentReviews as $rev)
                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800/80 space-y-2 relative overflow-hidden group">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs font-bold text-slate-200 block"><i class="fa-solid fa-user-circle text-emerald-400 mr-1.5"></i>{{ $rev->author_name }}</span>
                            <span class="text-[9px] text-indigo-400 font-extrabold uppercase mt-0.5 tracking-wider block">Phòng {{ $rev->room ? $rev->room->room_number : 'N/A' }}</span>
                        </div>
                        <span class="text-xs text-amber-400 font-bold flex items-center gap-1 bg-amber-500/5 border border-amber-500/10 px-2 py-0.5 rounded-lg shrink-0">
                            <i class="fa-solid fa-star text-[10px]"></i> {{ $rev->rating }}/5
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 italic pl-1 leading-relaxed border-l-2 border-slate-800">"{{ $rev->comment }}"</p>
                    <span class="text-[9px] text-slate-600 block text-right"><i class="fa-regular fa-clock mr-1"></i>{{ $rev->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @empty
                <div class="text-center text-xs text-slate-500 py-8">
                    <i class="fa-solid fa-comment-slash text-2xl mb-2 text-slate-700 block"></i>
                    <span>Chưa có đánh giá thực tế nào trên hệ thống.</span>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Q&A COMMENTS MODAL -->
    <div id="qa-comments-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-lg bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 shadow-2xl relative max-h-[85vh] flex flex-col animate-fade-in">
            <button onclick="closeQaCommentsModal()" class="absolute top-6 right-6 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-base font-bold mb-4 text-slate-100 flex items-center gap-2">
                <i class="fa-regular fa-comments text-teal-400 animate-pulse"></i> Bình Luận Cộng Đồng
            </h2>
            
            <!-- Question Content Header -->
            <div class="p-4 rounded-2xl bg-slate-950/40 border border-slate-800/80 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold border uppercase tracking-wider bg-teal-500/10 text-teal-400 border-teal-500/20" id="qa-modal-area">Khu vực</span>
                    <span class="text-[9px] text-slate-500 font-semibold" id="qa-modal-time">Thời gian</span>
                </div>
                <p class="text-xs font-bold text-slate-200 leading-relaxed" id="qa-modal-question">Nội dung câu hỏi</p>
            </div>
            
            <!-- Scrollable Comments List -->
            <div class="flex-grow overflow-y-auto mb-4 space-y-3 pr-1 scrollbar-thin max-h-[40vh]" id="qa-modal-comments-list">
                <!-- Comments dynamically populated here -->
            </div>
            
            <!-- Reply input form -->
            <form id="qa-reply-form" onsubmit="submitQaReply(event)" class="mt-auto pt-3 border-t border-slate-800/80">
                <div class="relative w-full">
                    <input type="text" id="qa-reply-input" placeholder="Viết phản hồi ẩn danh của bạn..." required class="w-full pl-4 pr-16 py-3 bg-slate-900/60 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-550 text-xs focus:outline-none focus:border-teal-500 transition-colors" />
                    <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-500 hover:to-cyan-500 text-white rounded-lg text-[10px] font-extrabold transition-all">
                        Gửi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- NOTIFY SUBSCRIBE MODAL -->
    <div id="notify-subscribe-modal" class="fixed inset-0 z-50 bg-[#04060b]/90 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <button onclick="closeNotifySubscribeModal()" class="absolute top-4 right-4 w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-200 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400">
                    <i class="fa-solid fa-bell text-lg"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-100">Đăng ký chuông báo</h2>
                    <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Nhận thông báo ngay khi phòng trống</p>
                </div>
            </div>
            
            <form id="notify-subscribe-form" onsubmit="handleNotifySubscribeSubmit(event)">
                <input type="hidden" id="notify-room-id">
                
                <div class="space-y-4">
                    <div>
                        <span class="block text-[10px] text-slate-500 font-bold uppercase mb-1.5">Tên phòng trọ</span>
                        <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-900 text-xs font-bold text-slate-300" id="notify-room-title-display"></div>
                    </div>
                    
                    <div>
                        <label for="notify-contact-input" class="block text-[10px] text-slate-500 font-bold uppercase mb-1.5">Email hoặc Số điện thoại</label>
                        <input type="text" id="notify-contact-input" required placeholder="nhap-email@example.com hoặc 09xxxxxx" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-950/80 border border-slate-800 text-slate-200 focus:border-teal-500 focus:ring-0 focus:outline-none placeholder-slate-600 transition-colors">
                    </div>
                    
                    <div class="flex items-start gap-2.5 p-3 rounded-xl bg-teal-500/5 border border-teal-500/10">
                        <i class="fa-solid fa-circle-info text-teal-400 text-xs mt-0.5 shrink-0"></i>
                        <p class="text-[10px] text-teal-300/85 leading-normal font-semibold">Chúng tôi sẽ tự động gửi thông báo qua Email hoặc SMS ngay khi phòng trọ này được cập nhật trạng thái "SẴN SÀNG".</p>
                    </div>
                    
                    <div class="flex items-center gap-2 pt-2">
                        <button type="button" onclick="closeNotifySubscribeModal()" class="w-1/2 py-2 px-3 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-xs font-bold text-slate-400 hover:text-slate-200 transition-colors">
                            Hủy
                        </button>
                        <button type="submit" class="w-1/2 py-2 px-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-xs font-extrabold text-white shadow-lg shadow-teal-600/15 transition-all">
                            Đăng ký ngay
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- CUSTOM ALERT MODAL -->
    <div id="custom-alert-modal" class="fixed inset-0 z-[60] bg-[#04060b]/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="w-full max-w-sm bg-[#0a0f1d] border border-slate-800 rounded-3xl p-5 shadow-2xl relative text-center">
            <div class="w-12 h-12 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mx-auto mb-3">
                <i class="fa-solid fa-circle-check text-xl"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-100 mb-1.5" id="custom-alert-title">Thành công!</h3>
            <p class="text-[11px] text-slate-400 leading-normal mb-4" id="custom-alert-message"></p>
            <button onclick="closeCustomAlert()" class="w-full py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-extrabold text-white transition-colors">
                Xác nhận
            </button>
        </div>
    </div>

    <!-- FEAT_04_LANDING: Script tìm kiếm, Filter Chips và Xử lý Lỗi chuẩn spec -->
    <script>
        window.submitHeroSearch = function(event) {
            if (event) event.preventDefault();
            const heroInput = document.getElementById('hero-search-input');
            const boxContainer = document.getElementById('hero-search-box-container');
            const errEmpty = document.getElementById('search-empty-error');
            const errInvalid = document.getElementById('search-invalid-error');
            const val = heroInput ? heroInput.value.trim().slice(0, 100) : '';

            if (!val) {
                // ERR_04_01: Bấm tìm kiếm khi từ khóa rỗng -> Viền sáng ô tìm kiếm và hiển thị gợi ý
                if (boxContainer) {
                    boxContainer.classList.add('border-amber-400', 'ring-2', 'ring-amber-400/40');
                }
                if (errEmpty) errEmpty.classList.remove('hidden');
                if (errInvalid) errInvalid.classList.add('hidden');
                if (typeof openRentySearchSuggestions === 'function') openRentySearchSuggestions();
                if (heroInput) heroInput.focus();
                return;
            }

            if (errEmpty) errEmpty.classList.add('hidden');
            if (boxContainer) {
                boxContainer.classList.remove('border-amber-400', 'ring-2', 'ring-amber-400/40');
            }

            // ERR_04_02: Nhập từ khóa chứa mã độc/ký tự lạ -> Tự động làm sạch
            if (/[<>{}\$*&^%`~;]/.test(val)) {
                const cleaned = val.replace(/[<>{}\$*&^%`~;]/g, '').trim();
                if (heroInput) heroInput.value = cleaned;
                const navbarInput = document.getElementById('search-input');
                if (navbarInput) navbarInput.value = cleaned;
                if (errInvalid) errInvalid.classList.remove('hidden');
                setTimeout(() => {
                    if (errInvalid) errInvalid.classList.add('hidden');
                }, 5000);
            } else {
                if (errInvalid) errInvalid.classList.add('hidden');
            }

            if (typeof filterItems === 'function') {
                filterItems({ resetPage: true });
            }
        };

        window.applyQuickTag = function(tag) {
            const heroInput = document.getElementById('hero-search-input');
            const navInput = document.getElementById('search-input');
            if (heroInput) heroInput.value = tag;
            if (navInput) navInput.value = tag;
            
            const errEmpty = document.getElementById('search-empty-error');
            if (errEmpty) errEmpty.classList.add('hidden');
            const boxContainer = document.getElementById('hero-search-box-container');
            if (boxContainer) boxContainer.classList.remove('border-amber-400', 'ring-2', 'ring-amber-400/40');

            if (typeof filterItems === 'function') {
                filterItems({ resetPage: true });
            }
        };

        // Lắng nghe trạng thái mất kết nối mạng (ERR_04_04)
        window.addEventListener('offline', () => {
            const banner = document.getElementById('renty-network-error-banner');
            if (banner) banner.classList.remove('hidden');
        });
        window.addEventListener('online', () => {
            const banner = document.getElementById('renty-network-error-banner');
            if (banner) banner.classList.add('hidden');
        });

        document.addEventListener('DOMContentLoaded', () => {
            const navbarInput = document.getElementById('search-input');
            const heroInput = document.getElementById('hero-search-input');

            function syncInputs(source, target) {
                if (source && target && target.value !== source.value) {
                    target.value = source.value.slice(0, 100);
                }
            }

            if (navbarInput && heroInput) {
                navbarInput.addEventListener('input', () => {
                    syncInputs(navbarInput, heroInput);
                    const errEmpty = document.getElementById('search-empty-error');
                    if (errEmpty && navbarInput.value.trim() !== '') errEmpty.classList.add('hidden');
                });
                
                heroInput.addEventListener('input', () => {
                    syncInputs(heroInput, navbarInput);
                    const errEmpty = document.getElementById('search-empty-error');
                    if (errEmpty && heroInput.value.trim() !== '') errEmpty.classList.add('hidden');
                    if (typeof filterItems === 'function') {
                        filterItems();
                    }
                });

                navbarInput.addEventListener('change', () => {
                    syncInputs(navbarInput, heroInput);
                });

                setInterval(() => {
                    if (navbarInput.value !== heroInput.value) {
                        syncInputs(navbarInput, heroInput);
                    }
                }, 200);
            }
        });
    </script>
<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- RENTY CHATBOT - Tư vấn tìm trọ thông minh                       -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- Holographic AI Assistant Mascot & Speech Bubble -->
<div id="renty-agent-bubble" class="renty-agent-bubble">
    <div class="renty-agent-bubble-header">
        <i class="fa-solid fa-sparkles text-teal-400"></i> Renty Companion AI
    </div>
    <div id="renty-agent-bubble-text" class="renty-agent-bubble-body">
        Chào bạn! Mình có thể giúp bạn tìm phòng và check review quanh khu vực đấy!
    </div>
</div>

<div id="renty-chatbot-toggle" class="renty-chatbot-toggle" onclick="toggleRentyChatbot()" aria-label="Mở chatbot tư vấn" onmouseenter="showAgentBubble()" onmouseleave="hideAgentBubble()">
    <div class="renty-chatbot-toggle-pulse"></div>
    <div class="renty-chatbot-toggle-glow"></div>
    <!-- Mascot representation -->
    <div class="renty-chatbot-mascot">
        <i class="fa-solid fa-robot"></i>
    </div>
    <span class="renty-chatbot-badge" id="renty-chatbot-badge">1</span>
</div>

<div id="renty-chatbot-panel" class="renty-chatbot-panel" role="region" aria-label="Khung chat Trợ lý ảo AI Renty Chatbot">
    <!-- Header (STT 1: chat_head) -->
    <div id="chat_head" class="renty-chatbot-header" data-field="chat_head">
        <div class="renty-chatbot-header-left">
            <div class="renty-chatbot-avatar">
                <i class="fa-solid fa-robot"></i>
                <span class="renty-chatbot-status-dot"></span>
            </div>
            <div>
                <h4>Renty AI Companion</h4>
                <span class="renty-chatbot-status-text">Online - Trả lời tức thì</span>
            </div>
        </div>
        <div class="renty-chatbot-header-actions">
            <button type="button" onclick="clearRentyChatbot()" title="Xoá lịch sử chat" aria-label="Xoá lịch sử">
                <i class="fa-solid fa-trash-can"></i>
            </button>
            <button type="button" onclick="toggleRentyChatbot()" title="Đóng" aria-label="Đóng chatbot">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Message Body (STT 2: chat_messages) -->
    <div class="renty-chatbot-messages" id="chat_messages" data-field="chat_messages" aria-label="Lịch sử tin nhắn">
        <!-- Messages will be injected here by JS -->
    </div>

    <!-- Quick Actions (Thẻ gợi ý câu hỏi nhanh: Dưới 3 triệu, Có ban công, Cầu Giấy, Thú cưng) -->
    <div class="renty-chatbot-quick" id="renty-chatbot-quick">
        <button type="button" onclick="sendRentyChatbotMessage('Tìm phòng dưới 3 triệu')">💰 Dưới 3 triệu</button>
        <button type="button" onclick="sendRentyChatbotMessage('Phòng có ban công')">🌿 Có ban công</button>
        <button type="button" onclick="sendRentyChatbotMessage('Tìm phòng Cầu Giấy')">📍 Cầu Giấy</button>
        <button type="button" onclick="sendRentyChatbotMessage('Phòng cho nuôi thú cưng')">🐾 Thú cưng</button>
        <button type="button" onclick="sendRentyChatbotMessage('Tìm phòng Thủ Đức dưới 3 triệu')">🏙️ Thủ Đức</button>
    </div>

    <!-- Input Field (STT 3: chat_input) & Send Button (STT 4: btnSendChat) -->
    <div class="renty-chatbot-input-wrapper">
        <div id="chat_input_error" class="renty-chat-error-text hidden" role="alert">
            <i class="fa-solid fa-circle-exclamation"></i> Vui lòng nhập nội dung câu hỏi trước khi gửi.
        </div>
        <div class="renty-chatbot-input-area">
            <input type="text" id="chat_input" name="chat_input" data-field="chat_input"
                placeholder="Tìm phòng gần trường ĐH..." maxlength="500"
                aria-label="Nhập câu hỏi..."
                onkeydown="if(event.key==='Enter'){sendRentyChatbotMessage();event.preventDefault();}" autocomplete="off">
            <button type="button" id="btnSendChat" name="btnSendChat" data-field="btnSendChat"
                onclick="sendRentyChatbotMessage()" aria-label="Gửi" class="renty-chatbot-send-btn" title="Gửi">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<!-- FLOATING ROOM COMPARISON BAR (MATCHING USER SCREENSHOT) -->
<div id="renty-compare-bar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-2 p-1.5 sm:p-2 bg-[#0c1017]/95 border border-zinc-800/90 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.85)] backdrop-blur-xl transition-all duration-300 translate-y-28 opacity-0 pointer-events-none max-w-[96vw] sm:max-w-max select-none">
    <!-- Icon so sánh góc trái -->
    <div class="w-10 h-10 rounded-xl bg-[#141824] border border-zinc-800 flex items-center justify-center shrink-0 text-white shadow-inner" title="So sánh phòng">
        <svg class="w-5 h-5 text-zinc-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="6" cy="18" r="2.5"></circle>
            <circle cx="18" cy="6" r="2.5"></circle>
            <path d="M6 15.5V11a3 3 0 0 1 3-3h6.5"></path>
            <path d="M18 8.5V13a3 3 0 0 1-3 3H8.5"></path>
            <polyline points="13 5.5 15.5 8 13 10.5"></polyline>
            <polyline points="11 13.5 8.5 16 11 18.5"></polyline>
        </svg>
    </div>

    <!-- Danh sách các phòng đã chọn (render động bởi JS) -->
    <div id="compare-bar-items" class="flex items-center gap-2 max-w-[50vw] sm:max-w-none overflow-x-auto no-scrollbar py-0.5">
        <!-- Chứa các thẻ chip phòng -->
    </div>

    <!-- Nút Hủy -->
    <button type="button" onclick="clearCompareList()" class="px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-[#141824] hover:bg-zinc-800 border border-zinc-800 text-white font-bold text-xs sm:text-sm transition-colors cursor-pointer shrink-0">
        Hủy
    </button>

    <!-- Nút So sánh ngay -->
    <button type="button" onclick="showCompareModal()" id="compare-submit-btn" class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl bg-gradient-to-r from-zinc-200 via-neutral-100 to-zinc-300 hover:from-white hover:to-zinc-200 text-zinc-950 font-black text-xs sm:text-sm shadow-md flex items-center gap-2 transition-all cursor-pointer shrink-0">
        <i class="fa-solid fa-code-compare text-xs sm:text-sm"></i>
        <span>So sánh ngay (<span id="compare-btn-count">0</span>)</span>
    </button>
</div>

<!-- DETAILED COMPARISON MODAL (MATCHING SPEC HÌNH 23.2) -->
<div id="renty-compare-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/80 backdrop-blur-md px-4 py-6">
    <div class="w-full max-w-3xl bg-[#0c1017] border border-slate-800 rounded-2xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh] animate-fade-in p-6">
        <!-- Header -->
        <div class="flex justify-between items-start pb-4 border-b border-slate-800/80 mb-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fa-solid fa-code-compare text-base"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-white">So sánh phòng đã chọn</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Vuốt ngang trên điện thoại để xem đủ các phòng.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="copyCompareShareLink()" title="Sao chép link so sánh" class="px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-800 hover:border-emerald-500/50 text-slate-300 hover:text-emerald-400 flex items-center gap-1.5 text-xs font-bold transition-all shadow-sm">
                    <i class="fa-solid fa-share-nodes text-xs"></i>
                    <span class="hidden sm:inline">Chia sẻ</span>
                </button>
                <button type="button" onclick="hideCompareModal()" class="w-8 h-8 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition-all">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>
        
        <!-- Table Container -->
        <div class="overflow-x-auto flex-grow rounded-xl border border-slate-800/80 bg-slate-950/40">
            <table class="w-full text-left text-xs text-slate-200 min-w-[500px] border-collapse compare-table" id="tblCompare">
                <!-- Populated by JS -->
            </table>
        </div>

        <!-- AI Recommendation Insight Box -->
        <div id="compare-ai-box" class="hidden mt-4 p-4 rounded-xl bg-emerald-950/20 border border-emerald-500/30 text-xs text-slate-200 shadow-xl relative animate-fade-in">
            <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                </div>
                <div class="flex-grow">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-emerald-400 text-xs">Phân tích so sánh AI</h4>
                        <button type="button" onclick="document.getElementById('compare-ai-box').classList.add('hidden')" class="text-slate-500 hover:text-white transition-colors">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                    <div id="compare-ai-content" class="mt-2 text-[11px] text-slate-300 leading-relaxed"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="pt-4 mt-3 border-t border-slate-800/80 flex items-center justify-between gap-3">
            <button type="button" onclick="generateAiComparison()" id="compare-ai-btn" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-emerald-500/40 text-emerald-400 text-xs font-bold transition-all">
                <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                <span>Tư vấn so sánh bằng AI</span>
            </button>
            <div class="flex items-center gap-2">
                <button type="button" onclick="clearCompareList(); hideCompareModal();" class="px-3 py-1.5 text-xs font-semibold text-slate-400 hover:text-rose-400 transition-colors">
                    <i class="fa-solid fa-trash-can mr-1 text-xs"></i> Xóa tất cả
                </button>
                <button type="button" onclick="hideCompareModal()" class="px-4 py-1.5 rounded-xl border border-slate-800 bg-slate-900 text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-800 transition-all">
                    Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<!-- RENTY TOAST NOTIFICATION CONTAINER -->
<div id="renty-toast-container" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none max-w-sm w-full"></div>

<script>
    function showRentyToast(message, type = 'success', customTitle = null) {
        const container = document.getElementById('renty-toast-container');
        if (!container) return;

        let styleClasses = 'bg-slate-900/95 border-emerald-500/40 text-emerald-300 shadow-emerald-500/15';
        let iconBg = 'bg-emerald-500/15';
        let icon = 'fa-circle-check text-emerald-400';
        let defaultTitle = 'Đăng nhập thành công!';

        if (type === 'warning') {
            styleClasses = 'bg-slate-900/95 border-amber-500/40 text-amber-300 shadow-amber-500/15';
            iconBg = 'bg-amber-500/15';
            icon = 'fa-triangle-exclamation text-amber-400';
            defaultTitle = 'Cảnh báo';
        } else if (type === 'error') {
            styleClasses = 'bg-slate-900/95 border-rose-500/40 text-rose-300 shadow-rose-500/15';
            iconBg = 'bg-rose-500/15';
            icon = 'fa-circle-exclamation text-rose-400';
            defaultTitle = 'Thông báo';
        }

        const title = customTitle || defaultTitle;
        const toast = document.createElement('div');
        toast.className = `pointer-events-auto flex items-start gap-3 p-4 rounded-2xl border shadow-2xl backdrop-blur-xl transition-all duration-300 transform translate-x-10 opacity-0 ${styleClasses}`;

        toast.innerHTML = `
            <div class="w-8 h-8 rounded-xl ${iconBg} flex items-center justify-center shrink-0 mt-0.5">
                <i class="fa-solid ${icon} text-base"></i>
            </div>
            <div class="flex-grow min-w-0">
                <h4 class="font-extrabold text-white text-xs tracking-wide">${title}</h4>
                <p class="text-[11px] text-slate-300 mt-0.5 leading-relaxed">${message}</p>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-500 hover:text-white transition-colors shrink-0 p-1">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        `;

        container.appendChild(toast);

        // Hiệu ứng trượt vào
        setTimeout(() => {
            toast.classList.remove('translate-x-10', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        }, 50);

        // Tự động tắt sau 4.5 giây
        setTimeout(() => {
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 4500);
    }
    window.showRentyToast = showRentyToast;

    document.addEventListener('DOMContentLoaded', () => {
        if (window.rentySessionSuccess) {
            showRentyToast(window.rentySessionSuccess, 'success');
        }
        }
    });
</script>

    <!-- Popup Modal: Hướng dẫn bật GPS [ERR_05_01] -->
    <div id="gps-permission-modal" class="hidden fixed inset-0 z-[9999999] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div class="relative w-full max-w-md p-6 rounded-3xl bg-slate-900 border-2 border-rose-500 shadow-2xl shadow-rose-950/50 text-slate-100 flex flex-col gap-4 animate-scale-in">
            <div class="flex items-center gap-3">
                <span class="w-12 h-12 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-2xl shrink-0 shadow-lg">
                    <i class="fa-solid fa-location-crosshairs"></i>
                </span>
                <div>
                    <h3 class="text-base font-black text-white mt-1">Yêu cầu quyền truy cập vị trí</h3>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-rose-950/40 border border-rose-500/30 text-rose-200 text-xs font-semibold leading-relaxed">
                Vui lòng cho phép truy cập vị trí trên trình duyệt để tìm phòng gần bạn nhất.
            </div>

            <div class="text-xs text-slate-300 space-y-2">
                <div class="font-bold text-white flex items-center gap-1.5 text-xs">
                    <i class="fa-solid fa-circle-info text-sky-400"></i> Hướng dẫn bật lại GPS trên trình duyệt:
                </div>
                <ul class="space-y-1.5 pl-4 list-disc text-slate-300 text-[11px] leading-relaxed">
                    <li>Nhấp vào biểu tượng <b>Cài đặt trang / Ổ khóa 🔒</b> bên trái thanh địa chỉ trình duyệt.</li>
                    <li>Tìm mục <b>Vị trí (Location)</b> và chuyển từ <b>Chặn</b> sang <b>Cho phép</b>.</li>
                    <li>Nhấn nút <b>"Thử lại định vị"</b> bên dưới để quét các phòng trọ lân cận bạn.</li>
                </ul>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-800">
                <button type="button" onclick="closeGpsModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all cursor-pointer">
                    Đóng
                </button>
                <button type="button" onclick="retryLocateUser()" class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white text-xs font-bold shadow-lg shadow-emerald-500/20 flex items-center gap-1.5 transition-all cursor-pointer">
                    <i class="fa-solid fa-rotate-right"></i> Thử lại định vị
                </button>
            </div>
        </div>
    </div>

    <script>
        function closeGpsModal() {
            const m = document.getElementById('gps-permission-modal');
            if (m) m.classList.add('hidden');
        }
        function retryLocateUser() {
            closeGpsModal();
            if (window.locateUserRentyMap) window.locateUserRentyMap();
        }
    </script>
</body>
</html>
