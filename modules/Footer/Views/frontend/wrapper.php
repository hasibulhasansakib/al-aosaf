<?php
if (!defined('ABSPATH')) exit;

$settings = get_option('aa_footer_settings', []);
$general_enable = $settings['general_enable'] ?? 'yes';

if ($general_enable !== 'yes') {
    return;
}

$newsletter_enable = $settings['newsletter_enable'] ?? 'yes';
$newsletter_title = $settings['newsletter_title'] ?? 'Join Our Newsletter';
$newsletter_text = $settings['newsletter_text'] ?? 'Subscribe to get special offers, free giveaways, and once-in-a-lifetime deals.';

$back_to_top = $settings['content_back_to_top'] ?? 'yes';
$payment_icons = $settings['content_payment_icons'] ?? 'yes';

$copyright = $settings['copyright_text'] ?? '&copy; {year} Al Aosaf. All Rights Reserved.';
$copyright = str_replace('{year}', date('Y'), $copyright);
?>

<footer class="aa-footer-wrapper">
    <div class="aa-hide-on-mobile">
        <?php include __DIR__ . '/desktop.php'; ?>
    </div>
    <div class="aa-hide-on-desktop">
        <?php include __DIR__ . '/mobile.php'; ?>
    </div>
    
    <?php if ($back_to_top === 'yes'): ?>
        <button id="aa-back-to-top" class="aa-back-to-top" aria-label="Back to top">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
        </button>
    <?php endif; ?>
</footer>
