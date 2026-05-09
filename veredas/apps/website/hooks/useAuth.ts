'use client'

// ============================================================
// useAuth.ts — Client-side authentication hook
// ============================================================
import { useCallback, useEffect, useState } from 'react'
import type { Session, User, AuthError } from '@supabase/supabase-js'
import { createClient } from '@/utils/supabase/client'

interface AuthState {
  user: User | null
  session: Session | null
  loading: boolean
  error: string | null
}

interface UseAuthReturn extends AuthState {
  signIn: (email: string, password: string) => Promise<{ error: string | null }>
  signUp: (
    email: string,
    password: string,
    metadata?: Record<string, string>,
  ) => Promise<{ error: string | null }>
  signOut: () => Promise<void>
  sendPasswordReset: (email: string) => Promise<{ error: string | null }>
  clearError: () => void
}

export function useAuth(): UseAuthReturn {
  const [state, setState] = useState<AuthState>({
    user: null,
    session: null,
    loading: true,
    error: null,
  })

  const supabase = createClient()

  useEffect(() => {
    // Initial session load
    supabase.auth.getSession().then(({ data: { session } }) => {
      setState((prev) => ({
        ...prev,
        session,
        user: session?.user ?? null,
        loading: false,
      }))
    })

    // Listen for auth state changes
    const {
      data: { subscription },
    } = supabase.auth.onAuthStateChange((_event, session) => {
      setState((prev) => ({
        ...prev,
        session,
        user: session?.user ?? null,
        loading: false,
        error: null,
      }))
    })

    return () => subscription.unsubscribe()
  }, []) // eslint-disable-line react-hooks/exhaustive-deps

  const signIn = useCallback(
    async (email: string, password: string): Promise<{ error: string | null }> => {
      setState((prev) => ({ ...prev, loading: true, error: null }))
      const { error } = await supabase.auth.signInWithPassword({ email, password })
      if (error) {
        setState((prev) => ({ ...prev, loading: false, error: error.message }))
        return { error: error.message }
      }
      setState((prev) => ({ ...prev, loading: false }))
      return { error: null }
    },
    [supabase],
  )

  const signUp = useCallback(
    async (
      email: string,
      password: string,
      metadata?: Record<string, string>,
    ): Promise<{ error: string | null }> => {
      setState((prev) => ({ ...prev, loading: true, error: null }))
      const { error } = await supabase.auth.signUp({
        email,
        password,
        options: { data: metadata },
      })
      if (error) {
        setState((prev) => ({ ...prev, loading: false, error: error.message }))
        return { error: error.message }
      }
      setState((prev) => ({ ...prev, loading: false }))
      return { error: null }
    },
    [supabase],
  )

  const signOut = useCallback(async (): Promise<void> => {
    setState((prev) => ({ ...prev, loading: true }))
    await supabase.auth.signOut()
    setState({ user: null, session: null, loading: false, error: null })
  }, [supabase])

  const sendPasswordReset = useCallback(
    async (email: string): Promise<{ error: string | null }> => {
      const { error } = await supabase.auth.resetPasswordForEmail(email, {
        redirectTo: `${window.location.origin}/auth/reset-password`,
      })
      return { error: error ? error.message : null }
    },
    [supabase],
  )

  const clearError = useCallback((): void => {
    setState((prev) => ({ ...prev, error: null }))
  }, [])

  return { ...state, signIn, signUp, signOut, sendPasswordReset, clearError }
}
