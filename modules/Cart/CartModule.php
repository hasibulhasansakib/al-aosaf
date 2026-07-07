<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Cart;

use Alaosaf\Interfaces\ModuleInterface;
use Alaosaf\Modules\Cart\Controllers\CartController;

class CartModule implements ModuleInterface {
    public function getModuleId(): string {
        return 'cart';
    }

    public function init(): void {
        $controller = new CartController();
        $controller->init();
    }
}
