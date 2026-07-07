<?php
declare(strict_types=1);

namespace Alaosaf\Modules\SingleProduct\Controllers;

use Alaosaf\Base\AbstractController;

class SingleProductShortcodeController extends AbstractController {
    
    public function init(): void {
        add_shortcode('aa_single_product', [$this, 'renderShortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function enqueueAssets(): void {
        // Register assets, enqueue them only when shortcode is used
        wp_register_style('aa-single-product-css', AA_PLUGIN_URL . 'modules/SingleProduct/assets/css/single-product.css', [], time());
        wp_register_script('aa-single-product-js', AA_PLUGIN_URL . 'modules/SingleProduct/assets/js/single-product.js', ['jquery'], time(), true);
        
        // Ensure standard wc scripts are loaded (for variation swatches, add to cart etc.)
        if (function_exists('is_product') && is_product()) {
            wp_enqueue_script('wc-add-to-cart-variation');
        }
    }

    public function renderShortcode($atts): string {
        if (!class_exists('WooCommerce')) {
            return '';
        }

        global $product;
        if (!$product || !is_a($product, 'WC_Product')) {
            $product = wc_get_product(get_the_ID());
            if (!$product) {
                return '<p>No product found.</p>';
            }
        }

        wp_enqueue_style('aa-single-product-css');
        wp_enqueue_script('aa-single-product-js');

        ob_start();
        $layout_path = AA_PLUGIN_DIR . 'modules/SingleProduct/Views/frontend/layout.php';
        if (file_exists($layout_path)) {
            include $layout_path;
        }
        return ob_get_clean();
    }
}
