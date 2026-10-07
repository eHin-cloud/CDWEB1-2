// Setup custom toast notification override for alert()
(function () {
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
        .custom-toast-success .custom-toast-icon {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .custom-toast-warning .custom-toast-icon {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        .custom-toast-error .custom-toast-icon {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
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

    window.alert = function (message) {
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
            <div class="custom-toast-close"><i class="fa-solid fa-xmark"></i></div>
        `;

        // Inline close click handler
        toast.querySelector('.custom-toast-close').addEventListener('click', () => {
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 400);
        });

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
})();

// Show session success or error toasts
function showSessionToasts() {
    if (window.rentySessionSuccess) {
        alert(window.rentySessionSuccess);
    }
    if (window.rentySessionError) {
        alert(window.rentySessionError);
    }
}
if (document.readyState === 'loading') {
    window.addEventListener('DOMContentLoaded', showSessionToasts);
} else {
    showSessionToasts();
}

// Toggle advanced filters
function toggleFilterDrawer() {
    const drawer = document.getElementById('filter-drawer');
    if (!drawer) return;
    const isHidden = drawer.classList.contains('hidden');
    if (isHidden) {
        drawer.classList.remove('hidden');
        drawer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        drawer.classList.add('hidden');
    }
}

function applyThemeMode(mode) {
    const isLight = mode === 'light';
    document.documentElement.classList.toggle('theme-light', isLight);
    document.body.classList.toggle('theme-light', isLight);
    document.querySelectorAll('[data-theme-icon], #theme-toggle-icon').forEach(icon => {
        if (!icon.classList.contains('theme-switch-icon')) {
            icon.classList.toggle('fa-sun', isLight);
            icon.classList.toggle('fa-moon', !isLight);
        }
    });
    document.querySelectorAll('[data-theme-switch]').forEach(button => {
        button.classList.toggle('is-light', isLight);
        button.setAttribute('aria-pressed', isLight ? 'true' : 'false');
    });
}

function toggleThemeMode() {
    const nextMode = document.body.classList.contains('theme-light') ? 'dark' : 'light';
    localStorage.setItem('renty_theme_mode', nextMode);
    document.body.classList.remove('theme-flipping');
    void document.body.offsetWidth;
    document.body.classList.add('theme-flipping');
    document.querySelectorAll('[data-theme-switch]').forEach(button => {
        button.classList.remove('is-animating');
        void button.offsetWidth;
        button.classList.add('is-animating');
    });
    applyThemeMode(nextMode);
}

// Read localStorage theme on run
applyThemeMode(localStorage.getItem('renty_theme_mode') || 'dark');

function initThemeAnimations() {
    document.getElementById('theme-flip-wash')?.addEventListener('animationend', () => {
        document.body.classList.remove('theme-flipping');
    });

    document.querySelectorAll('[data-theme-switch]').forEach(button => {
        button.addEventListener('animationend', () => button.classList.remove('is-animating'));
    });
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initThemeAnimations);
} else {
    initThemeAnimations();
}

let currentDetailRoomId = null;
let activeRoomReviews = [];
let activeRoomImages = [];
let activeRoomImageIndex = 0;
const MOVE_IN_MAX_PEOPLE = 5;
let activeRoomCost = {
    room: 0,
    people: 2,
    vehicles: 1
};

function formatCurrency(value) {
    return Number(value || 0).toLocaleString('vi-VN') + 'đ';
}

function formatShortPrice(value) {
    const millions = Number(value || 0) / 1000000;
    return `${millions.toFixed(millions % 1 === 0 ? 0 : 1)}tr/tháng`;
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function openQuickRoomPreview(event, roomId) {
    event.preventDefault();
    event.stopPropagation();

    const mockRooms = window.rentyRoomsData || {};
    const data = mockRooms[roomId];
    if (!data) return;

    const modal = document.getElementById('quick-room-preview');
    document.getElementById('quick-preview-image').src = data.cover_image;
    document.getElementById('quick-preview-media-label').textContent = `${data.media_source_label || 'Ảnh phòng'} · ${Array.isArray(data.image_urls) ? data.image_urls.length : 1} ảnh`;
    document.getElementById('quick-preview-title').textContent = data.title;
    document.getElementById('quick-preview-price').textContent = formatCurrency(data.price);
    document.getElementById('quick-preview-rating').textContent = `${data.rating} ⭐`;
    document.getElementById('quick-preview-area').textContent = data.area_text || `${data.area || 0} m²`;
    document.getElementById('quick-preview-location').textContent = data.area_name || 'Khu vực trung tâm';
    document.getElementById('quick-preview-video').textContent = data.video_url ? 'Có video tour' : 'Chưa có video tour';
    document.getElementById('quick-preview-detail').href = `/renty/room/${data.id}`;

    const tags = [
        data.loft_txt === 'Có' ? 'Có gác lửng' : 'Không gác lửng',
        data.balcony_txt === 'Có' ? 'Ban công/cửa sổ' : 'Không ban công',
        data.pets_txt === 'Có' ? 'Cho nuôi thú cưng' : 'Không thú cưng',
    ];
    document.getElementById('quick-preview-tags').innerHTML = tags
        .map(tag => `<span>${escapeHtml(tag)}</span>`)
        .join('');

    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeQuickRoomPreview() {
    const modal = document.getElementById('quick-room-preview');
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

function openReportModal(roomId, title) {
    const modal = document.getElementById('room-report-modal');
    const form = document.getElementById('room-report-form');
    form.action = `/renty/room/${roomId}/report`;
    document.getElementById('report-room-title').textContent = title || 'Renty Review sẽ kiểm tra báo cáo này.';
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeReportModal() {
    document.getElementById('room-report-modal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function updateMoveInCost(type, delta) {
    if (type === 'people') {
        activeRoomCost.people = Math.min(MOVE_IN_MAX_PEOPLE, Math.max(1, activeRoomCost.people + delta));
    }

    if (type === 'vehicles') {
        activeRoomCost.vehicles = Math.max(0, activeRoomCost.vehicles + delta);
    }

    const room = Number(activeRoomCost.room || 0);
    const deposit = room;
    const electric = 350000;
    const water = activeRoomCost.people * 20000;
    const service = 100000 + activeRoomCost.vehicles * 50000;
    const total = room + deposit + electric + water + service;

    document.getElementById('cost-people').textContent = activeRoomCost.people;
    document.getElementById('cost-vehicles').textContent = activeRoomCost.vehicles;
    document.getElementById('cost-room').textContent = formatCurrency(room);
    document.getElementById('cost-deposit').textContent = formatCurrency(deposit);
    document.getElementById('cost-electric').textContent = formatCurrency(electric);
    document.getElementById('cost-water').textContent = formatCurrency(water);
    document.getElementById('cost-service').textContent = formatCurrency(service);
    document.getElementById('cost-total').textContent = formatCurrency(total);

    const peopleMinus = document.getElementById('cost-people-minus');
    const peoplePlus = document.getElementById('cost-people-plus');
    peopleMinus.disabled = activeRoomCost.people <= 1;
    peoplePlus.disabled = activeRoomCost.people >= MOVE_IN_MAX_PEOPLE;
    peopleMinus.classList.toggle('opacity-40', peopleMinus.disabled);
    peoplePlus.classList.toggle('opacity-40', peoplePlus.disabled);
    peopleMinus.classList.toggle('cursor-not-allowed', peopleMinus.disabled);
    peoplePlus.classList.toggle('cursor-not-allowed', peoplePlus.disabled);
}

function toggleDetailDescription(button) {
    const description = document.getElementById('detail-full-description');
    description.classList.toggle('detail-description-clamped');
    button.textContent = description.classList.contains('detail-description-clamped') ? 'Xem thêm' : 'Thu gọn';
}

function renderReviewSummary(data) {
    const average = Number(data.rating || 0);
    const reviewCount = Array.isArray(data.reviews) ? data.reviews.length : 0;
    const criteria = [
        ['Sạch sẽ', Math.min(5, average + 0.1)],
        ['Vị trí', Math.max(3.5, average - 0.1)],
        ['Chủ nhà', Math.min(5, average + 0.05)],
        ['Giá cả', Math.max(3.5, average - 0.2)]
    ];

    document.getElementById('review-average-score').textContent = average.toFixed(1);
    document.getElementById('review-average-stars').textContent = '★'.repeat(Math.round(average)) + '☆'.repeat(5 - Math.round(average));
    document.getElementById('review-count-label').textContent = reviewCount > 0 ? `${reviewCount} đánh giá` : 'Chưa có đánh giá';
    document.getElementById('review-score-bars').innerHTML = criteria.map(([label, score]) => `
        <div class="review-score-row">
            <span>${label}</span>
            <div><i style="width: ${(score / 5) * 100}%"></i></div>
            <strong>${score.toFixed(1)}</strong>
        </div>
    `).join('');
}

function renderReviews(showAll = false) {
    const container = document.getElementById('detail-reviews-container');
    const button = document.getElementById('show-all-reviews-btn');
    container.innerHTML = '';

    if (!activeRoomReviews.length) {
        container.innerHTML = `
            <div class="py-4 text-center text-xs text-slate-500 italic">
                Chưa có đánh giá thực tế nào cho phòng này. Hãy là người đầu tiên đánh giá!
            </div>
        `;
        button.classList.add('hidden');
        return;
    }

    activeRoomReviews.slice(0, showAll ? activeRoomReviews.length : 2).forEach(rev => {
        const rating = Math.max(1, Math.min(5, Number(rev.rating || 5)));
        const stars = '★'.repeat(rating) + '☆'.repeat(5 - rating);
        const item = document.createElement('div');
        item.className = 'p-3 rounded-xl bg-slate-900/60 border border-slate-800/50 space-y-1.5';
        item.innerHTML = `
            <div class="flex justify-between items-center text-xs gap-3">
                <span class="font-bold text-slate-300">${escapeHtml(rev.author_name)}</span>
                <span class="text-amber-400 font-semibold whitespace-nowrap">${stars}</span>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed">${escapeHtml(rev.comment)}</p>
            <span class="block text-[9px] text-slate-600">${escapeHtml(rev.created_at)}</span>
        `;
        container.appendChild(item);
    });

    if (activeRoomReviews.length > 2) {
        button.classList.remove('hidden');
        button.textContent = showAll ? 'Thu gọn đánh giá' : 'Xem tất cả đánh giá';
        button.dataset.expanded = showAll ? 'true' : 'false';
    } else {
        button.classList.add('hidden');
    }
}

function toggleAllReviews() {
    const button = document.getElementById('show-all-reviews-btn');
    renderReviews(button.dataset.expanded !== 'true');
}

function normalizeRoomImages(data) {
    const rawAngles = Array.isArray(data.image_angles) ? data.image_angles : [];
    const rawUrls = Array.isArray(data.image_urls) && data.image_urls.length > 0 ? data.image_urls : [data.cover_image];

    return rawUrls.filter(Boolean).map((url, index) => {
        const angle = rawAngles[index] || {};
        return {
            url,
            label: angle.label || `Ảnh thực tế ${index + 1}`
        };
    });
}

function setActiveRoomImage(index) {
    if (!activeRoomImages.length) return;

    activeRoomImageIndex = Math.max(0, Math.min(activeRoomImages.length - 1, index));
    const image = activeRoomImages[activeRoomImageIndex];
    const mainImage = document.getElementById('detail-main-image');
    const angle = document.getElementById('detail-image-angle');

    mainImage.src = image.url;
    angle.textContent = image.label;
    document.querySelectorAll('#detail-image-thumbs button').forEach((btn, btnIndex) => {
        btn.classList.toggle('border-emerald-400', btnIndex === activeRoomImageIndex);
    });
}

function openImageZoom() {
    if (!activeRoomImages.length) return;

    const modal = document.getElementById('image-zoom-modal');
    modal.classList.remove('hidden');
    renderZoomImage();
}

function renderZoomImage() {
    const image = activeRoomImages[activeRoomImageIndex];
    if (!image) return;

    document.getElementById('zoom-main-image').src = image.url;
    document.getElementById('zoom-image-label').textContent = image.label;
    document.getElementById('zoom-image-count').textContent = `${activeRoomImageIndex + 1}/${activeRoomImages.length}`;
}

function changeZoomImage(delta) {
    if (!activeRoomImages.length) return;

    activeRoomImageIndex = (activeRoomImageIndex + delta + activeRoomImages.length) % activeRoomImages.length;
    setActiveRoomImage(activeRoomImageIndex);
    renderZoomImage();
}

function closeImageZoom() {
    document.getElementById('image-zoom-modal').classList.add('hidden');
}

function getViewedRoomIds() {
    try {
        return JSON.parse(localStorage.getItem('renty_viewed_rooms') || '[]');
    } catch (error) {
        return [];
    }
}

// Save room ID viewed in localStorage
function saveViewedRoom(roomId) {
    const normalizedId = String(roomId);
    const viewedIds = getViewedRoomIds().filter(id => id !== normalizedId);
    viewedIds.unshift(normalizedId);
    localStorage.setItem('renty_viewed_rooms', JSON.stringify(viewedIds.slice(0, 6)));
    renderViewedRooms();
}

function clearViewedRooms() {
    localStorage.removeItem('renty_viewed_rooms');
    document.querySelectorAll('.room-item-card').forEach(card => {
        card.dataset.viewed = 'false';
        card.classList.remove('room-card-viewed');
    });
    renderViewedRooms();
}

function renderViewedRooms() {
    const section = document.getElementById('viewed-rooms-section');
    const list = document.getElementById('viewed-rooms-list');
    const viewedIds = getViewedRoomIds();
    const mockRooms = window.rentyRoomsData || {};

    document.querySelectorAll('.room-item-card').forEach(card => {
        const isViewed = viewedIds.includes(String(card.dataset.roomId));
        card.dataset.viewed = isViewed ? 'true' : 'false';
        card.classList.toggle('room-card-viewed', isViewed);
        const strip = card.querySelector('.viewed-room-strip');
        if (strip) {
            strip.classList.toggle('hidden', !isViewed);
        }
    });

    if (!section || !list) return;

    const viewedRooms = viewedIds.map(id => mockRooms[id]).filter(Boolean);
    if (!viewedRooms.length) {
        section.classList.add('hidden');
        list.innerHTML = '';
        return;
    }

    section.classList.remove('hidden');
    list.innerHTML = viewedRooms.map(room => `
        <a href="/renty/room/${room.id}" class="viewed-room-chip">
            <img src="${room.cover_image}" alt="Phòng ${room.room_number}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
            <span>
                <strong>${escapeHtml(room.title)}</strong>
                <small>${Number(room.price || 0).toLocaleString('vi-VN')}đ/tháng · ${escapeHtml(room.area_text || '')}</small>
            </span>
        </a>
    `).join('');
}
window.saveViewedRoom = saveViewedRoom;
window.clearViewedRooms = clearViewedRooms;
window.renderViewedRooms = renderViewedRooms;

function getDynamicLocations() {
    const locations = new Set();
    
    // 1. Một số địa điểm mặc định phổ biến
    const defaults = [
        'cau giay', 'cầu giấy',
        'thanh xuan', 'thanh xuân',
        'quan 10', 'quận 10',
        'bach khoa', 'bách khoa',
        'dai hoc bach khoa', 'đại học bách khoa',
        'su pham', 'sư phạm',
        'quoc gia', 'quốc gia',
        'xuan thuy', 'xuân thủy',
        'thu duc', 'thủ đức',
        'quan 1', 'quận 1',
        'quan 3', 'quận 3',
        'quan 5', 'quận 5',
        'go vap', 'gò vấp',
        'binh thanh', 'bình thạnh'
    ];
    defaults.forEach(loc => locations.add(loc));

    // 2. Tự động quét từ các card phòng trọ trong trang
    document.querySelectorAll('.room-item-card').forEach(card => {
        const areaName = card.getAttribute('data-area-name');
        if (areaName) {
            const trimmed = areaName.trim().toLowerCase();
            locations.add(trimmed);
            locations.add(normalizeText(trimmed));
        }

        const title = card.getAttribute('data-title');
        if (title) {
            const schoolMatches = title.match(/(?:dai hoc|cao dang|truong)\s+[a-zA-Z0-9\sÀ-ỹ]+/gi);
            if (schoolMatches) {
                schoolMatches.forEach(match => {
                    const cleaned = match.replace(/[\,\.\-\;]/g, '').trim().toLowerCase();
                    if (cleaned.length > 5) {
                        locations.add(cleaned);
                        locations.add(normalizeText(cleaned));
                    }
                });
            }
        }
    });

    // Sắp xếp theo chiều dài giảm dần để ưu tiên so khớp các cụm từ dài nhất trước
    return Array.from(locations).sort((a, b) => b.length - a.length);
}

function parseNaturalSearch(query) {
    const normalized = query
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd');

    const parsed = {
        maxPrice: null,
        keywords: normalized.split(/\s+/).filter(Boolean),
        locations: [],
        amenities: {
            pets: normalized.includes('thu cung') || normalized.includes('pet'),
            loft: normalized.includes('gac') || normalized.includes('gac lung') || normalized.includes('gac xep'),
            balcony: normalized.includes('ban cong'),
            wc: normalized.includes('khep kin') || normalized.includes('wc') || normalized.includes('ve sinh') || normalized.includes('nha ve sinh')
        },
        near: []
    };

    const priceMatch = normalized.match(/(?:duoi|nho hon|toi da|<=?|tam|khoang)\s*(\d+(?:[.,]\d+)?)\s*(trieu|tr|m|000000)?/);
    if (priceMatch) {
        const amount = parseFloat(priceMatch[1].replace(',', '.'));
        parsed.maxPrice = amount < 100000 ? amount * 1000000 : amount;
    }

    const dynamicLocs = getDynamicLocations();
    let tempQuery = normalized;

    dynamicLocs.forEach(loc => {
        const index = tempQuery.indexOf(loc);
        if (index !== -1) {
            // Kiểm tra xem đây có phải là một từ độc lập hay không (tránh 'quan 1' khớp trong 'quan 10')
            const isWord = (index === 0 || /\s/.test(tempQuery[index - 1])) && 
                           (index + loc.length === tempQuery.length || /\s/.test(tempQuery[index + loc.length]));
            
            if (isWord) {
                parsed.locations.push(loc);
                parsed.near.push(loc);
                tempQuery = tempQuery.substring(0, index) + ' ' + tempQuery.substring(index + loc.length);
            }
        }
    });

    return parsed;
}

function normalizeText(value) {
    return String(value || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd');
}

// Room details modal
function openRoomDetailModal(roomId) {
    const mockRooms = window.rentyRoomsData || {};
    const data = mockRooms[roomId];
    if (!data) return;

    currentDetailRoomId = roomId;
    const summaryBox = document.getElementById('review-summary-box');
    summaryBox.classList.add('hidden');
    summaryBox.innerHTML = '';

    document.getElementById('detail-room-title').textContent = data.title;
    document.getElementById('detail-room-address').textContent = data.address;
    document.getElementById('detail-media-note').textContent = data.media_source_note || 'Ưu tiên ảnh thật theo từng góc, xem rõ trước khi liên hệ đặt lịch.';
    document.getElementById('detail-room-price').textContent = data.price.toLocaleString('vi-VN') + "đ/tháng";
    document.getElementById('sticky-room-price').textContent = formatShortPrice(data.price);
    document.getElementById('detail-room-rating').textContent = data.rating + " ⭐";
    document.getElementById('detail-room-owner').textContent = data.owner;
    document.getElementById('detail-room-sec').textContent = data.sec;
    document.getElementById('detail-room-pets').textContent = data.pets_txt;
    document.getElementById('detail-room-loft').textContent = data.loft_txt;
    document.getElementById('detail-room-balcony').textContent = data.balcony_txt;
    document.getElementById('detail-room-area').textContent = data.area_text;
    document.getElementById('detail-room-area-name').textContent = (data.address || '').split('(')[0].trim() || 'Khu vực trung tâm';

    const fullDescription = [
        data.location_description,
        data.scenery_description,
        data.space_description,
        `Tiện ích nổi bật: ${data.loft_txt === 'Có' ? 'có gác lửng' : 'không gác lửng'}, ${data.balcony_txt === 'Có' ? 'có ban công' : 'không ban công'}, ${data.pets_txt === 'Có' ? 'có thể nuôi thú cưng' : 'không nuôi thú cưng'}.`
    ].filter(Boolean).join(' ');
    const description = document.getElementById('detail-full-description');
    description.textContent = fullDescription;
    description.classList.add('detail-description-clamped');
    const descButton = description.nextElementSibling;
    if (descButton) descButton.textContent = 'Xem thêm';

    activeRoomCost = {
        room: Number(data.price || 0),
        people: 2,
        vehicles: 1
    };
    updateMoveInCost();

    const images = Array.isArray(data.image_urls) && data.image_urls.length > 0 ? data.image_urls : [data.cover_image];
    const mainImage = document.getElementById('detail-main-image');
    const imageCount = document.getElementById('detail-image-count');
    const thumbs = document.getElementById('detail-image-thumbs');
    const videoSection = document.getElementById('detail-video-section');
    const videoEmpty = document.getElementById('detail-video-empty');
    const roomVideo = document.getElementById('detail-room-video');

    activeRoomImages = normalizeRoomImages(data);
    activeRoomImageIndex = 0;
    mainImage.alt = `Ảnh phòng ${data.room_number}`;
    imageCount.textContent = `${images.length} ảnh phòng ${data.room_number}`;
    thumbs.innerHTML = '';
    setActiveRoomImage(0);

    activeRoomImages.slice(0, 6).forEach((image, index) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'relative h-20 rounded-xl overflow-hidden border border-slate-800 hover:border-emerald-500/70 transition-all focus:outline-none focus:border-emerald-400';
        button.innerHTML = `
            <img src="${image.url}" alt="${escapeHtml(image.label)} phòng ${data.room_number}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
            <span class="absolute left-1.5 right-1.5 bottom-1.5 rounded-md bg-slate-950/75 px-1.5 py-0.5 text-[8px] font-extrabold text-slate-100 truncate">${escapeHtml(image.label)}</span>
        `;
        button.addEventListener('click', () => {
            setActiveRoomImage(index);
        });
        if (index === 0) {
            button.classList.add('border-emerald-400');
        }
        thumbs.appendChild(button);
    });

    if (data.video_url) {
        roomVideo.src = data.video_url;
        videoSection.classList.remove('hidden');
        videoEmpty.classList.add('hidden');
    } else {
        roomVideo.removeAttribute('src');
        roomVideo.load();
        videoSection.classList.add('hidden');
        videoEmpty.classList.remove('hidden');
    }

    // Set form action route
    const form = document.getElementById('write-review-form');
    form.action = `/renty/room/${roomId}/review`;

    // Set contact request hidden input
    document.getElementById('contact-room-id').value = roomId;

    activeRoomReviews = Array.isArray(data.reviews) ? data.reviews : [];
    renderReviewSummary(data);
    renderReviews(false);

    const warningBox = document.getElementById('detail-price-warning');
    if (data.price_warning) {
        document.getElementById('detail-price-warning-title').textContent = data.price_warning.label;
        document.getElementById('detail-price-warning-message').textContent = data.price_warning.message;
        warningBox.classList.remove('hidden');
    } else {
        warningBox.classList.add('hidden');
    }

    saveViewedRoom(roomId);

    document.getElementById('room-detail-modal').classList.remove('hidden');
}

function loadReviewSummary(btn) {
    if (!currentDetailRoomId) return;

    const box = document.getElementById('review-summary-box');
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Đang tóm tắt...';
    box.classList.remove('hidden');
    box.textContent = 'AI đang đọc các review...';

    fetch(`/api/renty/rooms/${currentDetailRoomId}/reviews/summary`)
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = original;

            if (!data.success) {
                box.textContent = 'Không thể tóm tắt review.';
                return;
            }

            const summary = data.summary;
            const pros = (summary.pros || []).map(item => `<li>${escapeHtml(item)}</li>`).join('');
            const cons = (summary.cons || []).map(item => `<li>${escapeHtml(item)}</li>`).join('');
            box.innerHTML = `
                <div class="font-bold text-slate-200">${escapeHtml(summary.summary || '')}</div>
                ${pros ? `<div class="mt-2 text-emerald-300 font-bold">Ưu điểm</div><ul class="list-disc pl-5">${pros}</ul>` : ''}
                ${cons ? `<div class="mt-2 text-amber-300 font-bold">Cần lưu ý</div><ul class="list-disc pl-5">${cons}</ul>` : ''}
            `;
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = original;
            box.textContent = 'Không thể kết nối AI để tóm tắt review.';
        });
}

function closeRoomDetailModal() {
    document.getElementById('room-detail-modal').classList.add('hidden');
    closeImageZoom();
}

// Interactive Map Variables
let rentyMap = null;
let rentyMarkers = {};

function initRentyMap() {
    if (window.rentyGoogleMap && rentyMap) return;

    if (typeof GoogleMapsRenty !== 'undefined') {
        window.rentyGoogleMap = new GoogleMapsRenty('renty-interactive-map', {
            onSelectRoom: (room) => {
                const roomCard = document.querySelector(`.room-item-card[data-room-id="${room.id}"]`);
                if (roomCard) {
                    roomCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    roomCard.classList.add('ring-2', 'ring-emerald-500');
                    setTimeout(() => {
                        roomCard.classList.remove('ring-2', 'ring-emerald-500');
                    }, 2000);
                }
            }
        });
        rentyMap = window.rentyGoogleMap.map;
        rentyMarkers = window.rentyGoogleMap.markers;
        return;
    }

    if (rentyMap) return;

    const mockRooms = window.rentyRoomsData || {};

    // Center of Cầu Giấy area in Hà Nội by default
    let center = [21.036, 105.790];
    
    // Check if the first room is in HCMC to center the map on HCMC initially
    const roomsArray = Object.values(mockRooms);
    if (roomsArray.length > 0) {
        const firstRoom = roomsArray[0];
        const firstRoomIsHcm = (firstRoom.address && (firstRoom.address.includes('Hồ Chí Minh') || firstRoom.address.includes('Bình Thạnh') || firstRoom.address.includes('Quận 10') || firstRoom.address.includes('HCM'))) || (firstRoom.area_name && (firstRoom.area_name.includes('Quan 10') || firstRoom.area_name.includes('Bình Thạnh') || firstRoom.area_name.includes('Hồ Chí Minh')));
        if (firstRoomIsHcm) {
            center = [10.798, 106.705];
        }
    }

    // Create map
    rentyMap = L.map('renty-interactive-map', {
        zoomControl: true,
        attributionControl: false
    }).setView(center, 14);

    // Add colorful road map tile layer from Google Maps
    L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        attribution: 'Map data &copy; Google'
    }).addTo(rentyMap);

    // Add markers
    Object.values(mockRooms).forEach(room => {
        // Generate a deterministic coordinate based on room address/region keywords for realistic distribution
        let baseLat = 21.036; // Default Hanoi center
        let baseLng = 105.790;
        let isHcm = false;

        const addr = (room.address || '').toLowerCase();
        const area = (room.area_name || '').toLowerCase();

        if (addr.includes('hồ chí minh') || addr.includes('hcm') || addr.includes('thủ đức') || addr.includes('bình thạnh') || addr.includes('quận 10') || addr.includes('quận 7') || addr.includes('gò vấp') ||
            area.includes('hồ chí minh') || area.includes('hcm') || area.includes('thủ đức') || area.includes('bình thạnh') || area.includes('quan 10') || area.includes('quan 7') || area.includes('gò vấp')) {
            isHcm = true;
            // District specific coords in HCM
            if (addr.includes('thủ đức') || area.includes('thủ đức')) {
                if (addr.includes('linh trung') || addr.includes('đại học')) {
                    baseLat = 10.875;
                    baseLng = 106.801;
                } else {
                    baseLat = 10.850;
                    baseLng = 106.772;
                }
            } else if (addr.includes('quận 10') || area.includes('quan 10')) {
                baseLat = 10.775;
                baseLng = 106.667;
            } else if (addr.includes('quận 7') || area.includes('quan 7')) {
                baseLat = 10.732;
                baseLng = 106.726;
            } else if (addr.includes('gò vấp') || area.includes('gò vấp')) {
                baseLat = 10.838;
                baseLng = 106.665;
            } else {
                // default Binh Thanh / District 1
                baseLat = 10.803;
                baseLng = 106.708;
            }
        } else {
            // Hanoi districts mapping
            if (addr.includes('tây hồ') || area.includes('tây hồ')) {
                baseLat = 21.062;
                baseLng = 105.815;
            } else if (addr.includes('đống đa') || area.includes('đống đa')) {
                baseLat = 21.018;
                baseLng = 105.825;
            } else if (addr.includes('hai bà trưng') || area.includes('hai bà trưng')) {
                baseLat = 21.012;
                baseLng = 105.850;
            } else if (addr.includes('thanh xuân') || area.includes('thanh xuân')) {
                baseLat = 20.998;
                baseLng = 105.808;
            } else {
                // Default Cầu Giấy / Từ Liêm
                baseLat = 21.036;
                baseLng = 105.790;
            }
        }

        // Apply a small deterministic spread so pins do not overlap perfectly
        const offsetLat = Math.sin(room.id * 4.7) * 0.0035;
        const offsetLng = Math.cos(room.id * 5.9) * 0.0035;
        const lat = baseLat + offsetLat;
        const lng = baseLng + offsetLng;

        const shortPrice = (function (price) {
            if (price >= 1000000) {
                return (price / 1000000).toFixed(1).replace('.0', '') + 'M';
            }
            return (price / 1000).toFixed(0) + 'K';
        })(room.price);

        // Custom glowing pin HTML
        const customIcon = L.divIcon({
            className: 'custom-map-pin',
            html: `<div class="glowing-teal-pin" id="map-pin-${room.id}">${shortPrice}</div>`,
            iconSize: [50, 30],
            iconAnchor: [25, 15]
        });

        const marker = L.marker([lat, lng], { icon: customIcon }).addTo(rentyMap);

        // Add beautiful floating preview card inside popup
        const popupContent = `
            <div class="map-preview-card">
                <img class="map-preview-card-img" src="${room.cover_image}" alt="Phòng ${room.room_number}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">
                <div class="map-preview-card-body">
                    <h4 class="map-preview-card-title">${escapeHtml(room.title)}</h4>
                    <div class="map-preview-card-price-row">
                        <span class="map-preview-card-price">${Number(room.price).toLocaleString('vi-VN')}đ</span>
                        <span class="map-preview-card-rating">
                            <i class="fa-solid fa-star text-amber-400"></i> ${room.rating}
                        </span>
                    </div>
                    <a href="javascript:void(0)" class="map-preview-card-btn" id="map-card-detail-btn-${room.id}">Xem chi tiết</a>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent, {
            closeButton: false,
            offset: L.point(0, -10)
        });

        // Add custom detail link click handler after popup opens
        marker.on('popupopen', () => {
            const btn = document.getElementById(`map-card-detail-btn-${room.id}`);
            if (btn) {
                btn.addEventListener('click', () => {
                    openRoomDetailModal(room.id);
                });
            }
        });

        // Zoom straight in when hovering the pin on the map
        marker.on('mouseover', function () {
            document.querySelectorAll('.glowing-teal-pin').forEach(pin => {
                pin.classList.remove('active');
            });

            const pinEl = document.getElementById(`map-pin-${room.id}`);
            if (pinEl) {
                pinEl.classList.add('active');
            }

            rentyMap.setView(marker.getLatLng(), 17, {
                animate: true,
                duration: 0.8
            });

            marker.openPopup();
        });

        // Marker click effect and active state toggle
        marker.on('click', function () {
            // Remove active state from all other pins
            document.querySelectorAll('.glowing-teal-pin').forEach(pin => {
                pin.classList.remove('active');
            });

            // Add active state to this pin
            const pinEl = document.getElementById(`map-pin-${room.id}`);
            if (pinEl) {
                pinEl.classList.add('active');
            }

            // Smooth pan to marker
            rentyMap.panTo(marker.getLatLng());

            // Find and highlight matching card in scrollable right panel
            const roomCard = document.querySelector(`.room-item-card[data-room-id="${room.id}"]`);
            if (roomCard) {
                roomCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                roomCard.classList.add('ring-2', 'ring-emerald-500');
                setTimeout(() => {
                    roomCard.classList.remove('ring-2', 'ring-emerald-500');
                }, 2000);
            }
        });

        marker.on('popupclose', function () {
            const pinEl = document.getElementById(`map-pin-${room.id}`);
            if (pinEl) {
                pinEl.classList.remove('active');
            }
        });

        rentyMarkers[room.id] = marker;
    });

    // Create coordinate indicator box on the map
    const coordBox = document.createElement('div');
    coordBox.id = 'map-coords-indicator';
    coordBox.className = 'absolute bottom-4 left-4 z-[1000] px-3 py-1.5 rounded-xl bg-slate-950/85 border border-white/10 text-[10px] font-extrabold text-slate-300 backdrop-blur pointer-events-none transition-opacity duration-300 opacity-0 flex items-center shadow-lg';
    coordBox.innerHTML = `<i class="fa-solid fa-location-crosshairs text-teal-400 mr-1.5"></i> 0.00000, 0.00000`;

    const mapContainer = document.getElementById('renty-interactive-map');
    if (mapContainer) {
        mapContainer.appendChild(coordBox);
    }

    // Update coordinate indicator when hovering and moving mouse over the map
    rentyMap.on('mousemove', function (e) {
        const coordEl = document.getElementById('map-coords-indicator');
        if (coordEl) {
            const lat = e.latlng.lat.toFixed(5);
            const lng = e.latlng.lng.toFixed(5);
            coordEl.innerHTML = `<i class="fa-solid fa-location-crosshairs text-teal-400 mr-1.5"></i> Tọa độ: ${lat}, ${lng}`;
            coordEl.style.opacity = '1';
        }
    });

    rentyMap.on('mouseout', function () {
        const coordEl = document.getElementById('map-coords-indicator');
        if (coordEl) {
            coordEl.style.opacity = '0';
        }
    });

    // Global hover listener using event delegation to zoom straight to matching map marker
    document.body.addEventListener('mouseover', (e) => {
        const card = e.target.closest('.room-item-card');
        if (!card) return;
        const roomId = card.getAttribute('data-room-id');
        if (!roomId) return;

        if (window.rentyGoogleMap && window.rentyGoogleMap.markers && window.rentyGoogleMap.markers[roomId]) {
            window.rentyGoogleMap.highlightMarker(roomId);
            window.rentyGoogleMap.markers[roomId].openPopup();
            window.rentyGoogleMap.map.panTo(window.rentyGoogleMap.markers[roomId].getLatLng(), {
                animate: true,
                duration: 0.8
            });
        } else if (rentyMarkers && rentyMarkers[roomId] && rentyMap) {
            // Highlight pin
            document.querySelectorAll('.glowing-teal-pin').forEach(pin => {
                pin.classList.remove('active');
            });
            const pinEl = document.getElementById(`map-pin-${roomId}`);
            if (pinEl) pinEl.classList.add('active');

            // Open Leaflet popup
            rentyMarkers[roomId].openPopup();
            
            // Zoom straight into marker (level 17)
            rentyMap.setView(rentyMarkers[roomId].getLatLng(), 17, {
                animate: true,
                duration: 0.8
            });
        }
    });
}

// Helper to control marker visibility during filtering
function showMarker(id) {
    if (window.rentyGoogleMap && window.rentyGoogleMap.markers && window.rentyGoogleMap.markers[id]) {
        const marker = window.rentyGoogleMap.markers[id];
        if (window.rentyGoogleMap.map && !window.rentyGoogleMap.map.hasLayer(marker)) {
            window.rentyGoogleMap.map.addLayer(marker);
        }
    } else if (rentyMarkers[id] && rentyMap) {
        if (!rentyMap.hasLayer(rentyMarkers[id])) {
            rentyMap.addLayer(rentyMarkers[id]);
        }
    }
}

function hideMarker(id) {
    if (window.rentyGoogleMap && window.rentyGoogleMap.markers && window.rentyGoogleMap.markers[id]) {
        const marker = window.rentyGoogleMap.markers[id];
        if (window.rentyGoogleMap.map && window.rentyGoogleMap.map.hasLayer(marker)) {
            window.rentyGoogleMap.map.removeLayer(marker);
        }
    } else if (rentyMarkers[id] && rentyMap) {
        if (rentyMap.hasLayer(rentyMarkers[id])) {
            rentyMap.removeLayer(rentyMarkers[id]);
        }
    }
}

function setViewMode(mode) {
    const mapBtn = document.getElementById('view-mode-map-btn');
    const gridBtn = document.getElementById('view-mode-grid-btn');

    if (mode === 'map') {
        document.body.classList.add('renty-map-mode');
        if (mapBtn) mapBtn.classList.add('active');
        if (gridBtn) gridBtn.classList.remove('active');
        localStorage.setItem('rentry_view_mode', 'map');

        // Initialize map if not already done
        setTimeout(() => {
            initRentyMap();
            if (rentyMap) {
                rentyMap.invalidateSize();
            }
        }, 450);
    } else {
        document.body.classList.remove('renty-map-mode');
        if (mapBtn) mapBtn.classList.remove('active');
        if (gridBtn) gridBtn.classList.add('active');
        localStorage.setItem('rentry_view_mode', 'grid');
    }
}

function initModalListeners() {
    document.getElementById('image-zoom-modal')?.addEventListener('click', (event) => {
        if (event.target.id === 'image-zoom-modal') {
            closeImageZoom();
        }
    });

    document.getElementById('quick-room-preview')?.addEventListener('click', (event) => {
        if (event.target.id === 'quick-room-preview') {
            closeQuickRoomPreview();
        }
    });

    document.getElementById('room-report-modal')?.addEventListener('click', (event) => {
        if (event.target.id === 'room-report-modal') {
            closeReportModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeImageZoom();
            closeQuickRoomPreview();
            closeReportModal();
        }

        if (!document.getElementById('image-zoom-modal')?.classList.contains('hidden')) {
            if (event.key === 'ArrowLeft') changeZoomImage(-1);
            if (event.key === 'ArrowRight') changeZoomImage(1);
        }
    });
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModalListeners);
} else {
    initModalListeners();
}

let rentySearchSkeletonTimer = null;

function setSearchSkeletonLoading(isLoading) {
    const resultsCount = document.getElementById('results-count');

    document.querySelectorAll('.room-item-card').forEach(card => {
        if (isLoading) {
            card.classList.remove('hidden');
        }
        card.classList.toggle('is-search-loading', isLoading);
    });

    if (isLoading && resultsCount) {
        resultsCount.textContent = 'Đang tìm phòng phù hợp...';
    }
}

function runSearchWithSkeleton() {
    clearTimeout(rentySearchSkeletonTimer);
    setSearchSkeletonLoading(true);

    rentySearchSkeletonTimer = setTimeout(() => {
        filterItems({ keepSkeleton: true });
        setSearchSkeletonLoading(false);
    }, 680);
}

let rentyCurrentPage = 1;
const rentyItemsPerPage = 9;

// Dynamically render premium glassmorphism pagination controls
function renderPageButton(page, isActive) {
    return `
        <button type="button" 
                onclick="changeRentyPage(${page})" 
                class="w-9 h-9 rounded-xl font-extrabold text-xs transition-all border ${isActive ? 'bg-gradient-to-tr from-emerald-600 to-teal-500 text-white border-transparent shadow-lg shadow-emerald-500/20' : 'bg-slate-900/40 border-slate-800/80 text-slate-400 hover:border-emerald-500/30 hover:text-slate-200'}">
            ${page}
        </button>
    `;
}

function renderEllipsisButton(totalPages) {
    return `
        <div class="relative inline-block pagination-ellipsis-container">
            <button type="button" 
                    onclick="makePaginationInput(this, ${totalPages})" 
                    class="w-9 h-9 rounded-xl font-extrabold text-xs transition-all border bg-slate-900/40 border-slate-800/80 text-slate-400 hover:border-emerald-500/30 hover:text-slate-200 flex items-center justify-center cursor-pointer"
                    title="Click để nhập số trang nhanh">
                ...
            </button>
        </div>
    `;
}

function makePaginationInput(button, totalPages) {
    const container = button.parentElement;
    if (!container) return;

    container.innerHTML = `
        <input type="number" 
               min="1" 
               max="${totalPages}" 
               placeholder="..." 
               class="w-12 h-9 text-center rounded-xl bg-slate-950/90 border border-emerald-500/80 text-emerald-400 font-extrabold text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all shadow-lg shadow-emerald-500/10 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
               onkeydown="handlePaginationInputKey(event, this, ${totalPages})" 
               onblur="restorePaginationEllipsis(this, ${totalPages})">
    `;

    const input = container.querySelector('input');
    if (input) {
        input.focus();
        input.select();
    }
}

function handlePaginationInputKey(event, input, totalPages) {
    if (event.key === 'Enter') {
        const val = parseInt(input.value);
        if (!isNaN(val) && val >= 1 && val <= totalPages) {
            changeRentyPage(val);
        } else {
            input.classList.add('border-rose-500', 'text-rose-450');
            setTimeout(() => {
                input.classList.remove('border-rose-500', 'text-rose-455');
            }, 500);
        }
    } else if (event.key === 'Escape') {
        restorePaginationEllipsis(input, totalPages);
    }
}

function restorePaginationEllipsis(input, totalPages) {
    const container = input.parentElement;
    if (!container) return;

    setTimeout(() => {
        if (container.contains(input)) {
            container.innerHTML = `
                <button type="button" 
                        onclick="makePaginationInput(this, ${totalPages})" 
                        class="w-9 h-9 rounded-xl font-extrabold text-xs transition-all border bg-slate-900/40 border-slate-800/80 text-slate-400 hover:border-emerald-500/30 hover:text-slate-200 flex items-center justify-center cursor-pointer"
                        title="Click để nhập số trang nhanh">
                    ...
                </button>
            `;
        }
    }, 120);
}

// Make functions global
window.makePaginationInput = makePaginationInput;
window.handlePaginationInputKey = handlePaginationInputKey;
window.restorePaginationEllipsis = restorePaginationEllipsis;

function renderPaginationControls(totalPages) {
    const container = document.getElementById('renty-pagination');
    if (!container) return;

    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '';

    // Previous Button
    html += `
        <button type="button" 
                onclick="changeRentyPage(${rentyCurrentPage - 1})" 
                class="px-4 py-2 rounded-xl bg-slate-900/40 border border-slate-800/85 hover:border-emerald-500/40 hover:text-emerald-400 transition-all font-bold text-xs flex items-center justify-center gap-1.5 ${rentyCurrentPage === 1 ? 'opacity-40 cursor-not-allowed' : ''}" 
                ${rentyCurrentPage === 1 ? 'disabled' : ''}>
            <i class="fa-solid fa-chevron-left text-[10px]"></i> Trước
        </button>
    `;

    // Page numbers algorithm
    const maxVisiblePages = 7;
    if (totalPages <= maxVisiblePages) {
        for (let i = 1; i <= totalPages; i++) {
            html += renderPageButton(i, i === rentyCurrentPage);
        }
    } else {
        // We have more than 7 pages, use ellipsis
        if (rentyCurrentPage <= 4) {
            // Near the start: 1 2 3 4 5 ... totalPages
            for (let i = 1; i <= 5; i++) {
                html += renderPageButton(i, i === rentyCurrentPage);
            }
            html += renderEllipsisButton(totalPages);
            html += renderPageButton(totalPages, false);
        } else if (rentyCurrentPage >= totalPages - 3) {
            // Near the end: 1 ... totalPages-4 totalPages-3 totalPages-2 totalPages-1 totalPages
            html += renderPageButton(1, false);
            html += renderEllipsisButton(totalPages);
            for (let i = totalPages - 4; i <= totalPages; i++) {
                html += renderPageButton(i, i === rentyCurrentPage);
            }
        } else {
            // In the middle: 1 ... current-1 current current+1 ... totalPages
            html += renderPageButton(1, false);
            html += renderEllipsisButton(totalPages);
            
            html += renderPageButton(rentyCurrentPage - 1, false);
            html += renderPageButton(rentyCurrentPage, true);
            html += renderPageButton(rentyCurrentPage + 1, false);
            
            html += renderEllipsisButton(totalPages);
            html += renderPageButton(totalPages, false);
        }
    }

    // Next Button
    html += `
        <button type="button" 
                onclick="changeRentyPage(${rentyCurrentPage + 1})" 
                class="px-4 py-2 rounded-xl bg-slate-900/40 border border-slate-800/85 hover:border-emerald-500/40 hover:text-emerald-400 transition-all font-bold text-xs flex items-center justify-center gap-1.5 ${rentyCurrentPage === totalPages ? 'opacity-40 cursor-not-allowed' : ''}" 
                ${rentyCurrentPage === totalPages ? 'disabled' : ''}>
            Sau <i class="fa-solid fa-chevron-right text-[10px]"></i>
        </button>
    `;

    container.innerHTML = html;
}

function changeRentyPage(page) {
    rentyCurrentPage = page;
    filterItems({ resetPage: false });
    
    // Smooth scroll to top of main workspace
    const resultsCount = document.getElementById('results-count');
    if (resultsCount) {
        resultsCount.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Make changeRentyPage global so inline event handler onclick works
window.changeRentyPage = changeRentyPage;

let rentySearchAbortController = null;

function renderRoomCardsHtml(rooms) {
    const compareList = window.compareRoomIds || [];

    return rooms.map(room => {
        const isRented = room.status !== 'empty';
        const cardImages = (room.image_urls && room.image_urls.length > 0 ? room.image_urls : [room.cover_image]).slice(0, 4);

        let imagesHtml = '';
        cardImages.forEach((imgUrl, idx) => {
            imagesHtml += `<img src="${imgUrl}" alt="Ảnh ${idx + 1}" class="renty-card-carousel-image" style="--carousel-delay: ${idx * 2}s;" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80';">`;
        });

        const statusBadgeHtml = !isRented
            ? `<span class="room-status-badge room-status-empty absolute top-4 left-4 px-2.5 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm z-10 flex items-center gap-1.5">
                   <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Sẵn sàng
               </span>`
            : `<span class="room-status-badge room-status-rented absolute top-4 left-4 px-2.5 py-1 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm z-10 flex items-center gap-1.5">
                   <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Đã thuê
               </span>
               <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-[0.5px] z-10"></div>
               <div class="absolute inset-0 flex items-center justify-center z-20">
                   <button type="button" onclick="subscribeEmptyNotification(event, '${room.id}', '${escapeHtml(room.title)}')" class="py-2 px-3.5 rounded-xl bg-slate-900/95 hover:bg-slate-900 border border-slate-800 text-slate-200 hover:text-white flex items-center gap-2 text-[10px] font-extrabold shadow-lg transition-all renty-notify-btn group/bell-btn">
                       <i class="fa-solid fa-bell text-teal-400 text-xs"></i> Chuông báo khi trống phòng
                   </button>
               </div>`;

        const priceWarningHtml = room.price_warning 
            ? `<span class="px-2.5 py-1 bg-amber-500/10 text-amber-300 border border-amber-500/25 rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm flex items-center gap-1.5" title="${escapeHtml(room.price_warning.message)}">
                   <i class="fa-solid fa-triangle-exclamation"></i> ${room.price_warning.type === 'low' ? 'Giá quá rẻ' : 'Giá cao'}
               </span>`
            : '';

        const trustBadge = room.trust_badge || { label: 'Xác Minh', icon: 'fa-shield-check', class: 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30' };
        const trustBadgeHtml = `<span class="px-2.5 py-1 border rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm flex items-center gap-1.5 ${trustBadge.class}">
            <i class="fa-solid ${trustBadge.icon}"></i> ${escapeHtml(trustBadge.label)}
        </span>`;

        const hybridBadgeHtml = (room.retrieval_method === 'hybrid_rrf' && room.rrf_score > 0)
            ? `<span class="px-2.5 py-1 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-lg text-[9px] font-extrabold uppercase tracking-wider shadow-sm flex items-center gap-1.5" title="Hybrid Search (Dual Retrieval + RRF Score: ${room.rrf_score})">
                <i class="fa-solid fa-bolt-lightning text-amber-400"></i> Hybrid RRF
            </span>`
            : '';

        const isCheckedInCompare = compareList.includes(String(room.id));

        return `
        <div class="room-item-card glass-card rounded-2xl overflow-hidden group flex flex-col justify-between relative"
             data-room-id="${room.id}"
             data-price="${room.price}"
             data-rating="${room.rating}"
             data-pets="${room.pets}"
             data-loft="${room.loft}"
             data-balcony="${room.balcony}"
             data-wc="${room.wc}"
             data-distance="${room.distance}"
             data-area-name="${escapeHtml(room.area_name)}"
             data-status="${room.status}"
             data-viewed="false"
             data-title="${escapeHtml(room.title)}"
             data-address="${escapeHtml(room.address)}">
            <div class="room-card-content">
                <div class="room-card-media h-48 bg-slate-950 relative overflow-hidden border-b border-slate-900 group">
                    <a href="${room.url}" class="absolute inset-0 z-0 renty-card-carousel" aria-label="Xem chi tiết phòng ${room.room_number}">
                        ${imagesHtml}
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/10 to-transparent"></div>
                    </a>
                    ${statusBadgeHtml}
                    <div class="absolute top-14 right-4 z-10 flex flex-col items-end gap-1.5">
                        ${priceWarningHtml}
                        ${trustBadgeHtml}
                        ${hybridBadgeHtml}
                    </div>
                    <button type="button" onclick="openQuickRoomPreview(event, '${room.id}')" class="absolute top-4 right-4 px-3 py-1.5 rounded-xl bg-slate-950/82 border border-white/10 text-[10px] font-extrabold text-slate-100 backdrop-blur z-20 flex items-center gap-1.5 hover:border-emerald-400/60 hover:text-emerald-200 quick-eye-button" title="Xem nhanh thông tin phòng">
                        <i class="fa-solid fa-eye text-slate-300"></i> <span class="quick-eye-text">Xem nhanh</span>
                    </button>
                    <div class="absolute left-4 right-4 bottom-4 z-10 flex items-end justify-between gap-3">
                        <div>
                            <span class="block text-[10px] font-extrabold text-white uppercase tracking-widest drop-shadow">${escapeHtml(room.media_source_label || 'Ảnh thực tế')}</span>
                            <span class="block text-[10px] font-semibold text-slate-300 mt-0.5">Phòng ${room.room_number} · ${(room.image_urls || []).length} ảnh</span>
                        </div>
                        <span class="w-9 h-9 rounded-xl bg-slate-950/70 border border-white/10 backdrop-blur flex items-center justify-center text-emerald-300">
                            <i class="fa-solid fa-images"></i>
                        </span>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <a href="${room.url}" class="hover:text-emerald-400 transition-colors">
                            <h3 class="text-sm font-bold text-slate-100 leading-snug line-clamp-1">${escapeHtml(room.title)}</h3>
                        </a>
                        <div class="flex items-center gap-1 px-2 py-0.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xs font-bold shrink-0">
                            <i class="fa-solid fa-star text-[10px]"></i>
                            <span>${room.rating}</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 flex items-center gap-1.5 mb-3 line-clamp-1">
                        <i class="fa-solid fa-location-dot text-slate-500 text-[10px] shrink-0"></i>
                        <span>${escapeHtml(room.address)}</span>
                    </p>
                    <div class="flex items-center justify-between pt-3 border-t border-slate-800/80">
                        <div>
                            <span class="text-lg font-black text-emerald-400">${room.price_formatted}</span>
                            <span class="text-[10px] text-slate-500 font-bold">/tháng</span>
                        </div>
                        <span class="text-xs text-slate-400 font-bold bg-slate-900/60 px-2.5 py-1 rounded-lg border border-slate-800/60">
                            ${room.area_text}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mt-3 pt-3 border-t border-slate-800/40">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold ${room.wc ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20' : 'bg-slate-900 text-slate-500'}">WC ${room.wc_txt}</span>
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold ${room.balcony ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20' : 'bg-slate-900 text-slate-500'}">Ban công ${room.balcony_txt}</span>
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold ${room.loft ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20' : 'bg-slate-900 text-slate-500'}">Gác lửng ${room.loft_txt}</span>
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold ${room.pets ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20' : 'bg-slate-900 text-slate-500'}">Pet ${room.pets_txt}</span>
                    </div>
                </div>
            </div>
            <div class="p-4 pt-0 flex items-center justify-between gap-3">
                <label class="flex items-center gap-2 cursor-pointer select-none text-[11px] text-slate-400 hover:text-slate-200">
                    <input type="checkbox" value="${room.id}" data-room-id="${room.id}" onchange="handleCompareCheck(this, '${room.id}')" ${isCheckedInCompare ? 'checked' : ''} class="compare-checkbox rounded border-slate-700 bg-slate-900 text-emerald-500 focus:ring-0 cursor-pointer" style="accent-color: #10b981; width: 17px; height: 17px;">
                    <span class="font-bold">So sánh</span>
                </label>
                <a href="${room.url}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-slate-200 hover:text-white border border-slate-800 hover:border-emerald-500 text-xs font-bold transition-all flex items-center gap-1.5">
                    <span>Xem chi tiết</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>`;
    }).join('');
}

function syncFilterStateToUrl(params) {
    if (!window.history || !window.history.replaceState) return;
    const url = new URL(window.location.href);
    ['q', 'min_price', 'max_price', 'status', 'rating', 'pets', 'loft', 'balcony', 'wc', 'page'].forEach(k => url.searchParams.delete(k));

    if (params.q) url.searchParams.set('q', params.q);
    if (params.min_price) url.searchParams.set('min_price', params.min_price);
    if (params.max_price) url.searchParams.set('max_price', params.max_price);
    if (params.status && params.status !== 'all') url.searchParams.set('status', params.status);
    if (params.rating && params.rating !== 'all') url.searchParams.set('rating', params.rating);
    if (params.amenities && params.amenities.length > 0) {
        params.amenities.forEach(a => url.searchParams.set(a, '1'));
    }
    if (rentyCurrentPage > 1) url.searchParams.set('page', rentyCurrentPage);

    window.history.replaceState({}, '', url.toString());
}

function applyDidYouMeanSearch() {
    const textEl = document.getElementById('smart-search-did-you-mean-text');
    if (!textEl || !textEl.textContent) return;
    const input = document.getElementById('search-input');
    if (input) {
        input.value = textEl.textContent.trim();
        filterItems();
    }
}
window.applyDidYouMeanSearch = applyDidYouMeanSearch;

function resetAllSearchFilters() {
    const searchInput = document.getElementById('search-input');
    if (searchInput) searchInput.value = '';
    const minPriceInput = document.getElementById('filter-price-min');
    if (minPriceInput) minPriceInput.value = '';
    const maxPriceInput = document.getElementById('filter-price-max');
    if (maxPriceInput) maxPriceInput.value = '';
    const filterPrice = document.getElementById('filter-price');
    if (filterPrice) filterPrice.value = 'all';
    const filterRating = document.getElementById('filter-rating');
    if (filterRating) filterRating.value = 'all';
    const hideRented = document.getElementById('hide-rented-toggle');
    if (hideRented) hideRented.checked = false;
    ['pets', 'loft', 'balcony', 'wc'].forEach(k => {
        const cb = document.getElementById('tag-' + k);
        if (cb) cb.checked = false;
        const btn = document.getElementById('vbtn-' + k);
        if (btn) btn.classList.remove('active');
    });
    const errorEl = document.getElementById('filter-price-error');
    if (errorEl) errorEl.classList.add('hidden');
    if (minPriceInput) minPriceInput.classList.remove('border-rose-500', 'ring-1', 'ring-rose-500');
    if (maxPriceInput) maxPriceInput.classList.remove('border-rose-500', 'ring-1', 'ring-rose-500');
    
    filterItems();
}
window.resetAllSearchFilters = resetAllSearchFilters;

function restoreFiltersFromUrl() {
    const url = new URL(window.location.href);
    const q = url.searchParams.get('q') || url.searchParams.get('search');
    if (q) {
        const input = document.getElementById('search-input');
        if (input) input.value = q;
    }
    const minPrice = url.searchParams.get('min_price');
    if (minPrice) {
        const el = document.getElementById('filter-price-min');
        if (el) el.value = minPrice;
    }
    const maxPrice = url.searchParams.get('max_price');
    if (maxPrice) {
        const el = document.getElementById('filter-price-max');
        if (el) el.value = maxPrice;
    }
    const status = url.searchParams.get('status');
    if (status === 'empty') {
        const el = document.getElementById('hide-rented-toggle');
        if (el) el.checked = true;
    }
    const rating = url.searchParams.get('rating');
    if (rating) {
        const el = document.getElementById('filter-rating');
        if (el) el.value = rating;
    }
    ['pets', 'loft', 'balcony', 'wc'].forEach(k => {
        if (url.searchParams.get(k) === '1') {
            const cb = document.getElementById('tag-' + k);
            if (cb) cb.checked = true;
            const btn = document.getElementById('vbtn-' + k);
            if (btn) btn.classList.add('active');
        }
    });
    const page = url.searchParams.get('page');
    if (page && parseInt(page) > 0) {
        rentyCurrentPage = parseInt(page);
    }
}
window.restoreFiltersFromUrl = restoreFiltersFromUrl;

async function executeSmartSearchApi(params) {
    const amenities = [];
    if (params.petChecked) amenities.push('pets');
    if (params.loftChecked) amenities.push('loft');
    if (params.balconyChecked) amenities.push('balcony');
    if (params.wcChecked) amenities.push('wc');
    if (document.getElementById('tag-ac')?.checked) amenities.push('air_conditioner');
    if (document.getElementById('tag-washer')?.checked) amenities.push('washing_machine');

    let effectiveMaxPrice = params.maxPriceVal;
    if (effectiveMaxPrice === null && params.filterPrice && params.filterPrice !== 'all') {
        effectiveMaxPrice = parseInt(params.filterPrice);
    }

    const sortBy = document.getElementById('sort_by')?.value || 'default';

    const searchParams = new URLSearchParams();
    if (params.query) searchParams.set('q', params.query);
    if (params.minPriceVal !== null) searchParams.set('min_price', params.minPriceVal);
    if (effectiveMaxPrice !== null) searchParams.set('max_price', effectiveMaxPrice);
    if (params.filterRating && params.filterRating !== 'all') searchParams.set('rating', params.filterRating);
    if (sortBy !== 'default') searchParams.set('sort_by', sortBy);
    if (params.hideRented) searchParams.set('status', 'empty');
    if (amenities.length > 0) searchParams.set('amenities', amenities.join(','));
    searchParams.set('page', rentyCurrentPage);
    searchParams.set('limit', rentyItemsPerPage);

    syncFilterStateToUrl({
        q: params.query,
        min_price: params.minPriceVal,
        max_price: effectiveMaxPrice,
        rating: params.filterRating,
        sort_by: sortBy,
        status: params.hideRented ? 'empty' : 'all',
        amenities: amenities
    });

    setSearchSkeletonLoading(true);

    if (rentySearchAbortController) {
        rentySearchAbortController.abort();
    }
    rentySearchAbortController = new AbortController();

    try {
        const response = await fetch(`/api/renty/rooms/smart-search?${searchParams.toString()}`, {
            signal: rentySearchAbortController.signal
        });
        const data = await response.json();

        setSearchSkeletonLoading(false);

        if (!data.success) {
            console.warn("Lỗi tìm kiếm:", data.message);
            return;
        }

        // 1. Xử lý banner Did You Mean
        const didYouMeanBanner = document.getElementById('smart-search-did-you-mean-banner');
        const didYouMeanText = document.getElementById('smart-search-did-you-mean-text');
        if (data.has_correction && data.did_you_mean && data.did_you_mean.toLowerCase() !== (params.query || '').toLowerCase()) {
            if (didYouMeanBanner && didYouMeanText) {
                didYouMeanText.textContent = data.did_you_mean;
                didYouMeanBanner.classList.remove('hidden');
            }
        } else {
            if (didYouMeanBanner) didYouMeanBanner.classList.add('hidden');
        }

        // 2. Cập nhật số lượng kết quả
        const resultsCountEl = document.getElementById('results-count');
        const total = data.total ?? data.count ?? 0;
        if (resultsCountEl) {
            resultsCountEl.textContent = `Tìm thấy ${total} phòng`;
        }

        // 3. Xử lý hiển thị danh sách phòng & Empty State (ERR_21_04)
        const roomsGrid = document.getElementById('rooms-grid');
        const emptyState = document.getElementById('smart-search-empty-state');

        if (total === 0 || !data.rooms || data.rooms.length === 0) {
            if (roomsGrid) roomsGrid.classList.add('hidden');
            if (emptyState) emptyState.classList.remove('hidden');
            const emptyTitleEl = document.getElementById('smart-search-empty-title');
            const emptyCodeTextEl = document.getElementById('smart-search-empty-code-text') || document.getElementById('smart-search-empty-code');
            const q = (params.query || '').trim();
            if (emptyCodeTextEl) {
                if (q) {
                    emptyCodeTextEl.textContent = `Lỗi: Không tìm thấy kết quả phù hợp cho "${q}" (Mã: ERR_21_04)`;
                    if (emptyTitleEl) emptyTitleEl.textContent = `Không tìm thấy phòng nào khớp với "${q}".`;
                } else {
                    emptyCodeTextEl.textContent = 'Lỗi: Không tìm thấy phòng phù hợp với bộ lọc bạn đã chọn (Mã: ERR_21_04)';
                    if (emptyTitleEl) emptyTitleEl.textContent = 'Không tìm thấy phòng nào phù hợp với bộ lọc bạn đã chọn.';
                }
            }
            renderPaginationControls(0);
        } else {
            if (emptyState) emptyState.classList.add('hidden');
            if (roomsGrid) {
                roomsGrid.classList.remove('hidden');
                roomsGrid.innerHTML = renderRoomCardsHtml(data.rooms);
            }
            const totalPages = Math.ceil(total / rentyItemsPerPage);
            renderPaginationControls(totalPages);
        }

        // 4. Đồng bộ Leaflet map nếu có
        if (rentyMap && typeof L !== 'undefined' && data.rooms) {
            const currentIds = data.rooms.map(r => String(r.id));
            Object.keys(rentyMarkers).forEach(roomId => {
                if (currentIds.includes(String(roomId))) {
                    showMarker(roomId);
                } else {
                    hideMarker(roomId);
                }
            });
        }

    } catch (err) {
        if (err.name !== 'AbortError') {
            console.error("Lỗi kết nối khi tìm kiếm:", err);
            setSearchSkeletonLoading(false);
        }
    }
}

// Search and filter function
function filterItems(options = {}) {
    clearTimeout(rentySearchSkeletonTimer);
    if (!options.keepSkeleton) {
        setSearchSkeletonLoading(false);
    }
    
    // Reset page to 1 unless explicitly requested to keep page
    if (options.resetPage !== false) {
        rentyCurrentPage = 1;
    }
    
    const query = document.getElementById('search-input') ? document.getElementById('search-input').value : '';
    const parsedSearch = parseNaturalSearch(query);
    const normalizedQuery = normalizeText(query);
    const filterPrice = document.getElementById('filter-price') ? document.getElementById('filter-price').value : 'all';
    const filterRating = document.getElementById('filter-rating') ? document.getElementById('filter-rating').value : 'all';
    const distanceSlider = document.getElementById('distance-slider');
    const filterDistance = distanceSlider ? parseFloat(distanceSlider.value) : 3.0;

    // Kiểm tra khoảng giá tùy chỉnh Min - Max
    const minPriceInput = document.getElementById('filter-price-min');
    const maxPriceInput = document.getElementById('filter-price-max');
    const priceErrorEl = document.getElementById('filter-price-error');
    const priceErrorTextEl = document.getElementById('filter-price-error-text');

    const rawMin = minPriceInput ? minPriceInput.value.trim() : '';
    const rawMax = maxPriceInput ? maxPriceInput.value.trim() : '';
    const isDigitsOnly = val => /^\d+$/.test(val);

    // Bẫy lỗi ERR_21_03: Giá phòng chỉ được nhập số
    if ((rawMin !== '' && !isDigitsOnly(rawMin)) || (rawMax !== '' && !isDigitsOnly(rawMax))) {
        if (rawMin !== '' && !isDigitsOnly(rawMin) && minPriceInput) {
            minPriceInput.classList.add('border-rose-500', 'ring-1', 'ring-rose-500');
        }
        if (rawMax !== '' && !isDigitsOnly(rawMax) && maxPriceInput) {
            maxPriceInput.classList.add('border-rose-500', 'ring-1', 'ring-rose-500');
        }
        if (priceErrorTextEl) {
            priceErrorTextEl.textContent = 'Giá phòng và diện tích chỉ được nhập số.';
        }
        if (priceErrorEl) priceErrorEl.classList.remove('hidden');
        return;
    }

    const minPriceVal = rawMin !== '' ? parseInt(rawMin, 10) : null;
    const maxPriceVal = rawMax !== '' ? parseInt(rawMax, 10) : null;

    // Bẫy lỗi ERR_21_01: Min > Max
    if (minPriceVal !== null && maxPriceVal !== null && minPriceVal > maxPriceVal) {
        if (minPriceInput) minPriceInput.classList.add('border-rose-500', 'ring-1', 'ring-rose-500');
        if (maxPriceInput) maxPriceInput.classList.add('border-rose-500', 'ring-1', 'ring-rose-500');
        if (priceErrorTextEl) {
            priceErrorTextEl.textContent = 'Khoảng giá tìm kiếm không hợp lệ (Giá tối thiểu phải nhỏ hơn giá tối đa)';
        }
        if (priceErrorEl) priceErrorEl.classList.remove('hidden');
        return; // Dừng lọc khi khoảng giá không hợp lệ
    } else {
        if (minPriceInput) minPriceInput.classList.remove('border-rose-500', 'ring-1', 'ring-rose-500');
        if (maxPriceInput) maxPriceInput.classList.remove('border-rose-500', 'ring-1', 'ring-rose-500');
        if (priceErrorEl) priceErrorEl.classList.add('hidden');
    }

    const petEl = document.getElementById('tag-pets');
    const petChecked = petEl ? petEl.checked : false;
    const loftEl = document.getElementById('tag-loft');
    const loftChecked = loftEl ? loftEl.checked : false;
    const balconyEl = document.getElementById('tag-balcony');
    const balconyChecked = balconyEl ? balconyEl.checked : false;
    const wcChecked = document.getElementById('tag-wc') ? document.getElementById('tag-wc').checked : false;
    const acChecked = document.getElementById('tag-ac') ? document.getElementById('tag-ac').checked : false;
    const washerChecked = document.getElementById('tag-washer') ? document.getElementById('tag-washer').checked : false;
    const fridgeChecked = document.getElementById('tag-fridge') ? document.getElementById('tag-fridge').checked : false;
    const hideRented = document.getElementById('hide-rented-toggle') ? document.getElementById('hide-rented-toggle').checked : false;

    setSearchSkeletonLoading(false);

    let matches = [];

    document.querySelectorAll('.room-item-card').forEach(card => {
        const title = (card.getAttribute('data-title') || '').toLowerCase();
        const price = parseInt(card.getAttribute('data-price') || 0);
        const rating = parseFloat(card.getAttribute('data-rating') || 0);
        const distance = parseFloat(card.getAttribute('data-distance') || 0);
        const pets = card.getAttribute('data-pets') === 'true';
        const loft = card.getAttribute('data-loft') === 'true';
        const balcony = card.getAttribute('data-balcony') === 'true';
        const wc = card.getAttribute('data-wc') === 'true';
        const status = card.getAttribute('data-status');
        const searchableText = normalizeText(
            `${card.getAttribute('data-title') || ''} ` +
            `${card.getAttribute('data-area-name') || ''} ` +
            `${card.getAttribute('data-address') || ''} ` +
            `${card.getAttribute('data-location-desc') || ''} ` +
            `${card.getAttribute('data-space-desc') || ''} ` +
            `${card.getAttribute('data-scenery-desc') || ''} ` +
            `${card.textContent || ''}`
        );

        let matchesQuery = true;
        if (normalizedQuery.trim() !== '') {
            const cleanQuery = normalizedQuery
                .replace(/^(gan|o|tim|cho thue|khu vuc|xung quanh)\s+/g, '')
                .replace(/\b(gan|o|tim|cho thue|khu vuc|xung quanh)\b/g, '')
                .replace(/\s+/g, ' ')
                .trim();

            const containsFullQuery = searchableText.includes(cleanQuery);

            const stopWords = [
                'tim', 'phong', 'tro', 'duoi', 'o', 'gan', 'dai', 'hoc', 'trieu', 'tr', 'gia',
                'co', 'khong', 'cho', 'thue', 'can', 'ho', 'va', 'voi', 'trong', 'ngoai',
                'dep', 're', 'nha', 'chinh', 'chu', 'thang'
            ];

            const importantTerms = (parsedSearch.keywords || []).filter(term => !stopWords.includes(term));

            let matchesAllTerms = false;
            if (importantTerms.length === 0) {
                matchesAllTerms = true;
            } else {
                const matchCount = importantTerms.filter(term => searchableText.includes(term)).length;
                const matchRatio = matchCount / importantTerms.length;
                matchesAllTerms = importantTerms.length <= 2 ? (matchRatio === 1) : (matchRatio >= 0.7);
            }

            matchesQuery = containsFullQuery || matchesAllTerms;
        }

        let matchesPrice = true;
        if (minPriceVal !== null) {
            matchesPrice = matchesPrice && (price >= minPriceVal);
        }
        if (maxPriceVal !== null) {
            matchesPrice = matchesPrice && (price <= maxPriceVal);
        } else if (filterPrice !== 'all') {
            matchesPrice = matchesPrice && (price <= parseInt(filterPrice));
        }
        if (parsedSearch.maxPrice) {
            matchesPrice = matchesPrice && price <= parsedSearch.maxPrice;
        }

        let matchesRating = true;
        if (filterRating !== 'all') {
            matchesRating = rating >= parseFloat(filterRating);
        }

        let matchesDistance = true;
        if (distanceSlider) {
            matchesDistance = distance <= filterDistance;
        }

        let matchesTags = true;
        if (petChecked && !pets) matchesTags = false;
        if (loftChecked && !loft) matchesTags = false;
        if (balconyChecked && !balcony) matchesTags = false;
        if (wcChecked && !wc) matchesTags = false;
        if (acChecked && !(searchableText.includes('may lanh') || searchableText.includes('dieu hoa'))) matchesTags = false;
        if (washerChecked && !searchableText.includes('may giat')) matchesTags = false;
        if (fridgeChecked && !searchableText.includes('tu lanh')) matchesTags = false;

        let matchesLocation = true;
        if (parsedSearch.locations && parsedSearch.locations.length > 0) {
            matchesLocation = parsedSearch.locations.some(location => searchableText.includes(location));
        }

        let matchesStatus = true;
        if (hideRented && status !== 'empty') {
            matchesStatus = false;
        }

        if (matchesQuery && matchesPrice && matchesRating && matchesDistance && matchesTags && matchesLocation && matchesStatus) {
            matches.push(card);
            showMarker(card.getAttribute('data-room-id'));
        } else {
            card.classList.add('hidden');
            hideMarker(card.getAttribute('data-room-id'));
        }
    });

    // Sắp xếp danh sách phòng
    const sortBy = document.getElementById('sort_by') ? document.getElementById('sort_by').value : 'default';
    if (sortBy === 'price_asc') {
        matches.sort((a, b) => parseInt(a.getAttribute('data-price') || 0) - parseInt(b.getAttribute('data-price') || 0));
    } else if (sortBy === 'price_desc') {
        matches.sort((a, b) => parseInt(b.getAttribute('data-price') || 0) - parseInt(a.getAttribute('data-price') || 0));
    } else if (sortBy === 'newest') {
        matches.sort((a, b) => parseInt(b.getAttribute('data-room-id') || 0) - parseInt(a.getAttribute('data-room-id') || 0));
    }

    const roomsGrid = document.getElementById('rooms-grid');
    if (roomsGrid) {
        matches.forEach(card => roomsGrid.appendChild(card));
    }

    const matchesCount = matches.length;
    const resultsCountEl = document.getElementById('results-count');
    if (resultsCountEl) {
        resultsCountEl.textContent = `Tìm thấy ${matchesCount} phòng`;
    }

    // Xử lý Empty State ERR_21_04
    const emptyState = document.getElementById('smart-search-empty-state');
    if (matchesCount === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        if (roomsGrid) roomsGrid.classList.add('hidden');
        const emptyTitleEl = document.getElementById('smart-search-empty-title');
        const emptyCodeTextEl = document.getElementById('smart-search-empty-code-text') || document.getElementById('smart-search-empty-code');
        const q = (query || '').trim();
        if (emptyCodeTextEl) {
            if (q) {
                emptyCodeTextEl.textContent = `Lỗi: Không tìm thấy kết quả phù hợp cho "${q}" (Mã: ERR_21_04)`;
                if (emptyTitleEl) emptyTitleEl.textContent = `Không tìm thấy phòng nào khớp với "${q}".`;
            } else {
                emptyCodeTextEl.textContent = 'Lỗi: Không tìm thấy phòng phù hợp với bộ lọc bạn đã chọn (Mã: ERR_21_04)';
                if (emptyTitleEl) emptyTitleEl.textContent = 'Không tìm thấy phòng nào phù hợp với bộ lọc bạn đã chọn.';
            }
        }
        renderPaginationControls(0);
    } else {
        if (emptyState) emptyState.classList.add('hidden');
        if (roomsGrid) roomsGrid.classList.remove('hidden');

        // Phân trang 9 phòng/trang
        const totalPages = Math.ceil(matchesCount / rentyItemsPerPage);
        if (rentyCurrentPage > totalPages) {
            rentyCurrentPage = totalPages || 1;
        }
        if (rentyCurrentPage < 1) {
            rentyCurrentPage = 1;
        }

        const startIndex = (rentyCurrentPage - 1) * rentyItemsPerPage;
        const endIndex = startIndex + rentyItemsPerPage;

        matches.forEach((card, index) => {
            if (index >= startIndex && index < endIndex) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });

        renderPaginationControls(totalPages);
    }

    // Fit map bounds to visible markers
    if (rentyMap && typeof L !== 'undefined') {
        const visibleLatLngs = [];
        Object.values(rentyMarkers).forEach(marker => {
            if (rentyMap.hasLayer(marker)) {
                visibleLatLngs.push(marker.getLatLng());
            }
        });
        if (visibleLatLngs.length > 0) {
            const bounds = L.latLngBounds(visibleLatLngs);
            rentyMap.fitBounds(bounds, { maxZoom: 14, padding: [30, 30] });
        }
    }
}

let rentySmartSearchDebounce = null;
let currentSmartSearchCorrection = null;

// Gửi yêu cầu tìm kiếm thông minh tới API với debounce và hiển thị trực tiếp
function fetchLiveSmartSearch(query) {
    clearTimeout(rentySmartSearchDebounce);

    const didYouMeanBox = document.getElementById('renty-did-you-mean-box');
    const didYouMeanBtn = document.getElementById('renty-did-you-mean-btn');
    const liveResultsSection = document.getElementById('renty-live-results-section');
    const liveRoomsList = document.getElementById('renty-live-rooms-list');
    const liveCount = document.getElementById('renty-live-count');
    const loadingEl = document.getElementById('renty-search-loading');
    const defaultChips = document.getElementById('renty-default-chips-section');

    const trimmed = (query || '').trim();

    if (trimmed.length < 2) {
        currentSmartSearchCorrection = null;
        if (didYouMeanBox) didYouMeanBox.classList.add('hidden');
        if (liveResultsSection) liveResultsSection.classList.add('hidden');
        if (loadingEl) loadingEl.classList.add('hidden');
        if (defaultChips) defaultChips.classList.remove('hidden');
        return;
    }

    if (loadingEl) loadingEl.classList.remove('hidden');

    rentySmartSearchDebounce = setTimeout(async () => {
        try {
            const startTime = performance.now();
            // Gọi song song API autocomplete (Search Engine) và Smart Search (Correction)
            const [resAuto, resSmart] = await Promise.all([
                fetch(`/api/renty/rooms/autocomplete?q=${encodeURIComponent(trimmed)}&limit=5`).catch(() => null),
                fetch(`/api/renty/rooms/smart-search?q=${encodeURIComponent(trimmed)}&limit=5`).catch(() => null)
            ]);

            const autoData = resAuto ? await resAuto.json() : null;
            const data = resSmart ? await resSmart.json() : null;
            const latency = Math.round(performance.now() - startTime);

            if (loadingEl) loadingEl.classList.add('hidden');

            // Hiển thị độ trễ Search Engine nếu có
            const heroLatencyEl = document.getElementById('hero-live-latency');
            if (heroLatencyEl) heroLatencyEl.textContent = `${latency}ms (${autoData?.source || 'engine'})`;

            // 1. Xử lý hiển thị "Có phải bạn muốn tìm..." khi phát hiện gõ nhầm / sai chính tả
            if (data && data.has_correction && data.did_you_mean && data.did_you_mean.toLowerCase() !== trimmed.toLowerCase()) {
                currentSmartSearchCorrection = data.did_you_mean;
                if (didYouMeanBox && didYouMeanBtn) {
                    didYouMeanBtn.textContent = data.did_you_mean;
                    didYouMeanBox.classList.remove('hidden');
                }
                const heroSugBox = document.getElementById('smart-search-suggestion');
                const heroSugBtn = document.getElementById('did-you-mean-btn');
                if (heroSugBox && heroSugBtn) {
                    heroSugBtn.textContent = data.did_you_mean;
                    heroSugBox.classList.remove('hidden');
                }
            } else {
                currentSmartSearchCorrection = null;
                if (didYouMeanBox) didYouMeanBox.classList.add('hidden');
                document.getElementById('smart-search-suggestion')?.classList.add('hidden');
            }

            // 2. Danh sách phòng gợi ý từ Search Engine hoặc Smart Search
            const candidateRooms = (autoData && autoData.rooms && autoData.rooms.length > 0) 
                ? autoData.rooms 
                : (data && data.rooms ? data.rooms : []);

            // 2.1 Cập nhật danh sách ở Header search panel
            if (liveResultsSection && liveRoomsList) {
                if (candidateRooms.length > 0) {
                    if (liveCount) liveCount.textContent = candidateRooms.length;
                    liveRoomsList.innerHTML = candidateRooms.map(room => `
                        <a href="/renty/rooms/${room.id}" class="renty-live-room-item flex items-center justify-between p-2 rounded-xl bg-slate-900/80 hover:bg-slate-850 border border-slate-800 hover:border-emerald-500/50 transition-all group">
                            <div class="flex items-center gap-2.5 overflow-hidden">
                                <img src="${room.thumbnail || room.cover_image || '/images/room-placeholder.jpg'}" alt="" class="w-10 h-10 rounded-lg object-cover shrink-0 border border-slate-700/60" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80'">
                                <div class="truncate text-left">
                                    <h5 class="text-xs font-bold text-slate-200 group-hover:text-emerald-400 transition-colors truncate">Phòng ${escapeHtml(room.room_number || '')} - ${escapeHtml(room.building_name || '')}</h5>
                                    <p class="text-[10px] text-slate-400 truncate flex items-center gap-1.5 mt-0.5">
                                        <span><i class="fa-solid fa-location-dot text-[8px] text-emerald-400"></i> ${escapeHtml(room.building_address || room.address || '')}</span>
                                        <span>•</span>
                                        <span>${room.area ? room.area + ' m²' : (room.area_formatted || '')}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0 pl-2">
                                <span class="text-xs font-black text-emerald-400 block">${room.price_formatted || (room.price ? Number(room.price).toLocaleString('vi-VN') + ' đ' : '')}</span>
                                <span class="text-[9px] text-amber-400 font-bold"><i class="fa-solid fa-star text-[8px]"></i> ${room.rating_avg || room.rating || 5.0}</span>
                            </div>
                        </a>
                    `).join('');
                    liveResultsSection.classList.remove('hidden');
                } else {
                    liveRoomsList.innerHTML = `
                        <div class="py-3 px-2 text-center text-[11px] text-slate-400 bg-slate-900/40 rounded-xl border border-slate-800/60">
                            <i class="fa-solid fa-magnifying-glass text-slate-500 mb-1 block"></i>
                            Không có kết quả khớp. Thử tìm kiếm với từ khóa khác.
                        </div>
                    `;
                    liveResultsSection.classList.remove('hidden');
                }
            }

            // 2.2 Cập nhật danh sách Autocomplete cho Hero Search Input
            const heroDropdown = document.getElementById('hero-live-search-dropdown');
            const heroList = document.getElementById('hero-live-rooms-list');
            if (heroDropdown && heroList) {
                if (candidateRooms.length > 0) {
                    heroList.innerHTML = candidateRooms.map(room => `
                        <a href="/renty/rooms/${room.id}" class="flex items-center justify-between p-2.5 rounded-xl bg-slate-900/90 hover:bg-slate-800/90 border border-slate-800/80 hover:border-emerald-500/50 transition-all group">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <img src="${room.thumbnail || room.cover_image || '/images/room-placeholder.jpg'}" alt="" class="w-11 h-11 rounded-lg object-cover shrink-0 border border-slate-700/60" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80'">
                                <div class="truncate text-left">
                                    <div class="text-xs font-bold text-slate-200 group-hover:text-emerald-400 transition-colors truncate">
                                        Phòng ${escapeHtml(room.room_number || '')} • ${escapeHtml(room.building_name || '')}
                                    </div>
                                    <div class="text-[10px] text-slate-400 truncate flex items-center gap-1.5 mt-0.5">
                                        <i class="fa-solid fa-location-dot text-emerald-400 text-[9px]"></i>
                                        <span>${escapeHtml(room.building_address || room.address || '')}</span>
                                        <span>•</span>
                                        <span class="text-slate-300 font-semibold">${room.area ? room.area + ' m²' : ''}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0 pl-3">
                                <span class="text-xs font-extrabold text-emerald-400 block">${room.price_formatted || (room.price ? Number(room.price).toLocaleString('vi-VN') + ' đ' : '')}</span>
                                <span class="text-[9px] text-amber-400 font-bold"><i class="fa-solid fa-star text-[8px]"></i> ${room.rating_avg || 5.0}</span>
                            </div>
                        </a>
                    `).join('');
                    heroDropdown.classList.remove('hidden');
                } else {
                    heroDropdown.classList.add('hidden');
                }
            }

            // 3. Nếu đang ở trang xem phòng, kích hoạt lại filterItems
            if (document.getElementById('rooms-grid')) {
                filterItems({ keepSkeleton: true, resetPage: false });
            }

        } catch (err) {
            console.warn('Smart search request failed:', err);
            if (loadingEl) loadingEl.classList.add('hidden');
        }
    }, 300); // Debounce chuẩn 300ms theo đúng đặc tả hệ thống
}

// Hàm lắng nghe input Hero Search với Debounce 300ms
let heroLiveSearchTimer = null;
function handleHeroLiveSearch(e) {
    const val = e.target.value;
    const heroDropdown = document.getElementById('hero-live-search-dropdown');

    if (e.key === 'Escape') {
        if (heroDropdown) heroDropdown.classList.add('hidden');
        return;
    }

    if (e.key === 'Enter') {
        if (heroDropdown) heroDropdown.classList.add('hidden');
        filterItems();
        return;
    }

    // Đồng bộ giá trị lên search-input nếu có
    const navInput = document.getElementById('search-input');
    if (navInput && navInput !== e.target) {
        navInput.value = val;
    }

    // Áp dụng Debounce 300ms
    fetchLiveSmartSearch(val);
}
window.handleHeroLiveSearch = handleHeroLiveSearch;

// Ẩn dropdown khi click ra ngoài màn hình
document.addEventListener('click', (e) => {
    const heroDropdown = document.getElementById('hero-live-search-dropdown');
    const heroInput = document.getElementById('hero-search-input');
    if (heroDropdown && !heroDropdown.contains(e.target) && e.target !== heroInput) {
        heroDropdown.classList.add('hidden');
    }
});

// Áp dụng từ gợi ý sửa lỗi (Did you mean)
function applySearchCorrection() {
    if (!currentSmartSearchCorrection) return;

    const navInput = document.getElementById('search-input');
    const heroInput = document.getElementById('hero-search-input');

    if (navInput) navInput.value = currentSmartSearchCorrection;
    if (heroInput) heroInput.value = currentSmartSearchCorrection;

    // Ẩn hộp gợi ý sau khi đã click sửa
    document.getElementById('renty-did-you-mean-box')?.classList.add('hidden');

    if (!document.getElementById('rooms-grid')) {
        window.location.href = '/renty?search=' + encodeURIComponent(currentSmartSearchCorrection);
    } else {
        filterItems();
        fetchLiveSmartSearch(currentSmartSearchCorrection);
    }
}
window.applySearchCorrection = applySearchCorrection;

function openRentySearchSuggestions() {
    document.getElementById('renty-search-suggestions')?.classList.remove('hidden');
    document.getElementById('renty-search-panel')?.classList.add('is-search-active');
    document.getElementById('renty-search-backdrop')?.classList.add('is-active');

    const input = document.getElementById('search-input');
    if (input && input.value.trim().length >= 2) {
        fetchLiveSmartSearch(input.value);
    }
}

function blurRentySearch() {
    document.getElementById('renty-search-suggestions')?.classList.add('hidden');
    document.getElementById('renty-search-panel')?.classList.remove('is-search-active');
    document.getElementById('renty-search-backdrop')?.classList.remove('is-active');
    document.getElementById('search-input')?.blur();
}

// Tự động đóng dropdown gợi ý khi click ra ngoài thanh tìm kiếm
document.addEventListener('click', (e) => {
    const searchPanel = document.getElementById('renty-search-panel');
    const suggestions = document.getElementById('renty-search-suggestions');
    if (searchPanel && suggestions && !searchPanel.contains(e.target)) {
        suggestions.classList.add('hidden');
        searchPanel.classList.remove('is-search-active');
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const suggestions = document.getElementById('renty-search-suggestions');
        if (suggestions) suggestions.classList.add('hidden');
    }
});

function applySearchSuggestion(query) {
    const input = document.getElementById('search-input');
    const heroInput = document.getElementById('hero-search-input');
    if (input) {
        input.value = query;
        if (heroInput) heroInput.value = query;
        if (!document.getElementById('rooms-grid')) {
            window.location.href = '/renty?search=' + encodeURIComponent(query);
        } else {
            input.focus();
            openRentySearchSuggestions();
            filterItems();
            fetchLiveSmartSearch(query);
        }
    }
}

function triggerRentySearch() {
    const input = document.getElementById('search-input');
    if (!input) return;
    const value = input.value;
    if (!document.getElementById('rooms-grid')) {
        window.location.href = '/renty?search=' + encodeURIComponent(value);
    } else {
        filterItems();
        blurRentySearch();
    }
}
window.triggerRentySearch = triggerRentySearch;

function handleSearchInput(e) {
    const query = e.target.value;

    // Kích hoạt tìm kiếm thông minh live với backend API
    fetchLiveSmartSearch(query);

    if (!document.getElementById('rooms-grid')) {
        if (e.key === 'Enter') {
            window.location.href = '/renty?search=' + encodeURIComponent(query);
        }
    } else {
        filterItems();
        if (e.key === 'Enter') {
            blurRentySearch();
        }
    }
}

// ── BỔ SUNG: CÁC HÀM NÂNG CẤP BỘ LỌC THÔNG MINH CHO HUỲNH VĂN VĨNH EM ──

let rentyFilterDebounceTimer = null;

function debouncedFilterItems(delay = 300) {
    clearTimeout(rentyFilterDebounceTimer);
    rentyFilterDebounceTimer = setTimeout(() => {
        filterItems();
    }, delay);
}
window.debouncedFilterItems = debouncedFilterItems;

function handlePricePresetChange(preset) {
    const minInput = document.getElementById('filter-price-min');
    const maxInput = document.getElementById('filter-price-max');
    const priceSelect = document.getElementById('filter-price');

    if (minInput) minInput.value = '';
    if (maxInput) {
        maxInput.value = preset !== 'all' ? preset : '';
    }

    // Xóa lỗi viền đỏ nếu có
    const priceErrorEl = document.getElementById('filter-price-error');
    if (minInput) minInput.classList.remove('border-rose-500', 'ring-1', 'ring-rose-500');
    if (maxInput) maxInput.classList.remove('border-rose-500', 'ring-1', 'ring-rose-500');
    if (priceErrorEl) priceErrorEl.classList.add('hidden');

    filterItems();
}
window.handlePricePresetChange = handlePricePresetChange;

function resetAllFilters() {
    // 1. Reset các ô tìm kiếm
    const searchInput = document.getElementById('search-input');
    const heroInput = document.getElementById('hero-search-input');
    if (searchInput) searchInput.value = '';
    if (heroInput) heroInput.value = '';

    // 2. Reset khoảng giá
    const filterPrice = document.getElementById('filter-price');
    const minInput = document.getElementById('filter-price-min');
    const maxInput = document.getElementById('filter-price-max');
    const priceErrorEl = document.getElementById('filter-price-error');
    if (filterPrice) filterPrice.value = 'all';
    if (minInput) {
        minInput.value = '';
        minInput.classList.remove('border-rose-500', 'ring-1', 'ring-rose-500');
    }
    if (maxInput) {
        maxInput.value = '';
        maxInput.classList.remove('border-rose-500', 'ring-1', 'ring-rose-500');
    }
    if (priceErrorEl) priceErrorEl.classList.add('hidden');

    // 3. Reset đánh giá
    const filterRating = document.getElementById('filter-rating');
    if (filterRating) filterRating.value = 'all';

    // 4. Reset khoảng cách Slider
    const distSlider = document.getElementById('distance-slider');
    if (distSlider) {
        distSlider.value = 3.0;
        if (typeof updateDistanceSlider === 'function') {
            updateDistanceSlider(3.0);
        }
    }

    // 5. Reset các tiện ích checkbox & visual buttons
    ['pets', 'loft', 'balcony', 'wc', 'ac', 'washer', 'fridge'].forEach(key => {
        const checkbox = document.getElementById(`tag-${key}`);
        if (checkbox) checkbox.checked = false;
        const vbtn = document.getElementById(`vbtn-${key}`);
        if (vbtn) vbtn.classList.remove('active');
    });

    // 6. Reset công tắc ẩn phòng đã thuê
    const hideRented = document.getElementById('hide-rented-toggle');
    if (hideRented) hideRented.checked = false;

    // 7. Ẩn gợi ý sửa lỗi & live results
    closeSuggestion();
    document.getElementById('renty-did-you-mean-box')?.classList.add('hidden');
    document.getElementById('renty-live-results-section')?.classList.add('hidden');

    // 8. Cập nhật lại URL sạch
    if (window.history && window.history.replaceState) {
        window.history.replaceState({}, '', window.location.pathname);
    }

    // 9. Chạy lại bộ lọc hiển thị đầy đủ
    filterItems();
}
window.resetAllFilters = resetAllFilters;

function applySuggestedQuery() {
    const btn = document.getElementById('did-you-mean-btn') || document.getElementById('renty-did-you-mean-btn');
    const suggested = btn ? btn.textContent.trim() : currentSmartSearchCorrection;
    if (!suggested) return;

    const navInput = document.getElementById('search-input');
    const heroInput = document.getElementById('hero-search-input');
    if (navInput) navInput.value = suggested;
    if (heroInput) heroInput.value = suggested;

    closeSuggestion();
    document.getElementById('renty-did-you-mean-box')?.classList.add('hidden');

    filterItems();
    fetchLiveSmartSearch(suggested);
}
window.applySuggestedQuery = applySuggestedQuery;

function closeSuggestion() {
    document.getElementById('smart-search-suggestion')?.classList.add('hidden');
}
window.closeSuggestion = closeSuggestion;

function syncFilterToUrl() {
    if (typeof window === 'undefined' || !window.history || !window.history.replaceState) return;
    const params = new URLSearchParams();
    const query = document.getElementById('search-input')?.value.trim() || document.getElementById('hero-search-input')?.value.trim();
    if (query) params.set('q', query);

    const minVal = document.getElementById('filter-price-min')?.value.trim();
    if (minVal) params.set('min_price', minVal);

    const maxVal = document.getElementById('filter-price-max')?.value.trim();
    if (maxVal) params.set('max_price', maxVal);

    const rating = document.getElementById('filter-rating')?.value;
    if (rating && rating !== 'all') params.set('rating', rating);

    const dist = document.getElementById('distance-slider')?.value;
    if (dist && parseFloat(dist) < 3.0) params.set('distance', dist);

    if (document.getElementById('tag-pets')?.checked) params.set('pets', '1');
    if (document.getElementById('tag-loft')?.checked) params.set('loft', '1');
    if (document.getElementById('tag-balcony')?.checked) params.set('balcony', '1');
    if (document.getElementById('tag-wc')?.checked) params.set('wc', '1');

    const newSearch = params.toString();
    const newUrl = newSearch ? `${window.location.pathname}?${newSearch}` : window.location.pathname;
    window.history.replaceState({}, '', newUrl);
}
window.syncFilterToUrl = syncFilterToUrl;

function initFilterFromUrl() {
    if (typeof window === 'undefined' || !window.location.search) return;
    const params = new URLSearchParams(window.location.search);
    let hasParam = false;

    const q = params.get('q') || params.get('search');
    if (q) {
        const hero = document.getElementById('hero-search-input');
        const nav = document.getElementById('search-input');
        if (hero) hero.value = q;
        if (nav) nav.value = q;
        hasParam = true;
    }

    const minP = params.get('min_price');
    if (minP && document.getElementById('filter-price-min')) {
        document.getElementById('filter-price-min').value = minP;
        hasParam = true;
    }

    const maxP = params.get('max_price');
    if (maxP && document.getElementById('filter-price-max')) {
        document.getElementById('filter-price-max').value = maxP;
        hasParam = true;
    }

    const rating = params.get('rating');
    if (rating && document.getElementById('filter-rating')) {
        document.getElementById('filter-rating').value = rating;
        hasParam = true;
    }

    const dist = params.get('distance');
    if (dist && document.getElementById('distance-slider')) {
        document.getElementById('distance-slider').value = dist;
        if (typeof updateDistanceSlider === 'function') updateDistanceSlider(dist);
        hasParam = true;
    }

    ['pets', 'loft', 'balcony', 'wc'].forEach(tag => {
        if (params.get(tag) === '1') {
            const el = document.getElementById(`tag-${tag}`);
            if (el) el.checked = true;
            const vbtn = document.getElementById(`vbtn-${tag}`);
            if (vbtn) vbtn.classList.add('active');
            hasParam = true;
        }
    });

    if (hasParam) {
        const drawer = document.getElementById('filter-drawer');
        if (drawer && (minP || maxP || rating || dist)) {
            drawer.classList.remove('hidden');
        }
        if (typeof filterItems === 'function') {
            filterItems({ keepSkeleton: true });
        }
    }
}
window.initFilterFromUrl = initFilterFromUrl;

function initSearchListeners() {
    document.addEventListener('click', (event) => {
        const panel = document.getElementById('renty-search-panel');
        if (panel && panel.classList.contains('is-search-active') && !panel.contains(event.target)) {
            blurRentySearch();
        }
    });

    // Tự động khôi phục bộ lọc từ URL param khi tải trang
    initFilterFromUrl();
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSearchListeners);
} else {
    initSearchListeners();
}

function subscribeEmptyNotification(event, roomId, roomTitle) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    document.getElementById('notify-room-id').value = roomId;
    document.getElementById('notify-room-title-display').textContent = roomTitle;
    document.getElementById('notify-contact-input').value = '';

    const modal = document.getElementById('notify-subscribe-modal');
    modal.classList.remove('hidden');
}

function closeNotifySubscribeModal() {
    document.getElementById('notify-subscribe-modal').classList.add('hidden');
}

function handleNotifySubscribeSubmit(event) {
    event.preventDefault();
    const roomTitle = document.getElementById('notify-room-title-display').textContent;
    const contactInput = document.getElementById('notify-contact-input').value.trim();

    if (!contactInput) return;

    closeNotifySubscribeModal();
    showCustomAlert('Đăng ký thành công!', `Đã kích hoạt chuông báo trống phòng thành công cho phòng "${roomTitle}". Chúng tôi sẽ gửi thông báo tới "${contactInput}" ngay khi phòng Sẵn sàng.`);
}

function showCustomAlert(title, message) {
    document.getElementById('custom-alert-title').textContent = title;
    document.getElementById('custom-alert-message').textContent = message;
    document.getElementById('custom-alert-modal').classList.remove('hidden');
}

function closeCustomAlert() {
    document.getElementById('custom-alert-modal').classList.add('hidden');
}

function toggleVisualFilter(key) {
    const btn = document.getElementById(`vbtn-${key}`);
    const checkbox = document.getElementById(`tag-${key}`);
    if (!btn || !checkbox) return;

    checkbox.checked = !checkbox.checked;
    btn.classList.toggle('active', checkbox.checked);
    filterItems();
}

function syncFromCheckbox(key) {
    const btn = document.getElementById(`vbtn-${key}`);
    const checkbox = document.getElementById(`tag-${key}`);
    if (!btn || !checkbox) return;

    btn.classList.toggle('active', checkbox.checked);
    filterItems();
}

function initRentyDashboard() {
    // Only initialize if we are on the Renty dashboard page (contains map container or mode toggle)
    if (!document.getElementById('renty-interactive-map') && !document.getElementById('view-mode-map-btn')) {
        return;
    }

    renderViewedRooms();

    // Set initial view mode, default to 'grid'
    const savedMode = localStorage.getItem('rentry_view_mode') || 'grid';
    setViewMode(savedMode);

    // Read search param from URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = urlParams.get('search');
    if (searchQuery) {
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.value = searchQuery;
        }
    }

    filterItems(); // Run initial filter to apply checked state of pets & balcony
}
if (document.readyState === 'loading') {
    window.addEventListener('DOMContentLoaded', initRentyDashboard);
} else {
    initRentyDashboard();
}

function openHotAreasModal() {
    document.getElementById('hot-areas-modal').classList.remove('hidden');
}

function closeHotAreasModal() {
    document.getElementById('hot-areas-modal').classList.add('hidden');
}

function selectHotArea(areaQuery) {
    document.getElementById('search-input').value = areaQuery;
    filterItems();
    closeHotAreasModal();
    // Highlight search input briefly
    const input = document.getElementById('search-input');
    input.focus();
    input.classList.add('ring-2', 'ring-emerald-500');
    setTimeout(() => {
        input.classList.remove('ring-2', 'ring-emerald-500');
    }, 1000);
}

function openNewReviewsModal() {
    document.getElementById('new-reviews-modal').classList.remove('hidden');
}

function closeNewReviewsModal() {
    document.getElementById('new-reviews-modal').classList.add('hidden');
}

// Database of Q&A comments
const qaCommentsData = {
    0: [
        { author: 'Hoàng Anh', meta: 'Sinh viên Sư Phạm', text: 'Khu này bể ngầm hơi nhỏ nên nếu mất nước chung thì cúp tầm nửa ngày thôi bạn, chủ nhà có bể dự phòng nhé.', is_best: true, time: '2 giờ trước' },
        { author: 'Trần Nam', meta: 'Người dùng ẩn danh', text: 'Chính xác luôn, đợt năm ngoái nắng nóng cúp nước liên tục cơ mà nhà này vẫn có nước dùng tạm.', is_best: false, time: '1 giờ trước' },
        { author: 'Ngọc Mai', meta: 'Sinh viên Quốc Gia', text: 'Chủ nhà có báo trước lịch cắt nước không bạn ơi?', is_best: false, time: '30 phút trước' }
    ],
    1: [
        { author: 'Khánh Linh', meta: 'Ngoại Thương', text: 'Chủ nhà ngõ này hiền lắm, giữ xe free mà 11h đêm khóa cổng thôi. Không chung đụng gì nhiều đâu em.', is_best: true, time: '5 giờ trước' },
        { author: 'Duy Bách', meta: 'Người dùng ẩn danh', text: 'Có quy định giờ giấc nghiêm ngặt không chị? Bạn bè tới chơi có phải xin phép không?', is_best: false, time: '3 giờ trước' }
    ],
    2: [
        { author: 'Minh Đức', meta: 'Bách Khoa', text: 'Tầm giá này ở ngõ Tự Do hơi hiếm ban công rộng, bạn chịu khó lùi ra Trần Đại Nghĩa hoặc Lê Thanh Nghị thì nhiều phòng đẹp hơn nha.', is_best: true, time: '1 ngày trước' },
        { author: 'Văn Hải', meta: 'Xây Dựng', text: 'Ngõ Tự Do phòng bé tí mà đắt lắm, khuyên thật nên ra Lê Thanh Nghị tìm phòng rộng hơn.', is_best: false, time: '18 giờ trước' }
    ],
    3: [
        { author: 'Thu Trang', meta: 'Báo Chí', text: 'Đầu ngõ có chốt dân phòng với đèn đường sáng trưng tới sáng luôn bạn, yên tâm cực kỳ nha.', is_best: true, time: '3 ngày trước' },
        { author: 'Hương Giang', meta: 'Sư Phạm', text: 'Mình con gái ở đây 2 năm rồi, đi làm thêm về muộn 11h đêm suốt thấy an toàn lắm.', is_best: false, time: '2 ngày trước' }
    ],
    4: [
        { author: 'Hoàng Long', meta: 'ĐH Ngoại Thương', text: 'Mấy ngõ như ngõ 80 hoặc ngõ 157 Chùa Láng nhiều chung cư mini mới xây lắm bạn ơi. Có hầm xe rộng rãi nhưng nhớ hỏi kỹ xem có tính thêm phí gửi xe không nha.', is_best: true, time: '5 giờ trước' },
        { author: 'Quốc Anh', meta: 'Người dùng ẩn danh', text: 'Ngõ 80 Chùa Láng công nhận nhiều nhà đẹp thật, cơ mà đỗ xe oto hơi khó.', is_best: false, time: '4 giờ trước' }
    ],
    5: [
        { author: 'Thu Thảo', meta: 'Học viện Bưu chính', text: 'Đúng là mạn này thỉnh thoảng nước hơi yếu thật ấy, nhất là mấy khu tập thể cũ. Bạn nên mua thêm một đầu lọc thô lắp ở vòi lavabo với vòi tắm cho an tâm.', is_best: true, time: '1 ngày trước' },
        { author: 'Đức Huy', meta: 'Mật Mã', text: 'Khu Phùng Khoang nước sinh hoạt có vị hơi lợ, nên dùng máy lọc nước RO để nấu ăn nha mọi người.', is_best: false, time: '20 giờ trước' }
    ]
};

let activeQaIndex = null;
let activeQaButton = null;

function openQaCommentsModal(button) {
    activeQaButton = button;
    activeQaIndex = button.getAttribute('data-qa-index');
    const question = button.getAttribute('data-qa-question');
    const area = button.getAttribute('data-qa-area');
    const time = button.getAttribute('data-qa-time');

    // Populate modal fields
    document.getElementById('qa-modal-area').textContent = area;
    document.getElementById('qa-modal-time').textContent = time;
    document.getElementById('qa-modal-question').textContent = question;

    // Reset input
    document.getElementById('qa-reply-input').value = '';

    // Load comments
    renderQaComments();

    // Show modal
    document.getElementById('qa-comments-modal').classList.remove('hidden');
}

function closeQaCommentsModal() {
    document.getElementById('qa-comments-modal').classList.add('hidden');
}

function renderQaComments() {
    const list = document.getElementById('qa-modal-comments-list');
    if (!list) return;
    list.innerHTML = '';

    const comments = qaCommentsData[activeQaIndex] || [];
    comments.forEach(comment => {
        const card = document.createElement('div');
        const isBest = comment.is_best;
        const borderClass = isBest ? 'border-emerald-500/20 bg-emerald-500/5' : 'border-slate-800/80 bg-slate-900/60';
        const badgeHtml = isBest ? '<span class="text-[8px] text-emerald-400 bg-emerald-500/10 border border-emerald-500/15 px-1.5 py-0.5 rounded font-extrabold uppercase tracking-wider">Best Reply</span>' : '';

        card.className = `p-4 rounded-2xl border ${borderClass} space-y-2 relative overflow-hidden transition-all duration-300`;
        card.innerHTML = `
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[10px] font-bold text-slate-200 block">
                        <i class="fa-solid fa-user-circle text-teal-400 mr-1.5"></i>${comment.author}
                    </span>
                    <span class="text-[8px] text-indigo-400 font-extrabold uppercase mt-0.5 tracking-wider block">
                        ${comment.meta}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    ${badgeHtml}
                    <span class="text-[8px] text-slate-650 font-bold shrink-0">
                        <i class="fa-regular fa-clock mr-1"></i>${comment.time}
                    </span>
                </div>
            </div>
            <p class="text-xs text-slate-350 italic pl-1 leading-relaxed border-l-2 border-slate-850">
                "${escapeHtml(comment.text)}"
            </p>
        `;
        list.appendChild(card);
    });
}

function submitQaReply(event) {
    event.preventDefault();
    const input = document.getElementById('qa-reply-input');
    if (!input) return;
    const text = input.value.trim();
    if (!text) return;

    if (!qaCommentsData[activeQaIndex]) {
        qaCommentsData[activeQaIndex] = [];
    }

    // Add comment to database
    const newComment = {
        author: 'Người dùng ẩn danh',
        meta: 'Thành viên cộng đồng',
        text: text,
        is_best: false,
        time: 'Vừa xong'
    };
    qaCommentsData[activeQaIndex].push(newComment);

    // Render new comment
    renderQaComments();

    // Scroll last comment into view
    const list = document.getElementById('qa-modal-comments-list');
    if (list && list.lastChild) {
        list.lastChild.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Clear input
    input.value = '';

    // Update comment count on Q&A card
    if (activeQaButton) {
        const countSpan = activeQaButton.querySelector('.qa-comment-count');
        if (countSpan) {
            const newCount = qaCommentsData[activeQaIndex].length;
            countSpan.textContent = `${newCount} bình luận`;
        }
    }

    // Show toast notice
    alert('Cảm ơn bạn đã gửi ý kiến');
}

function voteQa(button, direction) {
    const parent = button.parentElement;
    const countSpan = parent.querySelector('.qa-vote-count');
    if (!countSpan) return;

    let currentVotes = parseInt(countSpan.textContent) || 0;
    const activeUp = button.classList.contains('voted-up');
    const activeDown = button.classList.contains('voted-down');

    if (direction === 'up') {
        const downBtn = parent.querySelector('button[aria-label="Downvote"]');
        if (activeUp) {
            button.classList.remove('voted-up');
            countSpan.textContent = currentVotes - 1;
        } else {
            button.classList.add('voted-up');
            if (downBtn && downBtn.classList.contains('voted-down')) {
                downBtn.classList.remove('voted-down');
                countSpan.textContent = currentVotes + 2;
            } else {
                countSpan.textContent = currentVotes + 1;
            }
        }
    } else if (direction === 'down') {
        const upBtn = parent.querySelector('button[aria-label="Upvote"]');
        if (activeDown) {
            button.classList.remove('voted-down');
            countSpan.textContent = currentVotes + 1;
        } else {
            button.classList.add('voted-down');
            if (upBtn && upBtn.classList.contains('voted-up')) {
                upBtn.classList.remove('voted-up');
                countSpan.textContent = currentVotes - 2;
            } else {
                countSpan.textContent = currentVotes - 1;
            }
        }
    }
}

// ── Community Q&A Input Interactions ──
function updateQaCharCount() {
    const input = document.getElementById('qa-input-field');
    const counter = document.getElementById('qa-char-count');
    if (input && counter) {
        const len = input.value.length;
        counter.textContent = `${len}/200`;
        counter.style.color = len >= 180 ? '#f87171' : len >= 120 ? '#fbbf24' : '';
    }
}

function submitQaQuestion() {
    const input = document.getElementById('qa-input-field');
    if (!input) return;
    const question = input.value.trim();
    if (question.length === 0) {
        alert('Vui lòng nhập câu hỏi của bạn.');
        input.focus();
        return;
    }

    const grid = document.getElementById('qa-grid');
    if (!grid) return;

    // Animate submit button
    const submitBtn = document.querySelector('.qa-submit-btn');
    let originalHtml = '';
    if (submitBtn) {
        originalHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang gửi...';
        submitBtn.classList.add('opacity-80', 'pointer-events-none');
    }

    const newId = 'qa-' + Date.now();
    qaCommentsData[newId] = [
        {
            author: 'Renty Bot',
            meta: 'Hệ thống tự động',
            text: 'Chào bạn, câu hỏi của bạn đã được đăng thành công. Hệ thống sẽ tự động gửi thông báo đến các thành viên trong khu vực để phản hồi sớm nhất!',
            is_best: true,
            time: 'Vừa xong'
        }
    ];

    setTimeout(() => {
        // Create new element
        const newCard = document.createElement('div');
        newCard.className = 'qa-card rounded-2xl border border-slate-800/50 flex flex-col justify-between transition-all duration-300 hover:border-slate-700/60 group/card overflow-hidden animate-fade-in';
        newCard.style.backgroundColor = '#1a1a20';
        newCard.style.animationDelay = '0s';

        newCard.innerHTML = `
            <div class="p-5 pb-0">
                <!-- Meta Row -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-slate-800/80 flex items-center justify-center border border-slate-700/60">
                            <i class="fa-solid fa-user-secret text-xs text-teal-400"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] font-extrabold text-slate-300">Người dùng ẩn danh</span>
                            <span class="block text-[8px] text-slate-600 font-bold mt-0.5">Vừa xong</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold border uppercase tracking-wider bg-teal-500/10 text-teal-400 border-teal-500/20">
                        Cầu Giấy
                    </span>
                </div>

                <!-- Question Title -->
                <h3 class="text-xs font-bold text-slate-200 leading-relaxed group-hover/card:text-teal-400 transition-colors mb-2.5 flex items-start gap-1.5">
                    ${escapeHtml(question)}
                </h3>

                <!-- Tags -->
                <div class="flex flex-wrap gap-1.5 mb-3">
                    <span class="px-2 py-0.5 rounded-md bg-slate-800/60 text-slate-500 text-[9px] font-bold border border-slate-800/40">#HỏiẨnDanh</span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-800/60 text-slate-500 text-[9px] font-bold border border-slate-800/40">#RentyCommunity</span>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="px-5 pb-4 pt-3 mt-auto border-t border-slate-800/40">
                <!-- Interaction Row -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-0.5 bg-slate-900/50 border border-slate-800/60 rounded-lg overflow-hidden">
                        <button type="button" onclick="voteQa(this, 'up')" class="qa-vote-btn px-2.5 py-1.5 text-slate-500 hover:text-emerald-400 hover:bg-emerald-500/8 transition-all text-xs" aria-label="Upvote">
                            <i class="fa-solid fa-arrow-up"></i>
                        </button>
                        <span class="px-2 text-[11px] font-extrabold text-slate-300 tabular-nums qa-vote-count select-none">1</span>
                        <button type="button" onclick="voteQa(this, 'down')" class="qa-vote-btn px-2.5 py-1.5 text-slate-500 hover:text-rose-400 hover:bg-rose-500/8 transition-all text-xs" aria-label="Downvote">
                            <i class="fa-solid fa-arrow-down"></i>
                        </button>
                    </div>
                    <button type="button" onclick="openQaCommentsModal(this)" class="qa-comment-btn flex items-center gap-1.5 text-[11px] font-bold text-slate-500 hover:text-slate-300 transition-colors" data-qa-index="${newId}" data-qa-question="${escapeHtml(question)}" data-qa-area="Cầu Giấy" data-qa-time="Vừa xong">
                        <i class="fa-regular fa-message text-[10px]"></i>
                        <span class="qa-comment-count">1 bình luận</span>
                    </button>
                </div>

                <!-- Best Reply -->
                <div class="qa-best-reply rounded-xl p-3 flex flex-col gap-1.5 bg-slate-900/40 border border-slate-800/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-bold text-slate-400">Renty Bot</span>
                            <span class="w-3.5 h-3.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[7px] border border-emerald-500/15 inline-flex items-center justify-center" title="Đã xác minh">
                                <i class="fa-solid fa-check"></i>
                            </span>
                        </div>
                        <span class="text-[8px] text-teal-500/70 font-bold uppercase tracking-wider">Best</span>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed italic">
                        "Chào bạn, câu hỏi của bạn đã được đăng thành công. Hệ thống sẽ tự động gửi thông báo đến các thành viên trong khu vực để phản hồi sớm nhất!"
                    </p>
                </div>
            </div>
        `;

        grid.insertBefore(newCard, grid.firstChild);

        // Scroll the newly posted comment into view smoothly without page reload or jump
        newCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        // Show thank you toast notification
        alert('Cảm ơn bạn đã gửi ý kiến');

        input.value = '';
        updateQaCharCount();
        if (submitBtn) {
            submitBtn.innerHTML = originalHtml || '<i class="fa-solid fa-paper-plane mr-1"></i> Gửi';
            submitBtn.classList.remove('opacity-80', 'pointer-events-none');
        }
    }, 1000);
}

function loadMoreQaQuestions(button) {
    if (!button) return;
    const grid = document.getElementById('qa-grid');
    if (!grid) return;

    // Check if extra cards are already added
    const extraCards = grid.querySelectorAll('.qa-card-extra');

    if (extraCards.length > 0) {
        // Toggle visibility
        const isHidden = extraCards[0].classList.contains('hidden');
        if (isHidden) {
            extraCards.forEach(card => {
                card.classList.remove('hidden');
                card.style.animation = 'fadeSlideDown 0.4s ease-out forwards';
            });
            button.innerHTML = '<i class="fa-solid fa-angles-up text-[10px] mr-1.5"></i> Thu gọn câu hỏi';
        } else {
            extraCards.forEach(card => {
                card.classList.add('hidden');
            });
            button.innerHTML = '<i class="fa-solid fa-angles-down text-[10px] mr-1.5"></i> Xem thêm câu hỏi';
            // Scroll back to the top of QA section smoothly
            const qaHeader = document.querySelector('.qa-section-header');
            if (qaHeader) {
                qaHeader.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
        return;
    }

    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Đang tải thêm câu hỏi...';
    button.classList.add('pointer-events-none', 'opacity-80');

    setTimeout(() => {
        const mockQuestions = [
            {
                time: '5 giờ trước',
                area: 'Đống Đa',
                areaClass: 'bg-violet-500/10 text-violet-400 border-violet-500/20',
                question: 'Khu vực Chùa Láng có nhà trọ nào tầm 3.5tr - 4tr mà có chỗ để xe máy tầng 1 rộng rãi không ạ? Nghe bảo khu này hay bị chật chỗ để xe.',
                tags: ['Tìm phòng', 'Chùa Láng', 'Chung cư mini'],
                votes: 19,
                comments: 7,
                reply_author: 'Hoàng Long',
                reply_school: 'ĐH Ngoại Thương',
                reply_text: 'Mấy ngõ như ngõ 80 hoặc ngõ 157 Chùa Láng nhiều chung cư mini mới xây lắm bạn ơi. Có hầm xe rộng rãi nhưng nhớ hỏi kỹ xem có tính thêm phí gửi xe không nha.'
            },
            {
                time: '1 ngày trước',
                area: 'Thanh Xuân',
                areaClass: 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                question: 'Mọi người cho mình hỏi nước sinh hoạt ở mạn Phùng Khoang dạo này có ổn không ạ? Có bị cặn đen hay mất nước đột ngột không?',
                tags: ['Nước sinh hoạt', 'Phùng Khoang', 'Review'],
                votes: 8,
                comments: 3,
                reply_author: 'Thu Thảo',
                reply_school: 'Học viện Bưu chính',
                reply_text: 'Đúng là mạn này thỉnh thoảng nước hơi yếu thật ấy, nhất là mấy khu tập thể cũ. Bạn nên mua thêm một đầu lọc thô lắp ở vòi lavabo với vòi tắm cho an tâm.'
            }
        ];

        mockQuestions.forEach((qa, idx) => {
            const card = document.createElement('div');
            card.className = 'qa-card qa-card-extra rounded-2xl border border-slate-800/50 flex flex-col justify-between transition-all duration-300 hover:border-slate-700/60 group/card overflow-hidden animate-fade-in';
            card.style.backgroundColor = '#1a1a20';
            card.style.animationDelay = `${idx * 0.1}s`;

            const tagsHtml = qa.tags.map(t => `<span class="px-2 py-0.5 rounded-md bg-slate-800/60 text-slate-500 text-[9px] font-bold border border-slate-800/40">#${t}</span>`).join('');

            card.innerHTML = `
                <div class="p-5 pb-0">
                    <!-- Meta Row -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-slate-800/80 flex items-center justify-center border border-slate-700/60">
                                <i class="fa-solid fa-user-secret text-xs text-teal-400"></i>
                            </div>
                            <div>
                                <span class="block text-[10px] font-extrabold text-slate-300">Người dùng ẩn danh</span>
                                <span class="block text-[8px] text-slate-600 font-bold mt-0.5">${qa.time}</span>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold border uppercase tracking-wider ${qa.areaClass}">
                            ${qa.area}
                        </span>
                    </div>

                    <!-- Question Title -->
                    <h3 class="text-xs font-bold text-slate-200 leading-relaxed group-hover/card:text-teal-400 transition-colors mb-2.5">
                        ${qa.question}
                    </h3>

                    <!-- Tags -->
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        ${tagsHtml}
                    </div>
                </div>

                <!-- Bottom Section -->
                <div class="px-5 pb-4 pt-3 mt-auto border-t border-slate-800/40">
                    <!-- Interaction Row -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-0.5 bg-slate-900/50 border border-slate-800/60 rounded-lg overflow-hidden">
                            <button type="button" onclick="voteQa(this, 'up')" class="qa-vote-btn px-2.5 py-1.5 text-slate-500 hover:text-emerald-400 hover:bg-emerald-500/8 transition-all text-xs" aria-label="Upvote">
                                <i class="fa-solid fa-arrow-up"></i>
                            </button>
                            <span class="px-2 text-[11px] font-extrabold text-slate-300 tabular-nums qa-vote-count select-none">${qa.votes}</span>
                            <button type="button" onclick="voteQa(this, 'down')" class="qa-vote-btn px-2.5 py-1.5 text-slate-500 hover:text-rose-400 hover:bg-rose-500/8 transition-all text-xs" aria-label="Downvote">
                                <i class="fa-solid fa-arrow-down"></i>
                            </button>
                        </div>
                        <button type="button" onclick="openQaCommentsModal(this)" class="qa-comment-btn flex items-center gap-1.5 text-[11px] font-bold text-slate-500 hover:text-slate-300 transition-colors" data-qa-index="${idx + 4}" data-qa-question="${escapeHtml(qa.question)}" data-qa-area="${qa.area}" data-qa-time="${qa.time}">
                            <i class="fa-regular fa-message text-[10px]"></i>
                            <span class="qa-comment-count">${qa.comments} bình luận</span>
                        </button>
                    </div>

                    <!-- Best Reply -->
                    <div class="qa-best-reply rounded-xl p-3 flex flex-col gap-1.5 bg-slate-900/40 border border-slate-800/30">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-bold text-slate-400">${qa.reply_author} (${qa.reply_school})</span>
                                <span class="w-3.5 h-3.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[7px] border border-emerald-500/15 inline-flex items-center justify-center" title="Đã xác minh">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                            </div>
                            <span class="text-[8px] text-teal-500/70 font-bold uppercase tracking-wider">Best</span>
                        </div>
                        <p class="text-[11px] text-slate-400 leading-relaxed italic">
                            "${qa.reply_text}"
                        </p>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });

        // Update button status
        button.innerHTML = '<i class="fa-solid fa-angles-up text-[10px] mr-1.5"></i> Thu gọn câu hỏi';
        button.classList.remove('pointer-events-none', 'opacity-80');
    }, 800);
}

function showCommentsAlert() {
    if (typeof showCustomAlert === 'function') {
        showCustomAlert('Hệ thống bình luận chi tiết đang được đồng bộ hóa, tính năng này sẽ khả dụng sớm nhất!', 'info');
    }
}

function subscribeNewsletter() {
    const emailInput = document.getElementById('footer-newsletter-email');
    if (!emailInput) return;
    const email = emailInput.value.trim();
    if (!email) {
        if (typeof showCustomAlert === 'function') {
            showCustomAlert('Vui lòng nhập địa chỉ email của bạn.', 'warning');
        }
        emailInput.focus();
        return;
    }
    // Simple email validation regex
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        if (typeof showCustomAlert === 'function') {
            showCustomAlert('Địa chỉ email không hợp lệ. Vui lòng kiểm tra lại.', 'warning');
        }
        emailInput.focus();
        return;
    }

    if (typeof showCustomAlert === 'function') {
        showCustomAlert('Đăng ký nhận tin tức phòng trọ mới thành công!', 'success');
    }
    emailInput.value = '';
}

// Bind functions to window so inline onclick event handlers can call them
window.toggleFilterDrawer = toggleFilterDrawer;
window.toggleThemeMode = toggleThemeMode;
window.openQuickRoomPreview = openQuickRoomPreview;
window.closeQuickRoomPreview = closeQuickRoomPreview;
window.openReportModal = openReportModal;
window.closeReportModal = closeReportModal;
window.updateMoveInCost = updateMoveInCost;
window.toggleDetailDescription = toggleDetailDescription;
window.renderReviewSummary = renderReviewSummary;
window.renderReviews = renderReviews;
window.toggleAllReviews = toggleAllReviews;
window.setActiveRoomImage = setActiveRoomImage;
window.openImageZoom = openImageZoom;
window.changeZoomImage = changeZoomImage;
window.closeImageZoom = closeImageZoom;
window.clearViewedRooms = clearViewedRooms;
window.openRoomDetailModal = openRoomDetailModal;
window.loadReviewSummary = loadReviewSummary;
window.closeRoomDetailModal = closeRoomDetailModal;
window.setViewMode = setViewMode;
window.runSearchWithSkeleton = runSearchWithSkeleton;
window.filterItems = filterItems;
window.openRentySearchSuggestions = openRentySearchSuggestions;
window.blurRentySearch = blurRentySearch;
window.applySearchSuggestion = applySearchSuggestion;
window.handleSearchInput = handleSearchInput;
window.subscribeEmptyNotification = subscribeEmptyNotification;
window.closeNotifySubscribeModal = closeNotifySubscribeModal;
window.handleNotifySubscribeSubmit = handleNotifySubscribeSubmit;
window.showCustomAlert = showCustomAlert;
window.closeCustomAlert = closeCustomAlert;
window.toggleVisualFilter = toggleVisualFilter;
window.syncFromCheckbox = syncFromCheckbox;
window.openHotAreasModal = openHotAreasModal;
window.closeHotAreasModal = closeHotAreasModal;
window.selectHotArea = selectHotArea;
window.openNewReviewsModal = openNewReviewsModal;
window.closeNewReviewsModal = closeNewReviewsModal;
window.showMarker = showMarker;
window.hideMarker = hideMarker;
window.submitQaQuestion = submitQaQuestion;
window.voteQa = voteQa;
window.updateQaCharCount = updateQaCharCount;
window.loadMoreQaQuestions = loadMoreQaQuestions;
window.showCommentsAlert = showCommentsAlert;
window.subscribeNewsletter = subscribeNewsletter;
window.openQaCommentsModal = openQaCommentsModal;
window.closeQaCommentsModal = closeQaCommentsModal;
window.submitQaReply = submitQaReply;

function updateDistanceSlider(val) {
    const slider = document.getElementById('distance-slider');
    if (!slider) return;
    const min = parseFloat(slider.min) || 0;
    const max = parseFloat(slider.max) || 3;
    const percentage = ((parseFloat(val) - min) / (max - min)) * 100;
    slider.style.setProperty('--range-progress', `${percentage}%`);
    
    // Update feedback text
    const feedback = document.getElementById('distance-feedback');
    if (feedback) {
        const formattedDist = (parseFloat(val) % 1 === 0) ? parseInt(val, 10) : parseFloat(val);
        feedback.innerText = `Tìm phòng trong bán kính dưới ${formattedDist}km từ Đại học Bách Khoa`;
    }
    
    // Update active state on ticks
    document.querySelectorAll('.tick-mark').forEach(tick => {
        const tickVal = parseFloat(tick.getAttribute('data-value'));
        if (parseFloat(val) >= tickVal) {
            tick.classList.add('active', 'text-teal-400');
        } else {
            tick.classList.remove('active', 'text-teal-400');
        }
    });
    
    // Run filtering
    filterItems();
}

function setSliderValue(val) {
    const slider = document.getElementById('distance-slider');
    if (slider) {
        slider.value = val;
        updateDistanceSlider(val);
    }
}

// Bind distance slider helpers to window
window.updateDistanceSlider = updateDistanceSlider;
window.setSliderValue = setSliderValue;

// Initialize the distance slider progress on DOM Content Loaded
document.addEventListener('DOMContentLoaded', () => {
    const slider = document.getElementById('distance-slider');
    if (slider) {
        updateDistanceSlider(slider.value);
    }

    // 1. 3D CARD TILT & GLARE EFFECT
    function init3DCardTilt() {
        const cardsSelector = '.room-item-card, #renty-hero-section div.lg-col-span-2, #renty-hero-section div.bg-gradient-to-br';
        
        // Add glare overlay dynamically to room cards
        document.querySelectorAll('.room-item-card').forEach(card => {
            if (!card.querySelector('.card-3d-glare')) {
                const glare = document.createElement('div');
                glare.className = 'card-3d-glare';
                card.appendChild(glare);
            }
        });

        // Use event delegation for dynamic rooms list updates
        document.body.addEventListener('mousemove', (e) => {
            const card = e.target.closest(cardsSelector);
            if (!card) return;

            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const xc = rect.width / 2;
            const yc = rect.height / 2;
            const dx = x - xc;
            const dy = y - yc;

            // Apply slight tilt angle (max 6 degrees)
            const rotateX = -(dy / yc) * 6;
            const rotateY = (dx / xc) * 6;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.015, 1.015, 1.015)`;
            card.style.setProperty('--glare-x', `${(x / rect.width) * 100}%`);
            card.style.setProperty('--glare-y', `${(y / rect.height) * 100}%`);
            
            // Still support standard radial glow
            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);
        });

        document.body.addEventListener('mouseleave', (e) => {
            const card = e.target.closest(cardsSelector);
            if (card) {
                card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
            }
        }, true);
    }
    init3DCardTilt();

    // Re-run glare adding on list filter updates
    const originalFilterItems = window.filterItems;
    if (typeof originalFilterItems === 'function') {
        window.filterItems = function(...args) {
            originalFilterItems(...args);
            // Re-apply glare overlay to newly generated/revealed room cards
            setTimeout(() => {
                document.querySelectorAll('.room-item-card').forEach(card => {
                    if (!card.querySelector('.card-3d-glare')) {
                        const glare = document.createElement('div');
                        glare.className = 'card-3d-glare';
                        card.appendChild(glare);
                    }
                });
            }, 100);
        };
    }

    // 2. AMBIENT PARTICLES BACKGROUND FOR HERO SECTION
    function initAmbientParticles() {
        const canvas = document.getElementById('renty-ambient-particles');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        let particles = [];
        let mouse = { x: null, y: null, radius: 100 };
        
        function resize() {
            const rect = canvas.parentNode.getBoundingClientRect();
            canvas.width = rect.width;
            canvas.height = rect.height;
        }
        resize();
        window.addEventListener('resize', resize);
        
        canvas.parentNode.addEventListener('mousemove', (e) => {
            const rect = canvas.getBoundingClientRect();
            mouse.x = e.clientX - rect.left;
            mouse.y = e.clientY - rect.top;
        });
        
        canvas.parentNode.addEventListener('mouseleave', () => {
            mouse.x = null;
            mouse.y = null;
        });

        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height + canvas.height;
                this.size = Math.random() * 2 + 1;
                this.speedX = Math.random() * 0.4 - 0.2;
                this.speedY = -(Math.random() * 0.6 + 0.2);
                this.color = Math.random() > 0.5 ? 'rgba(16, 185, 129, 0.15)' : 'rgba(99, 102, 241, 0.12)';
                this.baseX = this.x;
                this.baseY = this.y;
                this.density = (Math.random() * 20) + 5;
            }
            update() {
                this.y += this.speedY;
                this.x += this.speedX;
                
                // Repel effect on mouse hover
                if (mouse.x !== null && mouse.y !== null) {
                    let dx = mouse.x - this.x;
                    let dy = mouse.y - this.y;
                    let distance = Math.sqrt(dx * dx + dy * dy);
                    if (distance < mouse.radius) {
                        let force = (mouse.radius - distance) / mouse.radius;
                        let directionX = dx / distance;
                        let directionY = dy / distance;
                        this.x -= directionX * force * 3;
                        this.y -= directionY * force * 3;
                    }
                }
                
                // Reset when off-screen
                if (this.y < -10) {
                    this.y = canvas.height + 10;
                    this.x = Math.random() * canvas.width;
                }
                if (this.x < -10 || this.x > canvas.width + 10) {
                    this.x = Math.random() * canvas.width;
                }
            }
            draw() {
                ctx.fillStyle = this.color;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }
        
        // Spawn particles
        const particleCount = Math.min(50, Math.floor(canvas.width / 15));
        for (let i = 0; i < particleCount; i++) {
            particles.push(new Particle());
        }
        
        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
                particles[i].draw();
            }
            requestAnimationFrame(animate);
        }
        animate();
    }
    initAmbientParticles();

    // 3. LIVE ACTIVITY TICKER PILL (Multi-Agent Interaction Simulation)
    function initLiveActivityTicker() {
        const textEl = document.getElementById('live-activity-text');
        const pillEl = document.getElementById('live-activity-pill');
        if (!textEl || !pillEl) return;
        
        const activities = [
            '24 người đang tìm phòng tại Đống Đa',
            'Một phòng mới ở Cầu Giấy vừa được thuê thành công 🎉',
            '35 sinh viên đang xem đánh giá khu vực Bách Khoa',
            '12 người dùng đang so sánh căn hộ tại Thanh Xuân',
            'AI Companion vừa cập nhật 5 mẹo tránh mất cọc trọ',
            'Hơn 40 lượt tìm kiếm phòng có ban công và thú cưng trong 1 giờ qua',
            'Chủ nhà Nguyễn Văn A vừa cập nhật trạng thái phòng trống mới'
        ];
        
        let index = 0;
        setInterval(() => {
            pillEl.style.opacity = '0';
            pillEl.style.transform = 'translateY(-5px)';
            
            setTimeout(() => {
                index = (index + 1) % activities.length;
                textEl.textContent = activities[index];
                
                // Randomize background/text opacity for visual variety
                if (index % 3 === 0) {
                    pillEl.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-[10px] font-extrabold text-emerald-400 select-none shadow-sm transition-all duration-500';
                } else if (index % 3 === 1) {
                    pillEl.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-[10px] font-extrabold text-indigo-400 select-none shadow-sm transition-all duration-500';
                } else {
                    pillEl.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-[10px] font-extrabold text-cyan-400 select-none shadow-sm transition-all duration-500';
                }
                
                pillEl.style.opacity = '1';
                pillEl.style.transform = 'translateY(0)';
            }, 500);
        }, 7000);
    }
    initLiveActivityTicker();

    // 4. RENTY COMPANION AI SPEECH BUBBLE HINTS
    const bubbleEl = document.getElementById('renty-agent-bubble');
    const bubbleTextEl = document.getElementById('renty-agent-bubble-text');
    let bubbleTimer = null;
    
    const companionTips = [
        'Hôm nay khu Cầu Giấy đang có nhiều phòng giá tốt nhất đấy! 📍',
        '💡 Mẹo: Nhấp chọn nhiều phòng để bật tính năng so sánh trực quan nhé!',
        '🐾 Bạn mang theo thú cưng? Hãy bật bộ lọc "Nuôi thú cưng" ngay.',
        '🛡️ Nhớ đọc kỹ điều khoản hợp đồng trước khi chuyển tiền đặt cọc nha!',
        '✨ Renty AI hỗ trợ tư vấn 24/7. Bạn cứ tự nhiên chat với mình nhé!'
    ];
    
    function showAgentBubble() {
        if (!bubbleEl || !bubbleTextEl) return;
        clearTimeout(bubbleTimer);
        
        // Pick a random tip
        const randomTip = companionTips[Math.floor(Math.random() * companionTips.length)];
        bubbleTextEl.innerHTML = randomTip;
        
        bubbleEl.classList.add('show');
    }
    
    function hideAgentBubble() {
        if (!bubbleEl) return;
        bubbleEl.classList.remove('show');
    }
    
    // Bind to window for HTML event handlers
    window.showAgentBubble = showAgentBubble;
    window.hideAgentBubble = hideAgentBubble;
    
    // Periodically show auto tips when idle
    function initAgentCompanion() {
        if (!bubbleEl) return;
        
        // Show first tip after 4 seconds
        setTimeout(() => {
            showAgentBubble();
            // Hide after 6 seconds
            bubbleTimer = setTimeout(hideAgentBubble, 6000);
        }, 4000);
        
        // Repeat cycle every 24 seconds
        setInterval(() => {
            if (!document.getElementById('renty-chatbot-panel').classList.contains('is-open')) {
                showAgentBubble();
                bubbleTimer = setTimeout(hideAgentBubble, 7000);
            }
        }, 24000);
    }
    initAgentCompanion();

    // Dynamic Scroll Progress Indicator
    const progressContainer = document.createElement('div');
    progressContainer.style.position = 'fixed';
    progressContainer.style.top = '0';
    progressContainer.style.left = '0';
    progressContainer.style.width = '100%';
    progressContainer.style.height = '3px';
    progressContainer.style.zIndex = '9999';
    progressContainer.style.pointerEvents = 'none';

    const progressBar = document.createElement('div');
    progressBar.style.width = '0%';
    progressBar.style.height = '100%';
    progressBar.style.background = 'linear-gradient(to right, #10b981, #06b6d4, #6366f1)';
    progressBar.style.transition = 'width 0.08s ease-out';
    progressContainer.appendChild(progressBar);
    document.body.appendChild(progressContainer);

    window.addEventListener('scroll', () => {
        const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
        if (scrollHeight > 0) {
            const progress = (window.scrollY / scrollHeight) * 100;
            progressBar.style.width = `${progress}%`;
        }
    }, { passive: true });
});

