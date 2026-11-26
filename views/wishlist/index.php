<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs font-bold tracking-wide uppercase mb-2">
                <i class="fa-solid fa-heart"></i> Danh Sách Yêu Thích (Wishlist)
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white">Sản Phẩm Bạn Đã Lưu</h1>
            <p class="text-xs text-slate-400 mt-1">Các sản phẩm bạn quan tâm sẽ luôn được cập nhật giá mới nhất tại đây</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= BASE_URL ?>index.php?url=phones" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold py-2.5 px-4 rounded-xl transition flex items-center gap-2">
                <i class="fa-solid fa-mobile-screen"></i> Xem Điện Thoại
            </a>
            <a href="<?= BASE_URL ?>index.php?url=accessories" class="bg-gradient-to-r from-primary-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white text-xs font-extrabold py-2.5 px-4 rounded-xl shadow-lg shadow-primary-500/25 transition flex items-center gap-2">
                <i class="fa-solid fa-headphones"></i> Xem Phụ Kiện
            </a>
        </div>
    </div>

    <?php if (empty($items)): ?>
        <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-12 text-center max-w-lg mx-auto shadow-2xl space-y-4">
            <div class="w-20 h-20 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-full flex items-center justify-center mx-auto text-3xl">
                <i class="fa-regular fa-heart"></i>
            </div>
            <h3 class="text-lg font-bold text-white">Danh sách yêu thích đang trống</h3>
            <p class="text-xs text-slate-400">Bạn chưa lưu sản phẩm nào. Hãy bấm vào biểu tượng trái tim ở các sản phẩm để lưu lại xem sau nhé!</p>
            <div class="pt-2">
                <a href="<?= BASE_URL ?>index.php?url=home" class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white text-xs font-extrabold py-3 px-6 rounded-xl shadow-lg transition">
                    <i class="fa-solid fa-store"></i> Khám phá sản phẩm ngay
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($items as $prod): ?>
                <?php
                    $isAvailable = (int)($prod['quantity'] ?? 0) > 0;
                    $currentPrice = (float)($prod['price'] ?? 0);
                    $oldPrice = !empty($prod['old_price']) ? (float)$prod['old_price'] : 0;
                ?>
                <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl hover:border-slate-700 transition flex flex-col group relative" id="wishlist-card-<?= $prod['id'] ?>">
                    <!-- Nút Bỏ thích (Remove) -->
                    <button type="button" 
                            onclick="toggleWishlist(<?= $prod['id'] ?>, this, event)" 
                            data-wishlist-pid="<?= $prod['id'] ?>"
                            class="btn-wishlist-toggle absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-slate-900/80 hover:bg-rose-500 text-rose-400 hover:text-white border border-slate-700 flex items-center justify-center transition shadow"
                            title="Bỏ thích sản phẩm này">
                        <i class="fa-solid fa-heart text-sm"></i>
                    </button>

                    <!-- Ảnh sản phẩm -->
                    <a href="<?= BASE_URL ?>index.php?url=products/detail&slug=<?= urlencode($prod['slug']) ?>" class="block aspect-square overflow-hidden bg-slate-950 p-4">
                        <img src="<?= htmlspecialchars(strpos($prod['image'], 'http') === 0 ? $prod['image'] : BASE_URL . $prod['image']) ?>" 
                             alt="<?= htmlspecialchars($prod['name']) ?>"
                             class="w-full h-full object-contain group-hover:scale-105 transition duration-300">
                    </a>

                    <!-- Thông tin -->
                    <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-cyan-400 tracking-wider">
                                <?= htmlspecialchars($prod['brand_name'] ?? $prod['category_name'] ?? 'Chính Hãng') ?>
                            </span>
                            <h3 class="text-sm font-bold text-white line-clamp-2 mt-1 group-hover:text-cyan-400 transition">
                                <a href="<?= BASE_URL ?>index.php?url=products/detail&slug=<?= urlencode($prod['slug']) ?>">
                                    <?= htmlspecialchars($prod['name']) ?>
                                </a>
                            </h3>
                        </div>

                        <div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-base font-black text-rose-400">
                                    <?= number_format($currentPrice, 0, ',', '.') ?>đ
                                </span>
                                <?php if ($oldPrice > $currentPrice): ?>
                                    <span class="text-xs text-slate-500 line-through">
                                        <?= number_format($oldPrice, 0, ',', '.') ?>đ
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="mt-2 flex items-center justify-between text-xs">
                                <?php if ($isAvailable): ?>
                                    <span class="text-emerald-400 font-semibold flex items-center gap-1 text-[11px]">
                                        <i class="fa-solid fa-circle-check"></i> Còn hàng
                                    </span>
                                <?php else: ?>
                                    <span class="text-rose-400 font-semibold flex items-center gap-1 text-[11px]">
                                        <i class="fa-solid fa-circle-xmark"></i> Hết hàng
                                    </span>
                                <?php endif; ?>

                                <span class="text-slate-500 text-[10px]">
                                    Lưu: <?= date('d/m/Y', strtotime($prod['wishlisted_at'])) ?>
                                </span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-800">
                            <a href="<?= BASE_URL ?>index.php?url=products/detail&slug=<?= urlencode($prod['slug']) ?>" 
                               class="w-full py-2 px-3 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 transition">
                                <i class="fa-solid fa-eye"></i> Xem Chi Tiết
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
