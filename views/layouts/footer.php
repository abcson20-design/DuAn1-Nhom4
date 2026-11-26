    </main>

    <!-- Footer NEXVORA -->
    <footer class="relative z-10 border-t border-slate-700/60 bg-slate-900/80 text-slate-300 text-xs mt-20 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Col 1: Brand Info -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <img src="<?= BASE_URL ?>public/images/logo.png" alt="NEXVORA N Emblem" class="h-10 w-auto object-contain drop-shadow-[0_0_15px_rgba(6,182,212,0.45)]">
                        <div>
                            <span class="text-lg font-black tracking-tight text-white leading-none">NEX<span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400">VORA</span></span>
                            <span class="block text-[8px] uppercase tracking-widest text-cyan-400 font-extrabold -mt-0.5">Connect To The Future</span>
                        </div>
                    </div>
                    <p class="text-slate-400 leading-relaxed text-[11px]">
                        Hệ thống phân phối điện thoại flagship, tai nghe bluetooth chống ồn ANC và phụ kiện công nghệ cao cấp chính hãng. Tiên phong giải pháp thanh toán VietQR Napas 247 an toàn.
                    </p>
                    <div class="space-y-1.5 text-[11px] text-slate-300">
                        <div><i class="fa-solid fa-location-dot text-primary-400 mr-2"></i> <?= SHOP_ADDRESS ?></div>
                        <div><i class="fa-solid fa-phone text-emerald-400 mr-2"></i> Hotline: <strong class="text-white"><?= SHOP_HOTLINE ?></strong></div>
                        <div><i class="fa-solid fa-envelope text-cyan-400 mr-2"></i> <?= SHOP_EMAIL ?></div>
                    </div>
                </div>

                <!-- Col 2: Điện Thoại (Các hãng) -->
                <div class="space-y-3">
                    <h4 class="font-bold text-white text-xs uppercase tracking-wider text-cyan-400">Hãng Điện Thoại</h4>
                    <ul class="space-y-2 text-[11px]">
                        <li><a href="<?= BASE_URL ?>index.php?url=phones&brand=1" class="hover:text-cyan-400 transition flex items-center"><i class="fa-brands fa-apple text-slate-300 w-4 mr-1.5"></i> iPhone</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?url=phones&brand=2" class="hover:text-cyan-400 transition flex items-center"><i class="fa-solid fa-gem text-blue-400 w-4 mr-1.5"></i> Samsung</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?url=phones&brand=3" class="hover:text-cyan-400 transition flex items-center"><i class="fa-solid fa-fire text-amber-400 w-4 mr-1.5"></i> Xiaomi</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?url=phones&brand=4" class="hover:text-cyan-400 transition flex items-center"><i class="fa-solid fa-camera text-emerald-400 w-4 mr-1.5"></i> OPPO</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?url=phones" class="hover:text-cyan-400 transition flex items-center text-primary-400 font-semibold"><i class="fa-solid fa-arrow-right w-4 mr-1.5"></i> Xem tất cả điện thoại</a></li>
                    </ul>
                </div>

                <!-- Col 3: Phụ Kiện (Các loại) -->
                <div class="space-y-3">
                    <h4 class="font-bold text-white text-xs uppercase tracking-wider text-primary-400">Loại Phụ Kiện</h4>
                    <ul class="space-y-2 text-[11px]">
                        <li><a href="<?= BASE_URL ?>index.php?url=accessories&category=2" class="hover:text-primary-400 transition flex items-center"><i class="fa-solid fa-headphones-simple text-purple-400 w-4 mr-1.5"></i> Tai nghe Bluetooth ANC</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?url=accessories&category=3" class="hover:text-primary-400 transition flex items-center"><i class="fa-solid fa-headphones text-cyan-400 w-4 mr-1.5"></i> Tai nghe có dây Type-C/3.5mm</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?url=accessories&category=4" class="hover:text-primary-400 transition flex items-center"><i class="fa-solid fa-bolt text-amber-400 w-4 mr-1.5"></i> Củ sạc nhanh & Cáp sạc GaN</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?url=accessories&category=6" class="hover:text-primary-400 transition flex items-center"><i class="fa-solid fa-battery-three-quarters text-emerald-400 w-4 mr-1.5"></i> Sạc dự phòng MagSafe</a></li>
                        <li><a href="<?= BASE_URL ?>index.php?url=accessories&category=5" class="hover:text-primary-400 transition flex items-center"><i class="fa-solid fa-shield-halved text-rose-400 w-4 mr-1.5"></i> Ốp lưng & Kính cường lực</a></li>
                    </ul>
                </div>

                <!-- Col 4: Cam kết & Thanh toán -->
                <div class="space-y-3">
                    <h4 class="font-bold text-white text-xs uppercase tracking-wider text-emerald-400">Thanh Toán & Cam Kết</h4>
                    <div class="glass-card p-3 rounded-2xl space-y-2 text-[11px]">
                        <div class="flex items-center text-slate-200">
                            <i class="fa-solid fa-qrcode text-cyan-400 mr-2 text-sm"></i> Quét mã VietQR tự động 24/7
                        </div>
                        <div class="flex items-center text-slate-200">
                            <i class="fa-solid fa-box-open text-emerald-400 mr-2 text-sm"></i> Mở hộp kiểm tra trước khi trả tiền
                        </div>
                        <div class="flex items-center text-slate-200">
                            <i class="fa-solid fa-rotate-left text-purple-400 mr-2 text-sm"></i> 1 đổi 1 tận nơi trong 30 ngày
                        </div>
                    </div>
                    <div class="pt-2 flex flex-wrap items-center gap-1.5">
                        <span class="px-2 py-1 bg-slate-900 border border-slate-700/80 rounded-lg text-[10px] text-slate-300 whitespace-nowrap">Sepay</span>
                        <span class="px-2 py-1 bg-slate-900 border border-slate-700/80 rounded-lg text-[10px] text-slate-300 whitespace-nowrap">Vietcombank</span>
                        <span class="px-2 py-1 bg-slate-900 border border-slate-700/80 rounded-lg text-[10px] text-slate-300 whitespace-nowrap">COD</span>
                        <span class="px-2 py-1 bg-slate-900 border border-slate-700/80 rounded-lg text-[10px] text-slate-300 whitespace-nowrap">Napas 247</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-800/80 mt-10 pt-6 flex flex-col sm:flex-row justify-between items-center text-[11px] text-slate-500 gap-4">
                <div>&copy; <?= date('Y') ?> NEXVORA Store. Bản quyền thuộc về Dự Án 1 - PHP Pure MVC & Sepay.</div>
                <div class="flex items-center space-x-4">
                    <a href="<?= BASE_URL ?>index.php?url=contact" class="hover:text-cyan-400 transition">Liên hệ & CSKH</a>
                    <span>•</span>
                    <a href="<?= BASE_URL ?>index.php?url=order/track" class="hover:text-primary-400 transition">Tra cứu bảo hành</a>
                    <span>•</span>
                    <a href="<?= SHOP_ZALO ?>" target="_blank" class="hover:text-cyan-400 transition">Tư vấn Zalo</a>
                    <span>•</span>
                    <a href="<?= BASE_URL ?>index.php?url=admin/login" class="hover:text-amber-400 transition">Admin Portal</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Nút Chat Zalo Nổi Ở Góc Phải Màn Hình (Cách đáy 1/4 màn hình) -->
    <div class="fixed bottom-[25vh] right-6 z-50 flex items-center group">
        <!-- Nhãn / Tooltip hiển thị số điện thoại khi rê chuột -->
        <a href="https://zalo.me/0362225438" target="_blank" rel="noopener noreferrer" 
           class="hidden sm:inline-flex items-center bg-slate-900/95 text-white text-xs font-bold py-2 px-3.5 rounded-full border border-blue-500/40 shadow-xl shadow-blue-500/20 mr-2 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-3 group-hover:translate-x-0 backdrop-blur-md">
            <span class="w-2 h-2 rounded-full bg-emerald-400 mr-2 animate-ping"></span>
            <span>Chat Zalo: <strong class="text-cyan-400">0362.225.438</strong></span>
        </a>

        <!-- Nút Tròn Zalo với hiệu ứng sóng tỏa (Pulse Ring Animation) -->
        <a href="https://zalo.me/0362225438" target="_blank" rel="noopener noreferrer" 
           class="relative flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-tr from-[#0068ff] to-[#00a6ff] text-white shadow-2xl shadow-blue-600/50 hover:scale-110 active:scale-95 transition-all duration-300"
           title="Chat Zalo hỗ trợ: 0362225438">
            
            <!-- Vòng sóng phát sáng nhấp nháy -->
            <span class="absolute -inset-1 rounded-full bg-blue-500/50 animate-ping -z-10 pointer-events-none"></span>
            <span class="absolute -inset-2.5 rounded-full bg-blue-500/20 animate-pulse -z-10 pointer-events-none"></span>

            <!-- Logo Zalo sắc nét -->
            <img src="https://upload.wikimedia.org/wikipedia/commons/9/91/Icon_of_Zalo.svg" alt="Zalo" class="w-9 h-9 object-contain drop-shadow pointer-events-none">

            <!-- Chấm xanh Online trạng thái sẵn sàng tư vấn -->
            <span class="absolute top-0 right-0 w-3.5 h-3.5 rounded-full bg-emerald-400 border-2 border-slate-900 shadow"></span>
        </a>
    </div>

    <!-- Nút Quay lại nổi cố định góc dưới bên trái (Client) -->
    <button type="button" 
            onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '<?= BASE_URL ?>'; }" 
            id="clientFloatingBackBtn"
            class="fixed bottom-6 left-6 z-40 inline-flex items-center gap-2 px-3.5 py-2.5 rounded-2xl bg-slate-900/95 hover:bg-slate-800 text-slate-200 hover:text-cyan-400 border border-slate-700/80 hover:border-cyan-500/50 shadow-2xl shadow-cyan-950/40 text-xs font-bold backdrop-blur-xl transition-all duration-300 hover:scale-105 active:scale-95 group"
            title="Quay lại trang trước đó">
        <span class="w-6 h-6 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center group-hover:-translate-x-0.5 transition-transform">
            <i class="fa-solid fa-arrow-left text-xs"></i>
        </span>
        <span class="hidden sm:inline">Quay lại trang trước</span>
    </button>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom Main JS (có tham số chống cache trình duyệt) -->
    <script src="<?= BASE_URL ?>public/js/main.js?v=<?= @filemtime(__DIR__ . '/../../public/js/main.js') ?: time() ?>"></script>
    <!-- Universal Real-time Form Validator (Thông báo lỗi đỏ tức thì bên dưới ô nhập) -->
    <script src="<?= BASE_URL ?>public/js/form-validator.js?v=<?= @filemtime(__DIR__ . '/../../public/js/form-validator.js') ?: time() ?>"></script>
</body>
</html>
