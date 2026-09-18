<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SolicitudController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Solicitud::with(['solicitante', 'prestador', 'aula.edificio', 'inventario'])->latest();

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado'));
        }

        if ($request->boolean('mis_solicitudes')) {
            $query->where('usuario_solicitante_id', $request->user()->id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());
        $data['usuario_solicitante_id'] = $request->user()->id;
        $data['estado'] = 'pendiente';

        return response()->json(Solicitud::create($data)->load(['solicitante', 'aula.edificio', 'inventario']), 201);
    }

    public function show(Solicitud $solicitud): JsonResponse
    {
        return response()->json($solicitud->load(['solicitante', 'prestador', 'aula.edificio', 'inventario']));
    }

    public function update(Request $request, Solicitud $solicitud): JsonResponse
    {
        $data = $request->validate([
            'usuario_prestador_id' => ['sometimes', 'nullable', 'integer', 'exists:perfiles,id'],
            'fecha_inicio' => ['sometimes', 'required', 'date'],
            'fecha_fin' => ['sometimes', 'nullable', 'date', 'after_or_equal:fecha_inicio'],
            'estado' => ['sometimes', Rule::in(['pendiente', 'activa', 'finalizada', 'cancelada'])],
            'descripcion' => ['sometimes', 'nullable', 'string'],
        ]);

        $solicitud->update($data);

        return response()->json($solicitud->fresh()->load(['solicitante', 'prestador', 'aula.edificio', 'inventario']));
    }

    public function destroy(Solicitud $solicitud): JsonResponse
    {
        $solicitud->delete();

        return response()->json(status: 204);
    }

    private function rules(): array
    {
        return [
            'tipo_solicitud' => ['required', Rule::in(['aula', 'inventario'])],
            'aula_id' => ['required_if:tipo_solicitud,aula', 'nullable', 'integer', 'exists:aulas,id'],
            'inventario_id' => ['required_if:tipo_solicitud,inventario', 'nullable', 'integer', 'exists:inventario,id'],
            'cantidad' => ['required_if:tipo_solicitud,inventario', 'nullable', 'integer', 'min:1'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'descripcion' => ['nullable', 'string'],
        ];
    }
}