<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Infrastructure extends Model
{
    protected $table = 'infrastructures';

    protected $fillable = [
        'departement_id',
        'commune_id',
        'localite_id',
        'secteur_id',
        'nom',
        'description',
        'latitude',
        'longitude',
        'date_creation',
        'etat_lieu',
        'type_infrastructure',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'date_creation' => 'date',
    ];

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function localite()
    {
        return $this->belongsTo(Localite::class);
    }

    public function secteur()
    {
        return $this->belongsTo(Secteur::class);
    }

    public function localitesCouvertes()
    {
        return $this->belongsToMany(
            Localite::class,
            'localite_couvert'
        )
        ->withPivot('nbre_population_couvert')
        ->withTimestamps();
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function photos()
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

}
