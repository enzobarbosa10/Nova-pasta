// ============================================================
// realtime.ts — Realtime subscription helpers (client-side)
// ============================================================
import type { RealtimeChannel, RealtimePostgresChangesPayload } from '@supabase/supabase-js'
import { createClient } from './client'
import type { Database, TableName, RowOf } from './types'

export type RealtimeEvent = 'INSERT' | 'UPDATE' | 'DELETE' | '*'

export interface SubscriptionOptions<T extends TableName> {
  table: T
  event?: RealtimeEvent
  schema?: string
  filter?: string
  onInsert?: (row: RowOf<T>) => void
  onUpdate?: (row: RowOf<T>, oldRow: RowOf<T>) => void
  onDelete?: (oldRow: RowOf<T>) => void
  onChange?: (payload: RealtimePostgresChangesPayload<RowOf<T>>) => void
}

export function subscribeToTable<T extends TableName>(
  options: SubscriptionOptions<T>,
): RealtimeChannel {
  const supabase = createClient()
  const {
    table,
    event = '*',
    schema = 'public',
    filter,
    onInsert,
    onUpdate,
    onDelete,
    onChange,
  } = options

  const channel = supabase
    .channel(`realtime:${schema}:${table}:${filter ?? 'all'}`)
    .on(
      'postgres_changes' as Parameters<RealtimeChannel['on']>[0],
      {
        event,
        schema,
        table,
        filter,
      },
      (payload: RealtimePostgresChangesPayload<RowOf<T>>) => {
        onChange?.(payload)

        if (payload.eventType === 'INSERT' && onInsert) {
          onInsert(payload.new as RowOf<T>)
        } else if (payload.eventType === 'UPDATE' && onUpdate) {
          onUpdate(payload.new as RowOf<T>, payload.old as RowOf<T>)
        } else if (payload.eventType === 'DELETE' && onDelete) {
          onDelete(payload.old as RowOf<T>)
        }
      },
    )
    .subscribe()

  return channel
}

export function unsubscribe(channel: RealtimeChannel): void {
  const supabase = createClient()
  supabase.removeChannel(channel)
}

// ── Convenience helpers ────────────────────────────────────

export function subscribeToExpeditions(
  callbacks: Pick<SubscriptionOptions<'expeditions'>, 'onInsert' | 'onUpdate' | 'onDelete'>,
): RealtimeChannel {
  return subscribeToTable({ table: 'expeditions', ...callbacks })
}

export function subscribeToBookings(
  agencyId: string,
  callbacks: Pick<SubscriptionOptions<'bookings'>, 'onInsert' | 'onUpdate'>,
): RealtimeChannel {
  return subscribeToTable({
    table: 'bookings',
    filter: `agency_id=eq.${agencyId}`,
    ...callbacks,
  })
}

export function subscribeToLeads(
  agencyId: string,
  onInsert: (lead: Database['public']['Tables']['leads']['Row']) => void,
): RealtimeChannel {
  return subscribeToTable({
    table: 'leads',
    event: 'INSERT',
    filter: `agency_id=eq.${agencyId}`,
    onInsert,
  })
}
