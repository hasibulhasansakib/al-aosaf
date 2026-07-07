<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Checkout;

use Alaosaf\Interfaces\ModuleInterface;
use Alaosaf\Modules\Checkout\Controllers\CheckoutProgressController;

class CheckoutProgressModule implements ModuleInterface {
    public function getModuleId(): string {
        return 'checkout';
    }

    public function init(): void {
        $progressController = new CheckoutProgressController();
        $progressController->init();

        $noticeController = new \Alaosaf\Modules\Checkout\Controllers\CheckoutNoticeController();
        $noticeController->init();
    }
}
