'use server'
// ============================================================
// app/(app)/leads/actions.ts
// Server Actions for Lead CRUD — replaces Laravel LeadController
// ============================================================
import { revalidatePath } from 'next/cache'
import { redirect } from 'next/navigation'
import { createClient } from '@/lib/supabase/server'
import { requireAuth } from '@/lib/auth/helpers'
import type { LeadInsert, LeadUpdate } from '@/types'

// ── CREATE ──────────────────────────────────────────────────

export async function createLead(formData: FormData) {
  await requireAuth()
  const supabase = createClient()

  const payload: LeadInsert = {
    name:           formData.get('name') as string,
    email:          (formData.get('email') as string) || null,
    phone:          (formData.get('phone') as string) || null,
    instagram:      (formData.get('instagram') as string) || null,
    source:         (formData.get('source') as string) || null,
    interest:       (formData.get('interest') as string) || null,
    destination:    (formData.get('destination') as string) || null,
    date_desired:   (formData.get('date_desired') as string) || null,
    people_count:   formData.get('people_count') ? Number(formData.get('people_count')) : null,
    total_price:    formData.get('total_price') ? Number(formData.get('total_price')) : null,
    status:         (formData.get('status') as LeadInsert['status']) ?? 'NEW',
    notes:          (formData.get('notes') as string) || null,
    last_contact:   (formData.get('last_contact') as string) || null,
    next_follow_up: (formData.get('next_follow_up') as string) || null,
    tags:           [],
    expedition_id:  (formData.get('expedition_id') as string) || null,
    assigned_to:    null,
  }

  const { error } = await supabase.from('leads').insert(payload)
  if (error) {
    redirect(`/leads/new?error=${encodeURIComponent(error.message)}`)
  }

  revalidatePath('/leads')
  redirect('/leads')
}

// ── UPDATE ──────────────────────────────────────────────────

export async function updateLead(id: string, formData: FormData) {
  await requireAuth()
  const supabase = createClient()

  const payload: LeadUpdate = {
    name:           (formData.get('name') as string) || undefined,
    email:          (formData.get('email') as string) || null,
    phone:          (formData.get('phone') as string) || null,
    instagram:      (formData.get('instagram') as string) || null,
    source:         (formData.get('source') as string) || null,
    interest:       (formData.get('interest') as string) || null,
    destination:    (formData.get('destination') as string) || null,
    date_desired:   (formData.get('date_desired') as string) || null,
    people_count:   formData.get('people_count') ? Number(formData.get('people_count')) : null,
    total_price:    formData.get('total_price') ? Number(formData.get('total_price')) : null,
    status:         (formData.get('status') as LeadUpdate['status']) ?? undefined,
    notes:          (formData.get('notes') as string) || null,
    last_contact:   (formData.get('last_contact') as string) || null,
    next_follow_up: (formData.get('next_follow_up') as string) || null,
    expedition_id:  (formData.get('expedition_id') as string) || null,
  }

  const { error } = await supabase.from('leads').update(payload).eq('id', id)
  if (error) {
    redirect(`/leads/${id}?error=${encodeURIComponent(error.message)}`)
  }

  revalidatePath('/leads')
  revalidatePath(`/leads/${id}`)
  redirect(`/leads/${id}`)
}

// ── UPDATE STATUS (quick action) ────────────────────────────

export async function updateLeadStatus(id: string, status: LeadUpdate['status']) {
  await requireAuth()
  const supabase = createClient()

  const { error } = await supabase.from('leads').update({ status }).eq('id', id)
  if (error) throw new Error(error.message)

  revalidatePath('/leads')
  revalidatePath(`/leads/${id}`)
}

// ── DELETE ──────────────────────────────────────────────────

export async function deleteLead(id: string) {
  await requireAuth()
  const supabase = createClient()

  const { error } = await supabase.from('leads').delete().eq('id', id)
  if (error) throw new Error(error.message)

  revalidatePath('/leads')
  redirect('/leads')
}

// ── ADD NOTE ────────────────────────────────────────────────

export async function addLeadNote(leadId: string, formData: FormData) {
  const user = await requireAuth()
  const supabase = createClient()

  const content = formData.get('content') as string
  if (!content?.trim()) return

  const { error } = await supabase.from('lead_notes').insert({
    lead_id:   leadId,
    author_id: user.id,
    content:   content.trim(),
  })

  if (error) throw new Error(error.message)
  revalidatePath(`/leads/${leadId}`)
}
