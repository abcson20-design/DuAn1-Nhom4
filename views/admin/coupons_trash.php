<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thùng Rác Mã Khuyến Mại - NEXVORA Admin</title>
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
                            <i class="fa-solid fa-trash-can text-amber-400"></i>
                            <span>Thùng Rác Mã Khuyến Mại</span>
                            <span class="text-xs bg-amber-950 text-amber-300 border border-amber-800/80 px-2.5 py-0.5 rounded-full font-mono font-normal">
                                <?= count($trashCoupons) ?> mã đã xóa
                            </span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">
                            Danh sách các mã khuyến mại đã chuyển vào thùng rác. Bạn có thể khôi phục lại hoặc xóa vĩnh viễn.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a href="<?= BASE_URL ?>index.php?url=admin/coupons" class="bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs px-4 py-2.5 rounded-xl border border-slate-700 transition flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-arrow-left text-xs"></i> <span>Quay Lại Danh Sách</span>
                        </a>
                    </div>
                </div>

                <!-- 2 PHÂN HỆ: TẤT CẢ & THÙNG RÁC (ĐỒNG BỘ 100%) -->
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
                    <a href="<?= BASE_URL ?>index.php?url=admin/coupons" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-900 text-slate-400 hover:text-white border border-slate-800">
                        <i class="fa-solid fa-ticket"></i>
                        <span>🏷️ Danh Sách Mã Khuyến Mại</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-slate-800 text-slate-400"><?= $countAll ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/coupon-trash" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-md shadow-amber-600/30">
                        <i class="fa-solid fa-trash-can text-white"></i>
                        <span>🗑️ Thùng Rác</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-white/20 text-white"><?= $countTrash ?? count($trashCoupons) ?></span>
                    </a>
                </div>

                <!-- Bảng Danh Sách Trong Thùng Rác -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[10px] text-slate-400 uppercase tracking-wider border-b border-slate-800/80 bg-slate-950/60">
                                    <th class="py-3 px-4 whitespace-nowrap">ID</th>
                                    <th class="py-3 px-4">Mã Voucher & Tiêu Đề</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Mức Giảm</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Thời Gian Xóa</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                <?php if (!empty($trashCoupons)): ?>
                                    <?php foreach ($trashCoupons as $c): ?>
                                        <tr class="hover:bg-slate-800/40 transition">
                                            <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                                #<?= $c['id'] ?>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-slate-400 font-mono font-bold text-xs tracking-wider line-through">
                                                        <?= htmlspecialchars($c['code']) ?>
                                                    </span>
                                                    <span class="font-bold text-slate-300 text-xs">
                                                        <?= htmlspecialchars($c['title']) ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <?php if ($c['discount_type'] === 'percent'): ?>
                                                    <span class="text-xs font-bold text-purple-400">
                                                        Giảm <?= (float)$c['discount_value'] ?>%
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-xs font-bold text-emerald-400">
                                                        -<?= number_format($c['discount_value'], 0, ',', '.') ?>đ
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap text-slate-400 text-xs font-mono">
                                                <?= !empty($c['deleted_at']) ? date('d/m/Y H:i:s', strtotime($c['deleted_at'])) : '---' ?>
                                            </td>
                                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-2">
                                                    <!-- Nút Khôi Phục -->
                                                    <a href="<?= BASE_URL ?>index.php?url=admin/coupon-restore&id=<?= $c['id'] ?>&csrf_token=<?= Csrf::token() ?>" 
                                                       class="px-3 py-1.5 rounded-xl bg-emerald-950/80 hover:bg-emerald-600 border border-emerald-800 text-emerald-300 hover:text-white flex items-center gap-1.5 text-xs font-bold transition shadow-sm"
                                                       title="Khôi phục lại mã này">
                                                        <i class="fa-solid fa-rotate-left text-[11px]"></i>
                                                        <span>Khôi phục</span>
                                                    </a>
                                                    <!-- Nút Xóa Vĩnh Viễn -->
                                                    <a href="<?= BASE_URL ?>index.php?url=admin/coupon-force-delete&id=<?= $c['id'] ?>&csrf_token=<?= Csrf::token() ?>" 
                                                       onclick="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN mã khuyến mại [<?= htmlspecialchars($c['code']) ?>]? Hành động này không thể hoàn tác!')" 
                                                       class="px-3 py-1.5 rounded-xl bg-rose-950/80 hover:bg-rose-600 border border-rose-800 text-rose-300 hover:text-white flex items-center gap-1.5 text-xs font-bold transition shadow-sm"
                                                       title="Xóa vĩnh viễn khỏi Database">
                                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                                        <span>Xóa vĩnh viễn</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-12 text-slate-500">
                                            <i class="fa-solid fa-trash-can text-4xl mb-3 text-slate-600 block"></i>
                                            <p class="text-xs">Thùng rác hiện đang trống. Không có mã khuyến mại nào bị xóa.</p>
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
</body>
</html>