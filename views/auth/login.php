<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 glass-card rounded-3xl p-8 sm:p-10 border border-slate-700/80 shadow-2xl relative">
        <!-- Nút Quay lại góc trên Card -->
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
                    <span class="block text-[8.5px] uppercase tracking-widest text-cyan-400 font-extrabold mt-0.5">Connect To The Future</span>
                </div>
            </div>
            <h2 class="text-xl font-bold text-slate-200 tracking-tight">Đăng Nhập Tài Khoản</h2>
            <p class="text-xs text-slate-400">Trải nghiệm mua sắm, quản lý giỏ hàng & đặt hàng tại NEXVORA</p>
        </div>

        <?php if (!empty($notice)): ?>
            <div class="p-4 rounded-2xl bg-amber-950/60 border border-amber-500/40 text-amber-300 text-xs flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-amber-400 text-sm flex-shrink-0"></i>
                <span><?= htmlspecialchars($notice) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm flex-shrink-0"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Form Đăng nhập -->
        <form class="mt-6 space-y-5" action="<?= BASE_URL ?>index.php?url=auth/login<?= !empty($redirectUrl) ? '&redirect=' . urlencode($redirectUrl) : '' ?>" method="POST">
            <?= Csrf::field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Địa chỉ Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 text-xs">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="inputEmail" required 
                           class="w-full pl-9 pr-3 py-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500 transition" 
                           placeholder="duan1@gmail.com">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold text-slate-300">Mật khẩu</label>
                    <a href="<?= BASE_URL ?>index.php?url=auth/forgot-password" class="text-[11px] text-cyan-400 hover:underline">Quên mật khẩu?</a>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 text-xs">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="inputPass" required 
                           class="w-full pl-9 pr-10 py-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500 transition" 
                           placeholder="••••••••">
                    <button type="button" onclick="togglePassword('inputPass', 'eyeIcon')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-cyan-400 focus:outline-none transition" title="Hiện/Ẩn mật khẩu">
                        <i class="fa-solid fa-eye text-xs" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-primary-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white font-extrabold text-xs py-3.5 px-4 rounded-xl shadow-lg shadow-primary-500/30 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập
            </button>
        </form>

        <div class="text-center text-xs text-slate-400 pt-4 border-t border-slate-800">
            Chưa có tài khoản User? 
            <a href="<?= BASE_URL ?>index.php?url=auth/register" class="text-cyan-400 font-bold hover:underline">Đăng ký ngay</a>
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
