jQuery(document).ready(function($) {
    
    // Initialize Left Hero Slider
    if ($('.aa-hero-swiper').length) {
        new Swiper('.aa-hero-swiper', {
            loop: true,
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.aa-hero-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.aa-hero-next',
                prevEl: '.aa-hero-prev',
            },
        });
    }

    // Initialize Right Product Carousel (Crossfade)
    if ($('.aa-right-product-swiper').length) {
        new Swiper('.aa-right-product-swiper', {
            loop: true,
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            allowTouchMove: false, // Prevent manual dragging so it acts just like a dynamic banner
        });
    }

    // Initialize Featured Categories Carousel
    if ($('.aa-categories-swiper').length) {
        new Swiper('.aa-categories-swiper', {
            loop: false,
            spaceBetween: 20,
            slidesPerView: 2,
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.aa-cat-next',
                prevEl: '.aa-cat-prev',
            },
            pagination: {
                el: '.aa-cat-pagination',
                clickable: true,
            },
            breakpoints: {
                576: {
                    slidesPerView: 3,
                },
                768: {
                    slidesPerView: 4,
                },
                992: {
                    slidesPerView: 5,
                },
                1200: {
                    slidesPerView: 6,
                }
            }
        });
    }

    // Initialize Dynamic Sliders
    if ($('.aa-dynamic-swiper').length) {
        $('.aa-dynamic-swiper').each(function() {
            var $this = $(this);
            var autoplay = $this.data('autoplay') === true;
            
            var swiperOptions = {
                loop: false,
                spaceBetween: 15,
                slidesPerView: 2,
                centerInsufficientSlides: true,
                navigation: {
                    nextEl: $this.closest('.aa-dynamic-slider-wrapper').find('.aa-dynamic-next')[0],
                    prevEl: $this.closest('.aa-dynamic-slider-wrapper').find('.aa-dynamic-prev')[0],
                },
                pagination: {
                    el: $this.find('.aa-dynamic-pagination')[0],
                    clickable: true,
                },
                breakpoints: {
                    576: { slidesPerView: 3, spaceBetween: 20 },
                    992: { slidesPerView: 4, spaceBetween: 30 },
                    1200: { slidesPerView: 5, spaceBetween: 30 },
                }
            };

            if (autoplay) {
                swiperOptions.autoplay = {
                    delay: 4000,
                    disableOnInteraction: false,
                };
            }

            new Swiper(this, swiperOptions);
        });
    }

});