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
        this.buildingMarkers = {};
        this.buildingMapByRoomId = {};
        this.placesMarkers = [];
        this.routePolyline = null;
        this.routeBadgeMarker = null;
        this.uniMarker = null;
        this.campusRadiusCircle = null;
        this.selectedCampusId = 'all';
        this.activeRoomId = null;
        this.selectedUniversity = null;
        this.initialCenter = this.options.defaultCenter;
        this.currentCity = 'all'; // 'all' | 'hcm' | 'hanoi'
        this.currentRadius = 3500; // default 3.5km
        this.activeCenter = null; // Tâm quét bán kính hiện tại (Trường, Du lịch, Quận, Địa điểm)

        // Landmark Universities & Colleges (Đại học & Cao đẳng đầy đủ 2 miền, đa cơ sở)
        this.universities = [
            // =========================================================================
            // =========================================================================
            // TP. HỒ CHÍ MINH - TRƯỜNG ĐẠI HỌC & CAO ĐẲNG (ƯU TIÊN LÊN ĐẦU)
            // =========================================================================
            {
                id: 'cd_tdc',
                type: 'college',
                name: 'CĐ Công Nghệ Thủ Đức (TDC)',
                shortName: 'CĐ Công Nghệ Thủ Đức',
                campus: '53 Võ Văn Ngân (Thủ Đức)',
                district: 'TP. Thủ Đức',
                address: '53 Võ Văn Ngân, Linh Chiểu, TP. Thủ Đức',
                fullName: 'Trường Cao đẳng Công nghệ Thủ Đức (TDC)',
                city: 'hcm',
                lat: 10.85142, lng: 106.75775,
                icon: 'fa-graduation-cap',
                aliases: ['tdc', 'cong nghe thu duc', 'cd cong nghe thu duc', 'cđ công nghệ thủ đức', 'cao dang cong nghe thu duc', 'vo van ngan', 'thu duc', 'truong cao dang', 'cd']
            },
            {
                id: 'sgu',
                type: 'university',
                name: 'ĐH Sài Gòn (SGU) - CS Quận 5',
                shortName: 'ĐH Sài Gòn (Q.5)',
                campus: '273 An Dương Vương (Q.5)',
                district: 'Quận 5',
                address: '273 An Dương Vương, P.3, Quận 5',
                fullName: 'Trường Đại học Sài Gòn (Cơ sở chính Quận 5)',
                city: 'hcm',
                lat: 10.7599, lng: 106.6823,
                icon: 'fa-graduation-cap',
                aliases: ['sgu', 'dai hoc sai gon', 'dh sai gon', 'đh sài gòn', 'an duong vuong', 'quan 5', 'q5', 'truong dai hoc']
            },
            {
                id: 'sgu_q3',
                type: 'university',
                name: 'ĐH Sài Gòn (SGU) - CS Quận 3',
                shortName: 'ĐH Sài Gòn (Q.3)',
                campus: '105 Bà Huyện Thanh Quan (Q.3)',
                district: 'Quận 3',
                address: '105 Bà Huyện Thanh Quan, P.7, Quận 3',
                fullName: 'Trường Đại học Sài Gòn (Cơ sở Quận 3)',
                city: 'hcm',
                lat: 10.7816, lng: 106.6853,
                icon: 'fa-graduation-cap',
                aliases: ['sgu q3', 'dh sai gon q3', 'ba huyen thanh quan', 'quan 3', 'q3', 'truong dai hoc']
            },
            {
                id: 'cd_hitc',
                type: 'college',
                name: 'CĐ Công Thương TP.HCM (HITC)',
                shortName: 'CĐ Công Thương',
                campus: '20 Tăng Nhơn Phú (Thủ Đức)',
                district: 'TP. Thủ Đức',
                address: '20 Tăng Nhơn Phú, Phước Long B, TP. Thủ Đức',
                fullName: 'Trường Cao đẳng Công Thương TP.HCM (HITC)',
                city: 'hcm',
                lat: 10.8306, lng: 106.7762,
                icon: 'fa-graduation-cap',
                aliases: ['hitc', 'cong thuong', 'cd cong thuong', 'cđ công thương', 'cao dang cong thuong', 'tang nhon phu', 'thu duc', 'quan 9', 'truong cao dang', 'cd']
            },
            {
                id: 'vnuhcm',
                type: 'university',
                name: 'Làng Đại Học Quốc Gia TP.HCM',
                shortName: 'Làng ĐH Thủ Đức',
                campus: 'Khu Đô Thị ĐHQG (Thủ Đức)',
                district: 'TP. Thủ Đức',
                address: 'Khu đô thị ĐHQG TP.HCM, Linh Trung, TP. Thủ Đức',
                fullName: 'Đại học Quốc gia TP.HCM (Làng ĐH Thủ Đức - KTX Khu A & B)',
                city: 'hcm',
                lat: 10.8754, lng: 106.8007,
                icon: 'fa-graduation-cap',
                aliases: ['lang dai hoc', 'lang dh', 'dhqg', 'vnuhcm', 'ktx khu a', 'ktx khu b', 'linh trung', 'uit', 'khtn', 'ussh', 'khoa hoc tu nhien', 'nhan van', 'cong nghe thong tin', 'truong dai hoc']
            },
            {
                id: 'hcmut',
                type: 'university',
                name: 'ĐH Bách Khoa TP.HCM (HCMUT) - CS Quận 10',
                shortName: 'Bách Khoa (Q.10)',
                campus: '268 Lý Thường Kiệt (Q.10)',
                district: 'Quận 10',
                address: '268 Lý Thường Kiệt, P.14, Quận 10',
                fullName: 'Trường Đại học Bách Khoa - Cơ sở 1 (Quận 10)',
                city: 'hcm',
                lat: 10.7725, lng: 106.6578,
                icon: 'fa-graduation-cap',
                aliases: ['bk', 'bktphcm', 'bach khoa', 'hcmut', 'ly thuong kiet', 'quan 10', 'q10', 'to hien thanh', 'truong bach khoa', 'dh bach khoa', 'truong dai hoc']
            },
            {
                id: 'hcmut_dian',
                type: 'university',
                name: 'ĐH Bách Khoa TP.HCM - CS2 Làng ĐH',
                shortName: 'Bách Khoa (CS2 Làng ĐH)',
                campus: 'Khu Đô Thị ĐHQG (Dĩ An / Thủ Đức)',
                district: 'TP. Thủ Đức',
                address: 'Khu đô thị ĐHQG TP.HCM, Đông Hòa, Dĩ An / TP. Thủ Đức',
                fullName: 'Trường Đại học Bách Khoa TP.HCM (Cơ sở 2)',
                city: 'hcm',
                lat: 10.8805, lng: 106.8055,
                icon: 'fa-graduation-cap',
                aliases: ['bk cs2', 'bktphcm cs2', 'bach khoa di an', 'bach khoa lang dai hoc', 'hcmut cs2', 'di an', 'thu duc']
            },
            {
                id: 'spkt',
                type: 'university',
                name: 'ĐH Sư Phạm Kỹ Thuật (HCMUTE)',
                shortName: 'ĐH SPKT (Thủ Đức)',
                campus: '01 Võ Văn Ngân (Thủ Đức)',
                district: 'TP. Thủ Đức',
                address: '01 Võ Văn Ngân, Linh Chiểu, TP. Thủ Đức',
                fullName: 'Trường Đại học Sư Phạm Kỹ Thuật TP.HCM',
                city: 'hcm',
                lat: 10.8505, lng: 106.7720,
                icon: 'fa-graduation-cap',
                aliases: ['spkt', 'ute', 'hcmute', 'su pham ky thuat', 'vo van ngan', 'nga tu thu duc', 'linh chieu', 'truong spkt', 'truong dai hoc']
            },
            {
                id: 'ueh',
                type: 'university',
                name: 'ĐH Kinh Tế TP.HCM (UEH) - CS Quận 10',
                shortName: 'Kinh Tế UEH (Q.10)',
                campus: '279 Nguyễn Tri Phương (Q.10)',
                district: 'Quận 10',
                address: '279 Nguyễn Tri Phương, P.5, Quận 10',
                fullName: 'Đại học Kinh Tế TP.HCM (Cơ sở B Quận 10)',
                city: 'hcm',
                lat: 10.7612, lng: 106.6675,
                icon: 'fa-graduation-cap',
                aliases: ['ueh', 'kinh te tphcm', 'nguyen tri phuong', 'quan 10', 'q10', 'nguyen dinh chieu', 'quan 3', 'truong ueh', 'truong dai hoc']
            },
            {
                id: 'ueh_binhchanh',
                type: 'university',
                name: 'ĐH Kinh Tế TP.HCM - CS Nam Sài Gòn',
                shortName: 'UEH (CS Nam Sài Gòn)',
                campus: 'Nguyễn Văn Linh (Bình Chánh)',
                district: 'Huyện Bình Chánh',
                address: 'Khu chức năng 15, Đô thị mới Nam TP, Nguyễn Văn Linh, Phong Phú, Bình Chánh',
                fullName: 'Đại học Kinh Tế TP.HCM (Cơ sở Nam Sài Gòn)',
                city: 'hcm',
                lat: 10.7180, lng: 106.6625,
                icon: 'fa-graduation-cap',
                aliases: ['ueh binh chanh', 'ueh nam sai gon', 'ueh nguyen van linh', 'binh chanh', 'phong phu']
            },
            {
                id: 'hutech',
                type: 'university',
                name: 'ĐH Công Nghệ TP.HCM (HUTECH) - Điện Biên Phủ',
                shortName: 'HUTECH (Điện Biên Phủ)',
                campus: '475A Điện Biên Phủ (Bình Thạnh)',
                district: 'Quận Bình Thạnh',
                address: '475A Điện Biên Phủ, P.25, Bình Thạnh',
                fullName: 'Trường Đại học Công Nghệ TP.HCM (HUTECH)',
                city: 'hcm',
                lat: 10.8013, lng: 106.7145,
                icon: 'fa-graduation-cap',
                aliases: ['hutech', 'dh cong nghe', 'cong nghe tphcm', 'dien bien phu', 'hang xanh', 'binh thanh', 'ung van khiem', 'truong hutech', 'dh hutech', 'truong dai hoc']
            },
            {
                id: 'hutech_cnc',
                type: 'university',
                name: 'ĐH HUTECH - CS Khu Công Nghệ Cao',
                shortName: 'HUTECH (Khu CNC Q.9)',
                campus: 'Khu Công Nghệ Cao (Thủ Đức)',
                district: 'TP. Thủ Đức',
                address: 'Lô E1, Phân khu Đào tạo, Khu CNC, P. Hiệp Phú, TP. Thủ Đức',
                fullName: 'Trường ĐH Công Nghệ TP.HCM (Trung tâm Đào tạo CNC)',
                city: 'hcm',
                lat: 10.8550, lng: 106.7865,
                icon: 'fa-graduation-cap',
                aliases: ['hutech cnc', 'hutech quan 9', 'hutech thu duc', 'khu cong nghe cao']
            },
            {
                id: 'tdtu',
                type: 'university',
                name: 'ĐH Tôn Đức Thắng (TDTU)',
                shortName: 'ĐH Tôn Đức Thắng',
                campus: '19 Nguyễn Hữu Thọ (Quận 7)',
                district: 'Quận 7',
                address: '19 Nguyễn Hữu Thọ, Tân Phong, Quận 7',
                fullName: 'Trường Đại học Tôn Đức Thắng (TDTU)',
                city: 'hcm',
                lat: 10.7325, lng: 106.6990,
                icon: 'fa-graduation-cap',
                aliases: ['tdtu', 'ton duc thang', 'nguyen huu tho', 'quan 7', 'q7', 'truong ton duc thang', 'truong dai hoc']
            },
            {
                id: 'rmit',
                type: 'university',
                name: 'ĐH RMIT Việt Nam (Quận 7)',
                shortName: 'ĐH RMIT Q.7',
                campus: '702 Nguyễn Văn Linh (Quận 7)',
                district: 'Quận 7',
                address: '702 Nguyễn Văn Linh, Tân Phong, Quận 7',
                fullName: 'Trường Đại học RMIT Việt Nam (Cơ sở Nam Sài Gòn)',
                city: 'hcm',
                lat: 10.7297, lng: 106.6934,
                icon: 'fa-graduation-cap',
                aliases: ['rmit', 'nguyen van linh', 'quan 7', 'q7', 'phu my hung', 'truong rmit', 'truong dai hoc']
            },
            {
                id: 'iuh',
                type: 'university',
                name: 'ĐH Công Nghiệp TP.HCM (IUH)',
                shortName: 'ĐH Công Nghiệp IUH',
                campus: '12 Nguyễn Văn Bảo (Gò Vấp)',
                district: 'Quận Gò Vấp',
                address: '12 Nguyễn Văn Bảo, P.4, Gò Vấp',
                fullName: 'Trường Đại học Công Nghiệp TP.HCM (IUH)',
                city: 'hcm',
                lat: 10.8222, lng: 106.6875,
                icon: 'fa-graduation-cap',
                aliases: ['iuh', 'cong nghiep tphcm', 'nguyen van bao', 'go vap', 'cho go vap', 'truong cong nghiep', 'truong dai hoc']
            },
            {
                id: 'vanlang',
                type: 'university',
                name: 'ĐH Văn Lang (VLU) - CS3 Bình Thạnh',
                shortName: 'Văn Lang (CS3 Bình Thạnh)',
                campus: '69/68 Đặng Thùy Trâm (Bình Thạnh)',
                district: 'Quận Bình Thạnh',
                address: '69/68 Đặng Thùy Trâm, P.13, Bình Thạnh',
                fullName: 'Trường Đại học Văn Lang (Cơ sở chính 3)',
                city: 'hcm',
                lat: 10.8278, lng: 106.6985,
                icon: 'fa-graduation-cap',
                aliases: ['van lang', 'vanlang', 'dang thuy tram', 'go vap', 'binh thanh', 'vlu', 'truong van lang', 'truong dai hoc']
            },
            {
                id: 'vanlang_cs1',
                type: 'university',
                name: 'ĐH Văn Lang - CS1 Quận 1',
                shortName: 'Văn Lang (CS1 Q.1)',
                campus: '45 Nguyễn Khắc Nhu (Quận 1)',
                district: 'Quận 1',
                address: '45 Nguyễn Khắc Nhu, P. Cô Giang, Quận 1',
                fullName: 'Trường Đại học Văn Lang (Cơ sở 1)',
                city: 'hcm',
                lat: 10.7588, lng: 106.6925,
                icon: 'fa-graduation-cap',
                aliases: ['van lang cs1', 'van lang quan 1', 'nguyen khac nhu', 'co giang', 'quan 1']
            },
            {
                id: 'khtn',
                type: 'university',
                name: 'ĐH Khoa Học Tự Nhiên (ĐHQG-HCM)',
                shortName: 'ĐH KHTN',
                campus: '227 Nguyễn Văn Cừ (Quận 5)',
                district: 'Quận 5',
                address: '227 Nguyễn Văn Cừ, Phường 4, Quận 5',
                fullName: 'Trường Đại học Khoa học Tự nhiên - ĐHQG TP.HCM',
                city: 'hcm',
                lat: 10.7628, lng: 106.6825,
                icon: 'fa-graduation-cap',
                aliases: ['khtn', 'khoa hoc tu nhien', 'nguyen van cu', 'quan 5', 'q5', 'truong khtn', 'dh khtn', 'truong dai hoc']
            },
            {
                id: 'ussh',
                type: 'university',
                name: 'ĐH Khoa Học Xã Hội & Nhân Văn',
                shortName: 'ĐH Nhân Văn',
                campus: '10-12 Đinh Tiên Hoàng (Quận 1)',
                district: 'Quận 1',
                address: '10-12 Đinh Tiên Hoàng, Bến Nghé, Quận 1',
                fullName: 'Trường ĐH Khoa học Xã hội và Nhân văn - ĐHQG TP.HCM',
                city: 'hcm',
                lat: 10.7865, lng: 106.7018,
                icon: 'fa-graduation-cap',
                aliases: ['nhan van', 'ussh', 'dinh tien hoang', 'quan 1', 'q1', 'truong nhan van', 'dh nhan van', 'truong dai hoc']
            },
            {
                id: 'uit',
                type: 'university',
                name: 'ĐH Công Nghệ Thông Tin (UIT)',
                shortName: 'ĐH CNTT',
                campus: 'Khu Đô Thị ĐHQG (Thủ Đức)',
                district: 'TP. Thủ Đức',
                address: 'Khu phố 6, P. Linh Trung, TP. Thủ Đức',
                fullName: 'Trường Đại học Công nghệ Thông tin - ĐHQG TP.HCM',
                city: 'hcm',
                lat: 10.8700, lng: 106.8030,
                icon: 'fa-graduation-cap',
                aliases: ['uit', 'cong nghe thong tin', 'dh cntt', 'truong uit', 'lang dai hoc', 'thu duc', 'truong dai hoc']
            },

            // =========================================================================
            // TP. HỒ CHÍ MINH - TRƯỜNG CAO ĐẲNG (CĐ)
            // =========================================================================
            {
                id: 'cd_caothang',
                type: 'college',
                name: 'CĐ Kỹ Thuật Cao Thắng',
                shortName: 'CĐ Cao Thắng',
                campus: '65 Huỳnh Thúc Kháng (Quận 1)',
                district: 'Quận 1',
                address: '65 Huỳnh Thúc Kháng, P. Bến Nghé, Quận 1',
                fullName: 'Trường Cao đẳng Kỹ thuật Cao Thắng',
                city: 'hcm',
                lat: 10.7719, lng: 106.7011,
                icon: 'fa-graduation-cap',
                aliases: ['cao thang', 'cd cao thang', 'cđ cao thắng', 'cao dang cao thang', 'kt cao thang', 'ckct', 'truong cao thang', 'quan 1', 'huynh thuc khang', 'truong cao dang', 'cd']
            },
            {
                id: 'cd_cofer',
                type: 'college',
                name: 'CĐ Kinh Tế Đối Ngoại (COFER)',
                shortName: 'CĐ KT Đối Ngoại',
                campus: '287 Phan Đình Phùng (Phú Nhuận)',
                district: 'Quận Phú Nhuận',
                address: '287 Phan Đình Phùng, P.15, Phú Nhuận',
                fullName: 'Trường Cao đẳng Kinh tế Đối ngoại',
                city: 'hcm',
                lat: 10.7963, lng: 106.6812,
                icon: 'fa-graduation-cap',
                aliases: ['cofer', 'kinh te doi ngoai', 'ktdn', 'cd kinh te doi ngoai', 'cđ kinh tế đối ngoại', 'cao dang kinh te doi ngoai', 'phu nhuan', 'phan dinh phung', 'tang nhon phu', 'thu duc', 'truong cao dang', 'cd']
            },

            {
                id: 'cd_fpt_hcm',
                type: 'college',
                name: 'CĐ FPT Polytechnic TP.HCM',
                shortName: 'CĐ FPT Poly',
                campus: 'CVPM Quang Trung (Quận 12)',
                district: 'Quận 12',
                address: 'Tòa nhà Innovation, CVPM Quang Trung, Tân Chánh Hiệp, Quận 12',
                fullName: 'Trường Cao đẳng FPT Polytechnic TP.HCM',
                city: 'hcm',
                lat: 10.8539, lng: 106.6272,
                icon: 'fa-graduation-cap',
                aliases: ['fpt poly', 'fpt polytechnic', 'poly', 'cd fpt', 'cđ fpt', 'cao dang fpt', 'cvpm quang trung', 'to ky', 'quan 12', 'truong cao dang', 'cd']
            },

            // =========================================================================
            // HÀ NỘI - TRƯỜNG ĐẠI HỌC (CHUẨN HÓA KHÔNG TRÙNG LẶP)
            // =========================================================================
            {
                id: 'vnu_hn',
                type: 'university',
                name: 'Đại học Quốc Gia Hà Nội (VNU) - CS Cầu Giấy',
                shortName: 'ĐHQG HN (Cầu Giấy)',
                campus: '144 Xuân Thủy (Cầu Giấy)',
                district: 'Quận Cầu Giấy',
                address: '144 Xuân Thủy, Dịch Vọng Hậu, Cầu Giấy, Hà Nội',
                fullName: 'Đại học Quốc gia Hà Nội (Cơ sở Cầu Giấy)',
                city: 'hanoi',
                lat: 21.0374, lng: 105.7825,
                icon: 'fa-graduation-cap',
                aliases: ['vnu', 'dhqg ha noi', 'dhqghn', 'xuan thuy', 'cau giay', 'uet', 'ulis', 'truong dhqg', 'truong dai hoc']
            },
            {
                id: 'vnu_hn_hoalac',
                type: 'university',
                name: 'ĐHQG Hà Nội - CS Hòa Lạc',
                shortName: 'ĐHQG HN (Hòa Lạc)',
                campus: 'Đô thị ĐHQGHN tại Hòa Lạc (Thạch Thất)',
                district: 'Huyện Thạch Thất',
                address: 'Đô thị Đại học Quốc gia Hà Nội tại Hòa Lạc, Thạch Thất, Hà Nội',
                fullName: 'Đại học Quốc gia Hà Nội (Cơ sở Hòa Lạc)',
                city: 'hanoi',
                lat: 20.9920, lng: 105.5345,
                icon: 'fa-graduation-cap',
                aliases: ['vnu hoa lac', 'dhqg hoa lac', 'dhqghn hoa lac', 'thach that', 'hoa lac']
            },
            {
                id: 'hust',
                type: 'university',
                name: 'ĐH Bách Khoa Hà Nội (HUST)',
                shortName: 'Bách Khoa HN',
                campus: '01 Đại Cồ Việt (Hai Bà Trưng)',
                district: 'Quận Hai Bà Trưng',
                address: '01 Đại Cồ Việt, Bách Khoa, Hai Bà Trưng, Hà Nội',
                fullName: 'Đại học Bách Khoa Hà Nội (HUST)',
                city: 'hanoi',
                lat: 21.0056, lng: 105.8433,
                icon: 'fa-graduation-cap',
                aliases: ['bk', 'bkhn', 'hust', 'bach khoa ha noi', 'dai co viet', 'giai phong', 'hai ba trung', 'truong bach khoa', 'truong dai hoc']
            },
            {
                id: 'neu',
                type: 'university',
                name: 'ĐH Kinh Tế Quốc Dân (NEU)',
                shortName: 'ĐH KT Quốc Dân',
                campus: '207 Giải Phóng (Hai Bà Trưng)',
                district: 'Quận Hai Bà Trưng',
                address: '207 Giải Phóng, Đồng Tâm, Hai Bà Trưng, Hà Nội',
                fullName: 'Trường Đại học Kinh Tế Quốc Dân (NEU)',
                city: 'hanoi',
                lat: 20.9961, lng: 105.8425,
                icon: 'fa-graduation-cap',
                aliases: ['neu', 'ktqd', 'kinh te quoc dan', 'giai phong', 'tran dai nghia', 'hai ba trung', 'truong neu', 'truong dai hoc']
            },
            {
                id: 'ftu',
                type: 'university',
                name: 'ĐH Ngoại Thương Hà Nội (FTU) - CS Đống Đa',
                shortName: 'ĐH Ngoại Thương (Đống Đa)',
                campus: '91 Chùa Láng (Đống Đa)',
                district: 'Quận Đống Đa',
                address: '91 Chùa Láng, Láng Thượng, Đống Đa, Hà Nội',
                fullName: 'Trường Đại học Ngoại Thương Hà Nội (Trụ sở chính)',
                city: 'hanoi',
                lat: 21.0235, lng: 105.8052,
                icon: 'fa-graduation-cap',
                aliases: ['ftu', 'ngoai thuong', 'chua lang', 'dong da', 'truong ftu', 'truong dai hoc']
            },
            {
                id: 'ftu_hcm',
                type: 'university',
                name: 'ĐH Ngoại Thương - Cơ sở 2 TP.HCM',
                shortName: 'Ngoại Thương (CS2 Bình Thạnh)',
                campus: '15 Đường D5 (Bình Thạnh)',
                district: 'Quận Bình Thạnh',
                address: 'Số 15 Đường D5, Phường 25, Bình Thạnh, TP.HCM',
                fullName: 'Trường Đại học Ngoại Thương (Cơ sở 2 TP.HCM)',
                city: 'hcm',
                lat: 10.8062, lng: 106.7138,
                icon: 'fa-graduation-cap',
                aliases: ['ftu2', 'ftu cs2', 'ftu tphcm', 'ngoai thuong binh thanh', 'duong d5', 'hang xanh']
            },
            {
                id: 'ptit',
                type: 'university',
                name: 'Học viện Bưu Chính Viễn Thông (PTIT - Hà Đông)',
                shortName: 'Học viện PTIT (Hà Đông)',
                campus: 'Km10 Nguyễn Trãi (Hà Đông)',
                district: 'Quận Hà Đông',
                address: 'Km10 Đường Nguyễn Trãi, Hà Đông, Hà Nội',
                fullName: 'Học viện Công nghệ Bưu chính Viễn thông',
                city: 'hanoi',
                lat: 20.9806, lng: 105.7876,
                icon: 'fa-graduation-cap',
                aliases: ['ptit', 'buu chinh vien thong', 'ha dong', 'nguyen trai', 'tran phu', 'hoc vien buu chinh', 'truong ptit']
            },
            {
                id: 'ptit_hcm',
                type: 'university',
                name: 'Học viện Bưu Chính Viễn Thông - CS TP.HCM',
                shortName: 'PTIT (CS TP.Thủ Đức)',
                campus: '97 Man Thiện (Thủ Đức)',
                district: 'TP. Thủ Đức',
                address: '97 Man Thiện, P. Hiệp Phú, TP. Thủ Đức, TP.HCM',
                fullName: 'Học viện Công nghệ Bưu chính Viễn thông (Cơ sở TP.HCM)',
                city: 'hcm',
                lat: 10.8480, lng: 106.7868,
                icon: 'fa-graduation-cap',
                aliases: ['ptit hcm', 'buu chinh tphcm', 'ptit thu duc', 'man thien', 'hiep phu']
            },
            {
                id: 'tmu',
                type: 'university',
                name: 'ĐH Thương Mại (TMU)',
                shortName: 'ĐH Thương Mại',
                campus: '79 Hồ Tùng Mậu (Cầu Giấy)',
                district: 'Quận Cầu Giấy',
                address: '79 Hồ Tùng Mậu, Mai Dịch, Cầu Giấy, Hà Nội',
                fullName: 'Trường Đại học Thương Mại (TMU)',
                city: 'hanoi',
                lat: 21.0368, lng: 105.7745,
                icon: 'fa-graduation-cap',
                aliases: ['tmu', 'thuong mai', 'ho tung mau', 'cau giay', 'truong thuong mai', 'truong dai hoc']
            },
            {
                id: 'utc',
                type: 'university',
                name: 'ĐH Giao Thông Vận Tải (UTC)',
                shortName: 'ĐH GTVT HN',
                campus: 'Số 3 Cầu Giấy (Đống Đa)',
                district: 'Quận Đống Đa',
                address: 'Số 3 Phố Cầu Giấy, Láng Thượng, Đống Đa, Hà Nội',
                fullName: 'Trường Đại học Giao thông Vận tải Hà Nội',
                city: 'hanoi',
                lat: 21.0285, lng: 105.8035,
                icon: 'fa-graduation-cap',
                aliases: ['utc', 'giao thong van tai', 'gtvt', 'pho cau giay', 'dong da', 'truong dai hoc']
            },
            {
                id: 'hnue',
                type: 'university',
                name: 'ĐH Sư Phạm Hà Nội (HNUE)',
                shortName: 'ĐH Sư Phạm HN',
                campus: '136 Xuân Thủy (Cầu Giấy)',
                district: 'Quận Cầu Giấy',
                address: '136 Xuân Thủy, Dịch Vọng Hậu, Cầu Giấy, Hà Nội',
                fullName: 'Trường Đại học Sư Phạm Hà Nội',
                city: 'hanoi',
                lat: 21.0370, lng: 105.7830,
                icon: 'fa-graduation-cap',
                aliases: ['hnue', 'su pham ha noi', 'sphn', 'xuan thuy', 'cau giay', 'truong dai hoc']
            },
            {
                id: 'fpt_hn',
                type: 'university',
                name: 'ĐH FPT Hà Nội',
                shortName: 'ĐH FPT HN',
                campus: 'Khu CNC Hòa Lạc',
                district: 'Huyện Thạch Thất',
                address: 'Khu Công Nghệ Cao Hòa Lạc, Km29 Đại lộ Thăng Long, Hà Nội',
                fullName: 'Trường Đại học FPT Hà Nội (Hòa Lạc)',
                city: 'hanoi',
                lat: 21.0130, lng: 105.5270,
                icon: 'fa-graduation-cap',
                aliases: ['fpt', 'fpt hoa lac', 'hoa lac', 'dai lo thang long', 'thach that', 'truong fpt', 'truong dai hoc']
            },

            // =========================================================================
            // HÀ NỘI - TRƯỜNG CAO ĐẲNG (CĐ)
            // =========================================================================
            {
                id: 'cd_fpt_hn',
                type: 'college',
                name: 'CĐ FPT Polytechnic Hà Nội',
                shortName: 'CĐ FPT Poly HN',
                campus: 'Trịnh Văn Bô (Nam Từ Liêm)',
                district: 'Quận Nam Từ Liêm',
                address: 'Đường Trịnh Văn Bô, Phương Canh, Nam Từ Liêm, Hà Nội',
                fullName: 'Trường Cao đẳng FPT Polytechnic Hà Nội',
                city: 'hanoi',
                lat: 21.0378, lng: 105.7469,
                icon: 'fa-graduation-cap',
                aliases: ['fpt poly', 'fpt polytechnic', 'poly', 'cd fpt', 'cđ fpt', 'cao dang fpt', 'trinh van bo', 'nam tu liem', 'truong cao dang', 'cd']
            },
            {
                id: 'cd_dulich_hn',
                type: 'college',
                name: 'CĐ Du Lịch Hà Nội',
                shortName: 'CĐ Du Lịch HN',
                campus: '236 Hoàng Quốc Việt (Cầu Giấy)',
                district: 'Quận Cầu Giấy',
                address: '236 Hoàng Quốc Việt, Cổ Nhuế 1, Cầu Giấy, Hà Nội',
                fullName: 'Trường Cao đẳng Du lịch Hà Nội',
                city: 'hanoi',
                lat: 21.0465, lng: 105.7870,
                icon: 'fa-graduation-cap',
                aliases: ['cd du lich', 'cđ du lịch', 'du lich ha noi', 'cao dang du lich', 'hoang quoc viet', 'cau giay', 'truong cao dang', 'cd']
            },
            {
                id: 'cd_congthuong_hn',
                type: 'college',
                name: 'CĐ Công Thương Hà Nội',
                shortName: 'CĐ Công Thương HN',
                campus: '54A Vũ Trọng Phụng (Thanh Xuân)',
                district: 'Quận Thanh Xuân',
                address: '54A Vũ Trọng Phụng, Thanh Xuân Trung, Thanh Xuân, Hà Nội',
                fullName: 'Trường Cao đẳng Công Thương Hà Nội',
                city: 'hanoi',
                lat: 20.9995, lng: 105.8080,
                icon: 'fa-graduation-cap',
                aliases: ['cong thuong', 'cd cong thuong', 'cđ công thương', 'cao dang cong thuong', 'vu trong phung', 'thanh xuan', 'truong cao dang', 'cd']
            },

            // =========================================================================
            // ĐÀ NẴNG - TRƯỜNG ĐẠI HỌC & CAO ĐẲNG
            // =========================================================================
            {
                id: 'dut_dn',
                type: 'university',
                name: 'ĐH Bách Khoa Đà Nẵng (DUT)',
                shortName: 'Bách Khoa ĐN',
                campus: '54 Nguyễn Lương Bằng (Liên Chiểu)',
                district: 'Quận Liên Chiểu',
                address: '54 Nguyễn Lương Bằng, Hòa Khánh Bắc, Liên Chiểu, Đà Nẵng',
                fullName: 'Trường Đại học Bách Khoa - Đại học Đà Nẵng',
                city: 'danang',
                lat: 16.0760, lng: 108.1520,
                icon: 'fa-graduation-cap',
                aliases: ['dut', 'bach khoa da nang', 'bkdn', 'nguyen luong bang', 'lien chieu', 'dai hoc da nang']
            },
            {
                id: 'due_dn',
                type: 'university',
                name: 'ĐH Kinh Tế Đà Nẵng (DUE)',
                shortName: 'Kinh Tế ĐN',
                campus: '71 Ngũ Hành Sơn (Ngũ Hành Sơn)',
                district: 'Quận Ngũ Hành Sơn',
                address: '71 Ngũ Hành Sơn, Bắc Mỹ An, Ngũ Hành Sơn, Đà Nẵng',
                fullName: 'Trường Đại học Kinh Tế - Đại học Đà Nẵng',
                city: 'danang',
                lat: 16.0490, lng: 108.2410,
                icon: 'fa-graduation-cap',
                aliases: ['due', 'kinh te da nang', 'ngu hanh son', 'bac my an', 'dai hoc da nang']
            },
            {
                id: 'dtu_dn',
                type: 'university',
                name: 'ĐH Duy Tân (DTU)',
                shortName: 'ĐH Duy Tân',
                campus: '254 Nguyễn Văn Linh (Thanh Khê)',
                district: 'Quận Thanh Khê',
                address: '254 Nguyễn Văn Linh, Thạc Gián, Thanh Khê, Đà Nẵng',
                fullName: 'Trường Đại học Duy Tân',
                city: 'danang',
                lat: 16.0610, lng: 108.2140,
                icon: 'fa-graduation-cap',
                aliases: ['dtu', 'duy tan', 'nguyen van linh', 'thanh khe', 'hai chau']
            },
            {
                id: 'ufl_dn',
                type: 'university',
                name: 'ĐH Ngoại Ngữ Đà Nẵng (UFL)',
                shortName: 'Ngoại Ngữ ĐN',
                campus: '131 Lương Nhữ Hộc (Cẩm Lệ)',
                district: 'Quận Cẩm Lệ',
                address: '131 Lương Nhữ Hộc, Khuê Trung, Cẩm Lệ, Đà Nẵng',
                fullName: 'Trường Đại học Ngoại Ngữ - Đại học Đà Nẵng',
                city: 'danang',
                lat: 16.0350, lng: 108.2080,
                icon: 'fa-graduation-cap',
                aliases: ['ufl', 'ngoai ngu da nang', 'luong nhu hoc', 'cam le']
            },
            {
                id: 'vku_dn',
                type: 'university',
                name: 'ĐH CNTT & TT Việt - Hàn (VKU)',
                shortName: 'Việt - Hàn (VKU)',
                campus: '470 Trần Đại Nghĩa (Ngũ Hành Sơn)',
                district: 'Quận Ngũ Hành Sơn',
                address: '470 Trần Đại Nghĩa, Hòa Quý, Ngũ Hành Sơn, Đà Nẵng',
                fullName: 'Trường Đại học Công nghệ Thông tin và Truyền thông Việt - Hàn',
                city: 'danang',
                lat: 15.9750, lng: 108.2520,
                icon: 'fa-graduation-cap',
                aliases: ['vku', 'viet han', 'tran dai nghia', 'hoa quy', 'lang dai hoc da nang']
            },

            // =========================================================================
            // ĐÀ LẠT - TRƯỜNG ĐẠI HỌC & CAO ĐẲNG
            // =========================================================================
            {
                id: 'dlu_dl',
                type: 'university',
                name: 'ĐH Đà Lạt (DLU)',
                shortName: 'ĐH Đà Lạt',
                campus: '01 Phù Đổng Thiên Vương (Phường 8)',
                district: 'Phường 8, Đà Lạt',
                address: '01 Phù Đổng Thiên Vương, Phường 8, TP. Đà Lạt, Lâm Đồng',
                fullName: 'Trường Đại học Đà Lạt',
                city: 'dalat',
                lat: 11.9560, lng: 108.4460,
                icon: 'fa-graduation-cap',
                aliases: ['dlu', 'dai hoc da lat', 'phu dong thien vuong', 'phuong 8', 'da lat']
            },
            {
                id: 'yersin_dl',
                type: 'university',
                name: 'ĐH Yersin Đà Lạt',
                shortName: 'ĐH Yersin',
                campus: '27 Tôn Thất Tùng (Phường 8)',
                district: 'Phường 8, Đà Lạt',
                address: '27 Tôn Thất Tùng, Phường 8, TP. Đà Lạt, Lâm Đồng',
                fullName: 'Trường Đại học Yersin Đà Lạt',
                city: 'dalat',
                lat: 11.9680, lng: 108.4450,
                icon: 'fa-graduation-cap',
                aliases: ['yersin', 'dai hoc yersin', 'ton that tung', 'phuong 8', 'da lat']
            },
            {
                id: 'cd_sphn_dl',
                type: 'college',
                name: 'CĐ Sư Phạm Đà Lạt (Grand Lycée Yersin)',
                shortName: 'CĐ Sư Phạm Đà Lạt',
                campus: '29 Yersin (Phường 10)',
                district: 'Phường 10, Đà Lạt',
                address: '29 Yersin, Phường 10, TP. Đà Lạt, Lâm Đồng',
                fullName: 'Trường Cao đẳng Sư phạm Đà Lạt',
                city: 'dalat',
                lat: 11.9380, lng: 108.4520,
                icon: 'fa-graduation-cap',
                aliases: ['cd su pham da lat', 'su pham da lat', 'grand lycee yersin', 'yersin', 'phuong 10', 'thap chuong cong']
            },
            {
                id: 'cd_dulich_dl',
                type: 'college',
                name: 'CĐ Du Lịch Đà Lạt',
                shortName: 'CĐ Du Lịch ĐL',
                campus: '02 Lữ Gia (Phường 9)',
                district: 'Phường 9, Đà Lạt',
                address: '02 Lữ Gia, Phường 9, TP. Đà Lạt, Lâm Đồng',
                fullName: 'Trường Cao đẳng Du lịch Đà Lạt',
                city: 'dalat',
                lat: 11.9510, lng: 108.4600,
                icon: 'fa-graduation-cap',
                aliases: ['cd du lich da lat', 'du lich da lat', 'lu gia', 'phuong 9']
            },
        ];

        this.init();
    }

    init() {
        if (typeof L === 'undefined') {
            console.error('GoogleMapsRenty requires Leaflet mapping engine.');
            return;
        }

        // 1. Tile Layers with Multi-Provider Fallback & Subdomain Rotation
        const createResilientTileLayer = (primaryUrl, primarySubdomains, fallbackUrl, fallbackSubdomains, options = {}) => {
            const layer = L.tileLayer(primaryUrl, Object.assign({
                maxZoom: 20,
                subdomains: primarySubdomains,
                keepBuffer: 3,
                updateWhenIdle: true,
                updateWhenZooming: false
            }, options));

            let failedAttempts = 0;
            let loadedCount = 0;
            let switchedToFallback = false;

            layer.on('tileload', function() {
                loadedCount++;
                failedAttempts = 0;
            });

            layer.on('tileerror', function() {
                failedAttempts++;
                // Chỉ chuyển sang dự phòng khi Google Maps hoàn toàn không tải được (lỗi liên tiếp >= 8 tile và chưa tải được tile nào)
                if (failedAttempts >= 8 && loadedCount === 0 && !switchedToFallback && fallbackUrl) {
                    switchedToFallback = true;
                    console.warn('[RentyMap] Google Maps tile error. Auto-switching to fallback provider:', fallbackUrl);
                    layer.setUrl(fallbackUrl);
                    if (fallbackSubdomains) {
                        layer.options.subdomains = fallbackSubdomains;
                    }
                    if (typeof layer.redraw === 'function') {
                        layer.redraw();
                    }
                    const banner = document.getElementById('gm-tiles-error-banner');
                    if (banner) banner.classList.add('hidden');
                } else if (failedAttempts >= 16 && switchedToFallback) {
                    const banner = document.getElementById('gm-tiles-error-banner');
                    if (banner) banner.classList.remove('hidden');
                }
            });

            return layer;
        };

        // Nguồn chính: Google Maps tiếng Việt | Dự phòng ổn định: CartoDB Voyager / OpenStreetMap
        this.tileLayers.roadmap = createResilientTileLayer(
            'https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&hl=vi',
            ['0', '1', '2', '3'],
            'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
            ['a', 'b', 'c', 'd'],
            { attribution: 'Dữ liệu bản đồ &copy; Google Maps / OpenStreetMap' }
        );

        this.tileLayers.satellite = createResilientTileLayer(
            'https://mt{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}&hl=vi',
            ['0', '1', '2', '3'],
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
            ['server'],
            { attribution: 'Hình ảnh vệ tinh &copy; Google Maps / Esri' }
        );

        this.tileLayers.terrain = createResilientTileLayer(
            'https://mt{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}&hl=vi',
            ['0', '1', '2', '3'],
            'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
            ['a', 'b', 'c'],
            { attribution: 'Địa hình &copy; Google Maps / OpenTopoMap' }
        );

        // 2. Detect center from URL params, sessionStorage, or rooms (Chế độ khám phá chỗ đó)
        const rooms = (window.rentyRoomsData && Object.keys(window.rentyRoomsData).length > 0) ? window.rentyRoomsData : {};
        let center = this.options.defaultCenter;
        let initialZoom = this.options.defaultZoom;
        let preservedCity = null;

        // Ưu tiên 1: Đọc từ query string trên URL (?map_lat=...&map_lng=...&map_zoom=...&map_city=...)
        const urlParams = new URLSearchParams(window.location.search);
        const qLat = parseFloat(urlParams.get('map_lat'));
        const qLng = parseFloat(urlParams.get('map_lng'));
        const qZoom = parseInt(urlParams.get('map_zoom'), 10);
        const qCity = urlParams.get('map_city');

        // Ưu tiên 2: Đọc từ sessionStorage (khi bấm 'Xem tất cả trọ hệ thống' hoặc chuyển cơ sở)
        let savedView = null;
        try {
            const rawSaved = sessionStorage.getItem('renty_map_preserve_view');
            if (rawSaved) {
                savedView = JSON.parse(rawSaved);
                sessionStorage.removeItem('renty_map_preserve_view');
            }
        } catch (e) {}

        if (!isNaN(qLat) && !isNaN(qLng)) {
            center = [qLat, qLng];
            if (!isNaN(qZoom)) initialZoom = qZoom;
            if (qCity) preservedCity = qCity;
        } else if (savedView && savedView.lat && savedView.lng) {
            center = [savedView.lat, savedView.lng];
            if (savedView.zoom) initialZoom = savedView.zoom;
            if (savedView.city) preservedCity = savedView.city;
        } else {
            // Nhận diện vị trí từ danh sách phòng hiện có
            const firstRoom = Object.values(rooms)[0];
            if (firstRoom) {
                const addr = ((firstRoom.address || '') + ' ' + (firstRoom.area_name || '')).toLowerCase();
                const isHcm = addr.includes('hồ chí minh') || addr.includes('tp.hcm') || addr.includes('tphcm') || addr.includes('hcm')
                    || addr.includes('thủ đức') || addr.includes('bình thạnh') || addr.includes('quận 10') || addr.includes('quận 7')
                    || addr.includes('quận 1') || addr.includes('quận 3') || addr.includes('quận 5') || addr.includes('quận 4') || addr.includes('gò vấp') || addr.includes('tân bình');
                const isDanang = addr.includes('đà nẵng') || addr.includes('da nang') || addr.includes('hải châu') || addr.includes('sơn trà')
                    || addr.includes('ngũ hành sơn') || addr.includes('thanh khê') || addr.includes('liên chiểu') || addr.includes('cẩm lệ') || addr.includes('hòa vang');
                const isDalat = addr.includes('đà lạt') || addr.includes('da lat') || addr.includes('lâm đồng') || addr.includes('tuyền lâm')
                    || addr.includes('trại mát') || addr.includes('vạn thành') || addr.includes('thái phiên');
                const isHanoi = addr.includes('hà nội') || addr.includes('hanoi') || addr.includes('cầu giấy') || addr.includes('đống đa')
                    || addr.includes('thanh xuân') || addr.includes('tây hồ') || addr.includes('ba đình') || addr.includes('hai bà trưng')
                    || addr.includes('nam từ liêm') || addr.includes('bắc từ liêm') || addr.includes('hà đông') || addr.includes('hoàng mai');

                if (isDanang) {
                    center = [16.060, 108.220]; // Đà Nẵng
                    preservedCity = 'danang';
                } else if (isDalat) {
                    center = [11.942, 108.440]; // Đà Lạt
                    preservedCity = 'dalat';
                } else if (isHcm && !isHanoi) {
                    center = [10.798, 106.705]; // HCMC
                    preservedCity = 'hcm';
                } else if (isHanoi) {
                    center = [21.036, 105.790]; // Hanoi Cầu Giấy
                    preservedCity = 'hanoi';
                } else {
                    preservedCity = 'all';
                    center = [16.047, 108.206]; // Tâm bản đồ Việt Nam
                    initialZoom = 6;
                }
            } else {
                preservedCity = 'all';
                center = [16.047, 108.206];
                initialZoom = 6;
            }
        }

        this.initialCenter = center;
        this.options.defaultZoom = initialZoom;
        this.currentCity = preservedCity || 'all';

        // 3. Instantiate Leaflet Map with Google Maps
        if (this.container) {
            if (this.container._leaflet_id) {
                this.container._leaflet_id = null;
            }
            // Clear any old child nodes to guarantee clean canvas
            this.container.innerHTML = '';
        }

        const mapOptions = {
            center: center,
            zoom: initialZoom,
            zoomControl: false,
            attributionControl: false,
            preferCanvas: true,
            zoomAnimation: true,
            markerZoomAnimation: true,
            fadeAnimation: true,
            wheelDebounceTime: 50,
            wheelPxPerZoomLevel: 100,
            zoomSnap: 1,
            zoomDelta: 1,
            inertia: true,
            inertiaDeceleration: 3000
        };

        try {
            this.map = L.map(this.containerId, mapOptions);
        } catch (err) {
            console.warn('Leaflet map re-initialization catch:', err);
            if (this.container) {
                this.container._leaflet_id = null;
                this.container.innerHTML = '';
                this.map = L.map(this.containerId, mapOptions);
            }
        }

        if (!this.map) {
            console.error('Failed to create Leaflet map instance.');
            return;
        }

        // Add default Google Roadmap layer
        this.tileLayers.roadmap.addTo(this.map);

        // Add custom sleek zoom control in bottom right
        L.control.zoom({ position: 'bottomright' }).addTo(this.map);

        // 4. Render markers
        this.renderRoomMarkers(rooms);

        // Đồng bộ giao diện tab thành phố và danh sách chips trường học phù hợp với khu vực khám phá
        const activeCity = this.currentCity || 'all';
        const cityTab = document.getElementById(`city-tab-${activeCity}`);
        if (cityTab) {
            document.querySelectorAll('.gm-city-tab').forEach(btn => {
                btn.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-sm');
                btn.classList.add('bg-slate-900', 'text-slate-400');
            });
            cityTab.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-sm');
            cityTab.classList.remove('bg-slate-900', 'text-slate-400');
        }
        this.renderCampusChips();

        // Track user interaction to prevent resetting viewport during drag/pan
        this._userHasInteracted = false;
        this.map.on('movestart zoomstart dragstart', () => {
            this._userHasInteracted = true;
        });

        // 5. Invalidate size after layout transition and re-center smoothly once
        let hasFittedInitialView = false;
        [150, 450].forEach(delay => {
            setTimeout(() => {
                if (!this.map || typeof this.map.invalidateSize !== 'function') return;
                this.map.invalidateSize({ pan: false });
                if (!hasFittedInitialView && !this._userHasInteracted) {
                    hasFittedInitialView = true;
                    if (this.currentCity === 'all' && this.markers && Object.keys(this.markers).length > 0) {
                        try {
                            const markerGroup = L.featureGroup(Object.values(this.markers));
                            this.map.fitBounds(markerGroup.getBounds(), { padding: [50, 50], maxZoom: 13, animate: false });
                        } catch (e) {
                            if (this.initialCenter) {
                                this.map.setView(this.initialCenter, this.options.defaultZoom || 6, { animate: false });
                            }
                        }
                    }
                }
            }, delay);
        });
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

    switchToFallbackTiles() {
        if (!this.map) return;
        const currentL = this.tileLayers[this.currentLayer] || this.tileLayers.roadmap;
        if (currentL) {
            currentL.setUrl('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png');
            currentL.options.subdomains = ['a', 'b', 'c', 'd'];
            if (typeof currentL.redraw === 'function') currentL.redraw();
        }
        const banner = document.getElementById('gm-tiles-error-banner');
        if (banner) banner.classList.add('hidden');
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
       BUILDING-LEVEL GROUPING & ROOM MARKERS (Phương án 1: Tối ưu 60 FPS)
       ========================================================================= */
    groupRoomsByBuilding(roomList) {
        const buildingsMap = {};
        (roomList || []).forEach(room => {
            if (!room) return;
            // Khóa nhóm: ưu tiên building_id, sau đó tới building_name / address
            let bKey = room.building_id ? ('b_' + room.building_id) : null;
            if (!bKey) {
                const rawName = (room.building_name || room.title || room.address || ('room_' + room.id)).trim().toLowerCase();
                bKey = 'b_name_' + rawName;
            }

            if (!buildingsMap[bKey]) {
                const bId = room.building_id || room.id;
                buildingsMap[bKey] = {
                    key: bKey,
                    id: bId,
                    building_id: room.building_id || null,
                    name: room.building_name || (room.title ? room.title.split('-')[0].trim() : 'Tòa nhà Renty'),
                    address: room.building_address || room.address || '',
                    area_name: room.area_name || '',
                    property_type: room.property_type || 'apartment', // 'hotel' | 'apartment' | 'boarding'
                    verification_status: room.tenant_verification_status || room.verification_status || 'unverified',
                    listing_badge: room.listing_badge || 'unverified',
                    trust_badge: room.trust_badge || null,
                    tenant_name: room.tenant_name || room.landlord_name || '',
                    landlord_phone: room.landlord_phone || '',
                    cover_image: room.cover_image || (room.image_urls && room.image_urls[0]) || '',
                    rating: parseFloat(room.rating || 4.5),
                    min_price: parseFloat(room.price || 0),
                    max_price: parseFloat(room.price || 0),
                    rooms: [],
                    empty_count: 0,
                };
            }

            const b = buildingsMap[bKey];
            b.rooms.push(room);
            const p = parseFloat(room.price || 0);
            if (p > 0) {
                if (b.min_price === 0 || p < b.min_price) b.min_price = p;
                if (p > b.max_price) b.max_price = p;
            }
            if (room.status === 'empty' || !room.status) {
                b.empty_count++;
            }
            if (room.tenant_verification_status === 'verified' || room.listing_badge === 'verified' || room.listing_badge === 'premium_verified') {
                b.verification_status = 'verified';
                b.listing_badge = room.listing_badge || 'verified';
            }
        });

        return Object.values(buildingsMap);
    }

    renderRoomMarkers(rooms) {
        const roomsToRender = (rooms && Object.keys(rooms).length > 0)
            ? rooms
            : (window.rentyRoomsData || {});

        if (this.markers) {
            Object.values(this.markers).forEach(m => {
                try { this.map.removeLayer(m); } catch (e) { }
            });
        }
        if (this.buildingMarkers) {
            Object.values(this.buildingMarkers).forEach(m => {
                try { this.map.removeLayer(m); } catch (e) { }
            });
        }
        this.markers = {};
        this.buildingMarkers = {};
        this.buildingMapByRoomId = {};

        const roomList = Array.isArray(roomsToRender) ? roomsToRender : Object.values(roomsToRender);
        const buildings = this.groupRoomsByBuilding(roomList);

        buildings.forEach(b => {
            try {
                const coords = this.computeBuildingCoordinates(b);
                if (!coords || isNaN(coords.lat) || isNaN(coords.lng)) return;

                const minPriceM = (b.min_price / 1000000).toFixed(1).replace('.0', '');
                const priceBadgeText = `Từ ${minPriceM}Tr • ${b.rooms.length}p`;

                let typeIcon = '<i class="fa-solid fa-building text-[10px]"></i>';
                if (b.property_type === 'hotel') {
                    typeIcon = '<i class="fa-solid fa-hotel text-[10px]"></i>';
                } else if (b.property_type === 'boarding') {
                    typeIcon = '<i class="fa-solid fa-house-chimney text-[10px]"></i>';
                }

                const isVerified = (b.verification_status === 'verified' || b.listing_badge === 'verified' || b.listing_badge === 'premium_verified');
                const badgeBg = isVerified
                    ? 'background: linear-gradient(135deg, #059669, #0d9488);'
                    : 'background: linear-gradient(135deg, #1e40af, #2563eb);';

                // Lớp class chứa ID của các phòng để tương thích bộ chọn CSS
                const roomClasses = b.rooms.map(r => `marker-room-${r.id}`).join(' ');

                const customHtml = `
                    <div class="gm-price-marker gm-building-marker ${roomClasses}" id="marker-building-${b.id}" data-building-id="${b.id}" style="
                        display: inline-flex;
                        align-items: center;
                        gap: 5px;
                        padding: 4px 10px;
                        border-radius: 9999px;
                        color: #ffffff;
                        font-size: 11px;
                        font-weight: 800;
                        font-family: 'Plus Jakarta Sans', sans-serif;
                        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.4), 0 0 0 1.5px rgba(255, 255, 255, 0.85);
                        cursor: pointer;
                        transition: transform 0.15s ease, box-shadow 0.15s ease;
                        white-space: nowrap;
                        ${badgeBg}
                    ">
                        ${typeIcon}
                        <span>${priceBadgeText}</span>
                        ${b.empty_count > 0 ? `<span style="display:inline-block; width:6px; height:6px; border-radius:9999px; background:#4ade80; box-shadow:0 0 6px #4ade80;" title="Còn ${b.empty_count} phòng trống"></span>` : ''}
                        ${isVerified ? '<i class="fa-solid fa-circle-check text-[10px] text-emerald-200"></i>' : ''}
                    </div>
                `;

                const icon = L.divIcon({
                    html: customHtml,
                    className: 'gm-marker-container',
                    iconSize: [85, 28],
                    iconAnchor: [42, 14]
                });

                const marker = L.marker([coords.lat, coords.lng], { icon: icon });
                marker._buildingId = b.id;
                marker._buildingData = b;
                marker._buildingCoords = coords;

                // Popup HTML (Interactive Building InfoWindow)
                const popupHtml = this.buildBuildingInfoWindowHtml(b, coords);
                marker.bindPopup(popupHtml, {
                    maxWidth: 340,
                    minWidth: 300,
                    className: 'gm-infowindow-popup',
                    offset: [0, -10]
                });

                // Marker hover: Di chuột vào marker trên bản đồ -> cập nhật popup động và highlight
                marker.on('mouseover', () => {
                    this.highlightBuildingMarker(b.id);
                    marker.openPopup();
                });

                // Marker click
                marker.on('click', () => {
                    this.highlightBuildingMarker(b.id);
                    if (this.selectedUniversity && this.selectedCampusId && this.selectedCampusId !== 'all') {
                        this.drawCommuteRoute(coords, this.selectedUniversity);
                    }
                    if (b.rooms.length > 0 && typeof window.highlightRoomCard === 'function') {
                        window.highlightRoomCard(b.rooms[0].id);
                    }
                });

                marker.addTo(this.map);

                // Ánh xạ tương thích ngược 100% cho từng phòng
                b.rooms.forEach(r => {
                    this.markers[r.id] = marker;
                    this.buildingMapByRoomId[r.id] = b.id;
                });
                this.buildingMarkers[b.id] = marker;

            } catch (err) {
                console.warn('Skipping marker for building:', b, err);
            }
        });
    }

    buildBuildingInfoWindowHtml(b, coords) {
        const coverImg = b.cover_image || 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80';
        const minPriceText = (b.min_price / 1000000).toFixed(1).replace('.0', '');
        const maxPriceText = (b.max_price / 1000000).toFixed(1).replace('.0', '');
        const priceRange = b.min_price === b.max_price
            ? `${minPriceText}Tr/tháng`
            : `${minPriceText} - ${maxPriceText}Tr/tháng`;

        let typeLabel = 'Căn hộ chung cư mini';
        let typeBadgeClass = 'background: rgba(59, 130, 246, 0.2); color: #93c5fd; border: 1px solid rgba(59, 130, 246, 0.35);';
        if (b.property_type === 'hotel') {
            typeLabel = 'Khách sạn / Hotel';
            typeBadgeClass = 'background: rgba(245, 158, 11, 0.2); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.35);';
        } else if (b.property_type === 'boarding') {
            typeLabel = 'Khu nhà trọ sinh viên';
            typeBadgeClass = 'background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.35);';
        }

        const googleMapsDirUrl = `https://www.google.com/maps/dir/?api=1&destination=${coords.lat},${coords.lng}`;
        const streetViewUrl = `https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${coords.lat},${coords.lng}`;

        let uniDistHtml = '';
        if (this.selectedUniversity) {
            const uniDistKm = this.calcHaversineDistance(coords.lat, coords.lng, this.selectedUniversity.lat, this.selectedUniversity.lng);
            const bikeM = Math.max(2, Math.round((uniDistKm / 24) * 60 + 2));
            const cleanUni = this.selectedUniversity.shortName || this.selectedUniversity.name.split('(')[0].trim();
            uniDistHtml = `
                <div style="margin-bottom: 8px; padding: 5px 9px; border-radius: 9px; background: rgba(14, 165, 233, 0.12); border: 1px solid rgba(56, 189, 248, 0.3); display: flex; align-items: center; justify-content: space-between; gap: 6px;">
                    <span style="font-size: 11px; color: #7dd3fc; display: flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-graduation-cap text-sky-400"></i> Cách ${cleanUni}:
                    </span>
                    <span style="font-size: 11px; font-weight: 800; color: #ffffff; white-space: nowrap;">
                        ${uniDistKm.toFixed(1)} km <span style="font-weight: 500; color: #94a3b8; font-size: 10px;">(~${bikeM}p xe máy)</span>
                    </span>
                </div>
            `;
        }

        // Tạo danh sách các phòng mini (cuộn mượt)
        const roomItemsHtml = b.rooms.map(room => {
            const roomPriceMillion = (room.price / 1000000).toFixed(1).replace('.0', '');
            const isEmpty = (room.status === 'empty' || !room.status);
            const statusBadge = isEmpty
                ? '<span style="font-size: 9px; font-weight: 700; color: #34d399; background: rgba(16,185,129,0.15); padding: 1px 5px; border-radius: 4px;">Trống</span>'
                : '<span style="font-size: 9px; font-weight: 700; color: #94a3b8; background: rgba(148,163,184,0.15); padding: 1px 5px; border-radius: 4px;">Đã thuê</span>';

            return `
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; padding: 5px 8px; background: rgba(15, 23, 42, 0.6); border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.05); margin-bottom: 4px;">
                    <div style="min-width: 0; display: flex; align-items: center; gap: 6px;">
                        <span style="font-size: 11px; font-weight: 800; color: #f8fafc; white-space: nowrap;">P.${room.room_number || room.id}</span>
                        <span style="font-size: 10px; color: #94a3b8;">${room.area || 25}m²</span>
                        ${statusBadge}
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                        <span style="font-size: 11px; font-weight: 800; color: #38bdf8;">${roomPriceMillion}Tr</span>
                        <button type="button" onclick="if(window.openRoomDetailModal){window.openRoomDetailModal(${room.id});}else{window.location.href='/renty/room/${room.id}';}" style="background: #2563eb; color: #ffffff; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 5px; border: none; cursor: pointer; transition: all 0.2s;">
                            Xem
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        return `
            <div class="gm-infowindow-card" style="font-family: 'Plus Jakarta Sans', sans-serif; border-radius: 14px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 20px 40px rgba(0,0,0,0.55); width: 100%;">
                <div style="position: relative; height: 120px; overflow: hidden;">
                    <img src="${coverImg}" alt="${b.name}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80';">
                    <span style="position: absolute; top: 8px; left: 8px; backdrop-filter: blur(8px); padding: 3px 8px; border-radius: 9999px; font-size: 10px; font-weight: 800; ${typeBadgeClass}">
                        ${typeLabel}
                    </span>
                    <span style="position: absolute; top: 8px; right: 8px; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px); padding: 3px 8px; border-radius: 9999px; font-size: 10px; font-weight: 800; color: #fbbf24;">
                        ★ ${b.rating || '4.5'}
                    </span>
                </div>
                <div style="padding: 10px 12px;">
                    <h3 class="gm-info-title" style="font-size: 13px; font-weight: 800; margin: 0 0 3px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #ffffff;">
                        ${b.name}
                    </h3>
                    <p class="gm-info-address" style="font-size: 11px; margin: 0 0 6px 0; display: flex; align-items: center; gap: 4px; color: #94a3b8;">
                        <i class="fa-solid fa-location-dot" style="color: #10b981; font-size: 10px;"></i>
                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${b.address || ''}</span>
                    </p>
                    ${uniDistHtml}
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                        <div>
                            <span style="font-size: 10px; color: #94a3b8; display: block;">Khoảng giá</span>
                            <span class="gm-info-price" style="font-size: 13px; font-weight: 900; color: #34d399;">${priceRange}</span>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 10px; color: #94a3b8; display: block;">Quy mô</span>
                            <span style="font-size: 11px; font-weight: 700; color: #f8fafc;">${b.rooms.length} phòng (${b.empty_count} trống)</span>
                        </div>
                    </div>

                    <!-- Danh sách phòng cuộn mượt -->
                    <div style="margin-bottom: 8px;">
                        <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 4px; display: flex; justify-content: space-between;">
                            <span>Danh sách phòng:</span>
                            <span style="color: #38bdf8;">${b.rooms.length} phòng</span>
                        </div>
                        <div style="max-height: 125px; overflow-y: auto; padding-right: 2px;">
                            ${roomItemsHtml}
                        </div>
                    </div>

                    <div style="display: flex; gap: 6px;">
                        <a href="${googleMapsDirUrl}" target="_blank" rel="noopener noreferrer" style="flex: 1; text-align: center; background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.35); color: #7dd3fc; font-size: 11px; font-weight: 700; padding: 6px 0; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 5px;">
                            <i class="fa-solid fa-diamond-turn-right"></i> Chỉ đường
                        </a>
                        <a href="${streetViewUrl}" target="_blank" rel="noopener noreferrer" style="flex: 1; text-align: center; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.35); color: #fcd34d; font-size: 11px; font-weight: 700; padding: 6px 0; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 5px;">
                            <i class="fa-solid fa-street-view"></i> Street View
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    highlightBuildingMarker(buildingId) {
        if (!buildingId || this._activeHighlightedBuildingId === buildingId) return;

        // Reset previous highlighted marker in O(1)
        if (this._activeHighlightedBuildingId) {
            const prevEl = document.getElementById(`marker-building-${this._activeHighlightedBuildingId}`) ||
                           document.getElementById(`marker-room-${this._activeHighlightedBuildingId}`);
            if (prevEl) {
                prevEl.style.transform = 'translate3d(0, 0, 0) scale(1)';
                prevEl.style.zIndex = '1';
                prevEl.style.boxShadow = '0 3px 10px rgba(0, 0, 0, 0.4), 0 0 0 1.5px rgba(255, 255, 255, 0.85)';
                prevEl.style.filter = 'none';
            }
        }

        this._activeHighlightedBuildingId = buildingId;
        const markerEl = document.getElementById(`marker-building-${buildingId}`);
        if (markerEl) {
            markerEl.style.transform = 'translate3d(0, 0, 0) scale(1.18)';
            markerEl.style.zIndex = '9999';
            markerEl.style.boxShadow = '0 0 0 3px #38bdf8, 0 8px 24px rgba(56, 189, 248, 0.65)';
        }
    }

    getMarkerElementForRoom(roomId) {
        const targetBuildingId = (this.buildingMapByRoomId && this.buildingMapByRoomId[roomId]) || null;
        if (targetBuildingId) {
            const bEl = document.getElementById(`marker-building-${targetBuildingId}`);
            if (bEl) return bEl;
        }
        return document.getElementById(`marker-room-${roomId}`) || document.querySelector(`.marker-room-${roomId}`);
    }

    highlightMarker(roomId) {
        const targetBuildingId = (this.buildingMapByRoomId && this.buildingMapByRoomId[roomId]) || null;
        if (targetBuildingId) {
            this.highlightBuildingMarker(targetBuildingId);
            return;
        }

        if (!roomId || this._activeHighlightedBuildingId === roomId) return;

        if (this._activeHighlightedBuildingId) {
            const prevEl = document.getElementById(`marker-building-${this._activeHighlightedBuildingId}`) ||
                           document.getElementById(`marker-room-${this._activeHighlightedBuildingId}`);
            if (prevEl) {
                prevEl.style.transform = 'translate3d(0, 0, 0) scale(1)';
                prevEl.style.zIndex = '1';
                prevEl.style.boxShadow = '0 3px 10px rgba(0, 0, 0, 0.4), 0 0 0 1.5px rgba(255, 255, 255, 0.85)';
                prevEl.style.filter = 'none';
            }
        }

        this._activeHighlightedBuildingId = roomId;
        const markerEl = document.getElementById(`marker-room-${roomId}`);
        if (markerEl) {
            markerEl.style.transform = 'translate3d(0, 0, 0) scale(1.18)';
            markerEl.style.zIndex = '9999';
            markerEl.style.boxShadow = '0 0 0 3px #38bdf8, 0 8px 24px rgba(56, 189, 248, 0.65)';
        }
    }

    buildInfoWindowHtml(room, coords) {
        const coverImg = room.cover_image || (room.image_urls && room.image_urls[0]) || 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=600&q=80';
        const formattedPrice = new Intl.NumberFormat('vi-VN').format(room.price) + 'đ/tháng';
        const googleMapsDirUrl = `https://www.google.com/maps/dir/?api=1&destination=${coords.lat},${coords.lng}`;
        const streetViewUrl = `https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${coords.lat},${coords.lng}`;

        let uniDistHtml = '';
        // CHỈ hiển thị khoảng cách đến trường khi người dùng THỰC SỰ CHỌN một trường cụ thể (không phải 'Tất cả')
        // và khoảng cách nằm trong phạm vi hợp lý (< 45km), tránh hiển thị vô lý hàng nghìn km xuyên tỉnh thành
        if (this.selectedUniversity && this.selectedCampusId && this.selectedCampusId !== 'all') {
            const uniDistKm = this.calcHaversineDistance(coords.lat, coords.lng, this.selectedUniversity.lat, this.selectedUniversity.lng);
            if (uniDistKm <= 45) {
                const bikeM = Math.max(2, Math.round((uniDistKm / 24) * 60 + 2));
                const cleanUni = this.selectedUniversity.shortName || this.selectedUniversity.name.split('(')[0].trim();
                uniDistHtml = `
                    <div style="margin-bottom: 10px; padding: 6px 10px; border-radius: 10px; background: rgba(14, 165, 233, 0.12); border: 1px solid rgba(56, 189, 248, 0.3); display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                        <span style="font-size: 11px; color: #7dd3fc; display: flex; align-items: center; gap: 5px;">
                            <i class="fa-solid fa-graduation-cap text-sky-400"></i> Cách ${cleanUni}:
                        </span>
                        <span style="font-size: 11px; font-weight: 800; color: #ffffff; white-space: nowrap;">
                            ${uniDistKm.toFixed(1)} km <span style="font-weight: 500; color: #94a3b8; font-size: 10px;">(~${bikeM}p xe máy)</span>
                        </span>
                    </div>
                `;
            }
        }

        return `
            <div class="gm-infowindow-card" style="font-family: 'Plus Jakarta Sans', sans-serif; border-radius: 16px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
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
                    <h3 class="gm-info-title" style="font-size: 13px; font-weight: 800; margin: 0 0 4px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${room.title}
                    </h3>
                    <p class="gm-info-address" style="font-size: 11px; margin: 0 0 8px 0; display: flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-location-dot" style="color: #10b981; font-size: 10px;"></i>
                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${room.address || ''}</span>
                    </p>
                    ${uniDistHtml}
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                        <span class="gm-info-price" style="font-size: 14px; font-weight: 900; color: #34d399;">${formattedPrice}</span>
                        <span class="gm-info-area" style="font-size: 10px; background: rgba(255,255,255,0.06); padding: 2px 7px; border-radius: 6px;">
                            ${room.area || 25}m²
                        </span>
                    </div>
                    <div style="display: flex; gap: 6px;">
                        <button type="button" class="gm-info-btn-detail" onclick="if(window.openRoomDetailModal){window.openRoomDetailModal(${room.id});}else{window.location.href='/renty/room/${room.id}';}" style="flex: 1; text-align: center; background: #2563eb; color: #ffffff; font-size: 11px; font-weight: 700; padding: 7px 0; border-radius: 8px; border: none; cursor: pointer; transition: all 0.2s;">
                            Xem Chi Tiết
                        </button>
                        <a href="${googleMapsDirUrl}" target="_blank" rel="noopener noreferrer" class="gm-info-btn-action" style="padding: 7px 10px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #93c5fd; font-size: 11px; font-weight: 700; border-radius: 8px; text-decoration: none; display: flex; align-items: center; gap: 4px;" title="Chỉ đường Google Maps">
                            <i class="fa-solid fa-diamond-turn-right"></i>
                        </a>
                        <a href="${streetViewUrl}" target="_blank" rel="noopener noreferrer" class="gm-info-btn-action" style="padding: 7px 10px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #fbbf24; font-size: 11px; font-weight: 700; border-radius: 8px; text-decoration: none; display: flex; align-items: center; gap: 4px;" title="Xem Google Street View 360°">
                            <i class="fa-solid fa-street-view"></i>
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    /* =========================================================================
       GOOGLE MAPS COMMUTE & DISTANCE MATRIX CALCULATOR
       ========================================================================= */
    selectCampus(uniId) {
        if (!uniId || uniId === 'all') {
            const prevUni = this.selectedUniversity;
            this.selectedCampusId = 'all';
            this.selectedUniversity = null;
            this.activeCenter = null;

            if (this.customPlacePin) {
                this.map.removeLayer(this.customPlacePin);
                this.customPlacePin = null;
            }
            if (this.campusRadiusCircle) {
                this.map.removeLayer(this.campusRadiusCircle);
                this.campusRadiusCircle = null;
            }
            if (this.uniMarker) {
                this.map.removeLayer(this.uniMarker);
                this.uniMarker = null;
            }
            if (this.routePolyline) {
                this.map.removeLayer(this.routePolyline);
                this.routePolyline = null;
            }
            if (this.routeBadgeMarker) {
                this.map.removeLayer(this.routeBadgeMarker);
                this.routeBadgeMarker = null;
            }
            this.updateCommuteBox(null);

            // Sync dropdown & search input
            const uniSelect = document.getElementById('gm-uni-select');
            if (uniSelect) uniSelect.value = '';
            const gmInput = document.getElementById('gm-search-input');
            if (gmInput) gmInput.value = '';
            const gmClear = document.getElementById('gm-search-clear-btn');
            if (gmClear) gmClear.classList.add('hidden');

            // Xóa Empty State banner nếu có
            const emptyBanner = document.getElementById('map-empty-state-banner');
            if (emptyBanner) emptyBanner.remove();

            // Reset markers opacity
            document.querySelectorAll('.gm-price-marker').forEach(el => {
                el.style.opacity = '1';
                el.style.filter = 'none';
                el.style.display = 'inline-flex';
            });

            // Remove campus badges & restore all room cards display
            document.querySelectorAll('.campus-distance-badge').forEach(b => b.remove());
            document.querySelectorAll('.room-item-card').forEach(card => {
                card.style.display = '';
                card.classList.remove('hidden');
            });

            // Trở về chế độ khám phá chỗ đó: Zoom bao quát toàn cảnh khu vực trường vừa xem
            if (this.map) {
                if (prevUni && prevUni.lat && prevUni.lng) {
                    this.map.setView([prevUni.lat, prevUni.lng], 13.5, { animate: true, duration: 0.8 });
                } else if (this.currentCity === 'hcm') {
                    this.map.setView([10.800, 106.705], 13, { animate: true, duration: 0.8 });
                } else if (this.currentCity === 'hanoi') {
                    this.map.setView([21.028, 105.834], 13, { animate: true, duration: 0.8 });
                } else {
                    const curZoom = this.map.getZoom();
                    if (curZoom > 14) {
                        this.map.setView(this.map.getCenter(), 13.5, { animate: true, duration: 0.8 });
                    }
                }
            }

            // Đồng bộ trạng thái active của chip "Tất cả" trên thanh HUD
            document.querySelectorAll('.gm-campus-chip').forEach(btn => {
                btn.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-sm');
                btn.classList.add('bg-slate-900', 'text-slate-300');
            });
            const allChip = document.getElementById('campus-chip-all');
            if (allChip) {
                allChip.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-sm');
                allChip.classList.remove('bg-slate-900', 'text-slate-300');
            }

            if (this.currentCity && this.currentCity !== 'all') {
                // Nếu đang chọn thành phố cụ thể, chỉ hiển thị phòng thuộc thành phố đó
                const cards = document.querySelectorAll('.room-item-card');
                let count = 0;
                cards.forEach(card => {
                    const roomId = card.getAttribute('data-room-id');
                    const roomData = (window.rentyRoomsData && window.rentyRoomsData[roomId]);
                    const markerEl = document.getElementById(`marker-room-${roomId}`);
                    if (!roomData) return;
                    const addr = ((roomData.address || '') + ' ' + (roomData.area_name || '')).toLowerCase();
                    const isHcm = addr.includes('hồ chí minh') || addr.includes('tp.hcm') || addr.includes('tphcm') || addr.includes('hcm')
                        || addr.includes('thủ đức') || addr.includes('bình thạnh') || addr.includes('quận 10') || addr.includes('quận 7')
                        || addr.includes('quận 1') || addr.includes('quận 4') || addr.includes('gò vấp') || addr.includes('tân bình');
                    const isMatch = (this.currentCity === 'hcm' && isHcm) || (this.currentCity === 'hanoi' && !isHcm);
                    if (isMatch) {
                        card.classList.remove('hidden');
                        if (markerEl) markerEl.style.display = 'inline-flex';
                        count++;
                    } else {
                        card.classList.add('hidden');
                        if (markerEl) markerEl.style.display = 'none';
                    }
                });
                const countEl = document.getElementById('results-count');
                if (countEl) countEl.innerHTML = `Tìm thấy <span class="text-emerald-400 font-extrabold">${count}</span> phòng`;
            } else if (typeof window.filterItems === 'function') {
                window.filterItems({ resetPage: true });
            } else {
                const cards = document.querySelectorAll('.room-item-card');
                cards.forEach(card => card.classList.remove('hidden'));
                const countEl = document.getElementById('results-count');
                if (countEl) countEl.innerHTML = `Tìm thấy ${cards.length} phòng`;
            }

            return;
        }

        const uni = this.universities.find(u => u.id === uniId);
        if (!uni) return;

        // Tự động chuyển tab thành phố sang thành phố của trường nếu đang khác
        if (uni.city && this.currentCity !== 'all' && uni.city !== this.currentCity) {
            this.switchCity(uni.city);
        }

        if (this.customPlacePin) {
            this.map.removeLayer(this.customPlacePin);
            this.customPlacePin = null;
        }

        this.selectedCampusId = uniId;
        this.selectedUniversity = uni;
        this.activeCenter = { lat: uni.lat, lng: uni.lng, label: uni.name };

        // Sync dropdown
        const uniSelect = document.getElementById('gm-uni-select');
        if (uniSelect) uniSelect.value = uniId;

        // 1. Chuyển thẳng đến vị trí trường ĐH ở mức cận cảnh (không dùng flyTo để tránh thu nhỏ bản đồ ra xa gây đau mắt)
        this.map.setView([uni.lat, uni.lng], 15, { animate: false });

        // 2. Vẽ marker biểu tượng trường ĐH nổi bật
        if (this.uniMarker) {
            this.map.removeLayer(this.uniMarker);
        }
        const uniIcon = L.divIcon({
            html: `
                <div style="background: linear-gradient(135deg, #ef4444, #b91c1c); color: #ffffff; width: 38px; height: 38px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 18px rgba(239,68,68,0.7), 0 0 0 3px #ffffff; font-size: 15px;">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            `,
            className: 'gm-uni-marker',
            iconSize: [38, 38],
            iconAnchor: [19, 19]
        });
        const addressText = uni.address || uni.campus || '53 Võ Văn Ngân, Linh Chiểu, TP. Thủ Đức';
        this.uniMarker = L.marker([uni.lat, uni.lng], { icon: uniIcon })
            .bindTooltip(`<b>🎓 ${uni.name}</b><br><span style="font-size:10px; color:#38bdf8; font-weight:700;">📍 ${addressText}</span><br><span style="font-size:10px; color:#cbd5e1;">Tâm quét phòng trọ lân cận</span>`, { permanent: true, direction: 'top', className: 'gm-tooltip' })
            .addTo(this.map);

        // 3. Vẽ vòng tròn bán kính quét quanh trường
        if (this.campusRadiusCircle) {
            this.map.removeLayer(this.campusRadiusCircle);
        }
        const activeRadius = this.currentRadius || 3500;
        this.campusRadiusCircle = L.circle([uni.lat, uni.lng], {
            radius: activeRadius,
            color: '#0284c7',
            fillColor: '#38bdf8',
            fillOpacity: 0.12,
            dashArray: '6, 6',
            weight: 2
        }).addTo(this.map);

        // 4. Tính khoảng cách từng phòng và cập nhật danh sách
        const cards = document.querySelectorAll('.room-item-card');
        const roomDistances = [];
        let nearbyCount = 0;

        cards.forEach(card => {
            const roomId = card.getAttribute('data-room-id');
            const roomData = (window.rentyRoomsData && window.rentyRoomsData[roomId]);
            let distKm = 999;
            if (roomData) {
                const coords = this.computeRoomCoordinates(roomData);
                if (coords) {
                    distKm = this.calcHaversineDistance(coords.lat, coords.lng, uni.lat, uni.lng);
                }
            }
            roomDistances.push({ card, distKm, roomId });
        });

        // Lọc hiển thị phòng theo bán kính đã chọn (Chuẩn xác 100% theo khoảng cách thực tế)
        const maxRadiusKm = (activeRadius / 1000);
        roomDistances.forEach(item => {
            const isNearby = item.distKm <= maxRadiusKm;
            const markerEl = this.getMarkerElementForRoom(item.roomId);

            if (isNearby) {
                item.card.classList.remove('hidden');
                item.card.style.display = '';
                item.card.dataset.filteredOut = 'false';
                nearbyCount++;

                if (markerEl) {
                    markerEl.style.opacity = '1';
                    markerEl.style.filter = 'drop-shadow(0 0 6px rgba(56, 189, 248, 0.7))';
                }

                // Gắn huy hiệu khoảng cách vào bên trong thông tin thẻ phòng
                let badge = item.card.querySelector('.campus-distance-badge');
                if (!badge) {
                    badge = document.createElement('div');
                    badge.className = 'campus-distance-badge w-full my-2 px-3 py-1.5 rounded-xl bg-sky-500/10 border border-sky-500/25 text-sky-300 text-xs font-semibold flex items-center justify-between gap-2 transition-all';
                    const detailsEl = item.card.querySelector('.room-card-content .p-5') || item.card.querySelector('.p-5') || item.card;
                    const tagsEl = detailsEl.querySelector('.flex-wrap');
                    if (tagsEl) {
                        detailsEl.insertBefore(badge, tagsEl);
                    } else {
                        detailsEl.appendChild(badge);
                    }
                }
                const bikeMins = Math.max(2, Math.round((item.distKm / 24) * 60 + 2));
                const walkMins = Math.max(3, Math.round((item.distKm / 4.5) * 60));
                const cleanUniLabel = uni.shortName || uni.name.split('(')[0].trim();
                badge.innerHTML = `
                    <span class="flex items-center gap-1.5 shrink-0 text-sky-300 font-bold">
                        <i class="fa-solid fa-graduation-cap text-sky-400"></i> Cách ${cleanUniLabel}:
                    </span>
                    <span class="text-white font-extrabold text-right shrink-0">
                        ${item.distKm.toFixed(1)} km <span class="text-slate-400 font-normal text-[11px]">(${bikeMins}p xe máy / ${walkMins}p đi bộ)</span>
                    </span>
                `;
            } else {
                if (markerEl) {
                    markerEl.style.opacity = '0.35';
                    markerEl.style.filter = 'none';
                }
                item.card.classList.add('hidden');
                item.card.style.display = 'none';
                item.card.dataset.filteredOut = 'true';
            }
        });

        // Xóa banner Empty State cũ nếu có
        let existingBanner = document.getElementById('map-empty-state-banner');
        if (existingBanner) existingBanner.remove();

        const countEl = document.getElementById('results-count');
        const roomsGrid = document.getElementById('rooms-grid');
        const cleanUniName = uni.name.split('(')[0].trim();
        const radiusKmText = (activeRadius / 1000).toFixed(1);

        if (nearbyCount === 0) {
            // [ERR_05_02]: Không có phòng trong bán kính chọn
            // Chuyển vòng quét sang viền đỏ nét đứt cảnh báo theo spec
            if (this.campusRadiusCircle) {
                this.campusRadiusCircle.setStyle({
                    color: '#f43f5e',
                    fillColor: '#f43f5e',
                    fillOpacity: 0.16,
                    dashArray: '8, 8',
                    weight: 3
                });
            }

            // Đảm bảo ẩn toàn bộ các thẻ phòng trọ khi không có phòng nào trong bán kính
            cards.forEach(c => {
                c.classList.add('hidden');
                c.style.display = 'none';
                c.dataset.filteredOut = 'true';
            });

            if (countEl) {
                countEl.innerHTML = `<span class="text-rose-400 font-bold"><i class="fa-solid fa-triangle-exclamation mr-1.5"></i>Không tìm thấy phòng trọ nào quanh ${cleanUniName} (< ${radiusKmText} km)</span>`;
            }

            if (roomsGrid) {
                const emptyBanner = document.createElement('div');
                emptyBanner.id = 'map-empty-state-banner';
                emptyBanner.className = 'w-full my-6 p-6 rounded-3xl bg-slate-900 border-2 border-rose-500/80 text-center shadow-2xl col-span-full animate-fade-in';
                emptyBanner.innerHTML = `
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-2xl mb-3 shadow-lg">
                        <i class="fa-solid fa-location-crosshairs"></i>
                    </div>
                    <h4 class="text-white font-black text-base mt-2 mb-1.5">Không tìm thấy phòng trọ nào quanh ${cleanUniName}</h4>
                    <p class="text-slate-300 text-xs mb-4 max-w-md mx-auto leading-relaxed">
                        Hiện không có phòng trọ nào trong bán kính <span class="text-rose-400 font-bold">&lt; ${radiusKmText} km</span> quanh trường này. Bạn có thể mở rộng bán kính quét, chọn trường đại học khác hoặc chuyển sang cơ sở lưu trú gần bạn.
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-2.5">
                        <button type="button" onclick="applyPresetRadius(6)" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-lg shadow-sky-500/20 inline-flex items-center gap-1.5 transition-all cursor-pointer">
                            <i class="fa-solid fa-expand"></i> Mở rộng 6 km
                        </button>
                        <button type="button" onclick="applyPresetRadius(10)" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-500/20 inline-flex items-center gap-1.5 transition-all cursor-pointer">
                            <i class="fa-solid fa-maximize"></i> Mở rộng 10 km
                        </button>
                        <button type="button" onclick="if(typeof clearMapSearchInput==='function'){clearMapSearchInput();}else{location.reload();}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 inline-flex items-center gap-1.5 transition-all cursor-pointer">
                            <i class="fa-solid fa-rotate-left"></i> Xem tất cả phòng
                        </button>
                    </div>
                `;
                roomsGrid.parentNode.insertBefore(emptyBanner, roomsGrid);
            }

            if (typeof renderPaginationControls === 'function') {
                renderPaginationControls(0);
            }
        } else {
            // Có phòng phù hợp -> Vòng quét màu xanh
            if (this.campusRadiusCircle) {
                this.campusRadiusCircle.setStyle({
                    color: '#0284c7',
                    fillColor: '#38bdf8',
                    fillOpacity: 0.12,
                    dashArray: '6, 6',
                    weight: 2
                });
            }

            // Phân trang danh sách phòng phù hợp
            let visibleIdx = 0;
            roomDistances.forEach(item => {
                if (item.distKm <= maxRadiusKm) {
                    if (visibleIdx < 9) {
                        item.card.classList.remove('hidden');
                        item.card.style.display = '';
                    } else {
                        item.card.classList.add('hidden');
                        item.card.style.display = 'none';
                    }
                    visibleIdx++;
                }
            });

            // Cập nhật tiêu đề danh sách
            if (countEl) {
                countEl.innerHTML = `🎯 Tìm thấy <span class="text-emerald-400 font-extrabold">${nearbyCount}</span> phòng trong bán kính ${radiusKmText}km quanh <span class="text-sky-300 font-bold">${cleanUniName}</span> <button type="button" onclick="selectCampusFilter('all')" class="ml-2 px-2 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white text-[11px] font-semibold transition-all cursor-pointer"><i class="fa-solid fa-xmark mr-1"></i>Hủy lọc</button>`;
            }

            if (typeof renderPaginationControls === 'function') {
                const totalPages = Math.ceil(nearbyCount / 9);
                renderPaginationControls(totalPages);
            }
        }

        // Xóa tuyến đường nét đứt cũ nếu có
        if (this.routePolyline) {
            this.map.removeLayer(this.routePolyline);
            this.routePolyline = null;
        }
        if (this.routeBadgeMarker) {
            this.map.removeLayer(this.routeBadgeMarker);
            this.routeBadgeMarker = null;
        }
        if (typeof this.updateCommuteBox === 'function') {
            this.updateCommuteBox(null);
        }
    }

    setUniversityDestination(uniId) {
        this.selectCampus(uniId);
    }

    /* =========================================================================
       CITY SWITCHER (TP.HCM / HÀ NỘI / TOÀN QUỐC)
       ========================================================================= */
    switchCity(city) {
        this.currentCity = city;
        this.selectedCampusId = 'all';
        this.selectedUniversity = null;
        this.activeCenter = null;

        // Đồng bộ giao diện tab thành phố trên HUD Hàng 2
        document.querySelectorAll('.gm-city-tab').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-sm');
            btn.classList.add('text-slate-300');
        });
        const activeCityTab = document.getElementById(`city-tab-${city}`);
        if (activeCityTab) {
            activeCityTab.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-sm');
            activeCityTab.classList.remove('text-slate-300');
            if (typeof activeCityTab.scrollIntoView === 'function') {
                activeCityTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
        }

        if (this.campusRadiusCircle) {
            this.map.removeLayer(this.campusRadiusCircle);
            this.campusRadiusCircle = null;
        }
        if (this.uniMarker) {
            this.map.removeLayer(this.uniMarker);
            this.uniMarker = null;
        }
        if (this.routePolyline) {
            this.map.removeLayer(this.routePolyline);
            this.routePolyline = null;
        }
        this.updateCommuteBox(null);
        document.querySelectorAll('.campus-distance-badge').forEach(b => b.remove());

        // Di chuyển đến trung tâm thành phố tương ứng êm dịu, không giật màn hình
        const cityCoords = {
            'hcm': [10.800, 106.705],
            'hanoi': [21.028, 105.834],
            'danang': [16.060, 108.220],
            'dalat': [11.942, 108.440],
            'nhatrang': [12.245, 109.196],
            'vungtau': [10.346, 107.084],
            'hoian': [15.880, 108.338],
            'phuquoc': [10.228, 103.957],
            'hue': [16.463, 107.590],
            'sapa': [22.336, 103.843]
        };

        if (cityCoords[city]) {
            this.map.setView(cityCoords[city], 13, { animate: false });
        } else {
            this.resetView();
        }

        // Cập nhật giao diện thanh Chips
        this.renderCampusChips();

        // Lọc danh sách thẻ phòng hiển thị theo thành phố
        const cards = document.querySelectorAll('.room-item-card');
        if (city === 'all') {
            if (typeof window.filterItems === 'function') {
                window.filterItems({ resetPage: true });
            } else {
                cards.forEach(card => card.classList.remove('hidden'));
            }
            document.querySelectorAll('.gm-price-marker').forEach(el => el.style.display = 'inline-flex');
            const countEl = document.getElementById('results-count');
            if (countEl) {
                countEl.innerHTML = `Tìm thấy <span class="text-emerald-400 font-extrabold">${cards.length}</span> phòng`;
            }
            return;
        }

        let visibleCount = 0;
        const visibleBuildingIds = new Set();

        cards.forEach(card => {
            const roomId = card.getAttribute('data-room-id');
            const roomData = (window.rentyRoomsData && window.rentyRoomsData[roomId]);
            if (!roomData) return;

            const addr = ((roomData.address || '') + ' ' + (roomData.area_name || '') + ' ' + (roomData.building_address || '')).toLowerCase();
            const isHcm = addr.includes('hồ chí minh') || addr.includes('hcm') || addr.includes('thủ đức') || addr.includes('bình thạnh') || addr.includes('quận 10') || addr.includes('quận 7') || addr.includes('gò vấp') || addr.includes('tân bình') || addr.includes('phú nhuận') || addr.includes('quận 1') || addr.includes('quận 3') || addr.includes('quận 5');
            const isDanang = addr.includes('đà nẵng') || addr.includes('da nang') || addr.includes('hải châu') || addr.includes('sơn trà') || addr.includes('ngũ hành sơn') || addr.includes('thanh khê') || addr.includes('liên chiểu') || addr.includes('cẩm lệ') || addr.includes('hòa vang');
            const isDalat = addr.includes('đà lạt') || addr.includes('da lat') || addr.includes('lâm đồng') || addr.includes('tuyền lâm') || addr.includes('trại mát') || addr.includes('vạn thành') || addr.includes('thái phiên');
            const isHanoi = addr.includes('hà nội') || addr.includes('hanoi') || addr.includes('cầu giấy') || addr.includes('đống đa') || addr.includes('thanh xuân') || addr.includes('tây hồ') || addr.includes('ba đình') || addr.includes('hai bà trưng') || addr.includes('nam từ liêm') || addr.includes('bắc từ liêm') || addr.includes('hà đông') || addr.includes('hoàng mai');
            const isNhatrang = addr.includes('nha trang') || addr.includes('khánh hòa');
            const isVungtau = addr.includes('vũng tàu') || addr.includes('bà rịa');
            const isHoian = addr.includes('hội an') || addr.includes('quảng nam');
            const isPhuquoc = addr.includes('phú quốc') || addr.includes('kiên giang');
            const isHue = addr.includes('huế') || addr.includes('thừa thiên');
            const isSapa = addr.includes('sa pa') || addr.includes('sapa') || addr.includes('lào cai');

            let matchesCity = false;
            if (city === 'hcm') matchesCity = isHcm;
            else if (city === 'hanoi') matchesCity = isHanoi;
            else if (city === 'danang') matchesCity = isDanang;
            else if (city === 'dalat') matchesCity = isDalat;
            else if (city === 'nhatrang') matchesCity = isNhatrang;
            else if (city === 'vungtau') matchesCity = isVungtau;
            else if (city === 'hoian') matchesCity = isHoian;
            else if (city === 'phuquoc') matchesCity = isPhuquoc;
            else if (city === 'hue') matchesCity = isHue;
            else if (city === 'sapa') matchesCity = isSapa;

            if (matchesCity) {
                card.classList.remove('hidden');
                visibleCount++;
                const bId = this.buildingMapByRoomId ? this.buildingMapByRoomId[roomId] : null;
                if (bId) visibleBuildingIds.add(String(bId));
            } else {
                card.classList.add('hidden');
            }
        });

        // Ẩn/hiện marker tòa nhà tương ứng với thành phố
        document.querySelectorAll('.gm-building-marker').forEach(markerEl => {
            const bId = markerEl.getAttribute('data-building-id');
            if (visibleBuildingIds.has(String(bId))) {
                markerEl.style.display = 'inline-flex';
            } else {
                markerEl.style.display = 'none';
            }
        });

        const countEl = document.getElementById('results-count');
        if (countEl) {
            const cityNameMap = {
                'hcm': 'TP. Hồ Chí Minh',
                'hanoi': 'Hà Nội',
                'danang': 'Đà Nẵng',
                'dalat': 'Đà Lạt',
                'nhatrang': 'Nha Trang',
                'vungtau': 'Vũng Tàu',
                'hoian': 'Hội An',
                'phuquoc': 'Phú Quốc',
                'hue': 'Huế',
                'sapa': 'Sa Pa'
            };
            const cityName = cityNameMap[city] || 'toàn quốc';
            countEl.innerHTML = `Tìm thấy <span class="text-emerald-400 font-extrabold">${visibleCount}</span> phòng tại <span class="text-sky-300 font-bold">${cityName}</span>`;
        }
    }

    setCommuteRadius(radiusMeters) {
        this.currentRadius = radiusMeters;
        const radiusKm = (radiusMeters / 1000);

        // Đồng bộ lên thanh slider range_radius và label nếu có
        const slider = document.getElementById('range_radius');
        const sliderLabel = document.getElementById('range_radius_val');
        if (slider) slider.value = radiusKm;
        if (sliderLabel) sliderLabel.textContent = `${radiusKm}km`;

        // Đồng bộ active state các preset chips
        document.querySelectorAll('.gm-radius-chip').forEach(chip => {
            chip.classList.remove('active', 'bg-emerald-600', 'text-white', 'font-bold');
            chip.classList.add('text-slate-400');
        });
        const matchedChip = document.getElementById(`radius-chip-${radiusMeters}`);
        if (matchedChip) {
            matchedChip.classList.add('active', 'bg-emerald-600', 'text-white', 'font-bold');
            matchedChip.classList.remove('text-slate-400');
        }

        if (this.selectedUniversity && this.selectedCampusId && this.selectedCampusId !== 'all') {
            this.selectCampus(this.selectedUniversity.id);
        } else if (this.activeCenter) {
            this.scanRadiusAtLocation(this.activeCenter.lat, this.activeCenter.lng, this.activeCenter.label, radiusMeters);
        }
    }

    showCoordErrorToast(customMessage = null) {
        const toast = document.getElementById('gm-coord-error-toast');
        if (toast) {
            if (customMessage) {
                const span = toast.querySelector('span');
                if (span) span.textContent = customMessage;
            }
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 5500);
        } else if (typeof showRentyToast === 'function') {
            showRentyToast(customMessage || 'Tọa độ tìm kiếm nằm ngoài phạm vi bản đồ hỗ trợ.', 'error', 'Mã lỗi: ERR_05_03');
        }
    }

    scanRadiusAtLocation(lat, lng, label = 'Địa điểm đã chọn', customRadiusMeters = null) {
        if (!this.map) return;

        // [ERR_05_03]: Tọa độ địa lý không hợp lệ hoặc nằm ngoài phạm vi bản đồ hỗ trợ (Việt Nam: lat 8.0 - 24.0, lng 101.0 - 111.0)
        const numLat = parseFloat(lat);
        const numLng = parseFloat(lng);
        if (isNaN(numLat) || isNaN(numLng) || numLat < 8.0 || numLat > 24.0 || numLng < 101.0 || numLng > 111.0) {
            console.warn('[ERR_05_03] Tọa độ tìm kiếm nằm ngoài phạm vi bản đồ hỗ trợ:', { lat, lng });
            this.showCoordErrorToast();
            this.resetView(); // Tự động chuyển tâm bản đồ về khu vực trung tâm mặc định
            return;
        }

        this.activeCenter = { lat: numLat, lng: numLng, label };
        const activeRadius = customRadiusMeters || this.currentRadius || 5000;

        // 1. Vẽ vòng tròn bán kính quét quanh địa điểm
        if (this.campusRadiusCircle) {
            this.map.removeLayer(this.campusRadiusCircle);
        }
        this.campusRadiusCircle = L.circle([numLat, numLng], {
            radius: activeRadius,
            color: '#0284c7',
            fillColor: '#38bdf8',
            fillOpacity: 0.12,
            dashArray: '6, 6',
            weight: 2
        }).addTo(this.map);

        // 2. Canh khung bản đồ để nhìn rõ vòng tròn bán kính
        try {
            this.map.fitBounds(this.campusRadiusCircle.getBounds(), { padding: [35, 35], maxZoom: 15, animate: false });
        } catch (e) {
            this.map.setView([numLat, numLng], 14, { animate: false });
        }

        // 3. Tính khoảng cách từng phòng và cập nhật danh sách
        const cards = document.querySelectorAll('.room-item-card');
        const roomDistances = [];
        let nearbyCount = 0;

        cards.forEach(card => {
            const roomId = card.getAttribute('data-room-id');
            const roomData = (window.rentyRoomsData && window.rentyRoomsData[roomId]);
            let distKm = 999;
            if (roomData) {
                const coords = this.computeRoomCoordinates(roomData);
                if (coords) {
                    distKm = this.calcHaversineDistance(coords.lat, coords.lng, numLat, numLng);
                }
            }
            roomDistances.push({ card, distKm, roomId });
        });

        const maxRadiusKm = (activeRadius / 1000);
        roomDistances.forEach(item => {
            const isNearby = item.distKm <= maxRadiusKm;
            const markerEl = this.getMarkerElementForRoom(item.roomId);

            if (isNearby) {
                item.card.classList.remove('hidden');
                item.card.style.display = '';
                item.card.dataset.filteredOut = 'false';
                nearbyCount++;

                if (markerEl) {
                    markerEl.style.opacity = '1';
                    markerEl.style.filter = 'drop-shadow(0 0 6px rgba(56, 189, 248, 0.7))';
                }

                // Gắn huy hiệu khoảng cách vào bên trong thông tin thẻ phòng
                let badge = item.card.querySelector('.campus-distance-badge');
                if (!badge) {
                    badge = document.createElement('div');
                    badge.className = 'campus-distance-badge w-full my-2 px-3 py-1.5 rounded-xl bg-sky-500/10 border border-sky-500/25 text-sky-300 text-xs font-semibold flex items-center justify-between gap-2 transition-all';
                    const detailsEl = item.card.querySelector('.room-card-content .p-5') || item.card.querySelector('.p-5') || item.card;
                    const tagsEl = detailsEl.querySelector('.flex-wrap');
                    if (tagsEl) {
                        detailsEl.insertBefore(badge, tagsEl);
                    } else {
                        detailsEl.appendChild(badge);
                    }
                }
                const bikeMins = Math.max(2, Math.round((item.distKm / 24) * 60 + 2));
                const walkMins = Math.max(3, Math.round((item.distKm / 4.5) * 60));
                badge.innerHTML = `
                    <span class="flex items-center gap-1.5 shrink-0 text-sky-300 font-bold">
                        <i class="fa-solid fa-location-arrow text-sky-400"></i> Cách ${label.split('(')[0].trim()}:
                    </span>
                    <span class="text-white font-extrabold text-right shrink-0">
                        ${item.distKm.toFixed(1)} km <span class="text-slate-400 font-normal text-[11px]">(${bikeMins}p xe máy / ${walkMins}p đi bộ)</span>
                    </span>
                `;
            } else {
                if (markerEl) {
                    markerEl.style.opacity = '0.35';
                    markerEl.style.filter = 'none';
                }
                item.card.classList.add('hidden');
                item.card.style.display = 'none';
                item.card.dataset.filteredOut = 'true';
            }
        });

        // Xử lý Empty State hoặc phân trang cho danh sách phòng gần đây
        const cleanLabel = label.split('(')[0].trim();
        let existingBanner = document.getElementById('map-empty-state-banner');
        if (existingBanner) existingBanner.remove();

        const countEl = document.getElementById('results-count');
        const roomsGrid = document.getElementById('rooms-grid');

        // [ERR_05_02]: Không có phòng trong bán kính chọn
        if (nearbyCount === 0) {
            // Ẩn toàn bộ thẻ phòng
            cards.forEach(c => {
                c.classList.add('hidden');
                c.style.display = 'none';
                c.dataset.filteredOut = 'true';
            });

            // Hiển thị vòng quét trên bản đồ dạng viền đỏ cảnh báo theo spec
            if (this.campusRadiusCircle) {
                this.campusRadiusCircle.setStyle({
                    color: '#f43f5e',
                    fillColor: '#f43f5e',
                    fillOpacity: 0.16,
                    dashArray: '8, 8',
                    weight: 3
                });
            }

            if (countEl) {
                countEl.innerHTML = `<span class="text-rose-400 font-bold"><i class="fa-solid fa-triangle-exclamation mr-1.5"></i>Không tìm thấy phòng trọ nào trong bán kính đã chọn (${(activeRadius / 1000).toFixed(1)} km quanh ${cleanLabel})</span> <button type="button" onclick="clearMapSearchInput()" class="ml-2 px-2.5 py-1 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 border border-emerald-500/30 text-emerald-300 text-xs font-bold transition-all cursor-pointer"><i class="fa-solid fa-rotate-left mr-1"></i>Thoát ra</button>`;
            }

            if (roomsGrid) {
                const emptyBanner = document.createElement('div');
                emptyBanner.id = 'map-empty-state-banner';
                emptyBanner.className = 'w-full my-6 p-6 rounded-3xl bg-slate-900 border-2 border-rose-500 text-center shadow-2xl col-span-full';
                emptyBanner.innerHTML = `
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-2xl mb-3 shadow-lg">
                        <i class="fa-solid fa-location-crosshairs"></i>
                    </div>
                    <h4 class="text-white font-black text-base mt-2 mb-1.5">Không tìm thấy phòng trọ nào trong bán kính đã chọn.</h4>
                    <p class="text-slate-300 text-xs mb-4 max-w-md mx-auto leading-relaxed">
                        Vòng quét bán kính <span class="text-rose-400 font-bold">${(activeRadius / 1000).toFixed(1)} km</span> quanh <span class="text-white font-bold">"${cleanLabel}"</span> hiện không có phòng trọ phù hợp. Bạn hãy tăng bán kính quét hoặc xem toàn bộ phòng.
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-2.5">
                        <button type="button" onclick="applyPresetRadius(5)" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-lg shadow-sky-500/20 inline-flex items-center gap-1.5 transition-all cursor-pointer">
                            <i class="fa-solid fa-expand"></i> Mở rộng lên 5 km
                        </button>
                        <button type="button" onclick="applyPresetRadius(10)" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-500/20 inline-flex items-center gap-1.5 transition-all cursor-pointer">
                            <i class="fa-solid fa-maximize"></i> Mở rộng lên 10 km
                        </button>
                        <button type="button" onclick="clearMapSearchInput()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 inline-flex items-center gap-1.5 transition-all cursor-pointer">
                            <i class="fa-solid fa-rotate-left"></i> Xem tất cả phòng
                        </button>
                    </div>
                `;
                roomsGrid.parentNode.insertBefore(emptyBanner, roomsGrid);
            }

            if (typeof renderPaginationControls === 'function') {
                renderPaginationControls(0);
            }
        } else {
            // Có phòng -> phục hồi màu vòng quét bình thường
            if (this.campusRadiusCircle) {
                this.campusRadiusCircle.setStyle({
                    color: '#0284c7',
                    fillColor: '#38bdf8',
                    fillOpacity: 0.12,
                    dashArray: '6, 6',
                    weight: 2
                });
            }

            if (countEl) {
                countEl.innerHTML = `🎯 Tìm thấy <span class="text-emerald-400 font-extrabold">${nearbyCount}</span> phòng trong bán kính ${(activeRadius / 1000).toFixed(1)}km quanh <span class="text-sky-300 font-bold">${cleanLabel}</span> <button type="button" onclick="clearMapSearchInput()" class="ml-2 px-2 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white text-[11px] font-semibold transition-all cursor-pointer"><i class="fa-solid fa-xmark mr-1"></i>Hủy lọc</button>`;
            }

            // Phân trang 9 phòng đầu cho danh sách phòng gần đây
            let visibleIdx = 0;
            roomDistances.forEach(item => {
                if (item.distKm <= maxRadiusKm) {
                    if (visibleIdx < 9) {
                        item.card.classList.remove('hidden');
                        item.card.style.display = '';
                    } else {
                        item.card.classList.add('hidden');
                    }
                    visibleIdx++;
                }
            });

            if (typeof renderPaginationControls === 'function') {
                renderPaginationControls(Math.ceil(nearbyCount / 9));
            }
        }

        // Xóa tuyến đường nét đứt cũ nếu có
        if (this.routePolyline) {
            this.map.removeLayer(this.routePolyline);
            this.routePolyline = null;
        }
        if (this.routeBadgeMarker) {
            this.map.removeLayer(this.routeBadgeMarker);
            this.routeBadgeMarker = null;
        }
        if (typeof this.updateCommuteBox === 'function') {
            this.updateCommuteBox(null);
        }
    }

    renderCampusChips() {
        const bar = document.querySelector('.gm-campus-bar');
        if (!bar) return;

        let filteredUnis = this.universities;
        if (this.currentCity === 'hcm') {
            filteredUnis = this.universities.filter(u => u.city === 'hcm');
        } else if (this.currentCity === 'hanoi') {
            filteredUnis = this.universities.filter(u => u.city === 'hanoi');
        } else if (this.currentCity === 'danang') {
            filteredUnis = this.universities.filter(u => u.city === 'danang');
        } else if (this.currentCity === 'dalat') {
            filteredUnis = this.universities.filter(u => u.city === 'dalat');
        }

        let html = `
            <button type="button" onclick="selectCampusFilter('all', this)" id="campus-chip-all" class="gm-campus-chip active px-2 py-0.5 rounded-lg bg-blue-600 text-white text-[11px] font-bold transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                <span>🌐 Tất cả</span>
            </button>
        `;

        filteredUnis.forEach(uni => {
            const label = uni.shortName || uni.name;
            html += `
                <button type="button" onclick="selectCampusFilter('${uni.id}', this)" class="gm-campus-chip px-2 py-0.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white text-[11px] font-medium transition-all shrink-0 flex items-center gap-1 cursor-pointer">
                    <span>🎓 ${label}</span>
                </button>
            `;
        });

        bar.innerHTML = html;
    }


    async searchGeocodeOrCampus(keyword) {
        if (!keyword || !keyword.trim()) return;
        const normKey = removeVietnameseTones(keyword);
        const tokens = normKey.split(/\s+/).filter(Boolean);
        if (!tokens.length) return;

        // 1. Tìm trong danh sách trường & cơ sở (Tokenized + Aliases + Teencode)
        let effectiveTokens = tokens;
        if (tokens.length > 1 && (tokens.includes('truong') || tokens.includes('trg'))) {
            effectiveTokens = tokens.filter(t => t !== 'truong' && t !== 'trg');
        }

        const matchedUni = this.universities.find(u => {
            const searchPool = [
                'truong',
                u.type === 'college' ? 'cao dang cd cđ' : 'dai hoc dh đh',
                removeVietnameseTones(u.name || ''),
                removeVietnameseTones(u.shortName || ''),
                removeVietnameseTones(u.campus || ''),
                removeVietnameseTones(u.district || ''),
                removeVietnameseTones(u.address || ''),
                removeVietnameseTones(u.fullName || ''),
                removeVietnameseTones(u.id || ''),
                ...(u.aliases || []).map(a => removeVietnameseTones(a))
            ];
            return effectiveTokens.every(tok => {
                if (tok === 'cd' || tok === 'cđ') return u.type === 'college' || searchPool.some(f => f.includes('cao dang'));
                if (tok === 'dh' || tok === 'đh') return u.type !== 'college' || searchPool.some(f => f.includes('dai hoc'));
                return searchPool.some(field => field.includes(tok));
            });
        });

        if (matchedUni) {
            // Tự động chuyển thành phố nếu khác
            if (this.currentCity !== 'all' && matchedUni.city !== this.currentCity) {
                this.switchCity(matchedUni.city);
            }
            this.selectCampus(matchedUni.id);
            // Highlight chip
            const chip = Array.from(document.querySelectorAll('.gm-campus-chip')).find(el => el.textContent.includes(matchedUni.name.split('(')[0].trim()));
            if (chip) {
                document.querySelectorAll('.gm-campus-chip').forEach(c => c.classList.remove('active', 'bg-blue-600', 'text-white'));
                chip.classList.add('active', 'bg-blue-600', 'text-white');
            }
            return;
        }

        // 2. Tìm theo Quận / Địa danh hành chính
        const districtCoordinates = {
            'binh thanh': [10.803, 106.711],
            'thu duc': [10.850, 106.772],
            'quan 10': [10.772, 106.669],
            'quan 7': [10.730, 106.695],
            'go vap': [10.838, 106.665],
            'quan 1': [10.778, 106.699],
            'tan binh': [10.798, 106.653],
            'phu nhuan': [10.799, 106.680],
            'cau giay': [21.036, 105.790],
            'dong da': [21.018, 105.827],
            'thanh xuan': [20.998, 105.811],
            'hai ba trung': [21.008, 105.852],
            'quan 3': [10.781, 106.684],
            'quan 4': [10.760, 106.703],
            'quan 5': [10.755, 106.666],
            'quan 6': [10.748, 106.635],
            'quan 8': [10.725, 106.650],
            'quan 11': [10.766, 106.649],
            'quan 12': [10.867, 106.641],
            'tan phu': [10.790, 106.628],
            'binh tan': [10.750, 106.608],
            'nha be': [10.695, 106.730],
            'binh chanh': [10.690, 106.580],
            'ba dinh': [21.034, 105.824],
            'hoan kiem': [21.028, 105.854],
            'nam tu liem': [21.013, 105.765],
            'bac tu liem': [21.055, 105.755],
            'hoang mai': [20.975, 105.850],
            'long bien': [21.040, 105.880],
            'ho guom': [21.028, 105.852],
            'landmark 81': [10.795, 106.722],
            'ben thanh': [10.772, 106.698],
            'san bay': [10.818, 106.652],

            // Đà Nẵng
            'hai chau': [16.068, 108.220],
            'son tra': [16.065, 108.245],
            'ngu hanh son': [16.035, 108.248],
            'thanh khe': [16.063, 108.190],
            'lien chieu': [16.070, 108.155],
            'cam le': [16.025, 108.205],
            'hoa vang': [16.010, 108.140],
            'cau rong': [16.060, 108.227],
            'song han': [16.072, 108.225],
            'my khe': [16.062, 108.246],
            'da nang': [16.060, 108.220],

            // Đà Lạt
            'da lat': [11.942, 108.440],
            'dalat': [11.942, 108.440],
            'lam dong': [11.942, 108.440],
            'ho xuan huong': [11.942, 108.438],
            'tuyen lam': [11.895, 108.432],
            'trai mat': [11.945, 108.498],
            'thai phien': [11.968, 108.485],
            'van thanh': [11.941, 108.412],
            'cho dem da lat': [11.941, 108.435],
            'langbiang': [12.045, 108.435],
            'phuong 1': [11.942, 108.438],
            'phuong 2': [11.947, 108.441],
            'phuong 3': [11.926, 108.443],
            'phuong 4': [11.939, 108.416],
            'phuong 8': [11.960, 108.448],
            'phuong 9': [11.948, 108.472],
            'phuong 10': [11.939, 108.450],
            'phuong 11': [11.945, 108.498],
            'phuong 12': [11.968, 108.485],
        };

        for (const [dist, coords] of Object.entries(districtCoordinates)) {
            if (normKey.includes(dist)) {
                this.map.flyTo(coords, 14, { duration: 1.2 });
                return;
            }
        }

        // 3. Ưu tiên tìm phòng trọ trực tiếp bằng Apache Solr Engine
        try {
            const solrRes = await fetch(`/api/renty/rooms/smart-search?q=${encodeURIComponent(keyword)}&engine=solr&limit=1`);
            const solrData = await solrRes.json();
            if (solrData && solrData.success && solrData.rooms && solrData.rooms.length > 0) {
                const targetRoom = solrData.rooms[0];
                const coords = this.computeRoomCoordinates(targetRoom);
                if (coords && !isNaN(coords.lat) && !isNaN(coords.lng)) {
                    // Zoom mượt mà trực tiếp đến vị trí phòng trọ
                    this.map.flyTo([coords.lat, coords.lng], 16, { duration: 1.2 });

                    setTimeout(() => {
                        if (this.markers && this.markers[targetRoom.id]) {
                            this.markers[targetRoom.id].openPopup();
                            this.highlightMarker(targetRoom.id);
                        }
                    }, 1250);

                    // Đồng thời cuộn và highlight thẻ phòng ở danh sách
                    if (typeof window.highlightRoomCard === 'function') {
                        window.highlightRoomCard(targetRoom.id);
                    } else {
                        const card = document.querySelector(`.room-item-card[data-room-id="${targetRoom.id}"]`);
                        if (card) {
                            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                            card.classList.add('ring-2', 'ring-amber-400');
                            setTimeout(() => card.classList.remove('ring-2', 'ring-amber-400'), 2500);
                        }
                    }
                    return;
                }
            }
        } catch (e) {
            console.warn('[Solr Map Search Error]', e);
        }

        // 4. Tìm vị trí bất kỳ (Tên đường, địa chỉ tự do) qua Live Geocoding
        this.geocodeAndFly(keyword);
    }

    geocodeAndFly(addressOrPlace) {
        if (!addressOrPlace || !this.map) return;
        const normKey = removeVietnameseTones(addressOrPlace);
        const tokens = normKey.split(/\s+/).filter(Boolean);

        // 1. Kiểm tra nhanh nếu là trường đại học / cao đẳng trong danh bạ
        if (this.universities && this.universities.length > 0 && tokens.length > 0) {
            let effectiveTokens = tokens;
            if (tokens.length > 1 && (tokens.includes('truong') || tokens.includes('trg'))) {
                effectiveTokens = tokens.filter(t => t !== 'truong' && t !== 'trg');
            }
            const matchedUni = this.universities.find(u => {
                const searchPool = [
                    'truong',
                    u.type === 'college' ? 'cao dang cd cđ' : 'dai hoc dh đh',
                    removeVietnameseTones(u.name || ''),
                    removeVietnameseTones(u.shortName || ''),
                    removeVietnameseTones(u.campus || ''),
                    removeVietnameseTones(u.district || ''),
                    removeVietnameseTones(u.address || ''),
                    removeVietnameseTones(u.fullName || ''),
                    removeVietnameseTones(u.id || ''),
                    ...(u.aliases || []).map(a => removeVietnameseTones(a))
                ];
                return effectiveTokens.every(tok => {
                    if (tok === 'cd' || tok === 'cđ') return u.type === 'college' || searchPool.some(f => f.includes('cao dang'));
                    if (tok === 'dh' || tok === 'đh') return u.type !== 'college' || searchPool.some(f => f.includes('dai hoc'));
                    return searchPool.some(field => field.includes(tok));
                });
            });

            if (matchedUni) {
                if (this.currentCity !== 'all' && matchedUni.city !== this.currentCity) {
                    this.switchCity(matchedUni.city);
                }
                this.selectCampus(matchedUni.id);
                const chip = document.querySelector(`.gm-campus-chip[onclick*="'${matchedUni.id}'"]`);
                if (chip) {
                    document.querySelectorAll('.gm-campus-chip').forEach(c => c.classList.remove('active', 'bg-blue-600', 'text-white'));
                    chip.classList.add('active', 'bg-blue-600', 'text-white');
                    if (typeof chip.scrollIntoView === 'function') {
                        chip.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    }
                }
                return;
            }
        }

        // 2. Tìm kiếm qua dịch vụ Live Geocoding OpenStreetMap
        const query = encodeURIComponent(addressOrPlace.trim() + ', Vietnam');
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&limit=1`, {
            headers: { 'Accept-Language': 'vi,en' }
        })
            .then(res => res.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    if (!isNaN(lat) && !isNaN(lon)) {
                        this.scanRadiusAtLocation(lat, lon, addressOrPlace);
                        return;
                    }
                }
                // Không tìm thấy tọa độ hợp lệ
                console.warn('[Geocoding] Không tìm thấy tọa độ cho:', addressOrPlace);
                if (typeof showRentyToast === 'function') {
                    showRentyToast(`Không tìm thấy vị trí "${addressOrPlace}". Vui lòng thử từ khóa khác.`, 'warning');
                } else {
                    this.showCoordErrorToast(`Không tìm thấy vị trí "${addressOrPlace}". Vui lòng thử từ khóa khác.`);
                }
            })
            .catch(() => {
                console.warn('[Geocoding Error] Lỗi kết nối geocode cho:', addressOrPlace);
                if (typeof showRentyToast === 'function') {
                    showRentyToast(`Không tìm thấy vị trí "${addressOrPlace}". Vui lòng thử từ khóa khác.`, 'warning');
                } else {
                    this.showCoordErrorToast(`Không tìm thấy vị trí "${addressOrPlace}". Vui lòng thử từ khóa khác.`);
                }
            });
    }

    async drawCommuteRoute(startCoords, destUni) {
        if (this.routePolyline) {
            this.map.removeLayer(this.routePolyline);
            this.routePolyline = null;
        }
        if (this.routeBadgeMarker) {
            this.map.removeLayer(this.routeBadgeMarker);
            this.routeBadgeMarker = null;
        }

        const destLat = destUni.lat;
        const destLng = destUni.lng;
        const destName = destUni.name || destUni.label || 'Điểm đến';
        const distKm = this.calcHaversineDistance(startCoords.lat, startCoords.lng, destLat, destLng);

        let bikeMins = Math.max(2, Math.round((distKm / 24) * 60 + 2));
        let walkMins = Math.max(3, Math.round((distKm / 4.5) * 60));
        let busMins = Math.max(10, Math.round((distKm / 16) * 60 + 8));
        let routeDistanceKm = distKm;
        let latLngs = null;

        // Fetch OSRM song song siêu tốc qua Promise.any (bám 100% theo mạng lưới đường sá thực tế)
        const routingEndpoints = [
            `https://router.project-osrm.org/route/v1/driving/${startCoords.lng},${startCoords.lat};${destLng},${destLat}?overview=full&geometries=geojson`,
            `https://routing.openstreetmap.de/routed-car/route/v1/driving/${startCoords.lng},${startCoords.lat};${destLng},${destLat}?overview=full&geometries=geojson`,
            `https://routing.openstreetmap.de/routed-bike/route/v1/driving/${startCoords.lng},${startCoords.lat};${destLng},${destLat}?overview=full&geometries=geojson`
        ];

        const fetchRoute = async (url) => {
            const controller = new AbortController();
            const timer = setTimeout(() => controller.abort(), 4000);
            try {
                const resp = await fetch(url, { signal: controller.signal });
                clearTimeout(timer);
                if (!resp.ok) throw new Error('Route status not ok');
                const data = await resp.json();
                if (!data.routes || !data.routes.length) throw new Error('No route');
                const r = data.routes[0];
                if (!r.geometry || !Array.isArray(r.geometry.coordinates) || r.geometry.coordinates.length < 2) {
                    throw new Error('Invalid coordinates');
                }
                return r;
            } catch (err) {
                clearTimeout(timer);
                throw err;
            }
        };

        try {
            const route = await Promise.any(routingEndpoints.map(url => fetchRoute(url)));
            if (route) {
                latLngs = route.geometry.coordinates.map(c => [c[1], c[0]]);
                routeDistanceKm = route.distance / 1000;
                bikeMins = Math.max(2, Math.round(route.duration / 60));
                walkMins = Math.max(3, Math.round((routeDistanceKm / 4.5) * 60));
                busMins = Math.max(8, Math.round((routeDistanceKm / 16) * 60 + 5));
            }
        } catch (err) {
            console.warn('[Routing] OSRM mirrors pending or unavailable:', err);
        }

        // Nếu tất cả mirror OSRM đều mất mạng hoàn toàn: fallback đường nối mượt mà
        if (!latLngs || latLngs.length < 2) {
            latLngs = [[startCoords.lat, startCoords.lng], [destLat, destLng]];
        }

        // Tuyến đường thực tế bám sát theo mặt đường: ĐƯỜNG LIỀN MÀU RÕ NÉT (KHÔNG NÉT ĐỨT)
        this.routePolyline = L.polyline(latLngs, {
            color: '#2563eb',
            weight: 6,
            opacity: 0.9,
            lineJoin: 'round',
            lineCap: 'round'
        }).addTo(this.map);

        const bounds = L.latLngBounds(latLngs);
        this.map.fitBounds(bounds, { padding: [60, 60], maxZoom: 16, animate: false });

        const cleanUniName = destUni.shortName || destName.split('(')[0].trim();

        // Cập nhật duy nhất Hộp hiển thị khoảng cách ở góc trái dưới bản đồ (không tạo badge che khuất trên bản đồ)
        this.updateCommuteBox({
            distance: routeDistanceKm.toFixed(1),
            bikeTime: bikeMins,
            walkTime: walkMins,
            busTime: busMins,
            destination: cleanUniName
        });
    }

    updateCommuteBox(data) {
        const box = document.getElementById('gm-commute-box');
        if (box) {
            if (!data) {
                box.classList.add('hidden');
            } else {
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
        }

        // Cập nhật hoặc tạo Floating Card nổi trực tiếp ở góc dưới bên trái bản đồ
        let floatCard = document.getElementById('gm-floating-commute-card');
        if (!data) {
            if (floatCard) floatCard.remove();
            return;
        }

        if (!floatCard) {
            const mapContainer = document.getElementById('renty-interactive-map');
            if (mapContainer && mapContainer.parentNode) {
                floatCard = document.createElement('div');
                floatCard.id = 'gm-floating-commute-card';
                floatCard.className = 'absolute bottom-6 left-6 z-[1000] p-3 rounded-2xl bg-slate-950/90 backdrop-blur-md border border-sky-500/40 shadow-2xl transition-all duration-300 pointer-events-auto flex items-center gap-3.5';
                mapContainer.parentNode.appendChild(floatCard);
            }
        }

        if (floatCard) {
            floatCard.innerHTML = `
                <div class="w-10 h-10 rounded-xl bg-sky-500/20 border border-sky-500/40 text-sky-400 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-route"></i>
                </div>
                <div class="flex flex-col">
                    <div class="text-[11px] text-slate-400 font-semibold flex items-center gap-1.5">
                        <span>Khoảng cách đến:</span>
                        <span class="text-sky-300 font-bold truncate max-w-[150px]">${data.destination}</span>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-sm font-black text-white">${data.distance} km</span>
                        <span class="text-xs text-slate-500">•</span>
                        <span class="text-xs font-bold text-emerald-400 flex items-center gap-1">
                            <i class="fa-solid fa-motorcycle text-[10px]"></i> ${data.bikeTime}p xe máy
                        </span>
                        <span class="text-xs text-slate-500">•</span>
                        <span class="text-xs font-semibold text-amber-300 flex items-center gap-1">
                            <i class="fa-solid fa-person-walking text-[10px]"></i> ${data.walkTime}p đi bộ
                        </span>
                    </div>
                </div>
                <button type="button" onclick="if(window.rentyGoogleMap && window.rentyGoogleMap.activeRoomId){const r=window.rentyRoomsData&&window.rentyRoomsData[window.rentyGoogleMap.activeRoomId];if(r){window.open('https://www.google.com/maps/dir/?api=1&destination='+r.latitude+','+r.longitude,'_blank');}}" class="ml-2 px-2.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-[11px] font-bold shadow-md transition-all flex items-center gap-1" title="Mở chỉ đường Google Maps">
                    <i class="fa-solid fa-diamond-turn-right"></i> Chỉ đường
                </button>
            `;
        }
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
       NEARBY PLACES (Siêu thị, Bệnh viện & Nhà thuốc, Xe buýt & Metro, Quán ăn, Cà phê, ATM, Giặt sấy, Gym)
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
        const lat = center.lat;
        const lng = center.lng;

        // Bộ tiện ích phong phú, thiết thực phục vụ nhu cầu sinh hoạt của người thuê trọ
        const mockPlaces = {
            supermarket: [
                { name: 'Cửa hàng tiện lợi Circle K (Mở 24/7)', dist: 'Cách ~180m', offset: [0.0018, 0.0015], icon: 'fa-shop', color: '#ef4444' },
                { name: 'Siêu thị WinMart+ (Thực phẩm tươi sống)', dist: 'Cách ~320m', offset: [-0.0022, 0.0028], icon: 'fa-cart-shopping', color: '#dc2626' },
                { name: 'Cửa hàng GS25 24h (Khu ăn uống)', dist: 'Cách ~450m', offset: [0.0031, -0.0021], icon: 'fa-store', color: '#2563eb' },
                { name: 'Bách Hóa Xanh (Rau củ & Nhu yếu phẩm)', dist: 'Cách ~580m', offset: [-0.0035, -0.0032], icon: 'fa-basket-shopping', color: '#16a34a' },
                { name: 'Siêu thị mini Co.op Food', dist: 'Cách ~720m', offset: [0.0042, 0.0038], icon: 'fa-cart-shopping', color: '#0284c7' },
                { name: 'Cửa hàng tiện lợi FamilyMart 24/7', dist: 'Cách ~850m', offset: [-0.0048, 0.0019], icon: 'fa-shop', color: '#059669' }
            ],
            hospital: [
                { name: 'Nhà thuốc FPT Long Châu (Đạt chuẩn GPP)', dist: 'Cách ~120m', offset: [0.0012, -0.0018], icon: 'fa-prescription-bottle-medical', color: '#0284c7' },
                { name: 'Nhà thuốc Pharmacity 24/7 (Đủ thuốc & Dược phẩm)', dist: 'Cách ~260m', offset: [-0.0021, -0.0024], icon: 'fa-tablets', color: '#059669' },
                { name: 'Trạm Y tế Phường (Khám chữa bệnh ban đầu)', dist: 'Cách ~480m', offset: [0.0035, 0.0026], icon: 'fa-kit-medical', color: '#10b981' },
                { name: 'Phòng khám Đa khoa Quốc tế', dist: 'Cách ~690m', offset: [-0.0038, 0.0042], icon: 'fa-hospital', color: '#0d9488' },
                { name: 'Bệnh viện Đa khoa Quận', dist: 'Cách ~1.1km', offset: [0.0058, -0.0045], icon: 'fa-square-h', color: '#e11d48' },
                { name: 'Nhà thuốc An Khang (Tư vấn tận tâm)', dist: 'Cách ~400m', offset: [0.0025, 0.0039], icon: 'fa-tablets', color: '#16a34a' }
            ],
            bus: [
                { name: 'Trạm dừng Xe buýt (Tuyến 08, 19, 53)', dist: 'Cách ~90m', offset: [0.0009, 0.0011], icon: 'fa-bus', color: '#3b82f6' },
                { name: 'Trạm dừng Xe buýt đối diện (Tuyến 06, 56, 150)', dist: 'Cách ~140m', offset: [-0.0013, 0.0008], icon: 'fa-bus-simple', color: '#2563eb' },
                { name: 'Ga Metro Đô thị (Tuyến Bến Thành - Suối Tiên)', dist: 'Cách ~550m', offset: [0.0038, -0.0031], icon: 'fa-train-subway', color: '#8b5cf6' },
                { name: 'Trạm trung chuyển Xe buýt Đô thị', dist: 'Cách ~780m', offset: [-0.0042, -0.0039], icon: 'fa-route', color: '#1d4ed8' },
                { name: 'Bến đón xe công nghệ Grab/Be/Gojek 24/7', dist: 'Cách ~210m', offset: [0.0022, -0.0019], icon: 'fa-motorcycle', color: '#059669' }
            ],
            food: [
                { name: 'Cơm tấm Sườn Bì Chả Sinh Viên (25k - 30k)', dist: 'Cách ~110m', offset: [-0.0011, 0.0016], icon: 'fa-utensils', color: '#f97316' },
                { name: 'Quán Phở bò & Bún bò Huế gia truyền', dist: 'Cách ~230m', offset: [0.0021, -0.0014], icon: 'fa-bowl-rice', color: '#ea580c' },
                { name: 'Cơm bình dân tự chọn (Ngon bổ rẻ)', dist: 'Cách ~340m', offset: [-0.0026, -0.0028], icon: 'fa-bowl-food', color: '#d97706' },
                { name: 'Tiệm Bánh mì & Xôi nóng mang đi', dist: 'Cách ~160m', offset: [0.0017, 0.0029], icon: 'fa-burger', color: '#eab308' },
                { name: 'Khu ẩm thực & Trà sữa ăn vặt sinh viên', dist: 'Cách ~490m', offset: [0.0036, 0.0024], icon: 'fa-fire-flame-curved', color: '#e11d48' },
                { name: 'Quán Hủ tiếu gõ & Mì xào đêm 24/7', dist: 'Cách ~380m', offset: [-0.0032, 0.0035], icon: 'fa-utensils', color: '#c2410c' }
            ],
            cafe: [
                { name: 'Highlands Coffee (Chỗ ngồi máy lạnh & Wifi)', dist: 'Cách ~220m', offset: [0.0019, -0.0022], icon: 'fa-mug-hot', color: '#b45309' },
                { name: 'The Coffee House (Không gian học tập & Deadline)', dist: 'Cách ~350m', offset: [-0.0027, 0.0021], icon: 'fa-coffee', color: '#92400e' },
                { name: 'Phúc Long Tea & Coffee (Trà đào & Trà sữa)', dist: 'Cách ~480m', offset: [0.0034, 0.0031], icon: 'fa-mug-saucer', color: '#15803d' },
                { name: 'Quán Cafe Cóc vỉa hè sinh viên (15k)', dist: 'Cách ~130m', offset: [-0.0014, -0.0016], icon: 'fa-mug-hot', color: '#78350f' },
                { name: 'Katinat Saigon Kafe (Check-in cực đẹp)', dist: 'Cách ~620m', offset: [0.0045, -0.0036], icon: 'fa-coffee', color: '#854d0e' },
                { name: 'Cà phê 24/7 học nhóm & Cắm sạc free', dist: 'Cách ~410m', offset: [-0.0036, -0.0029], icon: 'fa-laptop-code', color: '#3b82f6' }
            ],
            atm: [
                { name: 'Cây ATM Vietcombank 24/7 (Rút & Chuyển tiền)', dist: 'Cách ~150m', offset: [0.0015, 0.0018], icon: 'fa-credit-card', color: '#16a34a' },
                { name: 'Cây ATM MB Bank SmartBank (Nộp/Rút tiền tự động)', dist: 'Cách ~280m', offset: [-0.0023, -0.0021], icon: 'fa-money-bill-transfer', color: '#2563eb' },
                { name: 'Cây ATM Techcombank 24/7', dist: 'Cách ~420m', offset: [0.0032, -0.0028], icon: 'fa-building-columns', color: '#dc2626' },
                { name: 'Phòng giao dịch & ATM Agribank', dist: 'Cách ~560m', offset: [-0.0037, 0.0034], icon: 'fa-building-columns', color: '#991b1b' },
                { name: 'Cây ATM BIDV 24/7', dist: 'Cách ~690m', offset: [0.0048, 0.0022], icon: 'fa-credit-card', color: '#0d9488' }
            ],
            laundry: [
                { name: 'Tiệm Giặt Sấy Tự Động 24/7 Wash & Dry', dist: 'Cách ~170m', offset: [0.0016, -0.0015], icon: 'fa-soap', color: '#06b6d4' },
                { name: 'Giặt Sấy Kilo Sinh Viên (10k/kg - Giao tận nơi)', dist: 'Cách ~290m', offset: [-0.0024, 0.0026], icon: 'fa-shirt', color: '#0891b2' },
                { name: 'Tiệm Giặt Ủi Nhanh Hấp Khô Chuyên Nghiệp', dist: 'Cách ~470m', offset: [0.0036, -0.0033], icon: 'fa-jug-detergent', color: '#0e7490' },
                { name: 'Tự Giặt Sấy Thông Minh Smart Laundry', dist: 'Cách ~620m', offset: [-0.0041, -0.0028], icon: 'fa-soap', color: '#0284c7' }
            ],
            gym: [
                { name: 'The New Gym 24/7 (Trang thiết bị hiện đại)', dist: 'Cách ~310m', offset: [0.0026, 0.0024], icon: 'fa-dumbbell', color: '#dc2626' },
                { name: 'Phòng Tập Gym & Fitness Thể Thao Sinh Viên (200k/tháng)', dist: 'Cách ~440m', offset: [-0.0031, -0.0029], icon: 'fa-heart-pulse', color: '#ea580c' },
                { name: 'CLB Cầu Lông & Gym Thể Hình Đa Năng', dist: 'Cách ~650m', offset: [0.0043, -0.0038], icon: 'fa-person-running', color: '#16a34a' },
                { name: 'Phòng Yoga & Dance Fit', dist: 'Cách ~520m', offset: [-0.0039, 0.0037], icon: 'fa-child-reaching', color: '#9333ea' }
            ]
        };

        const list = mockPlaces[category] || [];
        list.forEach(place => {
            const pLat = lat + place.offset[0];
            const pLng = lng + place.offset[1];

            const icon = L.divIcon({
                html: `
                    <div style="background: ${place.color}; color: #ffffff; width: 30px; height: 30px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.4), 0 0 0 2px #ffffff; font-size: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">
                        <i class="fa-solid ${place.icon}"></i>
                    </div>
                `,
                className: 'gm-place-marker',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });

            const marker = L.marker([pLat, pLng], { icon: icon })
                .bindTooltip(`
                    <div style="font-size: 11px; line-height: 1.4;">
                        <b style="color: #0284c7;">${place.name}</b><br>
                        <span style="color: #10b981; font-weight: bold;">📍 ${place.dist}</span>
                    </div>
                `, { direction: 'top', className: 'gm-tooltip' })
                .bindPopup(`
                    <div style="padding: 6px; font-family: inherit;">
                        <div style="font-size: 12px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">${place.name}</div>
                        <div style="font-size: 11px; color: #059669; font-weight: 700; margin-bottom: 6px;">📍 ${place.dist} (Rất gần khu trọ)</div>
                        <div style="font-size: 10px; color: #64748b;">Tiện ích thiết yếu hỗ trợ đời sống sinh viên và người thuê trọ thuận tiện nhất.</div>
                    </div>
                `)
                .addTo(this.map);

            this.placesMarkers.push({ category, marker });
        });
    }

    /* =========================================================================
       GEOLOCATION (My Location - Chức năng định vị GPS & ERR_05_01)
       ========================================================================= */
    showGpsPermissionModal() {
        const modal = document.getElementById('gps-permission-modal');
        if (modal) {
            modal.classList.remove('hidden');
        } else if (typeof showRentyToast === 'function') {
            showRentyToast('Vui lòng cho phép truy cập vị trí trên trình duyệt để tìm phòng gần bạn nhất.', 'error', 'Mã lỗi: ERR_05_01');
        } else {
            alert('Vui lòng cho phép truy cập vị trí trên trình duyệt để tìm phòng gần bạn nhất.');
        }
    }

    locateUser() {
        if (!navigator.geolocation) {
            this.showGpsPermissionModal();
            return;
        }

        // Hiện banner hướng dẫn nổi bật chỉ lên góc trên thanh địa chỉ
        if (typeof window.showGpsBrowserGuide === 'function') {
            window.showGpsBrowserGuide();
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                // Ẩn banner hướng dẫn khi đã lấy được vị trí
                if (typeof window.hideGpsBrowserGuide === 'function') {
                    window.hideGpsBrowserGuide();
                }

                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;

                // DoD 1: Bản đồ di chuyển tâm về vị trí hiện tại và tải các Marker lân cận
                const myIcon = L.divIcon({
                    html: `
                        <div style="background: #3b82f6; width: 20px; height: 20px; border-radius: 9999px; border: 3px solid #ffffff; box-shadow: 0 0 16px rgba(59, 130, 246, 0.95); animation: pulse 1.5s infinite;"></div>
                    `,
                    className: 'gm-user-location',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });

                if (this.userLocMarker) {
                    this.map.removeLayer(this.userLocMarker);
                }
                if (this.uniMarker) {
                    this.map.removeLayer(this.uniMarker);
                    this.uniMarker = null;
                }
                this.selectedUniversity = null;
                this.selectedCampusId = 'all';

                // Hủy kích hoạt chip trường học khi người dùng chọn định vị GPS
                document.querySelectorAll('.gm-campus-chip').forEach(btn => {
                    btn.classList.remove('active', 'bg-blue-600', 'text-white');
                    btn.classList.add('bg-slate-900', 'text-slate-300');
                });
                const allBtn = document.getElementById('campus-chip-all');
                if (allBtn) {
                    allBtn.classList.add('active', 'bg-blue-600', 'text-white');
                    allBtn.classList.remove('bg-slate-900', 'text-slate-300');
                }

                this.userLocMarker = L.marker([lat, lng], { icon: myIcon })
                    .bindTooltip('📍 Vị trí hiện tại của bạn', { permanent: true, direction: 'top' })
                    .addTo(this.map);

                // Quét bán kính tìm phòng trọ lân cận vị trí GPS hiện tại
                this.scanRadiusAtLocation(lat, lng, 'Vị trí hiện tại của bạn');
            },
            (err) => {
                if (typeof window.hideGpsBrowserGuide === 'function') {
                    window.hideGpsBrowserGuide();
                }
                // [ERR_05_01]: Từ chối cấp quyền định vị GPS -> Popup thông báo hướng dẫn bật GPS
                console.warn('[ERR_05_01] Từ chối cấp quyền định vị GPS:', err);
                this.showGpsPermissionModal();
            },
            { enableHighAccuracy: true, timeout: 6000 }
        );
    }

    /* =========================================================================
       COORDINATE COMPUTATION (Ground Truth Coordinates cho 4 thành phố)
       ========================================================================= */
    computeBuildingCoordinates(building) {
        if (!building) return { lat: 21.0368, lng: 105.7832 };

        // 1. Kiểm tra nếu có sẵn latitude & longitude
        if (building.latitude && building.longitude && !isNaN(parseFloat(building.latitude)) && !isNaN(parseFloat(building.longitude))) {
            return { lat: parseFloat(building.latitude), lng: parseFloat(building.longitude) };
        }
        if (building.lat && building.lng && !isNaN(parseFloat(building.lat)) && !isNaN(parseFloat(building.lng))) {
            return { lat: parseFloat(building.lat), lng: parseFloat(building.lng) };
        }

        // 2. Tra cứu theo bảng tọa độ 62 Tòa nhà chuẩn của FullDemoSeeder
        const bId = parseInt(building.building_id || building.id || 0, 10);
        const BUILDING_COORDS = {
            // Hà Nội (1 - 15)
            1:  { lat: 21.0368, lng: 105.7832 }, // Cầu Giấy (Xuân Thủy)
            2:  { lat: 21.0345, lng: 105.7958 }, // Cầu Giấy (Chùa Hà)
            3:  { lat: 20.9982, lng: 105.8195 }, // Thanh Xuân (Vũ Tông Phan)
            4:  { lat: 20.9912, lng: 105.8115 }, // Thanh Xuân (Khương Đình)
            5:  { lat: 21.0152, lng: 105.8340 }, // Đống Đa (Xã Đàn)
            6:  { lat: 21.0078, lng: 105.8285 }, // Đống Đa (Chùa Bộc)
            7:  { lat: 21.0485, lng: 105.8080 }, // Tây Hồ (Lạc Long Quân)
            8:  { lat: 21.0450, lng: 105.8200 }, // Tây Hồ (Trích Sài)
            9:  { lat: 21.0045, lng: 105.8505 }, // Hai Bà Trưng (Bạch Mai)
            10: { lat: 21.0340, lng: 105.8150 }, // Ba Đình (Liễu Giai)
            11: { lat: 21.0185, lng: 105.7760 }, // Nam Từ Liêm (Đình Thôn)
            12: { lat: 21.0310, lng: 105.7680 }, // Nam Từ Liêm (Lê Đức Thọ)
            13: { lat: 20.9650, lng: 105.8280 }, // Hoàng Mai (Linh Đàm)
            14: { lat: 21.0650, lng: 105.7720 }, // Bắc Từ Liêm (Cổ Nhuế)
            15: { lat: 20.9810, lng: 105.7930 }, // Hà Đông (Chiến Thắng)

            // TP. Hồ Chí Minh (16 - 31)
            16: { lat: 10.7712, lng: 106.6925 }, // Quận 1 (Lê Lai)
            17: { lat: 10.7675, lng: 106.6885 }, // Quận 1 (Cống Quỳnh)
            18: { lat: 10.8015, lng: 106.7145 }, // Bình Thạnh (Điện Biên Phủ)
            19: { lat: 10.7950, lng: 106.7080 }, // Bình Thạnh (Xô Viết Nghệ Tĩnh)
            20: { lat: 10.8514, lng: 106.7578 }, // TP. Thủ Đức (Võ Văn Ngân)
            21: { lat: 10.8405, lng: 106.8415 }, // TP. Thủ Đức (Nguyễn Xiển)
            22: { lat: 10.7875, lng: 106.6890 }, // Quận 3 (Nam Kỳ Khởi Nghĩa)
            23: { lat: 10.7735, lng: 106.6610 }, // Quận 10 (Thành Thái)
            24: { lat: 10.7725, lng: 106.6578 }, // Quận 10 (Lý Thường Kiệt)
            25: { lat: 10.7290, lng: 106.7120 }, // Quận 7 (Phú Mỹ Hưng)
            26: { lat: 10.7410, lng: 106.7050 }, // Quận 7 (Nguyễn Thị Thập)
            27: { lat: 10.7960, lng: 106.6870 }, // Phú Nhuận (Phan Xích Long)
            28: { lat: 10.8140, lng: 106.6660 }, // Tân Bình (Bạch Đằng)
            29: { lat: 10.8030, lng: 106.6490 }, // Tân Bình (Hoàng Hoa Thám)
            30: { lat: 10.8350, lng: 106.6650 }, // Gò Vấp (Quang Trung)
            31: { lat: 10.7570, lng: 106.6780 }, // Quận 5 (Nguyễn Trãi)

            // Đà Nẵng (32 - 46)
            32: { lat: 16.0685, lng: 108.2245 }, // Hải Châu (Bạch Đằng)
            33: { lat: 16.0605, lng: 108.2160 }, // Hải Châu (Nguyễn Văn Linh)
            34: { lat: 16.0620, lng: 108.2465 }, // Sơn Trà (Võ Nguyên Giáp)
            35: { lat: 16.0690, lng: 108.2390 }, // Sơn Trà (Dương Đình Nghệ)
            36: { lat: 16.0020, lng: 108.2610 }, // Ngũ Hành Sơn (Trường Sa)
            37: { lat: 16.0490, lng: 108.2410 }, // Ngũ Hành Sơn (ĐH Kinh Tế)
            38: { lat: 16.0730, lng: 108.1960 }, // Thanh Khê (Nguyễn Tất Thành)
            39: { lat: 16.0610, lng: 108.1920 }, // Thanh Khê (Điện Biên Phủ)
            40: { lat: 16.0760, lng: 108.1520 }, // Liên Chiểu (Nguyễn Lương Bằng)
            41: { lat: 16.0630, lng: 108.1680 }, // Liên Chiểu (Tôn Đức Thắng)
            42: { lat: 16.0280, lng: 108.2050 }, // Cẩm Lệ (Cách Mạng Tháng 8)
            43: { lat: 16.1020, lng: 108.2650 }, // Sơn Trà (Hoàng Sa)
            44: { lat: 16.0520, lng: 108.2220 }, // Hải Châu (Cầu Rồng, 2 Tháng 9)
            45: { lat: 16.0080, lng: 108.1400 }, // Hòa Vang (QL14B)
            46: { lat: 16.0520, lng: 108.2450 }, // Ngũ Hành Sơn (Phố Tây An Thượng)

            // Đà Lạt (47 - 62)
            47: { lat: 11.9425, lng: 108.4380 }, // Phường 1 (Nguyễn Chí Thanh)
            48: { lat: 11.9410, lng: 108.4350 }, // Phường 1 (Lê Thị Hồng Gấm, Chợ Đêm)
            49: { lat: 11.9470, lng: 108.4410 }, // Phường 2 (Bùi Thị Xuân)
            50: { lat: 11.9485, lng: 108.4340 }, // Phường 2 (Phan Đình Phùng)
            51: { lat: 11.9250, lng: 108.4420 }, // Phường 3 (Đống Đa, Cáp Treo)
            52: { lat: 11.9270, lng: 108.4450 }, // Phường 3 (Đường 3 Tháng 4)
            53: { lat: 11.9410, lng: 108.4120 }, // Phường 4 (Vạn Hạnh, Làng Hoa)
            54: { lat: 11.9380, lng: 108.4210 }, // Phường 4 (Hoàng Văn Thụ, Cam Ly)
            55: { lat: 11.9560, lng: 108.4460 }, // Phường 8 (Phù Đổng Thiên Vương)
            56: { lat: 11.9720, lng: 108.4510 }, // Phường 8 (Mai Anh Đào, Tình Yêu)
            57: { lat: 11.9420, lng: 108.4550 }, // Phường 10 (Quang Trung, Ga Cổ)
            58: { lat: 11.9360, lng: 108.4450 }, // Phường 10 (Yersin, Quảng Trường)
            59: { lat: 11.9450, lng: 108.4980 }, // Phường 11 (Tự Phước, Trại Mát)
            60: { lat: 11.9480, lng: 108.4720 }, // Phường 9 (Hùng Vương, Thung Lũng Đèn)
            61: { lat: 11.9680, lng: 108.4850 }, // Phường 12 (Ngô Gia Tự, Thái Phiên)
            62: { lat: 11.8950, lng: 108.4320 }, // Phường 3 (Hồ Tuyền Lâm, Trúc Lâm)
        };

        if (bId > 0 && BUILDING_COORDS[bId]) {
            return BUILDING_COORDS[bId];
        }

        // 3. Phân tích địa chỉ theo từ khóa 4 thành phố
        const addr = ((building.address || '') + ' ' + (building.area_name || '') + ' ' + (building.name || '')).toLowerCase();

        // Đà Lạt
        if (addr.includes('đà lạt') || addr.includes('da lat') || addr.includes('lâm đồng') || addr.includes('tuyền lâm') || addr.includes('trại mát')) {
            if (addr.includes('tuyền lâm')) return { lat: 11.8950, lng: 108.4320 };
            if (addr.includes('trại mát') || addr.includes('tự phước')) return { lat: 11.9450, lng: 108.4980 };
            if (addr.includes('thái phiên') || addr.includes('phường 12')) return { lat: 11.9680, lng: 108.4850 };
            if (addr.includes('hùng vương') || addr.includes('phường 9')) return { lat: 11.9480, lng: 108.4720 };
            if (addr.includes('vạn thành') || addr.includes('phường 4')) return { lat: 11.9410, lng: 108.4120 };
            if (addr.includes('phù đổng thiên vương') || addr.includes('phường 8')) return { lat: 11.9560, lng: 108.4460 };
            if (addr.includes('yersin') || addr.includes('quang trung') || addr.includes('phường 10')) return { lat: 11.9360, lng: 108.4450 };
            if (addr.includes('phường 3') || addr.includes('đống đa')) return { lat: 11.9250, lng: 108.4420 };
            if (addr.includes('phường 2') || addr.includes('bùi thị xuân')) return { lat: 11.9470, lng: 108.4410 };
            return { lat: 11.9425, lng: 108.4380 }; // Trung tâm Đà Lạt
        }

        // Đà Nẵng
        if (addr.includes('đà nẵng') || addr.includes('da nang') || addr.includes('hải châu') || addr.includes('sơn trà') || addr.includes('ngũ hành sơn') || addr.includes('thanh khê') || addr.includes('liên chiểu') || addr.includes('cẩm lệ') || addr.includes('hòa vang')) {
            if (addr.includes('sơn trà') || addr.includes('võ nguyên giáp') || addr.includes('mỹ khê')) return { lat: 16.0620, lng: 108.2465 };
            if (addr.includes('ngũ hành sơn') || addr.includes('trường sa') || addr.includes('non nước')) return { lat: 16.0350, lng: 108.2480 };
            if (addr.includes('thanh khê') || addr.includes('nguyễn tất thành') || addr.includes('điện biên phủ')) return { lat: 16.0610, lng: 108.1920 };
            if (addr.includes('liên chiểu') || addr.includes('nguyễn lương bằng') || addr.includes('tôn đức thắng')) return { lat: 16.0760, lng: 108.1520 };
            if (addr.includes('cẩm lệ') || addr.includes('cách mạng tháng 8')) return { lat: 16.0280, lng: 108.2050 };
            if (addr.includes('hòa vang')) return { lat: 16.0080, lng: 108.1400 };
            return { lat: 16.0685, lng: 108.2245 }; // Hải Châu - Trung tâm Đà Nẵng
        }

        // TP. Hồ Chí Minh
        if (addr.includes('hồ chí minh') || addr.includes('hcm') || addr.includes('thủ đức') || addr.includes('bình thạnh') || addr.includes('quận 1') || addr.includes('quận 3') || addr.includes('quận 5') || addr.includes('quận 7') || addr.includes('quận 10') || addr.includes('tân bình') || addr.includes('gò vấp') || addr.includes('phú nhuận')) {
            if (addr.includes('võ văn ngân') || addr.includes('thủ đức')) return { lat: 10.8514, lng: 106.7578 };
            if (addr.includes('bình thạnh') || addr.includes('điện biên phủ')) return { lat: 10.8015, lng: 106.7145 };
            if (addr.includes('quận 1') || addr.includes('lê lai') || addr.includes('bến thành')) return { lat: 10.7712, lng: 106.6925 };
            if (addr.includes('quận 3') || addr.includes('nam kỳ khởi nghĩa')) return { lat: 10.7875, lng: 106.6890 };
            if (addr.includes('quận 10') || addr.includes('thành thái') || addr.includes('lý thường kiệt')) return { lat: 10.7735, lng: 106.6610 };
            if (addr.includes('quận 7') || addr.includes('phú mỹ hưng') || addr.includes('nguyễn thị thập')) return { lat: 10.7290, lng: 106.7120 };
            if (addr.includes('phú nhuận') || addr.includes('phan xích long')) return { lat: 10.7960, lng: 106.6870 };
            if (addr.includes('tân bình') || addr.includes('bạch đằng') || addr.includes('sân bay')) return { lat: 10.8140, lng: 106.6660 };
            if (addr.includes('gò vấp') || addr.includes('quang trung')) return { lat: 10.8350, lng: 106.6650 };
            if (addr.includes('quận 5') || addr.includes('nguyễn trãi')) return { lat: 10.7570, lng: 106.6780 };
            return { lat: 10.7800, lng: 106.6950 }; // Trung tâm HCM
        }

        // Hà Nội
        if (addr.includes('thanh xuân') || addr.includes('vũ tông phan') || addr.includes('khương đình')) return { lat: 20.9982, lng: 105.8195 };
        if (addr.includes('đống đa') || addr.includes('xã đàn') || addr.includes('chùa bộc') || addr.includes('chùa láng')) return { lat: 21.0152, lng: 105.8340 };
        if (addr.includes('tây hồ') || addr.includes('lạc long quân') || addr.includes('trích sài')) return { lat: 21.0485, lng: 105.8080 };
        if (addr.includes('hai bà trưng') || addr.includes('bạch mai') || addr.includes('đại cồ việt')) return { lat: 21.0045, lng: 105.8505 };
        if (addr.includes('ba đình') || addr.includes('liễu giai') || addr.includes('kim mã')) return { lat: 21.0340, lng: 105.8150 };
        if (addr.includes('nam từ liêm') || addr.includes('đình thôn') || addr.includes('mỹ đình')) return { lat: 21.0185, lng: 105.7760 };
        if (addr.includes('bắc từ liêm') || addr.includes('cổ nhuế')) return { lat: 21.0650, lng: 105.7720 };
        if (addr.includes('hoàng mai') || addr.includes('linh đàm')) return { lat: 20.9650, lng: 105.8280 };
        if (addr.includes('hà đông') || addr.includes('chiến thắng') || addr.includes('văn quán')) return { lat: 20.9810, lng: 105.7930 };

        return { lat: 21.0368, lng: 105.7832 }; // Cầu Giấy - Hà Nội
    }

    computeRoomCoordinates(room) {
        if (!room) return null;

        // 1. Check if database provides latitude and longitude
        if (room.latitude && room.longitude && !isNaN(parseFloat(room.latitude)) && !isNaN(parseFloat(room.longitude))) {
            return {
                lat: parseFloat(room.latitude),
                lng: parseFloat(room.longitude)
            };
        }
        if (room.lat && room.lng && !isNaN(parseFloat(room.lat)) && !isNaN(parseFloat(room.lng))) {
            return {
                lat: parseFloat(room.lat),
                lng: parseFloat(room.lng)
            };
        }

        // 2. Tọa độ chính xác theo Tòa nhà chứa phòng đó
        return this.computeBuildingCoordinates(room);
    }
}

// Global Handlers
window.GoogleMapsRenty = GoogleMapsRenty;

window.setRentyMapLayer = function(type) {
    if (window.rentyGoogleMap) {
        window.rentyGoogleMap.setMapType(type);
    }
    ['roadmap', 'satellite', 'terrain'].forEach(t => {
        const btn = document.getElementById('gm-layer-' + t);
        if (btn) {
            if (t === type) {
                btn.className = 'w-6 h-6 rounded-lg bg-blue-600 text-white font-bold transition-all flex items-center justify-center shadow-sm shrink-0';
            } else {
                btn.className = 'w-6 h-6 rounded-lg text-slate-400 hover:text-slate-200 transition-all flex items-center justify-center shrink-0';
            }
        }
    });
};

window.switchToFallbackMapTiles = function() {
    if (window.rentyGoogleMap && typeof window.rentyGoogleMap.switchToFallbackTiles === 'function') {
        window.rentyGoogleMap.switchToFallbackTiles();
    }
};

let _lastCampusFilterTime = 0;

window.selectCampusFilter = function(uniId, btnEl) {
    const now = Date.now();
    // Chống double-click hoặc gọi đúp do sự kiện nổi bọt trong vòng 350ms
    if (now - _lastCampusFilterTime < 350) {
        return;
    }
    _lastCampusFilterTime = now;

    if (!btnEl && uniId) {
        btnEl = document.querySelector(`.gm-campus-chip[onclick*="'${uniId}'"]`) || (uniId === 'all' ? document.getElementById('campus-chip-all') : null);
    }

    const mapInst = window.rentyGoogleMap;

    // Kiểm tra xem trường này có đang được chọn hay không:
    // Nếu bấm lại lần 2 vào chính trường đang quét -> HỦY QUÉT (chuyển về 'all')
    const isCurrentlyActive = (
        uniId && uniId !== 'all' && (
            (mapInst && mapInst.selectedCampusId === uniId) ||
            (btnEl && btnEl.classList.contains('active'))
        )
    );

    if (isCurrentlyActive) {
        // BẤM LẦN 2: HỦY QUÉT -> Bỏ active trường hiện tại, chuyển active về chip "Tất cả"
        document.querySelectorAll('.gm-campus-chip').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-sm');
            btn.classList.add('bg-slate-900', 'text-slate-300');
        });
        const allBtn = document.getElementById('campus-chip-all');
        if (allBtn) {
            allBtn.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-sm');
            allBtn.classList.remove('bg-slate-900', 'text-slate-300');
        }
        if (mapInst) {
            mapInst.selectCampus('all');
        }
        return;
    }

    // BẤM LẦN 1: KÍCH HOẠT QUÉT GẦN TRƯỜNG ĐÃ CHỌN
    document.querySelectorAll('.gm-campus-chip').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-sm');
        btn.classList.add('bg-slate-900', 'text-slate-300');
    });
    if (btnEl) {
        btnEl.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-sm');
        btnEl.classList.remove('bg-slate-900', 'text-slate-300');
    }
    if (mapInst) {
        mapInst.selectCampus(uniId);
    }
};

