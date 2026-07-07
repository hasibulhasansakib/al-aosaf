<?php
namespace Alaosaf\Modules\Cart\Controllers;

class CartController {

    public function init(): void {
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets'], 99);
        
        // Hide shipping calculator ONLY on the cart page (show on checkout)
        add_filter('woocommerce_cart_ready_to_calc_shipping', [$this, 'disableShippingOnCartPage']);
    }

    public function disableShippingOnCartPage($show_shipping) {
        if (is_cart()) {
            return false;
        }
        return $show_shipping;
    }

    public function enqueueAssets() {
        if (is_cart()) {
            wp_enqueue_style('aa-cart-css', AA_PLUGIN_URL . 'modules/Cart/assets/css/cart.css', [], time());
            wp_enqueue_script('aa-cart-js', AA_PLUGIN_URL . 'modules/Cart/assets/js/cart.js', ['jquery'], time(), true);
        }
    }
}
