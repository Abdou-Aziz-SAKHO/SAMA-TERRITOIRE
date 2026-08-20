<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actualite extends Model
{
    protected $table = 'actualites';

    protected $fillable = [
        'titre',
        'contenu',
        'date_publication',
        'region_id',
        'departement_id',
        'localite_id',
        'commune_id',
        'infrastructure_id',

    ];

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }
}
