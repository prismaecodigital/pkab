# Docker Deployment

> For the release and deployment runbook (build, push, update, roll back), see [PRODUCTION.md](PRODUCTION.md).
> For the version upgrade that introduced this stack, see [UPGRADING_LARAVEL_PHP.md](UPGRADING_LARAVEL_PHP.md).

The stack runs these services, isolated from anything else on the host:

| Service | Image | Role |
| --- | --- | --- |
| `app` | built from `Dockerfile` (`php:8.5.12-fpm-alpine`) | Laravel 13 / PHP-FPM |
| `nginx` | `nginx:1.27-alpine` (dev) or the built `web_prod` stage | Web server, serves `public/` and proxies PHP |
| `db` | `mysql:8.0` | Database |
| `adminer` | `adminer:4.8.1` | Database UI |
| `vite` | `node:26.12.1-alpine` | Vite dev server with HMR (development only) |
| `scheduler` | `pkab-app` | Runs `php artisan schedule:work` (production only) |

Compose files:

- `docker-compose.yml` — development (bind-mounted source, Vite HMR, published ports, project name `pkab-dev`)
- `docker-compose.prod.yml` — VM/production runtime only (no `build:` keys, no `env_file:`, no relative mounts, so stack managers accept it)
- `docker-compose.build.yml` — build overlay for the CLI; never loaded by a stack manager
- `docker-compose.local.yml` — publishes the production ports so it can be opened locally without Traefik

All commands below are wrapped by the `Makefile`, which passes `--env-file docker/.env`. Run `make help` for the full list.

Container names are fixed rather than Compose's default `<project>-<service>-<number>`: production runs `pkab-app`, `pkab-nginx`, `pkab-db`, `pkab-adminer` and `pkab-scheduler`, development adds a `pkab-dev-` prefix. Two consequences:

- `docker compose --scale app=2` is not possible for a service with a fixed name.
- A container name is unique per host, so the development stack must keep its prefix — otherwise it would collide with production when both run on the same machine.

The image names are separate from the container names: `prismaeco/pkab-app:prod` / `prismaeco/pkab-nginx:prod` are the images, `pkab-app` / `pkab-nginx` are the running containers.

## Prerequisites

- Docker Engine 24+ with the Compose v2 plugin
- An existing Traefik container on the VM with the Docker provider enabled (see below)
- Free host ports for the development stack: `8080`, `5173`, `8081`, `3307` (all overridable, see below)

## First-time setup

```bash
make env                 # copies docker/.env.example -> docker/.env
make key                 # prints a fresh APP_KEY value
```

Edit `docker/.env`:

- `APP_KEY` — paste the value printed by `make key`
- `APP_DOMAIN` — the hostname Traefik will serve, e.g. `pkab.example.com`
- `DB_PASSWORD`, `DB_ROOT_PASSWORD` — real passwords
- `WHACENTER_API_URL`, `WHACENTER_DEVICE_ID` — WhatsApp notifications (leave empty to disable)
- `MAIL_*` — `MAIL_MAILER=log` by default, so nothing leaves the machine until you point it at an SMTP server

