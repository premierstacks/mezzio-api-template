# Default shell
SHELL := /bin/bash

# Default goal
.DEFAULT_GOAL := help

# Variables
MAKE_PHP_8_3_EXE ?= php8.3
MAKE_COMPOSER_EXE ?= /usr/local/bin/composer
MAKE_NPM_EXE ?= npm
MAKE_NODE_EXE ?= node

MAKE_PHP ?= ${MAKE_PHP_8_3_EXE}
MAKE_COMPOSER ?= ${MAKE_PHP} ${MAKE_COMPOSER_EXE}
MAKE_NPM ?= ${MAKE_NPM_EXE}
MAKE_NODE ?= ${MAKE_NODE_EXE}

# Goals
.PHONY: help
help:
	@echo 'Usage:'
	@echo '  make <target>'
	@echo ''
	@echo 'Mission-critical delivery flows:'
	@echo '  local'
	@echo '                    Prepare the local developer environment build.'
	@echo '  ci'
	@echo '                    Prepare the continuous integration environment build.'
	@echo '  development'
	@echo '                    Prepare the shared development-ready environment build.'
	@echo '  qa'
	@echo '                    Prepare the QA validation environment build.'
	@echo '  staging'
	@echo '                    Prepare the staging certification environment build.'
	@echo '  production'
	@echo '                    Prepare the production release environment build.'
	@echo ''
	@echo 'Application runtime interface:'
	@echo '  start | serve | up | server | dev'
	@echo '                    Boot the development HTTP server.'
	@echo ''
	@echo 'Quality gates & assurance:'
	@echo '  check'
	@echo '                    Execute the end-to-end quality gate before promotion.'
	@echo '  test'
	@echo '                    Run the full automated test campaign.'
	@echo '  coverage'
	@echo '                    Host the local web console for the latest coverage run.'
	@echo '  lint'
	@echo '                    Execute all linters across source code and assets.'
	@echo '  fix'
	@echo '                    Autofix style and formatting deviations across stacks.'
	@echo '  stan'
	@echo '                    Run advanced static analysis for the PHP domain.'
	@echo '  audit'
	@echo '                    Assess dependency health and supply-chain posture.'
	@echo ''
	@echo 'Dependencies & environment:'
	@echo '  install'
	@echo '                    Provision all runtime dependencies for the project.'
	@echo '  update'
	@echo '                    Refresh dependencies to the latest approved revisions.'
	@echo ''
	@echo 'Housekeeping & recovery:'
	@echo '  clean'
	@echo '                    Purge build caches and dependency artifacts.'
	@echo '  distclean'
	@echo '                    Reset the project to a pristine state.'
	@echo ''
	@echo 'Meta:'
	@echo '  help'
	@echo '                    Show this operational guide.'

.PHONY: local
local: ./.env.ini install
	${MAKE_COMPOSER} run dump:development
	${MAKE_COMPOSER} run apcu:clear
	${MAKE_COMPOSER} run migrate:up

.PHONY: development
development: ./.env.ini install
	${MAKE_COMPOSER} run dump:development
	${MAKE_COMPOSER} run apcu:clear
	${MAKE_COMPOSER} run migrate:up

.PHONY: testing
testing: ./.env.ini install
	${MAKE_COMPOSER} run dump:production
	${MAKE_COMPOSER} run apcu:clear
	${MAKE_COMPOSER} run migrate:up

.PHONY: staging
staging: ./.env.ini install
	${MAKE_COMPOSER} run dump:production
	${MAKE_COMPOSER} run apcu:clear
	${MAKE_COMPOSER} run migrate:up

.PHONY: production
production: ./.env.ini install
	${MAKE_COMPOSER} run dump:production
	${MAKE_COMPOSER} run apcu:clear
	${MAKE_COMPOSER} run migrate:up

.PHONY: start serve up server dev
start serve up server dev: local
	${MAKE_COMPOSER} run start:local

.PHONY: audit
audit: audit_npm audit_composer

.PHONY: audit_composer
audit_composer: ./vendor ./composer.json ./composer.lock
	${MAKE_COMPOSER} run composer:audit
	${MAKE_COMPOSER} run composer:platform
	${MAKE_COMPOSER} run composer:validate

.PHONY: audit_npm
audit_npm: ./node_modules ./package.json ./package-lock.json
	${MAKE_NPM} run npm:audit

.PHONY: check
check: lint stan test audit

.PHONY: clean
clean:
	rm -rf ./.php-cs-fixer.cache
	rm -rf ./.phpunit.cache
	rm -rf ./.phpunit.coverage
	rm -rf ./.phpunit.result.cache
	rm -rf ./composer.lock
	rm -rf ./node_modules
	rm -rf ./package-lock.json
	rm -rf ./vendor

.PHONY: distclean
distclean: clean

.PHONY: coverage
coverage: ./.phpunit.coverage/html
	${MAKE_COMPOSER} start:coverage

.PHONY: fix
fix: fix_eslint fix_prettier fix_php_cs_fixer

.PHONY: fix_eslint
fix_eslint: ./node_modules ./eslint.config.js
	${MAKE_NPM} run fix:eslint

.PHONY: fix_php_cs_fixer
fix_php_cs_fixer: ./vendor ./.php-cs-fixer.php
	${MAKE_COMPOSER} run fix:php-cs-fixer

.PHONY: fix_prettier
fix_prettier: ./node_modules ./prettier.config.js
	${MAKE_NPM} run fix:prettier

.PHONY: lint
lint: lint_eslint lint_prettier lint_php_cs_fixer

.PHONY: lint_eslint
lint_eslint: ./node_modules ./eslint.config.js
	${MAKE_NPM} run lint:eslint

.PHONY: lint_php_cs_fixer
lint_php_cs_fixer: ./vendor ./.php-cs-fixer.php
	${MAKE_COMPOSER} run lint:php-cs-fixer

.PHONY: lint_prettier
lint_prettier: ./node_modules ./prettier.config.js
	${MAKE_NPM} run lint:prettier

.PHONY: stan
stan: stan_phpstan

.PHONY: stan_phpstan
stan_phpstan: ./vendor ./phpstan.neon
	${MAKE_COMPOSER} run stan:phpstan

.PHONY: test
test: test_phpunit

.PHONY: test_phpunit
test_phpunit: ./vendor ./phpunit.xml
	${MAKE_COMPOSER} run test:phpunit

.PHONY: install
install: install_npm install_composer

.PHONY: install_npm
install_npm: ./package.json
	${MAKE_NPM} run npm:install

.PHONY: install_composer
install_composer: ./composer.json
	${MAKE_COMPOSER} run composer:install

.PHONY: update
update: update_npm update_composer

.PHONY: update_npm
update_npm: ./package.json
	rm -rf ./node_modules
	rm -rf ./package-lock.json
	${MAKE_NPM} run npm:update

.PHONY: update_composer
update_composer: ./composer.json
	rm -rf ./vendor
	rm -rf ./composer.lock
	${MAKE_COMPOSER} run composer:update

# Dependencies
./.phpunit.coverage/html:
	${MAKE} test_phpunit

./package-lock.json ./node_modules:
	${MAKE} install

./composer.lock ./vendor:
	${MAKE} install
