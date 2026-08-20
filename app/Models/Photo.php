<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photos';

    protected $fillable = [
        'infrastructure_id',
        'nom',
        'chemin_photo',
        'description',
        'actualite_id',
    ];

    public function infrastructure()
    {
        return $this->belongsTo(Infrastructure::class);
    }


}
