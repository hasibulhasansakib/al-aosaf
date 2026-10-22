<?php
/** @var array $d @var \Alaosaf\Modules\Atelier\AtelierView $v */
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
?>
<div class="at-bento at-bento--<?php echo count($tiles); ?>">
    <?php foreach ($tiles as $i => $t) : ?>
    <a class="at-tile" href="<?php echo esc_url($t['url']); ?>" data-reveal>
        <span class="at-tile__media"><?php echo $v->img((int) $t['image'], $i === 0 ? 'full' : 'large', ['class' => 'at-tile__img', 'alt' => $t['label'], 'sizes' => $i === 0 ? '(max-width: 860px) 80vw, 50vw' : '(max-width: 860px) 70vw, 25vw']); ?></span>
        <span class="at-tile__cap">
            <span class="at-tile__idx"><?php echo esc_html($v->bn(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT))); ?></span>
            <span class="at-tile__name"><?php echo esc_html($t['label']); ?></span>
            <?php if ($t['note'] !== '') : ?><span class="at-tile__note"><?php echo esc_html($t['note']); ?></span><?php endif; ?>
            <span class="at-tile__go">দেখুন <?php echo $v->icon('arrow'); ?></span>
        </span>
    </a>
    <?php endforeach; ?>
</div>
