// ============================================================
// types/index.ts
// TypeScript types for the entire application.
// Maps 1:1 to the Supabase PostgreSQL schema.
// ============================================================

// ── Primitives ─────────────────────────────────────────────

export type Json = string | number | boolean | null | { [key: string]: Json } | Json[]

// ── Enums ──────────────────────────────────────────────────

export type UserRole = 'MASTER_ADMIN' | 'ADMIN' | 'OPERATOR' | 'GUIDE' | 'TRAVELER'

export type LeadStatus =
  | 'NEW'
  | 'CONTACTED'
  | 'QUALIFIED'
  | 'PROPOSAL'
  | 'RESERVED'
  | 'PAID'
  | 'POST_TRIP'
  | 'REFERRAL'

export type ExpeditionStatus = 'draft' | 'published' | 'ongoing' | 'completed' | 'cancelled'

export type TrailLevel = 'EASY' | 'MODERATE' | 'HARD' | 'CHALLENGING'

export type BookingStatus = 'pending' | 'confirmed' | 'cancelled' | 'completed'

export type PaymentStatus = 'pending' | 'paid' | 'refunded' | 'failed'

export type PaymentMethod = 'credit_card' | 'pix' | 'bank_transfer'

export type MediaType = 'image' | 'video' | 'document'

// ── Row types (mirror DB columns exactly) ──────────────────

export interface UserRow {
  id: string
  email: string
  full_name: string | null
  avatar_url: string | null
  role: UserRole
  is_active: boolean
  last_login_at: string | null
  created_at: string
  updated_at: string
}

export interface ExpeditionRow {
  id: string
  title: string
  slug: string | null
  description: string | null
  destination: string
  start_date: string
  end_date: string
  max_travelers: number
  current_travelers: number
  price_per_person: number
  currency: string
  cover_image_url: string | null
  guide_id: string | null
  accommodation: string | null
  transport: string | null
  trail_level: TrailLevel | null
  costs: number | null
  margin_predicted: number | null
  margin_real: number | null
  participants: Json
  status: ExpeditionStatus
  deleted_at: string | null
  created_at: string
  updated_at: string
}

export interface LeadRow {
  id: string
  name: string
  email: string | null
  phone: string | null
  instagram: string | null
  source: string | null
  interest: string | null
  destination: string | null
  date_desired: string | null
  people_count: number | null
  total_price: number | null
  status: LeadStatus
  notes: string | null
  last_contact: string | null
  next_follow_up: string | null
  tags: Json
  expedition_id: string | null
  assigned_to: string | null
  created_at: string
  updated_at: string
}

export interface LeadNoteRow {
  id: string
  lead_id: string
  author_id: string | null
  content: string
  created_at: string
  updated_at: string
}

export interface ChecklistItemRow {
  id: string
  expedition_id: string
  label: string
  is_done: boolean
  due_date: string | null
  assigned_to: string | null
  sort_order: number
  created_at: string
  updated_at: string
}

export interface MediaRow {
  id: string
  expedition_id: string | null
  uploaded_by: string | null
  url: string
  storage_path: string
  type: MediaType
  name: string
  size_bytes: number
  mime_type: string | null
  created_at: string
}

export interface BookingRow {
  id: string
  expedition_id: string
  traveler_id: string
  lead_id: string | null
  seats: number
  total_price: number
  currency: string
  status: BookingStatus
  notes: string | null
  created_at: string
  updated_at: string
}

export interface PaymentRow {
  id: string
  booking_id: string
  amount: number
  currency: string
  status: PaymentStatus
  method: PaymentMethod | null
  gateway_id: string | null
  paid_at: string | null
  created_at: string
  updated_at: string
}

// ── Insert / Update types ──────────────────────────────────

export type UserInsert = Omit<UserRow, 'created_at' | 'updated_at'>
export type UserUpdate = Partial<Omit<UserRow, 'id' | 'created_at' | 'updated_at'>>

export type ExpeditionInsert = Omit<ExpeditionRow, 'id' | 'created_at' | 'updated_at' | 'current_travelers' | 'deleted_at'>
export type ExpeditionUpdate = Partial<Omit<ExpeditionRow, 'id' | 'created_at' | 'updated_at'>>

export type LeadInsert = Omit<LeadRow, 'id' | 'created_at' | 'updated_at'>
export type LeadUpdate = Partial<Omit<LeadRow, 'id' | 'created_at' | 'updated_at'>>

export type LeadNoteInsert = Omit<LeadNoteRow, 'id' | 'created_at' | 'updated_at'>
export type LeadNoteUpdate = Partial<Omit<LeadNoteRow, 'id' | 'created_at' | 'updated_at'>>

export type ChecklistItemInsert = Omit<ChecklistItemRow, 'id' | 'created_at' | 'updated_at'>
export type ChecklistItemUpdate = Partial<Omit<ChecklistItemRow, 'id' | 'created_at' | 'updated_at'>>

export type MediaInsert = Omit<MediaRow, 'id' | 'created_at'>

export type BookingInsert = Omit<BookingRow, 'id' | 'created_at' | 'updated_at'>
export type BookingUpdate = Partial<Omit<BookingRow, 'id' | 'created_at' | 'updated_at'>>

export type PaymentInsert = Omit<PaymentRow, 'id' | 'created_at' | 'updated_at'>
export type PaymentUpdate = Partial<Omit<PaymentRow, 'id' | 'created_at' | 'updated_at'>>

// ── Supabase Database interface (for typed client) ─────────

export interface Database {
  public: {
    Tables: {
      users: {
        Row: UserRow
        Insert: UserInsert
        Update: UserUpdate
      }
      expeditions: {
        Row: ExpeditionRow
        Insert: ExpeditionInsert
        Update: ExpeditionUpdate
      }
      leads: {
        Row: LeadRow
        Insert: LeadInsert
        Update: LeadUpdate
      }
      lead_notes: {
        Row: LeadNoteRow
        Insert: LeadNoteInsert
        Update: LeadNoteUpdate
      }
      checklist_items: {
        Row: ChecklistItemRow
        Insert: ChecklistItemInsert
        Update: ChecklistItemUpdate
      }
      media: {
        Row: MediaRow
        Insert: MediaInsert
        Update: Partial<MediaInsert>
      }
      bookings: {
        Row: BookingRow
        Insert: BookingInsert
        Update: BookingUpdate
      }
      payments: {
        Row: PaymentRow
        Insert: PaymentInsert
        Update: PaymentUpdate
      }
    }
    Views: Record<string, never>
    Functions: {
      auth_user_role: {
        Args: Record<string, never>
        Returns: string
      }
    }
    Enums: Record<string, never>
  }
}

// ── Generic helpers ────────────────────────────────────────

export type TableName = keyof Database['public']['Tables']

export type RowOf<T extends TableName> = Database['public']['Tables'][T]['Row']

export interface PaginatedResult<T> {
  data: T[]
  count: number
  page: number
  pageSize: number
  totalPages: number
}

export interface ServiceResult<T> {
  data: T | null
  error: string | null
}

export interface ServiceListResult<T> {
  data: T[]
  error: string | null
}
