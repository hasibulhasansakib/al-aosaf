<?php
if (!defined('ABSPATH')) exit;

use Alaosaf\Services\BrandService;
$brand = new BrandService();

$phone = $brand->phone();
$email = $brand->email();
$address = $brand->address();

$facebook = $brand->facebook();
$instagram = $brand->instagram();
$twitter = $brand->twitter();
?>

<div class="aa-footer-mobile">
    
    <?php if ($newsletter_enable === 'yes'): ?>
    <div class="aa-footer-newsletter-area">
        <div class="aa-container">
            <div class="aa-newsletter-inner">
                <div class="aa-newsletter-text">
                    <h3><?php echo esc_html($newsletter_title); ?></h3>
                    <p><?php echo esc_html($newsletter_text); ?></p>
                </div>
                <div class="aa-newsletter-form-wrap">
                    <form class="aa-newsletter-form" onsubmit="event.preventDefault();">
                        <input type="email" placeholder="Email address" required>
                        <button type="submit">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="aa-footer-main">
        <div class="aa-container">
            
            <!-- Brand -->
            <div class="aa-footer-mobile-brand">
                <div class="aa-footer-logo">
                    <?php echo $brand->renderLogo('aa-footer-logo-wrapper'); ?>
                </div>
                <p class="aa-footer-desc">Your premium destination for luxury fashion and modern ecommerce.</p>
            </div>

            <!-- Accordion Links -->
            <div class="aa-footer-accordion">
                <!-- Quick Links -->
                <div class="aa-accordion-item">
                    <button class="aa-accordion-header" aria-expanded="false">
                        <span>Quick Links</span>
                        <div class="aa-accordion-icon"></div>
                    </button>
                    <div class="aa-accordion-content">
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'aa_footer_quick_links',
                            'container' => false,
                            'menu_class' => 'aa-footer-menu',
                            'fallback_cb' => function() { echo '<ul class="aa-footer-menu"><li><a href="#">Setup Menu</a></li></ul>'; }
                        ]);
                        ?>
                    </div>
                </div>

                <!-- Customer Service -->
                <div class="aa-accordion-item">
                    <button class="aa-accordion-header" aria-expanded="false">
                        <span>Customer Service</span>
                        <div class="aa-accordion-icon"></div>
                    </button>
                    <div class="aa-accordion-content">
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'aa_footer_customer_service',
                            'container' => false,
                            'menu_class' => 'aa-footer-menu',
                            'fallback_cb' => function() { echo '<ul class="aa-footer-menu"><li><a href="#">Setup Menu</a></li></ul>'; }
                        ]);
                        ?>
                    </div>
                </div>
                
                <!-- Contact Us -->
                <div class="aa-accordion-item">
                    <button class="aa-accordion-header" aria-expanded="false">
                        <span>Contact Us</span>
                        <div class="aa-accordion-icon"></div>
                    </button>
                    <div class="aa-accordion-content">
                        <ul class="aa-footer-contact-list">
                            <?php if ($address): ?>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    <span><?php echo esc_html($address); ?></span>
                                </li>
                            <?php endif; ?>
                            <?php if ($phone): ?>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                                </li>
                            <?php endif; ?>
                            <?php if ($email): ?>
                                <li>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                    <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Trust & Payment -->
            <div class="aa-footer-mobile-trust">
                <?php if ($payment_icons === 'yes'): ?>
                    <div class="aa-payment-icons">
                        <img src="<?php echo AA_PLUGIN_URL; ?>modules/Appearance/assets/images/visa.svg" alt="Visa" onerror="this.style.display='none'">
                        <img src="<?php echo AA_PLUGIN_URL; ?>modules/Appearance/assets/images/mastercard.svg" alt="Mastercard" onerror="this.style.display='none'">
                        <img src="<?php echo AA_PLUGIN_URL; ?>modules/Appearance/assets/images/paypal.svg" alt="PayPal" onerror="this.style.display='none'">
                        <img src="<?php echo AA_PLUGIN_URL; ?>modules/Appearance/assets/images/amex.svg" alt="Amex" onerror="this.style.display='none'">
                    </div>
                <?php endif; ?>
                
                <div class="aa-footer-social">
                    <?php if ($facebook && $facebook !== '#'): ?>
                        <a href="<?php echo esc_url($facebook); ?>" target="_blank" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></a>
                    <?php endif; ?>
                    <?php if ($instagram && $instagram !== '#'): ?>
                        <a href="<?php echo esc_url($instagram); ?>" target="_blank" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg></a>
                    <?php endif; ?>
                    <?php if ($twitter && $twitter !== '#'): ?>
                        <a href="<?php echo esc_url($twitter); ?>" target="_blank" aria-label="Twitter"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg></a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <div class="aa-footer-bottom">
        <div class="aa-container">
            <div class="aa-copyright">
                <?php echo $copyright; ?>
            </div>
        </div>
    </div>
