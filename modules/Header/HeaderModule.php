<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Header;

use Alaosaf\Interfaces\ModuleInterface;
use Alaosaf\Modules\Header\Controllers\HeaderSettingsController;
use Alaosaf\Modules\Header\Controllers\HeaderShortcodeController;

class HeaderModule implements ModuleInterface {
    public function init(): void {
        $this->seedDefaults();

        $settings = new HeaderSettingsController();
        $settings->init();

        $shortcode = new HeaderShortcodeController();
        $shortcode->init();

        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('wp_footer', [$this, 'renderFloatingCart']);
        
        // WooCommerce Mini Cart Integrations
        if (class_exists('WooCommerce')) {
            add_filter('woocommerce_widget_cart_item_quantity', [$this, 'miniCartQuantityHtml'], 10, 3);
            add_action('woocommerce_widget_shopping_cart_total', [$this, 'prependTotalItems'], 1);
            add_filter('woocommerce_add_to_cart_fragments', [$this, 'updateCartFragments']);
            add_action('wp_ajax_aa_update_mini_cart_qty', [$this, 'updateMiniCartQty']);
            add_action('wp_ajax_nopriv_aa_update_mini_cart_qty', [$this, 'updateMiniCartQty']);
            
            // Live Search
            add_action('wp_ajax_aa_live_search', [$this, 'liveSearch']);
            add_action('wp_ajax_nopriv_aa_live_search', [$this, 'liveSearch']);
        }
    }

    public function prependTotalItems() {
        $count = WC()->cart->get_cart_contents_count();
        // Use <span> instead of <div> because WooCommerce natively wraps this in a <p> tag!
        // Browsers will break the layout if a <div> is placed inside a <p>.
        echo '<span class="aa-mini-cart-total-items"><span class="aa-total-label">Total Items:</span><span class="aa-total-count">' . esc_html($count) . '</span></span>';
    }

    public function renderFloatingCart() {
        $view_path = AA_PLUGIN_DIR . 'modules/Header/Views/frontend/floating-cart.php';
        if (file_exists($view_path)) {
            include $view_path;
        }
    }

    public function updateCartFragments($fragments) {
        ob_start();
        $this->renderFloatingCart();
        $fragments['div.aa-floating-cart-wrapper'] = ob_get_clean();
        return $fragments;
    }

    public function miniCartQuantityHtml($html, $cart_item, $cart_item_key) {
        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
        
        // Revert to Unit Price as requested
        $unit_price = WC()->cart->get_product_price($_product);
        $qty = $cart_item['quantity'];
        
        $custom_html = '<div class="aa-mini-cart-qty-wrap">';
        $custom_html .= '<div class="aa-mini-cart-price">' . $unit_price . '</div>';
        $custom_html .= '<div class="aa-qty-pill">';
        $custom_html .= '<button type="button" class="aa-qty-btn aa-qty-minus" data-cart_item_key="' . esc_attr($cart_item_key) . '">&minus;</button>';
        $custom_html .= '<input type="number" class="aa-qty-input" value="' . esc_attr($qty) . '" min="1" step="1" readonly />';
        $custom_html .= '<button type="button" class="aa-qty-btn aa-qty-plus" data-cart_item_key="' . esc_attr($cart_item_key) . '">&plus;</button>';
        $custom_html .= '</div>';
        $custom_html .= '</div>';
        
        return $custom_html;
    }

    public function updateMiniCartQty() {
        if (!isset($_POST['cart_item_key']) || !isset($_POST['qty'])) {
            wp_send_json_error();
        }
        $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
        $qty = (int) $_POST['qty'];
        
        if (WC()->cart->set_quantity($cart_item_key, $qty)) {
            WC()->cart->calculate_totals();
            
            // Build fragments manually to guarantee it doesn't fail on custom endpoints
            ob_start();
            woocommerce_mini_cart();
            $mini_cart = ob_get_clean();

            $fragments = [
                'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
            ];
            
            wp_send_json([
                'success' => true,
                'fragments' => $fragments
            ]);
        }
        wp_send_json_error();
    }

    public function liveSearch() {
        if (!isset($_POST['query']) || strlen(trim($_POST['query'])) < 2) {
            wp_send_json_success([]);
        }
        $search_query = sanitize_text_field($_POST['query']);
        $args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            's' => $search_query,
            'posts_per_page' => 6,
        ];
        $query = new \WP_Query($args);
        $results = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                global $product;
                if (!$product) continue;
                
                $image_url = wp_get_attachment_image_url(get_post_thumbnail_id(), 'thumbnail');
                if (!$image_url) $image_url = wc_placeholder_img_src();

                $results[] = [
                    'title' => get_the_title(),
                    'url' => get_permalink(),
                    'image' => $image_url,
                    'price' => $product->get_price_html()
                ];
            }
        }
        wp_reset_postdata();
        wp_send_json_success($results);
    }

    public function enqueueAssets(): void {
        wp_enqueue_style('aa-header-css', AA_PLUGIN_URL . 'modules/Header/assets/css/header.css', [], time() . rand());
        wp_enqueue_style('aa-cart-drawer-css', AA_PLUGIN_URL . 'modules/Header/assets/css/cart-drawer.css', [], time() . rand());
        wp_enqueue_style('aa-sidebar-drawer-css', AA_PLUGIN_URL . 'modules/Header/assets/css/sidebar-drawer.css', [], time() . rand());
        wp_enqueue_script('aa-header-js', AA_PLUGIN_URL . 'modules/Header/assets/js/header.js', ['jquery'], time() . rand(), true);
        
        wp_localize_script('aa-header-js', 'aa_header_ajax', [
            'ajax_url' => admin_url('admin-ajax.php')
        ]);
    }

    private function seedDefaults(): void {
        if (get_option('aa_header_settings', false) === false) {
            $defaults = [
                'general_enable' => 'yes',
                'general_preset' => 'luxury_fashion',
                'general_transparent' => 'no',
                'announcement_enable' => 'no',
                'announcement_text' => 'Free shipping on orders over $200',
                'topbar_enable' => 'no',
                'sticky_enable' => 'yes',
            ];
            add_option('aa_header_settings', $defaults);
        }
    }

    public function getModuleId(): string {
        return 'header';
    }
}
