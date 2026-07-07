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

<div class="aa-footer-desktop">
    
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
                        <input type="email" placeholder="Enter your email address" required>
                        <button type="submit">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="aa-footer-main">
        <div class="aa-container">
            <div class="aa-footer-columns">
                
                <!-- Column 1: Brand -->
                <div class="aa-footer-col aa-footer-col-brand">
                    <div class="aa-footer-logo">
                        <?php echo $brand->renderLogo('aa-footer-logo-wrapper'); ?>
                    </div>
                    <p class="aa-footer-desc">
                        Your premium destination for luxury fashion and modern ecommerce. We deliver excellence in every stitch.
                    </p>
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

                <!-- Column 2: Quick Links -->
                <div class="aa-footer-col">
                    <h4 class="aa-footer-heading">Quick Links</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'aa_footer_quick_links',
                        'container' => false,
                        'menu_class' => 'aa-footer-menu',
                        'fallback_cb' => function() {
                            echo '<ul class="aa-footer-menu"><li><a href="#">Setup Menu in WP Admin</a></li></ul>';
                        }
                    ]);
                    ?>
                </div>

                <!-- Column 3: Customer Service -->
                <div class="aa-footer-col">
                    <h4 class="aa-footer-heading">Customer Service</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'aa_footer_customer_service',
                        'container' => false,
                        'menu_class' => 'aa-footer-menu',
                        'fallback_cb' => function() {
                            echo '<ul class="aa-footer-menu"><li><a href="#">Setup Menu in WP Admin</a></li></ul>';
                        }
                    ]);
                    ?>
                </div>

                <!-- Column 4: Contact Info -->
                <div class="aa-footer-col aa-footer-col-contact">
                    <h4 class="aa-footer-heading">Contact Us</h4>
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

                <!-- Column 5: Trust & Payment -->
                <div class="aa-footer-col aa-footer-col-trust">
                    <h4 class="aa-footer-heading">Secure Shopping</h4>
                    <p class="aa-footer-desc">We use encrypted SSL security to ensure that your credit card information is 100% protected.</p>
                    <?php if ($payment_icons === 'yes'): ?>
                        <div class="aa-payment-icons">
                            <img src="<?php echo AA_PLUGIN_URL; ?>modules/Appearance/assets/images/visa.svg" alt="Visa" onerror="this.style.display='none'">
                            <img src="<?php echo AA_PLUGIN_URL; ?>modules/Appearance/assets/images/mastercard.svg" alt="Mastercard" onerror="this.style.display='none'">
                            <img src="<?php echo AA_PLUGIN_URL; ?>modules/Appearance/assets/images/paypal.svg" alt="PayPal" onerror="this.style.display='none'">
                            <img src="<?php echo AA_PLUGIN_URL; ?>modules/Appearance/assets/images/amex.svg" alt="Amex" onerror="this.style.display='none'">
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <div class="aa-footer-bottom">
        <div class="aa-container">
            <div class="aa-footer-bottom-inner">
                <div class="aa-copyright">
                    <?php echo $copyright; ?>
                </div>
            </div>
        </div>
    </div>
</div>
