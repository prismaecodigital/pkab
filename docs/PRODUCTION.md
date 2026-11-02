# Production Guide

Release and deployment runbook for the PKAB stack: build the images, push them to a registry, and roll them out on the VM behind Traefik.

For local development, Traefik wiring and environment variable details, see [DOCKER.md](DOCKER.md).

## 1. Images and versioning

| Image | Dockerfile target | Contains |
| --- | --- | --- |
| `prismaeco/pkab-app` | `prod` | PHP 8.5.12-FPM, application code, `vendor/`, built `public/build`, OPcache |
| `prismaeco/pkab-nginx` | `web_prod` | nginx + the built `public/` directory |

Both images come from the single root `Dockerfile`. `--target prod` builds the application image, `--target web_prod` builds the web image on top of it, so the nginx image always carries exactly the assets from the same commit.

**Version format:** `YYYY-MM-DD.N`, for example `2026-11-01.1`. Increment `N` for every release on the same day (`2026-11-01.2`, `2026-11-01.3`, …). Every version is immutable — never push over an existing tag.

`latest` is published alongside each version for convenience, but **production must pin an explicit version** in `docker/.env` so rollbacks and restarts are deterministic.

### Application name and locale surfaces

There is no single source of truth for the name, and this application does not have a version badge:

| Surface | Source | When it takes effect |
| --- | --- | --- |
| Blade `<title>` and the sidebar brand | `resources/lang/{en,id}/panel.php` → `panel.site_title` (`PKAB`) | image rebuild |
| Session cookie name, cache prefix, mail sender name | `APP_NAME` container env (compose default `PKAB`) | container recreate |
| UI language | `VITE_VUE_APP_I18N_LOCALE` / `VITE_VUE_APP_I18N_FALLBACK_LOCALE`, baked into the JS bundle | image rebuild |
| Server locale | `SetPreferredLocale` middleware + `config/project.php` (`id` only) | image rebuild |

`VITE_VUE_APP_I18N_LOCALE` and `VITE_VUE_APP_I18N_FALLBACK_LOCALE` are build args on the `assets` stage, defaulting to `id`:

```bash
docker buildx build --build-arg VITE_VUE_APP_I18N_LOCALE=id ...
```

`docker-compose.build.yml` passes both from `docker/.env`.

Because the browser title lives in a language file, changing `APP_NAME` alone does **not** rename the tab. Change both.

## 2. Prerequisites

On the machine that builds the images:

- Docker Engine 24+ with the `buildx` plugin (`docker buildx version`)
- Access to the registry namespace: `docker login` (use an access token, not your password)
- QEMU emulation if you build `linux/amd64` on an Apple Silicon or other arm64 machine — Docker Desktop provides it; on Linux install `binfmt` support (`docker run --privileged --rm tonistiigi/binfmt --install all`)

Create a dedicated builder once. The default `docker` driver cannot build multi-platform images:

```bash
make builder
# equivalent to: docker buildx create --name multiarch --driver docker-container --bootstrap
```

Pass `--builder multiarch` to every build command below (or set it as the default with `docker buildx use multiarch`).

## 3. Build and push

From the repository root, on a clean checkout of the commit you are releasing:

```bash
VERSION=2026-11-01.1

docker buildx build \
  --platform linux/amd64,linux/arm64 \
  --builder multiarch \
  -f Dockerfile --target prod \
  -t prismaeco/pkab-app:${VERSION} \
  -t prismaeco/pkab-app:latest \
  --push .

docker buildx build \
  --platform linux/amd64,linux/arm64 \
  --builder multiarch \
  -f Dockerfile --target web_prod \
  -t prismaeco/pkab-nginx:${VERSION} \
  -t prismaeco/pkab-nginx:latest \
  --push .
```

What each flag does:

- `--platform linux/amd64,linux/arm64` — builds both architectures into one manifest, so the same tag runs natively on an amd64 VM and on Apple Silicon.
- `--builder multiarch` — the `docker-container` builder that supports multi-platform output.
- `-f Dockerfile --target prod|web_prod` — selects the stage; there are no separate `docker/php/Dockerfile` / `docker/nginx/Dockerfile` files.
- `--push` — required for multi-platform builds; Docker cannot store a multi-arch manifest in the local image store.
- Two `-t` flags — the immutable version plus `latest`.

**Faster single-architecture release.** If the VM architecture is known and you do not need arm64, build only that platform. This is roughly twice as fast:

