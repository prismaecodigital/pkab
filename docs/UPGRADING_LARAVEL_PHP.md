# Upgrading PHP and Laravel — Reusable Playbook

A worked example of upgrading **Laravel 9 → 13**, **PHP 8.0 → 8.5.12**, **Node.js → 26.12.1** and the
frontend build (**laravel-mix 5 / webpack 4 → Vite 7**) on PKAB, written so the same process can be
applied to other projects. Replace the version numbers, package names and container names as needed.

The golden rule: **upgrade PHP and Laravel together, in one branch, with a database backup and a
rollback image tag ready before you start.**

> **The frontend is part of the upgrade, not an afterthought.** A Node bump is usually forced by the
> build tool, not chosen: laravel-mix 5 runs webpack 4, which cannot run on modern Node at all
> (webpack 4 hashes with MD4 through `crypto.createHash`, removed from OpenSSL 3 / Node 18+). If the
> target Node is 26, the webpack 4 toolchain has to go. On this project that meant migrating the Vue 2
> admin SPA to Vite 7 + `@vitejs/plugin-vue2`, not just bumping a version.

---

## 1. Version matrix

| Laravel | Supported PHP | Released | Bug fixes until | Security until |
|---|---|---|---|---|
| 10 | 8.1 – 8.3 | Feb 2023 | Aug 2024 | Feb 2025 |
| 11 | 8.2 – 8.4 | Mar 2024 | Sep 2025 | Mar 2026 |
| 12 | 8.2 – 8.5 | Feb 2025 | Aug 2026 | Feb 2027 |
| 13 | 8.3 – 8.5 | Mar 2026 | Q3 2027 | Mar 2028 |

Pick the newest PHP the target Laravel supports and that your hosting can run. Pin the exact
patch version everywhere (composer platform, container base image, CI).

### Node.js baseline

The frontend toolchain has its own floor, independent of PHP:

| Tool | Constraint |
|---|---|
| Vite 6 | `engines.node`: `^18 \|\| ^20 \|\| >=22` |
| Vite 7 | `engines.node`: `^20.19 \|\| >=22.12` |
| Vite 8 | `engines.node`: `^20.19 \|\| >=22.12` |
| `laravel-vite-plugin` 2.x | peer `vite: ^7.0.0` |
| `laravel-vite-plugin` 3.x | peer `vite: ^8.0.0` |
| `@vitejs/plugin-vue2` 2.3.4 | peer `vite: ^3 \|\| ^4 \|\| ^5 \|\| ^6 \|\| ^7` |

That last row is the constraint that decides the Vite major for a Vue 2 application: there is no
Vue 2 plugin for Vite 8, so a Vue 2 SPA stays on **Vite 7 + laravel-vite-plugin 2.x**, even though
the Laravel 13 skeleton ships Vite 8. Read the real constraints instead of trusting docs —
`npm view <pkg> engines peerDependencies`, and repeat for every plugin in `devDependencies`. Node
odd-numbered releases (21, 23, 25) are non-LTS; prefer an even release and pin the exact patch,
e.g. `26.12.1`.

---

## 2. Phase 0 — Assessment (do this before touching code)

```bash
# What blocks the target PHP version?
composer why-not php 8.5

# What is behind?
composer outdated --direct

# What does the framework itself require?
composer show laravel/framework --all | head -20
```

Check these before writing any code:

1. **First-party package support.** Each Laravel major has matching package majors:
   `laravel/sanctum` 2 → 4, `laravel/tinker` 2 → 3, `laravel/ui` 3 → 4, `laravel/dusk` 6 → 8,
   `phpunit` 9 → 12, `nunomaduro/collision` 6 → 8.
2. **Third-party blockers.** `composer why-not php <version>` names them precisely. A single
   package with a `~8.3.0` PHP constraint will hold the whole upgrade back.
3. **Packages the framework now ships.** `fruitcake/laravel-cors` is dead — Laravel 9 already had
   built-in CORS (`HandleCors` is in the default global middleware). `spatie/laravel-ignition` was
   removed from the skeleton in Laravel 11; the framework renders its own error page.
   `doctrine/dbal` is no longer needed for `->change()` migrations.
4. **Packages used at runtime but declared dev-only.** This one bites late: PKAB's `routes/web.php`
   calls `Auth::routes()`, which only exists in `laravel/ui`. With `laravel/ui` in `require-dev`,
   `composer install --no-dev` produces an image that boots straight into
   `In order to use the Auth::routes() method, please install the laravel/ui package.` Audit every
   `require-dev` package that a route, config file or view references.
5. **Frontend coupling.** A Vue 2 SPA cannot use Vite 8 (no plugin), and cannot stay on webpack 4
   with Node 26. Decide up front whether the frontend is part of this upgrade.
6. **Skeleton strategy.** Laravel 11+ still supports the Laravel 9 structure (kernels,
   `RouteServiceProvider`, `config/app.php` providers), but the new skeleton is the documented
   path. Choose deliberately — see §4.
7. **Upgrade path.** One jump is fine when the intermediate majors are documented as minor
   (11 → 12 → 13). Stage the upgrade when a major rewrites the skeleton or a frontend stack.
8. **Node.js baseline.** Read the `engines` of Vite and every Vite plugin you use. Confirm your
   laptop, CI and the Docker build image all run the same exact Node version.

What this project actually had:

```bash
composer why-not php 8.5
# laravel/framework 9.x requires php ^8.0.2 (no 8.3+ guarantees, no 8.5)
# fruitcake/laravel-cors 2.0.5 (abandoned, Laravel 9 only)
# spatie/laravel-ignition 1.x (removed from the skeleton in Laravel 11)
```

---

## 3. Phase 1 — Safety net