window.onUniversityDestinationChange = function(uniId) {
    document.querySelectorAll('.gm-campus-chip').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('bg-slate-900', 'text-slate-300');
    });
    if (!uniId) {
        const allBtn = document.getElementById('campus-chip-all');
        if (allBtn) {
            allBtn.classList.add('active', 'bg-blue-600', 'text-white');
            allBtn.classList.remove('bg-slate-900', 'text-slate-300');
        }
    }
    if (window.rentyGoogleMap) {
        window.rentyGoogleMap.selectCampus(uniId || 'all');
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

window.switchMapCity = function(city, btnEl) {
    document.querySelectorAll('.gm-city-tab').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-sm');
        btn.classList.add('bg-slate-900', 'text-slate-400');
    });
    if (btnEl) {
        btnEl.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-sm');
        btnEl.classList.remove('bg-slate-900', 'text-slate-400');
    }
    if (window.rentyGoogleMap) {
        window.rentyGoogleMap.switchCity(city);
    }
};

window.setMapRadius = function(radiusMeters, btnEl) {
    document.querySelectorAll('.gm-radius-chip').forEach(btn => {
        btn.classList.remove('active', 'bg-emerald-600', 'text-white');
        btn.classList.add('bg-slate-900', 'text-slate-400');
    });
    if (btnEl) {
        btnEl.classList.add('active', 'bg-emerald-600', 'text-white');
        btnEl.classList.remove('bg-slate-900', 'text-slate-400');
    }
    if (window.rentyGoogleMap) {
        window.rentyGoogleMap.setCommuteRadius(radiusMeters);
    }
};

