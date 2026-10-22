<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Atelier\Controllers;

use Alaosaf\Modules\Atelier\AtelierSchema;
use Alaosaf\Modules\Atelier\AtelierView;

/**
 * [aa_atelier_home] shortcode: renders the builder sections in their saved order,
 * and on that page only loads the assets, the page palette and the SEO tags.
 */
class AtelierFrontController {

    public const SHORTCODE = 'aa_atelier_home';

    private bool $onPage = false;
    private ?array $doc = null;

    public function init(): void {
        add_shortcode(self::SHORTCODE, [$this, 'render']);
        add_action('wp', [$this, 'detect']);
        foreach (['save_post_product', 'woocommerce_update_product', 'woocommerce_product_set_stock_status', 'woocommerce_variation_set_stock_status', 'comment_post', 'wp_set_comment_status'] as $hook) {
            add_action($hook, [AtelierView::class, 'flushCache']);
        }
    }

    private function isPreview(): bool {
        return isset($_GET['aa_preview']) && current_user_can('manage_options'); // phpcs:ignore WordPress.Security.NonceVerification
    }

    private function doc(): array {
        if ($this->doc === null) {
            $this->doc = AtelierSchema::get($this->isPreview());
        }
        return $this->doc;
    }

    /** Is the current page one that holds the shortcode (plain content or Elementor data)? */
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
        $settings = $this->doc()['settings'];

