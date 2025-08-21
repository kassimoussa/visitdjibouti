# 📮 Guide de Test des APIs avec Postman

## 🚀 Installation et Configuration

### 1. Importer les fichiers Postman

1. **Collection API** : `Visit-Djibouti-API.postman_collection.json`
2. **Environnement Développement** : `Visit-Djibouti-Environment.postman_environment.json`
3. **Environnement Production** : `Visit-Djibouti-Production.postman_environment.json`

### 2. Configuration des environnements

#### Développement
- **Base URL** : `http://localhost:8000`
- **Email de test** : `test@visitdjibouti.dj`
- **Mot de passe** : `password123`

#### Production
- **Base URL** : `https://api.visitdjibouti.dj`
- **Email de démo** : `demo@visitdjibouti.dj`
- **Mot de passe** : `DemoPassword2024!`

---

## 🔄 Workflow de Test Recommandé

### Phase 1A : Authentification Anonyme (NEW) 🚀
```
1. Create Anonymous User (onboarding sans friction)
2. Retrieve Anonymous User (récupération par anonymous_id)
3. Update Anonymous Preferences (langue, notifications)
```

### Phase 1B : Authentification Traditionnelle
```
4. Register (créer un compte test)
5. Login (récupérer le token)
6. Get Profile (vérifier l'authentification)
```

### Phase 1C : Conversion Anonyme (NEW) 🔄
```
7. Convert Anonymous to Complete (utilisateur anonyme → complet)
8. Verify Data Preservation (favoris et données conservées)
```

### Phase 2 : Contenu Public
```
9. Get All POIs (tester les filtres)
10. Get POI Details (par ID et slug)
11. Get Nearby POIs (géolocalisation)
12. Get All Events (tester les filtres)
13. Get Event Details
14. Get Organization Info
15. Get External Links
16. Get Embassies
```

### Phase 3 : Fonctionnalités Utilisateur (Anonyme + Authentifié)
```
17. Register for Event (anonyme et authentifié)
18. Get My Registrations
19. Update Profile
20. Add POI to Favorites (anonyme)
21. Add Event to Favorites (anonyme)
22. Get All Favorites (vérifier synchronisation)
```

### Phase 4 : Test Complet du Workflow Anonyme
```
23. Create Anonymous User avec device_id
24. Add 3-5 Favorites (POIs et Events)
25. Make Event Reservation (anonyme)
26. Convert to Complete Account
27. Verify All Data Preserved (favoris + réservations)
28. Delete Anonymous User (cleanup test)
```

### Phase 5 : OAuth (si configuré)
```
29. Google OAuth Redirect
30. Facebook OAuth Redirect
31. Mobile OAuth Token
```

---

## 🚀 Tests Spécifiques Utilisateurs Anonymes

### 🔍 Variables à Surveiller
```javascript
// Variables Postman à créer pour les tests anonymes
anonymous_id          // Stocké après création
anonymous_token       // Token pour authentification anonyme
device_id            // Identifiant unique de l'appareil test
```

### 📋 Checklist de Validation
- [ ] Création utilisateur anonyme avec device_id
- [ ] Récupération utilisateur existant avec même device_id
- [ ] Ajout favoris avec utilisateur anonyme
- [ ] Réservation événement avec utilisateur anonyme
- [ ] Conversion vers compte complet
- [ ] Préservation des données lors de la conversion
- [ ] Suppression utilisateur anonyme

### 🎯 Points de Test Critiques
1. **Unicité device_id** : Un seul utilisateur anonyme par device_id
2. **Conservation des données** : Favoris + réservations préservées lors conversion
3. **Tokens valides** : Tokens anonymes vs. complets fonctionnent correctement
4. **Synchronisation** : Les favoris apparaissent immédiatement après ajout

---

## 🧪 Scripts de Test Automatiques

