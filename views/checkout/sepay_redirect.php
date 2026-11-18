<?php
/**
 * Cổng thanh toán trực tuyến SePay (Hỗ trợ Sandbox Simulator & Production Gateway)
 */
$refCode = $txnRef ?? $checkout['fields']['order_invoice_number'] ?? '';
$isSandboxEnv = defined('SEPAY_ENV') && SEPAY_ENV === 'sandbox';

// Link mã QR demo phục vụ mô phỏng giao dịch
$qrApiUrl = 'https://api.vietqr.io/image/' . rawurlencode(BANK_ID) . '-' . rawurlencode(BANK_ACCOUNT_NO) . '-' . rawurlencode(VIETQR_TEMPLATE) . '.png?amount=' . urlencode((string)(int)$amount) . '&addInfo=' . urlencode('SEPAY ' . $orderCode) . '&accountName=' . rawurlencode(BANK_ACCOUNT_NAME);
?>
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6">
    <div class="glass-card max-w-xl w-full rounded-3xl p-6 sm:p-8 text-center space-y-6 shadow-2xl border border-cyan-500/40 relative overflow-hidden">
        <!-- Background Glow -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- SePay Header -->
        <div class="space-y-3">
            <div class="inline-flex items-center gap-2 bg-amber-500/15 text-amber-300 border border-amber-500/30 px-3.5 py-1.5 rounded-full text-xs font-bold">
                <i class="fa-solid fa-bolt text-amber-400"></i>
                <span>CỔNG THANH TOÁN TRỰC TUYẾN SEPAY</span>
                <?php if ($isSandboxEnv): ?>
                    <span class="bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 px-2 py-0.5 rounded text-[10px]">SANDBOX MÔ PHỎNG</span>
                <?php endif; ?>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                Xác Nhận Thanh Toán Đơn Hàng
            </h1>
            <p class="text-xs text-slate-400">
                Thanh toán an toàn qua Cổng liên ngân hàng SePay (Napas 24/7 &amp; VietQR)
            </p>
        </div>

        <!-- Chi tiết hóa đơn SePay -->
        <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/80 border border-slate-800 text-left space-y-2.5 text-xs">
            <div class="flex justify-between items-center text-slate-400 pb-2 border-b border-slate-800/80">
                <span>Mã đơn hàng:</span>
                <span class="text-white font-mono font-bold text-sm"><?= htmlspecialchars($orderCode) ?></span>
            </div>
            <div class="flex justify-between items-center text-slate-400 pb-2 border-b border-slate-800/80">
                <span>Số tiền thanh toán:</span>
                <span class="text-rose-400 font-black text-base"><?= number_format($amount, 0, ',', '.') ?>₫</span>
            </div>
            <div class="flex justify-between items-center text-slate-400">
                <span>Mã giao dịch SePay (Ref):</span>
                <span class="text-cyan-400 font-mono text-[11px]"><?= htmlspecialchars($refCode) ?></span>
            </div>
        </div>

        <?php if ($isSandboxEnv): ?>
            <!-- KHỐI MÔ PHỎNG GIẢ LẬP THANH TOÁN SEPAY (SANDBOX TESTING) -->
            <div class="p-5 rounded-2xl bg-gradient-to-b from-slate-900 via-slate-900/90 to-slate-950 border border-emerald-500/40 space-y-4 shadow-lg shadow-emerald-950/20">
                <div class="flex items-center justify-between text-xs pb-3 border-b border-slate-800">
                    <span class="font-bold text-emerald-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-flask-vial"></i> Bảng Điều Khiển Giả Lập Sandbox
                    </span>
                    <span class="text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 px-2 py-0.5 rounded font-mono font-bold">1-Click Test</span>
                </div>

                <div class="bg-white p-3 rounded-2xl inline-block shadow-md">
                    <img src="<?= htmlspecialchars($qrApiUrl) ?>" alt="VietQR Sandbox" class="w-44 h-44 object-contain mx-auto">
                </div>

                <p class="text-[11px] text-slate-400 max-w-sm mx-auto leading-relaxed">
                    Bạn đang ở chế độ <strong>Sandbox / Kiểm thử đồ án</strong>. Bấm nút màu xanh bên dưới để giả lập SePay gửi tín hiệu xác nhận thanh toán thành công ngay lập tức!
                </p>

                <!-- Nút Giả lập thanh toán SePay thành công -->
                <a href="<?= BASE_URL ?>index.php?url=payment/sepay/simulate&ref=<?= urlencode($refCode) ?>&csrf_token=<?= urlencode(Csrf::token()) ?>" 
                   class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-500 hover:from-emerald-500 hover:to-cyan-400 text-white font-extrabold text-sm shadow-xl shadow-emerald-500/30 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span>GIẢ LẬP THANH TOÁN THÀNH CÔNG NGAY</span>
                </a>
            </div>

            <!-- Các tùy chọn phụ -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 text-xs">
                <a href="<?= BASE_URL ?>index.php?url=payment/sepay/cancel&ref=<?= urlencode($refCode) ?>" 
                   class="w-full sm:w-auto text-slate-400 hover:text-rose-400 transition py-2 px-3 rounded-xl border border-slate-800 hover:border-rose-500/30">
                    <i class="fa-solid fa-xmark mr-1"></i> Hủy thanh toán SePay
                </a>

                <?php if (!empty($checkout['fields']['signature'])): ?>
                    <button type="button" onclick="document.getElementById('sepay-checkout-form').submit();" 
                            class="w-full sm:w-auto text-cyan-400 hover:text-white transition py-2 px-3 rounded-xl border border-cyan-800 hover:bg-cyan-950/40">
                        <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Chuyển tới cổng SePay thật &rarr;
                    </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- CHẾ ĐỘ PRODUCTION: TỰ ĐỘNG CHUYỂN HƯỚNG SANG SEPAY GATEWAY -->
            <div class="py-4 space-y-3">
                <div class="w-10 h-10 mx-auto rounded-full bg-cyan-500/10 border-2 border-cyan-400 flex items-center justify-center text-cyan-400 text-lg animate-spin">
                    <i class="fa-solid fa-spinner"></i>
                </div>
                <p class="text-xs text-slate-400">Đang tự động chuyển hướng tới Cổng thanh toán SePay trong 1 giây...</p>
            </div>
        <?php endif; ?>

        <!-- Form ẩn chứa chữ ký bảo mật HMAC-SHA256 để POST sang SePay nếu cần -->
        <form id="sepay-checkout-form" method="post" action="<?= htmlspecialchars($checkout['action']) ?>" class="hidden">
            <?php foreach ($checkout['fields'] as $name => $value): ?>
                <input type="hidden" name="<?= htmlspecialchars($name) ?>" value="<?= htmlspecialchars((string)$value) ?>">
            <?php endforeach; ?>
            <noscript>
                <button type="submit" class="w-full bg-cyan-500 text-white p-3 rounded-xl">Bấm vào đây để chuyển sang SePay</button>
            </noscript>
        </form>
    </div>
</div>

<?php if (!$isSandboxEnv): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            const form = document.getElementById('sepay-checkout-form');
            if (form) form.submit();
        }, 800);
    });
</script>
<?php endif; ?>
