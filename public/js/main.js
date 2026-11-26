/**
 * TechStore - Frontend JavaScript & APIs
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. AJAX Add to Cart
    const addToCartForms = document.querySelectorAll('.ajax-add-to-cart');
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang thêm...';

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;

                if (data.require_login) {
                    const productId = formData.get('product_id') || '';
                    const color = formData.get('color') || '';
                    const quantity = formData.get('quantity') || '1';

                    if (window.Swal) {
                        Swal.fire({
                            title: '⚡ Mua Hàng & Giữ Chỗ Nhanh',
                            html: '<div class="text-left text-xs text-slate-300 space-y-3">' +
                                  '<p class="text-slate-400">Bạn đang duyệt với tư cách Khách. Bạn có thể <b>Đặt giữ chỗ ngay</b> (CSKH sẽ liên hệ giao tận nơi) hoặc <b>Đăng nhập</b> để quản lý đơn:</p>' +
                                  '<div class="grid grid-cols-1 gap-2 pt-1">' +
                                  '<button type="button" id="btnSwalGuestHold" class="w-full bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white font-bold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-lg"><i class="fa-solid fa-bolt"></i> ⚡ Đặt Giữ Chỗ Nhanh (Không cần đăng nhập)</button>' +
                                  '<button type="button" id="btnSwalLogin" class="w-full bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2"><i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập Mua Trực Tiếp</button>' +
                                  '</div>' +
                                  '</div>',
                            icon: 'info',
                            background: '#0f172a',
                            color: '#f8fafc',
                            showConfirmButton: false,
                            showCancelButton: true,
                            cancelButtonText: 'Đóng',
                            cancelButtonColor: '#334155',
                            didOpen: () => {
                                document.getElementById('btnSwalLogin').onclick = () => {
                                    window.location.href = data.login_url || 'index.php?url=auth/login&msg=login_required_cart';
                                };
                                document.getElementById('btnSwalGuestHold').onclick = () => {
                                    Swal.close();
                                    openGuestHoldModal(productId, color, quantity);
                                };
                            }
                        });
                    } else {
                        window.location.href = data.login_url || 'index.php?url=auth/login&msg=login_required_cart';
                    }
                    return;
                }

                if (data.success) {
                    // Update badge count
                    const badge = document.querySelector('.cart-badge');
                    if (badge) {
                        badge.textContent = data.cartCount;
                        badge.classList.remove('hidden');
                        badge.classList.remove('d-none');
                    }

                    // Show toast notification
                    if (window.Swal) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message,
                            background: '#0f172a',
                            color: '#f8fafc',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                console.error(err);
            });
        });
    });

    // 2. Sao chép nhanh (Copy to clipboard)
    window.copyText = function (elementId, message) {
        const text = document.getElementById(elementId).innerText.trim();
        navigator.clipboard.writeText(text).then(() => {
            if (window.Swal) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: message || 'Đã sao chép vào bộ nhớ tạm!',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                alert('Đã sao chép: ' + text);
            }
        });
    };

    // 3. Audio Demo Simulator (Dành cho trang chi tiết tai nghe)
    window.playAudioDemo = function (type) {
        const audioVisualizer = document.getElementById('audioVisualizer');
        const demoStatus = document.getElementById('demoStatus');
        if (!audioVisualizer) return;

        let soundDesc = '';
        if (type === 'bass') soundDesc = 'Đang phát: Bass Boost (Dải âm trầm uy lực 40Hz - 100Hz)';
        else if (type === 'vocal') soundDesc = 'Đang phát: Vocal / Acoustic (Âm trung trong trẻo, tách bạch)';
        else if (type === 'anc') soundDesc = 'Đang mô phỏng: Khử tiếng ồn chủ động ANC (Triệt tiêu tạp âm 43dB)';

        if (demoStatus) {
            demoStatus.innerHTML = `<span class="badge bg-success me-2">Đang phát</span> ${soundDesc}`;
        }

        audioVisualizer.classList.add('playing');
        setTimeout(() => {
            audioVisualizer.classList.remove('playing');
            if (demoStatus) {
                demoStatus.innerHTML = `<span class="badge bg-secondary me-2">Tạm dừng</span> Bấm nút để thử lại đoạn âm thanh`;
            }
        }, 6000);
    };

    // 4. Nhấn vào ảnh trên thẻ card sẽ hiện nút xem chi tiết
    document.addEventListener('click', function (e) {
        const detailBtn = e.target.closest('.btn-detail-link');
        const wishlistBtn = e.target.closest('.btn-wishlist-toggle');
        // Nếu click đúng nút Xem chi tiết hoặc nút Yêu thích -> không kích hoạt mở overlay ảnh
        if (detailBtn || wishlistBtn) {
            return;
        }

        const thumbBox = e.target.closest('.product-thumb-box');
        if (thumbBox) {
            // Khi nhấn vào ảnh sản phẩm trên thẻ card
            const overlay = thumbBox.querySelector('.product-detail-overlay');
            const isAlreadyActive = overlay ? overlay.classList.contains('active') : false;

            // Đóng tất cả overlay trên các thẻ card khác
            document.querySelectorAll('.product-detail-overlay.active').forEach(ov => {
                ov.classList.remove('active');
            });

            // Nếu overlay này chưa mở, mở nó ra để hiện nút xem chi tiết
            if (overlay && !isAlreadyActive) {
                overlay.classList.add('active');
            }
        } else {
            // Nhấn ra ngoài vùng ảnh -> đóng tất cả overlay đang mở
            document.querySelectorAll('.product-detail-overlay.active').forEach(ov => {
                ov.classList.remove('active');
            });
        }
    });
});

/**
 * 5. Global Wishlist Toggle Handler: Dùng chung cho thẻ card sản phẩm ở tất cả các trang
 */
