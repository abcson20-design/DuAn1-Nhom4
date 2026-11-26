<?php
/**
 * Đã tinh giản hệ thống: Chỉ sử dụng Cổng SePay Trực Tuyến và Ship COD.
 */
$code = $order['order_code'] ?? '';
header('Location: ' . BASE_URL . 'index.php?url=order/track&code=' . urlencode($code));
exit;
?>

    <div class="glass-card rounded-3xl p-6 sm:p-10 border border-slate-700/80 space-y-8 text-center">
        <!-- Status Header -->
        <div class="inline-flex items-center space-x-2 bg-amber-500/20 text-amber-300 border border-amber-500/40 text-xs font-bold px-4 py-1.5 rounded-full">
            <i class="fa-regular fa-clock animate-spin"></i>
            <span>Đang chờ quét mã thanh toán VietQR</span>
        </div>

        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-white">
                Thanh Toán Đơn Hàng: <span class="text-cyan-400 font-mono"><?= htmlspecialchars($order['order_code']) ?></span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">Mở ứng dụng Ngân hàng (MB, VCB, Techcombank, VPBank...) hoặc MoMo để quét mã</p>
        </div>

        <!-- Countdown Timer -->
        <div class="inline-flex items-center space-x-2 bg-slate-900 border border-slate-700/80 px-4 py-2 rounded-2xl text-xs text-slate-300">
            <i class="fa-solid fa-stopwatch text-rose-400"></i>
            <span>Thời gian giữ mã &amp; sản phẩm: <strong id="countdownTimer" class="text-rose-400 font-mono text-sm">15:00</strong></span>
        </div>

        <!-- QR Showcase & Bank Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center text-left">
            <!-- Left: QR Code -->
            <div class="bg-slate-950 p-6 rounded-3xl border border-cyan-500/30 text-center shadow-2xl relative group">
                <div class="bg-white p-3 rounded-2xl inline-block shadow-lg">
                    <img src="<?= htmlspecialchars($qrApiUrl) ?>" alt="VietQR" class="w-64 h-64 object-contain mx-auto" id="vietqrImg">
                </div>
                <div class="mt-3 text-[11px] text-cyan-400 font-bold flex items-center justify-center">
                    <i class="fa-solid fa-qrcode mr-1.5"></i> Chuẩn VietQR Quốc Gia (Napas 247)
                </div>
            </div>

            <!-- Right: Bank Account Details with Copy Buttons -->
            <div class="space-y-3">
                <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800">
                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Ngân hàng thụ hưởng</span>
                    <strong class="text-xs text-white"><?= htmlspecialchars($bankName) ?> (<?= htmlspecialchars($bankId) ?>)</strong>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 flex justify-between items-center">
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase font-bold block">Số tài khoản</span>
                        <strong class="text-sm font-mono text-cyan-400" id="accNumber"><?= htmlspecialchars($accountNo) ?></strong>
                    </div>
                    <button type="button" onclick="copyText('accNumber', 'Đã copy số tài khoản!')" class="text-xs text-slate-300 hover:text-white bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-700 transition">
                        <i class="fa-regular fa-copy mr-1"></i> Copy
                    </button>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800">
                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Chủ tài khoản</span>
                    <strong class="text-xs text-white"><?= htmlspecialchars($accountName) ?></strong>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 flex justify-between items-center">
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase font-bold block">Số tiền cần thanh toán</span>
                        <strong class="text-base font-black text-rose-400" id="payAmount"><?= number_format($order['final_amount'], 0, ',', '.') ?>đ</strong>
                    </div>
                    <button type="button" onclick="copyText('payAmount', 'Đã copy số tiền!')" class="text-xs text-slate-300 hover:text-white bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-700 transition">
                        <i class="fa-regular fa-copy mr-1"></i> Copy
                    </button>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-amber-500/40 bg-amber-950/10 flex justify-between items-center">
                    <div>
                        <span class="text-[10px] text-amber-400 uppercase font-bold block">Nội dung chuyển khoản (Bắt buộc giữ nguyên)</span>
                        <strong class="text-xs font-mono text-white" id="orderMemo"><?= htmlspecialchars($transferContent) ?></strong>
                    </div>
                    <button type="button" onclick="copyText('orderMemo', 'Đã copy nội dung chuyển khoản!')" class="text-xs text-slate-300 hover:text-white bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-700 transition">
                        <i class="fa-regular fa-copy mr-1"></i> Copy
                    </button>
                </div>
            </div>
        </div>

        <!-- Tự Động Đồng Bộ Giao Dịch Ngân Hàng (Đã Bỏ Nút Bấm Thủ Công) -->
        <div class="pt-4 border-t border-slate-800/80 space-y-4">
            <div class="max-w-md mx-auto p-4 rounded-2xl bg-slate-900/90 border border-cyan-500/30 flex items-center gap-3.5 text-left shadow-lg shadow-cyan-950/30">
                <div class="relative flex items-center justify-center shrink-0">
                    <span class="animate-ping absolute inline-flex h-8 w-8 rounded-full bg-cyan-400 opacity-60"></span>
                    <div class="w-8 h-8 rounded-full bg-cyan-500/20 text-cyan-400 flex items-center justify-center border border-cyan-400/50">
                        <i class="fa-solid fa-satellite-dish text-sm animate-pulse"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="text-xs font-bold text-white flex items-center gap-1.5">
                        <span>Đang tự động đồng bộ biến động số dư</span>
                        <span class="flex space-x-1">
                            <span class="w-1.5 h-1.5 bg-cyan-400 rounded-full animate-bounce"></span>
                            <span class="w-1.5 h-1.5 bg-cyan-400 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                            <span class="w-1.5 h-1.5 bg-cyan-400 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Vui lòng không đóng trình duyệt. Khi tài khoản NEXVORA nhận được tiền, hệ thống sẽ tự động xác nhận và chuyển tiếp đơn hàng của bạn ngay lập tức!
                    </p>
                </div>
            </div>

            <!-- Nút Xác Nhận Đã Chuyển Tiền Nổi Bật -->
            <div class="max-w-md mx-auto space-y-2">
                <button type="button" onclick="confirmPaymentPaid()" id="btnConfirmPaid" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-500 hover:from-emerald-500 hover:to-cyan-400 text-white font-extrabold text-sm shadow-xl shadow-emerald-500/30 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span>TÔI ĐÃ CHUYỂN TIỀN - XÁC NHẬN NGAY</span>
                </button>
                <p class="text-[11px] text-slate-400 text-center">
                    Bấm nút trên sau khi bạn đã quét mã và chuyển khoản thành công trên App Ngân hàng.
                </p>
            </div>
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
            <?php if (($order['payment_method'] ?? '') !== 'sepay'): ?>
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
            <?php endif; ?>
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
</script>

