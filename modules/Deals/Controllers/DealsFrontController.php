<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Deals\Controllers;

use Alaosaf\Modules\Atelier\AtelierSchema;
use Alaosaf\Modules\Atelier\AtelierView;
use Alaosaf\Modules\Deals\DealsPricing;
use Alaosaf\Modules\Deals\DealsSchema;

/**
 * [aa_offer_page] shortcode: every product that gets an offer price, in the Atelier card style,
 * with a countdown, category filter chips and "show more".
 */
class DealsFrontController {

    public const SHORTCODE = 'aa_offer_page';

    private bool $onPage = false;

    public function init(): void {
        add_shortcode(self::SHORTCODE, [$this, 'render']);
        add_action('wp', [$this, 'detect']);
        add_filter('aa_atelier_card_deal_label', [$this, 'cardLabel'], 10, 2);
    }

    public function detect(): void {
        if (!is_singular()) {
            return;
        }
        $post = get_queried_object();
        if (!$post instanceof \WP_Post) {
            return;
        }
        $found = has_shortcode($post->post_content, self::SHORTCODE)
            || strpos((string) get_post_meta($post->ID, '_elementor_data', true), self::SHORTCODE) !== false;
        if (!$found) {
            return;
        }
        $this->onPage = true;
        $palette = class_exists(AtelierSchema::class) && !empty(AtelierSchema::get(false)['settings']['palette']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue'], 20);
        add_filter('body_class', static function (array $c) use ($palette): array {
            $c[] = 'at-page';
            $c[] = 'aa-offer-page';
            if ($palette) {
                $c[] = 'at-palette';
            }
            return $c;
        });
        if (DealsPricing::preview()) {
            nocache_headers();
            add_filter('wp_robots', 'wp_robots_no_robots');
        }
    }

    public function enqueue(): void {
        $at = AA_PLUGIN_DIR . 'modules/Atelier/assets/';
        $atUrl = AA_PLUGIN_URL . 'modules/Atelier/assets/';
        $base = AA_PLUGIN_DIR . 'modules/Deals/assets/';
        $url = AA_PLUGIN_URL . 'modules/Deals/assets/';
        wp_enqueue_style('aa-atelier-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Manrope:wght@400;500;600;700&family=Noto+Serif+Bengali:wght@500;600&display=swap', [], null);
        wp_enqueue_style('aa-atelier', $atUrl . 'css/front.css', [], (string) filemtime($at . 'css/front.css'));
        wp_enqueue_script('aa-atelier', $atUrl . 'js/front.js', [], (string) filemtime($at . 'js/front.js'), true);
        wp_enqueue_style('aa-deals', $url . 'front.css', ['aa-atelier'], (string) filemtime($base . 'front.css'));
        wp_enqueue_script('aa-deals', $url . 'front.js', ['aa-atelier'], (string) filemtime($base . 'front.js'), true);
        wp_dequeue_style('swiper-css');
        wp_dequeue_script('swiper-js');
    }

    /** Badge on cards: the rule's own text, else the page default. */
    public function cardLabel($label, $product) {
        if (!$product instanceof \WC_Product || !(DealsSchema::isLive() || DealsPricing::preview())) {
            return $label;
        }
        $rule = DealsPricing::ruleFor($product);
        if (!$rule) {
            return $label;
        }
        return $rule['badge'] !== '' ? $rule['badge'] : (DealsSchema::get()['settings']['badge'] ?: $label);
    }

    /** Discount in % against the regular price (for variable products: the dearest variation, as on the card). */
    private function percent(\WC_Product $p): float {
        if ($p->is_type('variable')) {
            $reg = (float) $p->get_variation_regular_price('max');
            $now = (float) $p->get_variation_sale_price('max');
        } else {
            $reg = (float) $p->get_regular_price();
            $now = (float) $p->get_price();
        }
        return ($reg > 0 && $now > 0 && $now < $reg) ? (1 - $now / $reg) * 100 : 0.0;
    }

    /** @return \WC_Product[] */
    public function products(array $s): array {
        $map = DealsPricing::map();
        if (!$map) {
            return [];
        }
        $list = [];
        foreach (array_slice(array_keys($map), 0, 400) as $pid) {
            $p = wc_get_product($pid);
            if (!$p || $p->get_status() !== 'publish' || $p->get_catalog_visibility() === 'hidden') {
                continue;
            }
            if ($s['hide_out'] && !$p->is_in_stock()) {
                continue;
            }
            $pct = $this->percent($p);
            if ($pct <= 0) {
                continue; // a rule that does not lower this price (e.g. fixed price above the current one)
            }
            $list[] = ['p' => $p, 'pct' => $pct, 'rule' => $map[$pid]];
        }
        switch ($s['sort']) {
            case 'discount':
                usort($list, static fn($a, $b) => $b['pct'] <=> $a['pct']);
                break;
            case 'rule':
                usort($list, static fn($a, $b) => $a['rule'] <=> $b['rule']);
                break;
            case 'new':
                usort($list, static fn($a, $b) => $b['p']->get_id() <=> $a['p']->get_id());
                break;
            case 'low':
                usort($list, static fn($a, $b) => (float) $a['p']->get_price() <=> (float) $b['p']->get_price());
                break;
            case 'best':
                usort($list, static fn($a, $b) => (int) $b['p']->get_total_sales() <=> (int) $a['p']->get_total_sales());
                break;
        }
        return $list;
    }

    /**
     * Filter chip categories: the ones chosen on the settings page, else the categories the rules use.
     *
     * @return \WP_Term[]
     */
    private function chipTerms(array $doc): array {
        $ids = $doc['settings']['chips'];
        if (!$ids) {
            foreach ($doc['rules'] as $r) {
                if ($r['on'] && $r['target'] === 'cats') {
                    $ids = array_merge($ids, $r['cats']);
                }
            }
        }
        $out = [];
        foreach (array_unique($ids) as $id) {
            $t = get_term((int) $id, 'product_cat');
            if ($t instanceof \WP_Term) {
                $out[] = $t;
            }
        }
        return $out;
    }

    /** First chip category the product belongs to (directly or through a sub-category). */
    private function chipFor(\WC_Product $p, array $terms): ?\WP_Term {
        $own = [];
        foreach ($p->get_category_ids() as $cid) {
            $own[] = (int) $cid;
            foreach (get_ancestors((int) $cid, 'product_cat') as $a) {
                $own[] = (int) $a;
            }
        }
        foreach ($terms as $t) {
            if (in_array((int) $t->term_id, $own, true)) {
                return $t;
            }
        }
        return null;
    }

    public function render(): string {
        if (!function_exists('wc_get_product')) {
            return '';
        }
        $doc = DealsSchema::get();
        $s = $doc['settings'];
        $live = DealsSchema::isLive();
        $preview = !$live && DealsPricing::preview();
        $v = new AtelierView();
        $noDecimals = static fn() => 0;
        add_filter('wc_get_price_decimals', $noDecimals, 99);

        $items = ($live || $preview) ? $this->products($s) : [];
        $cats = [];
        $terms = $s['filters'] ? $this->chipTerms($doc) : [];
        foreach ($terms as $t) {
            $cats[$t->slug] = ['name' => $t->name, 'n' => 0];
        }
        foreach ($items as &$it) {
            $t = $terms ? $this->chipFor($it['p'], $terms) : null;
            $it['cat'] = $t ? $t->slug : '';
            if ($t) {
                $cats[$t->slug]['n']++;
            }
        }
        unset($it);
        $cats = array_filter($cats, static fn($c) => $c['n'] > 0); // chip order = the order chosen in settings
        $maxPct = $items ? (int) floor(max(array_column($items, 'pct'))) : 0;
        $ends = DealsSchema::ts($s['end']);

        ob_start();
        include AA_PLUGIN_DIR . 'modules/Deals/Views/front/page.php';
        remove_filter('wc_get_price_decimals', $noDecimals, 99);
        return (string) ob_get_clean();
    }
}