// ════════════════════════════════════════════════════════════════════════
// RENTY CHATBOT ENGINE
// ════════════════════════════════════════════════════════════════════════

let rentyChatbotOpen = false;
let rentyChatbotHistory = [];
let rentyChatbotInitialized = false;
let rentyChatbotSending = false;
const RENTY_CHATBOT_MAX_LENGTH = 300;

// ── Conversation Context (Ngữ cảnh hội thoại) ──────────────────────
let chatbotContext = {
    locations: [],       // Khu vực đã hỏi
    maxPrice: null,      // Giá tối đa
    minPrice: null,      // Giá tối thiểu
    amenities: { pets: false, loft: false, balcony: false, wc: false },
    lastResults: [],     // Kết quả lần trước
    lastQuery: '',       // Câu hỏi gốc lần trước
    turnCount: 0         // Số lượt hội thoại
};

function toggleRentyChatbot() {
    const panel = document.getElementById('renty-chatbot-panel');
    if (!panel) return;
    rentyChatbotOpen = !rentyChatbotOpen;
    panel.classList.toggle('is-open', rentyChatbotOpen);

    // Hide badge when opening
    if (rentyChatbotOpen) {
        const badge = document.getElementById('renty-chatbot-badge');
        if (badge) badge.classList.add('hidden');
    }

    // Show welcome message on first open
    if (rentyChatbotOpen && !rentyChatbotInitialized) {
        rentyChatbotInitialized = true;
        addBotMessage(
            `👋 Chào bạn! Mình là <strong>Renty AI</strong> - Trợ lý tìm kiếm phòng trọ, chung cư mini, căn hộ và review không gian sống thông minh. 🏠<br><br>` +
            `Mình có thể hỗ trợ bạn:<br>` +
            `• 📍 Tìm kiếm nơi ở theo khu vực, tuyến đường, địa danh<br>` +
            `• 💰 Lọc phòng theo khoảng giá và ngân sách phù hợp<br>` +
            `• 🏠 Lọc nhanh phòng có ban công, gác lửng, cho nuôi thú cưng...<br>` +
            `• 🛡️ Tư vấn mẹo thuê phòng an toàn và tránh các rủi ro đặt cọc<br><br>` +
            `Bạn cần mình tìm kiếm nơi ở tại khu vực nào hoặc có tiêu chuẩn cụ thể gì không? Nhắn cho mình biết nhé! 👇`
        );
    }
}
window.toggleRentyChatbot = toggleRentyChatbot;

