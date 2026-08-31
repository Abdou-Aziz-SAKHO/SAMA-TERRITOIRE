<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localite extends Model
{
    protected $table = 'localites';

    protected $fillable = [
        'nom',
        'superficie',
        'taille_population',
        'nbre_menage',
        'nbre_homme',
        'nbre_femme',
        'latitude',
        'longitude',
        'commune_id',
    ];

    /**
     * Commune à laquelle appartient cette localité.
     */
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    /**
     * Infrastructures qui couvrent cette localité (relation inverse de
     * Infrastructure::localitesCouvertes(), via la pivot localite_couverts).
     */
    public function infrastructuresCouvertes()
    {
        return $this->belongsToMany(
            Infrastructure::class,
            'localite_couverts',   // table pivot harmonisée
            'localite_id',         // clé étrangère vers localites
            'infrastructure_id'    // clé étrangère vers infrastructures
        )
        ->withPivot('nbre_population_couvert')
        ->withTimestamps();
    }
}
