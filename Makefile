#
# Tests
#

test: lint phpunit phpstan

lint: ; ./vendor/bin/php-cs-fixer fix \
        --verbose \
        --dry-run

fix-lint: ; ./vendor/bin/php-cs-fixer fix \
            --verbose

diff-lint: ; ./vendor/bin/php-cs-fixer fix \
             --verbose \
             --dry-run \
             --diff

phpstan: ; ./vendor/bin/phpstan analyze --level=max src

phpunit: ; ./vendor/bin/phpunit

coverage: ; ./vendor/bin/phpunit \
            --coverage-text \
            --coverage-html build/coverage
#
# Build
#

build-coverage: ; ./vendor/bin/phpunit \
                  --coverage-text \
                  --coverage-clover build/phpunit_clover.xml \
                  --log-junit build/phpunit_junit.xml \
                  --coverage-html build/coverage \
                  --colors=never

install: ; composer install

#
# Phony
#

.PHONY: install test lint fix-lint diff-lint phpunit coverage build-coverage phan

