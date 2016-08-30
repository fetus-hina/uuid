all: setup test

setup: composer.phar composer-update composer-plugin vendor

test: setup
	vendor/bin/phpunit

check-style: setup
	vendor/bin/phpcs --standard=PSR2 --encoding=UTF-8 --runtime-set ignore_warnings_on_exit 1 src

fix-style: setup
	vendor/bin/phpcbf --standard=PSR2 --encoding=UTF-8 src

composer-update: composer.phar
	./composer.phar --no-plugins self-update

composer-plugin: composer.phar
	grep '"fxp/composer-asset-plugin"' ~/.composer/composer.json >/dev/null || ./composer.phar --no-plugins global require 'fxp/composer-asset-plugin:^1.1'
	./composer.phar --no-plugins global update -vvv

vendor: composer.phar composer.lock
	./composer.phar install --prefer-dist --profile
	touch -r composer.lock vendor

clean:
	rm -rf composer.phar vendor

composer.phar:
	curl -sS https://getcomposer.org/installer | php -- --stable

composer.lock: composer.json composer.phar
	./composer.phar update -vvv

.PHONY: all setup composer-update composer-plugin
