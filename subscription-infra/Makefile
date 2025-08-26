SHELL := /bin/sh

up:          ## Lancer les conteneurs
	docker compose up -d --build

down:        ## Arrêter et supprimer les conteneurs + volumes
	docker compose down -v

migrate:     ## Exécuter les migrations Doctrine
	docker compose exec backend php bin/console doctrine:migrations:migrate -n

fixtures:    ## Charger les fixtures
	docker compose exec backend php bin/console doctrine:fixtures:load -n

phpunit:     ## Lancer les tests PHPUnit
	docker compose exec backend ./vendor/bin/phpunit --testdox
