<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f5f6;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f5f6;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="background:#1a2a34;padding:26px 24px;text-align:center;font-family:Arial,Helvetica,sans-serif;font-size:22px;line-height:1.4;font-weight:700;color:#ffffff;">
                            &#128203; {{ $title }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:22px 18px 8px;background:#f7f8f8;">
                            @foreach ($rows as $row)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="width:100%;margin:0 0 14px;background:#ffffff;border-radius:12px;border-left:5px solid #c6a664;">
                                    <tr>
                                        <td style="padding:16px 18px;font-family:Arial,Helvetica,sans-serif;">
                                            <div style="margin:0 0 8px;color:#1a2a34;font-size:15px;font-weight:700;">{{ $row['icon'] }} {{ $row['label'] }}</div>
                                            <div style="margin:0;color:#243038;font-size:16px;line-height:1.6;">{!! nl2br(e($row['value'])) !!}</div>
                                        </td>
                                    </tr>
                                </table>
                            @endforeach
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
