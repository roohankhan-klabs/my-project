# My Project - Laravel Drive

A Google Drive-like file management application built with Laravel. This project allows users to manage their files and folders securely.

## Features

- **User Authentication**: Secure login and registration.
- **File Management**: Upload, delete, and download files.
- **Folder Management**: Create, rename, and delete folders (along with all nested contents).
- **Nested Structures**: Organize files inside an infinite hierarchy of folders.
- **Storage Tracking**: Tracks the total storage used by each user based on their uploaded files.
- **Admin Panel**: Manage users and monitor the application using Laravel Nova.
- **Responsive UI**: A modern interface built with Tailwind CSS.

## Tech Stack

- **Framework**: Laravel 12
- **Admin Panel**: Laravel Nova 5
- **Styling**: Tailwind CSS v4
- **Database**: SQLite (or any supported Laravel database)

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM

## Quick Start

1. **Clone the repository** (or navigate to the project folder):
   ```bash
   cd my-project
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install NPM dependencies & build assets**:
   ```bash
   npm install
   npm run build
   ```

4. **Set up the `.env` file**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Run migrations and seed the database** (if applicable):
   ```bash
   php artisan migrate
   ```

6. **Serve the application**:
   ```bash
   php artisan serve
   ```

You can now access the application in your browser.

## Testing

This application uses PHPUnit for its test suite. To run the tests, execute:

```bash
php artisan test
```

## License

This project is open-source software.
