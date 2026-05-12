import { createClient } from '@/lib/supabase/server'
import { requireAuth } from '@/lib/auth/helpers'
import { redirect } from 'next/navigation'
import Link from 'next/link'
import type { ExpeditionRow, ChecklistItemRow, MediaRow } from '@/types'
import {
  updateExpedition,
  deleteExpedition,
  updateExpeditionStatus,
  addChecklistItem,
  toggleChecklistItem,
} from '../actions'

const STATUS_BADGE: Record<string, string> = {
  draft:     'bg-gray-100 text-gray-600',
  published: 'bg-blue-100 text-blue-700',
  ongoing:   'bg-brand-100 text-brand-700',
  completed: 'bg-purple-100 text-purple-700',
  cancelled: 'bg-red-100 text-red-600',
}

const TRAIL_BADGE: Record<string, string> = {
  EASY:        'bg-green-100 text-green-700',
  MODERATE:    'bg-yellow-100 text-yellow-700',
  HARD:        'bg-orange-100 text-orange-700',
  CHALLENGING: 'bg-red-100 text-red-700',
}

export default async function ExpeditionDetailPage({
  params,
  searchParams,
}: {
  params: { id: string }
  searchParams: { error?: string }
}) {
  await requireAuth()
  const supabase = createClient()

  const [
    { data: expedition, error },
    { data: checklist },
    { data: media },
  ] = await Promise.all([
    supabase.from('expeditions').select('*').eq('id', params.id).single() as unknown as
      Promise<{ data: ExpeditionRow | null; error: { message: string } | null }>,
    supabase
      .from('checklist_items')
      .select('*')
      .eq('expedition_id', params.id)
      .order('sort_order') as unknown as
      Promise<{ data: ChecklistItemRow[] | null }>,
    supabase
      .from('media')
      .select('*')
      .eq('expedition_id', params.id)
      .order('created_at', { ascending: false }) as unknown as
      Promise<{ data: MediaRow[] | null }>,
  ])

  if (error || !expedition) redirect('/expeditions')

  const currency = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' })

  const updateWithId    = updateExpedition.bind(null, params.id)
  const addItemWithId   = addChecklistItem.bind(null, params.id)
  const deleteWithId    = deleteExpedition.bind(null, params.id)

  return (
    <div className="max-w-5xl mx-auto space-y-6">
      {/* Breadcrumb */}
      <nav className="text-sm text-gray-500 flex gap-2">
        <Link href="/expeditions" className="hover:text-brand-600 transition-colors">Expedições</Link>
        <span>/</span>
        <span className="text-gray-800 font-medium truncate">{expedition.title}</span>
      </nav>

      {searchParams.error && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-3 text-red-700 text-sm">
          {searchParams.error}
        </div>
      )}

      {/* Status bar */}
      <div className="flex flex-wrap items-center gap-3">
        <span className={`px-3 py-1 rounded-full text-xs font-semibold capitalize ${STATUS_BADGE[expedition.status] ?? ''}`}>
          {expedition.status}
        </span>
        {expedition.trail_level && (
          <span className={`px-3 py-1 rounded-full text-xs font-semibold ${TRAIL_BADGE[expedition.trail_level] ?? ''}`}>
            {expedition.trail_level}
          </span>
        )}
        <div className="flex gap-2 ml-auto">
          {expedition.status === 'draft' && (
            <form action={updateExpeditionStatus.bind(null, params.id, 'published')}>
              <button type="submit" className="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg font-medium transition-colors">
                Publicar
              </button>
            </form>
          )}
          {expedition.status === 'published' && (
            <form action={updateExpeditionStatus.bind(null, params.id, 'ongoing')}>
              <button type="submit" className="px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs rounded-lg font-medium transition-colors">
                Iniciar
              </button>
            </form>
          )}
          {expedition.status === 'ongoing' && (
            <form action={updateExpeditionStatus.bind(null, params.id, 'completed')}>
              <button type="submit" className="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs rounded-lg font-medium transition-colors">
                Concluir
              </button>
            </form>
          )}
        </div>
      </div>

      <div className="grid lg:grid-cols-3 gap-6">
        {/* Main — edit form */}
        <div className="lg:col-span-2 space-y-6">
          <div className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 className="text-base font-semibold text-gray-900 mb-5">Detalhes</h2>
            <form action={updateWithId} className="space-y-4">
              <div className="grid sm:grid-cols-2 gap-4">
                <div className="sm:col-span-2">
                  <label className="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                  <input name="title" required defaultValue={expedition.title}
                    className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <Field label="Destino" name="destination" defaultValue={expedition.destination} />
                <Field label="Nível de trilha" name="trail_level" defaultValue={expedition.trail_level ?? ''} />
                <Field label="Data início" name="start_date" type="date" defaultValue={expedition.start_date} />
                <Field label="Data fim" name="end_date" type="date" defaultValue={expedition.end_date} />
                <Field label="Capacidade" name="max_travelers" type="number" defaultValue={String(expedition.max_travelers)} />
                <Field label="Preço / pessoa (R$)" name="price_per_person" type="number" step="0.01" defaultValue={String(expedition.price_per_person)} />
                <Field label="Acomodação" name="accommodation" defaultValue={expedition.accommodation ?? ''} />
                <Field label="Transporte" name="transport" defaultValue={expedition.transport ?? ''} />
                <Field label="Custos (R$)" name="costs" type="number" step="0.01" defaultValue={String(expedition.costs ?? '')} />
                <Field label="Margem prevista (%)" name="margin_predicted" type="number" step="0.01" defaultValue={String(expedition.margin_predicted ?? '')} />
                <Field label="Margem real (%)" name="margin_real" type="number" step="0.01" defaultValue={String(expedition.margin_real ?? '')} />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea name="description" rows={4} defaultValue={expedition.description ?? ''}
                  className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
              </div>

              <div className="flex gap-3 pt-2">
                <button type="submit"
                  className="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-colors">
                  Salvar
                </button>
                <Link href="/expeditions"
                  className="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                  Cancelar
                </Link>
              </div>
            </form>
          </div>

          {/* Checklist */}
          <div className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 className="text-base font-semibold text-gray-900 mb-4">Checklist</h2>

            <form action={addItemWithId} className="flex gap-3 mb-4">
              <input name="label" placeholder="Nova tarefa…"
                className="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
              <button type="submit"
                className="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm rounded-lg font-medium transition-colors">
                +
              </button>
            </form>

            <ul className="space-y-2">
              {(checklist ?? []).length === 0 && (
                <p className="text-sm text-gray-400">Nenhum item no checklist.</p>
              )}
              {(checklist ?? []).map((item) => (
                <li key={item.id} className="flex items-center gap-3">
                  <form action={toggleChecklistItem.bind(null, item.id, !item.is_done)}>
                    <button type="submit"
                      className={`w-5 h-5 rounded border-2 flex-shrink-0 transition-colors ${
                        item.is_done
                          ? 'bg-brand-600 border-brand-600'
                          : 'border-gray-300 hover:border-brand-500'
                      }`}
                    >
                      {item.is_done && <span className="text-white text-xs leading-none">✓</span>}
                    </button>
                  </form>
                  <span className={`text-sm ${item.is_done ? 'line-through text-gray-400' : 'text-gray-700'}`}>
                    {item.label}
                  </span>
                </li>
              ))}
            </ul>
          </div>

          {/* Media */}
          {(media ?? []).length > 0 && (
            <div className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <h2 className="text-base font-semibold text-gray-900 mb-4">Mídias</h2>
              <div className="grid grid-cols-3 gap-3">
                {(media ?? []).map((m) => (
                  <a key={m.id} href={m.url} target="_blank" rel="noreferrer"
                    className="block rounded-lg overflow-hidden border border-gray-200 hover:opacity-80 transition-opacity">
                    {m.type === 'image' ? (
                      // eslint-disable-next-line @next/next/no-img-element
                      <img src={m.url} alt={m.name} className="w-full h-24 object-cover" />
                    ) : (
                      <div className="w-full h-24 bg-gray-100 flex items-center justify-center text-xs text-gray-500">
                        📄 {m.name}
                      </div>
                    )}
                  </a>
                ))}
              </div>
            </div>
          )}
        </div>

        {/* Sidebar */}
        <aside className="space-y-4">
          <div className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm space-y-3">
            <h3 className="text-sm font-semibold text-gray-700 uppercase tracking-wide">Resumo</h3>
            <Stat label="Vagas" value={`${expedition.current_travelers}/${expedition.max_travelers}`} />
            <Stat label="Preço/pessoa" value={currency.format(expedition.price_per_person)} />
            <Stat label="Custos" value={expedition.costs ? currency.format(expedition.costs) : '—'} />
            <Stat label="Margem prevista" value={expedition.margin_predicted ? `${expedition.margin_predicted}%` : '—'} />
            <Stat label="Margem real" value={expedition.margin_real ? `${expedition.margin_real}%` : '—'} />
            <Stat label="Criado em" value={new Date(expedition.created_at).toLocaleDateString('pt-BR')} />
          </div>

          <form action={deleteWithId}>
            <button type="submit"
              onClick={(e) => { if (!confirm('Deletar esta expedição?')) e.preventDefault() }}
              className="w-full px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium rounded-lg border border-red-200 transition-colors">
              Deletar expedição
            </button>
          </form>
        </aside>
      </div>
    </div>
  )
}

function Field({
  label, name, type = 'text', defaultValue = '', required = false, step,
}: {
  label: string; name: string; type?: string; defaultValue?: string; required?: boolean; step?: string
}) {
  return (
    <div>
      <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
      <input name={name} type={type} defaultValue={defaultValue} required={required} step={step}
        className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
    </div>
  )
}

function Stat({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex justify-between text-sm">
      <span className="text-gray-500">{label}</span>
      <span className="font-medium text-gray-800">{value}</span>
    </div>
  )
}
