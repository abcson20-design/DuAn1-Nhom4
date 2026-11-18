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
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Chức Năng Dành Cho Thành Viên',
                            html: '<div class="text-left text-xs text-slate-300 space-y-2">' +
                                  '<p>• <b>Khách</b>: Chỉ được xem giao diện sản phẩm.</p>' +
                                  '<p>• ' + (data.message || 'Quý khách vui lòng Đăng nhập tài khoản User để sử dụng giỏ hàng và đặt hàng!') + '</p>' +
                                  '</div>',
                            icon: 'info',
                            background: '#0f172a',
                            color: '#f8fafc',
                            showCancelButton: true,
                            confirmButtonText: '<i class="fa-solid fa-right-to-bracket mr-1"></i> Đăng Nhập Ngay',
                            cancelButtonText: 'Đóng',
                            confirmButtonColor: '#6366f1',
                            cancelButtonColor: '#334155'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = data.login_url || 'index.php?url=auth/login&msg=login_required_cart';
                            }
                        });
                    } else {
                        alert(data.message);
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
        // Nếu click đúng nút Xem chi tiết -> cho phép chuyển hướng bình thường tới trang chi tiết sản phẩm
        if (detailBtn) {
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
