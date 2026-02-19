<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #111;">Test email</h1>

    <p>Hi {{ $user->name }},</p>

    <p>This is a test email sent from Laravel Nova.</p>

    <p>If you received this, your email delivery is working.</p>

    <p>
        Thanks,<br>
        {{ config('app.name') }}
    </p>
</body>
</html>
