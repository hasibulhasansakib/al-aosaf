<?php
if (!defined('ABSPATH')) exit;

// Gather Quick Stats
$sales_today = 0;
$pending_orders = 0;
$out_of_stock = 0;
$total_products = 0;

if (class_exists('WooCommerce')) {
    // Sales Today
    $orders_today = wc_get_orders([
        'limit' => -1,
        'status' => ['wc-completed', 'wc-processing', 'wc-on-hold'],
        'date_created' => date('Y-m-d') . '...' . date('Y-m-d')
    ]);
    foreach ($orders_today as $order) {
        $sales_today += $order->get_total();
    }

    // Pending Orders
    $pending_orders = wc_orders_count('processing') + wc_orders_count('on-hold');

    // Out of Stock Products
    $out_of_stock_query = new WP_Query([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => [
            [
                'key' => '_stock_status',
                'value' => 'outofstock',
                'compare' => '='
            ]
        ]
    ]);
    $out_of_stock = $out_of_stock_query->found_posts;
    
    // Total Products
    $total_products_query = new WP_Query([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 1
    ]);
    $total_products = $total_products_query->found_posts;
}
?>

<div class="wrap aa-dashboard-wrap">
    <div class="aa-dashboard-header">
        <div class="aa-dh-text">
            <h1><?php _e('Welcome to Al Aosaf', 'al-aosaf'); ?> 👋</h1>
            <p><?php _e('Your central control hub for managing the store\'s design, features, and performance.', 'al-aosaf'); ?></p>
        </div>
        <div class="aa-dh-logo">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="aa-logo-icon"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
        </div>
    </div>

    <div class="aa-dashboard-grid">
        
        <!-- Left Column -->
        <div class="aa-grid-left">
            
            <!-- Quick Stats -->
            <div class="aa-dashboard-card aa-stats-card">
                <div class="aa-card-header">
                    <h2><?php _e('Store Overview (Today)', 'al-aosaf'); ?></h2>
                </div>
                <div class="aa-stats-grid">
                    <div class="aa-stat-box">
                        <div class="aa-stat-icon aa-icon-blue">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        </div>
                        <div class="aa-stat-content">
                            <span class="aa-stat-label">Sales Today</span>
                            <span class="aa-stat-value"><?php echo wc_price($sales_today); ?></span>
                        </div>
                    </div>
                    <a href="<?php echo admin_url('admin.php?page=aa-pending-orders'); ?>" class="aa-stat-box aa-clickable-box" style="text-decoration:none; color:inherit;">
                        <div class="aa-stat-icon aa-icon-orange">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                        </div>
                        <div class="aa-stat-content">
                            <span class="aa-stat-label">Pending Orders</span>
                            <span class="aa-stat-value"><?php echo esc_html($pending_orders); ?></span>
                        </div>
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=aa-out-of-stock'); ?>" class="aa-stat-box aa-clickable-box" style="text-decoration:none; color:inherit;">
                        <div class="aa-stat-icon aa-icon-red">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        </div>
                        <div class="aa-stat-content">
                            <span class="aa-stat-label">Out of Stock</span>
                            <span class="aa-stat-value"><?php echo esc_html($out_of_stock); ?></span>
                        </div>
                    </a>
                    <div class="aa-stat-box">
                        <div class="aa-stat-icon aa-icon-green">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        </div>
                        <div class="aa-stat-content">
                            <span class="aa-stat-label">Total Products</span>
                            <span class="aa-stat-value"><?php echo esc_html($total_products); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="aa-dashboard-card">
                <div class="aa-card-header">
                    <h2><?php _e('Quick Shortcuts', 'al-aosaf'); ?></h2>
                </div>
                <div class="aa-shortcuts-grid">
                    <a href="<?php echo admin_url('admin.php?page=aa-homepage'); ?>" class="aa-shortcut-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        Edit Homepage
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=aa-header'); ?>" class="aa-shortcut-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line></svg>
                        Header Settings
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=aa-footer'); ?>" class="aa-shortcut-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="15" x2="21" y2="15"></line></svg>
                        Footer Settings
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=aa-checkout'); ?>" class="aa-shortcut-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        Checkout Fields
                    </a>
                    <a href="<?php echo admin_url('post-new.php?post_type=product'); ?>" class="aa-shortcut-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Add Product
                    </a>
                    <a href="<?php echo admin_url('edit.php?post_type=shop_order'); ?>" class="aa-shortcut-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        All Orders
                    </a>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="aa-grid-right">
            
            <!-- System Health -->
            <div class="aa-dashboard-card aa-system-card">
                <div class="aa-card-header">
                    <h2><?php _e('System Health', 'al-aosaf'); ?></h2>
                </div>
                <ul class="aa-system-list">
                    <li>
                        <div class="aa-sl-info">
                            <strong>Al Aosaf Framework</strong>
                            <span>Version 1.0.0</span>
                        </div>
                        <div class="aa-sl-status success">Active</div>
                    </li>
                    <li>
                        <div class="aa-sl-info">
                            <strong>WooCommerce</strong>
                            <span><?php echo class_exists('WooCommerce') ? WC()->version : 'Not Installed'; ?></span>
                        </div>
                        <div class="aa-sl-status <?php echo class_exists('WooCommerce') ? 'success' : 'error'; ?>">
                            <?php echo class_exists('WooCommerce') ? 'Active' : 'Missing'; ?>
                        </div>
                    </li>
                    <li>
                        <div class="aa-sl-info">
                            <strong>PHP Version</strong>
                            <span><?php echo phpversion(); ?></span>
                        </div>
                        <div class="aa-sl-status <?php echo version_compare(phpversion(), '7.4', '>=') ? 'success' : 'warning'; ?>">
                            <?php echo version_compare(phpversion(), '7.4', '>=') ? 'Good' : 'Update'; ?>
                        </div>
                    </li>
                    <li>
                        <div class="aa-sl-info">
                            <strong>Memory Limit</strong>
                            <span><?php echo WP_MEMORY_LIMIT; ?></span>
                        </div>
                        <div class="aa-sl-status success">Good</div>
                    </li>
                </ul>
            </div>

            <!-- Help / Support -->
            <div class="aa-dashboard-card aa-support-card" style="background: linear-gradient(135deg, #111 0%, #333 100%); color: #fff;">
                <div class="aa-card-header" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h2 style="color: #fff;"><?php _e('Need Help?', 'al-aosaf'); ?></h2>
                </div>
                <div class="aa-support-content" style="padding: 20px;">
                    <p style="color: #ccc; margin-bottom: 20px;">If you face any issues with the design or functionality, feel free to reach out to the development team.</p>
                    <a href="https://wa.me/8801724212748" target="_blank" class="aa-support-btn" style="display: inline-block; background: var(--aa-primary, #C8A15A); color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600;">Contact Developer</a>
                </div>
            </div>

        </div>

    </div>
