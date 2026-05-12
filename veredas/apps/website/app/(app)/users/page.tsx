import { createClient } from '@/utils/supabase/server'
import { redirect } from 'next/navigation'

const ROLE_BADGE: Record<string, string> = {
  MASTER_ADMIN: 'bg-purple-100 text-purple-800',
  ADMIN:        'bg-blue-100   text-blue-800',
  OPERATOR:     'bg-yellow-100 text-yellow-800',
  GUIDE:        'bg-green-100  text-green-800',
}

const ROLE_LABEL: Record<string, string> = {
  MASTER_ADMIN: 'Master Admin',
  ADMIN:        'Admin',
  OPERATOR:     'Operador',
  GUIDE:        'Guia',
}

export default async function UsersPage() {
  const supabase = createClient()

  // Only MASTER_ADMIN can access this page
  const { data: { user } } = await supabase.auth.getUser()
  if (!user) redirect('/login')

  const { data: profileData } = await supabase
    .from('users')
    .select('role')
    .eq('id', user.id)
    .single() as unknown as { data: { role: string } | null; error: unknown }

  if (profileData?.role !== 'MASTER_ADMIN') redirect('/dashboard')

  const { data: users } = await supabase
    .from('users')
    .select('id, full_name, email, role, created_at') as unknown as {
      data: Array<{ id: string; full_name: string | null; email: string; role: string; is_active?: boolean; last_sign_in_at?: string | null; created_at: string }> | null;
      error: unknown
    }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <p className="text-xs font-medium text-gray-400 uppercase tracking-widest mb-1">Administração</p>
          <h1 className="text-2xl font-bold text-gray-900">Gestão de Usuários</h1>
          <p className="mt-1 text-sm text-gray-500">Crie e gerencie os acessos ao sistema</p>
        </div>
        <button className="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-lg transition-colors">
          + Novo Usuário
        </button>
      </div>

      {/* Role Legend */}
      <div className="flex flex-wrap gap-3">
        {Object.entries(ROLE_LABEL).map(([role, label]) => (
          <div key={role} className="flex items-center gap-2">
            <span className={`px-2.5 py-0.5 rounded-full text-xs font-semibold ${ROLE_BADGE[role]}`}>{label}</span>
            <span className="text-xs text-gray-500">
              {role === 'MASTER_ADMIN' && 'Acesso total · Gerencia usuários'}
              {role === 'ADMIN' && 'Dashboards, CRM, operações'}
              {role === 'OPERATOR' && 'Módulos operacionais'}
              {role === 'GUIDE' && 'Expedições e checklists'}
            </span>
          </div>
        ))}
      </div>

      {/* Users Table */}
      <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <h2 className="text-sm font-semibold text-gray-700 flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            Usuários do Sistema
            <span className="ml-1 px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs">
              {users?.length ?? 0}
            </span>
          </h2>
        </div>

        {!users || users.length === 0 ? (
          <div className="py-16 flex flex-col items-center text-gray-400">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.2" opacity=".4">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
            </svg>
            <p className="mt-3 text-sm">Nenhum usuário encontrado.</p>
          </div>
        ) : (
          <table className="w-full text-sm">
            <thead>
              <tr className="text-xs font-medium text-gray-500 uppercase tracking-wider">
                <th className="px-6 py-3 text-left bg-gray-50">Usuário</th>
                <th className="px-6 py-3 text-left bg-gray-50">Cargo</th>
                <th className="px-6 py-3 text-left bg-gray-50">Status</th>
                <th className="px-6 py-3 text-left bg-gray-50">Último Acesso</th>
                <th className="px-6 py-3 text-left bg-gray-50">Criado em</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {users.map((u) => (
                <tr key={u.id} className="hover:bg-gray-50 transition-colors">
                  <td className="px-6 py-4">
                    <div className="flex items-center gap-3">
                      <div className="w-8 h-8 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-semibold text-xs">
                        {(u.full_name ?? u.email ?? '?')[0].toUpperCase()}
                      </div>
                      <div>
                        <p className="font-medium text-gray-900">{u.full_name ?? '—'}</p>
                        <p className="text-xs text-gray-500">{u.email}</p>
                      </div>
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <span className={`px-2.5 py-0.5 rounded-full text-xs font-semibold ${ROLE_BADGE[u.role] ?? 'bg-gray-100 text-gray-600'}`}>
                      {ROLE_LABEL[u.role] ?? u.role}
                    </span>
                  </td>
                  <td className="px-6 py-4">
                    <span className={`px-2.5 py-0.5 rounded-full text-xs font-semibold ${u.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>
                      {u.is_active ? 'Ativo' : 'Inativo'}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-gray-500">
                    {u.last_sign_in_at
                      ? new Date(u.last_sign_in_at).toLocaleDateString('pt-BR')
                      : '—'}
                  </td>
                  <td className="px-6 py-4 text-gray-500">
                    {new Date(u.created_at).toLocaleDateString('pt-BR')}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
    </div>
  )
}
