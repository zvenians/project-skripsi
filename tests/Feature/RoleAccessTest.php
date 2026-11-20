<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_demo_role_creates_a_distinct_scoped_identity(): void
    {
        config()->set('demo.enabled', true);

        foreach (['superadmin', 'organization', 'manager'] as $role) {
            $response = $this->post(route('demo.role', $role));
            $response->assertRedirect('/dashboard/utama');
            $this->assertAuthenticated();
            $this->assertSame($role, auth()->user()->role);
            $this->assertSame(config("demo.profiles.$role.email"), auth()->user()->email);
            if ($role === 'organization') {
                $this->assertNotNull(auth()->user()->organization_id);
            }
            if ($role === 'manager') {
                $this->assertNotNull(auth()->user()->facility_manager_id);
            }
        }

        $this->assertDatabaseCount('users', 3);
    }

    public function test_unknown_demo_role_is_not_available(): void
    {
        config()->set('demo.enabled', true);

        $this->post('/demo/role/unknown')->assertNotFound();
    }

    public function test_organization_cannot_use_facility_decision_routes(): void
    {
        config()->set('demo.enabled', false);
        $user = User::factory()->create(['role' => 'organization', 'is_admin' => false]);

        $this->actingAs($user)->post('/dashboard/terima/999')->assertForbidden();
    }
}
