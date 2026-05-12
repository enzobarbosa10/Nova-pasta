# Veredas — Arquitetura Next.js + Supabase

> **Migração completa de Laravel + MySQL → Next.js + Supabase**  
> Stack: Next.js 14 (App Router) · Supabase Auth · PostgreSQL · Supabase Storage · TypeScript · Tailwind CSS

---

## Estrutura do projeto

```
veredas/apps/website/
├── app/
│   ├── (app)/                  # Rotas protegidas (require auth)
│   │   ├── layout.tsx          # Layout com Sidebar
│   │   ├── dashboard/page.tsx  # Dashboard com métricas
│   │   ├── expeditions/
│   │   │   ├── page.tsx        # Lista de expedições
│   │   │   ├── actions.ts      # Server Actions (CRUD)
│   │   │   ├── new/page.tsx    # Formulário nova expedição
│   │   │   └── [id]/page.tsx   # Detalhe + edição + checklist
│   │   ├── leads/
│   │   │   ├── page.tsx        # CRM / lista de leads
│   │   │   ├── actions.ts      # Server Actions (CRUD + notas)
│   │   │   ├── new/page.tsx    # Formulário novo lead
│   │   │   └── [id]/page.tsx   # Detalhe + edição + histórico
│   │   ├── users/page.tsx      # Gestão de usuários (MASTER_ADMIN)
│   │   ├── media/page.tsx      # Upload e galeria de mídias
│   │   ├── analytics/page.tsx  # Funil de vendas + receita
│   │   └── calendar/page.tsx   # Calendário de expedições
│   └── login/
│       ├── page.tsx            # Tela de login
│       └── actions.ts          # signIn / signOut (Server Actions)
│
├── lib/                        # Utilitários centrais
│   ├── supabase/
│   │   ├── client.ts           # createBrowserClient (Client Components)
│   │   ├── server.ts           # createServerClient  (Server Components)
│   │   └── index.ts            # barrel export
│   └── auth/
│       └── helpers.ts          # getAuthUser, requireAuth, requireRole, signOut…
│
├── types/
│   └── index.ts                # Todos os tipos TypeScript + Database interface
│
├── hooks/                      # Hooks reutilizáveis (client-side)
│   ├── useAuth.ts              # Sessão, signIn, signOut, sendPasswordReset
│   ├── useSupabase.ts          # Typed Supabase client + useQuery generic
│   └── useRealtime.ts          # Realtime subscriptions (Postgres Changes)
│
├── services/                   # Lógica de negócio (server-safe)
│   ├── expeditionService.ts    # CRUD de expedições + validações
│   ├── bookingService.ts       # Reservas + controle de vagas
│   └── userService.ts          # Perfis de usuário
│
├── components/
│   └── Sidebar.tsx             # Navegação lateral
│
├── providers/
│   └── AuthProvider.tsx        # Context de autenticação global
│
├── utils/supabase/             # Helpers genéricos (database, storage, realtime)
│
├── middleware.ts               # Proteção de rotas + refresh de sessão
└── .env.example                # Variáveis de ambiente documentadas
```

---

## Como rodar localmente

