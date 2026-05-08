# API Documentation - Expedition Management System

## Base URL
```
http://localhost:8000/api/v1
```

## Authentication

This API uses Laravel Sanctum for authentication. Most endpoints require authentication via Bearer token.

### Headers
```
Authorization: Bearer {your_token}
Content-Type: application/json
Accept: application/json
```

---

## Leads API

### List Leads
```http
GET /leads
```

**Query Parameters:**
- `status` (optional): Filter by status (NEW, CONTACTED, QUALIFIED, PROPOSAL, RESERVED, PAID, POST_TRIP, REFERRAL)
- `upcoming` (optional): Boolean to filter upcoming follow-ups
- `search` (optional): Search by name, whatsapp, or destination

**Response:**
```json
[
  {
    "id": "uuid",
    "name": "Ana Silva",
    "whatsapp": "+55 11 99999-9999",
    "instagram": "@anasilva",
    "source": "Instagram",
    "interest": "Chapada Diamantina",
    "destination": "Vale do Pati",
    "date_desired": "2024-07-15",
    "people_count": 2,
    "estimated_ticket": 4500.00,
    "status": "NEW",
    "notes": "Interessada em trekking de luxo.",
    "last_contact": "2024-05-01",
    "next_follow_up": "2024-05-08",
    "tags": ["Premium", "Trekking"],
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
]
```

### Create Lead
```http
POST /leads
```

**Request Body:**
```json
{
  "name": "Bruno Costa",
  "whatsapp": "+55 21 98888-8888",
  "instagram": "@brunocosta",
  "source": "Indicação",
  "interest": "Pantanal",
  "destination": "Safari Nobre",
  "date_desired": "2024-08-20",
  "people_count": 4,
  "estimated_ticket": 12000.00,
  "status": "NEW",
  "notes": "Família com crianças.",
  "last_contact": "2024-04-28",
  "next_follow_up": "2024-05-05",
  "tags": ["Family", "Safari"]
}
```

### Get Lead by ID
```http
GET /leads/{id}
```

### Update Lead
```http
PUT /leads/{id}
```

**Request Body:** Same as Create Lead (all fields optional)

### Delete Lead
```http
DELETE /leads/{id}
```

### Update Lead Status
```http
PATCH /leads/{id}/status
```

**Request Body:**
```json
{
  "status": "CONTACTED"
}
```

### Add Note to Lead
```http
POST /leads/{id}/notes
```

**Request Body:**
```json
{
  "note": "Cliente confirmou interesse na expedição"
}
```

---

## Expeditions API

### List Expeditions
```http
GET /expeditions
```

**Query Parameters:**
- `status` (optional): Filter by status (PLANNING, OPEN, GUARANTEED, IN_PROGRESS, COMPLETED, CANCELLED)
- `active` (optional): Boolean to filter active expeditions

**Response:**
```json
[
  {
    "id": "uuid",
    "name": "Chapada Raiz: Edição Fundadora",
    "cover_image": "https://example.com/image.jpg",
    "destination": "Palmeiras, BA",
    "dates": "15 - 20 Julho, 2024",
    "capacity": 8,
    "remaining_spots": 3,
    "guide_id": "uuid",
    "accommodation": "Pousada Boutique do Vale",
    "transport": "4x4 Premium",
    "trail_level": "MODERATE",
    "status": "OPEN",
    "costs": 15000.00,
    "margin_predicted": 45.00,
    "margin_real": null,
    "participants": ["uuid1", "uuid2"],
    "checklist_items": [],
    "media": []
  }
]
```

### List Public Expeditions
```http
GET /expeditions/public
```

Returns expeditions with status OPEN or GUARANTEED (no authentication required).

### Create Expedition
```http
POST /expeditions
```

**Request Body:**
```json
{
  "name": "Nova Expedição",
  "cover_image": "https://example.com/image.jpg",
  "destination": "Chapada Diamantina",
  "dates": "15 - 20 Julho, 2024",
  "capacity": 10,
  "remaining_spots": 10,
  "guide_id": "uuid",
  "accommodation": "Pousada Local",
  "transport": "Van",
  "trail_level": "MODERATE",
  "status": "PLANNING",
  "costs": 20000.00,
  "margin_predicted": 40.00,
  "participants": []
}
```

### Get Expedition by ID
```http
GET /expeditions/{id}
```

### Update Expedition
```http
PUT /expeditions/{id}
```

### Delete Expedition
```http
DELETE /expeditions/{id}
```