The root `.env` file is **not** used inside the containers. Container environment variables take precedence over it (Laravel's Dotenv never overwrites existing variables), so a local MAMP setup (`DB_HOST=127.0.0.1`, `DB_PORT=8889`) stays untouched.

## Development

```bash
make dev-up
```

| URL | Service |
| --- | --- |
| http://localhost:8080 | Application (nginx → php-fpm) |
| http://localhost:5173 | Vite dev server (HMR) |
| http://localhost:8081 | Adminer (server `db`, user `pkab`) |
| 127.0.0.1:3307 | MySQL from a desktop client |

Ports are configurable — `APP_PORT`, `VITE_PORT`, `ADMINER_PORT`, `DB_HOST_PORT` in `docker/.env`. This matters when another project already listens on `3307`/`8081`/`5173`: change the value, run `make dev-down && make dev-up`, and update `APP_URL` **and** `SANCTUM_STATEFUL_DOMAINS` to match `APP_PORT`. The SPA talks to `/api/v1` with the session cookie, so a stateful domain that does not match the port makes every API call return `401`.

The stack was verified on `APP_PORT=8180`, `VITE_PORT=5273`, `ADMINER_PORT=8181`, `DB_HOST_PORT=3317` because those ports were already taken on the development machine — the defaults above are only defaults.

Notes:

- The source tree is bind-mounted, so PHP changes apply immediately.
- The entrypoint runs `php artisan migrate --force` on start (`RUN_MIGRATIONS=true` in the development compose file), so a fresh volume comes up with the schema. Seeding is explicit: `make fresh` (destroys data) or `make artisan CMD="db:seed --force"`.
- The default seeder creates one user: username `admin`, email `admin@admin.com`, password `admin`. The login form accepts either the username or the email in its single field. Change it anywhere real.
- Vite writes `public/hot`, which is how Blade knows to load assets from the dev server. The container listens on `VITE_PORT` itself (`vite.config.js` reads `VITE_PORT`), so the published port and the URL in `public/hot` always agree.
- If the browser cannot reach HMR, set `VITE_HMR_HOST` in `docker/.env` to the hostname/IP you open the app with, then `make dev-down && make dev-up`.
- `vendor/` comes from the bind mount; if it is missing the entrypoint runs `composer install`.
- `node_modules` lives in a named volume (`pkab-dev_node_modules`), so the host `node_modules` is untouched. After changing `package.json`, refresh it:
  ```bash
  docker compose --env-file docker/.env -f docker-compose.yml up -d --renew-anon-volumes --force-recreate vite
  ```
- `public/storage` is linked by the entrypoint (`php artisan storage:link`) on first boot.
- The development project is named `pkab-dev` and uses the network `pkab-dev-internal`, so it can run next to the production stack (project `pkab`, network `pkab-internal`) without sharing containers, networks or volumes.
- MySQL imports files placed in `docker/mysql/dump/` on the **first** boot of an empty volume (`docker/mysql/init/00-import-dump.sh`). The dump must not contain `CREATE DATABASE`/`USE` statements that conflict with `MYSQL_DATABASE`.

Useful commands:

```bash
make dev-logs            # tail logs
make sh                  # shell inside the app container
make artisan CMD="about"
make test                # php artisan test against the pkab_testing database
make fresh               # migrate:fresh --seed
make db-import FILE=dump.sql
make dev-down
```

`make test` creates the `pkab_testing` database, grants the application user access, and points the suite at it (`APP_ENV=testing`, `DB_DATABASE=pkab_testing`). `.env.testing` supplies the non-secret test defaults. **Never** run `php artisan test` against the development database without those overrides: `phpunit.xml` does not pin the connection, so the suite would use `DB_DATABASE` from the container environment.

### Tests

The suite is small: `tests/Unit/ExampleTest` and two feature tests that assert the root redirect and that `/login` renders. `make test` currently reports `3 passed`.

`tests/Feature/ExampleTest` used to assert `200` for `/`, which was never true — the root redirects to `/login`. It now asserts the redirect and adds the login-page check.

### Known limitations

- The theme used to reference `/md/img/login.jpg`, `/md/img/register.jpg`, `/md/img/vue.png` and `/md/img/apple-icon.png`; none of those files were ever committed (only `download_icon.png` exists). The login/register backgrounds now use their CSS gradient only, and every layout uses the existing `/favicon.ico`, so both pages load with no `404`s.
- `resources/adminapp/js/layouts/DefaultLayout.vue` is dead code: it imports `@/components/AppHeader.vue`, `AppFooter.vue` and `AppSidebar.vue`, which do not exist. Nothing imports the file, so the Vite build ignores it — do not resurrect it without fixing those paths.
- `public/adminapp/` and the `adminapp` file in the repository root are the old laravel-mix/webpack output. Nothing references them any more; they are excluded from the image. The build now emits `public/build/`.
- The SPA is Vue 2.7 with `@vitejs/plugin-vue2`. That plugin supports Vite 3–7, which is why `vite` is pinned to `^7.3.7` instead of Vite 8. It also forces the runtime-only Vue build, so components must not use inline `template:` strings — `routes.js` uses `render: h => h('router-view')` for the nested-route wrapper.
- **Seeding gaps (pre-existing):** `DatabaseSeeder` does not call `PermissionMarketlistTableSeeder`, so a fresh `db:seed` has no `marketlist_*` / `marketlistItem_*` permissions and every marketlist/category API returns `403`. Run it explicitly after seeding (it also grants the permissions to the admin and purchasing roles):
  ```bash
  make artisan CMD="db:seed --class=PermissionMarketlistTableSeeder --force"
  ```
  `status_history_access` is not seeded anywhere, so `/api/v1/status-histories` is always `403`; the SPA has no route for it.
- The marketlist list is scoped to the user's `bu_user` pivot. The seeded admin has no pivot row, so the list is empty until one is added.
- `GET /api/v1/pkab-items/export` is shadowed by `Route::resource('pkab-items')` and the controller method is a stub — it returns `404` and does nothing.
- `GET /api/v1/update-user-survey` still contains a `dd($user)`, so it returns `500` if called.

### Opening the production stack locally (Adminer without Traefik)

`docker-compose.prod.yml` publishes no ports for nginx, so it can only be reached through Traefik. To inspect it from the host, add the local override:

```bash
APP_PORT=8090 DB_HOST_PORT=3308 ADMINER_PORT=8091 \
APP_URL=http://localhost:8090 \
SANCTUM_STATEFUL_DOMAINS=localhost:8090,127.0.0.1:8090 \
RUN_MIGRATIONS=true \
make prod-local
```

| URL | Service |
| --- | --- |
| http://localhost:8090 | Application (built assets, cached config) |
| http://localhost:8091 | Adminer — server `db`, credentials from `docker/.env` |
| 127.0.0.1:3308 | MySQL from a desktop client |

The environment overrides are required: `APP_URL` must match the published port and `SANCTUM_STATEFUL_DOMAINS` must list it, otherwise the SPA logs in but its API calls return `401`. `RUN_MIGRATIONS=true` is only needed for the first boot of an empty `pkab_mysql-data` volume.

Never load `docker-compose.local.yml` on the VM: there Traefik is the only entry point.

## Production on the VM

### 1. Confirm the Traefik setup

This stack assumes Traefik runs with `network_mode: host`, the Docker provider enabled, and TLS configured:

```yaml
services:
  traefik:
    network_mode: host
    command:
      - --providers.docker=true
      - --providers.docker.exposedbydefault=false
      - --entrypoints.web.address=:80
      - --entrypoints.websecure.address=:443
      - --certificatesresolvers.letsencrypt.acme.httpchallenge=true
      - --certificatesresolvers.letsencrypt.acme.httpchallenge.entrypoint=web
      - --certificatesresolvers.letsencrypt.acme.storage=/letsencrypt/acme.json
      - --entrypoints.web.http.redirections.entrypoint.to=websecure
      - --entrypoints.web.http.redirections.entrypoint.scheme=https
```

Because Traefik is on the host network it does **not** join a Docker network; it reaches containers through their bridge IP. That is why the labels pin `traefik.docker.network` to the app's own network (`APP_NETWORK`, default `pkab-internal`).

If your entrypoints or resolver use different names, set `TRAEFIK_ENTRYPOINT` / `TRAEFIK_CERT_RESOLVER` in `docker/.env`. The HTTP→HTTPS redirect is handled globally by Traefik, so the compose file declares only the `websecure` router.

If your Traefik runs in bridge mode on a shared network instead, declare that network as `external: true` and point `APP_NETWORK` at it.

`APP_URL` must be set to the public https URL (it is not derived from `APP_DOMAIN`), so links, redirects and asset URLs are correct behind TLS. `bootstrap/app.php` calls `trustProxies(at: '*')`, so `X-Forwarded-Proto` from Traefik is honoured.

### 2. Import the existing database

Option A (development only) — first boot with an empty volume: copy the dump into `docker/mysql/dump/` before starting. Files ending in `.sql` and `.sql.gz` are imported automatically. The production compose file has no bind mounts, so this folder is not used there — use `make db-import` or Adminer instead.

Option B — import into a running stack:

```bash
make prod-up
make db-import FILE=/path/to/dump.sql.gz STACK=prod
```

Option C — import into an existing volume, then skip migrations:

```bash
docker compose --env-file docker/.env -f docker-compose.prod.yml exec -T db \
  sh -c 'exec mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' < dump.sql
```

A dump that already contains the schema means you should **not** run `migrate --force` (keep `RUN_MIGRATIONS=false`). For an empty database, either set `RUN_MIGRATIONS=true` or run migrations explicitly:

```bash
make prod-migrate
```

> **Dump vs. migrations drift.** A dump taken from a database that was migrated by hand can be missing `migrations` rows for changes whose columns already exist. Check before running anything:
>
> ```bash
> make prod-artisan CMD="migrate:status"
> make prod-artisan CMD="migrate --pretend --force"
> ```
>
> If the schema already matches, insert the missing names into `migrations` with a new `batch` instead of running them — otherwise `migrate` fails with `Duplicate column name`. This was the case for the `u1591907_pkab` dump: six migrations were missing from its table while their columns/indexes were present.

> **Schema note:** `2026_11_01_000000_add_site_foreign_key_to_marketlist_order_table` only adds a constraint that is missing. A dump taken before this upgrade already has the `marketlist_order.site_id` foreign key and the migration skips it. A dump taken from a database created before the upgrade may not have it at all — the migration adds it, and fails if orphaned `site_id` values exist. Check first:
>
> ```sql
> SELECT COUNT(*) FROM marketlist_order mo
> LEFT JOIN site s ON s.id = mo.site_id
> WHERE mo.site_id IS NOT NULL AND s.id IS NULL;
> ```
>
> Fix orphaned rows (`UPDATE marketlist_order SET site_id = NULL ...`) before running it.

### 3. Build and start

```bash
make prod-build      # local, native-architecture build of both images
make prod-up
```

The production image runs `npm run build` and `composer install --no-dev` at build time, then caches config, routes, events and views on container start (`CACHE_CONFIG=true` → `php artisan optimize`). The entrypoint runs `php artisan optimize:clear` before caching, because compiled Blade views live in the persistent `storage` volume and Docker preserves source mtimes — without the clear, views compiled by a previous release can be considered fresh forever. No source is mounted and no ports are published — only Traefik reaches nginx.

Route caching works even though `routes/web.php` registers closure routes: Laravel 11+ serialises closures with `laravel/serializable-closure`. If a route cache ever fails, the cause is a duplicate route name, not the closures.

### 4. Adminer over SSH

Adminer is intentionally **not** exposed to the internet. In `docker-compose.prod.yml` it has no Traefik labels and is bound to the VM's loopback only:

```yaml
ports:
  - "127.0.0.1:${ADMINER_PORT}:8080"
```

That means no public hostname, no firewall rule, and nothing reachable from outside — the only way in is an SSH tunnel.

**1. Verify the binding on the VM**

```bash
docker port pkab-adminer
# 8080/tcp -> 127.0.0.1:8081

ss -tlnp | grep 8081
```

If it shows `0.0.0.0:8081` instead, the mapping is public — change it back to `127.0.0.1:8081`.

**2. Open the tunnel from your machine**

```bash
ssh -N -L 8081:127.0.0.1:8081 <user>@<vm-ip>
```

**3. Browse to http://localhost:8081**

| Field | Value |
| --- | --- |
| System | MySQL |
| Server | `db` |
| Username | `DB_USERNAME` from `docker/.env` |
| Password | `DB_PASSWORD` from `docker/.env` |
| Database | `DB_DATABASE` from `docker/.env` |

Close the tunnel with `Ctrl+C` (or `pkill -f "ssh -N -L 8081"`).

## Multi-architecture builds

All base images (`php:8.5.12-fpm-alpine`, `node:26.12.1-alpine`, `nginx:1.27-alpine`, `composer:2.11.1`, `mysql:8.0`, `adminer:4.8.1`) publish both `linux/amd64` and `linux/arm64` manifests, and the Dockerfile pins no platform. Composer is pinned by `ARG COMPOSER_VERSION=2.11.1` so a new Composer release cannot change lockfile handling mid-release.

The compose file does **not** pin a platform either: `make prod-build` produces images for the machine that runs the build. Building on the VM is therefore always correct and never emulated.

If you build somewhere else (an Apple Silicon laptop, CI), publish a multi-arch image instead of copying the local image, using the release flow described in [PRODUCTION.md](PRODUCTION.md):

```bash
make builder
make release VERSION=2026-11-01.1
# or a single platform:
make release VERSION=2026-11-01.1 PLATFORMS=linux/amd64
```

Docker cannot keep a multi-arch manifest in the local image store, so `make release` always pushes — log in first (`docker login`).

On the VM, deploy the published images instead of building:

```dotenv
APP_IMAGE=prismaeco/pkab-app:2026-11-01.1
NGINX_IMAGE=prismaeco/pkab-nginx:2026-11-01.1
```

```bash
make prod-deploy
```

Verify what a tag actually contains:

```bash
docker buildx imagetools inspect prismaeco/pkab-app:2026-11-01.1
```

The Vite assets **and** Composer `vendor` stages are pinned to `$BUILDPLATFORM`, so `npm ci`/`npm run build` and `composer install` always run natively and are shared by every target architecture. Only the PHP stage (`apk` + `docker-php-ext-install`) is emulated during a cross-build.

## Scheduler and queues

`docker-compose.prod.yml` runs a `scheduler` service (`php artisan schedule:work`). `routes/console.php` currently defines no scheduled tasks — the only console entry is the stock `inspire` command. Add tasks there with the `Schedule` facade:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('whacenter:sync')->dailyAt('06:00');
```

`QUEUE_CONNECTION` defaults to `sync`, so queued jobs run inside the request. `App\Notifications\WhacenterNotification` implements `ShouldQueue`, so WhatsApp notifications are already queued work. To move them to the database queue (the `jobs` table exists):

```dotenv
QUEUE_CONNECTION=database
```

then run a worker next to the stack:

```bash
docker compose --env-file docker/.env -f docker-compose.prod.yml run -d --name pkab-queue \
  app php artisan queue:work --sleep=3 --tries=3
```

## Environment variables

The defaults below belong to the **development** compose file. `docker-compose.prod.yml` declares no fallbacks: every `${VAR}` it references must be set (see [PRODUCTION.md](PRODUCTION.md#4-first-deployment-on-the-vm)), because Compose silently substitutes an empty string for a missing variable.

| Variable | Default | Purpose |
| --- | --- | --- |
| `APP_NAME` | `PKAB` | Session cookie / cache prefix and mail sender name |
| `APP_TIMEZONE` | `Asia/Jakarta` | App timezone; also the container `TZ`, which MySQL follows |
| `APP_KEY` | — | Laravel encryption key (required) |
| `APP_URL` | `http://localhost:8080` (dev) | Base URL — required in production, must include the scheme |
| `APP_DOMAIN` | `pkab.example.com` | Traefik host rule |
| `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | `pkab` / `pkab` / `secret` | Application DB credentials |
| `DB_ROOT_PASSWORD` | `root-secret` | MySQL root password |
| `MYSQL_VERSION` | `8.0` | Database image tag |
| `APP_PORT` / `VITE_PORT` / `ADMINER_PORT` / `DB_HOST_PORT` | `8080` / `5173` / `8081` / `3307` | Development host ports |
| `APP_NETWORK` | `pkab-internal` | Bridge network Traefik uses to reach nginx |
| `TRAEFIK_ENTRYPOINT` | `websecure` | TLS entrypoint |
| `TRAEFIK_CERT_RESOLVER` | `letsencrypt` | Traefik cert resolver name |
| `APP_IMAGE` / `NGINX_IMAGE` | `prismaeco/pkab-app:prod` / `prismaeco/pkab-nginx:prod` | Override to pull pre-built images from a registry |
| `RUN_MIGRATIONS` | `true` (dev) / `false` (prod) | Run `migrate --force` on start |
| `CACHE_CONFIG` | `true` (prod) | Cache config/routes/views on start |
| `CACHE_STORE` / `SESSION_DRIVER` | `file` / `file` | Cache and session stores |
| `QUEUE_CONNECTION` | `sync` | `sync` or `database` (needs a worker) |
| `BROADCAST_CONNECTION` | `log` | Broadcast driver |
| `SESSION_SECURE_COOKIE` | `false` | Set `true` behind TLS |
| `MAIL_*` | `MAIL_MAILER=log` | Mail transport |
| `WHACENTER_API_URL` / `WHACENTER_DEVICE_ID` | — | WhatsApp notifications (Whacenter) |
| `SANCTUM_STATEFUL_DOMAINS` | `localhost:8080,127.0.0.1:8080` | SPA cookie domains — must match the port you browse |
| `VITE_HMR_HOST` | `localhost` | Host used by the Vite HMR client |
| `VITE_VUE_APP_I18N_LOCALE` / `VITE_VUE_APP_I18N_FALLBACK_LOCALE` | `id` / `id` | Locale baked into the bundle at build time |

`WHACENTER_API_URL` and `WHACENTER_DEVICE_ID` reach `App\Services\WhacenterService` through `config('services.whacenter.*')`, so they keep working with `config:cache` active in production. The values still come from the container environment (compose passes them). Leaving them empty disables WhatsApp notifications — the service builds requests against an empty base URL and the HTTP call fails, it does not crash on a `TypeError` any more.

## Timezones

One knob, applied to three layers so the offset is never applied twice:

| Layer | Source |
| --- | --- |
| Laravel (`config('app.timezone')`, Carbon, `created_at` serialization) | `APP_TIMEZONE` |
| Container clock, log timestamps, `php artisan schedule:*` | `TZ` (set from `APP_TIMEZONE`) |
| MySQL session (`@@system_time_zone`, `NOW()`) | inherits the container `TZ` — `SYSTEM`, no `default-time-zone` needed |

All three must agree. When only one is shifted you get a silent double conversion: PHP writes Jakarta wall-clock time, MySQL stores it as if it were UTC, and the browser (also Jakarta) adds another +7 — timestamps end up 14 hours ahead of UTC.

Check that all three agree:

```bash
docker compose --env-file docker/.env -f docker-compose.yml exec app \
  php artisan tinker --execute="echo config('app.timezone'), ' ', now(), ' UTC: ', now()->utc(), PHP_EOL;"

docker compose --env-file docker/.env -f docker-compose.yml exec db \
  sh -c 'date; mysql -u root -p"$MYSQL_ROOT_PASSWORD" -N -e "SELECT @@system_time_zone, NOW(), UTC_TIMESTAMP();"'
```

`NOW()` must be `UTC_TIMESTAMP()` + 7 hours, and the app's `now()` must match `NOW()`. On the verified stack: app `21:48 WIB`, DB `WIB 21:48`, UTC `14:48`.

Existing rows are not rewritten: switching timezones changes how `TIMESTAMP` values are interpreted from then on.

## Operations

```bash
make prod-logs                                   # logs
make prod-shell                                  # shell as www-data
make prod-artisan CMD="about"                    # any artisan command
make backup                                      # backup-YYYY-MM-DD.sql

# Database backup by hand
docker compose --env-file docker/.env -f docker-compose.prod.yml exec -T db \
  sh -c 'exec mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' > backup.sql
```

`docker compose exec` runs as `root`; the `Makefile` passes `-u www-data` for the production shell and artisan targets so files in `storage/` stay writable by PHP-FPM. Use the same flag when running commands by hand.

Changing any value in `docker/.env` requires recreating the containers:

```bash
make prod-down && make prod-up
```

because config caching happens at container start.

## Troubleshooting

**502 Bad Gateway** — PHP-FPM is not up yet or crashed. Check `docker compose logs app`. The entrypoint refuses to start in production when `APP_KEY` is empty.

**500 after first boot / permission denied** — fix storage ownership:

```bash
docker compose --env-file docker/.env -f docker-compose.prod.yml exec -u root app \
  chown -R www-data:www-data storage bootstrap/cache
```

**Assets 404 after a rebuild (`/build/assets/app-XXXX.js`)** — `docker restart` keeps the old image. Recreate the containers so nginx serves the new `public/build` that matches the manifest in the app image:

```bash
make prod-build
make prod-up   # up -d --no-build --force-recreate is implicit when the image changed
```

**502 after recreating only the `app` container** — nginx resolved `app:9000` to the old container IP at start-up and keeps using it. Restart nginx after recreating `app` on its own:

```bash
docker restart pkab-nginx
```

**Assets 404 in production** — the nginx image copies `public/` at build time. Rebuild after changing frontend code: `make prod-build && make prod-up`.

**Login succeeds but every API call returns 401** — `SANCTUM_STATEFUL_DOMAINS` does not contain the host and port you are browsing. It is a literal list: `localhost:8080,127.0.0.1:8080`. Change it, then recreate the containers.

**Asset URLs contain the host twice** (`https://host/host/build/assets/…`, and CSS is rejected with `MIME type ('text/html')`) — `APP_URL` is missing its scheme. A schemeless value makes `asset()` emit `host/build/...`, which the browser resolves relative to the current page, so the request hits a path that does not exist. Set `APP_URL=https://host`; the entrypoint refuses to start in production otherwise. Do not set `ASSET_URL` to the app URL — leave it unset so assets stay root-relative and immune to this.

**Mixed content / http URLs behind Traefik** — `bootstrap/app.php` trusts `X-Forwarded-*`, so verify Traefik forwards the proto and that `APP_URL` is the public https URL and `APP_DOMAIN` the matching hostname.

**Traefik returns 404 for the domain** — `traefik.docker.network` must match the network the nginx container is actually attached to (`APP_NETWORK`, default `pkab-internal`). Check the labels and the container's networks:

```bash
docker inspect pkab-nginx --format '{{json .Config.Labels}}' | tr ',' '\n' | grep traefik
docker inspect pkab-nginx --format '{{range $k,$v := .NetworkSettings.Networks}}{{$k}} {{end}}'
```

**`Unable to obtain ACME certificate` — DNS points somewhere else** — HTTP-01 validation goes wherever DNS says, not to your VM. Check first:

```bash
dig +short pkab.example.com          # must be the VM's public IP
```

A subdomain that is still on the hosting provider's shared IP answers with a parking page and a `302` to the provider's site. Let's Encrypt follows that redirect, and the error then names the *redirect target's* address (often a Cloudflare edge, e.g. `2606:4700::…`) with `Error reading HTTP response body: reader size limit exceeded` — which looks like a Cloudflare problem but is not. Confirm by hand:

```bash
curl -sI http://pkab.example.com/.well-known/acme-challenge/probe | head -5
# Server: LiteSpeed + Location: https://hostinger.com  -> wrong server, the record is mispointed
```

Fix the `A` record (and delete any `AAAA`), make sure the hostname is not also registered as an addon/parked domain on the shared plan, then restart Traefik so it retries immediately:

```bash
docker restart traefik-1 && docker logs -f traefik-1 | grep -i acme
```

The same probe must then be answered by Traefik itself (`404`), never by a redirect to the hosting provider. Domains already working behind the same Traefik are the reference: compare `dig +short` for one of them against the failing hostname.

**Certificate issued, but only with Cloudflare in "DNS only"** — if the zone is later proxied (orange cloud), HTTP-01 breaks: the challenge is answered by Cloudflare, not Traefik. Either keep the record grey while a certificate is issued and refresh it before expiry, or switch the resolver to DNS-01 with a Cloudflare API token. With the proxy on, Cloudflare's SSL/TLS mode must be *Full (strict)* and the origin must present a valid certificate.

**MySQL dump fails to import** — check the server version of the source. Dumps from MySQL 5.7 usually load into 8.0; if not, change `MYSQL_VERSION` to `5.7` in `docker/.env` and re-import into a fresh volume.

**`exec format error` or `no matching manifest for linux/amd64`** — the image was built for a different CPU architecture than the VM. Build on the VM itself, or publish a multi-arch image and pull it (see [Multi-architecture builds](#multi-architecture-builds)).

**`port is already allocated`** — another stack owns `8080`/`8081`/`5173`/`3307`. Change `APP_PORT`/`ADMINER_PORT`/`VITE_PORT`/`DB_HOST_PORT` in `docker/.env` and update `APP_URL` and `SANCTUM_STATEFUL_DOMAINS`.

**The build dies with `input/output error`, `blob sha256:… expected at …: input/output error`, `Cannot open phar archive "/usr/bin/composer"` or `listing workers: … EOF`** — the Docker host is out of disk space. Those errors look like image or BuildKit corruption, but they are read/write failures: check `df -h` first (macOS: `/System/Volumes/Data`) and `docker system df`. Free space with `docker builder prune -a -f` and `docker image prune -a -f`, restart Docker Desktop so it compacts `Docker.raw`, then recreate the crashed builder (`docker buildx rm multiarch && make builder`) — a builder that lost its worker stays in `error` state even after space is freed.

**`opcache` fails to build (`cp: can't stat 'modules/*'`)** — `php:8.5.12` ships Zend OPcache compiled in. Do not list `opcache` in `docker-php-ext-install`; configure it only through `docker/php/opcache*.ini`.

**Tests print `file_get_contents(/var/www/html/.env): Failed to open stream`** — the image has no `.env` (by design). `.env.testing` supplies the test defaults, and `make test` passes `APP_ENV=testing` so Laravel loads it instead of `.env`. Running `php artisan test` by hand without `-e APP_ENV=testing` brings the warning back.
