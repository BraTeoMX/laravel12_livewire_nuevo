<?php

namespace App\Livewire;

use App\Support\LaboratorioTelasData;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class LotesTelaTable extends PowerGridComponent
{
    public string $tableName = 'lotes_tela_table';

    public function datasource(): Collection
    {
        return LaboratorioTelasData::lotes();
    }

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput()
                ->showToggleColumns(),

            PowerGrid::footer()
                ->showPerPage(5, [5, 10, 25, 0]),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('lote')
            ->add('tipo_tela')
            ->add('composicion')
            ->add('resistencia_tension')
            ->add('elongacion')
            ->add('defectos')
            ->add('estado')
            ->add('estado_badge', fn ($entry): string => $this->estadoBadge($entry->estado))
            ->add('tecnico')
            ->add('fecha_formateada', fn ($entry): string => Carbon::parse($entry->fecha)->format('d/m/Y'));
    }

    public function columns(): array
    {
        return [
            Column::make('Lote', 'lote')
                ->searchable()
                ->sortable(),

            Column::make('Tipo de tela', 'tipo_tela')
                ->searchable()
                ->sortable(),

            Column::make('Composicion', 'composicion')
                ->searchable(),

            Column::make('Tension (N)', 'resistencia_tension')
                ->sortable(),

            Column::make('Elongacion (%)', 'elongacion')
                ->sortable(),

            Column::make('Defectos', 'defectos')
                ->sortable(),

            Column::make('Estado', 'estado_badge', 'estado')
                ->searchable('estado')
                ->sortable(),

            Column::make('Tecnico', 'tecnico')
                ->searchable()
                ->sortable(),

            Column::make('Fecha', 'fecha_formateada', 'fecha')
                ->sortable(),
        ];
    }

    /**
     * Render personalizado cuando no hay resultados.
     */
    public function noResults(): string
    {
        return <<<HTML
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <svg class="h-12 w-12 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 0115.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="mt-2 text-sm text-zinc-500">No se encontraron registros que coincidan con la búsqueda.</p>
        </div>
        HTML;
    }

    private function estadoBadge(string $estado): string
    {
        $classes = match ($estado) {
            'Aprobado' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
            'Observacion' => 'bg-amber-100 text-amber-700 ring-amber-600/20',
            'Rechazado' => 'bg-rose-100 text-rose-700 ring-rose-600/20',
            default => 'bg-zinc-100 text-zinc-700 ring-zinc-600/20',
        };

        return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$classes.'">'.$estado.'</span>';
    }
}
