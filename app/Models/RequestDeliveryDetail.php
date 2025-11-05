<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\RequestModel;
use App\Models\Product; // Asumiendo que tienes un modelo Product

class RequestDeliveryDetail extends Model
{
    use HasFactory;

    // Nombre de la tabla explícito
    protected $table = 'request_delivery_details'; 
    
    // Campos que permiten asignación masiva
    protected $fillable = [
        'request_id',
        'product_id',
        'quantity_delivered',
        'delivered_by_user_id',
        'received_by_user_id',
        'delivery_date',
        'delivery_notes',
    ];
    
    // Castings de fechas
    protected $casts = [
        'delivery_date' => 'datetime',
    ];

    /**
     * RELACIONES
     */

    public function request()
    {
        return $this->belongsTo(RequestModel::class, 'request_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    
    public function deliveredBy()
    {
        return $this->belongsTo(User::class, 'delivered_by_user_id');
    }
    
    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }
}