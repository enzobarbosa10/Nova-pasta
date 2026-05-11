import type { ReactNode } from 'react'
import { Sidebar } from '@/components/Sidebar'

export default function ExpeditionsLayout({ children }: { children: ReactNode }) {
  return (
    <div className="flex min-h-screen">
      <Sidebar />
      <main className="flex-1 p-6 md:p-8 overflow-auto bg-gray-50">{children}</main>
    </div>
  )
}
