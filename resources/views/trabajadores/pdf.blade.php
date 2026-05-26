<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listado de Trabajadores</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #222; }
        .header { display:flex; align-items:center; border-bottom:2px solid #333; padding-bottom:10px; margin-bottom:15px; }
        .logo { width:120px; margin-right:15px }
        .company { font-size:18px; font-weight:700 }
        .meta { font-size:12px; color:#555 }
        table { width:100%; border-collapse: collapse; margin-top:10px }
        th, td { border:1px solid #ddd; padding:8px; font-size:12px }
        th { background:#f4f4f4; text-align:left }
        .footer { margin-top:20px; font-size:11px; color:#666 }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('img/Logo.png') }}" alt="Logo" style="max-width:100%; height:auto">
        </div>
        <div>
            <div class="company">Empresa Mina Porco S.A.</div>
            <div class="meta">Listado de Trabajadores — Generado: {{ now()->format('Y-m-d H:i') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>CI</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Cargo</th>
                <th>Área</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Activo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $r)
                <tr>
                    <td>{{ $r->ci }}</td>
                    <td>{{ $r->nombre }}</td>
                    <td>{{ trim(($r->ap_paterno ?? '') . ' ' . ($r->ap_materno ?? '')) }}</td>
                    <td>{{ $r->cargo->nombre ?? '' }}</td>
                    <td>{{ $r->area->nombre ?? '' }}</td>
                    <td>{{ $r->email }}</td>
                    <td>{{ $r->celular }}</td>
                    <td>{{ $r->activo ? 'Sí' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Documento generado por el sistema Mina Porco — Página 1</div>
</body>
</html>