### 1. Pré-requisitos
- Node.js 18+
- Conta no [Supabase](https://supabase.com) (gratuito)

### 2. Clone e instale

```bash
cd veredas/apps/website
npm install
```

### 3. Configure o banco de dados

No dashboard do Supabase → **SQL Editor**, execute os arquivos na ordem:

```
veredas/supabase/migrations/001_initial_schema.sql
veredas/supabase/migrations/002_rls_policies.sql
```

### 4. Configure os buckets de Storage

No dashboard do Supabase → **Storage**, crie:

| Bucket            | Visibilidade |
|-------------------|-------------|
| `expeditions-media` | Public     |
| `avatars`           | Public     |
| `documents`         | Private    |

### 5. Variáveis de ambiente

```bash
cp .env.example .env.local
# Edite .env.local com suas chaves do Supabase
```

| Variável                       | Onde encontrar                              |
|--------------------------------|---------------------------------------------|
| `NEXT_PUBLIC_SUPABASE_URL`     | Dashboard → Settings → API → Project URL   |
| `NEXT_PUBLIC_SUPABASE_ANON_KEY`| Dashboard → Settings → API → anon key      |
| `SUPABASE_SERVICE_ROLE_KEY`    | Dashboard → Settings → API → service_role  |
| `NEXT_PUBLIC_SITE_URL`         | `http://localhost:3000` (dev)              |

### 6. Crie o primeiro usuário admin

No Supabase Dashboard → **Authentication → Users** → Add user.

Em seguida, no **SQL Editor**:
```sql
UPDATE public.users SET role = 'MASTER_ADMIN' WHERE email = 'seu@email.com';
```

### 7. Rode o servidor

```bash
npm run dev
# Acesse: http://localhost:3000
```

---

## Autenticação

| Fluxo                | Como funciona                                             |
|----------------------|-----------------------------------------------------------|
| Login                | Server Action → `supabase.auth.signInWithPassword`        |
| Logout               | Server Action → `supabase.auth.signOut`                   |
| Sessão persistente   | Middleware lê/escreve cookies automaticamente             |
| Proteção de rotas    | `middleware.ts` redireciona para `/login` se não autenticado |
| Proteção por role    | `lib/auth/helpers.ts` → `requireRole(['ADMIN', 'OPERATOR'])` |
| Refresh de token     | Middleware executa `supabase.auth.getUser()` em cada request |

### Proteção de páginas (Server Component)

```ts
import { requireAuth, requireRole } from '@/lib/auth/helpers'

// Qualquer usuário autenticado
export default async function Page() {
  const user = await requireAuth()
  ...
}

// Apenas admin
export default async function AdminPage() {
  const profile = await requireRole(['MASTER_ADMIN', 'ADMIN'])
  ...
}
```

---

## CRUD com Server Actions

```ts
// app/(app)/leads/actions.ts
'use server'
import { revalidatePath } from 'next/cache'
import { createClient } from '@/lib/supabase/server'
import { requireAuth } from '@/lib/auth/helpers'

export async function createLead(formData: FormData) {
  await requireAuth()
  const supabase = createClient()
  const { error } = await supabase.from('leads').insert({ ... })
  revalidatePath('/leads')
}
```

```tsx
// app/(app)/leads/new/page.tsx
import { createLead } from '../actions'

export default function NewLeadPage() {
  return (
    <form action={createLead}>
      <input name="name" required />
      <button type="submit">Criar</button>
    </form>
  )
}
```

---

## Supabase Storage — Upload de imagens

```ts
// Client Component
import { uploadExpeditionImage } from '@/utils/supabase/storage'

async function handleUpload(file: File) {
  const { url, error } = await uploadExpeditionImage(expeditionId, file)
  if (error) console.error(error)
  // url = https://...supabase.co/storage/v1/object/public/expeditions/...
}
```

---

## Row Level Security (RLS)

Toda segurança de acesso é gerenciada via políticas no banco de dados — não no código.  
Cada tabela tem políticas que verificam o role do usuário autenticado via:

```sql
-- Função helper que retorna o role do usuário atual
public.auth_user_role()
```

| Role            | Expedições | Leads | Usuários |
|-----------------|-----------|-------|----------|
| `MASTER_ADMIN`  | R/W/D     | R/W/D | R/W/D    |
| `ADMIN`         | R/W/D     | R/W/D | R       |
| `OPERATOR`      | R/W       | R/W   | —        |
| `GUIDE`         | R         | R*    | —        |
| `TRAVELER`      | R (pub)   | —     | próprio  |

*Guias veem apenas leads atribuídos a eles.

---

## Deploy

### Vercel (recomendado)

```bash
# Na raiz do repositório
vercel --cwd veredas/apps/website
```

Adicione as variáveis de ambiente no painel da Vercel:
- `NEXT_PUBLIC_SUPABASE_URL`
- `NEXT_PUBLIC_SUPABASE_ANON_KEY`
- `SUPABASE_SERVICE_ROLE_KEY`
- `NEXT_PUBLIC_SITE_URL` → URL de produção

### Railway / Render / Fly.io

```bash
cd veredas/apps/website
npm run build
npm start
```

---

## O que foi removido (Laravel)

| Laravel                   | Substituído por                        |
|---------------------------|----------------------------------------|
| `app/Http/Controllers/`   | `app/(app)/*/actions.ts` (Server Actions) |
| `app/Models/`             | `types/index.ts` + queries Supabase    |
| `routes/api.php`          | App Router + Server Actions            |
| `Sanctum` auth            | Supabase Auth (JWT + cookies)          |
| `database/migrations/`    | `supabase/migrations/*.sql`            |
| MySQL                     | PostgreSQL (Supabase)                  |
| PHP Middleware             | `middleware.ts` (Next.js Edge)         |
| `vendor/`                 | `node_modules/` (npm)                  |

---

## Boas práticas adotadas

- **Server Components por padrão** — queries no servidor, zero waterfall  
- **Server Actions** para mutações — formulários nativos HTML, sem fetch manual  
- **`getUser()` não `getSession()`** — valida JWT com Supabase, não só cookie  
- **RLS em todas as tabelas** — segurança no banco, não apenas no código  
- **`revalidatePath`** após mutações — cache limpo automaticamente  
- **Soft delete** em expedições — `deleted_at` em vez de `DELETE`  
- **`@ts-strict`** nos tipos — Database interface completa e tipada
