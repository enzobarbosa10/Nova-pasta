# 🚀 Guia de Início Rápido - Veredas Expedition Management

## 🎯 Em 5 Minutos

### 1. Instalar Dependências (2 min)
```bash
# No diretório raiz do projeto
npm install

# No backend
cd backend
composer install
```

### 2. Configurar Backend (1 min)
```bash
cd backend
cp .env.example .env
# Edite o .env com suas credenciais do MySQL
php artisan key:generate
php artisan migrate --seed
```

### 3. Iniciar Aplicação (1 min)
```bash
# Terminal 1 - Frontend
npm run dev

# Terminal 2 - Backend
cd backend
php artisan serve
```

### 4. Acessar (1 min)
- Frontend: http://localhost:3000
- Backend: http://localhost:8000

---

## 🎨 Tour Rápido da Interface

### Dashboard Principal
```
http://localhost:3000
```
- ✅ Métricas de revenue, leads, conversões
- ✅ Gráficos interativos
- ✅ Expedições ativas
- ✅ Alertas prioritários
- ✅ Hot leads
- ✅ Tarefas do dia

### Busca Global
```
Pressione: ⌘K (Mac) ou Ctrl+K (Windows)
```
- Digite qualquer termo
- Navegue com setas ↑↓
- Enter para selecionar

### Notificações
```
Clique no ícone de sino no topo
```
- Visualize notificações não lidas
- Clique para marcar como lida
- "Mark all read" para limpar todas

### Analytics
```
Menu lateral → Analytics
```
- 4 abas: Revenue, Lead Sources, Destinations, Funnel
- Múltiplos gráficos interativos
- Exportação de dados (em breve)

### Calendário
```
Menu lateral → Calendar
```
- Visualize expedições por data
- Clique em um dia para ver detalhes
- Navegue pelos meses

### CRM Kanban
```
Menu lateral → CRM Leads
```
- Veja pipeline visual de leads
- 4 colunas: New, Contacted, Qualified, Proposal
- Busque e filtre leads

### Expedições
```
Menu lateral → Expeditions
```
- Catálogo de expedições
- Status e capacidade
- Gestão de logística

---

## 🎯 Fluxos Principais

### Adicionar Novo Lead
1. Dashboard → Seção "Hot Leads"
2. Clique em "+"
3. Preencha informações
4. Salvar

### Criar Nova Expedição
1. Menu → Expeditions
2. Clique "Draft New Journey"
3. Preencha detalhes
4. Upload de imagens
5. Definir capacidade
6. Salvar

### Gerenciar Tarefas
1. Dashboard → "Today's Tasks"
2. Clique no checkbox para completar
3. Adicione novas tarefas com "+"

### Visualizar Analytics
1. Menu → Analytics
2. Selecione aba desejada
3. Interaja com gráficos
4. Filtre por período

---

## 🔑 Atalhos Úteis

| Atalho | Ação |
|--------|------|
| `⌘K` / `Ctrl+K` | Busca global |
| `Esc` | Fechar modais |
| `↑` `↓` | Navegar |
| `Enter` | Selecionar |

---

## 📊 Dados Demo

O sistema vem com dados de exemplo:

### Leads
- Marcus Aurelius (Iceland - $18,500)
- Sereina Wu (Kenya - $12,000)
- Julian Draxler (Amazon - $8,400)

### Expedições
- Amazon River Basin (Aug 12-24)
- Patagonia Peaks (Sept 04-15)

### Notificações
- 5+ notificações de exemplo
- Diferentes categorias e prioridades

---

## 🎨 Customização Rápida

### Cores
Edite `src/index.css`:
```css
:root {
  --primary: #3B4A2F;     /* Sua cor primária */
  --secondary: #C4A882;   /* Sua cor secundária */
}
```

### Logo
Substitua em:
- `AppLayout.tsx` (sidebar)
- Adicione na pasta `public/`

### Mock Data
Edite `src/constants.ts`:
- `MOCK_LEADS` - Leads de exemplo
- `MOCK_EXPEDITIONS` - Expedições de exemplo

---

## 🐛 Resolução de Problemas

### Erro: "Port 3000 já em uso"
```bash
# Encontre e mate o processo
netstat -ano | findstr :3000
taskkill /PID <PID> /F

# Ou use outra porta
npm run dev -- --port 3001
```

### Erro: "Cannot find module"
```bash
# Limpe e reinstale
rm -rf node_modules package-lock.json
npm install
```

### Erro: "Database connection failed"
1. Verifique MySQL está rodando
2. Confirme credenciais no `.env`
3. Crie o banco de dados manualmente:
```sql
CREATE DATABASE expedition_db;
```

### Erro: TypeScript
```bash
# Rebuilde o projeto
npm run build
```

---

## 📚 Próximos Passos

1. ✅ Explore cada seção do menu
2. ✅ Teste a busca global
3. ✅ Adicione seus próprios dados
4. ✅ Customize as cores
5. ✅ Configure integrações
6. ✅ Leia a documentação completa

---

## 🤝 Precisa de Ajuda?

- 📖 [Documentação Completa](./README_UPDATED.md)
- 🔧 [Melhorias Implementadas](./IMPROVEMENTS.md)
- 🏗️ [Arquitetura UX](./UX_ARCHITECTURE.md)
- 🔌 [API Documentation](./backend/API_DOCUMENTATION.md)

---

## 🎉 Você está pronto!

Seu sistema está configurado e pronto para uso. Explore as funcionalidades e personalize conforme suas necessidades.

**Happy Managing! 🗺️✨**
