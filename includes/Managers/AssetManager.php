<?php
declare(strict_types=1);

namespace Alaosaf\Managers;

class AssetManager {
    private array $scripts = [];
    private array $styles = [];
    
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }

    public function registerScript(string $handle, string $src, array $deps = [], string $version = AA_VERSION, bool $inFooter = true, callable $condition = null): void {
        $this->scripts[$handle] = compact('src', 'deps', 'version', 'inFooter', 'condition');
    }

    public function registerStyle(string $handle, string $src, array $deps = [], string $version = AA_VERSION, string $media = 'all', callable $condition = null): void {
        $this->styles[$handle] = compact('src', 'deps', 'version', 'media', 'condition');
    }

    public function enqueueAssets(): void {
        foreach ($this->scripts as $handle => $data) {
            if ($data['condition'] === null || call_user_func($data['condition'])) {
                wp_enqueue_script($handle, $data['src'], $data['deps'], $data['version'], $data['inFooter']);
            }
        }

        foreach ($this->styles as $handle => $data) {
            if ($data['condition'] === null || call_user_func($data['condition'])) {
                wp_enqueue_style($handle, $data['src'], $data['deps'], $data['version'], $data['media']);
            }
        }
    }

    public function enqueueAdminAssets(): void {
        // Handle admin assets if needed
    }
}
