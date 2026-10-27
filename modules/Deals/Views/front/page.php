<?php
/**
 * Offer page.
 *
 * @var array $s        settings
 * @var array $items    [['p' => WC_Product, 'pct' => float, 'cat' => slug], ...]
 * @var array $cats     slug => ['name', 'n']
 * @var int   $maxPct
 * @var int   $ends
 * @var bool  $live
 * @var bool  $preview
 * @var \Alaosaf\Modules\Atelier\AtelierView $v
 */
if (!defined('ABSPATH')) exit;
$count = count($items);
$showChips = $s['filters'] && count($cats) > 1;
?>
<div class="at-root aa-offer" data-atelier data-offer data-per="<?php echo esc_attr((string) $s['per_page']); ?>">
    <?php if ($preview) : ?>
        <div class="at-preview-flag">প্রিভিউ · অফার এখনো চালু নেই, শুধু অ্যাডমিন এই দাম দেখছেন</div>
    <?php endif; ?>

    <section class="at-sec at-tone--dark aa-offer__hero">
        <div class="aa-offer__head" data-reveal>
            <?php echo $v->eyebrow($s['eyebrow']); ?>
            <?php if ($s['title'] !== '') : ?><h1 class="at-title at-title--xl"><?php echo $v->rich($s['title']); ?></h1><?php endif; ?>
            <?php if ($s['text'] !== '' && $count) : ?><p class="at-lead"><?php echo nl2br(esc_html($s['text'])); ?></p><?php endif; ?>

            <?php if ($count) : ?>
            <ul class="aa-offer__facts">
                <li><b><?php echo esc_html($v->bn((string) $count)); ?></b>টি পণ্যে ছাড়</li>
                <?php if ($maxPct > 0) : ?><li><b><?php echo esc_html($v->bn((string) $maxPct)); ?>%</b>পর্যন্ত কম দাম</li><?php endif; ?>
                <li><b>COD</b>হাতে পেয়ে টাকা</li>
            </ul>
            <?php endif; ?>

            <?php if ($count && $s['countdown'] && $ends > time()) : ?>
            <div class="at-count aa-offer__count" data-ends="<?php echo esc_attr((string) $ends); ?>" aria-label="অফার শেষ হতে বাকি">
                <span><b data-u="d">০০</b>দিন</span><span><b data-u="h">০০</b>ঘণ্টা</span><span><b data-u="m">০০</b>মিনিট</span><span><b data-u="s">০০</b>সেকেন্ড</span>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="at-sec at-tone--dark aa-offer__list">
        <?php if (!$count) : ?>
            <div class="aa-offer__empty" data-reveal>
                <p><?php echo esc_html($s['empty_text']); ?></p>
                <a class="at-btn at-btn--solid" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><span>সব পণ্য দেখুন</span><?php echo $v->icon('arrow'); ?></a>
            </div>
        <?php else : ?>
            <?php if ($showChips) : ?>
            <div class="aa-offer__bar">
                <div class="at-tabs aa-offer__chips" role="toolbar" aria-label="ক্যাটাগরি">
                    <button type="button" class="at-tab is-on" data-chip="">সব <small><?php echo esc_html($v->bn((string) $count)); ?></small></button>
                    <?php foreach ($cats as $slug => $c) : ?>
                        <button type="button" class="at-tab" data-chip="<?php echo esc_attr($slug); ?>"><?php echo esc_html($c['name']); ?> <small><?php echo esc_html($v->bn((string) $c['n'])); ?></small></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="at-grid aa-offer__grid" style="--cols:<?php echo esc_attr($s['cols']); ?>" data-offer-grid>
                <?php foreach ($items as $i => $it) : ?>
                    <div class="aa-offer__cell" data-cat="<?php echo esc_attr($it['cat']); ?>"<?php echo $i >= $s['per_page'] ? ' hidden' : ''; ?>>
                        <?php $v->card($it['p'], $i < 4); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($count > $s['per_page']) : ?>
            <div class="aa-offer__more">
                <button type="button" class="at-btn at-btn--line" data-offer-more><span>আরও দেখুন</span></button>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</div>
