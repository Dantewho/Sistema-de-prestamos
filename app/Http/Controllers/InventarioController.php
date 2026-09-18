<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Inventario::latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'descripcion' => ['nullable', 'string'],
        ]);

        return response()->json(Inventario::create($data), 201);
    }

    public function show(Inventario $inventario): JsonResponse
    {
        return response()->json($inventario);
    }

    public function update(Request $request, Inventario $inventario): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'cantidad' => ['sometimes', 'required', 'integer', 'min:0'],
            'descripcion' => ['sometimes', 'nullable', 'string'],
        ]);

        $inventario->update($data);

        return response()->json($inventario->fresh());
    }

    public function destroy(Inventario $inventario): JsonResponse
    {
        $inventario->delete();

        return response()->json(status: 204);
    }
}