<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe - Visit Djibouti</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #007bff;
            margin-bottom: 20px;
        }
        .alert-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .alert-box p {
            margin: 0;
            color: #856404;
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: white !important;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 25px 0;
            font-weight: bold;
            text-align: center;
        }
        .btn:hover {
            background: #0056b3;
        }
        .token-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border: 2px dashed #dee2e6;
            margin: 20px 0;
            text-align: center;
        }
        .token-box code {
            background: white;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            color: #007bff;
            font-weight: bold;
            display: inline-block;
            letter-spacing: 2px;
        }
        .info-box {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 0;
            color: #0c5460;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 13px;
            border-top: 1px solid #dee2e6;
        }
        .footer p {
            margin: 5px 0;
        }
        .divider {
            height: 1px;
            background: #dee2e6;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🔐 Visit Djibouti</h1>
            <p>Réinitialisation de mot de passe</p>
        </div>

        <div class="content">
            <p class="greeting">Bonjour {{ $userName }},</p>

            <p>Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte Visit Djibouti.</p>

            <p>Pour réinitialiser votre mot de passe, cliquez sur le bouton ci-dessous :</p>

            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="btn">Réinitialiser mon mot de passe</a>
            </div>

            <div class="alert-box">
                <p><strong>⏰ Important :</strong> Ce lien est valable pendant <strong>60 minutes</strong>. Après ce délai, vous devrez faire une nouvelle demande.</p>
            </div>

            <div class="divider"></div>

            <p><strong>Si le bouton ne fonctionne pas,</strong> copiez et collez ce lien dans votre navigateur :</p>

            <div class="token-box">
                <code style="word-break: break-all; letter-spacing: 0;">{{ $resetUrl }}</code>
            </div>

            <div class="info-box">
                <p><strong>🛡️ Sécurité :</strong> Si vous n'avez pas demandé cette réinitialisation, ignorez simplement cet email. Votre mot de passe actuel restera inchangé.</p>
            </div>

            <div class="divider"></div>

            <p><strong>Conseils de sécurité :</strong></p>
            <ul style="color: #6c757d;">
                <li>Utilisez un mot de passe unique et complexe</li>
                <li>Ne partagez jamais votre mot de passe</li>
                <li>Activez l'authentification à deux facteurs si disponible</li>
            </ul>

            <p style="margin-top: 30px;">Si vous avez des questions ou besoin d'aide, n'hésitez pas à nous contacter.</p>

            <p>Cordialement,<br>
            <strong>L'équipe Visit Djibouti</strong></p>
        </div>

        <div class="footer">
            <p><strong>© {{ date('Y') }} Visit Djibouti</strong></p>
            <p>Promotion du Tourisme à Djibouti</p>
            <p style="margin-top: 10px; font-size: 11px;">
                Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.
            </p>
        </div>
    </div>
</body>
</html>
