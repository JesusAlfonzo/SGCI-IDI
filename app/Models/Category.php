<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Permitir asignación masiva para los campos 'name' y 'description'
    protected $fillable = [
        'name',
        'description', // Asumiendo que agregaste este campo a tu tabla
    ];

    // Opcional: Definir relaciones
    public function products()
    {
        // Una categoría puede tener muchos productos
        return $this->hasMany(Product::class);
    }
}