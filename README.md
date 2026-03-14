# 💳 BeTalent Payment API

API RESTful de gerenciamento de pagamentos multi-gateway desenvolvida em Laravel 11 com Docker.

---

## 📋 Sobre o Projeto

Sistema que gerencia pagamentos através de múltiplos gateways com fallback automático. Ao realizar uma compra, o sistema tenta processar pelo gateway de maior prioridade e, em caso de falha, automaticamente tenta o próximo gateway disponível.

---

## 🛠️ Tecnologias

- **PHP 8.2**
- **Laravel 11**
- **MySQL 8.0**
- **Docker + Docker Compose**
- **Laravel Sanctum** (autenticação via token)
- **Nginx**

---

## ✅ Requisitos

- Docker Desktop instalado e rodando
- Docker Compose v2+
- Portas disponíveis: `8000`, `3001`, `3002`, `3306`, `8080`

---

## 🚀 Como Instalar e Rodar

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/betalent-api.git
cd betalent-api
```

### 2. Copie o arquivo de ambiente

```bash
cp .env.example .env
```

### 3. Suba os containers

```bash
docker-compose up -d --build
```

### 4. Instale as dependências

```bash
docker exec betalent-app composer install
```

### 5. Gere a chave da aplicação

```bash
docker exec betalent-app php artisan key:generate
```

### 6. Rode as migrations e seeders

```bash
docker exec betalent-app php artisan migrate --seed
```

### 7. Acesse a aplicação

- **API:** `http://localhost:8000/api`
- **Adminer (DB):** `http://localhost:8080`
- **Gateway 1:** `http://localhost:3001`
- **Gateway 2:** `http://localhost:3002`

---

## 🔑 Credenciais padrão (Seeder)

```
Email: admin@test.com
Senha: 123456
Role:  ADMIN
```

---

## 🛣️ Rotas da API

### Públicas (sem autenticação)

| Método | Rota | Descrição |
|--------|------|-----------|
| POST | `/api/login` | Autenticação do usuário |
| POST | `/api/transactions` | Realizar uma compra |

### Privadas (requer Bearer Token)

#### Transações
| Método | Rota | Roles | Descrição |
|--------|------|-------|-----------|
| GET | `/api/transactions` | Todos | Listar todas as transações |
| GET | `/api/transactions/{id}` | Todos | Detalhe de uma transação |
| POST | `/api/transactions/{id}/refund` | ADMIN, FINANCE | Reembolso de uma transação |

#### Clientes
| Método | Rota | Roles | Descrição |
|--------|------|-------|-----------|
| GET | `/api/clients` | Todos | Listar todos os clientes |
| GET | `/api/clients/{id}` | Todos | Detalhe do cliente e suas compras |

#### Produtos
| Método | Rota | Roles | Descrição |
|--------|------|-------|-----------|
| GET | `/api/products` | Todos | Listar produtos |
| GET | `/api/products/{id}` | Todos | Detalhe de um produto |
| POST | `/api/products` | ADMIN, MANAGER | Criar produto |
| PUT | `/api/products/{id}` | ADMIN, MANAGER | Atualizar produto |
| DELETE | `/api/products/{id}` | ADMIN | Remover produto |

#### Gateways
| Método | Rota | Roles | Descrição |
|--------|------|-------|-----------|
| PATCH | `/api/gateways/{id}/toggle` | ADMIN | Ativar/desativar gateway |
| PATCH | `/api/gateways/{id}/priority` | ADMIN | Alterar prioridade do gateway |

#### Usuários
| Método | Rota | Roles | Descrição |
|--------|------|-------|-----------|
| GET | `/api/users` | ADMIN, MANAGER | Listar usuários |
| POST | `/api/users` | ADMIN, MANAGER | Criar usuário |
| GET | `/api/users/{id}` | ADMIN, MANAGER | Detalhe de um usuário |
| PUT | `/api/users/{id}` | ADMIN, MANAGER | Atualizar usuário |
| DELETE | `/api/users/{id}` | ADMIN, MANAGER | Remover usuário |

---

## 📦 Exemplos de Requisições

### Login
```json
POST /api/login
{
    "email": "admin@test.com",
    "password": "123456"
}
```

### Realizar Compra
```json
POST /api/transactions
{
    "name": "Cliente Teste",
    "email": "cliente@teste.com",
    "products": [
        { "product_id": 1, "quantity": 2 }
    ],
    "cardNumber": "5569000000006063",
    "cvv": "010"
}
```

### Criar Produto
```json
POST /api/products
Authorization: Bearer {token}
{
    "name": "Notebook",
    "amount": 500000
}
```

### Alterar Prioridade do Gateway
```json
PATCH /api/gateways/1/priority
Authorization: Bearer {token}
{
    "priority": 2
}
```

---

## 👥 Roles de Usuário

| Role | Permissões |
|------|-----------|
| `ADMIN` | Acesso total |
| `MANAGER` | Gerenciar produtos e usuários |
| `FINANCE` | Gerenciar produtos e realizar reembolsos |
| `USER` | Acesso às rotas não listadas acima |

---

## 🏦 Multi-Gateway

O sistema possui dois gateways configurados com fallback automático:

- **Gateway 1** (`http://gateways-mock:3001`) — prioridade 1
- **Gateway 2** (`http://gateways-mock:3002`) — prioridade 2

Se o Gateway 1 falhar, o sistema automaticamente tenta o Gateway 2. Novos gateways podem ser adicionados implementando a interface `GatewayInterface`.

---

## 🧪 Rodando os Testes

```bash
docker exec betalent-app php artisan test
```

Ou com output detalhado:

```bash
docker exec betalent-app php artisan test --verbose
```

---

## 🗄️ Banco de Dados

Acesse o **Adminer** em `http://localhost:8080` com:

- **Servidor:** `db`
- **Usuário:** `root`
- **Senha:** `B3t@lEnt.`
- **Banco:** `betalent`

---

## ⚠️ Troubleshooting

**Erro de symlink no storage:**
```bash
docker exec betalent-app php artisan storage:link
```

**Limpar caches:**
```bash
docker exec betalent-app php artisan cache:clear
docker exec betalent-app php artisan config:clear
docker exec betalent-app php artisan route:clear
```

**Recriar banco do zero:**
```bash
docker exec betalent-app php artisan migrate:fresh --seed
```