window.onMapSearchSubmit = async function(e) {
    if (e) e.preventDefault();
    const input = document.getElementById('gm-search-input');
    if (input && window.rentyGoogleMap) {
        await window.rentyGoogleMap.searchGeocodeOrCampus(input.value);
    }
};

window.toggleGnHudDetails = function() {
    const el = document.getElementById('gm-hud-collapsible');
    const icon = document.getElementById('gm-hud-toggle-icon');
    const iconInline = document.getElementById('gm-hud-toggle-icon-inline');
    if (!el) return;
    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
        if (icon) icon.style.transform = 'rotate(180deg)';
        if (iconInline) iconInline.style.transform = 'rotate(180deg)';
        if (typeof initCampusBarDragScroll === 'function') {
            initCampusBarDragScroll();
        }
    } else {
        el.classList.add('hidden');
        if (icon) icon.style.transform = 'rotate(0deg)';
        if (iconInline) iconInline.style.transform = 'rotate(0deg)';
    }
};

window.toggleRentyMapFullscreen = function() {
    const isFull = document.body.classList.toggle('renty-map-fullscreen');
    const icon = document.getElementById('gm-fullscreen-icon');
    const btn = document.getElementById('gm-fullscreen-btn');
    if (icon) {
        if (isFull) {
            icon.className = 'fa-solid fa-compress text-amber-400';
            if (btn) {
                btn.title = 'Thu nhỏ (Esc)';
                btn.classList.add('border-amber-500/60', 'text-amber-400');
            }
        } else {
            icon.className = 'fa-solid fa-expand';
            if (btn) {
                btn.title = 'Toàn màn hình (Esc)';
                btn.classList.remove('border-amber-500/60', 'text-amber-400');
            }
        }
    }

    const mapObj = window.rentyGoogleMap && window.rentyGoogleMap.map;
    [60, 180, 350].forEach(delay => {
        setTimeout(() => {
            if (mapObj && typeof mapObj.invalidateSize === 'function') {
                mapObj.invalidateSize();
            }
        }, delay);
    });
};

