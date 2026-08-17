<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $company = Company::factory()->create();
        $user = User::factory()->admin()->create(['company_id' => $company->id]);

        Sanctum::actingAs($user);

        return $user;
    }

    public function test_public_endpoint_lists_companies_without_auth(): void
    {
        Company::factory()->count(3)->create();

        $this->getJson('/api/companies')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_anyone_can_create_and_update_company(): void
    {
        $response = $this->postJson('/api/companies', [
            'name' => 'Dentix Colombia',
            'email' => 'info@dentix.com.co',
            'phone' => '+57 601 744 1234',
            'city' => 'Medellín',
        ]);

        $response->assertCreated()->assertJsonPath('data.name', 'Dentix Colombia');

        $company = Company::first();

        $this->postJson('/api/companies', [
            'id' => $company->id,
            'name' => 'Dentix Colombia S.A.S.',
            'email' => 'info@dentix.com.co',
        ])->assertOk()->assertJsonPath('data.name', 'Dentix Colombia S.A.S.');

        $this->assertDatabaseHas('companies', ['name' => 'Dentix Colombia S.A.S.']);
    }

    public function test_anyone_can_delete_company(): void
    {
        $company = Company::factory()->create();

        $this->deleteJson("/api/companies/{$company->id}")
            ->assertOk();

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    public function test_admin_can_list_companies(): void
    {
        $this->actingAsAdmin();
        Company::factory()->count(4)->create();

        $this->getJson('/api/admin/companies')
            ->assertOk()
            ->assertJsonCount(5, 'data');
    }

    public function test_admin_can_delete_company(): void
    {
        $this->actingAsAdmin();
        $company = Company::factory()->create();

        $this->deleteJson("/api/admin/companies/{$company->id}")
            ->assertOk();

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    public function test_company_validation_requires_name(): void
    {
        $this->postJson('/api/companies', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }
}
