# Guia Rápido de Setup - Expedition Management

## Para começar rapidamente:

### 1. Backend (Laravel)

```bash
# Entre na pasta backend
cd backend

# Configure o .env
copy .env.example .env

# Edite o .env e configure o banco de dados:
# DB_DATABASE=expedition_db
# DB_USERNAME=root
# DB_PASSWORD=

# Crie o banco de dados no MySQL
# No XAMPP: Acesse http://localhost/phpmyadmin
# Crie um banco chamado: expedition_db

# Execute as migrations
php artisan migrate

# (Opcional) Crie usuários de teste
php artisan db:seed

# Inicie o servidor
php artisan serve
```

Backend rodando em: `http://localhost:8000`

### 2. Frontend (React)

```bash
# Volte para a pasta raiz
cd ..

# Configure o .env.local
copy .env.local.example .env.local

# Instale as dependências (se ainda não instalou)
npm install

# Inicie o servidor
npm run dev
```

Frontend rodando em: `http://localhost:5173`

## Estrutura do Projeto

```
.
├── backend/              # API Laravel
│   ├── app/              # Código da aplicação
│   ├── config/           # Configurações
│   ├── database/         # Migrations e Seeders
│   ├── routes/           # Rotas da API
│   └── .env              # Configuração do ambiente
│
└── src/                  # Frontend React
    ├── components/       # Componentes React
    ├── services/         # Serviço de API
    └── types.ts          # Tipos TypeScript
```

## Usuários de Teste (após db:seed)

- Admin: admin@expedition.com / password
- Operator: operator@expedition.com / password
- Guide: guide@expedition.com / password

## Problemas Comuns

### Backend não inicia
- Verifique se o PHP está instalado: `php -v`
- Verifique se o MySQL está rodando (XAMPP)
- Certifique-se de que criou o banco de dados

### Erro de APP_KEY
Execute: `php artisan key:generate`

### Erro de CORS
Verifique se `FRONTEND_URL` no `.env` do backend está correto

### Frontend não conecta à API
Verifique se `VITE_API_URL` no `.env.local` está correto

## Próximos Passos

1. Acesse o frontend em `http://localhost:5173`
2. Faça login com um dos usuários de teste
3. Explore as funcionalidades:
   - Dashboard com estatísticas
   - Gestão de Leads (CRM)
   - Gestão de Expedições
   - Checklist Operacional
   - Banco de Mídia
   - Portal do Viajante

## Documentação Completa

- [README Principal](README.md)
- [README Backend](backend/README.md)
- [Documentação da API](backend/API_DOCUMENTATION.md)

## Suporte

Para mais informações, consulte os arquivos de documentação ou abra uma issue.
