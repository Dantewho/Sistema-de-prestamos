<?php

namespace App\Http\Controllers;

use App\Models\Edificio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EdificioController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Edificio::with('aulas')->latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:10', 'unique:edificios,nombre'],
            'descripcion' => ['nullable', 'string'],
        ]);

        return response()->json(Edificio::create($data), 201);
    }

    public function show(Edificio $edificio): JsonResponse
    {
        return response()->json($edificio->load('aulas'));
    }

    public function update(Request $request, Edificio $edificio): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:10', Rule::unique('edificios')->ignore($edificio)],
            'descripcion' => ['sometimes', 'nullable', 'string'],
        ]);

        $edificio->update($data);

        return response()->json($edificio->fresh());
    }

    public function destroy(Edificio $edificio): JsonResponse
    {
        $edificio->delete();

        return response()->json(status: 204);
    }
}