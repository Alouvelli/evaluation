
Write-Host "========================================"
Write-Host " Configuration Git & CI/CD" -ForegroundColor Cyan
Write-Host "========================================"

# Vérifier qu'on est dans le bon dossier
if (-not (Test-Path "artisan")) {
    Write-Host "Erreur: artisan non trouvé. Exécutez ce script depuis le dossier Laravel." -ForegroundColor Red
    exit 1
}

# ============================================
# 1. Créer .gitignore
# ============================================
Write-Host "  Création du .gitignore optimisé" -ForegroundColor Yellow

$gitignore = @"
# Laravel
/vendor
/node_modules
/.env
/.env.backup
/.env.production
/public/hot
/public/storage
/storage/*.key
Homestead.yaml
Homestead.json
/.vagrant

# Logs
/storage/logs/*.log
npm-debug.log
yarn-error.log

# Cache
/storage/framework/cache/data/*
/storage/framework/sessions/*
/storage/framework/views/*.php
/bootstrap/cache/*.php

# IDE
/.idea
/.vscode
*.swp
*.swo

# OS
.DS_Store
Thumbs.db

# Build
/public/build

# Tests
.phpunit.result.cache
.phpunit.cache

# Temp files
/storage/app/temp/*
*.zip
*.tar.gz
github-deploy-key*
"@

Set-Content -Path ".gitignore" -Value $gitignore
Write-Host "  .gitignore créé" -ForegroundColor Green

# ============================================
# 2. Créer le dossier workflows
# ============================================
Write-Host " Création des workflows GitHub Actions" -ForegroundColor Yellow

New-Item -ItemType Directory -Path ".github/workflows" -Force | Out-Null

# Copier le workflow (vous devez avoir le fichier deploy.yml)
# Si le fichier existe déjà, on le garde
Write-Host "   Dossier .github/workflows créé" -ForegroundColor Green

# ============================================
# 3. Créer/mettre à jour .env.example
# ============================================
Write-Host "  Mise à jour .env.example" -ForegroundColor Yellow

$envExample = @"
APP_NAME="ISI ENS_EVAL"
APP_ENV=local
APP_KEY=base64:RFRArAJXiZGm9WbrBhaaUE4/9hQtdmleV2l4EW6ctWc=
APP_DEBUG=true
APP_TIMEZONE=Africa/Dakar
APP_URL=http://localhost

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr

LOG_CHANNEL=stack
LOG_LEVEL=debug

# Database - MySQL pour production
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=teacher_eval_db
DB_USERNAME=root
DB_PASSWORD=

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=file
QUEUE_CONNECTION=sync

# Mail (Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=no-reply@groupeisi.com
MAIL_PASSWORD=axftghwrpeqstrga
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@groupeisi.com
MAIL_FROM_NAME="ISI ENS_EVAL"

# Sécurité
BCRYPT_ROUNDS=12
"@

Set-Content -Path ".env.example" -Value $envExample
Write-Host "   .env.example mis à jour" -ForegroundColor Green

# ============================================
# 4. Initialiser Git
# ============================================
Write-Host "Initialisation Git" -ForegroundColor Yellow

if (Test-Path ".git") {
    Write-Host "   Repository Git existe déjà" -ForegroundColor Green
} else {
    git init
    Write-Host "   Repository Git initialisé" -ForegroundColor Green
}

# ============================================
# 5. Premier commit
# ============================================
Write-Host " Premier commit" -ForegroundColor Yellow

git add .
git commit -m " Initial commit - Evaluation ISI v2" -m "Laravel 12, Multi-campus, Super Admin" 2>$null

if ($LASTEXITCODE -eq 0) {
    Write-Host "    Commit créé" -ForegroundColor Green
} else {
    Write-Host "     Rien à commiter ou déjà commité" -ForegroundColor Yellow
}

# ============================================
# 6. Configuration remote (optionnel)
# ============================================
Write-Host " Configuration GitHub" -ForegroundColor Yellow

$repoUrl = Read-Host "   URL du repository GitHub (ou Entrée pour skip)"

if ($repoUrl) {
    git remote remove origin 2>$null
    git remote add origin $repoUrl
    git branch -M main
    Write-Host "   Remote configuré: $repoUrl" -ForegroundColor Green

    Write-Host " Pour pousser le code:" -ForegroundColor Cyan
    Write-Host "      git push -u origin main" -ForegroundColor White
} else {
    Write-Host "   Configuration remote ignorée" -ForegroundColor Yellow
}

# ============================================
# Résumé
# ============================================
Write-Host "`n========================================"
Write-Host " CONFIGURATION TERMINÉE" -ForegroundColor Green
Write-Host "========================================`n"

Write-Host " Prochaines étapes:" -ForegroundColor Cyan
Write-Host "   1. Créez un repository sur GitHub"
Write-Host "   2. Ajoutez les secrets dans Settings > Secrets:"
Write-Host ""
Write-Host "   Pour déploiement FTP:" -ForegroundColor Yellow
Write-Host "      - FTP_HOST     : ftp.votredomaine.com"
Write-Host "      - FTP_USER     : votre_user_ftp"
Write-Host "      - FTP_PASSWORD : votre_mot_de_passe"
Write-Host "      - FTP_PATH     : /evaluation-isi/"
Write-Host ""
Write-Host "    Pour déploiement SSH:" -ForegroundColor Yellow
Write-Host "      - SSH_HOST       : votredomaine.com"
Write-Host "      - SSH_USER       : votre_user_cpanel"
Write-Host "      - SSH_PORT       : 22"
Write-Host "      - SSH_PRIVATE_KEY: contenu de github-deploy-key"
Write-Host "      - DEPLOY_PATH    : /home/username/evaluation-isi"
Write-Host ""
Write-Host "   3. Poussez le code: git push -u origin main"
Write-Host "   4. Le déploiement se lancera automatiquement!"
Write-Host ""
