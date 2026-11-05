<!-- SweetAlert2 Core Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- SmartRoom Glassmorphism Alert & Confirmation Box Styling -->
<style>
    .swal2-container {
        z-index: 999999 !important;
    }

    .smart-swal-popup {
        background: #0d121f !important;
        background-color: #0d121f !important;
        border: 1px solid rgba(51, 65, 85, 0.8) !important;
        border-radius: 1.5rem !important; /* rounded-3xl */
        color: #f1f5f9 !important;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.8), 0 0 0 1px rgba(99, 102, 241, 0.2) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        padding: 2rem 1.75rem !important;
        max-width: 28rem !important;
        width: 90% !important;
    }
    
    html.theme-light .smart-swal-popup,
    body.theme-light .smart-swal-popup {
        background: #ffffff !important;
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        color: #0f172a !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2) !important;
    }
    
    .smart-swal-popup .swal2-title {
        color: #f8fafc !important;
        font-size: 1.25rem !important;
        font-weight: 800 !important;
        letter-spacing: -0.025em !important;
        padding: 0 !important;
        margin-top: 1rem !important;
        line-height: 1.35 !important;
    }
    
    html.theme-light .smart-swal-popup .swal2-title,
    body.theme-light .smart-swal-popup .swal2-title {
        color: #0f172a !important;
    }
    
    .smart-swal-popup .swal2-html-container {
        color: #94a3b8 !important;
        font-size: 0.875rem !important;
        line-height: 1.6 !important;
        margin-top: 0.75rem !important;
        padding: 0 0.5rem !important;
        white-space: pre-line !important;
    }
    
    html.theme-light .smart-swal-popup .swal2-html-container,
    body.theme-light .smart-swal-popup .swal2-html-container {
        color: #475569 !important;
    }
    
    .smart-swal-popup .swal2-actions {
        display: flex !important;
        flex-direction: row-reverse !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 0.75rem !important;
        margin-top: 1.75rem !important;
        width: 100% !important;
    }
    
    .smart-swal-confirm-btn {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 0.875rem !important;
        padding: 0.7rem 1.5rem !important;
        font-size: 0.8125rem !important;
        font-weight: 700 !important;
        box-shadow: 0 8px 20px -4px rgba(79, 70, 229, 0.45) !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        outline: none !important;
    }
    
    .smart-swal-confirm-btn:hover {
        transform: translateY(-1.5px) !important;
        box-shadow: 0 12px 25px -4px rgba(79, 70, 229, 0.55) !important;
        opacity: 0.95 !important;
    }
    
    .smart-swal-cancel-btn {
        background: #0f172a !important;
        color: #94a3b8 !important;
        border: 1px solid #1e293b !important;
        border-radius: 0.875rem !important;
        padding: 0.7rem 1.35rem !important;
        font-size: 0.8125rem !important;
        font-weight: 700 !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        cursor: pointer !important;
        outline: none !important;
    }
    
    .smart-swal-cancel-btn:hover {
        background: #1e293b !important;
        color: #f1f5f9 !important;
        border-color: #334155 !important;
    }
    
    html.theme-light .smart-swal-cancel-btn,
    body.theme-light .smart-swal-cancel-btn {
        background: #f1f5f9 !important;
        color: #64748b !important;
        border: 1px solid #cbd5e1 !important;
    }
    
    html.theme-light .smart-swal-cancel-btn:hover,
    body.theme-light .smart-swal-cancel-btn:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
    }
    
    /* Icon styling adjustments */
    .smart-swal-popup .swal2-icon {
        margin: 0.5rem auto 0 auto !important;
        transform: scale(0.9) !important;
        border-width: 3px !important;
    }
    
    .smart-swal-popup .swal2-icon.swal2-warning {
        border-color: #f59e0b !important;
        color: #f59e0b !important;
    }
    
    .smart-swal-popup .swal2-icon.swal2-error {
        border-color: #f43f5e !important;
        color: #f43f5e !important;
    }
    
    .smart-swal-popup .swal2-icon.swal2-success {
        border-color: #10b981 !important;
        color: #10b981 !important;
    }
    
    .smart-swal-popup .swal2-icon.swal2-question {
        border-color: #6366f1 !important;
        color: #6366f1 !important;
    }
    
    .smart-swal-popup .swal2-icon.swal2-info {
        border-color: #06b6d4 !important;
        color: #06b6d4 !important;
    }
    
    .swal2-backdrop-show {
        background: rgba(4, 6, 11, 0.8) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
    }

    html.theme-light .swal2-backdrop-show,
    body.theme-light .swal2-backdrop-show {
        background: rgba(15, 23, 42, 0.45) !important;
    }
