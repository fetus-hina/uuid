.PHONY: all
all: setup

.PHONY: setup
setup: composer.phar vendor

.PHONY: test
test: setup
	vendor/bin/phpunit

.PHONY: check-style
check-style: setup
	vendor/bin/phpcs --encoding=UTF-8 --runtime-set ignore_warnings_on_exit 1 src test
	vendor/bin/phpstan

.PHONY: fix-style
fix-style: setup
	vendor/bin/phpcbf --encoding=UTF-8 src test

vendor: composer.phar composer.json
	./composer.phar install --prefer-dist
	@touch vendor

.PHONY: clean
clean:
	rm -rf \
		composer.lock \
		composer.phar \
		coverage \
		vendor

composer.phar:
	curl -fsS https://getcomposer.org/installer | php -- --stable
