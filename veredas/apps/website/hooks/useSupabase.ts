'use client'

// ============================================================
// useSupabase.ts — Typed Supabase client hook
// ============================================================
import { useMemo } from 'react'
import { createClient } from '@/utils/supabase/client'
import type { SupabaseClient } from '@supabase/supabase-js'
import type { Database } from '@/utils/supabase/types'

export function useSupabase(): SupabaseClient<Database> {
  return useMemo(() => createClient(), [])
}

// ── Convenience query hook ─────────────────────────────────

import { useState, useCallback } from 'react'
import type { TableName, RowOf } from '@/utils/supabase/types'

interface QueryState<T> {
  data: T | null
  loading: boolean
  error: string | null
}

interface UseQueryReturn<T> extends QueryState<T> {
  refetch: () => Promise<void>
}

export function useQuery<T extends TableName>(
  table: T,
  id: string | null,
): UseQueryReturn<RowOf<T>> {
  const supabase = useSupabase()
  const [state, setState] = useState<QueryState<RowOf<T>>>({
    data: null,
    loading: Boolean(id),
    error: null,
  })

  const refetch = useCallback(async () => {
    if (!id) return
    setState((prev) => ({ ...prev, loading: true, error: null }))
    const { data, error } = await supabase.from(table).select('*').eq('id', id).single()
    if (error) {
      setState({ data: null, loading: false, error: error.message })
    } else {
      setState({ data: data as RowOf<T>, loading: false, error: null })
    }
  }, [supabase, table, id])

  // Auto-fetch on mount when id is set
  useMemo(() => {
    if (id) refetch()
  }, [id]) // eslint-disable-line react-hooks/exhaustive-deps

  return { ...state, refetch }
}

interface UseListReturn<T> {
  data: T[]
  loading: boolean
  error: string | null
  refetch: () => Promise<void>
}

export function useList<T extends TableName>(
  table: T,
  filters: Partial<RowOf<T>> = {},
): UseListReturn<RowOf<T>> {
  const supabase = useSupabase()
  const filterKey = JSON.stringify(filters)
  const [state, setState] = useState<{ data: RowOf<T>[]; loading: boolean; error: string | null }>(
    { data: [], loading: true, error: null },
  )

  const refetch = useCallback(async () => {
    setState((prev) => ({ ...prev, loading: true, error: null }))
    let query = supabase.from(table).select('*')
    for (const [key, value] of Object.entries(filters)) {
      query = query.eq(key, value as string)
    }
    const { data, error } = await query
    if (error) {
      setState({ data: [], loading: false, error: error.message })
    } else {
      setState({ data: (data ?? []) as RowOf<T>[], loading: false, error: null })
    }
  }, [supabase, table, filterKey]) // eslint-disable-line react-hooks/exhaustive-deps

  useMemo(() => {
    refetch()
  }, [filterKey]) // eslint-disable-line react-hooks/exhaustive-deps

  return { ...state, refetch }
}
