# Coding Agent Instructions

This file defines the conventions, architecture, and rules for this specific project. Read it fully before making any changes.

---

## Project Overview

**Laravel Drive** is a file management application (Google Drive-style). Users upload, organise, move, and download files inside nested folders. Admins manage the platform via Laravel Nova and Filament.

The project has **two frontends** sharing the same Laravel 12 backend:

| Frontend | Location | Entry point |
|----------|----------|-------------|
| Classic Blade | `resources/views/` | `http://localhost:8000/signin` |
| Vue 3 SPA | `frontend/src/` | `http://localhost:5174` (dev) |

Always read `context.md` in the project root for the full architectural reference before starting work.

---

## Stack Versions

| Package | Version |
|---------|---------|
| PHP | 8.2+ |
| Laravel | 12 |
| Laravel Sanctum | v4 |
| Laravel Nova | v5 |
| Filament | v5 |
| Livewire | v4 |
| Tailwind CSS | v4 |
| PHPUnit | v11 |
| Vue | 3 (Composition API) |
| Pinia | 3 |
| Vue Router | 4 |
| Vite | 7 |

---

## Laravel 12 Structure Rules

- **No `app/Http/Kernel.php`** — middleware is registered in `bootstrap/app.php` using `->withMiddleware()`.
- **No `routes/api.php` auto-loading** — it must be explicitly declared in `bootstrap/app.php` under `->withRouting(api: ...)`.
- **Route files:** `routes/web.php` (Blade), `routes/api.php` (JSON API), `routes/console.php`.
- **Middleware aliases** are declared in `bootstrap/app.php`, not in a Kernel.
- **Service providers** go in `bootstrap/providers.php`.
- Never use `env()` directly in code — only in `config/*.php` files. Use `config('key')` everywhere else.
- Use `Model::query()` instead of `DB::`. Prefer Eloquent relationships over raw queries.
- Always use `--no-interaction` with Artisan commands.

---

## Dual-Mode Controllers (Critical)

All four controllers handle **both Blade (web) and JSON (API) requests**. This is the core pattern of the project:

```php
if ($request->expectsJson()) {
    return response()->json([...]);
}
return redirect()->back()->with('success', '...');
```

**Rules:**
- **Never remove the Blade fallback.** The `routes/web.php` Blade routes still exist and must keep working.
- **Always add the JSON branch** when modifying or adding controller methods.
- `$request->expectsJson()` returns `true` when the `Accept: application/json` header is present. The Vue Axios instance sets this header on every request.
- For `DELETE` endpoints, the JSON body is read via `$request->validate()` because Axios sends `{ data: { ... } }` with `Content-Type: application/json`. This works correctly — do not add form method spoofing.

## API Routes (`routes/api.php`)

- All routes are prefixed `/api` automatically.
- Public routes: `POST /api/login`, `POST /api/register`.
- Protected routes use `middleware(['auth:sanctum', 'user'])`.
- **Critical ordering:** literal routes must come before wildcard routes. `PATCH /folders/move` is declared **before** `PATCH /folders/{folder}`.

## Authentication

- **Laravel Sanctum v4** with SPA cookie auth (not token auth).
- `$middleware->statefulApi()` in `bootstrap/app.php` enables Sanctum session cookies for the `SANCTUM_STATEFUL_DOMAINS` origins.
- `HasApiTokens` trait is on the `User` model alongside `Actionable` (Nova) and `HasFactory`, `Notifiable`.
- The Vue frontend calls `GET /sanctum/csrf-cookie` via `initCsrf()` before the first POST. Never skip this step.
- Required `.env` values: `SANCTUM_STATEFUL_DOMAINS=localhost:5174`, `SESSION_DOMAIN=localhost`, `SESSION_SAME_SITE=lax`.

## Middleware

- `admin` → `App\Http\Middleware\AdminMiddleware` — allows only `is_admin = true` users.
- `user` → `App\Http\Middleware\UserMiddleware` — allows only non-admin authenticated users.
- Both middlewares check `$request->expectsJson()` and return a JSON error instead of a redirect for API requests.

---

## Vue SPA (`frontend/`)

The Vue app is a completely standalone project inside `frontend/`. It has its own `package.json`, `vite.config.js`, and `node_modules`.

### Key files

