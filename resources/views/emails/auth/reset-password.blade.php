<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Reset Your Password — {{ config('app.name') }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f5f6fa; font-family:'Segoe UI',Arial,sans-serif;">

    {{-- Wrapper --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f5f6fa; padding:40px 0;">
        <tr>
            <td align="center">

                {{-- Email Card --}}
                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="max-width:600px; width:100%; background:#ffffff; border-radius:16px;
                           box-shadow:0 4px 24px rgba(26,31,46,0.08); overflow:hidden;">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#1a1f2e; padding:32px 40px; text-align:center;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="background:#e8b94f; width:10px; height:10px;
                                                           border-radius:50%;"></td>
                                                <td style="width:10px;"></td>
                                                <td style="color:#ffffff; font-size:20px; font-weight:700;
                                                           letter-spacing:0.5px;">
                                                    {{ config('app.name') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Accent Border --}}
                    <tr>
                        <td style="background:#e8b94f; height:4px; font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:40px 40px 32px;">

                            {{-- Icon --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="margin-bottom:24px;">
                                <tr>
                                    <td align="center">
                                        <div style="width:64px; height:64px; background:#fff8e8;
                                                    border-radius:50%; display:inline-block;
                                                    text-align:center; line-height:64px;
                                                    font-size:28px;">
                                            🔐
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            {{-- Greeting --}}
                            <p style="margin:0 0 8px; font-size:22px; font-weight:700;
                                      color:#1a1f2e; line-height:1.3; text-align:center;">
                                Hello {{ $user->first_name }}!
                            </p>
                            <p style="margin:0 0 28px; font-size:15px; color:#8a94a6;
                                      line-height:1.6; text-align:center;">
                                We received a request to reset your account password.<br>
                                Click the button below to proceed.
                            </p>

                            {{-- Divider --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="margin-bottom:28px;">
                                <tr>
                                    <td style="border-top:1px solid #e2e6f0; font-size:0;">&nbsp;</td>
                                </tr>
                            </table>

                            {{-- Info Box --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background:#f5f6fa; border-radius:10px; margin-bottom:28px;">
                                <tr>
                                    <td style="padding:20px 24px;">

                                        {{-- Email --}}
                                        <table width="100%" cellpadding="0" cellspacing="0"
                                            border="0" style="margin-bottom:14px;">
                                            <tr>
                                                <td style="width:16px; vertical-align:top;">
                                                    <div style="width:8px; height:8px; background:#e8b94f;
                                                                border-radius:50%; margin-top:5px;"></div>
                                                </td>
                                                <td>
                                                    <span style="font-size:12px; color:#8a94a6;
                                                                 text-transform:uppercase; letter-spacing:1px;
                                                                 font-weight:600;">
                                                        Account Email
                                                    </span><br>
                                                    <span style="font-size:14px; color:#1a1f2e; font-weight:600;">
                                                        {{ $user->email }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Expiry --}}
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="width:16px; vertical-align:top;">
                                                    <div style="width:8px; height:8px; background:#e8b94f;
                                                                border-radius:50%; margin-top:5px;"></div>
                                                </td>
                                                <td>
                                                    <span style="font-size:12px; color:#8a94a6;
                                                                 text-transform:uppercase; letter-spacing:1px;
                                                                 font-weight:600;">
                                                        Link Expires In
                                                    </span><br>
                                                    <span style="font-size:14px; color:#1a1f2e; font-weight:600;">
                                                        60 Minutes
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>

                            {{-- CTA Button --}}
                            <table cellpadding="0" cellspacing="0" border="0"
                                width="100%" style="margin-bottom:28px;">
                                <tr>
                                    <td align="center">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="background:#1a1f2e; border-radius:10px;">
                                                    <a href="{{ $resetUrl }}"
                                                       style="display:inline-block; padding:14px 40px;
                                                              font-size:15px; font-weight:700;
                                                              color:#ffffff; text-decoration:none;
                                                              border-radius:10px; letter-spacing:0.3px;">
                                                        Reset Password
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Warning Box --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background:#fdf0ee; border-radius:10px; margin-bottom:8px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <p style="margin:0; font-size:13px; color:#e85f4f; line-height:1.6;">
                                            <strong>If you did not request a password reset,</strong>
                                            no further action is required — your password will remain unchanged.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- Divider --}}
                    <tr>
                        <td style="padding:0 40px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="border-top:1px solid #e2e6f0; font-size:0;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:24px 40px 32px; text-align:center;">
                            <p style="margin:0 0 6px; font-size:13px; font-weight:700;
                                      color:#1a1f2e; letter-spacing:0.5px;">
                                {{ config('app.name') }} Security Team
                            </p>
                            <p style="margin:0; font-size:12px; color:#8a94a6; line-height:1.6;">
                                This is an automated security notification.<br>
                                Please do not reply to this email.
                            </p>
                        </td>
                    </tr>

                </table>
                {{-- End Email Card --}}

                {{-- Bottom Note --}}
                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="max-width:600px; width:100%; margin-top:20px;">
                    <tr>
                        <td style="text-align:center; padding:0 40px;">
                            <p style="margin:0; font-size:11px; color:#b0b8c9; line-height:1.7;">
                                If you're having trouble clicking the button, copy and paste the URL below:<br>
                                <a href="{{ $resetUrl }}"
                                   style="color:#8a94a6; word-break:break-all;">
                                    {{ $resetUrl }}
                                </a>
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>
