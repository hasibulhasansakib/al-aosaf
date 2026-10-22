<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Atelier\Controllers;

use Alaosaf\Base\AbstractController;
use Alaosaf\Modules\Atelier\AtelierSchema;
use Alaosaf\Modules\Atelier\AtelierView;

/**
 * Al Aosaf → Homepage Builder: section list with order, on/off, fields, draft + publish.
 */
class AtelierAdminController extends AbstractController {

    private const NONCE = 'aa_atelier_save';

    public function init(): void {
        add_action('aa_admin_page_home-builder', [$this, 'renderPage']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue'], 20);
        add_action('wp_ajax_aa_atelier_save', [$this, 'ajaxSave']);
        add_action('admin_post_aa_atelier_import', [$this, 'import']);
        add_action('admin_post_aa_atelier_reset', [$this, 'reset']);
    }

    public function enqueue(string $hook): void {
        if (strpos($hook, 'aa-home-builder') === false) {
            return;
        }
        $base = AA_PLUGIN_DIR . 'modules/Atelier/assets/';
        $url  = AA_PLUGIN_URL . 'modules/Atelier/assets/';
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-sortable');
        if (wp_script_is('wc-enhanced-select', 'registered')) {
            wp_enqueue_script('wc-enhanced-select');
            wp_enqueue_style('woocommerce_admin_styles');
        }
        wp_enqueue_style('aa-atelier-admin', $url . 'css/admin.css', [], (string) filemtime($base . 'css/admin.css'));
        wp_enqueue_script('aa-atelier-admin', $url . 'js/admin.js', ['jquery', 'jquery-ui-sortable'], (string) filemtime($base . 'js/admin.js'), true);
        wp_localize_script('aa-atelier-admin', 'aaAtelier', [
            'ajax'  => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(self::NONCE),
        ]);
    }

    private function previewUrl(array $doc): string {
        $page = (int) ($doc['settings']['page'] ?? 0);
        if (!$page) {
            $page = $this->findPage();
        }
        return $page ? add_query_arg('aa_preview', '1', get_permalink($page)) : '';
    }

    /** First page that holds the shortcode. */
    private function findPage(): int {
        global $wpdb;
        $like = '%' . $wpdb->esc_like('[' . AtelierFrontController::SHORTCODE) . '%';
        $id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status IN ('publish','private','draft') AND post_content LIKE %s ORDER BY ID DESC LIMIT 1", $like));
        if (!$id) {
            $id = $wpdb->get_var($wpdb->prepare(
                "SELECT p.ID FROM {$wpdb->posts} p JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_elementor_data'
                 WHERE p.post_type = 'page' AND p.post_status IN ('publish','private','draft') AND m.meta_value LIKE %s ORDER BY p.ID DESC LIMIT 1", $like));
        }
        return (int) $id;
    }

    public function renderPage(): void {
        $this->requireCapability('manage_options');
        if (!is_array(get_option(AtelierSchema::OPTION_LIVE, null))) {
            // First visit: store the starting content so the live page and the editor agree.
            add_option(AtelierSchema::OPTION_LIVE, AtelierSchema::seed(), '', false);
        }
        $draft   = AtelierSchema::get(true);
        $live    = AtelierSchema::get(false);
        $changed = wp_json_encode($draft) !== wp_json_encode($live);
        $types   = AtelierSchema::types();
        $preview = $this->previewUrl($draft);
        $cats    = $this->categoryOptions();
        $notice  = isset($_GET['at_msg']) ? sanitize_key($_GET['at_msg']) : ''; // phpcs:ignore WordPress.Security.NonceVerification
        $renderer = $this;
        include AA_PLUGIN_DIR . 'modules/Atelier/Views/admin/builder.php';
    }

    /** id => indented name, for category selects. */
    private function categoryOptions(): array {
        if (!taxonomy_exists('product_cat')) {
            return [];
        }
        $terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'orderby' => 'name']);
        if (is_wp_error($terms)) {
            return [];
        }
        $byParent = [];
        foreach ($terms as $t) {
            $byParent[(int) $t->parent][] = $t;
        }
        $out = [];
        $walk = static function (int $parent, int $depth) use (&$walk, &$out, $byParent): void {
            foreach ($byParent[$parent] ?? [] as $t) {
                $out[(int) $t->term_id] = str_repeat('— ', $depth) . $t->name . ' (' . $t->count . ')';
                $walk((int) $t->term_id, $depth + 1);
            }
        };
        $walk(0, 0);
        return $out;
    }

    /* ------------------------------------------------------------------ field rendering (used by the view) */

    public function sectionPanel(string $type, array $sec, array $cats): void {
        $types = AtelierSchema::types();
        $def = $types[$type];
        $label = $def['label'];
        include AA_PLUGIN_DIR . 'modules/Atelier/Views/admin/section.php';
    }

    public function fields(array $fields, array $data, array $cats): void {
        echo '<div class="at-fields">';
        foreach ($fields as $key => $f) {
            $this->field($key, $f, array_key_exists($key, $data) ? $data[$key] : $f['default'], $cats);
        }
        echo '</div>';
    }

    public function field(string $key, array $f, $value, array $cats): void {
        $t = $f['type'];
        $wide = in_array($t, ['textarea', 'repeater', 'hotspots', 'products', 'categories'], true) ? ' at-f--wide' : '';
        echo '<div class="at-f at-f--' . esc_attr($t) . $wide . '">';
        if ($t !== 'toggle') {
            echo '<label class="at-f__l">' . esc_html($f['label']) . '</label>';
        }
        $attr = 'data-k="' . esc_attr($key) . '" data-t="' . esc_attr($t) . '"';
        switch ($t) {
            case 'text':
            case 'url':
                echo '<input type="text" class="widefat" ' . $attr . ' value="' . esc_attr((string) $value) . '"' . ($t === 'url' ? ' placeholder="https://… or #anchor"' : '') . '>';
                break;
            case 'textarea':
                echo '<textarea class="widefat" rows="3" ' . $attr . '>' . esc_textarea((string) $value) . '</textarea>';
                break;
            case 'number':
                echo '<input type="number" class="small-text" ' . $attr . ' value="' . esc_attr((string) $value) . '"'
                    . (isset($f['min']) ? ' min="' . esc_attr((string) $f['min']) . '"' : '') . (isset($f['max']) ? ' max="' . esc_attr((string) $f['max']) . '"' : '')
                    . ' step="' . esc_attr((string) ($f['step'] ?? 1)) . '">';
                break;
            case 'datetime':
                echo '<input type="datetime-local" ' . $attr . ' value="' . esc_attr((string) $value) . '">';
                break;
            case 'toggle':
                echo '<label class="at-switch"><input type="checkbox" ' . $attr . ($value ? ' checked' : '') . '><span></span>' . esc_html($f['label']) . '</label>';
                break;
            case 'select':
                echo '<select ' . $attr . '>';
                foreach ($f['options'] as $k => $l) {
                    echo '<option value="' . esc_attr((string) $k) . '"' . selected((string) $value, (string) $k, false) . '>' . esc_html($l) . '</option>';
                }
                echo '</select>';
                break;
            case 'page':
                echo '<select ' . $attr . '><option value="0">— auto (page with the shortcode) —</option>';
                foreach (get_pages(['post_status' => 'publish,private,draft']) as $p) {
                    echo '<option value="' . esc_attr((string) $p->ID) . '"' . selected((int) $value, (int) $p->ID, false) . '>' . esc_html($p->post_title . ' (#' . $p->ID . ')') . '</option>';
                }
                echo '</select>';
                break;
            case 'category':
            case 'categories':
                $multi = $t === 'categories';
                $vals = array_map('intval', (array) $value);
                echo '<select class="at-select wc-enhanced-select" ' . $attr . ($multi ? ' multiple' : '') . ' data-placeholder="Choose…" style="width:100%">';
                if (!$multi) echo '<option value="0">—</option>';
                foreach ($cats as $id => $name) {
                    echo '<option value="' . esc_attr((string) $id) . '"' . (in_array($id, $vals, true) ? ' selected' : '') . '>' . esc_html($name) . '</option>';
                }
                echo '</select>';
                break;
            case 'product':
            case 'products':
                $multi = $t === 'products';
                echo '<select class="at-select wc-product-search" ' . $attr . ($multi ? ' multiple' : '') . ' data-action="woocommerce_json_search_products" data-placeholder="Search products…" data-allow_clear="true" style="width:100%">';
                foreach (array_filter(array_map('intval', (array) $value)) as $pid) {
                    $p = function_exists('wc_get_product') ? wc_get_product($pid) : null;
                    if ($p) echo '<option value="' . esc_attr((string) $pid) . '" selected>' . esc_html(wp_strip_all_tags($p->get_formatted_name())) . '</option>';
                }
                echo '</select>';
                break;
            case 'image':
                $src = $value ? wp_get_attachment_image_url((int) $value, 'medium') : '';
                echo '<div class="at-img' . ($src ? ' has-img' : '') . '"><input type="hidden" ' . $attr . ' value="' . esc_attr((string) (int) $value) . '">'
                    . '<span class="at-img__prev">' . ($src ? '<img src="' . esc_url($src) . '" alt="">' : '') . '</span>'
                    . '<button type="button" class="button at-img__pick">Choose image</button> <button type="button" class="button-link at-img__clear">Remove</button></div>';
                break;
            case 'repeater':
            case 'hotspots':
                echo '<div class="at-rep" ' . $attr . ($t === 'hotspots' ? ' data-image="' . esc_attr($f['image']) . '"' : '') . '>';
                if ($t === 'hotspots') {
                    echo '<div class="at-stage"><p class="at-stage__empty">Choose the photo above first. Then click on it to add a dot.</p></div>';
                }
                echo '<div class="at-items">';
                foreach ((array) $value as $item) {
                    $this->repItem($f, (array) $item, $cats);
                }
                echo '</div>';
                echo '<template class="at-item-tpl">';
                $empty = [];
                foreach ($f['fields'] as $k => $sf) $empty[$k] = $sf['default'];
                $this->repItem($f, $empty, $cats);
                echo '</template>';
                echo '<button type="button" class="button at-rep__add">+ Add ' . esc_html(strtolower($f['item'] ?? 'item')) . '</button>';
                echo '</div>';
                break;
        }
        echo '</div>';
    }

    private function repItem(array $f, array $item, array $cats): void {
        $title = '';
        foreach (['title', 'text', 'label', 'q', 'name'] as $k) {
            if (!empty($item[$k]) && is_string($item[$k])) { $title = wp_strip_all_tags(str_replace('<br>', ' ', $item[$k])); break; }
        }
        echo '<div class="at-item" data-scope>';
        echo '<div class="at-item__bar"><span class="at-item__drag dashicons dashicons-menu" title="Drag to reorder"></span><span class="at-item__title">'
            . esc_html($title !== '' ? $title : ($f['item'] ?? 'Item')) . '</span>'
            . '<button type="button" class="button-link at-item__dup" title="Duplicate"><span class="dashicons dashicons-admin-page"></span></button>'
            . '<button type="button" class="button-link at-item__del" title="Remove"><span class="dashicons dashicons-trash"></span></button>'
            . '<button type="button" class="button-link at-item__tog" title="Open / close"><span class="dashicons dashicons-arrow-down-alt2"></span></button></div>';
        echo '<div class="at-item__body">';
        $this->fields($f['fields'], $item, $cats);
        echo '</div></div>';
    }

    /* ------------------------------------------------------------------ saving */

    public function ajaxSave(): void {
        if (!current_user_can('manage_options') || !check_ajax_referer(self::NONCE, 'nonce', false)) {
            wp_send_json_error(['message' => 'Not allowed'], 403);
        }
        $raw = json_decode(wp_unslash((string) ($_POST['doc'] ?? '')), true);
        if (!is_array($raw)) {
            wp_send_json_error(['message' => 'The data could not be read.'], 400);
        }
        $doc = AtelierSchema::sanitizeDocument($raw);
        $mode = sanitize_key((string) ($_POST['mode'] ?? 'draft'));
        update_option(AtelierSchema::OPTION_DRAFT, $doc, false);
        if ($mode === 'publish') {
            update_option(AtelierSchema::OPTION_LIVE, $doc, false);
            AtelierView::flushCache();
            $this->purgePageCache();
        }
        wp_send_json_success([
            'mode'    => $mode,
            'preview' => $this->previewUrl($doc),
            'changed' => wp_json_encode($doc) !== wp_json_encode(AtelierSchema::get(false)),
            'time'    => wp_date('H:i'),
        ]);
    }

    /** Clear common page caches after publishing, when such a plugin is present. */
    private function purgePageCache(): void {
        if (function_exists('rocket_clean_domain')) rocket_clean_domain();
        if (function_exists('w3tc_flush_all')) w3tc_flush_all();
        if (function_exists('wp_cache_clear_cache')) wp_cache_clear_cache();
        do_action('litespeed_purge_all');
    }

    public function import(): void {
        $this->requireCapability('manage_options');
        check_admin_referer('aa_atelier_import');
        $raw = json_decode(wp_unslash((string) ($_POST['json'] ?? '')), true);
        $msg = 'bad';
        if (is_array($raw) && isset($raw['sections'])) {
            update_option(AtelierSchema::OPTION_DRAFT, AtelierSchema::sanitizeDocument($raw), false);
            $msg = 'imported';
        }
        wp_safe_redirect(admin_url('admin.php?page=aa-home-builder&at_msg=' . $msg));
        exit;
    }

    public function reset(): void {
        $this->requireCapability('manage_options');
        check_admin_referer('aa_atelier_reset');
        $what = sanitize_key((string) ($_POST['what'] ?? ''));
        if ($what === 'seed') {
            update_option(AtelierSchema::OPTION_DRAFT, AtelierSchema::seed(), false);
        } else {
            delete_option(AtelierSchema::OPTION_DRAFT);
        }
        wp_safe_redirect(admin_url('admin.php?page=aa-home-builder&at_msg=' . ($what === 'seed' ? 'seeded' : 'discarded')));
        exit;
    }
}
