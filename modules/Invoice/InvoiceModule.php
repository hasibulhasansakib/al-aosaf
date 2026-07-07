<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Invoice;

use Alaosaf\Interfaces\ModuleInterface;

class InvoiceModule implements ModuleInterface {
    
    public function getModuleId(): string {
        return 'invoice';
    }

    public function init(): void {
        // Initialize Settings
        require_once __DIR__ . '/Admin/InvoiceSettings.php';
        $settings = new \Alaosaf\Modules\Invoice\Admin\InvoiceSettings();
        $settings->init();

        // Add Admin Menu
        add_action('admin_menu', [$this, 'addAdminMenu']);
        
        // Setup Invoice Routing
        add_filter('query_vars', [$this, 'addQueryVars']);
        add_action('template_redirect', [$this, 'renderInvoiceTemplate']);

        // Add Customer 'My Account' Invoices Endpoint
        add_action('init', [$this, 'addEndpoints']);
        add_filter('woocommerce_get_query_vars', [$this, 'addWooQueryVars']);
        add_filter('woocommerce_account_menu_items', [$this, 'addMenuItems']);
        add_action('woocommerce_account_invoices_endpoint', [$this, 'renderInvoicesEndpoint']);
        
        // Add Invoice Button to Customer 'My Account' -> Orders
        add_filter('woocommerce_my_account_my_orders_actions', [$this, 'addCustomerInvoiceButton'], 10, 2);
        
        // Add Invoice Button to Admin Orders List
        add_action('woocommerce_admin_order_actions_end', [$this, 'addAdminInvoiceButton']);

        // Auto Attach PDF to emails
        add_filter('woocommerce_email_attachments', [$this, 'attachPdfToEmail'], 10, 3);
    }

    public function attachPdfToEmail($attachments, $email_id, $order) {
        $enable_auto = get_option('aa_invoice_enable_auto_pdf', 'yes');
        if ($enable_auto !== 'yes') return $attachments;

        // Only attach to specific emails
        $allowed_emails = ['customer_processing_order', 'customer_completed_order'];
        if (!in_array($email_id, $allowed_emails)) return $attachments;

        if ($order instanceof \WC_Order) {
            require_once __DIR__ . '/Helpers/PdfGenerator.php';
            $pdf_path = \Alaosaf\Modules\Invoice\Helpers\PdfGenerator::generateInvoicePdf($order, 'F');
            if ($pdf_path && file_exists($pdf_path)) {
                $attachments[] = $pdf_path;
            }
        }
        return $attachments;
    }

    public function addAdminMenu(): void {
        add_submenu_page(
            'aa-dashboard',
            'Invoices',
            'Invoices',
            'manage_woocommerce',
            'aa-invoices',
            [$this, 'renderAdminInvoiceList']
        );

        add_submenu_page(
            'aa-dashboard',
            'Invoice Settings',
            'Invoice Settings',
            'manage_woocommerce',
            'aa-invoice-settings',
            [$this, 'renderAdminInvoiceSettings']
        );
    }

    public function renderAdminInvoiceSettings(): void {
        $view_path = AA_PLUGIN_DIR . 'modules/Invoice/Views/admin-invoice-settings.php';
        if (file_exists($view_path)) {
            include $view_path;
        } else {
            echo "<h2>Settings View Missing</h2>";
        }
    }

    public function renderAdminInvoiceList(): void {
        $view_path = AA_PLUGIN_DIR . 'modules/Invoice/Views/admin-invoice-list.php';
        if (file_exists($view_path)) {
            include $view_path;
        } else {
            echo "<h2>Admin Invoice List View Missing</h2>";
        }
    }

    public function addQueryVars($vars) {
        $vars[] = 'aa_invoice';
        $vars[] = 'order_key';
        $vars[] = 'action';
        return $vars;
    }

    public function renderInvoiceTemplate(): void {
        $order_id = get_query_var('aa_invoice');
        $order_key = get_query_var('order_key');
        $action = get_query_var('action'); // e.g. 'download'

        if (!empty($order_id)) {
            $order = wc_get_order($order_id);

            if (!$order) {
                wp_die('Invalid Order ID.');
            }

            // Security Check: Must be admin or have the correct order key
            $is_admin = current_user_can('manage_woocommerce');
            $valid_key = ($order->get_order_key() === $order_key);

            if (!$is_admin && !$valid_key) {
                wp_die('You do not have permission to view this invoice.');
            }

            if ($action === 'download') {
                require_once __DIR__ . '/Helpers/PdfGenerator.php';
                \Alaosaf\Modules\Invoice\Helpers\PdfGenerator::generateInvoicePdf($order, 'D'); // D for download
                exit;
            }

            // Render the A4 HTML template
            $template_path = AA_PLUGIN_DIR . 'modules/Invoice/Views/invoice-template.php';
            if (file_exists($template_path)) {
                include $template_path;
                exit; // Stop WordPress from loading the rest of the site
            } else {
                wp_die('Invoice template not found.');
            }
        }
    }

    public function addEndpoints() {
        add_rewrite_endpoint('invoices', EP_ROOT | EP_PAGES);
    }

    public function addWooQueryVars($vars) {
        $vars['invoices'] = 'invoices';
        return $vars;
    }

    public function addMenuItems($items) {
        $new_items = [];
        foreach ($items as $key => $item) {
            $new_items[$key] = $item;
            if ($key === 'orders') {
                $new_items['invoices'] = 'Invoices';
            }
        }
        return $new_items;
    }

    public function renderInvoicesEndpoint() {
        $view_path = AA_PLUGIN_DIR . 'modules/Invoice/Views/my-account-invoices.php';
        if (file_exists($view_path)) {
            include $view_path;
        } else {
            echo "<h3>My Invoices</h3><p>Invoices list template is missing.</p>";
        }
    }

    public function addCustomerInvoiceButton($actions, $order) {
        $actions['aa_invoice_btn'] = [
            'url'  => site_url('/?aa_invoice=' . $order->get_id() . '&order_key=' . $order->get_order_key()),
            'name' => 'Invoice'
        ];
        return $actions;
    }

    public function addAdminInvoiceButton($order) {
        $url = site_url('/?aa_invoice=' . $order->get_id() . '&order_key=' . $order->get_order_key());
        printf(
            '<a class="button tips invoice" href="%s" data-tip="%s" target="_blank" style="margin-left: 4px;">%s</a>',
            esc_url($url),
            esc_attr__('Print Invoice', 'al-aosaf'),
            '<span class="dashicons dashicons-media-document" style="margin-top:2px;"></span>'
        );
    }
}
