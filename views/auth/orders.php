<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-bold tracking-wide uppercase mb-2">
                <i class="fa-solid fa-box-archive"></i> Khu Vực Thành Viên
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white">Lịch Sử Đơn Hàng Của Bạn</h1>
            <p class="text-xs text-slate-400 mt-1">Xin chào <span class="text-cyan-400 font-bold"><?= htmlspecialchars($user['fullname'] ?? '') ?></span>! Đây là toàn bộ đơn hàng bạn đã đặt tại NEXVORA</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= BASE_URL ?>index.php?url=phones" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold py-2.5 px-4 rounded-xl transition flex items-center gap-2">
                <i class="fa-solid fa-mobile-screen"></i> Mua Điện Thoại
            </a>
            <a href="<?= BASE_URL ?>index.php?url=accessories" class="bg-gradient-to-r from-primary-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white text-xs font-extrabold py-2.5 px-4 rounded-xl shadow-lg shadow-primary-500/25 transition flex items-center gap-2">
                <i class="fa-solid fa-headphones"></i> Mua Phụ Kiện
            </a>
        </div>
    </div>

    <?php if (!empty($_SESSION['flash_user_order_msg'])): ?>
        <div class="p-4 bg-emerald-950/70 border border-emerald-500/50 text-emerald-300 text-xs rounded-2xl flex items-center justify-between shadow-lg shadow-emerald-950/40">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-lg text-emerald-400"></i>
                <span class="font-bold"><?= htmlspecialchars($_SESSION['flash_user_order_msg']) ?></span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <?php unset($_SESSION['flash_user_order_msg']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_user_order_err'])): ?>
        <div class="p-4 bg-rose-950/80 border border-rose-500/50 text-rose-200 text-xs rounded-2xl flex items-center justify-between shadow-lg shadow-rose-950/40">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-lg text-rose-400"></i>
                <span class="font-bold"><?= htmlspecialchars($_SESSION['flash_user_order_err']) ?></span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <?php unset($_SESSION['flash_user_order_err']); ?>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div class="glass-card rounded-3xl p-12 text-center max-w-lg mx-auto space-y-5 border border-slate-700/80">
            <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 text-primary-400 text-3xl flex items-center justify-center mx-auto">
                <i class="fa-solid fa-basket-shopping"></i>
            </div>
            <h3 class="text-lg font-bold text-white">Bạn chưa có đơn hàng nào</h3>
            <p class="text-xs text-slate-400">Hãy khám phá bộ sưu tập smartphone đỉnh cao cùng tai nghe và phụ kiện chính hãng tại NEXVORA ngay!</p>
            <a href="<?= BASE_URL ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white font-extrabold text-xs py-3 px-6 rounded-xl shadow-lg transition">
                <i class="fa-solid fa-cart-shopping"></i> Bắt Đầu Mua Sắm
            </a>
        </div>
    <?php else: ?>
        <?php
        $countAll = count($orders);
        $countPending = count(array_filter($orders, fn($o) => $o['order_status'] === 'pending'));
        $countConfirmed = count(array_filter($orders, fn($o) => $o['order_status'] === 'confirmed'));
        $countShipping = count(array_filter($orders, fn($o) => $o['order_status'] === 'shipping'));
        $countCompleted = count(array_filter($orders, fn($o) => $o['order_status'] === 'completed'));
        $countCancelled = count(array_filter($orders, fn($o) => $o['order_status'] === 'cancelled'));
        $countReturned = count(array_filter($orders, fn($o) => in_array($o['order_status'], ['returned', 'return_requested'], true)));
        ?>

        <!-- THANH TABS TRẠNG THÁI CHUẨN SHOPEE -->
        <div class="glass-card rounded-2xl p-1.5 border border-slate-800 bg-slate-950/70 flex items-center overflow-x-auto no-scrollbar gap-1 text-xs font-bold shadow-lg">
            <button type="button" onclick="filterShopeeOrders('all', this)" class="shopee-tab-btn active px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap bg-gradient-to-r from-primary-600 to-cyan-500 text-white shadow-md shadow-primary-500/20">
                <span>Tất Cả</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-900/60 text-[10px]"><?= $countAll ?></span>
            </button>
            <button type="button" onclick="filterShopeeOrders('pending', this)" class="shopee-tab-btn px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap text-slate-400 hover:text-white hover:bg-slate-900">
                <span>Chờ Xác Nhận</span>
                <?php if ($countPending > 0): ?>
                    <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px]"><?= $countPending ?></span>
                <?php endif; ?>
            </button>
            <button type="button" onclick="filterShopeeOrders('confirmed', this)" class="shopee-tab-btn px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap text-slate-400 hover:text-white hover:bg-slate-900">
                <span>Chờ Lấy Hàng</span>
                <?php if ($countConfirmed > 0): ?>
                    <span class="px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-[10px]"><?= $countConfirmed ?></span>
                <?php endif; ?>
            </button>
            <button type="button" onclick="filterShopeeOrders('shipping', this)" class="shopee-tab-btn px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap text-slate-400 hover:text-white hover:bg-slate-900">
                <span>Đang Giao</span>
                <?php if ($countShipping > 0): ?>
                    <span class="px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[10px]"><?= $countShipping ?></span>
                <?php endif; ?>
            </button>
            <button type="button" onclick="filterShopeeOrders('completed', this)" class="shopee-tab-btn px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap text-slate-400 hover:text-white hover:bg-slate-900">
                <span>Đã Giao</span>
                <?php if ($countCompleted > 0): ?>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px]"><?= $countCompleted ?></span>
                <?php endif; ?>
            </button>
            <button type="button" onclick="filterShopeeOrders('cancelled', this)" class="shopee-tab-btn px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap text-slate-400 hover:text-white hover:bg-slate-900">
                <span>Đã Hủy</span>
                <?php if ($countCancelled > 0): ?>
                    <span class="px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[10px]"><?= $countCancelled ?></span>
                <?php endif; ?>
            </button>
            <button type="button" onclick="filterShopeeOrders('returned', this)" class="shopee-tab-btn px-4 py-2.5 rounded-xl transition flex items-center gap-2 whitespace-nowrap text-slate-400 hover:text-white hover:bg-slate-900">
                <span>Trả Hàng/Hoàn Tiền</span>
                <?php if ($countReturned > 0): ?>
                    <span class="px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[10px]"><?= $countReturned ?></span>
                <?php endif; ?>
            </button>
        </div>

        <div class="space-y-6" id="ordersListContainer">
            <?php foreach ($orders as $order): ?>
                <div class="glass-card shopee-order-card rounded-3xl p-6 sm:p-7 border border-slate-700/80 space-y-5 hover:border-cyan-500/40 transition duration-300" data-status="<?= htmlspecialchars($order['order_status']) ?>">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 font-bold">
                                <i class="fa-solid fa-file-invoice"></i>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 block">Mã đơn hàng:</span>
                                <span class="text-sm font-extrabold text-cyan-400 tracking-wider font-mono">#<?= htmlspecialchars($order['order_code']) ?></span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Trạng thái thanh toán -->
                            <?php if ($order['order_status'] === 'cancelled'): ?>
                                <?php if ($order['payment_status'] === 'paid'): ?>
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30 flex items-center gap-1.5 animate-pulse">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Chờ Xử Lý Hoàn Tiền
                                    </span>
                                <?php elseif ($order['payment_status'] === 'refunded'): ?>
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/30 flex items-center gap-1.5">
                                        <i class="fa-solid fa-rotate-left"></i> Đã Hoàn Tiền
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-slate-800 text-slate-400 border border-slate-700 flex items-center gap-1.5">
                                        <i class="fa-solid fa-ban"></i> Đã Hủy Đơn
                                    </span>
                                <?php endif; ?>
                            <?php elseif ($order['payment_status'] === 'refunded'): ?>
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/30 flex items-center gap-1.5">
                                    <i class="fa-solid fa-rotate-left"></i> Đã Tiếp Nhận Hoàn Tiền
                                </span>
                            <?php elseif ($order['payment_status'] === 'paid'): ?>
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check"></i> Đã Thanh Toán Online
                                </span>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30 flex items-center gap-1.5">
                                    <i class="fa-solid fa-clock"></i> Chưa Thanh Toán
                                </span>
                            <?php endif; ?>

                            <!-- Trạng thái đơn hàng -->
                            <?php
                                $statusMap = [
                                    'pending' => ['bg' => 'bg-amber-500/10 text-amber-400 border-amber-500/30', 'text' => 'Chờ xác nhận', 'icon' => 'fa-hourglass-start'],
                                    'confirmed' => ['bg' => 'bg-blue-500/10 text-blue-400 border-blue-500/30', 'text' => 'Đã xác nhận', 'icon' => 'fa-check'],
                                    'shipping' => ['bg' => 'bg-purple-500/10 text-purple-400 border-purple-500/30', 'text' => 'Đang giao hàng', 'icon' => 'fa-truck-fast'],
                                    'completed' => ['bg' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30', 'text' => 'Giao thành công', 'icon' => 'fa-circle-check'],
                                    'return_requested' => ['bg' => 'bg-purple-500/10 text-purple-400 border-purple-500/30', 'text' => 'Chờ duyệt trả hàng', 'icon' => 'fa-rotate-left'],
                                    'returned' => ['bg' => 'bg-purple-500/10 text-purple-400 border-purple-500/30', 'text' => 'Đã trả hàng / Hoàn tiền', 'icon' => 'fa-rotate-left'],
                                    'cancelled' => ['bg' => 'bg-rose-500/10 text-rose-400 border-rose-500/30', 'text' => 'Đã hủy', 'icon' => 'fa-ban'],
                                ];
                                $st = $statusMap[$order['order_status']] ?? $statusMap['pending'];
                            ?>
                            <span class="px-3 py-1 rounded-full text-[11px] font-bold border <?= $st['bg'] ?> flex items-center gap-1.5">
                                <i class="fa-solid <?= $st['icon'] ?>"></i> <?= $st['text'] ?>
                            </span>

                            <span class="text-xs text-slate-500 ml-2">
                                <i class="fa-regular fa-clock mr-1"></i> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Khối thông tin chi tiết nếu đơn ĐÃ HỦY -->
                    <?php if ($order['order_status'] === 'cancelled'): ?>
                        <div class="p-3.5 bg-rose-950/40 border border-rose-500/30 rounded-2xl text-xs space-y-1.5 text-rose-300">
                            <div class="flex items-center gap-2 font-bold">
                                <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm"></i> 
                                <span>Đơn hàng đã được hủy</span>
                            </div>
                            <?php if (!empty($order['cancel_reason'])): ?>
                                <div class="text-[11px] text-slate-300">Lý do hủy: <span class="text-rose-300 font-semibold"><?= htmlspecialchars($order['cancel_reason']) ?></span></div>
                            <?php endif; ?>
                            <?php if (!empty($order['refund_info'])): ?>
                                <div class="text-[11px] text-purple-200 bg-purple-950/50 border border-purple-800/60 p-2.5 rounded-xl mt-1 space-y-1">
                                    <div class="flex items-center gap-1.5 font-bold text-purple-300">
                                        <i class="fa-solid fa-money-bill-transfer"></i> Thông tin nhận hoàn tiền:
                                    </div>
                                    <div class="text-slate-200 font-mono"><?= htmlspecialchars($order['refund_info']) ?></div>
                                    <div class="text-[10px] text-purple-400">Số tiền <strong class="text-white"><?= number_format($order['final_amount'], 0, ',', '.') ?>đ</strong> sẽ được kế toán NEXVORA hoàn trả về tài khoản trên trong vòng 24h làm việc.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Khối thông tin chi tiết nếu đơn ĐÃ TRẢ HÀNG & HOÀN TIỀN -->
                    <?php if (in_array($order['order_status'], ['returned', 'return_requested'], true)): ?>
                        <?php $isReturnPending = ($order['order_status'] === 'return_requested'); ?>
                        <div class="p-3.5 bg-purple-950/40 border border-purple-500/30 rounded-2xl text-xs space-y-1.5 text-purple-300">
                            <div class="flex items-center gap-2 font-bold text-purple-300">
                                <i class="fa-solid fa-rotate-left text-purple-400 text-sm"></i> 
                                <span><?= $isReturnPending ? 'Đã gửi yêu cầu Trả hàng - Đang chờ cửa hàng duyệt' : 'Đơn hàng đã được Trả hàng & Đang hoàn tiền' ?></span>
                            </div>
                            <?php if (!empty($order['cancel_reason'])): ?>
                                <div class="text-[11px] text-slate-300">Lý do trả hàng: <span class="text-purple-300 font-semibold"><?= htmlspecialchars($order['cancel_reason']) ?></span></div>
                            <?php endif; ?>
                            <?php if (!empty($order['refund_info'])): ?>
                                <div class="text-[11px] text-purple-200 bg-purple-950/50 border border-purple-800/60 p-2.5 rounded-xl mt-1 space-y-1">
                                    <div class="flex items-center gap-1.5 font-bold text-purple-300">
                                        <i class="fa-solid fa-money-bill-transfer"></i> Thông tin nhận hoàn tiền:
                                    </div>
                                    <div class="text-slate-200 font-mono"><?= htmlspecialchars($order['refund_info']) ?></div>
                                    <?php if ($isReturnPending): ?>
                                    <div class="text-[10px] text-purple-400">Số tiền <strong class="text-white"><?= number_format($order['final_amount'], 0, ',', '.') ?>đ</strong> sẽ được hoàn về tài khoản trên sau khi cửa hàng nhận lại và kiểm tra sản phẩm.</div>
                                    <?php elseif ($order['payment_status'] === 'refunded'): ?>
                                    <div class="text-[10px] text-purple-400">Số tiền <strong class="text-white"><?= number_format($order['final_amount'], 0, ',', '.') ?>đ</strong> đã được ghi nhận hoàn lại và trừ khỏi doanh thu hệ thống.</div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Danh sách sản phẩm trong đơn -->
                    <div class="space-y-3">
                        <?php if (!empty($order['items'])): ?>
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="flex items-center justify-between text-xs py-1.5">
                                    <div class="flex items-center gap-3">
                                        <img src="<?= htmlspecialchars($item['product_image'] ?: 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=100') ?>" 
                                             alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                             class="w-12 h-12 object-cover rounded-xl border border-slate-700 flex-shrink-0">
                                        <div>
                                            <div class="font-bold text-white"><?= htmlspecialchars($item['product_name']) ?></div>
                                            <div class="text-slate-400 text-[11px] mt-0.5">Số lượng: <span class="text-slate-200 font-bold"><?= $item['quantity'] ?></span> × <?= number_format($item['price'], 0, ',', '.') ?>₫</div>
                                        </div>
                                    </div>
                                    <div class="font-bold text-cyan-400 text-sm">
                                        <?= number_format($item['total'], 0, ',', '.') ?>₫
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Footer của Đơn Hàng -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4 border-t border-slate-800 text-xs">
                        <div class="text-slate-400 space-y-1">
                            <div><i class="fa-solid fa-location-dot text-rose-400 mr-1.5"></i> Giao đến: <span class="text-slate-200"><?= htmlspecialchars($order['customer_address']) ?></span></div>
                            <div>
                                <i class="fa-solid fa-credit-card text-cyan-400 mr-1.5"></i> Thanh toán: 
                                <span class="text-slate-200 font-bold uppercase">
                                    <?= $order['payment_method'] === 'sepay' ? 'Cổng SePay' : ($order['payment_method'] === 'cod' ? 'COD Khi Nhận Hàng' : htmlspecialchars(strtoupper($order['payment_method']))) ?>
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <div class="text-right mr-1">
                                <span class="text-slate-400 text-[11px] block">Tổng thanh toán:</span>
                                <span class="text-lg font-black text-cyan-400"><?= number_format($order['final_amount'], 0, ',', '.') ?>₫</span>
                            </div>

                            <!-- Nút Thanh Toán SePay (Cho phép khi đơn chưa thanh toán và chưa bị huỷ) -->
                            <?php if ($order['payment_status'] !== 'paid' && $order['order_status'] !== 'cancelled'): ?>
                                <a href="<?= BASE_URL ?>index.php?url=checkout/change-payment-method&code=<?= urlencode($order['order_code']) ?>&method=sepay" 
                                   class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-extrabold text-xs py-2.5 px-3.5 rounded-xl shadow-lg shadow-cyan-500/25 transition flex items-center gap-1.5 whitespace-nowrap">
                                    <i class="fa-solid fa-bolt text-amber-300"></i> Thanh Toán SePay
                                </a>
                            <?php endif; ?>

                            <!-- Nút Mua Lại Chuẩn Shopee (Cho phép trên mọi đơn) -->
                            <button type="button" 
                                    onclick="reorderShopee('<?= htmlspecialchars($order['order_code']) ?>')" 
                                    class="bg-cyan-500/10 hover:bg-cyan-500 text-cyan-300 hover:text-white border border-cyan-500/30 text-xs font-bold py-2.5 px-3.5 rounded-xl transition flex items-center gap-1.5 whitespace-nowrap shadow-sm"
                                    title="Thêm các sản phẩm của đơn hàng này vào giỏ hàng để mua lại">
                                <i class="fa-solid fa-rotate-right text-xs"></i> Mua Lại
                            </button>

                            <!-- Nút Xem Tiến Trình Đơn Hàng Shopee -->
                            <button type="button" 
                                    onclick="openTimelineModal('<?= htmlspecialchars($order['order_code']) ?>')" 
                                    class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold py-2.5 px-3.5 rounded-xl transition flex items-center gap-1.5 whitespace-nowrap shadow-sm"
                                    title="Xem tiến trình giao nhận chi tiết">
                                <i class="fa-solid fa-timeline text-cyan-400"></i> Tiến Trình
                            </button>

                            <a href="<?= BASE_URL ?>index.php?url=order/track&q=<?= urlencode($order['order_code']) ?>" 
                               class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold py-2.5 px-4 rounded-xl transition flex items-center gap-2">
                                <i class="fa-solid fa-route text-cyan-400"></i> Tra Cứu
                            </a>

                            <!-- Nút Hủy Đơn Hàng (Cho phép khi đơn chưa xuất kho giao) -->
                            <?php if (in_array($order['order_status'], ['pending', 'confirmed'])): ?>
                                <button type="button" 
                                        onclick="openCancelOrderModal('<?= htmlspecialchars($order['order_code']) ?>', '<?= htmlspecialchars($order['payment_status']) ?>', '<?= number_format($order['final_amount'], 0, ',', '.') ?>đ', '<?= htmlspecialchars($order['customer_phone'] ?? '') ?>')"
                                        class="bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 text-xs font-bold py-2.5 px-3.5 rounded-xl transition flex items-center gap-1.5 whitespace-nowrap shadow-sm"
                                        title="Hủy đơn hàng này">
                                    <i class="fa-solid fa-xmark text-xs"></i> Hủy Đơn
                                </button>
                            <?php endif; ?>

                            <!-- Nút Xác Nhận Đã Nhận Được Hàng (Chỉ Client mới được xác nhận khi đơn đang giao) -->
                            <?php if ($order['order_status'] === 'shipping'): ?>
                                <form action="<?= BASE_URL ?>index.php?url=order/received" method="POST" onsubmit="return confirm('Bạn xác nhận đã nhận được đầy đủ kiện hàng và hài lòng với đơn hàng?');">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="order_code" value="<?= htmlspecialchars($order['order_code']) ?>">
                                    <button type="submit" class="bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-extrabold text-xs py-2.5 px-4 rounded-xl shadow-lg shadow-emerald-500/25 transition flex items-center gap-1.5 whitespace-nowrap">
                                        <i class="fa-solid fa-box-open text-xs"></i> Đã Nhận Hàng
                                    </button>
                                </form>
                            <?php elseif ($order['order_status'] === 'completed'): ?>
                                <span class="text-[11px] text-emerald-400 font-bold bg-emerald-950/60 border border-emerald-800/80 px-3 py-2 rounded-xl flex items-center gap-1.5 whitespace-nowrap">
                                    <i class="fa-solid fa-circle-check"></i> Đơn Đã Hoàn Thành
                                </span>
                            <?php endif; ?>

                            <!-- Nút Yêu Cầu Trả Hàng & Hoàn Tiền (Theo chính sách: ĐT 6 tháng, Tai nghe Bluetooth 7 ngày, Phụ kiện khác khi xem hàng) -->
                            <?php if (!empty($order['return_eligibility']['can_return'])): ?>
                                <button type="button" 
                                        onclick="openReturnOrderModal('<?= htmlspecialchars($order['order_code']) ?>', '<?= number_format($order['final_amount'], 0, ',', '.') ?>đ', '<?= htmlspecialchars($order['return_eligibility']['badge_text'] ?? '') ?>', '<?= htmlspecialchars(addslashes($order['return_eligibility']['message'] ?? '')) ?>', '<?= htmlspecialchars($order['customer_phone'] ?? '') ?>')"
                                        class="bg-purple-600/20 hover:bg-purple-600 text-purple-300 hover:text-white border border-purple-500/40 text-xs font-bold py-2.5 px-3.5 rounded-xl transition flex items-center gap-1.5 whitespace-nowrap shadow-sm"
                                        title="<?= htmlspecialchars($order['return_eligibility']['message'] ?? '') ?>">
                                    <i class="fa-solid fa-rotate-left text-xs"></i> <span>Trả Hàng & Hoàn Tiền</span>
                                </button>
                            <?php elseif ($order['order_status'] === 'completed' && !empty($order['return_eligibility']['badge_text'])): ?>
                                <span class="text-[10px] text-slate-400 bg-slate-800/80 border border-slate-700/80 px-2.5 py-1.5 rounded-xl flex items-center gap-1 whitespace-nowrap" title="<?= htmlspecialchars($order['return_eligibility']['message'] ?? '') ?>">
                                    <i class="fa-solid fa-shield-halved text-slate-500"></i> <?= htmlspecialchars($order['return_eligibility']['badge_text']) ?>
                                </span>
                            <?php endif; ?>

                            <!-- Nút Xóa Đơn (Chỉ ẩn khỏi lịch sử cá nhân của khách hàng, không ảnh hưởng phía admin) -->
                            <form action="<?= BASE_URL ?>index.php?url=order/delete-user-order" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng #<?= htmlspecialchars($order['order_code']) ?> khỏi danh sách lịch sử của bạn? (Đơn hàng vẫn được hệ thống lưu trữ phục vụ bảo hành/hỗ trợ)');" class="inline-block">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="order_code" value="<?= htmlspecialchars($order['order_code']) ?>">
                                <button type="submit" class="bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 border border-slate-700 hover:border-rose-500/40 text-xs font-bold py-2.5 px-3 rounded-xl transition flex items-center gap-1.5 whitespace-nowrap shadow-sm" title="Xóa khỏi danh sách đơn hàng của bạn">
                                    <i class="fa-regular fa-trash-can text-xs"></i> Xóa Đơn
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- POPUP MODAL HỦY ĐƠN HÀNG & THÔNG TIN HOÀN TIỀN -->
<div id="cancelOrderModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-rose-500/20 border border-rose-500/30 flex items-center justify-center text-rose-400 font-bold text-sm">
                    <i class="fa-solid fa-ban"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>Hủy Đơn Hàng</span>
                        <span id="modalOrderCodeBadge" class="font-mono text-cyan-400 text-xs"></span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Xác nhận hủy đơn và ghi nhận thông tin hoàn tiền</p>
                </div>
            </div>
            <button type="button" onclick="closeCancelOrderModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="<?= BASE_URL ?>index.php?url=order/cancel" method="POST" class="p-6 space-y-4 text-xs">
            <?= Csrf::field() ?>
            <input type="hidden" name="order_code" id="modalOrderCodeInput" value="">
            <input type="hidden" name="customer_phone" id="modalCustomerPhoneInput" value="">
            <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '') ?>">

            <!-- Khối thông báo hoàn tiền nếu đơn ĐÃ THANH TOÁN -->
            <div id="refundNoticeBox" class="hidden p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-200 space-y-3">
                <div class="flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-amber-400 text-base mt-0.5 shrink-0"></i>
                    <div>
                        <strong class="text-white block font-bold">Đơn hàng này ĐÃ THANH TOÁN ONLINE</strong>
                        <span class="text-[11px] text-slate-300">
                            Số tiền thanh toán: <strong id="modalRefundAmount" class="text-cyan-400 font-bold"></strong>. 
                            Vui lòng cung cấp tài khoản ngân hàng để NEXVORA chuyển tiền hoàn lại 100% cho bạn.
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">Tên Ngân Hàng <span class="text-rose-400">*</span></label>
                        <select name="bank_name" id="modalBankName" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                            <option value="">-- Chọn ngân hàng --</option>
                            <option value="Vietcombank">Vietcombank (VCB)</option>
                            <option value="MBBank">MB Bank (Quân Đội)</option>
                            <option value="Techcombank">Techcombank (TCB)</option>
                            <option value="BIDV">BIDV</option>
                            <option value="VietinBank">VietinBank</option>
                            <option value="ACB">ACB</option>
                            <option value="VPBank">VPBank</option>
                            <option value="TPBank">TPBank</option>
                            <option value="Agribank">Agribank</option>
                            <option value="HDBank">HDBank</option>
                            <option value="Sacombank">Sacombank</option>
                            <option value="MoMo">Ví MoMo</option>
                            <option value="Khác">Ngân hàng khác</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">Số Tài Khoản (STK) <span class="text-rose-400">*</span></label>
                        <input type="text" name="account_number" id="modalAccountNumber" placeholder="VD: 0987654321..." class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-400">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">Tên Chủ Tài Khoản (Viết hoa không dấu) <span class="text-rose-400">*</span></label>
                        <input type="text" name="account_holder" id="modalAccountHolder" placeholder="VD: NGUYEN VAN A" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white uppercase focus:outline-none focus:border-cyan-400">
                    </div>
                </div>
            </div>

            <!-- Lý do hủy đơn -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-300">
                    Vui lòng chọn lý do hủy đơn: <span class="text-rose-400">*</span>
                </label>
                <select name="cancel_reason" id="modalCancelReasonSelect" onchange="toggleCustomReason(this.value)" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400" required>
                    <option value="Đổi ý, không còn nhu cầu mua nữa">Đổi ý, không còn nhu cầu mua nữa</option>
                    <option value="Muốn thay đổi sản phẩm, phiên bản màu hoặc dung lượng">Muốn thay đổi sản phẩm, phiên bản màu hoặc dung lượng</option>
                    <option value="Thời gian giao hàng lâu hoặc không phù hợp">Thời gian giao hàng lâu hoặc không phù hợp</option>
                    <option value="Đặt nhầm thông tin địa chỉ hoặc số điện thoại">Đặt nhầm thông tin địa chỉ hoặc số điện thoại</option>
                    <option value="Tìm thấy giá rẻ hơn ở cửa hàng khác">Tìm thấy giá rẻ hơn ở cửa hàng khác</option>
                    <option value="other">Lý do khác...</option>
                </select>

                <div id="customReasonBox" class="hidden mt-2">
                    <textarea name="custom_reason" id="modalCustomReason" rows="2" placeholder="Nhập lý do chi tiết của bạn..." class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-cyan-400"></textarea>
                </div>
            </div>

            <!-- Tùy chọn ẩn khỏi danh sách của tôi sau khi hủy -->
            <div class="pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-slate-300 text-xs select-none">
                    <input type="checkbox" name="hide_after_cancel" value="1" class="w-4 h-4 rounded text-rose-600 bg-slate-950 border-slate-700 focus:ring-0">
                    <span>Đồng thời xóa đơn hàng này khỏi danh sách đơn của tôi sau khi hủy</span>
                </label>
            </div>

            <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeCancelOrderModal()" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 text-xs font-semibold transition">
                    Đóng
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-extrabold text-xs shadow-lg shadow-rose-600/30 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Xác Nhận Hủy Đơn</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TIẾN TRÌNH ĐƠN HÀNG CHUẨN SHOPEE (ORDER TIMELINE STEPPER) -->
<div id="timelineModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-cyan-500/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 font-bold text-sm">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>Hành Trình Đơn Hàng</span>
                        <span id="timelineOrderCodeBadge" class="font-mono text-cyan-400 text-xs"></span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Tiến trình vận chuyển và cập nhật trạng thái thời gian thực</p>
                </div>
            </div>
            <button type="button" onclick="closeTimelineModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-6 space-y-6" id="timelineContent">
            <!-- Nội dung timeline stepper được render bằng JavaScript -->
        </div>

        <div class="p-4 bg-slate-950/60 border-t border-slate-800 text-right">
            <button type="button" onclick="closeTimelineModal()" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition">
                Đóng
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// 1. Lọc đơn hàng theo Tab trạng thái Shopee (0ms lag)
function filterShopeeOrders(status, activeBtn) {
    // Đổi active style cho tabs
    document.querySelectorAll('.shopee-tab-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-gradient-to-r', 'from-primary-600', 'to-cyan-500', 'text-white', 'shadow-md', 'shadow-primary-500/20');
        btn.classList.add('text-slate-400', 'hover:text-white', 'hover:bg-slate-900');
    });

    if (activeBtn) {
        activeBtn.classList.remove('text-slate-400', 'hover:text-white', 'hover:bg-slate-900');
        activeBtn.classList.add('active', 'bg-gradient-to-r', 'from-primary-600', 'to-cyan-500', 'text-white', 'shadow-md', 'shadow-primary-500/20');
    }

    const cards = document.querySelectorAll('.shopee-order-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const orderStatus = card.getAttribute('data-status');
        let show = false;

        if (status === 'all') {
            show = true;
        } else if (status === 'pending' && orderStatus === 'pending') {
            show = true;
        } else if (status === 'confirmed' && orderStatus === 'confirmed') {
            show = true;
        } else if (status === 'shipping' && orderStatus === 'shipping') {
            show = true;
        } else if (status === 'completed' && orderStatus === 'completed') {
            show = true;
        } else if (status === 'cancelled' && orderStatus === 'cancelled') {
            show = true;
        } else if (status === 'returned' && (orderStatus === 'returned' || orderStatus === 'return_requested')) {
            show = true;
        }

        if (show) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
}

