
echo -e "\e[104m                                        \e[49m"
echo -e "\e[104m SPLASH BUNDLE --> Install Symfony      \e[49m"
echo -e "\e[104m                                        \e[49m"

echo "Build Dependencies"
composer update  --prefer-dist --no-interaction  

echo "Install Symfony"
php bin/console doctrine:schema:update --force  --no-interaction --no-debug

echo "Start Web Server"
php bin/console server:start