</div>

<!-- Mobile App Bottom Bar (Floating Pill) -->
<div class="aa-mobile-bottom-bar">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="aa-bottom-bar-item active">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
        <span>Home</span>
    </a>
    <a href="<?php echo esc_url(function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : '#'); ?>" class="aa-bottom-bar-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
        <span>Shop</span>
    </a>
    <a href="#" class="aa-bottom-bar-item aa-sheet-trigger" data-target="aa-wishlist-sheet">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
        <span>Wishlist</span>
    </a>
    <a href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#'); ?>" class="aa-bottom-bar-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
        <span>Cart</span>
    </a>
    <a href="<?php echo esc_url(function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('myaccount')) : '#'); ?>" class="aa-bottom-bar-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        <span>Account</span>
    </a>
</div>

<!-- Bottom Sheet Overlay -->
<div class="aa-bottom-sheet-overlay"></div>

<!-- Wishlist Bottom Sheet -->
<div class="aa-bottom-sheet" id="aa-wishlist-sheet">
    <div class="aa-bottom-sheet-header">
        <h3>Your Wishlist</h3>
        <button class="aa-bottom-sheet-close" aria-label="Close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <div class="aa-bottom-sheet-content">
        <?php 
        $wishlist_items = class_exists('\Alaosaf\Modules\Wishlist\WishlistModule') ? \Alaosaf\Modules\Wishlist\WishlistModule::getWishlist() : [];
        if (!empty($wishlist_items)): 
        ?>
            <div class="aa-mini-wishlist-items" style="max-height: 60vh; overflow-y: auto; padding-right: 5px;">
                <?php foreach ($wishlist_items as $product_id): 
                    $product = wc_get_product($product_id);
                    if (!$product) continue;
                ?>
                    <div class="aa-mini-wishlist-item" data-product-id="<?php echo esc_attr($product_id); ?>" style="display: flex; gap: 12px; margin-bottom: 12px; align-items: center; border-bottom: 1px solid #f2f2f2; padding-bottom: 12px;">
                        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="aa-mw-img" style="width: 55px; border-radius: 6px; overflow: hidden; flex-shrink: 0; border: 1px solid #eaeaea;">
                            <?php echo $product->get_image('thumbnail', ['style' => 'width: 100%; height: auto; display: block;']); ?>
                        </a>
                        <div class="aa-mw-content" style="flex-grow: 1; min-width: 0;">
                            <a href="<?php echo esc_url($product->get_permalink()); ?>" class="aa-mw-title" style="font-weight: 500; font-size: 13px; color: #333; text-decoration: none; margin-bottom: 4px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                                <?php echo wp_kses_post($product->get_name()); ?>
                            </a>
                            <div class="aa-mw-price" style="color: var(--aa-primary, #C8A15A); font-weight: 700; font-size: 13px;">
                                <?php echo wp_kses_post($product->get_price_html()); ?>
                            </div>
                        </div>
                        <button class="aa-mw-remove aa-wishlist-btn aa-in-wishlist" data-product-id="<?php echo esc_attr($product_id); ?>" title="Remove from wishlist" style="background: #fff0f0; border: none; cursor: pointer; color: #ff4d4d; padding: 6px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; flex-shrink: 0; margin-left: 5px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="margin-top: 20px; text-align: center;">
                <a href="<?php echo esc_url(function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : '#'); ?>" class="aa-sheet-btn" style="width: 100%; box-sizing: border-box;">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="aa-empty-state">
                <div class="aa-empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                </div>
                <h4>Your Wishlist is Empty</h4>
                <p style="text-align: center; color: #666; margin-bottom: 20px; font-size: 14px;">Start exploring and add your favorite products here!</p>
                <a href="<?php echo esc_url(function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : '#'); ?>" class="aa-sheet-btn">Browse Shop</a>
            </div>
        <?php endif; ?>
    </div>
</div>
