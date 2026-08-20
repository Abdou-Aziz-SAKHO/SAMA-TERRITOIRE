<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    protected $table = 'commentaires';

    protected $fillable = [
        'message',
        'statut',
        'nom',
        'email',
        'actualite_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actualite()
    {
        return $this->belongsTo(Actualite::class);
    }
}
