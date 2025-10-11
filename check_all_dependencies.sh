#!/bin/bash

#############################################################
# Analyse COMPLÈTE de toutes les dépendances entre migrations
#############################################################

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║     Analyse Exhaustive des Dépendances - Migrations         ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

cd database/migrations 2>/dev/null || cd /var/www/html/visitdjibouti/database/migrations

#############################################################
# Fonction pour vérifier qu'une table A vient avant table B
#############################################################
check_order() {
    local table_created=$1
    local table_referenced=$2
    local migration_file=$3

    CREATE_LINE=$(ls -1 *.php | grep -n "create.*${table_created}" | head -1 | cut -d: -f1)
    REF_LINE=$(echo "$migration_file" | cut -d: -f1)

    if [ -z "$CREATE_LINE" ]; then
        echo "   ⚠️  Table '$table_created' non trouvée"
        return 1
    fi

    if [ "$CREATE_LINE" -lt "$REF_LINE" ]; then
        echo "   ✅ OK: $table_created (ligne $CREATE_LINE) avant $table_referenced (ligne $REF_LINE)"
        return 0
    else
        echo "   ❌ ERREUR: $table_referenced (ligne $REF_LINE) référence $table_created (ligne $CREATE_LINE) AVANT sa création !"
        return 1
    fi
}

#############################################################
# 1. VÉRIFIER TOUTES LES FOREIGN KEYS
#############################################################
echo "🔍 ÉTAPE 1: Analyse de toutes les foreign keys"
echo ""

ERRORS=0
WARNINGS=0
SUCCESS=0

# Extraire toutes les contraintes foreign key
echo "📋 Foreign keys détectées:"
echo ""

