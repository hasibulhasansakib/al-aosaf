<?php
if (!defined('ABSPATH')) exit;
?>
<div class="aa-page-content" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
    <h2 style="margin-top:0;">Header System</h2>
    
    <?php if (isset($_GET['updated']) && $_GET['updated'] === 'true'): ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Header settings saved successfully.', 'al-aosaf'); ?></p></div>
    <?php endif; ?>

    <h2 class="nav-tab-wrapper" style="margin-bottom: 20px;">
        <a href="?page=aa-header&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">General</a>
        <a href="?page=aa-header&tab=announcement" class="nav-tab <?php echo $active_tab === 'announcement' ? 'nav-tab-active' : ''; ?>">Announcement Bar</a>
        <a href="?page=aa-header&tab=desktop" class="nav-tab <?php echo $active_tab === 'desktop' ? 'nav-tab-active' : ''; ?>">Desktop</a>
        <a href="?page=aa-header&tab=mobile" class="nav-tab <?php echo $active_tab === 'mobile' ? 'nav-tab-active' : ''; ?>">Mobile</a>
        <a href="?page=aa-header&tab=sticky" class="nav-tab <?php echo $active_tab === 'sticky' ? 'nav-tab-active' : ''; ?>">Sticky</a>
    </h2>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="aa_save_header_settings">
        <input type="hidden" name="current_tab" value="<?php echo esc_attr($active_tab); ?>">
        <?php wp_nonce_field('aa_save_header_settings', 'aa_header_nonce'); ?>

        <table class="form-table">
            <?php if ($active_tab === 'general'): ?>
                <tr>
                    <th scope="row"><label for="aa_header_general_enable">Enable Header</label></th>
                    <td>
                        <select name="aa_header_general_enable" id="aa_header_general_enable">
                            <option value="yes" <?php selected($settings['general_enable'] ?? 'yes', 'yes'); ?>>Yes</option>
                            <option value="no" <?php selected($settings['general_enable'] ?? 'yes', 'no'); ?>>No</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="aa_header_general_transparent">Transparent Header</label></th>
                    <td>
                        <select name="aa_header_general_transparent" id="aa_header_general_transparent">
                            <option value="no" <?php selected($settings['general_transparent'] ?? 'no', 'no'); ?>>No</option>
                            <option value="yes" <?php selected($settings['general_transparent'] ?? 'no', 'yes'); ?>>Yes</option>
                        </select>
                        <p class="description">Makes the header background transparent and overlaps content. Ideal for hero images.</p>
                    </td>
                </tr>

            <?php elseif ($active_tab === 'announcement'): ?>
                <tr>
                    <th scope="row"><label for="aa_header_announcement_enable">Enable Announcement Bar</label></th>
                    <td>
                        <select name="aa_header_announcement_enable" id="aa_header_announcement_enable">
                            <option value="no" <?php selected($settings['announcement_enable'] ?? 'no', 'no'); ?>>No</option>
                            <option value="yes" <?php selected($settings['announcement_enable'] ?? 'no', 'yes'); ?>>Yes</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="aa_header_announcement_text">Announcement Text</label></th>
                    <td>
                        <input type="text" name="aa_header_announcement_text" id="aa_header_announcement_text" value="<?php echo esc_attr($settings['announcement_text'] ?? 'Free shipping on orders over $200'); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="aa_header_announcement_link">Announcement Link</label></th>
                    <td>
                        <input type="url" name="aa_header_announcement_link" id="aa_header_announcement_link" value="<?php echo esc_attr($settings['announcement_link'] ?? ''); ?>" class="regular-text" placeholder="https://...">
                    </td>
                </tr>

            <?php elseif ($active_tab === 'desktop'): ?>

                <tr>
                    <th scope="row"><label for="aa_header_desktop_search">Show Search Icon</label></th>
                    <td>
                        <select name="aa_header_desktop_search" id="aa_header_desktop_search">
                            <option value="yes" <?php selected($settings['desktop_search'] ?? 'yes', 'yes'); ?>>Yes</option>
                            <option value="no" <?php selected($settings['desktop_search'] ?? 'yes', 'no'); ?>>No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="aa_header_desktop_account">Show Account Icon</label></th>
                    <td>
                        <select name="aa_header_desktop_account" id="aa_header_desktop_account">
                            <option value="yes" <?php selected($settings['desktop_account'] ?? 'yes', 'yes'); ?>>Yes</option>
                            <option value="no" <?php selected($settings['desktop_account'] ?? 'yes', 'no'); ?>>No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="aa_header_desktop_cart">Show Cart Icon</label></th>
                    <td>
                        <select name="aa_header_desktop_cart" id="aa_header_desktop_cart">
                            <option value="yes" <?php selected($settings['desktop_cart'] ?? 'yes', 'yes'); ?>>Yes</option>
                            <option value="no" <?php selected($settings['desktop_cart'] ?? 'yes', 'no'); ?>>No</option>
                        </select>
                    </td>
                </tr>

            <?php elseif ($active_tab === 'mobile'): ?>

                <tr>
                    <th scope="row"><label for="aa_header_mobile_cart">Show Cart Icon</label></th>
                    <td>
                        <select name="aa_header_mobile_cart" id="aa_header_mobile_cart">
                            <option value="yes" <?php selected($settings['mobile_cart'] ?? 'yes', 'yes'); ?>>Yes</option>
                            <option value="no" <?php selected($settings['mobile_cart'] ?? 'yes', 'no'); ?>>No</option>
                        </select>
                    </td>
                </tr>

            <?php elseif ($active_tab === 'sticky'): ?>
                <tr>
                    <th scope="row"><label for="aa_header_sticky_enable">Enable Sticky Header</label></th>
                    <td>
                        <select name="aa_header_sticky_enable" id="aa_header_sticky_enable">
                            <option value="yes" <?php selected($settings['sticky_enable'] ?? 'yes', 'yes'); ?>>Yes</option>
                            <option value="no" <?php selected($settings['sticky_enable'] ?? 'yes', 'no'); ?>>No</option>
                        </select>
                    </td>
                </tr>

            <?php endif; ?>
        </table>
        
        <?php submit_button('Save Header Settings'); ?>
    </form>
</div>
