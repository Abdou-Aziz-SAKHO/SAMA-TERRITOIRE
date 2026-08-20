<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regions';

    protected $fillable = [
        'nom',
        'superficie',
        'taille_population',
        'nbre_menage',
        'nbre_homme',
        'nbre_femme',
        'nbre_infrastructure',
        'latitude',
        'longitude',
    ];



    public function departements()
    {
        return $this->hasMany(Departement::class);
    }



}
