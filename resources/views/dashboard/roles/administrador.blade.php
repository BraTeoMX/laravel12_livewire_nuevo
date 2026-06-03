@php
    $totalUsers = \App\Models\User::count();
    $activeUsers = \App\Models\User::where('estatus', 1)->count();
    $inactiveUsers = $totalUsers - $activeUsers;
    $totalRoles = \App\Models\CatalogoRole::count();
    $appUrl = config('app.url');
    $dbConnection = config('database.default');
    $phpVersion = PHP_VERSION;
    $laravelVersion = app()->version();
@endphp

<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <flux:heading size="xl" level="1">Panel de Desarrollo y Administración</flux:heading>
                <flux:subheading>
                    Acceso absoluto y total del sistema. Diseñado exclusivamente para el control técnico y de desarrollo.
                </flux:subheading>
            </div>
            
            <div class="flex gap-2">
                <flux:button variant="primary" icon="cog-6-tooth" href="{{ route('settings.profile') }}" wire:navigate>
                    Configuración de Perfil
                </flux:button>
            </div>
        </div>

        <!-- Banner de Bienvenida con Degradado Premium -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-900 via-slate-900 to-indigo-950 p-6 text-white shadow-xl">
            <div class="relative z-10 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-500/25 px-3 py-1 text-xs font-semibold text-indigo-200">
                    <span class="h-2 w-2 rounded-full bg-indigo-400 animate-pulse"></span>
                    Rol: Administrador General (Dev)
                </span>
                <h2 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl text-white">
                    ¡Hola, {{ auth()->user()->name }}!
                </h2>
                <p class="mt-3 text-lg text-indigo-100">
                    Este panel te permite gestionar y auditar la infraestructura interna de la aplicación, supervisar el estatus de usuarios y acceder a cualquier módulo del sistema sin restricciones.
                </p>
            </div>
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl"></div>
            <div class="absolute right-32 bottom-0 h-32 w-32 rounded-full bg-blue-500/10 blur-2xl"></div>
        </div>

        <!-- Métricas Rápidas -->
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-zinc-500">Usuarios Registrados</span>
                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-blue-700/10 ring-inset">Total</span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-zinc-950 leading-tight">{{ $totalUsers }}</span>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-zinc-500">Usuarios Activos</span>
                    <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-600/10 ring-inset">En línea</span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-emerald-600 leading-tight">{{ $activeUsers }}</span>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-zinc-500">Usuarios Inactivos</span>
                    <span class="inline-flex items-center rounded-md bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-rose-600/10 ring-inset">Bloqueados</span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-rose-600 leading-tight">{{ $inactiveUsers }}</span>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-zinc-500">Roles del Sistema</span>
                    <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10 ring-inset">Catálogo</span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-zinc-950 leading-tight">{{ $totalRoles }}</span>
                </div>
            </div>
        </div>

        <!-- Secciones de Acceso Rápido y Diagnóstico de Servidor -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Accesos Rápidos de Administración -->
            <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs space-y-4">
                <div>
                    <flux:heading size="lg">Accesos Directos Administrativos</flux:heading>
                    <flux:subheading>Navega a los paneles principales con tus permisos de superusuario.</flux:subheading>
                </div>

                <div class="grid gap-3">
                    <a href="{{ route('users.index') }}" wire:navigate class="flex items-center justify-between p-3 rounded-lg border border-zinc-150 hover:bg-zinc-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="p-2 bg-indigo-50 text-indigo-700 rounded-lg">
                                <flux:icon.users class="size-5" />
                            </span>
                            <div>
                                <h4 class="font-medium text-zinc-900">Administrar Usuarios</h4>
                                <p class="text-xs text-zinc-500">Alta, baja, cambio de estatus y edición de perfiles.</p>
                            </div>
                        </div>
                        <flux:icon.chevron-right class="size-4 text-zinc-400" />
                    </a>

                    <a href="{{ route('catalogo-roles.index') }}" wire:navigate class="flex items-center justify-between p-3 rounded-lg border border-zinc-150 hover:bg-zinc-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="p-2 bg-amber-50 text-amber-700 rounded-lg">
                                <flux:icon.shield-check class="size-5" />
                            </span>
                            <div>
                                <h4 class="font-medium text-zinc-900">Control de Roles</h4>
                                <p class="text-xs text-zinc-500">Lista, permisos e identidades de roles asignados.</p>
                            </div>
                        </div>
                        <flux:icon.chevron-right class="size-4 text-zinc-400" />
                    </a>

                    <a href="{{ route('dashboard.gerente') }}" wire:navigate class="flex items-center justify-between p-3 rounded-lg border border-zinc-150 hover:bg-zinc-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="p-2 bg-emerald-50 text-emerald-700 rounded-lg">
                                <flux:icon.beaker class="size-5" />
                            </span>
                            <div>
                                <h4 class="font-medium text-zinc-900">Ver Dashboard Operativo (Gerente)</h4>
                                <p class="text-xs text-zinc-500">Dashboard de Laboratorio de Pruebas de Telas.</p>
                            </div>
                        </div>
                        <flux:icon.chevron-right class="size-4 text-zinc-400" />
                    </a>

                    <a href="{{ route('inspeccion.tela') }}" wire:navigate class="flex items-center justify-between p-3 rounded-lg border border-zinc-150 hover:bg-zinc-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="p-2 bg-rose-50 text-rose-700 rounded-lg">
                                <flux:icon.document-text class="size-5" />
                            </span>
                            <div>
                                <h4 class="font-medium text-zinc-900">Inspección de Tela (Auditoría)</h4>
                                <p class="text-xs text-zinc-500">Formulario y registros de fallas de tela.</p>
                            </div>
                        </div>
                        <flux:icon.chevron-right class="size-4 text-zinc-400" />
                    </a>
                </div>
            </section>

            <!-- Diagnóstico e Info del Sistema -->
            <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs space-y-4">
                <div>
                    <flux:heading size="lg">Información de Diagnóstico</flux:heading>
                    <flux:subheading>Parámetros del entorno local del servidor.</flux:subheading>
                </div>

                <div class="divide-y divide-zinc-100 text-sm">
                    <div class="flex justify-between py-2.5">
                        <span class="text-zinc-500">Versión de Laravel</span>
                        <span class="font-mono text-zinc-900 font-semibold">{{ $laravelVersion }}</span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-zinc-500">Versión de PHP</span>
                        <span class="font-mono text-zinc-900 font-semibold">{{ $phpVersion }}</span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-zinc-500">Motor de Base de Datos</span>
                        <span class="font-mono text-zinc-900 font-semibold">{{ $dbConnection }}</span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-zinc-500">URL del Sistema</span>
                        <span class="font-mono text-zinc-900">{{ $appUrl }}</span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-zinc-500">Entorno actual</span>
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-800 ring-1 ring-amber-600/25">
                            {{ app()->environment() }}
                        </span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-zinc-500">Zona Horaria</span>
                        <span class="text-zinc-900">{{ config('app.timezone') }}</span>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
