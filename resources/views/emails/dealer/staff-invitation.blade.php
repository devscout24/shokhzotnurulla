<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Web Gurus 365!</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; border: 1px solid #eee; }
        .header { text-align: center; padding: 30px 20px; }
        .header img { max-width: 200px; }
        .content { padding: 20px 40px; background: #fff; }
        .footer { padding: 20px 40px; background: #f9f9f9; font-size: 12px; color: #777; border-top: 1px solid #eee; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #3498db; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold; margin-top: 20px; }
        .credentials { background: #f4f7f6; padding: 15px; border-radius: 4px; margin: 20px 0; }
        .credentials p { margin: 5px 0; font-size: 14px; }
        h1 { font-size: 24px; color: #222; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
             <div style="font-size: 28px; font-weight: bold; color: #2c3e50;">
                <span style="color: #3498db;">Web Gurus</span> 365
            </div>
        </div>

        <div class="content">
            <h1>Welcome to Web Gurus 365!</h1>

            <p>You have been invited to the Web Gurus 365 account for <strong>{{ $dealerName }}</strong>. Your login credentials are:</p>

            <div class="credentials">
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Password:</strong> {{ $plainPassword }}</p>
            </div>

            <p>You can log in to the account by visiting <a href="{{ url('/login') }}">{{ url('/login') }}</a></p>

            <p>You will be asked to change your password on first login.</p>

            <p>Welcome aboard!</p>
        </div>

        <div class="footer">
            <p>Web Gurus 365, Inc.<br>
            Professional Web & Inventory Solutions<br>
            support@webgurus365.com</p>
        </div>
    </div>
</body>
</html>
