document.addEventListener('DOMContentLoaded', function () {
    // Sidebar toggle for mobile
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('sidebarClose');

    function openSidebar() {
        if (sidebar) sidebar.classList.add('show');
        if (overlay) overlay.classList.add('show');
    }
    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
    }

    if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // Confirm delete with SweetAlert2
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const message = this.getAttribute('data-confirm') || 'Are you sure?';
            const href = this.getAttribute('href');
            const form = this.closest('form');
            Swal.fire({
                title: 'Confirm',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, proceed'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (href) window.location.href = href;
                    else if (form) form.submit();
                }
            });
        });
    });

    // Auto-dismiss alerts
    setTimeout(function () {
        document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        });
    }, 5000);

    // Pakistani phone validation helper
    window.validatePakPhone = function (phone) {
        return /^03[0-9]{9}$/.test(phone);
    };

    // CNIC validation
    window.validateCNIC = function (cnic) {
        return /^[0-9]{5}-[0-9]{7}-[0-9]$/.test(cnic);
    };

    // Hospital Register Form Validation (Safe Check)
    const hospitalForm = document.getElementById('hospitalRegisterForm');
    if (hospitalForm) {
        hospitalForm.addEventListener('submit', function(e) {
            const phoneInput = document.querySelector('input[name="phone"]').value;

            if (!window.validatePakPhone(phoneInput)) {
                e.preventDefault();
                alert('Phone must be a valid Pakistani number (03XXXXXXXXX)');
                return false;
            }
        });
    }
});