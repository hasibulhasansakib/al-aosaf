<?php
declare(strict_types=1);

namespace Alaosaf\Core;

use Alaosaf\Managers\AssetManager;

class Framework {
    private static ?self $instance = null;
    
    private function __construct() {}

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(): void {
        // 1. Initialize Service Registry
        $serviceRegistry = ServiceRegistry::getInstance();
        
        // 2. Register Core Services
        $serviceRegistry->register(AssetManager::class, new AssetManager());
        $serviceRegistry->register(\Alaosaf\Services\BrandService::class, new \Alaosaf\Services\BrandService());
        
        // 3. Load Active Modules from Module Registry
        $moduleRegistry = ModuleRegistry::getInstance();
        $moduleRegistry->loadActiveModules();
        
        // 4. Initialize Modules
        $moduleRegistry->initModules();
    }
}
