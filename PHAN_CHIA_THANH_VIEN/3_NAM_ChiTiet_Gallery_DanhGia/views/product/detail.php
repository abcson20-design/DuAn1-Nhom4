<?php
$isAdmin = !empty($_SESSION['admin_user']) || (!empty($currentUser) && ($currentUser['role'] ?? '') === 'admin');

// Chuẩn hóa ảnh chính: Nếu là OPPO Reno12 mà đang dính ảnh cũ smartwatch thì đổi thành ảnh smartphone chuẩn
$mainImg = $product['image'];
if (!empty($product['slug']) && $product['slug'] === 'oppo-reno12-5g-vu-tru-ai' && strpos($mainImg, '1546868871-7041f2a55e12') !== false) {
    $mainImg = 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=800&auto=format&fit=crop';
}

// Xây dựng danh sách các hình ảnh & bộ phận cấu tạo của sản phẩm
$isPhone = ($product['category_id'] == 1) || (isset($product['category_type']) && $product['category_type'] === 'phone') || (strpos(strtolower($product['category_name'] ?? ''), 'điện thoại') !== false);

$customGallery = !empty($product['gallery']) ? json_decode($product['gallery'], true) : [];
$slides = [];

if (!empty($customGallery) && is_array($customGallery)) {
    // Sử dụng bộ ảnh do Admin cấu hình trực tiếp từ form Thêm/Sửa sản phẩm
    $slides[] = [
        'tag' => 'Ngoại hình',
        'title' => 'Tổng thể thiết kế sản phẩm',
        'desc' => 'Thiết kế nguyên khối sang trọng, hoàn thiện tinh xảo từng đường nét.',
        'icon' => 'fa-solid fa-mobile-screen-button',
        'image' => $mainImg,
        'badge' => '1. Tổng thể'
    ];
    foreach ($customGallery as $idx => $gItem) {
        $slides[] = [
            'tag' => !empty($gItem['tag']) ? $gItem['tag'] : 'Cấu tạo',
            'title' => !empty($gItem['title']) ? $gItem['title'] : ('Phần cấu tạo #' . ($idx + 1)),
            'desc' => !empty($gItem['desc']) ? $gItem['desc'] : 'Linh kiện công nghệ cao cấp chính hãng.',
            'icon' => !empty($gItem['icon']) ? $gItem['icon'] : 'fa-solid fa-layer-group',
            'image' => $gItem['image'],
            'badge' => ($idx + 2) . '. ' . (!empty($gItem['title']) ? $gItem['title'] : ('Slide ' . ($idx + 1)))
        ];
    }
} elseif ($isPhone) {
    // 1. Tổng quan máy
    $slides[] = [
        'tag' => 'Ngoại hình',
        'title' => 'Tổng thể thiết kế & Mặt lưng',
        'desc' => 'Thiết kế nguyên khối siêu mỏng, mặt lưng dòng chảy ánh bạc phong cách tương lai thời thượng.',
        'icon' => 'fa-solid fa-mobile-screen-button',
        'image' => $mainImg,
        'badge' => '1. Tổng thể'
    ];
    // 2. Màn hình AMOLED 120Hz
    $slides[] = [
        'tag' => 'Màn hình',
        'title' => 'Màn hình AMOLED 120Hz cong vô cực 3D',
        'desc' => 'Tấm nền rực rỡ 1.07 tỷ màu, tần số quét 120Hz siêu mượt mà, độ sáng đỉnh cao hiển thị rõ ngoài trời.',
        'icon' => 'fa-solid fa-tv',
        'image' => 'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&auto=format&fit=crop',
        'badge' => '2. Màn hình 3D'
    ];
    // 3. Cụm Camera Ultra-Clear OIS
    $slides[] = [
        'tag' => 'Camera AI',
        'title' => 'Cụm Camera 50MP Sony LYT-600 OIS',
        'desc' => 'Ống kính thu sáng lớn, chống rung quang học OIS kết hợp thuật toán AI Eraser 2.0 xóa vật thể thông minh.',
        'icon' => 'fa-solid fa-camera',
        'image' => 'https://images.unsplash.com/photo-1617788138017-80ad40651399?w=800&auto=format&fit=crop',
        'badge' => '3. Camera AI'
    ];
    // 4. Vi xử lý & Bo mạch tản nhiệt
    $slides[] = [
        'tag' => 'Hiệu năng',
        'title' => 'Vi xử lý Chipset & Bo mạch tản nhiệt VC',
        'desc' => 'Chip tiến trình cao cấp siêu tiết kiệm điện năng, kết hợp hệ thống buồng hơi Vapor Chamber làm mát tức thì.',
        'icon' => 'fa-solid fa-microchip',
        'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop',
        'badge' => '4. Chip & Bo mạch'
    ];
    // 5. Khung viền hợp kim siêu bền
    $slides[] = [
        'tag' => 'Khung viền',
        'title' => 'Khung viền hợp kim chuẩn quân đội',
        'desc' => 'Gia công CNC nguyên khối chống cong vênh, hấp thụ 95% lực va đập khi rơi rớt bất ngờ.',
        'icon' => 'fa-solid fa-shield-halved',
        'image' => 'https://images.unsplash.com/photo-1605236453806-6ff36851218e?w=800&auto=format&fit=crop',
        'badge' => '5. Khung viền'
    ];
    // 6. Lõi Pin & Cổng sạc nhanh
    $slides[] = [
        'tag' => 'Pin & Sạc',
        'title' => 'Lõi Pin 5000mAh & Sạc siêu tốc SuperVOOC',
        'desc' => 'Chu kỳ pin bền bỉ 1600 lần sạc, tích hợp chip kiểm soát dòng điện an toàn tuyệt đối chống chai phù.',
        'icon' => 'fa-solid fa-bolt',
        'image' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop',
        'badge' => '6. Pin & Sạc'
    ];
} else {
    // Dành cho Phụ kiện & Thiết bị âm thanh / Sạc cáp
    $slides[] = [
        'tag' => 'Thiết kế',
        'title' => 'Tổng thể thiết kế & Ngoại quan',
        'desc' => 'Hoàn thiện tinh xảo từ vật liệu cao cấp, độ bền bỉ tiêu chuẩn và kích thước công thái học.',
        'icon' => 'fa-solid fa-cube',
        'image' => $mainImg,
        'badge' => '1. Tổng quan'
    ];
    $slides[] = [
        'tag' => 'Linh kiện',
        'title' => 'Cấu tạo lõi vi mạch & Buồng âm bán dẫn',
        'desc' => 'Trang bị linh kiện điện tử hiện đại, chip xử lý âm thanh DAC chất lượng cao hoặc công nghệ bán dẫn GaN.',
        'icon' => 'fa-solid fa-microchip',
        'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop',
        'badge' => '2. Bo mạch lõi'
    ];
    $slides[] = [
        'tag' => 'Chống va đập',
        'title' => 'Khung vỏ bảo vệ & Chống trầy xước',
        'desc' => 'Lớp vỏ chịu nhiệt và kháng lực cao cấp, bảo vệ linh kiện khỏi va chạm và rơi rớt trong sinh hoạt.',
        'icon' => 'fa-solid fa-shield-halved',
        'image' => 'https://images.unsplash.com/photo-1605236453806-6ff36851218e?w=800&auto=format&fit=crop',
        'badge' => '3. Vỏ chịu lực'
    ];
    $slides[] = [
        'tag' => 'Kết nối',
        'title' => 'Cổng kết nối & Tiếp xúc mạ kim loại cao cấp',
        'desc' => 'Cổng sạc và chân tiếp xúc gia công tỉ mỉ chống rỉ sét, truyền tải tín hiệu và điện năng tối ưu.',
        'icon' => 'fa-solid fa-bolt',
        'image' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop',
        'badge' => '4. Chân tiếp xúc'
    ];
}
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12">
    <!-- Breadcrumb -->
    <nav class="flex text-xs text-slate-400">
        <a href="<?= BASE_URL ?>" class="hover:text-cyan-400">Trang chủ</a>
        <span class="mx-2">/</span>
        <?php if ($product['category_id'] == 1): ?>
            <a href="<?= BASE_URL ?>index.php?url=phones" class="hover:text-cyan-400">Điện thoại</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>index.php?url=accessories" class="hover:text-cyan-400">Phụ kiện</a>
        <?php endif; ?>
        <span class="mx-2">/</span>
        <span class="text-slate-200 font-semibold truncate"><?= htmlspecialchars($product['name']) ?></span>
    </nav>

    <!-- Product Info Section (TechZone Glassmorphism) -->
    <div class="glass-card rounded-3xl p-6 sm:p-10 grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Gallery Images & Structure Slideshow / Video -->
        <div class="space-y-4" id="productGallerySection">
            <?php
            $hasVideo = !empty($product['video']);
            $mediaType = $product['media_type'] ?? 'slideshow';
            $isMediaVideo = $hasVideo && ($mediaType === 'video');
            $isYoutube = $hasVideo && (strpos($product['video'], 'youtube.com') !== false || strpos($product['video'], 'youtu.be') !== false);
            $ytEmbedUrl = '';
            if ($isYoutube) {
                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/', $product['video'], $m)) {
                    $ytEmbedUrl = 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $m[1];
                }
            }
            ?>

            <!-- BỘ CHUYỂN ĐỔI CHỌN 1 TRONG 2: SLIDESHOW CẤU TẠO HOẶC VIDEO TRẢI NGHIỆM -->
            <?php if ($hasVideo): ?>
                <div class="flex items-center justify-between gap-2 pb-1">
                    <div class="inline-flex p-1 bg-slate-900/90 border border-slate-700/80 rounded-2xl gap-1 shadow-md">
                        <button type="button" id="tabBtnSlideshow" onclick="switchDetailMedia('slideshow')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= !$isMediaVideo ? 'bg-gradient-to-r from-primary-600 to-cyan-500 text-white shadow-md' : 'text-slate-400 hover:text-white' ?>">
                            <i class="fa-solid fa-images text-[11px]"></i> Slideshow Cấu Tạo
                        </button>
                        <button type="button" id="tabBtnVideo" onclick="switchDetailMedia('video')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= $isMediaVideo ? 'bg-gradient-to-r from-rose-600 to-pink-500 text-white shadow-md' : 'text-slate-400 hover:text-white' ?>">
                            <i class="fa-solid fa-circle-play text-[11px]"></i> Video Trải Nghiệm
                        </button>
                    </div>
                    <span class="text-[11px] text-slate-400 hidden sm:inline-flex items-center gap-1 font-medium">
                        <i class="fa-solid fa-sparkles text-cyan-400"></i> Trải nghiệm đa chiều
                    </span>
                </div>
            <?php endif; ?>

            <!-- KHUNG 1: TRÌNH CHIẾU SLIDESHOW CÁC PHẦN CẤU TẠO -->
            <div id="detailSlideshowFrame" class="<?= $isMediaVideo ? 'hidden' : '' ?>">
                <div class="glass-card rounded-3xl overflow-hidden border border-slate-700/80 bg-slate-950/90 relative flex flex-col justify-between shadow-2xl group/slider">
                    
                    <!-- Thanh Trạng Thái Trên Cùng: Badge Cấu Tạo & Số Thứ Tự Slide & Nút Tạm Dừng/Tiếp Tục -->
                    <div class="absolute top-3 inset-x-3 z-20 flex items-center justify-between pointer-events-none">
                        <div class="flex items-center gap-2 pointer-events-auto">
                            <span id="slideTagBadge" class="px-3 py-1 rounded-full bg-cyan-950/80 border border-cyan-500/40 text-cyan-300 text-[11px] font-bold shadow-md backdrop-blur-md flex items-center gap-1.5">
                                <i id="slideIcon" class="<?= htmlspecialchars($slides[0]['icon']) ?> text-cyan-400"></i>
                                <span id="slideTagText"><?= htmlspecialchars($slides[0]['tag']) ?></span>
                            </span>
                            <span class="hidden sm:inline-flex px-2.5 py-1 rounded-full bg-slate-900/80 border border-slate-700 text-slate-300 text-[10px] font-medium backdrop-blur-md items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span id="autoPlayStatus">Tự động quay vòng</span>
                            </span>
                        </div>

                        <div class="flex items-center gap-1.5 pointer-events-auto">
                            <!-- Nút Tạm dừng / Tiếp tục Auto-slide -->
                            <button type="button" id="toggleAutoPlayBtn" class="w-8 h-8 rounded-full bg-slate-900/80 hover:bg-slate-800 text-slate-300 hover:text-cyan-400 border border-slate-700 flex items-center justify-center transition shadow-md" title="Tạm dừng / Tiếp tục tự động chuyển">
                                <i id="autoPlayIcon" class="fa-solid fa-pause text-[10px]"></i>
                            </button>
                            <!-- Số thứ tự slide -->
                            <span id="slideCounter" class="px-2.5 py-1 rounded-full bg-slate-900/80 border border-slate-700 text-white font-mono text-xs font-bold shadow-md">
                                1 / <?= count($slides) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Vùng Hiển Thị Ảnh Chính & Nút Tiến / Lùi -->
                    <div class="relative w-full h-[320px] sm:h-[400px] flex items-center justify-center p-6 overflow-hidden select-none">
                        <!-- Glow effect nền -->
                        <div class="absolute w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

                        <img id="main-image" 
                             src="<?= htmlspecialchars($slides[0]['image']) ?>" 
                             alt="<?= htmlspecialchars($slides[0]['title']) ?>" 
                             class="max-h-full max-w-full object-contain rounded-2xl transition-all duration-300 filter drop-shadow-[0_10px_25px_rgba(0,0,0,0.5)] transform scale-100">

                        <!-- Nút Lùi (Previous Slide) -->
                        <button type="button" id="prevSlideBtn" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-slate-900/85 hover:bg-primary-600 text-slate-200 hover:text-white border border-slate-700 hover:border-primary-400 flex items-center justify-center shadow-xl transition-all duration-200 transform hover:scale-110 active:scale-95 z-30" title="Phần cấu tạo trước (Lùi)">
                            <i class="fa-solid fa-chevron-left text-sm"></i>
                        </button>

                        <!-- Nút Tiến (Next Slide) -->
                        <button type="button" id="nextSlideBtn" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-slate-900/85 hover:bg-primary-600 text-slate-200 hover:text-white border border-slate-700 hover:border-primary-400 flex items-center justify-center shadow-xl transition-all duration-200 transform hover:scale-110 active:scale-95 z-30" title="Phần cấu tạo tiếp theo (Tiến)">
                            <i class="fa-solid fa-chevron-right text-sm"></i>
                        </button>
                    </div>

                    <!-- Thanh Chú Thích Cấu Tạo Ở Đáy Ảnh Chính & Chấm Chỉ Báo -->
                    <div class="border-t border-slate-800 bg-slate-900/90 backdrop-blur-md p-4 sm:px-6 z-20">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <h3 id="slideTitle" class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                                    <i id="slideTitleIcon" class="<?= htmlspecialchars($slides[0]['icon']) ?> text-cyan-400 mr-1.5 text-sm"></i>
                                    <span id="slideTitleText"><?= htmlspecialchars($slides[0]['title']) ?></span>
                                </h3>
                                <p id="slideDesc" class="text-xs text-slate-400 line-clamp-2">
                                    <?= htmlspecialchars($slides[0]['desc']) ?>
                                </p>
                            </div>
                            
                            <!-- Dots Indicators -->
                            <div class="flex items-center gap-1.5 self-center sm:self-auto shrink-0" id="dotsContainer">
                                <?php foreach ($slides as $idx => $s): ?>
                                    <button type="button" onclick="goToSlide(<?= $idx ?>)" class="dot-btn h-2 rounded-full transition-all duration-300 <?= $idx === 0 ? 'w-6 bg-cyan-400 shadow-sm shadow-cyan-400/50' : 'w-2 bg-slate-700 hover:bg-slate-500' ?>" title="<?= htmlspecialchars($s['title']) ?>"></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KHUNG 2: TRÌNH PHÁT VIDEO TRẢI NGHIỆM SẢN PHẨM -->
            <?php if ($hasVideo): ?>
                <div id="detailVideoFrame" class="<?= !$isMediaVideo ? 'hidden' : '' ?>">
                    <div class="glass-card rounded-3xl overflow-hidden border border-rose-500/40 bg-slate-950/95 relative shadow-2xl flex flex-col justify-between">
                        <!-- Top status badge -->
                        <div class="p-3 bg-slate-900/90 border-b border-slate-800 flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full bg-rose-950/80 border border-rose-500/40 text-rose-300 text-[11px] font-bold flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-circle-play text-rose-400"></i>
                                <span>Video Thực Tế / Trải Nghiệm Máy</span>
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono bg-slate-950 px-2 py-0.5 rounded border border-slate-800">
                                <?= $isYoutube ? 'YouTube HD' : 'Full HD 1080p' ?>
                            </span>
                        </div>

                        <!-- Vùng Video -->
                        <div class="relative w-full h-[320px] sm:h-[400px] flex items-center justify-center bg-black/90 overflow-hidden">
                            <?php if ($isYoutube): ?>
                                <iframe src="<?= $ytEmbedUrl ?>" class="w-full h-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            <?php else: ?>
                                <video id="detailHtml5Video" controls playsinline autoplay muted loop class="w-full h-full object-contain">
                                    <source src="<?= htmlspecialchars($product['video']) ?>">
                                    Trình duyệt của bạn không hỗ trợ thẻ video.
                                </video>
                            <?php endif; ?>
                        </div>

                        <!-- Chú thích chân Video -->
                        <div class="border-t border-slate-800 bg-slate-900/90 backdrop-blur-md p-4 sm:px-6">
                            <div class="space-y-0.5">
                                <h3 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                                    <i class="fa-solid fa-video text-rose-400 text-sm"></i>
                                    <span><?= htmlspecialchars($product['name']) ?></span>
                                </h3>
                                <p class="text-xs text-slate-400">
                                    Góc nhìn thực tế cận cảnh, thiết kế hoàn thiện và trải nghiệm tính năng chi tiết.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Audio Test Preview (Dành riêng cho Tai nghe) -->
            <?php if (strpos(strtolower($product['category_name'] ?? ''), 'tai nghe') !== false): ?>
                <div class="glass-card rounded-2xl p-5 border border-purple-500/30 bg-purple-950/20 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-white text-xs flex items-center">
                            <i class="fa-solid fa-waveform-lines text-cyan-400 mr-2 text-sm"></i> Trình Nghe Thử Âm Thanh Ảo
                        </h4>
                        <span class="text-[10px] font-bold text-purple-300 bg-purple-500/20 px-2 py-0.5 rounded-md border border-purple-500/30">Hi-Res Audio Demo</span>
                    </div>
                    <p class="text-[11px] text-slate-300">Bấm để mô phỏng dải trầm Bass, âm trung Vocal và tính năng khử ồn ANC:</p>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 hover:border-amber-400 text-amber-300 text-xs font-bold transition flex items-center" onclick="playAudioDemo('bass')">
                            <i class="fa-solid fa-play mr-1.5 text-[10px]"></i> Test Bass (Trầm)
                        </button>
                        <button type="button" class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 hover:border-cyan-400 text-cyan-300 text-xs font-bold transition flex items-center" onclick="playAudioDemo('vocal')">
                            <i class="fa-solid fa-play mr-1.5 text-[10px]"></i> Test Vocal
                        </button>
                        <button type="button" class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 hover:border-emerald-400 text-emerald-300 text-xs font-bold transition flex items-center" onclick="playAudioDemo('anc')">
                            <i class="fa-solid fa-shield-halved mr-1.5 text-[10px]"></i> Test Chống Ồn ANC
                        </button>
                    </div>
                    <div id="audioVisualizer" class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 text-[11px] text-slate-400 text-center">
                        <div id="demoStatus"><span class="text-slate-500">Sẵn sàng phát</span> • Chọn chế độ phía trên để nghe thử</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Details & Actions -->
        <div class="space-y-6">
            <div>
                <div class="flex items-center space-x-3 text-xs font-semibold mb-2">
                    <span class="text-cyan-400 uppercase bg-cyan-950/80 border border-cyan-800/60 px-2.5 py-1 rounded-md"><?= htmlspecialchars($product['category_name'] ?? '') ?></span>
                    <?php if (!empty($product['brand_name'])): ?>
                        <span class="text-slate-400">Hãng: <strong class="text-slate-200"><?= htmlspecialchars($product['brand_name']) ?></strong></span>
                    <?php endif; ?>
                    <span class="text-slate-500 font-mono">Tình trạng: <span class="text-emerald-400 font-bold">Còn hàng (<?= $product['quantity'] ?>)</span></span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white leading-snug"><?= htmlspecialchars($product['name']) ?></h1>
                
                <div class="flex items-center space-x-4 mt-3">
                    <div class="flex items-center text-amber-400 text-xs">
                        <?php 
                        $curRating = (float)($product['rating'] ?? 5.0);
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= floor($curRating)) echo '<i class="fa-solid fa-star"></i>';
                            elseif ($i - $curRating < 0.6) echo '<i class="fa-solid fa-star-half-stroke"></i>';
                            else echo '<i class="fa-regular fa-star text-slate-700"></i>';
                        }
                        ?>
                        <span class="ml-1.5 text-white font-bold text-xs"><?= number_format($curRating, 1) ?></span>
                        <a href="#reviews-section" class="text-slate-400 hover:text-cyan-400 text-xs ml-1 underline">(<?= count($reviews ?? []) ?> đánh giá)</a>
                    </div>
                    <span class="text-slate-700">|</span>
                    <span class="text-xs text-slate-400"><i class="fa-regular fa-eye mr-1 text-primary-400"></i><?= $product['views'] ?> lượt xem</span>
                    <span class="text-slate-700">|</span>
                    <span class="text-xs text-emerald-400"><i class="fa-solid fa-check-circle mr-1"></i>100% Chính hãng</span>
                </div>
            </div>

            <!-- Price Card -->
            <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800/80 flex items-baseline space-x-4">
                <span class="text-3xl font-black text-cyan-400"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                <?php if (!empty($product['old_price']) && $product['old_price'] > $product['price']): ?>
                    <span class="text-base text-slate-500 line-through"><?= number_format($product['old_price'], 0, ',', '.') ?>đ</span>
                    <?php $percent = round((($product['old_price'] - $product['price']) / $product['old_price']) * 100); ?>
                    <span class="bg-red-500/20 text-red-400 border border-red-500/40 text-xs font-black px-2 py-0.5 rounded-md">-<?= $percent ?>%</span>
                <?php endif; ?>
            </div>

            <!-- Add to Cart Form -->
            <?php
            // Lấy danh sách màu sắc của sản phẩm
            $productColors = [];
            $hasCustomColors = !empty($product['colors']);
            if ($hasCustomColors) {
                $rawColors = json_decode($product['colors'], true) ?: [];
                // TỰ ĐỘNG XÓA MÀU KHI HẾT HÀNG: Lọc bỏ các màu có số lượng (quantity) <= 0
                foreach ($rawColors as $c) {
                    $cQty = isset($c['quantity']) ? (int)$c['quantity'] : 50;
                    if ($cQty > 0) {
                        $c['quantity'] = $cQty;
                        $productColors[] = $c;
                    }
                }
            }
            // Nếu sản phẩm chưa cài màu riêng, tạo 4 màu mặc định kèm hình ảnh phù hợp
            if (!$hasCustomColors) {
                $mainFallback = !empty($product['image']) ? $product['image'] : 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&auto=format&fit=crop&q=80';
                if ($isPhone) {
                    $productColors = [
                        ['name' => 'Titan Tự Nhiên', 'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-naturaltitanium-select?wid=470&hei=556&fmt=png-alpha', 'quantity' => 50],
                        ['name' => 'Titan Đen', 'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-blacktitanium-select?wid=470&hei=556&fmt=png-alpha', 'quantity' => 50],
                        ['name' => 'Titan Trắng', 'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-whitetitanium-select?wid=470&hei=556&fmt=png-alpha', 'quantity' => 50],
                        ['name' => 'Titan Sa Mạc', 'image' => 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-deserttitanium-select?wid=470&hei=556&fmt=png-alpha', 'quantity' => 50]
                    ];
                } else {
                    $productColors = [
                        ['name' => 'Đen Huyền Bí', 'image' => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=500&auto=format&fit=crop&q=80', 'quantity' => 50],
                        ['name' => 'Bạc Ánh Kim', 'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=500&auto=format&fit=crop&q=80', 'quantity' => 50],
                        ['name' => 'Xanh Đại Dương', 'image' => 'https://images.unsplash.com/photo-1574944985070-8f3ebc6b79d2?w=500&auto=format&fit=crop&q=80', 'quantity' => 50],
                        ['name' => 'Vàng Hoàng Kim', 'image' => 'https://images.unsplash.com/photo-1565849904461-04a58ad377e0?w=500&auto=format&fit=crop&q=80', 'quantity' => 50]
                    ];
                }
            }
            $isAllColorsOutOfStock = $hasCustomColors && empty($productColors);
            $defaultColor = !empty($productColors[0]['name']) ? $productColors[0]['name'] : 'Mặc định';
            $defaultColorQty = !empty($productColors[0]['quantity']) ? (int)$productColors[0]['quantity'] : (int)($product['quantity'] ?? 50);
            ?>
            <form action="<?= BASE_URL ?>index.php?url=cart/add" method="POST" id="addToCartForm" class="space-y-4 ajax-add-to-cart">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                
                <!-- Số lượng -->
                <?php 
                    $isExpensiveProd = ((float)($product['price'] ?? 0) >= 15000000);
                    $maxQtyAllowed = $isExpensiveProd ? 2 : 10;
                ?>
                <div class="flex items-center space-x-4">
                    <span class="text-xs font-bold text-slate-400 uppercase">Số lượng:</span>
                    <div class="flex items-center border border-slate-700 rounded-xl bg-slate-950 overflow-hidden">
                        <button type="button" onclick="let input = document.getElementById('qtyInput'); input.value = Math.max(1, parseInt(input.value) - 1); document.getElementById('buyNowQty').value = input.value;" class="px-3 py-1.5 text-slate-400 hover:text-white transition">-</button>
                        <input type="number" id="qtyInput" name="quantity" value="1" min="1" max="<?= min($maxQtyAllowed, (int)$defaultColorQty) ?>" oninput="let v = Math.min(<?= $maxQtyAllowed ?>, Math.max(1, parseInt(this.value)||1)); this.value = v; document.getElementById('buyNowQty').value = v;" class="w-12 text-center text-xs bg-transparent text-white border-0 focus:outline-none">
                        <button type="button" onclick="let input = document.getElementById('qtyInput'); let max = Math.min(<?= $maxQtyAllowed ?>, parseInt(input.getAttribute('max') || '<?= $maxQtyAllowed ?>')); if (parseInt(input.value) >= max) { Swal.fire({toast:true, position:'top-end', icon:'warning', title:'<?= $isExpensiveProd ? "Sản phẩm cao cấp: Giới hạn tối đa 2 chiếc/đơn!" : "Tối đa 10 sản phẩm mỗi loại!" ?>', showConfirmButton:false, timer:2000}); } else { input.value = Math.min(max, parseInt(input.value) + 1); document.getElementById('buyNowQty').value = input.value; }" class="px-3 py-1.5 text-slate-400 hover:text-white transition">+</button>
                    </div>
                    <?php if ($isExpensiveProd): ?>
                        <span class="text-[11px] text-amber-400 font-bold flex items-center gap-1"><i class="fa-solid fa-shield-halved text-xs"></i> (Tối đa 2 chiếc/đơn - Chống đầu cơ)</span>
                    <?php else: ?>
                        <span class="text-[11px] text-slate-500 font-medium">(Tối đa 10 cái/sản phẩm)</span>
                    <?php endif; ?>
                </div>

                <!-- KHU VỰC CHỌN MÀU SẮC SẢN PHẨM: ẢNH Ở TRÊN, CHỮ Ở DƯỚI, TỰ ĐỘNG XÓA KHI HẾT MÀU -->
                <?php if ($isAllColorsOutOfStock): ?>
                    <div class="p-3.5 rounded-2xl bg-rose-950/50 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2.5">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400 text-base shrink-0"></i>
                        <span>Hiện tại <strong>tất cả các màu</strong> của sản phẩm này đã tạm hết hàng trong kho!</span>
                    </div>
                <?php else: ?>
                    <div class="space-y-2 pt-1 pb-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-palette text-cyan-400"></i> Màu sắc:
                                <span id="selectedColorLabel" class="text-cyan-300 font-extrabold normal-case text-xs"><?= htmlspecialchars($defaultColor) ?></span>
                                <span id="selectedColorStock" class="text-[10px] text-emerald-400 font-semibold normal-case">(Còn <?= $defaultColorQty ?> chiếc)</span>
                            </span>
                            <span class="text-[10px] text-slate-500 font-medium">Bấm ảnh để đổi màu</span>
                        </div>

                        <!-- DANH SÁCH MÀU CÒN HÀNG (grid-cols-4 hoặc tương ứng số màu còn lại) - ẢNH Ở TRÊN, CHỮ Ở DƯỚI -->
                        <div class="grid grid-cols-<?= max(1, min(4, count($productColors))) ?> gap-2" id="colorOptionsGrid">
                            <?php foreach ($productColors as $cIdx => $c): 
                                $cImg = !empty($c['image']) ? $c['image'] : (!empty($product['image']) ? $product['image'] : 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&auto=format&fit=crop&q=80');
                                $cQty = isset($c['quantity']) ? (int)$c['quantity'] : 50;
                            ?>
                                <button type="button" 
                                        onclick="selectProductColor(<?= $cIdx ?>, '<?= addslashes(htmlspecialchars($c['name'])) ?>', '<?= addslashes(htmlspecialchars($cImg)) ?>', <?= $cQty ?>)"
                                        class="color-option-btn group relative flex flex-col items-center justify-center p-2 rounded-2xl border transition-all duration-200 <?= $cIdx === 0 ? 'border-cyan-400 bg-cyan-950/40 text-cyan-300 ring-2 ring-cyan-400/50 shadow-lg shadow-cyan-500/10' : 'border-slate-800 bg-slate-950/60 hover:bg-slate-900 text-slate-300 hover:border-slate-700' ?>"
                                        data-color-name="<?= htmlspecialchars($c['name']) ?>"
                                        data-color-image="<?= htmlspecialchars($cImg) ?>"
                                        data-color-qty="<?= $cQty ?>"
                                        data-color-index="<?= $cIdx ?>">
                                    <!-- Huy hiệu tích chọn ở góc trên phải -->
                                    <span class="check-badge absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-cyan-400 text-slate-950 flex items-center justify-center shadow-md <?= $cIdx === 0 ? '' : 'hidden' ?>">
                                        <i class="fa-solid fa-check text-[9px] font-black"></i>
                                    </span>
                                    <!-- HÌNH ẢNH MÀU SẢN PHẨM NẰM Ở TRÊN -->
                                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-slate-900/90 border border-slate-800 p-1 flex items-center justify-center mb-1.5 overflow-hidden group-hover:scale-105 transition-transform duration-200 shadow-inner">
                                        <img src="<?= htmlspecialchars($cImg) ?>" alt="<?= htmlspecialchars($c['name']) ?>" class="max-w-full max-h-full object-contain filter drop-shadow">
                                    </div>
                                    <!-- DÒNG CHỮ TÊN MÀU NẰM Ở DƯỚI -->
                                    <span class="text-[10px] sm:text-[11px] font-bold text-center truncate w-full px-0.5 leading-tight"><?= htmlspecialchars($c['name']) ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="color" id="formSelectedColor" value="<?= htmlspecialchars($defaultColor) ?>">

                <?php if ($isAdmin): ?>
                    <div class="pt-3 space-y-2">
                        <div class="p-3 rounded-2xl bg-amber-950/40 border border-amber-500/40 text-amber-300 text-xs flex items-center">
                            <i class="fa-solid fa-user-shield text-amber-400 mr-2 text-base"></i>
                            <span>Tài khoản Quản trị viên (Admin) chỉ dùng để quản lý hệ thống, không thể đặt hàng.</span>
                        </div>
                        <button type="button" disabled class="w-full opacity-60 cursor-not-allowed bg-slate-800 text-slate-400 text-xs font-bold py-3.5 px-6 rounded-2xl border border-slate-700 flex items-center justify-center">
                            <i class="fa-solid fa-ban mr-2 text-sm"></i> Admin Không Thể Đặt Hàng
                        </button>
                    </div>
                <?php elseif ($isAllColorsOutOfStock || (int)$product['quantity'] <= 0): ?>
                    <div class="pt-3">
                        <button type="button" disabled class="w-full opacity-60 cursor-not-allowed bg-slate-800 text-rose-400 text-xs font-bold py-3.5 px-6 rounded-2xl border border-slate-700 flex items-center justify-center shadow-lg">
                            <i class="fa-solid fa-box-open mr-2 text-sm"></i> Tạm Hết Hàng (Vui lòng chọn sản phẩm khác)
                        </button>
                    </div>
                <?php else: ?>
                    <!-- ĐẨY LÙI 2 NÚT XUỐNG DƯỚI VỚI KHOẢNG CÁCH PT-3 -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-3 items-stretch">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-500 hover:to-indigo-500 text-white text-xs font-bold py-3.5 px-6 rounded-2xl shadow-xl shadow-primary-600/30 transition flex items-center justify-center">
                            <i class="fa-solid fa-cart-plus mr-2 text-sm"></i> Thêm Vào Giỏ Hàng
                        </button>
                        <button type="button" onclick="document.getElementById('buyNowForm').submit();" class="flex-1 bg-gradient-to-r from-amber-500 to-rose-600 hover:from-amber-400 hover:to-rose-500 text-white text-xs font-bold py-3.5 px-6 rounded-2xl shadow-xl shadow-rose-600/30 transition flex items-center justify-center">
                            <i class="fa-solid fa-bolt mr-2 text-sm"></i> Mua Ngay Bây Giờ
                        </button>
                        <?php $isDetailW = !empty($userWishlistIds) && in_array($product['id'], $userWishlistIds); ?>
                        <button type="button" 
                                onclick="toggleWishlist(<?= $product['id'] ?>, this, event)" 
                                id="btnWishlistDetail" 
                                data-product-id="<?= $product['id'] ?>"
                                data-wishlist-pid="<?= $product['id'] ?>"
                                class="<?= $isDetailW ? 'bg-rose-500 hover:bg-rose-600 text-white border border-rose-400 shadow-lg shadow-rose-500/25' : 'bg-slate-800 hover:bg-rose-500/20 text-rose-400 border border-slate-700 hover:border-rose-500/50' ?> px-4 py-3.5 rounded-2xl transition flex items-center justify-center shrink-0" 
                                title="<?= $isDetailW ? 'Bỏ thích sản phẩm này' : 'Thêm vào danh sách yêu thích' ?>">
                            <i class="fa-solid fa-heart text-base"></i>
                        </button>
                    </div>

                    <?php if (empty($_SESSION['user']) && empty($_SESSION['admin_user'])): ?>
                        <div class="pt-2">
                            <button type="button" 
                                    onclick="openGuestHoldModal(<?= (int)$product['id'] ?>, document.getElementById('formSelectedColor').value, document.getElementById('qtyInput').value)"
                                    class="w-full bg-slate-900/90 hover:bg-slate-800 text-cyan-300 hover:text-white border border-cyan-500/40 text-xs font-bold py-3 px-4 rounded-2xl shadow-lg transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-bolt text-cyan-400"></i> ⚡ Mua Nhanh Không Cần Đăng Nhập (CSKH Gọi Chốt Đơn)
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </form>

            <form id="buyNowForm" action="<?= BASE_URL ?>index.php?url=cart/buy-now" method="POST" class="hidden">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="quantity" id="buyNowQty" value="1">
                <input type="hidden" name="color" id="buyNowColor" value="<?= htmlspecialchars($defaultColor) ?>">
            </form>

            <!-- Tech Trust Checklist -->
            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-800 text-[11px] text-slate-400">
                <div class="flex items-center"><i class="fa-solid fa-box-open text-emerald-400 mr-2"></i> Mở hộp test thử trước</div>
                <div class="flex items-center"><i class="fa-solid fa-rotate-left text-primary-400 mr-2"></i> 1 đổi 1 trong 30 ngày</div>
                <div class="flex items-center"><i class="fa-solid fa-qrcode text-cyan-400 mr-2"></i> Thanh toán VietQR 24/7</div>
                <div class="flex items-center"><i class="fa-solid fa-shield-halved text-amber-400 mr-2"></i> Bảo hành điện tử qua SĐT</div>
            </div>
        </div>
    </div>

    <!-- Details Description & Specifications Grid (Cân đối 2 cột: Cột trái gọn gàng, Cột phải thông số mở rộng không bị gãy chữ) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Cột Bên Trái: Chi Tiết & Điểm Nổi Bật (col-span-5) -->
        <div class="lg:col-span-5 glass-card rounded-3xl p-6 sm:p-7 space-y-5">
            <h3 class="text-base sm:text-lg font-bold text-white border-b border-slate-800 pb-3 flex items-center">
                <i class="fa-solid fa-circle-info text-cyan-400 mr-2.5"></i> Chi Tiết & Điểm Nổi Bật
            </h3>
            <div class="text-xs text-slate-300 leading-relaxed space-y-3">
                <?= nl2br(htmlspecialchars($product['description'] ?? '')) ?>
            </div>

            <!-- Khối cam kết & hộp phụ kiện đi kèm để bổ sung thông tin cân đối khung -->
            <div class="pt-4 border-t border-slate-800/80 space-y-3">
                <h4 class="text-xs font-bold text-slate-200 flex items-center gap-1.5 uppercase tracking-wider">
                    <i class="fa-solid fa-box text-cyan-400"></i> Trong hộp sản phẩm gồm
                </h4>
                <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-400">
                    <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-900/60 border border-slate-800">
                        <i class="fa-solid fa-mobile text-cyan-400 shrink-0"></i> Thân máy mới 100%
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-900/60 border border-slate-800">
                        <i class="fa-solid fa-plug text-primary-400 shrink-0"></i> Cáp sạc C-to-C
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-900/60 border border-slate-800">
                        <i class="fa-solid fa-sim-card text-amber-400 shrink-0"></i> Cây lấy SIM
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-xl bg-slate-900/60 border border-slate-800">
                        <i class="fa-solid fa-book-open text-emerald-400 shrink-0"></i> Sách hướng dẫn
                    </div>
                </div>

                <div class="p-3 rounded-2xl bg-cyan-950/30 border border-cyan-500/20 text-[11px] text-cyan-300 flex items-start gap-2.5">
                    <i class="fa-solid fa-shield-check text-cyan-400 text-sm mt-0.5 shrink-0"></i>
                    <div>
                        <strong class="block font-bold">Bảo hành chính hãng 12 tháng</strong>
                        <span class="text-slate-400 text-[10px]">Đổi mới trong 30 ngày nếu phát sinh lỗi từ nhà sản xuất.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột Bên Phải: Thông Số Kỹ Thuật (col-span-7) -->
        <div class="lg:col-span-7 glass-card rounded-3xl p-6 sm:p-7 space-y-4">
            <h3 class="text-base sm:text-lg font-bold text-white border-b border-slate-800 pb-3 flex items-center justify-between">
                <span class="flex items-center">
                    <i class="fa-solid fa-microchip text-primary-400 mr-2.5"></i> Thông Số Kỹ Thuật
                </span>
                <?php if (!empty($specs)): ?>
                    <span class="text-[11px] font-medium text-slate-400 bg-slate-900 px-2.5 py-1 rounded-full border border-slate-800">
                        <?= count($specs) ?> thông số
                    </span>
                <?php endif; ?>
            </h3>
            <?php if (!empty($specs)): ?>
                <div class="space-y-1 text-xs divide-y divide-slate-800/60">
                    <?php foreach ($specs as $key => $val): ?>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2.5 px-3 rounded-xl hover:bg-slate-900/50 transition gap-1 sm:gap-4">
                            <span class="text-slate-400 font-semibold w-full sm:w-44 shrink-0 flex items-center gap-1.5 text-[11px] sm:text-xs">
                                <i class="fa-solid fa-circle-dot text-[8px] text-cyan-400/80"></i>
                                <?= htmlspecialchars($key) ?>
                            </span>
                            <span class="text-slate-200 text-left sm:text-right font-medium text-xs leading-relaxed flex-1">
                                <?= htmlspecialchars($val) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-xs text-slate-500 py-4 text-center">Thông số kỹ thuật đang được cập nhật từ nhà sản xuất.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MỤC BÌNH LUẬN & ĐÁNH GIÁ SẢN PHẨM (NEXVORA CUSTOMER REVIEWS) -->
    <!-- ============================================================== -->
    <div id="reviews-section" class="scroll-mt-24 space-y-8">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h3 class="text-xl sm:text-2xl font-extrabold text-white flex items-center">
                    <i class="fa-solid fa-comments text-cyan-400 mr-3 text-2xl"></i> Khách Hàng Đánh Giá & Bình Luận
                </h3>
                <p class="text-xs text-slate-400 mt-1">Trải nghiệm và đánh giá thực tế từ người dùng đã mua sắm tại NEXVORA Store</p>
            </div>
            <?php if ($isAdmin): ?>
                <span class="hidden sm:inline-flex items-center bg-amber-950/70 text-amber-300 border border-amber-500/40 text-xs font-bold py-2.5 px-4 rounded-xl">
                    <i class="fa-solid fa-user-shield mr-2"></i> Chế độ Quản trị viên
                </span>
            <?php elseif (empty($currentUser)): ?>
                <a href="<?= BASE_URL ?>index.php?url=auth/login&msg=login_required_review&redirect=<?= urlencode('index.php?url=products/detail&slug=' . $product['slug'] . '#reviews-section') ?>" class="hidden sm:inline-flex items-center bg-gradient-to-r from-primary-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white text-xs font-bold py-2.5 px-5 rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-arrow-right-to-bracket mr-2"></i> Đăng Nhập Để Đánh Giá
                </a>
            <?php else: ?>
                <a href="#reviewFormBox" class="hidden sm:inline-flex items-center bg-gradient-to-r from-cyan-500 to-primary-600 hover:from-cyan-400 hover:to-primary-500 text-white text-xs font-bold py-2.5 px-5 rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-pen-to-square mr-2"></i> Viết Đánh Giá
                </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($_SESSION['review_success'])): ?>
            <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 text-xs flex items-center justify-between animate-fade-in">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-check text-emerald-400 mr-2.5 text-base"></i>
                    <span><?= htmlspecialchars($_SESSION['review_success']) ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <?php unset($_SESSION['review_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['review_error'])): ?>
            <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs flex items-center justify-between animate-fade-in">
                <div class="flex items-center">
                    <i class="fa-solid fa-triangle-exclamation text-rose-400 mr-2.5 text-base"></i>
                    <span><?= htmlspecialchars($_SESSION['review_error']) ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <?php unset($_SESSION['review_error']); ?>
        <?php endif; ?>

        <!-- 1. Review Summary Dashboard -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 grid grid-cols-1 md:grid-cols-12 gap-8 items-center border border-slate-800">
            <!-- Left Score -->
            <div class="md:col-span-4 text-center md:text-left md:border-r border-slate-800/80 md:pr-8 space-y-2">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Điểm Đánh Giá Trung Bình</div>
                <div class="flex items-baseline justify-center md:justify-start space-x-2">
                    <span class="text-5xl font-black text-amber-400"><?= number_format($reviewStats['avg_rating'] ?? 5.0, 1) ?></span>
                    <span class="text-sm text-slate-400 font-bold">/ 5.0</span>
                </div>
                <div class="flex items-center justify-center md:justify-start space-x-1 text-amber-400 text-lg">
                    <?php
                    $avg = $reviewStats['avg_rating'] ?? 5.0;
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= floor($avg)) {
                            echo '<i class="fa-solid fa-star"></i>';
                        } elseif ($i - $avg < 0.6) {
                            echo '<i class="fa-solid fa-star-half-stroke"></i>';
                        } else {
                            echo '<i class="fa-regular fa-star text-slate-600"></i>';
                        }
                    }
                    ?>
                </div>
                <p class="text-xs text-slate-400 font-medium">
                    Dựa trên <strong class="text-white"><?= $reviewStats['total'] ?? count($reviews ?? []) ?></strong> lượt đánh giá thực tế
                </p>
            </div>

            <!-- Center Star Progress Bars -->
            <div class="md:col-span-5 space-y-2">
                <?php for ($s = 5; $s >= 1; $s--): 
                    $starData = $reviewStats['stars'][$s] ?? ['count' => 0, 'percent' => 0];
                ?>
                    <div class="flex items-center space-x-3 text-xs">
                        <span class="w-12 text-slate-400 font-semibold flex items-center justify-end">
                            <?= $s ?> <i class="fa-solid fa-star text-amber-400 text-[10px] ml-1"></i>
                        </span>
                        <div class="flex-1 h-2 rounded-full bg-slate-900 border border-slate-800 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-amber-400 to-cyan-400 rounded-full transition-all duration-500" style="width: <?= $starData['percent'] ?>%"></div>
                        </div>
                        <span class="w-10 text-[11px] text-slate-400 font-mono text-right"><?= $starData['count'] ?></span>
                    </div>
                <?php endfor; ?>
            </div>

            <!-- Right Callout Button -->
            <div class="md:col-span-3 text-center space-y-3 bg-slate-950/60 p-5 rounded-2xl border border-slate-800/80">
                <i class="fa-solid fa-award text-3xl text-cyan-400"></i>
                <div class="text-xs font-bold text-white">Bạn đã trải nghiệm sản phẩm này?</div>
                <p class="text-[11px] text-slate-400">Đóng góp ý kiến giúp cộng đồng người dùng công nghệ chọn lựa đúng đắn</p>
                <?php if ($isAdmin): ?>
                    <div class="text-[11px] text-amber-300 bg-amber-950/60 p-2.5 rounded-xl border border-amber-500/30">
                        <i class="fa-solid fa-info-circle mr-1"></i> Admin không thể đánh giá sản phẩm.
                    </div>
                <?php elseif (empty($currentUser)): ?>
                    <a href="<?= BASE_URL ?>index.php?url=auth/login&msg=login_required_review&redirect=<?= urlencode('index.php?url=products/detail&slug=' . $product['slug'] . '#reviews-section') ?>" class="inline-block w-full bg-slate-800 hover:bg-slate-700 text-cyan-400 hover:text-cyan-300 font-bold text-xs py-2.5 px-4 rounded-xl border border-cyan-500/30 transition">
                        <i class="fa-solid fa-arrow-right-to-bracket mr-1.5"></i> Đăng Nhập Để Đánh Giá
                    </a>
                <?php else: ?>
                    <a href="#reviewFormBox" class="inline-block w-full bg-slate-800 hover:bg-slate-700 text-cyan-400 hover:text-cyan-300 font-bold text-xs py-2.5 px-4 rounded-xl border border-cyan-500/30 transition">
                        <i class="fa-solid fa-pen-nib mr-1.5"></i> Gửi Bình Luận Ngay
                    </a>
                <?php endif; ?>
            </div>
        </div>

                <!-- 2. Form Gửi Bình Luận Mới / Thông Báo Quyền Quản Trị -->
        <?php if ($isAdmin): ?>
            <div id="reviewFormBox" class="glass-card rounded-3xl p-8 space-y-4 border border-amber-500/40 bg-amber-950/20 text-center">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/40 text-amber-400 mx-auto flex items-center justify-center text-2xl shadow-lg">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-extrabold text-amber-300 uppercase tracking-wider">Tài khoản Quản trị viên (Admin)</h4>
                    <p class="text-xs text-slate-300 max-w-lg mx-auto leading-relaxed">
                        Bạn đang đăng nhập với tư cách <strong class="text-white">Quản trị viên</strong>. Nhằm đảm bảo tính minh bạch và khách quan cho hệ thống đánh giá sản phẩm, tài khoản Admin không được phép tự viết đánh giá và không thể đặt hàng.
                    </p>
                </div>
                <div class="pt-2 flex items-center justify-center gap-3">
                    <a href="<?= BASE_URL ?>index.php?url=admin" class="inline-flex items-center text-xs font-bold text-white bg-slate-800 hover:bg-slate-700 px-5 py-2.5 rounded-xl border border-slate-700 transition">
                        <i class="fa-solid fa-gauge mr-1.5 text-cyan-400"></i> Vào Trang Quản Trị
                    </a>
                    <a href="<?= BASE_URL ?>index.php?url=auth/logout" class="inline-flex items-center text-xs font-bold text-rose-400 hover:text-white bg-rose-950/60 hover:bg-rose-900 px-5 py-2.5 rounded-xl border border-rose-500/30 transition">
                        <i class="fa-solid fa-right-from-bracket mr-1.5"></i> Đăng Xuất Admin
                    </a>
                </div>
            </div>
        <?php elseif (empty($currentUser)): ?>
            <!-- Hộp Thoại Bắt Buộc Đăng Nhập Mới Được Đánh Giá -->
            <div id="reviewFormBox" class="glass-card rounded-3xl p-8 sm:p-10 space-y-5 border border-cyan-500/30 bg-cyan-950/20 text-center">
                <div class="w-16 h-16 rounded-2xl bg-cyan-500/20 border border-cyan-500/40 text-cyan-400 mx-auto flex items-center justify-center text-3xl shadow-lg">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div class="space-y-1.5">
                    <h4 class="text-base sm:text-lg font-extrabold text-white">Đăng Nhập Để Viết Bình Luận & Đánh Giá</h4>
                    <p class="text-xs text-slate-300 max-w-md mx-auto leading-relaxed">
                        Bạn cần đăng nhập tài khoản Khách hàng tại NEXVORA để viết nhận xét, đánh giá số sao và giúp cộng đồng người dùng mua sắm tốt hơn.
                    </p>
                </div>
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="<?= BASE_URL ?>index.php?url=auth/login&msg=login_required_review&redirect=<?= urlencode('index.php?url=products/detail&slug=' . $product['slug'] . '#reviews-section') ?>" class="w-full sm:w-auto bg-gradient-to-r from-primary-600 via-indigo-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white font-extrabold text-xs py-3.5 px-8 rounded-xl shadow-xl shadow-primary-600/30 transition transform hover:-translate-y-0.5 flex items-center justify-center">
                        <i class="fa-solid fa-arrow-right-to-bracket mr-2"></i> Đăng Nhập Ngay
                    </a>
                    <a href="<?= BASE_URL ?>index.php?url=auth/register" class="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-slate-200 font-bold text-xs py-3.5 px-6 rounded-xl border border-slate-700 transition flex items-center justify-center">
                        <i class="fa-solid fa-user-plus mr-1.5"></i> Tạo Tài Khoản Mới
                    </a>
                </div>
            </div>
        <?php else: ?>

        <div id="reviewFormBox" class="glass-card rounded-3xl p-6 sm:p-8 space-y-6 border border-slate-800">
            <h4 class="text-sm font-bold text-white uppercase tracking-wider flex items-center border-b border-slate-800 pb-3">
                <i class="fa-solid fa-feather text-amber-400 mr-2 text-base"></i> Viết Bình Luận & Đánh Giá Của Bạn
            </h4>

            <form action="<?= BASE_URL ?>index.php?url=products/comment" method="POST" class="space-y-5">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="product_slug" value="<?= htmlspecialchars($product['slug']) ?>">

                <!-- Star Rating Picker -->
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-300">1. Mức độ hài lòng của bạn về sản phẩm này: <span class="text-rose-400">*</span></label>
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center space-x-1.5 text-2xl" id="starRatingContainer">
                            <i class="fa-solid fa-star star-btn cursor-pointer text-amber-400 transition hover:scale-110" data-value="1" onclick="setRating(1)"></i>
                            <i class="fa-solid fa-star star-btn cursor-pointer text-amber-400 transition hover:scale-110" data-value="2" onclick="setRating(2)"></i>
                            <i class="fa-solid fa-star star-btn cursor-pointer text-amber-400 transition hover:scale-110" data-value="3" onclick="setRating(3)"></i>
                            <i class="fa-solid fa-star star-btn cursor-pointer text-amber-400 transition hover:scale-110" data-value="4" onclick="setRating(4)"></i>
                            <i class="fa-solid fa-star star-btn cursor-pointer text-amber-400 transition hover:scale-110" data-value="5" onclick="setRating(5)"></i>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="5">
                        <span id="ratingText" class="text-xs font-bold text-amber-300 bg-amber-500/10 border border-amber-500/30 px-3 py-1 rounded-lg">
                            <i class="fa-solid fa-face-smile mr-1"></i> Rất Hài Lòng (5 sao)
                        </span>
                    </div>
                </div>

                <!-- Quick Tags Selector -->
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-300">2. Chọn nhanh cảm nhận (Bấm để chèn vào nội dung):</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="quick-tag px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 hover:border-cyan-400 text-slate-300 text-xs transition" onclick="appendTag('Hàng chính hãng 100%, nguyên seal chuẩn xịn.')">
                            ✨ Hàng chuẩn chính hãng
                        </button>
                        <button type="button" class="quick-tag px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 hover:border-cyan-400 text-slate-300 text-xs transition" onclick="appendTag('Giao hàng siêu nhanh, shipper thân thiện nhiệt tình.')">
                            ⚡ Giao hàng hỏa tốc
                        </button>
                        <button type="button" class="quick-tag px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 hover:border-cyan-400 text-slate-300 text-xs transition" onclick="appendTag('Đóng gói cẩn thận 2 lớp chống sốc dày dặn.')">
                            📦 Đóng gói cẩn thận
                        </button>
                        <button type="button" class="quick-tag px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 hover:border-cyan-400 text-slate-300 text-xs transition" onclick="appendTag('Chất âm cực đỉnh, nghe bass trầm sâu sống động.')">
                            🎧 Âm thanh đỉnh cao
                        </button>
                        <button type="button" class="quick-tag px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 hover:border-cyan-400 text-slate-300 text-xs transition" onclick="appendTag('Pin rất trâu dùng thoải mái cả ngày dài.')">
                            🔋 Pin trâu bền bỉ
                        </button>
                    </div>
                </div>

                <!-- Input Info & Content -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Họ và tên của bạn <span class="text-rose-400">*</span></label>
                        <input type="text" name="author_name" value="<?= htmlspecialchars($currentUser['fullname'] ?? '') ?>" placeholder="Ví dụ: Lê Văn Sơn" required class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Số điện thoại liên hệ (để xác nhận đã mua hàng)</label>
                        <input type="tel" name="author_phone" value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>" placeholder="0988.xxx.xxx" class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Nội dung đánh giá & cảm nhận chi tiết <span class="text-rose-400">*</span></label>
                        <textarea id="commentTextarea" name="content" rows="4" placeholder="Hãy chia sẻ trải nghiệm thực tế của bạn về chất lượng sản phẩm, hiệu năng, độ bền bỉ, tính năng vượt trội để giúp mọi người cùng tham khảo nhé..." required class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-400 leading-relaxed"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <p class="text-[11px] text-slate-500 italic">
                        <i class="fa-solid fa-shield-check text-emerald-400 mr-1"></i> Bình luận văn minh, tôn trọng người đọc và tuân thủ chính sách cộng đồng NEXVORA.
                    </p>
                    <button type="submit" class="bg-gradient-to-r from-primary-600 via-indigo-600 to-cyan-500 hover:from-primary-500 hover:to-cyan-400 text-white font-extrabold text-xs py-3 px-8 rounded-xl shadow-xl shadow-primary-600/30 transition transform hover:-translate-y-0.5 flex items-center">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Đăng Đánh Giá
                    </button>
                </div>
            </form>
        </div>

        
        <?php endif; ?>

        <!-- 3. Danh Sách Các Đánh Giá & Bình Luận -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-bold text-white uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-list-check text-cyan-400 mr-2 text-base"></i> Tất Cả Đánh Giá (<span id="totalReviewsCount"><?= count($reviews ?? []) ?></span>)
                </h4>
            </div>

            <?php if (empty($reviews)): ?>
                <div class="glass-card rounded-3xl p-10 text-center space-y-3 border border-slate-800">
                    <div class="w-14 h-14 rounded-full bg-slate-900 border border-slate-800 mx-auto flex items-center justify-center text-slate-500 text-2xl">
                        <i class="fa-solid fa-comment-dots"></i>
                    </div>
                    <div class="text-sm font-bold text-slate-200">Chưa có bình luận nào cho sản phẩm này</div>
                    <p class="text-xs text-slate-400">Hãy là người đầu tiên chia sẻ cảm nhận và đánh giá sản phẩm này bạn nhé!</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($reviews as $rev): 
                        $avatarLetter = mb_strtoupper(mb_substr($rev['author_name'] ?? 'U', 0, 1, 'UTF-8'), 'UTF-8');
                        $bgColor = 'from-primary-600 to-cyan-600';
                        if (($rev['rating'] ?? 5) == 5) $bgColor = 'from-cyan-600 to-indigo-600';
                        elseif (($rev['rating'] ?? 5) == 4) $bgColor = 'from-indigo-600 to-purple-600';
                    ?>
                        <div class="review-card-item glass-card rounded-2xl p-5 sm:p-6 space-y-3 border border-slate-800/80 hover:border-slate-700 transition">
                            <!-- Review Header -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br <?= $bgColor ?> text-white font-extrabold text-sm flex items-center justify-center shadow-md">
                                        <?= $avatarLetter ?>
                                    </div>
                                    <div>
                                        <div class="flex items-center flex-wrap gap-1.5">
                                            <span class="text-xs font-bold text-slate-200"><?= htmlspecialchars($rev['author_name'] ?? 'Khách hàng') ?></span>
                                            <?php if (!empty($rev['is_purchased'])): ?>
                                                <span class="inline-flex items-center text-[10px] text-emerald-400 bg-emerald-950/80 border border-emerald-500/30 px-2 py-0.5 rounded-full font-bold">
                                                    <i class="fa-solid fa-circle-check mr-1 text-[9px]"></i> Đã mua tại NEXVORA
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex items-center space-x-2 text-[11px] text-slate-500 mt-0.5">
                                            <div class="flex text-amber-400 text-xs">
                                                <?php for ($k = 1; $k <= 5; $k++): ?>
                                                    <i class="<?= ($k <= ($rev['rating'] ?? 5)) ? 'fa-solid fa-star' : 'fa-regular fa-star text-slate-700' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <span>•</span>
                                            <span><?= date('d/m/Y - H:i', strtotime($rev['created_at'])) ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <!-- Nút Admin Xóa Đánh Giá -->
                                    <?php if ($isAdmin): ?>
                                        <button type="button" 
                                                data-review-id="<?= (int)$rev['id'] ?>" 
                                                data-author-name="<?= htmlspecialchars($rev['author_name'] ?? 'Khách hàng', ENT_QUOTES) ?>" 
                                                onclick="handleDeleteReviewBtn(this)" 
                                                class="text-[11px] text-rose-400 hover:text-white bg-rose-950/50 hover:bg-rose-600 px-3 py-1.5 rounded-xl border border-rose-500/30 hover:border-rose-500 transition flex items-center space-x-1.5 shadow-sm cursor-pointer"
                                                title="Quản trị viên xóa vĩnh viễn đánh giá này">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            <span>Xóa</span>
                                        </button>
                                    <?php endif; ?>

                                    <!-- Like Helpful Button -->
                                    <button type="button" onclick="likeReview(<?= $rev['id'] ?>, this)" class="text-[11px] text-slate-400 hover:text-cyan-400 bg-slate-950 px-3 py-1.5 rounded-xl border border-slate-800 hover:border-cyan-500/40 transition flex items-center space-x-1.5">
                                        <i class="fa-regular fa-thumbs-up"></i>
                                        <span>Hữu ích (<span class="like-counter"><?= (int)($rev['likes'] ?? 0) ?></span>)</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Review Body -->
                            <p class="text-xs text-slate-300 leading-relaxed pl-13 pt-1">
                                <?= nl2br(htmlspecialchars($rev['content'] ?? '')) ?>
                            </p>

                            <!-- Official Store Admin Response (Phản hồi từ NEXVORA) -->
                            <div class="mt-2.5 ml-1 sm:ml-4 p-3 rounded-xl bg-slate-950/80 border-l-2 border-cyan-400 text-[11px] space-y-1">
                                <div class="font-bold text-cyan-400 flex items-center">
                                    <i class="fa-solid fa-headset mr-1.5"></i> NEXVORA Care - Chăm sóc khách hàng
                                </div>
                                <p class="text-slate-400">
                                    Cảm ơn bạn <strong class="text-slate-300"><?= htmlspecialchars($rev['author_name'] ?? '') ?></strong> đã tin tưởng mua sắm và dành thời gian chia sẻ đánh giá quý báu. Chúc bạn có trải nghiệm tuyệt vời cùng sản phẩm!
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Related Products Section (Sản phẩm cùng loại / Liên quan) -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="space-y-6 pt-6 border-t border-slate-800">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-extrabold text-white flex items-center">
                        <i class="fa-solid fa-boxes-stacked text-primary-400 mr-2.5"></i> Sản Phẩm Liên Quan
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Các sản phẩm cùng danh mục được khách hàng quan tâm nhiều</p>
                </div>
                <a href="<?= BASE_URL ?>index.php?url=products&category=<?= urlencode($product['category_slug'] ?? '') ?>" class="text-xs font-bold text-cyan-400 hover:text-cyan-300 flex items-center">
                    Xem tất cả <i class="fa-solid fa-arrow-right ml-1.5"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                <?php foreach ($relatedProducts as $rel): ?>
                    <div class="glass-card rounded-2xl p-4 flex flex-col justify-between group hover:border-cyan-500/50 transition duration-300 relative">
                        <?php $isRelW = !empty($userWishlistIds) && in_array($rel['id'], $userWishlistIds); ?>
                        <button type="button" 
                                onclick="toggleWishlist(<?= $rel['id'] ?>, this, event)" 
                                data-wishlist-pid="<?= $rel['id'] ?>"
                                class="btn-wishlist-toggle absolute top-3 right-3 z-20 w-7 h-7 rounded-full <?= $isRelW ? 'bg-rose-500/20 border-rose-500/50 text-rose-400' : 'bg-slate-900/80 hover:bg-rose-500/20 border-slate-700/80 hover:border-rose-500/40 text-slate-400 hover:text-rose-400' ?> border flex items-center justify-center transition shadow-md group/wishlist"
                                title="<?= $isRelW ? 'Bỏ thích sản phẩm này' : 'Thêm vào danh sách yêu thích' ?>">
                            <i class="fa-<?= $isRelW ? 'solid text-rose-500' : 'regular text-slate-300 group-hover/wishlist:text-white' ?> fa-heart text-[10px]"></i>
                        </button>
                        <a href="<?= BASE_URL ?>index.php?url=products/detail&slug=<?= urlencode($rel['slug']) ?>" class="space-y-3 block">
                            <div class="bg-slate-950/80 rounded-xl p-4 flex items-center justify-center aspect-square border border-slate-800/80 overflow-hidden">
                                <img src="<?= htmlspecialchars($rel['image']) ?>" alt="<?= htmlspecialchars($rel['name']) ?>" class="max-h-full object-contain group-hover:scale-105 transition duration-300">
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white line-clamp-2 group-hover:text-cyan-400 transition"><?= htmlspecialchars($rel['name']) ?></h4>
                                <div class="flex items-baseline space-x-2 mt-2">
                                    <span class="text-sm font-extrabold text-cyan-400"><?= number_format($rel['price'], 0, ',', '.') ?>đ</span>
                                    <?php if (!empty($rel['old_price']) && $rel['old_price'] > $rel['price']): ?>
                                        <span class="text-[10px] text-slate-500 line-through"><?= number_format($rel['old_price'], 0, ',', '.') ?>đ</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                        <div class="pt-3 border-t border-slate-800/80 mt-3 flex items-center justify-between">
                            <span class="text-[10px] text-slate-500 flex items-center">
                                <i class="fa-solid fa-star text-amber-400 mr-1 text-[9px]"></i> <?= number_format($rel['rating'] ?? 5.0, 1) ?>
                            </span>
                            <a href="<?= BASE_URL ?>index.php?url=products/detail&slug=<?= urlencode($rel['slug']) ?>" class="text-[11px] font-bold text-cyan-400 hover:text-white transition">
                                Chi tiết <i class="fa-solid fa-chevron-right text-[9px] ml-0.5"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function setRating(val) {
    const rInput = document.getElementById('ratingInput');
    if (rInput) rInput.value = val;
    const stars = document.querySelectorAll('#starRatingContainer .star-btn');
    const texts = {
        1: '<i class="fa-solid fa-face-frown mr-1"></i> Chưa Hài Lòng (1 sao)',
        2: '<i class="fa-solid fa-face-meh mr-1"></i> Tạm Được (2 sao)',
        3: '<i class="fa-solid fa-face-smile mr-1"></i> Bình Thường (3 sao)',
        4: '<i class="fa-solid fa-face-smile-beam mr-1"></i> Hài Lòng (4 sao)',
        5: '<i class="fa-solid fa-face-grin-stars mr-1"></i> Rất Hài Lòng (5 sao)'
    };
    stars.forEach(star => {
        const starVal = parseInt(star.getAttribute('data-value'));
        if (starVal <= val) {
            star.className = 'fa-solid fa-star star-btn cursor-pointer text-amber-400 transition hover:scale-110';
        } else {
            star.className = 'fa-regular fa-star star-btn cursor-pointer text-slate-600 transition hover:scale-110';
        }
    });
    const rText = document.getElementById('ratingText');
    if (rText) rText.innerHTML = texts[val] || '';
}

function appendTag(text) {
    const area = document.getElementById('commentTextarea');
    if (!area) return;
    if (area.value.trim() === '') {
        area.value = text;
    } else {
        area.value = area.value.trim() + ' ' + text;
    }
    area.focus();
}

function likeReview(reviewId, btn) {
    const counter = btn.querySelector('.like-counter');
    const fd = new FormData();
    fd.append('review_id', reviewId);

    fetch('<?= BASE_URL ?>index.php?url=products/like-comment', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (counter) counter.innerText = parseInt(counter.innerText || '0') + 1;
            btn.classList.add('text-cyan-400', 'border-cyan-400');
            btn.disabled = true;
        }
    })
    .catch(() => {});
}

