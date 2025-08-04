# GEMINI.md

## Project Overview

This project is a Laravel-based web application called "SnapMap". It allows users to create and view "tracemaps," which appear to be geotagged media (images and videos) that expire after 24 hours. The application also includes real-time features like a chat and notifications for new tracemaps, powered by Pusher. It has a Progressive Web App (PWA) integration, allowing it to be installed on mobile and desktop devices. An admin backend is available for managing tracemaps, messages, and maintenance mode.

### Key Technologies

*   **Backend:** Laravel (PHP)
*   **Frontend:** Blade templates, Tailwind CSS, Alpine.js, Vite
*   **Real-time:** Pusher (via `pusher/pusher-php-server` and `pusher-js`)
*   **Database:** SQLite (default)
*   **PWA:** `erag/laravel-pwa` package

### Architecture

The application follows a standard Laravel MVC architecture:

*   **Models:** `Tracemap`, `Media`, `Message`, `User`
*   **Views:** Blade templates located in `resources/views`
*   **Controllers:** `TracemapController`, `AdminController`, `ProfileController`
*   **Routes:** Defined in `routes/web.php` and `routes/auth.php`
*   **Frontend Assets:** Managed with Vite and located in `resources/js` and `resources/css`

## Building and Running

### Prerequisites

*   PHP >= 8.2
*   Composer
*   Node.js & npm

### Installation

1.  **Clone the repository.**
2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```
3.  **Install frontend dependencies:**
    ```bash
    npm install
    ```
4.  **Set up the environment:**
    *   Copy `.env.example` to `.env`:
        ```bash
        cp .env.example .env
        ```
    *   Generate an application key:
        ```bash
        php artisan key:generate
        ```
    *   Configure your database and Pusher credentials in the `.env` file.
5.  **Run database migrations:**
    ```bash
    php artisan migrate
    ```

### Development

To run the development server, which includes the Laravel server, queue listener, log pail, and Vite bundler, use the following command:

```bash
composer run dev
```

### Testing

To run the feature and unit tests, use the following command:

```bash
composer run test
```

## Development Conventions

*   **Coding Style:** The project uses Laravel Pint for code styling.
*   **Real-time Events:** Real-time events are broadcast using Laravel's broadcasting system with the Pusher driver. Events like `NewTracemapEvent` and `NewMessageEvent` are fired to notify clients of new content.
*   **PWA:** The application is a PWA, configured via the `erag/laravel-pwa` package. The manifest can be updated by running `php artisan erag:update-manifest`.
*   **Admin Functionality:** An admin area is available at `/dashboard` for authenticated admin users. It provides functionality for managing tracemaps, messages, and maintenance mode.
*   **Database Reset:** A route is available for admins to reset the database: `POST /admin/reset`.
*   **File Storage:** User-uploaded media is stored in the `storage/app/public/tracemaps` directory.
