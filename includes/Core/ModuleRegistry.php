<?php
declare(strict_types=1);

namespace Alaosaf\Core;

use Alaosaf\Interfaces\ModuleInterface;

class ModuleRegistry {
    private array $modules = [];
    private static ?self $instance = null;

    private function __construct() {}

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function loadActiveModules(): void {
        // Load active modules from DB option 'aa_active_modules'
        // For scaffolding, this will just be a placeholder logic.
        $activeModules = get_option('aa_active_modules', []);
        
        // Force load Admin module during scaffolding
        $activeModules[] = \Alaosaf\Modules\Admin\AdminModule::class;
        $activeModules[] = \Alaosaf\Modules\Appearance\AppearanceModule::class;
        $activeModules[] = \Alaosaf\Modules\Header\HeaderModule::class;
        $activeModules[] = \Alaosaf\Modules\Footer\FooterModule::class;
        $activeModules[] = \Alaosaf\Modules\Homepage\HomepageModule::class;
        $activeModules[] = \Alaosaf\Modules\Shop\ShopModule::class;
        $activeModules[] = \Alaosaf\Modules\Cart\CartModule::class;
        $activeModules[] = \Alaosaf\Modules\Checkout\CheckoutProgressModule::class;
        $activeModules[] = \Alaosaf\Modules\Account\AccountModule::class;
        $activeModules[] = \Alaosaf\Modules\Wishlist\WishlistModule::class;
        $activeModules[] = \Alaosaf\Modules\SingleProduct\SingleProductModule::class;
        $activeModules[] = \Alaosaf\Modules\Invoice\InvoiceModule::class;
        
        foreach ($activeModules as $moduleClass) {
            if (class_exists($moduleClass) && is_subclass_of($moduleClass, ModuleInterface::class)) {
                $module = new $moduleClass();
                $this->modules[$module->getModuleId()] = $module;
            }
        }
    }

    public function initModules(): void {
        foreach ($this->modules as $module) {
            $module->init();
        }
    }

    public function getModules(): array {
        return $this->modules;
    }
}