function clearRentyChatbot() {
    const container = document.getElementById('renty-chatbot-messages');
    if (container) container.innerHTML = '';
    rentyChatbotHistory = [];
    rentyChatbotInitialized = false;
    chatbotContext = {
        locations: [], maxPrice: null, minPrice: null,
        amenities: { pets: false, loft: false, balcony: false, wc: false },
        lastResults: [], lastQuery: '', turnCount: 0
    };
    addBotMessage('Đã xoá lịch sử. Bạn muốn tìm phòng trọ như thế nào? 😊');
    rentyChatbotInitialized = true;
}
window.clearRentyChatbot = clearRentyChatbot;

function getChatMessagesContainer() {
    return document.getElementById('chat_messages') || document.getElementById('renty-chatbot-messages');
}

function triggerEmptyChatError(inputEl) {
    const input = inputEl || document.getElementById('chat_input') || document.getElementById('renty-chatbot-input');
    const errorTextEl = document.getElementById('chat_input_error');

    if (input) {
        input.classList.remove('renty-input-shake');
        void input.offsetWidth;
        input.classList.add('renty-input-shake');

        const originalPlaceholder = input.getAttribute('data-original-placeholder') || input.placeholder;
        if (!input.getAttribute('data-original-placeholder')) {
            input.setAttribute('data-original-placeholder', originalPlaceholder);
        }
        input.placeholder = 'Vui lòng nhập nội dung câu hỏi trước khi gửi.';
        input.focus();

        if (errorTextEl) {
            errorTextEl.classList.remove('hidden');
        }

        setTimeout(() => {
            if (input) {
                input.classList.remove('renty-input-shake');
                input.placeholder = originalPlaceholder;
            }
            if (errorTextEl) {
                errorTextEl.classList.add('hidden');
            }
        }, 2500);
    }
}
window.triggerEmptyChatError = triggerEmptyChatError;

