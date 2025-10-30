<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;

class SupplierController extends Controller
{
    public function __construct()
    {
        // Requiere rol Administrador o Super Administrador
        $this->middleware('role:Administrador|Super Administrador');
    }

    /**
     * Muestra una lista de proveedores.
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->paginate(10); 
        return view('admin.suppliers.index', compact('suppliers')); 
    }

    /**
     * Muestra el formulario para crear un nuevo proveedor.
     */
    public function create()
    {
        // Opciones de Prioridad para el ENUM (Ejemplo basado en el diccionario: A, B, C)
        $priorities = ['A', 'B', 'C'];
        return view('admin.suppliers.create', compact('priorities'));
    }

    /**
     * Almacena un proveedor recién creado.
     */
    public function store(StoreSupplierRequest $request)
    {
        Supplier::create($request->validated());

        return redirect()->route('admin.suppliers.index')->with('success', 'Proveedor creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar el proveedor especificado.
     */
    public function edit(Supplier $supplier)
    {
        // Opciones de Prioridad
        $priorities = ['A', 'B', 'C'];
        return view('admin.suppliers.edit', compact('supplier', 'priorities'));
    }

    /**
     * Actualiza el proveedor especificado.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return redirect()->route('admin.suppliers.index')->with('success', 'Proveedor actualizado exitosamente.');
    }

    /**
     * Elimina el proveedor especificado.
     */
    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();
            $message = 'Proveedor eliminado exitosamente.';
            $type = 'success';
        } catch (\Illuminate\Database\QueryException $e) {
            // Error si el proveedor está asociado a compras (FK Constraint)
            $message = 'Error: No se puede eliminar el proveedor porque está asociado a registros de compras.';
            $type = 'error';
        }

        return redirect()->route('admin.suppliers.index')->with($type, $message);
    }
}