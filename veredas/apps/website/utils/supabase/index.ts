// ============================================================
// utils/supabase/index.ts — Barrel export
// ============================================================
export { createClient as createBrowserClient } from './client'
export { createClient as createServerSupabaseClient } from './server'
export * from './types'
export * from './auth'
export * from './database'
export * from './storage'
export * from './realtime'
