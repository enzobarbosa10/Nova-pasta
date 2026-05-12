// @ts-nocheck
// ============================================================
// userService.ts — User & profile operations
// ============================================================
import { createDbClient, findById, updateRow } from '@/utils/supabase/database'
import type {
  UserRow,
  UserUpdate,
  ServiceResult,
  ServiceListResult,
} from '@/utils/supabase/types'

export async function getUserById(id: string): Promise<ServiceResult<UserRow>> {
  const data = await findById('users', id)
  return { data, error: data ? null : 'Usuário não encontrado' }
}

export async function getUserByEmail(email: string): Promise<ServiceResult<UserRow>> {
  const db = createDbClient()
  const { data, error } = await db
    .from('users')
    .select('*')
    .eq('email', email)
    .single()
  return { data: data ?? null, error: error?.message ?? null }
}

export async function updateUserProfile(
  id: string,
  payload: UserUpdate,
): Promise<ServiceResult<UserRow>> {
  const data = await updateRow('users', id, payload)
  return { data, error: data ? null : 'Falha ao atualizar perfil' }
}

export async function listUsersByAgency(agencyId: string): Promise<ServiceListResult<UserRow>> {
  const db = createDbClient()
  const { data, error, count } = await db
    .from('users')
    .select('*', { count: 'exact' })
    .eq('agency_id', agencyId)
    .order('full_name', { ascending: true })
  return { data: data ?? [], error: error?.message ?? null, count: count ?? 0 }
}

export async function deactivateUser(id: string): Promise<ServiceResult<UserRow>> {
  return updateUserProfile(id, { role: 'traveler' })
}
