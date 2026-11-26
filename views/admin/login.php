<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản Trị - NEXVORA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-b from-[#0b172e] via-[#0f2347] to-[#0a162c] text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-x-hidden selection:bg-primary-600 selection:text-white">
    <?php require_once __DIR__ . '/partials/bg_effects.php'; ?>
    <div class="w-full max-w-md relative z-10">
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-xl space-y-6">
            <!-- Nút Quay lại -->
            <div>
                <a href="javascript:if(window.history.length>1){window.history.back()}else{window.location.href='<?= BASE_URL ?>'}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-cyan-400 font-semibold transition group">
                    <i class="fa-solid fa-arrow-left text-[11px] group-hover:-translate-x-1 transition-transform"></i>
                    <span>Quay lại trang trước</span>
                </a>
            </div>
            <div class="text-center space-y-3">
                <div class="inline-flex items-center justify-center space-x-3 mb-1">
                    <img src="<?= BASE_URL ?>public/images/logo.png" alt="NEXVORA N Emblem" class="h-12 w-auto object-contain drop-shadow-[0_0_15px_rgba(6,182,212,0.45)]">
                    <div class="text-left">
                        <span class="text-2xl font-black tracking-tight text-white leading-none">NEX<span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-indigo-300 to-purple-400">VORA</span></span>
                        <span class="block text-[8px] uppercase tracking-widest text-cyan-400 font-extrabold mt-0.5">Admin Portal</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400">Connect To The Future • Hệ thống quản trị bán hàng</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="p-3 bg-red-950/60 border border-red-500/40 text-red-300 text-xs rounded-xl flex items-center">
                    <i class="fa-solid fa-circle-exclamation mr-2 text-sm"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>index.php?url=admin/login" method="POST" class="space-y-4">
                <?= Csrf::field() ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email quản trị</label>
                    <input type="email" name="email" placeholder="duan1@gmail.com" required class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Mật khẩu</label>
                    <div class="relative">
                        <input type="password" name="password" id="adminPass" placeholder="••••••••" required class="w-full text-xs pl-3 pr-10 py-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                        <button type="button" onclick="togglePassword('adminPass', 'adminEye')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-cyan-400 focus:outline-none transition" title="Hiện/Ẩn mật khẩu">
                            <i class="fa-solid fa-eye text-xs" id="adminEye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white font-extrabold text-xs py-3.5 rounded-xl shadow-lg transition">
                    <i class="fa-solid fa-right-to-bracket mr-1.5"></i> Đăng Nhập Hệ Thống
                </button>
            </form>

            <div class="text-center pt-2">
                <a href="<?= BASE_URL ?>" class="text-xs text-slate-400 hover:text-cyan-400 transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>

    <script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>
