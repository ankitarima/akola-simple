# PHP + MySQL Boilerplate

A ready-to-deploy starter: a landing page, an example contact form
(API + database + admin inbox), and a session-authenticated admin panel —
built on plain PHP/MySQL with no framework or build step, pre-configured
for one-click deployment on [Coolify](https://coolify.io).

## Quick start (new project from this template)

1. Fill in **`PROJECT.md`** with what you want built.
2. Drop any copy, images, or reference files into **`data/`**.
3. Tell Claude Code to **"build"**. It reads both and generates the real
   site on top of this skeleton — see `AGENT.md` for exactly what it does.
4. Deploy: push to a repo, connect it to a Coolify app, set the `DB_*`
   environment variables, and deploy. No Dockerfile or extra config needed.

## Local development

```
cp backend/db_credentials.example.php backend/db_credentials.php
# edit it with your local MySQL/MariaDB details
php -S localhost:8000
```

Visit `http://localhost:8000` for the site and `http://localhost:8000/admin/`
for the admin panel. The first request creates the database tables and a
one-time admin password, written to `backend/data/INITIAL_ADMIN_PASSWORD.txt`.

## Docs

- **`AGENT.md`** — architecture, conventions, and the full build workflow.
- **`PROJECT.md`** — the spec template you fill in per project.
