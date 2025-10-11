# 🚀 Guide d'Installation - Visit Djibouti (Back-Office)
## Installation sur Ubuntu Server/VM

---

## 📋 Prérequis

- Ubuntu 22.04 LTS ou 24.04 LTS (recommandé pour production)
- **OU** Ubuntu 25.04 (Plucky Puffin) pour développement
- Accès root ou sudo
- Connexion Internet
- Au moins 2GB de RAM
- 10GB d'espace disque disponible

---

## 1️⃣ Mise à Jour du Système

```bash
sudo apt update && sudo apt upgrade -y
```

---

## 2️⃣ Installation des Paquets Système de Base

```bash
sudo apt install -y software-properties-common apt-transport-https ca-certificates \
    curl wget gnupg lsb-release git unzip
```

---

## 3️⃣ Installation de PHP 8.2 / 8.3

### Option A: Ubuntu 22.04 / 24.04 LTS (avec PPA ondrej)

```bash
# Ajouter le repository PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Installer PHP 8.2 et les extensions requises
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-xml php8.2-curl php8.2-gd php8.2-mbstring \
    php8.2-zip php8.2-bcmath php8.2-intl php8.2-readline php8.2-tokenizer \
    php8.2-imagick php8.2-soap php8.2-opcache
```

### Option B: Ubuntu 25.04 Plucky (dépôts officiels)

⚠️ **Le PPA ondrej/php ne supporte pas encore Ubuntu 25.04**

```bash
# Supprimer le PPA ondrej s'il a été ajouté par erreur
sudo add-apt-repository --remove ppa:ondrej/php -y 2>/dev/null || true
sudo rm -f /etc/apt/sources.list.d/ondrej-ubuntu-php-plucky.list*

# Mise à jour
sudo apt update

# Installer PHP 8.3 depuis les dépôts Ubuntu officiels
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-common \
    php8.3-mysql php8.3-xml php8.3-curl php8.3-gd php8.3-mbstring \
    php8.3-zip php8.3-bcmath php8.3-intl php8.3-readline php8.3-tokenizer \
    php8.3-imagick php8.3-soap php8.3-opcache
```

### Vérifier l'installation de PHP
```bash
php -v
# Doit afficher: PHP 8.2.x ou PHP 8.3.x
```

### Configuration PHP (optionnel mais recommandé)

**Pour PHP 8.2:**
```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

**Pour PHP 8.3:**
```bash
sudo nano /etc/php/8.3/fpm/php.ini
```

Modifier les valeurs suivantes:
```ini
memory_limit = 256M
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
```

Redémarrer PHP-FPM:

**Pour PHP 8.2:**
```bash
sudo systemctl restart php8.2-fpm
```

**Pour PHP 8.3:**
```bash
sudo systemctl restart php8.3-fpm
```

---

## 4️⃣ Installation de Composer

```bash
# Télécharger Composer
curl -sS https://getcomposer.org/installer | php

# Déplacer vers /usr/local/bin
sudo mv composer.phar /usr/local/bin/composer

# Vérifier l'installation
composer --version
# Doit afficher: Composer version 2.x.x
```

---

## 5️⃣ Installation de MySQL 8.0

### Installer MySQL Server
```bash
sudo apt install -y mysql-server mysql-client
```

### Sécuriser MySQL
```bash
sudo mysql_secure_installation
```

Répondre aux questions:
- Set root password: **YES** (choisir un mot de passe fort)
- Remove anonymous users: **YES**
- Disallow root login remotely: **YES**
- Remove test database: **YES**
- Reload privilege tables: **YES**

### Créer la base de données et l'utilisateur
```bash
sudo mysql -u root -p
```

Dans MySQL:
```sql
CREATE DATABASE visitdjibouti CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'djvi_user'@'localhost' IDENTIFIED BY 'VotreMotDePasseSecurise123!';

GRANT ALL PRIVILEGES ON visitdjibouti.* TO 'djvi_user'@'localhost';

FLUSH PRIVILEGES;

EXIT;
```

### Vérifier MySQL
```bash
sudo systemctl status mysql
# Doit être: active (running)
```

---

## 6️⃣ Installation de Node.js et NPM

### Installer Node.js 20.x (LTS)
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### Vérifier l'installation
```bash
node -v
# Doit afficher: v20.x.x

