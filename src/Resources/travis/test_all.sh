
echo -e "\e[104m                                        \e[49m"
echo -e "\e[104m SPLASH BUNDLE --> Full Test Suite      \e[49m"
echo -e "\e[104m                                        \e[49m"

# Execute Grump Travis Testsuite 
php vendor/bin/grumphp run --testsuite=travis

# With PHP > 7.0 => Execute CsFixer
if [[ ${TRAVIS_PHP_VERSION:0:3} > "7.0" ]]; then php ./vendor/bin/grumphp run --testsuite=csfixer; fi    

# With PHP > 7.0 => Execute Phpstan 
if [[ ${TRAVIS_PHP_VERSION:0:3} > "7.0" ]]; then php ./vendor/bin/grumphp run --testsuite=phpstan; fi   

# Execute PhpUnit Tests 
vendor/bin/phpunit

# Show Outdated Packages   
composer outdated 
