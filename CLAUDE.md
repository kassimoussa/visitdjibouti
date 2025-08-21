# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 11 application for managing tourist attractions and events in Djibouti. The project is called "visitdjibouti" and features a multilingual admin panel for managing Points of Interest (POIs), events, categories, and media.

## Development Commands

### Development Environment
- `composer run dev` - Start full development environment (Laravel server, queue worker, logs, and Vite)
- `php artisan serve` - Start Laravel development server only
- `npm run dev` - Start Vite development server for frontend assets
- `npm run build` - Build production assets

### Database
- `php artisan migrate` - Run database migrations
- `php artisan db:seed` - Seed database with initial data
- `php artisan migrate:fresh --seed` - Fresh migration with seeding

### Code Quality
- `vendor/bin/pint` - Laravel Pint code formatting (available via composer)
- `vendor/bin/phpunit` - Run PHPUnit tests

### Cache and Optimization
- `php artisan config:cache` - Cache configuration
- `php artisan route:cache` - Cache routes
- `php artisan view:cache` - Cache views
- `php artisan optimize:clear` - Clear all caches

## Architecture

### Mobile User Authentication System
The application features a complete authentication system for mobile app users with revolutionary anonymous user support:
- **Separate AppUser Model**: Mobile users stored in `app_users` table, isolated from admin users
- **🚀 Anonymous User System**: Progressive onboarding without friction - users can use the app immediately
- **Laravel Sanctum**: API token authentication for both anonymous and registered users
- **OAuth Integration**: Google and Facebook social login via Laravel Socialite
- **Flexible Authentication**: Support for email/password, social authentication, and anonymous usage
- **Progressive Registration**: Anonymous users can convert to full accounts while preserving all data
- **Multilingual Support**: User preferences for language (French, English, Arabic)
- **Complete Profile Management**: Full CRUD operations for user profiles

#### Authentication Endpoints
- **Public Routes**: Register, Login, OAuth flows, Anonymous user creation (`/api/auth/*`)
- **Anonymous Routes**: Create anonymous user, retrieve by anonymous_id (public)
- **Protected Routes**: Profile management, password change, account deletion, social account linking
- **Anonymous Protected Routes**: Convert to complete, update preferences, delete anonymous user
- **OAuth Flows**: Web callback, mobile token-based authentication
- **API Structure**: RESTful design with standardized JSON responses

#### Key Files
- `app/Models/AppUser.php` - Mobile user model with Sanctum authentication and anonymous support
- `app/Http/Controllers/Api/AuthController.php` - User authentication endpoints
- `app/Http/Controllers/Api/AnonymousAuthController.php` - Anonymous user management (NEW)
- `app/Http/Controllers/Api/SocialAuthController.php` - OAuth integration
- `database/migrations/*_create_app_users_table.php` - Mobile user schema
- `database/migrations/*_add_anonymous_support_to_app_users_table.php` - Anonymous user fields (NEW)
- `routes/api.php` - Complete API routes for mobile app
- `API_DOCUMENTATION.md` - Complete API documentation with 33 endpoints

### Multilingual Content System
The application implements a custom translation system for content:
- Main models (Poi, Event, Category, Media) have separate translation tables
- Translation models: `PoiTranslation`, `EventTranslation`, `CategoryTranslation`, `MediaTranslation`
- Each translation stores locale-specific content (name, description, etc.)
- Models have `translation()` method to get content for specific locale
- Accessor methods like `getNameAttribute()` automatically fetch translated content

### Admin Panel Structure
- **Authentication**: Custom admin authentication system with `AdminUser` model
- **Middleware**: `AdminAuth` middleware protects admin routes
- **Controllers**: Located in `app/Http/Controllers/Admin/`
- **Livewire Components**: Extensive use of Livewire for interactive admin interfaces in `app/Livewire/Admin/`

### Key Models and Relationships
- **Poi (Points of Interest)**: Main tourist attraction model with categories, media, and translations
- **Event**: Event management with registrations and reviews
- **Category**: Hierarchical categories for organizing content
- **Media**: File management system with translations for descriptions
- **AdminUser**: Admin authentication and authorization
- **AppUser**: Mobile app users with Sanctum authentication
- **EventRegistration**: Event booking system with payment support
- **OrganizationInfo**: Tourism organization information with multilingual support
- **ExternalLink**: External useful links management
- **Embassy**: Embassy information for both foreign embassies in Djibouti and Djiboutian embassies abroad

