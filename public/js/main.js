let currentSlide = 0;

function showSlide(index) {
    const slides = document.querySelectorAll('#carousel-slides .slide');
    const slideLabel = document.querySelector('#slide-label');

    if (!slides.length) return;

    if (index >= slides.length) {
        currentSlide = 0;
    } else if (index < 0) {
        currentSlide = slides.length - 1;
    } else {
        currentSlide = index;
    }

    slides.forEach((slide, i) => {
        slide.style.display = (i === currentSlide) ? 'block' : 'none';
    });

    if (slideLabel) {
        slideLabel.textContent = slides[currentSlide].dataset.title || '';
    }
}

function goSlide(direction) {
    showSlide(currentSlide + direction);
}

function initSlider() {
    const slides = document.querySelectorAll('#carousel-slides .slide');

    if (slides.length) {
        showSlide(0);
        return true;
    }

    return false;
}

// Try immediately
if (!initSlider()) {

    // Watch for dynamically added slides
    const observer = new MutationObserver(function() {
        if (initSlider()) {
            observer.disconnect();
        }
    });

    observer.observe(document.getElementById('carousel-slides'), {
        childList: true,
        subtree: true
    });
}

let currentTesti = 0;

function goTesti(direction) {
    const grid = document.getElementById('testi-grid');
    const cards = grid.querySelectorAll('.testi-card');

    const visibleCards = 2;
    const totalPages = Math.ceil(cards.length / visibleCards);

    currentTesti += direction;

    if (currentTesti < 0) {
        currentTesti = totalPages - 1;
    }

    if (currentTesti >= totalPages) {
        currentTesti = 0;
    }

    const slideWidth = grid.parentElement.offsetWidth;

    grid.style.transform =
        `translateX(-${currentTesti * slideWidth}px)`;

    updateDots(totalPages);
}

function updateDots(totalPages) {
    const dots = document.querySelectorAll('.tdot');

    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentTesti);
    });
}