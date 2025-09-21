# GEMINI.md

## Project Overview

This is a Laravel-based project for "Visit Djibouti", a tourism application. It features a web-based admin panel and a RESTful API, likely for a mobile application.

**Key Technologies:**

*   **Backend:** PHP / Laravel
*   **Frontend (Admin Panel):** Livewire
*   **API:** Laravel, with Sanctum for authentication
*   **Database:** Relational (inferred from migrations)
*   **Styling:** Tailwind CSS

**Architecture:**

The project is structured as a standard Laravel application.

*   `app/`: Contains the core application logic, including Models, Controllers, and Livewire components.
*   `routes/`: Defines the application's web and API routes.
    *   `web.php`: Handles the admin panel routes.
    *   `api.php`: Defines the API endpoints for the mobile app.
*   `resources/views/`: Contains the Blade templates and Livewire views for the admin panel.
*   `database/`: Includes database migrations and seeders.
*   `config/`: Holds the application's configuration files.

**Features:**

*   **Admin Panel:** A web interface for managing the application's content, including Points of Interest (POIs), events, news, categories, and users.
*   **Mobile App API:** A comprehensive API that provides data for a mobile application. It includes endpoints for:
    *   Authentication (including social login and anonymous users)
    *   Content retrieval (POIs, events, news, etc.)
    *   User features (favorites, reservations, location tracking)
*   **Multilingual Support:** The application supports French, English, and Arabic.

## Building and Running

To run the project in a development environment, use the following command:

```bash
npm run dev
```

This command will:

1.  Start the PHP development server (`php artisan serve`).
2.  Start the queue listener (`php artisan queue:listen`).
3.  Start the log tailing utility (`php artisan pail`).
4.  Build the frontend assets with Vite (`npm run dev`).

**Prerequisites:**

*   PHP and Composer installed.
*   Node.js and npm installed.
*   A configured `.env` file (you can copy `.env.example`).

## Development Conventions

*   **Coding Style:** The project uses Laravel Pint for PHP code style. To format your code, run:

    ```bash
    ./vendor/bin/pint
    ```

*   **Testing:**
    *   **TODO:** Document the testing practices and commands once they are established.
