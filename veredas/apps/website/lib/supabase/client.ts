// ============================================================
// lib/supabase/client.ts
// Browser-side Supabase client (use in Client Components & hooks)
// ============================================================
import { createBrowserClient } from '@supabase/ssr'
import type { Database } from '@/types'

/**
 * Creates a Supabase client for use in browser (Client Components).
 * Call this inside components — it reuses the singleton under the hood.
 *
 * @example
 * const supabase = createClient()
 * const { data } = await supabase.from('leads').select('*')
 */
export function createClient() {
  return createBrowserClient<Database>(
    process.env.NEXT_PUBLIC_SUPABASE_URL!,
    process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY!,
  )
}
