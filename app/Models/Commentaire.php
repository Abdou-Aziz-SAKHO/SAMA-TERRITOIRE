<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    protected $table = 'commentaires';

    protected $fillable = [
        'message',
        'statut',
        'nom',
        'email',
        'actualite_id',
        'type',
    ];

    /**
     * Commentaire indépendant (pas lié à une actualité).
     */
    public function scopeIndependant($query)
    {
        return $query->whereNull('actualite_id');
    }

    /**
     * Commentaires liés à une actualité.
     */
    public function scopeLiesActualite($query)
    {
        return $query->whereNotNull('actualite_id');
    }

    public function actualite()
    {
        return $this->belongsTo(Actualite::class);
    }
}
