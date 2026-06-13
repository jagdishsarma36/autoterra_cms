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
if (!initSlider()) {

    const carouselSlides = document.getElementById('carousel-slides');

    if (carouselSlides) {
        const observer = new MutationObserver(function() {
            if (initSlider()) {
                observer.disconnect();
            }
        });

        observer.observe(carouselSlides, {
            childList: true,
            subtree: true
        });
    }
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

jQuery(document).ready(function() {
    jQuery('.logos-strip').addClass('owl-carousel').owlCarousel({
        loop: true,
        margin: 20,
        nav: false,
        dots: false,
        autoplay: true,
        autoplayTimeout: 3000,
        responsive: {
            0: { items: 2 },
            768: { items: 4 },
            1024: { items: 6 }
        }
    });
// contact active button
    $(document).on('change', '.field_wrap_request_for input[type="radio"]', function () {
    $('.field_wrap_request_for .radio-label').removeClass('active');
    $(this).closest('.radio-label').addClass('active');
    });
});
document.addEventListener("DOMContentLoaded", function () {

    function initScrollSpy({
        linkSelector = '.legal-toc a',
        sectionSelector = 'h2[id]',
        offset = 150,
        scrollOffset = 140,
        activeClass = 'active'
    } = {}) {

        const links = document.querySelectorAll(linkSelector);
        const sections = document.querySelectorAll(sectionSelector);

        if (!links.length || !sections.length) return;

        let ticking = false;

        function setActiveOnScroll() {
            let currentSectionId = "";

            sections.forEach(section => {
                const rect = section.getBoundingClientRect();

                // Active only when section is in visible range
                if (rect.top <= offset && rect.bottom > offset) {
                    currentSectionId = section.id;
                }
            });

            // fallback (top case)
            if (!currentSectionId) {
                sections.forEach(section => {
                    if (section.getBoundingClientRect().top <= offset) {
                        currentSectionId = section.id;
                    }
                });
            }

            // fallback to first section
            if (!currentSectionId && sections.length) {
                currentSectionId = sections[0].id;
            }

            links.forEach(link => {
                link.classList.remove(activeClass);

                if (link.getAttribute('href') === "#" + currentSectionId) {
                    link.classList.add(activeClass);
                }
            });

            ticking = false;
        }

        // optimized scroll (performance)
        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(setActiveOnScroll);
                ticking = true;
            }
        });

        window.addEventListener('load', setActiveOnScroll);
        setActiveOnScroll();

        // click scroll
        links.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                const target = document.querySelector(this.getAttribute('href'));

                if (!target) return;

                links.forEach(el => el.classList.remove(activeClass));
                this.classList.add(activeClass);

                window.scrollTo({
                    top: target.offsetTop - scrollOffset,
                    behavior: 'smooth'
                });
            });
        });
    }

    //  INIT (default)
    initScrollSpy();

    //  Example reuse (uncomment if needed)
    /*
    initScrollSpy({
        linkSelector: '.sidebar a',
        sectionSelector: '.section',
        offset: 120,
        scrollOffset: 100,
        activeClass: 'active'
    });
    */

});
function toggleRN(el) {
    const currentItem = el.closest('.res-rn-item');
    const allItems = document.querySelectorAll('.res-rn-item');

    // If already open → close it
    if (currentItem.classList.contains('open')) {
        currentItem.classList.remove('open');
        return;
    }

    // Close all
    allItems.forEach(item => {
        item.classList.remove('open');
    });

    // Open clicked
    currentItem.classList.add('open');
}

// toggle pricing page
function setTrack(trackId) {
    trackId = trackId.replace('#', '');
    document.querySelectorAll('.pr-cards-line').forEach(track => {
        track.style.display = 'none';
    });
    document.querySelectorAll('.pr-track-tab').forEach(btn => {
        btn.classList.remove('active');
    });
    const selectedTrack = document.getElementById(trackId);
    if (selectedTrack) {
        selectedTrack.style.display = 'block';
    }
    const activeBtn = document.querySelector(
        `[onclick="setTrack('#${trackId}')"],
         [onclick="setTrack('${trackId}')"]`
    );
    if (activeBtn) {
        activeBtn.classList.add('active');
    }
}

function toggleLicenseNote() {
    const checkbox = document.getElementById('floatingToggle');
    const note = document.getElementById('floatingNote');
    if (!checkbox || !note) return;
    note.style.display = checkbox.checked ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.pr-cards-line').forEach(track => {
        track.style.display = 'none';
    });
    const defaultBtn = document.querySelector('.pr-track-tab.active');
    if (defaultBtn) {
        defaultBtn.click();
    }
    const note = document.getElementById('floatingNote');
    if (note) {
        note.style.display = 'none';
    }
});

function renderFeatures(key) {
    const p = PRODUCTS[key];

    document.getElementById('fcTitle').textContent =
        p.name + ' Features';

    const rows = document.getElementById('fcRows');

    rows.innerHTML = p.features.map(f => {

        let valHtml = '';

        if (f.val === 'yes') {
            valHtml =
                '<div class="fc-val yes"><span class="ti ti-circle-check"></span> Yes</div>';
        }
        else if (f.val === 'partial') {
            valHtml =
                `<div class="fc-val partial"><span class="ti ti-clock"></span> ${f.note || 'Partial'}</div>`;
        }
        else {
            valHtml =
                '<div class="fc-val"><span class="ti ti-circle-x" style="color:var(--border)"></span></div>';
        }

        return `
            <div class="fc-row">
                <div class="fc-feature">${f.name}</div>
                ${valHtml}
            </div>
        `;
    }).join('');
}

function updateTermPills(key) {

    const terms = TERM_AVAIL[key];
    const container = document.getElementById('termPills');

    let html = '';
    let firstEnabled = null;

    Object.entries(terms).forEach(([term, enabled]) => {

        if (!enabled) return;

        if (!firstEnabled) {
            firstEnabled = term;
        }

        html += `
            <label class="term-pill ${firstEnabled === term ? 'selected' : ''}"
                   onclick="selectTerm(this)">
                <input
                    type="radio"
                    name="term"
                    value="${term}"
                    ${firstEnabled === term ? 'checked' : ''}
                >
                ${TERM_LABELS[term] || term}
            </label>
        `;
    });

    container.innerHTML = html;

    selectedTerm = firstEnabled;
}

function selectProduct(el) {

    document.querySelectorAll('.prod-card')
        .forEach(card => card.classList.remove('selected'));

    el.classList.add('selected');

    el.querySelector('input[type="radio"]').checked = true;

    selectedProduct = el.dataset.product;

    renderFeatures(selectedProduct);
    updateTermPills(selectedProduct);
}

function selectTerm(el) {

    document.querySelectorAll('.term-pill')
        .forEach(p => p.classList.remove('selected'));

    el.classList.add('selected');

    const input = el.querySelector('input');

    input.checked = true;

    selectedTerm = input.value;
}

document.addEventListener('DOMContentLoaded', function () {

    const firstCard = document.querySelector('.prod-card');

    if (firstCard) {
        firstCard.classList.add('selected');

        selectedProduct = firstCard.dataset.product;

        renderFeatures(selectedProduct);
        updateTermPills(selectedProduct);
    }
});