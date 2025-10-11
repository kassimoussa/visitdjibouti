# 🔧 Configuration phpMyAdmin sur Ubuntu

Ce guide vous aide à configurer l'accès à phpMyAdmin sur votre VM Ubuntu.

---

## ⚠️ Problème Composer à résoudre d'abord

Vous avez une erreur car le `composer.lock` n'est pas synchronisé avec `composer.json`.

### Solution sur la VM

```bash
# Se placer dans le répertoire du projet
cd /var/www/html/visitdjibouti

# Option 1: Mettre à jour uniquement le lock file (RAPIDE)
composer update --lock --no-scripts

# Option 2: Installer toutes les dépendances (RECOMMANDÉ)
composer install --optimize-autoloader

# Vérifier que laravel/socialite est installé
composer show laravel/socialite
```

---

## 🌐 Configuration Nginx pour phpMyAdmin

### Option 1: Accès via sous-répertoire (RECOMMANDÉ - Plus sécurisé)

Modifiez votre fichier `/etc/nginx/sites-available/visitdjibouti` et ajoutez cette section **avant le dernier `}`** :

```nginx
    # phpMyAdmin access
    location /phpmyadmin {
        alias /usr/share/phpmyadmin;
        index index.php;

        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $request_filename;
        }
    }
```

**Commandes d'activation :**
```bash
sudo nginx -t
sudo systemctl reload nginx
```

**Accès :** `http://votre-domaine.com/phpmyadmin` ou `http://VOTRE_IP/phpmyadmin`

---

### Option 2: Sous-domaine dédié

Créez un nouveau fichier : `/etc/nginx/sites-available/phpmyadmin`

```nginx
server {
    listen 80;
    server_name phpmyadmin.votre-domaine.com;  # Ou utilisez votre IP

    root /usr/share/phpmyadmin;
    index index.php;

    # Logs
    access_log /var/log/nginx/phpmyadmin-access.log;
    error_log /var/log/nginx/phpmyadmin-error.log;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~ /(libraries|setup|config) {
        deny all;
    }
}
```

**Commandes d'activation :**
```bash
sudo ln -s /etc/nginx/sites-available/phpmyadmin /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

**Accès :** `http://phpmyadmin.votre-domaine.com`

---

## 🔒 Sécurisation avec Authentification HTTP (FORTEMENT RECOMMANDÉ)

phpMyAdmin est une cible fréquente des attaques. Ajoutez une couche de sécurité supplémentaire :

```bash
# Installer apache2-utils si nécessaire
sudo apt install -y apache2-utils

# Créer un fichier de mots de passe
sudo htpasswd -c /etc/nginx/.htpasswd admin

# Vous serez invité à entrer un mot de passe
# Pour ajouter d'autres utilisateurs (sans -c) :
# sudo htpasswd /etc/nginx/.htpasswd autreuser
```

### Ajouter l'authentification dans Nginx

**Pour Option 1 (sous-répertoire)**, modifiez le bloc `location /phpmyadmin` :

```nginx
    location /phpmyadmin {
        alias /usr/share/phpmyadmin;
        index index.php;

        # Authentification HTTP
        auth_basic "Restricted Access - phpMyAdmin";
        auth_basic_user_file /etc/nginx/.htpasswd;

        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $request_filename;
        }
    }
```

**Pour Option 2 (sous-domaine)**, ajoutez dans le bloc `server` :

```nginx
    # Authentification HTTP
    auth_basic "Restricted Access - phpMyAdmin";
    auth_basic_user_file /etc/nginx/.htpasswd;
```

**Recharger Nginx :**
```bash
sudo nginx -t
sudo systemctl reload nginx
```

---

## 🎯 Vérifications

```bash
# Vérifier que phpMyAdmin est installé
ls -la /usr/share/phpmyadmin/

# Vérifier que PHP-FPM tourne
sudo systemctl status php8.3-fpm

# Vérifier la config Nginx
sudo nginx -t

# Voir les logs en cas de problème
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/visitdjibouti-error.log  # Si vous avez nommé ainsi
```

---

## 🆘 Dépannage

### Erreur 404 Not Found
```bash
# Vérifier l'emplacement de phpMyAdmin
dpkg -L phpmyadmin | grep "/usr/share"

# Si phpMyAdmin est ailleurs, ajustez le chemin dans la config
```

### Erreur 502 Bad Gateway
```bash
# Vérifier le socket PHP-FPM
ls -la /var/run/php/

# Ajuster le chemin dans la config Nginx si nécessaire :
# fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;  # Pour PHP 8.2
# fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;  # Pour PHP 8.3
```

### Permission Denied
```bash
# Corriger les permissions
sudo chown -R www-data:www-data /usr/share/phpmyadmin
sudo chmod -R 755 /usr/share/phpmyadmin
```

---

## 📱 URLs d'accès finales

| Méthode | URL | Sécurité |
|---------|-----|----------|
| **Sous-répertoire** | `http://votre-ip/phpmyadmin` | ⭐⭐⭐ (+ auth HTTP) |
| **Sous-domaine** | `http://phpmyadmin.votre-domaine.com` | ⭐⭐ (+ auth HTTP) |
| **Application principale** | `http://votre-domaine.com` | ✅ |

---

## 🔐 Bonnes Pratiques de Sécurité

1. ✅ **Toujours utiliser l'authentification HTTP** (htpasswd)
2. ✅ **Changer le chemin d'accès** : `/phpmyadmin` → `/admin-db-2024` (plus obscur)
3. ✅ **Limiter l'accès par IP** si possible :
   ```nginx
   location /phpmyadmin {
       allow 123.123.123.123;  # Votre IP
       deny all;
       # ... reste de la config
   }
   ```
4. ✅ **Utiliser HTTPS** avec Let's Encrypt (Certbot)
5. ✅ **Désactiver root login** dans phpMyAdmin
6. ⚠️ **Ne PAS exposer phpMyAdmin en production** si non nécessaire

---

## 🚀 Commandes Complètes (Copier-Coller)

### Installation rapide Option 1 (sous-répertoire sécurisé)

```bash
# 1. Installer apache2-utils
sudo apt install -y apache2-utils

# 2. Créer l'authentification
sudo htpasswd -c /etc/nginx/.htpasswd admin

# 3. Éditer la config Nginx
sudo nano /etc/nginx/sites-available/visitdjibouti

# 4. Ajouter le bloc /phpmyadmin avec auth_basic (voir ci-dessus)

# 5. Tester et recharger
sudo nginx -t && sudo systemctl reload nginx

# 6. Accéder
# http://votre-ip/phpmyadmin
```

---

✅ **Accès phpMyAdmin configuré avec succès !**
