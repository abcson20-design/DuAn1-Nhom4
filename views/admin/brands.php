<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Thương Hiệu / Hãng - NEXVORA Admin</title>
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
                $activeNav = 'brands';
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

                <?php 
                    $currentType = $type ?? ($_GET['type'] ?? 'all');
                ?>

                <!-- Header Banner & Nút Thao Tác Nhanh -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                            <?php if ($currentType === 'phone'): ?>
                                <i class="fa-solid fa-mobile-screen text-cyan-400"></i>
                                <span>Hãng Điện Thoại (Smartphone)</span>
                            <?php elseif ($currentType === 'accessory'): ?>
                                <i class="fa-solid fa-headphones text-purple-400"></i>
                                <span>Hãng Phụ Kiện & Âm Thanh</span>
                            <?php else: ?>
                                <i class="fa-solid fa-copyright text-emerald-400"></i>
                                <span>Tất Cả Thương Hiệu / Hãng</span>
                            <?php endif; ?>
                            <span class="text-xs bg-emerald-950 text-emerald-300 border border-emerald-800/80 px-2.5 py-0.5 rounded-full font-mono font-normal">
                                <?= count($brands) ?> thương hiệu
                            </span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">
                            <?php if ($currentType === 'phone'): ?>
                                Danh sách các thương hiệu Smartphone hàng đầu thế giới: Apple, Samsung, Xiaomi, OPPO.
                            <?php elseif ($currentType === 'accessory'): ?>
                                Danh sách các thương hiệu phụ kiện âm thanh & nguồn điện cao cấp: Sony, Anker, Baseus, Soundpeats, Ugreen, Hoco.
                            <?php else: ?>
                                Danh sách các đối tác thương hiệu điện thoại & phụ kiện chính hãng tại NEXVORA Store.
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a href="<?= BASE_URL ?>index.php?url=admin/brand-create" class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-500/20 transition flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-plus text-xs"></i> <span>+ Thêm Thương Hiệu</span>
                        </a>
                    </div>
                </div>

                <!-- 4 PHÂN HỆ QUẢN LÝ THƯƠNG HIỆU (TÍCH HỢP TRỌN VẸN: TẤT CẢ, ĐIỆN THOẠI, PHỤ KIỆN, THÙNG RÁC) -->
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
                    <a href="<?= BASE_URL ?>index.php?url=admin/brands" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 <?= ($currentType === 'all' || empty($currentType)) ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Tất Cả Thương Hiệu</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full <?= ($currentType === 'all' || empty($currentType)) ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' ?>"><?= $countAll ?? count($brands) ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/brands&type=phone" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 <?= $currentType === 'phone' ? 'bg-gradient-to-r from-cyan-600 to-blue-600 text-white shadow-md shadow-cyan-600/30' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
                        <i class="fa-solid fa-mobile-screen <?= $currentType === 'phone' ? 'text-white' : 'text-cyan-400' ?>"></i>
                        <span>📱 Hãng Điện Thoại</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full <?= $currentType === 'phone' ? 'bg-white/20 text-white' : 'bg-cyan-950 text-cyan-400 border border-cyan-800' ?>"><?= $countPhones ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/brands&type=accessory" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 <?= $currentType === 'accessory' ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md shadow-purple-600/30' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
                        <i class="fa-solid fa-headphones <?= $currentType === 'accessory' ? 'text-white' : 'text-purple-400' ?>"></i>
                        <span>🎧 Hãng Phụ Kiện</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full <?= $currentType === 'accessory' ? 'bg-white/20 text-white' : 'bg-purple-950 text-purple-400 border border-purple-800' ?>"><?= $countAccessories ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/brand-trash" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-900 text-slate-400 hover:text-amber-300 border border-slate-800 hover:border-amber-600/40">
                        <i class="fa-solid fa-trash-can text-amber-400"></i>
                        <span>🗑️ Thùng Rác</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-amber-950 text-amber-400 border border-amber-800/80"><?= $countTrash ?? 0 ?></span>
                    </a>
                </div>

                <!-- Nút bấm Mở / Đóng Bộ Lọc & Tìm Kiếm -->
                <?php 
                    $hasActiveFilters = !empty($keyword);
                ?>
                <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-900/90 border border-slate-800 rounded-2xl px-5 py-3.5 shadow-sm">
                    <button type="button" 
                            onclick="toggleAdminFilter('brandFilterCollapse', 'brandFilterChevron')" 
                            class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold cursor-pointer">
                        <i class="fa-solid fa-filter text-white"></i>
                        <span>Bộ Lọc</span>
                        <?php if ($hasActiveFilters): ?>
                            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                        <?php endif; ?>
                        <i id="brandFilterChevron" class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300"></i>
                    </button>

                    <div class="flex items-center gap-2 text-xs">
                        <?php if ($hasActiveFilters): ?>
                            <span class="text-emerald-400 font-semibold flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-[10px]"></i> Đang áp dụng lọc từ khóa
                            </span>
                            <a href="<?= BASE_URL ?>index.php?url=admin/brands<?= !empty($currentType) && $currentType !== 'all' ? '&type=' . urlencode($currentType) : '' ?>" class="text-xs text-slate-400 hover:text-rose-400 transition underline underline-offset-2 flex items-center gap-1 ml-2">
                                <i class="fa-solid fa-xmark"></i> Xóa lọc
                            </a>
                        <?php else: ?>
                            <span class="text-slate-400 text-xs">Bấm "Bộ Lọc" để tìm kiếm thương hiệu & hãng nhanh</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Khối Bộ Lọc Thu Phóng (Mặc định ẩn, tự động đóng khi lọc xong) -->
                <div id="brandFilterCollapse" class="hidden transition-all duration-300">
                    <form action="<?= BASE_URL ?>index.php" method="GET" class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 shadow-sm">
                        <input type="hidden" name="url" value="admin/brands">
                        <?php if ($currentType !== 'all' && !empty($currentType)): ?>
                            <input type="hidden" name="type" value="<?= htmlspecialchars($currentType) ?>">
                        <?php endif; ?>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-8 relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input type="text" name="keyword" value="<?= htmlspecialchars($keyword ?? '') ?>" placeholder="Tìm kiếm theo tên thương hiệu, hãng, đường dẫn slug..." class="w-full text-xs pl-9 pr-3 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-emerald-400">
                            </div>
                            <div class="sm:col-span-4 flex items-center gap-2">
                                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-filter text-[11px]"></i> Lọc
                                </button>
                                <?php if ($hasActiveFilters): ?>
                                    <a href="<?= BASE_URL ?>index.php?url=admin/brands<?= !empty($currentType) && $currentType !== 'all' ? '&type=' . urlencode($currentType) : '' ?>" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold py-2.5 px-3 rounded-xl border border-slate-700 transition" title="Xóa bộ lọc">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Bảng Danh Sách Thương Hiệu -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[10px] text-slate-400 uppercase tracking-wider border-b border-slate-800/80 bg-slate-950/60">
                                    <th class="py-3 px-4 whitespace-nowrap">ID</th>
                                    <th class="py-3 px-4">Logo & Tên Thương Hiệu</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Đường Dẫn (Slug)</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Phân Loại Hãng</th>
                                    <th class="py-3 px-4 whitespace-nowrap text-center">Sản Phẩm</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                <?php if (empty($brands)): ?>
                                    <tr>
                                        <td colspan="6" class="py-12 text-center text-slate-500">
                                            <i class="fa-solid fa-copyright text-3xl mb-2 text-slate-600 block"></i>
                                            Không tìm thấy thương hiệu nào phù hợp.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($brands as $b): ?>
                                        <tr class="hover:bg-slate-800/40 transition">
                                            <td class="py-4 px-4 font-mono text-slate-500">#<?= $b['id'] ?></td>
                                            <td class="py-4 px-4">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-12 h-10 rounded-xl bg-slate-950 border border-slate-800 p-1 flex items-center justify-center shrink-0 shadow-inner overflow-hidden">
                                                        <?php if (!empty($b['logo'])): ?>
                                                            <img src="<?= htmlspecialchars($b['logo']) ?>" alt="<?= htmlspecialchars($b['name']) ?>" class="max-w-full max-h-full object-contain">
                                                        <?php else: ?>
                                                            <i class="fa-solid fa-tag text-slate-500 text-sm"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <strong class="text-white text-sm block"><?= htmlspecialchars($b['name']) ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 whitespace-nowrap font-mono text-slate-400 text-[11px]">
                                                <?= htmlspecialchars($b['slug']) ?>
                                            </td>
                                            <td class="py-4 px-4 whitespace-nowrap">
                                                <?php if (!empty($b['is_phone_brand'])): ?>
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                                        <i class="fa-solid fa-mobile-screen mr-1 text-[9px]"></i> Hãng Điện Thoại
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                                        <i class="fa-solid fa-plug-circle-bolt mr-1 text-[9px]"></i> Hãng Phụ Kiện
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-4 px-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center justify-center min-w-[28px] h-6 px-2 rounded-full font-bold text-xs <?= ($b['product_count'] ?? 0) > 0 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-800 text-slate-500' ?>">
                                                    <?= (int)($b['product_count'] ?? 0) ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center space-x-2">
                                                    <a href="<?= BASE_URL ?>index.php?url=admin/brand-edit&id=<?= $b['id'] ?>" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-400 hover:text-cyan-300 border border-slate-700 transition" title="Chỉnh sửa thương hiệu">
                                                        <i class="fa-solid fa-pen-to-square"></i> Sửa
                                                    </a>
                                                    <a href="<?= BASE_URL ?>index.php?url=admin/brand-delete&id=<?= $b['id'] ?>&csrf_token=<?= Csrf::token() ?>" onclick="return confirm('Bạn có chắc chắn muốn chuyển thương hiệu \'<?= htmlspecialchars(addslashes($b['name'])) ?>\' vào Thùng rác không? Bạn có thể khôi phục lại bất kỳ lúc nào.');" class="p-2 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 border border-slate-700 hover:border-rose-500/30 transition" title="Chuyển vào Thùng rác">
                                                        <i class="fa-solid fa-trash-can"></i> Xóa
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Footer Bảng -->
                    <div class="border-t border-slate-800/80 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400 bg-slate-950/40">
                        <div>
                            Hiển thị <span class="font-bold text-white"><?= count($brands) ?></span> thương hiệu
                        </div>
                        <div class="text-[11px] text-slate-500">
                            Phân hệ thương hiệu đồng bộ với cấu trúc Sản phẩm
                        </div>
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
