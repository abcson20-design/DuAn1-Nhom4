<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="glass-card rounded-3xl p-6 sm:p-10 border border-slate-700/80 space-y-6 text-center">
        <div class="w-20 h-20 bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 rounded-full flex items-center justify-center mx-auto text-3xl shadow-lg shadow-emerald-500/30 animate-bounce">
            <i class="fa-solid fa-check"></i>
        </div>

        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-white">Đặt Hàng Thành Công!</h2>
            <p class="text-xs text-slate-400 mt-1">Cảm ơn bạn đã tin tưởng mua sắm tại NEXVORA. Đơn hàng của bạn đang được xử lý.</p>
        </div>

        <!-- Order Summary Card -->
        <div class="p-6 rounded-2xl bg-slate-950/80 border border-slate-800 text-left space-y-4">
            <div class="grid grid-cols-2 gap-4 text-xs pb-4 border-b border-slate-800">
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Mã Đơn Hàng</span>
                    <strong class="text-cyan-400 font-mono text-sm"><?= htmlspecialchars($order['order_code']) ?></strong>
                </div>
                <div class="text-right">
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Thời gian đặt</span>
                    <strong class="text-slate-300"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></strong>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Phương thức thanh toán</span>
                    <strong class="text-slate-300 uppercase"><?= htmlspecialchars($order['payment_method']) ?></strong>
                </div>
                <div class="text-right">
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Trạng thái thanh toán</span>
                    <?php if ($order['order_status'] === 'cancelled'): ?>
                        <span class="bg-rose-500/20 text-rose-300 border border-rose-500/40 px-2 py-0.5 rounded text-[10px] font-bold">Đã hủy đơn</span>
                    <?php elseif ($order['payment_status'] === 'paid'): ?>
                        <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 px-2 py-0.5 rounded text-[10px] font-bold">Đã thanh toán</span>
                    <?php elseif ($order['payment_status'] === 'refunded'): ?>
                        <span class="bg-purple-500/20 text-purple-300 border border-purple-500/40 px-2 py-0.5 rounded text-[10px] font-bold">Đã hoàn tiền</span>
                    <?php else: ?>
                        <span class="bg-amber-500/20 text-amber-300 border border-amber-500/40 px-2 py-0.5 rounded text-[10px] font-bold">Chờ thanh toán / COD</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Customer & Shipping -->
            <div class="text-xs space-y-1 text-slate-300">
                <div><span class="text-slate-500">Người nhận:</span> <strong class="text-white"><?= htmlspecialchars($order['customer_name']) ?></strong> (<?= htmlspecialchars($order['customer_phone']) ?>)</div>
                <div><span class="text-slate-500">Địa chỉ giao:</span> <?= htmlspecialchars($order['customer_address']) ?></div>
            </div>

            <!-- Items -->
            <div class="pt-3 border-t border-slate-800 space-y-2">
                <span class="text-[10px] text-slate-500 uppercase font-bold block">Sản phẩm đã đặt</span>
                <?php if (!empty($order['items'])): ?>
                    <?php foreach ($order['items'] as $it): ?>
                        <div class="flex justify-between items-center text-xs py-1">
                            <span class="text-slate-300"><?= htmlspecialchars($it['product_name']) ?> <span class="text-slate-500">x<?= $it['quantity'] ?></span></span>
                            <span class="font-bold text-slate-200"><?= number_format($it['total'], 0, ',', '.') ?>đ</span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Total -->
            <div class="pt-3 border-t border-slate-800 flex justify-between items-baseline">
                <span class="text-xs font-bold text-white uppercase">Tổng thanh toán:</span>
                <span class="text-xl font-black text-cyan-400"><?= number_format($order['final_amount'], 0, ',', '.') ?>đ</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3 justify-center pt-2">
            <button type="button" onclick="window.print();" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold px-6 py-3 rounded-xl border border-slate-700 transition">
                <i class="fa-solid fa-print mr-1.5"></i> In Hóa Đơn
            </button>
            <a href="<?= BASE_URL ?>index.php?url=order/track&search=<?= urlencode($order['order_code']) ?>" class="bg-primary-600 hover:bg-primary-500 text-white text-xs font-bold px-6 py-3 rounded-xl transition">
                <i class="fa-solid fa-truck-fast mr-1.5"></i> Theo Dõi Hành Trình
            </a>
            <?php if (!empty($isOwnerOrAdmin) && in_array($order['order_status'], ['pending', 'confirmed'])): ?>
                <button type="button" 
                        onclick="openCancelOrderModal('<?= htmlspecialchars($order['order_code']) ?>', '<?= htmlspecialchars($order['payment_status']) ?>', '<?= number_format($order['final_amount'], 0, ',', '.') ?>đ', '<?= htmlspecialchars($order['customer_phone'] ?? '') ?>')"
                        class="bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 text-xs font-bold px-5 py-3 rounded-xl transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-xmark text-xs"></i> Hủy Đơn Hàng
                </button>
            <?php elseif ($order['order_status'] === 'cancelled'): ?>
                <span class="bg-rose-950/60 text-rose-400 border border-rose-800 px-4 py-2.5 rounded-xl text-xs font-bold inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-ban"></i> Đơn Đã Hủy
                </span>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>" class="bg-slate-900 hover:bg-slate-800 text-slate-300 text-xs font-bold px-6 py-3 rounded-xl border border-slate-800 transition">
                <i class="fa-solid fa-house mr-1.5"></i> Về Trang Chủ
            </a>
        </div>
    </div>
