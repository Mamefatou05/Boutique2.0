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

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy', 
        Client::class => ClientPolicy::class,
        Article::class => ArticlePolicy::class,
        User::class => UserPolicy::class,



    ];

    /**
     * Register any authentication / authorization services.
     */
  
public function boot(): void
{
    Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');
        Passport::hashClientSecrets();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

    }
}
