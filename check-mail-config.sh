#!/bin/bash

# Script de diagnostic de la configuration email
# Usage: ./check-mail-config.sh

echo "=========================================="
echo "Diagnostic de la configuration email"
echo "=========================================="
echo ""

# 1. Vérifier les fichiers de cache
echo "1. Vérification des fichiers de cache..."
if [ -f "bootstrap/cache/config.php" ]; then
    echo "   ⚠️  PROBLÈME: Le fichier bootstrap/cache/config.php existe"
    echo "      Ce fichier peut contenir une ancienne configuration."
    echo "      Exécutez: sudo rm bootstrap/cache/config.php && php artisan config:clear"
else
    echo "   ✅ Aucun fichier config.php en cache"
fi
echo ""

# 2. Vérifier les variables d'environnement
echo "2. Variables d'environnement (.env)..."
echo "   MAIL_MAILER: $(grep '^MAIL_MAILER=' .env | cut -d '=' -f2)"
echo "   MAIL_HOST: $(grep '^MAIL_HOST=' .env | cut -d '=' -f2)"
echo "   MAIL_PORT: $(grep '^MAIL_PORT=' .env | cut -d '=' -f2)"
echo "   MAIL_USERNAME: $(grep '^MAIL_USERNAME=' .env | cut -d '=' -f2)"
echo "   MAIL_ENCRYPTION: $(grep '^MAIL_ENCRYPTION=' .env | cut -d '=' -f2)"
echo "   MAIL_FROM_ADDRESS: $(grep '^MAIL_FROM_ADDRESS=' .env | cut -d '=' -f2)"
echo ""

# 3. Vérifier la configuration Laravel actuelle
echo "3. Configuration Laravel actuelle..."
php artisan tinker --execute="
echo '   Mail Default: ' . config('mail.default') . PHP_EOL;
echo '   Mail Host: ' . config('mail.mailers.smtp.host') . PHP_EOL;
echo '   Mail Port: ' . config('mail.mailers.smtp.port') . PHP_EOL;
echo '   Mail Username: ' . config('mail.mailers.smtp.username') . PHP_EOL;
echo '   Mail From: ' . config('mail.from.address') . PHP_EOL;
"
echo ""

echo "=========================================="
echo "Diagnostic terminé"
echo "=========================================="
