# 🎯 INÍCIO RÁPIDO - Expedition Management SaaS

## ⚡ TL;DR - Para Começar AGORA

### Primeira vez usando o projeto?

1. **Certifique-se de que o MySQL está rodando no XAMPP**
2. Execute:
```bash
setup.bat
```
3. Depois execute:
```bash
start.bat
```
4. Acesse: http://localhost:3000

---

## 📋 Scripts Disponíveis

### 🔧 Setup e Configuração

- **`setup.bat`** - Configuração inicial completa (use apenas na primeira vez)
  - Instala todas as dependências
  - Cria arquivos de configuração
  - Configura o banco de dados

- **`verificar-sistema.bat`** - Verifica se tudo está instalado corretamente

### 🚀 Iniciar o Projeto

- **`start.bat`** - Inicia frontend e backend juntos (RECOMENDADO)
- **`start-backend.bat`** - Inicia apenas o backend (Laravel)
- **`start-frontend.bat`** - Inicia apenas o frontend (React)

---

## 🌐 URLs Importantes

Após iniciar com `start.bat`:

| Serviço | URL | Descrição |
|---------|-----|-----------|
| **Frontend** | http://localhost:3000 | Interface do usuário |
| **Backend API** | http://localhost:8000 | API REST |
| **API Routes** | http://localhost:8000/api/v1 | Endpoints da API |

---

## ✅ Pré-requisitos

Antes de executar `setup.bat`, certifique-se de ter:

- [x] **XAMPP** instalado e MySQL rodando
- [x] **Node.js** (v18+) instalado
- [x] **Composer** instalado (ou use o do XAMPP)

---

## 🐛 Problemas Comuns

### MySQL não conecta
```bash
# Solução: Abra o XAMPP Control Panel e inicie o MySQL
```

### Porta 3000 ou 8000 em uso
```bash
# Feche o processo que está usando a porta ou use outra porta
# Para mudar a porta do frontend, edite package.json
```

### Erro ao instalar dependências
```bash
# Tente limpar o cache e reinstalar
npm cache clean --force
npm install

cd backend
composer clear-cache
composer install
```

---

## 📚 Documentação Completa

Para mais detalhes, consulte:
- [COMO_INICIAR.md](COMO_INICIAR.md) - Guia completo de instalação
- [README.md](README.md) - Documentação do projeto
- [backend/API_DOCUMENTATION.md](backend/API_DOCUMENTATION.md) - Documentação da API

---

## 🆘 Ajuda

**Não funciona?**
1. Execute `verificar-sistema.bat` para diagnosticar
2. Siga as instruções exibidas
3. Execute `setup.bat` novamente se necessário

---

**✨ Feito! Agora você está pronto para desenvolver!**
