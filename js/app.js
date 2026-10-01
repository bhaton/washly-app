// app.js - Simplified for PHP Backend

const app = {
    toggleMobileMenu() {
        const nav = document.getElementById('nav-links');
        if(nav) nav.classList.toggle('show');
    },
    
    // Dipanggil dari input pencarian area di desain lama (jika masih tersisa)
    checkArea() {
        const input = document.getElementById('area-check-input');
        if(!input) return;
        const val = input.value;
        const res = document.getElementById('area-result');
        if (val.trim().length > 3) {
            res.innerHTML = `<div class="alert-info text-success" style="background: rgba(16,185,129,0.1);"><i class="fa-solid fa-check-circle"></i> Sistem mendeteksi area pencarian.</div>`;
        } else {
            res.innerHTML = `<div class="alert-info text-warning" style="background: rgba(245,158,11,0.1);"><i class="fa-solid fa-circle-exclamation"></i> Masukkan nama area lebih panjang.</div>`;
        }
    }
};

// No longer overriding window.onload and window.location because the backend handles routing.
