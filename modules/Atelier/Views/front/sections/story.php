<?php
/** @var array $d @var \Alaosaf\Modules\Atelier\AtelierView $v */
if (!defined('ABSPATH')) exit;
if ($d['title'] === '' && $d['text'] === '') return;
$points = array_values(array_filter($d['points'], static fn($p) => $p['title'] !== ''));
?>
<div class="at-story<?php echo $d['image'] ? '' : ' at-story--text'; ?>">
    <?php if ($d['image']) : ?><figure class="at-story__media" data-reveal="mask"><?php echo $v->img($d['image'], 'full', ['class' => 'at-story__img', 'sizes' => '(max-width: 860px) 100vw, 45vw', 'alt' => wp_strip_all_tags($d['title'])]); ?></figure><?php endif; ?>
    <div class="at-story__copy">
        <?php $v->heading($d); ?>
        <?php if ($points) : ?>
        <dl class="at-story__points">
            <?php foreach ($points as $p) : ?><div data-reveal><dt><?php echo esc_html($p['title']); ?></dt><dd><?php echo esc_html($p['text']); ?></dd></div><?php endforeach; ?>
        </dl>
        <?php endif; ?>
        <?php if ($d['btn'] !== '') : ?><a class="at-btn at-btn--line" href="<?php echo $v->link($d['btn_url']); ?>"><span><?php echo esc_html($d['btn']); ?></span><?php echo $v->icon('arrow'); ?></a><?php endif; ?>
    </div>
</div>
