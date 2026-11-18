<?php
/**
 * Trang trung gian: In form ẩn đã ký chữ ký bảo mật HMAC-SHA256 rồi tự POST sang trang thanh toán của SePay.
 * Đồng bộ 100% cấu trúc từ dự án Base Enterprise.
 *
 * @var array{action: string, fields: array<string, string>} $checkout
 */
$refCode = $checkout['fields']['order_invoice_number'] ?? '';
$amount = (int)($checkout['fields']['order_amount'] ?? 0);
$isSandbox = defined('SEPAY_ENV') && SEPAY_ENV === 'sandbox';
?>

<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6">
    <div class="glass-card max-w-lg w-full rounded-3xl p-6 sm:p-8 text-center space-y-6 shadow-2xl border border-cyan-500/40 relative overflow-hidden">
        <!-- Glow effects -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header -->
        <div class="space-y-3">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-2xl animate-pulse">
                <i class="fa-solid fa-lock"></i>
            </div>

            <h1 class="text-2xl font-black text-white tracking-tight">
                Đang chuyển sang trang thanh toán…
            </h1>
            <p class="text-xs text-slate-400">
                Hệ thống đang kết nối an toàn với Cổng thanh toán trực tuyến SePay (Napas / VietQR 24/7).
            </p>
        </div>

        <!-- Chi tiết hóa đơn -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 text-left space-y-2 text-xs">
            <div class="flex justify-between items-center text-slate-400 pb-2 border-b border-slate-800">
                <span>Mã hoá đơn SePay:</span>
                <span class="text-white font-mono font-bold"><?= htmlspecialchars($refCode) ?></span>
            </div>
            <div class="flex justify-between items-center text-slate-400 pb-2 border-b border-slate-800">
                <span>Số tiền cần thanh toán:</span>
                <span class="text-rose-400 font-black text-sm"><?= number_format($amount, 0, ',', '.') ?>₫</span>
            </div>
            <div class="flex justify-between items-center text-slate-400">
                <span>Cổng xử lý:</span>
                <span class="text-cyan-400 font-medium">SePay Payment Gateway (v1)</span>
            </div>
        </div>

        <!-- Form POST ẩn gửi sang SePay -->
        <form id="payment-redirect" method="post" action="<?= htmlspecialchars($checkout['action']) ?>" class="space-y-3">
            <?php foreach ($checkout['fields'] as $name => $value): ?>
                <input type="hidden" name="<?= htmlspecialchars($name) ?>" value="<?= htmlspecialchars((string)$value) ?>">
            <?php endforeach; ?>

            <button class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-sm shadow-lg shadow-cyan-500/25 transition flex items-center justify-center gap-2" type="submit">
                <span>Tiếp tục thanh toán</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <p class="text-[11px] text-slate-500">
            Nếu trình duyệt không tự chuyển trong 2 giây, vui lòng bấm nút "Tiếp tục thanh toán" ở trên.
        </p>

        <?php if ($isSandbox && !empty($refCode)): ?>
            <!-- Hỗ trợ Sandbox Simulator trực tiếp -->
            <div class="pt-4 border-t border-slate-800 space-y-2">
                <div class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider flex items-center justify-center gap-1">
                    <i class="fa-solid fa-flask-vial"></i> Chế độ Sandbox / Thử nghiệm
                </div>
                <a href="<?= BASE_URL ?>index.php?url=payment/sepay/simulate&ref=<?= urlencode($refCode) ?>&csrf_token=<?= urlencode(Csrf::token()) ?>" 
                   class="inline-flex items-center gap-2 text-xs font-bold text-emerald-400 hover:text-emerald-300 py-2 px-4 rounded-xl border border-emerald-500/30 bg-emerald-950/20 hover:bg-emerald-950/40 transition">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Giả lập thanh toán thành công ngay (1-Click Test)</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tự động submit sau 600ms
        setTimeout(function () {
            var form = document.getElementById('payment-redirect');
            if (form) {
                form.submit();
            }
        }, 600);
    });
</script>
