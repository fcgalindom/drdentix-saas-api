<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $company = Company::factory()->create();
        $user = User::factory()->admin()->create(['company_id' => $company->id]);

        Sanctum::actingAs($user);

        return $user;
    }

    public function test_public_plans_endpoint_returns_all_plans(): void
    {
        Plan::factory()->count(3)->create();

        $this->getJson('/api/plans')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_plans_are_scoped_to_company(): void
    {
        $plan = Plan::factory()->create();
        $company = Company::factory()->create();

        $company->plans()->attach($plan);

        $this->assertDatabaseHas('company_plan', [
            'company_id' => $company->id,
            'plan_id' => $plan->id,
        ]);
    }

    public function test_admin_can_list_plans(): void
    {
        $this->actingAsAdmin();
        Plan::factory()->count(5)->create();

        $this->getJson('/api/admin/plans')
            ->assertOk()
            ->assertJsonCount(5, 'data');
    }

    public function test_admin_can_store_and_update_plan(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/plans', [
            'name' => 'Pro',
            'price' => 49.90,
            'description' => 'Plan para clínicas medianas.',
        ]);

        $response->assertCreated()->assertJsonPath('data.name', 'Pro');

        $plan = Plan::first();

        $this->postJson('/api/admin/plans', [
            'id' => $plan->id,
            'name' => 'Pro Plus',
            'price' => 59.90,
            'description' => 'Plan actualizado.',
        ])->assertOk()->assertJsonPath('data.name', 'Pro Plus');

        $this->assertDatabaseHas('plans', ['name' => 'Pro Plus']);
    }

    public function test_admin_can_delete_plan(): void
    {
        $this->actingAsAdmin();
        $plan = Plan::factory()->create();

        $this->deleteJson("/api/admin/plans/{$plan->id}")
            ->assertOk();

        $this->assertDatabaseMissing('plans', ['id' => $plan->id]);
    }

    public function test_unauthenticated_user_cannot_access_admin_plans(): void
    {
        $this->getJson('/api/admin/plans')->assertUnauthorized();
    }

    public function test_plan_validation_requires_fields(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/admin/plans', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'price', 'description']);
    }
}
