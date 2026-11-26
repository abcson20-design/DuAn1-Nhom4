<?php
$isEdit = !empty($category);
$pageTitle = $isEdit ? 'Chỉnh Sửa Danh Mục: ' . htmlspecialchars($category['name']) : 'Thêm Danh Mục Mới';
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
                $activeNav = 'categories';
                require_once __DIR__ . '/partials/sidebar.php'; 
            ?>

            <!-- Main Content Area -->
            <main class="lg:col-span-4 space-y-6">
                <div class="flex items-center justify-between bg-slate-900/60 backdrop-blur p-6 rounded-3xl border border-slate-800">
                    <div class="flex items-center space-x-4">
                        <a href="<?= BASE_URL ?>index.php?url=admin/categories" class="w-10 h-10 rounded-2xl bg-slate-800 hover:bg-slate-700 border border-slate-700 flex items-center justify-center text-slate-300 hover:text-white transition">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <div>
                            <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                                <i class="fa-solid fa-folder-tree text-amber-400"></i> <?= $pageTitle ?>
                            </h2>
                            <p class="text-slate-400 text-xs mt-0.5"><?= $isEdit ? 'Chỉnh sửa thông tin danh mục hiển thị trên website' : 'Tạo thêm danh mục mới cho sản phẩm' ?></p>
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
                        <!-- Tên danh mục -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Tên Danh Mục <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" name="name" id="categoryName" required value="<?= htmlspecialchars($_POST['name'] ?? $category['name'] ?? '') ?>" placeholder="VD: Củ sạc nhanh, Cáp sạc, Tai nghe..." class="w-full bg-slate-950/80 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition">
                        </div>

                        <!-- Slug SEO -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Đường Dẫn Tĩnh (Slug)
                            </label>
                            <input type="text" name="slug" id="categorySlug" value="<?= htmlspecialchars($_POST['slug'] ?? $category['slug'] ?? '') ?>" placeholder="Tự động tạo nếu để trống (vd: cu-sac-nhanh)" class="w-full bg-slate-950/80 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-slate-300 font-mono focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition">
                            <p class="text-[10px] text-slate-500">Đường dẫn dùng cho URL lọc và SEO trên website</p>
                        </div>

                        <!-- Loại danh mục -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Phân Loại Danh Mục <span class="text-rose-400">*</span>
                            </label>
                            <?php $currType = $_POST['type'] ?? $category['type'] ?? 'accessory'; ?>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="flex items-center space-x-3 p-3.5 rounded-2xl bg-slate-950/80 border border-slate-700 cursor-pointer hover:border-amber-400/50 transition">
                                    <input type="radio" name="type" value="accessory" <?= $currType === 'accessory' ? 'checked' : '' ?> class="text-amber-500 focus:ring-amber-400">
                                    <div>
                                        <div class="text-xs font-bold text-white flex items-center gap-1.5">
                                            <i class="fa-solid fa-headphones text-purple-400"></i> Phụ Kiện
                                        </div>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Sạc, Cáp, Tai nghe, Ốp lưng...</p>
                                    </div>
                                </label>
                                <label class="flex items-center space-x-3 p-3.5 rounded-2xl bg-slate-950/80 border border-slate-700 cursor-pointer hover:border-amber-400/50 transition">
                                    <input type="radio" name="type" value="phone" <?= $currType === 'phone' ? 'checked' : '' ?> class="text-amber-500 focus:ring-amber-400">
                                    <div>
                                        <div class="text-xs font-bold text-white flex items-center gap-1.5">
                                            <i class="fa-solid fa-mobile-screen text-cyan-400"></i> Điện Thoại
                                        </div>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Smartphone, Điện thoại thông minh</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Icon FontAwesome -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center justify-between">
                                <span>Biểu Tượng (FontAwesome Icon)</span>
                                <span class="text-amber-400 font-normal normal-case flex items-center gap-1.5">
                                    Xem trước: <i id="iconPreview" class="<?= htmlspecialchars($_POST['icon'] ?? $category['icon'] ?? 'fa-solid fa-folder') ?> text-base"></i>
                                </span>
                            </label>
                            <input type="text" name="icon" id="categoryIcon" value="<?= htmlspecialchars($_POST['icon'] ?? $category['icon'] ?? '') ?>" placeholder="VD: fa-solid fa-mobile-screen, fa-solid fa-bolt..." class="w-full bg-slate-950/80 border border-slate-700 rounded-2xl px-4 py-3 text-xs text-white font-mono focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition">
                            
                            <!-- Gợi ý icon phổ biến -->
                            <div class="pt-1 flex flex-wrap gap-1.5 items-center">
                                <span class="text-[10px] text-slate-500 mr-1">Gợi ý nhanh:</span>
                                <?php
                                $presetIcons = [
                                    'fa-solid fa-mobile-screen' => 'Điện thoại',
                                    'fa-solid fa-bolt' => 'Củ sạc',
                                    'fa-solid fa-network-wired' => 'Cáp sạc',
                                    'fa-solid fa-headphones' => 'Tai nghe',
                                    'fa-solid fa-battery-three-quarters' => 'Sạc dự phòng',
                                    'fa-solid fa-shield-halved' => 'Ốp lưng',
                                    'fa-solid fa-clock' => 'Đồng hồ',
                                    'fa-solid fa-plug' => 'Adapter',
                                    'fa-solid fa-layer-group' => 'Chung'
                                ];
                                foreach ($presetIcons as $ico => $icoName):
                                ?>
                                    <button type="button" onclick="selectIcon('<?= $ico ?>')" class="px-2 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[10px] border border-slate-700 transition flex items-center gap-1">
                                        <i class="<?= $ico ?> text-amber-400"></i> <?= $icoName ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Mô tả danh mục -->
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Mô Tả Danh Mục
                            </label>
                            <textarea name="description" rows="3" placeholder="Nhập mô tả ngắn gọn về danh mục sản phẩm này..." class="w-full bg-slate-950/80 border border-slate-700 rounded-2xl p-4 text-xs text-white focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400 transition"><?= htmlspecialchars($_POST['description'] ?? $category['description'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Nút hành động -->
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-800">
                        <a href="<?= BASE_URL ?>index.php?url=admin/categories" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold text-xs border border-slate-700 transition">
                            Hủy Bỏ
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/20 transition flex items-center">
                            <i class="fa-solid fa-floppy-disk mr-1.5"></i> <?= $isEdit ? 'Lưu Thay Đổi' : 'Tạo Danh Mục Mới' ?>
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
        const iconInput = document.getElementById('categoryIcon');
        const iconPreview = document.getElementById('iconPreview');

        function selectIcon(iconClass) {
            iconInput.value = iconClass;
            iconPreview.className = iconClass + ' text-base';
        }

        iconInput.addEventListener('input', function() {
            iconPreview.className = (this.value.trim() || 'fa-solid fa-folder') + ' text-base';
        });
    </script>
</body>
</html>
