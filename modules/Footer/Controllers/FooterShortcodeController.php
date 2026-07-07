<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Footer\Controllers;

class FooterShortcodeController {
    
    private array $settings;

    public function init(): void {
        add_shortcode('aa_footer', [$this, 'renderShortcode']);
        add_action('init', [$this, 'registerMenus']);
    }

    public function registerMenus(): void {
        register_nav_menus([
            'aa_footer_quick_links' => __('Al Aosaf Footer Quick Links', 'al-aosaf'),
            'aa_footer_customer_service' => __('Al Aosaf Footer Customer Service', 'al-aosaf')
        ]);
    }

    public function renderShortcode($atts): string {
        $this->settings = get_option('aa_footer_settings', []);
        
        if (($this->settings['general_enable'] ?? 'yes') !== 'yes') {
            return '';
        }

        ob_start();
        include AA_PLUGIN_DIR . 'modules/Footer/Views/frontend/wrapper.php';
        return ob_get_clean();
    }
}
