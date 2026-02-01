<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo mensaje - Mentorial</title>
</head>
<body style="margin:0; padding:0; background-color:#0f0f14; font-family: Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0f0f14; padding:20px;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#141421; border-radius:10px; overflow:hidden; box-shadow:0 6px 20px rgba(0,0,0,0.6);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(90deg,#7c3aed,#9333ea); padding:22px; text-align:center; color:#ffffff;">
                            <h1 style="margin:0; font-size:22px; letter-spacing:0.5px;">
                                📩 Nuevo mensaje de contacto
                            </h1>
                            <p style="margin:6px 0 0; font-size:13px; opacity:0.9;">
                                Mentorial Simulacros
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:26px; color:#e5e7eb; font-size:14px; line-height:1.7;">
                            <p style="margin-top:0;">
                                Hola,
                            </p>

                            <p>
                                Una persona se ha puesto en contacto a través de la plataforma
                                <strong style="color:#c084fc;">Mentorial</strong>.
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:18px;">
                                <tr>
                                    <td style="padding:8px 0; font-weight:bold; width:130px; color:#c084fc;">
                                        Nombre:
                                    </td>
                                    <td style="padding:8px 0;">
                                        {{ $nombre }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; font-weight:bold; color:#c084fc;">
                                        Correo:
                                    </td>
                                    <td style="padding:8px 0;">
                                        <a href="mailto:{{ $correo }}" style="color:#a78bfa; text-decoration:none;">
                                            {{ $correo }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <hr style="border:none; border-top:1px solid #2e2e44; margin:22px 0;">

                            <p style="font-weight:bold; color:#c084fc; margin-bottom:8px;">
                                Mensaje:
                            </p>

                            <p style="background-color:#1c1c2e; padding:16px; border-left:4px solid #9333ea; margin:0; color:#f3f4f6; white-space: pre-line;">
                                {{ $mensaje }}
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#10101a; padding:16px; text-align:center; font-size:12px; color:#9ca3af;">
                            Este mensaje fue enviado desde el formulario de contacto de
                            <strong style="color:#c084fc;">Mentorial Simulacros</strong>.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
