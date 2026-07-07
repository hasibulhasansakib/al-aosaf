<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Invoice\Admin;

class InvoiceSettings {

    public function init(): void {
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function registerSettings(): void {
        // Register options
        register_setting('aa_invoice_settings_group', 'aa_invoice_enable_auto_pdf');
        register_setting('aa_invoice_settings_group', 'aa_invoice_company_name');
        register_setting('aa_invoice_settings_group', 'aa_invoice_company_address');
        register_setting('aa_invoice_settings_group', 'aa_invoice_company_email');
        register_setting('aa_invoice_settings_group', 'aa_invoice_company_phone');
        register_setting('aa_invoice_settings_group', 'aa_invoice_custom_logo');
        register_setting('aa_invoice_settings_group', 'aa_invoice_footer_text');
        register_setting('aa_invoice_settings_group', 'aa_invoice_show_shipping');
        
        // Add Section
        add_settings_section(
            'aa_invoice_main_section',
            'General Invoice Settings',
            null,
            'aa-invoice-settings'
        );

        // Add Fields
        add_settings_field('aa_invoice_enable_auto_pdf', 'Enable Auto PDF Attachment', [$this, 'renderCheckbox'], 'aa-invoice-settings', 'aa_invoice_main_section', ['id' => 'aa_invoice_enable_auto_pdf']);
        add_settings_field('aa_invoice_custom_logo', 'Custom Logo URL', [$this, 'renderText'], 'aa-invoice-settings', 'aa_invoice_main_section', ['id' => 'aa_invoice_custom_logo', 'desc' => 'Enter the URL of your logo image. Leave blank to use site title.']);
        add_settings_field('aa_invoice_company_name', 'Company Name', [$this, 'renderText'], 'aa-invoice-settings', 'aa_invoice_main_section', ['id' => 'aa_invoice_company_name']);
        add_settings_field('aa_invoice_company_email', 'Company Email', [$this, 'renderText'], 'aa-invoice-settings', 'aa_invoice_main_section', ['id' => 'aa_invoice_company_email']);
        add_settings_field('aa_invoice_company_phone', 'Company Phone', [$this, 'renderText'], 'aa-invoice-settings', 'aa_invoice_main_section', ['id' => 'aa_invoice_company_phone']);
        add_settings_field('aa_invoice_company_address', 'Company Address', [$this, 'renderTextarea'], 'aa-invoice-settings', 'aa_invoice_main_section', ['id' => 'aa_invoice_company_address']);
        add_settings_field('aa_invoice_show_shipping', 'Show Shipping Address', [$this, 'renderCheckbox'], 'aa-invoice-settings', 'aa_invoice_main_section', ['id' => 'aa_invoice_show_shipping']);
        add_settings_field('aa_invoice_footer_text', 'Footer / Terms', [$this, 'renderTextarea'], 'aa-invoice-settings', 'aa_invoice_main_section', ['id' => 'aa_invoice_footer_text']);
    }

    public function renderCheckbox(array $args): void {
        $id = $args['id'];
        $value = get_option($id, 'yes');
        $checked = checked($value, 'yes', false);
        echo "<input type='checkbox' name='{$id}' value='yes' {$checked} />";
    }

    public function renderText(array $args): void {
        $id = $args['id'];
        $desc = $args['desc'] ?? '';
        $value = get_option($id, '');
        echo "<input type='text' name='{$id}' value='" . esc_attr($value) . "' class='regular-text' />";
        if ($desc) {
            echo "<p class='description'>{$desc}</p>";
        }
    }

    public function renderTextarea(array $args): void {
        $id = $args['id'];
        $value = get_option($id, '');
        echo "<textarea name='{$id}' rows='5' cols='50' class='large-text'>" . esc_textarea($value) . "</textarea>";
    }
}
