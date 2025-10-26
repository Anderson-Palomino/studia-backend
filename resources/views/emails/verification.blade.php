<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifica tu cuenta - Studia</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center; color: white; }
        .content { padding: 30px; background: #f9f9f9; }
        .button { display: inline-block; padding: 14px 35px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; background: #fff; }
        .code { background: #eee; padding: 15px; border-radius: 5px; margin: 20px 0; word-break: break-all; font-family: monospace; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1 style="margin: 0; font-size: 28px;">🎓 Studia</h1>
        <p style="margin: 10px 0 0 0; font-size: 18px; opacity: 0.9;">Verifica tu cuenta</p>
    </div>

    <div class="content">
        <h2 style="color: #333; margin-top: 0;">¡Hola {{ $user->name }}! 👋</h2>

        <p>Gracias por registrarte en <strong>Studia</strong>. Para comenzar a usar tu cuenta y acceder a todas las funciones, necesitas verificar tu dirección de email.</p>

        <div style="text-align: center; margin: 35px 0;">
            <a href="{{ $verificationUrl }}" class="button">
                ✅ Verificar mi email
            </a>
        </div>

        <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
        <div class="code">
            {{ $verificationUrl }}
        </div>

        <p><strong>¿Por qué debo verificar mi email?</strong></p>
        <ul>
            <li>Acceder a todos los cursos y materiales</li>
            <li>Recibir notificaciones importantes</li>
            <li>Proteger tu cuenta y datos personales</li>
        </ul>

        <p style="color: #666; font-size: 14px;">
            Si no te registraste en Studia, puedes ignorar este email de manera segura.
        </p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Studia. Todos los derechos reservados.</p>
        <p>Este es un email automático, por favor no respondas a este mensaje.</p>
    </div>
</div>
</body>
</html>
