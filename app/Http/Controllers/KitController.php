<?php

namespace App\Http\Controllers;

use App\Models\Kit;
use App\Models\Product; // Para seleccionar el producto base
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreKitRequest;
use App\Http\Requests\UpdateKitRequest;

class KitController extends Controller
{
    public function __construct()
    {
        // Se sugiere usar el permiso 'productos_gestionar' para crear kits,
        // ya que los kits son una extensión de los productos.
        $this->middleware('permission:productos_gestionar'); 
    }

    public function index()
    {
        $kits = Kit::with('product')->paginate(15);
        return view('inventory.kits.index', compact('kits'));
    }

    public function create()
    {
        // Obtener productos que *aún no* son kits
        $products = Product::whereDoesntHave('kit')->pluck('name', 'id');
        return view('inventory.kits.create', compact('products'));
    }

    public function store(StoreKitRequest $request)
    {
        Kit::create($request->validated());

        // 💡 NOTA: Después de crear el kit, el siguiente paso lógico es
        // redirigir a una vista para definir los componentes (tabla kit_components).
        
        return redirect()->route('inventory.kits.index')
                         ->with('success', '✅ Kit creado exitosamente. ¡No olvide asignar sus componentes!');
    }

    public function edit(Kit $kit)
    {
        // 1. Productos disponibles para ser componentes (todos los que NO son kits)
        $componentProducts = Product::whereDoesntHave('kit')->pluck('name', 'id');
        
        // 2. Componentes actuales del kit, cargados desde la tabla pivote
        $currentComponents = $kit->components()->get(); 
        
        return view('inventory.kits.edit', compact('kit', 'componentProducts', 'currentComponents'));
    }

    public function update(UpdateKitRequest $request, Kit $kit)
    {
        $kit->update($request->validated());
        
        return redirect()->route('inventory.kits.index')
                         ->with('success', '✅ Kit actualizado exitosamente.');
    }

    public function destroy(Kit $kit)
    {
        $kitName = $kit->product->name;
        $kit->delete();
        
        return redirect()->route('inventory.kits.index')
                         ->with('success', '✅ Kit ' . $kitName . ' eliminado exitosamente.');
    }

    /**
     * Almacena/Actualiza los componentes de un kit (Petición AJAX/Formulario POST).
     * NOTA: Esto requiere que configures esta ruta manualmente en web.php.
     */
    public function syncComponents(Request $request, Kit $kit)
    {
        $request->validate([
            'components' => 'required|array',
            'components.*.product_id' => 'required|exists:products,id',
            'components.*.quantity' => 'required|integer|min:1',
        ]);

        $componentsToSync = [];
        foreach ($request->components as $component) {
            $componentsToSync[$component['product_id']] = ['quantity' => $component['quantity']];
        }

        // 🎯 Sincronizar: Adjunta, desvincula y actualiza la tabla pivote kit_components.
        $kit->components()->sync($componentsToSync); 

        return redirect()->back()->with('success', '✅ Composición del Kit actualizada exitosamente.');
    }
}