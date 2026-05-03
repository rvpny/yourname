document.addEventListener("DOMContentLoaded", () => {
    try {
        // 1. Inisialisasi Swiper dengan error handling
        if (document.querySelector(".book-slider")) {
            const swiper = new Swiper(".book-slider", {
                loop: true,
                autoplay: {
                     delay: 3000,
                    disableOnInteraction: false,
                },
                effect: "fade",
                fadeEffect: {
                    crossFade: true
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
            });
            console.log("Swiper initialized successfully");
        } else {
            console.warn("Swiper container (.book-slider) not found");
        }

        // 2. Animasi Teks Welcome dengan error handling
        const welcomeTextElement = document.getElementById("welcome-text");
        const taglineElement = document.getElementById("welcome-tagline");
        if (welcomeTextElement && taglineElement) {
            const translations = [
                { 
                    lang: "Selamat Datang",
                    tagline: '"Temukan dunia baru dalam setiap halaman"'
                },
                { 
                    lang: "Welcome",
                    tagline: '"Discover new worlds in every page"'
                },
                { 
                    lang: "ようこそ",
                    tagline: '"すべてのページに新しい世界が広がっています"'
                },
                { 
                    lang: "Bienvenido",
                    tagline: '"Encuentra un nuevo mundo en cada página"'
                },
                { 
                    lang: "Bienvenue",
                    tagline: '"Découvrez de nouveaux mondes à chaque page"'
                },
                { 
                    lang: "Willkommen",
                    tagline: '"Entdecke neue Welten auf jeder Seite"'
                },
                { 
                    lang: "欢迎",
                    tagline: '"在每一页中发现新世界"'
                },
                { 
                    lang: "Merhaba",
                    tagline: '"Her sayfada yeni bir dünya keşfedin"'
                },
                { 
                    lang: "Добро пожаловать",
                    tagline: '"Откройте для себя новый мир на каждой странице"'
                },
                { 
                    lang: "مرحبا",
                    tagline: '"اكتشف عوالم جديدة في كل صفحة"'
                }
            ];

            let currentIndex = 0;
            const animationDuration = 4000;

            function animateText() {
                //Fade out
                 welcomeTextElement.classList.remove("visible");
                 taglineElement.classList.remove("visible");
            
            setTimeout(() => {
                //Update text
                currentIndex = (currentIndex + 1) % translations.length;
                    
                // Set teks dan tagline dalam bahasa yang sama
                welcomeTextElement.textContent = translations[currentIndex].lang;
                taglineElement.textContent = translations[currentIndex].tagline;

        // Animasikan fade in
        void welcomeTextElement.offsetWidth; // Trigger reflow
        welcomeTextElement.classList.add("visible");
        taglineElement.classList.add("visible");

        setTimeout(animateText, animationDuration);
    }, 1000); // Waktu fade out
} 
        // Mulai animasi pertama kali
        welcomeTextElement.textContent = translations[0].lang;
        taglineElement.textContent = translations[0].tagline;
        welcomeTextElement.classList.add("visible");
        taglineElement.classList.add("visible");
        
        // Mulai loop animasi setelah delay awal
        setTimeout(animateText, animationDuration)
        }
        
    // 3. Mobile Menu Toggle dengan error handling
        const menuToggle = document.querySelector(".mobile-menu-toggle");
        const navMenu = document.querySelector(".navigation-menu");

        if (menuToggle && navMenu) {
            menuToggle.addEventListener("click", () => {
                navMenu.classList.toggle("active");
                const icon = menuToggle.querySelector("i");
                if (navMenu.classList.contains("active")) {
                    icon.classList.replace("fa-bars", "fa-times");
                } else {
                    icon.classList.replace("fa-times", "fa-bars");
                }
            });

            navMenu.querySelectorAll("a").forEach(link => {
                link.addEventListener("click", () => {
                    if (navMenu.classList.contains("active")) {
                        navMenu.classList.remove("active");
                        const icon = menuToggle.querySelector("i");
                        icon.classList.replace("fa-times", "fa-bars");
                    }
                });
            });
        }
    } catch (error) {
        console.error("Error in main script:", error);
    }
});