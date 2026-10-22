// Experiment 04 - JavaScript Client-Side Validation
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('studentForm');
    const studentId = document.getElementById('studentId');
    const studentName = document.getElementById('studentName');
    const studentEmail = document.getElementById('studentEmail');
    const studentPassword = document.getElementById('studentPassword');
    const successBox = document.getElementById('formSuccessMessage');

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        let isValid = true;

        // Reset errors
        document.querySelectorAll('.error').forEach(el => el.textContent = '');
        successBox.style.display = 'none';

        // 1. Validate Student ID (9 digits)
        const idRegex = /^\d{9}$/;
        if (!idRegex.test(studentId.value.trim())) {
            document.getElementById('idError').textContent = 'Registration ID must be exactly 9 digits.';
            isValid = false;
        }

        // 2. Validate Name (alphabetical only, min 3 chars)
        const nameRegex = /^[A-Za-z\s]{3,}$/;
        if (!nameRegex.test(studentName.value.trim())) {
            document.getElementById('nameError').textContent = 'Name must be at least 3 characters and contain letters only.';
            isValid = false;
        }

        // 3. Validate Email Format
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailRegex.test(studentEmail.value.trim())) {
            document.getElementById('emailError').textContent = 'Please enter a valid email address.';
            isValid = false;
        }

        // 4. Validate Password Strength (Min 8 chars, 1 uppercase, 1 number)
        const pwd = studentPassword.value;
        const pwdRegex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
        if (!pwdRegex.test(pwd)) {
            document.getElementById('passwordError').textContent = 'Password must have at least 8 chars, 1 uppercase letter, and 1 digit.';
            isValid = false;
        }

        if (isValid) {
            successBox.style.display = 'block';
            console.log('Client validation passed successfully:', {
                studentId: studentId.value.trim(),
                name: studentName.value.trim(),
                email: studentEmail.value.trim()
            });
        }
    });
});
