# Consolidation des Migrations - Visit Djibouti

**Date**: 14 Octobre 2025
**Statut**: ✅ Complète

## Résumé

Les migrations ont été entièrement consolidées pour refléter exactement la structure de la base de données actuelle (`vidj.sql`).

- **36 anciennes migrations** supprimées
- **12 nouvelles migrations consolidées** créées
- **57 tables** couvertes (la table `migrations` est gérée automatiquement par Laravel)

## Nouvelles Migrations Créées

### 1. `0001_01_01_000000_create_base_laravel_tables.php`
Tables de base Laravel (9 tables):
- `users`
- `password_reset_tokens`
- `sessions`
- `cache`, `cache_locks`
- `jobs`, `job_batches`, `failed_jobs`
- `personal_access_tokens` (Sanctum)

### 2. `2025_01_01_000001_create_admin_auth_tables.php`
Système d'authentification admin (3 tables):
- `roles`
- `admin_users`
- `admin_password_reset_tokens`

### 3. `2025_01_01_000002_create_media_tables.php`
Système de gestion média (2 tables):
- `media`
- `media_translations`

### 4. `2025_01_01_000003_create_categories_tables.php`
Système de catégories hiérarchiques (2 tables):
- `categories` (avec `sort_order`, `icon`, `is_active`)
- `category_translations`

### 5. `2025_01_01_000004_create_app_users_table.php`
Utilisateurs mobiles avec tracking avancé (1 table):
- `app_users` (100+ colonnes incluant):
  - Support utilisateurs anonymes
  - Tracking appareil complet
  - Localisation GPS
  - Permissions et préférences
  - Statistiques d'utilisation
  - Sécurité (jailbreak, etc.)

### 6. `2025_01_01_000005_create_user_related_tables.php`
Tables liées aux utilisateurs (2 tables):
- `user_favorites` (polymorphic)
- `user_location_history`

### 7. `2025_01_01_000006_create_pois_tables.php`
Points d'intérêt touristiques (4 tables):
- `pois`
- `poi_translations`
- `category_poi` (pivot)
- `media_poi` (pivot)

### 8. `2025_01_01_000007_create_tour_operators_tables.php`
Opérateurs touristiques (6 tables):
- `tour_operators`
- `tour_operator_translations`
- `tour_operator_media`
- `tour_operator_users`
- `operator_password_reset_tokens`
- `poi_tour_operator` (pivot)

### 9. `2025_01_01_000008_create_tours_tables.php`
Tours et excursions (4 tables):
- `tours`
- `tour_translations`
- `media_tour` (pivot)
- `tour_schedules`

### 10. `2025_01_01_000009_create_events_tables.php`
Système d'événements (6 tables):
- `events`
- `event_translations`
- `category_event` (pivot)
- `media_event` (pivot)
- `event_registrations`
- `event_reviews`

### 11. `2025_01_01_000010_create_news_tables.php`
Système de news/actualités (9 tables):
- `news_categories` + `news_category_translations`
- `news_tags` + `news_tag_translations`
- `news` + `news_translations`
- `news_news_category` (pivot)
- `news_news_tag` (pivot)
- `media_news` (pivot)

### 12. `2025_01_01_000011_create_misc_tables.php`
Tables diverses (9 tables):
- `organization_info` + `organization_info_translations`
- `links` + `link_translations`
- `embassies` + `embassy_translations`
- `external_links`
- `app_settings`
- `reservations` (polymorphic)

## Améliorations Principales

### 1. **Structure Cohérente**
- Toutes les tables suivent le schéma exact de la base de données
- Relations foreign keys correctement définies
- Index optimisés pour les performances

### 2. **Système Multilingue Complet**
- Support FR, EN, AR
- Tables de traductions pour tous les contenus
- Colonnes `locale` correctement dimensionnées

### 3. **Système d'Utilisateurs Anonymes**
- Support complet pour utilisateurs anonymes
- Tracking avancé des appareils mobiles
- Conversion anonyme → utilisateur complet

