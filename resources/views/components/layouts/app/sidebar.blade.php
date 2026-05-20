<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <style>
            /* Sidebar oscuro: texto claro en ítems NO activos */
            #sidebar-nav a:not([aria-current]) {
                color: rgb(203 213 225); /* slate-300 */
                transition: color 0.15s ease;
            }
            #sidebar-nav a:not([aria-current]):hover {
                color: rgb(248 250 252); /* slate-50 */
            }
            /* Headings de grupo (Platform, Administrador, etc.) */
            #sidebar-nav [class*="group"] > span,
            #sidebar-nav [class*="heading"] {
                color: rgb(148 163 184); /* slate-400 */
            }
        </style>
    </head>
    <body class="min-h-screen bg-white">
        <flux:sidebar sticky stashable class="border-r border-slate-700 bg-slate-800">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="mr-5 flex items-center space-x-2" wire:navigate>
                <x-app-logo class="size-8" href="#"></x-app-logo>
            </a>

            <div id="sidebar-nav">
            <flux:navlist variant="outline">
                <flux:navlist.group heading="Platform" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>Dashboard</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group heading="Administrador" :expanded="request()->routeIs('users.*') || request()->routeIs('catalogo-roles.*')" class="mt-4" expandable>
                    <flux:navlist.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>Adm. Usuarios</flux:navlist.item>
                    <flux:navlist.item icon="shield-check" :href="route('catalogo-roles.index')" :current="request()->routeIs('catalogo-roles.*')" wire:navigate>Roles</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group heading="Reportes" expandable class="mt-4">
                    <flux:navlist.item icon="document-chart-bar" href="#" wire:navigate>
                        Reporte General
                    </flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group heading="Auditorías" expandable class="mt-4">
                    <flux:navlist.item icon="document-text" :href="route('inspeccion.tela')" wire:navigate>
                        Inspección de Tela
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>
            </div>

            <flux:spacer />

            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
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
        </flux:sidebar>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
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

        {{ $slot }}

        @fluxScripts
    </body>
</html>
