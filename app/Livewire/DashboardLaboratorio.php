<?php

namespace App\Livewire;

use App\Exports\LotesTelaExport;
use App\Support\LaboratorioTelasData;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class DashboardLaboratorio extends Component
{
    public bool $mostrarModal = false;

    public string $mensajeModal = '';

    public function simularPrueba(): void
    {
        usleep(450000);

        $this->mensajeModal = 'La prueba simulada se completo: resistencia dentro de tolerancia y lote listo para revision.';
        $this->mostrarModal = true;
    }

    public function sincronizarEquipos(): void
    {
        usleep(650000);

        $this->mensajeModal = 'Equipos sincronizados: tensiometro, camara de defectos y balanza reportan lectura estable.';
        $this->mostrarModal = true;
    }

    public function exportarExcel(): BinaryFileResponse
    {
        return Excel::download(new LotesTelaExport(), 'lotes-pruebas-tela.xlsx');
    }

    public function exportarPdf(): BinaryFileResponse
    {
        $directory = storage_path('app/laboratorio-reportes');
        File::ensureDirectoryExists($directory);

        $path = $directory.'/reporte-laboratorio-telas-'.now()->format('Ymd-His').'.pdf';

        Browsershot::html(view('reports.laboratorio-telas-pdf', [
            'lotes' => LaboratorioTelasData::lotes(),
            'defectosPorTela' => LaboratorioTelasData::defectosPorTela(),
            'generadoEn' => now(),
        ])->render())
            ->setNodeBinary(config('browsershot.node_binary'))
            ->setNpmBinary(config('browsershot.npm_binary'))
            ->setChromePath(config('browsershot.chrome_path'))
            ->setNodeModulePath(config('browsershot.node_modules_path'))
            ->format('A4')
            ->margins(12, 12, 12, 12)
            ->showBackground()
            ->save($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function render()
    {
        $lotes = LaboratorioTelasData::lotes();

        return view('livewire.dashboard-laboratorio', [
            'totalLotes' => $lotes->count(),
            'aprobados' => $lotes->where('estado', 'Aprobado')->count(),
            'observacion' => $lotes->where('estado', 'Observacion')->count(),
            'rechazados' => $lotes->where('estado', 'Rechazado')->count(),
            'resistenciaPromedio' => round($lotes->avg('resistencia_tension'), 1),
            'chartLabels' => array_keys(LaboratorioTelasData::defectosPorTela()),
            'chartSeries' => array_values(LaboratorioTelasData::defectosPorTela()),
        ]);
    }
}
