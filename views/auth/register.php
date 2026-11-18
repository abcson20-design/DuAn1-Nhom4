<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6 glass-card rounded-3xl p-8 sm:p-10 border border-slate-700/80 shadow-2xl relative">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 bg-gradient-to-tr from-cyan-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-black mx-auto shadow-lg shadow-cyan-500/30">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight">Đăng Ký Tài Khoản User</h2>
            <p class="text-xs text-slate-400">Trở thành thành viên NEXVORA để mua sắm, nhận ưu đãi và bảo hành</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm flex-shrink-0"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Form Đăng ký -->
        <form class="space-y-4" action="<?= BASE_URL ?>index.php?url=auth/register" method="POST">
            <?= Csrf::field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Họ và tên <span class="text-red-400">*</span></label>
                <input type="text" name="fullname" required 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500" 
                       placeholder="Ví dụ: Nguyễn Văn An">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email <span class="text-red-400">*</span></label>
                    <input type="email" name="email" required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500" 
                           placeholder="user@gmail.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Số điện thoại <span class="text-red-400">*</span></label>
                    <input type="tel" name="phone" required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500" 
                           placeholder="0988.888.888">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Địa chỉ nhận hàng mặc định</label>
                <input type="text" name="address" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500" 
                       placeholder="Số nhà, đường, phường/xã, quận/huyện...">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Mật khẩu <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="password" name="password" id="regPass" required 
                               class="w-full pl-3.5 pr-9 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500" 
                               placeholder="Từ 6 ký tự, có ký tự đặc biệt (@, #, $...)">
                        <button type="button" onclick="togglePassword('regPass', 'regEyeIcon1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-cyan-400 focus:outline-none transition" title="Hiện/Ẩn mật khẩu">
                            <i class="fa-solid fa-eye text-xs" id="regEyeIcon1"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nhập lại mật khẩu <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="password" name="password_confirm" id="regPassConfirm" required 
                               class="w-full pl-3.5 pr-9 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500" 
                               placeholder="Khớp với mật khẩu">
                        <button type="button" onclick="togglePassword('regPassConfirm', 'regEyeIcon2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-cyan-400 focus:outline-none transition" title="Hiện/Ẩn mật khẩu">
                            <i class="fa-solid fa-eye text-xs" id="regEyeIcon2"></i>
                        </button>
                    </div>
                </div>
            </div>
            <p class="text-[11px] text-amber-400 flex items-center gap-1.5 bg-amber-950/30 border border-amber-500/20 px-3 py-1.5 rounded-lg">
                <i class="fa-solid fa-shield-halved text-amber-400"></i>
                <span>Yêu cầu mật khẩu: Tối thiểu 6 ký tự và bắt buộc phải có ít nhất 1 ký tự đặc biệt (ví dụ: <b>@</b>, <b>#</b>, <b>$</b>, <b>!</b>).</span>
            </p>

            <div class="pt-2">
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white font-extrabold text-xs py-3 px-4 rounded-xl shadow-lg shadow-cyan-500/25 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-check"></i> Hoàn Tất Đăng Ký Tài Khoản
                </button>
            </div>
        </form>

        <div class="text-center text-xs text-slate-400 pt-2 border-t border-slate-800">
            Đã có tài khoản User? 
            <a href="<?= BASE_URL ?>index.php?url=auth/login" class="text-cyan-400 font-bold hover:underline">Đăng nhập ngay</a>
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
