.PHONY: up down restart shell test cs-fix stan init mig-apply mig-diff test-db-init test-db-drop test-db-reset lint

DOCKER_AVAILABLE := $(shell command -v docker 2> /dev/null)

ifdef DOCKER_AVAILABLE
	EXEC_PHP = docker compose exec php
	EXEC_PHP_T = docker compose exec -T php
else
	EXEC_PHP = 
	EXEC_PHP_T = 
endif

up:
	docker compose up -d

down:
	docker compose down

restart: down up

cli:
	docker compose exec php bash

init:
	docker compose up -d
	sleep 3
	$(MAKE) db-reset
	$(MAKE) mig-apply
	$(MAKE) test-db-reset

lint:
	$(MAKE) csfix
	$(MAKE) stan

mig-apply:
	$(EXEC_PHP_T) php bin/console doctrine:migrations:migrate --no-interaction

mig-diff:
	$(EXEC_PHP_T) php bin/console doctrine:migrations:diff --formatted

db-reset:
	$(EXEC_PHP_T) php bin/console doctrine:database:drop --force --if-exists
	$(EXEC_PHP_T) php bin/console doctrine:database:create --if-not-exists
	$(EXEC_PHP_T) php bin/console doctrine:migrations:migrate --no-interaction

test:
	$(EXEC_PHP) bin/phpunit $(if $(test),$(test),tests/)

test-db-init:
	$(EXEC_PHP_T) php bin/console doctrine:database:create --if-not-exists --env=test

test-db-drop:
	$(EXEC_PHP_T) php bin/console doctrine:database:drop --force --if-exists --env=test

test-db-reset:
	$(EXEC_PHP_T) php bin/console doctrine:database:drop --force --if-exists
	$(EXEC_PHP_T) php bin/console doctrine:database:create --if-not-exists
	$(EXEC_PHP_T) php bin/console doctrine:migrations:migrate --no-interaction --env=test

csfix:
	$(EXEC_PHP) vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php $(if $(path),$(path),src/)

stan:
	$(EXEC_PHP) vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=256M
