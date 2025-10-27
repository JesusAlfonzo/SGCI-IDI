<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', function(){
    return view('welcome');
})->name('welcome');

// --- RUTAS DE AUTENTICACIÓN Y PÁGINA DE INICIO ---
Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home');


// --- GRUPO DE RUTAS PROTEGIDAS (REQUIERE AUTENTICACIÓN) ---
Route::middleware(['auth'])->group(function () {

    // =======================================================================
    // 1. INVENTARIO CENTRAL (Prefijo: /inventory)
    // Coincide con la estructura de vistas: resources/views/inventory/...
    // =======================================================================
    Route::prefix('inventory')->name('inventory.')->group(function () {
        
        // Entidad PRODUCTS [cite: 1]
        Route::resource('products', ProductController::class); 

        // Entidad KITS (Extensión de productos) [cite: 1]
        Route::resource('kits', KitController::class)->except(['show']);

        // Entidad PRODUCT_PRICES (Historial de precios) [cite: 1]
        Route::resource('prices', ProductPriceController::class)->except(['edit', 'update', 'destroy'])->names('product_prices');
    });


    // =======================================================================
    // 2. FLUJOS DE TRABAJO (Prefijo: /flows)
    // Coincide con la estructura de vistas: resources/views/flows/...
    // =======================================================================
    Route::prefix('flows')->name('flows.')->group(function () {

        // FLUJO DE ENTRADA: PURCHASES [cite: 12]
        Route::resource('purchases', PurchaseController::class); 

        // FLUJO DE SALIDA: REQUESTS (Maestra) [cite: 18]
        Route::resource('requests', RequestController::class);

        // APROBACIONES (Relación 1:1 con requests) [cite: 1]
        Route::resource('approvals', ApprovalController::class)->only(['index', 'update', 'show']);

        // ENTREGAS (Salida final que resta stock) [cite: 1]
        Route::resource('deliveries', DeliveryController::class)->only(['index', 'create', 'store', 'show']);

        // USO DE KITS (Registro de consumo) [cite: 1]
        Route::resource('kit-usages', KitUsageController::class)->only(['index', 'create', 'store'])->names('kit_usages');
    });


    // =======================================================================
    // 3. MÓDULOS MAESTROS Y CONFIGURACIÓN (Prefijo: /admin)
    // Coincide con la estructura de vistas: resources/views/admin/...
    // =======================================================================
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Entidad USERS (Gestión de usuarios) [cite: 1, 5]
        Route::resource('users', UserController::class);

        // Entidades Maestras (SUPPLIERS, LOCATIONS, CATEGORIES, UNITS) [cite: 1, 5]
        Route::resource('suppliers', SupplierController::class);
        Route::resource('locations', LocationController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('units', UnitController::class);
    });
});