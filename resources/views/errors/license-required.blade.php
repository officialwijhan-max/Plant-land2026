<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>License Required</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 40px;
            max-width: 480px;
            width: 92%;
            text-align: center;
        }
        h1 { font-size: 20px; font-weight: 600; margin-bottom: 12px; }
        p { color: #475569; font-size: 14px; line-height: 1.7; }
        hr { border: none; border-top: 1px solid #e2e8f0; margin: 24px 0; }
        .sub { font-size: 12px; color: #94a3b8; margin-bottom: 10px; }
        .row { display: flex; gap: 8px; justify-content: center; }
        input[name="_license_temp_code"] {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 15px;
            letter-spacing: 3px;
            text-align: center;
            width: 160px;
            text-transform: uppercase;
        }
        button {
            background: #3b82f6;
            color: #fff;
            border: none;
            padding: 9px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover { background: #2563eb; }
        .footer { margin-top: 24px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="card">
        <h1>License Required</h1>
        <p>
            {{ $message ?? 'This software requires an active license. Please contact AZ For Trade & Marketing to resolve this.' }}
        </p>

        <hr>
        <p class="sub">Have a temporary access code?</p>
        <form method="POST" action="">
            @csrf
            <div class="row">
                <input type="text" name="_license_temp_code" placeholder="CODE" maxlength="20" autocomplete="off" autofocus>
                <button type="submit">Enter</button>
            </div>
        </form>

        <p class="footer">{{ config('app.name') }}</p>
    </div>
</body>
</html>
