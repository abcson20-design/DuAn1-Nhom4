<?php
$isEdit = !empty($brand);
$pageTitle = $isEdit ? 'Chỉnh Sửa Thương Hiệu: ' . htmlspecialchars($brand['name']) : 'Thêm Thương Hiệu Mới';
?>
<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - NEXVORA Admin</title>
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
            <!-- Sidebar -->
            <?php 
                $activeNav = 'brands';
                require_once __DIR__ . '/partials/sidebar.php'; 
            ?>

            <!-- Main Content Area -->
            <main class="lg:col-span-4 space-y-6">
                <div class="flex items-center justify-between bg-slate-900/60 backdrop-blur p-6 rounded-3xl border border-slate-800">
                    <div class="flex items-center space-x-4">
                        <a href="<?= BASE_URL ?>index.php?url=admin/brands" class="w-10 h-10 rounded-2xl bg-slate-800 hover:bg-slate-700 border border-slate-700 flex items-center justify-center text-slate-300 hover:text-white transition">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <div>
                            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                                <i class="fa-solid fa-copyright text-emerald-400"></i> <?= $pageTitle ?>
                            </h2>
                            <p class="text-slate-400 text-xs mt-0.5"><?= $isEdit ? 'Chỉnh sửa thông tin hãng/thương hiệu' : 'Thêm thương hiệu mới vào hệ thống' ?></p>
                        </div>
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs flex items-center space-x-2 shadow-lg">
                        <i class="fa-solid fa-circle-exclamation text-base"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
                    <?= Csrf::field() ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tên thương hiệu -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Tên Thương Hiệu / Hãng <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" name="name" id="brandName" required value="<?= htmlspecialchars($_POST['name'] ?? $brand['name'] ?? '') ?>" placeholder="VD: Apple, Samsung, Xiaomi, Anker, Baseus..." class="w-full bg-slate-950/80 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition">
                        </div>

                        <!-- Slug SEO -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Đường Dẫn Tĩnh (Slug)
                            </label>
                            <input type="text" name="slug" id="brandSlug" value="<?= htmlspecialchars($_POST['slug'] ?? $brand['slug'] ?? '') ?>" placeholder="Tự động tạo nếu để trống (vd: apple, anker)" class="w-full bg-slate-950/80 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-slate-300 font-mono focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition">
                            <p class="text-[10px] text-slate-500">Đường dẫn dùng cho bộ lọc theo hãng trên website</p>
                        </div>

                        <!-- Phân loại hãng -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Phân Loại Hãng
                            </label>
                            <?php 
                                $isPhone = isset($_POST['is_phone_brand']) ? (bool)$_POST['is_phone_brand'] : (!empty($brand['is_phone_brand']));
                            ?>
                            <label class="flex items-center space-x-3 p-4 rounded-2xl bg-slate-950/80 border border-slate-700 cursor-pointer hover:border-emerald-400/50 transition">
                                <input type="checkbox" name="is_phone_brand" value="1" <?= $isPhone ? 'checked' : '' ?> class="w-4 h-4 rounded text-emerald-500 focus:ring-emerald-400 bg-slate-900 border-slate-700">
                                <div>
                                    <div class="text-xs font-bold text-white flex items-center gap-1.5">
                                        <i class="fa-solid fa-mobile-screen text-cyan-400"></i> Đây là Hãng sản xuất Điện thoại thông minh (Smartphone)
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Nếu chọn, hãng này sẽ xuất hiện trên menu bộ lọc nhanh Điện thoại của trang chủ (Apple, Samsung, Xiaomi, OPPO, vivo...)</p>
                                </div>
                            </label>
                        </div>

                        <!-- Link ảnh Logo thương hiệu -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Link Ảnh Logo Thương Hiệu
                            </label>
                            <div class="flex flex-col sm:flex-row gap-4 items-start">
                                <div class="flex-1 w-full space-y-2">
                                    <input type="text" name="logo" id="brandLogo" value="<?= htmlspecialchars($_POST['logo'] ?? $brand['logo'] ?? '') ?>" placeholder="https://example.com/logo.png hoặc public/images/..." class="w-full bg-slate-950/80 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition">
                                    <p class="text-[10px] text-slate-500">Khuyên dùng ảnh logo định dạng PNG hoặc SVG nền trong suốt (transparent)</p>
                                </div>
                                <div class="w-24 h-16 rounded-2xl bg-slate-950 border border-slate-800 p-2 flex items-center justify-center shrink-0 shadow-inner">
                                    <img id="logoPreview" src="<?= htmlspecialchars($_POST['logo'] ?? $brand['logo'] ?? '') ?>" alt="Xem trước logo" class="max-w-full max-h-full object-contain <?= empty($_POST['logo'] ?? $brand['logo'] ?? '') ? 'hidden' : '' ?>" onerror="this.classList.add('hidden')">
                                    <span id="logoPlaceholder" class="text-[10px] text-slate-600 font-mono <?= !empty($_POST['logo'] ?? $brand['logo'] ?? '') ? 'hidden' : '' ?>">Logo</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nút hành động -->
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-800">
                        <a href="<?= BASE_URL ?>index.php?url=admin/brands" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold text-xs border border-slate-700 transition">
                            Hủy Bỏ
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition flex items-center">
                            <i class="fa-solid fa-floppy-disk mr-1.5"></i> <?= $isEdit ? 'Lưu Thay Đổi' : 'Tạo Thương Hiệu Mới' ?>
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
        const logoInput = document.getElementById('brandLogo');
        const logoPreview = document.getElementById('logoPreview');
        const logoPlaceholder = document.getElementById('logoPlaceholder');

        logoInput.addEventListener('input', function() {
            const url = this.value.trim();
            if (url) {
                logoPreview.src = url;
                logoPreview.classList.remove('hidden');
                logoPlaceholder.classList.add('hidden');
            } else {
                logoPreview.src = '';
                logoPreview.classList.add('hidden');
                logoPlaceholder.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
