<?php
declare(strict_types=1);

namespace Alaosaf\Core;

class ServiceRegistry {
    private array $services = [];
    private static ?self $instance = null;

    private function __construct() {}

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function register(string $interface, object $implementation): void {
        $this->services[$interface] = $implementation;
    }

    public function get(string $interface): ?object {
        return $this->services[$interface] ?? null;
    }
    
    public function has(string $interface): bool {
        return isset($this->services[$interface]);
    }
}
