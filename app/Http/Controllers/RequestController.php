<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\RequestModel;
use App\Models\RequestDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreRequestRequest;
use App\Http\Requests\UpdateRequestRequest;
use Carbon\Carbon;

class RequestController extends Controller
{
    public function __construct()
    {
        // Todos los roles logueados pueden ver sus solicitudes y el administrador ve todas.
        $this->middleware('role:Solicitante|Administrador|Super Administrador');
    }

    /**
     * Muestra una lista del recurso.
     */
    public function index()
    {
        // ... (sin cambios)
        $requests = RequestModel::with(['requestedBy'])->latest()->paginate(10);
        return view('flows.requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ... (sin cambios)
        $products = Product::where('stock_actual', '>', 0)
                            ->with('unit')
                            ->orderBy('name')
                            ->get(['id', 'name', 'unit_id', 'stock_actual']); 
        
        return view('flows.requests.create', compact('products'));
    }

    /**
     * Almacena una nueva solicitud de materiales.
     */
    public function store(StoreRequestRequest $request)
    {
        $validatedData = $request->validated();
        
        DB::beginTransaction();

        try {
            // PASO 1 y 2: Creación de la Solicitud
            $details = $validatedData['details'];
            $lastId = RequestModel::max('id') ?? 0;
            $requestCode = 'REQ-' . Carbon::now()->year . '-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

            $materialRequest = RequestModel::create([
                'request_code' => $requestCode,
                'request_date' => now(), 
                'requested_by_user_id' => auth()->id(), 
                'purpose' => $validatedData['purpose'] ?? null,
                'status' => 'Pending', 
                // 'delivery_date' => $validatedData['delivery_date'] ?? null, 
            ]);

            // PASO 3: Guardar los Detalles
            $materialRequest->details()->createMany($details);

            DB::commit();

            // Redirección implícita con el objeto del modelo. Laravel usará el ID para el parámetro de ruta 'request'.
            return redirect()->route('flows.requests.show', $materialRequest)->with('success', 'Solicitud ' . $requestCode . ' registrada exitosamente y está pendiente de aprobación.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Fallo al registrar solicitud. Mensaje: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $userMessage = env('APP_DEBUG') ? 'Error de Base de Datos: ' . $e->getMessage() : 'Error al registrar la solicitud. Revise el log del servidor para el detalle.';
            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }


    /**
     * Muestra los detalles de una solicitud específica.
     * @param \App\Models\RequestModel $request Inyectado automáticamente por Route Model Binding
     */
    public function show(RequestModel $request)
    {
        // CORRECCIÓN CLAVE 2: Se cambió el nombre de la variable de $requestModel a $request 
        // para que coincida con el nombre del parámetro de la ruta de recurso /{request} y 
        // asegurar el Model Binding.
        
        // Forzamos la recarga para asegurar que todas las relaciones estén disponibles, especialmente después de un commit.
        $materialRequest = RequestModel::with(['details.product.unit', 'requestedBy', 'approvedBy'])
                                       ->findOrFail($request->id);

        $statusInfo = $this->getStatusInfo($materialRequest->status);

        // Pasamos $materialRequest a la vista para evitar tener que cambiar la vista blade.
        return view('flows.requests.show', compact('materialRequest', 'statusInfo'));
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
    public function update(UpdateRequestRequest $request, $id)
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

    /**
     * Marca la solicitud como APROBADA y descuenta el stock.
     */
    public function approve(RequestModel $materialRequest)
    {
        // Solo procesar si está Pendiente
        if ($materialRequest->status !== 'Pending') {
            return redirect()->back()->with('error', 'La solicitud ya ha sido procesada.');
        }

        DB::beginTransaction();

        try {
            // CORRECCIÓN DE EFICIENCIA: Cargamos la relación del producto UNA SOLA VEZ antes del bucle.
            $materialRequest->load('details.product'); 
            $details = $materialRequest->details;
            
            foreach ($details as $detail) {
                // El producto ya está cargado.
                $product = $detail->product;
                
                // Asegurar que el producto existe antes de operar
                if (!$product) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Error: Producto no encontrado en uno de los detalles.');
                }
                
                // Verificar si hay suficiente stock
                if ($product->stock_actual < $detail->quantity_requested) {
                    DB::rollBack();
                    Log::warning("Intento de aprobar solicitud con stock insuficiente.", [
                        'request_id' => $materialRequest->id,
                        'product_id' => $product->id,
                        'stock_actual' => $product->stock_actual,
                        'cantidad_solicitada' => $detail->quantity_requested,
                    ]);
                    
                    return redirect()->back()->with('error', 'Error: Stock insuficiente para el producto ' . $product->name . '. Stock actual: ' . number_format($product->stock_actual, 0) . ', Solicitado: ' . number_format($detail->quantity_requested, 0));
                }
                
                // Descontar el stock y guardar
                $product->stock_actual -= $detail->quantity_requested;
                $product->save();

                // Actualizar la cantidad entregada en el detalle
                $detail->quantity_delivered = $detail->quantity_requested;
                $detail->save();
            }

            // 2. Actualizar la cabecera de la Solicitud
            $materialRequest->status = 'Approved';
            $materialRequest->approved_by_user_id = Auth::id(); // Usa el ID del usuario actual
            $materialRequest->approval_date = now(); 
            $materialRequest->save();

            DB::commit();

            return redirect()->route('flows.requests.show', $materialRequest)->with('success', 'Solicitud ' . $materialRequest->request_code . ' APROBADA y stock descontado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Fallo al aprobar solicitud. Mensaje: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Error al aprobar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Marca la solicitud como RECHAZADA.
     */
    public function reject(RequestModel $materialRequest)
    {
        // Solo procesar si está Pendiente
        if ($materialRequest->status !== 'Pending') {
            return redirect()->back()->with('error', 'La solicitud ya ha sido procesada.');
        }
        
        try {
            // 1. Actualizar la cabecera de la Solicitud
            $materialRequest->status = 'Rejected';
            $materialRequest->approved_by_user_id = Auth::id(); // Guardamos quién la rechazó
            $materialRequest->approval_date = now();
            $materialRequest->save();

            return redirect()->route('flows.requests.show', $materialRequest)->with('success', 'Solicitud ' . $materialRequest->request_code . ' RECHAZADA exitosamente.');

        } catch (\Exception $e) {
            Log::error("Fallo al rechazar solicitud. Mensaje: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Error al rechazar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Función auxiliar para obtener la información de estado.
     */
    private function getStatusInfo(?string $status): array
    {
        // Aceptamos null y lo tratamos como 'Unknown' si viene de registros antiguos
        $status = $status ?? 'Unknown';
        
        return match ($status) {
            'Pending' => ['text' => 'PENDIENTE', 'class' => 'warning'],
            'Approved' => ['text' => 'APROBADA', 'class' => 'info'],
            'Rejected' => ['text' => 'RECHAZADA', 'class' => 'danger'],
            'Delivered' => ['text' => 'ENTREGADA', 'class' => 'success'],
            default => ['text' => 'DESCONOCIDO', 'class' => 'secondary'],
        };
    }
}
