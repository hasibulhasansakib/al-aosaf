jQuery(document).ready(function($) {
    $(document).on('click', '.aa-wishlist-btn', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var productId = $btn.data('product-id');
        
        if ($btn.hasClass('loading')) {
            return;
        }

        $btn.addClass('loading');

        $.ajax({
            url: aa_wishlist_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aa_toggle_wishlist',
                product_id: productId,
                nonce: aa_wishlist_ajax.nonce
            },
            success: function(response) {
                $btn.removeClass('loading');
                if (response.success) {
                    if (response.data.added) {
                        $btn.addClass('aa-in-wishlist');
                    } else {
                        $btn.removeClass('aa-in-wishlist');
                    }
                    
                    // Update fragments
                    if (response.data.fragments) {
                        $.each(response.data.fragments, function(selector, html) {
                            $(selector).html(html);
                        });
                    }

                    // Update badge
                    var count = response.data.count;
                    var $badge = $('.aa-wishlist-badge');
                    if ($badge.length) {
                        $badge.text(count);
                        if (count > 0) {
                            $badge.show();
                        } else {
                            $badge.hide();
                        }
                    }
                } else {
                    console.error('Wishlist error:', response);
                }
            },
            error: function() {
                $btn.removeClass('loading');
                console.error('Wishlist AJAX request failed');
            }
        });
    });
});
