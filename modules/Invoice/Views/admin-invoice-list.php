<?php
if (!defined('ABSPATH')) exit;

if (!current_user_can('manage_woocommerce')) {
    wp_die('Unauthorized');
}

// Handle Search and Filter
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$status_filter = isset($_GET['aa_status']) ? sanitize_text_field($_GET['aa_status']) : '';
$date_filter = isset($_GET['aa_date']) ? sanitize_text_field($_GET['aa_date']) : '';

// Stats Calculation
$total_processing = wc_orders_count('processing');
$total_completed = wc_orders_count('completed');
$total_on_hold = wc_orders_count('on-hold');
$total_orders_count = $total_processing + $total_completed + $total_on_hold;

// Query Arguments
$paged = isset($_GET['aa_paged']) ? max(1, intval($_GET['aa_paged'])) : 1;
$per_page = 20;

$args = [
    'limit' => $per_page,
    'page' => $paged,
    'orderby' => 'date',
    'order' => 'DESC',
    'return' => 'objects',
    'paginate' => true,
];

if (!empty($status_filter)) {
    $args['status'] = $status_filter;
}

if (!empty($date_filter)) {
    // Filter by exact date Y-m-d
    $args['date_created'] = $date_filter . '...'; // Matches anything from start to end of that day
}

if (!empty($search_query)) {
    // Basic search handling
    $clean_search = str_replace('#', '', $search_query);
    if (is_numeric($clean_search)) {
        // If it's a number, try to find by ID
        $args['post__in'] = [(int)$clean_search];
    } else {
        // Advanced search by name or email (split by space to match partial names like "Hasibul Hasan")
        $search_terms = explode(' ', $clean_search);
        $meta_query = ['relation' => 'AND'];
        
        foreach ($search_terms as $term) {
            $term = trim($term);
            if (empty($term)) continue;
            
            $meta_query[] = [
                'relation' => 'OR',
                ['_billing_first_name', 'LIKE', $term],
                ['_billing_last_name', 'LIKE', $term],
                ['_billing_email', 'LIKE', $term],
                ['_billing_phone', 'LIKE', $term]
            ];
        }
        $args['meta_query'] = $meta_query;
    }
}

