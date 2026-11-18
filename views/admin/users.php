<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Khách Hàng - NEXVORA Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-b from-[#0b172e] via-[#0f2347] to-[#0a162c] text-slate-100 min-h-screen flex flex-col relative overflow-x-hidden selection:bg-primary-600 selection:text-white">
    <?php require_once __DIR__ . '/partials/bg_effects.php'; ?>
    <!-- Admin Navbar -->
    <header class="bg-slate-900 border-b border-slate-800 px-6 py-3 sticky top-0 z-40">
        <div class="flex items-center justify-between max-w-7xl mx-auto">
            <div class="flex items-center space-x-3">
                <img src="<?= BASE_URL ?>public/images/logo.png" alt="NEXVORA Logo" class="h-9 w-auto object-contain drop-shadow-[0_0_12px_rgba(6,182,212,0.45)]">
                <span class="text-base font-black tracking-tight text-white">NEX<span class="text-cyan-400">VORA</span> <span class="text-xs bg-cyan-950 text-cyan-300 border border-cyan-800 px-2 py-0.5 rounded-md ml-2 font-mono">Quản Lý Thành Viên</span></span>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-1 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
            <!-- Sidebar Navigation -->
            <?php 
                $activeNav = 'users';
                require_once __DIR__ . '/partials/sidebar.php'; 
            ?>

            <!-- Main Content Area -->
            <main class="lg:col-span-4 space-y-6">
                <div>
                    <h2 class="text-2xl font-black text-white flex items-center gap-2">
                        <i class="fa-solid fa-user-shield text-amber-400"></i> Danh Sách Thành Viên & Quản Trị
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Phân quyền tài khoản hệ thống: Khách (Chưa đăng nhập), User (Khách hàng), Admin (Quản trị viên)</p>
                </div>

                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden p-6 space-y-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-800">
                                    <th class="pb-3 font-semibold">Thành Viên</th>
                                    <th class="pb-3 font-semibold">Số Điện Thoại</th>
                                    <th class="pb-3 font-semibold">Địa Chỉ</th>
                                    <th class="pb-3 font-semibold">Vai Trò</th>
                                    <th class="pb-3 font-semibold">Ngày Tạo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60">
                                <?php foreach ($users as $u): ?>
                                    <tr class="hover:bg-slate-800/30 transition">
                                        <td class="py-4">
                                            <div class="font-bold text-white"><?= htmlspecialchars($u['fullname']) ?></div>
                                            <div class="text-[11px] text-slate-400"><?= htmlspecialchars($u['email']) ?></div>
                                        </td>
                                        <td class="py-4 text-cyan-400 font-mono">
                                            <?= htmlspecialchars($u['phone'] ?: 'Chưa cập nhật') ?>
                                        </td>
                                        <td class="py-4 text-slate-400 max-w-xs truncate">
                                            <?= htmlspecialchars($u['address'] ?: 'Chưa có địa chỉ') ?>
                                        </td>
                                        <td class="py-4 whitespace-nowrap">
                                            <?php if ($u['role'] === 'admin'): ?>
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30 flex items-center gap-1 w-fit">
                                                    <i class="fa-solid fa-shield-halved"></i> Admin
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 flex items-center gap-1 w-fit">
                                                    <i class="fa-solid fa-user"></i> User
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 text-slate-400 text-[11px] whitespace-nowrap">
                                            <?= !empty($u['created_at']) ? date('d/m/Y', strtotime($u['created_at'])) : '—' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
