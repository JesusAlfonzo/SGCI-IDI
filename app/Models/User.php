<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
     * Spatie: Determina si el usuario es un super-administrador
     * y puede ignorar todas las comprobaciones de permiso.
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        // Si el usuario tiene el rol 'Super Administrador', devuelve TRUE inmediatamente
        if ($this->hasRole('Super Administrador')) {
            return true;
        }

        // De lo contrario, procede con la comprobación de permisos normal de Spatie
        return parent::hasPermissionTo($permission, $guardName);
    }
}