```bash
git switch -c upgrade/laravel-13-php-8.5
cp composer.lock /tmp/composer.lock.bak       # only if the lockfile is not tracked
cp package-lock.json /tmp/package-lock.json.bak
```

Database:

```bash
mysqldump --single-transaction --quick --routines --triggers \
  -h "$DB_HOST" -u "$DB_USERNAME" -p "$DB_DATABASE" > backup-$(date +%F-%H%M).sql
```

Record row counts so you can compare after the deploy:

```sql
SELECT 'users', COUNT(*) FROM users
UNION ALL SELECT 'pkab_items', COUNT(*) FROM pkab_items
UNION ALL SELECT 'marketlist_order', COUNT(*) FROM marketlist_order
UNION ALL SELECT 'status_histories', COUNT(*) FROM status_histories;
```

Also tag or note the **last known-good container image** — that is your fastest rollback.

---

## 4. Phase 2 — Toolchain and dependency resolution

### Pin the runtime

```json
// composer.json
"require": { "php": "^8.5", "laravel/framework": "^13.17" },
"config": { "platform": { "php": "8.5.12" } }
```

`config.platform.php` makes resolution deterministic regardless of the PHP running Composer.

### Run Composer on the target PHP

Prefer the target runtime, not `--ignore-platform-reqs`. With `config.platform.php` set, the
official Composer image resolves for that version even if its own PHP is older:

```bash
docker run --rm -v "$PWD":/app -w /app composer:2 \
  composer update --with-all-dependencies --no-scripts
```

`--no-scripts` matters when the skeleton is still the old one: the `post-autoload-dump` script runs
`php artisan package:discover`, which fails while `bootstrap/app.php` still binds the old kernels.
Regenerate the autoloader once the code is migrated (`composer dump-autoload`).

**The Composer image lacks `ext-gd`.** PKAB's `maatwebsite/excel` 4 pulls `phpoffice/phpspreadsheet` 5,
which requires `ext-gd`; resolution aborts with `phpoffice/phpspreadsheet 5.10.0 requires ext-gd *`.
Ignore that one requirement for resolution only — the runtime image installs GD:

```bash
docker run --rm -v "$PWD":/app -w /app composer:2 \
  composer update --with-all-dependencies --no-scripts --ignore-platform-req=ext-gd
```

Verify:

```bash
php artisan --version        # expect the new major
composer validate
```

### Container base images

Pin exact patches, never floating tags:

```dockerfile
FROM php:8.5.12-fpm-alpine
```

Pin Composer too — `composer:2` floats across releases, and lockfile handling has changed between
Composer minors:

```dockerfile
ARG COMPOSER_VERSION=2.11.1
FROM composer:${COMPOSER_VERSION} AS composer_bin
```

```dockerfile
FROM node:26.12.1-alpine AS assets
```

Declare the Node version for local development as well, so a stale laptop install is caught
immediately:

```json
"engines": { "node": ">=26.12.1" }
```

```text
# .nvmrc
26.12.1
```

Verify the new Node version before committing the bump:

```bash
node -v && npm -v
npm ci && npm run build && shasum -a 256 public/build/manifest.json
```

If the host Node differs from the target, run the same check inside the pinned image:

```bash
docker run --rm -v "$PWD":/src:ro -w /tmp node:26.12.1-alpine sh -c '
  mkdir -p /tmp/app && cd /tmp/app &&
  cp -a /src/package.json /src/package-lock.json /src/vite.config.js /src/resources /src/public . &&
  npm ci && npm run build && shasum -a 256 public/build/manifest.json'
```

The manifest hash must match what the Docker build produces. `npm ci` (lockfile) is
deterministic; a local `npm install` can resolve different transitive versions and produce
different asset hashes, which makes committed assets and image assets diverge.

---

## 5. Phase 3 — Skeleton modernization (Laravel 11+)

Only if you chose the new skeleton. It removes five files and moves their behaviour into
`bootstrap/app.php`.

### New `bootstrap/app.php`

```php
use App\Http\Middleware\AuthGates;
use App\Http\Middleware\SetPreferredLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            AuthGates::class,
        ]);

        $middleware->api(
            prepend: [EnsureFrontendRequestsAreStateful::class],
            append: [AuthGates::class, SetPreferredLocale::class],
        );

        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo('/admin/dashboard');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
```

### New `bootstrap/providers.php`

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
];
```

**Remove `providers` from `config/app.php`.** The framework base config supplies the framework
providers and `bootstrap/providers.php` is merged on top — leaving the old array causes duplicate
provider registration.

**`aliases` is still read.** `RegisterFacades` merges `config('app.aliases')` with the package
manifest, so a custom facade alias still belongs there. PKAB keeps only:

```php
'aliases' => Facade::defaultAliases()->merge([
    'PDF' => Barryvdh\DomPDF\Facade\Pdf::class,
])->toArray(),
```

`Barryvdh\DomPDF\Facade` (the v2 class) does not exist in v3 — the alias must point at
`Barryvdh\DomPDF\Facade\Pdf`.

### New `public/index.php`

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
```

### Delete

| File | Replacement |
|---|---|
| `app/Http/Kernel.php` | `withMiddleware()` |
| `app/Console/Kernel.php` | `withRouting(commands:)` |
| `app/Exceptions/Handler.php` | `withExceptions()` |
| `app/Providers/RouteServiceProvider.php` | `withRouting()` + route groups |
| `app/Providers/EventServiceProvider.php` | automatic event discovery; observers registered in `AppServiceProvider::boot()` |
| Default middleware copies | framework defaults (see below) |
| `tests/CreatesApplication.php` | framework `TestCase::createApplication()` |

