<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Obituaries.co.ke' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 15px;
        }
        .main {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #0f172a;
            padding: 32px 30px;
            text-align: center;
            border-bottom: 4px solid #d97706;
        }
        .logo {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 26px;
            font-weight: bold;
            color: #ffffff;
            text-decoration: none;
            letter-spacing: -0.5px;
        }
        .logo-gold {
            color: #f59e0b;
        }
        .tagline {
            color: #94a3b8;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 6px;
            font-weight: 600;
        }
        .content {
            padding: 36px 32px;
            line-height: 1.7;
            font-size: 15px;
            color: #334155;
        }
        .content p {
            margin: 0 0 16px 0;
        }
        .button-wrapper {
            text-align: center;
            margin: 32px 0 16px 0;
        }
        .btn {
            display: inline-block;
            background-color: #d97706;
            color: #ffffff !important;
            font-weight: bold;
            font-size: 14px;
            padding: 14px 28px;
            border-radius: 10px;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(217, 119, 6, 0.3);
        }
        .footer {
            background-color: #f1f5f9;
            padding: 24px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .footer a {
            color: #d97706;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main">
            <div class="header">
                <a href="{{ config('app.url') }}" style="display: inline-block;">
                    <img src="{{ asset('images/logo-light.png') }}" alt="Obituaries.co.ke Logo" style="height: 48px; width: auto; border: 0; display: block; margin: 0 auto;">
                </a>
                <div class="tagline">Kenya's Dignified Memorial Platform</div>
            </div>

            <div class="content">
                {!! nl2br(e($bodyContent)) !!}

                @if(!empty($actionUrl))
                    <div class="button-wrapper">
                        <a href="{{ $actionUrl }}" class="btn" target="_blank">{{ $actionText ?? 'View Details' }}</a>
                    </div>
                @endif
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} Obituaries.co.ke. All rights reserved.</p>
                <p>Nairobi, Kenya &bull; <a href="https://obituaries.co.ke">obituaries.co.ke</a></p>
            </div>
        </div>
    </div>
</body>
</html>
