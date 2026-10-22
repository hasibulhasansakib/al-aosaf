<?php
/** @var array $d @var \Alaosaf\Modules\Atelier\AtelierView $v */
if (!defined('ABSPATH')) exit;
$items = array_values(array_filter($d['items'], static fn($i) => $i['q'] !== '' && $i['a'] !== ''));
if (!$items) return;
?>
<div class="at-faq">
    <?php $v->heading($d); ?>
    <div class="at-faq__list">
        <?php foreach ($items as $i => $it) : ?>
        <details class="at-faq__item" data-reveal<?php echo $i === 0 ? ' open' : ''; ?>>
            <summary><span><?php echo esc_html($it['q']); ?></span><?php echo $v->icon('plus'); ?></summary>
            <div class="at-faq__a"><p><?php echo nl2br(esc_html($it['a'])); ?></p></div>
        </details>
        <?php endforeach; ?>
    </div>
</div>
