<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Yaml\Yaml;

class YamlInterfaceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    protected $configPath = __DIR__ . '/../../config/docs/dependencies.yaml';

    public function register()
    {

        $config = Yaml::parseFile($this->configPath);

        Log::info($this->configPath);

        foreach ($config as $interface => $implementations) {
            $this->app->bind($interface, function ($app) use ($implementations) {
                $implementation = $this->getImplementation($implementations);
                return $app->make($implementation);
            });
        }
    }

    protected function getImplementation($implementations)
    {
        if (is_string($implementations)) {
            return $implementations;
        }

        $default = $implementations['default'] ?? null;
        $envImplementation = env('APP_IMPLEMENTATION', $default);

        return $implementations[$envImplementation] ?? $default;
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
