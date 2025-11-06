<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'kit_id', 
        'used_by_user_id', 
        'usage_date', 
        'notes', // Asumo que el campo 'purpose' del request va a 'notes' aquí
    ];

    /**
     * Relación: El kit que se usó.
     */
    public function kit()
    {
        return $this->belongsTo(Kit::class, 'kit_id');
    }

    /**
     * Relación: El usuario que registró el uso.
     */
    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by_user_id');
    }
}