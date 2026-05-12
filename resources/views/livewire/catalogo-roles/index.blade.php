<?php

use App\Livewire\CatalogoRolesTable;
use Illuminate\Contracts\View\View;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public function mount(): void
    {
        // Se puede agregar lógica de inicialización aquí
    }

    public function table(): string
    {
        return CatalogoRolesTable::class;
    }
}; ?>

<div class="flex flex-col gap-6 w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    @livewire('catalogo-roles-table')
</div>
