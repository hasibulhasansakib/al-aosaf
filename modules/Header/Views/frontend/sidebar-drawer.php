<?php
if (!defined('ABSPATH')) exit;
use Alaosaf\Helpers\Brand;
?>
<style>
/* Sidebar Menu Premium UI - Extremely Robust */
.aa-sidebar-nav ul,
.aa-sidebar-nav li {
    list-style: none !important;
    list-style-type: none !important;
    margin: 0 !important;
    padding: 0 !important;
    background: none !important;
}
.aa-sidebar-nav li {
    border-bottom: 1px solid #f2f2f2 !important;
}
.aa-sidebar-nav li:last-child {
    border-bottom: none !important;
}
.aa-sidebar-nav li::before,
.aa-sidebar-nav li::after,
.aa-sidebar-nav a::before {
    display: none !important;
    content: none !important;
    background: none !important;
}
.aa-sidebar-nav a {
    position: relative !important;
    display: block !important;
    padding: 16px 25px !important;
    color: #333333 !important;
    font-size: 16px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
    background-color: transparent !important;
    text-align: left !important;
    width: 100% !important;
    box-sizing: border-box !important;
}
.aa-sidebar-nav a:hover {
    color: var(--aa-primary, #C8A15A) !important;
    padding-left: 35px !important;
    background-color: #fafafa !important;
}
.aa-sidebar-nav a::after {
    content: '\2192' !important; /* Right arrow */
    position: absolute !important;
    top: 50% !important;
    right: 25px !important;
    transform: translateY(-50%) translateX(-10px) !important;
    font-size: 18px !important;
    opacity: 0 !important;
    transition: all 0.3s ease !important;
    color: var(--aa-primary, #C8A15A) !important;
}
.aa-sidebar-nav a:hover::after {
    opacity: 1 !important;
    transform: translateY(-50%) translateX(0) !important;
}

/* Bottom Close Button Premium UI */
.aa-sidebar-bottom-close-wrapper {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #eaeaea;
    display: flex;
    justify-content: center;
}

.aa-sidebar-bottom-btn {
    position: static !important; /* Override .aa-sidebar-close absolute positioning */
    display: flex !important;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100% !important;
    height: auto !important;
    padding: 14px 24px !important;
    background: #111111 !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 8px !important;
    font-size: 15px !important;
    font-weight: 600 !important;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.aa-sidebar-bottom-btn:hover {
    background: var(--aa-primary, #C8A15A) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(200, 161, 90, 0.3) !important;
}

.aa-sidebar-bottom-btn svg {
    transition: transform 0.3s ease;
}

.aa-sidebar-bottom-btn:hover svg {
    transform: rotate(90deg);
}
</style>
<div class="aa-sidebar-overlay"></div>
<div class="aa-sidebar-drawer">
    
    <div class="aa-sidebar-header">
        <div class="aa-sidebar-logo">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <?php echo Brand::renderLogo('aa-sidebar-logo-img'); ?>
            </a>
        </div>
        <button class="aa-sidebar-close" aria-label="Close Sidebar">
            &times;
        </button>
    </div>

    <div class="aa-sidebar-body">
        <nav class="aa-sidebar-nav">
            <?php
            if (has_nav_menu('aa_sidebar_menu')) {
                wp_nav_menu([
                    'theme_location' => 'aa_sidebar_menu',
                    'container' => false,
                    'menu_class' => 'aa-sidebar-menu-list',
                    'fallback_cb' => false
                ]);
            } else {
                echo '<div class="aa-sidebar-empty-menu">';
                echo '<p>Please assign a menu to the <strong>Al Aosaf Sidebar Menu</strong> location in WordPress Appearance > Menus.</p>';
                echo '</div>';
            }
            ?>
        </nav>
    </div>

    <div class="aa-sidebar-footer">
        <div class="aa-sidebar-contact">
            <?php if (Brand::phone()): ?>
                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', Brand::phone())); ?>" class="aa-sidebar-contact-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    <?php echo esc_html(Brand::phone()); ?>
                </a>
            <?php endif; ?>
            
            <?php if (Brand::email()): ?>
                <a href="mailto:<?php echo esc_attr(Brand::email()); ?>" class="aa-sidebar-contact-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <?php echo esc_html(Brand::email()); ?>
                </a>
            <?php endif; ?>
        </div>

        <div class="aa-sidebar-social">
            <?php if (Brand::facebook()): ?>
                <a href="<?php echo esc_url(Brand::facebook()); ?>" target="_blank" aria-label="Facebook">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                </a>
            <?php endif; ?>
            <?php if (Brand::instagram()): ?>
                <a href="<?php echo esc_url(Brand::instagram()); ?>" target="_blank" aria-label="Instagram">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                </a>
            <?php endif; ?>
            <?php if (Brand::twitter()): ?>
                <a href="<?php echo esc_url(Brand::twitter()); ?>" target="_blank" aria-label="Twitter">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                </a>
            <?php endif; ?>
            <?php if (Brand::youtube()): ?>
                <a href="<?php echo esc_url(Brand::youtube()); ?>" target="_blank" aria-label="YouTube">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.42a2.78 2.78 0 0 0-1.94 2C1 8.13 1 12 1 12s0 3.87.46 5.58a2.78 2.78 0 0 0 1.94 2C5.12 20 12 20 12 20s6.88 0 8.6-.42a2.78 2.78 0 0 0 1.94-2C23 15.87 23 12 23 12s0-3.87-.46-5.58z"></path><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"></polygon></svg>
                </a>
            <?php endif; ?>
        </div>

        <!-- Premium Bottom Close Button -->
        <div class="aa-sidebar-bottom-close-wrapper">
            <button class="aa-sidebar-close aa-sidebar-bottom-btn" aria-label="Close Sidebar" onclick="document.querySelector('.aa-sidebar-drawer').classList.remove('is-active'); document.querySelector('.aa-sidebar-overlay').classList.remove('is-active'); document.body.style.overflow = '';">
                <span>Close Sidebar</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
    </div>
</div>
