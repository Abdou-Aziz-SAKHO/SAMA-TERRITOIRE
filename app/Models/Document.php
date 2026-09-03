<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $table = 'documents';

    protected $fillable = [
        'titre',
        'type_document',
        'description',
        'departement_id',
        'region_id',
        'localite_id',
        'commune_id',
        'infrastructure_id',
        'nom_fichier',
        'chemin_document',
        'extension',
        'mime_type',
        'taille',
    ];

    /** Rattaché à une région (si niveau "région" choisi). */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /** Rattaché à un département (si niveau "département" choisi). */
    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    /** Rattaché à une commune (si niveau "commune" choisi). */
    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    /** Rattaché à une localité (si niveau "localité" choisi). */
    public function localite(): BelongsTo
    {
        return $this->belongsTo(Localite::class);
    }

    /** Rattaché à une infrastructure (facultatif, indépendant du territoire). */
    public function infrastructure(): BelongsTo
    {
        return $this->belongsTo(Infrastructure::class);
    }
}