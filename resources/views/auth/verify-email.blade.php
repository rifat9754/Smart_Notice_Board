<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verify Email</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; margin: 0;
            background: linear-gradient(160deg, #0f172a, #1e3a8a);
            color: #fff;
        }
        .card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px;
            padding: 40px;
            max-width: 480px; text-align: center;
        }
        .icon { font-size: 56px; margin-bottom: 16px; }
        h1 { margin: 0 0 12px; font-size: 24px; }
        p { color: rgba(255,255,255,0.75); line-height: 1.6; margin-bottom: 24px; }
        button {
            background: #2563eb; color: #fff; border: none;
            padding: 12px 24px; border-radius: 12px;
            font-size: 15px; font-weight: 600; cursor: pointer;
        }
        .success {
            background: rgba(34,197,94,0.15); color: #4ade80;
            padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 14px;
        }
        a { color: #93c5fd; font-size: 14px; display: block; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">📧</div>
        <h1>Verify Your Email</h1>

        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <p>
            We've sent a verification link to your email address.
            Please click that link to verify your account.<br><br>
            After verification, an administrator will approve your account before you can log in.
        </p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit">Resend Verification Email</button>
        </form>

        <a href="{{ route('login') }}">Back to login</a>
    </div>
</body>
</html>