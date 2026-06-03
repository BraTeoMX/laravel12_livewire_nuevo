<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <flux:heading size="xl" level="1">Portal de Gestión Operativa</flux:heading>
            <flux:subheading>
                Mantenimiento de información de lotes, registros, altas y bajas del laboratorio.
            </flux:subheading>
        </div>

        <!-- Panel de Acciones Rápidas -->
        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 bg-blue-50 text-blue-700 rounded-lg">
                        <flux:icon.plus class="size-6" />
                    </span>
                    <h3 class="text-lg font-bold text-zinc-900 leading-tight">Alta de Lotes</h3>
                </div>
                <p class="text-sm text-zinc-500">
                    Registra un nuevo lote de tela en el sistema para iniciar las pruebas físicas y químicas correspondientes.
                </p>
                <div>
                    <flux:button variant="primary" icon="plus" class="w-full">
                        Nueva Alta de Lote
                    </flux:button>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 bg-rose-50 text-rose-700 rounded-lg">
                        <flux:icon.trash class="size-6" />
                    </span>
                    <h3 class="text-lg font-bold text-zinc-900 leading-tight">Bajas y Ajustes</h3>
                </div>
                <p class="text-sm text-zinc-500">
                    Dar de baja registros duplicados o incorrectos del sistema con la justificación del supervisor.
                </p>
                <div>
                    <flux:button variant="danger" icon="trash" class="w-full">
                        Solicitar Baja
                    </flux:button>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 bg-amber-50 text-amber-700 rounded-lg">
                        <flux:icon.arrow-path class="size-6" />
                    </span>
                    <h3 class="text-lg font-bold text-zinc-900 leading-tight">Control de Estatus</h3>
                </div>
                <p class="text-sm text-zinc-500">
                    Cambiar el estado de los lotes de tela (En Espera, En Pruebas, Rechazado o Aprobado).
                </p>
                <div>
                    <flux:button variant="filled" icon="arrow-path" class="w-full">
                        Actualizar Estatus
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Sección de Resumen Operativo -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Estadísticas operativas en tiempo real -->
            <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs space-y-4">
                <div>
                    <flux:heading size="lg">Resumen de Movimientos</flux:heading>
                    <flux:subheading>Registros asignados a tu cuenta esta semana.</flux:subheading>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-lg bg-zinc-50 border border-zinc-150">
                        <div class="text-xs text-zinc-400 font-semibold uppercase">Altas del Día</div>
                        <div class="text-3xl font-bold text-zinc-800 mt-2">12</div>
                    </div>
                    <div class="p-4 rounded-lg bg-zinc-50 border border-zinc-150">
                        <div class="text-xs text-zinc-400 font-semibold uppercase">Pendientes de Firma</div>
                        <div class="text-3xl font-bold text-zinc-800 mt-2">3</div>
                    </div>
                </div>
            </section>

            <!-- Historial Reciente de Operaciones -->
            <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs space-y-4">
                <div>
                    <flux:heading size="lg">Historial de Operaciones</flux:heading>
                    <flux:subheading>Últimas acciones registradas en el portal de gestión.</flux:subheading>
                </div>

                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-zinc-200" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center ring-8 ring-white">
                                            <flux:icon.check class="size-4" />
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-zinc-700">Alta de Lote <a href="#" class="font-medium text-zinc-900">LT-9080</a> por Brayam</p>
                                        </div>
                                        <div class="text-right text-xs whitespace-nowrap text-zinc-400">Hace 2 horas</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="relative pb-8">
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center ring-8 ring-white">
                                            <flux:icon.arrow-path class="size-4" />
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-zinc-700">Cambio de estatus del Lote <a href="#" class="font-medium text-zinc-900">LT-8742</a> a "Observación"</p>
                                        </div>
                                        <div class="text-right text-xs whitespace-nowrap text-zinc-400">Hace 5 horas</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
