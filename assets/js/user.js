 // Fungsi untuk menampilkan toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Animasi masuk
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    // Animasi keluar setelah 3 detik
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

// Cek session/message dari PHP dan tampilkan toast
document.addEventListener('DOMContentLoaded', function() {
    // Untuk notifikasi login
    if (typeof loginSuccess !== 'undefined' && loginSuccess) {
        showToast(`Selamat datang, ${username}`, 'success');
    }
    
    // Untuk notifikasi logout
    if (typeof logoutSuccess !== 'undefined' && logoutSuccess) {
        showToast('Anda telah berhasil logout', 'info');
    }
});

 document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.createElement('button');
    mobileMenuBtn.className = 'mobile-menu-toggle';
    mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
    
    const sidebarHeader = document.querySelector('.sidebar-header');
    if (sidebarHeader) {
        sidebarHeader.appendChild(mobileMenuBtn);
        
        mobileMenuBtn.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    }
    
    // Active link highlighting
    const currentPage = location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.sidebar-nav a');
    
    navLinks.forEach(link => {
        const linkPage = link.getAttribute('href').split('/').pop();
        if (linkPage === currentPage) {
            link.parentElement.classList.add('active');
        }
    });
    
    // Search functionality
    const searchForm = document.querySelector('.search-bar form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchTerm = this.search.value.trim();
            if (searchTerm === '') {
                e.preventDefault();
                alert('Masukkan kata kunci pencarian');
            }
        });
    }
});