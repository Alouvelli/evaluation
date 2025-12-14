#!/bin/bash

# ============================================
# ISI Evaluation System - Script d'installation cPanel
# ============================================
# Usage: bash setup.sh
# À exécuter dans le dossier ~/evaluation-isi/ via Terminal cPanel
# ============================================

set -e

echo "========================================"
echo "🚀 Installation Evaluation ISI"
echo "========================================"

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Vérifier qu'on est dans le bon dossier
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Erreur: artisan non trouvé. Exécutez ce script depuis le dossier Laravel.${NC}"
    exit 1
fi

echo -e "${YELLOW}📁 Dossier actuel: $(pwd)${NC}"

# ============================================
# 1. Fichier .env
# ============================================
echo ""
echo "1️⃣  Configuration .env"

if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo -e "${GREEN}   ✓ .env créé depuis .env.example${NC}"
        echo -e "${YELLOW}   ⚠️  IMPORTANT: Modifiez .env avec vos paramètres de BDD !${NC}"
    else
        echo -e "${RED}   ❌ .env.example non trouvé${NC}"
        exit 1
    fi
else
    echo -e "${GREEN}   ✓ .env existe déjà${NC}"
fi

# ============================================
# 2. Dépendances Composer
# ============================================
echo ""
echo "2️⃣  Installation des dépendances"

if [ -d "vendor" ]; then
    echo -e "${GREEN}   ✓ vendor/ existe - skip composer install${NC}"
else
    echo "   Installation de Composer..."
    composer install --no-dev --optimize-autoloader --no-interaction
    echo -e "${GREEN}   ✓ Dépendances installées${NC}"
fi

# ============================================
# 3. Clé d'application
# ============================================
echo ""
echo "3️⃣  Clé d'application"

if grep -q "APP_KEY=base64:" .env; then
    echo -e "${GREEN}   ✓ APP_KEY déjà configurée${NC}"
else
    php artisan key:generate --force
    echo -e "${GREEN}   ✓ APP_KEY générée${NC}"
fi

# ============================================
# 4. Dossiers de stockage
# ============================================
echo ""
echo "4️⃣  Création des dossiers storage"

mkdir -p storage/framework/{cache/data,sessions,views}
mkdir -p storage/logs
mkdir -p storage/app/{public,temp}
mkdir -p bootstrap/cache

echo -e "${GREEN}   ✓ Dossiers créés${NC}"

# ============================================
# 5. Permissions
# ============================================
echo ""
echo "5️⃣  Permissions"

chmod -R 775 storage
chmod -R 775 bootstrap/cache

echo -e "${GREEN}   ✓ Permissions appliquées${NC}"

# ============================================
# 6. Migrations (optionnel)
# ============================================
echo ""
echo "6️⃣  Migrations de base de données"
read -p "   Exécuter les migrations? (y/n): " run_migrations

if [ "$run_migrations" = "y" ] || [ "$run_migrations" = "Y" ]; then
    php artisan migrate --force
    echo -e "${GREEN}   ✓ Migrations exécutées${NC}"
else
    echo -e "${YELLOW}   ⏭️  Migrations ignorées${NC}"
fi

# ============================================
# 7. Storage link
# ============================================
echo ""
echo "7️⃣  Lien symbolique storage"

if [ -L "public/storage" ]; then
    echo -e "${GREEN}   ✓ Lien existe déjà${NC}"
else
    php artisan storage:link
    echo -e "${GREEN}   ✓ Lien créé${NC}"
fi

# ============================================
# 8. Cache optimization
# ============================================
echo ""
echo "8️⃣  Optimisation du cache"

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo -e "${GREEN}   ✓ Cache optimisé${NC}"

# ============================================
# 9. Créer Super Admin (optionnel)
# ============================================
echo ""
echo "9️⃣  Création Super Admin"
read -p "   Créer un super admin? (y/n): " create_admin

if [ "$create_admin" = "y" ] || [ "$create_admin" = "Y" ]; then
    read -p "   Email: " admin_email
    read -p "   Nom: " admin_name
    read -sp "   Mot de passe: " admin_password
    echo ""
    
    php artisan tinker --execute="
        \$user = App\Models\User::firstOrCreate(
            ['email' => '$admin_email'],
            [
                'name' => '$admin_name',
                'password' => bcrypt('$admin_password'),
                'is_super_admin' => true,
                'is_actif' => true
            ]
        );
        echo 'Super Admin créé: ' . \$user->email;
    "
    echo -e "${GREEN}   ✓ Super Admin créé${NC}"
else
    echo -e "${YELLOW}   ⏭️  Création admin ignorée${NC}"
fi

# ============================================
# Résumé
# ============================================
echo ""
echo "========================================"
echo -e "${GREEN}✅ INSTALLATION TERMINÉE${NC}"
echo "========================================"
echo ""
echo "📋 Prochaines étapes:"
echo "   1. Modifiez .env avec vos paramètres MySQL"
echo "   2. Vérifiez que public_html/evaluation/ pointe vers ce dossier"
echo "   3. Testez l'accès à votre site"
echo ""
echo "📁 Structure attendue:"
echo "   ~/evaluation-isi/          (ce dossier)"
echo "   ~/public_html/evaluation/  (index.php + assets)"
echo ""
