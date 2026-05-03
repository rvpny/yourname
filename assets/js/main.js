// Register page validation
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.querySelector('.form-side form');
    const namaInput = document.getElementById('nama_lengkap');
    const emailInput = document.getElementById('email');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    const registerBtn = document.getElementById('registerBtn');

    function checkRegisterInputs() {
        const isNamaValid = namaInput.value.trim() !== '';
        const isEmailValid = validateEmail(emailInput.value.trim());
        const isUsernameValid = usernameInput.value.trim() !== '';
        const isPasswordValid = passwordInput.value.trim() !== '';
        const isConfirmValid = confirmInput.value.trim() !== '' && 
                             confirmInput.value.trim() === passwordInput.value.trim();

        if (isNamaValid && isEmailValid && isUsernameValid && isPasswordValid && isConfirmValid) {
            registerBtn.disabled = false;
            registerBtn.style.opacity = '1';
            registerBtn.style.cursor = 'pointer';
        } else {
            registerBtn.disabled = true;
            registerBtn.style.opacity = '0.7';
            registerBtn.style.cursor = 'not-allowed';
        }
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Event listeners untuk semua input
    [namaInput, emailInput, usernameInput, passwordInput, confirmInput].forEach(input => {
        input.addEventListener('input', checkRegisterInputs);
    });

    // Validasi email saat kehilangan fokus
    emailInput.addEventListener('blur', function() {
        if (!validateEmail(emailInput.value.trim())) {
            showError(emailInput, 'Format email tidak valid');
        } else {
            clearError(emailInput);
        }
        checkRegisterInputs();
    });

    // Validasi konfirmasi password saat kehilangan fokus
    confirmInput.addEventListener('blur', function() {
        if (confirmInput.value.trim() !== passwordInput.value.trim()) {
            showError(confirmInput, 'Konfirmasi password tidak cocok');
        } else {
            clearError(confirmInput);
        }
        checkRegisterInputs();
    });

    // Fungsi bantuan untuk menampilkan error
    function showError(input, message) {
        clearError(input); // Hapus error yang ada terlebih dahulu
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'input-error';
        errorDiv.textContent = message;
        errorDiv.style.color = '#ff4444';
        errorDiv.style.fontSize = '0.8rem';
        errorDiv.style.marginTop = '5px';
        
        const inputGroup = input.parentElement;
        inputGroup.appendChild(errorDiv);
        inputGroup.classList.add('has-error');
    }
    
    function clearError(input) {
        const inputGroup = input.parentElement;
        const errorDiv = inputGroup.querySelector('.input-error');
        if (errorDiv) {
            inputGroup.removeChild(errorDiv);
        }
        inputGroup.classList.remove('has-error');
    }

    // Cek awal saat halaman dimuat
    checkRegisterInputs();

    // Handle form submission
    registerForm.addEventListener('submit', function(e) {
        let isValid = true;

        // Validasi nama
        if (namaInput.value.trim() === '') {
            showError(namaInput, 'Nama lengkap wajib diisi');
            isValid = false;
        }

        // Validasi email
        if (emailInput.value.trim() === '') {
            showError(emailInput, 'Email wajib diisi');
            isValid = false;
        } else if (!validateEmail(emailInput.value.trim())) {
            showError(emailInput, 'Format email tidak valid');
            isValid = false;
        }

        // Validasi username
        if (usernameInput.value.trim() === '') {
            showError(usernameInput, 'Username wajib diisi');
            isValid = false;
        }

        // Validasi password
        if (passwordInput.value.trim() === '') {
            showError(passwordInput, 'Password wajib diisi');
            isValid = false;
        }

        // Validasi konfirmasi password
        if (confirmInput.value.trim() === '') {
            showError(confirmInput, 'Konfirmasi password wajib diisi');
            isValid = false;
        } else if (confirmInput.value.trim() !== passwordInput.value.trim()) {
            showError(confirmInput, 'Konfirmasi password tidak cocok');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});

// login page
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const loginBtn = document.getElementById('login-btn');

    function checkLoginInputs() {
        if (usernameInput.value.trim() !== '' && passwordInput.value.trim() !== '') {
            loginBtn.disabled = false;
            loginBtn.style.opacity = '1';
            loginBtn.style.cursor = 'pointer';
        } else {
            loginBtn.disabled = true;
            loginBtn.style.opacity = '0.7';
            loginBtn.style.cursor = 'not-allowed';
        }
    }

    // Cek saat input berubah
    usernameInput.addEventListener('input', checkLoginInputs);
    passwordInput.addEventListener('input', checkLoginInputs);

    // Cek awal saat halaman dimuat
    checkLoginInputs();
});

//Konfirmasi pass
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const pass = document.getElementById('password').value;
    const conf = document.getElementById('confirm_password').value;
    if (pass !== conf) {
        alert('Konfirmasi kata sandi tidak cocok!');
        e.preventDefault();
    }
});
