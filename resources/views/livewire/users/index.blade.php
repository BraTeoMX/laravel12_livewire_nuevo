<?php

use App\Models\User;
use App\Models\CatalogoRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    // ─── Estado del Modal ──────────────────────────────────────────────────────
    public bool $showModal  = false;
    public bool $editMode   = false;
    public ?int $editUserId = null;

    // ─── Campos del Formulario ─────────────────────────────────────────────────
    public string $name                  = '';
    public string $email                 = '';
    public int    $employee_number       = 0;
    public string $password              = '';
    public string $password_confirmation = '';
    public string $role_id               = '';

    // ─── Flags de Control de Email ────────────────────────────────────────────
    /** Creación: genera el email automáticamente con la regla {num_empleado}@lab.com */
    public bool $autoGenerateEmail = false;
    /** Edición: permite desbloquear el campo de email para modificarlo */
    public bool $allowEditEmail    = false;

    // ─── Ciclo de Vida del Modal ───────────────────────────────────────────────

    /**
     * Abre el modal en modo CREACIÓN.
     * Garantiza que el estado sea limpio antes de mostrarse.
     */
    public function openCreateModal(): void
    {
        $this->resetFormState();
        $this->editMode   = false;
        $this->editUserId = null;
        $this->showModal  = true;
    }

    /**
     * Cierra el modal y limpia TODO el estado residual,
     * independientemente del modo en que estaba abierto.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFormState();
    }

    /**
     * Limpia las propiedades del formulario y los flags de edición.
     * Método privado de apoyo para garantizar consistencia del estado.
     */
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

    // ─── Flujo de Edición ──────────────────────────────────────────────────────

    /**
     * Abre el modal en modo EDICIÓN, cargando los datos del usuario seleccionado.
     */
    public function edit(int $userId): void
    {
        // Limpiar cualquier estado previo antes de cargar datos nuevos
        $this->resetFormState();

        $user = User::findOrFail($userId);

        $this->editUserId      = $user->id;
        $this->name            = $user->name;
        $this->email           = $user->email;
        $this->employee_number = $user->employee_number;
        $this->role_id         = (string) $user->role_id;

        // La contraseña se deja vacía intencionalmente en edición
        $this->password              = '';
        $this->password_confirmation = '';

        // El email comienza bloqueado por seguridad; el usuario debe habilitarlo
        $this->allowEditEmail = false;

        $this->editMode  = true;
        $this->showModal = true;
    }

    // ─── Persistencia ─────────────────────────────────────────────────────────

    public function save(): void
    {
        // Normalizar el nombre a mayúsculas antes de validar y persistir
        $this->name = strtoupper(trim($this->name));

        // ── Autogeneración de email (solo en modo Creación) ────────────────────
        if (! $this->editMode && $this->autoGenerateEmail) {
            // Regla: {numero_empleado}@lab.com — se garantiza unicidad por employee_number
            $this->email = strtolower($this->employee_number) . '@lab.com';
        }

        $rules = [
            'name'            => ['required', 'string', 'max:255'],
            'employee_number' => ['required', 'integer', 'unique:' . User::class . ',employee_number,' . ($this->editUserId ?? 'null')],
            'role_id'         => ['required', 'integer', 'exists:catalogo_roles,id'],
        ];

        // ── Validación de email según contexto ────────────────────────────────
        if (! $this->editMode && $this->autoGenerateEmail) {
            // Email autogenerado: se valida como cadena única, sin intervención del usuario
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:' . User::class . ',email'];
        } elseif ($this->editMode && ! $this->allowEditEmail) {
            // Edición con email bloqueado: no se valida ni se modifica
            $rules['email'] = ['sometimes'];
        } else {
            // Creación manual o edición desbloqueada: validación completa
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ',email,' . ($this->editUserId ?? 'null')];
        }

        // ── Validación de contraseña según contexto ───────────────────────────
        if (! $this->editMode) {
            $rules['password'] = ['required', 'string', 'confirmed', Rules\Password::defaults()];
        } else {
            $rules['password'] = ['nullable', 'string', 'confirmed', Rules\Password::defaults()];
        }

        $validated = $this->validate($rules, [
            'name.required'            => 'El nombre es obligatorio.',
            'name.string'              => 'El nombre debe ser una cadena de texto.',
            'name.max'                 => 'El nombre no puede tener más de 255 caracteres.',
            'email.required'           => 'El correo electrónico es obligatorio.',
            'email.email'              => 'El correo electrónico debe ser válido.',
            'email.unique'             => 'Este correo electrónico ya está registrado.',
            'employee_number.required' => 'El número de empleado es obligatorio.',
            'employee_number.integer'  => 'El número de empleado debe ser un valor numérico.',
            'employee_number.unique'   => 'Este número de empleado ya está registrado.',
            'password.required'        => 'La contraseña es obligatoria.',
            'password.confirmed'       => 'La confirmación de la contraseña no coincide.',
            'role_id.required'         => 'Debe seleccionar un rol para el usuario.',
            'role_id.exists'           => 'El rol seleccionado no es válido.',
        ]);

        $dataToStore = $validated;

        // Asegurar que el email autogenerado se incluya en los datos a guardar
        if (! $this->editMode && $this->autoGenerateEmail) {
            $dataToStore['email'] = $this->email;
        }

        // En edición con email bloqueado, excluir el email de la actualización
        if ($this->editMode && ! $this->allowEditEmail) {
            unset($dataToStore['email']);
        }

        // Solo hashear si se proporcionó contraseña (relevante en edición)
        if (! empty($validated['password'])) {
            $dataToStore['password'] = Hash::make($validated['password']);
        } else {
            unset($dataToStore['password']);
        }

        if ($this->editMode && $this->editUserId) {
            $user = User::findOrFail($this->editUserId);
            $user->update($dataToStore);
            $this->dispatch('notify', type: 'success', message: 'Usuario actualizado correctamente.');
        } else {
            $user = User::create($dataToStore);
            event(new Registered($user));
            $this->dispatch('notify', type: 'success', message: 'Usuario creado correctamente.');
        }

        $this->closeModal();
    }

    // ─── Datos para la Vista ───────────────────────────────────────────────────

    public function with(): array
    {
        return [
            'users' => User::with('role')->latest()->paginate(10),
            'roles' => CatalogoRole::orderBy('nombre')->get(),
        ];
    }

    // ─── Acciones de Tabla ─────────────────────────────────────────────────────

    public function toggleEstatus(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->estatus = ! $user->estatus;
        $user->save();
        
        $estado = $user->estatus ? 'activado' : 'desactivado';
        $this->dispatch('notify', type: 'success', message: "El usuario ha sido {$estado}.");
    }
}; ?>

