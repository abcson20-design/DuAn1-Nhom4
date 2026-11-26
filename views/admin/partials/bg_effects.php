<!-- Hiệu ứng Nền & Các Hình Khối Phát Sáng Bay Qua Lại Dành Cho Admin TechZone -->
<style>
    .tech-grid-pattern {
        background-size: 40px 40px;
        background-image: 
            linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
    }
    @keyframes blobSlow {
        0%, 100% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(45px, -50px) scale(1.1); }
        66% { transform: translate(-35px, 35px) scale(0.92); }
    }
    @keyframes blobReverse {
        0%, 100% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(-45px, 45px) scale(1.12); }
        66% { transform: translate(35px, -35px) scale(0.94); }
    }
    .animate-blob-slow { animation: blobSlow 14s infinite ease-in-out; }
    .animate-blob-reverse { animation: blobReverse 16s infinite ease-in-out; }
</style>

<!-- Lớp Nền Ambient & Các Hình Khối Bay (Canvas) -->
<div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
    <!-- Aurora Glow Blobs (Làm sáng không gian nền) -->
    <div class="absolute -top-24 -left-24 w-[36rem] h-[36rem] bg-indigo-500/25 rounded-full blur-[110px] animate-blob-slow"></div>
    <div class="absolute top-1/6 right-0 w-[38rem] h-[38rem] bg-cyan-500/25 rounded-full blur-[130px] animate-blob-reverse"></div>
    <div class="absolute top-1/2 left-1/3 w-[34rem] h-[34rem] bg-purple-600/25 rounded-full blur-[125px] animate-blob-slow"></div>
    <div class="absolute -bottom-20 right-1/4 w-[40rem] h-[40rem] bg-blue-600/25 rounded-full blur-[140px]"></div>

    <!-- Tech Grid Pattern -->
    <div class="absolute inset-0 tech-grid-pattern opacity-70"></div>

    <!-- Radial Center Highlight -->
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_50%_-10%,rgba(56,189,248,0.2),transparent_70%)]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_45%,rgba(99,102,241,0.12),transparent_75%)]"></div>

    <!-- Canvas: Các hình vuông, tam giác, tròn, thoi phát sáng bay qua lại -->
    <canvas id="adminGeometricCanvas" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>
</div>

<script>
(function() {
    const canvas = document.getElementById('adminGeometricCanvas');
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
        const size = Math.random() * 24 + 16;
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
            glow: Math.random() * 12 + 18,
            fillAlpha: Math.random() * 0.12 + 0.08,
            pulse: Math.random() * Math.PI * 2,
            pulseSpeed: Math.random() * 0.03 + 0.015,
            bounce: Math.random() > 0.35 // 65% dội qua lại giữa các cạnh
        };
    }

    // 48 hình khối phát sáng bay khắp toàn màn hình
    const count = Math.min(55, Math.max(32, Math.floor(window.innerWidth / 28)));
    for (let i = 0; i < count; i++) {
        shapes.push(createShape());
    }

    function drawShape(s) {
        ctx.save();
        ctx.translate(s.x, s.y);
        ctx.rotate(s.angle);

        let currentGlow = s.glow + Math.sin(s.pulse) * 6;
        let strokeColor = s.color;
        let strokeWidth = 2.2;

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

            // Tương tác đẩy nhẹ khi chuột tới gần
            const dx = s.x - mouse.x;
            const dy = s.y - mouse.y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < mouse.radius && dist > 0) {
                const force = (1 - dist / mouse.radius) * 1.8;
                s.x += (dx / dist) * force;
                s.y += (dy / dist) * force;
            }

            s.x += s.vx;
            s.y += s.vy;
            s.angle += s.vRot;
            s.pulse += s.pulseSpeed;

            // Va chạm & bay qua lại trên toàn màn hình
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

<!-- Nút Quay lại nổi cố định góc dưới bên trái (Admin) -->
<button type="button" 
        onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '<?= BASE_URL ?>index.php?url=admin'; }" 
        id="adminFloatingBackBtn"
        class="fixed bottom-6 left-6 z-50 inline-flex items-center gap-2 px-3.5 py-2.5 rounded-2xl bg-slate-900/95 hover:bg-slate-800 text-slate-200 hover:text-cyan-400 border border-cyan-500/40 hover:border-cyan-400 shadow-2xl shadow-cyan-950/60 text-xs font-bold backdrop-blur-xl transition-all duration-300 hover:scale-105 active:scale-95 group" 
        title="Quay lại trang trước đó">
    <span class="w-6 h-6 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center group-hover:-translate-x-0.5 transition-transform">
        <i class="fa-solid fa-arrow-left text-xs"></i>
    </span>
    <span class="hidden sm:inline">Quay lại trang trước</span>
</button>

<!-- Universal Real-time Form Validator (Thông báo lỗi đỏ tức thì bên dưới ô nhập cho Admin) -->
<script src="<?= BASE_URL ?>public/js/form-validator.js?v=<?= @filemtime(__DIR__ . '/../../../public/js/form-validator.js') ?: time() ?>"></script>
