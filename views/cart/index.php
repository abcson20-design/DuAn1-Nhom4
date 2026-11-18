<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div class="flex items-center space-x-3">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white flex items-center">
            <i class="fa-solid fa-bag-shopping text-cyan-400 mr-3"></i> Giỏ Hàng Của Bạn
        </h2>
        <span class="bg-primary-500/20 text-cyan-300 border border-primary-500/40 text-xs font-bold px-2.5 py-0.5 rounded-full">
            <?= count($cart) ?> mặt hàng
        </span>
    </div>

    <!-- Thông báo Flash message nếu có -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 text-xs flex items-center justify-between">
            <div class="flex items-center"><i class="fa-solid fa-circle-check text-emerald-400 mr-2 text-sm"></i> <?= $_SESSION['flash_success'] ?></div>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

        <?php if (!empty($_SESSION['cart_error'])): ?>
        <div class="p-4 rounded-2xl bg-amber-950/70 border border-amber-500/50 text-amber-200 text-xs flex items-center justify-between">
            <div class="flex items-center">
                <i class="fa-solid fa-user-shield text-amber-400 mr-2.5 text-base"></i> 
                <span><?= htmlspecialchars($_SESSION['cart_error']) ?></span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <?php unset($_SESSION['cart_error']); ?>
    <?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="p-4 rounded-2xl bg-red-950/60 border border-red-500/40 text-red-300 text-xs flex items-center justify-between">
            <div class="flex items-center"><i class="fa-solid fa-triangle-exclamation text-red-400 mr-2 text-sm"></i> <?= $_SESSION['flash_error'] ?></div>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <?php if (empty($cart)): ?>
        <div class="glass-card rounded-3xl p-12 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-800/80 flex items-center justify-center mx-auto text-slate-500 text-2xl">
                <i class="fa-solid fa-cart-arrow-down"></i>
            </div>
            <h3 class="text-lg font-bold text-white">Giỏ hàng của bạn đang trống</h3>
            <p class="text-xs text-slate-400">Chưa có sản phẩm nào được chọn. Hãy khám phá ngay các mẫu điện thoại và phụ kiện hot nhất!</p>
            <div class="pt-2 flex justify-center gap-3">
                <a href="<?= BASE_URL ?>index.php?url=phones" class="bg-gradient-to-r from-primary-600 to-indigo-600 text-white text-xs font-bold px-6 py-3 rounded-xl shadow-lg shadow-primary-600/30">
                    <i class="fa-solid fa-mobile-screen mr-1.5"></i> Xem Điện Thoại
                </a>
                <a href="<?= BASE_URL ?>index.php?url=accessories" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold px-6 py-3 rounded-xl border border-slate-700">
                    <i class="fa-solid fa-headphones mr-1.5"></i> Xem Phụ Kiện
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Free Shipping Banner -->
        <div class="glass-card p-3.5 rounded-2xl border border-emerald-500/30 bg-emerald-950/20 text-xs text-emerald-300 flex items-center justify-between">
            <span class="flex items-center">
                <i class="fa-solid fa-truck-fast text-emerald-400 mr-2 text-sm"></i> 
                Chính sách ưu đãi: Đơn hàng của bạn được áp dụng <strong>MIỄN PHÍ VẬN CHUYỂN (0đ)</strong> toàn quốc!
            </span>
            <span class="bg-emerald-500/20 text-emerald-300 px-2.5 py-0.5 rounded-full font-bold">100% Free</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Cart Items Table -->
            <div class="lg:col-span-2 space-y-4">
                <div class="glass-card rounded-3xl overflow-hidden">
                    <form action="<?= BASE_URL ?>index.php?url=cart/update" method="POST">
                        <?= csrf_field() ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="border-b border-slate-800 text-[10px] text-slate-400 uppercase tracking-wider bg-slate-950/60">
                                        <th class="py-3.5 px-6">Sản phẩm</th>
                                        <th class="py-3.5 px-4 text-center">Đơn giá</th>
                                        <th class="py-3.5 px-4 text-center">Số lượng</th>
                                        <th class="py-3.5 px-4 text-right">Tạm tính</th>
                                        <th class="py-3.5 px-4 text-center">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/80">
                                    <?php foreach ($cart as $key => $item): ?>
                                        <tr class="hover:bg-slate-900/40 transition">
                                            <td class="py-4 px-6 flex items-center space-x-3">
                                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="" class="w-14 h-14 object-contain rounded-xl bg-slate-950 p-1 border border-slate-800">
                                                <div>
                                                    <a href="<?= BASE_URL ?>index.php?url=products/detail&slug=<?= urlencode($item['slug']) ?>" class="font-bold text-slate-200 hover:text-cyan-400 transition line-clamp-1">
                                                        <?= htmlspecialchars($item['name']) ?>
                                                    </a>
                                                    <div class="flex items-center gap-2 mt-0.5">
                                                        <span class="text-[10px] text-slate-500"><?= htmlspecialchars($item['category_name']) ?></span>
                                                        <?php if (!empty($item['color'])): ?>
                                                            <span class="text-[10px] bg-slate-800 text-cyan-300 border border-slate-700 px-1.5 py-0.5 rounded font-medium">
                                                                Màu: <?= htmlspecialchars($item['color']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-center font-medium text-slate-300">
                                                <?= number_format($item['price'], 0, ',', '.') ?>đ
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <div class="inline-flex items-center border border-slate-700 rounded-lg bg-slate-950 overflow-hidden">
                                                    <button type="button" onclick="let input = this.parentNode.querySelector('input'); input.value = Math.max(1, parseInt(input.value) - 1);" class="px-2 py-1 text-slate-400 hover:text-white">-</button>
                                                    <input type="number" name="quantities[<?= htmlspecialchars((string)$key) ?>]" value="<?= $item['quantity'] ?>" min="1" max="99" class="w-10 text-center text-xs bg-transparent text-white border-0 focus:outline-none">
                                                    <button type="button" onclick="let input = this.parentNode.querySelector('input'); input.value = parseInt(input.value) + 1;" class="px-2 py-1 text-slate-400 hover:text-white">+</button>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-right font-black text-cyan-400">
                                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <a href="<?= BASE_URL ?>index.php?url=cart/remove&key=<?= urlencode((string)$key) ?>&id=<?= $item['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')" class="text-slate-500 hover:text-rose-400 p-2 inline-flex items-center justify-center transition" title="Xóa khỏi giỏ hàng">
                                                    <i class="fa-regular fa-trash-can text-sm"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 bg-slate-950/60 border-t border-slate-800 flex flex-wrap gap-3 justify-between items-center">
                            <div class="flex items-center gap-4">
                                <a href="<?= BASE_URL ?>" class="text-xs text-slate-400 hover:text-cyan-400 transition flex items-center">
                                    <i class="fa-solid fa-arrow-left mr-1.5"></i> Tiếp tục mua hàng
                                </a>
                                <span class="text-slate-700">|</span>
                                <a href="<?= BASE_URL ?>index.php?url=cart/remove&clear_all=1" onclick="return confirm('Bạn có chắc chắn muốn xóa toàn bộ sản phẩm trong giỏ hàng?')" class="text-xs text-rose-400 hover:text-rose-300 font-semibold transition flex items-center">
                                    <i class="fa-regular fa-trash-can mr-1.5"></i> Xóa tất cả giỏ hàng
                                </a>
                            </div>
                            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold px-4 py-2 rounded-xl border border-slate-700 transition">
                                <i class="fa-solid fa-rotate mr-1.5"></i> Cập nhật giỏ hàng
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right: Order Summary & Coupon -->
            <div class="space-y-4">
                <div class="glass-card rounded-3xl p-6 space-y-4">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider border-b border-slate-800 pb-3">Tóm Tắt Đơn Hàng</h3>

                    <!-- Coupon Code Input -->
                    <form action="<?= BASE_URL ?>index.php?url=cart/coupon" method="POST" class="space-y-2">
                        <?= csrf_field() ?>
                        <label class="block text-[11px] font-medium text-slate-400">Mã giảm giá / Voucher tri ân</label>
                        <div class="flex gap-2">
                            <input type="text" name="coupon_code" value="<?= htmlspecialchars($couponCode) ?>" placeholder="Nhập TECH20 (-20%)" class="w-full text-xs p-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 uppercase focus:outline-none focus:ring-1 focus:ring-primary-500">
                            <button type="submit" class="bg-primary-600 hover:bg-primary-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition">Áp dụng</button>
                        </div>
                        <span class="text-[10px] text-cyan-400 block">Gợi ý: Mã khảo sát giảm 20%: <strong>TECH20</strong></span>
                    </form>

                    <div class="space-y-2 pt-2 border-t border-slate-800 text-xs">
                        <div class="flex justify-between text-slate-400">
                            <span>Tổng tiền hàng:</span>
                            <span class="text-slate-200 font-bold"><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                        </div>
                        <?php if ($discount > 0): ?>
                            <div class="flex justify-between text-emerald-400 font-bold">
                                <span>Giảm giá (Voucher):</span>
                                <span>-<?= number_format($discount, 0, ',', '.') ?>đ</span>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between text-slate-400">
                            <span>Phí vận chuyển:</span>
                            <span class="text-slate-200 font-medium"><?= ($shipping == 0) ? '<span class="text-emerald-400 font-bold"><i class="fa-solid fa-truck-fast mr-1"></i> Miễn phí (0đ)</span>' : number_format($shipping, 0, ',', '.') . 'đ' ?></span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex justify-between items-baseline">
                        <span class="text-sm font-bold text-white">Tổng thanh toán:</span>
                        <span class="text-2xl font-black text-cyan-400"><?= number_format($finalTotal, 0, ',', '.') ?>đ</span>
                    </div>

                    <?php 
$isAdmin = !empty($_SESSION['admin_user']) || (!empty($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin');
if ($isAdmin): 
?>
                    <div class="p-3.5 rounded-2xl bg-amber-950/50 border border-amber-500/40 text-amber-300 text-xs space-y-1.5 text-center">
                        <div class="font-bold flex items-center justify-center text-amber-400 text-xs">
                            <i class="fa-solid fa-user-shield mr-1.5"></i> Tài Khoản Quản Trị Viên (Admin)
                        </div>
                        <p class="text-[11px] text-slate-300">Tài khoản Admin không thể đặt hàng để đảm bảo tính khách quan và minh bạch.</p>
                    </div>
                    <button type="button" disabled class="w-full opacity-60 cursor-not-allowed text-center bg-slate-800 text-slate-400 font-bold text-xs py-4 px-6 rounded-2xl border border-slate-700">
                        <i class="fa-solid fa-ban mr-2"></i> Admin Không Thể Đặt Hàng
                    </button>
<?php else: ?>
                    <a href="<?= BASE_URL ?>index.php?url=checkout" class="w-full block text-center bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-500 hover:to-indigo-500 text-white font-bold text-xs py-4 px-6 rounded-2xl shadow-xl shadow-primary-600/30 transition transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-credit-card mr-2"></i> Tiến Hành Thanh Toán
                    </a>
<?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
