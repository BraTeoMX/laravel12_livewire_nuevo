<?php

namespace App\Http\Livewire;

use App\Models\InspeccionTelaTemporal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Throwable;

class InspeccionTela extends Component
{
    public string $terminoBusqueda = '';

    public array $resultadosBusqueda = [];

    public ?string $mensajeBusqueda = null;

    public ?string $tipoMensajeBusqueda = null;

    public function buscarInformacion(): void
    {
        $termino = $this->validarTerminoBusqueda();

        $this->reset('resultadosBusqueda', 'mensajeBusqueda', 'tipoMensajeBusqueda');

        $registrosLocales = $this->consultarMySqlPorNumeroDiario($termino);

        if ($registrosLocales->isNotEmpty()) {
            $this->resultadosBusqueda = $this->mapearTemporalesParaVista($registrosLocales);
            $this->tipoMensajeBusqueda = 'success';
            $this->mensajeBusqueda = "Información encontrada en MySQL para la recepción {$termino}.";

            return;
        }

        $this->buscarEnSqlServerYSincronizar($termino, 'No había registros en MySQL. Se consultó SQL Server y se sincronizó la información.');
    }

    public function forzarBusquedaDirecta(): void
    {
        $termino = $this->validarTerminoBusqueda();

        $this->reset('resultadosBusqueda', 'mensajeBusqueda', 'tipoMensajeBusqueda');

        $this->buscarEnSqlServerYSincronizar($termino, 'Consulta directa a SQL Server completada.');
    }

    public function limpiarBusqueda(): void
    {
        $this->reset('terminoBusqueda', 'resultadosBusqueda', 'mensajeBusqueda', 'tipoMensajeBusqueda');
        $this->resetValidation();
    }

    protected function validarTerminoBusqueda(): string
    {
        $this->validate([
            'terminoBusqueda' => ['required', 'string', 'min:3', 'max:80'],
        ], [
            'terminoBusqueda.required' => 'Ingresa una orden de compra o número de recepción.',
            'terminoBusqueda.min' => 'Ingresa al menos 3 caracteres para buscar.',
        ]);

        return trim($this->terminoBusqueda);
    }

    protected function buscarEnSqlServerYSincronizar(string $termino, string $mensajeBase): void
    {
        try {
            $registros = $this->consultarSqlServer($termino);
        } catch (Throwable $exception) {
            Log::error('No fue posible consultar SQL Server para inspección de tela.', [
                'termino' => $termino,
                'exception' => $exception,
            ]);

            $this->tipoMensajeBusqueda = 'error';
            $this->mensajeBusqueda = 'No fue posible conectar con SQL Server. Intenta nuevamente o contacta a sistemas.';

            return;
        }

        if ($registros->isEmpty()) {
            $this->tipoMensajeBusqueda = 'warning';
            $this->mensajeBusqueda = 'No se encontraron registros con ese criterio.';

            return;
        }

        $sincronizacion = $this->sincronizarRegistrosTemporales($registros, $termino);

        $this->resultadosBusqueda = $registros
            ->map(fn (object $registro): array => $this->normalizarRegistro($registro, $termino))
            ->values()
            ->all();

        $this->tipoMensajeBusqueda = 'success';
        $this->mensajeBusqueda = sprintf(
            '%s Nuevos: %d. Actualizados: %d. Sin cambios: %d.',
            $mensajeBase,
            $sincronizacion['creados'],
            $sincronizacion['actualizados'],
            $sincronizacion['sin_cambios'],
        );
    }

    protected function consultarSqlServer(string $termino): Collection
    {
        $esBusquedaPorRecepcion = strtoupper(substr($termino, 0, 3)) === 'REC';
        $columnaFiltro = $esBusquedaPorRecepcion ? 'WJT.JOURNALID' : 'WJT.INVENTTRANSREFID';

        $sql = <<<SQL
            SELECT DISTINCT
                ISNULL(NULLIF(WJT.JOURNALID, ''), 'N/A') AS numero_diario,
                ISNULL(NULLIF(WJT.INVENTTRANSREFID, ''), 'N/A') AS orden_compra,
                ISNULL(NULLIF(WJT.DESCRIPTION, ''), 'N/A') AS proveedor,
                ISNULL(NULLIF(WJT_TRANS.ITEMID, ''), 'N/A') AS estilo,
                ISNULL(NULLIF(PL.NAME, ''), 'N/A') AS nombre_producto,
                ISNULL(NULLIF(PL.EXTERNALDESCRIPTION_AT, ''), 'N/A') AS nombre_producto_externo,
                ISNULL(NULLIF(PL.EXTERNALITEMID, ''), 'N/A') AS estilo_externo,
                ISNULL(NULLIF(IDIM.INVENTSIZEID, ''), 'N/A') AS talla,
                ISNULL(NULLIF(IDIM.INVENTCOLORID, ''), 'N/A') AS color,
                ISNULL(NULLIF(IDIM.INVENTBATCHID, ''), 'N/A') AS lote_intimark
            FROM
                [AX_SERVER_LIVE].[INTIMARKDBAXPRODLIVE].[dbo].[WMSJOURNALTABLE] AS WJT
            INNER JOIN
                [AX_SERVER_LIVE].[INTIMARKDBAXPRODLIVE].[dbo].[WMSJOURNALTRANS] AS WJT_TRANS
                ON WJT.JOURNALID = WJT_TRANS.JOURNALID
            INNER JOIN
                [AX_SERVER_LIVE].[INTIMARKDBAXPRODLIVE].[dbo].[PURCHLINE] AS PL
                ON WJT_TRANS.INVENTTRANSID = PL.INVENTTRANSID
            INNER JOIN
                [AX_SERVER_LIVE].[INTIMARKDBAXPRODLIVE].[dbo].[INVENTDIM] AS IDIM
                ON WJT_TRANS.INVENTDIMID = IDIM.INVENTDIMID
            WHERE
                {$columnaFiltro} = ?
        SQL;

        return collect(DB::connection('sqlsrv_dev')->select($sql, [$termino]));
    }

