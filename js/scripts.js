document.addEventListener('DOMContentLoaded', function() {
    
    // Slider del Banner Principal
    const slides = document.querySelectorAll('.slide');
    if (slides.length > 0) {
        let currentSlide = 0;
        const sliderElement = document.querySelector('.slider');
        const slideInterval = sliderElement ? parseInt(sliderElement.dataset.speed, 10) : 5000;

        // Función para controlar la reproducción de videos
        function handleSlideChange(oldIndex, newIndex) {
            const oldSlide = slides[oldIndex];
            const newSlide = slides[newIndex];

            // Pausa el video del slide que se oculta
            const oldVideo = oldSlide.querySelector('video');
            if (oldVideo) {
                oldVideo.pause();
            }

            // Reproduce el video del slide que se muestra
            const newVideo = newSlide.querySelector('video');
            if (newVideo) {
                newVideo.currentTime = 0; // Reinicia el video
                newVideo.play();
            }
        }

        function nextSlide() {
            const previousSlide = currentSlide;
            slides[previousSlide].classList.remove('active');
            
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');

            handleSlideChange(previousSlide, currentSlide);
        }

        // Iniciar el primer video si existe
        const firstVideo = slides[0].querySelector('video');
        if (firstVideo) {
            firstVideo.play().catch(error => {
                console.log("El navegador impidió la reproducción automática: ", error);
            });
        }

        setInterval(nextSlide, slideInterval);
    }

    // Menú Hamburguesa para Móviles
    const hamburger = document.getElementById('hamburger-icon');
    const navMenu = document.getElementById('nav-menu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    }
});