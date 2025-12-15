document.addEventListener('DOMContentLoaded', function () {
    // Adjusting the selector to match the password toggle icon in the modal
    const togglePassword = document.querySelector('#toggleModalPassword');
    // Adjusting the selector to match the password input in the modal
    const password = document.querySelector('#modalPassword');
    // Adjusting the selector to match the email input in the modal
    const email = document.querySelector('#modalEmail');
    // Adjusting the selector for the remember me checkbox in the modal
    const rememberMe = document.querySelector('#rememberMeModal');
    // Adjusting the selector for the login form in the modal
    const loginForm = document.querySelector('#loginFormModal');

    // Toggle password visibility
    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // Toggle the icon classes for showing/hiding the password
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });

    // "Remember Me" functionality: Check if the email is stored in localStorage and auto-fill
    if (localStorage.getItem('email')) {
        email.value = localStorage.getItem('email');
        rememberMe.checked = true;
    }

    // On form submission: Store or remove the email from localStorage based on the "Remember Me" checkbox
    loginForm.addEventListener('submit', function (e) {
        if (rememberMe.checked) {
            localStorage.setItem('email', email.value);
        } else {
            localStorage.removeItem('email');
        }
    });
});
