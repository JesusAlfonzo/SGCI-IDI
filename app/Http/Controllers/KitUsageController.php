<?php

namespace App\Http\Controllers;

use App\Models\Kit; // Importamos el modelo Kit
use App\Models\KitUsage; // Importamos el modelo KitUsage
use App\Models\User; // Para el formulario
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreKitUsageRequest;
use App\Http\Requests\UpdateKitUsageRequest;
use Illuminate\Support\Facades\DB; // Para transacciones

class KitUsageController extends Controller
{
    public function __construct()
    {
        // Nota: Cambiado a permiso para más flexibilidad (asumiendo que kits_registrar_uso es el permiso correcto)
        $this->middleware('permission:kits_registrar_uso')->only(['create', 'store']);
        $this->middleware('permission:kits_ver_uso')->only(['index', 'show']);
        $this->middleware('permission:kits_auditar_uso')->only(['edit', 'update', 'destroy']);
    }

    public function index()
    {
        $usages = KitUsage::with(['kit.product', 'usedBy'])
                          ->latest()
                          ->paginate(15);
        return view('flows.kit_usages.index', compact('usages'));
    }

    public function create()
    {
        // 🎯 Lógica para obtener SOLAMENTE los Kits existentes:
        // Consulta directamente la tabla 'kits' a través del modelo Kit.
        $kits = Kit::with('product')->get()->pluck('product.name', 'id');
        
        // Si quieres que el nombre sea el del producto más los usos disponibles:
        // $kits = Kit::with('product')->get()->mapWithKeys(function ($kit) {
        //     return [$kit->id => $kit->product->name . ' (Usos restantes: ' . $kit->total_usages . ')'];
        // });
        
        // ... (resto del código del método create)
        return view('flows.kit_usages.create', compact('kits'));
    }

    /**
     * Almacena un nuevo registro de uso y disminuye el stock de USAGES.
     */
    public function store(StoreKitUsageRequest $request)
    {
        // DB::transaction asegura atomicidad: si falla, se revierte.
        try {
            DB::beginTransaction();

            $kitId = $request->input('kit_id');
            $kit = Kit::lockForUpdate()->findOrFail($kitId); // Bloqueo de fila para evitar condiciones de carrera

            // 1. Verificar si quedan usos disponibles
            if ($kit->total_usages <= 0) {
                DB::rollBack();
                return redirect()->back()->with('error', '⚠️ El kit ' . $kit->product->name . ' no tiene usos disponibles.')->withInput();
            }

            // 2. Decrementar el total_usages del kit
            $kit->decrement('total_usages');
            
            // 3. Registrar el uso del kit
            KitUsage::create([
                'kit_id' => $kitId,
                'used_by_user_id' => auth()->id(), // Usamos el usuario logueado
                'usage_date' => $request->input('usage_date'),
                'notes' => $request->input('purpose'), // Mapea 'purpose' a 'notes'
            ]);

            DB::commit();

            return redirect()->route('flows.kit_usages.index')
                             ->with('success', '✅ Uso de kit ' . $kit->product->name . ' registrado exitosamente. Usos restantes: ' . $kit->fresh()->total_usages);

        } catch (\Exception $e) {
            DB::rollBack();
            // Esto captura errores inesperados, como fallos de base de datos
            return redirect()->back()->with('error', '❌ Error al registrar el uso del kit. Intente de nuevo.')->withInput();
        }
    }
    
    // show, edit, update, destroy se dejan vacíos por ahora
    public function show(KitUsage $kitUsage) { /* Lógica de vista */ }
    public function edit(KitUsage $kitUsage) { /* Lógica de vista */ }
    public function update(UpdateKitUsageRequest $request, KitUsage $kitUsage) { /* Lógica de actualización */ }
    public function destroy(KitUsage $kitUsage) { /* Lógica de eliminación */ }
}