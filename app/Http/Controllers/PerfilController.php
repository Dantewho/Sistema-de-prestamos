<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Perfil::withCount('solicitudesRealizadas')->latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'usuario' => ['required', 'string', 'max:255', Rule::unique('perfiles', 'usuario')],
            'email' => ['required', 'email', 'max:255', Rule::unique('perfiles', 'email')],
            'tipo_usuario' => ['required', 'integer', 'between:1,3'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $data['password'] = Hash::make($data['password']);

        return response()->json(Perfil::create($data), 201);
    }

    public function show(Perfil $perfil): JsonResponse
    {
        return response()->json($perfil);
    }

    public function update(Request $request, Perfil $perfil): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'usuario' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('perfiles', 'usuario')->ignore($perfil->id)],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('perfiles', 'email')->ignore($perfil->id)],
            'tipo_usuario' => ['sometimes', 'required', 'integer', 'between:1,3'],
            'imagen_perfil' => ['sometimes', 'nullable', 'string', 'max:255'],
            'password' => ['sometimes', 'required', 'string', 'min:8'],
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $perfil->update($data);

        return response()->json($perfil->fresh());
    }

    public function destroy(Perfil $perfil): JsonResponse
    {
        $perfil->delete();

        return response()->json(status: 204);
    }
}