npm -v
# Doit afficher: 10.x.x
```

---

## 7️⃣ Installation de Nginx (Serveur Web)

### Installer Nginx
```bash
sudo apt install -y nginx
```

### Vérifier l'installation
```bash
sudo systemctl status nginx
# Doit être: active (running)
```

---

## 8️⃣ Cloner le Projet depuis Git

### Se placer dans le répertoire web
```bash
cd /var/www
```

### Cloner le repository (remplacer par votre URL Git)
```bash
sudo git clone https://github.com/votre-username/djvi.git visitdjibouti
```

### Ou si vous utilisez SSH:
```bash
sudo git clone git@github.com:votre-username/djvi.git visitdjibouti
```

### Donner les bonnes permissions
```bash
sudo chown -R $USER:www-data /var/www/visitdjibouti
sudo chmod -R 755 /var/www/visitdjibouti
```

### Se placer dans le répertoire du projet
```bash
cd /var/www/visitdjibouti
```

---

## 9️⃣ Configuration du Projet Laravel

### Installer les dépendances PHP
```bash
composer install --no-dev --optimize-autoloader
```

### Installer les dépendances Node.js
```bash
npm install
```

### Créer le fichier .env
```bash
cp .env.example .env
```

### Modifier le fichier .env
```bash
nano .env
```

Configurer les variables:
```env
APP_NAME="Visit Djibouti"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://votre-domaine.com

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=visitdjibouti
DB_USERNAME=djvi_user
DB_PASSWORD=VotreMotDePasseSecurise123!

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Configuration Mail (si nécessaire)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@visitdjibouti.dj"
MAIL_FROM_NAME="${APP_NAME}"

# OAuth (si configuré)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
```

### Générer la clé d'application
```bash
php artisan key:generate
```

### Créer les liens symboliques pour le storage
```bash
php artisan storage:link
```

### Configurer les permissions des répertoires
```bash
sudo chown -R www-data:www-data /var/www/visitdjibouti/storage
sudo chown -R www-data:www-data /var/www/visitdjibouti/bootstrap/cache

