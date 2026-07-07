<?php
if (!defined('ABSPATH')) exit;
global $product;

$tabs = apply_filters('woocommerce_product_tabs', array());

// Fallback if tabs are empty (e.g. in Elementor custom context)
if (empty($tabs)) {
    $content = $product->get_description();
    if ($content) {
        $tabs['description'] = array(
            'title'    => __('Description', 'al-aosaf'),
            'priority' => 10,
            'callback' => function() use ($content) { echo wpautop(do_shortcode($content)); }
        );
    }

    if ($product->has_attributes() || $product->has_dimensions() || $product->has_weight()) {
        $tabs['additional_information'] = array(
            'title'    => __('Specifications', 'al-aosaf'),
            'priority' => 20,
            'callback' => 'woocommerce_product_additional_information_tab'
        );
    }

    if (comments_open()) {
        $tabs['reviews'] = array(
            'title'    => sprintf(__('Reviews (%d)', 'al-aosaf'), $product->get_review_count()),
            'priority' => 30,
            'callback' => 'comments_template'
        );
    }
}

$tabs['product_info'] = array(
    'title'    => __('Product Info', 'al-aosaf'),
    'priority' => 15,
    'callback' => function() {
        $meta_path = AA_PLUGIN_DIR . 'modules/SingleProduct/Views/frontend/meta-list.php';
        if (file_exists($meta_path)) include $meta_path;
    }
);

uasort($tabs, function($a, $b) {
    return $a['priority'] - $b['priority'];
});

if (empty($tabs)) return;
?>

<div class="aa-product-tabs-wrapper">
    <ul class="aa-tabs-nav">
        <?php $i = 0; foreach ($tabs as $key => $tab) : ?>
            <li class="<?php echo $i === 0 ? 'active' : ''; ?> <?php echo esc_attr($key); ?>_tab" data-tab="tab-<?php echo esc_attr($key); ?>">
                <?php echo wp_kses_post(apply_filters('woocommerce_product_' . $key . '_tab_title', $tab['title'], $key)); ?>
            </li>
        <?php $i++; endforeach; ?>
    </ul>

    <div class="aa-tabs-content">
        <?php $i = 0; foreach ($tabs as $key => $tab) : ?>
            <div class="aa-tab-panel <?php echo $i === 0 ? 'active' : ''; ?>" id="tab-<?php echo esc_attr($key); ?>">
                <div class="aa-desc-left">
                    <?php 
                    if (isset($tab['callback'])) {
                        call_user_func($tab['callback'], $key, $tab);
                    }
                    ?>
                </div>
            </div>
        <?php $i++; endforeach; ?>
    </div>
</div>
