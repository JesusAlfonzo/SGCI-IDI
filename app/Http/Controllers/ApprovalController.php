<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RequestModel; // Importar el modelo principal
use App\Http\Requests\StoreRequestApprovalRequest;
use App\Http\Requests\UpdateRequestApprovalRequest;

class ApprovalController extends Controller
{
    public function __construct()
    {
        // Solo roles que pueden aprobar solicitudes
        $this->middleware('role:Administrador|Super Administrador');
    }

    /**
     * Muestra una lista de las solicitudes PENDIENTES de aprobación.
     */
    public function index()
    {
        $pendingRequests = RequestModel::where('status', 'Pending')
                                         ->with('requestedBy')
                                         ->latest()
                                         ->paginate(10);
        
        // Redirigimos a una nueva vista específica para Aprobaciones
        return view('flows.approvals.index', compact('pendingRequests'));
    }

   /**
     * Procesa la APROBACIÓN de una solicitud y descuenta el stock.
     */
    public function approve(Request $request, RequestModel $materialRequest)
    {
        if ($materialRequest->status !== 'Pending') {
             return redirect()->back()->with('error', 'La solicitud ya ha sido procesada.');
        }

        DB::beginTransaction();
        try {
            $materialRequest->load('details.product'); 
            
            foreach ($materialRequest->details as $detail) {
                $product = $detail->product;
                
                if (!$product) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Error: Producto no encontrado en uno de los detalles.');
                }
                
                // 1. Verificar stock
                if ($product->stock_actual < $detail->quantity_requested) {
                    DB::rollBack();
                    $errorMsg = 'Error: Stock insuficiente para el producto ' . $product->name . 
                                 '. Stock actual: ' . number_format($product->stock_actual, 0) . 
                                 ', Solicitado: ' . number_format($detail->quantity_requested, 0);
                    return redirect()->back()->with('error', $errorMsg);
                }
                
                // 2. Descontar stock
                $product->stock_actual -= $detail->quantity_requested;
                $product->save();

                // 3. ¡CORRECCIÓN CRÍTICA! Se ELIMINA la actualización de quantity_delivered en request_details.
                // Esta columna no existe en 'request_details' y causaba el error.
                // La cantidad entregada real se registrará en 'request_delivery_details' más adelante.
                // $detail->quantity_delivered = $detail->quantity_requested; 
                // $detail->save(); 
            }

            // 4. Actualizar la cabecera de la Solicitud
            $materialRequest->status = 'Approved';
            $materialRequest->approved_by_user_id = Auth::id(); // Asumiendo que esta columna existe en DB
            $materialRequest->approval_date = now();           // Asumiendo que esta columna existe en DB
            $materialRequest->save();

            DB::commit();
            return redirect()->route('flows.requests.show', $materialRequest)->with('success', 'Solicitud ' . $materialRequest->request_code . ' APROBADA y stock descontado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Fallo al aprobar solicitud. Mensaje: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // Mensaje de error ajustado para ser más informativo en producción
            $userMessage = env('APP_DEBUG') ? 'Error al aprobar la solicitud: ' . $e->getMessage() : 'Error interno al aprobar la solicitud. Revise los logs.';
            return redirect()->back()->with('error', $userMessage);
        }
    }

    /**
     * Procesa el RECHAZO de una solicitud.
     */
    public function reject(Request $request, RequestModel $materialRequest)
    {
        if ($materialRequest->status !== 'Pending') {
            return redirect()->back()->with('error', 'La solicitud ya ha sido procesada.');
        }
        
        try {
            // Se asume que el modal envía 'rejection_reason'. Validamos y usamos el input.
            $request->validate([
                'rejection_reason' => 'nullable|string|max:500', 
            ]);

            $materialRequest->status = 'Rejected';
            
            // ¡CORRECCIÓN CRÍTICA! Se usa la columna 'reason' que existe en tu tabla 'requests'.
            $materialRequest->reason = $request->input('rejection_reason', 'Rechazado por Administrador sin motivo específico.'); 
            
            $materialRequest->approved_by_user_id = Auth::id(); // Asumiendo que esta columna existe en DB
            $materialRequest->approval_date = now();           // Asumiendo que esta columna existe en DB
            $materialRequest->save();

            return redirect()->route('flows.requests.show', $materialRequest)->with('success', 'Solicitud ' . $materialRequest->request_code . ' RECHAZADA exitosamente.');

        } catch (\Exception $e) {
            Log::error("Fallo al rechazar solicitud. Mensaje: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Error al rechazar la solicitud: ' . $e->getMessage());
        }
    }
    
    // Dejamos las funciones de recursos que especificaste en las rutas, aunque vacías
    public function show(string $id) { /* Podría usarse para ver una solicitud pendiente */ }
    public function update(UpdateRequestApprovalRequest $request, $id) { /* Podría usarse para modificar la cantidad aprobada */ }
}