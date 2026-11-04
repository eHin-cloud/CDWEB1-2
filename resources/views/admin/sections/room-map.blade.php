            <!-- SECTION 2: VISUAL ROOM MAP -->
            <section id="room-map-section" class="tab-content hidden space-y-8 animate-fade-in">
                <!-- PROPERTY LOCATION & BUILDING INFO BANNER -->
                <div class="glass-card property-banner-card rounded-2xl p-5 border border-slate-800/80 bg-gradient-to-r from-slate-900/90 via-[#0d121f]/95 to-indigo-950/40 relative overflow-hidden shadow-xl">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shrink-0 text-xl shadow-lg shadow-indigo-500/10">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">
                                        Vị trí cơ sở trọ
                                    </span>
                                    @if(isset($buildings) && $buildings->count() > 1)
                                        <span class="text-[10px] text-slate-400">({{ $buildings->count() }} tòa nhà)</span>
                                    @endif
                                </div>
                                <h3 id="current-building-name-display" class="text-base sm:text-lg font-black text-white mt-1">
                                    {{ $primaryBuilding?->name ?? ($tenant?->name ?? 'Cơ sở lưu trú SmartRoom') }}
                                </h3>
                                <p id="current-building-address-display" class="text-xs text-slate-300 mt-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-location-dot text-rose-400 text-sm shrink-0"></i>
                                    <span>{{ $primaryBuilding?->address ?? ($tenant?->address ?? 'Số 12 Ngõ 105 Xuân Thủy, Cầu Giấy, Hà Nội') }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Action controls: Open Google Maps & Building selector if multiple -->
                        <div class="flex flex-wrap items-center gap-3">
                            @php
                                $mapQuery = urlencode($primaryBuilding?->address ?? ($primaryBuilding?->name ?? 'Hà Nội'));
                            @endphp
                            <a id="current-building-gmaps-btn" href="https://www.google.com/maps/search/?api=1&query={{ $mapQuery }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition-all flex items-center gap-2 shadow-lg shadow-blue-600/20">
                                <i class="fa-brands fa-google text-sm"></i>
                                <span>Xem trên Google Maps</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>

                            @if(isset($buildings) && $buildings->count() > 1)
                                <div class="building-filter-bar flex items-center gap-1 bg-slate-900/90 p-1 rounded-xl border border-slate-800 text-xs">
                                    <button type="button" onclick="filterByBuilding('all', this)" class="building-tab-btn px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-bold transition-all">
                                        Tất cả tòa
                                    </button>
                                    @foreach($buildings as $b)
                                        <button type="button" 
                                                data-building-id="{{ $b->id }}"
                                                data-building-name="{{ $b->name }}"
                                                data-building-address="{{ $b->address }}"
                                                onclick="filterByBuilding({{ $b->id }}, this)" 
                                                class="building-tab-btn px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 font-semibold transition-all">
                                            {{ $b->name }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Filter buttons and color legend -->
                <div class="flex flex-wrap items-center justify-between gap-4 bg-slate-900/40 border border-slate-800/80 p-4 rounded-2xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <button data-filter="all" onclick="filterRooms('all', this)" class="room-filter-btn px-3.5 py-2 text-xs font-bold rounded-xl bg-indigo-600 text-white transition-all">
                            Tất cả (<span id="filter-count-all">{{ $totalRooms }}</span>)
                        </button>
                        <button data-filter="empty" onclick="filterRooms('empty', this)" class="room-filter-btn px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Trống (<span id="filter-count-empty">{{ $emptyRooms }}</span>)
                        </button>
                        <button data-filter="occupied" onclick="filterRooms('occupied', this)" class="room-filter-btn px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Đã thuê (<span id="filter-count-occupied">{{ $occupiedRooms }}</span>)
                        </button>
                        <button data-filter="overdue" onclick="filterRooms('overdue', this)" class="room-filter-btn px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Nợ phí (<span id="filter-count-overdue">{{ $overdueRooms }}</span>)
                        </button>
                        <button data-filter="cleaning" onclick="filterRooms('cleaning', this)" class="room-filter-btn px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Cần dọn (<span id="filter-count-cleaning">{{ $cleaningRooms ?? 0 }}</span>)
                        </button>
                        <button data-filter="maintenance" onclick="filterRooms('maintenance', this)" class="room-filter-btn px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                            Bảo trì (<span id="filter-count-maintenance">{{ $maintenanceRooms ?? 0 }}</span>)
                        </button>
                        <div class="relative ml-2">
                            <input type="text" id="room-map-search-input" oninput="filterRoomsByKeyword(this.value)" placeholder="Tìm số phòng, cư dân..." class="px-3 py-2 text-xs rounded-xl bg-slate-900 border border-slate-800 text-slate-200 placeholder-slate-500 focus:border-indigo-500 focus:outline-none w-44">
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-4 sm:gap-6 text-xs font-bold text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-md bg-emerald-500 border border-emerald-400/30"></span>
                            <span>Trống</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-md bg-red-500 border border-red-400/30"></span>
                            <span>Đã thuê</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-md bg-amber-500 border border-amber-400/30"></span>
                            <span>Nợ tiền</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-md bg-orange-500 border border-orange-400/30 animate-pulse"></span>
                            <span>Dọn dẹp</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-md bg-slate-500 border border-slate-400/30"></span>
                            <span>Bảo trì</span>
                        </div>
                    </div>
                <!-- Floor filter toolbar (FEAT_13) -->
                <div class="flex items-center gap-2 flex-wrap bg-slate-900/40 border border-slate-800/80 p-3 rounded-2xl">
                    <span class="text-xs font-bold text-slate-400 flex items-center gap-1.5 shrink-0">
                        <i class="fa-solid fa-layer-group text-indigo-400"></i> Lọc Tầng:
                    </span>
                    <button type="button" data-floor-filter="all" onclick="filterByFloor('all', this)" class="floor-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-600 text-white transition-all">
                        Tất cả tầng
                    </button>
                    @php
                        $existingFloors = $roomsByFloor->keys()->map(fn($f) => (int)$f)->filter(fn($f) => $f > 0)->sort()->values();
                        $maxBuildingFloors = (int) ($buildings->max('total_floors') ?? ($existingFloors->max() ?? 1));
                        $maxFloorNumber = max($maxBuildingFloors, $existingFloors->max() ?? 1);
                        $floorList = range(1, max(1, $maxFloorNumber));
                    @endphp
                    @foreach($floorList as $fl)
                        <button type="button" 
                                data-floor-filter="{{ $fl }}" 
                                onclick="filterByFloor('{{ $fl }}', this)" 
                                class="floor-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-900 text-slate-400 hover:text-slate-200 hover:bg-slate-800 transition-all">
                            Tầng {{ $fl }}
                        </button>
                    @endforeach
                </div>

                <!-- Empty state khi tầng được chọn không có phòng nào (ERR_13_03) -->
                <div id="floor-empty-state" class="hidden p-12 text-center rounded-3xl bg-slate-900/40 border border-slate-800/80 space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-2xl mx-auto mb-2">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <h4 class="text-base font-bold text-slate-200">Tầng được chọn chưa có phòng nào được tạo.</h4>
                    <p class="text-xs text-slate-400 max-w-md mx-auto">Vui lòng chọn tầng khác trên bộ lọc hoặc tiến hành thêm phòng mới vào sơ đồ để hoàn tất cấu hình tòa nhà.</p>
                    <div class="pt-2">
                        <a href="{{ route('admin.rooms.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-lg shadow-indigo-600/30">
                            <i class="fa-solid fa-plus"></i>
                            <span>Cấu hình thêm phòng ngay</span>
                        </a>
                    </div>
                </div>

                <!-- Floors and room grid -->
                <div class="space-y-8" id="room-matrix-container">
                    @foreach($roomsByFloor as $floor => $floorRooms)
                    <div class="floor-group" data-floor="{{ $floor }}">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-indigo-400"></i> Tầng {{ $floor }}
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            @foreach($floorRooms as $room)
                            @php
                                $resident = $room->residents->first();
                                $latestBill = $room->utilityRecords->first();
                                $elecUsed = $latestBill ? ($latestBill->new_electricity - $latestBill->old_electricity) : 0;
                                $waterUsed = $latestBill ? ($latestBill->new_water - $latestBill->old_water) : 0;
                                $statusLabel = $room->status_label;
                                $statusClass = $room->status_class;
                                $badgeClass = $room->badge_class;
                                $totalBill = $latestBill ? ($room->price + ($elecUsed * $latestBill->electricity_price) + ($waterUsed * $latestBill->water_price) + 150000) : 0;
                            @endphp
                            <div id="room-card-{{ $room->id }}"
                                 data-room-id="{{ $room->id }}"
                                 data-room-number="{{ $room->room_number }}"
                                 data-room-status="{{ $room->status }}"
                                 data-building-id="{{ $room->building_id }}"
                                 data-building-name="{{ $room->building->name ?? '' }}"
                                 data-building-address="{{ $room->building->address ?? '' }}"
                                 data-resident-name="{{ $resident ? $resident->name : '' }}"
                                 data-resident-phone="{{ $resident ? $resident->phone : '' }}"
                                 data-price="{{ number_format($room->price) }}đ"
                                 data-elec-used="{{ $elecUsed }} kWh"
                                 data-water-used="{{ $waterUsed }} m3"
                                 data-total-bill="{{ number_format($totalBill) }}đ"
                                 data-latest-bill-id="{{ $latestBill ? $latestBill->id : '' }}"
                                 onclick="openRoomDetailById({{ $room->id }})" 
                                 class="room-card {{ $statusClass }} glass-card rounded-2xl p-5 cursor-pointer relative overflow-hidden group transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/10">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="text-lg font-extrabold text-slate-200">P. {{ $room->room_number }}</span>
                                    <span class="room-badge px-2 py-0.5 rounded text-[10px] font-extrabold border {{ $badgeClass }}">{{ $statusLabel }}</span>
                                </div>
                                <div class="text-[10px] text-indigo-400 font-semibold truncate flex items-center gap-1 mb-2" title="{{ $room->building->name ?? '' }} - {{ $room->building->address ?? '' }}">
                                    <i class="fa-solid fa-location-dot text-rose-400 text-[9px] shrink-0"></i>
                                    <span class="truncate">{{ $room->building->name ?? 'Tòa nhà' }}</span>
                                </div>
                                @if($resident && in_array($room->status, ['occupied', 'overdue']))
                                    <h4 class="room-resident text-xs font-bold text-slate-400 truncate mb-1">Cư dân: {{ $resident->name }}</h4>
                                @else
                                    <h4 class="room-resident text-xs font-bold text-slate-500 italic mb-1">Chưa có cư dân</h4>
                                @endif
                                <p class="text-[10px] text-slate-500">Giá phòng: {{ number_format($room->price) }}đ</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- HUY HIỆU MẤT KẾT NỐI SSE THỜI GIAN THỰC (ERR_13_01) -->
                <div id="sse-reconnect-badge" class="hidden fixed top-20 right-6 z-50 items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-amber-500/20 border border-amber-500/50 text-amber-300 text-xs font-bold shadow-xl shadow-amber-500/10 backdrop-blur-md animate-pulse transition-all">
                    <i class="fa-solid fa-triangle-exclamation text-amber-400 text-sm"></i>
                    <span id="sse-reconnect-text">Mất kết nối dữ liệu thời gian thực. Hệ thống đang tự động kết nối lại...</span>
                </div>

                </div>
            </section>

