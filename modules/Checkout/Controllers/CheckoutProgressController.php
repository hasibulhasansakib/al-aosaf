<?php
namespace Alaosaf\Modules\Checkout\Controllers;

class CheckoutProgressController {

    public function init(): void {
        // Enqueue Assets
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets'], 99);

        // Inject Progress Bar before Cart
        add_action('woocommerce_before_cart', [$this, 'renderCartProgressBar'], 5);

        // Inject Progress Bar before Checkout
        add_action('woocommerce_before_checkout_form', [$this, 'renderCheckoutProgressBar'], 5);

        // Inject Progress Bar on Thank You Page
        add_action('woocommerce_before_thankyou', [$this, 'renderThankYouProgressBar'], 5);
        
        // Add Custom Order Details Card
        add_action('woocommerce_thankyou', [$this, 'renderCustomOrderDetails'], 8);
        
        // Add Invoice Download Button Placeholder
        add_action('woocommerce_thankyou', [$this, 'renderInvoiceButton'], 9);

        // Filter Checkout Fields based on Plugin Settings
        add_filter('woocommerce_checkout_fields', [$this, 'filterCheckoutFields'], PHP_INT_MAX);
        add_filter('woocommerce_default_address_fields', [$this, 'filterDefaultAddressFields'], PHP_INT_MAX);
        add_filter('woocommerce_form_field_args', [$this, 'filterFormFieldArgs'], PHP_INT_MAX, 3);
        
        // Add Remove Button to Checkout Items
        add_filter('woocommerce_cart_item_name', [$this, 'addRemoveButtonToCheckout'], 10, 3);
    }

    public function enqueueAssets() {
        if (is_cart() || is_checkout() || is_checkout_pay_page() || is_order_received_page()) {
            wp_enqueue_style('aa-checkout-progress-css', AA_PLUGIN_URL . 'modules/Checkout/assets/css/checkout-progress.css', [], time());
        }
        if (is_checkout() && !is_order_received_page()) {
            wp_enqueue_style('aa-checkout-ui', AA_PLUGIN_URL . 'modules/Checkout/assets/css/checkout-ui-v2.css', [], time());
        }
        if (is_order_received_page()) {
            wp_enqueue_style('aa-thankyou-ui', AA_PLUGIN_URL . 'modules/Checkout/assets/css/thankyou-ui.css', [], time());
        }
    }

    public function renderCartProgressBar() {
        $this->renderProgressBar(1);
    }

    public function renderCheckoutProgressBar() {
        $this->renderProgressBar(2);
    }

    public function renderThankYouProgressBar($order_id) {
        $this->renderProgressBar(3, $order_id);
    }

    public function renderCustomOrderDetails($order_id) {
        if (!$order_id) return;
        $order = wc_get_order($order_id);
        if (!$order) return;

        $order_number = $order->get_order_number();
        $name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $phone = $order->get_billing_phone();
        $address = $order->get_formatted_billing_address();
        $total = $order->get_formatted_order_total();
        
        echo '<div class="aa-premium-receipt-wrapper">';
        
        // 1. Overview Row
        echo '  <ul class="aa-overview-list">';
        echo '      <li class="aa-overview-item"><span>Order Number</span><strong>#' . esc_html($order_number) . '</strong></li>';
        echo '      <li class="aa-overview-item"><span>Customer Name</span><strong>' . esc_html(trim($name)) . '</strong></li>';
        echo '      <li class="aa-overview-item"><span>Phone Number</span><strong>' . esc_html($phone) . '</strong></li>';
        echo '      <li class="aa-overview-item"><span>Total Amount</span><strong>' . wp_kses_post($total) . '</strong></li>';
        echo '  </ul>';

        echo '</div>'; // End wrapper
    }

    public function renderInvoiceButton($order_id) {
        if (!$order_id) return;
        
        $order = wc_get_order($order_id);
        if (!$order) return;
        
        $invoice_url = site_url('/?aa_invoice=' . $order_id . '&order_key=' . $order->get_order_key() . '&action=download');

        echo '<div class="aa-invoice-download-wrapper">';
        echo '<a href="' . esc_url($invoice_url) . '" target="_blank" class="aa-btn-download-invoice">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>';
        echo ' <span>Download Invoice</span>';
        echo '</a>';
        echo '</div>';
    }

    private function renderProgressBar(int $current_step, $order_id = null) {
        $steps = [
            1 => [
                'title' => 'Cart',
                'desc' => 'Review your items',
                'url' => wc_get_cart_url(),
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>'
            ],
            2 => [
                'title' => 'Checkout',
                'desc' => 'Payment & Shipping',
                'url' => wc_get_checkout_url(),
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>'
            ],
            3 => [
                'title' => 'Done',
                'desc' => 'Thank you!',
                'url' => '#',
                'icon' => '<svg class="aa-truck-anim" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>'
            ]
        ];

        $customer_name = '';
        $customer_phone = '';
        if ($order_id) {
            $order = wc_get_order($order_id);
            if ($order) {
                $customer_name = $order->get_billing_first_name();
                $customer_phone = $order->get_billing_phone();
            }
        }

        ob_start();
        include AA_PLUGIN_DIR . 'modules/Checkout/Views/frontend/progress-bar.php';
        $content = ob_get_clean();
        
        echo $content;
    }

