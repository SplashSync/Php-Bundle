
echo -e "\e[104m                                        \e[49m"
echo -e "\e[104m SPLASH BUNDLE --> Install Symfony      \e[49m"
echo -e "\e[104m                                        \e[49m"

echo "Build Dependencies"
composer update  --prefer-dist --no-interaction  

echo "Install Symfony"
php bin/console doctrine:schema:update --force  --no-interaction --no-debug

echo "Link Symfony Test Container Xml"
# Symfony 2 & 3
if [ -f var/cache/dev/appDevDebugProjectContainer.xml ]; then cp var/cache/dev/appDevDebugProjectContainer.xml var/cache/dev/testContainer.xml; fi;
# Symfony 4+
if [ -f var/cache/dev/testsKernelDevDebugContainer.xml ]; then cp var/cache/dev/testsKernelDevDebugContainer.xml var/cache/dev/testContainer.xml; fi;

echo "Start Web Server"
php bin/console server:start