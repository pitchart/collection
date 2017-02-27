### Variables

# Applications
COMPOSER ?= /usr/bin/env composer

### Helpers
all: clean depend

.PHONY: all

### Dependencies
depend:
	${COMPOSER} install --no-progress --prefer-source

.PHONY: depend

### QA
qa: lint phpmd phpcs phpcpd

lint:
	find ./src -name "*.php" -exec /usr/bin/env php -l {} \; | grep "Parse error" > /dev/null && exit 1 || exit 0

phploc:
	vendor/bin/phploc src

phpmd:
	vendor/bin/phpmd --suffixes php src/ text codesize,design,naming,unusedcode,controversial

phpcs:
	vendor/bin/phpcs --standard=PSR2 --extensions=php src/
	
phpcpd:
	vendor/bin/phpcpd src/

.PHONY: qa lint phploc phpmd phpcs phpcpd

### Testing
test:
	php vendor/bin/phpunit -v --colors --coverage-text

test-report:
	php vendor/bin/phpunit -v --colors --coverage-html ./tests/coverage/

.PHONY: test

### Cleaning
clean:
	rm -rf vendor

.PHONY: clean

phpcbf:
	php vendor/bin/phpcbf --standard=PSR2 --extensions=php src/
	php vendor/bin/phpcbf --standard=PSR2 --extensions=php tests/

.PHONY: phpcbf

