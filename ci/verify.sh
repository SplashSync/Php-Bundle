
################################################################################
#
#  This file is part of SplashSync Project.
#
#  Copyright (C) Splash Sync <www.splashsync.com>
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
#
#  For the full copyright and license information, please view the LICENSE
#  file that was distributed with this source code.
#
#  @author Bernard Paquier <contact@splashsync.com>
#
################################################################################

################################################################################
# Docker Compose Container you want to check
CONTAINERS="php-7.4,php-8.0,php-8.1"
WORKDIR="/var/www/html"

################################################################################
# Start Docker Compose Stack
echo '===> Start Docker Stack'
docker-compose up -d

######################################
# Walk on Docker Compose Container
for ID in $(echo $CONTAINERS | tr "," "\n")
do
    echo "===> Checks Php $ID"

    # Ensure Git is Installed
    docker-compose exec $ID apt install git -y
    # Run Composer Update
    docker-compose exec -w $WORKDIR $ID composer update -q
    # Run Grumphp Test Suites
    docker-compose exec -w $WORKDIR $ID php vendor/bin/grumphp run --testsuite=travis
    docker-compose exec -w $WORKDIR $ID php vendor/bin/grumphp run --testsuite=csfixer
    docker-compose exec -w $WORKDIR $ID php vendor/bin/grumphp run --testsuite=phpstan
done