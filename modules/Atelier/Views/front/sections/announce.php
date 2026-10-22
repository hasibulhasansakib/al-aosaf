<?php
/** @var array $d @var \Alaosaf\Modules\Atelier\AtelierView $v */
if (!defined('ABSPATH')) exit;
$items = array_values(array_filter($d['items'], static fn($i) => $i['text'] !== ''));
if (!$items) return;
?>
<div class="at-announce" data-announce>
    <div class="at-announce__track">
        <?php foreach ($items as $i => $it) : ?>
            <p class="at-announce__item<?php echo $i === 0 ? ' is-on' : ''; ?>">
                <?php if ($it['url'] !== '') : ?><a href="<?php echo $v->link($it['url']); ?>"><?php echo esc_html($it['text']); ?></a><?php else : echo esc_html($it['text']); endif; ?>
            </p>
        <?php endforeach; ?>
    </div>
</div>
