<?php
if (!defined('ABSPATH')) exit;

if (!class_exists('WooCommerce')) {
    echo '<div class="wrap"><p>WooCommerce is not active.</p></div>';
    return;
}

$out_of_stock_query = new WP_Query([
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_query' => [
        [
            'key' => '_stock_status',
            'value' => 'outofstock',
            'compare' => '='
        ]
    ]
]);
?>
<div class="wrap aa-admin-wrap">
    <div class="aa-admin-header">
        <h1><?php _e('Out of Stock Products', 'al-aosaf'); ?></h1>
        <p><?php _e('Manage all products that are currently out of stock.', 'al-aosaf'); ?></p>
    </div>

    <div class="aa-admin-content">
        <div class="aa-table-wrapper">
            <table class="aa-premium-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Stock Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$out_of_stock_query->have_posts()): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 30px;">
                                <p style="color: #646970;">All products are in stock. Great job!</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php while ($out_of_stock_query->have_posts()): $out_of_stock_query->the_post(); 
                            $product = wc_get_product(get_the_ID());
                        ?>
                            <tr>
                                <td style="width: 60px;">
                                    <?php echo $product->get_image([40, 40], ['style' => 'border-radius:6px; object-fit:cover;']); ?>
                                </td>
                                <td>
                                    <strong><?php echo esc_html($product->get_name()); ?></strong>
                                    <?php if ($product->get_sku()): ?>
                                        <br><span style="font-size:12px; color:#646970;">SKU: <?php echo esc_html($product->get_sku()); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo wp_kses_post($product->get_price_html()); ?></strong></td>
                                <td>
                                    <span class="aa-badge aa-badge-outofstock">
                                        Out of stock
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo admin_url('post.php?post=' . $product->get_id() . '&action=edit'); ?>" class="aa-action-btn">
                                        Update Stock
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; wp_reset_postdata(); ?>
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

.aa-badge-outofstock {
    background: #ffebee;
    color: #d32f2f;
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
