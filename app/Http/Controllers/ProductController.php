<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Location;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
public function __construct()
    {
        // Administrador y Super Administrador gestionan el inventario principal
        $this->middleware('role:Administrador|Super Administrador');
    }

    /**
     * Muestra una lista de productos.
     */
    public function index()
    {
        // Carga relaciones para evitar consultas N+1 y ordena por nombre
        $products = Product::with(['category', 'unit', 'location'])
                            ->orderBy('name')
                            ->paginate(15); 
                            
        return view('inventory.products.index', compact('products')); 
    }

    /**
     * Muestra el formulario para crear un nuevo producto.
     */
    public function create()
    {
        // Obtener listas para los SELECTs
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('inventory.products.create', compact('categories', 'units', 'locations'));
    }

    /**
     * Almacena un producto recién creado.
     */
    public function store(StoreProductRequest $request)
    {
        // 1. La validación ya ocurrió en StoreProductRequest.
        
        // 2. Crea el producto.
        Product::create($request->validated());

        // 3. Redirige con un mensaje de éxito.
        return redirect()->route('inventory.products.index')->with('success', 'Producto creado y stock inicial registrado exitosamente.');
    }

    /**
     * Muestra el formulario para editar el producto especificado.
     */
    public function edit(Product $product)
    {
        // Obtener listas para los SELECTs
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('inventory.products.edit', compact('product', 'categories', 'units', 'locations'));
    }

    /**
     * Actualiza el producto especificado.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        // Solo actualizamos los campos validados
        $product->update($request->validated());

        return redirect()->route('inventory.products.index')->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Elimina el producto especificado.
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            $message = 'Producto eliminado exitosamente.';
            $type = 'success';
        } catch (\Illuminate\Database\QueryException $e) {
            // Este error ocurre si el producto está asociado a detalles de compra o solicitud
            $message = 'Error: No se puede eliminar el producto porque está asociado a registros de transacciones (compras/solicitudes).';
            $type = 'error';
        }

        return redirect()->route('inventory.products.index')->with($type, $message);
    }
}