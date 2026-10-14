@php
    $congratData = session('verification_congratulation');
    $shouldShowCelebration = !empty($congratData) || request()->has('celebrate');
    
    // Nếu request test ?celebrate=1 mà không có session, mock dữ liệu mẫu để dễ kiểm tra
    if (empty($congratData) && request()->has('celebrate')) {
        $type = request()->query('celebrate') === 'kyc' ? 'kyc' : 'premium';
        $congratData = [
            'type' => $type,
            'title' => $type === 'premium' ? 'Chúc mừng! Bạn đã đạt Huy hiệu Tích Xanh!' : 'Chúc mừng! Hồ sơ KYC đã được phê duyệt thành công!',
            'badge' => $type === 'premium' ? 'Tích xanh' : 'Đã xác minh KYC',
            'desc' => $type === 'premium'
                ? 'Cơ sở lưu trú của bạn đã hoàn tất kiểm duyệt giấy tờ kinh doanh & an toàn PCCC, chính thức đạt danh hiệu Tích Xanh uy tín và được tăng điểm ưu tiên hiển thị tối đa trên toàn bộ hệ thống Renty!'
                : 'Hồ sơ định danh chủ trọ và tài khoản ngân hàng của bạn đã được kiểm duyệt thành công. Bạn đã có thể nhận tiền thuê phòng, kích hoạt thanh toán VietQR tự động và gắn huy hiệu Đã xác minh trên hệ thống!'
        ];
    }
    
    $isPremiumModal = ($congratData['type'] ?? '') === 'premium';
@endphp

@if($shouldShowCelebration && !empty($congratData))
<div id="celebration-verification-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-slate-950/85 backdrop-blur-xl animate-fade-in transition-all duration-300">
    <!-- Confetti Canvas -->
    <canvas id="celebration-confetti-canvas" class="absolute inset-0 pointer-events-none w-full h-full z-0"></canvas>

    <!-- Modal Content Card -->
    <div class="relative z-10 w-full max-w-lg bg-gradient-to-b {{ $isPremiumModal ? 'from-[#0b1b36] via-[#091124] to-[#040711] border-sky-500/40 shadow-sky-500/20' : 'from-[#06241b] via-[#071714] to-[#040b09] border-emerald-500/40 shadow-emerald-500/20' }} border-2 rounded-3xl p-6 sm:p-8 shadow-2xl text-center overflow-hidden">
        
        <!-- Glow Backdrop circles -->
        <div class="absolute -top-24 -left-24 w-52 h-52 {{ $isPremiumModal ? 'bg-sky-500/20' : 'bg-emerald-500/20' }} rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-52 h-52 {{ $isPremiumModal ? 'bg-indigo-500/20' : 'bg-teal-500/20' }} rounded-full blur-3xl pointer-events-none"></div>

        <!-- Close button -->
        <button type="button" onclick="closeCelebrationModal()" class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-slate-900/80 hover:bg-slate-800 border border-slate-700/60 text-slate-400 hover:text-white flex items-center justify-center transition-all">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>

        <!-- Badge Icon Hero -->
        <div class="relative mx-auto mb-5 w-24 h-24 flex items-center justify-center">
            <div class="absolute inset-0 rounded-full {{ $isPremiumModal ? 'bg-sky-500/20 border-sky-400/40' : 'bg-emerald-500/20 border-emerald-400/40' }} border-2 animate-ping opacity-40"></div>
            <div class="w-20 h-20 rounded-2xl {{ $isPremiumModal ? 'bg-gradient-to-tr from-sky-600 to-indigo-500 shadow-sky-500/40 text-white' : 'bg-gradient-to-tr from-emerald-600 to-teal-500 shadow-emerald-500/40 text-white' }} shadow-xl flex items-center justify-center text-4xl border border-white/20 transform hover:scale-105 transition-transform duration-300">
                @if($isPremiumModal)
                    <i class="fa-solid fa-circle-check"></i>
                @else
                    <i class="fa-solid fa-shield-halved"></i>
                @endif
            </div>
            @if($isPremiumModal)
                <span class="absolute -top-2 -right-1 text-2xl animate-bounce">
                    <i class="fa-solid fa-crown text-amber-400 drop-shadow-md"></i>
                </span>
            @endif
        </div>

        <!-- Category Tag -->
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $isPremiumModal ? 'bg-sky-500/15 border-sky-400/30 text-sky-300' : 'bg-emerald-500/15 border-emerald-400/30 text-emerald-300' }} border text-[11px] font-extrabold uppercase tracking-wider mb-3">
            <i class="fa-solid fa-sparkles"></i>
            {{ $congratData['badge'] ?? ($isPremiumModal ? 'Tích xanh chính thức' : 'Xác minh KYC thành công') }}
        </div>

        <!-- Title -->
        <h2 class="text-xl sm:text-2xl font-black text-slate-100 mb-2 leading-snug">
            {{ $congratData['title'] }}
        </h2>

        <!-- Description -->
        <p class="text-xs sm:text-sm text-slate-300/90 leading-relaxed mb-6 font-normal">
            {{ $congratData['desc'] }}
        </p>

        <!-- Perks List -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 mb-6 text-left">
            @if($isPremiumModal)
                <div class="p-3 rounded-2xl bg-slate-900/80 border border-sky-500/20 backdrop-blur-sm">
                    <div class="w-7 h-7 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center text-xs mb-1.5">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                    </div>
                    <div class="text-[11px] font-bold text-slate-200">Ưu tiên hiển thị Top 1</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Tiếp cận tối đa lượng khách thuê tiềm năng trên Renty.</div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-900/80 border border-sky-500/20 backdrop-blur-sm">
                    <div class="w-7 h-7 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center text-xs mb-1.5">
                        <i class="fa-solid fa-shield-heart"></i>
                    </div>
                    <div class="text-[11px] font-bold text-slate-200">Gắn nhãn Tích Xanh</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Xây dựng lòng tin tuyệt đối, chốt phòng nhanh gấp 3 lần.</div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-900/80 border border-sky-500/20 backdrop-blur-sm">
                    <div class="w-7 h-7 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center text-xs mb-1.5">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div class="text-[11px] font-bold text-slate-200">Mở toàn bộ tính năng</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Đầy đủ công cụ AI, quản lý hợp đồng số và hóa đơn tự động.</div>
                </div>
            @else
                <div class="p-3 rounded-2xl bg-slate-900/80 border border-emerald-500/20 backdrop-blur-sm">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs mb-1.5">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div class="text-[11px] font-bold text-slate-200">Nhận tiền VietQR</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Tiền thuê tự động chuyển thẳng về tài khoản ngân hàng của bạn.</div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-900/80 border border-emerald-500/20 backdrop-blur-sm">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs mb-1.5">
                        <i class="fa-solid fa-file-contract"></i>
                    </div>
                    <div class="text-[11px] font-bold text-slate-200">Ký hợp đồng số OTP</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Tạo và ký hợp đồng thuê nhà điện tử chuẩn pháp lý.</div>
                </div>
                <div class="p-3 rounded-2xl bg-slate-900/80 border border-emerald-500/20 backdrop-blur-sm">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs mb-1.5">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="text-[11px] font-bold text-slate-200">Huy hiệu Đã xác minh</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Nâng cao độ tin cậy của chuỗi nhà trọ đối với khách thuê.</div>
                </div>
            @endif
        </div>

        <!-- Action Button -->
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="button" onclick="closeCelebrationModal()" class="flex-1 py-3 px-5 rounded-2xl {{ $isPremiumModal ? 'bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white shadow-sky-500/30' : 'bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white shadow-emerald-500/30' }} text-sm font-bold shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                <i class="fa-solid fa-rocket"></i>
                Bắt Đầu Quản Lý Cơ Sở Ngay
            </button>
        </div>
    </div>
