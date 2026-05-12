'use server'
// ============================================================
// app/(app)/expeditions/actions.ts
// Server Actions for Expedition CRUD — replaces Laravel ExpeditionController
// ============================================================
import { revalidatePath } from 'next/cache'
import { redirect } from 'next/navigation'
import { createClient } from '@/lib/supabase/server'
import { requireAuth } from '@/lib/auth/helpers'
import type { ExpeditionInsert, ExpeditionUpdate } from '@/types'

// ── CREATE ──────────────────────────────────────────────────

export async function createExpedition(formData: FormData) {
  await requireAuth()
  const supabase = createClient()

  const payload: ExpeditionInsert = {
    title:           formData.get('title') as string,
    description:     (formData.get('description') as string) || null,
    destination:     formData.get('destination') as string,
    start_date:      formData.get('start_date') as string,
    end_date:        formData.get('end_date') as string,
    max_travelers:   Number(formData.get('max_travelers')),
    price_per_person: Number(formData.get('price_per_person')),
    currency:        'BRL',
    cover_image_url: (formData.get('cover_image_url') as string) || null,
    guide_id:        (formData.get('guide_id') as string) || null,
    accommodation:   (formData.get('accommodation') as string) || null,
    transport:       (formData.get('transport') as string) || null,
    trail_level:     (formData.get('trail_level') as ExpeditionInsert['trail_level']) || null,
    costs:           formData.get('costs') ? Number(formData.get('costs')) : null,
    margin_predicted: formData.get('margin_predicted') ? Number(formData.get('margin_predicted')) : null,
    margin_real:     null,
    participants:    [],
    status:          'draft',
    slug:            null,
  }

  const { error } = await supabase.from('expeditions').insert(payload)
  if (error) {
    redirect(`/expeditions/new?error=${encodeURIComponent(error.message)}`)
  }

  revalidatePath('/expeditions')
  redirect('/expeditions')
}

// ── UPDATE ──────────────────────────────────────────────────

export async function updateExpedition(id: string, formData: FormData) {
  await requireAuth()
  const supabase = createClient()

  const payload: ExpeditionUpdate = {
    title:           (formData.get('title') as string) || undefined,
    description:     (formData.get('description') as string) || null,
    destination:     (formData.get('destination') as string) || undefined,
    start_date:      (formData.get('start_date') as string) || undefined,
    end_date:        (formData.get('end_date') as string) || undefined,
    max_travelers:   formData.get('max_travelers') ? Number(formData.get('max_travelers')) : undefined,
    price_per_person: formData.get('price_per_person') ? Number(formData.get('price_per_person')) : undefined,
    cover_image_url: (formData.get('cover_image_url') as string) || null,
    guide_id:        (formData.get('guide_id') as string) || null,
    accommodation:   (formData.get('accommodation') as string) || null,
    transport:       (formData.get('transport') as string) || null,
    trail_level:     (formData.get('trail_level') as ExpeditionUpdate['trail_level']) || null,
    costs:           formData.get('costs') ? Number(formData.get('costs')) : null,
    margin_predicted: formData.get('margin_predicted') ? Number(formData.get('margin_predicted')) : null,
    margin_real:     formData.get('margin_real') ? Number(formData.get('margin_real')) : null,
  }

  const { error } = await supabase.from('expeditions').update(payload).eq('id', id)
  if (error) {
    redirect(`/expeditions/${id}/edit?error=${encodeURIComponent(error.message)}`)
  }

  revalidatePath('/expeditions')
  revalidatePath(`/expeditions/${id}`)
  redirect(`/expeditions/${id}`)
}

// ── UPDATE STATUS (publish/cancel/etc.) ─────────────────────

export async function updateExpeditionStatus(
  id: string,
  status: ExpeditionUpdate['status'],
) {
  await requireAuth()
  const supabase = createClient()

  const { error } = await supabase.from('expeditions').update({ status }).eq('id', id)
  if (error) throw new Error(error.message)

  revalidatePath('/expeditions')
  revalidatePath(`/expeditions/${id}`)
}

// ── SOFT DELETE ─────────────────────────────────────────────

export async function deleteExpedition(id: string) {
  await requireAuth()
  const supabase = createClient()

  // Check for confirmed bookings before deleting
  const { count } = await supabase
    .from('bookings')
    .select('*', { count: 'exact', head: true })
    .eq('expedition_id', id)
    .eq('status', 'confirmed')

  if ((count ?? 0) > 0) {
    throw new Error('Não é possível deletar uma expedição com reservas confirmadas.')
  }

  // Soft delete
  const { error } = await supabase
    .from('expeditions')
    .update({ deleted_at: new Date().toISOString(), status: 'cancelled' })
    .eq('id', id)

  if (error) throw new Error(error.message)

  revalidatePath('/expeditions')
  redirect('/expeditions')
}

// ── CHECKLIST ITEM TOGGLE ────────────────────────────────────

export async function toggleChecklistItem(itemId: string, isDone: boolean) {
  await requireAuth()
  const supabase = createClient()

  const { error } = await supabase
    .from('checklist_items')
    .update({ is_done: isDone })
    .eq('id', itemId)

  if (error) throw new Error(error.message)
  revalidatePath('/expeditions')
}

// ── ADD CHECKLIST ITEM ───────────────────────────────────────

export async function addChecklistItem(expeditionId: string, formData: FormData) {
  await requireAuth()
  const supabase = createClient()

  const label = formData.get('label') as string
  if (!label?.trim()) return

  const { error } = await supabase.from('checklist_items').insert({
    expedition_id: expeditionId,
    label:         label.trim(),
    is_done:       false,
    sort_order:    0,
  })

  if (error) throw new Error(error.message)
  revalidatePath(`/expeditions/${expeditionId}`)
}
