<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Admin\Controllers;

use Alaosaf\Base\AbstractController;

class AdminMenuController extends AbstractController {
    
    private array $pages = [
        'dashboard' => 'Dashboard',
        'appearance' => 'Appearance',
        'header' => 'Header',
        'footer' => 'Footer',
        'homepage' => 'Homepage',
        'single-product' => 'Single Product',
        'checkout' => 'Checkout',
        'pending-orders' => 'Pending Orders',
        'out-of-stock' => 'Out of Stock'
    ];

    public function init(): void {
        add_action('admin_menu', [$this, 'registerAdminMenu']);
    }

    public function registerAdminMenu(): void {
        // Main menu position 56 (Below WooCommerce)
        add_menu_page(
            __('Al Aosaf', 'al-aosaf'),
            __('Al Aosaf', 'al-aosaf'),
            'manage_options',
            'aa-dashboard',
            [$this, 'renderPage'],
            'dashicons-admin-generic',
            56
        );

        // Submenus
        foreach ($this->pages as $slug => $title) {
            $menu_slug = 'aa-' . $slug;
            add_submenu_page(
                'aa-dashboard',
                $title . ' - ' . __('Al Aosaf', 'al-aosaf'),
                $title,
                'manage_options',
                $menu_slug,
                [$this, 'renderPage']
            );
        }
    }

    public function renderPage(): void {
        $this->requireCapability('manage_options');
        
        $current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 'aa-dashboard';
        $page_slug = str_replace('aa-', '', $current_page);
        
        $page_title = $this->pages[$page_slug] ?? 'Dashboard';

        // Load the reusable layout header
        include AA_PLUGIN_DIR . 'modules/Admin/Views/layout/header.php';
        
        // Load the specific page content
        $view_path = AA_PLUGIN_DIR . 'modules/Admin/Views/pages/' . $page_slug . '.php';
        
        if (has_action("aa_admin_page_{$page_slug}")) {
            do_action("aa_admin_page_{$page_slug}");
        } elseif (file_exists($view_path)) {
            include $view_path;
        } else {
            // Fallback placeholder
            echo '<div class="aa-page-content" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">';
            echo '<h2 style="margin-top:0;">' . esc_html($page_title) . '</h2>';
            echo '<p>' . __('This module settings page is under construction.', 'al-aosaf') . '</p>';
            echo '</div>';
        }

        // Load the reusable layout footer
        include AA_PLUGIN_DIR . 'modules/Admin/Views/layout/footer.php';
    }
}
