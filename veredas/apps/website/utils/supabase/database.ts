// ============================================================
// database.ts — Generic typed query helpers (server-side)
// ============================================================
import { cookies } from 'next/headers'
import { createServerClient } from '@supabase/ssr'
import type { Database, TableName, RowOf, PaginatedResult } from './types'

const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL!
const supabaseKey = process.env.NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY!

export function createDbClient() {
  const cookieStore = cookies()
  return createServerClient<Database>(supabaseUrl, supabaseKey, {
    cookies: {
      getAll() {
        return (cookieStore as unknown as { getAll(): { name: string; value: string }[] }).getAll()
      },
      setAll(cookiesToSet) {
        try {
          cookiesToSet.forEach(({ name, value, options }) =>
            (cookieStore as unknown as { set(name: string, value: string, options: object): void }).set(
              name,
              value,
              options,
            ),
          )
        } catch {
          // Server Component — safe to ignore
        }
      },
    },
  })
}

// ── Generic CRUD ───────────────────────────────────────────

export async function findById<T extends TableName>(
  table: T,
  id: string,
): Promise<RowOf<T> | null> {
  const db = createDbClient()
  const { data, error } = await db.from(table).select('*').eq('id', id).single()
  if (error) {
    console.error(`[db] findById(${table}, ${id}):`, error.message)
    return null
  }
  return data as RowOf<T>
}

export async function findMany<T extends TableName>(
  table: T,
  filters: Partial<RowOf<T>> = {},
): Promise<RowOf<T>[]> {
  const db = createDbClient()
  let query = db.from(table).select('*')
  for (const [key, value] of Object.entries(filters)) {
    query = query.eq(key, value as string)
  }
  const { data, error } = await query
  if (error) {
    console.error(`[db] findMany(${table}):`, error.message)
    return []
  }
  return (data ?? []) as RowOf<T>[]
}

export async function findPaginated<T extends TableName>(
  table: T,
  page: number = 1,
  pageSize: number = 20,
  filters: Partial<RowOf<T>> = {},
): Promise<PaginatedResult<RowOf<T>>> {
  const db = createDbClient()
  const from = (page - 1) * pageSize
  const to = from + pageSize - 1

  let query = db.from(table).select('*', { count: 'exact' }).range(from, to)
  for (const [key, value] of Object.entries(filters)) {
    query = query.eq(key, value as string)
  }

  const { data, error, count } = await query
  if (error) {
    console.error(`[db] findPaginated(${table}):`, error.message)
    return { data: [], count: 0, page, pageSize, totalPages: 0 }
  }

  const total = count ?? 0
  return {
    data: (data ?? []) as RowOf<T>[],
    count: total,
    page,
    pageSize,
    totalPages: Math.ceil(total / pageSize),
  }
}

export async function insertRow<T extends TableName>(
  table: T,
  payload: Database['public']['Tables'][T]['Insert'],
): Promise<RowOf<T> | null> {
  const db = createDbClient()
  const { data, error } = await db.from(table).insert(payload as never).select().single()
  if (error) {
    console.error(`[db] insertRow(${table}):`, error.message)
    return null
  }
  return data as RowOf<T>
}

export async function updateRow<T extends TableName>(
  table: T,
  id: string,
  payload: Database['public']['Tables'][T]['Update'],
): Promise<RowOf<T> | null> {
  const db = createDbClient()
  const { data, error } = await db
    .from(table)
    .update(payload as never)
    .eq('id', id)
    .select()
    .single()
  if (error) {
    console.error(`[db] updateRow(${table}, ${id}):`, error.message)
    return null
  }
  return data as RowOf<T>
}

export async function deleteRow<T extends TableName>(
  table: T,
  id: string,
): Promise<boolean> {
  const db = createDbClient()
  const { error } = await db.from(table).delete().eq('id', id)
  if (error) {
    console.error(`[db] deleteRow(${table}, ${id}):`, error.message)
    return false
  }
  return true
}
