<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thùng Rác Đơn Hàng - NEXVORA Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-b from-[#0b172e] via-[#0f2347] to-[#0a162c] text-slate-100 min-h-screen flex flex-col relative overflow-x-hidden selection:bg-primary-600 selection:text-white">
    <?php require_once __DIR__ . '/partials/bg_effects.php'; ?>
    
    <!-- Admin Navbar -->
    <header class="bg-slate-900 border-b border-slate-800 px-6 py-3 sticky top-0 z-40">
        <div class="flex items-center justify-between max-w-[1550px] mx-auto">
            <div class="flex items-center space-x-3">
                <img src="<?= BASE_URL ?>public/images/logo.png" alt="NEXVORA Logo" class="h-9 w-auto object-contain drop-shadow-[0_0_12px_rgba(6,182,212,0.45)]">
                <span class="text-base font-black tracking-tight text-white">NEX<span class="text-cyan-400">VORA</span> <span class="text-xs bg-cyan-950 text-cyan-300 border border-cyan-800 px-2 py-0.5 rounded-md ml-2 font-mono">Admin</span></span>
            </div>
            <div class="flex items-center space-x-4 text-xs">
                <a href="<?= BASE_URL ?>" target="_blank" class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-3.5 py-1.5 rounded-xl border border-slate-700 transition flex items-center">
                    <i class="fa-solid fa-eye mr-1.5"></i> Xem Website
                </a>
                <a href="<?= BASE_URL ?>index.php?url=admin/logout" class="bg-red-500/20 hover:bg-red-500 text-red-300 hover:text-white px-3 py-1.5 rounded-xl border border-red-500/30 transition">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> Thoát
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-[1550px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-1 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
            <!-- Sidebar Navigation -->
            <?php 
                $activeNav = 'orders';
                require_once __DIR__ . '/partials/sidebar.php'; 
            ?>

            <main class="lg:col-span-4 space-y-6">
                <!-- Flash Notification Success -->
                <?php if (!empty($_SESSION['flash_order_msg'])): ?>
                    <div id="flashMsgAlert" class="bg-emerald-950/80 border border-emerald-500/50 text-emerald-300 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-semibold shadow-lg shadow-emerald-500/10">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
                            <span><?= htmlspecialchars($_SESSION['flash_order_msg']) ?></span>
                        </div>
                        <button type="button" onclick="document.getElementById('flashMsgAlert').remove()" class="text-emerald-400 hover:text-white transition p-1">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                    <?php unset($_SESSION['flash_order_msg']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['flash_order_err'])): ?>
                    <div id="flashErrAlert" class="bg-rose-950/90 border border-rose-500/60 text-rose-200 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-semibold shadow-lg shadow-rose-500/10">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-exclamation text-rose-400 text-base"></i>
                            <span><?= htmlspecialchars($_SESSION['flash_order_err']) ?></span>
                        </div>
                        <button type="button" onclick="document.getElementById('flashErrAlert').remove()" class="text-rose-400 hover:text-white transition p-1">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                    <?php unset($_SESSION['flash_order_err']); ?>
                <?php endif; ?>

                <!-- Header & Nút Quay lại -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                            <i class="fa-solid fa-trash-can text-amber-400"></i>
                            <span>Thùng Rác Đơn Hàng</span>
                            <span class="text-xs bg-amber-950 text-amber-300 border border-amber-800/80 px-2.5 py-0.5 rounded-full font-mono">
                                <?= count($trashOrders) ?> đơn đã xóa tạm
                            </span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">
                            Các đơn hàng đã chuyển vào Thùng rác. Số liệu báo cáo & doanh thu lịch sử vẫn được bảo toàn nguyên vẹn.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="<?= BASE_URL ?>index.php?url=admin/orders" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold py-2.5 px-4 rounded-xl border border-slate-700 transition flex items-center gap-2 shadow-sm">
                            <i class="fa-solid fa-arrow-left"></i> Quay Lại Quản Lý Đơn
                        </a>
                    </div>
                </div>

                <!-- TABS ĐIỀU HƯỚNG ĐỒNG BỘ -->
                <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
                    <a href="<?= BASE_URL ?>index.php?url=admin/orders" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-900 text-slate-400 hover:text-white border border-slate-800">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Tất Cả Đơn Hàng</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-slate-800 text-slate-400"><?= $countAll ?? 0 ?></span>
                    </a>

                    <a href="<?= BASE_URL ?>index.php?url=admin/orders-trash" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-md shadow-amber-600/30">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>🗑️ Thùng Rác</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-white/20 text-white"><?= count($trashOrders) ?></span>
                    </a>
                </div>

                <!-- BẢNG DANH SÁCH ĐƠN HÀNG TRONG THÙNG RÁC -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[10px] text-slate-400 uppercase tracking-wider border-b border-slate-800/80">
                                    <th class="py-3 px-4 whitespace-nowrap">Mã Đơn</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Khách Hàng</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Tổng Tiền</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Phương Thức</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Trạng Thái Khi Xóa</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Thời Gian Xóa</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                <?php if (empty($trashOrders)): ?>
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-slate-500">
                                            <i class="fa-solid fa-box-open text-3xl mb-2 text-slate-600 block"></i>
                                            Thùng rác đơn hàng đang trống.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($trashOrders as $ord): ?>
                                        <tr class="hover:bg-slate-800/40 transition">
                                            <td class="py-3.5 px-4 font-mono font-bold text-cyan-400 whitespace-nowrap">
                                                <?= htmlspecialchars($ord['order_code']) ?><br>
                                                <span class="text-[10px] text-slate-500 font-normal"><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></span>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-bold text-white"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                                <div class="text-[10px] text-slate-500"><?= htmlspecialchars($ord['customer_phone']) ?></div>
                                            </td>
                                            <td class="py-3.5 px-4 font-black text-rose-400 whitespace-nowrap">
                                                <?= number_format($ord['final_amount'], 0, ',', '.') ?>đ
                                            </td>
                                            <td class="py-3.5 px-4 uppercase text-[10px] font-bold whitespace-nowrap">
                                                <?= htmlspecialchars($ord['payment_method']) ?>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <?php
                                                    $stMap = [
                                                        'pending' => ['bg' => 'bg-amber-500/20 text-amber-300 border-amber-500/40', 'text' => 'Chờ duyệt'],
                                                        'confirmed' => ['bg' => 'bg-blue-500/20 text-blue-300 border-blue-500/40', 'text' => 'Đã duyệt'],
                                                        'shipping' => ['bg' => 'bg-purple-500/20 text-purple-300 border-purple-500/40', 'text' => 'Đang giao'],
                                                        'completed' => ['bg' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40', 'text' => 'Hoàn thành'],
                                                        'cancelled' => ['bg' => 'bg-rose-500/20 text-rose-300 border-rose-500/40', 'text' => 'Đã hủy'],
                                                    ];
                                                    $curSt = $stMap[$ord['order_status']] ?? $stMap['pending'];
                                                ?>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border <?= $curSt['bg'] ?>">
                                                    <?= $curSt['text'] ?>
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap text-slate-400 text-[11px]">
                                                <?= !empty($ord['deleted_at']) ? date('d/m/Y H:i', strtotime($ord['deleted_at'])) : '---' ?>
                                            </td>
                                            <td class="py-3.5 px-4 text-right whitespace-nowrap space-x-1.5">
                                                <!-- Nút Xem Chi Tiết -->
                                                <button type="button" 
                                                        onclick='openOrderDetailModal(<?= json_encode($ord, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>)'
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-cyan-300 border border-slate-700 text-[11px] font-bold transition" 
                                                        title="Xem chi tiết đơn hàng">
                                                    <i class="fa-solid fa-eye text-xs"></i> <span>Chi tiết</span>
                                                </button>

                                                <!-- Nút Khôi Phục -->
                                                <form action="<?= BASE_URL ?>index.php?url=admin/order-restore" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn khôi phục đơn hàng #<?= htmlspecialchars($ord['order_code']) ?> trở lại danh sách quản lý?');">
                                                    <?= Csrf::field() ?>
                                                    <input type="hidden" name="id" value="<?= $ord['id'] ?>">
                                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 text-[11px] font-bold transition">
                                                        <i class="fa-solid fa-rotate-left text-xs"></i> <span>Khôi phục</span>
                                                    </button>
                                                </form>

                                                <!-- Nút Xóa Vĩnh Viễn -->
                                                <form action="<?= BASE_URL ?>index.php?url=admin/order-force-delete" method="POST" class="inline-block" onsubmit="return confirm('CẢNH BÁO: Thao tác này sẽ xóa vĩnh viễn đơn hàng #<?= htmlspecialchars($ord['order_code']) ?> cùng toàn bộ mặt hàng liên quan khỏi cơ sở dữ liệu và không thể lấy lại. Bạn chắc chắn muốn xóa?');">
                                                    <?= Csrf::field() ?>
                                                    <input type="hidden" name="id" value="<?= $ord['id'] ?>">
                                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 text-[11px] font-bold transition">
                                                        <i class="fa-solid fa-trash-can text-xs"></i> <span>Xóa hẳn</span>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- POPUP MODAL XEM CHI TIẾT ĐƠN HÀNG (DÙNG CHUNG) -->
    <div id="orderDetailModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-2xl max-h-[90vh] overflow-hidden shadow-2xl flex flex-col animate-in fade-in zoom-in duration-200 text-left">
            <div class="p-5 border-b border-slate-800 flex items-center justify-between shrink-0 bg-slate-950/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-cyan-500/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 font-bold">
                        <i class="fa-solid fa-file-invoice text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                            <span>Chi Tiết Đơn Hàng</span>
                            <span id="modalOrderCode" class="font-mono text-cyan-400"></span>
                        </h3>
                        <p id="modalOrderDate" class="text-[11px] text-slate-400"></p>
                    </div>
                </div>
                <button type="button" onclick="closeOrderDetailModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-5 text-xs">
                <!-- Thông tin khách hàng & Giao nhận -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 rounded-2xl bg-slate-950/70 border border-slate-800">
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase font-bold block mb-1">Người nhận hàng</span>
                        <div id="modalCustomerName" class="font-bold text-white text-sm"></div>
                        <div id="modalCustomerPhone" class="text-slate-400 font-mono mt-0.5"></div>
                        <div id="modalCustomerEmail" class="text-slate-400 text-[11px]"></div>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase font-bold block mb-1">Địa chỉ nhận hàng</span>
                        <div id="modalCustomerAddress" class="text-slate-200 leading-relaxed"></div>
                    </div>
                </div>

                <!-- Lý do hủy & Hoàn tiền nếu có -->
                <div id="modalCancelInfoBox" class="hidden p-4 rounded-2xl bg-rose-950/40 border border-rose-500/40 space-y-2 text-rose-300">
                    <div class="flex items-center gap-2 font-bold text-xs text-rose-400">
                        <i class="fa-solid fa-circle-exclamation text-sm"></i>
                        <span>Đơn Hàng Đã Bị Hủy</span>
                    </div>
                    <div id="modalCancelReason" class="text-[11px] text-slate-300"></div>
                    <div id="modalRefundInfo" class="hidden text-[11px] text-purple-300 bg-purple-950/60 p-2.5 rounded-xl border border-purple-800/60 font-mono"></div>
                </div>

                <!-- Danh sách sản phẩm mua -->
                <div class="space-y-2">
                    <span class="text-[11px] text-slate-400 uppercase font-bold block">Danh Sách Mặt Hàng</span>
                    <div id="modalItemsList" class="divide-y divide-slate-800 border border-slate-800 rounded-2xl overflow-hidden bg-slate-950/50">
                        <!-- Rendered by JS -->
                    </div>
                </div>

                <!-- Tổng tiền -->
                <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-2">
                    <div class="flex justify-between text-slate-400">
                        <span>Phương thức thanh toán:</span>
                        <span id="modalPaymentMethod" class="text-white font-bold uppercase"></span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Trạng thái thanh toán:</span>
                        <span id="modalPaymentStatus" class="font-bold"></span>
                    </div>
                    <div class="flex justify-between text-slate-400 pt-2 border-t border-slate-800">
                        <span>Tổng tiền thanh toán:</span>
                        <span id="modalFinalAmount" class="text-base font-black text-rose-400"></span>
                    </div>
                </div>

                <div id="modalNotesBox" class="hidden p-3 rounded-xl bg-slate-950/50 border border-slate-800/80 text-[11px] text-slate-400">
                    <span class="font-bold text-slate-300 block mb-0.5">Ghi chú:</span>
                    <span id="modalNotes"></span>
                </div>
            </div>

            <div class="p-4 border-t border-slate-800 bg-slate-950/60 flex items-center justify-end">
                <button type="button" onclick="closeOrderDetailModal()" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs transition">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <script>
    function openOrderDetailModal(order) {
        if (!order) return;
        document.getElementById('modalOrderCode').textContent = '#' + (order.order_code || '');
        document.getElementById('modalOrderDate').textContent = 'Đặt lúc: ' + (order.created_at || '');
        document.getElementById('modalCustomerName').textContent = order.customer_name || 'Khách vãng lai';
        document.getElementById('modalCustomerPhone').textContent = order.customer_phone || '---';
        document.getElementById('modalCustomerEmail').textContent = order.customer_email || '';
        document.getElementById('modalCustomerAddress').textContent = order.customer_address || '---';

        // Lý do hủy & Hoàn tiền
        const cancelBox = document.getElementById('modalCancelInfoBox');
        const reasonEl = document.getElementById('modalCancelReason');
        const refundEl = document.getElementById('modalRefundInfo');
        if (order.order_status === 'cancelled') {
            cancelBox.classList.remove('hidden');
            reasonEl.textContent = 'Lý do: ' + (order.cancel_reason || 'Khách yêu cầu hủy đơn');
            if (order.refund_info) {
                refundEl.classList.remove('hidden');
                refundEl.textContent = 'Hoàn tiền về: ' + order.refund_info;
            } else {
                refundEl.classList.add('hidden');
            }
        } else {
            cancelBox.classList.add('hidden');
        }

        // Danh sách items
        const itemsList = document.getElementById('modalItemsList');
        itemsList.innerHTML = '';
        const items = order.items || [];
        if (items.length === 0) {
            itemsList.innerHTML = '<div class="p-4 text-center text-slate-500">Không có dữ liệu mặt hàng.</div>';
        } else {
            items.forEach(it => {
                const img = it.product_image || 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=100';
                const total = parseInt(it.total || 0).toLocaleString('vi-VN') + 'đ';
                const price = parseInt(it.price || 0).toLocaleString('vi-VN') + 'đ';
                itemsList.innerHTML += `
                    <div class="p-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <img src="${img}" class="w-12 h-12 object-contain rounded-xl bg-slate-900 border border-slate-800 p-1 shrink-0">
                            <div>
                                <div class="font-bold text-white text-xs">${it.product_name || 'Sản phẩm'}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">SL: <strong class="text-slate-200">${it.quantity || 1}</strong> × ${price}</div>
                            </div>
                        </div>
                        <div class="font-extrabold text-cyan-400 text-xs whitespace-nowrap">${total}</div>
                    </div>
                `;
            });
        }

        document.getElementById('modalPaymentMethod').textContent = (order.payment_method === 'vietqr' ? 'VietQR 24/7' : (order.payment_method === 'bank_transfer' ? 'Chuyển khoản' : (order.payment_method === 'sepay' ? 'Cổng SePay' : 'COD Khi Nhận Hàng')));
        
        const payStatusEl = document.getElementById('modalPaymentStatus');
        if (order.order_status === 'cancelled') {
            if (order.payment_status === 'paid') {
                payStatusEl.className = 'font-bold text-rose-400';
                payStatusEl.textContent = 'Đã hủy (Cần hoàn tiền)';
            } else if (order.payment_status === 'refunded') {
                payStatusEl.className = 'font-bold text-purple-400';
                payStatusEl.textContent = 'Đã hủy (Đã hoàn tiền)';
            } else {
                payStatusEl.className = 'font-bold text-slate-400';
                payStatusEl.textContent = 'Đã hủy (Không thu tiền)';
            }
        } else if (order.payment_status === 'paid') {
            payStatusEl.className = 'font-bold text-emerald-400';
            payStatusEl.textContent = 'Đã thanh toán';
        } else if (order.payment_status === 'refunded') {
            payStatusEl.className = 'font-bold text-purple-400';
            payStatusEl.textContent = 'Đã hoàn tiền';
        } else {
            payStatusEl.className = 'font-bold text-amber-400';
            payStatusEl.textContent = 'Chờ thanh toán';
        }

        document.getElementById('modalFinalAmount').textContent = parseInt(order.final_amount || 0).toLocaleString('vi-VN') + 'đ';

        const notesBox = document.getElementById('modalNotesBox');
        if (order.notes) {
            notesBox.classList.remove('hidden');
            document.getElementById('modalNotes').textContent = order.notes;
        } else {
            notesBox.classList.add('hidden');
        }

        document.getElementById('orderDetailModal').classList.remove('hidden');
    }

    function closeOrderDetailModal() {
        document.getElementById('orderDetailModal').classList.add('hidden');
    }

    window.addEventListener('click', function(e) {
        const m = document.getElementById('orderDetailModal');
        if (e.target === m) closeOrderDetailModal();
    });
    </script>
</body>
</html>
