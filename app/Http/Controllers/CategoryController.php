<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function __construct()
    {
        // Requiere rol Administrador o Super Administrador
        $this->middleware('role:Administrador|Super Administrador');
    }

    public function index()
    {
        // 1. Obtiene todas las categorías, ordenadas por nombre y paginadas.
        $categories = Category::orderBy('name')->paginate(10);

        // 2. Retorna la vista y pasa las categorías.
        // La ruta de la vista debe ser 'admin.categories.index' según tu estructura.
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
return view('admin.categories.create');    }

    /**
     * Store a newly created resource in storage.
     */
public function store(StoreCategoryRequest $request)
    {
        // 1. La validación ya ocurrió en StoreCategoryRequest.
        
        // 2. Crea la categoría usando solo los datos validados.
        Category::create($request->validated());

        // 3. Redirige con un mensaje de éxito.
        return redirect()->route('admin.categories.index')->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Muestra la categoría especificada. (Opcional para Maestros)
     */

    /**
     * Display the specified resource.
     */
public function show(Category $category)
    {
        // En entidades simples como categorías, show se usa poco, pero es útil.
        // return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
public function edit(Category $category)
    {
        // Retorna la vista de edición, pasando el objeto de categoría.
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(UpdateCategoryRequest $request, Category $category)
    {
        // 1. La validación ya ocurrió en UpdateCategoryRequest.
        
        // 2. Actualiza la categoría con los datos validados.
        $category->update($request->validated());

        // 3. Redirige con un mensaje de éxito.
        return redirect()->route('admin.categories.index')->with('success', 'Categoría actualizada exitosamente.');
    }

 /**
     * Elimina la categoría especificada del almacenamiento.
     */
    public function destroy(Category $category)
    {
        // 1. Verificar si la categoría está en uso (Lógica de negocio opcional, pero recomendada)
        // Por ahora, asumimos que no tiene restricciones. Si tiene, se usa try-catch.

        try {
            $category->delete();
            $message = 'Categoría eliminada exitosamente.';
            $type = 'success';
        } catch (\Illuminate\Database\QueryException $e) {
            // Este error ocurre si la categoría está asociada a productos (FK Constraint)
            $message = 'Error: No se puede eliminar la categoría porque está siendo utilizada por productos.';
            $type = 'error';
        }

        // 2. Redirige con el mensaje.
        return redirect()->route('admin.categories.index')->with($type, $message);
    }
}
