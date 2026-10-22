<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Atelier;

/**
 * Helpers shared by the section views: images, product queries, cards, icons.
 */
class AtelierView {

    private const CACHE_KEY = 'aa_atelier_qv';

    /** Bumped whenever products change, so cached product lists refresh. */
    public static function flushCache(): void {
        update_option(self::CACHE_KEY, (string) time(), false);
    }

    public function rich(string $s): string {
        return wp_kses($s, ['em' => [], 'br' => [], 'strong' => []]);
    }

    public function img(int $id, string $size = 'large', array $attr = []): string {
        if (!$id) {
            return '';
        }
        $attr += ['loading' => 'lazy', 'decoding' => 'async'];
        return (string) wp_get_attachment_image($id, $size, false, $attr);
    }

    /** Small label above a title. Bangla text gets no letter-spacing (it would break the conjuncts). */
    public function eyebrow(string $text, string $class = ''): string {
        if ($text === '') {
            return '';
        }
        $cls = trim('at-eyebrow ' . $class . (preg_match('/\p{Bengali}/u', $text) ? ' is-bn' : ''));
        return '<p class="' . esc_attr($cls) . '">' . esc_html($text) . '</p>';
    }

    /** Eyebrow + title + text block used by most sections. */
    public function heading(array $d, string $extra = '', string $tag = 'h2'): void {
        if (empty($d['eyebrow']) && empty($d['title']) && empty($d['text'])) {
            return;
        }
        echo '<header class="at-head" data-reveal>';
        echo '<div class="at-head__main">';
        echo $this->eyebrow((string) ($d['eyebrow'] ?? ''));
        if (!empty($d['title'])) {
            echo '<' . $tag . ' class="at-title">' . $this->rich($d['title']) . '</' . $tag . '>';
        }
        if (!empty($d['text'])) {
            echo '<p class="at-lead">' . nl2br(esc_html($d['text'])) . '</p>';
        }
        echo '</div>';
        echo $extra; // already escaped by the caller
        echo '</header>';
    }

    public function link(string $url): string {
        if ($url === '') {
            return '';
        }
        return $url[0] === '#' ? esc_attr($url) : esc_url($url);
    }

    /* ------------------------------------------------------------------ products */

