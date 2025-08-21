# Test des API Réservations

## 1. Créer une réservation pour un POI (utilisateur invité)

```bash
curl -X POST http://197.241.32.130:8080/api/reservations \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Accept-Language: fr" \
  -d '{
    "reservable_type": "poi",
    "reservable_id": 1,
    "reservation_date": "2025-08-25",
    "reservation_time": "14:00",
    "number_of_people": 2,
    "guest_name": "Jean Dupont",
    "guest_email": "jean.dupont@email.com",
    "guest_phone": "+253 77 12 34 56",
    "special_requirements": "Accès handicapé nécessaire"
  }'
```

## 2. Créer une réservation pour un événement (utilisateur invité)

```bash
curl -X POST http://197.241.32.130:8080/api/reservations \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Accept-Language: fr" \
  -d '{
    "reservable_type": "event",
    "reservable_id": 1,
    "reservation_date": "2025-08-30",
    "number_of_people": 1,
    "guest_name": "Marie Martin",
    "guest_email": "marie.martin@email.com",
    "notes": "Première visite à Djibouti"
  }'
```

## 3. Consulter une réservation par numéro de confirmation

```bash
curl -X GET http://197.241.32.130:8080/api/reservations/POI-ABC12345 \
  -H "Accept: application/json" \
  -H "Accept-Language: fr"
```

## 4. Créer une réservation avec utilisateur authentifié

```bash
# D'abord, s'authentifier
curl -X POST http://197.241.32.130:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'

# Utiliser le token reçu pour faire une réservation
curl -X POST http://197.241.32.130:8080/api/reservations \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Accept-Language: fr" \
  -H "Authorization: Bearer TOKEN_ICI" \
  -d '{
    "reservable_type": "poi",
    "reservable_id": 2,
    "reservation_date": "2025-09-01",
    "number_of_people": 3,
    "special_requirements": "Groupe avec enfants"
  }'
```

## 5. Voir toutes ses réservations (utilisateur authentifié)

```bash
curl -X GET http://197.241.32.130:8080/api/reservations \
  -H "Accept: application/json" \
  -H "Accept-Language: fr" \
  -H "Authorization: Bearer TOKEN_ICI"
```

## 6. Filtrer les réservations

```bash
# Seulement les POIs
curl -X GET "http://197.241.32.130:8080/api/reservations?type=poi" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN_ICI"

# Seulement les événements
curl -X GET "http://197.241.32.130:8080/api/reservations?type=event" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN_ICI"

# Seulement les réservations confirmées
curl -X GET "http://197.241.32.130:8080/api/reservations?status=confirmed" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN_ICI"

# Seulement les réservations à venir
curl -X GET "http://197.241.32.130:8080/api/reservations?upcoming=true" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN_ICI"
```

## 7. Annuler une réservation

```bash
curl -X PATCH http://197.241.32.130:8080/api/reservations/POI-ABC12345/cancel \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer TOKEN_ICI" \
  -d '{
    "reason": "Changement de plans"
  }'
```

## Structure des réponses API

### Réservation créée avec succès
```json
{
  "success": true,
  "message": "Reservation created successfully",
  "data": {
    "reservation": {
      "id": 1,
      "confirmation_number": "POI-ABC12345",
      "reservable_type": "poi",
      "reservable_id": 1,
      "reservable_name": "Lac Assal",
      "reservation_date": "2025-08-25",
      "reservation_time": "14:00",
      "number_of_people": 2,
      "status": "pending",
      "user_name": "Jean Dupont",
      "user_email": "jean.dupont@email.com",
      "user_phone": "+253 77 12 34 56",
      "special_requirements": "Accès handicapé nécessaire",
      "payment_status": "not_required",
      "payment_amount": null,
      "can_be_cancelled": true,
      "is_active": true,
      "created_at": "2025-08-20T10:30:00Z",
      "updated_at": "2025-08-20T10:30:00Z"
    }
  }
}
```

## Endpoints disponibles

### Routes publiques :
- `POST /api/reservations` - Créer une réservation (invité ou authentifié)
- `GET /api/reservations/{confirmation_number}` - Détails d'une réservation

### Routes authentifiées :
- `GET /api/reservations` - Toutes les réservations de l'utilisateur
- `PATCH /api/reservations/{confirmation_number}/cancel` - Annuler une réservation

### Paramètres de filtrage pour GET /api/reservations :
- `type` : poi|event
- `status` : pending|confirmed|cancelled|completed|no_show
- `upcoming` : true|false
- `per_page` : nombre d'éléments par page (max 50)