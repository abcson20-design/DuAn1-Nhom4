<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($product) ? 'Sửa' : 'Thêm' ?> Sản Phẩm - NEXVORA Admin</title>
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
                <span class="text-base font-black tracking-tight text-white">NEX<span class="text-cyan-400">VORA</span> Admin</span>
            </div>
            <a href="<?= BASE_URL ?>index.php?url=admin/products" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs px-3.5 py-1.5 rounded-xl border border-slate-700 transition flex items-center">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Quay lại danh sách
            </a>
        </div>
    </header>

    <?php
    $formType = $selectedType ?? ($_GET['type'] ?? 'phone');
    if (isset($product)) {
        $formType = ($product['category_id'] == 1 || (!empty($product['category_type']) && $product['category_type'] === 'phone')) ? 'phone' : 'accessory';
    } else {
        if ($formType !== 'accessory') {
            $formType = 'phone';
        }
    }

    // Tách riêng danh mục & hãng theo phân hệ Điện thoại vs Phụ kiện
    $phoneCategories = array_filter($categories ?? [], function($c) {
        return ($c['type'] ?? '') === 'phone' || $c['id'] == 1;
    });
    $accessoryCategories = array_filter($categories ?? [], function($c) {
        return ($c['type'] ?? '') === 'accessory' || $c['id'] > 1;
    });

    $phoneBrands = array_filter($brands ?? [], function($b) {
        return !empty($b['is_phone_brand']);
    });
    $accessoryBrands = array_filter($brands ?? [], function($b) {
        return empty($b['is_phone_brand']);
    });
    ?>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-1 relative z-10">
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-8 space-y-6">
            <div class="border-b border-slate-800 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 id="formTitleHeader" class="text-xl font-extrabold text-white flex items-center">
                        <?php if ($formType === 'phone'): ?>
                            <i class="fa-solid fa-mobile-screen text-cyan-400 mr-2.5 text-lg"></i>
                            <?= isset($product) ? 'Chỉnh Sửa Điện Thoại: ' . htmlspecialchars($product['name']) : 'Thêm Mới Điện Thoại Smartphone' ?>
                        <?php else: ?>
                            <i class="fa-solid fa-headphones text-purple-400 mr-2.5 text-lg"></i>
                            <?= isset($product) ? 'Chỉnh Sửa Phụ Kiện: ' . htmlspecialchars($product['name']) : 'Thêm Mới Phụ Kiện & Thiết Bị Công Nghệ' ?>
                        <?php endif; ?>
                    </h2>
                    <p id="formSubHeader" class="text-xs text-slate-400 mt-1">
                        <?php if ($formType === 'phone'): ?>
                            Form chuyên biệt độc lập cho Smartphone: Tối ưu thông số Vi xử lý, RAM, ROM, Camera OIS, Khung viền và Pin sạc nhanh.
                        <?php else: ?>
                            Form chuyên biệt độc lập cho Phụ kiện: Linh hoạt chuyển đổi mẫu theo từng loại như Tai nghe, Củ/Cáp sạc, Pin sạc, Ốp lưng, Đồng hồ.
                        <?php endif; ?>
                    </p>
                </div>
                <!-- Badge phân hệ cố định (Không thể chuyển qua lại) -->
                <div id="formTypeBadge">
                    <?php if ($formType === 'phone'): ?>
                        <span class="inline-flex items-center gap-1.5 bg-cyan-950 text-cyan-300 border border-cyan-800/80 text-xs px-3 py-1.5 rounded-xl font-bold self-start">
                            <i class="fa-solid fa-mobile-screen"></i> Phân Hệ Điện Thoại
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 bg-purple-950 text-purple-300 border border-purple-800/80 text-xs px-3 py-1.5 rounded-xl font-bold self-start">
                            <i class="fa-solid fa-headphones"></i> Phân Hệ Phụ Kiện
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <?php // [SỬA LỖI] Thông báo lỗi kiểm tra dữ liệu trước đây không được hiển thị ?>
            <?php if (!empty($_SESSION['flash_admin_err'])): ?>
                <div class="bg-rose-950/80 border border-rose-500/50 text-rose-300 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-semibold">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-exclamation text-rose-400 text-base"></i>
                        <span><?= htmlspecialchars(html_entity_decode((string)$_SESSION['flash_admin_err'], ENT_QUOTES, 'UTF-8')) ?></span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white transition p-1"><i class="fa-solid fa-xmark text-sm"></i></button>
                </div>
                <?php unset($_SESSION['flash_admin_err']); ?>
            <?php endif; ?>
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?= Csrf::field() ?>
                <input type="hidden" name="form_product_type" value="<?= htmlspecialchars($formType) ?>">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Tên sản phẩm <span class="text-red-400">*</span></label>
                        <input type="text" id="productNameInput" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" placeholder="<?= $formType === 'phone' ? 'Ví dụ: iPhone 16 Pro Max 256GB' : 'Ví dụ: Tai nghe Sony WH-1000XM5 ANC, Củ sạc Anker 65W GaN...' ?>" required class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">
                            <?= $formType === 'phone' ? 'Danh mục Điện Thoại' : 'Danh mục Phụ Kiện' ?> <span class="text-red-400">*</span>
                        </label>
                        <select name="category_id" id="categorySelect" required onchange="onCategoryChanged(this)" class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                            <?php if ($formType === 'phone'): ?>
                                <option value="">-- Chọn danh mục điện thoại --</option>
                                <?php foreach ($phoneCategories as $cat): 
                                    $isCatSelected = (isset($product) && $product['category_id'] == $cat['id']);
                                ?>
                                    <option value="<?= $cat['id'] ?>" <?= $isCatSelected ? 'selected' : '' ?>>
                                        📱 <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">-- Chọn loại phụ kiện --</option>
                                <?php foreach ($accessoryCategories as $cat): 
                                    $isCatSelected = (isset($product) && $product['category_id'] == $cat['id']);
                                ?>
                                    <option value="<?= $cat['id'] ?>" data-slug="<?= htmlspecialchars($cat['slug'] ?? '') ?>" data-name="<?= htmlspecialchars($cat['name']) ?>" <?= $isCatSelected ? 'selected' : '' ?>>
                                        🎧 <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">
                            <?= $formType === 'phone' ? 'Hãng Điện Thoại' : 'Hãng Phụ Kiện' ?>
                        </label>
                        <select name="brand_id" id="brandSelect" class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                            <?php if ($formType === 'phone'): ?>
                                <option value="">-- Chọn hãng điện thoại --</option>
                                <?php foreach ($phoneBrands as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= (isset($product) && $product['brand_id'] == $b['id']) ? 'selected' : '' ?>>
                                        📱 <?= htmlspecialchars($b['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">-- Chọn hãng phụ kiện --</option>
                                <?php foreach ($accessoryBrands as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= (isset($product) && $product['brand_id'] == $b['id']) ? 'selected' : '' ?>>
                                        🎧 <?= htmlspecialchars($b['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Giá bán (VNĐ) <span class="text-red-400">*</span></label>
                        <input type="number" name="price" value="<?= htmlspecialchars($product['price'] ?? '') ?>" placeholder="VD: 28990000" required class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Giá cũ / Giá niêm yết</label>
                        <input type="number" name="old_price" value="<?= htmlspecialchars($product['old_price'] ?? '') ?>" placeholder="VD: 34990000 (Để trống nếu không giảm)" class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                    </div>

                    <!-- ẢNH ĐẠI DIỆN SẢN PHẨM: TẢI TỪ MÁY HOẶC NHẬP URL -->
                    <div class="sm:col-span-2 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-300">
                                Ảnh đại diện sản phẩm <span class="text-red-400">*</span>
                            </label>
                            <span class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Tự động xóa nền ảnh
                            </span>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2.5 items-start sm:items-center">
                            <!-- Nút chọn file từ máy tính -->
                            <div class="flex-1 w-full">
                                <label class="flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl border border-dashed border-cyan-500/50 bg-cyan-950/20 hover:bg-cyan-950/50 text-cyan-300 text-xs font-bold cursor-pointer transition">
                                    <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                                    <span id="mainFileLabel">Tải ảnh lên từ máy tính</span>
                                    <input type="file" name="image_file" accept="image/*" class="hidden" onchange="previewMainFile(this)">
                                </label>
                            </div>
                            <!-- Ô nhập link URL (dự phòng hoặc ảnh online) -->
                            <div class="flex-1 w-full">
                                <input type="url" id="mainImageUrlInput" name="image" value="<?= htmlspecialchars($product['image'] ?? '') ?>" placeholder="Hoặc dán link URL: https://..." oninput="previewMainUrl(this.value)" class="w-full text-xs p-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                            </div>
                            <!-- Preview Thumbnail -->
                            <div class="w-11 h-11 rounded-xl border border-slate-700 bg-slate-950 flex items-center justify-center overflow-hidden shrink-0 shadow">
                                <img id="mainImagePreview" src="<?= !empty($product['image']) ? htmlspecialchars($product['image']) : '' ?>" alt="Preview" class="<?= empty($product['image']) ? 'hidden' : '' ?> w-full h-full object-contain">
                                <i id="mainImagePlaceholder" class="<?= !empty($product['image']) ? 'hidden' : '' ?> fa-solid fa-image text-slate-600 text-base"></i>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Số lượng kho</label>
                        <input type="number" name="quantity" value="<?= htmlspecialchars($product['quantity'] ?? '') ?>" placeholder="VD: 50" class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                    </div>

                    <!-- MỤC QUẢN LÝ TRÌNH CHIẾU TRANG CHI TIẾT (CHỌN 1 TRONG 2: SLIDESHOW HOẶC VIDEO) -->
                    <?php
                    $currentMediaType = $product['media_type'] ?? 'slideshow';
                    $currentVideo = $product['video'] ?? '';
                    $isYoutube = !empty($currentVideo) && (strpos($currentVideo, 'youtube.com') !== false || strpos($currentVideo, 'youtu.be') !== false);
                    ?>
                    <div class="sm:col-span-3 border border-cyan-500/30 bg-slate-950/80 rounded-2xl p-5 space-y-4 shadow-lg shadow-cyan-500/5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
                            <div>
                                <label class="text-sm font-extrabold text-cyan-400 flex items-center gap-2">
                                    <i class="fa-solid fa-photo-film text-base"></i> Trình Chiếu Trang Chi Tiết (Chọn 1 trong 2)
                                </label>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Chọn hiển thị <strong>Bộ Ảnh Slideshow cấu tạo</strong> hoặc <strong>Video trải nghiệm/giới thiệu</strong> ở khung ảnh bên trái trang chi tiết.
                                </p>
                            </div>

                            <!-- BỘ CHỌN 1 TRONG 2: SLIDESHOW HOẶC VIDEO -->
                            <div class="inline-flex p-1 bg-slate-900 border border-slate-700/80 rounded-xl gap-1 shrink-0">
                                <label class="cursor-pointer">
                                    <input type="radio" name="media_type" value="slideshow" <?= ($currentMediaType !== 'video') ? 'checked' : '' ?> onchange="switchMediaType('slideshow')" class="hidden peer">
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-slate-400 peer-checked:bg-gradient-to-r peer-checked:from-primary-600 peer-checked:to-cyan-500 peer-checked:text-white peer-checked:shadow transition">
                                        <i class="fa-solid fa-images text-[11px]"></i> 1. Bộ Ảnh Slideshow
                                    </span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="media_type" value="video" <?= ($currentMediaType === 'video') ? 'checked' : '' ?> onchange="switchMediaType('video')" class="hidden peer">
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-slate-400 peer-checked:bg-gradient-to-r peer-checked:from-rose-600 peer-checked:to-pink-500 peer-checked:text-white peer-checked:shadow transition">
                                        <i class="fa-solid fa-circle-play text-[11px]"></i> 2. Video Trải Nghiệm
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- KHỐI 1: BỘ ẢNH SLIDESHOW TỰ ĐỘNG -->
                        <div id="mediaSlideshowBlock" class="<?= ($currentMediaType === 'video') ? 'hidden' : '' ?> space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-2 bg-slate-900/60 p-3 rounded-xl border border-slate-800">
                                <span class="text-xs text-slate-300 font-semibold flex items-center gap-1.5">
                                    <i class="fa-solid fa-layer-group text-cyan-400"></i> Quản lý các góc ảnh và phần cấu tạo máy:
                                </span>
                                <span class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Toàn bộ ảnh tải lên tự động xóa nền
                                </span>
                                <div class="flex flex-wrap items-center gap-2">
                                    <!-- Nút Tải nhiều ảnh từ máy tính cùng lúc -->
                                    <label class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-emerald-600/20 cursor-pointer" title="Chọn cùng lúc nhiều file ảnh trên máy tính để tạo nhanh các slide">
                                        <i class="fa-solid fa-folder-open text-[11px]"></i> Tải nhiều ảnh từ máy
                                        <input type="file" multiple accept="image/*" class="hidden" onchange="handleMultipleSlideUpload(this)">
                                    </label>
                                    <button type="button" onclick="addSlideRow()" class="px-3 py-1.5 rounded-xl bg-cyan-600/30 hover:bg-cyan-600 text-cyan-300 hover:text-white text-xs font-bold border border-cyan-500/40 transition flex items-center gap-1.5 shadow-sm">
                                        <i class="fa-solid fa-plus text-[10px]"></i> Thêm slide
                                    </button>
                                </div>
                            </div>

                        <!-- Danh sách các dòng nhập slide -->
                        <?php
                        $existingGallery = [];
                        if (!empty($product['gallery'])) {
                            $existingGallery = json_decode($product['gallery'], true) ?: [];
                        }
                        ?>
                        <div id="slidesContainer" class="space-y-3">
                            <?php if (!empty($existingGallery)): ?>
                                <?php foreach ($existingGallery as $i => $item): ?>
                                    <div class="slide-row bg-slate-900/90 p-3.5 rounded-xl border border-slate-800 space-y-2.5 relative group">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-cyan-300 flex items-center gap-1.5 slide-num-label">
                                                <i class="fa-solid fa-image text-[11px]"></i> Slide #<?= $i + 1 ?>
                                            </span>
                                            <button type="button" onclick="removeSlideRow(this)" class="w-6 h-6 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 flex items-center justify-center transition" title="Xóa slide này">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center">
                                            <!-- Chọn file từ máy HOẶC link URL + Thumbnail Preview -->
                                            <div class="sm:col-span-6 flex items-center gap-2">
                                                <div class="w-10 h-10 rounded-lg border border-slate-700 bg-slate-950 flex items-center justify-center overflow-hidden shrink-0">
                                                    <img src="<?= htmlspecialchars($item['image'] ?? '') ?>" alt="Preview" class="w-full h-full object-contain slide-thumb-img <?= empty($item['image']) ? 'hidden' : '' ?>">
                                                    <i class="fa-solid fa-image text-slate-600 text-xs slide-thumb-placeholder <?= !empty($item['image']) ? 'hidden' : '' ?>"></i>
                                                </div>
                                                <label class="cursor-pointer px-2.5 py-2 rounded-xl border border-dashed border-cyan-500/40 bg-cyan-950/30 hover:bg-cyan-900/40 text-cyan-300 text-[11px] font-bold flex items-center gap-1 whitespace-nowrap shrink-0 transition" title="Chọn file ảnh từ máy tính">
                                                    <i class="fa-solid fa-upload"></i> <span class="file-name-label">Từ máy</span>
                                                    <input type="file" name="slide_files[]" accept="image/*" class="hidden" onchange="handleSlideFileSelect(this)">
                                                </label>
                                                <input type="url" name="slide_images[]" value="<?= htmlspecialchars($item['image'] ?? '') ?>" placeholder="Hoặc dán URL: https://..." oninput="handleSlideUrlInput(this)" class="w-full text-xs p-2 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                                            </div>
                                            <div class="sm:col-span-3">
                                                <input type="text" name="slide_titles[]" value="<?= htmlspecialchars($item['title'] ?? '') ?>" placeholder="Tiêu đề (VD: Cụm Camera)" class="w-full text-xs p-2 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                                            </div>
                                            <div class="sm:col-span-3">
                                                <input type="text" name="slide_tags[]" value="<?= htmlspecialchars($item['tag'] ?? 'Cấu tạo') ?>" placeholder="Nhãn Tag (VD: Camera)" class="w-full text-xs p-2 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                                            </div>
                                            <div class="sm:col-span-12">
                                                <input type="text" name="slide_descs[]" value="<?= htmlspecialchars($item['desc'] ?? '') ?>" placeholder="Mô tả công nghệ ngắn cho slide này..." class="w-full text-xs p-2 rounded-xl border border-slate-800 bg-slate-950/70 text-slate-300 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                            <div id="emptySlideNotice" class="<?= !empty($existingGallery) ? 'hidden' : '' ?> text-center py-6 border border-dashed border-slate-800 rounded-xl bg-slate-950/50">
                                <i class="fa-solid fa-images text-slate-600 text-2xl mb-1.5 block"></i>
                                <span class="text-xs text-slate-400">Chưa có ảnh slide riêng. Hệ thống sẽ tự động dùng ảnh cấu tạo mặc định.</span>
                                <div class="mt-2.5 flex justify-center gap-2">
                                    <label class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition cursor-pointer">
                                        <i class="fa-solid fa-cloud-arrow-up mr-1"></i> Tải nhiều ảnh từ máy tính
                                        <input type="file" multiple accept="image/*" class="hidden" onchange="handleMultipleSlideUpload(this)">
                                    </label>
                                    <button type="button" onclick="addSlideRow()" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-cyan-400 text-xs font-semibold border border-slate-700 transition">
                                        + Thêm 1 dòng trống
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- KHỐI 2: VIDEO TRẢI NGHIỆM SẢN PHẨM -->
                        <div id="mediaVideoBlock" class="<?= ($currentMediaType !== 'video') ? 'hidden' : '' ?> space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start bg-slate-900/60 p-4 rounded-2xl border border-slate-800">
                                <!-- Cột chọn file hoặc nhập URL -->
                                <div class="space-y-3.5">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                                            1. Tải Video từ máy tính <span class="text-slate-500 font-normal">(MP4, WebM, MOV)</span>
                                        </label>
                                        <label class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border border-dashed border-rose-500/50 bg-rose-950/20 hover:bg-rose-950/40 text-rose-300 text-xs font-bold cursor-pointer transition shadow-sm">
                                            <i class="fa-solid fa-cloud-arrow-up text-base"></i>
                                            <span id="videoFileLabel">Chọn file Video từ máy tính</span>
                                            <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime" class="hidden" onchange="previewVideoFile(this)">
                                        </label>
                                    </div>

                                    <div class="relative">
                                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                                            2. Hoặc dán link Video <span class="text-slate-500 font-normal">(YouTube hoặc link URL file .mp4)</span>
                                        </label>
                                        <div class="relative">
                                            <i class="fa-brands fa-youtube absolute left-3 top-1/2 -translate-y-1/2 text-rose-500 text-sm"></i>
                                            <input type="url" name="video_url" id="videoUrlInput" value="<?= htmlspecialchars($currentVideo) ?>" placeholder="https://www.youtube.com/watch?v=... hoặc link mp4" oninput="previewVideoUrl(this.value)" class="w-full text-xs pl-9 pr-3 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-rose-400">
                                        </div>
                                    </div>

                                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80 text-[11px] text-slate-400 space-y-1 leading-relaxed">
                                        <div class="text-rose-400 font-bold flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-info"></i> Hiển thị ở trang chi tiết:
                                        </div>
                                        <p>Video sẽ xuất hiện thay thế trực tiếp vào vị trí khung trình chiếu ở trang chi tiết, có trình điều khiển xem toàn màn hình và bật tắt âm thanh mượt mà.</p>
                                    </div>
                                </div>

                                <!-- Cột Khung xem trước Video Player -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-semibold text-slate-300 flex items-center justify-between">
                                        <span>Khung xem trước Video (Preview)</span>
                                        <span class="text-[10px] text-slate-500 font-normal">Tự động nhận diện MP4 & YouTube</span>
                                    </label>
                                    <div class="w-full aspect-video rounded-2xl border border-slate-700 bg-slate-950 flex items-center justify-center overflow-hidden relative shadow-lg">
                                        <!-- Video HTML5 -->
                                        <video id="adminVideoPreview" controls playsinline class="<?= (!empty($currentVideo) && !$isYoutube) ? '' : 'hidden' ?> w-full h-full object-contain" src="<?= (!empty($currentVideo) && !$isYoutube) ? htmlspecialchars($currentVideo) : '' ?>"></video>

                                        <!-- YouTube Iframe -->
                                        <?php
                                        $ytEmbed = '';
                                        if ($isYoutube) {
                                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/', $currentVideo, $m)) {
                                                $ytEmbed = 'https://www.youtube.com/embed/' . $m[1];
                                            }
                                        }
                                        ?>
                                        <iframe id="adminYoutubePreview" src="<?= $ytEmbed ?>" class="<?= $isYoutube ? '' : 'hidden' ?> w-full h-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <!-- Placeholder khi chưa có video -->
                                        <div id="videoEmptyPlaceholder" class="<?= !empty($currentVideo) ? 'hidden' : '' ?> flex flex-col items-center justify-center text-slate-600 space-y-1.5 p-4 text-center">
                                            <i class="fa-solid fa-film text-3xl text-slate-700"></i>
                                            <span class="text-xs text-slate-400">Chưa có video được chọn</span>
                                            <span class="text-[10px] text-slate-500">Tải file từ máy hoặc dán link YouTube bên cạnh để xem trước</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MỤC QUẢN LÝ MÀU SẮC SẢN PHẨM (1 HOẶC NHIỀU MÀU - CHỌN ẢNH HOẶC LINK URL) -->
                    <?php
                    $existingColors = [];
                    if (!empty($product['colors'])) {
                        $existingColors = json_decode($product['colors'], true) ?: [];
                    }
                    ?>
                    <div class="sm:col-span-3 border border-indigo-500/40 bg-slate-950/80 rounded-2xl p-5 space-y-4 shadow-lg shadow-indigo-500/5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
                            <div>
                                <label class="text-sm font-extrabold text-indigo-400 flex items-center gap-2">
                                    <i class="fa-solid fa-images text-base"></i> Tùy Chọn Màu Sắc & Ảnh Màu Sản Phẩm (1 hoặc nhiều màu)
                                </label>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Cài đặt từng màu sắc kèm hình ảnh (tải ảnh từ máy hoặc dán link). Ở trang chi tiết, ảnh màu sẽ nằm ở TRÊN dòng chữ tên màu.
                                </p>
                                <span class="inline-flex items-center gap-1 text-[10px] text-emerald-400 font-semibold mt-1">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Ảnh màu tải lên từ máy tự động tách nền trong suốt
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <label class="cursor-pointer px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold border border-slate-700 transition flex items-center gap-1.5 shadow-sm" title="Tải nhiều file ảnh màu cùng lúc từ máy">
                                    <i class="fa-solid fa-folder-open text-[10px] text-cyan-400"></i> Tải nhiều ảnh từ máy
                                    <input type="file" multiple accept="image/*" class="hidden" onchange="handleMultipleColorUpload(this)">
                                </label>
                                <button type="button" onclick="addColorRow()" class="px-3 py-1.5 rounded-xl bg-indigo-600/30 hover:bg-indigo-600 text-indigo-300 hover:text-white text-xs font-bold border border-indigo-500/40 transition flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-plus text-[10px]"></i> Thêm 1 màu
                                </button>
                            </div>
                        </div>

                        <!-- Danh sách các thẻ màu sắc (mỗi thẻ có Ảnh xem trước, Upload file hoặc Link URL, Tên màu) -->
                        <div id="colorsContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <?php if (!empty($existingColors)): ?>
                                <?php foreach ($existingColors as $cIdx => $colorItem): 
                                    $cImg = $colorItem['image'] ?? '';
                                    $cName = $colorItem['name'] ?? '';
                                ?>
                                    <div class="color-row bg-slate-900/90 p-3 rounded-2xl border border-slate-800 relative group transition hover:border-slate-700 space-y-2.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-bold text-slate-400 flex items-center gap-1.5 color-num-label">
                                                <i class="fa-solid fa-palette text-indigo-400 text-xs"></i> Màu #<?= $cIdx + 1 ?>
                                            </span>
                                            <button type="button" onclick="removeColorRow(this)" class="w-5 h-5 rounded-md bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 flex items-center justify-center transition" title="Xóa màu này">
                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                            </button>
                                        </div>

                                        <!-- Khung hiển thị ảnh xem trước ở trên -->
                                        <div class="w-full h-24 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center overflow-hidden relative group/thumb">
                                            <img src="<?= htmlspecialchars($cImg ?: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&auto=format&fit=crop&q=80') ?>" 
                                                 alt="<?= htmlspecialchars($cName) ?>" 
                                                 class="color-thumb-preview max-w-full max-h-full object-contain p-1 transition duration-200 <?= empty($cImg) ? 'hidden' : '' ?>">
                                            <div class="color-thumb-placeholder text-center text-slate-500 <?= !empty($cImg) ? 'hidden' : '' ?>">
                                                <i class="fa-solid fa-image text-xl mb-1 block"></i>
                                                <span class="text-[10px]">Chưa có ảnh</span>
                                            </div>
                                        </div>

                                        <!-- Nhập tên màu -->
                                        <div>
                                            <label class="text-[10px] font-semibold text-slate-400 block mb-1">Tên màu sắc</label>
                                            <input type="text" name="color_names[]" value="<?= htmlspecialchars($cName) ?>" placeholder="VD: Titan Sa Mạc" required class="w-full text-xs p-2 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-indigo-400">
                                        </div>

                                        <!-- Chọn ảnh: Tải từ máy HOẶC Dán link -->
                                        <div class="space-y-1.5 pt-1 border-t border-slate-800/80">
                                            <div class="flex items-center gap-1.5">
                                                <label class="cursor-pointer flex-1 py-1.5 px-2 rounded-lg bg-indigo-950/60 hover:bg-indigo-900 border border-indigo-500/30 text-indigo-300 hover:text-white text-[11px] font-semibold flex items-center justify-center gap-1 transition">
                                                    <i class="fa-solid fa-arrow-up-from-bracket text-[10px]"></i>
                                                    <span class="color-file-label truncate">Tải từ máy</span>
                                                    <input type="file" name="color_files[]" accept="image/*" class="hidden color-file-input" onchange="previewColorFile(this)">
                                                </label>
                                            </div>
                                            <input type="url" name="color_images[]" value="<?= htmlspecialchars($cImg) ?>" placeholder="Hoặc dán URL ảnh..." oninput="previewColorUrl(this)" class="color-url-input w-full text-[11px] p-1.5 rounded-lg border border-slate-700 bg-slate-950 text-slate-200 focus:outline-none focus:ring-1 focus:ring-indigo-400">
                                        </div>

                                        <!-- Số lượng kho của màu này -->
                                        <div class="pt-1.5 border-t border-slate-800/80">
                                            <div class="flex items-center justify-between mb-1">
                                                <label class="text-[10px] font-semibold text-slate-400 flex items-center gap-1">
                                                    <i class="fa-solid fa-boxes-stacked text-cyan-400"></i> Số lượng màu này
                                                </label>
                                                <span class="text-[9px] text-slate-500">0 = tự xóa ở web</span>
                                            </div>
                                            <div class="relative">
                                                <input type="number" 
                                                       name="color_quantities[]" 
                                                       min="0" 
                                                       value="<?= htmlspecialchars(isset($colorItem['quantity']) ? (int)$colorItem['quantity'] : '') ?>" 
                                                       placeholder="VD: 50" 
                                                       required 
                                                       class="w-full text-xs py-1.5 px-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-500 pointer-events-none">chiếc</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div id="emptyColorNotice" class="<?= !empty($existingColors) ? 'hidden' : '' ?> text-center py-6 border border-dashed border-slate-800 rounded-2xl bg-slate-950/40">
                            <i class="fa-solid fa-images text-slate-600 text-2xl mb-1.5 block"></i>
                            <span class="text-xs text-slate-400">Chưa có tùy chọn màu riêng. Hệ thống sẽ tự động dùng 4 màu chuẩn theo danh mục ở trang chi tiết.</span>
                            <div class="mt-3 flex flex-wrap justify-center gap-2">
                                <button type="button" onclick="addColorRow()" class="px-3.5 py-1.5 rounded-xl bg-indigo-900/40 hover:bg-indigo-800 text-indigo-300 text-xs font-semibold border border-indigo-700/50 transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-plus text-xs"></i> Thêm 1 màu mới
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Mô tả sản phẩm</label>
                        <textarea name="description" rows="4" class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>

                    <!-- MỤC QUẢN LÝ THÔNG SỐ KỸ THUẬT SẢN PHẨM (HIỂN THỊ TRANG CHI TIẾT) -->
                    <?php
                    $existingSpecs = [];
                    if (!empty($product['specs'])) {
                        $decodedSpecs = json_decode($product['specs'], true);
                        if (is_array($decodedSpecs)) {
                            $existingSpecs = $decodedSpecs;
                        } else {
                            $rawLines = explode("\n", str_replace(["\r\n", "\r"], "\n", $product['specs']));
                            foreach ($rawLines as $l) {
                                $l = trim($l);
                                if (empty($l)) continue;
                                if (strpos($l, "\t") !== false) {
                                    $p = explode("\t", $l, 2);
                                    $existingSpecs[trim($p[0])] = trim($p[1]);
                                } elseif (strpos($l, ':') !== false) {
                                    $p = explode(':', $l, 2);
                                    $existingSpecs[trim($p[0])] = trim($p[1]);
                                }
                            }
                        }
                    }
                    ?>
                    <?php
                    $specsTextLines = [];
                    foreach ($existingSpecs as $k => $v) {
                        $specsTextLines[] = $k . ': ' . $v;
                    }
                    $specsTextVal = implode("\n", $specsTextLines);
                    ?>
                    <div class="sm:col-span-3 border border-slate-800 bg-slate-950/80 rounded-2xl p-5 space-y-4 shadow-lg">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
                            <div>
                                <label class="text-sm font-extrabold <?= $formType === 'phone' ? 'text-cyan-400' : 'text-purple-400' ?> flex items-center gap-2">
                                    <i class="<?= $formType === 'phone' ? 'fa-solid fa-mobile-screen text-cyan-400' : 'fa-solid fa-headphones text-purple-400' ?> text-base"></i>
                                    Bảng Thông Số Kỹ Thuật <?= $formType === 'phone' ? 'Smartphone' : 'Phụ Kiện' ?> (Hiển thị ở trang chi tiết)
                                </label>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    <?= $formType === 'phone' ? 'Mặc định tải sẵn cấu hình chuẩn Smartphone (Màn hình, Vi xử lý, RAM, ROM, Camera, Pin sạc, Hệ điều hành).' : 'Có thể bấm các nút bên cạnh hoặc đổi Danh mục để chuyển nhanh mẫu theo từng loại phụ kiện.' ?>
                                </p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Chuyển đổi giữa 2 chế độ -->
                                <div class="bg-slate-900 p-1 rounded-xl border border-slate-800 flex items-center gap-1">
                                    <button type="button" id="tabBtnTable" onclick="switchSpecsMode('table')" class="px-3 py-1 rounded-lg text-xs font-bold transition bg-cyan-600 text-white shadow">
                                        <i class="fa-solid fa-table-list mr-1"></i> Bảng ô nhập
                                    </button>
                                    <button type="button" id="tabBtnText" onclick="switchSpecsMode('text')" class="px-3 py-1 rounded-lg text-xs font-bold transition text-slate-400 hover:text-white">
                                        <i class="fa-solid fa-file-lines mr-1"></i> Dán văn bản
                                    </button>
                                </div>
                                <?php if ($formType === 'phone'): ?>
                                    <button type="button" onclick="fillSampleSpecs('phone')" class="px-3 py-1.5 rounded-xl bg-cyan-900/50 hover:bg-cyan-800 text-cyan-300 hover:text-white text-xs font-bold border border-cyan-700/60 transition flex items-center gap-1.5 shadow-sm" title="Điền lại thông số kỹ thuật mẫu cho Smartphone">
                                        <i class="fa-solid fa-rotate-right text-[11px]"></i> Điền lại mẫu ĐT
                                    </button>
                                    <button type="button" id="addSpecRowBtn" onclick="addSpecRow()" class="px-3 py-1.5 rounded-xl bg-cyan-600/30 hover:bg-cyan-600 text-cyan-300 hover:text-white text-xs font-bold border border-cyan-500/40 transition flex items-center gap-1.5 shadow-sm">
                                        <i class="fa-solid fa-plus text-[10px]"></i> Thêm 1 dòng
                                    </button>
                                <?php else: ?>
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mr-1 hidden lg:inline">Đổi mẫu:</span>
                                        <button type="button" onclick="fillSampleSpecs('headphone')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-purple-900/60 text-purple-300 hover:text-white text-xs font-semibold border border-purple-700/40 transition flex items-center gap-1" title="Mẫu Tai nghe & Loa">
                                            <i class="fa-solid fa-headphones text-[10px]"></i> Tai nghe
                                        </button>
                                        <button type="button" onclick="fillSampleSpecs('charger')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-amber-900/60 text-amber-300 hover:text-white text-xs font-semibold border border-amber-700/40 transition flex items-center gap-1" title="Mẫu Củ sạc & Cáp sạc">
                                            <i class="fa-solid fa-bolt text-[10px]"></i> Củ/Cáp
                                        </button>
                                        <button type="button" onclick="fillSampleSpecs('powerbank')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-emerald-900/60 text-emerald-300 hover:text-white text-xs font-semibold border border-emerald-700/40 transition flex items-center gap-1" title="Mẫu Pin sạc dự phòng">
                                            <i class="fa-solid fa-battery-three-quarters text-[10px]"></i> Pin sạc
                                        </button>
                                        <button type="button" onclick="fillSampleSpecs('case')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-cyan-900/60 text-cyan-300 hover:text-white text-xs font-semibold border border-cyan-700/40 transition flex items-center gap-1" title="Mẫu Ốp lưng & Kính cường lực">
                                            <i class="fa-solid fa-shield-halved text-[10px]"></i> Ốp lưng
                                        </button>
                                        <button type="button" onclick="fillSampleSpecs('watch')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-rose-900/60 text-rose-300 hover:text-white text-xs font-semibold border border-rose-700/40 transition flex items-center gap-1" title="Mẫu Đồng hồ Smartwatch">
                                            <i class="fa-solid fa-clock text-[10px]"></i> Smartwatch
                                        </button>
                                        <button type="button" id="addSpecRowBtn" onclick="addSpecRow()" class="px-3 py-1 rounded-lg bg-cyan-600/30 hover:bg-cyan-600 text-cyan-300 hover:text-white text-xs font-bold border border-cyan-500/40 transition flex items-center gap-1">
                                            <i class="fa-solid fa-plus text-[10px]"></i> + Thêm dòng
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- CHẾ ĐỘ 1: BẢNG Ô NHẬP (TABLE VIEW) -->
                        <div id="specsTableView" class="space-y-3">
                            <div id="specsContainer" class="space-y-2.5">
                                <?php if (!empty($existingSpecs)): ?>
                                    <?php foreach ($existingSpecs as $sKey => $sVal): ?>
                                        <div class="spec-row flex items-center gap-2.5 bg-slate-900/90 p-2.5 rounded-xl border border-slate-800 hover:border-slate-700 transition">
                                            <div class="w-1/3 sm:w-1/4 shrink-0">
                                                <input type="text" name="spec_keys[]" value="<?= htmlspecialchars($sKey) ?>" placeholder="Tên thông số" class="spec-key-input w-full text-xs p-2 rounded-lg border border-slate-700 bg-slate-950 text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-400 font-semibold">
                                            </div>
                                            <div class="flex-1">
                                                <input type="text" name="spec_values[]" value="<?= htmlspecialchars($sVal) ?>" placeholder="Giá trị thông số" class="spec-val-input w-full text-xs p-2 rounded-lg border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                                            </div>
                                            <button type="button" onclick="removeSpecRow(this)" class="w-8 h-8 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 flex items-center justify-center shrink-0 transition" title="Xóa thông số này">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div id="emptySpecsNotice" class="<?= !empty($existingSpecs) ? 'hidden' : '' ?> text-center py-6 border border-dashed border-slate-800 rounded-2xl bg-slate-950/40 space-y-3">
                                <i class="fa-solid fa-microchip text-slate-600 text-2xl block"></i>
                                <div class="text-xs text-slate-400">Chưa có thông số kỹ thuật nào. Bấm <strong>"+ Thêm 1 dòng"</strong> để thêm hoặc chọn mẫu gợi ý bên trên khi cần.</div>
                                <div>
                                    <button type="button" onclick="addSpecRow()" class="px-4 py-2 rounded-xl bg-cyan-600/30 hover:bg-cyan-600 text-cyan-300 hover:text-white text-xs font-bold border border-cyan-500/40 transition inline-flex items-center gap-1.5 shadow-sm">
                                        <i class="fa-solid fa-plus text-xs"></i> Thêm 1 dòng thông số
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- CHẾ ĐỘ 2: DÁN VĂN BẢN TRỰC TIẾP (TEXTAREA VIEW) -->
                        <div id="specsTextView" class="hidden space-y-2">
                            <label class="block text-xs font-semibold text-slate-300">
                                Dán danh sách thông số (Mỗi dòng dạng: <code class="text-cyan-300 bg-slate-900 px-1 py-0.5 rounded">Tên thông số: Giá trị</code>)
                            </label>
                            <textarea id="specsDirectTextarea" 
                                      name="specs" 
                                      rows="9" 
                                      placeholder="Màn hình: 6.9 inch OLED 120Hz ProMotion&#10;Vi xử lý (CPU): Apple A20 Pro 2nm&#10;Dung lượng RAM: 12 GB&#10;Bộ nhớ trong: 256 GB / 512 GB / 1 TB&#10;Pin & Sạc: 4685 mAh, Sạc nhanh 45W&#10;Camera sau: 48MP + 48MP + 48MP" 
                                      class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 font-mono text-[11px] leading-relaxed focus:outline-none focus:ring-1 focus:ring-cyan-400"><?= htmlspecialchars($specsTextVal) ?></textarea>
                            <div class="flex items-center justify-between text-[11px] text-slate-500">
                                <span>Hỗ trợ định dạng dấu hai chấm (:), dấu gạch ngang ( - ), hoặc phím Tab.</span>
                                <button type="button" onclick="syncTextToTable()" class="text-cyan-400 hover:text-cyan-300 font-bold underline">
                                    Đồng bộ sang dạng Bảng ô nhập &rarr;
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-6 pt-2">
                    <label class="flex items-center text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_featured" <?= (!empty($product['is_featured'])) ? 'checked' : '' ?> class="mr-2 text-cyan-400 bg-slate-950 border-slate-700 rounded">
                        Sản phẩm nổi bật (Trang chủ)
                    </label>
                    <label class="flex items-center text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_hot" <?= (!empty($product['is_hot'])) ? 'checked' : '' ?> class="mr-2 text-cyan-400 bg-slate-950 border-slate-700 rounded">
                        Sản phẩm Hot / Flash Sale
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-800 flex justify-end space-x-3">
                    <a href="<?= BASE_URL ?>index.php?url=admin/products" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold px-6 py-3 rounded-xl transition">Hủy</a>
                    <button type="submit" class="bg-gradient-to-r from-primary-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white text-xs font-bold px-8 py-3 rounded-xl shadow-lg transition">
                        <i class="fa-solid fa-floppy-disk mr-1.5"></i> Lưu Sản Phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    /**
     * Thuật toán Canvas BFS Flood Fill: Tự động phát hiện và xóa phông nền (Studio/Trắng/Sáng viền)
     * Trả về file PNG mới và dataURL xem trước trong suốt mượt mà ngay trên trình duyệt
     */
    function autoRemoveBackground(file, onDone) {
        if (!file || !file.type.startsWith('image/') || file.type === 'image/svg+xml') {
            onDone(file, null);
            return;
        }

        const img = new Image();
        const objUrl = URL.createObjectURL(file);
        img.onload = function() {
            URL.revokeObjectURL(objUrl);
            try {
                let w = img.naturalWidth || img.width;
                let h = img.naturalHeight || img.height;
                if (!w || !h) {
                    onDone(file, null);
                    return;
                }

                // Giới hạn max 1200px để xử lý nhanh tức thì trên client
                const maxDim = 1200;
                if (w > maxDim || h > maxDim) {
                    const ratio = Math.min(maxDim / w, maxDim / h);
                    w = Math.round(w * ratio);
                    h = Math.round(h * ratio);
                }

                const canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                const ctx = canvas.getContext('2d', { willReadFrequently: true });
                ctx.drawImage(img, 0, 0, w, h);

                const imgData = ctx.getImageData(0, 0, w, h);
                const data = imgData.data;

                // Kiểm tra nếu góc (0,0) đã trong suốt sẵn
                if (data[3] < 50) {
                    canvas.toBlob(blob => {
                        const newFile = blob ? new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".png", { type: "image/png" }) : file;
                        onDone(newFile, canvas.toDataURL('image/png'));
                    }, 'image/png');
                    return;
                }

                // Lấy màu mẫu từ 4 góc
                const corners = [
                    0,
                    (w - 1) * 4,
                    (h - 1) * w * 4,
                    ((h - 1) * w + (w - 1)) * 4
                ];
                let avgR = 0, avgG = 0, avgB = 0;
                corners.forEach(idx => {
                    avgR += data[idx];
                    avgG += data[idx + 1];
                    avgB += data[idx + 2];
                });
                avgR /= 4; avgG /= 4; avgB /= 4;

                const visited = new Uint8Array(w * h);
                const queue = [];

                function enqueue(x, y) {
                    const p = y * w + x;
                    if (!visited[p]) {
                        visited[p] = 1;
                        queue.push(p);
                    }
                }

                for (let x = 0; x < w; x++) {
                    enqueue(x, 0);
                    enqueue(x, h - 1);
                }
                for (let y = 1; y < h - 1; y++) {
                    enqueue(0, y);
                    enqueue(w - 1, y);
                }

                const tolerance = 38;
                const smoothRange = 15;
                let head = 0;

                while (head < queue.length) {
                    const p = queue[head++];
                    const cx = p % w;
                    const cy = Math.floor(p / w);
                    const dIdx = p * 4;

                    const r = data[dIdx];
                    const g = data[dIdx + 1];
                    const b = data[dIdx + 2];

                    const dist = Math.hypot(r - avgR, g - avgG, b - avgB);

                    if (dist <= tolerance) {
                        data[dIdx + 3] = 0; // Xóa trong suốt
                        if (cx > 0) enqueue(cx - 1, cy);
                        if (cx < w - 1) enqueue(cx + 1, cy);
                        if (cy > 0) enqueue(cx, cy - 1);
                        if (cy < h - 1) enqueue(cx, cy + 1);
                    } else if (dist <= tolerance + smoothRange) {
                        // Khử răng cưa viền (anti-aliasing)
                        const alphaRatio = (dist - tolerance) / smoothRange;
                        data[dIdx + 3] = Math.round(alphaRatio * 255);
                    }
                }

                ctx.putImageData(imgData, 0, 0);
                canvas.toBlob(blob => {
                    if (blob) {
                        const cleanBase = file.name.replace(/\.[^/.]+$/, "");
                        const newFile = new File([blob], cleanBase + ".png", { type: "image/png" });
                        onDone(newFile, canvas.toDataURL('image/png'));
                    } else {
                        onDone(file, canvas.toDataURL('image/png'));
                    }
                }, 'image/png');
            } catch (err) {
                console.error('Lỗi tách nền Canvas:', err);
                onDone(file, null);
            }
        };
        img.onerror = function() {
            URL.revokeObjectURL(objUrl);
            onDone(file, null);
        };
        img.src = objUrl;
    }

    // Preview ảnh đại diện chính khi chọn file từ máy (Tự động xóa nền)
    function previewMainFile(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const label = document.getElementById('mainFileLabel');
            if (label) {
                label.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-cyan-400"></i> Đang xóa nền...';
            }

            autoRemoveBackground(file, function(processedFile, previewDataUrl) {
                if (window.DataTransfer && processedFile) {
                    try {
                        const dt = new DataTransfer();
                        dt.items.add(processedFile);
                        input.files = dt.files;
                    } catch(e) {}
                }

                const preview = document.getElementById('mainImagePreview');
                const placeholder = document.getElementById('mainImagePlaceholder');
                if (preview) {
                    preview.src = previewDataUrl || URL.createObjectURL(processedFile);
                    preview.classList.remove('hidden');
                }
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
                if (label) {
                    label.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> ' + 
                        (processedFile.name.length > 20 ? processedFile.name.substring(0, 17) + '...' : processedFile.name);
                }
            });
        }
    }

    // Preview ảnh đại diện chính khi nhập URL
    function previewMainUrl(url) {
        const preview = document.getElementById('mainImagePreview');
        const placeholder = document.getElementById('mainImagePlaceholder');
        const cleanUrl = (url || '').trim();
        if (cleanUrl) {
            if (preview) {
                preview.src = cleanUrl;
                preview.classList.remove('hidden');
            }
            if (placeholder) placeholder.classList.add('hidden');
        } else {
            if (preview) {
                preview.src = '';
                preview.classList.add('hidden');
            }
            if (placeholder) placeholder.classList.remove('hidden');
        }
    }

    // Cập nhật số thứ tự các slide và ẩn/hiện thông báo rỗng
    function updateSlideLabels() {
        const rows = document.querySelectorAll('#slidesContainer .slide-row');
        const emptyNotice = document.getElementById('emptySlideNotice');
        if (emptyNotice) {
            emptyNotice.style.display = rows.length === 0 ? 'block' : 'none';
        }
        rows.forEach((row, idx) => {
            const label = row.querySelector('.slide-num-label');
            if (label) {
                label.innerHTML = `<i class="fa-solid fa-image text-[11px]"></i> Slide #${idx + 1}`;
            }
        });
    }

    // Thêm một dòng slide mới vào container (Hỗ trợ file đã xóa nền & preview URL)
    function addSlideRow(imageUrl = '', title = '', tag = 'Cấu tạo', desc = '', fileObj = null, previewSrc = '') {
        const container = document.getElementById('slidesContainer');
        const emptyNotice = document.getElementById('emptySlideNotice');
        if (emptyNotice) emptyNotice.style.display = 'none';

        const rowCount = container.querySelectorAll('.slide-row').length + 1;
        const row = document.createElement('div');
        row.className = 'slide-row bg-slate-900/90 p-3.5 rounded-xl border border-slate-800 space-y-2.5 relative group transition';
        
        const displaySrc = previewSrc || imageUrl;
        const hasImg = !!displaySrc;
        row.innerHTML = `
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-cyan-300 flex items-center gap-1.5 slide-num-label">
                    <i class="fa-solid fa-image text-[11px]"></i> Slide #${rowCount}
                </span>
                <button type="button" onclick="removeSlideRow(this)" class="w-6 h-6 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 flex items-center justify-center transition" title="Xóa slide này">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center">
                <!-- Chọn file từ máy HOẶC link URL + Thumbnail Preview -->
                <div class="sm:col-span-6 flex items-center gap-2">
                    <div class="w-10 h-10 rounded-lg border border-slate-700 bg-slate-950 flex items-center justify-center overflow-hidden shrink-0">
                        <img src="${displaySrc}" alt="Preview" class="w-full h-full object-contain slide-thumb-img ${hasImg ? '' : 'hidden'}">
                        <i class="fa-solid fa-image text-slate-600 text-xs slide-thumb-placeholder ${hasImg ? 'hidden' : ''}"></i>
                    </div>
                    <label class="cursor-pointer px-2.5 py-2 rounded-xl border border-dashed border-cyan-500/40 bg-cyan-950/30 hover:bg-cyan-900/40 text-cyan-300 text-[11px] font-bold flex items-center gap-1 whitespace-nowrap shrink-0 transition" title="Chọn file ảnh từ máy tính (tự động xóa nền)">
                        <i class="fa-solid fa-upload"></i> <span class="file-name-label">${fileObj ? '<i class=\"fa-solid fa-check text-emerald-400\"></i> Đã xóa nền' : 'Từ máy'}</span>
                        <input type="file" name="slide_files[]" accept="image/*" class="hidden slide-file-input" onchange="handleSlideFileSelect(this)">
                    </label>
                    <input type="url" name="slide_images[]" value="${imageUrl.replace(/"/g, '&quot;')}" placeholder="Hoặc dán URL: https://..." oninput="handleSlideUrlInput(this)" class="w-full text-xs p-2 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                </div>
                <div class="sm:col-span-3">
                    <input type="text" name="slide_titles[]" value="${title.replace(/"/g, '&quot;')}" placeholder="Tiêu đề (VD: Màn hình 120Hz)" class="w-full text-xs p-2 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                </div>
                <div class="sm:col-span-3">
                    <input type="text" name="slide_tags[]" value="${tag.replace(/"/g, '&quot;')}" placeholder="Nhãn Tag (VD: Màn hình)" class="w-full text-xs p-2 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                </div>
                <div class="sm:col-span-12">
                    <input type="text" name="slide_descs[]" value="${desc.replace(/"/g, '&quot;')}" placeholder="Mô tả công nghệ ngắn cho slide này..." class="w-full text-xs p-2 rounded-xl border border-slate-800 bg-slate-950/70 text-slate-300 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                </div>
            </div>
        `;
        container.appendChild(row);

        // Nếu có truyền fileObj
        if (fileObj) {
            const fileInput = row.querySelector('.slide-file-input');
            const thumbImg = row.querySelector('.slide-thumb-img');
            const placeholder = row.querySelector('.slide-thumb-placeholder');

            try {
                const dt = new DataTransfer();
                dt.items.add(fileObj);
                fileInput.files = dt.files;
            } catch (err) {
                console.log('DataTransfer assign skipped:', err);
            }

            if (previewSrc && thumbImg) {
                thumbImg.src = previewSrc;
                thumbImg.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            } else {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (thumbImg) {
                        thumbImg.src = e.target.result;
                        thumbImg.classList.remove('hidden');
                    }
                    if (placeholder) {
                        placeholder.classList.add('hidden');
                    }
                };
                reader.readAsDataURL(fileObj);
            }
        }

        updateSlideLabels();
    }

    // Xử lý khi chọn file đơn lẻ trên 1 dòng slide (Tự động xóa nền)
    function handleSlideFileSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const row = input.closest('.slide-row');
            if (!row) return;

            const thumbImg = row.querySelector('.slide-thumb-img');
            const placeholder = row.querySelector('.slide-thumb-placeholder');
            const label = row.querySelector('.file-name-label');
            const titleInput = row.querySelector('input[name="slide_titles[]"]');

            if (label) {
                label.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Xóa nền...';
            }

            // Gợi ý tiêu đề từ tên file nếu tiêu đề đang trống
            if (titleInput && !titleInput.value.trim()) {
                const cleanName = file.name.replace(/\.[^/.]+$/, "").replace(/[-_]/g, ' ');
                titleInput.value = cleanName.charAt(0).toUpperCase() + cleanName.slice(1);
            }

            autoRemoveBackground(file, function(processedFile, previewDataUrl) {
                if (window.DataTransfer && processedFile) {
                    try {
                        const dt = new DataTransfer();
                        dt.items.add(processedFile);
                        input.files = dt.files;
                    } catch(e) {}
                }
                if (thumbImg) {
                    thumbImg.src = previewDataUrl || URL.createObjectURL(processedFile);
                    thumbImg.classList.remove('hidden');
                }
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
                if (label) {
                    label.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> Đã xóa nền';
                }
            });
        }
    }

    // Xử lý khi nhập link URL trên 1 dòng slide
    function handleSlideUrlInput(input) {
        const row = input.closest('.slide-row');
        if (!row) return;
        const thumbImg = row.querySelector('.slide-thumb-img');
        const placeholder = row.querySelector('.slide-thumb-placeholder');
        const url = (input.value || '').trim();

        if (url) {
            if (thumbImg) {
                thumbImg.src = url;
                thumbImg.classList.remove('hidden');
            }
            if (placeholder) placeholder.classList.add('hidden');
        } else {
            if (thumbImg) {
                thumbImg.src = '';
                thumbImg.classList.add('hidden');
            }
            if (placeholder) placeholder.classList.remove('hidden');
        }
    }

    // Xử lý khi chọn cùng lúc nhiều file ảnh từ máy tính (Tự động xóa nền từng ảnh)
    function handleMultipleSlideUpload(input) {
        if (!input.files || input.files.length === 0) return;

        const files = Array.from(input.files);
        files.forEach((file) => {
            const cleanName = file.name.replace(/\.[^/.]+$/, "").replace(/[-_]/g, ' ');
            const title = cleanName.charAt(0).toUpperCase() + cleanName.slice(1);

            autoRemoveBackground(file, function(processedFile, previewDataUrl) {
                addSlideRow('', title, 'Cấu tạo', '', processedFile, previewDataUrl);
            });
        });

        // Reset input để có thể chọn lại cùng file nếu muốn
        input.value = '';
    }

    function removeSlideRow(btn) {
        const row = btn.closest('.slide-row');
        if (row) {
            row.remove();
            updateSlideLabels();
        }
    }

    // Chuyển đổi giữa chế độ Slideshow và chế độ Video
    function switchMediaType(type) {
        const slideBlock = document.getElementById('mediaSlideshowBlock');
        const videoBlock = document.getElementById('mediaVideoBlock');
        if (type === 'video') {
            if (slideBlock) slideBlock.classList.add('hidden');
            if (videoBlock) videoBlock.classList.remove('hidden');
        } else {
            if (slideBlock) slideBlock.classList.remove('hidden');
            if (videoBlock) videoBlock.classList.add('hidden');
        }
    }

    // Xử lý xem trước file Video tải lên từ máy tính
    function previewVideoFile(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const label = document.getElementById('videoFileLabel');
            if (label) {
                label.textContent = file.name.length > 25 ? file.name.substring(0, 22) + '...' : file.name;
            }
            const videoEl = document.getElementById('adminVideoPreview');
            const ytEl = document.getElementById('adminYoutubePreview');
            const placeholder = document.getElementById('videoEmptyPlaceholder');

            if (ytEl) {
                ytEl.src = '';
                ytEl.classList.add('hidden');
            }
            if (placeholder) placeholder.classList.add('hidden');
            if (videoEl) {
                videoEl.src = URL.createObjectURL(file);
                videoEl.classList.remove('hidden');
                videoEl.load();
                videoEl.play().catch(e => {});
            }
        }
    }

    // Xử lý xem trước khi dán link URL Video (MP4 hoặc YouTube)
    function previewVideoUrl(url) {
        url = (url || '').trim();
        const videoEl = document.getElementById('adminVideoPreview');
        const ytEl = document.getElementById('adminYoutubePreview');
        const placeholder = document.getElementById('videoEmptyPlaceholder');

        if (!url) {
            if (videoEl) { videoEl.src = ''; videoEl.classList.add('hidden'); }
            if (ytEl) { ytEl.src = ''; ytEl.classList.add('hidden'); }
            if (placeholder) placeholder.classList.remove('hidden');
            return;
        }

        // Nhận diện link YouTube (watch, youtu.be, shorts, embed)
        const ytMatch = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/);
        if (ytMatch) {
            if (videoEl) { videoEl.src = ''; videoEl.classList.add('hidden'); }
            if (ytEl) {
                ytEl.src = 'https://www.youtube.com/embed/' + ytMatch[1] + '?autoplay=1&mute=1';
                ytEl.classList.remove('hidden');
            }
            if (placeholder) placeholder.classList.add('hidden');
        } else {
            if (ytEl) { ytEl.src = ''; ytEl.classList.add('hidden'); }
            if (videoEl) {
                videoEl.src = url;
                videoEl.classList.remove('hidden');
                videoEl.load();
                videoEl.play().catch(e => {});
            }
            if (placeholder) placeholder.classList.add('hidden');
        }
    }

    // ==========================================
    // QUẢN LÝ MÀU SẮC SẢN PHẨM (ẢNH & LINK URL)
    // ==========================================
    function updateColorLabels() {
        const rows = document.querySelectorAll('#colorsContainer .color-row');
        const emptyNotice = document.getElementById('emptyColorNotice');
        if (emptyNotice) {
            emptyNotice.style.display = rows.length === 0 ? 'block' : 'none';
        }
        rows.forEach((row, idx) => {
            const label = row.querySelector('.color-num-label');
            if (label) {
                label.innerHTML = `<i class="fa-solid fa-palette text-indigo-400 text-xs"></i> Màu #${idx + 1}`;
            }
        });
    }

    function previewColorFile(input) {
        const row = input.closest('.color-row');
        if (!row) return;
        const img = row.querySelector('.color-thumb-preview');
        const placeholder = row.querySelector('.color-thumb-placeholder');
        const fileLabel = row.querySelector('.color-file-label');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (fileLabel) {
                fileLabel.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Xóa nền...';
            }

            autoRemoveBackground(file, function(processedFile, previewDataUrl) {
                if (window.DataTransfer && processedFile) {
                    try {
                        const dt = new DataTransfer();
                        dt.items.add(processedFile);
                        input.files = dt.files;
                    } catch(e) {}
                }
                if (img) {
                    img.src = previewDataUrl || URL.createObjectURL(processedFile);
                    img.classList.remove('hidden');
                }
                if (placeholder) placeholder.classList.add('hidden');
                if (fileLabel) {
                    fileLabel.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> Đã xóa nền';
                }
            });
        }
    }

    function previewColorUrl(input) {
        const row = input.closest('.color-row');
        if (!row) return;
        const img = row.querySelector('.color-thumb-preview');
        const placeholder = row.querySelector('.color-thumb-placeholder');
        const url = input.value.trim();

        if (url) {
            if (img) {
                img.src = url;
                img.classList.remove('hidden');
            }
            if (placeholder) placeholder.classList.add('hidden');
        } else {
            const fileInput = row.querySelector('.color-file-input');
            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                if (img) img.classList.add('hidden');
                if (placeholder) placeholder.classList.remove('hidden');
            }
        }
    }

    function addColorRow(name = '', imgUrl = '', quantity = 50, fileObj = null, previewSrc = '') {
        const container = document.getElementById('colorsContainer');
        const emptyNotice = document.getElementById('emptyColorNotice');
        if (emptyNotice) emptyNotice.style.display = 'none';

        const count = container.querySelectorAll('.color-row').length + 1;
        const row = document.createElement('div');
        row.className = 'color-row bg-slate-900/90 p-3 rounded-2xl border border-slate-800 relative group transition hover:border-slate-700 space-y-2.5';
        
        const displaySrc = previewSrc || imgUrl;
        row.innerHTML = `
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 flex items-center gap-1.5 color-num-label">
                    <i class="fa-solid fa-palette text-indigo-400 text-xs"></i> Màu #${count}
                </span>
                <button type="button" onclick="removeColorRow(this)" class="w-5 h-5 rounded-md bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 flex items-center justify-center transition" title="Xóa màu này">
                    <i class="fa-solid fa-xmark text-[10px]"></i>
                </button>
            </div>

            <!-- Khung hiển thị ảnh xem trước ở trên -->
            <div class="w-full h-24 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center overflow-hidden relative group/thumb">
                <img src="${displaySrc}" 
                     alt="${name}" 
                     class="color-thumb-preview max-w-full max-h-full object-contain p-1 transition duration-200 ${displaySrc ? '' : 'hidden'}">
                <div class="color-thumb-placeholder text-center text-slate-500 ${displaySrc ? 'hidden' : ''}">
                    <i class="fa-solid fa-image text-xl mb-1 block"></i>
                    <span class="text-[10px]">Chưa có ảnh</span>
                </div>
            </div>

            <!-- Nhập tên màu -->
            <div>
                <label class="text-[10px] font-semibold text-slate-400 block mb-1">Tên màu sắc</label>
                <input type="text" name="color_names[]" value="${name.replace(/"/g, '&quot;')}" placeholder="VD: Titan Sa Mạc" required class="w-full text-xs p-2 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-indigo-400">
            </div>

            <!-- Chọn ảnh: Tải từ máy HOẶC Dán link -->
            <div class="space-y-1.5 pt-1 border-t border-slate-800/80">
                <div class="flex items-center gap-1.5">
                    <label class="cursor-pointer flex-1 py-1.5 px-2 rounded-lg bg-indigo-950/60 hover:bg-indigo-900 border border-indigo-500/30 text-indigo-300 hover:text-white text-[11px] font-semibold flex items-center justify-center gap-1 transition">
                        <i class="fa-solid fa-arrow-up-from-bracket text-[10px]"></i>
                        <span class="color-file-label truncate">${fileObj ? '<i class=\"fa-solid fa-check text-emerald-400\"></i> Đã xóa nền' : 'Tải từ máy'}</span>
                        <input type="file" name="color_files[]" accept="image/*" class="hidden color-file-input" onchange="previewColorFile(this)">
                    </label>
                </div>
                <input type="url" name="color_images[]" value="${imgUrl}" placeholder="Hoặc dán URL ảnh..." oninput="previewColorUrl(this)" class="color-url-input w-full text-[11px] p-1.5 rounded-lg border border-slate-700 bg-slate-950 text-slate-200 focus:outline-none focus:ring-1 focus:ring-indigo-400">
            </div>

            <!-- Số lượng kho của màu này -->
            <div class="pt-1.5 border-t border-slate-800/80">
                <div class="flex items-center justify-between mb-1">
                    <label class="text-[10px] font-semibold text-slate-400 flex items-center gap-1">
                        <i class="fa-solid fa-boxes-stacked text-cyan-400"></i> Số lượng màu này
                    </label>
                    <span class="text-[9px] text-slate-500">0 = tự xóa ở web</span>
                </div>
                <div class="relative">
                    <input type="number" 
                           name="color_quantities[]" 
                           min="0" 
                           value="${quantity}" 
                           placeholder="Số lượng kho" 
                           required 
                           class="w-full text-xs py-1.5 px-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-500 pointer-events-none">chiếc</span>
                </div>
            </div>
        `;
        container.appendChild(row);

        if (fileObj) {
            const fileInput = row.querySelector('.color-file-input');
            if (window.DataTransfer) {
                try {
                    const dt = new DataTransfer();
                    dt.items.add(fileObj);
                    fileInput.files = dt.files;
                } catch(e) {}
            }
        }

        updateColorLabels();
        return row;
    }

    function removeColorRow(btn) {
        const row = btn.closest('.color-row');
        if (row) {
            row.remove();
            updateColorLabels();
        }
    }

    // Tải nhiều ảnh màu từ máy cùng một lúc (Tự động xóa nền từng ảnh)
    function handleMultipleColorUpload(input) {
        if (!input.files || input.files.length === 0) return;
        const files = Array.from(input.files);
        files.forEach((file) => {
            let suggestedName = file.name.replace(/\.[^/.]+$/, '');
            suggestedName = suggestedName.replace(/[-_]/g, ' ');
            suggestedName = suggestedName.charAt(0).toUpperCase() + suggestedName.slice(1);

            autoRemoveBackground(file, function(processedFile, previewDataUrl) {
                addColorRow(suggestedName, '', 50, processedFile, previewDataUrl);
            });
        });
        input.value = '';
    }

    // ==========================================
    // QUẢN LÝ THÔNG SỐ KỸ THUẬT (JAVASCRIPT)
    // ==========================================
    function checkEmptySpecsNotice() {
        const rows = document.querySelectorAll('#specsContainer .spec-row');
        const emptyNotice = document.getElementById('emptySpecsNotice');
        if (emptyNotice) {
            emptyNotice.style.display = rows.length === 0 ? 'block' : 'none';
        }
    }

    function addSpecRow(key = '', val = '', placeholder = '') {
        const container = document.getElementById('specsContainer');
        const emptyNotice = document.getElementById('emptySpecsNotice');
        if (emptyNotice) emptyNotice.style.display = 'none';

        const row = document.createElement('div');
        row.className = 'spec-row flex items-center gap-2.5 bg-slate-900/90 p-2.5 rounded-xl border border-slate-800 hover:border-slate-700 transition';
        const displayVal = val ? val.replace(/"/g, '&quot;') : '';
        const displayPh = placeholder ? placeholder.replace(/"/g, '&quot;') : 'Nhập giá trị thông số...';
        row.innerHTML = `
            <div class="w-1/3 sm:w-1/4 shrink-0">
                <input type="text" name="spec_keys[]" value="${key.replace(/"/g, '&quot;')}" placeholder="Tên thông số" required class="spec-key-input w-full text-xs p-2 rounded-lg border border-slate-700 bg-slate-950 text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-400 font-semibold">
            </div>
            <div class="flex-1">
                <input type="text" name="spec_values[]" value="${displayVal}" placeholder="${displayPh}" class="spec-val-input w-full text-xs p-2 rounded-lg border border-slate-700 bg-slate-950 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-cyan-400">
            </div>
            <button type="button" onclick="removeSpecRow(this)" class="w-8 h-8 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 flex items-center justify-center shrink-0 transition" title="Xóa thông số này">
                <i class="fa-solid fa-trash-can text-xs"></i>
            </button>
        `;
        container.appendChild(row);
        checkEmptySpecsNotice();
        return row;
    }

    function removeSpecRow(btn) {
        const row = btn.closest('.spec-row');
        if (row) {
            row.remove();
            checkEmptySpecsNotice();
        }
    }

    function fillSampleSpecs(type) {
        const container = document.getElementById('specsContainer');
        if (!container) return;
        container.innerHTML = '';
        let samples = [];
        if (type === 'phone') {
            samples = [
                { k: 'Màn hình', v: '6.9 inch OLED Super Retina XDR, 120Hz ProMotion' },
                { k: 'Vi xử lý (CPU/SoC)', v: 'Apple A19 Pro (tiến trình 2nm) 6 nhân CPU' },
                { k: 'RAM', v: '12 GB LPDDR5X' },
                { k: 'Bộ nhớ trong', v: '256 GB / 512 GB / 1 TB' },
                { k: 'Camera sau', v: 'Chính 48MP OIS + Góc siêu rộng 48MP + Telephoto 48MP (Zoom 5x)' },
                { k: 'Camera trước', v: '18MP Center Stage' },
                { k: 'Pin & Sạc', v: '4685 mAh, Sạc nhanh có dây 45W, MagSafe 25W' },
                { k: 'Hệ điều hành', v: 'iOS mới nhất (Tích hợp Apple Intelligence)' },
                { k: 'Kháng nước / bụi', v: 'Chuẩn IP68' }
            ];
        } else if (type === 'charger') {
            samples = [
                { k: 'Công suất tối đa', v: '65W GaN sạc siêu nhanh' },
                { k: 'Số cổng kết nối', v: '2 cổng USB-C + 1 cổng USB-A' },
                { k: 'Chuẩn sạc hỗ trợ', v: 'Power Delivery 3.0 (PD), Quick Charge 4.0, PPS' },
                { k: 'Công nghệ an toàn', v: 'Kiểm soát nhiệt độ thông minh, chống quá dòng, chống đoản mạch' },
                { k: 'Chất liệu vỏ', v: 'Nhựa PC chống cháy cao cấp' },
                { k: 'Độ tương thích', v: 'iPhone, iPad, MacBook, Laptop Type-C, Samsung Galaxy' }
            ];
        } else if (type === 'powerbank') {
            samples = [
                { k: 'Dung lượng pin', v: '10.000 mAh (Lõi Li-Polymer an toàn)' },
                { k: 'Công suất sạc ra', v: '22.5W Max (Hỗ trợ sạc không dây MagSafe 15W)' },
                { k: 'Cổng kết nối', v: '1x USB-C In/Out 20W, 1x USB-A Out 22.5W' },
                { k: 'Tính năng nổi bật', v: 'Hít từ tính MagSafe tiện lợi, màn hình LED báo % pin' },
                { k: 'Trọng lượng', v: '210g, thiết kế siêu mỏng bỏ túi' },
                { k: 'Tương thích', v: 'iPhone 12 - 16 Series, Smartphone Android, Tai nghe' }
            ];
        } else if (type === 'case') {
            samples = [
                { k: 'Dòng máy tương thích', v: 'iPhone 16 Pro Max / 16 Pro' },
                { k: 'Chất liệu viền & lưng', v: 'Viền TPU đàn hồi chống sốc + Lưng PC cứng chống ố vàng' },
                { k: 'Tính năng MagSafe', v: 'Tích hợp vòng nam châm N52 lực hút siêu mạnh' },
                { k: 'Tiêu chuẩn bảo vệ', v: 'Đạt chuẩn quân đội Mỹ MIL-STD-810G rơi vỡ 2.5m' },
                { k: 'Gờ bảo vệ camera', v: 'Gờ viền nâng cao 1.5mm chống trầy xước' },
                { k: 'Trọng lượng', v: 'Khoảng 35g' }
            ];
        } else if (type === 'watch') {
            samples = [
                { k: 'Màn hình', v: '1.43 inch AMOLED sắc nét, độ sáng 1000 nits, Always-on Display' },
                { k: 'Thời lượng pin', v: 'Lên đến 12 ngày sử dụng thông thường' },
                { k: 'Cảm biến sức khỏe', v: 'Đo nhịp tim 24/7, SpO2, theo dõi giấc ngủ & Stress' },
                { k: 'Chế độ thể thao', v: '120+ môn thể thao, tích hợp GPS định vị chuẩn xác' },
                { k: 'Chuẩn chống nước', v: '5ATM (Độ sâu 50 mét)' },
                { k: 'Kết nối', v: 'Bluetooth 5.3, nghe gọi đàm thoại trực tiếp trên đồng hồ' }
            ];
        } else {
            // headphone / mặc định phụ kiện
            samples = [
                { k: 'Loại kết nối', v: 'Bluetooth 5.3 / Jack Type-C' },
                { k: 'Thời lượng pin', v: 'Lên đến 30 giờ (kèm hộp sạc), sạc nhanh 10 phút dùng 2 giờ' },
                { k: 'Công nghệ âm thanh', v: 'Chống ồn chủ động ANC & Spatial Audio' },
                { k: 'Chống nước & mồ hôi', v: 'Chuẩn IPX4 kháng nước bắn' },
                { k: 'Microphone đàm thoại', v: 'Mic kép lọc ồn đàm thoại AI rõ nét' },
                { k: 'Tương thích', v: 'iOS, Android, Windows, macOS' }
            ];
        }
        // Phần bên phải (giá trị) để là gợi ý (placeholder), không điền sẵn value
        samples.forEach(s => addSpecRow(s.k, '', s.v));

        const emptyNotice = document.getElementById('emptySpecsNotice');
        if (emptyNotice) emptyNotice.classList.add('hidden');
    }

    // Chuyển đổi giữa 2 chế độ Bảng và Dán văn bản
    let currentSpecsMode = 'table';
    function switchSpecsMode(mode) {
        currentSpecsMode = mode;
        const tableView = document.getElementById('specsTableView');
        const textView = document.getElementById('specsTextView');
        const tabBtnTable = document.getElementById('tabBtnTable');
        const tabBtnText = document.getElementById('tabBtnText');
        const addBtn = document.getElementById('addSpecRowBtn');

        if (mode === 'text') {
            // Đồng bộ từ Bảng sang Textarea trước khi hiển thị
            syncTableToText();
            if (tableView) tableView.classList.add('hidden');
            if (textView) textView.classList.remove('hidden');
            if (addBtn) addBtn.classList.add('hidden');
            if (tabBtnText) tabBtnText.className = 'px-3 py-1 rounded-lg text-xs font-bold transition bg-cyan-600 text-white shadow';
            if (tabBtnTable) tabBtnTable.className = 'px-3 py-1 rounded-lg text-xs font-bold transition text-slate-400 hover:text-white';
        } else {
            // Đồng bộ từ Textarea sang Bảng khi quay lại
            syncTextToTable();
            if (textView) textView.classList.add('hidden');
            if (tableView) tableView.classList.remove('hidden');
            if (addBtn) addBtn.classList.remove('hidden');
            if (tabBtnTable) tabBtnTable.className = 'px-3 py-1 rounded-lg text-xs font-bold transition bg-cyan-600 text-white shadow';
            if (tabBtnText) tabBtnText.className = 'px-3 py-1 rounded-lg text-xs font-bold transition text-slate-400 hover:text-white';
        }
    }

    // Đồng bộ từ các dòng của Bảng sang Textarea
    function syncTableToText() {
        const rows = document.querySelectorAll('#specsContainer .spec-row');
        const textarea = document.getElementById('specsDirectTextarea');
        if (!textarea) return;
        if (rows.length === 0) return;

        const lines = [];
        rows.forEach(r => {
            const k = r.querySelector('.spec-key-input')?.value.trim();
            const v = r.querySelector('.spec-val-input')?.value.trim();
            if (k && v) {
                lines.push(`${k}: ${v}`);
            }
        });
        if (lines.length > 0) {
            textarea.value = lines.join('\n');
        }
    }

    // Đồng bộ từ Textarea sang Bảng ô nhập
    function syncTextToTable() {
        const textarea = document.getElementById('specsDirectTextarea');
        if (!textarea) return;
        const text = textarea.value.trim();
        if (!text) return;

        const container = document.getElementById('specsContainer');
        const lines = text.split(/\r?\n/);
        let validLines = [];

        lines.forEach(line => {
            line = line.trim();
            if (!line) return;
            let key = '', val = '';
            if (line.includes('\t')) {
                const parts = line.split('\t');
                key = parts[0].trim();
                val = parts.slice(1).join('\t').trim();
            } else if (line.includes(':')) {
                const idx = line.indexOf(':');
                key = line.substring(0, idx).trim();
                val = line.substring(idx + 1).trim();
            } else if (line.includes(' - ')) {
                const idx = line.indexOf(' - ');
                key = line.substring(0, idx).trim();
                val = line.substring(idx + 3).trim();
            }
            if (key && val) {
                validLines.push({ k: key, v: val });
            }
        });

        if (validLines.length > 0) {
            container.innerHTML = '';
            validLines.forEach(item => addSpecRow(item.k, item.v));
            checkEmptySpecsNotice();
        }
    }

    // Xử lý khi Admin thay đổi Danh mục trong Form Phụ Kiện để chuyển mẫu thông số gợi ý phù hợp
    function onCategoryChanged(selectEl) {
        if (!selectEl) return;
        const currentFormType = <?= json_encode((string)$formType) ?>;
        if (currentFormType !== 'accessory') return;

        const opt = selectEl.options[selectEl.selectedIndex];
        if (!opt) return;
        const text = (opt.getAttribute('data-name') || opt.text || '').toLowerCase();
        const slug = (opt.getAttribute('data-slug') || '').toLowerCase();

        if (slug.includes('sac') || text.includes('sạc') || text.includes('cáp') || text.includes('củ')) {
            fillSampleSpecs('charger');
        } else if (slug.includes('pin') || text.includes('pin') || text.includes('dự phòng')) {
            fillSampleSpecs('powerbank');
        } else if (slug.includes('tai-nghe') || text.includes('tai nghe') || text.includes('loa') || text.includes('âm thanh')) {
            fillSampleSpecs('headphone');
        } else if (slug.includes('op') || text.includes('ốp') || text.includes('kính') || text.includes('cường lực')) {
            fillSampleSpecs('case');
        } else if (slug.includes('dong-ho') || text.includes('đồng hồ') || text.includes('watch')) {
            fillSampleSpecs('watch');
        }
    }

    // Khởi tạo form khi tải trang: Mặc định nạp mẫu thông số cho cả 2 form (phần bên phải là chữ mờ gợi ý)
    document.addEventListener('DOMContentLoaded', function() {
        const isNew = <?= !isset($product) ? 'true' : 'false' ?>;
        const currentFormType = <?= json_encode((string)$formType) ?>;

        if (isNew) {
            if (currentFormType === 'phone') {
                fillSampleSpecs('phone');
            } else {
                const catSelect = document.getElementById('categorySelect');
                if (catSelect && catSelect.value) {
                    onCategoryChanged(catSelect);
                } else {
                    fillSampleSpecs('headphone');
                }
            }
        }
    });
    </script>
</body>
</html>
