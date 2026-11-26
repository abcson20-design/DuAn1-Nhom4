<div class="space-y-16 pb-20">
    <!-- Hero Banner with 3D Float & Ambient Glow (TechZone Style) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <div class="relative rounded-3xl overflow-hidden border border-slate-700/60 bg-gradient-to-br from-slate-900/90 via-indigo-950/80 to-slate-900/90 p-8 md:p-14 shadow-2xl backdrop-blur-2xl">
            <!-- Decorative Backlight -->
            <div class="absolute top-0 right-1/4 w-72 h-72 bg-primary-500/20 rounded-full blur-[90px] pointer-events-none"></div>
            <div class="absolute bottom-0 right-10 w-80 h-80 bg-cyan-500/20 rounded-full blur-[100px] pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-10">
                <div class="max-w-xl space-y-6 text-center md:text-left">
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-primary-500/20 text-cyan-300 border border-primary-500/30 backdrop-blur-md animate-pulse">
                        <i class="fa-solid fa-microchip mr-2 text-cyan-400"></i> KỶ NGUYÊN CÔNG NGHỆ 2026
                    </span>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-white leading-tight">
                        Chinh Phục <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-indigo-300 to-primary-400">Đỉnh Cao Hiệu Năng</span>
                    </h1>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                        Hệ thống phân phối điện thoại flagship Apple, Samsung, Xiaomi, OPPO chính hãng cùng hệ sinh thái tai nghe chống ồn ANC & phụ kiện sạc nhanh GaN cao cấp.
                    </p>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 pt-2">
                        <a href="<?= BASE_URL ?>index.php?url=phones" class="relative group bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-500 hover:to-indigo-500 text-white font-bold px-7 py-3.5 rounded-2xl shadow-xl shadow-primary-600/30 transition transform hover:-translate-y-1 text-xs">
                            <span class="flex items-center">
                                <i class="fa-solid fa-mobile-screen mr-2"></i> Xem Điện Thoại <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition duration-300"></i>
                            </span>
                        </a>
                        <a href="<?= BASE_URL ?>index.php?url=accessories" class="bg-slate-800/80 hover:bg-slate-700/80 text-slate-200 border border-slate-700 font-semibold px-6 py-3.5 rounded-2xl backdrop-blur-md transition hover:-translate-y-0.5 text-xs">
                            <i class="fa-solid fa-headphones text-cyan-400 mr-2"></i> Xem Phụ Kiện
                        </a>
                    </div>
                </div>

                <!-- Floating 3D Device Showcase with Ambient Halo -->
                <div class="relative mt-4 md:mt-0 flex justify-center">
                    <div class="w-72 sm:w-80 md:w-96 relative animate-float">
                        <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500 to-indigo-600 rounded-3xl blur-2xl opacity-40 transform rotate-6"></div>
                        <img src="https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=700&auto=format&fit=crop" alt="iPhone 15 Pro Max Titan" class="relative z-10 w-full rounded-3xl shadow-2xl border border-slate-700/80 object-cover transform hover:scale-105 transition duration-500">

                        <!-- Mini floating badge (Chính Hãng 100%) -->
                        <div class="absolute bottom-4 -left-2 sm:bottom-6 sm:-left-4 z-20 bg-slate-900/95 border border-slate-700/90 py-2.5 px-4 rounded-2xl shadow-2xl backdrop-blur-xl flex items-center space-x-3 hover:scale-105 transition duration-300">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl shadow-[0_0_15px_rgba(52,211,153,0.35)] flex-shrink-0">
                                <i class="fa-solid fa-shield-halved text-emerald-400"></i>
                            </div>
                            <div class="whitespace-nowrap">
                                <p class="text-[10px] text-slate-400 font-medium">Cam kết</p>
                                <p class="text-xs font-bold text-white tracking-wide">Chính Hãng 100%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tech Service Benefits Grid -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="glass-card p-5 rounded-2xl flex items-center space-x-4 hover:-translate-y-1 transition duration-300">
                <div class="w-12 h-12 rounded-xl bg-blue-500/15 text-blue-400 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-100 text-xs sm:text-sm">Giao Hỏa Tốc</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Freeship từ 500K</p>
                </div>
            </div>
            <div class="glass-card p-5 rounded-2xl flex items-center space-x-4 hover:-translate-y-1 transition duration-300">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-100 text-xs sm:text-sm">Mở Hộp Test Thử</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Hài lòng mới trả tiền</p>
                </div>
            </div>
            <div class="glass-card p-5 rounded-2xl flex items-center space-x-4 hover:-translate-y-1 transition duration-300">
                <div class="w-12 h-12 rounded-xl bg-purple-500/15 text-purple-400 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-rotate-left"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-100 text-xs sm:text-sm">Đổi Trả 30 Ngày</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">1 đổi 1 tận nhà</p>
                </div>
            </div>
            <div class="glass-card p-5 rounded-2xl flex items-center space-x-4 hover:-translate-y-1 transition duration-300">
                <div class="w-12 h-12 rounded-xl bg-cyan-500/15 text-cyan-400 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-100 text-xs sm:text-sm">VietQR 24/7</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Quét mã ngân hàng tự động</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 1: CÁC HÃNG ĐIỆN THOẠI NỔI BẬT -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-8">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-cyan-400">Smartphone Flagship</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white mt-1">Điện Thoại Chính Hãng Các Hãng</h2>
            </div>
            <a href="<?= BASE_URL ?>index.php?url=phones" class="text-xs sm:text-sm font-bold text-primary-400 hover:text-cyan-300 transition flex items-center">
                Xem toàn bộ điện thoại <i class="fa-solid fa-angle-right ml-1"></i>
            </a>
        </div>

        <!-- Phone Brands Bar -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <a href="<?= BASE_URL ?>index.php?url=phones&brand=1" class="glass-card p-4 rounded-2xl flex items-center space-x-3 hover:border-cyan-400 transition group">
                <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-white text-lg group-hover:scale-110 transition">
                    <i class="fa-brands fa-apple"></i>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-200 group-hover:text-cyan-400 transition">Apple (iPhone)</h4>
                    <span class="text-[10px] text-slate-500">iPhone 15 Series</span>
                </div>
            </a>
            <a href="<?= BASE_URL ?>index.php?url=phones&brand=2" class="glass-card p-4 rounded-2xl flex items-center space-x-3 hover:border-cyan-400 transition group">
                <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-blue-400 text-lg group-hover:scale-110 transition">
                    <i class="fa-solid fa-gem"></i>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-200 group-hover:text-cyan-400 transition">Samsung Galaxy</h4>
                    <span class="text-[10px] text-slate-500">Galaxy AI Series</span>
                </div>
            </a>
            <a href="<?= BASE_URL ?>index.php?url=phones&brand=3" class="glass-card p-4 rounded-2xl flex items-center space-x-3 hover:border-cyan-400 transition group">
                <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-amber-400 text-lg group-hover:scale-110 transition">
                    <i class="fa-solid fa-fire"></i>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-200 group-hover:text-cyan-400 transition">Xiaomi Flagship</h4>
                    <span class="text-[10px] text-slate-500">Camera Leica</span>
                </div>
            </a>
            <a href="<?= BASE_URL ?>index.php?url=phones&brand=4" class="glass-card p-4 rounded-2xl flex items-center space-x-3 hover:border-cyan-400 transition group">
                <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-emerald-400 text-lg group-hover:scale-110 transition">
                    <i class="fa-solid fa-camera"></i>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-slate-200 group-hover:text-cyan-400 transition">OPPO Series</h4>
                    <span class="text-[10px] text-slate-500">Hasselblad Zoom</span>
                </div>
            </a>
        </div>

        <!-- Phone Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($phones as $prod): ?>
                <div class="glass-card rounded-2xl overflow-hidden hover:-translate-y-2 hover:border-cyan-400/50 hover:shadow-2xl hover:shadow-cyan-500/10 transition duration-300 flex flex-col justify-between">
                    <div class="product-thumb-box relative bg-slate-950/60 p-5 flex items-center justify-center cursor-pointer group/thumb select-none overflow-hidden rounded-t-2xl">
                        <?php $isW = !empty($userWishlistIds) && in_array($prod['id'], $userWishlistIds); ?>
                        <!-- Nút Thêm vào Danh sách yêu thích -->
                        <button type="button" 
                                onclick="toggleWishlist(<?= $prod['id'] ?>, this, event)" 
                                data-wishlist-pid="<?= $prod['id'] ?>"
                                class="btn-wishlist-toggle absolute top-3 right-3 z-30 w-8 h-8 rounded-full <?= $isW ? 'bg-rose-500/20 border-rose-500/50 text-rose-400' : 'bg-slate-900/80 hover:bg-rose-500/20 border-slate-700/80 hover:border-rose-500/40 text-slate-400 hover:text-rose-400' ?> border flex items-center justify-center transition shadow-lg group/wishlist"
                                title="<?= $isW ? 'Bỏ thích sản phẩm này' : 'Thêm vào danh sách yêu thích' ?>">
                            <i class="fa-<?= $isW ? 'solid text-rose-500' : 'regular text-slate-300 group-hover/wishlist:text-white' ?> fa-heart text-xs"></i>
                        </button>
                        <?php if (!empty($prod['old_price']) && $prod['old_price'] > $prod['price']): ?>
                            <?php $percent = round((($prod['old_price'] - $prod['price']) / $prod['old_price']) * 100); ?>
                            <span class="absolute top-3 left-3 z-10 bg-red-500/20 text-red-400 border border-red-500/40 text-[10px] font-black px-2 py-0.5 rounded-md">-<?= $percent ?>%</span>
                        <?php endif; ?>
                        <span class="absolute bottom-3 left-3 z-10 bg-slate-900/80 border border-slate-700/80 text-[10px] font-medium text-slate-300 px-2 py-0.5 rounded-md">
                            <?= htmlspecialchars($prod['brand_name'] ?? 'Flagship') ?>
                        </span>
                        <div class="w-full flex items-center justify-center overflow-hidden rounded-xl">
                            <img src="<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="h-44 object-contain transition-transform duration-500 group-hover/thumb:scale-105 pointer-events-none">
                        </div>

                        <!-- Lớp phủ nút Xem chi tiết khi nhấn hoặc rê chuột vào ảnh -->
                        <div class="product-detail-overlay absolute inset-0 bg-slate-950/80 backdrop-blur-sm opacity-0 pointer-events-none group-hover/thumb:opacity-100 group-hover/thumb:pointer-events-auto transition-all duration-300 flex flex-col items-center justify-center p-3 z-20">
                            <a href="<?= BASE_URL ?>index.php?url=products/detail&slug=<?= urlencode($prod['slug']) ?>" 
                               class="btn-detail-link px-4 py-2 rounded-xl bg-gradient-to-r from-primary-600 via-indigo-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white font-bold text-xs shadow-xl shadow-cyan-500/20 border border-cyan-400/50 flex items-center gap-2 transform translate-y-2 group-hover/thumb:translate-y-0 hover:scale-105 active:scale-95 transition-all duration-300 pointer-events-auto"
                               title="Xem chi tiết sản phẩm">
                                <i class="fa-solid fa-eye text-cyan-200"></i>
                                <span>Xem chi tiết</span>
                            </a>
                            <span class="text-[10px] text-cyan-300/80 mt-2 flex items-center gap-1 font-medium">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i> Nhấn để mở chi tiết
                            </span>
                        </div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] text-cyan-400 font-bold uppercase mb-1"><?= htmlspecialchars($prod['compatible_device'] ?? 'Chính hãng') ?></div>
                            <a href="<?= BASE_URL ?>index.php?url=products/detail&slug=<?= urlencode($prod['slug']) ?>" class="font-bold text-slate-200 text-xs sm:text-sm hover:text-cyan-400 transition line-clamp-2">
                                <?= htmlspecialchars($prod['name']) ?>
                            </a>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between gap-2">
                            <div>
                                <div class="text-sm sm:text-base font-black text-cyan-400"><?= number_format($prod['price'], 0, ',', '.') ?>đ</div>
                                <?php if (!empty($prod['old_price'])): ?>
                                    <div class="text-[11px] text-slate-500 line-through"><?= number_format($prod['old_price'], 0, ',', '.') ?>đ</div>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <form action="<?= BASE_URL ?>index.php?url=cart/add" method="POST" class="ajax-add-to-cart m-0">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-8 h-8 rounded-xl bg-primary-600/30 hover:bg-primary-600 text-cyan-300 hover:text-white flex items-center justify-center border border-primary-500/30 transition shadow-sm" title="Thêm vào giỏ">
                                        <i class="fa-solid fa-cart-plus text-xs"></i>
                                    </button>
                                </form>
                                <form action="<?= BASE_URL ?>index.php?url=cart/buy-now" method="POST" class="m-0">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-rose-500 hover:from-amber-400 hover:to-rose-400 text-white font-bold text-[11px] flex items-center gap-1 shadow-md shadow-rose-500/20 hover:scale-105 transition" title="Mua ngay không qua giỏ hàng">
                                        <i class="fa-solid fa-bolt text-[10px]"></i>
                                        <span class="whitespace-nowrap">Mua ngay</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- SECTION 2: CÁC LOẠI PHỤ KIỆN & TAI NGHE ĐỈNH CAO -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-8">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-primary-400">Audio & Gadgets</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white mt-1">Hệ Sinh Thái Phụ Kiện & Tai Nghe</h2>
            </div>
            <a href="<?= BASE_URL ?>index.php?url=accessories" class="text-xs sm:text-sm font-bold text-primary-400 hover:text-cyan-300 transition flex items-center">
                Xem tất cả phụ kiện <i class="fa-solid fa-angle-right ml-1"></i>
            </a>
        </div>

        <!-- Accessory Categories Quick Pills -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
            <a href="<?= BASE_URL ?>index.php?url=accessories&category=2" class="glass-card p-3 rounded-2xl text-center hover:border-primary-500 transition group">
                <i class="fa-solid fa-headphones-simple text-purple-400 text-xl mb-1.5 group-hover:scale-110 transition block"></i>
                <span class="text-xs font-bold text-slate-200 group-hover:text-purple-300">Tai nghe Bluetooth</span>
            </a>
            <a href="<?= BASE_URL ?>index.php?url=accessories&category=3" class="glass-card p-3 rounded-2xl text-center hover:border-primary-500 transition group">
                <i class="fa-solid fa-headphones text-cyan-400 text-xl mb-1.5 group-hover:scale-110 transition block"></i>
                <span class="text-xs font-bold text-slate-200 group-hover:text-cyan-300">Tai nghe có dây</span>
            </a>
            <a href="<?= BASE_URL ?>index.php?url=accessories&category=4" class="glass-card p-3 rounded-2xl text-center hover:border-primary-500 transition group">
                <i class="fa-solid fa-bolt text-amber-400 text-xl mb-1.5 group-hover:scale-110 transition block"></i>
                <span class="text-xs font-bold text-slate-200 group-hover:text-amber-300">Củ & Cáp sạc GaN</span>
            </a>
            <a href="<?= BASE_URL ?>index.php?url=accessories&category=6" class="glass-card p-3 rounded-2xl text-center hover:border-primary-500 transition group">
                <i class="fa-solid fa-battery-three-quarters text-emerald-400 text-xl mb-1.5 group-hover:scale-110 transition block"></i>
                <span class="text-xs font-bold text-slate-200 group-hover:text-emerald-300">Sạc dự phòng MagSafe</span>
            </a>
            <a href="<?= BASE_URL ?>index.php?url=accessories&category=5" class="glass-card p-3 rounded-2xl text-center hover:border-primary-500 transition group">
                <i class="fa-solid fa-shield-halved text-rose-400 text-xl mb-1.5 group-hover:scale-110 transition block"></i>
                <span class="text-xs font-bold text-slate-200 group-hover:text-rose-300">Ốp lưng & Kính 9H</span>
            </a>
        </div>

        <!-- Headphones & Accessories Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($headphones as $prod): ?>
                <div class="glass-card rounded-2xl overflow-hidden hover:-translate-y-2 hover:border-primary-500/50 hover:shadow-2xl hover:shadow-primary-500/10 transition duration-300 flex flex-col justify-between">
                    <div class="product-thumb-box relative bg-slate-950/60 p-5 flex items-center justify-center cursor-pointer group/thumb select-none overflow-hidden rounded-t-2xl">
                        <?php $isW = !empty($userWishlistIds) && in_array($prod['id'], $userWishlistIds); ?>
                        <!-- Nút Thêm vào Danh sách yêu thích -->
                        <button type="button" 
                                onclick="toggleWishlist(<?= $prod['id'] ?>, this, event)" 
                                data-wishlist-pid="<?= $prod['id'] ?>"
                                class="btn-wishlist-toggle absolute top-3 right-3 z-30 w-8 h-8 rounded-full <?= $isW ? 'bg-rose-500/20 border-rose-500/50 text-rose-400' : 'bg-slate-900/80 hover:bg-rose-500/20 border-slate-700/80 hover:border-rose-500/40 text-slate-400 hover:text-rose-400' ?> border flex items-center justify-center transition shadow-lg group/wishlist"
                                title="<?= $isW ? 'Bỏ thích sản phẩm này' : 'Thêm vào danh sách yêu thích' ?>">
                            <i class="fa-<?= $isW ? 'solid text-rose-500' : 'regular text-slate-300 group-hover/wishlist:text-white' ?> fa-heart text-xs"></i>
                        </button>
                        <?php if (!empty($prod['old_price']) && $prod['old_price'] > $prod['price']): ?>
                            <?php $percent = round((($prod['old_price'] - $prod['price']) / $prod['old_price']) * 100); ?>
                            <span class="absolute top-3 left-3 z-10 bg-red-500/20 text-red-400 border border-red-500/40 text-[10px] font-black px-2 py-0.5 rounded-md">-<?= $percent ?>%</span>
                        <?php endif; ?>
                        <span class="absolute bottom-3 left-3 z-10 bg-slate-900/80 border border-slate-700/80 text-[10px] font-medium text-slate-300 px-2 py-0.5 rounded-md">
                            <?= htmlspecialchars($prod['brand_name'] ?? 'Audio') ?>
                        </span>
                        <div class="w-full flex items-center justify-center overflow-hidden rounded-xl">
                            <img src="<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="h-44 object-contain transition-transform duration-500 group-hover/thumb:scale-105 pointer-events-none">
                        </div>

                        <!-- Lớp phủ nút Xem chi tiết khi nhấn hoặc rê chuột vào ảnh -->
                        <div class="product-detail-overlay absolute inset-0 bg-slate-950/80 backdrop-blur-sm opacity-0 pointer-events-none group-hover/thumb:opacity-100 group-hover/thumb:pointer-events-auto transition-all duration-300 flex flex-col items-center justify-center p-3 z-20">
                            <a href="<?= BASE_URL ?>index.php?url=products/detail&slug=<?= urlencode($prod['slug']) ?>" 
                               class="btn-detail-link px-4 py-2 rounded-xl bg-gradient-to-r from-primary-600 via-indigo-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white font-bold text-xs shadow-xl shadow-cyan-500/20 border border-cyan-400/50 flex items-center gap-2 transform translate-y-2 group-hover/thumb:translate-y-0 hover:scale-105 active:scale-95 transition-all duration-300 pointer-events-auto"
                               title="Xem chi tiết sản phẩm">
                                <i class="fa-solid fa-eye text-cyan-200"></i>
                                <span>Xem chi tiết</span>
                            </a>
                            <span class="text-[10px] text-cyan-300/80 mt-2 flex items-center gap-1 font-medium">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i> Nhấn để mở chi tiết
                            </span>
                        </div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] text-purple-400 font-bold uppercase mb-1"><?= htmlspecialchars($prod['category_name']) ?></div>
                            <a href="<?= BASE_URL ?>index.php?url=products/detail&slug=<?= urlencode($prod['slug']) ?>" class="font-bold text-slate-200 text-xs sm:text-sm hover:text-cyan-400 transition line-clamp-2">
                                <?= htmlspecialchars($prod['name']) ?>
                            </a>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between gap-2">
                            <div>
                                <div class="text-sm sm:text-base font-black text-cyan-400"><?= number_format($prod['price'], 0, ',', '.') ?>đ</div>
                                <?php if (!empty($prod['old_price'])): ?>
                                    <div class="text-[11px] text-slate-500 line-through"><?= number_format($prod['old_price'], 0, ',', '.') ?>đ</div>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <form action="<?= BASE_URL ?>index.php?url=cart/add" method="POST" class="ajax-add-to-cart m-0">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-8 h-8 rounded-xl bg-primary-600/30 hover:bg-primary-600 text-cyan-300 hover:text-white flex items-center justify-center border border-primary-500/30 transition shadow-sm" title="Thêm vào giỏ">
                                        <i class="fa-solid fa-cart-plus text-xs"></i>
                                    </button>
                                </form>
                                <form action="<?= BASE_URL ?>index.php?url=cart/buy-now" method="POST" class="m-0">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-rose-500 hover:from-amber-400 hover:to-rose-400 text-white font-bold text-[11px] flex items-center gap-1 shadow-md shadow-rose-500/20 hover:scale-105 transition" title="Mua ngay không qua giỏ hàng">
                                        <i class="fa-solid fa-bolt text-[10px]"></i>
                                        <span class="whitespace-nowrap">Mua ngay</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- SECTION 3: SMART COMBO TIẾT KIỆM (TỰ ĐỘNG THÊM / HIỂN THỊ KHI CÓ SẢN PHẨM) -->
    <?php if (!empty($chargers)): ?>
    <?php
        $item1 = $chargers[0] ?? null;
        $item2 = $chargers[1] ?? null;

        $comboTitle = $item1 ? htmlspecialchars($item1['name']) : 'Combo Sạc Nhanh GaN';
        if ($item2) {
            $comboTitle .= ' + ' . htmlspecialchars($item2['name']);
        }

        $rawPrice = ($item1['price'] ?? 0) + ($item2['price'] ?? 0);
        if ($rawPrice == 0) $rawPrice = $item1['price'] ?? 499000;
        $discountPercent = 20;
        $comboPrice = round($rawPrice * (1 - $discountPercent / 100));
    ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="glass-card rounded-3xl p-8 border border-emerald-500/30 bg-gradient-to-r from-emerald-950/40 via-slate-900/90 to-cyan-950/40">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
                <div class="max-w-md space-y-4 text-center lg:text-left">
                    <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-xs font-bold px-3 py-1 rounded-full uppercase">
                        <i class="fa-solid fa-sparkles mr-1"></i> Gợi Ý Combo Tiết Kiệm
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-black text-white"><?= $comboTitle ?></h3>
                    <p class="text-slate-300 text-xs leading-relaxed">
                        Combo phụ kiện chính hãng tối ưu nhất, bảo vệ pin toàn diện và tự động tiết kiệm <?= $discountPercent ?>% so với khi mua lẻ.
                    </p>
                    <div class="flex items-baseline space-x-3 justify-center lg:justify-start">
                        <span class="text-3xl font-black text-cyan-400"><?= number_format($comboPrice, 0, ',', '.') ?>đ</span>
                        <?php if ($rawPrice > $comboPrice): ?>
                            <span class="text-slate-500 text-sm line-through"><?= number_format($rawPrice, 0, ',', '.') ?>đ</span>
                            <span class="bg-red-500/20 text-red-400 border border-red-500/40 text-xs font-bold px-2 py-0.5 rounded-md">-<?= $discountPercent ?>%</span>
                        <?php endif; ?>
                    </div>
                    <a href="<?= BASE_URL ?>index.php?url=accessories&category=4" class="inline-flex items-center bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-6 py-3 rounded-xl transition text-xs shadow-lg shadow-emerald-600/30">
                        <i class="fa-solid fa-bolt mr-2"></i> Mua ngay combo tiết kiệm
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-4 w-full lg:w-auto">
                    <?php foreach ($chargers as $item): ?>
                        <div class="bg-slate-950/80 border border-slate-800 p-4 rounded-2xl text-center space-y-2 hover:border-emerald-500/50 transition">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="" class="w-24 h-24 object-contain mx-auto rounded-xl">
                            <div class="text-[11px] font-bold text-slate-200 truncate"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="text-xs font-black text-cyan-400"><?= number_format($item['price'], 0, ',', '.') ?>đ</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>
