<?php

namespace App\Imports;

use App\Models\Solicitud;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;


class SolicitudImport implements ToModel, SkipsEmptyRows, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Model
    {
        return new Solicitud([
            'usuario_prestador_id'   => $row['usuario_prestador_id'],
            'identificacion'         => $row['identificacion'] ?? null,
            'usuario_solicitante_id' => $row['usuario_solicitante_id'],
            'tipo_solicitud'         => $row['tipo_solicitud'],
            'aula_id'                => $row['aula_id'] ?? null,
            'inventario_id'          => $row['inventario_id'] ?? null,
            'cantidad'               => $row['cantidad'] ?? null,
            'fecha_inicio'           => $row['fecha_inicio'],
            'fecha_fin'              => $row['fecha_fin'] ?? null,
            'estado'                => $row['estado'] ?? null,
            'descripcion'            => $row['descripcion'] ?? null,
        ]);
    }
        public function rules(): array{
            return [
                'usuario_prestador_id' => ['required', 'integer', 'exists:perfiles,id'],
                'identificacion' => ['nullable', 'string', 'max:100'],
                'usuario_solicitante_id' => ['required', 'integer', 'exists:perfiles,id'],
                'tipo_solicitud' => ['required', Rule::in(['aula', 'inventario'])],
                'aula_id' => ['required_if:tipo_solicitud,aula', 'nullable', 'integer', 'exists:aulas,id'],
                'inventario_id' => ['required_if:tipo_solicitud,inventario', 'nullable', 'integer', 'exists:inventario,id'],
                'cantidad' => ['required_if:tipo_solicitud,inventario', 'nullable', 'integer', 'min:1'],
                'fecha_inicio' => ['required', 'date'],
                'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
                'estado' => ['nullable', Rule::in(['pendiente', 'activa', 'cancelada', 'finalizada'])],
                'descripcion' => ['nullable', 'string'],
            ];
        }
}
