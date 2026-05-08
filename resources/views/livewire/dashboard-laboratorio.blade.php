<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <flux:heading size="xl" level="1">Laboratorio de Pruebas de Telas</flux:heading>
            <flux:subheading>PoC operativa con Flux, Livewire, PowerGrid, Excel, Browsershot y ApexCharts.</flux:subheading>
        </div>

        <div class="flex flex-wrap gap-2">
            <flux:button variant="primary" icon="beaker" wire:click="simularPrueba" wire:loading.attr="disabled" wire:target="simularPrueba">
                <span wire:loading.remove wire:target="simularPrueba">Simular Prueba</span>
                <span wire:loading wire:target="simularPrueba">Procesando...</span>
            </flux:button>

            <flux:button variant="filled" icon="arrow-path" wire:click="sincronizarEquipos" wire:loading.attr="disabled" wire:target="sincronizarEquipos">
                <span wire:loading.remove wire:target="sincronizarEquipos">Sincronizar Equipos</span>
                <span wire:loading wire:target="sincronizarEquipos">Sincronizando...</span>
            </flux:button>

            <flux:button icon="document-arrow-down" wire:click="exportarExcel" wire:loading.attr="disabled" wire:target="exportarExcel">
                Excel
            </flux:button>

            <flux:button icon="document-text" wire:click="exportarPdf" wire:loading.attr="disabled" wire:target="exportarPdf">
                PDF
            </flux:button>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="text-sm text-zinc-500 dark:text-zinc-400">Lotes</div>
            <div class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-50">{{ $totalLotes }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="text-sm text-zinc-500 dark:text-zinc-400">Aprobados</div>
            <div class="mt-2 text-2xl font-semibold text-emerald-600">{{ $aprobados }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="text-sm text-zinc-500 dark:text-zinc-400">En observacion</div>
            <div class="mt-2 text-2xl font-semibold text-amber-600">{{ $observacion }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="text-sm text-zinc-500 dark:text-zinc-400">Rechazados</div>
            <div class="mt-2 text-2xl font-semibold text-rose-600">{{ $rechazados }}</div>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="text-sm text-zinc-500 dark:text-zinc-400">Tension promedio</div>
            <div class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-50">{{ $resistenciaPromedio }} N</div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(360px,0.85fr)]">
        <section class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <flux:heading size="lg">Lotes de tela</flux:heading>
                    <flux:subheading>Busqueda, ordenamiento, paginacion y columnas dinamicas sobre Collection estatica.</flux:subheading>
                </div>
            </div>

            <livewire:lotes-tela-table />
        </section>

        <section class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-4">
                <flux:heading size="lg">Indice de defectos</flux:heading>
                <flux:subheading>Defectos acumulados por tipo de tela.</flux:subheading>
            </div>

            <div
                wire:ignore
                x-data="laboratorioDefectosChart({
                    labels: @js($chartLabels),
                    series: @js($chartSeries)
                })"
                x-init="render()"
                class="min-h-[360px]"
            >
                <div x-ref="chart" class="h-[360px]"></div>
            </div>
        </section>
    </div>

    <flux:modal wire:model="mostrarModal" class="md:w-[32rem]">
        <div class="space-y-5">
            <div>
                <flux:heading size="lg">Operacion completada</flux:heading>
                <flux:subheading>{{ $mensajeModal }}</flux:subheading>
            </div>

            <div class="flex justify-end">
                <flux:button variant="primary" wire:click="$set('mostrarModal', false)">Entendido</flux:button>
            </div>
        </div>
    </flux:modal>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('laboratorioDefectosChart', (config) => ({
                chart: null,
                render() {
                    const options = {
                        chart: {
                            type: 'bar',
                            height: 360,
                            toolbar: { show: false },
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui',
                        },
                        series: [{
                            name: 'Defectos',
                            data: config.series,
                        }],
                        xaxis: {
                            categories: config.labels,
                            labels: { rotate: -35, trim: true },
                        },
                        colors: ['#0f766e'],
                        plotOptions: {
                            bar: {
                                borderRadius: 5,
                                columnWidth: '52%',
                            },
                        },
                        dataLabels: { enabled: false },
                        grid: { borderColor: '#e5e7eb' },
                        tooltip: {
                            y: {
                                formatter: (value) => `${value} defectos`,
                            },
                        },
                    };

                    this.chart = new window.ApexCharts(this.$refs.chart, options);
                    this.chart.render();
                },
            }));
        });
    </script>
</div>
