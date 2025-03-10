sail = ./vendor/bin/sail

up:
	$(sail) up -d

seed:
	$(sail) artisan migrate:fresh --seed

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

commit-dependencies:
	make dependencies
	git add composer.lock package-lock.json
	@if git diff --name-only --cached | grep -qE '^(composer\.lock|package-lock\.json)$$'; then \
		git commit composer.lock package-lock.json -m "Updated dependencies"; \
	else \
		echo "No changes in composer.lock or package-lock.json. Skipping commit."; \
	fi

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

lang-update:
	$(sail) artisan lang:update
	make pint
