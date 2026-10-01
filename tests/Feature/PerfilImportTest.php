<?php

namespace Tests\Feature;

use App\Models\Perfil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PerfilImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_imports_profiles_and_preserves_an_existing_password_when_blank(): void
    {
        $admin = $this->createPerfil('admin', 'admin@example.test', 1);
        $existingProfile = $this->createPerfil('existente', 'existente@example.test', 2);
        $this->actingAs($admin);

        $csv = implode("\n", [
            'name,usuario,email,tipo_usuario,imagen_perfil,password',
            'Nuevo usuario,nuevo,nuevo@example.test,2,,clave-nueva',
            'Usuario actualizado,existente,existente@example.test,3,avatar.jpg,',
        ]);

        $response = $this->post(route('perfiles.importar'), [
            'archivo' => UploadedFile::fake()->createWithContent('perfiles.csv', $csv),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Los perfiles se importaron correctamente.');
        $this->assertDatabaseHas('perfiles', [
            'usuario' => 'nuevo',
            'name' => 'Nuevo usuario',
            'tipo_usuario' => 2,
        ]);
        $this->assertTrue(Hash::check('clave-nueva', Perfil::where('usuario', 'nuevo')->value('password')));
        $this->assertDatabaseHas('perfiles', [
            'id' => $existingProfile->id,
            'name' => 'Usuario actualizado',
            'tipo_usuario' => 3,
            'imagen_perfil' => 'avatar.jpg',
        ]);
        $this->assertTrue(Hash::check('password-inicial', $existingProfile->fresh()->password));
    }

    public function test_non_admin_cannot_import_profiles(): void
    {
        $this->actingAs($this->createPerfil('usuario', 'usuario@example.test', 2));

        $this->post(route('perfiles.importar'))->assertForbidden();
    }

    private function createPerfil(string $username, string $email, int $type): Perfil
    {
        return Perfil::create([
            'name' => $username,
            'usuario' => $username,
            'email' => $email,
            'password' => 'password-inicial',
            'tipo_usuario' => $type,
        ]);
    }
}
