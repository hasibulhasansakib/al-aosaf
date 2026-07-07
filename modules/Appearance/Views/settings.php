<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="aa-page-content" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
    <h2 style="margin-top:0;">Brand Management System</h2>
    
    <?php if (isset($_GET['updated']) && $_GET['updated'] === 'true'): ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Brand settings saved successfully.', 'al-aosaf'); ?></p></div>
    <?php endif; ?>

    <h2 class="nav-tab-wrapper" style="margin-bottom: 20px;">
        <a href="?page=aa-appearance&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">General</a>
        <a href="?page=aa-appearance&tab=branding" class="nav-tab <?php echo $active_tab === 'branding' ? 'nav-tab-active' : ''; ?>">Branding</a>
        <a href="?page=aa-appearance&tab=colors" class="nav-tab <?php echo $active_tab === 'colors' ? 'nav-tab-active' : ''; ?>">Colors</a>
        <a href="?page=aa-appearance&tab=contact" class="nav-tab <?php echo $active_tab === 'contact' ? 'nav-tab-active' : ''; ?>">Contact</a>
        <a href="?page=aa-appearance&tab=social" class="nav-tab <?php echo $active_tab === 'social' ? 'nav-tab-active' : ''; ?>">Social</a>
        <a href="?page=aa-appearance&tab=business" class="nav-tab <?php echo $active_tab === 'business' ? 'nav-tab-active' : ''; ?>">Business</a>
    </h2>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="aa_save_brand_settings">
        <input type="hidden" name="current_tab" value="<?php echo esc_attr($active_tab); ?>">
        <?php wp_nonce_field('aa_save_brand_settings', 'aa_brand_nonce'); ?>

        <table class="form-table">
            <?php if ($active_tab === 'general'): ?>
                <tr>
                    <th scope="row"><label for="aa_brand_general_business_name">Business Name</label></th>
                    <td><input type="text" name="aa_brand_general_business_name" id="aa_brand_general_business_name" value="<?php echo esc_attr($settings['general_business_name'] ?? ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="aa_brand_general_website_name">Website Name</label></th>
                    <td><input type="text" name="aa_brand_general_website_name" id="aa_brand_general_website_name" value="<?php echo esc_attr($settings['general_website_name'] ?? ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="aa_brand_general_tagline">Tagline</label></th>
                    <td><input type="text" name="aa_brand_general_tagline" id="aa_brand_general_tagline" value="<?php echo esc_attr($settings['general_tagline'] ?? ''); ?>" class="regular-text"></td>
                </tr>

            <?php elseif ($active_tab === 'branding'): ?>
                <tr>
                    <th scope="row"><label for="aa_brand_branding_logo_type">Logo Type</label></th>
                    <td>
                        <select name="aa_brand_branding_logo_type" id="aa_brand_branding_logo_type" class="aa-logo-type-select">
                            <option value="image" <?php selected($settings['branding_logo_type'] ?? 'image', 'image'); ?>>Image Logo</option>
                            <option value="text" <?php selected($settings['branding_logo_type'] ?? 'image', 'text'); ?>>Text Logo</option>
                            <option value="image_text" <?php selected($settings['branding_logo_type'] ?? 'image', 'image_text'); ?>>Image + Text Logo</option>
                        </select>
                    </td>
                </tr>
                <tr class="aa-logo-text-row">
                    <th scope="row"><label for="aa_brand_branding_logo_text">Logo Text</label></th>
                    <td>
                        <input type="text" name="aa_brand_branding_logo_text" id="aa_brand_branding_logo_text" value="<?php echo esc_attr($settings['branding_logo_text'] ?? ''); ?>" class="regular-text">
                        <p class="description">Used when Logo Type is set to Text or Image + Text.</p>
                    </td>
                </tr>
                <?php
                $logos = [
                    'branding_primary_logo' => 'Primary Logo',
                    'branding_dark_logo' => 'Dark Logo',
                    'branding_light_logo' => 'Light Logo',
                    'branding_mobile_logo' => 'Mobile Logo',
                    'branding_favicon' => 'Favicon',
                    'branding_invoice_logo' => 'Invoice Logo',
                    'branding_email_logo' => 'Email Logo'
                ];
                foreach ($logos as $key => $label): ?>
                <tr>
                    <th scope="row"><label><?php echo esc_html($label); ?></label></th>
                    <td>
                        <input type="text" name="aa_brand_<?php echo $key; ?>" id="aa_brand_<?php echo $key; ?>" value="<?php echo esc_attr($settings[$key] ?? ''); ?>" class="regular-text">
                        <button type="button" class="button aa-upload-media" data-target="#aa_brand_<?php echo $key; ?>">Upload Image</button>
                    </td>
                </tr>
                <?php endforeach; ?>

            <?php elseif ($active_tab === 'colors'): ?>
                <?php
                $colors = [
                    'colors_primary' => ['Primary Color', '#C8A15A'],
                    'colors_primary_hover' => ['Primary Hover', '#E0B96D'],
                    'colors_secondary' => ['Secondary Color', '#D9D9D9'],
                    'colors_background' => ['Background Color', '#050505'],
                    'colors_surface' => ['Surface Color', '#111111'],
                    'colors_text' => ['Text Color', '#F5F5F5'],
                    'colors_muted' => ['Muted Text', '#A0A0A0'],
                    'colors_border' => ['Border Color', 'rgba(200,161,90,.2)']
                ];
                foreach ($colors as $key => $data): ?>
                <tr>
                    <th scope="row"><label for="aa_brand_<?php echo $key; ?>"><?php echo esc_html($data[0]); ?></label></th>
                    <td><input type="text" name="aa_brand_<?php echo $key; ?>" id="aa_brand_<?php echo $key; ?>" value="<?php echo esc_attr($settings[$key] ?? $data[1]); ?>" class="aa-color-picker" data-default-color="<?php echo esc_attr($data[1]); ?>"></td>
                </tr>
                <?php endforeach; ?>

            <?php elseif ($active_tab === 'contact'): ?>
                <?php
                $contact = [
                    'contact_phone' => 'Phone Number',
                    'contact_whatsapp' => 'WhatsApp Number',
                    'contact_email' => 'Email Address',
                    'contact_support_email' => 'Support Email',
                    'contact_address' => 'Address',
                    'contact_map_url' => 'Google Map URL',
                    'contact_hours' => 'Business Hours'
                ];
                foreach ($contact as $key => $label): ?>
                <tr>
                    <th scope="row"><label for="aa_brand_<?php echo $key; ?>"><?php echo esc_html($label); ?></label></th>
                    <td><input type="text" name="aa_brand_<?php echo $key; ?>" id="aa_brand_<?php echo $key; ?>" value="<?php echo esc_attr($settings[$key] ?? ''); ?>" class="regular-text"></td>
                </tr>
                <?php endforeach; ?>

            <?php elseif ($active_tab === 'social'): ?>
                <?php
                $social = ['facebook', 'instagram', 'twitter', 'youtube', 'tiktok', 'linkedin', 'pinterest', 'telegram'];
                foreach ($social as $network): ?>
                <tr>
                    <th scope="row"><label for="aa_brand_social_<?php echo $network; ?>"><?php echo ucfirst($network); ?></label></th>
                    <td><input type="text" name="aa_brand_social_<?php echo $network; ?>" id="aa_brand_social_<?php echo $network; ?>" value="<?php echo esc_attr($settings['social_' . $network] ?? ''); ?>" class="regular-text" placeholder="https://..."></td>
                </tr>
                <?php endforeach; ?>

            <?php elseif ($active_tab === 'business'): ?>
                <?php
                $business = [
                    'business_company_name' => 'Company Name',
                    'business_vat' => 'VAT Number',
                    'business_tax' => 'Tax Number',
                    'business_invoice_footer' => 'Invoice Footer Text',
                    'business_currency' => 'Currency',
                    'business_country' => 'Country'
                ];
                foreach ($business as $key => $label): ?>
                <tr>
                    <th scope="row"><label for="aa_brand_<?php echo $key; ?>"><?php echo esc_html($label); ?></label></th>
                    <td><input type="text" name="aa_brand_<?php echo $key; ?>" id="aa_brand_<?php echo $key; ?>" value="<?php echo esc_attr($settings[$key] ?? ''); ?>" class="regular-text"></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>

        <?php submit_button('Save Settings'); ?>
    </form>
</div>
