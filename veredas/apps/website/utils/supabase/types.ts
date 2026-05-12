// ============================================================
// Database Types — mirrors ALL 23 Supabase public schema tables
// Auto-mapped from live OpenAPI schema
// ============================================================

export type Json = string | number | boolean | null | { [key: string]: Json } | Json[]

// ── Row types ──────────────────────────────────────────────

export interface UserRow {
  id: string
  email: string
  full_name: string | null
  avatar_url: string | null
  role: 'admin' | 'agency_owner' | 'agent' | 'traveler'
  agency_id: string | null
  created_at: string
  updated_at: string
}

export interface AgencyRow {
  id: string
  name: string
  slug: string
  logo_url: string | null
  description: string | null
  owner_id: string
  plan: 'starter' | 'professional' | 'enterprise'
  is_active: boolean
  created_at: string
  updated_at: string
}

export interface ExpeditionRow {
  id: string
  agency_id: string
  title: string
  slug: string
  description: string | null
  destination: string
  start_date: string
  end_date: string
  max_travelers: number
  current_travelers: number
  price_per_person: number
  currency: string
  status: 'draft' | 'published' | 'ongoing' | 'completed' | 'cancelled'
  cover_image_url: string | null
  created_at: string
  updated_at: string
}

export interface TravelerRow {
  id: string
  user_id: string
  full_name: string
  email: string
  phone: string | null
  document_number: string | null
  document_type: 'cpf' | 'passport' | 'rg' | null
  birth_date: string | null
  emergency_contact: string | null
  created_at: string
  updated_at: string
}

export interface BookingRow {
  id: string
  expedition_id: string
  traveler_id: string
  agency_id: string
  status: 'pending' | 'confirmed' | 'cancelled' | 'completed'
  seats: number
  total_price: number
  currency: string
  notes: string | null
  created_at: string
  updated_at: string
}

export interface PaymentRow {
  id: string
  booking_id: string
  amount: number
  currency: string
  status: 'pending' | 'paid' | 'refunded' | 'failed'
  method: 'credit_card' | 'pix' | 'bank_transfer' | null
  gateway_id: string | null
  paid_at: string | null
  created_at: string
  updated_at: string
}

export interface LeadRow {
  id: string
  agency_id: string
  expedition_id: string | null
  name: string
  email: string
  phone: string | null
  message: string | null
  source: string | null
  destination: string | null
  total_price: number | null
  status: 'NEW' | 'CONTACTED' | 'QUALIFIED' | 'PROPOSAL' | 'RESERVED' | 'PAID' | 'POST_TRIP' | 'REFERRAL' | 'new' | 'contacted' | 'qualified' | 'converted' | 'lost'
  created_at: string
  updated_at: string
}

export interface MediaRow {
  id: string
  agency_id: string
  expedition_id: string | null
  url: string
  storage_path: string
  type: 'image' | 'video' | 'document'
  name: string
  size_bytes: number
  mime_type: string
  created_at: string
}

export interface ChecklistRow {
  id: string
  expedition_id: string
  title: string
  items: ChecklistItem[]
  is_required: boolean
  created_at: string
  updated_at: string
}

export interface ChecklistItem {
  id: string
  label: string
  completed: boolean
  required: boolean
}

// ── Insert types (omit auto-generated fields) ──────────────

export type UserInsert = Omit<UserRow, 'id' | 'created_at' | 'updated_at'>
export type AgencyInsert = Omit<AgencyRow, 'id' | 'created_at' | 'updated_at'>
export type ExpeditionInsert = Omit<ExpeditionRow, 'id' | 'created_at' | 'updated_at'>
export type TravelerInsert = Omit<TravelerRow, 'id' | 'created_at' | 'updated_at'>
export type BookingInsert = Omit<BookingRow, 'id' | 'created_at' | 'updated_at'>
export type PaymentInsert = Omit<PaymentRow, 'id' | 'created_at' | 'updated_at'>
export type LeadInsert = Omit<LeadRow, 'id' | 'created_at' | 'updated_at'>
export type MediaInsert = Omit<MediaRow, 'id' | 'created_at'>
export type ChecklistInsert = Omit<ChecklistRow, 'id' | 'created_at' | 'updated_at'>

// ── Update types (all fields optional except id) ───────────

export type UserUpdate = Partial<UserInsert>
export type AgencyUpdate = Partial<AgencyInsert>
export type ExpeditionUpdate = Partial<ExpeditionInsert>
export type TravelerUpdate = Partial<TravelerInsert>
export type BookingUpdate = Partial<BookingInsert>
export type PaymentUpdate = Partial<PaymentInsert>
export type LeadUpdate = Partial<LeadInsert>
export type ChecklistUpdate = Partial<ChecklistInsert>

