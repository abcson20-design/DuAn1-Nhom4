<?php
$activeNav = $activeNav ?? '';
$adminName = $_SESSION['admin_user']['fullname'] ?? $_SESSION['admin_user']['username'] ?? 'Quản trị viên';
$adminEmail = $_SESSION['admin_user']['email'] ?? 'admin@nexvora.vn';

function getNavBtnClass(string $key, string $active): string {
    $isMatch = ($key === $active) || ($key === 'products' && in_array($active, ['products', 'products_phone', 'products_accessory', 'products_trash'], true));
    if ($isMatch) {
        return 'bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-bold shadow-md shadow-cyan-500/20 border border-cyan-400/30';
    }
    return 'bg-slate-900/50 hover:bg-slate-800/80 text-slate-300 hover:text-white font-medium border border-slate-800/80 hover:border-slate-700/80 transition-all duration-200';
}

function getIconClass(string $key, string $active, string $defaultColor): string {
    $isMatch = ($key === $active) || ($key === 'products' && in_array($active, ['products', 'products_phone', 'products_accessory', 'products_trash'], true));
    return $isMatch ? 'text-white' : $defaultColor;
}
?>
<aside class="lg:col-span-1 space-y-4 sticky top-20 self-start">
    <!-- Nút Quay lại trang trước (Admin Sidebar) -->
    <button type="button" 
            onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '<?= BASE_URL ?>index.php?url=admin'; }" 
            class="w-full py-2.5 px-3.5 rounded-2xl bg-slate-900/90 hover:bg-slate-800 text-slate-200 hover:text-cyan-400 border border-slate-700/80 hover:border-cyan-500/40 text-xs font-bold transition flex items-center justify-center gap-2 shadow-md group"
            title="Quay lại trang trước đó">
        <i class="fa-solid fa-arrow-left text-cyan-400 group-hover:-translate-x-1 transition-transform"></i>
        <span>Quay Lại Trang Trước</span>
    </button>

    <!-- Admin Profile Badge -->
    <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800 backdrop-blur shadow-lg flex items-center gap-3">
        <div class="relative shrink-0">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-white font-black text-sm shadow-md shadow-cyan-500/20">
                <?= strtoupper(mb_substr($adminName, 0, 1, 'UTF-8')) ?>
            </div>
            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-slate-900 rounded-full" title="Đang trực tuyến"></span>
        </div>
        <div class="min-w-0 flex-1">
            <div class="text-xs font-bold text-white truncate" title="<?= htmlspecialchars($adminName) ?>"><?= htmlspecialchars($adminName) ?></div>
            <div class="flex items-center gap-1.5 mt-0.5">
                <span class="text-[9px] uppercase tracking-wider font-extrabold px-1.5 py-0.5 rounded bg-cyan-950 text-cyan-300 border border-cyan-800/80">Quản trị viên</span>
            </div>
        </div>
    </div>

    <!-- Navigation Menu Grouped -->
    <div class="p-3 rounded-2xl bg-slate-900/60 border border-slate-800/80 backdrop-blur shadow-xl space-y-3.5 text-xs">
        <!-- NHÓM 1: TỔNG QUAN HỆ THỐNG -->
        <div>
            <div class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 px-3 py-1 flex items-center gap-1.5">
                <i class="fa-solid fa-gauge-high text-cyan-400 text-[10px]"></i>
                <span>Hệ Thống</span>
            </div>
            <div class="space-y-1 mt-1">
                <a href="<?= BASE_URL ?>index.php?url=admin" class="flex items-center gap-2.5 px-3 py-2 rounded-xl <?= getNavBtnClass('dashboard', $activeNav) ?>">
                    <i class="fa-solid fa-house text-xs w-4 text-center <?= getIconClass('dashboard', $activeNav, 'text-cyan-400') ?>"></i>
                    <span>Tổng Quan</span>
                </a>
                <a href="<?= BASE_URL ?>index.php?url=admin/statistics" class="flex items-center gap-2.5 px-3 py-2 rounded-xl <?= getNavBtnClass('statistics', $activeNav) ?>">
                    <i class="fa-solid fa-chart-line text-xs w-4 text-center <?= getIconClass('statistics', $activeNav, 'text-indigo-400') ?>"></i>
                    <span>Thống Kê Báo Cáo</span>
                </a>
            </div>
        </div>

        <!-- NHÓM 2: SẢN PHẨM & KHO -->
        <div class="pt-2 border-t border-slate-800/80">
            <div class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 px-3 py-1 flex items-center gap-1.5">
                <i class="fa-solid fa-boxes-stacked text-blue-400 text-[10px]"></i>
                <span>Kho & Sản Phẩm</span>
            </div>
            <div class="space-y-1 mt-1">
                <a href="<?= BASE_URL ?>index.php?url=admin/products" class="flex items-center justify-between px-3 py-2 rounded-xl <?= getNavBtnClass('products', $activeNav) ?>">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-box text-xs w-4 text-center <?= getIconClass('products', $activeNav, 'text-blue-400') ?>"></i>
                        <span>Sản Phẩm</span>
                    </div>
                    <span class="text-[9px] px-1.5 py-0.5 rounded <?= in_array($activeNav, ['products', 'products_phone', 'products_accessory', 'products_trash'], true) ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' ?>">Kho</span>
                </a>
                <a href="<?= BASE_URL ?>index.php?url=admin/categories" class="flex items-center gap-2.5 px-3 py-2 rounded-xl <?= getNavBtnClass('categories', $activeNav) ?>">
                    <i class="fa-solid fa-folder-tree text-xs w-4 text-center <?= getIconClass('categories', $activeNav, 'text-amber-400') ?>"></i>
                    <span>Danh Mục</span>
                </a>
                <a href="<?= BASE_URL ?>index.php?url=admin/brands" class="flex items-center gap-2.5 px-3 py-2 rounded-xl <?= getNavBtnClass('brands', $activeNav) ?>">
                    <i class="fa-solid fa-copyright text-xs w-4 text-center <?= getIconClass('brands', $activeNav, 'text-emerald-400') ?>"></i>
                    <span>Thương Hiệu</span>
                </a>
            </div>
        </div>

        <!-- NHÓM 3: BÁN HÀNG & KHUYẾN MẠI -->
        <div class="pt-2 border-t border-slate-800/80">
            <div class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 px-3 py-1 flex items-center gap-1.5">
                <i class="fa-solid fa-cart-shopping text-purple-400 text-[10px]"></i>
                <span>Bán Hàng</span>
            </div>
            <div class="space-y-1 mt-1">
                <a href="<?= BASE_URL ?>index.php?url=admin/orders" class="flex items-center gap-2.5 px-3 py-2 rounded-xl <?= getNavBtnClass('orders', $activeNav) ?>">
                    <i class="fa-solid fa-receipt text-xs w-4 text-center <?= getIconClass('orders', $activeNav, 'text-purple-400') ?>"></i>
                    <span>Đơn Hàng</span>
                </a>
                <a href="<?= BASE_URL ?>index.php?url=admin/coupons" class="flex items-center gap-2.5 px-3 py-2 rounded-xl <?= getNavBtnClass('coupons', $activeNav) ?>">
                    <i class="fa-solid fa-ticket text-xs w-4 text-center <?= getIconClass('coupons', $activeNav, 'text-pink-400') ?>"></i>
                    <span>Mã Khuyến Mại</span>
                </a>
            </div>
        </div>

        <!-- NHÓM 4: KHÁCH HÀNG & TƯƠNG TÁC -->
        <div class="pt-2 border-t border-slate-800/80">
            <div class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 px-3 py-1 flex items-center gap-1.5">
                <i class="fa-solid fa-users-gear text-rose-400 text-[10px]"></i>
                <span>Khách Hàng</span>
            </div>
            <div class="space-y-1 mt-1">
                <a href="<?= BASE_URL ?>index.php?url=admin/users" class="flex items-center gap-2.5 px-3 py-2 rounded-xl <?= getNavBtnClass('users', $activeNav) ?>">
                    <i class="fa-solid fa-users text-xs w-4 text-center <?= getIconClass('users', $activeNav, 'text-rose-400') ?>"></i>
                    <span>Khách Hàng User</span>
                </a>
                <a href="<?= BASE_URL ?>index.php?url=admin/reviews" class="flex items-center gap-2.5 px-3 py-2 rounded-xl <?= getNavBtnClass('reviews', $activeNav) ?>">
                    <i class="fa-solid fa-star-half-stroke text-xs w-4 text-center <?= getIconClass('reviews', $activeNav, 'text-amber-400') ?>"></i>
                    <span>Đánh Giá SP</span>
                </a>
                <a href="<?= BASE_URL ?>index.php?url=admin/contacts" class="flex items-center gap-2.5 px-3 py-2 rounded-xl <?= getNavBtnClass('contacts', $activeNav) ?>">
                    <i class="fa-solid fa-envelope text-xs w-4 text-center <?= getIconClass('contacts', $activeNav, 'text-teal-400') ?>"></i>
                    <span>Hộp Thư Liên Hệ</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Footer Links -->
    <div class="p-2 rounded-2xl bg-slate-900/40 border border-slate-800/60 flex items-center justify-between text-[11px] text-slate-400">
        <a href="<?= BASE_URL ?>" target="_blank" class="px-2.5 py-1 rounded-lg hover:text-cyan-300 hover:bg-slate-800/60 transition flex items-center gap-1">
            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
            <span>Xem Web</span>
        </a>
        <a href="<?= BASE_URL ?>index.php?url=admin/logout" class="px-2.5 py-1 rounded-lg text-rose-400 hover:text-white hover:bg-rose-500/20 transition flex items-center gap-1">
            <i class="fa-solid fa-power-off text-[10px]"></i>
            <span>Đăng xuất</span>
        </a>
    </div>
</aside>
