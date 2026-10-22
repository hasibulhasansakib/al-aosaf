<?php
/** @var array $d @var \Alaosaf\Modules\Atelier\AtelierView $v */
if (!defined('ABSPATH')) exit;
$slides = array_values(array_filter($d['slides'], static fn($s) => $s['image'] || $s['title'] !== ''));
if (!$slides) return;
$total = count($slides);
?>
<div class="at-hero" data-hero data-interval="<?php echo esc_attr((string) $d['interval']); ?>" aria-roledescription="carousel" aria-label="হাইলাইট">
    <div class="at-hero__slides">
        <?php foreach ($slides as $i => $s) :
            $attr = ['class' => 'at-hero__img', 'sizes' => '(max-width: 860px) 100vw, 58vw',
                'alt' => wp_strip_all_tags(str_replace('<br>', ' ', $s['title'])), 'loading' => $i === 0 ? 'eager' : 'lazy'];
            if ($i === 0) $attr['fetchpriority'] = 'high';
        ?>
        <article class="at-hero__slide<?php echo $i === 0 ? ' is-on' : ''; ?>" aria-roledescription="slide" aria-label="<?php echo esc_attr($v->bn(($i + 1) . ' / ' . $total)); ?>"<?php echo $i ? ' aria-hidden="true"' : ''; ?>>
            <div class="at-hero__copy">
                <?php echo $v->eyebrow($s['eyebrow'], 'at-hero__eyebrow'); ?>
                <?php if ($s['title'] !== '') : ?><h2 class="at-hero__title"><?php echo $v->rich($s['title']); ?></h2><?php endif; ?>
                <?php if ($s['text'] !== '') : ?><p class="at-hero__text"><?php echo esc_html($s['text']); ?></p><?php endif; ?>
                <div class="at-hero__btns">
                    <?php if ($s['btn1'] !== '') : ?><a class="at-btn at-btn--solid" href="<?php echo $v->link($s['btn1_url']); ?>"<?php echo $i ? ' tabindex="-1"' : ''; ?>><span><?php echo esc_html($s['btn1']); ?></span><?php echo $v->icon('arrow'); ?></a><?php endif; ?>
                    <?php if ($s['btn2'] !== '') : ?><a class="at-btn at-btn--line" href="<?php echo $v->link($s['btn2_url']); ?>"<?php echo $i ? ' tabindex="-1"' : ''; ?>><span><?php echo esc_html($s['btn2']); ?></span></a><?php endif; ?>
                </div>
            </div>
            <figure class="at-hero__media">
                <?php if ($s['image_m']) : ?>
                <picture>
                    <source media="(max-width: 860px)" srcset="<?php echo esc_attr((string) wp_get_attachment_image_srcset($s['image_m'], 'large')); ?>" sizes="100vw">
                    <?php echo $v->img($s['image'] ?: $s['image_m'], 'full', $attr); ?>
                </picture>
                <?php else : echo $v->img($s['image'], 'full', $attr); endif; ?>
            </figure>
        </article>
        <?php endforeach; ?>
    </div>
    <div class="at-hero__ui">
        <p class="at-hero__count"><span data-hero-now><?php echo esc_html($v->bn('01')); ?></span><i></i><span><?php echo esc_html($v->bn(str_pad((string) $total, 2, '0', STR_PAD_LEFT))); ?></span></p>
        <?php if ($total > 1) : ?>
        <div class="at-hero__bars">
            <?php for ($i = 0; $i < $total; $i++) : ?>
                <button type="button" class="at-hero__bar<?php echo $i === 0 ? ' is-on' : ''; ?>" data-go="<?php echo $i; ?>" aria-label="<?php echo esc_attr('স্লাইড ' . $v->bn((string) ($i + 1))); ?>"><i></i></button>
            <?php endfor; ?>
        </div>
        <div class="at-hero__arrows">
            <button type="button" class="at-round" data-prev aria-label="আগের স্লাইড"><?php echo $v->icon('left'); ?></button>
            <button type="button" class="at-round" data-next aria-label="পরের স্লাইড"><?php echo $v->icon('right'); ?></button>
        </div>
        <?php endif; ?>
        <?php if ($d['note'] !== '') : ?><p class="at-hero__note"><?php echo esc_html($d['note']); ?></p><?php endif; ?>
    </div>
</div>
