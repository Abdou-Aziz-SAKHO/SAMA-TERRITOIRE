<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';
      protected $fillable = [
        'titre',
        'type_document',
        'departement_id',
        'region_id',
        'localite_id',
        'commune_id',
        'infrastructure_id',
        'nom',
        'chemin_document',
        'description',

    ];
}
