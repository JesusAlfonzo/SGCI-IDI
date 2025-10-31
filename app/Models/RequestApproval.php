<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestApproval extends Model
{
    use HasFactory;
    
    // Asumo que tu tabla es 'request_approvals' o 'request_aprovals'

    // 🔑 CAMPOS ASIGNABLES
    protected $fillable = [
        'request_id',
        'user_id',
        'action', // Ej: 'APROBADO' o 'RECHAZADO'
        'comment',
        // otros campos...
    ];

    // 🔑 RELACIONES
    
    public function request()
    {
        return $this->belongsTo(RequestModel::class, 'request_id');
    }
    
    public function approver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}