<?php

namespace App\Providers;


// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Client;
use App\Policies\ClientPolicy;
use Laravel\Passport\Passport;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Client::class => ClientPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

    }
    /**
     * Register any authentication / authorization services.
     */
   
}
