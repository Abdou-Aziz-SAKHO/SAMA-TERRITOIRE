<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localite_Couvert extends Model
{
    protected $table = 'localite_couverts';

    protected $fillable = [
        'localite_id',
        'infrastructure_id',
        'nbre_population_couvert',
    ];
}
