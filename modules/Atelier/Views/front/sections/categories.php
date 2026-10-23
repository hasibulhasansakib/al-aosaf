<?php
/**
 * Category boxes: equal photo boxes with the name in the middle.
 *
 * @var array $d @var \Alaosaf\Modules\Atelier\AtelierView $v
 */
if (!defined('ABSPATH')) exit;
$tiles = [];
foreach ($d['items'] as $it) {
    $term = $it['cat'] ? get_term($it['cat'], 'product_cat') : null;
    if (!$term || is_wp_error($term)) continue;
    $link = get_term_link($term);
    $tiles[] = [
        'url'   => is_string($link) ? $link : '#',
        'label' => $it['label'] !== '' ? $it['label'] : $term->name,
        'note'  => $it['note'],
        'image' => $it['image'] ?: $v->categoryImage((int) $term->term_id),
    ];
}
if (!$tiles) return;
$v->heading($d);
$n = count($tiles);
?>
<div class="at-cats" style="--n:<?php echo esc_attr((string) ($n > 4 ? 3 + ($n > 6 ? 1 : 0) : $n)); ?>">
    <?php foreach ($tiles as $t) : ?>
    <a class="at-cat" href="<?php echo esc_url($t['url']); ?>" data-reveal>
        <?php echo $v->img((int) $t['image'], 'large', ['class' => 'at-cat__img', 'alt' => $t['label'], 'sizes' => '(max-width: 760px) 46vw, 25vw']); ?>
        <span class="at-cat__cap">
            <span class="at-cat__name"><?php echo esc_html($t['label']); ?></span>
            <?php if ($t['note'] !== '') : ?><span class="at-cat__note"><?php echo esc_html($t['note']); ?></span><?php endif; ?>
            <span class="at-cat__go">দেখুন <?php echo $v->icon('arrow'); ?></span>
        </span>
    </a>
    <?php endforeach; ?>
</div>
