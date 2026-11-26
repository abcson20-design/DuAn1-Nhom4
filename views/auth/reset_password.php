<div class="max-w-md mx-auto px-4 py-16">
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-700/80 shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 font-bold mx-auto text-xl">
                <i class="fa-solid fa-key"></i>
            </div>
            <h1 class="text-2xl font-black text-white">Đặt Lại Mật Khẩu Mới</h1>
            <p class="text-xs text-slate-400">Thiết lập mật khẩu an toàn mới cho tài khoản của bạn</p>
        </div>

        <?php if (!empty($errMsg)): ?>
            <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-400 text-base shrink-0"></i>
                <div><?= htmlspecialchars($errMsg) ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($valid)): ?>
            <div class="p-3 bg-slate-900 border border-slate-800 rounded-2xl text-xs text-slate-300 flex items-center gap-2">
                <i class="fa-solid fa-user-shield text-cyan-400 text-sm"></i>
                <div>Đang đặt lại mật khẩu cho: <strong class="text-white"><?= htmlspecialchars($user['email'] ?? '') ?></strong></div>
            </div>

            <form action="<?= BASE_URL ?>index.php?url=auth/reset-password" method="POST" class="space-y-4">
                <?= Csrf::field() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Mật khẩu mới (tối thiểu 6 ký tự)</label>
                    <div class="relative">
                        <input type="password" name="password" id="resetNewPass" required minlength="6" placeholder="••••••••"
                               class="w-full text-xs p-3.5 pr-10 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                        <button type="button" onclick="togglePassVisibility('resetNewPass', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Xác nhận mật khẩu mới</label>
                    <div class="relative">
                        <input type="password" name="confirm_password" id="resetConfirmPass" required minlength="6" placeholder="••••••••"
                               class="w-full text-xs p-3.5 pr-10 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                        <button type="button" onclick="togglePassVisibility('resetConfirmPass', this)" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white font-extrabold text-xs py-3.5 px-4 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Cập Nhật Mật Khẩu Mới</span>
                </button>
            </form>
        <?php else: ?>
            <div class="text-center pt-2">
                <a href="<?= BASE_URL ?>index.php?url=auth/forgot-password" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs transition">
                    <i class="fa-solid fa-rotate-left"></i> Gửi lại yêu cầu khôi phục
                </a>
            </div>
        <?php endif; ?>

        <div class="text-center pt-2 border-t border-slate-800 text-xs">
            <a href="<?= BASE_URL ?>index.php?url=auth/login" class="text-cyan-400 hover:underline">
                &larr; Quay lại trang Đăng nhập
            </a>
        </div>
    </div>
</div>

<script>
function togglePassVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const icon = btn.querySelector('i');
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
