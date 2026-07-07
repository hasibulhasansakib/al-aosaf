<?php
/**
 * Plugin Name: Al Aosaf
 * Description: The core business system.
 * Version: 1.1.2
 * Author: Hasibul Hasan Sakib
 * Text Domain: al-aosaf
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Immutable Identifiers Constants
define('AA_VERSION', '1.1.2');
define('AA_PLUGIN_FILE', __FILE__);
define('AA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AA_PLUGIN_URL', plugin_dir_url(__FILE__));

// Require Composer Autoloader
if (file_exists(AA_PLUGIN_DIR . 'vendor/autoload.php')) {
    require_once AA_PLUGIN_DIR . 'vendor/autoload.php';
}

// Bootstrap the framework
add_action('plugins_loaded', function () {
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
