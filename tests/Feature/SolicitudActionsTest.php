<?php

namespace Tests\Feature;

use App\Models\Inventario;
use App\Models\Perfil;
use App\Models\Solicitud;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolicitudActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_finishing_an_inventory_loan_returns_stock_only_once(): void
    {
        $user = $this->createPerfil();
        $inventory = Inventario::create(['nombre' => 'Proyector', 'cantidad' => 3]);
        $loan = $this->createInventoryLoan($user, $inventory, 2);
        $this->actingAs($user);

        $this->postJson("/api/solicitudes/{$loan->id}/finalizar")
            ->assertOk()
            ->assertJsonPath('estado', 'finalizada');
        $this->postJson("/api/solicitudes/{$loan->id}/finalizar")
            ->assertOk()
            ->assertJsonPath('estado', 'finalizada');

        $this->assertDatabaseHas('inventario', ['id' => $inventory->id, 'cantidad' => 5]);
        $this->assertDatabaseHas('solicitudes', ['id' => $loan->id, 'estado' => 'finalizada']);
    }

    public function test_editing_an_inventory_loan_adjusts_stock_and_loan_details(): void
    {
        $user = $this->createPerfil();
        $inventory = Inventario::create(['nombre' => 'Proyector', 'cantidad' => 4]);
        $loan = $this->createInventoryLoan($user, $inventory, 1);
        $this->actingAs($user);

        $this->patchJson("/api/solicitudes/{$loan->id}", [
            'tipo_solicitud' => 'inventario',
            'inventario_id' => $inventory->id,
            'cantidad' => 3,
            'aula_id' => null,
            'identificacion' => 'INE',
            'fecha_inicio' => '2026-09-28 10:00:00',
            'fecha_fin' => '2026-09-28 12:00:00',
            'descripcion' => 'Uso en sala de juntas',
        ])->assertOk()
            ->assertJsonPath('cantidad', 3)
            ->assertJsonPath('descripcion', 'Uso en sala de juntas');

        $this->assertDatabaseHas('inventario', ['id' => $inventory->id, 'cantidad' => 2]);
        $this->assertDatabaseHas('solicitudes', ['id' => $loan->id, 'cantidad' => 3]);
    }

    public function test_editing_an_inventory_loan_rejects_unavailable_quantity_without_changing_stock(): void
    {
        $user = $this->createPerfil();
        $inventory = Inventario::create(['nombre' => 'Proyector', 'cantidad' => 1]);
        $loan = $this->createInventoryLoan($user, $inventory, 1);
        $this->actingAs($user);

        $this->patchJson("/api/solicitudes/{$loan->id}", [
            'tipo_solicitud' => 'inventario',
            'inventario_id' => $inventory->id,
            'cantidad' => 3,
            'aula_id' => null,
            'fecha_inicio' => '2026-09-28 10:00:00',
            'fecha_fin' => '2026-09-28 12:00:00',
        ])->assertUnprocessable();

        $this->assertDatabaseHas('inventario', ['id' => $inventory->id, 'cantidad' => 1]);
        $this->assertDatabaseHas('solicitudes', ['id' => $loan->id, 'cantidad' => 1]);
    }

    private function createPerfil(): Perfil
    {
        return Perfil::create([
            'name' => 'Usuario de prueba',
            'usuario' => 'usuario-prueba',
            'email' => 'usuario-prueba@example.test',
            'password' => 'password',
            'tipo_usuario' => 1,
        ]);
    }

    private function createInventoryLoan(Perfil $user, Inventario $inventory, int $quantity): Solicitud
    {
        return Solicitud::create([
            'usuario_solicitante_id' => $user->id,
            'usuario_prestador_id' => $user->id,
            'tipo_solicitud' => 'inventario',
            'inventario_id' => $inventory->id,
            'cantidad' => $quantity,
            'fecha_inicio' => '2026-09-28 10:00:00',
            'fecha_fin' => '2026-09-28 12:00:00',
            'estado' => 'activa',
        ]);
    }
}
