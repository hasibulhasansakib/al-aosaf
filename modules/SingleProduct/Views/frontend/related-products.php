<?php
if (!defined('ABSPATH')) exit;
global $product;

$related_ids = wc_get_related_products($product->get_id(), 4);
if (empty($related_ids)) return;

$args = array(
    'post_type' => 'product',
    'post__in' => $related_ids,
    'posts_per_page' => 4,
);
$query = new WP_Query($args);

if ($query->have_posts()): ?>
    <div class="aa-related-products-wrapper">
        <h2 class="aa-related-products-title"><?php _e('Related Products', 'al-aosaf'); ?></h2>
        <div class="aa-related-products-grid">
            <?php while ($query->have_posts()): $query->the_post(); 
                $related_product = wc_get_product(get_the_ID());
                if (!$related_product) continue;
                
                // Save global product so it can be restored after we output the related product card
                $original_product = $GLOBALS['product'];
                $GLOBALS['product'] = $related_product;
                
                $card_path = AA_PLUGIN_DIR . 'modules/Homepage/Views/frontend/components/product-card-vertical.php';
                if (file_exists($card_path)) {
                    include $card_path;
                }
                
                $GLOBALS['product'] = $original_product; // Restore original product
            endwhile; ?>
        </div>
    </div>
    <?php wp_reset_postdata(); ?>
<?php endif; ?>
