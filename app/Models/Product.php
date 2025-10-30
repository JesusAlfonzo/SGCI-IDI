<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'unit_id',
        'location_id',
        'sku',
        'name',
        'stock_actual',
        'stock_minimo',
        'is_kit',
        'description', // Asumiendo que existe para detallar el producto
    ];

    // Los campos de stock son enteros
    protected $casts = [
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'is_kit' => 'boolean',
    ];

    // Definición de las Relaciones (Foreign Keys)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Otras relaciones
    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }
}