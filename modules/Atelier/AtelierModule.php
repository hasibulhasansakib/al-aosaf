<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Atelier;

use Alaosaf\Interfaces\ModuleInterface;
use Alaosaf\Modules\Atelier\Controllers\AtelierAdminController;
use Alaosaf\Modules\Atelier\Controllers\AtelierFrontController;

/**
 * Homepage Builder ("Atelier"): a premium homepage made of sections that are
 * ordered, switched on/off and edited from Al Aosaf → Homepage Builder.
 * Output: [aa_atelier_home].
 */
class AtelierModule implements ModuleInterface {
    public function init(): void {
        (new AtelierFrontController())->init();
        if (is_admin()) {
            (new AtelierAdminController())->init();
        }
    }

    public function getModuleId(): string {
        return 'atelier';
    }
}
