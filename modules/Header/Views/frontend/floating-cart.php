<?php
if (!defined('ABSPATH')) exit;

$cart_count = 0;
$cart_total = '৳0.00';

if (class_exists('WooCommerce') && !is_null(WC()->cart)) {
    $cart_count = WC()->cart->get_cart_contents_count();
    $cart_total = WC()->cart->get_cart_subtotal(); // subtotal includes HTML formatting e.g. <span class="woocommerce-Price-amount...
    
    // In case subtotal is empty because cart is empty, fallback
    if (empty(strip_tags($cart_total))) {
        $cart_total = wc_price(0);
    }
}
?>
<div class="aa-floating-cart-wrapper">
    <a href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#'); ?>" class="aa-floating-cart aa-cart-drawer-trigger" aria-label="Open Cart" style="text-decoration: none; display: block;">
        <div class="aa-fc-top">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            <span class="aa-fc-count"><?php echo esc_html($cart_count); ?> Items</span>
        </div>
        <div class="aa-fc-bottom">
            <?php echo $cart_total; ?>
        </div>
    </a>
</div>
