<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'estado', // Añadido por consistencia con activar/desactivar
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

}