        add_action('wp_enqueue_scripts', [$this, 'enqueue'], 20);
        add_action('wp_head', [$this, 'head'], 2);
        add_filter('body_class', static function (array $c) use ($settings): array {
            $c[] = 'at-page';
            if (!empty($settings['palette'])) {
                $c[] = 'at-palette';
            }
            return $c;
        });
        if ($settings['meta_title'] !== '' && !defined('WPSEO_VERSION') && !defined('RANK_MATH_VERSION')) {
            add_filter('pre_get_document_title', static fn() => $settings['meta_title'], 20);
        }
        // Only the real front page is indexed; a test copy of the builder page is not.
        if ($this->isPreview() || !is_front_page()) {
            add_filter('wp_robots', 'wp_robots_no_robots');
        }
        if ($this->isPreview()) {
            nocache_headers();
        }
    }

    public function enqueue(): void {
        $base = AA_PLUGIN_DIR . 'modules/Atelier/assets/';
        $url  = AA_PLUGIN_URL . 'modules/Atelier/assets/';
        wp_enqueue_style('aa-atelier-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Manrope:wght@400;500;600;700&family=Noto+Serif+Bengali:wght@500;600&display=swap', [], null);
        wp_enqueue_style('aa-atelier', $url . 'css/front.css', [], (string) filemtime($base . 'css/front.css'));
        wp_add_inline_style('aa-atelier', $this->bengaliFontFace());
        wp_enqueue_script('aa-atelier', $url . 'js/front.js', [], (string) filemtime($base . 'js/front.js'), true);
        // The old homepage slider library is not used here.
        wp_dequeue_style('swiper-css');
        wp_dequeue_script('swiper-js');
        wp_dequeue_style('aa-homepage-css');
        wp_dequeue_script('aa-homepage-js');
    }

    /** Self-hosted Noto Sans Bengali (house font) from uploads/aosaf-fonts, when present. */
    private function bengaliFontFace(): string {
        $up = wp_upload_dir();
        $dir = trailingslashit($up['basedir']) . 'aosaf-fonts/';
        $url = trailingslashit(set_url_scheme($up['baseurl'])) . 'aosaf-fonts/';
        $css = '';
        if (file_exists($dir . 'noto-sans-bengali-bengali.woff2')) {
            $css .= "@font-face{font-family:'AT Bengali';font-style:normal;font-weight:100 900;font-display:swap;src:url('{$url}noto-sans-bengali-bengali.woff2') format('woff2');unicode-range:U+0951-0952,U+0964-0965,U+0980-09FE,U+1CD0,U+1CD2,U+1CD5-1CD6,U+1CD8,U+1CE1,U+1CEA,U+1CED,U+1CF2,U+1CF5-1CF7,U+200C-200D,U+20B9,U+25CC,U+A8F1;}";
        }
        return $css;
    }

    public function head(): void {
        if (!$this->onPage) {
            return;
        }
        $doc = $this->doc();
        $s = $doc['settings'];

        // Preload the first hero image: it is the largest thing on screen.
        foreach ($doc['sections'] as $sec) {
            if ($sec['on'] && $sec['type'] === 'hero') {
                foreach ($sec['d']['slides'] as $slide) {
                    if ($slide['image']) {
                        $src = wp_get_attachment_image_src($slide['image'], 'full');
                        $set = wp_get_attachment_image_srcset($slide['image'], 'full');
                        if ($src) {
                            printf('<link rel="preload" as="image" href="%s"%s imagesizes="(max-width: 860px) 100vw, 58vw" fetchpriority="high">' . "\n",
                                esc_url($src[0]), $set ? ' imagesrcset="' . esc_attr($set) . '"' : '');
                        }
                        break;
                    }
                }
                break;
            }
        }

        if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) {
            return;
        }
        if ($s['meta_desc'] !== '') {
            echo '<meta name="description" content="' . esc_attr($s['meta_desc']) . '">' . "\n";
            echo '<meta property="og:description" content="' . esc_attr($s['meta_desc']) . '">' . "\n";
        }
        if ($s['meta_title'] !== '') {
            echo '<meta property="og:title" content="' . esc_attr($s['meta_title']) . '">' . "\n";
        }

        $graph = [];
        if (!empty($s['schema'])) {
            $brand = get_option('aa_brand_settings', []);
            $name = $brand['general_business_name'] ?? get_bloginfo('name');
            $logo = get_site_icon_url(512) ?: ($brand['branding_primary_logo'] ?? '');
            $org = ['@type' => 'Organization', '@id' => home_url('/#org'), 'name' => $name, 'url' => home_url('/')];
            if ($logo) $org['logo'] = $logo;
            $phone = $this->mainPhone();
            if ($phone) $org['contactPoint'] = ['@type' => 'ContactPoint', 'telephone' => '+88' . $phone, 'contactType' => 'customer service', 'areaServed' => 'BD', 'availableLanguage' => ['bn', 'en']];
            $graph[] = $org;
            $graph[] = ['@type' => 'WebSite', '@id' => home_url('/#website'), 'url' => home_url('/'), 'name' => $name, 'inLanguage' => 'bn-BD', 'publisher' => ['@id' => home_url('/#org')],
                'potentialAction' => ['@type' => 'SearchAction', 'target' => home_url('/?s={q}&post_type=product'), 'query-input' => 'required name=q']];
        }
        foreach ($doc['sections'] as $sec) {
            if ($sec['on'] && $sec['type'] === 'faq') {
                $qa = [];
                foreach ($sec['d']['items'] as $it) {
                    if ($it['q'] !== '' && $it['a'] !== '') {
                        $qa[] = ['@type' => 'Question', 'name' => $it['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $it['a']]];
                    }
                }
                if ($qa) $graph[] = ['@type' => 'FAQPage', 'mainEntity' => $qa];
            }
        }
        if ($graph) {
            echo '<script type="application/ld+json">' . wp_json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";
        }
    }

    private function mainPhone(): string {
        foreach ($this->doc()['sections'] as $sec) {
            if ($sec['type'] === 'whatsapp' && $sec['d']['phone'] !== '') {
                return preg_replace('/\D/', '', $sec['d']['phone']);
            }
        }
        $brand = get_option('aa_brand_settings', []);
        return preg_replace('/\D/', '', (string) ($brand['contact_phone'] ?? ''));
    }

    public function render(): string {
        $doc = $this->doc();
        $v = new AtelierView();
        // Taka prices are whole numbers: show ৳1,550 instead of ৳1,550.00 in this layout.
        $noDecimals = static fn() => 0;
        add_filter('wc_get_price_decimals', $noDecimals, 99);
        ob_start();
        echo '<div class="at-root" data-atelier>';
        if ($this->isPreview()) {
            echo '<div class="at-preview-flag">প্রিভিউ (ড্রাফট) · শুধু অ্যাডমিন দেখছেন</div>';
        }
        if ($doc['settings']['h1'] !== '') {
            echo '<h1 class="at-sr">' . esc_html($doc['settings']['h1']) . '</h1>';
        }
        foreach ($doc['sections'] as $sec) {
            if (!$sec['on']) {
                continue;
            }
            $file = AA_PLUGIN_DIR . 'modules/Atelier/Views/front/sections/' . $sec['type'] . '.php';
            if (!file_exists($file)) {
                continue;
            }
            $d = $sec['d'];
            $inner = (static function () use ($file, $d, $v, $sec): string {
                ob_start();
                include $file;
                return (string) ob_get_clean();
            })();
            if (trim($inner) === '') {
                continue;
            }
            $cls = 'at-sec at-sec--' . $sec['type'];
            if (isset($d['tone'])) $cls .= ' at-tone--' . $d['tone'];
            if (isset($d['space'])) $cls .= ' at-space--' . $d['space'];
            $id = !empty($d['anchor']) ? sanitize_title($d['anchor']) : 'at-' . $sec['id'];
            echo '<section class="' . esc_attr($cls) . '" id="' . esc_attr($id) . '">' . $inner . '</section>';
        }
        echo '</div>';
        remove_filter('wc_get_price_decimals', $noDecimals, 99);
        return (string) ob_get_clean();
    }
}