// Thoát toàn màn hình khi nhấn Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && document.body.classList.contains('renty-map-fullscreen')) {
        const dropdown = document.getElementById('gm-search-autocomplete');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            return; // Đang đóng dropdown autocomplete thì ưu tiên cho dropdown
        }
        window.toggleRentyMapFullscreen();
    }
});

window.initRentyMap = function() {
    const container = document.getElementById('renty-interactive-map');
    if (!container) return;

    if (window.rentyGoogleMap && window.rentyGoogleMap.map) {
        const m = window.rentyGoogleMap;
        try {
            m.map.invalidateSize();
            if (Object.keys(m.markers || {}).length === 0 && window.rentyRoomsData && Object.keys(window.rentyRoomsData).length > 0) {
                m.renderRoomMarkers(window.rentyRoomsData);
            }
            if (m.initialCenter) {
                m.map.setView(m.initialCenter, m.map.getZoom() || 14, { animate: false });
            }
        } catch (e) {
            console.warn('Error invalidating existing map size:', e);
        }
        return;
    }

    if (typeof L === 'undefined') {
        console.warn('Leaflet mapping library not ready yet.');
        return;
    }

    try {
        window.rentyGoogleMap = new GoogleMapsRenty('renty-interactive-map', {
            onSelectRoom: (room) => {
                if (typeof window.highlightRoomCard === 'function') {
                    window.highlightRoomCard(room.id);
                } else {
                    const roomCard = document.querySelector(`.room-item-card[data-room-id="${room.id}"]`);
                    if (roomCard) {
                        roomCard.classList.remove('hidden');
                        roomCard.style.display = '';
                        roomCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        roomCard.classList.add('renty-card-highlight-active');
                        setTimeout(() => roomCard.classList.remove('renty-card-highlight-active'), 2800);
                    }
                }
            }
        });
        window.rentyMap = window.rentyGoogleMap.map;
        window.rentyMarkers = window.rentyGoogleMap.markers;

        // Kích hoạt đồng bộ di chuột xem trọ
        if (typeof window.initRoomCardMapSync === 'function') {
            window.initRoomCardMapSync();
        }
    } catch (err) {
        console.error('Failed to instantiate GoogleMapsRenty:', err);
    }
};

