<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modèle pivot entre un indicateur et une infrastructure.
 *
 * Porte la `valeur` que prend l'indicateur pour une infrastructure précise.
 * Hérite de la classe Pivot d'Eloquent : utile pour accéder aux attributs
 * de la table d'association de façon typée et pour d'éventuels futurs attributs.
 */
class IndicateurInfrastructure extends Pivot
{
    protected $table = 'indicateur_infrastructure';

    protected $fillable = [
        'indicateur_id',
        'infrastructure_id',
        'valeur',
    ];

    protected $casts = [
        'valeur' => 'decimal:2',
    ];
}
