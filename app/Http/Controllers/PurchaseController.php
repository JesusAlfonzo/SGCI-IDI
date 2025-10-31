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
    public function store(StorePurchaseRequest $request)
    {
        $validatedData = $request->validated();

        // 1. Iniciar la Transacción de Base de Datos
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
                'registered_by_user_id' => auth()->id(),
            ]);

            $details = $validatedData['details'];
            $now = now();
            // ID del proveedor (tomado de la cabecera)
            $supplierId = $purchase->supplier_id;

            // 3. Procesar los Detalles
            foreach ($details as $detail) {
                // Buscamos el producto para la actualización de stock
                $product = Product::find($detail['product_id']);

                // Calculamos los valores necesarios
                $unitCost = $detail['unit_cost'];
                $quantity = $detail['quantity'];
                $totalDetail = $unitCost * $quantity;

                // 3a. Guardar el Detalle de la Compra
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $quantity,

                    // Necesario por los campos obligatorios en tu BD:
                    'unit_cost' => $unitCost,
                    'unit_purchase_price' => $unitCost,

                    'total_detail' => $totalDetail,
                ]);

                // 3b. Actualizar Stock del Producto (INCREMENT)
                $product->increment('stock_actual', $quantity);

                // 3c. Registrar Precio de Compra (Histórico en ProductPrice)
                // Marcamos el precio anterior como no vigente
                ProductPrice::where('product_id', $detail['product_id'])
                    ->where('is_latest', true)
                    ->update(['is_latest' => false]);

                // Insertamos el nuevo precio de compra como el más reciente
                ProductPrice::create([
                    'product_id' => $detail['product_id'],
                    'price' => $unitCost,
                    'recorded_at' => $now,
                    'is_latest' => true,
                    'supplier_id' => $supplierId, // 🔑 CORRECCIÓN FINAL
                ]);
            }

            // 4. Confirmar la Transacción
            DB::commit();

            return redirect()->route('flows.purchases.index')->with('success', 'Compra registrada exitosamente con Código: ' . $purchaseCode);
        } catch (\Exception $e) {
            // 5. Revertir la Transacción en caso de error
            DB::rollBack();

            // Log y mensaje de error mejorado
            Log::error("Fallo al registrar compra. Mensaje: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            $userMessage = 'Error al registrar la compra. Revise el log del servidor para el detalle.';
            if (env('APP_DEBUG')) {
                $userMessage = 'Error de Base de Datos: ' . $e->getMessage();
            }

            return redirect()->back()->withInput()->with('error', $userMessage);
        }
    }
    /**
     * Muestra los detalles de una compra específica.
     */
    public function show(Purchase $purchase)
    {
        // Carga las relaciones necesarias para mostrar el detalle completo
        $purchase->load([
            'supplier',
            'registeredBy', // Usuario que registró la compra
            'details.product.unit' // Detalles, producto y su unidad
        ]);

        return view('flows.purchases.show', compact('purchase'));
    }
}
