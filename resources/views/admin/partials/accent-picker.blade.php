<!-- Bộ chọn Double Màu (Cặp Màu Kép) giao diện Admin -->
<div class="relative" id="admin-accent-picker-container">
    <button type="button" 
            id="admin-accent-btn" 
            onclick="toggleAccentDropdown(event)" 
            class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-all shadow-sm focus:outline-none"
            title="Tùy chỉnh cặp màu kép (Double Accent)">
        <span id="current-accent-dot" class="w-3.5 h-3.5 rounded-full shadow-sm" style="background: var(--duo-gradient, linear-gradient(135deg, #8b5cf6, #10b981));"></span>
        <span class="hidden sm:inline text-[11px] font-bold">Cặp màu</span>
        <i class="fa-solid fa-chevron-down text-[9px] text-slate-500 ml-0.5"></i>
    </button>
    <!-- Dropdown danh sách 4 cặp màu kép -->
    <div id="admin-accent-dropdown" class="hidden absolute right-0 mt-2 w-64 rounded-2xl bg-[#0d121f]/95 border border-slate-800 shadow-2xl backdrop-blur-xl p-3 z-50">
        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 px-1 flex items-center justify-between">
            <span>4 Cặp màu kép</span>
            <i class="fa-solid fa-wand-magic-sparkles text-amber-400 text-xs"></i>
        </div>
        <div class="space-y-1.5">
            <!-- 1. Tím Neon & Xanh Bạc Hà (Cyberpunk Neon) -->
            <button type="button" onclick="setDuoTheme('violet-mint')" class="duo-color-btn w-full flex items-center justify-between p-2 rounded-xl hover:bg-slate-800/60 border border-transparent transition-all cursor-pointer group" data-duo="violet-mint">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg shadow-sm flex overflow-hidden border border-white/20">
                        <span class="w-1/2 h-full bg-[#8b5cf6]"></span>
                        <span class="w-1/2 h-full bg-[#10b981]"></span>
                    </div>
                    <div class="text-left">
                        <div class="text-xs font-bold text-slate-200 group-hover:text-white">Tím & Bạc Hà</div>
                        <div class="text-[9px] text-slate-400 font-semibold">Cyberpunk Neon</div>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md">#1</span>
            </button>

            <!-- 2. Băng Tuyết & Rực Lửa (Băng & Hỏa) -->
            <button type="button" onclick="setDuoTheme('ice-fire')" class="duo-color-btn w-full flex items-center justify-between p-2 rounded-xl hover:bg-slate-800/60 border border-transparent transition-all cursor-pointer group" data-duo="ice-fire">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg shadow-sm flex overflow-hidden border border-white/20">
                        <span class="w-1/2 h-full bg-[#0284c7]"></span>
                        <span class="w-1/2 h-full bg-[#f97316]"></span>
                    </div>
                    <div class="text-left">
                        <div class="text-xs font-bold text-slate-200 group-hover:text-white">Băng & Hỏa</div>
                        <div class="text-[9px] text-slate-400 font-semibold">Hàn Băng vs Rực Lửa</div>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-orange-400 bg-orange-500/10 px-2 py-0.5 rounded-md">#2</span>
            </button>

            <!-- 3. Hồng Neon & Xanh Lam Cyan (Synthwave) -->
            <button type="button" onclick="setDuoTheme('pink-cyan')" class="duo-color-btn w-full flex items-center justify-between p-2 rounded-xl hover:bg-slate-800/60 border border-transparent transition-all cursor-pointer group" data-duo="pink-cyan">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg shadow-sm flex overflow-hidden border border-white/20">
                        <span class="w-1/2 h-full bg-[#f43f5e]"></span>
                        <span class="w-1/2 h-full bg-[#06b6d4]"></span>
                    </div>
                    <div class="text-left">
                        <div class="text-xs font-bold text-slate-200 group-hover:text-white">Hồng & Xanh Cyan</div>
                        <div class="text-[9px] text-slate-400 font-semibold">Synthwave Wave</div>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded-md">#3</span>
            </button>

            <!-- 4. Cam Hổ Phách & Xanh Biển Sâu (Sunset Horizon) -->
            <button type="button" onclick="setDuoTheme('amber-blue')" class="duo-color-btn w-full flex items-center justify-between p-2 rounded-xl hover:bg-slate-800/60 border border-transparent transition-all cursor-pointer group" data-duo="amber-blue">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg shadow-sm flex overflow-hidden border border-white/20">
                        <span class="w-1/2 h-full bg-[#f59e0b]"></span>
                        <span class="w-1/2 h-full bg-[#3b82f6]"></span>
                    </div>
                    <div class="text-left">
                        <div class="text-xs font-bold text-slate-200 group-hover:text-white">Cam & Xanh Biển</div>
                        <div class="text-[9px] text-slate-400 font-semibold">Sunset Horizon</div>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-md">#4</span>
            </button>
        </div>
    </div>
</div>