### Update Expedition Status
```http
PATCH /expeditions/{id}/status
```

**Request Body:**
```json
{
  "status": "OPEN"
}
```

### Add Participant
```http
POST /expeditions/{id}/participants
```

**Request Body:**
```json
{
  "traveler_id": "uuid"
}
```

### Remove Participant
```http
DELETE /expeditions/{id}/participants/{participantId}
```

---

## Checklist Items API

### List Checklist Items
```http
GET /checklist-items
```

**Query Parameters:**
- `status` (optional): PENDING or DONE
- `category` (optional): PRE, DURING, or POST
- `expedition_id` (optional): Filter by expedition

**Response:**
```json
[
  {
    "id": "uuid",
    "task": "Confirmar reserva de pousada",
    "category": "PRE",
    "status": "PENDING",
    "expedition_id": "uuid",
    "assigned_to": "uuid",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
]
```

### Create Checklist Item
```http
POST /checklist-items
```

**Request Body:**
```json
{
  "task": "Confirmar transporte",
  "category": "PRE",
  "status": "PENDING",
  "expedition_id": "uuid",
  "assigned_to": "uuid"
}
```

### Get Checklist Item by ID
```http
GET /checklist-items/{id}
```

### Update Checklist Item
```http
PUT /checklist-items/{id}
```

### Delete Checklist Item
```http
DELETE /checklist-items/{id}
```

### Toggle Item Status
```http
PATCH /checklist-items/{id}/toggle
```

Toggles between PENDING and DONE.

### Get Checklist by Expedition
```http
GET /expeditions/{expeditionId}/checklist
```

---

## Media API

### List Media
```http
GET /media
```

**Query Parameters:**
- `type` (optional): PHOTO, VIDEO, DRONE, REEL, STORY, REVIEW
- `expedition_id` (optional): Filter by expedition
- `tag` (optional): Filter by tag

**Response:**
```json
[
  {
    "id": "uuid",
    "url": "https://example.com/photo.jpg",
    "type": "PHOTO",
    "expedition_id": "uuid",
    "tags": ["aventura", "natureza"],
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
]
```

### Create Media
```http
POST /media
```

**Request Body:**
```json
{
  "url": "https://example.com/video.mp4",
  "type": "VIDEO",
  "expedition_id": "uuid",
  "tags": ["aventura", "drone"]
}
```

### Get Media by ID
```http
GET /media/{id}
```

### Update Media
```http
PUT /media/{id}
```

### Delete Media
```http
DELETE /media/{id}
```

### Bulk Upload Media
```http
POST /media/bulk-upload
```

**Request Body:**
```json
{
  "media": [
    {
      "url": "https://example.com/photo1.jpg",
      "type": "PHOTO",
      "expedition_id": "uuid",
      "tags": ["natureza"]
    },
    {
      "url": "https://example.com/photo2.jpg",
      "type": "PHOTO",
      "expedition_id": "uuid",
      "tags": ["aventura"]
    }
  ]
}
```

### Get Media by Expedition
```http
GET /expeditions/{expeditionId}/media
```

---

## Traveler Portal API

### Get Traveler Data
```http
GET /traveler-portal/{travelerId}
```

**Response:**
```json
{
  "traveler": {
    "id": "uuid",
    "name": "João Silva",
    "email": "joao@example.com"
  },
  "expeditions": [...],
  "itinerary": [...],
  "documents": [...],
  "personal_checklist": [...]
}
```

### Get Traveler Itinerary
```http
GET /traveler-portal/{travelerId}/itinerary
```

### Get Traveler Documents
```http
GET /traveler-portal/{travelerId}/documents
```

---

## Dashboard API

### Get Dashboard Stats
```http
GET /dashboard/stats
```

**Response:**
```json
{
  "total_leads": 45,
  "total_expeditions": 12,
  "active_expeditions": 5,
  "pending_tasks": 23
}
```

---

## Status Codes

- `200 OK` - Request successful
- `201 Created` - Resource created successfully
- `400 Bad Request` - Invalid request data
- `401 Unauthorized` - Authentication required
- `403 Forbidden` - Insufficient permissions
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation error
- `500 Internal Server Error` - Server error

## Error Response Format

```json
{
  "message": "Error message",
  "errors": {
    "field": ["Error description"]
  }
}
```

## Rate Limiting

The API implements rate limiting to prevent abuse. Default limits:
- 60 requests per minute for authenticated users
- 30 requests per minute for unauthenticated users
