import { createClient } from '@/lib/supabase/server'
import { requireAuth } from '@/lib/auth/helpers'
import { redirect } from 'next/navigation'
import Link from 'next/link'
import type { LeadRow, LeadNoteRow } from '@/types'
import { updateLead, deleteLead, addLeadNote } from '../actions'

const STATUS_BADGE: Record<string, string> = {
  NEW:       'bg-blue-100 text-blue-700',
  CONTACTED: 'bg-yellow-100 text-yellow-700',
  QUALIFIED: 'bg-purple-100 text-purple-700',
  PROPOSAL:  'bg-orange-100 text-orange-700',
  RESERVED:  'bg-indigo-100 text-indigo-700',
  PAID:      'bg-green-100 text-green-700',
  POST_TRIP: 'bg-teal-100 text-teal-700',
  REFERRAL:  'bg-pink-100 text-pink-700',
}

const CRM_STATUSES = ['NEW','CONTACTED','QUALIFIED','PROPOSAL','RESERVED','PAID','POST_TRIP','REFERRAL'] as const

export default async function LeadDetailPage({
  params,
  searchParams,
}: Readonly<{
  params: { id: string }
  searchParams: { error?: string }
}>) {
  await requireAuth()
  const supabase = createClient()

  const [{ data: lead, error }, { data: notes }] = await Promise.all([
    supabase.from('leads').select('*').eq('id', params.id).single() as unknown as
      Promise<{ data: LeadRow | null; error: { message: string } | null }>,
    supabase
      .from('lead_notes')
      .select('*')
      .eq('lead_id', params.id)
      .order('created_at', { ascending: false }) as unknown as
      Promise<{ data: LeadNoteRow[] | null }>,
  ])

  if (error || !lead) redirect('/leads')

  const updateLeadWithId = updateLead.bind(null, params.id)
  const addNoteWithId    = addLeadNote.bind(null, params.id)
  const deleteLeadWithId = deleteLead.bind(null, params.id)

  const currency = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' })

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      {/* Breadcrumb */}
      <nav className="text-sm text-gray-500 flex gap-2">
        <Link href="/leads" className="hover:text-brand-600 transition-colors">CRM / Leads</Link>
        <span>/</span>
        <span className="text-gray-800 font-medium">{lead.name}</span>
      </nav>

      {searchParams.error && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-3 text-red-700 text-sm">
          {searchParams.error}
        </div>
      )}

      <div className="grid lg:grid-cols-3 gap-6">
        {/* Main — edit form */}
        <div className="lg:col-span-2 space-y-6">
          <div className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div className="flex items-center justify-between mb-5">
              <h1 className="text-xl font-bold text-gray-900">{lead.name}</h1>
              <span className={`px-3 py-1 rounded-full text-xs font-semibold ${STATUS_BADGE[lead.status] ?? 'bg-gray-100 text-gray-600'}`}>
                {lead.status}
              </span>
            </div>

            <form action={updateLeadWithId} className="space-y-4">
              <div className="grid sm:grid-cols-2 gap-4">
                <Field label="Nome *" name="name" defaultValue={lead.name} required />
                <Field label="WhatsApp" name="phone" defaultValue={lead.phone ?? ''} />
                <Field label="E-mail" name="email" type="email" defaultValue={lead.email ?? ''} />
                <Field label="Instagram" name="instagram" defaultValue={lead.instagram ?? ''} />
                <Field label="Origem" name="source" defaultValue={lead.source ?? ''} />
                <Field label="Interesse" name="interest" defaultValue={lead.interest ?? ''} />
                <Field label="Destino" name="destination" defaultValue={lead.destination ?? ''} />
                <Field label="Data desejada" name="date_desired" type="date" defaultValue={lead.date_desired ?? ''} />
                <Field label="Nº de pessoas" name="people_count" type="number" defaultValue={String(lead.people_count ?? 1)} />
                <Field label="Ticket estimado (R$)" name="total_price" type="number" step="0.01" defaultValue={String(lead.total_price ?? '')} />
                <Field label="Último contato" name="last_contact" type="date" defaultValue={lead.last_contact ?? ''} />
                <Field label="Próximo follow-up" name="next_follow_up" type="date" defaultValue={lead.next_follow_up ?? ''} />
              </div>

              <div>
                <label htmlFor="status" className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" defaultValue={lead.status}
                  className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                  {CRM_STATUSES.map((s) => <option key={s} value={s}>{s}</option>)}
                </select>
              </div>

              <div>
                <label htmlFor="notes" className="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea id="notes" name="notes" rows={3} defaultValue={lead.notes ?? ''}
                  className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
              </div>

              <div className="flex gap-3 pt-2">
                <button type="submit"
                  className="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-colors">
                  Salvar
                </button>
                <Link href="/leads"
                  className="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                  Cancelar
                </Link>
              </div>
            </form>
          </div>

          {/* Notes */}
          <div className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 className="text-base font-semibold text-gray-900 mb-4">Anotações</h2>

            <form action={addNoteWithId} className="flex gap-3 mb-5">
              <input name="content" placeholder="Adicionar anotação…"
                className="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
              <button type="submit"
                className="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm rounded-lg font-medium transition-colors">
                Adicionar
              </button>
            </form>

            <div className="space-y-3">
              {(notes ?? []).length === 0 && (
                <p className="text-sm text-gray-400">Nenhuma anotação ainda.</p>
              )}
              {(notes ?? []).map((note) => (
                <div key={note.id} className="bg-gray-50 rounded-lg p-3 text-sm text-gray-700">
                  <p>{note.content}</p>
                  <p className="mt-1.5 text-xs text-gray-400">
                    {new Date(note.created_at).toLocaleString('pt-BR')}
                  </p>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Sidebar — summary */}
        <aside className="space-y-4">
          <div className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm space-y-3">
            <h3 className="text-sm font-semibold text-gray-700 uppercase tracking-wide">Resumo</h3>
            <Stat label="Pessoas" value={String(lead.people_count ?? '—')} />
            <Stat label="Ticket" value={lead.total_price ? currency.format(lead.total_price) : '—'} />
            <Stat label="Destino" value={lead.destination ?? '—'} />
            <Stat label="Data desejada" value={lead.date_desired ? new Date(lead.date_desired).toLocaleDateString('pt-BR') : '—'} />
            <Stat label="Criado em" value={new Date(lead.created_at).toLocaleDateString('pt-BR')} />
            <Stat label="Atualizado" value={new Date(lead.updated_at).toLocaleDateString('pt-BR')} />
          </div>

          <form action={deleteLeadWithId}>
            <button type="submit"
              onClick={(e) => { if (!confirm('Deletar este lead?')) e.preventDefault() }}
              className="w-full px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium rounded-lg border border-red-200 transition-colors">
              Deletar lead
            </button>
          </form>
        </aside>
      </div>
    </div>
  )
}

function Field({
  label, name, type = 'text', defaultValue = '', required = false, step,
}: Readonly<{
  label: string; name: string; type?: string; defaultValue?: string; required?: boolean; step?: string
}>) {
  return (
    <div>
      <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
      <input
        name={name} type={type} defaultValue={defaultValue} required={required} step={step}
        className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
      />
    </div>
  )
}

function Stat({ label, value }: Readonly<{ label: string; value: string }>) {
  return (
    <div className="flex justify-between text-sm">
      <span className="text-gray-500">{label}</span>
      <span className="font-medium text-gray-800">{value}</span>
    </div>
  )
}