function addBotErrorMessage(errorText, retryText) {
    const container = getChatMessagesContainer();
    if (!container) return;

    const retryBtnHtml = retryText
        ? `<br><button type="button" class="renty-cb-retry-btn" onclick="sendRentyChatbotMessage('${escapeHtml(retryText)}');"><i class="fa-solid fa-rotate-right"></i> Gửi lại</button>`
        : '';

    const msgEl = document.createElement('div');
    msgEl.className = 'renty-cb-msg renty-cb-msg-bot renty-cb-msg-error';
    msgEl.innerHTML = `
        <div class="renty-cb-msg-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="renty-cb-bubble">
            <strong>Mất kết nối máy chủ AI Chatbot</strong><br>
            ${errorText}
            ${retryBtnHtml}
        </div>
    `;
    container.appendChild(msgEl);
    container.scrollTop = container.scrollHeight;
}
window.addBotErrorMessage = addBotErrorMessage;

function addBotMessage(html, roomCards) {
    const container = getChatMessagesContainer();
    if (!container) return;

    let cardsHtml = '';
    if (roomCards && roomCards.length > 0) {
        cardsHtml = roomCards.map(room => {
            const price = Number(room.price || 0).toLocaleString('vi-VN');
            const amenities = [];
            if (room.balcony === 'true' || room.balcony_txt === 'Có') amenities.push('Ban công');
            if (room.pets === 'true' || room.pets_txt === 'Có') amenities.push('Thú cưng');
            if (room.loft === 'true' || room.loft_txt === 'Có') amenities.push('Gác lửng');
            if (room.wc === 'true' || room.wc_txt === 'Có') amenities.push('WC riêng');
            const amenityText = amenities.slice(0, 3).join(' · ') || 'Cơ bản';

            return `<a href="/renty/room/${room.id}" class="renty-cb-room-card" target="_blank">
                <img src="${escapeHtml(room.cover_image || '')}" alt="${escapeHtml(room.title || '')}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=400&q=60';">
                <div class="renty-cb-room-info">
                    <span class="room-title">${escapeHtml(room.title || 'Phòng trọ')}</span>
                    <span class="room-price">${price}đ/tháng</span>
                    <div class="room-meta">
                        <span><i class="fa-solid fa-ruler-combined"></i> ${room.area_text || room.area + 'm²'}</span>
                        <span><i class="fa-solid fa-star"></i> ${room.rating}</span>
                        <span>${amenityText}</span>
                    </div>
                </div>
            </a>`;
        }).join('');
    }

    const msgEl = document.createElement('div');
    msgEl.className = 'renty-cb-msg renty-cb-msg-bot';
    msgEl.innerHTML = `
        <div class="renty-cb-msg-icon"><i class="fa-solid fa-robot"></i></div>
        <div class="renty-cb-bubble">${html}${cardsHtml}</div>
    `;
    container.appendChild(msgEl);
    container.scrollTop = container.scrollHeight;

    rentyChatbotHistory.push({ role: 'bot', text: html });
}

