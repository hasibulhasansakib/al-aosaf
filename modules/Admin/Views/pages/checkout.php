<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$default_fields = [
    'billing' => [
        'billing_first_name' => 'আপনার নাম', // Your Name
        'billing_last_name'  => 'Last Name',
        'billing_company'    => 'Company Name',
        'billing_country'    => 'Country / Region',
        'billing_address_1'  => 'আপনার সম্পূর্ণ ঠিকানা', // Your Full Address
        'billing_address_2'  => 'Apartment, suite, etc.',
        'billing_city'       => 'Town / City',
        'billing_state'      => 'State / County',
        'billing_postcode'   => 'Postcode / ZIP',
        'billing_phone'      => 'মোবাইল নাম্বার', // Mobile Number
        'billing_email'      => 'Email Address'
    ],
    'shipping' => [
        'shipping_first_name' => 'First Name',
        'shipping_last_name'  => 'Last Name',
        'shipping_company'    => 'Company Name',
        'shipping_country'    => 'Country / Region',
        'shipping_address_1'  => 'Street Address',
        'shipping_address_2'  => 'Apartment, suite, etc.',
        'shipping_city'       => 'Town / City',
        'shipping_state'      => 'State / County',
        'shipping_postcode'   => 'Postcode / ZIP',
    ],
    'order' => [
        'order_comments' => 'Order Notes'
    ]
];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['aa_checkout_settings_nonce']) && wp_verify_nonce($_POST['aa_checkout_settings_nonce'], 'aa_save_checkout_settings')) {
        
        $saved_config = [];

        foreach ($default_fields as $section => $fields) {
            foreach ($fields as $key => $default_label) {
                $active = isset($_POST['aa_fields'][$key]['active']) ? 'yes' : 'no';
                $required = isset($_POST['aa_fields'][$key]['required']) ? 'yes' : 'no';
                $label = isset($_POST['aa_fields'][$key]['label']) ? sanitize_text_field($_POST['aa_fields'][$key]['label']) : '';
                
                $saved_config[$key] = [
                    'active'   => $active,
                    'required' => $required,
                    'label'    => $label
                ];
            }
        }

        update_option('aa_checkout_fields_config', json_encode($saved_config));
        echo '<div class="notice notice-success is-dismissible"><p>Advanced Checkout Fields Saved Successfully!</p></div>';
    }

    if (isset($_POST['aa_checkout_notice_nonce']) && wp_verify_nonce($_POST['aa_checkout_notice_nonce'], 'aa_save_checkout_notice')) {
        $notice_enabled = isset($_POST['aa_checkout_notice_enabled']) ? 'yes' : 'no';
        $pci_enabled = isset($_POST['aa_checkout_pci_enabled']) ? 'yes' : 'no';
        $notice_height = isset($_POST['aa_checkout_notice_height']) ? absint($_POST['aa_checkout_notice_height']) : 120;
        // Use wp_kses_post to safely save HTML from wp_editor
        $notice_content = isset($_POST['aa_checkout_notice_content']) ? wp_kses_post(wp_unslash($_POST['aa_checkout_notice_content'])) : '';

        $pci_header = isset($_POST['aa_checkout_pci_header']) ? sanitize_text_field($_POST['aa_checkout_pci_header']) : '';
        $pci_intro = isset($_POST['aa_checkout_pci_intro']) ? sanitize_textarea_field($_POST['aa_checkout_pci_intro']) : '';
        $pci_list_title = isset($_POST['aa_checkout_pci_list_title']) ? sanitize_text_field($_POST['aa_checkout_pci_list_title']) : '';
        $pci_list_items = isset($_POST['aa_checkout_pci_list_items']) ? sanitize_textarea_field($_POST['aa_checkout_pci_list_items']) : '';
        $pci_warning = isset($_POST['aa_checkout_pci_warning']) ? sanitize_textarea_field($_POST['aa_checkout_pci_warning']) : '';

        update_option('aa_checkout_notice_enabled', $notice_enabled);
        update_option('aa_checkout_pci_enabled', $pci_enabled);
        update_option('aa_checkout_notice_height', $notice_height);
        update_option('aa_checkout_notice_content', $notice_content);

        update_option('aa_checkout_pci_header', $pci_header);
        update_option('aa_checkout_pci_intro', $pci_intro);
        update_option('aa_checkout_pci_list_title', $pci_list_title);
        update_option('aa_checkout_pci_list_items', $pci_list_items);
        update_option('aa_checkout_pci_warning', $pci_warning);

        echo '<div class="notice notice-success is-dismissible"><p>Checkout Notice Settings Saved Successfully!</p></div>';
    }
}

