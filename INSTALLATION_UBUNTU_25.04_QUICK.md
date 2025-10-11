# 🚀 Installation Rapide - Ubuntu 25.04 Plucky Puffin

> **Note importante**: Ubuntu 25.04 est une version de développement. Le PPA `ondrej/php` ne la supporte pas encore. Nous utilisons PHP 8.3 depuis les dépôts officiels Ubuntu.

## ⚡ Installation en Une Commande

```bash
# Copier-coller cette commande complète
sudo apt update && \
sudo apt install -y software-properties-common apt-transport-https ca-certificates curl wget gnupg lsb-release git unzip && \
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-common php8.3-mysql php8.3-xml php8.3-curl php8.3-gd php8.3-mbstring php8.3-zip php8.3-bcmath php8.3-intl php8.3-readline php8.3-tokenizer php8.3-imagick php8.3-soap php8.3-opcache && \
curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer && \
sudo apt install -y mysql-server mysql-client && \
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash - && sudo apt install -y nodejs && \
sudo apt install -y nginx && \
echo "✅ Installation terminée !"
```

---

## 🔧 Si vous avez déjà ajouté le PPA ondrej par erreur

**Symptôme**: Erreur `404 Not Found` avec `ppa.launchpadcontent.net/ondrej/php/ubuntu plucky`

**Solution**:
```bash
# 1. Supprimer le PPA ondrej
sudo add-apt-repository --remove ppa:ondrej/php -y

# 2. Nettoyer les fichiers
sudo rm -f /etc/apt/sources.list.d/ondrej-ubuntu-php-plucky.list*
sudo rm -f /etc/apt/sources.list.d/ondrej-ubuntu-php-plucky.sources

# 3. Mettre à jour les dépôts
sudo apt update

# 4. Installer PHP 8.3 depuis les dépôts Ubuntu officiels
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-common \
    php8.3-mysql php8.3-xml php8.3-curl php8.3-gd php8.3-mbstring \
    php8.3-zip php8.3-bcmath php8.3-intl php8.3-readline php8.3-tokenizer \
    php8.3-imagick php8.3-soap php8.3-opcache

# 5. Vérifier l'installation
php -v
```

---

## 📝 Étapes Manuelles Détaillées

### 1. Mise à jour du système
```bash
sudo apt update && sudo apt upgrade -y
```

### 2. Paquets de base
```bash
sudo apt install -y software-properties-common apt-transport-https ca-certificates \
    curl wget gnupg lsb-release git unzip
```

### 3. PHP 8.3 (depuis dépôts Ubuntu)
```bash
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-common \
    php8.3-mysql php8.3-xml php8.3-curl php8.3-gd php8.3-mbstring \
    php8.3-zip php8.3-bcmath php8.3-intl php8.3-readline php8.3-tokenizer \
    php8.3-imagick php8.3-soap php8.3-opcache
```

Vérifier:
```bash
php -v
# Doit afficher: PHP 8.3.x
```

### 4. Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

### 5. MySQL 8.0
```bash
sudo apt install -y mysql-server mysql-client
sudo mysql_secure_installation
```

### 6. Node.js 20.x
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
node -v && npm -v
```

### 7. Nginx
```bash
sudo apt install -y nginx
sudo systemctl status nginx
```

---

## 🎯 Configuration Nginx pour PHP 8.3

Fichier: `/etc/nginx/sites-available/visitdjibouti`

```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    root /var/www/visitdjibouti/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        # ⚠️ IMPORTANT: Utiliser php8.3-fpm pour Ubuntu 25.04
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;

        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Activer le site:
```bash
sudo ln -s /etc/nginx/sites-available/visitdjibouti /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx
```

---

## 🚀 Déploiement Laravel

```bash
# Cloner le projet
cd /var/www
sudo git clone votre-repo visitdjibouti

# Permissions
sudo chown -R $USER:www-data /var/www/html/visitdjibouti
cd /var/www/visitdjibouti

# Installer les dépendances
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Configuration
cp .env.example .env
nano .env  # Configurer les variables (DB, etc.)

php artisan key:generate
php artisan storage:link

# Permissions pour Laravel
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Migrations
php artisan migrate --force

# Cache pour production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## ✅ Vérifications

```bash
# Services actifs
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo systemctl status mysql

# Test accès
curl http://localhost

# Version PHP
php -v

# Extensions PHP chargées
php -m | grep -E '(mysql|curl|gd|mbstring|xml|zip)'
```

---

## 🆘 Dépannage

### Erreur 404 avec le PPA ondrej
```bash
sudo add-apt-repository --remove ppa:ondrej/php -y
sudo rm -f /etc/apt/sources.list.d/ondrej-ubuntu-php-plucky.list*
sudo apt update
```

### PHP-FPM ne démarre pas
```bash
sudo systemctl restart php8.3-fpm
sudo tail -f /var/log/php8.3-fpm.log
```

### Nginx erreur 502 Bad Gateway
```bash
# Vérifier que php8.3-fpm.sock existe
ls -la /var/run/php/php8.3-fpm.sock

# Vérifier la config Nginx
sudo nginx -t
```

---

## 📊 Versions Installées

- **OS**: Ubuntu 25.04 Plucky Puffin (développement)
- **PHP**: 8.3.x (dépôts officiels Ubuntu)
- **MySQL**: 8.0.x
- **Node.js**: 20.x (LTS)
- **Nginx**: 1.24+
- **Composer**: 2.x

---

## ⚠️ Recommandations

1. **Pour la production**, utilisez plutôt **Ubuntu 24.04 LTS** (supportée jusqu'en 2029)
2. Ubuntu 25.04 est une **version de développement** (support court terme)
3. PHP 8.3 est compatible avec Laravel 11 ✅
4. Le PPA ondrej/php supportera Ubuntu 25.04 après sa sortie stable (avril 2025)

---

## 🔗 Ressources

- [Guide complet INSTALLATION_UBUNTU.md](./INSTALLATION_UBUNTU.md)
- [Laravel 11 Documentation](https://laravel.com/docs/11.x)
- [PHP 8.3 Release Notes](https://www.php.net/releases/8.3/en.php)

---

✅ **Installation Ubuntu 25.04 terminée !**
