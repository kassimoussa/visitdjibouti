# Guide de diagnostic et correction de l'upload

## 🔧 Problèmes potentiels identifiés et solutions

### 1. **Lien symbolique storage manquant**
```bash
php artisan storage:link
```

### 2. **Permissions des dossiers**
```bash
# Sur Linux/Mac
chmod -R 755 storage/
chmod -R 755 public/storage/

# Sur Windows (en tant qu'administrateur)
icacls storage /grant Users:F /T
icacls public\storage /grant Users:F /T
```

### 3. **Création des dossiers nécessaires**
```bash
mkdir -p storage/app/public/media/images
mkdir -p public/storage/media/images
```

### 4. **Vérification de la configuration Intervention Image**
Le code utilise Intervention Image v3. Vérifier dans `composer.json` :
```json
"intervention/image": "^3.11"
```

Si version 2.x, modifier le code de création de miniature :
```php
// Version 2.x
$img = Image::make($file->getPathname())
    ->fit(300, 300)
    ->save($thumbnailPath);
```

### 5. **Variables d'environnement importantes**
Vérifier dans `.env` :
```env
FILESYSTEM_DISK=local
APP_URL=http://localhost  # ou votre domaine
```

### 6. **Problèmes de validation Livewire**
Les règles de validation peuvent être trop strictes. Tester avec :
```php
protected $rules = [
    'uploadFiles.*' => 'file|max:10240',  // Plus permissif pour test
];
```

### 7. **Debugging avec les logs**
Vérifier les logs Laravel :
```bash
tail -f storage/logs/laravel.log
```

### 8. **Test manuel de l'upload**
Utiliser le bouton "Test config" ajouté dans l'interface pour vérifier :
- Lien symbolique storage
- Permissions d'écriture
- Existence des dossiers
- Configuration générale

## 🚨 Points de vérification prioritaires

1. **Cliquer sur "Test config"** dans le modal pour diagnostiquer
2. **Vérifier les messages d'erreur** dans les alertes du modal
3. **Consulter les logs** pour les erreurs détaillées
4. **Tester avec un fichier image simple** (JPEG < 1MB)

## 📋 Checklist de résolution

- [ ] `php artisan storage:link` exécuté
- [ ] Permissions des dossiers correctes
- [ ] Dossier `storage/app/public/media/images` existe
- [ ] Intervention Image installé et version compatible
- [ ] Test config dans le modal réussi
- [ ] Upload d'un fichier test fonctionnel

Une fois ces vérifications effectuées, l'upload devrait fonctionner correctement dans le modal.