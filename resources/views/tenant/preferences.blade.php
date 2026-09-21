<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bạn Đang Tìm Phòng Khu Vực Nào? - SmartRoom &amp; Renty</title>

    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --bg-base: #030712;
            --card-bg: rgba(15, 23, 42, 0.65);
            --border-color: rgba(51, 65, 85, 0.4);
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            --accent-emerald: linear-gradient(135deg, #10b981 0%, #14b8a6 100%);
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-base);
            color: #f8fafc;
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 15% 20%, rgba(99, 102, 241, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 85% 80%, rgba(139, 92, 246, 0.12) 0%, transparent 40%);
            background-attachment: fixed;
        }

        .pref-chip {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            user-select: none;
        }

        .pref-chip:hover {
            transform: translateY(-2px);
            border-color: rgba(99, 102, 241, 0.6);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
        }

        .pref-chip.selected {
            background: rgba(99, 102, 241, 0.25) !important;
            border-color: #818cf8 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3) !important;
        }

        .pref-chip.selected i {
            color: #a5b4fc !important;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen justify-between p-4 sm:p-6 lg:p-8">

    <!-- Header Logo -->
    <header class="w-full max-w-2xl mx-auto flex items-center justify-between py-2">
        <a href="/renty" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white text-lg shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-house-user"></i>
            </div>
            <div>
                <span class="font-extrabold text-lg text-white tracking-tight">SmartRoom <span class="text-indigo-400">&amp; Renty</span></span>
                <span class="block text-[10px] text-slate-500 uppercase tracking-widest font-semibold">Khách Thuê • Cá Nhân Hóa</span>
            </div>
        </a>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400 hidden sm:inline">Xin chào,</span>
            <span class="text-xs font-bold text-indigo-300 bg-indigo-500/10 px-3 py-1 rounded-full border border-indigo-500/20">
                {{ $user->name ?? 'Bạn' }}
            </span>
        </div>
    </header>

    <!-- Main Card -->
    <main class="w-full max-w-2xl mx-auto my-6">
        <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 rounded-3xl p-6 sm:p-8 shadow-[0_25px_60px_rgba(0,0,0,0.4)]">
            
            <!-- Icon & Heading -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-tr from-indigo-500/20 to-violet-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-2xl mb-4 shadow-inner">
                    <i class="fa-solid fa-compass"></i>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Bạn Đang Tìm Phòng Khu Vực Nào?
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 mt-2 max-w-md mx-auto leading-relaxed">
                    Chọn các khu vực hoặc tiện ích bạn quan tâm để Renty tự động ưu tiên gợi ý những phòng trọ phù hợp nhất cho bạn.
                </p>
            </div>

            <!-- Form -->
            <form id="preferences-form" action="{{ route('tenant.preferences.save') }}" method="POST">
                @csrf
                <input type="hidden" name="area_tags" id="area-tags-input" value="{{ json_encode($savedTags ?? []) }}">

                <!-- Nhóm: Khu Vực Phổ Biến -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-indigo-400"></i> Khu vực gợi ý (Click chọn)
                        </label>
                        <span class="text-[11px] text-slate-500" id="selected-count">Đã chọn 0</span>
                    </div>

                    <div class="flex flex-wrap gap-2" id="chip-container">
                        @php
                            $defaultTags = [
                                'Cầu Giấy' => 'fa-location-dot',
                                'Bách Khoa' => 'fa-graduation-cap',
                                'Đống Đa' => 'fa-location-dot',
                                'Quận 1' => 'fa-city',
                                'Quận 10' => 'fa-location-dot',
                                'Bình Thạnh' => 'fa-location-dot',
                                'Thủ Đức' => 'fa-tree',
                                'Gò Vấp' => 'fa-location-dot',
                                'Phú Nhuận' => 'fa-location-dot',
                                'Tân Bình' => 'fa-plane',
                            ];
                        @endphp

                        @foreach($defaultTags as $tag => $icon)
                            @php $isSelected = in_array($tag, $savedTags ?? []); @endphp
                            <div class="pref-chip px-3.5 py-2 rounded-xl text-xs font-semibold border border-slate-700/70 bg-slate-800/50 text-slate-300 flex items-center gap-2 {{ $isSelected ? 'selected' : '' }}" data-tag="{{ $tag }}" onclick="toggleTag('{{ $tag }}', this)">
                                <i class="fa-solid {{ $icon }} text-[10px] text-slate-400"></i>
                                <span>{{ $tag }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Nhóm: Tiện Ích Mong Muốn -->
                <div class="mb-6">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-sparkles text-amber-400"></i> Tiện ích phòng mong muốn
                    </label>

                    <div class="flex flex-wrap gap-2">
                        @php
                            $amenityTags = [
                                'Gác lửng' => 'fa-stairs',
                                'Ban công thoáng' => 'fa-sun',
                                'Nuôi thú cưng' => 'fa-paw',
                                'Full nội thất' => 'fa-couch',
                                'Không chung chủ' => 'fa-key',
                                'Giờ giấc tự do' => 'fa-clock',
                                'Có thang máy' => 'fa-elevator',
                                'Phòng mới xây' => 'fa-wand-magic-sparkles',
                                'Gần trạm xe buýt' => 'fa-bus',
                            ];
                        @endphp

                        @foreach($amenityTags as $tag => $icon)
                            @php $isSelected = in_array($tag, $savedTags ?? []); @endphp
                            <div class="pref-chip px-3.5 py-2 rounded-xl text-xs font-semibold border border-slate-700/70 bg-slate-800/50 text-slate-300 flex items-center gap-2 {{ $isSelected ? 'selected' : '' }}" data-tag="{{ $tag }}" onclick="toggleTag('{{ $tag }}', this)">
                                <i class="fa-solid {{ $icon }} text-[10px] text-slate-400"></i>
                                <span>{{ $tag }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Ô nhập thêm tag tùy chỉnh -->
                <div class="mb-8">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2 mb-2" for="custom-tag-input">
                        <i class="fa-solid fa-circle-plus text-emerald-400"></i> Thêm khu vực hoặc yêu cầu khác
                    </label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-xs text-slate-500"></i>
                            <input type="text" id="custom-tag-input" onkeydown="handleCustomTagKeydown(event)" placeholder="Ví dụ: Gần ĐH Kinh Tế, Quận 7, máy giặt riêng..." class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-950/70 border border-slate-800 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
                        </div>
                        <button type="button" onclick="addCustomTag()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 transition-all flex items-center gap-1.5 shrink-0">
                            <i class="fa-solid fa-plus text-[10px]"></i> Thêm
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-3 pt-4 border-t border-slate-800/80">
                    <button type="submit" class="w-full sm:flex-1 py-3.5 px-6 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold text-sm shadow-lg shadow-indigo-500/25 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                        <span>Lưu &amp; Khám Phá Renty</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>

                    <button type="button" onclick="skipPreferences()" class="w-full sm:w-auto py-3.5 px-6 rounded-xl bg-slate-950/80 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 font-semibold text-xs transition-all flex items-center justify-center gap-1.5">
                        <span>Bỏ qua bước này</span>
                    </button>
                </div>
            </form>

        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-2xl mx-auto text-center py-4 text-xs text-slate-600">
        &copy; 2026 SmartRoom &amp; Renty. Nền tảng tìm kiếm và quản lý phòng trọ thế hệ mới.
    </footer>

    <!-- Scripts -->
    <script>
        let selectedTags = @json($savedTags ?? []);

        function updateSelectedCount() {
            const countEl = document.getElementById('selected-count');
            if (countEl) {
                countEl.textContent = `Đã chọn ${selectedTags.length}`;
            }
            document.getElementById('area-tags-input').value = JSON.stringify(selectedTags);
        }

        function toggleTag(tag, element) {
            tag = tag.trim();
            const index = selectedTags.indexOf(tag);
            if (index > -1) {
                selectedTags.splice(index, 1);
                element.classList.remove('selected');
            } else {
                selectedTags.push(tag);
                element.classList.add('selected');
            }
            updateSelectedCount();
        }

        function handleCustomTagKeydown(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addCustomTag();
            }
        }

        function addCustomTag() {
            const input = document.getElementById('custom-tag-input');
            const val = input.value.trim();
            if (!val) return;

            if (!selectedTags.includes(val)) {
                selectedTags.push(val);
                
                // Tạo chip mới chèn vào nhóm khu vực
                const chip = document.createElement('div');
                chip.className = 'pref-chip px-3.5 py-2 rounded-xl text-xs font-semibold border border-slate-700/70 bg-slate-800/50 text-slate-300 flex items-center gap-2 selected';
                chip.setAttribute('data-tag', val);
                chip.innerHTML = `<i class="fa-solid fa-tag text-[10px] text-slate-400"></i><span>${escapeHtml(val)}</span>`;
                chip.onclick = function() {
                    toggleTag(val, chip);
                };
                
                document.getElementById('chip-container').appendChild(chip);
                updateSelectedCount();
            }

            input.value = '';
            input.focus();
        }

        function skipPreferences() {
            const form = document.getElementById('preferences-form');
            const skipInput = document.createElement('input');
            skipInput.type = 'hidden';
            skipInput.name = 'skip';
            skipInput.value = '1';
            form.appendChild(skipInput);
            form.submit();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateSelectedCount();
        });
    </script>
</body>
</html>
