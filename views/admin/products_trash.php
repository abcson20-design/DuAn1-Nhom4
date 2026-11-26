<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thùng Rác Sản Phẩm - NEXVORA Admin</title>
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
            <!-- Sidebar Navigation -->
            <?php 
                $activeNav = 'products';
                require_once __DIR__ . '/partials/sidebar.php'; 
            ?>

            <main class="lg:col-span-4 space-y-6">
                <!-- Flash Notification Success -->
                <?php if (!empty($_SESSION['flash_admin_msg'])): ?>
                    <div id="flashMsgAlert" class="bg-emerald-950/80 border border-emerald-500/50 text-emerald-300 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-semibold shadow-lg shadow-emerald-500/10">
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

                <!-- Flash Notification Error (Chặn xóa vĩnh viễn khi có đơn hàng) -->
                <?php if (!empty($_SESSION['flash_admin_error'])): ?>
                    <div id="flashErrAlert" class="bg-amber-950/90 border border-amber-500/60 text-amber-200 px-4 py-3.5 rounded-2xl flex items-center justify-between text-xs font-semibold shadow-lg shadow-amber-500/10">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-triangle-exclamation text-amber-400 text-base shrink-0"></i>
                            <span><?= htmlspecialchars($_SESSION['flash_admin_error']) ?></span>
                        </div>
                        <button type="button" onclick="document.getElementById('flashErrAlert').remove()" class="text-amber-400 hover:text-white transition p-1">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                    <?php unset($_SESSION['flash_admin_error']); ?>
                <?php endif; ?>

                <!-- 4 PHÂN HỆ QUẢN LÝ SẢN PHẨM (TÍCH HỢP TRỌN VẸN: TẤT CẢ, ĐIỆN THOẠI, PHỤ KIỆN, THÙNG RÁC) -->
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
                    <a href="<?= BASE_URL ?>index.php?url=admin/products" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-900 text-slate-400 hover:text-white border border-slate-800">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Tất Cả Sản Phẩm</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-slate-800 text-slate-400"><?= $countAll ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/products&type=phone" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-900 text-slate-400 hover:text-white border border-slate-800">
                        <i class="fa-solid fa-mobile-screen text-cyan-400"></i>
                        <span>📱 Quản Lý Điện Thoại</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-cyan-950 text-cyan-400 border border-cyan-800"><?= $countPhones ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/products&type=accessory" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-900 text-slate-400 hover:text-white border border-slate-800">
                        <i class="fa-solid fa-headphones text-purple-400"></i>
                        <span>🎧 Quản Lý Phụ Kiện</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-purple-950 text-purple-400 border border-purple-800"><?= $countAccessories ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/product-trash" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-amber-600 text-white shadow-md shadow-amber-600/30">
                        <i class="fa-solid fa-trash-can text-white"></i>
                        <span>🗑️ Thùng Rác</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-white/20 text-white"><?= $countTrash ?? count($trashProducts) ?></span>
                    </a>
                </div>

                <!-- Header Thùng rác -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                            <i class="fa-solid fa-trash-can text-amber-400"></i>
                            <span>Thùng Rác Sản Phẩm</span>
                            <span class="text-xs bg-amber-950 text-amber-300 border border-amber-800/80 px-2.5 py-0.5 rounded-full font-mono font-normal">
                                <?= count($trashProducts) ?> sản phẩm đã xóa mềm
                            </span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">Sản phẩm ở đây đã ẩn khỏi website nhưng vẫn bảo vệ lịch sử đơn hàng cũ. Bạn có thể khôi phục lại bất kỳ lúc nào.</p>
                    </div>
                    <a href="<?= BASE_URL ?>index.php?url=admin/products" class="bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs px-4 py-2.5 rounded-xl border border-slate-700 transition flex items-center justify-center gap-2 shrink-0">
                        <i class="fa-solid fa-arrow-left text-xs"></i> Quay Lại Danh Sách
                    </a>
                </div>

                <!-- Bảng danh sách sản phẩm đã xóa mềm -->
                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden shadow-xl backdrop-blur-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                                <tr>
                                    <th class="py-3 px-4">Sản Phẩm</th>
                                    <th class="py-3 px-4">Hãng & Danh Mục</th>
                                    <th class="py-3 px-4">Giá Bán</th>
                                    <th class="py-3 px-4">Thời Gian Xóa</th>
                                    <th class="py-3 px-4 text-right">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                <?php if (empty($trashProducts)): ?>
                                    <tr>
                                        <td colspan="5" class="py-12 text-center text-slate-500">
                                            <i class="fa-solid fa-trash-can text-3xl mb-2 text-slate-600 block"></i>
                                            Thùng rác hiện đang trống. Không có sản phẩm nào bị xóa!
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($trashProducts as $item): ?>
                                        <tr class="hover:bg-slate-800/40 transition">
                                            <td class="py-3 px-4">
                                                <div class="flex items-center space-x-3">
                                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="" class="w-12 h-12 object-contain rounded-xl bg-slate-950 p-1 border border-slate-800 shrink-0">
                                                    <div>
                                                        <div class="font-bold text-white text-xs line-clamp-1 max-w-[220px]">
                                                            <?= htmlspecialchars($item['name']) ?>
                                                        </div>
                                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                                            Mã ID: #<?= $item['id'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-xs">
                                                <span class="text-cyan-400 font-medium"><?= htmlspecialchars($item['brand_name'] ?? 'Chưa rõ') ?></span>
                                                <div class="text-[10px] text-slate-500"><?= htmlspecialchars($item['category_name'] ?? '—') ?></div>
                                            </td>
                                            <td class="py-3 px-4 font-bold text-slate-200 text-xs">
                                                <?= number_format($item['price'], 0, ',', '.') ?>đ
                                            </td>
                                            <td class="py-3 px-4 text-[11px] text-slate-400">
                                                <?= !empty($item['deleted_at']) ? date('d/m/Y H:i', strtotime($item['deleted_at'])) : '—' ?>
                                            </td>
                                            <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                                                <!-- Nút Khôi phục -->
                                                <a href="<?= BASE_URL ?>index.php?url=admin/product-restore&id=<?= $item['id'] ?>&csrf_token=<?= Csrf::token() ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500 text-emerald-300 hover:text-white border border-emerald-500/40 text-xs font-bold transition shadow-sm" title="Khôi phục lại sản phẩm ra bán">
                                                    <i class="fa-solid fa-rotate-left"></i> Khôi phục
                                                </a>
                                                <!-- Nút Xóa vĩnh viễn -->
                                                <a href="<?= BASE_URL ?>index.php?url=admin/product-force-delete&id=<?= $item['id'] ?>&csrf_token=<?= Csrf::token() ?>" onclick="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN sản phẩm này khỏi cơ sở dữ liệu? Thao tác này không thể hoàn tác!')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-500/20 hover:bg-red-500 text-red-300 hover:text-white border border-red-500/40 text-xs font-bold transition shadow-sm" title="Xóa vĩnh viễn">
                                                    <i class="fa-solid fa-trash-xmark"></i> Xóa vĩnh viễn
                                                </a>
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
</body>
</html>
