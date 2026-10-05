@if (isset($solicitudes))
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de solicitudes</title>
    <style>
        @page { margin: 28px; }
        body { color: #243247; font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        header { background: #17365d; color: #fff; margin-bottom: 20px; padding: 18px 20px; }
        h1 { font-size: 20px; margin: 0 0 5px; }
        header p { color: #dce7f5; margin: 0; }
        table { border-collapse: collapse; width: 100%; }
        th { background: #17365d; color: #fff; text-align: left; }
        th, td { border: 1px solid #d9e2f3; padding: 7px; }
        tr:nth-child(even) { background: #f2f6fa; }
        .footer { color: #617086; font-size: 9px; margin-top: 16px; text-align: right; }
    </style>
</head>
<body>
    <header>
        <h1>Reporte de solicitudes</h1>
        <p>{{ $solicitudes->count() }} registros · Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </header>

    <table>
        <thead>
            <tr>
                <th>Folio</th>
                <th>Solicitante</th>
                <th>Recurso</th>
                <th>Fecha de inicio</th>
                <th>Fecha de devolución</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($solicitudes as $solicitud)
                <tr>
                    <td>{{ $solicitud->getKey() }}</td>
                    <td>{{ $solicitud->solicitante?->name ?? 'No disponible' }}</td>
                    <td>
                        @if ($solicitud->aula)
                            Aula {{ $solicitud->aula->numero }}{{ $solicitud->aula->edificio?->nombre ? '-'.$solicitud->aula->edificio->nombre : '' }}
                        @elseif ($solicitud->inventario)
                            {{ $solicitud->inventario->nombre }} ({{ $solicitud->cantidad }})
                        @else
                            No disponible
                        @endif
                    </td>
                    <td>{{ $solicitud->fecha_inicio?->format('d/m/Y H:i') ?? 'No especificada' }}</td>
                    <td>{{ $solicitud->fecha_fin?->format('d/m/Y H:i') ?? 'No especificada' }}</td>
                    <td>{{ ucfirst($solicitud->estado ?? 'No disponible') }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No hay solicitudes registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
@else
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de solicitud {{ $solicitud->getKey() }}</title>
    <style>
        @page { margin: 36px; }
        body { color: #243247; font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { background: #17365d; color: #fff; padding: 22px 24px; }
        .header h1 { font-size: 21px; margin: 0 0 6px; }
        .header p { color: #dce7f5; margin: 0; }
        .section-title { border-bottom: 2px solid #d9e2f3; color: #17365d; font-size: 14px; margin: 24px 0 10px; padding-bottom: 6px; }
        table { border-collapse: collapse; width: 100%; }
        td { border-bottom: 1px solid #e5eaf1; padding: 9px 8px; vertical-align: top; }
        td.label { color: #617086; font-weight: bold; width: 34%; }
        .footer { color: #617086; font-size: 10px; margin-top: 30px; text-align: center; }
    </style>
</head>
<body>
    <header class="header">
        <h1>Comprobante de solicitud</h1>
        <p>Folio #{{ $solicitud->getKey() }}</p>
    </header>

    <h2 class="section-title">Personas</h2>
    <table>
        <tbody>
            <tr><td class="label">Solicitante</td><td>{{ $solicitud->solicitante?->name ?? 'No disponible' }}</td></tr>
            <tr><td class="label">Identificación</td><td>{{ $solicitud->identificacion ?: 'No proporcionada' }}</td></tr>
            <tr><td class="label">Préstamo registrado por</td><td>{{ $solicitud->prestador?->name ?? 'No disponible' }}</td></tr>
        </tbody>
    </table>

    <h2 class="section-title">Detalle del préstamo</h2>
    <table>
        <tbody>
            <tr><td class="label">Tipo de solicitud</td><td>{{ ucfirst($solicitud->tipo_solicitud) }}</td></tr>
            <tr>
                <td class="label">Recurso</td>
                <td>
                    @if ($solicitud->aula)
                        Aula {{ $solicitud->aula->numero }}{{ $solicitud->aula->edificio?->nombre ? '-'.$solicitud->aula->edificio->nombre : '' }}
                    @elseif ($solicitud->inventario)
                        {{ $solicitud->inventario->nombre }}
                    @else
                        No disponible
                    @endif
                </td>
            </tr>
            <tr><td class="label">Cantidad</td><td>{{ $solicitud->cantidad ?? 'No aplica' }}</td></tr>
            <tr><td class="label">Fecha de inicio</td><td>{{ $solicitud->fecha_inicio?->format('d/m/Y H:i') ?? 'No especificada' }}</td></tr>
            <tr><td class="label">Fecha de devolución</td><td>{{ $solicitud->fecha_fin?->format('d/m/Y H:i') ?? 'No especificada' }}</td></tr>
            <tr><td class="label">Estado</td><td>{{ ucfirst($solicitud->estado ?? 'No disponible') }}</td></tr>
            <tr><td class="label">Descripción</td><td>{{ $solicitud->descripcion ?: 'Sin descripción' }}</td></tr>
        </tbody>
    </table>

    <p class="footer">Documento generado el {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
@endif
