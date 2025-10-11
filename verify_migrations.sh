#!/bin/bash

#############################################################
# Script de vérification des migrations
# Vérifie que toutes les dépendances sont respectées
#############################################################

echo "╔══════════════════════════════════════════════════════╗"
echo "║     Vérification de l'ordre des migrations           ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""

cd database/migrations 2>/dev/null || cd /var/www/html/visitdjibouti/database/migrations

echo "📋 Migrations dans l'ordre chronologique:"
echo ""
ls -1 *.php | sort | nl
echo ""

echo "🔍 Vérification des dépendances critiques:"
echo ""

# Vérifier tour_operator_users
echo "1️⃣  Tour Operator Users:"
CREATE_TOU=$(ls -1 | grep "create_tour_operator_users_table" | head -1)
MOD_USERNAME=$(ls -1 | grep "add_username_to_tour_operator_users" | head -1)
MOD_PERMS=$(ls -1 | grep "remove_permissions_from_tour_operator_users" | head -1)

echo "   Création: $CREATE_TOU"
echo "   Modification (username): $MOD_USERNAME"
echo "   Modification (permissions): $MOD_PERMS"

if [[ "$CREATE_TOU" < "$MOD_USERNAME" && "$CREATE_TOU" < "$MOD_PERMS" ]]; then
    echo "   ✅ Ordre correct"
else
    echo "   ❌ ERREUR: Ordre incorrect !"
fi
echo ""

# Vérifier events
echo "2️⃣  Events:"
CREATE_EVENTS=$(ls -1 | grep "create_events_tables" | head -1)
MOD_RESERVATIONS=$(ls -1 | grep "add_allow_reservations_to_events" | head -1)

echo "   Création: $CREATE_EVENTS"
echo "   Modification (reservations): $MOD_RESERVATIONS"

if [[ "$CREATE_EVENTS" < "$MOD_RESERVATIONS" ]]; then
    echo "   ✅ Ordre correct"
else
    echo "   ❌ ERREUR: Ordre incorrect !"
fi
echo ""

# Vérifier poi_tour_operator (pivot table)
echo "3️⃣  POI-TourOperator Pivot:"
CREATE_POIS=$(ls -1 | grep "create_pois_table" | head -1)
CREATE_TO=$(ls -1 | grep "create_simple_tour_operators_tables\|create_tour_operators_tables" | tail -1)
CREATE_PIVOT=$(ls -1 | grep "create_poi_tour_operator_table" | head -1)

echo "   Création POIs: $CREATE_POIS"
echo "   Création Tour Operators: $CREATE_TO"
echo "   Création Pivot: $CREATE_PIVOT"

if [[ "$CREATE_POIS" < "$CREATE_PIVOT" && "$CREATE_TO" < "$CREATE_PIVOT" ]]; then
    echo "   ✅ Ordre correct"
else
    echo "   ❌ ERREUR: Ordre incorrect !"
fi
echo ""

# Vérifier categories
echo "4️⃣  Categories:"
CREATE_CAT=$(ls -1 | grep "create_categories_table" | head -1)
MOD_HIERARCHY=$(ls -1 | grep "add_hierarchy_to_categories" | head -1)

echo "   Création: $CREATE_CAT"
echo "   Modification (hierarchy): $MOD_HIERARCHY"

if [[ "$CREATE_CAT" < "$MOD_HIERARCHY" ]]; then
    echo "   ✅ Ordre correct"
else
    echo "   ❌ ERREUR: Ordre incorrect !"
fi
echo ""

# Vérifier app_users
echo "5️⃣  App Users:"
CREATE_APP=$(ls -1 | grep "create_app_users_table" | head -1)
MOD_ANON=$(ls -1 | grep "add_anonymous_support_to_app_users" | head -1)
MOD_DEVICE=$(ls -1 | grep "add_comprehensive_device_info_to_app_users" | head -1)

echo "   Création: $CREATE_APP"
echo "   Modification (anonymous): $MOD_ANON"
echo "   Modification (device): $MOD_DEVICE"

if [[ "$CREATE_APP" < "$MOD_ANON" && "$CREATE_APP" < "$MOD_DEVICE" ]]; then
    echo "   ✅ Ordre correct"
else
    echo "   ❌ ERREUR: Ordre incorrect !"
fi
echo ""

# Vérifier POIs
echo "6️⃣  POIs Contact Conversion:"
CREATE_POIS2=$(ls -1 | grep "create_pois_table" | head -1)
MOD_TEXT=$(ls -1 | grep "change_contact_to_text_in_pois" | head -1)
MOD_JSON=$(ls -1 | grep "convert_poi_contact_to_json" | head -1)

echo "   Création: $CREATE_POIS2"
echo "   Modification (text): $MOD_TEXT"
echo "   Modification (json): $MOD_JSON"

if [[ "$CREATE_POIS2" < "$MOD_TEXT" && "$MOD_TEXT" < "$MOD_JSON" ]]; then
    echo "   ✅ Ordre correct"
else
    echo "   ❌ ERREUR: Ordre incorrect !"
fi
echo ""

echo "╔══════════════════════════════════════════════════════╗"
echo "║          Vérification terminée                       ║"
echo "╚══════════════════════════════════════════════════════╝"
echo ""
echo "💡 Si tout est ✅, vous pouvez exécuter:"
echo "   php artisan migrate:fresh --force"
echo ""
