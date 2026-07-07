<?php if (!defined('ABSPATH')) exit; ?>
<div class="aa-section aa-top-selling">
    <div class="aa-container">
        
        <?php if (!empty($title)): ?>
            <div class="aa-section-header">
                <h2 class="aa-section-title"><?php echo esc_html($title); ?></h2>
            </div>
        <?php endif; ?>

        <?php if ($products->have_posts()): ?>
            <div class="aa-ts-grid">
                <?php while ($products->have_posts()): $products->the_post(); 
                    global $product;
                    
                    // Price Logic
                    $regular_price = $product->get_regular_price();
                    $sale_price = $product->get_sale_price();
                    $save_amount = 0;
                    if ($product->is_on_sale() && $regular_price && $sale_price) {
                        $save_amount = floatval($regular_price) - floatval($sale_price);
                    }
                ?>
                    <div class="aa-ts-card">
                        <div class="aa-ts-img-wrapper" style="position: relative;">
                            <a href="<?php the_permalink(); ?>" class="aa-ts-img-link">
                                <?php echo $product->get_image('medium', ['class' => 'aa-ts-img']); ?>
                            </a>
                            <?php 
                            // Wishlist Button
                            $product_id = $product->get_id();
                            $is_in_wishlist = class_exists('\Alaosaf\Modules\Wishlist\WishlistModule') ? \Alaosaf\Modules\Wishlist\WishlistModule::isInWishlist($product_id) : false;
                            $wishlist_class = $is_in_wishlist ? 'aa-wishlist-btn aa-in-wishlist' : 'aa-wishlist-btn';
                            ?>
                            <button class="<?php echo esc_attr($wishlist_class); ?>" data-product-id="<?php echo esc_attr($product_id); ?>" title="Toggle Wishlist">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="aa-ts-content">
                            <a href="<?php the_permalink(); ?>" class="aa-ts-title-link">
                                <h3 class="aa-ts-title"><?php the_title(); ?></h3>
                            </a>
                            
                            <div class="aa-ts-price-box">
                                <span class="aa-ts-price"><?php echo wc_price($product->get_price()); ?></span>
                                <?php if ($product->is_on_sale()): ?>
                                    <span class="aa-ts-regular-price"><del><?php echo wc_price($regular_price); ?></del></span>
                                <?php endif; ?>
                            </div>

                            <?php if ($save_amount > 0): ?>
                                <div class="aa-ts-badge">
                                    Save <?php echo wc_price($save_amount); ?>
                                </div>
                            <?php endif; ?>

                            <div class="aa-ts-actions">
                                <?php if ($product->is_type('variable')): ?>
                                    <button class="aa-ts-btn aa-ts-btn-outline aa-quick-view-btn" data-product_id="<?php echo esc_attr($product->get_id()); ?>" aria-label="<?php echo esc_attr($product->add_to_cart_description()); ?>">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 20a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-9.8-3.6h11.2a2 2 0 0 0 1.9-1.4l2.4-9.6H6.2M3 3h2.5l1.6 7.2"/></svg>
                                        Add To Cart
                                    </button>
                                    <button class="aa-ts-btn aa-ts-btn-solid aa-quick-view-btn" data-product_id="<?php echo esc_attr($product->get_id()); ?>" data-buy_now="true">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                        Buy now
                                    </button>
                                <?php else: ?>
                                    <a href="?add-to-cart=<?php echo esc_attr($product->get_id()); ?>" data-quantity="1" class="aa-ts-btn aa-ts-btn-outline ajax_add_to_cart add_to_cart_button" data-product_id="<?php echo esc_attr($product->get_id()); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" aria-label="<?php echo esc_attr($product->add_to_cart_description()); ?>" rel="nofollow">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 20a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-9.8-3.6h11.2a2 2 0 0 0 1.9-1.4l2.4-9.6H6.2M3 3h2.5l1.6 7.2"/></svg>
                                        Add To Cart
                                    </a>
                                    <a href="<?php echo esc_url(wc_get_checkout_url() . '?add-to-cart=' . $product->get_id()); ?>" class="aa-ts-btn aa-ts-btn-solid">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                        Buy now
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="aa-placeholder">
                <p><?php _e('No top selling products found yet.', 'al-aosaf'); ?></p>
            </div>
        <?php endif; ?>

    </div>
</div>
