<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerie d'icônes - Visit Djibouti</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Multi-Provider Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css">
    
    @livewireStyles
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .container-fluid {
            padding: 0;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        @livewire('admin.icon-gallery')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
    
    <script>
        // Fonction de copie d'icône
        function copyIconCode(iconCode) {
            // Créer un élément textarea temporaire
            const textarea = document.createElement('textarea');
            textarea.value = iconCode;
            document.body.appendChild(textarea);
            
            // Sélectionner et copier le texte
            textarea.select();
            textarea.setSelectionRange(0, 99999); // Pour les appareils mobiles
            
            try {
                document.execCommand('copy');
                showCopyNotification(iconCode);
            } catch (err) {
                // Fallback pour les navigateurs modernes
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(iconCode).then(function() {
                        showCopyNotification(iconCode);
                    }).catch(function(err) {
                        console.error('Erreur de copie:', err);
                    });
                }
            }
            
            // Nettoyer
            document.body.removeChild(textarea);
        }
        
        // Afficher une notification de copie
        function showCopyNotification(iconCode) {
            // Supprimer toute notification existante
            const existingNotification = document.querySelector('.copy-notification');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // Créer la notification
            const notification = document.createElement('div');
            notification.className = 'copy-notification';
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    <span><strong>${iconCode}</strong> copié dans le presse-papiers</span>
                </div>
            `;
            
            // Ajouter au DOM
            document.body.appendChild(notification);
            
            // Animation d'apparition
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            // Suppression automatique après 2 secondes
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 2000);
        }
        
        // Gestion des raccourcis clavier
        document.addEventListener('keydown', function(e) {
            // Escape pour fermer
            if (e.key === 'Escape') {
                window.close();
            }
            
            // Ctrl+F pour focus sur la recherche
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                document.querySelector('input[placeholder*="Rechercher"]')?.focus();
            }
        });
        
        // Auto-focus sur le champ de recherche au chargement
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.querySelector('input[placeholder*="Rechercher"]')?.focus();
            }, 500);
        });
    </script>
    
    <style>
        /* Notification de copie */
        .copy-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            max-width: 300px;
        }
        
        .copy-notification.show {
            opacity: 1;
            transform: translateX(0);
        }
        
        .copy-notification:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }
    </style>
</body>
</html>