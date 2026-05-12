<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white">
        <flux:header container class="border-b border-gray-800 bg-gray-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <a href="{{ route('dashboard') }}" class="ml-2 mr-5 flex items-center space-x-2 lg:ml-0" wire:navigate>
                <x-app-logo class="size-8" href="#"></x-app-logo>
            </a>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')" wire:navigate>
                    Dashboard
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="mr-1.5 space-x-0.5 py-0!">
                <flux:tooltip content="Search" position="bottom">
                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" label="Search" />
                </flux:tooltip>
            </flux:navbar>

            <flux:dropdown position="top" align="end">
                <flux:profile
                    class="cursor-pointer"
                    :initials="auth()->user()->initials()"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-gray-700 text-gray-100 font-semibold"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold text-gray-100">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs text-gray-300">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <flux:sidebar stashable sticky class="lg:hidden border-r border-gray-800 bg-gray-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="ml-1 flex items-center space-x-2" wire:navigate>
                <x-app-logo class="size-8" href="#"></x-app-logo>
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group heading="Platform">
                    <flux:navlist.item icon="layout-grid" href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')" wire:navigate>
                        Dashboard
                    </flux:navlist.item>
                </flux:navlist.group>

                @can('manage-users')
                <flux:navlist.group heading="Administrador" :expanded="request()->routeIs('users.*') || request()->routeIs('catalogo-roles.*')" expandable>
                    <flux:navlist.item icon="users" href="{{ route('users.index') }}" :current="request()->routeIs('users.*')" wire:navigate>
                        Adm. Usuarios
                    </flux:navlist.item>
                    <flux:navlist.item icon="shield-check" href="{{ route('catalogo-roles.index') }}" :current="request()->routeIs('catalogo-roles.*')" wire:navigate>
                        Roles
                    </flux:navlist.item>
                </flux:navlist.group>
                @endcan

                <flux:navlist.group heading="Reportes" expandable>
                    <flux:navlist.item icon="document-chart-bar" href="#" wire:navigate>
                        Reporte General
                    </flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group heading="Auditorías" expandable>
                    <flux:navlist.item icon="shield-exclamation" href="#" wire:navigate>
                        Log de Actividades
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
