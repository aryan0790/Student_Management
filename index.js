// Modal Handling
const loginModal = document.getElementById('loginModal');
const registerModal = document.getElementById('registerModal');

// Modal Buttons
const loginBtn = document.getElementById('loginBtn');
const registerBtn = document.getElementById('registerBtn');

// Close Buttons
const loginClose = document.getElementById('loginClose');
const registerClose = document.getElementById('registerClose');

// Toggle modal visibility
function openModal(modal) {
    modal.style.display = 'flex';
}

function closeModal(modal) {
    modal.style.display = 'none';
}

// Open Modals
loginBtn.addEventListener('click', () => openModal(loginModal));
registerBtn.addEventListener('click', () => openModal(registerModal));

// Close Modals
loginClose.addEventListener('click', () => closeModal(loginModal));
registerClose.addEventListener('click', () => closeModal(registerModal));

// Switch between Modals
document.getElementById('openRegisterModal').addEventListener('click', () => {
    closeModal(loginModal);
    openModal(registerModal);
});

document.getElementById('openLoginModal').addEventListener('click', () => {
    closeModal(registerModal);
    openModal(loginModal);
});

// Close Modal when clicking outside of the modal content
window.addEventListener('click', (event) => {
    if (event.target === loginModal) {
        closeModal(loginModal);
    } else if (event.target === registerModal) {
        closeModal(registerModal);
    }
});

// Hamburger Menu Toggle
document.querySelector('.hamburger').addEventListener('click', () => {
    document.querySelector('.nav-links').classList.toggle('show');
});

// toggle button
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
    } else {
        passwordInput.type = "password";
    }
}

// Check for success message in session storage
if (sessionStorage.getItem('success')) {
    alert(sessionStorage.getItem('success'));
    sessionStorage.removeItem('success'); // Clear the message after displaying
}

// Check for error message in session storage
if (sessionStorage.getItem('error')) {
    alert(sessionStorage.getItem('error'));
    sessionStorage.removeItem('error'); // Clear the message after displaying
}