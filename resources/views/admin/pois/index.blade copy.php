@extends('layouts.admin')

@section('title', 'Points d\'intérêt')

@section('page-title', 'Points d\'intérêt')

@section('styles')
<!-- Ajouter les styles Leaflet pour la carte -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    /* Style pour le conteneur de carte */
    #map-view {
        height: 600px;
        width: 100%;
        display: none;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    /* Style pour les popups de carte */
    .map-popup-content {
        max-width: 250px;
    }
    
    .map-popup-content img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 4px;
        margin-bottom: 8px;
    }
    
    .map-popup-title {
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .map-popup-category {
        display: inline-block;
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 10px;
        margin-bottom: 5px;
    }
    
    .map-popup-rating {
        font-size: 12px;
        margin-bottom: 5px;
    }
    
    .map-popup-actions {
        display: flex;
        gap: 5px;
        margin-top: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <h1 class="mb-0">Points d'intérêt</h1>
                    <a href="{{ route('pois.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Ajouter un POI
                    </a>
                </div>
                
                <!-- Filtres et sélecteur de vue -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <div class="d-flex gap-2 ">
                        <select class="form-select" style="max-width: 200px;">
                            <option>Toutes les catégories</option>
                            <option>Site naturel</option>
                            <option>Site culturel</option>
                            <option>Plage</option>
                            <option>Restaurant</option>
                            <option>Hébergement</option>
                        </select>
                        
                        <select class="form-select" style="max-width: 200px;">
                            <option>Tous les statuts</option>
                            <option>Publié</option>
                            <option>Brouillon</option>
                            <option>Archivé</option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-3">
                        <!-- Barre de recherche -->
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Rechercher...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        
                        <!-- Sélecteur de vue avec 3 options -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active" id="grid-view-btn" title="Vue en grille">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="list-view-btn" title="Vue en liste">
                                <i class="fas fa-list"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="map-view-btn" title="Vue carte">
                                <i class="fas fa-map-marked-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Vue en grille (Cards) -->
                <div id="grid-view" class="poi-grid">
                    <!-- POI 1 -->
                    <div class="poi-card">
                        <div class="poi-image">
                            <img src="https://via.placeholder.com/400x250?text=Lac+Assal" alt="Lac Assal">
                            <div class="featured-badge">En vedette</div>
                        </div>
                        <div class="poi-content">
                            <div class="badge bg-info mb-2">Site naturel</div>
                            <h3 class="poi-title">Lac Assal</h3>
                            <div class="mb-2 text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i> Centre, Djibouti
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                                <span class="ms-1">4.8 (58 avis)</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-success">Publié</span>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('pois.edit', 1) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('pois.show', 1) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal1">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- POI 2 -->
                    <div class="poi-card">
                        <div class="poi-image">
                            <img src="https://via.placeholder.com/400x250?text=Plage+des+Sables+Blancs" alt="Plage des Sables Blancs">
                        </div>
                        <div class="poi-content">
                            <div class="badge bg-primary mb-2">Plage</div>
                            <h3 class="poi-title">Plage des Sables Blancs</h3>
                            <div class="mb-2 text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i> Sud, Djibouti
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <span class="ms-1">5.0 (42 avis)</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-success">Publié</span>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('pois.edit', 2) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('pois.show', 2) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- POI 3 -->
                    <div class="poi-card">
                        <div class="poi-image">
                            <img src="https://via.placeholder.com/400x250?text=Forêt+de+Day" alt="Forêt de Day">
                        </div>
                        <div class="poi-content">
                            <div class="badge bg-info mb-2">Site naturel</div>
                            <h3 class="poi-title">Forêt de Day</h3>
                            <div class="mb-2 text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i> Nord, Djibouti
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="far fa-star text-warning"></i>
                                <span class="ms-1">4.0 (28 avis)</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-success">Publié</span>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('pois.edit', 3) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('pois.show', 3) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal3">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- POI 4 -->
                    <div class="poi-card">
                        <div class="poi-image">
                            <img src="https://via.placeholder.com/400x250?text=Îles+des+Moucha" alt="Îles des Moucha">
                        </div>
                        <div class="poi-content">
                            <div class="badge bg-info mb-2">Site naturel</div>
                            <h3 class="poi-title">Îles des Moucha</h3>
                            <div class="mb-2 text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i> Est, Djibouti
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                                <span class="ms-1">4.7 (35 avis)</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-info">Brouillon</span>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('pois.edit', 4) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('pois.show', 4) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal4">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- POI 5 -->
                    <div class="poi-card">
                        <div class="poi-image">
                            <img src="https://via.placeholder.com/400x250?text=Restaurant+La+Terrasse" alt="Restaurant La Terrasse">
                        </div>
                        <div class="poi-content">
                            <div class="badge bg-warning text-dark mb-2">Restaurant</div>
                            <h3 class="poi-title">Restaurant La Terrasse</h3>
                            <div class="mb-2 text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i> Centre, Djibouti
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="far fa-star text-warning"></i>
                                <span class="ms-1">4.2 (19 avis)</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-secondary">Archivé</span>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('pois.edit', 5) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('pois.show', 5) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal5">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- POI 6 -->
                    <div class="poi-card">
                        <div class="poi-image">
                            <img src="https://via.placeholder.com/400x250?text=Hôtel+Kempinski" alt="Hôtel Kempinski">
                        </div>
                        <div class="poi-content">
                            <div class="badge bg-danger mb-2">Hébergement</div>
                            <h3 class="poi-title">Hôtel Kempinski</h3>
                            <div class="mb-2 text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i> Centre, Djibouti
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <span class="ms-1">4.9 (87 avis)</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-success">Publié</span>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('pois.edit', 6) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('pois.show', 6) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal6">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vue en tableau (Liste) -->
                <div id="list-view" class="table-responsive" style="display: none;">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Localisation</th>
                                <th>Note</th>
                                <th>Statut</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- POI 1 -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 40px; height: 40px; overflow: hidden; border-radius: 4px;">
                                            <img src="https://via.placeholder.com/40x40?text=LA" alt="Lac Assal" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">Lac Assal</div>
                                    <div class="text-muted small">
                                        <i class="fas fa-star-of-life text-primary"></i> En vedette
                                    </div>
                                </td>
                                <td>Site naturel</td>
                                <td>Centre, Djibouti</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <span>4.8 (58)</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">Publié</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('pois.edit', 1) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('pois.show', 1) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- POI 2 -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 40px; height: 40px; overflow: hidden; border-radius: 4px;">
                                            <img src="https://via.placeholder.com/40x40?text=PL" alt="Plage des Sables Blancs" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">Plage des Sables Blancs</div>
                                </td>
                                <td>Plage</td>
                                <td>Sud, Djibouti</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <span>5.0 (42)</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">Publié</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('pois.edit', 2) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('pois.show', 2) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal2">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- POI 3 -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 40px; height: 40px; overflow: hidden; border-radius: 4px;">
                                            <img src="https://via.placeholder.com/40x40?text=FD" alt="Forêt de Day" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">Forêt de Day</div>
                                </td>
                                <td>Site naturel</td>
                                <td>Nord, Djibouti</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <span>4.0 (28)</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">Publié</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('pois.edit', 3) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('pois.show', 3) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal3">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- POI 4 -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 40px; height: 40px; overflow: hidden; border-radius: 4px;">
                                            <img src="https://via.placeholder.com/40x40?text=IM" alt="Îles des Moucha" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">Îles des Moucha</div>
                                </td>
                                <td>Site naturel</td>
                                <td>Est, Djibouti</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <span>4.7 (35)</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-info">Brouillon</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('pois.edit', 4) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('pois.show', 4) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal4">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- POI 5 -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 40px; height: 40px; overflow: hidden; border-radius: 4px;">
                                            <img src="https://via.placeholder.com/40x40?text=RT" alt="Restaurant La Terrasse" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">Restaurant La Terrasse</div>
                                </td>
                                <td>Restaurant</td>
                                <td>Centre, Djibouti</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <span>4.2 (19)</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary">Archivé</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('pois.edit', 5) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('pois.show', 5) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal5">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- POI 6 -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 40px; height: 40px; overflow: hidden; border-radius: 4px;">
                                            <img src="https://via.placeholder.com/40x40?text=HK" alt="Hôtel Kempinski" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">Hôtel Kempinski</div>
                                </td>
                                <td>Hébergement</td>
                                <td>Centre, Djibouti</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <span>4.9 (87)</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">Publié</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('pois.edit', 6) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('pois.show', 6) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal6">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Vue en carte (Map) -->
                <div id="map-view"></div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">Affichage de 1 à 6 sur 15 points d'intérêt</div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Précédent</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Suivant</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals de confirmation de suppression -->
@for ($i = 1; $i <= 6; $i++)
<div class="modal fade" id="deleteModal{{ $i }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $i }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $i }}">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce point d'intérêt ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('pois.index') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endfor

<!-- Script pour la carte et la bascule de vue, placé directement dans la page -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Éléments DOM
        const gridViewBtn = document.getElementById('grid-view-btn');
        const listViewBtn = document.getElementById('list-view-btn');
        const mapViewBtn = document.getElementById('map-view-btn');
        const gridView = document.getElementById('grid-view');
        const listView = document.getElementById('list-view');
        const mapView = document.getElementById('map-view');
        
        // Variable pour la carte Leaflet
        let map = null;
        
        // Données des POIs pour la carte - Utilisant les mêmes POIs que les autres vues
        const pois = [
            {
                id: 1,
                name: "Lac Assal",
                category: "Site naturel",
                categoryColor: "#0ea5e9",
                location: "Centre, Djibouti",
                lat: 11.6506,  // Latitude réelle du Lac Assal
                lng: 42.4097,  // Longitude réelle du Lac Assal
                rating: 4.8,
                reviews: 58,
                status: "Publié",
                statusClass: "bg-success",
                image: "https://via.placeholder.com/400x250?text=Lac+Assal",
                featured: true
            },
            {
                id: 2,
                name: "Plage des Sables Blancs",
                category: "Plage",
                categoryColor: "#3860f8",
                location: "Sud, Djibouti",
                lat: 11.5406,  // Coordonnées approximatives
                lng: 42.6697,
                rating: 5.0,
                reviews: 42,
                status: "Publié",
                statusClass: "bg-success",
                image: "https://via.placeholder.com/400x250?text=Plage+des+Sables+Blancs",
                featured: false
            },
            {
                id: 3,
                name: "Forêt de Day",
                category: "Site naturel",
                categoryColor: "#0ea5e9",
                location: "Nord, Djibouti",
                lat: 11.7806,  // Coordonnées approximatives
                lng: 42.6397,
                rating: 4.0,
                reviews: 28,
                status: "Publié",
                statusClass: "bg-success",
                image: "https://via.placeholder.com/400x250?text=Forêt+de+Day",
                featured: false
            },
            {
                id: 4,
                name: "Îles des Moucha",
                category: "Site naturel",
                categoryColor: "#0ea5e9",
                location: "Est, Djibouti",
                lat: 11.7106,  // Coordonnées approximatives
                lng: 43.2097,
                rating: 4.7,
                reviews: 35,
                status: "Brouillon",
                statusClass: "bg-info",
                image: "https://via.placeholder.com/400x250?text=Îles+des+Moucha",
                featured: false
            },
            {
                id: 5,
                name: "Restaurant La Terrasse",
                category: "Restaurant",
                categoryColor: "#f97316",
                location: "Centre, Djibouti",
                lat: 11.5906,  // Coordonnées approximatives
                lng: 43.1497,
                rating: 4.2,
                reviews: 19,
                status: "Archivé",
                statusClass: "bg-secondary",
                image: "https://via.placeholder.com/400x250?text=Restaurant+La+Terrasse",
                featured: false
            },
            {
                id: 6,
                name: "Hôtel Kempinski",
                category: "Hébergement",
                categoryColor: "#ef4444",
                location: "Centre, Djibouti",
                lat: 11.6006,  // Coordonnées approximatives
                lng: 43.1397,
                rating: 4.9,
                reviews: 87,
                status: "Publié",
                statusClass: "bg-success",
                image: "https://via.placeholder.com/400x250?text=Hôtel+Kempinski",
                featured: false
            }
        ];
        
        // Fonction d'initialisation de la carte
        function initMap() {
            if (map === null) {
                // Centrer la carte sur Djibouti
                map = L.map('map-view').setView([11.6506, 42.4097], 9);
                
                // Ajouter le fond de carte OpenStreetMap
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                
                // Ajouter les marqueurs pour chaque POI
                pois.forEach(poi => {
                    const marker = L.marker([poi.lat, poi.lng]).addTo(map);
                    
                    // Créer le contenu de la popup
                    const popupContent = `
                        <div class="map-popup-content">
                            <img src="${poi.image}" alt="${poi.name}">
                            <div class="map-popup-title">${poi.name}</div>
                            <div class="map-popup-category badge" style="background-color: ${poi.categoryColor}; color: white;">${poi.category}</div>
                            <div class="map-popup-rating">
                                <i class="fas fa-star text-warning"></i> ${poi.rating} (${poi.reviews} avis)
                            </div>
                            <div>${poi.location}</div>
                            <div class="badge ${poi.statusClass}">${poi.status}</div>
                            <div class="map-popup-actions">
                                <a href="{{ route('pois.edit', '') }}/${poi.id}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('pois.show', '') }}/${poi.id}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal${poi.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    
                    marker.bindPopup(popupContent);
                    
                    // Personnaliser le marqueur pour les POI en vedette
                    if (poi.featured) {
                        marker.setIcon(L.divIcon({
                            html: '<i class="fas fa-star" style="color: gold; font-size: 24px;"></i>',
                            className: 'featured-marker',
                            iconSize: [24, 24],
                            iconAnchor: [12, 12]
                        }));
                    }
                });
                
                // Force la mise à jour de la carte après affichage
                setTimeout(() => {
                    map.invalidateSize();
                }, 100);
            }
        }
        
        // Fonction pour basculer vers la vue en grille
        function showGridView() {
            gridView.style.display = 'grid';
            listView.style.display = 'none';
            mapView.style.display = 'none';
            
            gridViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');
            mapViewBtn.classList.remove('active');
            
            localStorage.setItem('poiViewPreference', 'grid');
        }
        
        // Fonction pour basculer vers la vue en liste
        function showListView() {
            gridView.style.display = 'none';
            listView.style.display = 'block';
            mapView.style.display = 'none';
            
            gridViewBtn.classList.remove('active');
            listViewBtn.classList.add('active');
            mapViewBtn.classList.remove('active');
            
            localStorage.setItem('poiViewPreference', 'list');
        }
        
        // Fonction pour basculer vers la vue en carte
        function showMapView() {
            gridView.style.display = 'none';
            listView.style.display = 'none';
            mapView.style.display = 'block';
            
            gridViewBtn.classList.remove('active');
            listViewBtn.classList.remove('active');
            mapViewBtn.classList.add('active');
            
            localStorage.setItem('poiViewPreference', 'map');
            
            // Petite temporisation avant d'initialiser la carte
            setTimeout(() => {
                // Initialiser la carte si ce n'est pas déjà fait
                initMap();
                
                // Forcer la mise à jour de la taille de la carte
                if (map) {
                    map.invalidateSize();
                }
            }, 50);
        }
        
        // Ajouter les événements de clic
        gridViewBtn.addEventListener('click', showGridView);
        listViewBtn.addEventListener('click', showListView);
        mapViewBtn.addEventListener('click', showMapView);
        
        // Vérifier s'il y a une préférence enregistrée
        const savedPreference = localStorage.getItem('poiViewPreference');
        if (savedPreference === 'list') {
            showListView();
        } else if (savedPreference === 'map') {
            showMapView();
        } else {
            showGridView(); // Par défaut ou si savedPreference est 'grid'
        }
    });
</script>
@endsection