Default middleware copies that can be dropped: `Authenticate` (→ `redirectGuestsTo`), `EncryptCookies`,
`PreventRequestsDuringMaintenance`, `RedirectIfAuthenticated` (→ `redirectUsersTo`), `TrimStrings`
(the framework default already skips the password fields), `TrustHosts`, `TrustProxies`
(→ `trustProxies()`), `ValidateSignature`, `VerifyCsrfToken` (Laravel 13 renamed the middleware to
`PreventRequestForgery`; the old class is a deprecated subclass). Keep only the ones with real
customisation (`AuthGates`, `SetPreferredLocale`, …).

**Search for references to deleted classes** — the compiler will not catch string/route references:

```bash
grep -rn "RouteServiceProvider\|EventServiceProvider\|App\\\\Http\\\\Kernel\|CreatesApplication\|Fruitcake" \
  app bootstrap config routes tests
```

`RouteServiceProvider::HOME` is the usual casualty. Replace it with `'/admin/dashboard'` (or
`route('dashboard', absolute: false)`) in the auth controllers **and tests** — PKAB had it in five.

### Route namespaces

The Laravel 11+ skeleton no longer sets a root controller namespace. Route groups still accept a
`namespace` attribute (`RouteRegistrar` allows it), but a relative value is no longer prefixed with
`App\Http\Controllers`:

```php
// Laravel 9: resolved to App\Http\Controllers\Api\V1\Admin\AbilitiesController
Route::group(['namespace' => 'Api\V1\Admin'], ...);

// Laravel 13: must be absolute, or the class is looked up as Api\V1\Admin\AbilitiesController
Route::group(['namespace' => 'App\Http\Controllers\Api\V1\Admin'], ...);
```

`php artisan route:list` fails with `Class "Api\V1\Admin\AbilitiesController" does not exist` until
this is fixed. Prefer `[Controller::class, 'method']` for single routes.

### Config files and base-config merging

Laravel 11+ merges framework defaults under your config files. Deleting `config/view.php`,
`config/hashing.php` or `config/broadcasting.php` is safe; deleting `config/cors.php` or
`config/sanctum.php` silently changes behaviour, so keep files that carry real customisation.

Two env keys were renamed and this project still read the old ones:

- `config/cache.php` — `env('CACHE_DRIVER')` → `env('CACHE_STORE')`
- `config/broadcasting.php` — `env('BROADCAST_DRIVER')` → `env('BROADCAST_CONNECTION')`

The Laravel 13 skeleton, `phpunit.xml` and the Docker compose files all use the new names; leaving
the old ones means the new variables are silently ignored.

---

## 6. Phase 4 — First-party packages

### Sanctum 4

```bash
php artisan vendor:publish --tag=sanctum-config --force
php artisan vendor:publish --tag=sanctum-migrations --force
```

Update `config/sanctum.php` keys:

```php
'middleware' => [
    'authenticate_session' => AuthenticateSession::class,
    'encrypt_cookies'      => EncryptCookies::class,
    'validate_csrf_token'  => PreventRequestForgery::class,
],
```

The old `verify_csrf_token` key still works as a fallback, but it points at a deprecated class.
Existing `personal_access_tokens` tables keep working: the refreshed migration only changes
`name` to `text` and indexes `expires_at` for **new** installs. Because the filename is unchanged,
Laravel skips it on existing databases.

### laravel-dompdf 3

- The facade moved to `Barryvdh\DomPDF\Facade\Pdf`; the `PDF` alias in `config/app.php` must be
  updated (see §5). `PDF::loadview()` / `->download()` usage is unchanged.
- Publish the config if you need to change paper size or the base path:
  `php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"`.

### maatwebsite/excel 4

- Excel 4 requires `phpoffice/phpspreadsheet` 5 and **`ext-gd`** — the Docker image must install GD
  (`docker-php-ext-install gd` with `--with-freetype --with-jpeg`), and Composer resolution in the
  `composer:2` image needs `--ignore-platform-req=ext-gd`.
- The `Excel::download()` / `FromCollection` API used here is unchanged.
- `App\Exports\PkabItemExport` is a stub that returns nothing and is not wired into a route; it was
  left as-is.

### laravel/ui 4

`Auth::routes(['register' => false])` still works and still resolves
`App\Http\Controllers\Auth\*`. It must be a **runtime** dependency (§2, point 4).

### Ziggy / Tinker

This project has no Ziggy (the SPA uses plain `axios` against `/api/v1`). Tinker 3 is a drop-in for
2.x usage.

---

## 7. Phase 5 — PHP version gotchas

Run a syntax pass over the whole tree with the target binary:

```bash
find app bootstrap config database routes tests -name '*.php' \
  -exec php -l {} \; | grep -v "No syntax errors"
```

Common issues between 8.0 and 8.5:

| Change | Fix |
|---|---|
| `PDO::MYSQL_ATTR_SSL_CA` deprecated in 8.5 | `Pdo\Mysql::ATTR_SSL_CA` (add `use Pdo\Mysql;`) |
| Implicit nullable params deprecated in 8.4 | `?Type $x = null` |
| Non-canonical casts deprecated in 8.5 | `(bool)`, `(int)`, `(float)`, `(string)` |
| `null` as array offset deprecated in 8.5 | use `''` |
| `symfony/polyfill-php85` defines `array_first()` / `array_last()` | prefer `Arr::first()` / `Arr::last()`; avoid global helpers with those names |
| Incrementing non-numeric strings deprecated | `str_increment()` |
| Carbon 2 → 3 (`nesbot/carbon ^3`) | `diffIn*` now returns float by default; check date maths |
| `Carbon::createFromFormat('Y-m-d H:i:s', $value)` on a `DATE` column | Carbon 3 throws `InvalidFormatException: Not enough data available to satisfy format` (Carbon 2 returned `false` and the following `->format()` blew up anyway). Use `Carbon::parse($value)` in accessors — it accepts both `Y-m-d` and `Y-m-d H:i:s` |