    public function filterCheckoutFields($fields) {
        $config_json = get_option('aa_checkout_fields_config', '{}');
        $config = json_decode($config_json, true) ?: [];

        $allowed_fields = ['billing_first_name', 'billing_address_1', 'billing_phone'];

        foreach (['billing', 'shipping', 'order'] as $section) {
            if (!isset($fields[$section])) continue;
            
            foreach ($fields[$section] as $key => $field_data) {
                // 1. Active / Disable
                $is_active = isset($config[$key]['active']) ? $config[$key]['active'] : (in_array($key, $allowed_fields) ? 'yes' : 'no');
                
                if ($is_active === 'no') {
                    unset($fields[$section][$key]);
                    continue;
                }

                // 2. Required / Optional
                $is_required = isset($config[$key]['required']) ? $config[$key]['required'] : (in_array($key, $allowed_fields) ? 'yes' : 'no');
                $fields[$section][$key]['required'] = ($is_required === 'yes');

                // 3. Label & Placeholder
                $label = isset($config[$key]['label']) ? sanitize_text_field($config[$key]['label']) : '';

                // Force Bengali defaults if label is empty or matches English defaults
                if ($key === 'billing_first_name' && (empty($label) || $label === 'First Name' || $label === 'First name')) {
                    $label = 'আপনার নাম';
                } elseif ($key === 'billing_address_1' && (empty($label) || strpos(strtolower($label), 'address') !== false)) {
                    $label = 'আপনার সম্পূর্ণ ঠিকানা';
                } elseif ($key === 'billing_phone' && (empty($label) || $label === 'Phone')) {
                    $label = 'মোবাইল নাম্বার';
                }

                if (!empty($label)) {
                    $fields[$section][$key]['label'] = $label;
                    $fields[$section][$key]['placeholder'] = $label;
                }

                // 4. Force Full Width
                if ($key === 'billing_first_name' || $key === 'billing_last_name') {
                    $fields[$section][$key]['class'] = ['form-row-wide'];
                }
            }
        }

        return $fields;
    }

    public function filterDefaultAddressFields($fields) {
        $config_json = get_option('aa_checkout_fields_config', '{}');
        $config = json_decode($config_json, true) ?: [];

        // Force First Name
        if (isset($fields['first_name'])) {
            $label = isset($config['billing_first_name']['label']) ? sanitize_text_field($config['billing_first_name']['label']) : '';
            if (empty($label) || $label === 'First Name' || $label === 'First name') {
                $label = 'আপনার নাম';
            }
            $fields['first_name']['label'] = $label;
            $fields['first_name']['placeholder'] = $label;
            $fields['first_name']['class'] = ['form-row-wide'];
            $fields['first_name']['clear'] = true;
        }

        // Force Last Name Full Width
        if (isset($fields['last_name'])) {
            $fields['last_name']['class'] = ['form-row-wide'];
        }

        // Force Address 1
        if (isset($fields['address_1'])) {
            $label = isset($config['billing_address_1']['label']) ? sanitize_text_field($config['billing_address_1']['label']) : '';
            if (empty($label) || strpos(strtolower($label), 'address') !== false) {
                $label = 'আপনার সম্পূর্ণ ঠিকানা';
            }
            $fields['address_1']['label'] = $label;
            $fields['address_1']['placeholder'] = $label;
        }

        return $fields;
    }

    public function filterFormFieldArgs($args, $key, $value) {
        $config_json = get_option('aa_checkout_fields_config', '{}');
        $config = json_decode($config_json, true) ?: [];

        // Ultimate Override for First Name
        if ($key === 'billing_first_name' || $key === 'first_name') {
            $label = isset($config['billing_first_name']['label']) ? sanitize_text_field($config['billing_first_name']['label']) : '';
            if (empty($label) || $label === 'First Name' || $label === 'First name') {
                $label = 'আপনার নাম';
            }
            $args['label'] = $label;
            $args['placeholder'] = $label;
            $args['class'] = ['form-row-wide'];
            $args['clear'] = true;
        }

        // Ultimate Override for Address
        if ($key === 'billing_address_1' || $key === 'address_1') {
            $label = isset($config['billing_address_1']['label']) ? sanitize_text_field($config['billing_address_1']['label']) : '';
            if (empty($label) || strpos(strtolower($label), 'address') !== false || strpos(strtolower($label), 'house') !== false) {
                $label = 'আপনার সম্পূর্ণ ঠিকানা';
            }
            $args['label'] = $label;
            $args['placeholder'] = $label;
        }

        // Ultimate Override for Phone
        if ($key === 'billing_phone' || $key === 'phone') {
            $label = isset($config['billing_phone']['label']) ? sanitize_text_field($config['billing_phone']['label']) : '';
            if (empty($label) || $label === 'Phone') {
                $label = 'মোবাইল নাম্বার';
            }
            $args['label'] = $label;
            $args['placeholder'] = $label;
        }

        return $args;
    }

    public function addRemoveButtonToCheckout($product_name, $cart_item, $cart_item_key) {
        if (is_checkout() && !is_order_received_page()) {
            $remove_url = wc_get_cart_remove_url($cart_item_key);
            $remove_btn = sprintf(
                '<a href="%s" class="aa-remove-item-checkout" style="display:block; font-size:12px; color:#ef4444; margin-top:5px; text-decoration:none;">× Remove Item</a>',
                esc_url($remove_url)
            );
            return $product_name . $remove_btn;
        }
        return $product_name;
    }
}
