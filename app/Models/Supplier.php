<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'priority',  // Asumido del diccionario (ENUM)
        'phone',     // Campo de contacto
        'email',     // Campo de contacto
        'contact_person', // Persona de contacto
        // Agrega otros campos de tu tabla 'suppliers' si existen (ej. address, rfc)
    ];

    // Relaciones (A futuro: un proveedor tiene muchas compras)
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}