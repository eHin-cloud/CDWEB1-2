@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();

        // Xây dựng danh sách trang hiển thị thông minh: 1 2 3 ... 15 (hoặc 1 ... 7 8 9 ... 15)
        $items = [];
        if ($last <= 7) {
            for ($i = 1; $i <= $last; $i++) {
                $items[] = ['type' => 'page', 'num' => $i];
            }
        } else {
            // Luôn có trang 1
            $items[] = ['type' => 'page', 'num' => 1];

            if ($current <= 3) {
                // Đang ở đầu: 1 2 3 ... last
                for ($i = 2; $i <= min(3, $last - 1); $i++) {
                    $items[] = ['type' => 'page', 'num' => $i];
                }
                if ($last > 4) {
                    $items[] = ['type' => 'dots', 'id' => 'dots-after'];
                }
            } elseif ($current >= $last - 2) {
                // Đang ở cuối: 1 ... (last-2) (last-1) last
                $items[] = ['type' => 'dots', 'id' => 'dots-before'];
                for ($i = $last - 2; $i < $last; $i++) {
                    if ($i > 1) {
                        $items[] = ['type' => 'page', 'num' => $i];
                    }
                }
            } else {
                // Đang ở giữa: 1 ... (current-1) current (current+1) ... last
                $items[] = ['type' => 'dots', 'id' => 'dots-before'];
                for ($i = $current - 1; $i <= $current + 1; $i++) {
                    $items[] = ['type' => 'page', 'num' => $i];
                }
                $items[] = ['type' => 'dots', 'id' => 'dots-after'];
            }

            // Luôn có trang cuối
            if ($last > 1) {
                $items[] = ['type' => 'page', 'num' => $last];
            }
        }
    @endphp

    <nav role="navigation" aria-label="Pagination Navigation" class="smartroom-pagination-nav flex items-center justify-between w-full select-none">
        <style>
            .smartroom-pagination-nav .pagination-desktop {
                display: flex !important;
            }
            .smartroom-pagination-nav .pagination-mobile {
                display: none !important;
            }
            @media (max-width: 767px) {
                .smartroom-pagination-nav .pagination-desktop {
                    display: none !important;
                }
                .smartroom-pagination-nav .pagination-mobile {
                    display: flex !important;
                }
            }
        </style>

        <!-- Mobile Simple View -->
        <div class="pagination-mobile justify-between items-center flex-1 gap-2">
            @if ($paginator->onFirstPage())
                <span class="px-3.5 py-2 text-xs font-bold text-slate-600 bg-slate-900/20 border border-slate-800/80 rounded-xl cursor-default select-none">
                    <i class="fa-solid fa-chevron-left text-[10px] mr-1"></i> Trước
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3.5 py-2 text-xs font-bold text-slate-300 bg-slate-900/40 border border-slate-800 hover:border-indigo-500/40 hover:text-white transition-all active:scale-95 flex items-center rounded-xl">
                    <i class="fa-solid fa-chevron-left text-[10px] mr-1"></i> Trước
                </a>
            @endif

            <span class="text-xs font-bold text-slate-400">
                <span class="text-indigo-400 font-extrabold">{{ $current }}</span> / {{ $last }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3.5 py-2 text-xs font-bold text-slate-300 bg-slate-900/40 border border-slate-800 hover:border-indigo-500/40 hover:text-white transition-all active:scale-95 flex items-center rounded-xl">
                    Sau <i class="fa-solid fa-chevron-right text-[10px] ml-1"></i>
                </a>
            @else
                <span class="px-3.5 py-2 text-xs font-bold text-slate-600 bg-slate-900/20 border border-slate-800/80 rounded-xl cursor-default select-none">
                    Sau <i class="fa-solid fa-chevron-right text-[10px] ml-1"></i>
                </span>
            @endif
        </div>

        <!-- Desktop Advanced View -->
        <div class="pagination-desktop flex-1 items-center justify-between gap-4">
            <!-- Tóm tắt số lượng -->
            <div>
                <p class="text-xs text-slate-400">
                    Hiển thị từ
                    <span class="font-extrabold text-slate-200">{{ $paginator->firstItem() }}</span>
                    đến
                    <span class="font-extrabold text-slate-200">{{ $paginator->lastItem() }}</span>
                    trong tổng số
                    <span class="font-extrabold text-indigo-400">{{ $paginator->total() }}</span>
                    thành viên
                </p>
            </div>

            <!-- Cụm nút trang & Đi nhanh -->
            <div class="flex items-center gap-2">
                <div class="relative z-0 inline-flex shadow-sm rounded-xl gap-1.5 items-center">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="relative inline-flex items-center px-3 py-2 rounded-xl border border-slate-800/60 bg-slate-900/20 text-xs font-bold text-slate-600 cursor-default select-none" aria-hidden="true">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 rounded-xl border border-slate-800 bg-slate-900/40 text-xs font-bold text-slate-400 hover:text-white hover:border-indigo-500/50 hover:shadow-lg hover:shadow-indigo-500/5 transition-all active:scale-95" aria-label="@lang('pagination.previous')" title="Trang trước">
                            <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        </a>
                    @endif

                    {{-- Dãy nút phân trang thông minh kèm Jump Box tại dấu "..." --}}
                    @foreach ($items as $item)
                        @if ($item['type'] === 'page')
                            @if ($item['num'] == $current)
                                <span aria-current="page">
                                    <span class="relative inline-flex items-center px-3.5 py-2 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 text-white text-xs font-extrabold border border-indigo-500/25 shadow-lg shadow-indigo-600/20 scale-105 select-none">{{ $item['num'] }}</span>
                                </span>
                            @else
                                <a href="{{ $paginator->url($item['num']) }}" class="relative inline-flex items-center px-3.5 py-2 rounded-xl border border-slate-800/80 bg-slate-900/40 text-xs font-bold text-slate-400 hover:text-slate-100 hover:border-indigo-500/40 hover:bg-slate-800/50 transition-all active:scale-95" aria-label="Trang {{ $item['num'] }}">
                                    {{ $item['num'] }}
                                </a>
                            @endif
                        @elseif ($item['type'] === 'dots')
                            {{-- Nút "..." có thể điền số để nhảy nhanh --}}
                            <div class="relative inline-flex items-center jump-box" data-dots-id="{{ $item['id'] }}">
                                <button type="button" 
                                        onclick="activateJumpInput(this)" 
                                        class="jump-btn px-2.5 py-2 rounded-xl border border-slate-800/80 bg-slate-900/40 text-xs font-bold text-slate-400 hover:text-indigo-300 hover:border-indigo-500/50 hover:bg-indigo-500/10 transition-all flex items-center justify-center gap-1 group" 
                                        title="Nhấn để nhập số trang nhanh (1 - {{ $last }})">
                                    <span class="font-black tracking-wider">...</span>
                                    <i class="fa-solid fa-bolt text-[8px] text-indigo-400/50 group-hover:text-amber-400 transition-colors"></i>
                                </button>
                                
                                <div class="jump-form items-center gap-1 bg-slate-950 border border-indigo-500/80 rounded-xl px-1.5 py-1 shadow-2xl shadow-indigo-500/20" style="display: none;">
                                    <input type="number" 
                                           min="1" 
                                           max="{{ $last }}" 
                                           placeholder="1-{{ $last }}"
                                           class="jump-input w-12 bg-slate-900 border border-slate-700/80 focus:border-indigo-500 rounded-lg px-1 py-1 text-xs text-center font-bold text-indigo-200 outline-none"
                                           onkeydown="if(event.key==='Enter'){event.preventDefault();submitJumpPage(this, {{ $last }});} else if(event.key==='Escape'){deactivateJumpInput(this);}">
                                    <button type="button" 
                                            onclick="submitJumpPage(this.previousElementSibling, {{ $last }})"
                                            class="px-2 py-1 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-bold transition-all shadow flex items-center justify-center"
                                            title="Đi tới trang này">
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </button>
                                    <button type="button" 
                                            onclick="deactivateJumpInput(this)"
                                            class="w-5 h-5 text-slate-400 hover:text-slate-200 flex items-center justify-center rounded text-xs leading-none"
                                            title="Hủy">
                                        &times;
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 rounded-xl border border-slate-800 bg-slate-900/40 text-xs font-bold text-slate-400 hover:text-white hover:border-indigo-500/50 hover:shadow-lg hover:shadow-indigo-500/5 transition-all active:scale-95" aria-label="@lang('pagination.next')" title="Trang sau">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="relative inline-flex items-center px-3 py-2 rounded-xl border border-slate-800/60 bg-slate-900/20 text-xs font-bold text-slate-600 cursor-default select-none" aria-hidden="true">
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </span>
                        </span>
                    @endif
                </div>

                {{-- Ô Đi đến trang nhanh bổ sung kế bên --}}
                <div class="hidden lg:inline-flex items-center gap-1.5 pl-2 ml-1 border-l border-slate-800/80">
                    <span class="text-[11px] text-slate-500 font-semibold">Tới:</span>
                    <input type="number" 
                           min="1" 
                           max="{{ $last }}" 
                           placeholder="{{ $current }}"
                           onkeydown="if(event.key==='Enter'){event.preventDefault();submitJumpPage(this, {{ $last }});}"
                           class="w-12 bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-indigo-500 rounded-xl px-1.5 py-1.5 text-xs text-center font-bold text-slate-200 outline-none transition-all">
                    <button type="button" 
                            onclick="submitJumpPage(this.previousElementSibling, {{ $last }})"
                            class="px-2.5 py-1.5 bg-slate-900 hover:bg-indigo-600 border border-slate-800 hover:border-indigo-500 text-slate-300 hover:text-white rounded-xl text-xs font-bold transition-all shadow flex items-center justify-center gap-1"
                            title="Đi tới số trang đã nhập">
                        <span>Đi</span>
                        <i class="fa-solid fa-arrow-right text-[9px]"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <script>
        function activateJumpInput(btn) {
            const box = btn.closest('.jump-box');
            if (!box) return;
            btn.style.display = 'none';
            const form = box.querySelector('.jump-form');
            if (form) {
                form.style.display = 'inline-flex';
                const input = form.querySelector('.jump-input');
                if (input) {
                    input.focus();
                    input.select();
                }
            }
        }

        function deactivateJumpInput(el) {
            const box = el.closest('.jump-box');
            if (!box) return;
            const btn = box.querySelector('.jump-btn');
            const form = box.querySelector('.jump-form');
            if (form) {
                form.style.display = 'none';
            }
            if (btn) {
                btn.style.display = 'inline-flex';
            }
        }

        function submitJumpPage(input, maxPage) {
            if (!input) return;
            let val = parseInt(input.value, 10);
            if (isNaN(val)) return;
            if (val < 1) val = 1;
            if (val > maxPage) val = maxPage;

            const url = new URL(window.location.href);
            url.searchParams.set('page', val);
            window.location.href = url.toString();
        }

        // Tự động đóng jump input khi bấm ra ngoài
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.jump-box')) {
                document.querySelectorAll('.jump-box').forEach(box => {
                    const btn = box.querySelector('.jump-btn');
                    const form = box.querySelector('.jump-form');
                    if (form && form.style.display !== 'none') {
                        form.style.display = 'none';
                        if (btn) btn.style.display = 'inline-flex';
                    }
                });
            }
        });
    </script>
@endif
