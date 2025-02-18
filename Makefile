### ——————————————————————————————————————————————————————————————————
### —— Local Makefile
### ——————————————————————————————————————————————————————————————————

include vendor/badpixxel/php-sdk/make/sdk.mk

up: 	## Execute Functional Test
	symfony serve --no-tls

test: 	## Execute Functional Test
	php vendor/bin/phpunit --testdox