</div>

<script>
    function closeCelebrationModal() {
        const modal = document.getElementById('celebration-verification-modal');
        if (modal) {
            modal.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(() => modal.remove(), 300);
        }
    }

    // Hiệu ứng pháo hoa Confetti siêu nhẹ (Pure Canvas)
    (function initCelebrationConfetti() {
        const canvas = document.getElementById('celebration-confetti-canvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        const colors = {{ $isPremiumModal ? "['#38bdf8', '#818cf8', '#60a5fa', '#f59e0b', '#ffffff']" : "['#34d399', '#10b981', '#14b8a6', '#f59e0b', '#ffffff']" }};
        const pieces = [];
        const numberOfPieces = 80;

        for (let i = 0; i < numberOfPieces; i++) {
            pieces.push({
                x: Math.random() * width,
                y: Math.random() * height - height,
                rotation: Math.random() * 360,
                size: Math.random() * 8 + 5,
                color: colors[Math.floor(Math.random() * colors.length)],
                speedY: Math.random() * 3 + 2,
                speedX: Math.random() * 2 - 1,
                rotationSpeed: Math.random() * 4 - 2
            });
        }

        let animationId;
        function render() {
            ctx.clearRect(0, 0, width, height);
            pieces.forEach(p => {
                p.y += p.speedY;
                p.x += p.speedX;
                p.rotation += p.rotationSpeed;

                ctx.save();
                ctx.translate(p.x, p.y);
                ctx.rotate((p.rotation * Math.PI) / 180);
                ctx.fillStyle = p.color;
                ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * 0.6);
                ctx.restore();

                if (p.y > height) {
                    p.y = -20;
                    p.x = Math.random() * width;
                }
            });
            animationId = requestAnimationFrame(render);
        }

        render();

        // Tự động dừng sau 6 giây để tiết kiệm tài nguyên máy
        setTimeout(() => {
            if (animationId) cancelAnimationFrame(animationId);
        }, 6000);

        window.addEventListener('resize', () => {
            if (canvas) {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            }
        });
    })();
</script>
@endif
