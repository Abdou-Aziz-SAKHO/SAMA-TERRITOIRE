<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Infrastructure extends Model
{
    protected $table = 'infrastructures';

    /**
     * RÈGLE MÉTIER : une infrastructure appartient à UN SEUL niveau territorial :
     *   - soit 1 département  → departement_id rempli
     *   - soit 1 commune      → commune_id remplie
     *   - soit 1..n localités → commune_id remplie + lignes dans la pivot localite_couverts
     * (contrainte CHECK en base : departement_id OU commune_id, jamais les deux).
     */
    protected $fillable = [
        'departement_id',
        'commune_id',
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

    /**
     * Département d'implantation (si rattachée directement à un département).
     */
    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    /**
     * Commune d'implantation (si rattachée à une commune ou à des localités).
     */
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    /**
     * Secteur d'activité de l'infrastructure (santé, éducation, etc.).
     */
    public function secteur()
    {
        return $this->belongsTo(Secteur::class);
    }

    /**
     * Localités couvertes par l'infrastructure (plusieurs-à-plusieurs).
     * La pivot "localite_couverts" contient aussi nbre_population_couvert :
     * pour chaque localité on y stocke sa population (taille_population),
     * la population couverte totale étant la somme de ces valeurs.
     */
    public function localitesCouvertes()
    {
        return $this->belongsToMany(
            Localite::class,
            'localite_couverts',   // nom harmonisé de la table pivot (corrigé)
            'infrastructure_id',   // clé étrangère vers infrastructures
            'localite_id'          // clé étrangère vers localites
        )
        ->withPivot('nbre_population_couvert')
        ->withTimestamps();
    }

    /**
     * Population couverte totale = somme des populations des localités couvertes.
     * Retourne null si l'infrastructure n'est pas rattachée à des localités.
     */
    public function populationCouverte(): ?int
    {
        // sum() sur la colonne pivot ; null si aucune localité couverte
        return $this->localitesCouvertes->sum('pivot.nbre_population_couvert') ?: null;
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Photos de l'infrastructure. La table `photos` référence directement
     * l'infrastructure via `infrastructure_id` (pas de morph).
     */
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * Indicateurs mesurés pour cette infrastructure (many-to-many via la pivot
     * `indicateur_infrastructure`). Les indicateurs proviennent du secteur de
     * l'infrastructure ; la valeur de chacun est dans `pivot->valeur`.
     */
    public function indicateurs()
    {
        return $this->belongsToMany(
            Indicateur::class,
            'indicateur_infrastructure',
            'infrastructure_id',
            'indicateur_id'
        )
        ->withPivot('valeur')
        ->withTimestamps();
    }
}
