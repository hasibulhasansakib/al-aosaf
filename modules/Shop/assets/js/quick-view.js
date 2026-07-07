jQuery(document).ready(function($) {

    var modal = $('#aa-quick-view-modal');
    var content = modal.find('.aa-qv-content');
    var loader = modal.find('.aa-qv-loader');
    
    var qvCache = {};

    // Universal Interceptor for Add to Cart / Buy Now buttons on Variable Products
    $(document).on('click', '.aa-quick-view-btn, .add_to_cart_button, a[href*="?add-to-cart="], button.single_add_to_cart_button', function(e) {
        var btn = $(this);
        
        // Skip if this button is inside the quick view modal itself
        if (btn.closest('.aa-qv-modal').length > 0) {
            return; 
        }

        // Skip if this is the main add to cart button on the actual single product page
        if (btn.closest('form.cart').length > 0 && !btn.hasClass('aa-quick-view-btn')) {
            return;
        }

        // Determine Product ID
        var productId = btn.data('product_id');
        if (!productId) {
            var href = btn.attr('href');
            if (href && href.indexOf('?add-to-cart=') !== -1) {
                var match = href.match(/add-to-cart=(\d+)/);
                if (match) {
                    productId = match[1];
                }
            }
        }

        if (!productId) return; 

        var isVariable = btn.hasClass('aa-quick-view-btn') || 
                         btn.hasClass('product_type_variable') || 
                         btn.closest('.product-type-variable').length > 0 ||
                         btn.closest('.product-type-grouped').length > 0;
        
        if (isVariable) {
            e.preventDefault();
            
            var btnText = btn.text().trim().toLowerCase();
            var isBuyNow = btnText.indexOf('buy now') !== -1 || btn.data('buy_now') === true;
            
            modal.addClass('is-open');
            content.hide().empty();
            loader.show();

            var renderContent = function(html) {
                loader.hide();
                content.html(html).show();
                
                if (typeof $.fn.wc_variation_form !== 'undefined') {
                    content.find('.variations_form').wc_variation_form();
                }

                content.find('table.variations select').each(function() {
                    var select = $(this);
                    select.hide();
                    
                    var wrapper = $('<div class="aa-qv-swatches"></div>');
                    select.after(wrapper);
                    
                    select.find('option').each(function() {
                        var val = $(this).val();
                        var text = $(this).text();
                        if (val) {
                            var btn = $('<button type="button" class="aa-qv-swatch-btn" data-value="' + val + '">' + text + '</button>');
                            wrapper.append(btn);
                        }
                    });

                    wrapper.on('click', '.aa-qv-swatch-btn', function() {
                        wrapper.find('.aa-qv-swatch-btn').removeClass('selected');
                        $(this).addClass('selected');
                        select.val($(this).data('value')).trigger('change');
                    });
                    
                    select.on('change', function() {
                        var currentVal = select.val();
                        wrapper.find('.aa-qv-swatch-btn').removeClass('selected');
                        if (currentVal) {
                            wrapper.find('.aa-qv-swatch-btn[data-value="' + currentVal + '"]').addClass('selected');
                        }
                    });
                });

                if (isBuyNow) {
                    content.find('form.cart').append('<input type="hidden" name="aa_buy_now" value="1">');
                    var submitBtn = content.find('button.single_add_to_cart_button');
                    if (submitBtn.length) {
                        submitBtn.text('Buy Now').css('background', '#f97316');
                    }
                }
            };

            if (qvCache[productId]) {
                renderContent(qvCache[productId]);
            } else {
                $.ajax({
                    url: aaQuickView.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'aa_quick_view',
                        product_id: productId
                    },
                    success: function(response) {
                        if (response.success) {
                            qvCache[productId] = response.data.html;
                            renderContent(response.data.html);
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
            }
        }
    });

    // Close Modal
    $(document).on('click', '.aa-qv-close, .aa-qv-overlay', function() {
        modal.removeClass('is-open');
    });

});
