# API Reference

Base URL: `https://api.yourdomain.com/api/v1`

All endpoints return JSON. Include `Accept: application/json` header.

Authentication: The token is stored in an **HttpOnly cookie** (`api_token`) set by the login endpoint. The browser sends it automatically. There is no Bearer token header for browser clients.

---

## Authentication

### POST /auth/login

Authenticate and receive an HttpOnly session cookie.

**Rate limit:** 10 requests/minute per IP

**Body:**
```json
{
  "email": "admin@example.com",
  "password": "secret",
  "remember": false
}
```

**Response 200:**
```json
{
  "user": {
    "id": "uuid",
    "name": "João Silva",
    "email": "admin@example.com",
    "role": "ADMIN",
    "role_label": "Administrador"
  }
}
```
*Cookie `api_token` is set automatically.*

**Errors:** `422` invalid credentials · `403` account deactivated

---

### POST /auth/logout *(auth required)*

Revokes the current session token and clears the cookie.

**Response 200:** `{ "message": "Sessão encerrada com sucesso." }`

---

### GET /auth/me *(auth required)*

Returns the authenticated user profile.

**Response 200:**
```json
{
  "id": "uuid",
  "name": "João Silva",
  "email": "admin@example.com",
  "role": "ADMIN",
  "role_label": "Administrador",
  "last_login_at": "2026-05-11T10:00:00+00:00"
}
```

---

## Dashboard

### GET /dashboard/stats *(auth required)*

Aggregated KPI cards for the dashboard.

**Response 200:**
```json
{
  "total_leads": 150,
  "new_leads_month": 12,
  "total_expeditions": 8,
  "active_expeditions": 3,
  "pending_tasks": 7,
  "total_revenue": 45000.00,
  "conversion_rate": 18.5,
  "leads_this_week": 5,
  "conversions_this_week": 2,
  "revenue_this_week": 6000.00,
  "recent_leads": [...],
  "upcoming_expeditions": [...]
}
```

### GET /dashboard/analytics *(auth required)*

Deep metrics: monthly revenue, conversion funnel, lead sources, top destinations.

---

## Expeditions

### GET /expeditions *(auth required)*

| Parameter | Type | Default | Description |
|---|---|---|---|
| `page` | int | 1 | Page number |
| `per_page` | int | 25 | Records per page (max 100) |
| `status` | string | — | Filter by status |
| `active` | bool | — | Only OPEN/GUARANTEED/IN_PROGRESS |

**Response 200:** Paginated list (`data` + `meta` + `links`)

### GET /expeditions/public *(auth required)*

Returns only `OPEN` and `GUARANTEED` expeditions.

### GET /expeditions/{id} *(auth required)*

**Response 200:** Full expedition object with `checklistItems` and `media` loaded.

### POST /expeditions *(ADMIN or OPERATOR)*

**Rate limit:** 30/min

**Body:**
```json
{
  "name": "Expedição Chapada",
  "destination": "Chapada dos Veadeiros",
  "dates": "15/07/2027 a 22/07/2027",
  "start_date": "2027-07-15",
  "end_date": "2027-07-22",
  "capacity": 12,
  "remaining_spots": 12,
  "accommodation": "Camping",
  "transport": "Van",
  "trail_level": "MODERATE",
  "costs": 5000.00,
  "margin_predicted": 2000.00
}
```

Valid `trail_level` values: `EASY` · `MODERATE` · `HARD` · `CHALLENGING`

Valid `status` values: `PLANNING` · `OPEN` · `GUARANTEED` · `IN_PROGRESS` · `COMPLETED` · `CANCELLED`

### PUT /expeditions/{id} *(ADMIN or OPERATOR)*

Partial or full update. Same fields as POST, all optional.

### DELETE /expeditions/{id} *(ADMIN or OPERATOR)*

**Returns 409 Conflict** if the expedition has active participants or pending checklist items.

On success: soft delete (sets `deleted_at`).

### PATCH /expeditions/{id}/status *(ADMIN or OPERATOR)*

```json
{ "status": "OPEN" }
```

---

## Leads / CRM

### GET /leads *(auth required)*

| Parameter | Type | Default | Description |
|---|---|---|---|
| `page` | int | 1 | Page number |
| `per_page` | int | 25 | Records per page (max 100) |
| `status` | string | — | Filter by status |
| `search` | string | — | Full-text search on name, destination |

Valid `status` values: `NEW` · `CONTACTED` · `QUALIFIED` · `PROPOSAL` · `RESERVED` · `PAID` · `POST_TRIP` · `REFERRAL`

### GET /leads/{id} *(auth required)*

### POST /leads *(ADMIN or OPERATOR)*

**Rate limit:** 30/min

### PUT /leads/{id} *(ADMIN or OPERATOR)*

### DELETE /leads/{id} *(ADMIN or OPERATOR)*

### PATCH /leads/{id}/status *(ADMIN or OPERATOR)*

```json
{ "status": "CONTACTED" }
```

### POST /leads/{id}/notes *(ADMIN or OPERATOR)*

```json
{ "body": "Cliente demonstrou interesse..." }
```

**Response 201:** Note object with `author` relation.

### PUT /leads/{id}/notes/{noteId} *(ADMIN or OPERATOR)*

Users can only edit their own notes. ADMIN/MASTER_ADMIN can edit any note.

### DELETE /leads/{id}/notes/{noteId} *(ADMIN or OPERATOR)*

Same ownership rules as edit.

---

## Users (MASTER_ADMIN only)

### GET /users

### POST /users

**Rate limit:** 20/min

```json
{
  "name": "Novo Admin",
  "email": "novo@example.com",
  "password": "SecurePass123!",
  "role": "ADMIN"
}
```

Cannot create `MASTER_ADMIN` via API.

### PUT /users/{id}

### DELETE /users/{id}

Cannot delete own account or MASTER_ADMIN.

### POST /users/{id}/reset-password

**Rate limit:** 10/min

```json
{
  "password": "NewPass123!",
  "password_confirmation": "NewPass123!"
}
```

### PATCH /users/{id}/toggle-active

Toggles `active` status. Cannot deactivate own account or MASTER_ADMIN.

---

## Checklist Items

### GET /checklist-items *(auth required)*
### GET /checklist-items/{id} *(auth required)*
### GET /expeditions/{id}/checklist *(auth required)*
### POST /checklist-items *(ADMIN or OPERATOR)*
### PUT /checklist-items/{id} *(ADMIN or OPERATOR)*
### DELETE /checklist-items/{id} *(ADMIN or OPERATOR)*
### PATCH /checklist-items/{id}/toggle *(ADMIN or OPERATOR)*

---

## Error Responses

```json
// 422 Validation Error
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}

// 403 Forbidden
{ "message": "Você não tem permissão para realizar esta ação." }

// 409 Conflict
{
  "message": "Não é possível excluir a expedição: existem dependências ativas.",
  "blockers": ["participants", "checklist_items"]
}

// 429 Too Many Requests
{ "message": "Too Many Attempts." }
```