// Get Saved Configs
$config_json = get_option('aa_checkout_fields_config', '{}');
$config = json_decode($config_json, true) ?: [];

$notice_enabled = get_option('aa_checkout_notice_enabled', 'no');
$pci_enabled = get_option('aa_checkout_pci_enabled', 'yes');
$notice_height = get_option('aa_checkout_notice_height', 120);

// Default PCI content
$def_pci_items = "<strong>Water Resistant</strong> ফিচার ঠিক আছে কিনা\nচেইন সঠিকভাবে কাজ করছে কিনা\nঘড়ি সচল ও সঠিকভাবে চলছে কিনা\nপণ্যের অন্যান্য সকল ফিচার ও অবস্থা";
$pci_header = get_option('aa_checkout_pci_header', 'গুরুত্বপূর্ণ নির্দেশনা');
$pci_intro = get_option('aa_checkout_pci_intro', 'ডেলিভারি ম্যানের উপস্থিতিতে পন্যটি ভালোভাবে পরীক্ষা করে সন্তুষ্ট হওয়ার পরই মূল্য পরিশোধ করুন।');
$pci_list_title = get_option('aa_checkout_pci_list_title', 'অবশ্যই নিচের বিষয়গুলো ভালোভাবে চেক করে নিন:');
$pci_list_items = get_option('aa_checkout_pci_list_items', $def_pci_items);
$pci_warning = get_option('aa_checkout_pci_warning', 'ডেলিভারি ম্যান চলে যাওয়ার পর কোনো অভিযোগ গ্রহণযোগ্য হবে না।');

// Default notice content replicating the screenshot structure nicely
$default_notice_content = '<div style="background-color: #10593b; color: white; padding: 10px; border-radius: 4px 4px 0 0; text-align: center; font-weight: bold; font-size: 16px;">🤝 আন্তরিক অনুরোধ</div>
<div style="border: 1px solid #10593b; border-top: none; padding: 15px; border-radius: 0 0 4px 4px;">
<p style="margin-top: 0;"><strong>আপনার সহযোগিতা আমাদের জন্য অত্যন্ত গুরুত্বপূর্ণ।</strong></p>
<p>অনুগ্রহ করে শুধুমাত্র প্রয়োজন হলে এবং পণ্যটি গ্রহণ করার ইচ্ছা থাকলেই অর্ডার করুন। অযথা পার্সেল রিটার্ন হলে আমাদের পরিবহন, প্যাকেজিং, ডেলিভারি এবং পরিচালনাগত খরচের কারণে উল্লেখযোগ্য ক্ষতির সম্মুখীন হতে হয়। আমরা বিশ্বাস করি, আপনি একজন সচেতন ও দায়িত্বশীল ক্রেতা হিসেবে অপ্রয়োজনীয় রিটার্ন এড়িয়ে চলবেন এবং আমাদের এই ক্ষতি থেকে রক্ষা করতে সহযোগিতা করবেন।</p>
<div style="text-align: center; padding: 10px; background: #f9f9f9; border-top: 1px dashed #ccc; border-bottom: 1px dashed #ccc; margin: 15px 0;">🤝 দায়িত্বশীল অর্ডার আমাদের সেবা আরও উন্নত করতে সাহায্য করে</div>
<div style="border-left: 4px solid #d32f2f; background: #fff5f5; padding: 10px;">
<p style="color: #d32f2f; margin: 0 0 5px 0; font-weight: bold;">⚠️ বিশেষ সতর্কতা</p>
<p style="margin: 0;">অনুগ্রহ করে ডেলিভারি ম্যানের উপস্থিতিতে পণ্যটি সম্পূর্ণভাবে পরীক্ষা করে নিন। পণ্য যাচাই-বাছাই করে সন্তুষ্ট হওয়ার পরই মূল্য পরিশোধ করুন। ডেলিভারি ম্যান চলে যাওয়ার পর কোনো সমস্যা, ত্রুটি বা অভিযোগ থাকলে কর্তৃপক্ষ দায়ী থাকবে থাকতে।</p>
</div>
<p style="text-align: center; margin-bottom: 0; font-weight: bold;">আপনাদের সহযোগিতার জন্য আন্তরিক ধন্যবাদ।</p>
</div>';
$notice_content = get_option('aa_checkout_notice_content', $default_notice_content);

