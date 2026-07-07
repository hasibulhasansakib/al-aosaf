<?php
if (!defined('ABSPATH')) exit;
global $product;
if (!$product) return;
?>
<div class="aa-single-product-container">

    <div class="aa-product-main-area">
        <!-- Gallery Section -->
        <div class="aa-product-gallery-wrapper">
            <?php 
            $gallery_path = AA_PLUGIN_DIR . 'modules/SingleProduct/Views/frontend/gallery.php';
            if (file_exists($gallery_path)) include $gallery_path; 
            ?>
        </div>

        <!-- Summary Section -->
        <div class="aa-product-summary-wrapper">
            <?php 
            $summary_path = AA_PLUGIN_DIR . 'modules/SingleProduct/Views/frontend/summary.php';
            if (file_exists($summary_path)) include $summary_path; 
            ?>
        </div>
    </div>

    <!-- Value Props Section -->
    <?php 
    $props_path = AA_PLUGIN_DIR . 'modules/SingleProduct/Views/frontend/value-props.php';
    if (file_exists($props_path)) include $props_path; 
    ?>

    <!-- Tabs Section -->
    <?php 
    $tabs_path = AA_PLUGIN_DIR . 'modules/SingleProduct/Views/frontend/tabs.php';
    if (file_exists($tabs_path)) include $tabs_path; 
    ?>

    <!-- Related Products -->
    <?php 
    $related_path = AA_PLUGIN_DIR . 'modules/SingleProduct/Views/frontend/related-products.php';
    if (file_exists($related_path)) include $related_path; 
    ?>
</div>
