<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreRequestDeliveryDetailRequest;
use App\Http\Requests\UpdateRequestDeliveryDetailRequest;

class DeliveryController extends Controller
{
    public function __construct()
    {
        // Administrador y Super Administrador gestionan las compras/entradas
        $this->middleware('role:Administrador|Super Administrador');
    }

    public function index()
    {
        return "✅ Acceso permitido. Index de Compras (Administrador/Super Administrador)";
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
    public function store(StoreRequestDeliveryDetailRequest $request)
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
    public function update(UpdateRequestDeliveryDetailRequest $request, $id)
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
