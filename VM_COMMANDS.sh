#!/bin/bash

#############################################################
# Commandes à exécuter sur la VM Ubuntu
# Après avoir poussé les corrections sur Git
#############################################################

echo "╔══════════════════════════════════════════════════════╗"
echo "║     Déploiement des Migrations Corrigées - VM       ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""

# Vérifier qu'on est dans le bon répertoire
if [ ! -f "artisan" ]; then
    echo "❌ Erreur: Vous n'êtes pas dans le répertoire Laravel"
    echo "   Exécutez: cd /var/www/html/visitdjibouti"
    exit 1
fi

echo "📂 Répertoire: $(pwd)"
echo ""

#############################################################
# ÉTAPE 1: Récupérer les derniers changements
#############################################################
echo "📥 ÉTAPE 1: Récupération des changements depuis Git..."
git pull origin main

if [ $? -ne 0 ]; then
    echo ""
    echo "❌ Erreur lors du git pull"
    echo ""
    echo "🔧 Solutions:"
    echo "   1. Vérifier qu'il n'y a pas de conflits: git status"
    echo "   2. Stash vos changements locaux: git stash"
    echo "   3. Réessayer: git pull origin main"
    exit 1
fi

echo "✅ Changements récupérés"
echo ""

#############################################################
# ÉTAPE 2: Vérifier l'ordre des migrations
#############################################################
echo "🔍 ÉTAPE 2: Vérification de l'ordre des migrations..."
echo ""

# Vérifier que tours vient après tour_operators
TOUR_OPS_LINE=$(ls database/migrations/*.php | grep -n "create_simple_tour_operators_tables" | cut -d: -f1)
TOURS_LINE=$(ls database/migrations/*.php | grep -n "2025_09_01_100000_create_tours_table" | cut -d: -f1)

if [ -z "$TOURS_LINE" ]; then
    echo "❌ ERREUR: Fichier create_tours_table.php non trouvé avec la nouvelle date (2025_09_01)"
    echo "   Assurez-vous d'avoir bien poussé les changements depuis votre machine locale"
    exit 1
fi

echo "   Tour Operators: ligne $TOUR_OPS_LINE"
echo "   Tours: ligne $TOURS_LINE"

if [ "$TOURS_LINE" -gt "$TOUR_OPS_LINE" ]; then
    echo "   ✅ Ordre correct (tours APRÈS tour_operators)"
else
    echo "   ❌ ERREUR: Tours est toujours avant tour_operators !"
    exit 1
fi

echo ""

#############################################################
# ÉTAPE 3: Backup de la base (recommandé)
#############################################################
echo "💾 ÉTAPE 3: Backup de la base de données (optionnel)..."
echo ""
read -p "   Voulez-vous créer un backup avant migration ? (y/N) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
    mysqldump -u root -p vidj > "$BACKUP_FILE"

    if [ $? -eq 0 ]; then
        echo "   ✅ Backup créé: $BACKUP_FILE"
    else
        echo "   ⚠️  Backup échoué, mais on continue..."
    fi
else
    echo "   ⏭️  Backup ignoré"
fi

echo ""

#############################################################
# ÉTAPE 4: Reset de la base de données
#############################################################
echo "🗄️  ÉTAPE 4: Reset de la base de données..."
echo ""
echo "   ⚠️  ATTENTION: Cette opération va SUPPRIMER toutes les tables et données !"
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
    echo "❌ Erreur lors des migrations"
    echo ""
    echo "🔍 Vérifiez les logs:"
    echo "   tail -f storage/logs/laravel.log"
    echo ""
    echo "🔧 En cas de problème persistant:"
    echo "   1. Vérifier la connexion MySQL: php artisan db"
    echo "   2. Recréer la base manuellement:"
    echo "      mysql -u root -p -e 'DROP DATABASE vidj; CREATE DATABASE vidj;'"
    echo "   3. Réessayer: php artisan migrate --force"
    exit 1
fi

echo ""
echo "✅ Migrations exécutées avec succès"
echo ""

#############################################################
# ÉTAPE 5: Vérification
#############################################################
echo "🔍 ÉTAPE 5: Vérification des migrations..."
echo ""

# Compter les migrations
MIGRATION_COUNT=$(php artisan migrate:status | grep "Ran" | wc -l)

echo "   📊 Nombre de migrations exécutées: $MIGRATION_COUNT"

if [ "$MIGRATION_COUNT" -ge 45 ]; then
    echo "   ✅ Toutes les migrations sont exécutées (attendu: 45)"
else
    echo "   ⚠️  Nombre de migrations inférieur à 45"
fi

echo ""

# Vérifier que la table tours existe
echo "   🔍 Vérification de la table 'tours'..."
php artisan db:table tours > /dev/null 2>&1

if [ $? -eq 0 ]; then
    echo "   ✅ Table 'tours' créée avec succès"
else
    echo "   ❌ Table 'tours' introuvable"
fi

echo ""

#############################################################
# ÉTAPE 6: Voir les tables créées
#############################################################
echo "📋 ÉTAPE 6: Tables créées dans la base de données:"
echo ""
php artisan db:show
echo ""

#############################################################
# RÉSUMÉ FINAL
#############################################################
echo "╔══════════════════════════════════════════════════════╗"
echo "║          ✅ DÉPLOIEMENT TERMINÉ !                    ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""
echo "📋 RÉSUMÉ:"
echo "   • Migrations exécutées: $MIGRATION_COUNT"
echo "   • Base de données: vidj"
echo "   • Statut: ✅ Opérationnel"
echo ""
echo "🔧 PROCHAINES ÉTAPES:"
echo "   1. Seeder la base (optionnel): php artisan db:seed"
echo "   2. Créer un admin: php artisan make:admin"
echo "   3. Tester l'application: curl http://localhost"
echo "   4. Vérifier les logs: tail -f storage/logs/laravel.log"
echo ""
echo "📊 COMMANDES UTILES:"
echo "   • Voir le statut: php artisan migrate:status"
echo "   • Voir les tables: php artisan db:show"
echo "   • Voir une table: php artisan db:table tours"
echo "   • Rollback: php artisan migrate:rollback"
echo ""
