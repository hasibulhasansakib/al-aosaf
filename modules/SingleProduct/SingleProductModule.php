<?php
declare(strict_types=1);

namespace Alaosaf\Modules\SingleProduct;

use Alaosaf\Interfaces\ModuleInterface;
use Alaosaf\Modules\SingleProduct\Controllers\SingleProductShortcodeController;

class SingleProductModule implements ModuleInterface {
    
    public function getModuleId(): string {
        return 'single-product';
    }

    public function init(): void {
        (new SingleProductShortcodeController())->init();

        add_filter('woocommerce_add_to_cart_redirect', function($url) {
            if (isset($_REQUEST['aa_buy_now']) && $_REQUEST['aa_buy_now']) {
                return wc_get_checkout_url();
            }
            return $url;
        });

        add_action('woocommerce_after_add_to_cart_button', function() {
            echo '<button type="button" class="aa-buy-now-btn">';
            echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>';
            echo __('Buy Now', 'al-aosaf');
            echo '</button>';
        });

        add_action('woocommerce_after_add_to_cart_form', function() {
            if (get_option('aa_sp_help_enable', 'yes') !== 'yes') return;
            
            $title = get_option('aa_sp_help_title', 'Need Help Ordering?');
            $text = get_option('aa_sp_help_text', 'If you need any assistance with your order, feel free to contact us via WhatsApp or Facebook.');
            $wa = get_option('aa_sp_help_wa', '+8801724212748');
            $fb = get_option('aa_sp_help_fb', 'https://facebook.com/aljayyidbd');
            
            $help_path = AA_PLUGIN_DIR . 'modules/SingleProduct/Views/frontend/help-section.php';
            if (file_exists($help_path)) include $help_path;
        });
    }
}
