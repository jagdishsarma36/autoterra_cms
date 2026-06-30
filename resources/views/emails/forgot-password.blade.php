<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f8fc; margin: 0; padding: 40px 0; }
    .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
    .header { background: #0B1522; padding: 32px 40px; }
    .header h1 { color: #fff; font-size: 20px; margin: 0; }
    .header h1 span { color: #00A8F8; }
    .body { padding: 36px 40px; }
    .body h2 { font-size: 18px; color: #1C2B3A; margin: 0 0 8px; }
    .body p { font-size: 14px; color: #5A7A96; line-height: 1.7; margin: 0 0 16px; }
    .password-box { background: #F0F7FF; border: 2px dashed #00A8F8; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
    .password-box .password { font-size: 22px; font-weight: 800; color: #0B1522; letter-spacing: 2px; font-family: 'Courier New', monospace; }
    .password-box .label { font-size: 12px; color: #5A7A96; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
    .btn { display: inline-block; background: #00A8F8; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 7px; font-size: 14px; font-weight: 700; margin: 16px 0; }
    .footer { padding: 24px 40px; background: #F5F8FC; border-top: 1px solid #D2DCE6; }
    .footer p { font-size: 12px; color: #5A7A96; margin: 0; line-height: 1.6; }
    .footer a { color: #00A8F8; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1><span>AUTO</span>TERRA</h1>
    </div>
    <div class="body">
      <h2>Password Reset</h2>
      <p>Hi <strong>{{ $user->name }}</strong>,</p>
      <p>Your AutoTerra account password has been reset as requested. Use the password below to sign in.</p>

      <div class="password-box">
        <div class="label">Your new password</div>
        <div class="password">{{ $newPassword }}</div>
      </div>

      <p style="font-size:13px;">For security reasons, please change your password after signing in from your dashboard profile settings.</p>

      <a href="{{ route('login') }}" class="btn">Sign In Now</a>
    </div>
    <div class="footer">
      <p>AutoTerra by Infyterra Technologies · <a href="{{ config('app.url') }}">autoterra.net</a></p>
      <p style="margin-top:8px;">Questions? Contact <a href="mailto:support@autoterra.net">support@autoterra.net</a></p>
    </div>
  </div>
</body>
</html>