</div>

<!-- POPUP MODAL HỦY ĐƠN HÀNG & THÔNG TIN HOÀN TIỀN -->
<div id="cancelOrderModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4 text-left">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-rose-500/20 border border-rose-500/30 flex items-center justify-center text-rose-400 font-bold text-sm">
                    <i class="fa-solid fa-ban"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>Hủy Đơn Hàng</span>
                        <span id="modalOrderCodeBadge" class="font-mono text-cyan-400 text-xs"></span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Xác nhận hủy đơn và ghi nhận thông tin hoàn tiền</p>
                </div>
            </div>
            <button type="button" onclick="closeCancelOrderModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="<?= BASE_URL ?>index.php?url=order/cancel" method="POST" class="p-6 space-y-4 text-xs">
            <?= Csrf::field() ?>
            <input type="hidden" name="order_code" id="modalOrderCodeInput" value="">
            <input type="hidden" name="customer_phone" id="modalCustomerPhoneInput" value="">
            <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '') ?>">

            <!-- Khối thông báo hoàn tiền nếu đơn ĐÃ THANH TOÁN -->
            <div id="refundNoticeBox" class="hidden p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-200 space-y-3">
                <div class="flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-amber-400 text-base mt-0.5 shrink-0"></i>
                    <div>
                        <strong class="text-white block font-bold">Đơn hàng này ĐÃ THANH TOÁN ONLINE</strong>
                        <span class="text-[11px] text-slate-300">
                            Số tiền thanh toán: <strong id="modalRefundAmount" class="text-cyan-400 font-bold"></strong>. 
                            Vui lòng cung cấp tài khoản ngân hàng để NEXVORA chuyển tiền hoàn lại 100% cho bạn.
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">Tên Ngân Hàng <span class="text-rose-400">*</span></label>
                        <select name="bank_name" id="modalBankName" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                            <option value="">-- Chọn ngân hàng --</option>
                            <option value="Vietcombank">Vietcombank (VCB)</option>
                            <option value="MBBank">MB Bank (Quân Đội)</option>
                            <option value="Techcombank">Techcombank (TCB)</option>
                            <option value="BIDV">BIDV</option>
                            <option value="VietinBank">VietinBank</option>
                            <option value="ACB">ACB</option>
                            <option value="VPBank">VPBank</option>
                            <option value="TPBank">TPBank</option>
                            <option value="Agribank">Agribank</option>
                            <option value="HDBank">HDBank</option>
                            <option value="Sacombank">Sacombank</option>
                            <option value="MoMo">Ví MoMo</option>
                            <option value="Khác">Ngân hàng khác</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">Số Tài Khoản (STK) <span class="text-rose-400">*</span></label>
                        <input type="text" name="account_number" id="modalAccountNumber" placeholder="VD: 0987654321..." class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-400">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">Tên Chủ Tài Khoản (Viết hoa không dấu) <span class="text-rose-400">*</span></label>
                        <input type="text" name="account_holder" id="modalAccountHolder" placeholder="VD: NGUYEN VAN A" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white uppercase focus:outline-none focus:border-cyan-400">
                    </div>
                </div>
            </div>

            <!-- Lý do hủy đơn -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-300">
                    Vui lòng chọn lý do hủy đơn: <span class="text-rose-400">*</span>
                </label>
                <select name="cancel_reason" id="modalCancelReasonSelect" onchange="toggleCustomReason(this.value)" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400" required>
                    <option value="Đổi ý, không còn nhu cầu mua nữa">Đổi ý, không còn nhu cầu mua nữa</option>
                    <option value="Muốn thay đổi sản phẩm, phiên bản màu hoặc dung lượng">Muốn thay đổi sản phẩm, phiên bản màu hoặc dung lượng</option>
                    <option value="Thời gian giao hàng lâu hoặc không phù hợp">Thời gian giao hàng lâu hoặc không phù hợp</option>
                    <option value="Đặt nhầm thông tin địa chỉ hoặc số điện thoại">Đặt nhầm thông tin địa chỉ hoặc số điện thoại</option>
                    <option value="Tìm thấy giá rẻ hơn ở cửa hàng khác">Tìm thấy giá rẻ hơn ở cửa hàng khác</option>
                    <option value="other">Lý do khác...</option>
                </select>

                <div id="customReasonBox" class="hidden mt-2">
                    <textarea name="custom_reason" id="modalCustomReason" rows="2" placeholder="Nhập lý do chi tiết của bạn..." class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-cyan-400"></textarea>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeCancelOrderModal()" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 text-xs font-semibold transition">
                    Đóng
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-extrabold text-xs shadow-lg shadow-rose-600/30 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Xác Nhận Hủy Đơn</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCancelOrderModal(orderCode, paymentStatus, finalAmount, phone) {
    document.getElementById('modalOrderCodeBadge').textContent = '#' + orderCode;
    document.getElementById('modalOrderCodeInput').value = orderCode;
    document.getElementById('modalCustomerPhoneInput').value = phone || '';

    const refundBox = document.getElementById('refundNoticeBox');
    const bankName = document.getElementById('modalBankName');
    const accNum = document.getElementById('modalAccountNumber');
    const accHolder = document.getElementById('modalAccountHolder');

    if (paymentStatus === 'paid') {
        refundBox.classList.remove('hidden');
        document.getElementById('modalRefundAmount').textContent = finalAmount;
        bankName.required = true;
        accNum.required = true;
        accHolder.required = true;
    } else {
        refundBox.classList.add('hidden');
        bankName.required = false;
        accNum.required = false;
        accHolder.required = false;
    }

    document.getElementById('cancelOrderModal').classList.remove('hidden');
}

function closeCancelOrderModal() {
    document.getElementById('cancelOrderModal').classList.add('hidden');
}

function toggleCustomReason(val) {
    const box = document.getElementById('customReasonBox');
    if (val === 'other') {
        box.classList.remove('hidden');
        document.getElementById('modalCustomReason').required = true;
    } else {
        box.classList.add('hidden');
        document.getElementById('modalCustomReason').required = false;
    }
}
</script>
    </div>
</div>
