# 🚀 Guia de Início Rápido - Versão PHP

## ✅ Pré-requisitos

- XAMPP instalado (PHP 8.x + Apache + MySQL)
- Navegador moderno (Chrome, Firefox, Edge)

## 📦 Instalação

### 1. Iniciar Serviços XAMPP

```bash
# Abra o XAMPP Control Panel
# Inicie os serviços:
- Apache ✓
- MySQL ✓
```

### 2. Configurar Banco de Dados

```bash
# No terminal, navegue até a pasta backend
cd "c:\xampp\htdocs\Nova pasta\backend"

# Execute as migrations
C:\xampp\php\php.exe artisan migrate --seed
```

### 3. Iniciar Aplicação

**Opção 1: Usar o script automático**
```bash
# Clique duas vezes em:
start-php.bat
```

**Opção 2: Abrir manualmente**
```
Abra no navegador:
http://localhost/backend/public/dashboard.php
```

**Opção 3: Página de boas-vindas**
```
Abra no navegador:
http://localhost/welcome.html
```

## 🎯 Páginas Disponíveis

### Dashboard
```
http://localhost/backend/public/dashboard.php
```
- Métricas em tempo real
- Feed de atividades
- Estatísticas consolidadas

### Expedições
```
http://localhost/backend/public/expeditions.php
```
- Catálogo de expedições
- Criar nova expedição
- Gerenciar expedições

### CRM
```
http://localhost/backend/public/crm.php
```
- Pipeline Kanban
- Gestão de leads
- Arrastar e soltar cards

### Calendário
```
http://localhost/backend/public/calendar.php
```
- Visualização mensal
- Eventos de expedições

### Banco de Mídia
```
http://localhost/backend/public/media.php
```
- Galeria de fotos
- Gestão de mídia

### Analytics
```
http://localhost/backend/public/analytics.php
```
- Gráficos e relatórios
- Métricas avançadas

## 🔧 Solução de Problemas

### Apache não inicia
```
- Verifique se a porta 80 está livre
- Feche Skype ou outros programas usando porta 80
- Configure porta alternativa no XAMPP (ex: 8080)
```

### MySQL não inicia
```
- Verifique se a porta 3306 está livre
- Verifique logs em: C:\xampp\mysql\data\
```

### Página em branco
```
- Verifique se o Apache está rodando
- Confirme o caminho correto da URL
- Verifique logs do PHP: C:\xampp\apache\logs\error.log
```

### CSS não carrega
```
- Limpe o cache do navegador (Ctrl + Shift + Del)
- Verifique permissões da pasta
- Confirme que o caminho está correto
```

### API não responde
```
# Configure CORS no Laravel
# Arquivo: backend/config/cors.php

'paths' => ['api/*'],
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
```

## 📁 Estrutura de Arquivos

```
Nova pasta/
├── welcome.html              # Página de boas-vindas
├── start-php.bat            # Script de inicialização
├── PHP_CONVERSION_README.md # Documentação detalhada
└── backend/
    └── public/
        ├── dashboard.php    # Dashboard
        ├── expeditions.php  # Expedições
        ├── crm.php         # CRM
        ├── calendar.php    # Calendário
        ├── media.php       # Mídia
        ├── analytics.php   # Analytics
        ├── includes/
        │   ├── header.php  # Cabeçalho comum
        │   └── footer.php  # Rodapé comum
        ├── css/
        │   ├── main.css
        │   └── components.css
        └── js/
            ├── api.js
            ├── main.js
            ├── dashboard.js
            ├── expeditions.js
            ├── crm.js
            ├── calendar.js
            ├── media.js
            └── analytics.js
```

## 🎨 Recursos

### Design
- ✅ Design moderno e responsivo
- ✅ Animações suaves
- ✅ Ícones SVG integrados
- ✅ Paleta de cores profissional

### Funcionalidades
- ✅ Navegação sidebar
- ✅ Busca global
- ✅ Notificações
- ✅ Modais interativos
- ✅ Formulários validados
- ✅ Drag & drop (CRM)

### Performance
- ✅ Sem build process
- ✅ Carregamento instantâneo
- ✅ CSS puro (~50KB)
- ✅ JavaScript otimizado

## 📚 Próximos Passos

1. **Explorar as páginas**
   - Navegue por todas as seções
   - Teste as funcionalidades
   - Experimente criar expedições

2. **Configurar API**
   - Conecte com backend Laravel
   - Configure autenticação
   - Teste endpoints

3. **Personalizar**
   - Ajuste cores em CSS
   - Adicione novos componentes
   - Customize layout

## 💡 Dicas

- Use F12 para abrir DevTools e debugar
- Console do navegador mostra erros JavaScript
- Network tab mostra requisições da API
- PHP errors aparecem em: `C:\xampp\apache\logs\error.log`

## 🆘 Suporte

Problemas? Verifique:
1. ✅ Apache está rodando?
2. ✅ MySQL está rodando?
3. ✅ URL está correta?
4. ✅ Logs de erro?

## 🎉 Pronto!

Seu sistema está funcionando! Acesse:
```
http://localhost/welcome.html
```

Ou vá direto para o dashboard:
```
http://localhost/backend/public/dashboard.php
```

---

**Nota**: Este projeto foi convertido de React/TypeScript para PHP/CSS/JavaScript para melhor performance e simplicidade.