```bash
VERSION=2026-11-01.1

docker buildx build --platform linux/amd64 \
  -f Dockerfile --target prod \
  -t prismaeco/pkab-app:${VERSION} -t prismaeco/pkab-app:latest --push .

docker buildx build --platform linux/amd64 \
  -f Dockerfile --target web_prod \
  -t prismaeco/pkab-nginx:${VERSION} -t prismaeco/pkab-nginx:latest --push .
```

**Make shortcut.** The same release through the `Makefile`:

```bash
make release VERSION=2026-11-01.1
# override defaults when needed:
make release VERSION=2026-11-01.1 REGISTRY=ghcr.io/prismaeco PLATFORMS=linux/amd64
```

`make release` tags both images with `${VERSION}` and `latest`, using the `multiarch` builder. The repository names are `pkab-app` / `pkab-nginx` under `REGISTRY` (default `prismaeco`).

### Building on the VM instead of a registry

Some stack managers can run containers but cannot build images. In that case build on the VM with the CLI first, then let the manager start the stack — it only has to run the existing images.

```bash
cd /opt/pkab
make prod-build   # docker compose ... -f docker-compose.prod.yml -f docker-compose.build.yml build
make prod-up      # up -d --no-build
```

The `build:` sections live in `docker-compose.build.yml`, not in the runtime file, so a stack manager never sees them. Compose tags the images with the names from `APP_IMAGE` / `NGINX_IMAGE`, so the build and the runtime always agree:

```dotenv
APP_IMAGE=prismaeco/pkab-app:2026-11-01.1
NGINX_IMAGE=prismaeco/pkab-nginx:2026-11-01.1
```

The build is native to the VM architecture — no platform pinning, no emulation.

Do **not** run `make prod-deploy` on a locally built tag: it runs `docker compose pull`, which would overwrite your local image with the registry version of the same tag. Use `make prod-build && make prod-up` for local builds and `make prod-deploy` only for registry builds.

### Transferring images without a registry

When the VM cannot pull from a registry, build on the machine that has the source and copy the images across as a tarball.

```bash
# build machine — cross-builds for the VM architecture
make save-images VERSION=2026-11-01.1 PLATFORM=linux/amd64   # -> pkab-images.tar.gz

scp pkab-images.tar.gz user@vm:/tmp/

# on the VM
gunzip -c /tmp/pkab-images.tar.gz | docker load
docker compose --env-file docker/.env -f docker-compose.prod.yml up -d --no-build
```

- `PLATFORM` must match the VM: `linux/amd64` for most VPS, `linux/arm64` for ARM instances. The wrong value produces `exec format error` at runtime.
- Start with `up -d --no-build`, **not** `make prod-deploy` — the latter runs `pull` and would replace the loaded image with the registry copy.
- The nginx image already contains the built `public/build`, so nothing extra needs to be copied or committed for the frontend.

`pkab-images.tar.gz` is gitignored. Don't commit image tarballs to git — every version would live in the repository history forever.

### Verify the pushed tags

```bash
docker buildx imagetools inspect prismaeco/pkab-app:2026-11-01.1
docker buildx imagetools inspect prismaeco/pkab-nginx:2026-11-01.1
```

The output must list both `linux/amd64` and `linux/arm64`. A missing platform means the build ran without `--platform` or with a single value.

Sanity-check the image starts and reports the expected PHP version:

```bash
docker run --rm --platform linux/amd64 prismaeco/pkab-app:2026-11-01.1 php -v
```

## 4. First deployment on the VM

The VM needs the repository for the compose files (the application code itself comes from the image).

```bash
git clone git@github.com:prismaecodigital/pkab.git /opt/pkab
cd /opt/pkab
cp docker/.env.example docker/.env
make key                 # paste the value into APP_KEY
```

Edit `docker/.env`:

```dotenv
APP_NAME=PKAB
APP_KEY=base64:...
APP_URL=https://pkab.example.com
APP_DOMAIN=pkab.example.com
APP_TIMEZONE=Asia/Jakarta

DB_DATABASE=pkab
DB_USERNAME=pkab
DB_PASSWORD=...
DB_ROOT_PASSWORD=...
MYSQL_VERSION=8.0

WHACENTER_API_URL=https://api.whacenter.com
WHACENTER_DEVICE_ID=...

CACHE_STORE=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
QUEUE_CONNECTION=sync
BROADCAST_CONNECTION=log

MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@pkab.example.com
MAIL_FROM_NAME=PKAB

LOG_CHANNEL=stack
LOG_LEVEL=warning

SANCTUM_STATEFUL_DOMAINS=pkab.example.com

APP_NETWORK=pkab-internal
TRAEFIK_ENTRYPOINT=websecure
TRAEFIK_CERT_RESOLVER=letsencrypt
ADMINER_PORT=8081

# Pin the released images
APP_IMAGE=prismaeco/pkab-app:2026-11-01.1
NGINX_IMAGE=prismaeco/pkab-nginx:2026-11-01.1
```

