<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng - NEXVORA Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-b from-[#0b172e] via-[#0f2347] to-[#0a162c] text-slate-100 min-h-screen flex flex-col relative overflow-x-hidden selection:bg-primary-600 selection:text-white">
    <?php require_once __DIR__ . '/partials/bg_effects.php'; ?>
    <!-- Admin Navbar -->
    <header class="bg-slate-900 border-b border-slate-800 px-6 py-3 sticky top-0 z-40">
        <div class="flex items-center justify-between max-w-7xl mx-auto">
            <div class="flex items-center space-x-3">
                <img src="<?= BASE_URL ?>public/images/logo.png" alt="NEXVORA Logo" class="h-9 w-auto object-contain drop-shadow-[0_0_12px_rgba(6,182,212,0.45)]">
                <span class="text-base font-black tracking-tight text-white">NEX<span class="text-cyan-400">VORA</span> <span class="text-xs bg-cyan-950 text-cyan-300 border border-cyan-800 px-2 py-0.5 rounded-md ml-2 font-mono">Admin</span></span>
            </div>
            <div class="flex items-center space-x-4 text-xs">
                <a href="<?= BASE_URL ?>" target="_blank" class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-3.5 py-1.5 rounded-xl border border-slate-700 transition flex items-center">
                    <i class="fa-solid fa-eye mr-1.5"></i> Xem Website
                </a>
                <a href="<?= BASE_URL ?>index.php?url=admin/logout" class="bg-red-500/20 hover:bg-red-500 text-red-300 hover:text-white px-3 py-1.5 rounded-xl border border-red-500/30 transition">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> Thoát
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-1 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
            <?php 
                $activeNav = 'orders';
                require_once __DIR__ . '/partials/sidebar.php'; 
                $statusFilter = $status ?? ($_GET['status'] ?? 'all');
            ?>

            <main class="lg:col-span-4 space-y-6">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-extrabold text-white flex items-center gap-2.5">
                            <span>Quản Lý Đơn Hàng (<?= count($orders) ?>)</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Realtime Sync
                            </span>
                        </h2>
                        <span class="text-xs text-slate-400">Tự động đồng bộ số liệu sang Trang Tổng quan & Trang Thống kê</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="location.reload()" class="px-3.5 py-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-200 hover:text-cyan-300 border border-slate-700 text-xs font-bold flex items-center gap-1.5 transition shadow-sm" title="Làm mới bảng đơn hàng ngay lập tức">
                            <i class="fa-solid fa-rotate text-xs"></i> <span>Làm Mới Ngay</span>
                        </button>
                    </div>
                </div>

                <!-- Flash Notification Success -->
                <?php if (!empty($_SESSION['flash_order_msg'])): ?>
                    <div id="flashMsgAlert" class="p-3.5 bg-emerald-950/70 border border-emerald-500/50 text-emerald-300 text-xs rounded-2xl flex items-center justify-between shadow-lg shadow-emerald-950/40">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-base text-emerald-400"></i> 
                            <span class="font-bold"><?= htmlspecialchars($_SESSION['flash_order_msg']) ?></span>
                        </div>
                        <button type="button" onclick="document.getElementById('flashMsgAlert').remove()" class="text-slate-400 hover:text-white transition">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                    <?php unset($_SESSION['flash_order_msg']); ?>
                <?php endif; ?>

                <!-- Flash Notification Error (ví dụ: khi admin cố tình sửa đơn đã hủy) -->
                <?php if (!empty($_SESSION['flash_order_err'])): ?>
                    <div id="flashErrAlert" class="p-3.5 bg-rose-950/80 border border-rose-500/60 text-rose-200 text-xs rounded-2xl flex items-center justify-between shadow-lg shadow-rose-950/40">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation text-base text-rose-400"></i> 
                            <span class="font-bold"><?= htmlspecialchars($_SESSION['flash_order_err']) ?></span>
                        </div>
                        <button type="button" onclick="document.getElementById('flashErrAlert').remove()" class="text-slate-400 hover:text-white transition">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                    <?php unset($_SESSION['flash_order_err']); ?>
                <?php endif; ?>

                <!-- TABS PHÂN LOẠI TRẠNG THÁI & THÙNG RÁC (ĐỒNG BỘ TRANG SẢN PHẨM) -->
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
                    <a href="<?= BASE_URL ?>index.php?url=admin/orders" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 <?= ($statusFilter === 'all' || empty($statusFilter)) ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Tất Cả Đơn</span>
                        <span id="badgeCountAll" class="px-2 py-0.5 text-[10px] rounded-full bg-slate-800 text-slate-300 font-extrabold transition-all duration-300"><?= $countAll ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/orders&status=pending" 
                       class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($statusFilter === 'pending') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'bg-slate-900 text-slate-400 hover:text-amber-300 border border-slate-800' ?>">
                        <span>⏳ Chờ Duyệt</span>
                        <span id="badgeCountPending" class="px-2 py-0.5 text-[10px] rounded-full bg-amber-950 text-amber-300 border border-amber-800/60 font-extrabold transition-all duration-300"><?= $countPending ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/orders&status=confirmed" 
                       class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($statusFilter === 'confirmed') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-600/30' : 'bg-slate-900 text-slate-400 hover:text-cyan-300 border border-slate-800' ?>">
                        <span>✅ Đã Duyệt</span>
                        <span id="badgeCountConfirmed" class="px-2 py-0.5 text-[10px] rounded-full bg-cyan-950 text-cyan-300 border border-cyan-800/60 font-extrabold transition-all duration-300"><?= $countConfirmed ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/orders&status=shipping" 
                       class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($statusFilter === 'shipping') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-slate-900 text-slate-400 hover:text-indigo-300 border border-slate-800' ?>">
                        <span>🚚 Đang Giao</span>
                        <span id="badgeCountShipping" class="px-2 py-0.5 text-[10px] rounded-full bg-indigo-950 text-indigo-300 border border-indigo-800/60 font-extrabold transition-all duration-300"><?= $countShipping ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/orders&status=completed" 
                       class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($statusFilter === 'completed') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-slate-900 text-slate-400 hover:text-emerald-300 border border-slate-800' ?>">
                        <span>🎉 Hoàn Thành</span>
                        <span id="badgeCountCompleted" class="px-2 py-0.5 text-[10px] rounded-full bg-emerald-950 text-emerald-300 border border-emerald-800/60 font-extrabold transition-all duration-300"><?= $countCompleted ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/orders&status=returned" 
                       class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($statusFilter === 'returned') ? 'bg-purple-600 text-white shadow-md shadow-purple-600/30' : 'bg-slate-900 text-slate-400 hover:text-purple-300 border border-slate-800' ?>">
                        <span>🔄 Đã Trả Hàng</span>
                        <span id="badgeCountReturned" class="px-2 py-0.5 text-[10px] rounded-full bg-purple-950 text-purple-300 border border-purple-800/60 font-extrabold transition-all duration-300"><?= $countReturned ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/orders&status=cancelled" 
                       class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($statusFilter === 'cancelled') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'bg-slate-900 text-slate-400 hover:text-rose-300 border border-slate-800' ?>">
                        <span>❌ Đã Hủy</span>
                        <span id="badgeCountCancelled" class="px-2 py-0.5 text-[10px] rounded-full bg-rose-950 text-rose-300 border border-rose-800/60 font-extrabold transition-all duration-300"><?= $countCancelled ?? 0 ?></span>
                    </a>

                    <!-- TAB THÙNG RÁC (XÓA MỀM) -->
                    <a href="<?= BASE_URL ?>index.php?url=admin/orders-trash" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-900 text-slate-400 hover:text-amber-300 border border-slate-800 hover:border-amber-600/40 ml-auto">
                        <i class="fa-solid fa-trash-can text-amber-400"></i>
                        <span>Thùng Rác</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-amber-950 text-amber-400 border border-amber-800/80"><?= $trashCount ?? 0 ?></span>
                    </a>
                </div>

                <!-- Bảng danh sách đơn hàng -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden p-6 shadow-xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[10px] text-slate-400 uppercase tracking-wider border-b border-slate-800/80">
                                    <th class="py-3 px-4 whitespace-nowrap">Mã Đơn</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Khách Hàng</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Tổng Tiền</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Phương Thức</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Thanh Toán</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Cập Nhật Trạng Thái & Thao Tác</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap">Xóa / Thùng Rác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                <?php if (empty($orders)): ?>
                                    <tr><td colspan="7" class="py-12 text-center text-slate-500 font-medium">Không tìm thấy đơn hàng nào trong phân mục này.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $ord): ?>
                                        <tr class="hover:bg-slate-800/40 transition">
                                            <td class="py-3.5 px-4 font-mono font-bold text-cyan-400 whitespace-nowrap">
                                                <button type="button" onclick='openOrderDetailModal(<?= json_encode($ord, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="hover:underline text-cyan-300 font-black flex items-center gap-1">
                                                    <span>#<?= htmlspecialchars($ord['order_code']) ?></span>
                                                    <i class="fa-solid fa-magnifying-glass text-[10px] text-slate-400"></i>
                                                </button>
                                                <span class="text-[10px] text-slate-500 font-normal"><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></span>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-bold text-white"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                                <div class="text-[10px] text-slate-500"><?= htmlspecialchars($ord['customer_phone']) ?></div>
                                                <?php if (!empty($ord['is_hidden_by_user'])): ?>
                                                    <div class="mt-0.5">
                                                        <span class="inline-flex items-center gap-1 text-[9px] font-bold text-amber-300 bg-amber-500/15 border border-amber-500/30 px-1.5 py-0.5 rounded" title="Khách đã xóa đơn này khỏi lịch sử cá nhân (Admin vẫn quản lý đầy đủ)">
                                                            <i class="fa-solid fa-eye-slash text-[8px]"></i> Khách đã ẩn lịch sử
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($ord['cancel_reason'])): ?>
                                                    <div class="text-[10px] text-rose-400 mt-0.5 max-w-[220px] truncate" title="<?= htmlspecialchars($ord['cancel_reason']) ?>">
                                                        <i class="fa-solid fa-circle-xmark mr-0.5"></i> <?= in_array($ord['order_status'], ['return_requested', 'returned'], true) ? 'Trả hàng' : 'Hủy' ?>: <?= htmlspecialchars($ord['cancel_reason']) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($ord['refund_info'])): ?>
                                                    <div class="text-[10px] text-purple-300 mt-0.5 max-w-[220px] truncate" title="<?= htmlspecialchars($ord['refund_info']) ?>">
                                                        <i class="fa-solid fa-money-bill-transfer mr-0.5"></i> Hoàn tiền: <?= htmlspecialchars($ord['refund_info']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3.5 px-4 font-black text-rose-400 whitespace-nowrap"><?= number_format($ord['final_amount'], 0, ',', '.') ?>đ</td>
                                            <td class="py-3.5 px-4 uppercase text-[10px] font-bold whitespace-nowrap">
                                                <span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700 text-slate-300">
                                                    <?= htmlspecialchars($ord['payment_method']) ?>
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap payment-status-cell-<?= $ord['id'] ?>">
                                                <?php if ($ord['order_status'] === 'return_requested'): ?>
                                                    <span class="bg-purple-500/20 text-purple-300 border border-purple-500/40 px-2.5 py-1 rounded-lg text-[10px] font-bold inline-flex items-center gap-1 animate-pulse" title="Khách yêu cầu trả hàng - Nhận lại & kiểm tra hàng rồi chọn 'Đã trả hàng' để duyệt, hoặc chọn lại 'Hoàn thành' để từ chối">
                                                        <i class="fa-solid fa-rotate-left text-[9px]"></i> Chờ duyệt trả hàng
                                                    </span>
                                                <?php elseif ($ord['payment_status'] === 'refunded'): ?>
                                                    <span class="bg-purple-500/20 text-purple-300 border border-purple-500/40 px-2.5 py-1 rounded-lg text-[10px] font-bold inline-flex items-center gap-1">
                                                        <i class="fa-solid fa-rotate-left text-[9px]"></i> Đã hoàn tiền
                                                    </span>
                                                <?php elseif ($ord['order_status'] === 'cancelled' && $ord['payment_status'] === 'paid'): ?>
                                                    <span class="bg-rose-500/20 text-rose-300 border border-rose-500/40 px-2.5 py-1 rounded-lg text-[10px] font-bold inline-flex items-center gap-1 animate-pulse" title="Đơn đã hủy - Cần hoàn tiền cho khách!">
                                                        <i class="fa-solid fa-triangle-exclamation text-[9px]"></i> Cần hoàn tiền
                                                    </span>
                                                <?php elseif ($ord['payment_status'] === 'paid'): ?>
                                                    <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 px-2.5 py-1 rounded-lg text-[10px] font-bold inline-flex items-center gap-1">
                                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Đã TT
                                                    </span>
                                                <?php else: ?>
                                                    <span class="bg-amber-500/20 text-amber-300 border border-amber-500/40 px-2.5 py-1 rounded-lg text-[10px] font-bold inline-flex items-center gap-1">
                                                        <i class="fa-solid fa-clock text-[9px]"></i> Chờ TT
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- CỘT CẬP NHẬT TRẠNG THÁI: TỰ ĐỘNG LƯU KHI CHỌN KHÔNG CẦN BẤM NÚT LƯU -->
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <?php if ($ord['order_status'] === 'cancelled'): ?>
                                                    <!-- Đơn đã bị hủy: Không cho chỉnh sửa và hiển thị nút Xem Chi Tiết thay cho nút Lưu -->
                                                    <div class="inline-flex items-center gap-2">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-950/70 border border-rose-800/80 text-rose-300 font-bold text-xs" title="Đơn hàng khách đã hủy - Admin không thể thay đổi trạng thái">
                                                            <i class="fa-solid fa-lock text-rose-400"></i>
                                                            <span>Đơn đã hủy (Đã khóa)</span>
                                                        </span>
                                                        <button type="button" 
                                                                onclick='openOrderDetailModal(<?= json_encode($ord, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
                                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-extrabold text-xs rounded-xl shadow-md transition hover:scale-105 active:scale-95 whitespace-nowrap"
                                                                title="Xem toàn bộ mặt hàng & thông tin đơn">
                                                            <i class="fa-solid fa-eye text-xs"></i>
                                                            <span>Xem Chi Tiết</span>
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Đơn bình thường: Chọn tự động lưu lập tức -->
                                                    <form action="<?= BASE_URL ?>index.php?url=admin/order-status" method="POST" class="inline-flex items-center gap-2 whitespace-nowrap auto-save-order-form" data-order-code="<?= htmlspecialchars($ord['order_code']) ?>">
                                                        <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                        <input type="hidden" name="order_code" value="<?= $ord['order_code'] ?>">
                                                        <input type="hidden" name="ajax" value="1">
                                                        <?= csrf_field() ?>
                                                        
                                                        <!-- Trạng thái đơn hàng -->
                                                        <select name="order_status" 
                                                                data-prev-value="<?= $ord['order_status'] ?>"
                                                                onchange="handleOrderStatusChange(this)"
                                                                class="text-xs py-1.5 px-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 focus:outline-none focus:border-cyan-400 cursor-pointer hover:border-slate-500 transition">
                                                            <option value="pending" <?= ($ord['order_status'] === 'pending') ? 'selected' : '' ?>>⏳ Chờ duyệt</option>
                                                            <option value="confirmed" <?= ($ord['order_status'] === 'confirmed') ? 'selected' : '' ?>>✅ Đã duyệt</option>
                                                            <option value="shipping" <?= ($ord['order_status'] === 'shipping') ? 'selected' : '' ?>>🚚 Đang giao</option>
                                                            <option value="completed" <?= ($ord['order_status'] === 'completed') ? 'selected' : '' ?>>🎉 Hoàn thành</option>
                                                            <option value="return_requested" <?= ($ord['order_status'] === 'return_requested') ? 'selected' : '' ?>>📨 Yêu cầu trả hàng (chờ duyệt)</option>
                                                            <option value="returned" <?= ($ord['order_status'] === 'returned') ? 'selected' : '' ?>>🔄 Đã trả hàng</option>
                                                            <option value="cancelled" <?= ($ord['order_status'] === 'cancelled') ? 'selected' : '' ?>>❌ Hủy đơn</option>
                                                        </select>

                                                        <!-- Trạng thái thanh toán -->
                                                        <select name="payment_status" 
                                                                data-prev-value="<?= $ord['payment_status'] ?>"
                                                                onchange="handleOrderStatusChange(this)"
                                                                class="text-xs py-1.5 px-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 focus:outline-none focus:border-cyan-400 cursor-pointer hover:border-slate-500 transition">
                                                            <option value="pending" <?= ($ord['payment_status'] === 'pending') ? 'selected' : '' ?>>⚠️ Chưa TT</option>
                                                            <option value="paid" <?= ($ord['payment_status'] === 'paid') ? 'selected' : '' ?>>💳 Đã TT</option>
                                                            <option value="refunded" <?= ($ord['payment_status'] === 'refunded') ? 'selected' : '' ?>>🔄 Đã hoàn tiền</option>
                                                        </select>

                                                        <!-- Trạng thái tự động lưu -->
                                                        <span class="auto-save-status text-xs transition duration-200 min-w-[65px] inline-flex items-center text-slate-500"></span>
                                                    </form>
                                                <?php endif; ?>
                                            </td>

                                            <!-- CỘT THAO TÁC: XEM NHANH, ĐỐI SOÁT & XÓA MỀM VÀO THÙNG RÁC -->
                                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center gap-1.5 justify-end">
                                                    <!-- Nút mở modal xem chi tiết nhanh -->
                                                    <button type="button" 
                                                            onclick='openOrderDetailModal(<?= json_encode($ord, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-950/60 hover:bg-blue-600 text-blue-300 hover:text-white border border-blue-800/80 transition" 
                                                            title="Xem chi tiết đơn hàng (Modal)">
                                                        <i class="fa-solid fa-eye text-xs"></i>
                                                    </button>

                                                    <!-- Link xem hóa đơn công khai -->
                                                    <a href="<?= BASE_URL ?>index.php?url=order/success&code=<?= urlencode($ord['order_code']) ?>" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-cyan-400 border border-slate-700 transition" title="Mở trang hóa đơn đơn hàng">
                                                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                                    </a>

                                                    <!-- Đối soát SePay nếu chưa TT -->
                                                    <?php if ($ord['payment_status'] !== 'paid'): ?>
                                                        <a href="<?= BASE_URL ?>index.php?url=admin/reconcile-sepay&code=<?= urlencode($ord['order_code']) ?>" class="inline-flex items-center justify-center px-2 py-1.5 rounded-lg bg-cyan-950/60 hover:bg-cyan-900 text-cyan-300 border border-cyan-700/60 text-[10px] font-bold transition" title="Đối soát trực tiếp qua API SePay">
                                                            <i class="fa-solid fa-arrows-rotate mr-1"></i> Đối soát
                                                        </a>
                                                    <?php endif; ?>

                                                    <!-- NÚT XÓA MỀM (CHUYỂN VÀO THÙNG RÁC, KHÔNG ẢNH HƯỞNG THỐNG KÊ) -->
                                                    <form action="<?= BASE_URL ?>index.php?url=admin/order-delete" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn chuyển đơn hàng #<?= htmlspecialchars($ord['order_code']) ?> vào Thùng rác?\n\n(Lưu ý: Thống kê doanh thu & lịch sử kinh doanh vẫn được bảo toàn nguyên vẹn)');">
                                                        <?= Csrf::field() ?>
                                                        <input type="hidden" name="id" value="<?= $ord['id'] ?>">
                                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-950/60 border border-rose-800/80 text-rose-400 hover:text-white hover:bg-rose-600 transition shadow-sm" title="Chuyển đơn hàng vào Thùng rác">
                                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- MODAL CHI TIẾT ĐƠN HÀNG (POPUP XEM NHANH) -->
    <div id="orderDetailModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col shadow-2xl">
            <div class="p-5 border-b border-slate-800 flex items-center justify-between bg-slate-950/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
                        <i class="fa-solid fa-receipt text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                            <span>Chi Tiết Đơn Hàng</span>
                            <span id="modalOrderCode" class="text-cyan-400 font-mono text-sm"></span>
                        </h3>
                        <p id="modalOrderDate" class="text-xs text-slate-400"></p>
                    </div>
                </div>
                <button type="button" onclick="closeOrderDetailModal()" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-5 text-xs">
                <!-- Thông tin khách hàng -->
                <div class="p-4 rounded-2xl bg-slate-950/50 border border-slate-800/80 space-y-2">
                    <span class="text-[11px] text-cyan-400 uppercase font-bold block mb-1">Thông Tin Người Nhận</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-300">
                        <div>Họ tên: <strong id="modalCustomerName" class="text-white"></strong></div>
                        <div>SĐT: <strong id="modalCustomerPhone" class="text-white"></strong></div>
                        <div class="sm:col-span-2">Email: <span id="modalCustomerEmail" class="text-slate-400"></span></div>
                        <div class="sm:col-span-2">Địa chỉ giao: <span id="modalCustomerAddress" class="text-slate-300"></span></div>
                    </div>
                </div>

                <!-- Box thông tin hủy đơn (nếu có) -->
                <div id="modalCancelInfoBox" class="hidden p-4 rounded-2xl bg-rose-950/40 border border-rose-800/60 space-y-2">
                    <div class="flex items-center gap-2 text-rose-400 font-bold">
                        <i class="fa-solid fa-circle-exclamation text-sm"></i>
                        <span>Đơn Hàng Đã Bị Hủy</span>
                    </div>
                    <div id="modalCancelReason" class="text-[11px] text-slate-300"></div>
                    <div id="modalRefundInfo" class="hidden text-[11px] text-purple-300 bg-purple-950/60 p-2.5 rounded-xl border border-purple-800/60 font-mono"></div>
                </div>

                <!-- Danh sách sản phẩm mua -->
                <div class="space-y-2">
                    <span class="text-[11px] text-slate-400 uppercase font-bold block">Danh Sách Mặt Hàng Đã Mua</span>
                    <div id="modalItemsList" class="divide-y divide-slate-800 border border-slate-800 rounded-2xl overflow-hidden bg-slate-950/50">
                        <!-- Rendered by JS -->
                    </div>
                </div>

                <!-- Tổng tiền -->
                <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-2">
                    <div class="flex justify-between text-slate-400">
                        <span>Phương thức thanh toán:</span>
                        <span id="modalPaymentMethod" class="text-white font-bold uppercase"></span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Trạng thái thanh toán:</span>
                        <span id="modalPaymentStatus" class="font-bold"></span>
                    </div>
                    <div class="flex justify-between text-slate-400 pt-2 border-t border-slate-800">
                        <span>Tổng tiền thanh toán:</span>
                        <span id="modalFinalAmount" class="text-base font-black text-rose-400"></span>
                    </div>
                </div>

                <div id="modalNotesBox" class="hidden p-3 rounded-xl bg-slate-950/50 border border-slate-800/80 text-[11px] text-slate-400">
                    <span class="font-bold text-slate-300 block mb-0.5">Ghi chú của khách hàng:</span>
                    <span id="modalNotes"></span>
                </div>
            </div>

            <div class="p-4 border-t border-slate-800 bg-slate-950/60 flex items-center justify-end">
                <button type="button" onclick="closeOrderDetailModal()" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs transition">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <script>
    function openOrderDetailModal(order) {
        if (!order) return;
        document.getElementById('modalOrderCode').textContent = '#' + (order.order_code || '');
        document.getElementById('modalOrderDate').textContent = 'Đặt lúc: ' + (order.created_at || '');
        document.getElementById('modalCustomerName').textContent = order.customer_name || 'Khách vãng lai';
        document.getElementById('modalCustomerPhone').textContent = order.customer_phone || '---';
        document.getElementById('modalCustomerEmail').textContent = order.customer_email || 'Chưa cập nhật';
        document.getElementById('modalCustomerAddress').textContent = order.customer_address || '---';

        // Lý do hủy & Hoàn tiền
        const cancelBox = document.getElementById('modalCancelInfoBox');
        const reasonEl = document.getElementById('modalCancelReason');
        const refundEl = document.getElementById('modalRefundInfo');
        if (order.order_status === 'cancelled') {
            cancelBox.classList.remove('hidden');
            reasonEl.textContent = 'Lý do: ' + (order.cancel_reason || 'Khách yêu cầu hủy đơn');
            if (order.refund_info) {
                refundEl.classList.remove('hidden');
                refundEl.textContent = 'Hoàn tiền về: ' + order.refund_info;
            } else {
                refundEl.classList.add('hidden');
            }
        } else {
            cancelBox.classList.add('hidden');
        }

        // Danh sách items
        const itemsList = document.getElementById('modalItemsList');
        itemsList.innerHTML = '';
        const items = order.items || [];
        if (items.length === 0) {
            itemsList.innerHTML = '<div class="p-4 text-center text-slate-500">Không có dữ liệu mặt hàng.</div>';
        } else {
            items.forEach(it => {
                const img = it.product_image || 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=100';
                const total = parseInt(it.total || 0).toLocaleString('vi-VN') + 'đ';
                const price = parseInt(it.price || 0).toLocaleString('vi-VN') + 'đ';
                itemsList.innerHTML += `
                    <div class="p-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <img src="${img}" class="w-12 h-12 object-contain rounded-xl bg-slate-900 border border-slate-800 p-1 shrink-0">
                            <div>
                                <div class="font-bold text-white text-xs">${it.product_name || 'Sản phẩm'}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">SL: <strong class="text-slate-200">${it.quantity || 1}</strong> × ${price}</div>
                            </div>
                        </div>
                        <div class="font-extrabold text-cyan-400 text-xs whitespace-nowrap">${total}</div>
                    </div>
                `;
            });
        }

        document.getElementById('modalPaymentMethod').textContent = (order.payment_method === 'sepay' ? 'Cổng SePay Trực Tuyến' : 'Thanh Toán Khi Nhận Hàng (Ship COD)');
        
        const payStatusEl = document.getElementById('modalPaymentStatus');
        if (order.order_status === 'cancelled') {
            if (order.payment_status === 'paid') {
                payStatusEl.className = 'font-bold text-rose-400';
                payStatusEl.textContent = 'Đã hủy (Cần hoàn tiền)';
            } else if (order.payment_status === 'refunded') {
                payStatusEl.className = 'font-bold text-purple-400';
                payStatusEl.textContent = 'Đã hủy (Đã hoàn tiền)';
            } else {
                payStatusEl.className = 'font-bold text-slate-400';
                payStatusEl.textContent = 'Đã hủy (Không thu tiền)';
            }
        } else if (order.payment_status === 'paid') {
            payStatusEl.className = 'font-bold text-emerald-400';
            payStatusEl.textContent = 'Đã thanh toán';
        } else if (order.payment_status === 'refunded') {
            payStatusEl.className = 'font-bold text-purple-400';
            payStatusEl.textContent = 'Đã hoàn tiền';
        } else {
            payStatusEl.className = 'font-bold text-amber-400';
            payStatusEl.textContent = 'Chờ thanh toán';
        }

        document.getElementById('modalFinalAmount').textContent = parseInt(order.final_amount || 0).toLocaleString('vi-VN') + 'đ';

        const notesBox = document.getElementById('modalNotesBox');
        if (order.notes) {
            notesBox.classList.remove('hidden');
            document.getElementById('modalNotes').textContent = order.notes;
        } else {
            notesBox.classList.add('hidden');
        }

        document.getElementById('orderDetailModal').classList.remove('hidden');
    }

    function closeOrderDetailModal() {
        document.getElementById('orderDetailModal').classList.add('hidden');
    }

    window.addEventListener('click', function(e) {
        const m = document.getElementById('orderDetailModal');
        if (e.target === m) closeOrderDetailModal();
    });

    // ==========================================
    // TỰ ĐỘNG LƯU TRẠNG THÁI ĐƠN HÀNG KHI ADMIN CHỌN
    // ==========================================
    let isSavingOrder = false;

    const currentStatusFilter = <?= json_encode((string)$statusFilter, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    async function handleOrderStatusChange(selectEl) {
        const form = selectEl.closest('form');
        if (!form) return;

        const orderId = form.querySelector('input[name="order_id"]').value;
        const orderCode = form.dataset.orderCode || form.querySelector('input[name="order_code"]').value;
        const orderStatusSelect = form.querySelector('select[name="order_status"]');
        const paymentStatusSelect = form.querySelector('select[name="payment_status"]');
        const statusEl = form.querySelector('.auto-save-status');

        const newOrderStatus = orderStatusSelect.value;
        const newPaymentStatus = paymentStatusSelect.value;
        const prevValue = selectEl.getAttribute('data-prev-value') || '';

        // Nếu admin chọn HỦY ĐƠN -> yêu cầu xác nhận trước khi thực hiện
        if (selectEl.name === 'order_status' && newOrderStatus === 'returned') {
            const confirmReturn = confirm(`Duyệt trả hàng cho đơn #${orderCode}?\n\nHệ thống sẽ hoàn sản phẩm lại vào kho, trừ khỏi doanh thu và chuyển sang "Đã hoàn tiền" (nếu đơn đã thanh toán). Hãy chắc chắn đã nhận lại hàng và chuyển khoản hoàn tiền cho khách.`);
            if (!confirmReturn) {
                selectEl.value = prevValue;
                return;
            }
        }

        if (selectEl.name === 'order_status' && newOrderStatus === 'cancelled') {
            const confirmCancel = confirm(`Bạn có chắc chắn muốn hủy đơn hàng #${orderCode}?\n\nLưu ý: Hệ thống sẽ tự động hoàn trả số lượng sản phẩm lại vào kho và khóa chỉnh sửa đơn hàng này!`);
            if (!confirmCancel) {
                selectEl.value = prevValue;
                return;
            }
        }

        // Tự động đồng bộ UI: Nếu đơn Hoàn thành thì chuyển thanh toán sang Đã TT
        if (selectEl.name === 'order_status' && newOrderStatus === 'completed') {
            if (paymentStatusSelect.value === 'pending') {
                paymentStatusSelect.value = 'paid';
            }
        }

        // Hiển thị trạng thái đang lưu
        isSavingOrder = true;
        if (statusEl) {
            statusEl.innerHTML = '<span class="text-cyan-400 inline-flex items-center gap-1 font-semibold"><i class="fa-solid fa-circle-notch fa-spin text-xs"></i> Đang lưu</span>';
        }

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // Cập nhật giá trị mốc
                selectEl.setAttribute('data-prev-value', selectEl.value);
                if (result.payment_status) {
                    paymentStatusSelect.value = result.payment_status;
                    paymentStatusSelect.setAttribute('data-prev-value', result.payment_status);
                }

                // Cập nhật nhãn thanh toán ở cột Thanh toán bên cạnh
                updatePaymentCellBadge(orderId, result.payment_status || paymentStatusSelect.value, result.order_status || orderStatusSelect.value);

                // CẬP NHẬT NGAY LẬP TỨC CÁC SỐ LƯỢNG TRÊN TAB BẰNG REALTIME KHÔNG CẦN LOAD LẠI TRANG!
                if (result.counts) {
                    updateBadgeCount('badgeCountAll', result.counts.all);
                    updateBadgeCount('badgeCountPending', result.counts.pending);
                    updateBadgeCount('badgeCountConfirmed', result.counts.confirmed);
                    updateBadgeCount('badgeCountShipping', result.counts.shipping);
                    updateBadgeCount('badgeCountCompleted', result.counts.completed);
                    updateBadgeCount('badgeCountCancelled', result.counts.cancelled);
                }

                // Hiển thị trạng thái đã lưu
                if (statusEl) {
                    statusEl.innerHTML = '<span class="text-emerald-400 font-bold inline-flex items-center gap-1 animate-pulse"><i class="fa-solid fa-circle-check text-xs"></i> Đã lưu</span>';
                    setTimeout(() => {
                        if (statusEl.innerHTML.includes('Đã lưu')) {
                            statusEl.innerHTML = '';
                        }
                    }, 2500);
                }

                // Nếu đang lọc theo 1 tab cụ thể mà đơn chuyển sang trạng thái khác -> làm mờ hàng để Admin nhận biết
                const row = form.closest('tr');
                if (row && currentStatusFilter && currentStatusFilter !== 'all' && newOrderStatus !== currentStatusFilter) {
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '0.4';
                    row.style.filter = 'grayscale(80%)';
                }

                // Toast thông báo nổi góc trên
                showAutoSaveToast(result.message || `Đã lưu đơn #${orderCode} thành công!`, 'success');

                // Nếu đơn bị hủy -> tải lại sau 1.2s để chuyển sang chế độ Khóa
                if (newOrderStatus === 'cancelled' || newOrderStatus === 'returned') {
                    setTimeout(() => {
                        location.reload();
                    }, 1200);
                }
            } else {
                throw new Error(result.message || 'Lỗi khi cập nhật trạng thái đơn hàng');
            }
        } catch (err) {
            console.error('Lỗi tự động lưu:', err);
            selectEl.value = prevValue;
            if (statusEl) {
                statusEl.innerHTML = '<span class="text-rose-400 font-bold inline-flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation text-xs"></i> Lỗi</span>';
                setTimeout(() => { statusEl.innerHTML = ''; }, 3000);
            }
            showAutoSaveToast(err.message || 'Không thể lưu trạng thái đơn hàng!', 'error');
        } finally {
            isSavingOrder = false;
        }
    }

    function updateBadgeCount(badgeId, newCount) {
        const badge = document.getElementById(badgeId);
        if (!badge) return;
        const currentCount = parseInt(badge.textContent.trim() || '0');
        if (currentCount !== parseInt(newCount)) {
            badge.textContent = newCount;
            badge.classList.add('scale-125', 'ring-2', 'ring-cyan-400');
            setTimeout(() => {
                badge.classList.remove('scale-125', 'ring-2', 'ring-cyan-400');
            }, 350);
        }
    }

    function updatePaymentCellBadge(orderId, payStatus, ordStatus) {
        const cell = document.querySelector(`.payment-status-cell-${orderId}`);
        if (!cell) return;

        if (payStatus === 'refunded') {
            cell.innerHTML = `
                <span class="bg-purple-500/20 text-purple-300 border border-purple-500/40 px-2.5 py-1 rounded-lg text-[10px] font-bold inline-flex items-center gap-1">
                    <i class="fa-solid fa-rotate-left text-[9px]"></i> Đã hoàn tiền
                </span>`;
        } else if (ordStatus === 'cancelled' && payStatus === 'paid') {
            cell.innerHTML = `
                <span class="bg-rose-500/20 text-rose-300 border border-rose-500/40 px-2.5 py-1 rounded-lg text-[10px] font-bold inline-flex items-center gap-1 animate-pulse" title="Đơn đã hủy - Cần hoàn tiền cho khách!">
                    <i class="fa-solid fa-triangle-exclamation text-[9px]"></i> Cần hoàn tiền
                </span>`;
        } else if (payStatus === 'paid') {
            cell.innerHTML = `
                <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 px-2.5 py-1 rounded-lg text-[10px] font-bold inline-flex items-center gap-1">
                    <i class="fa-solid fa-circle-check text-[9px]"></i> Đã TT
                </span>`;
        } else {
            cell.innerHTML = `
                <span class="bg-amber-500/20 text-amber-300 border border-amber-500/40 px-2.5 py-1 rounded-lg text-[10px] font-bold inline-flex items-center gap-1">
                    <i class="fa-solid fa-clock text-[9px]"></i> Chờ TT
                </span>`;
        }
    }

    function showAutoSaveToast(message, type = 'success') {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'fixed top-5 right-5 z-50 flex flex-col gap-2 pointer-events-none';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        const isSuccess = type === 'success';
        toast.className = `pointer-events-auto flex items-center gap-2.5 px-4 py-3 rounded-2xl shadow-xl border text-xs font-semibold backdrop-blur-md transition-all duration-300 transform translate-y-2 opacity-0 ${
            isSuccess 
                ? 'bg-slate-900/95 border-emerald-500/50 text-emerald-300 shadow-emerald-950/40' 
                : 'bg-slate-900/95 border-rose-500/50 text-rose-300 shadow-rose-950/40'
        }`;
        toast.innerHTML = `
            <i class="fa-solid ${isSuccess ? 'fa-circle-check text-emerald-400' : 'fa-circle-exclamation text-rose-400'} text-sm"></i>
            <span>${message}</span>
        `;

        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-2', 'opacity-0');
        });

        setTimeout(() => {
            toast.classList.add('opacity-0', '-translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Tự động làm mới dữ liệu định kỳ (Realtime Check) nếu người dùng không thao tác
    setTimeout(function() {
        const modal = document.getElementById('orderDetailModal');
        const isModalOpen = modal && !modal.classList.contains('hidden');
        const activeTag = document.activeElement ? document.activeElement.tagName : '';
        if (!isModalOpen && !isSavingOrder && activeTag !== 'SELECT' && activeTag !== 'INPUT') {
            location.reload();
        }
    }, 15000);
    </script>
</body>
</html>
