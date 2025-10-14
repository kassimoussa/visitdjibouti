-- Script SQL pour marquer les migrations consolidées comme déjà exécutées
-- À utiliser UNIQUEMENT si vous avez déjà une base de données avec les tables existantes
--
-- INSTRUCTIONS:
-- 1. Faire un BACKUP complet de la base de données
-- 2. Vérifier que toutes les tables existent déjà
-- 3. Vider la table migrations existante (optionnel)
-- 4. Exécuter ce script
--
-- Date: 2025-10-14
-- Projet: Visit Djibouti

-- Vider la table migrations existante (décommenter si nécessaire)
-- TRUNCATE TABLE migrations;

-- Insérer toutes les migrations consolidées comme batch 1
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

-- Vérifier le résultat
SELECT * FROM migrations ORDER BY batch, migration;

-- Compter les migrations
SELECT COUNT(*) as total_migrations FROM migrations;
