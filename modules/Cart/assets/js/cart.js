jQuery(document).ready(function($) {
    
    // Function to inject custom +/- buttons into WooCommerce quantity inputs
    function initCustomQuantityButtons() {
        $('.woocommerce-cart-form td.product-quantity .quantity').each(function() {
            // Prevent multiple initializations
            if ($(this).find('.aa-qty-btn').length > 0) return;

            const $input = $(this).find('input.qty');
            
            // Inject minus button before
            $('<button type="button" class="aa-qty-btn aa-qty-minus">-</button>').insertBefore($input);
            
            // Inject plus button after
            $('<button type="button" class="aa-qty-btn aa-qty-plus">+</button>').insertAfter($input);
        });
    }

    // Initialize on page load
    initCustomQuantityButtons();

    // Handle click events for custom buttons (using event delegation to handle AJAX updates)
    $(document).on('click', '.aa-qty-btn', function() {
        const $btn = $(this);
        const $input = $btn.siblings('input.qty');
        let currentVal = parseFloat($input.val());
        const max = parseFloat($input.attr('max'));
        const min = parseFloat($input.attr('min'));
        const step = parseFloat($input.attr('step')) || 1;

        if (isNaN(currentVal)) currentVal = 0;

        if ($btn.hasClass('aa-qty-plus')) {
            if (!isNaN(max) && currentVal >= max) {
                $input.val(max);
            } else {
                $input.val(currentVal + step);
            }
        } else {
            if (!isNaN(min) && currentVal <= min) {
                $input.val(min);
            } else if (currentVal > 0) {
                $input.val(currentVal - step);
            }
        }

        // Trigger change event to notify WooCommerce
        $input.trigger('change');
    });

    // Auto-update cart when quantity changes
    let updateTimeout;
    $(document).on('change', 'input.qty', function() {
        if (updateTimeout) clearTimeout(updateTimeout);
        updateTimeout = setTimeout(function() {
            // Click the native WooCommerce "Update Cart" button
            $('[name="update_cart"]').trigger('click');
        }, 500); // Wait 500ms before triggering update to allow multiple rapid clicks
    });

    // WooCommerce triggers 'updated_cart_totals' when AJAX update completes
    $(document).on('updated_cart_totals', function() {
        // Re-initialize custom buttons on the newly injected HTML
        initCustomQuantityButtons();
    });

});
