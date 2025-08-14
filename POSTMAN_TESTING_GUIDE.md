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

### Phase 1 : Authentification
```
1. Register (créer un compte test)
2. Login (récupérer le token)
3. Get Profile (vérifier l'authentification)
```

### Phase 2 : Contenu Public
```
4. Get All POIs (tester les filtres)
5. Get POI Details (par ID et slug)
6. Get Nearby POIs (géolocalisation)
7. Get All Events (tester les filtres)
8. Get Event Details
9. Get Organization Info
10. Get External Links
11. Get Embassies
```

### Phase 3 : Fonctionnalités Utilisateur
```
12. Register for Event (authentifié)
13. Get My Registrations
14. Update Profile
15. Change Password
```

### Phase 4 : OAuth (si configuré)
```
16. Google OAuth Redirect
17. Facebook OAuth Redirect
18. Mobile OAuth Token
```

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