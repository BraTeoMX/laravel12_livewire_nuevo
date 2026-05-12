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

    public bool $showModal = false;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role_id = '';

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'integer', 'exists:catalogo_roles,id'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'role_id.required' => 'Debe seleccionar un rol para el usuario.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        
        event(new Registered($user));

        $this->reset(['name', 'email', 'password', 'password_confirmation', 'role_id', 'showModal']);
        
        session()->flash('status', 'Usuario creado correctamente.');
    }

    public function with(): array
    {
        return [
            'users' => User::with('role')->latest()->paginate(10),
            'roles' => CatalogoRole::orderBy('nombre')->get(),
        ];
    }

    public function toggleEstatus(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->estatus = !$user->estatus;
        $user->save();
    }

    public function edit(int $userId): void
    {
        // Funcionalidad de edición se implementará en futuras versiones
        // Por ahora solo muestra un mensaje o podría abrir un modal
    }
}; ?>

<div class="flex flex-col gap-6 w-full max-w-5xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
            Administración de Usuarios
        </h2>
        <flux:button wire:click="$set('showModal', true)" variant="primary">
            Crear Usuario
        </flux:button>
    </div>

    @if (session('status'))
        <div class="rounded-md bg-green-50 p-4 border border-green-200 dark:bg-green-900/20 dark:border-green-900/50">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-400">{{ session('status') }}</p>
                </div>
            </div>
        </div>
    @endif

     <!-- Tabla de Usuarios -->
     <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
         <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
              <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                  <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">ID</th>
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
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100">{{ $user->id }}</td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-zinc-100 font-medium">{{ $user->name }}</td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                              {{ $user->role ? $user->role->nombre : '-' }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                              @if($user->estatus)
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
                                      wire:click="toggleEstatus({{ $user->id }})"
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

    <!-- Modal para crear usuario -->
    <flux:modal wire:model="showModal" class="md:w-[480px]">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                Crear Nuevo Usuario
            </h2>

            <form wire:submit="save" class="flex flex-col gap-6">
                <!-- Nombre -->
                <div class="grid gap-2">
                    <flux:input wire:model="name" id="name" label="Nombre" type="text" required autofocus autocomplete="name" placeholder="Nombre completo" />
                </div>

                <!-- Correo Electrónico -->
                <div class="grid gap-2">
                    <flux:input wire:model="email" id="email" label="Correo electrónico" type="email" required autocomplete="email" placeholder="correo@ejemplo.com" />
                </div>

                <!-- Contraseña -->
                <div class="grid gap-2">
                    <flux:input wire:model="password" id="password" label="Contraseña" type="password" required autocomplete="new-password" placeholder="Contraseña" />
                </div>

                <!-- Confirmar Contraseña -->
                <div class="grid gap-2">
                    <flux:input wire:model="password_confirmation" id="password_confirmation" label="Confirmar contraseña" type="password" required autocomplete="new-password" placeholder="Confirmar contraseña" />
                </div>

                <!-- Rol -->
                <div class="grid gap-2">
                    <flux:select
                        wire:model="role_id"
                        label="Rol del Usuario"
                        placeholder="Seleccione un rol"
                        required
                    >
                        <flux:select.option value="">-- Seleccione un rol --</flux:select.option>
                        @foreach($roles as $role)
                            <flux:select.option value="{{ $role->id }}">
                                {{ $role->nombre }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('role_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 mt-4">
                    <flux:button wire:click="$set('showModal', false)" variant="subtle">
                        Cancelar
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        Crear cuenta
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
