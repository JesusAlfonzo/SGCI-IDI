<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreKitUsageRequest;
use App\Http\Requests\UpdateKitUsageRequest;

class KitUsageController extends Controller
{
    public function __construct()
    {
        // Solicitantes registran el uso, Administradores lo auditan.
        $this->middleware('role:Solicitante|Administrador|Super Administrador');
    }

    public function index()
    {
        return "✅ Acceso permitido. Index de Usos de Kits (Todos los roles)";
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKitUsageRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKitUsageRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