Run the test suite with deprecations visible:

```bash
php -d error_reporting=E_ALL artisan test
```

---

## 8. Phase 6 — Database safety

**Goal: prove that `migrate` is a no-op before running it on production.**

1. No new migrations should be required for the upgrade itself. If a package refreshed a
   migration file, the filename is unchanged, so it is skipped.
2. Review every migration that is still pending. Additive migrations (new table, nullable
   column) are safe; anything with `change()`, `dropColumn`, `renameColumn` or raw
   `ALTER TABLE` needs a rehearsal.
3. Rehearse against a scratch database built from the **pre-upgrade** code:

```bash
DB_DATABASE=scratch php artisan migrate --force
DB_DATABASE=scratch php artisan migrate:status
DB_DATABASE=scratch php artisan migrate --pretend --force   # expect "Nothing to migrate"
```

4. Compare row counts before/after, and exercise the package whose migration changed
   (e.g. issue and resolve a Sanctum token).

Breaking changes to look for in migrations when jumping to Laravel 11+:

- `->change()` now drops modifiers you do not repeat
- `float`/`double` signatures changed; `unsignedDecimal`/`unsignedFloat` removed
- `upsert()` now requires a non-empty `uniqueBy`
- Doctrine DBAL helpers removed (`Schema::getAllTables()` → `Schema::getTables()`), and
  `doctrine/dbal` itself is no longer installed

### The migration-order bug this project had

`2023_07_05_152138_marketlist_order_table` declared a foreign key to `site`, but
`2023_07_11_143839_create_site_table` creates that table **six days later**. On an existing
production database the table existed already, so nobody noticed; on a fresh database the
migration fails with:

```
SQLSTATE[HY000]: General error: 1824 Failed to open the referenced table 'site'
```

Fix without renumbering (renaming a migration makes it re-run on existing databases):

1. Create the column without the constraint in the original migration, and only add the
   constraint when `Schema::hasTable('site')`.
2. Add a new, later migration that adds the constraint when it is missing:

```php
$hasSiteForeignKey = collect(Schema::getForeignKeys('marketlist_order'))
    ->contains(fn (array $key) => $key['foreign_table'] === 'site');
```

`Schema::getForeignKeys()` is native in Laravel 11+ (no DBAL). The migration is a no-op on
databases that already have the key, and it makes fresh installs match.

A quick way to find other ordering bugs before running anything:

```python
# compare every ->on('table') against the timestamp of Schema::create('table')
```

**Never run on production:** `migrate:fresh`, `migrate:refresh`, `db:wipe`, `db:seed`
(the default seeder inserts a demo admin), or `docker compose down -v`.

---

## 9. Phase 7 — Frontend: laravel-mix 5 (webpack 4) → Vite 7

This was the largest part of the PKAB upgrade. The SPA is Vue 2 (`resources/adminapp/js`, ~120 SFCs,
lazy route imports, `element-ui`, `bootstrap-vue`, `@casl/vue`, `vue-excel-export`).

### Why webpack 4 had to go

- webpack 4 uses MD4 hashing, unavailable on Node 18+ without `--openssl-legacy-provider`.
- `laravel-mix` 5 depends on webpack 4; `laravel-mix` 6 (webpack 5) is unmaintained.
- Node 26 + Vite 7 is a supported pair, and Vite is what the Laravel 13 skeleton ships.

### Dependency changes

| Before | After |
|---|---|
| `laravel-mix` 5, `webpack`, `webpack-cli`, `cross-env`, `resolve-url-loader`, `sass-loader` 8, `@vue/cli-plugin-babel`, `.babelrc` | `vite` 7.3.7, `laravel-vite-plugin` 2.1.0, `@vitejs/plugin-vue2` 2.3.4, `sass` 1.107.1 |
| `vue` 2.5/2.6, `vue-template-compiler` | `vue` 2.7.16 (required by `@vitejs/plugin-vue2`, which uses `vue/compiler-sfc`) |
| `axios` 0.19 | `axios` 1.21 |
| `object-to-formdata` 3 | `object-to-formdata` 5 |
| `lodash` 4.17.13 | `lodash` 4.18.1 |
| `vue-sweetalert2` 3 | `vue-sweetalert2` 5 |
| `vue-router` 3.3, `vuex` 3.5, `vue-i18n` 8.22 | `vue-router` 3.6.5, `vuex` 3.6.2, `vue-i18n` 8.28.2 |

Keep `@casl/ability` 4 + `@casl/vue` 1: `@casl/vue` 2+ requires Vue 3. Keep `vue-multiselect` 2.x
(3.x is Vue 3 only).

### Code changes

1. **`require()` → `import`.** Vite is ESM-only; `require('vue')` in application source fails at
   runtime. `resources/adminapp/js/app.js` and `bootstrap.js` now use `import`.

2. **`process.env.MIX_*` → `import.meta.env.VITE_*`.** Rename the variables in `.env` too
   (`VITE_VUE_APP_I18N_LOCALE`, `VITE_VUE_APP_I18N_FALLBACK_LOCALE`). `process.env.NODE_ENV` →
   `import.meta.env.DEV`.

3. **Sass `~` imports.** `@import '~vue-select/dist/vue-select.css'` is webpack syntax; Vite resolves
   bare package paths, so drop the tilde.

4. **Blade `mix()` → `@vite`.** `{{ mix('/adminapp/js/app.js') }}` becomes
   `@vite(['resources/adminapp/sass/app.scss', 'resources/adminapp/js/app.js'])`. Vite emits
   `public/build/manifest.json`; the old `public/adminapp/*` bundles are unused.

