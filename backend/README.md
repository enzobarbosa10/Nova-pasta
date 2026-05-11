# Expedition Management API - Laravel Backend

Backend API para o sistema de gestão de expedições.

## Requisitos

- PHP 8.1 ou superior
- MySQL 5.7 ou superior
- Composer (opcional, mas recomendado)
- XAMPP ou servidor web com PHP

## Configuração Rápida

### 1. Configurar o Banco de Dados

Crie o banco de dados no MySQL:

```sql
CREATE DATABASE expedition_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Configurar o Arquivo .env

Copie o arquivo de exemplo:

```bash
copy .env.example .env
```

Edite o `.env` com suas configurações:

```env
APP_NAME="Expedition Management API"
APP_KEY=base64:GERE_UMA_CHAVE_AQUI
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=expedition_db
DB_USERNAME=root
DB_PASSWORD=

FRONTEND_URL=http://localhost:5173
```

### 3. Gerar Chave da Aplicação

Se o Composer estiver instalado:

```bash
php artisan key:generate
```

Caso contrário, gere uma chave base64 manualmente e adicione ao `.env`:

```env
APP_KEY=base64:sua_chave_gerada_aqui
```

### 4. Executar Migrations

```bash
php artisan migrate
```

Isso criará as seguintes tabelas:
- users
- leads
- expeditions
- checklist_items
- media
- sessions
- password_reset_tokens

### 5. Executar Seeders (Opcional)

Para criar usuários de teste:

```bash
php artisan db:seed
```

Usuários criados (emails):
- admin@expedition.com (role: ADMIN)
- operator@expedition.com (role: OPERATOR)
- guide@expedition.com (role: OPERATOR)

> **Segurança:** As senhas são geradas aleatoriamente em tempo de seed e gravadas em
> `storage/app/seed-credentials.txt` (arquivo gitignored). Nunca são armazenadas no código-fonte.

### 6. Iniciar o Servidor

```bash
php artisan serve
```

A API estará disponível em: `http://localhost:8000`

## Estrutura do Projeto

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── LeadController.php
│   │   │       ├── ExpeditionController.php
│   │   │       ├── ChecklistItemController.php
│   │   │       ├── MediaController.php
│   │   │       └── TravelerPortalController.php
│   │   └── Middleware/
│   └── Models/
│       ├── User.php
│       ├── Lead.php
│       ├── Expedition.php
│       ├── ChecklistItem.php
│       └── Media.php
├── config/
│   ├── app.php
│   ├── cors.php
│   ├── database.php
│   └── sanctum.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   ├── api.php
│   ├── web.php
│   └── console.php
└── public/
    └── index.php
