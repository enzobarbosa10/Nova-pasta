# 🚀 Como Iniciar o Expedition Management SaaS

## 📋 Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- ✅ **XAMPP** (com MySQL/MariaDB ativo)
- ✅ **Node.js** (v18 ou superior)
- ✅ **Composer** (gerenciador de dependências PHP)

## 🎯 Início Rápido (Primeira Vez)

### 1️⃣ Setup Inicial

Execute o script de setup para configurar tudo automaticamente:

```bash
setup.bat
```

Este script irá:
- Instalar dependências do frontend (npm)
- Instalar dependências do backend (composer)
- Criar arquivos `.env` necessários
- Gerar chave da aplicação Laravel
- Executar migrations e seeders do banco de dados

### 2️⃣ Iniciar o Projeto

Após o setup inicial, você pode iniciar o projeto de duas formas:

#### Opção A: Iniciar Tudo de Uma Vez
```bash
start.bat
```

#### Opção B: Iniciar Separadamente
```bash
# Terminal 1 - Backend
start-backend.bat

# Terminal 2 - Frontend
start-frontend.bat
```

## 🌐 URLs de Acesso

Após iniciar o projeto:

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000
- **API Docs**: http://localhost:8000/api/documentation

## 🔧 Configuração Manual (se necessário)

### Backend (Laravel)

1. Navegue até a pasta backend:
```bash
cd backend
```

2. Instale as dependências:
```bash
composer install
```

3. Copie o arquivo de ambiente:
```bash
copy .env.example .env
```

4. Gere a chave da aplicação:
```bash
php artisan key:generate
```

5. Configure o banco de dados no arquivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=expedition_db
DB_USERNAME=root
DB_PASSWORD=
```

6. Execute as migrations:
```bash
php artisan migrate --seed
```

7. Inicie o servidor:
```bash
php artisan serve
```

### Frontend (React + Vite)

1. Instale as dependências:
```bash
npm install
```

2. Crie o arquivo `.env` na raiz do projeto:
```env
VITE_API_URL=http://localhost:8000/api/v1
```

3. Inicie o servidor de desenvolvimento:
```bash
npm run dev
```

## 📊 Banco de Dados

O projeto usa MySQL/MariaDB. Certifique-se de que o MySQL está rodando no XAMPP.

**Configuração padrão:**
- Host: 127.0.0.1
- Port: 3306
- Database: expedition_db
- Username: root
- Password: (vazio)

## 🧪 Dados de Teste

Após executar as migrations com seed, você terá:
- Usuários de teste
- Expedições de exemplo
- Leads de exemplo
- Itens de checklist
- Mídias de exemplo

## 🛠️ Comandos Úteis

### Backend

```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear

# Recriar banco de dados
php artisan migrate:fresh --seed

# Ver rotas disponíveis
php artisan route:list
```

### Frontend

```bash
# Build para produção
npm run build

# Verificar erros de TypeScript
npm run lint

# Limpar build
npm run clean
```

## ❌ Solução de Problemas

### Erro: "SQLSTATE[HY000] [1045] Access denied"
- Verifique se o MySQL está rodando no XAMPP
- Confirme as credenciais no arquivo `.env` do backend

### Erro: "Port 3000 is already in use"
- Outra aplicação está usando a porta 3000
- Altere a porta no arquivo `package.json` em `"dev": "vite --port=3001"`

### Erro: "Port 8000 is already in use"
- Outra aplicação está usando a porta 8000
- Altere no comando: `php artisan serve --port=8001`

### Frontend não conecta com Backend
- Verifique se o backend está rodando
- Confirme a URL no arquivo `.env` do frontend
- Verifique o console do navegador para erros CORS

## 📚 Documentação Adicional

- [README.md](README.md) - Documentação completa do projeto
- [API_DOCUMENTATION.md](backend/API_DOCUMENTATION.md) - Documentação da API
- [USER_FLOWS.md](USER_FLOWS.md) - Fluxos de usuário
- [UX_ARCHITECTURE.md](UX_ARCHITECTURE.md) - Arquitetura UX

## 🆘 Suporte

Se encontrar problemas:
1. Verifique os logs do Laravel em `backend/storage/logs`
2. Verifique o console do navegador para erros do frontend
3. Certifique-se de que todas as dependências estão instaladas
4. Confirme que o MySQL está rodando

---

**Desenvolvido com ❤️ para gestão de expedições**
