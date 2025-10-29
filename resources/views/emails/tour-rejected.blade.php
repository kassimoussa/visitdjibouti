<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Non Approuvé - Visit Djibouti</title>
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
            background: linear-gradient(135deg, #dc3545, #c82333);
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
            border-left: 4px solid #dc3545;
            margin: 20px 0;
        }
        .tour-info p {
            margin: 8px 0;
        }
        .reason-box {
            background: #f8d7da;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            color: #721c24;
            border-left: 4px solid #dc3545;
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
        .info-box {
            background: #d1ecf1;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            color: #0c5460;
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
        <h2>❌ Votre Tour n'a pas été Approuvé</h2>

        <p>Bonjour,</p>

        <p>Nous vous informons que votre tour n'a malheureusement pas été approuvé par l'équipe d'administration.</p>

        <div class="tour-info">
            <h3>🎯 Détails du Tour</h3>
            <p><strong>Titre :</strong> {{ $tour->title }}</p>
            <p><strong>Type :</strong> {{ $tour->type_label }}</p>
            <p><strong>Prix :</strong> {{ $tour->formatted_price }}</p>
            <p><strong>Durée :</strong> {{ $tour->formatted_duration }}</p>
            @if($admin)
            <p><strong>Examiné par :</strong> {{ $admin->name }}</p>
            @endif
        </div>

        <div class="reason-box">
            <h3>📝 Raison du Refus</h3>
            <p><strong>{{ $rejectionReason }}</strong></p>
        </div>

        <div class="info-box">
            <strong>💡 Que Faire Maintenant ?</strong>
            <ul style="margin: 10px 0;">
                <li>Examinez attentivement la raison du refus ci-dessus</li>
                <li>Apportez les modifications nécessaires à votre tour</li>
                <li>Resoumettez votre tour pour une nouvelle approbation</li>
                <li>Si vous avez des questions, contactez l'administration</li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ $tourUrl }}" class="btn">Modifier Mon Tour</a>
        </div>

        <div style="background: #fff3cd; padding: 15px; border-radius: 6px; margin: 20px 0; color: #856404;">
            <strong>⚠️ Rappel :</strong> Assurez-vous que toutes les informations sont complètes, exactes et conformes à nos standards de qualité avant de resoumettre votre tour.
        </div>

        <p>Notre objectif est de maintenir la qualité et la fiabilité de l'offre touristique sur Visit Djibouti. Nous vous encourageons à améliorer votre tour et à le soumettre à nouveau.</p>

        <p>N'hésitez pas à nous contacter si vous avez besoin de clarifications ou d'assistance.</p>

        <p>Cordialement,<br>
        <strong>L'équipe Visit Djibouti</strong></p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Visit Djibouti - Promotion du Tourisme à Djibouti</p>
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
    </div>
</body>
</html>