sudo chmod -R 775 /var/www/visitdjibouti/storage
sudo chmod -R 775 /var/www/visitdjibouti/bootstrap/cache
```

### Exécuter les migrations
```bash
php artisan migrate --force
```

### (Optionnel) Charger les données de démo
```bash
php artisan db:seed
```

### Compiler les assets (production)
```bash
npm run build
```

### Optimiser Laravel pour la production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## 🔟 Configuration de Nginx

### Créer le fichier de configuration du site
```bash
sudo nano /etc/nginx/sites-available/visitdjibouti
```

Coller cette configuration:
```nginx
server {
    listen 80;
    listen [::]:80;

    server_name votre-domaine.com www.votre-domaine.com;
    root /var/www/visitdjibouti/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Logs
    access_log /var/log/nginx/visitdjibouti-access.log;
    error_log /var/log/nginx/visitdjibouti-error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        # Pour PHP 8.2 (Ubuntu 22.04/24.04):
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;

        # Pour PHP 8.3 (Ubuntu 25.04), utiliser:
        # fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;

        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss application/rss+xml font/truetype font/opentype application/vnd.ms-fontobject image/svg+xml;

    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

### Activer le site
```bash
sudo ln -s /etc/nginx/sites-available/visitdjibouti /etc/nginx/sites-enabled/
```

### Désactiver le site par défaut
```bash
sudo rm /etc/nginx/sites-enabled/default
```

### Tester la configuration Nginx
```bash
sudo nginx -t
```

### Redémarrer Nginx
```bash
sudo systemctl restart nginx
```

---

## 1️⃣1️⃣ Configuration du Pare-feu (UFW)

```bash
# Activer UFW
sudo ufw enable

# Autoriser SSH (important!)
sudo ufw allow 22/tcp

# Autoriser HTTP et HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Vérifier le statut
sudo ufw status
```

---

## 1️⃣2️⃣ Configuration SSL avec Let's Encrypt (Optionnel mais Recommandé)

### Installer Certbot
```bash
sudo apt install -y certbot python3-certbot-nginx
```

### Obtenir le certificat SSL
```bash
sudo certbot --nginx -d votre-domaine.com -d www.votre-domaine.com
```

Suivre les instructions et choisir:
- Redirect: **2** (Redirect - faire une redirection automatique vers HTTPS)

### Renouvellement automatique
```bash
# Tester le renouvellement
sudo certbot renew --dry-run

# Le renouvellement automatique est déjà configuré via systemd
```

---

## 1️⃣3️⃣ Configuration du Queue Worker (Optionnel)

Si vous utilisez des queues pour les jobs asynchrones:

### Créer le service systemd
```bash
sudo nano /etc/systemd/system/visitdjibouti-worker.service
```

Contenu:
```ini
[Unit]
Description=Visit Djibouti Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
RestartSec=5s
ExecStart=/usr/bin/php /var/www/visitdjibouti/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

### Activer et démarrer le service
```bash
sudo systemctl daemon-reload
sudo systemctl enable visitdjibouti-worker
sudo systemctl start visitdjibouti-worker
```

### Vérifier le statut
```bash
sudo systemctl status visitdjibouti-worker
```

---

## 1️⃣4️⃣ Configuration du Scheduler (Cron)

Laravel utilise un scheduler pour les tâches planifiées:

```bash
# Éditer le crontab
sudo crontab -e -u www-data
```

Ajouter cette ligne:
```cron
* * * * * cd /var/www/visitdjibouti && php artisan schedule:run >> /dev/null 2>&1
```

---

## 1️⃣5️⃣ Créer le Premier Utilisateur Admin

```bash
cd /var/www/visitdjibouti

# Se connecter à MySQL
mysql -u djvi_user -p visitdjibouti
```

Dans MySQL:
```sql
INSERT INTO admin_users (name, email, password, created_at, updated_at)
VALUES (
    'Admin',
    'admin@visitdjibouti.dj',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: "password"
    NOW(),
    NOW()
);

EXIT;
```

**Important:** Changez le mot de passe après la première connexion!

Ou utiliser Tinker:
```bash
php artisan tinker
```

```php
$admin = new App\Models\AdminUser();
$admin->name = 'Admin';
$admin->email = 'admin@visitdjibouti.dj';
$admin->password = bcrypt('VotreMotDePasseSecurise123!');
$admin->save();
exit;
```

---

## 1️⃣6️⃣ Vérifications Finales

### Vérifier que tous les services sont actifs

**Pour PHP 8.2 (Ubuntu 22.04/24.04):**
```bash
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
```

**Pour PHP 8.3 (Ubuntu 25.04):**
```bash
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo systemctl status mysql
```

### Tester l'accès au site
```bash
curl http://localhost
# Ou dans un navigateur: http://votre-ip
```

### Vérifier les logs en cas de problème
```bash
# Logs Laravel
tail -f /var/www/visitdjibouti/storage/logs/laravel.log

# Logs Nginx
tail -f /var/log/nginx/visitdjibouti-error.log

# Logs PHP
tail -f /var/log/php8.2-fpm.log
```

---

## 📝 Commandes Utiles pour la Maintenance

### Mettre à jour le code depuis Git
```bash
cd /var/www/visitdjibouti
git pull origin main
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl restart php8.2-fpm
```

### Nettoyer les caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Voir les logs en temps réel
```bash
php artisan pail
```

### Backup de la base de données
```bash
mysqldump -u djvi_user -p visitdjibouti > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restaurer un backup
```bash
mysql -u djvi_user -p visitdjibouti < backup_20250110_120000.sql
```

---

## 🔧 Dépannage

### Erreur 500 Internal Server Error
```bash
# Vérifier les permissions
sudo chown -R www-data:www-data /var/www/visitdjibouti/storage
sudo chmod -R 775 /var/www/visitdjibouti/storage

# Vérifier les logs
tail -f /var/www/visitdjibouti/storage/logs/laravel.log
```

### Erreur "Storage not linked"
```bash
php artisan storage:link
```

### Erreur de connexion à la base de données
```bash
# Vérifier que MySQL est actif
sudo systemctl status mysql

# Tester la connexion
mysql -u djvi_user -p visitdjibouti
```

### Nginx ne démarre pas
```bash
# Tester la configuration
sudo nginx -t

# Voir les logs d'erreur
sudo tail -f /var/log/nginx/error.log
```

---

## 📊 Résumé des Paquets Installés

### Système
- software-properties-common
- apt-transport-https
- ca-certificates
- curl, wget
- gnupg, lsb-release
- git, unzip

### PHP 8.2 / 8.3 et Extensions
- php8.2 / php8.3, php-fpm, php-cli
- php-mysql
- php-xml, php-curl
- php-gd, php-mbstring
- php-zip, php-bcmath
- php-intl, php-readline
- php-tokenizer, php-imagick
- php-soap, php-opcache

### Base de données
- mysql-server
- mysql-client

### Node.js
- nodejs (v20.x)
- npm (v10.x)

### Serveur Web
- nginx

### Outils Laravel
- composer

### SSL (Optionnel)
- certbot
- python3-certbot-nginx

---

## 🎯 Accès à l'Application

- **URL Frontend:** `http://votre-domaine.com` ou `https://votre-domaine.com`
- **URL Admin:** `http://votre-domaine.com/dashboard`
- **Identifiants par défaut:**
  - Email: `admin@visitdjibouti.dj`
  - Password: `VotreMotDePasseSecurise123!` (à changer)

---

## 📞 Support

Pour toute question ou problème:
- Consulter la documentation Laravel: https://laravel.com/docs
- Vérifier les logs dans `/var/www/visitdjibouti/storage/logs/`
- Contacter l'administrateur système

---

**Installation réalisée le:** {{ date }}
**Version Laravel:** 11.x
**Version PHP:** 8.2 (Ubuntu 22.04/24.04) ou 8.3 (Ubuntu 25.04)
**Version MySQL:** 8.0
**Version Node.js:** 20.x

---

✅ **Installation terminée avec succès!**
