<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <div class="text-center space-y-2">
        <span class="bg-primary-500/20 text-cyan-300 border border-primary-500/40 text-xs font-bold px-4 py-1.5 rounded-full inline-flex items-center">
            <i class="fa-solid fa-shield-halved mr-1.5"></i> Tra Cứu Thời Gian Thực
        </span>
        <h2 class="text-2xl sm:text-3xl font-black text-white">
            Tra Cứu Đơn Hàng & Bảo Hành Điện Tử
        </h2>
        <p class="text-xs text-slate-400">Nhập Mã đơn hàng (Ví dụ: TS...) hoặc Số điện thoại đặt hàng để kiểm tra</p>
    </div>

    <!-- Search Box -->
    <div class="glass-card rounded-3xl p-6 sm:p-8">
        <form action="<?= BASE_URL ?>index.php" method="GET">
            <input type="hidden" name="url" value="order/track">
            <div class="flex gap-2">
                <input type="text" name="search" value="<?= htmlspecialchars($keyword) ?>" placeholder="Nhập Số điện thoại hoặc Mã đơn hàng..." required
                    class="w-full text-xs p-3.5 rounded-2xl border border-slate-700 bg-slate-950 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                <button type="submit" class="bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-500 hover:to-indigo-500 text-white text-xs font-bold px-8 py-3.5 rounded-2xl shadow-xl shadow-primary-600/30 transition whitespace-nowrap">
                    <i class="fa-solid fa-magnifying-glass mr-1.5"></i> Tra Cứu
                </button>
            </div>
        </form>
    </div>

    <!-- Search Results -->
    <?php if (!empty($securityNotice)): ?>
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-amber-500/40 bg-amber-950/30 text-amber-200 text-xs flex items-start gap-3.5 shadow-lg">
            <i class="fa-solid fa-user-shield text-amber-400 text-xl shrink-0 mt-0.5"></i>
            <div class="space-y-2">
                <h4 class="font-bold text-amber-300 text-sm">Chính Sách Bảo Mật Quyền Riêng Tư Khách Hàng</h4>
                <p class="leading-relaxed"><?= htmlspecialchars($securityNotice) ?></p>
                <div class="pt-1 flex gap-3">
                    <a href="<?= BASE_URL ?>index.php?url=auth/login" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Đăng Nhập Ngay
                    </a>
                </div>
            </div>
        </div>
    <?php elseif ($searched): ?>
        <?php if (empty($orders)): ?>
            <div class="glass-card rounded-3xl p-12 text-center space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-slate-800/80 flex items-center justify-center mx-auto text-slate-500 text-2xl">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3 class="text-lg font-bold text-white">Không tìm thấy thông tin đơn hàng</h3>
                <p class="text-xs text-slate-400">Vui lòng kiểm tra lại số điện thoại hoặc mã đơn hàng đã nhập chính xác chưa.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Kết quả tìm kiếm (<?= count($orders) ?> đơn hàng):</h3>
                <?php foreach ($orders as $ord): ?>
                    <div class="glass-card rounded-3xl p-6 space-y-4">
                        <div class="flex flex-wrap justify-between items-center pb-3 border-b border-slate-800 gap-2">
                            <div>
                                <span class="text-[10px] text-slate-500 uppercase font-bold block">Mã đơn hàng</span>
                                <strong class="text-cyan-400 font-mono text-base"><?= htmlspecialchars($ord['order_code']) ?></strong>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-500 uppercase font-bold block">Ngày đặt</span>
                                <strong class="text-xs text-slate-300"><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></strong>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-300">
                            <div><span class="text-slate-500">Khách hàng:</span> <strong class="text-white"><?= htmlspecialchars($ord['customer_name']) ?></strong> (<?= htmlspecialchars($ord['customer_phone']) ?>)</div>
                            <div><span class="text-slate-500">Địa chỉ nhận:</span> <?= htmlspecialchars($ord['customer_address']) ?></div>
                            <div>
                                <span class="text-slate-500">Phương thức TT:</span> 
                                <span class="font-bold text-slate-200">
                                    <?= $ord['payment_method'] === 'sepay' ? 'Cổng SePay Trực Tuyến' : ($ord['payment_method'] === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : htmlspecialchars(strtoupper($ord['payment_method']))) ?>
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-slate-500">Trạng thái TT:</span>
                                <?php if ($ord['payment_status'] === 'paid'): ?>
                                    <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 px-2 py-0.5 rounded text-[10px] font-bold">Đã thanh toán</span>
                                <?php elseif ($ord['order_status'] === 'cancelled'): ?>
                                    <span class="bg-rose-500/20 text-rose-300 border border-rose-500/40 px-2 py-0.5 rounded text-[10px] font-bold">Đã hủy đơn</span>
                                <?php elseif ($ord['payment_status'] === 'refunded'): ?>
                                    <span class="bg-purple-500/20 text-purple-300 border border-purple-500/40 px-2 py-0.5 rounded text-[10px] font-bold">Đã hoàn tiền</span>
                                <?php else: ?>
                                    <span class="bg-amber-500/20 text-amber-300 border border-amber-500/40 px-2 py-0.5 rounded text-[10px] font-bold">Chờ thanh toán</span>
                                    <a href="<?= BASE_URL ?>index.php?url=checkout/change-payment-method&code=<?= urlencode($ord['order_code']) ?>&method=sepay" 
                                       class="bg-cyan-500/20 hover:bg-cyan-500/40 text-cyan-300 border border-cyan-500/40 px-2 py-0.5 rounded text-[10px] font-bold transition flex items-center gap-1">
                                        <i class="fa-solid fa-bolt text-amber-300"></i> Thanh toán SePay
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Stepper Hành Trình -->
                        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800">
                            <span class="text-[10px] text-slate-500 uppercase font-bold block mb-3">Lộ trình đơn hàng:</span>
                            <div class="grid grid-cols-4 text-center text-[11px] gap-2">
                                <div class="<?= in_array($ord['order_status'], ['pending', 'confirmed', 'shipping', 'completed']) ? 'text-cyan-400 font-bold' : 'text-slate-600' ?>">
                                    <i class="fa-solid fa-circle-check text-base mb-1 block"></i> Tiếp nhận
                                </div>
                                <div class="<?= in_array($ord['order_status'], ['confirmed', 'shipping', 'completed']) ? 'text-cyan-400 font-bold' : 'text-slate-600' ?>">
                                    <i class="fa-solid fa-box-archive text-base mb-1 block"></i> Đã đóng gói
                                </div>
                                <div class="<?= in_array($ord['order_status'], ['shipping', 'completed']) ? 'text-cyan-400 font-bold' : 'text-slate-600' ?>">
                                    <i class="fa-solid fa-truck-fast text-base mb-1 block"></i> Đang giao
                                </div>
                                <div class="<?= ($ord['order_status'] === 'completed') ? 'text-emerald-400 font-bold' : 'text-slate-600' ?>">
                                    <i class="fa-solid fa-gift text-base mb-1 block"></i> Thành công
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin nếu đơn ĐÃ HỦY -->
                        <?php if ($ord['order_status'] === 'cancelled'): ?>
                            <div class="p-3 bg-rose-950/40 border border-rose-500/30 rounded-2xl text-xs space-y-1 text-rose-300">
                                <div class="flex items-center gap-1.5 font-bold">
                                    <i class="fa-solid fa-ban"></i> Đơn hàng này đã được hủy
                                </div>
                                <?php if (!empty($ord['cancel_reason'])): ?>
                                    <div class="text-[11px] text-slate-300">Lý do: <span class="text-rose-200"><?= htmlspecialchars($ord['cancel_reason']) ?></span></div>
                                <?php endif; ?>
                                <?php if (!empty($ord['refund_info'])): ?>
                                    <div class="text-[11px] text-purple-300 bg-purple-950/40 border border-purple-800/50 p-2 rounded-xl mt-1">
                                        <i class="fa-solid fa-money-bill-transfer mr-1"></i> <strong>Hoàn tiền về:</strong> <?= htmlspecialchars($ord['refund_info']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Thông tin nếu đơn ĐÃ TRẢ HÀNG & HOÀN TIỀN -->
                        <?php if (in_array($ord['order_status'], ['returned', 'return_requested'], true)): ?>
                            <div class="p-3 bg-purple-950/40 border border-purple-500/30 rounded-2xl text-xs space-y-1 text-purple-300">
                                <div class="flex items-center gap-1.5 font-bold text-purple-300">
                                    <i class="fa-solid fa-rotate-left"></i> <?= $ord['order_status'] === 'return_requested' ? 'Đã gửi yêu cầu Trả hàng - Đang chờ cửa hàng duyệt' : 'Đơn hàng đã được Trả hàng & Đang hoàn tiền' ?>
                                </div>
                                <?php if (!empty($ord['cancel_reason'])): ?>
                                    <div class="text-[11px] text-slate-300">Lý do trả hàng: <span class="text-purple-200"><?= htmlspecialchars($ord['cancel_reason']) ?></span></div>
                                <?php endif; ?>
                                <?php if (!empty($ord['refund_info'])): ?>
                                    <div class="text-[11px] text-purple-300 bg-purple-950/40 border border-purple-800/50 p-2 rounded-xl mt-1">
                                        <i class="fa-solid fa-money-bill-transfer mr-1"></i> <strong>Hoàn tiền về:</strong> <?= htmlspecialchars($ord['refund_info']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="flex flex-wrap justify-between items-center gap-3 pt-2">
                            <span class="text-xs text-slate-400">Tổng thanh toán: <strong class="text-cyan-400 font-bold text-sm"><?= number_format($ord['final_amount'], 0, ',', '.') ?>đ</strong></span>
                            <div class="flex items-center gap-2">
                                <?php if (in_array($ord['order_status'], ['pending', 'confirmed'])): ?>
                                    <button type="button" 
                                            onclick="openCancelOrderModal('<?= htmlspecialchars($ord['order_code']) ?>', '<?= htmlspecialchars($ord['payment_status']) ?>', '<?= number_format($ord['final_amount'], 0, ',', '.') ?>đ', '<?= htmlspecialchars($ord['customer_phone'] ?? '') ?>')"
                                            class="bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 text-xs font-bold px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shadow-sm">
                                        <i class="fa-solid fa-xmark text-xs"></i> Hủy Đơn
                                    </button>
                                <?php elseif ($ord['order_status'] === 'shipping'): ?>
                                    <span class="text-[11px] text-amber-300 bg-amber-950/60 border border-amber-800/80 px-2.5 py-1.5 rounded-xl flex items-center gap-1.5" title="Đơn hàng đã bàn giao cho ĐVVC nên không thể hủy">
                                        <i class="fa-solid fa-truck-fast text-xs"></i> Đang giao (Không thể hủy)
                                    </span>
                                    <form action="<?= BASE_URL ?>index.php?url=order/received" method="POST" onsubmit="return confirm('Bạn xác nhận đã nhận được đầy đủ kiện hàng và hài lòng với đơn hàng?');" class="inline-block">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="order_code" value="<?= htmlspecialchars($ord['order_code']) ?>">
                                        <button type="submit" class="bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-extrabold text-xs py-2 px-3.5 rounded-xl shadow-lg shadow-emerald-500/25 transition flex items-center gap-1.5 whitespace-nowrap">
                                            <i class="fa-solid fa-box-open text-xs"></i> Đã Nhận Hàng
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if (!empty($ord['return_eligibility']['can_return'])): ?>
                                    <button type="button" 
                                            onclick="openReturnOrderModal('<?= htmlspecialchars($ord['order_code']) ?>', '<?= number_format($ord['final_amount'], 0, ',', '.') ?>đ', '<?= htmlspecialchars($ord['return_eligibility']['badge_text'] ?? '') ?>', '<?= htmlspecialchars(addslashes($ord['return_eligibility']['message'] ?? '')) ?>', '<?= htmlspecialchars($ord['customer_phone'] ?? '') ?>')"
                                            class="bg-purple-600/20 hover:bg-purple-600 text-purple-300 hover:text-white border border-purple-500/40 text-xs font-bold px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shadow-sm"
                                            title="<?= htmlspecialchars($ord['return_eligibility']['message'] ?? '') ?>">
                                        <i class="fa-solid fa-rotate-left text-xs"></i> Trả Hàng & Hoàn Tiền
                                    </button>
                                <?php endif; ?>

                                <a href="<?= BASE_URL ?>index.php?url=order/success&code=<?= urlencode($ord['order_code']) ?>" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs px-4 py-2 rounded-xl border border-slate-700 transition">
                                    Chi tiết hóa đơn
                                </a>
                            </div>
                        </div>
                                    Chi tiết hóa đơn
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- POPUP MODAL HỦY ĐƠN HÀNG & THÔNG TIN HOÀN TIỀN -->
<div id="cancelOrderModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
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
                            Vui lòng cung cấp tài khoản ngân hàng để shop chuyển tiền hoàn trả lại 100% cho bạn.
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

function openReturnOrderModal(orderCode, finalAmount, badgeText, policyMsg, phone) {
    document.getElementById('returnModalOrderCodeBadge').textContent = '#' + orderCode;
    document.getElementById('returnModalOrderCodeInput').value = orderCode;
    document.getElementById('returnModalCustomerPhoneInput').value = phone || '';
    document.getElementById('returnModalRefundAmount').textContent = finalAmount;
    document.getElementById('returnModalPolicyBadge').textContent = badgeText;
    document.getElementById('returnModalPolicyMsg').textContent = policyMsg;

    document.getElementById('returnOrderModal').classList.remove('hidden');
}

function closeReturnOrderModal() {
    document.getElementById('returnOrderModal').classList.add('hidden');
}

function toggleReturnCustomReason(val) {
    const box = document.getElementById('returnCustomReasonBox');
    if (val === 'other') {
        box.classList.remove('hidden');
        document.getElementById('returnModalCustomReason').required = true;
    } else {
        box.classList.add('hidden');
        document.getElementById('returnModalCustomReason').required = false;
    }
}

// ----------------------------------------------------
// LIVE REALTIME TRACKING POLLING (CLIENT NHẬN BIẾT NGAY KHI ADMIN CẬP NHẬT)
// ----------------------------------------------------
<?php if (!empty($orders)): ?>
const trackingCodes = <?= json_encode(array_column($orders, 'order_code')) ?>;
const trackingStatuses = <?= json_encode(array_combine(array_column($orders, 'order_code'), array_column($orders, 'order_status'))) ?>;

setInterval(async () => {
    for (const code of trackingCodes) {
        try {
            const res = await fetch(`<?= BASE_URL ?>index.php?url=api/orders/status&code=${encodeURIComponent(code)}`);
            if (!res.ok) continue;
            const data = await res.json();
            if (data.success && data.order_status) {
                if (trackingStatuses[code] && trackingStatuses[code] !== data.order_status) {
                    trackingStatuses[code] = data.order_status;
                    const statusNameMap = {
                        'pending': 'Chờ xác nhận',
                        'confirmed': 'Đã xác nhận (Đang đóng gói)',
                        'shipping': 'Đang giao hàng (Đã bàn giao ĐVVC)',
                        'completed': 'Giao hàng thành công',
                        'return_requested': 'Yêu cầu trả hàng đã được gửi',
                        'returned': 'Đã hoàn tất trả hàng & hoàn tiền',
                        'cancelled': 'Đơn hàng đã hủy'
                    };
                    const statusText = statusNameMap[data.order_status] || data.order_status;
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Cập nhật đơn hàng!',
                            text: `Đơn #${code} vừa chuyển sang: "${statusText}"`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                    setTimeout(() => location.reload(), 1500);
                }
            }
        } catch(e) {}
    }
}, 2500);
<?php endif; ?>
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- POPUP MODAL TRẢ HÀNG & HOÀN TIỀN -->
<div id="returnOrderModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400 font-bold text-sm">
                    <i class="fa-solid fa-rotate-left"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>Yêu Cầu Trả Hàng & Hoàn Tiền</span>
                        <span id="returnModalOrderCodeBadge" class="font-mono text-cyan-400 text-xs"></span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Chính sách bảo hành đổi trả & hoàn tiền của NEXVORA</p>
                </div>
            </div>
            <button type="button" onclick="closeReturnOrderModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="<?= BASE_URL ?>index.php?url=order/return" method="POST" class="p-6 space-y-4 text-xs">
            <?= Csrf::field() ?>
            <input type="hidden" name="order_code" id="returnModalOrderCodeInput" value="">
            <input type="hidden" name="customer_phone" id="returnModalCustomerPhoneInput" value="">
            <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '') ?>">

            <!-- Khối huy hiệu & chính sách áp dụng -->
            <div class="p-4 rounded-2xl bg-purple-500/10 border border-purple-500/30 text-purple-200 space-y-2">
                <div class="flex items-center justify-between">
                    <span id="returnModalPolicyBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-500/20 text-purple-300 border border-purple-500/40"></span>
                    <span class="text-xs text-slate-300">Hoàn tiền: <strong id="returnModalRefundAmount" class="text-cyan-400 font-bold text-sm"></strong></span>
                </div>
                <p id="returnModalPolicyMsg" class="text-[11px] text-slate-300 leading-relaxed"></p>
                <div class="text-[10px] text-slate-400 pt-1 border-t border-purple-500/20">
                    <i class="fa-solid fa-circle-info text-cyan-400 mr-1"></i> Khi trả hàng thành công: Hàng hóa sẽ được hoàn lại vào kho và hệ thống tự động trừ khỏi doanh thu.
                </div>
            </div>

            <!-- Khối nhập tài khoản ngân hàng nhận tiền hoàn lại -->
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                <div class="flex items-center gap-1.5 font-bold text-slate-200">
                    <i class="fa-solid fa-money-bill-transfer text-emerald-400"></i>
                    <span>Tài Khoản Nhận Tiền Hoàn Lại: <span class="text-rose-400">*</span></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Tên Ngân Hàng <span class="text-rose-400">*</span></label>
                        <select name="bank_name" id="returnModalBankName" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400" required>
                            <option value="">-- Chọn ngân hàng --</option>
                            <option value="VietinBank">VietinBank (ICB)</option>
                            <option value="Vietcombank">Vietcombank (VCB)</option>
                            <option value="MBBank">MB Bank (Quân Đội)</option>
                            <option value="Techcombank">Techcombank (TCB)</option>
                            <option value="BIDV">BIDV</option>
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
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Số Tài Khoản (STK) <span class="text-rose-400">*</span></label>
                        <input type="text" name="account_number" id="returnModalAccountNumber" placeholder="VD: 101883603231..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-cyan-400" required>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">Tên Chủ Tài Khoản (Viết hoa không dấu) <span class="text-rose-400">*</span></label>
                        <input type="text" name="account_holder" id="returnModalAccountHolder" placeholder="VD: LE VAN SON" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white uppercase focus:outline-none focus:border-cyan-400" required>
                    </div>
                </div>
            </div>

            <!-- Lý do trả hàng -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-300">
                    Vui lòng chọn lý do trả hàng: <span class="text-rose-400">*</span>
                </label>
                <select name="return_reason" id="returnModalReasonSelect" onchange="toggleReturnCustomReason(this.value)" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400" required>
                    <option value="Sản phẩm bị lỗi kỹ thuật từ nhà sản xuất">Sản phẩm bị lỗi kỹ thuật từ nhà sản xuất</option>
                    <option value="Sản phẩm không đúng với mô tả trên website">Sản phẩm không đúng với mô tả trên website</option>
                    <option value="Kiểm tra hàng thấy không ưng ý (Khi xem hàng)">Kiểm tra hàng thấy không ưng ý (Khi xem hàng)</option>
                    <option value="Giao nhầm phiên bản, màu sắc hoặc thiếu phụ kiện">Giao nhầm phiên bản, màu sắc hoặc thiếu phụ kiện</option>
                    <option value="Hàng bị móp méo, trầy xước trong quá trình vận chuyển">Hàng bị móp méo, trầy xước trong quá trình vận chuyển</option>
                    <option value="Đổi ý, không còn nhu cầu sử dụng (Theo chính sách)">Đổi ý, không còn nhu cầu sử dụng (Theo chính sách)</option>
                    <option value="other">Lý do khác...</option>
                </select>

                <div id="returnCustomReasonBox" class="hidden mt-2">
                    <textarea name="custom_reason" id="returnModalCustomReason" rows="2" placeholder="Nhập chi tiết tình trạng sản phẩm hoặc lý do trả hàng..." class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-cyan-400"></textarea>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeReturnOrderModal()" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 text-xs font-semibold transition">
                    Đóng
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-extrabold text-xs shadow-lg shadow-purple-600/30 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Xác Nhận Trả Hàng & Hoàn Tiền</span>
                </button>
            </div>
        </form>
    </div>
</div>
