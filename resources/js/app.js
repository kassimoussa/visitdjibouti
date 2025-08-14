import './bootstrap';
import * as bootstrap from 'bootstrap';
import '../sass/app.scss';

// Rendre bootstrap disponible globalement
window.bootstrap = bootstrap;

// Vos tooltips existants
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
 

// Importer main.js APRÈS avoir défini window.bootstrap
import './main.js';

// Universal Media Selector Utilities
window.UMSUtils = {
    open: function(config = {}) {
        window.Livewire.dispatch('open-universal-media-selector', config);
    },
    onSelection: function(callback) {
        window.addEventListener('media-selected', callback);
    },
    getSelected: function() {
        return window.umsCurrentSelection || [];
    },
    clearSelection: function() {
        window.Livewire.dispatch('clear-universal-media-selector');
    }
};

 