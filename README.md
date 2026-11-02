# PKAB

Purchase Request (PKAB) and marketlist management for Prisma Eco Digital: a Laravel 13 API with a
Vue 2 admin SPA (Vite), shipped as Docker images.

## Stack

| Layer | Version |
| --- | --- |
| PHP | 8.5.12 |
| Laravel | 13 |
| Database | MySQL 8.0 |
| Node.js | 26.12.1 |
| Frontend build | Vite 7 + `@vitejs/plugin-vue2` (Vue 2.7) |
| Web server | nginx 1.27 (behind Traefik in production) |

## Quick start (Docker)

```bash
make env                 # docker/.env.example -> docker/.env
make key                 # paste the printed value into APP_KEY
make dev-up
```

| URL | Service |
| --- | --- |
| http://localhost:8080 | Application |
| http://localhost:5173 | Vite dev server (HMR) |
| http://localhost:8081 | Adminer |
| 127.0.0.1:3307 | MySQL |

The first boot migrates the database. Seed it with `make artisan CMD="db:seed --force"` — the seeder
creates the user `admin` / `admin` (email `admin@admin.com`; the login field accepts either).

If any of those ports are taken, change `APP_PORT`, `VITE_PORT`, `ADMINER_PORT`, `DB_HOST_PORT` in
`docker/.env`, then update `APP_URL` and `SANCTUM_STATEFUL_DOMAINS` to match `APP_PORT`.

## Common commands

```bash
make help
make dev-up / dev-down / dev-logs
make sh
make artisan CMD="about"
make test
make fresh                  # migrate:fresh --seed (destroys data)
make prod-build && make prod-up
make prod-deploy            # pull the pinned images and roll out
```

## Documentation

- [docs/DOCKER.md](docs/DOCKER.md) — stack layout, development, Traefik, environment variables, troubleshooting
- [docs/PRODUCTION.md](docs/PRODUCTION.md) — build/push, first deployment, releases, rollback, backups
- [docs/UPGRADING_LARAVEL_PHP.md](docs/UPGRADING_LARAVEL_PHP.md) — the Laravel 9 → 13 / PHP 8.5 / Vite migration playbook

## Without Docker

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install && npm run build
php artisan migrate --seed
php artisan serve
```

PHP 8.5 and Node 26 are required (`composer.json` pins the platform to 8.5.12, `.nvmrc` to 26.12.1).
If the host PHP is older, `composer install` refuses to run — use the container instead:

```bash
docker compose --env-file docker/.env -f docker-compose.yml run --rm app composer install
```
