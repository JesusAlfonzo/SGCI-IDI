<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'price',
        'recorded_at',
        'is_latest',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'is_latest' => 'boolean',
        'recorded_at' => 'datetime',
    ];
    
    // El diccionario no menciona created_at/updated_at, así que los deshabilitamos por simplicidad
    public $timestamps = false; 

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}