// Quản trị viên xóa bình luận / đánh giá
function handleDeleteReviewBtn(btn) {
    const reviewId = parseInt(btn.getAttribute('data-review-id') || '0');
    const authorName = btn.getAttribute('data-author-name') || 'Khách hàng';
    if (reviewId > 0) {
        deleteReview(reviewId, authorName, btn);
    }
}

function deleteReview(reviewId, authorName, btn) {
    if (!confirm('Bạn có chắc chắn muốn xóa vĩnh viễn đánh giá của "' + authorName + '" không? Hành động này không thể hoàn tác.')) {
        return;
    }

    btn.disabled = true;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-[10px]"></i> <span>Đang xóa...</span>';

    const fd = new FormData();
    fd.append('review_id', reviewId);
    fd.append('ajax', '1');

    fetch('<?= BASE_URL ?>index.php?url=products/delete-comment', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const card = btn.closest('.review-card-item');
            if (card) {
                card.style.transition = 'all 0.35s ease';
                card.style.opacity = '0';
                card.style.transform = 'translateY(-10px) scale(0.96)';
                setTimeout(() => {
                    card.remove();
                    const counter = document.getElementById('totalReviewsCount');
                    if (counter) {
                        let cur = parseInt(counter.innerText || '0');
                        counter.innerText = Math.max(0, cur - 1);
                    }
                    // Nếu không còn đánh giá nào, có thể tải lại trang hoặc hiện thông báo
                    const remaining = document.querySelectorAll('.review-card-item');
                    if (remaining.length === 0) {
                        location.reload();
                    }
                }, 350);
            }
        } else {
            alert(data.message || 'Không thể xóa đánh giá này. Vui lòng thử lại!');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    })
    .catch((err) => {
        console.error('Lỗi khi xóa đánh giá:', err);
        alert('Lỗi kết nối khi xóa đánh giá. Vui lòng thử lại!');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

// Audio Test Synthesizer Demo (Web Audio API)
let audioCtx = null;
function playAudioDemo(type) {
    const status = document.getElementById('demoStatus');
    try {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }

        const now = audioCtx.currentTime;
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();

        osc.connect(gain);
        gain.connect(audioCtx.destination);

        if (type === 'bass') {
            if (status) status.innerHTML = '<span class="text-amber-400 font-bold">Đang phát:</span> Dải trầm Siêu Trầm Bass Drop (55Hz - 80Hz)';
            osc.type = 'sine';
            osc.frequency.setValueAtTime(120, now);
            osc.frequency.exponentialRampToValueAtTime(45, now + 1.2);
            gain.gain.setValueAtTime(0.5, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 1.5);
            osc.start(now);
            osc.stop(now + 1.5);
        } else if (type === 'vocal') {
            if (status) status.innerHTML = '<span class="text-cyan-400 font-bold">Đang phát:</span> Dải âm trung Vocal Acoustic trong trẻo (440Hz - 880Hz)';
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(440, now);
            osc.frequency.setValueAtTime(554.37, now + 0.3);
            osc.frequency.setValueAtTime(659.25, now + 0.6);
            osc.frequency.setValueAtTime(880, now + 0.9);
            gain.gain.setValueAtTime(0.3, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 1.4);
            osc.start(now);
            osc.stop(now + 1.4);
        } else if (type === 'anc') {
            if (status) status.innerHTML = '<span class="text-emerald-400 font-bold">Đang kích hoạt:</span> Mô phỏng triệt tiêu tiếng ồn môi trường ANC (Triệt tiêu 98% tạp âm)';
            osc.type = 'sine';
            osc.frequency.setValueAtTime(200, now);
            osc.frequency.exponentialRampToValueAtTime(20, now + 1.0);
            gain.gain.setValueAtTime(0.1, now);
            gain.gain.exponentialRampToValueAtTime(0.0001, now + 1.2);
            osc.start(now);
            osc.stop(now + 1.2);
        }

        setTimeout(() => {
            if (status) status.innerHTML = '<span class="text-slate-500">Sẵn sàng phát</span> • Chọn chế độ phía trên để nghe thử tiếp';
        }, 2000);

    } catch (e) {
        if (status) status.innerHTML = '<span class="text-slate-400">Trình duyệt đã kích hoạt chế độ âm thanh</span>';
    }
}

// ==========================================
// LOGIC SLIDESHOW QUAY VÒNG CÁC PHẦN CẤU TẠO
// ==========================================
const productSlides = <?= json_encode($slides, JSON_UNESCAPED_UNICODE) ?>;
let currentSlideIdx = 0;
let slideIntervalTimer = null;
let isSlideAutoPlaying = true;
const SLIDE_DURATION = 3500; // 3.5 giây tự động chuyển quay vòng một lần

function showSlide(index, isAnimated = true) {
    if (!productSlides || productSlides.length === 0) return;
    
    // Xoay vòng vô tận (infinite loop)
    currentSlideIdx = (index + productSlides.length) % productSlides.length;
    const slide = productSlides[currentSlideIdx];

    const mainImg = document.getElementById('main-image');
    const slideIcon = document.getElementById('slideIcon');
    const slideTagText = document.getElementById('slideTagText');
    const slideCounter = document.getElementById('slideCounter');
    const slideTitleIcon = document.getElementById('slideTitleIcon');
    const slideTitleText = document.getElementById('slideTitleText');
    const slideDesc = document.getElementById('slideDesc');

    if (mainImg) {
        if (isAnimated) {
            mainImg.style.opacity = '0.2';
            mainImg.style.transform = 'scale(0.96)';
            setTimeout(() => {
                mainImg.src = slide.image;
                mainImg.alt = slide.title;
                mainImg.style.opacity = '1';
                mainImg.style.transform = 'scale(1)';
            }, 150);
        } else {
            mainImg.src = slide.image;
            mainImg.alt = slide.title;
        }
    }

    if (slideTagText) slideTagText.textContent = slide.tag;
    if (slideIcon) slideIcon.className = slide.icon + ' text-cyan-400';
    if (slideCounter) slideCounter.textContent = `${currentSlideIdx + 1} / ${productSlides.length}`;
    if (slideTitleIcon) slideTitleIcon.className = slide.icon + ' text-cyan-400 mr-1.5 text-sm';
    if (slideTitleText) slideTitleText.textContent = slide.title;
    if (slideDesc) slideDesc.textContent = slide.desc;

    // Cập nhật dots indicators
    const dots = document.querySelectorAll('#dotsContainer .dot-btn');
    dots.forEach((dot, i) => {
        if (i === currentSlideIdx) {
            dot.className = 'dot-btn h-2 w-6 rounded-full bg-cyan-400 shadow-sm shadow-cyan-400/50 transition-all duration-300';
        } else {
            dot.className = 'dot-btn h-2 w-2 rounded-full bg-slate-700 hover:bg-slate-500 transition-all duration-300';
        }
    });
}

function nextSlide() {
    showSlide(currentSlideIdx + 1);
    resetAutoPlayTimer();
}

function prevSlide() {
    showSlide(currentSlideIdx - 1);
    resetAutoPlayTimer();
}

function goToSlide(idx) {
    showSlide(idx);
    resetAutoPlayTimer();
}

function startSlideAutoPlay() {
    stopSlideAutoPlay();
    if (!isSlideAutoPlaying) return;
    slideIntervalTimer = setInterval(() => {
        showSlide(currentSlideIdx + 1);
    }, SLIDE_DURATION);
}

function stopSlideAutoPlay() {
    if (slideIntervalTimer) {
        clearInterval(slideIntervalTimer);
        slideIntervalTimer = null;
    }
}

function resetAutoPlayTimer() {
    if (isSlideAutoPlaying) {
        startSlideAutoPlay();
    }
}

function toggleSlideAutoPlay() {
    isSlideAutoPlaying = !isSlideAutoPlaying;
    const icon = document.getElementById('autoPlayIcon');
    const statusText = document.getElementById('autoPlayStatus');
    if (isSlideAutoPlaying) {
        if (icon) icon.className = 'fa-solid fa-pause text-[10px]';
        if (statusText) statusText.textContent = 'Tự động quay vòng';
        startSlideAutoPlay();
    } else {
        if (icon) icon.className = 'fa-solid fa-play text-[10px]';
        if (statusText) statusText.textContent = 'Đã tạm dừng';
        stopSlideAutoPlay();
    }
}

// Chuyển đổi giữa chế độ xem Slideshow và Video ở trang chi tiết
function switchDetailMedia(type) {
    const slideFrame = document.getElementById('detailSlideshowFrame');
    const videoFrame = document.getElementById('detailVideoFrame');
    const tabSlide = document.getElementById('tabBtnSlideshow');
    const tabVideo = document.getElementById('tabBtnVideo');
    const html5Video = document.getElementById('detailHtml5Video');

    if (type === 'video') {
        if (slideFrame) slideFrame.classList.add('hidden');
        if (videoFrame) videoFrame.classList.remove('hidden');
        if (tabSlide) {
            tabSlide.className = 'inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition text-slate-400 hover:text-white';
        }
        if (tabVideo) {
            tabVideo.className = 'inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-gradient-to-r from-rose-600 to-pink-500 text-white shadow-md';
        }
        if (html5Video) {
            html5Video.play().catch(e => {});
        }
        stopSlideAutoPlay();
    } else {
        if (slideFrame) slideFrame.classList.remove('hidden');
        if (videoFrame) videoFrame.classList.add('hidden');
        if (tabSlide) {
            tabSlide.className = 'inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition bg-gradient-to-r from-primary-600 to-cyan-500 text-white shadow-md';
        }
        if (tabVideo) {
            tabVideo.className = 'inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition text-slate-400 hover:text-white';
        }
        if (html5Video) {
            html5Video.pause();
        }
        if (isSlideAutoPlaying) {
            startSlideAutoPlay();
        }
    }
}

// Xử lý chọn màu sắc sản phẩm (Ảnh ở trên, Chữ ở dưới, đổi ảnh chính, cập nhật tồn kho)
function selectProductColor(idx, colorName, colorImage, colorQty) {
    const label = document.getElementById('selectedColorLabel');
    const stockLabel = document.getElementById('selectedColorStock');
    const inputCart = document.getElementById('formSelectedColor');
    const inputBuyNow = document.getElementById('buyNowColor');
    const qtyInput = document.getElementById('qtyInput');
    const buyNowQty = document.getElementById('buyNowQty');

    if (label) label.textContent = colorName;
    if (stockLabel && colorQty !== undefined) {
        stockLabel.textContent = `(Còn ${colorQty} chiếc)`;
    }
    if (inputCart) inputCart.value = colorName;
    if (inputBuyNow) inputBuyNow.value = colorName;

    // Giới hạn số lượng tối đa theo tồn kho của màu được chọn
    if (qtyInput && colorQty !== undefined && colorQty > 0) {
        qtyInput.setAttribute('max', colorQty);
        if (parseInt(qtyInput.value) > colorQty) {
            qtyInput.value = colorQty;
        }
        if (buyNowQty) buyNowQty.value = qtyInput.value;
    }

    // Cập nhật trạng thái viền sáng và check badge cho nút màu được chọn
    const buttons = document.querySelectorAll('#colorOptionsGrid .color-option-btn');
    buttons.forEach((btn, i) => {
        const checkBadge = btn.querySelector('.check-badge');
        if (i === idx) {
            btn.className = 'color-option-btn group relative flex flex-col items-center justify-center p-2 rounded-2xl border border-cyan-400 bg-cyan-950/40 text-cyan-300 ring-2 ring-cyan-400/50 shadow-lg shadow-cyan-500/10 transition-all duration-200';
            if (checkBadge) checkBadge.classList.remove('hidden');
        } else {
            btn.className = 'color-option-btn group relative flex flex-col items-center justify-center p-2 rounded-2xl border border-slate-800 bg-slate-950/60 hover:bg-slate-900 text-slate-300 hover:border-slate-700 transition-all duration-200';
            if (checkBadge) checkBadge.classList.add('hidden');
        }
    });

    // Chuyển ảnh sản phẩm chính sang ảnh của màu vừa chọn (nếu có ảnh)
    if (colorImage) {
        const slideshowFrame = document.getElementById('detailSlideshowFrame');
        const videoFrame = document.getElementById('detailVideoFrame');
        if (slideshowFrame && videoFrame && !videoFrame.classList.contains('hidden')) {
            if (typeof switchMediaTab === 'function') {
                switchMediaTab('slideshow');
            }
        }
        const mainImg = document.getElementById('main-image');
        if (mainImg) {
            mainImg.style.opacity = '0.3';
            mainImg.style.transform = 'scale(0.95)';
            setTimeout(() => {
                mainImg.src = colorImage;
                mainImg.style.opacity = '1';
                mainImg.style.transform = 'scale(1)';
            }, 120);
        }
        const slideTitleText = document.getElementById('slideTitleText');
        const slideTagText = document.getElementById('slideTagText');
        if (slideTitleText) slideTitleText.textContent = colorName;
        if (slideTagText) slideTagText.textContent = 'Phiên bản ' + colorName;
    }
}

// Khởi chạy khi DOM sẵn sàng
document.addEventListener('DOMContentLoaded', function() {
    const prevBtn = document.getElementById('prevSlideBtn');
    const nextBtn = document.getElementById('nextSlideBtn');
    const toggleBtn = document.getElementById('toggleAutoPlayBtn');
    const gallerySection = document.getElementById('productGallerySection');
    const isVideoInitial = <?= $isMediaVideo ? 'true' : 'false' ?>;

    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    if (toggleBtn) toggleBtn.addEventListener('click', toggleSlideAutoPlay);

    // Tạm dừng khi rê chuột vào để người dùng quan sát kỹ, rê ra ngoài thì tiếp tục quay vòng
    if (gallerySection) {
        gallerySection.addEventListener('mouseenter', () => {
            if (isSlideAutoPlaying) stopSlideAutoPlay();
        });
        gallerySection.addEventListener('mouseleave', () => {
            const videoFrame = document.getElementById('detailVideoFrame');
            if (isSlideAutoPlaying && (!videoFrame || videoFrame.classList.contains('hidden'))) {
                startSlideAutoPlay();
            }
        });
    }

    // Hỗ trợ phím mũi tên trái/phải trên bàn phím
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        if (e.key === 'ArrowLeft') prevSlide();
        else if (e.key === 'ArrowRight') nextSlide();
    });

    // Bắt đầu tự động chuyển quay vòng (nếu không phải đang mở video)
    if (!isVideoInitial) {
        startSlideAutoPlay();
    }
});

function toggleDetailWishlist(productId) {
    if (typeof window.toggleWishlist === 'function') {
        window.toggleWishlist(productId, document.getElementById('btnWishlistDetail'));
    }
}

// =========================================================================
// REAL-TIME BIẾN THỂ: TỰ ĐỘNG CẬP NHẬT KHI ADMIN THÊM / ĐỔI BIẾN THỂ MÀU SẮC
// =========================================================================
let currentColorsCount = <?= count($productColors) ?>;
setInterval(async () => {
    try {
        const res = await fetch(`<?= BASE_URL ?>index.php?url=api/products/variants&product_id=<?= (int)$product['id'] ?>`);
        if (!res.ok) return;
        const data = await res.json();
        if (data.success && Array.isArray(data.colors)) {
            if (data.colors.length !== currentColorsCount) {
                currentColorsCount = data.colors.length;
                if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: '⚡ Biến thể sản phẩm vừa được cập nhật!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                setTimeout(() => location.reload(), 1500);
            }
        }
    } catch (e) {}
}, 3000);
</script>