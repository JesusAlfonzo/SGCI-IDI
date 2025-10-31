<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestModel;
use App\Models\RequestDetail; // 🔑 CORRECCIÓN 1: Importar el modelo de detalle
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreRequestRequest;
use App\Http\Requests\UpdateRequestRequest;

class RequestController extends Controller
{
    public function __construct()
    {
        // Todos los roles logueados pueden ver sus solicitudes y el administrador ve todas.
        $this->middleware('role:Solicitante|Administrador|Super Administrador');
    }

    public function index()
    {
        $requests = RequestModel::with('requestedBy')
                                       ->orderByDesc('request_date')
                                       ->paginate(10);
                                       
        return view('flows.requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 🔑 CORRECCIÓN 2: Eliminar $departments ya que no se está usando este módulo.
        
        // Solo productos con stock > 0 pueden ser solicitados
        $products = Product::where('stock_actual', '>', 0)
                           ->with('unit')
                           ->orderBy('name')
                           ->get(['id', 'name', 'unit_id', 'stock_actual']); 
        
        // Pasamos solo $products a la vista.
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
            // Generar el código de solicitud
            $requestCode = 'REQ-' . date('Ymd') . '-' . rand(100, 999);

            // 1. Crear la Cabecera de la Solicitud
            $materialRequest = RequestModel::create([
                'request_code' => $requestCode,
                'request_date' => $validatedData['request_date'],
                'requested_by_user_id' => auth()->id(),
                'purpose' => $validatedData['purpose'] ?? null,
                // 🔑 CORRECCIÓN: Cambiar 'PENDIENTE' a 'Pending' para coincidir con el ENUM de la DB
                'status' => 'Pending', 
                // otros campos obligatorios en tu BD...
            ]);

            // ... el resto del código es correcto ...
            
            DB::commit();

            return redirect()->route('flows.requests.index')->with('success', 'Solicitud ' . $requestCode . ' registrada exitosamente y está pendiente de aprobación.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Fallo al registrar solicitud. Mensaje: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            $userMessage = env('APP_DEBUG') ? 'Error de Base de Datos: ' . $e->getMessage() : 'Error al registrar la solicitud. Revise el log del servidor para el detalle.';

            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
}
