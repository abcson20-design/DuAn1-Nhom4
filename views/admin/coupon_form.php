<?php
$isEdit = !empty($coupon);
$pageTitle = $isEdit ? 'Chỉnh Sửa Mã Khuyến Mại: ' . htmlspecialchars($coupon['code']) : 'Thêm Mã Khuyến Mại Mới';
?>
<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - NEXVORA Admin</title>
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
            <!-- Sidebar -->
            <?php 
                $activeNav = 'coupons';
                require_once __DIR__ . '/partials/sidebar.php'; 
            ?>

            <!-- Main Content Area -->
            <main class="lg:col-span-4 space-y-6">
                <div class="flex items-center justify-between bg-slate-900/60 backdrop-blur p-6 rounded-3xl border border-slate-800">
                    <div class="flex items-center space-x-4">
                        <a href="<?= BASE_URL ?>index.php?url=admin/coupons" class="w-10 h-10 rounded-2xl bg-slate-800 hover:bg-slate-700 border border-slate-700 flex items-center justify-center text-slate-300 hover:text-white transition">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <div>
                            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                                <i class="fa-solid fa-ticket text-amber-400"></i> <?= $pageTitle ?>
                            </h2>
                            <p class="text-slate-400 text-xs mt-0.5"><?= $isEdit ? 'Chỉnh sửa thông số mã khuyến mại' : 'Tạo voucher khuyến mại mới để khách hàng áp dụng tại trang thanh toán' ?></p>
                        </div>
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs flex items-center space-x-2 shadow-lg">
                        <i class="fa-solid fa-circle-exclamation text-base"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
                    <?= Csrf::field() ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Mã Voucher -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                    Mã Khuyến Mại (Code) <span class="text-rose-400">*</span>
                                </label>
                                <button type="button" onclick="generateRandomCode()" class="text-[11px] text-amber-400 hover:text-amber-300 transition flex items-center gap-1 font-semibold">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Tạo mã nhanh
                                </button>
                            </div>
                            <input type="text" name="code" id="couponCode" required value="<?= htmlspecialchars($_POST['code'] ?? $coupon['code'] ?? '') ?>" placeholder="VD: NEXVORA50, FREESHIP, TET2026..." class="w-full bg-slate-950/80 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white uppercase font-mono font-bold tracking-wider focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition">
                            <p class="text-[11px] text-slate-500">Mã chữ in hoa, không dấu, viết liền (VD: SALE100K, VIPMEMBER).</p>
                        </div>

                        <!-- Tên tiêu đề chương trình -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Tên Chương Trình / Tiêu Đề <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" name="title" required value="<?= htmlspecialchars($_POST['title'] ?? $coupon['title'] ?? '') ?>" placeholder="VD: Giảm 50.000đ cho đơn từ 200.000đ..." class="w-full bg-slate-950/80 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition">
                            <p class="text-[11px] text-slate-500">Mô tả ngắn gọn để hiển thị cho khách hàng chọn tại giỏ hàng & thanh toán.</p>
                        </div>
                    </div>

                    <!-- KHỐI PHẠM VI ÁP DỤNG VOUCHER (CHUẨN SHOPEE) -->
                    <div class="p-5 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-cyan-400 flex items-center gap-2">
                                <i class="fa-solid fa-crosshairs"></i> Phạm Vi Áp Dụng Voucher
                            </h4>
                            <span class="text-[10px] text-slate-400">Tùy biến áp dụng theo Shop / Danh mục / 1 Sản phẩm</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <?php $currentScope = $_POST['apply_scope'] ?? $coupon['apply_scope'] ?? 'all'; ?>
                            <label class="scope-card relative p-3.5 rounded-2xl border cursor-pointer transition flex items-center gap-3 <?= $currentScope === 'all' ? 'border-cyan-500 bg-cyan-950/20' : 'border-slate-800 bg-slate-900/60 hover:border-slate-700' ?>">
                                <input type="radio" name="apply_scope" value="all" <?= $currentScope === 'all' ? 'checked' : '' ?> onchange="toggleScopeInputs()" class="text-cyan-500 focus:ring-cyan-500 bg-slate-900 border-slate-700">
                                <div>
                                    <span class="text-xs font-bold text-white block">Tất Cả Sản Phẩm</span>
                                    <span class="text-[10px] text-slate-400">Áp dụng cho mọi mặt hàng</span>
                                </div>
                            </label>

                            <label class="scope-card relative p-3.5 rounded-2xl border cursor-pointer transition flex items-center gap-3 <?= $currentScope === 'category' ? 'border-cyan-500 bg-cyan-950/20' : 'border-slate-800 bg-slate-900/60 hover:border-slate-700' ?>">
                                <input type="radio" name="apply_scope" value="category" <?= $currentScope === 'category' ? 'checked' : '' ?> onchange="toggleScopeInputs()" class="text-cyan-500 focus:ring-cyan-500 bg-slate-900 border-slate-700">
                                <div>
                                    <span class="text-xs font-bold text-white block">Theo 1 Danh Mục</span>
                                    <span class="text-[10px] text-slate-400">Chỉ giảm cho danh mục chọn</span>
                                </div>
                            </label>

                            <label class="scope-card relative p-3.5 rounded-2xl border cursor-pointer transition flex items-center gap-3 <?= $currentScope === 'product' ? 'border-cyan-500 bg-cyan-950/20' : 'border-slate-800 bg-slate-900/60 hover:border-slate-700' ?>">
                                <input type="radio" name="apply_scope" value="product" <?= $currentScope === 'product' ? 'checked' : '' ?> onchange="toggleScopeInputs()" class="text-cyan-500 focus:ring-cyan-500 bg-slate-900 border-slate-700">
                                <div>
                                    <span class="text-xs font-bold text-white block">Theo 1 Sản Phẩm</span>
                                    <span class="text-[10px] text-slate-400">Chỉ giảm riêng cho sản phẩm đó</span>
                                </div>
                            </label>
                        </div>

                        <!-- Dropdown chọn Danh Mục nếu chọn scope = category -->
                        <div id="categoryScopeBox" class="space-y-2 pt-2 <?= $currentScope === 'category' ? '' : 'hidden' ?>">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Chọn Danh Mục Được Áp Dụng <span class="text-rose-400">*</span>
                            </label>
                            <?php $selectedCatId = (int)($_POST['category_id'] ?? $coupon['category_id'] ?? 0); ?>
                            <select name="category_id" id="categorySelect" class="w-full bg-slate-900 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-cyan-400">
                                <option value="">-- Chọn danh mục áp dụng voucher --</option>
                                <?php foreach ($categories ?? [] as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $selectedCatId === (int)$cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?> (<?= $cat['type'] === 'phone' ? 'Điện thoại' : 'Phụ kiện' ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Dropdown chọn Sản Phẩm nếu chọn scope = product -->
                        <div id="productScopeBox" class="space-y-2 pt-2 <?= $currentScope === 'product' ? '' : 'hidden' ?>">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Chọn Sản Phẩm Được Áp Dụng <span class="text-rose-400">*</span>
                            </label>
                            <?php $selectedProdId = (int)($_POST['product_id'] ?? $coupon['product_id'] ?? 0); ?>
                            <select name="product_id" id="productSelect" class="w-full bg-slate-900 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-cyan-400">
                                <option value="">-- Chọn sản phẩm duy nhất áp dụng voucher --</option>
                                <?php foreach ($products ?? [] as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $selectedProdId === (int)$p['id'] ? 'selected' : '' ?>>
                                        [#<?= $p['id'] ?>] <?= htmlspecialchars($p['name']) ?> - <?= number_format($p['price'], 0, ',', '.') ?>đ
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-amber-300 flex items-center gap-2">
                            <i class="fa-solid fa-percent"></i> Cơ Chế Giảm Giá
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Loại giảm giá -->
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                    Loại Giảm Giá <span class="text-rose-400">*</span>
                                </label>
                                <select name="discount_type" id="discountType" onchange="toggleDiscountInputs()" class="w-full bg-slate-900 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition">
                                    <?php $currentType = $_POST['discount_type'] ?? $coupon['discount_type'] ?? 'fixed'; ?>
                                    <option value="fixed" <?= $currentType === 'fixed' ? 'selected' : '' ?>>Số tiền cố định (VNĐ)</option>
                                    <option value="percent" <?= $currentType === 'percent' ? 'selected' : '' ?>>Phần trăm (%)</option>
                                </select>
                            </div>

                            <!-- Mức giảm -->
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider" id="discountValueLabel">
                                    Mức Giảm (VNĐ) <span class="text-rose-400">*</span>
                                </label>
                                <input type="number" step="any" min="1" name="discount_value" id="discountValue" required value="<?= htmlspecialchars($_POST['discount_value'] ?? $coupon['discount_value'] ?? '') ?>" placeholder="VD: 50000" class="w-full bg-slate-900 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Giá trị đơn hàng tối thiểu -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Đơn Hàng Tối Thiểu (VNĐ)
                            </label>
                            <input type="number" step="any" min="0" name="min_order_amount" value="<?= htmlspecialchars($_POST['min_order_amount'] ?? $coupon['min_order_amount'] ?? '') ?>" placeholder="VD: 0 hoặc 100000 (0 hoặc để trống = Mọi đơn hàng)" class="w-full bg-slate-950/80 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition">
                            <p class="text-[11px] text-slate-500">Khách hàng cần đạt giá trị đơn hàng này mới có thể bấm áp dụng voucher.</p>
                        </div>

                        <!-- Số lượng mã phát hành -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Số Lượng Lượt Dùng Còn Lại <span class="text-rose-400">*</span>
                            </label>
                            <input type="number" min="0" name="quantity" required value="<?= htmlspecialchars($_POST['quantity'] ?? $coupon['quantity'] ?? '') ?>" placeholder="VD: 100" class="w-full bg-slate-950/80 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition">
                            <p class="text-[11px] text-slate-500">Mỗi lần khách hoàn tất đơn hàng thành công, số lượng sẽ tự động giảm đi 1.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Ngày bắt đầu -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Bắt Đầu Hiệu Lực
                            </label>
                            <?php 
                                $startDateVal = !empty($coupon['start_date']) ? date('Y-m-d\TH:i', strtotime($coupon['start_date'])) : '';
                            ?>
                            <input type="datetime-local" name="start_date" value="<?= htmlspecialchars($_POST['start_date'] ?? $startDateVal) ?>" class="w-full bg-slate-950/80 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition">
                        </div>

                        <!-- Ngày hết hạn -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Ngày Hết Hạn
                            </label>
                            <?php 
                                $endDateVal = !empty($coupon['end_date']) ? date('Y-m-d\TH:i', strtotime($coupon['end_date'])) : '';
                            ?>
                            <input type="datetime-local" name="end_date" value="<?= htmlspecialchars($_POST['end_date'] ?? $endDateVal) ?>" class="w-full bg-slate-950/80 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition">
                            <p class="text-[11px] text-slate-500">Để trống nếu không giới hạn thời gian.</p>
                        </div>

                        <!-- Trạng thái -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Trạng Thái Hoạt Động <span class="text-rose-400">*</span>
                            </label>
                            <?php $currStatus = $_POST['status'] ?? $coupon['status'] ?? 'active'; ?>
                            <select name="status" class="w-full bg-slate-950/80 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition">
                                <option value="active" <?= $currStatus === 'active' ? 'selected' : '' ?>>🟢 Đang Hoạt Động (Cho phép dùng)</option>
                                <option value="inactive" <?= $currStatus === 'inactive' ? 'selected' : '' ?>>🔴 Tạm Khóa / Vô hiệu hóa</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-800">
                        <a href="<?= BASE_URL ?>index.php?url=admin/coupons" class="px-5 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 text-xs font-bold transition">
                            Hủy Bỏ
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/20 transition flex items-center space-x-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span><?= $isEdit ? 'Lưu Thay Đổi' : 'Tạo Mã Khuyến Mại' ?></span>
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
        function toggleDiscountInputs() {
            const type = document.getElementById('discountType').value;
            const label = document.getElementById('discountValueLabel');
            const discountInput = document.getElementById('discountValue');

            if (type === 'percent') {
                label.innerHTML = 'Mức Giảm (%) <span class="text-rose-400">*</span>';
                discountInput.placeholder = 'VD: 10, 15, 20... (Tối đa 99%)';
                discountInput.max = '99';
                discountInput.min = '1';
            } else {
                label.innerHTML = 'Mức Giảm (VNĐ) <span class="text-rose-400">*</span>';
                discountInput.placeholder = 'VD: 50000, 100000...';
                discountInput.removeAttribute('max');
                discountInput.min = '1000';
            }

            // Kích hoạt lại kiểm tra ngay khi đổi loại giảm giá nếu ô đã có giá trị
            if (discountInput.value && window.FormValidator) {
                const err = window.FormValidator.validateField(discountInput);
                if (err) window.FormValidator.showFieldError(discountInput, err);
                else window.FormValidator.clearFieldError(discountInput);
            }
        }

        function generateRandomCode() {
            const prefixes = ['NEX', 'SALE', 'VIP', 'FLASH', 'DISCOUNT'];
            const prefix = prefixes[Math.floor(Math.random() * prefixes.length)];
            const randomNum = Math.floor(10 + Math.random() * 90);
            document.getElementById('couponCode').value = prefix + randomNum;
        }

        function toggleScopeInputs() {
            const scope = document.querySelector('input[name="apply_scope"]:checked')?.value || 'all';
            const catBox = document.getElementById('categoryScopeBox');
            const prodBox = document.getElementById('productScopeBox');

            if (scope === 'category') {
                catBox.classList.remove('hidden');
                prodBox.classList.add('hidden');
            } else if (scope === 'product') {
                catBox.classList.add('hidden');
                prodBox.classList.remove('hidden');
            } else {
                catBox.classList.add('hidden');
                prodBox.classList.add('hidden');
            }

            // Update card borders
            document.querySelectorAll('.scope-card').forEach(card => {
                const radio = card.querySelector('input[type="radio"]');
                if (radio && radio.checked) {
                    card.classList.add('border-cyan-500', 'bg-cyan-950/20');
                    card.classList.remove('border-slate-800', 'bg-slate-900/60');
                } else {
                    card.classList.remove('border-cyan-500', 'bg-cyan-950/20');
                    card.classList.add('border-slate-800', 'bg-slate-900/60');
                }
            });
        }

        // Initialize state on load
        toggleDiscountInputs();
        toggleScopeInputs();
    </script>
</body>
</html>