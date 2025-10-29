<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Approuvé - Visit Djibouti</title>
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
            background: linear-gradient(135deg, #28a745, #20c997);
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
        .tour-info {
            background: white;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
        .tour-info p {
            margin: 8px 0;
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
        .success-box {
            background: #d4edda;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            color: #155724;
            border-left: 4px solid #28a745;
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
        <h2>✅ Félicitations ! Votre Tour a été Approuvé</h2>

        <div class="success-box">
            <strong>🎉 Bonne nouvelle :</strong> Votre tour a été approuvé par l'équipe d'administration et est maintenant visible publiquement sur la plateforme Visit Djibouti !
        </div>

        <p>Bonjour,</p>

        <p>Nous avons le plaisir de vous informer que votre tour a été approuvé avec succès.</p>

        <div class="tour-info">
            <h3>🎯 Détails du Tour Approuvé</h3>
            <p><strong>Titre :</strong> {{ $tour->title }}</p>
            <p><strong>Type :</strong> {{ $tour->type_label }}</p>
            <p><strong>Prix :</strong> {{ $tour->formatted_price }}</p>
            <p><strong>Durée :</strong> {{ $tour->formatted_duration }}</p>
            <p><strong>Date d'approbation :</strong> {{ $tour->approved_at->format('d/m/Y à H:i') }}</p>
            @if($admin)
            <p><strong>Approuvé par :</strong> {{ $admin->name }}</p>
            @endif
        </div>

        <div style="background: #e7f3ff; padding: 15px; border-radius: 6px; margin: 20px 0; color: #004085;">
            <strong>📢 Prochaines Étapes :</strong>
            <ul>
                <li>Votre tour est maintenant visible par le public</li>
                <li>Les utilisateurs peuvent réserver des places</li>
                <li>Vous recevrez des notifications pour chaque réservation</li>
                <li>Vous pouvez consulter vos statistiques dans votre tableau de bord</li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ $tourUrl }}" class="btn">Voir Mon Tour</a>
        </div>

        <div style="background: #fff3cd; padding: 15px; border-radius: 6px; margin: 20px 0; color: #856404;">
            <strong>⚠️ Important :</strong> Si vous modifiez des informations importantes de ce tour (prix, dates, durée, etc.), il devra être resoumis pour approbation.
        </div>

        <p>Continuez à offrir des expériences exceptionnelles à nos visiteurs !</p>

        <p>Cordialement,<br>
        <strong>L'équipe Visit Djibouti</strong></p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Visit Djibouti - Promotion du Tourisme à Djibouti</p>
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
    </div>
</body>
</html>