5. **Vite aliases.** `@`, `@pages`, `@components`, `@cruds` map to `resources/adminapp/js` in
   `vite.config.js` — the same aliases `mix.webpackConfig()` defined.

6. **CommonJS deep imports inside source-shipped packages.** `@laraveldaily/vue2-datatable` publishes
   `src/` as its `module`, so Vite serves it as source and its `import groupBy from 'lodash/groupBy'`
   reaches the browser untransformed:

   ```
   SyntaxError: The requested module '/node_modules/lodash/groupBy.js' does not provide an export named 'default'
   ```

   Pre-bundle each one explicitly (the list was found by iterating the runtime errors):

   ```js
   optimizeDeps: {
     include: ['lodash/groupBy', 'lodash/debounce', 'lodash/throttle', 'browser-downloads']
   }
   ```

   `browser-downloads` comes from `vue-excel-export`, which also ships `.vue` sources and therefore
   is not pre-bundled.

7. **`object-to-formdata` 5 renamed its export.** `objectToFormData` → `serialize`:

   ```js
   import { serialize as objectToFormData } from 'object-to-formdata'
   window.objectToFormData = objectToFormData
   ```

   The global is what the 30+ store modules call, so aliasing on import keeps them untouched.

8. **Case-sensitive import paths.** macOS resolves `DatetimePicker.vue` against
   `DatetimePIcker.vue`; Linux does not. The Docker build fails with
   `[vite:load-fallback] Could not load .../DatetimePicker.vue`. Two were fixed here:
   `components/DatetimePIcker.vue` → `DatetimePicker.vue`, and
   `SidebarPlugin/index.js` imported `./SideBar.vue` instead of `./Sidebar.vue`.
   `resources/adminapp/js/layouts/DefaultLayout.vue` still imports three components that do not
   exist; it is dead code and not part of the bundle.

9. **Vue 2 mount.** `new Vue({ el: '#app' })` replaces the `#app` element, so
   `document.getElementById('app')` is `null` afterwards — that is normal, not a failed mount. Check
   `window.Vue` and rendered text instead.

10. **Runtime-only Vue build.** `@vitejs/plugin-vue2` aliases `vue` to
    `vue/dist/vue.runtime.esm.js` unless you define your own alias. Any component declared with an
    inline `template:` string then fails to compile — in development Vue logs
    `You are using the runtime-only build of Vue where the template compiler is not available`, and in
    production the warning is stripped, so the component renders **nothing, silently**. PKAB had one:
    `const View = { template: '<router-view></router-view>' }` in `routes.js`, used as the parent of
    every nested route, so all nested pages were empty shells. Fix it by precompiling:

    ```js
    const View = { render: h => h('router-view') }
    ```

    (The alternative, `resolve.alias: { vue: 'vue/dist/vue.esm.js' }`, bundles the compiler and hides
    the next one. Prefer render functions.)

11. **Global script tags still work.** jQuery, `moment` and `bootstrap-material-design` are loaded by
    `<script>` tags in the Blade layout. Vite's `type="module"` scripts are deferred, so they run
    after those classic scripts and `window.moment` / `window.$` exist by the time the bundle runs.

### Vite config used

```js
export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/adminapp/js/app.js', 'resources/adminapp/sass/app.scss'],
      refresh: ['resources/views/**']
    }),
    vue()
  ],
  resolve: { /* @, @pages, @components, @cruds */ },
  optimizeDeps: { include: ['lodash/groupBy', 'lodash/debounce', 'lodash/throttle', 'browser-downloads'] },
  server: {
    host: '0.0.0.0',
    port: Number(process.env.VITE_PORT ?? 5173),
    strictPort: true,
    hmr: { host: process.env.VITE_HMR_HOST || 'localhost' }
  }
})
```

`VITE_PORT` / `VITE_HMR_HOST` keep the dev container, the published port and `public/hot` in sync.

---

## 10. Phase 8 — Container and build pitfalls

These caused every real failure during this upgrade; check them in any project.

### `.dockerignore` can strip required runtime directories

If `storage/framework/{views,cache,sessions}` or `storage/logs` are excluded, the image has no
compiled-view directory and every request fails with:

```
Please provide a valid cache path. (500)
```

because `config/view.php` resolves `realpath(storage_path('framework/views'))` to `false`.
Create them in the Dockerfile **and** in the entrypoint (a named volume mounted over `storage/`
starts empty):

```dockerfile
RUN mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache
```

`bootstrap/cache` must be in that list too — the **development** stage has no application code yet
(bind-mounted at runtime), so `chown bootstrap/cache` fails the image build without `mkdir -p`.

### OPcache is already compiled into `php:8.5`

`docker-php-ext-install opcache` fails with `cp: can't stat 'modules/*'` on the 8.5 images because
Zend OPcache ships statically linked. Only configure it, never build it:

```dockerfile
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/zz-opcache.ini
```

### `composer install --no-dev` and runtime-only dev packages

See §2 point 4: the image boots, the entrypoint fails, and the container restart-loops. Test the
production image, not just the dev container.

### Stale compiled Blade views survive deploys

`@vite`, `@csrf` and friends are **compile-time** directives. Compiled views live in the
mounted `storage` volume, and Docker preserves source mtimes. Blade only recompiles when
`mtime(source) >= mtime(compiled)`, so a view compiled by the previous release can be
considered fresh forever.

**Always run after deploying an image:**

```bash
docker exec <php-container> php artisan optimize:clear
docker exec <php-container> php artisan migrate --force
docker exec <php-container> php artisan optimize
```

### Assets must come from the same commit

