<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
// use App\Facades\ScopeFilterFacade;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'login',
        'password',
        'role_id',
        'photo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function scopeActive($query, $value)
    {
        if ($value === 'oui') {
            return $query->where('active', 1);
        } elseif ($value === 'non') {
            return $query->where('active', 0);
        }

        return $query;
    }


    
    

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function client(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    
    // protected static function booted()
    // {
    //     static::addGlobalScope(ScopeFilterFacade::apply());
    // }
}
