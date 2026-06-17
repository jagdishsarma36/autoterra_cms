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

// products page FAQ
function toggleFaq(btn) {
    const item = btn.closest('.faq-item');
    const ans  = item.querySelector('.faq-a');
    const icon = btn.querySelector('i');
    const isOpen = ans.style.maxHeight && ans.style.maxHeight !== '0px';
    document.querySelectorAll('.faq-a').forEach(a => { a.style.maxHeight = '0px'; a.style.opacity='0'; });
    document.querySelectorAll('.faq-q i').forEach(i => i.className='ti ti-plus');
    if (!isOpen) {
      ans.style.maxHeight = ans.scrollHeight + 'px';
      ans.style.opacity = '1';
      icon.className = 'ti ti-minus';
    }
}

// counter for home page 
$('.stats .stat-item .stat-num').each(function () {
    var $this = $(this);
    var original = $this.text().trim();

    // Extract number only
    var target = parseInt(original.replace(/[^\d]/g, ''), 10);

    // Keep everything before and after the number
    var prefix = original.match(/^[^\d]*/) ? original.match(/^[^\d]*/)[0] : '';
    var suffix = original.match(/[^\d]*$/) ? original.match(/[^\d]*$/)[0] : '';

    $({ count: 0 }).animate(
        { count: target },
        {
            duration: 2000,
            easing: 'swing',
            step: function () {
                $this.text(prefix + Math.floor(this.count) + suffix);
            },
            complete: function () {
                $this.text(prefix + target + suffix);
            }
        }
    );
});

//tabs for pro spatial
function scrollToModule(id, btn) {
    const el = document.getElementById(id);
    if (!el) return;
    const navH  = document.querySelector('.nav').offsetHeight;
    const tabsH = document.querySelector('.ps-tabs') ? document.querySelector('.ps-tabs').offsetHeight : 0;
    window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - navH - tabsH - 8, behavior: 'smooth' });
    document.querySelectorAll('.ps-tab').forEach(t => t.classList.remove('active'));
    if (btn) btn.classList.add('active');
}