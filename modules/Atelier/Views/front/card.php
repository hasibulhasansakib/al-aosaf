<?php
/**
 * Atelier product card (same layout as the Al Jayyid store cards, in the Atelier colours):
 * framed card, inset photo, deal/new pills, wishlist, a row of small photos that switch the big one,
 * name, one-line description, price with % off, order button.
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
$sizes    = '(max-width: 640px) 46vw, (max-width: 1100px) 30vw, 320px';
$slots    = 4;

$reg = (float) $product->get_regular_price();
$now = (float) $product->get_price();
if ($variable) {
    $reg = (float) $product->get_variation_regular_price('max');
    $now = (float) $product->get_variation_sale_price('max');
}
$onSale = $product->is_on_sale() && $reg > 0 && $now > 0 && $now < $reg;
$isNew  = $product->get_date_created() && $product->get_date_created()->getTimestamp() > time() - 30 * DAY_IN_SECONDS;

$sub = trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags(strip_shortcodes((string) $product->get_short_description()))));
if (mb_strlen($sub) > 80) {
    $sub = rtrim(mb_substr($sub, 0, 78), " ,.;:-") . '…';
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
        <div class="at-card__tags">
            <?php if (!$instock) : ?><span class="at-tag at-tag--out">স্টক শেষ</span>
            <?php else : ?>
                <?php if ($onSale) : ?><span class="at-tag at-tag--deal">অফার</span><?php endif; ?>
                <?php if ($isNew) : ?><span class="at-tag at-tag--new">নতুন</span><?php endif; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="aa-wishlist-btn at-card__wish<?php echo $inWishlist ? ' aa-in-wishlist' : ''; ?>" data-product-id="<?php echo esc_attr((string) $pid); ?>" aria-label="উইশলিস্টে রাখুন">
            <?php echo $this->icon('heart'); ?>
        </button>
    </div>

    <?php if (count($photos) > 1) : ?>
    <div class="at-card__thumbs" role="group" aria-label="আরও ছবি">
        <?php foreach (array_slice($photos, 0, $slots) as $i => $aid) :
            $full = wp_get_attachment_image_src($aid, 'woocommerce_single');
            if (!$full) continue;
            $more = ($i === $slots - 1 && count($photos) > $slots) ? count($photos) - $slots : 0;
        ?>
            <button type="button" class="at-card__thumb<?php echo $i === 0 ? ' is-on' : ''; ?>"
                data-src="<?php echo esc_url($full[0]); ?>"
                data-srcset="<?php echo esc_attr((string) wp_get_attachment_image_srcset($aid, 'woocommerce_single')); ?>"
                aria-label="<?php echo esc_attr('ছবি ' . $this->bn((string) ($i + 1))); ?>">
                <?php echo $this->img($aid, 'thumbnail', ['alt' => '', 'sizes' => '64px']); ?>
                <?php if ($more) : ?><span class="at-card__more">+<?php echo esc_html($this->bn((string) $more)); ?></span><?php endif; ?>
            </button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="at-card__body">
        <h3 class="at-card__title"><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($name); ?></a></h3>
        <?php if ($sub !== '') : ?><p class="at-card__sub"><?php echo esc_html($sub); ?></p><?php endif; ?>
        <div class="at-card__price">
            <?php if ($onSale && !$variable) : ?>
                <span class="at-card__now"><?php echo wp_kses_post(wc_price($now)); ?></span>
                <del class="at-card__was"><?php echo wp_kses_post(wc_price($reg)); ?></del>
            <?php else : ?>
                <span class="at-card__now"><?php echo wp_kses_post($product->get_price_html()); ?></span>
            <?php endif; ?>
            <?php if ($onSale) : ?><span class="at-card__off"><?php echo esc_html($this->bn((string) round(100 - $now / $reg * 100))); ?>% ছাড়</span><?php endif; ?>
        </div>
        <div class="at-card__actions">
            <?php if (!$instock) : ?>
                <a class="at-card__buy is-off" href="<?php echo esc_url($url); ?>">বিস্তারিত দেখুন</a>
            <?php elseif ($variable) : ?>
                <button type="button" class="at-card__buy aa-quick-view-btn" data-product_id="<?php echo esc_attr((string) $pid); ?>" data-buy_now="true">অর্ডার করুন</button>
            <?php else : ?>
                <a href="<?php echo esc_url(add_query_arg('add-to-cart', $pid, wc_get_checkout_url())); ?>" class="at-card__buy" rel="nofollow">অর্ডার করুন</a>
            <?php endif; ?>
        </div>
    </div>
</article>
