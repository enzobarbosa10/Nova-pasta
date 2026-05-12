import { requireAuth } from '@/lib/auth/helpers'
import Link from 'next/link'
import { createExpedition } from '../actions'

const TRAIL_LEVELS = ['EASY', 'MODERATE', 'HARD', 'CHALLENGING'] as const

export default async function NewExpeditionPage({
  searchParams,
}: {
  searchParams: { error?: string }
}) {
  await requireAuth()

  const today = new Date().toISOString().slice(0, 10)

  return (
    <div className="max-w-2xl mx-auto space-y-6">
      {/* Breadcrumb */}
      <nav className="text-sm text-gray-500 flex gap-2">
        <Link href="/expeditions" className="hover:text-brand-600 transition-colors">Expedições</Link>
        <span>/</span>
        <span className="text-gray-800 font-medium">Nova Expedição</span>
      </nav>

      <div className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h1 className="text-xl font-bold text-gray-900 mb-6">Nova Expedição</h1>

        {searchParams.error && (
          <div className="mb-4 bg-red-50 border border-red-200 rounded-lg p-3 text-red-700 text-sm">
            {searchParams.error}
          </div>
        )}

        <form action={createExpedition} className="space-y-4">
          <div className="grid sm:grid-cols-2 gap-4">
            <div className="sm:col-span-2">
              <label htmlFor="title" className="block text-sm font-medium text-gray-700 mb-1">Título *</label>
              <input id="title" name="title" required placeholder="Chapada Diamantina — Inverno 2026"
                className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
            </div>

            <Field label="Destino *" name="destination" placeholder="Chapada Diamantina, BA" required />

            <div>
              <label htmlFor="trail_level" className="block text-sm font-medium text-gray-700 mb-1">Nível de trilha</label>
              <select id="trail_level" name="trail_level"
                className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                <option value="">Selecionar…</option>
                {TRAIL_LEVELS.map((l) => <option key={l} value={l}>{l}</option>)}
              </select>
            </div>

            <Field label="Data início *" name="start_date" type="date" defaultValue={today} required />
            <Field label="Data fim *" name="end_date" type="date" defaultValue={today} required />
            <Field label="Capacidade (vagas) *" name="max_travelers" type="number" placeholder="12" required />
            <Field label="Preço / pessoa (R$) *" name="price_per_person" type="number" step="0.01" placeholder="3500.00" required />
            <Field label="Acomodação" name="accommodation" placeholder="Pousada, camping…" />
            <Field label="Transporte" name="transport" placeholder="Van, ônibus, avião…" />
            <Field label="Custos totais (R$)" name="costs" type="number" step="0.01" placeholder="20000.00" />
            <Field label="Margem prevista (%)" name="margin_predicted" type="number" step="0.01" placeholder="25" />
          </div>

          <div>
            <label htmlFor="description" className="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
            <textarea id="description" name="description" rows={4}
              placeholder="Descreva a expedição, roteiro, incluções e informações importantes…"
              className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
          </div>

          <div className="flex gap-3 pt-2">
            <button type="submit"
              className="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-colors">
              Criar expedição
            </button>
            <Link href="/expeditions"
              className="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
              Cancelar
            </Link>
          </div>
        </form>
      </div>
    </div>
  )
}

function Field({
  label, name, type = 'text', placeholder = '', required = false, step, defaultValue = '',
}: {
  label: string; name: string; type?: string; placeholder?: string; required?: boolean
  step?: string; defaultValue?: string
}) {
  return (
    <div>
      <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
      <input name={name} type={type} placeholder={placeholder} required={required} step={step} defaultValue={defaultValue}
        className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
    </div>
  )
}
