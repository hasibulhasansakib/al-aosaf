<?php
if (!defined('ABSPATH')) exit;

$current_user = wp_get_current_user();
if (0 === $current_user->ID) return;

global $wp;
$current_page = isset($wp->query_vars['invoices']) && !empty($wp->query_vars['invoices']) ? absint($wp->query_vars['invoices']) : 1;
$orders_per_page = 10;

// Get customer orders
$customer_orders = wc_get_orders([
    'customer' => $current_user->ID,
    'status'   => ['wc-processing', 'wc-completed', 'wc-on-hold'],
    'limit'    => $orders_per_page,
    'page'     => $current_page,
    'paginate' => true,
]);

$orders = $customer_orders->orders;
$max_num_pages = $customer_orders->max_num_pages;

?>

<div class="aa-dashboard-card" style="margin-top: 0; border: none; box-shadow: none;">
    <div class="aa-card-header">
        <h3>My Invoices</h3>
    </div>

    <?php if (empty($orders)): ?>
        <p class="aa-no-orders">No invoices available yet.</p>
    <?php else: ?>
        <div class="aa-orders-list">
            <?php foreach ($orders as $order): 
                $invoice_url = site_url('/?aa_invoice=' . $order->get_id() . '&order_key=' . $order->get_order_key());
                $status = wc_get_order_status_name($order->get_status());
                $status_class = 'status-' . $order->get_status();
                $item_count = $order->get_item_count();
            ?>
            <div class="aa-order-item">
                <div class="aa-order-img">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px; color: #64748b;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <div class="aa-order-details">
                    <strong>Invoice #<?php echo esc_html($order->get_order_number()); ?></strong>
                    <span><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></span>
                </div>
                
                <div class="aa-order-status">
                    <span class="aa-badge <?php echo esc_attr($status_class); ?>"><?php echo esc_html($status); ?></span>
                </div>
                
                <div class="aa-order-meta-right">
                    <strong class="aa-order-total"><?php echo wp_kses_post($order->get_formatted_order_total()); ?></strong>
                    <span class="aa-order-items"><?php echo $item_count; ?> items</span>
                </div>
                
                <div class="aa-order-action-btn" style="margin-left: 20px; display: flex; gap: 8px;">
                    <a href="<?php echo esc_url($invoice_url); ?>" target="_blank" class="aa-btn-invoice" style="background: #f8fafc; color: #475569; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: 1px solid #e2e8f0; transition: all 0.3s;">View / Print</a>
                    <a href="<?php echo esc_url($invoice_url . '&action=download'); ?>" target="_blank" class="aa-btn-invoice" style="background: #eff6ff; color: #3b82f6; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: 1px solid #bfdbfe; transition: all 0.3s;">Download</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($max_num_pages > 1): ?>
        <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination" style="margin-top:20px; text-align:right; border-top: 1px solid #f1f5f9; padding-top: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div class="pagination-info" style="font-size: 13px; color: #64748b;">
                Page <?php echo $current_page; ?> of <?php echo $max_num_pages; ?>
            </div>
            <div class="pagination-buttons">
                <?php if (1 !== $current_page): ?>
                    <a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url(wc_get_endpoint_url('invoices', $current_page - 1)); ?>" style="background: #f1f5f9; color: #475569; padding: 8px 12px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px; margin-right: 4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg> Previous
                    </a>
                <?php endif; ?>
                
                <?php if (intval($max_num_pages) !== $current_page): ?>
                    <a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url(wc_get_endpoint_url('invoices', $current_page + 1)); ?>" style="margin-left:10px; background: #c59b5f; color: #fff; padding: 8px 12px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center;">
                        Next <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px; margin-left: 4px;"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