function addUserMessage(text) {
    const container = getChatMessagesContainer();
    if (!container) return;

    const msgEl = document.createElement('div');
    msgEl.className = 'renty-cb-msg renty-cb-msg-user';
    msgEl.innerHTML = `
        <div class="renty-cb-msg-icon"><i class="fa-solid fa-user"></i></div>
        <div class="renty-cb-bubble">${escapeHtml(text)}</div>
    `;
    container.appendChild(msgEl);
    container.scrollTop = container.scrollHeight;

    rentyChatbotHistory.push({ role: 'user', text });
}

function showTypingIndicator() {
    const container = getChatMessagesContainer();
    if (!container) return;

    const typingEl = document.createElement('div');
    typingEl.className = 'renty-cb-msg renty-cb-msg-bot';
    typingEl.id = 'renty-cb-typing';
    typingEl.innerHTML = `
        <div class="renty-cb-msg-icon"><i class="fa-solid fa-robot"></i></div>
        <div class="renty-cb-bubble">
            <div class="renty-cb-typing"><span></span><span></span><span></span></div>
        </div>
    `;
    container.appendChild(typingEl);
    container.scrollTop = container.scrollHeight;
}

function removeTypingIndicator() {
    const el = document.getElementById('renty-cb-typing');
    if (el) el.remove();
}

