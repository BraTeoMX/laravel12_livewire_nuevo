<div>
    {{-- Encabezado con botón de crear --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
            Catálogo de Roles
        </h2>
        <flux:button wire:click="openCreateModal" variant="primary">
            Nuevo Rol
        </flux:button>
    </div>

    {{-- Mensajes flash --}}
    @if (session('message'))
        <div class="rounded-md bg-green-50 p-4 border border-green-200 dark:bg-green-900/20 dark:border-green-900/50 mb-4">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-400">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-md bg-red-50 p-4 border border-red-200 dark:bg-red-900/20 dark:border-red-900/50 mb-4">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800 dark:text-red-400">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- PowerGrid Table --}}
    {{ $this->table }}

    {{-- Modal para crear/editar rol --}}
    <flux:modal wire:model="isModalOpen" class="md:w-[480px]">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                {{ $editingRoleId ? 'Editar Rol' : 'Nuevo Rol' }}
            </h2>

            <form wire:submit="save" class="flex flex-col gap-6">
                {{-- Nombre del Rol --}}
                <div class="grid gap-2">
                    <flux:input
                        wire:model="nombre"
                        label="Nombre del Rol"
                        type="text"
                        required
                        autofocus
                        placeholder="Ej: Administrador, Gerente, etc."
                    />
                    @error('nombre')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descripción --}}
                <div class="grid gap-2">
                    <flux:textarea
                        wire:model="descripcion"
                        label="Descripción"
                        placeholder="Descripción detallada del rol (opcional)"
                    />
                    @error('descripcion')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 mt-4">
                    <flux:button wire:click="closeModal" variant="subtle">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editingRoleId ? 'Actualizar' : 'Crear' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
