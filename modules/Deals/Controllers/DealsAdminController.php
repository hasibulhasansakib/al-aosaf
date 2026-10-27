<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Deals\Controllers;

use Alaosaf\Base\AbstractController;
use Alaosaf\Modules\Deals\DealsPricing;
use Alaosaf\Modules\Deals\DealsSchema;

/**
 * Al Aosaf → Offer Page: offer on/off + dates, discount rules, page texts, and a price preview.
 */
class DealsAdminController extends AbstractController {

    private const NONCE = 'aa_deals_save';

    public function init(): void {
        add_action('aa_admin_page_offers', [$this, 'renderPage']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue'], 20);
        add_action('admin_post_aa_deals_save', [$this, 'save']);
        add_action('admin_post_aa_deals_page', [$this, 'createPage']);
    }

    public function enqueue(string $hook): void {
        if (strpos($hook, 'aa-offers') === false) {
            return;
        }
        $base = AA_PLUGIN_DIR . 'modules/Deals/assets/';
        $url = AA_PLUGIN_URL . 'modules/Deals/assets/';
        wp_enqueue_script('jquery-ui-sortable');
        if (wp_script_is('wc-enhanced-select', 'registered')) {
            wp_enqueue_script('wc-enhanced-select');
            wp_enqueue_style('woocommerce_admin_styles');
        }
        wp_enqueue_style('aa-deals-admin', $url . 'admin.css', [], (string) filemtime($base . 'admin.css'));
        wp_enqueue_script('aa-deals-admin', $url . 'admin.js', ['jquery', 'jquery-ui-sortable'], (string) filemtime($base . 'admin.js'), true);
    }

    public function renderPage(): void {
        $this->requireCapability('manage_options');
        $doc = DealsSchema::get();
        $s = $doc['settings'];
        $pageId = DealsSchema::pageId();
        $cats = $this->categoryOptions();
        $status = $this->status($s);
        $preview = $this->previewRows($doc);
        $msg = isset($_GET['aa_msg']) ? sanitize_key($_GET['aa_msg']) : ''; // phpcs:ignore WordPress.Security.NonceVerification
        $nonce = self::NONCE;
        $ctl = $this;
        include AA_PLUGIN_DIR . 'modules/Deals/Views/admin/page.php';
    }

    /** @return array{0:string,1:string} [css class, text] */
    private function status(array $s): array {
        $from = DealsSchema::ts($s['start']);
        $to = DealsSchema::ts($s['end']);
        $now = time();
        if (!$s['enabled']) {
            return ['off', 'Off: offer prices are not used'];
        }
        if ($to && $now >= $to) {
            return ['ended', 'Ended on ' . wp_date('j M Y, g:i a', $to)];
        }
        if ($from && $now < $from) {
            return ['wait', 'Scheduled: starts ' . wp_date('j M Y, g:i a', $from)];
        }
        return ['live', 'LIVE now' . ($to ? ' · ends ' . wp_date('j M Y, g:i a', $to) : '')];
    }

    /** Products each rule catches, with old and new price (first 60), using the saved rules. */
    private function previewRows(array $doc): array {
        if (!function_exists('wc_get_product')) {
            return ['counts' => [], 'rows' => [], 'total' => 0];
        }
        DealsPricing::reset();
        $map = DealsPricing::map();
        $counts = array_count_values($map);
        $rows = [];
        foreach (array_slice($map, 0, 60, true) as $pid => $ri) {
            $p = wc_get_product($pid);
            if (!$p) {
                continue;
            }
            $rule = $doc['rules'][$ri];
            $ref = $p;
            if ($p->is_type('variable')) {
                $kids = $p->get_children();
                $ref = $kids ? wc_get_product($kids[0]) : null;
            }
            $reg = $ref ? (float) $ref->get_regular_price('edit') : 0;
            $sale = $ref ? $ref->get_sale_price('edit') : '';
            $now = ($ref && $sale !== '' && $ref->is_on_sale('edit')) ? (float) $sale : $reg;
            $new = $ref ? DealsPricing::discounted($ref) : null;
            $rows[] = [
                'id' => $pid, 'name' => $p->get_name(), 'edit' => get_edit_post_link($pid, 'raw'),
                'rule' => $rule['name'] !== '' ? $rule['name'] : 'Rule ' . ($ri + 1),
                'reg' => $reg, 'now' => $now, 'new' => $new, 'var' => $p->is_type('variable'),
                'stock' => $p->is_in_stock(),
            ];
        }
        return ['counts' => $counts, 'rows' => $rows, 'total' => count($map)];
    }

    /** id => indented name. */
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

    /** Selected products as <option>s for a product search box. */
    public function productOptions(array $ids): string {
        $html = '';
        foreach ($ids as $pid) {
            $p = wc_get_product($pid);
            if ($p) {
                $html .= '<option value="' . esc_attr((string) $pid) . '" selected>' . esc_html(wp_strip_all_tags($p->get_formatted_name())) . '</option>';
            }
        }
        return $html;
    }

    public function save(): void {
        $this->requireCapability('manage_options');
        $this->verifyNonce((string) ($_POST['_wpnonce'] ?? ''), self::NONCE);
        $in = wp_unslash($_POST['deals'] ?? []);
        $in = is_array($in) ? $in : [];
        $in['rules'] = array_values((array) ($in['rules'] ?? []));
        $doc = DealsSchema::save($in);
        DealsPricing::reset();
        $this->schedule($doc['settings']);
        self::purge();
        wp_safe_redirect(add_query_arg(['page' => 'aa-offers', 'aa_msg' => 'saved'], admin_url('admin.php')));
        exit;
    }

    /** Clear page caches again when the offer starts and when it ends, so nobody sees old prices. */
    private function schedule(array $s): void {
        wp_clear_scheduled_hook('aa_deals_flip');
        foreach ([DealsSchema::ts($s['start']), DealsSchema::ts($s['end'])] as $ts) {
            if ($ts > time()) {
                wp_schedule_single_event($ts + 5, 'aa_deals_flip', [$ts]);
            }
        }
    }

    public static function purge(): void {
        do_action('litespeed_purge_all');
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }
        wp_cache_flush();
    }

    public function createPage(): void {
        $this->requireCapability('manage_options');
        $this->verifyNonce((string) ($_GET['_wpnonce'] ?? ''), 'aa_deals_page');
        $id = DealsSchema::pageId();
        if (!$id) {
            $id = (int) wp_insert_post([
                'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Offer', 'post_name' => 'offer',
                'post_content' => '[' . DealsFrontController::SHORTCODE . ']',
            ]);
            if ($id && class_exists('\Elementor\Plugin')) {
                update_post_meta($id, '_wp_page_template', 'elementor_header_footer');
            }
        }
        if ($id) {
            $doc = DealsSchema::get();
            $doc['settings']['page'] = $id;
            DealsSchema::save($doc);
        }
        wp_safe_redirect(add_query_arg(['page' => 'aa-offers', 'aa_msg' => $id ? 'page' : 'nopage'], admin_url('admin.php')));
        exit;
    }
}
