<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Homepage\Controllers;

use Alaosaf\Base\AbstractController;

class HomepageSettingsController extends AbstractController {
    
    public function init(): void {
        add_action('aa_admin_page_homepage', [$this, 'renderPage']);
        add_action('admin_post_aa_save_homepage_settings', [$this, 'saveSettings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }

    public function enqueueAdminAssets($hook): void {
        if (strpos($hook, 'aa-homepage') !== false) {
            wp_enqueue_media();
            wp_enqueue_style('aa-admin-homepage-css', AA_PLUGIN_URL . 'modules/Homepage/assets/css/admin-homepage.css', [], time() . rand());
            wp_enqueue_script('aa-admin-homepage-js', AA_PLUGIN_URL . 'modules/Homepage/assets/js/admin-homepage.js', ['jquery'], time() . rand(), true);
        }
    }

    public function renderPage(): void {
        $this->requireCapability('manage_options');
        $settings = get_option('aa_homepage_settings', []);
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'hero';
        
        $categories = [];
        if (taxonomy_exists('product_cat')) {
            $categories = get_terms([
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
            ]);
        }
        
        include AA_PLUGIN_DIR . 'modules/Homepage/Views/admin/settings.php';
    }

    public function saveSettings(): void {
        $this->requireCapability('manage_options');
        check_admin_referer('aa_save_homepage_settings', 'aa_homepage_nonce');

        $settings = get_option('aa_homepage_settings', []);
        
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'aa_homepage_') === 0) {
                $field_key = substr($key, 12);
                
                // Handle Arrays (like repeater fields)
                if (is_array($value)) {
                    // Sanitize the array specifically for hero_slides
                    if ($field_key === 'hero_slides') {
                        $sanitized_slides = [];
                        foreach ($value as $slide) {
                            if (!empty($slide['image'])) {
                                $sanitized_slides[] = [
                                    'image' => esc_url_raw($slide['image']),
                                    'link' => esc_url_raw($slide['link'] ?? '')
                                ];
                            }
                        }
                        $settings[$field_key] = $sanitized_slides;
                    } elseif ($field_key === 'dynamic_sliders') {
                        // Sanitize dynamic sliders
                        $sanitized_sliders = [];
                        if (is_array($value)) {
                            foreach ($value as $slider) {
                                $sanitized_sliders[] = [
                                    'title' => sanitize_text_field($slider['title'] ?? ''),
                                    'categories' => isset($slider['categories']) && is_array($slider['categories']) ? array_map('intval', $slider['categories']) : [],
                                    'orderby' => sanitize_text_field($slider['orderby'] ?? 'date'),
                                    'limit' => intval($slider['limit'] ?? 8),
                                    'autoplay' => sanitize_text_field($slider['autoplay'] ?? 'no'),
                                ];
                            }
                        }
                        $settings[$field_key] = $sanitized_sliders;
                    } elseif ($field_key === 'featured_cats_list') {
                        $settings[$field_key] = array_map('intval', $value);
                    }
                } 
                // Handle standard strings/URLs/Numbers
                else {
                    if ($field_key === 'featured_cats_enable' || $field_key === 'top_selling_enable') {
                        $settings[$field_key] = sanitize_text_field($value);
                    } elseif ($field_key === 'top_selling_count') {
                        $settings[$field_key] = intval($value);
                    } elseif (strpos($field_key, 'image_url') !== false || strpos($field_key, 'link') !== false || strpos($field_key, 'url') !== false) {
                        $settings[$field_key] = esc_url_raw($value);
                    } else {
                        $settings[$field_key] = sanitize_text_field($value);
                    }
                }
            }
        }

        $tab = isset($_POST['current_tab']) ? sanitize_text_field($_POST['current_tab']) : 'hero';

        // Handle unchecked checkboxes based on the submitted tab
        if ($tab === 'categories') {
            if (!isset($_POST['aa_homepage_featured_cats_enable'])) {
                $settings['featured_cats_enable'] = 'no';
            }
            if (!isset($_POST['aa_homepage_featured_cats_list'])) {
                $settings['featured_cats_list'] = [];
            }
        }
        
        if ($tab === 'top_selling') {
            if (!isset($_POST['aa_homepage_top_selling_enable'])) {
                $settings['top_selling_enable'] = 'no';
            }
        }

        update_option('aa_homepage_settings', $settings);
        
        wp_safe_redirect(admin_url('admin.php?page=aa-homepage&tab=' . $tab . '&updated=true'));
        exit;
    }
}
