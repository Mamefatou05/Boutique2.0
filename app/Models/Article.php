<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = ['name', 'description', 'price', 'quantity_in_stock'];
    protected $casts = ['quantity_in_stock' => 'integer'];
    protected $hidden = ['created_at', 'updated_at'];

    // Scope pour récupérer les articles supprimés // Scope pour récupérer les articles disponibles ou non disponibles
    public function scopeAvailable($query, $status = true)
    {
        return $query->where('quantity_in_stock', $status ? '>' : '=', 0);
    }

    // Scope pour récupérer les articles supprimés
    public function scopeTrashed($query, $trashed = false)
    {
        return $trashed ? $query->onlyTrashed() : $query;
    }

    public function dettes()
    {
        return $this->belongsToMany(Dette::class, 'article_dette')
                    ->withPivot('qteVente', 'prixVente');
    }
}
