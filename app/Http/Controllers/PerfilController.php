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
        return response()->json(Perfil::query()->latest()->get());
    }

    public function show(Perfil $perfil): JsonResponse
    {
        return response()->json($perfil);
    }

    public function update(Request $request, Perfil $perfil): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'usuario' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('perfiles')->ignore($perfil)],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('perfiles')->ignore($perfil)],
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
}