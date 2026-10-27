<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Deals;

/**
 * Applies the offer rules to WooCommerce prices while the offer is live, so the
 * same discounted price shows on the offer page, the product page, the cart and the order.
 * Product data is never changed: switch the offer off and every price is back to normal.
 */
class DealsPricing {

    /** @var array<int,int>|null product id => rule index */
    private static ?array $map = null;
    /** @var array<int,float|null> */
    private static array $memo = [];

    public function init(): void {
        foreach (['woocommerce_product_get_price', 'woocommerce_product_variation_get_price'] as $h) {
            add_filter($h, [$this, 'price'], 50, 2);
        }
        foreach (['woocommerce_product_get_sale_price', 'woocommerce_product_variation_get_sale_price'] as $h) {
            add_filter($h, [$this, 'salePrice'], 50, 2);
        }
        add_filter('woocommerce_variation_prices_price', [$this, 'variationPrice'], 50, 3);
        add_filter('woocommerce_variation_prices_sale_price', [$this, 'variationPrice'], 50, 3);
        add_filter('woocommerce_get_variation_prices_hash', [$this, 'hash'], 50);
        add_filter('woocommerce_product_is_on_sale', [$this, 'onSale'], 50, 2);
    }

    /** Prices are only changed for shoppers: wp-admin product screens keep the real prices. */
    private static function active(): bool {
        if (is_admin() && !wp_doing_ajax()) {
            return false;
        }
        return DealsSchema::isLive() || self::preview();
    }

    /** Admin preview of an offer that is off or not started yet: ?aa_preview=1 on the offer page. */
    public static function preview(): bool {
        return isset($_GET['aa_preview']) && did_action('init') && current_user_can('manage_options'); // phpcs:ignore WordPress.Security.NonceVerification
    }

    public static function reset(): void {
        self::$map = null;
        self::$memo = [];
    }

    /** @return array<int,int> */
    public static function map(): array {
        if (self::$map !== null) {
            return self::$map;
        }
        $doc = DealsSchema::get();
        $key = 'aa_deals_map_' . md5($doc['v'] . '|' . get_option('aa_atelier_qv', ''));
        $cached = get_transient($key);
        if (is_array($cached)) {
            return self::$map = $cached;
        }
        $map = [];
        foreach ($doc['rules'] as $i => $r) {
            if (!$r['on'] || $r['value'] <= 0) {
                continue;
            }
            $ids = $r['target'] === 'cats' ? self::categoryProducts($r['cats']) : $r['ids'];
            foreach (array_diff($ids, $r['exclude']) as $pid) {
                if (!isset($map[$pid])) {
                    $map[$pid] = $i;
                }
            }
        }
        set_transient($key, $map, 12 * HOUR_IN_SECONDS);
        return self::$map = $map;
    }

    /** @return int[] */
    private static function categoryProducts(array $cats): array {
        if (!$cats) {
            return [];
        }
        return array_map('intval', (array) get_posts([
            'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true,
            'tax_query' => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $cats, 'include_children' => true]],
        ]));
    }

    public static function ruleFor(\WC_Product $p): ?array {
        $pid = $p->get_parent_id() ?: $p->get_id();
        $map = self::map();
        if (!isset($map[$pid])) {
            return null;
        }
        return DealsSchema::get()['rules'][$map[$pid]] ?? null;
    }

    /** Offer price for a simple product or a variation, or null when it gets no discount. */
    public static function discounted(\WC_Product $p): ?float {
        if ($p->is_type('variable') || $p->is_type('grouped')) {
            return null;
        }
        $id = $p->get_id();
        if (array_key_exists($id, self::$memo)) {
            return self::$memo[$id];
        }
        $rule = self::ruleFor($p);
        $out = null;
        if ($rule) {
            $regular = (float) $p->get_regular_price('edit');
            $sale = $p->get_sale_price('edit');
            $base = ($rule['base'] === 'current' && $sale !== '' && $p->is_on_sale('edit')) ? (float) $sale : $regular;
            if ($base > 0) {
                $out = self::apply($base, $rule);
            }
        }
        return self::$memo[$id] = $out;
    }

    public static function apply(float $base, array $rule): ?float {
        $v = (float) $rule['value'];
        switch ($rule['type']) {
            case 'percent':
                $new = $base * (1 - $v / 100);
                break;
            case 'amount':
                $new = $base - $v;
                break;
            default:
                $new = $v;
        }
        $new = round($new); // taka prices are whole numbers
        return ($new > 0 && $new < $base) ? (float) $new : null;
    }

    /** A price that something else set on purpose (a landing page offer in the cart) is left alone. */
    private static function setAtRuntime(\WC_Product $p): bool {
        return array_key_exists('price', $p->get_changes());
    }

    public function price($price, $product) {
        if (!$product instanceof \WC_Product || !self::active() || self::setAtRuntime($product)) {
            return $price;
        }
        $d = self::discounted($product);
        return $d === null ? $price : (string) $d;
    }

    public function salePrice($price, $product) {
        if (!$product instanceof \WC_Product || !self::active()) {
            return $price;
        }
        $d = self::discounted($product);
        return $d === null ? $price : (string) $d;
    }

    public function variationPrice($price, $variation, $product) {
        if (!$variation instanceof \WC_Product || !self::active()) {
            return $price;
        }
        $d = self::discounted($variation);
        return $d === null ? $price : (string) $d;
    }

    public function hash($hash) {
        $hash = (array) $hash;
        $hash[] = 'aa_deals_' . (self::active() ? DealsSchema::get()['v'] : 'off');
        return $hash;
    }

    public function onSale($on, $product) {
        if ($on || !$product instanceof \WC_Product || !self::active()) {
            return $on;
        }
        return self::discounted($product) !== null ? true : $on;
    }
}