`php` and `nginx` images each build `public/build` independently. Build **both from the same
commit** and verify they agree:

```bash
docker exec <php-container>   shasum -a 256 public/build/manifest.json
docker exec <nginx-container> shasum -a 256 public/build/manifest.json
```

A mismatch means one image is stale: the HTML references asset hashes the other container does
not have → 404s → blank page.

### Tests and the missing `.env`

The image deliberately has no `.env`; configuration comes from container environment variables.
PHPUnit 12 surfaces the `@file_get_contents()` suppression that phpdotenv uses, so a missing
`.env` becomes a warning on every test. Two things fix it:

- a committed `.env.testing` with non-secret test defaults (`.dockerignore` needs `!.env.testing`
  after `.env.*`, or it is excluded from the image), and
- `APP_ENV=testing` in the **container** environment, because PHPUnit's `<env>` entries do not
  override an existing container variable and Laravel chooses `.env.testing` from `Env::get('APP_ENV')`.

`make test` passes `-e APP_ENV=testing -e DB_DATABASE=pkab_testing`.

### Lockfiles

`composer.lock` is tracked here; `package-lock.json` was **gitignored**, which makes `npm ci` in the
image impossible to reproduce. It is now tracked.

---

## 11. Verification checklist

```bash
# Runtime
php -v
php artisan --version
php artisan about
php artisan route:list
php artisan config:clear && php artisan view:cache && php artisan optimize:clear && php artisan optimize
composer validate
node -v && npm -v

# Static
find app bootstrap config database routes tests -name '*.php' -exec php -l {} \; | grep -v "No syntax errors"
./vendor/bin/pint --test        # only the files you touched, if the repo is not already clean

# Frontend
npm run build

# HTTP
curl -sI https://host/ | head -3                       # 302 -> /login
curl -sI https://host/login | head -3                  # 200
curl -s https://host/login | grep -o '<script[^>]*app-[^"]*\.js'   # built bundle
curl -sI https://host/build/assets/<app-bundle>.js     # 200

# Tests
php artisan test
```

Browser smoke test (catches client-side failures the server cannot see):

```bash
"/Applications/Google Chrome.app/Contents/MacOS/Google Chrome" \
  --headless=new --disable-gpu --no-sandbox --virtual-time-budget=8000 \
  --dump-dom https://host/login | grep -c "PKAB"
```

A rendered page with real content means Vue mounted. An empty `<div id="app">` with a 200 means a
client-side error — check the console. On this upgrade the SPA was verified through the Chrome
DevTools Protocol: log in, open `/admin/dashboard`, assert `window.Vue`, `window.axios` and the
rendered sidebar text, and collect `Runtime.exceptionThrown` / 4xx responses. That is what caught
the CommonJS deep-import failures, which a `200` never would.

---

## 12. Rollback

1. **Code:** redeploy the previous image tag or `git checkout <previous-tag>` and reinstall
   dependencies.
2. **Caches:** `php artisan optimize:clear` after switching back.
3. **Database:** only if a migration ran. Restore the dump:
   `mysql -h "$DB_HOST" -u "$DB_USERNAME" -p "$DB_DATABASE" < backup-<timestamp>.sql`.
   This upgrade adds one additive migration (the missing `site` foreign key); a code rollback
   alone is normally sufficient.

---

## 13. Failure → cause → fix

| Symptom | Cause | Fix |
|---|---|---|
| `npm ci` / `npm run build` fails with `EBADENGINE` or an unsupported syntax error | Node older than the toolchain floor | bump the build image, `engines` and `.nvmrc`; re-run `npm ci` |
| `ERR_OSSL_EVP_UNSUPPORTED` / `digital envelope routines::unsupported` | webpack 4 on Node 18+ (MD4) | migrate the build to Vite (or pin Node 16, which is EOL) |
| Multi-arch build fails with `input/output error`, `Cannot open phar archive "/usr/bin/composer"`, `listing workers: … EOF` | the build host is out of disk space; BuildKit's metadata write and container reads fail | free space (`docker builder prune -a`, `docker image prune -a`), restart Docker Desktop, recreate the builder |
| `variable expansion is not supported for --from` | `COPY --from=image:${ARG}` | declare a stage `FROM image:${ARG} AS name` and copy from that stage |
| `SyntaxError: The requested module '/node_modules/lodash/…js' does not provide an export named 'default'` | CommonJS deep import from a source-shipped package | add it to `optimizeDeps.include` |
| `[vite:load-fallback] Could not load …` | import path case differs from disk (macOS vs Linux) | fix the import or rename the file to match |
| `Class "Api\V1\Admin\…Controller" does not exist` on `route:list` | route group namespace is relative; the root namespace is gone in Laravel 11+ | make the namespace absolute or use `[Controller::class, …]` |
| `In order to use the Auth::routes() method, please install the laravel/ui package` in the production image | `laravel/ui` is in `require-dev` but used by `routes/web.php` | move it to `require` |
| `phpoffice/phpspreadsheet … requires ext-gd` during `composer update` | the Composer image has no GD | `--ignore-platform-req=ext-gd`, install GD in the runtime image |
| `SQLSTATE[HY000]: 1824 Failed to open the referenced table 'site'` | migration references a table created by a later migration | create the column first, add the constraint in a later migration |
| `Please provide a valid cache path.` | `storage/framework/views` missing in image or empty volume | create dirs in Dockerfile **and** entrypoint |
| `chown: bootstrap/cache: No such file or directory` while building the dev stage | no application code in the stage | `mkdir -p bootstrap/cache` before `chown` |
| `cp: can't stat 'modules/*'` building `opcache` | OPcache is prebuilt in `php:8.5` | remove `opcache` from `docker-php-ext-install` |
| 502 Bad Gateway | nginx cannot reach php-fpm | check `fastcgi_pass` host, container network, `docker logs` |
| Login works, every API call returns 401 | `SANCTUM_STATEFUL_DOMAINS` does not match host:port | add the host and port, recreate the containers |
| Blank page, 200 response, `https://` asset tags on `http://` | `forceScheme('https')` | serve over https or drop the force |
| Asset 404s | php/nginx images built from different commits | rebuild both, compare manifest hash |
| `Unable to prepare route … for serialization` on `route:cache` | duplicate route names (closure routes are serialisable since Laravel 11) | prefix one set (`->names('api.resource')`) |
| Composer: lock file has no compatible set | stale `composer.lock` on the server | `composer update` on the server or track the lockfile |
| Login rehashes passwords | Laravel 11+ rehash-on-login with changed work factor | expected; keep `BCRYPT_ROUNDS` stable |
| Sessions invalidated | skeleton switched `session.serialization` to `json` | keep `php` or accept re-login |
| `TypeError: Cannot assign null to property … of type string` | `env()` returned null into a typed property | give the property a default (`?? ''`) or read from config |
| Nested SPA routes render an empty shell; dev console shows `runtime-only build of Vue` | inline `template:` string with the runtime-only Vue build forced by `@vitejs/plugin-vue2` | replace with a render function (or alias the full build) |
| API 500 `InvalidFormatException: Not enough data available to satisfy format` | `createFromFormat('Y-m-d H:i:s', …)` on a `DATE` value, strict in Carbon 3 | `Carbon::parse($value)` |
| `Cannot read properties of undefined (reading 'name' / 'forEach')` in a Vuex `setEntry` | the `edit` endpoint loads fewer relations than the store mutation reads | load the same relations as `show` |
| Login posts, the form resets, **no error is shown** | the form posts `username`, `AuthenticatesUsers::username()` authenticates `email`, so validation fails on a missing `email` while the blade only renders `@error('username')` | override `username()` to `'username'` and map `credentials()` to username-or-email |
| Tests warn `file_get_contents(/var/www/html/.env)` | no `.env` in the image, `.env.testing` not selected | commit `.env.testing`, run tests with `APP_ENV=testing` |

