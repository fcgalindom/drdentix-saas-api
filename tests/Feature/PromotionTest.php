<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PromotionTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $company = Company::factory()->create();
        $user = User::factory()->admin()->create(['company_id' => $company->id]);

        Sanctum::actingAs($user);

        return $user;
    }

    public function test_admin_can_activate_promotion(): void
    {
        $user = $this->actingAsAdmin();
        $promotion = Promotion::factory()->create(['company_id' => $user->company_id, 'status' => 0]);

        $this->postJson('/api/admin/promotions/state', [
            'id' => $promotion->id,
            'state' => 'Activo',
        ])->assertOk()->assertJsonPath('message', 'Estado de promoción actualizado.');

        $this->assertDatabaseHas('promotions', ['id' => $promotion->id, 'status' => 1]);
    }

    public function test_admin_can_deactivate_promotion(): void
    {
        $user = $this->actingAsAdmin();
        $promotion = Promotion::factory()->create(['company_id' => $user->company_id, 'status' => 1]);

        $this->postJson('/api/admin/promotions/state', [
            'id' => $promotion->id,
            'state' => 'Inactivo',
        ])->assertOk();

        $this->assertDatabaseHas('promotions', ['id' => $promotion->id, 'status' => 0]);
    }

    public function test_state_validation_requires_valid_state(): void
    {
        $user = $this->actingAsAdmin();
        $promotion = Promotion::factory()->create(['company_id' => $user->company_id]);

        $this->postJson('/api/admin/promotions/state', [
            'id' => $promotion->id,
            'state' => 'Desconocido',
        ])->assertUnprocessable()->assertJsonValidationErrors(['state']);
    }

    public function test_promotion_from_another_company_is_not_found(): void
    {
        $this->actingAsAdmin();
        $otherCompany = Company::factory()->create();
        $promotion = Promotion::factory()->create(['company_id' => $otherCompany->id]);

        $this->postJson('/api/admin/promotions/state', [
            'id' => $promotion->id,
            'state' => 'Inactivo',
        ])->assertNotFound();
    }
}