// 2. Mua lại đơn cũ kiểu Shopee (Re-order)
async function reorderShopee(orderCode) {
    try {
        const formData = new FormData();
        formData.append('order_code', orderCode);

        Swal.fire({
            title: 'Đang xử lý...',
            text: 'Đang đưa sản phẩm vào giỏ hàng',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            background: '#0f172a',
            color: '#fff'
        });

        const res = await fetch('<?= BASE_URL ?>index.php?url=api/orders/reorder', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Mua lại thành công!',
                text: data.message,
                confirmButtonText: 'Đến giỏ hàng ngay',
                confirmButtonColor: '#06b6d4',
                background: '#0f172a',
                color: '#fff'
            }).then(() => {
                window.location.href = data.redirect_url;
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Không thể mua lại',
                text: data.message || 'Sản phẩm trong đơn có thể đã hết hàng',
                background: '#0f172a',
                color: '#fff'
            });
        }
    } catch (e) {
        console.error(e);
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Đã có lỗi kết nối, vui lòng thử lại',
            background: '#0f172a',
            color: '#fff'
        });
    }
}

// 3. Mở Modal Timeline Tiến Trình Shopee
async function openTimelineModal(orderCode) {
    const modal = document.getElementById('timelineModal');
    const badge = document.getElementById('timelineOrderCodeBadge');
    const content = document.getElementById('timelineContent');
    badge.textContent = '#' + orderCode;
    content.innerHTML = '<div class="text-center py-8 text-slate-400"><i class="fa-solid fa-spinner fa-spin text-2xl text-cyan-400 mb-2"></i><br>Đang tải hành trình đơn hàng...</div>';
    modal.classList.remove('hidden');

    try {
        const res = await fetch(`<?= BASE_URL ?>index.php?url=api/orders/track&code=${encodeURIComponent(orderCode)}`);
        const data = await res.json();

        if (data.success && data.timeline) {
            let html = '<div class="relative border-l-2 border-cyan-500/40 ml-4 space-y-6 pl-6">';
            data.timeline.forEach((step, idx) => {
                const isDone = step.completed;
                const dotColor = isDone ? 'bg-cyan-500 ring-4 ring-cyan-500/20 text-white' : 'bg-slate-800 border-2 border-slate-700 text-slate-500';
                const textColor = isDone ? 'text-white' : 'text-slate-400';

                html += `
                    <div class="relative">
                        <span class="absolute -left-[35px] top-0.5 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold ${dotColor}">
                            ${isDone ? '<i class="fa-solid fa-check text-[10px]"></i>' : (idx + 1)}
                        </span>
                        <div>
                            <h4 class="text-xs font-bold ${textColor}">${step.title}</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">${step.description}</p>
                            ${step.time ? `<span class="text-[10px] text-cyan-400 font-mono mt-1 inline-block"><i class="fa-regular fa-clock mr-1"></i>${step.time}</span>` : ''}
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            content.innerHTML = html;
        } else {
            content.innerHTML = '<div class="text-center py-4 text-rose-400">Không tìm thấy thông tin tiến trình đơn hàng.</div>';
        }
    } catch (e) {
        content.innerHTML = '<div class="text-center py-4 text-rose-400">Lỗi kết nối khi tải tiến trình.</div>';
    }
}

function closeTimelineModal() {
    document.getElementById('timelineModal').classList.add('hidden');
}

// ----------------------------------------------------
// LIVE REALTIME SSE (SERVER-SENT EVENTS) & POLLING CHO CLIENT ĐANG XEM DANH SÁCH ĐƠN HÀNG
// ----------------------------------------------------
<?php if (!empty($orders)): ?>
const clientOrderCodes = <?= json_encode(array_column($orders, 'order_code')) ?>;
const clientOrderStatuses = <?= json_encode(array_combine(array_column($orders, 'order_code'), array_column($orders, 'order_status'))) ?>;

const statusNameMap = {
    'pending': 'Chờ xác nhận',
    'confirmed': 'Đã xác nhận (Đang đóng gói)',
    'shipping': 'Đang giao hàng (Đã bàn giao ĐVVC)',
    'completed': 'Giao hàng thành công',
    'return_requested': 'Yêu cầu trả hàng đã được gửi',
    'returned': 'Đã hoàn tất trả hàng & hoàn tiền',
    'cancelled': 'Đơn hàng đã hủy'
};

function handleStatusUpdate(code, newStatus) {
    if (clientOrderStatuses[code] && clientOrderStatuses[code] !== newStatus) {
        clientOrderStatuses[code] = newStatus;
        const statusText = statusNameMap[newStatus] || newStatus;
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: '⚡ Realtime SSE: Đơn hàng cập nhật!',
                text: `Đơn #${code} vừa chuyển sang: "${statusText}"`,
                timer: 2000,
                showConfirmButton: false
            });
        }
        setTimeout(() => location.reload(), 1500);
    }
}

