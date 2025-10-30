<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', // Ej. Estante A, Refrigerador B, Almacén Central
    ];

    // Relaciones (A futuro: una ubicación puede tener muchos productos)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}