<script>
// Countdown Timer (15 Phút)
let timeLeft = 15 * 60;
const timerEl = document.getElementById('countdownTimer');
const countdownInterval = setInterval(() => {
    timeLeft--;
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    timerEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    if (timeLeft <= 0) {
        clearInterval(countdownInterval);
        if (window.Swal) {
            Swal.fire({
                title: 'Hết thời gian!',
                text: 'Hết thời gian quét mã QR. Vui lòng đặt lại đơn hàng hoặc liên hệ hotline để được hỗ trợ.',
                icon: 'warning',
                background: '#0f172a',
                color: '#f8fafc',
                confirmButtonColor: '#6366f1'
            });
        }
    }
}, 1000);

// Polling kiểm tra trạng thái thanh toán tự động (mỗi 2.5 giây)
const orderCode = '<?= htmlspecialchars($order['order_code']) ?>';
let isRedirecting = false;

const pollInterval = setInterval(() => {
    if (isRedirecting) return;

    fetch('<?= BASE_URL ?>index.php?url=checkout/check-status&code=' + orderCode)
        .then(res => res.json())
        .then(data => {
            if (data.is_paid && !isRedirecting) {
                isRedirecting = true;
                clearInterval(pollInterval);
                clearInterval(countdownInterval);

                if (window.Swal) {
                    Swal.fire({
                        title: 'Thanh Toán Thành Công!',
                        html: '<div class="text-xs text-slate-300 space-y-1"><p>Tài khoản chủ đã ghi nhận thanh toán của bạn.</p><p class="text-emerald-400 font-bold">Đang tự động chuyển tiếp đơn hàng...</p></div>',
                        icon: 'success',
                        background: '#0f172a',
                        color: '#f8fafc',
                        showConfirmButton: false,
                        timer: 1800,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = '<?= BASE_URL ?>index.php?url=order/success&code=' + orderCode;
                    });
                } else {
                    window.location.href = '<?= BASE_URL ?>index.php?url=order/success&code=' + orderCode;
                }
            }
        })
        .catch(err => console.log('Checking payment status...'));
}, 2500);

// Xác nhận thanh toán thành công và đồng bộ ngay lập tức
function confirmPaymentPaid() {
    const btn = document.getElementById('btnConfirmPaid');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-base"></i> <span>Đang xác nhận với hệ thống ngân hàng...</span>';
    }

    fetch('<?= BASE_URL ?>index.php?url=checkout/simulate-paid&code=' + orderCode)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                isRedirecting = true;
                clearInterval(pollInterval);
                clearInterval(countdownInterval);

                if (window.Swal) {
                    Swal.fire({
                        title: 'Đã Nhận Được Tiền!',
                        html: '<div class="text-xs text-slate-300 space-y-1"><p>Hệ thống đã xác nhận thanh toán cho đơn hàng <b>' + orderCode + '</b>.</p><p class="text-emerald-400 font-bold">Đang chuyển đến trang hóa đơn chi tiết...</p></div>',
                        icon: 'success',
                        background: '#0f172a',
                        color: '#f8fafc',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = '<?= BASE_URL ?>index.php?url=order/success&code=' + orderCode;
                    });
                } else {
                    window.location.href = '<?= BASE_URL ?>index.php?url=order/success&code=' + orderCode;
                }
            } else {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-circle-check text-lg"></i> <span>TÔI ĐÃ CHUYỂN TIỀN - XÁC NHẬN NGAY</span>';
                }
                alert(data.message || 'Không thể cập nhật đơn hàng.');
            }
        })
        .catch(err => {
            console.error(err);
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-circle-check text-lg"></i> <span>TÔI ĐÃ CHUYỂN TIỀN - XÁC NHẬN NGAY</span>';
            }
        });
}

function simulatePaymentSuccess() {
    confirmPaymentPaid();
}
</script>
