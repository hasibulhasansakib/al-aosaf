jQuery(document).ready(function($) {
    
    // Gallery Logic
    var $thumbs = $('.aa-thumb-item');
    var totalThumbs = $thumbs.length;
    var currentIndex = 0;
    var slideInterval;

    function changeImage(index) {
        if (totalThumbs === 0) return;
        if (index >= totalThumbs) index = 0;
        if (index < 0) index = totalThumbs - 1;
        currentIndex = index;

        var $activeThumb = $thumbs.eq(currentIndex);
        $thumbs.removeClass('active');
        $activeThumb.addClass('active');

        var fullImage = $activeThumb.data('full-image');
        $('#aa-main-product-image').attr('src', fullImage);
    }

    function startAutoSlide() {
        if (totalThumbs > 1) {
            slideInterval = setInterval(function() {
                changeImage(currentIndex + 1);
            }, 4000); // 4 seconds auto slide
        }
    }

    function stopAutoSlide() {
        clearInterval(slideInterval);
    }

    // Initialize Auto Slide
    startAutoSlide();

    // Pause on hover
    $('.aa-gallery-container').hover(stopAutoSlide, startAutoSlide);

    // Thumbnail Click
    $thumbs.on('click', function() {
        changeImage($(this).data('index'));
    });

    // Arrow Clicks
    $('.aa-gallery-prev').on('click', function() {
        changeImage(currentIndex - 1);
    });

    $('.aa-gallery-next').on('click', function() {
        changeImage(currentIndex + 1);
    });

    // Custom Tabs Logic
    $('.aa-tabs-nav li').on('click', function() {
        var target = $(this).data('tab');
        
        $('.aa-tabs-nav li').removeClass('active');
        $(this).addClass('active');

        $('.aa-tab-panel').removeClass('active');
        $('#' + target).addClass('active');
    });

    // Quantity buttons
    if ($('form.cart .quantity').length > 0) {
        $('form.cart .quantity').prepend('<button type="button" class="minus">-</button>');
        $('form.cart .quantity').append('<button type="button" class="plus">+</button>');
        
        $('form.cart .quantity button').on('click', function() {
            var $input = $(this).siblings('.qty');
            var val = parseInt($input.val()) || 0;
            var max = parseInt($input.attr('max')) || 999;
            var min = parseInt($input.attr('min')) || 1;
            var step = parseInt($input.attr('step')) || 1;
            
            if ($(this).hasClass('plus')) {
                if (val < max) $input.val(val + step);
            } else {
                if (val > min) $input.val(val - step);
            }
            $input.trigger('change');
        });
    }

    // Buy Now Logic
    $('.aa-buy-now-btn').on('click', function(e) {
        e.preventDefault();
        var $form = $(this).closest('form.cart');
        if ($form.length === 0) return;
        
        // Append a hidden input so the backend knows it's a Buy Now request
        if ($form.find('input[name="aa_buy_now"]').length === 0) {
            $form.append('<input type="hidden" name="aa_buy_now" value="1" />');
        }
        
        // Submit the form (WooCommerce will handle validation like required variations)
        $form.find('button.single_add_to_cart_button').click();
    });

    // Variation Swatches Logic
    function initVariationSwatches() {
        $('table.variations select').each(function() {
            var $select = $(this);
            if ($select.next('.aa-variation-swatches').length > 0) return; // Already initialized

            var $swatchesContainer = $('<div class="aa-variation-swatches"></div>');
            
            $select.find('option').each(function() {
                var val = $(this).attr('value');
                var text = $(this).text();
                
                if (!val) return; // Skip "Choose an option"
                
                var $swatch = $('<div class="aa-variation-swatch" data-value="' + val + '">' + text + '</div>');
                
                // If it's already selected
                if ($select.val() === val) {
                    $swatch.addClass('active');
                }
                
                $swatch.on('click', function() {
                    if ($(this).hasClass('disabled')) return;
                    
                    // Update siblings UI
                    $(this).siblings().removeClass('active');
                    $(this).addClass('active');
                    
                    // Update select and trigger change for WooCommerce
                    $select.val(val).trigger('change');
                });
                
                $swatchesContainer.append($swatch);
            });
            
            $select.after($swatchesContainer);
        });
    }

    // Initialize swatches on load
    initVariationSwatches();

    // Re-sync swatches when WooCommerce updates variations (e.g., clear selection)
    $(document).on('woocommerce_update_variation_values', function() {
        $('table.variations select').each(function() {
            var $select = $(this);
            var currentVal = $select.val();
            var $swatches = $select.next('.aa-variation-swatches').find('.aa-variation-swatch');
            
            $swatches.removeClass('active').removeClass('disabled');
            
            // Check which ones are still valid options
            $select.find('option').each(function() {
                var val = $(this).attr('value');
                if (val && $(this).prop('disabled')) {
                    $swatches.filter('[data-value="' + val + '"]').addClass('disabled');
                }
            });
            
            if (currentVal) {
                $swatches.filter('[data-value="' + currentVal + '"]').addClass('active');
            }
        });
    });

});
