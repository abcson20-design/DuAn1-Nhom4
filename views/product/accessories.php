<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Breadcrumb -->
    <nav class="flex text-xs text-slate-400">
        <a href="<?= BASE_URL ?>" class="hover:text-cyan-400">Trang chủ</a>
        <span class="mx-2">/</span>
        <span class="text-slate-200 font-semibold">Phụ kiện & Tai nghe</span>
        <?php if ($selectedCatId): ?>
            <span class="mx-2">/</span>
            <?php 
                $activeCatName = '';
                foreach ($accessoryCategories as $c) {
                    if ($c['id'] == $selectedCatId) $activeCatName = $c['name'];
                }
            ?>
            <span class="text-primary-400 font-semibold"><?= htmlspecialchars($activeCatName) ?></span>
        <?php endif; ?>
    </nav>

    <!-- Header Banner Trang Phụ Kiện -->
    <div class="glass-card rounded-3xl p-6 sm:p-10 border border-slate-700/80 bg-gradient-to-r from-slate-900/90 via-purple-950/60 to-slate-900/90 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-purple-500/15 rounded-full blur-[80px] pointer-events-none"></div>
        <div class="relative z-10 max-w-2xl space-y-3">
            <span class="bg-purple-500/20 text-purple-300 border border-purple-500/40 text-xs font-bold px-3 py-1 rounded-full uppercase inline-flex items-center">
                <i class="fa-solid fa-headphones mr-1.5"></i> Audio & Smart Accessories
            </span>
            <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                Phụ Kiện Điện Thoại & <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-primary-400 to-cyan-400">Tai Nghe Đỉnh Cao</span>
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                Đầy đủ các loại phụ kiện cao cấp: Tai nghe Bluetooth ANC chống ồn, tai nghe có dây chuẩn Hi-Res, củ sạc GaN siêu tốc, cáp bọc dù, sạc MagSafe và ốp chống sốc quân đội.
            </p>
        </div>
    </div>

    <!-- LOẠI PHỤ KIỆN (CATEGORY SELECTOR CARDS) -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Chọn Loại Phụ Kiện:</h3>
            <?php if ($selectedCatId || !empty($filters['brand_id']) || !empty($filters['device']) || !empty($filters['min_price'])): ?>
                <a href="<?= BASE_URL ?>index.php?url=accessories" class="text-xs text-rose-400 hover:underline">Xóa tất cả bộ lọc</a>
            <?php endif; ?>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
            <!-- Tất cả phụ kiện -->
            <a href="<?= BASE_URL ?>index.php?url=accessories" 
                class="p-4 rounded-2xl text-center transition flex flex-col items-center justify-center <?= empty($selectedCatId) ? 'bg-gradient-to-r from-primary-600 to-indigo-600 text-white shadow-lg shadow-primary-500/25 border border-primary-400' : 'glass-card text-slate-300 hover:text-white hover:border-primary-400' ?>">
                <i class="fa-solid fa-cubes text-xl mb-1.5"></i>
                <span class="text-xs font-bold">Tất cả phụ kiện</span>
            </a>

            <!-- Tai nghe Bluetooth -->
            <a href="<?= BASE_URL ?>index.php?url=accessories&category=2" 
                class="p-4 rounded-2xl text-center transition flex flex-col items-center justify-center <?= ($selectedCatId == 2) ? 'bg-gradient-to-r from-primary-600 to-indigo-600 text-white shadow-lg shadow-primary-500/25 border border-primary-400' : 'glass-card text-slate-300 hover:text-white hover:border-primary-400' ?>">
                <i class="fa-solid fa-headphones-simple text-purple-400 text-xl mb-1.5"></i>
                <span class="text-xs font-bold">Tai nghe Bluetooth</span>
            </a>

            <!-- Tai nghe có dây -->
            <a href="<?= BASE_URL ?>index.php?url=accessories&category=3" 
                class="p-4 rounded-2xl text-center transition flex flex-col items-center justify-center <?= ($selectedCatId == 3) ? 'bg-gradient-to-r from-primary-600 to-indigo-600 text-white shadow-lg shadow-primary-500/25 border border-primary-400' : 'glass-card text-slate-300 hover:text-white hover:border-primary-400' ?>">
                <i class="fa-solid fa-headphones text-cyan-400 text-xl mb-1.5"></i>
                <span class="text-xs font-bold">Tai nghe có dây</span>
            </a>

            <!-- Củ sạc & Cáp sạc -->
            <a href="<?= BASE_URL ?>index.php?url=accessories&category=4" 
                class="p-4 rounded-2xl text-center transition flex flex-col items-center justify-center <?= ($selectedCatId == 4) ? 'bg-gradient-to-r from-primary-600 to-indigo-600 text-white shadow-lg shadow-primary-500/25 border border-primary-400' : 'glass-card text-slate-300 hover:text-white hover:border-primary-400' ?>">
                <i class="fa-solid fa-bolt text-amber-400 text-xl mb-1.5"></i>
                <span class="text-xs font-bold">Củ & Cáp sạc GaN</span>
            </a>

            <!-- Sạc dự phòng -->
            <a href="<?= BASE_URL ?>index.php?url=accessories&category=6" 
                class="p-4 rounded-2xl text-center transition flex flex-col items-center justify-center <?= ($selectedCatId == 6) ? 'bg-gradient-to-r from-primary-600 to-indigo-600 text-white shadow-lg shadow-primary-500/25 border border-primary-400' : 'glass-card text-slate-300 hover:text-white hover:border-primary-400' ?>">
                <i class="fa-solid fa-battery-three-quarters text-emerald-400 text-xl mb-1.5"></i>
                <span class="text-xs font-bold">Sạc dự phòng</span>
            </a>

            <!-- Ốp lưng & Kính -->
            <a href="<?= BASE_URL ?>index.php?url=accessories&category=5" 
                class="p-4 rounded-2xl text-center transition flex flex-col items-center justify-center <?= ($selectedCatId == 5) ? 'bg-gradient-to-r from-primary-600 to-indigo-600 text-white shadow-lg shadow-primary-500/25 border border-primary-400' : 'glass-card text-slate-300 hover:text-white hover:border-primary-400' ?>">
                <i class="fa-solid fa-shield-halved text-rose-400 text-xl mb-1.5"></i>
                <span class="text-xs font-bold">Ốp lưng & Kính 9H</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar: Thương hiệu phụ kiện & Độ tương thích -->
    <div class="glass-card p-4 rounded-2xl flex flex-col lg:flex-row justify-between items-center gap-4">
        <div class="flex items-center space-x-2 text-xs text-slate-400">
            <span>Tìm thấy <strong class="text-primary-400 font-bold"><?= $totalProducts ?></strong> món phụ kiện</span>
        </div>

        <form action="<?= BASE_URL ?>index.php" method="GET" class="flex flex-wrap items-center gap-3">
            <input type="hidden" name="url" value="accessories">
            <?php if ($selectedCatId): ?>
                <input type="hidden" name="category" value="<?= $selectedCatId ?>">
            <?php endif; ?>

            <!-- Lọc theo Hãng Phụ Kiện -->
            <select name="brand" onchange="this.form.submit()" class="text-xs p-2 rounded-xl border border-slate-700 bg-slate-950/80 text-slate-200 focus:outline-none focus:ring-1 focus:ring-primary-500">
                <option value="">-- Tất cả thương hiệu --</option>
                <?php foreach ($accessoryBrands as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= (!empty($filters['brand_id']) && $filters['brand_id'] == $b['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Lọc theo Độ Tương Thích Thiết Bị -->
            <select name="device" onchange="this.form.submit()" class="text-xs p-2 rounded-xl border border-slate-700 bg-slate-950/80 text-slate-200 focus:outline-none focus:ring-1 focus:ring-primary-500">
                <option value="">-- Tương thích thiết bị --</option>
                <option value="iPhone" <?= ($filters['device'] === 'iPhone') ? 'selected' : '' ?>>Dành cho iPhone</option>
                <option value="Samsung" <?= ($filters['device'] === 'Samsung') ? 'selected' : '' ?>>Dành cho Samsung</option>
                <option value="Type-C" <?= ($filters['device'] === 'Type-C') ? 'selected' : '' ?>>Chuẩn sạc Type-C</option>
                <option value="3.5mm" <?= ($filters['device'] === '3.5mm') ? 'selected' : '' ?>>Jack 3.5mm</option>
            </select>

            <!-- Sắp Xếp -->
            <select name="sort" onchange="this.form.submit()" class="text-xs p-2 rounded-xl border border-slate-700 bg-slate-950/80 text-slate-200 focus:outline-none focus:ring-1 focus:ring-primary-500">
                <option value="newest" <?= ($filters['sort'] === 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                <option value="price_asc" <?= ($filters['sort'] === 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
                <option value="price_desc" <?= ($filters['sort'] === 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
                <option value="popular" <?= ($filters['sort'] === 'popular') ? 'selected' : '' ?>>Xem nhiều nhất</option>
            </select>
        </form>
    </div>

    <!-- Accessories Product Grid -->
    <?php if (empty($products)): ?>
        <div class="glass-card rounded-3xl p-12 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-800/80 flex items-center justify-center mx-auto text-slate-500 text-2xl">
                <i class="fa-solid fa-headphones"></i>
            </div>
            <h3 class="text-lg font-bold text-white">Chưa tìm thấy phụ kiện phù hợp</h3>
            <p class="text-xs text-slate-400">Vui lòng thử lại với loại phụ kiện hoặc mức giá khác.</p>
            <a href="<?= BASE_URL ?>index.php?url=accessories" class="inline-block bg-primary-600 text-white text-xs font-bold px-6 py-2.5 rounded-xl">
                Xem tất cả các loại phụ kiện
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($products as $prod): ?>
                <div class="glass-card rounded-2xl overflow-hidden hover:-translate-y-2 hover:border-primary-500/50 hover:shadow-2xl hover:shadow-primary-500/10 transition duration-300 flex flex-col justify-between">
                    <div class="product-thumb-box relative bg-slate-950/60 p-6 flex items-center justify-center cursor-pointer group/thumb select-none overflow-hidden rounded-t-2xl">
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
                            <?= htmlspecialchars($prod['brand_name'] ?? 'Phụ Kiện') ?>
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

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="flex justify-center space-x-2 pt-6">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= BASE_URL ?>index.php?url=accessories&page=<?= $i ?>&category=<?= $selectedCatId ?>&sort=<?= urlencode($filters['sort']) ?>" 
                        class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-bold transition <?= ($page == $i) ? 'bg-primary-600 text-white' : 'glass-card text-slate-400 hover:text-white' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
