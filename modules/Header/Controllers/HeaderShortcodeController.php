<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Header\Controllers;

class HeaderShortcodeController {
    
    private array $settings;

    public function init(): void {
        add_shortcode('aa_header', [$this, 'renderShortcode']);
        add_action('init', [$this, 'registerMenus']);
    }

    public function registerMenus(): void {
        register_nav_menus([
            'aa_primary_menu' => __('Al Aosaf Primary Menu', 'al-aosaf'),
            'aa_mobile_menu'  => __('Al Aosaf Mobile Menu', 'al-aosaf'),
            'aa_sidebar_menu' => __('Al Aosaf Sidebar Menu', 'al-aosaf')
        ]);
    }

    public function renderShortcode($atts): string {
        $this->settings = get_option('aa_header_settings', []);
        
        if (($this->settings['general_enable'] ?? 'yes') !== 'yes') {
            return '';
        }

        ob_start();
        include AA_PLUGIN_DIR . 'modules/Header/Views/frontend/wrapper.php';
        return ob_get_clean();
    }
}
