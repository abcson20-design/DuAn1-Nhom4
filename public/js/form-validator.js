/**
 * NEXVORA - Universal Real-Time Form Validator
 * Tự động kiểm tra và thông báo lỗi định dạng ngay khi người dùng điền xong ô (blur)
 * và cập nhật trực tiếp theo thời gian thực (input) bên dưới ô nhập bằng dòng chữ đỏ.
 * Áp dụng đồng bộ cho TẤT CẢ các form: THÊM, SỬA, ĐĂNG KÝ, ĐĂNG NHẬP ở cả Client & Admin.
 */

(function () {
    'use strict';

    // Khởi tạo hệ thống Validator
    function initFormValidator() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => setupFormValidation(form));

        // Hỗ trợ cả các modal hoặc form được thêm động vào DOM sau này
        const observer = new MutationObserver((mutations) => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) {
                        if (node.tagName === 'FORM') {
                            setupFormValidation(node);
                        } else if (node.querySelectorAll) {
                            const innerForms = node.querySelectorAll('form');
                            innerForms.forEach(f => setupFormValidation(f));
                            const dynamicInputs = node.querySelectorAll('input, select, textarea');
                            dynamicInputs.forEach(input => attachFieldEvents(input));
                        }
                    }
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    /**
     * Thiết lập validation cho một form
     */
    function setupFormValidation(form) {
        if (form.dataset.formValidatorAttached === 'true') return;
        form.dataset.formValidatorAttached = 'true';

        // Tắt validation mặc định của browser để hiển thị giao diện thông báo màu đỏ thống nhất của dự án
        form.setAttribute('novalidate', 'true');

        const fields = getFormFields(form);
        fields.forEach(field => attachFieldEvents(field));

        // Khi người dùng bấm nút Submit / Gửi form
        form.addEventListener('submit', function (e) {
            let firstInvalidField = null;
            let hasError = false;

            const currentFields = getFormFields(form);
            currentFields.forEach(field => {
                const error = validateField(field);
                if (error) {
                    showFieldError(field, error);
                    hasError = true;
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                } else {
                    clearFieldError(field);
                }
            });

            if (hasError) {
                e.preventDefault();
                e.stopPropagation();
                if (firstInvalidField) {
                    firstInvalidField.focus();
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
        });
    }

    /**
     * Lấy tất cả các trường có thể nhập liệu trong form
     */
    function getFormFields(form) {
        return Array.from(form.querySelectorAll('input, select, textarea')).filter(field => {
            const type = (field.type || '').toLowerCase();
            return !['hidden', 'submit', 'button', 'reset', 'image'].includes(type) && !field.disabled;
        });
    }

    /**
     * Gắn sự kiện kiểm tra cho từng trường nhập liệu
     */
    function attachFieldEvents(field) {
        if (field.dataset.fieldValidatorAttached === 'true') return;
        field.dataset.fieldValidatorAttached = 'true';

        // 1. Khi người dùng nhập xong và rời khỏi ô (blur) -> Kiểm tra và hiển thị ngay
        field.addEventListener('blur', function () {
            field.dataset.touched = 'true';
            const error = validateField(field);
            if (error) {
                showFieldError(field, error);
            } else {
                clearFieldError(field);
            }
        });

        // 2. Khi người dùng đang gõ (input/change):
        // Cập nhật tức thì nếu ô đã touched, đang có lỗi, hoặc giá trị nhập vi phạm ngưỡng rõ rệt (như % > 99)
        const liveEvent = ['checkbox', 'radio', 'select-one', 'select-multiple', 'file', 'date', 'datetime-local'].includes(field.type) ? 'change' : 'input';
        
        field.addEventListener(liveEvent, function () {
            const error = validateField(field);
            if (error) {
                const valNum = Number(field.value);
                const isOverLimit = !isNaN(valNum) && (valNum > 99 || valNum < 0);
                if (field.dataset.touched === 'true' || field.classList.contains('is-invalid') || field.dataset.hasError === 'true' || isOverLimit) {
                    showFieldError(field, error);
                }
            } else {
                clearFieldError(field);
            }
        });

        // Nếu là mật khẩu hoặc xác nhận mật khẩu, liên kết kiểm tra chéo
        if (field.type === 'password') {
            field.addEventListener('input', function () {
                const form = field.closest('form');
                if (!form) return;
                const confirmField = form.querySelector('input[name*="confirm"], input[name*="re_password"], #confirmPass, #regPassConfirm, #resetConfirmPass');
                if (confirmField && confirmField !== field && confirmField.dataset.touched === 'true') {
                    const cErr = validateField(confirmField);
                    if (cErr) showFieldError(confirmField, cErr);
                    else clearFieldError(confirmField);
                }
            });
        }
    }

    /**
     * Hàm kiểm tra tính hợp lệ của từng trường dữ liệu
     * Trả về thông báo lỗi (string) hoặc null nếu hợp lệ
     */
    function validateField(field) {
        // Bỏ qua nếu field bị ẩn hoặc disable
        if (field.disabled || field.offsetParent === null && field.type !== 'file') {
            return null;
        }

        const rawValue = field.value || '';
        const value = rawValue.trim();
        const type = (field.type || '').toLowerCase();
        const name = (field.name || '').toLowerCase();
        const id = (field.id || '').toLowerCase();
        const isRequired = field.hasAttribute('required') || field.required || field.dataset.required === 'true';

        // Lấy tên nhãn hiển thị thân thiện
        const labelText = getFieldLabel(field);

        // 1. KIỂM TRA BỎ TRỐNG TRƯỜNG BẮT BUỘC (REQUIRED)
        if (isRequired) {
            if (type === 'checkbox') {
                if (!field.checked) {
                    return `Vui lòng chọn hoặc đồng ý với ${labelText}!`;
                }
            } else if (type === 'file') {
                if (!field.files || field.files.length === 0) {
                    // Nếu là form sửa có ảnh cũ sẵn thì không bắt buộc
                    const hasExistingImg = field.closest('form')?.querySelector('img[src*="uploads"], img[src*="products"], .existing-image');
                    if (!hasExistingImg && !field.dataset.optionalIfEdit) {
                        return `Vui lòng chọn tệp ảnh cho ${labelText}!`;
                    }
                }
            } else if (value === '') {
                if (name.includes('phone') || id.includes('phone')) {
                    return 'Vui lòng nhập số điện thoại!';
                }
                if (name.includes('email') || id.includes('email') || type === 'email') {
                    return 'Vui lòng nhập địa chỉ email!';
                }
                if (type === 'password') {
                    return 'Vui lòng nhập mật khẩu!';
                }
                if (name.includes('name') || name === 'customer_name') {
                    return 'Vui lòng nhập họ và tên!';
                }
                if (name.includes('address') || name === 'customer_address') {
                    return 'Vui lòng nhập địa chỉ giao nhận hàng!';
                }
                if (field.tagName === 'SELECT') {
                    return `Vui lòng chọn ${labelText}!`;
                }
                return `Vui lòng không để trống ô ${labelText}!`;
            }
        }

        // Nếu không bắt buộc và đang để trống thì coi như hợp lệ
        if (value === '') {
            return null;
        }

        // 2. KIỂM TRA ĐỊNH DẠNG EMAIL
        if (type === 'email' || name.includes('email') || id.includes('email')) {
            const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(value)) {
                return 'Email không đúng định dạng (Ví dụ: name@example.com)!';
            }
        }

        // 3. KIỂM TRA ĐỊNH DẠNG SỐ ĐIỆN THOẠI VIỆT NAM (10 SỐ)
        if (type === 'tel' || name.includes('phone') || id.includes('phone') || name === 'customer_phone') {
            const cleanPhone = value.replace(/[\s.-]/g, '');
            // Chấp nhận đầu số 03, 05, 07, 08, 09 với đúng 10 số (hoặc +84)
            const phoneRegex = /^(0|\+84)(3|5|7|8|9)[0-9]{8}$/;
            if (!phoneRegex.test(cleanPhone)) {
                return 'Số điện thoại không hợp lệ (Cần 10 chữ số, bắt đầu bằng 03, 05, 07, 08, 09)!';
            }
        }

        // 4. KIỂM TRA MẬT KHẨU & XÁC NHẬN MẬT KHẨU
        if (type === 'password') {
            const isConfirmField = name.includes('confirm') || name.includes('re_') || id.includes('confirm');
            if (isConfirmField) {
                const form = field.closest('form');
                if (form) {
                    const originalPassField = form.querySelector('input[type="password"]:not([name*="confirm"]):not([name*="re_"]):not([id*="confirm"])');
                    if (originalPassField && originalPassField.value !== value) {
                        return 'Mật khẩu xác nhận không trùng khớp!';
                    }
                }
            } else {
                if (value.length < 6) {
                    return 'Mật khẩu phải có tối thiểu 6 ký tự!';
                }
            }
        }

        // 5. KIỂM TRA MỨC GIẢM GIÁ PHẦN TRĂM (%)
        const isPercentField = (function() {
            if (name === 'discount_value' || id === 'discountValue' || name.includes('percent')) {
                const form = field.closest('form');
                if (form) {
                    const typeEl = form.querySelector('select[name="discount_type"], #discountType, input[name="discount_type"]:checked');
                    if (typeEl && typeEl.value === 'percent') return true;
                }
                const lbl = getFieldLabel(field).toLowerCase();
                if (lbl.includes('%') || lbl.includes('phần trăm')) return true;
            }
            return false;
        })();

        if (isPercentField) {
            const num = Number(value);
            if (isNaN(num)) {
                return 'Mức giảm phần trăm phải là số!';
            }
            if (num < 1) {
                return 'Phần trăm giảm giá tối thiểu là 1%!';
            }
            if (num > 99) {
                return `Phần trăm giảm giá không được vượt quá 99% (Bạn đang nhập ${num}%)!`;
            }
        }

        // 6. KIỂM TRA SỐ (NUMBER / PRICE / QUANTITY / TIỀN)
        if (type === 'number' || field.inputMode === 'numeric' || name.includes('price') || name.includes('quantity') || name.includes('amount')) {
            const num = Number(value);
            if (isNaN(num)) {
                return 'Vui lòng chỉ nhập số hợp lệ!';
            }
            if (num < 0) {
                return 'Giá trị không được là số âm (phải ≥ 0)!';
            }
            if (field.min !== '' && num < Number(field.min)) {
                return `Giá trị tối thiểu cho phép là ${Number(field.min).toLocaleString('vi-VN')}!`;
            }
            if (field.max !== '' && num > Number(field.max)) {
                return `Giá trị tối đa cho phép là ${Number(field.max).toLocaleString('vi-VN')}!`;
            }
            if (name.includes('quantity') || id.includes('quantity')) {
                if (!Number.isInteger(num) || num < 1) {
                    return 'Số lượng phải là số nguyên dương từ 1 trở lên!';
                }
            }
            if ((name === 'price' || id === 'price') && !field.readOnly) {
                if (num <= 0) {
                    return 'Giá sản phẩm phải lớn hơn 0đ!';
                }
            }
        }

        // 6. KIỂM TRA ĐỘ DÀI TỐI THIỂU (MINLENGTH) & TỐI ĐA (MAXLENGTH)
        if (field.minLength && field.minLength > 0 && value.length < field.minLength) {
            return `Cần nhập tối thiểu ${field.minLength} ký tự (Hiện tại: ${value.length})!`;
        }
        if (field.maxLength && field.maxLength > 0 && value.length > field.maxLength) {
            return `Vượt quá giới hạn tối đa ${field.maxLength} ký tự!`;
        }

        // 7. KIỂM TRA NGÀY THÁNG (DATE / DATETIME)
        if (name.includes('end_date') || id.includes('end_date')) {
            const form = field.closest('form');
            const startDateField = form?.querySelector('input[name*="start_date"], #start_date');
            if (startDateField && startDateField.value) {
                const start = new Date(startDateField.value);
                const end = new Date(value);
                if (end <= start) {
                    return 'Ngày kết thúc phải lớn hơn ngày bắt đầu!';
                }
            }
        }

        // 8. KIỂM TRA ĐƯỜNG DẪN SLUG HOẶC MÃ CODE
        if (name === 'slug') {
            if (!/^[a-z0-9-]+$/.test(value)) {
                return 'Slug chỉ chứa chữ thường, số và dấu gạch nối (không dấu tiếng Việt, không khoảng trắng)!';
            }
        }
        if (name === 'code' && field.closest('form')?.action?.includes('coupon')) {
            if (!/^[A-Z0-9_-]+$/i.test(value)) {
                return 'Mã voucher chỉ chứa chữ cái, số và dấu gạch nối (không chứa khoảng trắng)!';
            }
        }

        // 9. KIỂM TRA BIỂU THỨC CHÍNH QUY (PATTERN) NẾU CÓ
        if (field.pattern) {
            try {
                const regex = new RegExp(`^(?:${field.pattern})$`);
                if (!regex.test(value)) {
                    return field.title || `Định dạng ${labelText} chưa hợp lệ!`;
                }
            } catch (e) {}
        }

        return null;
    }

    /**
     * Hiển thị dòng chữ đỏ báo lỗi trực tiếp bên dưới ô nhập
     */
    function showFieldError(field, errorMessage) {
        field.dataset.hasError = 'true';
        field.classList.add('is-invalid', '!border-red-500', 'border-red-500', 'focus:!border-red-500', 'focus:!ring-red-500');
        field.classList.remove('border-slate-700', 'border-slate-800', 'focus:ring-cyan-400');
        field.style.borderColor = '#ef4444';

        const anchor = getErrorAnchor(field);
        let errorEl = getExistingErrorElement(anchor);

        if (!errorEl) {
            errorEl = document.createElement('div');
            errorEl.className = 'form-validator-error text-red-400 text-[11px] sm:text-xs mt-1.5 flex items-center gap-1.5 font-medium transition-all duration-200 animate-fadeIn';
            errorEl.setAttribute('role', 'alert');
            anchor.parentNode.insertBefore(errorEl, anchor.nextSibling);
        }

        errorEl.innerHTML = `<i class="fa-solid fa-circle-exclamation text-[11px] shrink-0 text-red-500"></i> <span>${errorMessage}</span>`;
        errorEl.style.display = 'flex';
    }

    /**
     * Xóa dòng chữ đỏ báo lỗi và hoàn trả viền bình thường cho ô nhập
     */
    function clearFieldError(field) {
        field.dataset.hasError = 'false';
        field.classList.remove('is-invalid', '!border-red-500', 'border-red-500', 'focus:!border-red-500', 'focus:!ring-red-500');
        field.style.borderColor = '';

        const anchor = getErrorAnchor(field);
        const errorEl = getExistingErrorElement(anchor);
        if (errorEl) {
            errorEl.remove();
        }
    }

    /**
     * Xác định phần tử mỏ neo để chèn dòng thông báo lỗi phía dưới
     * (Tránh phá vỡ bố cục nếu ô nhập nằm trong khung relative chứa nút ẩn/hiện mật khẩu hoặc icon)
     */
    function getErrorAnchor(field) {
        // Nếu là radio hoặc checkbox
        if (field.type === 'radio' || field.type === 'checkbox') {
            const groupContainer = field.closest('.grid, .space-y-2, .space-y-3, .flex-wrap');
            if (groupContainer && groupContainer.contains(field)) {
                return groupContainer;
            }
            const labelParent = field.closest('label');
            if (labelParent) return labelParent;
        }

        const parent = field.parentElement;
        if (parent) {
            // Nếu cha là khung relative hoặc flex chứa các nút bấm bổ trợ (như nút mắt toggle password)
            if (parent.classList.contains('relative') || parent.classList.contains('input-group')) {
                if (parent.querySelector('button, .absolute, [onclick*="togglePassword"], [onclick*="togglePass"]')) {
                    return parent;
                }
            }
        }
        return field;
    }

    /**
     * Tìm phần tử lỗi đã tồn tại ngay sau anchor
     */
    function getExistingErrorElement(anchor) {
        let sibling = anchor.nextElementSibling;
        while (sibling) {
            if (sibling.classList && sibling.classList.contains('form-validator-error')) {
                return sibling;
            }
            break;
        }
        return null;
    }

    /**
     * Lấy tên nhãn (label) của trường để báo lỗi một cách tự nhiên, dễ hiểu
     */
    function getFieldLabel(field) {
        // 1. Tìm thẻ label trong form có attribute for="field.id"
        if (field.id) {
            const label = document.querySelector(`label[for="${field.id}"]`);
            if (label) {
                return cleanLabelText(label.textContent);
            }
        }
        // 2. Tìm label gần nhất phía trên field
        let prev = field.closest('div')?.querySelector('label');
        if (prev) {
            return cleanLabelText(prev.textContent);
        }
        // 3. Dựa vào placeholder hoặc name
        if (field.placeholder) {
            return field.placeholder.replace(/[.]{3}|[*]/g, '').trim();
        }
        return field.name || 'thông tin này';
    }

    function cleanLabelText(text) {
        if (!text) return 'thông tin';
        return text.replace(/[*:]/g, '').trim();
    }

    // Tự động chạy khi DOM sẵn sàng
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFormValidator);
    } else {
        initFormValidator();
    }

    // Export ra window để có thể gọi kích hoạt thủ công nếu cần
    window.FormValidator = {
        validateField: validateField,
        showFieldError: showFieldError,
        clearFieldError: clearFieldError,
        reinit: initFormValidator
    };
})();
