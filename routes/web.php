<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController; // ¡Importante para redefinir el registro!
use App\Http\Controllers\ProductController;
use App\Http\Controllers\KitController;
use App\Http\Controllers\ProductPriceController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RequestController; 
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\KitUsageController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Rutas Web
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// --- RUTAS DE AUTENTICACIÓN Y PÁGINA DE INICIO ---
// 🔒 EXCLUIMOS EL REGISTRO PÚBLICO
Auth::routes(['register' => false]);
Route::get('/home', [HomeController::class, 'index'])->name('home');


// --- GRUPO DE RUTAS PROTEGIDAS (REQUIERE AUTENTICACIÓN) ---
Route::middleware(['auth'])->group(function () {

    // =======================================================================
    // 1. INVENTARIO CENTRAL (Prefijo: /inventory)
    // Permisos: 'productos_gestionar', 'entradas_ver_precios', 'productos_ver'
    // =======================================================================
    Route::prefix('inventory')->name('inventory.')->group(function () {

        // Entidad PRODUCTS: Gestión completa (CRUD) solo si tiene el permiso.
        // Los 'Solicitantes' solo tendrán acceso si les asignas 'productos_gestionar',
        // si no, necesitarías una ruta de sólo lectura separada con 'productos_ver'.
        Route::resource('products', ProductController::class)
            ->middleware('permission:productos_gestionar');

        // Entidad KITS: Gestión completa (CRUD)
        Route::resource('kits', KitController::class)
            ->except(['show'])
            ->middleware('permission:productos_gestionar');

        // Entidad PRODUCT_PRICES: Sólo registro y visualización de precios (Acceso sensible)
        Route::resource('prices', ProductPriceController::class)
            ->only(['index', 'store'])
            ->names('product_prices')
            ->middleware('permission:entradas_ver_precios');
    });


    // =======================================================================
    // 2. FLUJOS DE TRABAJO (Prefijo: /flows)
    // Permisos: 'entradas_registrar', 'salidas_solicitar', 'salidas_aprobar', 'salidas_entregar', etc.
    // =======================================================================
    Route::prefix('flows')->name('flows.')->group(function () {

        // --- FLUJO DE ENTRADA: PURCHASES (Completamente restringido)
        Route::resource('purchases', PurchaseController::class)
            ->middleware('permission:entradas_registrar'); // Incluye index, create, store, etc.
        
        // --- FLUJO DE SALIDA: REQUESTS (Solicitudes)
        Route::resource('requests', RequestController::class);
        // El index y show deben ser accesibles por 'salidas_ver_todas' y el solicitante.
        // El create y store solo por 'salidas_solicitar' o 'salidas_aprobar'.

        // 🔒 Gestión de Solicitudes: APROBACIÓN Y RECHAZO
        // Solo el Encargado de Inventario o Super Admin puede aprobar.
        Route::post('requests/{materialRequest}/approve', [ApprovalController::class, 'approve'])
            ->name('requests.approve')
            ->middleware('permission:salidas_aprobar');
        
        Route::post('requests/{materialRequest}/reject', [ApprovalController::class, 'reject'])
            ->name('requests.reject')
            ->middleware('permission:salidas_aprobar');
            
        // APROBACIONES (Vistas que muestran estados de aprobación) 
        Route::resource('approvals', ApprovalController::class)
            ->only(['index', 'update', 'show'])
            ->middleware('permission:salidas_aprobar');

        // ENTREGAS (Salida final que resta stock) 
        // Solo el encargado puede registrar entregas (y por ende restar stock).
        Route::resource('deliveries', DeliveryController::class)
            ->only(['index', 'create', 'store', 'show'])
            ->middleware('permission:salidas_entregar');

        // USO DE KITS (Registro de consumo) 
        // Accesible por cualquiera que pueda registrar un uso de kit.
        Route::resource('kit-usages', KitUsageController::class)
            ->only(['index', 'create', 'store'])
            ->names('kit_usages')
            ->middleware('permission:kits_registrar_uso');
    });


    // =======================================================================
    // 3. MÓDULOS MAESTROS Y CONFIGURACIÓN (Prefijo: /admin)
    // =======================================================================
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // 🔒 GESTIÓN DE USUARIOS Y REGISTRO (SOLO SUPER ADMINISTRADOR)
        Route::middleware('role:Super Administrador')->group(function () {
            
            // Entidad USERS (CRUD de usuarios sin destroy)
            Route::resource('users', UserController::class)->only(['index', 'create', 'edit', 'update']);
            
            // Redefinición de la ruta de registro, ahora es privada y protegida
            Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
            Route::post('register', [RegisterController::class, 'register']);

            // Si llegas a implementar RoleController:
            // Route::resource('roles', RoleController::class);
        });
        
        // 🔒 ENTIDADES MAESTRAS (Requiere permiso para gestionar cualquier maestro)
        // Esto permite un control de acceso más granular que solo el Super Admin.
        Route::middleware('permission:maestros_gestionar')->group(function () {
            
            Route::resource('suppliers', SupplierController::class);
            Route::resource('locations', LocationController::class);
            Route::resource('categories', CategoryController::class);
            Route::resource('units', UnitController::class);
        });

    });

});