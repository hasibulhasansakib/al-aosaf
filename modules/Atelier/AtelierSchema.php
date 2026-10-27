<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Atelier;

/**
 * Section types of the Homepage Builder: their fields, defaults and sanitising.
 * The builder UI, the saved data and the front-end views are all driven from here.
 */
class AtelierSchema {

    public const OPTION_LIVE  = 'aa_atelier';
    public const OPTION_DRAFT = 'aa_atelier_draft';

    private const TONES  = ['dark' => 'Dark', 'surface' => 'Dark (raised)', 'sand' => 'Sand (light)'];
    private const SPACES = ['s' => 'Small', 'm' => 'Medium', 'l' => 'Large'];
    private const ICONS  = [
        'shield' => 'Original / shield', 'cash' => 'Cash on delivery', 'truck' => 'Delivery truck',
        'eye' => 'Check before paying', 'return' => 'Return / exchange', 'star' => 'Star', 'chat' => 'Chat / support',
        'gift' => 'Gift', 'tag' => 'Price tag', 'clock' => 'Clock',
    ];
    private const SOURCES = [
        'new' => 'Newest products', 'best' => 'Best sellers (real sales)', 'sale' => 'On sale',
        'cat' => 'From categories', 'pick' => 'Hand-picked products',
    ];

    /** Shared style fields appended to most section types. */
    private static function style(string $tone = 'dark'): array {
        return [
            'tone'  => ['type' => 'select', 'label' => 'Background', 'options' => self::TONES, 'default' => $tone],
            'space' => ['type' => 'select', 'label' => 'Spacing', 'options' => self::SPACES, 'default' => 'm'],
            'anchor' => ['type' => 'text', 'label' => 'Anchor id (optional, for #links)', 'default' => ''],
        ];
    }

    private static function heading(string $eyebrow = '', string $title = '', string $text = ''): array {
        return [
            'eyebrow' => ['type' => 'text', 'label' => 'Small label above title', 'default' => $eyebrow],
            'title'   => ['type' => 'text', 'label' => 'Title', 'default' => $title, 'rich' => true],
            'text'    => ['type' => 'textarea', 'label' => 'Text', 'default' => $text],
        ];
    }

    private static function productSource(int $limit = 8): array {
        return [
            'source' => ['type' => 'select', 'label' => 'Products from', 'options' => self::SOURCES, 'default' => 'new'],
            'cats'   => ['type' => 'categories', 'label' => 'Categories (when "From categories")', 'default' => []],
            'ids'    => ['type' => 'products', 'label' => 'Products (when "Hand-picked")', 'default' => []],
            'limit'  => ['type' => 'number', 'label' => 'How many', 'default' => $limit, 'min' => 1, 'max' => 24],
            'instock' => ['type' => 'toggle', 'label' => 'Hide out-of-stock products', 'default' => 1],
        ];
    }

