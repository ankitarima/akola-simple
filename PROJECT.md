<!--
  This describes Akola Simple itself. Unlike a typical PROJECT.md, this one
  isn't a spec for something to build on top of the boilerplate — this repo
  IS Akola Simple, and its landing page (index.html) is a showcase of the
  boilerplate's own features. Edit this file whenever that showcase page's
  scope changes, so it stays the source of truth.

  See AGENT.md for how a *future* project spun up from this boilerplate
  should use this file instead (to describe that project, not Akola Simple).
-->

# Akola Simple

A PHP + MySQL boilerplate for spinning up new small sites/apps fast: a
landing page, a working contact-to-admin pipeline, session-based admin
auth, and Coolify deployment config, all wired together out of the box.
The landing page at `index.html` is a showcase of the boilerplate
itself — its copy should describe what the repo does, not a fictional
company.

## Goal

A landing page that explains what Akola Simple provides (as a
boilerplate/template), demonstrates the contact-form-to-admin pipeline
live, and links out to the admin panel.

## Pages / Sections

- Home (single page): header/nav, hero, Features (6 cards: landing page,
  contact pipeline, auto-migrations, admin panel, Coolify deploy,
  AGENT.md build workflow), How It Works (the 4-step
  describe → supply → build → deploy workflow), Why Akola Simple (3
  cards), a stats band of real facts about the repo, and a Live Demo
  section that is the actual working contact form.

## Forms & Data

- **Contact form** (framed as "Live Demo"): full name, email, phone
  (optional), message → stored in `contact_messages`, visible/searchable/
  exportable in the admin panel at `/admin/messages.php`. This is a real
  feature of the boilerplate, not just a lead-gen form — the landing page
  copy should say so.

## Admin Panel

- Dashboard: total messages, 7-day chart, recent messages.
- Messages: search, pagination, delete, detail view, CSV export.
- Admin Users: any logged-in admin can add or delete other admin
  accounts. Can't delete your own account or the last remaining admin.
- Settings: change own password.

## Branding

- Name: Akola Simple
- Theme: light background (`#f8fafc`), near-black ink text, blue
  (`#1b82f4`) → violet (`#8f65b1`) → rose (`#bb4c89`) gradient accent,
  Manrope font, pill buttons, subtle grid pattern in the hero — inspired
  by [Akola Digital](https://akoladigital.com)'s visual language, applied
  to a boilerplate's own landing page rather than a business's.
- Logo: text wordmark only so far — no logo file provided yet.

## Content

Copy lives in `assets/js/config.js` (`SITE_CONFIG`): `features`,
`process`, `whyUs`, `stats`, and `demo` all describe the boilerplate's
real capabilities. Every stat in the dark band (`0` npm packages, `3`
steps, `1` command to deploy, `100%` PHP) is a factual claim about this
repo, not a fabricated business metric — keep it that way when editing.

## Infrastructure

- Database: MySQL on Hostinger (`u157609732_akola_simple`), credentials
  in `backend/db_credentials.php` (gitignored, local-only) — production
  Coolify deployment should set the same values as `DB_HOST`/`DB_PORT`/
  `DB_NAME`/`DB_USER`/`DB_PASS` environment variables instead.
- Deployed via Coolify (Nixpacks, PHP provider) per `AGENT.md`.

## Anything Else

- No fake testimonials, client logos, or business metrics — this page
  sells the boilerplate on what it actually does, demonstrated live via
  the working contact form, not on invented social proof.
