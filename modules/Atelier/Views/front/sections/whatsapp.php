<?php
/** @var array $d @var \Alaosaf\Modules\Atelier\AtelierView $v */
if (!defined('ABSPATH')) exit;
$digits = preg_replace('/\D/', '', $d['phone']);
if ($digits === '') return;
$intl = strpos($digits, '880') === 0 ? $digits : '88' . $digits;
$wa = 'https://wa.me/' . $intl . ($d['message'] !== '' ? '?text=' . rawurlencode($d['message']) : '');
?>
<div class="at-wa<?php echo $d['image'] ? ' has-img' : ''; ?>">
    <?php if ($d['image']) : ?><div class="at-wa__bg"><?php echo $v->img($d['image'], 'full', ['alt' => '', 'sizes' => '100vw']); ?></div><?php endif; ?>
    <div class="at-wa__inner" data-reveal>
        <?php $v->heading($d); ?>
        <div class="at-wa__btns">
            <a class="at-btn at-btn--solid" href="<?php echo esc_url($wa); ?>" target="_blank" rel="noopener"><?php echo $v->icon('wa'); ?><span><?php echo esc_html($d['btn']); ?></span></a>
            <?php if ($d['call'] !== '') : ?><a class="at-btn at-btn--line" href="<?php echo esc_url('tel:+' . $intl); ?>"><?php echo $v->icon('phone'); ?><span><?php echo esc_html($d['call'] . ' · ' . $d['phone']); ?></span></a><?php endif; ?>
        </div>
    </div>
</div>
