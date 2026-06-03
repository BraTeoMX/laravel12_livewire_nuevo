@php
    $lotes = \App\Support\LaboratorioTelasData::lotes();
    $totalLotes = $lotes->count();
    $promedioResistencia = round($lotes->avg('resistencia_tension'), 1);
    $totalDefectos = $lotes->sum('defectos');
    $defectosPorTela = \App\Support\LaboratorioTelasData::defectosPorTela();
@endphp

<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <flux:heading size="xl" level="1">Portal de Consulta y Reportes</flux:heading>
                <flux:subheading>
                    Monitoreo de calidad de telas, consulta de historial y descarga de reportes ejecutivos.
                </flux:subheading>
            </div>
        </div>

        <!-- Estadísticas de Consulta -->
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs">
                <span class="text-sm font-medium text-zinc-500">Lotes Evaluados</span>
                <div class="mt-2 text-3xl font-bold text-zinc-950 leading-tight">{{ $totalLotes }}</div>
            </div>
            
            <div class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs">
                <span class="text-sm font-medium text-zinc-500">Resistencia Promedio</span>
                <div class="mt-2 text-3xl font-bold text-zinc-950 leading-tight">{{ $promedioResistencia }} N</div>
            </div>

            <div class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs">
                <span class="text-sm font-medium text-zinc-500">Defectos Acumulados</span>
                <div class="mt-2 text-3xl font-bold text-rose-600 leading-tight">{{ $totalDefectos }}</div>
            </div>
        </div>

        <!-- Reporte Gráfico e Historial -->
        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <!-- Tabla de Consulta Rápida -->
            <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs space-y-4">
                <div>
                    <flux:heading size="lg">Historial de Calidad de Telas</flux:heading>
                    <flux:subheading>Vista general de lotes ensayados en el laboratorio.</flux:subheading>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-left text-sm">
                        <thead class="bg-zinc-50 font-semibold text-zinc-700">
                            <tr>
                                <th class="p-3">Lote</th>
                                <th class="p-3">Tipo de Tela</th>
                                <th class="p-3">Resistencia</th>
                                <th class="p-3">Defectos</th>
                                <th class="p-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 text-zinc-655">
                            @foreach($lotes as $lote)
                                <tr class="hover:bg-zinc-50 transition-colors">
                                    <td class="p-3 font-mono font-semibold text-zinc-900">{{ $lote['lote'] }}</td>
                                    <td class="p-3">{{ $lote['tipo_tela'] }}</td>
                                    <td class="p-3 font-semibold">{{ $lote['resistencia_tension'] }} N</td>
                                    <td class="p-3 text-center">{{ $lote['defectos'] }}</td>
                                    <td class="p-3">
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset
                                            @if($lote['estado'] === 'Aprobado') bg-emerald-50 text-emerald-700 ring-emerald-600/20
                                            @elseif($lote['estado'] === 'Observacion') bg-amber-50 text-amber-700 ring-amber-600/20
                                            @else bg-rose-50 text-rose-700 ring-rose-600/20 @endif">
                                            {{ $lote['estado'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Gráfico de Defectos por Tela -->
            <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs space-y-4">
                <div>
                    <flux:heading size="lg">Gráfico de Defectos</flux:heading>
                    <flux:subheading>Incidencias acumuladas por tipo de material.</flux:subheading>
                </div>

                <div
                    wire:ignore
                    x-data="consultaDefectosChart({
                        labels: @js(array_keys($defectosPorTela)),
                        series: @js(array_values($defectosPorTela))
                    })"
                    class="min-h-[300px]"
                >
                    <div x-ref="chart" class="h-[300px]"></div>
                </div>
            </section>
        </div>
    </div>

    <!-- Cargar ApexCharts si no está cargado y ejecutar script del chart de Consulta -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('consultaDefectosChart', (config) => ({
                chart: null,
                labels: config.labels,
                series: config.series,
                init() {
                    this.$nextTick(() => {
                        this.renderChart();
                    });
                },
                renderChart() {
                    if (this.chart) this.chart.destroy();
                    
                    this.chart = new ApexCharts(this.$refs.chart, {
                        chart: {
                            type: 'donut',
                            height: 300,
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui'
                        },
                        series: this.series,
                        labels: this.labels,
                        colors: ['#0f766e', '#0284c7', '#f59e0b', '#ef4444', '#6366f1'],
                        legend: {
                            position: 'bottom'
                        },
                        dataLabels: {
                            enabled: true
                        }
                    });
                    this.chart.render();
                },
                destroy() {
                    if (this.chart) this.chart.destroy();
                }
            }));
        });
    </script>
</x-layouts.app>
