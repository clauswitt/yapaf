test:
	phpunit

.PHONY: webserver
server:
	./start_local.sh

setup:
	composer install

