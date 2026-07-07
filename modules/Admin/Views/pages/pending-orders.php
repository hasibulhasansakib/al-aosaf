<?php
if (!defined('ABSPATH')) exit;

if (!class_exists('WooCommerce')) {
    echo '<div class="wrap"><p>WooCommerce is not active.</p></div>';
    return;
}

$args = [
    'limit' => -1,
    'status' => ['wc-processing', 'wc-on-hold'],
    'orderby' => 'date',
    'order' => 'DESC',
];
$orders = wc_get_orders($args);
?>
<div class="wrap aa-admin-wrap">
    <div class="aa-admin-header">
        <h1><?php _e('Pending Orders', 'al-aosaf'); ?></h1>
        <p><?php _e('Manage all your processing and on-hold orders from one place.', 'al-aosaf'); ?></p>
    </div>

    <div class="aa-admin-content">
        <div class="aa-table-wrapper">
            <table class="aa-premium-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 30px;">
                                <p style="color: #646970;">No pending orders found. Great job!</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <strong>#<?php echo $order->get_id(); ?></strong> 
                                    by <?php echo esc_html($order->get_formatted_billing_full_name()); ?>
                                </td>
                                <td><?php echo wc_format_datetime($order->get_date_created()); ?></td>
                                <td>
                                    <?php 
                                    $status = $order->get_status(); 
                                    $badge_class = 'aa-badge-' . $status;
                                    ?>
                                    <span class="aa-badge <?php echo $badge_class; ?>">
                                        <?php echo wc_get_order_status_name($status); ?>
                                    </span>
                                </td>
                                <td><strong><?php echo wp_kses_post($order->get_formatted_order_total()); ?></strong></td>
                                <td>
                                    <a href="<?php echo esc_url($order->get_edit_order_url()); ?>" class="aa-action-btn">
                                        View Order
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Base Styles */
.aa-admin-wrap {
    margin: 20px 20px 0 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
}

.aa-admin-header {
    background: #ffffff;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    border: 1px solid #e2e4e7;
    margin-bottom: 20px;
}

.aa-admin-header h1 {
    margin: 0 0 8px 0;
    font-size: 22px;
    font-weight: 700;
    color: #1d2327;
}

.aa-admin-header p {
    margin: 0;
    color: #646970;
    font-size: 14px;
}

/* Premium Table */
.aa-table-wrapper {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    border: 1px solid #e2e4e7;
    overflow: hidden;
}

.aa-premium-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

.aa-premium-table th {
    background: #f8f9fa;
    padding: 15px 25px;
    font-weight: 600;
    color: #1d2327;
    border-bottom: 1px solid #e2e4e7;
    font-size: 14px;
}

.aa-premium-table td {
    padding: 15px 25px;
    border-bottom: 1px solid #f0f0f1;
    color: #3c434a;
    font-size: 14px;
    vertical-align: middle;
}

.aa-premium-table tbody tr:hover {
    background: #fcfcfc;
}

.aa-premium-table tbody tr:last-child td {
    border-bottom: none;
}

/* Badges */
.aa-badge {
    display: inline-flex;
    align-items: center;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.aa-badge-processing {
    background: #e6f4ea;
    color: #1e8e3e;
}

.aa-badge-on-hold {
    background: #fff3e0;
    color: #f57c00;
}

/* Action Button */
.aa-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 15px;
    background: #f0f0f1;
    color: #2271b1;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s;
    border: 1px solid #c3c4c7;
}

.aa-action-btn:hover {
    background: #f6f7f7;
    border-color: #8c8f94;
    color: #135e96;
}
</style>