    protected function consultarMySqlPorNumeroDiario(string $numeroDiario): Collection
    {
        return InspeccionTelaTemporal::query()
            ->where('numero_diario', $numeroDiario)
            ->orderBy('lote_intimark')
            ->orderBy('estilo')
            ->get();
    }

    protected function sincronizarRegistrosTemporales(Collection $registros, string $termino): array
    {
        $resultado = [
            'creados' => 0,
            'actualizados' => 0,
            'sin_cambios' => 0,
        ];

        foreach ($registros as $registro) {
            $data = $this->normalizarRegistro($registro, $termino);
            $temporal = InspeccionTelaTemporal::where('source_key', $data['source_key'])->first();

            if (! $temporal) {
                InspeccionTelaTemporal::create($data);
                $resultado['creados']++;

                continue;
            }

            if ($this->registroTemporalTieneCambios($temporal, $data)) {
                $temporal->fill($data)->save();
                $resultado['actualizados']++;

                continue;
            }

            $resultado['sin_cambios']++;
        }

        return $resultado;
    }

    protected function registroTemporalTieneCambios(InspeccionTelaTemporal $temporal, array $data): bool
    {
        foreach ($data as $campo => $valor) {
            if (in_array($campo, ['source_key', 'termino_busqueda'], true)) {
                continue;
            }

            if ((string) ($temporal->{$campo} ?? '') !== (string) ($valor ?? '')) {
                return true;
            }
        }

        return false;
    }

    protected function mapearTemporalesParaVista(Collection $registros): array
    {
        return $registros
            ->map(fn (InspeccionTelaTemporal $registro): array => [
                'source_key' => $registro->source_key,
                'numero_diario' => $registro->numero_diario,
                'orden_compra' => $registro->orden_compra,
                'proveedor' => $registro->proveedor,
                'estilo' => $registro->estilo,
                'nombre_producto' => $registro->nombre_producto,
                'nombre_producto_externo' => $registro->nombre_producto_externo,
                'estilo_externo' => $registro->estilo_externo,
                'talla' => $registro->talla,
                'color' => $registro->color,
                'articulo' => $registro->articulo,
                'ancho_contratado' => $registro->ancho_contratado,
                'lote_intimark' => $registro->lote_intimark,
                'termino_busqueda' => $registro->termino_busqueda,
            ])
            ->values()
            ->all();
    }

    protected function normalizarRegistro(object $registro, string $termino): array
    {
        $estilo = $this->valor($registro, 'estilo');
        $color = $this->valor($registro, 'color');

        return [
            'source_key' => $this->crearSourceKey($registro),
            'numero_diario' => $this->valor($registro, 'numero_diario'),
            'orden_compra' => $this->valor($registro, 'orden_compra'),
            'proveedor' => $this->valor($registro, 'proveedor'),
            'estilo' => $estilo,
            'nombre_producto' => $this->valor($registro, 'nombre_producto'),
            'nombre_producto_externo' => $this->valor($registro, 'nombre_producto_externo'),
            'estilo_externo' => $this->valor($registro, 'estilo_externo'),
            'talla' => $this->valor($registro, 'talla'),
            'color' => $color,
            'articulo' => $this->unificarArticulo($estilo, $color),
            'ancho_contratado' => $this->extraerAncho($this->valor($registro, 'nombre_producto_externo')),
            'lote_intimark' => $this->valor($registro, 'lote_intimark'),
            'termino_busqueda' => $termino,
        ];
    }

    protected function crearSourceKey(object $registro): string
    {
        return hash('sha256', implode('|', [
            $this->valor($registro, 'numero_diario') ?? '',
            $this->valor($registro, 'orden_compra') ?? '',
            $this->valor($registro, 'lote_intimark') ?? '',
            $this->valor($registro, 'estilo') ?? '',
            $this->valor($registro, 'color') ?? '',
            $this->valor($registro, 'talla') ?? '',
        ]));
    }

    protected function valor(object $registro, string $campo): ?string
    {
        $valor = $registro->{$campo} ?? null;

        if ($valor === null) {
            return null;
        }

        $valor = trim((string) $valor);

        return $valor === '' ? null : $valor;
    }

    protected function extraerAncho(?string $nombreProductoExterno): ?int
    {
        if ($nombreProductoExterno && preg_match('/(\d+)"/', $nombreProductoExterno, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    protected function unificarArticulo(?string $estilo, ?string $color): ?string
    {
        if ($estilo && $color) {
            return "{$estilo}.{$color}";
        }

        return $estilo ?: $color;
    }

    public function render()
    {
        return view('livewire.inspeccion-tela');
    }
}
