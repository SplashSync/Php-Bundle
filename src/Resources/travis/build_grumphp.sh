
echo -e "\e[104m                                        \e[49m"
echo -e "\e[104m SPLASH BUNDLE --> Install Grumphp      \e[49m"
echo -e "\e[104m                                        \e[49m"

# With PHP < 7.1 => Remove Phpstan  
if [[ ${TRAVIS_PHP_VERSION:0:3} < "7.1" ]]; then echo "=> Remove All PhpStan Packages"; fi
if [[ ${TRAVIS_PHP_VERSION:0:3} < "7.1" ]]; then composer remove phpstan/phpstan-shim --no-update --dev; fi
if [[ ${TRAVIS_PHP_VERSION:0:3} < "7.1" ]]; then composer remove phpstan/phpstan-phpunit --no-update --dev; fi
if [[ ${TRAVIS_PHP_VERSION:0:3} < "7.1" ]]; then composer remove phpstan/phpstan-doctrine --no-update --dev; fi
if [[ ${TRAVIS_PHP_VERSION:0:3} < "7.1" ]]; then composer remove phpstan/phpstan-symfony --no-update --dev; fi