    /**
     * Product IDs for a source setting. Lists are cached until a product changes.
     *
     * @return int[]
     */
    public function productIds(string $source, array $cats, array $ids, int $limit, bool $instock): array {
        if (!function_exists('wc_get_product')) {
            return [];
        }
        $key = 'aa_at_' . md5(wp_json_encode([$source, $cats, $ids, $limit, $instock, get_option(self::CACHE_KEY, '')]));
        $cached = get_transient($key);
        if (is_array($cached)) {
            return $cached;
        }

        $q = [
            'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => $limit,
            'fields' => 'ids', 'no_found_rows' => true, 'ignore_sticky_posts' => true,
            'tax_query' => ['relation' => 'AND'],
        ];
        $hidden = ['exclude-from-catalog'];
        if ($instock) {
            $hidden[] = 'outofstock';
        }
        $q['tax_query'][] = ['taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => $hidden, 'operator' => 'NOT IN'];

        switch ($source) {
            case 'best':
                $q['meta_key'] = 'total_sales';
                $q['orderby'] = ['meta_value_num' => 'DESC', 'date' => 'DESC'];
                break;
            case 'sale':
                $sale = function_exists('wc_get_product_ids_on_sale') ? wc_get_product_ids_on_sale() : [];
                $q['post__in'] = $sale ?: [0];
                $q['orderby'] = 'date';
                break;
            case 'cat':
                if ($cats) {
                    $q['tax_query'][] = ['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $cats, 'include_children' => true];
                }
                $q['orderby'] = 'date';
                break;
            case 'pick':
                $q['post__in'] = $ids ?: [0];
                $q['orderby'] = 'post__in';
                break;
            default:
                $q['orderby'] = 'date';
        }

        $found = array_map('intval', (array) get_posts($q));
        set_transient($key, $found, 6 * HOUR_IN_SECONDS);
        return $found;
    }

    /** @return \WC_Product[] */
    public function products(array $d, int $limit = 0): array {
        $ids = $this->productIds(
            (string) ($d['source'] ?? 'new'),
            (array) ($d['cats'] ?? []),
            (array) ($d['ids'] ?? []),
            $limit ?: (int) ($d['limit'] ?? 8),
            !empty($d['instock'])
        );
        return array_values(array_filter(array_map('wc_get_product', $ids)));
    }

    public function card(\WC_Product $product, bool $eager = false): void {
        include __DIR__ . '/Views/front/card.php';
    }

    /** Category image: custom thumbnail, else the photo of its newest product. */
    public function categoryImage(int $termId): int {
        $thumb = (int) get_term_meta($termId, 'thumbnail_id', true);
        if ($thumb && wp_attachment_is_image($thumb)) {
            return $thumb;
        }
        $ids = $this->productIds('cat', [$termId], [], 1, true);
        return $ids ? (int) get_post_thumbnail_id($ids[0]) : 0;
    }

    /* ------------------------------------------------------------------ reviews */

    /** Approved WooCommerce reviews with a rating, newest first. */
    public function storeReviews(int $minStars, int $limit = 12): array {
        if (!post_type_exists('product')) {
            return [];
        }
        $comments = get_comments([
            'post_type' => 'product', 'status' => 'approve', 'type' => 'review', 'number' => $limit,
            'meta_query' => [['key' => 'rating', 'value' => $minStars, 'compare' => '>=', 'type' => 'NUMERIC']],
        ]);
        $out = [];
        foreach ($comments as $c) {
            $out[] = [
                'name' => $c->comment_author,
                'place' => get_the_title((int) $c->comment_post_ID),
                'text' => wp_strip_all_tags($c->comment_content),
                'rating' => (int) get_comment_meta($c->comment_ID, 'rating', true),
                'image' => 0,
                'verified' => (bool) get_comment_meta($c->comment_ID, 'verified', true),
            ];
        }
        return $out;
    }

    /* ------------------------------------------------------------------ icons */

    public function icon(string $name): string {
        $p = [
            'shield' => '<path d="M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6l7-3z"/><path d="M9 12l2 2 4-4"/>',
            'cash'   => '<rect x="2.5" y="6" width="19" height="12" rx="1.5"/><circle cx="12" cy="12" r="2.6"/><path d="M6 9.5v5M18 9.5v5"/>',
            'truck'  => '<path d="M2.5 6.5h11v9h-11zM13.5 9.5h4l3 3v3h-7"/><circle cx="6.5" cy="17.5" r="1.6"/><circle cx="16.5" cy="17.5" r="1.6"/>',
            'eye'    => '<path d="M2 12s3.6-6.5 10-6.5S22 12 22 12s-3.6 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="2.8"/>',
            'return' => '<path d="M4 9h11a5 5 0 010 10H9"/><path d="M8 5L4 9l4 4"/>',
            'star'   => '<path d="M12 3.5l2.6 5.4 5.9.8-4.3 4.1 1 5.8L12 16.9l-5.2 2.7 1-5.8-4.3-4.1 5.9-.8z"/>',
            'chat'   => '<path d="M4 5h16v11H9l-5 4z"/><path d="M8 10h8M8 13h5"/>',
            'gift'   => '<rect x="3.5" y="8.5" width="17" height="12"/><path d="M2.5 8.5h19M12 8.5v12M12 8.5S10.5 4 8 4.5 7 8.5 12 8.5zM12 8.5S13.5 4 16 4.5s1 4-4 4z"/>',
            'tag'    => '<path d="M3 12V4h8l10 10-8 8L3 12z"/><circle cx="7.5" cy="8" r="1.4"/>',
            'clock'  => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
            'arrow'  => '<path d="M5 12h14M13 6l6 6-6 6"/>',
            'left'   => '<path d="M15 5l-7 7 7 7"/>',
            'right'  => '<path d="M9 5l7 7-7 7"/>',
            'bag'    => '<path d="M5 8h14l-1 12H6L5 8z"/><path d="M9 8V6.5a3 3 0 016 0V8"/>',
            'heart'  => '<path d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 00-7.8 7.8l1 1.1L12 21.2l7.8-7.8 1-1.1a5.5 5.5 0 000-7.7z"/>',
            'plus'   => '<path d="M12 5v14M5 12h14"/>',
            'phone'  => '<path d="M5 3.5h3.5l1.8 4.5-2.3 1.4a11 11 0 006.6 6.6l1.4-2.3 4.5 1.8V19a1.5 1.5 0 01-1.5 1.5A16.5 16.5 0 013.5 5 1.5 1.5 0 015 3.5z"/>',
            'wa'     => '<path d="M4 20l1.2-4.1A8.3 8.3 0 1112 20.3a8.3 8.3 0 01-4.1-1.1L4 20z"/><path d="M9.2 8.6c.2-.5.5-.5.8-.5h.5c.2 0 .4 0 .5.4l.7 1.6c.1.2 0 .4-.1.5l-.5.6c-.1.1-.2.3 0 .5.7 1.2 1.6 2 2.9 2.6.2.1.4 0 .5-.1l.6-.7c.1-.2.3-.2.5-.1l1.6.8c.2.1.3.2.3.4 0 .7-.4 1.5-1.2 1.8-.8.3-1.9.3-3.7-.7a8.6 8.6 0 01-3.3-3.6c-.6-1.3-.4-2.4.1-3z"/>',
        ];
        return '<svg class="at-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
            . ($p[$name] ?? $p['star']) . '</svg>';
    }

    /** Bangla digits for counters like "০১ / ০৩". */
    public function bn(string $s): string {
        return strtr($s, ['0' => '০', '1' => '১', '2' => '২', '3' => '৩', '4' => '৪', '5' => '৫', '6' => '৬', '7' => '৭', '8' => '৮', '9' => '৯']);
    }
}
