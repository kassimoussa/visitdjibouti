<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de réinitialisation de mot de passe</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px 0;
        }
        .otp-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 30px;
            border-radius: 8px;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 10px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        .otp-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/logo_visitdjibouti.png')) }}" alt="Visit Djibouti" style="max-width: 200px; margin-bottom: 20px;">
            <h1>🔐 Réinitialisation de mot de passe</h1>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $userName }}</strong>,</p>

            <p>Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte Visit Djibouti.</p>

            <div class="otp-box">
                <div class="otp-label">VOTRE CODE DE VÉRIFICATION</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-label">Valide pendant 15 minutes</div>
            </div>

            <div class="info-box">
                <strong>Comment utiliser ce code :</strong>
                <ol style="margin: 10px 0;">
                    <li>Ouvrez l'application Visit Djibouti sur votre appareil mobile</li>
                    <li>Accédez à la page de réinitialisation de mot de passe</li>
                    <li>Entrez le code ci-dessus</li>
                    <li>Créez votre nouveau mot de passe</li>
                </ol>
            </div>

            <div class="warning">
                <strong>⚠️ Attention :</strong>
                <ul style="margin: 10px 0;">
                    <li>Ce code est valide pendant <strong>15 minutes</strong> uniquement</li>
                    <li>Ne partagez jamais ce code avec qui que ce soit</li>
                    <li>L'équipe Visit Djibouti ne vous demandera jamais ce code</li>
                    <li>Vous avez <strong>3 tentatives</strong> maximum pour entrer le bon code</li>
                </ul>
            </div>

            <p style="margin-top: 20px;">
                <strong>Vous n'avez pas demandé cette réinitialisation ?</strong><br>
                Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email.
                Votre mot de passe restera inchangé et ce code expirera automatiquement.
            </p>
        </div>

        <div class="footer">
            <p>
                Cet email a été envoyé par <strong>Visit Djibouti</strong><br>
                Pour toute question, contactez-nous à <a href="mailto:appsupport@visitdjibouti.dj">appsupport@visitdjibouti.dj</a>
            </p>
            <p style="margin-top: 10px; color: #999;">
                © {{ date('Y') }} Visit Djibouti - Office National du Tourisme de Djibouti
            </p>
        </div>
    </div>
</body>
</html>
