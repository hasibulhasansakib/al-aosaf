<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Wishlist;

use Alaosaf\Interfaces\ModuleInterface;

class WishlistModule implements ModuleInterface {
    
    public function getModuleId(): string {
        return 'wishlist';
    }

    public function init(): void {
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('wp_ajax_aa_toggle_wishlist', [$this, 'toggleWishlist']);
        add_action('wp_ajax_nopriv_aa_toggle_wishlist', [$this, 'toggleWishlist']);
    }

    public function enqueueAssets(): void {
        wp_enqueue_style('aa-wishlist-css', AA_PLUGIN_URL . 'modules/Wishlist/assets/css/wishlist.css', [], time() . rand());
        wp_enqueue_script('aa-wishlist-js', AA_PLUGIN_URL . 'modules/Wishlist/assets/js/wishlist.js', ['jquery'], time() . rand(), true);
        
        wp_localize_script('aa-wishlist-js', 'aa_wishlist_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aa_wishlist_nonce')
        ]);
    }

    public function toggleWishlist(): void {
        check_ajax_referer('aa_wishlist_nonce', 'nonce');
        
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        if (!$product_id) {
            wp_send_json_error(['message' => 'Invalid product ID']);
        }

        $wishlist = self::getWishlist();
        $is_in_wishlist = in_array($product_id, $wishlist);

        if ($is_in_wishlist) {
            $wishlist = array_diff($wishlist, [$product_id]);
            $added = false;
        } else {
            $wishlist[] = $product_id;
            $added = true;
        }

        self::saveWishlist($wishlist);

        // Render the mini-wishlist fragment
        ob_start();
        $file_path = AA_PLUGIN_DIR . 'modules/Wishlist/Views/mini-wishlist.php';
        if (file_exists($file_path)) {
            include $file_path;
        }
        $fragment = ob_get_clean();

        wp_send_json_success([
            'added' => $added,
            'count' => count($wishlist),
            'fragments' => [
                '.aa-wishlist-dropdown' => $fragment
            ]
        ]);
    }

    public static function getWishlist(): array {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $wishlist = get_user_meta($user_id, 'aa_wishlist', true);
            return is_array($wishlist) ? $wishlist : [];
        } else {
            $cookie = isset($_COOKIE['aa_guest_wishlist']) ? sanitize_text_field(wp_unslash($_COOKIE['aa_guest_wishlist'])) : '';
            if ($cookie) {
                $decoded = json_decode(base64_decode($cookie), true);
                return is_array($decoded) ? $decoded : [];
            }
            return [];
        }
    }

    public static function saveWishlist(array $wishlist): void {
        $wishlist = array_values(array_unique($wishlist)); // clean up
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            update_user_meta($user_id, 'aa_wishlist', $wishlist);
        } else {
            $cookie_value = base64_encode(json_encode($wishlist));
            setcookie('aa_guest_wishlist', $cookie_value, time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
            $_COOKIE['aa_guest_wishlist'] = $cookie_value;
        }
    }

    public static function isInWishlist(int $product_id): bool {
        $wishlist = self::getWishlist();
        return in_array($product_id, $wishlist);
    }
}
