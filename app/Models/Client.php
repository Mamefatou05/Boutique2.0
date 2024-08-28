<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }
 
    
}
