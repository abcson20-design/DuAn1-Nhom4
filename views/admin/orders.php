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
        <div class="flex items-center justify-between max-w-[1550px] mx-auto">
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

    <div class="max-w-[1550px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-1 relative z-10">
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

                <?php 
                    // Hàm tạo URL giữ nguyên các query filter khi chuyển Tab
                    $tabUrl = function(string $tabStatus) use ($filters) {
                        $p = ['url' => 'admin/orders'];
                        if ($tabStatus !== 'all') {
                            $p['status'] = $tabStatus;
                        }
                        foreach ($filters ?? [] as $k => $v) {
                            if (!empty($v) && $v !== 'newest') {
                                $p[$k] = $v;
                            }
                        }
                        return BASE_URL . 'index.php?' . http_build_query($p);
                    };
                    $hasActiveFilters = !empty($filters['keyword']) 
                                     || !empty($filters['payment_method']) 
                                     || !empty($filters['payment_status']) 
                                     || !empty($filters['date_from']) 
                                     || !empty($filters['date_to']) 
                                     || (!empty($filters['sort']) && $filters['sort'] !== 'newest');
                ?>

                <!-- CẢNH BÁO GOM ĐƠN CHO ADMIN (NẾU CÓ KHÁCH ĐẶT NHIỀU ĐƠN TRONG 24H) -->
                <?php if (!empty($splitOrdersSummary)): ?>
                    <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-950/80 to-orange-950/80 border border-amber-500/50 text-amber-200 text-xs shadow-xl shadow-amber-950/30 flex items-start justify-between gap-3 animate-pulse">
                        <div class="flex items-start gap-3">
                            <span class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/40 flex items-center justify-center shrink-0 text-base">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </span>
                            <div>
                                <h4 class="font-bold text-amber-300 text-sm flex items-center gap-2">
                                    <span>Cảnh Báo Nghiệp Vụ: Phát Hiện Khách Đặt Nhiều Đơn Chia Nhỏ</span>
                                    <span class="px-2 py-0.5 rounded-full bg-amber-400 text-slate-950 text-[10px] font-black"><?= count($splitOrdersSummary) ?> Khách Hàng</span>
                                </h4>
                                <p class="text-xs text-amber-200/90 mt-1 leading-relaxed">
                                    Hệ thống phát hiện khách hàng đặt nhiều đơn hàng trong vòng 24 giờ qua nhưng chia nhỏ đơn. Vui lòng liên hệ khách hàng để <strong>gom chung các đơn hàng</strong> trước khi bàn giao cho đơn vị vận chuyển nhằm tối ưu hóa chi phí và quản lý kiện hàng.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- TABS PHÂN LOẠI TRẠNG THÁI & THÙNG RÁC (ĐỒNG BỘ TRANG SẢN PHẨM) -->
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
                    <a href="<?= $tabUrl('all') ?>" 
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 <?= ($statusFilter === 'all' || empty($statusFilter)) ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Tất Cả Đơn</span>
                        <span id="badgeCountAll" class="px-2 py-0.5 text-[10px] rounded-full bg-slate-800 text-slate-300 font-extrabold transition-all duration-300"><?= $countAll ?? 0 ?></span>
                    </a>

                    <a href="<?= $tabUrl('pending') ?>" 
                       class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($statusFilter === 'pending') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'bg-slate-900 text-slate-400 hover:text-amber-300 border border-slate-800' ?>">
                        <span>⏳ Chờ Duyệt</span>
                        <span id="badgeCountPending" class="px-2 py-0.5 text-[10px] rounded-full bg-amber-950 text-amber-300 border border-amber-800/60 font-extrabold transition-all duration-300"><?= $countPending ?? 0 ?></span>
                    </a>

                    <a href="<?= $tabUrl('confirmed') ?>" 
                       class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($statusFilter === 'confirmed') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-600/30' : 'bg-slate-900 text-slate-400 hover:text-cyan-300 border border-slate-800' ?>">
                        <span>✅ Đã Duyệt</span>
                        <span id="badgeCountConfirmed" class="px-2 py-0.5 text-[10px] rounded-full bg-cyan-950 text-cyan-300 border border-cyan-800/60 font-extrabold transition-all duration-300"><?= $countConfirmed ?? 0 ?></span>
                    </a>

                    <a href="<?= $tabUrl('shipping') ?>" 
                       class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($statusFilter === 'shipping') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-slate-900 text-slate-400 hover:text-indigo-300 border border-slate-800' ?>">
                        <span>🚚 Đang Giao</span>
                        <span id="badgeCountShipping" class="px-2 py-0.5 text-[10px] rounded-full bg-indigo-950 text-indigo-300 border border-indigo-800/60 font-extrabold transition-all duration-300"><?= $countShipping ?? 0 ?></span>
                    </a>

                    <a href="<?= $tabUrl('completed') ?>" 
                       class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($statusFilter === 'completed') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-slate-900 text-slate-400 hover:text-emerald-300 border border-slate-800' ?>">
                        <span>🎉 Hoàn Thành</span>
                        <span id="badgeCountCompleted" class="px-2 py-0.5 text-[10px] rounded-full bg-emerald-950 text-emerald-300 border border-emerald-800/60 font-extrabold transition-all duration-300"><?= $countCompleted ?? 0 ?></span>
                    </a>

                    <!-- TAB CHỜ HOÀN HÀNG (YÊU CẦU TRẢ HÀNG CỦA KHÁCH) -->
                    <a href="<?= $tabUrl('return_requested') ?>" 
                       class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($statusFilter === 'return_requested') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'bg-slate-900 text-slate-400 hover:text-amber-300 border border-slate-800' ?>">
                        <span>⚠️ Chờ Hoàn Hàng</span>
                        <span id="badgeCountReturnRequested" class="px-2 py-0.5 text-[10px] rounded-full bg-amber-950 text-amber-300 border border-amber-800/60 font-extrabold transition-all duration-300"><?= $countReturnRequested ?? 0 ?></span>
                    </a>

                    <a href="<?= $tabUrl('returned') ?>" 
                       class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($statusFilter === 'returned') ? 'bg-purple-600 text-white shadow-md shadow-purple-600/30' : 'bg-slate-900 text-slate-400 hover:text-purple-300 border border-slate-800' ?>">
                        <span>🔄 Đã Trả Hàng</span>
                        <span id="badgeCountReturned" class="px-2 py-0.5 text-[10px] rounded-full bg-purple-950 text-purple-300 border border-purple-800/60 font-extrabold transition-all duration-300"><?= $countReturned ?? 0 ?></span>
                    </a>

                    <a href="<?= $tabUrl('cancelled') ?>" 
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

                <!-- THANH ĐIỀU KHIỂN: NÚT "BỘ LỌC" SỔ RA & THỐNG KÊ -->
                <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-900/80 border border-slate-800 rounded-2xl p-3 shadow-md">
                    <div class="flex items-center gap-3">
                        <button type="button" 
                                onclick="toggleAdminFilter('orderFilterCollapse', 'orderFilterChevron')" 
                                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 <?= $hasActiveFilters ? 'bg-gradient-to-r from-cyan-600 to-blue-600 text-white shadow-md shadow-cyan-600/30' : 'bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700' ?>">
                            <i class="fa-solid fa-filter text-xs <?= $hasActiveFilters ? 'text-white' : 'text-cyan-400' ?>"></i>
                            <span>Bộ Lọc</span>
                            <?php if ($hasActiveFilters): ?>
                                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                <span class="text-[10px] bg-white/20 text-white px-1.5 py-0.5 rounded-full font-bold">Đang lọc</span>
                            <?php endif; ?>
                            <i id="orderFilterChevron" class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300"></i>
                        </button>

                        <?php if ($hasActiveFilters): ?>
                            <a href="<?= BASE_URL ?>index.php?url=admin/orders<?= ($statusFilter !== 'all') ? '&status=' . urlencode($statusFilter) : '' ?>" 
                               class="text-xs text-rose-400 hover:text-rose-300 transition flex items-center gap-1 font-semibold">
                                <i class="fa-solid fa-rotate-left text-[11px]"></i> Xóa bộ lọc
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-3 sm:gap-4 text-xs text-slate-300">
                        <div>Hiển thị: <strong class="text-white"><?= count($orders) ?></strong> đơn</div>
                        <span class="text-slate-700">|</span>
                        <div>Doanh thu: <strong class="text-emerald-400 font-extrabold"><?= number_format($filteredRevenue ?? 0, 0, ',', '.') ?>đ</strong></div>
                    </div>
                </div>

                <!-- BỘ LỌC TÌM KIẾM ĐƠN HÀNG ĐA NĂNG (SỔ RA KHI BẤM NÚT BỘ LỌC, TỰ ĐỘNG ĐÓNG KHI LỌC XONG) -->
                <div id="orderFilterCollapse" class="hidden transition-all duration-300">
                    <form action="<?= BASE_URL ?>index.php" method="GET" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl space-y-4">
                        <input type="hidden" name="url" value="admin/orders">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter ?? 'all') ?>">

                        <!-- Hàng 1: Từ khóa tìm kiếm, Phương thức thanh toán, Trạng thái thanh toán, Sắp xếp -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5">
                            <!-- Từ khóa: Mã đơn, tên khách, SĐT, Email -->
                            <div class="lg:col-span-4 relative">
                                <label class="block text-[11px] font-bold text-slate-400 mb-1">
                                    <i class="fa-solid fa-magnifying-glass text-cyan-400 mr-1"></i> Tìm kiếm đơn hàng
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                                    <input type="text" 
                                           name="keyword" 
                                           value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>" 
                                           placeholder="Mã đơn (#TS...), tên khách, SĐT, email..." 
                                           class="w-full text-xs pl-9 pr-3 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition">
                                </div>
                            </div>

                            <!-- Phương thức thanh toán -->
                            <div class="lg:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-400 mb-1">
                                    <i class="fa-solid fa-wallet text-amber-400 mr-1"></i> Phương thức thanh toán
                                </label>
                                <select name="payment_method" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 cursor-pointer">
                                    <option value="">-- Tất cả phương thức --</option>
                                    <option value="cod" <?= (($filters['payment_method'] ?? '') === 'cod') ? 'selected' : '' ?>>💵 Tiền mặt (Ship COD)</option>
                                    <option value="sepay" <?= (($filters['payment_method'] ?? '') === 'sepay') ? 'selected' : '' ?>>💳 Chuyển khoản (VietQR / SePay)</option>
                                </select>
                            </div>

                            <!-- Trạng thái thanh toán -->
                            <div class="lg:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-400 mb-1">
                                    <i class="fa-solid fa-receipt text-emerald-400 mr-1"></i> Trạng thái thanh toán
                                </label>
                                <select name="payment_status" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 cursor-pointer">
                                    <option value="">-- Tất cả trạng thái TT --</option>
                                    <option value="pending" <?= (($filters['payment_status'] ?? '') === 'pending') ? 'selected' : '' ?>>⚠️ Chưa thanh toán</option>
                                    <option value="paid" <?= (($filters['payment_status'] ?? '') === 'paid') ? 'selected' : '' ?>>💳 Đã thanh toán</option>
                                    <option value="refunded" <?= (($filters['payment_status'] ?? '') === 'refunded') ? 'selected' : '' ?>>🔄 Đã hoàn tiền</option>
                                </select>
                            </div>

                            <!-- Sắp xếp -->
                            <div class="lg:col-span-2">
                                <label class="block text-[11px] font-bold text-slate-400 mb-1">
                                    <i class="fa-solid fa-arrow-down-wide-short text-indigo-400 mr-1"></i> Sắp xếp
                                </label>
                                <select name="sort" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 cursor-pointer">
                                    <option value="newest" <?= (($filters['sort'] ?? '') === 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                                    <option value="oldest" <?= (($filters['sort'] ?? '') === 'oldest') ? 'selected' : '' ?>>Cũ nhất</option>
                                    <option value="amount_desc" <?= (($filters['sort'] ?? '') === 'amount_desc') ? 'selected' : '' ?>>Tiền: Cao → Thấp</option>
                                    <option value="amount_asc" <?= (($filters['sort'] ?? '') === 'amount_asc') ? 'selected' : '' ?>>Tiền: Thấp → Cao</option>
                                </select>
                            </div>
                        </div>

                        <!-- Hàng 2: Khoảng ngày đặt hàng, Lối tắt ngày, Nút Áp dụng & Đặt lại -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end pt-2 border-t border-slate-800/80">
                            <!-- Từ ngày -->
                            <div class="lg:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-400 mb-1">
                                    <i class="fa-regular fa-calendar-days text-cyan-400 mr-1"></i> Từ ngày đặt
                                </label>
                                <input type="date" 
                                       id="filterDateFrom"
                                       name="date_from" 
                                       value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>" 
                                       class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400">
                            </div>

                            <!-- Đến ngày -->
                            <div class="lg:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-400 mb-1">
                                    <i class="fa-regular fa-calendar-check text-cyan-400 mr-1"></i> Đến ngày đặt
                                </label>
                                <input type="date" 
                                       id="filterDateTo"
                                       name="date_to" 
                                       value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>" 
                                       class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400">
                            </div>

                            <!-- Lối tắt nhanh ngày -->
                            <div class="lg:col-span-3 flex items-center gap-1.5 pb-0.5">
                                <button type="button" onclick="setQuickDate('today')" class="text-[11px] font-bold px-2.5 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition">Hôm nay</button>
                                <button type="button" onclick="setQuickDate('week')" class="text-[11px] font-bold px-2.5 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition">7 ngày qua</button>
                                <button type="button" onclick="setQuickDate('month')" class="text-[11px] font-bold px-2.5 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition">Tháng này</button>
                            </div>

                            <!-- Nút submit Lọc & Reset -->
                            <div class="lg:col-span-3 flex items-center gap-2">
                                <button type="submit" class="flex-1 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-lg shadow-cyan-600/20 transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-filter text-[11px]"></i>
                                    <span>Áp Dụng Lọc</span>
                                </button>
                                <?php if ($hasActiveFilters): ?>
                                    <a href="<?= BASE_URL ?>index.php?url=admin/orders<?= ($statusFilter !== 'all') ? '&status=' . urlencode($statusFilter) : '' ?>" 
                                       class="bg-slate-800 hover:bg-slate-700 text-rose-400 hover:text-rose-300 text-xs font-bold py-2.5 px-3 rounded-xl border border-slate-700 transition flex items-center gap-1 shadow-sm" 
                                       title="Xóa bộ lọc, giữ tab trạng thái hiện tại">
                                        <i class="fa-solid fa-rotate-left"></i>
                                        <span>Đặt lại</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
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
                                                <?php if (!empty($splitOrdersSummary[$ord['customer_phone']])): ?>
                                                    <div class="mt-1">
                                                        <span class="inline-flex items-center gap-1 text-[9px] font-bold text-amber-300 bg-amber-500/20 border border-amber-500/40 px-2 py-0.5 rounded-full animate-pulse" title="Gợi ý liên hệ gom đơn: <?= htmlspecialchars($splitOrdersSummary[$ord['customer_phone']]['order_codes']) ?>">
                                                            <i class="fa-solid fa-layer-group text-[8px]"></i> ⚠️ Gom đơn (Khách có <?= $splitOrdersSummary[$ord['customer_phone']]['order_count'] ?> đơn chưa giao)
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
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
                                                    <?php
                                                        $evidenceImg = null;
                                                        if (preg_match('/Ảnh minh chứng:\s*([^\s|]+)/u', $ord['refund_info'], $imgMatches)) {
                                                            $evidenceImg = trim($imgMatches[1]);
                                                        }
                                                    ?>
                                                    <?php if ($evidenceImg): ?>
                                                        <div class="mt-1">
                                                            <button type="button" 
                                                                    onclick="viewEvidenceModal('<?= BASE_URL . htmlspecialchars($evidenceImg) ?>', '<?= htmlspecialchars($ord['order_code']) ?>')" 
                                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-purple-900/60 hover:bg-purple-800 text-purple-200 border border-purple-700/70 text-[10px] font-bold transition">
                                                                <i class="fa-solid fa-camera text-cyan-400"></i> Xem ảnh minh chứng
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
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
                                                    <!-- Đơn đã bị hủy: Không cho chỉnh sửa trạng thái đơn -->
                                                    <div class="inline-flex items-center gap-2">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-950/70 border border-rose-800/80 text-rose-300 font-bold text-xs" title="Đơn hàng khách đã hủy - Admin không thể thay đổi trạng thái đơn">
                                                            <i class="fa-solid fa-lock text-rose-400"></i>
                                                            <span>Đơn đã hủy (Đã khóa)</span>
                                                        </span>

                                                        <?php if ($ord['payment_status'] === 'paid'): ?>
                                                            <!-- Đơn đã thanh toán trước khi hủy: Cho phép Admin cập nhật hoàn tiền -->
                                                            <button type="button" 
                                                                    onclick="confirmRefundOnly(<?= $ord['id'] ?>, '<?= htmlspecialchars($ord['order_code']) ?>')"
                                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition hover:scale-105 active:scale-95 animate-pulse"
                                                                    title="Khách đã thanh toán đơn này. Bấm vào đây sau khi đã chuyển khoản hoàn tiền cho khách!">
                                                                <i class="fa-solid fa-rotate-left text-[11px]"></i>
                                                                <span>Cập nhật Đã hoàn tiền</span>
                                                            </button>
                                                        <?php elseif ($ord['payment_status'] === 'refunded'): ?>
                                                            <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-purple-950/70 border border-purple-800 text-purple-300 font-bold text-xs" title="Đơn hàng đã hoàn tiền thành công cho khách">
                                                                <i class="fa-solid fa-rotate-left text-[10px]"></i> Đã hoàn tiền
                                                            </span>
                                                        <?php endif; ?>

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
                                                    <form action="<?= BASE_URL ?>index.php?url=admin/order-status" method="POST" class="inline-flex items-center gap-2 whitespace-nowrap auto-save-order-form" data-order-code="<?= htmlspecialchars($ord['order_code']) ?>" data-payment-method="<?= htmlspecialchars($ord['payment_method']) ?>">
                                                        <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                        <input type="hidden" name="order_code" value="<?= $ord['order_code'] ?>">
                                                        <input type="hidden" name="ajax" value="1">
                                                        <?= csrf_field() ?>
                                                        
                                                        <!-- Trạng thái đơn hàng (Tuân thủ máy trạng thái 1 chiều) -->
                                                        <?php
                                                            $allowedStateMap = [
                                                                'pending' => ['pending' => '⏳ Chờ duyệt', 'confirmed' => '✅ Đã duyệt', 'cancelled' => '❌ Hủy đơn'],
                                                                'confirmed' => ['confirmed' => '✅ Đã duyệt', 'shipping' => '🚚 Đang giao', 'cancelled' => '❌ Hủy đơn'],
                                                                'shipping' => ['shipping' => '🚚 Đang giao', 'completed' => '🎉 Hoàn thành'],
                                                                'completed' => ['completed' => '🎉 Hoàn thành', 'returned' => '🔄 Đã trả hàng'],
                                                                'return_requested' => ['return_requested' => '📨 Yêu cầu trả hàng (chờ duyệt)', 'returned' => '🔄 Đã trả hàng', 'completed' => '🎉 Hoàn thành (Từ chối)'],
                                                                'returned' => ['returned' => '🔄 Đã trả hàng'],
                                                                'cancelled' => ['cancelled' => '❌ Đã hủy']
                                                            ];
                                                            $currentAllowed = $allowedStateMap[$ord['order_status']] ?? [$ord['order_status'] => $ord['order_status']];
                                                        ?>
                                                        <select name="order_status" 
                                                                data-prev-value="<?= $ord['order_status'] ?>"
                                                                onchange="handleOrderStatusChange(this)"
                                                                class="text-xs py-1.5 px-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 focus:outline-none focus:border-cyan-400 cursor-pointer hover:border-slate-500 transition">
                                                            <?php foreach ($currentAllowed as $stKey => $stLabel): ?>
                                                                <option value="<?= $stKey ?>" <?= ($ord['order_status'] === $stKey) ? 'selected' : '' ?>><?= $stLabel ?></option>
                                                            <?php endforeach; ?>
                                                        </select>

                                                        <!-- Trạng thái thanh toán: Chưa TT và Đã TT tự động theo khách, Admin chỉ cập nhật Hoàn tiền -->
                                                        <?php if ($ord['payment_status'] === 'refunded'): ?>
                                                            <!-- Đã hoàn tiền: Khóa hoàn toàn, hiển thị cố định -->
                                                            <span class="text-xs py-1.5 px-2.5 rounded-xl border border-purple-800/80 bg-purple-950/60 text-purple-300 font-bold inline-flex items-center gap-1.5 cursor-not-allowed" title="Đã hoàn tiền cho khách (Không thể thay đổi)">
                                                                <i class="fa-solid fa-rotate-left text-[10px]"></i> Đã hoàn tiền
                                                            </span>
                                                            <input type="hidden" name="payment_status" value="refunded">
                                                        <?php elseif ($ord['payment_status'] === 'paid'): ?>
                                                            <!-- Đã TT: Khách đã thanh toán -> Admin chỉ có thể cập nhật sang "Đã hoàn tiền" -->
                                                            <select name="payment_status" 
                                                                    data-prev-value="paid"
                                                                    onchange="handleOrderStatusChange(this)"
                                                                    class="text-xs py-1.5 px-2.5 rounded-xl border border-cyan-800/80 bg-slate-950 text-emerald-400 font-bold focus:outline-none focus:border-cyan-400 cursor-pointer hover:border-slate-500 transition"
                                                                    title="Trạng thái thanh toán tự động cập nhật theo giao dịch của khách hàng. Admin chỉ cập nhật 'Đã hoàn tiền' khi hoàn trả tiền cho khách.">
                                                                <option value="paid" selected>💳 Đã TT (Tự động)</option>
                                                                <option value="refunded" class="text-purple-300 font-bold bg-slate-900">🔄 Đã hoàn tiền (Admin cập nhật)</option>
                                                            </select>
                                                        <?php else: ?>
                                                            <!-- Chưa TT: Tự động theo khách -> Admin KHÔNG ĐƯỢC THAY ĐỔI -->
                                                            <select name="payment_status" 
                                                                    data-prev-value="pending"
                                                                    disabled
                                                                    title="Trạng thái Chưa TT / Đã TT được hệ thống tự động cập nhật theo giao dịch của khách hàng, Admin không thể sửa thủ công"
                                                                    class="text-xs py-1.5 px-2.5 rounded-xl border border-slate-700/80 bg-slate-900/60 text-amber-300/80 font-medium cursor-not-allowed">
                                                                <option value="pending" selected>⚠️ Chưa TT (Tự động)</option>
                                                            </select>
                                                            <input type="hidden" name="payment_status" value="pending">
                                                        <?php endif; ?>

                                                        <!-- Trạng thái tự động lưu -->
                                                        <span class="auto-save-status text-xs transition duration-200 min-w-[65px] inline-flex items-center text-slate-500"></span>

                                                        <?php if ($ord['order_status'] === 'return_requested'): ?>
                                                            <div class="inline-flex items-center gap-1.5 ml-1">
                                                                <button type="button" 
                                                                        onclick="quickApproveReturn(<?= $ord['id'] ?>, '<?= htmlspecialchars($ord['order_code']) ?>', '<?= htmlspecialchars($ord['payment_method']) ?>')"
                                                                        class="px-2.5 py-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-[11px] rounded-xl shadow-md transition flex items-center gap-1"
                                                                        title="Duyệt yêu cầu trả hàng & hoàn tiền">
                                                                    <i class="fa-solid fa-check text-[10px]"></i> Duyệt hoàn hàng
                                                                </button>
                                                                <button type="button" 
                                                                        onclick="quickRejectReturn(<?= $ord['id'] ?>, '<?= htmlspecialchars($ord['order_code']) ?>')"
                                                                        class="px-2 py-1 bg-slate-800 hover:bg-rose-900/60 text-slate-300 hover:text-rose-300 font-bold text-[11px] rounded-xl border border-slate-700 hover:border-rose-700 transition flex items-center gap-1"
                                                                        title="Từ chối yêu cầu trả hàng, giữ đơn ở trạng thái Hoàn thành">
                                                                    <i class="fa-solid fa-xmark text-[10px]"></i> Từ chối
                                                                </button>
                                                            </div>
                                                        <?php endif; ?>
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

                                                    <!-- In Phiếu Giao Hàng Chuẩn Shopee -->
                                                    <button type="button" onclick="printShopeeInvoice('<?= htmlspecialchars($ord['order_code']) ?>')" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-950/60 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-800/80 transition shadow-sm" title="In phiếu giao hàng chuẩn khổ A5 dán kiện hàng">
                                                        <i class="fa-solid fa-print text-xs"></i>
                                                    </button>

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

            <div class="p-4 border-t border-slate-800 bg-slate-950/60 flex items-center justify-between">
                <button type="button" id="modalBtnPrintInvoice" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-md shadow-indigo-600/30">
                    <i class="fa-solid fa-print"></i> In Phiếu Giao Hàng
                </button>
                <button type="button" onclick="closeOrderDetailModal()" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs transition">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <script>
    function openOrderDetailModal(order) {
        if (!order) return;
        document.getElementById('modalBtnPrintInvoice').onclick = () => printShopeeInvoice(order.order_code);
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

        const newOrderStatus = orderStatusSelect ? orderStatusSelect.value : '';
        const newPaymentStatus = paymentStatusSelect ? paymentStatusSelect.value : '';
        const prevValue = selectEl.getAttribute('data-prev-value') || '';

        // KIỂM SOÁT QUYỀN TRẠNG THÁI THANH TOÁN:
        // Admin không thể tự do đổi giữa Chưa TT và Đã TT (do khách tự động cập nhật).
        // Admin chỉ có quyền cập nhật sang "Đã hoàn tiền" khi hoàn trả tiền cho khách.
        if (selectEl.name === 'payment_status') {
            if (newPaymentStatus === 'refunded') {
                const confirmRefund = confirm(`Xác nhận đơn hàng #${orderCode} đã được hoàn tiền cho khách?\n\nLưu ý: Bạn hãy chắc chắn đã chuyển khoản hoàn tiền thành công cho khách hàng trước khi xác nhận.`);
                if (!confirmRefund) {
                    selectEl.value = prevValue;
                    return;
                }
            } else if (newPaymentStatus !== prevValue) {
                alert('Trạng thái thanh toán (Chưa TT / Đã TT) được hệ thống tự động cập nhật theo giao dịch của khách hàng. Admin chỉ cập nhật trạng thái "Đã hoàn tiền" khi hoàn trả tiền cho khách!');
                selectEl.value = prevValue;
                return;
            }
        }

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

        // Ràng buộc nghiêm ngặt Ship COD: Admin không được tự ý bấm Hoàn thành / Đã giao hàng
        if (selectEl.name === 'order_status' && newOrderStatus === 'completed') {
            const payMethod = form.getAttribute('data-payment-method') || '';
            if (payMethod === 'cod' && prevValue !== 'completed') {
                alert('Quy định nghiêm ngặt: Với đơn hàng Ship COD (Thanh toán khi nhận hàng), Admin không được tự ý bấm "Hoàn thành / Đã giao hàng".\n\nĐơn hàng chỉ được xác nhận Hoàn thành khi Shipper giao hàng thành công hoặc Khách hàng bấm "Đã nhận hàng"!');
                selectEl.value = prevValue;
                return;
            }
            if (paymentStatusSelect && paymentStatusSelect.value === 'pending') {
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
                if (result.payment_status && paymentStatusSelect) {
                    paymentStatusSelect.value = result.payment_status;
                    paymentStatusSelect.setAttribute('data-prev-value', result.payment_status);

                    // Nếu đã chuyển sang "Đã hoàn tiền" -> Khóa select thành badge cố định
                    if (result.payment_status === 'refunded') {
                        paymentStatusSelect.outerHTML = `
                            <span class="text-xs py-1.5 px-2.5 rounded-xl border border-purple-800/80 bg-purple-950/60 text-purple-300 font-bold inline-flex items-center gap-1.5 cursor-not-allowed" title="Đã hoàn tiền cho khách (Không thể thay đổi)">
                                <i class="fa-solid fa-rotate-left text-[10px]"></i> Đã hoàn tiền
                            </span>
                            <input type="hidden" name="payment_status" value="refunded">
                        `;
                    } else if (result.payment_status === 'paid' && paymentStatusSelect.tagName === 'SELECT') {
                        paymentStatusSelect.disabled = false;
                        paymentStatusSelect.className = 'text-xs py-1.5 px-2.5 rounded-xl border border-cyan-800/80 bg-slate-950 text-emerald-400 font-bold focus:outline-none focus:border-cyan-400 cursor-pointer hover:border-slate-500 transition';
                        paymentStatusSelect.innerHTML = `
                            <option value="paid" selected>💳 Đã TT (Tự động)</option>
                            <option value="refunded" class="text-purple-300 font-bold bg-slate-900">🔄 Đã hoàn tiền (Admin cập nhật)</option>
                        `;
                    }
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

    // Thao tác Admin chủ động hoàn tiền cho đơn đã hủy hoặc trả hàng
    async function confirmRefundOnly(orderId, orderCode) {
        const confirmRefund = confirm(`Xác nhận đơn hàng #${orderCode} đã hoàn tiền thành công cho khách hàng?\n\nLưu ý: Hãy chắc chắn bạn đã chuyển khoản hoàn trả số tiền cho khách hàng trước khi xác nhận.`);
        if (!confirmRefund) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content 
                       || document.querySelector('input[name="_csrf_token"]')?.value 
                       || '';
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('order_code', orderCode);
        formData.append('order_status', 'cancelled');
        formData.append('payment_status', 'refunded');
        formData.append('ajax', '1');
        formData.append('_csrf_token', csrfToken);

        try {
            const response = await fetch('<?= BASE_URL ?>index.php?url=admin/order-status', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (response.ok && result.success) {
                updatePaymentCellBadge(orderId, 'refunded', 'cancelled');
                showAutoSaveToast(result.message || `Đã cập nhật đơn #${orderCode} sang "Đã hoàn tiền"!`, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                alert(result.message || 'Có lỗi xảy ra khi cập nhật hoàn tiền!');
            }
        } catch (err) {
            alert('Lỗi kết nối máy chủ!');
        }
    }

    // ----------------------------------------------------
    // CÁC HÀM XỬ LÝ NHANH YÊU CẦU TRẢ HÀNG & MINH CHỨNG
    // ----------------------------------------------------
    function viewEvidenceModal(imageUrl, orderCode) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: `Minh chứng trả hàng #${orderCode}`,
                imageUrl: imageUrl,
                imageAlt: 'Ảnh bằng chứng trả hàng',
                imageWidth: 500,
                imageHeight: 'auto',
                showCloseButton: true,
                confirmButtonColor: '#0284c7',
                confirmButtonText: 'Đóng'
            });
        } else {
            window.open(imageUrl, '_blank');
        }
    }

    async function quickApproveReturn(orderId, orderCode, paymentMethod) {
        const csrfToken = document.querySelector('input[name="_csrf_token"]')?.value || '';
        const confirmResult = await Swal.fire({
            title: 'Duyệt trả hàng & hoàn tiền?',
            text: `Xác nhận duyệt yêu cầu trả hàng cho đơn #${orderCode}? Hệ thống sẽ chuyển trạng thái đơn sang "Đã trả hàng" và tự động hoàn tiền cho khách.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Đồng ý duyệt',
            cancelButtonText: 'Hủy bỏ'
        });
        if (!confirmResult.isConfirmed) return;

        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('order_code', orderCode);
        formData.append('order_status', 'returned');
        formData.append('payment_status', 'refunded');
        formData.append('ajax', '1');
        formData.append('_csrf_token', csrfToken);

        try {
            const response = await fetch('<?= BASE_URL ?>index.php?url=admin/order-status', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (response.ok && result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Đã duyệt trả hàng!',
                    text: `Đơn #${orderCode} đã chuyển sang Đã trả hàng & Hoàn tiền thành công.`,
                    timer: 1500,
                    showConfirmButton: false
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire('Lỗi', result.message || 'Không thể duyệt trả hàng!', 'error');
            }
        } catch (err) {
            Swal.fire('Lỗi', 'Lỗi kết nối máy chủ!', 'error');
        }
    }

    async function quickRejectReturn(orderId, orderCode) {
        const csrfToken = document.querySelector('input[name="_csrf_token"]')?.value || '';
        const { value: reason } = await Swal.fire({
            title: 'Từ chối yêu cầu trả hàng?',
            text: `Nhập lý do từ chối đơn #${orderCode} (đơn sẽ giữ nguyên trạng thái Hoàn thành):`,
            input: 'text',
            inputPlaceholder: 'Ví dụ: Hàng đã qua sử dụng quá 7 ngày, ảnh không hợp lệ...',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Xác nhận từ chối',
            cancelButtonText: 'Đóng',
            inputValidator: (val) => {
                if (!val) return 'Vui lòng nhập lý do từ chối!';
            }
        });
        if (!reason) return;

        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('order_code', orderCode);
        formData.append('order_status', 'completed');
        formData.append('payment_status', 'paid');
        formData.append('note', `[TỪ CHỐI TRẢ HÀNG ${new Date().toLocaleString('vi-VN')}]: ${reason}`);
        formData.append('ajax', '1');
        formData.append('_csrf_token', csrfToken);

        try {
            const response = await fetch('<?= BASE_URL ?>index.php?url=admin/order-status', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (response.ok && result.success) {
                Swal.fire({
                    icon: 'info',
                    title: 'Đã từ chối trả hàng',
                    text: `Đơn #${orderCode} đã giữ nguyên ở trạng thái Hoàn thành.`,
                    timer: 1500,
                    showConfirmButton: false
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire('Lỗi', result.message || 'Không thể cập nhật!', 'error');
            }
        } catch (err) {
            Swal.fire('Lỗi', 'Lỗi kết nối máy chủ!', 'error');
        }
    }

    // ----------------------------------------------------
    // REALTIME LIVE POLLING (2-BROWSER DEMO ĐẲNG CẤP HỘI ĐỒNG)
    // ----------------------------------------------------
    let lastKnownOrderId = <?= !empty($orders) ? max(array_column($orders, 'id')) : 0 ?>;

    // Âm thanh thông báo đơn mới (Web Audio API không cần file bên ngoài)
    function playOrderChime() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
            osc.frequency.setValueAtTime(880.00, ctx.currentTime + 0.12); // A5
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.4);
        } catch(e) {}
    }

    async function pollNewOrders() {
        if (isSavingOrder) return;
        try {
            const res = await fetch(`<?= BASE_URL ?>index.php?url=api/admin/orders-poll&last_id=${lastKnownOrderId}`);
            if (!res.ok) return;
            const data = await res.json();
            if (data.success) {
                // Cập nhật số lượng các Tab Realtime
                if (data.counts) {
                    updateBadgeCount('badgeCountAll', data.counts.all);
                    updateBadgeCount('badgeCountPending', data.counts.pending);
                    updateBadgeCount('badgeCountConfirmed', data.counts.confirmed);
                    updateBadgeCount('badgeCountShipping', data.counts.shipping);
                    updateBadgeCount('badgeCountCompleted', data.counts.completed);
                    updateBadgeCount('badgeCountReturnRequested', data.counts.return_requested);
                    updateBadgeCount('badgeCountReturned', data.counts.returned);
                    updateBadgeCount('badgeCountCancelled', data.counts.cancelled);
                }

                // Nếu có đơn hàng mới phát sinh (khách vừa đặt ở Browser 1)
                if (data.new_count > 0 && data.new_orders && data.new_orders.length > 0) {
                    playOrderChime();
                    data.new_orders.forEach(o => {
                        const amountStr = new Intl.NumberFormat('vi-VN').format(o.final_amount) + 'đ';
                        showAutoSaveToast(`🔔 Đơn hàng mới #[${o.order_code}] vừa đặt! (${o.customer_name} - ${amountStr})`, 'success');
                    });
                    lastKnownOrderId = data.latest_id;

                    // Nếu đang ở tab 'all' hoặc 'pending' -> tải lại trang nhẹ nhàng sau 1.5s để hiển thị ngay
                    const curTab = '<?= $statusFilter ?? 'all' ?>';
                    if (curTab === 'all' || curTab === 'pending') {
                        setTimeout(() => location.reload(), 1500);
                    }
                } else if (data.latest_id > lastKnownOrderId) {
                    lastKnownOrderId = data.latest_id;
                }
            }
        } catch(err) {
            // Im lặng khi gặp sự cố mạng tạm thời
        }
    }

    // Bắt đầu thăm dò thời gian thực mỗi 2.5 giây
    setInterval(pollNewOrders, 2500);

    // In phiếu giao hàng chuẩn Shopee A5
    async function printShopeeInvoice(orderCode) {
        if (!orderCode) return;
        try {
            const res = await fetch(`<?= BASE_URL ?>index.php?url=api/admin/invoice-data&code=${encodeURIComponent(orderCode)}`);
            const data = await res.json();
            if (!data.success || !data.invoice) {
                alert('Không thể lấy thông tin hóa đơn!');
                return;
            }

            const ord = data.invoice;
            const items = ord.items || [];
            let itemsHtml = '';
            items.forEach((it, idx) => {
                const totalFormatted = new Intl.NumberFormat('vi-VN').format(it.total) + 'đ';
                itemsHtml += `
                    <tr>
                        <td style="border: 1px solid #333; padding: 6px; text-align: center;">${idx + 1}</td>
                        <td style="border: 1px solid #333; padding: 6px;"><strong>${it.product_name}</strong></td>
                        <td style="border: 1px solid #333; padding: 6px; text-align: center;">${it.quantity}</td>
                        <td style="border: 1px solid #333; padding: 6px; text-align: right;">${totalFormatted}</td>
                    </tr>
                `;
            });

            const finalAmountFormatted = new Intl.NumberFormat('vi-VN').format(ord.final_amount) + 'đ';
            const isCOD = (ord.payment_method === 'cod' && ord.payment_status !== 'paid');
            const codText = isCOD ? `THU TIỀN NGƯỜI NHẬN (COD): ${finalAmountFormatted}` : 'ĐÃ THANH TOÁN ONLINE (KHÔNG THU TIỀN)';

            const printHtml = `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>Phiếu Gửi Hàng #${ord.order_code}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #000; font-size: 13px; }
                        .ticket { max-width: 600px; margin: auto; border: 2px dashed #000; padding: 16px; border-radius: 8px; }
                        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 12px; }
                        .barcode { font-family: monospace; font-size: 20px; font-weight: bold; letter-spacing: 3px; }
                        .grid { display: flex; gap: 16px; margin-bottom: 12px; }
                        .col { flex: 1; border: 1px solid #000; padding: 8px; border-radius: 4px; }
                        .cod-box { background: #000; color: #fff; padding: 10px; font-weight: bold; font-size: 15px; text-align: center; border-radius: 4px; margin: 12px 0; }
                        .cod-box.paid { background: #047857; }
                        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 12px; }
                        .footer { margin-top: 16px; display: flex; justify-content: space-between; text-align: center; font-size: 11px; }
                        @media print {
                            body { padding: 0; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="ticket">
                        <div class="header">
                            <div>
                                <h2 style="margin: 0; font-size: 18px;">NEXVORA STORE</h2>
                                <div style="font-size: 11px;">Hệ Thống Phân Phối Smartphone & Phụ Kiện</div>
                            </div>
                            <div style="text-align: right;">
                                <div class="barcode">#${ord.order_code}</div>
                                <div style="font-size: 10px;">Ngày tạo: ${ord.created_at}</div>
                            </div>
                        </div>

                        <div class="grid">
                            <div class="col">
                                <strong>TỪ: NEXVORA OFFICIAL STORE</strong><br>
                                Hotline: 1900.8888 / 0988.888.888<br>
                                Địa chỉ: Toà nhà NEXVORA, Hà Nội<br>
                                Ghi chú: Cho xem hàng, không cho thử
                            </div>
                            <div class="col">
                                <strong>ĐẾN: ${ord.customer_name}</strong><br>
                                SĐT: <strong>${ord.customer_phone}</strong><br>
                                Đ/C: ${ord.customer_address}<br>
                                Ghi chú khách: ${ord.notes || 'Không có'}
                            </div>
                        </div>

                        <div class="cod-box ${isCOD ? '' : 'paid'}">
                            ${codText}
                        </div>

                        <table>
                            <thead>
                                <tr style="background: #f0f0f0;">
                                    <th style="border: 1px solid #333; padding: 6px;">STT</th>
                                    <th style="border: 1px solid #333; padding: 6px;">Tên sản phẩm</th>
                                    <th style="border: 1px solid #333; padding: 6px;">SL</th>
                                    <th style="border: 1px solid #333; padding: 6px;">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>

                        <div class="footer">
                            <div>
                                <strong>Chữ ký người gửi</strong><br><br><br>
                                (Đã ký đóng gói)
                            </div>
                            <div>
                                <strong>Chữ ký người giao</strong><br><br><br>
                                (Bưu tá giao hàng)
                            </div>
                            <div>
                                <strong>Chữ ký người nhận</strong><br><br><br>
                                (Xác nhận hàng nguyên vẹn)
                            </div>
                        </div>
                    </div>
                    <div style="text-align: center; margin-top: 16px;" class="no-print">
                        <button onclick="window.print()" style="padding: 10px 24px; font-weight: bold; background: #0284c7; color: #fff; border: none; border-radius: 8px; cursor: pointer;">IN PHIẾU NGAY</button>
                    </div>
                </body>
                </html>
            `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(printHtml);
            printWindow.document.close();
            setTimeout(() => {
                printWindow.print();
            }, 300);
        } catch (e) {
            console.error(e);
            alert('Lỗi in phiếu: ' + e.message);
        }
    }

    // Đóng/mở bộ lọc tìm kiếm
    function toggleAdminFilter(boxId, chevronId) {
        const box = document.getElementById(boxId);
        const chevron = chevronId ? document.getElementById(chevronId) : null;
        if (!box) return;
        box.classList.toggle('hidden');
        if (chevron) {
            chevron.classList.toggle('rotate-180');
        }
    }

    // Chọn nhanh khoảng ngày lọc đơn hàng
    function setQuickDate(type) {
        const fromInput = document.getElementById('filterDateFrom');
        const toInput = document.getElementById('filterDateTo');
        if (!fromInput || !toInput) return;

        const today = new Date();
        function fmt(d) {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }

        if (type === 'today') {
            fromInput.value = fmt(today);
            toInput.value = fmt(today);
        } else if (type === 'week') {
            const weekAgo = new Date();
            weekAgo.setDate(today.getDate() - 7);
            fromInput.value = fmt(weekAgo);
            toInput.value = fmt(today);
        } else if (type === 'month') {
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            fromInput.value = fmt(firstDay);
            toInput.value = fmt(today);
        }
    }
    </script>
</body>
</html>
