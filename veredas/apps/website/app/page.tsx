import { createClient } from '@/utils/supabase/server'
import { cookies } from 'next/headers'

async function carregarUsuarios() {
  const cookieStore = await cookies()
  const supabase = createClient(cookieStore)

  const { data, error } = await supabase
    .from('users')
    .select('*')

  if (error) {
    console.error('Erro ao carregar usuários:', error)
    return []
  }

  console.log(data)
  return data
}

export default async function Page() {
  const users = await carregarUsuarios()

  return (
    <ul>
      {users?.map((user: { id: string | number; email?: string; name?: string }) => (
        <li key={user.id}>{user.email ?? user.name ?? String(user.id)}</li>
      ))}
    </ul>
  )
}