window.toggleWishlist = function (productId, btnElement, e) {
    if (e && e.stopPropagation) {
        e.stopPropagation();
        e.preventDefault();
    } else if (window.event) {
        window.event.stopPropagation();
        window.event.preventDefault();
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content 
                   || document.querySelector('input[name="_csrf_token"]')?.value 
                   || '';
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('_csrf_token', csrfToken);

    let targetUrl = 'index.php?url=wishlist/toggle';
    if (window.BASE_URL) {
        try {
            targetUrl = new URL('index.php?url=wishlist/toggle', encodeURI(window.BASE_URL)).href;
        } catch (err) {
            targetUrl = encodeURI(window.BASE_URL) + 'index.php?url=wishlist/toggle';
        }
    }

    fetch(targetUrl, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.require_login) {
            // Khách chưa đăng nhập: chuyển hướng thẳng tới trang đăng nhập (giống luồng nút Mua ngay)
            const redirectUrl = data.login_url || ((window.BASE_URL ? window.BASE_URL : '') + 'index.php?url=auth/login&msg=login_required_wishlist');
            window.location.href = redirectUrl;
            return;
        }

        if (data.success) {
            // Cập nhật tất cả các nút wishlist của sản phẩm này trên trang
            const allWishlistBtns = document.querySelectorAll(`[data-wishlist-pid="${productId}"]`);
            allWishlistBtns.forEach(btn => {
                const icon = btn.querySelector('i');
                if (data.is_wishlisted) {
                    btn.classList.add('bg-rose-500/20', 'border-rose-500/50', 'text-rose-400');
                    btn.classList.remove('bg-slate-900/80', 'border-slate-700/80', 'text-slate-400');
                    if (icon) {
                        icon.className = 'fa-solid fa-heart text-xs text-rose-500 scale-125 transition-transform duration-200';
                        setTimeout(() => icon.classList.remove('scale-125'), 300);
                    }
                    btn.setAttribute('title', 'Bỏ thích sản phẩm này');
                } else {
                    btn.classList.remove('bg-rose-500/20', 'border-rose-500/50', 'text-rose-400');
                    btn.classList.add('bg-slate-900/80', 'border-slate-700/80', 'text-slate-400');
                    if (icon) {
                        icon.className = 'fa-regular fa-heart text-xs text-slate-300 group-hover/wishlist:text-white transition-transform duration-200';
                    }
                    btn.setAttribute('title', 'Thêm vào danh sách yêu thích');
                }
            });

            // Nếu đang ở trang chi tiết sản phẩm và có nút #btnWishlistDetail
            const detailBtn = document.getElementById('btnWishlistDetail');
            if (detailBtn && detailBtn.getAttribute('data-product-id') == productId) {
                if (data.is_wishlisted) {
                    detailBtn.className = 'bg-rose-500 hover:bg-rose-600 text-white border border-rose-400 px-4 py-3.5 rounded-2xl transition flex items-center justify-center shrink-0 shadow-lg shadow-rose-500/25';
                    detailBtn.setAttribute('title', 'Bỏ thích sản phẩm này');
                } else {
                    detailBtn.className = 'bg-slate-800 hover:bg-rose-500/20 text-rose-400 border border-slate-700 hover:border-rose-500/50 px-4 py-3.5 rounded-2xl transition flex items-center justify-center shrink-0';
                    detailBtn.setAttribute('title', 'Thêm vào danh sách yêu thích');
                }
            }

            // Nếu đang ở trang Danh sách yêu thích (Wishlist page) mà bấm xóa -> ẩn card mượt mà
            const card = document.getElementById('wishlist-card-' + productId);
            if (card && !data.is_wishlisted) {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                card.style.transition = 'all 0.3s ease';
                setTimeout(() => {
                    card.remove();
                    if (data.count === 0) {
                        location.reload();
                    }
                }, 300);
            }

            // Cập nhật số lượng trên badge ở Header
            const badge = document.getElementById('wishlist-badge-count');
            if (badge) {
                badge.textContent = data.count || 0;
                if (data.count > 0) {
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            // Thông báo Toast thành công
            if (window.Swal) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: data.is_wishlisted ? 'success' : 'info',
                    title: data.message,
                    background: '#0f172a',
                    color: '#f8fafc',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            }
        } else {
            if (window.Swal) {
                Swal.fire({
                    title: 'Thông báo',
                    text: data.message || 'Có lỗi xảy ra',
                    icon: 'warning',
                    background: '#0f172a',
                    color: '#f8fafc'
                });
            } else {
                alert(data.message || 'Có lỗi xảy ra');
            }
        }
    })
    .catch(err => console.error(err));
};