---

## 14. Reuse checklist for the next project

1. Fill in the version matrix and confirm the hosting supports the target PHP patch.
2. Run `composer why-not php <version>` and `composer outdated --direct`; write down blockers.
3. Audit `require-dev` for packages that routes/config/views use at runtime.
4. Decide: keep the old skeleton or modernize; stage or one jump; frontend scope (and whether the
   current bundler can run on the target Node at all).
5. Branch, back up lockfiles, dump the database, note the last-good image tag.
6. Pin PHP in `composer.json` (`config.platform.php`) and in the container base image.
7. Pin Node in the build-stage image, `package.json` `engines` and `.nvmrc`; verify
   `npm ci && npm run build` on that version and compare the manifest hash with the Docker build.
8. Resolve dependencies on the target PHP (`--no-scripts` while the old skeleton is still in
   place), then `artisan --version`.
9. Modernize the skeleton (if chosen) and grep for references to deleted classes and relative
   route namespaces.
10. Upgrade first-party packages and re-publish their config/migrations.
11. Fix PHP-version deprecations and run `php -l` across the tree.
12. Rehearse migrations against a pre-upgrade scratch database; confirm `Nothing to migrate` — and
    build a scratch database from zero to catch ordering bugs.
13. Migrate the frontend build, then run the production `npm run build` on Linux, not just macOS.
14. Build both images from the same commit; add the storage `mkdir`, `bootstrap/cache` and
    `optimize:clear` steps.
15. Run the verification checklist, including a headless browser render and a production-image boot.
16. Deploy: backup → pull → up → `optimize:clear` → `migrate --pretend` → `migrate --force` →
    `optimize` → row-count comparison.
17. Keep the previous image tag pinned for a one-command rollback.

---

## 15. Applied to this project (PKAB)

What the upgrade actually changed here, for the next person who repeats it.

### Versions

| Component | Before | After |
|---|---|---|
| PHP | `^8.0.2` (ran on 8.3 locally) | `^8.5`, `config.platform.php = 8.5.12` |
| Laravel | 9.x | 13.39.0 |
| Node | unpinned | `26.12.1` (`engines`, `.nvmrc`, Dockerfile) |
| Build tool | laravel-mix 5 (webpack 4) | Vite 7.3.7 + laravel-vite-plugin 2.1.0 |
| Vue | 2.5/2.6 + `vue-template-compiler` | 2.7.16 + `@vitejs/plugin-vue2` 2.3.4 |
| Sanctum | 2.14 | 4.3.3 |
| Tinker | 2.7 | 3.0.2 |
| laravel/ui | 3.4 (dev) | 4.6.3 (runtime) |
| laravel-dompdf | 2.1 | 3.1.2 |
| maatwebsite/excel | 3.1 | 4.0.6 (phpspreadsheet 5.10, needs `ext-gd`) |
| PHPUnit | 9.5 | 12.5.42 |
| `nunomaduro/collision` | 6.1 | 8.9.5 |
| laravel/dusk | 6.22 | 8.7.2 |
| `laravel/pint` | — | 1.32.1 (added) |
| axios | 0.19 | 1.21.0 |
| lodash | 4.17.13 | 4.18.1 |
| object-to-formdata | 3.0.9 | 5.0.0 (`objectToFormData` → `serialize`) |
| vue-sweetalert2 | 3.0.6 | 5.0.11 |
| sass | 1.28 | 1.107.1 |

