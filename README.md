# Gestor de Licitações Públicas

Sistema completo para gerenciamento de licitações públicas, desenvolvido com **Laravel** (back-end) e **Vue.js 3 + Vite** (front-end). Permite o controle, visualização, filtro e ordenação de processos licitatórios.

## Funcionalidades

-   Listagem completa de licitações
-   Filtro global e por período de datas
-   Ordenação crescente/decrescente por qualquer coluna
-   Cadastro de nova licitação
-   Edição de licitação existente
-   Exclusão com confirmação
-   Paginação automática
-   Integração com entidades relacionadas: Modalidade, Prioridade, Fase, Licitador

## Tecnologias Utilizadas

### Front-End

-   [Vue 3](https://vuejs.org/)
-   [Vite](https://vitejs.dev/)
-   [Bootstrap 5](https://getbootstrap.com/)
-   Axios para chamadas HTTP

### Back-End

-   [Laravel 10+](https://laravel.com/)
-   Eloquent ORM
-   PostgreSQL (pode ser adaptado para outros bancos)
-   API RESTful com autenticação opcional

## Instalação

### 1. Clone o repositório

'''bash
git clone https://github.com/Midylena/licitacoes
cd licitacoes

### 2. Backend (Laravel)

cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve

Configure o banco de dados no .env.

### 3. Frontend (Vue + Vite)

cd frontend
npm install
npm run dev

Acesse: http://localhost:5173

## Estrutura

resources/
├── js/
│ ├── components/
│ │ └── LicList.vue # Componente principal da listagem
│ ├── router/
│ └── app.js # Configuração da aplicação Vue

## Autor

Desenvolvido por [Milena Alves]
Email: milenape.alves@email.com
GitHub: @Midylena