</div>

<style>
/* Dashboard Styling */
.aa-dashboard-wrap {
    margin: 20px 20px 0 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
}

.aa-dashboard-header {
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    border: 1px solid #e2e4e7;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.aa-dh-text h1 {
    margin: 0 0 8px 0;
    font-size: 24px;
    font-weight: 700;
    color: #1d2327;
}

.aa-dh-text p {
    margin: 0;
    font-size: 15px;
    color: #50575e;
}

.aa-dh-logo {
    width: 60px;
    height: 60px;
    background: #f0f0f1;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--aa-primary, #C8A15A);
}

/* Grid Layout */
.aa-dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}

.aa-dashboard-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    border: 1px solid #e2e4e7;
    margin-bottom: 20px;
    overflow: hidden;
}

.aa-card-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e2e4e7;
}

.aa-card-header h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #1d2327;
}

/* Stats */
.aa-stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    padding: 25px;
}

.aa-stat-box {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #f0f0f1;
    transition: all 0.2s ease;
}

a.aa-stat-box.aa-clickable-box:hover {
    background: #ffffff;
    border-color: var(--aa-primary, #C8A15A);
    box-shadow: 0 4px 10px rgba(200, 161, 90, 0.1);
    transform: translateY(-2px);
}

.aa-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.aa-icon-blue { background: #e3f2fd; color: #1976d2; }
.aa-icon-orange { background: #fff3e0; color: #f57c00; }
.aa-icon-red { background: #ffebee; color: #d32f2f; }
.aa-icon-green { background: #e8f5e9; color: #388e3c; }

.aa-stat-content {
    display: flex;
    flex-direction: column;
}

.aa-stat-label {
    font-size: 13px;
    color: #646970;
    margin-bottom: 4px;
    font-weight: 500;
}

.aa-stat-value {
    font-size: 20px;
    font-weight: 700;
    color: #1d2327;
}

/* Shortcuts */
.aa-shortcuts-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 25px;
}

.aa-shortcut-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 20px;
    background: #f8f9fa;
    border: 1px solid #f0f0f1;
    border-radius: 10px;
    text-decoration: none;
    color: #3c434a;
    font-weight: 500;
    transition: all 0.2s ease;
}

.aa-shortcut-btn:hover {
    background: #ffffff;
    border-color: var(--aa-primary, #C8A15A);
    color: var(--aa-primary, #C8A15A);
    box-shadow: 0 4px 10px rgba(200, 161, 90, 0.1);
    transform: translateY(-2px);
}

/* System Health */
.aa-system-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.aa-system-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 25px;
    border-bottom: 1px solid #f0f0f1;
}

.aa-system-list li:last-child {
    border-bottom: none;
}

.aa-sl-info {
    display: flex;
    flex-direction: column;
}

.aa-sl-info strong {
    font-size: 14px;
    color: #1d2327;
    margin-bottom: 2px;
}

.aa-sl-info span {
    font-size: 12px;
    color: #646970;
}

.aa-sl-status {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.aa-sl-status.success { background: #e6f4ea; color: #1e8e3e; }
.aa-sl-status.error { background: #fce8e6; color: #d93025; }
.aa-sl-status.warning { background: #fef7e0; color: #f29900; }

@media (max-width: 1200px) {
    .aa-dashboard-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .aa-stats-grid {
        grid-template-columns: 1fr;
    }
    .aa-shortcuts-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
