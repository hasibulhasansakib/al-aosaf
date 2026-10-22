<?php
/**
 * Al Aosaf → Homepage Builder.
 *
 * @var array  $draft @var array $live @var bool $changed @var array $types @var string $preview
 * @var array  $cats  @var string $notice
 * @var \Alaosaf\Modules\Atelier\Controllers\AtelierAdminController $renderer
 */
if (!defined('ABSPATH')) exit;

use Alaosaf\Modules\Atelier\AtelierSchema;
use Alaosaf\Modules\Atelier\Controllers\AtelierFrontController;

$notices = [
    'imported'  => ['success', 'Imported into the draft. Check it with Preview, then Publish.'],
    'seeded'    => ['success', 'The starting layout was loaded into the draft. The live page is unchanged until you publish.'],
    'discarded' => ['success', 'Draft discarded. The editor now shows the live version.'],
    'bad'       => ['error', 'That JSON could not be read. Nothing was changed.'],
];
?>
<div class="at-builder" id="at-builder">
    <?php if (isset($notices[$notice])) : ?>
        <div class="notice notice-<?php echo esc_attr($notices[$notice][0]); ?> is-dismissible"><p><?php echo esc_html($notices[$notice][1]); ?></p></div>
    <?php endif; ?>

    <div class="at-top">
        <div class="at-top__info">
            <strong>Homepage Builder</strong>
            <span class="at-state<?php echo $changed ? ' is-changed' : ''; ?>" id="at-state"><?php echo $changed ? 'Draft has unpublished changes' : 'Draft = live page'; ?></span>
            <span class="at-saved" id="at-saved"></span>
        </div>
        <div class="at-top__actions">
            <button type="button" class="button" id="at-save">Save draft</button>
            <button type="button" class="button" id="at-preview"<?php echo $preview ? '' : ' disabled title="Add the shortcode to a page first"'; ?>>Save &amp; preview</button>
            <button type="button" class="button button-primary" id="at-publish">Publish</button>
        </div>
    </div>

    <div class="at-cols">
        <div class="at-main">
            <div class="at-list" id="at-list">
                <?php foreach ($draft['sections'] as $sec) {
                    if (isset($types[$sec['type']])) $renderer->sectionPanel($sec['type'], $sec, $cats);
                } ?>
            </div>
            <div class="at-addbar">
                <select id="at-add-type">
                    <?php foreach ($types as $k => $t) : ?><option value="<?php echo esc_attr($k); ?>"><?php echo esc_html($t['label']); ?></option><?php endforeach; ?>
                </select>
                <button type="button" class="button button-secondary" id="at-add">+ Add section</button>
            </div>
            <?php foreach ($types as $k => $t) : ?>
                <template id="at-tpl-<?php echo esc_attr($k); ?>"><?php $renderer->sectionPanel($k, ['id' => '__ID__', 'type' => $k, 'on' => 1, 'name' => '', 'd' => AtelierSchema::emptyData($k)], $cats); ?></template>
            <?php endforeach; ?>
        </div>

        <aside class="at-side">
            <div class="at-card-box" id="at-settings" data-scope>
                <h3>Page &amp; SEO</h3>
                <?php $renderer->fields(AtelierSchema::settingsFields(), $draft['settings'], $cats); ?>
            </div>

            <div class="at-card-box">
                <h3>How to use</h3>
                <ol class="at-help">
                    <li>Put <code>[<?php echo esc_html(AtelierFrontController::SHORTCODE); ?>]</code> on the page that should show these sections.</li>
                    <li>Edit, reorder (drag or arrows) and switch sections on or off.</li>
                    <li><b>Save draft</b> keeps your work without changing the site. <b>Save &amp; preview</b> opens the draft (only admins see it).</li>
                    <li><b>Publish</b> makes the draft live.</li>
                </ol>
            </div>

            <div class="at-card-box">
                <h3>Backup / move</h3>
                <p><button type="button" class="button" id="at-export">Download JSON backup</button></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="aa_atelier_import">
                    <?php wp_nonce_field('aa_atelier_import'); ?>
                    <textarea name="json" rows="3" class="widefat" placeholder="Paste a JSON backup here"></textarea>
                    <p><button class="button">Import into draft</button></p>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="at-danger">
                    <input type="hidden" name="action" value="aa_atelier_reset">
                    <?php wp_nonce_field('aa_atelier_reset'); ?>
                    <button class="button-link" name="what" value="discard" onclick="return confirm('Discard the draft and go back to the live version?')">Discard draft</button>
                    ·
                    <button class="button-link" name="what" value="seed" onclick="return confirm('Load the starting layout into the draft? The live page stays as it is.')">Load starting layout</button>
                </form>
            </div>
        </aside>
    </div>
</div>
