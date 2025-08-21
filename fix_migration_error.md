# Correction de l'erreur de migration - Index en double

## Problème
L'erreur `Duplicate key name 'reservations_reservable_type_reservable_id_index'` indique que l'index a été créé deux fois :
1. Automatiquement par `morphs('reservable')`
2. Manuellement par `$table->index(['reservable_type', 'reservable_id'])`

## Solution appliquée
Suppression de l'index manuel redondant dans la migration.

## Commandes pour corriger

### Option 1 : Si la migration a échoué (recommandé)
```bash
# La migration a été corrigée, relancez simplement
php artisan migrate
```

### Option 2 : Si la table existe avec l'erreur
```bash
# Supprimer la table problématique
php artisan migrate:rollback --step=1

# Relancer la migration corrigée  
php artisan migrate
```

### Option 3 : Si nécessaire, reset complet (attention aux données)
```bash
# ATTENTION: Ceci supprime toutes les données
php artisan migrate:fresh

# Ou si vous avez des seeders
php artisan migrate:fresh --seed
```

## Vérification
Après la migration réussie, vérifiez que la table est créée :
```bash
php artisan tinker
```

Puis dans tinker :
```php
Schema::hasTable('reservations')
// Doit retourner: true

\DB::select("SHOW INDEX FROM reservations")
// Doit afficher les index sans doublons
```

## Index créés automatiquement par morphs()
La méthode `morphs('reservable')` crée automatiquement :
- Colonnes : `reservable_id`, `reservable_type`  
- Index : `reservations_reservable_type_reservable_id_index`

## Index additionnels dans la migration
- `reservations_app_user_id_index`
- `reservations_guest_email_index`  
- `reservations_reservation_date_status_index`
- `reservations_confirmation_number_index`
- `reservations_status_index`

La migration devrait maintenant s'exécuter sans erreur.