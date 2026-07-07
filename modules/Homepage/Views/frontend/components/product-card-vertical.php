<?php 
if (!defined('ABSPATH')) exit; 
global $product;

$regular_price = $product->get_regular_price();
$sale_price = $product->get_sale_price();
$save_percentage = 0;

if ($product->is_on_sale() && $regular_price && $sale_price) {
    $save_percentage = round(((floatval($regular_price) - floatval($sale_price)) / floatval($regular_price)) * 100, 1);
}
?>
<div class="aa-product-card-v">
    <div class="aa-product-card-v-img-wrapper">
        <?php if ($save_percentage > 0): ?>
            <div class="aa-product-card-v-badge-discount">Save <?php echo esc_html($save_percentage); ?>%</div>
        <?php endif; ?>
        
        <?php 
        // Example for Combo Offer badge if tag exists
        if (has_term('combo', 'product_tag', $product->get_id())): 
        ?>
            <div class="aa-product-card-v-badge-combo">Combo Offer</div>
        <?php endif; ?>

        <a href="<?php the_permalink(); ?>">
            <?php echo $product->get_image('medium', ['class' => 'aa-product-card-v-img']); ?>
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
    <div class="aa-product-card-v-content">
        <a href="<?php the_permalink(); ?>" class="aa-product-card-v-title-link">
            <h3 class="aa-product-card-v-title"><?php the_title(); ?></h3>
        </a>
        
        <div class="aa-product-card-v-price-box">
            <span class="aa-product-card-v-price"><?php echo wc_price($product->get_price()); ?></span>
            <?php if ($product->is_on_sale()): ?>
                <span class="aa-product-card-v-regular-price"><del><?php echo wc_price($regular_price); ?></del></span>
            <?php endif; ?>
        </div>

        <div class="aa-product-card-v-actions">
            <a href="?add-to-cart=<?php echo esc_attr($product->get_id()); ?>" data-quantity="1" class="aa-product-card-v-btn-light ajax_add_to_cart add_to_cart_button" data-product_id="<?php echo esc_attr($product->get_id()); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" rel="nofollow">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 20a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-9.8-3.6h11.2a2 2 0 0 0 1.9-1.4l2.4-9.6H6.2M3 3h2.5l1.6 7.2"/></svg>
                Add to Cart
            </a>
            <a href="<?php echo esc_url(wc_get_checkout_url() . '?add-to-cart=' . $product->get_id()); ?>" class="aa-product-card-v-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                Buy Now
            </a>
        </div>
    </div>
</div>
