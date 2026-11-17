<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_remains_visible_without_credentials(): void
    {
        config()->set('demo.enabled', true);

        $this->get('/login')->assertOk()->assertSee('Masuk ke Demo');
        $this->post('/login', [])->assertRedirect('/dashboard/utama');
        $this->assertAuthenticated();
        $this->assertTrue((bool) auth()->user()->is_admin);
        $this->get('/login')->assertOk();
    }

    public function test_direct_dashboard_url_enters_demo_without_visiting_login_page(): void
    {
        config()->set('demo.enabled', true);
        $this->get('/dashboard/utama')->assertOk();
        $this->assertAuthenticated();
        $this->assertSame(config('demo.email'), auth()->user()->email);
    }

    public function test_login_requires_real_credentials_when_demo_is_disabled(): void
    {
        config()->set('demo.enabled', false);

        $this->post('/login', [])->assertSessionHasErrors(['email', 'password']);
        $this->get('/dashboard/utama')->assertRedirect('/login');
    }

    public function test_demo_identity_cannot_be_deleted_through_admin_dashboard(): void
    {
        config()->set('demo.enabled', true);
        $this->get('/dashboard/utama')->assertOk();
        $id = User::query()->where('email', config('demo.email'))->value('id');
        $this->delete('/dashboard/user/'.$id)->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $id]);
    }
}
