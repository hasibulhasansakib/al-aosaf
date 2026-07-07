<?php
if (!defined('ABSPATH')) exit;

$wishlist_items = class_exists('\Alaosaf\Modules\Wishlist\WishlistModule') ? \Alaosaf\Modules\Wishlist\WishlistModule::getWishlist() : [];

if (empty($wishlist_items)): ?>
    <div class="aa-wishlist-dropdown-inner aa-wishlist-empty">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--aa-primary, #C8A15A)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
        <h4>Your Wishlist is Empty</h4>
        <p>Start exploring and add your favorite products here!</p>
        <a href="<?php echo esc_url(function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : '#'); ?>" class="aa-wishlist-btn-shop">Browse Shop</a>
    </div>
<?php else: ?>
    <div class="aa-wishlist-dropdown-inner aa-wishlist-has-items">
        <div class="aa-mini-wishlist-items">
            <?php foreach ($wishlist_items as $product_id): 
                $product = wc_get_product($product_id);
                if (!$product) continue;
            ?>
                <div class="aa-mini-wishlist-item" data-product-id="<?php echo esc_attr($product_id); ?>">
                    <a href="<?php echo esc_url($product->get_permalink()); ?>" class="aa-mw-img">
                        <?php echo $product->get_image('thumbnail'); ?>
                    </a>
                    <div class="aa-mw-content">
                        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="aa-mw-title">
                            <?php echo wp_kses_post($product->get_name()); ?>
                        </a>
                        <div class="aa-mw-price">
                            <?php echo wp_kses_post($product->get_price_html()); ?>
                        </div>
                    </div>
                    <button class="aa-mw-remove aa-wishlist-btn aa-in-wishlist" data-product-id="<?php echo esc_attr($product_id); ?>" title="Remove from wishlist">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="aa-mini-wishlist-footer">
            <a href="<?php echo esc_url(function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : '#'); ?>" class="aa-wishlist-btn-shop">Continue Shopping</a>
        </div>
    </div>
<?php endif; ?>
