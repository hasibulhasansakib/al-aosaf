<?php
if (!defined('ABSPATH')) exit;
?>
<div class="aa-cart-drawer">
    <div class="aa-cart-drawer-overlay"></div>
    <div class="aa-cart-drawer-content">
        <div class="aa-cart-drawer-header">
            <h3>Your Cart</h3>
        </div>
        <div class="aa-cart-drawer-body">
            <?php
            if (class_exists('WooCommerce')) {
                // Wrapper required by WooCommerce to update via AJAX fragments
                echo '<div class="widget_shopping_cart_content">';
                woocommerce_mini_cart();
                echo '</div>';
            } else {
                echo '<p class="aa-cart-empty-msg">WooCommerce is not active.</p>';
            }
            ?>
        </div>
        <div class="aa-cart-drawer-footer">
            <button class="aa-cart-drawer-close" aria-label="Close Cart">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                <span>Close Cart</span>
            </button>
        </div>
    </div>
</div>
