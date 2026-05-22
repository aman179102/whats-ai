.PHONY: help install test lint fix serve clean

help:
	@echo "WhatsAI Development Commands"
	@echo "---------------------------"
	@echo "make install   - Install dependencies"
	@echo "make test      - Run PHPUnit tests"
	@echo "make lint      - Check code style"
	@echo "make fix       - Fix code style"
	@echo "make serve     - Start development server"
	@echo "make clean     - Clean temporary files"

install:
	composer install

test:
	vendor/bin/phpunit

lint:
	vendor/bin/php-cs-fixer fix --dry-run --diff

fix:
	vendor/bin/php-cs-fixer fix

serve:
	php -S localhost:8080 -t public

migrate:
	php cron/migrate.php

schedule-run:
	php cron/schedule-runner.php

bridge-install:
	cd bridge && npm install

bridge-start:
	cd bridge && npm start

bridge-stop:
	-kill `lsof -ti :3001` 2>/dev/null

clean:
	rm -rf storage/database.sqlite storage/logs/*.log
