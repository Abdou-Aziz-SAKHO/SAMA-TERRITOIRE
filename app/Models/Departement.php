<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    protected $table = 'departements';

    protected $fillable = [
        'nom',
        'superficie',
        'taille_population',
        'nbre_menage',
        'nbre_homme',
        'nbre_femme',
        'region_id',
        'latitude',
        'longitude',

    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /** Communes rattachées à ce département. */
    public function communes()
    {
        return $this->hasMany(Commune::class);
    }

    /** Infrastructures directement rattachées à ce département. */
    public function infrastructures()
    {
        return $this->hasMany(Infrastructure::class);
    }
}
