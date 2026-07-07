<?php
require_once dirname(__DIR__, 3) . '/wp-load.php';

$_POST['action'] = 'aa_filter_shop';
$_POST['nonce'] = wp_create_nonce('aa-shop-filter-nonce');
$_POST['posts_per_page'] = 16;
$_POST['paged'] = 1;

try {
    $controller = new \Alaosaf\Modules\Shop\Controllers\ShopController();
    $controller->ajaxFilterProducts();
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine();
}
