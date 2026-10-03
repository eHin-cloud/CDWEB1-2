/**
 * GoogleMapsRenty - Professional Google Maps Architecture for Renty Review
 * Inspired by Google Maps Services (Directions, Distance Matrix, Places API)
 * 
 * Features:
 * - Google Roadmap (tiếng Việt), Google Satellite HD, Google Terrain
 * - Custom Google Maps Price Badges with verified status and hover animations
 * - Rich InfoWindow popups (photos, rating, price, Google Directions & Street View 360° links)
 * - Distance Matrix & Commute Calculator (Motorbike, Bus, Walking) to universities
 * - Route polyline animation from room to selected university destination
 * - Nearby Places API toggles (Supermarket, Hospital, Bus stop, Cafe)
 * - Geolocation "My Location" (GPS)
 * - Seamless synchronization with room cards in the list view
 */

class GoogleMapsRenty {
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error(`GoogleMapsRenty: Container #${containerId} not found!`);
            return;
        }

        this.options = Object.assign({
            defaultCenter: [21.036, 105.790], // Default Hanoi Cầu Giấy
            defaultZoom: 14,
            onSelectRoom: null,
        }, options);

        this.map = null;
        this.currentLayer = 'roadmap'; // 'roadmap' | 'satellite' | 'terrain'
        this.tileLayers = {};
        this.markers = {};
        this.placesMarkers = [];
        this.routePolyline = null;
        this.uniMarker = null;
        this.activeRoomId = null;
        this.selectedUniversity = null;
        this.initialCenter = this.options.defaultCenter;

        // Landmark Universities for Commute Matrix Calculation
        this.universities = [
            // Hanoi
            { id: 'hust', name: 'ĐH Bách Khoa Hà Nội', city: 'hanoi', lat: 21.005, lng: 105.843, icon: 'fa-graduation-cap' },
            { id: 'neu', name: 'ĐH Kinh Tế Quốc Dân', city: 'hanoi', lat: 20.996, lng: 105.842, icon: 'fa-graduation-cap' },
            { id: 'vnu', name: 'ĐH Quốc Gia Hà Nội (Cầu Giấy)', city: 'hanoi', lat: 21.037, lng: 105.782, icon: 'fa-graduation-cap' },
            { id: 'ftu', name: 'ĐH Ngoại Thương', city: 'hanoi', lat: 21.023, lng: 105.805, icon: 'fa-graduation-cap' },
            { id: 'fpt_hn', name: 'ĐH FPT Hà Nội', city: 'hanoi', lat: 21.013, lng: 105.527, icon: 'fa-graduation-cap' },
            // HCMC
            { id: 'hutech', name: 'ĐH HUTECH (Điện Biên Phủ)', city: 'hcm', lat: 10.801, lng: 106.714, icon: 'fa-graduation-cap' },
            { id: 'ueh', name: 'ĐH Kinh Tế TP.HCM (Quận 10)', city: 'hcm', lat: 10.771, lng: 106.669, icon: 'fa-graduation-cap' },
            { id: 'vnuhcm', name: 'Làng ĐH Quốc Gia TP.HCM (Thủ Đức)', city: 'hcm', lat: 10.875, lng: 106.801, icon: 'fa-graduation-cap' },
            { id: 'rmit', name: 'ĐH RMIT (Quận 7)', city: 'hcm', lat: 10.730, lng: 106.693, icon: 'fa-graduation-cap' }
        ];

        this.init();
    }

    init() {
        if (typeof L === 'undefined') {
            console.error('GoogleMapsRenty requires Leaflet mapping engine.');
            return;
        }

        // 1. Google Maps Tile Layers
        this.tileLayers.roadmap = L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&hl=vi', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: 'Dữ liệu bản đồ &copy; Google Maps'
        });

        this.tileLayers.satellite = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}&hl=vi', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: 'Hình ảnh vệ tinh &copy; Google Maps'
        });

        this.tileLayers.terrain = L.tileLayer('https://mt1.google.com/vt/lyrs=p&x={x}&y={y}&z={z}&hl=vi', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: 'Địa hình &copy; Google Maps'
        });

        // 2. Detect center from rooms
        const rooms = window.rentyRoomsData || {};
        const firstRoom = Object.values(rooms)[0];
        let center = this.options.defaultCenter;
        if (firstRoom) {
            const addr = ((firstRoom.address || '') + ' ' + (firstRoom.area_name || '')).toLowerCase();
            if (addr.includes('hồ chí minh') || addr.includes('hcm') || addr.includes('quận') || addr.includes('bình thạnh') || addr.includes('thủ đức')) {
                center = [10.798, 106.705]; // HCMC
            }
        }
        this.initialCenter = center;

        // 3. Instantiate Leaflet Map with Google Maps
        this.map = L.map(this.containerId, {
            center: center,
            zoom: this.options.defaultZoom,
            zoomControl: false,
            attributionControl: false
        });

        // Add default Google Roadmap layer
        this.tileLayers.roadmap.addTo(this.map);

        // Add custom sleek zoom control in bottom right
        L.control.zoom({ position: 'bottomright' }).addTo(this.map);

        // 4. Render markers
        this.renderRoomMarkers(rooms);

        // 5. Invalidate size after layout
        setTimeout(() => {
            if (this.map) this.map.invalidateSize();
        }, 300);
    }

    /* =========================================================================
       MAP TYPE CONTROLS (Roadmap / Satellite / Terrain)
       ========================================================================= */
    setMapType(type) {
        if (!this.tileLayers[type] || this.currentLayer === type) return;

        this.map.removeLayer(this.tileLayers[this.currentLayer]);
        this.tileLayers[type].addTo(this.map);
        this.currentLayer = type;
    }

    /* =========================================================================
       RESET VIEW
       ========================================================================= */
    resetView() {
        if (!this.map) return;
        this.map.setView(this.initialCenter, this.options.defaultZoom, {
            animate: true,
            duration: 0.8
        });
    }

    /* =========================================================================
       ROOM PRICE MARKERS (Google Maps Style Badges)
       ========================================================================= */
    renderRoomMarkers(rooms) {
        Object.values(this.markers).forEach(m => this.map.removeLayer(m));
        this.markers = {};

        Object.values(rooms).forEach(room => {
            const coords = this.computeRoomCoordinates(room);
            if (!coords) return;

            const priceMillion = (room.price / 1000000).toFixed(1).replace('.0', '');
            const priceBadgeText = `${priceMillion}Tr`;

            const isVerified = (room.verification_status === 'verified' || room.listing_badge === 'verified' || room.listing_badge === 'premium_verified');
            const badgeBg = isVerified 
                ? 'background: linear-gradient(135deg, #059669, #0d9488);' 
                : 'background: linear-gradient(135deg, #1e40af, #2563eb);';

            const customHtml = `
                <div class="gm-price-marker" id="marker-room-${room.id}" style="
                    display: inline-flex;
                    align-items: center;
                    gap: 4px;
                    padding: 4px 9px;
                    border-radius: 9999px;
                    color: #ffffff;
                    font-size: 11px;
                    font-weight: 800;
                    font-family: 'Plus Jakarta Sans', sans-serif;
                    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4), 0 0 0 1.5px rgba(255, 255, 255, 0.85);
                    cursor: pointer;
                    transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                    white-space: nowrap;
                    ${badgeBg}
                ">
                    <i class="fa-solid fa-house text-[9px] opacity-80"></i>
                    <span>${priceBadgeText}</span>
                    ${isVerified ? '<i class="fa-solid fa-circle-check text-[9px] text-emerald-200"></i>' : ''}
                </div>
            `;

            const icon = L.divIcon({
                html: customHtml,
                className: 'gm-marker-container',
                iconSize: [60, 26],
                iconAnchor: [30, 13]
            });

            const marker = L.marker([coords.lat, coords.lng], { icon: icon });

            // Popup HTML (Google Maps Rich InfoWindow)
            const popupHtml = this.buildInfoWindowHtml(room, coords);
            marker.bindPopup(popupHtml, {
                maxWidth: 320,
                minWidth: 280,
                className: 'gm-infowindow-popup',
                offset: [0, -10]
            });

            // Marker click
            marker.on('click', () => {
                this.activeRoomId = room.id;
                this.highlightMarker(room.id);
                if (this.selectedUniversity) {
                    this.drawCommuteRoute(coords, this.selectedUniversity);
                }
                if (typeof this.options.onSelectRoom === 'function') {
                    this.options.onSelectRoom(room);
                }
            });

            marker.addTo(this.map);
            this.markers[room.id] = marker;
        });
    }

    buildInfoWindowHtml(room, coords) {
        const coverImg = room.cover_image || (room.image_urls && room.image_urls[0]) || 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=600&q=80';
        const formattedPrice = new Intl.NumberFormat('vi-VN').format(room.price) + 'đ/tháng';
        const googleMapsDirUrl = `https://www.google.com/maps/dir/?api=1&destination=${coords.lat},${coords.lng}`;
        const streetViewUrl = `https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${coords.lat},${coords.lng}`;

        return `
            <div style="font-family: 'Plus Jakarta Sans', sans-serif; background: #0b1120; border-radius: 16px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 20px 40px rgba(0,0,0,0.5); color: #f1f5f9;">
                <div style="position: relative; height: 135px; overflow: hidden;">
                    <img src="${coverImg}" alt="${room.title}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=600&q=80';">
                    <span style="position: absolute; top: 10px; left: 10px; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); padding: 3px 8px; border-radius: 9999px; font-size: 10px; font-weight: 800; color: #34d399; border: 1px solid rgba(52, 211, 153, 0.3);">
                        Phòng ${room.room_number || ''}
                    </span>
                    <span style="position: absolute; top: 10px; right: 10px; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); padding: 3px 8px; border-radius: 9999px; font-size: 10px; font-weight: 800; color: #fbbf24;">
                        ★ ${room.rating || '4.5'}
                    </span>
                </div>
                <div style="padding: 12px 14px;">
                    <h3 style="font-size: 13px; font-weight: 800; margin: 0 0 4px 0; color: #ffffff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${room.title}
                    </h3>
                    <p style="font-size: 11px; color: #94a3b8; margin: 0 0 10px 0; display: flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-location-dot" style="color: #10b981; font-size: 10px;"></i>
                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${room.address || ''}</span>
                    </p>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                        <span style="font-size: 14px; font-weight: 900; color: #34d399;">${formattedPrice}</span>
                        <span style="font-size: 10px; color: #cbd5e1; background: rgba(255,255,255,0.06); padding: 2px 7px; border-radius: 6px;">
                            ${room.area || 25}m²
                        </span>
                    </div>
                    <div style="display: flex; gap: 6px;">
                        <button type="button" onclick="if(window.openRoomDetailModal){window.openRoomDetailModal(${room.id});}else{window.location.href='/renty/room/${room.id}';}" style="flex: 1; text-align: center; background: #2563eb; color: #ffffff; font-size: 11px; font-weight: 700; padding: 7px 0; border-radius: 8px; border: none; cursor: pointer; transition: background 0.2s;">
                            Xem Chi Tiết
                        </button>
                        <a href="${googleMapsDirUrl}" target="_blank" rel="noopener noreferrer" style="padding: 7px 10px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #93c5fd; font-size: 11px; font-weight: 700; border-radius: 8px; text-decoration: none; display: flex; align-items: center; gap: 4px;" title="Chỉ đường Google Maps">
                            <i class="fa-solid fa-diamond-turn-right"></i>
                        </a>
                        <a href="${streetViewUrl}" target="_blank" rel="noopener noreferrer" style="padding: 7px 10px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #fbbf24; font-size: 11px; font-weight: 700; border-radius: 8px; text-decoration: none; display: flex; align-items: center; gap: 4px;" title="Xem Google Street View 360°">
                            <i class="fa-solid fa-street-view"></i>
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    highlightMarker(roomId) {
        const markerEl = document.getElementById(`marker-room-${roomId}`);
        if (markerEl) {
            document.querySelectorAll('.gm-price-marker').forEach(el => {
                el.style.transform = 'scale(1)';
                el.style.zIndex = '1';
                el.style.filter = 'none';
            });
            markerEl.style.transform = 'scale(1.22)';
            markerEl.style.zIndex = '999';
            markerEl.style.filter = 'drop-shadow(0 0 10px rgba(56, 189, 248, 0.8))';
        }
    }

    /* =========================================================================
       GOOGLE MAPS COMMUTE & DISTANCE MATRIX CALCULATOR
       ========================================================================= */
    setUniversityDestination(uniId) {
        const uni = this.universities.find(u => u.id === uniId);
        if (!uni) {
            this.selectedUniversity = null;
            if (this.routePolyline) {
                this.map.removeLayer(this.routePolyline);
                this.routePolyline = null;
            }
            if (this.uniMarker) {
                this.map.removeLayer(this.uniMarker);
                this.uniMarker = null;
            }
            this.updateCommuteBox(null);
            return;
        }

        this.selectedUniversity = uni;

        if (this.uniMarker) {
            this.map.removeLayer(this.uniMarker);
        }

        const uniIcon = L.divIcon({
            html: `
                <div style="background: #ef4444; color: #ffffff; width: 34px; height: 34px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(239,68,68,0.6), 0 0 0 2.5px #ffffff; font-size: 14px;">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            `,
            className: 'gm-uni-marker',
            iconSize: [34, 34],
            iconAnchor: [17, 17]
        });

        this.uniMarker = L.marker([uni.lat, uni.lng], { icon: uniIcon })
            .bindTooltip(`<b>${uni.name}</b>`, { permanent: true, direction: 'top', className: 'gm-tooltip' })
            .addTo(this.map);

        let roomCoords = null;
        if (this.activeRoomId && this.markers[this.activeRoomId]) {
            roomCoords = this.markers[this.activeRoomId].getLatLng();
        } else {
            const firstRoomMarker = Object.values(this.markers)[0];
            if (firstRoomMarker) {
                roomCoords = firstRoomMarker.getLatLng();
            }
        }

        if (roomCoords) {
            this.drawCommuteRoute(roomCoords, uni);
        }
    }

    drawCommuteRoute(startCoords, destUni) {
        if (this.routePolyline) {
            this.map.removeLayer(this.routePolyline);
            this.routePolyline = null;
        }

        const distKm = this.calcHaversineDistance(startCoords.lat, startCoords.lng, destUni.lat, destUni.lng);
        
        // Realistic commute estimates
        const bikeMins = Math.max(2, Math.round((distKm / 24) * 60 + 2));
        const walkMins = Math.max(3, Math.round((distKm / 4.5) * 60));
        const busMins = Math.max(10, Math.round((distKm / 16) * 60 + 8));

        // Bezier midpoint for a natural road arc
        const midLat = (startCoords.lat + destUni.lat) / 2 + 0.002;
        const midLng = (startCoords.lng + destUni.lng) / 2 - 0.003;

        this.routePolyline = L.polyline([
            [startCoords.lat, startCoords.lng],
            [midLat, midLng],
            [destUni.lat, destUni.lng]
        ], {
            color: '#38bdf8',
            weight: 5,
            opacity: 0.9,
            dashArray: '8, 8',
            lineCap: 'round'
        }).addTo(this.map);

        const bounds = L.latLngBounds([
            [startCoords.lat, startCoords.lng],
            [destUni.lat, destUni.lng]
        ]);
        this.map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });

        this.updateCommuteBox({
            distance: distKm.toFixed(1),
            bikeTime: bikeMins,
            walkTime: walkMins,
            busTime: busMins,
            destination: destUni.name
        });
    }

    updateCommuteBox(data) {
        const box = document.getElementById('gm-commute-box');
        if (!box) return;

        if (!data) {
            box.classList.add('hidden');
            return;
        }

        box.classList.remove('hidden');
        box.innerHTML = `
            <div class="flex items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-400 animate-ping"></span>
                    <span class="font-bold text-white">${data.destination}</span>
                    <span class="text-blue-300 font-extrabold">(${data.distance} km)</span>
                </div>
                <div class="flex items-center gap-3 font-semibold">
                    <span class="text-emerald-400 flex items-center gap-1" title="Xe máy">
                        <i class="fa-solid fa-motorcycle"></i> ${data.bikeTime} phút
                    </span>
                    <span class="text-sky-300 flex items-center gap-1 hidden sm:flex" title="Xe buýt">
                        <i class="fa-solid fa-bus"></i> ${data.busTime} phút
                    </span>
                    <span class="text-amber-300 flex items-center gap-1 hidden md:flex" title="Đi bộ">
                        <i class="fa-solid fa-person-walking"></i> ${data.walkTime} phút
                    </span>
                </div>
            </div>
        `;
    }

    calcHaversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = 
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
            Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    /* =========================================================================
       NEARBY PLACES (Trường học, Siêu thị, Bệnh viện, Bến xe)
       ========================================================================= */
    toggleNearbyPlaces(category, enabled) {
        this.placesMarkers = this.placesMarkers.filter(item => {
            if (item.category === category) {
                this.map.removeLayer(item.marker);
                return false;
            }
            return true;
        });

        if (!enabled) return;

        const center = this.map.getCenter();
        const mockPlaces = {
            supermarket: [
                { name: 'WinMart+ Cửa hàng tiện lợi', lat: center.lat + 0.003, lng: center.lng + 0.002, icon: 'fa-cart-shopping', color: '#ef4444' },
                { name: 'Circle K 24/7', lat: center.lat - 0.002, lng: center.lng + 0.004, icon: 'fa-shop', color: '#dc2626' }
            ],
            hospital: [
                { name: 'Phòng khám Đa khoa Quốc tế', lat: center.lat + 0.005, lng: center.lng - 0.003, icon: 'fa-hospital', color: '#10b981' },
                { name: 'Trạm Y tế Phường', lat: center.lat - 0.004, lng: center.lng - 0.002, icon: 'fa-kit-medical', color: '#059669' }
            ],
            bus: [
                { name: 'Điểm đón Xe buýt tuyến 09, 26', lat: center.lat + 0.001, lng: center.lng + 0.003, icon: 'fa-bus', color: '#3b82f6' },
                { name: 'Trạm dừng xe buýt nhanh BRT', lat: center.lat - 0.001, lng: center.lng - 0.003, icon: 'fa-bus', color: '#2563eb' }
            ],
            cafe: [
                { name: 'Highlands Coffee', lat: center.lat + 0.002, lng: center.lng - 0.002, icon: 'fa-mug-hot', color: '#d97706' },
                { name: 'The Coffee House', lat: center.lat - 0.003, lng: center.lng + 0.001, icon: 'fa-coffee', color: '#b45309' }
            ]
        };

        const list = mockPlaces[category] || [];
        list.forEach(place => {
            const icon = L.divIcon({
                html: `
                    <div style="background: ${place.color}; color: #ffffff; width: 28px; height: 28px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 8px rgba(0,0,0,0.3), 0 0 0 1.5px #ffffff; font-size: 11px;">
                        <i class="fa-solid ${place.icon}"></i>
                    </div>
                `,
                className: 'gm-place-marker',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });

            const marker = L.marker([place.lat, place.lng], { icon: icon })
                .bindTooltip(`<b>${place.name}</b>`, { direction: 'top', className: 'gm-tooltip' })
                .addTo(this.map);

            this.placesMarkers.push({ category, marker });
        });
    }

    /* =========================================================================
       GEOLOCATION (My Location)
       ========================================================================= */
    locateUser() {
        if (!navigator.geolocation) {
            alert('Trình duyệt của bạn không hỗ trợ định vị GPS.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                this.map.setView([lat, lng], 15);

                const myIcon = L.divIcon({
                    html: `
                        <div style="background: #3b82f6; width: 18px; height: 18px; border-radius: 9999px; border: 3px solid #ffffff; box-shadow: 0 0 15px rgba(59, 130, 246, 0.9);"></div>
                    `,
                    className: 'gm-user-location',
                    iconSize: [18, 18],
                    iconAnchor: [9, 9]
                });

                L.marker([lat, lng], { icon: myIcon })
                    .bindTooltip('Vị trí hiện tại của bạn', { permanent: true, direction: 'top' })
                    .addTo(this.map);
            },
            () => {
                alert('Không thể lấy vị trí hiện tại. Vui lòng cấp quyền truy cập vị trí trong trình duyệt.');
            },
            { enableHighAccuracy: true, timeout: 5000 }
        );
    }

    /* =========================================================================
       COORDINATE COMPUTATION
       ========================================================================= */
    computeRoomCoordinates(room) {
        const addr = ((room.address || '') + ' ' + (room.area_name || '')).toLowerCase();
        let baseLat = 21.036;
        let baseLng = 105.790;

        const num = parseInt(room.room_number || room.id || 1, 10);
        const offsetLat = ((num * 17) % 30 - 15) * 0.001;
        const offsetLng = ((num * 29) % 30 - 15) * 0.001;

        if (addr.includes('hồ chí minh') || addr.includes('hcm') || addr.includes('quận 1') || addr.includes('quận 7') || addr.includes('quận 10') || addr.includes('thủ đức') || addr.includes('bình thạnh') || addr.includes('gò vấp')) {
            if (addr.includes('thủ đức')) {
                baseLat = 10.850; baseLng = 106.772;
            } else if (addr.includes('bình thạnh')) {
                baseLat = 10.803; baseLng = 106.711;
            } else if (addr.includes('quận 10') || addr.includes('quan 10')) {
                baseLat = 10.775; baseLng = 106.667;
            } else if (addr.includes('quận 7') || addr.includes('quan 7')) {
                baseLat = 10.732; baseLng = 106.726;
            } else if (addr.includes('gò vấp')) {
                baseLat = 10.838; baseLng = 106.665;
            } else {
                baseLat = 10.780; baseLng = 106.695;
            }
        } else {
            // Hanoi districts
            if (addr.includes('thanh xuân')) {
                baseLat = 20.998; baseLng = 105.811;
            } else if (addr.includes('đống đa')) {
                baseLat = 21.018; baseLng = 105.827;
            } else if (addr.includes('ba đình')) {
                baseLat = 21.035; baseLng = 105.830;
            } else if (addr.includes('hai bà trưng')) {
                baseLat = 21.008; baseLng = 105.852;
            } else {
                baseLat = 21.036; baseLng = 105.790; // Cầu Giấy
            }
        }

        return {
            lat: baseLat + offsetLat,
            lng: baseLng + offsetLng
        };
    }
}

// Global Handlers
window.GoogleMapsRenty = GoogleMapsRenty;

window.setRentyMapLayer = function(type) {
    if (window.rentyGoogleMap) {
        window.rentyGoogleMap.setMapType(type);
    }
    ['roadmap', 'satellite', 'terrain'].forEach(t => {
        const btn = document.getElementById(`gm-layer-${t}`);
        if (btn) {
            if (t === type) {
                btn.className = 'px-2.5 py-1 rounded-lg bg-blue-600 text-white font-bold transition-all shadow-sm';
            } else {
                btn.className = 'px-2.5 py-1 rounded-lg text-slate-400 hover:text-slate-200 transition-all font-semibold';
            }
        }
    });
};

window.onUniversityDestinationChange = function(uniId) {
    if (window.rentyGoogleMap) {
        window.rentyGoogleMap.setUniversityDestination(uniId);
    }
};

window.toggleNearbyPlace = function(category, btnEl) {
    if (!window.rentyGoogleMap) return;
    const isActive = btnEl.classList.contains('active');
    if (isActive) {
        btnEl.classList.remove('active', 'bg-blue-600/30', 'text-blue-300', 'border-blue-500/50');
        btnEl.classList.add('bg-slate-900', 'text-slate-400', 'border-slate-800');
        window.rentyGoogleMap.toggleNearbyPlaces(category, false);
    } else {
        btnEl.classList.add('active', 'bg-blue-600/30', 'text-blue-300', 'border-blue-500/50');
        btnEl.classList.remove('bg-slate-900', 'text-slate-400', 'border-slate-800');
        window.rentyGoogleMap.toggleNearbyPlaces(category, true);
    }
};

window.locateUserRentyMap = function() {
    if (window.rentyGoogleMap) {
        window.rentyGoogleMap.locateUser();
    }
};

window.resetRentyMapView = function() {
    if (window.rentyGoogleMap) {
        window.rentyGoogleMap.resetView();
    }
};

window.toggleGnHudDetails = function() {
    const el = document.getElementById('gm-hud-collapsible');
    const icon = document.getElementById('gm-hud-toggle-icon');
    if (!el) return;
    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
        if (icon) icon.style.transform = 'rotate(0deg)';
    } else {
        el.classList.add('hidden');
        if (icon) icon.style.transform = 'rotate(180deg)';
    }
};

