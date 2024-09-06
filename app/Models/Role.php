<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
  // Indiquez les attributs pouvant être assignés en masse
  protected $fillable = ['name'];
  
    /**
     * Get the users for the role.
     */
    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
