<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle inscription</title>
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
            background-color: #0d6efd;
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
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box h3 {
            margin-top: 0;
            color: #0d6efd;
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
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #0d6efd;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .alert {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Nouvelle Inscription</h1>
        </div>

        <div class="content">
            <p>Bonjour,</p>

            <p>Vous avez reçu une nouvelle inscription pour l'activité <strong>{{ $activity->title }}</strong>.</p>

            <div class="info-box">
                <h3>👤 Informations du Client</h3>
                <div class="info-row">
                    <span class="label">Nom :</span>
                    <span class="value">{{ $registration->customer_name }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Email :</span>
                    <span class="value">{{ $registration->customer_email }}</span>
                </div>
                @if($registration->customer_phone)
                <div class="info-row">
                    <span class="label">Téléphone :</span>
                    <span class="value">{{ $registration->customer_phone }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="label">Type de client :</span>
                    <span class="value">
                        @if($registration->appUser)
                            Utilisateur inscrit
                        @else
                            Invité
                        @endif
                    </span>
                </div>
            </div>

            <div class="info-box">
                <h3>📋 Détails de l'Inscription</h3>
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
                    <span class="label">Prix total :</span>
                    <span class="value">{{ number_format($registration->total_price, 0, ',', ' ') }} DJF</span>
                </div>
                <div class="info-row">
                    <span class="label">Date d'inscription :</span>
                    <span class="value">{{ $registration->created_at->format('d/m/Y à H:i') }}</span>
                </div>
            </div>

            @if($registration->special_requirements)
            <div class="alert">
                <strong>Exigences spéciales :</strong><br>
                {{ $registration->special_requirements }}
            </div>
            @endif

            @if($registration->medical_conditions)
            <div class="alert" style="background-color: #f8d7da; border-color: #dc3545;">
                <strong>⚠️ Conditions médicales :</strong><br>
                {{ $registration->medical_conditions }}
            </div>
            @endif

            <p style="text-align: center;">
                <a href="{{ route('operator.activity-registrations.show', $registration) }}" class="button">
                    Voir l'inscription
                </a>
            </p>

            <p><strong>Prochaines étapes :</strong></p>
            <ul>
                <li>Vérifiez les disponibilités pour la date demandée</li>
                <li>Contactez le client si nécessaire</li>
                <li>Confirmez ou refusez l'inscription depuis votre tableau de bord</li>
            </ul>
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement depuis la plateforme Visit Djibouti.</p>
            <p>Pour toute question, connectez-vous à votre <a href="{{ route('operator.dashboard') }}">tableau de bord</a>.</p>
        </div>
    </div>
</body>
</html>
