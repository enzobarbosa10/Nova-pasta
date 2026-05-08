# 🚀 Expedition Management SaaS

Sistema completo de gestão de expedições com frontend React e backend Laravel.

---

## ⚡ INÍCIO RÁPIDO

**Primeira vez? Execute:**
```bash
setup.bat
```

**Depois, para iniciar o projeto:**
```bash
start.bat
```

**Acesse:** http://localhost:3000

📖 **Documentação completa:** [SAAS_PRONTO.md](SAAS_PRONTO.md)

---

## 📁 Estrutura do Projeto

```
├── backend/          # API Laravel
│   ├── app/
│   │   ├── Models/
│   │   └── Http/Controllers/Api/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   └── config/
│
└── src/              # Frontend React
    ├── components/
    ├── services/
    └── types.ts
```

## Backend (Laravel)

### Requisitos

- PHP 8.1 ou superior
- MySQL 5.7 ou superior
- Composer

### Configuração do Backend

1. **Navegue até a pasta backend:**
   ```bash
   cd backend
   ```

2. **Instale as dependências (se o Composer estiver disponível):**
   ```bash
   composer install
   ```

3. **Configure o arquivo .env:**
   ```bash
   cp .env.example .env
   ```

4. **Edite o arquivo .env com suas configurações do banco de dados:**
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=expedition_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Gere a chave da aplicação:**
   ```bash
   php artisan key:generate
   ```

6. **Crie o banco de dados:**
   ```sql
   CREATE DATABASE expedition_db;
   ```

7. **Execute as migrations:**
   ```bash
   php artisan migrate
   ```

8. **Execute os seeders (usuários de teste):**
   ```bash
   php artisan db:seed
   ```

9. **Inicie o servidor de desenvolvimento:**
   ```bash
   php artisan serve
   ```

   O backend estará disponível em: `http://localhost:8000`

### Usuários de Teste

Após executar os seeders, você terá acesso aos seguintes usuários:

- **Admin:** admin@expedition.com / password
- **Operator:** operator@expedition.com / password
- **Guide:** guide@expedition.com / password

### API Endpoints

#### Leads
- `GET /api/v1/leads` - Listar leads
- `POST /api/v1/leads` - Criar lead
- `GET /api/v1/leads/{id}` - Obter lead
- `PUT /api/v1/leads/{id}` - Atualizar lead
- `DELETE /api/v1/leads/{id}` - Deletar lead
- `PATCH /api/v1/leads/{id}/status` - Atualizar status
- `POST /api/v1/leads/{id}/notes` - Adicionar nota

#### Expeditions
- `GET /api/v1/expeditions` - Listar expedições
- `GET /api/v1/expeditions/public` - Listar expedições públicas
- `POST /api/v1/expeditions` - Criar expedição
- `GET /api/v1/expeditions/{id}` - Obter expedição
- `PUT /api/v1/expeditions/{id}` - Atualizar expedição
- `DELETE /api/v1/expeditions/{id}` - Deletar expedição
- `PATCH /api/v1/expeditions/{id}/status` - Atualizar status
- `POST /api/v1/expeditions/{id}/participants` - Adicionar participante
- `DELETE /api/v1/expeditions/{id}/participants/{participantId}` - Remover participante

#### Checklist Items
- `GET /api/v1/checklist-items` - Listar itens
- `POST /api/v1/checklist-items` - Criar item
- `GET /api/v1/checklist-items/{id}` - Obter item
- `PUT /api/v1/checklist-items/{id}` - Atualizar item
- `DELETE /api/v1/checklist-items/{id}` - Deletar item
- `PATCH /api/v1/checklist-items/{id}/toggle` - Alternar status
- `GET /api/v1/expeditions/{id}/checklist` - Listar por expedição

#### Media
- `GET /api/v1/media` - Listar mídia
- `POST /api/v1/media` - Criar mídia
- `GET /api/v1/media/{id}` - Obter mídia
- `PUT /api/v1/media/{id}` - Atualizar mídia
- `DELETE /api/v1/media/{id}` - Deletar mídia
- `POST /api/v1/media/bulk-upload` - Upload em lote
- `GET /api/v1/expeditions/{id}/media` - Listar por expedição

#### Traveler Portal
- `GET /api/v1/traveler-portal/{travelerId}` - Obter dados do viajante
- `GET /api/v1/traveler-portal/{travelerId}/itinerary` - Obter itinerário
- `GET /api/v1/traveler-portal/{travelerId}/documents` - Obter documentos

#### Dashboard
- `GET /api/v1/dashboard/stats` - Obter estatísticas

## Frontend (React)

### Requisitos

- Node.js 18 ou superior
- npm ou yarn

### Configuração do Frontend

1. **Instale as dependências:**
   ```bash
   npm install
   ```

2. **Configure as variáveis de ambiente:**
   ```bash
   cp .env.local.example .env.local
   ```

3. **Edite o .env.local:**
   ```
   VITE_API_URL=http://localhost:8000/api/v1
   GEMINI_API_KEY=your_gemini_api_key_here
   ```

4. **Inicie o servidor de desenvolvimento:**
   ```bash
   npm run dev
   ```

   O frontend estará disponível em: `http://localhost:5173`

### Serviço de API

O frontend possui um serviço centralizado de API em `src/services/api.ts` que gerencia todas as chamadas para o backend Laravel.

**Exemplo de uso:**

```typescript
import apiService from '@/services/api';

// Buscar leads
const leads = await apiService.getLeads({ status: 'NEW' });

// Criar expedição
const expedition = await apiService.createExpedition({
  name: 'Nova Expedição',
  destination: 'Chapada Diamantina',
  // ...
});

// Atualizar status
await apiService.updateLeadStatus(leadId, 'CONTACTED');
```

## Desenvolvimento

### Backend (Laravel)

Para criar novos endpoints:

1. Crie o Model e Migration:
   ```bash
   php artisan make:model NomeDoModel -m
   ```

2. Crie o Controller:
   ```bash
   php artisan make:controller Api/NomeDoController --api
   ```

3. Adicione as rotas em `routes/api.php`

### Frontend (React)

Para adicionar novos componentes:

1. Crie o componente em `src/components/`
2. Adicione os métodos da API em `src/services/api.ts`
3. Use o serviço no componente

## Autenticação

O sistema usa Laravel Sanctum para autenticação de API. Para autenticar:

1. Faça login e obtenha o token
2. Use o token nas requisições subsequentes
3. O serviço de API (`api.ts`) gerencia o token automaticamente

## CORS

O backend está configurado para aceitar requisições do frontend em `http://localhost:5173`. Para alterar, edite:

- `backend/config/cors.php`
- `backend/.env` (variável `FRONTEND_URL`)

## Estrutura do Banco de Dados

### Tabelas Principais

- **users** - Usuários do sistema (Admin, Operator, Guide, Traveler)
- **leads** - Leads de potenciais clientes
- **expeditions** - Expedições organizadas
- **checklist_items** - Itens de checklist operacional
- **media** - Banco de mídia (fotos, vídeos, etc.)

## Tecnologias Utilizadas

### Backend
- Laravel 11
- Laravel Sanctum (Autenticação)
- MySQL
- PHP 8.1+

### Frontend
- React 18
- TypeScript
- Vite
- TailwindCSS
- Shadcn/ui

## Licença

MIT
