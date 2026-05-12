// ============================================================
// lib/auth/helpers.ts
// Server-side auth utilities for App Router
// ============================================================
import { redirect } from 'next/navigation'
import { createClient } from '@/lib/supabase/server'
import type { User } from '@supabase/supabase-js'
import type { UserRow, UserRole } from '@/types'

// ── Session helpers ─────────────────────────────────────────

/**
 * Returns the currently authenticated Supabase Auth user, or null.
 * Always uses getUser() (validates JWT with Supabase) — never getSession()
 * which only reads from the cookie without verification.
 */
export async function getAuthUser(): Promise<User | null> {
  const supabase = createClient()
  const {
    data: { user },
  } = await supabase.auth.getUser()
  return user
}

/**
 * Returns the public.users profile row for the current user, or null.
 */
export async function getUserProfile(): Promise<UserRow | null> {
  const supabase = createClient()
  const {
    data: { user },
  } = await supabase.auth.getUser()
  if (!user) return null

  const { data } = await supabase
    .from('users')
    .select('*')
    .eq('id', user.id)
    .single()

  return data as UserRow | null
}

// ── Protection helpers ──────────────────────────────────────

/**
 * Throws a redirect to /login if the user is not authenticated.
 * Use at the top of Server Components / Server Actions.
 *
 * @example
 * export default async function ProtectedPage() {
 *   const user = await requireAuth()
 *   ...
 * }
 */
export async function requireAuth(redirectTo = '/login'): Promise<User> {
  const user = await getAuthUser()
  if (!user) redirect(redirectTo)
  return user
}

/**
 * Requires the user to have one of the allowed roles.
 * Redirects to /dashboard if the role is insufficient.
 */
export async function requireRole(
  allowedRoles: UserRole[],
  redirectTo = '/dashboard',
): Promise<UserRow> {
  const supabase = createClient()
  const {
    data: { user },
  } = await supabase.auth.getUser()
  if (!user) redirect('/login')

  const { data: profile } = await supabase
    .from('users')
    .select('*')
    .eq('id', user.id)
    .single()

  const p = profile as UserRow | null
  if (!p || !allowedRoles.includes(p.role)) redirect(redirectTo)
  return p
}

// ── Auth actions (call from Server Actions) ─────────────────

export interface AuthResult {
  error: string | null
}

/**
 * Signs in a user with email + password.
 * Returns an error string on failure, null on success.
 */
export async function signInWithPassword(
  email: string,
  password: string,
): Promise<AuthResult> {
  const supabase = createClient()
  const { error } = await supabase.auth.signInWithPassword({ email, password })
  return { error: error?.message ?? null }
}

/**
 * Sends a magic link to the given email address.
 */
export async function sendMagicLink(email: string, redirectTo?: string): Promise<AuthResult> {
  const supabase = createClient()
  const { error } = await supabase.auth.signInWithOtp({
    email,
    options: { emailRedirectTo: redirectTo ?? process.env.NEXT_PUBLIC_SITE_URL + '/auth/callback' },
  })
  return { error: error?.message ?? null }
}

/**
 * Signs out the current user.
 */
export async function signOut(): Promise<void> {
  const supabase = createClient()
  await supabase.auth.signOut()
}

/**
 * Sends a password-reset email.
 */
export async function sendPasswordReset(email: string): Promise<AuthResult> {
  const supabase = createClient()
  const { error } = await supabase.auth.resetPasswordForEmail(email, {
    redirectTo: (process.env.NEXT_PUBLIC_SITE_URL ?? 'http://localhost:3000') + '/auth/reset',
  })
  return { error: error?.message ?? null }
}
