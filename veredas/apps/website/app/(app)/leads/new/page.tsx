import { requireAuth } from '@/lib/auth/helpers'
import Link from 'next/link'
import { createLead } from '../actions'

const CRM_STATUSES = ['NEW','CONTACTED','QUALIFIED','PROPOSAL','RESERVED','PAID','POST_TRIP','REFERRAL'] as const
const SOURCES = ['Instagram','WhatsApp','Indicação','Google','Facebook','Evento','Outro']

export default async function NewLeadPage({
  searchParams,
}: {
  searchParams: { error?: string }
}) {
  await requireAuth()

  return (
    <div className="max-w-2xl mx-auto space-y-6">
      {/* Breadcrumb */}
      <nav className="text-sm text-gray-500 flex gap-2">
        <Link href="/leads" className="hover:text-brand-600 transition-colors">CRM / Leads</Link>
        <span>/</span>
        <span className="text-gray-800 font-medium">Novo Lead</span>
      </nav>

      <div className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h1 className="text-xl font-bold text-gray-900 mb-6">Novo Lead</h1>

        {searchParams.error && (
          <div className="mb-4 bg-red-50 border border-red-200 rounded-lg p-3 text-red-700 text-sm">
            {searchParams.error}
          </div>
        )}

        <form action={createLead} className="space-y-4">
          <div className="grid sm:grid-cols-2 gap-4">
            <div className="sm:col-span-2">
              <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
              <input id="name" name="name" required placeholder="João Silva"
                className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
            </div>

            <Field label="WhatsApp" name="phone" placeholder="+55 11 99999-9999" />
            <Field label="E-mail" name="email" type="email" placeholder="joao@email.com" />
            <Field label="Instagram" name="instagram" placeholder="@joaosilva" />

            <div>
              <label htmlFor="source" className="block text-sm font-medium text-gray-700 mb-1">Origem</label>
              <select id="source" name="source"
                className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                <option value="">Selecionar…</option>
                {SOURCES.map((s) => <option key={s} value={s}>{s}</option>)}
              </select>
            </div>

            <Field label="Interesse" name="interest" placeholder="Trilha, aventura…" />
            <Field label="Destino" name="destination" placeholder="Chapada Diamantina…" />

            <div>
              <label htmlFor="status" className="block text-sm font-medium text-gray-700 mb-1">Status inicial</label>
              <select id="status" name="status" defaultValue="NEW"
                className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                {CRM_STATUSES.map((s) => <option key={s} value={s}>{s}</option>)}
              </select>
            </div>

            <Field label="Data desejada" name="date_desired" type="date" />
            <Field label="Nº de pessoas" name="people_count" type="number" placeholder="2" />
            <Field label="Ticket estimado (R$)" name="total_price" type="number" step="0.01" placeholder="3500.00" />
            <Field label="Último contato" name="last_contact" type="date" />
            <Field label="Próximo follow-up" name="next_follow_up" type="date" />
          </div>

          <div>
            <label htmlFor="notes" className="block text-sm font-medium text-gray-700 mb-1">Observações</label>
            <textarea id="notes" name="notes" rows={3} placeholder="Informações adicionais sobre o lead…"
              className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
          </div>

          <div className="flex gap-3 pt-2">
            <button type="submit"
              className="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-colors">
              Criar lead
            </button>
            <Link href="/leads"
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
  label, name, type = 'text', placeholder = '', required = false, step,
}: {
  label: string; name: string; type?: string; placeholder?: string; required?: boolean; step?: string
}) {
  return (
    <div>
      <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
      <input name={name} type={type} placeholder={placeholder} required={required} step={step}
        className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
    </div>
  )
}
