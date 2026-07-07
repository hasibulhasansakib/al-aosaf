<?php
if (!defined('ABSPATH')) exit;
global $product;

$material = $product->get_attribute('pa_material') ?: '80% Cotton, 20% Polyester';
$fit = $product->get_attribute('pa_fit') ?: 'Regular Fit';
$sleeve = $product->get_attribute('pa_sleeve') ?: 'Full Sleeve';
$care = $product->get_attribute('pa_care') ?: 'Machine Wash';
$sku = $product->get_sku() ?: 'N/A';
?>
<ul class="aa-product-meta-list">
    <li>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l-5 5v15h10V7l-5-5z"></path></svg>
        <span>Material: <?php echo esc_html($material); ?></span>
    </li>
    <li>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
        <span>Fit: <?php echo esc_html($fit); ?></span>
    </li>
    <li>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        <span>Sleeve: <?php echo esc_html($sleeve); ?></span>
    </li>
    <li>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M3 15h6v2H3z"></path><path d="M3 19h6v2H3z"></path></svg>
        <span>Care: <?php echo esc_html($care); ?></span>
    </li>
    <li>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
        <span>SKU: <?php echo esc_html($sku); ?></span>
    </li>
</ul>
