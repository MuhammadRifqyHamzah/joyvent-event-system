<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode OTP JoyVent</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 500px;
            background-color: #ffffff;
            border-radius: 24px;
            padding: 40px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            text-align: center;
        }
        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #4f63ff;
            margin-bottom: 24px;
        }
        h1 {
            font-size: 20px;
            color: #1f2937;
            margin-bottom: 8px;
        }
        p {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 32px;
        }
        .otp-box {
            font-size: 32px;
            font-weight: 900;
            color: #4f63ff;
            background-color: #f5f7ff;
            border: 2px dashed #4f63ff;
            padding: 16px 24px;
            border-radius: 16px;
            letter-spacing: 6px;
            display: inline-block;
            margin-bottom: 32px;
        }
        .footer {
            font-size: 11px;
            color: #9ca3af;
            border-top: 1px solid #f1f3f7;
            padding-top: 24px;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">JoyVent</div>
        <h1>Verifikasi Lupa Password</h1>
        <p>Gunakan kode OTP di bawah ini untuk memverifikasi permintaan lupa password Anda. Kode ini hanya berlaku selama 10 menit.</p>
        <div class="otp-box">{{ $otp }}</div>
        <p>Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini.</p>
        <div class="footer">
            &copy; {{ date('Y') }} JoyVent Team. All rights reserved.
        </div>
    </div>
</body>
</html>
