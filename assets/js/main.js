// ==========================================================
// SIMATS Central Digital Library - Client-side Interactions
// ==========================================================

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Navigation Toggle
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    }

    // 2. Client-side Form Validation
    const forms = document.querySelectorAll('form[data-validate="true"]');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            let valid = true;
            const requiredInputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            
            requiredInputs.forEach(input => {
                const existingErr = input.parentNode.querySelector('.form-error-msg');
                if (existingErr) existingErr.remove();

                if (!input.value.trim()) {
                    valid = false;
                    input.classList.add('input-error');
                    const err = document.createElement('span');
                    err.className = 'form-error-msg';
                    err.style.color = '#ef4444';
                    err.style.fontSize = '0.78rem';
                    err.style.display = 'block';
                    err.style.marginTop = '0.25rem';
                    err.textContent = 'This field is required.';
                    input.parentNode.appendChild(err);
                } else {
                    input.classList.remove('input-error');
                }
            });

            // Email validation check
            const emailInput = form.querySelector('input[type="email"]');
            if (emailInput && emailInput.value.trim()) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value.trim())) {
                    valid = false;
                    emailInput.classList.add('input-error');
                    const err = document.createElement('span');
                    err.className = 'form-error-msg';
                    err.style.color = '#ef4444';
                    err.style.fontSize = '0.78rem';
                    err.style.display = 'block';
                    err.style.marginTop = '0.25rem';
                    err.textContent = 'Please enter a valid email address.';
                    emailInput.parentNode.appendChild(err);
                }
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    });

    // 3. Real-time Filter for Tables
    const filterInput = document.getElementById('tableFilterInput');
    if (filterInput) {
        filterInput.addEventListener('keyup', () => {
            const query = filterInput.value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    // 4. Auto-dismiss Flash Alerts after 5 seconds
    const alertBox = document.getElementById('globalAlert');
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.opacity = '0';
            alertBox.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alertBox.remove(), 500);
        }, 5000);
    }
});
