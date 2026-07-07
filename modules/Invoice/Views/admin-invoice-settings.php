<?php
if (!defined('ABSPATH')) exit;

if (!current_user_can('manage_woocommerce')) {
    wp_die('Unauthorized');
}

// Show updated message
if (isset($_GET['settings-updated'])) {
    add_settings_error('aa_invoice_messages', 'aa_invoice_message', 'Settings Saved', 'updated');
}
settings_errors('aa_invoice_messages');
?>
<style>
    .aa-settings-card {
        background: #fff;
        max-width: 800px;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        margin-top: 20px;
        border: 1px solid #f0f0f0;
    }
    .aa-settings-card h2 {
        border-bottom: 2px solid #C8A15A;
        padding-bottom: 10px;
        margin-bottom: 25px;
        font-weight: 600;
        color: #1e1e1e;
    }
    .aa-settings-card .form-table th {
        font-weight: 600;
        color: #333;
    }
    .aa-settings-card input[type="text"], .aa-settings-card textarea {
        width: 100%;
        max-width: 500px;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 8px 12px;
        transition: border-color 0.3s;
    }
    .aa-settings-card input[type="text"]:focus, .aa-settings-card textarea:focus {
        border-color: #C8A15A;
        box-shadow: 0 0 0 1px #C8A15A;
    }
    .aa-settings-card .button-primary {
        background: #C8A15A;
        border-color: #C8A15A;
        box-shadow: none;
        border-radius: 6px;
        padding: 0 24px;
        height: 40px;
        font-weight: 600;
    }
    .aa-settings-card .button-primary:hover {
        background: #b68e4c;
        border-color: #b68e4c;
    }
</style>
<div class="wrap">
    <h1>Al Aosaf Invoice Settings</h1>
    
    <div class="aa-settings-card">
        <form method="post" action="options.php">
            <?php
            settings_fields('aa_invoice_settings_group');
            do_settings_sections('aa-invoice-settings');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
</div>
