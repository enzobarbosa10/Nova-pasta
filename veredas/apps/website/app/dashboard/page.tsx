import { createClient } from '@/utils/supabase/server'
import { cookies } from 'next/headers'

interface DashboardStats {
  totalLeads: number
  newLeadsMonth: number
  totalExpeditions: number
  activeExpeditions: number
  totalRevenue: number
  conversionRate: number
  recentLeads: Array<{ id: string; name: string; status: string; destination: string | null; created_at: string }>
}

async function fetchStats(): Promise<DashboardStats> {
  const cookieStore = await cookies()
  const supabase = createClient(cookieStore)

  const now = new Date()
  const monthStart = new Date(now.getFullYear(), now.getMonth(), 1).toISOString()

  const [
    { count: totalLeads },
    { count: newLeadsMonth },
    { count: totalExpeditions },
    { count: activeExpeditions },
    { data: revenueData },
    { count: paidLeads },
    { data: recentLeads },
  ] = await Promise.all([
    supabase.from('leads').select('*', { count: 'exact', head: true }),
    supabase.from('leads').select('*', { count: 'exact', head: true }).gte('created_at', monthStart),
    supabase.from('expeditions').select('*', { count: 'exact', head: true }),
    supabase.from('expeditions').select('*', { count: 'exact', head: true }).in('status', ['published', 'ongoing']),
    supabase.from('leads').select('total_price').eq('status', 'PAID'),
    supabase.from('leads').select('*', { count: 'exact', head: true }).eq('status', 'PAID'),
    supabase.from('leads').select('id, name, status, destination, created_at').order('created_at', { ascending: false }).limit(5),
  ])

  const totalRevenue = (revenueData ?? []).reduce((sum, r) => sum + (r.total_price ?? 0), 0)
  const conversionRate = (totalLeads ?? 0) > 0
    ? Math.round(((paidLeads ?? 0) / (totalLeads ?? 1)) * 1000) / 10
    : 0

  return {
    totalLeads: totalLeads ?? 0,
    newLeadsMonth: newLeadsMonth ?? 0,
    totalExpeditions: totalExpeditions ?? 0,
    activeExpeditions: activeExpeditions ?? 0,
    totalRevenue,
    conversionRate,
    recentLeads: recentLeads ?? [],
  }
}

function StatCard({ label, value, sub }: { label: string; value: string | number; sub?: string }) {
  return (
    <div className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
      <p className="text-sm text-gray-500 font-medium">{label}</p>
      <p className="mt-1 text-2xl font-bold text-gray-900">{value}</p>
      {sub && <p className="mt-1 text-xs text-gray-400">{sub}</p>}
    </div>
  )
}

const STATUS_BADGE: Record<string, string> = {
  NEW:       'bg-blue-100 text-blue-700',
  CONTACTED: 'bg-yellow-100 text-yellow-700',
  QUALIFIED: 'bg-purple-100 text-purple-700',
  PROPOSAL:  'bg-orange-100 text-orange-700',
  RESERVED:  'bg-indigo-100 text-indigo-700',
  PAID:      'bg-green-100 text-green-700',
}

export default async function DashboardPage() {
  const stats = await fetchStats()

  const currency = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' })

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p className="mt-1 text-sm text-gray-500">Visão geral do negócio em tempo real</p>
      </div>

      {/* KPI grid */}
      <div className="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <StatCard label="Total de Leads"      value={stats.totalLeads} />
        <StatCard label="Novos este mês"      value={stats.newLeadsMonth} />
        <StatCard label="Total Expedições"    value={stats.totalExpeditions} />
        <StatCard label="Expedições Ativas"   value={stats.activeExpeditions} />
        <StatCard label="Receita (PAID)"      value={currency.format(stats.totalRevenue)} />
        <StatCard label="Taxa de Conversão"   value={`${stats.conversionRate}%`} />
      </div>

      {/* Recent leads */}
      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="px-5 py-4 border-b border-gray-100">
          <h2 className="font-semibold text-gray-800">Leads Recentes</h2>
        </div>
        <table className="min-w-full divide-y divide-gray-100 text-sm">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</th>
              <th className="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Destino</th>
              <th className="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
              <th className="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-50">
            {stats.recentLeads.map((lead) => (
              <tr key={lead.id} className="hover:bg-gray-50 transition-colors">
                <td className="px-5 py-3 font-medium text-gray-900">{lead.name}</td>
                <td className="px-5 py-3 text-gray-500">{lead.destination ?? '—'}</td>
                <td className="px-5 py-3">
                  <span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${STATUS_BADGE[lead.status] ?? 'bg-gray-100 text-gray-600'}`}>
                    {lead.status}
                  </span>
                </td>
                <td className="px-5 py-3 text-gray-400">
                  {new Date(lead.created_at).toLocaleDateString('pt-BR')}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        {stats.recentLeads.length === 0 && (
          <p className="px-5 py-6 text-center text-gray-400 text-sm">Nenhum lead cadastrado ainda.</p>
        )}
      </div>
    </div>
  )
}
