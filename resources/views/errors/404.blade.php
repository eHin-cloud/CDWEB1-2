<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không Tìm Thấy Trang | Renty & SmartRoom</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2310b981'><path d='M12 3L2 12h3v8h14v-8h3L12 3z'/></svg>">
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Space Grotesk"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            500: '#10b981',
                            600: '#059669',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            background-color: #030712;
            background-image: 
                radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(59, 130, 246, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(99, 102, 241, 0.1) 0px, transparent 50%);
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass-panel {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }

        .glow-text {
            text-shadow: 0 0 40px rgba(16, 185, 129, 0.35);
        }

        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(2deg); }
        }

        .floating-anim {
            animation: floatSlow 5s ease-in-out infinite;
        }
    </style>
</head>
<body class="text-slate-100 flex flex-col justify-between selection:bg-emerald-500 selection:text-white">

    <!-- Top Minimal Navigation -->
    <header class="w-full max-w-6xl mx-auto px-6 py-6 flex items-center justify-between z-10">
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 p-[1px] shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                <div class="w-full h-full bg-slate-950 rounded-2xl flex items-center justify-center text-emerald-400">
                    <i class="fa-solid fa-house-chimney-window text-lg"></i>
                </div>
            </div>
            <div>
                <span class="text-lg font-extrabold tracking-tight text-white flex items-center gap-1 font-display">
                    Renty <span class="text-emerald-400">Hub</span>
                </span>
                <span class="text-[10px] uppercase font-bold tracking-widest text-slate-500 block">SmartRoom Platform</span>
            </div>
        </a>

        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="px-4 py-2 rounded-xl bg-slate-900/80 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-xs font-semibold flex items-center gap-2 transition-all">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
            @auth
                @if(Auth::user()->canAccessLandlordDashboard())
                    <a href="{{ route('smartroom.admin') }}" class="px-4 py-2 rounded-xl bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/30 text-indigo-300 text-xs font-semibold flex items-center gap-2 transition-all">
                        <i class="fa-solid fa-gauge-high"></i> Trang Quản Trị
                    </a>
                @endif
            @endauth
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="w-full max-w-4xl mx-auto px-6 py-8 flex flex-col items-center justify-center flex-1 z-10 text-center">
        
        <div class="glass-panel w-full rounded-3xl p-8 md:p-12 relative overflow-hidden">
            <!-- Background Light Accents -->
            <div class="absolute -top-24 -left-24 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Floating 404 Illustration -->
            <div class="floating-anim inline-block mb-6 relative">
                <div class="w-28 h-28 md:w-32 md:h-32 rounded-3xl bg-gradient-to-tr from-slate-900 to-slate-800 border border-slate-700/60 shadow-2xl flex items-center justify-center mx-auto relative">
                    <i class="fa-solid fa-compass-drafting text-4xl md:text-5xl text-emerald-400"></i>
                    <span class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-xs font-bold animate-pulse">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </span>
                </div>
            </div>

            <!-- 404 Big Heading -->
            <h1 class="text-6xl md:text-8xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-sky-400 font-display glow-text leading-none">
                404
            </h1>

            <h2 class="text-xl md:text-2xl font-extrabold text-white mt-4 tracking-tight">
                Không tìm thấy trang hoặc phòng lưu trú
            </h2>

            <p class="text-sm md:text-base text-slate-400 max-w-xl mx-auto mt-3 leading-relaxed">
                Đường dẫn bạn vừa truy cập không tồn tại, đã được đổi tên hoặc phòng trọ này đã được gỡ bỏ khỏi hệ thống.
            </p>

            @if(isset($exception) && $exception->getMessage())
                <div class="mt-4 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900 border border-slate-800 text-xs text-slate-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                    <span>Chi tiết: {{ $exception->getMessage() }}</span>
                </div>
            @endif

            <!-- Action Buttons Grid -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3.5 max-w-md mx-auto">
                <a href="{{ url('/renty') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-bold text-sm flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Khám phá phòng trọ Renty</span>
                </a>

                <a href="{{ url('/renty?view=map') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-700/80 text-white font-semibold text-sm flex items-center justify-center gap-2 transition-all hover:border-slate-600 active:scale-[0.98]">
                    <i class="fa-solid fa-map-location-dot text-emerald-400"></i>
                    <span>Bản đồ thông minh</span>
                </a>
            </div>

            <!-- Quick Suggestions -->
            <div class="mt-10 pt-6 border-t border-slate-800/80 flex flex-wrap items-center justify-center gap-4 text-xs text-slate-400">
                <span class="font-medium text-slate-400">Gợi ý tìm nhanh:</span>
                <a href="{{ url('/renty?keyword=Cầu+Giấy') }}" class="px-2.5 py-1 rounded-lg bg-slate-900/60 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-slate-300 transition-colors">
                    #Cầu Giấy
                </a>
                <a href="{{ url('/renty?keyword=Thanh+Xuân') }}" class="px-2.5 py-1 rounded-lg bg-slate-900/60 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-slate-300 transition-colors">
                    #Thanh Xuân
                </a>
                <a href="{{ url('/renty?keyword=Đống+Đa') }}" class="px-2.5 py-1 rounded-lg bg-slate-900/60 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-slate-300 transition-colors">
                    #Đống Đa
                </a>
                <a href="{{ url('/renty?keyword=Bình+Thạnh') }}" class="px-2.5 py-1 rounded-lg bg-slate-900/60 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-slate-300 transition-colors">
                    #Bình Thạnh
                </a>
            </div>
        </div>

    </main>

    <!-- Footer Simple -->
    <footer class="w-full max-w-6xl mx-auto px-6 py-6 text-center text-xs text-slate-400 z-10">
        <p>&copy; {{ date('Y') }} SmartRoom & Renty Platform. Bản quyền thuộc về Nhóm Đồ Án Kỹ Thuật Phần Mềm.</p>
    </footer>

</body>
</html>
