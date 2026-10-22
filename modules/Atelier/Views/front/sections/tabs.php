<?php
/** @var array $d @var \Alaosaf\Modules\Atelier\AtelierView $v @var array $sec */
if (!defined('ABSPATH')) exit;
$panels = [];
foreach ($d['tabs'] as $t) {
    $list = $v->products(['source' => $t['source'], 'cats' => $t['cats'], 'ids' => $t['ids'], 'instock' => $d['instock']], (int) $d['limit']);
    if ($list && $t['label'] !== '') {
        $panels[] = ['label' => $t['label'], 'products' => $list];
    }
}
if (!$panels) return;
$uid = esc_attr($sec['id']);
$extra = '<div class="at-tabs" role="tablist">';
foreach ($panels as $i => $p) {
    $extra .= '<button type="button" role="tab" class="at-tab' . ($i === 0 ? ' is-on' : '') . '" id="' . $uid . '-t' . $i . '" aria-controls="' . $uid . '-p' . $i
        . '" aria-selected="' . ($i === 0 ? 'true' : 'false') . '" data-tab="' . $i . '">' . esc_html($p['label']) . '</button>';
}
$extra .= '</div>';
?>
<div data-tabs>
    <?php $v->heading($d, $extra); ?>
    <?php foreach ($panels as $i => $p) : ?>
    <div class="at-grid at-tabpanel<?php echo $i === 0 ? ' is-on' : ''; ?>" role="tabpanel" id="<?php echo $uid . '-p' . $i; ?>" aria-labelledby="<?php echo $uid . '-t' . $i; ?>"<?php echo $i ? ' hidden' : ''; ?>>
        <?php foreach ($p['products'] as $prod) { $v->card($prod); } ?>
    </div>
    <?php endforeach; ?>
</div>
