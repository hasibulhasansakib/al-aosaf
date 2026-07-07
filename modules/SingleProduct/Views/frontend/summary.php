<?php
if (!defined('ABSPATH')) exit;
global $product;
?>
<div class="aa-summary-inner">
    
    <?php if ($product->is_in_stock()): ?>
        <div class="aa-stock-badge in-stock"><?php _e('In Stock', 'al-aosaf'); ?></div>
    <?php else: ?>
        <div class="aa-stock-badge out-of-stock"><?php _e('Out of Stock', 'al-aosaf'); ?></div>
    <?php endif; ?>

    <h1 class="aa-product-title"><?php echo esc_html($product->get_name()); ?></h1>

    <div class="aa-product-rating-wrap">
        <?php 
        if (get_option('woocommerce_enable_review_rating') === 'yes') {
            $rating_count = $product->get_rating_count();
            $review_count = $product->get_review_count();
            $average      = $product->get_average_rating();
            if ($rating_count > 0) {
                echo wc_get_rating_html($average, $rating_count);
                echo '<span class="aa-review-count">(' . esc_html($review_count) . ' reviews)</span>';
            }
        }
        ?>
    </div>

    <div class="aa-product-price-wrap">
        <div class="aa-price-current">
            <?php echo $product->get_price_html(); ?>
        </div>
    </div>

    <div class="aa-product-short-description">
        <?php echo apply_filters('woocommerce_short_description', $product->get_short_description()); ?>
    </div>



    <!-- Add to Cart (Includes variations) -->
    <div class="aa-product-add-to-cart-wrap">
        <?php do_action('woocommerce_' . $product->get_type() . '_add_to_cart'); ?>
    </div>

    <!-- Product actions removed as requested -->
</div>
