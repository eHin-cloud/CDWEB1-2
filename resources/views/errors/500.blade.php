<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Lỗi Máy Chủ | Renty & SmartRoom</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23f59e0b'><path d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z'/></svg>">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Space Grotesk"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #030712;
            background-image: 
                radial-gradient(at 0% 0%, rgba(245, 158, 11, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(99, 102, 241, 0.1) 0px, transparent 50%);
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glass-panel {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }
    </style>
</head>
<body class="text-slate-100 flex flex-col justify-between selection:bg-amber-500 selection:text-white">

    <header class="w-full max-w-6xl mx-auto px-6 py-6 flex items-center justify-between z-10">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 p-[1px] shadow-lg">
                <div class="w-full h-full bg-slate-950 rounded-2xl flex items-center justify-center text-amber-400">
                    <i class="fa-solid fa-server text-lg"></i>
                </div>
            </div>
            <div>
                <span class="text-lg font-extrabold tracking-tight text-white flex items-center gap-1 font-display">
                    Smart<span class="text-amber-400">Room</span>
                </span>
                <span class="text-[10px] uppercase font-bold tracking-widest text-slate-500 block">System Service</span>
            </div>
        </a>

        <a href="javascript:location.reload()" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white text-xs font-semibold flex items-center gap-2 transition-all">
            <i class="fa-solid fa-rotate-right"></i> Tải lại trang
        </a>
    </header>

    <main class="w-full max-w-3xl mx-auto px-6 py-8 flex flex-col items-center justify-center flex-1 z-10 text-center">
        <div class="glass-panel w-full rounded-3xl p-8 md:p-12 relative overflow-hidden">
            <div class="w-24 h-24 rounded-3xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mx-auto text-4xl mb-6 shadow-xl">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>

            <h1 class="text-6xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-300 font-display">
                500
            </h1>

            <h2 class="text-xl md:text-2xl font-extrabold text-white mt-4 tracking-tight">
                Hệ thống máy chủ đang gặp gián đoạn tạm thời
            </h2>

            <p class="text-sm text-slate-400 max-w-md mx-auto mt-2.5 leading-relaxed">
                Đội ngũ kỹ thuật đã ghi nhận và đang khôi phục kết nối. Bạn vui lòng tải lại trang sau ít phút.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 max-w-sm mx-auto">
                <button onclick="location.reload()" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold text-xs transition-all shadow-lg shadow-amber-600/20 cursor-pointer">
                    <i class="fa-solid fa-rotate-right mr-1"></i> Tải lại ngay
                </button>
                <a href="{{ url('/') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700 text-white font-semibold text-xs transition-all">
                    Về Trang Chủ
                </a>
            </div>
        </div>
    </main>

    <footer class="w-full max-w-6xl mx-auto px-6 py-6 text-center text-xs text-slate-500 z-10">
        <p>&copy; {{ date('Y') }} SmartRoom Platform. Error Recovery System.</p>
    </footer>

</body>
</html>