    /** All section types. */
    public static function types(): array {
        return [
            'announce' => [
                'label' => 'Announcement ribbon',
                'hint'  => 'Thin line of rotating messages at the very top.',
                'fields' => [
                    'items' => ['type' => 'repeater', 'label' => 'Messages', 'item' => 'Message', 'fields' => [
                        'text' => ['type' => 'text', 'label' => 'Text', 'default' => ''],
                        'url'  => ['type' => 'url', 'label' => 'Link (optional)', 'default' => ''],
                    ], 'default' => []],
                ],
            ],
            'hero' => [
                'label' => 'Hero slider',
                'hint'  => 'Editorial split hero. Use separate mobile images for the best result on phones.',
                'fields' => [
                    'slides' => ['type' => 'repeater', 'label' => 'Slides', 'item' => 'Slide', 'fields' => [
                        'image'    => ['type' => 'image', 'label' => 'Image (desktop)', 'default' => 0],
                        'image_m'  => ['type' => 'image', 'label' => 'Image (mobile, optional, portrait)', 'default' => 0],
                        'eyebrow'  => ['type' => 'text', 'label' => 'Small label', 'default' => ''],
                        'title'    => ['type' => 'text', 'label' => 'Headline (use <em>word</em> for the accent, <br> for a new line)', 'default' => '', 'rich' => true],
                        'text'     => ['type' => 'textarea', 'label' => 'Text', 'default' => ''],
                        'btn1'     => ['type' => 'text', 'label' => 'Button 1 text', 'default' => ''],
                        'btn1_url' => ['type' => 'url', 'label' => 'Button 1 link', 'default' => ''],
                        'btn2'     => ['type' => 'text', 'label' => 'Button 2 text', 'default' => ''],
                        'btn2_url' => ['type' => 'url', 'label' => 'Button 2 link', 'default' => ''],
                    ], 'default' => []],
                    'interval' => ['type' => 'number', 'label' => 'Seconds per slide', 'default' => 6, 'min' => 3, 'max' => 20],
                    'note'     => ['type' => 'text', 'label' => 'Side note (e.g. "সারা দেশে ক্যাশ অন ডেলিভারি")', 'default' => ''],
                ],
            ],
            'trust' => [
                'label' => 'Trust bar',
                'hint'  => 'Short promises customers care about. Keep each to a few words.',
                'fields' => [
                    'items' => ['type' => 'repeater', 'label' => 'Items', 'item' => 'Item', 'fields' => [
                        'icon'  => ['type' => 'select', 'label' => 'Icon', 'options' => self::ICONS, 'default' => 'shield'],
                        'title' => ['type' => 'text', 'label' => 'Title', 'default' => ''],
                        'text'  => ['type' => 'text', 'label' => 'Sub text', 'default' => ''],
                    ], 'default' => []],
                ] + self::style('surface'),
            ],
            'categories' => [
                'label' => 'Category grid',
                'hint'  => 'First tile is the large one. Without a custom image, the newest product photo of the category is used.',
                'fields' => self::heading() + [
                    'items' => ['type' => 'repeater', 'label' => 'Tiles', 'item' => 'Tile', 'fields' => [
                        'cat'   => ['type' => 'category', 'label' => 'Category', 'default' => 0],
                        'label' => ['type' => 'text', 'label' => 'Name (optional, default = category name)', 'default' => ''],
                        'note'  => ['type' => 'text', 'label' => 'Small line (optional)', 'default' => ''],
                        'image' => ['type' => 'image', 'label' => 'Image (optional)', 'default' => 0],
                    ], 'default' => []],
                ] + self::style(),
            ],
            'products' => [
                'label' => 'Product carousel',
                'hint'  => 'A swipeable row of product cards.',
                'fields' => self::heading() + self::productSource(10) + [
                    'link'     => ['type' => 'text', 'label' => '"View all" text', 'default' => 'সব দেখুন'],
                    'link_url' => ['type' => 'url', 'label' => '"View all" link', 'default' => ''],
                    'layout'   => ['type' => 'select', 'label' => 'Layout', 'options' => ['carousel' => 'Carousel', 'grid' => 'Grid'], 'default' => 'carousel'],
                    'cols'     => ['type' => 'select', 'label' => 'Cards per row (desktop)', 'options' => ['3' => '3', '4' => '4', '5' => '5'], 'default' => '4'],
                ] + self::style(),
            ],
            'tabs' => [
                'label' => 'Product tabs',
                'hint'  => 'Several product groups under one title, switched with tabs.',
                'fields' => self::heading() + [
                    'tabs' => ['type' => 'repeater', 'label' => 'Tabs', 'item' => 'Tab', 'fields' => [
                        'label'  => ['type' => 'text', 'label' => 'Tab name', 'default' => ''],
                        'source' => ['type' => 'select', 'label' => 'Products from', 'options' => self::SOURCES, 'default' => 'best'],
                        'cats'   => ['type' => 'categories', 'label' => 'Categories', 'default' => []],
                        'ids'    => ['type' => 'products', 'label' => 'Products (hand-picked)', 'default' => []],
                    ], 'default' => []],
                    'limit'   => ['type' => 'number', 'label' => 'Products per tab', 'default' => 8, 'min' => 2, 'max' => 16],
                    'cols'    => ['type' => 'select', 'label' => 'Cards per row (desktop)', 'options' => ['3' => '3', '4' => '4', '5' => '5'], 'default' => '4'],
                    'instock' => ['type' => 'toggle', 'label' => 'Hide out-of-stock products', 'default' => 1],
                ] + self::style(),
            ],
            'banner' => [
                'label' => 'Campaign banner',
                'hint'  => 'Split banner: text panel + large image. The countdown only shows when an end date is set and still in the future.',
                'fields' => self::heading() + [
                    'btn'      => ['type' => 'text', 'label' => 'Button text', 'default' => ''],
                    'btn_url'  => ['type' => 'url', 'label' => 'Button link', 'default' => ''],
                    'image'    => ['type' => 'image', 'label' => 'Image', 'default' => 0],
                    'side'     => ['type' => 'select', 'label' => 'Image side', 'options' => ['right' => 'Right', 'left' => 'Left'], 'default' => 'right'],
                    'ends'     => ['type' => 'datetime', 'label' => 'Offer ends (optional, real date only)', 'default' => ''],
                ] + self::style('sand'),
            ],
            'look' => [
                'label' => 'Shop the look',
                'hint'  => 'One styled photo with clickable dots. Click on the photo in the editor to place a dot, then choose its product.',
                'fields' => self::heading() + [
                    'image' => ['type' => 'image', 'label' => 'Photo', 'default' => 0],
                    'spots' => ['type' => 'hotspots', 'label' => 'Dots', 'image' => 'image', 'item' => 'Dot', 'fields' => [
                        'x'       => ['type' => 'number', 'label' => 'X %', 'default' => 50, 'min' => 0, 'max' => 100, 'step' => 0.1],
                        'y'       => ['type' => 'number', 'label' => 'Y %', 'default' => 50, 'min' => 0, 'max' => 100, 'step' => 0.1],
                        'product' => ['type' => 'product', 'label' => 'Product', 'default' => 0],
                        'label'   => ['type' => 'text', 'label' => 'Short note (optional, e.g. "কলারে হাতের কাজ")', 'default' => ''],
                    ], 'default' => []],
                ] + self::style('surface'),
            ],
            'story' => [
                'label' => 'Brand story',
                'hint'  => 'Image + text block about the shop.',
                'fields' => self::heading() + [
                    'btn'     => ['type' => 'text', 'label' => 'Button text', 'default' => ''],
                    'btn_url' => ['type' => 'url', 'label' => 'Button link', 'default' => ''],
                    'image'   => ['type' => 'image', 'label' => 'Image', 'default' => 0],
                    'points'  => ['type' => 'repeater', 'label' => 'Key points', 'item' => 'Point', 'fields' => [
                        'title' => ['type' => 'text', 'label' => 'Title', 'default' => ''],
                        'text'  => ['type' => 'text', 'label' => 'Text', 'default' => ''],
                    ], 'default' => []],
                ] + self::style(),
            ],
            'reviews' => [
                'label' => 'Customer reviews',
                'hint'  => 'Real reviews only. "Store reviews" uses approved WooCommerce product reviews. The section hides itself when there is nothing to show.',
                'fields' => self::heading() + [
                    'source' => ['type' => 'select', 'label' => 'Reviews from', 'options' => ['woo' => 'Store reviews (WooCommerce)', 'manual' => 'Added below', 'both' => 'Both'], 'default' => 'both'],
                    'min'    => ['type' => 'number', 'label' => 'Minimum stars (store reviews)', 'default' => 4, 'min' => 1, 'max' => 5],
                    'items'  => ['type' => 'repeater', 'label' => 'Reviews', 'item' => 'Review', 'fields' => [
                        'name'   => ['type' => 'text', 'label' => 'Customer name', 'default' => ''],
                        'place'  => ['type' => 'text', 'label' => 'City / product', 'default' => ''],
                        'text'   => ['type' => 'textarea', 'label' => 'Review', 'default' => ''],
                        'rating' => ['type' => 'number', 'label' => 'Stars', 'default' => 5, 'min' => 1, 'max' => 5],
                        'image'  => ['type' => 'image', 'label' => 'Photo / screenshot (optional)', 'default' => 0],
                    ], 'default' => []],
                ] + self::style(),
            ],
            'faq' => [
                'label' => 'FAQ',
                'hint'  => 'Questions and answers. Also added to Google as FAQ data.',
                'fields' => self::heading() + [
                    'items' => ['type' => 'repeater', 'label' => 'Questions', 'item' => 'Question', 'fields' => [
                        'q' => ['type' => 'text', 'label' => 'Question', 'default' => ''],
                        'a' => ['type' => 'textarea', 'label' => 'Answer', 'default' => ''],
                    ], 'default' => []],
                ] + self::style(),
            ],
            'whatsapp' => [
                'label' => 'WhatsApp / call band',
                'hint'  => 'Personal help band. Number in local format (01XXXXXXXXX).',
                'fields' => self::heading() + [
                    'phone'   => ['type' => 'text', 'label' => 'WhatsApp / phone number', 'default' => ''],
                    'message' => ['type' => 'text', 'label' => 'Pre-filled WhatsApp message', 'default' => ''],
                    'btn'     => ['type' => 'text', 'label' => 'WhatsApp button text', 'default' => 'WhatsApp-এ মেসেজ দিন'],
                    'call'    => ['type' => 'text', 'label' => 'Call button text (empty = hide)', 'default' => 'কল করুন'],
                    'image'   => ['type' => 'image', 'label' => 'Background image (optional)', 'default' => 0],
                ] + self::style('sand'),
            ],
        ];
    }

