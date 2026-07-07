<?php
if (!defined('ABSPATH')) exit;
global $product;

$main_image_id = $product->get_image_id();
$gallery_image_ids = $product->get_gallery_image_ids();

if ($main_image_id) {
    array_unshift($gallery_image_ids, $main_image_id);
}
$gallery_image_ids = array_unique($gallery_image_ids);

$sale_badge = '';
if ($product->is_on_sale()) {
    if ($product->is_type('variable')) {
        $percentages = [];
        foreach ($product->get_children() as $child_id) {
            $variation = wc_get_product($child_id);
            if ($variation->get_regular_price() && $variation->get_sale_price()) {
                $percentage = round((($variation->get_regular_price() - $variation->get_sale_price()) / $variation->get_regular_price()) * 100);
                $percentages[] = $percentage;
            }
        }
        $max_percentage = !empty($percentages) ? max($percentages) : 0;
        if ($max_percentage > 0) {
            $sale_badge = '-' . $max_percentage . '%';
        }
    } else {
        if ($product->get_regular_price() && $product->get_sale_price()) {
            $percentage = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
            $sale_badge = '-' . $percentage . '%';
        }
    }
}
?>

<div class="aa-gallery-container">
    <div class="aa-gallery-thumbnails">
        <div class="aa-thumbnails-inner">
            <?php foreach ($gallery_image_ids as $index => $image_id): 
                $thumb_url = wp_get_attachment_image_url($image_id, 'woocommerce_gallery_thumbnail');
                $full_url = wp_get_attachment_image_url($image_id, 'full');
                if (!$thumb_url) continue;
            ?>
                <div class="aa-thumb-item <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo esc_attr($index); ?>" data-full-image="<?php echo esc_url($full_url); ?>">
                    <img src="<?php echo esc_url($thumb_url); ?>" alt="">
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($gallery_image_ids) > 4): ?>
            <button class="aa-thumb-nav aa-thumb-down">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
        <?php endif; ?>
    </div>

    <div class="aa-gallery-main">
        <?php if ($sale_badge): ?>
            <span class="aa-gallery-badge"><?php echo esc_html($sale_badge); ?></span>
        <?php endif; ?>

        <?php if (count($gallery_image_ids) > 1): ?>
            <button class="aa-gallery-arrow aa-gallery-prev">
                <svg width="24" height="24" viewBox="6 6 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button class="aa-gallery-arrow aa-gallery-next">
                <svg width="24" height="24" viewBox="6 6 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
            </button>
        <?php endif; ?>

        <?php 
        $main_full = wp_get_attachment_image_url($main_image_id ?: get_option('woocommerce_placeholder_image', 0), 'full');
        ?>
        <img src="<?php echo esc_url($main_full); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" id="aa-main-product-image">
    </div>
</div>
