<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Carbon\Carbon;
use App\Models\Client;
use App\Policies\ClientPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Client::class => ClientPolicy::class,
        // Autres politiques...
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Register Passport routes
        
        Passport::tokensExpireIn(Carbon::now()->addHours(1));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(7));
    }
}
