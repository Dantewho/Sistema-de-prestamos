<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Solicitud;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
        $data['usuario_prestador_id'] = $request->user()->getKey();

        $solicitud = DB::transaction(function () use ($data) {
            if ($data['tipo_solicitud'] === 'inventario') {
                $inventario = Inventario::whereKey($data['inventario_id'])->lockForUpdate()->firstOrFail();

                if ($inventario->cantidad < $data['cantidad']) {
                    abort(422, 'La cantidad solicitada supera la existencia disponible.');
                }

                $inventario->decrement('cantidad', $data['cantidad']);
            }

            $data['estado'] = Carbon::parse($data['fecha_inicio'])->isToday() ? 'activa' : 'pendiente';

            return Solicitud::create($data);
        });

        return response()->json($solicitud->load(['solicitante', 'prestador', 'aula.edificio', 'inventario']), 201);
    }

    public function show(Solicitud $solicitud): JsonResponse
    {
        return response()->json($solicitud->load(['solicitante', 'prestador', 'aula.edificio', 'inventario']));
    }

    public function update(Request $request, Solicitud $solicitud): JsonResponse
    {
        abort_if(in_array($solicitud->estado, ['finalizada', 'cancelada'], true), 422, 'Este prestamo ya no se puede editar.');

        $data = $request->validate([
            'identificacion' => ['sometimes', 'nullable', 'string', 'max:100'],
            'tipo_solicitud' => ['required', Rule::in(['aula', 'inventario'])],
            'aula_id' => ['required_if:tipo_solicitud,aula', 'nullable', 'integer', 'exists:aulas,id'],
            'inventario_id' => ['required_if:tipo_solicitud,inventario', 'nullable', 'integer', 'exists:inventario,id'],
            'cantidad' => ['required_if:tipo_solicitud,inventario', 'nullable', 'integer', 'min:1'],
            'fecha_inicio' => ['sometimes', 'required', 'date'],
            'fecha_fin' => ['sometimes', 'nullable', 'date', 'after_or_equal:fecha_inicio'],
            'descripcion' => ['sometimes', 'nullable', 'string'],
        ]);

        $data['aula_id'] = $data['tipo_solicitud'] === 'aula' ? $data['aula_id'] : null;
        $data['inventario_id'] = $data['tipo_solicitud'] === 'inventario' ? $data['inventario_id'] : null;
        $data['cantidad'] = $data['tipo_solicitud'] === 'inventario' ? (int) $data['cantidad'] : null;

        $updatedSolicitud = DB::transaction(function () use ($data, $solicitud) {
            $lockedSolicitud = Solicitud::whereKey($solicitud->getKey())->lockForUpdate()->firstOrFail();

            abort_if(in_array($lockedSolicitud->estado, ['finalizada', 'cancelada'], true), 422, 'Este prestamo ya no se puede editar.');

            $oldInventoryId = $lockedSolicitud->tipo_solicitud === 'inventario' ? (int) $lockedSolicitud->inventario_id : null;
            $newInventoryId = $data['tipo_solicitud'] === 'inventario' ? (int) $data['inventario_id'] : null;
            $oldQuantity = $oldInventoryId ? (int) $lockedSolicitud->cantidad : 0;
            $newQuantity = $newInventoryId ? (int) $data['cantidad'] : 0;
            $inventoryIds = array_values(array_unique(array_filter([$oldInventoryId, $newInventoryId])));
            sort($inventoryIds);

            $inventories = Inventario::whereIn('id', $inventoryIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($inventoryIds as $inventoryId) {
                $inventory = $inventories->get($inventoryId);
                $availableQuantity = (int) $inventory->cantidad
                    + ($inventoryId === $oldInventoryId ? $oldQuantity : 0)
                    - ($inventoryId === $newInventoryId ? $newQuantity : 0);

                abort_if($availableQuantity < 0, 422, 'No hay suficiente inventario disponible para este cambio.');

                $inventory->update(['cantidad' => $availableQuantity]);
            }

            $lockedSolicitud->update($data);

            return $lockedSolicitud->fresh()->load(['solicitante', 'prestador', 'aula.edificio', 'inventario']);
        }, attempts: 3);

        return response()->json($updatedSolicitud);
    }

    public function finalizar(Solicitud $solicitud): JsonResponse
    {
        $finalizedSolicitud = DB::transaction(function () use ($solicitud) {
            $lockedSolicitud = Solicitud::whereKey($solicitud->getKey())->lockForUpdate()->firstOrFail();

            if ($lockedSolicitud->estado === 'finalizada') {
                return $lockedSolicitud->load(['solicitante', 'prestador', 'aula.edificio', 'inventario']);
            }

            abort_if($lockedSolicitud->estado === 'cancelada', 422, 'Un prestamo cancelado no se puede finalizar.');

            if ($lockedSolicitud->tipo_solicitud === 'inventario') {
                $inventory = Inventario::whereKey($lockedSolicitud->inventario_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $inventory->increment('cantidad', $lockedSolicitud->cantidad);
            }

            $lockedSolicitud->update(['estado' => 'finalizada']);

            return $lockedSolicitud->fresh()->load(['solicitante', 'prestador', 'aula.edificio', 'inventario']);
        }, attempts: 3);

        return response()->json($finalizedSolicitud);
    }

    public function destroy(Solicitud $solicitud): JsonResponse
    {
        $solicitud->delete();

        return response()->json(status: 204);
    }

    private function rules(): array
    {
        return [
            'identificacion' => ['nullable', 'string', 'max:100'],
            'usuario_solicitante_id' => ['required', 'integer', 'exists:perfiles,id'],
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
