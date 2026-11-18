<div class="max-w-md mx-auto px-4 py-16">
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-700/80 shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 font-bold mx-auto text-xl">
                <i class="fa-solid fa-unlock-keyhole"></i>
            </div>
            <h1 class="text-2xl font-black text-white">Khôi Phục Mật Khẩu</h1>
            <p class="text-xs text-slate-400">Nhập email đã đăng ký của bạn để nhận mật khẩu tạm thời</p>
        </div>

        <?php if (!empty($msg)): ?>
            <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 text-xs space-y-2">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
                    <span class="font-bold">Khởi tạo mật khẩu mới thành công!</span>
                </div>
                <div><?= $msg ?></div>
                <div class="pt-2">
                    <a href="<?= BASE_URL ?>index.php?url=auth/login" class="inline-block bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold px-4 py-2 rounded-xl text-xs transition">
                        Đăng Nhập Ngay &rarr;
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($errMsg)): ?>
            <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-400 text-base"></i>
                <div><?= htmlspecialchars($errMsg) ?></div>
            </div>
        <?php endif; ?>

        <?php if (empty($msg)): ?>
            <form action="<?= BASE_URL ?>index.php?url=auth/forgot-password" method="POST" class="space-y-4">
                <?= Csrf::field() ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email đăng ký tài khoản</label>
                    <input type="email" name="email" required placeholder="nhap-email@example.com"
                           class="w-full text-xs p-3.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white font-extrabold text-xs py-3.5 px-4 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Khôi Phục Mật Khẩu</span>
                </button>
            </form>
        <?php endif; ?>

        <div class="text-center pt-2 border-t border-slate-800 flex justify-between text-xs">
            <a href="<?= BASE_URL ?>index.php?url=auth/login" class="text-cyan-400 hover:underline">
                &larr; Quay lại Đăng nhập
            </a>
            <a href="<?= BASE_URL ?>index.php?url=auth/register" class="text-slate-400 hover:text-white">
                Tạo tài khoản mới
            </a>
        </div>
    </div>
</div>
