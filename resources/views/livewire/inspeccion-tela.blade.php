<div class="space-y-6">
    <section class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="lg">Inspección de Tela</flux:heading>
                <flux:subheading>Búsqueda de información desde SQL Server</flux:subheading>
            </div>
        </div>

        <form wire:submit.prevent="buscarInformacion" class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_auto]">
            <flux:input
                wire:model="terminoBusqueda"
                label="Orden de compra o recepción"
                placeholder="Ej. REC12345 u OC12345"
                autocomplete="off"
                clearable
            />

            <div class="flex flex-col items-stretch gap-2 sm:flex-row sm:items-end">
                <flux:button
                    type="submit"
                    variant="primary"
                    icon="magnifying-glass"
                    wire:loading.attr="disabled"
                    wire:target="buscarInformacion"
                >
                    <span wire:loading.remove wire:target="buscarInformacion">Buscar</span>
                    <span wire:loading wire:target="buscarInformacion">Buscando...</span>
                </flux:button>

                <flux:button
                    type="button"
                    variant="filled"
                    icon="arrow-path"
                    wire:click="forzarBusquedaDirecta"
                    wire:loading.attr="disabled"
                    wire:target="forzarBusquedaDirecta"
                >
                    <span wire:loading.remove wire:target="forzarBusquedaDirecta">Forzar Búsqueda Directa</span>
                    <span wire:loading wire:target="forzarBusquedaDirecta">Consultando...</span>
                </flux:button>

                <flux:button
                    type="button"
                    variant="subtle"
                    icon="x-mark"
                    wire:click="limpiarBusqueda"
                >
                    Limpiar
                </flux:button>
            </div>
        </form>

        @error('terminoBusqueda')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror

        @if ($mensajeBusqueda)
            @php
                $alertClasses = match ($tipoMensajeBusqueda) {
                    'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200',
                    'warning' => 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-200',
                    default => 'border-red-200 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200',
                };
            @endphp

            <div class="mt-4 rounded-md border px-3 py-2 text-sm {{ $alertClasses }}">
                {{ $mensajeBusqueda }}
            </div>
        @endif
    </section>

    <section class="rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 p-4 dark:border-zinc-700">
            <flux:heading size="lg">Resultados de búsqueda</flux:heading>
            <flux:subheading>Registros encontrados y cacheados temporalmente en MySQL</flux:subheading>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                <thead class="bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Recepción</th>
                        <th class="px-4 py-3 text-left font-medium">Orden compra</th>
                        <th class="px-4 py-3 text-left font-medium">Proveedor</th>
                        <th class="px-4 py-3 text-left font-medium">Artículo</th>
                        <th class="px-4 py-3 text-left font-medium">Producto</th>
                        <th class="px-4 py-3 text-left font-medium">Ancho</th>
                        <th class="px-4 py-3 text-left font-medium">Talla</th>
                        <th class="px-4 py-3 text-left font-medium">Lote Intimark</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($resultadosBusqueda as $resultado)
                        <tr wire:key="resultado-{{ $resultado['numero_diario'] }}-{{ $resultado['lote_intimark'] }}-{{ $loop->index }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/70">
                            <td class="whitespace-nowrap px-4 py-3 text-zinc-700 dark:text-zinc-200">{{ $resultado['numero_diario'] ?? 'N/A' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-zinc-700 dark:text-zinc-200">{{ $resultado['orden_compra'] ?? 'N/A' }}</td>
                            <td class="min-w-48 px-4 py-3 text-zinc-700 dark:text-zinc-200">{{ $resultado['proveedor'] ?? 'N/A' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $resultado['articulo'] ?? 'N/A' }}</td>
                            <td class="min-w-64 px-4 py-3 text-zinc-700 dark:text-zinc-200">
                                <div>{{ $resultado['nombre_producto'] ?? 'N/A' }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $resultado['nombre_producto_externo'] ?? '' }}</div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-zinc-700 dark:text-zinc-200">
                                {{ $resultado['ancho_contratado'] ? $resultado['ancho_contratado'] . '"' : 'N/A' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-zinc-700 dark:text-zinc-200">{{ $resultado['talla'] ?? 'N/A' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-zinc-700 dark:text-zinc-200">{{ $resultado['lote_intimark'] ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                Ingresa una orden de compra o recepción para consultar información.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
