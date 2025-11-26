<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur Visit Djibouti</title>
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
        .welcome-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 30px;
            border-radius: 8px;
            margin: 30px 0;
        }
        .welcome-box h2 {
            margin: 0;
            font-size: 28px;
        }
        .feature-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .feature-box ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .feature-box li {
            margin: 8px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
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
            <h1>🎉 Bienvenue sur Visit Djibouti</h1>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $userName }}</strong>,</p>

            <div class="welcome-box">
                <h2>Bienvenue dans votre nouvelle aventure !</h2>
                <p style="margin-top: 15px; font-size: 16px;">Merci de rejoindre la communauté Visit Djibouti</p>
            </div>

            <p>Nous sommes ravis de vous accueillir parmi nous ! Votre compte a été créé avec succès et vous pouvez maintenant profiter de toutes les fonctionnalités de notre application.</p>

            <div class="feature-box">
                <strong>🌟 Découvrez tout ce que vous pouvez faire :</strong>
                <ul>
                    <li><strong>Explorer les sites touristiques</strong> - Découvrez les merveilles de Djibouti</li>
                    <li><strong>Participer aux événements</strong> - Ne manquez aucun événement culturel ou festif</li>
                    <li><strong>Enregistrer vos favoris</strong> - Sauvegardez vos lieux et événements préférés</li>
                    <li><strong>Réserver vos activités</strong> - Inscrivez-vous facilement aux événements</li>
                    <li><strong>Laisser des avis</strong> - Partagez votre expérience avec la communauté</li>
                    <li><strong>Trouver des opérateurs touristiques</strong> - Planifiez vos excursions avec des professionnels</li>
                </ul>
            </div>

            <div class="info-box">
                <strong>📱 Votre compte :</strong><br>
                Email : <strong>{{ $email }}</strong><br>
                <p style="margin-top: 10px;">
                    Vous pouvez maintenant vous connecter à tout moment avec cet email et votre mot de passe.
                </p>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <p><strong>Prêt à explorer Djibouti ?</strong></p>
                <p style="color: #666;">Ouvrez l'application Visit Djibouti sur votre appareil mobile et commencez votre aventure !</p>
            </div>

            <div class="info-box">
                <strong>💡 Astuce :</strong><br>
                N'oubliez pas d'activer les notifications push pour être informé des nouveaux événements et des meilleures offres touristiques !
            </div>

            <p style="margin-top: 20px;">
                <strong>Besoin d'aide ?</strong><br>
                Notre équipe est là pour vous accompagner. N'hésitez pas à nous contacter si vous avez des questions ou besoin d'assistance.
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
