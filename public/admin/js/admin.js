/**
 * System Admin JavaScript SDK - SmartRoom & Renty
 */

const API_BASE = '/api';

// Lấy thông tin phiên đăng nhập
function getAdminToken() {
    return localStorage.getItem('admin_token');
}

function getAdminUser() {
    try {
        return JSON.parse(localStorage.getItem('admin_user') || 'null');
    } catch (e) {
        return null;
    }
}

// Kiểm tra quyền truy cập (Auth Guard)
function checkAdminAuth(redirectOnFail = true) {
    const token = getAdminToken();
    const user = getAdminUser();

    if (!token || !user || user.role !== 'admin') {
        if (redirectOnFail) {
            localStorage.removeItem('admin_token');
            localStorage.removeItem('admin_user');
            window.location.href = '/admin/login.html';
        }
        return false;
    }
    return true;
}

// Đăng xuất
function adminLogout() {
    if (confirm('Bạn có chắc chắn muốn đăng xuất khỏi trang quản trị hệ thống?')) {
        const token = getAdminToken();
        if (token) {
            fetch(`${API_BASE}/auth/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            }).catch(() => {});
        }
        localStorage.removeItem('admin_token');
        localStorage.removeItem('admin_user');
        window.location.href = '/admin/login.html';
    }
}

// Wrapper fetch với Authorization Bearer
async function adminFetch(endpoint, options = {}) {
    const token = getAdminToken();
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
        ...(options.headers || {})
    };

    try {
        const res = await fetch(`${API_BASE}${endpoint}`, {
            ...options,
            headers
        });

        // Nếu hết hạn token hoặc không có quyền
        if (res.status === 401 || res.status === 403) {
            const data = await res.json().catch(() => ({}));
            if (res.status === 401) {
                showToast('Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.', 'error');
                setTimeout(() => {
                    localStorage.removeItem('admin_token');
                    localStorage.removeItem('admin_user');
                    window.location.href = '/admin/login.html';
                }, 1500);
                throw new Error('Unauthorized');
            }
            showToast(data.message || 'Truy cập bị từ chối!', 'error');
            throw new Error(data.message || 'Forbidden');
        }

        const data = await res.json();
        return data;
    } catch (err) {
        console.error(`API Error [${endpoint}]:`, err);
        throw err;
    }
}

// Toast notification
function showToast(message, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    let icon = 'fa-circle-info text-indigo-400';
    if (type === 'success') icon = 'fa-circle-check text-emerald-400';
    if (type === 'error') icon = 'fa-circle-exclamation text-rose-400';

    toast.innerHTML = `
        <i class="fa-solid ${icon}"></i>
        <div style="flex-grow: 1; line-height: 1.4;">${message}</div>
    `;

    container.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 4500);
}

// Khởi tạo tên admin trên thanh Navbar
document.addEventListener('DOMContentLoaded', () => {
    const user = getAdminUser();
    const adminNameEl = document.getElementById('admin-display-name');
    if (adminNameEl && user) {
        adminNameEl.textContent = user.name || user.email || 'Admin';
    }
});
