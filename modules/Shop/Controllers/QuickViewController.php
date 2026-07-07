<?php
namespace Alaosaf\Modules\Shop\Controllers;

class QuickViewController {

    public function init() {
        add_action('wp_ajax_aa_quick_view', [$this, 'getQuickViewContent']);
        add_action('wp_ajax_nopriv_aa_quick_view', [$this, 'getQuickViewContent']);
        add_action('wp_footer', [$this, 'renderQuickViewModal']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function enqueueAssets() {
        wp_enqueue_style('aa-quick-view', AA_PLUGIN_URL . 'modules/Shop/assets/css/quick-view.css', [], time());
        wp_enqueue_script('aa-quick-view', AA_PLUGIN_URL . 'modules/Shop/assets/js/quick-view.js', ['jquery', 'wc-add-to-cart-variation'], time(), true);
        wp_localize_script('aa-quick-view', 'aaQuickView', [
            'ajaxurl' => admin_url('admin-ajax.php')
        ]);
    }

    public function getQuickViewContent() {
        if (!isset($_POST['product_id'])) {
            wp_send_json_error(['message' => 'Product ID is missing']);
        }

        $product_id = intval($_POST['product_id']);
        global $post, $product;
        
        $post = get_post($product_id);
        setup_postdata($post);
        $product = wc_get_product($product_id);

        if (!$product) {
            wp_send_json_error(['message' => 'Product not found']);
        }

        ob_start();
        ?>
        <div class="aa-qv-compact-product">
            <div class="aa-qv-compact-header">
                <div class="aa-qv-compact-img">
                    <?php echo $product->get_image('thumbnail'); ?>
                </div>
                <div class="aa-qv-compact-info">
                    <h3 class="aa-qv-compact-title"><?php echo $product->get_name(); ?></h3>
                    <div class="aa-qv-compact-price"><?php echo $product->get_price_html(); ?></div>
                </div>
            </div>
            <div class="aa-qv-form-wrapper">
                <?php 
                // Render the form based on product type
                woocommerce_template_single_add_to_cart();
                ?>
            </div>
        </div>
        <?php
        $html = ob_get_clean();
        
        wp_reset_postdata();

        wp_send_json_success(['html' => $html]);
    }

    public function renderQuickViewModal() {
        ?>
        <div id="aa-quick-view-modal" class="aa-qv-modal">
            <div class="aa-qv-overlay"></div>
            <div class="aa-qv-content-wrapper">
                <button class="aa-qv-close">&times;</button>
                <div class="aa-qv-content">
                    <!-- Content injected via AJAX -->
                </div>
                <div class="aa-qv-loader" style="display:none;">
                    <svg class="aa-spinner" viewBox="0 0 50 50"><circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle></svg>
                </div>
            </div>
        </div>
        <?php
    }
}
