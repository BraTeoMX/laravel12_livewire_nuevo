<?php

use App\Models\CatalogoRole;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public bool $showModal = false;
    public bool $editMode = false;
    public ?int $editUserId = null;

    public string $name = '';
    public string $email = '';
    public int $employee_number = 0;
    public string $password = '';
    public string $password_confirmation = '';
    public string $role_id = '';

    public bool $autoGenerateEmail = false;
    public bool $allowEditEmail = false;

    public function openCreateModal(): void
    {
        $this->resetFormState();
        $this->editMode = false;
        $this->editUserId = null;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFormState();
    }

    private function resetFormState(): void
    {
        $this->reset([
            'name',
            'email',
            'employee_number',
            'password',
            'password_confirmation',
            'role_id',
            'editMode',
            'editUserId',
            'autoGenerateEmail',
            'allowEditEmail',
        ]);

        $this->resetErrorBag();
        $this->resetValidation();
    }

    #[On('edit-user')]
    public function edit(int $userId): void
    {
        $this->resetFormState();

        $user = User::findOrFail($userId);

        $this->editUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->employee_number = $user->employee_number;
        $this->role_id = (string) $user->role_id;
        $this->password = '';
        $this->password_confirmation = '';
        $this->allowEditEmail = false;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->name = strtoupper(trim($this->name));

        if (! $this->editMode && $this->autoGenerateEmail) {
            $this->email = strtolower((string) $this->employee_number).'@lab.com';
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'employee_number' => ['required', 'integer', 'unique:'.User::class.',employee_number,'.($this->editUserId ?? 'null')],
            'role_id' => ['required', 'integer', 'exists:catalogo_roles,id'],
        ];

        if (! $this->editMode && $this->autoGenerateEmail) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email'];
        } elseif ($this->editMode && ! $this->allowEditEmail) {
            $rules['email'] = ['sometimes'];
        } else {
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.($this->editUserId ?? 'null')];
        }

        $rules['password'] = $this->editMode
            ? ['nullable', 'string', 'confirmed', Rules\Password::defaults()]
            : ['required', 'string', 'confirmed', Rules\Password::defaults()];

        $validated = $this->validate($rules, [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electronico es obligatorio.',
            'email.email' => 'El correo electronico debe ser valido.',
            'email.unique' => 'Este correo electronico ya esta registrado.',
            'employee_number.required' => 'El numero de empleado es obligatorio.',
            'employee_number.integer' => 'El numero de empleado debe ser un valor numerico.',
            'employee_number.unique' => 'Este numero de empleado ya esta registrado.',
            'password.required' => 'La contrasena es obligatoria.',
            'password.confirmed' => 'La confirmacion de la contrasena no coincide.',
            'role_id.required' => 'Debe seleccionar un rol para el usuario.',
            'role_id.exists' => 'El rol seleccionado no es valido.',
        ]);

        $dataToStore = $validated;

        if (! $this->editMode && $this->autoGenerateEmail) {
            $dataToStore['email'] = $this->email;
        }

        if ($this->editMode && ! $this->allowEditEmail) {
            unset($dataToStore['email']);
        }

        if (! empty($validated['password'])) {
            $dataToStore['password'] = Hash::make($validated['password']);
        } else {
            unset($dataToStore['password']);
        }

        if ($this->editMode && $this->editUserId) {
            User::findOrFail($this->editUserId)->update($dataToStore);
            $this->dispatch('notify', type: 'success', message: 'Usuario actualizado correctamente.');
        } else {
            $user = User::create($dataToStore);
            event(new Registered($user));
            $this->dispatch('notify', type: 'success', message: 'Usuario creado correctamente.');
        }

        $this->closeModal();
        $this->dispatch('pg:eventRefresh-users_table');
    }

    public function with(): array
    {
        return [
            'roles' => CatalogoRole::orderBy('nombre')->get(),
        ];
    }

    public function toggleEstatus(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->estatus = ! $user->estatus;
        $user->save();

        $estado = $user->estatus ? 'activado' : 'desactivado';

        $this->dispatch('notify', type: 'success', message: "El usuario ha sido {$estado}.");
        $this->dispatch('pg:eventRefresh-users_table');
    }
}; ?>

