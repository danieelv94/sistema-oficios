<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento Firmado por Dirección General - Listo para Recoger</title>
    <style>
        body {
            font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #691c32 0%, #4a1222 100%);
            padding: 32px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 32px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 16px;
            color: #0f172a;
        }
        .alert-ready {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-left: 4px solid #2563eb;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            color: #1e3a8a;
            font-size: 14px;
            line-height: 1.5;
        }
        .card {
            background-color: #f1f5f9;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 28px;
            border: 1px solid #e2e8f0;
        }
        .card-title {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 16px;
            display: block;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .info-label {
            display: table-cell;
            width: 145px;
            font-weight: 600;
            color: #475569;
            font-size: 14px;
            padding-right: 12px;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            color: #0f172a;
            font-size: 14px;
            vertical-align: top;
        }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 9999px;
            text-transform: uppercase;
        }
        .badge-urgente {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-normal {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .note-box {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 14px;
            border-radius: 0 8px 8px 0;
            margin-top: 16px;
        }
        .note-title {
            font-size: 12px;
            font-weight: 700;
            color: #b45309;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .note-text {
            font-size: 13px;
            color: #78350f;
            margin: 0;
        }
        .btn-container {
            text-align: center;
            margin-top: 28px;
            margin-bottom: 12px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #691c32 0%, #4a1222 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
        }
        .footer {
            padding: 24px 32px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Dirección General · CEAA</h1>
            <p>Notificación de Documento Firmado Listo para Recoger</p>
        </div>
        <div class="content">
            <h2 class="greeting">Hola, {{ trim(($usuario->prof ? $usuario->prof . ' ' : '') . $usuario->name) }}</h2>

            <div class="alert-ready">
                <strong>✍️ ¡Documento firmado!</strong><br>
                Te informamos que el siguiente documento entregado en <strong>Dirección General</strong> ya fue firmado por el Director General y <strong>ya puedes pasar a recogerlo</strong>.
            </div>

            <div class="card">
                <span class="card-title">Datos del Documento Firmado</span>

                <div class="info-row">
                    <div class="info-label">Tipo de Documento:</div>
                    <div class="info-value"><strong>{{ $registro->tipo_documento }}</strong></div>
                </div>

                <div class="info-row">
                    <div class="info-label">No. de Oficio / Folio:</div>
                    <div class="info-value"><strong>{{ $registro->numero_oficio ?: 'S/N (Folio #' . $registro->id . ')' }}</strong></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Área / Dirección:</div>
                    <div class="info-value">{{ $registro->area ? $registro->area->name : 'N/A' }}</div>
                </div>

                @if($registro->entregado_por_nombre)
                    <div class="info-row">
                        <div class="info-label">Dejado en DG por:</div>
                        <div class="info-value">{{ $registro->entregado_por_nombre }}</div>
                    </div>
                @endif

                <div class="info-row">
                    <div class="info-label">Asunto:</div>
                    <div class="info-value">{{ $registro->asunto }}</div>
                </div>

                @if($registro->destinatario)
                    <div class="info-row">
                        <div class="info-label">Dirigido a:</div>
                        <div class="info-value">{{ $registro->destinatario }}</div>
                    </div>
                @endif

                <div class="info-row">
                    <div class="info-label">Fecha de Firma:</div>
                    <div class="info-value">{{ $registro->fecha_firma ? $registro->fecha_firma->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }} hrs</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Prioridad:</div>
                    <div class="info-value">
                        <span class="badge {{ $registro->prioridad === 'Urgente' ? 'badge-urgente' : 'badge-normal' }}">
                            {{ $registro->prioridad }}
                        </span>
                    </div>
                </div>

                @if($registro->observaciones)
                    <div class="note-box">
                        <div class="note-title">Observaciones de Dirección General:</div>
                        <p class="note-text">{{ $registro->observaciones }}</p>
                    </div>
                @endif
            </div>

            <div class="btn-container">
                <a href="{{ route('firma-dg.mis-oficios') }}" class="btn">Consultar Estatus en el Sistema</a>
            </div>
        </div>
        <div class="footer">
            Este es un correo automático generado por el Sistema de Gestión de Oficios (Dirección General).<br>
            © {{ date('Y') }} Comisión Estatal del Agua y Alcantarillado (CEAA).
        </div>
    </div>
</body>
</html>

