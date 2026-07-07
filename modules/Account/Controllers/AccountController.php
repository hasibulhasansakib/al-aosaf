<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Account\Controllers;

use Alaosaf\Base\AbstractController;

class AccountController extends AbstractController {
    
    public function init(): void {
        // Force enable WooCommerce account functionality
        add_filter('woocommerce_is_account_page', '__return_true');
        
        // Disable default WooCommerce account styling completely
        add_filter('woocommerce_enqueue_styles', function($styles) {
            unset($styles['woocommerce-general']);
            unset($styles['woocommerce-layout']);
            return $styles;
        });

        // Override default orders endpoint to use premium UI
        remove_action('woocommerce_account_orders_endpoint', 'woocommerce_account_orders');
        add_action('woocommerce_account_orders_endpoint', [$this, 'renderCustomOrdersEndpoint']);

        // Custom Profile Picture System
        add_action('woocommerce_edit_account_form_tag', function() {
            echo 'enctype="multipart/form-data"';
        });
        add_action('woocommerce_edit_account_form_start', [$this, 'renderProfilePictureUploadUI']);
        add_action('woocommerce_save_account_details', [$this, 'handleProfilePictureUpload']);
        add_filter('get_avatar_url', [$this, 'overrideAvatarUrl'], 10, 3);

        // Remove all default woocommerce account hooks as we are doing custom
        remove_all_actions('woocommerce_account_navigation');
        remove_all_actions('woocommerce_account_content');

        // Force enable WooCommerce registration on My Account page
        add_filter('option_woocommerce_enable_myaccount_registration', function() { return 'yes'; });

        add_action('wp_enqueue_scripts', [$this, 'enqueueAccountStyles']);

        // Wrap the entire login/register forms in a nice container
        add_action('woocommerce_before_customer_login_form', [$this, 'startLoginWrapper'], 1);
        add_action('woocommerce_after_customer_login_form', [$this, 'endLoginWrapper'], 99);

        // Lost password wrappers
        add_action('woocommerce_before_lost_password_form', [$this, 'startLostPasswordWrapper'], 1);
        add_action('woocommerce_after_lost_password_form', [$this, 'endLostPasswordWrapper'], 99);
        
        // Register Custom Dashboard Shortcode
        add_shortcode('aa_custom_dashboard', [$this, 'renderCustomDashboardShortcode']);
    }

    public function enqueueAccountStyles(): void {
        if (is_account_page() || has_shortcode(get_post()->post_content, 'aa_custom_dashboard')) {
            wp_enqueue_style(
                'aa-account-ui',
                AA_PLUGIN_URL . 'modules/Account/assets/css/account-ui.css',
                [],
                time() // Disable caching for development
            );
        }
    }

    public function startLoginWrapper(): void {
        echo '<div class="aa-login-register-container">';
        echo '<div class="aa-login-register-header">';
        echo '<h2>স্বাগতম!</h2>';
        echo '<p>আপনার অ্যাকাউন্টে লগইন করুন অথবা নতুন অ্যাকাউন্ট তৈরি করুন</p>';
        echo '</div>';
        
        // Tab Buttons
        echo '<div class="aa-auth-tabs">';
        echo '<button type="button" class="aa-tab-btn active" data-target="login">লগইন</button>';
        echo '<button type="button" class="aa-tab-btn" data-target="register">রেজিস্ট্রেশন</button>';
        echo '</div>';
        
        echo '<div class="aa-login-register-cards aa-tabbed-view">';
    }

