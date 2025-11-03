<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDetail extends Model
{
    use HasFactory;
    
    // Si tu tabla es 'request_detail' (singular), no necesitas $table. Si es 'request_details' (plural), es correcto.
    // Asumo que tu tabla es 'request_details' o 'request_detail'. Dejaré vacío si usa la convención.

    // 🔑 CAMPOS ASIGNABLES
    protected $fillable = [
        'request_id',
        'product_id',
        'quantity_requested',
        'quantity_delivered', // Incluir este campo si lo tienes en la migración
        
    ];

    // 🔑 RELACIONES

    /**
     * La solicitud a la que pertenece este detalle.
     */
    public function request()
    {
        return $this->belongsTo(RequestModel::class, 'request_id');
    }

    /**
     * El producto que se está solicitando.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relación con los detalles de entrega asociados a este ítem.
     * Esta relación es NECESARIA para el DeliveryController::show.
     */
    public function deliveryDetails()
    {
        return $this->hasMany(RequestDeliveryDetail::class, 'request_detail_id');
    }
}