<!DOCTYPE html>
<html>
<head>
    <title>Setup Your Dealer Account</title>
</head>
<body>
    <h2>Welcome!</h2>
    <p>Your dealer account has been created. Please click the button below to set up your password and verify your account.</p>
    
    <p>
        <a href="{{ route('dealer.setup', ['token' => $token, 'email' => $email]) }}" 
           style="display: inline-block; padding: 10px 20px; background-color: #c0392b; color: white; text-decoration: none; border-radius: 5px;">
            Setup Account
        </a>
    </p>
    
    <p>If you did not request this, you can ignore this email.</p>
    
    <p>Thanks,<br>{{ config('app.name') }}</p>
</body>
</html>
