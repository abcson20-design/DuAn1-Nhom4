<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= Csrf::token() ?>">
    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?> - NEXVORA Store</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        },
                        dark: {
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#090d16',
                        }
                    },
                    animation: {
                        'blob': 'blob 12s infinite ease-in-out',
                        'blob-reverse': 'blob-reverse 15s infinite ease-in-out',
                        'float': 'float 5s ease-in-out infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%, 100%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(45px, -60px) scale(1.15)' },
                            '66%': { transform: 'translate(-35px, 35px) scale(0.92)' },
                        },
                        'blob-reverse': {
                            '0%, 100%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(-50px, 50px) scale(1.1)' },
                            '66%': { transform: 'translate(40px, -40px) scale(0.95)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    window.BASE_URL = <?= json_encode(BASE_URL) ?>;

    window.getCsrfToken = function() {
        return document.querySelector('meta[name="csrf-token"]')?.content 
            || document.querySelector('input[name="_csrf_token"]')?.value 
            || '';
    };

    // Global Fetch Interceptor: Tự động bảo vệ mọi request POST / PUT / DELETE bằng CSRF Token
    (function() {
        const _origFetch = window.fetch;
        window.fetch = function(url, options) {
            options = options || {};
            const method = (options.method || 'GET').toUpperCase();
            if (['POST', 'PUT', 'DELETE', 'PATCH'].includes(method)) {
                const token = window.getCsrfToken();
                options.headers = options.headers || {};
                if (options.headers instanceof Headers) {
                    if (!options.headers.has('X-CSRF-TOKEN') && token) options.headers.set('X-CSRF-TOKEN', token);
                    if (!options.headers.has('X-Requested-With')) options.headers.set('X-Requested-With', 'XMLHttpRequest');
                } else if (Array.isArray(options.headers)) {
                    if (token) options.headers.push(['X-CSRF-TOKEN', token]);
                    options.headers.push(['X-Requested-With', 'XMLHttpRequest']);
                } else {
                    if (token && !options.headers['X-CSRF-TOKEN'] && !options.headers['X-CSRF-Token']) {
                        options.headers['X-CSRF-TOKEN'] = token;
                    }
                    if (!options.headers['X-Requested-With']) {
                        options.headers['X-Requested-With'] = 'XMLHttpRequest';
                    }
                }

                if (options.body instanceof FormData && token) {
                    if (!options.body.has('_csrf_token')) {
                        options.body.append('_csrf_token', token);
                    }
                }
            }
            return _origFetch.call(this, url, options);
        };
    })();

    window.toggleWishlist = function (productId, btnElement, e) {
        if (e && e.stopPropagation) {
            e.stopPropagation();
            e.preventDefault();
        } else if (window.event) {
            window.event.stopPropagation();
            window.event.preventDefault();
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content 
                       || document.querySelector('input[name="_csrf_token"]')?.value 
                       || '';
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('_csrf_token', csrfToken);

        let targetUrl = 'index.php?url=wishlist/toggle';
        if (window.BASE_URL) {
            try {
                targetUrl = new URL('index.php?url=wishlist/toggle', encodeURI(window.BASE_URL)).href;
            } catch (err) {
                targetUrl = encodeURI(window.BASE_URL) + 'index.php?url=wishlist/toggle';
            }
        }

        fetch(targetUrl, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.require_login) {
                // Khách chưa đăng nhập: chuyển hướng thẳng tới trang đăng nhập (giống luồng nút Mua ngay)
                window.location.href = data.login_url || 'index.php?url=auth/login&msg=login_required_wishlist';
                return;
            }

            if (data.success) {
                // Cập nhật tất cả các nút wishlist của sản phẩm này trên trang
                const allWishlistBtns = document.querySelectorAll(`[data-wishlist-pid="${productId}"]`);
                allWishlistBtns.forEach(btn => {
                    const icon = btn.querySelector('i');
                    if (data.is_wishlisted) {
                        btn.classList.add('bg-rose-500/20', 'border-rose-500/50', 'text-rose-400');
                        btn.classList.remove('bg-slate-900/80', 'border-slate-700/80', 'text-slate-400');
                        if (icon) {
                            icon.className = 'fa-solid fa-heart text-xs text-rose-500 scale-125 transition-transform duration-200';
                            setTimeout(() => icon.classList.remove('scale-125'), 300);
                        }
                        btn.setAttribute('title', 'Bỏ thích sản phẩm này');
                    } else {
                        btn.classList.remove('bg-rose-500/20', 'border-rose-500/50', 'text-rose-400');
                        btn.classList.add('bg-slate-900/80', 'border-slate-700/80', 'text-slate-400');
                        if (icon) {
                            icon.className = 'fa-regular fa-heart text-xs text-slate-300 group-hover/wishlist:text-white transition-transform duration-200';
                        }
                        btn.setAttribute('title', 'Thêm vào danh sách yêu thích');
                    }
                });

                // Nếu đang ở trang chi tiết sản phẩm và có nút #btnWishlistDetail
                const detailBtn = document.getElementById('btnWishlistDetail');
                if (detailBtn && detailBtn.getAttribute('data-product-id') == productId) {
                    if (data.is_wishlisted) {
                        detailBtn.className = 'bg-rose-500 hover:bg-rose-600 text-white border border-rose-400 px-4 py-3.5 rounded-2xl transition flex items-center justify-center shrink-0 shadow-lg shadow-rose-500/25';
                        detailBtn.setAttribute('title', 'Bỏ thích sản phẩm này');
                    } else {
                        detailBtn.className = 'bg-slate-800 hover:bg-rose-500/20 text-rose-400 border border-slate-700 hover:border-rose-500/50 px-4 py-3.5 rounded-2xl transition flex items-center justify-center shrink-0';
                        detailBtn.setAttribute('title', 'Thêm vào danh sách yêu thích');
                    }
                }

                // Nếu đang ở trang Danh sách yêu thích (Wishlist page) mà bấm xóa -> ẩn card mượt mà
                const card = document.getElementById('wishlist-card-' + productId);
                if (card && !data.is_wishlisted) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    card.style.transition = 'all 0.3s ease';
                    setTimeout(() => {
                        card.remove();
                        if (data.count === 0) {
                            location.reload();
                        }
                    }, 300);
                }

                // Cập nhật số lượng trên badge ở Header
                const badge = document.getElementById('wishlist-badge-count');
                if (badge) {
                    badge.textContent = data.count || 0;
                    if (data.count > 0) {
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }

                // Thông báo Toast thành công
                if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: data.is_wishlisted ? 'success' : 'info',
                        title: data.message,
                        background: '#0f172a',
                        color: '#f8fafc',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            } else {
                if (window.Swal) {
                    Swal.fire({
                        title: 'Thông báo',
                        text: data.message || 'Có lỗi xảy ra',
                        icon: 'warning',
                        background: '#0f172a',
                        color: '#f8fafc'
                    });
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            }
        })
        .catch(err => {
            console.error('Lỗi Wishlist:', err);
        });
    };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .tech-grid-pattern {
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        }

        .glass-card {
            background: rgba(15, 23, 42, 0.72);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .glass-card:hover {
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 10px 30px -10px rgba(79, 70, 229, 0.35);
        }

        /* Hiệu ứng hiển thị nút Xem chi tiết khi nhấn vào ảnh trên thẻ card */
        .product-detail-overlay.active {
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        .product-detail-overlay.active .btn-detail-link {
            transform: translateY(0) scale(1) !important;
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0c1836; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #6366f1; }

        /* Keyframes: Các hình học bay từ dưới lên (Rising Geometric Particles) */
        @keyframes geoRise1 {
            0% {
                transform: translateY(105vh) translateX(0) rotate(0deg) scale(0.8);
                opacity: 0;
            }
            8% {
                opacity: 0.8;
            }
            88% {
                opacity: 0.8;
            }
            100% {
                transform: translateY(-12vh) translateX(40px) rotate(360deg) scale(1.05);
                opacity: 0;
            }
        }

        @keyframes geoRise2 {
            0% {
                transform: translateY(105vh) translateX(0) rotate(0deg) scale(1);
                opacity: 0;
            }
            8% {
                opacity: 0.75;
            }
            90% {
                opacity: 0.75;
            }
            100% {
                transform: translateY(-12vh) translateX(-45px) rotate(-360deg) scale(0.85);
                opacity: 0;
            }
        }

        /* Keyframes: Các hình trôi bồng bềnh dập dềnh tại chỗ (Floating Anchors) */
        @keyframes geoFloatBob {
            0%, 100% {
                transform: translateY(0px) rotate(0deg) scale(1);
            }
            50% {
                transform: translateY(-30px) rotate(180deg) scale(1.08);
            }
        }

        @keyframes geoFloatDrift {
            0%, 100% {
                transform: translate(0px, 0px) rotate(0deg);
            }
            33% {
                transform: translate(25px, -30px) rotate(120deg);
            }
            66% {
                transform: translate(-20px, 20px) rotate(240deg);
            }
        }

        @keyframes geoPulseSlow {
            0%, 100% { transform: scale(1); opacity: 0.65; }
            50% { transform: scale(1.18); opacity: 0.95; }
        }
    </style>
</head>
<body class="bg-gradient-to-b from-[#0b172e] via-[#0f2347] to-[#0a162c] text-slate-100 flex flex-col min-h-screen relative overflow-x-hidden selection:bg-primary-600 selection:text-white">

    <?php
        $currentUser = $_SESSION['user'] ?? $_SESSION['admin_user'] ?? null;
        if (!empty($currentUser) && ($currentUser['role'] ?? '') === 'admin' && empty($_SESSION['admin_user'])) {
            $_SESSION['admin_user'] = $currentUser;
        }
        $isAdmin = !empty($_SESSION['admin_user']) || (!empty($currentUser) && ($currentUser['role'] ?? '') === 'admin');
        $isGuest = empty($currentUser) && empty($_SESSION['admin_user']);
    ?>

    <!-- Ambient Dynamic Background & Glowing Geometric Shapes (Canvas bay qua lại toàn màn hình) -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <!-- Aurora Glow Blobs (Nền sáng hơn, rực rỡ và có chiều sâu công nghệ) -->
        <div class="absolute -top-24 -left-24 w-[36rem] h-[36rem] bg-indigo-500/25 rounded-full blur-[110px] animate-blob"></div>
        <div class="absolute top-1/6 right-0 w-[38rem] h-[38rem] bg-cyan-500/25 rounded-full blur-[130px] animate-blob-reverse"></div>
        <div class="absolute top-1/2 left-1/3 w-[34rem] h-[34rem] bg-purple-600/25 rounded-full blur-[125px] animate-blob"></div>
        <div class="absolute -bottom-20 right-1/4 w-[40rem] h-[40rem] bg-blue-600/25 rounded-full blur-[140px]"></div>

        <!-- Tech Grid Pattern -->
        <div class="absolute inset-0 tech-grid-pattern opacity-70"></div>

        <!-- Radial Center Ambient Highlight (Tăng độ sáng và ánh sáng cho toàn trang) -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_50%_-10%,rgba(56,189,248,0.2),transparent_70%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_45%,rgba(99,102,241,0.12),transparent_75%)]"></div>

        <!-- HTML5 Canvas: Các hình vuông, tam giác, tròn, thoi phát sáng bay qua lại trên toàn màn hình -->
        <canvas id="techGeometricCanvas" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>
    </div>

    <script>
        (function() {
            const canvas = document.getElementById('techGeometricCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let width = 0, height = 0;
            let shapes = [];
            const colors = ['#00f2fe', '#22d3ee', '#c084fc', '#a855f7', '#38bdf8', '#f43f5e', '#34d399', '#fbbf24'];
            const types = ['triangle', 'triangle_inv', 'square', 'diamond', 'ring', 'cross', 'hexagon'];

            function hexToRgba(hex, alpha) {
                const r = parseInt(hex.slice(1, 3), 16);
                const g = parseInt(hex.slice(3, 5), 16);
                const b = parseInt(hex.slice(5, 7), 16);
                return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
            }

            function resize() {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            }
            window.addEventListener('resize', resize);
            resize();

            const mouse = { x: -1000, y: -1000, radius: 140 };
            window.addEventListener('mousemove', function(e) {
                mouse.x = e.clientX;
                mouse.y = e.clientY;
            });
            window.addEventListener('mouseleave', function() {
                mouse.x = -1000;
                mouse.y = -1000;
            });

            function createShape() {
                const size = Math.random() * 24 + 16; // 16px đến 40px
                const speed = Math.random() * 0.9 + 0.5;
                const angleDir = Math.random() * Math.PI * 2;
                return {
                    x: Math.random() * width,
                    y: Math.random() * height,
                    vx: Math.cos(angleDir) * speed,
                    vy: Math.sin(angleDir) * speed,
                    size: size,
                    angle: Math.random() * Math.PI * 2,
                    vRot: (Math.random() - 0.5) * 0.025,
                    type: types[Math.floor(Math.random() * types.length)],
                    color: colors[Math.floor(Math.random() * colors.length)],
                    glow: Math.random() * 12 + 18, // 18px - 30px phát sáng rực rỡ
                    fillAlpha: Math.random() * 0.12 + 0.08,
                    pulse: Math.random() * Math.PI * 2,
                    pulseSpeed: Math.random() * 0.03 + 0.015,
                    bounce: Math.random() > 0.35 // 65% dội qua lại giữa các cạnh màn hình
                };
            }

            // Tạo 48 hình khối phát sáng bay khắp toàn màn hình
            const count = Math.min(55, Math.max(32, Math.floor(window.innerWidth / 28)));
            for (let i = 0; i < count; i++) {
                shapes.push(createShape());
            }

            function drawShape(s) {
                ctx.save();
                ctx.translate(s.x, s.y);
                ctx.rotate(s.angle);

                // Hiệu ứng phát sáng nhấp nháy neon
                let currentGlow = s.glow + Math.sin(s.pulse) * 6;
                let strokeColor = s.color;
                let strokeWidth = 2.2;

                // Tương tác chuột phát sáng mạnh hơn
                const dx = s.x - mouse.x;
                const dy = s.y - mouse.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < mouse.radius) {
                    currentGlow += (1 - dist / mouse.radius) * 25;
                    strokeWidth = 3;
                }

                ctx.shadowColor = s.color;
                ctx.shadowBlur = Math.max(5, currentGlow);
                ctx.strokeStyle = strokeColor;
                ctx.lineWidth = strokeWidth;
                ctx.fillStyle = hexToRgba(s.color, s.fillAlpha);

                const half = s.size / 2;

                if (s.type === 'square') {
                    ctx.beginPath();
                    if (ctx.roundRect) {
                        ctx.roundRect(-half, -half, s.size, s.size, 6);
                    } else {
                        ctx.rect(-half, -half, s.size, s.size);
                    }
                    ctx.fill();
                    ctx.stroke();
                } else if (s.type === 'triangle') {
                    const h = s.size * 0.866;
                    ctx.beginPath();
                    ctx.moveTo(0, -h * 0.66);
                    ctx.lineTo(half, h * 0.33);
                    ctx.lineTo(-half, h * 0.33);
                    ctx.closePath();
                    ctx.fill();
                    ctx.stroke();
                } else if (s.type === 'triangle_inv') {
                    const h = s.size * 0.866;
                    ctx.beginPath();
                    ctx.moveTo(0, h * 0.66);
                    ctx.lineTo(half, -h * 0.33);
                    ctx.lineTo(-half, -h * 0.33);
                    ctx.closePath();
                    ctx.fill();
                    ctx.stroke();
                } else if (s.type === 'diamond') {
                    ctx.beginPath();
                    ctx.moveTo(0, -s.size * 0.65);
                    ctx.lineTo(half * 0.85, 0);
                    ctx.lineTo(0, s.size * 0.65);
                    ctx.lineTo(-half * 0.85, 0);
                    ctx.closePath();
                    ctx.fill();
                    ctx.stroke();
                } else if (s.type === 'ring') {
                    ctx.beginPath();
                    ctx.arc(0, 0, half, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.stroke();
                    // Vòng kép bên trong
                    ctx.beginPath();
                    ctx.arc(0, 0, half * 0.5, 0, Math.PI * 2);
                    ctx.stroke();
                } else if (s.type === 'cross') {
                    ctx.beginPath();
                    ctx.moveTo(-half, 0);
                    ctx.lineTo(half, 0);
                    ctx.moveTo(0, -half);
                    ctx.lineTo(0, half);
                    ctx.stroke();
                } else if (s.type === 'hexagon') {
                    ctx.beginPath();
                    for (let j = 0; j < 6; j++) {
                        const a = (Math.PI / 3) * j;
                        const hx = half * Math.cos(a);
                        const hy = half * Math.sin(a);
                        if (j === 0) ctx.moveTo(hx, hy);
                        else ctx.lineTo(hx, hy);
                    }
                    ctx.closePath();
                    ctx.fill();
                    ctx.stroke();
                }

                ctx.restore();
            }

            function animate() {
                ctx.clearRect(0, 0, width, height);

                for (let i = 0; i < shapes.length; i++) {
                    const s = shapes[i];

                    // Tương tác chuột đẩy nhẹ
                    const dx = s.x - mouse.x;
                    const dy = s.y - mouse.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < mouse.radius && dist > 0) {
                        const force = (1 - dist / mouse.radius) * 1.8;
                        s.x += (dx / dist) * force;
                        s.y += (dy / dist) * force;
                    }

                    // Di chuyển qua lại
                    s.x += s.vx;
                    s.y += s.vy;
                    s.angle += s.vRot;
                    s.pulse += s.pulseSpeed;

                    // Va chạm mép & Bay qua lại trên toàn màn hình
                    if (s.bounce) {
                        if (s.x <= s.size / 2) {
                            s.x = s.size / 2;
                            s.vx = Math.abs(s.vx);
                        } else if (s.x >= width - s.size / 2) {
                            s.x = width - s.size / 2;
                            s.vx = -Math.abs(s.vx);
                        }
                        if (s.y <= s.size / 2) {
                            s.y = s.size / 2;
                            s.vy = Math.abs(s.vy);
                        } else if (s.y >= height - s.size / 2) {
                            s.y = height - s.size / 2;
                            s.vy = -Math.abs(s.vy);
                        }
                    } else {
                        const pad = s.size + 20;
                        if (s.x < -pad) s.x = width + pad;
                        else if (s.x > width + pad) s.x = -pad;
                        if (s.y < -pad) s.y = height + pad;
                        else if (s.y > height + pad) s.y = -pad;
                    }

                    drawShape(s);
                }

                requestAnimationFrame(animate);
            }

            requestAnimationFrame(animate);
        })();
    </script>

    <!-- Top Announcement Bar -->
    <div class="relative z-30 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-3 pb-1">
        <div class="bg-slate-950/80 border border-slate-800/90 backdrop-blur-md text-xs py-2 px-5 rounded-t-2xl text-slate-300 flex justify-between items-center shadow-sm">
            <div class="flex items-center space-x-4">
                <span class="flex items-center"><i class="fa-solid fa-bolt text-amber-400 mr-1.5 animate-pulse"></i> Miễn phí vận chuyển cho đơn từ 500k | Voucher TECH20</span>
                <span class="hidden md:inline text-slate-700">|</span>
                <span class="hidden md:inline"><i class="fa-solid fa-phone mr-1 text-primary-400"></i> Hotline: <?= SHOP_HOTLINE ?></span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="<?= BASE_URL ?>index.php?url=order/track" class="hover:text-primary-400 transition flex items-center">
                    <i class="fa-solid fa-truck-fast mr-1.5 text-primary-400"></i> Tra cứu đơn hàng
                </a>
                <?php if ($isAdmin): ?>
                    <span class="text-slate-700">|</span>
                    <a href="<?= BASE_URL ?>index.php?url=admin/statistics" class="text-cyan-400 font-semibold hover:underline flex items-center">
                        <i class="fa-solid fa-chart-pie mr-1"></i> Thống Kê
                    </a>
                    <span class="text-slate-700">|</span>
                    <a href="<?= BASE_URL ?>index.php?url=admin" class="text-amber-400 font-semibold hover:underline flex items-center">
                        <i class="fa-solid fa-gauge mr-1"></i> Quản Trị
                    </a>
                <?php elseif ($isGuest): ?>
                    <span class="text-slate-700">|</span>
                    <span class="text-slate-400 text-[11px]"><i class="fa-solid fa-user-lock text-amber-400 mr-1"></i> Đang duyệt: Khách</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <div class="sticky top-0 z-40 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <header class="bg-slate-900/95 backdrop-blur-2xl border border-slate-700/80 rounded-b-2xl sm:rounded-b-3xl shadow-2xl shadow-black/80 px-5 sm:px-8 py-2">
            <div class="flex items-center justify-between h-16 gap-3 sm:gap-6">
                <!-- Logo NEXVORA (Bên trái) -->
                <a href="<?= BASE_URL ?>" class="flex items-center space-x-2 group flex-shrink-0">
                    <img src="<?= BASE_URL ?>public/images/logo.png" alt="NEXVORA N Emblem" class="h-8 sm:h-9 w-auto object-contain drop-shadow-[0_0_12px_rgba(6,182,212,0.45)] group-hover:scale-105 transition duration-300">
                    <span class="text-base sm:text-lg font-black tracking-tight text-white leading-none whitespace-nowrap">NEX<span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-indigo-300 to-purple-400">VORA</span></span>
                </a>

                <!-- Navigation Links: Dàn đều ở giữa, phân bổ khoảng cách đẹp mắt, không để khoảng cách thừa -->
                <?php
                    $currentUrl = $_GET['url'] ?? 'home';
                    $isHome = ($currentUrl === 'home' || empty($_GET['url']));
                    $isPhones = ($currentUrl === 'phones' || $currentUrl === 'dien-thoai');
                    $isAccessories = ($currentUrl === 'accessories' || $currentUrl === 'phu-kien');
                    $isContact = ($currentUrl === 'contact' || $currentUrl === 'lien-he');
                ?>
                <nav class="hidden md:flex flex-1 items-center justify-center space-x-4 lg:space-x-8 px-2 text-[11px] font-bold uppercase tracking-wider">
                    <!-- 1. Trang Chủ -->
                    <a href="<?= BASE_URL ?>" class="flex flex-col items-center justify-center hover:text-cyan-400 hover:bg-slate-800/40 rounded-xl transition group py-1 px-2.5 <?= $isHome ? 'text-cyan-400 font-extrabold' : 'text-slate-300' ?>">
                        <i class="fa-solid fa-house text-xs mb-1 group-hover:scale-110 transition"></i>
                        <span class="whitespace-nowrap">Trang Chủ</span>
                    </a>

                    <!-- 2. Trang Điện Thoại -->
                    <div class="relative group">
                        <a href="<?= BASE_URL ?>index.php?url=phones" class="flex flex-col items-center justify-center hover:text-cyan-400 hover:bg-slate-800/40 rounded-xl transition py-1 px-2.5 <?= $isPhones ? 'text-cyan-400 font-extrabold' : 'text-slate-300' ?>">
                            <div class="flex items-center gap-1 mb-1">
                                <i class="fa-solid fa-mobile-screen text-xs group-hover:scale-110 transition"></i>
                                <i class="fa-solid fa-chevron-down text-[8px] text-slate-500 group-hover:rotate-180 transition duration-200"></i>
                            </div>
                            <span class="whitespace-nowrap">Điện Thoại</span>
                        </a>
                        <!-- Dropdown Hãng Điện Thoại -->
                        <div class="absolute left-1/2 -translate-x-1/2 top-full hidden group-hover:block w-56 pt-2 z-50">
                            <div class="bg-slate-900 border border-slate-700/80 rounded-2xl shadow-2xl p-3 space-y-1 backdrop-blur-xl">
                                <div class="text-[10px] uppercase font-bold text-slate-500 px-3 py-1">Hãng Smartphone</div>
                                <a href="<?= BASE_URL ?>index.php?url=phones" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                    <i class="fa-solid fa-layer-group text-primary-400 mr-2"></i> Tất cả các hãng
                                </a>
                                <a href="<?= BASE_URL ?>index.php?url=phones&brand=1" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                    <i class="fa-brands fa-apple text-slate-200 mr-2"></i> iPhone (Apple)
                                </a>
                                <a href="<?= BASE_URL ?>index.php?url=phones&brand=2" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                    <i class="fa-solid fa-gem text-blue-400 mr-2"></i> Samsung Galaxy
                                </a>
                                <a href="<?= BASE_URL ?>index.php?url=phones&brand=3" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                    <i class="fa-solid fa-fire text-amber-400 mr-2"></i> Xiaomi Leica Flagship
                                </a>
                                <a href="<?= BASE_URL ?>index.php?url=phones&brand=4" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                    <i class="fa-solid fa-camera text-emerald-400 mr-2"></i> OPPO Hasselblad
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Trang Phụ Kiện -->
                    <div class="relative group">
                        <a href="<?= BASE_URL ?>index.php?url=accessories" class="flex flex-col items-center justify-center hover:text-cyan-400 hover:bg-slate-800/40 rounded-xl transition py-1 px-2.5 <?= $isAccessories ? 'text-cyan-400 font-extrabold' : 'text-slate-300' ?>">
                            <div class="flex items-center gap-1 mb-1">
                                <i class="fa-solid fa-headphones text-xs group-hover:scale-110 transition"></i>
                                <i class="fa-solid fa-chevron-down text-[8px] text-slate-500 group-hover:rotate-180 transition duration-200"></i>
                            </div>
                            <span class="whitespace-nowrap">Phụ Kiện</span>
                        </a>
                        <!-- Dropdown Loại Phụ Kiện -->
                        <div class="absolute left-1/2 -translate-x-1/2 top-full hidden group-hover:block w-64 pt-2 z-50">
                            <div class="bg-slate-900 border border-slate-700/80 rounded-2xl shadow-2xl p-3 space-y-1 backdrop-blur-xl">
                                <div class="text-[10px] uppercase font-bold text-slate-500 px-3 py-1">Loại Phụ Kiện</div>
                                <a href="<?= BASE_URL ?>index.php?url=accessories" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                    <i class="fa-solid fa-cubes text-primary-400 mr-2"></i> Tất cả phụ kiện
                                </a>
                                <a href="<?= BASE_URL ?>index.php?url=accessories&category=2" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                    <i class="fa-solid fa-headphones-simple text-purple-400 mr-2"></i> Tai nghe Bluetooth ANC
                                </a>
                                <a href="<?= BASE_URL ?>index.php?url=accessories&category=3" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                    <i class="fa-solid fa-headphones text-cyan-400 mr-2"></i> Tai nghe có dây Type-C/3.5mm
                                </a>
                                <a href="<?= BASE_URL ?>index.php?url=accessories&category=4" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                    <i class="fa-solid fa-bolt text-amber-400 mr-2"></i> Củ sạc & Cáp sạc GaN
                                </a>
                                <a href="<?= BASE_URL ?>index.php?url=accessories&category=6" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                    <i class="fa-solid fa-battery-three-quarters text-emerald-400 mr-2"></i> Sạc dự phòng MagSafe
                                </a>
                                <a href="<?= BASE_URL ?>index.php?url=accessories&category=5" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                    <i class="fa-solid fa-shield-halved text-rose-400 mr-2"></i> Ốp lưng & Cường lực 9H
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Trang Liên Hệ -->
                    <a href="<?= BASE_URL ?>index.php?url=contact" class="flex flex-col items-center justify-center hover:text-cyan-400 hover:bg-slate-800/40 rounded-xl transition group py-1 px-2.5 <?= $isContact ? 'text-cyan-400 font-extrabold' : 'text-slate-300' ?>">
                        <i class="fa-solid fa-headset text-xs mb-1 group-hover:scale-110 transition"></i>
                        <span class="whitespace-nowrap">Liên Hệ</span>
                    </a>
                </nav>

                <!-- Search Bar, Auth User & Cart Actions -->
                <div class="flex items-center space-x-2 sm:space-x-3 flex-shrink-0">
                    <!-- Thanh Tìm Kiếm Đa Năng (Được thu ngắn gọn gàng) -->
                    <div class="relative header-search-box">
                        <form action="<?= BASE_URL ?>index.php" method="GET" class="relative flex items-center">
                            <input type="hidden" name="url" value="products">
                            <div class="relative flex items-center">
                                <span class="absolute left-2.5 text-slate-400 pointer-events-none text-xs">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input type="text" name="q" id="headerSearchInput" 
                                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" 
                                    placeholder="Tìm kiếm..." 
                                    autocomplete="off"
                                    class="w-28 sm:w-36 lg:w-44 focus:w-32 sm:focus:w-44 lg:focus:w-52 pl-8 pr-7 py-1.5 text-xs rounded-xl border border-slate-700/80 bg-slate-950/90 text-slate-100 placeholder-slate-400 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-500/20 shadow-inner transition-all duration-300">
                                
                                <!-- Nút Clear nhanh x -->
                                <button type="button" id="headerSearchClear" class="absolute right-2 text-slate-400 hover:text-white text-xs <?= empty($_GET['q']) ? 'hidden' : '' ?> transition">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                </button>
                                
                                <!-- Loading Spinner -->
                                <span id="headerSearchSpinner" class="absolute right-2 text-cyan-400 text-xs hidden animate-spin">
                                    <i class="fa-solid fa-spinner"></i>
                                </span>
                            </div>
                        </form>

                        <!-- Dropdown Gợi Ý Tìm Kiếm Nhanh (Live Search Results) -->
                        <div id="headerSearchDropdown" class="absolute right-0 w-[260px] sm:w-[320px] lg:w-[360px] top-full mt-2 hidden z-50 bg-slate-900/95 border border-slate-700/90 rounded-2xl shadow-2xl shadow-black/90 backdrop-blur-2xl overflow-hidden transition-all duration-200">
                            <div class="px-3 py-2 bg-slate-950/90 border-b border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
                                <span class="font-semibold text-slate-300"><i class="fa-solid fa-bolt text-amber-400 mr-1.5"></i>Gợi ý sản phẩm tức thì</span>
                                <span id="headerSearchCountBadge" class="text-[10px] bg-cyan-950 text-cyan-300 border border-cyan-800/80 px-2 py-0.5 rounded-full font-mono hidden">0 kết quả</span>
                            </div>
                            <div id="headerSearchResults" class="max-h-80 overflow-y-auto divide-y divide-slate-800/80">
                                <!-- Kết quả tìm kiếm hiển thị ở đây -->
                            </div>
                            <div id="headerSearchFooter" class="p-2.5 bg-slate-950/90 border-t border-slate-800 text-center hidden">
                                <a href="#" id="headerSearchAllBtn" class="text-xs font-bold text-cyan-400 hover:text-cyan-300 flex items-center justify-center gap-1.5 transition">
                                    <span>Xem tất cả kết quả</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Phân Quyền Giỏ Hàng: KHÁCH KHÔNG CÓ GIỎ HÀNG (Hiển thị cảnh báo), USER CÓ GIỎ HÀNG -->
                    <?php if ($isGuest): ?>
                        <a href="javascript:void(0)" onclick="guestCartAlert()" 
                           title="Đăng nhập để dùng giỏ hàng"
                           class="relative group bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/80 p-2 rounded-xl transition flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-bag-shopping text-slate-400 text-sm"></i>
                            <span class="absolute -top-1.5 -right-1.5 bg-amber-500/80 text-slate-950 font-black text-[9px] px-1 py-0.2 rounded-full">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                        </a>

                        <!-- Nút Đăng nhập cho khách (Nằm trên 1 dòng, không xuống hàng) -->
                        <a href="<?= BASE_URL ?>index.php?url=auth/login" 
                           class="bg-gradient-to-r from-primary-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white font-extrabold text-xs py-1.5 px-3 rounded-xl shadow-lg shadow-primary-500/25 transition flex items-center gap-1.5 whitespace-nowrap flex-shrink-0">
                            <i class="fa-solid fa-right-to-bracket text-xs"></i>
                            <span class="whitespace-nowrap">Đăng Nhập</span>
                        </a>
                    <?php else: ?>
                        <!-- USER ĐÃ ĐĂNG NHẬP: Có giỏ hàng đầy đủ & Danh sách yêu thích -->
                        <a href="<?= BASE_URL ?>index.php?url=wishlist" class="relative group bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 p-2.5 rounded-xl transition flex items-center justify-center" title="Danh sách yêu thích">
                            <i class="fa-solid fa-heart text-slate-300 group-hover:text-rose-400 text-sm"></i>
                            <span id="wishlist-badge-count" class="wishlist-badge absolute -top-1.5 -right-1.5 bg-gradient-to-r from-rose-500 to-pink-600 text-white font-extrabold text-[10px] px-1.5 py-0.5 rounded-full shadow-md <?= empty($wishlistCount) ? 'hidden' : '' ?>">
                                <?= $wishlistCount ?? 0 ?>
                            </span>
                        </a>

                        <a href="<?= BASE_URL ?>index.php?url=cart" class="relative group bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 p-2.5 rounded-xl transition flex items-center justify-center">
                            <i class="fa-solid fa-bag-shopping text-slate-300 group-hover:text-cyan-400 text-sm"></i>
                            <span class="cart-badge absolute -top-1.5 -right-1.5 bg-gradient-to-r from-red-500 to-rose-600 text-white font-extrabold text-[10px] px-1.5 py-0.5 rounded-full shadow-md <?= empty($cartCount) ? 'hidden' : '' ?>">
                                <?= $cartCount ?? 0 ?>
                            </span>
                        </a>

                        <!-- Dropdown Menu Tài Khoản User -->
                        <!-- Dropdown Menu Tài Khoản User / Admin -->
                        <div class="relative group">
                            <button type="button" class="flex items-center gap-1.5 sm:gap-2 bg-slate-800/90 hover:bg-slate-700/90 border <?= $isAdmin ? 'border-amber-500/50 shadow-[0_0_12px_rgba(245,158,11,0.25)]' : 'border-slate-700/80' ?> px-2.5 sm:px-3 py-1.5 rounded-xl text-xs font-bold text-slate-200 transition flex-shrink-0">
                                <div class="w-6 h-6 rounded-lg <?= $isAdmin ? 'bg-gradient-to-tr from-amber-500 to-yellow-400 text-slate-950 shadow-sm' : 'bg-gradient-to-tr from-primary-500 to-cyan-400 text-white' ?> flex items-center justify-center text-[11px] font-black flex-shrink-0">
                                    <?= $isAdmin ? '<i class="fa-solid fa-shield text-[10px]"></i>' : mb_substr($currentUser['fullname'] ?? 'U', 0, 1) ?>
                                </div>
                                <span class="hidden sm:inline max-w-[90px] md:max-w-[110px] xl:max-w-[130px] truncate text-slate-200"><?= $isAdmin ? 'Admin' : htmlspecialchars($currentUser['fullname'] ?? 'User') ?></span>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 group-hover:rotate-180 transition flex-shrink-0"></i>
                            </button>

                            <!-- Dropdown Content -->
                            <div class="absolute right-0 top-full hidden group-hover:block w-56 pt-2 z-50">
                                <div class="bg-slate-900 border border-slate-700/80 rounded-2xl shadow-2xl p-2 space-y-1 backdrop-blur-xl">
                                    <div class="px-3 py-2 border-b border-slate-800 text-[11px]">
                                        <div class="font-bold text-white flex items-center justify-between gap-1">
                                            <span class="truncate"><?= htmlspecialchars($currentUser['fullname'] ?? '') ?></span>
                                            <?php if ($isAdmin): ?>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30 flex-shrink-0">Admin</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-slate-400 text-[10px] truncate"><?= htmlspecialchars($currentUser['email'] ?? '') ?></div>
                                    </div>
                                    <a href="<?= BASE_URL ?>index.php?url=auth/orders" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                        <i class="fa-solid fa-box text-cyan-400 mr-2"></i> Đơn hàng của tôi
                                    </a>
                                    <a href="<?= BASE_URL ?>index.php?url=wishlist" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                        <i class="fa-solid fa-heart text-rose-400 mr-2"></i> Danh sách yêu thích
                                    </a>
                                    <a href="<?= BASE_URL ?>index.php?url=auth/profile" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                        <i class="fa-solid fa-id-badge text-primary-400 mr-2"></i> Hồ sơ cá nhân
                                    </a>
                                    <a href="<?= BASE_URL ?>index.php?url=contact" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">
                                        <i class="fa-solid fa-headset text-emerald-400 mr-2"></i> Gửi hỗ trợ CSKH
                                    </a>
                                    <?php if ($isAdmin): ?>
                                        <div class="border-t border-slate-800 my-1"></div>
                                        <a href="<?= BASE_URL ?>index.php?url=admin/statistics" class="block px-3 py-2 rounded-xl text-xs font-semibold text-cyan-400 hover:bg-cyan-950/40 transition">
                                            <i class="fa-solid fa-chart-pie mr-2"></i> Thống kê báo cáo
                                        </a>
                                        <a href="<?= BASE_URL ?>index.php?url=admin" class="block px-3 py-2 rounded-xl text-xs font-semibold text-amber-400 hover:bg-amber-950/40 transition">
                                            <i class="fa-solid fa-gauge mr-2"></i> Quản trị Admin
                                        </a>
                                    <?php endif; ?>
                                    <div class="border-t border-slate-800 my-1"></div>
                                    <a href="<?= BASE_URL ?>index.php?url=auth/logout" class="block px-3 py-2 rounded-xl text-xs font-semibold text-rose-400 hover:bg-rose-950/30 transition">
                                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Đăng xuất
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header>
    </div>

    <script>
    function guestCartAlert() {
        Swal.fire({
            title: 'Quyền Hạn Khách Vãng Lai',
            html: '<div class="text-left text-xs text-slate-300 space-y-2">' +
                  '<p>• <b>Khách</b>: Chỉ được xem giao diện sản phẩm, thông tin cửa hàng và tra cứu đơn hàng.</p>' +
                  '<p>• <b>Không có giỏ hàng & Không đặt hàng</b>: Quý khách cần đăng nhập tài khoản <b>User</b> để thêm giỏ hàng, áp mã giảm giá và mua hàng.</p>' +
                  '</div>',
            icon: 'info',
            background: '#0f172a',
            color: '#f8fafc',
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-right-to-bracket mr-1"></i> Đăng Nhập Ngay',
            cancelButtonText: 'Đóng',
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#334155',
            customClass: {
                popup: 'rounded-3xl border border-slate-700 shadow-2xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= BASE_URL ?>index.php?url=auth/login&msg=login_required_cart';
            }
        });
    }

    // ==========================================
    // AJAX LIVE SEARCH (GỢI Ý TÌM KIẾM NHANH)
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('headerSearchInput');
        const searchClear = document.getElementById('headerSearchClear');
        const searchSpinner = document.getElementById('headerSearchSpinner');
        const searchDropdown = document.getElementById('headerSearchDropdown');
        const searchResults = document.getElementById('headerSearchResults');
        const searchCountBadge = document.getElementById('headerSearchCountBadge');
        const searchFooter = document.getElementById('headerSearchFooter');
        const searchAllBtn = document.getElementById('headerSearchAllBtn');
        const searchBox = document.querySelector('.header-search-box');

        if (!searchInput) return;

        let debounceTimer = null;

        function toggleClearBtn() {
            if (searchInput.value.trim() !== '') {
                searchClear.classList.remove('hidden');
            } else {
                searchClear.classList.add('hidden');
            }
        }

        // Xóa nhanh từ khóa
        if (searchClear) {
            searchClear.addEventListener('click', function(e) {
                e.stopPropagation();
                searchInput.value = '';
                toggleClearBtn();
                searchDropdown.classList.add('hidden');
                searchInput.focus();
            });
        }

        // Gõ phím tìm kiếm
        searchInput.addEventListener('input', function() {
            toggleClearBtn();
            const query = this.value.trim();

            clearTimeout(debounceTimer);

            if (query.length < 2) {
                searchDropdown.classList.add('hidden');
                searchSpinner.classList.add('hidden');
                toggleClearBtn();
                return;
            }

            // Hiển thị icon xoay loading
            searchSpinner.classList.remove('hidden');
            searchClear.classList.add('hidden');

            debounceTimer = setTimeout(() => {
                fetch('<?= BASE_URL ?>index.php?url=products/suggest&q=' + encodeURIComponent(query))
                    .then(res => res.json())
                    .then(res => {
                        searchSpinner.classList.add('hidden');
                        toggleClearBtn();

                        if (!res.success) return;

                        searchResults.innerHTML = '';

                        if (res.data && res.data.length > 0) {
                            searchCountBadge.textContent = res.count + ' sản phẩm';
                            searchCountBadge.classList.remove('hidden');

                            res.data.forEach(item => {
                                const el = document.createElement('a');
                                el.href = item.detail_url;
                                el.className = 'flex items-center gap-3 p-3 hover:bg-slate-800/80 transition group';
                                el.innerHTML = `
                                    <div class="w-12 h-12 rounded-xl bg-slate-950 border border-slate-800 overflow-hidden flex-shrink-0 p-1 flex items-center justify-center">
                                        <img src="${item.image}" alt="${item.name}" class="w-full h-full object-contain group-hover:scale-110 transition duration-300">
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-0.5">
                                        <div class="flex items-center gap-1.5">
                                            ${item.brand_name ? `<span class="text-[9px] font-bold uppercase tracking-wider text-cyan-400 bg-cyan-950/60 border border-cyan-800/60 px-1.5 py-0.2 rounded">${item.brand_name}</span>` : ''}
                                            <span class="text-[10px] text-slate-400 truncate">${item.category_name || ''}</span>
                                        </div>
                                        <h4 class="text-xs font-bold text-slate-200 group-hover:text-cyan-400 transition truncate">${item.name}</h4>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-black text-rose-400">${item.price_formatted}</span>
                                            ${item.old_price_formatted ? `<span class="text-[10px] text-slate-500 line-through">${item.old_price_formatted}</span>` : ''}
                                        </div>
                                    </div>
                                    <div class="text-slate-500 group-hover:text-cyan-400 group-hover:translate-x-0.5 transition text-xs">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </div>
                                `;
                                searchResults.appendChild(el);
                            });

                            searchAllBtn.href = res.all_url;
                            searchAllBtn.innerHTML = `<span>Xem tất cả kết quả cho "<b>${query}</b>"</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>`;
                            searchFooter.classList.remove('hidden');
                        } else {
                            searchCountBadge.classList.add('hidden');
                            searchFooter.classList.add('hidden');
                            searchResults.innerHTML = `
                                <div class="p-6 text-center text-slate-400 space-y-2">
                                    <div class="w-10 h-10 mx-auto rounded-full bg-slate-800/80 flex items-center justify-center text-slate-500">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-300">Không tìm thấy sản phẩm nào</p>
                                    <p class="text-[11px] text-slate-500">Thử tìm kiếm với từ khóa: <b>iPhone, Samsung, Tai nghe...</b></p>
                                </div>
                            `;
                        }

                        searchDropdown.classList.remove('hidden');
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                        searchSpinner.classList.add('hidden');
                        toggleClearBtn();
                    });
            }, 250);
        });

        // Focus lại vào input nếu đã có giá trị thì hiển thị dropdown
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2 && searchResults.children.length > 0) {
                searchDropdown.classList.remove('hidden');
            }
        });

        // Click ra ngoài để đóng dropdown
        document.addEventListener('click', function(e) {
            if (searchBox && !searchBox.contains(e.target)) {
                searchDropdown.classList.add('hidden');
            }
        });
    });
    </script>

    <!-- Main View Container -->
    <main class="relative z-10 flex-grow">
        <!-- Nút Quay lại trang trước toàn cục (Client - Tự động hiển thị trên tất cả các trang con) -->
        <?php 
        $currentClientUrl = trim($_GET['url'] ?? '');
        if (!empty($currentClientUrl) && $currentClientUrl !== 'home'): 
        ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-1 flex items-center justify-between">
            <button type="button" 
                    onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '<?= BASE_URL ?>'; }" 
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-900/90 hover:bg-slate-800 text-slate-200 hover:text-cyan-400 border border-slate-700/80 hover:border-cyan-500/50 text-xs font-semibold backdrop-blur-md shadow-lg transition-all duration-200 group"
                    title="Quay lại trang trước đó">
                <i class="fa-solid fa-arrow-left text-[11px] group-hover:-translate-x-1 transition-transform text-cyan-400"></i>
                <span>Quay lại trang trước</span>
            </button>
            <span class="text-[11px] text-slate-500 hidden sm:inline-flex items-center gap-1.5">
                <i class="fa-solid fa-clock-rotate-left text-[10px]"></i> Điều hướng trang
            </span>
        </div>
        <?php endif; ?>