// ── New table rows (14 additional) ────────────────────────

export interface CommissionRow {
  id: string
  agency_id: string
  booking_id: string
  user_id: string
  type: string
  rate: number
  amount: number
  status: string
  paid_at: string | null
  notes: string | null
  created_at: string
  updated_at: string
}

export interface ConversationParticipantRow {
  id: string
  conversation_id: string
  user_id: string
  role: string
  last_read_at: string | null
  muted: boolean
  joined_at: string
}

export interface AgencyMemberRow {
  id: string
  agency_id: string
  user_id: string
  role: string
  invited_by: string | null
  accepted_at: string | null
  created_at: string
}

export interface VehicleRow {
  id: string
  agency_id: string
  name: string
  type: string
  plate: string | null
  capacity: number | null
  year: number | null
  brand: string | null
  model: string | null
  notes: string | null
  status: string
  created_at: string
  updated_at: string
}

export interface TransactionRow {
  id: string
  agency_id: string
  booking_id: string | null
  payment_id: string | null
  type: string
  amount: number
  currency: string
  description: string | null
  category: string | null
  reference_date: string | null
  created_at: string
}

export interface ActivityLogRow {
  id: string
  actor_id: string | null
  entity_type: string
  entity_id: string | null
  action: string
  old_data: Json | null
  new_data: Json | null
  ip_address: string | null
  user_agent: string | null
  metadata: Json | null
  agency_id: string | null
  created_at: string
}

export interface RefundRow {
  id: string
  payment_id: string
  booking_id: string
  requested_by: string
  amount: number
  reason: string | null
  status: string
  provider_ref: string | null
  processed_at: string | null
  created_at: string
  updated_at: string
}

export interface GuideRow {
  id: string
  user_id: string
  agency_id: string
  bio: string | null
  specialties: string[]
  languages: string[]
  certifications: string[]
  rating: number | null
  status: string
  created_at: string
  updated_at: string
}

export interface MessageRow {
  id: string
  sender_id: string
  receiver_id: string | null
  booking_id: string | null
  content: string
  read: boolean
  read_at: string | null
  created_at: string
  agency_id: string | null
  conversation_id: string | null
}

export interface DocumentRow {
  id: string
  entity_type: string
  entity_id: string
  uploaded_by: string
  title: string
  type: string
  url: string
  status: string
  expires_at: string | null
  created_at: string
  updated_at: string
  agency_id: string | null
}

export interface ExpenseRow {
  id: string
  agency_id: string
  expedition_id: string | null
  created_by: string
  title: string
  category: string | null
  amount: number
  currency: string
  paid_at: string | null
  receipt_url: string | null
  notes: string | null
  created_at: string
  updated_at: string
}

export interface ItineraryRow {
  id: string
  expedition_id: string
  day_number: number
  title: string
  description: string | null
  location: string | null
  accommodation: string | null
  meals: string[]
  distance_km: number | null
  elevation_m: number | null
  created_at: string
  updated_at: string
}

export interface NotificationRow {
  id: string
  user_id: string
  type: string
  title: string
  body: string
  data: Json | null
  read: boolean
  read_at: string | null
  created_at: string
  agency_id: string | null
}

export interface ConversationRow {
  id: string
  type: string
  title: string | null
  agency_id: string | null
  expedition_id: string | null
  booking_id: string | null
  last_message_at: string | null
  created_by: string
  created_at: string
  updated_at: string
}

// ── Insert types for new tables ────────────────────────────

export type CommissionInsert = Omit<CommissionRow, 'id' | 'created_at' | 'updated_at'>
export type ConversationParticipantInsert = Omit<ConversationParticipantRow, 'id'>
export type AgencyMemberInsert = Omit<AgencyMemberRow, 'id' | 'created_at'>
export type VehicleInsert = Omit<VehicleRow, 'id' | 'created_at' | 'updated_at'>
export type TransactionInsert = Omit<TransactionRow, 'id' | 'created_at'>
export type ActivityLogInsert = Omit<ActivityLogRow, 'id' | 'created_at'>
export type RefundInsert = Omit<RefundRow, 'id' | 'created_at' | 'updated_at'>
export type GuideInsert = Omit<GuideRow, 'id' | 'created_at' | 'updated_at'>
export type MessageInsert = Omit<MessageRow, 'id' | 'created_at'>
export type DocumentInsert = Omit<DocumentRow, 'id' | 'created_at' | 'updated_at'>
export type ExpenseInsert = Omit<ExpenseRow, 'id' | 'created_at' | 'updated_at'>
export type ItineraryInsert = Omit<ItineraryRow, 'id' | 'created_at' | 'updated_at'>
export type NotificationInsert = Omit<NotificationRow, 'id' | 'created_at'>
export type ConversationInsert = Omit<ConversationRow, 'id' | 'created_at' | 'updated_at'>

