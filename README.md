<h1 align="center">
  🌴 Aloha App
</h1>

<p align="center">
  Plataforma B2B para pedidos de gelos saborizados
</p>

<p align="center">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white">
  <img alt="Tailwind CSS" src="https://img.shields.io/badge/Tailwind_CSS-4.0-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white">
  <img alt="Status" src="https://img.shields.io/badge/status-em%20desenvolvimento-yellow?style=for-the-badge">
</p>

---

## 📋 Sobre o Projeto

O **Aloha App** é uma plataforma B2B de gerenciamento e pedidos de gelos saborizados. O sistema conecta **clientes** (estabelecimentos que revendem os produtos), **administradores** e **vendedores**, com integração nativa à API do **Gestão Click** para sincronização de pedidos, produtos e estoque.

---

## ✨ Funcionalidades

### 👤 Clientes
- Cadastro e vinculação de lojas via CNPJ (validado pela API OpenCNPJ)
- Criação de pedidos com seleção de sabores 🥥🍓🥭🍉🍏🍑🍊🍋🐉
- Cálculo de preço em tempo real por faixas de quantidade
- Escolha de modalidade: **entrega** ou **retirada**
- Pagamento via **PIX**, **Boleto**, **Cartão** ou **Dinheiro**
- Acompanhamento de pedidos com paginação
- Painel financeiro e gestão de perfil

### 🛠️ Administradores
- Gerenciamento completo de clientes, lojas e vendedores
- Configuração de tabelas de preço com faixas por quantidade
- Configuração de dias e horários de entrega
- Painel administrativo com estatísticas gerais
- Sincronização de lojas com o GestaoClick

### 🔗 Integração GestaoClick
- Criação automática de pedidos no sistema externo
- Sincronização de lojas e produtos
- Mapeamento de métodos de pagamento

---

## 🚀 Tecnologias

| Camada | Tecnologia |
|--------|------------|
| Backend | [Laravel 12](https://laravel.com) + PHP 8.2+ |
| Frontend | [Tailwind CSS 4](https://tailwindcss.com)
| Autenticação e RBAC | [Spatie Laravel Permission 7](https://spatie.be/docs/laravel-permission) |
| Banco de Dados | MySQL |
| HTTP Client | Axios |
| Testes | PHPUnit 11 |
| API Externa | GestaoClick, OpenCNPJ, Google Maps |

---

## ⚙️ Pré-requisitos

- PHP **8.2** ou superior
- Composer **2.x**
- Node.js **18+** e NPM
- MySQL **8.0+**
- Credenciais da API **Gestão Click**

---

## 🛠️ Instalação

### Rodando com Docker Compose

Com Docker e Docker Compose instalados:

```bash
cp .env.example .env
docker compose up --build -d
```

A aplicação estará disponível em <http://localhost:8000>. O container executa as migrations automaticamente e os dados do MySQL ficam no volume `aloha_mysql`.

As migrations e os seeders são executados automaticamente na inicialização do
container. O usuário administrador padrão é:

```text
E-mail: admin@email.com
Senha: 123
```

Se precisar executar novamente manualmente:

```bash
docker compose exec app php artisan db:seed --force
```

Comandos úteis:

```bash
docker compose logs -f app
docker compose exec app php artisan migrate:status
docker compose down
```

Para apagar também os dados persistidos do banco, use `docker compose down -v`.

### Instalação local


## 🔧 Configuração do Ambiente

Copie `.env.example` para `.env` e preencha as variáveis necessárias:

```env
# Aplicação
APP_NAME="Aloha App"
APP_URL=http://localhost:8000
APP_LOCALE=pt_BR

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aloha_app
DB_USERNAME=root
DB_PASSWORD=

# GestaoClick API
GESTAOCLICK_URL=https://api.gestaoclick.com
GESTAOCLICK_ACCESS_TOKEN=seu_access_token
GESTAOCLICK_SECRET_TOKEN=seu_secret_token

# Google Maps
GOOGLE_MAPS_KEY=sua_chave_google_maps

# OpenCNPJ
OPEN_CNPJ_URL=https://publica.cnpj.ws/cnpj
```

### Serviços individuais

```bash
php artisan serve      # Servidor de desenvolvimento (http://localhost:8000)
```

---

## 📁 Estrutura do Projeto

```
aloha-app/
├── app/
│   ├── Console/        # Comandos Artisan customizados
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/  # Controllers do painel administrativo
│   │   │   ├── Auth/   # Registro e autenticação
│   │   │   └── Client/ # Controllers do portal do cliente
│   │   └── Middleware/ # Middlewares de autorização
│   ├── Models/         # Modelos Eloquent
│   ├── Services/       # Lógica de negócio e integrações (GestaoClick)
│   └── helpers.php     # Funções auxiliares globais
├── config/             # Arquivos de configuração do Laravel
├── database/
│   ├── migrations/     # Estrutura do banco de dados
│   ├── factories/      # Factories para testes
│   └── seeders/        # Dados iniciais
├── resources/
│   ├── views/
│   │   ├── admin/      # Templates do painel admin
│   │   └── client/     # Templates do portal cliente
│   └── css/ js/        # Assets do frontend
├── routes/
│   └── web.php         # Definição de todas as rotas
└── tests/
    ├── Feature/        # Testes de integração
    └── Unit/           # Testes unitários
```

---

## 🗃️ Comandos Úteis

```bash
# Sincronizar lojas com o GestaoClick
php artisan sync:gestao-click-stores

# Reiniciar banco de dados com seeders
php artisan migrate:fresh --seed

# Limpar caches
php artisan config:clear && php artisan cache:clear

# Acessar o REPL do Laravel
php artisan tinker
```

---

## 🤝 Contribuindo

Contribuições são bem-vindas! Siga os passos abaixo:

1. Faça um **fork** do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/minha-feature`)
3. Faça commit das suas alterações (`git commit -m 'feat: adiciona minha feature'`)
4. Envie para a branch (`git push origin feature/minha-feature`)
5. Abra um **Pull Request**

> Utilize [Conventional Commits](https://www.conventionalcommits.org/pt-br/) para as mensagens de commit.

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

<p align="center">
  Feito com ❤️ por <a href="https://github.com/AlisonSarto">Alison Sarto</a>
</p>
