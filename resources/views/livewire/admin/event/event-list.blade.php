<div>
    <div class="container-fluid">
        <!-- En-tête avec bouton d'ajout et basculement de vue -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <h1 class="h3 mb-0 me-3">Liste des événements</h1>
                <div class="btn-group" role="group">
                    <button type="button" class="btn {{ $view === 'list' ? 'btn-primary' : 'btn-outline-primary' }}"
                        wire:click="toggleView('list')">
                        <i class="fas fa-list me-1"></i> Liste
                    </button>
                    <button type="button" class="btn {{ $view === 'calendar' ? 'btn-primary' : 'btn-outline-primary' }}"
                        wire:click="toggleView('calendar')">
                        <i class="fas fa-calendar-alt me-1"></i> Calendrier
                    </button>
                </div>
            </div>
            <a href="{{ route('events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Ajouter un nouvel événement
            </a>
        </div>

        <!-- Filtres -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="search" class="form-label">Recherche</label>
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control"
                            id="search" placeholder="Rechercher...">
                    </div>

                    <div class="col-md-2">
                        <label for="parentCategory" class="form-label">Catégorie principale</label>
                        <select wire:model.live="parentCategory" id="parentCategory" class="form-select">
                            <option value="">Toutes les catégories</option>
                            @foreach ($parentCategories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->translation($currentLocale)?->name ?? $category->translation('fr')?->name ?? 'Sans nom' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="subcategory" class="form-label">Sous-catégorie</label>
                        <select wire:model.live="subcategory" id="subcategory" class="form-select" 
                                {{ empty($parentCategory) ? 'disabled' : '' }}>
                            <option value="">Toutes les sous-catégories</option>
                            @foreach ($subcategories as $subcat)
                                <option value="{{ $subcat->id }}">
                                    {{ $subcat->translation($currentLocale)?->name ?? $subcat->translation('fr')?->name ?? 'Sans nom' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="status" class="form-label">Statut</label>
                        <select wire:model.live="status" id="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="published">Publié</option>
                            <option value="draft">Brouillon</option>
                            <option value="archived">Archivé</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="dateFilter" class="form-label">Période</label>
                        <select wire:model.live="dateFilter" id="dateFilter" class="form-select">
                            @foreach ($dateFilters as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="locale" class="form-label">Langue</label>
                        <select wire:model.live="currentLocale" id="locale" class="form-select">
                            @foreach ($availableLocales as $locale)
                                <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue liste -->
        @if ($view === 'list')
            <!-- Tableau des événements -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Titre</th>
                                    <th>Dates</th>
                                    <th>Lieu</th>
                                    <th>Statut</th>
                                    <th>Participants</th>
                                    <th width="180">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $event)
                                    <tr>
                                        <td>{{ $event->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="event-image me-3"
                                                    style="width:40px; height:40px; border-radius:6px; overflow:hidden; background-color:#e2e8f0; display:flex; align-items:center; justify-content:center">
                                                    @if ($event->featuredImage)
                                                        <img src="{{ asset($event->featuredImage->path) }}"
                                                            alt="{{ $event->translation($currentLocale)->title ?? '' }}" class="img-fluid"
                                                            style="width:100%; height:100%; object-fit:cover">
                                                    @else
                                                        <i class="fas fa-calendar-alt text-muted"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $event->translation($currentLocale)->title ?? '' }}</div>
                                                    @if ($event->is_featured)
                                                        <span class="badge bg-info">À la une</span>
                                                    @endif
                                                    @if ($event->organizer)
                                                        <div class="text-muted small">{{ $event->organizer }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                @if ($event->start_date->isSameDay($event->end_date))
                                                    <div>{{ $event->start_date->format('d/m/Y') }}</div>
                                                @else
                                                    <div>{{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}</div>
                                                @endif
                                                @if ($event->start_time)
                                                    <div class="text-muted">{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if ($event->location)
                                                <div class="small">{{ $event->location }}</div>
                                                @if ($event->translation($currentLocale)->location_details)
                                                    <div class="text-muted small">{{ $event->translation($currentLocale)->location_details }}</div>
                                                @endif
                                            @else
                                                <span class="text-muted">Non spécifié</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $now = now();
                                                $today = $now->toDateString();
                                            @endphp
                                            
                                            <!-- Statut de publication -->
                                            @if ($event->status === 'published')
                                                <span class="badge bg-success mb-1">Publié</span>
                                            @elseif($event->status === 'draft')
                                                <span class="badge bg-warning mb-1">Brouillon</span>
                                            @else
                                                <span class="badge bg-secondary mb-1">Archivé</span>
                                            @endif
                                            
                                            <!-- Statut temporel -->
                                            <br>
                                            @if ($event->end_date < $today)
                                                <span class="badge bg-secondary">Terminé</span>
                                            @elseif ($event->start_date <= $today && $event->end_date >= $today)
                                                <span class="badge bg-success">En cours</span>
                                            @else
                                                <span class="badge bg-primary">À venir</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small">
                                                @if ($event->max_participants)
                                                    <div>{{ $event->current_participants ?? 0 }} / {{ $event->max_participants }}</div>
                                                    @if ($event->current_participants >= $event->max_participants)
                                                        <span class="badge bg-danger">Complet</span>
                                                    @else
                                                        <div class="text-muted">{{ $event->max_participants - ($event->current_participants ?? 0) }} places restantes</div>
                                                    @endif
                                                @else
                                                    <div>{{ $event->current_participants ?? 0 }}</div>
                                                    <div class="text-muted">Illimité</div>
                                                @endif
                                                
                                                @if ($event->price)
                                                    <div class="text-success small">{{ number_format($event->price, 0, ',', ' ') }} DJF</div>
                                                @else
                                                    <div class="text-success small">Gratuit</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('events.show', $event) }}"
                                                    class="btn btn-outline-secondary" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('events.edit', $event) }}"
                                                    class="btn btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" title="Supprimer"
                                                    wire:click="confirmDelete({{ $event->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">Aucun événement disponible</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $events->links() }}
            </div>
        @else
            <!-- Vue calendrier -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="calendar" style="height: 600px;" wire:ignore></div>
                </div>
            </div>

            <!-- Légende du calendrier -->
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Légende</h5>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <span class="badge bg-primary me-2">■</span> À venir
                        </div>
                        <div class="col-md-3 mb-2">
                            <span class="badge bg-success me-2">■</span> En cours
                        </div>
                        <div class="col-md-3 mb-2">
                            <span class="badge bg-secondary me-2">■</span> Terminé
                        </div>
                        <div class="col-md-3 mb-2">
                            <span class="badge bg-warning me-2">■</span> Brouillon
                        </div>
                    </div>
                    <div class="row mt-2">
                        @foreach ($parentCategories as $category)
                            <div class="col-md-3 mb-2">
                                <span class="badge rounded-pill"
                                    style="background-color: {{ $category->color ?? '#6c757d' }}">
                                    <i class="{{ $category->icon ?? 'fas fa-folder' }}"></i> 
                                    {{ $category->translation($currentLocale)?->name ?? $category->translation('fr')?->name ?? 'Sans nom' }}
                                </span>
                            </div>
                        @endforeach
                        @if($parentCategory && $subcategories->isNotEmpty())
                            @foreach ($subcategories as $subcat)
                                <div class="col-md-3 mb-2">
                                    <span class="badge rounded-pill"
                                        style="background-color: {{ $subcat->color ?? '#6c757d' }}">
                                        <i class="{{ $subcat->icon ?? 'fas fa-folder' }}"></i> 
                                        {{ $subcat->translation($currentLocale)?->name ?? $subcat->translation('fr')?->name ?? 'Sans nom' }}
                                    </span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal de confirmation de suppression -->
    @if ($deleteModalVisible)
        <div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer l'événement
                            <strong>"{{ $eventToDelete ? $eventToDelete->translation($currentLocale)->title : '' }}"</strong> ?</p>
                        <p class="text-danger">Cette action supprimera également toutes les inscriptions et avis liés à cet événement.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Annuler</button>
                        <button type="button" class="btn btn-danger" wire:click="delete">Supprimer</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('styles')
        <!-- FullCalendar CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.css" rel="stylesheet">
    @endpush

    @push('scripts')
        <!-- FullCalendar JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js"></script>
        
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('viewChanged', ({
                    viewMode
                }) => {
                    console.log('Vue active :', viewMode);
                    if (viewMode === 'calendar') {
                        setTimeout(() => {
                            initCalendar();
                        }, 300); // Attente pour que le calendrier s'affiche bien
                    }
                });
            });

            function initCalendar() {
                const events = @json($events->items());
                const currentLocale = @json($currentLocale);
                const calendarDiv = document.getElementById('calendar');
                
                // Nettoyer le div du calendrier
                calendarDiv.innerHTML = '';

                // Préparer les événements pour FullCalendar
                const calendarEvents = events.map(event => {
                    // Trouver la traduction pour la langue actuelle
                    const translation = event.translations.find(t => t.locale === currentLocale) 
                        || event.translations.find(t => t.locale === 'fr') 
                        || (event.translations.length > 0 ? event.translations[0] : null);
                    
                    const title = translation ? translation.title : event.slug;
                    
                    // Déterminer la couleur selon le statut
                    let backgroundColor = '#6c757d'; // gris par défaut
                    const today = new Date().toISOString().split('T')[0];
                    
                    if (event.status === 'draft') {
                        backgroundColor = '#ffc107'; // warning
                    } else if (event.end_date < today) {
                        backgroundColor = '#6c757d'; // secondary
                    } else if (event.start_date <= today && event.end_date >= today) {
                        backgroundColor = '#198754'; // success
                    } else {
                        backgroundColor = '#0d6efd'; // primary
                    }
                    
                    return {
                        id: event.id,
                        title: title,
                        start: event.start_date,
                        end: event.end_date,
                        backgroundColor: backgroundColor,
                        borderColor: backgroundColor,
                        extendedProps: {
                            description: translation ? translation.short_description : '',
                            location: event.location,
                            organizer: event.organizer,
                            price: event.price,
                            status: event.status
                        }
                    };
                });

                // Initialiser FullCalendar
                const calendar = new FullCalendar.Calendar(calendarDiv, {
                    initialView: 'dayGridMonth',
                    locale: currentLocale,
                    events: calendarEvents,
                    eventClick: function(info) {
                        // Rediriger vers la page de détail de l'événement
                        window.location.href = `/events/${info.event.id}`;
                    },
                    eventMouseEnter: function(info) {
                        // Afficher un tooltip au survol
                        const tooltip = document.createElement('div');
                        tooltip.className = 'tooltip bs-tooltip-top show';
                        tooltip.innerHTML = `
                            <div class="tooltip-arrow"></div>
                            <div class="tooltip-inner">
                                <strong>${info.event.title}</strong><br>
                                ${info.event.extendedProps.location ? info.event.extendedProps.location + '<br>' : ''}
                                ${info.event.extendedProps.organizer ? 'Par: ' + info.event.extendedProps.organizer + '<br>' : ''}
                                ${info.event.extendedProps.price ? info.event.extendedProps.price + ' DJF' : 'Gratuit'}
                            </div>
                        `;
                        
                        document.body.appendChild(tooltip);
                        
                        const rect = info.el.getBoundingClientRect();
                        tooltip.style.position = 'fixed';
                        tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
                        tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
                        tooltip.style.zIndex = '1070';
                        
                        info.el._tooltip = tooltip;
                    },
                    eventMouseLeave: function(info) {
                        if (info.el._tooltip) {
                            document.body.removeChild(info.el._tooltip);
                            delete info.el._tooltip;
                        }
                    },
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listWeek'
                    },
                    buttonText: {
                        today: 'Aujourd\'hui',
                        month: 'Mois',
                        week: 'Semaine',
                        list: 'Liste'
                    },
                    height: 'auto'
                });

                calendar.render();
            }
        </script>
    @endpush
</div>