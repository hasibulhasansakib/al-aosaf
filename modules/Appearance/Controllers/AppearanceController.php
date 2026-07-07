<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Appearance\Controllers;

use Alaosaf\Base\AbstractController;
use Alaosaf\Helpers\Brand;

class AppearanceController extends AbstractController {
    
    public function init(): void {
        add_action('aa_admin_page_appearance', [$this, 'renderPage']);
        add_action('admin_post_aa_save_brand_settings', [$this, 'saveSettings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('wp_head', [$this, 'outputDynamicCss'], 10);
        add_action('admin_head', [$this, 'outputDynamicCss'], 10);
    }

    public function enqueueAdminAssets(string $hook): void {
        if (isset($_GET['page']) && $_GET['page'] === 'aa-appearance') {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_media();
            wp_enqueue_script('aa-appearance-admin', AA_PLUGIN_URL . 'modules/Appearance/assets/js/admin.js', ['wp-color-picker', 'jquery'], AA_VERSION, true);
        }
    }

    public function renderPage(): void {
        $this->requireCapability('manage_options');
        
        $settings = get_option('aa_brand_settings', []);
        
        // Define default tabs and fields for rendering
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        
        include AA_PLUGIN_DIR . 'modules/Appearance/Views/settings.php';
    }

    public function saveSettings(): void {
        $this->requireCapability('manage_options');
        $this->verifyNonce($_POST['aa_brand_nonce'] ?? '', 'aa_save_brand_settings');

        $settings = get_option('aa_brand_settings', []);

        // Sanitize and save fields based on the submitted POST array
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'aa_brand_') === 0) {
                // Strip the prefix
                $field_key = substr($key, 9);
                $settings[$field_key] = sanitize_text_field($value);
            }
        }

        update_option('aa_brand_settings', $settings);

        wp_safe_redirect(admin_url('admin.php?page=aa-appearance&tab=' . sanitize_text_field($_POST['current_tab'] ?? 'general') . '&updated=true'));
        exit;
    }

    public function outputDynamicCss(): void {
        echo "<style id='aa-dynamic-brand-css'>\n";
        echo ":root {\n";
        echo "  --aa-primary: " . esc_attr(Brand::primaryColor()) . ";\n";
        echo "  --aa-primary-hover: " . esc_attr(Brand::primaryHover()) . ";\n";
        echo "  --aa-secondary: " . esc_attr(Brand::secondaryColor()) . ";\n";
        echo "  --aa-background: " . esc_attr(Brand::backgroundColor()) . ";\n";
        echo "  --aa-surface: " . esc_attr(Brand::surfaceColor()) . ";\n";
        echo "  --aa-text: " . esc_attr(Brand::textColor()) . ";\n";
        echo "  --aa-muted: " . esc_attr(Brand::mutedText()) . ";\n";
        echo "  --aa-border: " . esc_attr(Brand::borderColor()) . ";\n";
        echo "}\n";
        echo "</style>\n";
    }
}
