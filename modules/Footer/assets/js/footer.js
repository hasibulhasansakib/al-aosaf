document.addEventListener('DOMContentLoaded', function() {

    // 1. Mobile Accordion Logic
    const accordionHeaders = document.querySelectorAll('.aa-footer-mobile .aa-accordion-header');
    
    accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const isExpanded = this.getAttribute('aria-expanded') === 'true';

            // Close all other accordions
            accordionHeaders.forEach(otherHeader => {
                if (otherHeader !== this) {
                    otherHeader.setAttribute('aria-expanded', 'false');
                    otherHeader.nextElementSibling.style.maxHeight = null;
                }
            });

            // Toggle current accordion
            if (isExpanded) {
                this.setAttribute('aria-expanded', 'false');
                content.style.maxHeight = null;
            } else {
                this.setAttribute('aria-expanded', 'true');
                content.style.maxHeight = content.scrollHeight + "px";
            }
        });
    });

    // 2. Back to Top Button
    const backToTopBtn = document.getElementById('aa-back-to-top');
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 500) {
                backToTopBtn.classList.add('is-visible');
            } else {
                backToTopBtn.classList.remove('is-visible');
            }
        });

        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // 3. Mobile Bottom Sheet Logic
    const sheetTriggers = document.querySelectorAll('.aa-sheet-trigger');
    const sheetOverlay = document.querySelector('.aa-bottom-sheet-overlay');
    const sheetCloses = document.querySelectorAll('.aa-bottom-sheet-close, .aa-bottom-sheet-overlay');

    sheetTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            if (targetId) {
                const sheet = document.getElementById(targetId);
                if (sheet) {
                    sheet.classList.add('is-active');
                    if (sheetOverlay) sheetOverlay.classList.add('is-active');
                    document.body.style.overflow = 'hidden'; // Prevent scrolling
                }
            }
        });
    });

    sheetCloses.forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            document.querySelectorAll('.aa-bottom-sheet.is-active').forEach(sheet => {
                sheet.classList.remove('is-active');
            });
            if (sheetOverlay) sheetOverlay.classList.remove('is-active');
            document.body.style.overflow = ''; // Restore scrolling
        });
    });

});