function setRentyChatbotBusy(isBusy) {
    rentyChatbotSending = isBusy;

    const input = document.getElementById('renty-chatbot-input');
    const sendButton = document.querySelector('.renty-chatbot-send-btn');
    const quickButtons = document.querySelectorAll('#renty-chatbot-quick button');

    if (input) {
        input.disabled = isBusy;
        input.placeholder = isBusy ? 'Renty AI đang trả lời...' : 'Hỏi về phòng trọ, khu vực, giá cả...';
    }

    if (sendButton) {
        sendButton.disabled = isBusy;
        sendButton.classList.toggle('opacity-50', isBusy);
        sendButton.classList.toggle('cursor-not-allowed', isBusy);
        sendButton.setAttribute('aria-busy', isBusy ? 'true' : 'false');
    }

    quickButtons.forEach(button => {
        button.disabled = isBusy;
        button.classList.toggle('opacity-50', isBusy);
        button.classList.toggle('cursor-not-allowed', isBusy);
    });
}

// ── Core: Search rooms from rentyRoomsData ──────────────────────────
function chatbotSearchRooms(query) {
    const data = window.rentyRoomsData;
    if (!data) return [];

    const rooms = Object.values(data);
    const parsed = parseNaturalSearch(query);
    const norm = normalizeText(query);

    // Dọn dẹp từ khóa tìm kiếm thô
    const cleanQuery = norm
        .replace(/^(gan|o|tim|cho thue|khu vuc|xung quanh)\s+/g, '')
        .replace(/\b(gan|o|tim|cho thue|khu vuc|xung quanh)\b/g, '')
        .replace(/\s+/g, ' ')
        .trim();

    let results = rooms.filter(room => {
        // Build searchable text from room data
        const searchable = normalizeText(
            `${room.title || ''} ${room.area_name || ''} ${room.address || ''} ` +
            `${room.location_description || ''} ${room.space_description || ''} ` +
            `${room.scenery_description || ''}`
        );

        // Price filter
        if (parsed.maxPrice && room.price > parsed.maxPrice) return false;

        // Amenity filters
        if (parsed.amenities.pets && room.pets !== 'true' && room.pets_txt !== 'Có') return false;
        if (parsed.amenities.loft && room.loft !== 'true' && room.loft_txt !== 'Có') return false;
        if (parsed.amenities.balcony && room.balcony !== 'true' && room.balcony_txt !== 'Có') return false;
        if (parsed.amenities.wc && room.wc !== 'true' && room.wc_txt !== 'Có') return false;

        // Location filter (Nếu nhận diện được quận/địa danh cụ thể)
        if (parsed.locations.length > 0) {
            const locMatch = parsed.locations.some(loc => searchable.includes(loc));
            if (!locMatch) return false;
        }

        // Keyword match (Khớp từ khóa nghiêm ngặt bằng ranh giới từ AND)
        if (cleanQuery.length > 0) {
            // Nếu có khớp trực tiếp cụm từ nguyên bản
            if (searchable.includes(cleanQuery)) return true;

            const stopWords = ['tim', 'phong', 'tro', 'duoi', 'o', 'gan', 'dai', 'hoc', 'trieu', 'tr',
                'gia', 'co', 'khong', 'cho', 'thue', 'can', 'ho', 'va', 'voi', 'trong',
                'ngoai', 'dep', 're', 'nha', 'chinh', 'chu', 'thang', 'lam'];
            const words = cleanQuery.split(/\s+/).filter(w => !stopWords.includes(w) && w.length > 1);
            
            if (words.length > 0) {
                // TẤT CẢ các từ khóa quan trọng phải xuất hiện trong searchable dưới dạng từ nguyên gốc
                const allWordsMatch = words.every(w => {
                    // Tránh ký tự regex đặc biệt
                    const escapedWord = w.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                    const regex = new RegExp('\\b' + escapedWord + '\\b', 'i');
                    return regex.test(searchable);
                });
                return allWordsMatch;
            }
        }

        // Nếu người dùng không nhập từ khóa mà chỉ chọn lọc tiện ích/giá
        return true;
    });

    // Sắp xếp: Ưu tiên phòng trống lên đầu, sau đó sắp xếp theo rating giảm dần, giá tăng dần
    results.sort((a, b) => {
        // Trạng thái trống (empty) lên trước
        const aEmpty = a.status === 'empty' ? 1 : 0;
        const bEmpty = b.status === 'empty' ? 1 : 0;
        if (aEmpty !== bEmpty) {
            return bEmpty - aEmpty; // 1 (empty) đứng trước 0 (khác)
        }

        // Cùng trạng thái trống thì xếp theo rating giảm dần
        const ratingDiff = parseFloat(b.rating || 0) - parseFloat(a.rating || 0);
        if (ratingDiff !== 0) return ratingDiff;

        // Cùng rating thì xếp theo giá tăng dần
        return a.price - b.price;
    });

    return results.slice(0, 5);
}

// ── Core: Generate response ─────────────────────────────────────────
// ── Follow-up detection ─────────────────────────────────────────────
function detectFollowUp(norm) {
    const patterns = {
        cheaper: /\b(re hon|gia re|re nhat|thap hon|it tien)\b/,
        expensive: /\b(dat hon|cao hon|sang hon|xin hon)\b/,
        bigger: /\b(rong hon|to hon|lon hon)\b/,
        other: /\b(phong khac|cai khac|xem them|con gi|co gi khac|khac khong)\b/,
        sameArea: /\b(o do|khu do|cho do|gan do|quanh do|khu vuc do)\b/,
        addBalcony: /\b(co ban cong|them ban cong|ban cong)\b/,
        addPets: /\b(nuoi thu cung|thu cung|co pet|nuoi cho|nuoi meo)\b/,
        addLoft: /\b(co gac|gac lung|gac xep|them gac)\b/,
        addWc: /\b(wc rieng|khep kin|ve sinh rieng)\b/,
        howMany: /\b(bao nhieu phong|co may phong|tong cong)\b/,
        cheapest: /\b(re nhat|thap nhat|gia nhat)\b/,
        best: /\b(tot nhat|diem cao nhat|danh gia cao)\b/
    };
    for (const [type, regex] of Object.entries(patterns)) {
        if (regex.test(norm)) return type;
    }
    return null;
}

// ── Core: Generate response ─────────────────────────────────────────

