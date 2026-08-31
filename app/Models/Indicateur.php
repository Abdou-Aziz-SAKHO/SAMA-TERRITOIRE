<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Un indicateur mesure un aspect chiffré d'un secteur (santé, éducation...).
 *
 * Il est rattaché à un secteur (secteur_id). Sa VALEUR n'est pas stockée ici
 * car elle dépend de chaque infrastructure : elle est portée par la table pivot
 * `indicateur_infrastructure` (relation many-to-many avec Infrastructure).
 */
class Indicateur extends Model
{
    protected $table = 'indicateurs';

    protected $fillable = [
        'nom_indicateur',
        'unites',
        'description',
        'secteur_id',
    ];

    /**
     * Secteur auquel appartient cet indicateur.
     */
    public function secteur()
    {
        return $this->belongsTo(Secteur::class);
    }

    /**
     * Infrastructures mesurées par cet indicateur (many-to-many via la pivot
     * `indicateur_infrastructure`). La valeur propre à chaque infrastructure
     * est disponible via `pivot->valeur`.
     */
    public function infrastructures()
    {
        return $this->belongsToMany(
            Infrastructure::class,
            'indicateur_infrastructure',
            'indicateur_id',
            'infrastructure_id'
        )
        ->withPivot('valeur')
        ->withTimestamps();
    }
}
