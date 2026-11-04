    <!-- JS Logic -->
    <script>
        function openConfirmOfflineSignModal(contract) {
            const modal = document.getElementById('modal-confirm-offline-sign');
            const form = document.getElementById('form-confirm-offline-sign');
            const codeEl = document.getElementById('offline-modal-code');
            const residentEl = document.getElementById('offline-modal-resident');
            const roomEl = document.getElementById('offline-modal-room');

            if (!contract || !modal || !form) return;

            codeEl.innerText = contract.contract_code || '---';
            residentEl.innerText = (contract.resident && contract.resident.name) ? contract.resident.name : (contract.lessee_name || 'Cư dân');
            roomEl.innerText = (contract.room && contract.room.room_number) ? ('Phòng ' + contract.room.room_number) : 'Phòng lưu trú';

            form.action = '/smartroom/admin/contract/' + contract.id + '/confirm-offline-sign';
            modal.classList.remove('hidden');
        }

        function toggleConfirmOfflineSignModal(show) {
            const modal = document.getElementById('modal-confirm-offline-sign');
            if (modal) {
                if (show) modal.classList.remove('hidden');
                else modal.classList.add('hidden');
            }
        }
        const currentUserIsLandlord = @json($isLandlord);
        const canReceiveOnlinePayments = {{ in_array(($tenant->verification_status ?? 'unverified'), ['kyc_verified', 'premium_pending', 'premium_verified'], true) ? 'true' : 'false' }};

        function requireKycForOnlinePayments() {
            alert('Can hoan tat KYC nhan tien truoc khi hien thi VietQR/chuyen khoan. Vui long gui CCCD va tai khoan ngan hang tren the xac minh o dau trang.');
            return false;
        }

        // Setup custom toast notification override for alert()
        (function() {
            const toastStyle = document.createElement('style');
            toastStyle.innerHTML = `
                .custom-toast-container {
                    position: fixed;
                    top: 24px;
                    right: 24px;
                    z-index: 9999;
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                    pointer-events: none;
                }
                .custom-toast {
                    min-width: 320px;
                    max-width: 450px;
                    background: rgba(15, 23, 42, 0.9);
                    backdrop-filter: blur(12px);
                    -webkit-backdrop-filter: blur(12px);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    border-radius: 16px;
                    padding: 16px 20px;
                    color: #f1f5f9;
                    box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3), 0 0 1px 1px rgba(255, 255, 255, 0.05);
                    display: flex;
                    align-items: flex-start;
                    gap: 14px;
                    pointer-events: auto;
                    transform: translateX(120%);
                    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                }
                .custom-toast.show {
                    transform: translateX(0);
                }
                .custom-toast.hide {
                    transform: translateX(120%);
                    opacity: 0;
                    margin-top: -60px;
                }
                .custom-toast-icon {
                    flex-shrink: 0;
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 13px;
                }
                .custom-toast-success {
                    border-left: 4px solid #10b981;
                }
                .custom-toast-success .custom-toast-icon {
                    background: rgba(16, 185, 129, 0.15);
                    color: #10b981;
                    border: 1px solid rgba(16, 185, 129, 0.2);
                }
                .custom-toast-warning {
                    border-left: 4px solid #f59e0b;
                }
                .custom-toast-warning .custom-toast-icon {
                    background: rgba(245, 158, 11, 0.15);
                    color: #f59e0b;
                    border: 1px solid rgba(245, 158, 11, 0.2);
                }
                .custom-toast-error {
                    border-left: 4px solid #ef4444;
                }
                .custom-toast-error .custom-toast-icon {
                    background: rgba(239, 68, 68, 0.15);
                    color: #ef4444;
                    border: 1px solid rgba(239, 68, 68, 0.2);
                }
                .custom-toast-info {
                    border-left: 4px solid #3b82f6;
                }
                .custom-toast-info .custom-toast-icon {
                    background: rgba(59, 130, 246, 0.15);
                    color: #3b82f6;
                    border: 1px solid rgba(59, 130, 246, 0.2);
                }
                .custom-toast-content {
                    flex-grow: 1;
                }
                .custom-toast-title {
                    font-size: 13px;
                    font-weight: 700;
                    margin-bottom: 3px;
                    letter-spacing: 0.3px;
                }
                .custom-toast-message {
                    font-size: 12px;
                    color: #94a3b8;
                    line-height: 1.5;
                    white-space: pre-wrap;
                }
                .custom-toast-close {
                    color: #64748b;
                    cursor: pointer;
                    font-size: 14px;
                    transition: color 0.2s;
                    margin-top: 1px;
                }
                .custom-toast-close:hover {
                    color: #94a3b8;
                }
            `;
            document.head.appendChild(toastStyle);

            window.alert = function(message) {
                let type = 'success';
                let title = 'Thông Báo';
                
                const lowerMsg = message.toLowerCase();
                if (lowerMsg.includes('lỗi') || 
                    lowerMsg.includes('không thể') || 
                    lowerMsg.includes('thất bại') || 
                    lowerMsg.includes('chưa') || 
                    lowerMsg.includes('không được') || 
                    lowerMsg.includes('chỉ được') || 
                    lowerMsg.includes('nhỏ hơn') ||
                    lowerMsg.includes('vui lòng')) {
                    type = 'warning';
                    title = 'Cảnh Báo';
                } else if (lowerMsg.includes('thành công') || 
                           lowerMsg.includes('tuyệt vời') || 
                           lowerMsg.includes('đã') || 
                           lowerMsg.includes('sao chép')) {
                    type = 'success';
                    title = 'Thành Công';
                } else {
                    type = 'info';
                    title = 'Thông Tin';
                }
                
                let container = document.querySelector('.custom-toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'custom-toast-container';
                    document.body.appendChild(container);
                }
                
                const toast = document.createElement('div');
                toast.className = `custom-toast custom-toast-${type}`;
                
                let iconHtml = '';
                if (type === 'success') iconHtml = '<i class="fa-solid fa-check"></i>';
                else if (type === 'warning') iconHtml = '<i class="fa-solid fa-triangle-exclamation"></i>';
                else if (type === 'error') iconHtml = '<i class="fa-solid fa-circle-xmark"></i>';
                else iconHtml = '<i class="fa-solid fa-info"></i>';
                
                toast.innerHTML = `
                    <div class="custom-toast-icon">${iconHtml}</div>
                    <div class="custom-toast-content">
                        <div class="custom-toast-title">${title}</div>
                        <div class="custom-toast-message">${message}</div>
                    </div>
                    <div class="custom-toast-close" onclick="this.parentElement.classList.add('hide'); setTimeout(() => this.parentElement.remove(), 400);"><i class="fa-solid fa-xmark"></i></div>
                `;
                
                container.appendChild(toast);
                
                setTimeout(() => toast.classList.add('show'), 10);
                
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.classList.remove('show');
                        toast.classList.add('hide');
                        setTimeout(() => toast.remove(), 400);
                    }
                }, 4500);
            };

            window.showAdminToast = function(message, type = 'success', title = null) {
                if (!title) {
                    if (type === 'success') title = 'Đăng nhập thành công!';
                    else if (type === 'error') title = 'Thông Báo Lỗi';
                    else if (type === 'warning') title = 'Cảnh Báo';
                    else title = 'Thông Báo';
                }
                let container = document.querySelector('.custom-toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'custom-toast-container';
                    document.body.appendChild(container);
                }
                
                const toast = document.createElement('div');
                toast.className = `custom-toast custom-toast-${type}`;
                
                let iconHtml = '';
                if (type === 'success') iconHtml = '<i class="fa-solid fa-check"></i>';
                else if (type === 'warning') iconHtml = '<i class="fa-solid fa-triangle-exclamation"></i>';
                else if (type === 'error') iconHtml = '<i class="fa-solid fa-circle-xmark"></i>';
                else iconHtml = '<i class="fa-solid fa-info"></i>';
                
                toast.innerHTML = `
                    <div class="custom-toast-icon">${iconHtml}</div>
                    <div class="custom-toast-content">
                        <div class="custom-toast-title">${title}</div>
                        <div class="custom-toast-message">${message}</div>
                    </div>
                    <div class="custom-toast-close" onclick="this.parentElement.classList.add('hide'); setTimeout(() => this.parentElement.remove(), 400);"><i class="fa-solid fa-xmark"></i></div>
                `;
                
                container.appendChild(toast);
                
                setTimeout(() => toast.classList.add('show'), 10);
                
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.classList.remove('show');
                        toast.classList.add('hide');
                        setTimeout(() => toast.remove(), 400);
                    }
                }, 4500);
            };

            document.addEventListener('DOMContentLoaded', function() {
                @if(session('success'))
                    window.showAdminToast(@json(session('success')), 'success', 'Đăng nhập thành công!');
                @endif
                @if(session('error'))
                    window.showAdminToast(@json(session('error')), 'error', 'Thông Báo Lỗi');
                @endif
            });
        })();

        // Tab switching
        function switchTab(tabId, btn = null) {
            // Hide all sections
            document.querySelectorAll('.tab-content').forEach(section => {
                section.classList.add('hidden');
            });
            // Show active section
            const targetSection = document.getElementById(tabId);
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }
            
            // Remove active style from all sidebar nav links
            document.querySelectorAll('.sidebar-nav-link').forEach(button => {
                button.classList.remove('text-indigo-400', 'bg-indigo-500/10', 'border-indigo-500/10');
                button.classList.add('text-slate-400', 'hover:text-slate-100', 'hover:bg-slate-800/50', 'border-transparent', 'hover:border-slate-800');
            });
            
            // Find active button
            let activeBtn = btn;
            if (!activeBtn) {
                activeBtn = document.querySelector(`.sidebar-nav-link[data-section="${tabId}"]`);
            }
            
            // Add active style to current button
            if (activeBtn) {
                activeBtn.classList.remove('text-slate-400', 'hover:text-slate-100', 'hover:bg-slate-800/50', 'border-transparent', 'hover:border-slate-800');
                activeBtn.classList.add('text-indigo-400', 'bg-indigo-500/10', 'border-indigo-500/10');
            }
            
            // Set header title
            let title = "SmartRoom Dashboard";
            if(tabId === 'dashboard-section') title = "Tổng Quan Hệ Thống";
            else if(tabId === 'profile-section') title = "Hồ Sơ & Xác Minh";
            else if(tabId === 'room-map-section') title = "Sơ Đồ Phòng Trực Quan";
            else if(tabId === 'utility-section') title = "Chốt Chỉ Số Điện Nước";
            else if(tabId === 'resident-section') title = "Quản Lý Cư Dân";
            else if(tabId === 'contract-section') title = "Quản Lý Hợp Đồng Online";
            else if(tabId === 'contact-section') title = "Yêu Cầu Tư Vấn & Xem Phòng";
            else if(tabId === 'ticket-section') title = "Quản Lý Sự Cố & Báo Hỏng";
            
            const titleEl = document.getElementById('section-title');
            if (titleEl) {
                titleEl.textContent = title;
            }
            
            // Update URL without page reload
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') !== tabId) {
                urlParams.set('tab', tabId);
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + urlParams.toString();
                window.history.pushState({ tab: tabId }, '', newUrl);
            }
        }

        // Add event listener to sidebar-nav-link links on DOMContentLoaded to switch tabs dynamically without page reload
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.sidebar-nav-link[data-section]').forEach(link => {
                link.addEventListener('click', function(e) {
                    const sectionId = this.getAttribute('data-section');
                    if (sectionId) {
                        e.preventDefault();
                        switchTab(sectionId, this);
                    }
                });
            });

            // Initial switch tab based on URL query parameter
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab') || 'dashboard-section';
            switchTab(tabParam);

            // Sync with browser back/forward buttons
            window.addEventListener('popstate', function(event) {
                const params = new URLSearchParams(window.location.search);
                const currentTab = params.get('tab') || 'dashboard-section';
                switchTab(currentTab);
            });

            // Khởi tạo phân trang cho bảng sự cố báo hỏng
            filterAdminTickets(true);
        });

        // ==========================================
        // TICKET MANAGEMENT (SỰ CỐ & BÁO HỎNG)
        // ==========================================
        let currentTicketPage = 1;
        let ticketPageSize = 10;

        function filterAdminTickets(resetPage = true) {
            if (resetPage) {
                currentTicketPage = 1;
            }

            const statusFilter = document.getElementById('ticket-filter-status')?.value || 'all';
            const categoryFilter = document.getElementById('ticket-filter-category')?.value || 'all';
            const searchInput = (document.getElementById('ticket-search-input')?.value || '').toLowerCase().trim();

            const rows = Array.from(document.querySelectorAll('.ticket-row'));

            // Lọc các hàng thỏa mãn điều kiện
            const matchedRows = rows.filter(row => {
                const status = row.getAttribute('data-ticket-status');
                const category = row.getAttribute('data-ticket-category');
                const searchData = (row.getAttribute('data-ticket-search') || '').toLowerCase();

                const matchStatus = (statusFilter === 'all' || status === statusFilter);
                const matchCategory = (categoryFilter === 'all' || category === categoryFilter);
                const matchSearch = (!searchInput || searchData.includes(searchInput));

                return matchStatus && matchCategory && matchSearch;
            });

            const totalMatched = matchedRows.length;
            const totalPages = Math.max(1, Math.ceil(totalMatched / ticketPageSize));
            if (currentTicketPage > totalPages) {
                currentTicketPage = totalPages;
            }
            if (currentTicketPage < 1) {
                currentTicketPage = 1;
            }

            // Ẩn tất cả các hàng không khớp bộ lọc
            rows.forEach(row => {
                if (!matchedRows.includes(row)) {
                    row.classList.add('hidden');
                }
            });

            // Phân trang các hàng khớp
            const startIndex = (currentTicketPage - 1) * ticketPageSize;
            const endIndex = startIndex + ticketPageSize;

            matchedRows.forEach((row, index) => {
                if (index >= startIndex && index < endIndex) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });

            // Cập nhật badge số lượng
            const countBadge = document.getElementById('ticket-count-badge');
            if (countBadge) {
                countBadge.textContent = totalMatched + ' sự cố';
            }

            // Thông báo không có kết quả
            const noMatchRow = document.getElementById('ticket-no-search-match');
            if (noMatchRow) {
                if (rows.length > 0 && totalMatched === 0) {
                    noMatchRow.classList.remove('hidden');
                } else {
                    noMatchRow.classList.add('hidden');
                }
            }

            updateTicketPaginationUI(totalMatched, totalPages, startIndex, endIndex);
        }

        function updateTicketPaginationUI(total, totalPages, startIndex, endIndex) {
            const container = document.getElementById('ticket-pagination-container');
            if (!container) return;

            if (total === 0) {
                container.classList.add('hidden');
                return;
            }
            container.classList.remove('hidden');

            const startEl = document.getElementById('ticket-page-start');
            const endEl = document.getElementById('ticket-page-end');
            const totalEl = document.getElementById('ticket-page-total');
            if (startEl) startEl.textContent = total > 0 ? (startIndex + 1) : 0;
            if (endEl) endEl.textContent = Math.min(endIndex, total);
            if (totalEl) totalEl.textContent = total;

            const prevBtn = document.getElementById('ticket-prev-page');
            const nextBtn = document.getElementById('ticket-next-page');
            if (prevBtn) prevBtn.disabled = (currentTicketPage <= 1);
            if (nextBtn) nextBtn.disabled = (currentTicketPage >= totalPages);

            const numbersContainer = document.getElementById('ticket-pagination-numbers');
            if (numbersContainer) {
                numbersContainer.innerHTML = '';

                const maxButtons = 5;
                let startPage = Math.max(1, currentTicketPage - 2);
                let endPage = Math.min(totalPages, startPage + maxButtons - 1);
                if (endPage - startPage < maxButtons - 1) {
                    startPage = Math.max(1, endPage - maxButtons + 1);
                }

                if (startPage > 1) {
                    numbersContainer.appendChild(createTicketPageBtn(1));
                    if (startPage > 2) {
                        const ellipsis = document.createElement('span');
                        ellipsis.className = 'px-1.5 text-xs text-slate-600';
                        ellipsis.textContent = '...';
                        numbersContainer.appendChild(ellipsis);
                    }
                }

                for (let p = startPage; p <= endPage; p++) {
                    numbersContainer.appendChild(createTicketPageBtn(p, p === currentTicketPage));
                }

                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        const ellipsis = document.createElement('span');
                        ellipsis.className = 'px-1.5 text-xs text-slate-600';
                        ellipsis.textContent = '...';
                        numbersContainer.appendChild(ellipsis);
                    }
                    numbersContainer.appendChild(createTicketPageBtn(totalPages));
                }
            }
        }

        function createTicketPageBtn(page, isActive = false) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = page;
            btn.onclick = () => changeTicketPage(page);
            if (isActive) {
                btn.className = 'w-7 h-7 rounded-lg bg-indigo-600 text-white font-bold text-xs shadow-sm flex items-center justify-center';
            } else {
                btn.className = 'w-7 h-7 rounded-lg border border-slate-800 bg-slate-900/60 hover:bg-slate-800 text-slate-400 hover:text-slate-200 text-xs font-medium flex items-center justify-center transition';
            }
            return btn;
        }

        function changeTicketPage(page) {
            if (page < 1) return;
            currentTicketPage = page;
            filterAdminTickets(false);

            const table = document.getElementById('admin-ticket-table-body');
            if (table) {
                table.closest('.glass-card')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        function changeTicketPageSize(newSize) {
            ticketPageSize = parseInt(newSize, 10) || 10;
            currentTicketPage = 1;
            filterAdminTickets(true);
        }

        function openTicketUpdateModal(data) {
            const modal = document.getElementById('ticket-update-modal');
            if (!modal) return;

            document.getElementById('modal-ticket-id').textContent = '#' + data.id;
            document.getElementById('modal-ticket-room-location').textContent = `P.${data.room || 'N/A'} • ${data.location || 'Toàn phòng'}`;
            document.getElementById('modal-ticket-resident').textContent = data.resident || 'Cư dân';
            document.getElementById('modal-ticket-title').textContent = data.title || '';

            const statusSelect = document.getElementById('modal-ticket-status');
            if (statusSelect) {
                statusSelect.value = data.status || 'pending';
                updateModalAssignedValidation(statusSelect.value);
            }

            const assignedInput = document.getElementById('modal-ticket-assigned-to');
            if (assignedInput) {
                assignedInput.value = data.assigned_to || '';
                clearTicketAssignedError();
            }

            const form = document.getElementById('ticket-update-form');
            if (form) form.action = `/smartroom/admin/ticket/${data.id}/update`;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function handleTicketModalStatusChange(newStatus) {
            updateModalAssignedValidation(newStatus);
            if (newStatus === 'processing' || newStatus === 'resolved') {
                const assignedInput = document.getElementById('modal-ticket-assigned-to');
                if (assignedInput && !assignedInput.value.trim()) {
                    assignedInput.focus();
                }
            }
        }

        function updateModalAssignedValidation(status) {
            const star = document.getElementById('modal-assigned-required-star');
            const hint = document.getElementById('modal-assigned-hint');
            const input = document.getElementById('modal-ticket-assigned-to');
            const isRequired = (status === 'processing' || status === 'resolved');

            if (star) {
                if (isRequired) {
                    star.classList.remove('hidden');
                } else {
                    star.classList.add('hidden');
                }
            }

            if (hint) {
                if (isRequired) {
                    hint.textContent = 'Bắt buộc nhập tên thợ';
                    hint.className = 'text-[11px] text-amber-400 font-semibold';
                } else {
                    hint.textContent = 'Tùy chọn khi chờ tiếp nhận';
                    hint.className = 'text-[11px] text-slate-500 italic';
                }
            }

            if (input) {
                input.required = isRequired;
            }

            clearTicketAssignedError();
        }

        function clearTicketAssignedError() {
            const input = document.getElementById('modal-ticket-assigned-to');
            const errorBox = document.getElementById('modal-assigned-error');
            if (input) {
                input.classList.remove('border-rose-500', 'focus:border-rose-500');
            }
            if (errorBox) {
                errorBox.classList.add('hidden');
            }
        }

        function validateTicketUpdateForm() {
            const status = document.getElementById('modal-ticket-status')?.value || 'pending';
            const input = document.getElementById('modal-ticket-assigned-to');
            const errorBox = document.getElementById('modal-assigned-error');
            const errorText = document.getElementById('modal-assigned-error-text');
            const val = (input?.value || '').trim();

            if (status === 'processing' || status === 'resolved') {
                if (!val) {
                    if (input) {
                        input.classList.add('border-rose-500', 'focus:border-rose-500');
                        input.focus();
                    }
                    if (errorBox && errorText) {
                        errorText.textContent = 'Vui lòng nhập tên kỹ thuật viên / thợ phụ trách khi sự cố đang xử lý hoặc đã hoàn thành.';
                        errorBox.classList.remove('hidden');
                    }
                    return false;
                }
            }

            if (val && val.length < 2) {
                if (input) {
                    input.classList.add('border-rose-500', 'focus:border-rose-500');
                    input.focus();
                }
                if (errorBox && errorText) {
                    errorText.textContent = 'Tên kỹ thuật viên / thợ phụ trách phải có ít nhất 2 ký tự.';
                    errorBox.classList.remove('hidden');
                }
                return false;
            }

            clearTicketAssignedError();
            return true;
        }

        function closeTicketUpdateModal() {
            const modal = document.getElementById('ticket-update-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
            clearTicketAssignedError();
        }

        function viewTicketImage(src) {
            const modal = document.getElementById('ticket-image-modal');
            const img = document.getElementById('ticket-image-modal-src');
            if (modal && img) {
                img.src = src;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeTicketImageModal() {
            const modal = document.getElementById('ticket-image-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        // Room filter state & functions
        let currentRoomFilter = 'all';
        let currentBuildingFilter = 'all';
        let currentFloorFilter = 'all';
        let currentRoomSearchQuery = '';

        function filterRoomsByKeyword(keyword) {
            currentRoomSearchQuery = (keyword || '').trim().toLowerCase();
            document.querySelectorAll('.room-card').forEach(card => {
                applyCurrentFilterToCard(card);
            });
            refreshFloorGroupsVisibility();
        }

        function applyCurrentFilterToCard(card) {
            if (!card) return;
            const status = card.getAttribute('data-room-status') || '';
            const buildingId = card.getAttribute('data-building-id') || '';
            const cardFloor = card.closest('.floor-group')?.getAttribute('data-floor') || '';
            const statusMatch = (currentRoomFilter === 'all' || currentRoomFilter === status);
            const buildingMatch = (currentBuildingFilter === 'all' || String(buildingId) === String(currentBuildingFilter));
            const floorMatch = (currentFloorFilter === 'all' || String(cardFloor) === String(currentFloorFilter));

            let searchMatch = true;
            if (currentRoomSearchQuery !== '') {
                const roomNum = (card.getAttribute('data-room-number') || '').toLowerCase();
                const resName = (card.getAttribute('data-resident-name') || '').toLowerCase();
                const resPhone = (card.getAttribute('data-resident-phone') || '').toLowerCase();
                searchMatch = roomNum.includes(currentRoomSearchQuery) || resName.includes(currentRoomSearchQuery) || resPhone.includes(currentRoomSearchQuery);
            }

            if (statusMatch && buildingMatch && floorMatch && searchMatch) {
                card.classList.remove('hidden');
                card.style.display = '';
            } else {
                card.classList.add('hidden');
                card.style.display = 'none';
            }
        }

        function refreshFloorGroupsVisibility() {
            let totalVisibleRooms = 0;
            const floorGroups = document.querySelectorAll('.floor-group');
            
            floorGroups.forEach(group => {
                const floorVal = group.getAttribute('data-floor');
                const floorMatches = (currentFloorFilter === 'all' || String(floorVal) === String(currentFloorFilter));
                
                if (!floorMatches) {
                    group.classList.add('hidden');
                    group.style.display = 'none';
                } else {
                    const visibleCards = Array.from(group.querySelectorAll('.room-card')).filter(c => !c.classList.contains('hidden') && c.style.display !== 'none');
                    if (visibleCards.length === 0) {
                        group.classList.add('hidden');
                        group.style.display = 'none';
                    } else {
                        group.classList.remove('hidden');
                        group.style.display = '';
                        totalVisibleRooms += visibleCards.length;
                    }
                }
            });

            // ERR_13_03: Bộ lọc tầng không có phòng nào
            const emptyState = document.getElementById('floor-empty-state');
            if (emptyState) {
                if (totalVisibleRooms === 0) {
                    emptyState.classList.remove('hidden');
                    emptyState.style.display = 'block';
                } else {
                    emptyState.classList.add('hidden');
                    emptyState.style.display = 'none';
                }
            }
        }

        function filterByFloor(floor, btn) {
            currentFloorFilter = floor;
            document.querySelectorAll('.floor-filter-btn').forEach(b => {
                b.classList.remove('bg-indigo-600', 'text-white');
                b.classList.add('bg-slate-900', 'text-slate-400', 'hover:text-slate-200', 'hover:bg-slate-800');
                if (b.getAttribute('data-floor-filter') === String(floor)) {
                    b.classList.remove('bg-slate-900', 'text-slate-400', 'hover:text-slate-200', 'hover:bg-slate-800');
                    b.classList.add('bg-indigo-600', 'text-white');
                }
            });

            document.querySelectorAll('.room-card').forEach(card => {
                applyCurrentFilterToCard(card);
            });

            refreshFloorGroupsVisibility();
        }

        function showConflictModal(message) {
            const modal = document.getElementById('modal-conflict-warning');
            const msgEl = document.getElementById('modal-conflict-message');
            if (modal && msgEl) {
                msgEl.textContent = message || 'Phòng đang có hợp đồng thuê hoạt động, không thể chuyển sang bảo trì.';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            } else {
                alert(message);
            }
        }

        function closeConflictModal() {
            const modal = document.getElementById('modal-conflict-warning');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        function updateRoomFilterCounts() {
            const cards = document.querySelectorAll('.room-card');
            const counts = {
                all: cards.length,
                empty: 0,
                occupied: 0,
                overdue: 0,
                cleaning: 0,
                maintenance: 0
            };

            cards.forEach(card => {
                const st = card.getAttribute('data-room-status');
                if (counts.hasOwnProperty(st)) {
                    counts[st]++;
                }
            });

            for (const [key, val] of Object.entries(counts)) {
                const el = document.getElementById('filter-count-' + key);
                if (el) el.textContent = val;
            }
        }

        function filterRooms(status, btnElement = null) {
            currentRoomFilter = status;

            // Set active button style
            document.querySelectorAll('.room-filter-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'text-white');
                btn.classList.add('bg-slate-900', 'text-slate-400', 'hover:text-slate-200', 'hover:bg-slate-800');
                if (btn.getAttribute('data-filter') === status) {
                    btn.classList.remove('bg-slate-900', 'text-slate-400', 'hover:text-slate-200', 'hover:bg-slate-800');
                    btn.classList.add('bg-indigo-600', 'text-white');
                }
            });

            // Show/hide cards
            document.querySelectorAll('.room-card').forEach(card => {
                applyCurrentFilterToCard(card);
            });

            refreshFloorGroupsVisibility();
        }

        function toggleAddContractModal(show) {
            const modal = document.getElementById('add-contract-modal');
            if (show) {
                resetContractDraftFields();
                modal.classList.remove('hidden');
                setTimeout(initContractFormValidationListeners, 50);
            } else {
                modal.classList.add('hidden');
                closeContractRoomResults();
            }
        }

        function resetContractDraftFields() {
            const valuesToClear = [
                'contract-room-search',
                'contract-room-id',
                'lessee-name',
                'lessee-id-number',
                'lessee-phone',
                'lessee-email',
                'lessee-permanent-address',
                'rental-address',
                'rental-area-description',
                'contract-rent-price',
                'contract-deposit',
                'first-payment-date',
                'payment-method',
            ];

            valuesToClear.forEach(id => {
                const input = document.getElementById(id);
                if (input) input.value = '';
            });

            const currentAddress = document.querySelector('[name="lessee_current_address"]');
            if (currentAddress) currentAddress.value = '';

            const equipmentList = document.querySelector('[name="equipment_list"]');
            if (equipmentList) equipmentList.value = '';

            const residentSelect = document.getElementById('contract-resident-id');
            if (residentSelect) residentSelect.value = '';

            // Ẩn huy hiệu nhận dạng tài khoản khách và cảnh báo hợp đồng
            document.getElementById('customer-lookup-badge')?.classList.add('hidden');
            document.getElementById('customer-active-contract-warning')?.classList.add('hidden');
            document.getElementById('lookup-customer-spinner')?.classList.add('hidden');

            // Xóa toàn bộ trạng thái lỗi và ẩn các thông báo lỗi
            clearContractFormErrors();
            closeContractRoomResults();
        }

        function clearContractFormErrors() {
            const inputsToReset = [
                'contract-room-search-wrapper',
                'lessor-name',
                'lessee-name',
                'lessee-id-number',
                'lessee-phone',
                'lessee-permanent-address',
                'lessee-current-address',
                'contract-start-date',
                'contract-end-date',
                'contract-deposit',
                'rental-address',
                'contract-rent-price',
                'first-payment-date',
                'payment-method'
            ];
            inputsToReset.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.remove('input-error', '!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
                }
            });

            const errIds = [
                'err-contract-room',
                'err-lessor-name',
                'err-lessee-name',
                'err-lessee-id-number',
                'err-lessee-phone',
                'err-lessee-permanent-address',
                'err-lessee-current-address',
                'err-start-date',
                'err-end-date',
                'err-deposit',
                'err-rental-address',
                'err-rent-price',
                'err-first-payment-date',
                'err-payment-method'
            ];
            errIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.add('hidden');
                    el.classList.remove('flex');
                }
            });
        }

        function fillContractResident(select) {
            const option = select?.selectedOptions?.[0];
            if (!option) return;

            const fields = {
                'lessee-name': option.dataset.name || '',
                'lessee-id-number': option.dataset.cccd || '',
                'lessee-phone': option.dataset.phone || '',
                'lessee-email': option.dataset.email || '',
                'lessee-permanent-address': option.dataset.hometown || '',
                'lessee-current-address': option.dataset.address || '',
            };

            Object.entries(fields).forEach(([id, value]) => {
                const input = document.getElementById(id);
                if (input) {
                    input.value = value;
                }
            });

            // Xóa lỗi cư dân nếu đã chọn
            const fieldsToClear = [
                ['lessee-name', 'err-lessee-name'],
                ['lessee-id-number', 'err-lessee-id-number'],
                ['lessee-phone', 'err-lessee-phone'],
                ['lessee-permanent-address', 'err-lessee-permanent-address'],
                ['lessee-current-address', 'err-lessee-current-address'],
            ];
            fieldsToClear.forEach(([inputId, errId]) => {
                const el = document.getElementById(inputId);
                const err = document.getElementById(errId);
                el?.classList.remove('input-error', '!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
                if (err) {
                    err.classList.add('hidden');
                    err.classList.remove('flex');
                }
            });
        }

        // Tự động nhận diện tài khoản khách thuê theo số điện thoại và điền sẵn dữ liệu
        let customerLookupTimeout = null;

        function lookupCustomerByPhone(phone) {
            clearTimeout(customerLookupTimeout);
            const badge = document.getElementById('customer-lookup-badge');
            const contractWarning = document.getElementById('customer-active-contract-warning');
            const contractWarningDesc = document.getElementById('customer-active-contract-desc');
            const spinner = document.getElementById('lookup-customer-spinner');
            const cleanPhone = (phone || '').trim();

            if (!/^0[0-9]{9}$/.test(cleanPhone)) {
                if (badge) badge.classList.add('hidden');
                if (contractWarning) contractWarning.classList.add('hidden');
                if (spinner) spinner.classList.add('hidden');
                return;
            }

            if (spinner) spinner.classList.remove('hidden');

            customerLookupTimeout = setTimeout(() => {
                fetch(`{{ route('smartroom.admin.lookup_customer') }}?phone=${encodeURIComponent(cleanPhone)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (spinner) spinner.classList.add('hidden');
                    if (data && data.found && data.customer) {
                        const cust = data.customer;
                        // Hiển thị huy hiệu nhận dạng tài khoản thành công
                        if (badge) {
                            const nameEl = document.getElementById('lookup-customer-name');
                            const emailEl = document.getElementById('lookup-customer-email');
                            if (nameEl) nameEl.textContent = cust.name || 'Khách thuê';
                            if (emailEl) emailEl.textContent = cust.email || 'Chưa cập nhật email';
                            badge.classList.remove('hidden');
                        }

                        // Cảnh báo nếu khách đang có hợp đồng hiệu lực
                        if (data.has_active_contract && data.active_contract) {
                            if (contractWarning && contractWarningDesc) {
                                const c = data.active_contract;
                                contractWarningDesc.innerHTML = `Khách hàng hiện đang có hợp đồng <strong>${c.status}</strong> tại <strong>Phòng ${c.room_number}</strong> (${c.building_name}) • Mã HĐ: <span class="font-mono font-semibold">${c.contract_code}</span> • Hạn hợp đồng: <strong>${c.end_date}</strong>.`;
                                contractWarning.classList.remove('hidden');
                            }
                        } else {
                            if (contractWarning) contractWarning.classList.add('hidden');
                        }

                        // Tự động điền Họ tên nếu chưa nhập hoặc cập nhật
                        const nameInput = document.getElementById('lessee-name');
                        if (nameInput && (!nameInput.value || nameInput.value.trim() === '')) {
                            nameInput.value = cust.name || '';
                            clearContractFieldError(nameInput, document.getElementById('err-lessee-name'));
                        }

                        // Tự động điền Email nếu có
                        const emailInput = document.getElementById('lessee-email');
                        if (emailInput && (!emailInput.value || emailInput.value.trim() === '')) {
                            emailInput.value = cust.email || '';
                        }

                        // Tự động điền CCCD nếu có
                        const cccdInput = document.getElementById('lessee-id-number');
                        if (cccdInput && cust.cccd && (!cccdInput.value || cccdInput.value.trim() === '')) {
                            cccdInput.value = cust.cccd;
                            clearContractFieldError(cccdInput, document.getElementById('err-lessee-id-number'));
                        }

                        // Tự động điền Hộ khẩu thường trú
                        const permanentInput = document.getElementById('lessee-permanent-address');
                        if (permanentInput && cust.permanent_address && (!permanentInput.value || permanentInput.value.trim() === '')) {
                            permanentInput.value = cust.permanent_address;
                            clearContractFieldError(permanentInput, document.getElementById('err-lessee-permanent-address'));
                        }

                        // Tự động điền Chỗ ở hiện tại
                        const currentInput = document.getElementById('lessee-current-address');
                        if (currentInput && cust.current_address && (!currentInput.value || currentInput.value.trim() === '')) {
                            currentInput.value = cust.current_address;
                            clearContractFieldError(currentInput, document.getElementById('err-lessee-current-address'));
                        }

                        if (cust.resident_id) {
                            const resHidden = document.getElementById('contract-resident-id');
                            if (resHidden) resHidden.value = cust.resident_id;
                        }
                    } else {
                        if (badge) badge.classList.add('hidden');
                        if (contractWarning) contractWarning.classList.add('hidden');
                    }
                })
                .catch(err => {
                    if (spinner) spinner.classList.add('hidden');
                    if (badge) badge.classList.add('hidden');
                    if (contractWarning) contractWarning.classList.add('hidden');
                });
            }, 300);
        }

        function filterContractRooms(keyword = '') {
            const results = document.getElementById('contract-room-results');
            const empty = document.getElementById('contract-room-empty');
            if (!results) return;

            const normalize = (value) => (value || '')
                .toString()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^\w\s]/g, ' ')
                .replace(/\s+/g, ' ')
                .trim()
                .toLowerCase();
            const normalizedKeyword = normalize(keyword);
            let visibleCount = 0;

            document.querySelectorAll('.contract-room-option').forEach(option => {
                const haystack = normalize(`${option.dataset.search || ''} ${option.dataset.label || ''}`);
                const isVisible = !normalizedKeyword || haystack.includes(normalizedKeyword);
                option.classList.toggle('hidden', !isVisible);
                if (isVisible) visibleCount++;
            });

            empty?.classList.toggle('hidden', visibleCount > 0);
            results.classList.remove('hidden');
        }

        function closeContractRoomResults() {
            document.getElementById('contract-room-results')?.classList.add('hidden');
        }

        function selectContractRoom(option) {
            if (!option) return;

            const hiddenInput = document.getElementById('contract-room-id');
            const searchInput = document.getElementById('contract-room-search');

            if (hiddenInput) hiddenInput.value = option.dataset.id || '';
            if (searchInput) searchInput.value = option.dataset.label || '';
            fillContractRoom(option);
            closeContractRoomResults();

            // Xóa trạng thái lỗi phòng
            const roomWrapper = document.getElementById('contract-room-search-wrapper');
            const errRoom = document.getElementById('err-contract-room');
            roomWrapper?.classList.remove('input-error', '!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
            if (errRoom) {
                errRoom.classList.add('hidden');
                errRoom.classList.remove('flex');
            }
        }

        function fillContractRoom(option) {
            if (!option) return;

            const roomNumber = option.dataset.room || '';
            const area = option.dataset.area || '';
            const address = option.dataset.address || '';
            const price = option.dataset.price || '';

            const rentalAddress = document.getElementById('rental-address');
            const areaDescription = document.getElementById('rental-area-description');
            const rentPrice = document.getElementById('contract-rent-price');
            const deposit = document.getElementById('contract-deposit');

            if (rentalAddress) {
                rentalAddress.value = [address, roomNumber ? `Phòng ${roomNumber}` : ''].filter(Boolean).join(' - ');
                rentalAddress.classList.remove('input-error', '!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
                const errAddr = document.getElementById('err-rental-address');
                if (errAddr) { errAddr.classList.add('hidden'); errAddr.classList.remove('flex'); }
            }
            if (areaDescription) {
                areaDescription.value = roomNumber
                    ? `Phòng ${roomNumber}${area ? `, diện tích ${area} m²` : ''}.`
                    : '';
            }
            if (rentPrice) {
                rentPrice.value = price;
                rentPrice.classList.remove('input-error', '!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
                const errPrice = document.getElementById('err-rent-price');
                if (errPrice) { errPrice.classList.add('hidden'); errPrice.classList.remove('flex'); }
            }
            if (deposit && price) {
                deposit.value = price;
                deposit.classList.remove('input-error', '!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
                const errDep = document.getElementById('err-deposit');
                if (errDep) { errDep.classList.add('hidden'); errDep.classList.remove('flex'); }
            }
        }

        function calculateContractEndDate() {
            const startInput = document.getElementById('contract-start-date');
            const monthsInput = document.getElementById('contract-duration-months');
            const endInput = document.getElementById('contract-end-date');
            if (!startInput || !monthsInput || !endInput) return;

            const startVal = startInput.value;
            const months = parseInt(monthsInput.value) || 0;
            if (startVal && months > 0) {
                const parts = startVal.split('-');
                if (parts.length === 3) {
                    const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                    d.setMonth(d.getMonth() + months);
                    const yyyy = d.getFullYear();
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const dd = String(d.getDate()).padStart(2, '0');
                    endInput.value = `${yyyy}-${mm}-${dd}`;
                }
            }

            // Nếu ngày kết thúc hợp lệ sau ngày bắt đầu, xóa viền đỏ lỗi
            if (startVal && endInput.value && endInput.value > startVal) {
                endInput.classList.remove('input-error', '!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
                const errEnd = document.getElementById('err-end-date');
                if (errEnd) {
                    errEnd.classList.add('hidden');
                    errEnd.classList.remove('flex');
                }
            }
        }

        // Hàm đặt viền đỏ + thông báo lỗi
        function setContractFieldError(inputEl, errEl, msg) {
            if (inputEl) {
                inputEl.classList.add('input-error', '!border-rose-500');
            }
            if (errEl) {
                const span = errEl.querySelector('span');
                if (span && msg) span.textContent = msg;
                errEl.classList.remove('hidden');
                errEl.classList.add('flex');
            }
        }

        // Hàm xóa viền đỏ + ẩn thông báo lỗi
        function clearContractFieldError(inputEl, errEl) {
            if (inputEl) {
                inputEl.classList.remove('input-error', '!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
            }
            if (errEl) {
                errEl.classList.add('hidden');
                errEl.classList.remove('flex');
            }
        }

        // Validate từng trường độc lập (dùng cho blur, input, change và submit)
        function validateSingleContractField(fieldId) {
            let isValid = true;
            if (fieldId === 'lessor-name') {
                const el = document.getElementById('lessor-name');
                const err = document.getElementById('err-lessor-name');
                if (!el || !el.value.trim()) {
                    setContractFieldError(el, err, 'Họ và tên bên cho thuê không được để trống.');
                    isValid = false;
                } else {
                    clearContractFieldError(el, err);
                }
            } else if (fieldId === 'lessee-name') {
                const el = document.getElementById('lessee-name');
                const err = document.getElementById('err-lessee-name');
                if (!el || !el.value.trim()) {
                    setContractFieldError(el, err, 'Vui lòng chọn cư dân đại diện đứng tên trên hợp đồng.');
                    isValid = false;
                } else {
                    clearContractFieldError(el, err);
                }
            } else if (fieldId === 'lessee-id-number') {
                const el = document.getElementById('lessee-id-number');
                const err = document.getElementById('err-lessee-id-number');
                const val = el ? el.value.trim() : '';
                if (!val || !/^[0-9]{9,12}$/.test(val)) {
                    setContractFieldError(el, err, 'Số CMND/CCCD bên thuê phải gồm 9 đến 12 chữ số.');
                    isValid = false;
                } else {
                    clearContractFieldError(el, err);
                }
            } else if (fieldId === 'lessee-phone') {
                const el = document.getElementById('lessee-phone');
                const err = document.getElementById('err-lessee-phone');
                const val = el ? el.value.trim() : '';
                if (!val || !/^0[0-9]{9}$/.test(val)) {
                    setContractFieldError(el, err, 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng số 0 (VD: 0988123456).');
                    isValid = false;
                } else {
                    clearContractFieldError(el, err);
                }
            } else if (fieldId === 'lessee-permanent-address') {
                const el = document.getElementById('lessee-permanent-address');
                const err = document.getElementById('err-lessee-permanent-address');
                if (!el || !el.value.trim()) {
                    setContractFieldError(el, err, 'Vui lòng nhập hộ khẩu thường trú của bên thuê.');
                    isValid = false;
                } else {
                    clearContractFieldError(el, err);
                }
            } else if (fieldId === 'lessee-current-address') {
                const el = document.getElementById('lessee-current-address');
                const err = document.getElementById('err-lessee-current-address');
                if (!el || !el.value.trim()) {
                    setContractFieldError(el, err, 'Vui lòng nhập chỗ ở hiện tại của bên thuê.');
                    isValid = false;
                } else {
                    clearContractFieldError(el, err);
                }
            } else if (fieldId === 'contract-start-date') {
                const el = document.getElementById('contract-start-date');
                const err = document.getElementById('err-start-date');
                if (!el || !el.value) {
                    setContractFieldError(el, err, 'Ngày bắt đầu hợp đồng là bắt buộc.');
                    isValid = false;
                } else {
                    clearContractFieldError(el, err);
                }
            } else if (fieldId === 'contract-end-date') {
                const el = document.getElementById('contract-end-date');
                const startEl = document.getElementById('contract-start-date');
                const err = document.getElementById('err-end-date');
                if (!el || !el.value) {
                    setContractFieldError(el, err, 'Thời hạn kết thúc hợp đồng là bắt buộc.');
                    isValid = false;
                } else if (startEl && startEl.value && el.value <= startEl.value) {
                    setContractFieldError(el, err, 'Thời hạn kết thúc hợp đồng phải sau ngày bắt đầu hiệu lực.');
                    isValid = false;
                } else {
                    clearContractFieldError(el, err);
                }
            } else if (fieldId === 'contract-deposit') {
                const el = document.getElementById('contract-deposit');
                const err = document.getElementById('err-deposit');
                if (!el || el.value === '' || el.value === null) {
                    setContractFieldError(el, err, 'Số tiền đặt cọc hợp đồng là bắt buộc.');
                    isValid = false;
                } else if (parseInt(el.value) < 0) {
                    setContractFieldError(el, err, 'Số tiền đặt cọc hợp đồng phải lớn hơn hoặc bằng 0 đồng.');
                    isValid = false;
                } else {
                    clearContractFieldError(el, err);
                }
            } else if (fieldId === 'rental-address') {
                const el = document.getElementById('rental-address');
                const err = document.getElementById('err-rental-address');
                if (!el || !el.value.trim()) {
                    setContractFieldError(el, err, 'Địa chỉ nhà/phòng cho thuê là bắt buộc.');
                    isValid = false;
                } else {
                    clearContractFieldError(el, err);
                }
            } else if (fieldId === 'contract-rent-price') {
                const el = document.getElementById('contract-rent-price');
                const err = document.getElementById('err-rent-price');
                if (!el || el.value === '' || parseInt(el.value) <= 0) {
                    setContractFieldError(el, err, 'Giá thuê là bắt buộc và phải lớn hơn 0 đồng.');
                    isValid = false;
                } else {
                    clearContractFieldError(el, err);
                }
            } else if (fieldId === 'first-payment-date') {
                const el = document.getElementById('first-payment-date');
                const err = document.getElementById('err-first-payment-date');
                if (!el || !el.value.trim()) {
                    setContractFieldError(el, err, 'Thời điểm thanh toán lần đầu là bắt buộc.');
                    isValid = false;
                } else {
                    clearContractFieldError(el, err);
                }
            } else if (fieldId === 'payment-method') {
                const el = document.getElementById('payment-method');
                const err = document.getElementById('err-payment-method');
                if (!el || !el.value.trim()) {
                    setContractFieldError(el, err, 'Hình thức thanh toán là bắt buộc.');
                    isValid = false;
                } else {
                    clearContractFieldError(el, err);
                }
            }
            return isValid;
        }

        // Validate toàn bộ form khi submit
        function validateCreateContractForm(e) {
            let isValid = true;
            let firstInvalidEl = null;

            // 1. Kiểm tra Chọn Phòng: ERR_08_01
            const roomId = document.getElementById('contract-room-id')?.value;
            const roomWrapper = document.getElementById('contract-room-search-wrapper');
            const roomSearch = document.getElementById('contract-room-search');
            const errRoom = document.getElementById('err-contract-room');

            if (!roomId || !roomId.trim()) {
                setContractFieldError(roomWrapper, errRoom, 'Vui lòng chọn phòng lưu trú để thiết lập hợp đồng.');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = roomSearch || roomWrapper;
            } else {
                clearContractFieldError(roomWrapper, errRoom);
            }

            // 2. Kiểm tra tất cả các trường còn lại
            const contractFieldsToValidate = [
                'lessor-name',
                'lessee-name',
                'lessee-id-number',
                'lessee-phone',
                'lessee-permanent-address',
                'lessee-current-address',
                'contract-start-date',
                'contract-end-date',
                'contract-deposit',
                'rental-address',
                'contract-rent-price',
                'first-payment-date',
                'payment-method'
            ];

            contractFieldsToValidate.forEach(fieldId => {
                const isFieldValid = validateSingleContractField(fieldId);
                if (!isFieldValid) {
                    isValid = false;
                    if (!firstInvalidEl) {
                        firstInvalidEl = document.getElementById(fieldId);
                    }
                }
            });

            if (!isValid) {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                if (firstInvalidEl) {
                    firstInvalidEl.focus();
                    firstInvalidEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }

            return true;
        }

        // Gắn listener blur, input và change cho toàn bộ các trường để phản hồi tức thì
        function initContractFormValidationListeners() {
            const fields = [
                'lessor-name',
                'lessee-name',
                'lessee-id-number',
                'lessee-phone',
                'lessee-permanent-address',
                'lessee-current-address',
                'contract-start-date',
                'contract-end-date',
                'contract-deposit',
                'rental-address',
                'contract-rent-price',
                'first-payment-date',
                'payment-method'
            ];

            fields.forEach(fieldId => {
                const el = document.getElementById(fieldId);
                if (!el || el.dataset.validateAttached) return;
                el.dataset.validateAttached = 'true';

                // Khi rời ô (blur) -> bôi đỏ ngay nếu sai/thiếu
                el.addEventListener('blur', function() {
                    validateSingleContractField(fieldId);
                });

                // Khi đang gõ (input) -> kiểm tra và tự gỡ viền đỏ khi đã thỏa mãn
                el.addEventListener('input', function() {
                    validateSingleContractField(fieldId);
                });

                // Khi thay đổi giá trị (change)
                el.addEventListener('change', function() {
                    validateSingleContractField(fieldId);
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initContractFormValidationListeners();

            @if($errors->has('room_id') || $errors->has('resident_id') || $errors->has('lessee_name') || $errors->has('start_date') || $errors->has('end_date') || $errors->has('deposit') || $errors->has('first_payment_date') || $errors->has('payment_method'))
                toggleAddContractModal(true);
                @if($errors->has('room_id'))
                    document.getElementById('contract-room-search-wrapper')?.classList.add('!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
                    const errR = document.getElementById('err-contract-room');
                    if (errR) {
                        errR.querySelector('span').textContent = "{{ $errors->first('room_id') }}";
                        errR.classList.remove('hidden');
                        errR.classList.add('flex');
                    }
                @endif
                @if($errors->has('resident_id') || $errors->has('lessee_name'))
                    document.getElementById('lessee-name')?.classList.add('!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
                    const errL = document.getElementById('err-lessee-name');
                    if (errL) {
                        errL.querySelector('span').textContent = "{{ $errors->first('resident_id') ?: $errors->first('lessee_name') }}";
                        errL.classList.remove('hidden');
                        errL.classList.add('flex');
                    }
                @endif
                @if($errors->has('start_date'))
                    document.getElementById('contract-start-date')?.classList.add('!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
                    const errS = document.getElementById('err-start-date');
                    if (errS) {
                        errS.querySelector('span').textContent = "{{ $errors->first('start_date') }}";
                        errS.classList.remove('hidden');
                        errS.classList.add('flex');
                    }
                @endif
                @if($errors->has('end_date'))
                    document.getElementById('contract-end-date')?.classList.add('!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
                    const errE = document.getElementById('err-end-date');
                    if (errE) {
                        errE.querySelector('span').textContent = "{{ $errors->first('end_date') }}";
                        errE.classList.remove('hidden');
                        errE.classList.add('flex');
                    }
                @endif
                @if($errors->has('deposit'))
                    document.getElementById('contract-deposit')?.classList.add('!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
                    const errD = document.getElementById('err-deposit');
                    if (errD) {
                        errD.querySelector('span').textContent = "{{ $errors->first('deposit') }}";
                        errD.classList.remove('hidden');
                        errD.classList.add('flex');
                    }
                @endif
                @if($errors->has('first_payment_date'))
                    document.getElementById('first-payment-date')?.classList.add('!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
                    const errFP = document.getElementById('err-first-payment-date');
                    if (errFP) {
                        errFP.querySelector('span').textContent = "{{ $errors->first('first_payment_date') }}";
                        errFP.classList.remove('hidden');
                        errFP.classList.add('flex');
                    }
                @endif
                @if($errors->has('payment_method'))
                    document.getElementById('payment-method')?.classList.add('!border-rose-500', 'ring-2', 'ring-rose-500', 'bg-rose-500/10');
                    const errPM = document.getElementById('err-payment-method');
                    if (errPM) {
                        errPM.querySelector('span').textContent = "{{ $errors->first('payment_method') }}";
                        errPM.classList.remove('hidden');
                        errPM.classList.add('flex');
                    }
                @endif
            @endif
        });

        document.addEventListener('click', (event) => {
            const picker = document.getElementById('contract-room-results');
            const search = document.getElementById('contract-room-search');

            if (picker && search && !picker.contains(event.target) && event.target !== search) {
                closeContractRoomResults();
            }
        });

        function copySignLink(url, btn) {
            const fullUrl = url.startsWith('http') ? url : (window.location.origin + url);
            navigator.clipboard.writeText(fullUrl).then(() => {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Đã sao chép!';
                btn.classList.remove('text-indigo-400', 'bg-indigo-600/20');
                btn.classList.add('text-emerald-400', 'bg-emerald-600/20');
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('text-emerald-400', 'bg-emerald-600/20');
                    btn.classList.add('text-indigo-400', 'bg-indigo-600/20');
                }, 2000);
            }).catch(err => {
                console.error('Lỗi khi sao chép: ', err);
            });
        }

        let currentBillId = null;

        function printModalInvoice() {
            if (currentBillId) {
                window.open(`/smartroom/admin/utility/${currentBillId}/print`, '_blank');
            } else {
                alert('Không tìm thấy hóa đơn hợp lệ để in!');
            }
        }

        // Biến lưu thông tin phòng đang mở trong modal
        let currentActiveRoomId = null;
        let currentActiveRoomStatus = null;
        let currentActiveRoomNumber = null;

        function filterByBuilding(buildingId, btn) {
            currentBuildingFilter = buildingId;
            document.querySelectorAll('.building-tab-btn').forEach(b => {
                b.className = 'building-tab-btn px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 font-semibold transition-all';
            });
            btn.className = 'building-tab-btn px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-bold transition-all';

            const nameDisplay = document.getElementById('current-building-name-display');
            const addrDisplay = document.getElementById('current-building-address-display');
            const gmapsBtn = document.getElementById('current-building-gmaps-btn');

            if (buildingId === 'all') {
                if (nameDisplay) nameDisplay.textContent = 'Tất cả cơ sở quản lý';
                if (addrDisplay && addrDisplay.querySelector('span')) addrDisplay.querySelector('span').textContent = 'Hiển thị danh sách phòng thuộc tất cả các tòa nhà';
                const defaultAddr = btn.getAttribute('data-building-address') || 'Hà Nội';
                if (gmapsBtn) gmapsBtn.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(defaultAddr)}`;
            } else {
                const bName = btn.getAttribute('data-building-name') || '';
                const bAddr = btn.getAttribute('data-building-address') || '';
                if (nameDisplay) nameDisplay.textContent = bName;
                if (addrDisplay && addrDisplay.querySelector('span')) addrDisplay.querySelector('span').textContent = bAddr;
                if (gmapsBtn) gmapsBtn.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(bAddr || bName)}`;
            }

            document.querySelectorAll('.room-card').forEach(card => {
                applyCurrentFilterToCard(card);
            });

            refreshFloorGroupsVisibility();
        }

        function openRoomDetailById(roomId) {
            const card = document.getElementById('room-card-' + roomId);
            if (!card) return;

            const roomNum = card.getAttribute('data-room-number') || '';
            const status = card.getAttribute('data-room-status') || 'empty';
            const name = card.getAttribute('data-resident-name') || '';
            const phone = card.getAttribute('data-resident-phone') || '';
            const rent = card.getAttribute('data-price') || '0đ';
            const elec = card.getAttribute('data-elec-used') || '0 kWh';
            const water = card.getAttribute('data-water-used') || '0 m3';
            const total = card.getAttribute('data-total-bill') || '0đ';
            const latestBillId = card.getAttribute('data-latest-bill-id') || null;

            openRoomDetail(roomId, roomNum, status, name, phone, rent, elec, water, total, latestBillId);
        }

        // Room Detail modal triggers
        function openRoomDetail(roomId, roomNum, status, name, phone, rent, elec, water, total, latestBillId) {
            // Hỗ trợ trường hợp gọi cũ (9 tham số mà không có roomId ở đầu)
            if (arguments.length === 9 && typeof roomId === 'string' && isNaN(roomId)) {
                latestBillId = total;
                total = water;
                water = elec;
                elec = rent;
                rent = phone;
                phone = name;
                name = status;
                status = roomNum;
                roomNum = roomId;
                roomId = null;
            }

            currentActiveRoomId = roomId;
            currentActiveRoomStatus = status;
            currentActiveRoomNumber = roomNum;

            const modal = document.getElementById('room-detail-modal');
            const title = document.getElementById('modal-room-title');
            const badge = document.getElementById('modal-room-status-badge');
            
            title.textContent = "Phòng " + roomNum;

            // Cập nhật vị trí cơ sở cụ thể cho phòng trong modal
            if (roomId) {
                const card = document.getElementById('room-card-' + roomId);
                if (card) {
                    const bName = card.getAttribute('data-building-name') || '';
                    const bAddr = card.getAttribute('data-building-address') || '';
                    const modalBName = document.getElementById('modal-room-building-name');
                    const modalBAddr = document.getElementById('modal-room-building-address');
                    const modalBMaps = document.getElementById('modal-room-maps-link');
                    if (modalBName) modalBName.textContent = bName || 'Cơ sở chính';
                    if (modalBAddr) modalBAddr.textContent = bAddr || 'Chưa cập nhật địa chỉ cụ thể';
                    if (modalBMaps) {
                        const q = encodeURIComponent(bAddr || bName || 'Hà Nội');
                        modalBMaps.href = `https://www.google.com/maps/search/?api=1&query=${q}`;
                    }
                }
            }
            
            // Set nhãn trạng thái và badge
            const statusLabels = {
                'empty': 'Trống',
                'occupied': 'Đã thuê',
                'overdue': 'Nợ phí',
                'cleaning': 'Cần dọn dẹp',
                'maintenance': 'Bảo trì'
            };
            const badgeClasses = {
                'empty': 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                'occupied': 'bg-red-500/10 text-red-400 border-red-500/20',
                'overdue': 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                'cleaning': 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                'maintenance': 'bg-slate-500/10 text-slate-400 border-slate-500/20'
            };

            badge.textContent = statusLabels[status] || 'Không xác định';
            badge.className = "text-xs px-2.5 py-1 rounded-md font-bold uppercase border " + (badgeClasses[status] || 'bg-slate-500/10 text-slate-400 border-slate-500/20');

            // Cập nhật trạng thái active cho các nút Housekeeping
            updateQuickStatusButtons(status);

            // If empty, cleaning or maintenance, hide resident and billing details
            const resDetails = document.getElementById('modal-resident-details');
            const billDetails = document.getElementById('modal-billing-details');
            const actionBtn = document.getElementById('modal-btn-action');
            const payBtn = document.getElementById('modal-btn-pay');
            const printBtn = document.getElementById('modal-btn-print');
            const qrBtn = document.getElementById('modal-btn-qr');
            const modalBox = document.getElementById('room-detail-modal-box');
            const colBillingActions = document.getElementById('modal-col-billing-actions');
            const col1CloseBtn = document.getElementById('modal-col1-close-btn');
            const contentGrid = document.getElementById('modal-content-grid');
            const priceDisplay = document.getElementById('modal-room-price-display');

            if (priceDisplay) {
                priceDisplay.textContent = rent ? `${rent}/tháng` : '';
            }
            
            if(status === 'empty' || status === 'cleaning' || status === 'maintenance') {
                resDetails.classList.add('hidden');
                billDetails.classList.add('hidden');
                actionBtn.classList.add('hidden');
                payBtn.classList.add('hidden');
                printBtn.classList.add('hidden');
                if (qrBtn) qrBtn.classList.add('hidden');
                const zaloPersonalBtn = document.getElementById('modal-btn-zalo-personal');
                if (zaloPersonalBtn) zaloPersonalBtn.classList.add('hidden');
                currentBillId = null;

                // Che do phong trong: co gon 1 cot vua van
                if (modalBox) {
                    modalBox.classList.remove('max-w-2xl', 'lg:max-w-3xl');
                    modalBox.classList.add('max-w-md');
                }
                if (contentGrid) {
                    contentGrid.classList.remove('md:grid-cols-2');
                    contentGrid.classList.add('grid-cols-1');
                }
                if (colBillingActions) colBillingActions.classList.add('hidden');
                if (col1CloseBtn) col1CloseBtn.classList.remove('hidden');
            } else {
                resDetails.classList.remove('hidden');
                billDetails.classList.remove('hidden');
                actionBtn.classList.remove('hidden');
                if (qrBtn) qrBtn.classList.remove('hidden');

                // Che do da thue/no phi: 2 cot can doi, vua van tam mat
                if (modalBox) {
                    modalBox.classList.remove('max-w-md');
                    modalBox.classList.add('max-w-2xl', 'lg:max-w-3xl');
                }
                if (contentGrid) {
                    contentGrid.classList.remove('grid-cols-1');
                    contentGrid.classList.add('grid-cols-1', 'md:grid-cols-2');
                }
                if (colBillingActions) colBillingActions.classList.remove('hidden');
                if (col1CloseBtn) col1CloseBtn.classList.add('hidden');
                
                document.getElementById('modal-resident-name').textContent = name;
                document.getElementById('modal-resident-phone').textContent = phone;
                document.getElementById('modal-bill-rent').textContent = rent;
                document.getElementById('modal-bill-electric').textContent = elec;
                document.getElementById('modal-bill-water').textContent = water;
                document.getElementById('modal-bill-total').textContent = total || rent;
                
                const billStatusBadge = document.getElementById('modal-bill-status');
                const rawAmount = (total || rent).replace(/\D/g, '');
                
                if (qrBtn) {
                    qrBtn.onclick = function() {
                        if (latestBillId && latestBillId !== 'null') {
                            showVietQR(latestBillId);
                        } else {
                            showVietQRFallback(roomNum, rawAmount);
                        }
                    };
                }

                if (latestBillId && latestBillId !== 'null') {
                    currentBillId = latestBillId;
                    printBtn.classList.remove('hidden');
                } else {
                    currentBillId = null;
                    printBtn.classList.add('hidden');
                }

                if(status === 'overdue') {
                    billStatusBadge.textContent = 'Chưa thanh toán';
                    billStatusBadge.className = 'text-[10px] text-amber-400 font-bold px-2 py-0.5 bg-amber-500/10 border border-amber-500/20 rounded';
                    actionBtn.innerHTML = '<i class="fa-solid fa-bell"></i> Soạn tin nhắc nợ / Cổng ZNS & SMS';
                    actionBtn.className = "w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 shadow-md shadow-amber-600/20 transition-all cursor-pointer";
                    
                    const zaloPersonalBtn = document.getElementById('modal-btn-zalo-personal');
                    if (zaloPersonalBtn) zaloPersonalBtn.classList.remove('hidden');

                    if (latestBillId && latestBillId !== 'null') {
                        payBtn.classList.remove('hidden');
                        document.getElementById('modal-pay-form').action = `/smartroom/admin/utility/${latestBillId}/pay`;
                        document.getElementById('modal-notify-form').action = `/smartroom/admin/utility/${latestBillId}/notify`;
                        actionBtn.onclick = function() {
                            openSendMsgModal(phone, name, `Kính gửi anh/chị ${name}, ban quản lý thông báo hóa đơn dịch vụ phòng ${roomNum} chưa được thanh toán với tổng số tiền là ${total}. Vui lòng thanh toán sớm nhất có thể. Trân trọng!`, 'debt');
                        };
                    } else {
                        payBtn.classList.add('hidden');
                        actionBtn.onclick = null;
                    }
                } else {
                    billStatusBadge.textContent = 'Đã thanh toán';
                    billStatusBadge.className = 'text-[10px] text-emerald-400 font-bold px-2 py-0.5 bg-emerald-500/10 border border-emerald-500/20 rounded';
                    actionBtn.innerHTML = '<i class="fa-solid fa-check"></i> Đã đóng tiền tháng này';
                    actionBtn.className = "w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-semibold text-slate-500 bg-slate-900 border border-slate-800 cursor-not-allowed";
                    payBtn.classList.add('hidden');
                    actionBtn.onclick = null;
                    const zaloPersonalBtn = document.getElementById('modal-btn-zalo-personal');
                    if (zaloPersonalBtn) zaloPersonalBtn.classList.add('hidden');
                }
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRoomDetail() {
            const modal = document.getElementById('room-detail-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }


        function updateQuickStatusButtons(status) {
            const btnEmpty = document.getElementById('btn-quick-empty');
            const btnCleaning = document.getElementById('btn-quick-cleaning');
            const btnMaintenance = document.getElementById('btn-quick-maintenance');
            
            if (!btnEmpty || !btnCleaning || !btnMaintenance) return;

            // Reset classes
            [btnEmpty, btnCleaning, btnMaintenance].forEach(btn => {
                btn.classList.remove('ring-2', 'ring-offset-1', 'ring-offset-slate-950', 'border-emerald-500', 'border-orange-500', 'border-slate-400', 'bg-emerald-500/20', 'bg-orange-500/20', 'bg-slate-500/20');
            });

            if (status === 'empty') {
                btnEmpty.classList.add('border-emerald-500', 'bg-emerald-500/20', 'ring-2', 'ring-emerald-400');
            } else if (status === 'cleaning') {
                btnCleaning.classList.add('border-orange-500', 'bg-orange-500/20', 'ring-2', 'ring-orange-400');
            } else if (status === 'maintenance') {
                btnMaintenance.classList.add('border-slate-400', 'bg-slate-500/20', 'ring-2', 'ring-slate-400');
            }
        }

        // Cập nhật nhanh trạng thái phòng (Housekeeping) qua AJAX
        async function setQuickRoomStatus(newStatus) {
            if (!currentActiveRoomId) {
                alert('Không xác định được phòng cần cập nhật!');
                return;
            }

            const loadingSpinner = document.getElementById('quick-status-loading');
            if (loadingSpinner) loadingSpinner.classList.remove('hidden');

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                    || '{{ csrf_token() }}';

                let response;
                try {
                    response = await fetch(`/smartroom/admin/rooms/${currentActiveRoomId}/quick-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ status: newStatus })
                    });
                } catch (networkErr) {
                    console.error('Lỗi kết nối mạng: ', networkErr);
                    alert('Không thể kết nối đến máy chủ. Vui lòng kiểm tra lại tiến trình server hoặc nhấn F5!');
                    return;
                }

                if (response.status === 419) {
                    alert('Phiên làm việc đã hết hạn. Vui lòng nhấn F5 tải lại trang để tiếp tục!');
                    return;
                }

                let data;
                try {
                    data = await response.json();
                } catch (parseErr) {
                    alert('Máy chủ phản hồi không đúng định dạng hoặc đang khởi động lại. Vui lòng nhấn F5 tải lại trang!');
                    return;
                }

                if (!response.ok || !data.success) {
                    if (response.status === 422 || data.error_code === 'ERR_13_02') {
                        showConflictModal(data.message || 'Phòng đang có hợp đồng thuê hoạt động, không thể chuyển sang bảo trì.');
                    } else {
                        alert(data.message || 'Có lỗi xảy ra khi cập nhật trạng thái phòng.');
                    }
                    return;
                }

                // Cập nhật giao diện an toàn sau khi máy chủ đã xác nhận lưu thành công
                try {
                    if (typeof applyRoomCardUpdate === 'function') {
                        applyRoomCardUpdate(data.room);
                    }

                    // Cập nhật lại trạng thái hiển thị trong Drawer
                    currentActiveRoomStatus = newStatus;
                    const badge = document.getElementById('modal-room-status-badge');
                    if (badge) {
                        badge.textContent = data.room.status_label;
                        badge.className = "text-xs px-2.5 py-1 rounded-md font-bold uppercase border " + (data.room.badge_class || '');
                    }
                    if (typeof updateQuickStatusButtons === 'function') {
                        updateQuickStatusButtons(newStatus);
                    }

                    if (typeof showRealtimeToast === 'function') {
                        showRealtimeToast(`Đã cập nhật P.${data.room.room_number}: ${data.room.status_label}`, 'Dữ liệu đã được lưu thành công!', 'room');
                    }
                } catch (uiErr) {
                    console.warn('Lỗi cập nhật UI cục bộ sau khi lưu thành công: ', uiErr);
                }
            } catch (err) {
                console.error('Lỗi setQuickRoomStatus: ', err);
            } finally {
                if (loadingSpinner) loadingSpinner.classList.add('hidden');
            }
        }


        function syncInputs(btn) {
            const row = btn.closest('tr');
            const newElecInput = row.querySelector('.new-elec-input');
            const newWaterInput = row.querySelector('.new-water-input');
            const newElecVal = newElecInput.value;
            const newWaterVal = newWaterInput.value;
            
            const oldElec = parseInt(row.querySelector('[data-field="old-elec"]').textContent);
            const oldWater = parseInt(row.querySelector('[data-field="old-water"]').textContent);
            
            if (!newElecVal || !newWaterVal) {
                alert('Vui lòng nhập đầy đủ số điện và nước mới!');
                event.preventDefault();
                return;
            }
            
            if (parseInt(newElecVal) < oldElec || parseInt(newWaterVal) < oldWater) {
                alert('Số mới không được nhỏ hơn số cũ!');
                event.preventDefault();
                return;
            }
            
            row.querySelector('.form-new-elec').value = newElecVal;
            row.querySelector('.form-new-water').value = newWaterVal;
        }

        function closeRoomDetail() {
            document.getElementById('room-detail-modal').classList.add('hidden');
        }

        function submitModalPay() {
            if (confirm('Xác nhận cư dân đã thanh toán hóa đơn này?')) {
                document.getElementById('modal-pay-form').submit();
            }
        }

        function filterUtilityRows() {
            const query = (document.getElementById('utility-search-input')?.value || '').trim().toLowerCase();
            const rows = document.querySelectorAll('#tblUtility tbody tr');
            rows.forEach(row => {
                const roomNum = (row.getAttribute('data-room') || '').toLowerCase();
                const text = (row.textContent || '').toLowerCase();
                const serials = ((row.getAttribute('data-electric-serial') || '') + ' ' + (row.getAttribute('data-water-serial') || '')).toLowerCase();
                if (!query || roomNum.includes(query) || text.includes(query) || serials.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Live calculation for electricity & water utility row with anomaly detection & validation (FEAT_15_UTILITY_BULK)
        function calculateRowCost(input) {
            const row = input.closest('tr');
            if (!row) return;
            const roomPrice = parseInt(row.getAttribute('data-price')) || 0;
            
            const oldElecEl = row.querySelector('[data-field="old-elec"]');
            const oldWaterEl = row.querySelector('[data-field="old-water"]');
            const oldElec = oldElecEl ? parseFloat(oldElecEl.textContent) || 0 : 0;
            const oldWater = oldWaterEl ? parseFloat(oldWaterEl.textContent) || 0 : 0;

            const newElecInputEl = row.querySelector('.new-elec-input');
            const newWaterInputEl = row.querySelector('.new-water-input');
            const newElecVal = newElecInputEl ? newElecInputEl.value.trim() : '';
            const newWaterVal = newWaterInputEl ? newWaterInputEl.value.trim() : '';

            // Regex kiểm tra số: ^[0-9]+(\.[0-9]{1,2})?$ (ERR_15_04)
            const numRegex = /^[0-9]+(\.[0-9]{1,2})?$/;

            if (newElecInputEl) {
                if (newElecVal !== '' && !numRegex.test(newElecVal)) {
                    // ERR_15_04: Nhập ký tự chữ vào ô chỉ số
                    newElecInputEl.classList.add('border-rose-500', 'text-rose-400', 'bg-rose-500/10', 'shake-error');
                    newElecInputEl.title = 'Chỉ số công tơ điện nước chỉ được phép nhập số.';
                } else if (newElecVal !== '' && parseFloat(newElecVal) < oldElec) {
                    // ERR_15_02: Chỉ số điện mới nhỏ hơn điện cũ
                    newElecInputEl.classList.add('border-rose-500', 'text-rose-400', 'bg-rose-500/10', 'shake-error');
                    newElecInputEl.title = 'Chỉ số điện mới phải lớn hơn hoặc bằng chỉ số điện kỳ trước.';
                } else {
                    newElecInputEl.classList.remove('border-rose-500', 'text-rose-400', 'bg-rose-500/10', 'shake-error');
                    newElecInputEl.removeAttribute('title');
                }
            }

            if (newWaterInputEl) {
                if (newWaterVal !== '' && !numRegex.test(newWaterVal)) {
                    // ERR_15_04: Nhập ký tự chữ vào ô chỉ số
                    newWaterInputEl.classList.add('border-rose-500', 'text-rose-400', 'bg-rose-500/10', 'shake-error');
                    newWaterInputEl.title = 'Chỉ số công tơ điện nước chỉ được phép nhập số.';
                } else if (newWaterVal !== '' && parseFloat(newWaterVal) < oldWater) {
                    // ERR_15_03: Chỉ số nước mới nhỏ hơn nước cũ
                    newWaterInputEl.classList.add('border-rose-500', 'text-rose-400', 'bg-rose-500/10', 'shake-error');
                    newWaterInputEl.title = 'Chỉ số nước mới phải lớn hơn hoặc bằng chỉ số nước kỳ trước.';
                } else {
                    newWaterInputEl.classList.remove('border-rose-500', 'text-rose-400', 'bg-rose-500/10', 'shake-error');
                    newWaterInputEl.removeAttribute('title');
                }
            }

            const newElec = (newElecVal !== '' && numRegex.test(newElecVal)) ? parseFloat(newElecVal) : oldElec;
            const newWater = (newWaterVal !== '' && numRegex.test(newWaterVal)) ? parseFloat(newWaterVal) : oldWater;

            let elecUsed = 0;
            let waterUsed = 0;
            
            if (newElec >= oldElec) elecUsed = newElec - oldElec;
            if (newWater >= oldWater) waterUsed = newWater - oldWater;

            const usedElecEl = row.querySelector('[data-field="used-elec"]');
            const usedWaterEl = row.querySelector('[data-field="used-water"]');
            if (usedElecEl) usedElecEl.textContent = elecUsed;
            if (usedWaterEl) usedWaterEl.textContent = waterUsed;

            // Phát hiện tiêu thụ tăng đột biến bất thường
            const abnormalElecWarn = row.querySelector('.abnormal-elec-warning');
            const abnormalWaterWarn = row.querySelector('.abnormal-water-warning');
            if (abnormalElecWarn) {
                if (elecUsed > 1000) {
                    abnormalElecWarn.classList.remove('hidden');
                    abnormalElecWarn.classList.add('inline-flex');
                } else {
                    abnormalElecWarn.classList.add('hidden');
                    abnormalElecWarn.classList.remove('inline-flex');
                }
            }
            if (abnormalWaterWarn) {
                if (waterUsed > 100) {
                    abnormalWaterWarn.classList.remove('hidden');
                    abnormalWaterWarn.classList.add('inline-flex');
                } else {
                    abnormalWaterWarn.classList.add('hidden');
                    abnormalWaterWarn.classList.remove('inline-flex');
                }
            }

            // Tính tiền
            const elecCost = elecUsed * 3500;
            const waterCost = waterUsed * 15000;
            const serviceCost = 150000;
            const total = roomPrice + elecCost + waterCost + serviceCost;

            const totalEl = row.querySelector('[data-field="cost-total"]');
            if (totalEl) totalEl.textContent = total.toLocaleString('vi-VN') + "đ";
        }

        function saveSingleUtility(roomId, btn) {
            const row = btn.closest('tr');
            if (!row) return;
            const newElecInput = row.querySelector('.new-elec-input');
            const newWaterInput = row.querySelector('.new-water-input');
            const newElecVal = newElecInput ? newElecInput.value.trim() : '';
            const newWaterVal = newWaterInput ? newWaterInput.value.trim() : '';
            
            const oldElecEl = row.querySelector('[data-field="old-elec"]');
            const oldWaterEl = row.querySelector('[data-field="old-water"]');
            const oldElec = oldElecEl ? parseFloat(oldElecEl.textContent) || 0 : 0;
            const oldWater = oldWaterEl ? parseFloat(oldWaterEl.textContent) || 0 : 0;
            
            const numRegex = /^[0-9]+(\.[0-9]{1,2})?$/;

            if (!newElecVal || !newWaterVal) {
                if (typeof window.showAdminToast === 'function') {
                    window.showAdminToast('Vui lòng nhập đầy đủ số điện và nước mới!', 'error', 'Thiếu dữ liệu');
                } else {
                    alert('Vui lòng nhập đầy đủ số điện và nước mới!');
                }
                return;
            }

            // ERR_15_04: Nhập ký tự chữ vào ô chỉ số
            if (!numRegex.test(newElecVal) || !numRegex.test(newWaterVal)) {
                if (newElecInput && !numRegex.test(newElecVal)) newElecInput.classList.add('border-rose-500', 'shake-error');
                if (newWaterInput && !numRegex.test(newWaterVal)) newWaterInput.classList.add('border-rose-500', 'shake-error');
                if (typeof window.showAdminToast === 'function') {
                    window.showAdminToast('Chỉ số công tơ điện nước chỉ được phép nhập số.', 'error', 'Mã lỗi: ERR_15_04');
                } else {
                    alert('Chỉ số công tơ điện nước chỉ được phép nhập số.');
                }
                return;
            }
            
            // ERR_15_02: Chỉ số điện mới nhỏ hơn điện cũ
            if (parseFloat(newElecVal) < oldElec) {
                if (newElecInput) newElecInput.classList.add('border-rose-500', 'shake-error');
                if (typeof window.showAdminToast === 'function') {
                    window.showAdminToast('Chỉ số điện mới phải lớn hơn hoặc bằng chỉ số điện kỳ trước.', 'error', 'Mã lỗi: ERR_15_02');
                } else {
                    alert('Chỉ số điện mới phải lớn hơn hoặc bằng chỉ số điện kỳ trước.');
                }
                return;
            }

            // ERR_15_03: Chỉ số nước mới nhỏ hơn nước cũ
            if (parseFloat(newWaterVal) < oldWater) {
                if (newWaterInput) newWaterInput.classList.add('border-rose-500', 'shake-error');
                if (typeof window.showAdminToast === 'function') {
                    window.showAdminToast('Chỉ số nước mới phải lớn hơn hoặc bằng chỉ số nước kỳ trước.', 'error', 'Mã lỗi: ERR_15_03');
                } else {
                    alert('Chỉ số nước mới phải lớn hơn hoặc bằng chỉ số nước kỳ trước.');
                }
                return;
            }
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('smartroom.admin.utility.store') }}";
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = "{{ csrf_token() }}";
            form.appendChild(csrf);
            
            const roomInp = document.createElement('input');
            roomInp.type = 'hidden';
            roomInp.name = 'room_id';
            roomInp.value = roomId;
            form.appendChild(roomInp);
            
            const elecInp = document.createElement('input');
            elecInp.type = 'hidden';
            elecInp.name = 'new_electricity';
            elecInp.value = newElecVal;
            form.appendChild(elecInp);
            
            const waterInp = document.createElement('input');
            waterInp.type = 'hidden';
            waterInp.name = 'new_water';
            waterInp.value = newWaterVal;
            form.appendChild(waterInp);

            const cboMonth = document.getElementById('cboMonth');
            if (cboMonth && cboMonth.value) {
                const monthInp = document.createElement('input');
                monthInp.type = 'hidden';
                monthInp.name = 'billing_month';
                monthInp.value = cboMonth.value;
                form.appendChild(monthInp);
            }
            
            document.body.appendChild(form);
            form.submit();
        }

        // Tự động kiểm tra và xác thực form Chốt Điện Nước Hàng Loạt (FEAT_15_UTILITY_BULK)
        document.addEventListener('DOMContentLoaded', function() {
            const bulkForm = document.getElementById('bulk-utility-form');
            if (bulkForm) {
                bulkForm.addEventListener('submit', function(e) {
                    const cboMonth = document.getElementById('cboMonth');
                    const cboMonthErr = document.getElementById('cboMonth-error');

                    // 1. ERR_15_01: Để trống tháng chốt cước
                    if (!cboMonth || !cboMonth.value.trim()) {
                        e.preventDefault();
                        if (cboMonth) {
                            cboMonth.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/50', 'shake-error');
                            cboMonth.focus();
                        }
                        if (cboMonthErr) cboMonthErr.classList.remove('hidden');
                        if (typeof window.showAdminToast === 'function') {
                            window.showAdminToast('Vui lòng chọn tháng tính cước hóa đơn điện nước.', 'error', 'Mã lỗi: ERR_15_01');
                        } else {
                            alert('Vui lòng chọn tháng tính cước hóa đơn điện nước.');
                        }
                        return false;
                    } else {
                        cboMonth.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/50', 'shake-error');
                        if (cboMonthErr) cboMonthErr.classList.add('hidden');
                    }

                    const rows = document.querySelectorAll('#tblUtility tbody tr');
                    const numRegex = /^[0-9]+(\.[0-9]{1,2})?$/;

                    for (let row of rows) {
                        const newElecInput = row.querySelector('.new-elec-input');
                        const newWaterInput = row.querySelector('.new-water-input');
                        const newElecVal = newElecInput ? newElecInput.value.trim() : '';
                        const newWaterVal = newWaterInput ? newWaterInput.value.trim() : '';

                        const oldElecEl = row.querySelector('[data-field="old-elec"]');
                        const oldWaterEl = row.querySelector('[data-field="old-water"]');
                        const oldElec = oldElecEl ? parseFloat(oldElecEl.textContent) || 0 : 0;
                        const oldWater = oldWaterEl ? parseFloat(oldWaterEl.textContent) || 0 : 0;

                        // 2. ERR_15_04: Nhập ký tự chữ vào ô chỉ số
                        if ((newElecVal !== '' && !numRegex.test(newElecVal)) || (newWaterVal !== '' && !numRegex.test(newWaterVal))) {
                            e.preventDefault();
                            if (newElecVal !== '' && !numRegex.test(newElecVal) && newElecInput) {
                                newElecInput.classList.add('border-rose-500', 'shake-error');
                                newElecInput.focus();
                            } else if (newWaterInput) {
                                newWaterInput.classList.add('border-rose-500', 'shake-error');
                                newWaterInput.focus();
                            }
                            if (typeof window.showAdminToast === 'function') {
                                window.showAdminToast('Chỉ số công tơ điện nước chỉ được phép nhập số.', 'error', 'Mã lỗi: ERR_15_04');
                            } else {
                                alert('Chỉ số công tơ điện nước chỉ được phép nhập số.');
                            }
                            return false;
                        }

                        // 3. ERR_15_02: Chỉ số điện mới nhỏ hơn điện cũ
                        if (newElecVal !== '' && parseFloat(newElecVal) < oldElec) {
                            e.preventDefault();
                            if (newElecInput) {
                                newElecInput.classList.add('border-rose-500', 'shake-error');
                                newElecInput.focus();
                            }
                            if (typeof window.showAdminToast === 'function') {
                                window.showAdminToast('Chỉ số điện mới phải lớn hơn hoặc bằng chỉ số điện kỳ trước.', 'error', 'Mã lỗi: ERR_15_02');
                            } else {
                                alert('Chỉ số điện mới phải lớn hơn hoặc bằng chỉ số điện kỳ trước.');
                            }
                            return false;
                        }

                        // 4. ERR_15_03: Chỉ số nước mới nhỏ hơn nước cũ
                        if (newWaterVal !== '' && parseFloat(newWaterVal) < oldWater) {
                            e.preventDefault();
                            if (newWaterInput) {
                                newWaterInput.classList.add('border-rose-500', 'shake-error');
                                newWaterInput.focus();
                            }
                            if (typeof window.showAdminToast === 'function') {
                                window.showAdminToast('Chỉ số nước mới phải lớn hơn hoặc bằng chỉ số nước kỳ trước.', 'error', 'Mã lỗi: ERR_15_03');
                            } else {
                                alert('Chỉ số nước mới phải lớn hơn hoặc bằng chỉ số nước kỳ trước.');
                            }
                            return false;
                        }
                    }
                });
            }

            // Chặn nhập ký tự chữ vào ô chỉ số điện nước (ERR_15_04)
            document.querySelectorAll('.new-elec-input, .new-water-input').forEach(function(input) {
                input.addEventListener('keypress', function(e) {
                    // Cho phép các phím điều khiển, số 0-9 và dấu chấm
                    if (e.key && !/[\d.]/.test(e.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Enter'].includes(e.key)) {
                        e.preventDefault();
                        input.classList.add('border-rose-500', 'shake-error');
                        setTimeout(() => input.classList.remove('shake-error'), 500);
                        if (typeof window.showAdminToast === 'function') {
                            window.showAdminToast('Chỉ số công tơ điện nước chỉ được phép nhập số.', 'warning', 'Mã lỗi: ERR_15_04');
                        }
                    }
                });
            });
        });

        // =======================================================
        // 2. AI VISION OCR CAMERA QUÉT CÔNG TƠ ĐIỆN NƯỚC (GEMINI AI)
        // =======================================================
        const meterOcrState = {
            roomId: null,
            roomNumber: null,
            type: 'electricity',
            targetInput: null,
            stream: null,
            capturedBase64: null,
            recognizedValue: null,
            activeSource: 'camera'
        };

        function openMeterOcrModal(roomId, roomNumber, type, triggerBtn) {
            meterOcrState.roomId = roomId;
            meterOcrState.roomNumber = roomNumber;
            meterOcrState.type = type;
            
            const row = triggerBtn.closest('tr');
            meterOcrState.targetInput = row.querySelector('.new-' + (type === 'electricity' ? 'elec' : 'water') + '-input');
            
            // Cập nhật text UI
            document.getElementById('ocr-target-room-text').textContent = roomNumber;
            const badge = document.getElementById('ocr-meter-type-badge');
            const unit = document.getElementById('ocr-result-unit');
            if (type === 'electricity') {
                badge.textContent = 'Điện (kWh)';
                badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30';
                unit.textContent = 'kWh';
            } else {
                badge.textContent = 'Nước (m3)';
                badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-cyan-500/20 text-cyan-300 border border-cyan-500/30';
                unit.textContent = 'm3';
            }

            resetOcrModalState();
            document.getElementById('meter-ocr-modal').classList.remove('hidden');

            // Mặc định khởi động camera
            switchOcrSource('camera');
        }

        function closeMeterOcrModal() {
            stopOcrCamera();
            document.getElementById('meter-ocr-modal').classList.add('hidden');
            resetOcrModalState();
        }

        function resetOcrModalState() {
            meterOcrState.capturedBase64 = null;
            meterOcrState.recognizedValue = null;
            
            document.getElementById('ocr-image-preview').classList.add('hidden');
            document.getElementById('ocr-image-preview').src = '';
            document.getElementById('ocr-result-box').classList.add('hidden');
            document.getElementById('ocr-laser-line').classList.add('hidden');
            document.getElementById('ocr-loading-overlay').classList.add('hidden');
            document.getElementById('ocr-fallback-hint').classList.add('hidden');
            
            document.getElementById('btn-ocr-capture').classList.remove('hidden');
            document.getElementById('btn-ocr-retake').classList.add('hidden');
            document.getElementById('btn-ocr-analyze').classList.add('hidden');
            document.getElementById('btn-ocr-apply').disabled = true;
            
            const fileInp = document.getElementById('ocr-file-input');
            if (fileInp) fileInp.value = '';
        }

        function switchOcrSource(mode) {
            meterOcrState.activeSource = mode;
            const tabCamera = document.getElementById('tab-btn-camera');
            const tabUpload = document.getElementById('tab-btn-upload');
            const videoEl = document.getElementById('ocr-camera-video');
            const uploadZone = document.getElementById('ocr-upload-zone');
            const previewImg = document.getElementById('ocr-image-preview');
            const cameraError = document.getElementById('ocr-camera-error');
            const btnCapture = document.getElementById('btn-ocr-capture');

            cameraError.classList.add('hidden');

            if (mode === 'camera') {
                tabCamera.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 bg-indigo-600 text-white shadow-sm';
                tabUpload.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 text-slate-400 hover:text-slate-200';
                
                uploadZone.classList.add('hidden');
                previewImg.classList.add('hidden');
                btnCapture.classList.remove('hidden');
                document.getElementById('btn-ocr-retake').classList.add('hidden');
                document.getElementById('btn-ocr-analyze').classList.add('hidden');

                startOcrCamera();
            } else {
                stopOcrCamera();
                tabUpload.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 bg-indigo-600 text-white shadow-sm';
                tabCamera.className = 'py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 text-slate-400 hover:text-slate-200';
                
                videoEl.classList.add('hidden');
                btnCapture.classList.add('hidden');
                
                if (meterOcrState.capturedBase64) {
                    previewImg.classList.remove('hidden');
                    uploadZone.classList.add('hidden');
                    document.getElementById('btn-ocr-retake').classList.remove('hidden');
                    document.getElementById('btn-ocr-analyze').classList.remove('hidden');
                } else {
                    uploadZone.classList.remove('hidden');
                    previewImg.classList.add('hidden');
                }
            }
        }

        async function startOcrCamera() {
            const videoEl = document.getElementById('ocr-camera-video');
            const cameraError = document.getElementById('ocr-camera-error');
            videoEl.classList.add('hidden');
            cameraError.classList.add('hidden');

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                cameraError.classList.remove('hidden');
                return;
            }

            try {
                if (meterOcrState.stream) {
                    stopOcrCamera();
                }
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } },
                    audio: false
                });
                meterOcrState.stream = stream;
                videoEl.srcObject = stream;
                videoEl.classList.remove('hidden');
            } catch (err) {
                console.warn('Camera access error:', err);
                cameraError.classList.remove('hidden');
            }
        }

        function stopOcrCamera() {
            if (meterOcrState.stream) {
                meterOcrState.stream.getTracks().forEach(track => track.stop());
                meterOcrState.stream = null;
            }
            const videoEl = document.getElementById('ocr-camera-video');
            if (videoEl) {
                videoEl.srcObject = null;
                videoEl.classList.add('hidden');
            }
        }

        function captureOcrSnapshot() {
            const videoEl = document.getElementById('ocr-camera-video');
            const canvas = document.getElementById('ocr-capture-canvas');
            const previewImg = document.getElementById('ocr-image-preview');

            if (!videoEl || videoEl.videoWidth === 0) {
                alert('Camera chưa sẵn sàng để chụp!');
                return;
            }

            canvas.width = videoEl.videoWidth;
            canvas.height = videoEl.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);

            const base64 = canvas.toDataURL('image/jpeg', 0.9);
            meterOcrState.capturedBase64 = base64;

            // Dừng camera và hiển thị preview
            stopOcrCamera();
            previewImg.src = base64;
            previewImg.classList.remove('hidden');

            document.getElementById('btn-ocr-capture').classList.add('hidden');
            document.getElementById('btn-ocr-retake').classList.remove('hidden');
            document.getElementById('btn-ocr-analyze').classList.remove('hidden');
            
            // Tự động kích hoạt phân tích ngay
            submitOcrAnalysis();
        }

        function resetOcrCapture() {
            meterOcrState.capturedBase64 = null;
            meterOcrState.recognizedValue = null;
            document.getElementById('ocr-image-preview').classList.add('hidden');
            document.getElementById('ocr-result-box').classList.add('hidden');
            document.getElementById('btn-ocr-apply').disabled = true;

            if (meterOcrState.activeSource === 'camera') {
                document.getElementById('btn-ocr-capture').classList.remove('hidden');
                document.getElementById('btn-ocr-retake').classList.add('hidden');
                document.getElementById('btn-ocr-analyze').classList.add('hidden');
                startOcrCamera();
            } else {
                document.getElementById('ocr-upload-zone').classList.remove('hidden');
                document.getElementById('btn-ocr-retake').classList.add('hidden');
                document.getElementById('btn-ocr-analyze').classList.add('hidden');
                document.getElementById('ocr-file-input').value = '';
            }
        }

        function handleOcrFileSelected(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ảnh không được vượt quá 5MB!');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    meterOcrState.capturedBase64 = e.target.result;
                    const previewImg = document.getElementById('ocr-image-preview');
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                    document.getElementById('ocr-upload-zone').classList.add('hidden');
                    document.getElementById('btn-ocr-retake').classList.remove('hidden');
                    document.getElementById('btn-ocr-analyze').classList.remove('hidden');
                    
                    // Tự động phân tích
                    submitOcrAnalysis();
                };
                reader.readAsDataURL(file);
            }
        }

        async function submitOcrAnalysis() {
            if (!meterOcrState.capturedBase64) {
                alert('Vui lòng chụp ảnh hoặc tải ảnh công tơ trước!');
                return;
            }

            const loadingOverlay = document.getElementById('ocr-loading-overlay');
            const laserLine = document.getElementById('ocr-laser-line');
            const resultBox = document.getElementById('ocr-result-box');
            const btnAnalyze = document.getElementById('btn-ocr-analyze');
            const btnApply = document.getElementById('btn-ocr-apply');
            const btnConfirm = document.getElementById('btnConfirmOCR');
            const fallbackHint = document.getElementById('ocr-fallback-hint');
            const errBanner = document.getElementById('ocr-error-banner');
            const errBannerText = document.getElementById('ocr-error-banner-text');
            const uploadZone = document.getElementById('ocr-upload-zone');

            if (errBanner) errBanner.classList.add('hidden');
            if (uploadZone) uploadZone.classList.remove('border-rose-500', 'shake-error');

            // ERR_16_01: Chưa chọn tệp ảnh công tơ
            if (!meterOcrState.capturedBase64) {
                if (uploadZone) uploadZone.classList.add('border-rose-500', 'shake-error');
                if (errBanner && errBannerText) {
                    errBannerText.textContent = 'Vui lòng chọn hoặc kéo thả ít nhất một ảnh chụp mặt công tơ.';
                    errBanner.classList.remove('hidden');
                }
                if (typeof window.showAdminToast === 'function') {
                    window.showAdminToast('Vui lòng chọn hoặc kéo thả ít nhất một ảnh chụp mặt công tơ.', 'error', 'Mã lỗi: ERR_16_01');
                } else {
                    alert('Vui lòng chọn hoặc kéo thả ít nhất một ảnh chụp mặt công tơ.');
                }
                return;
            }

            loadingOverlay.classList.remove('hidden');
            loadingOverlay.classList.add('flex');
            laserLine.classList.remove('hidden');
            resultBox.classList.add('hidden');
            fallbackHint.classList.add('hidden');
            btnAnalyze.disabled = true;

            try {
                const response = await fetch("{{ route('smartroom.admin.ai.ocr_meter') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        image: meterOcrState.capturedBase64,
                        meter_image: meterOcrState.capturedBase64,
                        type: meterOcrState.type
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    const errCode = data.error_code || 'ERR_16_02';
                    const errMsg = data.message || 'Không thể nhận diện chỉ số từ ảnh. Vui lòng chụp rõ nét hơn hoặc nhập tay!';
                    if (errBanner && errBannerText) {
                        errBannerText.textContent = errMsg;
                        errBanner.classList.remove('hidden');
                    }
                    if (errCode === 'ERR_16_01' && uploadZone) {
                        uploadZone.classList.add('border-rose-500', 'shake-error');
                    }
                    if (typeof window.showAdminToast === 'function') {
                        window.showAdminToast(errMsg, 'error', `Mã lỗi: ${errCode}`);
                    } else {
                        alert(errMsg);
                    }
                    return;
                }

                if (data.success && data.result) {
                    const result = data.result;
                    meterOcrState.recognizedValue = result.value;

                    const ocrResInp = document.getElementById('ocr_result');
                    if (ocrResInp) ocrResInp.value = (result.value !== null ? result.value : 0);
                    const ocrResVal = document.getElementById('ocr-result-value');
                    if (ocrResVal) ocrResVal.textContent = (result.value !== null ? result.value.toLocaleString() : '0');
                    
                    const confPercent = Math.round((result.confidence || 0.95) * 100);
                    const confBadge = document.getElementById('ocr-confidence-badge');
                    if (confBadge) confBadge.textContent = 'Độ tin cậy: ' + confPercent + '%';

                    if (confPercent >= 85) {
                        if (confBadge) confBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                    } else if (confPercent >= 60) {
                        if (confBadge) confBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/20';
                        if (fallbackHint) fallbackHint.classList.remove('hidden');
                    } else {
                        if (confBadge) confBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500/10 text-rose-400 border border-rose-500/20';
                        if (fallbackHint) fallbackHint.classList.remove('hidden');
                    }

                    if (!result.used_ai) {
                        if (fallbackHint) fallbackHint.classList.remove('hidden');
                    }

                    resultBox.classList.remove('hidden');
                    if (btnConfirm) btnConfirm.disabled = false;
                    if (btnApply) btnApply.disabled = false;
                } else {
                    alert('Không thể nhận diện chỉ số từ ảnh. Vui lòng chụp rõ nét hơn hoặc nhập tay!');
                }
            } catch (err) {
                console.error('OCR Error:', err);
                alert('Có lỗi xảy ra trong quá trình gửi ảnh tới AI. Vui lòng thử lại hoặc tự nhập tay!');
            } finally {
                loadingOverlay.classList.add('hidden');
                loadingOverlay.classList.remove('flex');
                laserLine.classList.add('hidden');
                btnAnalyze.disabled = false;
            }
        }

        function applyOcrResultToInput() {
            if (meterOcrState.targetInput && meterOcrState.recognizedValue !== null) {
                meterOcrState.targetInput.value = meterOcrState.recognizedValue;
                calculateRowCost(meterOcrState.targetInput);
                
                // Hiệu ứng nháy xanh xác nhận ô vừa được điền (ERR_16_04)
                meterOcrState.targetInput.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-500');
                setTimeout(() => {
                    meterOcrState.targetInput.classList.remove('border-emerald-500', 'ring-2', 'ring-emerald-500');
                }, 2000);

                if (typeof window.showAdminToast === 'function') {
                    window.showAdminToast('Đã nhận diện thành công chỉ số công tơ và tự khớp vào phòng!', 'success', 'Mã: ERR_16_04');
                }

                closeMeterOcrModal();
            }
        }

        // ==========================================
        // AI BULK METER OCR (QUÉT HÀNG LOẠT NHIỀU CÔNG TƠ)
        // ==========================================
        let bulkOcrState = {
            type: 'electricity',
            files: [], // Array of base64 strings
            results: [], // { index, image, serial_number, room_id, room_number, value, confidence, is_matched, reason }
            allRooms: []
        };

        function openBulkOcrModal(type = 'electricity') {
            setBulkOcrType(type);
            clearBulkOcrFiles();
            const modal = document.getElementById('bulk-meter-ocr-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeBulkOcrModal() {
            const modal = document.getElementById('bulk-meter-ocr-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        function setBulkOcrType(type) {
            bulkOcrState.type = type;
            const badge = document.getElementById('bulk-ocr-type-badge');
            const btnElec = document.getElementById('bulk-type-btn-electricity');
            const btnWater = document.getElementById('bulk-type-btn-water');

            if (type === 'electricity') {
                if (badge) {
                    badge.textContent = 'Điện (kWh)';
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30';
                }
                if (btnElec) {
                    btnElec.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-sm';
                }
                if (btnWater) {
                    btnWater.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-slate-900 text-slate-400 border border-slate-800 hover:text-slate-200';
                }
            } else {
                if (badge) {
                    badge.textContent = 'Nước (m3)';
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-cyan-500/20 text-cyan-300 border border-cyan-500/30';
                }
                if (btnWater) {
                    btnWater.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shadow-sm';
                }
                if (btnElec) {
                    btnElec.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-slate-900 text-slate-400 border border-slate-800 hover:text-slate-200';
                }
            }
        }

        function handleBulkOcrFilesSelected(input) {
            if (!input.files || input.files.length === 0) return;
            loadBulkOcrFilesList(input.files);
        }

        function loadBulkOcrFilesList(fileList) {
            const files = Array.from(fileList).slice(0, 30);
            bulkOcrState.files = [];
            bulkOcrState.results = [];
            document.getElementById('bulk-ocr-results-wrapper')?.classList.add('hidden');
            const btnApply = document.getElementById('btn-bulk-ocr-apply');
            if (btnApply) btnApply.disabled = true;

            let loaded = 0;
            files.forEach(file => {
                if (!file.type.startsWith('image/')) {
                    loaded++;
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    bulkOcrState.files.push(e.target.result);
                    loaded++;
                    if (loaded === files.length) {
                        const countEl = document.getElementById('bulk-files-count');
                        if (countEl) countEl.textContent = bulkOcrState.files.length;
                        document.getElementById('bulk-ocr-files-bar')?.classList.remove('hidden');
                        const btnStart = document.getElementById('btn-start-bulk-ocr');
                        if (btnStart) btnStart.disabled = false;
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        function clearBulkOcrFiles() {
            bulkOcrState.files = [];
            bulkOcrState.results = [];
            const fileInput = document.getElementById('bulk-ocr-file-input');
            if (fileInput) fileInput.value = '';
            document.getElementById('bulk-ocr-files-bar')?.classList.add('hidden');
            document.getElementById('bulk-ocr-progress-box')?.classList.add('hidden');
            document.getElementById('bulk-ocr-results-wrapper')?.classList.add('hidden');
            const btnApply = document.getElementById('btn-bulk-ocr-apply');
            if (btnApply) btnApply.disabled = true;
        }

        async function startBulkOcrAnalysis() {
            if (!bulkOcrState.files || bulkOcrState.files.length === 0) {
                alert('Vui lòng chọn ít nhất 1 ảnh công tơ!');
                return;
            }

            const progressBox = document.getElementById('bulk-ocr-progress-box');
            const progressBar = document.getElementById('bulk-ocr-progress-bar');
            const progressText = document.getElementById('bulk-ocr-progress-text');
            const btnStart = document.getElementById('btn-start-bulk-ocr');

            progressBox.classList.remove('hidden');
            if (btnStart) btnStart.disabled = true;
            if (progressBar) progressBar.style.width = '30%';
            if (progressText) progressText.textContent = '30%';

            try {
                const response = await fetch("{{ route('smartroom.admin.ai.ocr_meter_bulk') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        images: bulkOcrState.files,
                        type: bulkOcrState.type
                    })
                });

                if (progressBar) progressBar.style.width = '75%';
                if (progressText) progressText.textContent = '75%';

                const data = await response.json();
                if (data.success) {
                    if (progressBar) progressBar.style.width = '100%';
                    if (progressText) progressText.textContent = '100%';
                    bulkOcrState.allRooms = data.all_rooms || [];
                    
                    bulkOcrState.results = [];
                    (data.matched || []).forEach(item => {
                        bulkOcrState.results.push({
                            index: item.index,
                            image: bulkOcrState.files[item.index],
                            serial_number: item.serial_number,
                            room_id: item.room_id,
                            room_number: item.room_number,
                            value: item.value,
                            confidence: item.confidence,
                            is_matched: true,
                            reason: ''
                        });
                    });
                    (data.unmatched || []).forEach(item => {
                        bulkOcrState.results.push({
                            index: item.index,
                            image: bulkOcrState.files[item.index],
                            serial_number: item.serial_number,
                            room_id: null,
                            room_number: null,
                            value: item.value,
                            confidence: item.confidence,
                            is_matched: false,
                            reason: item.reason || 'Chưa khớp phòng'
                        });
                    });

                    renderBulkOcrResults();
                } else {
                    alert('Lỗi phân tích: ' + (data.message || 'Không thể quét ảnh hàng loạt.'));
                }
            } catch (err) {
                console.error('Bulk OCR Error:', err);
                alert('Có lỗi xảy ra khi gửi dữ liệu tới AI. Vui lòng thử lại!');
            } finally {
                if (btnStart) btnStart.disabled = false;
                setTimeout(() => {
                    progressBox.classList.add('hidden');
                }, 600);
            }
        }

        function renderBulkOcrResults() {
            const tbody = document.getElementById('bulk-ocr-results-body');
            if (!tbody) return;
            tbody.innerHTML = '';

            let matchedCount = 0;
            bulkOcrState.results.forEach((item, idx) => {
                if (item.is_matched && item.room_id) matchedCount++;

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-900/40 transition-colors border-b border-slate-900';
                
                let roomOptionsHtml = `<option value="">-- Chọn phòng gán tay --</option>`;
                bulkOcrState.allRooms.forEach(r => {
                    const selected = item.room_id == r.id ? 'selected' : '';
                    roomOptionsHtml += `<option value="${r.id}" ${selected}>Phòng ${r.room_number}</option>`;
                });

                const serialDisplay = item.serial_number 
                    ? `<span class="font-mono font-bold text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded text-[11px]">${item.serial_number}</span>`
                    : `<span class="text-slate-500 italic text-[10px]">Không tìm thấy Số SX</span>`;

                const statusBadge = item.is_matched && item.room_id
                    ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><i class="fa-solid fa-circle-check"></i> Khớp tự động</span>`
                    : `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20" title="${item.reason}"><i class="fa-solid fa-circle-exclamation"></i> Cần gán phòng</span>`;

                tr.innerHTML = `
                    <td class="px-4 py-3">
                        <img src="${item.image}" class="w-10 h-10 object-cover rounded-lg border border-slate-800 cursor-pointer hover:scale-110 transition-transform shadow-sm" onclick="window.open('${item.image}')" title="Bấm xem ảnh gốc">
                    </td>
                    <td class="px-4 py-3 font-semibold">${serialDisplay}</td>
                    <td class="px-4 py-3">
                        <select onchange="updateBulkItemRoom(${idx}, this.value)" class="w-36 px-2 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:border-indigo-500 focus:outline-none transition-colors">
                            ${roomOptionsHtml}
                        </select>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1">
                            <input type="number" value="${item.value ?? 0}" onchange="updateBulkItemValue(${idx}, this.value)" class="w-20 px-2 py-1 rounded-lg bg-slate-900 border border-slate-800 text-xs font-mono font-bold text-emerald-400 focus:border-indigo-500 focus:outline-none">
                            <span class="text-[10px] text-slate-400">${bulkOcrState.type === 'electricity' ? 'kWh' : 'm3'}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">${statusBadge}</td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" onclick="removeBulkItem(${idx})" class="w-7 h-7 rounded-lg bg-slate-900 hover:bg-rose-500/20 text-slate-500 hover:text-rose-400 text-xs transition-all flex items-center justify-center mx-auto" title="Bỏ qua ảnh này">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            const statsBadge = document.getElementById('bulk-match-stats-badge');
            if (statsBadge) {
                statsBadge.textContent = `Khớp thành công ${matchedCount}/${bulkOcrState.results.length}`;
            }

            document.getElementById('bulk-ocr-results-wrapper')?.classList.remove('hidden');
            const btnApply = document.getElementById('btn-bulk-ocr-apply');
            if (btnApply) btnApply.disabled = (matchedCount === 0);
        }

        function updateBulkItemRoom(idx, roomId) {
            if (bulkOcrState.results[idx]) {
                bulkOcrState.results[idx].room_id = roomId ? parseInt(roomId) : null;
                const room = bulkOcrState.allRooms.find(r => r.id == roomId);
                bulkOcrState.results[idx].room_number = room ? room.room_number : null;
                bulkOcrState.results[idx].is_matched = !!roomId;
                
                const matchedCount = bulkOcrState.results.filter(r => r.room_id).length;
                const statsBadge = document.getElementById('bulk-match-stats-badge');
                if (statsBadge) {
                    statsBadge.textContent = `Khớp thành công ${matchedCount}/${bulkOcrState.results.length}`;
                }
                const btnApply = document.getElementById('btn-bulk-ocr-apply');
                if (btnApply) btnApply.disabled = (matchedCount === 0);
            }
        }

        function updateBulkItemValue(idx, val) {
            if (bulkOcrState.results[idx]) {
                bulkOcrState.results[idx].value = parseInt(val) || 0;
            }
        }

        function removeBulkItem(idx) {
            bulkOcrState.results.splice(idx, 1);
            renderBulkOcrResults();
        }

        function applyBulkOcrToTable() {
            const validItems = bulkOcrState.results.filter(item => item.room_id && item.value !== null);
            if (validItems.length === 0) {
                alert('Không có phòng nào được chọn để áp dụng!');
                return;
            }

            let appliedCount = 0;
            validItems.forEach(item => {
                const tr = document.querySelector(`tr[data-room-id="${item.room_id}"]`);
                if (tr) {
                    let input = null;
                    if (bulkOcrState.type === 'electricity') {
                        input = tr.querySelector('.new-elec-input');
                    } else {
                        input = tr.querySelector('.new-water-input');
                    }

                    if (input) {
                        input.value = item.value;
                        calculateRowCost(input);
                        
                        const ringClass = bulkOcrState.type === 'electricity' ? 'ring-emerald-500' : 'ring-cyan-500';
                        input.classList.add('ring-2', ringClass);
                        setTimeout(() => {
                            input.classList.remove('ring-2', ringClass);
                        }, 2500);

                        appliedCount++;
                    }
                }
            });

            closeBulkOcrModal();
            alert(`🎉 Đã áp dụng thành công chỉ số cho ${appliedCount} phòng trên bảng chốt số!`);
        }

        // Kéo thả Dropzone listener
        document.addEventListener('DOMContentLoaded', function() {
            const dropzone = document.getElementById('bulk-ocr-dropzone');
            if (dropzone) {
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.add('border-indigo-500', 'bg-indigo-500/10');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.remove('border-indigo-500', 'bg-indigo-500/10');
                    }, false);
                });

                dropzone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    if (files && files.length > 0) {
                        loadBulkOcrFilesList(files);
                    }
                }, false);
            }
        });

        // =======================================================
        // 2.2 FEAT_17_BILLING: TÍNH TIỀN PHÒNG & XUẤT HÓA ĐƠN TỰ ĐỘNG (BẢNG 118 - 123)
        // =======================================================
        let currentInvoiceBillId = null;

        function openInvoiceModal(roomId = '', roomNumber = '', residentName = '', price = '') {
            const modal = document.getElementById('calculate-invoice-modal');
            if (!modal) return;

            // Reset trạng thái lỗi
            document.getElementById('invoice-err-17-01')?.classList.add('hidden');
            document.getElementById('invoice-err-17-02')?.classList.add('hidden');
            const errDueDate = document.getElementById('due_date_error');
            if (errDueDate) {
                errDueDate.textContent = '';
                errDueDate.classList.add('hidden');
            }
            document.getElementById('due_date')?.classList.remove('border-rose-500');

            const roomSelect = document.getElementById('invoice_room_id');
            if (roomSelect) {
                if (roomId) {
                    roomSelect.value = roomId;
                } else if (!roomSelect.value && roomSelect.options.length > 1) {
                    roomSelect.selectedIndex = 1;
                }
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            calculateModalPreview();
        }

        function closeInvoiceModal() {
            const modal = document.getElementById('calculate-invoice-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = 'auto';
            }
        }

        function handleInvoiceRoomChange(roomId) {
            calculateModalPreview();
        }

        function validateDueDateRange() {
            const invoiceDateVal = document.getElementById('invoice_date')?.value;
            const dueDateVal = document.getElementById('due_date')?.value;
            const errEl = document.getElementById('due_date_error');
            const dueDateInput = document.getElementById('due_date');

            if (!dueDateVal) {
                if (errEl) {
                    errEl.textContent = 'Vui lòng chọn hạn chót thanh toán';
                    errEl.classList.remove('hidden');
                }
                dueDateInput?.classList.add('border-rose-500');
                return false;
            }

            if (invoiceDateVal && dueDateVal < invoiceDateVal) {
                if (errEl) {
                    errEl.textContent = 'Hạn thanh toán không được trước ngày lập hóa đơn';
                    errEl.classList.remove('hidden');
                }
                dueDateInput?.classList.add('border-rose-500');
                return false;
            }

            if (errEl) {
                errEl.textContent = '';
                errEl.classList.add('hidden');
            }
            dueDateInput?.classList.remove('border-rose-500');
            return true;
        }

        function calculateModalPreview() {
            const roomSelect = document.getElementById('invoice_room_id');
            if (!roomSelect || !roomSelect.value) return;

            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            const roomPrice = parseInt(selectedOption?.getAttribute('data-price')) || 4500000;
            const roomNumber = selectedOption?.getAttribute('data-room') || '---';
            const residentName = selectedOption?.getAttribute('data-resident') || '';

            // Kiểm tra nếu phòng chưa có cư dân (ERR_17_01)
            const err1701 = document.getElementById('invoice-err-17-01');
            if (!residentName) {
                if (err1701) err1701.classList.remove('hidden');
            } else {
                if (err1701) err1701.classList.add('hidden');
            }

            const elecPrice = parseInt(document.getElementById('invoice_electricity_price')?.value) || 0;
            const waterPrice = parseInt(document.getElementById('invoice_water_price')?.value) || 0;
            const otherFees = parseInt(document.getElementById('invoice_other_fees')?.value) || 0;

            // Kiểm tra ERR_17_02
            const err1702 = document.getElementById('invoice-err-17-02');
            if (elecPrice <= 0 || waterPrice <= 0) {
                if (err1702) err1702.classList.remove('hidden');
            } else {
                if (err1702) err1702.classList.add('hidden');
            }

            // Lấy tiêu thụ từ dòng bảng chốt số nếu có
            const tr = document.querySelector(`tr[data-room-id="${roomSelect.value}"]`);
            let elecUsed = 0;
            let waterUsed = 0;
            if (tr) {
                const oldElec = parseInt(tr.querySelector('[data-field="old-elec"]')?.textContent) || 0;
                const newElec = parseInt(tr.querySelector('.new-elec-input')?.value) || oldElec;
                elecUsed = Math.max(0, newElec - oldElec);

                const oldWater = parseInt(tr.querySelector('[data-field="old-water"]')?.textContent) || 0;
                const newWater = parseInt(tr.querySelector('.new-water-input')?.value) || oldWater;
                waterUsed = Math.max(0, newWater - oldWater);
            }

            const elecCost = elecUsed * elecPrice;
            const waterCost = waterUsed * waterPrice;
            const serviceCost = 150000;
            const totalAmount = roomPrice + elecCost + waterCost + serviceCost + otherFees;

            // Hiển thị summary_card theo Bảng 122 STT 1
            const cardAmount = document.getElementById('summary_card_amount');
            if (cardAmount) cardAmount.textContent = new Intl.NumberFormat('vi-VN').format(totalAmount) + ' đ';

            const summaryRoomTag = document.getElementById('summary_room_tag');
            if (summaryRoomTag) summaryRoomTag.textContent = 'Phòng: ' + roomNumber;

            const summaryResTag = document.getElementById('summary_resident_tag');
            if (summaryResTag) summaryResTag.textContent = residentName ? 'Cư dân: ' + residentName : '(Chưa có cư dân)';

            const elRoomPrice = document.getElementById('summary_room_price');
            if (elRoomPrice) elRoomPrice.textContent = new Intl.NumberFormat('vi-VN').format(roomPrice) + ' đ';

            const elUtilPrice = document.getElementById('summary_utility_price');
            if (elUtilPrice) elUtilPrice.textContent = new Intl.NumberFormat('vi-VN').format(elecCost + waterCost) + ' đ';

            const elExtraPrice = document.getElementById('summary_extra_price');
            if (elExtraPrice) elExtraPrice.textContent = new Intl.NumberFormat('vi-VN').format(serviceCost + otherFees) + ' đ';

            // Cập nhật ảnh qr_code (STT 3)
            const qrImg = document.getElementById('qr_code');
            const addInfo = encodeURIComponent('Thanh toan Phong ' + roomNumber);
            if (qrImg) {
                qrImg.src = `https://img.vietqr.io/image/VCB-1051572297-compact.png?amount=${totalAmount}&addInfo=${addInfo}`;
            }
        }

        async function submitPublishInvoice() {
            if (!validateDueDateRange()) return;

            const roomId = document.getElementById('invoice_room_id')?.value;
            const invoiceDate = document.getElementById('invoice_date')?.value;
            const dueDate = document.getElementById('due_date')?.value;
            const elecPrice = document.getElementById('invoice_electricity_price')?.value;
            const waterPrice = document.getElementById('invoice_water_price')?.value;
            const otherFees = document.getElementById('invoice_other_fees')?.value;

            const btnPublish = document.getElementById('btnPublish');
            if (btnPublish) {
                btnPublish.disabled = true;
                btnPublish.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tính toán và phát hành...';
            }

            try {
                const response = await fetch("{{ route('smartroom.admin.utility.calculate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        room_id: roomId,
                        invoice_date: invoiceDate,
                        due_date: dueDate,
                        electricity_price: elecPrice,
                        water_price: waterPrice,
                        other_fees: otherFees
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.error_code === 'ERR_17_01') {
                        const err1701 = document.getElementById('invoice-err-17-01');
                        if (err1701) {
                            err1701.classList.remove('hidden');
                            document.getElementById('invoice-err-17-01-msg').textContent = data.message;
                        }
                    } else if (data.error_code === 'ERR_17_02') {
                        const err1702 = document.getElementById('invoice-err-17-02');
                        if (err1702) {
                            err1702.classList.remove('hidden');
                            document.getElementById('invoice-err-17-02-msg').textContent = data.message;
                        }
                    } else if (data.error_code === 'ERR_17_DATE_REQUIRED' || data.error_code === 'ERR_17_DATE_RANGE') {
                        const errEl = document.getElementById('due_date_error');
                        if (errEl) {
                            errEl.textContent = data.message;
                            errEl.classList.remove('hidden');
                        }
                        document.getElementById('due_date')?.classList.add('border-rose-500');
                    } else {
                        alert(data.message || 'Có lỗi xảy ra khi tính hóa đơn!');
                    }
                    return;
                }

                // ERR_17_03: Thành công
                currentInvoiceBillId = data.bill?.id || data.data?.bill_id;
                const statusBadge = document.getElementById('invoice-status-badge');
                if (statusBadge) {
                    statusBadge.textContent = 'Chờ thanh toán';
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30';
                }

                const qrImg = document.getElementById('qr_code');
                if (qrImg && data.data?.vietqr_url) {
                    qrImg.src = data.data.vietqr_url;
                }

                // Cập nhật trạng thái dòng phòng trên bảng chốt số sang overdue/chờ thanh toán
                const tr = document.querySelector(`tr[data-room-id="${roomId}"]`);
                if (tr) {
                    const statusDot = tr.querySelector('.rounded-full');
                    if (statusDot) {
                        statusDot.className = 'w-2.5 h-2.5 rounded-full bg-amber-500';
                    }
                }

                alert(`🎉 ${data.message}\nTổng tiền: ${data.data?.formatted_total}\nTrạng thái: Chờ thanh toán`);
            } catch (err) {
                console.error('Invoice Calculation Error:', err);
                alert('Có lỗi mạng hoặc máy chủ. Vui lòng thử lại!');
            } finally {
                if (btnPublish) {
                    btnPublish.disabled = false;
                    btnPublish.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Phát hành hóa đơn';
                }
            }
        }

        function downloadInvoicePdfCurrent() {
            const roomId = document.getElementById('invoice_room_id')?.value;
            const targetId = currentInvoiceBillId || roomId || 1;
            window.open(`/smartroom/admin/utility/${targetId}/invoice-pdf`, '_blank');
        }

        // =======================================================
        // 3. IOT SMART METERING & REALTIME MONITORING (LORAWAN / ESP32 / MODBUS)
        // =======================================================
        const iotState = {
            currentTab: 'realtime',
            currentRoomId: null,
            currentRange: '1h',
            chartData: { electricity: [], water: [] },
            activePoints: [],
            hoveredIndex: null,
            autoSimTimer: null,
            autoSimCount: 0,
            autoPollTimer: null
        };

        function openIotDashboardModal() {
            const modal = document.getElementById('iot-metering-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';

                // Đợi layout modal vẽ xong hoàn toàn để canvas nhận đúng kích thước clientWidth
                setTimeout(() => {
                    loadIotSummary();
                    const roomSelect = document.getElementById('iot-room-filter-select');
                    if (roomSelect && roomSelect.options.length > 1) {
                        if (!roomSelect.value) {
                            roomSelect.selectedIndex = 1;
                        }
                        onIotRoomFilterChange(roomSelect.value);
                    }
                }, 100);

                // Tự động làm mới dữ liệu mỗi 10 giây khi modal đang mở (phục vụ test chu kỳ 1 phút)
                if (iotState.autoPollTimer) clearInterval(iotState.autoPollTimer);
                iotState.autoPollTimer = setInterval(() => {
                    loadIotSummary();
                    if (iotState.currentRoomId) {
                        onIotRoomFilterChange(iotState.currentRoomId, true);
                    }
                }, 10000);
            }
        }

        function closeIotDashboardModal() {
            const modal = document.getElementById('iot-metering-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
                if (iotState.autoPollTimer) {
                    clearInterval(iotState.autoPollTimer);
                    iotState.autoPollTimer = null;
                }
            }
        }

        function switchIotTab(tab) {
            iotState.currentTab = tab;
            ['realtime', 'devices', 'simulator', 'anomalies'].forEach(t => {
                const el = document.getElementById(`iot-tab-${t}`);
                const btn = document.getElementById(`iot-tab-btn-${t}`);
                if (el) {
                    if (t === tab) {
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                }
                if (btn) {
                    if (t === tab) {
                        if (t === 'anomalies') {
                            btn.className = 'px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-rose-500/15 text-rose-300 border border-rose-500/40 shadow-sm';
                        } else {
                            btn.className = 'px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-emerald-500/10 text-emerald-300 border border-emerald-500/30';
                        }
                    } else {
                        btn.className = 'px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-slate-200 border border-transparent';
                    }
                }
            });

            if (tab === 'devices') {
                loadIotSummary();
            } else if (tab === 'anomalies') {
                loadIotSummary();
            } else if (tab === 'realtime') {
                setTimeout(() => {
                    if (iotState.currentRoomId) {
                        onIotRoomFilterChange(iotState.currentRoomId);
                    }
                }, 60);
            }
        }

        async function loadIotSummary() {
            try {
                const response = await fetch("{{ route('smartroom.admin.iot.summary') }}", {
                    headers: { 'Accept': 'application/json' }
                });
                const res = await response.json();
                if (res.success && res.data) {
                    const d = res.data;
                    document.getElementById('iot-kpi-power').innerHTML = `${d.total_power_kw} <span class="text-xs font-bold text-slate-400">kW</span>`;
                    document.getElementById('iot-kpi-water').innerHTML = `${d.total_water_flow} <span class="text-xs font-bold text-slate-400">m3/h</span>`;
                    document.getElementById('iot-kpi-devices').textContent = `${d.online_devices} / ${d.total_devices}`;
                    document.getElementById('iot-kpi-status-hint').textContent = `${d.offline_devices} thiết bị Offline`;
                    document.getElementById('iot-kpi-warnings').textContent = d.warning_devices;

                    // Cập nhật tab Cảnh Báo Dị Thường
                    const anomalies = d.anomalies || [];
                    const tabBadge = document.getElementById('iot-tab-anomalies-badge');
                    const totalBadge = document.getElementById('anomalies-total-badge');
                    const anomaliesCount = anomalies.length || d.warning_devices || 0;

                    if (tabBadge) {
                        tabBadge.textContent = anomaliesCount;
                        if (anomaliesCount > 0) {
                            tabBadge.classList.remove('hidden');
                        } else {
                            tabBadge.classList.add('hidden');
                        }
                    }
                    if (totalBadge) {
                        totalBadge.textContent = `${anomaliesCount} Cảnh báo`;
                    }

                    // Render danh sách chi tiết Cảnh Báo Dị Thường
                    const anomaliesContainer = document.getElementById('iot-anomalies-container');
                    if (anomaliesContainer) {
                        if (anomalies.length === 0) {
                            anomaliesContainer.innerHTML = `
                                <div class="p-8 text-center rounded-2xl bg-slate-950/40 border border-slate-800 space-y-2">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xl flex items-center justify-center mx-auto mb-2">
                                        <i class="fa-solid fa-shield-check"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-200">Hệ Thống An Toàn — Không Có Cảnh Báo Dị Thường Nào</h4>
                                    <p class="text-xs text-slate-400 max-w-md mx-auto">Tất cả các phòng đang hoạt động trong ngưỡng công suất và lưu lượng nước cho phép. Không phát hiện quá tải hoặc rò rỉ nước.</p>
                                </div>
                            `;
                        } else {
                            anomaliesContainer.innerHTML = anomalies.map(a => {
                                const isDanger = a.severity === 'danger';
                                const borderClass = isDanger ? 'border-rose-500/40 bg-rose-950/20' : 'border-amber-500/40 bg-amber-950/20';
                                const iconBadge = isDanger 
                                    ? '<span class="px-2.5 py-1 rounded-xl text-[11px] font-extrabold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/40 flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation text-xs animate-bounce"></i> Cấp Bách</span>'
                                    : '<span class="px-2.5 py-1 rounded-xl text-[11px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation text-xs"></i> Cảnh Báo</span>';
                                const meterIcon = a.meter_type === 'electricity' ? '<i class="fa-solid fa-bolt text-amber-400"></i> Điện' : '<i class="fa-solid fa-droplet text-cyan-400"></i> Nước';
                                const phoneClean = a.resident_phone ? a.resident_phone.replace(/[^0-9]/g, '') : '';
                                const zaloActionBtn = phoneClean 
                                    ? `<a href="https://zalo.me/${phoneClean}" target="_blank" class="px-3.5 py-2 rounded-xl bg-blue-600/20 hover:bg-blue-600 text-blue-300 hover:text-white border border-blue-500/30 text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm" title="Mở Zalo nhắn cho khách"><i class="fa-solid fa-comment-dots"></i> Nhắn Zalo Cư Dân</a>`
                                    : '';

                                return `
                                    <div class="rounded-2xl border ${borderClass} p-5 space-y-4 shadow-xl transition-all">
                                        <!-- Header: Loại cảnh báo, Phòng và Giờ phát sinh -->
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-3.5">
                                            <div class="flex items-center gap-2.5 flex-wrap">
                                                ${iconBadge}
                                                <h5 class="text-sm font-bold text-slate-100">${a.title}</h5>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs font-mono font-semibold text-amber-300 bg-slate-950/80 px-3 py-1.5 rounded-xl border border-slate-800 self-start sm:self-auto">
                                                <i class="fa-regular fa-clock text-amber-400"></i>
                                                <span>${a.recorded_at_formatted}</span>
                                                <span class="text-[10px] text-slate-400">(${a.time_ago})</span>
                                            </div>
                                        </div>

                                        <!-- Thông số đo đạc so với định mức -->
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                                            <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80">
                                                <span class="text-slate-400 block text-[11px] mb-0.5">Vị trí phòng & Cư dân:</span>
                                                <strong class="text-slate-100 text-sm font-bold flex items-center gap-1.5">
                                                    <i class="fa-solid fa-door-open text-indigo-400"></i> Phòng ${a.room_number}
                                                </strong>
                                                <div class="text-[11px] text-slate-400 mt-1 truncate">
                                                    ${a.resident_name} ${a.resident_phone ? `(${a.resident_phone})` : ''}
                                                </div>
                                            </div>

                                            <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80">
                                                <span class="text-slate-400 block text-[11px] mb-0.5">Giá trị đo tức thời:</span>
                                                <div class="text-lg font-black text-rose-400 font-mono flex items-baseline gap-1">
                                                    ${a.measured_value}
                                                </div>
                                                <div class="text-[10px] text-slate-400 mt-0.5 font-mono">Công tơ: ${a.meter_serial} (${meterIcon})</div>
                                            </div>

                                            <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80">
                                                <span class="text-slate-400 block text-[11px] mb-0.5">Ngưỡng an toàn quy chuẩn:</span>
                                                <div class="text-sm font-bold text-emerald-400 font-mono mt-0.5">
                                                    ${a.threshold}
                                                </div>
                                                <div class="text-[10px] text-slate-400 mt-1">Chu kỳ quét: 15 phút/lần</div>
                                            </div>
                                        </div>

                                        <!-- Hộp giải thích TẠI SAO lại cảnh báo & Hướng dẫn xử lý -->
                                        <div class="p-4 rounded-xl bg-slate-950/90 border border-slate-800/90 space-y-2.5 text-xs">
                                            <div>
                                                <div class="text-[11px] font-extrabold uppercase tracking-wider text-amber-300 flex items-center gap-1.5 mb-1">
                                                    <i class="fa-solid fa-circle-question"></i> Tại sao hệ thống phát tín hiệu cảnh báo?
                                                </div>
                                                <p class="text-slate-300 leading-relaxed">${a.reason}</p>
                                            </div>
                                            <div class="pt-2 border-t border-slate-800/80 flex items-start gap-2">
                                                <i class="fa-solid fa-lightbulb text-amber-400 text-xs shrink-0 mt-0.5"></i>
                                                <div class="text-slate-300">
                                                    <strong class="text-slate-200">Hướng dẫn xử lý cho chủ trọ:</strong> ${a.suggestion}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Thanh nút hành động nhanh -->
                                        <div class="flex flex-wrap items-center justify-between gap-2.5 pt-1">
                                            <div class="flex items-center gap-2">
                                                ${a.room_id ? `
                                                    <button type="button" onclick="viewRoomIotChart(${a.room_id}, '${a.room_number}')" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-md shadow-emerald-600/20">
                                                        <i class="fa-solid fa-chart-line"></i> Xem Đồ Thị Phòng ${a.room_number}
                                                    </button>
                                                ` : ''}
                                                ${zaloActionBtn}
                                            </div>
                                            <span class="text-[11px] text-slate-500 font-mono">Bản ghi đo: #${a.id}</span>
                                        </div>
                                    </div>
                                `;
                            }).join('');
                        }
                    }

                    const tbody = document.getElementById('iot-devices-table-body');
                    if (tbody && d.devices) {
                        if (d.devices.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-6 text-slate-500">Chưa có thiết bị công tơ nào được kích hoạt. Hãy dùng Bộ Giả Lập để phát gói tin thử nghiệm!</td></tr>';
                        } else {
                            tbody.innerHTML = d.devices.map(dev => {
                                const statusBadge = dev.is_online
                                    ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><i class="fa-solid fa-circle text-[6px] mr-1"></i>Online</span>'
                                    : '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20"><i class="fa-solid fa-circle text-[6px] mr-1"></i>Offline</span>';
                                const typeIcon = dev.meter_type === 'electricity' ? '<i class="fa-solid fa-bolt text-amber-400"></i> Điện' : '<i class="fa-solid fa-droplet text-cyan-400"></i> Nước';
                                const unit = dev.meter_type === 'electricity' ? 'kWh' : 'm3';

                                return `
                                    <tr class="hover:bg-slate-900/30 transition-all font-mono">
                                        <td class="px-4 py-3 font-bold text-slate-200">${dev.device_code}</td>
                                        <td class="px-4 py-3 text-slate-400 font-sans">${dev.protocol}</td>
                                        <td class="px-4 py-3 font-sans font-bold text-emerald-400">P.${dev.room_number}</td>
                                        <td class="px-4 py-3 text-slate-400">${dev.meter_serial}</td>
                                        <td class="px-4 py-3 font-sans">${typeIcon}</td>
                                        <td class="px-4 py-3 font-bold text-slate-200">${dev.last_reading} ${unit}</td>
                                        <td class="px-4 py-3 font-sans">${statusBadge}</td>
                                        <td class="px-4 py-3 text-slate-500 font-sans">${dev.last_seen}</td>
                                    </tr>
                                `;
                            }).join('');
                        }
                    }
                }
            } catch (err) {
                console.error('Lỗi khi tải IoT Summary:', err);
            }
        }

        function viewRoomIotChart(roomId, roomNumber) {
            openIotDashboardModal();
            switchIotTab('realtime');
            setTimeout(() => {
                const roomSelect = document.getElementById('iot-room-filter-select');
                if (roomSelect) {
                    roomSelect.value = roomId;
                }
                onIotRoomFilterChange(roomId);
            }, 100);
        }

        async function onIotRoomFilterChange(roomId, isSilent = false) {
            if (!roomId) {
                document.getElementById('iot-chart-empty-state')?.classList.remove('hidden');
                document.getElementById('iot-room-stats-banner')?.classList.add('hidden');
                clearIotCanvas();
                return;
            }

            iotState.currentRoomId = roomId;
            try {
                const res = await fetch(`/smartroom/admin/iot/rooms/${roomId}/realtime?range=${iotState.currentRange}`);
                const json = await res.json();
                if (json.success && json.data) {
                    const data = json.data;
                    const hasData = (data.series.electricity && data.series.electricity.length > 0) || 
                                    (data.series.water && data.series.water.length > 0);

                    const emptyState = document.getElementById('iot-chart-empty-state');
                    if (hasData) {
                        if (emptyState) emptyState.classList.add('hidden');
                    } else {
                        if (emptyState) emptyState.classList.remove('hidden');
                    }

                    document.getElementById('iot-room-stats-banner')?.classList.remove('hidden');
                    document.getElementById('iot-chart-title').textContent = `Phụ Tải Phòng ${data.room.room_number} (Số SX Điện: ${data.room.electric_serial || 'Tự động gán'})`;

                    document.getElementById('iot-stat-elec-val').textContent = (data.latest.electric_reading !== null ? data.latest.electric_reading : '1380.50') + ' kWh';
                    document.getElementById('iot-stat-water-val').textContent = (data.latest.water_reading !== null ? data.latest.water_reading : '35.20') + ' m3';
                    const waterFlowEl = document.getElementById('iot-stat-water-flow');
                    if (waterFlowEl) {
                        waterFlowEl.textContent = (data.latest.water_flow_rate !== null ? Number(data.latest.water_flow_rate).toFixed(1) : '0.0') + ' L/m';
                    }
                    document.getElementById('iot-stat-cost-val').textContent = (data.consumption_today.estimated_cost || 0).toLocaleString('vi-VN') + 'đ';

                    iotState.chartData = data.series;
                    iotState.latestData = data.latest;
                    renderIotCanvasChart(data.series.electricity || [], data.series.water || []);
                }
            } catch (e) {
                console.error('Lỗi tải dữ liệu phòng:', e);
            }
        }

        function changeIotChartRange(range) {
            iotState.currentRange = range;
            ['15m', '1h', '24h', '7d'].forEach(r => {
                const btn = document.getElementById(`btn-range-${r}`);
                if (btn) {
                    if (r === range) {
                        btn.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
                    } else {
                        btn.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-900 text-slate-400 hover:bg-slate-800 border border-slate-800';
                    }
                }
            });

            if (iotState.currentRoomId) {
                onIotRoomFilterChange(iotState.currentRoomId);
            }
        }

        function clearIotCanvas() {
            const canvas = document.getElementById('iot-realtime-chart-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function renderIotCanvasChart(electricData, waterData, highlightIdx = null) {
            const canvas = document.getElementById('iot-realtime-chart-canvas');
            if (!canvas) return;

            const container = canvas.parentElement;
            // Luôn đọc chiều rộng và cao cố định từ container cha (khung bao w-full h-64),
            // Tuyệt đối không đọc từ rect.width của canvas để tránh lỗi canvas tự co lại mỗi lần re-render
            const width = Math.max(300, Math.floor(container ? (container.clientWidth - 16) : 750));
            const height = Math.max(180, Math.floor(container ? (container.clientHeight - 16) : 230));
            const dpr = window.devicePixelRatio || 1;

            canvas.width = Math.floor(width * dpr);
            canvas.height = Math.floor(height * dpr);
            canvas.style.width = '100%';
            canvas.style.height = '100%';

            const ctx = canvas.getContext('2d');
            ctx.setTransform(1, 0, 0, 1, 0, 0);
            ctx.scale(dpr, dpr);

            const padding = { top: 30, right: 40, bottom: 40, left: 55 };
            const chartW = Math.max(10, width - padding.left - padding.right);
            const chartH = Math.max(10, height - padding.top - padding.bottom);

            ctx.clearRect(0, 0, width, height);

            // 1. Vẽ các đường lưới ngang và nhãn trục Y
            ctx.strokeStyle = '#1e293b';
            ctx.lineWidth = 1;
            const steps = 4;

            const maxPower = electricData.length > 0 
                ? Math.max(...electricData.map(p => p.power || 0), 1200) 
                : 1500;
            const maxFlow = waterData.length > 0 
                ? Math.max(...waterData.map(p => p.flow_rate || 0), 4) 
                : 5;

            for (let i = 0; i <= steps; i++) {
                const y = padding.top + (chartH / steps) * i;
                ctx.beginPath();
                ctx.moveTo(padding.left, y);
                ctx.lineTo(width - padding.right, y);
                ctx.stroke();

                // Nhãn Watt bên trái
                const powerVal = Math.round(maxPower - (maxPower / steps) * i);
                ctx.fillStyle = '#fbbf24';
                ctx.font = '10px monospace';
                ctx.textAlign = 'right';
                ctx.fillText(`${powerVal}W`, padding.left - 8, y + 3);

                // Nhãn Lưu lượng nước bên phải
                const flowVal = (maxFlow - (maxFlow / steps) * i).toFixed(1);
                ctx.fillStyle = '#06b6d4';
                ctx.textAlign = 'left';
                ctx.fillText(`${flowVal}L`, width - padding.right + 6, y + 3);
            }

            if (electricData.length === 0 && waterData.length === 0) {
                return;
            }

            // Ghi nhận toạ độ các điểm đo để phục vụ tra cứu khi hover chuột
            const totalPoints = Math.max(electricData.length, waterData.length);
            iotState.activePoints = [];
            for (let i = 0; i < totalPoints; i++) {
                const ePt = electricData[i] || null;
                const wPt = waterData[i] || null;
                const px = padding.left + (chartW / Math.max(1, totalPoints - 1)) * i;
                const pyElec = ePt ? (height - padding.bottom - ((ePt.power || 0) / maxPower) * chartH) : null;
                const pyWater = wPt ? (height - padding.bottom - ((wPt.flow_rate || 0) / maxFlow) * chartH) : null;

                iotState.activePoints.push({
                    idx: i,
                    x: px,
                    elecY: pyElec,
                    waterY: pyWater,
                    time: ePt?.time || wPt?.time || '',
                    power: ePt?.power || 0,
                    voltage: ePt?.voltage || 220,
                    current: ePt?.current || 0,
                    reading: ePt?.reading || 0,
                    flow_rate: wPt?.flow_rate || 0,
                    water_reading: wPt?.reading || 0
                });
            }

            // 2. Vẽ Area Gradient và Đường cong cho Công Suất Điện (Amber)
            if (electricData.length > 0) {
                const getX = (idx) => padding.left + (chartW / Math.max(1, electricData.length - 1)) * idx;
                const getY = (val) => height - padding.bottom - ((val || 0) / maxPower) * chartH;

                // Vùng đổ màu Gradient (Area fill)
                const gradElec = ctx.createLinearGradient(0, padding.top, 0, height - padding.bottom);
                gradElec.addColorStop(0, 'rgba(251, 191, 36, 0.28)');
                gradElec.addColorStop(1, 'rgba(251, 191, 36, 0.01)');

                ctx.beginPath();
                ctx.moveTo(getX(0), height - padding.bottom);
                electricData.forEach((pt, idx) => {
                    ctx.lineTo(getX(idx), getY(pt.power));
                });
                ctx.lineTo(getX(electricData.length - 1), height - padding.bottom);
                ctx.closePath();
                ctx.fillStyle = gradElec;
                ctx.fill();

                // Vẽ đường viền chính
                ctx.strokeStyle = '#fbbf24';
                ctx.lineWidth = 2.5;
                ctx.shadowColor = 'rgba(251, 191, 36, 0.5)';
                ctx.shadowBlur = 6;
                ctx.beginPath();
                electricData.forEach((pt, idx) => {
                    const x = getX(idx);
                    const y = getY(pt.power);
                    if (idx === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                });
                ctx.stroke();
                ctx.shadowBlur = 0;

                // Vẽ các điểm nút và nhãn thời gian trục X
                ctx.fillStyle = '#f59e0b';
                electricData.forEach((pt, idx) => {
                    const x = getX(idx);
                    const y = getY(pt.power);
                    ctx.beginPath();
                    ctx.arc(x, y, 3, 0, Math.PI * 2);
                    ctx.fill();

                    // In nhãn thời gian trục X (cách khoảng để không đè chữ)
                    if (idx === 0 || idx === electricData.length - 1 || idx % Math.ceil(electricData.length / 5) === 0) {
                        ctx.fillStyle = '#94a3b8';
                        ctx.font = '10px monospace';
                        ctx.textAlign = 'center';
                        ctx.fillText(pt.time || '', x, height - padding.bottom + 18);
                        ctx.fillStyle = '#f59e0b';
                    }
                });

                // Hiệu ứng Pulse Glow cho điểm đo mới nhất (nếu không hover điểm khác)
                if (highlightIdx === null) {
                    const lastIdx = electricData.length - 1;
                    const lastX = getX(lastIdx);
                    const lastY = getY(electricData[lastIdx].power);
                    ctx.fillStyle = 'rgba(251, 191, 36, 0.35)';
                    ctx.beginPath();
                    ctx.arc(lastX, lastY, 8, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.fillStyle = '#ffffff';
                    ctx.beginPath();
                    ctx.arc(lastX, lastY, 3.5, 0, Math.PI * 2);
                    ctx.fill();
                }
            }

            // 3. Vẽ Đường cong cho Lưu Lượng Nước (Cyan nét đứt) và dải màu mờ
            if (waterData.length > 0) {
                const getX = (idx) => padding.left + (chartW / Math.max(1, waterData.length - 1)) * idx;
                const getY = (val) => height - padding.bottom - ((val || 0) / maxFlow) * chartH;

                // Vùng đổ màu Gradient mờ Cyan cho lưu lượng nước
                const gradWater = ctx.createLinearGradient(0, padding.top, 0, height - padding.bottom);
                gradWater.addColorStop(0, 'rgba(6, 182, 212, 0.16)');
                gradWater.addColorStop(1, 'rgba(6, 182, 212, 0.01)');

                ctx.beginPath();
                ctx.moveTo(getX(0), height - padding.bottom);
                waterData.forEach((pt, idx) => {
                    ctx.lineTo(getX(idx), getY(pt.flow_rate));
                });
                ctx.lineTo(getX(waterData.length - 1), height - padding.bottom);
                ctx.closePath();
                ctx.fillStyle = gradWater;
                ctx.fill();

                ctx.strokeStyle = '#06b6d4';
                ctx.lineWidth = 2;
                ctx.setLineDash([5, 3]);
                ctx.shadowColor = 'rgba(6, 182, 212, 0.4)';
                ctx.shadowBlur = 4;
                ctx.beginPath();
                waterData.forEach((pt, idx) => {
                    const x = getX(idx);
                    const y = getY(pt.flow_rate);
                    if (idx === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                });
                ctx.stroke();
                ctx.setLineDash([]);
                ctx.shadowBlur = 0;

                // Các điểm nút nước
                ctx.fillStyle = '#22d3ee';
                waterData.forEach((pt, idx) => {
                    ctx.beginPath();
                    ctx.arc(getX(idx), getY(pt.flow_rate), 2.5, 0, Math.PI * 2);
                    ctx.fill();
                });
            }

            // 4. Hiển thị đường dóng dọc và hiệu ứng nổi bật khi rê chuột (Hover Crosshair)
            if (highlightIdx !== null && iotState.activePoints[highlightIdx]) {
                const hl = iotState.activePoints[highlightIdx];

                // Đường dóng trục đứng
                ctx.save();
                ctx.strokeStyle = 'rgba(251, 191, 36, 0.45)';
                ctx.lineWidth = 1.5;
                ctx.setLineDash([4, 4]);
                ctx.beginPath();
                ctx.moveTo(hl.x, padding.top);
                ctx.lineTo(hl.x, height - padding.bottom);
                ctx.stroke();
                ctx.restore();

                // Điểm sáng nổi bật trên đường công suất điện
                if (hl.elecY !== null) {
                    ctx.fillStyle = 'rgba(251, 191, 36, 0.35)';
                    ctx.beginPath();
                    ctx.arc(hl.x, hl.elecY, 10, 0, Math.PI * 2);
                    ctx.fill();

                    ctx.fillStyle = '#fbbf24';
                    ctx.beginPath();
                    ctx.arc(hl.x, hl.elecY, 5, 0, Math.PI * 2);
                    ctx.fill();

                    ctx.fillStyle = '#ffffff';
                    ctx.beginPath();
                    ctx.arc(hl.x, hl.elecY, 2.5, 0, Math.PI * 2);
                    ctx.fill();
                }

                // Điểm sáng nổi bật và Callout Badge trên đường lưu lượng nước
                if (hl.waterY !== null) {
                    ctx.fillStyle = 'rgba(6, 182, 212, 0.4)';
                    ctx.beginPath();
                    ctx.arc(hl.x, hl.waterY, 8, 0, Math.PI * 2);
                    ctx.fill();

                    ctx.fillStyle = '#22d3ee';
                    ctx.beginPath();
                    ctx.arc(hl.x, hl.waterY, 4, 0, Math.PI * 2);
                    ctx.fill();

                    // Callout Badge hiển thị trực tiếp lưu lượng nước (L/m) tại điểm
                    ctx.save();
                    const waterValText = `${Number(hl.flow_rate || 0).toFixed(1)} L/m`;
                    ctx.font = 'bold 9px monospace';
                    const tw = ctx.measureText(waterValText).width;
                    const bw = tw + 10;
                    const bh = 16;
                    const bx = Math.min(width - padding.right - bw, Math.max(padding.left, hl.x - bw / 2));
                    const by = Math.max(padding.top + 2, hl.waterY - 22);

                    ctx.fillStyle = '#020617';
                    ctx.fillRect(bx, by, bw, bh);
                    ctx.strokeStyle = '#06b6d4';
                    ctx.lineWidth = 1;
                    ctx.strokeRect(bx, by, bw, bh);

                    ctx.fillStyle = '#22d3ee';
                    ctx.textAlign = 'center';
                    ctx.fillText(waterValText, bx + bw / 2, by + 11);
                    ctx.restore();
                }

                // Huy hiệu mốc thời gian nổi bật ở trục hoành
                if (hl.time) {
                    ctx.save();
                    const badgeW = 62;
                    const badgeH = 18;
                    const bx = hl.x - badgeW / 2;
                    const by = height - padding.bottom + 4;

                    ctx.fillStyle = '#0f172a';
                    ctx.fillRect(bx, by, badgeW, badgeH);

                    ctx.strokeStyle = '#fbbf24';
                    ctx.lineWidth = 1;
                    ctx.strokeRect(bx, by, badgeW, badgeH);

                    ctx.fillStyle = '#f8fafc';
                    ctx.font = 'bold 10px monospace';
                    ctx.textAlign = 'center';
                    ctx.fillText(hl.time, hl.x, by + 13);
                    ctx.restore();
                }
            }

            // Gắn lắng nghe sự kiện rê chuột (chỉ gán 1 lần)
            setupIotChartHoverEvents();
        }

        function setupIotChartHoverEvents() {
            const canvas = document.getElementById('iot-realtime-chart-canvas');
            const tooltip = document.getElementById('iot-chart-tooltip');
            if (!canvas || canvas.dataset.hoverBound) return;
            canvas.dataset.hoverBound = 'true';

            canvas.addEventListener('mousemove', (e) => {
                if (!iotState.activePoints || iotState.activePoints.length === 0) {
                    if (tooltip) tooltip.classList.add('hidden');
                    return;
                }

                const rect = canvas.getBoundingClientRect();
                const container = canvas.parentElement;
                const containerW = container ? (container.clientWidth - 16) : 750;
                const scaleX = rect.width > 0 ? (containerW / rect.width) : 1;
                const mouseX = (e.clientX - rect.left) * scaleX;

                // Tìm điểm đo gần nhất với con trỏ chuột theo trục X
                let closest = null;
                let minDiff = Infinity;
                iotState.activePoints.forEach((pt) => {
                    const diff = Math.abs(pt.x - mouseX);
                    if (diff < minDiff) {
                        minDiff = diff;
                        closest = pt;
                    }
                });

                if (!closest || minDiff > 55) {
                    if (tooltip) tooltip.classList.add('hidden');
                    if (iotState.hoveredIndex !== null) {
                        iotState.hoveredIndex = null;
                        renderIotCanvasChart(iotState.chartData.electricity || [], iotState.chartData.water || [], null);
                    }
                    return;
                }

                // Điền dữ liệu vào Tooltip hiển thị
                if (tooltip) {
                    const timeEl = document.getElementById('iot-tt-time');
                    const powerEl = document.getElementById('iot-tt-power');
                    const waterEl = document.getElementById('iot-tt-water');
                    const readingEl = document.getElementById('iot-tt-reading');
                    const waterReadingEl = document.getElementById('iot-tt-water-reading');

                    if (timeEl) timeEl.textContent = closest.time || '--:--:--';
                    if (powerEl) powerEl.textContent = `${Math.round(closest.power || 0)} W`;
                    if (waterEl) waterEl.textContent = `${Number(closest.flow_rate || 0).toFixed(1)} L/m`;
                    if (readingEl) readingEl.textContent = `${Number(closest.reading || 0).toFixed(2)} kWh`;
                    if (waterReadingEl) waterReadingEl.textContent = `${Number(closest.water_reading || 0).toFixed(2)} m3`;

                    // Hiển thị cảnh báo trực tiếp trong tooltip nếu điểm này vượt ngưỡng
                    const ttWarnBanner = document.getElementById('iot-tt-warning-banner');
                    const ttWarnText = document.getElementById('iot-tt-warning-text');
                    if (ttWarnBanner && ttWarnText) {
                        if (closest.power > 4500) {
                            ttWarnText.textContent = `Quá tải: ${Math.round(closest.power)}W (Vượt 4500W)!`;
                            ttWarnBanner.classList.remove('hidden');
                        } else if (closest.flow_rate > 15) {
                            ttWarnText.textContent = `Lưu lượng cao: ${Number(closest.flow_rate).toFixed(1)} L/m!`;
                            ttWarnBanner.classList.remove('hidden');
                        } else {
                            ttWarnBanner.classList.add('hidden');
                        }
                    }

                    tooltip.classList.remove('hidden');

                    const container = canvas.parentElement;
                    const containerW = container ? container.clientWidth : 750;
                    const containerH = container ? container.clientHeight : 250;

                    // Định vị tooltip thông minh tránh bị khuất ở mép phải
                    let leftPos = closest.x + 16;
                    if (leftPos + 185 > containerW) {
                        leftPos = Math.max(10, closest.x - 195);
                    }

                    const targetY = (closest.elecY !== null ? closest.elecY : (closest.waterY !== null ? closest.waterY : 60)) - 45;
                    const topPos = Math.max(10, Math.min(targetY, containerH - 120));

                    tooltip.style.left = `${leftPos}px`;
                    tooltip.style.top = `${topPos}px`;
                }

                // Đồng bộ cập nhật các ô thống kê bên dưới theo mốc thời gian chuột đang trỏ
                const statElec = document.getElementById('iot-stat-elec-val');
                const statWater = document.getElementById('iot-stat-water-val');
                const statFlow = document.getElementById('iot-stat-water-flow');
                if (statElec && closest.reading) statElec.textContent = `${Number(closest.reading).toFixed(2)} kWh`;
                if (statWater && closest.water_reading) statWater.textContent = `${Number(closest.water_reading).toFixed(2)} m3`;
                if (statFlow && closest.flow_rate !== undefined) statFlow.textContent = `${Number(closest.flow_rate).toFixed(1)} L/m`;

                if (iotState.hoveredIndex !== closest.idx) {
                    iotState.hoveredIndex = closest.idx;
                    renderIotCanvasChart(iotState.chartData.electricity || [], iotState.chartData.water || [], closest.idx);
                }
            });

            canvas.addEventListener('mouseleave', () => {
                if (tooltip) tooltip.classList.add('hidden');
                if (iotState.hoveredIndex !== null) {
                    iotState.hoveredIndex = null;
                    renderIotCanvasChart(iotState.chartData.electricity || [], iotState.chartData.water || [], null);
                }

                // Khôi phục giá trị tức thời mới nhất ở các thẻ thống kê
                if (iotState.latestData) {
                    const statElec = document.getElementById('iot-stat-elec-val');
                    const statWater = document.getElementById('iot-stat-water-val');
                    const statFlow = document.getElementById('iot-stat-water-flow');
                    if (statElec && iotState.latestData.electric_reading !== null) {
                        statElec.textContent = `${Number(iotState.latestData.electric_reading).toFixed(2)} kWh`;
                    }
                    if (statWater && iotState.latestData.water_reading !== null) {
                        statWater.textContent = `${Number(iotState.latestData.water_reading).toFixed(2)} m3`;
                    }
                    if (statFlow && iotState.latestData.water_flow_rate !== null) {
                        statFlow.textContent = `${Number(iotState.latestData.water_flow_rate).toFixed(1)} L/m`;
                    }
                }
            });
        }

        async function quickInjectDemoData() {
            const roomId = iotState.currentRoomId || document.getElementById('iot-room-filter-select')?.value;
            if (!roomId) {
                alert('Vui lòng chọn 1 phòng trước khi tạo điểm đo!');
                return;
            }

            const payload = {
                room_id: roomId,
                meter_type: 'electricity',
                protocol: 'esp32_wifi',
                reading: (Math.random() * 20 + 1380).toFixed(2),
                power: Math.floor(Math.random() * 500 + 700),
                voltage: (220 + Math.random() * 3 - 1.5).toFixed(1),
                current: 4.2
            };

            await dispatchSimulationPacket(payload, true);

            // Đồng thời bắn 1 gói nước
            await dispatchSimulationPacket({
                room_id: roomId,
                meter_type: 'water',
                protocol: 'lorawan',
                reading: (Math.random() * 5 + 35).toFixed(2),
                flow_rate: (Math.random() * 1.5 + 0.8).toFixed(2)
            }, true);

            loadIotSummary();
            onIotRoomFilterChange(roomId);
        }

        function onSimMeterTypeChange(val) {
            const elecInputs = document.getElementById('sim-elec-inputs');
            const waterInputs = document.getElementById('sim-water-inputs');
            const readingInp = document.getElementById('sim-reading');

            if (val === 'electricity') {
                elecInputs?.classList.remove('hidden');
                waterInputs?.classList.add('hidden');
                if (readingInp) readingInp.value = 1420.5;
            } else {
                elecInputs?.classList.add('hidden');
                waterInputs?.classList.remove('hidden');
                if (readingInp) readingInp.value = 48.2;
            }
        }

        async function dispatchSimulationPacket(payload, isSilent = false) {
            try {
                const response = await fetch("{{ route('smartroom.admin.iot.simulate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const res = await response.json();
                if (res.success) {
                    loadIotSummary();
                    if (payload.room_id) {
                        const roomSelect = document.getElementById('iot-room-filter-select');
                        if (roomSelect && roomSelect.value != payload.room_id) {
                            roomSelect.value = payload.room_id;
                        }
                        onIotRoomFilterChange(payload.room_id);
                    }
                    if (!isSilent) {
                        alert('🎉 ' + res.message);
                    }
                } else if (!isSilent) {
                    alert('Lỗi: ' + (res.message || 'Không thể gửi gói tin'));
                }
            } catch (err) {
                console.error(err);
                if (!isSilent) alert('Có lỗi xảy ra khi kết nối máy chủ IoT!');
            }
        }

        async function sendIotSimulation(event) {
            if (event) event.preventDefault();
            const btn = document.getElementById('btn-send-sim');
            const form = document.getElementById('iot-simulator-form');
            if (!form) return;

            const formData = new FormData(form);
            const payload = Object.fromEntries(formData.entries());

            const originalHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang truyền...';
            }

            await dispatchSimulationPacket(payload, false);

            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        function toggleAutoSimulation() {
            const btn = document.getElementById('btn-auto-sim');
            const icon = document.getElementById('auto-sim-icon');
            const text = document.getElementById('auto-sim-text');
            const counterBadge = document.getElementById('auto-sim-counter');
            const countEl = document.getElementById('auto-sim-count');

            if (iotState.autoSimTimer) {
                // Tắt auto simulation
                clearInterval(iotState.autoSimTimer);
                iotState.autoSimTimer = null;
                if (btn) btn.className = 'px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-amber-300 border border-amber-500/30 rounded-xl text-xs font-bold transition-all flex items-center gap-2';
                if (icon) icon.className = 'fa-solid fa-play text-amber-400';
                if (text) text.textContent = 'Bật Tự Động Bắn Gói Tin (Mỗi 1 Phút)';
            } else {
                // Bật auto simulation (chu kỳ 60 giây / 1 phút)
                if (btn) btn.className = 'px-3.5 py-2 bg-amber-500/20 text-amber-300 border border-amber-500/50 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-lg shadow-amber-500/10';
                if (icon) icon.className = 'fa-solid fa-pause text-amber-400';
                if (text) text.textContent = 'Đang Bắn Mỗi 1 Phút (Bấm để Dừng)';
                if (counterBadge) counterBadge.classList.remove('hidden');

                const autoStep = () => {
                    const form = document.getElementById('iot-simulator-form');
                    if (!form) return;
                    const readingInp = document.getElementById('sim-reading');
                    if (readingInp) {
                        // Tăng nhẹ chỉ số lũy kế theo thời gian thực mỗi phút
                        const currentVal = parseFloat(readingInp.value) || 1420.0;
                        readingInp.value = (currentVal + 0.05).toFixed(2);
                    }

                    const formData = new FormData(form);
                    const payload = Object.fromEntries(formData.entries());
                    // Dao động nhẹ công suất 650W - 1100W
                    if (payload.meter_type === 'electricity') {
                        payload.power = Math.floor(Math.random() * (1100 - 650 + 1) + 650);
                        payload.voltage = (220 + (Math.random() * 3 - 1.5)).toFixed(1);
                    } else {
                        payload.flow_rate = (Math.random() * 2.5 + 0.5).toFixed(2);
                    }

                    dispatchSimulationPacket(payload, true);
                    iotState.autoSimCount++;
                    if (countEl) countEl.textContent = iotState.autoSimCount;
                };

                // Phát ngay gói đầu tiên
                autoStep();
                // Lặp lại mỗi 60,000ms = 1 phút
                iotState.autoSimTimer = setInterval(autoStep, 60000);
            }
        }


        async function triggerIotAutoSync(btn) {
            if (!confirm('Bạn có chắc chắn muốn CHỐT SỐ TỰ ĐỘNG TỪ IOT cho toàn bộ các phòng vào hóa đơn tháng này?')) {
                return;
            }

            const originalHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang đồng bộ...';
            }

            try {
                const response = await fetch("{{ route('smartroom.admin.iot.sync_billing') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const res = await response.json();
                if (res.success) {
                    alert(`⚡ ĐỒNG BỘ THÀNH CÔNG!\n${res.message}`);
                    window.location.href = "{{ route('smartroom.admin', ['tab' => 'utility-section']) }}";
                } else {
                    alert('Lỗi: ' + (res.message || 'Không thể đồng bộ chốt số'));
                }
            } catch (e) {
                console.error(e);
                alert('Có lỗi xảy ra khi gọi API chốt số IoT!');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            }
        }

        function applyIotReadingToInput(btn, value) {
            const row = btn.closest('tr');
            if (!row) return;
            const input = btn.previousElementSibling;
            if (input && input.tagName === 'INPUT') {
                input.value = value;
                calculateRowCost(input);
                input.classList.add('ring-2', 'ring-emerald-400');
                setTimeout(() => input.classList.remove('ring-2', 'ring-emerald-400'), 2000);
            }
        }

        // Resident search
        function searchResidentTable() {

            const query = document.querySelector('input[name="resident_q"]')?.value.toLowerCase() || '';
            const rows = document.getElementById('resident-table-body').querySelectorAll('tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if(text.includes(query)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        }

        // Resident delete simulation
        function deleteRow(btn) {
            if(confirm('Bạn có chắc chắn muốn xóa cư dân này ra khỏi phòng trọ?')) {
                btn.closest('tr').remove();
            }
        }

        // Resident add modal triggers
        function toggleAddResidentModal(show) {
            const modal = document.getElementById('add-resident-modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function submitAddResident(e) {
            e.preventDefault();
            const name = document.getElementById('add-res-name').value;
            const phone = document.getElementById('add-res-phone').value;
            const room = document.getElementById('add-res-room').value;
            const dateStr = document.getElementById('add-res-date').value;
            
            const date = new Date(dateStr);
            const formattedDate = date.getDate().toString().padStart(2, '0') + '/' + (date.getMonth() + 1).toString().padStart(2, '0') + '/' + date.getFullYear();

            // Insert row to resident table
            const tbody = document.getElementById('resident-table-body');
            const tr = document.createElement('tr');
            tr.className = "hover:bg-slate-900/30 transition-all";
            tr.innerHTML = `
                <td class="px-6 py-4 font-bold text-slate-200">${name}</td>
                <td class="px-6 py-4 text-xs font-semibold text-indigo-400">P. ${room}</td>
                <td class="px-6 py-4 text-xs text-slate-400">${phone}</td>
                <td class="px-6 py-4 text-xs text-slate-500">${formattedDate}</td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Đang hoạt động</span>
                </td>
                <td class="px-6 py-4 flex gap-2 justify-center">
                    <button class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-lg text-xs font-bold text-indigo-400 transition-all"><i class="fa-regular fa-pen-to-square"></i> Sửa</button>
                    <button onclick="deleteRow(this)" class="px-2.5 py-1.5 bg-rose-500/5 hover:bg-rose-500/10 border border-rose-500/10 hover:border-rose-500/20 rounded-lg text-xs font-bold text-rose-400 transition-all"><i class="fa-regular fa-trash-can"></i> Xóa</button>
                </td>
            `;
            tbody.appendChild(tr);
            
            // Update the room card status in Sơ đồ phòng
            const roomCards = document.querySelectorAll('.room-card');
            roomCards.forEach(card => {
                if (card.querySelector('span').textContent.trim() === 'P. ' + room) {
                    // Update card design
                    card.classList.remove('room-empty');
                    card.classList.add('room-occupied');
                    
                    const badge = card.querySelector('span:nth-child(2)');
                    badge.textContent = 'Đã thuê';
                    badge.className = 'px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-500/10 text-red-400 border border-red-500/20';
                    
                    card.querySelector('h4').textContent = 'Cư dân: ' + name;
                    card.querySelector('h4').className = 'text-xs font-bold text-slate-400 truncate mb-1';
                    
                    // Attach click handler with new details
                    card.onclick = function() {
                        openRoomDetail(room, 'occupied', name, phone, '4.000.000đ', '0 kWh', '0 m3');
                    };
                }
            });

            // Close modal
            toggleAddResidentModal(false);
            alert(`Đã thêm thành công cư dân ${name} vào phòng ${room}!`);
            
            // Clear inputs
            document.getElementById('add-res-name').value = '';
            document.getElementById('add-res-phone').value = '';
            document.getElementById('add-res-date').value = '';
        }

        // ==========================================
        // 1. CƠ CHẾ KHÓA ĐỒNG THỜI (OPTIMISTIC LOCKING)
        // Giải thích: Server và Client truyền tay nhau thuộc tính 'version' (phiên bản dữ liệu).
        // Khi Landlord sửa dữ liệu cư dân, giá trị version hiện tại sẽ được gửi kèm lên Server.
        // Server kiểm tra xem version đó có khớp với database không. Nếu một Landlord khác đã lưu trước đó,
        // version trong database sẽ tăng lên, dẫn đến xung đột phiên bản và Server sẽ chặn cập nhật (HTTP 409).
        // Dưới client, nếu nhận được HTTP 409 hoặc lỗi 422, ta sẽ hiển thị cảnh báo cho người dùng reload lại trang.
        // ==========================================

        // Edit resident modal triggers
        function toggleEditResidentModal(show) {
            const modal = document.getElementById('edit-resident-modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function openEditResidentModal(id, name, phone, roomId, startDate, dob, cccd, hometown, tempStatus, version) {
            const form = document.getElementById('edit-resident-form');
            form.action = `/smartroom/admin/resident/${id}`;
            
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-phone').value = phone;
            document.getElementById('edit-room-id').value = roomId;
            document.getElementById('edit-start-date').value = startDate;
            
            // Điền thông tin cá nhân mở rộng
            document.getElementById('edit-dob').value = dob || '';
            document.getElementById('edit-cccd').value = cccd || '';
            document.getElementById('edit-hometown').value = hometown || '';
            document.getElementById('edit-temp-status').value = tempStatus || 'none';
            
            // Optimistic Locking: Gán version hiện tại của bản ghi
            document.getElementById('edit-version').value = version || 1;
            
            toggleEditResidentModal(true);
        }

        // View resident modal triggers
        function toggleViewResidentModal(show) {
            const modal = document.getElementById('view-resident-modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function openViewResidentModal(id, name, phone, email, room, startDate, status, dob, cccd, hometown, tempStatus) {
            document.getElementById('view-name').textContent = name;
            document.getElementById('view-phone').textContent = phone;
            document.getElementById('view-email').textContent = email || 'Chưa cung cấp';
            document.getElementById('view-room').textContent = 'Phòng ' + room;
            
            // Gán dữ liệu mở rộng
            document.getElementById('view-dob').textContent = dob ? formatDateString(dob) : 'Chưa cập nhật';
            document.getElementById('view-cccd').textContent = cccd || 'Chưa cập nhật';
            document.getElementById('view-hometown').textContent = hometown || 'Chưa cập nhật';
            
            const tempBadge = document.getElementById('view-temp-status');
            if (tempStatus === 'registered') {
                tempBadge.textContent = 'Đã đăng ký';
                tempBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
            } else if (tempStatus === 'absent') {
                tempBadge.textContent = 'Tạm vắng';
                tempBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20';
            } else {
                tempBadge.textContent = 'Chưa ĐK';
                tempBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20';
            }

            // Gán nút cấu hình người thân đi cùng
            const manageBtn = document.getElementById('view-manage-relatives-btn');
            manageBtn.onclick = function() {
                toggleViewResidentModal(false);
                openRelativesModal(id, name);
            };

            // Tải danh sách người thân trọ cùng qua AJAX
            loadRelativesForView(id);
            
            toggleViewResidentModal(true);
        }

        function formatDateString(dateStr) {
            if (!dateStr) return '—';
            try {
                const date = new Date(dateStr);
                if (isNaN(date.getTime())) return dateStr;
                return date.getDate().toString().padStart(2, '0') + '/' + (date.getMonth() + 1).toString().padStart(2, '0') + '/' + date.getFullYear();
            } catch(e) {
                return dateStr;
            }
        }

        // Tải danh sách người thân chỉ để hiển thị trong View Modal
        function loadRelativesForView(residentId) {
            const table = document.getElementById('view-relatives-table');
            const tbody = document.getElementById('view-relatives-tbody');
            const emptyDiv = document.getElementById('view-relatives-empty');

            tbody.innerHTML = '';
            table.classList.add('hidden');
            emptyDiv.classList.remove('hidden');

            fetch(`/smartroom/admin/resident/${residentId}/relatives`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.relatives.length > 0) {
                        data.relatives.forEach(relative => {
                            let tempBadgeHTML = '';
                            if (relative.temporary_residence_status === 'registered') {
                                tempBadgeHTML = '<span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400">Đã ĐK</span>';
                            } else if (relative.temporary_residence_status === 'absent') {
                                tempBadgeHTML = '<span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/10 text-amber-400">Tạm vắng</span>';
                            } else {
                                tempBadgeHTML = '<span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-500/10 text-rose-400">Chưa ĐK</span>';
                            }

                            const tr = document.createElement('tr');
                            tr.className = "hover:bg-slate-900/10 transition-all border-b border-slate-800 last:border-0";
                            tr.innerHTML = `
                                <td class="py-2 text-slate-200 font-semibold text-xs">${relative.name}</td>
                                <td class="py-2 text-slate-400 text-xs">${relative.relationship}</td>
                                <td class="py-2 text-slate-400 text-xs font-mono">${relative.cccd || '—'}</td>
                                <td class="py-2">${tempBadgeHTML}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                        table.classList.remove('hidden');
                        emptyDiv.classList.add('hidden');
                    }
                })
                .catch(err => console.error("Error fetching relatives for view:", err));
        }

        // ==========================================
        // RELATIVES AJAX CRUD LOGIC (Quản lý người thân tạm trú)
        // ==========================================
        let currentResidentId = null;

        function toggleRelativesModal(show) {
            const modal = document.getElementById('relatives-modal');
            if (show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
                resetRelativeForm();
            }
        }

        function openRelativesModal(residentId, residentName) {
            currentResidentId = residentId;
            document.getElementById('relatives-modal-resident-name').textContent = residentName;
            
            // Load danh sách người thân hiện tại
            loadRelativesList();
            toggleRelativesModal(true);
        }

        function loadRelativesList() {
            const container = document.getElementById('relatives-list-container');
            container.innerHTML = `
                <div class="flex items-center justify-center py-10">
                    <div class="w-6 h-6 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            `;

            fetch(`/smartroom/admin/resident/${currentResidentId}/relatives`)
                .then(res => res.json())
                .then(data => {
                    container.innerHTML = '';
                    if (data.success && data.relatives.length > 0) {
                        data.relatives.forEach(rel => {
                            let statusText = 'Chưa ĐK';
                            let statusClass = 'bg-rose-500/10 text-rose-400 border border-rose-500/20';
                            if (rel.temporary_residence_status === 'registered') {
                                statusText = 'Đã ĐK';
                                statusClass = 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                            } else if (rel.temporary_residence_status === 'absent') {
                                statusText = 'Tạm vắng';
                                statusClass = 'bg-amber-500/10 text-amber-400 border border-amber-500/20';
                            }

                            const card = document.createElement('div');
                            card.className = "flex items-center justify-between p-3 rounded-xl bg-slate-950/40 border border-slate-900 hover:border-slate-800 transition-all";
                            
                            // Tránh lỗi ký tự đặc biệt khi parse JSON trong inline onclick
                            const relEscaped = JSON.stringify(rel).replace(/'/g, "\\'").replace(/"/g, "&quot;");
                            
                            const deleteButton = currentUserIsLandlord ? `
                                    <button onclick="deleteRelative(${rel.id})" class="w-7 h-7 rounded-lg bg-slate-900 hover:bg-rose-950/20 border border-slate-800 hover:border-rose-900/50 flex items-center justify-center text-rose-400 transition-all" title="Xoa">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    </button>
                            ` : '';

                            card.innerHTML = `
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-xs font-bold text-slate-200">${rel.name}</h4>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-500/10 text-indigo-400 font-semibold">${rel.relationship}</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded ${statusClass} font-bold">${statusText}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 mt-1 flex gap-3">
                                        <span>CCCD: <strong class="font-mono text-slate-400">${rel.cccd || '—'}</strong></span>
                                        <span>Sinh: <strong class="font-mono text-slate-400">${rel.dob ? formatDateString(rel.dob) : '—'}</strong></span>
                                        <span class="truncate max-w-[150px]" title="${rel.hometown}">Quê: <strong class="text-slate-400">${rel.hometown || '—'}</strong></span>
                                    </div>
                                </div>
                                <div class="flex gap-1.5">
                                    <button onclick="editRelative(JSON.parse('${JSON.stringify(rel).replace(/'/g, "\\'")}'))" class="w-7 h-7 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 flex items-center justify-center text-cyan-400 transition-all" title="Sửa">
                                        <i class="fa-solid fa-pencil text-[10px]"></i>
                                    </button>
                                    ${deleteButton}
                                </div>
                            `;
                            container.appendChild(card);
                        });
                    } else {
                        container.innerHTML = `
                            <div class="flex flex-col items-center justify-center py-12 text-slate-500 text-xs">
                                <i class="fa-solid fa-people-arrows text-2xl mb-2 text-slate-600"></i>
                                <span>Chưa có người thân tạm trú nào đăng ký cùng.</span>
                            </div>
                        `;
                    }
                })
                .catch(err => {
                    container.innerHTML = `<p class="text-xs text-rose-400 py-4 text-center">Không thể tải danh sách người thân!</p>`;
                    console.error(err);
                });
        }

        function resetRelativeForm() {
            document.getElementById('relative-id').value = '';
            document.getElementById('relative-version').value = '1';
            document.getElementById('relative-name').value = '';
            document.getElementById('relative-relationship').value = '';
            document.getElementById('relative-dob').value = '';
            document.getElementById('relative-cccd').value = '';
            document.getElementById('relative-hometown').value = '';
            document.getElementById('relative-temp-status').value = 'none';

            document.getElementById('relative-form-title').innerHTML = '<i class="fa-solid fa-user-plus text-indigo-400"></i> Thêm Người Thân Mới';
            document.getElementById('relative-submit-btn').innerHTML = '<i class="fa-solid fa-save"></i> <span>Lưu Lại</span>';
            document.getElementById('relative-reset-btn').classList.add('hidden');
            document.getElementById('relative-submit-btn').className = "w-full py-2 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/20 transition-all flex items-center justify-center gap-1.5";
        }

        function editRelative(rel) {
            document.getElementById('relative-id').value = rel.id;
            document.getElementById('relative-version').value = rel.version;
            document.getElementById('relative-name').value = rel.name;
            document.getElementById('relative-relationship').value = rel.relationship;
            document.getElementById('relative-dob').value = rel.dob || '';
            document.getElementById('relative-cccd').value = rel.cccd || '';
            document.getElementById('relative-hometown').value = rel.hometown || '';
            document.getElementById('relative-temp-status').value = rel.temporary_residence_status || 'none';

            document.getElementById('relative-form-title').innerHTML = '<i class="fa-solid fa-user-pen text-cyan-400"></i> Cập Nhật Người Thân';
            document.getElementById('relative-submit-btn').innerHTML = '<i class="fa-solid fa-save"></i> <span>Cập Nhật</span>';
            document.getElementById('relative-reset-btn').classList.remove('hidden');
            document.getElementById('relative-submit-btn').className = "w-1/2 py-2 rounded-lg text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 shadow-md shadow-cyan-600/20 transition-all flex items-center justify-center gap-1.5";
        }

        function saveRelative(e) {
            e.preventDefault();
            const relativeId = document.getElementById('relative-id').value;
            const submitBtn = document.getElementById('relative-submit-btn');

            const payload = {
                name: document.getElementById('relative-name').value,
                relationship: document.getElementById('relative-relationship').value,
                dob: document.getElementById('relative-dob').value,
                cccd: document.getElementById('relative-cccd').value,
                hometown: document.getElementById('relative-hometown').value,
                temporary_residence_status: document.getElementById('relative-temp-status').value,
                version: document.getElementById('relative-version').value
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const originalHTML = submitBtn.innerHTML;
            
            // Chống click liên tiếp
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i>';

            let url = `/smartroom/admin/resident/${currentResidentId}/relative`;
            let method = 'POST';

            if (relativeId) {
                url = `/smartroom/admin/relative/${relativeId}`;
                method = 'PUT';
            }

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            })
            .then(async res => {
                const isJson = res.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await res.json() : null;

                if (res.status === 409) {
                    // Xử lý xung đột phiên bản dữ liệu (Optimistic Locking)
                    alert("❌ Lỗi Xung Đột Phiên Bản (Optimistic Locking):\nDữ liệu của người thân này đã bị thay đổi bởi một quản trị viên khác. Vui lòng đóng và mở lại danh sách để nhận dữ liệu mới nhất.");
                    return false;
                }

                if (!res.ok) {
                    throw new Error(data?.message || "Lỗi xử lý API!");
                }

                return data;
            })
            .then(data => {
                if (data) {
                    alert(data.message || "Đã lưu thông tin người thân thành công!");
                    resetRelativeForm();
                    loadRelativesList();
                }
            })
            .catch(err => {
                alert("Lỗi: " + err.message);
                console.error(err);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
            });
        }

        function deleteRelative(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa thông tin người thân này?')) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            fetch(`/smartroom/admin/relative/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || "Đã xóa thành công!");
                    loadRelativesList();
                } else {
                    alert("Có lỗi xảy ra: " + (data.message || "Không thể xóa!"));
                }
            })
            .catch(err => {
                alert("Không thể kết nối máy chủ để xóa!");
                console.error(err);
            });
        }

        // ==========================================
        // 2. CHỐNG SPAM CLICK (ANTI-SPAM frontend)
        // Giải thích: Vô hiệu hóa nút nhấn hoặc form submit ngay sau click đầu tiên,
        // ngăn chặn việc gửi trùng lặp nhiều request cùng lúc khi mạng chậm.
        // ==========================================
        function antiSpamSubmit(form) {
            const btn = form.querySelector('.anti-spam-btn') || form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                const origHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang xử lý...';
            }
            return true;
        }

        function confirmAndDisable(form, message) {
            if (confirm(message)) {
                const btn = form.querySelector('.anti-spam-btn') || form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i>';
                }
                return true;
            }
            return false;
        }

        // ==========================================
        // 3. CHỐNG MỞ F12 & CÔNG CỤ NHÀ PHÁT TRIỂN (ANTI-F12 / DEVTOOLS)
        // Giải thích: Hạn chế người dùng hoặc hacker táy máy thay đổi DOM, xem mã nguồn,
        // hoặc bắt các request API nhạy cảm bằng cách chặn phím tắt và theo dõi DevTools.
        // ==========================================
        (function() {
            // Chặn phím tắt mở Developer Tools
            window.addEventListener('keydown', function(e) {
                // F12
                if (e.keyCode === 123) {
                    e.preventDefault();
                    showDevToolsWarning();
                    return false;
                }
                // Ctrl+Shift+I
                if (e.ctrlKey && e.shiftKey && e.keyCode === 73) {
                    e.preventDefault();
                    showDevToolsWarning();
                    return false;
                }
                // Ctrl+Shift+J
                if (e.ctrlKey && e.shiftKey && e.keyCode === 74) {
                    e.preventDefault();
                    showDevToolsWarning();
                    return false;
                }
                // Ctrl+U (Xem nguồn trang)
                if (e.ctrlKey && e.keyCode === 85) {
                    e.preventDefault();
                    showDevToolsWarning();
                    return false;
                }
            });

            // Chặn chuột phải
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                showDevToolsWarning();
                return false;
            });

            // Phát hiện DevTools mở bằng sự khác biệt về kích thước màn hình
            const devtools = {
                isOpen: false,
                orientation: undefined
            };
            const threshold = 160;
            
            setInterval(function() {
                const widthThreshold = window.outerWidth - window.innerWidth > threshold;
                const heightThreshold = window.outerHeight - window.innerHeight > threshold;
                
                if (widthThreshold || heightThreshold) {
                    if (!devtools.isOpen) {
                        devtools.isOpen = true;
                        console.warn("Cảnh báo: Phát hiện bảng điều khiển DevTools!");
                    }
                } else {
                    devtools.isOpen = false;
                }
            }, 1000);

            function showDevToolsWarning() {
                console.warn("🔐 Chức năng F12 và chuột phải đã bị khóa để bảo mật trang Quản trị.");
            }
        })();

        // CHARTS INITIALIZATION
        window.addEventListener('DOMContentLoaded', () => {
            // Revenue Chart
            const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
            const gradRev = ctxRevenue.createLinearGradient(0, 0, 0, 300);
            gradRev.addColorStop(0, '#6366f1');
            gradRev.addColorStop(1, '#4f46e5');

            new Chart(ctxRevenue, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartMonths) !!},
                    datasets: [{
                        label: 'Doanh thu (VND)',
                        data: {!! json_encode($chartRevenue) !!},
                        backgroundColor: gradRev,
                        hoverBackgroundColor: '#818cf8',
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 16
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            grid: { color: 'rgba(255, 255, 255, 0.03)' },
                            ticks: {
                                color: '#64748b',
                                font: { size: 10, weight: 'bold' },
                                callback: function(value) { return (value / 1000000) + 'M'; }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#64748b',
                                font: { size: 10, weight: 'bold' }
                            }
                        }
                    }
                }
            });

            // Revenue Breakdown Doughnut Chart
            const ctxBreakdown = document.getElementById('revenueBreakdownChart').getContext('2d');
            let breakdownChart = new Chart(ctxBreakdown, {
                type: 'doughnut',
                data: {
                    labels: ['Tiền phòng', 'Tiền điện', 'Tiền nước', 'Dịch vụ'],
                    datasets: [{
                        data: [0, 0, 0, 0],
                        backgroundColor: [
                            '#6366f1',
                            '#f59e0b',
                            '#06b6d4',
                            '#10b981'
                        ],
                        borderWidth: 2,
                        borderColor: '#0a0f1d',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed !== null) {
                                        label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            fetch('/api/revenue-breakdown')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const b = data.breakdown;
                        const p = data.percentages;
                        breakdownChart.data.datasets[0].data = [b.room, b.electric, b.water, b.service];
                        breakdownChart.update();
                        
                        document.getElementById('breakdown-total-txt').textContent = (data.total / 1000000).toFixed(1) + 'M';
                        document.getElementById('breakdown-room-pct').textContent = p.room + '%';
                        document.getElementById('breakdown-elec-pct').textContent = p.electric + '%';
                        document.getElementById('breakdown-water-pct').textContent = p.water + '%';
                        document.getElementById('breakdown-service-pct').textContent = p.service + '%';
                    }
                })
                .catch(err => console.error("Error loading revenue breakdown:", err));

            // Status Pie Chart
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Trống', 'Đang thuê', 'Nợ tiền'],
                    datasets: [{
                        data: [{{ $emptyRooms }}, {{ $occupiedRooms }}, {{ $overdueRooms }}],
                        backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                        borderColor: '#0a0f1d',
                        borderWidth: 4,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((sum, item) => sum + item, 0);
                                    const value = context.parsed || 0;
                                    const percent = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${context.label}: ${value} phòng (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Trigger calculation for any pre-populated utility inputs
            document.querySelectorAll('.new-elec-input').forEach(input => {
                if (input.value) {
                    calculateRowCost(input);
                }
            });
        });

        let currentQrAccountNo = '1051572297';

        function showVietQR(billId) {
            if (!canReceiveOnlinePayments) {
                return requireKycForOnlinePayments();
            }

            const qrModal = document.getElementById('vietqr-modal');
            document.getElementById('qr-modal-loading').classList.remove('hidden');
            document.getElementById('qr-modal-content').classList.add('hidden');
            qrModal.classList.remove('hidden');

            const endpoint = `/smartroom/admin/utility/${billId}/vietqr`;

            fetch(endpoint, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data && data.success) {
                        const qrUrl = data.qr_url || data.vietqr_url;
                        const roomNum = data.room_number || currentActiveRoomNumber || 'N/A';
                        const resident = data.resident_name || 'Khách thuê / Cư dân';
                        const totalAmt = data.amount || data.total_amount || 0;
                        const formattedAmt = data.formatted_amount || (Number(totalAmt).toLocaleString('vi-VN') + " đ");
                        const bankName = data.bank_name || data.bank_id || 'Vietcombank (VCB)';
                        const accNo = data.account_no || data.account_number || '1051572297';
                        const accName = data.account_name || data.account_holder || 'CHỦ TRỌ';
                        const desc = data.description ? decodeURIComponent(data.description) : `Thanh toan Phong ${roomNum}`;

                        currentQrAccountNo = accNo;

                        document.getElementById('qr-modal-room').textContent = `Phòng ${roomNum}`;
                        document.getElementById('qr-modal-tenant').textContent = resident;
                        document.getElementById('qr-modal-amount').textContent = formattedAmt;
                        document.getElementById('qr-modal-bank').textContent = `${bankName} - ${accNo}`;
                        document.getElementById('qr-modal-name').textContent = accName;
                        document.getElementById('qr-modal-desc').textContent = desc;
                        document.getElementById('qr-modal-image').src = qrUrl;
                        document.getElementById('qr-modal-download').href = qrUrl;
                        
                        document.getElementById('qr-modal-loading').classList.add('hidden');
                        document.getElementById('qr-modal-content').classList.remove('hidden');
                    } else {
                        const currentCard = document.querySelector(`.room-card[data-latest-bill-id="${billId}"]`);
                        const roomNum = currentCard ? currentCard.getAttribute('data-room-number') : (currentActiveRoomNumber || '201');
                        const amount = currentCard ? currentCard.getAttribute('data-total-bill') : '0';
                        showVietQRFallback(roomNum, amount);
                    }
                })
                .catch(err => {
                    console.warn("Chuyển sang cơ chế tạo mã VietQR dự phòng:", err);
                    const currentCard = document.querySelector(`.room-card[data-latest-bill-id="${billId}"]`);
                    const roomNum = currentCard ? currentCard.getAttribute('data-room-number') : (currentActiveRoomNumber || '201');
                    const amount = currentCard ? currentCard.getAttribute('data-total-bill') : '0';
                    showVietQRFallback(roomNum, amount);
                });
        }

        function showVietQRFallback(roomNum, amount) {
            if (!canReceiveOnlinePayments) {
                return requireKycForOnlinePayments();
            }

            const qrModal = document.getElementById('vietqr-modal');
            qrModal.classList.remove('hidden');

            const amt = parseInt(String(amount).replace(/\D/g, '')) || 0;
            const bankId = 'VCB';
            const bankName = 'Vietcombank (VCB)';
            const accountNo = '1051572297';
            const accountName = 'CHỦ TRỌ';
            const desc = `Thanh toan Phong ${roomNum}`;
            const qrUrl = `https://img.vietqr.io/image/VCB-1051572297-compact.png?amount=${amt}&addInfo=${encodeURIComponent(desc)}`;

            currentQrAccountNo = accountNo;

            document.getElementById('qr-modal-room').textContent = `Phòng ${roomNum}`;
            document.getElementById('qr-modal-tenant').textContent = 'Khách mới / Cư dân';
            document.getElementById('qr-modal-amount').textContent = amt.toLocaleString('vi-VN') + " đ";
            document.getElementById('qr-modal-bank').textContent = `${bankName} - ${accountNo}`;
            document.getElementById('qr-modal-name').textContent = accountName;
            document.getElementById('qr-modal-desc').textContent = desc;
            document.getElementById('qr-modal-image').src = qrUrl;
            document.getElementById('qr-modal-download').href = qrUrl;

            document.getElementById('qr-modal-loading').classList.add('hidden');
            document.getElementById('qr-modal-content').classList.remove('hidden');
        }

        let activeMessageType = 'zalo';
        let currentRecipientPhone = '';
        let currentRecipientName = '';
        let currentMsgType = 'contract'; // 'contract' or 'debt'

        function openRoomZaloPersonalChat() {
            const card = currentActiveRoomId ? document.getElementById('room-card-' + currentActiveRoomId) : null;
            const phone = card ? card.getAttribute('data-resident-phone') : currentRecipientPhone;
            const name = card ? card.getAttribute('data-resident-name') : currentRecipientName;
            const roomNum = currentActiveRoomNumber || (card ? card.getAttribute('data-room-number') : '');
            const total = card ? card.getAttribute('data-total-bill') : '';
            
            if (!phone) {
                alert("Khách trọ chưa có số điện thoại hợp lệ để mở Zalo!");
                return;
            }

            const messageText = `Kính gửi anh/chị ${name || 'cư dân'}, Ban quản lý thông báo tiền phòng và hóa đơn dịch vụ tháng này của Phòng ${roomNum} chưa được thanh toán với tổng số tiền là ${total || 'tiền phòng'}. Vui lòng chuyển khoản thanh toán sớm giúp mình nhé. Trân trọng cảm ơn!`;

            openPersonalZaloChat(phone, messageText);
        }

        function openPersonalZaloChat(phone = null, messageText = null) {
            const targetPhone = phone || document.getElementById('msg-phone-input')?.value || currentRecipientPhone;
            const targetMsg = messageText || document.getElementById('msg-text-input')?.value;

            if (!targetPhone) {
                alert("Vui lòng nhập hoặc chọn số điện thoại khách trọ để mở Zalo!");
                return;
            }

            let cleanPhone = String(targetPhone).replace(/\D/g, '');
            if (cleanPhone.startsWith('84') && cleanPhone.length === 11) {
                cleanPhone = '0' + cleanPhone.substring(2);
            }

            if (targetMsg && navigator.clipboard) {
                navigator.clipboard.writeText(targetMsg).then(() => {
                    if (window.showAdminToast) {
                        window.showAdminToast("Đã tự động sao chép nội dung nhắc nợ! Đang mở Zalo cá nhân, bạn chỉ cần nhấn Ctrl + V để dán và gửi.", "success", "Mở Chat Zalo Cá Nhân");
                    } else {
                        alert("Đã sao chép nội dung nhắc nợ! Đang mở Zalo, bạn chỉ cần nhấn Ctrl + V để dán và gửi.");
                    }
                }).catch(() => {});
            }

            const zaloUrl = `https://zalo.me/${cleanPhone}`;
            window.open(zaloUrl, '_blank');
        }
        
        function openSendMsgModal(phone, name, messageText, msgType) {
            currentRecipientPhone = phone;
            currentRecipientName = name;
            currentMsgType = msgType;
            activeMessageType = 'zalo'; 
            
            document.getElementById('msg-phone-input').value = phone;
            document.getElementById('msg-text-input').value = messageText;
            document.getElementById('phone-screen-title').textContent = name || 'Khách thuê';
            
            updateMessagePreview();
            switchMsgTypeTab('zalo');
            
            document.getElementById('msg-success-overlay').classList.add('hidden');
            document.getElementById('msg-send-btn').disabled = false;
            document.getElementById('msg-send-btn').innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Gửi Tin Ngay';
            
            document.getElementById('send-msg-modal').classList.remove('hidden');
        }
        
        function closeSendMsgModal() {
            document.getElementById('send-msg-modal').classList.add('hidden');
        }
        
        function switchMsgTypeTab(type) {
            activeMessageType = type;
            const tabZalo = document.getElementById('msg-tab-zalo');
            const tabSms = document.getElementById('msg-tab-sms');
            const previewContainer = document.getElementById('msg-preview-container');
            const phoneHeader = document.getElementById('phone-header');
            
            if (type === 'zalo') {
                tabZalo.className = "flex-1 py-2 text-center text-xs font-bold text-indigo-400 bg-indigo-500/10 border-b-2 border-indigo-500 transition-all cursor-pointer";
                tabSms.className = "flex-1 py-2 text-center text-xs font-medium text-slate-500 hover:text-slate-300 transition-all cursor-pointer";
                previewContainer.className = "flex-1 p-4 overflow-y-auto space-y-3 bg-[#0f172a] rounded-2xl border border-slate-800/80 flex flex-col justify-end";
                phoneHeader.className = "flex items-center justify-between px-4 py-3 bg-[#1e293b] border-b border-slate-800 rounded-t-3xl text-slate-200";
                document.getElementById('phone-app-name').textContent = "Zalo Messenger";
                document.getElementById('preview-bubble').className = "max-w-[85%] rounded-2xl px-4 py-2 text-xs bg-indigo-600 text-white self-end ml-auto shadow-md";
            } else {
                tabSms.className = "flex-1 py-2 text-center text-xs font-bold text-emerald-400 bg-emerald-500/10 border-b-2 border-emerald-500 transition-all cursor-pointer";
                tabZalo.className = "flex-1 py-2 text-center text-xs font-medium text-slate-500 hover:text-slate-300 transition-all cursor-pointer";
                previewContainer.className = "flex-1 p-4 overflow-y-auto space-y-3 bg-[#0b0f19] rounded-2xl border border-slate-800/80 flex flex-col justify-end";
                phoneHeader.className = "flex items-center justify-between px-4 py-3 bg-[#111827] border-b border-slate-800 rounded-t-3xl text-slate-200";
                document.getElementById('phone-app-name').textContent = "Tin nhắn SMS";
                document.getElementById('preview-bubble').className = "max-w-[85%] rounded-2xl px-4 py-2 text-xs bg-emerald-600 text-white self-end ml-auto shadow-md";
            }
        }
        
        function updateMessagePreview() {
            const val = document.getElementById('msg-text-input').value;
            document.getElementById('preview-bubble-text').textContent = val || "(Trống)";
        }
        
        function selectMsgTemplate(templateIndex) {
            let msg = '';
            if (currentMsgType === 'contract') {
                if (templateIndex === 1) {
                    msg = `Kính gửi anh/chị ${currentRecipientName}, hợp đồng thuê phòng của anh/chị đã được ban quản lý khởi tạo. Vui lòng ký trực tuyến tại đây: [Link ký]`;
                } else if (templateIndex === 2) {
                    msg = `Chào ${currentRecipientName}, vui lòng kiểm tra và thực hiện ký hợp đồng online sớm nhất để hoàn tất thủ tục nhận phòng nhé.`;
                } else {
                    msg = `Thông báo: Link ký hợp đồng thuê phòng của anh/chị đã sẵn sàng. Truy cập ngay: [Link ký]`;
                }
            } else {
                if (templateIndex === 1) {
                    msg = `Kính gửi anh/chị ${currentRecipientName}, ban quản lý thông báo tiền phòng tháng này chưa thanh toán. Vui lòng nộp trước ngày 10. Trân trọng!`;
                } else if (templateIndex === 2) {
                    msg = `Nhắc nhở: Phòng của anh/chị còn dư nợ hóa đơn dịch vụ điện nước. Vui lòng thanh toán qua ứng dụng SmartRoom.`;
                } else {
                    msg = `Ban quản lý nhà trọ thông báo nhắc nợ tiền phòng tháng này đối với anh/chị ${currentRecipientName}. Liên hệ chủ nhà để biết thêm chi tiết.`;
                }
            }
            
            const originalVal = document.getElementById('msg-text-input').value;
            const linkMatch = originalVal.match(/https?:\/\/[^\s]+/);
            if (linkMatch && linkMatch[0]) {
                msg = msg.replace('[Link ký]', linkMatch[0]);
            }
            
            document.getElementById('msg-text-input').value = msg;
            updateMessagePreview();
        }
        
        function triggerSendMessage() {
            const phone = document.getElementById('msg-phone-input').value;
            const messageText = document.getElementById('msg-text-input').value;
            const btn = document.getElementById('msg-send-btn');
            
            if (!phone || !messageText) {
                alert("Vui lòng điền đầy đủ số điện thoại và nội dung tin nhắn!");
                return;
            }
            
            btn.disabled = true;
            btn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';
            
            fetch('/api/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    phone: phone,
                    message: messageText,
                    type: activeMessageType
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('msg-success-overlay').classList.remove('hidden');
                    
                    if (navigator.vibrate) {
                        navigator.vibrate([100, 50, 100]);
                    }
                    
                    setTimeout(() => {
                        closeSendMsgModal();
                        alert(`Đã gửi tin nhắn nhắc nhở đến ${data.phone} qua kênh ${data.type.toUpperCase()} thành công!`);
                    }, 2200);
                } else {
                    alert("Có lỗi xảy ra: " + (data.error || "Gửi tin thất bại"));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Gửi Tin Ngay';
                }
            })
            .catch(err => {
                alert("Không thể kết nối đến máy chủ!");
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i> Gửi Tin Ngay';
                console.error(err);
            });
        }

        function closeVietQRModal() {
            document.getElementById('vietqr-modal').classList.add('hidden');
        }

        function copyBankAccount() {
            const accNo = currentQrAccountNo || '1051572297';
            navigator.clipboard.writeText(accNo).then(() => {
                const btn = document.getElementById('qr-modal-copy');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> Đã sao chép!';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            });
        }

        function triggerAutoRemind(btn) {
            if (!confirm('Hệ thống sẽ tự động quét các hóa đơn chưa đóng trong tháng này và gửi tin nhắn nhắc nợ qua Zalo hàng loạt. Bạn có chắc chắn muốn thực hiện?')) {
                return;
            }

            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang tự động gửi...';

            fetch("{{ route('smartroom.admin.utility.auto_remind') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;

                if (data.success) {
                    if (data.sent_count > 0) {
                        let roomList = data.sent_rooms.map(r => `Phòng ${r.room_number} (${r.resident_name}): ${r.total_amount_formatted}`).join('\n');
                        alert(`🚀 Tự động nhắc nợ thành công!\nĐã gửi tin nhắn nhắc nợ Zalo tới ${data.sent_count} phòng chưa đóng tiền:\n\n${roomList}`);
                    } else {
                        alert(`✨ Tuyệt vời! Tất cả các phòng đã hoàn thành đóng tiền trọ tháng này.`);
                    }
                } else {
                    alert("Có lỗi xảy ra khi gửi nhắc nợ tự động.");
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                alert("Không thể kết nối đến máy chủ để thực hiện gửi nhắc nợ hàng loạt!");
                console.error(err);
            });
        }

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderAiList(title, items, iconClass) {
            if (!items || items.length === 0) {
                return '';
            }

            return `
                <div class="mt-3">
                    <div class="text-[11px] uppercase tracking-wider font-bold text-slate-500 flex items-center gap-2">
                        <i class="${iconClass}"></i> ${escapeHtml(title)}
                    </div>
                    <ul class="mt-2 space-y-1.5 text-xs text-slate-300">
                        ${items.map(item => `<li class="flex gap-2"><span class="text-indigo-400">•</span><span>${escapeHtml(item)}</span></li>`).join('')}
                    </ul>
                </div>
            `;
        }

        function loadAiDashboardInsight(btn) {
            const target = document.getElementById('ai-dashboard-insight');
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang phân tích...';
            target.innerHTML = '<span class="text-slate-500">AI đang đọc dữ liệu dashboard...</span>';

            fetch("{{ route('smartroom.admin.ai.dashboard_insight') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;

                if (!data.success) {
                    target.innerHTML = '<span class="text-rose-400">Không thể tạo nhận xét AI.</span>';
                    return;
                }

                const insight = data.insight;
                const badge = insight.used_ai
                    ? '<span class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">AI</span>'
                    : '<span class="text-[10px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20">Fallback</span>';

                target.innerHTML = `
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-sm text-slate-200 font-semibold">${escapeHtml(insight.summary || '')}</p>
                        ${badge}
                    </div>
                    ${renderAiList('Điểm đáng chú ý', insight.highlights, 'fa-solid fa-chart-line text-emerald-400')}
                    ${renderAiList('Rủi ro', insight.risks, 'fa-solid fa-triangle-exclamation text-amber-400')}
                    ${renderAiList('Gợi ý', insight.suggestions, 'fa-solid fa-lightbulb text-indigo-400')}
                `;
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                target.innerHTML = '<span class="text-rose-400">Không thể kết nối để tạo nhận xét AI.</span>';
                console.error(err);
            });
        }

        // ==========================================
        // FEAT_14_AI_ASSISTANT_HUB JAVASCRIPT LOGIC
        // ==========================================
        let currentAiHubMode = 'landlord_advisor';
        let aiHubCountdownTimer = null;
        let lastAiHubGeneratedContent = '';

        function switchAiHubMode(mode, btn) {
            currentAiHubMode = mode;
            document.querySelectorAll('.ai-hub-tab-btn').forEach(b => {
                b.className = 'ai-hub-tab-btn flex-1 min-w-[130px] py-2 px-3 rounded-xl font-semibold text-slate-400 hover:text-slate-200 transition-all flex items-center justify-center gap-2';
            });
            if (btn) {
                btn.className = 'ai-hub-tab-btn flex-1 min-w-[130px] py-2 px-3 rounded-xl font-bold bg-indigo-600 text-white transition-all flex items-center justify-center gap-2';
            }

            const promptEl = document.getElementById('ai-hub-prompt');
            const toneSelect = document.getElementById('ai-hub-tone');
            if (promptEl) {
                clearAiHubPromptError();
                if (mode === 'landlord_advisor') {
                    promptEl.placeholder = 'Nhập yêu cầu viết bài marketing SEO hoặc câu hỏi kinh doanh (ví dụ: "Soạn bài đăng phòng 101 chuẩn SEO")...';
                    if (toneSelect) toneSelect.value = 'professional';
                } else if (mode === 'landlord_qa') {
                    promptEl.placeholder = 'Đặt bất kỳ câu hỏi nào về nghiệp vụ quản lý phòng trọ, pháp lý, tính toán điện nước, xử lý hợp đồng... (ví dụ: "Quy trình đăng ký tạm trú cho khách thuê?")...';
                    if (toneSelect) toneSelect.value = 'professional';
                } else if (mode === 'room_finder') {
                    promptEl.placeholder = 'Nhập nhu cầu tìm phòng (ví dụ: "Tìm phòng khép kín dưới 4 triệu gần đại học có máy giặt")...';
                    if (toneSelect) toneSelect.value = 'friendly';
                } else if (mode === 'tenant_incident') {
                    promptEl.placeholder = 'Mô tả sự cố kỹ thuật phòng bạn (ví dụ: "Chập điện bốc khói khét ở ổ cắm gần tủ lạnh")...';
                    if (toneSelect) toneSelect.value = 'urgent';
                }
            }
        }

        function clearAiHubPromptError() {
            const promptEl = document.getElementById('ai-hub-prompt');
            const errBox = document.getElementById('ai-hub-prompt-error');
            const errText = document.getElementById('ai-hub-prompt-error-text');
            if (promptEl) {
                promptEl.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/40', 'shake-error');
            }
            if (errBox) errBox.classList.add('hidden');
            if (errText) errText.textContent = '';
        }

        function showAiHubPromptError(message, shake = false) {
            const promptEl = document.getElementById('ai-hub-prompt');
            const errBox = document.getElementById('ai-hub-prompt-error');
            const errText = document.getElementById('ai-hub-prompt-error-text');
            if (promptEl) {
                promptEl.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/40');
                if (shake) {
                    promptEl.classList.remove('shake-error');
                    void promptEl.offsetWidth;
                    promptEl.classList.add('shake-error');
                }
                promptEl.focus();
            }
            if (errBox && errText) {
                errText.textContent = message;
                errBox.classList.remove('hidden');
            }
        }

        function showAiHubToast(title, subtitle, type = 'success') {
            let container = document.getElementById('realtime-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'realtime-toast-container';
                container.className = 'fixed top-20 right-8 z-50 flex flex-col gap-3 pointer-events-none';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            const isWarning = type === 'warning';
            const iconBg = isWarning ? 'bg-amber-500/20 text-amber-400' : 'bg-emerald-500/20 text-emerald-400';
            const icon = isWarning ? 'fa-triangle-exclamation' : 'fa-circle-check';
            const borderColor = isWarning ? 'border-amber-500/40' : 'border-emerald-500/40';
            const tagLabel = isWarning ? 'Cảnh báo Trợ lý AI (ERR_14_03)' : 'Thông báo AI (ERR_14_05)';

            toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl bg-slate-900/95 border ${borderColor} text-slate-100 shadow-2xl backdrop-blur-md text-xs font-semibold animate-slide-in transition-all duration-300`;
            toast.innerHTML = `
                <div class="w-9 h-9 rounded-xl ${iconBg} flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid ${icon}"></i>
                </div>
                <div class="min-w-[180px]">
                    <div class="text-[10px] ${isWarning ? 'text-amber-400' : 'text-emerald-400'} font-bold uppercase tracking-wider">${tagLabel}</div>
                    <div class="text-slate-100 font-bold mt-0.5">${title}</div>
                    ${subtitle ? `<div class="text-[11px] text-slate-400 mt-0.5 leading-snug">${subtitle}</div>` : ''}
                </div>
            `;

            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-8');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        function startAiHubCountdown(seconds = 30) {
            const btn = document.getElementById('btn-send-ai-hub');
            const badge = document.getElementById('ai-hub-countdown-badge');
            const secSpan = document.getElementById('ai-hub-countdown-seconds');

            if (aiHubCountdownTimer) clearInterval(aiHubCountdownTimer);
            if (btn) btn.disabled = true;
            if (badge) badge.classList.remove('hidden');

            let remaining = seconds;
            if (secSpan) secSpan.textContent = remaining;

            aiHubCountdownTimer = setInterval(() => {
                remaining--;
                if (secSpan) secSpan.textContent = remaining;
                if (remaining <= 0) {
                    clearInterval(aiHubCountdownTimer);
                    if (btn) btn.disabled = false;
                    if (badge) badge.classList.add('hidden');
                }
            }, 1000);
        }

        async function submitAiAssistantHub() {
            clearAiHubPromptError();
            const promptInput = document.getElementById('ai-hub-prompt');
            const toneSelect = document.getElementById('ai-hub-tone');
            const sendBtn = document.getElementById('btn-send-ai-hub');
            const responseBox = document.getElementById('ai-hub-response-box');
            const statusBadge = document.getElementById('ai-hub-status-badge');
            const actionsBar = document.getElementById('ai-hub-actions-bar');
            const btnCopy = document.getElementById('btn-copy-ai-post');
            const btnBooking = document.getElementById('btn-booking-viewing');
            const btnEmergency = document.getElementById('btn-view-emergency-ticket');

            const prompt = promptInput ? promptInput.value.trim() : '';

            // 1. Kiểm tra để trống (ERR_14_01)
            if (!prompt) {
                showAiHubPromptError('Vui lòng nhập câu hỏi hoặc yêu cầu cần trợ lý AI hỗ trợ.', true);
                return;
            }

            // 2. Kiểm tra dưới 5 ký tự (ERR_14_02)
            if (prompt.length < 5) {
                showAiHubPromptError('Nội dung yêu cầu quá ngắn (tối thiểu 5 ký tự) để AI có đủ ngữ cảnh xử lý.', false);
                return;
            }

            const originalBtnHtml = sendBtn ? sendBtn.innerHTML : '';
            if (sendBtn) {
                sendBtn.disabled = true;
                sendBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang xử lý...';
            }

            if (responseBox) {
                responseBox.innerHTML = '<div class="flex items-center gap-2 text-purple-400 py-3"><i class="fa-solid fa-circle-notch animate-spin text-sm"></i> <span>Trợ lý AI đang tư duy và phân tích dữ liệu...</span></div>';
            }
            if (actionsBar) actionsBar.classList.add('hidden');

            try {
                const res = await fetch("{{ route('smartroom.admin.ai.assistant_hub') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken()
                    },
                    body: JSON.stringify({
                        prompt: prompt,
                        agent_mode: currentAiHubMode,
                        tone: toneSelect ? toneSelect.value : 'professional',
                    })
                });

                // 4. Xử lý Vượt quá tần suất truy vấn (ERR_14_04: HTTP 429)
                if (res.status === 429) {
                    const errData = await res.json().catch(() => ({}));
                    showAiHubPromptError(errData.message || 'Bạn đã gửi quá nhiều yêu cầu trong thời gian ngắn (tối đa 20 req/phút). Vui lòng đợi 30 giây!', false);
                    startAiHubCountdown(errData.retry_after || 30);
                    if (responseBox) {
                        responseBox.innerHTML = `<span class="text-amber-400 font-semibold">${errData.message || 'Bạn đã gửi quá nhiều yêu cầu trong thời gian ngắn (tối đa 20 req/phút). Vui lòng đợi 30 giây!'}</span>`;
                    }
                    return;
                }

                // 1 & 2: Xử lý validation từ backend (422)
                if (res.status === 422) {
                    const errData = await res.json().catch(() => ({}));
                    showAiHubPromptError(errData.message || 'Dữ liệu yêu cầu không hợp lệ.', errData.error_code === 'ERR_14_01');
                    if (responseBox) {
                        responseBox.innerHTML = `<span class="text-rose-400">${errData.message || 'Dữ liệu không hợp lệ.'}</span>`;
                    }
                    return;
                }

                const data = await res.json();
                if (!res.ok || !data.success) {
                    if (responseBox) responseBox.innerHTML = `<span class="text-rose-400">${data.message || 'Không thể nhận phản hồi từ AI.'}</span>`;
                    return;
                }

                lastAiHubGeneratedContent = data.response || '';

                if (responseBox) {
                    responseBox.textContent = data.response || '';
                }

                if (statusBadge) {
                    statusBadge.classList.remove('hidden');
                    if (data.is_fallback) {
                        statusBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded border bg-amber-500/10 text-amber-400 border-amber-500/20';
                        statusBadge.textContent = 'Kho tri thức dự phòng (Fallback 6s)';
                        showAiHubToast('Kích hoạt kho tri thức dự phòng', data.warning_message || 'Kết nối máy chủ AI quá thời gian chờ (6s), tự động kích hoạt câu trả lời từ kho tri thức dự phòng.', 'warning');
                    } else {
                        statusBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded border bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                        statusBadge.textContent = 'Google Gemini AI';
                    }
                }

                // 6. Xử lý Sự cố khẩn cấp (ERR_14_06)
                if (data.is_emergency) {
                    openAiEmergencyModal(data.message || 'Sự cố kỹ thuật khẩn cấp đã được AI ghi nhận và chuyển ngay đến đội ngũ bảo trì tòa nhà!');
                    if (btnEmergency) btnEmergency.classList.remove('hidden');
                } else if (btnEmergency) {
                    btnEmergency.classList.add('hidden');
                }

                if (actionsBar) actionsBar.classList.remove('hidden');
                if (btnCopy) {
                    if (currentAiHubMode === 'landlord_advisor' || currentAiHubMode === 'landlord_qa' || data.quick_action === 'copy_content') {
                        btnCopy.classList.remove('hidden');
                    } else {
                        btnCopy.classList.add('hidden');
                    }
                }
                if (btnBooking) {
                    if (currentAiHubMode === 'room_finder' || data.quick_action === 'book_viewing') {
                        btnBooking.classList.remove('hidden');
                    } else {
                        btnBooking.classList.add('hidden');
                    }
                }

            } catch (err) {
                console.error('Lỗi gọi AI Assistant Hub:', err);
                if (responseBox) responseBox.innerHTML = '<span class="text-rose-400">Lỗi kết nối đến máy chủ AI. Vui lòng thử lại sau giây lát!</span>';
            } finally {
                if (sendBtn) {
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = originalBtnHtml;
                }
            }
        }

        // ERR_14_05: Sao chép nội dung bài viết marketing chuẩn SEO vào bộ nhớ tạm
        function copyAiHubContent() {
            if (!lastAiHubGeneratedContent) {
                const box = document.getElementById('ai-hub-response-box');
                lastAiHubGeneratedContent = box ? box.textContent : '';
            }

            if (!lastAiHubGeneratedContent) return;

            navigator.clipboard.writeText(lastAiHubGeneratedContent).then(() => {
                showAiHubToast(
                    'Đã sao chép nội dung bài viết marketing chuẩn SEO vào bộ nhớ tạm thành công!',
                    '',
                    'success'
                );
            }).catch(() => {
                const tempArea = document.createElement('textarea');
                tempArea.value = lastAiHubGeneratedContent;
                document.body.appendChild(tempArea);
                tempArea.select();
                document.execCommand('copy');
                document.body.removeChild(tempArea);

                showAiHubToast(
                    'Đã sao chép nội dung bài viết marketing chuẩn SEO vào bộ nhớ tạm thành công!',
                    '',
                    'success'
                );
            });
        }

        function openAiEmergencyModal(message) {
            const modal = document.getElementById('modal-ai-emergency-ticket');
            const msgEl = document.getElementById('modal-ai-emergency-message');
            if (modal) {
                if (msgEl && message) msgEl.textContent = message;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeAiEmergencyModal() {
            const modal = document.getElementById('modal-ai-emergency-ticket');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        function openBookingFromAi() {
            window.location.href = "{{ route('smartroom.admin.contact_requests') }}";
        }

        function openEmergencyTicketModal() {
            window.location.href = "{{ route('smartroom.admin.tickets.index') }}";
        }

        document.addEventListener('DOMContentLoaded', function () {
            const promptInput = document.getElementById('ai-hub-prompt');
            const charCount = document.getElementById('ai-hub-char-count');

            if (promptInput) {
                promptInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        submitAiAssistantHub();
                    }
                });

                promptInput.addEventListener('input', function () {
                    clearAiHubPromptError();
                    if (charCount) {
                        charCount.textContent = `${this.value.length}/2000 ký tự`;
                    }
                });
            }
        });

        function generateContractTermsWithAi(btn) {
            const roomId = document.getElementById('contract-room-id')?.value;
            const residentId = document.getElementById('contract-resident-id')?.value;
            const startDate = document.getElementById('contract-start-date')?.value;
            const endDate = document.getElementById('contract-end-date')?.value;
            const deposit = document.getElementById('contract-deposit')?.value;
            const terms = document.getElementById('contract-terms');

            if (!roomId || !residentId || !startDate || !endDate || !deposit) {
                alert('Vui lòng nhập đủ phòng, cư dân, thời hạn và tiền cọc trước khi dùng AI.');
                return;
            }

            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang soạn...';

            fetch("{{ route('smartroom.admin.ai.contract_terms') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                body: JSON.stringify({
                    room_id: roomId,
                    resident_id: residentId,
                    start_date: startDate,
                    end_date: endDate,
                    deposit: Number(deposit)
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = original;

                if (!data.success) {
                    alert('Không thể tạo điều khoản bằng AI.');
                    return;
                }

                terms.value = data.terms.terms || terms.value;
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = original;
                alert('Không thể kết nối AI để tạo điều khoản.');
                console.error(err);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const aiQuestionInput = document.getElementById('ai-assistant-question');
            if (aiQuestionInput) {
                aiQuestionInput.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        const button = aiQuestionInput.parentElement.querySelector('button');
                        if (button) {
                            askAiAssistant(button);
                        }
                    }
                });
            }
        });
    </script>



    <script>
        function openKycTermsModal() {
            const modal = document.getElementById('kyc-terms-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeKycTermsModal() {
            const modal = document.getElementById('kyc-terms-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
        
        function openRenewContractModal(contract, resident, room) {
            const modal = document.getElementById('renew-contract-modal');
            if (!modal) return;

            const form = document.getElementById('renew-contract-form');
            form.action = `/smartroom/admin/contract/${contract.id}/renew`;

            document.getElementById('renew-room-display').value = 'Phòng ' + (room ? room.room_number : 'N/A');
            document.getElementById('renew-resident-display').value = resident ? resident.name : 'N/A';

            document.getElementById('renew-room-id').value = contract.room_id || '';
            document.getElementById('renew-resident-id').value = contract.resident_id || '';
            
            if (resident) {
                document.getElementById('renew-lessee-name').value = resident.name || '';
                document.getElementById('renew-lessee-phone').value = resident.phone || '';
                document.getElementById('renew-lessee-id-number').value = resident.cccd || '';
                document.getElementById('renew-lessee-permanent-address').value = resident.hometown || '';
                document.getElementById('renew-lessee-current-address').value = resident.hometown || '';
            }

            document.getElementById('renew-lessor-name').value = '{{ $tenant->name ?? "" }}';
            document.getElementById('renew-lessor-phone').value = '{{ $tenant->phone ?? "" }}';
            document.getElementById('renew-lessor-id-number').value = '';
            document.getElementById('renew-lessor-address').value = '{{ $tenant->address ?? "" }}';
            document.getElementById('renew-rental-address').value = (room && room.building) ? room.building.address : 'SmartRoom Apartment';
            document.getElementById('renew-rental-area-description').value = room ? `Phòng trọ số ${room.room_number}` : '';
            document.getElementById('renew-equipment-list').value = room ? (room.equipments || '') : '';
            document.getElementById('renew-rental-purpose').value = 'Để ở';
            document.getElementById('renew-occupant-count').value = '1';
            document.getElementById('renew-first-payment-date').value = '';
            document.getElementById('renew-payment-method').value = 'Chuyển khoản / Tiền mặt';

            const infoBox = document.getElementById('renew-request-info-box');
            const infoText = document.getElementById('renew-request-info-text');
            if (contract.renewal_status === 'requested') {
                infoBox.classList.remove('hidden');
                infoText.textContent = `Cư dân đã gửi yêu cầu gia hạn thêm ${contract.renewal_months} tháng.\nGhi chú của cư dân: "${contract.renewal_note || 'Không có ghi chú'}"`;
            } else {
                infoBox.classList.add('hidden');
            }

            document.getElementById('renew-rent-price').value = room ? room.price : (contract.rent_price || 0);
            document.getElementById('renew-deposit').value = contract.deposit || (room ? room.price : 0);
            document.getElementById('renew-payment-cycle-months').value = contract.payment_cycle_months || '3';

            if (contract.end_date) {
                const oldEndDate = new Date(contract.end_date);
                const nextDay = new Date(oldEndDate);
                nextDay.setDate(oldEndDate.getDate() + 1);
                
                const yyyy = nextDay.getFullYear();
                const mm = String(nextDay.getMonth() + 1).padStart(2, '0');
                const dd = String(nextDay.getDate()).padStart(2, '0');
                document.getElementById('renew-start-date').value = `${yyyy}-${mm}-${dd}`;

                const durationMonths = contract.renewal_status === 'requested' ? parseInt(contract.renewal_months) : 6;
                const newEndDate = new Date(oldEndDate);
                newEndDate.setMonth(oldEndDate.getMonth() + durationMonths);
                
                const ey = newEndDate.getFullYear();
                const em = String(newEndDate.getMonth() + 1).padStart(2, '0');
                const ed = String(newEndDate.getDate()).padStart(2, '0');
                document.getElementById('renew-end-date').value = `${ey}-${em}-${ed}`;
            }

            const declineBtn = document.getElementById('renew-decline-btn');
            if (contract.renewal_status === 'requested') {
                declineBtn.classList.remove('hidden');
                declineBtn.onclick = function() {
                    if (confirm('Bạn có chắc chắn muốn từ chối yêu cầu gia hạn này không?')) {
                        const declineForm = document.createElement('form');
                        declineForm.method = 'POST';
                        declineForm.action = `/smartroom/admin/contract/${contract.id}/decline-renewal`;
                        
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = '{{ csrf_token() }}';
                        declineForm.appendChild(csrfInput);
                        
                        document.body.appendChild(declineForm);
                        declineForm.submit();
                    }
                };
            } else {
                declineBtn.classList.add('hidden');
            }

            // Reset radio
            document.querySelector('input[name="renewal_mode"][value="extend"]').checked = true;
            toggleRenewalModeFields('extend');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeRenewContractModal() {
            const modal = document.getElementById('renew-contract-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        function toggleRenewalModeFields(mode) {
            const startDateGroup = document.getElementById('renew-start-date-group');
            const endDateLabel = document.getElementById('renew-end-date-label');
            
            if (mode === 'extend') {
                startDateGroup.classList.add('hidden');
                endDateLabel.textContent = 'Ngày kết thúc gia hạn mới';
            } else {
                startDateGroup.classList.remove('hidden');
                endDateLabel.textContent = 'Ngày kết thúc hợp đồng mới';
            }
        }

        // ==========================================
        // REALTIME ROOM MATRIX & HOUSEKEEPING
        // ==========================================
        let lastEventTimestamp = Math.floor(Date.now() / 1000);

        function applyRoomCardUpdate(roomData) {
            const card = document.getElementById('room-card-' + roomData.id);
            if (!card) return;

            // 1. Cập nhật thuộc tính trong dataset
            card.setAttribute('data-room-status', roomData.status);
            if (roomData.room_number) {
                card.setAttribute('data-room-number', roomData.room_number);
            }

            // 2. Cập nhật thông tin cư dân trên thẻ & dataset
            const resH4 = card.querySelector('.room-resident');
            if (roomData.has_resident && roomData.resident_name && (roomData.status === 'occupied' || roomData.status === 'overdue')) {
                card.setAttribute('data-resident-name', roomData.resident_name);
                if (roomData.resident_phone) card.setAttribute('data-resident-phone', roomData.resident_phone);
                if (resH4) {
                    resH4.textContent = 'Cư dân: ' + roomData.resident_name;
                    resH4.className = 'room-resident text-xs font-bold text-slate-400 truncate mb-1';
                }
            } else if (['empty', 'cleaning', 'maintenance'].includes(roomData.status)) {
                card.setAttribute('data-resident-name', '');
                card.setAttribute('data-resident-phone', '');
                if (resH4) {
                    resH4.textContent = 'Chưa có cư dân';
                    resH4.className = 'room-resident text-xs font-bold text-slate-500 italic mb-1';
                }
            }

            // 3. Cập nhật class viền/nền CSS
            card.className = card.className.replace(/room-(empty|occupied|overdue|cleaning|maintenance)/g, '');
            card.className = card.className.replace(/border-(emerald|red|amber|orange|slate)-500\/20/g, '');
            
            const newStatusClass = roomData.status_class || ('room-' + roomData.status);
            card.classList.add(...newStatusClass.split(' ').filter(c => c));

            // 4. Cập nhật Badge trên card
            const badge = card.querySelector('.room-badge');
            if (badge) {
                badge.textContent = roomData.status_label;
                badge.className = "room-badge px-2 py-0.5 rounded text-[10px] font-extrabold border " + (roomData.badge_class || 'border-slate-700 text-slate-400');
            }

            // 5. Ẩn/hiện card ngay theo tab bộ lọc hiện tại (nếu đang chọn tab)
            applyCurrentFilterToCard(card);

            // 6. Cập nhật số đếm tất cả các nút filter
            updateRoomFilterCounts();

            // 7. Đồng bộ trực tiếp nếu Modal của phòng này đang mở
            if (currentActiveRoomId == roomData.id) {
                currentActiveRoomStatus = roomData.status;
                const modalBadge = document.getElementById('modal-room-status-badge');
                if (modalBadge) {
                    modalBadge.textContent = roomData.status_label;
                    modalBadge.className = "text-xs px-2.5 py-1 rounded-md font-bold uppercase border " + (roomData.badge_class || 'bg-slate-500/10 text-slate-400 border-slate-500/20');
                }
                updateQuickStatusButtons(roomData.status);

                const resDetails = document.getElementById('modal-resident-details');
                const billDetails = document.getElementById('modal-billing-details');
                const actionBtn = document.getElementById('modal-btn-action');
                const payBtn = document.getElementById('modal-btn-pay');
                const printBtn = document.getElementById('modal-btn-print');
                const qrBtn = document.getElementById('modal-btn-qr');

                if (['empty', 'cleaning', 'maintenance'].includes(roomData.status)) {
                    if (resDetails) resDetails.classList.add('hidden');
                    if (billDetails) billDetails.classList.add('hidden');
                    if (actionBtn) actionBtn.classList.add('hidden');
                    if (payBtn) payBtn.classList.add('hidden');
                    if (printBtn) printBtn.classList.add('hidden');
                    if (qrBtn) qrBtn.classList.add('hidden');
                    const modalBox = document.getElementById('room-detail-modal-box');
                    const contentGrid = document.getElementById('modal-content-grid');
                    const colBillingActions = document.getElementById('modal-col-billing-actions');
                    const col1CloseBtn = document.getElementById('modal-col1-close-btn');
                    if (modalBox) {
                        modalBox.classList.remove('max-w-2xl', 'lg:max-w-3xl');
                        modalBox.classList.add('max-w-md');
                    }
                    if (contentGrid) {
                        contentGrid.classList.remove('md:grid-cols-2');
                        contentGrid.classList.add('grid-cols-1');
                    }
                    if (colBillingActions) colBillingActions.classList.add('hidden');
                    if (col1CloseBtn) col1CloseBtn.classList.remove('hidden');
                } else if (roomData.has_resident) {
                    if (resDetails) resDetails.classList.remove('hidden');
                    const resNameEl = document.getElementById('modal-resident-name');
                    if (resNameEl && roomData.resident_name) {
                        resNameEl.textContent = roomData.resident_name;
                    }
                    const resPhoneEl = document.getElementById('modal-resident-phone');
                    if (resPhoneEl && roomData.resident_phone) {
                        resPhoneEl.textContent = roomData.resident_phone;
                    }
                    const modalBox = document.getElementById('room-detail-modal-box');
                    const contentGrid = document.getElementById('modal-content-grid');
                    const colBillingActions = document.getElementById('modal-col-billing-actions');
                    const col1CloseBtn = document.getElementById('modal-col1-close-btn');
                    if (modalBox) {
                        modalBox.classList.remove('max-w-md');
                        modalBox.classList.add('max-w-2xl', 'lg:max-w-3xl');
                    }
                    if (contentGrid) {
                        contentGrid.classList.remove('grid-cols-1');
                        contentGrid.classList.add('grid-cols-1', 'md:grid-cols-2');
                    }
                    if (colBillingActions) colBillingActions.classList.remove('hidden');
                    if (col1CloseBtn) col1CloseBtn.classList.add('hidden');
                }
            }

            // 8. Hiệu ứng Pulse Flash Realtime
            card.classList.add('ring-4', 'ring-emerald-400', 'scale-[1.03]');
            setTimeout(() => {
                card.classList.remove('ring-4', 'ring-emerald-400', 'scale-[1.03]');
            }, 1800);
        }

        function showRealtimeToast(title, subtitle = '', type = 'room') {
            let container = document.getElementById('realtime-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'realtime-toast-container';
                container.className = 'fixed top-20 right-8 z-50 flex flex-col gap-3 pointer-events-none';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            const isTicket = type === 'ticket';
            const iconBg = isTicket ? 'bg-rose-500/20 text-rose-400' : 'bg-emerald-500/20 text-emerald-400';
            const icon = isTicket ? 'fa-triangle-exclamation' : 'fa-bolt';
            const borderColor = isTicket ? 'border-rose-500/40' : 'border-emerald-500/40';
            const tagLabel = isTicket ? 'Sự cố cư dân (Realtime Echo)' : 'Sơ đồ phòng (Realtime Reverb)';

            toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl bg-slate-900/95 border ${borderColor} text-slate-100 shadow-2xl shadow-indigo-500/20 backdrop-blur-md text-xs font-semibold animate-slide-in transition-all duration-300`;
            toast.innerHTML = `
                <div class="w-9 h-9 rounded-xl ${iconBg} flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid ${icon} animate-bounce"></i>
                </div>
                <div class="min-w-[180px]">
                    <div class="text-[10px] ${isTicket ? 'text-rose-400' : 'text-emerald-400'} font-bold uppercase tracking-wider">${tagLabel}</div>
                    <div class="text-slate-100 font-bold mt-0.5">${title}</div>
                    ${subtitle ? `<div class="text-[11px] text-slate-400 mt-0.5 leading-snug">${subtitle}</div>` : ''}
                </div>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-8');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        @php
            $activeTenantId = $tenant->id ?? (Auth::user()?->tenant_id ?? ($rooms->first()?->tenant_id ?? 1));
        @endphp
        const tenantId = {{ $activeTenantId }};
        let lastProcessedEventKey = '';

        function handleIncomingRoomUpdate(data) {
            if (!data || !data.id) return;
            const eventKey = `${data.id}_${data.status}_${data.updated_at || ''}`;
            if (eventKey === lastProcessedEventKey) {
                return; // Tránh xử lý trùng lặp từ 2 channel
            }
            lastProcessedEventKey = eventKey;
            lastEventTimestamp = Math.floor(Date.now() / 1000);
            applyRoomCardUpdate(data);
            showRealtimeToast(
                `P.${data.room_number}: ${data.status_label}`,
                'Đồng bộ trạng thái phòng tức thời qua WebSocket Reverb!',
                'room'
            );
        }

        let adminMaxTicketId = {{ (int) ($tickets->max('id') ?? 0) }};

        // Hàm phát chuông thông báo Ding-dong qua Web Audio API
        function playTicketAlertSound() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                const now = ctx.currentTime;

                // Nốt 1 (D5 - 587Hz)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, now);
                gain1.gain.setValueAtTime(0.12, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.3);

                // Nốt 2 (A5 - 880Hz) âm thanh trong trẻo, vui tai
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880, now + 0.16);
                gain2.gain.setValueAtTime(0.15, now + 0.16);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.16);
                osc2.stop(now + 0.6);
            } catch (e) {
                // Trình duyệt có thể chặn âm thanh nếu chưa có tương tác
            }
        }

        // Cập nhật số liệu thống kê sự cố trên toàn bộ Dashboard & Sidebar
        function updateTicketStatsCounters(stats) {
            if (!stats) return;
            const totalStatEl = document.getElementById('ticket-stat-total');
            if (totalStatEl) totalStatEl.textContent = stats.total ?? totalStatEl.textContent;

            const pendingStatEl = document.getElementById('ticket-stat-pending');
            if (pendingStatEl) pendingStatEl.textContent = stats.pending ?? pendingStatEl.textContent;

            const processingStatEl = document.getElementById('ticket-stat-processing');
            if (processingStatEl) processingStatEl.textContent = stats.processing ?? processingStatEl.textContent;

            const resolvedStatEl = document.getElementById('ticket-stat-resolved');
            if (resolvedStatEl) resolvedStatEl.textContent = stats.resolved ?? resolvedStatEl.textContent;

            const countBadge = document.getElementById('ticket-count-badge');
            if (countBadge && stats.total !== undefined) {
                countBadge.textContent = `${stats.total} sự cố`;
            }

            const sidebarTicketBadge = document.getElementById('sidebar-ticket-badge');
            if (sidebarTicketBadge && stats.pending !== undefined) {
                sidebarTicketBadge.textContent = stats.pending;
                if (parseInt(stats.pending) > 0) {
                    sidebarTicketBadge.classList.remove('hidden');
                } else {
                    sidebarTicketBadge.classList.add('hidden');
                }
            }
        }

        let isReloadingPageForTicket = false;

        // Xử lý khi có sự cố mới bay vào (Dùng chung cho cả WebSocket & Polling)
        function handleIncomingTicket(data, stats = null) {
            if (!data || !data.id) return;

            // Nếu sự cố này đã có trên bảng và không phải mới hơn ID hiện tại
            if (data.id <= adminMaxTicketId && document.querySelector(`.ticket-row[data-ticket-id="${data.id}"]`)) {
                if (stats) updateTicketStatsCounters(stats);
                return;
            }

            if (isReloadingPageForTicket) return;
            isReloadingPageForTicket = true;

            if (data.id > adminMaxTicketId) {
                adminMaxTicketId = data.id;
            }

            // 1. Âm thanh chuông báo
            playTicketAlertSound();

            // 2. Toast thông báo thời gian thực
            const locationText = data.specific_location ? ` • ${data.specific_location}` : '';
            showRealtimeToast(
                `🚨 Sự cố mới: P.${data.room_number || 'N/A'}${locationText}`,
                `Khách hàng vừa báo cáo sự cố! Đang tự động tải lại trang...`,
                'ticket'
            );

            // 3. Hiệu ứng nhấp nháy chuông header
            const bellBadge = document.querySelector('.fa-bell + span');
            if (bellBadge) bellBadge.classList.add('animate-ping');

            // 4. Tự động load trang lại sau 500ms về tab ticket-section
            setTimeout(() => {
                window.location.href = "{{ route('smartroom.admin') }}?tab=ticket-section";
            }, 500);
        }

        // Thăm dò kiểm tra sự cố mới định kỳ (Smart Polling 2.5s)
        let isPollingTickets = false;
        async function pollNewAdminTickets() {
            if (isPollingTickets) return;
            isPollingTickets = true;
            try {
                const res = await fetch("{{ route('smartroom.admin.tickets.poll') }}?last_id=" + adminMaxTicketId, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    hideSseWarning();
                    const json = await res.json();
                    if (json && json.success) {
                        if (json.has_new && Array.isArray(json.tickets)) {
                            json.tickets.forEach(ticket => {
                                handleIncomingTicket(ticket, json.stats);
                            });
                        } else if (json.stats) {
                            updateTicketStatsCounters(json.stats);
                        }
                        if (json.latest_id && json.latest_id > adminMaxTicketId) {
                            adminMaxTicketId = json.latest_id;
                        }
                    }
                }
            } catch (e) {
                // Im lặng bỏ qua lỗi mạng tạm thời
            } finally {
                isPollingTickets = false;
            }
        }

        let matrixEventSource = null;
        let sseReconnectTimer = null;
        let sseConsecutiveErrors = 0;
        let sseErrorCount = 0;

        function showSseWarning() {
            const badge = document.getElementById('sse-reconnect-badge');
            if (badge) {
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            }
        }

        function hideSseWarning() {
            if (sseReconnectTimer) {
                clearTimeout(sseReconnectTimer);
                sseReconnectTimer = null;
            }
            sseErrorCount = 0;
            sseConsecutiveErrors = 0;
            const badge = document.getElementById('sse-reconnect-badge');
            if (badge) {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        }

        function connectMatrixSSE() {
            if (typeof EventSource === 'undefined') return;
            try {
                if (matrixEventSource) {
                    matrixEventSource.close();
                }
                matrixEventSource = new EventSource("{{ route('admin.rooms.matrix.stream') }}");
                matrixEventSource.onopen = function () {
                    sseErrorCount = 0;
                    sseConsecutiveErrors = 0;
                    hideSseWarning();
                };
                matrixEventSource.addEventListener('ping', function () {
                    sseErrorCount = 0;
                    sseConsecutiveErrors = 0;
                    hideSseWarning();
                });
                matrixEventSource.addEventListener('room-updated', function (e) {
                    sseErrorCount = 0;
                    sseConsecutiveErrors = 0;
                    hideSseWarning();
                    try {
                        const roomData = JSON.parse(e.data);
                        if (roomData && roomData.id) {
                            applyRoomCardUpdate(roomData);
                        }
                    } catch (err) {
                        console.error('SSE room-updated parse error:', err);
                    }
                });
                matrixEventSource.onerror = function () {
                    sseErrorCount++;
                    // Nếu mất mạng hoàn toàn -> lập tức hiện cảnh báo
                    if (!navigator.onLine) {
                        showSseWarning();
                        return;
                    }

                    // Nếu vẫn online: Server-Sent Events khi server đóng chu kỳ stream (Connection: close)
                    // sẽ kích hoạt onerror rồi tự động chuyển sang CONNECTING để kết nối lại sau chu kỳ retry.
                    // Đây là cơ chế stream hoạt động bình thường, không phải lỗi mạng.
                    sseConsecutiveErrors++;

                    if (matrixEventSource.readyState === EventSource.CLOSED) {
                        if (!sseReconnectTimer) {
                            sseReconnectTimer = setTimeout(() => {
                                sseReconnectTimer = null;
                                connectMatrixSSE();
                            }, 5000);
                        }
                    } else if (matrixEventSource.readyState === EventSource.CONNECTING) {
                        if (!sseReconnectTimer && (sseConsecutiveErrors >= 3 || sseErrorCount >= 3)) {
                            sseReconnectTimer = setTimeout(() => {
                                sseReconnectTimer = null;
                                if (matrixEventSource && matrixEventSource.readyState !== EventSource.OPEN && !navigator.onLine) {
                                    showSseWarning();
                                }
                            }, 20000);
                        }
                    }
                };
            } catch (err) {
                if (!navigator.onLine) {
                    showSseWarning();
                }
            }
        }

        function initRoomMatrixRealtime() {
            // 0. Khởi tạo luồng SSE thời gian thực (FEAT_13)
            connectMatrixSSE();
            window.addEventListener('offline', showSseWarning);
            window.addEventListener('online', () => {
                connectMatrixSSE();
                hideSseWarning();
            });

            // 1. Kết nối Laravel Echo + Reverb (WebSocket)
            if (window.Echo) {
                try {
                    // Lắng nghe trạng thái phòng
                    window.Echo.channel(`tenant.${tenantId}.room-matrix`)
                        .listen('.room.status.updated', (data) => handleIncomingRoomUpdate(data));

                    window.Echo.channel('room-matrix')
                        .listen('.room.status.updated', (data) => handleIncomingRoomUpdate(data));

                    // Lắng nghe sự cố tức thời từ cư dân qua WebSocket Reverb
                    window.Echo.channel(`tenant.${tenantId}.dashboard`)
                        .listen('.ticket.created', (data) => handleIncomingTicket(data));
                } catch (err) {
                    console.warn('Echo Reverb subscription error: ', err);
                }
            }

            // 2. Thăm dò phụ (Smart Polling Fallback) mỗi 2.5s để đảm bảo 100% sự cố bay vào ngay lập tức
            setInterval(pollNewAdminTickets, 2500);

            // 3. Thăm dò phòng matrix mỗi 25s
            setInterval(async () => {
                if (document.hidden) return;
                try {
                    const res = await fetch("{{ route('admin.rooms.matrix.poll') }}?since=" + lastEventTimestamp);
                    if (res.ok) {
                        hideSseWarning();
                        const json = await res.json();
                        if (json.has_update && json.event) {
                            lastEventTimestamp = json.event.updated_at;
                            applyRoomCardUpdate(json.event);
                        }
                    } else {
                        if (!navigator.onLine) {
                            showSseWarning();
                        }
                    }
                } catch (e) {
                    if (!navigator.onLine) {
                        showSseWarning();
                    }
                }
            }, 25000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initRoomMatrixRealtime, 250);
        });

        // Quản lý dropdown thông báo cho chủ trọ
        function toggleAdminNotifications(forceState = null) {
            const dropdown = document.getElementById('admin-notification-dropdown');
            const btn = document.getElementById('admin-notification-btn');
            if (!dropdown || !btn) return;

            const isCurrentlyOpen = !dropdown.classList.contains('hidden');
            const shouldOpen = forceState !== null ? forceState : !isCurrentlyOpen;

            if (shouldOpen) {
                dropdown.classList.remove('hidden');
                btn.setAttribute('aria-expanded', 'true');
            } else {
                dropdown.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            }
        }

        function markAllNotificationsAsRead() {
            const badge = document.getElementById('admin-notification-badge');
            if (badge) {
                badge.style.display = 'none';
            }
            if (typeof showRealtimeToast === 'function') {
                showRealtimeToast('Đã đánh dấu đã đọc', 'Các thông báo hiện tại đã được đánh dấu hoàn tất.', 'ticket');
            }
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('admin-notification-container');
            if (container && !container.contains(e.target)) {
                toggleAdminNotifications(false);
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRoomDetail();
                toggleAdminNotifications(false);
            }
        });
    </script>