function generateChatbotResponse(userMsg) {
    const norm = normalizeText(userMsg);
    chatbotContext.turnCount++;

    // ── FAQ / Mẹo thuê trọ detection ──────────────────────────────────
    if (norm.includes('tip') || norm.includes('meo') || norm.includes('an toan') || norm.includes('luu y') || norm.includes('kinh nghiem')) {
        return {
            text: `🛡️ <strong>Mẹo thuê trọ an toàn từ Renty:</strong><br><br>` +
                `• <strong>Xem phòng trực tiếp</strong>: Không đặt cọc khi chưa đến xem thực tế.<br>` +
                `• <strong>Kiểm tra giấy tờ pháp lý</strong>: Yêu cầu xem sổ đỏ hoặc hợp đồng thuê gốc của chủ nhà.<br>` +
                `• <strong>Đọc kỹ hợp đồng</strong>: Chú ý kỹ các điều khoản hoàn cọc, chu kỳ tăng giá và chi phí dịch vụ phụ.<br>` +
                `• <strong>Kiểm tra an ninh xung quanh</strong>: Hệ thống camera, khóa cửa vân tay, hoặc có bảo vệ trông giữ.<br>` +
                `• <strong>Hỏi ý kiến cư dân cũ</strong>: Trò chuyện với người đang thuê xung quanh để biết thêm thông tin.<br>` +
                `• <strong>Chụp ảnh hiện trạng lúc bàn giao</strong>: Lưu lại hình ảnh thiết bị hư hỏng cũ để tránh tranh chấp khi trả phòng sau này.`,
            rooms: []
        };
    }
    if (norm.includes('xin chao') || norm.includes('hello') || norm === 'hi' || norm.includes('chao')) {
        return { text: `Chào bạn! 😄 Mình là Renty AI đây. Bạn muốn tìm phòng trọ, chung cư mini hay căn hộ ở khu vực nào, ngân sách tầm bao nhiêu để mình gợi ý cho bạn nhé?`, rooms: [] };
    }
    if (norm.includes('cam on') || norm.includes('thanks') || norm === 'tks') {
        return { text: `Không có gì nè! 🌟 Rất vui vì đã giúp được bạn. Cần tìm gì thêm cứ nhắn mình nhé!`, rooms: [] };
    }
    if (norm.includes('so sanh') || norm.includes('nen chon')) {
        return {
            text: `📊 <strong>Để so sánh nơi ở (phòng trọ, chung cư mini, căn hộ), bạn nên chú ý:</strong><br><br>` +
                `• <strong>Giá thuê / diện tích (m²)</strong> để tính đơn giá hợp lý.<br>` +
                `• <strong>Khoảng cách di chuyển</strong> đến nơi học tập, làm việc (đo bằng phút đi xe hoặc đi bộ).<br>` +
                `• <strong>Tiện ích sinh hoạt</strong>: Có WC riêng, máy giặt chung/riêng, chỗ phơi đồ đón nắng không.<br>` +
                `• <strong>Chi phí điện nước</strong>: Giá nhà nước hay giá kinh doanh tự phát.<br><br>` +
                `Bạn muốn mình tìm phòng theo tiêu chí nào trong số này?`,
            rooms: []
        };
    }

    // ── TRƯỜNG HỢP 2: Khu vực chưa có dữ liệu hoặc tỉnh thành khác (Ví dụ: Thủ Đức, Quận 9, TP.HCM...) ──
    const unsupportedLocations = [
        'thu duc', 'thu duoc', 'quan 9', 'q9', 'quan 1', 'q1', 'quan 7', 'q7', 'quan 2', 'q2', 'quan 3', 'q3',
        'quan 10', 'q10', 'quan 5', 'q5', 'quan 12', 'q12', 'quan 8', 'q8', 'binh thanh',
        'tan binh', 'tan phu', 'go vap', 'phu nhuan', 'binh tan', 'ho chi minh', 'hcm', 'sai gon', 'da nang', 'hai phong', 'can tho'
    ];
    const requestedUnsupported = unsupportedLocations.find(loc => norm.includes(loc));
    if (requestedUnsupported) {
        let matchedName = requestedUnsupported.toUpperCase();
        if (requestedUnsupported === 'hcm' || requestedUnsupported === 'ho chi minh' || requestedUnsupported === 'sai gon') matchedName = "TP. Hồ Chí Minh";
        if (requestedUnsupported === 'thu duc' || requestedUnsupported === 'thu duoc') matchedName = "Thủ Đức (TP.HCM)";
        if (requestedUnsupported.startsWith('q') && requestedUnsupported.length <= 3) {
            matchedName = `Quận ${requestedUnsupported.substring(1)} (TP.HCM)`;
        }
        
        return {
            text: `✨ <strong>Chào bạn thân mến,</strong><br><br>` +
                `Hiện tại Renty đang tập trung dữ liệu review và thông tin phòng xác thực tại khu vực <strong>Hà Nội</strong> (như Cầu Giấy, Đống Đa, Hai Bà Trưng, Thanh Xuân, Nam Từ Liêm...) nên thật tiếc là mình chưa có nhiều thông tin tại <strong>${matchedName}</strong>. 😢<br><br>` +
                `💡 <strong>Giải pháp thay thế cho bạn:</strong><br>` +
                `• Bạn có muốn tham khảo <strong>kinh nghiệm và mẹo thuê phòng an toàn</strong> áp dụng chung không?<br>` +
                `• Hoặc bạn có muốn tìm kiếm thử các phòng, căn hộ xác thực tại các khu vực thuộc <strong>Hà Nội</strong> không?<br><br>` +
                `Nhắn cho mình biết nếu bạn muốn chuyển hướng nhé!`,
            rooms: []
        };
    }

    // ── TRƯỜNG HỢP 3: Chỉ nhập từ khóa ngắn thiếu ngữ cảnh (Ví dụ: "thú cưng", "dưới 3 triệu", "ban công", "wc riêng") ──
    const shortKeywords = {
        pets: /\b(nuoi thu cung|thu cung|co pet|nuoi cho|nuoi meo|pet|thu cung)\b/,
        price: /\b(duoi 3 trieu|duoi 3tr|3 trieu|gia re|3tr|re)\b/,
        balcony: /\b(ban cong|co ban cong|thoang mat)\b/,
        wc: /\b(wc rieng|khep kin|ve sinh rieng|wc rieng)\b/,
        loft: /\b(co gac|gac lung|gac xep)\b/
    };

    let isOnlyShortKeyword = false;
    let shortType = '';
    
    // Nếu tin nhắn rất ngắn (< 15 ký tự) và khớp một trong các bộ lọc tiện ích/giá mà không có quận, trường học
    const hasLocationOrUni = norm.includes('cau giay') || norm.includes('dong da') || norm.includes('thanh xuan') || 
                             norm.includes('hai ba') || norm.includes('hoang mai') || norm.includes('ba dinh') ||
                             norm.includes('tay ho') || norm.includes('ha dong') || norm.includes('tu liem') || 
                             norm.includes('bach khoa') || norm.includes('ngoai thuong') || norm.includes('quoc gia') ||
                             norm.includes('su pham') || norm.includes('kinh te') || norm.includes('duong') || norm.includes('ngo');

    if (!hasLocationOrUni && userMsg.length < 25) {
        for (const [key, regex] of Object.entries(shortKeywords)) {
            if (regex.test(norm)) {
                isOnlyShortKeyword = true;
                shortType = key;
                break;
            }
        }
    }

    if (isOnlyShortKeyword) {
        let specName = '';
        if (shortType === 'pets') specName = 'cho phép nuôi thú cưng 🐾';
        if (shortType === 'price') specName = 'có giá dưới 3 triệu 💰';
        if (shortType === 'balcony') specName = 'có ban công thoáng mát 🌿';
        if (shortType === 'wc') specName = 'có vệ sinh khép kín riêng tư 🚿';
        if (shortType === 'loft') specName = 'có gác lửng tiện lợi 🏠';

        return {
            text: `🔍 <strong>Mình đã ghi nhận nhu cầu của bạn!</strong><br><br>` +
                `Mình thấy bạn đang tìm kiếm những nơi ở <strong>${specName}</strong>.<br><br>` +
                `👉 Để mình lọc chính xác nhất, bạn muốn tìm quanh <strong>khu vực quận nào</strong>, <strong>tuyến đường nào</strong> hoặc gần <strong>địa danh nào</strong> không? Nhắn cho mình biết nhé!`,
            rooms: []
        };
    }

    // ── Follow-up context handling ───────────────────────────────────
    const followUp = detectFollowUp(norm);
    if (followUp && chatbotContext.turnCount > 1) {
        return handleFollowUp(followUp, userMsg);
    }

    // ── TRƯỜNG HỢP 1: Thực hiện tìm kiếm phòng bình thường ─────────────────
    const parsed = parseNaturalSearch(userMsg);

    // Merge vào context để ghi nhớ
    if (parsed.locations.length > 0) chatbotContext.locations = parsed.locations;
    if (parsed.maxPrice) chatbotContext.maxPrice = parsed.maxPrice;
    if (parsed.amenities.pets) chatbotContext.amenities.pets = true;
    if (parsed.amenities.loft) chatbotContext.amenities.loft = true;
    if (parsed.amenities.balcony) chatbotContext.amenities.balcony = true;
    if (parsed.amenities.wc) chatbotContext.amenities.wc = true;
    chatbotContext.lastQuery = userMsg;

    const rooms = chatbotSearchRooms(userMsg);
    chatbotContext.lastResults = rooms;

    if (rooms.length > 0) {
        return buildRoomResponse(rooms, userMsg);
    }

    // Nếu hoàn toàn không có phòng nào khớp với khu vực Hà Nội được hỏi
    const locationPart = parsed.locations.length > 0 ? ` tại khu vực <strong>${parsed.locations.join(', ')}</strong>` : '';
    return {
        text: `😢 <strong>Renty AI chưa tìm thấy phòng phù hợp</strong>${locationPart} với đầy đủ các tiêu chí của bạn.<br><br>` +
            `💡 <strong>Gợi ý dành cho bạn:</strong><br>` +
            `• Thử nới rộng ngân sách hoặc giảm bớt một vài bộ lọc tiện ích (như ban công hoặc nuôi thú cưng) để xem thêm nhiều lựa chọn.<br>` +
            `• Hoặc thử tìm kiếm theo một số khu vực lân cận xem sao nhé!<br><br>` +
            `Nếu bạn cần tư vấn thêm về khu vực này, cứ nhắn cho mình!`,
        rooms: []
    };
}


function handleFollowUp(type, userMsg) {
    const data = window.rentyRoomsData ? Object.values(window.rentyRoomsData) : [];
    let results = [];
    let msg = '';

    switch (type) {
        case 'cheaper': {
            const maxP = chatbotContext.lastResults.length > 0
                ? Math.min(...chatbotContext.lastResults.map(r => r.price))
                : (chatbotContext.maxPrice || 5000000);
            results = data.filter(r => r.price < maxP && !chatbotContext.lastResults.some(lr => lr.id === r.id));
            if (chatbotContext.locations.length > 0) {
                results = results.filter(r => {
                    const s = normalizeText(`${r.area_name} ${r.address} ${r.location_description}`);
                    return chatbotContext.locations.some(l => s.includes(l));
                });
            }
            results.sort((a, b) => a.price - b.price);
            results = results.slice(0, 5);
            msg = results.length > 0
                ? `💰 Đây là các phòng <strong>rẻ hơn</strong> (dưới ${maxP.toLocaleString('vi-VN')}đ):`
                : `😅 Không tìm thấy phòng rẻ hơn${chatbotContext.locations.length ? ' ở ' + chatbotContext.locations.join(', ') : ''} rồi.`;
            break;
        }
        case 'expensive': {
            const minP = chatbotContext.lastResults.length > 0
                ? Math.max(...chatbotContext.lastResults.map(r => r.price))
                : 0;
            results = data.filter(r => r.price > minP);
            if (chatbotContext.locations.length > 0) {
                results = results.filter(r => {
                    const s = normalizeText(`${r.area_name} ${r.address}`);
                    return chatbotContext.locations.some(l => s.includes(l));
                });
            }
            results.sort((a, b) => a.price - b.price);
            results = results.slice(0, 5);
            msg = results.length > 0
                ? `✨ Phòng <strong>cao cấp hơn</strong> (trên ${minP.toLocaleString('vi-VN')}đ):`
                : `Không có phòng đắt hơn ở khu vực này rồi.`;
            break;
        }
        case 'other': {
            const excludeIds = chatbotContext.lastResults.map(r => r.id);
            results = data.filter(r => !excludeIds.includes(r.id));
            if (chatbotContext.locations.length > 0) {
                results = results.filter(r => {
                    const s = normalizeText(`${r.area_name} ${r.address}`);
                    return chatbotContext.locations.some(l => s.includes(l));
                });
            }
            if (chatbotContext.maxPrice) results = results.filter(r => r.price <= chatbotContext.maxPrice);
            results.sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating));
            results = results.slice(0, 5);
            msg = results.length > 0
                ? `🔄 Đây là các phòng <strong>khác</strong> phù hợp:`
                : `Hết phòng khác ở khu vực này rồi. Thử khu vực mới nhé?`;
            break;
        }
        case 'sameArea': {
            if (chatbotContext.locations.length === 0) {
                return { text: `📍 Bạn chưa chọn khu vực nào trước đó. Hãy cho mình biết khu vực bạn muốn tìm!`, rooms: [] };
            }
            results = data.filter(r => {
                const s = normalizeText(`${r.area_name} ${r.address} ${r.location_description}`);
                return chatbotContext.locations.some(l => s.includes(l));
            });
            results.sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating));
            results = results.slice(0, 5);
            msg = `📍 Tất cả phòng ở <strong>${chatbotContext.locations.join(', ')}</strong>:`;
            break;
        }
        case 'addBalcony': case 'addPets': case 'addLoft': case 'addWc': {
            const amenityMap = { addBalcony: 'balcony', addPets: 'pets', addLoft: 'loft', addWc: 'wc' };
            const amenityNames = { addBalcony: 'ban công', addPets: 'thú cưng', addLoft: 'gác lửng', addWc: 'WC riêng' };
            chatbotContext.amenities[amenityMap[type]] = true;
            results = chatbotSearchRooms(chatbotContext.lastQuery + ' ' + amenityNames[type]);
            msg = results.length > 0
                ? `🏠 Phòng có <strong>${amenityNames[type]}</strong>${chatbotContext.locations.length ? ' ở ' + chatbotContext.locations.join(', ') : ''}:`
                : `Không tìm thấy phòng có ${amenityNames[type]}${chatbotContext.locations.length ? ' ở ' + chatbotContext.locations.join(', ') : ''}.`;
            break;
        }
        case 'cheapest': {
            results = chatbotContext.lastResults.length > 0
                ? [...chatbotContext.lastResults].sort((a, b) => a.price - b.price).slice(0, 1)
                : data.sort((a, b) => a.price - b.price).slice(0, 3);
            msg = `💎 Phòng <strong>rẻ nhất</strong>:`;
            break;
        }
        case 'best': {
            results = chatbotContext.lastResults.length > 0
                ? [...chatbotContext.lastResults].sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating)).slice(0, 1)
                : data.sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating)).slice(0, 3);
            msg = `⭐ Phòng <strong>đánh giá cao nhất</strong>:`;
            break;
        }
        default: {
            results = chatbotContext.lastResults;
            msg = `Mình hiểu bạn muốn tìm thêm. Hãy thử mô tả cụ thể hơn nhé!`;
        }
    }

    chatbotContext.lastResults = results;
    if (results.length > 0) {
        return buildRoomResponse(results, msg);
    }
    return { text: msg, rooms: [] };
}

function buildRoomResponse(rooms, customMsg) {
    const areaNames = [...new Set(rooms.map(r => r.area_name).filter(Boolean))].join(', ');
    const prices = rooms.map(r => r.price);
    const minP = Math.min(...prices).toLocaleString('vi-VN');
    const maxP = Math.max(...prices).toLocaleString('vi-VN');
    const available = rooms.filter(r => r.status === 'empty').length;

    let text = '';
    
    // Nếu customMsg chứa HTML sẵn thì dùng, không thì tự dựng tiêu đề chuyên nghiệp
    if (typeof customMsg === 'string' && (customMsg.includes('<') || customMsg.includes('rẻ hơn') || customMsg.includes('cao cấp') || customMsg.includes('khác'))) {
        text = customMsg;
    } else {
        text = `🔍 <strong>Renty AI tìm thấy ${rooms.length} nơi ở phù hợp</strong>`;
        if (areaNames) text += ` tại khu vực <strong>${areaNames}</strong>`;
        text += `:<br>💰 Giá từ <strong>${minP}đ</strong> đến <strong>${maxP}đ</strong>/tháng.`;
        if (available > 0) text += `<br>✅ Hiện có <strong>${available}</strong> nơi đang trống sẵn sàng chuyển đến.`;
    }

    text += `<br><br>📝 <strong>Chi tiết các lựa chọn tốt nhất:</strong><br>`;
    
    rooms.forEach((r, idx) => {
        const ratingVal = r.rating ? parseFloat(r.rating).toFixed(1) : 'Chưa có';
        const ratingStar = ratingVal !== 'Chưa có' ? `⭐ ${ratingVal}/5` : '📝 Chưa có đánh giá';
        
        // Ghi nhận tiện ích nổi bật
        let amList = [];
        if (r.has_wc === 1 || r.has_wc === '1') amList.push('WC khép kín');
        if (r.has_balcony === 1 || r.has_balcony === '1') amList.push('Có ban công');
        if (r.has_loft === 1 || r.has_loft === '1') amList.push('Có gác lửng');
        if (r.allow_pets === 1 || r.allow_pets === '1') amList.push('Cho nuôi pet');
        const amString = amList.length > 0 ? amList.join(', ') : 'Đầy đủ điện nước';

        const priceText = parseInt(r.price).toLocaleString('vi-VN') + 'đ';

        text += `${idx + 1}️⃣ <strong>${escapeHtml(r.title)}</strong><br>` +
                `• 📍 Địa chỉ: ${escapeHtml(r.address || r.area_name)}<br>` +
                `• 💰 Giá: <strong>${priceText}</strong>/tháng<br>` +
                `• 🌿 Tiện ích: ${amString}<br>` +
                `• ${ratingStar} (Đánh giá thực tế từ người thuê trước)<br><br>`;
    });

    text += `👇 Bạn có thể click vào các thẻ nơi ở bên dưới để xem hình ảnh thực tế và liên hệ chủ nhà nhé!`;
    return { text, rooms };
}



// ── Send message handler ────────────────────────────────────────────
// ── Profanity Filter ────────────────────────────────────────────────
const RENTY_PROFANITY_LIST = [
    // Tiếng Việt - từ tục tĩu, xúc phạm phổ biến
    'dm', 'dcm', 'dkm', 'dtm', 'dmm', 'đm', 'đcm', 'đkm', 'đtm', 'đmm',
    'dit', 'dit me', 'đít', 'địt', 'địt mẹ', 'deo me', 'đéo mẹ',
    'cc', 'cac', 'cặc', 'buoi', 'buồi', 'lon', 'lol', 'lồn',
    'du ma', 'duma', 'đụ má', 'đụ mẹ', 'du me',
    'ngu', 'ngu vl', 'ngu vcl', 'ngu lắm',
    'vl', 'vcl', 'vkl', 'vleu',
    'clgt', 'cl', 'cln',
    'deo', 'đéo', 'đậu mẹ', 'đậu má',
    'thang cho', 'thằng chó', 'con cho', 'con chó',
    'mat day', 'mất dạy', 'vo hoc', 'vô học',
    'chan cho', 'chán chó', 'nhu cho', 'như chó',
    'thang ngu', 'thằng ngu', 'con ngu',
    'do ngoc', 'đồ ngốc', 'do ngu', 'đồ ngu',
    'do dien', 'đồ điên', 'thang dien', 'thằng điên',
    'xam loz', 'xạm lồn',
    // Tiếng Anh
    'fuck', 'shit', 'bitch', 'ass', 'dick', 'pussy', 'bastard', 'damn',
    'wtf', 'stfu', 'idiot', 'stupid'
];

function containsProfanity(text) {
    const norm = text.toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd')
        .replace(/[^a-z0-9\s]/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
    
    return RENTY_PROFANITY_LIST.some(word => {
        const normWord = word.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd');
        // Kiểm tra từ nguyên vẹn (word boundary) hoặc cụm từ
        const regex = new RegExp(`(?:^|\\s)${normWord.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}(?:\\s|$)`, 'i');
        return regex.test(norm) || norm === normWord;
    });
}

// ── Rate Limiter (localStorage) ─────────────────────────────────────
function getChatbotUsageToday() {
    const key = 'renty_chatbot_usage';
    const today = new Date().toISOString().slice(0, 10); // YYYY-MM-DD
    try {
        const stored = JSON.parse(localStorage.getItem(key) || '{}');
        if (stored.date !== today) {
            // Reset cho ngày mới
            const fresh = { date: today, count: 0 };
            localStorage.setItem(key, JSON.stringify(fresh));
            return fresh;
        }
        return stored;
    } catch {
        const fresh = { date: today, count: 0 };
        localStorage.setItem(key, JSON.stringify(fresh));
        return fresh;
    }
}

function incrementChatbotUsage() {
    const usage = getChatbotUsageToday();
    usage.count += 1;
    localStorage.setItem('renty_chatbot_usage', JSON.stringify(usage));
    return usage.count;
}

function getChatbotDailyLimit() {
    return window.rentyIsAuthenticated ? 50 : 10;
}

function getRemainingChatbotQuota() {
    const usage = getChatbotUsageToday();
    const limit = getChatbotDailyLimit();
    return Math.max(0, limit - usage.count);
}

// ── Send message handler (with profanity + rate limit) ──────────────
function sendRentyChatbotMessage(presetMsg) {
    if (rentyChatbotSending) return;

    const input = document.getElementById('chat_input') || document.getElementById('renty-chatbot-input');
    const text = presetMsg ? presetMsg.trim() : (input ? input.value.trim() : '');

    // DoD 1 & ERR_22_01: Kiểm tra rỗng -> rung viền đỏ ô input
    if (!text) {
        triggerEmptyChatError(input);
        return;
    }

    if (text.length > 500) {
        addBotMessage(
            `⚠️ <strong>Tin nhắn quá dài</strong><br><br>` +
            `Bạn vui lòng rút gọn câu hỏi còn tối đa <strong>500 ký tự</strong>. ` +
            `Ví dụ: <strong>"Tìm phòng Thủ Đức dưới 3 triệu"</strong>.`
        );
        return;
    }

    // Open panel if not open
    if (!rentyChatbotOpen) {
        toggleRentyChatbot();
    }

    // Clear input
    if (input && !presetMsg) input.value = '';

    // ── Kiểm tra từ ngữ tục tĩu ─────────────────────────────────────
    if (containsProfanity(text)) {
        addUserMessage(text);
        showTypingIndicator();
        setTimeout(() => {
            removeTypingIndicator();
            addBotMessage(
                `🚫 <strong>Tin nhắn bị chặn</strong><br><br>` +
                `Renty AI không hỗ trợ các tin nhắn chứa từ ngữ không phù hợp. ` +
                `Hãy sử dụng ngôn ngữ lịch sự để được hỗ trợ tốt nhất nhé! 🙏<br><br>` +
                `<em style="font-size:10px;color:#64748b;">Tin nhắn vi phạm sẽ không được xử lý và không tính vào lượt hỏi.</em>`
            );
        }, 300);
        return;
    }

    // ── Kiểm tra giới hạn lượt hỏi ──────────────────────────────────
    const remaining = getRemainingChatbotQuota();
    const limit = getChatbotDailyLimit();
    const isGuest = !window.rentyIsAuthenticated;

    if (remaining <= 0) {
        addUserMessage(text);
        showTypingIndicator();
        setTimeout(() => {
            removeTypingIndicator();
            const loginHint = isGuest
                ? `<br><br>💡 <strong>Mẹo:</strong> <a href="/login" style="color:#10b981;font-weight:700;text-decoration:underline;">Đăng nhập</a> để được nâng lên <strong>50 lượt/ngày</strong>!`
                : '';
            addBotMessage(
                `⏳ <strong>Đã hết lượt hỏi hôm nay</strong><br><br>` +
                `Bạn đã sử dụng hết <strong>${limit} lượt</strong> hỏi trong ngày. ` +
                `Giới hạn sẽ được đặt lại vào <strong>0:00 ngày mai</strong>.${loginHint}<br><br>` +
                `<em style="font-size:10px;color:#64748b;">Trong khi chờ, bạn vẫn có thể duyệt phòng trọ bằng bộ lọc ở trên!</em>`
            );
        }, 300);
        return;
    }

    // ── Tính lượt và gửi tin nhắn ────────────────────────────────────
    const currentCount = incrementChatbotUsage();
    const newRemaining = Math.max(0, limit - currentCount);

    // Add user message
    addUserMessage(text);

    // Show typing, then respond
    showTypingIndicator();
    setRentyChatbotBusy(true);

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    fetch('/renty/chatbot/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message: text, prompt: text })
    })
    .then(res => res.json().then(data => ({ ok: res.ok, status: res.status, data })).catch(() => ({
        ok: false,
        status: res.status,
        data: {}
    })))
    .then(({ ok, status, data }) => {
        removeTypingIndicator();
        setRentyChatbotBusy(false);
        
        let quotaNote = '';
        if (newRemaining <= 3 && newRemaining > 0) {
            quotaNote = `<br><br><em style="font-size:10px;color:#f59e0b;">⚠️ Còn ${newRemaining} lượt hỏi hôm nay${isGuest ? ' — <a href="/login" style="color:#10b981;font-weight:600;">đăng nhập</a> để có 50 lượt' : ''}.</em>`;
        }

        // Bắt lỗi rỗng từ server (ERR_22_01)
        if (status === 422 || data.error_code === 'ERR_22_01') {
            triggerEmptyChatError(input);
            return;
        }

        // Bắt lỗi câu hỏi không liên quan đến thuê phòng (ERR_22_03)
        if (data.error_code === 'ERR_22_03') {
            addBotMessage((data.response || data.message || 'Trợ lý ảo Renty chỉ hỗ trợ tư vấn thông tin thuê phòng và tiện ích lưu trú.') + quotaNote, []);
            return;
        }

        // Bắt lỗi mất kết nối máy chủ AI Chatbot (ERR_22_02)
        if (data.error_code === 'ERR_22_02' || (!ok && status >= 500)) {
            addBotErrorMessage(
                data.message || 'Không thể kết nối với máy chủ AI lúc này, vui lòng thử lại sau.',
                text
            );
            return;
        }

        if (ok && data.success) {
            addBotMessage((data.response || '') + quotaNote, data.rooms || []);
        } else {
            addBotErrorMessage(
                data.message || 'Không thể kết nối với máy chủ AI lúc này, vui lòng thử lại sau.',
                text
            );
        }
    })
    .catch(err => {
        console.error('Chatbot API error:', err);
        removeTypingIndicator();
        setRentyChatbotBusy(false);
        addBotErrorMessage(
            'Không thể kết nối với máy chủ AI lúc này, vui lòng thử lại sau.',
            text
        );
    });
}
window.sendRentyChatbotMessage = sendRentyChatbotMessage;


// ── Room Comparison Feature ──────────────────────────────────────────
let rentyCompareList = [];

