<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Breadcrumb -->
    <nav class="flex text-xs text-slate-400 items-center flex-wrap gap-2">
        <a href="<?= BASE_URL ?>" class="hover:text-cyan-400 transition flex items-center gap-1">
            <i class="fa-solid fa-house text-[11px]"></i> Trang chủ
        </a>
        <span class="text-slate-600">/</span>
        <a href="<?= BASE_URL ?>index.php?url=products" class="hover:text-cyan-400 transition">Sản phẩm</a>
        <?php if (!empty($filters['keyword'])): ?>
            <span class="text-slate-600">/</span>
            <span class="text-cyan-400 font-semibold truncate max-w-xs">Tìm kiếm: "<?= htmlspecialchars($filters['keyword']) ?>"</span>
        <?php elseif ($currentCategory): ?>
            <span class="text-slate-600">/</span>
            <span class="text-cyan-400 font-semibold"><?= htmlspecialchars($currentCategory['name']) ?></span>
        <?php endif; ?>
    </nav>

    <!-- Header Banner Kết Quả Tìm Kiếm / Danh Mục -->
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-700/80 bg-gradient-to-r from-slate-900/90 via-indigo-950/60 to-slate-900/90 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-cyan-500/10 rounded-full blur-[90px] pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="flex items-center gap-2 flex-wrap">
                    <?php if (!empty($filters['keyword'])): ?>
                        <span class="bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-[11px] font-bold px-3 py-1 rounded-full uppercase inline-flex items-center">
                            <i class="fa-solid fa-magnifying-glass mr-1.5 text-xs"></i> Kết quả tìm kiếm
                        </span>
                        <a href="<?= BASE_URL ?>index.php?url=products" class="text-xs text-rose-400 hover:text-rose-300 transition flex items-center gap-1 bg-rose-950/40 border border-rose-800/60 px-2.5 py-1 rounded-full">
                            <span>Xóa từ khóa</span> <i class="fa-solid fa-xmark"></i>
                        </a>
                    <?php else: ?>
                        <span class="bg-primary-500/20 text-primary-300 border border-primary-500/40 text-[11px] font-bold px-3 py-1 rounded-full uppercase inline-flex items-center">
                            <i class="fa-solid fa-layer-group mr-1.5 text-xs"></i> Khám phá sản phẩm
                        </span>
                    <?php endif; ?>
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    <?php if (!empty($filters['keyword'])): ?>
                        Kết quả cho: <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-primary-400">"<?= htmlspecialchars($filters['keyword']) ?>"</span>
                    <?php else: ?>
                        <?= htmlspecialchars($pageTitle) ?>
                    <?php endif; ?>
                </h1>

                <p class="text-slate-300 text-xs sm:text-sm">
                    Tìm thấy <strong class="text-cyan-400 font-bold"><?= $totalProducts ?></strong> sản phẩm công nghệ chính hãng phù hợp với yêu cầu của bạn.
                </p>
            </div>

            <!-- Form Tìm Kiếm Lớn Trực Tiếp Tại Trang -->
            <form action="<?= BASE_URL ?>index.php" method="GET" class="w-full md:w-80 flex-shrink-0">
                <input type="hidden" name="url" value="products">
                <div class="relative">
                    <input type="text" name="q" value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>" 
                        placeholder="Nhập tên sản phẩm, hãng..." 
                        class="w-full pl-10 pr-24 py-3 text-xs rounded-2xl border border-slate-700 bg-slate-950/80 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-500/20 shadow-inner">
                    <span class="absolute left-3.5 top-3.5 text-slate-400 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <button type="submit" class="absolute right-1.5 top-1.5 bottom-1.5 px-4 bg-gradient-to-r from-primary-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-md">
                        <span>Tìm</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Nút Bấm "Bộ Lọc" trên Mobile / Tablet -->
    <div class="lg:hidden mb-6">
        <button type="button" 
                onclick="toggleClientFilter()" 
                class="w-full flex items-center justify-between px-5 py-3.5 rounded-2xl bg-slate-900/95 border border-slate-700/80 text-white font-bold text-xs shadow-lg hover:border-cyan-400 transition cursor-pointer">
            <span class="flex items-center gap-2.5">
                <i class="fa-solid fa-filter text-cyan-400"></i>
                <span class="font-bold text-sm">Bộ Lọc</span>
                <?php if (!empty($filters['brand_id']) || !empty($filters['device']) || !empty($filters['min_price']) || !empty($filters['max_price']) || !empty($currentCategory)): ?>
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                    <span class="text-[11px] text-cyan-300 font-semibold">(Đang áp dụng)</span>
                <?php endif; ?>
            </span>
            <span class="flex items-center gap-2 text-slate-400 text-xs">
                <span>Nhấn để chọn</span>
                <i id="clientFilterChevron" class="fa-solid fa-chevron-down text-xs transition-transform duration-300"></i>
            </span>
        </button>
    </div>

    <!-- Layout 2 Cột: Sidebar Bộ Lọc & Lưới Sản Phẩm -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Bộ Lọc (Filters) -->
        <div id="productClientFilterPanel" class="hidden lg:block lg:col-span-1">
            <div class="glass-card rounded-3xl p-5 border border-slate-700/80 space-y-6 sticky top-24">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-cyan-400"></i> Bộ Lọc Tìm Kiếm
                    </h3>
                    <a href="<?= BASE_URL ?>index.php?url=products" class="text-[11px] text-rose-400 hover:underline">
                        Đặt lại
                    </a>
                </div>

                <form action="<?= BASE_URL ?>index.php" method="GET" class="space-y-5">
                    <input type="hidden" name="url" value="products">
                    <?php if (!empty($filters['keyword'])): ?>
                        <input type="hidden" name="q" value="<?= htmlspecialchars($filters['keyword']) ?>">
                    <?php endif; ?>

                    <!-- 1. Lọc theo Danh Mục -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Danh Mục</label>
                        <div class="space-y-1 text-xs">
                            <a href="<?= BASE_URL ?>index.php?url=products<?= !empty($filters['keyword']) ? '&q=' . urlencode($filters['keyword']) : '' ?>" 
                               class="flex items-center justify-between px-3 py-2 rounded-xl transition <?= empty($currentCategory) ? 'bg-primary-600/20 text-cyan-300 font-bold border border-primary-500/40' : 'text-slate-300 hover:bg-slate-800/60' ?>">
                                <span><i class="fa-solid fa-layer-group text-[10px] mr-2"></i> Tất cả danh mục</span>
                                <i class="fa-solid fa-chevron-right text-[10px] text-slate-500"></i>
                            </a>
                            <?php foreach ($categories as $cat): ?>
                                <?php $isActive = (!empty($currentCategory) && $currentCategory['id'] == $cat['id']); ?>
                                <a href="<?= BASE_URL ?>index.php?url=products&category=<?= urlencode($cat['slug']) ?><?= !empty($filters['keyword']) ? '&q=' . urlencode($filters['keyword']) : '' ?>" 
                                   class="flex items-center justify-between px-3 py-2 rounded-xl transition <?= $isActive ? 'bg-primary-600/20 text-cyan-300 font-bold border border-primary-500/40' : 'text-slate-300 hover:bg-slate-800/60' ?>">
                                    <span class="truncate"><i class="fa-solid fa-chevron-right text-[9px] mr-2 text-slate-500"></i> <?= htmlspecialchars($cat['name']) ?></span>
                                    <i class="fa-solid fa-angle-right text-[10px] text-slate-600"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 2. Lọc theo Thương Hiệu -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Thương Hiệu</label>
                        <select name="brand" class="w-full text-xs rounded-xl border border-slate-700 bg-slate-900/90 text-slate-200 px-3 py-2.5 focus:outline-none focus:border-cyan-400" onchange="this.form.submit()">
                            <option value="">-- Tất cả thương hiệu --</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand['id'] ?>" <?= ($filters['brand_id'] == $brand['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($brand['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 3. Lọc theo Độ Tương Thích -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Tương Thích Thiết Bị</label>
                        <select name="device" class="w-full text-xs rounded-xl border border-slate-700 bg-slate-900/90 text-slate-200 px-3 py-2.5 focus:outline-none focus:border-cyan-400" onchange="this.form.submit()">
                            <option value="">-- Tất cả thiết bị --</option>
                            <option value="iPhone" <?= ($filters['device'] === 'iPhone') ? 'selected' : '' ?>>iPhone (Apple)</option>
                            <option value="Samsung" <?= ($filters['device'] === 'Samsung') ? 'selected' : '' ?>>Samsung Galaxy</option>
                            <option value="Type-C" <?= ($filters['device'] === 'Type-C') ? 'selected' : '' ?>>Chuẩn Type-C GaN</option>
                            <option value="3.5mm" <?= ($filters['device'] === '3.5mm') ? 'selected' : '' ?>>Jack tai nghe 3.5mm</option>
                        </select>
                    </div>

                    <!-- 4. Lọc theo Khoảng Giá -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Khoảng Giá (VNĐ)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_price" class="w-full text-xs rounded-xl border border-slate-700 bg-slate-900/90 text-slate-200 px-3 py-2 focus:outline-none focus:border-cyan-400" placeholder="Từ..." value="<?= htmlspecialchars($filters['min_price'] ?? '') ?>">
                            <input type="number" name="max_price" class="w-full text-xs rounded-xl border border-slate-700 bg-slate-900/90 text-slate-200 px-3 py-2 focus:outline-none focus:border-cyan-400" placeholder="Đến..." value="<?= htmlspecialchars($filters['max_price'] ?? '') ?>">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-primary-600/25 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-filter text-xs"></i> Áp Dụng Bộ Lọc
                    </button>
                </form>
            </div>
        </div>

        <!-- Product Grid & Sorting -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Thanh Công Cụ Sắp Xếp & Thống Kê -->
            <div class="glass-card rounded-2xl p-4 border border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="text-xs text-slate-300">
                    Hiển thị <strong class="text-white"><?= count($products) ?></strong> / <strong class="text-cyan-400"><?= $totalProducts ?></strong> sản phẩm
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <span class="text-slate-400">Sắp xếp:</span>
                    <form action="<?= BASE_URL ?>index.php" method="GET" class="inline">
                        <input type="hidden" name="url" value="products">
                        <?php if (!empty($filters['category_id'])): ?><input type="hidden" name="category" value="<?= htmlspecialchars($_GET['category'] ?? '') ?>"><?php endif; ?>
                        <?php if (!empty($filters['brand_id'])): ?><input type="hidden" name="brand" value="<?= htmlspecialchars($filters['brand_id']) ?>"><?php endif; ?>
                        <?php if (!empty($filters['device'])): ?><input type="hidden" name="device" value="<?= htmlspecialchars($filters['device']) ?>"><?php endif; ?>
                        <?php if (!empty($filters['keyword'])): ?><input type="hidden" name="q" value="<?= htmlspecialchars($filters['keyword']) ?>"><?php endif; ?>
                        
                        <select name="sort" class="text-xs rounded-xl border border-slate-700 bg-slate-900 px-3 py-1.5 text-slate-200 focus:outline-none focus:border-cyan-400" onchange="this.form.submit()">
                            <option value="newest" <?= ($filters['sort'] === 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="price_asc" <?= ($filters['sort'] === 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
                            <option value="price_desc" <?= ($filters['sort'] === 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
                            <option value="popular" <?= ($filters['sort'] === 'popular') ? 'selected' : '' ?>>Phổ biến nhất</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Danh Sách Sản Phẩm -->
            <?php if (empty($products)): ?>
                <!-- Empty State (Không có kết quả tìm kiếm) -->
                <div class="glass-card rounded-3xl p-10 sm:p-14 text-center border border-slate-700/80 space-y-5">
                    <div class="w-20 h-20 mx-auto rounded-3xl bg-slate-800/80 border border-slate-700/90 flex items-center justify-center text-3xl text-cyan-400 shadow-xl shadow-cyan-500/10">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <div class="space-y-2 max-w-md mx-auto">
                        <h3 class="text-lg font-bold text-white">Không tìm thấy sản phẩm nào</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Không có sản phẩm nào khớp với từ khóa hoặc bộ lọc hiện tại. Quý khách vui lòng thử tìm kiếm lại với các từ khóa phổ biến dưới đây:
                        </p>
                    </div>

                    <!-- Gợi ý từ khóa thịnh hành -->
                    <div class="flex flex-wrap justify-center gap-2 pt-2 max-w-lg mx-auto">
                        <a href="<?= BASE_URL ?>index.php?url=products&q=iPhone" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-xs text-slate-300 hover:text-white border border-slate-700 transition">
                            <i class="fa-brands fa-apple text-slate-400 mr-1"></i> iPhone
                        </a>
                        <a href="<?= BASE_URL ?>index.php?url=products&q=Samsung" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-xs text-slate-300 hover:text-white border border-slate-700 transition">
                            <i class="fa-solid fa-gem text-blue-400 mr-1"></i> Samsung
                        </a>
                        <a href="<?= BASE_URL ?>index.php?url=products&q=Tai+nghe" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-xs text-slate-300 hover:text-white border border-slate-700 transition">
                            <i class="fa-solid fa-headphones text-purple-400 mr-1"></i> Tai nghe ANC
                        </a>
                        <a href="<?= BASE_URL ?>index.php?url=products&q=Củ+sạc" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-xs text-slate-300 hover:text-white border border-slate-700 transition">
                            <i class="fa-solid fa-bolt text-amber-400 mr-1"></i> Củ sạc GaN
                        </a>
                        <a href="<?= BASE_URL ?>index.php?url=products&q=Sạc+dự+phòng" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-xs text-slate-300 hover:text-white border border-slate-700 transition">
                            <i class="fa-solid fa-battery-half text-emerald-400 mr-1"></i> Sạc dự phòng
                        </a>
                    </div>

                    <div class="pt-4">
                        <a href="<?= BASE_URL ?>index.php?url=products" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-primary-500/25 transition">
                            <i class="fa-solid fa-rotate-left"></i> Xem tất cả sản phẩm
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Grid Sản Phẩm -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
                    <?php foreach ($products as $p): ?>
                        <div class="glass-card rounded-2xl overflow-hidden border border-slate-700/80 hover:border-cyan-500/50 hover:shadow-2xl hover:shadow-cyan-500/10 transition duration-300 flex flex-col group">
                            <!-- Ảnh Sản Phẩm -->
                            <div class="product-thumb-box relative pt-[85%] bg-slate-900/60 overflow-hidden cursor-pointer group/thumb select-none">
                                <?php $isW = !empty($userWishlistIds) && in_array($p['id'], $userWishlistIds); ?>
                                <!-- Nút Thêm vào Danh sách yêu thích -->
                                <button type="button" 
                                        onclick="toggleWishlist(<?= $p['id'] ?>, this, event)" 
                                        data-wishlist-pid="<?= $p['id'] ?>"
                                        class="btn-wishlist-toggle absolute top-2.5 right-2.5 z-30 w-8 h-8 rounded-full <?= $isW ? 'bg-rose-500/20 border-rose-500/50 text-rose-400' : 'bg-slate-900/80 hover:bg-rose-500/20 border-slate-700/80 hover:border-rose-500/40 text-slate-400 hover:text-rose-400' ?> border flex items-center justify-center transition shadow-lg group/wishlist"
                                        title="<?= $isW ? 'Bỏ thích sản phẩm này' : 'Thêm vào danh sách yêu thích' ?>">
                                    <i class="fa-<?= $isW ? 'solid text-rose-500' : 'regular text-slate-300 group-hover/wishlist:text-white' ?> fa-heart text-xs"></i>
                                </button>
                                <?php if (!empty($p['old_price']) && $p['old_price'] > $p['price']): ?>
                                    <?php $pct = round((($p['old_price'] - $p['price']) / $p['old_price']) * 100); ?>
                                    <span class="absolute top-2.5 left-2.5 z-10 bg-rose-500 text-white font-black text-[10px] px-2 py-0.5 rounded-md shadow-md">
                                        -<?= $pct ?>%
                                    </span>
                                <?php endif; ?>

                                <span class="absolute bottom-2.5 left-2.5 z-10 bg-slate-900/80 backdrop-blur-md text-slate-300 text-[10px] px-2 py-0.5 rounded-full border border-slate-700/60">
                                    <?= htmlspecialchars($p['compatible_device'] ?? 'Chính hãng') ?>
                                </span>

                                <div class="absolute inset-0 p-4 flex items-center justify-center">
                                    <img src="<?= htmlspecialchars($p['image']) ?>" 
                                         alt="<?= htmlspecialchars($p['name']) ?>" 
                                         loading="lazy"
                                         class="w-full h-full object-contain group-hover/thumb:scale-105 transition duration-500 pointer-events-none">
                                </div>

                                <!-- Lớp phủ nút Xem chi tiết khi nhấn hoặc rê chuột vào ảnh -->
                                <div class="product-detail-overlay absolute inset-0 bg-slate-950/80 backdrop-blur-sm opacity-0 pointer-events-none group-hover/thumb:opacity-100 group-hover/thumb:pointer-events-auto transition-all duration-300 flex flex-col items-center justify-center p-3 z-20">
                                    <a href="<?= BASE_URL ?>index.php?url=products/detail&slug=<?= urlencode($p['slug']) ?>" 
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

                            <!-- Thông Tin Sản Phẩm -->
                            <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                                        <span class="font-bold text-cyan-400 uppercase tracking-wider"><?= htmlspecialchars($p['brand_name'] ?? $p['category_name']) ?></span>
                                        <span class="flex items-center text-amber-400"><i class="fa-solid fa-star text-[9px] mr-1"></i>5.0</span>
                                    </div>
                                    <a href="<?= BASE_URL ?>index.php?url=products/detail&slug=<?= urlencode($p['slug']) ?>" 
                                       class="text-xs sm:text-sm font-bold text-white hover:text-cyan-400 transition line-clamp-2 leading-snug">
                                        <?= htmlspecialchars($p['name']) ?>
                                    </a>
                                </div>

                                <div class="space-y-3 pt-1">
                                    <!-- Giá -->
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-rose-400 font-extrabold text-sm sm:text-base">
                                            <?= number_format($p['price'], 0, ',', '.') ?>đ
                                        </span>
                                        <?php if (!empty($p['old_price'])): ?>
                                            <span class="text-slate-500 line-through text-[11px]">
                                                <?= number_format($p['old_price'], 0, ',', '.') ?>đ
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Cụm Nút Giỏ Hàng & Mua Ngay -->
                                    <div class="grid grid-cols-2 gap-2">
                                        <form action="<?= BASE_URL ?>index.php?url=cart/add" method="POST" class="ajax-add-to-cart m-0">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-200 hover:text-cyan-300 border border-slate-700 font-bold text-[11px] sm:text-xs transition flex items-center justify-center gap-1 shadow-sm" title="Thêm vào giỏ">
                                                <i class="fa-solid fa-cart-plus text-xs"></i> <span>Giỏ hàng</span>
                                            </button>
                                        </form>
                                        <form action="<?= BASE_URL ?>index.php?url=cart/buy-now" method="POST" class="m-0">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-rose-500 hover:from-amber-400 hover:to-rose-400 text-white font-bold text-[11px] sm:text-xs transition flex items-center justify-center gap-1 shadow-md shadow-rose-500/20 hover:scale-[1.02]" title="Mua ngay không qua giỏ hàng">
                                                <i class="fa-solid fa-bolt text-xs"></i> <span>Mua ngay</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Phân Trang (Pagination) -->
                <?php if ($totalPages > 1): ?>
                    <div class="flex justify-center items-center gap-2 pt-8">
                        <?php if ($page > 1): ?>
                            <a href="<?= BASE_URL ?>index.php?url=products&page=<?= $page - 1 ?>&category=<?= urlencode($_GET['category'] ?? '') ?>&brand=<?= urlencode($_GET['brand'] ?? '') ?>&device=<?= urlencode($_GET['device'] ?? '') ?>&q=<?= urlencode($_GET['q'] ?? '') ?>&sort=<?= urlencode($filters['sort']) ?>" 
                               class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center text-xs border border-slate-700 transition">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="<?= BASE_URL ?>index.php?url=products&page=<?= $i ?>&category=<?= urlencode($_GET['category'] ?? '') ?>&brand=<?= urlencode($_GET['brand'] ?? '') ?>&device=<?= urlencode($_GET['device'] ?? '') ?>&q=<?= urlencode($_GET['q'] ?? '') ?>&sort=<?= urlencode($filters['sort']) ?>" 
                               class="w-9 h-9 rounded-xl text-xs font-bold flex items-center justify-center transition <?= ($page == $i) ? 'bg-gradient-to-r from-primary-600 to-cyan-500 text-white shadow-lg shadow-primary-500/25 border border-primary-400' : 'bg-slate-800/80 hover:bg-slate-700 text-slate-300 border border-slate-700' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="<?= BASE_URL ?>index.php?url=products&page=<?= $page + 1 ?>&category=<?= urlencode($_GET['category'] ?? '') ?>&brand=<?= urlencode($_GET['brand'] ?? '') ?>&device=<?= urlencode($_GET['device'] ?? '') ?>&q=<?= urlencode($_GET['q'] ?? '') ?>&sort=<?= urlencode($filters['sort']) ?>" 
                               class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center text-xs border border-slate-700 transition">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function toggleClientFilter() {
        const panel = document.getElementById('productClientFilterPanel');
        const chevron = document.getElementById('clientFilterChevron');
        if (!panel) return;
        panel.classList.toggle('hidden');
        if (chevron) {
            chevron.classList.toggle('rotate-180');
        }
    }
</script>
