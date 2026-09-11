# AGENT.md

Instructions for Claude working in this repository. This repo is a
**boilerplate**: a working skeleton (landing page + contact form + admin
panel, PHP/MySQL, Coolify-ready) meant to be turned into a specific project
by reading `PROJECT.md` and `data/`, then generating the real site on top
of the existing structure.

## Tech stack

- **Backend**: PHP 8.1+, PDO/MySQL. No framework — small, explicit files.
- **Frontend**: plain HTML/CSS/JS. No build step, no bundler, no npm
  dependency required to run the site.
- **Database**: MySQL/MariaDB, schema managed via versioned SQL files in
  `database/migrations/` (see below) — no ORM.
- **Admin**: session-based auth, server-rendered PHP pages (no SPA).
- **Deployment**: Coolify, via Nixpacks (`nixpacks.toml`) + a custom nginx
  config (`nginx.template.conf`). No Docker, no server management.

Don't introduce a frontend framework, build tool, or backend framework
unless `PROJECT.md` explicitly calls for something this stack can't do.
The point of this boilerplate is that it deploys with zero extra
configuration — keep it that way.

## Folder structure

```
PROJECT.md              # what to build — read this first
data/                   # raw material for the build (copy, images, CSVs) — input only, not served
AGENT.md                # this file

index.html              # public landing page (webroot)
assets/
  css/style.css         # design system: CSS variables + components
  js/config.js          # SITE_CONFIG — site copy, nav, feature list
  js/main.js            # generic UI wiring (nav, [data-site] binding, mobile menu)
  js/forms.js           # generic form helpers (validation display, postJSON)
  js/contact.js         # wires #contact-form to backend/api/contact.php — pattern to copy
  img/                  # logos, hero images, etc. (empty by default)

backend/
  config.php            # app constants + DB credential loading (env vars in prod)
  db_credentials.example.php  # copy to db_credentials.php for local dev (gitignored)
  lib/
    Database.php        # PDO connection singleton + migration runner + admin seed
    helpers.php          # json_response, validation, clean_str, etc.
  api/
    contact.php          # example public endpoint — pattern to copy for new forms
  data/                 # private runtime data (admin's one-time password file) — never web-reachable

database/
  migrations/           # numbered .sql files, applied automatically on next request
    0001_create_contact_messages.sql

admin/                  # session-authenticated admin panel
  login.php, logout.php, index.php (dashboard), settings.php (change own password)
  users.php             # manage admin accounts: add/list/delete (self-delete and last-admin blocked)
  messages.php, export_messages.php   # list/search/export pattern — copy for new tables
  includes/
    auth.php            # session bootstrap, require_login(), CSRF helpers
    header.php, footer.php   # shared layout — nav items live in header.php
  assets/admin.css, admin.js

nixpacks.toml            # Coolify build config (PHP provider)
nginx.template.conf      # routing: public/, admin/, backend/api/ public, backend/ otherwise denied
composer.json
```

## The "build" workflow

When the user types **"build"** (or otherwise asks you to build the
project from the spec), do this:

1. **Read `PROJECT.md` in full.** If it's still mostly template
   boilerplate text with no real content, or a section critical to
   scoping the work is empty (e.g. no goal, no pages listed), ask the
   user targeted questions before writing code rather than guessing.
2. **Inventory `data/`.** Read every file there — copy, images, CSVs,
   briefs. Treat it as source-of-truth content, not decoration.
3. **Plan before editing**: list the pages/sections, forms, and DB tables
   implied by the spec. For anything beyond the default contact form,
   figure out what new migration files, API endpoints, and admin pages
   are needed (see conventions below).
