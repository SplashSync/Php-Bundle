
echo -e "\e[104m                                        \e[49m"
echo -e "\e[104m SPLASH BUNDLE --> Build Symfony        \e[49m"
echo -e "\e[104m                                        \e[49m"

# Setup Composer Stability if Required   
if ! [ -z "$STABILITY" ]; then echo "=> minimum-stability ${STABILITY}"; fi;
if ! [ -z "$STABILITY" ]; then composer config minimum-stability ${STABILITY}; fi;

# Setup Symfony Version if Required  
if [ "$SF_VERSION" != "" ]; then echo "=> update to Symfony $SF_VERSION"; fi;
if [ "$SF_VERSION" != "" ]; then composer require --no-update symfony/symfony=$SF_VERSION; fi;

# Create Database
echo "=> Create Database (symfony)"
mysql -e 'CREATE DATABASE IF NOT EXISTS symfony;'

