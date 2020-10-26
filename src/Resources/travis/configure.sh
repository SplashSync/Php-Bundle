echo -e "\e[104m                                        \e[49m"
echo -e "\e[104m SPLASH BUNDLE --> Config Travis        \e[49m"
echo -e "\e[104m                                        \e[49m"

# With PHP < 7.3 => Rollback to Composer 1
if [[ ${TRAVIS_PHP_VERSION:0:3} < "7.3" ]]; then
    composer self-update --rollback;
else
    composer self-update
fi

# Setup Travis PHP     
if [ "$TRAVIS_PHP_VERSION" != "hhvm" ]; then echo "memory_limit = -1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini; fi
