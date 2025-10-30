<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __construct()
    {
        // Requiere rol Administrador o Super Administrador
        $this->middleware('role:Administrador|Super Administrador');
    }

    public function index()
    {
        $units = Unit::orderBy('name')->paginate(10); 
        // La ruta de la vista es 'admin.units.index'
        return view('admin.units.index', compact('units')); 
    }

    /**
     * Muestra el formulario para crear una nueva unidad.
     */
    public function create()
    {
        return view('admin.units.create');
    }

    /**
     * Almacena una unidad recién creada.
     */
    public function store(StoreUnitRequest $request)
    {
        Unit::create($request->validated());

        return redirect()->route('admin.units.index')->with('success', 'Unidad de medida creada exitosamente.');
    }

    // El método show generalmente no se usa para entidades maestras simples.
    // public function show(Unit $unit) { ... }

    /**
     * Muestra el formulario para editar la unidad especificada.
     */
    public function edit(Unit $unit)
    {
        return view('admin.units.edit', compact('unit'));
    }

    /**
     * Actualiza la unidad especificada.
     */
    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        $unit->update($request->validated());

        return redirect()->route('admin.units.index')->with('success', 'Unidad de medida actualizada exitosamente.');
    }

    /**
     * Elimina la unidad especificada.
     */
    public function destroy(Unit $unit)
    {
        try {
            $unit->delete();
            $message = 'Unidad de medida eliminada exitosamente.';
            $type = 'success';
        } catch (\Illuminate\Database\QueryException $e) {
            // Error si la unidad está asociada a productos (FK Constraint)
            $message = 'Error: No se puede eliminar la unidad porque está siendo utilizada por productos.';
            $type = 'error';
        }

        return redirect()->route('admin.units.index')->with($type, $message);
    }
}