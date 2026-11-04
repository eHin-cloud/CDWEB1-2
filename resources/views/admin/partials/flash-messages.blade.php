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
            @if(session('warning'))
                <div class="mb-6 p-4 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-300 text-sm font-semibold flex items-center gap-3 animate-fade-in shadow-lg shadow-amber-500/5">
                    <i class="fa-solid fa-triangle-exclamation text-base text-amber-400 shrink-0"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif
