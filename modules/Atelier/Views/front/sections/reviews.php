<?php
/** @var array $d @var \Alaosaf\Modules\Atelier\AtelierView $v */
if (!defined('ABSPATH')) exit;
$items = $d['source'] !== 'manual' ? $v->storeReviews((int) $d['min']) : [];
if ($d['source'] !== 'woo') {
    foreach ($d['items'] as $it) {
        if ($it['text'] !== '' || $it['image']) {
            $items[] = $it + ['verified' => false];
        }
    }
}
if (!$items) return;
$extra = '<div class="at-head__side"><span class="at-arrows"><button type="button" class="at-round" data-rail-prev aria-label="আগে">' . $v->icon('left')
    . '</button><button type="button" class="at-round" data-rail-next aria-label="পরে">' . $v->icon('right') . '</button></span></div>';
?>
<div data-rail-wrap>
    <?php $v->heading($d, $extra); ?>
    <div class="at-rail at-rail--reviews" data-rail>
        <?php foreach ($items as $r) : $stars = max(1, min(5, (int) $r['rating'])); ?>
        <figure class="at-review">
            <?php if ($r['image']) : ?><div class="at-review__img"><?php echo $v->img((int) $r['image'], 'medium_large', ['alt' => '']); ?></div><?php endif; ?>
            <p class="at-stars" aria-label="<?php echo esc_attr($v->bn((string) $stars) . ' / ৫'); ?>"><?php echo str_repeat('★', $stars) . '<span>' . str_repeat('★', 5 - $stars) . '</span>'; ?></p>
            <?php if ($r['text'] !== '') : ?><blockquote class="at-review__text"><?php echo esc_html($r['text']); ?></blockquote><?php endif; ?>
            <figcaption class="at-review__by">
                <b><?php echo esc_html($r['name']); ?></b>
                <?php if ($r['place'] !== '') : ?><span><?php echo esc_html($r['place']); ?></span><?php endif; ?>
                <?php if (!empty($r['verified'])) : ?><em>ভেরিফায়েড ক্রেতা</em><?php endif; ?>
            </figcaption>
        </figure>
        <?php endforeach; ?>
    </div>
    <div class="at-progress" aria-hidden="true"><i data-rail-bar></i></div>
</div>