window.setViewMode = function(mode) {
    const mapBtn = document.getElementById('view-mode-map-btn');
    const gridBtn = document.getElementById('view-mode-grid-btn');
    if (mode === 'map') {
        document.body.classList.add('renty-map-mode');
        if (mapBtn) mapBtn.classList.add('active');
        if (gridBtn) gridBtn.classList.remove('active');
        localStorage.setItem('rentry_view_mode', 'map');

        window.initRentyMap();

        [60, 180, 350, 500, 800].forEach(delay => {
            setTimeout(() => {
                if (window.rentyGoogleMap && window.rentyGoogleMap.map) {
                    window.rentyGoogleMap.map.invalidateSize();
                    if (Object.keys(window.rentyGoogleMap.markers || {}).length === 0 && window.rentyRoomsData) {
                        window.rentyGoogleMap.renderRoomMarkers(window.rentyRoomsData);
                    }
                    if (delay >= 500 && window.rentyGoogleMap.initialCenter) {
                        window.rentyGoogleMap.map.setView(window.rentyGoogleMap.initialCenter, window.rentyGoogleMap.map.getZoom() || 14, { animate: false });
                    }
                }
            }, delay);
        });
    } else {
        document.body.classList.remove('renty-map-mode');
        if (mapBtn) mapBtn.classList.remove('active');
        if (gridBtn) gridBtn.classList.add('active');
        localStorage.setItem('rentry_view_mode', 'grid');
    }
};

