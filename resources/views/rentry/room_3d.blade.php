<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Mô hình phòng trọ ảo 3D tương tác 360 độ đầy đủ tiện ích - SmartRoom & Renty">
    <title>Mô Hình Phòng Trọ Ảo 3D Tương Tác | SmartRoom &amp; Renty</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Three.js and OrbitControls -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-panel {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.65);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .hud-btn {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hud-btn:hover {
            transform: translateY(-2px);
            background: rgba(59, 130, 246, 0.25);
            border-color: rgba(96, 165, 250, 0.4);
        }
        .hud-btn.active {
            background: #2563eb;
            color: #ffffff;
            border-color: #60a5fa;
            box-shadow: 0 0 15px rgba(37, 99, 235, 0.5);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.6); }
        ::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, 0.4); border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(148, 163, 184, 0.6); }
    </style>
</head>
<body class="h-full bg-[#070b14] text-slate-100 overflow-hidden select-none">

    <!-- 3D Canvas Container -->
    <div id="canvas-container" class="absolute inset-0 w-full h-full cursor-grab active:cursor-grabbing"></div>

    <!-- =========================================================================
         TOP HUD BAR
         ========================================================================= -->
    <header class="absolute top-0 left-0 right-0 p-4 md:p-6 z-20 pointer-events-none flex justify-between items-start gap-4">
        <!-- Room Identity -->
        <div class="glass-panel rounded-2xl p-3 md:p-4 pointer-events-auto flex items-center gap-3.5 shadow-2xl max-w-md">
            <a href="{{ url()->previous() ?: route('renty.user') }}" class="w-10 h-10 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 flex items-center justify-center transition-all">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        <i class="fa-solid fa-cube mr-1"></i> 3D Virtual Tour
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-sky-500/20 text-sky-400 border border-sky-500/30">
                        28m² • Tầng 2
                    </span>
                </div>
                <h1 class="text-base md:text-lg font-extrabold text-white mt-0.5 tracking-tight flex items-center gap-2">
                    <span>Phòng Studio Ban Công Cao Cấp</span>
                    <i class="fa-solid fa-circle-check text-sky-400 text-xs" title="Đã kiểm định thực tế"></i>
                </h1>
                <p class="text-xs text-slate-400">Giá thuê: <span class="text-amber-400 font-bold">4.500.000đ</span>/tháng • Đầy đủ tiện ích</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="pointer-events-auto flex items-center gap-2.5">
            <button type="button" id="btn-toggle-amenities" class="glass-panel px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-200 hover:text-white flex items-center gap-2 hud-btn shadow-lg">
                <i class="fa-solid fa-list-check text-sky-400"></i>
                <span class="hidden sm:inline">Danh Sách Tiện Ích</span>
            </button>
            <a href="{{ route('renty.user') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-blue-500/30 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-bolt"></i>
                <span>Đặt Thuê Phòng</span>
            </a>
        </div>
    </header>

    <!-- =========================================================================
         BOTTOM FLOATING DOCK BAR
         ========================================================================= -->
    <nav class="absolute bottom-4 md:bottom-6 left-1/2 -translate-x-1/2 z-20 pointer-events-auto max-w-[95vw]">
        <div class="glass-panel rounded-2xl px-3 py-2.5 flex items-center gap-1.5 md:gap-2 shadow-2xl border border-white/15 overflow-x-auto">
            
            <!-- Camera Preset: Isometric -->
            <button type="button" data-preset="isometric" class="hud-btn active px-3 py-2 rounded-xl text-xs font-medium flex items-center gap-1.5 whitespace-nowrap" title="Góc nhìn toàn cảnh 3D">
                <i class="fa-solid fa-cubes text-sm"></i>
                <span class="hidden md:inline">Toàn Cảnh</span>
            </button>

            <!-- Camera Preset: Floorplan -->
            <button type="button" data-preset="floorplan" class="hud-btn px-3 py-2 rounded-xl text-xs font-medium flex items-center gap-1.5 whitespace-nowrap" title="Mặt bằng cắt lớp 2D-3D">
                <i class="fa-solid fa-layer-group text-sm"></i>
                <span class="hidden md:inline">Mặt Bằng</span>
            </button>

            <!-- Camera Preset: First Person -->
            <button type="button" data-preset="firstperson" class="hud-btn px-3 py-2 rounded-xl text-xs font-medium flex items-center gap-1.5 whitespace-nowrap" title="Tầm nhìn người thuê">
                <i class="fa-solid fa-person-walking text-sm"></i>
                <span class="hidden md:inline">Góc Nhìn Thuê</span>
            </button>

            <div class="w-[1px] h-6 bg-slate-700/60 mx-1"></div>

            <!-- Quick Zones: Bed, Desk, Kitchen, WC, Balcony -->
            <button type="button" data-preset="bed" class="hud-btn px-2.5 py-2 rounded-xl text-xs font-medium" title="Khu Giường Ngủ">
                <i class="fa-solid fa-bed"></i>
            </button>
            <button type="button" data-preset="desk" class="hud-btn px-2.5 py-2 rounded-xl text-xs font-medium" title="Bàn Làm Việc">
                <i class="fa-solid fa-laptop"></i>
            </button>
            <button type="button" data-preset="kitchen" class="hud-btn px-2.5 py-2 rounded-xl text-xs font-medium" title="Khu Bếp Mini">
                <i class="fa-solid fa-utensils"></i>
            </button>
            <button type="button" data-preset="bathroom" class="hud-btn px-2.5 py-2 rounded-xl text-xs font-medium" title="Nhà Vệ Sinh Khép Kín">
                <i class="fa-solid fa-shower"></i>
            </button>
            <button type="button" data-preset="balcony" class="hud-btn px-2.5 py-2 rounded-xl text-xs font-medium" title="Ban Công & Máy Giặt">
                <i class="fa-solid fa-sun-plant-wilt"></i>
            </button>

            <div class="w-[1px] h-6 bg-slate-700/60 mx-1"></div>

            <!-- Day / Night Toggle -->
            <button type="button" id="btn-toggle-lighting" class="hud-btn px-3 py-2 rounded-xl text-xs font-medium flex items-center gap-1.5" title="Chuyển Ngày / Đêm">
                <i class="fa-solid fa-moon text-amber-300" id="icon-lighting"></i>
                <span class="hidden lg:inline" id="text-lighting">Ban Đêm</span>
            </button>

            <!-- Cutaway / Wall Transparency -->
            <button type="button" id="btn-toggle-cutaway" class="hud-btn px-3 py-2 rounded-xl text-xs font-medium flex items-center gap-1.5" title="Bật / Tắt Mặt Cắt Tường">
                <i class="fa-solid fa-border-all text-sky-400"></i>
                <span class="hidden lg:inline">Tháo Mái/Tường</span>
            </button>

            <!-- Customizer Palette -->
            <button type="button" id="btn-toggle-customizer" class="hud-btn px-3 py-2 rounded-xl text-xs font-medium flex items-center gap-1.5" title="Đổi màu nội thất">
                <i class="fa-solid fa-palette text-pink-400"></i>
                <span class="hidden lg:inline">Phối Màu</span>
            </button>
        </div>
    </nav>

    <!-- =========================================================================
         HOTSPOT DETAIL MODAL / CARD
         ========================================================================= -->
    <div id="hotspot-modal" class="fixed bottom-24 md:bottom-28 right-4 md:right-8 w-80 md:w-96 glass-panel rounded-2xl p-5 shadow-2xl z-30 transform translate-y-6 opacity-0 pointer-events-none transition-all duration-300 border border-blue-500/30">
        <div class="flex items-start justify-between gap-2 mb-3">
            <div>
                <span id="hm-category" class="text-[10px] uppercase font-bold text-sky-400 tracking-wider">Tiện Nghi</span>
                <h3 id="hm-title" class="text-base font-extrabold text-white">Tên Tiện Ích</h3>
            </div>
            <button type="button" id="btn-close-hotspot" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <p id="hm-description" class="text-xs text-slate-300 leading-relaxed mb-4">Mô tả chi tiết tiện ích.</p>

        <!-- Specs Grid -->
        <div id="hm-specs" class="grid grid-cols-2 gap-2 mb-4 bg-slate-900/60 p-2.5 rounded-xl border border-white/5">
            <!-- Injected via JS -->
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-800">
            <span class="text-[11px] text-emerald-400 flex items-center gap-1.5 font-medium">
                <i class="fa-solid fa-circle-check"></i> Đang hoạt động hoàn hảo
            </span>
            <button type="button" id="btn-focus-hotspot" class="px-3 py-1.5 bg-blue-600/30 hover:bg-blue-600/50 text-blue-300 text-xs font-semibold rounded-lg transition-all">
                Xem cận cảnh
            </button>
        </div>
    </div>

    <!-- =========================================================================
         COLOR CUSTOMIZER POPOVER
         ========================================================================= -->
    <div id="customizer-popover" class="fixed bottom-24 md:bottom-28 left-1/2 -translate-x-1/2 glass-panel rounded-2xl p-4 shadow-2xl z-30 transform translate-y-4 opacity-0 pointer-events-none transition-all duration-300 border border-white/15 w-80">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                <i class="fa-solid fa-sliders text-pink-400"></i> Phối Màu Phòng Trọ
            </span>
            <button type="button" id="btn-close-customizer" class="text-slate-400 hover:text-white text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Accent Wall Colors -->
        <div class="mb-3">
            <label class="block text-[11px] font-semibold text-slate-400 mb-1.5">Màu Tường Điểm Nhấn (Accent Wall)</label>
            <div class="flex items-center gap-2">
                <button type="button" class="btn-wall-color w-7 h-7 rounded-full bg-[#2563eb] ring-2 ring-white ring-offset-2 ring-offset-slate-900 transition-all" data-color="#2563eb" title="Xanh Navy Hiện Đại"></button>
                <button type="button" class="btn-wall-color w-7 h-7 rounded-full bg-[#059669] hover:scale-110 transition-all" data-color="#059669" title="Xanh Sage Thiên Nhiên"></button>
                <button type="button" class="btn-wall-color w-7 h-7 rounded-full bg-[#d97706] hover:scale-110 transition-all" data-color="#d97706" title="Vàng Đất Ấm Cúng"></button>
                <button type="button" class="btn-wall-color w-7 h-7 rounded-full bg-[#475569] hover:scale-110 transition-all" data-color="#475569" title="Xám Slate Tối Giản"></button>
                <button type="button" class="btn-wall-color w-7 h-7 rounded-full bg-[#7c3aed] hover:scale-110 transition-all" data-color="#7c3aed" title="Tím Pastel Sang Trọng"></button>
            </div>
        </div>

        <!-- Bedding Colors -->
        <div>
            <label class="block text-[11px] font-semibold text-slate-400 mb-1.5">Màu Chăn Ga Nệm</label>
            <div class="flex items-center gap-2">
                <button type="button" class="btn-bed-color w-7 h-7 rounded-full bg-[#3b82f6] ring-2 ring-white ring-offset-2 ring-offset-slate-900 transition-all" data-color="#3b82f6" title="Xanh Dương"></button>
                <button type="button" class="btn-bed-color w-7 h-7 rounded-full bg-[#334155] hover:scale-110 transition-all" data-color="#334155" title="Xám Than"></button>
                <button type="button" class="btn-bed-color w-7 h-7 rounded-full bg-[#d97706] hover:scale-110 transition-all" data-color="#d97706" title="Cam Đất"></button>
                <button type="button" class="btn-bed-color w-7 h-7 rounded-full bg-[#f43f5e] hover:scale-110 transition-all" data-color="#f43f5e" title="Hồng Pastel"></button>
                <button type="button" class="btn-bed-color w-7 h-7 rounded-full bg-[#e2e8f0] hover:scale-110 transition-all" data-color="#e2e8f0" title="Trắng Sữa"></button>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         ALL AMENITIES SIDE DRAWER
         ========================================================================= -->
    <div id="amenities-drawer" class="fixed inset-y-0 right-0 w-80 md:w-96 glass-panel z-40 transform translate-x-full transition-transform duration-300 ease-in-out p-6 overflow-y-auto flex flex-col justify-between shadow-2xl border-l border-white/10">
        <div>
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                <div>
                    <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-sky-400"></i> Tiện Ích Phòng Trọ
                    </h2>
                    <p class="text-xs text-slate-400">Bấm vào tiện ích để di chuyển góc nhìn 3D</p>
                </div>
                <button type="button" id="btn-close-drawer" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Amenity List -->
            <div class="space-y-2.5" id="drawer-amenity-list">
                <!-- Injected via JS -->
            </div>
        </div>

        <div class="pt-6 border-t border-slate-800 mt-6">
            <div class="bg-blue-950/40 border border-blue-500/20 rounded-xl p-3 mb-4">
                <p class="text-[11px] text-blue-200 leading-relaxed flex items-start gap-2">
                    <i class="fa-solid fa-shield-halved text-blue-400 mt-0.5"></i>
                    <span>Tất cả thiết bị đều có biên bản bàn giao kèm tem kiểm định an toàn chất lượng của SmartRoom.</span>
                </p>
            </div>
            <a href="{{ route('renty.user') }}" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2 transition-all">
                <i class="fa-solid fa-calendar-check"></i> Đăng Ký Xem Phòng Trực Tiếp
            </a>
        </div>
    </div>

    <!-- Backdrop for Drawer -->
    <div id="drawer-backdrop" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 opacity-0 pointer-events-none transition-opacity duration-300"></div>

    <!-- Onscreen Interaction Hint -->
    <div id="hint-overlay" class="absolute top-20 left-1/2 -translate-x-1/2 glass-card rounded-full px-4 py-1.5 text-xs text-slate-300 pointer-events-none z-10 flex items-center gap-2 shadow-lg animate-pulse">
        <i class="fa-solid fa-hand-pointer text-sky-400"></i>
        <span>Kéo chuột để xoay 360° • Cuộn để phóng to • Bấm vào các điểm chấm xanh để khám phá</span>
    </div>

    <!-- Three.js 3D Room Script -->
    <script src="{{ asset('js/room-3d-model.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Initialize 3D Room Tour
            const tour = new Room3DTour('canvas-container', {
                isNight: false,
                showCutaway: false,
                onSelectHotspot: (data) => {
                    showHotspotModal(data);
                }
            });

            // 2. Camera Preset Buttons
            const presetButtons = document.querySelectorAll('[data-preset]');
            presetButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    presetButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    tour.setPresetView(btn.dataset.preset);
                });
            });

            // 3. Day / Night Toggle
            let isNight = false;
            const btnLighting = document.getElementById('btn-toggle-lighting');
            const iconLighting = document.getElementById('icon-lighting');
            const textLighting = document.getElementById('text-lighting');
            btnLighting.addEventListener('click', () => {
                isNight = !isNight;
                tour.setDayNight(isNight);
                if (isNight) {
                    iconLighting.className = 'fa-solid fa-sun text-amber-400';
                    textLighting.textContent = 'Ban Ngày';
                    btnLighting.classList.add('active');
                } else {
                    iconLighting.className = 'fa-solid fa-moon text-amber-300';
                    textLighting.textContent = 'Ban Đêm';
                    btnLighting.classList.remove('active');
                }
            });

            // 4. Cutaway Mode Toggle
            let isCutaway = false;
            const btnCutaway = document.getElementById('btn-toggle-cutaway');
            btnCutaway.addEventListener('click', () => {
                isCutaway = !isCutaway;
                tour.setCutaway(isCutaway);
                if (isCutaway) {
                    btnCutaway.classList.add('active');
                } else {
                    btnCutaway.classList.remove('active');
                }
            });

            // 5. Customizer Popover
            const btnCustomizer = document.getElementById('btn-toggle-customizer');
            const customizerPopover = document.getElementById('customizer-popover');
            const btnCloseCustomizer = document.getElementById('btn-close-customizer');

            btnCustomizer.addEventListener('click', () => {
                const isOpen = !customizerPopover.classList.contains('pointer-events-none');
                if (isOpen) {
                    closeCustomizer();
                } else {
                    openCustomizer();
                }
            });
            btnCloseCustomizer.addEventListener('click', closeCustomizer);

            function openCustomizer() {
                customizerPopover.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
                btnCustomizer.classList.add('active');
            }
            function closeCustomizer() {
                customizerPopover.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
                btnCustomizer.classList.remove('active');
            }

            document.querySelectorAll('.btn-wall-color').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.btn-wall-color').forEach(b => b.classList.remove('ring-2', 'ring-white', 'ring-offset-2', 'ring-offset-slate-900'));
                    btn.classList.add('ring-2', 'ring-white', 'ring-offset-2', 'ring-offset-slate-900');
                    tour.setWallColor(btn.dataset.color);
                });
            });

            document.querySelectorAll('.btn-bed-color').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.btn-bed-color').forEach(b => b.classList.remove('ring-2', 'ring-white', 'ring-offset-2', 'ring-offset-slate-900'));
                    btn.classList.add('ring-2', 'ring-white', 'ring-offset-2', 'ring-offset-slate-900');
                    tour.setBeddingColor(btn.dataset.color);
                });
            });

            // 6. Hotspot Modal Logic
            const hotspotModal = document.getElementById('hotspot-modal');
            const btnCloseHotspot = document.getElementById('btn-close-hotspot');
            let currentSelectedHotspot = null;

            btnCloseHotspot.addEventListener('click', () => {
                hotspotModal.classList.add('opacity-0', 'pointer-events-none', 'translate-y-6');
            });

            document.getElementById('btn-focus-hotspot').addEventListener('click', () => {
                if (currentSelectedHotspot) {
                    tour.animateCamera(currentSelectedHotspot.cameraPos, currentSelectedHotspot.targetPos);
                }
            });

            function showHotspotModal(data) {
                currentSelectedHotspot = data;
                document.getElementById('hm-category').textContent = data.category;
                document.getElementById('hm-title').textContent = data.title;
                document.getElementById('hm-description').textContent = data.description;

                const specsContainer = document.getElementById('hm-specs');
                specsContainer.innerHTML = '';
                if (data.specs && data.specs.length > 0) {
                    data.specs.forEach(s => {
                        const item = document.createElement('div');
                        item.className = 'text-[11px]';
                        item.innerHTML = `<span class="text-slate-400 block">${s.label}:</span><span class="text-slate-200 font-semibold">${s.val}</span>`;
                        specsContainer.appendChild(item);
                    });
                }

                hotspotModal.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-6');
            }

            // 7. Amenities Drawer Logic
            const btnToggleAmenities = document.getElementById('btn-toggle-amenities');
            const amenitiesDrawer = document.getElementById('amenities-drawer');
            const btnCloseDrawer = document.getElementById('btn-close-drawer');
            const drawerBackdrop = document.getElementById('drawer-backdrop');
            const drawerList = document.getElementById('drawer-amenity-list');

            const amenitiesList = [
                { id: 'bed', icon: 'fa-bed', name: 'Giường Ngủ Đôi 1m6 x 2m', cat: 'Nệm cao su non 20cm, chăn ga Tencel' },
                { id: 'ac', icon: 'fa-snowflake', name: 'Điều Hòa Daikin Inverter', cat: '1.5 HP, lọc bụi mịn PM2.5, tiết kiệm điện' },
                { id: 'desk', icon: 'fa-laptop', name: 'Bàn Làm Việc & Ghế Xoay', cat: 'Gỗ MDF 1m4, ghế công thái học, đèn bàn' },
                { id: 'kitchen', icon: 'fa-utensils', name: 'Bếp Mini & Tủ Lạnh Inverter', cat: 'Mặt đá quartz, bếp từ, tủ lạnh 180L, bồn rửa' },
                { id: 'bathroom', icon: 'fa-shower', name: 'Nhà Vệ Sinh Khép Kín', cat: 'Bình nóng lạnh 20L, gương LED cảm ứng, sen đứng' },
                { id: 'balcony', icon: 'fa-sun-plant-wilt', name: 'Ban Công & Máy Giặt', cat: 'Máy giặt lồng ngang Electrolux, giàn phơi, cây xanh' },
                { id: 'smartlock', icon: 'fa-fingerprint', name: 'Khóa Cửa Thông Minh SmartLock', cat: 'Vân tay 1 chạm, mã số ảo, thẻ từ bảo mật' },
                { id: 'meter', icon: 'fa-gauge-high', name: 'Công Tơ Điện Riêng & PCCC', cat: 'Đồng hồ điện tử EVN, bình cứu hỏa MFZ4' },
            ];

            amenitiesList.forEach(item => {
                const el = document.createElement('div');
                el.className = 'glass-card p-3 rounded-xl hover:bg-slate-800/80 cursor-pointer transition-all flex items-center justify-between group';
                el.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-600/20 text-blue-400 flex items-center justify-center text-sm group-hover:scale-105 transition-all">
                            <i class="fa-solid ${item.icon}"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white group-hover:text-blue-400 transition-colors">${item.name}</h4>
                            <p class="text-[11px] text-slate-400 line-clamp-1">${item.cat}</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-500 group-hover:text-white transition-colors"></i>
                `;
                el.addEventListener('click', () => {
                    const hs = tour.hotspots.find(h => h.userData.id === item.id);
                    if (hs) {
                        tour.animateCamera(hs.userData.cameraPos, hs.userData.targetPos);
                        showHotspotModal(hs.userData);
                        closeDrawer();
                    }
                });
                drawerList.appendChild(el);
            });

            btnToggleAmenities.addEventListener('click', openDrawer);
            btnCloseDrawer.addEventListener('click', closeDrawer);
            drawerBackdrop.addEventListener('click', closeDrawer);

            function openDrawer() {
                amenitiesDrawer.classList.remove('translate-x-full');
                drawerBackdrop.classList.remove('opacity-0', 'pointer-events-none');
            }
            function closeDrawer() {
                amenitiesDrawer.classList.add('translate-x-full');
                drawerBackdrop.classList.add('opacity-0', 'pointer-events-none');
            }

            // Hide hint after 6s
            setTimeout(() => {
                const hint = document.getElementById('hint-overlay');
                if (hint) {
                    hint.style.transition = 'opacity 1s ease';
                    hint.style.opacity = '0';
                    setTimeout(() => hint.remove(), 1000);
                }
            }, 6000);
        });
    </script>
</body>
</html>
