<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation Visit Djibouti</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .credentials {
            background: white;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #007bff;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏝️ Visit Djibouti</h1>
        <p>Espace Tour Operators</p>
    </div>

    <div class="content">
        <h2>Bienvenue, {{ $user->name }} !</h2>

        <p>Vous avez été invité à rejoindre l'espace Tour Operators de <strong>Visit Djibouti</strong> pour <strong>{{ $tourOperator->getTranslatedName('fr') }}</strong>.</p>

        <p>Cette plateforme vous permettra de :</p>
        <ul>
            <li>📅 Gérer vos événements touristiques</li>
            <li>🎫 Suivre vos réservations en temps réel</li>
            <li>🗺️ Organiser vos tours guidés</li>
            <li>📊 Consulter vos rapports et statistiques</li>
            <li>👥 Gérer votre profil et vos préférences</li>
        </ul>

        <div class="credentials">
            <h3>🔐 Vos identifiants de connexion</h3>
            <p><strong>Email :</strong> {{ $user->email }}</p>
            <p><strong>Mot de passe temporaire :</strong> <code style="background: #e9ecef; padding: 4px 8px; border-radius: 4px;">{{ $password }}</code></p>
            <p style="color: #dc3545; font-size: 14px;">
                ⚠️ <strong>Important :</strong> Changez votre mot de passe lors de votre première connexion
            </p>
        </div>

        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="btn">Se connecter maintenant</a>
        </div>

        <div style="background: #d1ecf1; padding: 15px; border-radius: 6px; margin: 20px 0; color: #0c5460;">
            <strong>💡 Conseil :</strong> Ajoutez cette adresse email à vos favoris pour recevoir les notifications importantes.
        </div>

        <p>Votre poste : <strong>{{ $user->position ?? 'Utilisateur' }}</strong></p>

        <p>Si vous avez des questions ou besoin d'aide, n'hésitez pas à nous contacter.</p>

        <p>Bonne découverte de votre nouvel espace de travail !</p>

        <p>Cordialement,<br>
        <strong>L'équipe Visit Djibouti</strong></p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Visit Djibouti - Promotion du Tourisme à Djibouti</p>
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
    </div>
</body>
</html>