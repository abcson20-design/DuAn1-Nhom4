<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê Doanh Thu & Báo Cáo - NEXVORA Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-b from-[#0b172e] via-[#0f2347] to-[#0a162c] text-slate-100 min-h-screen flex flex-col relative overflow-x-hidden selection:bg-primary-600 selection:text-white">
    <?php require_once __DIR__ . '/partials/bg_effects.php'; ?>
    <!-- Admin Navbar -->
    <header class="bg-slate-900 border-b border-slate-800 px-6 py-3 sticky top-0 z-40">
        <div class="flex items-center justify-between max-w-[1550px] mx-auto">
            <div class="flex items-center space-x-3">
                <img src="<?= BASE_URL ?>public/images/logo.png" alt="NEXVORA Logo" class="h-9 w-auto object-contain drop-shadow-[0_0_12px_rgba(6,182,212,0.45)]">
                <span class="text-base font-black tracking-tight text-white">NEX<span class="text-cyan-400">VORA</span> <span class="text-xs bg-cyan-950 text-cyan-300 border border-cyan-800 px-2 py-0.5 rounded-md ml-2 font-mono">Báo Cáo Thống Kê</span></span>
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
                $activeNav = 'statistics';
                require_once __DIR__ . '/partials/sidebar.php'; 
            ?>

            <!-- Main Content Area -->
            <main class="lg:col-span-4 space-y-8">
                <div>
                    <h2 class="text-2xl font-black text-white flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-cyan-400"></i> Báo Cáo & Thống Kê Hoạt Động Kinh Doanh
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Dữ liệu thời gian thực cập nhật từ hệ thống đơn hàng và thanh toán trực tuyến VietQR</p>
                </div>

                <!-- 4 KPI Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-emerald-950/60 to-slate-900 border border-emerald-500/40 p-5 rounded-3xl space-y-2">
                        <div class="flex items-center justify-between text-emerald-400 text-xs font-bold uppercase tracking-wider">
                            <span>Doanh Thu Thực Tế</span>
                            <i class="fa-solid fa-wallet text-base"></i>
                        </div>
                        <div class="text-2xl font-black text-emerald-400"><?= number_format($detailedStats['total_revenue'], 0, ',', '.') ?>₫</div>
                        <span class="text-[11px] text-slate-400 block">Hôm nay: <strong class="text-slate-200"><?= number_format($detailedStats['today_revenue'], 0, ',', '.') ?>₫</strong></span>
                    </div>

                    <div class="bg-gradient-to-br from-cyan-950/60 to-slate-900 border border-cyan-500/40 p-5 rounded-3xl space-y-2">
                        <div class="flex items-center justify-between text-cyan-400 text-xs font-bold uppercase tracking-wider">
                            <span>Tổng Số Đơn Hàng</span>
                            <i class="fa-solid fa-cart-shopping text-base"></i>
                        </div>
                        <div class="text-2xl font-black text-cyan-400"><?= $detailedStats['total_orders'] ?> đơn</div>
                        <span class="text-[11px] text-slate-400 block">Hoàn thành: <strong class="text-emerald-400"><?= $detailedStats['status_counts']['completed'] ?> đơn</strong></span>
                    </div>

                    <div class="bg-gradient-to-br from-amber-950/60 to-slate-900 border border-amber-500/40 p-5 rounded-3xl space-y-2">
                        <div class="flex items-center justify-between text-amber-400 text-xs font-bold uppercase tracking-wider">
                            <span>Khách Hàng Đăng Ký</span>
                            <i class="fa-solid fa-user-group text-base"></i>
                        </div>
                        <div class="text-2xl font-black text-amber-400"><?= $detailedStats['total_users'] ?> User</div>
                        <span class="text-[11px] text-slate-400 block">Tài khoản thành viên hoạt động</span>
                    </div>

                    <div class="bg-gradient-to-br from-purple-950/60 to-slate-900 border border-purple-500/40 p-5 rounded-3xl space-y-2">
                        <div class="flex items-center justify-between text-purple-400 text-xs font-bold uppercase tracking-wider">
                            <span>Mặt Hàng Đang Bán</span>
                            <i class="fa-solid fa-cubes text-base"></i>
                        </div>
                        <div class="text-2xl font-black text-purple-400"><?= $detailedStats['total_products'] ?> SP</div>
                        <span class="text-[11px] text-slate-400 block">Smartphone & Phụ kiện</span>
                    </div>
                </div>

                <!-- 2 Hàng Biểu Đồ Trực Quan (Charts) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Biểu đồ Phương Thức Thanh Toán -->
                    <div class="bg-slate-900/90 border border-slate-800 p-6 rounded-3xl space-y-4">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center justify-between">
                            <span><i class="fa-solid fa-qrcode text-cyan-400 mr-2"></i> Phương Thức Thanh Toán</span>
                        </h3>
                        <div class="h-64 relative flex items-center justify-center">
                            <canvas id="paymentChart"></canvas>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-center text-xs pt-2 border-t border-slate-800">
                            <div>
                                <span class="block text-cyan-400 font-bold"><?= (int)(($detailedStats['payment_methods']['sepay'] ?? 0) + ($detailedStats['payment_methods']['vietqr'] ?? 0) + ($detailedStats['payment_methods']['bank_transfer'] ?? 0)) ?></span>
                                <span class="text-[11px] text-slate-400">Cổng SePay Trực Tuyến</span>
                            </div>
                            <div>
                                <span class="block text-amber-400 font-bold"><?= (int)($detailedStats['payment_methods']['cod'] ?? 0) ?></span>
                                <span class="text-[11px] text-slate-400">Thanh toán COD</span>
                            </div>
                        </div>
                    </div>

                    <!-- Biểu đồ Trạng Thái Đơn Hàng -->
                    <div class="bg-slate-900/90 border border-slate-800 p-6 rounded-3xl space-y-4">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center justify-between">
                            <span><i class="fa-solid fa-truck text-purple-400 mr-2"></i> Trạng Thái Đơn Hàng</span>
                        </h3>
                        <div class="h-64 relative flex items-center justify-center">
                            <canvas id="statusChart"></canvas>
                        </div>
                        <div class="grid grid-cols-5 gap-1.5 text-center text-xs pt-2 border-t border-slate-800">
                            <div>
                                <span class="block text-amber-400 font-bold"><?= $detailedStats['status_counts']['pending'] ?></span>
                                <span class="text-[10px] text-slate-400">Chờ xử lý</span>
                            </div>
                            <div>
                                <span class="block text-blue-400 font-bold"><?= $detailedStats['status_counts']['confirmed'] ?></span>
                                <span class="text-[10px] text-slate-400">Đã duyệt</span>
                            </div>
                            <div>
                                <span class="block text-purple-400 font-bold"><?= $detailedStats['status_counts']['shipping'] ?></span>
                                <span class="text-[10px] text-slate-400">Đang giao</span>
                            </div>
                            <div>
                                <span class="block text-emerald-400 font-bold"><?= $detailedStats['status_counts']['completed'] ?></span>
                                <span class="text-[10px] text-slate-400">Hoàn thành</span>
                            </div>
                            <div>
                                <span class="block text-rose-400 font-bold"><?= $detailedStats['status_counts']['cancelled'] ?></span>
                                <span class="text-[10px] text-slate-400">Đã hủy</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top 5 Sản Phẩm Bán Chạy Nhất & Phân Bổ Danh Mục -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Top Sản phẩm bán chạy (2 Cột) -->
                    <div class="lg:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 space-y-4">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-trophy text-amber-400"></i> Top Sản Phẩm Bán Chạy Nhất
                        </h3>

                        <?php if (empty($detailedStats['top_products'])): ?>
                            <div class="py-10 text-center text-xs text-slate-500">
                                Chưa có đủ dữ liệu bán hàng để xếp hạng sản phẩm.
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php $rank = 1; foreach ($detailedStats['top_products'] as $prod): 
                                    $dispName = $prod['product_name'] ?? $prod['name'] ?? 'Sản phẩm';
                                    $dispName = preg_replace('/\s*\([^)]+\)$/', '', $dispName);
                                    $dispImg = $prod['product_image'] ?? $prod['image'] ?: 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=100';
                                ?>
                                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-950/60 border border-slate-800">
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs <?= $rank === 1 ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : ($rank === 2 ? 'bg-slate-300/20 text-slate-300' : 'bg-slate-800 text-slate-400') ?>">
                                                #<?= $rank++ ?>
                                            </span>
                                            <img src="<?= htmlspecialchars($dispImg) ?>" 
                                                 class="w-10 h-10 object-cover rounded-xl border border-slate-800">
                                            <div>
                                                <div class="text-xs font-bold text-white"><?= htmlspecialchars($dispName) ?></div>
                                                <div class="text-[11px] text-slate-400"><?= htmlspecialchars($prod['category_name'] ?? 'Sản phẩm') ?></div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs font-extrabold text-cyan-400"><?= number_format($prod['total_revenue'], 0, ',', '.') ?>₫</div>
                                            <div class="text-[11px] text-slate-400">Đã bán: <strong class="text-slate-200"><?= $prod['total_sold'] ?></strong> chiếc</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Doanh số theo danh mục (1 Cột) -->
                    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 space-y-4">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-primary-400"></i> Phân Bổ Danh Mục
                        </h3>

                        <div class="space-y-4 pt-1">
                            <?php if (empty($detailedStats['category_revenue'])): ?>
                                <div class="py-10 text-center text-xs text-slate-500">Chưa có giao dịch theo danh mục.</div>
                            <?php else: ?>
                                <?php foreach ($detailedStats['category_revenue'] as $cat): ?>
                                    <div class="space-y-1.5 text-xs">
                                        <div class="flex justify-between font-semibold">
                                            <span class="text-slate-300"><?= htmlspecialchars($cat['name']) ?></span>
                                            <span class="text-cyan-400 font-bold"><?= number_format($cat['revenue'], 0, ',', '.') ?>₫</span>
                                        </div>
                                        <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-primary-500 to-cyan-400 rounded-full" style="width: <?= min(100, max(15, ($cat['revenue'] / ($detailedStats['total_revenue'] ?: 1)) * 100)) ?>%"></div>
                                        </div>
                                        <div class="text-[10px] text-slate-500 text-right">Đã bán <?= $cat['sold_qty'] ?> sản phẩm</div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script>
        // Chart 1: Phương thức thanh toán
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Cổng SePay Trực Tuyến', 'Ship COD'],
                datasets: [{
                    data: [
                        <?= (int)(($detailedStats['payment_methods']['sepay'] ?? 0) + ($detailedStats['payment_methods']['vietqr'] ?? 0) + ($detailedStats['payment_methods']['bank_transfer'] ?? 0)) ?>,
                        <?= (int)($detailedStats['payment_methods']['cod'] ?? 0) ?>
                    ],
                    backgroundColor: ['#22d3ee', '#10b981'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#94a3b8', font: { size: 11, family: 'Inter' } }
                    }
                },
                cutout: '70%'
            }
        });

        // Chart 2: Trạng thái đơn hàng
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: ['Chờ duyệt', 'Xác nhận', 'Đang giao', 'Hoàn tất', 'Hủy'],
                datasets: [{
                    label: 'Số lượng đơn',
                    data: [
                        <?= (int)($detailedStats['status_counts']['pending'] ?? 0) ?>,
                        <?= (int)($detailedStats['status_counts']['confirmed'] ?? 0) ?>,
                        <?= (int)($detailedStats['status_counts']['shipping'] ?? 0) ?>,
                        <?= (int)($detailedStats['status_counts']['completed'] ?? 0) ?>,
                        <?= (int)($detailedStats['status_counts']['cancelled'] ?? 0) ?>
                    ],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#a855f7', '#10b981', '#f43f5e'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        ticks: { color: '#94a3b8', font: { size: 10, family: 'Inter' } },
                        grid: { display: false }
                    },
                    y: {
                        ticks: { color: '#94a3b8', font: { size: 10, family: 'Inter' }, precision: 0 },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    }
                }
            }
        });
    </script>
</body>
</html>
