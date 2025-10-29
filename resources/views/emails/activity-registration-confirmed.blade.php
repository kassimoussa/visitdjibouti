<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription confirmée</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #198754;
            color: #fff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .success-icon {
            text-align: center;
            font-size: 60px;
            margin: 20px 0;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #198754;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box h3 {
            margin-top: 0;
            color: #198754;
            font-size: 16px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #6c757d;
        }
        .value {
            color: #333;
        }
        .highlight-box {
            background-color: #d1e7dd;
            border: 2px solid #198754;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .highlight-box h2 {
            margin: 0 0 10px 0;
            color: #198754;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .contact-info {
            background-color: #e7f3ff;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Inscription Confirmée</h1>
        </div>

        <div class="content">
            <div class="success-icon">
                🎉
            </div>

            <p>Bonjour <strong>{{ $registration->customer_name }}</strong>,</p>

            <p>Bonne nouvelle ! Votre inscription à l'activité <strong>{{ $activity->title }}</strong> a été confirmée par l'opérateur.</p>

            <div class="highlight-box">
                <h2>Votre inscription est confirmée !</h2>
                <p style="margin: 0;">Numéro de réservation : <strong>#{{ $registration->id }}</strong></p>
            </div>

            <div class="info-box">
                <h3>📋 Détails de votre Activité</h3>
                <div class="info-row">
                    <span class="label">Activité :</span>
                    <span class="value">{{ $activity->title }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Nombre de participants :</span>
                    <span class="value">{{ $registration->number_of_people }}</span>
                </div>
                @if($registration->preferred_date)
                <div class="info-row">
                    <span class="label">Date préférée :</span>
                    <span class="value">{{ $registration->preferred_date->format('d/m/Y') }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="label">Durée :</span>
                    <span class="value">{{ $activity->formatted_duration }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Niveau de difficulté :</span>
                    <span class="value">{{ $activity->difficulty_label }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Prix total :</span>
                    <span class="value"><strong>{{ number_format($registration->total_price, 0, ',', ' ') }} DJF</strong></span>
                </div>
            </div>

            @if($activity->location_address)
            <div class="info-box">
                <h3>📍 Lieu de Rendez-vous</h3>
                <p style="margin: 0;">{{ $activity->location_address }}</p>
            </div>
            @endif

            @if($activity->equipment_required && count($activity->equipment_required) > 0)
            <div class="info-box">
                <h3>🎒 À Apporter</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    @foreach($activity->equipment_required as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="contact-info">
                <h3 style="margin-top: 0; color: #0d6efd;">📞 Contact Opérateur</h3>
                <div class="info-row">
                    <span class="label">Nom :</span>
                    <span class="value">{{ $operator->name }}</span>
                </div>
                @if($operator->phone)
                <div class="info-row">
                    <span class="label">Téléphone :</span>
                    <span class="value">{{ $operator->phone }}</span>
                </div>
                @endif
                @if($operator->email)
                <div class="info-row">
                    <span class="label">Email :</span>
                    <span class="value">{{ $operator->email }}</span>
                </div>
                @endif
                <p style="margin: 10px 0 0 0; font-size: 14px; color: #6c757d;">
                    N'hésitez pas à contacter l'opérateur pour toute question concernant l'activité.
                </p>
            </div>

            <p><strong>Prochaines étapes :</strong></p>
            <ul>
                <li>L'opérateur vous contactera pour finaliser les détails</li>
                <li>Préparez l'équipement nécessaire (voir liste ci-dessus)</li>
                <li>Arrivez au point de rendez-vous à l'heure convenue</li>
            </ul>

            <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef; font-size: 14px; color: #6c757d;">
                <strong>Note :</strong> En cas d'empêchement, veuillez annuler votre inscription le plus tôt possible depuis l'application ou en contactant directement l'opérateur.
            </p>
        </div>

        <div class="footer">
            <p>Profitez bien de votre activité !</p>
            <p>L'équipe Visit Djibouti</p>
        </div>
    </div>
</body>
</html>
