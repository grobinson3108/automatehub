<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <style>
        /* Reset CSS */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        /* Email styles */
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            min-width: 100%;
            height: 100%;
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #374151;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        .email-header {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            padding: 40px 30px;
            text-align: center;
        }

        .email-logo {
            display: inline-block;
            width: 50px;
            height: 50px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            margin-bottom: 20px;
            line-height: 50px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: white;
        }

        .email-title {
            color: white;
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }

        .email-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
            margin: 10px 0 0 0;
        }

        .email-content {
            padding: 40px 30px;
        }

        .email-greeting {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 20px 0;
        }

        .email-text {
            margin: 0 0 20px 0;
            line-height: 1.6;
        }

        .email-button {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }

        .email-button:hover {
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        }

        .email-stats {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .stats-grid {
            display: flex;
            justify-content: space-between;
            text-align: center;
        }

        .stat-item {
            flex: 1;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            display: block;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
            margin-top: 5px;
        }

        .email-footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer-text {
            font-size: 14px;
            color: #6b7280;
            margin: 0 0 15px 0;
        }

        .footer-links {
            margin: 15px 0;
        }

        .footer-links a {
            color: #3b82f6;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
        }

        .social-links {
            margin: 20px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 5px;
            padding: 8px;
            background-color: #e5e7eb;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            text-align: center;
            line-height: 20px;
            text-decoration: none;
            color: #374151;
        }

        .unsubscribe {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 20px;
        }

        .unsubscribe a {
            color: #9ca3af;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            
            .email-header,
            .email-content,
            .email-footer {
                padding: 20px !important;
            }

            .email-title {
                font-size: 24px !important;
            }

            .stats-grid {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="email-logo">AH</div>
            <h1 class="email-title">@yield('title')</h1>
            @hasSection('subtitle')
                <p class="email-subtitle">@yield('subtitle')</p>
            @endif
        </div>

        <!-- Content -->
        <div class="email-content">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p class="footer-text">
                Merci de faire confiance à {{ config('app.name') }} pour votre apprentissage n8n !
            </p>
            
            <div class="footer-links">
                <a href="{{ url('/') }}">Accueil</a>
                <a href="{{ url('/tutorials') }}">Tutoriels</a>
                <a href="{{ url('/contact') }}">Support</a>
                <a href="{{ url('/help') }}">Aide</a>
            </div>

            <div class="social-links">
                <a href="#" title="Facebook">📘</a>
                <a href="#" title="Twitter">🐦</a>
                <a href="#" title="LinkedIn">💼</a>
            </div>

            <p class="unsubscribe">
                Vous recevez cet email car vous êtes inscrit sur {{ config('app.name') }}.<br>
                <a href="{{ url('/settings/notifications') }}">Gérer mes notifications</a> |
                <a href="#">Se désabonner</a>
            </p>
        </div>
    </div>
</body>
</html>