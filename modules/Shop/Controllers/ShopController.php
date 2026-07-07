<?php
namespace Alaosaf\Modules\Shop\Controllers;

class ShopController {

    public function init(): void {
        // Template Intercept
        add_filter('template_include', [$this, 'overrideTemplate'], 99);

        // Assets
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);

        // AJAX Handlers
        add_action('wp_ajax_aa_filter_shop', [$this, 'ajaxFilterProducts']);
        add_action('wp_ajax_nopriv_aa_filter_shop', [$this, 'ajaxFilterProducts']);
    }

    public function overrideTemplate($template) {
        if (is_shop() || is_product_category() || is_product_tag() || is_tax('product_brand')) {
            $custom_template = AA_PLUGIN_DIR . 'modules/Shop/Views/frontend/shop.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }

    public function enqueueAssets() {
        if (is_shop() || is_product_category() || is_product_tag() || is_tax('product_brand')) {
            wp_enqueue_script('jquery-ui-slider');
            wp_enqueue_script('wc-jquery-ui-touchpunch'); // For mobile touch support on sliders

            wp_enqueue_style('aa-shop-css', AA_PLUGIN_URL . 'modules/Shop/assets/css/shop.css', [], time());
            wp_enqueue_script('aa-shop-js', AA_PLUGIN_URL . 'modules/Shop/assets/js/shop.js', ['jquery'], time(), true);

            wp_localize_script('aa-shop-js', 'aaShopAjax', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('aa-shop-filter-nonce')
            ]);
        }
    }

    public function ajaxFilterProducts() {
        check_ajax_referer('aa-shop-filter-nonce', 'nonce');

        $args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => intval($_POST['posts_per_page'] ?? 16),
            'paged' => intval($_POST['paged'] ?? 1),
            'tax_query' => ['relation' => 'AND'],
            'meta_query' => ['relation' => 'AND']
        ];

        // Search Keyword
        if (!empty($_POST['search'])) {
            $args['s'] = sanitize_text_field($_POST['search']);
        }

        // Category Filter
        if (!empty($_POST['category'])) {
            $args['tax_query'][] = [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => sanitize_title($_POST['category'])
            ];
        }

        // Tag Filter
        if (!empty($_POST['tag'])) {
            $args['tax_query'][] = [
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => sanitize_title($_POST['tag'])
            ];
        }

        // Stock Filter
        if (!empty($_POST['instock']) && $_POST['instock'] === 'true') {
            $args['meta_query'][] = [
                'key' => '_stock_status',
                'value' => 'instock',
                'compare' => '='
            ];
        }

        // Price Filter
        if (isset($_POST['min_price']) && isset($_POST['max_price'])) {
            $args['meta_query'][] = [
                'key' => '_price',
                'value' => [floatval($_POST['min_price']), floatval($_POST['max_price'])],
                'type' => 'NUMERIC',
                'compare' => 'BETWEEN'
            ];
        }

        // Sorting
        $orderby = sanitize_text_field($_POST['orderby'] ?? 'menu_order');
        switch ($orderby) {
            case 'date':
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
            case 'price':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_price';
                $args['order'] = 'ASC';
                break;
            case 'price-desc':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_price';
                $args['order'] = 'DESC';
                break;
            case 'popularity':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = 'total_sales';
                $args['order'] = 'DESC';
                break;
            default:
                $args['orderby'] = 'menu_order title';
                $args['order'] = 'ASC';
                break;
        }

        $query = new \WP_Query($args);

        ob_start();
        $grid_path = AA_PLUGIN_DIR . 'modules/Shop/Views/frontend/partials/shop-grid.php';
        if (file_exists($grid_path)) {
            include $grid_path;
        }
        $html = ob_get_clean();

        // Calculate total info
        $total = $query->found_posts;
        $current_page = max(1, $args['paged']);
        $per_page = $args['posts_per_page'];
        $first = ($current_page - 1) * $per_page + 1;
        $last = min($total, $current_page * $per_page);
        
        $result_text = '';
        if ($total == 1) {
            $result_text = 'Showing the single result';
        } elseif ($total <= $per_page || -1 === $per_page) {
            $result_text = sprintf('Showing all %d results', $total);
        } else {
            $result_text = sprintf('Showing %d–%d of %d results', $first, $last, $total);
        }

        wp_send_json_success([
            'html' => $html,
            'total' => $total,
            'result_text' => $result_text,
            'max_num_pages' => $query->max_num_pages
        ]);
    }
}
