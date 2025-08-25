SHELL := /bin/sh
up:        docker compose up -d --build
down:      docker compose down -v
migrate:   docker compose exec php php bin/console doctrine:migrations:migrate -n
fixtures:  docker compose exec php php bin/console doctrine:fixtures:load -n
phpunit:   docker compose exec php ./vendor/bin/phpunit --testdox
