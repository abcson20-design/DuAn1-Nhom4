<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-white">Hồ Sơ &amp; Bảo Mật Tài Khoản</h1>
        <p class="text-xs text-slate-400 mt-1">Quản lý thông tin cá nhân, địa chỉ giao hàng và thay đổi mật khẩu của bạn tại NEXVORA</p>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
            <div><?= $msg ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($errMsg)): ?>
        <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-rose-400 text-base"></i>
            <div><?= htmlspecialchars($errMsg) ?></div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
        <!-- 1. THÔNG TIN HỒ SƠ -->
        <div class="glass-card rounded-3xl p-6 sm:p-7 border border-slate-700/80 space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-800">
                <div class="w-9 h-9 rounded-xl bg-cyan-500/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 font-bold">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Thông Tin Cá Nhân</h3>
                    <p class="text-[11px] text-slate-400">Cập nhật họ tên, SĐT và địa chỉ</p>
                </div>
            </div>

            <form action="<?= BASE_URL ?>index.php?url=auth/profile" method="POST" class="space-y-4">
                <?= Csrf::field() ?>
                <input type="hidden" name="action_type" value="update_profile">

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email (Cố định)</label>
                    <input type="email" disabled value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                           class="w-full text-xs p-3 rounded-xl border border-slate-800 bg-slate-950/50 text-slate-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Họ và tên <span class="text-rose-400">*</span></label>
                    <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required 
                           class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Số điện thoại liên hệ</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="09xxxxxxxx"
                           class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Địa chỉ nhận hàng mặc định</label>
                    <textarea name="address" rows="3" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành..." class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:border-cyan-500/50 text-white font-bold text-xs py-3 px-4 rounded-xl shadow transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-cyan-400"></i>
                        <span>Lưu Thay Đổi Hồ Sơ</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. ĐỔI MẬT KHẨU TÀI KHOẢN -->
        <div class="glass-card rounded-3xl p-6 sm:p-7 border border-slate-700/80 space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-800">
                <div class="w-9 h-9 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 font-bold">
                    <i class="fa-solid fa-key"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Đổi Mật Khẩu</h3>
                    <p class="text-[11px] text-slate-400">Bảo vệ tài khoản bằng mật khẩu an toàn</p>
                </div>
            </div>

            <form action="<?= BASE_URL ?>index.php?url=auth/profile" method="POST" class="space-y-4">
                <?= Csrf::field() ?>
                <input type="hidden" name="action_type" value="change_password">

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Mật khẩu hiện tại <span class="text-rose-400">*</span></label>
                    <input type="password" name="old_password" required placeholder="Nhập mật khẩu đang dùng"
                           class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Mật khẩu mới <span class="text-rose-400">*</span></label>
                    <input type="password" name="new_password" required placeholder="Ít nhất 6 ký tự, có ký tự đặc biệt"
                           class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                    <p class="text-[10px] text-slate-400 mt-1">Yêu cầu: Từ 6 ký tự trở lên và chứa ít nhất 1 ký tự đặc biệt (!, @, #, $, %...).</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Xác nhận mật khẩu mới <span class="text-rose-400">*</span></label>
                    <input type="password" name="confirm_password" required placeholder="Nhập lại mật khẩu mới"
                           class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white font-extrabold text-xs py-3 px-4 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Cập Nhật Mật Khẩu Mới</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
