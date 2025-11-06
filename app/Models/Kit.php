<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kit extends Model
{
    use HasFactory;

    // total_usages es el stock que se decrementa.
    protected $fillable = [
        'product_id', 
        'total_usages', 
    ];

    /**
     * Relación: El producto (item de inventario) al que corresponde este kit. (1:1)
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relación: Todos los registros de uso de este kit. (1:M)
     */
    public function usages()
    {
        return $this->hasMany(KitUsage::class);
    }

    /**
     * ⚠️ CRUCIAL: Relación con los componentes del kit.
     * Asume que hay una tabla pivote llamada 'kit_components'
     */
    public function components()
    {
        // Muchos a muchos con el producto que son sus componentes.
        return $this->belongsToMany(Product::class, 'kit_components', 'kit_id', 'component_id')
                    ->withPivot('quantity');
    }
}