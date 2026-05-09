// ============================================================
// expeditionService.ts — Expedition CRUD + business logic
// ============================================================
import { createDbClient, findById, insertRow, updateRow, deleteRow, findPaginated } from '@/utils/supabase/database'
import type {
  ExpeditionRow,
  ExpeditionInsert,
  ExpeditionUpdate,
  ServiceResult,
  ServiceListResult,
  PaginatedResult,
} from '@/utils/supabase/types'

export async function getExpeditionById(id: string): Promise<ServiceResult<ExpeditionRow>> {
  const data = await findById('expeditions', id)
  return { data, error: data ? null : 'Expedição não encontrada' }
}

export async function listExpeditionsByAgency(
  agencyId: string,
  page = 1,
  pageSize = 20,
): Promise<PaginatedResult<ExpeditionRow>> {
  return findPaginated('expeditions', page, pageSize, {
    agency_id: agencyId,
  } as Partial<ExpeditionRow>)
}

export async function listPublishedExpeditions(
  page = 1,
  pageSize = 20,
): Promise<PaginatedResult<ExpeditionRow>> {
  const db = createDbClient()
  const from = (page - 1) * pageSize
  const { data, error, count } = await db
    .from('expeditions')
    .select('*', { count: 'exact' })
    .eq('status', 'published')
    .order('start_date', { ascending: true })
    .range(from, from + pageSize - 1)

  const total = count ?? 0
  return {
    data: data ?? [],
    count: total,
    page,
    pageSize,
    totalPages: Math.ceil(total / pageSize),
  }
}

export async function createExpedition(
  payload: ExpeditionInsert,
): Promise<ServiceResult<ExpeditionRow>> {
  const data = await insertRow('expeditions', payload)
  return { data, error: data ? null : 'Falha ao criar expedição' }
}

export async function updateExpedition(
  id: string,
  payload: ExpeditionUpdate,
): Promise<ServiceResult<ExpeditionRow>> {
  const data = await updateRow('expeditions', id, payload)
  return { data, error: data ? null : 'Falha ao atualizar expedição' }
}

export async function publishExpedition(id: string): Promise<ServiceResult<ExpeditionRow>> {
  return updateExpedition(id, { status: 'published' })
}

export async function cancelExpedition(id: string): Promise<ServiceResult<ExpeditionRow>> {
  return updateExpedition(id, { status: 'cancelled' })
}

export async function deleteExpedition(id: string): Promise<{ error: string | null }> {
  const ok = await deleteRow('expeditions', id)
  return { error: ok ? null : 'Falha ao deletar expedição' }
}

export async function searchExpeditions(
  query: string,
  agencyId?: string,
): Promise<ServiceListResult<ExpeditionRow>> {
  const db = createDbClient()
  let q = db
    .from('expeditions')
    .select('*')
    .or(`title.ilike.%${query}%,destination.ilike.%${query}%`)
    .eq('status', 'published')

  if (agencyId) q = q.eq('agency_id', agencyId)

  const { data, error, count } = await q
  return { data: data ?? [], error: error?.message ?? null, count: count ?? 0 }
}
