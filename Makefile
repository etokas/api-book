start: up compose migrate fixture

up:
	docker compose up -d

compose:
	docker compose exec php composer install

migrate:
	docker compose exec php bin/console d:m:m -n

fixture:
	docker compose exec php bin/console doctrine:fixtures:load