```

## API Endpoints

### Base URL
```
http://localhost:8000/api/v1
```

### Autenticação

A API usa Laravel Sanctum para autenticação. Inclua o token no header:

```
Authorization: Bearer {seu_token}
```

### Rotas Disponíveis

#### Leads
```
GET    /api/v1/leads
POST   /api/v1/leads
GET    /api/v1/leads/{id}
PUT    /api/v1/leads/{id}
DELETE /api/v1/leads/{id}
PATCH  /api/v1/leads/{id}/status
POST   /api/v1/leads/{id}/notes
```

#### Expeditions
```
GET    /api/v1/expeditions
GET    /api/v1/expeditions/public
POST   /api/v1/expeditions
GET    /api/v1/expeditions/{id}
PUT    /api/v1/expeditions/{id}
DELETE /api/v1/expeditions/{id}
PATCH  /api/v1/expeditions/{id}/status
POST   /api/v1/expeditions/{id}/participants
DELETE /api/v1/expeditions/{id}/participants/{participantId}
```

#### Checklist Items
```
GET    /api/v1/checklist-items
POST   /api/v1/checklist-items
GET    /api/v1/checklist-items/{id}
PUT    /api/v1/checklist-items/{id}
DELETE /api/v1/checklist-items/{id}
PATCH  /api/v1/checklist-items/{id}/toggle
GET    /api/v1/expeditions/{id}/checklist
```

#### Media
```
GET    /api/v1/media
POST   /api/v1/media
GET    /api/v1/media/{id}
PUT    /api/v1/media/{id}
DELETE /api/v1/media/{id}
POST   /api/v1/media/bulk-upload
GET    /api/v1/expeditions/{id}/media
```

#### Traveler Portal
```
GET    /api/v1/traveler-portal/{travelerId}
GET    /api/v1/traveler-portal/{travelerId}/itinerary
GET    /api/v1/traveler-portal/{travelerId}/documents
```

#### Dashboard
```
GET    /api/v1/dashboard/stats
```

## Modelos de Dados

### Lead
```php
{
  "id": "uuid",
  "name": "string",
  "whatsapp": "string",
  "instagram": "string|null",
  "source": "string",
  "interest": "string",
  "destination": "string",
  "date_desired": "date",
  "people_count": "integer",
  "estimated_ticket": "decimal",
  "status": "enum",
  "notes": "text|null",
  "last_contact": "date",
  "next_follow_up": "date",
  "tags": "array|null"
}
```

### Expedition
```php
{
  "id": "uuid",
  "name": "string",
  "cover_image": "string|null",
  "destination": "string",
  "dates": "string",
  "capacity": "integer",
  "remaining_spots": "integer",
  "guide_id": "uuid|null",
  "accommodation": "string",
  "transport": "string",
  "trail_level": "enum",
  "status": "enum",
  "costs": "decimal",
  "margin_predicted": "decimal",
  "margin_real": "decimal|null",
  "participants": "array|null"
}
```

### ChecklistItem
```php
{
  "id": "uuid",
  "task": "string",
  "category": "enum",
  "status": "enum",
  "expedition_id": "uuid|null",
  "assigned_to": "uuid|null"
}
```

### Media
```php
{
  "id": "uuid",
  "url": "string",
  "type": "enum",
  "expedition_id": "uuid|null",
  "tags": "array|null",
  "created_at": "datetime"
}
```

## Configuração CORS

O backend está configurado para aceitar requisições do frontend em `http://localhost:5173`.

Para alterar, edite:
- `config/cors.php`
- `.env` (variável `FRONTEND_URL`)

## Comandos Úteis do Artisan

```bash
# Limpar cache
php artisan cache:clear

# Limpar cache de configuração
php artisan config:clear

# Limpar cache de rotas
php artisan route:clear

# Listar todas as rotas
php artisan route:list

# Criar um novo controller
php artisan make:controller NomeController

# Criar um novo model com migration
php artisan make:model NomeModel -m

# Executar migrations
php artisan migrate

# Reverter última migration
php artisan migrate:rollback

# Executar seeders
php artisan db:seed
```

## Troubleshooting

### Erro: "Please provide a valid cache path"
Crie as pastas necessárias:
```bash
mkdir bootstrap\cache
mkdir storage\framework\cache
mkdir storage\framework\sessions
mkdir storage\framework\views
```

### Erro: "No application encryption key"
Execute:
```bash
php artisan key:generate
```

### Erro de Conexão com o Banco
Verifique:
1. MySQL está rodando
2. Credenciais no `.env` estão corretas
3. Banco de dados existe

### Erro de CORS
Certifique-se de que `FRONTEND_URL` no `.env` está correto.

## Desenvolvimento

### Adicionar Novo Endpoint

1. Crie o método no Controller:
```php
public function novoMetodo(Request $request)
{
    // sua lógica aqui
}
```

2. Adicione a rota em `routes/api.php`:
```php
Route::get('/novo-endpoint', [Controller::class, 'novoMetodo']);
```

### Validação de Dados

Use a validação do Laravel nos controllers:
```php
$validated = $request->validate([
    'campo' => 'required|string|max:255',
    'outro_campo' => 'nullable|integer',
]);
```

## Licença

MIT
