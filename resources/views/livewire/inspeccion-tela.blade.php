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
                placeholder="Ej. REC12345"
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

    <section class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 pb-4 dark:border-zinc-700">
            <flux:heading size="lg">1. Encabezado</flux:heading>
            <flux:subheading>Datos base del lote seleccionado</flux:subheading>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">
            <flux:select
                wire:model="maquina"
                label="Máquina"
                placeholder="Selecciona una máquina"
                class="xl:col-span-2"
            >
                @forelse ($maquinasOptions as $maquinaOption)
                    <flux:select.option value="{{ $maquinaOption }}">{{ $maquinaOption }}</flux:select.option>
                @empty
                    <flux:select.option value="">No hay máquinas disponibles</flux:select.option>
                @endforelse
            </flux:select>

            <flux:select
                wire:model.live="lote_intimark"
                label="Lote Intimark"
                placeholder="Busca para cargar opciones"
                class="xl:col-span-2"
                :disabled="empty($loteIntimarkOptions)"
            >
                @forelse ($loteIntimarkOptions as $loteOption)
                    <flux:select.option value="{{ $loteOption }}">{{ $loteOption }}</flux:select.option>
                @empty
                    <flux:select.option value="">Busca para cargar opciones</flux:select.option>
                @endforelse
            </flux:select>

            <flux:input
                wire:model="articulo"
                label="Artículo"
                readonly
                class="xl:col-span-2"
            />

            <flux:input
                wire:model="proveedor"
                label="Proveedor"
                readonly
                class="xl:col-span-2"
            />

            <flux:input
                wire:model="color_nombre"
                label="Nombre Color"
                readonly
                class="xl:col-span-2"
            />

            <flux:input
                wire:model.live="ancho_contratado_input"
                type="number"
                min="0"
                max="1000"
                step="1"
                label="Ancho Contratado (Pulgadas)"
                placeholder="0"
                class="xl:col-span-1"
            />

            <flux:input
                wire:model="ancho_contratado_cm"
                type="number"
                label="Ancho Contratado (Centímetros)"
                readonly
                class="xl:col-span-1"
            />

            <flux:input
                wire:model="material"
                label="Material"
                readonly
                class="xl:col-span-2"
            />

            <flux:input
                wire:model="orden_compra"
                label="Orden Compra"
                readonly
                class="xl:col-span-1"
            />

            <flux:input
                wire:model="numero_recepcion"
                label="No. Recepción"
                readonly
                class="xl:col-span-1"
            />
        </div>
    </section>

    <section class="rounded-lg border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 pb-4 dark:border-zinc-700">
            <flux:heading size="lg">2. Detalle de Inspección</flux:heading>
            <flux:subheading>Parámetros iniciales del rollo inspeccionado</flux:subheading>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">
            <flux:input
                wire:model.live.debounce.300ms="ancho_cortable"
                type="number"
                step="0.01"
                label="Ancho Cortable"
                class="xl:col-span-2"
            />

            <flux:input
                wire:model.live.debounce.300ms="numero_piezas"
                type="number"
                step="1"
                label="# Piezas"
                class="xl:col-span-2"
            />

            <flux:input
                wire:model.live.debounce.300ms="numero_lote"
                label="Lote Teñido"
                class="xl:col-span-2"
            />

            <flux:input
                wire:model.live.debounce.300ms="yarda_ticket"
                type="number"
                step="0.01"
                label="Yarda Ticket"
                class="xl:col-span-2"
            />

            <flux:input
                wire:model.live.debounce.300ms="yarda_actual"
                type="number"
                step="0.01"
                label="Yarda Actual"
                class="xl:col-span-2"
            />

            <div class="md:col-span-2 xl:col-span-6">
                <div class="mb-2">
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Defectos por Puntos</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Selecciona el total por tipo. El desglose se integrará en el siguiente paso.</p>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                    @foreach ([
                        'puntos_1' => '1 Punto',
                        'puntos_2' => '2 Puntos',
                        'puntos_3' => '3 Puntos',
                        'puntos_4' => '4 Puntos',
                    ] as $campoPuntos => $labelPuntos)
                        @php
                            $defectosProperty = 'defectos_' . $campoPuntos;
                            $defectos = $this->{$defectosProperty};
                            $totalPuntos = (int) $this->{$campoPuntos};
                        @endphp

                        <div class="space-y-3 rounded-md border border-zinc-200 p-3 dark:border-zinc-700">
                            <flux:select wire:model.live="{{ $campoPuntos }}" label="{{ $labelPuntos }}">
                                @for ($i = 0; $i <= 20; $i++)
                                    <flux:select.option value="{{ $i }}">{{ $i }}</flux:select.option>
                                @endfor
                            </flux:select>

                            @if ($totalPuntos > 0)
                                <div class="space-y-2">
                                    @foreach ($defectos as $index => $fila)
                                        <div class="grid grid-cols-[minmax(0,1fr)_5rem_auto] items-end gap-2" wire:key="{{ $defectosProperty }}-{{ $index }}">
                                            <flux:select
                                                wire:model.live="{{ $defectosProperty }}.{{ $index }}.defecto_id"
                                                label="Defecto"
                                                placeholder="Selecciona"
                                            >
                                                @php
                                                    $defectosSeleccionadosOtros = collect($defectos)
                                                        ->reject(fn ($d, $k) => $k === $index)
                                                        ->pluck('defecto_id')
                                                        ->filter()
                                                        ->toArray();
                                                @endphp
                                                @foreach ($catalogoDefectosOptions as $defecto)
                                                    @if (!in_array($defecto['id'], $defectosSeleccionadosOtros))
                                                        <flux:select.option value="{{ $defecto['id'] }}">{{ $defecto['nombre'] }}</flux:select.option>
                                                    @endif
                                                @endforeach
                                            </flux:select>

                                            @php
                                                $sumaOtros = collect($defectos)->reject(fn ($d, $k) => $k === $index)->sum(fn ($d) => (int)($d['cantidad'] ?? 0));
                                                $maximoPermitido = max(1, $totalPuntos - $sumaOtros);
                                            @endphp

                                            <flux:select
                                                wire:model.live="{{ $defectosProperty }}.{{ $index }}.cantidad"
                                                label="Cant."
                                            >
                                                @for ($i = 1; $i <= $maximoPermitido; $i++)
                                                    <flux:select.option value="{{ $i }}">{{ $i }}</flux:select.option>
                                                @endfor
                                            </flux:select>

                                            <flux:button
                                                type="button"
                                                variant="ghost"
                                                icon="x-mark"
                                                wire:click="removeDefecto('{{ $defectosProperty }}', {{ $index }})"
                                            />
                                        </div>
                                    @endforeach

                                    @error($defectosProperty)
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror

                                    <flux:button
                                        type="button"
                                        variant="filled"
                                        icon="plus"
                                        class="w-full justify-center"
                                        wire:click="addDefecto('{{ $defectosProperty }}')"
                                    >
                                        Agregar defecto
                                    </flux:button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <flux:textarea
                wire:model.live.debounce.300ms="observaciones"
                label="Observaciones"
                rows="3"
                class="md:col-span-2 xl:col-span-6"
            />
        </div>

        <div class="mt-6 flex justify-end border-t border-zinc-200 pt-4 dark:border-zinc-700">
            <flux:button
                type="button"
                variant="primary"
                wire:click="guardarRegistro"
                wire:loading.attr="disabled"
                wire:target="guardarRegistro"
            >
                <span wire:loading.remove wire:target="guardarRegistro">Guardar Registro</span>
                <span wire:loading wire:target="guardarRegistro">Guardando...</span>
            </flux:button>
        </div>
    </section>

    <section class="rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 p-4 dark:border-zinc-700">
            <flux:heading size="lg">Registros del Día</flux:heading>
            <flux:subheading>Inspecciones generadas hoy (ordenadas de más antiguas a más recientes)</flux:subheading>
        </div>

        <div class="p-4">
            <livewire:registros-del-dia-table />
        </div>
    </section>
</div>
