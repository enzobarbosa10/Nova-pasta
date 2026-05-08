# 🗺️ Veredas Expedition Management System

**Sistema completo de gestão de expedições** com interface moderna e funcionalidades avançadas para agências de ecoturismo e turismo de aventura.

![Dashboard Preview](https://via.placeholder.com/1200x600/3B4A2F/FFFFFF?text=Veredas+Dashboard)

## ✨ Funcionalidades Principais

### 📊 Dashboard Inteligente
- **Métricas em tempo real** - Revenue, leads, conversion rates, avg ticket
- **Gráficos interativos** - Revenue trends, weekly performance, conversions
- **Expedições ativas** - Cards visuais com progress tracking
- **Alertas prioritários** - Notificações de ações importantes
- **Quick Actions** - Acesso rápido às funções mais usadas
- **Hot Leads** - Leads de alto valor destacados
- **Task Management** - Lista de tarefas com prioridades

### 🔔 Centro de Notificações
- Notificações em tempo real categorizadas
- Contador de não lidas com animação
- Filtros por tipo (lead, expedição, finance, operação)
- Marcação individual ou em massa
- Timestamps relativos

### 🔍 Busca Global
- Atalho rápido (⌘K / Ctrl+K)
- Busca unificada em todo o sistema
- Resultados categorizados com preview
- Navegação por teclado
- Metadata rica

### 📈 Analytics Avançado
- **Revenue Overview** - Análise de receita e lucro
- **Lead Sources** - Distribuição por canal
- **Top Destinations** - Performance por destino
- **Sales Funnel** - Análise de conversão por etapa
- **Cost Analysis** - Breakdown de custos
- **Múltiplos gráficos** - Area, Bar, Line, Pie charts

### 📅 Calendário Interativo
- Visualização mensal de expedições
- Eventos multi-dia
- Painel lateral com detalhes
- Tipos de evento (expedições, deadlines, meetings, follow-ups)
- Navegação intuitiva

### 👥 CRM Kanban
- Pipeline visual de leads
- Múltiplos estágios (New, Contacted, Qualified, Proposal)
- Cards interativos com informações completas
- Filtros e busca
- Ações rápidas

### 🗺️ Gestão de Expedições
- Catálogo visual de expedições
- Status tracking (Open, Confirmed, In Progress, Completed)
- Capacity management
- Logistics health indicators
- Informações detalhadas

### ⚙️ Operações
- Checklist pré/durante/pós expedição
- Atribuição de tarefas
- Status tracking
- Workflow structured

### 🖼️ Media Bank
- Organização de fotos e vídeos
- Categorização por tipo e expedição
- Tags personalizadas

### 👤 Portal do Viajante
- Informações da expedição
- Documentos e mapas
- Checklist pessoal
- Status de pagamentos

---

## 🚀 Começando

### Pré-requisitos

- Node.js 18+ 
- PHP 8.1+
- MySQL 5.7+
- Composer

### Instalação

#### 1. Clone o repositório
```bash
git clone <url-do-repositorio>
cd "Nova pasta"
```

#### 2. Instale dependências do Frontend
```bash
npm install
```

#### 3. Configure o Backend
```bash
cd backend
composer install
cp .env.example .env
# Edite o .env com suas configurações de banco de dados
php artisan key:generate
php artisan migrate --seed
```

#### 4. Inicie os servidores

**Frontend (Porta 3000):**
```bash
npm run dev
```

**Backend (Porta 8000):**
```bash
cd backend
php artisan serve
```

#### 5. Acesse a aplicação
```
Frontend: http://localhost:3000
Backend API: http://localhost:8000
```

---

## 🎯 Navegação Rápida

### Atalhos de Teclado

| Atalho | Ação |
|--------|------|
| `⌘K` / `Ctrl+K` | Abrir busca global |
| `Esc` | Fechar modais |
| `↑` `↓` | Navegar resultados |
| `Enter` | Selecionar item |

### Estrutura de Navegação

```
Dashboard          → Visão geral e métricas principais
Analytics          → Relatórios avançados e gráficos
CRM Leads          → Pipeline de vendas Kanban
Expeditions        → Catálogo e gestão de viagens
Calendar           → Visualização temporal
Operations         → Checklists operacionais
Traveler Portal    → Interface para viajantes
Media Bank         → Biblioteca de mídia
Financials         → Gestão financeira
Settings           → Configurações do sistema
```

---

## 🛠️ Tecnologias

### Frontend
- **React 19** - Framework UI
- **TypeScript** - Type safety
- **Vite** - Build tool
- **Tailwind CSS 4** - Styling
- **Shadcn/UI** - Component library
- **Radix UI** - Headless components
- **Recharts** - Data visualization
- **Framer Motion** - Animations
- **Lucide React** - Icons

### Backend
- **Laravel 11** - PHP Framework
- **MySQL** - Database
- **Sanctum** - API authentication

---

## 📁 Estrutura do Projeto

```
Nova pasta/
├── src/
│   ├── components/
│   │   ├── Dashboard.tsx              # Dashboard principal
│   │   ├── AdvancedAnalytics.tsx      # Analytics completo
│   │   ├── KanbanBoard.tsx            # CRM Pipeline
│   │   ├── ExpeditionList.tsx         # Lista de expedições
│   │   ├── ExpeditionCalendar.tsx     # Calendário
│   │   ├── NotificationCenter.tsx     # Central de notificações
│   │   ├── GlobalSearch.tsx           # Busca global
│   │   ├── ActivityFeed.tsx           # Feed de atividades
│   │   ├── AppLayout.tsx              # Layout principal
│   │   ├── OperationalChecklist.tsx   # Checklists
│   │   ├── MediaBank.tsx              # Banco de mídia
│   │   ├── TravelerPortal.tsx         # Portal do viajante
│   │   └── ui/                        # Componentes base
│   ├── services/
│   │   └── api.ts                     # Client API
│   ├── types.ts                       # TypeScript types
│   ├── constants.ts                   # Mock data
│   └── App.tsx                        # App root
├── backend/
│   ├── app/
│   │   ├── Models/                    # Eloquent models
│   │   └── Http/Controllers/Api/      # API controllers
│   ├── routes/
│   │   └── api.php                    # API routes
│   └── database/
│       ├── migrations/                # Schema migrations
│       └── seeders/                   # Data seeders
└── package.json
```

---

## 🎨 Paleta de Cores

```css
/* Cores Principais */
--primary: #3B4A2F;      /* Verde Musgo */
--secondary: #C4A882;    /* Areia */
--accent: #10B981;       /* Emerald */
--background: #FAFAFA;   /* Quase Branco */
--muted: #F5F2ED;        /* Creme */

/* Cores Semânticas */
--success: #10B981;      /* Emerald 500 */
--warning: #F59E0B;      /* Amber 500 */
--error: #EF4444;        /* Red 500 */
--info: #3B82F6;         /* Blue 500 */
```

---

## 📊 Dados de Exemplo

O sistema vem com dados mock incluídos:
- ✅ 2 leads de exemplo
- ✅ 1 expedição configurada
- ✅ 8+ notificações
- ✅ 8+ atividades recentes
- ✅ 5+ eventos de calendário
- ✅ 7 meses de dados financeiros

---

## 🔐 Autenticação (Backend)

### Usuários de Teste

| Email | Senha | Role |
|-------|-------|------|
| admin@expedition.com | password | Admin |
| operator@expedition.com | password | Operator |
| guide@expedition.com | password | Guide |

---

## 🚧 Roadmap

### Em Desenvolvimento
- [ ] Drag & Drop no Kanban
- [ ] Real-time com WebSockets
- [ ] Dark Mode
- [ ] Export de relatórios (PDF/Excel)

### Planejado
- [ ] Integrações (WhatsApp, Stripe, Google Calendar)
- [ ] Chat interno
- [ ] Mobile App (React Native)
- [ ] AI Assistant para sugestões
- [ ] Widgets customizáveis

---

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

---

## 📝 Licença

Este projeto está sob a licença MIT.

---

## 📧 Contato

Para dúvidas e suporte, entre em contato através de [seu-email@exemplo.com]

---

## 🌟 Agradecimentos

- Shadcn/UI pela biblioteca de componentes
- Radix UI pelos primitives
- Recharts pelos gráficos
- Lucide pela biblioteca de ícones

---

## 📚 Documentação Adicional

- [IMPROVEMENTS.md](./IMPROVEMENTS.md) - Detalhes das melhorias implementadas
- [API_DOCUMENTATION.md](./backend/API_DOCUMENTATION.md) - Documentação da API
- [PRODUCT_FLOW_README.md](./PRODUCT_FLOW_README.md) - Fluxos de produto
- [UX_ARCHITECTURE.md](./UX_ARCHITECTURE.md) - Arquitetura UX

---

**Desenvolvido com ❤️ para tornar a gestão de expedições mais eficiente e prazerosa.**