// 1. Tận dụng SSE Server-Sent Events với độ trễ cực thấp (Milliseconds)
if (window.EventSource) {
    try {
        const sseSource = new EventSource('<?= BASE_URL ?>index.php?url=api/orders/sse');
        sseSource.addEventListener('user_orders_update', function (e) {
            try {
                const data = JSON.parse(e.data);
                if (data && Array.isArray(data.orders)) {
                    data.orders.forEach(o => {
                        handleStatusUpdate(o.order_code, o.order_status);
                    });
                }
            } catch (err) {}
        });
        sseSource.onerror = function () {
            // Tự động đóng nếu ngắt kết nối để fallback sang polling
            sseSource.close();
        };
    } catch (e) {}
}

// 2. Polling dự phòng đảm bảo dữ liệu không bao giờ bị trễ
setInterval(async () => {
    for (const code of clientOrderCodes) {
        try {
            const res = await fetch(`<?= BASE_URL ?>index.php?url=api/orders/status&code=${encodeURIComponent(code)}`);
            if (!res.ok) continue;
            const data = await res.json();
            if (data.success && data.order_status) {
                handleStatusUpdate(code, data.order_status);
            }
        } catch(e) {}
    }
}, 2500);
<?php endif; ?>
</script>

<script>
function openCancelOrderModal(orderCode, paymentStatus, finalAmount, phone) {
    document.getElementById('modalOrderCodeBadge').textContent = '#' + orderCode;
    document.getElementById('modalOrderCodeInput').value = orderCode;
    document.getElementById('modalCustomerPhoneInput').value = phone || '';

    const refundBox = document.getElementById('refundNoticeBox');
    const bankName = document.getElementById('modalBankName');
    const accNum = document.getElementById('modalAccountNumber');
    const accHolder = document.getElementById('modalAccountHolder');

    if (paymentStatus === 'paid') {
        refundBox.classList.remove('hidden');
        document.getElementById('modalRefundAmount').textContent = finalAmount;
        bankName.required = true;
        accNum.required = true;
        accHolder.required = true;
    } else {
        refundBox.classList.add('hidden');
        bankName.required = false;
        accNum.required = false;
        accHolder.required = false;
    }

    document.getElementById('cancelOrderModal').classList.remove('hidden');
}

