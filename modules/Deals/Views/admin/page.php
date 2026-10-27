<?php
/**
 * Al Aosaf → Offer Page.
 *
 * @var array  $doc
 * @var array  $s
 * @var int    $pageId
 * @var array  $cats
 * @var array  $status
 * @var array  $preview
 * @var string $msg
 * @var string $nonce
 * @var \Alaosaf\Modules\Deals\Controllers\DealsAdminController $ctl
 */
if (!defined('ABSPATH')) exit;

use Alaosaf\Modules\Deals\DealsSchema;

$types = DealsSchema::types();
$money = static fn($n) => $n > 0 ? '৳' . number_format((float) $n) : '—';

$ruleCard = static function (array $r, string $i) use ($ctl, $cats, $types, $preview): void {
    $n = 'deals[rules][' . $i . ']';
    $count = ($preview['counts'][(int) $i] ?? 0);
    ?>
    <div class="dl-rule<?php echo $r['on'] ? '' : ' is-off'; ?>" data-rule>
        <div class="dl-rule__bar">
            <span class="dl-rule__drag dashicons dashicons-move" title="Drag to change priority"></span>
            <label class="dl-switch"><input type="checkbox" name="<?php echo esc_attr($n); ?>[on]" value="1" data-on<?php checked($r['on']); ?>><span></span></label>
            <input type="text" class="dl-rule__name" name="<?php echo esc_attr($n); ?>[name]" value="<?php echo esc_attr($r['name']); ?>" placeholder="Rule name (only you see it), e.g. Old Panjabi clearance">
            <?php if ($r['id'] !== '' && $count) : ?><span class="dl-pill"><?php echo esc_html((string) $count); ?> products</span><?php endif; ?>
            <button type="button" class="button-link dl-rule__del" data-del>Remove</button>
        </div>
        <input type="hidden" name="<?php echo esc_attr($n); ?>[id]" value="<?php echo esc_attr($r['id']); ?>">
        <div class="dl-grid">
            <div class="dl-f dl-f--wide">
                <span class="dl-l">Which products?</span>
                <div class="dl-seg">
                    <label><input type="radio" name="<?php echo esc_attr($n); ?>[target]" value="products" data-target<?php checked($r['target'], 'products'); ?>> Pick products</label>
                    <label><input type="radio" name="<?php echo esc_attr($n); ?>[target]" value="cats" data-target<?php checked($r['target'], 'cats'); ?>> Whole categories</label>
                </div>
            </div>
            <div class="dl-f dl-f--wide" data-show="products">
                <span class="dl-l">Products</span>
                <select class="wc-product-search" multiple name="<?php echo esc_attr($n); ?>[ids][]" data-action="woocommerce_json_search_products" data-placeholder="Search products…" style="width:100%"><?php echo $ctl->productOptions($r['ids']); // phpcs:ignore ?></select>
            </div>
            <div class="dl-f dl-f--wide" data-show="cats">
                <span class="dl-l">Categories (sub-categories included)</span>
                <select class="wc-enhanced-select" multiple name="<?php echo esc_attr($n); ?>[cats][]" data-placeholder="Choose categories…" style="width:100%">
                    <?php foreach ($cats as $id => $label) : ?><option value="<?php echo esc_attr((string) $id); ?>"<?php echo in_array($id, $r['cats'], true) ? ' selected' : ''; ?>><?php echo esc_html($label); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="dl-f dl-f--wide" data-show="cats">
                <span class="dl-l">Leave out these products</span>
                <select class="wc-product-search" multiple name="<?php echo esc_attr($n); ?>[exclude][]" data-action="woocommerce_json_search_products" data-placeholder="Optional…" style="width:100%"><?php echo $ctl->productOptions($r['exclude']); // phpcs:ignore ?></select>
            </div>
            <div class="dl-f">
                <span class="dl-l">Discount</span>
                <div class="dl-inline">
                    <select name="<?php echo esc_attr($n); ?>[type]" data-type>
                        <?php foreach ($types as $k => $l) : ?><option value="<?php echo esc_attr($k); ?>"<?php selected($r['type'], $k); ?>><?php echo esc_html($l); ?></option><?php endforeach; ?>
                    </select>
                    <input type="number" min="0" step="1" class="small-text" name="<?php echo esc_attr($n); ?>[value]" value="<?php echo esc_attr((string) $r['value']); ?>" data-value>
                    <span class="dl-unit" data-unit></span>
                </div>
            </div>
            <div class="dl-f">
                <span class="dl-l">Count the discount from</span>
                <select name="<?php echo esc_attr($n); ?>[base]">
                    <option value="current"<?php selected($r['base'], 'current'); ?>>Current price (sale price if set)</option>
                    <option value="regular"<?php selected($r['base'], 'regular'); ?>>Regular price</option>
                </select>
            </div>
            <div class="dl-f">
                <span class="dl-l">Badge on the card</span>
                <input type="text" name="<?php echo esc_attr($n); ?>[badge]" value="<?php echo esc_attr($r['badge']); ?>" placeholder="Empty = page default (e.g. ক্লিয়ারেন্স)">
            </div>
            <p class="dl-f dl-f--wide dl-example" data-example></p>
        </div>
    </div>
    <?php
};
?>
<div class="dl-wrap">
    <?php if ($msg === 'saved') : ?><div class="notice notice-success is-dismissible"><p>Saved. Page caches were cleared.</p></div><?php endif; ?>
    <?php if ($msg === 'page') : ?><div class="notice notice-success is-dismissible"><p>The offer page is ready.</p></div><?php endif; ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="dl-form">
        <input type="hidden" name="action" value="aa_deals_save">
        <?php wp_nonce_field($nonce); ?>

        <div class="dl-top">
            <div class="dl-top__info">
                <strong>Offer Page</strong>
                <span class="dl-state is-<?php echo esc_attr($status[0]); ?>"><?php echo esc_html($status[1]); ?></span>
            </div>
            <div class="dl-top__actions">
                <?php if ($pageId) : ?>
                    <a class="button" href="<?php echo esc_url(add_query_arg('aa_preview', '1', get_permalink($pageId))); ?>" target="_blank" rel="noopener">Preview</a>
                    <a class="button" href="<?php echo esc_url(get_permalink($pageId)); ?>" target="_blank" rel="noopener">View page</a>
                <?php endif; ?>
                <button type="submit" class="button button-primary">Save</button>
            </div>
        </div>

        <div class="dl-cols">
            <div>
                <div class="dl-card">
                    <h2>1. Offer on / off</h2>
                    <div class="dl-grid">
                        <div class="dl-f dl-f--wide">
                            <label class="dl-switch dl-switch--big"><input type="checkbox" name="deals[settings][enabled]" value="1"<?php checked($s['enabled']); ?>><span></span> Offer is on (prices below are used on the whole site: offer page, product page, cart, checkout)</label>
                        </div>
                        <div class="dl-f"><span class="dl-l">Starts (empty = now)</span><input type="datetime-local" name="deals[settings][start]" value="<?php echo esc_attr($s['start']); ?>"></div>
                        <div class="dl-f"><span class="dl-l">Ends (empty = no end)</span><input type="datetime-local" name="deals[settings][end]" value="<?php echo esc_attr($s['end']); ?>"></div>
                    </div>
                    <p class="dl-hint">Time is Bangladesh time (site time zone). When the end time passes, all prices go back to normal on their own.</p>
                </div>

                <div class="dl-card">
                    <h2>2. Discount rules</h2>
                    <p class="dl-hint">Each rule = a group of products + a discount. If a product is in two rules, the upper one wins (drag to reorder).</p>
                    <div class="dl-rules" data-rules>
                        <?php foreach ($doc['rules'] as $i => $r) { $ruleCard($r, (string) $i); } ?>
                    </div>
                    <p class="dl-empty" data-empty<?php echo $doc['rules'] ? ' hidden' : ''; ?>>No rules yet. Add the first one.</p>
                    <button type="button" class="button" data-add>+ Add rule</button>
                    <template id="dl-tpl"><?php $ruleCard(DealsSchema::rule(), '__i__'); ?></template>
                </div>

                <div class="dl-card">
                    <h2>3. Which products get the offer <small>(saved rules)</small></h2>
                    <?php if (!$preview['rows']) : ?>
                        <p class="dl-hint">No products yet. Add a rule and press Save.</p>
                    <?php else : ?>
                        <p class="dl-hint"><?php echo esc_html((string) $preview['total']); ?> products in total<?php echo $preview['total'] > 60 ? ' (first 60 shown)' : ''; ?>. Variable products show their first size/colour.</p>
                        <table class="widefat striped dl-table">
                            <thead><tr><th>Product</th><th>Rule</th><th>Regular</th><th>Now</th><th>Offer price</th></tr></thead>
                            <tbody>
                            <?php foreach ($preview['rows'] as $row) : ?>
                                <tr class="<?php echo $row['new'] === null ? 'is-none' : ''; ?>">
                                    <td><a href="<?php echo esc_url((string) $row['edit']); ?>"><?php echo esc_html($row['name']); ?></a><?php echo $row['stock'] ? '' : ' <em class="dl-out">out of stock</em>'; ?></td>
                                    <td><?php echo esc_html($row['rule']); ?></td>
                                    <td><?php echo esc_html($money($row['reg'])); ?></td>
                                    <td><?php echo esc_html($money($row['now'])); ?></td>
                                    <td><?php if ($row['new'] === null) : ?><span class="dl-out">no change</span><?php else : ?><b><?php echo esc_html($money($row['new'])); ?></b> <span class="dl-off"><?php echo esc_html((string) round(100 - $row['new'] / max(1, $row['reg']) * 100)); ?>% off</span><?php endif; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="dl-card">
                    <h2>Offer page link</h2>
                    <?php if ($pageId) : ?>
                        <input type="text" readonly class="widefat dl-link" value="<?php echo esc_attr((string) get_permalink($pageId)); ?>" onclick="this.select()">
                        <p class="dl-hint">Share this link in ads, Messenger or WhatsApp.</p>
                    <?php else : ?>
                        <p class="dl-hint">No page yet.</p>
                        <a class="button button-secondary" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=aa_deals_page'), 'aa_deals_page')); ?>">Create the offer page</a>
                    <?php endif; ?>
                    <div class="dl-f" style="margin-top:12px"><span class="dl-l">Page</span>
                        <select name="deals[settings][page]">
                            <option value="0">— auto (page with [aa_offer_page]) —</option>
                            <?php foreach (get_pages(['post_status' => 'publish,private,draft']) as $p) : ?>
                                <option value="<?php echo esc_attr((string) $p->ID); ?>"<?php selected($pageId, $p->ID); ?>><?php echo esc_html($p->post_title . ' (#' . $p->ID . ')'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="dl-card">
                    <h2>Page texts</h2>
                    <div class="dl-f"><span class="dl-l">Small label</span><input type="text" class="widefat" name="deals[settings][eyebrow]" value="<?php echo esc_attr($s['eyebrow']); ?>"></div>
                    <div class="dl-f"><span class="dl-l">Title (&lt;em&gt;word&lt;/em&gt; = gold)</span><input type="text" class="widefat" name="deals[settings][title]" value="<?php echo esc_attr($s['title']); ?>"></div>
                    <div class="dl-f"><span class="dl-l">Text</span><textarea class="widefat" rows="3" name="deals[settings][text]"><?php echo esc_textarea($s['text']); ?></textarea></div>
                    <div class="dl-f"><span class="dl-l">Default card badge</span><input type="text" class="widefat" name="deals[settings][badge]" value="<?php echo esc_attr($s['badge']); ?>"></div>
                    <div class="dl-f"><span class="dl-l">Text when no offer is running</span><textarea class="widefat" rows="2" name="deals[settings][empty_text]"><?php echo esc_textarea($s['empty_text']); ?></textarea></div>
                </div>

                <div class="dl-card">
                    <h2>Layout</h2>
                    <div class="dl-f"><span class="dl-l">Order of products</span>
                        <select name="deals[settings][sort]"><?php foreach (DealsSchema::sorts() as $k => $l) : ?><option value="<?php echo esc_attr($k); ?>"<?php selected($s['sort'], $k); ?>><?php echo esc_html($l); ?></option><?php endforeach; ?></select>
                    </div>
                    <div class="dl-f"><span class="dl-l">Cards per row (desktop)</span>
                        <select name="deals[settings][cols]"><?php foreach (['3', '4', '5'] as $c) : ?><option value="<?php echo esc_attr($c); ?>"<?php selected($s['cols'], $c); ?>><?php echo esc_html($c); ?></option><?php endforeach; ?></select>
                    </div>
                    <div class="dl-f"><span class="dl-l">Products before "আরও দেখুন"</span><input type="number" min="4" max="96" class="small-text" name="deals[settings][per_page]" value="<?php echo esc_attr((string) $s['per_page']); ?>"></div>
                    <label class="dl-switch"><input type="checkbox" name="deals[settings][countdown]" value="1"<?php checked($s['countdown']); ?>><span></span> Countdown (when an end time is set)</label>
                    <label class="dl-switch"><input type="checkbox" name="deals[settings][filters]" value="1"<?php checked($s['filters']); ?>><span></span> Category filter buttons</label>
                    <div class="dl-f"><span class="dl-l">Filter buttons (empty = the categories used in the rules)</span>
                        <select class="wc-enhanced-select" multiple name="deals[settings][chips][]" data-placeholder="e.g. Panjabi, Watch…" style="width:100%">
                            <?php foreach ($cats as $id => $label) : ?><option value="<?php echo esc_attr((string) $id); ?>"<?php echo in_array($id, $s['chips'], true) ? ' selected' : ''; ?>><?php echo esc_html($label); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <label class="dl-switch"><input type="checkbox" name="deals[settings][hide_out]" value="1"<?php checked($s['hide_out']); ?>><span></span> Hide out-of-stock products</label>
                </div>
            </div>
        </div>
    </form>
</div>
