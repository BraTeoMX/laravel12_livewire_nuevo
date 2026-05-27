<?php

namespace Tests\Feature;

use App\Http\Livewire\InspeccionTela;
use App\Models\CatalogoMaquina;
use App\Models\Inspeccion;
use App\Models\InspeccionTelaTemporal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class InspeccionTelaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Dynamically create missing catalog tables for testing
        if (!Schema::hasTable('catalogo_roles')) {
            Schema::create('catalogo_roles', function ($table) {
                $table->increments('id');
                $table->string('nombre');
                $table->string('descripcion')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('catalogo_maquinas')) {
            Schema::create('catalogo_maquinas', function ($table) {
                $table->increments('id');
                $table->string('nombre');
                $table->string('descripcion')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('catalogo_defectos')) {
            Schema::create('catalogo_defectos', function ($table) {
                $table->increments('id');
                $table->unsignedInteger('area_id')->nullable();
                $table->string('nombre');
                $table->string('descripcion')->nullable();
                $table->boolean('estatus')->default(true);
                $table->timestamps();
            });
        }
    }

    public function test_component_mounts_clean_without_today_inspections(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(InspeccionTela::class)
            ->assertSet('terminoBusqueda', '')
            ->assertSet('maquina', '')
            ->assertSet('lote_intimark', '');
    }

    public function test_component_prepopulates_from_latest_today_inspection(): void
    {
        $user = User::factory()->create();

        // Create catalog machines
        CatalogoMaquina::create(['nombre' => 'Maquina A']);
        CatalogoMaquina::create(['nombre' => 'Maquina B']);

        // Create temporal inspection records for search lookup
        InspeccionTelaTemporal::create([
            'source_key' => 'test-key-1',
            'numero_diario' => 'REC12345',
            'orden_compra' => 'OC-123',
            'proveedor' => 'Proveedor Test',
            'estilo' => 'Estilo A',
            'nombre_producto' => 'Tela Algodon',
            'nombre_producto_externo' => 'Tela Algodon 60"',
            'estilo_externo' => 'Algodon',
            'talla' => 'N/A',
            'color' => 'Rojo',
            'articulo' => 'Estilo A.Rojo',
            'ancho_contratado' => 60,
            'lote_intimark' => 'LOTE-ABC',
            'termino_busqueda' => 'REC12345',
        ]);

        // Create previous inspection record for today
        Inspeccion::create([
            'user_id' => $user->id,
            'maquina' => 'Maquina B',
            'lote_intimark' => 'LOTE-ABC',
            'articulo' => 'Estilo A.Rojo',
            'proveedor' => 'Proveedor Test',
            'color_nombre' => 'Tela Algodon',
            'ancho_contratado_input' => '58.00',
            'ancho_contratado_cm' => 147,
            'material' => 'Algodon',
            'orden_compra' => 'OC-123',
            'numero_recepcion' => 'REC12345',
            'ancho_cortable' => '57.50',
            'numero_piezas' => 1,
            'numero_lote' => 'DYE-001',
            'yarda_ticket' => '100.00',
            'yarda_actual' => '99.50',
            'observaciones' => 'Prueba',
        ]);

        Livewire::actingAs($user)
            ->test(InspeccionTela::class)
            ->assertSet('terminoBusqueda', 'REC12345')
            ->assertSet('maquina', 'Maquina B')
            ->assertSet('lote_intimark', 'LOTE-ABC')
            ->assertSet('ancho_contratado_input', '58')
            ->assertSet('ancho_contratado_cm', 147)
            ->assertSet('proveedor', 'Proveedor Test')
            ->assertSet('articulo', 'Estilo A.Rojo')
            ->assertSet('color_nombre', 'Tela Algodon');
    }

    public function test_component_does_not_prepopulate_from_yesterday_inspection(): void
    {
        $user = User::factory()->create();

        // Create yesterday's inspection record
        $yesterday = now()->subDay();
        $inspeccion = Inspeccion::create([
            'user_id' => $user->id,
            'maquina' => 'Maquina B',
            'lote_intimark' => 'LOTE-ABC',
            'articulo' => 'Estilo A.Rojo',
            'proveedor' => 'Proveedor Test',
            'color_nombre' => 'Tela Algodon',
            'ancho_contratado_input' => '58.00',
            'ancho_contratado_cm' => 147,
            'material' => 'Algodon',
            'orden_compra' => 'OC-123',
            'numero_recepcion' => 'REC12345',
            'ancho_cortable' => '57.50',
            'numero_piezas' => 1,
            'numero_lote' => 'DYE-001',
            'yarda_ticket' => '100.00',
            'yarda_actual' => '99.50',
            'observaciones' => 'Prueba',
        ]);

        // Force database created_at to yesterday
        $inspeccion->created_at = $yesterday;
        $inspeccion->save();

        Livewire::actingAs($user)
            ->test(InspeccionTela::class)
            ->assertSet('terminoBusqueda', '')
            ->assertSet('maquina', '')
            ->assertSet('lote_intimark', '');
    }

    public function test_component_does_not_prepopulate_from_other_users_inspection(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // Create today's inspection record for user B
        Inspeccion::create([
            'user_id' => $userB->id,
            'maquina' => 'Maquina B',
            'lote_intimark' => 'LOTE-ABC',
            'articulo' => 'Estilo A.Rojo',
            'proveedor' => 'Proveedor Test',
            'color_nombre' => 'Tela Algodon',
            'ancho_contratado_input' => '58.00',
            'ancho_contratado_cm' => 147,
            'material' => 'Algodon',
            'orden_compra' => 'OC-123',
            'numero_recepcion' => 'REC12345',
            'ancho_cortable' => '57.50',
            'numero_piezas' => 1,
            'numero_lote' => 'DYE-001',
            'yarda_ticket' => '100.00',
            'yarda_actual' => '99.50',
            'observaciones' => 'Prueba',
        ]);

        Livewire::actingAs($userA)
            ->test(InspeccionTela::class)
            ->assertSet('terminoBusqueda', '')
            ->assertSet('maquina', '')
            ->assertSet('lote_intimark', '');
    }
}
