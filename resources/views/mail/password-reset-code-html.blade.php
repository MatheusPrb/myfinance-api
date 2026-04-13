@php
    $frontend = rtrim((string) config('app.frontend_url', ''), '/');
    $loginUrl = $frontend !== '' ? $frontend.'/login' : '';
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Redefinição de senha — MyFinance</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f0f7;font-family:system-ui,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f3f0f7;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:480px;">
                    <tr>
                        <td style="padding-bottom:20px;text-align:center;">
                            <span style="font-size:18px;font-weight:600;color:#08060d;letter-spacing:-0.02em;">My</span><span style="font-size:18px;font-weight:600;color:#aa3bff;letter-spacing:-0.02em;">Finance</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#ffffff;border:1px solid rgba(229,228,231,0.95);border-radius:20px;padding:28px 24px;box-shadow:rgba(0,0,0,0.1) 0 10px 15px -3px,rgba(0,0,0,0.05) 0 4px 6px -2px;">
                            <p style="margin:0 0 8px;font-size:11px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;color:#aa3bff;">Redefinição de senha</p>
                            <h1 style="margin:0 0 16px;font-size:22px;font-weight:500;line-height:1.2;letter-spacing:-0.03em;color:#08060d;">Seu código de verificação</h1>
                            <p style="margin:0 0 20px;font-size:16px;line-height:1.5;color:#6b6375;">Use o código abaixo para concluir a alteração da senha. Ele é válido por <strong style="color:#08060d;font-weight:600;">{{ $ttlMinutes }} minutos</strong>.</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" style="padding:16px 20px;background-color:#f4f3ec;border-radius:12px;border:1px solid #e5e4e7;">
                                        <span style="font-family:ui-monospace,Consolas,'Courier New',monospace;font-size:28px;font-weight:600;letter-spacing:0.18em;color:#08060d;">{{ $code }}</span>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:20px 0 0;font-size:14px;line-height:1.45;color:#6b6375;">Se você não pediu essa redefinição, pode ignorar este e-mail com segurança.</p>
                            @if($loginUrl !== '')
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:24px;">
                                    <tr>
                                        <td align="center">
                                            <a href="{{ $loginUrl }}" style="display:inline-block;padding:12px 22px;min-height:44px;line-height:20px;font-size:15px;font-weight:500;text-decoration:none;border-radius:10px;background-color:#aa3bff;color:#ffffff;border:1px solid rgba(170,59,255,0.5);">Abrir MyFinance</a>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:20px;text-align:center;">
                            <p style="margin:0;font-size:13px;line-height:1.45;color:#6b6375;opacity:0.9;">Enviado por <span style="color:#08060d;font-weight:500;">MyFinance</span></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
