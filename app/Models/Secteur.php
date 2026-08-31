<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Secteur extends Model
{
    protected $table = 'secteurs';

    protected $fillable = [
        'nom',
    ];

    /** Infrastructures relevant de ce secteur. */
    public function infrastructures()
    {
        return $this->hasMany(Infrastructure::class);
    }

    /** Indicateurs (critères de mesure) rattachés à ce secteur. */
    public function indicateurs()
    {
        return $this->hasMany(Indicateur::class);
    }
}
