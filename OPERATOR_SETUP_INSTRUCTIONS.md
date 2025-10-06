# 🔧 Instructions de Configuration - Authentification Tour Operators

## ❌ Problème Identifié
```
Auth guard [operator] is not defined.
```

## ✅ Solution Mise en Place

### 1. **Configuration Vérifiée**
- ✅ Guard `operator` défini dans `config/auth.php`
- ✅ Provider `tour_operator_users` configuré
- ✅ Routes d'authentification créées
- ✅ Middlewares personnalisés utilisés

### 2. **Modifications Effectuées**

#### A) **Migration ajoutée** : `username` dans `tour_operator_users`
```php
// database/migrations/2025_01_06_120000_add_username_to_tour_operator_users_table.php
Schema::table('tour_operator_users', function (Blueprint $table) {
    $table->string('username')->unique()->after('name');
    $table->index(['username']);
});
```

#### B) **Modèle mis à jour** : `TourOperatorUser.php`
- Support authentification par `username` ou `email`
- Méthode `findForAuth()` ajoutée

#### C) **Contrôleur d'authentification** : `AuthController.php`
- Accepte `login` (username ou email) + `password`
- Validation et authentification flexible

#### D) **Routes corrigées** : `routes/operator.php`
- Évite `guest:operator` qui pose problème
- Utilise middleware personnalisé `operator.auth`

### 3. **Commandes à Exécuter**

```bash
# 1. Vider les caches de configuration
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# 2. Exécuter la migration
php artisan migrate

# 3. Créer les comptes de test
php artisan db:seed --class=TourOperatorUsernameSeeder

# 4. Redémarrer le serveur
php artisan serve
```

### 4. **Comptes de Test Créés**

| Username | Mot de passe | Rôle | Permissions |
|----------|--------------|------|-------------|
| `ahmed.hassan` | `admin123` | Directeur | Accès complet |
| `fatima.manager` | `manager123` | Manager | Events, Tours, Réservations |
| `omar.guide` | `guide123` | Guide | Tours (lecture seule) |
| `mohamed.director` | `director123` | Directeur | Accès complet |
| `sarah.coordinator` | `coord123` | Coordinatrice | Events, Tours, Réservations |

### 5. **URLs de Test**

- **Connexion** : `/operator/login`
- **Dashboard** : `/operator/dashboard` (après connexion)

### 6. **Vérifications à Effectuer**

1. **Accéder à** `/operator/login`
2. **Tester connexion** avec `ahmed.hassan` / `admin123`
3. **Vérifier redirection** vers `/operator/dashboard`
4. **Tester permissions** selon le rôle

### 7. **Si le Problème Persiste**

#### A) Vérifier la configuration en direct :
```php
// Dans une route de test
dd(config('auth.guards.operator'));
dd(config('auth.providers.tour_operator_users'));
```

#### B) Vérifier que le modèle est bien accessible :
```php
// Test de connexion directe
$user = \App\Models\TourOperatorUser::where('username', 'ahmed.hassan')->first();
dd($user);
```

#### C) Forcer la configuration :
```php
// Dans AppServiceProvider ou un provider personnalisé
Auth::extend('operator-session', function ($app, $name, array $config) {
    return new \Illuminate\Auth\SessionGuard(
        $name,
        Auth::getProvider($config['provider']),
        app('session.store')
    );
});
```

## ✅ Résultat Attendu

Après ces modifications, les opérateurs peuvent :
- Se connecter via `/operator/login`
- Utiliser leur `username` ou `email` + `password`
- Accéder à leur dashboard personnalisé
- Gérer leurs événements selon leurs permissions

## 🔄 Prochaines Étapes

1. Tester l'authentification complète
2. Vérifier les permissions par rôle
3. Tester la gestion des événements
4. Configurer les notifications email