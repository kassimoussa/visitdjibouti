#!/bin/bash

#############################################################
# Script de correction des migrations - Visit Djibouti
# À exécuter sur la VM Ubuntu
#############################################################

echo "╔══════════════════════════════════════════════════════╗"
echo "║     Correction de l'ordre des migrations             ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""

# Vérifier qu'on est dans le bon répertoire
if [ ! -d "database/migrations" ]; then
    echo "❌ Erreur: Répertoire database/migrations non trouvé"
    echo "   Exécutez ce script depuis /var/www/html/visitdjibouti"
    exit 1
fi

echo "📂 Répertoire: $(pwd)"
echo ""

#############################################################
# ÉTAPE 1: Reset de la base de données
#############################################################
echo "🗄️  ÉTAPE 1: Reset de la base de données"
echo "   ⚠️  Cette commande va SUPPRIMER toutes les tables !"
echo ""
read -p "   Voulez-vous continuer ? (y/N) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "   ❌ Opération annulée"
    exit 1
fi

php artisan migrate:fresh --force

if [ $? -ne 0 ]; then
    echo ""
    echo "❌ Erreur lors du reset de la base de données"
    echo ""
    echo "🔧 Solution manuelle:"
    echo "   1. Connectez-vous à MySQL:"
    echo "      mysql -u root -p"
    echo ""
    echo "   2. Supprimez et recréez la base:"
    echo "      DROP DATABASE vidj;"
    echo "      CREATE DATABASE vidj CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    echo "      exit;"
    echo ""
    echo "   3. Relancez ce script"
    exit 1
fi

echo ""
echo "✅ Base de données réinitialisée"
echo ""

#############################################################
# ÉTAPE 2: Exécuter les migrations dans le bon ordre
#############################################################
echo "🔄 ÉTAPE 2: Exécution des migrations"
echo ""

php artisan migrate --force

if [ $? -eq 0 ]; then
    echo ""
    echo "╔══════════════════════════════════════════════════════╗"
    echo "║          ✅ MIGRATIONS RÉUSSIES !                    ║"
    echo "╚══════════════════════════════════════════════════════╝"
    echo ""
    echo "📋 Tables créées:"
    php artisan db:show
    echo ""
    echo "🔧 Prochaines étapes:"
    echo "   1. Seeder la base: php artisan db:seed"
    echo "   2. Créer un admin: php artisan make:admin"
    echo "   3. Tester l'application"
    echo ""
else
    echo ""
    echo "❌ Erreur lors des migrations"
    echo ""
    echo "🔍 Vérifiez les logs:"
    echo "   tail -f storage/logs/laravel.log"
    echo ""
    exit 1
fi

#############################################################
# ÉTAPE 3: Informations sur les migrations
#############################################################
echo "📊 État des migrations:"
php artisan migrate:status
echo ""
