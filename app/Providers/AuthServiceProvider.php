<?php
namespace App\Providers;

use App\Models\Article;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Carbon\Carbon;  
use App\Models\Client;
use App\Models\User;
use App\Policies\ArticlePolicy;
use App\Policies\AuthPolicy;
use App\Policies\ClientPolicy;
use App\Policies\UserPolicy;
use App\Services\AuthentificationPassport;
use App\Services\AuthentificationServiceInterface;
use App\Services\TokenService;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Client::class => ClientPolicy::class,
        Article::class => ArticlePolicy::class,
        User::class => UserPolicy::class,



    ];
    public function register()
    {
        $this->app->bind(AuthentificationServiceInterface ::class, function ($app) {
            $tokenService = $app->make(TokenService ::class);
            return new AuthentificationPassport($tokenService);
        });
    }


    /**
     * Register any authentication / authorization services.
     */
  
public function boot(): void
{
    Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');
    Passport::tokensCan([
        'user' => 'Access user information',
        'role' => 'Access user role',
    ]);
    Passport::setDefaultScope([
        'user',
    ]);
        Passport::hashClientSecrets();
        Passport::tokensExpireIn(now()->addMinutes(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

    }
}
