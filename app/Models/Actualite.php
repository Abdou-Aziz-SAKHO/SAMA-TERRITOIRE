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
        'commune_id',
        'localite_id',
        'infrastructure_id',
    ];

    protected $casts = [
        'date_publication' => 'datetime',
    ];

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

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

    public function infrastructure()
    {
        return $this->belongsTo(Infrastructure::class);
    }
}
