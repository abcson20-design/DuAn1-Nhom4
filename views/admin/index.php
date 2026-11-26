<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Điều Khiển Admin - NEXVORA</title>
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
                $activeNav = 'dashboard';
                require_once __DIR__ . '/partials/sidebar.php'; 
            ?>

            <!-- Main Content Area -->
            <main class="lg:col-span-4 space-y-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-3xl space-y-2">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng Doanh Thu</span>
                        <div class="text-xl font-black text-emerald-400"><?= number_format($stats['total_revenue'] ?? $stats['revenue'] ?? 0, 0, ',', '.') ?>đ</div>
                    </div>
                    <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-3xl space-y-2">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng Đơn Hàng</span>
                        <div class="text-xl font-black text-cyan-400"><?= (int)($stats['total_orders'] ?? $stats['orders'] ?? 0) ?> đơn</div>
                    </div>
                    <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-3xl space-y-2">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Đơn Chờ Xử Lý</span>
                        <div class="text-xl font-black text-amber-400"><?= (int)($stats['pending_orders'] ?? 0) ?> đơn</div>
                    </div>
                    <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-3xl space-y-2">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng Sản Phẩm</span>
                        <div class="text-xl font-black text-primary-400"><?= (int)($stats['total_products'] ?? $stats['products'] ?? 0) ?> mặt hàng</div>
                    </div>
                </div>

                <!-- Recent Orders Table -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden space-y-4 p-6">
                    <div class="flex justify-between items-center pb-3 border-b border-slate-800">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Đơn Hàng Mới Nhất</h3>
                        <a href="<?= BASE_URL ?>index.php?url=admin/orders" class="text-xs text-cyan-400 hover:underline">Xem tất cả</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[10px] text-slate-400 uppercase tracking-wider border-b border-slate-800/80">
                                    <th class="py-3 px-4">Mã Đơn</th>
                                    <th class="py-3 px-4">Khách Hàng</th>
                                    <th class="py-3 px-4">Tổng Tiền</th>
                                    <th class="py-3 px-4">Phương Thức</th>
                                    <th class="py-3 px-4">Thanh Toán</th>
                                    <th class="py-3 px-4">Vận Chuyển</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                <?php if (empty($recentOrders)): ?>
                                    <tr><td colspan="6" class="py-8 text-center text-slate-500">Chưa có đơn hàng nào.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentOrders as $ord): ?>
                                        <tr class="hover:bg-slate-800/40 transition">
                                            <td class="py-3.5 px-4 font-mono font-bold text-cyan-400"><?= htmlspecialchars($ord['order_code']) ?></td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-bold text-white"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                                <div class="text-[10px] text-slate-500"><?= htmlspecialchars($ord['customer_phone']) ?></div>
                                            </td>
                                            <td class="py-3.5 px-4 font-black text-rose-400"><?= number_format($ord['final_amount'], 0, ',', '.') ?>đ</td>
                                            <td class="py-3.5 px-4 uppercase text-[10px] font-bold"><?= htmlspecialchars($ord['payment_method']) ?></td>
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <?php if ($ord['order_status'] === 'cancelled'): ?>
                                                    <?php if ($ord['payment_status'] === 'paid'): ?>
                                                        <span class="bg-rose-500/20 text-rose-300 border border-rose-500/40 px-2 py-0.5 rounded text-[10px] font-bold animate-pulse" title="Đơn đã hủy - Cần hoàn tiền cho khách">Cần hoàn tiền</span>
                                                    <?php elseif ($ord['payment_status'] === 'refunded'): ?>
                                                        <span class="bg-purple-500/20 text-purple-300 border border-purple-500/40 px-2 py-0.5 rounded text-[10px] font-bold">Đã hoàn tiền</span>
                                                    <?php else: ?>
                                                        <span class="bg-slate-800 text-slate-400 border border-slate-700 px-2 py-0.5 rounded text-[10px] font-bold">Đã hủy</span>
                                                    <?php endif; ?>
                                                <?php elseif ($ord['payment_status'] === 'refunded'): ?>
                                                    <span class="bg-purple-500/20 text-purple-300 border border-purple-500/40 px-2 py-0.5 rounded text-[10px] font-bold">Đã hoàn tiền</span>
                                                <?php elseif ($ord['payment_status'] === 'paid'): ?>
                                                    <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 px-2 py-0.5 rounded text-[10px] font-bold">Đã TT</span>
                                                <?php else: ?>
                                                    <span class="bg-amber-500/20 text-amber-300 border border-amber-500/40 px-2 py-0.5 rounded text-[10px] font-bold">Chờ TT</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <?php 
                                                    $statusMap = [
                                                        'pending' => ['label' => 'Chờ duyệt', 'class' => 'bg-amber-500/20 text-amber-300 border-amber-500/40'],
                                                        'confirmed' => ['label' => 'Đã duyệt', 'class' => 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40'],
                                                        'shipping' => ['label' => 'Đang giao', 'class' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40'],
                                                        'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40'],
                                                        'return_requested' => ['label' => 'Chờ duyệt trả hàng', 'class' => 'bg-purple-500/20 text-purple-300 border-purple-500/40'],
                                                        'returned' => ['label' => 'Đã trả hàng', 'class' => 'bg-purple-500/20 text-purple-300 border-purple-500/40'],
                                                        'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-rose-500/20 text-rose-300 border-rose-500/40'],
                                                    ];
                                                    $st = $statusMap[$ord['order_status']] ?? ['label' => $ord['order_status'], 'class' => 'bg-slate-800 text-slate-300 border-slate-700'];
                                                ?>
                                                <span class="<?= $st['class'] ?> border px-2 py-0.5 rounded text-[10px] font-bold"><?= $st['label'] ?></span>
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
