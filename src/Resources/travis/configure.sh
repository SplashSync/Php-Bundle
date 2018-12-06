echo -e "\e[104m                                        \e[49m"
echo -e "\e[104m SPLASH BUNDLE --> Config Travis        \e[49m"
echo -e "\e[104m                                        \e[49m"

# Update composer
- composer self-update

# Setup Travis PHP     
if [ "$TRAVIS_PHP_VERSION" != "hhvm" ]; then echo "memory_limit = -1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini; fi
