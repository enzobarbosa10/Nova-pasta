import { createClient } from '@/utils/supabase/server'

interface MonthlyRevenue { month: string; total: number }
interface FunnelItem { status: string; count: number }

async function fetchAnalytics() {
  const supabase = createClient()

  const now = new Date()
  const sixMonthsAgo = new Date(now.getFullYear(), now.getMonth() - 5, 1).toISOString()

  const [
    { data: revenueByMonth },
    { data: funnelData },
    { count: totalLeads },
    { count: paidLeads },
    { data: topDestinations },
  ] = await Promise.all([
    supabase
      .from('leads')
      .select('created_at, total_price')
      .eq('status', 'PAID')
      .gte('created_at', sixMonthsAgo) as unknown as Promise<{ data: Array<{ created_at: string; total_price: number | null }> | null; error: unknown }>,
    supabase
      .from('leads')
      .select('status') as unknown as Promise<{ data: Array<{ status: string }> | null; error: unknown }>,
    supabase.from('leads').select('*', { count: 'exact', head: true }),
    supabase.from('leads').select('*', { count: 'exact', head: true }).eq('status', 'PAID'),
    supabase
      .from('leads')
      .select('destination') as unknown as Promise<{ data: Array<{ destination: string | null }> | null; error: unknown }>,
  ])

  // Group revenue by month
  const monthlyMap: Record<string, number> = {}
  for (const r of (revenueByMonth as Array<{ created_at: string; total_price: number | null }> ?? [])) {
    const key = r.created_at.slice(0, 7) // YYYY-MM
    monthlyMap[key] = (monthlyMap[key] ?? 0) + (r.total_price ?? 0)
  }
  const monthly: MonthlyRevenue[] = Object.entries(monthlyMap)
    .sort(([a], [b]) => a.localeCompare(b))
    .map(([month, total]) => ({ month, total }))

  // Funnel counts
  const statusOrder = ['NEW', 'CONTACTED', 'QUALIFIED', 'PROPOSAL', 'RESERVED', 'PAID']
  const funnelMap: Record<string, number> = {}
  for (const f of funnelData ?? []) {
    funnelMap[f.status] = (funnelMap[f.status] ?? 0) + 1
  }
  const funnel: FunnelItem[] = statusOrder.map((s) => ({ status: s, count: funnelMap[s] ?? 0 }))

  // Top destinations
  const destMap: Record<string, number> = {}
  for (const d of topDestinations ?? []) {
    if (d.destination) destMap[d.destination] = (destMap[d.destination] ?? 0) + 1
  }
  const destinations = Object.entries(destMap)
    .sort(([, a], [, b]) => b - a)
    .slice(0, 5)
    .map(([name, count]) => ({ name, count }))

  const conversionRate = (totalLeads ?? 0) > 0
    ? Math.round(((paidLeads ?? 0) / (totalLeads ?? 1)) * 1000) / 10
    : 0

  return { monthly, funnel, destinations, totalLeads: totalLeads ?? 0, paidLeads: paidLeads ?? 0, conversionRate }
}

const FUNNEL_LABEL: Record<string, string> = {
  NEW: 'Novo', CONTACTED: 'Contactado', QUALIFIED: 'Qualificado',
  PROPOSAL: 'Proposta', RESERVED: 'Reservado', PAID: 'Pago',
}

const MONTH_NAMES = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']

export default async function AnalyticsPage() {
  const { monthly, funnel, destinations, totalLeads, paidLeads, conversionRate } = await fetchAnalytics()
  const currency = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' })
  const maxRevenue = Math.max(...monthly.map((m) => m.total), 1)
  const maxFunnel  = Math.max(...funnel.map((f) => f.count), 1)

  return (
    <div className="space-y-8">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Analytics</h1>
        <p className="mt-1 text-sm text-gray-500">Insights sobre o seu negócio de expedições</p>
      </div>

      {/* KPI Row */}
      <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
          <p className="text-sm text-gray-500 font-medium">Total de Leads</p>
          <p className="mt-1 text-3xl font-bold text-gray-900">{totalLeads}</p>
        </div>
        <div className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
          <p className="text-sm text-gray-500 font-medium">Leads Pagos</p>
          <p className="mt-1 text-3xl font-bold text-gray-900">{paidLeads}</p>
        </div>
        <div className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm col-span-2 md:col-span-1">
          <p className="text-sm text-gray-500 font-medium">Taxa de Conversão</p>
          <p className="mt-1 text-3xl font-bold text-brand-600">{conversionRate}%</p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Revenue Chart */}
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
          <h2 className="text-sm font-semibold text-gray-700 mb-5">Receita por Mês</h2>
          {monthly.length === 0 ? (
            <p className="text-sm text-gray-400 text-center py-10">Sem dados de receita ainda</p>
          ) : (
            <div className="flex items-end gap-2 h-40">
              {monthly.map(({ month, total }) => {
                const [y, m] = month.split('-')
                const label = `${MONTH_NAMES[parseInt(m) - 1]}/${y.slice(2)}`
                const pct = Math.round((total / maxRevenue) * 100)
                return (
                  <div key={month} className="flex-1 flex flex-col items-center gap-1">
                    <span className="text-[10px] text-gray-500 font-medium">{currency.format(total).replace('R$\u00a0', 'R$')}</span>
                    <div className="w-full bg-brand-500 rounded-t" style={{ height: `${Math.max(pct, 4)}%` }} title={currency.format(total)} />
                    <span className="text-[10px] text-gray-400">{label}</span>
                  </div>
                )
              })}
            </div>
          )}
        </div>

        {/* Conversion Funnel */}
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
          <h2 className="text-sm font-semibold text-gray-700 mb-5">Funil de Conversão</h2>
          <div className="space-y-3">
            {funnel.map(({ status, count }) => {
              const pct = Math.round((count / maxFunnel) * 100)
              return (
                <div key={status} className="flex items-center gap-3">
                  <span className="w-24 text-xs text-gray-600 font-medium shrink-0">{FUNNEL_LABEL[status]}</span>
                  <div className="flex-1 bg-gray-100 rounded-full h-2.5">
                    <div className="bg-brand-500 h-2.5 rounded-full transition-all" style={{ width: `${Math.max(pct, 2)}%` }} />
                  </div>
                  <span className="w-8 text-xs text-gray-500 text-right">{count}</span>
                </div>
              )
            })}
          </div>
        </div>
      </div>

      {/* Top Destinations */}
      {destinations.length > 0 && (
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
          <h2 className="text-sm font-semibold text-gray-700 mb-5">Top Destinos</h2>
          <div className="space-y-3">
            {destinations.map(({ name, count }, i) => (
              <div key={name} className="flex items-center gap-4">
                <span className="w-5 text-xs font-bold text-gray-400">#{i + 1}</span>
                <span className="flex-1 text-sm text-gray-700">{name}</span>
                <span className="text-xs text-gray-500">{count} lead{count !== 1 ? 's' : ''}</span>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  )
}
