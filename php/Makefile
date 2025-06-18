# Color Config
NOCOLOR=\033[0m
GREEN=\033[0;32m
BGREEN=\033[1;32m
YELLOW=\033[0;33m
CYAN=\033[0;36m
RED=\033[0;31m

# Config
BREAK=\n

# Default action
.DEFAULT_GOAL := help

# Checks if the docker-compose command is available in the system
DOCKER_COMPOSE := $(shell command -v docker-compose 2> /dev/null)

# If Docker-Compose is available, use it, otherwise it uses docker compose
ifeq ($(strip $(DOCKER_COMPOSE)),)
	DOCKER_COMPOSE := docker compose
else
	DOCKER_COMPOSE := docker-compose
endif

## General commands:
.PHONY: help
help: ## Display this message help
	@awk '\
		BEGIN {\
			FS = ":.*##";\
			printf "${BREAK}${YELLOW}Usage:${BREAK}${CYAN}  make [target]${BREAK}${BREAK}${YELLOW}Available targets:${BREAK}${BREAK}" \
		} /^##/ { \
			printf "${YELLOW}%s${NOCOLOR}${BREAK}", substr($$0, 4) \
		} /^[a-zA-Z0-9_-]+:.*?##/ { \
			printf "  ${BGREEN}%-18s${NOCOLOR} %s${BREAK}", $$1, $$2 \
		}' $(MAKEFILE_LIST)
		@printf "${BREAK}${YELLOW}Example:${BREAK}${CYAN}  make up${BREAK}"

.PHONY: build
build: ## Build all container
	@echo ""
	@echo "${YELLOW}Build all container${NOCOLOR}"
	@echo ""
	$(DOCKER_COMPOSE) up --build -d

.PHONY: rebuild
rebuild: destroy build ## Rebuild all container

.PHONY: up
up: ## Start all container in detached mode
	@echo ""
	@echo "${YELLOW}Start all container${NOCOLOR}"
	@echo ""
	$(DOCKER_COMPOSE) up -d;

.PHONY: restart
restart: stop up ## Restart all container

.PHONY: stop
stop: ## Stop all container
	@echo ""
	@echo "${YELLOW}Stop all container${NOCOLOR}"
	@echo ""
	$(DOCKER_COMPOSE) stop

.PHONY: destroy
destroy: ## Destroy all container
	@echo ""
	@echo "${RED}Warning: This will destroy all container and data${NOCOLOR}"
	@echo "${YELLOW}Destroy all container${NOCOLOR}"
	@echo ""
	$(DOCKER_COMPOSE) down --remove-orphans -v

.PHONY: logs
logs: ## See LOG in container php
	@echo ""
	@echo "${YELLOW}Log in php container${NOCOLOR}"
	@echo ""
	$(DOCKER_COMPOSE) logs -f php

.PHONY: bash
bash: ## Open bash in php container
	$(DOCKER_COMPOSE) exec php bash

## PHP commands:
.PHONY: phpcs
phpcs: ## Run PHPCS on staged PHP files
	@FILES=$$(git diff --cached --name-only --diff-filter=AM | grep '\.php$$' | sed 's:^php/::'); \
	if [ -z "$$FILES" ]; then \
		echo "No staged PHP files to check."; \
	else \
		echo "Running PHPCS on staged files:"; \
		echo "$$FILES"; \
		$(DOCKER_COMPOSE) exec php phpcs --standard=phpcs.xml --colors --report=full $$FILES; \
	fi

.PHONY: phpcbf
phpcbf: ## Run PHPCBF on staged PHP files
	@FILES=$$(git diff --cached --name-only --diff-filter=AM | grep '\.php$$' | sed 's:^php/::'); \
	if [ -z "$$FILES" ]; then \
		echo "No staged PHP files to fix."; \
	else \
		echo "Running PHPCBF on staged files:"; \
		echo "$$FILES"; \
		$(DOCKER_COMPOSE) exec php phpcbf --standard=phpcs.xml --colors --report=full $$FILES; \
	fi

.PHONY: phpstan
phpstan: ## Run PHPStan on staged PHP files
	@FILES=$$(git diff --cached --name-only --diff-filter=AM | grep '\.php$$' | sed 's:^php/::'); \
	if [ -z "$$FILES" ]; then \
		echo "No staged PHP files to analyse."; \
	else \
		echo "Running PHPStan on staged files:"; \
		echo "$$FILES"; \
		$(DOCKER_COMPOSE) exec php phpstan analyse --memory-limit=512M $$FILES; \
	fi

# Ignore make target errors for commands like `make symfony console migrate`
.PHONY: %
%:
	@:
