<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Notificación de Solicitud</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 20px;">
    
    <table width="100%" cellpadding="0" cellspacing="0" style="max-w-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <!-- CABECERA -->
        <tr>
            <td style="background-color: #18181b; padding: 30px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px;">LCB<span style="color: #4EAA68;">Portal</span></h1>
                <p style="color: #a1a1aa; font-size: 10px; text-transform: uppercase; letter-spacing: 2px; margin-top: 5px;">Sistema de Gestión Operativa</p>
            </td>
        </tr>

        <!-- CONTENIDO -->
        <tr>
            <td style="padding: 40px 30px;">
                
                @if($tipoDestinatario === 'admin')
                    <p style="color: #52525b; font-size: 16px; margin-top: 0; font-weight: bold;">Notificación para Administración,</p>
                    <p style="color: #52525b; font-size: 15px; line-height: 1.5;">Se ha registrado una nueva solicitud de <strong>{{ $datosCorreo['servicio'] }}</strong> en el portal que requiere revisión.</p>
                @else
                    <p style="color: #52525b; font-size: 16px; margin-top: 0; font-weight: bold;">Hola {{ $datosCorreo['solicitante_nombre'] }},</p>
                    <p style="color: #52525b; font-size: 15px; line-height: 1.5;">Hemos recibido correctamente tu solicitud de <strong>{{ $datosCorreo['servicio'] }}</strong>. Ha sido enviada a Coordinación/Gerencia y se encuentra en estado <span style="color:#eab308; font-weight:bold;">PENDIENTE</span>.</p>
                @endif
                
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; margin: 25px 0; border-radius: 8px;">
                    <p style="margin: 0 0 15px 0; font-size: 13px; color: #64748b; text-transform: uppercase; font-weight: bold; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Resumen del Evento</p>
                    
                    <p style="margin: 0 0 8px 0; color: #334155; font-size: 15px;"><strong>Asunto:</strong> {{ $datosCorreo['titulo'] }}</p>
                    <p style="margin: 0 0 8px 0; color: #334155; font-size: 15px;"><strong>Solicita:</strong> {{ $datosCorreo['solicitante_correo'] }}</p>
                    <p style="margin: 0 0 8px 0; color: #334155; font-size: 15px;"><strong>Fecha del Servicio:</strong> {{ $datosCorreo['fecha'] }}</p>
                    <p style="margin: 0; color: #334155; font-size: 15px;"><strong>Detalle Principal:</strong> {{ $datosCorreo['detalles'] }}</p>
                </div>

                @if($tipoDestinatario === 'admin')
                    <a href="{{ url('/') }}" style="display: inline-block; background-color: #4EAA68; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; margin-top: 10px;">Ingresar al Panel para Aprobar/Rechazar</a>
                @endif
            </td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 30px;">
    <a href="{{ url('/admin/restaurante') }}" style="background-color: #4EAA68; color: white; padding: 12px 25px; text-decoration: none; font-weight: bold; border-radius: 8px; display: inline-block;">
        Revisar en la Plataforma
    </a>
</div>
</body>
</html>