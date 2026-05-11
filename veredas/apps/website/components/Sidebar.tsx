'use client'

import Link from 'next/link'
import { usePathname } from 'next/navigation'
import { signOut } from '@/app/login/actions'

const NAV_ITEMS = [
  { href: '/dashboard',   label: 'Dashboard',   icon: '📊' },
  { href: '/expeditions', label: 'Expedições',   icon: '🏔️' },
  { href: '/leads',       label: 'CRM / Leads',  icon: '👥' },
]

export function Sidebar() {
  const pathname = usePathname()

  return (
    <aside className="w-64 bg-brand-900 text-white flex flex-col shrink-0 min-h-screen">
      {/* Logo */}
      <div className="px-6 py-5 border-b border-brand-700">
        <span className="text-xl font-bold tracking-tight">🌿 Veredas</span>
      </div>

      {/* Nav */}
      <nav className="flex-1 py-4 space-y-1 px-3">
        {NAV_ITEMS.map(({ href, label, icon }) => {
          const active = pathname.startsWith(href)
          return (
            <Link
              key={href}
              href={href}
              className={`flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
                active
                  ? 'bg-brand-700 text-white'
                  : 'text-brand-100 hover:bg-brand-700 hover:text-white'
              }`}
            >
              <span>{icon}</span>
              {label}
            </Link>
          )
        })}
      </nav>

      {/* Logout */}
      <div className="px-3 py-4 border-t border-brand-700">
        <form action={signOut}>
          <button
            type="submit"
            className="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-brand-100 hover:bg-brand-700 hover:text-white transition-colors"
          >
            <span>🚪</span>
            Sair
          </button>
        </form>
      </div>
    </aside>
  )
}
