// ============================================================
// @veredas/types — Unified domain types shared across all apps
// Source of truth for all Turborepo packages
// ============================================================

// ── User / Auth ──────────────────────────────────────────────

export type UserRole = 'MASTER_ADMIN' | 'ADMIN' | 'OPERATOR' | 'GUIDE' | 'TRAVELER'

export interface User {
  id: string
  name: string
  email: string
  role: UserRole
  active: boolean
  last_login_at: string | null
  created_at: string
  updated_at: string
}

// ── Expedition ───────────────────────────────────────────────

export type ExpeditionStatus =
  | 'PLANNING'
  | 'OPEN'
  | 'GUARANTEED'
  | 'IN_PROGRESS'
  | 'COMPLETED'
  | 'CANCELLED'

export type TrailLevel = 'EASY' | 'MODERATE' | 'HARD' | 'CHALLENGING'

export interface Expedition {
  id: string
  name: string
  cover_image: string | null
  destination: string
  dates: string
  start_date: string | null
  end_date: string | null
  capacity: number
  remaining_spots: number
  guide_id: string | null
  accommodation: string
  transport: string
  trail_level: TrailLevel
  status: ExpeditionStatus
  costs: number
  margin_predicted: number
  margin_real: number | null
  participants: string[]
  created_at: string
  updated_at: string
  deleted_at: string | null
}

export interface ExpeditionCreate
  extends Omit<Expedition, 'id' | 'created_at' | 'updated_at' | 'deleted_at'> {}

export type ExpeditionUpdate = Partial<ExpeditionCreate>

// ── Lead / CRM ───────────────────────────────────────────────

export type LeadStatus =
  | 'NEW'
  | 'CONTACTED'
  | 'QUALIFIED'
  | 'PROPOSAL'
  | 'RESERVED'
  | 'PAID'
  | 'POST_TRIP'
  | 'REFERRAL'

export type LeadSource = 'INSTAGRAM' | 'WHATSAPP' | 'REFERRAL' | 'WEBSITE' | 'EVENT' | string

export interface Lead {
  id: string
  name: string
  whatsapp: string
  instagram: string | null
  source: LeadSource
  interest: string
  destination: string
  date_desired: string
  people_count: number
  estimated_ticket: number
  status: LeadStatus
  notes: string | null
  last_contact: string
  next_follow_up: string
  tags: string[]
  created_at: string
  updated_at: string
}

export interface LeadCreate
  extends Omit<Lead, 'id' | 'created_at' | 'updated_at'> {}

export type LeadUpdate = Partial<LeadCreate>

// ── LeadNote ─────────────────────────────────────────────────

export interface LeadNote {
  id: string
  lead_id: string
  user_id: string
  body: string
  created_at: string
  updated_at: string
  author?: Pick<User, 'id' | 'name'>
}

// ── ChecklistItem ─────────────────────────────────────────────

export type ChecklistStatus = 'PENDING' | 'IN_PROGRESS' | 'DONE'

export interface ChecklistItem {
  id: string
  expedition_id: string
  title: string
  description: string | null
  status: ChecklistStatus
  assignee_id: string | null
  due_date: string | null
  created_at: string
  updated_at: string
}

// ── Pagination ────────────────────────────────────────────────

export interface PaginatedResponse<T> {
  data: T[]
  meta: {
    current_page: number
    per_page: number
    total: number
    last_page: number
    from: number | null
    to: number | null
  }
  links: {
    first: string | null
    last: string | null
    prev: string | null
    next: string | null
  }
}

// ── API responses ─────────────────────────────────────────────

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}

export interface DashboardStats {
  total_leads: number
  new_leads_month: number
  total_expeditions: number
  active_expeditions: number
  pending_tasks: number
  total_revenue: number
  conversion_rate: number
  leads_this_week: number
  conversions_this_week: number
  revenue_this_week: number
  recent_leads: Pick<Lead, 'id' | 'name' | 'status' | 'destination' | 'source' | 'created_at'>[]
  upcoming_expeditions: Pick<Expedition, 'id' | 'name' | 'destination' | 'dates' | 'start_date' | 'end_date' | 'status' | 'capacity' | 'remaining_spots'>[]
}
