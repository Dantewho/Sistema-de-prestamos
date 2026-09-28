<?php

namespace Tests\Feature;

use App\Models\Perfil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdministratorAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_a_protected_panel(): void
    {
        $this->get('/prestamos')->assertRedirect(route('login'));
        $this->getJson('/api/inventario')->assertUnauthorized();
    }

    public function test_non_administrator_cannot_access_panels_or_the_api(): void
    {
        $this->actingAs($this->createPerfil(2));

        $this->get('/prestamos')
            ->assertForbidden()
            ->assertSee('Solo los administradores pueden acceder al sistema.')
            ->assertSee('Cerrar sesión y regresar al login');
        $this->getJson('/api/inventario')->assertForbidden();
    }

    public function test_non_administrator_can_end_session_from_the_forbidden_page(): void
    {
        $this->actingAs($this->createPerfil(2));
        $this->get('/prestamos')->assertForbidden();

        $this->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_administrator_can_access_panels_and_the_api(): void
    {
        $this->actingAs($this->createPerfil(1));

        $this->get('/prestamos')->assertOk();
        $this->get('/inventario')->assertOk();
        $this->get('/usuarios')->assertOk();
        $this->getJson('/api/inventario')->assertOk();
    }

    public function test_public_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register')->assertNotFound();
    }

    private function createPerfil(int $type): Perfil
    {
        return Perfil::create([
            'name' => $type === 1 ? 'Administrador' : 'Usuario',
            'usuario' => $type === 1 ? 'admin-prueba' : 'usuario-prueba',
            'email' => $type === 1 ? 'admin@example.test' : 'usuario@example.test',
            'password' => 'password',
            'tipo_usuario' => $type,
        ]);
    }
}