// =========================================================================
// MODAL ĐẶT GIỮ CHỖ NHANH DÀNH CHO KHÁCH CHƯA ĐĂNG NHẬP (QUÉT IP CHỐNG GHIM HÀNG)
// =========================================================================
window.openGuestHoldModal = function (productId, color, quantity) {
    if (!window.Swal) return;

    Swal.fire({
        title: '⚡ ĐẶT GIỮ CHỖ NHANH',
        html: `
            <div class="text-left text-xs text-slate-300 space-y-3 pt-2">
                <p class="text-slate-400 text-[11px] leading-relaxed">
                    Không cần đăng ký tài khoản! Điền thông tin bên dưới, nhân viên CSKH NEXVORA sẽ liên hệ ngay để chốt đơn và giao hàng.
                </p>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Họ và tên của bạn <span class="text-red-400">*</span></label>
                    <input type="text" id="ghName" class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-950 text-white text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500" placeholder="Nguyễn Văn A">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Số điện thoại nhận hàng <span class="text-red-400">*</span></label>
                    <input type="tel" id="ghPhone" class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-950 text-white text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500" placeholder="0988.888.888">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Địa chỉ nhận hàng chi tiết <span class="text-red-400">*</span></label>
                    <textarea id="ghAddress" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-950 text-white text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500" placeholder="Số nhà, tên đường, phường/xã, quận/huyện..."></textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Ghi chú (Tùy chọn)</label>
                    <input type="text" id="ghNotes" class="w-full px-3 py-2 rounded-xl border border-slate-700 bg-slate-950 text-white text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500" placeholder="Giao giờ hành chính, gọi trước...">
                </div>
                <div class="text-[10px] text-amber-400/90 bg-amber-950/30 p-2 rounded-lg border border-amber-500/20 flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved shrink-0"></i>
                    <span>Hệ thống quét IP tự động nhằm phòng tránh ghim hàng ảo (Tối đa 3 lần/ngày/IP).</span>
                </div>
            </div>
        `,
        background: '#0f172a',
        color: '#f8fafc',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-paper-plane mr-1"></i> Gửi Yêu Cầu Giữ Chỗ',
        cancelButtonText: 'Hủy bỏ',
        confirmButtonColor: '#06b6d4',
        cancelButtonColor: '#334155',
        focusConfirm: false,
        preConfirm: () => {
            const name = document.getElementById('ghName').value.trim();
            const phone = document.getElementById('ghPhone').value.trim();
            const address = document.getElementById('ghAddress').value.trim();
            const notes = document.getElementById('ghNotes').value.trim();

            if (!name) {
                Swal.showValidationMessage('Vui lòng nhập họ và tên của bạn');
                return false;
            }
            const cleanPhone = phone.replace(/[^0-9]/g, '');
            if (!/^(0[35789])[0-9]{8}$/.test(cleanPhone)) {
                Swal.showValidationMessage('Số điện thoại không hợp lệ (cần 10 chữ số)');
                return false;
            }
            if (!address) {
                Swal.showValidationMessage('Vui lòng nhập địa chỉ nhận hàng');
                return false;
            }

            return { name, phone: cleanPhone, address, notes };
        }
    }).then(async (result) => {
        if (result.isConfirmed && result.value) {
            Swal.fire({
                title: 'Đang gửi thông tin...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
                background: '#0f172a',
                color: '#fff'
            });

            try {
                const fd = new FormData();
                fd.append('product_id', productId);
                fd.append('color', color || '');
                fd.append('quantity', quantity || '1');
                fd.append('customer_name', result.value.name);
                fd.append('customer_phone', result.value.phone);
                fd.append('customer_address', result.value.address);
                fd.append('notes', result.value.notes);

                const res = await fetch((window.BASE_URL ? window.BASE_URL : '') + 'index.php?url=api/cart/guest-hold', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Giữ Chỗ Thành Công!',
                        text: data.message,
                        confirmButtonText: 'Đã Hiểu',
                        confirmButtonColor: '#06b6d4',
                        background: '#0f172a',
                        color: '#fff'
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Không thể giữ chỗ',
                        text: data.message,
                        confirmButtonText: 'Đóng',
                        confirmButtonColor: '#f43f5e',
                        background: '#0f172a',
                        color: '#fff'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi kết nối',
                    text: 'Không thể kết nối máy chủ, vui lòng thử lại sau!',
                    background: '#0f172a',
                    color: '#fff'
                });
            }
        }
    });
};
