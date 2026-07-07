jQuery(document).ready(function($) {

    var modal = $('#aa-quick-view-modal');
    var content = modal.find('.aa-qv-content');
    var loader = modal.find('.aa-qv-loader');
    
    // Open Modal
    $(document).on('click', '.aa-quick-view-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var productId = btn.data('product_id');
        var isBuyNow = btn.data('buy_now') === true; // Check if it was Buy Now
        
        modal.addClass('is-open');
        content.hide().empty();
        loader.show();

        $.ajax({
            url: aaQuickView.ajaxurl,
            type: 'POST',
            data: {
                action: 'aa_quick_view',
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    loader.hide();
                    content.html(response.data.html).show();
                    
                    // Re-init WooCommerce variations script if exists
                    if (typeof $.fn.wc_variation_form !== 'undefined') {
                        content.find('.variations_form').wc_variation_form();
                    }

                    // If opened via Buy Now, add hidden input to redirect to checkout
                    if (isBuyNow) {
                        content.find('form.cart').append('<input type="hidden" name="aa_buy_now" value="1">');
                    }
                } else {
                    loader.hide();
                    content.html('<p>Error loading product. Please try again.</p>').show();
                }
            },
            error: function() {
                loader.hide();
                content.html('<p>Connection error. Please try again.</p>').show();
            }
        });
    });

    // Close Modal
    $(document).on('click', '.aa-qv-close, .aa-qv-overlay', function() {
        modal.removeClass('is-open');
    });

});
