sail = ./vendor/bin/sail

.PHONY: tests
tests:
	$(sail) test --parallel

phpstan:
	$(sail) exec laravel ./vendor/bin/phpstan analyse

pint:
	$(sail) exec laravel ./vendor/bin/pint

rector:
	$(sail) exec laravel ./vendor/bin/rector

checks:
	make pint
	make rector
	make phpstan
	make tests

ide-helper:
	$(sail) artisan clear-compiled
	$(sail) artisan ide-helper:generate
	$(sail) artisan ide-helper:models -M
	$(sail) artisan ide-helper:meta
