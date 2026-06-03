<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $fieldType = filter_var($this->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'employee_number';

        // Verificar si el usuario existe y su estatus
        $user = \App\Models\User::where($fieldType, $this->email)->first();

        if ($user && !$user->estatus) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Su cuenta está desactivada. Contacte al administrador.',
            ]);
        }

        if (! Auth::attempt([$fieldType => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        if ((int) Auth::user()->role_id === 5) {
            $this->redirectRoute('auditor.dashboard', navigate: true);

            return;
        }

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
        <x-auth-header title="Iniciar sesión" description="Ingresa tu correo electrónico y contraseña" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
            <flux:input wire:model="email" label="{{ __('Numero de empleo o Correo electrónico') }}" type="text" name="email" required autofocus autocomplete="username" placeholder="123456 o correo@ejemplo.com" />

        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model="password"
                label="{{ __('Contraseña') }}"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Contraseña"
            />

        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" label="{{ __('Recuérdame') }}" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Iniciar sesión') }}</flux:button>
        </div>
    </form>


</div>
