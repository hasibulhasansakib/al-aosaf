<?php
/**
 * Atelier product card: main photo with a glass "film dock" of small gallery photos.
 * Hover (desktop) or tap (phone) a small photo to show it big.
 *
 * @var \WC_Product $product
 * @var bool        $eager
 * @var \Alaosaf\Modules\Atelier\AtelierView $this
 */
if (!defined('ABSPATH')) exit;

$pid      = $product->get_id();
$url      = $product->get_permalink();
$name     = $product->get_name();
$main     = (int) $product->get_image_id();
$photos   = array_values(array_unique(array_filter(array_merge([$main], array_map('intval', $product->get_gallery_image_ids())))));
$instock  = $product->is_in_stock();
$variable = $product->is_type('variable');
$sizes    = '(max-width: 640px) 46vw, (max-width: 1100px) 30vw, 300px';
$dockMax  = 4;

$reg = (float) $product->get_regular_price();
$now = (float) $product->get_price();
if ($variable) {
    $reg = (float) $product->get_variation_regular_price('max');
    $now = (float) $product->get_variation_sale_price('max');
}
$onSale = $product->is_on_sale() && $reg > 0 && $now > 0 && $now < $reg;

$badges = [];
if (!$instock) {
    $badges[] = '<span class="at-badge at-badge--out">স্টক শেষ</span>';
} else {
    if ($onSale) {
        $badges[] = '<span class="at-badge at-badge--sale">' . esc_html($this->bn((string) round(100 - $now / $reg * 100))) . '% ছাড়</span>';
    }
    if ($product->get_date_created() && $product->get_date_created()->getTimestamp() > time() - 30 * DAY_IN_SECONDS) {
        $badges[] = '<span class="at-badge">নতুন</span>';
    }
}

$catName = '';
$terms = get_the_terms($pid, 'product_cat');
if ($terms && !is_wp_error($terms)) {
    $terms = array_values(array_filter($terms, static fn($t) => $t->slug !== 'uncategorized'));
    $catName = $terms ? end($terms)->name : '';
}

$inWishlist = class_exists('\Alaosaf\Modules\Wishlist\WishlistModule') && \Alaosaf\Modules\Wishlist\WishlistModule::isInWishlist($pid);
?>
<article class="at-card<?php echo $instock ? '' : ' is-out'; ?>" data-card>
    <div class="at-card__media">
        <a href="<?php echo esc_url($url); ?>" class="at-card__imgs" tabindex="-1" aria-hidden="true">
            <?php
            echo $main
                ? $this->img($main, 'woocommerce_single', ['class' => 'at-card__img', 'sizes' => $sizes, 'alt' => $name, 'loading' => $eager ? 'eager' : 'lazy', 'data-main' => '1'])
                : wc_placeholder_img('woocommerce_single', ['class' => 'at-card__img']);
            ?>
        </a>
        <?php if ($badges) : ?><div class="at-card__badges"><?php echo implode('', $badges); // escaped above ?></div><?php endif; ?>
        <button type="button" class="aa-wishlist-btn at-card__wish<?php echo $inWishlist ? ' aa-in-wishlist' : ''; ?>" data-product-id="<?php echo esc_attr((string) $pid); ?>" aria-label="উইশলিস্টে রাখুন">
            <?php echo $this->icon('heart'); ?>
        </button>
        <?php if (count($photos) > 1) : ?>
        <div class="at-dock" role="group" aria-label="আরও ছবি">
            <?php foreach (array_slice($photos, 0, $dockMax) as $i => $aid) :
                $full = wp_get_attachment_image_src($aid, 'woocommerce_single');
                if (!$full) continue;
            ?>
                <button type="button" class="at-dock__t<?php echo $i === 0 ? ' is-on' : ''; ?>"
                    data-src="<?php echo esc_url($full[0]); ?>"
                    data-srcset="<?php echo esc_attr((string) wp_get_attachment_image_srcset($aid, 'woocommerce_single')); ?>"
                    aria-label="<?php echo esc_attr('ছবি ' . $this->bn((string) ($i + 1))); ?>">
                    <?php echo $this->img($aid, 'thumbnail', ['alt' => '', 'sizes' => '40px']); ?>
                </button>
            <?php endforeach; ?>
            <?php if (count($photos) > $dockMax) : ?>
                <a class="at-dock__more" href="<?php echo esc_url($url); ?>" aria-label="সব ছবি দেখুন">+<?php echo esc_html($this->bn((string) (count($photos) - $dockMax))); ?></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="at-card__body">
        <?php if ($catName) : ?><p class="at-card__cat"><?php echo esc_html($catName); ?></p><?php endif; ?>
        <h3 class="at-card__title"><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($name); ?></a></h3>
        <div class="at-card__price">
            <?php echo wp_kses_post($product->get_price_html()); ?>
        </div>
        <?php if ($onSale) : ?><p class="at-card__save">সাশ্রয় ৳<?php echo esc_html($this->bn(number_format($reg - $now))); ?></p><?php endif; ?>
    </div>
    <?php if ($instock) : ?>
        <div class="at-card__actions">
            <?php if ($variable) : ?>
                <button type="button" class="at-card__buy aa-quick-view-btn" data-product_id="<?php echo esc_attr((string) $pid); ?>" data-buy_now="true">অর্ডার করুন</button>
                <button type="button" class="at-card__add aa-quick-view-btn" data-product_id="<?php echo esc_attr((string) $pid); ?>" aria-label="কার্টে যোগ করুন" title="কার্টে যোগ করুন">
                    <?php echo $this->icon('bag'); ?>
                </button>
            <?php else : ?>
                <a href="<?php echo esc_url(add_query_arg('add-to-cart', $pid, wc_get_checkout_url())); ?>" class="at-card__buy" rel="nofollow">অর্ডার করুন</a>
                <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" class="at-card__add add_to_cart_button ajax_add_to_cart" data-quantity="1" data-product_id="<?php echo esc_attr((string) $pid); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" rel="nofollow" aria-label="কার্টে যোগ করুন" title="কার্টে যোগ করুন">
                    <?php echo $this->icon('bag'); ?>
                </a>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <div class="at-card__actions"><a class="at-card__buy at-card__buy--ghost" href="<?php echo esc_url($url); ?>">বিস্তারিত দেখুন</a></div>
    <?php endif; ?>
</article>
