COMPOSER_VERSION=1.0.0-beta1

all: setup test

setup: composer.phar composer-update composer-plugin vendor

test: setup
	vendor/bin/phpunit

check-style: setup
	vendor/bin/phpcs --standard=PSR2 --encoding=UTF-8 --runtime-set ignore_warnings_on_exit 1 src

fix-style: setup
	vendor/bin/phpcbf --standard=PSR2 --encoding=UTF-8 src

composer-update: composer.phar
	(./composer.phar --version | grep "Composer version $(COMPOSER_VERSION) " >/dev/null) || ./composer.phar self-update -- $(COMPOSER_VERSION)

composer-plugin: composer.phar
	grep '"fxp/composer-asset-plugin"' ~/.composer/composer.json >/dev/null || ./composer.phar global require 'fxp/composer-asset-plugin:^1.1'
	grep '"hirak/prestissimo"' ~/.composer/composer.json >/dev/null || ./composer.phar global require 'hirak/prestissimo:^0.1'

vendor: composer.phar composer.lock
	./composer.phar install --prefer-dist --profile
	touch -r composer.lock vendor

clean:
	rm -rf composer.phar vendor

composer.phar:
	curl -sS https://getcomposer.org/installer | php -- --version=$(COMPOSER_VERSION)

composer.lock: composer.json composer.phar
	./composer.phar update -vvv

.PHONY: all setup composer-update composer-plugin