/* =========================================================================
   LIVE MAP SEARCH ENGINE (AUTOCOMPLETE & GEOCODING SUGGESTIONS)
   ========================================================================= */
function removeVietnameseTones(str) {
    if (!str) return '';
    str = String(str).toLowerCase();
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a');
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e');
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, 'i');
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o');
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u');
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y');
    str = str.replace(/đ/g, 'd');
    str = str.replace(/\u0300|\u0301|\u0303|\u0309|\u0323/g, '');
    str = str.replace(/\u02C6|\u0306|\u031B/g, '');
    return str.trim();
}

window.clearMapSearchInput = function() {
    const input = document.getElementById('gm-search-input');
    const clearBtn = document.getElementById('gm-search-clear-btn');
    const dropdown = document.getElementById('gm-search-autocomplete');
    if (input) input.value = '';
    if (clearBtn) clearBtn.classList.add('hidden');
    if (dropdown) dropdown.classList.add('hidden');

    const emptyBanner = document.getElementById('map-empty-state-banner');
    if (emptyBanner) emptyBanner.remove();

    if (window.rentyGoogleMap) {
        if (window.rentyGoogleMap.customPlacePin) {
            window.rentyGoogleMap.map.removeLayer(window.rentyGoogleMap.customPlacePin);
            window.rentyGoogleMap.customPlacePin = null;
        }
        if (window.rentyGoogleMap.campusRadiusCircle) {
            window.rentyGoogleMap.map.removeLayer(window.rentyGoogleMap.campusRadiusCircle);
            window.rentyGoogleMap.campusRadiusCircle = null;
        }
        if (window.rentyGoogleMap.routePolyline) {
            window.rentyGoogleMap.map.removeLayer(window.rentyGoogleMap.routePolyline);
            window.rentyGoogleMap.routePolyline = null;
        }
        if (window.rentyGoogleMap.routeBadgeMarker) {
            window.rentyGoogleMap.map.removeLayer(window.rentyGoogleMap.routeBadgeMarker);
            window.rentyGoogleMap.routeBadgeMarker = null;
        }
        if (window.rentyGoogleMap.uniMarker) {
            window.rentyGoogleMap.map.removeLayer(window.rentyGoogleMap.uniMarker);
            window.rentyGoogleMap.uniMarker = null;
        }
        window.rentyGoogleMap.activeCenter = null;
        window.rentyGoogleMap.selectedUniversity = null;
        window.rentyGoogleMap.selectedCampusId = 'all';

        // Đưa chip trường đại học về Tất cả
        document.querySelectorAll('.gm-campus-chip').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white');
            btn.classList.add('bg-slate-900', 'text-slate-300');
        });
        const allBtn = document.getElementById('campus-chip-all');
        if (allBtn) {
            allBtn.classList.add('active', 'bg-blue-600', 'text-white');
            allBtn.classList.remove('bg-slate-900', 'text-slate-300');
        }
        if (typeof window.rentyGoogleMap.updateCommuteBox === 'function') {
            window.rentyGoogleMap.updateCommuteBox(null);
        }
    }

    // Xóa tất cả badge khoảng cách
    document.querySelectorAll('.campus-distance-badge').forEach(b => b.remove());

    // Khôi phục tất cả marker và card display
    document.querySelectorAll('.gm-price-marker').forEach(el => {
        el.style.display = 'inline-flex';
        el.style.opacity = '1';
        el.style.filter = 'none';
    });

    document.querySelectorAll('.room-item-card').forEach(card => {
        card.style.display = '';
    });

    // Reset chip trường về Tất cả
    document.querySelectorAll('.gm-campus-chip').forEach(c => c.classList.remove('active', 'bg-blue-600', 'text-white'));
    const allChip = document.getElementById('campus-chip-all');
    if (allChip) allChip.classList.add('active', 'bg-blue-600', 'text-white');

    if (window.selectCampusFilter) {
        window.selectCampusFilter('all');
    } else if (typeof window.filterItems === 'function') {
        window.filterItems({ resetPage: true });
    }
};

