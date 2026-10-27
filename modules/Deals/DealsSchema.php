<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Deals;

/**
 * Offer page settings + discount rules, stored in one option.
 *
 * A rule picks products (by hand or by category), takes a discount
 * (percent / taka off / fixed price) and an optional badge text.
 * When products match more than one rule, the rule higher in the list wins.
 */
class DealsSchema {

    public const OPTION = 'aa_deals';

    public static function defaults(): array {
        return [
            'settings' => [
                'enabled'    => 0,
                'page'       => 0,
                'start'      => '',
                'end'        => '',
                'eyebrow'    => 'Limited Offer',
                'title'      => 'Special <em>Offers</em>',
                'text'       => 'বাছাই করা পণ্যে বিশেষ ছাড়। স্টক থাকা পর্যন্ত এই দাম, তাই দেরি না করে অর্ডার করে ফেলুন।',
                'badge'      => 'অফার',
                'countdown'  => 1,
                'filters'    => 1,
                'chips'      => [],
                'hide_out'   => 1,
                'sort'       => 'discount',
                'per_page'   => 24,
                'cols'       => '4',
                'empty_text' => 'এই মুহূর্তে কোনো অফার চলছে না। নতুন অফারের জন্য আমাদের পেজে চোখ রাখুন।',
            ],
            'rules' => [],
            'v'     => '',
        ];
    }

    public static function rule(): array {
        return [
            'id'      => '',
            'on'      => 1,
            'name'    => '',
            'badge'   => '',
            'target'  => 'products',
            'ids'     => [],
            'cats'    => [],
            'exclude' => [],
            'type'    => 'percent',
            'value'   => 10,
            'base'    => 'current',
        ];
    }

    public static function types(): array {
        return [
            'percent' => '% ছাড় (percent off)',
            'amount'  => '৳ ছাড় (taka off)',
            'price'   => 'Fixed price (৳)',
        ];
    }

    public static function sorts(): array {
        return [
            'discount' => 'Biggest discount first',
            'rule'     => 'Rule order',
            'new'      => 'Newest first',
            'low'      => 'Price: low to high',
            'best'     => 'Best sellers first',
        ];
    }

    private static ?array $cache = null;

    public static function get(): array {
        if (self::$cache === null) {
            $raw = get_option(self::OPTION, []);
            self::$cache = self::sanitize(is_array($raw) ? $raw : [], false);
        }
        return self::$cache;
    }

    public static function save(array $in): array {
        $doc = self::sanitize($in, true);
        $doc['v'] = (string) time();
        update_option(self::OPTION, $doc, true);
        self::$cache = $doc;
        return $doc;
    }

    public static function sanitize(array $in, bool $fromForm): array {
        $def = self::defaults();
        $s = array_merge($def['settings'], (array) ($in['settings'] ?? []));
        $out = ['settings' => [
            'enabled'    => empty($s['enabled']) ? 0 : 1,
            'page'       => absint($s['page']),
            'start'      => self::dt((string) $s['start']),
            'end'        => self::dt((string) $s['end']),
            'eyebrow'    => sanitize_text_field((string) $s['eyebrow']),
            'title'      => wp_kses((string) $s['title'], ['em' => [], 'br' => [], 'strong' => []]),
            'text'       => sanitize_textarea_field((string) $s['text']),
            'badge'      => sanitize_text_field((string) $s['badge']),
            'countdown'  => empty($s['countdown']) ? 0 : 1,
            'filters'    => empty($s['filters']) ? 0 : 1,
            'chips'      => array_values(array_unique(array_filter(array_map('absint', (array) $s['chips'])))),
            'hide_out'   => empty($s['hide_out']) ? 0 : 1,
            'sort'       => array_key_exists((string) $s['sort'], self::sorts()) ? (string) $s['sort'] : 'discount',
            'per_page'   => max(4, min(96, (int) $s['per_page'])),
            'cols'       => in_array((string) $s['cols'], ['3', '4', '5'], true) ? (string) $s['cols'] : '4',
            'empty_text' => sanitize_textarea_field((string) $s['empty_text']),
        ], 'rules' => [], 'v' => (string) ($in['v'] ?? '')];

        foreach ((array) ($in['rules'] ?? []) as $r) {
            if (!is_array($r)) {
                continue;
            }
            $r = array_merge(self::rule(), $r);
            $ints = static fn($a) => array_values(array_unique(array_filter(array_map('absint', (array) $a))));
            $rule = [
                'id'      => preg_replace('/[^a-z0-9]/i', '', (string) $r['id']) ?: 'r' . wp_generate_password(6, false, false),
                'on'      => empty($r['on']) ? 0 : 1,
                'name'    => sanitize_text_field((string) $r['name']),
                'badge'   => sanitize_text_field((string) $r['badge']),
                'target'  => in_array($r['target'], ['products', 'cats'], true) ? $r['target'] : 'products',
                'ids'     => $ints($r['ids']),
                'cats'    => $ints($r['cats']),
                'exclude' => $ints($r['exclude']),
                'type'    => array_key_exists((string) $r['type'], self::types()) ? (string) $r['type'] : 'percent',
                'value'   => max(0, round((float) $r['value'], 2)),
                'base'    => $r['base'] === 'regular' ? 'regular' : 'current',
            ];
            if ($rule['type'] === 'percent') {
                $rule['value'] = min(95, $rule['value']);
            }
            $out['rules'][] = $rule;
        }
        return $out;
    }

    /** "2026-11-01T10:00" (site time) or empty. */
    private static function dt(string $v): string {
        $v = trim($v);
        return preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $v) ? $v : '';
    }

    public static function ts(string $v): int {
        if ($v === '') {
            return 0;
        }
        try {
            return (new \DateTimeImmutable($v, wp_timezone()))->getTimestamp();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /** The offer is switched on and today is inside its dates. */
    public static function isLive(?array $doc = null): bool {
        $s = ($doc ?? self::get())['settings'];
        if (!$s['enabled']) {
            return false;
        }
        $now = time();
        $from = self::ts($s['start']);
        $to = self::ts($s['end']);
        return (!$from || $now >= $from) && (!$to || $now < $to);
    }

    /** Page that shows the offer: the chosen one, else the first page with the shortcode. */
    public static function pageId(): int {
        $id = self::get()['settings']['page'];
        if ($id && get_post_status($id)) {
            return $id;
        }
        global $wpdb;
        $like = '%' . $wpdb->esc_like('[' . Controllers\DealsFrontController::SHORTCODE) . '%';
        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status IN ('publish','private','draft') AND post_content LIKE %s ORDER BY ID DESC LIMIT 1", $like));
    }
}
