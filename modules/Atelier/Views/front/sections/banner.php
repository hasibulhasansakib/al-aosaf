<?php
/** @var array $d @var \Alaosaf\Modules\Atelier\AtelierView $v */
if (!defined('ABSPATH')) exit;
if ($d['title'] === '' && !$d['image']) return;
$ends = 0;
if ($d['ends'] !== '') {
    $dt = date_create($d['ends'], wp_timezone());
    $ends = $dt ? $dt->getTimestamp() : 0;
}
?>
<div class="at-banner at-banner--<?php echo esc_attr($d['side']); ?>">
    <div class="at-banner__copy" data-reveal>
        <?php echo $v->eyebrow($d['eyebrow']); ?>
        <?php if ($d['title'] !== '') : ?><h2 class="at-title at-title--xl"><?php echo $v->rich($d['title']); ?></h2><?php endif; ?>
        <?php if ($d['text'] !== '') : ?><p class="at-lead"><?php echo nl2br(esc_html($d['text'])); ?></p><?php endif; ?>
        <?php if ($ends > time()) : ?>
        <div class="at-count" data-ends="<?php echo esc_attr((string) $ends); ?>" aria-label="অফার শেষ হতে বাকি">
            <span><b data-u="d">০০</b>দিন</span><span><b data-u="h">০০</b>ঘণ্টা</span><span><b data-u="m">০০</b>মিনিট</span><span><b data-u="s">০০</b>সেকেন্ড</span>
        </div>
        <?php endif; ?>
        <?php if ($d['btn'] !== '') : ?><a class="at-btn at-btn--solid" href="<?php echo $v->link($d['btn_url']); ?>"><span><?php echo esc_html($d['btn']); ?></span><?php echo $v->icon('arrow'); ?></a><?php endif; ?>
    </div>
    <?php if ($d['image']) : ?>
    <figure class="at-banner__media" data-reveal="mask"><?php echo $v->img($d['image'], 'full', ['class' => 'at-banner__img', 'sizes' => '(max-width: 860px) 100vw, 55vw', 'alt' => wp_strip_all_tags($d['title'])]); ?></figure>
    <?php endif; ?>
</div>
