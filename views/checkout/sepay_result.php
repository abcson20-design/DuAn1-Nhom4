<?php
/**
 * Trang kết quả thanh toán SePay
 * Dữ liệu lấy từ Database, bảo vệ bằng đối soát server-to-server
 */
?>
<div class="min-h-[70vh] flex items-center justify-center py-16 px-4">
    <div class="glass-card max-w-lg w-full rounded-3xl p-8 text-center space-y-6 shadow-2xl border <?= $isSuccess ? 'border-emerald-500/40' : 'border-amber-500/40' ?>">
        
        <?php if ($isSuccess): ?>
            <div class="w-20 h-20 mx-auto rounded-full bg-emerald-500/10 border-2 border-emerald-400 flex items-center justify-center text-emerald-400 text-3xl shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="space-y-2">
                <h1 class="text-2xl font-black text-white">Thanh Toán Thành Công!</h1>
                <p class="text-xs text-slate-300 leading-relaxed">
                    <?= htmlspecialchars($message) ?>
                </p>
            </div>
        <?php else: ?>
            <div class="w-20 h-20 mx-auto rounded-full bg-amber-500/10 border-2 border-amber-400 flex items-center justify-center text-amber-400 text-3xl shadow-lg shadow-amber-500/20">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="space-y-2">
                <h1 class="text-2xl font-black text-white"><?= ($type === 'cancel') ? 'Đã Hủy Giao Dịch' : 'Thanh Toán Chưa Hoàn Tất' ?></h1>
                <p class="text-xs text-slate-300 leading-relaxed">
                    <?= htmlspecialchars($message) ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if (!empty($order)): ?>
            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 text-left space-y-2.5 text-xs">
                <div class="flex justify-between text-slate-400 pb-2 border-b border-slate-800">
                    <span>Mã đơn hàng:</span>
                    <span class="text-white font-mono font-bold"><?= htmlspecialchars($order['order_code']) ?></span>
                </div>
                <div class="flex justify-between text-slate-400 pb-2 border-b border-slate-800">
                    <span>Số tiền:</span>
                    <span class="text-cyan-400 font-bold"><?= number_format($order['final_amount'], 0, ',', '.') ?>đ</span>
                </div>
                <div class="flex justify-between text-slate-400 pb-2 border-b border-slate-800">
                    <span>Phương thức:</span>
                    <span class="text-slate-200">Cổng SePay (VietQR / Napas 247)</span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>Trạng thái thanh toán:</span>
                    <?php if (($order['payment_status'] ?? '') === 'paid'): ?>
                        <span class="badge bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 px-2 py-0.5 rounded font-bold">Đã thanh toán (PAID)</span>
                    <?php else: ?>
                        <span class="badge bg-amber-500/20 text-amber-400 border border-amber-500/40 px-2 py-0.5 rounded font-bold">Chờ thanh toán</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <?php if (!empty($order)): ?>
                <a href="<?= BASE_URL ?>index.php?url=order/track&code=<?= htmlspecialchars($order['order_code']) ?>" class="flex-1 bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-500 hover:to-indigo-500 text-white font-bold py-3 px-4 rounded-xl text-xs flex items-center justify-center transition">
                    <i class="fa-solid fa-receipt mr-2"></i> Chi Tiết Đơn Hàng
                </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>" class="flex-1 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold py-3 px-4 rounded-xl text-xs flex items-center justify-center transition border border-slate-700">
                <i class="fa-solid fa-house mr-2"></i> Về Trang Chủ
            </a>
        </div>
    </div>
</div>
