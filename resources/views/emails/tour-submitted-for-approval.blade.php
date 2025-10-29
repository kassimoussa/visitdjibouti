<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Tour à Approuver - Visit Djibouti</title>
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
            background: linear-gradient(135deg, #ffc107, #ff9800);
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
            border-left: 4px solid #ffc107;
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
        .alert {
            background: #fff3cd;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            color: #856404;
            border-left: 4px solid #ffc107;
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
        <p>Espace Administration</p>
    </div>

    <div class="content">
        <h2>📋 Nouveau Tour en Attente d'Approbation</h2>

        <p>Un nouveau tour a été soumis pour approbation et nécessite votre attention.</p>

        <div class="tour-info">
            <h3>🎯 Informations du Tour</h3>
            <p><strong>Titre :</strong> {{ $tour->title }}</p>
            <p><strong>Type :</strong> {{ $tour->type_label }}</p>
            <p><strong>Prix :</strong> {{ $tour->formatted_price }}</p>
            <p><strong>Durée :</strong> {{ $tour->formatted_duration }}</p>
            <p><strong>Date de soumission :</strong> {{ $tour->submitted_at->format('d/m/Y à H:i') }}</p>
        </div>

        <div class="tour-info">
            <h3>🏢 Tour Operator</h3>
            <p><strong>Nom :</strong> {{ $operator->name }}</p>
            @if($creator)
            <p><strong>Créé par :</strong> {{ $creator->name }} ({{ $creator->email }})</p>
            <p><strong>Poste :</strong> {{ $creator->position ?? 'Non spécifié' }}</p>
            @endif
        </div>

        <div class="alert">
            <strong>⏰ Action Requise :</strong> Ce tour est en attente de votre approbation. Veuillez examiner les détails et prendre une décision (approuver ou rejeter).
        </div>

        <div style="text-align: center;">
            <a href="{{ $approvalUrl }}" class="btn">Examiner le Tour</a>
        </div>

        <p><strong>Description courte :</strong></p>
        <p style="background: white; padding: 15px; border-radius: 6px;">
            {{ $tour->short_description ?: 'Aucune description fournie' }}
        </p>

        <p>Vous pouvez approuver ou rejeter ce tour depuis l'interface d'administration.</p>

        <p>Cordialement,<br>
        <strong>Système Visit Djibouti</strong></p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Visit Djibouti - Promotion du Tourisme à Djibouti</p>
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
    </div>
</body>
</html>
