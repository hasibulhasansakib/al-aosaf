<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Homepage;

use Alaosaf\Interfaces\ModuleInterface;
use Alaosaf\Modules\Homepage\Controllers\HomepageShortcodeController;
use Alaosaf\Modules\Homepage\Controllers\HomepageSettingsController;

class HomepageModule implements ModuleInterface {
    public function init(): void {
        $settings = new HomepageSettingsController();
        $settings->init();

        $shortcode = new HomepageShortcodeController();
        $shortcode->init();

        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function enqueueAssets(): void {
        // Enqueue Swiper JS/CSS
        wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], '11.0.5');
        wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], '11.0.5', true);

        // Enqueue Custom Assets
        wp_enqueue_style('aa-homepage-css', AA_PLUGIN_URL . 'modules/Homepage/assets/css/homepage.css', ['swiper-css'], time() . rand());
        wp_enqueue_script('aa-homepage-js', AA_PLUGIN_URL . 'modules/Homepage/assets/js/homepage.js', ['jquery', 'swiper-js'], time() . rand(), true);
    }

    public function getModuleId(): string {
        return 'homepage';
    }
}
