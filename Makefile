setup:
	@git clone git@github.com:Apropriare/gepg-integration-middleware.git code
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
	docker exec gepg-payment-middleware -c "php artisan config:clear"
	docker exec gepg-payment-middleware bash -c "php artisan config:cache"

stop:
	docker compose stop

migrate:
	docker exec gepg-payment-middleware bash -c "php artisan migrate"

start:
	docker compose restart

logs:
	docker logs -f gepg-payment-middleware

bash:
	docker exec -it gepg-payment-middleware bash

filamentuser:
	docker exec -it gepg-payment-middleware php artisan make:filament-user

superadmin:
	docker exec -it gepg-payment-middleware php artisan shield:super-admin