### Frontend Technologies
- **Livewire 3.6**: For reactive components
- **Bootstrap 5.3**: UI framework
- **Vite**: Asset bundling
- **SCSS/Sass**: Styling with custom admin theme
- **FontAwesome**: Icons
- **jQuery**: Legacy JavaScript support

### File Storage
- Media files stored in `storage/app/public/media/images/`
- Livewire temporary files in `storage/app/livewire-tmp/`
- Uses Intervention Image package for image processing

### Database
- **MySQL database** (NOT SQLite)
- Migrations follow Laravel conventions with timestamps
- Foreign key constraints with cascade deletes for translations

### Regions
The application is specifically designed for Djibouti with predefined regions:
- Djibouti, Ali Sabieh, Dikhil, Tadjourah, Obock, Arta

### Helper Functions
Global helper functions are autoloaded from `app/Helpers/functions.php` via Composer.

## OAuth Configuration Setup

For Google and Facebook authentication to work, you need to configure OAuth providers:

### Environment Variables
Add these to your `.env` file:

```env
# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret

# Facebook OAuth  
FACEBOOK_CLIENT_ID=your_facebook_app_id
FACEBOOK_CLIENT_SECRET=your_facebook_app_secret
```

### OAuth Provider Setup

#### Google Console Setup
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable Google+ API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URIs:
   - `http://your-domain.com/api/auth/google/callback` (development)
   - `https://your-domain.com/api/auth/google/callback` (production)

#### Facebook Developer Setup
1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Create a new app
3. Add Facebook Login product
4. Configure Valid OAuth Redirect URIs:
   - `http://your-domain.com/api/auth/facebook/callback` (development)
   - `https://your-domain.com/api/auth/facebook/callback` (production)

## Testing
- PHPUnit configuration in `phpunit.xml`
- Test files in `tests/Feature/` and `tests/Unit/`
- Use `vendor/bin/phpunit` to run tests

## Complete Mobile API Implementation

### 33+ API Endpoints Available
The mobile API is fully implemented with comprehensive endpoints:

#### Authentication & User Management (10 endpoints) - ENHANCED 🚀
- `POST /api/auth/register` - User registration 
- `POST /api/auth/login` - User login
- `GET /api/auth/{provider}/redirect` - OAuth redirect (Google/Facebook)
- `GET /api/auth/{provider}/callback` - OAuth callback
- `POST /api/auth/{provider}/token` - Mobile OAuth token authentication
- `POST /api/auth/anonymous` - Create anonymous user (NEW)
- `POST /api/auth/anonymous/retrieve` - Retrieve anonymous user (NEW)
- `POST /api/auth/convert-anonymous` - Convert anonymous to complete (NEW)
- `PUT /api/auth/anonymous/preferences` - Update anonymous preferences (NEW)
- `DELETE /api/auth/anonymous` - Delete anonymous user (NEW)
- Plus protected routes for profile, logout, password change, etc.

#### Points of Interest (4 endpoints)
- `GET /api/pois` - List POIs with advanced filtering
- `GET /api/pois/{id|slug}` - POI details
- `GET /api/pois/category/{id}` - POIs by category
- `GET /api/pois/nearby` - Nearby POIs with GPS coordinates

#### Events (5 endpoints)
- `GET /api/events` - List events with filtering
- `GET /api/events/{id|slug}` - Event details
- `POST /api/events/{event}/register` - Event registration (public & authenticated)
- `DELETE /api/events/{event}/registration` - Cancel registration (protected)
- `GET /api/my-registrations` - User's event registrations (protected)

#### Favorites Management (7 endpoints) - NEW
- `GET /api/favorites` - All user's favorites (POIs & Events)
- `GET /api/favorites/pois` - User's favorite POIs only
- `GET /api/favorites/stats` - Favorites statistics
- `POST /api/favorites/pois/{poi}` - Add/Remove POI to/from favorites (toggle)
- `DELETE /api/favorites/pois/{poi}` - Remove POI from favorites
- `POST /api/favorites/events/{event}` - Add/Remove Event to/from favorites (toggle)
- `DELETE /api/favorites/events/{event}` - Remove Event from favorites

