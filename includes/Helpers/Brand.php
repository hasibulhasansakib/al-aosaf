<?php
declare(strict_types=1);

namespace Alaosaf\Helpers;

use Alaosaf\Core\ServiceRegistry;
use Alaosaf\Services\BrandService;

/**
 * Static Facade for accessing Brand settings.
 */
class Brand {
    private static function getService(): BrandService {
        return ServiceRegistry::getInstance()->get(BrandService::class);
    }

    public static function __callStatic($name, $arguments) {
        return self::getService()->$name(...$arguments);
    }
}
