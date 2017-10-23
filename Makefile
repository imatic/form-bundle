SHELL := /bin/bash

.PHONY: test
test: phpunit phpmd phpcs phpda

.PHONY: phpcs
phpcs:
	./vendor/bin/php-cs-fixer fix --dry-run

.PHONY: phpmd
phpmd:
	./vendor/bin/phpmd $$(find * -maxdepth 0 -not -name 'vendor' -not -name 'Tests' -type d | paste --delimiter , --serial) text phpmd.xml

.PHONY: phpunit
phpunit:
	./vendor/bin/phpunit

.PHONY: update-test
update-test: | composer
	git checkout -- ./composer.lock
	./composer install

.PHONY: update-test-min
update-test-min: | composer
	./composer update --prefer-lowest

composer:
	$(if $(shell which composer 2> /dev/null),\
        ln --symbolic $$(which composer) composer,\
		curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=$$(pwd) --filename=composer)

.PHONY: configure-pipelines
configure-pipelines:
	apt-get update
	apt-get install --yes git graphviz

.PHONY: phpda
phpda:
	./vendor/bin/phpda analyze phpda.yml

