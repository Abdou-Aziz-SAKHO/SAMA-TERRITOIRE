<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $table = 'communes';

    protected $fillable = [
        'nom',
        'superficie',
        'taille_population',
        'nbre_menage',
        'nbre_homme',
        'nbre_femme',
        'latitude',
        'longitude',
        'departement_id',
    ];

    /** Département auquel appartient cette commune. */
    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    /** Localités rattachées à cette commune. */
    public function localites()
    {
        return $this->hasMany(Localite::class);
    }

    /** Infrastructures directement rattachées à cette commune. */
    public function infrastructures()
    {
        return $this->hasMany(Infrastructure::class);
    }
}
