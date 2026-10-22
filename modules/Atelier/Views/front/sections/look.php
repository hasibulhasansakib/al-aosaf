<?php
/** @var array $d @var \Alaosaf\Modules\Atelier\AtelierView $v */
if (!defined('ABSPATH')) exit;
if (!$d['image']) return;
$spots = [];
foreach ($d['spots'] as $s) {
    $p = $s['product'] ? wc_get_product($s['product']) : null;
    if ($p && $p->is_visible()) {
        $spots[] = ['x' => $s['x'], 'y' => $s['y'], 'p' => $p];
    }
}
?>
<div class="at-look" data-look>
    <div class="at-look__copy">
        <?php $v->heading($d); ?>
        <?php if ($spots) : ?>
        <ol class="at-look__list">
            <?php foreach ($spots as $i => $s) : $p = $s['p']; ?>
            <li data-reveal><a href="<?php echo esc_url($p->get_permalink()); ?>" data-spot-link="<?php echo $i; ?>">
                <span class="at-look__n"><?php echo esc_html($v->bn((string) ($i + 1))); ?></span>
                <span class="at-look__thumb"><?php echo $v->img((int) $p->get_image_id(), 'thumbnail', ['alt' => '']); ?></span>
                <span class="at-look__info"><span class="at-look__name"><?php echo esc_html($p->get_name()); ?></span><span class="at-look__price"><?php echo wp_kses_post($p->get_price_html()); ?></span></span>
                <?php echo $v->icon('arrow'); ?>
            </a></li>
            <?php endforeach; ?>
        </ol>
        <?php endif; ?>
    </div>
    <div class="at-look__stage" data-reveal="mask">
        <?php echo $v->img($d['image'], 'full', ['class' => 'at-look__img', 'sizes' => '(max-width: 860px) 100vw, 60vw', 'alt' => wp_strip_all_tags($d['title'])]); ?>
        <?php foreach ($spots as $i => $s) : $p = $s['p']; ?>
        <div class="at-spot<?php echo $s['x'] > 60 ? ' at-spot--l' : ''; ?>" style="left:<?php echo esc_attr((string) $s['x']); ?>%;top:<?php echo esc_attr((string) $s['y']); ?>%" data-spot="<?php echo $i; ?>">
            <button type="button" class="at-spot__dot" aria-expanded="false" aria-label="<?php echo esc_attr($p->get_name()); ?>"><span><?php echo esc_html($v->bn((string) ($i + 1))); ?></span></button>
            <a class="at-spot__card" href="<?php echo esc_url($p->get_permalink()); ?>">
                <?php echo $v->img((int) $p->get_image_id(), 'thumbnail', ['alt' => '']); ?>
                <span><span class="at-spot__name"><?php echo esc_html($p->get_name()); ?></span><span class="at-spot__price"><?php echo wp_kses_post($p->get_price_html()); ?></span><span class="at-spot__go">দেখুন <?php echo $v->icon('arrow'); ?></span></span>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
