<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Account;

use Alaosaf\Interfaces\ModuleInterface;
use Alaosaf\Modules\Account\Controllers\AccountController;

class AccountModule implements ModuleInterface {
    
    public function getModuleId(): string {
        return 'account';
    }

    public function init(): void {
        $controller = new AccountController();
        $controller->init();
    }
}
