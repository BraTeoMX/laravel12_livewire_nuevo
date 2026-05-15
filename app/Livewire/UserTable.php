<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class UserTable extends PowerGridComponent
{
    public string $tableName = 'users_table';

    public function datasource(): Builder
    {
        return User::query()
            ->leftJoin('catalogo_roles', 'catalogo_roles.id', '=', 'users.role_id')
            ->select('users.*', 'catalogo_roles.nombre as role_nombre');
    }

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput()
                ->showToggleColumns(),

            PowerGrid::footer()
                ->showPerPage(10, [10, 25, 50, 0]),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('employee_number')
            ->add('name')
            ->add('email')
            ->add('role_nombre', fn ($entry): string => $entry->role_nombre ?: '-')
            ->add('estatus')
            ->add('estatus_badge', fn ($entry): string => $this->estatusBadge((bool) $entry->estatus))
            ->add('acciones', fn ($entry): string => view('livewire.users.actions', ['entry' => $entry])->render());
    }

    public function columns(): array
    {
        return [
            Column::make('Numero de Empleado', 'employee_number', 'users.employee_number')
                ->searchable()
                ->sortable(),

            Column::make('Nombre', 'name', 'users.name')
                ->searchable()
                ->sortable(),

            Column::make('Email', 'email', 'users.email')
                ->searchable()
                ->sortable(),

            Column::make('Rol', 'role_nombre', 'catalogo_roles.nombre')
                ->searchable()
                ->sortable(),

            Column::make('Estatus', 'estatus_badge', 'users.estatus')
                ->sortable(),

            Column::make('Acciones', 'acciones')
                ->sortable(false),
        ];
    }

    public function noResults(): string
    {
        return <<<HTML
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <svg class="h-12 w-12 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 0115.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="mt-2 text-sm text-zinc-500">No se encontraron usuarios que coincidan con la busqueda.</p>
        </div>
        HTML;
    }

    private function estatusBadge(bool $estatus): string
    {
        $label = $estatus ? 'Activo' : 'Inactivo';
        $classes = $estatus
            ? 'bg-emerald-100 text-emerald-700 ring-emerald-600/20'
            : 'bg-rose-100 text-rose-700 ring-rose-600/20';

        return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$classes.'">'.$label.'</span>';
    }
}
