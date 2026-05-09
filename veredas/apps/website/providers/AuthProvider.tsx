'use client'

// ============================================================
// AuthProvider.tsx — Global auth context for client components
// ============================================================
import { createContext, useContext, useEffect, useState, type ReactNode } from 'react'
import type { Session, User } from '@supabase/supabase-js'
import { createClient } from '@/utils/supabase/client'
import type { UserRow } from '@/utils/supabase/types'

interface AuthContextValue {
  user: User | null
  session: Session | null
  profile: UserRow | null
  loading: boolean
  isAuthenticated: boolean
}

const AuthContext = createContext<AuthContextValue>({
  user: null,
  session: null,
  profile: null,
  loading: true,
  isAuthenticated: false,
})

interface AuthProviderProps {
  children: ReactNode
  initialSession?: Session | null
}

export function AuthProvider({ children, initialSession }: AuthProviderProps) {
  const [session, setSession] = useState<Session | null>(initialSession ?? null)
  const [profile, setProfile] = useState<UserRow | null>(null)
  const [loading, setLoading] = useState(!initialSession)

  const supabase = createClient()

  async function fetchProfile(userId: string) {
    const { data } = await supabase.from('users').select('*').eq('id', userId).single()
    if (data) setProfile(data as UserRow)
  }

  useEffect(() => {
    // Rehydrate session from server if not provided
    if (!initialSession) {
      supabase.auth.getSession().then(({ data: { session: s } }) => {
        setSession(s)
        if (s?.user) fetchProfile(s.user.id)
        setLoading(false)
      })
    } else if (initialSession?.user) {
      fetchProfile(initialSession.user.id)
      setLoading(false)
    }

    const {
      data: { subscription },
    } = supabase.auth.onAuthStateChange((_event, s) => {
      setSession(s)
      if (s?.user) {
        fetchProfile(s.user.id)
      } else {
        setProfile(null)
      }
      setLoading(false)
    })

    return () => subscription.unsubscribe()
  }, []) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <AuthContext.Provider
      value={{
        user: session?.user ?? null,
        session,
        profile,
        loading,
        isAuthenticated: Boolean(session?.user),
      }}
    >
      {children}
    </AuthContext.Provider>
  )
}

export function useAuthContext(): AuthContextValue {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuthContext must be used within <AuthProvider>')
  return ctx
}
