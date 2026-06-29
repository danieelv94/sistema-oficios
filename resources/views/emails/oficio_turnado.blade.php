<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Oficio Turnado</title>
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
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 32px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
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
        .intro {
            font-size: 15px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
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
            width: 120px;
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
            padding: 2px 8px;
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
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .instruction-box {
            background-color: #fff;
            border-left: 4px solid #4f46e5;
            padding: 16px;
            border-radius: 0 8px 8px 0;
            margin-top: 16px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .instruction-title {
            font-size: 12px;
            font-weight: 700;
            color: #4f46e5;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .instruction-text {
            font-size: 14px;
            color: #334155;
            line-height: 1.5;
            margin: 0;
        }
        .btn-container {
            text-align: center;
            margin-top: 32px;
            margin-bottom: 16px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2), 0 2px 4px -1px rgba(79, 70, 229, 0.1);
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
            <h1>Sistema de Oficios</h1>
            <p>Notificaciones de Gestión Institucional</p>
        </div>
        <div class="content">
            <h2 class="greeting">Hola, {{ $usuario->name }}</h2>
            <p class="intro">Se ha turnado un nuevo oficio a su área. Por favor, revise los detalles a continuación para asignarlo al personal correspondiente:</p>
            
            <div class="card">
                <span class="card-title">Detalles del Oficio</span>
                
                <div class="info-row">
                    <div class="info-label">Oficio No:</div>
                    <div class="info-value"><strong>{{ $oficio->numero_oficio }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Remitente:</div>
                    <div class="info-value">{{ $oficio->remitente }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Asunto:</div>
                    <div class="info-value">{{ $oficio->asunto }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Prioridad:</div>
                    <div class="info-value">
                        <span class="badge {{ $oficio->prioridad == 'Urgente' ? 'badge-urgente' : 'badge-normal' }}">
                            {{ $oficio->prioridad }}
                        </span>
                    </div>
                </div>
                
                <div class="instruction-box">
                    <div class="instruction-title">Instrucción de Turnado:</div>
                    <p class="instruction-text">{{ $instruccion }}</p>
                </div>
            </div>
            
            <div class="btn-container">
                <a href="{{ route('oficios.show', $oficio->id) }}" class="btn">Ver y Turnar Oficio</a>
            </div>
        </div>
        <div class="footer">
            Este es un correo automático generado por el Sistema de Oficios.<br>
            © {{ date('Y') }} Comisión de Agua y Alcantarillado del Estado de Hidalgo.
        </div>
    </div>
</body>
</html>
