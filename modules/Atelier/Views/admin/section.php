<?php
/**
 * One section panel in the builder.
 *
 * @var string $type @var array $sec @var array $def @var string $label @var array $cats
 * @var \Alaosaf\Modules\Atelier\Controllers\AtelierAdminController $this
 */
if (!defined('ABSPATH')) exit;
?>
<div class="at-s<?php echo $sec['on'] ? '' : ' is-off'; ?>" data-scope data-type="<?php echo esc_attr($type); ?>" data-id="<?php echo esc_attr($sec['id']); ?>">
    <div class="at-s__bar">
        <span class="at-s__drag dashicons dashicons-move" title="Drag to reorder"></span>
        <span class="at-s__type"><?php echo esc_html($label); ?></span>
        <input type="text" class="at-s__name" value="<?php echo esc_attr($sec['name']); ?>" placeholder="Note for yourself (optional)">
        <label class="at-switch at-s__on" title="Show on the page"><input type="checkbox" class="at-s__onbox"<?php checked($sec['on'], 1); ?>><span></span></label>
        <button type="button" class="button-link at-s__up" title="Move up"><span class="dashicons dashicons-arrow-up-alt2"></span></button>
        <button type="button" class="button-link at-s__down" title="Move down"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
        <button type="button" class="button-link at-s__dup" title="Duplicate"><span class="dashicons dashicons-admin-page"></span></button>
        <button type="button" class="button-link at-s__del" title="Delete"><span class="dashicons dashicons-trash"></span></button>
        <button type="button" class="button at-s__tog">Edit</button>
    </div>
    <div class="at-s__body">
        <?php if (!empty($def['hint'])) : ?><p class="at-hint"><?php echo esc_html($def['hint']); ?></p><?php endif; ?>
        <?php $this->fields($def['fields'], $sec['d'], $cats); ?>
    </div>
</div>