// Current Tab
$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'fields';
?>

<div class="wrap">
    <h2 class="nav-tab-wrapper" style="margin-top: 20px;">
        <a href="?page=aa-checkout&tab=fields" class="nav-tab <?php echo $active_tab == 'fields' ? 'nav-tab-active' : ''; ?>">Fields Manager</a>
        <a href="?page=aa-checkout&tab=notice" class="nav-tab <?php echo $active_tab == 'notice' ? 'nav-tab-active' : ''; ?>">Notices & Instructions</a>
    </h2>

    <?php if ($active_tab == 'fields'): ?>
    <div class="aa-page-content" style="background: #fff; padding: 30px; border: 1px solid #ccd0d4; border-top: none; border-radius: 0 0 4px 4px;">
        <h2 style="margin-top:0;">Advanced Checkout Field Manager</h2>
        <p>Take complete control over your checkout page. Enable/Disable fields, make them Required/Optional, and rename their labels.</p>
        
        <form method="POST" action="">
            <?php wp_nonce_field('aa_save_checkout_settings', 'aa_checkout_settings_nonce'); ?>
            
            <?php foreach ($default_fields as $section => $fields): ?>
                <h3 style="margin-top: 40px; padding-bottom: 10px; border-bottom: 1px solid #eee; text-transform: capitalize; color: #111;">
                    <?php echo esc_html($section); ?> Fields
                </h3>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Field Key</th>
                            <th style="width: 15%; text-align: center;">Enable Field</th>
                            <th style="width: 15%; text-align: center;">Required</th>
                            <th style="width: 45%;">Custom Label Override</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fields as $key => $default_label): 
                            $is_default_active = in_array($key, ['billing_first_name', 'billing_address_1', 'billing_phone']) ? 'yes' : 'no';
                            $active = $config[$key]['active'] ?? $is_default_active;
                            $required = $config[$key]['required'] ?? $is_default_active;
                            $label = $config[$key]['label'] ?? '';
                            $placeholder = $default_label;
                        ?>
                        <tr>
                            <td style="vertical-align: middle;">
                                <strong><?php echo esc_html($default_label); ?></strong><br>
                                <span style="color:#666; font-size: 11px;"><code><?php echo esc_html($key); ?></code></span>
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <label class="switch">
                                    <input type="checkbox" name="aa_fields[<?php echo esc_attr($key); ?>][active]" value="1" <?php checked($active, 'yes'); ?>>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <label class="switch switch-blue">
                                    <input type="checkbox" name="aa_fields[<?php echo esc_attr($key); ?>][required]" value="1" <?php checked($required, 'yes'); ?>>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td style="vertical-align: middle;">
                                <input type="text" name="aa_fields[<?php echo esc_attr($key); ?>][label]" value="<?php echo esc_attr($label); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" style="width: 100%; border-radius: 4px; padding: 6px 12px;">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
            
            <p class="submit" style="margin-top: 30px;">
                <button type="submit" class="button button-primary button-hero" style="background: #f38624; border-color: #e0771c; box-shadow: none;">Save All Fields Configuration</button>
            </p>
        </form>
    </div>
    <?php endif; ?>

    <?php if ($active_tab == 'notice'): ?>
    <div class="aa-page-content" style="background: #fff; padding: 30px; border: 1px solid #ccd0d4; border-top: none; border-radius: 0 0 4px 4px;">
        <h2 style="margin-top:0;">Checkout Notices & Instructions</h2>
        <p>Manage the notices and instructions displayed above and below the checkout form.</p>
        
        <form method="POST" action="">
            <?php wp_nonce_field('aa_save_checkout_notice', 'aa_checkout_notice_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Post-Checkout Instructions (Bottom)</th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="aa_checkout_pci_enabled" value="1" <?php checked($pci_enabled, 'yes'); ?>>
                            <span class="slider round"></span>
                        </label>
                        <p class="description">Enable the beautiful premium "Important Instructions" block at the bottom of the checkout page.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">PCI Header Title</th>
                    <td>
                        <input type="text" name="aa_checkout_pci_header" value="<?php echo esc_attr($pci_header); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">PCI Intro Text</th>
                    <td>
                        <textarea name="aa_checkout_pci_intro" rows="2" class="large-text"><?php echo esc_textarea($pci_intro); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row">PCI Checklist Title</th>
                    <td>
                        <input type="text" name="aa_checkout_pci_list_title" value="<?php echo esc_attr($pci_list_title); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">PCI Checklist Items</th>
                    <td>
                        <textarea name="aa_checkout_pci_list_items" rows="5" class="large-text"><?php echo esc_textarea($pci_list_items); ?></textarea>
                        <p class="description">Enter one item per line. You can use simple HTML like <code>&lt;strong&gt;</code>.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">PCI Warning Text</th>
                    <td>
                        <textarea name="aa_checkout_pci_warning" rows="2" class="large-text"><?php echo esc_textarea($pci_warning); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th colspan="2"><hr style="margin: 20px 0;"></th>
                </tr>
                <tr>
                    <th scope="row">Enable Top Notice Accordion</th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="aa_checkout_notice_enabled" value="1" <?php checked($notice_enabled, 'yes'); ?>>
                            <span class="slider round"></span>
                        </label>
                        <p class="description">If enabled, this notice will appear in a compact accordion above the checkout form.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Visible Height (px)</th>
                    <td>
                        <input type="number" name="aa_checkout_notice_height" value="<?php echo esc_attr($notice_height); ?>" style="width: 100px;"> px
                        <p class="description">Control exactly how tall the box is before the user clicks to expand it. Try <strong>180</strong> or <strong>200</strong> to show 3-4 lines of text.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Notice Content</th>
                    <td>
                        <?php 
                        $settings = array(
                            'textarea_name' => 'aa_checkout_notice_content',
                            'textarea_rows' => 15,
                            'media_buttons' => true,
                            'teeny'         => false,
                        );
                        wp_editor($notice_content, 'aa_checkout_notice_editor', $settings); 
                        ?>
                        <p class="description">Format your text here. The text will be highly compressed on the frontend to save space.</p>
                    </td>
                </tr>
            </table>

            <p class="submit" style="margin-top: 30px;">
                <button type="submit" class="button button-primary button-hero" style="background: #f38624; border-color: #e0771c; box-shadow: none;">Save Notice Configuration</button>
            </p>
        </form>
    </div>
    <?php endif; ?>
</div>

<style>
/* Simple Toggle Switch CSS */
.switch { position: relative; display: inline-block; width: 40px; height: 20px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .4s; }
.slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 2px; bottom: 2px; background-color: white; transition: .4s; }
input:checked + .slider { background-color: #10b981; } /* Green for Active */
input:focus + .slider { box-shadow: 0 0 1px #10b981; }
input:checked + .slider:before { transform: translateX(20px); }
.slider.round { border-radius: 20px; }
.slider.round:before { border-radius: 50%; }

/* Blue Toggle for Required */
.switch-blue input:checked + .slider { background-color: #3b82f6; }
.switch-blue input:focus + .slider { box-shadow: 0 0 1px #3b82f6; }
</style>
