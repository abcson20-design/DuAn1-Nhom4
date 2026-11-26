<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

    <!-- Header Title -->
    <div class="text-center max-w-2xl mx-auto space-y-3">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-bold tracking-wide uppercase">
            <i class="fa-solid fa-headset animate-pulse"></i> Trung Tâm Hỗ Trợ 24/7
        </div>
        <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
            Liên Hệ Với <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 to-cyan-400">NEXVORA</span>
        </h1>
        <p class="text-xs sm:text-sm text-slate-400">
            Chúng tôi luôn sẵn sàng lắng nghe mọi ý kiến đóng góp, phản hồi dịch vụ và giải đáp thắc mắc bảo hành của quý khách.
        </p>
    </div>

    <!-- Thông báo kết quả gửi liên hệ -->
    <?php if (!empty($success)): ?>
        <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 text-xs sm:text-sm flex items-center justify-between shadow-lg shadow-emerald-950/40">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs sm:text-sm flex items-center justify-between shadow-lg shadow-rose-950/40">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-400 text-lg"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Cột Trái (5 Cột): Thông tin liên hệ & Showroom -->
        <div class="lg:col-span-5 space-y-6">
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6 border border-slate-700/80">
                <h3 class="text-base font-extrabold text-white flex items-center gap-2 border-b border-slate-800 pb-4">
                    <i class="fa-solid fa-building text-primary-400"></i> Thông Tin Trụ Sở & Showroom
                </h3>

                <div class="space-y-5 text-xs text-slate-300">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-primary-400 flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <span class="block text-white font-bold mb-0.5">Địa chỉ Showroom chính:</span>
                            <span class="text-slate-400 leading-relaxed"><?= SHOP_ADDRESS ?></span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-phone-volume"></i>
                        </div>
                        <div>
                            <span class="block text-white font-bold mb-0.5">Hotline tư vấn & Bán hàng:</span>
                            <span class="text-cyan-400 font-bold"><?= SHOP_HOTLINE ?></span>
                            <span class="block text-[11px] text-slate-500">Cước gọi miễn phí 100%</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-purple-400 flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <span class="block text-white font-bold mb-0.5">Email tiếp nhận hỗ trợ:</span>
                            <span class="text-slate-400"><?= SHOP_EMAIL ?></span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <span class="block text-white font-bold mb-0.5">Thời gian phục vụ:</span>
                            <span class="text-slate-400">08:00 - 22:00 (Từ Thứ Hai đến Chủ Nhật, kể cả ngày Lễ)</span>
                        </div>
                    </div>
                </div>

                <!-- Google Map Preview -->
                <div class="pt-4 border-t border-slate-800">
                    <div class="rounded-2xl overflow-hidden border border-slate-800 h-48 bg-slate-950 relative">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.86393118206!2d105.74468687530145!3d21.038129780613822!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313455e940879933%3A0xcf10b34e9f1a03df!2zVHLGsOG7nW5nIENhbyDEkeG6s25nIEZQVCBQb2x5dGVjaG5pYw!5e0!3m2!1svi!2sus!4v1793944289540!5m2!1svi!2sus" 
                            width="100%" height="100%" style="border:0; filter: invert(90%) hue-rotate(180deg);" allowfullscreen="" loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột Phải (7 Cột): Form liên hệ (Phân quyền Khách vs User) -->
        <div class="lg:col-span-7">
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6 border border-slate-700/80 relative overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane text-cyan-400"></i> Gửi Yêu Cầu Hỗ Trợ Trực Tuyến
                    </h3>
                    <?php if ($isGuest): ?>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30 flex items-center gap-1.5">
                            <i class="fa-solid fa-user-lock"></i> Quyền Khách
                        </span>
                    <?php else: ?>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check"></i> Đã Đăng Nhập User
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ($isGuest): ?>
                    <!-- CẢNH BÁO PHÂN QUYỀN DÀNH CHO KHÁCH -->
                    <div class="p-5 rounded-2xl bg-gradient-to-r from-amber-950/40 via-slate-900 to-indigo-950/40 border border-amber-500/30 space-y-3">
                        <div class="flex items-center gap-3 text-amber-400 font-bold text-sm">
                            <i class="fa-solid fa-shield-halved text-lg"></i>
                            <span>Phân quyền bảo mật tài khoản: Khách chỉ được xem thông tin</span>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Để bảo vệ an toàn thông tin và tránh thư rác tự động, chức năng gửi liên hệ, khiếu nại và yêu cầu bảo hành chỉ mở cho thành viên **User đã đăng nhập**. Quý khách vui lòng đăng nhập hoặc tạo tài khoản miễn phí để gửi thông tin.
                        </p>
                        <div class="flex flex-wrap gap-3 pt-2">
                            <a href="<?= BASE_URL ?>index.php?url=auth/login&msg=login_required_contact&redirect=index.php?url=contact" 
                               class="bg-gradient-to-r from-primary-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white font-extrabold text-xs py-2.5 px-5 rounded-xl shadow-lg shadow-primary-500/25 transition flex items-center gap-2">
                                <i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập Ngay
                            </a>
                            <a href="<?= BASE_URL ?>index.php?url=auth/register" 
                               class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs py-2.5 px-5 rounded-xl transition flex items-center gap-2">
                                <i class="fa-solid fa-user-plus"></i> Tạo Tài Khoản User
                            </a>
                        </div>
                    </div>

                    <!-- Form xem trước bị khóa (Disabled) cho khách -->
                    <div class="space-y-4 opacity-50 pointer-events-none select-none">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1">Họ và tên</label>
                                <input type="text" disabled value="Quý khách vãng lai" class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-400">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1">Số điện thoại</label>
                                <input type="text" disabled placeholder="Vui lòng đăng nhập" class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-400">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Tiêu đề yêu cầu</label>
                            <input type="text" disabled placeholder="Ví dụ: Cần tư vấn tai nghe Bluetooth ANC..." class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Nội dung chi tiết</label>
                            <textarea rows="4" disabled placeholder="Nội dung cần liên hệ..." class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-400"></textarea>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- FORM DÀNH CHO USER ĐÃ ĐĂNG NHẬP -->
                    <form action="<?= BASE_URL ?>index.php?url=contact/send" method="POST" class="space-y-4">
                        <?= csrf_field() ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1">Họ và tên <span class="text-red-400">*</span></label>
                                <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required 
                                       class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1">Email phản hồi <span class="text-red-400">*</span></label>
                                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required 
                                       class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1">Số điện thoại liên hệ</label>
                                <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="0988.888.888" 
                                       class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1">Chủ đề cần hỗ trợ <span class="text-red-400">*</span></label>
                                <select name="subject" required class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400 transition">
                                    <option value="Tư vấn mua smartphone / phụ kiện">Tư vấn chọn mua điện thoại / phụ kiện</option>
                                    <option value="Hỗ trợ bảo hành & kỹ thuật">Hỗ trợ bảo hành & kỹ thuật thiết bị</option>
                                    <option value="Thắc mắc về đơn hàng & thanh toán">Thắc mắc về đơn hàng & thanh toán VietQR</option>
                                    <option value="Đóng góp ý kiến dịch vụ">Đóng góp ý kiến / Khiếu nại dịch vụ</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Nội dung chi tiết <span class="text-red-400">*</span></label>
                            <textarea name="message" rows="4" required placeholder="Vui lòng mô tả chi tiết yêu cầu hoặc vấn đề của bạn..." 
                                      class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400 transition"></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full bg-gradient-to-r from-primary-600 via-indigo-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white font-extrabold text-xs py-3.5 px-6 rounded-xl shadow-lg shadow-primary-500/30 transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i> Gửi Tin Nhắn Tới Ban Quản Trị
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
