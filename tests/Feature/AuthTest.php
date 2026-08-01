<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::create([
            'name'     => 'Pak Hendra',
            'email'    => 'hendra@jayamotor.id',
            'password' => bcrypt('password'),
            'role'     => 'owner',
        ]);

        $response = $this->post('/login', [
            'email'    => 'hendra@jayamotor.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        User::create([
            'name'     => 'Pak Hendra',
            'email'    => 'hendra@jayamotor.id',
            'password' => bcrypt('password'),
            'role'     => 'owner',
        ]);

        $this->post('/login', [
            'email'    => 'hendra@jayamotor.id',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::create([
            'name'     => 'Pak Hendra',
            'email'    => 'hendra@jayamotor.id',
            'password' => bcrypt('password'),
            'role'     => 'owner',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_cashier_cannot_access_owner_reports(): void
    {
        $cashier = User::create([
            'name'     => 'Mbak Rina',
            'email'    => 'rina@jayamotor.id',
            'password' => bcrypt('password'),
            'role'     => 'cashier',
        ]);

        $response = $this->actingAs($cashier)->get('/reports/profit-loss');

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_mechanic_cannot_access_bulk_payments_or_reports(): void
    {
        $mechanic = User::create([
            'name'     => 'Pak Sarno',
            'email'    => 'sarno@jayamotor.id',
            'password' => bcrypt('password'),
            'role'     => 'mechanic',
        ]);

        $respBulk = $this->actingAs($mechanic)->get('/payments/bulk');
        $respBulk->assertRedirect(route('dashboard'));

        $respReport = $this->actingAs($mechanic)->get('/reports/commissions');
        $respReport->assertRedirect(route('dashboard'));
    }

    public function test_owner_can_access_all_routes(): void
    {
        $owner = User::create([
            'name'     => 'Pak Hendra',
            'email'    => 'hendra@jayamotor.id',
            'password' => bcrypt('password'),
            'role'     => 'owner',
        ]);

        $this->actingAs($owner)->get('/')->assertStatus(200);
        $this->actingAs($owner)->get('/work-orders')->assertStatus(200);
        $this->actingAs($owner)->get('/payments/bulk')->assertStatus(200);
        $this->actingAs($owner)->get('/warranty')->assertStatus(200);
        $this->actingAs($owner)->get('/reports/profit-loss')->assertStatus(200);
        $this->actingAs($owner)->get('/reports/commissions')->assertStatus(200);
        $this->actingAs($owner)->get('/reports/scrap')->assertStatus(200);
    }
}
