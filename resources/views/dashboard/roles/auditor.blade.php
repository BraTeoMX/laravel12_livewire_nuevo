<x-layouts.app>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <flux:heading size="xl" level="1">Portal de Auditorías y Calidad</flux:heading>
                <flux:subheading>
                    Gestión de formularios de inspección, registro de no conformidades y auditoría de rollos de tela.
                </flux:subheading>
            </div>
        </div>

        <!-- Métricas de Auditoría -->
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs">
                <span class="text-sm font-medium text-zinc-500">Inspecciones de Tela Realizadas</span>
                <div class="mt-2 text-3xl font-bold text-zinc-950 leading-tight">42</div>
            </div>

            <div class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs">
                <span class="text-sm font-medium text-zinc-500">Auditorías Asignadas Pendientes</span>
                <div class="mt-2 text-3xl font-bold text-amber-600 leading-tight">5</div>
            </div>

            <div class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs">
                <span class="text-sm font-medium text-zinc-500">Tasa de Aprobación de Lotes</span>
                <div class="mt-2 text-3xl font-bold text-emerald-600 leading-tight">85.4%</div>
            </div>
        </div>

        <!-- Accesos y Directrices de Inspección -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Sección de Trabajo de Auditoría -->
            <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs space-y-5">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 bg-rose-50 text-rose-700 rounded-lg animate-pulse">
                        <flux:icon.document-text class="size-6" />
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-zinc-900 leading-tight">Módulo de Inspección de Tela</h3>
                        <p class="text-xs text-zinc-500">Formulario interactivo para reportar fallas en rollos de tela.</p>
                    </div>
                </div>

                <p class="text-sm text-zinc-650">
                    Utiliza la herramienta de inspección física para ingresar defectos detectados por metro (agujeros, hilos sueltos, manchas de aceite, etc.) y evaluar la tensión/elongación.
                </p>

                <div>
                    <flux:button variant="primary" icon="document-text" href="{{ route('inspeccion.tela') }}" wire:navigate class="w-full">
                        Iniciar Nueva Inspección
                    </flux:button>
                </div>
            </section>

            <!-- Lineamientos de Calidad e Inspección -->
            <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs space-y-4">
                <div>
                    <flux:heading size="lg">Guía y Lineamientos de Calidad</flux:heading>
                    <flux:subheading>Protocolo estándar para auditorías de tela.</flux:subheading>
                </div>

                <ul class="space-y-3 text-sm text-zinc-600">
                    <li class="flex items-start gap-2.5">
                        <span class="mt-1 h-2 w-2 rounded-full bg-indigo-600 shrink-0"></span>
                        <span><strong>Inspección visual:</strong> Revisar el ancho útil y el peso por metro cuadrado al inicio de cada rollo.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="mt-1 h-2 w-2 rounded-full bg-indigo-600 shrink-0"></span>
                        <span><strong>Mapeo de defectos:</strong> Clasificar defectos en trama y urdimbre, asignando la penalización de puntos correspondiente.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="mt-1 h-2 w-2 rounded-full bg-indigo-600 shrink-0"></span>
                        <span><strong>Registro final:</strong> Registrar de inmediato todos los datos en el sistema para evitar discrepancias.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="mt-1 h-2 w-2 rounded-full bg-indigo-600 shrink-0"></span>
                        <span><strong>Aprobación:</strong> Lotes con más de 20 puntos de penalización promedio deben marcarse como "Rechazado" o "En Observación".</span>
                    </li>
                </ul>
            </section>
        </div>
    </div>
</x-layouts.app>
