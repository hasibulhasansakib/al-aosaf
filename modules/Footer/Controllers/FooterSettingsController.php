<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Footer\Controllers;

class FooterSettingsController {
    
    public function init(): void {
        add_action('aa_admin_page_footer', [$this, 'renderAdminPage']);
        add_action('admin_post_aa_save_footer_settings', [$this, 'saveSettings']);
    }

    public function renderAdminPage(): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        $settings = get_option('aa_footer_settings', []);
        
        include AA_PLUGIN_DIR . 'modules/Footer/Views/admin/settings.php';
    }

    public function saveSettings(): void {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        check_admin_referer('aa_save_footer_settings', 'aa_footer_nonce');

        $settings = get_option('aa_footer_settings', []);
        $current_tab = isset($_POST['current_tab']) ? sanitize_text_field($_POST['current_tab']) : 'general';

        if ($current_tab === 'general') {
            $settings['general_enable'] = sanitize_text_field($_POST['aa_footer_general_enable'] ?? 'yes');
        } elseif ($current_tab === 'newsletter') {
            $settings['newsletter_enable'] = sanitize_text_field($_POST['aa_footer_newsletter_enable'] ?? 'no');
            $settings['newsletter_title'] = sanitize_text_field($_POST['aa_footer_newsletter_title'] ?? '');
            $settings['newsletter_text'] = sanitize_textarea_field($_POST['aa_footer_newsletter_text'] ?? '');
        } elseif ($current_tab === 'content') {
            $settings['content_back_to_top'] = sanitize_text_field($_POST['aa_footer_content_back_to_top'] ?? 'no');
            $settings['content_payment_icons'] = sanitize_text_field($_POST['aa_footer_content_payment_icons'] ?? 'no');
        } elseif ($current_tab === 'copyright') {
            $settings['copyright_text'] = wp_kses_post($_POST['aa_footer_copyright_text'] ?? '');
        }

        update_option('aa_footer_settings', $settings);

        wp_redirect(admin_url('admin.php?page=aa-footer&tab=' . $current_tab . '&updated=true'));
        exit;
    }
}
