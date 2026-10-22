<?php
/** @var array $d @var \Alaosaf\Modules\Atelier\AtelierView $v */
if (!defined('ABSPATH')) exit;
$items = array_values(array_filter($d['items'], static fn($i) => $i['title'] !== ''));
if (!$items) return;
?>
<div class="at-trust">
    <?php foreach ($items as $it) : ?>
    <div class="at-trust__item" data-reveal>
        <?php echo $v->icon($it['icon']); ?>
        <div>
            <p class="at-trust__t"><?php echo esc_html($it['title']); ?></p>
            <?php if ($it['text'] !== '') : ?><p class="at-trust__s"><?php echo esc_html($it['text']); ?></p><?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
