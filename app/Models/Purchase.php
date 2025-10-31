<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'purchase_date',
        'invoice_number',
        'total_amount',
        'purchase_code',
        'registered_by_user_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function registeredBy()
    {
        // Asume que el modelo de usuario es App\Models\User y la clave foránea es 'registered_by_user_id'
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }
}