### Script de Collection (déjà inclus)
```javascript
// Auto-sauvegarde du token d'authentification
if (pm.response.json() && pm.response.json().data && pm.response.json().data.token) {
    pm.environment.set('auth_token', pm.response.json().data.token);
    console.log('Token saved: ' + pm.response.json().data.token);
}

// Tests automatiques de structure
pm.test('Response has success field', function () {
    pm.expect(pm.response.json()).to.have.property('success');
});

pm.test('Status code is successful', function () {
    pm.expect(pm.response.code).to.be.oneOf([200, 201]);
});
```

### 🚀 Script Spécifique Utilisateurs Anonymes (NEW)
```javascript
// Auto-sauvegarde des données anonymes
if (pm.response.json() && pm.response.json().data && pm.response.json().data.anonymous_id) {
    pm.environment.set('anonymous_id', pm.response.json().data.anonymous_id);
    pm.environment.set('anonymous_token', pm.response.json().data.token);
    console.log('Anonymous data saved: ' + pm.response.json().data.anonymous_id);
}

// Test spécifique utilisateurs anonymes
pm.test('Anonymous user created successfully', function () {
    const response = pm.response.json();
    pm.expect(response.data.user.is_anonymous).to.be.true;
    pm.expect(response.data.anonymous_id).to.be.a('string');
});

// Test conversion anonyme
pm.test('Anonymous conversion preserves data', function () {
    const response = pm.response.json();
    if (response.data && response.data.user && response.data.user.converted_at) {
        pm.expect(response.data.user.is_anonymous).to.be.false;
        pm.expect(response.data.user.converted_at).to.be.a('string');
    }
});
```

---

## 📊 Données de Test Prêtes à l'Emploi

### Utilisateur de Test
```json
{
  "name": "Jean Testeur",
  "email": "test@visitdjibouti.dj",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+253 21 35 40 50",
  "preferred_language": "fr",
  "date_of_birth": "1990-05-15",
  "gender": "male",
  "city": "Djibouti"
}
```

### Coordonnées GPS Utiles
- **Lac Assal** : `11.6560, 42.4065`
- **Djibouti Ville** : `11.5721, 43.1456`
- **Tadjourah** : `11.7833, 42.8833`
- **Ali Sabieh** : `11.1556, 42.7125`

### Filtres de Test
- **Régions** : `Djibouti`, `Ali Sabieh`, `Dikhil`, `Tadjourah`, `Obock`, `Arta`
- **Statuts événements** : `upcoming`, `ongoing`
- **Types ambassades** : `foreign_in_djibouti`, `djiboutian_abroad`

---

## 🔍 Tests Spécifiques par Endpoint

### 🔐 Authentication Tests

#### Register
```bash
# Cas de succès
✅ Données complètes et valides
✅ Champs optionnels omis

# Cas d'erreur à tester
❌ Email déjà existant
❌ Mots de passe non identiques
❌ Email invalide
❌ Champs requis manquants
```

#### Login
```bash
# Cas de succès
✅ Email + mot de passe corrects

# Cas d'erreur à tester
❌ Email inexistant
❌ Mot de passe incorrect
❌ Champs manquants
```

### 🏛️ POIs Tests

#### Get All POIs
```bash
# Filtres à tester
✅ ?featured=1
✅ ?region=Tadjourah
✅ ?search=lac
✅ ?category_id=2
✅ ?sort_by=name&sort_order=asc
✅ ?per_page=5&page=2

# Headers à tester
✅ Accept-Language: fr
✅ Accept-Language: en
✅ Accept-Language: ar
```

#### Nearby POIs
```bash
# Paramètres à tester
✅ latitude=11.6560&longitude=42.4065&radius=10
✅ latitude=11.5721&longitude=43.1456&radius=50

# Cas d'erreur à tester
❌ Latitude/longitude manquants
❌ Coordonnées invalides
❌ Rayon trop grand (>100km)
```

### 🎉 Events Tests

#### Register for Event
```bash
# Utilisateur authentifié
✅ participants_count=2
✅ special_requirements="Accès PMR"

# Utilisateur invité
✅ user_name, user_email, user_phone requis
✅ participants_count=1

# Cas d'erreur à tester
❌ Événement complet
❌ Événement passé
❌ Données invité manquantes
```