<div class="flex flex-col gap-6 w-full max-w-5xl mx-auto p-4 sm:p-6 lg:p-8">

    {{-- ─── Encabezado ─────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
            Administración de Usuarios
        </h2>

        {{--
            CORRECCIÓN CLAVE: Se reemplazó `$set('showModal', true)` por el método
            dedicado `openCreateModal()`, que garantiza un estado limpio antes de
            mostrar el formulario de creación.
        --}}
        <flux:button wire:click="openCreateModal" variant="primary">
            Crear Usuario
        </flux:button>
    </div>


    {{-- ─── Tabla de Usuarios ───────────────────────────────────────────────── --}}
    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Número de Empleado</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Nombre</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Rol</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Estatus</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100 font-medium">{{ $user->employee_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100 font-medium">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $user->role ? $user->role->nombre : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                            @if ($user->estatus)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                            <div class="flex items-center gap-2">
                                <flux:button wire:click="edit({{ $user->id }})" variant="subtle" size="sm">
                                    Editar
                                </flux:button>
                                <button
                                    x-data
                                    @click="$dispatch('open-confirm', { method: 'toggleEstatus', params: {{ $user->id }} })"
                                    class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium transition-colors
                                        {{ $user->estatus
                                            ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400'
                                            : 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400' }}"
                                >
                                    {{ $user->estatus ? 'Desactivar' : 'Activar' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

    {{-- ─── Modal Unificado (Crear / Editar) ───────────────────────────────── --}}
    {{--
        `wire:model` vincula la visibilidad al servidor.
        El cierre nativo del modal (Escape / click fuera) llama a `closeModal()`
        a través del listener `@close`, asegurando el reset de estado.
    --}}
    <flux:modal wire:model="showModal" @close="$wire.closeModal()" class="md:w-[480px]">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                {{ $editMode ? 'Editar Usuario' : 'Crear Nuevo Usuario' }}
            </h2>

            <form wire:submit="save" class="flex flex-col gap-6">

                {{-- Número de Empleado --}}
                <div class="grid gap-2">
                    <flux:input
                        wire:model="employee_number"
                        id="employee_number"
                        label="Número de Empleado"
                        type="number"
                        required
                        autofocus
                        autocomplete="off"
                        placeholder="Ingrese número de empleado"
                    />
                    @error('employee_number')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nombre (se convierte a mayúsculas en tiempo real) --}}
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
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{--
                    ─── Campo de Correo Electrónico (lógica desacoplada por contexto) ─────
                    CREACIÓN : Campo editable por defecto. Checkbox "Autogenerar" lo
                               deshabilita y genera {num_empleado}@lab.com al guardar.
                    EDICIÓN  : Campo deshabilitado por defecto por seguridad. Checkbox
                               "Permitir edición" lo habilita para cambios manuales.

                    FIX: Se eliminó el conflicto x-model + wire:model en los checkboxes.
                    La reactividad del input se maneja 100% con Alpine (@entangle + x-bind:disabled).
                    El checkbox usa SOLO wire:model.live para sincronizar con Livewire.
                --}}
                @if (! $editMode)
                    {{-- ── Modo CREACIÓN: email manual o autogenerado ─────────────────── --}}
                    <div
                        x-data="{ auto: $wire.entangle('autoGenerateEmail') }"
                        class="grid gap-2"
                    >
                        {{-- Checkbox: SOLO wire:model.live (sin x-model para evitar conflicto) --}}
                        <div class="flex items-center gap-3 mb-1">
                            <input
                                type="checkbox"
                                id="auto_generate_email"
                                wire:model.live="autoGenerateEmail"
                                class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                            >
                            <label for="auto_generate_email" class="text-sm font-medium text-zinc-700 dark:text-zinc-300 cursor-pointer select-none">
                                Autogenerar correo
                                <span class="text-xs text-zinc-400 font-normal ml-1">(se usará {núm. empleado}@lab.com)</span>
                            </label>
                        </div>

                        {{-- Input: x-bind:disabled reacciona al estado Alpine "auto" en tiempo real --}}
                        <div x-bind:class="auto ? 'opacity-50' : ''">
                            <flux:input
                                wire:model="email"
                                id="email_create"
                                label="Correo electrónico"
                                type="email"
                                autocomplete="email"
                                placeholder="correo@ejemplo.com"
                                x-bind:disabled="auto"
                            />
                        </div>
                        <p x-show="auto" class="text-xs text-zinc-500 dark:text-zinc-400 -mt-1">
                            ✨ Se generará automáticamente al guardar: <strong>{{ $employee_number ?: '{núm. empleado}' }}@lab.com</strong>
                        </p>
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    {{-- ── Modo EDICIÓN: email bloqueado por defecto ──────────────────── --}}
                    <div
                        x-data="{ canEdit: $wire.entangle('allowEditEmail') }"
                        class="grid gap-2"
                    >
                        {{-- Checkbox: SOLO wire:model.live (sin x-model para evitar conflicto) --}}
                        <div class="flex items-center gap-3 mb-1">
                            <input
                                type="checkbox"
                                id="allow_edit_email"
                                wire:model.live="allowEditEmail"
                                class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                            >
                            <label for="allow_edit_email" class="text-sm font-medium text-zinc-700 dark:text-zinc-300 cursor-pointer select-none">
                                Permitir edición del correo
                            </label>
                        </div>

                        {{-- Input: x-bind:disabled reacciona al estado Alpine "canEdit" en tiempo real --}}
                        <div x-bind:class="! canEdit ? 'opacity-60' : ''">
                            <flux:input
                                wire:model="email"
                                id="email_edit"
                                label="Correo electrónico"
                                type="email"
                                autocomplete="email"
                                placeholder="correo@ejemplo.com"
                                x-bind:disabled="! canEdit"
                            />
                        </div>
                        <p x-show="! canEdit" class="text-xs text-zinc-400 dark:text-zinc-500 -mt-1">
                            🔒 El correo está protegido. Marque la casilla para modificarlo.
                        </p>
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                {{--
                    ─── Campo de Contraseña (lógica desacoplada por contexto) ───────────
                    CREACIÓN : Campo obligatorio con indicador visual claro.
                    EDICIÓN  : Campo opcional con nota explicativa; se oculta si no
                               se desea modificar (Alpine.js para UX local sin round-trip).
                --}}
                @if (! $editMode)
                    {{-- Modo CREACIÓN: contraseña requerida --}}
                    <div class="grid gap-2">
                        <flux:input
                            wire:model="password"
                            id="password_create"
                            label="Contraseña"
                            type="password"
                            required
                            autocomplete="new-password"
                            placeholder="Cree una contraseña segura"
                        />
                        @error('password')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-2">
                        <flux:input
                            wire:model="password_confirmation"
                            id="password_confirmation_create"
                            label="Confirmar contraseña"
                            type="password"
                            required
                            autocomplete="new-password"
                            placeholder="Repita la contraseña"
                        />
                    </div>
                @else
                    {{-- Modo EDICIÓN: contraseña opcional, controlada con Alpine.js --}}
                    <div
                        x-data="{ changePassword: false }"
                        class="grid gap-3 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4"
                    >
                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                id="toggle_password"
                                x-model="changePassword"
                                class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500"
                            >
                            <label for="toggle_password" class="text-sm font-medium text-zinc-700 dark:text-zinc-300 cursor-pointer select-none">
                                Cambiar contraseña
                            </label>
                        </div>

                        <div x-show="changePassword" x-transition class="grid gap-4">
                            <div class="grid gap-2">
                                <flux:input
                                    wire:model="password"
                                    id="password_edit"
                                    label="Nueva contraseña"
                                    type="password"
                                    autocomplete="new-password"
                                    placeholder="Ingrese nueva contraseña"
                                />
                                @error('password')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid gap-2">
                                <flux:input
                                    wire:model="password_confirmation"
                                    id="password_confirmation_edit"
                                    label="Confirmar nueva contraseña"
                                    type="password"
                                    autocomplete="new-password"
                                    placeholder="Repita la nueva contraseña"
                                />
                            </div>
                        </div>

                        <p x-show="! changePassword" class="text-xs text-zinc-500 dark:text-zinc-400">
                            La contraseña actual se mantendrá sin cambios.
                        </p>
                    </div>
                @endif

                {{-- Rol --}}
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
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Acciones --}}
                <div class="flex items-center justify-end gap-3 mt-4">
                    {{--
                        CORRECCIÓN CLAVE: Se reemplazó `$set('showModal', false)` por
                        `closeModal()` para garantizar el reset completo del estado
                        al cancelar, evitando contaminación hacia la próxima apertura.
                    --}}
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