Dropped: `fruitcake/laravel-cors` (built into Laravel 9+), `laravel/sail` (superseded by the Docker
stack), `spatie/laravel-ignition` (removed from the skeleton in Laravel 11), `laravel-mix`,
`@vue/cli-plugin-babel`, `cross-env`, `resolve-url-loader`, `sass-loader`,
`vue-template-compiler`, and `.babelrc`.

### Skeleton

The Laravel 9 structure was replaced with the Laravel 11+ skeleton:

- `bootstrap/app.php` builds the application (`withRouting`, `withMiddleware`, `withExceptions`) and
  registers `trustProxies(at: '*')`, the `AuthGates` middleware on both `web` and `api`, Sanctum's
  `EnsureFrontendRequestsAreStateful` + `SetPreferredLocale` on `api`, and the login/dashboard
  redirects.
- `bootstrap/providers.php` lists `AppServiceProvider` and `AuthServiceProvider`.
- Deleted: `app/Http/Kernel.php`, `app/Console/Kernel.php`, `app/Exceptions/Handler.php`,
  `app/Providers/RouteServiceProvider.php`, `app/Providers/EventServiceProvider.php`,
  `tests/CreatesApplication.php`, and the unmodified middleware copies (`Authenticate`,
  `EncryptCookies`, `PreventRequestsDuringMaintenance`, `RedirectIfAuthenticated`, `TrimStrings`,
  `TrustHosts`, `TrustProxies`, `VerifyCsrfToken`).
- The `StatusHistory` observer and the `Registered` → `SendEmailVerificationNotification` listener
  moved into `AppServiceProvider::boot()`.
- `RouteServiceProvider::HOME` was replaced with `'/admin/dashboard'` in the five auth controllers.
- `public/index.php` uses `handleRequest(Request::capture())`.
- `tests/TestCase.php` no longer uses `CreatesApplication`; `phpunit.xml` targets the PHPUnit 12.5
  schema (`<source>` instead of `<coverage><include>`).
- `config/app.php` keeps only the `aliases` array (for `PDF`); `providers` is gone.
- `config/cache.php` and `config/broadcasting.php` read `CACHE_STORE` / `BROADCAST_CONNECTION`.

### PHP 8.5

`config/database.php` used `PDO::MYSQL_ATTR_SSL_CA`, deprecated in 8.5 — now
`Pdo\Mysql::ATTR_SSL_CA`. `php -l` across `app bootstrap config database routes tests` is clean on
8.5.12, and no implicit-nullable parameters, non-canonical casts or null array offsets were found.

`WhacenterService` assigned `env('WHACENTER_*')` to typed `string` properties. That threw a
`TypeError` when the variables were unset, and returned `null` once `config:cache` was active in
production (`env()` outside config files is not available in a cached config). It now reads
`config('services.whacenter.url')` / `config('services.whacenter.device_id')`, defined in
`config/services.php`, and casts to string.

### Database

One migration added (`2026_11_01_000000_add_site_foreign_key_to_marketlist_order_table`) and one
edited (`2023_07_05_152138_marketlist_order_table`) to fix the ordering bug in §8. On a database
that already has the key both are no-ops. `migrate:fresh --seed` now succeeds from zero, which it
did not before.

### Authentication

The login form has always posted `username`, but `LoginController` relied on the trait default
(`username()` → `email`). A login attempt therefore failed validation on a missing `email`, the error
was flashed under the `email` key while the view renders `@error('username')`, and the page simply
reset with no message. `LoginController` now overrides `username()` and maps `credentials()` to the
username **or** the email address in that field.

### Frontend

- Entry points moved from `webpack.mix.js` to `vite.config.js`; Blade uses `@vite`; output moved from
  `public/adminapp/*` to `public/build/*` with a manifest.
- `app.js` / `bootstrap.js` converted from `require()` to `import`; `process.env.MIX_*` →
  `import.meta.env.VITE_*`; Sass `~` imports dropped.
- `optimizeDeps.include` lists the four CommonJS deep imports (`lodash/groupBy`,
  `lodash/debounce`, `lodash/throttle`, `browser-downloads`).
- `object-to-formdata` 5 export renamed and aliased on import.
- Two case-sensitive imports fixed (`DatetimePIcker.vue`, `SideBar.vue`); one dead layout left
  untouched.
- Verified in headless Chrome: the SPA mounts on `/admin/dashboard` in both the dev (Vite) and the
  production (built assets) stack, with `window.Vue` and `window.axios` present and no console
  errors other than the three missing `/md/img/*` files.

### Docker

See [DOCKER.md](DOCKER.md) and [PRODUCTION.md](PRODUCTION.md). The image builds `public/build` in a
Node 26 stage and copies it into both the PHP and nginx images; the two manifest hashes were
compared and match (`416e1fb6…`).

### Verification results

- `php artisan --version` → `Laravel Framework 13.39.0`; `php -v` → `8.5.12` inside the container.
- `php artisan about` in the production container: environment `production`, debug `OFF`, timezone
  `Asia/Jakarta`, locale `id`.
- `make test` → 3 passed.
- Dev stack: `/login` 200, login 302 → `/admin/dashboard` 200, SPA rendered via Vite.
- Production stack (built images): same flow with `public/build` assets, `manifest.json` identical in
  the app and nginx images, `/up` 200, `/password` and `/view-php-ini` 404 (production-gated),
  timezone agreement app `21:48 WIB` / DB `WIB 21:48` / UTC `14:48`.
- The theme referenced four images that were never committed (`/md/img/login.jpg`, `register.jpg`,
  `vue.png`, `apple-icon.png`), which produced 404s on every page load. The CSS backgrounds now use
  their gradient only and the layouts use `/favicon.ico`; `/login` and `/admin/dashboard` load with
  no 4xx responses.
