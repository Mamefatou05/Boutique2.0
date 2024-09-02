<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'telephone',
        'adresse',
        'surname',
        'user_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

  
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function dettes(): HasMany
    {
        return $this->hasMany(Dette::class);
    }

      // Scope pour filtrer les clients avec ou sans compte
      public function scopeCompte($query, $value)
      {
          if ($value === 'oui') {
              return $query->whereNotNull('user_id');
          } elseif ($value === 'non') {
              return $query->whereNull('user_id');
          }
  
          return $query;
      }
  
      // Scope pour filtrer les clients par téléphone
      public function scopeTelephone($query, $value)
      {
          return $query->where('telephone', 'LIKE', '%'.$value.'%');
      }
      

      // Scope pour filtrer les clients actifs ou inactifs
      public function scopeActive($query, $value)
      {
          if ($value === 'oui') {
              return $query->whereHas('user', function ($q) {
                  $q->where('active', 1);
              });
          } elseif ($value === 'non') {
              return $query->whereHas('user', function ($q) {
                  $q->where('active', 0);
              });
          }
  
          return $query;
      }
}
