<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class IconGallery extends Component
{
    public $activeTab = 'fontawesome';

    public $search = '';

    protected $iconCollections = [
        'fontawesome' => [
            'name' => 'FontAwesome',
            'prefix' => 'fas fa-',
            'icons' => [
                // Navigation & Interface
                'home', 'user', 'users', 'heart', 'star', 'star-half-alt', 'map-marker-alt',
                'folder', 'folder-open', 'file', 'file-alt', 'image', 'images', 'video',
                'music', 'phone', 'envelope', 'calendar', 'calendar-alt', 'clock',
                'shopping-cart', 'search', 'filter', 'sort', 'bars', 'list', 'th', 'th-large',

                // Actions
                'edit', 'pencil-alt', 'trash', 'trash-alt', 'save', 'download', 'upload',
                'print', 'share', 'share-alt', 'link', 'external-link-alt', 'copy', 'cut',
                'paste', 'undo', 'redo', 'sync', 'sync-alt', 'refresh',

                // Security & Settings
                'lock', 'unlock', 'key', 'shield', 'shield-alt', 'eye', 'eye-slash',
                'cog', 'cogs', 'wrench', 'tools', 'sliders-h', 'toggle-on', 'toggle-off',

                // Communication
                'bell', 'bell-slash', 'comment', 'comment-alt', 'comments', 'chat',
                'envelope-open', 'inbox', 'paper-plane', 'bullhorn', 'microphone',
                'microphone-slash', 'headphones', 'speaker',

                // Media & Entertainment
                'play', 'pause', 'stop', 'forward', 'backward', 'step-forward',
                'step-backward', 'volume-up', 'volume-down', 'volume-mute', 'camera',
                'video-camera', 'photo-video', 'film', 'tv', 'gamepad',

                // Transportation
                'car', 'car-alt', 'taxi', 'bus', 'truck', 'motorcycle', 'bicycle',
                'plane', 'helicopter', 'ship', 'anchor', 'train', 'subway', 'walking',
                'running', 'swimmer', 'horse', 'paw',

                // Nature & Weather
                'tree', 'leaf', 'seedling', 'flower', 'sun', 'moon', 'star', 'cloud',
                'cloud-rain', 'cloud-snow', 'bolt', 'rainbow', 'snowflake', 'fire',
                'fire-alt', 'water', 'mountain', 'volcano',

                // Buildings & Places
                'building', 'city', 'church', 'mosque', 'synagogue', 'hospital', 'school',
                'university', 'graduation-cap', 'store', 'store-alt', 'shopping-bag',
                'restaurant', 'utensils', 'coffee', 'wine-glass', 'beer', 'hotel', 'bed',
                'bank', 'landmark', 'monument', 'castle', 'fort-awesome',

                // Technology
                'laptop', 'desktop', 'mobile', 'mobile-alt', 'tablet', 'tablet-alt',
                'keyboard', 'mouse', 'wifi', 'signal', 'bluetooth', 'usb', 'sd-card',
                'hdd', 'database', 'server', 'network-wired', 'router', 'satellite',

                // Business & Finance
                'briefcase', 'handshake', 'chart-line', 'chart-bar', 'chart-pie',
                'chart-area', 'analytics', 'coins', 'dollar-sign', 'euro-sign',
                'pound-sign', 'yen-sign', 'credit-card', 'wallet', 'piggy-bank',
                'receipt', 'calculator',

                // Health & Medical
                'heart-broken', 'heartbeat', 'medkit', 'pills', 'syringe', 'thermometer',
                'stethoscope', 'user-md', 'ambulance', 'wheelchair', 'band-aid',
                'dna', 'tooth', 'eye-dropper',

                // Food & Dining
                'apple-alt', 'carrot', 'cheese', 'hamburger', 'pizza-slice', 'hotdog',
                'ice-cream', 'cookie', 'birthday-cake', 'wine-bottle', 'cocktail',

                // Sports & Activities
                'futbol', 'basketball-ball', 'football-ball', 'baseball-ball', 'tennis-ball',
                'volleyball-ball', 'golf-ball', 'hockey-puck', 'table-tennis', 'bowling-ball',
                'skiing', 'snowboarding', 'swimming-pool', 'dumbbell', 'weight-hanging',

                // Arrows & Directions
                'arrow-up', 'arrow-down', 'arrow-left', 'arrow-right', 'arrow-circle-up',
                'arrow-circle-down', 'arrow-circle-left', 'arrow-circle-right', 'chevron-up',
                'chevron-down', 'chevron-left', 'chevron-right', 'angle-up', 'angle-down',
                'angle-left', 'angle-right', 'caret-up', 'caret-down', 'caret-left', 'caret-right',

                // Status & Indicators
                'check', 'check-circle', 'check-square', 'times', 'times-circle',
                'exclamation', 'exclamation-triangle', 'exclamation-circle', 'question',
                'question-circle', 'info', 'info-circle', 'plus', 'plus-circle', 'minus',
                'minus-circle', 'dot-circle', 'circle', 'square', 'bookmark', 'tag', 'tags',
            ],
        ],
        'bootstrap' => [
            'name' => 'Bootstrap Icons',
            'prefix' => 'bi-',
            'icons' => [
                // Navigation & Interface
                'house', 'house-door', 'house-fill', 'person', 'person-fill', 'people',
                'people-fill', 'heart', 'heart-fill', 'star', 'star-fill', 'star-half',
                'geo-alt', 'geo-alt-fill', 'folder', 'folder2', 'folder2-open',
                'file-earmark', 'file-earmark-text', 'file-earmark-image', 'image', 'images',

                // Media & Communication
                'camera-video', 'camera-video-fill', 'music-note-beamed', 'music-note-list',
                'telephone', 'telephone-fill', 'envelope', 'envelope-fill', 'envelope-open',
                'chat', 'chat-fill', 'chat-dots', 'chat-square', 'mic', 'mic-fill',
                'mic-mute', 'headphones', 'speaker', 'volume-up', 'volume-down', 'volume-mute',

                // Time & Calendar
                'calendar', 'calendar-event', 'calendar-date', 'calendar-check', 'clock',
                'clock-fill', 'stopwatch', 'alarm', 'hourglass', 'hourglass-split',

                // Shopping & Commerce
                'cart', 'cart-fill', 'cart-plus', 'cart-dash', 'bag', 'bag-fill',
                'basket', 'basket-fill', 'credit-card', 'wallet', 'wallet2', 'currency-dollar',
                'currency-euro', 'piggy-bank', 'piggy-bank-fill',

                // Actions & Tools
                'search', 'zoom-in', 'zoom-out', 'gear', 'gears', 'tools', 'wrench',
                'hammer', 'screwdriver', 'pencil', 'pencil-fill', 'pen', 'eraser',
                'trash', 'trash-fill', 'save', 'save-fill', 'download', 'upload',
                'share', 'share-fill', 'copy', 'clipboard', 'clipboard-check',

                // Security & Privacy
                'lock', 'lock-fill', 'unlock', 'unlock-fill', 'key', 'key-fill',
                'shield', 'shield-fill', 'shield-check', 'shield-exclamation',
                'eye', 'eye-fill', 'eye-slash', 'eye-slash-fill',

                // Communication & Social
                'bell', 'bell-fill', 'bell-slash', 'bell-slash-fill', 'bookmark',
                'bookmark-fill', 'tag', 'tag-fill', 'tags', 'tags-fill', 'flag', 'flag-fill',

                // Navigation & Maps
                'globe', 'globe2', 'map', 'compass', 'compass-fill', 'signpost',
                'signpost-fill', 'pin-map', 'pin-map-fill', 'geo', 'geo-fill',

                // Transportation
                'car-front', 'car-front-fill', 'truck', 'truck-front', 'bus-front',
                'taxi-front', 'bicycle', 'scooter', 'airplane', 'airplane-engines',
                'train-front', 'subway', 'ship', 'sailboat', 'speedboat2',

                // Nature & Weather
                'tree', 'tree-fill', 'flower1', 'flower2', 'flower3', 'sun', 'sun-fill',
                'moon', 'moon-fill', 'moon-stars', 'moon-stars-fill', 'cloud', 'cloud-fill',
                'cloud-rain', 'cloud-rain-fill', 'cloud-snow', 'cloud-snow-fill',
                'lightning', 'lightning-fill', 'rainbow', 'snow', 'snow2', 'snow3',

                // Buildings & Places
                'building', 'buildings', 'hospital', 'bank', 'bank2', 'shop', 'shop-window',
                'storefront', 'house-heart', 'house-gear', 'church', 'mosque', 'temple-hindu',
                'synagogue', 'hotel', 'bed', 'bed-fill',

                // Technology
                'laptop', 'laptop-fill', 'pc-display', 'pc-display-horizontal', 'tablet',
                'tablet-fill', 'phone', 'phone-fill', 'smartwatch', 'keyboard', 'keyboard-fill',
                'mouse', 'mouse-fill', 'wifi', 'wifi-off', 'bluetooth', 'usb-drive',
                'usb-drive-fill', 'hdd', 'hdd-fill', 'memory', 'cpu', 'gpu-card',

                // Business & Work
                'briefcase', 'briefcase-fill', 'diagram-2', 'diagram-3', 'bar-chart',
                'bar-chart-fill', 'pie-chart', 'pie-chart-fill', 'graph-up', 'graph-down',
                'calculator', 'calculator-fill', 'receipt', 'receipt-cutoff',

                // Health & Medical
                'heart-pulse', 'heart-pulse-fill', 'bandaid', 'bandaid-fill', 'thermometer',
                'thermometer-half', 'capsule', 'prescription', 'prescription2',
                'hospital-fill', 'ambulance', 'wheelchair',

                // Sports & Activities
                'trophy', 'trophy-fill', 'award', 'award-fill', 'controller',
                'dice-1', 'dice-2', 'dice-3', 'dice-4', 'dice-5', 'dice-6',

                // Arrows & Navigation
                'arrow-up', 'arrow-up-circle', 'arrow-up-circle-fill', 'arrow-down',
                'arrow-down-circle', 'arrow-down-circle-fill', 'arrow-left', 'arrow-left-circle',
                'arrow-left-circle-fill', 'arrow-right', 'arrow-right-circle',
                'arrow-right-circle-fill', 'chevron-up', 'chevron-down', 'chevron-left',
                'chevron-right', 'caret-up', 'caret-down', 'caret-left', 'caret-right',

                // Status & Feedback
                'check', 'check-circle', 'check-circle-fill', 'check-square', 'check-square-fill',
                'x', 'x-circle', 'x-circle-fill', 'x-square', 'x-square-fill', 'plus',
                'plus-circle', 'plus-circle-fill', 'plus-square', 'plus-square-fill', 'dash',
                'dash-circle', 'dash-circle-fill', 'dash-square', 'dash-square-fill',
                'question', 'question-circle', 'question-circle-fill', 'exclamation',
                'exclamation-triangle', 'exclamation-triangle-fill', 'exclamation-circle',
                'exclamation-circle-fill', 'info', 'info-circle', 'info-circle-fill',
            ],
        ],
        'phosphor' => [
            'name' => 'Phosphor Icons',
            'prefix' => 'ph ph-',
            'icons' => [
                // Interface & Navigation
                'house', 'house-simple', 'user', 'user-circle', 'users', 'users-three',
                'heart', 'star', 'map-pin', 'folder', 'folder-open', 'file', 'file-text',
                'image', 'images', 'video-camera', 'music-notes', 'microphone',

                // Communication
                'phone', 'phone-call', 'envelope', 'envelope-open', 'chat-circle',
                'chat-centered', 'chat-text', 'bell', 'bell-ringing', 'bell-slash',

                // Time & Calendar
                'calendar', 'calendar-blank', 'clock', 'timer', 'hourglass',

                // Commerce & Shopping
                'shopping-cart', 'shopping-bag', 'credit-card', 'wallet', 'coins',
                'currency-dollar', 'currency-eur', 'storefront', 'receipt',

                // Actions & Tools
                'magnifying-glass', 'gear', 'gears', 'wrench', 'hammer', 'screwdriver',
                'pencil', 'pencil-simple', 'pen', 'eraser', 'trash', 'download',
                'upload', 'printer', 'share-network', 'export', 'import', 'copy',
                'clipboard', 'scissors', 'paperclip',

                // Security & Privacy
                'lock', 'lock-open', 'key', 'shield', 'shield-check', 'shield-warning',
                'eye', 'eye-slash', 'fingerprint', 'password',

                // Social & Bookmarks
                'bookmark', 'bookmark-simple', 'tag', 'flag', 'flag-banner',
                'thumbs-up', 'thumbs-down', 'hand-heart', 'smiley',

                // Maps & Location
                'globe', 'globe-hemisphere-east', 'globe-hemisphere-west', 'map-trifold',
                'compass', 'navigation-arrow', 'signpost', 'road-horizon',

                // Transportation
                'car', 'car-simple', 'taxi', 'bus', 'truck', 'motorcycle', 'bicycle',
                'airplane', 'airplane-takeoff', 'train', 'train-simple', 'boat',
                'rocket', 'parachute', 'traffic-cone', 'gas-pump',

                // Nature & Weather
                'tree', 'plant', 'leaf', 'flower', 'cactus', 'sun', 'sun-horizon',
                'moon', 'moon-stars', 'cloud', 'cloud-rain', 'cloud-snow',
                'lightning', 'fire', 'drop', 'snowflake', 'thermometer',

                // Buildings & Places
                'buildings', 'house-line', 'office-chair', 'hospital', 'church',
                'bank', 'factory', 'warehouse', 'storefront', 'tent', 'lighthouse',

                // Technology & Devices
                'device-mobile', 'device-tablet', 'laptop', 'desktop', 'monitor',
                'keyboard', 'mouse', 'headphones', 'speaker-high', 'speaker-none',
                'television', 'radio', 'camera', 'video-camera', 'gamepad',
                'usb', 'hard-drive', 'floppy-disk', 'cd', 'wifi-high', 'wifi-none',
                'bluetooth', 'airplay', 'battery-full', 'battery-empty', 'plug',

                // Business & Finance
                'briefcase', 'briefcase-metal', 'handshake', 'presentation-chart',
                'chart-line', 'chart-bar', 'chart-pie', 'trend-up', 'trend-down',
                'calculator', 'bank', 'vault', 'seal-check',

                // Health & Medical
                'heart-beat', 'pill', 'syringe', 'thermometer', 'bandaids',
                'first-aid-kit', 'stethoscope', 'tooth', 'virus', 'bacteria',

                // Food & Dining
                'coffee', 'wine', 'beer-bottle', 'hamburger', 'pizza', 'ice-cream',
                'apple-logo', 'carrot', 'cooking-pot', 'fork-knife',

                // Sports & Games
                'soccer-ball', 'basketball', 'tennis-ball', 'baseball', 'football',
                'trophy', 'medal', 'target', 'dice-one', 'dice-two', 'dice-three',
                'cards', 'puzzle-piece',

                // Media & Entertainment
                'play', 'pause', 'stop', 'record', 'skip-back', 'skip-forward',
                'shuffle', 'repeat', 'volume-high', 'volume-low', 'volume-none',
                'film-strip', 'video', 'music-note', 'playlist',

                // Arrows & Directions
                'arrow-up', 'arrow-down', 'arrow-left', 'arrow-right', 'arrow-circle-up',
                'arrow-circle-down', 'arrow-circle-left', 'arrow-circle-right',
                'caret-up', 'caret-down', 'caret-left', 'caret-right',
                'chevron-up', 'chevron-down', 'chevron-left', 'chevron-right',

                // Status & Feedback
                'check', 'check-circle', 'check-square', 'x', 'x-circle', 'x-square',
                'plus', 'plus-circle', 'minus', 'minus-circle', 'question', 'info',
                'warning', 'warning-circle', 'prohibition', 'seal-check', 'seal-warning',
            ],
        ],
        'tabler' => [
            'name' => 'Tabler Icons',
            'prefix' => 'ti ti-',
            'icons' => [
                // Navigation & Interface
                'home', 'home-2', 'user', 'user-circle', 'users', 'heart', 'star',
                'star-filled', 'map-pin', 'map-2', 'folder', 'folder-open', 'file',
                'file-text', 'files', 'photo', 'video', 'music', 'microphone',

                // Communication
                'phone', 'phone-call', 'mail', 'mail-opened', 'message', 'message-circle',
                'message-2', 'bell', 'bell-ringing', 'bell-off', 'notification',

                // Time & Calendar
                'calendar', 'calendar-event', 'clock', 'clock-hour-3', 'alarm',
                'hourglass', 'time',

                // Commerce & Shopping
                'shopping-cart', 'shopping-bag', 'credit-card', 'wallet', 'coin',
                'currency-dollar', 'currency-euro', 'cash', 'receipt',

                // Actions & Tools
                'search', 'zoom-in', 'zoom-out', 'settings', 'adjustments', 'tool',
                'tools', 'hammer', 'screwdriver', 'wrench', 'edit', 'pencil', 'pen',
                'eraser', 'trash', 'trash-x', 'download', 'upload', 'printer',
                'share', 'share-2', 'copy', 'clipboard', 'cut', 'scissors',

                // Security & Privacy
                'lock', 'lock-open', 'key', 'shield', 'shield-check', 'shield-x',
                'eye', 'eye-off', 'fingerprint', 'password',

                // Social & Bookmarks
                'bookmark', 'tag', 'tags', 'flag', 'flag-2', 'thumbs-up', 'thumbs-down',
                'mood-happy', 'mood-sad',

                // Maps & Location
                'world', 'globe', 'map', 'compass', 'navigation', 'route', 'location',
                'pin', 'target',

                // Transportation
                'car', 'taxi', 'bus', 'truck', 'bike', 'motorbike', 'plane', 'helicopter',
                'train', 'ship', 'rocket', 'scooter', 'walk', 'run',

                // Nature & Weather
                'tree', 'leaf', 'flower', 'sun', 'moon', 'moon-stars', 'cloud',
                'cloud-rain', 'cloud-snow', 'bolt', 'snowflake', 'flame', 'drop',

                // Buildings & Places
                'building', 'building-bank', 'building-hospital', 'building-store',
                'home-2', 'office', 'school', 'church', 'castle', 'tent',

                // Technology & Devices
                'device-mobile', 'device-tablet', 'laptop', 'desktop', 'tv', 'monitor',
                'keyboard', 'mouse', 'headphones', 'speaker', 'microphone-2', 'camera',
                'video-camera', 'gamepad', 'usb', 'hard-drive', 'database', 'server',
                'wifi', 'wifi-off', 'bluetooth', 'battery', 'plug',

                // Business & Finance
                'briefcase', 'chart-line', 'chart-bar', 'chart-pie', 'chart-area',
                'trending-up', 'trending-down', 'calculator', 'presentation',

                // Health & Medical
                'heart-broken', 'heartbeat', 'pill', 'stethoscope', 'thermometer',
                'bandage', 'vaccine', 'first-aid-kit',

                // Food & Dining
                'coffee', 'cup', 'wine', 'beer', 'ice-cream', 'pizza', 'apple',
                'carrot', 'fish', 'meat',

                // Sports & Games
                'ball-football', 'ball-basketball', 'ball-tennis', 'trophy', 'medal',
                'target-arrow', 'dice', 'puzzle',

                // Media & Entertainment
                'play', 'pause', 'stop', 'skip-back', 'skip-forward', 'volume',
                'volume-off', 'music-2', 'headphones-2', 'tv-2',

                // Arrows & Directions
                'arrow-up', 'arrow-down', 'arrow-left', 'arrow-right', 'arrow-up-circle',
                'arrow-down-circle', 'arrow-left-circle', 'arrow-right-circle',
                'chevron-up', 'chevron-down', 'chevron-left', 'chevron-right',
                'caret-up', 'caret-down', 'caret-left', 'caret-right',

                // Status & Feedback
                'check', 'check-circle', 'x', 'x-circle', 'plus', 'plus-circle',
                'minus', 'minus-circle', 'help', 'info-circle', 'alert-triangle',
                'alert-circle', 'exclamation-mark', 'question-mark',

                // Brands & Social Media
                'brand-facebook', 'brand-twitter', 'brand-instagram', 'brand-youtube',
                'brand-github', 'brand-linkedin', 'brand-google', 'brand-apple',
                'brand-windows', 'brand-android',
            ],
        ],
        'flags' => [
            'name' => 'Flag Icons',
            'prefix' => 'fi fi-',
            'icons' => [
                // Europe
                'fr', 'gb', 'de', 'es', 'it', 'pt', 'nl', 'be', 'ch', 'at',
                'se', 'no', 'dk', 'fi', 'ie', 'is', 'pl', 'cz', 'hu', 'ro',
                'bg', 'hr', 'si', 'sk', 'ee', 'lv', 'lt', 'lu', 'mt', 'cy',
                'gr', 'tr', 'ru', 'ua', 'by', 'md', 'rs', 'ba', 'me', 'mk',
                'al', 'xk', 'ad', 'mc', 'sm', 'va', 'li', 'fo', 'im', 'je', 'gg',

                // Asia
                'cn', 'jp', 'kr', 'kp', 'in', 'id', 'th', 'vn', 'ph', 'my',
                'sg', 'bn', 'kh', 'la', 'mm', 'tw', 'hk', 'mo', 'mn', 'af',
                'pk', 'bd', 'lk', 'np', 'bt', 'mv', 'ir', 'iq', 'sy', 'lb',
                'jo', 'il', 'ps', 'sa', 'ae', 'om', 'ye', 'qa', 'bh', 'kw',
                'az', 'am', 'ge', 'kg', 'kz', 'tj', 'tm', 'uz', 'cy-tr',

                // Americas
                'us', 'ca', 'mx', 'gt', 'bz', 'sv', 'hn', 'ni', 'cr', 'pa',
                'br', 'ar', 'cl', 'co', 'pe', 'ec', 'bo', 've', 'uy', 'py',
                'gf', 'sr', 'gy', 'fk', 'cu', 'jm', 'ht', 'do', 'pr', 'bs',
                'bb', 'tt', 'gd', 'lc', 'vc', 'dm', 'ag', 'kn', 'aw', 'cw',

                // Africa
                'za', 'eg', 'ma', 'dz', 'tn', 'ly', 'sd', 'et', 'ke', 'tz',
                'ug', 'rw', 'bi', 'mw', 'zm', 'zw', 'bw', 'na', 'sz', 'ls',
                'mg', 'mu', 'sc', 'km', 'dj', 'so', 'er', 'cf', 'td', 'ne',
                'ng', 'gh', 'ci', 'bf', 'ml', 'sn', 'gm', 'gw', 'sl', 'lr',
                'ao', 'mz', 'zr', 'cg', 'cm', 'ga', 'gq', 'st', 'cv', 'mr',

                // Oceania
                'au', 'nz', 'pg', 'fj', 'vu', 'nc', 'pf', 'ck', 'ws', 'to',
                'tv', 'nr', 'ki', 'pw', 'fm', 'mh', 'sb', 'as', 'gu', 'mp',
            ],
        ],
        'emojis' => [
            'name' => 'Emojis Unicode',
            'prefix' => '',
            'icons' => [
                // Buildings & Places
                '🏛️', '🏠', '🏡', '🏢', '🏣', '🏤', '🏥', '🏦', '🏧', '🏨',
                '🏩', '🏪', '🏫', '🏬', '🏭', '🏮', '🏯', '🏰', '🗼', '🗽',
                '⛪', '🕌', '🛕', '🕍', '⛩️', '🕋', '⛲', '⛱️', '🏖️', '🏜️',
                '🏝️', '🏞️', '🏟️', '🏛️', '🏗️', '🏘️', '🏙️', '🏚️', '🏔️', '⛰️',

                // Transportation
                '🚗', '🚙', '🚐', '🚛', '🚜', '🏎️', '🚓', '🚔', '🚑', '🚒',
                '🚚', '🚲', '🛴', '🛵', '🏍️', '✈️', '🛩️', '🛫', '🛬', '🪂',
                '💺', '🚁', '🚟', '🚠', '🚡', '🛰️', '🚀', '🛸', '🚂', '🚃',
                '🚄', '🚅', '🚆', '🚇', '🚈', '🚉', '🚊', '🚝', '🚞', '🚋',
                '🚌', '🚍', '🚎', '🚐', '⛵', '🛥️', '🚤', '⛴️', '🛳️', '🚢',

                // Nature & Weather
                '🌍', '🌎', '🌏', '🌐', '🗺️', '🗾', '🧭', '🏔️', '⛰️', '🌋',
                '🗻', '🏕️', '🏖️', '🏜️', '🏝️', '🏞️', '☀️', '🌤️', '⛅', '🌥️',
                '☁️', '🌦️', '🌧️', '⛈️', '🌩️', '🌨️', '❄️', '☃️', '⛄', '🌬️',
                '💨', '🌪️', '🌫️', '🌈', '☂️', '☔', '⚡', '🔥', '💧', '🌊',
                '🌱', '🌿', '☘️', '🍀', '🌾', '🌵', '🌴', '🌲', '🌳', '🌰',
                '🥥', '🌼', '🌻', '🌺', '🌷', '🌹', '🥀', '🌸', '💐', '🌕',

                // Objects & Tools
                '📱', '📲', '💻', '🖥️', '🖨️', '⌨️', '🖱️', '🖲️', '💽', '💾',
                '💿', '📀', '📼', '📷', '📸', '📹', '🎥', '📞', '☎️', '📟',
                '📠', '📺', '📻', '🎙️', '🎚️', '🎛️', '🧭', '⏰', '⏲️', '⏱️',
                '🕐', '🕑', '🕒', '🕓', '🕔', '🕕', '🕖', '🕗', '🕘', '🕙',
                '🔋', '🔌', '💡', '🔦', '🕯️', '🪔', '🧯', '🛢️', '⚙️', '🔧',

                // Activities & Entertainment
                '⚽', '🏀', '🏈', '⚾', '🥎', '🎾', '🏐', '🏉', '🥏', '🎱',
                '🪀', '🏓', '🏸', '🏒', '🏑', '🥍', '🏏', '🪃', '🥅', '⛳',
                '🪁', '🏹', '🎣', '🤿', '🥽', '🥼', '🦺', '⛷️', '🏂', '🪂',
                '🏋️', '🤼', '🤸', '⛹️', '🤺', '🏇', '🧘', '🏄', '🏊', '🚣',
                '🧗', '🚵', '🚴', '🏃', '🚶', '🧎', '🏃‍♀️', '🏃‍♂️', '🚶‍♀️', '🚶‍♂️',

                // Food & Drinks
                '🍎', '🍐', '🍊', '🍋', '🍌', '🍉', '🍇', '🍓', '🫐', '🍈',
                '🍒', '🍑', '🥭', '🍍', '🥥', '🥝', '🍅', '🍆', '🥑', '🥦',
                '🥬', '🥒', '🌶️', '🫑', '🌽', '🥕', '🫒', '🧄', '🧅', '🥔',
                '🍠', '🥐', '🥖', '🍞', '🥨', '🥯', '🧀', '🥚', '🍳', '🧈',
                '🥞', '🧇', '🥓', '🥩', '🍗', '🍖', '🌭', '🍔', '🍟', '🍕',
                '🌮', '🌯', '🥙', '🧆', '🥘', '🍝', '🍜', '🍲', '🍛', '🍣',
                '🍤', '🍙', '🍚', '🍱', '🍘', '🍥', '🥠', '🥮', '🍢', '🍡',

                // Symbols & Icons
                '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔',
                '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '💟', '☮️',
                '✝️', '☪️', '🕉️', '☸️', '✡️', '🔯', '🕎', '☯️', '☦️', '🛐',
                '⭐', '🌟', '💫', '⚡', '💥', '💢', '💨', '💤', '💣', '💍',
                '💎', '🔔', '🔕', '🎵', '🎶', '💯', '💮', '💰', '💴', '💵',
                '💶', '💷', '💸', '💳', '🧾', '💹', '💱', '💲', '✉️', '📧',
            ],
        ],
    ];

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->search = '';
    }

    public function getFilteredIcons()
    {
        $collection = $this->iconCollections[$this->activeTab];
        $icons = $collection['icons'];

        if ($this->search) {
            $icons = array_filter($icons, function ($icon) {
                return str_contains(strtolower($icon), strtolower($this->search));
            });
        }

        return [
            'name' => $collection['name'],
            'prefix' => $collection['prefix'],
            'icons' => array_values($icons),
        ];
    }

    public function render()
    {
        return view('livewire.admin.icon-gallery', [
            'filteredCollection' => $this->getFilteredIcons(),
            'iconCollections' => $this->iconCollections,
        ]);
    }
}
