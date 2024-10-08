<?php
namespace App\Providers;

use App\Models\User;
use App\Services\AuthentificationPassport;
use App\Services\AuthentificationServiceInterface;
use App\Services\TokenService;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use App\Observers\UserObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Liste des repositories et services.
//      */
//     protected $repositories = [
//         'Article',
//         'Client',
//         'User',
//         // Ajoutez d'autres noms de service ici
//         // Ajoutez d'autres noms de repository ici
//     ];

//     public function register()
//     {
//         foreach ($this->repositories as $name) {
//             $this->bindRepository($name);
            
//             // Bind services
//             $this->bindService($name);
//             // Bind service interface
//             $this->bindServiceInterface($name);
//         }
//     }
    
//     protected function bindRepository($name)
//     {
//         $this->app->bind(
//             "App\\Repositories\\{$name}RepositoryInterface",
//             "App\\Repositories\\{$name}Repository"
//         );
//     }
    
//     protected function bindService($name)
// {
//     $this->app->singleton(
//         "App\\Services\\{$name}Service",
//         function ($app) use ($name) {
//             $repositoryInterface = "App\\Repositories\\{$name}RepositoryInterface";
//             $serviceClass = "App\\Services\\{$name}Service";
//             return new $serviceClass($app->make($repositoryInterface));
//         }
//     );
// }
    
// protected function bindServiceInterface($name)
// {
//     $this->app->bind(
//         "App\\Services\\{$name}ServiceInterface",
//         "App\\Services\\{$name}Service"
//     );
// }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');
    }
}
