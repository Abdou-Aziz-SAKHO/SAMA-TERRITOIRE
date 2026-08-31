<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'statut',
        'cni',
        'telephone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];


    }

    /**
     * Vérifie si l'utilisateur est un Administrateur simple.
     * Utilisé pour restreindre l'accès aux fonctions réservées aux admins.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'ADMINISTRATEUR';
    }

    /**
     * Vérifie si l'utilisateur est un Super Administrateur.
     * Seul le Super Admin peut créer d'autres Super Admins.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'SUPER_ADMINISTRATEUR';
    }
}
