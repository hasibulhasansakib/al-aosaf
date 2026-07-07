<?php
if (!defined('ABSPATH')) exit;

$preset = $this->settings['general_preset'] ?? 'luxury_fashion';
$is_transparent = ($this->settings['general_transparent'] ?? 'no') === 'yes';

$classes = ['aa-header-wrapper', 'aa-preset-' . esc_attr($preset)];
if ($is_transparent) {
    $classes[] = 'aa-header-transparent';
}
?>
<header class="<?php echo implode(' ', $classes); ?>">
    <?php
    // Announcement Bar
    if (($this->settings['announcement_enable'] ?? 'no') === 'yes') {
        include __DIR__ . '/announcement-bar.php';
    }

    // Main Desktop
    include __DIR__ . '/desktop.php';

    // Mobile
    include __DIR__ . '/mobile.php';

    // Sticky
    if (($this->settings['sticky_enable'] ?? 'yes') === 'yes') {
        include __DIR__ . '/sticky.php';
    }

    // Cart Drawer
    include __DIR__ . '/cart-drawer.php';

    // Sidebar Drawer
    include __DIR__ . '/sidebar-drawer.php';
    ?>
</header>
