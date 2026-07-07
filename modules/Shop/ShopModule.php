<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Shop;

use Alaosaf\Interfaces\ModuleInterface;
use Alaosaf\Modules\Shop\Controllers\ShopController;

class ShopModule implements ModuleInterface {
    public function getModuleId(): string {
        return 'shop';
    }

    public function getModuleName(): string {
        return 'Shop';
    }

    public function init(): void {
        $shopController = new ShopController();
        $shopController->init();

        $quickViewController = new \Alaosaf\Modules\Shop\Controllers\QuickViewController();
        $quickViewController->init();
    }
}
