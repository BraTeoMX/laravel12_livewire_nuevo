<?php

namespace App\Livewire;

use App\Models\CatalogoRole;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class CatalogoRolesTable extends PowerGridComponent
{
    protected string $view = 'livewire.catalogo-roles.catalogo-roles-table';

    public string $tableName = 'catalogo_roles_table';

    // Form state
    public ?bool $isModalOpen = false;
    public ?int $editingRoleId = null;
    public string $nombre = '';
    public ?string $descripcion = null;

    public function datasource(): Collection
    {
        return CatalogoRole::all();
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
            ->add('nombre')
            ->add('descripcion')
            ->add('created_at_formatted', fn ($entry) => $entry->created_at ? $entry->created_at->format('d/m/Y H:i') : '-')
            ->add('updated_at_formatted', fn ($entry) => $entry->updated_at ? $entry->updated_at->format('d/m/Y H:i') : '-')
            ->add('acciones', fn ($entry): string => view('livewire.catalogo-roles.actions', ['entry' => $entry])->render());
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),

            Column::make('Nombre', 'nombre')
                ->searchable('nombre')
                ->sortable(),

            Column::make('Descripción', 'descripcion')
                ->searchable('descripcion'),

            Column::make('Creado', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Actualizado', 'updated_at_formatted', 'updated_at')
                ->sortable(),

            Column::make('Acciones', 'acciones')
                ->sortable(false),
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
            <p class="mt-2 text-sm text-zinc-500">No se encontraron registros de roles.</p>
        </div>
        HTML;
    }

    /* -------------------- Modal Form Logic -------------------- */

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function openEditModal(int $id): void
    {
        $role = CatalogoRole::findOrFail($id);
        $this->editingRoleId = $id;
        $this->nombre = $role->nombre;
        $this->descripcion = $role->descripcion;
        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingRoleId = null;
        $this->nombre = '';
        $this->descripcion = '';
        $this->resetErrorBag();
    }

    protected function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                \Illuminate\Validation\Rule::unique('catalogo_roles', 'nombre')->ignore($this->editingRoleId),
            ],
            'descripcion' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder los 100 caracteres.',
            'nombre.unique' => 'Este nombre de rol ya existe.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        CatalogoRole::updateOrCreate(
            ['id' => $this->editingRoleId],
            [
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
            ]
        );

        session()->flash('message', 'Rol guardado correctamente.');
        $this->closeModal();

        $this->dispatch('pg:event-table-refresh-catalogo-roles-table');
    }

    public function delete(int $id): void
    {
        $role = CatalogoRole::findOrFail($id);

        if ($role->users()->count() > 0) {
            session()->flash('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
            return;
        }

        $role->delete();
        session()->flash('message', 'Rol eliminado correctamente.');

        $this->dispatch('pg:event-table-refresh-catalogo-roles-table');
    }
}
