/**
 * Room3DTour - Interactive 3D Boarding House & Studio Apartment Viewer
 * Built with Three.js (Inspired by n0neye/A3D Architecture)
 * 
 * Features:
 * - Procedural architectural modeling with realistic scale (7m x 5m x 3.2m)
 * - Complete modern amenities: Bed, Desk, Wardrobe, Air Conditioner, Kitchenette, Bathroom, Balcony
 * - Particle breeze animation for Air Conditioner
 * - Dynamic Day / Night lighting system with realistic shadows
 * - Cutaway / Transparent walls mode for floorplan observation
 * - Interactive Hotspot pins with smooth camera focus and info cards
 * - Interior customizer (Wall paint and Bedding fabric colors)
 * - Camera presets: Isometric, Floorplan, First-person, Bed, Desk, Kitchen, Bathroom, Balcony
 */

class Room3DTour {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error(`Room3DTour: Container #${containerId} not found!`);
            return;
        }

        this.options = Object.assign({
            isNight: false,
            showCutaway: false,
            onSelectHotspot: null,
        }, options);

        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.controls = null;
        this.raycaster = new THREE.Raycaster();
        this.mouse = new THREE.Vector2();

        // Object references for manipulation
        this.walls = [];
        this.hotspots = [];
        this.acParticles = null;
        this.lights = {
            ambient: null,
            sun: null,
            ceilingLights: [],
            deskLight: null,
            bedsideLight: null,
            kitchenLight: null
        };
        this.materials = {};

        // Animation / Tween state
        this.isAnimatingCamera = false;
        this.cameraTargetPos = new THREE.Vector3();
        this.controlsTargetPos = new THREE.Vector3();
        this.cameraLerpSpeed = 0.05;

        this.init();
    }

    init() {
        const width = this.container.clientWidth || window.innerWidth;
        const height = this.container.clientHeight || window.innerHeight;

        // 1. Scene
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(this.options.isNight ? 0x070b14 : 0x0f172a);
        this.scene.fog = new THREE.FogExp2(this.options.isNight ? 0x070b14 : 0x0f172a, 0.025);

        // 2. Camera
        this.camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 100);
        this.camera.position.set(7.5, 6.5, 9.0);

        // 3. Renderer
        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'high-performance' });
        this.renderer.setSize(width, height);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.shadowMap.enabled = true;
        this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
        this.renderer.toneMappingExposure = 1.1;
        this.container.appendChild(this.renderer.domElement);

        // 4. Controls
        if (typeof THREE.OrbitControls !== 'undefined') {
            this.controls = new THREE.OrbitControls(this.camera, this.renderer.domElement);
            this.controls.enableDamping = true;
            this.controls.dampingFactor = 0.08;
            this.controls.maxPolarAngle = Math.PI / 2 - 0.03; // Don't go below floor
            this.controls.minDistance = 2.0;
            this.controls.maxDistance = 22.0;
            this.controls.target.set(0, 1.2, 0);
        }

        // 5. Generate Procedural Textures & Materials
        this.buildMaterials();

        // 6. Build Lights
        this.buildLighting();

        // 7. Build Architectural Shell (Floor, Walls, Ceiling, Balcony, Bathroom)
        this.buildArchitecture();

        // 8. Build Complete Amenities & Furniture
        this.buildFurniture();

        // 9. Build Interactive Hotspots
        this.buildHotspots();

        // 10. AC Particle Flow
        this.buildAcParticles();

        // 11. Event Listeners
        window.addEventListener('resize', this.onWindowResize.bind(this));
        this.renderer.domElement.addEventListener('pointerdown', this.onPointerDown.bind(this));
        this.renderer.domElement.addEventListener('pointermove', this.onPointerMove.bind(this));

        // 12. Start Loop
        this.clock = new THREE.Clock();
        this.animate();
    }

    /* =========================================================================
       TEXTURES & MATERIALS
       ========================================================================= */
    buildMaterials() {
        // Procedural Wood Plank Texture (Canvas)
        const woodCanvas = document.createElement('canvas');
        woodCanvas.width = 512;
        woodCanvas.height = 512;
        const ctx = woodCanvas.getContext('2d');
        ctx.fillStyle = '#c59b6d';
        ctx.fillRect(0, 0, 512, 512);
        
        // Draw wood planks
        for (let y = 0; y < 512; y += 64) {
            ctx.fillStyle = '#b78b5e';
            ctx.fillRect(0, y, 512, 2);
            for (let x = (y % 128 === 0 ? 0 : 128); x < 512; x += 256) {
                ctx.fillStyle = '#a6794d';
                ctx.fillRect(x, y, 2, 64);
            }
        }
        // Grain noise
        for (let i = 0; i < 4000; i++) {
            const rx = Math.random() * 512;
            const ry = Math.random() * 512;
            ctx.fillStyle = Math.random() > 0.5 ? 'rgba(80, 50, 20, 0.05)' : 'rgba(255, 230, 180, 0.04)';
            ctx.fillRect(rx, ry, Math.random() * 40, 1);
        }

        const woodTexture = new THREE.CanvasTexture(woodCanvas);
        woodTexture.wrapS = THREE.RepeatWrapping;
        woodTexture.wrapT = THREE.RepeatWrapping;
        woodTexture.repeat.set(4, 3);

        // Tile Texture for Bathroom / Balcony
        const tileCanvas = document.createElement('canvas');
        tileCanvas.width = 256;
        tileCanvas.height = 256;
        const tctx = tileCanvas.getContext('2d');
        tctx.fillStyle = '#e2e8f0';
        tctx.fillRect(0, 0, 256, 256);
        tctx.strokeStyle = '#94a3b8';
        tctx.lineWidth = 3;
        for (let i = 0; i <= 256; i += 64) {
            tctx.beginPath();
            tctx.moveTo(i, 0); tctx.lineTo(i, 256);
            tctx.moveTo(0, i); tctx.lineTo(256, i);
            tctx.stroke();
        }
        const tileTexture = new THREE.CanvasTexture(tileCanvas);
        tileTexture.wrapS = THREE.RepeatWrapping;
        tileTexture.wrapT = THREE.RepeatWrapping;
        tileTexture.repeat.set(3, 3);

        this.materials.floor = new THREE.MeshStandardMaterial({
            map: woodTexture,
            roughness: 0.35,
            metalness: 0.05
        });

        this.materials.tileFloor = new THREE.MeshStandardMaterial({
            map: tileTexture,
            roughness: 0.25,
            metalness: 0.1
        });

        this.materials.wall = new THREE.MeshStandardMaterial({
            color: 0xf1f5f9,
            roughness: 0.85,
            metalness: 0.02
        });

        this.materials.accentWall = new THREE.MeshStandardMaterial({
            color: 0x2563eb, // Default vibrant navy/slate accent
            roughness: 0.6,
            metalness: 0.05
        });

        this.materials.ceiling = new THREE.MeshStandardMaterial({
            color: 0xf8fafc,
            roughness: 0.9
        });

        this.materials.glass = new THREE.MeshPhysicalMaterial({
            color: 0xffffff,
            transparent: true,
            opacity: 0.25,
            roughness: 0.1,
            transmission: 0.9,
            thickness: 0.5,
            ior: 1.5
        });

        this.materials.bedding = new THREE.MeshStandardMaterial({
            color: 0x3b82f6, // Navy blue modern
            roughness: 0.8,
            metalness: 0.05
        });

        this.materials.mattress = new THREE.MeshStandardMaterial({
            color: 0xffffff,
            roughness: 0.9
        });

        this.materials.darkWood = new THREE.MeshStandardMaterial({
            color: 0x271f1b,
            roughness: 0.5,
            metalness: 0.1
        });

        this.materials.lightWood = new THREE.MeshStandardMaterial({
            color: 0xd4a373,
            roughness: 0.6,
            metalness: 0.05
        });

        this.materials.whiteGloss = new THREE.MeshStandardMaterial({
            color: 0xffffff,
            roughness: 0.2,
            metalness: 0.05
        });

        this.materials.chrome = new THREE.MeshStandardMaterial({
            color: 0xe2e8f0,
            roughness: 0.1,
            metalness: 0.9
        });

        this.materials.blackMetal = new THREE.MeshStandardMaterial({
            color: 0x1e293b,
            roughness: 0.4,
            metalness: 0.8
        });

        this.materials.screenGlow = new THREE.MeshBasicMaterial({
            color: 0x38bdf8
        });

        this.materials.foliage = new THREE.MeshStandardMaterial({
            color: 0x16a34a,
            roughness: 0.7
        });
    }

    /* =========================================================================
       LIGHTING (DAY / NIGHT)
       ========================================================================= */
    buildLighting() {
        // Ambient Light
        this.lights.ambient = new THREE.AmbientLight(0xffffff, this.options.isNight ? 0.35 : 0.75);
        this.scene.add(this.lights.ambient);

        // Sunlight / Moon Directional Light
        this.lights.sun = new THREE.DirectionalLight(this.options.isNight ? 0x93c5fd : 0xfffbeb, this.options.isNight ? 0.4 : 1.4);
        this.lights.sun.position.set(-8, 9, -5);
        this.lights.sun.castShadow = true;
        this.lights.sun.shadow.mapSize.width = 2048;
        this.lights.sun.shadow.mapSize.height = 2048;
        this.lights.sun.shadow.camera.near = 0.5;
        this.lights.sun.shadow.camera.far = 25;
        this.lights.sun.shadow.camera.left = -6;
        this.lights.sun.shadow.camera.right = 6;
        this.lights.sun.shadow.camera.top = 6;
        this.lights.sun.shadow.camera.bottom = -6;
        this.lights.sun.shadow.bias = -0.0005;
        this.scene.add(this.lights.sun);

        // Interior Downlights (Ceiling Potlights)
        const ceilingPositions = [
            [-1.5, 3.1, -0.5],
            [1.5, 3.1, -0.5],
            [-1.5, 3.1, 1.5],
            [1.5, 3.1, 1.5]
        ];

        ceilingPositions.forEach(pos => {
            const spot = new THREE.PointLight(0xffedd5, this.options.isNight ? 0.9 : 0.3, 7, 1.5);
            spot.position.set(pos[0], pos[1], pos[2]);
            this.scene.add(spot);
            this.lights.ceilingLights.push(spot);

            // Fixture ring
            const fixtureGeo = new THREE.CylinderGeometry(0.08, 0.08, 0.03, 16);
            const fixtureMat = new THREE.MeshBasicMaterial({ color: 0xffffff });
            const fixture = new THREE.Mesh(fixtureGeo, fixtureMat);
            fixture.position.set(pos[0], 3.19, pos[2]);
            this.scene.add(fixture);
        });

        // Desk Lamp Light
        this.lights.deskLight = new THREE.PointLight(0xfef08a, this.options.isNight ? 0.9 : 0.2, 3, 2);
        this.lights.deskLight.position.set(2.4, 1.6, -1.8);
        this.scene.add(this.lights.deskLight);

        // Bedside Lamp Light
        this.lights.bedsideLight = new THREE.PointLight(0xfde68a, this.options.isNight ? 0.8 : 0.1, 3, 2);
        this.lights.bedsideLight.position.set(-2.8, 1.1, -1.8);
        this.scene.add(this.lights.bedsideLight);

        // Kitchen under-cabinet LED strip
        this.lights.kitchenLight = new THREE.PointLight(0xe0f2fe, this.options.isNight ? 0.7 : 0.2, 3, 2);
        this.lights.kitchenLight.position.set(2.4, 1.5, 1.2);
        this.scene.add(this.lights.kitchenLight);
    }

    setDayNight(isNight) {
        this.options.isNight = isNight;
        this.scene.background.set(isNight ? 0x070b14 : 0x0f172a);
        this.scene.fog.color.set(isNight ? 0x070b14 : 0x0f172a);

        this.lights.ambient.intensity = isNight ? 0.3 : 0.75;
        this.lights.sun.intensity = isNight ? 0.3 : 1.4;
        this.lights.sun.color.set(isNight ? 0x93c5fd : 0xfffbeb);

        this.lights.ceilingLights.forEach(l => l.intensity = isNight ? 1.0 : 0.35);
        this.lights.deskLight.intensity = isNight ? 1.0 : 0.2;
        this.lights.bedsideLight.intensity = isNight ? 0.9 : 0.1;
        this.lights.kitchenLight.intensity = isNight ? 0.8 : 0.2;
    }

    /* =========================================================================
       ARCHITECTURE (Floor, Walls, Ceiling, Doors, Balcony, WC Partition)
       ========================================================================= */
    buildArchitecture() {
        const roomGroup = new THREE.Group();

        // 1. Main Room Floor (6m x 4.8m)
        const floorGeo = new THREE.BoxGeometry(6.0, 0.2, 4.8);
        const floor = new THREE.Mesh(floorGeo, this.materials.floor);
        floor.position.set(0, -0.1, 0);
        floor.receiveShadow = true;
        roomGroup.add(floor);

        // 2. Ceiling
        const ceilingGeo = new THREE.BoxGeometry(6.0, 0.15, 4.8);
        this.ceilingMesh = new THREE.Mesh(ceilingGeo, this.materials.ceiling);
        this.ceilingMesh.position.set(0, 3.25, 0);
        roomGroup.add(this.ceilingMesh);

        // 3. Back Wall (Bed Accent Wall) - North Wall (Z = -2.4)
        const backWallGeo = new THREE.BoxGeometry(6.0, 3.2, 0.2);
        const backWall = new THREE.Mesh(backWallGeo, this.materials.accentWall);
        backWall.position.set(0, 1.6, -2.4);
        backWall.receiveShadow = true;
        roomGroup.add(backWall);
        this.accentWallMesh = backWall;
        this.walls.push(backWall);

        // Feature Slats behind the bed on the accent wall
        const slatCount = 14;
        const slatMat = this.materials.lightWood;
        for (let i = 0; i < slatCount; i++) {
            const slatGeo = new THREE.BoxGeometry(0.06, 3.0, 0.04);
            const slat = new THREE.Mesh(slatGeo, slatMat);
            slat.position.set(-2.5 + i * 0.14, 1.6, -2.28);
            slat.castShadow = true;
            roomGroup.add(slat);
        }

        // 4. Left Wall (Balcony & Window) - West Wall (X = -3.0)
        // Two columns and window frame opening to balcony
        const leftWallTopGeo = new THREE.BoxGeometry(0.2, 0.8, 4.8);
        const leftWallTop = new THREE.Mesh(leftWallTopGeo, this.materials.wall);
        leftWallTop.position.set(-3.0, 2.8, 0);
        roomGroup.add(leftWallTop);
        this.walls.push(leftWallTop);

        const leftWallSide1 = new THREE.Mesh(new THREE.BoxGeometry(0.2, 2.4, 1.2), this.materials.wall);
        leftWallSide1.position.set(-3.0, 1.2, -1.8);
        roomGroup.add(leftWallSide1);
        this.walls.push(leftWallSide1);

        const leftWallSide2 = new THREE.Mesh(new THREE.BoxGeometry(0.2, 2.4, 1.0), this.materials.wall);
        leftWallSide2.position.set(-3.0, 1.2, 1.9);
        roomGroup.add(leftWallSide2);
        this.walls.push(leftWallSide2);

        // Sliding Glass Door to Balcony
        const glassDoorGeo = new THREE.BoxGeometry(0.04, 2.4, 2.5);
        const glassDoor = new THREE.Mesh(glassDoorGeo, this.materials.glass);
        glassDoor.position.set(-3.0, 1.2, 0.15);
        roomGroup.add(glassDoor);

        // Glass door frame
        const frameGeo = new THREE.BoxGeometry(0.08, 2.42, 0.08);
        const frame1 = new THREE.Mesh(frameGeo, this.materials.blackMetal);
        frame1.position.set(-3.0, 1.2, -1.1);
        const frame2 = new THREE.Mesh(frameGeo, this.materials.blackMetal);
        frame2.position.set(-3.0, 1.2, 1.4);
        const frameCenter = new THREE.Mesh(frameGeo, this.materials.blackMetal);
        frameCenter.position.set(-3.0, 1.2, 0.15);
        roomGroup.add(frame1, frame2, frameCenter);

        // 5. Right Wall - East Wall (X = 3.0)
        const rightWallGeo = new THREE.BoxGeometry(0.2, 3.2, 4.8);
        const rightWall = new THREE.Mesh(rightWallGeo, this.materials.wall);
        rightWall.position.set(3.0, 1.6, 0);
        rightWall.receiveShadow = true;
        roomGroup.add(rightWall);
        this.walls.push(rightWall);

        // 6. Front Wall (Entrance door & Bathroom) - South Wall (Z = 2.4)
        const frontWallLeft = new THREE.Mesh(new THREE.BoxGeometry(2.0, 3.2, 0.2), this.materials.wall);
        frontWallLeft.position.set(-2.0, 1.6, 2.4);
        roomGroup.add(frontWallLeft);
        this.walls.push(frontWallLeft);

        const frontWallRight = new THREE.Mesh(new THREE.BoxGeometry(2.6, 3.2, 0.2), this.materials.wall);
        frontWallRight.position.set(1.7, 1.6, 2.4);
        roomGroup.add(frontWallRight);
        this.walls.push(frontWallRight);

        const frontWallTop = new THREE.Mesh(new THREE.BoxGeometry(1.4, 0.9, 0.2), this.materials.wall);
        frontWallTop.position.set(-0.3, 2.75, 2.4);
        roomGroup.add(frontWallTop);
        this.walls.push(frontWallTop);

        // Entrance Main Door with SmartLock
        const mainDoorGeo = new THREE.BoxGeometry(1.2, 2.3, 0.08);
        const mainDoor = new THREE.Mesh(mainDoorGeo, this.materials.darkWood);
        mainDoor.position.set(-0.3, 1.15, 2.38);
        mainDoor.castShadow = true;
        roomGroup.add(mainDoor);

        // Digital SmartLock on door
        const smartlockGeo = new THREE.BoxGeometry(0.08, 0.28, 0.03);
        const smartlock = new THREE.Mesh(smartlockGeo, this.materials.blackMetal);
        smartlock.position.set(0.18, 1.1, 2.33);
        const lockHandle = new THREE.Mesh(new THREE.BoxGeometry(0.16, 0.04, 0.05), this.materials.chrome);
        lockHandle.position.set(0.14, 1.05, 2.31);
        roomGroup.add(smartlock, lockHandle);

        // 7. En-Suite Bathroom Partition (X: 1.3 to 3.0, Z: 0.9 to 2.4)
        const wcPartitionGeo = new THREE.BoxGeometry(0.12, 3.2, 1.5);
        const wcPartition = new THREE.Mesh(wcPartitionGeo, this.materials.wall);
        wcPartition.position.set(1.3, 1.6, 1.65);
        wcPartition.receiveShadow = true;
        roomGroup.add(wcPartition);
        this.walls.push(wcPartition);

        const wcFrontPartition = new THREE.Mesh(new THREE.BoxGeometry(0.6, 3.2, 0.12), this.materials.wall);
        wcFrontPartition.position.set(1.6, 1.6, 0.9);
        roomGroup.add(wcFrontPartition);
        this.walls.push(wcFrontPartition);

        // WC Frosted Glass Door
        const wcDoor = new THREE.Mesh(new THREE.BoxGeometry(0.9, 2.3, 0.04), this.materials.glass);
        wcDoor.position.set(2.4, 1.15, 0.9);
        roomGroup.add(wcDoor);

        // Bathroom Tiled Floor
        const wcFloorGeo = new THREE.BoxGeometry(1.65, 0.205, 1.45);
        const wcFloor = new THREE.Mesh(wcFloorGeo, this.materials.tileFloor);
        wcFloor.position.set(2.15, -0.09, 1.65);
        roomGroup.add(wcFloor);

        // 8. Outdoor Balcony (Outside left wall: X: -3.0 to -4.8, Z: -2.4 to 2.4)
        const balconyFloor = new THREE.Mesh(new THREE.BoxGeometry(1.8, 0.2, 4.8), this.materials.tileFloor);
        balconyFloor.position.set(-3.9, -0.1, 0);
        balconyFloor.receiveShadow = true;
        roomGroup.add(balconyFloor);

        // Balcony Railing (Glass + Metal Handrail)
        const railingGeo = new THREE.BoxGeometry(0.04, 1.1, 4.8);
        const railing = new THREE.Mesh(railingGeo, this.materials.glass);
        railing.position.set(-4.78, 0.55, 0);
        const handrail = new THREE.Mesh(new THREE.BoxGeometry(0.08, 0.05, 4.8), this.materials.blackMetal);
        handrail.position.set(-4.78, 1.12, 0);
        roomGroup.add(railing, handrail);

        // Balcony End Railings
        const endRailing1 = new THREE.Mesh(new THREE.BoxGeometry(1.8, 1.1, 0.04), this.materials.blackMetal);
        endRailing1.position.set(-3.9, 0.55, -2.38);
        const endRailing2 = new THREE.Mesh(new THREE.BoxGeometry(1.8, 1.1, 0.04), this.materials.blackMetal);
        endRailing2.position.set(-3.9, 0.55, 2.38);
        roomGroup.add(endRailing1, endRailing2);

        this.scene.add(roomGroup);
    }

    /* =========================================================================
       FURNITURE & AMENITIES
       ========================================================================= */
    buildFurniture() {
        const furnGroup = new THREE.Group();

        // -------------------------------------------------------------
        // A. BEDROOM AREA (Giường ngủ đôi 1m6 x 2m & Tab đầu giường)
        // -------------------------------------------------------------
        const bedFrameGeo = new THREE.BoxGeometry(1.8, 0.35, 2.15);
        const bedFrame = new THREE.Mesh(bedFrameGeo, this.materials.darkWood);
        bedFrame.position.set(-1.8, 0.175, -1.2);
        bedFrame.castShadow = true;
        bedFrame.receiveShadow = true;
        furnGroup.add(bedFrame);

        // Headboard (Tựa đầu giường)
        const headboardGeo = new THREE.BoxGeometry(1.9, 1.1, 0.12);
        const headboard = new THREE.Mesh(headboardGeo, this.materials.darkWood);
        headboard.position.set(-1.8, 0.65, -2.22);
        headboard.castShadow = true;
        furnGroup.add(headboard);

        // Mattress (Nệm cao cấp)
        const mattressGeo = new THREE.BoxGeometry(1.65, 0.28, 2.0);
        const mattress = new THREE.Mesh(mattressGeo, this.materials.mattress);
        mattress.position.set(-1.8, 0.45, -1.15);
        mattress.castShadow = true;
        furnGroup.add(mattress);

        // Duvet / Blanket (Chăn ga đổi màu được)
        const duvetGeo = new THREE.BoxGeometry(1.68, 0.12, 1.45);
        this.duvetMesh = new THREE.Mesh(duvetGeo, this.materials.bedding);
        this.duvetMesh.position.set(-1.8, 0.54, -0.85);
        this.duvetMesh.castShadow = true;
        furnGroup.add(this.duvetMesh);

        // Pillows (2 Gối)
        const pillowGeo = new THREE.BoxGeometry(0.65, 0.14, 0.45);
        const pillow1 = new THREE.Mesh(pillowGeo, this.materials.mattress);
        pillow1.position.set(-2.25, 0.64, -1.85);
        pillow1.rotation.x = 0.15;
        pillow1.castShadow = true;
        const pillow2 = new THREE.Mesh(pillowGeo, this.materials.mattress);
        pillow2.position.set(-1.35, 0.64, -1.85);
        pillow2.rotation.x = 0.15;
        pillow2.castShadow = true;
        furnGroup.add(pillow1, pillow2);

        // Nightstands (Tab đầu giường)
        const nightstandGeo = new THREE.BoxGeometry(0.45, 0.45, 0.45);
        const nightstand = new THREE.Mesh(nightstandGeo, this.materials.lightWood);
        nightstand.position.set(-2.8, 0.225, -2.0);
        nightstand.castShadow = true;
        furnGroup.add(nightstand);

        // Bedside Lamp
        const lampBase = new THREE.Mesh(new THREE.CylinderGeometry(0.08, 0.1, 0.03, 16), this.materials.chrome);
        lampBase.position.set(-2.8, 0.46, -2.0);
        const lampShade = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.16, 0.22, 16), this.materials.whiteGloss);
        lampShade.position.set(-2.8, 0.62, -2.0);
        furnGroup.add(lampBase, lampShade);

        // -------------------------------------------------------------
        // B. WORKSTATION (Bàn làm việc, Ghế xoay, Laptop, Kệ sách)
        // -------------------------------------------------------------
        // Desk
        const deskTop = new THREE.Mesh(new THREE.BoxGeometry(1.4, 0.05, 0.7), this.materials.lightWood);
        deskTop.position.set(2.1, 0.75, -2.0);
        deskTop.castShadow = true;
        const legGeo = new THREE.BoxGeometry(0.04, 0.73, 0.04);
        const leg1 = new THREE.Mesh(legGeo, this.materials.blackMetal);
        leg1.position.set(1.45, 0.365, -1.7);
        const leg2 = new THREE.Mesh(legGeo, this.materials.blackMetal);
        leg2.position.set(2.75, 0.365, -1.7);
        const leg3 = new THREE.Mesh(legGeo, this.materials.blackMetal);
        leg3.position.set(1.45, 0.365, -2.3);
        const leg4 = new THREE.Mesh(legGeo, this.materials.blackMetal);
        leg4.position.set(2.75, 0.365, -2.3);
        furnGroup.add(deskTop, leg1, leg2, leg3, leg4);

        // Laptop on desk
        const laptopBase = new THREE.Mesh(new THREE.BoxGeometry(0.35, 0.015, 0.25), this.materials.chrome);
        laptopBase.position.set(2.1, 0.78, -1.95);
        const laptopScreen = new THREE.Mesh(new THREE.BoxGeometry(0.35, 0.24, 0.012), this.materials.blackMetal);
        laptopScreen.position.set(2.1, 0.90, -2.08);
        laptopScreen.rotation.x = -0.15;
        const laptopDisplay = new THREE.Mesh(new THREE.BoxGeometry(0.32, 0.21, 0.005), this.materials.screenGlow);
        laptopDisplay.position.set(2.1, 0.90, -2.07);
        laptopDisplay.rotation.x = -0.15;
        furnGroup.add(laptopBase, laptopScreen, laptopDisplay);

        // Desk Lamp
        const dLampBase = new THREE.Mesh(new THREE.CylinderGeometry(0.06, 0.08, 0.02, 16), this.materials.blackMetal);
        dLampBase.position.set(2.65, 0.78, -2.15);
        const dLampArm = new THREE.Mesh(new THREE.CylinderGeometry(0.012, 0.012, 0.4, 8), this.materials.chrome);
        dLampArm.position.set(2.65, 0.98, -2.15);
        dLampArm.rotation.z = -0.3;
        const dLampHead = new THREE.Mesh(new THREE.ConeGeometry(0.08, 0.12, 16), this.materials.blackMetal);
        dLampHead.position.set(2.52, 1.15, -2.15);
        dLampHead.rotation.z = 0.9;
        furnGroup.add(dLampBase, dLampArm, dLampHead);

        // Ergonomic Chair
        const chairSeat = new THREE.Mesh(new THREE.BoxGeometry(0.48, 0.06, 0.48), this.materials.blackMetal);
        chairSeat.position.set(2.1, 0.45, -1.35);
        chairSeat.castShadow = true;
        const chairBack = new THREE.Mesh(new THREE.BoxGeometry(0.44, 0.52, 0.05), this.materials.blackMetal);
        chairBack.position.set(2.1, 0.73, -1.13);
        const chairStem = new THREE.Mesh(new THREE.CylinderGeometry(0.03, 0.03, 0.42, 16), this.materials.chrome);
        chairStem.position.set(2.1, 0.22, -1.35);
        const chairBase = new THREE.Mesh(new THREE.CylinderGeometry(0.24, 0.24, 0.03, 5), this.materials.blackMetal);
        chairBase.position.set(2.1, 0.03, -1.35);
        furnGroup.add(chairSeat, chairBack, chairStem, chairBase);

        // Floating Bookshelf with Books
        const shelf = new THREE.Mesh(new THREE.BoxGeometry(1.2, 0.04, 0.25), this.materials.darkWood);
        shelf.position.set(2.1, 1.8, -2.25);
        furnGroup.add(shelf);
        const bookColors = [0xef4444, 0x3b82f6, 0x10b981, 0xf59e0b, 0x8b5cf6];
        for (let b = 0; b < 5; b++) {
            const bookMat = new THREE.MeshStandardMaterial({ color: bookColors[b], roughness: 0.6 });
            const book = new THREE.Mesh(new THREE.BoxGeometry(0.05, 0.22 + (b % 3) * 0.03, 0.18), bookMat);
            book.position.set(1.65 + b * 0.07, 1.93, -2.25);
            furnGroup.add(book);
        }

        // -------------------------------------------------------------
        // C. WARDROBE (Tủ quần áo 2 cánh cao cấp)
        // -------------------------------------------------------------
        const wardrobeGeo = new THREE.BoxGeometry(1.2, 2.4, 0.6);
        const wardrobe = new THREE.Mesh(wardrobeGeo, this.materials.whiteGloss);
        wardrobe.position.set(0.4, 1.2, -2.05);
        wardrobe.castShadow = true;
        furnGroup.add(wardrobe);
        // Vertical Handles
        const handleGeo = new THREE.BoxGeometry(0.02, 0.5, 0.02);
        const h1 = new THREE.Mesh(handleGeo, this.materials.chrome);
        h1.position.set(0.36, 1.2, -1.74);
        const h2 = new THREE.Mesh(handleGeo, this.materials.chrome);
        h2.position.set(0.44, 1.2, -1.74);
        furnGroup.add(h1, h2);

        // -------------------------------------------------------------
        // D. AIR CONDITIONER (Điều hòa Daikin Inverter)
        // -------------------------------------------------------------
        const acBodyGeo = new THREE.BoxGeometry(1.1, 0.32, 0.26);
        const acBody = new THREE.Mesh(acBodyGeo, this.materials.whiteGloss);
        acBody.position.set(-1.8, 2.55, -2.25);
        acBody.castShadow = true;
        const acLouver = new THREE.Mesh(new THREE.BoxGeometry(0.95, 0.03, 0.08), this.materials.chrome);
        acLouver.position.set(-1.8, 2.42, -2.14);
        acLouver.rotation.x = 0.4; // Open angled
        // Glowing Digital Temp (24°C)
        const acDisplay = new THREE.Mesh(new THREE.BoxGeometry(0.08, 0.04, 0.005), this.materials.screenGlow);
        acDisplay.position.set(-1.45, 2.55, -2.11);
        furnGroup.add(acBody, acLouver, acDisplay);
        this.acPosition = new THREE.Vector3(-1.8, 2.4, -2.1);

        // -------------------------------------------------------------
        // E. KITCHENETTE (Bếp mini, Bồn rửa, Tủ lạnh, Lò vi sóng)
        // -------------------------------------------------------------
        // Lower Cabinet
        const kitchenCounter = new THREE.Mesh(new THREE.BoxGeometry(0.75, 0.85, 1.8), this.materials.darkWood);
        kitchenCounter.position.set(2.55, 0.425, 0.0);
        kitchenCounter.castShadow = true;
        kitchenCounter.receiveShadow = true;
        // Quartz Countertop
        const counterTop = new THREE.Mesh(new THREE.BoxGeometry(0.8, 0.04, 1.85), this.materials.whiteGloss);
        counterTop.position.set(2.55, 0.86, 0.0);
        counterTop.castShadow = true;
        furnGroup.add(kitchenCounter, counterTop);

        // Induction Cooktop (Bếp từ)
        const cooktop = new THREE.Mesh(new THREE.BoxGeometry(0.42, 0.01, 0.65), this.materials.blackMetal);
        cooktop.position.set(2.55, 0.885, -0.45);
        furnGroup.add(cooktop);

        // Kitchen Sink (Bồn rửa bát inox)
        const sink = new THREE.Mesh(new THREE.BoxGeometry(0.45, 0.01, 0.5), this.materials.chrome);
        sink.position.set(2.55, 0.885, 0.45);
        // Faucet
        const faucetStem = new THREE.Mesh(new THREE.CylinderGeometry(0.015, 0.015, 0.25, 16), this.materials.chrome);
        faucetStem.position.set(2.82, 1.0, 0.45);
        const faucetHead = new THREE.Mesh(new THREE.CylinderGeometry(0.012, 0.012, 0.15, 16), this.materials.chrome);
        faucetHead.position.set(2.72, 1.12, 0.45);
        faucetHead.rotation.z = 1.3;
        furnGroup.add(sink, faucetStem, faucetHead);

        // Upper Kitchen Cabinets
        const upperCabinet = new THREE.Mesh(new THREE.BoxGeometry(0.4, 0.65, 1.8), this.materials.whiteGloss);
        upperCabinet.position.set(2.75, 2.0, 0.0);
        upperCabinet.castShadow = true;
        furnGroup.add(upperCabinet);

        // Mini Refrigerator (Tủ lạnh 2 cánh Inverter)
        const fridgeGeo = new THREE.BoxGeometry(0.65, 1.4, 0.65);
        const fridge = new THREE.Mesh(fridgeGeo, this.materials.chrome);
        fridge.position.set(2.6, 0.7, -1.35);
        fridge.castShadow = true;
        const fHandle = new THREE.Mesh(new THREE.BoxGeometry(0.03, 0.6, 0.03), this.materials.blackMetal);
        fHandle.position.set(2.26, 0.8, -1.1);
        furnGroup.add(fridge, fHandle);

        // Microwave
        const micro = new THREE.Mesh(new THREE.BoxGeometry(0.35, 0.25, 0.48), this.materials.blackMetal);
        micro.position.set(2.75, 1.45, -0.45);
        furnGroup.add(micro);

        // -------------------------------------------------------------
        // F. BATHROOM INTERIORS (WC, Lavabo gương, Vòi sen đứng)
        // -------------------------------------------------------------
        // Modern Toilet (Bồn cầu sứ)
        const toiletBase = new THREE.Mesh(new THREE.BoxGeometry(0.4, 0.4, 0.6), this.materials.whiteGloss);
        toiletBase.position.set(2.1, 0.2, 2.0);
        toiletBase.castShadow = true;
        const toiletTank = new THREE.Mesh(new THREE.BoxGeometry(0.4, 0.42, 0.2), this.materials.whiteGloss);
        toiletTank.position.set(2.1, 0.6, 2.25);
        furnGroup.add(toiletBase, toiletTank);

        // Floating Vanity Sink & Mirror
        const vanity = new THREE.Mesh(new THREE.BoxGeometry(0.5, 0.35, 0.4), this.materials.whiteGloss);
        vanity.position.set(1.65, 0.7, 1.3);
        const mirror = new THREE.Mesh(new THREE.BoxGeometry(0.02, 0.8, 0.45), this.materials.glass);
        mirror.position.set(1.38, 1.5, 1.3);
        const mirrorBack = new THREE.Mesh(new THREE.BoxGeometry(0.015, 0.82, 0.47), this.materials.screenGlow);
        mirrorBack.position.set(1.37, 1.5, 1.3);
        furnGroup.add(vanity, mirror, mirrorBack);

        // Shower Head & Column
        const showerCol = new THREE.Mesh(new THREE.CylinderGeometry(0.015, 0.015, 1.5, 16), this.materials.chrome);
        showerCol.position.set(2.9, 1.6, 1.3);
        const showerHead = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.12, 0.02, 16), this.materials.chrome);
        showerHead.position.set(2.75, 2.3, 1.3);
        furnGroup.add(showerCol, showerHead);

        // -------------------------------------------------------------
        // G. BALCONY & LAUNDRY (Máy giặt, Cây cảnh, Giàn phơi)
        // -------------------------------------------------------------
        // Washing Machine (Máy giặt cửa ngang)
        const washer = new THREE.Mesh(new THREE.BoxGeometry(0.65, 0.85, 0.65), this.materials.whiteGloss);
        washer.position.set(-3.7, 0.425, 1.7);
        washer.castShadow = true;
        const washerDoor = new THREE.Mesh(new THREE.CylinderGeometry(0.22, 0.22, 0.02, 24), this.materials.blackMetal);
        washerDoor.rotation.z = Math.PI / 2;
        washerDoor.position.set(-3.36, 0.45, 1.7);
        furnGroup.add(washer, washerDoor);

        // Potted Plants (Cây xanh ban công)
        const potGeo = new THREE.CylinderGeometry(0.2, 0.14, 0.38, 16);
        const pot = new THREE.Mesh(potGeo, this.materials.whiteGloss);
        pot.position.set(-3.7, 0.19, -1.8);
        pot.castShadow = true;
        const plantGeo = new THREE.DodecahedronGeometry(0.35, 1);
        const plant = new THREE.Mesh(plantGeo, this.materials.foliage);
        plant.position.set(-3.7, 0.52, -1.8);
        plant.castShadow = true;
        furnGroup.add(pot, plant);

        // Indoor Planter beside bed
        const inPot = new THREE.Mesh(new THREE.CylinderGeometry(0.15, 0.12, 0.35, 16), this.materials.blackMetal);
        inPot.position.set(-2.6, 0.175, 0.4);
        const inPlant = new THREE.Mesh(new THREE.DodecahedronGeometry(0.26, 1), this.materials.foliage);
        inPlant.position.set(-2.6, 0.45, 0.4);
        furnGroup.add(inPot, inPlant);

        // -------------------------------------------------------------
        // H. SMART & SAFETY GADGETS (Wi-Fi 6, Công tơ điện, PCCC)
        // -------------------------------------------------------------
        // Wi-Fi 6 Router on wall
        const router = new THREE.Mesh(new THREE.BoxGeometry(0.25, 0.04, 0.18), this.materials.blackMetal);
        router.position.set(0.6, 2.2, 2.3);
        const routerAntenna1 = new THREE.Mesh(new THREE.CylinderGeometry(0.006, 0.006, 0.18), this.materials.blackMetal);
        routerAntenna1.position.set(0.52, 2.3, 2.3);
        const routerAntenna2 = new THREE.Mesh(new THREE.CylinderGeometry(0.006, 0.006, 0.18), this.materials.blackMetal);
        routerAntenna2.position.set(0.68, 2.3, 2.3);
        furnGroup.add(router, routerAntenna1, routerAntenna2);

        // Smart Electric Sub-Meter (Công tơ điện thông minh)
        const meter = new THREE.Mesh(new THREE.BoxGeometry(0.18, 0.28, 0.08), this.materials.whiteGloss);
        meter.position.set(-1.0, 1.8, 2.34);
        const meterLcd = new THREE.Mesh(new THREE.BoxGeometry(0.12, 0.06, 0.01), this.materials.screenGlow);
        meterLcd.position.set(-1.0, 1.84, 2.29);
        furnGroup.add(meter, meterLcd);

        // Fire Extinguisher (Bình chữa cháy PCCC)
        const extMat = new THREE.MeshStandardMaterial({ color: 0xef4444, roughness: 0.3, metalness: 0.3 });
        const extTank = new THREE.Mesh(new THREE.CylinderGeometry(0.07, 0.07, 0.38, 16), extMat);
        extTank.position.set(-0.85, 0.4, 2.25);
        extTank.castShadow = true;
        const extNozzle = new THREE.Mesh(new THREE.CylinderGeometry(0.02, 0.02, 0.1, 8), this.materials.blackMetal);
        extNozzle.position.set(-0.85, 0.62, 2.25);
        furnGroup.add(extTank, extNozzle);

        this.scene.add(furnGroup);
    }

    /* =========================================================================
       INTERACTIVE 3D HOTSPOTS
       ========================================================================= */
    buildHotspots() {
        const hotspotData = [
            {
                id: 'bed',
                title: 'Giường Ngủ Cao Cấp 1m6 x 2m',
                category: 'Phòng Ngủ',
                position: new THREE.Vector3(-1.8, 0.9, -1.2),
                cameraPos: new THREE.Vector3(-0.4, 2.0, 0.6),
                targetPos: new THREE.Vector3(-1.8, 0.7, -1.2),
                description: 'Giường gỗ sồi tự nhiên kèm nệm cao su non 20cm êm ái, bảo vệ cột sống. Ga nệm vải Tencel thoáng mát, kháng khuẩn.',
                specs: [
                    { label: 'Kích thước', val: '160cm x 200cm' },
                    { label: 'Nệm', val: 'Cao su non Foam 20cm' },
                    { label: 'Tiện ích kèm', val: 'Tab đầu giường, đèn ngủ' },
                    { label: 'Tình trạng', val: 'Mới 100%' }
                ]
            },
            {
                id: 'ac',
                title: 'Điều Hòa Daikin Inverter 1.5HP',
                category: 'Điện Lạnh & Tiện Nghi',
                position: new THREE.Vector3(-1.8, 2.45, -2.1),
                cameraPos: new THREE.Vector3(-1.8, 2.0, -0.2),
                targetPos: new THREE.Vector3(-1.8, 2.5, -2.2),
                description: 'Máy lạnh Daikin thế hệ mới tiết kiệm điện Inverter chuẩn 5 sao. Lọc bụi mịn PM2.5, làm lạnh nhanh Coanda dịu êm không khô da.',
                specs: [
                    { label: 'Công suất', val: '1.5 HP (12.000 BTU)' },
                    { label: 'Tiêu thụ', val: '~0.8 kWh/h' },
                    { label: 'Công nghệ', val: 'Inverter + Lọc PM2.5' },
                    { label: 'Bảo trì định kỳ', val: '3 tháng/lần miễn phí' }
                ]
            },
            {
                id: 'desk',
                title: 'Góc Làm Việc & Học Tập Chuẩn Công Thái Học',
                category: 'Góc Làm Việc',
                position: new THREE.Vector3(2.1, 1.1, -1.9),
                cameraPos: new THREE.Vector3(1.2, 1.8, -0.5),
                targetPos: new THREE.Vector3(2.1, 0.9, -1.9),
                description: 'Bàn làm việc gỗ công nghiệp phủ Melamine chống trầy 1m4 kèm ghế xoay công thái học nâng hạ piston. Trang bị đèn học LED bảo vệ thị lực và giá sách nổi.',
                specs: [
                    { label: 'Mặt bàn', val: 'Gỗ MDF 140cm x 70cm' },
                    { label: 'Ghế', val: 'Công thái học tựa lưới thoáng khí' },
                    { label: 'Cổng kết nối', val: 'Ổ cắm điện âm + sạc nhanh Type-C' },
                    { label: 'Ánh sáng', val: 'Đèn bàn 3 chế độ màu' }
                ]
            },
            {
                id: 'kitchen',
                title: 'Khu Bếp Mini & Tủ Lạnh Inverter',
                category: 'Bếp & Nấu Nướng',
                position: new THREE.Vector3(2.5, 1.1, 0.1),
                cameraPos: new THREE.Vector3(0.8, 1.8, 0.4),
                targetPos: new THREE.Vector3(2.5, 1.0, 0.0),
                description: 'Bếp tiện nghi mặt đá thạch anh dễ lau chùi, bếp từ đôi công suất lớn, bồn rửa inox 304 chống ồn. Tủ lạnh 2 cánh Inverter và lò vi sóng hâm nóng tiện lợi.',
                specs: [
                    { label: 'Mặt bếp', val: 'Đá nhân tạo Quartz chống thấm' },
                    { label: 'Bếp nấu', val: 'Bếp từ đôi Sunhouse' },
                    { label: 'Tủ lạnh', val: 'Panasonic Inverter 180L' },
                    { label: 'Hút mùi', val: 'Ống dẫn hút khí chuyên dụng' }
                ]
            },
            {
                id: 'bathroom',
                title: 'Nhà Vệ Sinh Khép Kín & Bình Nóng Lạnh',
                category: 'Nhà Tắm / WC',
                position: new THREE.Vector3(2.1, 1.3, 1.6),
                cameraPos: new THREE.Vector3(0.6, 2.0, 1.6),
                targetPos: new THREE.Vector3(2.1, 1.2, 1.6),
                description: 'Phòng tắm khép kín riêng tư, gạch chống trượt Ceramic, gương soi LED cảm ứng. Trang bị bình nước nóng Ariston 20L chống giật ELCB và vòi sen tăng áp.',
                specs: [
                    { label: 'Bình nóng lạnh', val: 'Ariston 20L chống giật' },
                    { label: 'Thiết bị vệ sinh', val: 'Viglacera liền khối cao cấp' },
                    { label: 'Gương', val: 'Gương LED cảm ứng sấy mờ' },
                    { label: 'Thông gió', val: 'Quạt hút mùi âm trần' }
                ]
            },
            {
                id: 'balcony',
                title: 'Ban Công Riêng & Máy Giặt Cửa Ngang',
                category: 'Ban Công & Giặt Sấy',
                position: new THREE.Vector3(-3.8, 1.1, 0.8),
                cameraPos: new THREE.Vector3(-1.8, 2.2, 0.5),
                targetPos: new THREE.Vector3(-3.8, 1.0, 0.5),
                description: 'Ban công view thoáng lấy trọn ánh sáng tự nhiên và gió trời. Có sẵn máy giặt Electrolux lồng ngang tiết kiệm nước, giàn phơi đồ thông minh và cây xanh tiểu cảnh.',
                specs: [
                    { label: 'Máy giặt', val: 'Electrolux Inverter 9kg' },
                    { label: 'Giàn phơi', val: 'Nâng hạ gắn trần thông minh' },
                    { label: 'An toàn', val: 'Lan can kính cường lực cao 1.2m' },
                    { label: 'Hướng view', val: 'Hướng Đông Nam mát mẻ' }
                ]
            },
            {
                id: 'smartlock',
                title: 'Khóa Cửa Thông Minh Vân Tay SmartLock',
                category: 'An Ninh & Bảo Mật',
                position: new THREE.Vector3(0.0, 1.3, 2.35),
                cameraPos: new THREE.Vector3(-0.2, 1.4, 1.0),
                targetPos: new THREE.Vector3(0.1, 1.2, 2.35),
                description: 'Khóa cửa vân tay cảm ứng điện dung một chạm, mở khóa bằng mã PIN số ảo, thẻ từ RFID hoặc app điện thoại. Tự động khóa chốt an toàn khi đóng cửa.',
                specs: [
                    { label: 'Phương thức', val: 'Vân tay + Mã PIN + Thẻ từ' },
                    { label: 'Tốc độ nhận diện', val: '< 0.3 giây' },
                    { label: 'Báo động', val: 'Cảnh báo chống cạy phá và nhập sai 5 lần' },
                    { label: 'Nguồn điện', val: 'Pin sạc lithium + cổng sạc cấp cứu Type-C' }
                ]
            },
            {
                id: 'meter',
                title: 'Công Tơ Điện Tử & Bình Cứu Hỏa PCCC',
                category: 'Kỹ Thuật & An Toàn',
                position: new THREE.Vector3(-1.0, 1.5, 2.35),
                cameraPos: new THREE.Vector3(-1.0, 1.5, 1.0),
                targetPos: new THREE.Vector3(-1.0, 1.4, 2.35),
                description: 'Đồng hồ đo chỉ số điện riêng từng phòng có kiểm định nhà nước, màn hình điện tử chống thất thoát. Trang bị bình bột chữa cháy mini tiêu chuẩn PCCC.',
                specs: [
                    { label: 'Công tơ điện', val: 'Điện tử kiểm định chuẩn EVN' },
                    { label: 'Đơn giá minh bạch', val: 'Đồng bộ trực tiếp lên app SmartRoom' },
                    { label: 'PCCC', val: 'Bình bột MFZ4 đạt chuẩn kiểm định' },
                    { label: 'Báo khói', val: 'Cảm biến quang điện trên trần' }
                ]
            }
        ];

        hotspotData.forEach(item => {
            // Hotspot Anchor 3D Sprite
            const canvas = document.createElement('canvas');
            canvas.width = 128;
            canvas.height = 128;
            const ctx = canvas.getContext('2d');
            
            // Outer glowing ring
            ctx.beginPath();
            ctx.arc(64, 64, 52, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(59, 130, 246, 0.35)';
            ctx.fill();

            // Inner solid circle
            ctx.beginPath();
            ctx.arc(64, 64, 36, 0, Math.PI * 2);
            ctx.fillStyle = '#2563eb';
            ctx.fill();
            ctx.lineWidth = 6;
            ctx.strokeStyle = '#ffffff';
            ctx.stroke();

            // Center dot
            ctx.beginPath();
            ctx.arc(64, 64, 14, 0, Math.PI * 2);
            ctx.fillStyle = '#ffffff';
            ctx.fill();

            const texture = new THREE.CanvasTexture(canvas);
            const spriteMat = new THREE.SpriteMaterial({ map: texture, depthTest: false, depthWrite: false });
            const sprite = new THREE.Sprite(spriteMat);
            sprite.position.copy(item.position);
            sprite.scale.set(0.38, 0.38, 0.38);
            sprite.userData = item;

            this.scene.add(sprite);
            this.hotspots.push(sprite);
        });
    }

    /* =========================================================================
       AC PARTICLE AIRFLOW BREEZE
       ========================================================================= */
    buildAcParticles() {
        const particleCount = 70;
        const geometry = new THREE.BufferGeometry();
        const positions = new Float32Array(particleCount * 3);
        const velocities = [];

        for (let i = 0; i < particleCount; i++) {
            positions[i * 3] = this.acPosition.x + (Math.random() - 0.5) * 0.9;
            positions[i * 3 + 1] = this.acPosition.y - Math.random() * 0.1;
            positions[i * 3 + 2] = this.acPosition.z + Math.random() * 0.2;

            velocities.push({
                x: (Math.random() - 0.5) * 0.012,
                y: -0.015 - Math.random() * 0.01,
                z: 0.02 + Math.random() * 0.02
            });
        }

        geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        const material = new THREE.PointsMaterial({
            color: 0x60a5fa,
            size: 0.08,
            transparent: true,
            opacity: 0.65,
            blending: THREE.AdditiveBlending
        });

        this.acParticles = new THREE.Points(geometry, material);
        this.acParticleVelocities = velocities;
        this.scene.add(this.acParticles);
    }

    updateAcParticles() {
        if (!this.acParticles) return;
        const positions = this.acParticles.geometry.attributes.position.array;
        const count = positions.length / 3;

        for (let i = 0; i < count; i++) {
            positions[i * 3] += this.acParticleVelocities[i].x;
            positions[i * 3 + 1] += this.acParticleVelocities[i].y;
            positions[i * 3 + 2] += this.acParticleVelocities[i].z;

            // Reset if traveled too far
            if (positions[i * 3 + 1] < 0.6 || positions[i * 3 + 2] > 1.2) {
                positions[i * 3] = this.acPosition.x + (Math.random() - 0.5) * 0.9;
                positions[i * 3 + 1] = this.acPosition.y - Math.random() * 0.1;
                positions[i * 3 + 2] = this.acPosition.z + Math.random() * 0.2;
            }
        }
        this.acParticles.geometry.attributes.position.needsUpdate = true;
    }

    /* =========================================================================
       CAMERA PRESETS
       ========================================================================= */
    setPresetView(preset) {
        switch (preset) {
            case 'isometric':
                this.animateCamera(new THREE.Vector3(7.5, 6.5, 9.0), new THREE.Vector3(0, 1.2, 0));
                break;
            case 'floorplan':
                this.animateCamera(new THREE.Vector3(0, 11.0, 0.1), new THREE.Vector3(0, 0, 0));
                break;
            case 'firstperson':
                this.animateCamera(new THREE.Vector3(-0.3, 1.6, 2.0), new THREE.Vector3(-0.3, 1.5, -1.0));
                break;
            case 'bed':
                this.animateCamera(new THREE.Vector3(-0.4, 2.0, 0.6), new THREE.Vector3(-1.8, 0.7, -1.2));
                break;
            case 'desk':
                this.animateCamera(new THREE.Vector3(1.2, 1.8, -0.5), new THREE.Vector3(2.1, 0.9, -1.9));
                break;
            case 'kitchen':
                this.animateCamera(new THREE.Vector3(0.8, 1.8, 0.4), new THREE.Vector3(2.5, 1.0, 0.0));
                break;
            case 'bathroom':
                this.animateCamera(new THREE.Vector3(0.6, 2.0, 1.6), new THREE.Vector3(2.1, 1.2, 1.6));
                break;
            case 'balcony':
                this.animateCamera(new THREE.Vector3(-1.8, 2.2, 0.5), new THREE.Vector3(-3.8, 1.0, 0.5));
                break;
            default:
                this.animateCamera(new THREE.Vector3(7.5, 6.5, 9.0), new THREE.Vector3(0, 1.2, 0));
        }
    }

    animateCamera(targetPos, targetLookAt) {
        this.cameraTargetPos.copy(targetPos);
        this.controlsTargetPos.copy(targetLookAt);
        this.isAnimatingCamera = true;
    }

    /* =========================================================================
       CUSTOMIZER & MODES
       ========================================================================= */
    setWallColor(hexColor) {
        if (this.accentWallMesh) {
            this.accentWallMesh.material.color.set(hexColor);
        }
    }

    setBeddingColor(hexColor) {
        if (this.duvetMesh) {
            this.duvetMesh.material.color.set(hexColor);
        }
    }

    setCutaway(enabled) {
        this.options.showCutaway = enabled;
        if (this.ceilingMesh) {
            this.ceilingMesh.visible = !enabled;
        }
        this.walls.forEach(w => {
            w.material.transparent = enabled;
            w.material.opacity = enabled ? 0.35 : 1.0;
        });
    }

    /* =========================================================================
       INTERACTION & RAYCASTING
       ========================================================================= */
    onPointerDown(event) {
        const rect = this.renderer.domElement.getBoundingClientRect();
        this.mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        this.mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

        this.raycaster.setFromCamera(this.mouse, this.camera);
        const intersects = this.raycaster.intersectObjects(this.hotspots, false);

        if (intersects.length > 0) {
            const hitSprite = intersects[0].object;
            const data = hitSprite.userData;
            if (data) {
                this.animateCamera(data.cameraPos, data.targetPos);
                if (typeof this.options.onSelectHotspot === 'function') {
                    this.options.onSelectHotspot(data);
                }
            }
        }
    }

    onPointerMove(event) {
        const rect = this.renderer.domElement.getBoundingClientRect();
        this.mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        this.mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

        this.raycaster.setFromCamera(this.mouse, this.camera);
        const intersects = this.raycaster.intersectObjects(this.hotspots, false);

        this.renderer.domElement.style.cursor = intersects.length > 0 ? 'pointer' : 'default';
    }

    onWindowResize() {
        if (!this.container || !this.renderer || !this.camera) return;
        const width = this.container.clientWidth;
        const height = this.container.clientHeight;
        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(width, height);
    }

    /* =========================================================================
       MAIN RENDER LOOP
       ========================================================================= */
    animate() {
        requestAnimationFrame(this.animate.bind(this));

        const delta = this.clock.getDelta();
        const time = this.clock.getElapsedTime();

        // Pulsate hotspots
        this.hotspots.forEach((h, idx) => {
            const scale = 0.38 + Math.sin(time * 3 + idx) * 0.04;
            h.scale.set(scale, scale, scale);
        });

        // Update AC Particle breeze
        this.updateAcParticles();

        // Camera Smooth Transition
        if (this.isAnimatingCamera) {
            this.camera.position.lerp(this.cameraTargetPos, this.cameraLerpSpeed);
            if (this.controls) {
                this.controls.target.lerp(this.controlsTargetPos, this.cameraLerpSpeed);
            }

            if (this.camera.position.distanceTo(this.cameraTargetPos) < 0.05) {
                this.isAnimatingCamera = false;
            }
        }

        if (this.controls) {
            this.controls.update();
        }

        this.renderer.render(this.scene, this.camera);
    }
}

// Attach globally
window.Room3DTour = Room3DTour;
