# Project Context

## What This Project Is

A **file management web application** built on Laravel 12. Users can upload, organise, move, and download files inside nested folders. Admins manage the platform through Laravel Nova.

A **standalone Vue 3 SPA** lives in the `frontend/` directory and connects to the Laravel backend via a JSON REST API secured with Laravel Sanctum (SPA cookie auth).

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend framework | Laravel 12 (PHP 8.2+) |
| Database | SQLite (local dev) |
| Admin panel | Filament v5 + Laravel Nova v5 |
| API auth | Laravel Sanctum (SPA cookie-based) |
| Frontend framework | Vue 3 (Composition API) |
| State management | Pinia |
| Client-side routing | Vue Router 4 |
| HTTP client | Axios (with CSRF + credentials) |
| CSS | Tailwind CSS v4 |
| Build tool | Vite 7 |

---

## Directory Structure

```
my-project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/AuthController.php    # login, register, logout
│   │   │   ├── FileController.php         # upload, delete, move, download
│   │   │   ├── FolderController.php       # create, rename, delete, move
│   │   │   └── UserController.php         # dashboard (folders + files for current path)
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php
│   │       └── UserMiddleware.php         # returns JSON 403 for API requests
│   ├── Models/
│   │   ├── User.php                       # HasApiTokens, FilamentUser, Actionable
│   │   ├── File.php                       # appends size_in_kb
│   │   └── Folder.php                     # appends total_size, getBreadcrumbPath()
│   └── Filament/ + Nova/                  # admin panel resources
│
├── bootstrap/app.php                      # middleware + routing config (Laravel 12 style)
│
├── routes/
│   ├── web.php                            # Blade routes + /spa catch-all
│   └── api.php                            # JSON API routes (Sanctum-protected)
│
├── config/
│   ├── cors.php                           # CORS config (supports_credentials: true)
│   └── sanctum.php
│
├── resources/views/                       # Blade templates (existing, unchanged)
│
├── frontend/                              # Vue 3 SPA (standalone)
│   ├── package.json
│   ├── vite.config.js                     # Vite with proxy to Laravel on :8000
│   ├── index.html
│   └── src/
│       ├── main.js                        # app entry point
│       ├── App.vue
│       ├── assets/main.css                # @import "tailwindcss"
│       ├── api/
│       │   └── axios.js                   # base axios instance + initCsrf()
│       ├── router/
│       │   └── index.js                   # /login (guest), /dashboard (auth)
│       ├── stores/
│       │   ├── auth.js                    # user, login, register, logout, fetchUser
│       │   └── files.js                   # fetchDashboard + all CRUD actions
│       ├── views/
│       │   ├── LoginView.vue              # login + register tabs
│       │   ├── DashboardView.vue          # main file manager UI
│       │   └── NotFoundView.vue
│       └── components/
│           ├── Breadcrumbs.vue
│           ├── DragDropUpload.vue         # upload via drag-drop or file picker
│           ├── FolderCard.vue             # drag source+target, inline rename
│           ├── FileCard.vue               # image preview, download, delete
│           ├── CreateFolderModal.vue
│           └── DeleteConfirmModal.vue
│
└── public/spa/                            # Vue production build output (git-ignored)
```

---

## API Routes (`routes/api.php`)

All routes are prefixed `/api`. Protected routes need a valid Sanctum session cookie.

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| POST | `/api/login` | Public | Session login |
| POST | `/api/register` | Public | Register + auto-login |
| GET | `/api/user` | Sanctum | Returns authenticated user JSON |
| POST | `/api/logout` | Sanctum + user | Invalidate session |
| GET | `/api/dashboard` | Sanctum + user | Folders + files for `?folder=` param |
| POST | `/api/folders` | Sanctum + user | Create folder |
| PATCH | `/api/folders/move` | Sanctum + user | Move folder (literal route before wildcard) |
| PATCH | `/api/folders/{folder}` | Sanctum + user | Rename folder |
| DELETE | `/api/folders` | Sanctum + user | Delete folder + all contents |
| POST | `/api/files` | Sanctum + user | Upload files (multipart) |
| PATCH | `/api/files/move` | Sanctum + user | Move file to folder |
| DELETE | `/api/files` | Sanctum + user | Delete file |
| GET | `/api/files/{file}/download` | Sanctum | Download file as blob |

---

## Authentication Flow

1. Vue calls `GET /sanctum/csrf-cookie` (via `initCsrf()`) before the first POST
2. Vue POSTs credentials to `/api/login` or `/api/register`
3. Laravel creates a session, sets the session cookie + `XSRF-TOKEN` cookie
4. Axios sends `XSRF-TOKEN` as `X-XSRF-TOKEN` header on every subsequent request
5. On hard refresh, `router.beforeEach` calls `GET /api/user` to restore session state
6. A 401 response triggers the Axios interceptor which redirects to `/login`

---

## Running the Project

### Development

```bash
# Terminal 1 — Laravel backend
php artisan serve          # http://localhost:8000

# Terminal 2 — Vue frontend
cd frontend
npm run dev                # http://localhost:5174
```

Access the Vue SPA at **http://localhost:5174**. Vite proxies `/api`, `/sanctum`, and `/storage` to `http://localhost:8000` automatically — no CORS issues.

### Build for production

```bash
cd frontend
npm run build              # outputs to public/spa/
```

The production build is served by Laravel via:
```
GET /spa/{any?} → public/spa/index.html
```

### Other useful commands

```bash
php artisan migrate              # run database migrations
php artisan storage:link         # create public/storage symlink (already done)
php artisan config:clear         # clear config cache after .env changes
php artisan test --compact       # run PHPUnit tests
vendor/bin/pint --dirty          # fix PHP code style
```

---

## Key `.env` Settings

```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5174
SANCTUM_STATEFUL_DOMAINS=localhost:5174
SESSION_DOMAIN=localhost
SESSION_SAME_SITE=lax           # must NOT be "strict"
```

---

## Important Implementation Notes

- **`DELETE` requests with JSON body** — uses `api.delete('/files', { data: { file_id: id } })`. Laravel reads the JSON body when `Content-Type: application/json` is set.
- **File uploads** — the per-request header is overridden to `multipart/form-data`. Never set the boundary manually; pass a `FormData` object.
- **Route ordering** — `PATCH /api/folders/move` is declared before `PATCH /api/folders/{folder}` to prevent "move" being matched as a folder ID.
- **All controllers are dual-mode** — they check `$request->expectsJson()` and return JSON for API calls, or redirect for Blade-based calls. Existing Blade app continues to work.
- **`UserMiddleware`** — returns JSON 403 (not HTML redirect) when `$request->expectsJson()` is true.
- **Image previews** — `FileCard.vue` constructs image src as `/storage/{file.path}`, proxied to Laravel in dev.

---

## Existing Blade Frontend

The original Blade-based frontend (login, dashboard) still exists and works at the same routes (`/signin`, `/dashboard`). It is completely untouched. Only the controllers were updated to dual-mode responses.

---

## Admin Panel

- **Filament v5**: `/filament`
- **Laravel Nova v5**: `/nova`

Both admin panels are completely untouched. Admin users are redirected to Nova after login (both from the Blade and Vue frontends).
