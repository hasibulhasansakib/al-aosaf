<?php
/** @var array $d @var \Alaosaf\Modules\Atelier\AtelierView $v */
if (!defined('ABSPATH')) exit;
$products = $v->products($d);
if (!$products) return;
$carousel = $d['layout'] === 'carousel';
$hasLink = $d['link'] !== '' && $d['link_url'] !== '';
$extra = '';
if ($carousel || $hasLink) {
    $extra .= '<div class="at-head__side">';
    if ($hasLink) {
        $extra .= '<a class="at-more" href="' . $v->link($d['link_url']) . '">' . esc_html($d['link']) . $v->icon('arrow') . '</a>';
    }
    if ($carousel) {
        $extra .= '<span class="at-arrows"><button type="button" class="at-round" data-rail-prev aria-label="আগে">' . $v->icon('left') . '</button>'
            . '<button type="button" class="at-round" data-rail-next aria-label="পরে">' . $v->icon('right') . '</button></span>';
    }
    $extra .= '</div>';
}
?>
<div data-rail-wrap>
    <?php $v->heading($d, $extra); ?>
    <div class="<?php echo $carousel ? 'at-rail' : 'at-grid'; ?>"<?php echo $carousel ? ' data-rail' : ''; ?>>
        <?php foreach ($products as $p) { $v->card($p); } ?>
    </div>
    <?php if ($carousel) : ?><div class="at-progress" aria-hidden="true"><i data-rail-bar></i></div><?php endif; ?>
</div>
