<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RequestModel; 
use App\Models\RequestDeliveryDetail; // Importado
use App\Models\User; // Importado
// Asegúrate de importar cualquier otro modelo que necesites, como Product.

class DeliveryController extends Controller
{
    /**
     * Define el middleware de acceso.
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Si usas roles, descomenta y ajusta el middleware:
        // $this->middleware('role:Almacenista|Administrador|Super Administrador');
    }

    /**
     * Muestra el historial de solicitudes YA ENTREGADAS (Delivered).
     * Corresponde a tu vista 'index'.
     */
    public function index()
    {
        $deliveries = RequestModel::with('requestedBy', 'approvedBy')
            ->where('status', 'Delivered')
            ->orderBy('delivery_date', 'desc')
            ->paginate(15, ['*'], 'deliveries_page'); // Paginación con nombre

        // La vista espera la variable $deliveries
        return view('flows.deliveries.index', compact('deliveries'));
    }

    /**
     * Muestra la bandeja de solicitudes APROBADAS PENDIENTES de entrega.
     * Corresponde a tu vista 'create'.
     */
    public function create()
    {
        $approvedRequests = RequestModel::with('requestedBy')
            ->where('status', 'Approved')
            ->orderBy('approval_date', 'asc')
            ->get();
            
        // Preparamos la lista para el <select> y la tabla de la vista 'create'
        $requestList = $approvedRequests->map(function ($request) {
            return [
                'id' => $request->id,
                'code' => $request->request_code,
                'requester' => $request->requestedBy->name ?? 'Desconocido',
                'purpose' => $request->purpose, // Añadido purpose para la tabla de abajo
                'approval_date' => $request->approval_date, // Añadido fecha para la tabla de abajo
                'display' => "[{$request->request_code}] - Solicitante: {$request->requestedBy->name} - Propósito: " . \Illuminate\Support\Str::limit($request->purpose, 30),
            ];
        });

        // La vista espera la variable $requestList
        return view('flows.deliveries.create', compact('requestList'));
    }

    /**
     * Procesa la entrega final de una solicitud seleccionada (POST al /deliveries).
     */
    public function store(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:requests,id',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $materialRequest = RequestModel::with('details')->findOrFail($request->request_id);

        if ($materialRequest->status !== 'Approved') {
            return redirect()->back()->with('error', 'La solicitud ya ha sido procesada o no está aprobada.');
        }

        DB::beginTransaction();
        try {
            $currentUserId = Auth::id();
            $now = now();

            // 1. Registrar los detalles de la entrega en la tabla request_delivery_details
            foreach ($materialRequest->details as $detail) {
                
                RequestDeliveryDetail::create([
                    'request_id' => $materialRequest->id,
                    'product_id' => $detail->product_id,
                    'quantity_delivered' => $detail->quantity_requested, // Cantidad aprobada = Cantidad entregada
                    'delivered_by_user_id' => $currentUserId, // Usuario de Almacén
                    'received_by_user_id' => $materialRequest->requested_by_user_id, // Receptor (el solicitante)
                    'delivery_date' => $now,
                    'delivery_notes' => $request->notes, 
                ]);
            }

            // 2. Actualizar la cabecera de la Solicitud
            $materialRequest->status = 'Delivered';
            $materialRequest->delivery_date = $now;
            $materialRequest->warehouse_staff_id = $currentUserId; // Registrar al personal de almacén
            $materialRequest->save();

            DB::commit();
            return redirect()->route('flows.deliveries.index')->with('success', 'Solicitud ' . $materialRequest->request_code . ' marcada como ENTREGADA exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Fallo al entregar solicitud. Mensaje: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $userMessage = env('APP_DEBUG') ? 'Error al procesar la entrega: ' . $e->getMessage() : 'Error interno al procesar la entrega. Revise los logs.';
            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }
    
    /**
     * Muestra el detalle de una solicitud entregada.
     */
    public function show(RequestModel $delivery)
    {
        // Se asume que en la ruta se pasa el ID de RequestModel
        $delivery->load(['requestedBy', 'warehouseStaff', 'deliveryDetails.product']);
        
        // Vista para mostrar los detalles de la entrega, incluyendo los productos y quién entregó/recibió
        return view('flows.deliveries.show', compact('delivery'));
    }
}