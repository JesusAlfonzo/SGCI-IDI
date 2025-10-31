<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDeliveryDetail extends Model
{
    use HasFactory;

    // Asumo que tu tabla es 'request_delivery_details'
    
    // 🔑 CAMPOS ASIGNABLES
    protected $fillable = [
        'request_detail_id',
        'delivered_by_user_id', // El almacenista que entrega
        'received_by_user_id', // Quién recibe el material
        'quantity_delivered',  // Cantidad real que sale del stock
        'delivery_date',
        // otros campos...
    ];

    // 🔑 RELACIONES

    public function detail()
    {
        return $this->belongsTo(RequestDetail::class, 'request_detail_id');
    }
    
    public function deliveredBy()
    {
        return $this->belongsTo(User::class, 'delivered_by_user_id');
    }
}