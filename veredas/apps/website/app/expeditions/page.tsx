import { createClient } from '@/utils/supabase/server'
import { cookies } from 'next/headers'
import Link from 'next/link'

const TRAIL_BADGE: Record<string, string> = {
  EASY:        'bg-green-100 text-green-700',
  MODERATE:    'bg-yellow-100 text-yellow-700',
  HARD:        'bg-orange-100 text-orange-700',
  CHALLENGING: 'bg-red-100 text-red-700',
}

const STATUS_BADGE: Record<string, string> = {
  draft:      'bg-gray-100 text-gray-600',
  published:  'bg-blue-100 text-blue-700',
  ongoing:    'bg-brand-100 text-brand-700',
  completed:  'bg-purple-100 text-purple-700',
  cancelled:  'bg-red-100 text-red-600',
}

export default async function ExpeditionsPage({
  searchParams,
}: {
  searchParams: { status?: string; page?: string }
}) {
  const cookieStore = await cookies()
  const supabase = createClient(cookieStore)

  const page     = Number(searchParams.page ?? 1)
  const pageSize = 20
  const from     = (page - 1) * pageSize
  const to       = from + pageSize - 1

  let query = supabase
    .from('expeditions')
    .select('id, title, destination, start_date, end_date, status, max_travelers, current_travelers, price_per_person', { count: 'exact' })
    .order('start_date', { ascending: true })
    .range(from, to)

  if (searchParams.status) {
    query = query.eq('status', searchParams.status)
  }

  const { data: expeditions, count, error } = await query

  const total     = count ?? 0
  const totalPages = Math.ceil(total / pageSize)

  const statuses = ['draft', 'published', 'ongoing', 'completed', 'cancelled']
  const currency = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' })

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Expedições</h1>
          <p className="mt-1 text-sm text-gray-500">{total} expedição(ões) encontrada(s)</p>
        </div>
        <Link
          href="/expeditions/new"
          className="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-colors"
        >
          + Nova Expedição
        </Link>
      </div>

      {/* Status filter */}
      <div className="flex flex-wrap gap-2">
        <Link
          href="/expeditions"
          className={`px-3 py-1.5 rounded-full text-xs font-medium transition-colors ${
            !searchParams.status ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
          }`}
        >
          Todos
        </Link>
        {statuses.map((s) => (
          <Link
            key={s}
            href={`/expeditions?status=${s}`}
            className={`px-3 py-1.5 rounded-full text-xs font-medium capitalize transition-colors ${
              searchParams.status === s ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
            }`}
          >
            {s}
          </Link>
        ))}
      </div>

      {/* Error */}
      {error && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 text-sm">
          Erro ao carregar expedições: {error.message}
        </div>
      )}

      {/* Grid */}
      {!error && (
        <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
          {(expeditions ?? []).map((exp) => (
            <Link
              key={exp.id}
              href={`/expeditions/${exp.id}`}
              className="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow p-5 space-y-3"
            >
              <div className="flex items-start justify-between gap-2">
                <h3 className="font-semibold text-gray-900 line-clamp-2">{exp.title}</h3>
                <span className={`shrink-0 inline-flex px-2 py-0.5 rounded-full text-xs font-medium capitalize ${STATUS_BADGE[exp.status] ?? 'bg-gray-100 text-gray-600'}`}>
                  {exp.status}
                </span>
              </div>

              <p className="text-sm text-gray-500">📍 {exp.destination}</p>

              <div className="text-xs text-gray-400 space-y-1">
                <p>📅 {new Date(exp.start_date).toLocaleDateString('pt-BR')} → {new Date(exp.end_date).toLocaleDateString('pt-BR')}</p>
                <p>👥 {exp.current_travelers}/{exp.max_travelers} viajantes</p>
                <p className="font-semibold text-gray-700">{currency.format(exp.price_per_person)}<span className="font-normal text-gray-400"> / pessoa</span></p>
              </div>
            </Link>
          ))}

          {(expeditions ?? []).length === 0 && (
            <div className="col-span-full py-12 text-center text-gray-400 text-sm">
              Nenhuma expedição encontrada para os filtros selecionados.
            </div>
          )}
        </div>
      )}

      {/* Pagination */}
      {totalPages > 1 && (
        <div className="flex justify-center gap-2 pt-2">
          {Array.from({ length: totalPages }, (_, i) => i + 1).map((p) => (
            <Link
              key={p}
              href={`/expeditions?page=${p}${searchParams.status ? `&status=${searchParams.status}` : ''}`}
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