function closeCancelOrderModal() {
    document.getElementById('cancelOrderModal').classList.add('hidden');
}

function toggleCustomReason(val) {
    const box = document.getElementById('customReasonBox');
    if (val === 'other') {
        box.classList.remove('hidden');
        document.getElementById('modalCustomReason').required = true;
    } else {
        box.classList.add('hidden');
        document.getElementById('modalCustomReason').required = false;
    }
}

function openReturnOrderModal(orderCode, finalAmount, badgeText, policyMsg, phone) {
    document.getElementById('returnModalOrderCodeBadge').textContent = '#' + orderCode;
    document.getElementById('returnModalOrderCodeInput').value = orderCode;
    document.getElementById('returnModalCustomerPhoneInput').value = phone || '';
    document.getElementById('returnModalRefundAmount').textContent = finalAmount;
    document.getElementById('returnModalPolicyBadge').textContent = badgeText;
    document.getElementById('returnModalPolicyMsg').textContent = policyMsg;

    document.getElementById('returnOrderModal').classList.remove('hidden');
}

function closeReturnOrderModal() {
    document.getElementById('returnOrderModal').classList.add('hidden');
}

function toggleReturnCustomReason(val) {
    const box = document.getElementById('returnCustomReasonBox');
    if (val === 'other') {
        box.classList.remove('hidden');
        document.getElementById('returnModalCustomReason').required = true;
    } else {
        box.classList.add('hidden');
        document.getElementById('returnModalCustomReason').required = false;
    }
}
</script>