Timezones are pinned in `docker-compose.prod.yml` (`APP_TIMEZONE` + `TZ` on app, scheduler, nginx, db and adminer). MySQL follows the container `TZ` through `SYSTEM`, so the app, the database session and the container clock all sit at +07:00 — set the same pair if you ever change it, never only one, or timestamps shift twice.

`docker-compose.prod.yml` declares **no fallback defaults** — every `${VAR}` it references must be present and non-empty, or the container starts with an empty string. `APP_URL` is not derived from `APP_DOMAIN`; set it to the public https URL. `MAIL_MAILER` in particular must be a real transport: an empty value fails with `Mailer [] is not defined`.

`WHACENTER_*` may stay empty — WhatsApp notifications are then not sent (the HTTP call fails against an empty base URL, which is logged). The values are read through `config('services.whacenter.*')`, so they work with `config:cache`.

Confirm Traefik is running with the Docker provider and host networking:

```bash
docker inspect traefik --format '{{.HostConfig.NetworkMode}}'          # host
docker inspect traefik --format '{{json .Config.Cmd}}' | grep providers.docker
```

No external network has to be created: Traefik is on the host network and reaches the app over `APP_NETWORK` (default `pkab-internal`), which Compose creates.

Load the database (skip for a brand-new install and run migrations instead):

```bash
make db-import FILE=/path/to/dump.sql STACK=prod
```

The production compose file has no bind mounts, so the `docker/mysql/dump/` auto-import folder is **development only**. On a VM, import with `make db-import` above or through Adminer (Import → choose the `.sql` file).

Start the stack from the published images:

```bash
make prod-deploy   # docker compose pull + up -d --no-build
```

`--no-build` is important: without it Compose may build locally instead of using the pinned image.

Run migrations only if the database is empty or the dump has no schema:

```bash
make prod-migrate
```

or set `RUN_MIGRATIONS=true` for the first boot and back to `false` afterwards (the scheduler never migrates — it hardcodes `RUN_MIGRATIONS: "false"` so it cannot race the app container).

## 5. Hostinger VPS (Docker Manager)

### Compatibility with KVM 1

| Resource | KVM 1 | Verdict |
| --- | --- | --- |
| CPU | 1 vCPU (AMD EPYC, x86_64) | Enough to **run** the stack; too slow to build on |
| RAM | 4 GB | Comfortable: MySQL ~400 MB, PHP-FPM ~150 MB, nginx/adminer ~40 MB |
| Disk | 50 GB NVMe | Plenty: images ~350 MB + database |
| Architecture | `linux/amd64` | Matches the multi-arch images published by `make release` |

Do not build the images on the VPS: `npm ci` + `npm run build` + `composer install` on 1 vCPU will take a long time and can hit the memory limit. Publish with `make release` (or `make save-images`) and let the VPS pull.

### Why the Docker Manager said "project not found"

The manager does not clone the repository. It only stores the compose text at `/docker/<project-name>/docker-compose.yml` and runs `docker compose` there. Anything that depends on files in the repository therefore fails before the project is created:

| Unsupported | Effect |
| --- | --- |
| `build:` | no source tree → build fails |
| `env_file: docker/.env` | file does not exist → Compose aborts |
| `./docker/mysql/conf.d`, `./docker/mysql/dump` | paths do not exist → empty/broken mounts |
| `name:` at the top level | overrides the project name the manager tracks |
| an image tag that cannot be pulled | `pull access denied` → the project is never created |

`docker-compose.prod.yml` is written for exactly this environment: no `build:`, no `env_file:`, no relative bind mounts, no top-level `name:`. All configuration is passed as `${VAR}` project environment variables.

The compose file has **no fallback defaults** for those variables. A variable that is missing in the panel is not an error — Compose substitutes an empty string, the containers start, and the failure only shows up at runtime (`APP_KEY` empty → the app container exits; `MAIL_MAILER` empty → `Mailer [] is not defined`). Set every variable listed in section 4.