    /** Site-wide settings of the builder (SEO + look). */
    public static function settingsFields(): array {
        return [
            'h1'        => ['type' => 'text', 'label' => 'Page H1 (for Google, shown only to screen readers)', 'default' => ''],
            'meta_title' => ['type' => 'text', 'label' => 'SEO title (browser tab / Google)', 'default' => ''],
            'meta_desc' => ['type' => 'textarea', 'label' => 'SEO description (about 150 characters)', 'default' => ''],
            'schema'    => ['type' => 'toggle', 'label' => 'Add Organization + WebSite data for Google', 'default' => 1],
            'palette'   => ['type' => 'toggle', 'label' => 'Use the Atelier colours for the header and footer on this page', 'default' => 1],
            'page'      => ['type' => 'page', 'label' => 'Page that shows the builder (for the Preview button)', 'default' => 0],
        ];
    }

    public static function emptyData(string $type): array {
        $types = self::types();
        $out = [];
        foreach ($types[$type]['fields'] ?? [] as $key => $f) {
            $out[$key] = $f['default'];
        }
        return $out;
    }

    /* ------------------------------------------------------------------ sanitising */

    public static function sanitizeDocument($doc): array {
        $doc = is_array($doc) ? $doc : [];
        $types = self::types();
        $sections = [];
        foreach ((array) ($doc['sections'] ?? []) as $s) {
            if (!is_array($s) || empty($types[$s['type'] ?? ''])) {
                continue;
            }
            $type = $s['type'];
            $id = preg_replace('/[^a-z0-9_]/', '', strtolower((string) ($s['id'] ?? '')));
            $sections[] = [
                'id'   => $id !== '' ? $id : 's' . wp_generate_password(8, false, false),
                'type' => $type,
                'on'   => empty($s['on']) ? 0 : 1,
                'name' => sanitize_text_field((string) ($s['name'] ?? '')),
                'd'    => self::sanitizeFields($types[$type]['fields'], is_array($s['d'] ?? null) ? $s['d'] : []),
            ];
        }
        return [
            'v'        => 1,
            'sections' => $sections,
            'settings' => self::sanitizeFields(self::settingsFields(), is_array($doc['settings'] ?? null) ? $doc['settings'] : []),
        ];
    }

