<?php
/**
 * Kết quả thanh toán trực tuyến SePay
 * Đồng bộ 100% cấu trúc và nghiệp vụ từ dự án Base Enterprise.
 * Mọi thông tin lấy hoàn toàn từ Database (chống giả mạo URL callback).
 *
 * @var bool $success
 * @var string $message
 * @var string|null $note
 * @var array|null $order
 * @var array|null $payment
 * @var array $methods
 * @var string $redirect
 */
$orderCode = $order['order_code'] ?? $order['code'] ?? ($payment['order_code'] ?? '');
$methodLabel = $methods[(string)($payment['bank_code'] ?? '')] ?? ($payment['bank_code'] ?? 'Chuyển khoản SePay');
$status = $payment['status'] ?? 'pending';
?>

<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6">
    <div class="glass-card max-w-xl w-full rounded-3xl p-6 sm:p-8 text-center space-y-6 shadow-2xl border <?= $success ? 'border-emerald-500/40' : 'border-rose-500/40' ?> relative overflow-hidden">
        <!-- Glow effects -->
        <div class="absolute -top-24 -right-24 w-48 h-48 <?= $success ? 'bg-emerald-500/10' : 'bg-rose-500/10' ?> rounded-full blur-3xl pointer-events-none"></div>

        <!-- Result Icon -->
        <div>
            <?php if ($success): ?>
                <div class="w-20 h-20 mx-auto rounded-3xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-3xl shadow-lg shadow-emerald-500/20">
                    <i class="fa-solid fa-check"></i>
                </div>
            <?php else: ?>
                <div class="w-20 h-20 mx-auto rounded-3xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-rose-400 text-3xl shadow-lg shadow-rose-500/20">
                    <i class="fa-solid fa-exclamation"></i>
                </div>
            <?php endif; ?>
        </div>

        <!-- Header -->
        <div class="space-y-2">
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                <?= $success ? 'Thanh toán thành công' : 'Thanh toán chưa hoàn tất' ?>
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 max-w-md mx-auto leading-relaxed">
                <?= htmlspecialchars($message) ?>
            </p>
            <?php if (!empty($note)): ?>
                <p class="text-xs text-amber-400 bg-amber-500/10 border border-amber-500/20 px-3 py-1.5 rounded-xl max-w-md mx-auto">
                    <i class="fa-solid fa-circle-info mr-1"></i> <?= htmlspecialchars($note) ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Chi tiết giao dịch xác thực từ Database -->
        <?php if ($payment): ?>
            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 text-left space-y-3 text-xs">
                <div class="font-bold text-slate-300 uppercase tracking-wider text-[11px] border-b border-slate-800 pb-2 flex justify-between items-center">
                    <span>Thông tin giao dịch SePay</span>
                    <span class="text-[10px] text-slate-500 font-mono">ID: #<?= (int)$payment['id'] ?></span>
                </div>

                <?php if ($orderCode): ?>
                    <div class="flex justify-between items-center text-slate-400">
                        <span>Mã đơn hàng:</span>
                        <span class="text-white font-mono font-bold"><?= htmlspecialchars($orderCode) ?></span>
                    </div>
                <?php endif; ?>

                <div class="flex justify-between items-center text-slate-400">
                    <span>Số tiền:</span>
                    <span class="text-rose-400 font-black text-sm"><?= number_format((int)$payment['amount'], 0, ',', '.') ?>₫</span>
                </div>

                <div class="flex justify-between items-center text-slate-400">
                    <span>Mã hoá đơn SePay:</span>
                    <code class="text-cyan-400 font-mono bg-cyan-950/40 px-2 py-0.5 rounded border border-cyan-800/40 text-[11px]"><?= htmlspecialchars($payment['txn_ref']) ?></code>
                </div>

                <div class="flex justify-between items-center text-slate-400">
                    <span>Mã giao dịch ngân hàng:</span>
                    <span class="text-slate-200 font-mono"><?= htmlspecialchars($payment['transaction_no'] ?? '—') ?></span>
                </div>

                <div class="flex justify-between items-center text-slate-400">
                    <span>Hình thức thanh toán:</span>
                    <span class="text-slate-200"><?= htmlspecialchars($methodLabel) ?></span>
                </div>

                <div class="flex justify-between items-center text-slate-400 pt-1 border-t border-slate-800">
                    <span>Trạng thái ghi nhận:</span>
                    <?php if ($status === 'success'): ?>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                            <i class="fa-solid fa-circle-check mr-1"></i> ĐÃ THANH TOÁN
                        </span>
                    <?php elseif ($status === 'pending'): ?>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                            <i class="fa-solid fa-clock mr-1"></i> CHỜ XÁC NHẬN
                        </span>
                    <?php else: ?>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40">
                            <i class="fa-solid fa-circle-xmark mr-1"></i> THẤT BẠI
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Nút hành động -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <?php if ($orderCode): ?>
                <a href="<?= BASE_URL ?>index.php?url=order/track&q=<?= urlencode($orderCode) ?>" 
                   class="w-full sm:w-auto py-3 px-6 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/20 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Xem chi tiết đơn hàng</span>
                </a>
            <?php endif; ?>

            <?php if (!$success && $orderCode && ($status === 'pending' || $redirect === 'cancel' || $redirect === 'error')): ?>
                <a href="<?= BASE_URL ?>index.php?url=checkout/change-payment-method&code=<?= urlencode($orderCode) ?>&method=sepay" 
                   class="w-full sm:w-auto py-3 px-6 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white font-bold text-xs shadow-lg shadow-amber-500/20 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-rotate-right"></i>
                    <span>Thử thanh toán lại</span>
                </a>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>" 
               class="w-full sm:w-auto py-3 px-6 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-house"></i>
                <span>Về trang chủ</span>
            </a>
        </div>
    </div>
</div>
