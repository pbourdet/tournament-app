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
