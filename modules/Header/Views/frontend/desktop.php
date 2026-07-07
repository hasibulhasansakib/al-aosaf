<?php
if (!defined('ABSPATH')) exit;
use Alaosaf\Helpers\Brand;
?>
<div class="aa-header-desktop aa-hide-on-mobile" style="background: var(--aa-background); border-bottom: none;">
    
    <!-- Top Row: Logo, Search, Actions -->
    <div class="aa-header-top-row" style="padding: 20px 0; background: #ffffff;">
        <div class="aa-container aa-top-row-inner">
            
            <!-- Logo -->
            <div class="aa-header-logo" style="flex-shrink: 0;">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="aa-logo-link" style="text-decoration: none;">
                    <?php echo Brand::renderLogo('aa-desktop-logo'); ?>
                </a>
            </div>

            <!-- Extended Search Bar -->
            <div class="aa-header-search-bar" style="flex-grow: 1; max-width: 600px;">
                <form action="<?php echo esc_url(home_url('/')); ?>" method="get" class="aa-search-form" style="position: relative; display: flex; align-items: center;">
                    <input type="text" name="s" placeholder="Search in..." class="aa-search-input" style="width: 100%; padding: 12px 20px; padding-right: 50px; border-radius: 8px; border: 1px solid #eaeaea; background: #f9f9f9; font-size: 14px; outline: none; transition: border-color 0.2s;">
                    <input type="hidden" name="post_type" value="product">
                    <button type="submit" class="aa-search-btn" aria-label="Search" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: #555; display: flex; align-items: center; justify-content: center; height: 100%; padding: 0 10px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </button>
                    <!-- Live Search Suggestions Container -->
                    <div class="aa-search-suggestions"></div>
                </form>
            </div>

            <!-- Actions (Icons + Labels) -->
            <div class="aa-header-actions">
                
                <!-- My Account / Sign In -->
                <a href="<?php echo esc_url(function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('myaccount')) : '#'); ?>" class="aa-action-item">
                    <div class="aa-action-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <span class="aa-action-label">
                        <?php echo is_user_logged_in() ? 'Account' : 'Sign In'; ?>
                    </span>
                </a>

                <!-- Wishlist -->
                <div class="aa-wishlist-wrapper">
                    <a href="#" class="aa-action-item">
                        <div class="aa-action-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                            <?php 
                            $wishlist_count = class_exists('\Alaosaf\Modules\Wishlist\WishlistModule') ? count(\Alaosaf\Modules\Wishlist\WishlistModule::getWishlist()) : 0;
                            ?>
                            <span class="aa-wishlist-badge aa-cart-badge" <?php echo $wishlist_count === 0 ? 'style="display:none;"' : ''; ?>><?php echo esc_html($wishlist_count); ?></span>
                        </div>
                        <span class="aa-action-label">Wishlist</span>
                    </a>
                    
                    <!-- Wishlist Dropdown -->
                    <div class="aa-wishlist-dropdown">
                        <?php 
                        $wishlist_path = AA_PLUGIN_DIR . 'modules/Wishlist/Views/mini-wishlist.php';
                        if (file_exists($wishlist_path)) {
                            include $wishlist_path;
                        }
                        ?>
                    </div>
                </div>

                <!-- Cart -->
                <a href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#'); ?>" class="aa-action-item aa-cart-drawer-trigger">
                    <div class="aa-action-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                        <span class="aa-cart-badge"><?php echo esc_html(function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0); ?></span>
                    </div>
                    <span class="aa-action-label">Cart</span>
                </a>

                <!-- More (Hamburger) -->
                <a href="#" class="aa-action-item aa-desktop-more-trigger">
                    <div class="aa-action-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </div>
                    <span class="aa-action-label">More</span>
                </a>
                
            </div>
        </div>
    </div>

    <!-- Bottom Row: Navigation Menu -->
    <div class="aa-header-bottom-row" style="background: #011d1a; color: #fff;">
        <div class="aa-container">
            <nav class="aa-header-nav">
                <?php
                if (has_nav_menu('aa_primary_menu')) {
                    wp_nav_menu([
                        'theme_location' => 'aa_primary_menu',
                        'container' => false,
                        'menu_class' => 'aa-desktop-menu aa-menu-dark',
                        'fallback_cb' => false
                    ]);
                } else {
                    echo '<p class="aa-menu-fallback" style="margin:0; padding: 15px 0; font-size: 14px; color: #a0a0a0;">Assign a menu to "Al Aosaf Primary Menu"</p>';
                }
                ?>
            </nav>
        </div>
    </div>
</div>