4. **Rewrite the landing page**: update `assets/js/config.js`
   (`SITE_CONFIG`) with real copy, and edit `index.html` directly for
   structural changes (new sections, reordering, removing the default
   ones that don't apply). Keep using `assets/css/style.css`'s existing
   variables and component classes; only add new CSS for genuinely new
   UI patterns, and add it to that file rather than inline `<style>`.
5. **Re-brand**: update the CSS variables in `:root` (`assets/css/style.css`)
   to match colors from `PROJECT.md`/`data/`. Drop in any logo/images from
   `data/` into `assets/img/` and reference them from `index.html`.
6. **Build new forms/tables** following the existing pattern (one example
   already wired end-to-end: contact form → `contact.php` → `contact_messages`
   table → `admin/messages.php`). See "Adding a new form" below.
7. **Update the admin panel** nav (`admin/includes/header.php`) and pages
   for whatever new tables exist, reusing `admin/messages.php` and
   `admin/export_messages.php` as templates.
8. **Sanity-check**: run `php -l` on every changed/new `.php` file
   (syntax check only — there's no PHP runtime with a DB here to fully
   exercise it). Open `index.html` structure mentally against
   `nginx.template.conf` to confirm no new top-level PHP directory needs
   a routing/deny rule added.
9. **Report a summary**: what was built, what's still a placeholder
   (e.g. "no real logo was provided, using a text wordmark"), and what
   the user needs to do before deploying (e.g. set env vars, provide a
   missing asset).

Don't ask the user to re-explain something already answered in
`PROJECT.md` or `data/`. Do ask when a decision materially changes scope
or architecture and the spec is silent on it.

## Conventions

### Adding a new form (e.g. a signup, RSVP, order form)

Copy the contact form pattern exactly:

1. `database/migrations/000N_create_<table>.sql` — new table, following
   the style of `0001_create_contact_messages.sql` (InnoDB, utf8mb4,
   sensible `VARCHAR` lengths, `created_at DATETIME DEFAULT CURRENT_TIMESTAMP`).
   It's picked up and applied automatically — no separate migration step.
2. `backend/api/<name>.php` — copy `contact.php`: `apply_cors_headers()`,
   reject non-POST, honeypot check, `clean_str`/`is_valid_email`/
   `is_valid_phone` validation with a field-keyed `errors` object on 422,
   prepared-statement insert, generic try/catch returning a 500 with a
   safe message (never leak `$e->getMessage()` to the client).
3. A form in `index.html` (or a new page) using the existing `.form-card`/
   `.field-group`/`.error-msg` classes, plus a small JS file modeled on
   `assets/js/contact.js` using the shared helpers in `forms.js`.
4. `admin/<name>s.php` — copy `messages.php`: search, pagination, delete,
   detail modal (uses `admin/assets/admin.js`'s existing modal/search/
   delete-confirm wiring — don't rewrite it).
5. `admin/export_<name>s.php` — copy `export_messages.php` for CSV export.
6. Add a nav link in `admin/includes/header.php`.

### Admin accounts

`admin/users.php` lets a logged-in admin add or delete other admin
accounts (`admin_users` table). Two invariants are enforced server-side,
not just hidden in the UI — keep both if you touch this file:
never allow deleting the account currently logged in, and never let the
last remaining admin_users row be deleted (defense in depth even though,
in practice, the self-delete check already covers the single-admin case).

### Database

- Every new table is a new file in `database/migrations/`, numbered after
  the last one. Never edit an already-applied migration file — add a new
  one (e.g. an `ALTER TABLE` migration) instead.
- Keep one logical change per migration file, and avoid semicolons inside
  string/default literals — `Database::runPendingMigrations()` splits
  naively on `;`.
- `admin_users` and `schema_migrations` are core tables created directly
  in `Database.php`; don't touch them from a migration.

### Security (already handled — don't undo these)

- All admin pages start with `require_once '.../includes/auth.php';
  require_login();`. Never expose data or actions without that.
- All state-changing admin form posts must include and verify the CSRF
  token (`csrf_token()` / `verify_csrf()`).
- All SQL uses PDO prepared statements — never interpolate user input
  into a query string.
- All output in admin PHP templates goes through `e()` (htmlspecialchars).
- `backend/` is denied to direct web access except `backend/api/*.php`
  (enforced in `nginx.template.conf` and the `.htaccess` files, which are
  belt-and-braces for non-nginx hosts). Never put a page that should be
  browsable directly under `backend/`.
- Never commit `backend/db_credentials.php` (gitignored) — real values
  come from Coolify environment variables in production.

### Styling

- Current theme: light, Manrope font, slate neutrals (`--bg`, `--ink`,
  `--muted`), and a blue → violet → rose brand gradient (`--blue`,
  `--violet`, `--rose`, `--gradient-brand`) used on buttons, gradient
  text, and the top-accent border cycled across `.accent-card`s. All
  defined as CSS variables at the top of `assets/css/style.css` — change
  those, not individual component rules, to re-theme. `admin/assets/admin.css`
  has its own copy of the same palette (`--blue`, `--violet`, `--magenta`)
  and should be kept in sync if the brand colors change.
- Reuse existing component classes (`.btn`, `.card`, `.accent-card`,
  `.form-card`, `.icon-badge`, `.section`) before inventing new ones.
- After any styling/markup change, grep `assets/css/style.css` class
  selectors against `index.html` + `assets/js/*.js` + `admin/**/*.php` and
  delete any that are no longer referenced anywhere — don't let dead CSS
  accumulate.

## Local development

1. `cp backend/db_credentials.example.php backend/db_credentials.php` and
   point it at a local MySQL/MariaDB instance.
2. Serve the repo root with any PHP server, e.g.
   `php -S localhost:8000` from the project root.
3. First run creates `admin_users` and writes a one-time password to
   `backend/data/INITIAL_ADMIN_PASSWORD.txt` — log in at `/admin/`,
   change the password (Settings), then delete that file.

## Deployment (Coolify)

- This app deploys as a single PHP service — no separate build step, no
  Dockerfile needed. Coolify uses Nixpacks with the `providers = ["php"]`
  setting in `nixpacks.toml`, and `nginx.template.conf` supplies routing
  (public site, `/admin/`, and `backend/api/*.php` only).
- In the Coolify app's **Environment Variables**, set: `DB_HOST`,
  `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`. `APP_NAME` and
  `APP_TIMEZONE` are optional (see `backend/config.php`).
- Point Coolify at a managed/external MySQL or MariaDB database — this
  boilerplate doesn't bundle a database container.
- On first request after deploy, the app creates its own tables and a
  one-time admin login (see `backend/data/INITIAL_ADMIN_PASSWORD.txt` on
  the server) — there's no manual migration command to run.
- If a new top-level PHP directory is ever added (rare — most new
  features live under `admin/` or `backend/api/`), add a matching allow
  rule to `nginx.template.conf` the way `admin/` and `backend/api/` are
  handled, or it won't be reachable in production even though it works
  locally.
