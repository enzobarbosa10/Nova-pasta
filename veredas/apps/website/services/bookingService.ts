// ============================================================
// bookingService.ts — Booking & payment operations
// ============================================================
import { createDbClient, findById, insertRow, updateRow, findPaginated } from '@/utils/supabase/database'
import type {
  BookingRow,
  BookingInsert,
  BookingUpdate,
  PaymentRow,
  PaymentInsert,
  ServiceResult,
  PaginatedResult,
} from '@/utils/supabase/types'

export async function getBookingById(id: string): Promise<ServiceResult<BookingRow>> {
  const data = await findById('bookings', id)
  return { data, error: data ? null : 'Reserva não encontrada' }
}

export async function listBookingsByAgency(
  agencyId: string,
  page = 1,
  pageSize = 20,
): Promise<PaginatedResult<BookingRow>> {
  return findPaginated('bookings', page, pageSize, {
    agency_id: agencyId,
  } as Partial<BookingRow>)
}

export async function listBookingsByExpedition(
  expeditionId: string,
): Promise<{ data: BookingRow[]; error: string | null }> {
  const db = createDbClient()
  const { data, error } = await db
    .from('bookings')
    .select('*')
    .eq('expedition_id', expeditionId)
    .order('created_at', { ascending: false })
  return { data: data ?? [], error: error?.message ?? null }
}

export async function createBooking(
  payload: BookingInsert,
): Promise<ServiceResult<BookingRow>> {
  const db = createDbClient()

  // Check available seats before booking
  const { data: expedition } = await db
    .from('expeditions')
    .select('max_travelers, current_travelers')
    .eq('id', payload.expedition_id)
    .single()

  if (!expedition) return { data: null, error: 'Expedição não encontrada' }

  const available = expedition.max_travelers - expedition.current_travelers
  if (available < payload.seats) {
    return { data: null, error: `Apenas ${available} vagas disponíveis` }
  }

  const data = await insertRow('bookings', payload)
  if (!data) return { data: null, error: 'Falha ao criar reserva' }

  // Update seat count
  await db
    .from('expeditions')
    .update({ current_travelers: expedition.current_travelers + payload.seats })
    .eq('id', payload.expedition_id)

  return { data, error: null }
}

export async function confirmBooking(id: string): Promise<ServiceResult<BookingRow>> {
  const data = await updateRow('bookings', id, { status: 'confirmed' } as BookingUpdate)
  return { data, error: data ? null : 'Falha ao confirmar reserva' }
}

export async function cancelBooking(
  id: string,
): Promise<ServiceResult<BookingRow>> {
  const db = createDbClient()
  const booking = await findById('bookings', id)
  if (!booking) return { data: null, error: 'Reserva não encontrada' }

  const data = await updateRow('bookings', id, { status: 'cancelled' } as BookingUpdate)
  if (!data) return { data: null, error: 'Falha ao cancelar reserva' }

  // Release seats
  const { data: expedition } = await db
    .from('expeditions')
    .select('current_travelers')
    .eq('id', booking.expedition_id)
    .single()

  if (expedition) {
    await db
      .from('expeditions')
      .update({
        current_travelers: Math.max(0, expedition.current_travelers - booking.seats),
      })
      .eq('id', booking.expedition_id)
  }

  return { data, error: null }
}

// ── Payments ───────────────────────────────────────────────

export async function getPaymentsByBooking(
  bookingId: string,
): Promise<{ data: PaymentRow[]; error: string | null }> {
  const db = createDbClient()
  const { data, error } = await db
    .from('payments')
    .select('*')
    .eq('booking_id', bookingId)
    .order('created_at', { ascending: false })
  return { data: data ?? [], error: error?.message ?? null }
}

export async function registerPayment(
  payload: PaymentInsert,
): Promise<ServiceResult<PaymentRow>> {
  const data = await insertRow('payments', payload)
  if (!data) return { data: null, error: 'Falha ao registrar pagamento' }

  if (payload.status === 'paid') {
    await updateRow('bookings', payload.booking_id, { status: 'confirmed' } as BookingUpdate)
  }

  return { data, error: null }
}
