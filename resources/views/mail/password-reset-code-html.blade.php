@php
    $frontend = rtrim((string) config('app.frontend_url', ''), '/');
    $loginUrl = $frontend !== '' ? $frontend.'/login' : '';
@endphp
<!DOCTYPE html>
<html lang="pt-BR" style="color-scheme:dark;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="dark">
    <meta name="supported-color-schemes" content="dark">
    <title>Redefinição de senha — MyFinance</title>
    <style type="text/css">
        :root { color-scheme: dark; }
        @media (prefers-color-scheme: light) {
            .email-root, .email-root table { background-color: #0c0a10 !important; }
            .email-card {
                background-color: #16131f !important;
                border-color: rgba(170, 59, 255, 0.28) !important;
            }
            .email-card h1, .email-card .email-strong, .email-brand-mid { color: #f0ecf8 !important; }
            .email-card .email-muted { color: #b4aac8 !important; }
            .email-code-wrap {
                background-color: #0f0d14 !important;
                border-color: rgba(170, 59, 255, 0.2) !important;
            }
            .email-code { color: #e8ddff !important; }
        }
    </style>
</head>
<body class="email-root" style="margin:0;padding:0;background-color:#0c0a10;font-family:system-ui,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">
    <table role="presentation" class="email-root" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#0c0a10" style="background-color:#0c0a10;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:480px;">
                    <tr>
                        <td style="padding-bottom:20px;text-align:center;">
                            <span class="email-brand-mid" style="font-size:18px;font-weight:600;color:#f0ecf8;letter-spacing:-0.02em;">My</span><span style="font-size:18px;font-weight:600;color:#c56aff;letter-spacing:-0.02em;">Finance</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-card" bgcolor="#16131f" style="background-color:#16131f;border:1px solid rgba(170,59,255,0.28);border-radius:20px;padding:28px 24px;box-shadow:rgba(0,0,0,0.45) 0 12px 24px -6px,rgba(170,59,255,0.08) 0 0 0 1px;">
                            <p style="margin:0 0 8px;font-size:11px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;color:#c56aff;">Redefinição de senha</p>
                            <h1 style="margin:0 0 16px;font-size:22px;font-weight:500;line-height:1.2;letter-spacing:-0.03em;color:#f0ecf8;">Seu código de verificação</h1>
                            <p class="email-muted" style="margin:0 0 20px;font-size:16px;line-height:1.5;color:#b4aac8;">Use o código abaixo para concluir a alteração da senha. Ele é válido por <strong class="email-strong" style="color:#f0ecf8;font-weight:600;">{{ $ttlMinutes }} minutos</strong>.</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td class="email-code-wrap" align="center" bgcolor="#0f0d14" style="padding:16px 20px;background-color:#0f0d14;border-radius:12px;border:1px solid rgba(170,59,255,0.22);">
                                        <span class="email-code" style="font-family:ui-monospace,Consolas,'Courier New',monospace;font-size:28px;font-weight:600;letter-spacing:0.18em;color:#e8ddff;">{{ $code }}</span>
                                    </td>
                                </tr>
                            </table>
                            <p class="email-muted" style="margin:20px 0 0;font-size:14px;line-height:1.45;color:#b4aac8;">Se você não pediu essa redefinição, pode ignorar este e-mail com segurança.</p>
                            @if($loginUrl !== '')
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:24px;">
                                    <tr>
                                        <td align="center">
                                            <a href="{{ $loginUrl }}" style="display:inline-block;padding:12px 22px;min-height:44px;line-height:20px;font-size:15px;font-weight:500;text-decoration:none;border-radius:10px;background-color:#aa3bff;color:#ffffff;border:1px solid rgba(200,140,255,0.45);">Abrir MyFinance</a>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:20px;text-align:center;">
                            <p class="email-muted" style="margin:0;font-size:13px;line-height:1.45;color:#9a90b5;opacity:0.95;">Enviado por <span class="email-strong" style="color:#f0ecf8;font-weight:500;">MyFinance</span></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
