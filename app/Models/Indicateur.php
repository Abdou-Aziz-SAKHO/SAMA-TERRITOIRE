<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indicateur extends Model
{
    protected $table = 'indicateurs';

    protected $fillable = [
        'nom_indicateur',
        'unites',
        'description',
        'valeur',
        'secteur_id',
        'infrastructure_id',
    ];

}