<!-- POPUP MODAL TRẢ HÀNG & HOÀN TIỀN -->
<div id="returnOrderModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400 font-bold text-sm">
                    <i class="fa-solid fa-rotate-left"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>Yêu Cầu Trả Hàng & Hoàn Tiền</span>
                        <span id="returnModalOrderCodeBadge" class="font-mono text-cyan-400 text-xs"></span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Chính sách bảo hành đổi trả & hoàn tiền của NEXVORA</p>
                </div>
            </div>
            <button type="button" onclick="closeReturnOrderModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="<?= BASE_URL ?>index.php?url=order/return" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
            <?= Csrf::field() ?>
            <input type="hidden" name="order_code" id="returnModalOrderCodeInput" value="">
            <input type="hidden" name="customer_phone" id="returnModalCustomerPhoneInput" value="">
            <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '') ?>">

            <!-- Khối huy hiệu & chính sách áp dụng -->
            <div class="p-4 rounded-2xl bg-purple-500/10 border border-purple-500/30 text-purple-200 space-y-2">
                <div class="flex items-center justify-between">
                    <span id="returnModalPolicyBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-500/20 text-purple-300 border border-purple-500/40"></span>
                    <span class="text-xs text-slate-300">Hoàn tiền: <strong id="returnModalRefundAmount" class="text-cyan-400 font-bold text-sm"></strong></span>
                </div>
                <p id="returnModalPolicyMsg" class="text-[11px] text-slate-300 leading-relaxed"></p>
                <div class="text-[10px] text-slate-400 pt-1 border-t border-purple-500/20">
                    <i class="fa-solid fa-circle-info text-cyan-400 mr-1"></i> Khi trả hàng thành công: Hàng hóa sẽ được hoàn lại vào kho và hệ thống tự động trừ khỏi doanh thu.
                </div>
            </div>

            <!-- Khối nhập tài khoản ngân hàng nhận tiền hoàn lại -->
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                <div class="flex items-center gap-1.5 font-bold text-slate-200">
                    <i class="fa-solid fa-money-bill-transfer text-emerald-400"></i>
                    <span>Tài Khoản Nhận Tiền Hoàn Lại: <span class="text-rose-400">*</span></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Tên Ngân Hàng <span class="text-rose-400">*</span></label>
                        <select name="bank_name" id="returnModalBankName" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400" required>
                            <option value="">-- Chọn ngân hàng --</option>
                            <option value="VietinBank">VietinBank (ICB)</option>
                            <option value="Vietcombank">Vietcombank (VCB)</option>
                            <option value="MBBank">MB Bank (Quân Đội)</option>
                            <option value="Techcombank">Techcombank (TCB)</option>
                            <option value="BIDV">BIDV</option>
                            <option value="ACB">ACB</option>
                            <option value="VPBank">VPBank</option>
                            <option value="TPBank">TPBank</option>
                            <option value="Agribank">Agribank</option>
                            <option value="HDBank">HDBank</option>
                            <option value="Sacombank">Sacombank</option>
                            <option value="MoMo">Ví MoMo</option>
                            <option value="Khác">Ngân hàng khác</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Số Tài Khoản (STK) <span class="text-rose-400">*</span></label>
                        <input type="text" name="account_number" id="returnModalAccountNumber" placeholder="VD: 101883603231..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-400" required>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Tên Chủ Tài Khoản (Viết hoa không dấu) <span class="text-rose-400">*</span></label>
                        <input type="text" name="account_holder" id="returnModalAccountHolder" placeholder="VD: LE VAN SON" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white uppercase focus:outline-none focus:border-cyan-400" required>
                    </div>
                </div>
            </div>

            <!-- Lý do trả hàng -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-300">
                    Vui lòng chọn lý do trả hàng: <span class="text-rose-400">*</span>
                </label>
                <select name="return_reason" id="returnModalReasonSelect" onchange="toggleReturnCustomReason(this.value)" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400" required>
                    <option value="Sản phẩm bị lỗi kỹ thuật từ nhà sản xuất">Sản phẩm bị lỗi kỹ thuật từ nhà sản xuất</option>
                    <option value="Sản phẩm không đúng với mô tả trên website">Sản phẩm không đúng với mô tả trên website</option>
                    <option value="Kiểm tra hàng thấy không ưng ý (Khi xem hàng)">Kiểm tra hàng thấy không ưng ý (Khi xem hàng)</option>
                    <option value="Giao nhầm phiên bản, màu sắc hoặc thiếu phụ kiện">Giao nhầm phiên bản, màu sắc hoặc thiếu phụ kiện</option>
                    <option value="Hàng bị móp méo, trầy xước trong quá trình vận chuyển">Hàng bị móp méo, trầy xước trong quá trình vận chuyển</option>
                    <option value="Đổi ý, không còn nhu cầu sử dụng (Theo chính sách)">Đổi ý, không còn nhu cầu sử dụng (Theo chính sách)</option>
                    <option value="other">Lý do khác...</option>
                </select>

                <div id="returnCustomReasonBox" class="hidden mt-2">
                    <textarea name="custom_reason" id="returnModalCustomReason" rows="2" placeholder="Nhập chi tiết tình trạng sản phẩm hoặc lý do trả hàng..." class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-cyan-400"></textarea>
                </div>
            </div>

            <!-- Tải ảnh minh chứng tình trạng sản phẩm lỗi (Yêu cầu) -->
            <div class="space-y-1.5 p-3 rounded-xl bg-slate-950 border border-slate-800">
                <label class="block text-xs font-bold text-slate-300">
                    <i class="fa-solid fa-camera text-cyan-400 mr-1"></i> Ảnh minh chứng lỗi / sai sản phẩm:
                </label>
                <input type="file" name="evidence_image" accept="image/*" class="w-full text-xs text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-cyan-600 file:text-white hover:file:bg-cyan-500 cursor-pointer">
                <p class="text-[10px] text-slate-500">Chụp rõ tem mác hoặc tình trạng lỗi để shop duyệt hoàn tiền nhanh chóng.</p>
            </div>

            <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeReturnOrderModal()" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 text-xs font-semibold transition">
                    Đóng
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-extrabold text-xs shadow-lg shadow-purple-600/30 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Xác Nhận Trả Hàng & Hoàn Tiền</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php /* [SỬA LỖI] Đã xóa khối timelineModal + script bị dán lặp 2 lần (gây lỗi JS: Identifier 'clientOrderCodes' has already been declared) */ ?>
