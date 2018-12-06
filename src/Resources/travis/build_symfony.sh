
echo -e "\e[104m                                        \e[49m"
echo -e "\e[104m SPLASH BUNDLE --> Install Symfony      \e[49m"
echo -e "\e[104m                                        \e[49m"

# Update composer
- composer self-update

# Setup Travis PHP     
if [ "$TRAVIS_PHP_VERSION" != "hhvm" ]; then echo "memory_limit = -1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini; fi

# Setup Composer Stability if Required   
if ! [ -z "$STABILITY" ]; then composer config minimum-stability ${STABILITY}; fi;

# Setup Symfony Version if Required    
if [ "$SF_VERSION" != "" ]; then composer require --no-update symfony/symfony=$SF_VERSION; fi;

# Create Database
mysql -e 'CREATE DATABASE IF NOT EXISTS symfony;'

