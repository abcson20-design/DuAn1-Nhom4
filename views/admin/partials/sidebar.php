<?php
$activeNav = $activeNav ?? '';
function getNavClass(string $key, string $active): string {
    if ($key === $active || ($key === 'products' && in_array($active, ['products', 'products_phone', 'products_accessory', 'products_trash']))) {
        return 'bg-gradient-to-r from-primary-600 to-indigo-600 text-white font-bold text-xs shadow-lg shadow-primary-600/30';
    }
    return 'bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white font-semibold text-xs border border-slate-800 transition';
}
?>
<aside class="lg:col-span-1 space-y-2 sticky top-24 self-start">
    <a href="<?= BASE_URL ?>index.php?url=admin" class="flex items-center space-x-3 px-4 py-3 rounded-2xl <?= getNavClass('dashboard', $activeNav) ?>">
        <i class="fa-solid fa-gauge text-sm <?= $activeNav === 'dashboard' ? 'text-white' : 'text-slate-400' ?>"></i>
        <span>Tổng Quan</span>
    </a>
    <a href="<?= BASE_URL ?>index.php?url=admin/statistics" class="flex items-center space-x-3 px-4 py-3 rounded-2xl <?= getNavClass('statistics', $activeNav) ?>">
        <i class="fa-solid fa-chart-pie text-sm <?= $activeNav === 'statistics' ? 'text-white' : 'text-cyan-400' ?>"></i>
        <span>Thống Kê Báo Cáo</span>
    </a>

    <!-- QUẢN LÝ SẢN PHẨM (TÍCH HỢP: ĐIỆN THOẠI, PHỤ KIỆN, THÙNG RÁC) -->
    <a href="<?= BASE_URL ?>index.php?url=admin/products" class="flex items-center justify-between px-4 py-3 rounded-2xl <?= getNavClass('products', $activeNav) ?>">
        <div class="flex items-center space-x-3">
            <i class="fa-solid fa-box text-sm <?= in_array($activeNav, ['products', 'products_phone', 'products_accessory', 'products_trash']) ? 'text-white' : 'text-blue-400' ?>"></i>
            <span>Quản Lý Sản Phẩm</span>
        </div>
        <span class="text-[10px] px-1.5 py-0.5 rounded-md <?= in_array($activeNav, ['products', 'products_phone', 'products_accessory', 'products_trash']) ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' ?>">Kho</span>
    </a>

    <a href="<?= BASE_URL ?>index.php?url=admin/categories" class="flex items-center space-x-3 px-4 py-3 rounded-2xl <?= getNavClass('categories', $activeNav) ?>">
        <i class="fa-solid fa-folder-tree text-sm <?= $activeNav === 'categories' ? 'text-white' : 'text-amber-400' ?>"></i>
        <span>Danh Mục</span>
    </a>
    <a href="<?= BASE_URL ?>index.php?url=admin/brands" class="flex items-center space-x-3 px-4 py-3 rounded-2xl <?= getNavClass('brands', $activeNav) ?>">
        <i class="fa-solid fa-copyright text-sm <?= $activeNav === 'brands' ? 'text-white' : 'text-emerald-400' ?>"></i>
        <span>Hãng / Thương Hiệu</span>
    </a>
    <a href="<?= BASE_URL ?>index.php?url=admin/coupons" class="flex items-center space-x-3 px-4 py-3 rounded-2xl <?= getNavClass('coupons', $activeNav) ?>">
        <i class="fa-solid fa-ticket text-sm <?= $activeNav === 'coupons' ? 'text-white' : 'text-pink-400' ?>"></i>
        <span>Mã Khuyến Mại</span>
    </a>
    <a href="<?= BASE_URL ?>index.php?url=admin/orders" class="flex items-center space-x-3 px-4 py-3 rounded-2xl <?= getNavClass('orders', $activeNav) ?>">
        <i class="fa-solid fa-receipt text-sm <?= $activeNav === 'orders' ? 'text-white' : 'text-purple-400' ?>"></i>
        <span>Đơn Hàng</span>
    </a>
    <a href="<?= BASE_URL ?>index.php?url=admin/contacts" class="flex items-center space-x-3 px-4 py-3 rounded-2xl <?= getNavClass('contacts', $activeNav) ?>">
        <i class="fa-solid fa-envelope text-sm <?= $activeNav === 'contacts' ? 'text-white' : 'text-teal-400' ?>"></i>
        <span>Hộp Thư Liên Hệ</span>
    </a>
    <a href="<?= BASE_URL ?>index.php?url=admin/reviews" class="flex items-center space-x-3 px-4 py-3 rounded-2xl <?= getNavClass('reviews', $activeNav) ?>">
        <i class="fa-solid fa-star-half-stroke text-sm <?= $activeNav === 'reviews' ? 'text-white' : 'text-amber-400' ?>"></i>
        <span>Đánh Giá Khách Hàng</span>
    </a>
    <a href="<?= BASE_URL ?>index.php?url=admin/users" class="flex items-center space-x-3 px-4 py-3 rounded-2xl <?= getNavClass('users', $activeNav) ?>">
        <i class="fa-solid fa-users text-sm <?= $activeNav === 'users' ? 'text-white' : 'text-rose-400' ?>"></i>
        <span>Khách Hàng User</span>
    </a>
</aside>
