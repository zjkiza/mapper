build:
	@docker-compose up -d --build > /dev/null

run:
	@docker-compose up -d > /dev/null

attach:
	@docker exec -it php_bundle_2 bash

shutdown:
	@docker-compose down

test:
	@docker-compose run --rm php_bundle_2 composer phpunit