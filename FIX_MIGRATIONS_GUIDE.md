# 🔧 Guide de Correction des Migrations

## ❌ Problème Rencontré

```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'vidj.tour_operator_users' doesn't exist
```

**Cause**: Une migration essaie de **modifier** une table avant qu'elle ne soit **créée**.

### Ordre Incorrect des Migrations

❌ **AVANT** (ordre incorrect):
```
2025_01_06_120000_add_username_to_tour_operator_users_table.php  (6 janvier - MODIFICATION)
2025_09_27_001000_create_tour_operator_users_table.php           (27 septembre - CRÉATION)
```

✅ **APRÈS** (ordre correct):
```
2025_09_27_001000_create_tour_operator_users_table.php           (27 septembre - CRÉATION)
2025_09_27_004000_add_username_to_tour_operator_users_table.php  (27 septembre - MODIFICATION)
```

---

## ✅ Solutions

### Solution 1: Commandes Directes sur la VM (RAPIDE)

```bash
# 1. Se placer dans le répertoire
cd /var/www/html/visitdjibouti

# 2. Reset complet de la base de données (⚠️ SUPPRIME LES DONNÉES)
php artisan migrate:fresh --force

# 3. Vérifier le statut
php artisan migrate:status
```

---

### Solution 2: Reset Manuel MySQL + Migration

```bash
# 1. Connectez-vous à MySQL
mysql -u root -p

# 2. Dans MySQL, supprimez et recréez la base
DROP DATABASE vidj;
CREATE DATABASE vidj CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# 3. Relancez les migrations
cd /var/www/html/visitdjibouti
php artisan migrate --force
```

---

### Solution 3: Utiliser le Script Automatique

```bash
# Télécharger ou créer le script FIX_MIGRATIONS_VM.sh
cd /var/www/html/visitdjibouti
chmod +x FIX_MIGRATIONS_VM.sh
./FIX_MIGRATIONS_VM.sh
```

---

## 🔍 Vérifications

### Voir l'ordre des migrations

```bash
ls -la database/migrations/ | grep -E "(tour_operator|events)"
```

**Ordre correct attendu:**

```
2025_05_24_172053_create_events_tables.php                       (création events)
2025_05_24_172054_add_allow_reservations_to_events_table.php    (modification events)
...
2025_09_27_001000_create_tour_operator_users_table.php           (création tour_operator_users)
2025_09_27_004000_add_username_to_tour_operator_users_table.php  (modification tour_operator_users)
```

### Voir le statut des migrations

```bash
php artisan migrate:status
```

### Vérifier les tables dans MySQL

```bash
mysql -u root -p -e "USE vidj; SHOW TABLES;"
```

---

## 📝 Migrations Renommées

Les fichiers suivants ont été renommés pour corriger l'ordre:

1. **❌ Ancien**: `2025_01_06_120000_add_username_to_tour_operator_users_table.php`
   **✅ Nouveau**: `2025_09_27_004000_add_username_to_tour_operator_users_table.php`

2. **❌ Ancien**: `2025_01_06_150000_add_allow_reservations_to_events_table.php`
   **✅ Nouveau**: `2025_05_24_172054_add_allow_reservations_to_events_table.php`

---

## 🚀 Après la Correction

Une fois les migrations exécutées avec succès:

```bash
# 1. Seeder la base de données (optionnel)
php artisan db:seed

# 2. Créer un utilisateur admin
php artisan make:admin

# 3. Voir les tables créées
php artisan db:show

# 4. Vérifier la structure d'une table
php artisan db:table tour_operator_users
```

---

## ⚠️ Important - Environnement de Production

Si vous avez déjà des données en production:

1. ❌ **NE PAS** utiliser `migrate:fresh` (supprime tout)
2. ✅ **Utiliser** `migrate:rollback` puis `migrate`
3. ✅ **Faire une sauvegarde** avant toute modification:

```bash
# Backup de la base
mysqldump -u root -p vidj > backup_$(date +%Y%m%d_%H%M%S).sql

# Rollback des migrations problématiques
php artisan migrate:rollback --step=1

# Relancer les migrations
php artisan migrate
```

---

## 🆘 Dépannage

### Erreur: "Table already exists"

```bash
# Supprimer la table manuellement
mysql -u root -p -e "USE vidj; DROP TABLE IF EXISTS tour_operator_users;"

# Relancer la migration spécifique
php artisan migrate --path=database/migrations/2025_09_27_001000_create_tour_operator_users_table.php
```

### Erreur: "Migration not found"

```bash
# Vérifier que le fichier existe
ls -la database/migrations/ | grep tour_operator

# Vider le cache
php artisan config:clear
php artisan cache:clear
```

### Voir les logs d'erreur

```bash
tail -f storage/logs/laravel.log
```

---

## 📊 Ordre Complet des Migrations (Résumé)

```
1. Migrations Laravel par défaut (users, cache, jobs)
2. Personal access tokens (Sanctum)
3. Roles & Admin users
4. Categories, Media, POIs (mai 2025)
5. Events (mai 2025) + modification reservations (mai 2025)
6. External links, Embassies, Organization (août 2025)
7. App users & favorites (août 2025)
8. Tour operators (août 2025)
9. Tours (janvier 2025 - dates en octobre)
10. Tour operator users (septembre 2025) + modifications (septembre 2025)
```

---

✅ **Migrations corrigées avec succès !**
