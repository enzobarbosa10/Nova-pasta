'use client'

// ============================================================
// useRealtime.ts — Realtime subscription hook
// ============================================================
import { useEffect, useRef, useState } from 'react'
import type { RealtimeChannel } from '@supabase/supabase-js'
import { createClient } from '@/utils/supabase/client'
import type { TableName, RowOf } from '@/utils/supabase/types'
import type { RealtimeEvent } from '@/utils/supabase/realtime'

interface UseRealtimeOptions<T extends TableName> {
  table: T
  event?: RealtimeEvent
  filter?: string
  onInsert?: (row: RowOf<T>) => void
  onUpdate?: (row: RowOf<T>, oldRow: RowOf<T>) => void
  onDelete?: (oldRow: RowOf<T>) => void
  enabled?: boolean
}

export function useRealtime<T extends TableName>(
  options: UseRealtimeOptions<T>,
): { connected: boolean } {
  const { table, event = '*', filter, onInsert, onUpdate, onDelete, enabled = true } = options
  const channelRef = useRef<RealtimeChannel | null>(null)
  const [connected, setConnected] = useState(false)

  useEffect(() => {
    if (!enabled) return

    const supabase = createClient()
    const channelName = `realtime:public:${table}:${filter ?? 'all'}`

    channelRef.current = supabase
      .channel(channelName)
      .on(
        'postgres_changes' as Parameters<RealtimeChannel['on']>[0],
        { event, schema: 'public', table, filter },
        (payload) => {
          const p = payload as {
            eventType: 'INSERT' | 'UPDATE' | 'DELETE'
            new: RowOf<T>
            old: RowOf<T>
          }
          if (p.eventType === 'INSERT') onInsert?.(p.new)
          else if (p.eventType === 'UPDATE') onUpdate?.(p.new, p.old)
          else if (p.eventType === 'DELETE') onDelete?.(p.old)
        },
      )
      .subscribe((status) => {
        setConnected(status === 'SUBSCRIBED')
      })

    return () => {
      if (channelRef.current) {
        supabase.removeChannel(channelRef.current)
        channelRef.current = null
        setConnected(false)
      }
    }
  }, [table, event, filter, enabled]) // eslint-disable-line react-hooks/exhaustive-deps

  return { connected }
}

// ── Convenience: live list hook ────────────────────────────

export function useRealtimeList<T extends TableName>(
  table: T,
  initialData: RowOf<T>[] = [],
  filter?: string,
): { items: RowOf<T>[]; connected: boolean } {
  const [items, setItems] = useState<RowOf<T>[]>(initialData)

  const { connected } = useRealtime({
    table,
    filter,
    onInsert: (row) => setItems((prev) => [row, ...prev]),
    onUpdate: (row) =>
      setItems((prev) => prev.map((item) => ((item as { id: string }).id === (row as { id: string }).id ? row : item))),
    onDelete: (oldRow) =>
      setItems((prev) => prev.filter((item) => (item as { id: string }).id !== (oldRow as { id: string }).id)),
  })

  return { items, connected }
}
