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
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - #<?php echo $order->get_order_number(); ?></title>
    <style>
        body {
            font-family: 'kalpurush', sans-serif;
            color: #334155;
            margin: 0;
            padding: 0;
            font-size: 14px;
            line-height: 1.5;
        }
        .invoice-wrapper {
            padding: 20px;
            border-top: 6px solid #c59b5f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .text-logo {
            font-size: 28px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .company-details {
            text-align: right;
            font-size: 13px;
            color: #64748b;
        }
        .invoice-title {
            font-size: 30px;
            color: #0f172a;
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .addresses-table {
            background-color: #f8fafc;
            margin-bottom: 20px;
        }
        .addresses-table td {
            padding: 15px;
            vertical-align: top;
        }
        .address-title {
            font-size: 12px;
            font-weight: bold;
            color: #c59b5f;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .meta-row {
            margin-bottom: 5px;
            font-size: 12px;
        }
        .items-table {
            margin-bottom: 20px;
        }
        .items-table th {
            padding: 10px;
            text-align: left;
            background-color: #f8fafc;
            border-bottom: 2px solid #c59b5f;
            font-size: 12px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-table {
            width: 100%;
        }
        .totals-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .grand-total td {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
        }
        .invoice-footer {
            clear: both;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            padding-top: 20px;
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="invoice-wrapper">
        <table class="header-table">
            <tr>
                <td style="width: 50%;">
                    <?php if ($custom_logo_url): ?>
                        <img src="<?php echo esc_url($custom_logo_url); ?>" style="max-height: 60px;">
                    <?php else: ?>
                        <div class="text-logo"><?php echo esc_html($company_name); ?></div>
                    <?php endif; ?>
                </td>
                <td style="width: 50%;" class="company-details">
                    <strong><?php echo esc_html($company_name); ?></strong><br>
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

        <div class="invoice-title">Invoice</div>

        <table class="addresses-table">
            <tr>
                <td style="width: 50%;">
                    <div class="address-title">Billed To</div>
                    <div>
                        <?php echo wp_kses_post($order->get_formatted_billing_address('<br>')); ?>
                        <br>
                        <?php if ($order->get_billing_email()): ?>
                            <strong>Email:</strong> <?php echo esc_html($order->get_billing_email()); ?><br>
                        <?php endif; ?>
                        <?php if ($order->get_billing_phone()): ?>
                            <strong>Phone:</strong> <?php echo esc_html($order->get_billing_phone()); ?>
                        <?php endif; ?>
                    </div>
                    <?php if ($show_shipping && $order->needs_shipping_address()): ?>
                        <div style="margin-top: 15px;">
                            <div class="address-title">Shipped To</div>
                            <div>
                                <?php echo wp_kses_post($order->get_formatted_shipping_address('<br>')); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </td>
                <td style="width: 50%; text-align: right;">
                    <div class="meta-row"><strong>Invoice Number:</strong> #<?php echo $order->get_order_number(); ?></div>
                    <div class="meta-row"><strong>Invoice Date:</strong> <?php echo wc_format_datetime($order->get_date_created()); ?></div>
                    <div class="meta-row"><strong>Payment Method:</strong> <?php echo wp_kses_post($order->get_payment_method_title()); ?></div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="width: 15%;" class="text-center">Qty</th>
                    <th style="width: 15%;" class="text-right">Price</th>
                    <th style="width: 20%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order->get_items() as $item_id => $item): ?>
                <tr>
                    <td>
                        <strong><?php echo wp_kses_post($item->get_name()); ?></strong>
                        <div style="color: #64748b; font-size: 11px;">
                            <?php 
                            if ($meta_data = $item->get_formatted_meta_data('')) {
                                foreach ($meta_data as $meta_id => $meta) {
                                    echo wp_kses_post('<strong>' . $meta->display_key . ':</strong> ' . $meta->display_value . '<br>');
                                }
                            }
                            ?>
                        </div>
                    </td>
                    <td class="text-center"><?php echo esc_html($item->get_quantity()); ?></td>
                    <td class="text-right"><?php echo wc_price($order->get_item_subtotal($item, false, true)); ?></td>
                    <td class="text-right"><strong><?php echo wc_price($order->get_line_subtotal($item, false, true)); ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; padding: 0;">
                    <table class="totals-table">
                        <?php foreach ($order->get_order_item_totals() as $key => $total): ?>
                            <tr class="<?php echo ($key === 'order_total') ? 'grand-total' : ''; ?>">
                                <td style="text-align: right; padding-right: 15px; border-bottom: 1px solid #e2e8f0;"><?php echo wp_kses_post($total['label']); ?></td>
                                <td style="text-align: right; font-weight: bold; border-bottom: 1px solid #e2e8f0;"><?php echo wp_kses_post($total['value']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <htmlpagefooter name="InvoiceFooter">
        <div class="invoice-footer">
            <?php echo wp_kses_post(nl2br($footer_text)); ?>
        </div>
    </htmlpagefooter>
    <sethtmlpagefooter name="InvoiceFooter" value="on" />

</body>
</html>
