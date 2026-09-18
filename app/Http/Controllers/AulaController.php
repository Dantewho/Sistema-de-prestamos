<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AulaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Aula::with('edificio')->latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'edificio_id' => ['required', 'integer', 'exists:edificios,id'],
            'numero' => ['required', 'string', 'max:10', Rule::unique('aulas')->where(fn ($query) => $query->where('edificio_id', $request->integer('edificio_id')))],
            'descripcion' => ['nullable', 'string'],
        ]);

        return response()->json(Aula::create($data)->load('edificio'), 201);
    }

    public function show(Aula $aula): JsonResponse
    {
        return response()->json($aula->load('edificio'));
    }

    public function update(Request $request, Aula $aula): JsonResponse
    {
        $data = $request->validate([
            'edificio_id' => ['sometimes', 'required', 'integer', 'exists:edificios,id'],
            'numero' => ['sometimes', 'required', 'string', 'max:10', Rule::unique('aulas')->where(fn ($query) => $query->where('edificio_id', $request->integer('edificio_id', $aula->edificio_id)))->ignore($aula)],
            'descripcion' => ['sometimes', 'nullable', 'string'],
        ]);

        $aula->update($data);

        return response()->json($aula->fresh()->load('edificio'));
    }

    public function destroy(Aula $aula): JsonResponse
    {
        $aula->delete();

        return response()->json(status: 204);
    }
}