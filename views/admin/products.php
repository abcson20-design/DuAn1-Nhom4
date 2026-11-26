<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm - NEXVORA Admin</title>
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
            <!-- Sidebar Navigation (Đứng im cố định khi cuộn chuột) -->
            <?php 
                $currentType = $type ?? ($_GET['type'] ?? 'all');
                $activeNav = 'products';
                require_once __DIR__ . '/partials/sidebar.php'; 
            ?>

            <main class="lg:col-span-4 space-y-6">
                <!-- Flash Notification nếu có -->
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

                <!-- Header & Nút thêm mới chuyên biệt cho từng phân hệ -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                            <?php if ($currentType === 'phone'): ?>
                                <i class="fa-solid fa-mobile-screen text-cyan-400"></i>
                                <span>Quản Lý Điện Thoại Smartphone</span>
                            <?php elseif ($currentType === 'accessory'): ?>
                                <i class="fa-solid fa-headphones text-purple-400"></i>
                                <span>Quản Lý Phụ Kiện & Tai Nghe</span>
                            <?php else: ?>
                                <i class="fa-solid fa-boxes-stacked text-blue-400"></i>
                                <span>Tất Cả Sản Phẩm Trong Kho</span>
                            <?php endif; ?>
                            <span class="text-xs bg-cyan-950 text-cyan-300 border border-cyan-800/80 px-2.5 py-0.5 rounded-full font-mono font-normal">
                                <?= count($products) ?> sản phẩm
                            </span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">
                            <?php if ($currentType === 'phone'): ?>
                                Phân hệ chuyên biệt dành cho Smartphone: Cấu hình Chipset, RAM, ROM, Camera OIS, Khung viền và Bảo hành IMEI.
                            <?php elseif ($currentType === 'accessory'): ?>
                                Phân hệ chuyên biệt dành cho Phụ kiện: Tai nghe Bluetooth ANC, Củ & Cáp sạc GaN, Pin dự phòng, Độ tương thích dòng máy.
                            <?php else: ?>
                                Toàn bộ kho hàng thiết bị công nghệ và phụ kiện chính hãng tại NEXVORA Store.
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a href="<?= BASE_URL ?>index.php?url=admin/product-create&type=phone" class="bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-cyan-500/20 transition flex items-center justify-center gap-1.5" title="Mở form thêm riêng cho Smartphone">
                            <i class="fa-solid fa-mobile-screen text-xs"></i> <span>+ Thêm Điện Thoại</span>
                        </a>
                        <a href="<?= BASE_URL ?>index.php?url=admin/product-create&type=accessory" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-purple-500/20 transition flex items-center justify-center gap-1.5" title="Mở form thêm riêng cho Tai nghe & Phụ kiện">
                            <i class="fa-solid fa-headphones text-xs"></i> <span>+ Thêm Phụ Kiện</span>
                        </a>
                    </div>
                </div>

                <!-- 4 PHÂN HỆ QUẢN LÝ SẢN PHẨM (TÍCH HỢP TRỌN VẸN: TẤT CẢ, ĐIỆN THOẠI, PHỤ KIỆN, THÙNG RÁC) -->
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
                    <a href="<?= BASE_URL ?>index.php?url=admin/products" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 <?= ($currentType === 'all' || empty($currentType)) ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Tất Cả Sản Phẩm</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full <?= ($currentType === 'all' || empty($currentType)) ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' ?>"><?= $countAll ?? count($products) ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/products&type=phone" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 <?= $currentType === 'phone' ? 'bg-gradient-to-r from-cyan-600 to-blue-600 text-white shadow-md shadow-cyan-600/30' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
                        <i class="fa-solid fa-mobile-screen <?= $currentType === 'phone' ? 'text-white' : 'text-cyan-400' ?>"></i>
                        <span>📱 Quản Lý Điện Thoại</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full <?= $currentType === 'phone' ? 'bg-white/20 text-white' : 'bg-cyan-950 text-cyan-400 border border-cyan-800' ?>"><?= $countPhones ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/products&type=accessory" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 <?= $currentType === 'accessory' ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md shadow-purple-600/30' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
                        <i class="fa-solid fa-headphones <?= $currentType === 'accessory' ? 'text-white' : 'text-purple-400' ?>"></i>
                        <span>🎧 Quản Lý Phụ Kiện</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full <?= $currentType === 'accessory' ? 'bg-white/20 text-white' : 'bg-purple-950 text-purple-400 border border-purple-800' ?>"><?= $countAccessories ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/product-trash" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-900 text-slate-400 hover:text-amber-300 border border-slate-800 hover:border-amber-600/40">
                        <i class="fa-solid fa-trash-can text-amber-400"></i>
                        <span>🗑️ Thùng Rác</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-amber-950 text-amber-400 border border-amber-800/80"><?= $countTrash ?? 0 ?></span>
                    </a>
                </div>

                <?php 
                    $hasActiveFilters = !empty($keyword) || !empty($categoryId) || !empty($brandId);
                ?>

                <!-- THANH ĐIỀU KHIỂN: NÚT "BỘ LỌC" SỔ RA & THỐNG KÊ -->
                <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-900/80 border border-slate-800 rounded-2xl p-3 shadow-md">
                    <div class="flex items-center gap-3">
                        <button type="button" 
                                onclick="toggleAdminFilter('productFilterCollapse', 'productFilterChevron')" 
                                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 <?= $hasActiveFilters ? 'bg-gradient-to-r from-cyan-600 to-blue-600 text-white shadow-md shadow-cyan-600/30' : 'bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700' ?>">
                            <i class="fa-solid fa-filter text-xs <?= $hasActiveFilters ? 'text-white' : 'text-cyan-400' ?>"></i>
                            <span>Bộ Lọc</span>
                            <?php if ($hasActiveFilters): ?>
                                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                <span class="text-[10px] bg-white/20 text-white px-1.5 py-0.5 rounded-full font-bold">Đang lọc</span>
                            <?php endif; ?>
                            <i id="productFilterChevron" class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300"></i>
                        </button>

                        <?php if ($hasActiveFilters): ?>
                            <a href="<?= BASE_URL ?>index.php?url=admin/products<?= (!empty($currentType) && $currentType !== 'all') ? '&type=' . urlencode($currentType) : '' ?>" 
                               class="text-xs text-rose-400 hover:text-rose-300 transition flex items-center gap-1 font-semibold">
                                <i class="fa-solid fa-rotate-left text-[11px]"></i> Xóa bộ lọc
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-300">
                        <i class="fa-solid fa-boxes-stacked text-cyan-400"></i>
                        <span>Hiển thị: <strong class="text-white"><?= count($products) ?></strong> sản phẩm</span>
                    </div>
                </div>

                <!-- KHỐI BỘ LỌC SỔ RA KHI BẤM NÚT "BỘ LỌC" (TỰ ĐỘNG ĐÓNG KHI LỌC XONG) -->
                <div id="productFilterCollapse" class="hidden transition-all duration-300">
                    <form action="<?= BASE_URL ?>index.php" method="GET" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl space-y-3">
                        <input type="hidden" name="url" value="admin/products">
                        <?php if (!empty($currentType) && $currentType !== 'all'): ?>
                            <input type="hidden" name="type" value="<?= htmlspecialchars($currentType) ?>">
                        <?php endif; ?>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-5 relative">
                                <label class="block text-[11px] font-bold text-slate-400 mb-1">
                                    <i class="fa-solid fa-magnifying-glass text-cyan-400 mr-1"></i> Tên sản phẩm, hãng
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input type="text" name="keyword" value="<?= htmlspecialchars($keyword ?? '') ?>" placeholder="Tìm kiếm theo tên sản phẩm, hãng..." class="w-full text-xs pl-9 pr-3 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-400 mb-1">
                                    <i class="fa-solid fa-folder-tree text-amber-400 mr-1"></i> Danh mục
                                </label>
                                <select name="category_id" class="w-full text-xs p-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                                    <option value="">-- Tất cả danh mục --</option>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= (!empty($categoryId) && (int)$categoryId === (int)$cat['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-bold text-slate-400 mb-1">
                                    <i class="fa-solid fa-copyright text-emerald-400 mr-1"></i> Hãng
                                </label>
                                <select name="brand_id" class="w-full text-xs p-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                                    <option value="">-- Tất cả hãng --</option>
                                    <?php if (!empty($brands)): ?>
                                        <?php foreach ($brands as $b): ?>
                                            <option value="<?= $b['id'] ?>" <?= (!empty($brandId) && (int)$brandId === (int)$b['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($b['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="sm:col-span-2 flex items-end gap-2 pt-5">
                                <button type="submit" class="flex-1 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white text-xs font-bold py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-1.5 shadow-md shadow-cyan-600/20">
                                    <i class="fa-solid fa-filter text-[11px]"></i> Lọc
                                </button>
                                <?php if ($hasActiveFilters): ?>
                                    <a href="<?= BASE_URL ?>index.php?url=admin/products" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold py-2.5 px-3 rounded-xl border border-slate-700 transition" title="Xóa bộ lọc">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Bảng danh sách sản phẩm -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden p-6 shadow-xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[10px] text-slate-400 uppercase tracking-wider border-b border-slate-800/80">
                                    <th class="py-3 px-3 whitespace-nowrap">Ảnh</th>
                                    <th class="py-3 px-3">Tên Sản Phẩm</th>
                                    <th class="py-3 px-3 whitespace-nowrap">Danh Mục</th>
                                    <th class="py-3 px-3 whitespace-nowrap">Giá Bán</th>
                                    <th class="py-3 px-3 whitespace-nowrap">Kho Hàng</th>
                                    <th class="py-3 px-3 whitespace-nowrap">Trình Chiếu Chi Tiết</th>
                                    <th class="py-3 px-3 text-right whitespace-nowrap">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $p): ?>
                                        <?php
                                        // Kiểm tra ảnh slideshow
                                        $galleryList = [];
                                        if (!empty($p['gallery'])) {
                                            $galleryList = json_decode($p['gallery'], true) ?: [];
                                        }
                                        $slideCount = count($galleryList);
                                        ?>
                                        <tr class="hover:bg-slate-800/40 transition">
                                            <!-- Cột Ảnh -->
                                            <td class="py-3 px-3 whitespace-nowrap">
                                                <div class="w-12 h-12 rounded-xl bg-slate-950 p-1 border border-slate-800 flex items-center justify-center overflow-hidden">
                                                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-contain hover:scale-110 transition duration-200">
                                                </div>
                                            </td>

                                            <!-- Cột Tên & Tag -->
                                            <td class="py-3 px-3 max-w-xs">
                                                <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                                    <?php if (!empty($p['is_featured'])): ?>
                                                        <span class="bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[9px] px-1.5 py-0.5 rounded font-bold">Nổi bật</span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($p['is_hot'])): ?>
                                                        <span class="bg-rose-500/20 text-rose-300 border border-rose-500/40 text-[9px] px-1.5 py-0.5 rounded font-bold">HOT</span>
                                                    <?php endif; ?>
                                                </div>
                                                <strong class="text-white block text-xs hover:text-cyan-400 transition"><?= htmlspecialchars($p['name']) ?></strong>
                                                <span class="text-[11px] text-slate-400">Hãng: <span class="text-slate-300 font-semibold"><?= htmlspecialchars($p['brand_name'] ?? 'N/A') ?></span></span>
                                            </td>

                                            <!-- Cột Danh mục -->
                                            <td class="py-3 px-3 whitespace-nowrap">
                                                <?php 
                                                    $isPhoneItem = ($p['category_id'] == 1) || (!empty($p['category_type']) && $p['category_type'] === 'phone') || (strpos(mb_strtolower($p['category_name'] ?? ''), 'điện thoại') !== false);
                                                ?>
                                                <?php if ($isPhoneItem): ?>
                                                    <span class="inline-flex items-center gap-1.5 bg-cyan-950/90 text-cyan-300 border border-cyan-800/80 px-2.5 py-1 rounded-lg text-[10px] font-semibold">
                                                        <i class="fa-solid fa-mobile-screen text-cyan-400 text-[10px]"></i>
                                                        <?= htmlspecialchars($p['category_name']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1.5 bg-purple-950/90 text-purple-300 border border-purple-800/80 px-2.5 py-1 rounded-lg text-[10px] font-semibold">
                                                        <i class="fa-solid fa-headphones text-purple-400 text-[10px]"></i>
                                                        <?= htmlspecialchars($p['category_name']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Cột Giá bán -->
                                            <td class="py-3 px-3 whitespace-nowrap">
                                                <div class="font-black text-rose-400 text-xs"><?= number_format($p['price'], 0, ',', '.') ?>đ</div>
                                                <?php if (!empty($p['old_price']) && $p['old_price'] > $p['price']): ?>
                                                    <div class="text-[10px] text-slate-500 line-through">
                                                        <?= number_format($p['old_price'], 0, ',', '.') ?>đ
                                                    </div>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Cột Kho -->
                                            <td class="py-3 px-3 whitespace-nowrap">
                                                <?php if ((int)$p['quantity'] > 10): ?>
                                                    <span class="inline-flex items-center gap-1 text-emerald-400 font-bold bg-emerald-950/40 border border-emerald-800/40 px-2 py-0.5 rounded-md text-[11px]">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> <?= $p['quantity'] ?> Sản phẩm
                                                    </span>
                                                <?php elseif ((int)$p['quantity'] > 0): ?>
                                                    <span class="inline-flex items-center gap-1 text-amber-400 font-bold bg-amber-950/40 border border-amber-800/40 px-2 py-0.5 rounded-md text-[11px]">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Còn <?= $p['quantity'] ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1 text-rose-400 font-bold bg-rose-950/40 border border-rose-800/40 px-2 py-0.5 rounded-md text-[11px]">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Hết hàng
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Cột Trình Chiếu Trang Chi Tiết (Slideshow hoặc Video) -->
                                            <td class="py-3 px-3 whitespace-nowrap">
                                                <?php if (!empty($p['media_type']) && $p['media_type'] === 'video' && !empty($p['video'])): ?>
                                                    <a href="<?= BASE_URL ?>index.php?url=admin/product-edit&id=<?= $p['id'] ?>" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-950/60 border border-rose-800/60 text-rose-300 hover:text-white hover:bg-rose-900 transition text-[10px] font-bold" title="Sản phẩm đang dùng Video trải nghiệm">
                                                        <i class="fa-solid fa-circle-play text-[10px] text-rose-400"></i> Video Thực Tế
                                                    </a>
                                                <?php elseif ($slideCount > 0): ?>
                                                    <a href="<?= BASE_URL ?>index.php?url=admin/product-edit&id=<?= $p['id'] ?>" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-cyan-950/60 border border-cyan-800/60 text-cyan-300 hover:text-white hover:bg-cyan-900 transition text-[10px] font-bold" title="Bấm để chỉnh sửa bộ ảnh slideshow">
                                                        <i class="fa-solid fa-photo-film text-[10px] text-cyan-400"></i>
                                                        <?= $slideCount ?> ảnh slide
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= BASE_URL ?>index.php?url=admin/product-edit&id=<?= $p['id'] ?>" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-800/70 border border-slate-700 text-slate-400 hover:text-cyan-300 hover:border-cyan-700 transition text-[10px]" title="Chưa tạo slide riêng (đang dùng mặc định). Bấm để thêm!">
                                                        <i class="fa-solid fa-images text-[10px]"></i> Mẫu mặc định
                                                    </a>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Cột Thao tác: 3 nút xếp chồng lên nhau, có khoảng cách thông thoáng -->
                                            <td class="py-3 px-3 text-right whitespace-nowrap align-middle">
                                                <div class="inline-flex flex-col items-stretch gap-2 min-w-[85px]">
                                                    <!-- Nút 1: Xem trên Web -->
                                                    <a href="<?= BASE_URL ?>index.php?url=product/detail&id=<?= $p['id'] ?>" target="_blank" class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 transition text-xs font-semibold shadow-sm" title="Xem sản phẩm trên Website">
                                                        <i class="fa-solid fa-eye mr-1.5 text-[11px] text-cyan-400"></i> Xem
                                                    </a>
                                                    <!-- Nút 2: Sửa -->
                                                    <a href="<?= BASE_URL ?>index.php?url=admin/product-edit&id=<?= $p['id'] ?>" class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-cyan-950/70 border border-cyan-800/80 text-cyan-300 hover:text-white hover:bg-cyan-600 transition text-xs font-bold shadow-sm" title="Chỉnh sửa thông tin & bộ ảnh slideshow">
                                                        <i class="fa-solid fa-pen-to-square mr-1.5 text-[11px]"></i> Sửa
                                                    </a>
                                                    <!-- Nút 3: Xóa -->
                                                    <a href="<?= BASE_URL ?>index.php?url=admin/product-delete&id=<?= $p['id'] ?>&csrf_token=<?= Csrf::token() ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm «<?= addslashes(htmlspecialchars($p['name'])) ?>» không?');" class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-rose-950/60 border border-rose-800/80 text-rose-400 hover:text-white hover:bg-rose-600 transition text-xs font-bold shadow-sm" title="Xóa sản phẩm">
                                                        <i class="fa-solid fa-trash mr-1.5 text-[11px]"></i> Xóa
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="py-14 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center space-y-3 max-w-sm mx-auto">
                                                <div class="w-16 h-16 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center text-slate-600">
                                                    <i class="fa-solid fa-box-open text-2xl"></i>
                                                </div>
                                                <?php if (!empty($keyword) || !empty($categoryId) || !empty($brandId)): ?>
                                                    <span class="text-sm font-semibold text-slate-300">Không tìm thấy sản phẩm nào theo bộ lọc!</span>
                                                    <a href="<?= BASE_URL ?>index.php?url=admin/products" class="text-xs text-cyan-400 hover:underline">
                                                        <i class="fa-solid fa-rotate-left mr-1"></i> Xóa bộ lọc tìm kiếm
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-sm font-semibold text-slate-200">Kho hàng hiện đang trống!</span>
                                                    <p class="text-xs text-slate-400">Tất cả sản phẩm đã được xóa sạch. Bạn có thể bắt đầu tạo sản phẩm mới ngay bây giờ.</p>
                                                    <div class="mt-2 flex items-center justify-center gap-2">
                                                        <a href="<?= BASE_URL ?>index.php?url=admin/product-create&type=phone" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold text-xs shadow-lg transition">
                                                            <i class="fa-solid fa-mobile-screen text-xs"></i> Thêm Điện Thoại
                                                        </a>
                                                        <a href="<?= BASE_URL ?>index.php?url=admin/product-create&type=accessory" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg transition">
                                                            <i class="fa-solid fa-headphones text-xs"></i> Thêm Phụ Kiện
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
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
