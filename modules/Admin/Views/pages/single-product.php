<?php
if (!defined('ABSPATH')) exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aa_sp_nonce']) && wp_verify_nonce($_POST['aa_sp_nonce'], 'aa_sp_settings')) {
    update_option('aa_sp_help_enable', isset($_POST['aa_sp_help_enable']) ? 'yes' : 'no');
    update_option('aa_sp_help_title', sanitize_text_field($_POST['aa_sp_help_title']));
    update_option('aa_sp_help_text', sanitize_textarea_field($_POST['aa_sp_help_text']));
    update_option('aa_sp_help_wa', sanitize_text_field($_POST['aa_sp_help_wa']));
    update_option('aa_sp_help_fb', esc_url_raw($_POST['aa_sp_help_fb']));
    
    echo '<div class="notice notice-success"><p>Settings saved successfully.</p></div>';
}

$enable = get_option('aa_sp_help_enable', 'yes');
$title = get_option('aa_sp_help_title', 'Need Help Ordering?');
$text = get_option('aa_sp_help_text', 'If you need any assistance with your order, feel free to contact us via WhatsApp or Facebook.');
$wa = get_option('aa_sp_help_wa', '+8801724212748');
$fb = get_option('aa_sp_help_fb', 'https://facebook.com/aljayyidbd');
?>
<div class="aa-admin-page">
    <div class="aa-admin-header">
        <h1><?php _e('Single Product Settings', 'al-aosaf'); ?></h1>
    </div>
    
    <div class="aa-admin-content">
        <form method="POST" action="">
            <?php wp_nonce_field('aa_sp_settings', 'aa_sp_nonce'); ?>
            
            <div class="aa-settings-section">
                <h3><?php _e('Help & Contact Section', 'al-aosaf'); ?></h3>
                <p class="description"><?php _e('This section appears below the Add to Cart and Buy Now buttons to encourage customers to reach out.', 'al-aosaf'); ?></p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Enable Help Section</th>
                        <td>
                            <label>
                                <input type="checkbox" name="aa_sp_help_enable" value="yes" <?php checked($enable, 'yes'); ?>>
                                Show the help section on single product page
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Title</th>
                        <td>
                            <input type="text" name="aa_sp_help_title" value="<?php echo esc_attr($title); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Description Text</th>
                        <td>
                            <textarea name="aa_sp_help_text" rows="3" class="large-text"><?php echo esc_textarea($text); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">WhatsApp Number</th>
                        <td>
                            <input type="text" name="aa_sp_help_wa" value="<?php echo esc_attr($wa); ?>" class="regular-text" placeholder="e.g. +8801724212748">
                            <p class="description">Include the country code.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Facebook Link</th>
                        <td>
                            <input type="url" name="aa_sp_help_fb" value="<?php echo esc_attr($fb); ?>" class="regular-text" placeholder="https://facebook.com/yourpage">
                        </td>
                    </tr>
                </table>
            </div>
            
            <p class="submit">
                <button type="submit" class="button button-primary">Save Changes</button>
            </p>
        </form>
    </div>
</div>
