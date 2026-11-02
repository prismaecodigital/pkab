SHELL := /bin/sh

ENV_FILE ?= docker/.env
STACK ?= dev
CMD ?=
FILE ?=
VERSION ?=
REGISTRY ?= prismaeco
PLATFORMS ?= linux/amd64,linux/arm64
PLATFORM ?= linux/amd64
TEST_DATABASE ?= pkab_testing
DB_USER := $(shell grep -E '^DB_USERNAME=' $(ENV_FILE) 2>/dev/null | cut -d= -f2)
BUILDER ?= multiarch

COMPOSE_DEV := docker compose --env-file $(ENV_FILE) -f docker-compose.yml
COMPOSE_PROD := docker compose --env-file $(ENV_FILE) -f docker-compose.prod.yml
COMPOSE_BUILD := $(COMPOSE_PROD) -f docker-compose.build.yml
COMPOSE_LOCAL := $(COMPOSE_PROD) -f docker-compose.local.yml

.DEFAULT_GOAL := help

.PHONY: help env key dev-up dev-down dev-build dev-logs dev-restart sh artisan test fresh db-import \
        prod-build prod-up prod-down prod-deploy prod-local prod-migrate prod-logs prod-shell prod-artisan \
        backup builder release save-images

help: ## Show this help
	@printf '\nPKAB — Docker stack\n\n'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-18s\033[0m %s\n", $$1, $$2}'
	@printf '\n'

env: ## Create docker/.env from docker/.env.example
	@test -f $(ENV_FILE) || cp docker/.env.example $(ENV_FILE)
	@echo "$(ENV_FILE) is ready — set APP_KEY with 'make key'"

key: ## Print a fresh APP_KEY value
	@docker run --rm php:8.5.12-cli php -r 'echo "base64:", base64_encode(random_bytes(32)), PHP_EOL;'

dev-up: ## Build and start the development stack
	$(COMPOSE_DEV) up -d --build

dev-build: ## Rebuild the development app image only
	$(COMPOSE_DEV) build app

dev-down: ## Stop the development stack (keeps volumes)
	$(COMPOSE_DEV) down

dev-restart: ## Recreate the development containers after a docker/.env change
	$(COMPOSE_DEV) up -d --force-recreate

dev-logs: ## Tail the development logs
	$(COMPOSE_DEV) logs -f --tail=100

sh: ## Shell inside the development app container
	$(COMPOSE_DEV) exec -u www-data app sh

artisan: ## Run an artisan command in development: make artisan CMD="about"
	@test -n "$(CMD)" || (echo 'Usage: make artisan CMD="about"'; exit 1)
	$(COMPOSE_DEV) exec -u www-data app php artisan $(CMD)

test: ## Run the test suite against the testing database
	$(COMPOSE_DEV) exec -T db sh -c 'mysql -u root -p"$$MYSQL_ROOT_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $(TEST_DATABASE); GRANT ALL PRIVILEGES ON $(TEST_DATABASE).* TO \"$(DB_USER)\"@\"%\"; FLUSH PRIVILEGES;"'""
	$(COMPOSE_DEV) exec -T -e APP_ENV=testing -e DB_DATABASE=$(TEST_DATABASE) -u www-data app php artisan test

fresh: ## migrate:fresh --seed (destroys development data)
	$(COMPOSE_DEV) exec -u www-data app php artisan migrate:fresh --seed --force

db-import: ## Import a dump: make db-import FILE=dump.sql [STACK=prod]
	@test -n "$(FILE)" || (echo 'Usage: make db-import FILE=/path/to/dump.sql [STACK=dev|prod]'; exit 1)
ifeq ($(STACK),prod)
	$(COMPOSE_PROD) exec -T db sh -c 'exec mysql -u root -p"$$MYSQL_ROOT_PASSWORD" "$$MYSQL_DATABASE"' < $(FILE)
else
	$(COMPOSE_DEV) exec -T db sh -c 'exec mysql -u root -p"$$MYSQL_ROOT_PASSWORD" "$$MYSQL_DATABASE"' < $(FILE)
endif

prod-build: ## Build both production images locally
	$(COMPOSE_BUILD) build

prod-up: ## Start the production stack from the existing images
	$(COMPOSE_PROD) up -d --no-build

prod-down: ## Stop the production stack (keeps volumes)
	$(COMPOSE_PROD) down

prod-deploy: ## Pull the pinned images and start the production stack
	$(COMPOSE_PROD) pull
	$(COMPOSE_PROD) up -d --no-build

prod-local: ## Open the production stack locally (no Traefik)
	$(COMPOSE_LOCAL) up -d --no-build

prod-migrate: ## Run migrations in production
	$(COMPOSE_PROD) exec -u www-data app php artisan migrate --force

prod-logs: ## Tail the production logs
	$(COMPOSE_PROD) logs -f --tail=100

prod-shell: ## Shell as www-data in the production app container
	$(COMPOSE_PROD) exec -u www-data app sh

prod-artisan: ## Run an artisan command in production: make prod-artisan CMD="about"
	@test -n "$(CMD)" || (echo 'Usage: make prod-artisan CMD="about"'; exit 1)
	$(COMPOSE_PROD) exec -u www-data app php artisan $(CMD)

backup: ## Dump the production database to backup-YYYY-MM-DD.sql
	$(COMPOSE_PROD) exec -T db sh -c 'exec mysqldump -u root -p"$$MYSQL_ROOT_PASSWORD" --single-transaction --routines "$$MYSQL_DATABASE"' > backup-$$(date +%F).sql
	@echo "backup-$$(date +%F).sql"

builder: ## Create the multi-platform buildx builder
	@docker buildx inspect $(BUILDER) >/dev/null 2>&1 || docker buildx create --name $(BUILDER) --driver docker-container --bootstrap
	@docker buildx use $(BUILDER)

release: ## Build and push both images: make release VERSION=2026-11-01.1 [PLATFORMS=linux/amd64]
	@test -n "$(VERSION)" || (echo 'Usage: make release VERSION=2026-11-01.1'; exit 1)
	docker buildx build --platform $(PLATFORMS) --builder $(BUILDER) -f Dockerfile --target prod \
		-t $(REGISTRY)/pkab-app:$(VERSION) -t $(REGISTRY)/pkab-app:latest --push .
	docker buildx build --platform $(PLATFORMS) --builder $(BUILDER) -f Dockerfile --target web_prod \
		-t $(REGISTRY)/pkab-nginx:$(VERSION) -t $(REGISTRY)/pkab-nginx:latest --push .

save-images: ## Build and export both images to pkab-images.tar.gz
	@test -n "$(VERSION)" || (echo 'Usage: make save-images VERSION=2026-11-01.1 [PLATFORM=linux/amd64]'; exit 1)
	docker buildx build --platform $(PLATFORM) -f Dockerfile --target prod \
		-t $(REGISTRY)/pkab-app:$(VERSION) --load .
	docker buildx build --platform $(PLATFORM) -f Dockerfile --target web_prod \
		-t $(REGISTRY)/pkab-nginx:$(VERSION) --load .
	docker save $(REGISTRY)/pkab-app:$(VERSION) $(REGISTRY)/pkab-nginx:$(VERSION) | gzip > pkab-images.tar.gz
	@echo "pkab-images.tar.gz"
