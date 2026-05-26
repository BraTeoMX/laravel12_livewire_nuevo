<?php

namespace App\Livewire;

use App\Models\Inspeccion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class RegistrosDelDiaTable extends PowerGridComponent
{
    public string $tableName = 'registros_del_dia_table';

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput(),
            
            PowerGrid::footer()
                ->showPerPage(0, [0]),
        ];
    }

    public function datasource(): Builder
    {
        return Inspeccion::query()
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'asc');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('numero_recepcion')
            ->add('lote_intimark')
            ->add('maquina')
            ->add('articulo')
            ->add('total_puntos_defectos')
            ->add('hora_formateada', fn ($inspeccion) => Carbon::parse($inspeccion->created_at)->format('h:i A'));
    }

    public function columns(): array
    {
        return [
            Column::make('Recepción', 'numero_recepcion')
                ->searchable()
                ->sortable(),

            Column::make('Lote Intimark', 'lote_intimark')
                ->searchable()
                ->sortable(),

            Column::make('Máquina', 'maquina')
                ->searchable()
                ->sortable(),

            Column::make('Artículo', 'articulo')
                ->searchable()
                ->sortable(),

            Column::make('Puntos Totales', 'total_puntos_defectos')
                ->sortable(),

            Column::make('Hora', 'hora_formateada', 'created_at')
                ->sortable(),

            Column::action('Acción')
        ];
    }

    public function actions(Inspeccion $row): array
    {
        return [
            Button::add('accion')
                ->slot('Acción')
                ->class('inline-flex items-center justify-center rounded-md border border-zinc-200 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-800 shadow-sm hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700')
                ->dispatch('editarRegistro', ['id' => $row->id]),
        ];
    }
}
