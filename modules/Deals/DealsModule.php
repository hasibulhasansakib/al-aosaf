<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Deals;

use Alaosaf\Interfaces\ModuleInterface;
use Alaosaf\Modules\Deals\Controllers\DealsAdminController;
use Alaosaf\Modules\Deals\Controllers\DealsFrontController;

/**
 * Offer page: discount rules for chosen products or categories (percent, taka off or fixed price),
 * applied to the real WooCommerce prices while the offer is live, and listed on [aa_offer_page].
 * Settings: Al Aosaf → Offer Page.
 */
class DealsModule implements ModuleInterface {
    public function init(): void {
        (new DealsPricing())->init();
        (new DealsFrontController())->init();
        add_action('aa_deals_flip', [DealsAdminController::class, 'purge']);
        if (is_admin()) {
            (new DealsAdminController())->init();
        }
    }

    public function getModuleId(): string {
        return 'deals';
    }
}
