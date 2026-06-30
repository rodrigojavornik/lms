# Publicacao em Producao com Docker

Este guia descreve a publicacao do LMS Laravel 10 usando Docker. No servidor devem existir apenas Docker, Docker Compose, Git e um proxy HTTPS em Docker, como Nginx Proxy Manager ou Traefik. PHP-FPM, Nginx da aplicacao, MySQL, Composer e Node rodam em containers ou durante o build das imagens.

Importante: o banco de dados da aplicacao e criado pelas migrations Laravel. O MySQL do Compose cria apenas o database vazio definido em `DB_DATABASE`; tabelas, indices e relacionamentos sao gerados por `php artisan migrate --force`.

## 1. Preparar o servidor

1. Aponte o DNS do dominio para o IP do servidor, por exemplo `lms.suaempresa.com`.
2. Instale Docker e Compose:

```bash
sudo apt update
curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker $USER
```

3. Saia e entre novamente no SSH.
4. Confirme a instalacao:

```bash
docker --version
docker compose version
```

## 2. Baixar o projeto

```bash
sudo mkdir -p /var/www
cd /var/www
git clone <url-do-repositorio> lms
cd /var/www/lms
```

Arquivos usados no deploy:

- `Dockerfile`: monta as imagens da aplicacao PHP-FPM e do Nginx.
- `docker-compose.production.yml`: define os servicos `app`, `web` e `db`.
- `deploy/nginx-docker.conf`: configura o Nginx interno.
- `deploy/.env.docker.example`: modelo de variaveis de producao.

## 3. Configurar o `.env`

Crie o arquivo de ambiente:

```bash
cp deploy/.env.docker.example .env
nano .env
```

Ajuste os valores principais:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://lms.suaempresa.com

DB_HOST=db
DB_DATABASE=lms
DB_USERNAME=lms_user
DB_PASSWORD=senha-forte
DB_ROOT_PASSWORD=senha-root-forte

MAIL_HOST=smtp.exemplo.com
MAIL_USERNAME=usuario
MAIL_PASSWORD=senha
WEB_PORT=8080
```

Nao altere `DB_HOST=db`, pois esse e o nome do servico MySQL dentro da rede Docker. Use senhas fortes e mantenha o arquivo `.env` fora do Git.

## 4. Gerar `APP_KEY`

Construa a imagem e gere a chave:

```bash
docker compose --env-file .env -f docker-compose.production.yml build
docker compose --env-file .env -f docker-compose.production.yml run --rm --no-deps app php artisan key:generate --show
```

Copie o valor exibido para `APP_KEY=` no `.env`.

## 5. Subir os containers

```bash
docker compose --env-file .env -f docker-compose.production.yml up -d
docker compose --env-file .env -f docker-compose.production.yml ps
```

O container `web` publica a porta definida em `WEB_PORT`, por padrao `8080`. O banco fica no volume Docker `lms_db`; nao apague esse volume em producao.

## 6. Criar o schema com migrations

Depois que os containers estiverem ativos, execute:

```bash
docker compose --env-file .env -f docker-compose.production.yml exec app php artisan migrate --force
```

Esse comando cria todo o schema do sistema a partir dos arquivos em `database/migrations`. Nao crie tabelas manualmente no MySQL e nao use `migrate:fresh` em producao, pois isso apaga os dados.

Se precisar conferir o estado:

```bash
docker compose --env-file .env -f docker-compose.production.yml exec app php artisan migrate:status
```

## 7. Otimizar Laravel

Rode os caches apos as migrations:

```bash
docker compose --env-file .env -f docker-compose.production.yml exec app php artisan optimize:clear
docker compose --env-file .env -f docker-compose.production.yml exec app php artisan config:cache
docker compose --env-file .env -f docker-compose.production.yml exec app php artisan route:cache
docker compose --env-file .env -f docker-compose.production.yml exec app php artisan view:cache
```

Nao rode seeders no deploy padrao. Use `db:seed --force` somente se houver uma decisao explicita de carregar dados iniciais controlados.

## 8. Configurar HTTPS

Use um proxy HTTPS em Docker na frente da aplicacao.

Exemplo com Nginx Proxy Manager:

1. Crie um Proxy Host para `lms.suaempresa.com`.
2. Aponte para `http://<ip-do-servidor>:8080`, ou para a porta configurada em `WEB_PORT`.
3. Ative SSL com Let's Encrypt.
4. Force HTTPS.

Depois confirme que `APP_URL` usa `https://`. Com HTTPS ativo, mantenha `SESSION_SECURE_COOKIE=true`.

## 9. Validar a publicacao

1. Acesse `https://lms.suaempresa.com`.
2. Teste login, cadastro/convite e dashboards de admin, gestor, instrutor e aluno.
3. Confirme se arquivos publicos de `storage` carregam corretamente.
4. Verifique logs se houver erro:

```bash
docker compose --env-file .env -f docker-compose.production.yml logs -f app
docker compose --env-file .env -f docker-compose.production.yml logs -f web
docker compose --env-file .env -f docker-compose.production.yml logs -f db
```

## 10. Atualizar uma producao existente

Antes de atualizar, faca backup do banco:

```bash
docker compose --env-file .env -f docker-compose.production.yml exec db sh -c 'mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' > backup-lms.sql
```

Publique a nova versao:

```bash
cd /var/www/lms
git pull
docker compose --env-file .env -f docker-compose.production.yml build --pull
docker compose --env-file .env -f docker-compose.production.yml exec app php artisan down
docker compose --env-file .env -f docker-compose.production.yml up -d
docker compose --env-file .env -f docker-compose.production.yml exec app php artisan migrate --force
docker compose --env-file .env -f docker-compose.production.yml exec app php artisan optimize:clear
docker compose --env-file .env -f docker-compose.production.yml exec app php artisan config:cache
docker compose --env-file .env -f docker-compose.production.yml exec app php artisan route:cache
docker compose --env-file .env -f docker-compose.production.yml exec app php artisan view:cache
docker compose --env-file .env -f docker-compose.production.yml exec app php artisan up
```

As migrations novas alteram o schema preservando os dados existentes. Se uma migration for destrutiva, revise e aprove antes de executar em producao.

## 11. Restaurar backup

Para restaurar um backup SQL em uma instalacao vazia:

```bash
docker compose --env-file .env -f docker-compose.production.yml exec -T db sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' < backup-lms.sql
```

Depois rode `php artisan migrate --force` para aplicar migrations que ainda nao existiam no backup.