| File | Purpose |
|------|---------|
| `src/api/axios.js` | Base Axios instance. Sets `withCredentials`, `withXSRFToken`, `Accept: application/json`. Exports `initCsrf()`. Has a 401 interceptor that redirects to `/login`. |
| `src/stores/auth.js` | Pinia store: `user`, `isAuthenticated`, `isAdmin`, `login`, `register`, `logout`, `fetchUser`. `logout()` uses `try/finally` to always clear state. |
| `src/stores/files.js` | Pinia store: all CRUD operations for folders and files. `moveFile`/`moveFolder` accept `null` as folder/parent ID (means root). |
| `src/router/index.js` | Vue Router. `/login` (guest-only), `/dashboard` (auth-only). `beforeEach` calls `fetchUser()` once on first navigation to restore session. |
| `src/views/DashboardView.vue` | Orchestrates all file manager UI. Reads `?folder=` query param for navigation. |
| `src/views/LoginView.vue` | Login + Register tabs. |

### Vue conventions

- Always use **Composition API** with `<script setup>`.
- Stores use the **setup function style** (`defineStore('name', () => { ... })`), not Options API style.
- All API calls go through `src/api/axios.js` — never use `fetch` or raw `axios`.
- **Moving items to root:** pass `null` as `folderId`/`parentId`. Never pass `0` — `Number(null) === 0` which fails backend validation. Always guard: `value != null ? Number(value) : null`.
- HTTP `DELETE` with a body: use `api.delete('/path', { data: { id: x } })`.
- File uploads: override `Content-Type` to `multipart/form-data` per-request. Never set the boundary manually — pass a `FormData` object and let the browser handle it.

### Vite proxy (dev only)

In dev, the Vite server proxies `/api`, `/sanctum`, and `/storage` to `http://localhost:8000`. This means there are **no CORS issues in development** — both origins appear as `localhost:5174` to the browser.

### Building for production

```bash
cd frontend && npm run build   # outputs to public/spa/
```

Laravel serves the built SPA at `/spa/{any}` via a catch-all route in `routes/web.php`.

---

## Models

| Model | Key details |
|-------|------------|
| `User` | Traits: `HasApiTokens`, `Actionable` (Nova), `HasFactory`, `Notifiable`. Implements `FilamentUser`. Appends `storage_used` computed accessor. |
| `Folder` | Appends `total_size` (SUM query). Has `getBreadcrumbPath()` method. Has `children` and `files` relationships. |
| `File` | Appends `size_in_kb`. Stored in `storage/app/public/uploads/{user_id}/`. |

`$appends` values are automatically included in JSON serialization — no extra work needed for API responses.

---

## FolderController: `isDescendant` Logic

The `move` method prevents circular folder structures. Use `isDescendant(Folder $source, int $targetId)` which walks **downward** through children recursively to check if `$targetId` is a descendant of `$source`.

**Do not** use upward ancestor traversal — it produces false positives (e.g., blocking a move to a grandparent folder).

---

## PHP Code Style

- Run `vendor/bin/pint --dirty --format agent` after modifying any PHP file.
- Always use curly braces for control structures, even single-line bodies.
- Always declare explicit return types on methods.
- Use PHP 8 constructor property promotion.
- PHPDoc blocks over inline comments.

---

## Admin Panels

- **Filament v5**: `/filament` — resources in `app/Filament/Resources/`.
- **Nova v5**: `/nova` — resources in `app/Nova/`.
- Both panels are untouched by the Vue SPA work. Do not modify them unless explicitly asked.
- Filament components use `static make()` initialisation. Use `Filament\Schemas\Components\` for layout, `Filament\Forms\Components\` for fields/entries, `Filament\Actions\` for actions.

---

## Testing

- Run tests with `php artisan test --compact`.
- Filter to one test: `php artisan test --compact --filter=testName`.
- Write PHPUnit feature tests (not Pest). Use `php artisan make:test --phpunit {Name}`.
- Use model factories for test data. Never manually create models without factories.
- Do not delete existing tests without explicit approval.

---

## Things to Never Do

- Do not use `env()` outside of config files.
- Do not create `app/Http/Kernel.php` — it does not exist in Laravel 12.
- Do not add `$middleware->api()` — use `$middleware->statefulApi()` for Sanctum SPA.
- Do not declare `routes/api.php` routes in `routes/web.php`.
- Do not remove Blade fallback branches from controllers.
- Do not force cast nullable IDs to `Number()` — returns `0` for `null`.
- Do not manually set `multipart/form-data` boundary in Axios for file uploads.
- Do not place literal API routes after wildcard routes of the same method and prefix.
- Do not create new top-level directories without approval.
- Do not install new Composer or NPM packages without approval.
