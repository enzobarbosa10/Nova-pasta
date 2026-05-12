import { createClient } from '@/utils/supabase/server'
import Link from 'next/link'

interface LeadCard {
  id: string
  name: string
  email: string
  phone: string | null
  status: string
  destination: string | null
  created_at: string
}

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

const CRM_STATUSES = ['NEW', 'CONTACTED', 'QUALIFIED', 'PROPOSAL', 'RESERVED', 'PAID', 'POST_TRIP', 'REFERRAL']

export default async function LeadsPage({
  searchParams,
}: {
  searchParams: { status?: string; search?: string; page?: string }
}) {
  const supabase = createClient()

  const page     = Number(searchParams.page ?? 1)
  const pageSize = 25
  const from     = (page - 1) * pageSize
  const to       = from + pageSize - 1

  let query = supabase
    .from('leads')
    .select('id, name, email, phone, status, destination, created_at', { count: 'exact' })
    .order('created_at', { ascending: false })
    .range(from, to)

  if (searchParams.status) {
    query = query.eq('status', searchParams.status)
  }

  if (searchParams.search) {
    query = query.or(`name.ilike.%${searchParams.search}%,email.ilike.%${searchParams.search}%`)
  }

  const { data: leads, count, error } = await query as unknown as { data: LeadCard[] | null; count: number | null; error: { message: string } | null }

  const total      = count ?? 0
  const totalPages = Math.ceil(total / pageSize)

  function buildHref(overrides: Record<string, string | undefined>) {
    const params = new URLSearchParams()
    const merged = { page: '1', status: searchParams.status, search: searchParams.search, ...overrides }
    Object.entries(merged).forEach(([k, v]) => { if (v) params.set(k, v) })
    return `/leads?${params.toString()}`
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">CRM / Leads</h1>
          <p className="mt-1 text-sm text-gray-500">{total} lead(s) encontrado(s)</p>
        </div>
        <Link
          href="/leads/new"
          className="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-colors"
        >
          + Novo Lead
        </Link>
      </div>

      {/* Search + status filter */}
      <div className="flex flex-col sm:flex-row gap-3">
        <form method="GET" action="/leads" className="flex gap-2">
          <input
            name="search"
            defaultValue={searchParams.search}
            placeholder="Buscar por nome ou e-mail…"
            className="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 w-64"
          />
          {searchParams.status && (
            <input type="hidden" name="status" value={searchParams.status} />
          )}
          <button
            type="submit"
            className="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors"
          >
            Buscar
          </button>
        </form>

        <div className="flex flex-wrap gap-1.5">
          <Link
            href={buildHref({ status: undefined })}
            className={`px-3 py-1.5 rounded-full text-xs font-medium ${!searchParams.status ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`}
          >
            Todos
          </Link>
          {CRM_STATUSES.map((s) => (
            <Link
              key={s}
              href={buildHref({ status: s })}
              className={`px-3 py-1.5 rounded-full text-xs font-medium ${searchParams.status === s ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`}
            >
              {s}
            </Link>
          ))}
        </div>
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 text-sm">
          Erro: {error.message}
        </div>
      )}

      {/* Table */}
      {!error && (
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
          <table className="min-w-full divide-y divide-gray-100 text-sm">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</th>
                <th className="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Contato</th>
                <th className="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Destino</th>
                <th className="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th className="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Data</th>
                <th className="px-5 py-3" />
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-50">
              {(leads ?? []).map((lead) => (
                <tr key={lead.id} className="hover:bg-gray-50 transition-colors">
                  <td className="px-5 py-3 font-medium text-gray-900">{lead.name}</td>
                  <td className="px-5 py-3 text-gray-500 hidden md:table-cell">{lead.email ?? lead.phone ?? '—'}</td>
                  <td className="px-5 py-3 text-gray-500 hidden lg:table-cell">{lead.destination ?? '—'}</td>
                  <td className="px-5 py-3">
                    <span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${STATUS_BADGE[lead.status] ?? 'bg-gray-100 text-gray-600'}`}>
                      {lead.status}
                    </span>
                  </td>
                  <td className="px-5 py-3 text-gray-400 hidden sm:table-cell">
                    {new Date(lead.created_at).toLocaleDateString('pt-BR')}
                  </td>
                  <td className="px-5 py-3 text-right">
                    <Link
                      href={`/leads/${lead.id}`}
                      className="text-brand-600 hover:text-brand-700 text-xs font-medium"
                    >
                      Ver →
                    </Link>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>

          {(leads ?? []).length === 0 && (
            <p className="py-10 text-center text-gray-400 text-sm">Nenhum lead encontrado.</p>
          )}
        </div>
      )}

      {/* Pagination */}
      {totalPages > 1 && (
        <div className="flex justify-center gap-2 pt-2">
          {Array.from({ length: totalPages }, (_, i) => i + 1).map((p) => (
            <Link
              key={p}
              href={buildHref({ page: String(p) })}
              className={`w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium ${
                page === p ? 'bg-brand-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'
              }`}
            >
              {p}
            </Link>
          ))}
        </div>
      )}
    </div>
  )
}
