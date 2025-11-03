<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Asegúrate de importar el modelo de usuario
use App\Models\RequestDetail; // Asumiendo que este es el modelo de detalle de la solicitud

class RequestModel extends Model
{
    use HasFactory;

    // Nombre de la tabla explícito
    protected $table = 'requests'; 
    
    // Campos que permiten asignación masiva
    protected $fillable = [
        'request_code',
        'request_date',
        // Usando el nombre de columna de tu tabla
        'requested_by_user_id', 
        'purpose',
        'status',
        'approved_by_user_id', 
        'approval_date', 
        'delivery_date',
        'warehouse_staff_id', // Para el usuario que realiza la entrega (si lo necesitas)
        'notes',
    ];
    
    /**
     * The attributes that should be cast to native types.
     * Esto asegura que las fechas siempre sean objetos Carbon.
     */
    protected $casts = [
        'request_date' => 'datetime',
        'delivery_date' => 'datetime', 
        'approval_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * RELACIONES
     */

    // Relación con el usuario que realiza la solicitud
    public function requestedBy()
    {
        // Se relaciona con la columna 'requested_by_user_id'
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }
    
    // Relación con el usuario que aprueba o rechaza la solicitud
    public function approvedBy()
    {
        // Se relaciona con la columna 'approved_by_user_id'
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
    
    // Relación con los detalles/ítems de la solicitud (usando tu nombre 'details')
    public function details()
    {
        // Asumiendo que la clave foránea en RequestDetail es 'request_id'
        return $this->hasMany(RequestDetail::class, 'request_id');
    }
}
