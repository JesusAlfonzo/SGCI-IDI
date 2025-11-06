<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role; // Necesario para listar y asignar roles

// Importa los Requests si los vas a usar, sino usa el Request base.
// use App\Http\Requests\StoreUserRequest; 
// use App\Http\Requests\UpdateUserRequest; 

class UserController extends Controller
{
    public function __construct()
    {
        // 🔒 Protege todo el controlador: Solo el 'Super Administrador' puede acceder.
        $this->middleware('role:Super Administrador'); 
    }

    /**
     * Muestra una lista de los usuarios. (admin.users.index)
     */
    public function index()
    {
        // Se asegura de cargar los roles para la vista
        $users = User::with('roles')->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Muestra el formulario de creación (Redirige al registro). (admin.users.create)
     */
    public function create()
    {
        // Redirigimos a la vista intermedia para mantener el flujo de AdminLTE/Laravel UI.
        return view('admin.users.create');
    }
    
    // El método store() no se usa porque Laravel UI maneja el registro y la creación.
    // Si quieres asignar un rol por defecto al usuario recién creado por Laravel UI,
    // tendrías que modificar el método register() en App\Http\Controllers\Auth\RegisterController.

    /**
     * Muestra el formulario para editar el usuario y asignar roles. (admin.users.edit)
     */
    public function edit(User $user)
    {
        // Obtiene todos los roles para el formulario (Super Admin, Encargado, Solicitante)
        $roles = Role::pluck('name', 'id');
        
        // Obtiene los IDs de los roles que el usuario tiene actualmente
        $userRoles = $user->roles->pluck('id')->all(); 
        
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

/**
     * Actualiza la información del usuario y sincroniza los roles. (admin.users.update)
     */
    public function update(Request $request, User $user)
    {
        // 1. Validaciones
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        // 2. Actualiza datos básicos
        $user->update($request->only('name', 'email'));
        
        // 3. Preparación y Mapeo de Roles (¡LA CORRECCIÓN!)
        $rolesToSync = $request->filled('roles') ? $request->input('roles') : [];

        // Si hay IDs de rol para sincronizar, obtenemos sus nombres.
        if (!empty($rolesToSync)) {
            // Buscamos los roles en la base de datos usando los IDs
            // y extraemos solo sus nombres (ej: ['Super Administrador', 'Solicitante'])
            $roleNamesToSync = Role::whereIn('id', $rolesToSync)->pluck('name');
        } else {
            // Si el array está vacío (el usuario revocó todos los roles)
            $roleNamesToSync = [];
        }

        // 4. Sincroniza los roles (Spatie funciona mejor con nombres aquí)
        $user->syncRoles($roleNamesToSync);
        
        return redirect()->route('admin.users.index')->with('success', 'Usuario y roles actualizados.');
    }


    // Los métodos store, show y destroy se dejan vacíos ya que no son parte del flujo principal aquí
    public function store(Request $request) { /* No implementado */ }
    public function show(string $id) { /* No implementado */ }
    public function destroy(string $id) { /* No implementado */ }
}