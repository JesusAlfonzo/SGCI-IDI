<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;

class LocationController extends Controller
{
    public function __construct()
    {
        // Requiere rol Administrador o Super Administrador
        $this->middleware('role:Administrador|Super Administrador');
    }

   /**
     * Muestra una lista de ubicaciones.
     */
    public function index()
    {
        $locations = Location::orderBy('name')->paginate(10); 
        // La ruta de la vista es 'admin.locations.index'
        return view('admin.locations.index', compact('locations')); 
    }

    /**
     * Muestra el formulario para crear una nueva ubicación.
     */
    public function create()
    {
        return view('admin.locations.create');
    }

    /**
     * Almacena una ubicación recién creada.
     */
    public function store(StoreLocationRequest $request)
    {
        // El request debe validar que 'name' sea único
        Location::create($request->validated());

        return redirect()->route('admin.locations.index')->with('success', 'Ubicación creada exitosamente.');
    }

    /**
     * Muestra el formulario para editar la ubicación especificada.
     */
    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    /**
     * Actualiza la ubicación especificada.
     */
    public function update(UpdateLocationRequest $request, Location $location)
    {
        // El request debe validar que 'name' sea único, ignorando el ID actual
        $location->update($request->validated());

        return redirect()->route('admin.locations.index')->with('success', 'Ubicación actualizada exitosamente.');
    }

    /**
     * Elimina la ubicación especificada.
     */
    public function destroy(Location $location)
    {
        try {
            $location->delete();
            $message = 'Ubicación eliminada exitosamente.';
            $type = 'success';
        } catch (\Illuminate\Database\QueryException $e) {
            // Error si la ubicación está asociada a productos (FK Constraint)
            $message = 'Error: No se puede eliminar la ubicación porque tiene productos asociados.';
            $type = 'error';
        }

        return redirect()->route('admin.locations.index')->with($type, $message);
    }
}