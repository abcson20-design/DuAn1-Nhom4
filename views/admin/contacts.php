<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Liên Hệ Khách Hàng - NEXVORA Admin</title>
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
                <span class="text-base font-black tracking-tight text-white">NEX<span class="text-cyan-400">VORA</span> <span class="text-xs bg-cyan-950 text-cyan-300 border border-cyan-800 px-2 py-0.5 rounded-md ml-2 font-mono">Hộp Thư Liên Hệ</span></span>
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
                $activeNav = 'contacts';
                require_once __DIR__ . '/partials/sidebar.php'; 
            ?>

            <!-- Main Content Area -->
            <main class="lg:col-span-4 space-y-6">
                <div>
                    <h2 class="text-2xl font-black text-white flex items-center gap-2">
                        <i class="fa-solid fa-envelope-open-text text-emerald-400"></i> Quản Lý Ý Kiến & Liên Hệ Khách Hàng
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Danh sách tin nhắn được gửi từ thành viên User đã đăng nhập</p>
                </div>

                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden p-6 space-y-4">
                    <?php if (empty($contacts)): ?>
                        <div class="py-12 text-center text-xs text-slate-500">
                            <i class="fa-regular fa-envelope text-3xl mb-2 block text-slate-600"></i>
                            Chưa có tin nhắn liên hệ nào từ khách hàng.
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="text-slate-400 border-b border-slate-800">
                                        <th class="pb-3 font-semibold">Khách Hàng</th>
                                        <th class="pb-3 font-semibold">Chủ Đề</th>
                                        <th class="pb-3 font-semibold">Nội Dung</th>
                                        <th class="pb-3 font-semibold">Thời Gian</th>
                                        <th class="pb-3 font-semibold">Trạng Thái</th>
                                        <th class="pb-3 font-semibold text-right">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60">
                                    <?php foreach ($contacts as $c): ?>
                                        <tr class="hover:bg-slate-800/30 transition">
                                            <td class="py-4">
                                                <div class="font-bold text-white"><?= htmlspecialchars($c['fullname']) ?></div>
                                                <div class="text-[11px] text-slate-400"><?= htmlspecialchars($c['email']) ?></div>
                                                <?php if (!empty($c['phone'])): ?>
                                                    <div class="text-[11px] text-cyan-400"><?= htmlspecialchars($c['phone']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-4 text-slate-300 font-semibold">
                                                <?= htmlspecialchars($c['subject']) ?>
                                            </td>
                                            <td class="py-4 text-slate-400 max-w-xs">
                                                <p class="line-clamp-2"><?= htmlspecialchars($c['message']) ?></p>
                                            </td>
                                            <td class="py-4 text-slate-400 text-[11px] whitespace-nowrap">
                                                <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?>
                                            </td>
                                            <td class="py-4 whitespace-nowrap">
                                                <?php if ($c['status'] === 'new'): ?>
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">Mới gửi</span>
                                                <?php elseif ($c['status'] === 'read'): ?>
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/30">Đã đọc</span>
                                                <?php else: ?>
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Đã phản hồi</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-4 text-right whitespace-nowrap">
                                                <form action="<?= BASE_URL ?>index.php?url=admin/contact-status" method="POST" class="inline-flex gap-1">
                                                    <?= Csrf::field() ?>
                                                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                    <?php if ($c['status'] === 'new'): ?>
                                                        <button type="submit" name="status" value="read" class="px-2 py-1 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-bold">
                                                            Đánh dấu đọc
                                                        </button>
                                                    <?php elseif ($c['status'] === 'read'): ?>
                                                        <button type="submit" name="status" value="replied" class="px-2 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold">
                                                            Đã phản hồi
                                                        </button>
                                                    <?php endif; ?>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- [BỔ SUNG] Danh sách Đặt giữ chỗ nhanh của khách vãng lai -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden p-6 space-y-4">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2"><i class="fa-solid fa-bookmark text-cyan-400"></i> Khách Vãng Lai Đặt Giữ Chỗ (<?= count($guestReservations ?? []) ?>)</h3>
                    <?php if (empty($guestReservations)): ?>
                        <div class="py-8 text-center text-xs text-slate-500">Chưa có yêu cầu giữ chỗ nào.</div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="text-slate-400 border-b border-slate-800">
                                        <th class="pb-3 font-semibold">Khách Hàng</th>
                                        <th class="pb-3 font-semibold">Sản Phẩm</th>
                                        <th class="pb-3 font-semibold">Địa Chỉ / Ghi Chú</th>
                                        <th class="pb-3 font-semibold">Thời Gian</th>
                                        <th class="pb-3 font-semibold">Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60">
                                    <?php foreach ($guestReservations as $r): ?>
                                        <tr class="hover:bg-slate-800/30 transition">
                                            <td class="py-4">
                                                <div class="font-bold text-white"><?= htmlspecialchars($r['customer_name']) ?></div>
                                                <div class="text-[11px] text-cyan-400 font-mono"><?= htmlspecialchars($r['customer_phone']) ?></div>
                                            </td>
                                            <td class="py-4 text-slate-300">
                                                <?= htmlspecialchars($r['product_name']) ?><?= !empty($r['color']) ? ' - ' . htmlspecialchars($r['color']) : '' ?> × <?= (int)$r['quantity'] ?>
                                                <div class="text-[11px] text-amber-400"><?= number_format((float)$r['price'], 0, ',', '.') ?>đ</div>
                                            </td>
                                            <td class="py-4 text-slate-400 max-w-xs">
                                                <p class="line-clamp-2"><?= htmlspecialchars($r['customer_address']) ?></p>
                                                <?php if (!empty($r['notes'])): ?><p class="text-[11px] text-slate-500 line-clamp-1"><?= htmlspecialchars($r['notes']) ?></p><?php endif; ?>
                                            </td>
                                            <td class="py-4 text-slate-400 text-[11px] whitespace-nowrap"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                                            <td class="py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30"><?= htmlspecialchars(['pending'=>'Chờ gọi lại','contacted'=>'Đã liên hệ','completed'=>'Hoàn tất','cancelled'=>'Đã hủy'][$r['status']] ?? $r['status']) ?></span>
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
