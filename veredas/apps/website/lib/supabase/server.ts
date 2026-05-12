// ============================================================
// lib/supabase/server.ts
// Server-side Supabase client (use in Server Components, Actions, Route Handlers)
// ============================================================
import { createServerClient } from '@supabase/ssr'
import { cookies } from 'next/headers'
import type { Database } from '@/types'

/**
 * Creates a Supabase client for use on the server.
 * Reads/writes cookies to keep the session in sync.
 *
 * Works in:
 *  - Server Components
 *  - Server Actions
 *  - Route Handlers (App Router)
 *
 * @example
 * const supabase = createClient()
 * const { data: { user } } = await supabase.auth.getUser()
 */
export function createClient() {
  const cookieStore = cookies()

  return createServerClient<Database>(
    process.env.NEXT_PUBLIC_SUPABASE_URL!,
    process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY!,
    {
      cookies: {
        getAll() {
          return (
            cookieStore as unknown as {
              getAll(): { name: string; value: string }[]
            }
          ).getAll()
        },
        setAll(cookiesToSet) {
          try {
            cookiesToSet.forEach(({ name, value, options }) =>
              (
                cookieStore as unknown as {
                  set(name: string, value: string, options: object): void
                }
              ).set(name, value, options),
            )
          } catch {
            // Called from a Server Component — safe to ignore.
            // Middleware will handle session refresh.
          }
        },
      },
    },
  )
}
