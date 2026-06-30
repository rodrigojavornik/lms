# LMS Plataforma

Sistema de gestao de aprendizagem desenvolvido em Laravel 10. A aplicacao organiza cursos, aulas, provas, certificados, entidades, grupos e trilhas de aprendizagem para diferentes perfis de usuario.

## Principais recursos

- Area do aluno com matricula em cursos, progresso por aula, provas e certificados.
- Area do instrutor para criar cursos, aulas, imagens de conteudo, provas e relatorios de alunos.
- Area do gestor de entidade para convidar membros, criar grupos, montar trilhas e atribui-las a usuarios ou grupos.
- Area administrativa para gerenciar usuarios, entidades e compartilhamento de cursos entre entidades.
- Certificados com codigo publico de verificacao em `/certificates/verify/{code}`.
- Autenticacao baseada no Laravel Breeze.

## Perfis de acesso

O acesso por perfil e controlado por campos booleanos na tabela `users`:

- `is_admin`: acesso ao painel administrativo e tambem as areas de instrutor/gestor.
- `is_instructor`: acesso a gestao de cursos e aulas.
- `is_entity_manager`: acesso a gestao de entidade, grupos, trilhas e convites.
- Usuario sem flags: acesso de aluno/colaborador.

## Stack

- PHP 8.1+
- Laravel 10
- MySQL/MariaDB
- Laravel Sanctum
- Laravel Breeze
- Blade, Vite, Tailwind CSS e Alpine.js
- PHPUnit
- Docker para publicacao em producao

## Estrutura do projeto

- `app/Models`: modelos do dominio (`Course`, `Lesson`, `Quiz`, `Trail`, `Certificate`, `Entity`, `Group`, `User`).
- `app/Http/Controllers`: controllers das areas de aluno, instrutor, gestor, admin e certificados.
- `routes/web.php`: rotas web principais da aplicacao.
- `resources/views`: telas Blade separadas por area (`admin`, `manager`, `instructor`, `student`, `auth`).
- `database/migrations`: definicao completa do schema do banco.
- `database/seeders`: dados de demonstracao para ambiente local.
- `tests`: testes PHPUnit.
- `deploy`: exemplos de configuracao para producao.

## Instalacao local

1. Instale dependencias:

```bash
composer install
npm install
```

2. Crie o `.env` e gere a chave:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

3. Configure o banco no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms
DB_USERNAME=root
DB_PASSWORD=
```

4. Crie o schema com migrations:

```bash
php artisan migrate
php artisan storage:link
```

5. Para carregar dados de demonstracao local:

```bash
php artisan db:seed
```

## Dados de demonstracao

O seeder cria entidades, cursos, aulas, prova, grupos, trilha e usuarios de teste. Use apenas em desenvolvimento.

Credenciais principais:

- Admin: `admin@lms.com` / `password`
- Gestor A: `gestor_a@lms.com` / `password`
- Instrutor A: `instrutor_a@lms.com` / `password`
- Aluno A: `aluno_a@lms.com` / `password`

Em producao, nao rode seeders por padrao. O banco deve ser criado pelas migrations com `php artisan migrate --force`.

## Executar localmente

Em dois terminais:

```bash
php artisan serve
npm run dev
```

Acesse `http://127.0.0.1:8000`.

## Testes

```bash
php artisan test
```

Os testes usam PHPUnit e ficam em `tests/Feature` e `tests/Unit`.

## Producao com Docker

A publicacao de producao deve seguir o guia [PUBLICACAO_PRODUCAO.md](PUBLICACAO_PRODUCAO.md). O fluxo usa Docker Compose com containers para aplicacao PHP-FPM, Nginx e MySQL.

Resumo:

```bash
cp deploy/.env.docker.example .env
docker compose --env-file .env -f docker-compose.production.yml build
docker compose --env-file .env -f docker-compose.production.yml up -d
docker compose --env-file .env -f docker-compose.production.yml exec app php artisan migrate --force
```

As tabelas, indices e relacionamentos sao sempre gerados pelas migrations. O container MySQL cria apenas o database vazio.

## Comandos uteis

```bash
php artisan migrate:status
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
./vendor/bin/pint
```

## Observacoes de seguranca

- Nunca versionar `.env` ou senhas reais.
- Manter `APP_DEBUG=false` em producao.
- Usar HTTPS em producao e manter `SESSION_SECURE_COOKIE=true`.
- Fazer backup do banco antes de executar novas migrations em producao.
