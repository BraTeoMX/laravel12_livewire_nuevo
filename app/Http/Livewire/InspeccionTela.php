<?php

namespace App\Http\Livewire;

use App\Models\CatalogoMaquina;
use App\Models\CatalogoDefecto;
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

    public array $maquinasOptions = [];

    public array $catalogoDefectosOptions = [];

    public array $loteIntimarkOptions = [];

    public string $maquina = '';

    public string $lote_intimark = '';

    public string $proveedor = '';

    public string $articulo = '';

    public string $color_nombre = '';

    public string $material = '';

    public string $orden_compra = '';

    public string $numero_recepcion = '';

    public string $ancho_contratado_input = '';

    public int $ancho_contratado = 0;

    public int $ancho_contratado_cm = 0;

    public string $ancho_cortable = '';

    public string $numero_piezas = '';

    public string $numero_lote = '';

    public string $yarda_ticket = '';

    public string $yarda_actual = '';

    public string $observaciones = '';

    public int $puntos_1 = 0;

    public int $puntos_2 = 0;

    public int $puntos_3 = 0;

    public int $puntos_4 = 0;

    public array $defectos_puntos_1 = [];

    public array $defectos_puntos_2 = [];

    public array $defectos_puntos_3 = [];

    public array $defectos_puntos_4 = [];

    public function mount(): void
    {
        $this->maquinasOptions = CatalogoMaquina::query()
            ->orderBy('nombre')
            ->pluck('nombre')
            ->values()
            ->all();

        $this->catalogoDefectosOptions = CatalogoDefecto::query()
            ->where('estatus', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre'])
            ->map(fn (CatalogoDefecto $defecto): array => [
                'id' => $defecto->id,
                'nombre' => $defecto->nombre,
            ])
            ->values()
            ->all();

        $this->maquina = $this->maquinasOptions[0] ?? '';
    }

    public function buscarInformacion(): void
    {
        $termino = $this->validarTerminoBusqueda();

        $this->reset('resultadosBusqueda', 'mensajeBusqueda', 'tipoMensajeBusqueda');
        $this->resetEncabezadoTela();

        $registrosLocales = $this->consultarMySqlPorNumeroDiario($termino);

        if ($registrosLocales->isNotEmpty()) {
            $this->resultadosBusqueda = $this->mapearTemporalesParaVista($registrosLocales);
            $this->prepararEncabezadoDesdeResultados();
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
        $this->resetEncabezadoTela();

        $this->buscarEnSqlServerYSincronizar($termino, 'Consulta directa a SQL Server completada.');
    }

    public function limpiarBusqueda(): void
    {
        $this->reset('terminoBusqueda', 'resultadosBusqueda', 'mensajeBusqueda', 'tipoMensajeBusqueda');
        $this->resetEncabezadoTela();
        $this->resetValidation();
    }

    public function updatedLoteIntimark(): void
    {
        $this->llenarEncabezadoDesdeLote();
    }

    public function updatedAnchoContratadoInput(): void
    {
        $valor = trim($this->ancho_contratado_input);

        if ($valor === '' || ! is_numeric($valor)) {
            $this->ancho_contratado = 0;
            $this->ancho_contratado_cm = 0;

            return;
        }

        $this->ancho_contratado = min(1000, max(0, (int) $valor));
        $this->ancho_contratado_input = (string) $this->ancho_contratado;
        $this->ancho_contratado_cm = $this->convertirPulgadasACentimetros($this->ancho_contratado);
    }

    public function updatedPuntos1(): void
    {
        $this->sincronizarDefectosPorPuntos('puntos_1', 'defectos_puntos_1');
    }

    public function updatedPuntos2(): void
    {
        $this->sincronizarDefectosPorPuntos('puntos_2', 'defectos_puntos_2');
    }

    public function updatedPuntos3(): void
    {
        $this->sincronizarDefectosPorPuntos('puntos_3', 'defectos_puntos_3');
    }

    public function updatedPuntos4(): void
    {
        $this->sincronizarDefectosPorPuntos('puntos_4', 'defectos_puntos_4');
    }

    public function addDefecto(string $defectosProperty): void
    {
        if (! in_array($defectosProperty, $this->defectosPermitidos(), true)) {
            return;
        }

        $this->{$defectosProperty}[] = [
            'defecto_id' => '',
            'cantidad' => 1,
        ];
    }

    public function removeDefecto(string $defectosProperty, int $index): void
    {
        if (! in_array($defectosProperty, $this->defectosPermitidos(), true)) {
            return;
        }

        $defectos = $this->{$defectosProperty};
        unset($defectos[$index]);

        $this->{$defectosProperty} = array_values($defectos);
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

        $this->prepararEncabezadoDesdeResultados();

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

    protected function prepararEncabezadoDesdeResultados(): void
    {
        $this->loteIntimarkOptions = collect($this->resultadosBusqueda)
            ->pluck('lote_intimark')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $this->lote_intimark = $this->loteIntimarkOptions[0] ?? '';
        $this->llenarEncabezadoDesdeLote();
    }

    protected function llenarEncabezadoDesdeLote(): void
    {
        if ($this->lote_intimark === '') {
            $this->resetCamposTela();

            return;
        }

        $registro = collect($this->resultadosBusqueda)
            ->first(fn (array $resultado): bool => (string) ($resultado['lote_intimark'] ?? '') === $this->lote_intimark);

        if (! $registro) {
            $this->resetCamposTela();

            return;
        }

        $this->proveedor = (string) ($registro['proveedor'] ?? '');
        $this->articulo = (string) ($registro['articulo'] ?? '');
        $this->color_nombre = (string) ($registro['nombre_producto'] ?? '');
        $this->material = (string) ($registro['estilo_externo'] ?? '');
        $this->orden_compra = (string) ($registro['orden_compra'] ?? '');
        $this->numero_recepcion = (string) ($registro['numero_diario'] ?? '');
        $this->ancho_contratado = (int) ($registro['ancho_contratado'] ?? 0);
        $this->ancho_contratado_input = $this->ancho_contratado > 0 ? (string) $this->ancho_contratado : '';
        $this->ancho_contratado_cm = $this->convertirPulgadasACentimetros($this->ancho_contratado);
    }

    protected function resetEncabezadoTela(): void
    {
        $this->loteIntimarkOptions = [];
        $this->lote_intimark = '';
        $this->resetCamposTela();
    }

    protected function resetCamposTela(): void
    {
        $this->proveedor = '';
        $this->articulo = '';
        $this->color_nombre = '';
        $this->material = '';
        $this->orden_compra = '';
        $this->numero_recepcion = '';
        $this->ancho_contratado_input = '';
        $this->ancho_contratado = 0;
        $this->ancho_contratado_cm = 0;
    }

    protected function sincronizarDefectosPorPuntos(string $puntosProperty, string $defectosProperty): void
    {
        $this->{$puntosProperty} = min(20, max(0, (int) $this->{$puntosProperty}));

        if ($this->{$puntosProperty} === 0) {
            $this->{$defectosProperty} = [];

            return;
        }

        if (empty($this->{$defectosProperty})) {
            $this->{$defectosProperty} = [
                [
                    'defecto_id' => '',
                    'cantidad' => 1,
                ],
            ];
        }
    }

    protected function defectosPermitidos(): array
    {
        return [
            'defectos_puntos_1',
            'defectos_puntos_2',
            'defectos_puntos_3',
            'defectos_puntos_4',
        ];
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

    protected function convertirPulgadasACentimetros(int $pulgadas): int
    {
        return (int) round($pulgadas * 2.54);
    }

    public function guardarRegistro(): void
    {
        $this->validate([
            'maquina' => 'required|string',
            'lote_intimark' => 'required|string',
            'ancho_contratado_input' => 'required|numeric|gt:0',
            'ancho_cortable' => 'required|numeric|min:0.01',
            'numero_piezas' => 'required|integer|min:1',
            'numero_lote' => 'required|string|max:100',
            'yarda_ticket' => 'required|numeric|min:0.01',
            'yarda_actual' => 'required|numeric|min:0.01',
        ], [
            'maquina.required' => 'La máquina es obligatoria.',
            'lote_intimark.required' => 'El lote intimark es obligatorio.',
            'ancho_contratado_input.required' => 'El ancho contratado es obligatorio.',
            'ancho_contratado_input.gt' => 'El ancho contratado debe ser mayor a 0.',
            'ancho_cortable.required' => 'El ancho cortable es obligatorio.',
            'numero_piezas.required' => 'El número de piezas es obligatorio.',
            'numero_lote.required' => 'El lote teñido es obligatorio.',
            'yarda_ticket.required' => 'La yarda ticket es obligatoria.',
            'yarda_actual.required' => 'La yarda actual es obligatoria.',
        ]);

        $customErrors = [];

        foreach ([
            ['puntos_1', 'defectos_puntos_1', '1 Punto'],
            ['puntos_2', 'defectos_puntos_2', '2 Puntos'],
            ['puntos_3', 'defectos_puntos_3', '3 Puntos'],
            ['puntos_4', 'defectos_puntos_4', '4 Puntos'],
        ] as [$puntosProp, $defectosProp, $label]) {
            $totalPuntos = (int) $this->{$puntosProp};
            if ($totalPuntos > 0) {
                $defectos = $this->{$defectosProp};
                
                if (empty($defectos)) {
                    $customErrors[$defectosProp] = "Debe agregar al menos un defecto para {$label}.";
                    continue;
                }

                $suma = 0;
                $defectosIds = [];
                foreach ($defectos as $index => $defecto) {
                    if (empty($defecto['defecto_id'])) {
                        $customErrors["{$defectosProp}.{$index}.defecto_id"] = "El defecto es obligatorio.";
                    } else {
                        if (in_array($defecto['defecto_id'], $defectosIds)) {
                            $customErrors["{$defectosProp}.{$index}.defecto_id"] = "Este defecto ya fue seleccionado.";
                        }
                        $defectosIds[] = $defecto['defecto_id'];
                    }
                    $suma += (int) ($defecto['cantidad'] ?? 0);
                }

                if ($suma !== $totalPuntos) {
                    $customErrors[$defectosProp] = "La suma de cantidades en {$label} debe ser exactamente {$totalPuntos}. Suma actual: {$suma}.";
                }
            }
        }

        if (!empty($customErrors)) {
            throw \Illuminate\Validation\ValidationException::withMessages($customErrors);
        }

        try {
            DB::transaction(function () {
                $inspeccion = \App\Models\Inspeccion::create([
                    'user_id' => auth()->id() ?? 1, // Ajusta según tu lógica de autenticación
                    'maquina' => $this->maquina,
                    'lote_intimark' => $this->lote_intimark,
                    'articulo' => $this->articulo,
                    'proveedor' => $this->proveedor,
                    'color_nombre' => $this->color_nombre,
                    'ancho_contratado_input' => $this->ancho_contratado_input === '' ? null : $this->ancho_contratado_input,
                    'ancho_contratado_cm' => $this->ancho_contratado_cm,
                    'material' => $this->material,
                    'orden_compra' => $this->orden_compra,
                    'numero_recepcion' => $this->numero_recepcion,
                    'ancho_cortable' => $this->ancho_cortable,
                    'numero_piezas' => $this->numero_piezas,
                    'numero_lote' => $this->numero_lote,
                    'yarda_ticket' => $this->yarda_ticket,
                    'yarda_actual' => $this->yarda_actual,
                    'observaciones' => $this->observaciones,
                ]);

                $defectosAInsertar = [];
                $totalPuntosDefectosCalculados = 0;

                foreach ([
                    ['puntos_1', 'defectos_puntos_1', 1],
                    ['puntos_2', 'defectos_puntos_2', 2],
                    ['puntos_3', 'defectos_puntos_3', 3],
                    ['puntos_4', 'defectos_puntos_4', 4],
                ] as [$puntosProp, $defectosProp, $puntosValue]) {
                    if ((int) $this->{$puntosProp} > 0) {
                        foreach ($this->{$defectosProp} as $defecto) {
                            $puntosCalculados = ((int) $defecto['cantidad']) * $puntosValue;
                            $totalPuntosDefectosCalculados += $puntosCalculados;

                            $defectosAInsertar[] = [
                                'defecto_id' => $defecto['defecto_id'],
                                'puntos' => $puntosValue,
                                'cantidad' => $defecto['cantidad'],
                                'puntos_calculados' => $puntosCalculados,
                            ];
                        }
                    }
                }

                if (!empty($defectosAInsertar)) {
                    $inspeccion->defectos()->createMany($defectosAInsertar);
                }

                $inspeccion->update(['total_puntos_defectos' => $totalPuntosDefectosCalculados]);
            });

            $this->dispatch('notify', type: 'success', message: 'Registro de inspección guardado exitosamente.');
            $this->dispatch('pg:eventRefresh-registros_del_dia_table');
            
            // Opcionalmente puedes limpiar el formulario aquí
            // $this->limpiarBusqueda();
            
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error guardando inspección de tela', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('notify', type: 'error', message: 'Ocurrió un error al guardar el registro. Revisa los logs para más detalles.');
        }
    }

    public function render()
    {
        return view('livewire.inspeccion-tela');
    }
}