// ── Update types for new tables ────────────────────────────

export type CommissionUpdate = Partial<CommissionInsert>
export type VehicleUpdate = Partial<VehicleInsert>
export type RefundUpdate = Partial<RefundInsert>
export type GuideUpdate = Partial<GuideInsert>
export type DocumentUpdate = Partial<DocumentInsert>
export type ExpenseUpdate = Partial<ExpenseInsert>
export type ItineraryUpdate = Partial<ItineraryInsert>
export type ConversationUpdate = Partial<ConversationInsert>

export interface Database {
  public: {
    Tables: {
      users: { Row: UserRow; Insert: UserInsert; Update: UserUpdate }
      agencies: { Row: AgencyRow; Insert: AgencyInsert; Update: AgencyUpdate }
      agency_members: { Row: AgencyMemberRow; Insert: AgencyMemberInsert; Update: Partial<AgencyMemberInsert> }
      expeditions: { Row: ExpeditionRow; Insert: ExpeditionInsert; Update: ExpeditionUpdate }
      travelers: { Row: TravelerRow; Insert: TravelerInsert; Update: TravelerUpdate }
      bookings: { Row: BookingRow; Insert: BookingInsert; Update: BookingUpdate }
      payments: { Row: PaymentRow; Insert: PaymentInsert; Update: PaymentUpdate }
      commissions: { Row: CommissionRow; Insert: CommissionInsert; Update: CommissionUpdate }
      refunds: { Row: RefundRow; Insert: RefundInsert; Update: RefundUpdate }
      transactions: { Row: TransactionRow; Insert: TransactionInsert; Update: Partial<TransactionInsert> }
      leads: { Row: LeadRow; Insert: LeadInsert; Update: LeadUpdate }
      guides: { Row: GuideRow; Insert: GuideInsert; Update: GuideUpdate }
      vehicles: { Row: VehicleRow; Insert: VehicleInsert; Update: VehicleUpdate }
      itineraries: { Row: ItineraryRow; Insert: ItineraryInsert; Update: ItineraryUpdate }
      checklists: { Row: ChecklistRow; Insert: ChecklistInsert; Update: ChecklistUpdate }
      expenses: { Row: ExpenseRow; Insert: ExpenseInsert; Update: ExpenseUpdate }
      documents: { Row: DocumentRow; Insert: DocumentInsert; Update: DocumentUpdate }
      media: { Row: MediaRow; Insert: MediaInsert; Update: Partial<MediaInsert> }
      conversations: { Row: ConversationRow; Insert: ConversationInsert; Update: ConversationUpdate }
      conversation_participants: { Row: ConversationParticipantRow; Insert: ConversationParticipantInsert; Update: Partial<ConversationParticipantInsert> }
      messages: { Row: MessageRow; Insert: MessageInsert; Update: Partial<MessageInsert> }
      notifications: { Row: NotificationRow; Insert: NotificationInsert; Update: Partial<NotificationInsert> }
      activity_logs: { Row: ActivityLogRow; Insert: ActivityLogInsert; Update: Partial<ActivityLogInsert> }
    }
    Views: Record<string, never>
    Functions: {
      check_expedition_availability: { Args: { expedition_id: string }; Returns: boolean }
      has_agency_role: { Args: { agency_id: string; role: string }; Returns: boolean }
      log_activity: { Args: { entity_type: string; entity_id: string; action: string }; Returns: void }
    }
    Enums: {
      user_role: 'admin' | 'agency_owner' | 'agent' | 'traveler'
      agency_plan: 'starter' | 'professional' | 'enterprise'
      expedition_status: 'draft' | 'published' | 'ongoing' | 'completed' | 'cancelled'
      booking_status: 'pending' | 'confirmed' | 'cancelled' | 'completed'
      payment_status: 'pending' | 'paid' | 'refunded' | 'failed'
      lead_status: 'new' | 'contacted' | 'qualified' | 'converted' | 'lost'
    }
  }
}

// ── Utility types ──────────────────────────────────────────

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
  count: number
}
