# Dossier Development

Ce dossier contient les composants Livewire qui ne sont **PAS utilisés en production** mais conservés pour le développement/test.

## Fichiers déplacés ici :

### 🔧 Tour Operators
- `tour-operator-manager.blade.php` - Ancienne version complexe des tour operators
- Remplacé par : `tour-operator/tour-operator-form.blade.php` et `tour-operator/tour-operator-list.blade.php`

### 🎨 Media Components
- `universal-media-selector.blade.php` - Sélecteur de média universel (test seulement)
- `media-selector-simple.blade.php` - Version simplifiée non utilisée
- `media-list.blade.php` - Liste de médias non utilisée en interface actuelle
- `media-selector-modal.blade.php` - Modal de sélection média (aucune référence trouvée en production)

## ⚠️ Attention

Ces fichiers ont été déplacés car ils ne sont **pas référencés dans les routes de production** ni utilisés dans l'interface admin principale.

Si vous avez besoin de les réactiver :
1. Déplacez le fichier vers le dossier parent
2. Corrigez le namespace PHP (enlevez `\_Development`)
3. Corrigez le chemin de la vue (enlevez `._development`)
4. Ajoutez les routes nécessaires

## 🧪 Tests

Ces composants peuvent être testés via les routes de test dans `ExampleController`.