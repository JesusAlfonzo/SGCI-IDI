<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // Ajusta este namespace si tu modelo User está en otra ubicación

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Resetear permisos cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =========================================================================
        // 2. Definición de Permisos
        // =========================================================================

        // --- Permisos de Maestros (categories, units, locations, suppliers)
        $masters = [
            'maestros_ver',
            'maestros_gestionar',
        ];

        // --- Permisos de Productos (products, kits)
        $products = [
            'productos_ver',
            'productos_gestionar',
            'kits_registrar_uso',
        ];

        // --- Permisos de Flujo de Entrada (purchases, product_prices)
        $entry_flow = [
            'entradas_ver',
            'entradas_registrar', // Registra purchases y SUMA a stock
            'entradas_ver_precios',
        ];

        // --- Permisos de Flujo de Salida (requests, request_approvals, request_delivery_details)
        $exit_flow = [
            'salidas_solicitar', // Registrar requests
            'salidas_aprobar', // Registrar request_approvals
            'salidas_entregar', // Registra delivery_details y RESTA a stock
            'salidas_ver_todas',
        ];

        // --- Permisos de Usuarios
        $users_management = [
            'usuarios_ver',
            'usuarios_gestionar',
            'roles_gestionar',
        ];

        // Combinar todos los permisos en un solo array
        $allPermissions = array_merge($masters, $products, $entry_flow, $exit_flow, $users_management);

        // Crear todos los permisos
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }


        // =========================================================================
        // 3. Creación de Roles y Asignación de Permisos
        // =========================================================================

        // Rol 1: Super Administrador (Acceso total)
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Administrador']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Rol 2: Encargado de Inventario
        $inventoryManagerRole = Role::firstOrCreate(['name' => 'Encargado Inventario']);
        $inventoryManagerRole->syncPermissions([
            'maestros_gestionar',
            'productos_gestionar',
            'entradas_registrar',
            'entradas_ver',
            'entradas_ver_precios',
            'salidas_aprobar',
            'salidas_entregar',
            'salidas_ver_todas',
            'kits_registrar_uso',
        ]);

        // Rol 3: Solicitante
        $requesterRole = Role::firstOrCreate(['name' => 'Solicitante']);
        $requesterRole->syncPermissions([
            'maestros_ver',
            'productos_ver',
            'salidas_solicitar',
            'kits_registrar_uso',
        ]);


        // =========================================================================
        // 4. Asignación del Rol al Usuario Inicial
        // =========================================================================

        // **IMPORTANTE:** Este código asignará el rol 'Super Administrador' al primer usuario
        // que encuentre con este email. Asegúrate de que el usuario ya existe en la DB.
        $user = User::where('email', 'snw@admin.com')->first();

        if ($user) {
            // Asigna el rol de máximo nivel
            $user->assignRole('Super Administrador');
        } else {
            // Opcional: Crear el usuario si no existe
            // User::firstOrCreate([
            //     'name' => 'Super Admin',
            //     'email' => 'admin@ejemplo.com',
            //     'password' => bcrypt('password'),
            // ])->assignRole('Super Administrador');
        }
    }
}