<div class="mx-auto flex w-full max-w-5xl flex-col gap-6 p-4 sm:p-6 lg:p-8">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
                Administracion de Usuarios
            </h2>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Busqueda, ordenamiento, paginacion y columnas dinamicas con PowerGrid.
            </p>
        </div>

        <flux:button wire:click="openCreateModal" variant="primary">
            Crear Usuario
        </flux:button>
    </div>

    <section class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <livewire:user-table />
    </section>

    <flux:modal wire:model="showModal" @close="$wire.closeModal()" class="md:w-[480px]">
        <div class="p-6">
            <h2 class="mb-6 text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $editMode ? 'Editar Usuario' : 'Crear Nuevo Usuario' }}
            </h2>

            <form wire:submit="save" class="flex flex-col gap-6">
                <div class="grid gap-2">
                    <flux:input
                        wire:model="employee_number"
                        id="employee_number"
                        label="Numero de Empleado"
                        type="number"
                        required
                        autofocus
                        autocomplete="off"
                        placeholder="Ingrese numero de empleado"
                    />
                    @error('employee_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-2">
                    <flux:input
                        wire:model="name"
                        id="name"
                        label="Nombre"
                        type="text"
                        required
                        autocomplete="name"
                        placeholder="NOMBRE COMPLETO"
                        x-on:input="$event.target.value = $event.target.value.toUpperCase(); $wire.set('name', $event.target.value)"
                        style="text-transform: uppercase"
                    />
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if (! $editMode)
                    <div x-data="{ auto: $wire.entangle('autoGenerateEmail') }" class="grid gap-2">
                        <div class="mb-1 flex items-center gap-3">
                            <input
                                type="checkbox"
                                id="auto_generate_email"
                                wire:model.live="autoGenerateEmail"
                                class="cursor-pointer rounded border-zinc-300 text-blue-600 focus:ring-blue-500"
                            >
                            <label for="auto_generate_email" class="cursor-pointer select-none text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Autogenerar correo
                                <span class="ml-1 text-xs font-normal text-zinc-400">(se usara num. empleado@lab.com)</span>
                            </label>
                        </div>

                        <div x-bind:class="auto ? 'opacity-50' : ''">
                            <flux:input
                                wire:model="email"
                                id="email_create"
                                label="Correo electronico"
                                type="email"
                                autocomplete="email"
                                placeholder="correo@ejemplo.com"
                                x-bind:disabled="auto"
                            />
                        </div>

                        <p x-show="auto" class="-mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            Se generara automaticamente al guardar: <strong>{{ $employee_number ?: '{num. empleado}' }}@lab.com</strong>
                        </p>

                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <div x-data="{ canEdit: $wire.entangle('allowEditEmail') }" class="grid gap-2">
                        <div class="mb-1 flex items-center gap-3">
                            <input
                                type="checkbox"
                                id="allow_edit_email"
                                wire:model.live="allowEditEmail"
                                class="cursor-pointer rounded border-zinc-300 text-blue-600 focus:ring-blue-500"
                            >
                            <label for="allow_edit_email" class="cursor-pointer select-none text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Permitir edicion del correo
                            </label>
                        </div>

                        <div x-bind:class="! canEdit ? 'opacity-60' : ''">
                            <flux:input
                                wire:model="email"
                                id="email_edit"
                                label="Correo electronico"
                                type="email"
                                autocomplete="email"
                                placeholder="correo@ejemplo.com"
                                x-bind:disabled="! canEdit"
                            />
                        </div>

                        <p x-show="! canEdit" class="-mt-1 text-xs text-zinc-400 dark:text-zinc-500">
                            El correo esta protegido. Marca la casilla para modificarlo.
                        </p>

                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                @if (! $editMode)
                    <div class="grid gap-2">
                        <flux:input
                            wire:model="password"
                            id="password_create"
                            label="Contrasena"
                            type="password"
                            required
                            autocomplete="new-password"
                            placeholder="Cree una contrasena segura"
                        />
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-2">
                        <flux:input
                            wire:model="password_confirmation"
                            id="password_confirmation_create"
                            label="Confirmar contrasena"
                            type="password"
                            required
                            autocomplete="new-password"
                            placeholder="Repita la contrasena"
                        />
                    </div>
                @else
                    <div x-data="{ changePassword: false }" class="grid gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                id="toggle_password"
                                x-model="changePassword"
                                class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500"
                            >
                            <label for="toggle_password" class="cursor-pointer select-none text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Cambiar contrasena
                            </label>
                        </div>

                        <div x-show="changePassword" x-transition class="grid gap-4">
                            <div class="grid gap-2">
                                <flux:input
                                    wire:model="password"
                                    id="password_edit"
                                    label="Nueva contrasena"
                                    type="password"
                                    autocomplete="new-password"
                                    placeholder="Ingrese nueva contrasena"
                                />
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid gap-2">
                                <flux:input
                                    wire:model="password_confirmation"
                                    id="password_confirmation_edit"
                                    label="Confirmar nueva contrasena"
                                    type="password"
                                    autocomplete="new-password"
                                    placeholder="Repita la nueva contrasena"
                                />
                            </div>
                        </div>

                        <p x-show="! changePassword" class="text-xs text-zinc-500 dark:text-zinc-400">
                            La contrasena actual se mantendra sin cambios.
                        </p>
                    </div>
                @endif

                <div class="grid gap-2">
                    <flux:select
                        wire:model="role_id"
                        label="Rol del Usuario"
                        placeholder="Seleccione un rol"
                        required
                    >
                        <flux:select.option value="">-- Seleccione un rol --</flux:select.option>
                        @foreach ($roles as $role)
                            <flux:select.option value="{{ $role->id }}">
                                {{ $role->nombre }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    @error('role_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4 flex items-center justify-end gap-3">
                    <flux:button wire:click="closeModal" variant="subtle">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editMode ? 'Actualizar Usuario' : 'Crear Usuario' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <x-confirm-action />
</div>
