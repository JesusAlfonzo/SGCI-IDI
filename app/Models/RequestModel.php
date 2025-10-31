<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestModel extends Model
{
    use HasFactory;

    // Nombre de la tabla explícito (por si acaso el plural de RequestModel es Requests)
    protected $table = 'requests'; 
    
    // Campos que permiten asignación masiva
    protected $fillable = [
        'request_code',
        'request_date',
        'requested_by_user_id',
        'purpose',
        'status',
        // Asegúrate de incluir aquí todos los campos que manejas
    ];
    
    /**
     * The attributes that should be cast to native types.
     * ESTO RESUELVE EL ERROR DE Call to a member function format() on string
     *
     * @var array
     */
    protected $casts = [
        'request_date' => 'datetime',
        'delivery_date' => 'datetime', // Asumimos que también tienes esta columna
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con el usuario que realiza la solicitud
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }
    
    // Si usas relaciones con RequestDetail, añádelas aquí.
    public function details()
    {
        return $this->hasMany(RequestDetail::class, 'request_id');
    }

    // Nota: Necesitarás importar el modelo User si no lo has hecho
    // use App\Models\User;
    // use App\Models\RequestDetail; // Si existe

}