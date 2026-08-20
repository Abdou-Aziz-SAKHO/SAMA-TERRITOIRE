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

}
