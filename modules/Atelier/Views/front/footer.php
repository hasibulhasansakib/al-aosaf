<?php
/**
 * Slim page footer for the Atelier pages: same background as the section above it,
 * brand, a few links, phone/email and the copyright line. Replaces the big site footer here.
 *
 * @var string $tone  dark | surface | sand
 * @var \Alaosaf\Modules\Atelier\AtelierView $this
 */
if (!defined('ABSPATH')) exit;

$brand = (array) get_option('aa_brand_settings', []);
$name  = (string) ($brand['general_business_name'] ?? get_bloginfo('name'));
$phone = preg_replace('/[^\d+]/', '', (string) ($brand['contact_phone'] ?? ''));
$email = sanitize_email((string) ($brand['contact_email'] ?? ''));

$links = [];
if (function_exists('wc_get_page_permalink')) {
    $links[] = ['সব পণ্য', wc_get_page_permalink('shop')];
}
if (class_exists('\Alaosaf\Modules\Deals\DealsSchema') && ($offer = \Alaosaf\Modules\Deals\DealsSchema::pageId()) && get_post_status($offer) === 'publish') {
    $links[] = ['অফার', get_permalink($offer)];
}
foreach (['refund_returns' => 'রিটার্ন পলিসি', 'privacy-policy' => 'প্রাইভেসি', 'terms-of-service' => 'শর্তাবলি'] as $slug => $label) {
    $p = get_page_by_path($slug);
    if ($p && $p->post_status === 'publish') {
        $links[] = [$label, get_permalink($p)];
    }
}
?>
<footer class="at-foot at-tone--<?php echo esc_attr($tone); ?>">
    <div class="at-foot__inner">
        <div class="at-foot__row">
            <a class="at-foot__brand" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html($name); ?></a>
            <?php if ($links) : ?>
            <nav class="at-foot__links" aria-label="ফুটার লিংক">
                <?php foreach ($links as [$label, $url]) : ?><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($label); ?></a><?php endforeach; ?>
            </nav>
            <?php endif; ?>
            <div class="at-foot__contact">
                <?php if ($phone) : ?><a href="tel:<?php echo esc_attr($phone); ?>"><?php echo $this->icon('phone'); ?><?php echo esc_html($phone); ?></a><?php endif; ?>
                <?php if ($email) : ?><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a><?php endif; ?>
            </div>
        </div>
        <p class="at-foot__copy">© <?php echo esc_html(wp_date('Y') . ' ' . $name); ?> · সারা দেশে ক্যাশ অন ডেলিভারি</p>
    </div>
</footer>
