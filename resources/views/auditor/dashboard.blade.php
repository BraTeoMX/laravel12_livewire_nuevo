<x-layouts.app>
    <div class="space-y-6">
        <div class="flex flex-col gap-2">
            <flux:heading size="xl" level="1">Panel de Auditorias</flux:heading>
            <flux:subheading>Accesos rapidos para las herramientas de auditoria disponibles.</flux:subheading>
        </div>

        @php
            $quickAccessItems = [
                [
                    'title' => 'Inspeccion de Tela',
                    'description' => 'Registro y seguimiento de auditorias de inspeccion de tela.',
                    'href' => route('inspeccion.tela'),
                    'icon' => 'document-text',
                    'available' => true,
                ],
                [
                    'title' => 'Auditoria de Materia Prima',
                    'description' => 'Modulo reservado para futuras auditorias de materia prima.',
                    'href' => '#',
                    'icon' => 'clipboard-document-check',
                    'available' => false,
                ],
                [
                    'title' => 'Auditoria de Proceso',
                    'description' => 'Modulo reservado para controles de proceso en planta.',
                    'href' => '#',
                    'icon' => 'adjustments-horizontal',
                    'available' => false,
                ],
                [
                    'title' => 'Auditoria de Producto Terminado',
                    'description' => 'Modulo reservado para validaciones finales de calidad.',
                    'href' => '#',
                    'icon' => 'archive-box',
                    'available' => false,
                ],
                [
                    'title' => 'Hallazgos y Acciones',
                    'description' => 'Modulo reservado para seguimiento de hallazgos correctivos.',
                    'href' => '#',
                    'icon' => 'exclamation-triangle',
                    'available' => false,
                ],
                [
                    'title' => 'Reportes de Auditoria',
                    'description' => 'Modulo reservado para reportes y consultas historicas.',
                    'href' => '#',
                    'icon' => 'document-chart-bar',
                    'available' => false,
                ],
            ];
        @endphp

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($quickAccessItems as $item)
                <a
                    href="{{ $item['href'] }}"
                    @class([
                        'group flex min-h-44 flex-col justify-between rounded-lg border bg-white p-5 transition',
                        'border-slate-200 shadow-sm hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md' => $item['available'],
                        'pointer-events-none border-dashed border-slate-200 opacity-75' => ! $item['available'],
                    ])
                    @if ($item['available']) wire:navigate @endif
                    @if (! $item['available']) aria-disabled="true" @endif
                >
                    <div class="space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div
                                @class([
                                    'flex size-11 items-center justify-center rounded-lg',
                                    'bg-indigo-50 text-indigo-700 group-hover:bg-indigo-100' => $item['available'],
                                    'bg-slate-100 text-slate-400' => ! $item['available'],
                                ])
                            >
                                <flux:icon :name="$item['icon']" class="size-5" />
                            </div>

                            @if (! $item['available'])
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">
                                    Proximamente
                                </span>
                            @endif
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-slate-900">{{ $item['title'] }}</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">{{ $item['description'] }}</p>
                        </div>
                    </div>

                    <div
                        @class([
                            'mt-5 flex items-center text-sm font-medium',
                            'text-indigo-700' => $item['available'],
                            'text-slate-400' => ! $item['available'],
                        ])
                    >
                        {{ $item['available'] ? 'Abrir modulo' : 'Ruta pendiente' }}
                        <flux:icon name="arrow-right" class="ml-2 size-4" />
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-layouts.app>
