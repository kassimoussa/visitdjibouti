<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Icon Selector</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Multi-Provider Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/tabler-icons.min.css">
    
    @livewireStyles
</head>
<body>
    <div class="container mt-5">
        <h1>🎨 Test du Sélecteur d'Icônes Multi-Provider</h1>
        
        <div class="card">
            <div class="card-header">
                <h3>Test Direct du Composant</h3>
            </div>
            <div class="card-body">
                @livewire('admin.icon-selector', ['initialIcon' => 'fas fa-test'])
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h3>Test des Icons CSS</h3>
            </div>
            <div class="card-body">
                <p><strong>FontAwesome:</strong> <i class="fas fa-home"></i> <i class="fas fa-user"></i> <i class="fab fa-facebook"></i></p>
                <p><strong>Bootstrap Icons:</strong> <i class="bi bi-house"></i> <i class="bi bi-person"></i> <i class="bi bi-gear"></i></p>
                <p><strong>Flag Icons:</strong> <span class="fi fi-dj"></span> <span class="fi fi-fr"></span> <span class="fi fi-us"></span></p>
                <p><strong>Emojis:</strong> 🏛️ 🏖️ 🌋 🐪 🦩</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>