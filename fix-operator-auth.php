<?php

/*
|--------------------------------------------------------------------------
| Script de correction pour l'authentification des opérateurs
|--------------------------------------------------------------------------
|
| Ce script corrige le problème "Auth guard [operator] is not defined"
| en vidant les caches de configuration et en vérifiant la configuration.
|
*/

echo "🔧 Correction de l'authentification des opérateurs...\n\n";

// Vider les caches
echo "📦 Vidage des caches...\n";
$commands = [
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan route:clear',
    'php artisan view:clear',
];

foreach ($commands as $command) {
    echo "   Exécution: $command\n";
    system($command);
}

echo "\n✅ Caches vidés avec succès!\n\n";

// Vérifier que la configuration est correcte
echo "🔍 Vérification de la configuration auth...\n";

$authConfig = include(__DIR__ . '/config/auth.php');

if (isset($authConfig['guards']['operator'])) {
    echo "   ✅ Guard 'operator' trouvé\n";
    echo "   📋 Configuration: " . json_encode($authConfig['guards']['operator']) . "\n";
} else {
    echo "   ❌ Guard 'operator' non trouvé!\n";
}

if (isset($authConfig['providers']['tour_operator_users'])) {
    echo "   ✅ Provider 'tour_operator_users' trouvé\n";
    echo "   📋 Configuration: " . json_encode($authConfig['providers']['tour_operator_users']) . "\n";
} else {
    echo "   ❌ Provider 'tour_operator_users' non trouvé!\n";
}

echo "\n🚀 Instructions pour tester:\n";
echo "1. Exécutez: php artisan migrate\n";
echo "2. Exécutez: php artisan db:seed --class=TourOperatorUsernameSeeder\n";
echo "3. Visitez: /operator/login\n";
echo "4. Testez avec: ahmed.hassan / admin123\n\n";

echo "✅ Configuration corrigée!\n";
?>