    private static function sanitizeFields(array $fields, array $data): array {
        $out = [];
        foreach ($fields as $key => $f) {
            $out[$key] = array_key_exists($key, $data) ? self::sanitizeValue($f, $data[$key]) : $f['default'];
        }
        return $out;
    }

    private static function sanitizeValue(array $f, $v) {
        switch ($f['type']) {
            case 'text':
                return !empty($f['rich'])
                    ? wp_kses((string) $v, ['em' => [], 'br' => [], 'strong' => []])
                    : sanitize_text_field((string) $v);
            case 'textarea':
                return sanitize_textarea_field((string) $v);
            case 'url':
                $v = trim((string) $v);
                return ($v !== '' && $v[0] === '#') ? '#' . sanitize_title(substr($v, 1)) : esc_url_raw($v);
            case 'number':
                $n = is_numeric($v) ? (float) $v : (float) $f['default'];
                if (isset($f['min'])) $n = max((float) $f['min'], $n);
                if (isset($f['max'])) $n = min((float) $f['max'], $n);
                return empty($f['step']) ? (int) round($n) : round($n, 1);
            case 'toggle':
                return empty($v) ? 0 : 1;
            case 'select':
                return array_key_exists((string) $v, $f['options']) ? (string) $v : $f['default'];
            case 'image':
            case 'category':
            case 'product':
            case 'page':
                return absint($v);
            case 'categories':
            case 'products':
                return array_values(array_filter(array_map('absint', (array) $v)));
            case 'datetime':
                $v = (string) $v;
                return preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $v) ? $v : '';
            case 'repeater':
            case 'hotspots':
                $items = [];
                foreach (array_slice((array) $v, 0, 40) as $item) {
                    if (is_array($item)) {
                        $items[] = self::sanitizeFields($f['fields'], $item);
                    }
                }
                return $items;
        }
        return '';
    }

    /* ------------------------------------------------------------------ starting content */

    /**
     * First version of the page, built from the store's real categories and product photos.
     * Images that do not exist on a site are left empty.
     */
    public static function seed(): array {
        $img = static function (int $id): int {
            return wp_attachment_is_image($id) ? $id : 0;
        };
        $cat = static function (int $id): int {
            return (taxonomy_exists('product_cat') && term_exists($id, 'product_cat')) ? $id : 0;
        };
        $catUrl = static function (int $id): string {
            $link = taxonomy_exists('product_cat') && term_exists($id, 'product_cat') ? get_term_link($id, 'product_cat') : '';
            return is_string($link) ? $link : '';
        };
        $shop = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/');
        $old = get_option('aa_homepage_settings', []);
        $oldCats = array_values(array_filter(array_map('intval', (array) ($old['featured_cats_list'] ?? []))));
        $phone = '01732698833';

        $sections = [];
        $add = static function (string $type, array $d, string $name = '') use (&$sections) {
            $sections[] = ['id' => 's' . wp_generate_password(8, false, false), 'type' => $type, 'on' => 1, 'name' => $name,
                'd' => array_merge(self::emptyData($type), $d)];
        };

        $add('announce', ['items' => [
            ['text' => 'সারা দেশে ক্যাশ অন ডেলিভারি, হাতে পেয়ে টাকা দিন', 'url' => ''],
            ['text' => 'অর্ডারের আগে সাইজ জেনে নিন: ' . $phone, 'url' => 'tel:+88' . $phone],
            ['text' => 'ডেলিভারি ম্যানের সামনেই পণ্য দেখে নিন', 'url' => ''],
        ]]);

        $add('hero', [
            'interval' => 6,
            'note' => '',
            'slides' => [
                ['image' => $img(780) ?: $img(1126), 'image_m' => 0, 'eyebrow' => 'New Collection 2026',
                    'title' => 'Crafted for<br><em>Every Celebration</em>',
                    'text' => 'দাওয়াত, বিয়ে কিংবা জুমা। হাতের কাজের এমব্রয়ডারি আর মাপমতো ফিটিং, হাতে পেয়ে দেখে তারপর টাকা।',
                    'btn1' => 'পাঞ্জাবি দেখুন', 'btn1_url' => $catUrl(271) ?: $shop, 'btn2' => 'সাইজ জেনে নিন', 'btn2_url' => '#whatsapp'],
                ['image' => $img(1000), 'image_m' => 0, 'eyebrow' => 'Signature Embroidery',
                    'title' => 'Black, Touched<br><em>with Gold</em>',
                    'text' => 'কালো পাঞ্জাবির উপর সোনালি জরির কাজ। যেখানেই পরবেন, সবার চোখে পড়বে।',
                    'btn1' => 'দেখে নিন', 'btn1_url' => $catUrl(271) ?: $shop, 'btn2' => '', 'btn2_url' => ''],
                ['image' => $img(1119), 'image_m' => 0, 'eyebrow' => 'Earth Tones',
                    'title' => 'Classic in<br><em>Warm Brown</em>',
                    'text' => 'সিম্পল কিন্তু রুচিশীল। যারা বেশি ঝকমকে কিছু চান না, কিন্তু মানুষ যেন একবার তাকায়।',
                    'btn1' => 'কালেকশন দেখুন', 'btn1_url' => $catUrl(271) ?: $shop, 'btn2' => '', 'btn2_url' => ''],
            ],
        ]);

        $add('trust', ['items' => [
            ['icon' => 'eye', 'title' => 'দেখে তারপর টাকা', 'text' => 'ডেলিভারি ম্যানের সামনেই চেক করুন'],
            ['icon' => 'cash', 'title' => 'ক্যাশ অন ডেলিভারি', 'text' => 'আগে কোনো টাকা লাগবে না'],
            ['icon' => 'truck', 'title' => 'সারা দেশে ডেলিভারি', 'text' => 'ঢাকা ও ঢাকার বাইরে'],
            ['icon' => 'chat', 'title' => 'কল বা WhatsApp', 'text' => 'সাইজ নিয়ে সরাসরি কথা বলুন'],
        ]]);
        $sections[count($sections) - 1]['on'] = 0; // kept in the builder, hidden by default

        $tiles = [];
        foreach ([[271, 'Panjabi', 'দাওয়াত থেকে জুমা'], [19, 'Watches', 'প্রতিদিনের স্টাইল'], [20, 'Naviforce', 'জনপ্রিয় মডেলগুলো'], [175, 'Wallets', 'চামড়ার মানিব্যাগ']] as [$cid, $label, $note]) {
            if ($cat($cid)) {
                $tiles[] = ['cat' => $cid, 'label' => $label, 'note' => $note, 'image' => 0];
            }
        }
        foreach ($oldCats as $cid) {
            if (count($tiles) >= 4) break;
            if ($cat($cid) && !in_array($cid, array_column($tiles, 'cat'), true)) {
                $tiles[] = ['cat' => $cid, 'label' => '', 'note' => '', 'image' => 0];
            }
        }
        $add('categories', ['eyebrow' => 'Collections', 'title' => 'Shop by <em>Category</em>', 'items' => $tiles]);

        $add('products', ['eyebrow' => 'Just In', 'title' => 'New <em>Arrivals</em>', 'source' => 'new', 'limit' => 10,
            'link' => 'সব দেখুন', 'link_url' => $shop]);

        $add('banner', ['eyebrow' => 'The Embroidery Edit', 'title' => 'Quiet Style,<br><em>Loud Detail</em>',
            'text' => 'সূক্ষ্ম এমব্রয়ডারি, মাপমতো কাটিং আর গাঢ় কালার। অফিস থেকে দাওয়াত, সব জায়গায় মানায়।',
            'btn' => 'পাঞ্জাবিগুলো দেখুন', 'btn_url' => $catUrl(271) ?: $shop, 'image' => $img(1127) ?: $img(1176), 'side' => 'right', 'tone' => 'surface']);

        $tabs = [['label' => 'Best Sellers', 'source' => 'best', 'cats' => [], 'ids' => []]];
        if ($cat(271)) $tabs[] = ['label' => 'Panjabi', 'source' => 'cat', 'cats' => [271], 'ids' => []];
        if ($cat(19))  $tabs[] = ['label' => 'Watches', 'source' => 'cat', 'cats' => [19], 'ids' => []];
        $add('tabs', ['eyebrow' => 'Most Loved', 'title' => 'Best <em>Sellers</em>', 'text' => 'ক্রেতারা যেগুলো বারবার অর্ডার করছেন।', 'tabs' => $tabs, 'limit' => 8]);

        $add('look', ['eyebrow' => 'Up Close', 'title' => 'In the <em>Details</em>',
            'text' => 'অনলাইনে কেনার আগে সবাই এটাই দেখতে চায়। ছবির নম্বরে চাপ দিন, কাজগুলো কাছ থেকে দেখুন।',
            'image' => $img(1045), 'spots' => $img(1045) ? [
                ['x' => 47, 'y' => 28, 'product' => 1065, 'label' => 'কলারে সূক্ষ্ম এমব্রয়ডারি'],
                ['x' => 56.5, 'y' => 50.5, 'product' => 1065, 'label' => 'নকশা করা মেটাল বোতাম'],
                ['x' => 42, 'y' => 72, 'product' => 1065, 'label' => 'হাতার কাফে ফুলের কাজ'],
            ] : []]);
        $sections[count($sections) - 1]['on'] = 0;

        $add('reviews', ['eyebrow' => 'Customer Love', 'title' => 'What Our <em>Customers Say</em>', 'source' => 'both']);

        $add('faq', ['eyebrow' => 'Help', 'title' => 'Questions, <em>Answered</em>', 'items' => [
            ['q' => 'অর্ডার কীভাবে করব?', 'a' => 'পছন্দের পণ্যে "অর্ডার করুন" চাপুন, তারপর নাম, মোবাইল নম্বর আর ঠিকানা দিন। আমরা ফোন দিয়ে অর্ডার কনফার্ম করে নেব।'],
            ['q' => 'আগে কি টাকা পাঠাতে হবে?', 'a' => 'না। ক্যাশ অন ডেলিভারি, পণ্য হাতে পেয়ে দেখে তারপর ডেলিভারি ম্যানকে টাকা দেবেন।'],
            ['q' => 'আমার সাইজ কোনটা হবে বুঝতে পারছি না', 'a' => 'অর্ডারের আগে ' . $phone . ' নম্বরে কল বা WhatsApp করুন। আপনার উচ্চতা আর সাধারণত কোন সাইজ পরেন জানালে আমরা মিলিয়ে বলে দেব।'],
            ['q' => 'ছবির সাথে আসল পণ্য মিলবে তো?', 'a' => 'ডেলিভারি ম্যানের সামনেই প্যাকেট খুলে মিলিয়ে নিতে পারবেন, তারপর টাকা দেবেন। অর্ডারের আগে আরও ছবি দেখতে চাইলে WhatsApp-এ জানান।'],
        ]]);
        $sections[count($sections) - 1]['on'] = 0;

        $add('whatsapp', ['eyebrow' => 'Personal Help', 'title' => 'Not Sure About <em>Your Size?</em>',
            'text' => 'উচ্চতা আর সাধারণ মাপটা জানান, সঠিক সাইজটা আমরাই বলে দেব। কোনো পণ্যের আরও ছবি লাগলেও মেসেজ দিন।',
            'phone' => $phone, 'message' => 'আসসালামু আলাইকুম, alaosaf.com থেকে একটা পণ্যের ব্যাপারে জানতে চাই।', 'anchor' => 'whatsapp']);

        return self::sanitizeDocument([
            'sections' => $sections,
            'settings' => [
                'h1' => 'Al Aosaf: প্রিমিয়াম পাঞ্জাবি, ঘড়ি ও মেনস ফ্যাশন, সারা বাংলাদেশে ক্যাশ অন ডেলিভারি',
                'meta_title' => 'Al Aosaf | প্রিমিয়াম পাঞ্জাবি, ঘড়ি ও মেনস ফ্যাশন',
                'meta_desc' => 'Al Aosaf থেকে কিনুন প্রিমিয়াম এমব্রয়ডারি পাঞ্জাবি, ঘড়ি ও অ্যাক্সেসরিজ। সারা বাংলাদেশে ক্যাশ অন ডেলিভারি, পণ্য দেখে টাকা দিন।',
                'schema' => 1, 'palette' => 1, 'page' => 0,
            ],
        ]);
    }

    public static function get(bool $draft = false): array {
        $doc = get_option($draft ? self::OPTION_DRAFT : self::OPTION_LIVE, null);
        if (!is_array($doc) && $draft) {
            $doc = get_option(self::OPTION_LIVE, null);
        }
        if (!is_array($doc)) {
            $doc = self::seed();
        }
        return $doc;
    }
}
