<?php
if (!defined('ABSPATH')) exit;
?>
<div class="aa-page-content" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
    <h2 style="margin-top:0;">Footer System</h2>
    
    <?php if (isset($_GET['updated']) && $_GET['updated'] === 'true'): ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Footer settings saved successfully.', 'al-aosaf'); ?></p></div>
    <?php endif; ?>

    <h2 class="nav-tab-wrapper" style="margin-bottom: 20px;">
        <a href="?page=aa-footer&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">General</a>
        <a href="?page=aa-footer&tab=newsletter" class="nav-tab <?php echo $active_tab === 'newsletter' ? 'nav-tab-active' : ''; ?>">Newsletter</a>
        <a href="?page=aa-footer&tab=content" class="nav-tab <?php echo $active_tab === 'content' ? 'nav-tab-active' : ''; ?>">Content</a>
        <a href="?page=aa-footer&tab=copyright" class="nav-tab <?php echo $active_tab === 'copyright' ? 'nav-tab-active' : ''; ?>">Copyright</a>
        <a href="?page=aa-footer&tab=social" class="nav-tab <?php echo $active_tab === 'social' ? 'nav-tab-active' : ''; ?>">Social</a>
        <a href="?page=aa-footer&tab=styles" class="nav-tab <?php echo $active_tab === 'styles' ? 'nav-tab-active' : ''; ?>">Styles</a>
    </h2>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="aa_save_footer_settings">
        <input type="hidden" name="current_tab" value="<?php echo esc_attr($active_tab); ?>">
        <?php wp_nonce_field('aa_save_footer_settings', 'aa_footer_nonce'); ?>

        <table class="form-table">
            <?php if ($active_tab === 'general'): ?>
                <tr>
                    <th scope="row"><label for="aa_footer_general_enable">Enable Footer</label></th>
                    <td>
                        <select name="aa_footer_general_enable" id="aa_footer_general_enable">
                            <option value="yes" <?php selected($settings['general_enable'] ?? 'yes', 'yes'); ?>>Yes</option>
                            <option value="no" <?php selected($settings['general_enable'] ?? 'yes', 'no'); ?>>No</option>
                        </select>
                    </td>
                </tr>

            <?php elseif ($active_tab === 'newsletter'): ?>
                <tr>
                    <th scope="row"><label for="aa_footer_newsletter_enable">Enable Newsletter Section</label></th>
                    <td>
                        <select name="aa_footer_newsletter_enable" id="aa_footer_newsletter_enable">
                            <option value="yes" <?php selected($settings['newsletter_enable'] ?? 'yes', 'yes'); ?>>Yes</option>
                            <option value="no" <?php selected($settings['newsletter_enable'] ?? 'yes', 'no'); ?>>No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="aa_footer_newsletter_title">Newsletter Title</label></th>
                    <td>
                        <input type="text" name="aa_footer_newsletter_title" id="aa_footer_newsletter_title" value="<?php echo esc_attr($settings['newsletter_title'] ?? 'Join Our Newsletter'); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="aa_footer_newsletter_text">Newsletter Description</label></th>
                    <td>
                        <textarea name="aa_footer_newsletter_text" id="aa_footer_newsletter_text" class="large-text" rows="3"><?php echo esc_textarea($settings['newsletter_text'] ?? 'Subscribe to get special offers, free giveaways, and once-in-a-lifetime deals.'); ?></textarea>
                    </td>
                </tr>

            <?php elseif ($active_tab === 'content'): ?>
                <tr>
                    <th scope="row"><label for="aa_footer_content_back_to_top">Show "Back to Top" Button</label></th>
                    <td>
                        <select name="aa_footer_content_back_to_top" id="aa_footer_content_back_to_top">
                            <option value="yes" <?php selected($settings['content_back_to_top'] ?? 'yes', 'yes'); ?>>Yes</option>
                            <option value="no" <?php selected($settings['content_back_to_top'] ?? 'yes', 'no'); ?>>No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="aa_footer_content_payment_icons">Show Payment Icons</label></th>
                    <td>
                        <select name="aa_footer_content_payment_icons" id="aa_footer_content_payment_icons">
                            <option value="yes" <?php selected($settings['content_payment_icons'] ?? 'yes', 'yes'); ?>>Yes</option>
                            <option value="no" <?php selected($settings['content_payment_icons'] ?? 'yes', 'no'); ?>>No</option>
                        </select>
                        <p class="description">Displays a row of standard payment method icons (Visa, Mastercard, etc.) in the footer bottom.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Trust Badges</th>
                    <td>
                        <p class="description">Trust Badges and Contact Information are automatically populated from the <strong>Appearance -> Brand</strong> settings.</p>
                    </td>
                </tr>

            <?php elseif ($active_tab === 'copyright'): ?>
                <tr>
                    <th scope="row"><label for="aa_footer_copyright_text">Copyright Text</label></th>
                    <td>
                        <input type="text" name="aa_footer_copyright_text" id="aa_footer_copyright_text" value="<?php echo esc_attr($settings['copyright_text'] ?? '&copy; {year} Al Aosaf. All Rights Reserved.'); ?>" class="regular-text">
                        <p class="description">Use <code>{year}</code> to automatically output the current year.</p>
                    </td>
                </tr>

            <?php elseif ($active_tab === 'social' || $active_tab === 'styles'): ?>
                <tr>
                    <td colspan="2">
                        <p class="description"><strong>Social Media Links</strong> and <strong>Styles/Colors</strong> are globally managed from the <strong>Appearance -> Brand</strong> module to ensure absolute consistency across the entire theme.</p>
                    </td>
                </tr>
            <?php endif; ?>
        </table>
        
        <?php submit_button('Save Footer Settings'); ?>
    </form>
</div>
