<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Admin;

use Alaosaf\Interfaces\ModuleInterface;
use Alaosaf\Modules\Admin\Controllers\AdminMenuController;

class AdminModule implements ModuleInterface {
    public function init(): void {
        $menuController = new AdminMenuController();
        $menuController->init();
    }

    public function getModuleId(): string {
        return 'admin_foundation';
    }
}
