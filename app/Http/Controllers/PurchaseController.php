<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Http\Requests\StorePurchaseDetailRequest;

class PurchaseController extends Controller
{
public function __construct()
    {
        // Administrador y Super Administrador gestionan las compras/entradas
        $this->middleware('role:Administrador|Super Administrador');
    }

    /**
     * Muestra una lista de compras.
     */
    public function index()
    {
        $purchases = Purchase::with('supplier')
                             ->orderByDesc('purchase_date')
                             ->orderByDesc('id')
                             ->paginate(10);
                             
        return view('flows.purchases.index', compact('purchases')); 
    }

    /**
     * Muestra el formulario para crear una nueva compra.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        // Solo productos no Kits pueden ser comprados directamente (a menos que la lógica lo permita)
        $products = Product::where('is_kit', false)->with('unit')->orderBy('name')->get(['id', 'name', 'unit_id', 'stock_actual']); 
        
        return view('flows.purchases.create', compact('suppliers', 'products'));
    }

    /**
     * Almacena una nueva compra y sus detalles, actualizando el inventario.
     */
/**
     * Almacena una nueva compra y sus detalles, actualizando el inventario.
     */
    public function store(StorePurchaseRequest $request)
    {
        $validatedData = $request->validated();
        
        DB::beginTransaction();

        try {
            // Generar el código de compra
            $purchaseCode = 'OC-' . time() . '-' . rand(100, 999);

            // 2. Crear la Cabecera de la Compra
            $purchase = Purchase::create([
                'supplier_id' => $validatedData['supplier_id'],
                'purchase_date' => $validatedData['purchase_date'],
                'invoice_number' => $validatedData['invoice_number'],
                'total_amount' => $validatedData['total_amount'], 
                'purchase_code' => $purchaseCode,
                'registered_by_user_id' => auth()->id(), // 🔑 AGREGAR EL ID DEL USUARIO
            ]);

            $details = $validatedData['details']; 
            $now = now();

            // 3. Procesar los Detalles
            foreach ($details as $detail) {
                $product = Product::find($detail['product_id']);
                
                // 3a. Guardar el Detalle de la Compra
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'unit_cost' => $detail['unit_cost'],
                    'total_detail' => $detail['quantity'] * $detail['unit_cost'],
                ]);

                // 3b. Actualizar Stock del Producto (INCREMENT)
                $product->increment('stock_actual', $detail['quantity']);

                // 3c. Actualizar Precio de Compra (Histórico)
                ProductPrice::where('product_id', $detail['product_id'])
                            ->where('is_latest', true)
                            ->update(['is_latest' => false]);

                ProductPrice::create([
                    'product_id' => $detail['product_id'],
                    'price' => $detail['unit_cost'], 
                    'recorded_at' => $now,
                    'is_latest' => true,
                ]);
            }

            // 4. Confirmar la Transacción
            DB::commit();

            return redirect()->route('flows.purchases.index')->with('success', 'Compra registrada exitosamente con Código: ' . $purchaseCode);

        } catch (\Exception $e) {
            // 5. Revertir la Transacción en caso de error
            DB::rollBack();
            
            Log::error("Fallo al registrar compra. Mensaje: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            $userMessage = 'Error al registrar la compra. Revise el log del servidor para el detalle.';
            if (env('APP_DEBUG')) {
                 $userMessage = 'Error de Base de Datos: ' . $e->getMessage();
            }

            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }
}