grep -n "constrained\|->references\|->foreign" *.php | while IFS=: read -r file line content; do

    # Extraire le nom de la table référencée
    if [[ $content =~ constrained\([\'\"]+([a-z_]+) ]]; then
        REFERENCED_TABLE="${BASH_REMATCH[1]}"
    elif [[ $content =~ references.*on\([\'\"]+([a-z_]+) ]]; then
        REFERENCED_TABLE="${BASH_REMATCH[1]}"
    else
        continue
    fi

    # Ignorer certains cas
    [[ "$REFERENCED_TABLE" == "id" ]] && continue

    echo "   📌 $file (ligne $line)"
    echo "      └─> Référence: $REFERENCED_TABLE"

done | head -50

echo ""

#############################################################
# 2. VÉRIFICATIONS SPÉCIFIQUES PAR TABLE
#############################################################
echo "🔍 ÉTAPE 2: Vérifications spécifiques"
echo ""

echo "1️⃣  ADMIN_USERS (créée mars 2025)"
echo "   Référencée par:"

# POIs
POIS_LINE=$(ls -1 *.php | grep -n "create_pois_table" | cut -d: -f1)
ADMIN_LINE=$(ls -1 *.php | grep -n "create_admin_users_table" | cut -d: -f1)

if [ "$ADMIN_LINE" -lt "$POIS_LINE" ]; then
    echo "   ✅ pois.creator_id → admin_users (ligne $POIS_LINE > $ADMIN_LINE)"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: pois avant admin_users !"
    ((ERRORS++))
fi

echo ""

echo "2️⃣  MEDIA (créée mai 2025)"
echo "   Référencée par:"

MEDIA_LINE=$(ls -1 *.php | grep -n "create_media_table" | cut -d: -f1)

# POIs featured_image
if [ "$MEDIA_LINE" -lt "$POIS_LINE" ]; then
    echo "   ✅ pois.featured_image_id → media"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: pois avant media !"
    ((ERRORS++))
fi

# Tours featured_image
TOURS_LINE=$(ls -1 *.php | grep -n "2025_09_01_100000_create_tours" | cut -d: -f1)
if [ "$MEDIA_LINE" -lt "$TOURS_LINE" ]; then
    echo "   ✅ tours.featured_image_id → media"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: tours avant media !"
    ((ERRORS++))
fi

# Organization logo
ORG_LINE=$(ls -1 *.php | grep -n "create_organization_info_table" | cut -d: -f1)
if [ "$MEDIA_LINE" -lt "$ORG_LINE" ]; then
    echo "   ✅ organization_info.logo_id → media"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: organization avant media !"
    ((ERRORS++))
fi

echo ""

echo "3️⃣  CATEGORIES (créée mai 2025)"
echo "   Référencée par:"

CAT_LINE=$(ls -1 *.php | grep -n "create_categories_table" | cut -d: -f1)

# POI categories pivot
POI_CAT_LINE=$(echo "$POIS_LINE")  # Même fichier
if [ "$CAT_LINE" -lt "$POI_CAT_LINE" ]; then
    echo "   ✅ category_poi pivot → categories"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: pivot avant categories !"
    ((ERRORS++))
fi

# Categories hierarchy (self-reference)
CAT_HIER_LINE=$(ls -1 *.php | grep -n "add_hierarchy_to_categories" | cut -d: -f1)
if [ "$CAT_LINE" -lt "$CAT_HIER_LINE" ]; then
    echo "   ✅ categories.parent_id → categories (self-reference)"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: hierarchy avant création !"
    ((ERRORS++))
fi

echo ""

echo "4️⃣  POIS (créée mai 2025)"
echo "   Référencée par:"

# poi_translations
POI_TRANS_LINE=$(echo "$POIS_LINE")  # Même fichier
echo "   ✅ poi_translations → pois (même fichier)"
((SUCCESS++))

# poi_tour_operator pivot
POI_TO_LINE=$(ls -1 *.php | grep -n "create_poi_tour_operator_table" | cut -d: -f1)
if [ "$POIS_LINE" -lt "$POI_TO_LINE" ]; then
    echo "   ✅ poi_tour_operator.poi_id → pois (ligne $POI_TO_LINE > $POIS_LINE)"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: pivot avant pois !"
    ((ERRORS++))
fi

echo ""

echo "5️⃣  EVENTS (créée mai 2025)"
echo "   Référencée par:"

EVENT_LINE=$(ls -1 *.php | grep -n "create_events_tables" | cut -d: -f1)

# Event translations, registrations, etc. (même fichier)
echo "   ✅ event_translations, event_registrations, etc. → events (même fichier)"
((SUCCESS++))

# Add tour_operator to events
EVENT_TO_LINE=$(ls -1 *.php | grep -n "add_tour_operator_to_events" | cut -d: -f1)
if [ "$EVENT_LINE" -lt "$EVENT_TO_LINE" ]; then
    echo "   ✅ events.tour_operator_id → ajouté après création"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: modification avant création !"
    ((ERRORS++))
fi

echo ""

echo "6️⃣  APP_USERS (créée août 2025)"
echo "   Référencée par:"

APP_USERS_LINE=$(ls -1 *.php | grep -n "create_app_users_table" | cut -d: -f1)

# User favorites
FAV_LINE=$(ls -1 *.php | grep -n "create_user_favorites_table" | cut -d: -f1)
if [ "$APP_USERS_LINE" -lt "$FAV_LINE" ]; then
    echo "   ✅ user_favorites.app_user_id → app_users"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: favorites avant app_users !"
    ((ERRORS++))
fi

# Reservations
RESERV_LINE=$(ls -1 *.php | grep -n "create_reservations_table" | cut -d: -f1)
if [ "$APP_USERS_LINE" -lt "$RESERV_LINE" ]; then
    echo "   ✅ reservations.app_user_id → app_users"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: reservations avant app_users !"
    ((ERRORS++))
fi

# Location history
LOC_LINE=$(ls -1 *.php | grep -n "create_user_location_history_table" | cut -d: -f1)
if [ "$APP_USERS_LINE" -lt "$LOC_LINE" ]; then
    echo "   ✅ user_location_history.app_user_id → app_users"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: location_history avant app_users !"
    ((ERRORS++))
fi

echo ""

echo "7️⃣  TOUR_OPERATORS (créée août 2025)"
echo "   Référencée par:"

TO_LINE=$(ls -1 *.php | grep -n "create_simple_tour_operators_tables" | cut -d: -f1)

# Tours
if [ "$TO_LINE" -lt "$TOURS_LINE" ]; then
    echo "   ✅ tours.tour_operator_id → tour_operators (ligne $TOURS_LINE > $TO_LINE)"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: tours avant tour_operators !"
    ((ERRORS++))
fi

# poi_tour_operator pivot
if [ "$TO_LINE" -lt "$POI_TO_LINE" ]; then
    echo "   ✅ poi_tour_operator.tour_operator_id → tour_operators"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: pivot avant tour_operators !"
    ((ERRORS++))
fi

# tour_operator_users
TOU_LINE=$(ls -1 *.php | grep -n "create_tour_operator_users_table" | cut -d: -f1)
if [ "$TO_LINE" -lt "$TOU_LINE" ]; then
    echo "   ✅ tour_operator_users.tour_operator_id → tour_operators"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: tour_operator_users avant tour_operators !"
    ((ERRORS++))
fi

# events.tour_operator_id
if [ "$TO_LINE" -lt "$EVENT_TO_LINE" ]; then
    echo "   ✅ events.tour_operator_id → tour_operators"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: events modification avant tour_operators !"
    ((ERRORS++))
fi

echo ""

echo "8️⃣  TOURS (créée septembre 2025)"
echo "   Référencée par:"

# tour_translations
TOUR_TRANS_LINE=$(ls -1 *.php | grep -n "create_tour_translations_table" | cut -d: -f1)
if [ "$TOURS_LINE" -lt "$TOUR_TRANS_LINE" ]; then
    echo "   ✅ tour_translations.tour_id → tours"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: tour_translations avant tours !"
    ((ERRORS++))
fi

# tour_schedules
TOUR_SCHED_LINE=$(ls -1 *.php | grep -n "create_tour_schedules_table" | cut -d: -f1)
if [ "$TOURS_LINE" -lt "$TOUR_SCHED_LINE" ]; then
    echo "   ✅ tour_schedules.tour_id → tours"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: tour_schedules avant tours !"
    ((ERRORS++))
fi

# media_tour pivot
MEDIA_TOUR_LINE=$(ls -1 *.php | grep -n "create_media_tour_table" | cut -d: -f1)
if [ "$TOURS_LINE" -lt "$MEDIA_TOUR_LINE" ]; then
    echo "   ✅ media_tour.tour_id → tours"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: media_tour avant tours !"
    ((ERRORS++))
fi

echo ""

#############################################################
# 3. VÉRIFIER LES RELATIONS POLYMORPHIQUES
#############################################################
echo "🔍 ÉTAPE 3: Relations polymorphiques (morphTo)"
echo ""

echo "1️⃣  user_favorites (favoritable_type/id)"
echo "   Peut référencer: pois, events"
if [ "$POIS_LINE" -lt "$FAV_LINE" ] && [ "$EVENT_LINE" -lt "$FAV_LINE" ]; then
    echo "   ✅ pois et events créées avant favorites"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: favorites avant une des tables polymorphiques !"
    ((ERRORS++))
fi

echo ""

echo "2️⃣  reservations (reservable_type/id)"
echo "   Peut référencer: events (principalement)"
if [ "$EVENT_LINE" -lt "$RESERV_LINE" ]; then
    echo "   ✅ events créée avant reservations"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: reservations avant events !"
    ((ERRORS++))
fi

echo ""

echo "3️⃣  tours (target_type/id)"
echo "   Peut référencer: pois, events"
if [ "$POIS_LINE" -lt "$TOURS_LINE" ] && [ "$EVENT_LINE" -lt "$TOURS_LINE" ]; then
    echo "   ✅ pois et events créées avant tours"
    ((SUCCESS++))
else
    echo "   ❌ ERREUR: tours avant une des tables target !"
    ((ERRORS++))
fi

echo ""

#############################################################
# RÉSUMÉ FINAL
#############################################################
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║                    RÉSUMÉ DE L'ANALYSE                       ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""
echo "✅ Vérifications réussies: $SUCCESS"
echo "❌ Erreurs détectées: $ERRORS"
echo "⚠️  Avertissements: $WARNINGS"
echo ""

TOTAL=$((SUCCESS + ERRORS))
PERCENT=$((SUCCESS * 100 / TOTAL))

echo "📊 Taux de réussite: $PERCENT% ($SUCCESS/$TOTAL)"
echo ""

if [ "$ERRORS" -eq 0 ]; then
    echo "🎉 PARFAIT ! Toutes les dépendances sont correctes !"
    echo ""
    echo "✅ Vous pouvez exécuter en toute sécurité:"
    echo "   php artisan migrate:fresh --force"
    exit 0
else
    echo "⚠️  ATTENTION ! $ERRORS erreur(s) détectée(s)"
    echo ""
    echo "🔧 Actions requises:"
    echo "   1. Corriger les migrations signalées ci-dessus"
    echo "   2. Relancer ce script pour vérifier"
    echo "   3. Puis exécuter: php artisan migrate:fresh --force"
    exit 1
fi
