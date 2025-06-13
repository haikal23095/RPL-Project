const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const mainContainer = document.getElementById('main'); // Ubah 'main' menjadi 'mainContainer' agar konsisten

// Fungsi untuk mengaktifkan tampilan sign-up (register)
function showRegisterForm() {
    mainContainer.classList.add("right-panel-active");
}

// Fungsi untuk mengaktifkan tampilan sign-in (login)
function showLoginForm() {
    mainContainer.classList.remove("right-panel-active");
}

// Event listener untuk tombol Sign Up
signUpButton.addEventListener('click', () => {
    showRegisterForm();
});

// Event listener untuk tombol Sign In
signInButton.addEventListener('click', () => {
    showLoginForm();
});

// Logika untuk Slider Gambar Otomatis
let currentIndex = 0;
const items = document.querySelectorAll('.slider .item');
const totalItems = items.length;

function showNextSlide() {
    currentIndex = (currentIndex + 1) % totalItems;
    const newTransformValue = `translateX(-${currentIndex * 100}%)`;
    const sliderList = document.querySelector('.slider .list');
    if (sliderList) { // Pastikan elemen .slider .list ada sebelum mencoba mengaksesnya
        sliderList.style.transform = newTransformValue;
    }
}

// Hanya jalankan slider jika elemennya ada di halaman
if (items.length > 0) {
    setInterval(showNextSlide, 5000); // Change image every 5 seconds
}


// --- Penambahan Logika Baru untuk Menangani URL Hash (#register) ---
document.addEventListener('DOMContentLoaded', () => {
    if (window.location.hash === '#register') {
        showRegisterForm();
        // Opsional: Hapus hash dari URL setelah transisi jika tidak ingin hash terlihat di URL
        // history.replaceState(null, null, window.location.pathname + window.location.search);
    }
});