$results = wc_get_orders($args);
$orders = $results->orders;
$total_pages = $results->max_num_pages;
$total_results = $results->total;
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .aa-invoice-dashboard {
        font-family: 'Inter', sans-serif;
        margin-top: 20px;
        margin-right: 20px;
    }
    .aa-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .aa-header h1 {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    
    /* Stats Cards */
    .aa-dashboard-cards {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }
    .aa-card {
        background: #fff;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        flex: 1;
        border-left: 5px solid #c59b5f;
        display: flex;
        flex-direction: column;
    }
    .aa-card:nth-child(2) { border-left-color: #3b82f6; }
    .aa-card:nth-child(3) { border-left-color: #10b981; }
    .aa-card:nth-child(4) { border-left-color: #f59e0b; }
    
    .aa-card-title {
        font-size: 13px;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }
    .aa-card-value {
        font-size: 32px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1;
    }

    /* Toolbar (Search & Filter) */
    .aa-toolbar {
        background: #fff;
        padding: 15px 20px;
        border-radius: 10px 10px 0 0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
    }
    .aa-toolbar-form {
        display: flex;
        gap: 15px;
        align-items: center;
    }
    .aa-input {
        padding: 10px 15px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 14px;
        color: #334155;
        min-width: 250px;
        transition: border-color 0.2s;
    }
    .aa-input[type="date"] {
        min-width: auto;
    }
    .aa-input:focus, .aa-select:focus {
        border-color: #c59b5f;
        outline: none;
        box-shadow: 0 0 0 2px rgba(197, 155, 95, 0.2);
    }
    .aa-select {
        padding: 10px 15px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 14px;
        color: #334155;
        background: #fff;
    }
    .aa-btn {
        background: #c59b5f;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
        display: inline-block;
        font-size: 13px;
    }
    .aa-btn:hover {
        background: #b48a50;
        color: #fff;
    }
    .aa-btn-outline {
        background: transparent;
        color: #334155;
        border: 1px solid #cbd5e1;
    }
    .aa-btn-outline:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    /* Table */
    .aa-table-container {
        background: #fff;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }
    .aa-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .aa-table th {
        background: #f8fafc;
        padding: 15px 20px;
        font-size: 12px;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 600;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
    }
    .aa-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #334155;
        vertical-align: middle;
    }
    .aa-table tr:last-child td {
        border-bottom: none;
    }
    .aa-table tr:hover td {
        background: #f8fafc;
    }
    
    .aa-checkbox {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        border: 1px solid #cbd5e1;
        cursor: pointer;
    }

    .aa-order-id {
        font-weight: 700;
        color: #0f172a;
        text-decoration: none;
    }
    .aa-order-id:hover {
        color: #c59b5f;
    }
    .aa-customer-name {
        display: block;
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }

    .aa-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    .aa-badge.processing { background: #dbeafe; color: #1e3a8a; }
    .aa-badge.completed { background: #d1fae5; color: #065f46; }
    .aa-badge.on-hold { background: #fef3c7; color: #92400e; }
    .aa-badge.default { background: #f1f5f9; color: #475569; }

    .aa-actions {
        display: flex;
        gap: 8px;
    }
    .aa-btn-icon {
        padding: 8px 12px;
        font-size: 12px;
    }

    /* Pagination */
    .aa-pagination {
        padding: 15px 20px;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .aa-page-info {
        font-size: 13px;
        color: #64748b;
    }
    .aa-page-links {
        display: flex;
        gap: 5px;
    }
    .aa-page-links a, .aa-page-links span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        padding: 0 8px;
        height: 32px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        text-decoration: none;
        border: 1px solid #cbd5e1;
        transition: all 0.2s;
    }
    .aa-page-links a:hover {
        border-color: #c59b5f;
        color: #c59b5f;
    }
    .aa-page-links span.current {
        background: #c59b5f;
        color: #fff;
        border-color: #c59b5f;
    }
</style>

<div class="aa-invoice-dashboard">
    <div class="aa-header">
        <h1>Al Aosaf Invoices</h1>
        <a href="<?php echo admin_url('admin.php?page=aa-invoice-settings'); ?>" class="aa-btn aa-btn-outline">
            <span class="dashicons dashicons-admin-settings" style="margin-top:2px;"></span> Settings
        </a>
    </div>

    <!-- Mini Dashboard -->
    <div class="aa-dashboard-cards">
        <div class="aa-card">
            <div class="aa-card-title">Total Active Orders</div>
            <div class="aa-card-value"><?php echo number_format($total_orders_count); ?></div>
        </div>
        <div class="aa-card">
            <div class="aa-card-title">Processing</div>
            <div class="aa-card-value"><?php echo number_format($total_processing); ?></div>
        </div>
        <div class="aa-card">
            <div class="aa-card-title">Completed</div>
            <div class="aa-card-value"><?php echo number_format($total_completed); ?></div>
        </div>
        <div class="aa-card">
            <div class="aa-card-title">On Hold</div>
            <div class="aa-card-value"><?php echo number_format($total_on_hold); ?></div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="aa-toolbar">
        <form method="GET" action="admin.php" class="aa-toolbar-form">
            <input type="hidden" name="page" value="aa-invoices">
            <input type="text" name="s" class="aa-input" placeholder="Search by ID, Name or Email..." value="<?php echo esc_attr($search_query); ?>">
            <input type="date" name="aa_date" class="aa-input" value="<?php echo esc_attr($date_filter); ?>">
            <select name="aa_status" class="aa-select">
                <option value="">All Statuses</option>
                <option value="processing" <?php selected($status_filter, 'processing'); ?>>Processing</option>
                <option value="completed" <?php selected($status_filter, 'completed'); ?>>Completed</option>
                <option value="on-hold" <?php selected($status_filter, 'on-hold'); ?>>On Hold</option>
                <option value="pending" <?php selected($status_filter, 'pending'); ?>>Pending Payment</option>
                <option value="cancelled" <?php selected($status_filter, 'cancelled'); ?>>Cancelled</option>
            </select>
            <button type="submit" class="aa-btn">Filter & Search</button>
            <?php if (!empty($search_query) || !empty($status_filter) || !empty($date_filter)): ?>
                <a href="<?php echo admin_url('admin.php?page=aa-invoices'); ?>" class="aa-btn aa-btn-outline">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Data Table -->
    <div class="aa-table-container">
        <table class="aa-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">
                        <input type="checkbox" id="aa-select-all" class="aa-checkbox">
                    </th>
                    <th>Order & Customer</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                            <span class="dashicons dashicons-search" style="font-size: 40px; height: 40px; width: 40px; margin-bottom: 15px; opacity: 0.5;"></span><br>
                            No invoices found matching your criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): 
                        $order_id = $order->get_id();
                        $order_url = admin_url('post.php?post=' . $order_id . '&action=edit');
                        $invoice_url = site_url('/?aa_invoice=' . $order_id . '&order_key=' . $order->get_order_key());
                        
                        $status = $order->get_status();
                        $badge_class = in_array($status, ['processing', 'completed', 'on-hold']) ? $status : 'default';
                    ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="invoice_ids[]" value="<?php echo esc_attr($order_id); ?>" class="aa-checkbox aa-invoice-select">
                            </td>
                            <td>
                                <a href="<?php echo esc_url($order_url); ?>" class="aa-order-id">#<?php echo $order->get_order_number(); ?></a>
                                <span class="aa-customer-name"><?php echo esc_html($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()); ?> (<?php echo esc_html($order->get_billing_email()); ?>)</span>
                            </td>
                            <td>
                                <?php echo esc_html(wc_format_datetime($order->get_date_created())); ?>
                            </td>
                            <td>
                                <span class="aa-badge <?php echo esc_attr($badge_class); ?>">
                                    <?php echo esc_html(wc_get_order_status_name($status)); ?>
                                </span>
                            </td>
                            <td>
                                <strong><?php echo wp_kses_post($order->get_formatted_order_total()); ?></strong>
                            </td>
                            <td style="text-align: right;">
                                <div class="aa-actions" style="justify-content: flex-end;">
                                    <a href="<?php echo esc_url($invoice_url); ?>" target="_blank" class="aa-btn aa-btn-outline aa-btn-icon">
                                        View/Print
                                    </a>
                                    <a href="<?php echo esc_url($invoice_url . '&action=download'); ?>" target="_blank" class="aa-btn aa-btn-icon">
                                        Download PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if ($total_pages > 1): ?>
        <div class="aa-pagination">
            <div class="aa-page-info">
                Showing page <?php echo $paged; ?> of <?php echo $total_pages; ?> (<?php echo $total_results; ?> total invoices)
            </div>
            <div class="aa-page-links">
                <?php
                echo paginate_links([
                    'base' => add_query_arg('aa_paged', '%#%'),
                    'format' => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total' => $total_pages,
                    'current' => $paged,
                ]);
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('aa-select-all').addEventListener('change', function(e) {
    var checkboxes = document.querySelectorAll('.aa-invoice-select');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = e.target.checked;
    }
});
</script>
