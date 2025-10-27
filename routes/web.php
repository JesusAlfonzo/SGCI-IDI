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
use App\Http\Controllers\HomeController; // Asumimos un HomeController

/*
|--------------------------------------------------------------------------
| Rutas Web
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar rutas web para tu aplicación. Estas
| rutas son cargadas por el RouteServiceProvider dentro de un grupo
| que contiene el grupo de middleware "web". ¡Ahora haz algo genial!
|
*/

Route::get('/', function(){
    return view('welcome');
})->name('welcome');

// --- RUTAS DE AUTENTICACIÓN Y PÁGINA DE INICIO ---
// Estas rutas son generadas por Laravel UI/AdminLTE para login, register, etc.
Auth::routes();

// Ruta principal después del login
Route::get('/home', [HomeController::class, 'index'])->name('home');


// --- GRUPO DE RUTAS PROTEGIDAS (ACCESO RESTRINGIDO) ---
// Todo lo que está aquí debe ser accedido por un usuario autenticado.
Route::middleware(['auth'])->group(function () {

    // =======================================================================
    // 1. MÓDULO DE INVENTARIO CENTRAL (PRODUCTS, KITS)
    // =======================================================================

    // Entidad PRODUCTS [cite: 1]
    Route::resource('products', ProductController::class); 

    // Entidad KITS (Extensión de productos) [cite: 1]
    Route::resource('kits', KitController::class)->except(['show']);

    // Entidad PRODUCT_PRICES (Historial de precios) [cite: 1]
    // Generalmente solo se listan (index), se crean/registran (store), y se ven (show).
    Route::resource('product_prices', ProductPriceController::class)->except(['edit', 'update', 'destroy']);


    // =======================================================================
    // 2. FLUJO DE ENTRADA (COMPRAS) [cite: 12]
    // =======================================================================

    // Entidades PURCHASES y PURCHASE_DETAILS [cite: 1, 5]
    // Usamos resource para la maestra de compras
    Route::resource('purchases', PurchaseController::class);


    // =======================================================================
    // 3. FLUJO DE SALIDA (SOLICITUDES Y CONSUMO) [cite: 18]
    // =======================================================================

    // Entidades REQUESTS y REQUEST_DETAILS [cite: 1]
    Route::resource('requests', RequestController::class);

    // Entidad REQUEST_APPROVALS (Relación 1:1 con requests) [cite: 1]
    // Usamos un resource simple para la gestión de aprobaciones
    Route::resource('approvals', ApprovalController::class)->only(['index', 'update', 'show']);

    // Entidad REQUEST_DELIVERY_DETAILS (Entrega final que RESTA stock) [cite: 1]
    // Usamos un resource simple para la gestión de entregas
    Route::resource('deliveries', DeliveryController::class)->only(['index', 'create', 'store', 'show']);

    // Entidad KIT_USAGES (Registro de uso individual) [cite: 1]
    // Generalmente solo se listan (index) y se registran (store)
    Route::resource('kit_usages', KitUsageController::class)->only(['index', 'create', 'store']);


    // =======================================================================
    // 4. MÓDULOS MAESTROS Y CONFIGURACIÓN
    // =======================================================================

    // Entidad SUPPLIERS [cite: 1, 5]
    Route::resource('suppliers', SupplierController::class);

    // Entidad LOCATIONS [cite: 1, 5]
    Route::resource('locations', LocationController::class);

    // Entidad CATEGORIES [cite: 1, 5]
    Route::resource('categories', CategoryController::class);

    // Entidad UNITS [cite: 1, 5]
    Route::resource('units', UnitController::class);

    // Entidad USERS (Para la gestión de usuarios, roles y permisos) [cite: 1, 5]
    Route::resource('users', UserController::class);

});