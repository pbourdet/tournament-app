sail = ./vendor/bin/sail

.PHONY: tests
tests:
	$(sail) test --parallel

.PHONY: dusk
dusk:
	$(sail) exec laravel supervisorctl stop vite
	$(sail) dusk || true
	$(sail) exec laravel supervisorctl start vite

phpstan:
	$(sail) exec laravel ./vendor/bin/phpstan analyse

pint:
	$(sail) exec laravel ./vendor/bin/pint

rector:
	$(sail) exec laravel ./vendor/bin/rector

dependencies:
	$(sail) composer update
	$(sail) npm update

checks:
	make pint
	make rector
	make phpstan
	make tests
	make dusk

ide-helper:
	$(sail) artisan clear-compiled
	$(sail) artisan ide-helper:generate
	$(sail) artisan ide-helper:models -M
	$(sail) artisan ide-helper:meta