### 4. **Relations Polymorphiques**
- `user_favorites` (POIs, Events, etc.)
- `reservations` (Tours, Events, POIs)

### 5. **Système de News**
- Catégories hiérarchiques
- Tags avec traductions
- Relations many-to-many

## Commandes de Migration

### Vérifier le statut
```bash
php artisan migrate:status
```

### Exécuter les migrations (⚠️ ATTENTION)
```bash
# Sur une NOUVELLE base de données vide
php artisan migrate

# Sur une base existante (DANGEREUX - faire backup d'abord!)
# Les migrations vont tenter de créer des tables qui existent déjà
```

## ⚠️ IMPORTANT: Base de Données Existante

Si vous avez déjà une base de données en production avec des données:

### Option 1: Marquer les migrations comme exécutées (RECOMMANDÉ)
```bash
# Insérer manuellement dans la table migrations
INSERT INTO migrations (migration, batch) VALUES
('0001_01_01_000000_create_base_laravel_tables', 1),
('2025_01_01_000001_create_admin_auth_tables', 1),
('2025_01_01_000002_create_media_tables', 1),
('2025_01_01_000003_create_categories_tables', 1),
('2025_01_01_000004_create_app_users_table', 1),
('2025_01_01_000005_create_user_related_tables', 1),
('2025_01_01_000006_create_pois_tables', 1),
('2025_01_01_000007_create_tour_operators_tables', 1),
('2025_01_01_000008_create_tours_tables', 1),
('2025_01_01_000009_create_events_tables', 1),
('2025_01_01_000010_create_news_tables', 1),
('2025_01_01_000011_create_misc_tables', 1);
```

### Option 2: Migration Fresh (⚠️ PERTE DE DONNÉES)
```bash
# ATTENTION: Supprime TOUTES les données!
php artisan migrate:fresh
php artisan db:seed  # Si vous avez des seeders
```

## Différences Corrigées

### Table `categories`
- ✅ `icon_class` → `icon`
- ✅ `order` → `sort_order`
- ✅ Ajout de `is_active`

### Table `app_users`
- ✅ Ajout de 80+ colonnes de tracking mobile
- ✅ Support anonyme complet
- ✅ `fcm_token` → `push_token`

### Table `tours`
- ✅ Restauration colonnes: `duration_hours`, `min_participants`, `is_recurring`, etc.
- ✅ Table `tour_schedules` recréée

### Système NEWS
- ✅ 9 tables complètes créées
- ✅ Catégories, tags, traductions, pivots

### Tables Diverses
- ✅ `event_registrations` créée
- ✅ Toutes les foreign keys correctes
- ✅ Tous les index optimisés

## Vérification de Cohérence

```bash
# Compter les tables dans SQL
grep -c "^CREATE TABLE" vidj.sql
# Résultat: 58 tables (incluant `migrations`)

# Compter les tables dans migrations
grep -c "Schema::create" database/migrations/*.php
# Résultat: 57 tables (migrations gérée par Laravel)

✅ TOUTES LES TABLES SONT PRÉSENTES
```

## Prochaines Étapes

1. ✅ Migrations consolidées
2. ⏳ Marquer migrations comme exécutées (si base existante)
3. ⏳ Tester sur environnement de développement
4. ⏳ Vérifier modèles Eloquent
5. ⏳ Tester API endpoints

## Notes Techniques

- **Ordre des migrations**: Les migrations sont ordonnées chronologiquement pour respecter les dépendances
- **Foreign Keys**: Toutes les contraintes de clés étrangères sont définies avec `onDelete` approprié
- **Index**: Index optimisés pour les requêtes fréquentes
- **JSON Columns**: Utilisation de `json()` pour flexibilité (device_languages, feature_usage, etc.)
- **Soft Deletes**: Implémenté sur tours, events, news, reservations, etc.

---

**Auteur**: Claude Code
**Version**: 1.0
**Projet**: Visit Djibouti Tourism Platform
