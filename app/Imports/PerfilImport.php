<?php

namespace App\Imports;

use App\Models\Perfil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PerfilImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Model
    {
        $perfil = Perfil::firstOrNew(['usuario' => $row['usuario']]);

        if (! $perfil->exists && blank($row['password'] ?? null)) {
            throw ValidationException::withMessages([
                'archivo' => "El usuario {$row['usuario']} requiere una contraseña para crearse.",
            ]);
        }

        $emailInUse = Perfil::query()
            ->where('email', $row['email'])
            ->where('usuario', '!=', $row['usuario'])
            ->exists();

        if ($emailInUse) {
            throw ValidationException::withMessages([
                'archivo' => "El correo {$row['email']} ya pertenece a otro usuario.",
            ]);
        }

        $perfil->fill([
            'name' => $row['name'],
            'email' => $row['email'],
            'tipo_usuario' => $row['tipo_usuario'],
            'imagen_perfil' => $row['imagen_perfil'] ?? null,
        ]);

        if (filled($row['password'] ?? null)) {
            $perfil->password = Hash::make($row['password']);
        }

        return $perfil;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'usuario' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'tipo_usuario' => ['required', 'integer', 'between:1,3'],
            'imagen_perfil' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
