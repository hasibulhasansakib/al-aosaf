<?php
/**
 * Atelier product card.
 *
 * @var \WC_Product $product
 * @var bool        $eager
 * @var \Alaosaf\Modules\Atelier\AtelierView $this
 */
if (!defined('ABSPATH')) exit;

$pid       = $product->get_id();
$url       = $product->get_permalink();
$name      = $product->get_name();
$main      = (int) $product->get_image_id();
$gallery   = $product->get_gallery_image_ids();
$alt       = $gallery ? (int) $gallery[0] : 0;
$instock   = $product->is_in_stock();
$variable  = $product->is_type('variable');
$sizes     = '(max-width: 640px) 46vw, (max-width: 1100px) 30vw, 280px';

$badge = '';
if (!$instock) {
    $badge = '<span class="at-badge at-badge--out">স্টক শেষ</span>';
} elseif ($product->is_on_sale()) {
    $reg = (float) $product->get_regular_price();
    $now = (float) $product->get_price();
    if ($variable) {
        $reg = (float) $product->get_variation_regular_price('max');
        $now = (float) $product->get_variation_sale_price('max');
    }
    if ($reg > 0 && $now > 0 && $now < $reg) {
        $badge = '<span class="at-badge at-badge--sale">−' . esc_html($this->bn((string) round(100 - $now / $reg * 100))) . '%</span>';
    }
} elseif ($product->get_date_created() && $product->get_date_created()->getTimestamp() > time() - 30 * DAY_IN_SECONDS) {
    $badge = '<span class="at-badge">নতুন</span>';
}

$catName = '';
$terms = get_the_terms($pid, 'product_cat');
if ($terms && !is_wp_error($terms)) {
    $terms = array_values(array_filter($terms, static fn($t) => $t->slug !== 'uncategorized'));
    $catName = $terms ? end($terms)->name : '';
}

$inWishlist = class_exists('\Alaosaf\Modules\Wishlist\WishlistModule') && \Alaosaf\Modules\Wishlist\WishlistModule::isInWishlist($pid);
?>
<article class="at-card<?php echo $instock ? '' : ' is-out'; ?><?php echo $alt ? ' has-alt' : ''; ?>">
    <div class="at-card__media">
        <a href="<?php echo esc_url($url); ?>" class="at-card__imgs" tabindex="-1" aria-hidden="true">
            <?php
            echo $main
                ? $this->img($main, 'woocommerce_single', ['class' => 'at-card__img', 'sizes' => $sizes, 'alt' => $name, 'loading' => $eager ? 'eager' : 'lazy'])
                : wc_placeholder_img('woocommerce_single', ['class' => 'at-card__img']);
            if ($alt) {
                echo $this->img($alt, 'woocommerce_single', ['class' => 'at-card__img at-card__img--alt', 'sizes' => $sizes, 'alt' => '']);
            }
            ?>
        </a>
        <?php echo $badge; // escaped above ?>
        <button type="button" class="aa-wishlist-btn at-card__wish<?php echo $inWishlist ? ' aa-in-wishlist' : ''; ?>" data-product-id="<?php echo esc_attr((string) $pid); ?>" aria-label="উইশলিস্টে রাখুন">
            <?php echo $this->icon('heart'); ?>
        </button>
    </div>
    <div class="at-card__body">
        <?php if ($catName) : ?><p class="at-card__cat"><?php echo esc_html($catName); ?></p><?php endif; ?>
        <h3 class="at-card__title"><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($name); ?></a></h3>
        <div class="at-card__price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
    </div>
    <?php if ($instock) : ?>
        <div class="at-card__actions">
            <?php if ($variable) : ?>
                <button type="button" class="at-card__add aa-quick-view-btn" data-product_id="<?php echo esc_attr((string) $pid); ?>">
                    <?php echo $this->icon('bag'); ?><span>কার্টে যোগ করুন</span>
                </button>
                <button type="button" class="at-card__buy aa-quick-view-btn" data-product_id="<?php echo esc_attr((string) $pid); ?>" data-buy_now="true">অর্ডার করুন</button>
            <?php else : ?>
                <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" class="at-card__add add_to_cart_button ajax_add_to_cart" data-quantity="1" data-product_id="<?php echo esc_attr((string) $pid); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" rel="nofollow" aria-label="<?php echo esc_attr($name); ?> কার্টে যোগ করুন">
                    <?php echo $this->icon('bag'); ?><span>কার্টে যোগ করুন</span>
                </a>
                <a href="<?php echo esc_url(add_query_arg('add-to-cart', $pid, wc_get_checkout_url())); ?>" class="at-card__buy" rel="nofollow">অর্ডার করুন</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</article>