</style>

<script>
    const _nativeAlert = window.alert ? window.alert.bind(window) : function() {};
    const _nativeConfirm = window.confirm ? window.confirm.bind(window) : function() { return true; };

    /**
     * Hộp thoại xác nhận thao tác chuẩn giao diện SmartRoom
     * @param {Object|string} titleOrOptions
     * @param {string} text
     * @param {string} icon ('question'|'warning'|'info'|'success'|'error')
     * @returns {Promise<boolean>}
     */
    window.smartConfirm = function(titleOrOptions, text = '', icon = 'question') {
        let opts = {};
        if (typeof titleOrOptions === 'object') {
            opts = titleOrOptions;
        } else {
            opts = { title: titleOrOptions, text: text, icon: icon };
        }

        if (typeof Swal === 'undefined') {
            return Promise.resolve(_nativeConfirm(opts.text || opts.title || ''));
        }

        return Swal.fire({
            title: opts.title || 'Xác Nhận Thao Tác',
            text: opts.text || '',
            html: opts.html,
            icon: opts.icon || 'question',
            showCancelButton: true,
            confirmButtonText: opts.confirmButtonText || '<i class="fa-solid fa-check mr-1.5"></i> Xác Nhận',
            cancelButtonText: opts.cancelButtonText || 'Hủy Bỏ',
            focusCancel: true,
            customClass: {
                popup: 'smart-swal-popup',
                confirmButton: opts.confirmButtonClass ? `smart-swal-confirm-btn ${opts.confirmButtonClass}` : 'smart-swal-confirm-btn',
                cancelButton: 'smart-swal-cancel-btn'
            }
        }).then(res => res.isConfirmed);
    };

    /**
     * Hộp thoại thông báo box chuẩn giao diện SmartRoom
     * @param {Object|string} titleOrMessage
     * @param {string} text
     * @param {string} icon
     * @returns {Promise<any>}
     */
    window.smartAlert = function(titleOrMessage, text = '', icon = '') {
        let opts = {};
        if (typeof titleOrMessage === 'object') {
            opts = titleOrMessage;
        } else if (text !== '') {
            opts = { title: titleOrMessage, text: text, icon: icon || 'info' };
        } else {
            const raw = String(titleOrMessage);
            const lower = raw.toLowerCase();
            let detectedIcon = 'info';
            let detectedTitle = 'Thông Báo';
            let detectedText = raw;

            if (lower.includes('thành công') || lower.includes('tuyệt vời') || lower.includes('đã lưu') || lower.includes('đã chốt') || lower.includes('hoàn tất') || lower.includes('đã sao chép') || lower.includes('đã thêm')) {
                detectedIcon = 'success';
                detectedTitle = 'Thành Công';
            } else if (lower.includes('lỗi') || lower.includes('thất bại') || lower.includes('không thể') || lower.includes('sai') || lower.includes('bị chặn') || lower.includes('can thiệp')) {
                detectedIcon = 'error';
                detectedTitle = 'Đã Xảy Ra Lỗi';
            } else if (lower.includes('cảnh báo') || lower.includes('vui lòng') || lower.includes('chưa') || lower.includes('yêu cầu') || lower.includes('khóa')) {
                detectedIcon = 'warning';
                detectedTitle = 'Lưu Ý';
            }

            // Tách tiêu đề nếu dòng đầu tiên ngắn và chứa dấu cảm thán hoặc emoji
            if (raw.includes('\n')) {
                const parts = raw.split('\n');
                const firstLine = parts[0].trim();
                if (firstLine.length < 60 && (firstLine.includes('!') || firstLine.includes(':') || firstLine.startsWith('⚡') || firstLine.startsWith('🚀') || firstLine.startsWith('✨') || firstLine.startsWith('🔔'))) {
                    detectedTitle = firstLine;
                    detectedText = parts.slice(1).join('\n').trim();
                }
            }

            opts = { title: detectedTitle, text: detectedText, icon: detectedIcon };
        }

        if (typeof Swal === 'undefined') {
            return Promise.resolve(_nativeAlert(opts.text || opts.title || ''));
        }

        return Swal.fire({
            title: opts.title,
            text: opts.text,
            html: opts.html,
            icon: opts.icon || 'info',
            confirmButtonText: opts.confirmButtonText || '<i class="fa-solid fa-check mr-1.5"></i> Đã Hiểu',
            customClass: {
                popup: 'smart-swal-popup',
                confirmButton: 'smart-swal-confirm-btn'
            }
        });
    };

    // Override toàn bộ window.alert mặc định của trình duyệt để biến mọi alert thành box thông báo hiện đại
    window.alert = function(msg) {
        return window.smartAlert(msg);
    };

    // Helper xử lý nút xóa / form xóa có modal xác nhận đẹp mắt
    window.confirmDelete = function(event, formOrElement, message = 'Bạn có chắc chắn muốn xóa không?', title = 'Xác Nhận Xóa') {
        if (event) event.preventDefault();
        const form = formOrElement?.tagName === 'FORM' ? formOrElement : (formOrElement?.closest('form') || event?.target?.closest('form'));
        window.smartConfirm({
            title: title,
            text: message,
            icon: 'warning',
            confirmButtonText: '<i class="fa-solid fa-trash-can mr-1.5"></i> Xóa ngay',
            confirmButtonClass: '!bg-rose-600 hover:!bg-rose-500 !shadow-rose-600/30'
        }).then(confirmed => {
            if (confirmed && form) {
                form.submit();
            }
        });
        return false;
    };

    // Helper xử lý hành động xác nhận form tổng quát
    window.confirmAction = function(event, formOrElement, message, title = 'Xác Nhận Thao Tác', icon = 'question', confirmText = '<i class="fa-solid fa-check mr-1.5"></i> Xác Nhận', confirmClass = '') {
        if (event) event.preventDefault();
        const form = formOrElement?.tagName === 'FORM' ? formOrElement : (formOrElement?.closest('form') || event?.target?.closest('form'));
        window.smartConfirm({
            title: title,
            text: message,
            icon: icon,
            confirmButtonText: confirmText,
            confirmButtonClass: confirmClass
        }).then(confirmed => {
            if (confirmed && form) {
                form.submit();
            }
        });
        return false;
    };

    // Helper xử lý liên kết thẻ <a> có modal xác nhận trước khi điều hướng
    window.confirmLink = function(event, url, message = 'Bạn có chắc chắn muốn thực hiện?', title = 'Xác Nhận') {
        if (event) event.preventDefault();
        window.smartConfirm({
            title: title,
            text: message,
            icon: 'warning',
            confirmButtonText: '<i class="fa-solid fa-trash-can mr-1.5"></i> Xóa ngay',
            confirmButtonClass: '!bg-rose-600 hover:!bg-rose-500 !shadow-rose-600/30'
        }).then(confirmed => {
            if (confirmed && url) {
                window.location.href = url;
            }
        });
        return false;
    };

    // Helper xác nhận và chống spam submit form
    window.confirmAndDisable = function(form, message, title = 'Xác Nhận Hành Động') {
        window.smartConfirm({
            title: title,
            text: message,
            icon: 'warning',
            confirmButtonText: '<i class="fa-solid fa-check mr-1.5"></i> Xác nhận',
            confirmButtonClass: '!bg-rose-600 hover:!bg-rose-500 !shadow-rose-600/30'
        }).then(confirmed => {
            if (confirmed && form) {
                const btn = form.querySelector('.anti-spam-btn') || form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1"></i> Đang xử lý...';
                }
                form.submit();
            }
        });
        return false;
    };
</script>
