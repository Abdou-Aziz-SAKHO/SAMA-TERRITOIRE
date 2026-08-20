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
}
