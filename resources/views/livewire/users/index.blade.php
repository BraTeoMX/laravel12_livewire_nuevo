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
    public ?int $role_id = null;

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'integer', 'exists:catalogo_roles,id'],
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
                     <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Fecha de Creación</th>
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
                         <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $user->created_at->format('d/m/Y H:i') }}</td>
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
                <!-- Name -->
                <div class="grid gap-2">
                    <flux:input wire:model="name" id="name" label="{{ __('Name') }}" type="text" required autofocus autocomplete="name" placeholder="Full name" />
                </div>

                <!-- Email Address -->
                <div class="grid gap-2">
                    <flux:input wire:model="email" id="email" label="{{ __('Email address') }}" type="email" required autocomplete="email" placeholder="email@example.com" />
                </div>

                <!-- Password -->
                <div class="grid gap-2">
                    <flux:input wire:model="password" id="password" label="{{ __('Password') }}" type="password" required autocomplete="new-password" placeholder="Password" />
                </div>

                <!-- Confirm Password -->
                <div class="grid gap-2">
                    <flux:input wire:model="password_confirmation" id="password_confirmation" label="{{ __('Confirm password') }}" type="password" required autocomplete="new-password" placeholder="Confirm password" />
                </div>

                <!-- Rol -->
                <div class="grid gap-2">
                    <flux:select
                        wire:model="role_id"
                        label="Rol del Usuario"
                        placeholder="Seleccione un rol"
                        required
                    >
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
