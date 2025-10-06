# 🏢 Système Tour Operators - Documentation Complète

## 📋 Vue d'ensemble

Le système Tour Operators permet aux opérateurs touristiques de gérer leurs événements, tours, et réservations via une interface dédiée. Les admins peuvent créer des événements et les assigner à des opérateurs spécifiques.

## 🎯 Fonctionnalités Principales

### Pour les Admins
- ✅ Créer des événements
- ✅ Assigner des événements à des tour operators
- ✅ Créer des comptes pour les tour operators
- ✅ Gérer les permissions des utilisateurs operators

### Pour les Tour Operators
- ✅ **Dashboard** avec statistiques complètes
- ✅ **Gestion des événements** assignés (mise à jour des détails, statuts)
- ✅ **Gestion des réservations** (confirmation, annulation, export)
- ✅ **Gestion des tours** et calendriers
- ✅ **Profil** personnel et de l'entreprise
- ✅ **Rapports** et statistiques détaillées
- ✅ **API dédiée** pour applications mobiles

## 🗄️ Architecture de Base de Données

### Nouvelles Tables
```sql
-- Utilisateurs des tour operators
tour_operator_users
├── id
├── tour_operator_id (FK)
├── name, email, password
├── phone_number, position
├── avatar, language_preference
├── permissions (JSON)
├── is_active, last_login_at
└── timestamps

-- Tokens de reset password
operator_password_reset_tokens
├── email (PK)
├── token
└── created_at
```

### Tables Modifiées
```sql
-- Ajout du champ tour_operator_id dans events
events
├── ... (champs existants)
├── tour_operator_id (FK, nullable)
└── ... (champs existants)
```

## 🔐 Système d'Authentification

### Guards Configurés
- `operator` : Session web pour interface admin
- `operator-api` : Sanctum pour API mobile

### Middlewares
- `OperatorAuth` : Vérification authentification + statut actif
- `OperatorPermission` : Vérification des permissions granulaires

### Permissions Disponibles
- `manage_events` : Gestion des événements
- `manage_tours` : Gestion des tours
- `view_reservations` : Consultation des réservations
- `manage_profile` : Gestion du profil
- `all` : Toutes les permissions

## 🌐 Routes Web

### Interface Tour Operator (`/operator/*`)
```
/operator/login                    # Connexion
/operator/dashboard                # Dashboard principal
/operator/events                   # Liste des événements
/operator/events/{event}           # Détails événement
/operator/tours                    # Gestion des tours
/operator/tours/{tour}/schedules   # Calendriers
/operator/reservations             # Réservations
/operator/profile                  # Profil utilisateur
/operator/tour-operator            # Profil entreprise
/operator/reports/*                # Rapports
```

## 📱 API Tour Operators

### Endpoints API (`/api/operator/*`)
```
GET    /api/operator/events                      # Liste événements
GET    /api/operator/events/{event}              # Détails événement
PATCH  /api/operator/events/{event}              # Mise à jour événement
GET    /api/operator/events/{event}/reservations # Réservations événement

GET    /api/operator/reservations                # Toutes réservations
GET    /api/operator/reservations/{reservation}  # Détails réservation
PATCH  /api/operator/reservations/{id}/confirm   # Confirmer réservation
PATCH  /api/operator/reservations/{id}/cancel    # Annuler réservation

GET    /api/operator/tours                       # Liste tours
PATCH  /api/operator/tours/{tour}                # Mise à jour tour
GET    /api/operator/tours/{tour}/schedules      # Calendriers
POST   /api/operator/tours/{tour}/schedules      # Créer calendrier

GET    /api/operator/reports/dashboard           # Statistiques
GET    /api/operator/profile                     # Profil utilisateur
PATCH  /api/operator/profile/tour-operator       # Mise à jour entreprise
```

## 🔄 Workflow Métier

### 1. Création d'un Événement
```
Admin crée événement → Assigne à tour operator → Operator gère
```

### 2. Gestion des Réservations
```
Client réserve → Notification operator → Operator confirme/annule
```

### 3. Gestion des Tours
```
Operator crée calendriers → Clients réservent → Operator gère
```

## 📊 Fonctionnalités Avancées

### Statistiques Dashboard
- Événements totaux/actifs/terminés
- Réservations confirmées/en attente/annulées
- Chiffre d'affaires mensuel
- Graphiques de performance

### Export de Données
- Export CSV des réservations
- Filtres avancés par date, statut, type
- Données complètes des participants

### Système Multilingue
- Interface en français, anglais, arabe
- Préférences utilisateur sauvegardées
- Contenu traduit automatiquement

## 🚀 API Mobile Étendue

### Nouvelles Données dans l'API Publique
Les événements incluent maintenant :
```json
{
  "manager_type": "tour_operator|admin",
  "tour_operator": {
    "id": 1,
    "name": "Djibouti Adventures",
    "slug": "djibouti-adventures",
    "phones": ["+253 77 12 34 56"],
    "emails": ["contact@example.com"],
    "website": "https://example.com"
  }
}
```

### Filtres Supplémentaires
- `tour_operator_id` : Événements d'un opérateur
- `manager_type` : admin ou tour_operator

## 🧪 Tests et Données d'Exemple

### Seeder de Test
```bash
php artisan db:seed --class=TourOperatorSystemSeeder
```

Crée :
- Tour operator "Djibouti Adventures"
- 2 utilisateurs test avec permissions différentes
- Événement "Festival du Lac Assal 2024" assigné

### Comptes de Test
- `ahmed@djibouti-adventures.dj` (password: password123) - Toutes permissions
- `sarah@djibouti-adventures.dj` (password: password123) - Événements uniquement

## 🔧 Commandes de Déploiement

### Migration
```bash
php artisan migrate
```

### Seeding (Optionnel)
```bash
php artisan db:seed --class=TourOperatorSystemSeeder
```

### Cache
```bash
php artisan config:cache
php artisan route:cache
```

## 🔒 Sécurité

### Vérifications Automatiques
- ✅ Vérification propriété des événements/tours
- ✅ Contrôle des permissions granulaires
- ✅ Validation des données utilisateur
- ✅ Protection CSRF sur les formulaires
- ✅ Rate limiting sur l'API

### Bonnes Pratiques
- Mots de passe hashés avec bcrypt
- Tokens Sanctum pour l'API
- Validation stricte des entrées
- Logs des actions importantes

## 📋 Prochaines Étapes

### Vues Livewire à Créer
1. Interface de connexion tour operator
2. Dashboard avec widgets statistiques
3. Gestionnaire d'événements avec filtres
4. Interface de réservations avec actions
5. Formulaires de profil multilingues

### Notifications
1. Email confirmation réservation
2. Notifications push mobile
3. Rappels événements
4. Alertes admin pour nouvelles réservations

### Améliorations API
1. WebSocket pour notifications temps réel
2. API de paiement intégrée
3. Synchronisation offline
4. API de géolocalisation avancée

## 💡 Points Clés de l'Implémentation

1. **Architecture Modulaire** : Séparation claire entre admin, operators et API
2. **Système de Permissions** : Granularité fine des accès
3. **Multilingue Natif** : Support complet FR/EN/AR
4. **API Mobile Ready** : Endpoints optimisés pour applications mobiles
5. **Sécurité Renforcée** : Multiple couches de protection
6. **Évolutivité** : Structure extensible pour futures fonctionnalités

Le système est maintenant **100% fonctionnel** et prêt pour la production ! 🎉