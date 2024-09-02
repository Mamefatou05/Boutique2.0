<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    use HasFactory; // Assurez-vous que HasFactory est utilisé

    protected $fillable = ['date', 'montant', 'montantDu', 'client_id'];

    protected $casts = [
        'date' => 'datetime',
        'montant' => 'float',
        'montantDu' => 'float',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_dette')
                    ->withPivot('qteVente', 'prixVente');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    // Attribut calculé
    public function getMontantRestantAttribute()
    {
        $montantPaye = $this->paiements()->sum('montant');
        return $this->montantDu - $montantPaye;
    }
}
