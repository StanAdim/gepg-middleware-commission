setup:
	@git clone git@github.com-gepg-integration-middleware:Apropriare/gepg-integration-middleware.git code
	@make update
	@make migrate
	@make filamentuser
	@make superadmin

update:
	cd code && git pull origin -ff
	@make up
	@make boost

up:
	# CURRENT_UID=$(id -u):$(id -g)
	docker compose up --force-recreate --build -d

boost:
	docker-compose exec app bash -c "php artisan config:clear"
	docker-compose exec app bash -c "php artisan config:cache"

stop:
	docker compose stop

migrate:
	docker-compose exec app bash -c "php artisan migrate"

start:
	docker compose restart

logs:
	docker-compose logs -f app

bash:
	docker-compose exec app bash

filamentuser:
	docker-compose exec app php artisan make:filament-user

superadmin:
	docker-compose exec app php artisan shield:super-admin
