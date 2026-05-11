# Architecture

## Overview

```
Browser / Mobile
      │
      ▼
Next.js 14 (veredas/apps/website)
   │  Supabase Auth (JWT cookie)
   │  Supabase JS SDK (DB reads)
   │
   ├─── Direct Supabase reads for high-frequency queries (dashboard, lists)
   │
   └─── Laravel 11 REST API (backend/)  ← for writes + business logic
             │  Sanctum HttpOnly cookie
             │
             ▼
         Supabase Postgres (shared DB)
```

## Stack Choices

| Layer | Technology | Rationale |
|---|---|---|
| API | Laravel 11 | Mature PHP ecosystem, Sanctum auth, Eloquent ORM |
| Frontend | Next.js 14 App Router | SSR, file-based routing, React Server Components |
| Database | Supabase Postgres | Managed Postgres, realtime, storage, RLS |
| Auth | Supabase Auth + Sanctum | Supabase handles OIDC; Sanctum guards the API |
| Monorepo | Turborepo | Shared packages (`@veredas/types`, `@veredas/ui`) |

## Authentication Flow

```
1. User submits email+password to POST /api/v1/auth/login
2. Laravel validates credentials, creates a Sanctum token
3. Token stored in HttpOnly + SameSite=Strict cookie (never in localStorage)
4. All subsequent API requests include the cookie automatically
5. ReadTokenFromCookie middleware extracts it for Sanctum
6. EnsureUserIsActive middleware blocks deactivated accounts
7. CheckRole middleware enforces role-based access
```

## Data Model

```
users (id, name, email, password, role, active, last_login_at)
  │
  ├── lead_notes (id, lead_id, user_id, body)
  │         ↑ many notes per lead, authored by users
  │
leads (id, name, whatsapp, source, destination, status, estimated_ticket, ...)
  │
expeditions (id, name, destination, status, capacity, participants[], ...)
  ├── checklist_items (id, expedition_id, title, status, assignee_id)
  └── media (id, expedition_id, url, type)
```

## Role Hierarchy

```
MASTER_ADMIN  →  Full access + user management
ADMIN         →  All operational writes
OPERATOR      →  All operational writes (same as ADMIN)
GUIDE         →  Read-only operational data
TRAVELER      →  Traveler portal only
```

## API Design Conventions

- All routes prefixed with `/api/v1/`
- JSON responses only (`Accept: application/json`)
- Paginated lists follow: `{ data: [...], meta: { current_page, per_page, total, last_page }, links: {...} }`
- Default page size: 25 rows · Maximum: 100 rows
- HTTP status codes: 200 OK, 201 Created, 204 No Content, 400 Bad Request, 401 Unauthorized, 403 Forbidden, 404 Not Found, 409 Conflict, 422 Unprocessable, 429 Too Many Requests
- Soft deletes on `expeditions` (physical deletes on `leads`, `lead_notes`, `users`)

## Monorepo Structure

```
veredas/
├── turbo.json              # Pipeline: build → lint → test
├── apps/
│   ├── website/            # Admin frontend (Next.js 14 + Tailwind)
│   ├── traveler-portal/    # Traveler-facing app
│   └── api/                # Optional BFF
└── packages/
    ├── types/              # @veredas/types — canonical domain types
    ├── ui/                 # @veredas/ui — shared React components
    ├── auth/               # Shared auth utilities
    ├── database/           # Supabase client factory
    └── shared/             # Utility helpers
```

## Security Posture

| Control | Implementation |
|---|---|
| Token storage | HttpOnly SameSite=Strict cookie |
| Rate limiting | Login: 10/min · Writes: 30/min · User mgmt: 20/min |
| Role enforcement | `CheckRole` middleware on all write routes |
| SQL injection | Eloquent ORM + parameterized queries only |
| XSS | JSON API; no server-rendered HTML with user content |
| CSRF | SameSite=Strict cookie; Sanctum CSRF token on SPA routes |
| SSL | CURLOPT_SSL_VERIFYPEER=true enforced |
| Host Header Injection | `session_handler.php` reads `APP_URL` from `.env` only |
| Credentials in code | Seeders generate random passwords; no hardcoded secrets |
