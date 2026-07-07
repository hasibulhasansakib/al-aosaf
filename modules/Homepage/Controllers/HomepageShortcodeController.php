<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Homepage\Controllers;

class HomepageShortcodeController {
    public function init(): void {
        add_shortcode('aa_home_hero', [$this, 'renderHero']);
        add_shortcode('aa_home_categories', [$this, 'renderCategories']);
        add_shortcode('aa_home_top_selling', [$this, 'renderTopSelling']);
        add_shortcode('aa_dynamic_slider', [$this, 'renderDynamicSlider']);
    }

    public function renderHero($atts): string {
        $settings = get_option('aa_homepage_settings', []);
        
        // Build Slides Array
        $slides = [];
        if (!empty($settings['hero_slides']) && is_array($settings['hero_slides'])) {
            $slides = $settings['hero_slides'];
        } else {
            // Fallback for old hardcoded slides
            for ($i=1; $i<=3; $i++) {
                $img = $settings['hero_slide_'.$i.'_image'] ?? '';
                $link = $settings['hero_slide_'.$i.'_link'] ?? '';
                if (!empty($img)) {
                    $slides[] = [
                        'image' => $img,
                        'link' => $link
                    ];
                }
            }
        }
        
        // Fetch Right Column Products
        $right_products = [];
        $category_slug = $settings['hero_right_category'] ?? '';
        
        if (class_exists('WooCommerce')) {
            $args = [
                'post_type' => 'product',
                'post_status' => 'publish',
                'posts_per_page' => 3,
                'orderby' => 'rand'
            ];
            if (!empty($category_slug)) {
                $args['product_cat'] = $category_slug;
            }
            $query = new \WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    global $product;
                    
                    $image_id = $product->get_image_id();
                    $image_url = wp_get_attachment_image_url($image_id, 'large');
                    
                    if ($image_url) {
                        $right_products[] = [
                            'title' => get_the_title(),
                            'url' => get_permalink(),
                            'image' => $image_url,
                            'price' => $product->get_price_html()
                        ];
                    }
                }
            }
            wp_reset_postdata();
        }

        ob_start();
        
        $view_path = AA_PLUGIN_DIR . 'modules/Homepage/Views/frontend/hero.php';
        if (file_exists($view_path)) {
            include $view_path;
        }

        return ob_get_clean();
    }

    public function renderCategories($atts): string {
        $settings = get_option('aa_homepage_settings', []);
        
        if (($settings['featured_cats_enable'] ?? 'yes') === 'no') {
            return '';
        }

        $title = $settings['featured_cats_title'] ?? 'Featured Categories';
        $selected_cats = $settings['featured_cats_list'] ?? [];
        
        $categories_data = [];
        if (!empty($selected_cats) && class_exists('WooCommerce')) {
            foreach ($selected_cats as $cat_id) {
                $term = get_term($cat_id, 'product_cat');
                if (!$term || is_wp_error($term)) continue;

                $image_url = '';
                
                // 1. Try to get category thumbnail
                $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
                if ($thumbnail_id) {
                    $image_url = wp_get_attachment_image_url($thumbnail_id, 'medium');
                }

                // 2. Fallback: Get a random product's image from this category
                if (empty($image_url)) {
                    $args = [
                        'post_type' => 'product',
                        'post_status' => 'publish',
                        'posts_per_page' => 1,
                        'orderby' => 'rand',
                        'tax_query' => [
                            [
                                'taxonomy' => 'product_cat',
                                'field' => 'term_id',
                                'terms' => $term->term_id
                            ]
                        ]
                    ];
                    $query = new \WP_Query($args);
                    if ($query->have_posts()) {
                        $query->the_post();
                        global $product;
                        $prod_image_id = $product->get_image_id();
                        if ($prod_image_id) {
                            $image_url = wp_get_attachment_image_url($prod_image_id, 'medium');
                        }
                    }
                    wp_reset_postdata();
                }

                // Use placeholder if still empty
                if (empty($image_url)) {
                    $image_url = wc_placeholder_img_src();
                }

                $categories_data[] = [
                    'name' => $term->name,
                    'url' => get_term_link($term),
                    'image' => $image_url,
                    'count' => $term->count
                ];
            }
        }

        ob_start();
        $view_path = AA_PLUGIN_DIR . 'modules/Homepage/Views/frontend/categories.php';
        if (file_exists($view_path)) {
            include $view_path;
        }

        return ob_get_clean();
    }

    public function renderTopSelling($atts): string {
        $settings = get_option('aa_homepage_settings', []);
        
        if (($settings['top_selling_enable'] ?? 'yes') === 'no') {
            return '';
        }

        if (!class_exists('WooCommerce')) {
            return '';
        }

        $title = $settings['top_selling_title'] ?? 'Top Selling';
        $count = $settings['top_selling_count'] ?? 4;

        $args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => $count,
            'meta_key' => 'total_sales',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'meta_query' => WC()->query->get_meta_query(),
            'tax_query' => WC()->query->get_tax_query()
        ];

        // Ensure we only get in-stock products
        $args['meta_query'][] = [
            'key' => '_stock_status',
            'value' => 'instock',
            'compare' => '='
        ];

        $products = new \WP_Query($args);

        ob_start();
        $view_path = AA_PLUGIN_DIR . 'modules/Homepage/Views/frontend/top-selling.php';
        if (file_exists($view_path)) {
            include $view_path;
        }

        wp_reset_postdata();

        return ob_get_clean();
    }

    public function renderDynamicSlider($atts): string {
        $settings = get_option('aa_homepage_settings', []);
        $sliders = $settings['dynamic_sliders'] ?? [];

        $atts = shortcode_atts(['id' => '1'], $atts);
        $index = intval($atts['id']) - 1;

        if (!isset($sliders[$index])) {
            return ''; // Slider not found
        }

        if (!class_exists('WooCommerce')) {
            return '';
        }

        $slider_config = $sliders[$index];
        $title = $slider_config['title'] ?? '';
        $categories = $slider_config['categories'] ?? [];
        $orderby = $slider_config['orderby'] ?? 'date';
        $limit = $slider_config['limit'] ?? 8;
        $autoplay = ($slider_config['autoplay'] ?? 'yes') === 'yes';

        $args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => $limit,
            'meta_query' => WC()->query->get_meta_query(),
            'tax_query' => WC()->query->get_tax_query()
        ];

        // Stock status
        $args['meta_query'][] = [
            'key' => '_stock_status',
            'value' => 'instock',
            'compare' => '='
        ];

        // Categories filter
        if (!empty($categories)) {
            $args['tax_query'][] = [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $categories,
                'operator' => 'IN'
            ];
        }

        // Order by
        if ($orderby === 'sales') {
            $args['meta_key'] = 'total_sales';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
        } elseif ($orderby === 'rand') {
            $args['orderby'] = 'rand';
        } else {
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
        }

        $products = new \WP_Query($args);

        // Generate View All URL
        $view_all_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : '#';
        if (!empty($categories)) {
            if (count($categories) === 1) {
                $term_link = get_term_link((int)$categories[0], 'product_cat');
                if (!is_wp_error($term_link)) {
                    $view_all_url = $term_link;
                }
            } else {
                $category_slugs = [];
                foreach ($categories as $cat_id) {
                    $term = get_term($cat_id, 'product_cat');
                    if ($term && !is_wp_error($term)) {
                        $category_slugs[] = $term->slug;
                    }
                }
                if (!empty($category_slugs)) {
                    $view_all_url = add_query_arg('product_cat', implode(',', $category_slugs), $view_all_url);
                }
            }
        }

        ob_start();
        $view_path = AA_PLUGIN_DIR . 'modules/Homepage/Views/frontend/dynamic-slider.php';
        if (file_exists($view_path)) {
            include $view_path;
        }

        wp_reset_postdata();

        return ob_get_clean();
    }
}
