<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Footer;

use Alaosaf\Interfaces\ModuleInterface;
use Alaosaf\Modules\Footer\Controllers\FooterSettingsController;
use Alaosaf\Modules\Footer\Controllers\FooterShortcodeController;

class FooterModule implements ModuleInterface {
    public function init(): void {
        $this->seedDefaults();

        $settings = new FooterSettingsController();
        $settings->init();

        $shortcode = new FooterShortcodeController();
        $shortcode->init();

        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function enqueueAssets(): void {
        wp_enqueue_style('aa-footer-css', AA_PLUGIN_URL . 'modules/Footer/assets/css/footer.css', [], time() . rand());
        wp_enqueue_script('aa-footer-js', AA_PLUGIN_URL . 'modules/Footer/assets/js/footer.js', ['jquery'], time() . rand(), true);
    }

    private function seedDefaults(): void {
        if (get_option('aa_footer_settings', false) === false) {
            $defaults = [
                'general_enable' => 'yes',
                'newsletter_enable' => 'yes',
                'newsletter_title' => 'Join Our Newsletter',
                'newsletter_text' => 'Subscribe to get special offers, free giveaways, and once-in-a-lifetime deals.',
                'content_back_to_top' => 'yes',
                'content_payment_icons' => 'yes',
                'copyright_text' => '&copy; {year} Al Aosaf. All Rights Reserved.',
            ];
            add_option('aa_footer_settings', $defaults);
        }
    }

    public function getModuleId(): string {
        return 'footer';
    }
}
