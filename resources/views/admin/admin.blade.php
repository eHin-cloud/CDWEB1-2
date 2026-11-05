<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Hệ thống quản lý SmartRoom - Trang quản trị nhà trọ.">
    <title>SmartRoom - Quản Trị Nhà Trọ Cao Cấp</title>
    @include('admin.partials.theme-head-script')
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
</head>
<body class="bg-[#080b11] text-slate-100 min-h-screen selection:bg-indigo-500 selection:text-white overflow-hidden">
    @php
        $isLandlord = Auth::user()?->isLandlord();
    @endphp

    <!-- Decorative glows -->
    <div class="absolute top-[-10%] right-[-10%] w-[400px] h-[400px] rounded-full bg-indigo-600/5 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-[400px] h-[400px] rounded-full bg-emerald-600/5 blur-[100px] pointer-events-none"></div>

    <!-- SIDEBAR -->
    @include('admin.partials.sidebar')

    <!-- MAIN APP WRAPPER -->
    <div id="admin-shell" class="ml-64 min-w-0 flex flex-col h-screen overflow-y-auto relative z-10 transition-[margin-left] duration-200">
        

        <!-- TOP NAVBAR -->
        @include('admin.partials.navbar')

        <!-- CONTENT PANEL -->
        <main class="p-8 flex-grow overflow-y-auto">
            @include('admin.partials.flash-messages')

            <!-- SECTION PROFILE: HỒ SƠ & XÁC MINH -->
            @include('admin.sections.profile')

            <!-- SECTION 1: DASHBOARD OVERVIEW -->
            @include('admin.sections.dashboard')

            <!-- SECTION 2: VISUAL ROOM MAP -->
            @include('admin.sections.room-map')

            <!-- SECTION 3: UTILITY RECORD INPUT -->
            @include('admin.sections.utility')

            <!-- SECTION 4: RESIDENT MANAGEMENT -->
            @include('admin.sections.resident')

            <!-- SECTION 5: CONTRACT MANAGEMENT -->
            @include('admin.sections.contract')

            <!-- SECTION 6: CONTACT REQUESTS -->
            @include('admin.sections.contact')

            <!-- SECTION TICKET: QUẢN LÝ SỰ CỐ & BÁO HỎNG -->
            @include('admin.sections.ticket')
        </main>
    </div>

    <!-- MODALS -->
    @include('admin.modals.ticket')
    @include('admin.modals.resident')
    @include('admin.modals.contract')
    @include('admin.modals.system')
    @include('admin.modals.room-detail')
    @include('admin.modals.utility')

    <!-- SCRIPTS -->
    @include('admin.scripts.admin-scripts')

    <script src="{{ asset('js/admin-sidebar.js') }}"></script>
</body>
</html>
