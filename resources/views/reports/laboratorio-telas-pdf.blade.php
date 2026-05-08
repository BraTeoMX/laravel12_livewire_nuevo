<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte Laboratorio de Telas</title>
    <style>
        body { font-family: Arial, sans-serif; color: #18181b; margin: 0; }
        .header { border-bottom: 2px solid #0f766e; padding-bottom: 14px; margin-bottom: 18px; }
        .eyebrow { color: #0f766e; font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        h1 { font-size: 26px; margin: 6px 0; }
        .muted { color: #71717a; font-size: 12px; }
        .metrics { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 18px 0; }
        .metric { border: 1px solid #e4e4e7; border-radius: 8px; padding: 12px; }
        .metric span { display: block; color: #71717a; font-size: 11px; }
        .metric strong { display: block; font-size: 20px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th { background: #f4f4f5; text-align: left; padding: 8px; border-bottom: 1px solid #d4d4d8; }
        td { padding: 8px; border-bottom: 1px solid #e4e4e7; vertical-align: top; }
        .badge { border-radius: 999px; padding: 3px 8px; font-size: 10px; font-weight: 700; }
        .ok { background: #d1fae5; color: #047857; }
        .warn { background: #fef3c7; color: #b45309; }
        .bad { background: #ffe4e6; color: #be123c; }
    </style>
</head>
<body>
    <div class="header">
        <div class="eyebrow">Reporte de control textil</div>
        <h1>Laboratorio de Pruebas de Telas</h1>
        <div class="muted">Generado: {{ $generadoEn->format('d/m/Y H:i') }}</div>
    </div>

    <div class="metrics">
        <div class="metric"><span>Lotes evaluados</span><strong>{{ $lotes->count() }}</strong></div>
        <div class="metric"><span>Aprobados</span><strong>{{ $lotes->where('estado', 'Aprobado')->count() }}</strong></div>
        <div class="metric"><span>Observacion</span><strong>{{ $lotes->where('estado', 'Observacion')->count() }}</strong></div>
        <div class="metric"><span>Defectos totales</span><strong>{{ $lotes->sum('defectos') }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Lote</th>
                <th>Tipo de tela</th>
                <th>Composicion</th>
                <th>Tension</th>
                <th>Elongacion</th>
                <th>Defectos</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lotes as $lote)
                <tr>
                    <td>{{ $lote['lote'] }}</td>
                    <td>{{ $lote['tipo_tela'] }}</td>
                    <td>{{ $lote['composicion'] }}</td>
                    <td>{{ $lote['resistencia_tension'] }} N</td>
                    <td>{{ $lote['elongacion'] }}%</td>
                    <td>{{ $lote['defectos'] }}</td>
                    <td>
                        <span @class([
                            'badge',
                            'ok' => $lote['estado'] === 'Aprobado',
                            'warn' => $lote['estado'] === 'Observacion',
                            'bad' => $lote['estado'] === 'Rechazado',
                        ])>{{ $lote['estado'] }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
