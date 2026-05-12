'use client'

import { useEffect, useState } from 'react'
import { createClient } from '@/utils/supabase/client'

interface Expedition {
  id: string
  title: string
  destination: string | null
  start_date: string
  end_date: string
  status: string
  current_travelers: number
  max_travelers: number
}

const STATUS_COLOR: Record<string, string> = {
  draft:     'bg-gray-200 text-gray-700',
  published: 'bg-blue-200 text-blue-800',
  ongoing:   'bg-brand-200 text-brand-800',
  completed: 'bg-purple-200 text-purple-800',
  cancelled: 'bg-red-200 text-red-700',
}

const STATUS_LABEL: Record<string, string> = {
  draft: 'Rascunho', published: 'Publicada', ongoing: 'Em andamento',
  completed: 'Concluída', cancelled: 'Cancelada',
}

const DAYS = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb']
const MONTHS = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro']

function getCalendarDays(year: number, month: number): (Date | null)[] {
  const first = new Date(year, month, 1)
  const last  = new Date(year, month + 1, 0)
  const days: (Date | null)[] = []
  for (let i = 0; i < first.getDay(); i++) days.push(null)
  for (let d = 1; d <= last.getDate(); d++) days.push(new Date(year, month, d))
  return days
}

export default function CalendarPage() {
  const today = new Date()
  const [year,  setYear]  = useState(today.getFullYear())
  const [month, setMonth] = useState(today.getMonth())
  const [expeditions, setExpeditions] = useState<Expedition[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const supabase = createClient()
    const from = new Date(year, month, 1).toISOString().slice(0, 10)
    const to   = new Date(year, month + 1, 0).toISOString().slice(0, 10)

    supabase
      .from('expeditions')
      .select('id, title, destination, start_date, end_date, status, current_travelers, max_travelers')
      .lte('start_date', to)
      .gte('end_date', from)
      .neq('status', 'cancelled')
      .order('start_date')
      .then(({ data }) => { setExpeditions(data ?? []); setLoading(false) })
  }, [year, month])

  function prevMonth() {
    if (month === 0) { setYear(y => y - 1); setMonth(11) }
    else setMonth(m => m - 1)
  }
  function nextMonth() {
    if (month === 11) { setYear(y => y + 1); setMonth(0) }
    else setMonth(m => m + 1)
  }

  const days = getCalendarDays(year, month)

  function expeditionsOnDay(day: Date): Expedition[] {
    const d = day.toISOString().slice(0, 10)
    return expeditions.filter(e => e.start_date <= d && e.end_date >= d)
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Calendário de Expedições</h1>
          <p className="mt-1 text-sm text-gray-500">Visualize e gerencie os cronogramas</p>
        </div>
        <div className="flex items-center gap-2">
          <button onClick={prevMonth} className="p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="Mês anterior">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <span className="min-w-[160px] text-center font-semibold text-gray-800">{MONTHS[month]} {year}</span>
          <button onClick={nextMonth} className="p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="Próximo mês">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
      </div>

      {/* Calendar Grid */}
      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        {/* Day headers */}
        <div className="grid grid-cols-7 border-b border-gray-100">
          {DAYS.map(d => (
            <div key={d} className="py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{d}</div>
          ))}
        </div>

        {loading ? (
          <div className="py-20 flex justify-center items-center text-gray-400 text-sm">
            Carregando expedições…
          </div>
        ) : (
          <div className="grid grid-cols-7 divide-x divide-gray-100">
            {days.map((day, idx) => {
              if (!day) return <div key={`empty-${idx}`} className="min-h-[100px] bg-gray-50" />
              const isToday = day.toDateString() === today.toDateString()
              const exps = expeditionsOnDay(day)
              return (
                <div key={day.toISOString()} className={`min-h-[100px] p-2 border-b border-gray-100 ${isToday ? 'bg-brand-50' : ''}`}>
                  <span className={`inline-flex w-6 h-6 items-center justify-center rounded-full text-xs font-medium ${
                    isToday ? 'bg-brand-600 text-white' : 'text-gray-600'
                  }`}>
                    {day.getDate()}
                  </span>
                  <div className="mt-1 space-y-1">
                    {exps.slice(0, 3).map(e => (
                      <div key={e.id} className={`text-[10px] px-1.5 py-0.5 rounded font-medium truncate ${STATUS_COLOR[e.status] ?? 'bg-gray-100 text-gray-600'}`} title={e.title}>
                        {e.title}
                      </div>
                    ))}
                    {exps.length > 3 && (
                      <div className="text-[10px] text-gray-400">+{exps.length - 3} mais</div>
                    )}
                  </div>
                </div>
              )
            })}
          </div>
        )}
      </div>

      {/* Legend */}
      <div className="flex flex-wrap gap-3">
        {Object.entries(STATUS_LABEL).map(([status, label]) => (
          <div key={status} className="flex items-center gap-1.5">
            <span className={`w-3 h-3 rounded-sm ${STATUS_COLOR[status]}`} />
            <span className="text-xs text-gray-600">{label}</span>
          </div>
        ))}
      </div>

      {/* Upcoming expeditions list */}
      {expeditions.length > 0 && (
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
          <h2 className="text-sm font-semibold text-gray-700 mb-4">Expedições neste mês</h2>
          <div className="space-y-3">
            {expeditions.map(e => (
              <div key={e.id} className="flex items-center gap-4 py-2 border-b border-gray-50 last:border-0">
                <span className={`px-2 py-0.5 rounded-full text-xs font-semibold shrink-0 ${STATUS_COLOR[e.status]}`}>
                  {STATUS_LABEL[e.status]}
                </span>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium text-gray-800 truncate">{e.title}</p>
                  {e.destination && <p className="text-xs text-gray-500">{e.destination}</p>}
                </div>
                <div className="text-right shrink-0">
                  <p className="text-xs text-gray-600 font-medium">
                    {new Date(e.start_date).toLocaleDateString('pt-BR')} → {new Date(e.end_date).toLocaleDateString('pt-BR')}
                  </p>
                  <p className="text-xs text-gray-400">{e.current_travelers}/{e.max_travelers} viajantes</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  )
}
