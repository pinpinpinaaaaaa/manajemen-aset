<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    // Buat record menu dashboard di DB — dibutuhkan agar middleware
    // tahu bahwa route 'dashboard' adalah route berbasis menu.
    private function buatMenuDashboard(): Menu
    {
        return Menu::create([
            'name'       => 'Dashboard',
            'route_name' => 'dashboard',
            'type'       => 'file',
            'order'      => 1,
        ]);
    }

    public function test_user_dengan_izin_bisa_akses_dashboard(): void
    {
        $menu = $this->buatMenuDashboard();

        // Role yang punya akses ke menu dashboard
        $role = Role::factory()->create(['menu' => [$menu->id]]);
        $user = User::factory()->create(['id_role' => $role->id_role]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertStatus(200);
    }

    public function test_user_tanpa_izin_mendapat_403_di_dashboard(): void
    {
        $this->buatMenuDashboard(); // menu ada di DB tapi tidak di whitelist role

        // Role dengan whitelist menu yang tidak termasuk dashboard
        $role = Role::factory()->create(['menu' => [99999]]);
        $user = User::factory()->create(['id_role' => $role->id_role]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertStatus(403);
    }

    public function test_user_tanpa_role_mendapat_403(): void
    {
        // Pastikan menu ada di DB — middleware harus tahu route ini menu-based
        $this->buatMenuDashboard();

        // User tanpa role → allowedMenuIds() mengembalikan [] (kosong, bukan null)
        $user = User::factory()->create(['id_role' => null]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertStatus(403);
    }

    public function test_user_dengan_role_full_access_bisa_akses_semua(): void
    {
        $this->buatMenuDashboard();

        // Role dengan menu=null → full access (tidak dibatasi)
        $role = Role::factory()->create(['menu' => null]);
        $user = User::factory()->create(['id_role' => $role->id_role]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertStatus(200);
    }
}
