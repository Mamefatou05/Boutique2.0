<?php
namespace App\Providers;

use App\Services\AuthentificationPassport;
use App\Services\AuthentificationServiceInterface;
use App\Services\TokenService;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class ExceptionProvider extends ServiceProvider
{
    /**
     * Liste des repositories et services.
     */
    protected $repositories = [
        'Repository',
        'Service',
        // Ajoutez d'autres noms de repository ici
    ];

    public function register()
    {
        foreach ($this->repositories as $name) {
            $this->bindRepository($name);
            
            // Bind services
            $this->bindService($name);
            // Bind service interface
            $this->bindServiceInterface($name);
        }
    }
    
    protected function bindRepository($name)
    {
        $this->app->bind(
            "App\\Repositories\\{$name}ExeptionInterface",
            "App\\Repositories\\{$name}Repository"
        );
    }
    
    protected function bindService($name)
{
    $this->app->singleton(
        "App\\Services\\{$name}Service",
        function ($app) use ($name) {
            $repositoryInterface = "App\\Repositories\\{$name}ExcInterface";
            $serviceClass = "App\\Services\\{$name}Service";
            return new $serviceClass($app->make($repositoryInterface));
        }
    );
}
    
protected function bindServiceInterface($name)
{
    $this->app->bind(
        "App\\Services\\{$name}ServiceInterface",
        "App\\Services\\{$name}Service"
    );
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');
    }
}
