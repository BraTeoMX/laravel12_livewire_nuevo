<div class="space-y-6">
    <div class="rounded-lg border border-zinc-200 bg-white p-4">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <flux:heading size="lg">Inspección de Tela</flux:heading>
                <flux:subheading>Búsqueda y registro de inspecciones de tela</flux:subheading>
            </div>
        </div>

        <!-- Sección superior: Búsqueda -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">Buscar por lote, tipo de tela o inspector</label>
                <flux:input type="text" wire:model.debounce.500ms="buscar" placeholder="Ingrese término de búsqueda" />
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">Fecha de ingreso</label>
                <flux:input type="date" wire:model="fechaSeleccionada" />
            </div>
        </div>
    </div>

    <!-- Sección intermedia: Resultados de búsqueda -->
    <div class="rounded-lg border border-zinc-200 bg-white p-4">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <flux:heading size="lg">Resultados de la búsqueda</flux:heading>
                <flux:subheading>Lista de telas que coinciden con los criterios</flux:subheading>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200">
                <thead class="bg-zinc-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Lote</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Tipo de Tela</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Ancho</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Longitud</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Fecha Ingreso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Inspector</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Resultado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Observaciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    <!-- Datos simulados para resultados -->
                    <tr class="hover:bg-zinc-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">LOT-001</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">Algodón Peinado</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">1.50 m</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">50 m</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">2026-05-18</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">Juan Pérez</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                Aprobado
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">Sin observaciones</td>
                    </tr>
                    <tr class="hover:bg-zinc-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">LOT-002</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">Poliéster Deportivo</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">1.40 m</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">40 m</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">2026-05-18</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">María García</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">
                                Observado
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">Leve variación de color</td>
                    </tr>
                    <tr class="hover:bg-zinc-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">LOT-003</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">Mezclilla Ligera</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">1.60 m</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">60 m</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">2026-05-19</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">Carlos López</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-rose-100 text-rose-800">
                                Rechazado
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">Defectos en el tejido</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sección inferior: Registros del día -->
    <div class="rounded-lg border border-zinc-200 bg-white p-4">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <flux:heading size="lg">Registros del día</flux:heading>
                <flux:subheading>Inspecciones realizadas hoy</flux:subheading>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200">
                <thead class="bg-zinc-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Lote</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Tipo de Tela</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Resultado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Hora</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    <!-- Datos simulados para registros del día -->
                    <tr class="hover:bg-zinc-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">1</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">LOT-001</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">Algodón Peinado</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                Aprobado
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">08:30</td>
                    </tr>
                    <tr class="hover:bg-zinc-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">2</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">LOT-002</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">Poliéster Deportivo</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">
                                Observado
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">09:15</td>
                    </tr>
                    <tr class="hover:bg-zinc-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">3</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">LOT-003</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">Mezclilla Ligera</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-rose-100 text-rose-800">
                                Rechazado
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">10:00</td>
                    </tr>
                    <tr class="hover:bg-zinc-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">4</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">LOT-004</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">Rayón Estampado</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                Aprobado
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900">11:45</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>