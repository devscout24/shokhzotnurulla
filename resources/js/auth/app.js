document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Date Range Picker
    if (document.getElementById('dateRange')) {
        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: ["2026-02-01", "2026-02-27"]
        });
    }

    // 2. Mobile Sidebar Toggle
    const mobileToggle = document.getElementById('mobileToggle');
    const sidebar = document.getElementById('sidebar');

    if (mobileToggle && sidebar) {
        mobileToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }

    // 3. Login Form
    const form = document.getElementById('loginForm');
    if (form) {
        const submitBtn = form.querySelector('.btn-login');

        form.addEventListener('submit', async (e) => {

            if (!submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Signing in...';
            }

        });
    }
});

function validateCredentials(email, password) {
    const errors = [];
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email) errors.push('Email is required.');
    else if (!emailRegex.test(email)) errors.push('Enter a valid email address.');

    if (!password) errors.push('Password is required.');
    else if (password.length < 6) errors.push('Password must be at least 6 characters.');

    return errors;
}

function showAlert(html, type = 'info') {
    clearAlerts();
    const wrapper = document.createElement('div');
    wrapper.className = `alert alert-${type} alert-dismissible fade show`;
    wrapper.role = 'alert';
    wrapper.style.cssText = 'position:fixed;top:18px;right:18px;z-index:1200;min-width:260px;';
    wrapper.innerHTML = `${html}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
    document.body.appendChild(wrapper);
    setTimeout(() => { wrapper.remove(); }, 5000);
}

function clearAlerts(){ document.querySelectorAll('.alert[role="alert"]').forEach(n => n.remove()); }

function fakeLogin(email, password){
    return new Promise((resolve) => {
        setTimeout(() => {
            if (email === 'admin@example.com' && password === 'password123') resolve({ success: true });
            else resolve({ success: false, message: 'Invalid email or password. Use admin@example.com / password123' });
        }, 900);
    });
}