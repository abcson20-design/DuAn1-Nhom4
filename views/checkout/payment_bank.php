<?php
/**
 * Đã tinh giản hệ thống: Chỉ sử dụng Cổng SePay Trực Tuyến và Ship COD.
 */
$code = $order['order_code'] ?? '';
header('Location: ' . BASE_URL . 'index.php?url=order/track&code=' . urlencode($code));
exit;
?>

    <div class="glass-card rounded-3xl p-6 sm:p-10 border border-slate-700/80 space-y-8">
        <div class="text-center space-y-2">
            <span class="bg-primary-500/20 text-cyan-300 border border-primary-500/40 text-xs font-bold px-4 py-1.5 rounded-full inline-flex items-center">
                <i class="fa-solid fa-building-columns mr-1.5"></i> Chuyển Khoản Trực Tiếp Qua Ngân Hàng
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white">
                Mã Đơn Hàng: <span class="text-cyan-400 font-mono"><?= htmlspecialchars($order['order_code']) ?></span>
            </h2>
            <p class="text-xs text-slate-400">Vui lòng chuyển khoản đúng số tiền và điền mã giao dịch bên dưới</p>
        </div>

        <!-- Bank Card Visual -->
        <div class="rounded-3xl p-6 bg-gradient-to-tr from-slate-900 via-indigo-950 to-slate-900 border border-slate-700/80 shadow-2xl relative overflow-hidden space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-sm font-black text-cyan-400 tracking-wider flex items-center">
                    <i class="fa-solid fa-credit-card mr-2 text-base"></i> <?= htmlspecialchars($bankName ?? '') ?>
                </span>
                <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[10px] font-bold px-2.5 py-0.5 rounded-md">Tài Khoản Doanh Nghiệp</span>
            </div>

            <div>
                <span class="text-[10px] text-slate-400 uppercase tracking-widest block font-bold">Số tài khoản nhận tiền</span>
                <div class="flex items-center justify-between mt-1">
                    <span class="text-2xl sm:text-3xl font-mono font-black text-white tracking-widest" id="bankAccNo"><?= htmlspecialchars($accountNo ?? '') ?></span>
                    <button type="button" onclick="copyText('bankAccNo', 'Đã copy số tài khoản!')" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs px-3 py-1.5 rounded-xl border border-slate-700 transition">
                        <i class="fa-regular fa-copy mr-1"></i> Copy
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-800">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Chủ tài khoản</span>
                    <strong class="text-xs text-slate-200"><?= htmlspecialchars($accountName ?? '') ?></strong>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Số tiền cần chuyển</span>
                    <strong class="text-base font-black text-rose-400"><?= number_format($order['final_amount'], 0, ',', '.') ?>đ</strong>
                </div>
            </div>
        </div>

        <!-- Confirm Form -->
        <div class="p-6 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-4">
            <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center">
                <i class="fa-solid fa-pen-to-square text-cyan-400 mr-2"></i> Xác Nhận Giao Dịch Sau Khi Đã Chuyển Tiền:
            </h4>

            <form action="<?= BASE_URL ?>index.php?url=checkout/confirm-payment" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="order_code" value="<?= htmlspecialchars($order['order_code']) ?>">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Ngân hàng bạn vừa chuyển đi</label>
                        <input type="text" name="sender_bank" placeholder="Ví dụ: Vietcombank, Techcombank..." required class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-900 text-slate-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Mã giao dịch / Mã tham chiếu FT...</label>
                        <input type="text" name="transaction_id" placeholder="FT241105..." required class="w-full text-xs p-3 rounded-xl border border-slate-700 bg-slate-900 text-slate-100 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>
                </div>

                <div class="text-center pt-2">
                    <button type="submit" class="bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-500 hover:to-indigo-500 text-white font-extrabold text-xs py-3.5 px-8 rounded-2xl shadow-xl shadow-primary-600/30 transition transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-circle-check mr-2"></i> Xác Nhận Tôi Đã Chuyển Tiền
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center">
            <a href="<?= BASE_URL ?>index.php?url=checkout/payment-qr&code=<?= urlencode($order['order_code']) ?>" class="text-xs text-cyan-400 hover:underline">
                <i class="fa-solid fa-qrcode mr-1"></i> Hoặc chuyển sang quét mã VietQR tự động
            </a>
        </div>

        <!-- KHỐI ĐIỀU HƯỚNG QUAY LẠI: ĐỔI PHƯƠNG THỨC HOẶC ĐỔI SẢN PHẨM -->
        <div class="pt-6 border-t border-slate-800/80 space-y-4">
            <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider text-center">
                <i class="fa-solid fa-arrow-rotate-left mr-1 text-cyan-400"></i> Bạn muốn thay đổi phương thức hoặc sản phẩm?
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-xl mx-auto">
                <!-- Nút Đổi Phương Thức Thanh Toán -->
                <button type="button" onclick="openChangePaymentModal()" class="py-3 px-4 rounded-2xl bg-slate-900 hover:bg-slate-800 border border-slate-700 hover:border-cyan-500/50 text-slate-200 text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fa-solid fa-credit-card text-cyan-400 text-sm"></i>
                    <span>Đổi Phương Thức Thanh Toán</span>
                </button>

                <!-- Nút Đổi Sản Phẩm / Quay Lại Giỏ Hàng -->
                <form action="<?= BASE_URL ?>index.php?url=checkout/rollback-to-cart" method="POST" onsubmit="return confirm('Hệ thống sẽ giữ lại toàn bộ sản phẩm của đơn hàng vào giỏ hàng để bạn thoải mái chọn thêm, bớt hoặc đổi màu/mẫu khác. Bạn có chắc muốn quay lại giỏ hàng?');" class="m-0">
                    <?= csrf_field() ?>
                    <input type="hidden" name="order_code" value="<?= htmlspecialchars($order['order_code']) ?>">
                    <button type="submit" class="w-full py-3 px-4 rounded-2xl bg-slate-900 hover:bg-rose-500/15 border border-slate-700 hover:border-rose-500/40 text-slate-200 hover:text-rose-300 text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm">
                        <i class="fa-solid fa-cart-shopping text-amber-400 text-sm"></i>
                        <span>Đổi Sản Phẩm / Sửa Giỏ Hàng</span>
                    </button>
                </form>
            </div>

            <!-- Nút Quay lại Sửa thông tin nhận hàng -->
            <div class="text-center pt-1">
                <form action="<?= BASE_URL ?>index.php?url=checkout/rollback-to-checkout" method="POST" onsubmit="return confirm('Quay lại trang thanh toán để sửa địa chỉ giao hàng, SĐT hoặc áp dụng mã giảm giá khác?');" class="inline-block">
                    <?= csrf_field() ?>
                    <input type="hidden" name="order_code" value="<?= htmlspecialchars($order['order_code']) ?>">
                    <button type="submit" class="text-xs text-slate-400 hover:text-cyan-400 transition inline-flex items-center gap-1.5 underline-offset-4 hover:underline">
                        <i class="fa-solid fa-pen-to-square text-[11px]"></i> Sửa lại địa chỉ giao hàng hoặc mã giảm giá
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- POPUP MODAL ĐỔI PHƯƠNG THỨC THANH TOÁN -->
<div id="changePaymentModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200 text-left">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-cyan-500/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 font-bold text-sm">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Đổi Phương Thức Thanh Toán</h3>
                    <p class="text-[11px] text-slate-400">Chọn hình thức thanh toán mới phù hợp với bạn</p>
                </div>
            </div>
            <button type="button" onclick="closeChangePaymentModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-6 space-y-3 text-xs">
            <!-- 1. Thanh toán khi nhận hàng (COD) -->
            <form action="<?= BASE_URL ?>index.php?url=checkout/change-payment-method" method="POST" onsubmit="return confirm('Chuyển đơn hàng sang Thanh toán khi nhận hàng (Ship COD)?');">
                <?= csrf_field() ?>
                <input type="hidden" name="order_code" value="<?= htmlspecialchars($order['order_code']) ?>">
                <input type="hidden" name="new_method" value="cod">
                <button type="submit" class="w-full p-4 rounded-2xl border border-slate-700 bg-slate-950/60 hover:border-emerald-500/60 hover:bg-emerald-950/15 transition flex items-center justify-between text-left group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-base shrink-0 group-hover:scale-105 transition">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs text-white group-hover:text-emerald-300">Thanh Toán Khi Nhận Hàng (Ship COD)</div>
                            <div class="text-[11px] text-slate-400">Kiểm tra máy/tai nghe rồi mới gửi tiền cho shipper</div>
                        </div>
                    </div>
                    <span class="text-xs text-emerald-400 font-bold flex items-center gap-1 shrink-0">
                        Chọn <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </span>
                </button>
            </form>

            <!-- 2. Cổng SePay Trực Tuyến -->
            <form action="<?= BASE_URL ?>index.php?url=checkout/change-payment-method" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="order_code" value="<?= htmlspecialchars($order['order_code']) ?>">
                <input type="hidden" name="new_method" value="sepay">
                <button type="submit" class="w-full p-4 rounded-2xl border border-slate-700 bg-slate-950/60 hover:border-amber-500/60 hover:bg-amber-950/15 transition flex items-center justify-between text-left group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 text-base shrink-0 group-hover:scale-105 transition">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div>
                            <div class="font-bold text-xs text-white group-hover:text-amber-300">Cổng Thanh Toán SePay (Napas 24/7)</div>
                            <div class="text-[11px] text-slate-400">Cổng thanh toán tự động xác nhận trong 3 giây</div>
                        </div>
                    </div>
                    <span class="text-xs text-amber-400 font-bold flex items-center gap-1 shrink-0">
                        Chọn <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </span>
                </button>
            </form>
        </div>

        <div class="p-4 bg-slate-950/60 border-t border-slate-800 text-center">
            <button type="button" onclick="closeChangePaymentModal()" class="text-xs text-slate-400 hover:text-white font-semibold">
                Đóng
            </button>
        </div>
    </div>
</div>

<script>
function openChangePaymentModal() {
    const m = document.getElementById('changePaymentModal');
    if (m) m.classList.remove('hidden');
}
function closeChangePaymentModal() {
    const m = document.getElementById('changePaymentModal');
    if (m) m.classList.add('hidden');
}
window.addEventListener('click', function(e) {
    const m = document.getElementById('changePaymentModal');
    if (e.target === m) closeChangePaymentModal();
});
function copyText(elementId, successMsg) {
    const text = document.getElementById(elementId).innerText.replace(/[^\d]/g, '');
    navigator.clipboard.writeText(text).then(() => {
        alert(successMsg);
    });
}
</script>
