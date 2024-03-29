SHELL := /bin/bash

include .env
export

ALL: up

install:
	@docker network ls|grep ct_project_networks > /dev/null || docker network create dev_ocean_networks
	@docker compose build
	@docker compose up -d

build:
	@docker network ls|grep ct_project_networks > /dev/null || docker network create dev_ocean_networks
	@docker compose build

rebuild:
	@docker network ls|grep ct_project_networks > /dev/null || docker network create dev_ocean_networks
	@docker compose build --no-cache

ps:
	@docker compose ps -a

up:
	@docker compose up -d

down:
	@docker compose down

stop:
	@docker compose stop

restart:
	@docker compose down
	@docker compose up -d

reload:
	@docker compose stop
	@docker compose build
	@docker compose up -d

sh:
	@docker compose exec dev_ocean_api /bin/bash

inspect:
	@docker inspect dev_ocean_db | grep "IPAddress"
