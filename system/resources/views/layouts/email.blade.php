<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('subject', Setting::get('school_name', 'Sekolah'))</title>
    <style>
        /* Modern Minimalist CSS Reset */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 20px;
            box-sizing: border-box;
        }
        .container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        .header {
            padding: 35px 40px 25px;
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
        }
        .logo-img {
            height: 48px;
            width: auto;
            margin-bottom: 12px;
            border-radius: 12px;
            object-fit: contain;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.025em;
        }
        .header p {
            margin: 4px 0 0;
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .content {
            padding: 40px 40px 30px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 16px;
        }
        .body-text {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
            margin-top: 0;
            margin-bottom: 24px;
        }
        .button-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .action-button {
            display: inline-block;
            background-color: {{ Setting::get('primary_color', '#6366f1') }};
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 12px color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 25%, transparent);
            transition: all 0.2s ease;
        }
        .highlight-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px;
            margin: 24px 0;
            text-align: center;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
        }
        .info-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }
        .info-table td.label {
            font-weight: 600;
            color: #64748b;
            width: 35%;
        }
        .info-table td.value {
            font-weight: 700;
            color: #334155;
            text-align: right;
        }
        .warning-box {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 12px;
            padding: 16px;
            margin: 24px 0;
            font-size: 12px;
            color: #b45309;
            line-height: 1.5;
            text-align: left;
        }
        .footer {
            background-color: #fafafa;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
        }
        .footer p {
            color: #94a3b8;
            font-size: 11px;
            margin: 4px 0;
            line-height: 1.5;
        }
        .footer-logo {
            height: 24px;
            opacity: 0.5;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header -->
            <div class="header">
                @if(Setting::get('logo_path'))
                    <img src="{{ asset(Setting::get('logo_path')) }}" alt="Logo" class="logo-img">
                @else
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo-img">
                @endif
                <h1>{{ Setting::get('school_name', 'Sekolah') }}</h1>
                <p>Portal Akademik Digital</p>
            </div>
            
            <!-- Content -->
            <div class="content">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ Setting::get('school_name', 'Sekolah') }}. All rights reserved.</p>
                <p>Email ini dikirim secara otomatis oleh sistem, mohon untuk tidak membalas email ini.</p>
            </div>
        </div>
    </div>
</body>
</html>
