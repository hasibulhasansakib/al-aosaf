<?php
if (!defined('ABSPATH')) exit;

/**
 * @var WC_Order $order
 */
if (!isset($order)) return;

$company_name = get_option('aa_invoice_company_name', '');
if (empty($company_name)) $company_name = get_bloginfo('name');
if (empty($company_name)) $company_name = 'AL AOSAF';

$company_email = get_option('aa_invoice_company_email', get_option('admin_email'));
$company_phone = get_option('aa_invoice_company_phone', '');
$custom_logo_url = get_option('aa_invoice_custom_logo', '');

$custom_address = get_option('aa_invoice_company_address', '');
if (!empty($custom_address)) {
    $full_store_address = nl2br(esc_html($custom_address));
} else {
    $store_address = get_option('woocommerce_store_address', '');
    $store_city = get_option('woocommerce_store_city', '');
    $store_postcode = get_option('woocommerce_store_postcode', '');
    $full_store_address = trim($store_address . ', ' . $store_city . ' ' . $store_postcode, ', ');
}

$show_shipping = get_option('aa_invoice_show_shipping', 'yes') === 'yes';
$footer_text = get_option('aa_invoice_footer_text', 'Thank you for shopping with us!');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - #<?php echo $order->get_order_number(); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #c59b5f; /* Richer Gold */
            --primary-light: #f8fafc; /* Crisp, clean light slate/gray */
            --text-dark: #0f172a; /* Deep Slate instead of harsh black */
            --text-main: #334155;
            --text-light: #64748b;
            --border: #e2e8f0;
        }
        body {
            font-family: 'Inter', 'Hind Siliguri', sans-serif;
            color: var(--text-main);
            margin: 0;
            padding: 0;
            background: #f1f5f9;
            -webkit-font-smoothing: antialiased;
        }
        .invoice-wrapper {
            max-width: 21cm;
            margin: 1rem auto;
            background: #ffffff;
            padding: 1.5cm 2cm;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border-top: 6px solid var(--primary);
        }
        /* Header */
        .header-table {
            width: 100%;
            margin-bottom: 1.5rem;
        }
        .header-logo {
            width: 50%;
            vertical-align: top;
        }
        .header-logo img {
            max-height: 60px;
            width: auto;
        }
        .text-logo {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 3px;
            margin: 0;
            line-height: 1;
        }
        .text-logo span {
            color: var(--primary);
        }
        .company-details {
            width: 50%;
            text-align: right;
            font-size: 13px;
            line-height: 1.6;
            color: var(--text-light);
            vertical-align: top;
        }
        .company-details strong {
            color: var(--text-dark);
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
        }
        
        /* Invoice Title & Meta Box */
        .invoice-meta-table {
            width: 100%;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
            padding-bottom: 1.2rem;
        }
        .invoice-title-cell {
            width: 50%;
            vertical-align: bottom;
        }
        .invoice-meta-cell {
            width: 50%;
            vertical-align: bottom;
            text-align: right;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: 300;
            margin: 0;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 5px;
        }
        .meta-row {
            margin-bottom: 4px;
            font-size: 12px;
        }
        .meta-row span.label {
            color: var(--text-light);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
            margin-right: 10px;
            display: inline-block;
            width: 110px;
        }
        .meta-row span.value {
            font-weight: 600;
            color: var(--text-dark);
            display: inline-block;
            width: 140px;
        }

        /* Billing / Shipping */
        .addresses-table {
            width: 100%;
            margin-bottom: 1.5rem;
            background: var(--primary-light);
            padding: 1.2rem;
            border-radius: 8px;
        }
        .address-box {
            width: 50%;
            vertical-align: top;
            padding-right: 1.5rem;
        }
        .address-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--primary);
            margin-bottom: 8px;
        }
        .address-content {
            font-size: 13px;
            line-height: 1.6;
            color: var(--text-main);
        }
        .address-content strong {
            color: var(--text-dark);
        }

        /* Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        .items-table th {
            padding: 10px 15px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
            color: var(--text-dark);
            border-bottom: 1px solid var(--primary);
        }
        .items-table th.text-right {
            text-align: right;
        }
        .items-table th.col-desc { width: 55%; }
        .items-table th.col-qty { width: 10%; text-align: center; }
        .items-table th.col-price { width: 15%; text-align: right; }
        .items-table th.col-total { width: 20%; text-align: right; }

        .items-table td {
            padding: 10px 15px;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
            vertical-align: middle;
        }
        .items-table td.text-right {
            text-align: right;
        }
        .product-name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 14px;
            margin-bottom: 4px;
        }
        .product-meta {
            font-size: 12px;
            color: var(--text-light);
        }

        /* Totals */
        .totals-table {
            width: 100%;
            margin-bottom: 1.5rem;
        }
        .totals-empty {
            width: 55%;
        }
        .totals-content {
            width: 45%;
        }
        .totals-items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-items-table td {
            padding: 8px 15px;
            font-size: 13px;
            color: var(--text-main);
            border-bottom: 1px solid var(--border);
        }
        .totals-items-table tr:last-child td {
            border-bottom: none;
        }
        .totals-items-table td:first-child {
            color: var(--text-light);
        }
        .totals-items-table td:last-child {
            text-align: right;
        }
        .grand-total td {
            font-size: 16px !important;
            color: #ffffff !important;
            margin-top: 5px;
            background: var(--text-dark);
            font-weight: 700;
        }

        /* Footer */
        .invoice-footer {
            text-align: center;
            font-size: 12px;
            color: var(--text-light);
            padding-top: 1rem;
            border-top: 1px solid var(--border);
            line-height: 1.6;
        }

        /* Print Styles */
        @media print {
            @page { size: A4; margin: 0; }
            body { background: none; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .invoice-wrapper { box-shadow: none; margin: 0; padding: 1.5cm 2cm; max-width: 100%; border-top: 8px solid var(--primary); }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="invoice-wrapper">
        
        <table class="header-table">
            <tr>
            <td class="header-logo">
                <?php if ($custom_logo_url): ?>
                    <img src="<?php echo esc_url($custom_logo_url); ?>" alt="<?php echo esc_attr($company_name); ?>">
                <?php else: ?>
                    <div class="text-logo"><?php 
                        // Split name to colorize the last part if multiple words
                        $parts = explode(' ', $company_name);
                        if (count($parts) > 1) {
                            $last = array_pop($parts);
                            echo esc_html(implode(' ', $parts)) . ' <span>' . esc_html($last) . '</span>';
                        } else {
                            echo esc_html($company_name);
                        }
                    ?></div>
                <?php endif; ?>
            </td>
            <td class="company-details">
                <strong><?php echo esc_html($company_name); ?></strong>
                <?php if ($full_store_address): ?>
                    <?php echo wp_kses_post($full_store_address); ?><br>
                <?php endif; ?>
                <?php if ($company_phone): ?>
                    Phone: <?php echo esc_html($company_phone); ?><br>
                <?php endif; ?>
                <?php echo esc_html($company_email); ?>
            </td>
            </tr>
        </table>

        <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1.2rem;">
            <h1 class="invoice-title">Invoice</h1>
        </div>

        <table class="addresses-table">
            <tr>
            <td class="address-box" style="width: 50%; vertical-align: top; padding-right: 1.5rem;">
                <div class="address-title">Billed To</div>
                <div class="address-content">
                    <?php echo wp_kses_post($order->get_formatted_billing_address('<br>')); ?>
                    <br><br>
                    <?php if ($order->get_billing_email()): ?>
                        <strong>Email:</strong> <?php echo esc_html($order->get_billing_email()); ?><br>
                    <?php endif; ?>
                    <?php if ($order->get_billing_phone()): ?>
                        <strong>Phone:</strong> <?php echo esc_html($order->get_billing_phone()); ?>
                    <?php endif; ?>
                </div>

                <?php if ($show_shipping && $order->needs_shipping_address()): ?>
                <div style="margin-top: 1.5rem;">
                    <div class="address-title">Shipped To</div>
                    <div class="address-content">
                        <?php echo wp_kses_post($order->get_formatted_shipping_address('<br>')); ?>
                    </div>
                </div>
                <?php endif; ?>
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <div class="meta-row">
                    <span class="label">Invoice Number</span>
                    <span class="value">#<?php echo $order->get_order_number(); ?></span>
                </div>
                <div class="meta-row">
                    <span class="label">Invoice Date</span>
                    <span class="value"><?php echo wc_format_datetime($order->get_date_created()); ?></span>
                </div>
                <div class="meta-row">
                    <span class="label">Payment Method</span>
                    <span class="value"><?php echo wp_kses_post($order->get_payment_method_title()); ?></span>
                </div>
            </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-desc">Description</th>
                    <th class="col-qty">Qty</th>
                    <th class="col-price text-right">Price</th>
                    <th class="col-total text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order->get_items() as $item_id => $item): 
                    $product = $item->get_product();
                ?>
                <tr>
                    <td>
                        <div class="product-name"><?php echo wp_kses_post($item->get_name()); ?></div>
                        <div class="product-meta">
                            <?php 
                            if ($meta_data = $item->get_formatted_meta_data('')) {
                                foreach ($meta_data as $meta_id => $meta) {
                                    echo wp_kses_post('<strong>' . $meta->display_key . ':</strong> ' . $meta->display_value . '<br>');
                                }
                            }
                            ?>
                        </div>
                    </td>
                    <td style="text-align: center;"><?php echo esc_html($item->get_quantity()); ?></td>
                    <td class="text-right">
                        <?php echo wc_price($order->get_item_subtotal($item, false, true)); ?>
                    </td>
                    <td class="text-right">
                        <strong><?php echo wc_price($order->get_line_subtotal($item, false, true)); ?></strong>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
            <td class="totals-empty"></td>
            <td class="totals-content">
                <table class="totals-items-table">
                    <?php foreach ($order->get_order_item_totals() as $key => $total): ?>
                        <tr class="<?php echo ($key === 'order_total') ? 'grand-total' : ''; ?>">
                            <td><?php echo wp_kses_post($total['label']); ?></td>
                            <td><?php echo wp_kses_post($total['value']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </td>
            </tr>
        </table>

        <div class="invoice-footer">
            <?php echo wp_kses_post(nl2br($footer_text)); ?>
        </div>

    </div>
</body>
</html>
