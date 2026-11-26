<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đánh Giá Khách Hàng - NEXVORA Admin</title>
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
                <span class="text-base font-black tracking-tight text-white">NEX<span class="text-cyan-400">VORA</span> <span class="text-xs bg-amber-950 text-amber-300 border border-amber-800 px-2 py-0.5 rounded-md ml-2 font-mono">Đánh Giá Khách Hàng</span></span>
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
                $activeNav = 'reviews';
                require_once __DIR__ . '/partials/sidebar.php'; 
            ?>

            <!-- Main Content Area -->
            <main class="lg:col-span-4 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-white flex items-center gap-2">
                            <i class="fa-solid fa-star-half-stroke text-amber-400"></i> Quản Lý Đánh Giá & Bình Luận
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">Danh sách tất cả bình luận và đánh giá từ khách hàng trên toàn bộ sản phẩm</p>
                    </div>
                </div>

                <?php if (!empty($_SESSION['flash_review_msg'])): ?>
                    <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 text-xs flex items-center justify-between shadow-lg">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
                            <span><?= htmlspecialchars($_SESSION['flash_review_msg']) ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <?php unset($_SESSION['flash_review_msg']); ?>
                <?php endif; ?>

                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden p-6 space-y-4 shadow-xl">
                    <?php if (empty($reviews)): ?>
                        <div class="py-16 text-center text-xs text-slate-500 space-y-2">
                            <i class="fa-regular fa-comment-dots text-4xl block text-slate-600"></i>
                            <div class="font-bold text-slate-300">Chưa có bình luận nào</div>
                            <p class="text-[11px] text-slate-500">Khách hàng chưa gửi đánh giá nào hoặc tất cả đã bị xóa.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="text-slate-400 border-b border-slate-800 text-[11px] uppercase tracking-wider">
                                        <th class="pb-3 font-bold">Sản Phẩm</th>
                                        <th class="pb-3 font-bold">Khách Hàng</th>
                                        <th class="pb-3 font-bold">Đánh Giá</th>
                                        <th class="pb-3 font-bold">Nội Dung</th>
                                        <th class="pb-3 font-bold">Thời Gian</th>
                                        <th class="pb-3 font-bold text-right">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60">
                                    <?php foreach ($reviews as $rev): ?>
                                        <tr class="hover:bg-slate-800/40 transition">
                                            <!-- Sản phẩm -->
                                            <td class="py-4 pr-3 max-w-[200px]">
                                                <a href="<?= BASE_URL ?>index.php?url=products/detail&slug=<?= urlencode($rev['product_slug'] ?? '') ?>#reviews-section" 
                                                   target="_blank" 
                                                   class="flex items-center gap-2.5 group" 
                                                   title="Xem trang chi tiết sản phẩm">
                                                    <div class="w-10 h-10 rounded-xl bg-slate-950 border border-slate-800 p-1 flex items-center justify-center shrink-0 overflow-hidden group-hover:border-cyan-400 transition">
                                                        <img src="<?= htmlspecialchars($rev['product_image'] ?? '') ?>" alt="" class="max-w-full max-h-full object-contain">
                                                    </div>
                                                    <span class="font-bold text-slate-200 group-hover:text-cyan-400 line-clamp-2 transition text-[11px]">
                                                        <?= htmlspecialchars($rev['product_name'] ?? 'Sản phẩm #' . $rev['product_id']) ?>
                                                    </span>
                                                </a>
                                            </td>

                                            <!-- Khách hàng -->
                                            <td class="py-4 pr-3 whitespace-nowrap">
                                                <div class="font-bold text-white flex items-center gap-1.5">
                                                    <?= htmlspecialchars($rev['author_name']) ?>
                                                    <?php if (!empty($rev['is_purchased'])): ?>
                                                        <span class="text-[9px] text-emerald-400 bg-emerald-950/80 px-1.5 py-0.5 rounded border border-emerald-500/30">Đã mua</span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if (!empty($rev['author_phone'])): ?>
                                                    <div class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($rev['author_phone']) ?></div>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Đánh giá sao -->
                                            <td class="py-4 pr-3 whitespace-nowrap">
                                                <div class="flex text-amber-400 text-xs">
                                                    <?php for ($k = 1; $k <= 5; $k++): ?>
                                                        <i class="<?= ($k <= ($rev['rating'] ?? 5)) ? 'fa-solid fa-star' : 'fa-regular fa-star text-slate-700' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                                <div class="text-[10px] text-slate-500 mt-0.5">
                                                    <?= (int)($rev['rating'] ?? 5) ?> / 5 sao
                                                </div>
                                            </td>

                                            <!-- Nội dung -->
                                            <td class="py-4 pr-3 text-slate-300 max-w-xs leading-relaxed">
                                                <p class="line-clamp-3 text-[11px]"><?= htmlspecialchars($rev['content']) ?></p>
                                                <?php if (!empty($rev['likes'])): ?>
                                                    <span class="inline-flex items-center gap-1 text-[10px] text-cyan-400 mt-1">
                                                        <i class="fa-regular fa-thumbs-up"></i> <?= (int)$rev['likes'] ?> hữu ích
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Thời gian -->
                                            <td class="py-4 pr-3 text-slate-400 text-[11px] whitespace-nowrap">
                                                <?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?>
                                            </td>

                                            <!-- Thao tác -->
                                            <td class="py-4 text-right whitespace-nowrap">
                                                <a href="<?= BASE_URL ?>index.php?url=admin/review-delete&id=<?= $rev['id'] ?>&csrf_token=<?= Csrf::token() ?>" 
                                                   onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn đánh giá của <?= addslashes(htmlspecialchars($rev['author_name'])) ?>? Hành động này không thể hoàn tác!')" 
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 font-bold transition text-xs shadow-sm"
                                                   title="Xóa đánh giá này">
                                                    <i class="fa-solid fa-trash-can text-[11px]"></i> Xóa
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
