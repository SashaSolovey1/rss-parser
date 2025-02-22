build:
	docker compose build --no-cache --force-rm
stop:
	docker compose stop
down:
	docker compose down
up:
	docker compose up -d --build
	docker exec laravel-docker bash -c "composer install"
	docker exec laravel-docker bash -c "php artisan migrate"
	docker exec laravel-docker bash -c "php artisan schedule:run"