function initMapSearchEngine() {
    const input = document.getElementById('gm-search-input');
    const dropdown = document.getElementById('gm-search-autocomplete');
    const clearBtn = document.getElementById('gm-search-clear-btn');
    const wrapper = document.getElementById('gm-search-engine-wrapper');
    if (!input || !dropdown) return;

    let activeSuggestionIndex = -1;

    // Danh bạ Quận / Khu vực hành chính & Địa danh trọng điểm
    const districtHotspots = [
        // TP.HCM
        { name: 'Quận 1 (Trung tâm TP.HCM)', coords: [10.778, 106.699], city: 'hcm', keywords: 'quan 1 q1 trung tam ben thanh pho di bo nguyen hue ham nghi dong khoi nguyen du ly tu trong' },
        { name: 'Quận 3 (TP.HCM)', coords: [10.781, 106.684], city: 'hcm', keywords: 'quan 3 q3 ho con rua vo van tan nguyen dinh chieu le van sy nam ky khoi nghia ba huyen thanh quan' },
        { name: 'Quận 4 (TP.HCM)', coords: [10.760, 106.703], city: 'hcm', keywords: 'quan 4 q4 hoang dieu doan van bo ben van don ton dan nguyen tat thanh' },
        { name: 'Quận 5 (TP.HCM)', coords: [10.755, 106.666], city: 'hcm', keywords: 'quan 5 q5 cho lon an duong vuong tran phu nguyen trai hung vuong nguyen tri phuong' },
        { name: 'Quận 6 (TP.HCM)', coords: [10.748, 106.635], city: 'hcm', keywords: 'quan 6 q6 cho binh tay hau giang kinh duong vuong binh phu ba hom' },
        { name: 'Quận 7 (TP.HCM)', coords: [10.730, 106.695], city: 'hcm', keywords: 'quan 7 q7 phu my hung nguyen van linh ton duc thang nguyen thi thap huynh tan phat lam van ben' },
        { name: 'Quận 8 (TP.HCM)', coords: [10.725, 106.650], city: 'hcm', keywords: 'quan 8 q8 pham the hien ta quang buu duong ba trac da nam tung thien vuong' },
        { name: 'Quận 10 (TP.HCM)', coords: [10.772, 106.669], city: 'hcm', keywords: 'quan 10 q10 to hien thanh nguyen tri phuong bac hai thanh thai ly thuong kiet su van hanh' },
        { name: 'Quận 11 (TP.HCM)', coords: [10.766, 106.649], city: 'hcm', keywords: 'quan 11 q11 dam sen le dai hanh 3 thang 2 lac long quan binh thoi' },
        { name: 'Quận 12 (TP.HCM)', coords: [10.867, 106.641], city: 'hcm', keywords: 'quan 12 q12 to ky an phu dong nga tu ga nga tu an suong quang trung' },
        { name: 'Quận Bình Thạnh (TP.HCM)', coords: [10.803, 106.711], city: 'hcm', keywords: 'binh thanh hang xanh dien bien phu thi nghe bach dang xo viet nghe tinh dinh bo linh no trang long' },
        { name: 'TP. Thủ Đức (TP.HCM)', coords: [10.850, 106.772], city: 'hcm', keywords: 'thu duc linh chieu quan 9 quan 2 linh trung hiep phu tang nhon phu nga tu thu duc metro xa lo ha noi thao dien an phu' },
        { name: 'Quận Gò Vấp (TP.HCM)', coords: [10.838, 106.665], city: 'hcm', keywords: 'go vap quang trung nguyen van bao iuh phan van tri cho go vap le duc tho nguyen oanh thong nhat duong quang ham' },
        { name: 'Quận Tân Bình (TP.HCM)', coords: [10.798, 106.653], city: 'hcm', keywords: 'tan binh cong hoa hoang hoa tham truong chinh san bay tan son nhat le van sy pham van hai hoang van thu' },
        { name: 'Quận Tân Phú (TP.HCM)', coords: [10.790, 106.628], city: 'hcm', keywords: 'tan phu luy ban bich tay thanh hoa binh le trong tan aeon mall tan phu vuon lai doc lap thoai ngoc hau' },
        { name: 'Quận Phú Nhuận (TP.HCM)', coords: [10.799, 106.680], city: 'hcm', keywords: 'phu nhuan phan xich long nguyen van troi huynh van banh hoang van thu le van sy phan dinh phung' },
        { name: 'Quận Bình Tân (TP.HCM)', coords: [10.750, 106.608], city: 'hcm', keywords: 'binh tan ten lua kinh duong vuong le van quoi ma lo aeon binh tan quoc lo 1a huong lo 2' },
        { name: 'Huyện Nhà Bè (TP.HCM)', coords: [10.695, 106.730], city: 'hcm', keywords: 'nha be le van luong huynh tan phat nguyen huu tho phuoc kien hiep phuoc' },
        { name: 'Huyện Bình Chánh (TP.HCM)', coords: [10.690, 106.580], city: 'hcm', keywords: 'binh chanh quoc lo 50 nguyen van linh vinh loc tan kien an phu tay' },
        { name: 'Sân bay Tân Sơn Nhất (TP.HCM)', coords: [10.818, 106.652], city: 'hcm', keywords: 'san bay tan son nhat tsn truong son bach dang hong ha tan binh' },

        // Hà Nội
        { name: 'Quận Cầu Giấy (Hà Nội)', coords: [21.036, 105.790], city: 'hanoi', keywords: 'cau giay xuan thuy tran thai tong duy tan hoang quoc viet vnu uet trung hoa nhan chinh doan ke thien' },
        { name: 'Quận Đống Đa (Hà Nội)', coords: [21.018, 105.827], city: 'hanoi', keywords: 'dong da chua lang chua boc tay son ton duc thang xa dan ftu lang ha cat linh o cho dua' },
        { name: 'Quận Hai Bà Trưng (Hà Nội)', coords: [21.008, 105.852], city: 'hanoi', keywords: 'hai ba trung bach khoa dai co viet pho hue bach mai hust neu times city minh khai truong dinh' },
        { name: 'Quận Thanh Xuân (Hà Nội)', coords: [20.998, 105.811], city: 'hanoi', keywords: 'thanh xuan nguyen trai khuat duy tien nga tu so le van luong royal city ha dinh nguyen xuyen' },
        { name: 'Quận Ba Đình (Hà Nội)', coords: [21.034, 105.824], city: 'hanoi', keywords: 'ba dinh doi can kim ma lieu giai van cao giang vo lang bac ho tay hoang hoa tham' },
        { name: 'Quận Hoàn Kiếm (Hà Nội)', coords: [21.028, 105.854], city: 'hanoi', keywords: 'hoan kiem ho guom pho co trang tien phan chu trinh ly thuong kiet nha hat lon hang bai' },
        { name: 'Quận Tây Hồ (Hà Nội)', coords: [21.065, 105.820], city: 'hanoi', keywords: 'tay ho ho tay lac long quan to ngoc van yen phu xuan dieu vo chi cong trich sai' },
        { name: 'Quận Nam Từ Liêm (Hà Nội)', coords: [21.013, 105.765], city: 'hanoi', keywords: 'nam tu liem me tri my dinh le duc tho ham nghi keangnam the manor dai mo tay mo' },
        { name: 'Quận Bắc Từ Liêm (Hà Nội)', coords: [21.055, 105.755], city: 'hanoi', keywords: 'bac tu liem co nhue xuan dinh nhon phu dien kieu mai hoc vien canh sat mo dia chat' },
        { name: 'Quận Hà Đông (Hà Nội)', coords: [20.970, 105.770], city: 'hanoi', keywords: 'ha dong quang trung van quan ptit to hieu van phu mo lao le trong tan kien hung' },
        { name: 'Quận Hoàng Mai (Hà Nội)', coords: [20.975, 105.850], city: 'hanoi', keywords: 'hoang mai giai phong linh dam tam trinh tan mai dinh cong vinh tuy truong dinh' },
        { name: 'Quận Long Biên (Hà Nội)', coords: [21.040, 105.880], city: 'hanoi', keywords: 'long bien nguyen van cu ngoc lam sai dong aeon mall long bien bo de viet hung' },

        // Đà Nẵng
        { name: 'Quận Hải Châu (Đà Nẵng)', coords: [16.068, 108.220], city: 'danang', keywords: 'hai chau bach dang nguyen van linh song han cau rong 2 thang 9 trung tam' },
        { name: 'Quận Sơn Trà (Đà Nẵng)', coords: [16.065, 108.245], city: 'danang', keywords: 'son tra bien my khe vo nguyen giap duong dinh nghe ban dao son tra hoang sa' },
        { name: 'Quận Ngũ Hành Sơn (Đà Nẵng)', coords: [16.035, 108.248], city: 'danang', keywords: 'ngu hanh son truong sa non nuoc dai hoc kinh te pho tay an thuong' },
        { name: 'Quận Thanh Khê (Đà Nẵng)', coords: [16.063, 108.190], city: 'danang', keywords: 'thanh khe nguyen tat thanh dien bien phu vinh da nang' },
        { name: 'Quận Liên Chiểu (Đà Nẵng)', coords: [16.070, 108.155], city: 'danang', keywords: 'lien chieu nguyen luong bang ton duc thang bach khoa hoa khanh hoa minh' },
        { name: 'Quận Cẩm Lệ (Đà Nẵng)', coords: [16.025, 108.205], city: 'danang', keywords: 'cam le cach mang thang 8 khue trung ngoai ngu hoa tho' },

        // Đà Lạt
        { name: 'Phường 1 - Chợ Đêm & Hồ Xuân Hương (Đà Lạt)', coords: [11.942, 108.438], city: 'dalat', keywords: 'phuong 1 cho dem nguyen chi thanh le thi hong gam ho xuan huong trung tam' },
        { name: 'Phường 2 - Bùi Thị Xuân & Phan Đình Phùng (Đà Lạt)', coords: [11.947, 108.441], city: 'dalat', keywords: 'phuong 2 bui thi xuan phan dinh phung an uong' },
        { name: 'Phường 3 - Đồi Robin & Đèo Prenn (Đà Lạt)', coords: [11.926, 108.443], city: 'dalat', keywords: 'phuong 3 dong da ba thang tu cap treo robin ho tuyen lam thien vien truc lam' },
        { name: 'Phường 4 - Làng Hoa Vạn Thành & Thác Cam Ly (Đà Lạt)', coords: [11.939, 108.416], city: 'dalat', keywords: 'phuong 4 van hanh hoang van thu lang hoa van thanh cam ly' },
        { name: 'Phường 8 - ĐH Đà Lạt & Thung Lũng Tình Yêu', coords: [11.960, 108.448], city: 'dalat', keywords: 'phuong 8 phu dong thien vuong mai anh dao thung lung tinh yeu dai hoc da lat' },
        { name: 'Phường 10 - Ga Đà Lạt & Quảng Trường Lâm Viên', coords: [11.939, 108.450], city: 'dalat', keywords: 'phuong 10 quang trung yersin ga da lat quang truong lam vien nu hoa atiso' },
        { name: 'Phường 11 & 12 - Trại Mát & Làng Hoa Thái Phiên', coords: [11.955, 108.490], city: 'dalat', keywords: 'phuong 11 phuong 12 trai mat thai phien san may chua ve chai tu phuoc ngo gia tu' },
        { name: 'Khu Du Lịch Hồ Tuyền Lâm (Đà Lạt)', coords: [11.895, 108.432], city: 'dalat', keywords: 'ho tuyen lam thien vien truc lam resort du lich sinh thai' }
    ];

    function renderSuggestions(queryText) {
        const raw = (queryText || '').trim();
        const q = removeVietnameseTones(raw);
        const tokens = q.split(/\s+/).filter(Boolean);
        activeSuggestionIndex = -1;

        if (clearBtn) {
            if (raw.length > 0) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        // Khi chưa nhập từ khóa, tuyệt đối không xổ dropdown
        if (!raw || tokens.length === 0) {
            dropdown.classList.add('hidden');
            dropdown.innerHTML = '';
            return;
        }

        const mapInstance = window.rentyGoogleMap;
        const universities = mapInstance ? mapInstance.universities : [];
        const rooms = (window.rentyRoomsData && Object.keys(window.rentyRoomsData).length > 0) ? Object.values(window.rentyRoomsData) : [];

        // Hàm tính nhanh số phòng trọ bán kính quanh tọa độ
        const countNearbyRooms = (lat, lng, radiusKm = 5.0) => {
            if (!rooms.length || !mapInstance) return 0;
            return rooms.filter(r => {
                const rLat = parseFloat(r.lat || r.latitude);
                const rLng = parseFloat(r.lng || r.longitude);
                if (isNaN(rLat) || isNaN(rLng)) return false;
                return mapInstance.calcHaversineDistance(lat, lng, rLat, rLng) <= radiusKm;
            }).length;
        };

        // Hàm đếm số phòng có địa chỉ thuộc quận
        const countDistrictRooms = (districtName, keywords) => {
            if (!rooms.length) return 0;
            const normDist = removeVietnameseTones(districtName);
            const distKeywords = keywords.split(/\s+/);
            return rooms.filter(r => {
                const addr = removeVietnameseTones((r.address || '') + ' ' + (r.area_name || ''));
                return addr.includes(normDist) || distKeywords.some(k => k.length > 2 && addr.includes(k));
            }).length;
        };

        const isGeneralSchool = /^(truong|trg|truong hoc|tat ca truong|schools?)$/.test(q);
        const isCollegeOnly = /^(cao dang|cd|cđ|truong cao dang|trg cao dang|trg cd|cd hoc)$/.test(q);
        const isUniversityOnly = /^(dai hoc|dh|đh|truong dai hoc|trg dai hoc|trg dh)$/.test(q);

        // 1. Khớp Trường Đại Học & Cao Đẳng
        let matchedUnis = [];
        if (isGeneralSchool) {
            matchedUnis = universities.slice(0, 5);
        } else if (isCollegeOnly) {
            matchedUnis = universities.filter(u => u.type === 'college').slice(0, 5);
        } else if (isUniversityOnly) {
            matchedUnis = universities.filter(u => u.type !== 'college').slice(0, 5);
        } else if (tokens.length > 0) {
            let effectiveTokens = tokens;
            if (tokens.length > 1 && (tokens.includes('truong') || tokens.includes('trg'))) {
                effectiveTokens = tokens.filter(t => t !== 'truong' && t !== 'trg');
            }

            matchedUnis = universities.filter(u => {
                const searchPool = [
                    'truong',
                    'truong hoc',
                    u.type === 'college' ? 'cao dang cd cđ truong cao dang' : 'dai hoc dh đh truong dai hoc',
                    removeVietnameseTones(u.name || ''),
                    removeVietnameseTones(u.shortName || ''),
                    removeVietnameseTones(u.campus || ''),
                    removeVietnameseTones(u.district || ''),
                    removeVietnameseTones(u.address || ''),
                    removeVietnameseTones(u.fullName || ''),
                    removeVietnameseTones(u.id || ''),
                    ...(u.aliases || []).map(a => removeVietnameseTones(a))
                ];

                return effectiveTokens.every(tok => {
                    if (tok === 'cd' || tok === 'cđ') return u.type === 'college' || searchPool.some(f => f.includes('cao dang'));
                    if (tok === 'dh' || tok === 'đh') return u.type !== 'college' || searchPool.some(f => f.includes('dai hoc'));
                    return searchPool.some(field => field.includes(tok));
                });
            }).slice(0, 4);
        }

        // 2. Khớp Khu vực & Quận Huyện
        let matchedDistricts = [];
        if (tokens.length > 0 && !isGeneralSchool && !isCollegeOnly && !isUniversityOnly) {
            matchedDistricts = districtHotspots.filter(d => {
                const searchPool = [
                    removeVietnameseTones(d.name || ''),
                    removeVietnameseTones(d.keywords || '')
                ];
                return tokens.every(tok => searchPool.some(field => field.includes(tok)));
            }).slice(0, 3);
        }

        // 3. Khớp Tòa nhà & Phòng trọ
        let matchedRooms = [];
        if (tokens.length > 0 && !isGeneralSchool && !isCollegeOnly && !isUniversityOnly) {
            matchedRooms = rooms.filter(r => {
                const title = removeVietnameseTones(r.title || '');
                const addr = removeVietnameseTones(r.address || '');
                const rnum = removeVietnameseTones(String(r.room_number || ''));
                return tokens.every(tok => title.includes(tok) || addr.includes(tok) || rnum.includes(tok));
            }).slice(0, 3);
        }

        let html = '<div class="py-1">';

        // Danh sách phẳng (Flat List): Trường học
        matchedUnis.forEach((u) => {
            const isCollege = u.type === 'college';
            const badge = isCollege 
                ? '<span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300 border border-amber-400/30">Cao Đẳng</span>'
                : '<span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-sky-500/20 text-sky-300 border border-sky-400/30">Đại Học</span>';

            const nearbyCount = countNearbyRooms(u.lat, u.lng, 5.0);

            html += `
                <div class="gm-search-item px-3 py-2 hover:bg-sky-950/70 cursor-pointer transition-all flex items-center justify-between gap-3 group text-slate-100 border-b border-slate-800/40" data-type="uni" data-uni-id="${u.id}" data-name="${u.name}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-7 h-7 rounded-lg bg-sky-500/15 border border-sky-400/20 text-sky-300 flex items-center justify-center shrink-0 group-hover:bg-sky-500 group-hover:text-white transition-all text-xs shadow-sm">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </span>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-bold text-white text-[13px] group-hover:text-sky-300 truncate">${u.name}</span>
                                ${badge}
                            </div>
                            <div class="text-[11px] text-slate-400 group-hover:text-slate-300 truncate mt-0.5 flex items-center gap-1.5">
                                <span>📍 ${u.district || u.address || ''}</span>
                                ${nearbyCount > 0 ? `<span class="text-emerald-400 font-bold">• ${nearbyCount} phòng quanh đây</span>` : ''}
                            </div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-500 group-hover:text-sky-400 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            `;
        });

        // Danh sách phẳng: Khu vực & Quận Huyện
        matchedDistricts.forEach((d) => {
            const roomCount = countDistrictRooms(d.name, d.keywords);
            html += `
                <div class="gm-search-item px-3 py-2 hover:bg-emerald-950/70 cursor-pointer transition-all flex items-center justify-between gap-3 group text-slate-100 border-b border-slate-800/40" data-type="district" data-lat="${d.coords[0]}" data-lng="${d.coords[1]}" data-name="${d.name}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-7 h-7 rounded-lg bg-emerald-500/15 border border-emerald-400/20 text-emerald-300 flex items-center justify-center shrink-0 group-hover:bg-emerald-500 group-hover:text-white transition-all text-xs shadow-sm">
                            <i class="fa-solid fa-location-dot"></i>
                        </span>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-bold text-white text-[13px] group-hover:text-emerald-300 truncate">${d.name}</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">Khu vực</span>
                            </div>
                            <div class="text-[11px] text-slate-400 group-hover:text-slate-300 truncate mt-0.5">
                                ${roomCount > 0 ? `<span class="text-emerald-400 font-bold">• Có ${roomCount} phòng trọ tại đây</span>` : '<span>Định vị khu vực trên bản đồ</span>'}
                            </div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-500 group-hover:text-emerald-400 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            `;
        });

        // Danh sách phẳng: Phòng trọ & Tòa nhà
        matchedRooms.forEach((r) => {
            const priceText = (r.price ? (r.price / 1000000).toFixed(1).replace('.0', '') + ' Tr/th' : '');
            html += `
                <div class="gm-search-item px-3 py-2 hover:bg-amber-950/70 cursor-pointer transition-all flex items-center justify-between gap-3 group text-slate-100 border-b border-slate-800/40" data-type="room" data-room-id="${r.id}" data-name="${r.title || ('Phòng ' + r.room_number)}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-7 h-7 rounded-lg bg-amber-500/15 border border-amber-400/20 text-amber-300 flex items-center justify-center shrink-0 group-hover:bg-amber-500 group-hover:text-white transition-all text-xs shadow-sm">
                            <i class="fa-solid fa-door-open"></i>
                        </span>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-bold text-white text-[13px] group-hover:text-amber-300 truncate">${r.title || ('Phòng ' + r.room_number)}</span>
                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300 border border-amber-400/30">Phòng trọ</span>
                            </div>
                            <div class="text-[11px] text-slate-400 group-hover:text-slate-300 truncate mt-0.5">${r.address || ''}</div>
                        </div>
                    </div>
                    <span class="font-black text-amber-400 group-hover:text-white text-xs shrink-0 px-2 py-0.5 rounded bg-amber-950/80 border border-amber-500/30">${priceText}</span>
                </div>
            `;
        });

        // Dòng hành động: Geocoding tìm bất kỳ vị trí/địa chỉ nào trên bản đồ
        html += `
            <div class="gm-search-item px-3 py-2 hover:bg-sky-600 hover:text-white cursor-pointer transition-all flex items-center justify-between gap-3 group text-slate-200 mt-0.5 bg-slate-900/60" data-type="geocode" data-query="${raw}" data-name="${raw}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="w-7 h-7 rounded-lg bg-sky-500/20 border border-sky-400/30 text-sky-300 flex items-center justify-center shrink-0 group-hover:bg-white group-hover:text-sky-700 transition-all text-xs shadow-sm">
                        <i class="fa-solid fa-magnifying-glass-location"></i>
                    </span>
                    <div class="min-w-0">
                        <div class="font-bold text-[12px] group-hover:text-white truncate">Tìm kiếm vị trí "<b>${raw}</b>" trên bản đồ</div>
                        <div class="text-[10px] text-slate-400 group-hover:text-sky-100 truncate">Định vị đường phố hoặc địa chỉ bất kỳ</div>
                    </div>
                </div>
                <i class="fa-solid fa-arrow-turn-down text-[10px] text-slate-400 group-hover:text-white -rotate-90"></i>
            </div>
        </div>`;

        dropdown.innerHTML = html;
        dropdown.classList.remove('hidden');

        // Bắt sự kiện click từng gợi ý
        dropdown.querySelectorAll('.gm-search-item').forEach(item => {
            item.addEventListener('click', () => {
                selectSuggestionItem(item);
            });
        });
    }

    function selectSuggestionItem(itemEl) {
        if (!itemEl) return;
        const type = itemEl.getAttribute('data-type');
        const name = itemEl.getAttribute('data-name');
        if (name) input.value = name;
        dropdown.classList.add('hidden');
        if (clearBtn) clearBtn.classList.remove('hidden');

        const mapInstance = window.rentyGoogleMap;
        if (!mapInstance) return;

        if (type === 'uni') {
            const uniId = itemEl.getAttribute('data-uni-id');
            if (window.selectCampusFilter) {
                window.selectCampusFilter(uniId);
            } else {
                mapInstance.selectCampus(uniId);
            }
        } else if (type === 'tourist' || type === 'district' || type === 'live-place') {
            const lat = parseFloat(itemEl.getAttribute('data-lat'));
            const lng = parseFloat(itemEl.getAttribute('data-lng'));
            if (!isNaN(lat) && !isNaN(lng)) {
                // Ghim Marker địa điểm nổi bật
                if (mapInstance.customPlacePin) {
                    mapInstance.map.removeLayer(mapInstance.customPlacePin);
                }
                const pinIcon = L.divIcon({
                    className: 'renty-custom-place-marker',
                    html: `
                        <div style="background: linear-gradient(135deg, #0284c7, #0369a1); color: white; padding: 6px 12px; border-radius: 9999px; font-size: 11px; font-weight: 800; box-shadow: 0 10px 25px rgba(0,0,0,0.5); border: 2px solid white; white-space: nowrap; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-location-dot text-amber-300"></i> ${name}
                        </div>
                    `,
                    iconSize: [160, 32],
                    iconAnchor: [80, 16]
                });
                mapInstance.customPlacePin = L.marker([lat, lng], { icon: pinIcon }).addTo(mapInstance.map);

                // Quét bán kính quanh địa điểm này và lọc phòng trọ!
                mapInstance.scanRadiusAtLocation(lat, lng, name);
            }
        } else if (type === 'geocode') {
            const q = itemEl.getAttribute('data-query') || name;
            mapInstance.geocodeAndFly(q);
        } else if (type === 'room') {
            const roomId = itemEl.getAttribute('data-room-id');
            const roomData = window.rentyRoomsData ? window.rentyRoomsData[roomId] : null;
            if (roomData) {
                const coords = mapInstance.computeRoomCoordinates(roomData);
                if (coords) {
                    mapInstance.map.flyTo([coords.lat, coords.lng], 16, { duration: 1.0 });
                    setTimeout(() => {
                        if (mapInstance.markers && mapInstance.markers[roomId]) {
                            mapInstance.markers[roomId].openPopup();
                            mapInstance.highlightMarker(roomId);
                        }
                    }, 1050);
                }
            }
            if (typeof window.highlightRoomCard === 'function') {
                window.highlightRoomCard(roomId);
            } else {
                const card = document.querySelector(`.room-item-card[data-room-id="${roomId}"]`);
                if (card) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    card.classList.add('ring-2', 'ring-amber-400');
                    setTimeout(() => card.classList.remove('ring-2', 'ring-amber-400'), 2500);
                }
            }
        }
    }

    // Sự kiện gõ phím
    input.addEventListener('input', (e) => {
        if (clearBtn) clearBtn.classList.toggle('hidden', !e.target.value.trim());
        renderSuggestions(e.target.value);
    });

    // Khi người dùng click vào ô input: chỉ hiển thị gợi ý nếu đã có từ khóa
    input.addEventListener('focus', () => {
        if (input.value && input.value.trim().length > 0) {
            renderSuggestions(input.value);
        } else {
            dropdown.classList.add('hidden');
        }
    });

    // Bàn phím điều hướng mũi tên Lên / Xuống / Enter
    input.addEventListener('keydown', (e) => {
        const items = dropdown.querySelectorAll('.gm-search-item');
        if (!items.length || dropdown.classList.contains('hidden')) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeSuggestionIndex = (activeSuggestionIndex + 1) % items.length;
            updateItemSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeSuggestionIndex = (activeSuggestionIndex - 1 + items.length) % items.length;
            updateItemSelection(items);
        } else if (e.key === 'Enter') {
            if (activeSuggestionIndex >= 0 && items[activeSuggestionIndex]) {
                e.preventDefault();
                selectSuggestionItem(items[activeSuggestionIndex]);
            }
        } else if (e.key === 'Escape') {
            dropdown.classList.add('hidden');
        }
    });

    function updateItemSelection(items) {
        items.forEach((it, idx) => {
            if (idx === activeSuggestionIndex) {
                it.classList.add('bg-sky-500/25', 'ring-1', 'ring-sky-400');
                it.scrollIntoView({ block: 'nearest' });
            } else {
                it.classList.remove('bg-sky-500/25', 'ring-1', 'ring-sky-400');
            }
        });
    }

    // Đóng dropdown khi click ra ngoài
    document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

