<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thùng Rác Danh Mục - NEXVORA Admin</title>
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
                $activeNav = 'categories';
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

                <!-- Header Banner -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                            <i class="fa-solid fa-trash-can text-amber-400"></i>
                            <span>Thùng Rác Danh Mục</span>
                            <span class="text-xs bg-amber-950 text-amber-300 border border-amber-800/80 px-2.5 py-0.5 rounded-full font-mono font-normal">
                                <?= count($trashCategories) ?> danh mục đã xóa mềm
                            </span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">
                            Các danh mục đã xóa mềm sẽ ẩn khỏi website nhưng vẫn bảo vệ liên kết sản phẩm và đơn hàng cũ. Bạn có thể khôi phục lại bất kỳ lúc nào.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a href="<?= BASE_URL ?>index.php?url=admin/categories" class="bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs px-4 py-2.5 rounded-xl border border-slate-700 transition flex items-center justify-center gap-2 shrink-0">
                            <i class="fa-solid fa-arrow-left text-xs"></i> Quay Lại Danh Sách
                        </a>
                    </div>
                </div>

                <!-- 4 PHÂN HỆ QUẢN LÝ DANH MỤC (TÍCH HỢP TRỌN VẸN: TẤT CẢ, ĐIỆN THOẠI, PHỤ KIỆN, THÙNG RÁC) -->
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
                    <a href="<?= BASE_URL ?>index.php?url=admin/categories" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-900 text-slate-400 hover:text-white border border-slate-800">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Tất Cả Danh Mục</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-slate-800 text-slate-400"><?= $countAll ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/categories&type=phone" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-900 text-slate-400 hover:text-white border border-slate-800">
                        <i class="fa-solid fa-mobile-screen text-cyan-400"></i>
                        <span>📱 Danh Mục Điện Thoại</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-cyan-950 text-cyan-400 border border-cyan-800"><?= $countPhones ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/categories&type=accessory" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-900 text-slate-400 hover:text-white border border-slate-800">
                        <i class="fa-solid fa-headphones text-purple-400"></i>
                        <span>🎧 Danh Mục Phụ Kiện</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-purple-950 text-purple-400 border border-purple-800"><?= $countAccessories ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/category-trash" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-amber-600 text-white shadow-md shadow-amber-600/30">
                        <i class="fa-solid fa-trash-can text-white"></i>
                        <span>🗑️ Thùng Rác</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-white/20 text-white"><?= count($trashCategories) ?></span>
                    </a>
                </div>

                <!-- Bảng Danh Sách Danh Mục Đã Xóa -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[10px] text-slate-400 uppercase tracking-wider border-b border-slate-800/80 bg-slate-950/60">
                                    <th class="py-3 px-4 whitespace-nowrap">ID</th>
                                    <th class="py-3 px-4">Tên Danh Mục & Biểu Tượng</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Phân Loại</th>
                                    <th class="py-3 px-4 whitespace-nowrap text-center">Sản Phẩm</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Thời Gian Xóa</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                <?php if (empty($trashCategories)): ?>
                                    <tr>
                                        <td colspan="6" class="py-12 text-center text-slate-500">
                                            <i class="fa-solid fa-trash-can text-3xl mb-2 text-slate-600 block"></i>
                                            Thùng rác danh mục hiện đang trống! Không có danh mục nào bị xóa.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($trashCategories as $c): ?>
                                        <tr class="hover:bg-slate-800/40 transition">
                                            <td class="py-4 px-4 font-mono text-slate-500">#<?= $c['id'] ?></td>
                                            <td class="py-4 px-4">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-400 shrink-0 text-base shadow-inner">
                                                        <i class="<?= htmlspecialchars($c['icon'] ?: 'fa-solid fa-folder') ?>"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="text-white text-sm block"><?= htmlspecialchars($c['name']) ?></strong>
                                                        <span class="text-[11px] text-slate-500 font-mono"><?= htmlspecialchars($c['slug']) ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 whitespace-nowrap">
                                                <?php if ($c['type'] === 'phone'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                                        <i class="fa-solid fa-mobile-screen mr-1 text-[9px]"></i> Điện Thoại
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                                        <i class="fa-solid fa-headphones mr-1 text-[9px]"></i> Phụ Kiện
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-4 px-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center justify-center min-w-[28px] h-6 px-2 rounded-full font-bold text-xs <?= ($c['product_count'] ?? 0) > 0 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-800 text-slate-500' ?>">
                                                    <?= (int)($c['product_count'] ?? 0) ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 whitespace-nowrap text-slate-400 font-mono text-[11px]">
                                                <?= !empty($c['deleted_at']) ? date('d/m/Y H:i', strtotime($c['deleted_at'])) : 'Vừa xong' ?>
                                            </td>
                                            <td class="py-4 px-4 text-right whitespace-nowrap">
                                                <div class="inline-flex items-center space-x-2">
                                                    <a href="<?= BASE_URL ?>index.php?url=admin/category-restore&id=<?= $c['id'] ?>&csrf_token=<?= Csrf::token() ?>" class="p-2 rounded-xl bg-slate-800 hover:bg-emerald-500/20 text-emerald-400 hover:text-emerald-300 border border-slate-700 hover:border-emerald-500/30 transition flex items-center gap-1.5" title="Khôi phục danh mục">
                                                        <i class="fa-solid fa-rotate-left"></i> Khôi phục
                                                    </a>
                                                    <a href="<?= BASE_URL ?>index.php?url=admin/category-force-delete&id=<?= $c['id'] ?>&csrf_token=<?= Csrf::token() ?>" onclick="return confirm('CẢNH BÁO: Thao tác này sẽ xóa vĩnh viễn danh mục \'<?= htmlspecialchars(addslashes($c['name'])) ?>\' khỏi cơ sở dữ liệu và KHÔNG THỂ khôi phục lại! Bạn có chắc chắn muốn xóa không?');" class="p-2 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 border border-slate-700 hover:border-rose-500/30 transition flex items-center gap-1.5" title="Xóa vĩnh viễn">
                                                        <i class="fa-solid fa-trash-can"></i> Xóa hẳn
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
                            Tổng số <span class="font-bold text-white"><?= count($trashCategories) ?></span> danh mục trong thùng rác
                        </div>
                        <div class="text-[11px] text-slate-500">
                            Xóa vĩnh viễn chỉ cho phép khi danh mục không còn sản phẩm
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
