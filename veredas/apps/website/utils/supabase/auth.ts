// ============================================================
// auth.ts — Server-side auth helpers (App Router safe)
// ============================================================
import { createServerClient } from '@supabase/ssr'
import { cookies } from 'next/headers'
import type { Session, User, AuthError } from '@supabase/supabase-js'
import type { Database } from './types'

const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL!
const supabaseKey = process.env.NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY!

function createAuthClient() {
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
          // Called from Server Component — safe to ignore
        }
      },
    },
  })
}

export async function getSession(): Promise<Session | null> {
  const supabase = createAuthClient()
  const {
    data: { session },
  } = await supabase.auth.getSession()
  return session
}

export async function getUser(): Promise<User | null> {
  const supabase = createAuthClient()
  const {
    data: { user },
  } = await supabase.auth.getUser()
  return user
}

export async function requireAuth(): Promise<User> {
  const user = await getUser()
  if (!user) throw new Error('Unauthorized')
  return user
}

export interface SignInResult {
  session: Session | null
  user: User | null
  error: AuthError | null
}

export async function signInWithEmailPassword(
  email: string,
  password: string,
): Promise<SignInResult> {
  const supabase = createAuthClient()
  const { data, error } = await supabase.auth.signInWithPassword({ email, password })
  return { session: data.session, user: data.user, error }
}

export async function signUpWithEmailPassword(
  email: string,
  password: string,
  metadata?: Record<string, string>,
): Promise<SignInResult> {
  const supabase = createAuthClient()
  const { data, error } = await supabase.auth.signUp({
    email,
    password,
    options: { data: metadata },
  })
  return { session: data.session, user: data.user, error }
}

export async function signOut(): Promise<{ error: AuthError | null }> {
  const supabase = createAuthClient()
  const { error } = await supabase.auth.signOut()
  return { error }
}

export async function sendPasswordResetEmail(
  email: string,
): Promise<{ error: AuthError | null }> {
  const supabase = createAuthClient()
  const { error } = await supabase.auth.resetPasswordForEmail(email, {
    redirectTo: `${process.env.NEXT_PUBLIC_SITE_URL ?? 'http://localhost:3000'}/auth/reset-password`,
  })
  return { error }
}

export async function updatePassword(
  newPassword: string,
): Promise<{ error: AuthError | null }> {
  const supabase = createAuthClient()
  const { error } = await supabase.auth.updateUser({ password: newPassword })
  return { error }
}
