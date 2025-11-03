<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RequestDeliveryDetail;
use App\Models\RequestModel;
use App\Models\RequestDetail;
use App\Http\Requests\StoreRequestDeliveryDetailRequest;
use App\Http\Requests\UpdateRequestDeliveryDetailRequest;

class DeliveryController extends Controller
{
    public function __construct()
    {
        // CORREGIDO: Almacenista es el rol principal en Entregas.
        $this->middleware('role:Almacenista|Administrador|Super Administrador');
    }

   /**
     * Muestra una lista de las solicitudes que ya han sido entregadas (status = 'Delivered').
     */
    public function index()
    {
        // Ahora mostramos las Solicitudes (RequestModel) que ya están en estado Delivered
        $deliveries = RequestModel::where('status', 'Delivered')
                              ->with(['requestedBy', 'approvedBy'])
                              ->latest()
                              ->paginate(10);
                              
        // La variable $deliveries contiene una colección de RequestModel
        return view('flows.deliveries.index', compact('deliveries'));
    }

    /**
     * Muestra el formulario para registrar la entrega de una solicitud.
     * Solo permite seleccionar solicitudes que están APROBADAS y NO ENTREGADAS.
     */
    public function create()
    {
        // 1. Obtener las Solicitudes Aprobadas y Pendientes de Entrega ('Approved')
        $approvedRequests = RequestModel::where('status', 'Approved')
            ->with('requestedBy')
            ->get();
            
        // Formatear los datos para el selector
        $requestList = $approvedRequests->map(function ($request) {
            return [
                'id' => $request->id,
                'code' => $request->request_code,
                'requester' => $request->requestedBy->name ?? 'Usuario Desconocido',
                'display' => $request->request_code . ' (Solicitado por: ' . ($request->requestedBy->name ?? 'Usuario Desconocido') . ')',
            ];
        });

        // Pasamos la lista de solicitudes aprobadas
        return view('flows.deliveries.create', compact('requestList'));
    }

     /**
     * Almacena una nueva entrega en la base de datos y finaliza la Solicitud.
     */
    public function store(StoreRequestDeliveryDetailRequest $request)
    {
        $validatedData = $request->validated();
        $requestId = $validatedData['request_id'];
        
        DB::beginTransaction();

        try {
            // 1. Encontrar la Solicitud y sus Detalles
            $materialRequest = RequestModel::with('details')->findOrFail($requestId);
            
            if ($materialRequest->status !== 'Approved') {
                 DB::rollBack();
                 return redirect()->back()->with('error', 'La solicitud debe estar en estado APROBADA para poder registrar la entrega.');
            }

            // 2. Registrar los detalles de entrega (uno por cada detalle de la solicitud)
            // Ya que el stock se descontó en RequestController::approve(), asumimos que la cantidad entregada
            // es igual a la cantidad entregada del detalle (quantity_delivered en RequestDetail).
            foreach ($materialRequest->details as $detail) {
                // Se crea un registro de entrega por cada ítem.
                RequestDeliveryDetail::create([
                    'request_detail_id' => $detail->id,
                    'delivered_by_user_id' => Auth::id(), 
                    'received_by_user_id' => $materialRequest->requested_by_user_id, // Solicitante
                    'quantity_delivered' => $detail->quantity_delivered, // Usamos la cantidad aprobada/descontada
                    'delivery_date' => now(),
                ]);
            }

            // 3. Actualizar el estado de la Solicitud a 'Delivered'
            $materialRequest->status = 'Delivered';
            $materialRequest->delivery_date = now();
            $materialRequest->save();

            DB::commit();
            
            // Redirigimos al show de la SOLICITUD
            return redirect()->route('flows.requests.show', $materialRequest->id)->with('success', 'Entrega de Solicitud ' . $materialRequest->request_code . ' registrada y finalizada.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Fallo al registrar la entrega. Mensaje: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', 'Error al registrar la entrega: ' . $e->getMessage());
        }
    }

   /**
     * Muestra los detalles de la solicitud que ya fue entregada.
     * @param RequestModel $materialRequest Inyectado por Route Model Binding (desde el ID de la solicitud).
     */
    public function show(RequestModel $materialRequest)
    {
        // Cargar las relaciones necesarias, incluyendo los detalles de la entrega real
        $materialRequest->load([
            'requestedBy', 
            'approvedBy', 
            'details.product', 
            // Necesitamos esta relación para cargar los datos de la entrega en la vista
            'details.deliveryDetails.deliveredBy' 
        ]);
        
        // Usaremos la vista show para mostrar el RequestModel completo, incluyendo los detalles de entrega
        return view('flows.deliveries.show', compact('materialRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequestDeliveryDetailRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
