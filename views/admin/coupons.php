<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Mã Khuyến Mại & Voucher - NEXVORA Admin</title>
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
                <span class="text-slate-400 font-semibold"><?= htmlspecialchars($_SESSION['admin_user']['fullname'] ?? 'Quản trị viên') ?></span>
                <a href="<?= BASE_URL ?>index.php?url=admin/logout" class="bg-red-500/20 hover:bg-red-500 text-red-300 hover:text-white px-3 py-1.5 rounded-xl border border-red-500/30 transition">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> Thoát
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-[1550px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-1 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
            <!-- Sidebar Navigation -->
            <?php 
                $activeNav = 'coupons';
                require_once __DIR__ . '/partials/sidebar.php'; 
            ?>

            <!-- Main Content Area -->
            <main class="lg:col-span-4 space-y-6">
                <!-- Flash Notification Success -->
                <?php if (!empty($_SESSION['flash_admin_msg'])): ?>
                    <div id="flashMsgAlert" class="bg-emerald-950/80 border border-emerald-500/50 text-emerald-300 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-semibold shadow-lg shadow-emerald-500/10 transition-all">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
                            <span><?= htmlspecialchars($_SESSION['flash_admin_msg']) ?></span>
                        </div>
                        <button type="button" onclick="document.getElementById('flashMsgAlert').remove()" class="text-emerald-400 hover:text-white transition p-1">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                    <?php unset($_SESSION['flash_admin_msg']); ?>
                <?php endif; ?>

                <!-- Flash Notification Error -->
                <?php if (!empty($_SESSION['flash_admin_err'])): ?>
                    <div id="flashErrAlert" class="bg-rose-950/90 border border-rose-500/60 text-rose-200 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-semibold shadow-lg shadow-rose-500/10 transition-all">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-exclamation text-rose-400 text-base"></i>
                            <span><?= htmlspecialchars($_SESSION['flash_admin_err']) ?></span>
                        </div>
                        <button type="button" onclick="document.getElementById('flashErrAlert').remove()" class="text-rose-400 hover:text-white transition p-1">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                    <?php unset($_SESSION['flash_admin_err']); ?>
                <?php endif; ?>

                <!-- Header Banner & Nút Thao Tác Nhanh -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                            <i class="fa-solid fa-ticket text-amber-400"></i>
                            <span>Mã Khuyến Mại & Voucher Giảm Giá</span>
                            <span class="text-xs bg-amber-950 text-amber-300 border border-amber-800/80 px-2.5 py-0.5 rounded-full font-mono font-normal">
                                <?= count($coupons) ?> mã
                            </span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">
                            Quản lý các chương trình ưu đãi, mã giảm giá trực tiếp tại bước đặt hàng của khách hàng.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a href="<?= BASE_URL ?>index.php?url=admin/coupon-create" class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-slate-950 font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-amber-500/20 transition flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-plus text-xs"></i> <span>+ Thêm Mã Khuyến Mại</span>
                        </a>
                    </div>
                </div>

                <!-- 2 PHÂN HỆ: DANH SÁCH MÃ HOẠT ĐỘNG & THÙNG RÁC (ĐỒNG BỘ 100% VỚI SẢN PHẨM / HÃNG / DANH MỤC) -->
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
                    <a href="<?= BASE_URL ?>index.php?url=admin/coupons" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-amber-600 text-white shadow-md shadow-amber-600/30">
                        <i class="fa-solid fa-ticket"></i>
                        <span>🏷️ Danh Sách Mã Khuyến Mại</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-white/20 text-white"><?= $countAll ?? count($coupons) ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/coupon-trash" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-900 text-slate-400 hover:text-amber-300 border border-slate-800 hover:border-amber-600/40">
                        <i class="fa-solid fa-trash-can text-amber-400"></i>
                        <span>🗑️ Thùng Rác</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-amber-950 text-amber-400 border border-amber-800/80"><?= $countTrash ?? 0 ?></span>
                    </a>
                </div>

                <!-- Nút bấm Mở / Đóng Bộ Lọc & Tìm Kiếm -->
                <?php 
                    $hasActiveFilters = !empty($keyword) || ($statusFilter ?? 'all') !== 'all' || ($typeFilter ?? 'all') !== 'all';
                ?>
                <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-900/90 border border-slate-800 rounded-2xl px-5 py-3.5 shadow-sm">
                    <button type="button" 
                            onclick="toggleAdminFilter('couponFilterCollapse', 'couponFilterChevron')" 
                            class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl text-xs font-black transition shadow-sm bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-slate-950 cursor-pointer">
                        <i class="fa-solid fa-filter text-slate-950"></i>
                        <span>Bộ Lọc</span>
                        <?php if ($hasActiveFilters): ?>
                            <span class="w-2 h-2 rounded-full bg-slate-950 animate-ping"></span>
                        <?php endif; ?>
                        <i id="couponFilterChevron" class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300"></i>
                    </button>

                    <div class="flex items-center gap-2 text-xs">
                        <?php if ($hasActiveFilters): ?>
                            <span class="text-amber-400 font-semibold flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-[10px]"></i> Đang áp dụng bộ lọc
                            </span>
                            <a href="<?= BASE_URL ?>index.php?url=admin/coupons" class="text-xs text-slate-400 hover:text-rose-400 transition underline underline-offset-2 flex items-center gap-1 ml-2">
                                <i class="fa-solid fa-xmark"></i> Xóa bộ lọc
                            </a>
                        <?php else: ?>
                            <span class="text-slate-400 text-xs">Bấm "Bộ Lọc" để tìm kiếm & phân loại mã coupon</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Khối Bộ Lọc Thu Phóng (Mặc định ẩn, tự động đóng khi lọc xong) -->
                <div id="couponFilterCollapse" class="hidden transition-all duration-300">
                    <form action="<?= BASE_URL ?>index.php" method="GET" class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 shadow-sm">
                        <input type="hidden" name="url" value="admin/coupons">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-5 relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input type="text" name="keyword" value="<?= htmlspecialchars($keyword ?? '') ?>" placeholder="Tìm kiếm theo mã coupon, tên khuyến mại..." class="w-full text-xs pl-9 pr-3 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-400">
                            </div>
                            <div class="sm:col-span-3">
                                <select name="status" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 focus:outline-none focus:ring-1 focus:ring-amber-400">
                                    <option value="all">Tất cả trạng thái</option>
                                    <option value="active" <?= ($statusFilter ?? '') === 'active' ? 'selected' : '' ?>>Đang khả dụng</option>
                                    <option value="expired" <?= ($statusFilter ?? '') === 'expired' ? 'selected' : '' ?>>Hết hạn / Hết lượt</option>
                                    <option value="inactive" <?= ($statusFilter ?? '') === 'inactive' ? 'selected' : '' ?>>Đã tắt (Inactive)</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <select name="type" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-200 focus:outline-none focus:ring-1 focus:ring-amber-400">
                                    <option value="all">Mọi loại giảm</option>
                                    <option value="fixed" <?= ($typeFilter ?? '') === 'fixed' ? 'selected' : '' ?>>Số tiền cố định (đ)</option>
                                    <option value="percent" <?= ($typeFilter ?? '') === 'percent' ? 'selected' : '' ?>>Phần trăm (%)</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2 flex items-center gap-2">
                                <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-filter text-[11px]"></i> Lọc
                                </button>
                                <?php if ($hasActiveFilters): ?>
                                    <a href="<?= BASE_URL ?>index.php?url=admin/coupons" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold py-2.5 px-3 rounded-xl border border-slate-700 transition" title="Xóa bộ lọc">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Bảng Danh Sách Mã Khuyến Mại -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[10px] text-slate-400 uppercase tracking-wider border-b border-slate-800/80 bg-slate-950/60">
                                    <th class="py-3 px-4 whitespace-nowrap">ID</th>
                                    <th class="py-3 px-4">Mã Voucher & Tiêu Đề</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Mức Giảm</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Đơn Tối Thiểu</th>
                                    <th class="py-3 px-4 whitespace-nowrap text-center">Số Lượng / Đã Dùng</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Hạn Sử Dụng</th>
                                    <th class="py-3 px-4 whitespace-nowrap text-center">Trạng Thái</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                <?php if (!empty($coupons)): ?>
                                    <?php foreach ($coupons as $c): 
                                        $isExpired = (!empty($c['end_date']) && strtotime($c['end_date']) < time());
                                        $isOutOfStock = ($c['quantity'] <= 0);
                                    ?>
                                        <tr class="hover:bg-slate-800/40 transition">
                                            <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                                #<?= $c['id'] ?>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-300 font-mono font-black text-xs tracking-wider">
                                                        <i class="fa-solid fa-tag text-[10px]"></i>
                                                        <?= htmlspecialchars($c['code']) ?>
                                                    </span>
                                                    <span class="font-bold text-white text-xs">
                                                        <?= htmlspecialchars($c['title']) ?>
                                                    </span>
                                                </div>
                                                <div class="mt-1 flex items-center gap-2">
                                                    <?php 
                                                        $scope = $c['apply_scope'] ?? 'all';
                                                        if ($scope === 'category'): ?>
                                                            <span class="inline-flex items-center gap-1 text-[10px] bg-purple-950/70 border border-purple-800/80 text-purple-300 px-2 py-0.5 rounded">
                                                                <i class="fa-solid fa-folder-tree text-[9px]"></i> Danh mục: <?= htmlspecialchars($c['category_name'] ?? 'ID #' . $c['category_id']) ?>
                                                            </span>
                                                        <?php elseif ($scope === 'product'): ?>
                                                            <span class="inline-flex items-center gap-1 text-[10px] bg-cyan-950/70 border border-cyan-800/80 text-cyan-300 px-2 py-0.5 rounded">
                                                                <i class="fa-solid fa-mobile-screen text-[9px]"></i> Sản phẩm: <?= htmlspecialchars($c['product_name'] ?? 'ID #' . $c['product_id']) ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="inline-flex items-center gap-1 text-[10px] bg-slate-800 border border-slate-700 text-slate-400 px-2 py-0.5 rounded">
                                                                <i class="fa-solid fa-globe text-[9px]"></i> Tất cả sản phẩm
                                                            </span>
                                                        <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <?php if ($c['discount_type'] === 'percent'): ?>
                                                    <span class="px-2.5 py-1 rounded-md text-xs font-black bg-purple-950/80 border border-purple-800/80 text-purple-300">
                                                        Giảm <?= (float)$c['discount_value'] ?>%
                                                    </span>
                                                    <?php if (!empty($c['max_discount'])): ?>
                                                        <span class="block text-[10px] text-slate-400 mt-0.5">Tối đa <?= number_format($c['max_discount'], 0, ',', '.') ?>đ</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="px-2.5 py-1 rounded-md text-xs font-black bg-emerald-950/80 border border-emerald-800/80 text-emerald-300">
                                                        -<?= number_format($c['discount_value'], 0, ',', '.') ?>đ
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap text-slate-300 font-medium">
                                                <?php if ($c['min_order_amount'] > 0): ?>
                                                    Từ <?= number_format($c['min_order_amount'], 0, ',', '.') ?>đ
                                                <?php else: ?>
                                                    <span class="text-slate-500 italic">Không giới hạn</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap text-center">
                                                <div class="inline-flex flex-col items-center">
                                                    <span class="font-bold text-white">
                                                        <?= $c['quantity'] ?> <span class="text-slate-500 text-[10px] font-normal">lượt còn</span>
                                                    </span>
                                                    <span class="text-[10px] text-slate-400">Đã dùng: <?= $c['used_count'] ?></span>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap text-xs">
                                                <?php if (!empty($c['end_date'])): ?>
                                                    <span class="<?= $isExpired ? 'text-rose-400 font-semibold' : 'text-slate-300' ?>">
                                                        <?= date('d/m/Y H:i', strtotime($c['end_date'])) ?>
                                                    </span>
                                                    <?php if ($isExpired): ?>
                                                        <span class="block text-[10px] text-rose-500 font-bold uppercase tracking-wider">Đã hết hạn</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-slate-500 italic">Vô thời hạn</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap text-center">
                                                <?php if ($c['status'] === 'active' && !$isExpired && !$isOutOfStock): ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-950 text-emerald-300 border border-emerald-800">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                                        Áp dụng
                                                    </span>
                                                <?php elseif ($isExpired): ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-950 text-rose-300 border border-rose-800">
                                                        Hết hạn
                                                    </span>
                                                <?php elseif ($isOutOfStock): ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-950 text-amber-300 border border-amber-800">
                                                        Hết lượt
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-400 border border-slate-700">
                                                        Tắt
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <a href="<?= BASE_URL ?>index.php?url=admin/coupon-edit&id=<?= $c['id'] ?>" class="w-8 h-8 rounded-lg bg-cyan-950/60 hover:bg-cyan-600 border border-cyan-800/80 text-cyan-300 hover:text-white flex items-center justify-center transition" title="Chỉnh sửa">
                                                        <i class="fa-solid fa-pen text-[11px]"></i>
                                                    </a>
                                                    <a href="<?= BASE_URL ?>index.php?url=admin/coupon-delete&id=<?= $c['id'] ?>&csrf_token=<?= Csrf::token() ?>" onclick="return confirm('Bạn có chắc chắn muốn chuyển mã khuyến mại [<?= htmlspecialchars($c['code']) ?>] vào Thùng rác?')" class="w-8 h-8 rounded-lg bg-rose-950/60 hover:bg-rose-600 border border-rose-800/80 text-rose-300 hover:text-white flex items-center justify-center transition" title="Bỏ vào thùng rác">
                                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-12 text-slate-500">
                                            <i class="fa-solid fa-ticket text-4xl mb-3 text-slate-600 block"></i>
                                            <p class="text-xs">Không tìm thấy mã khuyến mại nào phù hợp.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleAdminFilter(boxId, chevronId) {
            const box = document.getElementById(boxId);
            const chevron = chevronId ? document.getElementById(chevronId) : null;
            if (!box) return;
            box.classList.toggle('hidden');
            if (chevron) {
                chevron.classList.toggle('rotate-180');
            }
        }
    </script>
</body>
</html>