// Khôi phục trạng thái so sánh từ sessionStorage nếu có
try {
    const saved = sessionStorage.getItem('renty_compare_list');
    if (saved) {
        rentyCompareList = JSON.parse(saved).map(id => parseInt(id)).filter(Boolean);
    }
} catch (e) {
    rentyCompareList = [];
}

function saveCompareState() {
    try {
        sessionStorage.setItem('renty_compare_list', JSON.stringify(rentyCompareList));
    } catch (e) {}
}

function syncCompareCheckboxes() {
    document.querySelectorAll('.compare-checkbox').forEach(cb => {
        let rId = parseInt(cb.value) || 
                  parseInt(cb.getAttribute('data-room-id')) || 
                  parseInt(cb.getAttribute('onchange')?.match(/\d+/)?.[0]);

        if (!rId) {
            const card = cb.closest('[data-room-id]') || cb.closest('.room-item-card') || cb.closest('[data-id]') || cb.closest('.room-card');
            if (card) {
                rId = parseInt(card.getAttribute('data-room-id') || card.getAttribute('data-id'));
            }
        }

        if (rId && rentyCompareList.includes(rId)) {
            cb.checked = true;
        } else {
            cb.checked = false;
        }
    });
}

function toggleCompare(arg1, arg2) {
    let roomId = null;
    let checkbox = null;

    if (arg1 && typeof arg1 === 'object' && arg1.tagName === 'INPUT') {
        checkbox = arg1;
        roomId = parseInt(arg2);
    } else {
        roomId = parseInt(arg1);
        checkbox = (arg2 && typeof arg2 === 'object' && arg2.tagName === 'INPUT') ? arg2 : null;
    }

    if (!roomId) return;

    if (checkbox && checkbox.checked) {
        if (rentyCompareList.length >= 3) {
            checkbox.checked = false;
            const notify = window.showRentyToast || alert;
            notify('Bạn chỉ có thể so sánh đối đầu tối đa 3 phòng cùng lúc.', 'warning', 'ERR_23_01');
            return;
        }
        if (!rentyCompareList.includes(roomId)) {
            rentyCompareList.push(roomId);
        }
    } else if (checkbox && !checkbox.checked) {
        rentyCompareList = rentyCompareList.filter(id => id !== roomId);
    } else {
        if (rentyCompareList.includes(roomId)) {
            rentyCompareList = rentyCompareList.filter(id => id !== roomId);
        } else {
            if (rentyCompareList.length >= 3) {
                const notify = window.showRentyToast || alert;
                notify('Bạn chỉ có thể so sánh đối đầu tối đa 3 phòng cùng lúc.', 'warning', 'ERR_23_01');
                return;
            }
            rentyCompareList.push(roomId);
        }
    }

    saveCompareState();
    updateCompareBar();
    syncCompareCheckboxes();
}
window.toggleCompare = toggleCompare;

function handleCompareCheck(checkbox, roomId) {
    toggleCompare(roomId, checkbox);
}
window.handleCompareCheck = handleCompareCheck;
window.openCompareModal = showCompareModal;
window.closeCompareModal = hideCompareModal;
window.clearCompare = clearCompareList;

function removeCompareItem(roomId) {
    roomId = parseInt(roomId);
    rentyCompareList = rentyCompareList.filter(id => id !== roomId);
    saveCompareState();
    updateCompareBar();
    syncCompareCheckboxes();

    if (window.showRentyToast) {
        window.showRentyToast('Đã xóa phòng khỏi danh sách so sánh.', 'success', 'ERR_23_03');
    }

    if (rentyCompareList.length < 2) {
        hideCompareModal();
    } else {
        showCompareModal();
    }
}
window.removeCompareItem = removeCompareItem;

function clearCompareList() {
    rentyCompareList = [];
    saveCompareState();
    document.querySelectorAll('.compare-checkbox').forEach(cb => cb.checked = false);
    updateCompareBar();
}
window.clearCompareList = clearCompareList;

function formatComparePriceCompact(price) {
    if (!price && price !== 0) return 'Thỏa thuận';
    const num = Number(price);
    if (isNaN(num)) return String(price);
    if (num >= 1000000) {
        const tr = (num / 1000000).toFixed(1).replace(/\.0$/, '');
        return `${tr}Tr`;
    }
    if (num >= 1000) {
        return `${Math.round(num / 1000)}k`;
    }
    return `${num}đ`;
}
window.formatComparePriceCompact = formatComparePriceCompact;

function getCompareRoomInfo(roomId) {
    const id = parseInt(roomId);
    if (window.rentyRoomsData && window.rentyRoomsData[id]) {
        const r = window.rentyRoomsData[id];
        return {
            id: id,
            title: r.title || `Phòng ${id}`,
            price: r.price
        };
    }
    const cb = document.querySelector(`.compare-checkbox[data-room-id="${id}"], .compare-checkbox[value="${id}"]`);
    if (cb && cb.dataset) {
        const title = cb.dataset.roomTitle || cb.dataset.title;
        const price = cb.dataset.roomPrice || cb.dataset.price;
        if (title) {
            return {
                id: id,
                title: title,
                price: price
            };
        }
    }
    const card = document.querySelector(`.room-item-card[data-room-id="${id}"], [data-room-id="${id}"]`);
    if (card && card.dataset) {
        const title = card.dataset.title || card.querySelector('h3, h4')?.textContent?.trim();
        const price = card.dataset.price;
        if (title) {
            return {
                id: id,
                title: title,
                price: price
            };
        }
    }
    return {
        id: id,
        title: `Phòng #${id}`,
        price: null
    };
}
window.getCompareRoomInfo = getCompareRoomInfo;

function updateCompareBar() {
    const bar = document.getElementById('renty-compare-bar');
    if (!bar) return;

    const itemsContainer = document.getElementById('compare-bar-items');
    const btnCount = document.getElementById('compare-btn-count');
    const badge = document.getElementById('compare-count-badge');

    const count = rentyCompareList.length;

    if (btnCount) btnCount.textContent = count;
    if (badge) badge.textContent = count;

    if (count === 0) {
        bar.classList.add('translate-y-28', 'opacity-0', 'pointer-events-none');
        bar.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
        if (itemsContainer) itemsContainer.innerHTML = '';
        return;
    }

    bar.classList.remove('translate-y-28', 'opacity-0', 'pointer-events-none');
    bar.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');

    if (itemsContainer) {
        itemsContainer.innerHTML = rentyCompareList.map(roomId => {
            const room = getCompareRoomInfo(roomId);
            const priceText = formatComparePriceCompact(room.price);
            const escapedTitle = (room.title || '').replace(/"/g, '&quot;');
            return `
                <div class="compare-item-chip flex items-center gap-2.5 px-3.5 py-1.5 rounded-xl bg-[#141824] border border-zinc-800 hover:border-zinc-700 max-w-[210px] sm:max-w-[270px] shrink-0 transition-all cursor-pointer group" onclick="removeCompareItem(${roomId})" title="Bấm để bỏ chọn ${escapedTitle}">
                    <span class="w-2 h-2 rounded-full bg-white shrink-0 group-hover:bg-rose-400 transition-colors" title="Đang chọn"></span>
                    <div class="flex flex-col text-left leading-tight truncate">
                        <span class="text-[12px] sm:text-[13px] font-bold text-white truncate group-hover:text-emerald-300 transition-colors">${room.title}</span>
                        <span class="text-[11px] sm:text-[12px] font-bold text-zinc-300">(${priceText})</span>
                    </div>
                </div>
            `;
        }).join('');
    }
}
window.updateCompareBar = updateCompareBar;

function renderCompareRadarChart(rooms) {
    let container = document.getElementById('compare-chart-container');
    if (!container) {
        const canvas = document.getElementById('compareRadarCanvas');
        if (canvas && canvas.parentElement) {
            container = canvas.parentElement;
            container.id = 'compare-chart-container';
        }
    }
    if (!container) {
        console.warn('[Room Compare] compare-chart-container not found in DOM.');
        return;
    }

    if (!Array.isArray(rooms) || rooms.length === 0) {
        container.innerHTML = `<div class="text-xs text-slate-400 py-6 text-center">Chưa có dữ liệu phòng để vẽ biểu đồ đối chiếu.</div>`;
        return;
    }

    const isLight = document.body.classList.contains('theme-light') || 
                    document.documentElement.classList.contains('theme-light') || 
                    localStorage.getItem('renty_theme_mode') === 'light';

    const textColor = isLight ? '#1e293b' : '#e2e8f0';
    const subTextColor = isLight ? '#64748b' : '#94a3b8';
    const gridColor = isLight ? 'rgba(100, 116, 139, 0.22)' : 'rgba(148, 163, 184, 0.18)';
    const axisColor = isLight ? 'rgba(100, 116, 139, 0.35)' : 'rgba(148, 163, 184, 0.3)';

    const palette = [
        { stroke: '#10b981', fill: 'rgba(16, 185, 129, 0.22)', point: '#059669', badgeBg: 'bg-emerald-500/15', text: 'text-emerald-500' },
        { stroke: '#6366f1', fill: 'rgba(99, 102, 241, 0.22)', point: '#4f46e5', badgeBg: 'bg-indigo-500/15', text: 'text-indigo-400' },
        { stroke: '#f59e0b', fill: 'rgba(245, 158, 11, 0.22)', point: '#d97706', badgeBg: 'bg-amber-500/15', text: 'text-amber-400' }
    ];

    const labels = [
        'Giá thuê rẻ',
        'Diện tích rộng',
        'Nhiều tiện nghi',
        'Đánh giá cao',
        'Tiết kiệm điện nước'
    ];

    const N = labels.length;
    const cx = 200;
    const cy = 135;
    const R = 85;

    // 1. Vẽ các vòng lưới đa giác (20%, 40%, 60%, 80%, 100%)
    let gridSvg = '';
    const levels = [0.2, 0.4, 0.6, 0.8, 1.0];
    levels.forEach((lvl, idx) => {
        const points = [];
        for (let i = 0; i < N; i++) {
            const angle = -Math.PI / 2 + (i * 2 * Math.PI) / N;
            const x = cx + R * lvl * Math.cos(angle);
            const y = cy + R * lvl * Math.sin(angle);
            points.push(`${x.toFixed(1)},${y.toFixed(1)}`);
        }
        const isOuter = idx === levels.length - 1;
        gridSvg += `<polygon points="${points.join(' ')}" fill="${isOuter ? (isLight ? 'rgba(241, 245, 249, 0.4)' : 'rgba(15, 23, 42, 0.3)') : 'none'}" stroke="${gridColor}" stroke-width="${isOuter ? '1.5' : '1'}" stroke-dasharray="${isOuter ? 'none' : '3,3'}" />`;
    });

    // 2. Vẽ 5 trục tỏa ra từ tâm và các nhãn trục
    let axesSvg = '';
    let labelsSvg = '';
    for (let i = 0; i < N; i++) {
        const angle = -Math.PI / 2 + (i * 2 * Math.PI) / N;
        const xOuter = cx + R * Math.cos(angle);
        const yOuter = cy + R * Math.sin(angle);
        axesSvg += `<line x1="${cx}" y1="${cy}" x2="${xOuter.toFixed(1)}" y2="${yOuter.toFixed(1)}" stroke="${axisColor}" stroke-width="1.2" />`;

        // Tính vị trí đặt chữ
        const labelR = R + 22;
        const lx = cx + labelR * Math.cos(angle);
        let ly = cy + labelR * Math.sin(angle);
        if (i === 0) ly -= 4;
        else if (i === 2 || i === 3) ly += 10;
        else ly += 4;

        let textAnchor = 'middle';
        if (Math.cos(angle) > 0.3) textAnchor = 'start';
        else if (Math.cos(angle) < -0.3) textAnchor = 'end';

        labelsSvg += `<text x="${lx.toFixed(1)}" y="${ly.toFixed(1)}" text-anchor="${textAnchor}" fill="${textColor}" font-size="10.5" font-weight="700" font-family="system-ui, -apple-system, sans-serif">${labels[i]}</text>`;
    }

    // 3. Vẽ đa giác dữ liệu cho từng phòng
    let roomsSvg = '';
    let legendItems = '';

    rooms.forEach((room, idx) => {
        const theme = palette[idx % palette.length];
        const scores = room.radar_scores || {
            price: 70,
            area: 70,
            amenity: 70,
            rating: 70,
            economic: 70
        };

        const scoreValues = [
            Number.isFinite(scores.price) ? Number(scores.price) : 70,
            Number.isFinite(scores.area) ? Number(scores.area) : 70,
            Number.isFinite(scores.amenity) ? Number(scores.amenity) : 70,
            Number.isFinite(scores.rating) ? Number(scores.rating) : 70,
            Number.isFinite(scores.economic) ? Number(scores.economic) : 70
        ];

        const polygonPoints = [];
        let pointsCircles = '';

        for (let i = 0; i < N; i++) {
            const angle = -Math.PI / 2 + (i * 2 * Math.PI) / N;
            const currentR = Math.max(8, Math.min(R, (scoreValues[i] / 100) * R));
            const px = cx + currentR * Math.cos(angle);
            const py = cy + currentR * Math.sin(angle);

            polygonPoints.push(`${px.toFixed(1)},${py.toFixed(1)}`);
            pointsCircles += `<circle cx="${px.toFixed(1)}" cy="${py.toFixed(1)}" r="4.5" fill="${theme.stroke}" stroke="#ffffff" stroke-width="1.8" />`;
        }

        roomsSvg += `
            <g class="radar-room-${room.id}">
                <polygon points="${polygonPoints.join(' ')}" fill="${theme.fill}" stroke="${theme.stroke}" stroke-width="2.5" stroke-linejoin="round" />
                ${pointsCircles}
            </g>
        `;

        legendItems += `
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900/10 border border-slate-700/20 text-xs font-extrabold shadow-sm" style="color: ${theme.stroke}">
                <span class="w-3 h-3 rounded-full" style="background-color: ${theme.stroke}"></span>
                <span>P.${room.room_number}</span>
                <span class="text-[10px] font-medium" style="color: ${subTextColor}">(${room.building_name})</span>
            </div>
        `;
    });

    container.innerHTML = `
        <div class="w-full flex flex-col items-center animate-fade-in py-2">
            <div class="flex flex-wrap items-center justify-center gap-2.5 mb-3">
                ${legendItems}
            </div>
            <div class="w-full max-w-md flex justify-center">
                <svg viewBox="0 0 400 270" width="100%" height="260" class="overflow-visible select-none drop-shadow-md" style="max-width: 440px; height: 260px;">
                    ${gridSvg}
                    ${axesSvg}
                    ${roomsSvg}
                    ${labelsSvg}
                </svg>
            </div>
        </div>
    `;
}
window.renderCompareRadarChart = renderCompareRadarChart;

async function showCompareModal() {
    const modal = document.getElementById('renty-compare-modal');
    const table = document.getElementById('tblCompare') || document.getElementById('compare-table');
    if (!modal) return;

    // Kịch bản xử lý lỗi theo Báo cáo: Cần tối thiểu 2 phòng để so sánh
    if (rentyCompareList.length < 2) {
        const notify = window.showRentyToast || alert;
        notify('Vui lòng chọn ít nhất 2 phòng để tiến hành so sánh đối đầu.', 'warning', 'ERR_23_02');
        return;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    if (table) {
        table.innerHTML = `
            <tbody>
                <tr>
                    <td colspan="${rentyCompareList.length + 1}" class="py-16 text-center">
                        <div class="inline-flex flex-col items-center gap-3">
                            <div class="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-400 rounded-full animate-spin"></div>
                            <span class="text-xs font-bold text-slate-300">Đang đối chiếu thông số các phòng đã chọn...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        `;
    }

    // Reset AI Box
    const aiBox = document.getElementById('compare-ai-box');
    if (aiBox) aiBox.classList.add('hidden');

    try {
        const res = await fetch('/api/renty/rooms/compare', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ room_ids: rentyCompareList })
        });

        const data = await res.json();
        if (!res.ok || !data.success || !Array.isArray(data.comparison)) {
            throw new Error(data.message || 'Không thể lấy dữ liệu so sánh.');
        }

        const rooms = data.comparison;
        renderComparisonTable(rooms);

    } catch (err) {
        if (table) {
            table.innerHTML = `
                <tbody>
                    <tr>
                        <td class="py-8 text-center text-rose-400 text-xs font-bold">
                            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> ${err.message}
                        </td>
                    </tr>
                </tbody>
            `;
        }
    }
}
window.showCompareModal = showCompareModal;

function formatRoomAmenities(room) {
    const list = [];
    const chk = room.amenities_checklist || {};
    if (chk.air_conditioner) list.push('Máy lạnh');
    if (chk.balcony) list.push('Ban công');
    if (chk.loft) list.push('Gác lửng');
    if (chk.wc_private) list.push('WC khép kín');
    if (chk.water_heater) list.push('Nóng lạnh');
    if (chk.elevator) list.push('Thang máy');
    if (chk.pets) list.push('Cho nuôi pet');
    if (chk.fingerprint_lock) list.push('Khóa vân tay');

    if (list.length === 0 && Array.isArray(room.amenities) && room.amenities.length > 0) {
        return room.amenities.join(', ');
    }
    return list.length > 0 ? list.join(', ') : 'Đang cập nhật';
}

function renderComparisonTable(rooms) {
    const table = document.getElementById('tblCompare') || document.getElementById('compare-table');
    if (!table) return;

    let html = `
        <thead>
            <tr class="bg-slate-900/90 border-b border-slate-800 text-xs">
                <th class="px-5 py-4 font-bold text-slate-300 w-1/4">Tiêu chí</th>
    `;

    rooms.forEach(room => {
        html += `
            <th class="px-5 py-4 font-bold text-white text-xs relative group text-left min-w-[200px]">
                <div class="flex items-start justify-between gap-2.5">
                    <span class="whitespace-normal break-words leading-relaxed text-slate-100 font-bold" title="Phòng ${room.room_number} – ${room.building_name}">Phòng ${room.room_number} – ${room.building_name}</span>
                    <button type="button" onclick="removeCompareItem(${room.id})" title="Gỡ phòng khỏi bảng so sánh" class="btnRemoveRoom w-5 h-5 rounded-md bg-slate-800 hover:bg-rose-500 text-slate-400 hover:text-white flex items-center justify-center transition-all opacity-80 hover:opacity-100 shrink-0 mt-0.5">
                        <span class="text-xs leading-none">&times;</span>
                    </button>
                </div>
            </th>
        `;
    });

    html += `
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/80 text-xs">
            <!-- Row 1: Giá thuê (tháng) -->
            <tr class="spec_rows hover:bg-slate-900/30 transition-all">
                <td class="px-5 py-4 font-semibold text-slate-400">Giá thuê (tháng)</td>
                ${rooms.map(r => `
                    <td class="px-5 py-4 font-bold text-white text-xs">
                        ${Number(r.price).toLocaleString('vi-VN')} VNĐ
                    </td>
                `).join('')}
            </tr>
            <!-- Row 2: Diện tích -->
            <tr class="spec_rows hover:bg-slate-900/30 transition-all">
                <td class="px-5 py-4 font-semibold text-slate-400">Diện tích</td>
                ${rooms.map(r => `
                    <td class="px-5 py-4 text-slate-200">
                        ${r.area} m²
                    </td>
                `).join('')}
            </tr>
            <!-- Row 3: Tiền cọc -->
            <tr class="spec_rows hover:bg-slate-900/30 transition-all">
                <td class="px-5 py-4 font-semibold text-slate-400">Tiền cọc</td>
                ${rooms.map(r => `
                    <td class="px-5 py-4 text-slate-200">
                        ${r.deposit && r.deposit !== r.price ? Number(r.deposit).toLocaleString('vi-VN') + ' VNĐ' : '1 tháng'}
                    </td>
                `).join('')}
            </tr>
            <!-- Row 4: Tiện nghi -->
            <tr class="spec_rows hover:bg-slate-900/30 transition-all">
                <td class="px-5 py-4 font-semibold text-slate-400">Tiện nghi</td>
                ${rooms.map(r => `
                    <td class="px-5 py-4 text-slate-300 leading-relaxed text-[11px]">
                        ${formatRoomAmenities(r)}
                    </td>
                `).join('')}
            </tr>
            <!-- Row 5: Đánh giá trung bình -->
            <tr class="spec_rows hover:bg-slate-900/30 transition-all">
                <td class="px-5 py-4 font-semibold text-slate-400">Đánh giá trung bình</td>
                ${rooms.map(r => `
                    <td class="px-5 py-4 text-slate-200 font-medium">
                        ${r.rating_avg} ⭐ <span class="text-slate-400 text-[11px]">(${r.reviews_count || 0} reviews)</span>
                    </td>
                `).join('')}
            </tr>
        </tbody>
    `;

    table.innerHTML = html;
}

function hideCompareModal() {
    const modal = document.getElementById('renty-compare-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
window.hideCompareModal = hideCompareModal;

async function generateAiComparison() {
    if (rentyCompareList.length < 2) return;

    const aiBox = document.getElementById('compare-ai-box');
    const aiContent = document.getElementById('compare-ai-content');
    const aiBtn = document.getElementById('compare-ai-btn');

    if (!aiBox || !aiContent) return;

    aiBox.classList.remove('hidden');
    aiContent.innerHTML = `<div class="flex items-center gap-2 text-emerald-400 py-1"><i class="fa-solid fa-circle-notch fa-spin text-xs"></i> <span>Đang kết nối Gemini AI phân tích đối chiếu chuyên sâu...</span></div>`;

    try {
        const res = await fetch('/api/renty/rooms/compare-ai', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ room_ids: rentyCompareList })
        });

        const data = await res.json();
        if (!res.ok || !data.success || !data.insight) {
            throw new Error(data.message || 'Không thể tạo nhận định AI.');
        }

        const insight = data.insight;
        const recommendationsList = Array.isArray(insight.recommendations) 
            ? insight.recommendations.map(r => `<li>${r}</li>`).join('')
            : '';

        aiContent.innerHTML = `
            <div class="space-y-2">
                <p class="font-semibold text-slate-200">${insight.summary}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[10px] my-1.5">
                    <div class="p-2 rounded-xl bg-emerald-950/30 border border-emerald-500/20 text-emerald-300">
                        <strong>💰 Tối ưu kinh tế:</strong> ${insight.best_economic || 'Đang cập nhật'}
                    </div>
                    <div class="p-2 rounded-xl bg-indigo-950/30 border border-indigo-500/20 text-indigo-300">
                        <strong>📐 Rộng rãi nhất:</strong> ${insight.best_space || 'Đang cập nhật'}
                    </div>
                </div>
                ${recommendationsList ? `
                    <ul class="list-disc pl-4 space-y-1 text-slate-300">
                        ${recommendationsList}
                    </ul>
                ` : ''}
                <div class="pt-1.5 border-t border-slate-800/80 text-emerald-400 font-medium">
                    🎯 <strong>Lời khuyên tổng kết:</strong> ${insight.verdict || ''}
                </div>
            </div>
        `;

    } catch (err) {
        aiContent.innerHTML = `<span class="text-rose-400"><i class="fa-solid fa-circle-exclamation mr-1"></i> ${err.message}</span>`;
    }
}
window.generateAiComparison = generateAiComparison;

function copyCompareShareLink() {
    if (rentyCompareList.length < 2) {
        const notify = window.showRentyToast || alert;
        notify('Vui lòng chọn ít nhất 2 phòng để tạo link chia sẻ.', 'warning', 'Chia sẻ');
        return;
    }

    const shareUrl = new URL(window.location.origin + '/renty');
    shareUrl.searchParams.set('compare', rentyCompareList.join(','));

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(shareUrl.toString()).then(() => {
            const notify = window.showRentyToast || alert;
            notify('Đã sao chép link so sánh vào bộ nhớ tạm! Bạn có thể gửi cho bạn bè.', 'success', 'Chia sẻ thành công');
        }).catch(() => {
            prompt('Sao chép liên kết so sánh dưới đây:', shareUrl.toString());
        });
    } else {
        prompt('Sao chép liên kết so sánh dưới đây:', shareUrl.toString());
    }
}
window.copyCompareShareLink = copyCompareShareLink;

function checkCompareUrlParams() {
    const urlParams = new URLSearchParams(window.location.search);
    const compareParam = urlParams.get('compare');
    if (!compareParam) return;

    const ids = compareParam.split(',')
        .map(id => parseInt(id.trim()))
        .filter(id => !isNaN(id) && id > 0)
        .slice(0, 3);

    if (ids.length >= 2) {
        rentyCompareList = ids;
        saveCompareState();
        updateCompareBar();
        syncCompareCheckboxes();

        setTimeout(() => {
            showCompareModal();
        }, 300);
    }
}

// Tự động đồng bộ trạng thái khi tải trang
document.addEventListener('DOMContentLoaded', () => {
    updateCompareBar();
    syncCompareCheckboxes();
    checkCompareUrlParams();
    renderViewedRooms();

    // Khôi phục bộ lọc từ URL nếu người dùng truy cập link chia sẻ hoặc reload
    restoreFiltersFromUrl();
    const urlParams = new URLSearchParams(window.location.search);
    const hasSearchFilterParams = ['q', 'search', 'min_price', 'max_price', 'status', 'rating', 'pets', 'loft', 'balcony', 'wc', 'page'].some(k => urlParams.has(k));
    if (hasSearchFilterParams) {
        filterItems({ resetPage: false });
    }
});


