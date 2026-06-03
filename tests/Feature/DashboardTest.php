<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CatalogoRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Registrar roles requeridos en la base de datos de prueba
        CatalogoRole::create(['id' => 1, 'nombre' => 'Administrador']);
        CatalogoRole::create(['id' => 2, 'nombre' => 'Gerente']);
        CatalogoRole::create(['id' => 3, 'nombre' => 'Gestion']);
        CatalogoRole::create(['id' => 4, 'nombre' => 'Consulta']);
        CatalogoRole::create(['id' => 5, 'nombre' => 'Auditor']);
    }

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_are_redirected_to_role_dashboard(): void
    {
        // Crear un usuario con rol Auditor (ID 5)
        $user = User::factory()->create(['role_id' => 5]);
        $this->actingAs($user);

        $response = $this->get('/dashboard');
        // Debe redirigir al dashboard de Auditor
        $response->assertRedirect(route('dashboard.auditor'));
        
        // Seguir la redirección y verificar que carga exitosamente
        $response = $this->followingRedirects()->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Portal de Auditorías y Calidad');
    }

    public function test_unauthorized_users_are_blocked_and_redirected(): void
    {
        // Crear un usuario con rol Consulta (ID 4)
        $user = User::factory()->create(['role_id' => 4]);
        $this->actingAs($user);

        // Intentar acceder al dashboard de Auditor (ID 5)
        $response = $this->get(route('dashboard.auditor'));
        $response->assertRedirect(route('dashboard'));

        // Intentar acceder a administración de usuarios (Adm. Usuarios)
        $response = $this->get(route('users.index'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_administrators_have_access_to_all_dashboards(): void
    {
        // Crear un usuario con rol Administrador (ID 1)
        $user = User::factory()->create(['role_id' => 1]);
        $this->actingAs($user);

        // Puede acceder al dashboard del Auditor
        $response = $this->get(route('dashboard.auditor'));
        $response->assertStatus(200);

        // Puede acceder al dashboard del Gerente
        $response = $this->get(route('dashboard.gerente'));
        $response->assertStatus(200);

        // Puede acceder a administración de usuarios
        $response = $this->get(route('users.index'));
        $response->assertStatus(200);
    }
}
