<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Actualización de Solicitud</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 20px;">
    
    <table width="100%" cellpadding="0" cellspacing="0" style="max-w-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <!-- CABECERA -->
        <tr>
            <td style="background-color: #18181b; padding: 30px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px;">LCB<span style="color: #4EAA68;">Portal</span></h1>
                <p style="color: #a1a1aa; font-size: 10px; text-transform: uppercase; letter-spacing: 2px; margin-top: 5px;">Portal de Gestión y Reservas</p>
            </td>
        </tr>

        <!-- CONTENIDO -->
        <tr>
            <td style="padding: 40px 30px;">
                <p style="color: #52525b; font-size: 16px; margin-top: 0;">Hola,</p>
                <p style="color: #52525b; font-size: 16px; line-height: 1.5;">Su solicitud para el servicio de <strong>{{ $datosCorreo['servicio'] }}</strong> ha sido actualizada por la Administración.</p>
                
                <div style="background-color: #f8fafc; border-left: 4px solid #4EAA68; padding: 15px 20px; margin: 25px 0; border-radius: 0 8px 8px 0;">
                    <p style="margin: 0 0 10px 0; font-size: 14px; color: #64748b; text-transform: uppercase; font-weight: bold;">Detalles del Evento</p>
                    <p style="margin: 0 0 5px 0; color: #334155; font-size: 16px;"><strong>Asunto:</strong> {{ $datosCorreo['titulo'] }}</p>
                    
                    @php
                        $colorEstado = $datosCorreo['estado'] == 'Aprobado' || $datosCorreo['estado'] == 'Agendado' ? '#4EAA68' : ($datosCorreo['estado'] == 'Rechazado' ? '#ef4444' : '#eab308');
                    @endphp
                    
                    <p style="margin: 0; color: #334155; font-size: 16px;">
                        <strong>Estado Actual:</strong> 
                        <span style="color: {{ $colorEstado }}; font-weight: bold; text-transform: uppercase;">{{ $datosCorreo['estado'] }}</span>
                    </p>
                </div>

                @if(!empty($datosCorreo['notas']))
                <div style="margin-top: 25px;">
                    <p style="font-size: 14px; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">Notas de Coordinación / Gerencia:</p>
                    <p style="background-color: #fffbeb; border: 1px solid #fde68a; color: #92400e; padding: 15px; border-radius: 8px; font-size: 15px; margin: 0; font-style: italic;">
                        "{{ $datosCorreo['notas'] }}"
                    </p>
                </div>
                @endif

                <p style="color: #52525b; font-size: 14px; margin-top: 30px;">Puede verificar más detalles ingresando al módulo de 'Mis Solicitudes' en su portal.</p>
            </td>
        </tr>

        <!-- FOOTER -->
        <tr>
            <td style="background-color: #f4f4f5; padding: 20px; text-align: center; border-top: 1px solid #e4e4e7;">
                <p style="color: #a1a1aa; font-size: 12px; margin: 0;">Este es un mensaje automático del Liceo de Colombia Bilingüe. Por favor, no responda a este correo.</p>
            </td>
        </tr>
    </table>

</body>
</html>