window.scrollCityBar = function(offset) {
    const bar = document.querySelector('.gm-city-switcher');
    if (bar) {
        bar.scrollBy({ left: offset, behavior: 'smooth' });
    }
};

window.scrollCampusBar = function(offset) {
    const bar = document.querySelector('.gm-campus-bar');
    if (bar) {
        bar.scrollBy({ left: offset, behavior: 'smooth' });
    }
};

window.scrollNearbyBar = function(offset) {
    const bar = document.querySelector('.gm-nearby-bar');
    if (bar) {
        bar.scrollBy({ left: offset, behavior: 'smooth' });
    }
};

function makeHorizontalScrollable(selector) {
    const slider = document.querySelector(selector);
    if (!slider) return;
    if (slider.dataset.dragScrollInit === 'true') return;
    slider.dataset.dragScrollInit = 'true';

    let isDown = false;
    let startX = 0;
    let scrollStartX = 0;
    let hasDragged = false;
    const DRAG_THRESHOLD = 6; // px

    slider.style.cursor = 'grab';

    slider.addEventListener('mousedown', (e) => {
        // Chỉ kéo rê bằng chuột trái
        if (e.button !== 0) return;
        isDown = true;
        hasDragged = false;
        startX = e.pageX;
        scrollStartX = slider.scrollLeft;
    });

    const endDrag = () => {
        if (!isDown) return;
        isDown = false;
        slider.style.cursor = 'grab';
        // Giữ cờ hasDragged ngắn hạn để chặn click nếu vừa kéo chuột
        setTimeout(() => {
            hasDragged = false;
        }, 80);
    };

    window.addEventListener('mouseup', endDrag);
    slider.addEventListener('mouseleave', () => {
        if (isDown) endDrag();
    });

    slider.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        const deltaX = e.pageX - startX;

        // Nếu di chuyển quá ngưỡng drag thì coi là kéo rê cuộn
        if (Math.abs(deltaX) > DRAG_THRESHOLD) {
            hasDragged = true;
            slider.style.cursor = 'grabbing';
            e.preventDefault();
            slider.scrollLeft = scrollStartX - deltaX;
        }
    });

    // Bắt chặn sự kiện click nếu vừa thực hiện thao tác kéo rê chuột
    slider.addEventListener('click', (e) => {
        if (hasDragged) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
        }
    }, true); // useCapture: true để chặn trước khi sự kiện tới button

    // Cuộn ngang tức thì bằng con lăn chuột (Mouse Wheel)
    slider.addEventListener('wheel', (e) => {
        if (e.deltaY !== 0) {
            e.preventDefault();
            slider.scrollLeft += e.deltaY;
        }
    }, { passive: false });
}

// Bắt sự kiện Click trực tiếp toàn cục cho tất cả Chip trường học (Đảm bảo 100% không bao giờ trượt click)
document.addEventListener('click', (e) => {
    const chip = e.target.closest('.gm-campus-chip');
    if (!chip) return;

    // Nếu chip đã có thuộc tính inline onclick, trình duyệt đã tự động xử lý -> không gọi lặp
    if (chip.hasAttribute('onclick')) return;

    const onclickStr = chip.getAttribute('onclick') || '';
    const match = onclickStr.match(/selectCampusFilter\(['"]([^'"]+)['"]/);
    const uniId = match ? match[1] : (chip.id === 'campus-chip-all' ? 'all' : chip.getAttribute('data-uni-id'));
    if (uniId) {
        window.selectCampusFilter(uniId, chip);
    }
});

function initCampusBarDragScroll() {
    makeHorizontalScrollable('.gm-city-switcher');
    makeHorizontalScrollable('.gm-campus-bar');
    makeHorizontalScrollable('.gm-nearby-bar');
}

// Đồng bộ tương tác 2 chiều: Di chuột tới đâu hiện trọ lên bản đồ tới đó
window.initRoomCardMapSync = function() {
    if (window._rentyRoomCardMapSyncInit) return;
    window._rentyRoomCardMapSyncInit = true;

    let hoverTimer = null;
    let lastHoveredId = null;

    document.body.addEventListener('mouseover', (e) => {
        const card = e.target.closest('.room-item-card');
        if (!card) return;
        const roomId = card.getAttribute('data-room-id');
        if (!roomId || roomId === lastHoveredId) return;

        if (hoverTimer) clearTimeout(hoverTimer);
        hoverTimer = setTimeout(() => {
            lastHoveredId = roomId;
            const mapInstance = window.rentyGoogleMap;
            if (!mapInstance || !mapInstance.markers) return;

            const marker = mapInstance.markers[roomId];
            if (marker && mapInstance.map) {
                // 1. Chỉ highlight nổi bật pin/marker trên bản đồ (nhẹ nhàng, không giật lag)
                mapInstance.highlightMarker(roomId);
            }
        }, 120);
    });
};

// Auto-observe map container resize & initialize search engine + campus drag scroll + room hover sync
document.addEventListener('DOMContentLoaded', () => {
    const mapEl = document.getElementById('renty-interactive-map');
    if (mapEl && window.ResizeObserver) {
        let resizeTimer = null;
        const resizeObserver = new ResizeObserver(() => {
            if (resizeTimer) clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (window.rentyGoogleMap && window.rentyGoogleMap.map) {
                    window.rentyGoogleMap.map.invalidateSize({ pan: false });
                }
            }, 100);
        });
        resizeObserver.observe(mapEl);
    }

    // Khởi tạo Live Search Engine cho bản đồ
    initMapSearchEngine();

    // Khởi tạo cuộn / vuốt chuột cho thanh trường học
    initCampusBarDragScroll();

    // Khởi tạo tính năng: Di tới đâu hiện trọ lên bản đồ tới đó
    window.initRoomCardMapSync();
});

/* =========================================================================
   RANGE VALIDATION & RADIUS CONTROLS (DoD 2 & STT 3 range_radius)
   ========================================================================= */
window.onRadiusSliderChange = function(val) {
    const numVal = parseFloat(val);
    const slider = document.getElementById('range_radius');
    const label = document.getElementById('range_radius_val');
    const errBox = document.getElementById('range_radius_error');
    const wrapper = document.getElementById('radius_slider_wrapper');

    // Kịch bản kiểm thử DoD 2: Kéo thanh bán kính về 0km -> Báo lỗi 'Bán kính tối thiểu phải từ 1km'
    if (isNaN(numVal) || numVal < 1.0) {
        if (label) label.textContent = `${ numVal } km`;
        if (errBox) {
            errBox.classList.remove('hidden');
        }
        if (slider) {
            slider.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
        }
        if (wrapper) {
            wrapper.classList.add('border-rose-500');
        }
        return false;
    }

    // Giá trị hợp lệ (>= 1km)
    if (errBox) {
        errBox.classList.add('hidden');
    }
    if (slider) {
        slider.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
    }
    if (wrapper) {
        wrapper.classList.remove('border-rose-500');
    }
    if (label) {
        label.textContent = `${ numVal } km`;
    }

    // Cập nhật bán kính bản đồ
    if (window.rentyGoogleMap) {
        window.rentyGoogleMap.setCommuteRadius(numVal * 1000);
    }
    return true;
};

window.applyPresetRadius = function(km) {
    const slider = document.getElementById('range_radius');
    if (slider) slider.value = km;
    window.onRadiusSliderChange(km);
};

window.locateUserRentyMap = function() {
    if (window.rentyGoogleMap && typeof window.rentyGoogleMap.locateUser === 'function') {
        window.rentyGoogleMap.locateUser();
    } else {
        const modal = document.getElementById('gps-permission-modal');
        if (modal) modal.classList.remove('hidden');
    }
};
