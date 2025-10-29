<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription annulée</title>
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
            background-color: #dc3545;
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
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box h3 {
            margin-top: 0;
            color: #dc3545;
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
        .alert {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Inscription Annulée</h1>
        </div>

        <div class="content">
            @if($cancelledBy === 'operator')
                {{-- Email pour le client quand l'opérateur annule --}}
                <p>Bonjour <strong>{{ $registration->customer_name }}</strong>,</p>

                <p>Nous sommes désolés de vous informer que votre inscription à l'activité <strong>{{ $activity->title }}</strong> a été annulée par l'opérateur.</p>

                @if($registration->cancellation_reason)
                <div class="alert">
                    <strong>Raison de l'annulation :</strong><br>
                    {{ $registration->cancellation_reason }}
                </div>
                @endif

                <div class="info-box">
                    <h3>📋 Détails de l'Inscription Annulée</h3>
                    <div class="info-row">
                        <span class="label">Numéro :</span>
                        <span class="value">#{{ $registration->id }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Activité :</span>
                        <span class="value">{{ $activity->title }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Participants :</span>
                        <span class="value">{{ $registration->number_of_people }}</span>
                    </div>
                    @if($registration->preferred_date)
                    <div class="info-row">
                        <span class="label">Date prévue :</span>
                        <span class="value">{{ $registration->preferred_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="label">Annulée le :</span>
                        <span class="value">{{ $registration->cancelled_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>

                <p>Si un paiement a été effectué, un remboursement sera traité selon les conditions générales.</p>

                <p>Pour toute question concernant cette annulation, vous pouvez contacter directement l'opérateur :</p>
                <ul>
                    <li><strong>{{ $operator->name }}</strong></li>
                    @if($operator->phone)
                    <li>Téléphone : {{ $operator->phone }}</li>
                    @endif
                    @if($operator->email)
                    <li>Email : {{ $operator->email }}</li>
                    @endif
                </ul>

                <p>Nous vous invitons à découvrir d'autres activités disponibles sur Visit Djibouti.</p>

            @else
                {{-- Email pour l'opérateur quand le client annule --}}
                <p>Bonjour,</p>

                <p>L'utilisateur <strong>{{ $registration->customer_name }}</strong> a annulé son inscription à l'activité <strong>{{ $activity->title }}</strong>.</p>

                @if($registration->cancellation_reason)
                <div class="alert">
                    <strong>Raison de l'annulation :</strong><br>
                    {{ $registration->cancellation_reason }}
                </div>
                @endif

                <div class="info-box">
                    <h3>📋 Détails de l'Inscription Annulée</h3>
                    <div class="info-row">
                        <span class="label">Numéro :</span>
                        <span class="value">#{{ $registration->id }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Client :</span>
                        <span class="value">{{ $registration->customer_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Activité :</span>
                        <span class="value">{{ $activity->title }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Participants libérés :</span>
                        <span class="value">{{ $registration->number_of_people }}</span>
                    </div>
                    @if($registration->preferred_date)
                    <div class="info-row">
                        <span class="label">Date prévue :</span>
                        <span class="value">{{ $registration->preferred_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="label">Annulée le :</span>
                        <span class="value">{{ $registration->cancelled_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>

                <p>Les places ont été automatiquement libérées et sont maintenant disponibles pour d'autres participants.</p>

                @if($registration->payment_status === 'paid')
                <p style="margin-top: 20px; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107;">
                    <strong>⚠️ Action requise :</strong> Un paiement a été reçu pour cette inscription. Veuillez traiter le remboursement selon votre politique d'annulation.
                </p>
                @endif
            @endif
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement depuis la plateforme Visit Djibouti.</p>
            @if($cancelledBy === 'user')
            <p>Connectez-vous à votre <a href="{{ route('operator.dashboard') }}">tableau de bord</a> pour plus de détails.</p>
            @endif
        </div>
    </div>
</body>
</html>