### Deploy

1. Publish the images (from your build machine):

   ```bash
   make release VERSION=2026-11-01.1
   ```

2. In hPanel → VPS → Docker Manager → Compose → **Compose manually**:
   - Project name: `pkab` (3–64 chars, letters/digits/`-`/`_` only)
   - Paste the contents of `docker-compose.prod.yml`

3. Add **all** environment variables from section 4 to the project.

4. Deploy and watch the log:

   ```bash
   cat /docker/pkab/.build.log
   docker ps -a
   docker logs pkab-app --tail 50
   ```

5. Import the database through Adminer over an SSH tunnel (see [Adminer over SSH](DOCKER.md#4-adminer-over-ssh)) or run migrations from the container terminal:

   ```bash
   docker exec -it pkab-app php artisan migrate --force
   ```

### Notes

- The containers are named `pkab-app`, `pkab-nginx`, `pkab-db`, `pkab-adminer` and `pkab-scheduler` (fixed `container_name`, no `-1` suffix), so `docker logs pkab-app` works without looking up the generated name.
- The Traefik labels use `traefik.docker.network: pkab-internal`, which the compose file creates. This works because Traefik runs with `network_mode: host`.
- The named volumes are pinned to `pkab_mysql-data` and `pkab_app-storage`, independent of the Compose project name, so a project rename does not orphan the database.
- If the project still does not appear, the deployment log above always contains the real error — "project not found" in the UI is only the symptom of a failed deploy.
- Keep `APP_IMAGE` / `NGINX_IMAGE` pinned to a released version; `latest` makes rollbacks ambiguous.
- `docker compose exec` runs as `root`. Use `docker exec -u www-data` for anything that writes to `storage/`.

## 6. Releasing an update

```bash
# 1. Build and push the new version from the build machine
VERSION=2026-11-08.1
docker buildx build --platform linux/amd64,linux/arm64 --builder multiarch \
  -f Dockerfile --target prod \
  -t prismaeco/pkab-app:${VERSION} -t prismaeco/pkab-app:latest --push .

docker buildx build --platform linux/amd64,linux/arm64 --builder multiarch \
  -f Dockerfile --target web_prod \
  -t prismaeco/pkab-nginx:${VERSION} -t prismaeco/pkab-nginx:latest --push .

# 2. On the VM: update the pins and roll out
cd /opt/pkab
sed -i 's|^APP_IMAGE=.*|APP_IMAGE=prismaeco/pkab-app:2026-11-08.1|' docker/.env
sed -i 's|^NGINX_IMAGE=.*|NGINX_IMAGE=prismaeco/pkab-nginx:2026-11-08.1|' docker/.env
git pull                       # only if compose/config files changed
make prod-deploy   # docker compose pull + up -d --no-build

# 3. Migrations, when the release includes schema changes
make prod-migrate
```

Always update both images together: the nginx image ships the frontend assets that match the PHP code.

### Rollback

```bash
sed -i 's|^APP_IMAGE=.*|APP_IMAGE=prismaeco/pkab-app:2026-11-01.1|' docker/.env
sed -i 's|^NGINX_IMAGE=.*|NGINX_IMAGE=prismaeco/pkab-nginx:2026-11-01.1|' docker/.env
make prod-deploy   # docker compose pull + up -d --no-build
```

Rolling back the code does **not** roll back the database. If the release ran destructive migrations, restore the backup taken before the deploy (see [Backups and restore](#8-backups-and-restore)) or roll the schema back explicitly.

## 7. Post-deploy checklist

```bash
# Containers are up, no restarts
docker compose --env-file docker/.env -f docker-compose.prod.yml ps

# No fatal errors in the app log
docker compose --env-file docker/.env -f docker-compose.prod.yml logs app --tail=50
docker compose --env-file docker/.env -f docker-compose.prod.yml logs scheduler --tail=20

# Environment and caches are correct
make prod-artisan CMD="about"
# Migrations applied
make prod-artisan CMD="migrate:status"

# HTTPS through Traefik
curl -I https://pkab.example.com/login
```

In the browser, verify:

- [ ] Login works with a **username** (the form also accepts an email address) and `/admin/dashboard` renders the sidebar and dashboard cards
- [ ] Assets load without 404s (hard reload to bypass the cached bundle)
- [ ] PKAB items: list, create, edit, approve and reject
- [ ] Marketlist: list, create, approve/reject, and the PDF report (`/marketlist-report` with a date range) downloads
- [ ] Users, roles, permissions, bus, depts and sites CRUD pages load
- [ ] Language switcher still shows `ID` and the UI is translated
- [ ] An API token works: `curl -H "Authorization: Bearer <token>" https://pkab.example.com/api/v1/users`
- [ ] The scheduler container is running (`docker ps | grep pkab-scheduler`) and `make prod-artisan CMD="schedule:list"` runs
- [ ] WhatsApp notifications reach Whacenter when `WHACENTER_*` is configured
- [ ] Adminer is reachable through the SSH tunnel (`ssh -N -L 8081:127.0.0.1:8081`) and logs in with the DB credentials

## 8. Backups and restore

Back up the database before every release:

```bash
make backup
# or by hand:
docker compose --env-file docker/.env -f docker-compose.prod.yml exec -T db \
  sh -c 'exec mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" --single-transaction --routines "$MYSQL_DATABASE"' \
  > backup-$(date +%Y-%m-%d).sql
```

Restore:

```bash
docker compose --env-file docker/.env -f docker-compose.prod.yml exec -T db \
  sh -c 'exec mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' < backup-2026-11-01.sql
```

Persistent data lives in the named volumes `pkab_mysql-data` (database) and `pkab_app-storage` (logs and compiled views; uploads go to `storage/app/public`). The names are pinned in the compose file, so they do not depend on the Compose project name. `docker compose down` keeps them; `docker compose down -v` deletes them.

Back up the storage volume as well, since it is not in the database:

```bash
docker run --rm -v pkab_app-storage:/data -v "$PWD":/backup alpine \
  tar czf /backup/storage-$(date +%Y-%m-%d).tar.gz -C /data .
```

## 9. Security notes

- `.env` is excluded from the image by `.dockerignore`; all secrets arrive as container environment variables at runtime.
- `docker/.env` holds production secrets — keep it `chmod 600` and out of git (already gitignored).
- Adminer is not published on a public port and has no Traefik router. It binds to `127.0.0.1` on the VM and is reachable only through an SSH tunnel — see [Adminer over SSH](DOCKER.md#4-adminer-over-ssh).
- The PHP-FPM master process runs as `root` (it needs to drop privileges itself); workers and every artisan command started by the entrypoint run as `www-data`.
- `bootstrap/app.php` trusts `X-Forwarded-*` from any proxy. That is only safe because the app container publishes no ports and is reachable exclusively through nginx/Traefik. Do not publish port 9000.
- `APP_DEBUG=false` in the image; Laravel renders its own error page (there is no Ignition — it was dropped with the Laravel 13 upgrade).
- Two debug routes exist in `routes/web.php` (`/view-php-ini` and `/password`) and are registered **only when the app is not in production**. Verified: both return `404` in the production stack.
- The default seeder creates `admin@admin.com` / `admin`. Never seed a production database with it; create real users instead.
- Use a registry access token scoped to read/write for the release machine; the VM only needs pull access.

## 10. Troubleshooting

**`exec format error` / `no matching manifest for linux/amd64`** — the image architecture does not match the VM. Build on the VM itself, or publish a multi-arch image (`make release`) and pull it. Check a tag with `docker buildx imagetools inspect`.

**`pull access denied`** — wrong image name or not logged in on the VM. `docker login` and confirm `APP_IMAGE` / `NGINX_IMAGE` match the pushed tags exactly.

**Compose rebuilds instead of pulling** — `up` was run without `--no-build`, or the image tag does not exist locally. Run `docker compose ... pull` first, then `up -d --no-build`.

**502 from Traefik** — PHP-FPM is not ready or the app container crashed. Check `logs app`; a failed start (usually an empty `APP_KEY`, a schemeless `APP_URL`, or a `require-dev` package used at runtime) aborts the entrypoint. `laravel/ui` is a **runtime** dependency here because `routes/web.php` calls `Auth::routes()`; moving it back to `require-dev` makes the production image crash with "In order to use the Auth::routes() method, please install the laravel/ui package."

**Blank page after a deploy, old markup with new assets** — stale compiled Blade views in the `storage` volume. The entrypoint runs `optimize:clear` on start; if it was skipped, run `make prod-artisan CMD="optimize:clear"` and reload.

**`Please provide a valid cache path.`** — `storage/framework/views` is missing. The Dockerfile creates the directories and the entrypoint recreates them, so this only appears if `storage` is mounted from an empty volume with a stale image.
