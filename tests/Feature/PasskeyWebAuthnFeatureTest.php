<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasskeyWebAuthnFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $tenant = Tenant::firstOrCreate(['email' => 'tenant@test.com'], ['name' => 'Test Tenant']);
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $superadminRole = Role::firstOrCreate(['slug' => 'superadmin'], ['name' => 'Superadmin']);
    }

    public function test_authenticated_user_can_request_webauthn_register_options()
    {
        $user = User::factory()->create([
            'email' => 'admin@smartroom.test',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->postJson('/webauthn/register/options');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'rp' => ['name'],
            'user' => ['id', 'name', 'displayName'],
            'challenge',
            'pubKeyCredParams',
        ]);
    }

    public function test_unauthenticated_user_is_forbidden_or_redirected()
    {
        $response = $this->postJson('/webauthn/register/options');
        $response->assertStatus(403);
    }
}
