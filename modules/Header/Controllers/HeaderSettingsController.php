<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Header\Controllers;

use Alaosaf\Base\AbstractController;

class HeaderSettingsController extends AbstractController {
    
    public function init(): void {
        add_action('aa_admin_page_header', [$this, 'renderPage']);
        add_action('admin_post_aa_save_header_settings', [$this, 'saveSettings']);
    }

    public function renderPage(): void {
        $this->requireCapability('manage_options');
        $settings = get_option('aa_header_settings', []);
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        include AA_PLUGIN_DIR . 'modules/Header/Views/admin/settings.php';
    }

    public function saveSettings(): void {
        $this->requireCapability('manage_options');
        $this->verifyNonce($_POST['aa_header_nonce'] ?? '', 'aa_save_header_settings');

        $settings = get_option('aa_header_settings', []);

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'aa_header_') === 0) {
                $field_key = substr($key, 10);
                $settings[$field_key] = sanitize_text_field($value);
            }
        }

        update_option('aa_header_settings', $settings);

        wp_safe_redirect(admin_url('admin.php?page=aa-header&tab=' . sanitize_text_field($_POST['current_tab'] ?? 'general') . '&updated=true'));
        exit;
    }
}
