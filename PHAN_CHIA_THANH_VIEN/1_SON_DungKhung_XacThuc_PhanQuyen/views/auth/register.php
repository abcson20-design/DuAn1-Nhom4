<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6 glass-card rounded-3xl p-8 sm:p-10 border border-slate-700/80 shadow-2xl relative">
        <!-- Nút Quay lại góc trên Card -->
        <div>
            <a href="javascript:if(window.history.length>1){window.history.back()}else{window.location.href='<?= BASE_URL ?>'}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-cyan-400 font-semibold transition group">
                <i class="fa-solid fa-arrow-left text-[11px] group-hover:-translate-x-1 transition-transform"></i>
                <span>Quay lại trang trước</span>
            </a>
        </div>
        <div class="text-center space-y-2">
            <div class="w-14 h-14 bg-gradient-to-tr from-cyan-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-black mx-auto shadow-lg shadow-cyan-500/30">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight">Đăng Ký Tài Khoản User</h2>
            <p class="text-xs text-slate-400">Trở thành thành viên NEXVORA để mua sắm, nhận ưu đãi và bảo hành</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm flex-shrink-0"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Form Đăng ký -->
        <form class="space-y-4" action="<?= BASE_URL ?>index.php?url=auth/register" method="POST" id="registerForm" novalidate>
            <?= Csrf::field() ?>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-semibold text-slate-300">Họ và tên <span class="text-red-400">*</span></label>
                    <span id="fullnameFeedback" class="text-[10px] hidden font-semibold"></span>
                </div>
                <input type="text" name="fullname" id="regFullname" required 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500 transition" 
                       placeholder="Ví dụ: Nguyễn Văn An">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-300">Email <span class="text-red-400">*</span></label>
                        <span id="emailFeedback" class="text-[10px] hidden font-semibold"></span>
                    </div>
                    <input type="email" name="email" id="regEmail" required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500 transition" 
                           placeholder="user@gmail.com">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-300">Số điện thoại <span class="text-red-400">*</span></label>
                        <span id="phoneFeedback" class="text-[10px] hidden font-semibold"></span>
                    </div>
                    <div class="relative">
                        <input type="tel" name="phone" id="regPhone" required 
                               class="w-full px-3.5 py-2.5 pr-8 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500 transition" 
                               placeholder="0988.888.888">
                        <span id="phoneSpinner" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-cyan-400 text-xs hidden">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Địa chỉ nhận hàng mặc định</label>
                <input type="text" name="address" id="regAddress" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500" 
                       placeholder="Số nhà, đường, phường/xã, quận/huyện...">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-300">Mật khẩu <span class="text-red-400">*</span></label>
                        <span id="passFeedback" class="text-[10px] hidden font-semibold"></span>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="regPass" required 
                               class="w-full pl-3.5 pr-9 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500 transition" 
                               placeholder="Từ 6 ký tự, có ký tự đặc biệt (@, #, $...)">
                        <button type="button" onclick="togglePassword('regPass', 'regEyeIcon1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-cyan-400 focus:outline-none transition" title="Hiện/Ẩn mật khẩu">
                            <i class="fa-solid fa-eye text-xs" id="regEyeIcon1"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-300">Nhập lại mật khẩu <span class="text-red-400">*</span></label>
                        <span id="passConfirmFeedback" class="text-[10px] hidden font-semibold"></span>
                    </div>
                    <div class="relative">
                        <input type="password" name="password_confirm" id="regPassConfirm" required 
                               class="w-full pl-3.5 pr-9 py-2.5 rounded-xl border border-slate-700 bg-slate-950 text-slate-100 text-xs focus:outline-none focus:ring-1 focus:ring-cyan-400 placeholder-slate-500 transition" 
                               placeholder="Khớp với mật khẩu">
                        <button type="button" onclick="togglePassword('regPassConfirm', 'regEyeIcon2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-cyan-400 focus:outline-none transition" title="Hiện/Ẩn mật khẩu">
                            <i class="fa-solid fa-eye text-xs" id="regEyeIcon2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Real-time Checklist Mật khẩu -->
            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 text-[11px] space-y-1">
                <div id="checkLength" class="text-slate-400 flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-circle-dot text-[9px]"></i> Tối thiểu 6 ký tự
                </div>
                <div id="checkSpecial" class="text-slate-400 flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-circle-dot text-[9px]"></i> Ít nhất 1 ký tự đặc biệt (@, #, $, !, %, ^, &, *...)
                </div>
                <div id="checkMatch" class="text-slate-400 flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-circle-dot text-[9px]"></i> Mật khẩu xác nhận trùng khớp
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" id="btnSubmitRegister" 
                        class="w-full bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white font-extrabold text-xs py-3 px-4 rounded-xl shadow-lg shadow-cyan-500/25 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-check"></i> Hoàn Tất Đăng Ký Tài Khoản
                </button>
            </div>
        </form>

        <div class="text-center text-xs text-slate-400 pt-2 border-t border-slate-800">
            Đã có tài khoản User? 
            <a href="<?= BASE_URL ?>index.php?url=auth/login" class="text-cyan-400 font-bold hover:underline">Đăng nhập ngay</a>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// =========================================================================
// REAL-TIME INSTANT FORM VALIDATION & CHECK SỐ ĐIỆN THOẠI TRÙNG DATABASE
// =========================================================================
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registerForm');
    const fullnameInput = document.getElementById('regFullname');
    const emailInput = document.getElementById('regEmail');
    const phoneInput = document.getElementById('regPhone');
    const passInput = document.getElementById('regPass');
    const passConfirmInput = document.getElementById('regPassConfirm');
    const btnSubmit = document.getElementById('btnSubmitRegister');

    const fnFeedback = document.getElementById('fullnameFeedback');
    const emFeedback = document.getElementById('emailFeedback');
    const phFeedback = document.getElementById('phoneFeedback');
    const phoneSpinner = document.getElementById('phoneSpinner');
    const passFeedback = document.getElementById('passFeedback');
    const passConfFeedback = document.getElementById('passConfirmFeedback');

    const checkLength = document.getElementById('checkLength');
    const checkSpecial = document.getElementById('checkSpecial');
    const checkMatch = document.getElementById('checkMatch');

    let isPhoneUnique = false;
    let phoneTimer = null;

    // 1. Validate Họ tên
    fullnameInput.addEventListener('input', function () {
        const val = this.value.trim();
        if (val.length < 2) {
            setFeedback(fnFeedback, this, false, 'Tối thiểu 2 ký tự');
        } else {
            setFeedback(fnFeedback, this, true, 'Hợp lệ');
        }
    });

    // 2. Validate Email
    emailInput.addEventListener('input', function () {
        const val = this.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(val)) {
            setFeedback(emFeedback, this, false, 'Email không đúng định dạng');
        } else {
            setFeedback(emFeedback, this, true, 'Email hợp lệ');
        }
    });

    // 3. Validate Số điện thoại & Check trùng qua API
    phoneInput.addEventListener('input', function () {
        clearTimeout(phoneTimer);
        const val = this.value.replace(/[^0-9]/g, '');
        this.value = val;

        const phoneRegex = /^0[35789][0-9]{8}$/;
        if (val.length < 10) {
            isPhoneUnique = false;
            setFeedback(phFeedback, this, false, `Đã nhập ${val.length}/10 số`);
            return;
        }

        if (!phoneRegex.test(val)) {
            isPhoneUnique = false;
            setFeedback(phFeedback, this, false, 'Đầu số phải là 03, 05, 07, 08, 09');
            return;
        }

        // Gọi API check trùng số điện thoại
        phoneSpinner.classList.remove('hidden');
        phFeedback.classList.add('hidden');

        phoneTimer = setTimeout(async () => {
            try {
                const res = await fetch(`<?= BASE_URL ?>index.php?url=api/check-phone&phone=${encodeURIComponent(val)}`);
                const data = await res.json();
                phoneSpinner.classList.add('hidden');

                if (data.exists) {
                    isPhoneUnique = false;
                    setFeedback(phFeedback, phoneInput, false, '❌ SĐT này đã có người đăng ký!');
                } else {
                    isPhoneUnique = true;
                    setFeedback(phFeedback, phoneInput, true, '✅ SĐT hợp lệ và khả dụng');
                }
            } catch (err) {
                phoneSpinner.classList.add('hidden');
                isPhoneUnique = true; // Fallback
            }
        }, 350);
    });

    // 4. Validate Mật khẩu & Nhập lại mật khẩu
    function validatePasswords() {
        const p1 = passInput.value;
        const p2 = passConfirmInput.value;
        const specialRegex = /[@#$%^&*!_\\-]/;

        const hasLen = p1.length >= 6;
        const hasSpec = specialRegex.test(p1);
        const isMatch = p1.length > 0 && p1 === p2;

        updateCheckItem(checkLength, hasLen, 'Tối thiểu 6 ký tự');
        updateCheckItem(checkSpecial, hasSpec, 'Ít nhất 1 ký tự đặc biệt (@, #, $, !, %, ^, &, *...)');
        updateCheckItem(checkMatch, isMatch, 'Mật khẩu xác nhận trùng khớp');

        if (!hasLen || !hasSpec) {
            setFeedback(passFeedback, passInput, false, 'Chưa đủ điều kiện bảo mật');
        } else {
            setFeedback(passFeedback, passInput, true, 'Độ mạnh đạt');
        }

        if (p2.length > 0) {
            if (isMatch) {
                setFeedback(passConfFeedback, passConfirmInput, true, 'Khớp');
            } else {
                setFeedback(passConfFeedback, passConfirmInput, false, 'Chưa khớp');
            }
        }
    }

    passInput.addEventListener('input', validatePasswords);
    passConfirmInput.addEventListener('input', validatePasswords);

    function setFeedback(feedbackEl, inputEl, isValid, msg) {
        feedbackEl.classList.remove('hidden', 'text-emerald-400', 'text-rose-400');
        inputEl.classList.remove('border-emerald-500', 'border-rose-500', 'focus:ring-emerald-400', 'focus:ring-rose-400');

        if (isValid) {
            feedbackEl.classList.add('text-emerald-400');
            feedbackEl.textContent = '✓ ' + msg;
            inputEl.classList.add('border-emerald-500', 'focus:ring-emerald-400');
        } else {
            feedbackEl.classList.add('text-rose-400');
            feedbackEl.textContent = '✕ ' + msg;
            inputEl.classList.add('border-rose-500', 'focus:ring-rose-400');
        }
    }

    function updateCheckItem(el, isValid, text) {
        el.classList.remove('text-slate-400', 'text-emerald-400', 'text-rose-400');
        if (isValid) {
            el.classList.add('text-emerald-400');
            el.innerHTML = `<i class="fa-solid fa-circle-check text-[10px]"></i> ${text}`;
        } else {
            el.classList.add('text-slate-400');
            el.innerHTML = `<i class="fa-solid fa-circle-dot text-[9px]"></i> ${text}`;
        }
    }

    // Chặn submit nếu form chưa hoàn toàn hợp lệ
    form.addEventListener('submit', function (e) {
        const fn = fullnameInput.value.trim();
        const em = emailInput.value.trim();
        const ph = phoneInput.value.trim();
        const p1 = passInput.value;
        const p2 = passConfirmInput.value;

        if (fn.length < 2) {
            e.preventDefault();
            fullnameInput.focus();
            if (window.FormValidator) window.FormValidator.showFieldError(fullnameInput, 'Vui lòng nhập họ và tên hợp lệ (tối thiểu 2 ký tự)!');
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) {
            e.preventDefault();
            emailInput.focus();
            if (window.FormValidator) window.FormValidator.showFieldError(emailInput, 'Vui lòng nhập địa chỉ email hợp lệ!');
            return;
        }

        if (!/^0[35789][0-9]{8}$/.test(ph)) {
            e.preventDefault();
            phoneInput.focus();
            if (window.FormValidator) window.FormValidator.showFieldError(phoneInput, 'Vui lòng nhập số điện thoại hợp lệ (10 chữ số, bắt đầu bằng 03, 05, 07, 08, 09)!');
            return;
        }

        if (!isPhoneUnique) {
            e.preventDefault();
            phoneInput.focus();
            if (window.FormValidator) window.FormValidator.showFieldError(phoneInput, 'Số điện thoại này đã được đăng ký tài khoản. Vui lòng sử dụng số khác hoặc đăng nhập!');
            return;
        }

        if (p1.length < 6 || !/[@#$%^&*!_\-]/.test(p1)) {
            e.preventDefault();
            passInput.focus();
            if (window.FormValidator) window.FormValidator.showFieldError(passInput, 'Mật khẩu phải từ 6 ký tự và có ít nhất 1 ký tự đặc biệt (@, #, $...)!');
            return;
        }

        if (p1 !== p2) {
            e.preventDefault();
            passConfirmInput.focus();
            if (window.FormValidator) window.FormValidator.showFieldError(passConfirmInput, 'Mật khẩu xác nhận không khớp!');
            return;
        }
    });
});
</script>
