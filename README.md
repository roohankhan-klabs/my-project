# Laravel Drive — User Manual

A Google Drive-style file manager. Store, organise, and access your files from anywhere through a clean web interface.

---

## Table of Contents

1. [Overview](#overview)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Running the Application](#running-the-application)
5. [User Guide](#user-guide)
   - [Creating an Account](#creating-an-account)
   - [Logging In](#logging-in)
   - [Dashboard](#dashboard)
   - [Working with Folders](#working-with-folders)
   - [Working with Files](#working-with-files)
   - [Navigating Your Drive](#navigating-your-drive)
   - [Logging Out](#logging-out)
6. [Admin Guide](#admin-guide)
7. [Troubleshooting](#troubleshooting)
8. [Running Tests](#running-tests)

---

## Overview

Laravel Drive lets each user maintain their own private file storage with:

- **Nested folders** — organise files inside any number of sub-folders
- **File uploads** — drag and drop or click to upload, up to 10 MB per file
- **File downloads** — download any file back to your device
- **Drag-to-move** — drag a file or folder onto another folder to move it
- **Inline rename** — double-click a folder name to rename it in place
- **Image previews** — thumbnail previews for image files

There are two frontends that both connect to the same backend:

| Frontend | URL | Best for |
|----------|-----|---------|
| Classic (Blade) | `http://localhost:8000/signin` | Server-rendered pages |
| Vue SPA | `http://localhost:5174` (dev) or `http://localhost:8000/spa` (production build) | Modern single-page experience |

Admins access the management panels at `/nova` (Laravel Nova) and `/filament` (Filament).

---

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18+ and NPM
- SQLite (included by default) or any Laravel-supported database

---

## Installation

**1. Clone or navigate to the project folder:**
```bash
cd my-project
```

**2. Install PHP dependencies:**
```bash
composer install
```

**3. Create the environment file:**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Run database migrations:**
```bash
php artisan migrate
```

**5. Create the public storage symlink** (for file previews):
```bash
php artisan storage:link
```

**6. Install frontend dependencies:**

For the classic Blade frontend:
```bash
npm install
npm run build
```

For the Vue SPA frontend:
```bash
cd frontend
npm install
```

---

## Running the Application

### Option A — Classic Blade frontend only

```bash
php artisan serve
```

Open **http://localhost:8000** in your browser.

---

### Option B — Vue SPA (recommended)

Run both servers in separate terminals:

**Terminal 1 — Laravel backend:**
```bash
php artisan serve
```

**Terminal 2 — Vue frontend:**
```bash
cd frontend
npm run dev
```

Open **http://localhost:5174** in your browser.

> All API requests from the Vue app are automatically proxied to Laravel — no CORS configuration needed in development.

---

### Production build

To serve the Vue SPA through Laravel (single server):

```bash
cd frontend
npm run build
php artisan serve
```

Then open **http://localhost:8000/spa**.

---

## User Guide

### Creating an Account

1. Open the application in your browser
2. Click **Register** (on the login page, select the Register tab in the Vue SPA)
3. Enter your full name, email address, and a password (minimum 8 characters)
4. Confirm your password and submit
5. You are automatically logged in and taken to your Dashboard

---

### Logging In

1. Go to the application URL
2. Enter your email and password
3. Click **Sign In**

If you enter wrong credentials, an error message is shown. Your account is not locked after failed attempts.

> **Admin accounts** are redirected to the Nova admin panel after login. The Vue SPA will display a message directing admin users to `/nova`.

---

### Dashboard

The Dashboard is your personal drive. It shows all your folders and files for the current location (starting at the root).

The page is divided into three areas:

| Area | Description |
|------|-------------|
| **Header** | App name and Logout button |
| **Breadcrumb bar** | Shows your current location path. Click any segment to jump to that folder. Includes a "New Folder" button. |
| **Upload zone** | Drag files here or click to select files from your device |
| **Folders grid** | All folders in the current location |
| **Files grid** | All files in the current location |

---

### Working with Folders

#### Create a folder
1. Click **New Folder** in the top-right of the breadcrumb bar
2. Type a name in the modal dialog
3. Click **Create**

The new folder appears immediately in the grid.

#### Rename a folder
1. **Double-click** the folder name
2. An inline text input appears — edit the name
3. Press **Enter** to save or **Escape** to cancel

#### Delete a folder
1. Hover over the folder card — a **Delete** button appears in the corner
2. Click **Delete**
3. Confirm in the dialog

> Deleting a folder permanently removes it and **all files and sub-folders inside it**. This cannot be undone.

#### Move a folder into another folder
1. Click and **drag** the folder card
2. Drop it onto the target folder card

The dragged folder disappears from the current view — it has been moved into the target folder. Navigate into the target folder to confirm.

#### Move a folder to the parent
While inside a folder, drag a folder card onto the **"Up to parent folder"** button at the top of the folders list.

---

### Working with Files

#### Upload files
**Method 1 — Drag and drop:**
Drop one or more files directly onto the upload zone.

**Method 2 — File picker:**
Click anywhere on the upload zone to open your system's file browser. Select one or more files.

> Maximum file size is **10 MB per file**. You can upload multiple files at once.

Uploaded files appear in the files grid immediately. They are stored inside the folder you are currently viewing.

#### Download a file
1. Hover over the file card — action buttons appear in the corner
2. Click the **download icon** (arrow pointing down)

The file is downloaded to your device's default downloads folder.

#### Delete a file
1. Hover over the file card
2. Click the **delete icon** (X)
3. Confirm in the dialog

> Deleting a file permanently removes it from storage. This cannot be undone.

#### Move a file into a folder
1. Click and **drag** the file card
2. Drop it onto any folder card in the same view

The file disappears from the current view — it has been moved into that folder. Navigate into the folder to access it.

#### Move a file to the parent folder
While inside a folder, drag a file card onto the **"Up to parent folder"** button.

#### Image previews
Files with image MIME types (JPEG, PNG, GIF, WebP, etc.) automatically show a thumbnail preview in their card. Other file types show the MIME type as a label.

---

### Navigating Your Drive

#### Enter a folder
Click anywhere on a folder card (except the rename input or delete button) to navigate into it.

#### Go back up
- Use the **breadcrumb path** at the top of the page — click any segment to jump directly to that level
- Click **Root** in the breadcrumbs to return to the top level
- Click the **"Up to parent folder"** button that appears when you are inside a folder

The URL updates with a `?folder=` parameter as you navigate, so your browser's back button also works.

---

### Logging Out

Click **Logout** in the top-right corner of the header. You are returned to the login page. Your files remain stored and are accessible the next time you log in.

---

## Admin Guide

Admin users have a separate panel for managing the application. They cannot use the regular user dashboard.

### Accessing the Admin Panel

Log in with an admin account. You are automatically redirected to the Nova panel.

- **Laravel Nova**: `http://localhost:8000/nova` — manage users, files, and folders
- **Filament**: `http://localhost:8000/filament` — alternative admin interface

### User Management (Nova)

From the Nova panel you can:
- View all registered users and their storage usage
- Create or delete user accounts
- Inspect or delete any user's files and folders

---

## Troubleshooting

**The Vue SPA shows a blank page or cannot connect to the API**
- Make sure `php artisan serve` is running on port 8000
- Make sure `npm run dev` is running in the `frontend/` directory
- Check that your `.env` contains `SANCTUM_STATEFUL_DOMAINS=localhost:5174`

**Login returns a 419 error (CSRF token mismatch)**
- This is a session/cookie issue. Try clearing your browser cookies for `localhost` and logging in again.
- Check that `SESSION_SAME_SITE=lax` is set in `.env`

**Uploaded images don't show a preview**
- The public storage symlink may be missing. Run: `php artisan storage:link`

**Changes to the Blade frontend are not reflected**
- Run `npm run build` (or `npm run dev` for live reloading) in the project root

**Changes to the Vue SPA are not reflected**
- The Vite dev server hot-reloads automatically. If using the production build, run `npm run build` from the `frontend/` directory.

**"Folder and everything inside it" was deleted by mistake**
- Deletion is permanent. There is no recycle bin or undo.

---

## Running Tests

```bash
# Run all tests
php artisan test --compact

# Run a specific test file
php artisan test --compact tests/Feature/ExampleTest.php

# Run tests matching a name
php artisan test --compact --filter=testName
```

---

## License

This project is open-source software.
