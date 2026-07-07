<?php
if (!defined('ABSPATH')) exit;
use Alaosaf\Helpers\Brand;
?>
<div class="aa-header-mobile aa-hide-on-desktop" style="background: var(--aa-primary, #C8A15A); color: #ffffff; position: relative;">
    <div class="aa-container">
        <div class="aa-mobile-inner" style="display: flex; justify-content: space-between; align-items: center; height: 70px;">
            <!-- Hamburger -->
            <button class="aa-mobile-toggle" aria-label="Menu" style="background: none; border: none; padding: 0; color: #ffffff; display: flex; align-items: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </button>

            <!-- Logo -->
            <div class="aa-header-logo" style="flex-grow: 1; text-align: center; padding: 0 15px;">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="aa-logo-link" style="text-decoration: none; display: inline-block;">
                    <?php echo Brand::renderLogo('aa-mobile-logo'); ?>
                </a>
            </div>

            <!-- Icons (Search + Cart + Account) -->
            <div class="aa-mobile-icons" style="display: flex; align-items: center; gap: 15px;">
                <!-- Search Toggle -->
                <button class="aa-mobile-search-toggle" aria-label="Search" style="background: none; border: none; padding: 0; color: #ffffff; display: flex; align-items: center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </button>

                <!-- Cart -->
                <a href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#'); ?>" class="aa-action-icon aa-cart-drawer-trigger" aria-label="Cart" style="position: relative; display: flex; align-items: center; color: #ffffff;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    <span class="aa-cart-badge" style="position: absolute; top: -6px; right: -8px; background: #ffffff; color: var(--aa-primary, #C8A15A); font-size: 10px; font-weight: 700; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: 2px solid var(--aa-primary, #C8A15A); box-sizing: content-box;">
                        <?php echo esc_html(function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0); ?>
                    </span>
                </a>

                <!-- Account -->
                <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="aa-action-icon" aria-label="My Account" style="display: flex; align-items: center; color: #ffffff;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Search Expandable Overlay -->
    <div class="aa-mobile-search-overlay">
        <form action="<?php echo esc_url(home_url('/')); ?>" method="get" class="aa-search-form aa-mobile-search-form">
            <input type="text" name="s" placeholder="Search for products..." class="aa-search-input">
            <input type="hidden" name="post_type" value="product">
            <button type="submit" class="aa-search-btn" aria-label="Search">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
            <button type="button" class="aa-mobile-search-close" aria-label="Close">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <!-- Live Search Suggestions Container -->
            <div class="aa-search-suggestions"></div>
        </form>
    </div>

    <!-- Off-Canvas Menu -->
    <div class="aa-offcanvas-menu">
        <div class="aa-offcanvas-overlay"></div>
        <div class="aa-offcanvas-content">
            <div class="aa-offcanvas-header" style="justify-content: center;">
                <?php echo Brand::renderLogo('aa-offcanvas-logo'); ?>
            </div>
            <div class="aa-offcanvas-body">
                <nav class="aa-mobile-nav">
                    <?php
                    if (has_nav_menu('aa_mobile_menu')) {
                        wp_nav_menu([
                            'theme_location' => 'aa_mobile_menu',
                            'container' => false,
                            'menu_class' => 'aa-mobile-menu-list',
                            'fallback_cb' => false
                        ]);
                    } else {
                        echo '<p class="aa-menu-fallback" style="padding: 20px; margin: 0;">Assign a menu to "Al Aosaf Mobile Menu"</p>';
                    }
                    ?>
                </nav>
            </div>
            <div class="aa-offcanvas-footer-premium">
                <div class="aa-mobile-contact">
                    <?php if (Brand::phone()): ?>
                    <a href="tel:<?php echo esc_attr(str_replace(' ', '', Brand::phone())); ?>" class="aa-contact-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        <span><?php echo esc_html(Brand::phone()); ?></span>
                    </a>
                    <?php endif; ?>
                    <?php if (Brand::email()): ?>
                    <a href="mailto:<?php echo esc_attr(Brand::email()); ?>" class="aa-contact-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        <span><?php echo esc_html(Brand::email()); ?></span>
                    </a>
                    <?php endif; ?>
                </div>
                <div class="aa-mobile-socials">
                    <?php if (Brand::facebook() && Brand::facebook() !== '#'): ?>
                    <a href="<?php echo esc_url(Brand::facebook()); ?>" aria-label="Facebook" target="_blank"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3.81l.39-4h-4.2V7a1 1 0 0 1 1-1h3z"></path></svg></a>
                    <?php endif; ?>
                    <?php if (Brand::instagram() && Brand::instagram() !== '#'): ?>
                    <a href="<?php echo esc_url(Brand::instagram()); ?>" aria-label="Instagram" target="_blank"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg></a>
                    <?php endif; ?>
                    <?php if (Brand::twitter() && Brand::twitter() !== '#'): ?>
                    <a href="<?php echo esc_url(Brand::twitter()); ?>" aria-label="Twitter" target="_blank"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg></a>
                    <?php endif; ?>
                </div>
                <div class="aa-offcanvas-close-wrapper">
                    <button class="aa-offcanvas-close-bottom-icon" aria-label="Close">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
