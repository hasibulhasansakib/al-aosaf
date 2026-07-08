<?php
/**
 * Plugin Name: Al Aosaf
 * Description: The core business system.
 * Version: 1.2.2
 * Author: Hasibul Hasan Sakib
 * Text Domain: al-aosaf
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Immutable Identifiers Constants
define('AA_VERSION', '1.2.2');
define('AA_PLUGIN_FILE', __FILE__);
define('AA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AA_PLUGIN_URL', plugin_dir_url(__FILE__));

// Require Composer Autoloader
if (file_exists(AA_PLUGIN_DIR . 'vendor/autoload.php')) {
    require_once AA_PLUGIN_DIR . 'vendor/autoload.php';
}

// Security: Prevent WooCommerce Deactivation
add_filter('plugin_action_links_woocommerce/woocommerce.php', function($actions) {
    if (isset($actions['deactivate'])) {
        $actions['deactivate'] = '<span style="color:#a0a0a0; font-weight:bold;" title="Locked by Al Aosaf Framework">Locked</span>';
    }
    return $actions;
}, 999);

// Bootstrap the framework
add_action('plugins_loaded', function () {
    // Fail-Safe: Show notice if WooCommerce is missing
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p><strong>Al Aosaf Framework:</strong> WooCommerce is missing or deactivated. Shop features have been hidden to prevent site crashes, but the core design remains active.</p></div>';
        });
    }

    if (class_exists(\Alaosaf\Core\Framework::class)) {
        \Alaosaf\Core\Framework::getInstance()->init();
    }
});

// Initialize Plugin Update Checker for GitHub auto-updates
if (file_exists(AA_PLUGIN_DIR . 'vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php')) {
    require_once AA_PLUGIN_DIR . 'vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';

    $myUpdateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        'https://github.com/hasibulhasansakib/al-aosaf/',
        AA_PLUGIN_FILE,
        'al-aosaf'
    );

    // Set the branch that contains the stable release.
    $myUpdateChecker->setBranch('main');
}
