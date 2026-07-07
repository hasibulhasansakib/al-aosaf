<?php
declare(strict_types=1);

namespace Alaosaf\Interfaces;

interface ModuleInterface {
    /**
     * Initialize the module and hook into WP lifecycle.
     */
    public function init(): void;

    /**
     * Get the unique identifier for the module.
     */
    public function getModuleId(): string;
}
