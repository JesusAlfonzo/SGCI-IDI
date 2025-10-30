<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Opcional: Relación con productos (Many-to-One)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}