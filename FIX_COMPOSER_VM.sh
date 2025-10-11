#!/bin/bash

#############################################################
# Script de correction Composer - Visit Djibouti
# À exécuter sur la VM Ubuntu
#############################################################

echo "╔══════════════════════════════════════════════════════╗"
echo "║     Correction du problème Composer Lock File       ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""

# Vérifier qu'on est dans le bon répertoire
if [ ! -f "composer.json" ]; then
    echo "❌ Erreur: composer.json non trouvé"
    echo "   Exécutez ce script depuis /var/www/html/visitdjibouti"
    exit 1
fi

echo "📂 Répertoire actuel: $(pwd)"
echo ""

#############################################################
# Solution 1: Mettre à jour le lock file (RAPIDE)
#############################################################
echo "🔧 Option 1: Mise à jour du composer.lock uniquement..."
echo "   Cette commande va synchroniser le lock file avec composer.json"
echo ""

composer update --lock --no-scripts --no-interaction

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ composer.lock mis à jour avec succès !"
    echo ""
    echo "📦 Installation des dépendances..."
    composer install --no-dev --optimize-autoloader --no-interaction

    if [ $? -eq 0 ]; then
        echo ""
        echo "╔══════════════════════════════════════════════════════╗"
        echo "║          ✅ INSTALLATION TERMINÉE !                  ║"
        echo "╚══════════════════════════════════════════════════════╝"
        echo ""
        echo "📋 Vérification de laravel/socialite:"
        composer show laravel/socialite 2>/dev/null || echo "⚠️  Package non installé"
        echo ""
        echo "🔧 Prochaines étapes:"
        echo "   1. Configurer le .env"
        echo "   2. Exécuter: php artisan migrate"
        echo "   3. Générer la clé: php artisan key:generate"
        echo ""
    else
        echo ""
        echo "❌ Erreur lors de l'installation des dépendances"
        echo ""
        echo "🔄 Essayez la Solution 2 ci-dessous..."
        echo ""
    fi
else
    echo ""
    echo "⚠️  La mise à jour du lock file a échoué"
    echo ""
fi

echo ""
echo "════════════════════════════════════════════════════════"
echo ""
echo "🔄 SOLUTION 2 (si la Solution 1 a échoué):"
echo ""
echo "   # Supprimer le lock file et réinstaller"
echo "   rm -f composer.lock"
echo "   composer install --no-dev --optimize-autoloader"
echo ""
echo "════════════════════════════════════════════════════════"
echo ""
echo "🔄 SOLUTION 3 (alternative - installe tout):"
echo ""
echo "   # Update complet (installe les nouvelles dépendances)"
echo "   composer update --no-dev --optimize-autoloader"
echo ""
echo "════════════════════════════════════════════════════════"
echo ""
