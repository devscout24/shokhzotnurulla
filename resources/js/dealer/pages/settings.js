(function () {
    'use strict';

    function getCsrf() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.content : '';
    }

    function clearErrors() {
        ['errOldPassword', 'errPassword'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) { el.textContent = ''; el.classList.add('d-none'); }
        });
        document.querySelectorAll('#password-form .form-control').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
    }

    function showFieldError(id, msg) {
        var el = document.getElementById(id);
        if (!el) return;
        el.textContent = msg;
        el.classList.remove('d-none');
    }

    var form = document.getElementById('password-form');
    var btn  = document.getElementById('btnSavePassword');

    if (!form || !btn) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();

        var origHtml  = btn.innerHTML;
        btn.disabled  = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:14px;height:14px;border-width:2px;"></span>';

        var data = new FormData(form);
        data.append('_method', 'PATCH');

        fetch(form.dataset.url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN':     getCsrf(),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
            },
            body: data,
        })
        .then(function (res) {
            return res.json().then(function (d) { return { ok: res.ok, status: res.status, data: d }; });
        })
        .then(function (result) {
            if (result.ok && result.data.success) {
                toastr.success(result.data.message || 'Password updated successfully.', 'Success');
                // Clear fields on success
                document.getElementById('oldPassword').value    = '';
                document.getElementById('newPassword').value    = '';
                document.getElementById('confirmPassword').value = '';
            } else if (result.status === 422 && result.data.errors) {
                var errors = result.data.errors;
                if (errors.old_password) showFieldError('errOldPassword', errors.old_password[0]);
                if (errors.password)     showFieldError('errPassword',    errors.password[0]);
                if (!errors.old_password && !errors.password) {
                    toastr.error(result.data.message || 'Something went wrong.', 'Error');
                }
            } else {
                toastr.error(result.data.message || 'Something went wrong.', 'Error');
            }
        })
        .catch(function () {
            toastr.error('Network error. Please try again.', 'Error');
        })
        .finally(function () {
            btn.disabled  = false;
            btn.innerHTML = origHtml;
        });
    });

})();