### 🏢 Organization Tests

#### Embassies
```bash
# Filtres à tester
✅ ?type=foreign_in_djibouti
✅ ?type=djiboutian_abroad
✅ ?search=france
✅ ?country_code=FR

# Géolocalisation
✅ /nearby?latitude=11.5721&longitude=43.1456&radius=50
```

---

## 🚨 Tests d'Erreur Importants

### Authentification
```bash
❌ Token expiré/invalide (401)
❌ Accès ressource protégée sans token (401)
❌ Token malformé (401)
```

### Validation
```bash
❌ Données manquantes (422)
❌ Format email invalide (422)
❌ Coordonnées GPS hors limites (422)
❌ Paramètres de pagination invalides (422)
```

### Ressources
```bash
❌ POI/Event/Embassy inexistant (404)
❌ Catégorie inexistante (404)
❌ Route invalide (404)
```

### Serveur
```bash
❌ Base de données indisponible (500)
❌ Service externe indisponible (500)
```

---

## 📈 Métriques de Performance à Vérifier

### Temps de Réponse
- **Authentification** : < 500ms
- **Liste POIs** : < 800ms
- **Détails POI** : < 300ms
- **Recherche géographique** : < 1000ms
- **Inscription événement** : < 600ms

### Taille des Réponses
- **Liste POIs** (15 items) : < 50KB
- **Détails POI complet** : < 20KB
- **Liste événements** : < 40KB

---

## 🔧 Variables d'Environnement Utiles

```javascript
// Variables automatiquement mises à jour
{{auth_token}}      // Token Bearer (auto-saved)
{{base_url}}        // URL de base

// Variables de test pré-configurées  
{{test_email}}      // Email de test
{{test_password}}   // Mot de passe de test
{{test_poi_id}}     // ID POI de test
{{test_event_id}}   // ID événement de test
{{test_latitude}}   // Latitude test (Lac Assal)
{{test_longitude}}  // Longitude test (Lac Assal)
```

---

## 🎯 Checklist de Test Complet

### ✅ Tests Fonctionnels
- [ ] Inscription et connexion utilisateur
- [ ] Récupération profil utilisateur
- [ ] Mise à jour profil
- [ ] Changement mot de passe
- [ ] Déconnexion
- [ ] Liste POIs avec tous les filtres
- [ ] Détails POI (ID et slug)
- [ ] POIs par catégorie
- [ ] POIs à proximité
- [ ] Liste événements avec filtres
- [ ] Détails événement
- [ ] Inscription événement (authentifié + invité)
- [ ] Mes inscriptions
- [ ] Annulation inscription
- [ ] Informations organisation
- [ ] Liens externes
- [ ] Liste ambassades avec filtres
- [ ] Détails ambassade
- [ ] Ambassades par type
- [ ] Ambassades à proximité

### ✅ Tests Multilingues
- [ ] Réponses en français (Accept-Language: fr)
- [ ] Réponses en anglais (Accept-Language: en)
- [ ] Réponses en arabe (Accept-Language: ar)

### ✅ Tests OAuth (si configuré)
- [ ] Redirection Google OAuth
- [ ] Redirection Facebook OAuth
- [ ] Authentification mobile Google
- [ ] Authentification mobile Facebook

### ✅ Tests d'Erreur
- [ ] Tous les codes d'erreur 400, 401, 404, 422, 500
- [ ] Messages d'erreur cohérents
- [ ] Validation des données
- [ ] Gestion des ressources inexistantes

---

## 🚀 Lancement Rapide

1. **Importer** les 3 fichiers JSON dans Postman
2. **Sélectionner** l'environnement "Visit Djibouti - Development"
3. **Exécuter** "Register" puis "Login" pour obtenir un token
4. **Tester** les endpoints dans l'ordre recommandé
5. **Vérifier** les réponses et les codes de statut

Le token d'authentification se sauvegarde automatiquement après le login ! 🎉