    public function endLoginWrapper(): void {
        echo '</div>'; // End of aa-login-register-cards
        
        $home_url = home_url('/');
        $shop_url = wc_get_page_permalink('shop');
        
        echo '<div class="aa-auth-footer-links">';
        echo '<a href="' . esc_url($home_url) . '" class="aa-auth-home-btn">&larr; Back to Home</a>';
        echo '<a href="' . esc_url($shop_url) . '" class="aa-auth-shop-btn">Go to Shop &rarr;</a>';
        echo '</div>';
        
        echo '</div>'; // End of container
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tabBtns = document.querySelectorAll('.aa-tab-btn');
                const loginCol = document.querySelector('.u-column1.col-1');
                const registerCol = document.querySelector('.u-column2.col-2');

                if (tabBtns.length && loginCol && registerCol) {
                    loginCol.classList.add('active-tab');
                    registerCol.classList.remove('active-tab');

                    tabBtns.forEach(btn => {
                        btn.addEventListener('click', function() {
                            tabBtns.forEach(b => b.classList.remove('active'));
                            this.classList.add('active');

                            if (this.dataset.target === 'login') {
                                loginCol.classList.add('active-tab');
                                registerCol.classList.remove('active-tab');
                            } else {
                                loginCol.classList.remove('active-tab');
                                registerCol.classList.add('active-tab');
                            }
                        });
                    });
                }
            });
        </script>
        <?php
    }

    public function startLostPasswordWrapper(): void {
        echo '<div class="aa-login-register-container">';
        echo '<div class="aa-login-register-header">';
        echo '<h2>পাসওয়ার্ড পুনরুদ্ধার</h2>';
        echo '<p>আপনার ইউজারনেম বা ইমেইল প্রদান করুন। পাসওয়ার্ড রিসেট করার একটি লিংক আপনার ইমেইলে পাঠানো হবে।</p>';
        echo '</div>';
        echo '<div class="aa-login-register-cards aa-lost-password-card">';
    }

    public function endLostPasswordWrapper(): void {
        $login_url = wc_get_page_permalink( 'myaccount' );
        echo '<div class="aa-back-to-login">';
        echo '<a href="' . esc_url( $login_url ) . '">&larr; লগইন পেজে ফিরে যান</a>';
        echo '</div>';
        echo '</div></div>';
    }

    public function renderCustomDashboardShortcode($atts): string {
        if (!is_user_logged_in()) {
            return do_shortcode('[woocommerce_my_account]');
        }

        global $wp;
        
        $current_endpoint = '';
        $endpoints = \WC()->query->get_query_vars();
        foreach ($endpoints as $key => $value) {
            if (isset($wp->query_vars[$key])) {
                $current_endpoint = $key;
                break;
            }
        }

        ob_start();
        ?>
        <div class="aa-mobile-dashboard-header">
            <button class="aa-mobile-menu-toggle" onclick="document.querySelector('.aa-custom-dashboard-wrapper').classList.add('aa-sidebar-open')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 28px; height: 28px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
            <h2>My Account</h2>
        </div>

        <div class="aa-custom-dashboard-wrapper">
            <div class="aa-sidebar-overlay" onclick="document.querySelector('.aa-custom-dashboard-wrapper').classList.remove('aa-sidebar-open')"></div>
            <?php $this->renderDashboardSidebar($current_endpoint); ?>
            
            <main class="aa-dashboard-content">
                <?php
                if (empty($current_endpoint) || $current_endpoint === 'dashboard') {
                    $this->renderDashboardOverview();
                } else {
                    echo '<div class="woocommerce-MyAccount-content">';
                    do_action('woocommerce_account_' . $current_endpoint . '_endpoint', $wp->query_vars[$current_endpoint] ?? '');
                    echo '</div>';
                }
                ?>
            </main>
        </div>
        <?php
        $this->renderRecommendedProducts();
        return ob_get_clean();
    }

    private function renderDashboardSidebar($current_endpoint): void {
        $current_user = wp_get_current_user();
        $display_name = $current_user->display_name;
        $email = $current_user->user_email;
        $avatar = get_avatar($current_user->ID, 80, '', '', ['class' => 'aa-profile-avatar']);

        echo '<aside class="aa-dashboard-sidebar">';
        
        echo '<button class="aa-sidebar-close" onclick="document.querySelector(\'.aa-custom-dashboard-wrapper\').classList.remove(\'aa-sidebar-open\')"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>';

        echo '<div class="aa-sidebar-profile">';
        echo '<div class="aa-profile-img-wrapper">' . $avatar . '</div>';
        echo '<div class="aa-profile-info">';
        echo '<h3>' . esc_html($display_name) . '</h3>';
        echo '<span>' . esc_html($email) . '</span>';
        echo '</div>';
        echo '</div>';

        echo '<nav class="aa-sidebar-nav">';
        ?>
                    <ul>
                        <?php foreach (wc_get_account_menu_items() as $endpoint => $label) : 
                            $is_active = ($current_endpoint === $endpoint) || (empty($current_endpoint) && $endpoint === 'dashboard');
                            $active_class = $is_active ? 'is-active' : '';
                        ?>
                            <li class="<?php echo esc_attr($active_class); ?>">
                                <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>">
                                    <?php echo esc_html($label); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        
                        <li class="aa-shop-now-wrapper" style="margin-top: 20px;">
                            <a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop')); ?>" class="aa-btn-shop-now">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px; margin-right: 8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                                Shop Now
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="aa-btn-back-home">
                                &larr; Back to Home
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="aa-sidebar-support">
                    <strong>Need Help?</strong>
                    <p>We're here for you</p>
                    <a href="/contact" class="aa-support-btn">Contact Support</a>
                    <svg class="aa-support-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" /></svg>
                </div>
            </aside>
        <?php
    }

    private function renderDashboardOverview(): void {
        if (!function_exists('wc_get_order')) return;

        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $display_name = esc_html($current_user->display_name);

        $total_spent = wc_get_customer_total_spent($user_id);
        $total_orders = wc_get_customer_order_count($user_id);
        
        $pending_orders = wc_get_orders([
            'customer' => $user_id,
            'status' => ['wc-pending', 'wc-processing', 'wc-on-hold'],
            'return' => 'ids',
        ]);
        $pending_count = count($pending_orders);

        $recent_orders = wc_get_orders([
            'customer' => $user_id,
            'limit' => 5,
        ]);

        echo '<div class="aa-light-dashboard">';
        
        echo '<div class="aa-dashboard-header">';
        echo '<div class="aa-header-left">';
        echo '<h1>Welcome back, ' . $display_name . '</h1>';
        echo '<p>Here\'s what\'s happening with your account today.</p>';
        echo '</div>';
        echo '<div class="aa-header-right">';
        
        $last_login = get_user_meta($user_id, 'last_login', true);
        $last_login_date = $last_login ? date_i18n('M d, Y h:i A', (int)$last_login) : date_i18n('M d, Y h:i A');
        echo '<span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; margin-right: 5px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg> Last login: ' . $last_login_date . '</span>';
        echo '</div>';
        echo '</div>';

        echo '<div class="aa-stat-cards">';
        
        echo '<a href="' . wc_get_endpoint_url('orders') . '" class="aa-stat-card">';
        echo '<div class="aa-stat-icon blue"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg></div>';
        echo '<div class="aa-stat-info">';
        echo '<span>Total Orders</span>';
        echo '<strong>' . $total_orders . '</strong>';
        echo '</div></a>';

        echo '<a href="' . wc_get_endpoint_url('orders') . '" class="aa-stat-card">';
        echo '<div class="aa-stat-icon green"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></div>';
        echo '<div class="aa-stat-info">';
        echo '<span>Total Spent</span>';
        echo '<strong>' . wc_price($total_spent) . '</strong>';
        echo '</div></a>';

        echo '<a href="' . wc_get_endpoint_url('orders') . '" class="aa-stat-card">';
        echo '<div class="aa-stat-icon purple"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg></div>';
        echo '<div class="aa-stat-info">';
        echo '<span>Pending Orders</span>';
        echo '<strong>' . $pending_count . '</strong>';
        echo '</div></a>';

        echo '<a href="#" class="aa-stat-card">';
        echo '<div class="aa-stat-icon orange"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg></div>';
        echo '<div class="aa-stat-info">';
        echo '<span>Wishlist Items</span>';
        $wishlist_count = class_exists('\Alaosaf\Modules\Wishlist\WishlistModule') ? count(\Alaosaf\Modules\Wishlist\WishlistModule::getWishlist()) : 0;
        echo '<strong>' . esc_html($wishlist_count) . '</strong>';
        echo '</div></a>';

        echo '</div>'; 

        echo '<div class="aa-dashboard-main-grid">';
        
        echo '<div class="aa-recent-orders-card">';
        echo '<div class="aa-card-header">';
        echo '<h3>Recent Orders</h3>';
        echo '<a href="' . wc_get_endpoint_url('orders') . '">View all orders &rarr;</a>';
        echo '</div>';
        
        if (empty($recent_orders)) {
            echo '<p class="aa-no-orders">No recent orders found.</p>';
        } else {
            echo '<div class="aa-orders-list">';
            foreach ($recent_orders as $order) {
                $order_id = $order->get_id();
                $date = wc_format_datetime($order->get_date_created());
                $status = wc_get_order_status_name($order->get_status());
                $status_class = 'status-' . $order->get_status();
                $total = $order->get_formatted_order_total();
                $item_count = $order->get_item_count();

                echo '<div class="aa-order-item">';
                echo '<div class="aa-order-img"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px; color: #64748b;"><path stroke-linecap="round" stroke-linejoin="round" d="m21 8.25-5.25 2.75M21 8.25V15.75c0 .621-.504 1.125-1.125 1.125H5.125A1.125 1.125 0 0 1 4 15.75V8.25m17 0-9-5.25M4 8.25l5.25 2.75m11.75-2.75-9 5.25m0 0-9-5.25M13 13.5V21" /></svg></div>';
                echo '<div class="aa-order-details">';
                echo '<strong>Order #' . $order_id . '</strong>';
                echo '<span>' . $date . '</span>';
                echo '</div>';
                echo '<div class="aa-order-status"><span class="aa-badge ' . esc_attr($status_class) . '">' . esc_html($status) . '</span></div>';
                echo '<div class="aa-order-meta-right">';
                echo '<strong class="aa-order-total">' . wp_kses_post($order->get_formatted_order_total()) . '</strong>';
                echo '<span class="aa-order-items">' . $item_count . ' items</span>';
                echo '</div>';
                
                echo '<div class="aa-order-action-btn" style="margin-left: 20px;">';
                $invoice_url = site_url('/?aa_invoice=' . $order_id . '&order_key=' . $order->get_order_key());
                echo '<a href="' . esc_url($invoice_url) . '" target="_blank" class="aa-btn-invoice" style="background: #eff6ff; color: #3b82f6; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: 1px solid #bfdbfe; transition: all 0.3s;">Invoice</a>';
                echo '</div>';
                
                echo '</div>';
            }
            echo '</div>';
            echo '<div class="aa-orders-footer"><a href="' . wc_get_endpoint_url('orders') . '">View all orders &rarr;</a></div>';
        }
        echo '</div>';

        echo '</div>'; 
        echo '</div>'; 
    }

    public function renderProfilePictureUploadUI(): void {
        $user_id = get_current_user_id();
        $custom_avatar = get_user_meta($user_id, 'aa_custom_avatar', true);
        $avatar_url = $custom_avatar ? esc_url($custom_avatar) : get_avatar_url($user_id, ['size' => 150]);
        
        echo '<div class="aa-profile-upload-wrapper">';
        echo '<div class="aa-profile-upload-preview">';
        echo '<img src="' . $avatar_url . '" alt="Profile Picture" id="aa-avatar-preview-img">';
        echo '</div>';
        echo '<div class="aa-profile-upload-actions">';
        echo '<h4>Profile Picture</h4>';
        echo '<p>Upload a new avatar (JPEG or PNG, max 2MB).</p>';
        echo '<label for="aa_profile_picture" class="aa-btn-outline aa-upload-btn">Choose Image</label>';
        echo '<input type="file" name="aa_profile_picture" id="aa_profile_picture" accept="image/*" style="display: none;" onchange="document.getElementById(\'aa-avatar-preview-img\').src = window.URL.createObjectURL(this.files[0])">';
        echo '</div>';
        echo '</div>';
    }

    public function handleProfilePictureUpload($user_id): void {
        if (isset($_FILES['aa_profile_picture']) && $_FILES['aa_profile_picture']['error'] === UPLOAD_ERR_OK) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            
            $uploaded_file = $_FILES['aa_profile_picture'];
            
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($uploaded_file['type'], $allowed_types)) {
                wc_add_notice('Invalid file type. Please upload a JPEG or PNG.', 'error');
                return;
            }

            $upload_overrides = ['test_form' => false];
            $movefile = wp_handle_upload($uploaded_file, $upload_overrides);

            if ($movefile && !isset($movefile['error'])) {
                update_user_meta($user_id, 'aa_custom_avatar', $movefile['url']);
            } else {
                wc_add_notice($movefile['error'], 'error');
            }
        }
    }

    public function overrideAvatarUrl($url, $id_or_email, $args) {
        $user_id = 0;
        if (is_numeric($id_or_email)) {
            $user_id = (int)$id_or_email;
        } elseif (is_string($id_or_email) && ($user = get_user_by('email', $id_or_email))) {
            $user_id = $user->ID;
        } elseif (is_object($id_or_email) && !empty($id_or_email->user_id)) {
            $user_id = (int)$id_or_email->user_id;
        }

        if ($user_id) {
            $custom_avatar = get_user_meta($user_id, 'aa_custom_avatar', true);
            if ($custom_avatar) {
                return esc_url($custom_avatar);
            }
        }
        return $url;
    }

    private function renderRecommendedProducts(): void {
        if (!function_exists('wc_get_products')) return;

        $args = [
            'status' => 'publish',
            'limit' => 4,
            'orderby' => 'date',
            'order' => 'DESC',
        ];
        
        $products = wc_get_products($args);
        if (empty($products)) return;
        
        $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
        
        echo '<div class="aa-dashboard-recommended">';
        echo '<div class="aa-recommended-header">';
        echo '<h3>Recommended For You</h3>';
        echo '<a href="' . esc_url($shop_url) . '" class="aa-btn-view-all">View All Products &rarr;</a>';
        echo '</div>';
        
        echo '<div class="aa-recommended-grid">';
        foreach ($products as $product) {
            $image = $product->get_image('woocommerce_thumbnail');
            $title = $product->get_name();
            $price = $product->get_price_html();
            $url = $product->get_permalink();
            
            echo '<div class="aa-product-card">';
            echo '<a href="' . esc_url($url) . '" class="aa-product-img">' . $image . '</a>';
            echo '<div class="aa-product-info">';
            echo '<h4><a href="' . esc_url($url) . '">' . esc_html($title) . '</a></h4>';
            echo '<div class="aa-product-price">' . wp_kses_post($price) . '</div>';
            echo '<a href="' . esc_url($url) . '" class="aa-product-btn">View Details</a>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }

    public function renderCustomOrdersEndpoint($current_page): void {
        $current_page    = empty( $current_page ) ? 1 : absint( $current_page );
        $customer_orders = wc_get_orders( [
            'customer' => get_current_user_id(),
            'page'     => $current_page,
            'paginate' => true,
        ] );

        echo '<div class="aa-dashboard-card" style="margin-top: 0; border: none; box-shadow: none;">';
        echo '<div class="aa-card-header">';
        echo '<h3>All Orders</h3>';
        echo '</div>';

        if ( ! $customer_orders || empty( $customer_orders->orders ) ) {
            echo '<p class="aa-no-orders">No orders found.</p>';
        } else {
            echo '<div class="aa-orders-list">';
            foreach ( $customer_orders->orders as $order ) {
                $order_id = $order->get_id();
                $date = wc_format_datetime($order->get_date_created());
                $status = wc_get_order_status_name($order->get_status());
                $status_class = 'status-' . $order->get_status();
                $item_count = $order->get_item_count();

                echo '<div class="aa-order-item">';
                echo '<div class="aa-order-img"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px; color: #64748b;"><path stroke-linecap="round" stroke-linejoin="round" d="m21 8.25-5.25 2.75M21 8.25V15.75c0 .621-.504 1.125-1.125 1.125H5.125A1.125 1.125 0 0 1 4 15.75V8.25m17 0-9-5.25M4 8.25l5.25 2.75m11.75-2.75-9 5.25m0 0-9-5.25M13 13.5V21" /></svg></div>';
                echo '<div class="aa-order-details">';
                echo '<strong>Order #' . $order_id . '</strong>';
                echo '<span>' . $date . '</span>';
                echo '</div>';
                
                echo '<div class="aa-order-status"><span class="aa-badge ' . esc_attr($status_class) . '">' . esc_html($status) . '</span></div>';
                
                echo '<div class="aa-order-meta-right">';
                echo '<strong class="aa-order-total">' . wp_kses_post($order->get_formatted_order_total()) . '</strong>';
                echo '<span class="aa-order-items">' . $item_count . ' items</span>';
                echo '</div>';
                
                echo '<div class="aa-order-action-btn" style="margin-left: 20px; display: flex; gap: 8px;">';
                echo '<a href="' . esc_url($order->get_view_order_url()) . '" class="aa-btn-invoice" style="background: #f8fafc; color: #475569; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: 1px solid #e2e8f0; transition: all 0.3s;">View</a>';
                
                $invoice_url = site_url('/?aa_invoice=' . $order_id . '&order_key=' . $order->get_order_key());
                echo '<a href="' . esc_url($invoice_url) . '" target="_blank" class="aa-btn-invoice" style="background: #eff6ff; color: #3b82f6; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: 1px solid #bfdbfe; transition: all 0.3s;">Invoice</a>';
                echo '</div>';
                
                echo '</div>';
            }
            echo '</div>';
            
            // Pagination
            if ( 1 < $customer_orders->max_num_pages ) {
                echo '<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination" style="margin-top:20px; text-align:right; border-top: 1px solid #f1f5f9; padding-top: 20px; display: flex; justify-content: space-between; align-items: center;">';
                echo '<div class="pagination-info" style="font-size: 13px; color: #64748b;">Page ' . $current_page . ' of ' . $customer_orders->max_num_pages . '</div>';
                echo '<div class="pagination-buttons">';
                if ( 1 !== $current_page ) {
                    echo '<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="' . esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ) . '" style="background: #f1f5f9; color: #475569; padding: 8px 12px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px; margin-right: 4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg> Previous</a>';
                }
                if ( intval( $customer_orders->max_num_pages ) !== $current_page ) {
                    echo '<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="' . esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ) . '" style="margin-left:10px; background: #c59b5f; color: #fff; padding: 8px 12px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center;">Next <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px; margin-left: 4px;"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg></a>';
                }
                echo '</div>';
                echo '</div>';
            }
        }
        echo '</div>';
    }
}
