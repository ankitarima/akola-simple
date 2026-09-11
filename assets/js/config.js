/**
 * Single source of truth for site copy shown on the landing page. Update
 * here and every element with a matching [data-site] attribute, or a
 * [data-*-grid] rendering hook, updates automatically (see main.js).
 *
 * This page describes Akola Simple itself — the boilerplate — not a
 * fictional company. Keep copy factual and specific to what the repo
 * actually does; don't invent business metrics or testimonials.
 */
const SITE_CONFIG = {
  name: "Akola Simple",
  badge: "Open-source PHP + MySQL boilerplate",
  tagline: "From a spec to a live site, without the setup tax.",
  description:
    "Akola Simple is a batteries-included starting point: a landing page, a working contact-to-admin pipeline, session-based auth, and one-command Coolify deployment — already wired together, so a new project starts from a working app instead of a blank folder.",
  ctaLabel: "Try the live demo",
  ctaHref: "#demo",
  secondaryCtaLabel: "See features",
  secondaryCtaHref: "#features",
  apiBase: "backend/api",
  contactEmail: "hello@akolasimple.com",
  repo: {
    url: "https://github.com/ankitarima/akola-simple",
    label: "ankitarima/akola-simple",
  },
  socials: {
    twitter: "",
    linkedin: "",
    github: "https://github.com/ankitarima/akola-simple",
  },
  nav: [
    { label: "Features", href: "#features" },
    { label: "Get Started", href: "#get-started" },
    { label: "How It Works", href: "#how-it-works" },
    { label: "Why Akola Simple", href: "#why-us" },
    { label: "Live Demo", href: "#demo" },
  ],

  heroStats: [
    { value: "PHP 8.1+", label: "Backend" },
    { value: "MySQL", label: "Database" },
    { value: "Coolify", label: "Deploy target" },
  ],

  heroVisual: {
    label: "Akola Simple, at a glance",
    nodes: [
      { title: "Landing page", caption: "Config-driven copy" },
      { title: "Contact API", caption: "Validate + store" },
      { title: "Admin panel", caption: "Auth + CSV export" },
      { title: "Coolify deploy", caption: "Nixpacks + nginx" },
    ],
    centerTitle: "One repo, ready to ship",
    centerCaption: "Landing page, contact pipeline, admin panel, and deployment config — already wired together.",
  },

  features: [
    {
      icon: "🖥",
      title: "Zero-Config Landing Page",
      description: "Edit one JS file (SITE_CONFIG) to change copy, nav, and section content — no rebuild, no bundler, just refresh.",
      tags: ["No build step", "Plain JS"],
      linkLabel: "See it in action",
    },
    {
      icon: "📬",
      title: "Contact Form, End to End",
      description: "A working example wired all the way through: validated form → JSON API → MySQL table → searchable admin inbox.",
      tags: ["Validation", "CSRF", "CSV export"],
      linkLabel: "Try the live demo",
    },
    {
      icon: "🗄",
      title: "Auto-Migrating Database",
      description: "Drop a numbered .sql file in database/migrations/ and it runs itself on the next request — no CLI step, no deploy hook.",
      tags: ["MySQL", "No ORM"],
      linkLabel: "How migrations work",
    },
    {
      icon: "🔐",
      title: "Admin Panel Included",
      description: "Session auth, CSRF protection, and full admin-user management — add or remove admins from the UI, not the database.",
      tags: ["Auth", "Multi-admin"],
      linkLabel: "See the admin panel",
    },
    {
      icon: "🚀",
      title: "One-Push Coolify Deploy",
      description: "A Nixpacks config and an nginx template are already wired up — connect a repo to Coolify, set the env vars, and deploy.",
      tags: ["Nixpacks", "No Docker"],
      linkLabel: "Deployment details",
    },
    {
      icon: "🤖",
      title: "AGENT.md-Driven Builds",
      description: "Describe a new project in PROJECT.md, drop reference material in data/, and say \"build\" — Claude does the rest.",
      tags: ["PROJECT.md", "data/"],
      linkLabel: "Read the workflow",
    },
  ],

  gettingStarted: {
    heading: "Clone it and run it",
    description: "No build step, no framework install — just PHP and MySQL.",
    commands: [
      "git clone https://github.com/ankitarima/akola-simple.git",
      "cd akola-simple",
      "cp backend/db_credentials.example.php backend/db_credentials.php",
      "php -S localhost:8000",
    ],
    steps: [
      { title: "Point it at a database", description: "Edit backend/db_credentials.php with your local MySQL/MariaDB details." },
      { title: "Open the site", description: "http://localhost:8000 — the landing page and admin panel both work immediately, no setup step." },
      { title: "Grab your admin password", description: "The first request creates admin_users and writes a one-time password to backend/data/INITIAL_ADMIN_PASSWORD.txt." },
      { title: "Deploy when ready", description: "Push to a Coolify app — Nixpacks and the included nginx config handle the rest. Full guide in AGENT.md." },
    ],
  },

  process: [
    {
      title: "Describe",
      description: "Fill out PROJECT.md with what you want built — pages, forms, branding, whatever you know so far.",
    },
    {
      title: "Supply",
      description: "Drop copy, images, CSVs, or briefs into data/ — anything Claude should treat as real source material.",
    },
    {
      title: "Build",
      description: "Type \"build\". Claude reads AGENT.md's conventions and turns the spec into real pages, forms, and admin views.",
    },
    {
      title: "Deploy",
      description: "Push to Coolify. The included Nixpacks config and nginx template handle routing — no extra setup.",
    },
  ],

  whyUs: [
    {
      icon: "⚡",
      title: "Fast by default",
      description: "Zero build step, zero bundler — edit a file and refresh. Ship a first draft in minutes, not sprints.",
    },
    {
      icon: "🔒",
      title: "Built to last",
      description: "Clean, documented PHP and a real admin panel — not a black box, and not locked to any framework.",
    },
    {
      icon: "📈",
      title: "Scales with you",
      description: "Start with a landing page, grow into forms, tables, and multi-admin access — same repo, no rebuild.",
    },
  ],

  stats: [
    { figure: "0", caption: "npm packages required" },
    { figure: "3", caption: "Steps: spec → data → build" },
    { figure: "1", caption: "Command to deploy" },
    { figure: "100%", caption: "PHP + MySQL, no bundler" },
  ],

  demo: {
    heading: "Try the live demo",
    description: "This form is wired to a real database — submit it and the entry shows up in the admin inbox immediately.",
    steps: [
      { icon: "✅", title: "Validate", description: "Checked server-side in backend/api/contact.php with clean_str, is_valid_email, is_valid_phone." },
      { icon: "🗄", title: "Store", description: "Inserted via a PDO prepared statement into the contact_messages table." },
      { icon: "👀", title: "Review", description: "Shows up instantly on the admin dashboard and in the searchable messages list." },
    ],
  },
};