#### Organization & Links (4 endpoints)
- `GET /api/organization` - Tourism organization information
- `GET /api/external-links` - External useful links
- `GET /api/external-links/{id}` - Link details
- `GET /api/embassies` - Embassy listings with advanced features

#### App Settings (3 endpoints) - NEW
- `GET /api/app-settings` - All mobile app configuration settings
- `GET /api/app-settings/flat` - Settings in flat list format
- `GET /api/app-settings/type/{type}` - Settings by specific type

#### Tour Operators (4 endpoints) - NEW
- `GET /api/tour-operators` - List with advanced filtering
- `GET /api/tour-operators/nearby` - Nearby operators with GPS coordinates
- `GET /api/tour-operators/service/{type}` - Operators by service type
- `GET /api/tour-operators/{identifier}` - Operator details (ID or slug)

### Key Features Implemented
- **🚀 Anonymous User System**: Revolutionary progressive onboarding without friction (NEW)
- **Complete OAuth Integration**: Google & Facebook login with Laravel Socialite
- **Event Reservation System**: Full booking system with guest, anonymous, and authenticated user support
- **Favorites Management System**: Complete favorites system for POIs and Events with polymorphic relations
- **Tour Operators Management**: Complete system with service filtering, geolocation, and certification levels
- **Dynamic App Settings**: Mobile app configuration system for splash screens, onboarding, and app info
- **Multilingual API**: All endpoints support Accept-Language header (fr, en, ar)
- **Geolocation Support**: Nearby POIs, embassies, and tour operators with GPS coordinates
- **Advanced Filtering**: Search, categories, dates, regions, status, services, and certification filters
- **Comprehensive Documentation**: API_DOCUMENTATION.md with examples and cURL commands

### Mobile App Ready Features
- **🎯 Zero-friction onboarding**: Anonymous users can use the app immediately (NEW)
- **Progressive registration**: Anonymous users convert to full accounts seamlessly (NEW)
- **Data preservation**: All anonymous user data (favorites, reservations) is preserved during conversion (NEW)
- Event bookings for anonymous and registered users
- Social login integration
- **Favorites system with cloud synchronization** (works for anonymous users)
- **Real-time favorites count and status**
- **Tour operators with geolocation and filtering**
- **Dynamic app configuration (splash screens, onboarding)**
- Complete tourism information (POIs, events, embassies, organization)
- Multilingual content with automatic fallback
- GPS-based nearby searches
- Standardized JSON responses with error handling

## Key Files to Check When Working on Features

### Admin Interface
- Routes: `routes/web.php` (all admin routes are here)
- Models with relationships: `app/Models/`
- Livewire components for admin interface: `app/Livewire/Admin/`
- Views for Livewire: `resources/views/livewire/admin/`

### Livewire Admin File Organization (Recently Reorganized)
The admin Livewire files are now properly organized in logical subfolders:

- `settings/` - App settings, embassy and organization management
- `categories/` - Category and modern category managers
- `external-links/` - External links management
- `components/` - Reusable components (icon-selector)
- `media/` - Media management (edit, manager, upload, simple-upload)
- `news/` - News category management  
- `users/` - User management
- `_development/` - Non-production files (tour-operator-manager, media-selector-modal, etc.)

All root-level files have been moved to appropriate subfolders and PHP component view paths have been updated accordingly.

### Mobile API
- API Routes: `routes/api.php` - All 30+ endpoints organized by feature
- API Controllers: `app/Http/Controllers/Api/` - AuthController, PoiController, EventController, TourOperatorController, AppSettingController, etc.
- Models: Support for both admin and mobile user authentication
- Documentation: `API_DOCUMENTATION.md` - Complete reference with examples

### Key Implementation Notes
- All API endpoints follow RESTful design patterns
- Consistent error handling and response format across all endpoints
- EventRegistration model updated to work with AppUser instead of User
- OAuth fully configured and ready for production use
- Complete translation system integration for all API responses
- **Favorites system with polymorphic relationships supporting multiple content types**
- **Automatic favorites count and status included in all POI/Event responses**
- **Cloud-based favorites synchronization with offline capability**
- **Tour operators system with comprehensive filtering and geolocation**
- **Dynamic app settings for mobile configuration management**